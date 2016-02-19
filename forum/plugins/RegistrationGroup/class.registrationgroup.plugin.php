<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2013 Alessandro Miliucci <lifeisfoo@gmail.com>
This file is part of RegistrationGroup vanillaforums plugin <https://github.com/lifeisfoo/RegistrationGroup>

RegistrationGroup is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

RegistrationGroup is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with RegistrationGroup. If not, see <http://www.gnu.org/licenses/>.

== Based on the VanillaStarter plugin ==
https://github.com/lifeisfoo/VanillaStarter
*/

// Define the plugin:
$PluginInfo['RegistrationGroup'] = array(
   'Description' => 'Allow users to select a group at registration.',
   'Version' => '0.1',
   'RequiredApplications' => array('Vanilla' => '2.1a1'), //registration hooks...
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => array('Memberships' => '0.1'),
   'HasLocale' => FALSE,
   'Author' => "Alessandro Miliucci",
   'AuthorEmail' => 'lifeisfoo@gmail.com',
   'AuthorUrl' => 'http://forkwait.net'
);

class RegistrationGroupPlugin extends Gdn_Plugin {

  public function __construct() {}
   
  /**
  * Add fields to registration forms.
  */
  public function EntryController_RegisterBeforePassword_Handler($Sender) {
    $Groups = Gdn::SQL()->Select('gr.GroupID', '', 'value')
                         ->Select('gr.Name', '', 'text')
                         ->From('Group gr')
                         ->Get();

    echo Wrap($Sender->Form->Label(T('Group'), 'Plugin.RegistrationGroup.GroupID').
      $Sender->Form->DropDown('Plugin.RegistrationGroup.GroupID', $Groups, array('IncludeNull' => TRUE)), 'li');
  }

  /**
  * Required fields on registration forms.
  */
  public function EntryController_RegisterValidation_Handler($Sender) {
    $FormPostValues = GetValue('_FormValues', $Sender->Form);
    $GroupID = GetValue('Plugin.RegistrationGroup.GroupID', $FormPostValues);
    
    $Group = Gdn::SQL()->Select('g.*')
              ->From('Group g')
              ->Where('g.GroupID', $GroupID)
              ->Get()->Result();
    if(!$Group){
      //no group found
      $Sender->UserModel->Validation->AddValidationResult('Plugin.RegistrationGroup.GroupID', sprintf(T('%s is required.'), T('Group')));
    }
  }

  public function UserModel_AfterInsertUser_Handler($Sender) { 
    if (!(Gdn::Controller() instanceof Gdn_Controller)) return;
      
    //Get user-submitted
    $FormPostValues = Gdn::Controller()->Form->FormValues();
    $UserID = GetValue('InsertUserID', $Sender->EventArguments);
    $GroupID = GetValue('Plugin.RegistrationGroup.GroupID', $FormPostValues);

    if( $UserID && $GroupID ){
        Gdn::SQL()->Insert('UserGroup',array(
            'UserID' => $UserID,
            'GroupID' => $GroupID
        ));
    }

  }


}