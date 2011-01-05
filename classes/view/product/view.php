<?php
/**
 * View class for viewing a product
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_Product_View extends View_Layout
{
	public $title;
	public $product;

	/**
	 * Returns the title for this page
	 *
	 * @return string
	 */
	public function title()
	{
		return $this->title;
	}

	/**
	 * Returns data for this product
	 *
	 * @return array
	 */
	public function product()
	{
		return $this->product->as_array()+array(
			'photo' => $this->product->primary_photo()->uri(),
		);
	}
}