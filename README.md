## Installation

### Install ChangeLog-Plugin
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
git clone git@git.studer-raimann.ch:ILIAS/Plugins/ChangeLog.git ChangeLog
```
Update, activate and config the plugin in the ILIAS Plugin Administration

### Dependencies
* [composer](https://getcomposer.org)
* [CtrlMainMenu](https://github.com/studer-raimann/CtrlMainMenu)
* [srag/activerecordconfig](https://packagist.org/packages/srag/activerecordconfig)
* [srag/dic](https://packagist.org/packages/srag/dic)
* [srag/removeplugindataconfirm](https://packagist.org/packages/srag/removeplugindataconfirm)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/ChangeLog/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/PLCH
* Bug reports under https://jira.studer-raimann.ch/projects/PLCH
* For external users please send an email to support-custom1@studer-raimann.ch
