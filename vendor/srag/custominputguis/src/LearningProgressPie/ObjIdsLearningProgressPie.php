<?php

namespace srag\CustomInputGUIs\ChangeLog\LearningProgressPie;

use ilLPObjSettings;
use ilLPStatus;
use ilObjectLP;

/**
 * Class ObjIdsLearningProgressPie
 *
 * @package srag\CustomInputGUIs\ChangeLog\LearningProgressPie
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjIdsLearningProgressPie extends AbstractLearningProgressPie {

	/**
	 * @var int[]
	 */
	protected $obj_ids = [];
	/**
	 * @var int
	 */
	protected $usr_id;


	/**
	 * @param array $obj_ids
	 *
	 * @return self
	 */
	public function withObjIds(array $obj_ids): self {
		$this->obj_ids = $obj_ids;

		return $this;
	}


	/**
	 * @param int $usr_id
	 *
	 * @return self
	 */
	public function withUsrId(int $usr_id): self {
		$this->usr_id = $usr_id;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	protected function parseData(): array {
		if (count($this->obj_ids) > 0) {
			return array_reduce($this->obj_ids, function (array $data, int $obj_id): array {
				$status = $this->getStatus($obj_id);

				if (!isset($data[$status])) {
					$data[$status] = 0;
				}

				$data[$status] ++;

				return $data;
			}, []);
		} else {
			return [];
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function getCount(): int {
		return count($this->obj_ids);
	}


	/**
	 * @param int $obj_id
	 *
	 * @return int
	 */
	private function getStatus(int $obj_id): int {
		// Avoid exit
		if (ilObjectLP::getInstance($obj_id)->getCurrentMode() != ilLPObjSettings::LP_MODE_UNDEFINED) {
			$status = intval(ilLPStatus::_lookupStatus($obj_id, $this->usr_id));
		} else {
			$status = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
		}

		return $status;
	}
}
