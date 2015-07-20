<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtUser');

/**
 * user read class
 * 
 * @since  2012-8-6
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtUser extends MbqBaseRdEtUser {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtUser, $pName, $mbqOpt = array()) {
        switch ($pName) {
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * judge is admin role
     *
     * @param  Object  $oMbqEtUser
     * @return  Boolean
     */
    public function isAdminRole($oMbqEtUser) {
        return ($oMbqEtUser->mbqBind['oStdUser']->Admin ? true : false);
    }
    
    /**
     * get user objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byUserIds' means get data by user ids.$var is the ids.
     * @mbqOpt['case'] = 'online' means get online user.
     * @return  Array
     */
    public function getObjsMbqEtUser($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byUserIds') {
            $userIds = $var;
            foreach ($var as $userId) {
                $objsStdUser[$userId] = Gdn::UserModel()->GetID($userId);
            }
            $objsMbqEtUser = array();
            foreach ($objsStdUser as $oStdUser) {
                $objsMbqEtUser[] = $this->initOMbqEtUser($oStdUser, array('case' => 'oStdUser'));
            }
            return $objsMbqEtUser;
        } elseif ($mbqOpt['case'] == 'online') {
            if (!MbqMain::$oMbqAppEnv->check3rdPluginEnabled('WhosOnline')) {
                return array();
            } else {
                require_once(MBQ_APPEXTENTION_PATH.'ExttMbqWhosOnlineModule.php');
                $oExttMbqWhosOnlineModule = new ExttMbqWhosOnlineModule();
                $oExttMbqWhosOnlineModule->GetData();
                $arr = $oExttMbqWhosOnlineModule->exttMbqGetUsers()->Result();
                $userIds = array();
                foreach ($arr as $v) {
                    $userIds[] = $v->UserID;
                }
                return $this->getObjsMbqEtUser($userIds, array('case' => 'byUserIds'));
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one user by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdUser' means init user by oStdUser.$var is oStdUser.
     * $mbqOpt['case'] = 'byUserId' means init user by user id.$var is user id.
     * $mbqOpt['case'] = 'byLoginName' means init user by login name.$var is login name.
     * $mbqOpt['case'] = 'byEmail' means init user by user email.$var is user email.
     * @return  Mixed
     */
    public function initOMbqEtUser($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdUser') {
            $oMbqEtUser = MbqMain::$oClk->newObj('MbqEtUser');
            $oMbqEtUser->userId->setOriValue($var->UserID);
            $oMbqEtUser->loginName->setOriValue($var->Name);
            $oMbqEtUser->userName->setOriValue($var->Name);
            $oMbqEtUser->userEmail->setOriValue($var->Email);
            if ($var->Photo) {
                $oMbqEtUser->iconUrl->setOriValue(MbqMain::$oMbqAppEnv->rootUrl.'uploads/'.ChangeBasename($var->Photo, 'n%s'));
            }
            $oMbqEtUser->canSearch->setOriValue(MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSearch.range.yes'));
            $oMbqEtUser->canPm->setOriValue(MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canPm.range.yes'));
            $oMbqEtUser->postCount->setOriValue($var->CountComments);
            $oMbqEtUser->displayText->setOriValue('Discussions '.$var->CountDiscussions.', Comments '.$var->CountComments);
            $oMbqEtUser->regTime->setOriValue(strtotime($var->DateFirstVisit));
            $oMbqEtUser->lastActivityTime->setOriValue(strtotime($var->DateLastActive));
            //$oMbqEtUser->isOnline->setOriValue($var['oKunenaUser']->isOnline() ? MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.isOnline.range.yes') : MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.isOnline.range.no'));
            //$oMbqEtUser->maxAttachment->setOriValue(MbqMain::$oMbqAppEnv->oKunenaConfig->attachment_limit);
            //$oMbqEtUser->maxPngSize->setOriValue(MbqMain::$oMbqAppEnv->oKunenaConfig->imagesize * 1024);
            //$oMbqEtUser->maxJpgSize->setOriValue(MbqMain::$oMbqAppEnv->oKunenaConfig->imagesize * 1024);
            $oMbqEtUser->mbqBind['oStdUser'] = $var;
            $oMbqEtUser->canWhosonline->setOriValue(MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canWhosonline.range.yes'));
            $oMbqEtUser->canSendPm->setOriValue(MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSendPm.range.yes'));
            return $oMbqEtUser;
        } elseif ($mbqOpt['case'] == 'byUserId') {
            $userIds = array($var);
            $objsMbqEtUser = $this->getObjsMbqEtUser($userIds, array('case' => 'byUserIds'));
            if (is_array($objsMbqEtUser) && (count($objsMbqEtUser) == 1)) {
                return $objsMbqEtUser[0];
            }
            return false;
        } elseif ($mbqOpt['case'] == 'byLoginName') {
            $oStdUser = Gdn::UserModel()->GetByUsername($var);
            if ($oStdUser) {
                return $this->initOMbqEtUser($oStdUser->UserID, array('case' => 'byUserId'));
            } else {
                return false;
            }
        } elseif ($mbqOpt['case'] == 'byEmail') {
            $oStdUser = Gdn::UserModel()->GetByEmail($var);
            if ($oStdUser) {
                return $this->initOMbqEtUser($oStdUser->UserID, array('case' => 'byUserId'));
            } else {
                return false;
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * get user display name
     *
     * @param  Object  $oMbqEtUser
     * @return  String
     */
    public function getDisplayName($oMbqEtUser) {
        return $oMbqEtUser->loginName->oriValue;
    }
    
    /**
     * login
     *
     * @param  String  $loginName
     * @param  String  $password
     * @return  Boolean  return true when login success.
     */
    public function login($loginName, $password) {
        $oEntryController = new EntryController();
        $oEntryController->Initialize();
        /* modified from EntryController::SignIn() */
        $oEntryController->FireEvent('SignIn');
        //$Email = $this->Form->GetFormValue('Email');
        $Email = $loginName;
        $User = Gdn::UserModel()->GetByEmail($Email);
        if (!$User)
           $User = Gdn::UserModel()->GetByUsername($Email);

        if (!$User) {
           //$this->Form->AddError('ErrorCredentials');
           return false;
        } else {
           //$ClientHour = $this->Form->GetFormValue('ClientHour');
           $ClientHour = date('Y-m-d H:i');
           $HourOffset = Gdn_Format::ToTimestamp($ClientHour) - time();
           $HourOffset = round($HourOffset / 3600);

           // Check the password.
           $PasswordHash = new Gdn_PasswordHash();
           //if ($PasswordHash->CheckPassword($this->Form->GetFormValue('Password'), GetValue('Password', $User), GetValue('HashMethod', $User))) {
           if ($PasswordHash->CheckPassword($password, GetValue('Password', $User), GetValue('HashMethod', $User))) {
              //Gdn::Session()->Start(GetValue('UserID', $User), TRUE, (bool)$this->Form->GetFormValue('RememberMe'));
              Gdn::Session()->Start(GetValue('UserID', $User), TRUE, TRUE);
              if (!Gdn::Session()->CheckPermission('Garden.SignIn.Allow')) {
                 //$this->Form->AddError('ErrorPermission');
                 Gdn::Session()->End();
                 return false;
              } else {
                 if ($HourOffset != Gdn::Session()->User->HourOffset) {
                    Gdn::UserModel()->SetProperty(Gdn::Session()->UserID, 'HourOffset', $HourOffset);
                 }
                 MbqMain::$oMbqAppEnv->oCurStdUser = $User;

                 //$this->_SetRedirect();
                 $this->initOCurMbqEtUser();
                 return true;
              }
           } else {
              //$this->Form->AddError('ErrorCredentials');
              return false;
           }
        }
    }
    
    /**
     * login directly without password
     * only used for sign_in method
     *
     * @param  Object  $oMbqEtUser
     * @return  Boolean
     */
    protected function loginDirectly($oMbqEtUser) {    //ref self::login()
        $oEntryController = new EntryController();
        $oEntryController->Initialize();
        /* modified from EntryController::SignIn() */
        $oEntryController->FireEvent('SignIn');
        //$Email = $this->Form->GetFormValue('Email');
        $Email = $oMbqEtUser->loginName->oriValue;
        $User = Gdn::UserModel()->GetByEmail($Email);
        if (!$User)
           $User = Gdn::UserModel()->GetByUsername($Email);

        if (!$User) {
           //$this->Form->AddError('ErrorCredentials');
           return false;
        } else {
           //$ClientHour = $this->Form->GetFormValue('ClientHour');
           $ClientHour = date('Y-m-d H:i');
           $HourOffset = Gdn_Format::ToTimestamp($ClientHour) - time();
           $HourOffset = round($HourOffset / 3600);

           // Check the password.
           $PasswordHash = new Gdn_PasswordHash();
           //if ($PasswordHash->CheckPassword($this->Form->GetFormValue('Password'), GetValue('Password', $User), GetValue('HashMethod', $User))) {
           //if ($PasswordHash->CheckPassword($password, GetValue('Password', $User), GetValue('HashMethod', $User))) {
              //Gdn::Session()->Start(GetValue('UserID', $User), TRUE, (bool)$this->Form->GetFormValue('RememberMe'));
              Gdn::Session()->Start(GetValue('UserID', $User), TRUE, TRUE);
              if (!Gdn::Session()->CheckPermission('Garden.SignIn.Allow')) {
                 //$this->Form->AddError('ErrorPermission');
                 Gdn::Session()->End();
                 return false;
              } else {
                 if ($HourOffset != Gdn::Session()->User->HourOffset) {
                    Gdn::UserModel()->SetProperty(Gdn::Session()->UserID, 'HourOffset', $HourOffset);
                 }
                 MbqMain::$oMbqAppEnv->oCurStdUser = $User;

                 //$this->_SetRedirect();
                 $this->initOCurMbqEtUser();
                 return true;
              }
           //} else {
              //$this->Form->AddError('ErrorCredentials');
              //return false;
           //}
        }
    }
    
    /**
     * logout
     *
     * @return  Boolean  return true when logout success.
     */
    public function logout() {
        $oEntryController = new EntryController();
        $oEntryController->Initialize();
        /* modified from EntryController::SignOut() */
        if (MbqMain::hasLogin()) {
             $User = Gdn::Session()->User;
             
             $oEntryController->EventArguments['SignoutUser'] = $User;
             $oEntryController->FireEvent("BeforeSignOut");
             
             // Sign the user right out.
             Gdn::Session()->End();
             
             $oEntryController->EventArguments['SignoutUser'] = $User;
             $oEntryController->FireEvent("SignOut");
        }
        $oEntryController->Leaving = FALSE;
        return true;
    }
    
    /**
     * init current user obj if login
     */
    public function initOCurMbqEtUser() {
        if (MbqMain::$oMbqAppEnv->oCurStdUser) {
            MbqMain::$oCurMbqEtUser = $this->initOMbqEtUser(MbqMain::$oMbqAppEnv->oCurStdUser, array('case' => 'oStdUser'));
        }
    }
    
    /**
     * sign in
     *
     * @return  Array  return array data for output
     */
    public function signIn() {
        $retData['result'] = false; //!!!
        //$apiKey = MbqMain::$oMbqCm->exttGetApiKey(MbqMain::$oMbqAppEnv->rootUrl); //do not need this to improve performance.
        $apiKey = '';
        //ref vb3x sign_in.php->sign_in_func()
        $vPost['token'] = MbqMain::$input[0];
        $vPost['code'] = MbqMain::$input[1];
        $vPost['email'] = MbqMain::$input[2];
        $vPost['username'] = MbqMain::$input[3];
        $vPost['password'] = MbqMain::$input[4];
        
        $email_response = MbqMain::$oMbqCm->auRegVerify($vPost['token'], $vPost['code'], $apiKey, MbqMain::$oMbqAppEnv->rootUrl);

        $response_verified = $email_response['result'] && isset($email_response['email']) && !empty($email_response['email']);
        if(!$response_verified) {
            /*
            if(!$apiKey) {
                $retData['result_text'] = 'Sorry, this community has not yet full configured to work with Tapatalk, this feature has been disabled.';
                return $retData;
            }
            else if(empty($email_response)) {
                $retData['result_text'] = 'Failed to connect to tapatalk server, please try again later.';
                return $retData;
            }
            else {
                $retData['result_text'] = isset($email_response['result_text'])? $email_response['result_text'] : 'Tapatalk ID session expired, please re-login Tapatalk ID and try again, if the problem persist please tell us.';
                return $retData;
            }
            */
            if(empty($email_response)) {
                $retData['result_text'] = 'Failed to connect to tapatalk server, please try again later.';
                return $retData;
            }
            else {
                $retData['result_text'] = isset($email_response['result_text'])? $email_response['result_text'] : 'Tapatalk ID session expired, please re-login Tapatalk ID and try again, if the problem persist please tell us.';
                return $retData;
            }
        }
        
        // Sign in logic
        if(!empty($vPost['email']))
        {
            if($email_response['email'] == $vPost['email'])
            {
                $user = $this->initOMbqEtUser($vPost['email'], array('case' => 'byEmail'));
                if($user && $user->userId->oriValue)
                {
                    return $this->exttMakeReturnDataForLoginDirectly($user);
                }
                else
                {
                    if(!empty($vPost['username']))
                    {
                        if (!MbqMain::$oMbqConfig->getCfg('user.sso_signin')->oriValue) {
                            $retData['result_text'] = 'Sorry,not support free activation SSO registration and directly login.';
                            return $retData;
                        }
                        $user = $this->initOMbqEtUser($vPost['username'], array('case' => 'byLoginName'));
                        $username_exist = ($user && $user->userId->oriValue);
                        
                        $oMbqWrEtUser = MbqMain::$oClk->newObj('MbqWrEtUser');
                        //register new user and active the new user directly
                        MbqMain::$oMbqAppEnv->otherParams['needNativeRegister'] = false;    //!!!
                        MbqMain::$oMbqAppEnv->otherParams['ssoCase'] = 'ssoRegistrationInSignInMethod'; //!!!
                        $reg_response = $oMbqWrEtUser->registerUser($vPost, $email_response['profile']);
    
                        if(is_array($reg_response))
                        {
                            if ($reg_response['registerStatus'] == 'registerOk') {
                                // login if registered
                                //return $this->exttMakeReturnDataForLoginDirectly($user, true);  
                                
                                //actually has login already in vanilla logic(refer ExttMbqPostController::exttRegisterBasic()),so we only need return data then.
                                if ($oNewMbqEtUser = $this->initOMbqEtUser($reg_response['newUserId'], array('case' => 'byUserId'))) {
                                    $retData['result'] = true;
                                    $retData['register'] = true;
                                    $data1 = $this->returnApiDataUser($oNewMbqEtUser);
                                    MbqMain::$oMbqCm->mergeApiData($retData, $data1);
                                    $oMbqWrEtUser->exttAssignRoleIfNeededAfterRegistration($oNewMbqEtUser);  //!!!
                                    return $retData;
                                } else {
                                    $retData['result_text'] = MBQ_ERR_INFO_UNKNOWN_ERROR;
                                    return $retData;
                                }
                            } else {
                                if ($username_exist) {
                                    $retData['status'] = (string) 1;
                                    $retData['result_text'] = 'Username was occupied.';
                                    return $retData;
                                } else {
                                    $retData['result_text'] = $reg_response['registerMessage'];
                                    return $retData;
                                }
                            }
                        }
                        else
                        {
                            if ($username_exist) {
                                $retData['status'] = (string) 1;
                                $retData['result_text'] = 'Username was occupied.';
                                return $retData;
                            } else {
                                $retData['result_text'] = MBQ_ERR_INFO_REGISTRATION_FAIL;
                                return $retData;
                            }
                        }
                    }
                    else
                    {
                        $retData['status'] = (string) 2;
                        return $retData;
                    }
                }
            }
            else
            {
                $retData['status'] = (string) 3;
                return $retData;
            }
        }
        else if(!empty($vPost['username']))
        {
            $user = $this->initOMbqEtUser($vPost['username'], array('case' => 'byLoginName'));
    
            if($user && $user->userId->oriValue && ($user->userEmail->oriValue == $email_response['email']))
            {
                return $this->exttMakeReturnDataForLoginDirectly($user);
            }
            else
            {
                $retData['status'] = (string) 3;
                return $retData;
            }
        }
        else
        {
            $retData['result_text'] = 'Application Error : either email or username should provided.';
            return $retData;
        }
        
        return $retData;
    }
    /**
     * make return data for login directly
     *
     * @param  Object  $oMbqEtUser
     * @param  Boolean  $isNewRegister
     * @return  Array
     */
    private function exttMakeReturnDataForLoginDirectly($oMbqEtUser, $isNewRegister = false) {
        $retData['result'] = false;
        if ($this->loginDirectly($oMbqEtUser)) {
            $retData['result'] = true;
            $retData['register'] = $isNewRegister;
            $data1 = $this->returnApiDataUser($oMbqEtUser);
            MbqMain::$oMbqCm->mergeApiData($retData, $data1);
            return $retData;
        } else {
            return $retData; //login failed
        }
    }
    
    /**
     * forget_password
     *
     * @return  Array  return array data for output
     */
    public function forgetPassword() {
        $retData['result'] = false; //!!!
        $retData['verified'] = false; //!!!
        $response['handleStatus'] = 'handleFail';
        
        $vPost['username'] = MbqMain::$input[0];
        $vPost['token'] = MbqMain::$input[1];
        $vPost['code'] = MbqMain::$input[2];
        
        if ($oMbqEtUser = $this->initOMbqEtUser($vPost['username'], array('case' => 'byLoginName'))) {
            $vPost['oMbqEtUser'] = $oMbqEtUser;
        } elseif ($oMbqEtUser = $this->initOMbqEtUser($vPost['username'], array('case' => 'byEmail'))) {
            $vPost['oMbqEtUser'] = $oMbqEtUser;
        } else {
            $retData['result_text'] = 'Need valid user.';
            return $retData;
        }
        if (!$oMbqEtUser->userEmail->oriValue) {
            $retData['result_text'] = 'Need valid email.';
            return $retData;
        }
        if ($this->isAdminRole($oMbqEtUser)) {
            $retData['result_text'] = MBQ_ERR_INFO_NOT_PERMIT_FOR_ADMIN;
            return $retData;
        }
        
        if (MbqMain::$input[1]) {
            //$apiKey = MbqMain::$oMbqCm->exttGetApiKey(MbqMain::$oMbqAppEnv->rootUrl); //do not need this to improve performance.
            $apiKey = '';
            $email_response = MbqMain::$oMbqCm->auRegVerify($vPost['token'], $vPost['code'], $apiKey, MbqMain::$oMbqAppEnv->rootUrl);
            $retData['verified'] = isset($email_response['result']) && $email_response['result'] && isset($email_response['email']) && !empty($email_response['email']) && ($email_response['email'] == $oMbqEtUser->userEmail->oriValue);
        }
        
        if (!$retData['verified']) {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqEntryController.php');
            $oExttMbqEntryController = new ExttMbqEntryController();
            $oExttMbqEntryController->Initialize();
            $response = $oExttMbqEntryController->exttPasswordRequest($vPost, $response);
            if(is_array($response))
            {
                if ($response['handleStatus'] == 'handleOk') {
                    $retData['result'] = true;
                    $retData['result_text'] = 'A reset password email has been sent, please check your email to continue.';
                    return $retData;
                } else {
                    $retData['result_text'] = $response['handleMessage'];
                    return $retData;
                }
            }
            else
            {
                $retData['result_text'] = 'Send out password reset email failed.';
                return $retData;
            }
        }
        
        return $retData;
    }
  
}

?>