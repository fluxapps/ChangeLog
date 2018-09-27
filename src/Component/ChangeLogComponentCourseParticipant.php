<?php

namespace srag\Plugins\ChangeLog\Component;

use ilObject;
use ilObjUser;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;

/**
 * Class ChangeLogComponentObject
 *
 * @package srag\Plugins\ChangeLog\Component
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogComponentCourseParticipant extends ChangeLogComponent {

	const COMPONENT_ID = 'course_participant';


	/**
	 * Return the ChangeLogDeletionEntry object for this component
	 *
	 * @param array $parameters
	 *
	 * @return ChangeLogDeletionEntry
	 */
	public function getDeletion(array $parameters) {
		/** @var ilObject $object */
		$obj_id = $parameters['obj_id'];
		$user_id = $parameters['usr_id'];
		/** @var ilObjUser $object */
		$object = new ilObjUser($user_id);
		$deletion = new ChangeLogDeletionEntry();
		$deletion->setObjId($user_id);
		$course_ref_id = array_pop(ilObject::_getAllReferences($obj_id));
		$deletion->setContainerRefId($course_ref_id);
		$deletion->setContainerObjId($obj_id);
		$deletion->setContainerType('crs');
		$deletion->setTitle('[' . $object->getLogin() . '] ' . $object->getFirstname() . ' ' . $object->getLastname());
		$deletion->setDescription(self::plugin()->translate("user_removed_from_course"));

		return $deletion;
	}


	/**
	 * Return a title for this component
	 *
	 * @return string
	 */
	public function getTitle() {
		return self::plugin()->translate("course_participant");
	}


	/**
	 * Return a unique ID among all components
	 *
	 * @return string
	 */
	public function getId() {
		return self::COMPONENT_ID;
	}


	public function getModification(array $parameters) {
		return NULL;
	}
}
