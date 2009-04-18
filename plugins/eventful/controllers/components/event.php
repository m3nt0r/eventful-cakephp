<?php
/**
 * Eventful CakePHP
 * 
 * Component to access the controller event system.
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage components
 * @version $Id$
 */

/**
 * EventComponent
 * 
 * 
 * @package eventful
 * @subpackage components
 */
class EventComponent extends Object {
	
	/**
	 * Default state is "no listener available"
	 *
	 * @var unknown_type
	 */
	public $Listener = false;
	
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
	 * @param boolean $global If true, all possible listeners are notified
	 * 
	 * @return mixed FALSE -or- assoc result array
	 */
	public function dispatch($event, $data = array(), $global = true) {
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