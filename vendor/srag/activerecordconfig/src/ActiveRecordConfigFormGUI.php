<?php

namespace srag\ActiveRecordConfig\ChangeLog;

use srag\ActiveRecordConfig\ChangeLog\Exception\ActiveRecordConfigException;
use srag\CustomInputGUIs\ChangeLog\PropertyFormGUI\PropertyFormGUI;

/**
 * Class ActiveRecordConfigFormGUI
 *
 * @package srag\ActiveRecordConfig\ChangeLog
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigFormGUI extends PropertyFormGUI {

	/**
	 * @var string
	 *
	 * @abstract
	 */
	const CONFIG_CLASS_NAME = "";
	/**
	 * @var string
	 */
	const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
	/**
	 * @var string
	 */
	protected $tab_id;


	/**
	 * ActiveRecordConfigFormGUI constructor
	 *
	 * @param ActiveRecordConfigGUI $parent
	 * @param string                $tab_id
	 */
	public function __construct(ActiveRecordConfigGUI $parent, /*string*/
		$tab_id) {
		$this->checkConfigClassNameConst();

		$this->tab_id = $tab_id;

		parent::__construct($parent);
	}


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key) {
		return (static::CONFIG_CLASS_NAME)::getField($key);
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		$this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE . "_" . $this->tab_id, $this->txt("save"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle($this->txt($this->tab_id));
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {
		return (static::CONFIG_CLASS_NAME)::setField($key, $value);
	}


	/**
	 * @throws ActiveRecordConfigException Your class needs to implement the CONFIG_CLASS_NAME constant!
	 */
	private final function checkConfigClassNameConst()/*: void*/ {
		if (!defined("static::CONFIG_CLASS_NAME") || empty(static::CONFIG_CLASS_NAME) || !class_exists(static::CONFIG_CLASS_NAME)) {
			throw new ActiveRecordConfigException("Your class needs to implement the CONFIG_CLASS_NAME constant!");
		}
	}
}
