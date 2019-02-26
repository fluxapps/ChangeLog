<?php

namespace srag\CustomInputGUIs\ChangeLog\LearningProgressPie;

/**
 * Class CountLearningProgressPie
 *
 * @package srag\CustomInputGUIs\ChangeLog\LearningProgressPie
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CountLearningProgressPie extends AbstractLearningProgressPie {

	/**
	 * @var int[]
	 */
	protected $count = [];


	/**
	 * @param array $count
	 *
	 * @return self
	 */
	public function withCount(array $count): self {
		$this->count = $count;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	protected function parseData(): array {
		if (count($this->count) > 0) {
			return $this->count;
		} else {
			return [];
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function getCount(): int {
		return array_reduce($this->count, function (int $sum, int $count): int {
			return ($sum + $count);
		}, 0);
	}
}
