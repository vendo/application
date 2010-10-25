<?php
/**
 * Product index view class
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
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
		foreach (AutoModeler_ORM::factory('product')->fetch_all() as $product)
		{
			$products[] = $product->as_array();
		}
		return $products;
	}
}