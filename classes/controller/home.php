<?php

/**
 * Homepage controller
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Controller_Home extends Controller
{
	public function action_index()
	{
		$this->request->response = new View_Home;
	}
}