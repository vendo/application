<?php
/**
 * Controller for managing a user's checkout process
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Controller_Checkout extends Controller
{
	/**
	 * Displays the user's cart, along with an order form with items needed
	 * to process the order.
	 *
	 * @return null
	 */
	public function action_index()
	{
		$this->view = new View_Checkout_Index;
		$this->view->set(
			array(
				'user' => Auth::instance()->get_user()->as_array(),
				'address' => Auth::instance()->get_user()->address->as_array(),
				'cart' => Auth::instance()->get_user()->cart(),
			)
		);
	}

	/**
	 * Processes the shopping cart with the payment processor, saves the user's
	 * cart, clears it and redirects to a success method.
	 *
	 * @return null
	 */
	public function action_process()
	{
		$order = Auth::instance()->get_user()->cart();
		$user_post = arr::get($_POST, 'user', array());
		$user = arr::get($_POST, 'user', Auth::instance()->get_user());
		if ( ! is_object($user))
		{
			$temp = $user;
			$user = Model::factory('vendo_user');
			$user->set_fields($temp);
		}

		$address = Model::factory('vendo_address');
		$address->set_fields(arr::get($_POST, 'address', array()));

		// Build the contact model
		$contact = new Model_Contact;
		$contact->set_fields(
			array(
				'email'      => $user->email,
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
			)
		);

		// Build the credit card model
		$credit_card_post = arr::get($_POST, 'payment');
		$credit_card = new Model_Credit_Card(
			arr::get($credit_card_post, 'card_number'),
			arr::get($credit_card_post, 'months').
				arr::get($credit_card_post, 'years'),
			arr::get($credit_card_post, 'card_code'),
			$contact,
			$address
		);

		$errors = array();

		// Check for a new user registration, and make a user if so
		if ($this->should_create_account($user))
		{
			$status = $this->process_new_account(
				$user, $user_post, $address
			);

			if ( ! $status)
			{
				return;
			}

			$contact->save();
			$order->user_id = $user->id;
		}
		// See if the address entered matches the existing user address, and
		// make a new one if it doesn't.
		else
		{
			$user_address = $user->address->as_array();
			unset($user_address['id']);
			if ($user_address != $address AND TRUE === $address->is_valid())
			{
				$address->save();
				$user->address_id = $address->id;
				$contact->address_id = $address->id;
			}
			elseif (TRUE !== $address->is_valid())
			{
				$errors+=$address->errors('form_errors');
			}
			else
			{
				$address = $user->address;
			}

			$contact->address_id = $address->id;

			try
			{
				$contact->save();
			}
			catch (AutoModeler_Exception $e)
			{
				$errors+=$e->errors;
			}

			$order->contact_id = $contact->id;

			if (TRUE !== $contact->is_valid())
			{
				$errors+=$contact->errors('form_errors');
			}

			if (Auth::instance()->logged_in())
			{
				$order->user_id = $user->id;
			}
		}

		// Verify the credit card is valid
		if (TRUE !== ($cc_errors = $credit_card->validate()))
		{
			$errors+=$cc_errors;
		}

		if ($errors)
		{
			// If we've failed, and we aren't registering a new user, delete
			// the address
			if ( ! $user->id)
			{
				$address->delete();
			}

			$this->view = new View_Checkout_Index;
			$this->view->set(
				array(
					'user' => $user->as_array(),
					'address' => $address->as_array(),
					'cart' => Auth::instance()->get_user()->cart(),
					'credit_card' => $credit_card,
				)
			);
			$errors = (string) View::factory('form_errors')->set(
				array('errors' => $errors)
			);
			$this->view->errors = $errors;
			return;
		}

		$order->credit_card = $credit_card;

		// Process the credit card
		try
		{
			// Save the unpaid order
			$order->save();

			$status = Payment::process($order);

			if (1 != $status->response_code)
			{
				throw new Payment_Exception(
					'Problem processing your payment.'
				);
			}

			// Persist the order
			$contact->save();
			$order->contact_id = $contact->id;
			$order->address_id = $address->id;
			$order->paid = TRUE;
			$order->order_type_id = Model_Order::TYPE_CREDIT_CARD;
			$order->save();

			Auth::instance()->get_user()->cart(new Model_Order);

			// Show success message!
			$this->view = new View_Checkout_Process;
		}
		catch (Payment_Exception $e)
		{
			// If we've failed, and we aren't registering a new user, delete
			// the address
			if ( ! $user->id)
			{
				$address->delete();
			}

			$this->view = new View_Checkout_Index;
			$this->view->set(
				array(
					'user' => $user->as_array(),
					'address' => $address->as_array(),
					'cart' => Auth::instance()->get_user()->cart(),
					'credit_card' => $credit_card,
				)
			);
			$errors = (string) View::factory('form_errors')->set(
				array('errors' => array(
					'general' => $e->getMessage(),
				))
			);
			$this->view->errors = $errors;
			return;
		}
	}

	/**
	 * Helper method to check if the user has filled out form fields to create
	 * a new account
	 *
	 * @return bool
	 */
	protected function should_create_account(Model_Vendo_User $user)
	{
		$status = FALSE;

		if ( ! $user->id AND $user->email AND $user->password)
		{
			$status = TRUE;
		}

		return $status;
	}

	/**
	 * Performs functionality to process a new user account during checkout
	 *
	 * @return bool
	 */
	protected function process_new_account(
		Model_Vendo_User & $user,
		$user_post = array(),
		Model_Vendo_Address $address
	)
	{
		$validate = Model_Vendo_User::get_password_validation();

		$valid_user = $user->is_valid($validate);
		$valid_address = $address->is_valid();

		if (TRUE === $valid_user AND TRUE === $valid_address)
		{
			$address->save();
			$user->address_id = $address->id;
			$user->save();

			$user->roles = Model_Vendo_Role::LOGIN;
		}
		else
		{
			$errors = $user->errors('form_errors');
			$errors+=$address->errors('form_errors');

			$this->view = new View_Checkout_Index;
			$this->view->set(
				array(
					'user' => $user->as_array(),
					'address' => $address->as_array(),
					'cart' => Auth::instance()->get_user()->cart(),
				)
			);

			$errors = (string) View::factory('form_errors')->set(
				array('errors' => $errors)
			);
			$this->view->errors = $errors;

			return FALSE;
		}

		return TRUE;
	}
}
