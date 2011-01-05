<?php
/**
 * Generic error view class that all other error views should exptend
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
abstract class View_Error extends View_Layout
{
	public $message;
}