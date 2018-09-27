<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\Component;

use ilAdministrationGUI;
use ilObjOrgUnit;
use ilObjOrgUnitTree;
use ilObjUser;
use ilObjUserGUI;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationEntry;

/**
 * Class ChangeLogComponentOrgUnit
 *
 * @package srag\Plugins\ChangeLog\Component
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogComponentOrgUnit extends ChangeLogComponent {

	const COMPONENT_ID = 'Modules/OrgUnit';
	protected $user_id;


	public function getModification(array $parameters) {
		$event = $parameters['event'];
		$user = new ilObjUser($parameters['user_id']);
		$this->user_id = $parameters['user_id'];
		$modification = new ChangeLogModificationEntry();
		$modification->setTitle($user->getTitle());
		$modification->setObjId($user->getId());

		$new_org_ref_ids = ilObjOrgUnitTree::_getInstance()->getOrgUnitOfUser($parameters['user_id']);
		$new_org_string = $this->formatValue('objs_orgu', $new_org_ref_ids);

		switch ($event) {
			case 'assignUsersToEmployeeRole':
			case 'assignUsersToSuperiorRole':
				$role = substr($event, 13, 8);
				$old_org_string = str_replace(ilObjOrgUnit::_lookupTitle($parameters['obj_id']) . ' (' . $role . ')<br>', '', $new_org_string);
				break;
			case 'deassignUserFromEmployeeRole':
			case 'deassignUserFromSuperiorRole':
				$role = substr($event, 16, 8);
				$old_org_string = $new_org_string . ilObjOrgUnit::_lookupTitle($parameters['obj_id']) . ' (' . $role . ')<br>';
				break;
		}

		$modification->addModification('objs_orgu', array(
			'old' => rtrim($old_org_string, '<br>'),
			'new' => rtrim($new_org_string, '<br>'),
		));

		return $modification;
	}


	function formatValue($attribute, $value) {
		switch ($attribute) {
			case 'objs_orgu':
				$return = '<br>';
				foreach ($value as $v) {
					if (in_array($this->user_id, ilObjOrgUnitTree::_getInstance()->getSuperiors($v))) {
						$return .= ilObjOrgUnit::_lookupTitle(ilObjOrgUnit::_lookupObjectId($v)) . ' (Superior)' . '<br>';
					}
					if (in_array($this->user_id, ilObjOrgUnitTree::_getInstance()->getEmployees($v))) {
						$return .= ilObjOrgUnit::_lookupTitle(ilObjOrgUnit::_lookupObjectId($v)) . ' (Employee)' . '<br>';
					}
				}

				return $return;
			default:
				return $value;
		}
	}


	public function getId() {
		return self::COMPONENT_ID;
	}


	public function getTitle() {
		return self::plugin()->translate("user_assigment");
	}


	/**
	 * Overwrite this and return array of form array('cmdClass' => ..., 'cmd' =>, 'attribute' => ...)
	 * to enable title as link in the modification table gui
	 *
	 * @return array cmdClass, cmd and attribute for creation of links
	 */
	public function getCtrlParameters() {
		return array(
			'cmdClass' => array( ilAdministrationGUI::class, ilObjUserGUI::class ),
			'cmd' => 'view',
			'attribute' => 'obj_id'
		);
	}
}
