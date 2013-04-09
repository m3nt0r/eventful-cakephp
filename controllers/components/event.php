<?php
/**
 * Eventful CakePHP
 * 
 * @author Kjell Bublitz <m3nt0r.de@gmail.com>
 * @copyright 2008-2013 (c) Kjell Bublitz
 * @link https://github.com/m3nt0r/eventful-cakephp
 * @link https://github.com/m3nt0r
 * @package eventful
 * @subpackage components
 * @version $Id$
 */

/**
 * EventComponent
 * 
 * Trigger controller_events via `$this->Event->dispatch()`
 * 
 * @package eventful
 * @subpackage components
 */
class EventComponent extends Object {
	
	/**
	 * Array with loaded event listener classes
	 *
	 * @var array
	 */
	public $listeners = array();
	
	/**
	 * On every controller startup
	 *
	 * @param unknown_type $controller
	 */
	public function initialize(&$controller, $settings) {
		App::import('Vendor', 'Eventful.Startup'); // bootstrap
		
		$this->Controller = $controller;
		$this->CakeEvents = CakeEvents::getInstance();		
		
		$listeners = $this->CakeEvents->loadListeners('controllers');		
		foreach ($listeners as $class => $params) { extract($params);			
			$this->listeners[$class] = $this->CakeEvents->addListener($className, $eventType, $pluginDir);
		}
	}
	
	/**
	 * Wrapper method to Events::dispatchEvent
	 *
	 * @param string $event Name of the event
	 * @param array $data Any data to attach
	 * 
	 * @return mixed FALSE -or- assoc result array
	 */
	public function dispatch($event, $data = array()) {
		$return = array();
		
		// Set controller reference
		ClassRegistry::addObject('EventController', $this->Controller);
		
		// Do whatever it does
		$result = $this->CakeEvents->dispatchEvent($event, am($data, array(
			'Controller' => $this->Controller
		)));
		
		// Unset controller reference
		ClassRegistry::removeObject('EventController');
		
		if ($result) {
			foreach ($result as $eventResult) {
				if ($eventResult['returns']) {
					$return[$eventResult['name']] = $eventResult['returns'];
				}
			}
		}
		return $return;
	}
}