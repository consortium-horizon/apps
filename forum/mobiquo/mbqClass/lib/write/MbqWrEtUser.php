<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseWrEtUser');

/**
 * user write class
 * 
 * @since  2012-9-28
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqWrEtUser extends MbqBaseWrEtUser {
    
    public function __construct() {
    }
    
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
     * @return  Array
     */
    public function registerUser($data, $profile) {
        $retData['registerStatus'] = 'registerFail';
        if (is_array($profile) && $profile) {
        } else {
            $profile = array();
        }
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqEntryController.php');
        $oExttMbqEntryController = new ExttMbqEntryController();
        $oExttMbqEntryController->Initialize();
        return $oExttMbqEntryController->exttRegisterBasic($data, $profile, $retData);
    }
    
    /**
     * register
     *
     * @return  Array  return array data for output
     */
    public function register() {
        $retData['result'] = false; //!!!
        $reg_response['registerStatus'] = 'registerFail';
        MbqMain::$oMbqAppEnv->otherParams['needNativeRegister'] = true; //!!!
        $profile = array();
        if (count(MbqMain::$input) == 5) {
            $vPost['username'] = MbqMain::$input[0];
            $vPost['password'] = MbqMain::$input[1];
            $vPost['email'] = MbqMain::$input[2];
            $vPost['token'] = MbqMain::$input[3];
            $vPost['code'] = MbqMain::$input[4];
            $apiKey = '';
            $email_response = MbqMain::$oMbqCm->auRegVerify($vPost['token'], $vPost['code'], $apiKey, MbqMain::$oMbqAppEnv->rootUrl);
            if (isset($email_response['result']) && $email_response['result']) {
                if (isset($email_response['email']) && !empty($email_response['email']) && ($email_response['email'] == $vPost['email'])) {
                    if (!MbqMain::$oMbqConfig->getCfg('user.sso_register')->oriValue) {
                        $retData['result_text'] = 'Sorry,not support free activation SSO registration.';
                        return $retData;
                    }
                    MbqMain::$oMbqAppEnv->otherParams['needNativeRegister'] = false;
                    $profile = $email_response['profile'];
                }
            } else {
                if (!$vPost['email'] && $email_response['email']) {
                    $vPost['email'] = $email_response['email'];
                }
            }
        } else {
            $vPost['username'] = MbqMain::$input[0];
            $vPost['password'] = MbqMain::$input[1];
            $vPost['email'] = MbqMain::$input[2];
        }
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqEntryController.php');
        $oExttMbqEntryController = new ExttMbqEntryController();
        $oExttMbqEntryController->Initialize();
        if (MbqMain::$oMbqAppEnv->otherParams['needNativeRegister']) {
            if (!MbqMain::$oMbqConfig->getCfg('user.native_register')->oriValue) {
                $retData['result_text'] = 'Sorry,not support native registration.';
                return $retData;
            }
            switch (strtolower(C('Garden.Registration.Method'))) {
                case 'basic':
                    $reg_response = $oExttMbqEntryController->exttRegisterBasic($vPost, $profile, $reg_response);
                break;
                case 'captcha':
                    $reg_response = $oExttMbqEntryController->exttRegisterBasic($vPost, $profile, $reg_response);
                break;
                case 'approval':
                    $reg_response = $oExttMbqEntryController->exttRegisterApproval($vPost, $profile, $reg_response);
                break;
                default:    //any other registration method
                    $reg_response = $oExttMbqEntryController->exttRegisterBasic($vPost, $profile, $reg_response);
                break;
            }
        } else {    //register new user and active the new user directly
            MbqMain::$oMbqAppEnv->otherParams['ssoCase'] = 'ssoRegistrationInRegisterMethod'; //!!!
            $reg_response = $oExttMbqEntryController->exttRegisterBasic($vPost, $profile, $reg_response);
        }
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if(is_array($reg_response))
        {
            if ($reg_response['registerStatus'] == 'registerOk') {
                if ($oNewMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($reg_response['newUserId'], array('case' => 'byUserId'))) {
                    $retData['result'] = true;
                    $retData['result_text'] = MBQ_ERR_INFO_REGISTRATION_SUCCESS;
                    $this->exttAssignRoleIfNeededAfterRegistration($oNewMbqEtUser); //!!!
                    return $retData;
                } else {
                    $retData['result_text'] = MBQ_ERR_INFO_UNKNOWN_ERROR;
                    return $retData;
                }
            } else {
                $retData['result_text'] = $reg_response['registerMessage'];
                return $retData;
            }
        }
        else
        {
            $retData['result_text'] = MBQ_ERR_INFO_REGISTRATION_FAIL;
            return $retData;
        }
    }
    /**
     * assign role if needed after registration
     *
     * @param  Object  $oMbqEtUser
     */
    public function exttAssignRoleIfNeededAfterRegistration($oMbqEtUser) {
        if (isset(MbqMain::$oMbqAppEnv->otherParams['ssoCase']) && (
            MbqMain::$oMbqAppEnv->otherParams['ssoCase'] == 'ssoRegistrationInSignInMethod' || 
            MbqMain::$oMbqAppEnv->otherParams['ssoCase'] == 'ssoRegistrationInRegisterMethod'
        ) && C('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment')) {
            $oRoleModel = new RoleModel();
            $result = $oRoleModel->SQL->Select('*')
             ->From('UserRole')
             ->Where('UserRole.UserID', $oMbqEtUser->userId->oriValue)
             ->Get()
             ->ResultArray();
            if ($result) {
                $oRoleModel->SQL->Update('UserRole')
                ->Set('UserRole.RoleID', C('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment'))
                ->Where('UserRole.UserID', $oMbqEtUser->userId->oriValue)
                ->Put();
            }
        }
    }
    
    /**
     * update password
     *
     * @return  Array  return array data for output
     */
    public function updatePassword() {
        $retData['result'] = false; //!!!
        $response['updateStatus'] = 'updateFail';
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if ($oMbqRdEtUser->isAdminRole(MbqMain::$oCurMbqEtUser)) {
            $retData['result_text'] = MBQ_ERR_INFO_NOT_PERMIT_FOR_ADMIN;
            return $retData;
        }
        if (MbqMain::$input[2]) {
            $vPost['case'] = 'updatePasswordByTapatalkId';
            $vPost['new_password'] = MbqMain::$input[0];
            $vPost['token'] = MbqMain::$input[1];
            $vPost['code'] = MbqMain::$input[2];
            
            //$apiKey = MbqMain::$oMbqCm->exttGetApiKey(MbqMain::$oMbqAppEnv->rootUrl); //do not need this to improve performance.
            $apiKey = '';
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
            
            if (!$vPost['new_password']) {
                $retData['result_text'] = MBQ_ERR_INFO_PARAMS_ERROR;
                return $retData;
            }
            if ($email_response['email'] != MbqMain::$oCurMbqEtUser->userEmail->oriValue) {
                $retData['result_text'] = 'You just only can modify your own password.';
                return $retData;
            }
        } else {
            $vPost['case'] = 'updatePasswordByNativeLogic';
            $vPost['old_password'] = MbqMain::$input[0];
            $vPost['new_password'] = MbqMain::$input[1];
            
            if (!$vPost['new_password']) {
                $retData['result_text'] = MBQ_ERR_INFO_PARAMS_ERROR;
                return $retData;
            }
        }
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqProfileController.php');
        $oExttMbqProfileController = new ExttMbqProfileController();
        $oExttMbqProfileController->Initialize();
        $response = $oExttMbqProfileController->exttPassword($vPost, $response);
        if(is_array($response))
        {
            if ($response['updateStatus'] == 'updateOk') {
                $retData['result'] = true;
                $retData['result_text'] = 'Update password success.';
                return $retData;
            } else {
                $retData['result_text'] = $response['updateMessage'];
                return $retData;
            }
        }
        else
        {
            $retData['result_text'] = 'Update password failed.';
            return $retData;
        }
    }
    
    /**
     * update email
     *
     * @return  Array  return array data for output
     */
    public function updateEmail() {
        $retData['result'] = false; //!!!
        $response['updateStatus'] = 'updateFail';
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if ($oMbqRdEtUser->isAdminRole(MbqMain::$oCurMbqEtUser)) {
            $retData['result_text'] = MBQ_ERR_INFO_NOT_PERMIT_FOR_ADMIN;
            return $retData;
        }
        $vPost['password'] = MbqMain::$input[0];
        $vPost['new_email'] = MbqMain::$input[1];
        if (!$vPost['password'] || !$vPost['new_email']) {
            $retData['result_text'] = MBQ_ERR_INFO_PARAMS_ERROR;
            return $retData;
        }
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqProfileController.php');
        $oExttMbqProfileController = new ExttMbqProfileController();
        $oExttMbqProfileController->Initialize();
        $response = $oExttMbqProfileController->exttEdit($vPost, $response);
        if(is_array($response))
        {
            if ($response['updateStatus'] == 'updateOk') {
                $retData['result'] = true;
                $retData['result_text'] = 'Update email success.';
                return $retData;
            } else {
                $retData['result_text'] = $response['updateMessage'];
                return $retData;
            }
        }
        else
        {
            $retData['result_text'] = 'Update email failed.';
            return $retData;
        }
    }
  
}

?>