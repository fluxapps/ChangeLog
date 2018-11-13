<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\Config;

use ilChangeLogPlugin;
use srag\ActiveRecordConfig\ChangeLog\ActiveRecordConfig;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class ChangeLogConfig
 *
 * @package srag\Plugins\ChangeLog\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ChangeLogConfig extends ActiveRecordConfig {

	use ChangeLogTrait;
	const TABLE_NAME = "chlog_config";
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	const KEY_ROLES = "conf_roles";
	/**
	 * @var array
	 */
	protected static $fields = [

	];


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
}
