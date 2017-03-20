<?php if (!defined('APPLICATION')) exit();

/* ===================================
CHANGELOG
23 Jan 2010
[!] beta release
23 Jan 2010
[x] fixed bug for plugins/hooks that uses Gdn_UserModel_[EventName]
23 Jan 2010
[*] removed duplicated object ($SimpleModel)
[*] CustomUserModel_SessionQuery -> Base_SessionQuery
30 Mar 2010
[=] AfterGetSession added to the core (CustomUserModel no need anymore)
[=] extending RoleController instead of Profile
[=] removed popup

TODO: add sidemenu to profile page

*/

$PluginInfo['UserPermissions'] = array(
	'Name' => 'User Permissions',
	'Description' => 'Allows to set custom global permissions for user, ignoring the permissions of user\'s roles.',
	'Version' => '1.0.7',
	'Date' => '30 Dec 2010',
	'Author' => 'Grandfather Frost'
);

class UserPermissions implements Gdn_IPlugin {
	
	public function ProfileController_AfterAddSideMenu_Handler(&$Profile) {
		if(Gdn::Session()->CheckPermission('Garden.Roles.Manage') != False){
			$SideMenu =& $Profile->EventArguments['SideMenu'];
			$Url = '/role/custompermissions/'.$Profile->User->UserID;
			$SideMenu->AddLink('Options', Gdn::Translate('Permissions'), $Url);
		}
	}
	
	public function Base_AfterGetSession_Handler(&$Sender) {
		$User =& $Sender->EventArguments['User'];
		$this->ChangeUserPermissions($User);
	}
	
	public function Base_SessionQuery_Handler(&$Sender) {
		$Sender->SQL->Select('u.CustomPermissions');
	}
	
	private function ChangeUserPermissions(&$User) {

		if($User == False) return;

		$CustomPermissions = Gdn_Format::Unserialize($User->CustomPermissions);
		$Permissions = Gdn_Format::Unserialize($User->Permissions);

		if(is_array($Permissions) && is_array($CustomPermissions)){
			foreach($Permissions as $N => $Name){
				// clear global role permissions
				if(is_numeric($N) && is_string($Name)) unset($Permissions[$N]);
			}
			// set custom
			$Permissions = array_merge($CustomPermissions, $Permissions);
			$User->Permissions = $Permissions;
		}
	}
	
	public function RoleController_CustomPermissions_Create(&$Controller) {
		
		$Controller->Permission('Garden.Roles.Manage');
		$UserID = ArrayValue(0, $Controller->RequestArgs);
		$Form =& $Controller->Form;
		
	
		if($Controller->Form->AuthenticatedPostBack() != False){
			$FormValues = $Controller->Form->FormValues();
			$UserPermissions = $Form->GetFormValue('UserPermissions', '');
			if($Form->ButtonExists('Reset')) $UserPermissions = False;
			$Saved = $this->SaveUserPermissions($UserID, $UserPermissions);
			if($Saved != False){
				if($Controller->DeliveryType() == DELIVERY_TYPE_ALL) Redirect($Controller->SelfUrl);
				/*$Controller->DeliveryType(DELIVERY_TYPE_BOOL);
				$Controller->StatusMessage = Gdn::Translate('Your changes have been saved successfully.');
				$Controller->Render();
				return;*/
			}

		}
		
		$UserModel = Gdn::UserModel();
		$User = $UserModel->Get($UserID);
		if($User == False) Redirect($Controller->Routes['Default404']);
		$RoleData = $UserModel->GetRoles($UserID);
		
		$RoleIDs = ConsolidateArrayValuesByKey($RoleData->ResultArray(), 'RoleID');
		
		$PermissionModel = Gdn::PermissionModel();
		$Permissions = $PermissionModel->GetGlobalPermissions(0);
		
		$UserPermissions = Gdn_Format::Unserialize($User->CustomPermissions);
		if(!is_array($UserPermissions)) $UserPermissions = array();

		foreach($UserPermissions as $Name) if(is_string($Name)) $Permissions[$Name] = '1';
		$Permissions = RemoveKeyFromArray($Permissions, 'Garden.SignIn.Allow');

		$Permissions = array($Permissions);
		$PermissionData = $PermissionModel->UnpivotPermissions($Permissions);

		$Controller->SetData('PermissionData', $PermissionData, True);
		$Controller->SetData('User', $User, True);
		
		$Controller->AddSideMenu('');
		$Controller->Render(dirname(__FILE__).DS.'view.custompermissions.php');
	
	}
	
	private function SaveUserPermissions($UserID, $PermissionData = False) {
		$UserModel = new Gdn_Model('User');
		// savetoserializedcolumn merging with old values, so need empty this field
		$Result = $UserModel->SQL
			->Update('User')
			->Set('CustomPermissions', Null)
			->Where('UserID', $UserID)
			->Limit(1)
			->Put();
		// and save custom permissions
		if ($PermissionData != False) {
			$Result = $UserModel->SaveToSerializedColumn('CustomPermissions', $UserID, $PermissionData);
		}
		return $Result;
	}
	
	public function Setup() {
		Gdn::Structure()
			->Table('User')
			->Column('CustomPermissions', 'text', True)
			->Set(False, False);
	}

}