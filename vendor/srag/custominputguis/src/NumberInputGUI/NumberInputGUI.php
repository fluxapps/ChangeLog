<?php

namespace srag\CustomInputGUIs\ChangeLog\NumberInputGUI;

use ilNumberInputGUI;
use ilTableFilterItem;
use srag\DIC\ChangeLog\DICTrait;

/**
 * Class NumberInputGUI
 *
 * @package srag\CustomInputGUIs\ChangeLog\NumberInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NumberInputGUI extends ilNumberInputGUI implements ilTableFilterItem {

	use DICTrait;


	/**
	 * @inheritdoc
	 */
	public function getTableFilterHTML()/*: string*/ {
		return $this->render();
	}
}
