<?php

namespace srag\Plugins\ChangeLog\Menu;

use ilChangeLogPlugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\ChangeLog\DICTrait;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionGUI;
use srag\Plugins\ChangeLog\LogEntry\Modification\ChangeLogModificationGUI;
use srag\Plugins\ChangeLog\Utils\ChangeLogTrait;

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
                })->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->custom(ilUtil::getImagePath("outlined/icon_lstv.svg"), ilChangeLogPlugin::PLUGIN_NAME))
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
                ->withTitle(self::plugin()->translate("modification_log"))->withAction(str_replace("\\", "%5C", self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    ChangeLogModificationGUI::class
                ], ChangeLogModificationGUI::CMD_INDEX)))->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->custom(ilUtil::getImagePath("outlined/icon_lstv.svg"), ilChangeLogPlugin::PLUGIN_NAME)),
            $this->mainmenu->link($this->if->identifier(ilChangeLogPlugin::PLUGIN_ID . "_del"))->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("deletion_log"))->withAction(str_replace("\\", "%5C", self::dic()->ctrl()->getLinkTargetByClass([
                    ilUIPluginRouterGUI::class,
                    ChangeLogDeletionGUI::class
                ], ChangeLogDeletionGUI::CMD_INDEX)))->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->custom(ilUtil::getImagePath("outlined/icon_lstv.svg"), ilChangeLogPlugin::PLUGIN_NAME))
        ];
    }
}
