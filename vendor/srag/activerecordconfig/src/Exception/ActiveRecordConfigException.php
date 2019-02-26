<?php

namespace srag\ActiveRecordConfig\ChangeLog\Exception;

use ilException;

/**
 * Class ActiveRecordConfigException
 *
 * @package srag\ActiveRecordConfig\ChangeLog\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class ActiveRecordConfigException extends ilException {

	/**
	 * @var int
	 */
	const CODE_INVALID_FIELD = 1;
	/**
	 * @var int
	 */
	const CODE_UNKOWN_COMMAND = 2;
	/**
	 * @var int
	 */
	const CODE_INVALID_CONFIG_GUI_CLASS = 3;


	/**
	 * ActiveRecordConfigException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @internal
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code) {
		parent::__construct($message, $code);
	}
}
