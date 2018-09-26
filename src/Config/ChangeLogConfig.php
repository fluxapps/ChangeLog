<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\Config;

use ChangeLogRemoveDataConfirm;
use ilChangeLogPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfig;

/**
 * Class ChangeLogConfig
 *
 * @package srag\Plugins\ChangeLog\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ChangeLogConfig extends ActiveRecordConfig {

	const TABLE_NAME = "chlog_config";
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	const KEY_ROLES = "conf_roles";


	/**
	 * @param string      $key
	 * @param string|null $default_value
	 *
	 * @return string
	 */
	public static function getValueByKey($key, $default_value = NULL) {
		return self::getStringValue($key, $default_value);
	}


	/**
	 * @param string $name
	 * @param string $value
	 */
	public static function setValueByKey($name, $value) {
		self::setStringValue($name, $value);
	}


	/**
	 * @return bool|null
	 */
	public static function getUninstallRemovesData()/*: ?bool*/ {
		return self::getXValue(ChangeLogRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, ChangeLogRemoveDataConfirm::DEFAULT_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @param bool $uninstall_removes_data
	 */
	public static function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		self::setBooleanValue(ChangeLogRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}


	/**
	 *
	 */
	public static function removeUninstallRemovesData()/*: void*/ {
		self::removeName(ChangeLogRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA);
	}
}
