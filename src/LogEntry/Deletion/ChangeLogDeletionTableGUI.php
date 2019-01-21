<?php

namespace srag\Plugins\ChangeLog\LogEntry\Deletion;

use ChangeLogDeletionGUI;
use ilCombinationInputGUI;
use ilDateTimeInputGUI;
use ilLink;
use ilModalGUI;
use ilObject;
use ilObjUser;
use ilPlugin;
use ilSelectInputGUI;
use ilTextInputGUI;
use ilUIPluginRouterGUI;
use srag\Plugins\ChangeLog\ChangeLog\ChangeLogChangeLog;
use srag\Plugins\ChangeLog\LogEntry\ChangeLogChangeLogTableGUI;

/**
 * Class ChangeLogDeletionTableGUI
 *
 * @package srag\Plugins\ChangeLog\LogEntry\Deletion
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ChangeLogDeletionTableGUI extends ChangeLogChangeLogTableGUI {

	/**
	 * @var array
	 */
	protected $columns = array(
		'component_id' => array( 'visible' => true, 'sortable' => true ),
		'deletion_mode' => array( 'visible' => true, 'sortable' => true ),
		'title' => array( 'visible' => true, 'sortable' => true ),
		'description' => array( 'visible' => true, 'sortable' => true ),
		'type' => array( 'visible' => true, 'sortable' => true ),
		'obj_id' => array( 'visible' => false, 'sortable' => true ),
		'ref_id' => array( 'visible' => false, 'sortable' => true ),
		'created_at' => array( 'visible' => true, 'sortable' => true ),
		'created_user_id' => array( 'visible' => true, 'sortable' => true ),
		'obj_created_at' => array( 'visible' => true, 'sortable' => true ),
		'owner' => array( 'visible' => false, 'sortable' => true ),
		'context' => array( 'visible' => true, 'sortable' => false ),
		'additional_data' => array( 'visible' => true, 'sortable' => true ),
	);


	/**
	 * @param        $a_parent_obj
	 * @param string $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd = "") {
		$this->setId("del");
		parent::__construct($a_parent_obj, $a_parent_cmd);
	}


	/**
	 * Init filter items
	 *
	 */
	public function initFilter() {
		if ($this->isColumnSelected('component_id')) {
			$item = new ilSelectInputGUI(self::plugin()->translate('del_col_component_id'), 'component_id');
			$options = array();
			foreach (ChangeLogChangeLog::getInstance()->getComponents() as $component) {
				$options[$component->getId()] = $component->getTitle();
			}
			$options = array( '' => '' ) + $options;
			$item->setOptions($options);
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('deletion_mode')) {
			$item = new ilSelectInputGUI(self::plugin()->translate('del_col_deletion_mode'), 'deletion_mode');
			$options = array(
				'' => '',
				ChangeLogDeletionEntry::MODE_TRASH => self::plugin()->translate('deletion_mode_' . ChangeLogDeletionEntry::MODE_TRASH),
				ChangeLogDeletionEntry::MODE_DELETE => self::plugin()->translate('deletion_mode_' . ChangeLogDeletionEntry::MODE_DELETE),
			);
			$item->setOptions($options);
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('title')) {
			$item = new ilTextInputGUI(self::plugin()->translate('del_col_title'), 'title');
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('type')) {
			$set = self::dic()->database()->query('SELECT DISTINCT type, component_id FROM ' . ChangeLogDeletionEntry::TABLE_NAME);
			$options = array();
			while ($row = self::dic()->database()->fetchObject($set)) {
				$type = $row->type;
				$label = $this->getLabelOfType($type);
				$options[$row->type] = $label;
			}
			$item = new ilSelectInputGUI(self::plugin()->translate('del_col_type'), 'type');
			$item->setOptions(array( '' => '' ) + $options);
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('obj_id')) {
			$item = new ilTextInputGUI(self::plugin()->translate('del_col_obj_id'), 'obj_id');
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('ref_id')) {
			$item = new ilTextInputGUI(self::plugin()->translate('del_col_ref_id'), 'ref_id');
			$this->addFilterItemWithValue($item);
		}

		if ($this->isColumnSelected('created_at')) {
			$item = new ilCombinationInputGUI(self::plugin()->translate('deleted_between'), 'created_at');
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
			$item = new ilTextInputGUI(self::plugin()->translate('del_col_created_user_id'), 'created_user_id');
			$this->addFilterItemWithValue($item);
		}
	}


	function render() {
		$modal = ilModalGUI::getInstance();
		$modal->setType(ilModalGUI::TYPE_LARGE);
		$modal->setHeading(self::plugin()->translate('del_col_additional_data'));
		$modal->setId('chlog-del-data');
		$modal->setBody('<section></section>');

		return parent::render() . self::output()->getHTML($modal);
	}


	/**
	 * @param string $type
	 *
	 * @return string string
	 */
	protected function getLabelOfType($type) {
		if (!$type) {
			return '';
		}
		$label = self::dic()->language()->txt('obj_' . $type);
		// Is a repository object plugin?
		if ($type[0] == 'x' && $label[0] == '-') {
			$label = ilPlugin::lookupTxt('rep_robj', $type, 'obj_' . $type);
		}

		return $label;
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
			case 'owner':
			case 'created_user_id':
				if ($value) {
					$names = ilObjUser::_lookupName($value);
					$value = '[' . $names['login'] . '] ' . $names['firstname'] . ' ' . $names['lastname'];
				}
				break;
			case 'deletion_mode':
				$value = self::plugin()->translate('deletion_mode_' . $value);
				break;
			case 'context':
				if ($ref_id = $row['container_ref_id']) {
					$link = ilLink::_getStaticLink($ref_id);
					$title = $row['container_obj_id'] ? ilObject::_lookupTitle($row['container_obj_id']) : 'Link';
					$value = "<a href='{$link}'>" . $title . "</a>";
					if ($csv) {
						$value = $link;
					}
				}
				break;
			case 'type':
				$value = $this->getLabelOfType($value);
				break;
			case 'additional_data':
				if (is_array($value) && count($value)) {
					if ($csv) {
						$out = '';
						foreach ($value as $label => $data) {
							$out .= "$label : $data, ";
						}
						$value = rtrim($out, ', ');
					} else {
						self::dic()->ctrl()->setParameterByClass(ChangeLogDeletionGUI::class, 'deletion_id', $row['id']);
						$url = self::dic()->ctrl()->getLinkTargetByClass(array(
							ilUIPluginRouterGUI::class,
							ChangeLogDeletionGUI::class
						), ChangeLogDeletionGUI::CMD_GET_ADDITIONAL_DATA, '', true);
						self::dic()->ctrl()->clearParametersByClass(ChangeLogDeletionGUI::class);
						$value = "<a class='chlog-del-show-data' href='{$url}'>" . self::plugin()->translate('show') . "</a>";
					}
				} else {
					$value = '';
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

		$query = ChangeLogDeletionEntry::getCollection();
		$query->orderBy($this->getOrderField(), $this->getOrderDirection());
		$query->limit($this->getOffset(), $this->getLimit());
		$count = ChangeLogDeletionEntry::getCollection();
		$wheres = array();
		$equals = array( 'component_id', 'deletion_mode', 'type', 'ref_id', 'obj_id' );
		foreach ($equals as $equal) {
			if (isset($this->filter[$equal]) && $this->filter[$equal]) {
				$wheres[$equal] = $this->filter[$equal];
			}
		}
		if (isset($this->filter['title']) && $this->filter['title']) {
			$query->where("title LIKE " . self::dic()->database()->quote('%' . $this->filter['title'] . '%'));
			$count->where("title LIKE " . self::dic()->database()->quote('%' . $this->filter['title'] . '%'));
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
