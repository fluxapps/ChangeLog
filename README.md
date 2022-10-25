This is an OpenSource project by studer + raimann ag, CH-Burgdorf (https://studer-raimann.ch)

## Installation

### Install ChangeLog-Plugin
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook
cd Customizing/global/plugins/Services/EventHandling/EventHook
git clone https://github.com/studer-raimann/ChangeLog.git ChangeLog
```
Update, activate and config the plugin in the ILIAS Plugin Administration

### Menu (Only ILIAS 5.3)
For ILIAS 5.3, you need to use [CtrlMainMenu](https://github.com/studer-raimann/CtrlMainMenu)

### ILIAS-Core-Patch
In order for a user's update to be recognized correctly, it needs a patch in the ILIAS core in `Services/User/classes/class.ilObjUser.php::update`:
```php
...
class ilObjUser {
	...
	public function update() {
		...
		global $ilErr, $ilDB, $ilAppEventHandler;
		
		//PATCH ChangeLog
		$ilAppEventHandler->raise("Services/User", "beforeUpdate",
		array("user_obj" => $this));
		//PATCH ChangeLog
		
		$this->syncActive();
		
		...
	}
	...
}
```

### Some screenshots
Log table:
![Log table](./doc/screenshots/log_table.png)

### Requirements
* ILIAS 5.3 or ILIAS 5.4
* PHP >=7.0

## Rebuild & Maintenance

fluxlabs ag, support@fluxlabs.ch

This project needs to be rebuilt before it can be maintained.

Are you interested in a rebuild and would you like to participate?
Take advantage of the crowdfunding opportunity under [discussions](https://github.com/fluxapps/ChangeLog/discussions/3).
