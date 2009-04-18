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
	
	public static function file2class($filename) {
		return Inflector::camelize(r('.php', '', $filename));
	}
	
	public function loadListeners($dir = 'controllers') {
		$loadable = array();
		$filePaths = $this->eventFilePaths($dir);
		
		foreach ($filePaths as $className => $classFilePath) {
			if (App::import('File', $className, array('file' => $classFilePath))) {
				
				$eventType = 'app';
				$pluginDir = null;
				
				$filePathArray = explode(DS, $classFilePath);
				if ($filePathKey = array_search('plugins', $filePathArray)) {
					$eventType = 'plugin';
					$pluginDir = $filePathArray[$filePathKey+1];
				}
				
				$loadable[$className] = compact('className', 'eventType', 'pluginDir', 'classFilePath');

			} else {
				unset($filePaths[$i]);
			}
		}
		Cache::write('event_class_paths', $filePaths);

		return $loadable;
	}
	
	
	public function eventFilePaths($dir = 'controllers') {
		App::import('Core', 'Folder'); 
		
		$eventFilePaths = array();
		
		// Lookup APP events
		$events = new Folder(EVENTS . $dir);
		list($folders, $files) = $events->ls();
		foreach ($files as $listenerClassFile)
			$eventFilePaths[self::file2class($listenerClassFile)] = $events->path . DS . $listenerClassFile;
		
		// Lookup PLUGIN events
		$plugins = new Folder(PLUGINS);
		list($folders, $files) = $plugins->ls();
		if (count($folders) > 1) {
			foreach ($folders as $pluginsFolder) {
				if ($pluginsFolder == 'eventful') continue; 
				
				$pluginEvents = new Folder(PLUGINS . $pluginsFolder . DS . EVENTS_DIR . DS . $dir);
				list($folders, $files) = $pluginEvents->ls();
				
				foreach ($files as $listenerClassFile)
					$eventFilePaths[self::file2class($listenerClassFile)] = $pluginEvents->path . DS . $listenerClassFile;
			}
		}
		
		return $eventFilePaths;
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
		
		if ($plugin) { // disabled unless i find out how to create a fallback class at runtime
			# App::import('File', PLUGINS. $plugin .DS. $plugin . '_app_controller_events.php');
			# App::import('File', PLUGINS. $plugin .DS. $plugin . '_app_model_events.php');		
		}
		
		if (in_array($eventClassName, array_keys($this->listeners))) {
			return $this->listeners[$eventClassName][2];
		}		
		
		// create instance and add as listener class
		$listener = new $eventClassName($eventClassName, array('type' => $type, 'plugin' => $plugin));
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
	public function dispatchEvent($name, $data = array(), $global = true) {
		return $this->EventDispatcher->dispatchEvent(new Event($name, $data), $global);
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