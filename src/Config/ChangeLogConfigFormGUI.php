<?php

namespace srag\Plugins\ChangeLog\Config;

use ilChangeLogPlugin;
use ilTextInputGUI;
use srag\ActiveRecordConfig\ChangeLog\ActiveRecordConfigFormGUI;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class ChangeLogConfigFormGUI
 *
 * @package srag\Plugins\ChangeLog\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ChangeLogConfigFormGUI extends ActiveRecordConfigFormGUI
{

    use ChangeLogTrait;
    const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
    const CONFIG_CLASS_NAME = ChangeLogConfig::class;


    /**
     * @inheritdoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            ChangeLogConfig::KEY_ROLES => [
                self::PROPERTY_CLASS => ilTextInputGUI::class
            ]
        ];
    }
}
