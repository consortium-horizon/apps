#Aelia Foundation Classes for Vanilla Forums 2.0/2.1 - Change Log

####14.08.21.001
* Added semaphore class, `Aelia\Semaphore`.

####14.07.22.002
* Removed creation of uneeded routes, which prevented the plugin from enabling.
* Refactored Definitions class to usa late static binding.

####14.07.22.001
* Fixed incorrect references to definitions.
* Removed override for ActivityModel for Vanilla 2.1b1.
* Added override for ActivityModel for Vanilla 2.1 (production).

####14.07.19.001
* Imported methods from ThankFrank\BaseController class:
	* Imported `AeliaController::IsAjaxRequest()`.
	* Imported `AeliaController::AddCssFiles()`.
	* Imported `AeliaController::AddJsFiles()`.
	* Imported `AeliaController::SetRedirect()`.
	* Imported `AeliaController::StashMessages()`.
	* Imported `AeliaController::AddMessagesKey()`.
	* Imported `AeliaController::LoadStashedMessages()`.
	* Imported `AeliaController::Target()`.
* Moved definitions to `Aelia\AFC\Definitions` class.

####14.06.24.001
* Added population of global variables for more HTTP methods: PUT, DELETE and PATCH.

####14.05.08.001
* Corrected constructor of Aelia\Module class.

####14.03.21.001
* Added base Aelia\Pluggable class.

####14.03.20.001
* Fixed bugs in Aelia\Schema
	* Fixed reference to Gdn class in root scope.
	* Fixed bug in Schema::ColumnExists() caused by the usage of global Database instance.

####14.02.17.001
* Added new CookieIdentity class, with support for configurable session lifespan.

####14.02.05.001
* Improved handling of "permission denied" condition by redirecting the user to the login page

####14.02.03.001
* Improved validation in base model

####13.12.29.001
* Fixed bug in handling of Transient Key for guest users

####13.12.09.001
* Refactored logic used to override Core classes

####13.12.04.001
* Fixed bug in handling "not found" response
* Corrected rendering of "not found" and "permission denied" pages

####13.11.30.001
* Added base menu module

####13.11.23.001
* Improved checking of Primary Key in BaseModel::Save()

####13.11.22.001
* Improved checking of Primary Key in BaseModel::Save()

####13.11.20.001
* Added base configuration model class

####13.11.19.003
* Corrected generation of date ranges in helper class

####13.11.19.002
* Added helper class.

####13.11.19.001
* Modified base model class. Added event when saving of data fails
* Corrected initialisation of Logger in base classes
* Refactored BaseModel::SetDateRange() to make it more flexible

####13.11.18.001
* Minor improvements in base Model class
* Added function to validate positive numbers (integers and decimals)

####13.11.15.001
* Added class to implement auto-update of a package (plugin, application, theme)
* Added class to display messages to Administrators

####13.11.12.002
* Corrected query used to retrieve the AutoIncrement field from a table

####13.11.12
* Corrected scope resolution of base Exceptions

####13.11.11
* Corrected rendering of multi-select dropdown fields
* Corrected logic to return the primary key of inserted rows

####13.11.11
* Optimised handling of limit and offset in base model

####13.11.07
* Refactored code used to return "permission denied" and "not found" errors
* Refactored validation code in base model to make it more flexible

####13.11.05
* Modified base model to load additional rules, if any, before attempting to save any data

####13.11.04
* Added overrides for SideMenuModule class (Vanilla 2.0 and 2.1b1). New classes grant more flexibility in determining the permission requirements to access a menu item

####13.11.03
* Improved support for multi-select dropdown fields

####13.11.01
* Moved base classes to their own namespace. The original classes are still maintained in the root namespace, for backward compatibility.
* Added base Dashboard controller

####13.10.31
* Added automatic initialisation of an AeliaForm in base controller class

####13.10.29
* Added support for grouping of textarea fields

####13.10.28
* Added methods to create grouped field to AeliaForm class

####13.10.27
* Added AeliaForm and AeliaSession classes, which extend standard Gdn_Form and Gdn_Session classes

####13.10.22
* Added base Schema class

####13.10.15
* Added method to easily set date ranges in base Model class

####13.10.09
* Added base Controller class

####13.09.06
* Added base Plugin class

####13.07.17
* Altered ActivityModel::GetWhere() to include User Photo (required by Awards plugin)

####13.05.23
* Removed class Alias

####13.05.16
* Replaced custom Autoloader with Composer

####13.04.30
* Disabled forced error reporting

####13.04.24
* Added jQuery TipTip to improve tooltips

####13.04.10
* Added dependency from Logger plugin
* Prepended internal autoloader to autoloaders queue

####13.04.07
* Added clearfix to base CSS styles

####13.04.02
* Corrected issue with permissions. Previous versions allowed only SuperAdmin to access Plugin's settings.

####13.03.27
* Added Autoloader as a safety net for when Vanilla Autoloader fails to pick up the classes exposed by the plugin

####13.03.26
* Initial Release
