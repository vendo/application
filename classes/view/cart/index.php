<?php

class View_Cart_Index extends View_Layout
{
	public $title = 'Your Shopping Cart';

	/**
	 * Var method to get the user's cart items
	 *
	 * @return array
	 */
	public function cart()
	{
		$cart_items = array();

		foreach ($this->cart->get_products() as $product_id => $product)
		{
			$cart_items[] = array(
				'product'  => array(
					'total_price' => ($product['product']->price*$product['quantity'])
				)+$product['product']->as_array(),
				'quantity' => $product['quantity'],
			);
		}

		return $cart_items;
	}

	/**
	 * Var method to return the total number of items
	 *
	 * @return int
	 */
	public function total_items()
	{
		return count($this->cart);
	}

	/**
	 * Var method to return the total cost of items
	 *
	 * @return int
	 */
	public function total_price()
	{
		return $this->cart->amount();
	}
}