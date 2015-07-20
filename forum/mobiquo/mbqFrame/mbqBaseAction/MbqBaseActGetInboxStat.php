<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_inbox_stat action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetInboxStat extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        /* TODO */
        if (MbqMain::$oMbqConfig->moduleIsEnable('pc') && (MbqMain::$oMbqConfig->getCfg('pc.conversation')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.pc.conversation.range.support'))) {
            $oMbqAclEtPc = MbqMain::$oClk->newObj('MbqAclEtPc');
            if ($oMbqAclEtPc->canAclGetInboxStat()) {    //acl judge
                $oMbqRdEtPc = MbqMain::$oClk->newObj('MbqRdEtPc');
                $this->data['inbox_unread_count'] = (int) $oMbqRdEtPc->getUnreadPcNum();
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } elseif (MbqMain::$oMbqConfig->moduleIsEnable('pm')) {
            $this->data['inbox_unread_count'] = (int) 0;
        } else {
            $this->data['inbox_unread_count'] = (int) 0;
        }
        if (MbqMain::$oMbqConfig->moduleIsEnable('subscribe')) {
            $this->data['subscribed_topic_unread_count'] = (int) 0;
        } else {
            $this->data['subscribed_topic_unread_count'] = (int) 0;
        }
    }
  
}

?>