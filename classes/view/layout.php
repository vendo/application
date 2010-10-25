<?php
/**
 * Layout view class
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_Layout extends View_Kohana_Layout
{
	public $errors;

	protected $_partials = array(
		'address'          => 'partials/address',
		'cart'             => 'partials/cart',
		'product_category' => 'partials/product_category'
	);

	/**
	 * Var method to get a generic list of years. This should generally be
	 * overloaded to specify a pre-selected year
	 * 
	 * @param int $year a pre-selected year
	 *
	 * @return array
	 */
	public function years($pre_selected_year = NULL)
	{
		$years = array();
		foreach (
			range(date('Y'), date('Y', strtotime('+10 years')))
		as $year)
		{
			$years[] = array(
				'value' => $year,
				'year' => $year,
				'selected' => $year == $pre_selected_year,
			);
		}

		return $years;
	}

	/**
	 * Var method to get a generic list of months. This should generally be
	 * overloaded to specify a pre-selected month
	 * 
	 * @param int $year a pre-selected year
	 *
	 * @return array
	 */
	public function months($pre_selected_month = NULL)
	{
		$months = array();
		foreach (range(1, 12) as $month)
		{
			if ($month < 10)
			{
				$month = '0'.$month;
			}

			$months[] = array(
				'value' => $month,
				'month' => $month,
				'selected' => $month == $pre_selected_month,
			);
		}

		return $months;
	}

	/**
	 * Var method to get the base url for the application
	 * 
	 * @return string
	 */
	public function base()
	{
		return url::base(FALSE, TRUE);
	}

	/**
	 * Var method to get the logged in status for this user. Returns a string
	 * to insert into the template
	 * 
	 * @return string
	 */
	public function logged_in_status()
	{
		return Auth::instance()->logged_in() ? 'Logged In' : 'Logged Out';
	}

	/**
	 * Var method to get the list of product categories
	 *
	 * @return array
	 */
	public function product_categories()
	{
		return AutoModeler_ORM::factory('product_category')->full_tree();
	}

	/**
	 * Var method to build the links sidebar that this user can see
	 * 
	 * @return string
	 */
	public function account_links()
	{
		$links = array();

		if (Auth::instance()->get_user()->can('manage_preferences'))
		{
			$links[] = array(
				'location' => 'user/manage',
				'text'     => 'Manage My Account',
			);
		}
		if (Auth::instance()->get_user()->can('use_admin'))
		{
			$links[] = array(
				'location' => 'admin/category',
				'text'     => 'Manage Product Categories',
			);
			$links[] = array(
				'location' => 'admin/product',
				'text'     => 'Manage Products',
			);
		}
		if (Auth::instance()->get_user()->can('logout'))
		{
			$links[] = array(
				'location' => 'user/logout',
				'text'     => 'Logout',
			);
		}
		elseif (Auth::instance()->get_user()->can('login'))
		{
			$links[] = array(
				'location' => 'user/register',
				'text'     => 'Register',
			);
			$links[] = array(
				'location' => 'user/login',
				'text'     => 'Login',
			);
		}

		return $links;
	}
} // End View_Layout