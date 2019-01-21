<?php

namespace srag\CustomInputGUIs\ChangeLog\TableGUI\Exception;

use ilException;

/**
 * Class TableGUIException
 *
 * @package srag\CustomInputGUIs\ChangeLog\TableGUI\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class TableGUIException extends ilException {

	/**
	 * TableGUIException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @internal
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
