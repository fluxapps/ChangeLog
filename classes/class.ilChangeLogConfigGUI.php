<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once __DIR__ . "/../vendor/autoload.php";

use srag\ActiveRecordConfig\ActiveRecordConfigGUI;
use srag\Plugins\ChangeLog\Config\ChangeLogConfigFormGUI;

/**
 * Class ilChangeLogConfigGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilChangeLogConfigGUI extends ActiveRecordConfigGUI {

	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	/**
	 * @var array
	 */
	protected static $tabs = [ "configuration" => ChangeLogConfigFormGUI::class ];
}
