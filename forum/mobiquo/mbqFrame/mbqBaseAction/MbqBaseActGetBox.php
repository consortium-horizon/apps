<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_box action
 * 
 * @since  2012-12-29
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetBox extends MbqBaseAct {
    
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
        $boxId = MbqMain::$input[0];
        $startNum = (int) MbqMain::$input[1];
        $lastNum = (int) MbqMain::$input[2];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $oMbqRdEtPm = MbqMain::$oClk->newObj('MbqRdEtPm');
        if ($oMbqEtPmBox = $oMbqRdEtPm->initOMbqEtPmBox($boxId, array('case' => 'byBoxId'))) {
            $oMbqAclEtPm = MbqMain::$oClk->newObj('MbqAclEtPm');
            if ($oMbqAclEtPm->canAclGetBox($oMbqEtPmBox)) {
                $oMbqDataPage = $oMbqRdEtPm->getObjsMbqEtPm($oMbqEtPmBox, array('case' => 'byBox', 'oMbqDataPage' => $oMbqDataPage));
                $this->data = $oMbqRdEtPm->returnApiDataPmBox($oMbqEtPmBox);
                $this->data['result'] = true;
                $this->data['list'] = $oMbqRdEtPm->returnApiArrDataPm($oMbqDataPage->datas, false);
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid pm box id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>