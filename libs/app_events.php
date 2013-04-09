<?php
/**
 * Eventful CakePHP
 * 
 * @author Kjell Bublitz <m3nt0r.de@gmail.com>
 * @copyright 2008-2013 (c) Kjell Bublitz
 * @link https://github.com/m3nt0r/eventful-cakephp
 * @link https://github.com/m3nt0r
 * @package eventful
 * @subpackage libs
 * @version $Id$
 */

/**
 * AppEvents Class
 * 
 * Base class for Controller/Models-Event base classes
 * 
 * @package eventful
 * @subpackage libs
 */
class AppEvents extends Object 
{
	function __construct($name, $params = null) {
		$this->name = $name;
		$this->params = $params;
	}
}


require_once 'app_events/controller.php';
require_once 'app_events/model.php';
