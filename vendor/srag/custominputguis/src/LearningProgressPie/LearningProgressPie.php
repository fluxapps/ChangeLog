<?php

namespace srag\CustomInputGUIs\ChangeLog\LearningProgressPie;

use srag\DIC\ChangeLog\DICTrait;

/**
 * Class LearningProgressPie
 *
 * @package srag\CustomInputGUIs\ChangeLog
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class LearningProgressPie {

	use DICTrait;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @return CountLearningProgressPie
	 */
	public function count(): CountLearningProgressPie {
		return new CountLearningProgressPie();
	}


	/**
	 * @return ObjIdsLearningProgressPie
	 */
	public function objIds(): ObjIdsLearningProgressPie {
		return new ObjIdsLearningProgressPie();
	}
}
