<?php

namespace srag\Plugins\ChangeLog\Menu;

use ilChangeLogPlugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilUIPluginRouterGUI;
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
class Menu extends AbstractStaticPluginMainMenuProvider {

	use DICTrait;
	use ChangeLogTrait;
	const PLUGIN_CLASS_NAME = ilChangeLogPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getStaticTopItems(): array {
		return [
			self::dic()->globalScreen()->mainmenu()->topParentItem(self::dic()->globalScreen()->identification()->plugin(self::plugin()
				->getPluginObject(), $this)->identifier(ilChangeLogPlugin::PLUGIN_ID))->withTitle(ilChangeLogPlugin::PLUGIN_NAME)
				->withAvailableCallable(function (): bool {
					return self::plugin()->getPluginObject()->isActive();
				})->withVisibilityCallable(function (): bool {
					return in_array(ilChangeLogPlugin::ADMIN_ROLE_ID, self::dic()->rbacreview()->assignedRoles(self::dic()->user()->getId()));
				})
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getStaticSubItems(): array {
		$parent = $this->getStaticTopItems()[0];

		return [
			self::dic()->globalScreen()->mainmenu()->link(self::dic()->globalScreen()->identification()->plugin(self::plugin()
				->getPluginObject(), $this)->identifier(ilChangeLogPlugin::PLUGIN_ID . "_mod"))->withParent($parent->getProviderIdentification())
				->withTitle(self::plugin()->translate("modification_log"))->withAction(self::dic()->ctrl()->getLinkTargetByClass([
					ilUIPluginRouterGUI::class,
					ChangeLogModificationGUI::class
				], ChangeLogModificationGUI::CMD_INDEX)),
			self::dic()->globalScreen()->mainmenu()->link(self::dic()->globalScreen()->identification()->plugin(self::plugin()
				->getPluginObject(), $this)->identifier(ilChangeLogPlugin::PLUGIN_ID . "_del"))->withParent($parent->getProviderIdentification())
				->withTitle(self::plugin()->translate("deletion_log"))->withAction(self::dic()->ctrl()->getLinkTargetByClass([
					ilUIPluginRouterGUI::class,
					ChangeLogDeletionGUI::class
				], ChangeLogDeletionGUI::CMD_INDEX))
		];
	}
}
