<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActLogin');

/**
 * login action
 * 
 * @since  2012-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActLogin extends MbqBaseActLogin {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('user')) {
            MbqError::alert('', "Not support module user!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        $result = $oMbqRdEtUser->login(MbqMain::$input[0], MbqMain::$input[1]);
        if ($result) {
            $this->data['result'] = true;
            $data1 = $oMbqRdEtUser->returnApiDataUser(MbqMain::$oCurMbqEtUser);
            MbqMain::$oMbqCm->mergeApiData($this->data, $data1);
            $oTapatalkPush = new TapatalkPush();
            $oTapatalkPush->callMethod('doAfterAppLogin');
        } else {
            $this->data['result'] = false;
            $this->data['result_text'] = 'Login failed.';
            if (!$oMbqRdEtUser->initOMbqEtUser(MbqMain::$input[0], array('case' => 'byLoginName'))) {
                $this->data['status'] = (string) 2; //!!! attention the (string)
            }
        }
    }
  
}

?>