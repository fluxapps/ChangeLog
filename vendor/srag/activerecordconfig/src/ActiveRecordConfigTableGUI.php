<?php

namespace srag\ActiveRecordConfig\ChangeLog;

use srag\CustomInputGUIs\ChangeLog\TableGUI\TableGUI;

/**
 * Class ActiveRecordConfigTableGUI
 *
 * @package srag\ActiveRecordConfig\ChangeLog
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigTableGUI extends TableGUI {

	/**
	 * @var string
	 */
	const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
	/**
	 * @var ActiveRecordConfigGUI
	 */
	protected $parent_obj;
	/**
	 * @var string
	 */
	protected $tab_id;


	/**
	 * ActiveRecordConfigTableGUI constructor
	 *
	 * @param ActiveRecordConfigGUI $parent
	 * @param string                $parent_cmd
	 * @param string                $tab_id
	 */
	public function __construct(ActiveRecordConfigGUI $parent, /*string*/
		$parent_cmd, /*string*/
		$tab_id) {
		$this->tab_id = $tab_id;

		parent::__construct($parent, $parent_cmd);
	}


	/**
	 * @inheritdoc
	 */
	public function initFilterFields()/*: void*/ {
		$this->setFilterCommand(ActiveRecordConfigGUI::CMD_APPLY_FILTER . "_" . $this->tab_id);
		$this->setResetCommand(ActiveRecordConfigGUI::CMD_RESET_FILTER . "_" . $this->tab_id);
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
