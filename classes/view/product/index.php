<?php
/**
 * Product index view class
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_Product_Index extends View_Layout
{
	public $title = 'All Products';

	/**
	 * Returns all the products for demo purposes right now
	 *
	 * @return array
	 */
	public function products()
	{
		$products = array();
		foreach (Model::factory('vendo_product')->load(NULL, NULL) as $product)
		{
			$products[] = $product->as_array();
		}
		return $products;
	}
}