<?php

namespace srag\Plugins\ChangeLog\Access;

use ilChangeLogPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class Permission
 *
 * @package srag\Plugins\ChangeLog\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Permission {

	use DICTrait;
	use ChangeLogTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Permission constructor
	 */
	private function __construct() {

	}
}
