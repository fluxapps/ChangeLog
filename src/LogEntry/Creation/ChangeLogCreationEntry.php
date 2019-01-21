<?php

namespace srag\Plugins\ChangeLog\LogEntry\Creation;

use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModification;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationEntry;

/**
 * Class ChangeLogCreationEntry
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Creation
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogCreationEntry extends ChangeLogModificationEntry {

	/**
	 * @var integer
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       4
	 */
	protected $type = self::TYPE_CREATION;


	public function addModification($key, $changes, $lng_prefix = '') {
		if (is_array($changes)) {
			$value = isset($changes['new']) ? $changes['new'] : NULL;
		} else {
			$value = $changes;
		}

		if ($value) {
			$this->addCreation($key, $value, $lng_prefix);
		}
	}


	public function addCreation($key, $value, $lng_prefix = '') {
		$mod = new ChangeLogModification();
		$mod->setEntryId($this->getId());
		$mod->setAttribute($key);
		$mod->setNewValue($value);
		$mod->setLngPrefix($lng_prefix);
		$this->modifications[] = $mod;
	}
}
