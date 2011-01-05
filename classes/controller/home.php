<?php

/**
 * Homepage controller
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Controller_Home extends Controller
{
	public function action_index()
	{
		$this->request->response = new View_Home;
	}
}