<?php if (!defined('APPLICATION')) exit();

$PluginInfo['Group2Role'] = array(
   'Name' => 'Group2Role',
   'Description' => "Add selected role to all users in a group",
   'SettingsUrl' => 'settings/group2role',
   'Version' => '0.1.1',
   'RequiredPlugins' => array('Memberships' => '0.1'),
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'RegisterPermissions' => array(),
   'Author' => 'Alessandro Miliucci',
   'AuthorEmail' => 'lifeisfoo@gmail.com',
   'AuthorUrl' => 'http://forkwait.net',
   'MobileSite' => FALSE
);

class Group2Role extends Gdn_Plugin{

  /**
    * Add the Dashboard menu item.
    */
  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = &$Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Users', T('Group2Role'), 'settings/group2role', 'Garden.Settings.Manage');
  }

  /**
    * Settings page.
    */
  public function SettingsController_Group2Role_Create($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $this->ShowSettingsPage($Sender, false);
  }

  public function SettingsController_Group2RoleApply_Create($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $Form = Gdn::Controller()->Form->FormValues();

    //Search the role and check that exists
    $RoleID = GetValue('Plugin.Role2Group.RoleID', $Form);
    $RoleModel = new RoleModel();
    $Role = $RoleModel->GetByRoleID($RoleID);
    if(!$Role){
      $this->ShowSettingsPage($Sender, true, T("The selected role doesn't exists"));
    }
    //Search the group and check that exists
    $GroupID = GetValue('Plugin.Role2Group.GroupID', $Form);
    $Group = $this->GetGroupById($GroupID);
    if(!$Group){
      $this->ShowSettingsPage($Sender, true, T("The selected group doesn't exists"));
    }
    //get users in the group
    $GroupMembers = Gdn::SQL()->Select('us.UserID, us.Name')
                              ->Select('ug.GroupID')
                              ->OrderBy('us.UserID', 'asc')
                              ->Select('gr.Name', '', 'GroupName')
                              ->From('User us')
                              ->Where('us.Deleted', 0)
                              ->Where('gr.GroupID', $GroupID)
                              ->Join('UserGroup ug', 'us.UserID = ug.UserID', 'left')
                              ->Join('Group gr', 'ug.GroupID = gr.GroupID', 'left')
                              ->Get();
    //add the role to every user in the group using sql
    $UserAffected = 0;
    $UserYetWithRole = 0;
    $UserAffectedNames = array();
    foreach ($GroupMembers->Result() as $User) {
      $CurrentRoles = Gdn::UserModel()->GetRoles($User->UserID)->Result();
      $RolesNames = array_map(array($this, "RoleName"), $CurrentRoles);
      if(!in_array($Role->Name, $RolesNames)){
        array_push($RolesNames, $Role->Name);
        $UserAffected++;
        array_push($UserAffectedNames, $User->Name);
        Gdn::UserModel()->SaveRoles($User->UserID, implode(",", $RolesNames));
      }else{
        $UserYetWithRole++;
      }
    }

    $Message = $Role->Name . " role was given to " . $UserAffected . " users in the ". $Group->Name ." group.";
    $Message .= " " . $UserYetWithRole . " present yet.";
    $Message .= " Affected users:[ " . implode(",", $UserAffectedNames) . " ]";
    $this->ShowSettingsPage($Sender, false, $Message);
  }

  private function RoleName($Role){
    return $Role['Name'];
  }

  private function ShowSettingsPage($Sender, $Error=false, $Message){
    //to have the side menu with the current page selected
    $Sender->AddSideMenu('settings/group2role');
    $Sender->SetData('Title', T('Group2Role'));
    
    //Get groups data
    $Sender->Groups = Gdn::SQL()->Select('g.GroupID', '', 'value')
                        ->Select('g.Name', '', 'text')
                        ->From('Group g')
                        ->Get();

    //Get roles data
    $Sender->Roles = Gdn::SQL()->Select('r.RoleID', '', 'value')
                        ->Select('r.Name', '', 'text')
                        ->From('Role r')
                        ->Get();

    if($Message && $Error){
      $Sender->ErrorMessage = $Message;
    }elseif ($Message && !$Error) {
      $Sender->Message = $Message;
    }

    $this->RenderView($Sender, 'group2role');
  }

   private function GetGroupById($GroupID){
    return Gdn::SQL()->Select('g.GroupID', '', 'GroupID')
                      ->Select('g.Name', '', 'Name')
                      ->From('Group g')
                      ->Where('g.GroupID', $GroupID)
                      ->Get()
                      ->FirstRow();
   }

  private function GetGroups(){
    return Gdn::SQL()->Select('g.GroupID', '', 'GroupID')
                      ->Select('g.Name', '', 'Name')
                      ->From('Group g')
                      ->Get()
                      ->Result();
  }

  private function RenderView($Sender, $View){
    $Sender->View = dirname(__FILE__) . DS . 'views' . DS . $View . '.php';
    $Sender->Render();
  }
  
  public function Setup() {}
}