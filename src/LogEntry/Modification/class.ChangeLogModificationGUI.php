<?php

namespace srag\Plugins\ChangeLog\LogEntry\Modification;

// ilCtrlMainMenu Bug
require_once __DIR__ . "/../../../vendor/autoload.php";

use srag\Plugins\ChangeLog\LogEntry\ChangeLogChangeLogGUI;

/**
 * Class ChangeLogModificationGUI
 *
 * @package           srag\Plugins\ChangeLog\LogEntry\Modification
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationGUI: ilUIPluginRouterGUI
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
