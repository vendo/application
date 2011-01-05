<?php
/**
 * View class to let the user checkout
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_Checkout_Index extends View_Cart_Index
{
	public $user;
	public $address;
	public $cart;
	public $credit_card;

	/**
	 * Var method to let the user know they can login to save the order
	 *
	 * @return bool
	 */
	public function can_login()
	{
		return Auth::instance()->get_user()->can('login');
	}

	/**
	 * Returns HTML to render for the payment information.
	 * 
	 * To add HTML for new payment methods (paypal), modify this method.
	 *
	 * @return string
	 */
	public function payment_form()
	{
		return Kostache::factory('authorize')->set(
			'credit_card', $this->credit_card
		)->render();
	}
}