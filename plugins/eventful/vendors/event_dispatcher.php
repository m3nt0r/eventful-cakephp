<?php
/**
 * Eventful CakePHP
 * 
 * EventDispatcher Class. Manages and calls
 * all registered EventListeners and Handlers 
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage libs
 * @version $Id$
 */

/**
 * Event Dispatcher
 * 
 * @package core
 * @subpackage core.libs
 */
class EventDispatcher {
	
	/**
	 * Event <-> Listener
	 * 
	 * @var array
	 */
	protected $events = array();
	
	/**
	 * Dispatch the event, using the Event Class Object
	 *
	 * @param object $event Instance of Event object
	 */
	public function dispatchEvent($event, $global = true) {
		
		$result = array();
		$eventName = low($event->type);
				
		if (!array_key_exists($eventName, $this->events)) return false;
		foreach ((array) $this->events[$eventName] as $listener) {
			if (is_callable($listener)) {
				$return = call_user_func($listener, $event);
				$result[] = array(
					'name' => $listener[0]->name, 
					'params' => (isset($listener[0]->params) ? $listener[0]->params : ''), 
					'method' => $listener[1], 
					'returns' => $return
				);
			}
		}
		return $result;
	}	
	
	/**
	 * Register class listener.
	 *
	 * @param object $listener the listener to be registered
	 * @return boolean
	 */
	public function addListener($listener) {
		if (!is_object($listener)) return false;
		$methods = get_class_methods($listener);
		foreach ($methods as $method) {
			if (substr($method, 0, 2) == 'on') {
				$this->addEventListener(substr($method, 2), array(&$listener, $method));
			}
		}
		return true;
	}
	
	/**
	 * Unregisters a class listener.
	 *
	 * @param object $listener Listener to unregister
	 * @return boolean
	 */
	public function removeListener($listener) {
		if (!is_object($listener)) return false;
		$methods = get_class_methods($listener);
		foreach ($methods as $method) {
			if (substr($method, 0, 2) == 'on') {
				$this->removeEventListener(substr($method, 2), array(&$listener, $method));
			}
		}
		return true;
	}
	
	/**
	 * Register callable function/method as listener
	 *
	 * @param string $event Event name
	 * @param mixed $listener function/method name
	 */
	public function addEventListener($event, $listener) {
		
		
		$this->events[low($event)][] = $listener;
	}
		
	/**
	 * Unregister a callable function/method 
	 *
	 * @param string $event Event name
	 * @param mixed $listener function/method name
	 */
	public function removeEventListener($event, $listener) {
		$this->events[low($event)] = array_remove($this->events[low($event)], $listener);
	}
}
