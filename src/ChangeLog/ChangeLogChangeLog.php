<?php

namespace srag\Plugins\ChangeLog\ChangeLog;

use ilChangeLogPlugin;
use ilException;
use srag\DIC\ChangeLog\DICTrait;
use srag\Plugins\ChangeLog\Component\ChangeLogComponent;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentCourseParticipant;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentObject;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentOrgUnit;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentUser;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class ChangeLogChangeLog
 *
 * @package srag\Plugins\ChangeLog\ChangeLog
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogChangeLog {

	use DICTrait;
	use ChangeLogTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	/**
	 * @var ChangeLogChangeLog
	 */
	protected static $instance;
	/**
	 * @var ChangeLogComponent[]
	 */
	protected $components = array();


	private function __construct() {
		// Register standard components shipped with this module
		$this->registerComponent(new ChangeLogComponentObject());
		$this->registerComponent(new ChangeLogComponentCourseParticipant());
		$this->registerComponent(new ChangeLogComponentUser());
		$this->registerComponent(new ChangeLogComponentOrgUnit());
	}


	/**
	 * @param ChangeLogComponent $component
	 */
	public function registerComponent(ChangeLogComponent $component) {
		$this->components[$component->getId()] = $component;
	}


	/**
	 * Handles events of type 'changelogModify'. To add a new component, simply register the component in this class' constructor.
	 * Note, that the ChangeLogComponent's ID must be the same as the component ID passed when raising the event:
	 * $ilAppEventHandler->raise([COMPONENT_ID], 'changelogModify', $parameters);
	 *
	 * If you want to use the generic getModification method in ChangeLogComponent, raise the event after changing the object but before
	 * storing the changes to the db, e.g.  between 'fillObject' and 'update', and pass the object in $parameters['object'].
	 *
	 *
	 * @param $comp_id
	 * @param $parameters
	 *
	 */
	public function handleModification($comp_id, $parameters) {
		if (!isset($this->components[$comp_id])) {
			self::dic()->log()->write("ChangeLogChangeLog::handleModification no component registered for component id $comp_id");

			return;
		}

		$component = $this->components[$comp_id];
		$modification = $component->getModification($parameters);

		if (!$modification) {
			return;
		}

		if (!$modification->getComponentId()) {
			$modification->setComponentId($component->getId());
		}

		if ($modification->hasModifications()) {
			$modification->save();
		}
	}


	public function handleCreation($comp_id, $parameters) {
		if (!isset($this->components[$comp_id])) {
			self::dic()->log()->write("ChangeLogChangeLog::handleCreation no component registered for component id $comp_id");

			return;
		}

		$component = $this->components[$comp_id];
		$modification = $component->getCreation($parameters);

		if (!$modification->getComponentId()) {
			$modification->setComponentId($component->getId());
		}
		if ($modification->hasModifications()) {
			$modification->save();
		}
	}


	/**
	 * Track a new deletion with a given ChangeLogComponent
	 *
	 * @param ChangeLogComponent $component
	 * @param array              $parameters
	 * @param int                $deletion_mode
	 *
	 * @throws ilException
	 */
	public function trackDeletion(ChangeLogComponent $component, array $parameters, $deletion_mode) {
		if (!isset($this->components[$component->getId()])) {
			throw new ilException("ChangeLog: Unknown component '{$component->getId()}': This component is not registered!");
		}
		/** @var ChangeLogComponent $ChangeLogComponent */
		$deletion = $component->getDeletion($parameters);

		if (!$deletion->getComponentId()) {
			$deletion->setComponentId($component->getId());
		}
		$deletion->setDeletionMode($deletion_mode);
		$deletion->save();
	}


	/**
	 * @return ChangeLogComponent[]
	 */
	public function getComponents() {
		return $this->components;
	}


	/**
	 * @param string $component_id
	 *
	 * @return ChangeLogComponent|null
	 */
	public function getComponentById($component_id) {
		return (isset($this->components[$component_id])) ? $this->components[$component_id] : NULL;
	}


	/**
	 * @return ChangeLogChangeLog
	 */
	public static function getInstance() {
		if (static::$instance === NULL) {
			static::$instance = new static();
		}

		return static::$instance;
	}
}
