<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['Memberships'] = array(
   'Name' => 'Memberships',
   'Description' => 'This plugin allows users to be assigned to groups created by the Groups plugin.',
   'Version' => '0.1',
   'RequiredApplications' => FALSE,
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => array('Groups' => '0.1'),
   'SettingsUrl' => '/dashboard/plugin/memberships',
   'SettingsPermission' => 'Garden.Settings.Manage',
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "Johnathon Williams",
   'AuthorEmail' => 'john@oddjar.com',
   'AuthorUrl' => 'http://oddjar.com'
);

class MembershipsPlugin extends Gdn_Plugin {
   
   public function Base_GetAppSettingsMenuItems_Handler($Sender) {    
      $LinkText = T('Memberships');
      $Menu = $Sender->EventArguments['SideMenu'];
      $Menu->AddItem('Users', T('Users'));
      $Menu->AddLink('Users', $LinkText, 'plugin/memberships', 'Garden.Settings.Manage');
   }

   public function PluginController_Memberships_Create($Sender) {
      $Sender->Permission('Garden.Settings.Manage');
      $Sender->Title('Membership Management');
      $Sender->AddSideMenu('plugin/memberships');
      $Sender->Form = new Gdn_Form();
      $this->Dispatch($Sender, $Sender->RequestArgs);
   }
   
   public function Controller_Index($Sender) {
      $Sender->AddCssFile('admin.css');
      $Sender->AddCssFile($this->GetResource('design/memberships.css', FALSE, FALSE));
      $GroupCheck = Gdn::SQL()
		 ->Select('gr.GroupID')
		 ->From('Group gr')
		 ->Get()->NumRows();
      $MemberList = Gdn::SQL()
		 ->Select('us.UserID, us.Name, us.Email')
	  	 ->Select('ug.GroupID')
		 ->OrderBy('us.Email', 'asc')
	     ->Select('gr.Name', '', 'GroupName')
	     ->From('User us')
	     ->Where('us.Deleted', 0)
		 ->Join('UserGroup ug', 'us.UserID = ug.UserID', 'left')
		 ->Join('Group gr', 'ug.GroupID = gr.GroupID', 'left')
         ->Get();
      while ($MemberItems = $MemberList->NextRow(DATASET_TYPE_ARRAY)) {
		 $Sender->MemberList[] = $MemberItems;
      }
	  $Sender->GroupCheck = $GroupCheck;
      unset($MemberList);
      $Sender->Render($this->GetView('memberships.php'));
   }

   public function Controller_Edit($Sender) {   
	
      if ($Sender->Form->AuthenticatedPostBack()) {
         $UserID = $Sender->Form->GetValue('Plugin.Memberships.UserID');
		 $GroupID = $Sender->Form->GetValue('Plugin.Memberships.GroupID');
		
		// check for existing membership
		  $Membership = Gdn::SQL()->Select('ug.GroupID', '', 'OldGroupID')
	         ->From('UserGroup ug')
			 ->Where('ug.UserID', $UserID)
	         ->Get();
	
	      $MembershipCheck = $Membership->FirstRow(DATASET_TYPE_ARRAY);

			if ($MembershipCheck['OldGroupID'] > 0) {
				try {
	            Gdn::SQL()->Update('UserGroup ug')
	            ->Set('ug.GroupID', $GroupID)
	            ->Where('ug.UserID', $UserID)
	            ->Put();
	         } catch(Exception $e) {}
			} else {
				Gdn::SQL()->Insert('UserGroup',array(
		         'UserID' => $UserID,
				 'GroupID' => $GroupID
		        ));
			}
         $Sender->StatusMessage = T("Your changes have been saved.");
         $Sender->RedirectUrl = Url('plugin/memberships');

      } else {
		  // send the group data to the form
		  $Arguments = $Sender->RequestArgs;
	      if (sizeof($Arguments) != 2) return;
	      list($Controller, $UserID) = $Arguments;
	
	      $UserInQuestion = Gdn::SQL()->Select('us.UserID, us.Name')
	         ->From('User us')
			 ->Where('us.UserID', $UserID)
	         ->Get();
	      $OldMembership = Gdn::SQL()->Select('ug.GroupID', '', 'OldGroupID')
	         ->From('UserGroup ug')
			 ->Where('ug.UserID', $UserID)
	         ->Get();
		  $Groups = Gdn::SQL()
		     ->Select('gr.GroupID', '', 'value')
		     ->Select('gr.Name', '', 'text')
	         ->From('Group gr')
	         ->Get();
		  $Sender->Groups = $Groups;
		  $Sender->OldMembership = $OldMembership->FirstRow(DATASET_TYPE_ARRAY);
		  $Sender->UserInQuestion = $UserInQuestion->FirstRow(DATASET_TYPE_ARRAY);
	  }
      $Sender->Render($this->GetView('edit.php'));
   }
   

   public function Structure() {
      $Structure = Gdn::Structure();
      $Structure
         ->Table('UserGroup')
         ->Column('UserID', 'int(11)')
         ->Column('GroupID', 'int(11)')
         ->Set(FALSE, FALSE);
   }

   public function Setup() {
      $this->Structure();
      SaveToConfig('Plugins.Memberships.Enabled', TRUE);
   }
   
	public function OnDisable() {
		SaveToConfig('Plugins.Memberships.Enabled', FALSE);
	}


   
}