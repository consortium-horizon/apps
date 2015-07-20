<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_box_info action
 * 
 * @since  2012-12-28
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetBoxInfo extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (MbqMain::$oMbqConfig->moduleIsEnable('pm')) {
        } else {
            MbqError::alert('', "Not support module private message!", '', MBQ_ERR_NOT_SUPPORT);
        }        
        $oMbqAclEtPm = MbqMain::$oClk->newObj('MbqAclEtPm');
        if ($oMbqAclEtPm->canAclGetBoxInfo()) {    //acl judge
            $oMbqRdEtPm = MbqMain::$oClk->newObj('MbqRdEtPm');
            $objsMbqEtPmBox = $oMbqRdEtPm->getObjsMbqEtPmBox();
            $this->data['result'] = true;
            $this->data['list'] = $oMbqRdEtPm->returnApiArrDataPmBox($objsMbqEtPmBox);
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>