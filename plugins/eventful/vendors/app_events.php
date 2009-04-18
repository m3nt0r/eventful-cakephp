<?php 
/**
 * Eventful CakePHP
 * 
 * AppEvent. Base class for Controller/Models-Event base classes
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage libs
 * @version $Id$
 */

/**
 * AppEvents Class
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
	
	function renderElement($element, $data = array()) {
		$view = new View(ClassRegistry::getObject('EventController'), false);
		$view->set('data', $data);
		$html = $view->element($element, $this->params, true);		
		$view = null; unset($view); return $html;
	}
}