#Awards Plugin for Vanilla 2.0

##Description
Awards Plugin adds gamification features to your self-hosted Vanilla Forum, by allowing you to create Badges and assign them to Users based on a combination of criteria.

The plugin implements a Rule system, which provides a high degree of flexibility in determining how Users can earn an Award, by supporting a virtually unlimited amount of Rules.The plugin comes with a set of Rules, and it can be extended by adding Custom Rules.

For more information about the implementation of Custom Rules, see the **Customisation** section.

##Requirements
* PHP 5.3+
* Vanilla 2.0.18
* Logger Plugin (either Basic or Advanced version) 12.10.28 or newer
* Aelia Foundation Classes 13.04.26 or newer

##Installation

* Copy the Awards Plugin folder in the /plugins folder, which can be found in your Vanilla installation folder.
* Delete all .ini files from the cache folder and all its subfolders. Cache folder is also in your Vanilla installation folder.
*	Enable the Awards Plugin.

###Important installation notes
The following directories must be writable, for the plugin to work correctly:

* plugins/Awards/design/css. CSS file for Award Classes will be saved here.
* plugins/Awards/images/awards. It will contain Awards Images.
* plugins/Awards/images/awardclasses. It will contain Award Classes Images.
* plugins/Awards/export. Exported files will be saved here.
* uploads/awards/ixport. Imported files will be saved here.

##Upgrade
*	In Vanilla Control Panel, disable the Awards Plugin. Do not skip this step.
*	Delete all .ini files from the cache folder and all its subfolders. Cache folder is also in your Vanilla installation folder.
* Enable the Awards Plugin.

##Usage
The Plugin provides an intuitive User Interface. However, there are a few key concepts that you must learn before using it.

###Award Classes
Award Classes are a neat way to organise Awards. By creating Classes, you can group Awards together, for example to divide the common Awards from the most prestigious ones.

The following is a generic set of Award Classes that could suit most Communities:
*	Bronze. These would be the most common, easy to achieve Awards.
*	Silver. These would be the uncommon Awards, granted to Users who show effort and participation.
*	Gold. These would be the rarest Awards, obtained only by User who achieved excellent results.
* Special. These would be Awards that are assigned in particular circumstances, usually only by Administrators, rather than automatically.

As you can see, Award Classes can immediately make clear what Awards are the most valuable, incrementing the involvement of your forum Users.

####How to configure Award Classes
Configuring an Award Class requires just a few steps:

* In Vanilla Forums Admin area, click on Award Classes in the side menu.
* Click on Add Award Class.
* Enter Award Class Details
	* Fill the Name and the Description.
	* Upload an image for the Class (optional). Such image will be used as a background for all the Awards in the class, and can help distinguishing them at a glance.
	* Specify the Score to add to all Awards in this class (optional)
	* For fine tuning of Class' look and feel, enter additional CSS properties (optional). **Important**: specify CSS attributes without enclosing them in braces. The plugin will add them automatically.
  * Click on Save.

The Award Class is now configured and can be used when creating new Awards.

If you do not wish to use the features provided by Award Classes, simply create a single one without a background image and/or a score, and assign all Awards to it. You will be able to change this later, if needed.

###Awards
Awards (also called Badges) are assigned to Users who satisfy certain criteria. Creating an Award is very easy:

In Vanilla Forums Admin area, click on Awards in the side menu.
* Click on Add Award.
* Enter Award Details
	* Fill the Name, Description and Rank Points fields.
	* Select a Class for the Award.
	* Upload an image for the Award.

Configure the Rules that will have to be satisfied for the Award to be assigned to a User. To do so:
	* Tick the checkbox of the Rule that you would like to enable.
	* Fill the related field(s).

When you're done with the Rules, click on Save. The Award is now configured, and you will be able to see it in the Awards List page and in the frontend Awards page. **Note**: the latter will only display Enabled Awards.

###Assigning Awards
Awards can be assigned in two ways:
* Automatically, whenever a User visits the forum. The plugin will go through the Awards that the User has not yet earned, and check if he satisfies the conditions for one or more of them. If he does, he will earn the Award(s) assigned, and a notification will be saved to the Activity List.
* Manually, from the Awards List page in the Admin section. By clicking on the Assign button next to an Award, you will open a page where you will be able to select one or more Users who will receive the Award.

**Note**: if a User already earned the Award, he will not get it again.

###Importing and Exporting Awards
The plugin implements an Import/Export mechanism, which allows to export your Awards and Award Classes (together with their Images) and import them back at a later stage. This feature can be used as a simple backup mechanism, or as a way to package pre-configured Awards and distribute them.

####Exporting Awards and Award Classes
To use the Export feature, go to Vanilla Admin Dashboard and click on the Export menu item in the Awards group. The interface is intuitive and it should be self-explanatory.

####Importing Awards and Award Classes
To use the Import feature, go to Vanilla Admin Dashboard and click on the Import menu item in the Awards group. The interface is slightly more complex than the Export one, but it's easy to use.

One important feature that you should familiarise with is the Test Import. Such action performs the same operations as the normal Import, but all modifications are reverted at the end of the process. The purpose of this is testing if a file can be imported correctly, without risking to mix up the data should anything go wrong half way through the import.

**Important**: always perform a backup of the forum before attempting to use the Import feature. This will ensure that, in the unlikely case of unexpected errors, you will be able to restore it to its original condition.

###Notes
*	Plugin has not been tested with Vanilla 2.1. We cannot guarantee that it will work with such version.

##License
GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
