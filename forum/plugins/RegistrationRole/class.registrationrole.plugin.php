<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['RegistrationRole'] = array(
   'Name' => 'Registration Role',
   'Description' => 'This plugin allows users to select a role during registration.',
   'Version' => '0.2',
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'RequiredTheme' => FALSE, 
   'SettingsUrl' => 'settings/registrationrole',
   'SettingsPermission' => 'Garden.Settings.Manage',
   'RegisterPermissions' => FALSE,
   'Author' => "Alessandro Miliucci",
   'AuthorEmail' => 'lifeisfoo@gmail.com',
   'AuthorUrl' => 'http://forkwait.net'
);

class RegistrationRolePlugin extends Gdn_Plugin {
 
  /**
   * Display a link in the dashboard side panel
   */  
  public function Base_GetAppSettingsMenuItems_Handler($Sender) {    
    $LinkText = T('Registration Role');
    $Menu = $Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Users', $LinkText, 'settings/registrationrole', 'Garden.Settings.Manage');
  }

  /**
   * Generate data to be displayed in the plugin's settings page
   */
  public function SettingsController_RegistrationRole_Create($Sender) {
    $Sender->Permission('Garden.Plugins.Manage');
    $Sender->AddSideMenu();
    $Sender->Title('Select roles that can be selected by users at registration');
    $ConfigurationModule = new ConfigurationModule($Sender);
    $ConfigurationModule->RenderAll = True;
    $DynamicSchema = array();//Plugin.RegistrationRole.RemoveMemberRole
    $DynamicSchema = array_merge($DynamicSchema, array(
          'Plugins.RegistrationRole.RemoveMemberRole' => array(
            'LabelCode' => 'Remove "Member" role from new users roles', 
            'Control' => 'CheckBox', 
            'Default' => C('Plugins.RegistrationRole.RemoveMemberRole', '')
          )
        ));
    $RoleModel = new RoleModel();
    foreach($RoleModel->Get() as $Role){
      $RoleName = $Role->Name;
      if ($RoleName != 'Administrator') {
        $RoleNameNoSpace = self::normalizeName($RoleName);
        $SingleRoleSchema = array(
          'Plugins.RegistrationRole.'.$RoleNameNoSpace => array(
            'LabelCode' => $RoleName, 
            'Control' => 'CheckBox', 
            'Default' => C('Plugins.RegistrationRole'.$RoleNameNoSpace, '')
          )
        );
      $DynamicSchema = array_merge($DynamicSchema, $SingleRoleSchema);
      }
    }
    
    $Schema = $DynamicSchema;
    $ConfigurationModule->Schema($Schema);
    $ConfigurationModule->Initialize();
    $Sender->View = dirname(__FILE__) . DS . 'views' . DS . 'settings.php';
    $Sender->ConfigurationModule = $ConfigurationModule;
    $Sender->Render();
  }
  
  /**
   * Replace all whitespaces in the string with underscores( vanilla conf keys doesn't support whitespaces)
   */
  private static function normalizeName($CatName){
    return str_replace(" ", "_", $CatName);
  }

  /**
   * Replace underscores with whitespaces
   */
  private static function denormalizeName($CatName){
    return str_replace("_", " ", $CatName);
  }

  /**
   * Return selected registration roles
   */
  private static function registrationRolesNames() {
    $RoleNames = array();
    foreach (C('Plugins.RegistrationRole') as $Key => $Value) {
      if( strcmp(C('Plugins.RegistrationRole.' . $Key, '0'), '1') == 0 ){
        array_push($RoleNames, self::denormalizeName($Key));
      }
    }
    return $RoleNames;
  }

  /**
   * Return available registration roles (dropdown compatible)
   */
  private static function availableRolesDropdown() {
    $RoleNames = self::registrationRolesNames();
    $Roles= Gdn::SQL()->Select('r.RoleID', '', 'value')
              ->Select('r.Name', '', 'text')
              ->From('Role r')
              ->WhereIn('r.Name', $RoleNames)
              ->Get();
    return $Roles;
  }

  /**
   * Return available roles for internal plugin use
   */
  private static function availableRoles() {
    $RoleNames = self::registrationRolesNames();
    $RolesDataArray = Gdn::SQL()->Select('r.RoleID, r.Name')
                        ->From('Role r')
                        ->WhereIn('r.Name', $RoleNames)
                        ->Get()->Result(DATASET_TYPE_ARRAY);
    return new Gdn_DataSet($RolesDataArray);
  }

