<?php
/**
 * Product Controller
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
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
		$this->request->response = new View_Product_Index;
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

		$this->request->response = new View_Product_Category;
		$this->request->response->title = $category->name;
		$this->request->response->category = $category;
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

		$this->request->response = new View_Product_View;
		$this->request->response->title = $product->name;
		$this->request->response->product = $product;
	}
}