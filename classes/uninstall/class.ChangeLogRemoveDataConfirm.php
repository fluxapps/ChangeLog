<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\ChangeLog\Config\ChangeLogConfig;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class ChangeLogRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ChangeLogRemoveDataConfirm: ilUIPluginRouterGUI
 */
class ChangeLogRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData() {
		return ChangeLogConfig::getUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData($uninstall_removes_data) {
		ChangeLogConfig::setUninstallRemovesData($uninstall_removes_data);
	}


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData() {
		ChangeLogConfig::removeUninstallRemovesData();
	}
}
