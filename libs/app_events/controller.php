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
 * ModelEvents
 * 
 * @package eventful
 * @subpackage app_events
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