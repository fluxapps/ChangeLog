<?php

namespace srag\Plugins\ChangeLog\LogEntry;

use ChangeLogModificationGUI;
use ilChangeLogPlugin;
use ilExcel;
use ilObjUser;
use ilTable2GUI;
use srag\DIC\DICTrait;
use srag\Plugins\ChangeLog\ChangeLog\ChangeLogChangeLog;

/**
 * Class ChangeLogChangeLogTableGUI
 *
 * @package srag\Plugins\ChangeLog\LogEntry
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogChangeLogTableGUI extends ilTable2GUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;
	/**
	 * @var array
	 */
	protected $filter = array();
	/**
	 * @var array
	 */
	protected $columns;


	/**
	 * @param        $a_parent_obj
	 * @param string $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd = "") {
		$this->setPrefix('ChangeLog_');
		parent::__construct($a_parent_obj, $a_parent_cmd, '');
		$this->setRowTemplate('tpl.row_generic.html', self::plugin()->directory());
		$this->setFormAction(self::dic()->ctrl()->getFormAction($a_parent_obj));
		$this->addColumns();
		$this->setExportFormats(array( self::EXPORT_CSV, self::EXPORT_EXCEL ));
		$this->initFilter();
		$this->setDisableFilterHiding(true);
		if (!in_array($a_parent_cmd, array( ChangeLogModificationGUI::CMD_APPLY_FILTER, ChangeLogModificationGUI::CMD_RESET_FILTER ))) {
			$this->buildData();
		}
	}


	/**
	 * @return array
	 */
	public function getSelectableColumns() {
		$columns = array();
		foreach ($this->columns as $column => $options) {
			$columns[$column] = array( 'txt' => self::plugin()->translate($this->getId() . '_col_' . $column), 'default' => $options['visible'] );
		}

		return $columns;
	}


	/**
	 * Add a filter item
	 *
	 * @param $item
	 */
	protected function addFilterItemWithValue($item) {
		$this->addFilterItem($item);
		$item->readFromSession();
		switch (get_class($item)) {
			case 'ilSelectInputGUI':
				$value = $item->getValue();
				break;
			case 'ilCheckboxInputGUI':
				$value = $item->getChecked();
				break;
			case 'ilDateTimeInputGUI':
				$value = $item->getDate();
				break;
			default:
				$value = $item->getValue();
				break;
		}
		if ($value) {
			$this->filter[$item->getPostVar()] = $value;
		}
	}


	/**
	 * @param array $a_set
	 */
	protected function fillRow($a_set) {
		foreach ($this->columns as $col => $options) {
			if ($this->isColumnSelected($col)) {
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $this->getFormattedValue($a_set[$col], $col, $a_set));
				$this->tpl->parseCurrentBlock();
			}
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
		}

		return $value = ($value) ? $value : "&nbsp;";
	}


	protected function fillRowCSV($a_csv, $a_set) {
		foreach ($this->columns as $col => $options) {
			if ($this->isColumnSelected($col)) {
				$value = $this->getFormattedValue($a_set[$col], $col, $a_set, true);
				$a_csv->addColumn($this->sanitizeValueForExport($value));
			}
		}
		$a_csv->addRow();
	}


	protected function sanitizeValueForExport($value) {
		$value = trim(filter_var($value, FILTER_SANITIZE_STRING));

		return str_replace('&nbsp;', '', $value);
	}


	protected function fillHeaderExcel(ilExcel $a_excel, &$a_row) {
		$col = 0;
		foreach ($this->columns as $name => $options) {
			if ($this->isColumnSelected($name)) {
				$title = strip_tags(self::plugin()->translate($this->getId() . '_col_' . $name));
				$a_excel->setCell($a_row, $col, $title);
				$col ++;
			}
		}
		$a_row ++;
	}


	protected function fillRowExcel(ilExcel $a_excel, &$a_row, $a_set) {
		$col = 0;
		foreach ($this->columns as $column => $options) {
			if ($this->isColumnSelected($column)) {
				$value = $this->getFormattedValue($a_set[$column], $column, $a_set, true);
				$a_excel->setCell($a_row, $col, $this->sanitizeValueForExport($value));
				$col ++;
			}
		}
	}


	protected function addColumns() {
		foreach ($this->columns as $col => $options) {
			if ($this->isColumnSelected($col)) {
				$sort_field = ($options['sortable']) ? $col : '';
				$width = (isset($options['width'])) ? $options['width'] : '';
				$this->addColumn(self::plugin()->translate($this->getId() . '_col_' . $col), $sort_field, $width);
			}
		}
	}

	//
	//	protected function buildData()
	//	{
	//		$this->setExternalSorting(true);
	//		$this->setExternalSegmentation(true);
	//		$this->setDefaultOrderField('created_at');
	//		$this->setDefaultOrderDirection('DESC');
	//		$this->determineLimit();
	//		$this->determineOffsetAndOrder();
	//
	//		$query = ChangeLogModification::getCollection();
	//		$query->orderBy($this->getOrderField(), $this->getOrderDirection());
	//		$query->limit($this->getOffset(), $this->getLimit());
	//		$count = ChangeLogModification::getCollection();
	//		$wheres = array();
	//		$equals = array('component_id', 'deletion_mode', 'type', 'ref_id', 'obj_id');
	//		foreach ($equals as $equal) {
	//			if (isset($this->filter[$equal]) && $this->filter[$equal]) {
	//				$wheres[$equal] = $this->filter[$equal];
	//			}
	//		}
	//		if (isset($this->filter['title']) && $this->filter['title']) {
	//			$query->where("title LIKE " . self::dic()->database()->quote('%' . $this->filter['title'] . '%'));
	//			$count->where("title LIKE " . self::dic()->database()->quote('%' . $this->filter['title'] . '%'));
	//		}
	//		if (isset($this->filter['created_at']) && $this->filter['created_at']) {
	//			if (isset($this->filter['created_at']['from'])) {
	//				/** @var ilDateTime $from */
	//				$from = $this->filter['created_at']['from'];
	//				$query->where("created_at >= '" . $from->get(IL_CAL_DATETIME) . "'");
	//				$count->where("created_at >= '" . $from->get(IL_CAL_DATETIME) . "'");
	//			}
	//			if (isset($this->filter['created_at']['to'])) {
	//				/** @var ilDateTime $to */
	//				$to = $this->filter['created_at']['to'];
	//				$query->where("created_at <= '" . date('Y-m-d', strtotime('+1 day', $to->get(IL_CAL_UNIX))) . "'");
	//				$count->where("created_at <= '" . date('Y-m-d', strtotime('+1 day', $to->get(IL_CAL_UNIX))) . "'");
	//			}
	//		}
	//		if (isset($this->filter['created_user_id']) && $this->filter['created_user_id']) {
	//			$query->innerjoin('usr_data', 'created_user_id', 'usr_id');
	//			$count->innerjoin('usr_data', 'created_user_id', 'usr_id');
	//			$data = self::dic()->database()->quote('%' . $this->filter['created_user_id'] . '%', 'text');
	//			$query->where("usr_data.login LIKE {$data} OR usr_data.lastname LIKE {$data}");
	//			$count->where("usr_data.login LIKE {$data} OR usr_data.lastname LIKE {$data}");
	//			if (strpos($data, '@') !== false) {
	//				$query->where("TRUE OR usr_data.email LIKE {$data}");
	//				$count->where("TRUE OR usr_data.email LIKE {$data}");
	//			}
	//		}
	//		$query->where($wheres);
	//		$count->where($wheres);
	//		$this->setData($query->getArray());
	//		$this->setMaxCount($count->count());
	//	}

}
