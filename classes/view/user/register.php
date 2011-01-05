<?php

/**
 * User register view class
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class View_User_Register extends View_Layout
{
	public $title = 'Register a user';

	public $user;
	public $address;
	public $errors;

	/**
	 * Gets a list of roles for the user to select. This should go away once the
	 * application is in a usable state.
	 * 
	 * @return array
	 */
	public function roles()
	{
		$roles = array();
		foreach (Model::factory('role')->load(NULL, NULL) as $role)
		{
			$roles[] = array(
				'id' => $role->id,
				'name' => $role->name,
			);
		}
		return $roles;
	}
}