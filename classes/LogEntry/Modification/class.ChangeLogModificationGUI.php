<?php

require_once __DIR__ . "/../../../vendor/autoload.php";

use srag\Plugins\ChangeLog\LogEntry\ChangeLogChangeLogGUI;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationTableGUI;

/**
 * Class ChangeLogModificationGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ChangeLogModificationGUI: ilUIPluginRouterGUI
 */
class ChangeLogModificationGUI extends ChangeLogChangeLogGUI {

	function getTableGUI($cmd) {
		return new ChangeLogModificationTableGUI($this, $cmd);
	}


	protected function index() {
		self::dic()->mainTemplate()->setTitle(self::plugin()->translate('modification_log'));
		parent::index();
	}
}
