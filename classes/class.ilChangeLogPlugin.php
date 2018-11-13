<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\ChangeLog\ChangeLog\ChangeLogChangeLog;
use srag\Plugins\ChangeLog\Component\ChangeLogComponent;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentCourseParticipant;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentObject;
use srag\Plugins\ChangeLog\Component\ChangeLogComponentUser;
use srag\Plugins\ChangeLog\Config\ChangeLogConfig;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModification;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationEntry;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrl;
use srag\Plugins\CtrlMainMenu\EntryTypes\Dropdown\ctrlmmEntryDropdown;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;
use srag\RemovePluginDataConfirm\ChangeLog\PluginUninstallTrait;

/**
 * Class ilChangeLogPlugin
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilChangeLogPlugin extends ilEventHookPlugin {

	use PluginUninstallTrait;
	use ChangeLogTrait;
	const PLUGIN_ID = "chlog";
	const PLUGIN_NAME = "ChangeLog";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = ChangeLogRemoveDataConfirm::class;
	const ADMIN_ROLE_ID = 2;
	/**
	 * @var ChangeLogChangeLog
	 */
	protected $changeLog;
	/**
	 * @var self
	 */
	protected static $instance;


	/**
	 * @return self
	 */
	public static function getInstance() {
		if (static::$instance === NULL) {
			static::$instance = new self();
		}

		return static::$instance;
	}


	/**
	 * Track any object types that need its own ChangeLogComponent handler
	 * key = object type, value = ID of the ChangeLogComponent object
	 *
	 * @var array
	 */
	protected static $tracked_types = array();


	/**
	 * Tell this plugin to track a object of type x by given component
	 *
	 * @param string             $type
	 * @param ChangeLogComponent $component
	 */
	public static function trackObjectByType($type, ChangeLogComponent $component) {
		static::$tracked_types[$type] = $component;
	}


	protected function init() {
		parent::init();
		$this->changeLog = ChangeLogChangeLog::getInstance();
		if (isset($_GET['ulx'])) {
			$this->updateLanguages();
		}
	}


	public function getCtrlParameters($component) {
		return $this->changeLog->getComponentById($component)->getCtrlParameters();
	}


	/**
	 * @param string $component
	 * @param string $event
	 * @param array  $parameters
	 */
	public function handleEvent($component, $event, $parameters) {
		// Modifications
		if ($event == 'changelogModify') {
			$this->changeLog->handleModification($component, $parameters);

			return;
		}

		// Creations
		if (in_array($event, array( 'changelogCreate', 'afterCreate' ))) {
			$this->changeLog->handleCreation($component, $parameters);

			return;
		}

		// Deletions & Modifications
		switch ($component) {
			case 'Modules/OrgUnit':
				switch ($event) {
					case 'assignUsersToEmployeeRole':
					case 'assignUsersToSuperiorRole':
					case 'deassignUserFromEmployeeRole':
					case 'deassignUserFromSuperiorRole':
						$parameters['event'] = $event;
						$this->changeLog->handleModification($component, $parameters);
						break;
					default:
						break;
				}
				break;
			case 'Services/Object':
				switch ($event) {
					case 'changelogDelete':
						$this->handleDeletion($parameters, ChangeLogDeletionEntry::MODE_DELETE);
						break;
					case 'toTrash':
						$object = ilObjectFactory::getInstanceByRefId($parameters['ref_id'], false);
						if ($object === false) {
							return;
						}
						$parameters['object'] = $object;
						$this->handleDeletion($parameters, ChangeLogDeletionEntry::MODE_TRASH);
						break;
					default:
						break;
				}
				break;
			case 'Modules/Course':
				switch ($event) {
					case 'deleteParticipant':
						$this->trackDeletion(new ChangeLogComponentCourseParticipant(), $parameters, ChangeLogDeletionEntry::MODE_DELETE);
						break;
					default:
						break;
				}
				break;
			case 'Services/User':
				switch ($event) {
					case 'afterCreate':
						$this->changeLog->handleCreation($component, $parameters);
						break;
					case 'beforeUpdate':
						$this->changeLog->handleModification($component, $parameters);
						break;
					case 'deleteUser':
						$user = new ilObjUser();
						$user->setId($parameters["usr_id"]);
						self::trackObjectByType($user->getType(), new ChangeLogComponentUser());
						$this->handleDeletion([ "object" => $user ], ChangeLogDeletionEntry::MODE_DELETE);
						break;
					default:
						break;
				}
				break;
			default:
				break;
		}
	}


	/**
	 * Tracks deletion of all ilObject derived objects with the standard ChangeLogComponentObject class.
	 * However, you can tell this event plugin that a object type should be handled with a different implementation
	 * of the ChangeLogComponent interface. To register a component from outside this plugin:
	 *
	 * 1. Implement your component class by implementing the ChangeLogComponent interface
	 * 2. Register your component by the changelog plugin: ChangeLogChangeLog::getInstance()->registerComponent()
	 * 2. If your component handles ILIAS objects, you can tell this plugin to track a object type by your component:
	 *    ilChangeLogPlugin::trackObjectByType(object-type, component-instance)
	 * 3. If your component is not an ILIAS object: You must catch the appropriate events by yourself and use
	 *    the ChangeLogChangeLog class to track deletions: ChangeLogChangeLog::trackDeletion()
	 *
	 * @param array  $parameters
	 * @param string $deletion_mode
	 *
	 * @throws ilException
	 */
	protected function handleDeletion(array $parameters, $deletion_mode) {
		if (!isset($parameters['object'])) {
			return;
		}
		/** @var ilObject $object */
		$object = $parameters['object'];
		$component = new ChangeLogComponentObject();
		if (isset(static::$tracked_types[$object->getType()])) {
			$component = static::$tracked_types[$object->getType()];
		}
		$this->trackDeletion($component, $parameters, $deletion_mode);
	}


	/**
	 * @param ChangeLogComponent $component
	 * @param array              $parameters
	 * @param string             $deletion_mode
	 *
	 * @throws ilException
	 */
	protected function trackDeletion(ChangeLogComponent $component, array $parameters, $deletion_mode) {
		try {
			$this->changeLog->trackDeletion($component, $parameters, $deletion_mode);
		} catch (ilException $e) {
			if (DEVMODE) {
				throw $e;
			}
			self::dic()->log()->write("ChangeLog Plugin: Exception when tracking deletion: " . $e->getMessage());
		}
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 *
	 */
	protected function afterActivation() {
		$this->addCtrlMainMenu();
	}


	/**
	 *
	 */
	protected function afterDeactivation() {
		$this->removeCtrlMainMenu();
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(ChangeLogDeletionEntry::TABLE_NAME, false);
		self::dic()->database()->dropTable(ChangeLogModification::TABLE_NAME, false);
		self::dic()->database()->dropTable(ChangeLogModificationEntry::TABLE_NAME, false);
		self::dic()->database()->dropTable(ChangeLogConfig::TABLE_NAME, false);

		$this->removeCtrlMainMenu();
	}


	/**
	 *
	 */
	protected function addCtrlMainMenu() {
		try {
			include_once __DIR__ . "/../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

			if (class_exists(ctrlmmEntry::class)) {
				if (count(ctrlmmEntry::getEntriesByCmdClass(ChangeLogModificationGUI::class)) === 0
					&& count(ctrlmmEntry::getEntriesByCmdClass(ChangeLogDeletionGUI::class)) === 0) {
					$dropdown = new ctrlmmEntryDropdown();
					$dropdown->setTitle(self::PLUGIN_NAME);
					$dropdown->setTranslations([
						"en" => self::PLUGIN_NAME,
						"de" => self::PLUGIN_NAME
					]);
					$dropdown->setPermissionType(ctrlmmMenu::PERM_ROLE);
					$dropdown->setPermission(json_encode([ self::ADMIN_ROLE_ID ]));
					$dropdown->store();

					$entry_modification = new ctrlmmEntryCtrl();
					$entry_modification->setTitle(self::PLUGIN_NAME);
					$entry_modification->setTranslations([
						"en" => self::plugin()->translate("modification_log", "", [], true, "en"),
						"de" => self::plugin()->translate("modification_log", "", [], true, "de")
					]);
					$entry_modification->setGuiClass(implode(",", [ ilUIPluginRouterGUI::class, ChangeLogModificationGUI::class ]));
					$entry_modification->setCmd(ChangeLogModificationGUI::CMD_INDEX);
					$entry_modification->setParent($dropdown->getId());
					$entry_modification->store();

					$entry_deletion = new ctrlmmEntryCtrl();
					$entry_deletion->setTitle(self::PLUGIN_NAME);
					$entry_deletion->setTranslations([
						"en" => self::plugin()->translate("deletion_log", "", [], true, "en"),
						"de" => self::plugin()->translate("deletion_log", "", [], true, "de")
					]);
					$entry_deletion->setGuiClass(implode(",", [ ilUIPluginRouterGUI::class, ChangeLogDeletionGUI::class ]));
					$entry_deletion->setCmd(ChangeLogDeletionGUI::CMD_INDEX);
					$entry_deletion->setParent($dropdown->getId());
					$entry_deletion->store();
				}
			}
		} catch (Throwable $ex) {
		}
	}


	/**
	 *
	 */
	protected function removeCtrlMainMenu() {
		try {
			include_once __DIR__ . "/../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

			if (class_exists(ctrlmmEntry::class)) {
				foreach (ctrlmmEntry::getEntriesByCmdClass(ChangeLogModificationGUI::class) as $entry) {
					/**
					 * @var ctrlmmEntry $entry
					 */
					$entry->delete();

					if (!empty($entry->getParent())) {
						$entry = ctrlmmEntry::find($entry->getParent());
						if ($entry !== NULL && $entry->getTitle() === self::PLUGIN_NAME) {
							$entry->delete();
						}
					}
				}
				foreach (ctrlmmEntry::getEntriesByCmdClass(ChangeLogDeletionGUI::class) as $entry) {
					/**
					 * @var ctrlmmEntry $entry
					 */
					$entry->delete();

					if (!empty($entry->getParent())) {
						$entry = ctrlmmEntry::find($entry->getParent());
						if ($entry !== NULL && $entry->getTitle() === self::PLUGIN_NAME) {
							$entry->delete();
						}
					}
				}
			}
		} catch (Throwable $ex) {
		}
	}
}
