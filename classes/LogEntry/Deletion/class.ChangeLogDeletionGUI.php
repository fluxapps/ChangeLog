<?php

require_once __DIR__ . "/../../../vendor/autoload.php";

use srag\Plugins\ChangeLog\LogEntry\ChangeLogChangeLogGUI;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionTableGUI;

/**
 * Class ChangeLogDeletionGUI
 *
 * @author            Stefan Wanzenried <sw@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ChangeLogDeletionGUI: ilUIPluginRouterGUI
 */
class ChangeLogDeletionGUI extends ChangeLogChangeLogGUI {

	function getTableGUI($cmd) {
		return new ChangeLogDeletionTableGUI($this, $cmd);
	}


	protected function getAdditionalData() {
		/** @var ChangeLogDeletionEntry $deletion */
		$deletion = ChangeLogDeletionEntry::findOrFail((int)$_GET['deletion_id']);
		$table = $this->renderTable($deletion->getAdditionalData());
		self::dic()->template()->setContent($table);
		if (self::dic()->ctrl()->isAsynch()) {
			echo $table;
			exit();
		}
	}


	protected function index() {
		self::dic()->template()->setTitle(self::plugin()->translate('deletion_log'));
		parent::index();
	}


	/**
	 * @param array $data
	 *
	 * @return string
	 */
	protected function renderTable(array $data) {
		$out = '<table class="chlog-inline-table">';
		foreach ($data as $label => $value) {
			if (is_array($value)) {
				$value = $this->renderTable($value);
			}
			$out .= "<tr>";
			if (!is_numeric($label)) {
				$out .= "<td class='td-label'>{$label}</td>";
			}
			$out .= "<td>{$value}</td></tr>";
		}
		$out .= '</table>';

		return $out;
	}
}
