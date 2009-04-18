<?php
/**
 * Eventful CakePHP
 * 
 * Cake Events Class. Singleton, loading listener classes
 * by classname and manage/cache paths, etc.. 
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage libs
 * @version $Id$
 */

/**
 * CakeEvents.
 * EventDispatcher Wrapper
 * 
 * @package eventful
 * @subpackage libs
 */
class CakeEvents extends Object {

	/**
	 * Listener class names and their params
	 *
	 * @var array
	 */
	private $listeners = array();
	
	/**
	 * List of not existing event class filepaths
	 *
	 * @var unknown_type
	 */
	private $notFound = array();
	
	/**
	 * Singleton Class
	 *
	 * @return object
	 */
	function &getInstance() {
		
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new CakeEvents();
		}
		// Add EventDispatcher as property 		
		$instance[0]->EventDispatcher = ClassRegistry::getObject('event_dispatcher');
		return $instance[0];
	}
		
	/**
	 * Load and add a listener
	 *
	 * @param string $eventClassName The name of the event listener class
	 * @param string $type Type of event listener Available: core, app, plugin (default: core)
	 * @param string $plugin Plugin foldername (required for type=plugin)
	 * @return mixed false if unsuccessful or an array with params
	 */
	public function addListener($eventClassName, $type = 'app', $plugin = '') {

		if ($type == 'plugin' && empty($plugin)) return false;
		$classPathsCache = Cache::read('event_class_paths');
		$classPaths = $classPathsCache ? $classPathsCache : array();
		
		if (in_array($eventClassName, array_keys($classPaths))) {
			
			$eventClassPath = $classPaths[$eventClassName];
			
		} else { // not in cache
			
			$eventClassFile = Inflector::underscore($eventClassName);			
			$class = explode('_', $eventClassFile);
					
			switch ($type) {
				case 'app':
					if (in_array('controller', $class)) {
						$eventClassPath = EVENTS . 'controllers' . DS . $eventClassFile . '.php';
					} else {
						$eventClassPath = EVENTS . 'models' . DS . $eventClassFile . '.php';
					}
					break;
							
				case 'plugin':
					if (in_array('controller', $class)) {
						$eventClassPath = PLUGINS . $plugin . DS . EVENTS_DIR .DS.'controllers'.DS. $eventClassFile . '.php';
					} else {
						$eventClassPath = PLUGINS . $plugin . DS . EVENTS_DIR .DS.'models'.DS. $eventClassFile . '.php';
					}
					break;
			}
			
			$classPaths[$eventClassName] = $eventClassPath;
			Cache::write('event_class_paths', $classPaths);			
		}
				
		if (!is_file($eventClassPath)) {
			$this->notFound[] = $eventClassPath;
			return false;
		}
		
		if (class_exists($eventClassName)) {
			return $this->listeners[$eventClassName][2];
		}
		
		if ($plugin) { 
			App::import('File', PLUGINS. $plugin .DS. $plugin . '_app_controller_events.php');
			App::import('File', PLUGINS. $plugin .DS. $plugin . '_app_model_events.php');		
		}
		
		require_once ($eventClassPath);
		
		$listener = new $eventClassName($eventClassName, array(
			'type' => $type,
			'plugin' => $plugin,
			'file' => str_replace(APP, '', $eventClassPath),
		));
		
		if ($this->EventDispatcher->addListener($listener)) {
			$this->listeners[$listener->name] = array($listener, $listener->name, $listener->params);
			return $listener->params;
		}
		return false;
	}
	
	/**
	 * Dispatch a event to all loaded listeners
	 *
	 * @param string $name Event Name
	 * @param string $data Event Data (optional)
	 * @return array result
	 */
	public function dispatchEvent($name, $data = array()) {
		return $this->EventDispatcher->dispatchEvent(new Event($name, $data));
	}
	
    /**
     * Unregister a class listener
     * 
     * Unregisters a class listener. Each method of this instance that matches "on[event]" will be 
     * registered for the corresponding event. See "addListener" method for more details on this.
     *
     * @param object listener the listener to be unregistered
     */
	public function removeListener($listener) {
		return $this->EventDispatcher->removeListener($listener);
	}
	
	/**
	 * Get a listener object by class name
	 *
	 * @param string $className
	 * @return object
	 */
	public function getListener($className) {
		return $this->listeners[$className][0];
	}
	
	/**
	 * Getter: $listeners
	 *
	 * @return unknown
	 */
	public function getListeners() {
		
		return $this->listeners;
	}
	
	/**
	 * Getter: $notFound
	 *
	 * @return unknown
	 */	
	public function getNotFound() {
		
		return $this->notFound;
	}	
}