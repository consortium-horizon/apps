<?php if (!defined('APPLICATION')) exit();
//Original Plugin by @peregrine, Redistributed by VrijVlinder with Peregrine's permission.

// Define the plugin:
$PluginInfo['ForumDonate'] = array(
   'Name' => 'ForumDonate',
   'Description' => "Put a panel in the sidebar for forum donations. Donate",
   'Version' => '1.3',
   'Requires' => FALSE, 
   'HasLocale' => FALSE,
   'License'=>"GNU GPL2",
   'Author' => "VrijVlinder" 
);

class ForumDonatePlugin extends Gdn_Plugin {
  
   public function Base_Render_Before($Sender) {
    $Controller = $Sender->ControllerName;
	$ShowOnController = array(
					'discussioncontroller',
					'categoriescontroller',
					'discussionscontroller',
					'profilecontroller',
					'activitycontroller'
				);
   if (!InArrayI($Controller, $ShowOnController)) return; 

     $Sender->AddCssFile('forumdonate.css', 'plugins/ForumDonate');
    
      $ForumDonateModule = new ForumDonateModule($Sender);
      $Sender->AddModule($ForumDonateModule);
   }
 
  
   
   public function Setup() {
   
   }
}
