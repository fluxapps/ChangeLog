<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\LogEntry\Modification;

use ActiveRecord;
use ilChangeLogPlugin;
use srag\DIC\ChangeLog\DICTrait;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

/**
 * Class ChangeLogModification
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Modification
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogModification extends ActiveRecord
{

    use DICTrait;
    use ChangeLogTrait;
    const TABLE_NAME = "chlog_modification";
    const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


    /**
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::TABLE_NAME;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public static function returnDbTableName()
    {
        return self::TABLE_NAME;
    }


    /**
     * @var int
     *
     * @db_has_field    true
     * @db_fieldtype    integer
     * @db_length       8
     * @db_is_primary   true
     * @db_sequence     true
     */
    protected $id;
    /**
     * @var int
     *
     * @db_has_field    true
     * @db_fieldtype    integer
     * @db_length       8
     */
    protected $entry_id;
    /**
     * @var string
     *
     * @db_has_field    true
     * @db_fieldtype    text
     * @db_length       255
     */
    protected $attribute;
    /**
     * @var string
     *
     * @db_has_field    true
     * @db_fieldtype    text
     * @db_length       60
     */
    protected $lng_prefix;
    /**
     * @var string
     *
     * @db_has_field    true
     * @db_fieldtype    clob
     */
    protected $old_value;
    /**
     * @var string
     *
     * @db_has_field    true
     * @db_fieldtype    clob
     */
    protected $new_value;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->entry_id;
    }


    /**
     * @param int $entry_id
     */
    public function setEntryId($entry_id)
    {
        $this->entry_id = $entry_id;
    }


    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }


    /**
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }


    /**
     * @return string
     */
    public function getOldValue()
    {
        return $this->old_value;
    }


    /**
     * @param string $old_value
     */
    public function setOldValue($old_value)
    {
        $this->old_value = $old_value;
    }


    /**
     * @return string
     */
    public function getNewValue()
    {
        return $this->new_value;
    }


    /**
     * @param string $new_value
     */
    public function setNewValue($new_value)
    {
        $this->new_value = $new_value;
    }


    /**
     * @return string
     */
    public function getLngPrefix()
    {
        return $this->lng_prefix;
    }


    /**
     * @param string $lng_prefix
     */
    public function setLngPrefix($lng_prefix)
    {
        $this->lng_prefix = $lng_prefix;
    }


    /**
     * @return string
     */
    public function getLangVar()
    {
        $delimiter = (substr($this->getLngPrefix(), -1) == '_' || !$this->getLngPrefix()) ? '' : '_';

        return $this->getLngPrefix() . $delimiter . $this->getAttribute();
    }
}
