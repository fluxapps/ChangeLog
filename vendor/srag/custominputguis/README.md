Custom Input-GUI's

### Usage

#### Composer
First add the follow to your `composer.json` file:
```json
"require": {
  "srag/custominputguis": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

### Input-GUI's
* [MultiSelectSearchInputGUI](./doc/MultiSelectSearchInputGUI.md)
* [MultiSelectSearchInput2GUI](./doc/MultiSelectSearchInput2GUI.md)

### Dependencies
* [composer](https://getcomposer.org)
* [npm](https://nodejs.org)
* [select2](https://www.npmjs.com/package/select2)
* [srag/dic](https://packagist.org/packages/srag/dic)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/CustomInputGUIs/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LINP
* Bug reports under https://jira.studer-raimann.ch/projects/LINP
* For external users please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this library you should install this library like follow:

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Libraries
cd Customizing/global/plugins/Libraries
git clone git@git.studer-raimann.ch:ILIAS/Plugins/CustomInputGUIs.git CustomInputGUIs
```

### Contact
support-custom1@studer-raimann.ch
https://studer-raimann.ch
