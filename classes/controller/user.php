<?php

/**
 * User controller
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Controller_User extends Controller
{
	public function action_register()
	{
		$this->request->response = new View_User_Register;
		$user = new Model_Vendo_User;
		$address = new Model_Vendo_Address;

		if ($_POST)
		{
			$user_post = arr::get($_POST, 'user', array());
			$address_post = arr::get($_POST, 'address', array());
			$validate = Validate::factory(
				array(
					'password' => arr::get($user_post, 'password'),
					'repeat_password' => arr::get($user_post, 'repeat_password'),
				)
			)->rule('repeat_password', 'not_empty')
			->rule('password', 'matches', array('repeat_password'));

			$roles = arr::get($user_post, 'role_id', array());
			unset($_POST['user']['role_id']);
			unset($_POST['user']['repeat_password']);

			$user->set_fields($user_post);
			$address->set_fields($address_post);

			try
			{
				// See if the user entered any address information
				$entered_address = FALSE;
				foreach ($address_post as $address_info)
				{
					if ($address_info)
					{
						$entered_address = TRUE;
						break;
					}
				}

				if ($entered_address)
				{
					$address->save();
					$user->address_id = $address->id;
				}
				else
				{
					$user->address_id = NULL;
				}

				$user->save($validate);

				foreach ($roles as $role)
				{
					$user->vendo_roles = $role;
				}

				Request::instance()->redirect('home');
			}
			catch (AutoModeler_Exception $e)
			{
				$this->request->response->errors = (string) $e;

				// If we've saved an address, get rid of it because it's junk
				// This can happen if the address is valid on the page, but the
				// user is not
				if ($address->id)
				{
					$address->delete();
				}
			}
		}

		$this->request->response->user = $user;
		$this->request->response->address = $address;
	}

	public function action_login()
	{
		$this->request->response = new View_User_Login;

		if ($_POST)
		{
			$cart = Auth::instance()->get_user()->cart();

			// Try to login
			if (
				Auth::instance()->login(
					new Model_Vendo_User(arr::get($_POST, 'email')),
					arr::get($_POST, 'password')
				)
			)
			{
				Auth::instance()->get_user()->cart($cart);

				Request::instance()->redirect('home');
			}

			$this->request->response->errors = 'Invalid email or password';
		}
	}

	public function action_logout()
	{
		$cart = Auth::instance()->get_user()->cart();

		Auth::instance()->logout();

		Auth::instance()->get_user()->cart($cart);

		Request::instance()->redirect('home');
	}

	/**
	 * Action to manage a logged in user's account
	 *
	 * @return null
	 */
	public function action_manage()
	{
		if ( ! Auth::instance()->logged_in())
			throw new Vendo_404('Account Not Found');

		$this->request->response = new View_User_Manage;
		$this->request->response->bind('user', $user);
		$this->request->response->bind('address', $address);

		$user = Auth::instance()->get_user();
		$address = $user->address ? $user->address : new Model_Vendo_Address;

		if ($_POST)
		{
			$user_post = arr::get($_POST, 'user', array());
			$address_post = arr::get($_POST, 'address', array());

			$validate = NULL;
			if (arr::get($_POST, 'password'))
			{
				$validate = Validate::factory(
					array(
						'password' => arr::get($user_post, 'password'),
						'repeat_password' => arr::get(
							$user_post, 'repeat_password'
						),
					)
				)->rule('repeat_password', 'not_empty')
				->rule('password', 'matches', array('repeat_password'));
			}
			else
			{
				unset($user_post['password'], $user_post['repeat_password']);
			}

			$user->set_fields($user_post);
			$address->set_fields($address_post);
			$address->id = '';

			try
			{
				// See if the user entered any address information
				$entered_address = FALSE;
				foreach ($address_post as $address_info)
				{
					if ($address_info)
					{
						$entered_address = TRUE;
						break;
					}
				}

				// Only save the address if they've entered data and it's
				// different than their old one
				$user_address = $user->address ? $user->address : new Model_Vendo_Address;
				if (
					$entered_address
					AND $address->as_array() != $user_address
				)
				{
					$address->save();
					$user->address_id = $address->id;
				}

				$user->save($validate);

				$this->request->response->success =
					'You have saved your settings';
			}
			catch (AutoModeler_Exception $e)
			{
				$this->request->response->errors = (string) $e;
			}
		}
	}
}