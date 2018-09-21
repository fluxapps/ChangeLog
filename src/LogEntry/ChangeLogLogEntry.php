<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\LogEntry;

use ActiveRecord;
use ilChangeLogPlugin;
use srag\DIC\DICTrait;

/**
 * Class ChangeLogLogEntry
 *
 * abstract class for ChangeLogDeletionEntry and ChangeLogModificationEntry
 *
 * @package srag\Plugins\ChangeLog\LogEntry
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
abstract class ChangeLogLogEntry extends ActiveRecord {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TABLE_NAME = "";
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return static::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 * @db_is_primary   true
	 * @db_sequence     true
	 */
	protected $id = 0;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       128
	 * @db_index        true
	 */
	protected $component_id = '';
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1204
	 */
	protected $title;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    timestamp
	 */
	protected $created_at;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 * @db_index        true
	 */
	protected $created_user_id;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $obj_id;


	public function create() {
		$this->created_at = date('Y-m-d H:i:s');
		$this->updated_at = date('Y-m-d H:i:s');
		$this->created_user_id = self::dic()->user()->getId();
		$this->updated_user_id = self::dic()->user()->getId();
		parent::create();
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->created_at;
	}


	/**
	 * @param string $created_at
	 */
	public function setCreatedAt($created_at) {
		$this->created_at = $created_at;
	}


	/**
	 * @return int
	 */
	public function getCreatedUserId() {
		return $this->created_user_id;
	}


	/**
	 * @param int $created_user_id
	 */
	public function setCreatedUserId($created_user_id) {
		$this->created_user_id = $created_user_id;
	}


	/**
	 * @return string
	 */
	public function getComponentId() {
		return $this->component_id;
	}


	/**
	 * @param string $component_id
	 */
	public function setComponentId($component_id) {
		$this->component_id = $component_id;
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
}
