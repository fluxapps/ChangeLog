<?php

namespace srag\Plugins\ChangeLog\LogEntry\Modification;

use srag\Plugins\ChangeLog\LogEntry\ChangeLogLogEntry;

/**
 * Class ChangeLogModificationEntry
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Modification
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogModificationEntry extends ChangeLogLogEntry {

	const TABLE_NAME = "chlog_log_entry_mod";
	const TYPE_MODIFICATION = 1;
	const TYPE_CREATION = 2;
	const TYPE_DELETION = 3;
	/**
	 * @var integer
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       4
	 */
	protected $type = self::TYPE_MODIFICATION;
	/**
	 * @var array
	 */
	protected $modifications = array();


	/**
	 * @return string
	 */
	public function getModifications() {
		return ChangeLogModification::where(array( 'entry_id' => $this->getId() ))->orderBy('id')->get();
	}


	public function hasModifications() {
		return !empty($this->modifications);
	}


	public function addModification($key, $changes, $lng_prefix = '') {
		$mod = new ChangeLogModification();
		$mod->setEntryId($this->getId());
		$mod->setAttribute($key);
		$mod->setOldValue($changes['old']);
		$mod->setNewValue($changes['new']);
		$mod->setLngPrefix($lng_prefix);
		$this->modifications[] = $mod;
	}


	public function save() {
		parent::save();
		foreach ($this->modifications as $mod) {
			$mod->setEntryId($this->getId());
			$mod->save();
		}
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}
}
