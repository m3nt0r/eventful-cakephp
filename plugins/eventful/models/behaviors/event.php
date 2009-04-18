<?php
/**
 * Eventful CakePHP
 *
 * Use the method dispatchEvent() to send a event notification to your ModelEvents class.
 *
 * OPTIONS:
 * - triggerDefaults: (BOOL) Auto-dispatch beforeSave/afterDelete/beforeFind etc..
 * - pluginDir: (STRING) Directory. Make sure you've created "events"-dir in the plugin dir
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
	 * Defaults
	 */
	public $defaultConfig = array(
		'triggerDefaults' => true, 
		'pluginDir' => ''
	);
	
	/**
	 * Add a Event Listener to this model class
	 * 
	 * @param object $model
	 * @access public
	 */
	function addListener(&$model) {
		$plugin = $this->settings[$model->alias]['pluginDir'];
		$type = (!empty($plugin) ? 'plugin' : 'app'); 
		
		$events = CakeEvents::getInstance();
		$model->listenerClass = $model->name . 'Events';
		$model->hasListener = $events->addListener($model->listenerClass, $type, $plugin);
	}
	/**
	 * Unregisters a class listener
	 *
	 * @param object $model
	 */
	function removeListener(&$model) {
		if (!$model->hasListener) return false;
		$events = CakeEvents::getInstance();
		$listener = $events->getListener($model->listenerClass);
		$model->hasListener = false;
		return $events->removeListener($listener);
	}
	/**
	 * Wrapper for default event dispatching
	 * 
	 * @param object $model
	 * @param string $event
	 * @param array $data (optional)
	 * @access public
	 */
	function dispatchEvent ($model, $event, $data = array()) {
		if (! $model->hasListener) return false;
		$events = CakeEvents::getInstance();
		return $events->dispatchEvent($event, am($data, array(
			'Model' => $model
		)));
	}
	/**
	 * Setup this behavior with the specified configuration settings.
	 *
	 * @param object $model Model using this behavior
	 * @param array $config Configuration settings for $model
	 * @access public
	 */
	function setup(&$model, $config = array()) {
		$this->settings[$model->alias] = am($this->defaultConfig, $config);
		$this->addListener($model);
	}
	/**
	 * Clean up any initialization this behavior has done on a model.  Called when a behavior is dynamically
	 * detached from a model using Model::detach().
	 *
	 * @param object $model Model using this behavior
	 * @access public
	 * @see BehaviorCollection::detach()
	 */
	function cleanup (&$model) {
		parent::cleanup($model);
		$this->removeListener($model);
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
			$this->dispatchEvent($model, 'beforeFind', array('model' => $model, 'query' => $query));
		}
	}
	/**
	 * After find callback. Can be used to modify any results returned by find and findAll.
	 *
	 * @param object $model Model using this behavior
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly or via association
	 * @return mixed Result of the find operation
	 * @access public
	 */
	function afterFind ($model, $results, $primary) {
		if ($this->settings[$model->alias]['triggerDefaults']) {
			$this->dispatchEvent($model, 'afterFind', array('model' => $model, 'results' => $results, 'primary' => $primary));
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
			$this->dispatchEvent($model, 'beforeValidate', array('model' => $model));
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
			$this->dispatchEvent($model, 'beforeSave', array('model' => $model));
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
			$this->dispatchEvent($model, 'afterSave', array('model' => $model, 'created' => $created));
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
			$this->dispatchEvent($model, 'beforeDelete', array('model' => $model, 'cascade' => $cascade));
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
			$this->dispatchEvent($model, 'afterDelete', array('model' => $model));
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
			$this->dispatchEvent($model, 'error', array('model' => $model, 'error' => $error));
		}
	}

}
?>