  /**
   * Replaces registration pages with custom pages (with role selector)
   */
  public function EntryController_Render_Before($Sender) {
    $Sender->RegistrationRoles = self::availableRolesDropdown();
    if (strtolower($Sender->RequestMethod) == 'register' 
        || strtolower($Sender->RequestMethod) == 'connect'){//only on registration/connect page
      $RegistrationMethod = $Sender->View;
      switch ($RegistrationMethod) {
        case 'RegisterCaptcha':
          $Sender->View=$this->GetView('registercaptcha.php');
          break;
        case 'RegisterApproval':
          $Sender->View=$this->GetView('registerapproval.php');
          break;
        case 'RegisterInvitation':
          $Sender->View=$this->GetView('registerinvitation.php');
          break;
        case 'connect':
          $Sender->View=$this->GetView('connect.php');
          break;
        default:
          break;
      }
    }
  }

  /**
   * Save selected role
   */
  public function UserModel_AfterInsertUser_Handler($Sender) {
    if (!(Gdn::Controller() instanceof Gdn_Controller)) return;

    $FormPostValues = Gdn::Controller()->Form->FormValues();
	$RoleID = GetValue('Plugin.RegistrationRole.RoleID', $FormPostValues);
	$UserID = GetValue('InsertUserID', $Sender->EventArguments);

	//Save te selected registration role in the custom table
	Gdn::SQL()->Insert('RegistrationRole', 
        array('UserID' => $UserID, 
              'RoleID' => $RoleID
            )
		);

    if( (bool)C('Plugins.RegistrationRole.RemoveMemberRole', '0') ){
    	//remove member role if present
		$CurrentRoles = Gdn::UserModel()->GetRoles($UserID);
		$RolesToSaveArray = array();
		foreach ($CurrentRoles as $ARole) {
			$RoleName = GetValue('Name', $ARole);
			//remove member role from default roles (if present and if setting's selected)
			if( strcmp(trim($RoleName),'Member') != 0){
				array_push($RolesToSaveArray, $RoleName);
			}
		}
		//SaveRoles expect a string like "Moderator, Member, ..." see class.usermodel.php
    	Gdn::UserModel()->SaveRoles($UserID, implode(",", $RolesToSaveArray));
    }
  }

  /**
   * Add selected role after at email confirmation
   */
  public function UserModel_BeforeConfirmEmail_Handler($Sender) {
    $UserID = $Sender->EventArguments['ConfirmUserID'];

	$RegRoles = Gdn::SQL()->Select('*')
			->From('RegistrationRole rr')
			->Where('rr.UserID', Gdn::Session()->UserID)
			->Get()
			->Result();

    $SelectedRoles = array();//normally is only one, future-aware
    foreach ($RegRoles as $Role) {
      array_push($SelectedRoles, $Role->RoleID);
    }

    $AvailableRolesIds = array();
    foreach (self::availableRoles() as $Role) {
      array_push($AvailableRolesIds, $Role['RoleID']);
    }
    //Search for user roles contained in the availableRoles
    //then push all roles to the ConfirmUserRoles array (passed by reference)
    //security check->get only available roles
    $Roles = array_intersect($SelectedRoles, $AvailableRolesIds);
    //if member role needs to be removed
    if( strcmp(C('Plugins.RegistrationRole.RemoveMemberRole', '0'), '1') ==  0 ){
      $RoleToRemoveID = null;
      $RoleModel = new RoleModel();
      foreach ($Sender->EventArguments['ConfirmUserRoles'] as $RoleID) {
        $Role = $RoleModel->GetByRoleID($RoleID);
        if( strcmp(GetValue('Name', $Role), 'Member') == 0){
          $RoleToRemoveID = GetValue('RoleID', $Role);
        }
      }
      if($RoleToRemoveID){
        for ($i=0; $i < sizeof($Sender->EventArguments['ConfirmUserRoles']); $i++) { 
          if($Sender->EventArguments['ConfirmUserRoles'][$i] == $RoleToRemoveID){
            unset($Sender->EventArguments['ConfirmUserRoles'][$i]);
          }
        }
      }
    }
    $Sender->EventArguments['ConfirmUserRoles'] = array_merge($Sender->EventArguments['ConfirmUserRoles'], $Roles);

  }

  public function Setup() {
  	Gdn::Structure()
      ->Table('RegistrationRole')
      ->Column('UserID', 'int(11)', FALSE, 'primary')
      ->Column('RoleID', 'int(11)', FALSE, 'primary')
      ->Set(FALSE, FALSE);
  }
   
  public function OnDisable() {}

}