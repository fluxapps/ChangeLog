<?php

namespace srag\Plugins\ChangeLog\Utils;

use srag\Plugins\ChangeLog\Access\Access;
use srag\Plugins\ChangeLog\Access\Permission;

/**
 * Trait ChangeLogTrait
 *
 * @package srag\Plugins\ChangeLog\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait ChangeLogTrait {

	/**
	 * @return Access
	 */
	protected static function access()/*: Access*/ {
		return Access::getInstance();
	}


	/**
	 * @return Permission
	 */
	protected static function permission()/*: Permission*/ {
		return Permission::getInstance();
	}
}
