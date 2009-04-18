<?php
/**
 * Eventful CakePHP
 *
 * Bootstrap. Loaded on EventComponent init
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage scripts
 * @version $Id$
 */

/**
 * Eventful Bootstrap
 * 
 * @package eventful
 * @subpackage scripts
 */
if (!defined('EVENTS_DIR')) {
	define('EVENTS_DIR', 'events');
}
if (!defined('EVENTS')) {
	define('EVENTS', APP . EVENTS_DIR . DS);
}
if (!defined('PLUGINS')) {
	define('PLUGINS', APP . 'plugins' . DS);
}

// Libraries
App::import('Vendor', 'Eventful.Event');
App::import('Vendor', 'Eventful.EventDispatcher');
App::import('Vendor', 'Eventful.CakeEvents');
App::import('Vendor', 'Eventful.AppEvents');

// Register EventDispatcher
ClassRegistry::addObject('event_dispatcher', new EventDispatcher());

// Base Classes
App::import('Vendor', 'Eventful.ControllerEvents');
App::import('Vendor', 'Eventful.ModelEvents');

// Lookup app folder for custom base classes
App::import('File', APP . 'app_model_events.php');
App::import('File', APP . 'app_controller_events.php');

// Define default custom base classes if none available
if (!class_exists('AppControllerEvents')) {
	class AppControllerEvents extends ControllerEvents {
		var $name = 'AppControllerEvents';
	}
}
if (!class_exists('AppModelEvents')) {
	class AppModelEvents extends ModelEvents {
		var $name = 'AppModelEvents';
	}
}


