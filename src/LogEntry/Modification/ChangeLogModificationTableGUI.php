<?php

namespace srag\Plugins\ChangeLog\LogEntry\Modification;

use ilCombinationInputGUI;
use ilDateTimeInputGUI;
use ilObjUser;
use ilTextInputGUI;
use srag\CustomInputGUIs\MultiSelectSearchInputGUI;
use srag\Plugins\ChangeLog\ChangeLog\ChangeLogChangeLog;
use srag\Plugins\ChangeLog\LogEntry\ChangeLogChangeLogTableGUI;

/**
 * Class ChangeLogDeletionTableGUI
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Modification
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogModificationTableGUI extends ChangeLogChangeLogTableGUI {

	/**
	 * @var array
	 */
	protected $columns = array(
		'component_id' => array( 'visible' => true, 'sortable' => true ),
		'title' => array( 'visible' => true, 'sortable' => true ),
		'old_values' => array( 'visible' => true, 'sortable' => false ),
		'new_values' => array( 'visible' => true, 'sortable' => false ),
		'created_at' => array( 'visible' => true, 'sortable' => true ),
		'created_user_id' => array( 'visible' => true, 'sortable' => true ),
	);


	/**
	 * @param        $a_parent_obj
	 * @param string $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd = "") {
		$this->setFormName('mod');
		$this->setId("mod");
		parent::__construct($a_parent_obj, $a_parent_cmd);
	}


	/**
	 * Init filter items
	 *
	 */
	public function initFilter() {
		if ($this->isColumnSelected('component_id')) {
			$item = new MultiSelectSearchInputGUI(self::plugin()->translate('del_col_component_id'), 'component_id');
			$options = array();
			foreach (ChangeLogChangeLog::getInstance()->getComponents() as $component) {
				if (in_array($component->getId(), array( 'services_object', 'course_participant' ))) {
					continue;
				}
				$options[$component->getId()] = $component->getTitle();
			}
			asort($options);
			$item->setOptions($options);
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('title')) {
			$item = new ilTextInputGUI(self::plugin()->translate('del_col_title'), 'title');
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('created_at')) {
			$item = new ilCombinationInputGUI(self::plugin()->translate('modified_between'), 'created_at');
			$from = new ilDateTimeInputGUI("", 'created_at_from');
			//$from->setMode(ilDateTimeInputGUI::MODE_INPUT);
			$item->addCombinationItem("from", $from, '');
			$to = new ilDateTimeInputGUI("", 'created_at_to');
			//$to->setMode(ilDateTimeInputGUI::MODE_INPUT);
			$item->addCombinationItem("to", $to, '');
			$item->setComparisonMode(ilCombinationInputGUI::COMPARISON_ASCENDING);
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('created_user_id')) {
			$item = new ilTextInputGUI(self::plugin()->translate('mod_col_created_user_id'), 'created_user_id');
			$this->addFilterItemWithValue($item);
		}
	}


	/**
	 * Return the formatted value
	 *
	 * @param string $value
	 * @param string $col
	 * @param array  $row
	 * @param bool   $csv
	 *
	 * @return string
	 */
	protected function getFormattedValue($value, $col, array $row, $csv = false) {
		switch ($col) {
			case 'title':
				$ctrl_params = self::plugin()->getPluginObject()->getCtrlParameters($row['component_id']);
				if (!$ctrl_params) {
					break;
				}
				self::dic()->ctrl()
					->setParameterByClass(is_array($ctrl_params['cmdClass']) ? end($ctrl_params['cmdClass']) : $ctrl_params['cmdClass'], $ctrl_params['attribute'], $row['obj_id']);
				$value = '<a href="' . self::dic()->ctrl()->getLinkTargetByClass($ctrl_params['cmdClass'], $ctrl_params['cmd']) . '">' . $value
					. '</a>';
				break;
			case 'component_id':
				static $components = NULL;
				if (!$components) {
					$components = ChangeLogChangeLog::getInstance()->getComponents();
				}
				$value = (isset($components[$value])) ? $components[$value]->getTitle() : '';
				break;
			case 'created_user_id':
				if ($value) {
					$names = ilObjUser::_lookupName($value);
					$value = '[' . $names['login'] . '] ' . $names['firstname'] . ' ' . $names['lastname'];
				}
				break;
			case 'old_values':
				$modification_entry = ChangeLogModificationEntry::where(array( 'id' => $row['id'] ))->first();
				if ($modification_entry->getType() == ChangeLogModificationEntry::TYPE_CREATION) {
					$value = self::plugin()->translate('newly_created');
					break;
				}

				$modifications = $modification_entry->getModifications();
				$value = '';
				foreach ($modifications as $mod) {

					switch ($mod->getAttribute()) {
						case "company_orgu_ref_ids":

							break;
						case "product_ids":

							break;
						default:
							$value .= self::dic()->language()->txt($mod->getLangVar()) . ': ' . $mod->getOldValue() . '<br>';
							break;
					}
					// print_r($mod); exit;

				}
				break;
			case 'new_values':
				$modifications = ChangeLogModification::where(array( 'entry_id' => $row['id'] ))->orderBy('id')->get();
				$value = '';
				foreach ($modifications as $mod) {

					switch ($mod->getAttribute()) {
						case "company_orgu_ref_ids":

							break;
						case "product_ids":

							break;
						default:
							$value .= self::dic()->language()->txt($mod->getLangVar()) . ': ' . $mod->getNewValue() . '<br>';
							break;
					}
				}
				break;
		}

		return $value = ($value) ? $value : "&nbsp;";
	}


	protected function buildData() {
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setDefaultOrderField('created_at');
		$this->setDefaultOrderDirection('DESC');
		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$query = ChangeLogModificationEntry::getCollection();
		$query->orderBy($this->getOrderField(), $this->getOrderDirection());
		$query->limit($this->getOffset(), $this->getLimit());
		$count = ChangeLogModificationEntry::getCollection();
		$wheres = array();

		if (isset($this->filter['component_id[]']) && $this->filter['component_id[]']) {
			$wheres['component_id'] = $this->filter['component_id[]'];
		}
		if (isset($this->filter['title']) && $this->filter['title']) {
			$query->where(ChangeLogModificationEntry::TABLE_NAME . ".title LIKE " . self::dic()->database()->quote('%' . $this->filter['title']
					. '%'));
			$count->where(ChangeLogModificationEntry::TABLE_NAME . ".title LIKE " . self::dic()->database()->quote('%' . $this->filter['title']
					. '%'));
		}
		if (isset($this->filter['created_at']) && $this->filter['created_at']) {
			if (isset($this->filter['created_at']['from'])) {
				/** @var ilDateTime $from */
				$from = $this->filter['created_at']['from'];
				$query->where("created_at >= '" . $from->get(IL_CAL_DATETIME) . "'");
				$count->where("created_at >= '" . $from->get(IL_CAL_DATETIME) . "'");
			}
			if (isset($this->filter['created_at']['to'])) {
				/** @var ilDateTime $to */
				$to = $this->filter['created_at']['to'];
				$query->where("created_at <= '" . date('Y-m-d', strtotime('+1 day', $to->get(IL_CAL_UNIX))) . "'");
				$count->where("created_at <= '" . date('Y-m-d', strtotime('+1 day', $to->get(IL_CAL_UNIX))) . "'");
			}
		}
		if (isset($this->filter['created_user_id']) && $this->filter['created_user_id']) {
			$query->innerjoin('usr_data', 'created_user_id', 'usr_id');
			$count->innerjoin('usr_data', 'created_user_id', 'usr_id');
			$data = self::dic()->database()->quote('%' . $this->filter['created_user_id'] . '%', 'text');
			$query->where("usr_data.login LIKE {$data} OR usr_data.lastname LIKE {$data}");
			$count->where("usr_data.login LIKE {$data} OR usr_data.lastname LIKE {$data}");
			if (strpos($data, '@') !== false) {
				$query->where("TRUE OR usr_data.email LIKE {$data}");
				$count->where("TRUE OR usr_data.email LIKE {$data}");
			}
		}
		$query->where($wheres);
		$count->where($wheres);
		$this->setData($query->getArray());
		$this->setMaxCount($count->count());
	}
}
