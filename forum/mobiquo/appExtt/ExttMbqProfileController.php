<?php 

/**
 * ExttMbqProfileController extended from ProfileController
 * added method exttPassword() modified from method Password().
 * added method exttEdit() modified from method Edit().
 * added method __construct(),Initialize().
 * 
 * @since  2013-10-24
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqProfileController extends ProfileController {
   
   /**
    * Prep properties.
    *
    * @since 2.0.0
    * @access public
    */
   public function __construct() {
      parent::__construct();
   }
   
   /**
    * Adds JS, CSS, & modules. Automatically run on every use.
    *
    * @since 2.0.0
    * @access public
    */
   public function Initialize() {
      parent::Initialize();
   }
   
   /**
    * Edit user account.
    *
    * @since 2.0.0
    * @access public
    * @param mixed $UserReference Username or User ID.
    */
   /**
     * update email
     * return array(
        'updateStatus' => 'updateOk',   //updateOk means update email ok,updateFail means update email failed,needParams means need valid params
        'updateMessage' => '...'  //optional,update email message for display
     )
     * 
     * @param  Array  $data  user data
     * @return  Array
    */
   public function exttEdit($data, $retData) {
      $UserReference = $Username = '';
      $this->Permission('Garden.SignIn.Allow');
      
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqUserModel.php');
      $this->Form = $this->Form ? $this->Form : new Gdn_Form(); //wztmdf 20131026
      //$this->UserModel = $this->UserModel ? $this->UserModel : new UserModel();     //wztmdf 20131026
      $this->UserModel = new ExttMbqUserModel();     //wztmdf 20131026
      $this->Form->SetModel($this->UserModel);
      
      $this->GetUserInfo($UserReference, $Username, '', TRUE);
      $Session = Gdn::Session();
      
      // Check the password.
      $tempUser = Gdn::UserModel()->GetByUsername(MbqMain::$oCurMbqEtUser->loginName->oriValue);
      $PasswordHash = new Gdn_PasswordHash();
      if ($Session->UserID && $PasswordHash->CheckPassword($data['password'], GetValue('Password', $tempUser), GetValue('HashMethod', $tempUser))) {
      } else {
        $retData['updateStatus'] = 'updateFail';
        $retData['updateMessage'] = 'Invalid user or password.';
        return $retData;
      }
      
      // Decide if they have ability to edit the username
      $this->CanEditUsername = Gdn::Config("Garden.Profile.EditUsernames");
      $this->CanEditUsername = $this->CanEditUsername || $Session->CheckPermission('Garden.Users.Edit');
         
      $UserModel = Gdn::UserModel();
      $User = $UserModel->GetID($this->User->UserID);
      //$this->Form->SetModel($UserModel);
      $this->Form->AddHidden('UserID', $this->User->UserID);
      
      // Define gender dropdown options
      $this->GenderOptions = array(
         'm' => T('Male'),
         'f' => T('Female')
      );
      
      $mbqExttFormValues = array(
        'TransientKey' => '',
        'hpt' => '', 
        'UserID' => MbqMain::$oCurMbqEtUser->userId->oriValue,
        'Name' => MbqMain::$oCurMbqEtUser->loginName->oriValue,
        'Email' => $data['new_email'],
        'Gender' => MbqMain::$oCurMbqEtUser->mbqBind['oStdUser']->Gender,
        'Save' => 'Save',
        'ShowEmail' => MbqMain::$oCurMbqEtUser->mbqBind['oStdUser']->ShowEmail
      );
      foreach ($mbqExttFormValues as $k => $v) {
         $this->Form->SetFormValue($k, $v);
      }
      // If seeing the form for the first time...
      //if ($this->Form->AuthenticatedPostBack() === FALSE) {
         // Get the user data for the requested $UserID and put it into the form.
         //$this->Form->SetData($this->User);
      //} else {
         if (!$this->CanEditUsername)
            $this->Form->SetFormValue("Name", $User->Name);
         else {
            $UsernameError = T('UsernameError', 'Username can only contain letters, numbers, underscores, and must be between 3 and 20 characters long.');
            $UserModel->Validation->ApplyRule('Name', 'Username', $UsernameError);
         }
         if ($this->Form->Save() !== FALSE) {
            $User = $UserModel->GetID($this->User->UserID);
            //$this->InformMessage('<span class="InformSprite Check"></span>'.T('Your changes have been saved.'), 'Dismissable AutoDismiss HasSprite');
            //$this->RedirectUrl = Url('/profile/'.$this->ProfileUrl($User->Name));
            $retData['updateStatus'] = 'updateOk';
            return $retData;
         } else {
            $retData['updateStatus'] = 'updateFail';
            $retData['updateMessage'] = 'Update email failed.';
            return $retData;
         }
      //}
      
      return $retData;
      //$this->Render();
   } 
   
   /**
    * Set new password for current user.
    *
    * @since 2.0.0
    * @access public
    */
   /**
     * update password
     * return array(
        'updateStatus' => 'updateOk',   //updateOk means update password ok,updateFail means update password failed,needParams means need valid params
        'updateMessage' => '...'  //optional,update password message for display
     )
     * 
     * @param  Array  $data  user data
     * @return  Array
    */
   public function exttPassword($data, $retData) {
      $this->Permission('Garden.SignIn.Allow');
      
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqUserModel.php');
      $this->Form = $this->Form ? $this->Form : new Gdn_Form(); //wztmdf 20131025
      //$this->UserModel = $this->UserModel ? $this->UserModel : new UserModel();     //wztmdf 20131025
      $this->UserModel = new ExttMbqUserModel();     //wztmdf 20131025
      $this->Form->SetModel($this->UserModel);
      
      // Get user data and set up form
      $this->GetUserInfo();
      
      $this->Form->SetModel($this->UserModel);
      $this->Form->AddHidden('UserID', $this->User->UserID);
      
      $mbqExttFormValues = array(
        'TransientKey' => '',
        'hpt' => '',
        'UserID' => $this->User->UserID,
        'OldPassword' => $data['old_password'],
        'Password' => $data['new_password'],
        'PasswordMatch' => $data['new_password'],
        'Change_Password' => 'Change Password'
      );
      foreach ($mbqExttFormValues as $k => $v) {
         $this->Form->SetFormValue($k, $v);
      }
      //if ($this->Form->AuthenticatedPostBack() === TRUE) {
         $this->UserModel->DefineSchema();
//         $this->UserModel->Validation->AddValidationField('OldPassword', $this->Form->FormValues());
         
         // No password may have been set if they have only signed in with a connect plugin
         if (!$this->User->HashMethod || $this->User->HashMethod == "Vanilla") {
            if ($data['case'] != 'updatePasswordByTapatalkId') {
   	      $this->UserModel->Validation->ApplyRule('OldPassword', 'Required');
   	      $this->UserModel->Validation->ApplyRule('OldPassword', 'OldPassword', 'Your old password was incorrect.');
   	        }
         }
         
         $this->UserModel->Validation->ApplyRule('Password', 'Required');
         $this->UserModel->Validation->ApplyRule('Password', 'Match');
         
         if ($this->Form->Save()) {
				//$this->InformMessage('<span class="InformSprite Check"></span>'.T('Your password has been changed.'), 'Dismissable AutoDismiss HasSprite');
            //$this->Form->ClearInputs();
            $retData['updateStatus'] = 'updateOk';
            return $retData;
         } else {
            $retData['updateStatus'] = 'updateFail';
            $retData['updateMessage'] = 'Update password failed.';
            return $retData;
         }
      //}
      return $retData;
      //$this->Render();
   }
   
}

?>