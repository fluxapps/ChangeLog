<?php

namespace srag\Plugins\ChangeLog\Component;

use ilChangeLogPlugin;
use ilException;
use srag\DIC\DICTrait;
use srag\Plugins\ChangeLog\LogEntry\Creation\ChangeLogCreationEntry;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationEntry;

/**
 * Interface ChangeLogComponent
 *
 * @package srag\Plugins\ChangeLog\Component
 */
abstract class ChangeLogComponent {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	const COMPONENT_ID = '';
	/**
	 * used for plugin objects
	 */
	protected $lng_prefix = '';
	/**
	 * Define the attributes that will be checked in getModification in the
	 * form array ( [lang_var] => [getterMethod] ). If defined, $lng_prefix
	 * will be prepended.
	 *
	 * @var array
	 */
	protected $attributes = array();
	/**
	 * @var array
	 */
	protected $dependencies = array();


	/**
	 * Return a unique ID among all components
	 *
	 * @return string
	 */
	abstract public function getId();


	/**
	 * Return a title for this component
	 *
	 * @return string
	 */
	abstract public function getTitle();


	/**
	 * Return the ChangeLogDeletion object for this component
	 *
	 * @param array $parameters
	 *
	 * @return ChangeLogDeletionEntry
	 */
	public function getDeletion(array $parameters) {
		return NULL;
	}


	/**
	 * Return the Id to be stored as ChangeLogModificationEntry->obj_id, which will be used e.g. to create
	 * a Link to the object inside the changelog tableGUI. Tries out getRefId, then getId on the object.     *
	 *
	 * @param $object
	 *
	 * @return mixed
	 * @throws ilException
	 */
	protected function getModificationObjId($object) {
		if (method_exists($object, 'getRefId')) {
			return $object->getRefId();
		} elseif (method_exists($object, 'getId')) {
			return $object->getId();
		} else {
			throw new ilException("ChangeLog: No id found for object of type " . get_class($object));
		}
	}


	/**
	 * @param $object
	 *
	 * @return mixed
	 * @throws ilException
	 */
	protected function getModificationTitle($object) {
		if (method_exists($object, 'getTitle')) {
			return $object->getTitle();
		} else {
			throw new ilException("ChangeLog: No title found for object of type " . get_class($object));
		}
	}


	/**
	 * Generic method to return the ChangeLogModificationEntry object for this component.
	 *
	 * To create a new component, simply fill in the attributes you want to check for
	 * modification in the form: array([attribute_name] => [getAttributeMethod], ...),
	 * e.g.: array('title' => 'getTitle', 'description' => 'getDescription', ...)
	 *
	 * Also Overwrite $this->getTitle() and self::COMPONENT_ID. The ID must be the same
	 * as passed in $ilAppEventHandler->raise([COMPONENT_ID], 'changelogModify', $parameters)
	 * (see doc in ChangeLogChangeLog->handleModification)
	 *
	 * @param array $parameters
	 *
	 * @return ChangeLogModificationEntry
	 */
	public function getModification(array $parameters) {
		if ($parameters['object']) {
			$new_object = $parameters['object'];
			$old_object = $this->getOldObject($new_object);
		} elseif ($parameters['old_object'] && $parameters['new_object']) {
			$old_object = $parameters['old_object'];
			$new_object = $parameters['new_object'];
		} else {
			self::dic()->log()->write('ChangeLogChangeLog:: raised changelogModify event with wrong parameters - no object found');

			return NULL;
		}

		$modification = new ChangeLogModificationEntry();
		$modification->setTitle($this->getModificationTitle($new_object));
		$modification->setObjId($this->getModificationObjId($new_object));

		foreach ($this->attributes as $attribute => $getter_method) {
			$old_value = $old_object->$getter_method();
			$new_value = $new_object->$getter_method();

			if (is_array($old_value) && is_array($new_value)) {
				array_multisort($old_value);
				array_multisort($new_value);
			}
			if ($old_value != $new_value) {
				$modification->addModification($attribute, array(
					'old' => $this->formatValue($attribute, $old_value),
					'new' => $this->formatValue($attribute, $new_value),
				), $this->lng_prefix);
			}
		}

		return $modification;
	}


	/**
	 * Generic method to return the ChangeLogModificationEntry object for this component. Called by event 'changelogCreate'.
	 *
	 * @param array $parameters
	 *
	 * @return ChangeLogModificationEntry
	 */
	public function getCreation(array $parameters) {
		$object = $parameters['object'];
		$creation = new ChangeLogCreationEntry();
		$creation->setTitle($this->getModificationTitle($object));
		$creation->setObjId($this->getModificationObjId($object));

		foreach ($this->attributes as $attribute => $getter_method) {
			$value = $object->$getter_method();
			if ($value !== NULL) {
				$creation->addCreation($attribute, $this->formatValue($attribute, $value), $this->lng_prefix);
			}
		}

		return $creation;
	}


	/**
	 * Load and return the object in the old state from the DB.
	 * The new object is filled with the updated values, but not
	 * yet stored to the db.
	 *
	 * @param $new_object
	 *
	 * @return mixed
	 */
	public function getOldObject($new_object) {
		$class = get_class($new_object);

		return new $class($new_object->getId());
	}


	/**
	 * Overwrite if you want any values formatted. Format of all values should be string for
	 * the tablegui to represent them.
	 *
	 * @param $attribute
	 * @param $value
	 *
	 * @return String
	 */
	function formatValue($attribute, $value) {
		return $value;
	}


	/**
	 * Overwrite this and return array of form array('cmdClass' => ..., 'cmd' =>, 'attribute' => ...)
	 * to enable title as link in the modification table gui
	 *
	 * @return array cmdClass, cmd and attribute for creation of links
	 */
	public function getCtrlParameters() {
		return NULL;
	}
}
