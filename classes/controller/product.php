<?php
/**
 * Product Controller
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Controller_Product extends Controller
{
	/**
	 * Shows an index of products
	 *
	 * @return null
	 */
	public function action_index()
	{
		$this->view = new View_Product_Index;
	}

	/**
	 * Shows a list of products for a category
	 *
	 * @return null
	 */
	public function action_category($id = NULL)
	{
		$category = new Model_Vendo_Product_Category($id);

		if ( ! $category->id)
		{
			throw new Vendo_404('Category Not Found');
		}

		$this->view = new View_Product_Category;
		$this->view->title = $category->name;
		$this->view->category = $category;
	}

	/**
	 * Shows a specific product page
	 *
	 * @return null
	 */
	public function action_view($product_id = NULL)
	{
		$product = new Model_Vendo_Product($product_id);

		if ( ! $product->id)
		{
			throw new Vendo_404('Product not found!');
		}

		$this->view = new View_Product_View;
		$this->view->title = $product->name;
		$this->view->product = $product;
	}
}