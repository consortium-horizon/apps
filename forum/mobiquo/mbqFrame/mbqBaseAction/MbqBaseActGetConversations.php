<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_conversations action
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetConversations extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (MbqMain::$oMbqConfig->moduleIsEnable('pc') && (MbqMain::$oMbqConfig->getCfg('pc.conversation')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.pc.conversation.range.support'))) {
        } else {
            MbqError::alert('', "Not support module private conversation!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $startNum = (int) MbqMain::$input[0];
        $lastNum = (int) MbqMain::$input[1];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $oMbqAclEtPc = MbqMain::$oClk->newObj('MbqAclEtPc');
        if ($oMbqAclEtPc->canAclGetConversations()) {    //acl judge
            $oMbqRdEtPc = MbqMain::$oClk->newObj('MbqRdEtPc');
            $oMbqDataPage = $oMbqRdEtPc->getObjsMbqEtPc(NULL, array('case' => 'all', 'oMbqDataPage' => $oMbqDataPage));
            $this->data['conversation_count'] = (int) $oMbqDataPage->totalNum;
            $this->data['unread_count'] = (int) $oMbqRdEtPc->getUnreadPcNum();
            $this->data['list'] = $oMbqRdEtPc->returnApiArrDataPc($oMbqDataPage->datas);
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>