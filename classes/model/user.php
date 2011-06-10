<?php
/**
 * Transparant extension of Model_Vendo_User
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_User extends AutoModeler_ORM
{
	protected $_table_name = 'users';

	protected $_data = array(
		'id' => NULL,
		'email' => '',
		'first_name' => '',
		'last_name' => '',
		'password' => '',
		'address_id' => NULL,
	);

	protected $_rules = array(
		'email' => array(
			array('not_empty'),
			array('email'),
		),
		'first_name' => array(
			array('not_empty'),
		),
		'last_name' => array(
			array('not_empty'),
		),
		'password' => array(
			array('not_empty'),
		),
		'address_id' => array(
			array('numeric'),
		),	
	);

	protected $_has_many = array(
		'roles',
	);

	/**
	 * Sets a value to this object. Used for hashing passwords for the user
	 * 
	 * @param string $key   the key to set
	 * @param mixed  $value the value to set
	 * 
	 * @return null
	 */
	public function __set($key, $value)
	{
		if ('password' == $key AND $value)
		{
			$value = Auth::instance()->hash($value, Auth::$salt);
		}

		parent::__set($key, $value);
	}

	/**
	 * Constructor to load the object by an email address
	 * 
	 * @param mixed $id the id to load by. A numerical ID or an email address
	 * 
	 * @return null
	 */
	public function __construct($id = NULL)
	{
		if ( ! is_numeric($id) AND NULL != $id)
		{
			// try and get a row with this ID
			$data = db::select_array(array_keys($this->_data))
				->from($this->_table_name)
				->where('email', '=', $id)
				->execute($this->_db);

			// try and assign the data
			if (count($data) == 1 AND $data = $data->current())
			{
				foreach ($data as $key => $value)
					$this->_data[$key] = $value;
			}
		}
		else
		{
			parent::__construct($id);
		}
	}

	/**
	 * Empty complete_login() method for Auth. May contain behavior later.
	 * 
	 * @return null
	 */
	public function complete_login()
	{

	}
}