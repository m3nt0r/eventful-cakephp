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

if (!class_exists('CakeEvents')) {
	
	// CakeEvents
	App::import('Lib', 'Eventful.CakeEvents');
	App::import('Lib', 'Eventful.AppEvents');

	// Register Dispatcher
	ClassRegistry::addObject('event_dispatcher', new EventDispatcher());
	
	// Lookup app folder for custom base classes
	App::import('File','AppModelEvents', false, array(), APP . 'app_model_events.php');	
	App::import('File','AppControllerEvents', false, array(), APP . 'app_controller_events.php');
	
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
}
