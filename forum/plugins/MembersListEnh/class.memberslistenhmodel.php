<?php if(!defined('APPLICATION')) exit();

class MembersListEnhModel extends VanillaModel{
 
public function GetMembersListEnh($Limit=5, $Offset=0, $SortOrder, $UserField){


$RoleAction = $RoleActionID = "";
$RemoveRoleArray = C('Plugins.MembersListEnh.RoleID');
if (isset($RemoveRoleArray)) {
      $RoleAction =   $RemoveRoleArray[0];
      $RoleActionID = $RemoveRoleArray[1];
}

    
     $MembersListEnhModel = new Gdn_Model('User');
        $SQL = $MembersListEnhModel->SQL
        ->Select('*')
        ->From('User u')
        ->LeftJoin('UserRole ur', 'u.UserID = ur.UserID'); 
        if (C('EnabledPlugins.KarmaBank') == TRUE)
           $SQL->LeftJoin('KarmaBankBalance kb', 'u.UserID = kb.UserID');
        if (($UserField != "Balance"))
           $SQL->OrderBy("u.$UserField",$SortOrder);
        if ((C('EnabledPlugins.KarmaBank') == TRUE) && ($UserField == "Balance")) 
            $SQL->OrderBy("kb.Balance",$SortOrder);
        $SQL->Where('Deleted',false);
        if ($RoleAction == "Exclude")
           $SQL->Where('ur.RoleID<>',$RoleActionID); 
        if ($RoleAction == "Include")
            $SQL->Where('ur.RoleID',$RoleActionID);  
            // Only show user once if in more than one role.
            $SQL->GroupBy('ur.UserID');
    
        $Sender->UserData = $SQL->Limit($Limit, $Offset)->Get();
       RoleModel::SetUserRoles($Sender->UserData->Result());
       return $Sender->UserData;
}    
    
    
    
    
     
public function GetMembersCount(){
    
$RoleAction = $RoleActionID = "";
$RemoveRoleArray = C('Plugins.MembersListEnh.RoleID');
if (isset($RemoveRoleArray)) {
      $RoleAction =   $RemoveRoleArray[0];
      $RoleActionID = $RemoveRoleArray[1];
}
    
     $MembersListEnhModel = new Gdn_Model('User');
     $SQL = $MembersListEnhModel->SQL
      ->Select('u.UserID', 'count','UserCount')
        ->LeftJoin('UserRole ur', 'u.UserID = ur.UserID')
        ->From('User u')
        ->Where('Deleted',false);
        if ($RoleAction == "Exclude")
           $SQL->Where('ur.RoleID<>',$RoleActionID); 
        if ($RoleAction == "Include")
            $SQL->Where('ur.RoleID',$RoleActionID); 
       $result = $SQL->Get()->FirstRow()->UserCount;
       return $result;
     

    }

}



  
