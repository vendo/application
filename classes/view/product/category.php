<?php
/**
 * Product index view class
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_Product_Category extends View_Layout
{
	public $title = 'All Products';
	public $category;

	/**
	 * Var method to get an array version of the category
	 *
	 * @return array
	 */
	public function category()
	{
		return $this->category->as_array();
	}

	/**
	 * Returns all the products for this category
	 *
	 * @return array
	 */
	public function products()
	{
		$products = array();
		foreach ($this->category->find_related('vendo_products') as $product)
		{
			$products[] = $product->as_array()+array(
				'photo' => $product->primary_photo()->uri(),
			);
		}
		return $products;
	}
}