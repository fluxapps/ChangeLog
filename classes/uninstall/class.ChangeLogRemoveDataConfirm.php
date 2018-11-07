<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class ChangeLogRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ChangeLogRemoveDataConfirm: ilUIPluginRouterGUI
 */
class ChangeLogRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use ChangeLogTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
}
