<?php
/**
 * Eventful CakePHP
 *
 * Use the dispatch() method
 *
 * OPTIONS:
 * - triggerDefaults: (BOOL) Auto-dispatch beforeSave/afterDelete/beforeFind etc..
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage behaviors
 * @version $Id$
 */

/**
 * Event Behavior
 * 
 * @package eventful
 * @subpackage behaviors
 */
class EventBehavior extends ModelBehavior {
	
	/**
	 * Array with loaded event listener classes
	 *
	 * @var array
	 */
	public $listeners = array();	
	
	/**
	 * Default Settings
	 * 
	 * @var array
	 */
	public $defaultConfig = array(
		'triggerDefaults' => true
	);
	
	/**
	 * Setup this behavior with the specified configuration settings.
	 *
	 * @param object $model Model using this behavior
	 * @param array $config Configuration settings for $model
	 * @access public
	 */
	function setup(&$model, $config = array()) {
		App::import('Vendor', 'Eventful.Startup'); // bootstrap
		
		$this->settings[$model->alias] = am($this->defaultConfig, $config);
		$this->CakeEvents = CakeEvents::getInstance();		
		
		$listeners = $this->CakeEvents->loadListeners('models');
		foreach ($listeners as $class => $params) {	extract($params);
			$this->listeners[$class] = $this->CakeEvents->addListener($className, $eventType, $pluginDir);
		}
	}
	
	/**
	 * Wrapper for default event dispatching
	 * 
	 * @param object $model
	 * @param string $event
	 * @param array $data (optional)
	 * @access public
	 */
	function dispatchEvent($model, $event, $data = array(), $global = true) {
		$cake_events = CakeEvents::getInstance();
		return $cake_events->dispatchEvent($event, am($data, array('Model' => $model)), $global);
	}

	/**
	 * Before find callback
	 *
	 * @param object $model Model using this behavior
	 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeFind (&$model, $query) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'beforeFind', array('query' => $query), false);
		}
	}
	/**
	 * After find callback.
	 *
	 * @param object $model Model using this behavior
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly or via association
	 * @return mixed Result of the find operation
	 * @access public
	 */
	function afterFind ($model, $results, $primary) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'afterFind', array('results' => $results, 'primary' => $primary));
		}
	}
	/**
	 * Before validate callback
	 *
	 * @param object $model Model using this behavior
	 * @return boolean True if validate operation should continue, false to abort
	 * @access public
	 */
	function beforeValidate (&$model) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'beforeValidate');
		}
	}
	/**
	 * Before save callback
	 *
	 * @param object $model Model using this behavior
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeSave (&$model) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'beforeSave');
		}
	}
	/**
	 * After save callback
	 *
	 * @param object $model Model using this behavior
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	function afterSave (&$model, $created) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'afterSave', array('created' => $created));
		}
	}
	/**
	 * Before delete callback
	 *
	 * @param object $model Model using this behavior
	 * @param boolean $cascade If true records that depend on this record will also be deleted
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeDelete (&$model, $cascade = true) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'beforeDelete', array('cascade' => $cascade));
		}
	}
	/**
	 * After delete callback
	 *
	 * @param object $model Model using this behavior
	 * @access public
	 */
	function afterDelete (&$model) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'afterDelete');
		}
	}
	/**
	 * DataSource error callback
	 *
	 * @param object $model Model using this behavior
	 * @param string $error Error generated in DataSource
	 * @access public
	 */
	function onError (&$model, $error) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'datasourceError', array('error' => $error));
		}
	}

}
