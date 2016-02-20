<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['Memberships'] = array(
   'Name' => 'Memberships',
   'Description' => 'This plugin allows user assignment to groups. Used in tandem with the Groups plugin',
   'Version' => '0.1',
   'RequiredApplications' => FALSE,
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
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
      unset($MemberList);
      $Sender->Render($this->GetView('memberships.php'));
   }

   public function Controller_Edit($Sender) {   
	
      if ($Sender->Form->AuthenticatedPostBack()) {
         $UserID = $Sender->Form->GetValue('Plugin.Memberships.UserID');
		 $GroupID = $Sender->Form->GetValue('Plugin.Memberships.GroupID');
		
		// check for existing membership
		  $Membership = Gdn::SQL()->Select('*')
	         ->From('UserGroup ug')
			 ->Where('ug.UserID', $UserID)
			 ->Where('ug.GroupID', $GroupID)
	         ->Get();
	
	      $MembershipCheck = $Membership->FirstRow(DATASET_TYPE_ARRAY);

			if ($MembershipCheck['GroupID'] != '')) {
				try {
	            Gdn::SQL()
	       	    ->Update('UserGroup ug')
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
   

   public function Setup() {
      // $this->Structure();
      SaveToConfig('Plugins.Memberships.Enabled', TRUE);
   }
   
	public function OnDisable() {
		SaveToConfig('Plugins.Memberships.Enabled', FALSE);
	}


   
}