<?php

namespace srag\Plugins\ChangeLog\Component;

use ilObject;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;

/**
 * Class ChangeLogComponentObject
 *
 * @package srag\Plugins\ChangeLog\Component
 *
 * This is the component handling global ILIAS objects
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogComponentObject extends ChangeLogComponent {

	const COMPONENT_ID = 'services_object';


	/**
	 * Return the ChangeLogDeletionEntry object for this component
	 *
	 * @param array $parameters
	 *
	 * @return ChangeLogDeletionEntry
	 */
	public function getDeletion(array $parameters) {
		/** @var ilObject $object */
		$object = $parameters['object'];
		$deletion = new ChangeLogDeletionEntry();
		$deletion->setType($object->getType());
		$deletion->setObjId($object->getId());
		$deletion->setRefId($object->getRefId());
		// In case we don't have the ref id...
		if (!$object->getRefId()) {
			$references = ilObject::_getAllReferences($object->getId());
			if (count($references)) {
				$deletion->setRefId(array_pop($references));
			}
		}
		$deletion->setTitle($object->getTitle());
		$deletion->setDescription($object->getDescription());
		$deletion->setOwner($object->getOwner());
		$deletion->setObjCreatedAt($object->getCreateDate());
		if ($ref_id = $deletion->getRefId()) {
			// Find the parent container (category or course)
			do {
				$sql = "SELECT tree.parent, data.type, data.obj_id FROM tree
                        INNER JOIN object_reference AS ref ON (ref.ref_id = tree.parent)
                        INNER JOIN object_data AS data ON (data.obj_id = ref.obj_id)
                        WHERE tree.child = " . (int)$ref_id;
				$set = self::dic()->database()->query($sql);
				$node_data = self::dic()->database()->fetchObject($set);
				$parent_type = $node_data->type;
				$ref_id = $node_data->parent;
				$obj_id = $node_data->obj_id;
			} while (self::dic()->database()->numRows($set) && ($parent_type != 'crs' && $parent_type != 'cat'));
			$deletion->setContainerRefId($ref_id);
			$deletion->setContainerType($parent_type);
			$deletion->setContainerObjId($obj_id);
		}

		return $deletion;
	}


	/**
	 * Return a unique ID among all components
	 *
	 * @return string
	 */
	public function getId() {
		return self::COMPONENT_ID;
	}


	/**
	 * Return a title for this component
	 *
	 * @return string
	 */
	public function getTitle() {
		return 'ILIAS Object'; // TODO: Translate
	}


	public function getModification(array $parameters) {
		return NULL;
	}
}
