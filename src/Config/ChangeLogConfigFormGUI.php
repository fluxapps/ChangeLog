<?php

namespace srag\Plugins\ChangeLog\Config;

use ilChangeLogPlugin;
use ilTextInputGUI;
use srag\ActiveRecordConfig\ActiveRecordConfigFormGUI;

/**
 * Class ChangeLogConfigFormGUI
 *
 * @package srag\Plugins\ChangeLog\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ChangeLogConfigFormGUI extends ActiveRecordConfigFormGUI {

	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


	/**
	 * @inheritdoc
	 */
	protected function setForm() {
		parent::setForm();

		$conf_roles = new ilTextInputGUI(self::plugin()->translate("conf_roles"), ChangeLogConfig::KEY_ROLES);
		$conf_roles->setInfo(self::plugin()->translate("conf_roles_info"));
		$this->addItem($conf_roles);
	}


	/**
	 * @inheritdoc
	 */
	public function updateConfig() {
		$conf_roles = $this->getInput(ChangeLogConfig::KEY_ROLES);
		ChangeLogConfig::setValueByKey(ChangeLogConfig::KEY_ROLES, $conf_roles);
	}
}
