#Aelia Foundation Classes for Vanilla

##Description
Aelia Foundation Classes plugin implements some convenience classes that can be used by other plugins. It also extends some Core files by adding new events and features, while maintaining the compatibility with original ones.

##Installation
* Copy the AeliaFoundationClasses folder in the /plugins folder, which can be found in your Vanilla installation folder.
* Delete all .ini files from the cache folder and all its subfolders. Cache folder is also in your Vanilla installation folder.
* Enable the Aelia Foundation Classes Plugin.

##Upgrade
* In Vanilla Control Panel, disable the Aelia Foundation Classes Plugin. Please note that, if you have plugins that depend on AFC, you will have to disable them first, before you can disable this plugin. Do not skip this step.
* Delete all .ini files from the cache folder and all its subfolders. Cache folder is also in your Vanilla installation folder.
* Enable the Aelia Foundation Classes plugin.

##Usage
The Plugin doesn't require any configuration and it doesn't perform any action, apart from exposing classes to other plugins. Therefore, you just need to install and enabled it before the plugins that require it.

##Requirements
* PHP 5.3+
* Vanilla 2.0/2.1b (see Notes)
* Logger Plugin 12.10.28 or newer

Notes
* Plugin has been tested on Vanilla 2.1b and it should be compatibile with it. However, since Vanilla 2.1 is still in Beta, compatibility cannot be guaranteed. For the same reason, we provide limited assistance for installations in such environment.
* **Important**: this plugin makes some modifications to Vanilla Core files. Such changes are made automatically, in a non-permanent way when the plugin is enabled, and should not affect normal operations.
If you wish to ensure that every modification to Core files is removed when the plugin is disabled, simply remove all ini files from cache folder and its subfolders.

##License
GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt
