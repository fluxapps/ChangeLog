<?php

namespace srag\Plugins\ChangeLog\LogEntry\Deletion;

use srag\ActiveRecordConfig\ChangeLog\ActiveRecordConfig;
use srag\Plugins\ChangeLog\LogEntry\ChangeLogLogEntry;

/**
 * Class ChangeLogDeletionEntry
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Deletion
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogDeletionEntry extends ChangeLogLogEntry {

	const TABLE_NAME = "chlog_log_entry_del";
	const MODE_TRASH = 'trash';
	const MODE_DELETE = 'delete';
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       6
	 * @db_index        truecertadministratecertificategui
	 */
	protected $deletion_mode;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    timestamp
	 */
	protected $updated_at;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $updated_user_id;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $ref_id;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       4000
	 */
	protected $description;
	/**
	 * Owner of object
	 *
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $owner;
	/**
	 * Created timestamp from object (in table obj_data)
	 *
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    timestamp
	 */
	protected $obj_created_at;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       16
	 * @db_index        true
	 */
	protected $type;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $container_ref_id;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $container_obj_id;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       16
	 * @db_index        true
	 */
	protected $container_type;
	/**
	 * @var array
	 *
	 * @db_has_field    true
	 * @db_fieldtype    clob
	 */
	protected $additional_data = array();


	public function update() {
		$this->updated_at = date(ActiveRecordConfig::SQL_DATE_FORMAT);
		$this->updated_user_id = self::dic()->user()->getId();
		parent::update();
	}


	/**
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function additionalData($key, $value) {
		$this->additional_data[$key] = $value;

		return $this;
	}


	public function sleep($field_name) {
		switch ($field_name) {
			case 'additional_data':
				return json_encode($this->additional_data);
		}

		return parent::sleep($field_name);
	}


	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case 'additional_data':
				return json_decode($field_value, true);
		}

		return parent::wakeUp($field_name, $field_value);
	}


	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}


	/**
	 * @param string $updated_at
	 */
	public function setUpdatedAt($updated_at) {
		$this->updated_at = $updated_at;
	}


	/**
	 * @return int
	 */
	public function getUpdatedUserId() {
		return $this->updated_user_id;
	}


	/**
	 * @param int $updated_user_id
	 */
	public function setUpdatedUserId($updated_user_id) {
		$this->updated_user_id = $updated_user_id;
	}


	/**
	 * @return int
	 */
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return int
	 */
	public function getContainerRefId() {
		return $this->container_ref_id;
	}


	/**
	 * @param int $container_ref_id
	 */
	public function setContainerRefId($container_ref_id) {
		$this->container_ref_id = $container_ref_id;
	}


	/**
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->additional_data;
	}


	/**
	 * @param array $additional_data
	 */
	public function setAdditionalData($additional_data) {
		$this->additional_data = $additional_data;
	}


	/**
	 * @return int
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param int $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return string
	 */
	public function getContainerType() {
		return $this->container_type;
	}


	/**
	 * @param string $container_type
	 */
	public function setContainerType($container_type) {
		$this->container_type = $container_type;
	}


	/**
	 * @return string
	 */
	public function getDeletionMode() {
		return $this->deletion_mode;
	}


	/**
	 * @param string $deletion_mode
	 */
	public function setDeletionMode($deletion_mode) {
		$this->deletion_mode = $deletion_mode;
	}


	/**
	 * @return int
	 */
	public function getContainerObjId() {
		return $this->container_obj_id;
	}


	/**
	 * @param int $container_obj_id
	 */
	public function setContainerObjId($container_obj_id) {
		$this->container_obj_id = $container_obj_id;
	}


	/**
	 * @return string
	 */
	public function getObjCreatedAt() {
		return $this->obj_created_at;
	}


	/**
	 * @param string $obj_created_at
	 */
	public function setObjCreatedAt($obj_created_at) {
		$this->obj_created_at = $obj_created_at;
	}
}
