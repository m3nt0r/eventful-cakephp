<?php
/**
 * Eventful CakePHP
 * 
 * Controller Events. Extend App*Events from this
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage app
 * @version $Id$
 */

/**
 * ControllerEvents
 * 
 * @package eventful
 * @subpackage app
 */
class ControllerEvents extends AppEvents {
	
	/**
	 * Name of this EventListener
	 *
	 * @var string
	 */
	public $name = 'ControllerEvents';
	
	/*
	 * renderElement
	 * 	
	 * This allows you to render Elements from a handler method 
	 * within the scope of the current controller
	 * 
	 * @param string $element Name of the element
	 * @param array $data Any data you want to pass as variable
	 */
	protected function renderElement($element, $data = array()) {
		$view = new View(ClassRegistry::getObject('EventController'), false);
		$view->set('data', $data);
		$html = $view->element($element, $this->params, true);		
		$view = null; unset($view); return $html;
	}	
}