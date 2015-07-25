Custom Rules Directory
==
You can place any custom Rule in this directory, to make sure that your changes won't be overwritten by future updates.

If you wish to implement a Custom Rule to replace a Core one, simply give it the same name.

Example 1 - Brand new Rule
--
- Make a copy of *SkeletonRule* directory. Name it *MyCustomRule* and save it to *AwardsPlugin/lib/classes/rules/custom*.
- Rename file *class.skeletonrule.php* to *class.mycustomrule.php*.
- Open file *class.mycustomrule.php* and rename class `SkeletonRule` to `MyCustomRule`.

At this point, your rule should now be already picked up by the Awards Plugin, although it doesn't really do anything. You can now implement its logic and configuration interface, and see the plugin fire it when appropriate.

Example 2 - Core Rule Override
--
- Copy the whole directory of the Core Rule that you wish to override (e.g. Anniversary) from *AwardsPlugin/lib/classes/rules/core* to *AwardsPlugin/lib/classes/rules/custom*. Make sure that you keep the same directory name.
- Modify the Core as you wish. The Awards Plugin will automatically pick your rule, instead of the Core one.

**Important**
Be **extremely careful** when you override Core rules that have already been used. Your Custom Rule will receive the configuration that was saved by the Core Rule earlier, and it's supposed to parse it appropriately.

Also, if your Custom Rule produces a configuration incompatible with the original Core one, you may encounter unpredictable results if you choose to replace 
