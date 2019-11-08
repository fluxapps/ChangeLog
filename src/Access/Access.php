<?php

namespace srag\Plugins\ChangeLog\Access;

use ilChangeLogPlugin;
use srag\DIC\ChangeLog\DICTrait;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class Access
 *
 * @package srag\Plugins\ChangeLog\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Access
{

    use DICTrait;
    use ChangeLogTrait;
    const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/*: self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Access constructor
     */
    private function __construct()
    {

    }
}
