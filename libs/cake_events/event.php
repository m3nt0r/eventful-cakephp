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
 * Event Object
 *
 * An instance of this class is passed to the event handler as first argument.
 * Possibly contains data from the dispatched event for use in the handler methods.
 * 
 * @package eventful
 * @subpackage libs
 */
class Event {
	
	/**
	 * Contains assigned values
	 *
	 * @var array
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