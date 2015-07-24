<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['HiddenRoles'] = array(
   'Name' => 'Hidden Roles',
   'Description' => 'Allows selected roles from being displayed in profile, and elsewhere, essentially assigning anonymous permissions.',
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'Version' => '0.1.7b',
   'Author' => "Paul Thomas",
   'AuthorEmail' => 'dt01pqt_pt@yahoo.com',
   'AuthorUrl' => 'http://vanillaforums.org/profile/x00'
);



class HiddenRoles extends Gdn_Plugin {

    protected $PublicRoles = array();
    protected $AllRoles = array();

    public function GetPublicRoles($UserID,$Roles){
        if(!empty($this->PublicRoles) && GetValue($UserID,$this->PublicRoles))
            return $this->PublicRoles[$UserID];
        
        $this->PublicRoles[$UserID] = array(); 
        if(empty($this->AllRoles)){
            $RoleModel = new RoleModel();
            $this->AllRoles = $RoleModel->Get()->Result();
        }
        foreach($this->AllRoles As $Role){
            
            if(!GetValue('Hidden',$Role) && in_array(GetValue('Name',$Role),$Roles)){
                $this->PublicRoles[$UserID][] = GetValue('Name',$Role);
            }
        }
        if(empty($this->PublicRoles[$UserID]))
            $this->PublicRoles[$UserID][] = T('Hidden');
        return $this->PublicRoles[$UserID];
    }

    public function ProfileController_Render_Before($Sender){
        if(GetValue('Roles',$Sender))
            $Sender->Roles = $this->GetPublicRoles($Sender->User->UserID,$Sender->Roles);
    }

    public function Base_BeforeInfo_Handler($Sender){
        if(GetValue('Roles',$Sender))
            $Sender->Roles = $this->GetPublicRoles($Sender->User->UserID,$Sender->Roles);
    }

    public function Base_BeforeAddModule_Handler($Sender){
        if(GetValue('Roles',$Sender))
            $Sender->Roles = $this->GetPublicRoles($Sender->User->UserID,$Sender->Roles);
    }
    
    public function ProfileController_AfterUserInfo_Handler($Sender){

    }

    //RoleTitle Plugin Compatibility
    public function Base_BeforeDiscussionDisplay_Handler($Sender){
        $this->Base_BeforeCommentDisplay_Handler($Sender);
    }
    
    public function Base_BeforeCommentDisplay_Handler($Sender){
        $Object = $Sender->EventArguments['Object'];
        $Roles = $Object && GetValueR('User.Roles', Gdn::Session()) ? TRUE : FALSE;
            if (!$Roles)
             return;
        
        $Object->Roles = $this->GetPublicRoles($Object->InsertUserID,$Object->Roles);
    }



    public function HiddenRolesJson(){
        $Roles = array();
        $RoleModel = new RoleModel();
        $AllRoles = $RoleModel ->Get();
        foreach($AllRoles As $Role)
            if($Role->RoleID>0){
                $Roles[$Role->RoleID]=array('hidden'=>$Role->Hidden,'name'=>$Role->HiddenName, 'id'=>$Role->Name);
            }
            
        return json_encode($Roles);
    }

    public function SettingsController_HiddenRole_Create($Sender,$Args){
        $Sender->Permission('Garden.Settings.Manage');
        $Id = $Args[0];
        $On = $Args[1]?1:0;
        if(!ctype_digit($Id) || $Id<1){
            die(json_encode(FALSE));
        }
        
        
        
        $Column='Hidden';
        
        $Role = Gdn::SQL()->Select('r.*')->From('Role r')->Where('RoleID',$Id)->Get()->FirstRow();
        $Name = $Role->Name;
        $HiddenName = $Role->HiddenName;
        if($On){
            $Name = 'hr' . rand(0,99999) . time();
            $HiddenName = $Role->Name;
        }else{
            if($Role->Hidden && $Role->HiddenName){
                $Name = $Role->HiddenName;
                $HiddenName = $Role->Name;
            }
        }
        Gdn::SQL()->Put('Role',array($Column=>$On, 'Name'=> $Name, 'HiddenName' => $HiddenName),array('RoleID'=>$Id));
        
        die(json_encode(TRUE));
    }

    public function RoleController_Render_Before($Sender){
        switch(strtolower($Sender->RequestMethod)){
            case 'add':
            case 'edit':
                $Sender->AddDefinition('Hidden',T('Hide Role from being displayed with profile'));
                $Sender->AddJsFile('hiddenrole.js','plugins/HiddenRoles');
                $Sender->AddDefinition('IsHidden',GetValueR('Role.Hidden',$Sender,0));
                $Sender->AddDefinition('HiddenName',GetValueR('Role.HiddenName',$Sender,0));
                break;
            case 'index':
                $Sender->AddDefinition('Hidden',T('Hidden'));
                $Sender->AddJsFile('hiddenroles.js','plugins/HiddenRoles');
                $Sender->AddDefinition('HiddenRoles',$this->HiddenRolesJson());
                break;
        }
        
        $Sender->AddDefinition('Hide','Caution! Are you sure you want to hide this role?');
        $Sender->AddDefinition('Unhide','Caution! Are you sure you want to unhide this role?');
        $Sender->AddDefinition('Remember','Remember you still have to save the role to apply.');
    }

    public function UserController_Render_Before($Sender){
        switch(strtolower($Sender->RequestMethod)){
            case 'add':
            case 'edit':
            case 'browse':
            case 'index':
                $Sender->AddDefinition('Hidden',T('Hidden'));
                $Sender->AddJsFile('hiddenrolelist.js','plugins/HiddenRoles');
                $Sender->AddDefinition('HiddenRoles',$this->HiddenRolesJson());
                break;
        }
        
    }

    public function SettingsController_Render_Before($Sender){
        switch(strtolower($Sender->RequestMethod)){
            case 'addcategory':
            case 'editcategory':
                $Sender->AddDefinition('Hidden',T('Hidden'));
                $Sender->AddJsFile('hiddenrolelist.js','plugins/HiddenRoles');
                $Sender->AddDefinition('HiddenRoles',$this->HiddenRolesJson());
                break;
        }
    }

    public function Base_BeforeDispatch_Handler($Sender){
        if(C('Plugins.HiddenRoles.Version')!=$this->PluginInfo['Version'])
            $this->Structure();
    }

    public function Setup() {
        $this->Structure();
    }

    public function Structure(){
        Gdn::Structure()
        ->Table('Role')
        ->Column('Hidden','int(4)',0)
        ->Column('HiddenName','varchar(100)',0)
        ->Set();
        SaveToConfig('Plugins.HiddenRoles.Version', $this->PluginInfo['Version']);
    }
}

