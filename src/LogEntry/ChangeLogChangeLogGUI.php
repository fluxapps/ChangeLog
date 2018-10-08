<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\LogEntry;

use ilChangeLogPlugin;
use ilPersonalDesktopGUI;
use ilUtil;
use srag\DIC\DICTrait;
use srag\Plugins\ChangeLog\Config\ChangeLogConfig;

/**
 * Class ChangeLogChangeLogGUI
 *
 * @package srag\Plugins\ChangeLog\LogEntry
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
abstract class ChangeLogChangeLogGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	const CMD_INDEX = "index";
	const CMD_GET_ADDITIONAL_DATA = "getAdditionalData";
	const CMD_APPLY_FILTER = "applyFilter";
	const CMD_RESET_FILTER = "resetFilter";


	public function __construct() {
		self::dic()->mainTemplate()->getStandardTemplate();
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/css/ChangeLog.css');
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/js/ChangeLog.js');
	}


	public function executeCommand() {
		self::dic()->mainTemplate()->getStandardTemplate();

		$cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);
		$this->checkPermission();
		switch ($cmd) {
			case self::CMD_INDEX:
			case self::CMD_GET_ADDITIONAL_DATA:
			case self::CMD_APPLY_FILTER:
			case self::CMD_RESET_FILTER:
				$this->$cmd();
				break;
		}

		self::dic()->mainTemplate()->show();
	}


	protected function checkPermission() {
		$roles = explode(',', ChangeLogConfig::getValueByKey(ChangeLogConfig::KEY_ROLES));
		$roles[] = 2;
		if (!self::dic()->rbacreview()->isAssignedToAtLeastOneGivenRole(self::dic()->user()->getId(), $roles)) {
			ilUtil::sendFailure(self::dic()->language()->txt('permission_denied'), true);
			self::dic()->ctrl()->redirectByClass(ilPersonalDesktopGUI::class);
		}
	}


	protected function index() {
		$table = $this->getTableGUI(self::CMD_INDEX);
		self::dic()->mainTemplate()->setContent($table->getHTML());
	}


	protected function applyFilter() {
		$table = $this->getTableGUI(self::CMD_APPLY_FILTER);
		$table->writeFilterToSession();
		$table->resetOffset();
		$this->index();
	}


	protected function resetFilter() {
		$table = $this->getTableGUI(self::CMD_RESET_FILTER);
		$table->resetOffset();
		$table->resetFilter();
		$this->index();
	}


	/**
	 * return correct tableGUI with $cmd as parent cmd
	 *
	 * @param $cmd
	 *
	 * @return mixed
	 */
	abstract function getTableGUI($cmd);
}
