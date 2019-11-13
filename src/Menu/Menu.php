<?php

namespace srag\Plugins\ChangeLog\Menu;

use ilChangeLogPlugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilUIPluginRouterGUI;
use srag\DIC\ChangeLog\DICTrait;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionGUI;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationGUI;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrl;
use srag\Plugins\CtrlMainMenu\EntryTypes\Dropdown\ctrlmmEntryDropdown;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;
use Throwable;

/**
 * Class Menu
 *
 * @package srag\Plugins\ChangeLog\Menu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticPluginMainMenuProvider
{

    use DICTrait;
    use ChangeLogTrait;
    const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


    /**
     * @inheritdoc
     */
    public function getStaticTopItems() : array
    {
        return [
            $this->mainmenu->topParentItem($this->if->identifier(ilChangeLogPlugin::PLUGIN_ID))->withTitle(self::plugin()->translate("changelog"))
                ->withAvailableCallable(function () : bool {
                    return self::plugin()->getPluginObject()->isActive();
                })->withVisibilityCallable(function () : bool {
                    return in_array(ilChangeLogPlugin::ADMIN_ROLE_ID, self::dic()->rbacreview()->assignedRoles(self::dic()->user()->getId()));
                })
        ];
    }


    /**
     * @inheritdoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];

        return [
            $this->mainmenu->link($this->if->identifier(ilChangeLogPlugin::PLUGIN_ID . "_mod"))->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("modification_log"))->withAction(self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    ChangeLogModificationGUI::class
                ], ChangeLogModificationGUI::CMD_INDEX)),
            $this->mainmenu->link($this->if->identifier(ilChangeLogPlugin::PLUGIN_ID . "_del"))->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("deletion_log"))->withAction(self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    ChangeLogDeletionGUI::class
                ], ChangeLogDeletionGUI::CMD_INDEX))
        ];
    }


    /**
     * @deprecated
     */
    public static function addCtrlMainMenu()
    {
        try {
            include_once __DIR__ . "/../../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

            if (class_exists(ctrlmmEntry::class)) {
                if (count(ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", ChangeLogModificationGUI::class))) === 0
                    && count(ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", ChangeLogDeletionGUI::class))) === 0
                ) {
                    $dropdown = new ctrlmmEntryDropdown();
                    $dropdown->setTitle(ilChangeLogPlugin::PLUGIN_NAME);
                    $dropdown->setTranslations([
                        "en" => self::plugin()->translate("changelog", "", [], true, "en"),
                        "de" => self::plugin()->translate("changelog", "", [], true, "de")
                    ]);
                    $dropdown->setPermissionType(ctrlmmMenu::PERM_ROLE);
                    $dropdown->setPermission(json_encode([ilChangeLogPlugin::ADMIN_ROLE_ID]));
                    $dropdown->store();

                    $entry_modification = new ctrlmmEntryCtrl();
                    $entry_modification->setTitle(ilChangeLogPlugin::PLUGIN_NAME);
                    $entry_modification->setTranslations([
                        "en" => self::plugin()->translate("modification_log", "", [], true, "en"),
                        "de" => self::plugin()->translate("modification_log", "", [], true, "de")
                    ]);
                    $entry_modification->setGuiClass(implode(",", [ilUIPluginRouterGUI::class, ChangeLogModificationGUI::class]));
                    $entry_modification->setCmd(ChangeLogModificationGUI::CMD_INDEX);
                    $entry_modification->setParent($dropdown->getId());
                    $entry_modification->store();

                    $entry_deletion = new ctrlmmEntryCtrl();
                    $entry_deletion->setTitle(ilChangeLogPlugin::PLUGIN_NAME);
                    $entry_deletion->setTranslations([
                        "en" => self::plugin()->translate("deletion_log", "", [], true, "en"),
                        "de" => self::plugin()->translate("deletion_log", "", [], true, "de")
                    ]);
                    $entry_deletion->setGuiClass(implode(",", [ilUIPluginRouterGUI::class, ChangeLogDeletionGUI::class]));
                    $entry_deletion->setCmd(ChangeLogDeletionGUI::CMD_INDEX);
                    $entry_deletion->setParent($dropdown->getId());
                    $entry_deletion->store();
                }
            }
        } catch (Throwable $ex) {
        }
    }


    /**
     * @deprecated
     */
    public static function removeCtrlMainMenu()
    {
        try {
            include_once __DIR__ . "/../../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

            if (class_exists(ctrlmmEntry::class)) {
                foreach (ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", ChangeLogModificationGUI::class)) as $entry) {
                    /**
                     * @var ctrlmmEntry $entry
                     */
                    $entry->delete();

                    if (!empty($entry->getParent())) {
                        $entry = ctrlmmEntry::find($entry->getParent());
                        if ($entry !== null && $entry->getTitle() === ilChangeLogPlugin::PLUGIN_NAME) {
                            $entry->delete();
                        }
                    }
                }
                foreach (ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", ChangeLogDeletionGUI::class)) as $entry) {
                    /**
                     * @var ctrlmmEntry $entry
                     */
                    $entry->delete();

                    if (!empty($entry->getParent())) {
                        $entry = ctrlmmEntry::find($entry->getParent());
                        if ($entry !== null && $entry->getTitle() === ilChangeLogPlugin::PLUGIN_NAME) {
                            $entry->delete();
                        }
                    }
                }
            }
        } catch (Throwable $ex) {
        }
    }
}
