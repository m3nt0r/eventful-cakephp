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
 * CakeEvents
 *
 * Singleton responsible for loading listener classes by classname and to manage/cache paths
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
	 * Ignore these file extensions when determining listeners
	 *
	 * @var array
	 */
	public $ignore = array('bak', 'svn', 'gitignore');

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
	 * Convert a filename to a class name
	 *
	 * @param string $filename Any PHP file following the conventions
	 * @return string Camelcased class name
	 */
	public static function file2class($filename) {
		return Inflector::camelize(r('.php', '', $filename));
	}

	/**
	 * Find and prepare all possible listener classes.
	 * Returns a array with classname as key and addListener parameters array as value
	 *
	 * @see CakeEvents::eventFilePaths()
	 * @param string $dir Currently: 'controllers' and 'models'
	 * @return array Available Listeners
	 */
	public function loadListeners($dir = 'controllers') {
		$loadable = array();
		$filePaths = $this->eventFilePaths($dir);

		foreach ($filePaths as $className => $classFilePath) {
			if (App::import('File', $className, array('file' => $classFilePath))) {
//cdv-debug($filePaths);
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

	/**
	 * Walk through all directories and search for listener classes
	 * Returns a array with classname as key and full path as value
	 *
	 * @param string $dir Currently: 'controllers' and 'models'
	 * @return array
	 */
	public function eventFilePaths($dir = 'controllers') {
		App::import('Core', 'Folder');

		$eventFilePaths = array();
		
		// get controller-type event handler filepaths
		$events = new Folder(APP.'controllers');
		list($folders, $files) = $events->read();
		foreach ($files as $listenerClassFile) {
			if (array_pop(explode('_', $listenerClassFile)) === 'events.php') {
				$eventFilePaths[self::file2class($listenerClassFile)] = $events->path . DS . $listenerClassFile;
			}
		}
		
		// get model-type event handler filepaths
		$events = new Folder(APP.'models');
		list($folders, $files) = $events->read();
		foreach ($files as $listenerClassFile) {
			if (array_pop(explode('_', $listenerClassFile)) === 'events.php') {
				$eventFilePaths[self::file2class($listenerClassFile)] = $events->path . DS . $listenerClassFile;
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
		
		if ($eventClassName == 'Empty') return false;  //Catch if eventClass is passed as "Empty" when nothing was configured. Was resulting in "Class 'Empty' not found" was the fatal error message.
		
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
}

require_once 'cake_events/event.php';
require_once 'cake_events/dispatcher.php';