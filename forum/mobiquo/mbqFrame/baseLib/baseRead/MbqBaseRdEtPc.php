<?php

defined('MBQ_IN_IT') or exit;

/**
 * private conversation read class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtPc extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return private conversation api data
     *
     * @param  Object  $oMbqEtPc
     * @return  Array
     */
    public function returnApiDataPc($oMbqEtPc) {
        $data = array();
        if ($oMbqEtPc->convId->hasSetOriValue()) {
            $data['conv_id'] = (string) $oMbqEtPc->convId->oriValue;
        }
        if ($oMbqEtPc->convTitle->hasSetOriValue()) {
            $data['conv_subject'] = (string) $oMbqEtPc->convTitle->oriValue;
            $data['conv_title'] = (string) $oMbqEtPc->convTitle->oriValue;
        }
        if ($oMbqEtPc->totalMessageNum->hasSetOriValue()) {
            $data['total_message_num'] = (int) $oMbqEtPc->totalMessageNum->oriValue;
            $data['reply_count'] = (int) ($oMbqEtPc->totalMessageNum->oriValue - 1);
        }
        if ($oMbqEtPc->participantCount->hasSetOriValue()) {
            $data['participant_count'] = (int) $oMbqEtPc->participantCount->oriValue;
        }
        if ($oMbqEtPc->startUserId->hasSetOriValue()) {
            $data['start_user_id'] = (string) $oMbqEtPc->startUserId->oriValue;
        }
        if ($oMbqEtPc->startConvTime->hasSetOriValue()) {
            $data['start_conv_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtPc->startConvTime->oriValue);
        }
        if ($oMbqEtPc->lastUserId->hasSetOriValue()) {
            $data['last_user_id'] = (string) $oMbqEtPc->lastUserId->oriValue;
        }
        if ($oMbqEtPc->lastConvTime->hasSetOriValue()) {
            $data['last_conv_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtPc->lastConvTime->oriValue);
        }
        if ($oMbqEtPc->newPost->hasSetOriValue()) {
            $data['new_post'] = (boolean) $oMbqEtPc->newPost->oriValue;
        }
        if ($oMbqEtPc->canInvite->hasSetOriValue()) {
            $data['can_invite'] = (boolean) $oMbqEtPc->canInvite->oriValue;
        } else {
            $data['can_invite'] = (boolean) MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.canInvite.default');
        }
        if ($oMbqEtPc->canEdit->hasSetOriValue()) {
            $data['can_edit'] = (boolean) $oMbqEtPc->canEdit->oriValue;
        } else {
            $data['can_edit'] = (boolean) MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.canEdit.default');
        }
        if ($oMbqEtPc->canClose->hasSetOriValue()) {
            $data['can_close'] = (boolean) $oMbqEtPc->canClose->oriValue;
        } else {
            $data['can_close'] = (boolean) MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.canClose.default');
        }
        if ($oMbqEtPc->isClosed->hasSetOriValue()) {
            $data['is_closed'] = (boolean) $oMbqEtPc->isClosed->oriValue;
        }
        if ($oMbqEtPc->deleteMode->hasSetOriValue()) {
            $data['delete_mode'] = (int) $oMbqEtPc->deleteMode->oriValue;
        }
        if ($oMbqEtPc->objsRecipientMbqEtUser) {
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $data['participants'] = $oMbqRdEtUser->returnApiArrDataUser($oMbqEtPc->objsRecipientMbqEtUser, true);
        } else {
            $data['participants'] = array();
        }
        return $data;
    }
    
    /**
     * return private conversation array api data
     *
     * @param  Array  $objsMbqEtPc
     * @return  Array
     */
    public function returnApiArrDataPc($objsMbqEtPc) {
        $data = array();
        foreach ($objsMbqEtPc as $oMbqEtPc) {
            $data[] = $this->returnApiDataPc($oMbqEtPc);
        }
        return $data;
    }
    
    /**
     * get private conversation objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtPc() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one private conversation by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtPc() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get unread private conversations number
     *
     * @return  Integer
     */
    public function getUnreadPcNum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>