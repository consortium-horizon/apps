<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['CustomPages'] = array(
   'Name' => 'Custom Pages',
   'Description' => 'A plugin that lets you add custom pages. You need to add them to the "pages" folder of this plugin.',
   'Version' => '1',
   'Author' => "Mark O'Sullivan",
   'AuthorEmail' => 'mark@vanillaforums.com',
   'AuthorUrl' => 'http://vanillaforums.com'
);

class CustomPagesPlugin implements Gdn_IPlugin {

   /**
    * Adds the "default" page to the dashboard side menu.
    */
   public function Base_GetAppSettingsMenuItems_Handler($Sender) {
      $Menu = $Sender->EventArguments['SideMenu'];
      $Menu->AddLink('Site Settings', 'Custom Pages', 'plugin/page/default/admin');
   }
   
   public function PluginController_Page_Create($Sender) {
		// See what page was requested
		$Page = ArrayValue('0', $Sender->RequestArgs, 'default');
		$MasterView = ArrayValue('1', $Sender->RequestArgs, 'default');
		$MasterView = $MasterView != 'admin' ? 'planetside' : 'admin';
		$Path = PATH_PLUGINS . DS . 'CustomPages' . DS . 'pages' . DS;
		// If the page doesn't exist, roll back to the default
		if (!file_exists($Path.$Page.'.php'))
			$Page = 'default';
		
		// Use the default css if not viewing admin master
		if ($MasterView != 'admin') {
			$Sender->ClearCssFiles();
			$Sender->AddCssFile('style.css');
		} else {
			$Sender->AddSideMenu('plugin/page/default/admin');
		}
		$Sender->MasterView = $MasterView;
      $Sender->Render($Path.$Page.'.php');
   }

   public function Setup() {
      // No setup required.
   }
}
