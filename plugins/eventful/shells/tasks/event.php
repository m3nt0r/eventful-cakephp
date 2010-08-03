<?php
/**
 * Eventful CakePHP
 *
 * Task to access the shell event system.
 * @author Gustavo Dutra <mechamo@gustavodutra.com> 
 * @package eventful
 * @subpackage shells
 */

 /**
  * EventTask
  */
class EventTask extends Shell {

	/**
	 * Array with loaded event listener classes
	 * @var array
	 */
	public $listeners = array();

	/**
	 * @param ShellDispatcher $Dispatcher
	 */
	public function __construct($Dispatcher) {
		parent::__construct($Dispatcher);
		App::import('Vendor', 'Eventful.Startup'); // bootstrap

		$this->Dispatcher = $Dispatcher;
		$this->CakeEvents = CakeEvents::getInstance();

		$listeners = $this->CakeEvents->loadListeners('shells');
		foreach ($listeners as $class => $params) {
			extract($params);
			$this->listeners[$class] = $this->CakeEvents->addListener($className, $eventType, $pluginDir);
		}
	}

	public function dispatch($event, $data = array()) {
		$return = array();

		// Set shell reference
		ClassRegistry::addObject('EventShell', $this->Dispatcher);

		$result = $this->CakeEvents->dispatchEvent(
			$event,
			am($data, array('Dispatcher' => $this->Dispatcher))
		);

		ClassRegistry::removeObject('EventShell');

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

?>
