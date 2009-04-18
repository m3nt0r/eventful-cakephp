<?php
/**
 * Eventful CakePHP
 *
 * Event Object Class. An instance of this class 
 * is passed to the event handler as first argument.
 * Possibly contains data from the dispatched event
 * for use in the handler methods.
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage libs
 * @version $Id$
 */

/**
 * Event Object
 *
 * @package core
 * @subpackage core.libs
 */
class Event {
	
	/**
	 * Contains assigned values
	 */
	protected $values = array();
	
	/**
	 * Constructor with EventType and EventData (optional)
	 *
	 * @param string $eventType Name of the Event
	 * @param array $data optional array with k/v data
	 */
	public function __construct($eventType, $data = array()) {
		$this->type = $eventType;
		
		if (!empty($data)) {
			foreach ($data as $name => $value) {
				$this->{$name} = $value;
			} // push data values to props
		}
	}
	
	/**
	 * Write to object
	 *
	 * @param string $name Key
	 * @param mixed $value Value
	 */
	public function __set($name, $value) {
		$this->values[$name] = $value;
	}
	
	/**
	 * Read from object
	 * 
	 * @param string $name Key
	 */	
	public function __get($name) {
		return $this->values[$name];
	}
}