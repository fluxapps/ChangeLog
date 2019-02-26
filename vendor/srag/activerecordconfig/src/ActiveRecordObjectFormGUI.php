<?php

namespace srag\ActiveRecordConfig\ChangeLog;

use ActiveRecord;
use ilObject;
use srag\CustomInputGUIs\ChangeLog\PropertyFormGUI\ObjectPropertyFormGUI;

/**
 * Class ActiveRecordObjectFormGUI
 *
 * @package srag\ActiveRecordConfig\ChangeLog
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordObjectFormGUI extends ObjectPropertyFormGUI {

	/**
	 * @var string
	 */
	const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
	/**
	 * @var string
	 */
	protected $tab_id;


	/**
	 * ActiveRecordObjectFormGUI constructor
	 *
	 * @param object                            $parent
	 * @param ilObject|ActiveRecord|object|null $object
	 * @param bool                              $object_auto_store
	 */
	public function __construct($parent, $tab_id, $object = NULL,/*bool*/
		$object_auto_store = true) {
		$this->tab_id = $tab_id;

		parent::__construct($parent, $object, $object_auto_store);
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
}
