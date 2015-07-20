<?php

/**
 * ExttMbqEntryController extended from EntryController
 * added method exttRegisterBasic() modified from method RegisterBasic().
 * added method exttPasswordRequest() modified from method PasswordRequest().
 * added method __construct(),Initialize().
 * modified method Target().
 * 
 * @since  2013-10-17
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqEntryController extends EntryController {
   
   /**
    * Setup error message & override MasterView for popups.
    * 
    * @since 2.0.0
    * @access public
    */
   public function  __construct() {
      parent::__construct();
   }
   
   /**
    * Include JS and CSS used by all methods.
    *
    * Always called by dispatcher before controller's requested method.
    * 
    * @since 2.0.0
    * @access public
    */
   public function Initialize() {
      parent::Initialize();
   }
   
   /**
    * Registration that requires approval.
    *
    * Events: RegistrationPending
    * 
    * @access private
    * @since 2.0.0
    */
   /**
     * register user
     * return array(
        'registerStatus' => 'registerOk',   //registerOk means register ok,registerFail means register failed,occupied means username was occupied,needParams means need valid registration info
        'newUserId' => '123',    //optional,new registed user id
        'registerMessage' => '...'  //optional,register message for display
     )
     * 
     * @param  Array  $data  user data
     * @param  Mixed  $profile  user profile(comes from tapatalk id)
     * @param  Array  $retData
     * @return  Array
    */
   public function exttRegisterApproval($data, $profile, $retData) {
      $retData['registerStatus'] = 'registerFail';  //!!! set default register status
      if ($data['email'] && $data['username'] && $data['password']) {
      } else {
         $retData['registerStatus'] = 'needParams';
         $retData['registerMessage'] = MBQ_ERR_INFO_NEED_PARAMS_FOR_REGISTRATION;
         return $retData;
      }
      //refer EntryController::Register()
      $this->FireEvent("Register");
      
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqUserModel.php');
      $this->Form = $this->Form ? $this->Form : new Gdn_Form(); //wztmdf 20131017
      //$this->UserModel = $this->UserModel ? $this->UserModel : new UserModel();     //wztmdf 20131017
      $this->UserModel = new ExttMbqUserModel();     //wztmdf 20131018
      $this->Form->SetModel($this->UserModel);

      // Define gender dropdown options
      $this->GenderOptions = array(
         'm' => T('Male'),
         'f' => T('Female')
      );

      // Make sure that the hour offset for new users gets defined when their account is created
      $this->AddJsFile('entry.js');
         
      $this->Form->AddHidden('ClientHour', date('Y-m-d H:00')); // Use the server's current hour as a default
      $this->Form->AddHidden('Target', $this->Target());

      //$RegistrationMethod = $this->_RegistrationView();
      $RegistrationMethod = 'RegisterApproval';    //!!!
      $this->View = $RegistrationMethod;
      //$this->$RegistrationMethod($InvitationCode);
      
      //refer EntryController::RegisterApproval()
      if ($profile['gender'] == 'female') {
        $mbqExttGender = 'f';
      } elseif ($profile['gender'] == 'male') {
        $mbqExttGender = 'm';
      } else {
        $mbqExttGender = 'm';
      }
      $mbqExttFormValues = array(
        'TransientKey' => '',
        'hpt' => '',
        'ClientHour' => date('Y-m-d H:00'),
        'Target' => '/discussions',
        'Email' => $data['email'],
        'Name' => $data['username'],
        'Password' => $data['password'],
        'PasswordMatch' => $data['password'],
        'Gender' => $mbqExttGender,
        'DiscoveryText' => 'like it',
        'TermsOfService' => 1,
        'Apply_for_Membership' => 'Apply for Membership'
      );
      foreach ($mbqExttFormValues as $k => $v) {
         $this->Form->SetFormValue($k, $v);
      }
      // If the form has been posted back...
      if ($this->Form->IsPostBack()) {
         // Add validation rules that are not enforced by the model
         $this->UserModel->DefineSchema();
         $this->UserModel->Validation->ApplyRule('Name', 'Username', $this->UsernameError);
         //$this->UserModel->Validation->ApplyRule('TermsOfService', 'Required', T('You must agree to the terms of service.'));
         $this->UserModel->Validation->ApplyRule('Password', 'Required');
         //$this->UserModel->Validation->ApplyRule('Password', 'Match');
         //$this->UserModel->Validation->ApplyRule('DiscoveryText', 'Required', 'Tell us why you want to join!');
         // $this->UserModel->Validation->ApplyRule('DateOfBirth', 'MinimumAge');

         try {
            $Values = $this->Form->FormValues();
            unset($Values['Roles']);
            $AuthUserID = $this->UserModel->Register($Values);  //!!! use ExttMbqUserModel
            if (!$AuthUserID) {
               $this->Form->SetValidationResults($this->UserModel->ValidationResults());
                $retData['registerStatus'] = 'registerFail';
                $retData['registerMessage'] = MBQ_ERR_INFO_REGISTRATION_FAIL;
                return $retData;
            } else {
               // The user has been created successfully, so sign in now.
               Gdn::Session()->Start($AuthUserID);

               if ($this->Form->GetFormValue('RememberMe'))
                  Gdn::Authenticator()->SetIdentity($AuthUserID, TRUE);

               $this->EventArguments['AuthUserID'] = $AuthUserID;
               $this->FireEvent('RegistrationPending');
               $this->View = "RegisterThanks"; // Tell the user their application will be reviewed by an administrator.
                $retData['registerStatus'] = 'registerOk';
                $retData['newUserId'] = $AuthUserID;
                return $retData;
            }
         } catch (Exception $Ex) {
            $this->Form->AddError($Ex);
            $retData['registerStatus'] = 'registerFail';
            $retData['registerMessage'] = MBQ_ERR_INFO_REGISTRATION_FAIL.$Ex->getMessage();
            return $retData;
         }
      }
      return $retData;
      //$this->Render();
   }
    
    /**
    * Basic/simple registration. Allows immediate access.
    *
    * Events: RegistrationSuccessful
    * 
    * @access private
    * @since 2.0.0
    */
   /**
     * register user
     * return array(
        'registerStatus' => 'registerOk',   //registerOk means register ok,registerFail means register failed,occupied means username was occupied,needParams means need valid registration info
        'newUserId' => '123',    //optional,new registed user id
        'registerMessage' => '...'  //optional,register message for display
     )
     * 
     * @param  Array  $data  user data
     * @param  Mixed  $profile  user profile(comes from tapatalk id)
     * @param  Array  $retData
     * @return  Array
    */
   public function exttRegisterBasic($data, $profile, $retData) {
      $retData['registerStatus'] = 'registerFail';  //!!! set default register status
      if ($data['email'] && $data['username'] && $data['password']) {
      } else {
         $retData['registerStatus'] = 'needParams';
         $retData['registerMessage'] = MBQ_ERR_INFO_NEED_PARAMS_FOR_REGISTRATION;
         return $retData;
      }
      //refer EntryController::Register()
      $this->FireEvent("Register");
      
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqUserModel.php');
      $this->Form = $this->Form ? $this->Form : new Gdn_Form(); //wztmdf 20131017
      //$this->UserModel = $this->UserModel ? $this->UserModel : new UserModel();     //wztmdf 20131017
      $this->UserModel = new ExttMbqUserModel();     //wztmdf 20131018
      $this->Form->SetModel($this->UserModel);

      // Define gender dropdown options
      $this->GenderOptions = array(
         'm' => T('Male'),
         'f' => T('Female')
      );

      // Make sure that the hour offset for new users gets defined when their account is created
      $this->AddJsFile('entry.js');
         
      $this->Form->AddHidden('ClientHour', date('Y-m-d H:00')); // Use the server's current hour as a default
      $this->Form->AddHidden('Target', $this->Target());

      $RegistrationMethod = $this->_RegistrationView();
     // $RegistrationMethod = 'RegisterBasic';    //!!!
      $this->View = $RegistrationMethod;
      //$this->$RegistrationMethod($InvitationCode);
      
      //refer EntryController::RegisterBasic()
      if ($profile['gender'] == 'female') {
        $mbqExttGender = 'f';
      } elseif ($profile['gender'] == 'male') {
        $mbqExttGender = 'm';
      } else {
        $mbqExttGender = 'm';
      }
      $mbqExttFormValues = array(
        'TransientKey' => '',
        'hpt' => '',
        'ClientHour' => date('Y-m-d H:00'),
        'Target' => '/discussions',
        'Email' => $data['email'],
        'Name' => $data['username'],
        'Password' => $data['password'],
        'PasswordMatch' => $data['password'],
        'Gender' => $mbqExttGender,
        'TermsOfService' => 1,
        'RememberMe' => 1,
        'Sign_Up' => 'Sign Up'
      );
      foreach ($mbqExttFormValues as $k => $v) {
         $this->Form->SetFormValue($k, $v);
      }
      if ($this->Form->IsPostBack() === TRUE) {
         // Add validation rules that are not enforced by the model
         $this->UserModel->DefineSchema();
         $this->UserModel->Validation->ApplyRule('Name', 'Username', $this->UsernameError);
         //$this->UserModel->Validation->ApplyRule('TermsOfService', 'Required', T('You must agree to the terms of service.'));
         $this->UserModel->Validation->ApplyRule('Password', 'Required');
         //$this->UserModel->Validation->ApplyRule('Password', 'Match');
         // $this->UserModel->Validation->ApplyRule('DateOfBirth', 'MinimumAge');

         try {
            $Values = $this->Form->FormValues();
            unset($Values['Roles']);
            $AuthUserID = $this->UserModel->Register($Values);  //!!! use ExttMbqUserModel
         
            if (!$AuthUserID) {
               $this->Form->SetValidationResults($this->UserModel->ValidationResults());
                $retData['registerStatus'] = 'registerFail';
                $retData['registerMessage'] = MBQ_ERR_INFO_REGISTRATION_FAIL;
                return $retData;
            } else {
               // The user has been created successfully, so sign in now.
               Gdn::Session()->Start($AuthUserID);

               if ($this->Form->GetFormValue('RememberMe'))
                  Gdn::Authenticator()->SetIdentity($AuthUserID, TRUE);

               try {
                  $this->UserModel->SendWelcomeEmail($AuthUserID, '', 'Register');
               } catch (Exception $Ex) {
               }

               $this->FireEvent('RegistrationSuccessful');
                $retData['registerStatus'] = 'registerOk';
                $retData['newUserId'] = $AuthUserID;
                return $retData;
               

               // ... and redirect them appropriately
               /*
               $Route = $this->RedirectTo();
               if ($this->_DeliveryType != DELIVERY_TYPE_ALL) {
                  $this->RedirectUrl = Url($Route);
               } else {
                  if ($Route !== FALSE)
                     Redirect($Route);
               }
               */
            }
         } catch (Exception $Ex) {
            $this->Form->AddError($Ex);
            $retData['registerStatus'] = 'registerFail';
            $retData['registerMessage'] = MBQ_ERR_INFO_REGISTRATION_FAIL.$Ex->getMessage();
            return $retData;
         }
      }
      return $retData;
      //$this->Render();
   }
   
   /**
    * Request password reset.
    *
    * @access public
    * @since 2.0.0
    */
   /**
     * forget_password
     * return array(
        'handleStatus' => 'handleOk',   //handleOk means handle ok,handleFail means handle failed,needParams means need valid params
        'handleMessage' => '...'  //optional,handle message for display
     )
     * 
     * @param  Array  $data  user data
     * @return  Array
    */
   public function exttPasswordRequest($data, $retData) {
      $this->Form = $this->Form ? $this->Form : new Gdn_Form(); //wztmdf 20131027
      $this->UserModel = $this->UserModel ? $this->UserModel : new UserModel();     //wztmdf 20131027
      $mbqExttFormValues = array(
        'TransientKey' => '',
        'hpt' => '',
        'Target' => 'discussions',
        'ClientHour' => date('Y-m-d H:i'),
        'Email' => $data['username'],
        'Request_a_new_password' => 'Request a new password'
      );
      foreach ($mbqExttFormValues as $k => $v) {
         $this->Form->SetFormValue($k, $v);
      }
      
      Gdn::Locale()->SetTranslation('Email', T('Email/Username'));
      if ($this->Form->IsPostBack() === TRUE) {
         $this->Form->ValidateRule('Email', 'ValidateRequired');

         if ($this->Form->ErrorCount() == 0) {
            try {
               $Email = $this->Form->GetFormValue('Email');
               if (!$this->UserModel->PasswordRequest($Email)) {
                  //$this->Form->AddError("Couldn't find an account associated with that email/username.");
                  $retData['handleStatus'] = 'handleFail';
                  $retData['handleMessage'] = "Couldn't find an account associated with that email/username.";
                  return $retData;
               }
            } catch (Exception $ex) {
               //$this->Form->AddError($ex->getMessage());
              $retData['handleStatus'] = 'handleFail';
              $retData['handleMessage'] = $ex->getMessage();
              return $retData;
            }
            if ($this->Form->ErrorCount() == 0) {
               //$this->Form->AddError('Success!');
               //$this->View = 'passwordrequestsent';
              $retData['handleStatus'] = 'handleOk';
              return $retData;
            }
         } else {
            $retData['handleStatus'] = 'handleFail';
            if ($this->Form->ErrorCount() == 0)
               //$this->Form->AddError("Couldn't find an account associated with that email/username.");
               $retData['handleMessage'] = "Couldn't find an account associated with that email/username.";
            $retData['handleMessage'] = 'Handle forget password failed.';
            return $retData;
         }
      }
      return $retData;
      //$this->Render();
   }
   
   /**
    * Set where to go after signin.
    *
    * @access public
    * @since 2.0.0
    *
    * @param string $Target Where we're requested to go to.
    * @return string URL to actually go to (validated & safe).
    */
   public function Target($Target = FALSE) {
      $Target = MbqMain::$oMbqAppEnv->rootUrl;
      if ($Target === FALSE) {
         $Target = $this->Form->GetFormValue('Target', FALSE);
         if (!$Target)
            $Target = $this->Request->Get('Target', '/');
      }
      
      // Make sure that the target is a valid url.
      if (!preg_match('`(^https?://)`', $Target)) {
         $Target = '/'.ltrim($Target, '/');
      } else {
         $MyHostname = parse_url(Gdn::Request()->Domain(),PHP_URL_HOST);
         $TargetHostname = parse_url($Target, PHP_URL_HOST);
         
         // Only allow external redirects to trusted domains.
         $TrustedDomains = C('Garden.TrustedDomains');
			if (!is_array($TrustedDomains))
				$TrustedDomains = array();
			
			// Add this domain to the trusted hosts
			$TrustedDomains[] = $MyHostname;
         $Sender->EventArguments['TrustedDomains'] = &$TrustedDomains;
         $this->FireEvent('BeforeTargetReturn');
			
			if (count($TrustedDomains) == 0) {
				// Only allow http redirects if they are to the same host name.
				if ($MyHostname != $TargetHostname)
					$Target = '';
			} else {
				// Loop the trusted domains looking for a match
				$Match = FALSE;
				foreach ($TrustedDomains as $TrustedDomain) {
					if (StringEndsWith($TargetHostname, $TrustedDomain, TRUE))
						$Match = TRUE;
				}
				if (!$Match)
					$Target = '';
			}
      }
      return $Target;
   }
   
}

?>