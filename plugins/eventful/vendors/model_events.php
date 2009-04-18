<?php
/**
 * Eventful CakePHP
 * 
 * Model Events. Extend App*Events from this
 * 
 * @author Kjell Bublitz <kjell@growinthings.de>
 * @copyright 2008-2009 (c) Kjell Bublitz
 * @link http://cakealot.com
 * @package eventful
 * @subpackage app
 * @version $Id$
 */

/**
 * ModelEvents
 * 
 * @package eventful
 * @subpackage app
 */
class ModelEvents extends AppEvents {
	
	/**
	 * EventListener Name
	 *
	 * @var string
	 */
	public $name = 'ModelEvents';
}