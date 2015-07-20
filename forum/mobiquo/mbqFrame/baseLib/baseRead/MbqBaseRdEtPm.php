<?php

defined('MBQ_IN_IT') or exit;

/**
 * private message read class
 * 
 * @since  2012-12-29
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtPm extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return private message api data
     *
     * @param  Object  $oMbqEtPm
     * @param  Boolean  $returnHtml
     * @return  Array
     */
    public function returnApiDataPm($oMbqEtPm, $returnHtml = true) {
        $data = array();
        if ($oMbqEtPm->boxId->hasSetOriValue()) {
            $data['box_id'] = (string) $oMbqEtPm->boxId->oriValue;
        }
        
        if ($oMbqEtPm->msgId->hasSetOriValue()) {
            $data['msg_id'] = (string) $oMbqEtPm->msgId->oriValue;
        }
        if ($oMbqEtPm->msgTitle->hasSetOriValue()) {
            $data['msg_subject'] = (string) $oMbqEtPm->msgTitle->oriValue;
        }
        if ($returnHtml) {
            if ($oMbqEtPm->msgContent->hasSetTmlDisplayValue()) {
                $data['text_body'] = (string) $oMbqEtPm->msgContent->tmlDisplayValue;
            }
        } else {
            if ($oMbqEtPm->msgContent->hasSetTmlDisplayValueNoHtml()) {
                $data['text_body'] = (string) $oMbqEtPm->msgContent->tmlDisplayValueNoHtml;
            }
        }
       
        $data['short_content'] = (string) $oMbqEtPm->shortContent->oriValue;
        if ($oMbqEtPm->isOnline->hasSetOriValue()) {
            $data['is_online'] = (boolean) $oMbqEtPm->isOnline->oriValue;
        }
        if ($oMbqEtPm->isRead->hasSetOriValue() && $oMbqEtPm->isRead->oriValue == MbqBaseFdt::getFdt('MbqFdtPm.MbqEtPm.isRead.range.yes')) {
            $data['msg_state'] = 2;
        } elseif ($oMbqEtPm->isReply->hasSetOriValue() && $oMbqEtPm->isReply->oriValue == MbqBaseFdt::getFdt('MbqFdtPm.MbqEtPm.isReply.range.yes')) {
            $data['msg_state'] = 3;
        } elseif ($oMbqEtPm->isForward->hasSetOriValue() && $oMbqEtPm->isForward->oriValue == MbqBaseFdt::getFdt('MbqFdtPm.MbqEtPm.isForward.range.yes')) {
            $data['msg_state'] = 4;
        } else {
            $data['msg_state'] = 1;
        } 
        if ($oMbqEtPm->sentDate->hasSetOriValue()) {
            $data['sent_date'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtPm->sentDate->oriValue);
        }
        if ($oMbqEtPm->msgFromId->hasSetOriValue()) {
            $data['msg_from_id'] = (string) $oMbqEtPm->msgFromId->oriValue;
        }
        if ($oMbqEtPm->msgFrom->hasSetOriValue()) {
            $data['msg_from'] = (string) $oMbqEtPm->msgFrom->oriValue;
        }
        if ($oMbqEtPm->allowSmilies->hasSetOriValue()) {
            $data['allow_smilies'] = (boolean) $oMbqEtPm->allowSmilies->oriValue;
        }
        if ($oMbqEtPm->oMbqEtPmBox && $oMbqEtPm->oMbqEtPmBox->isSentBox() && $oMbqEtPm->oFirstRecipientMbqEtUser && $oMbqEtPm->oFirstRecipientMbqEtUser->iconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtPm->oFirstRecipientMbqEtUser->iconUrl->oriValue;
        } elseif ($oMbqEtPm->oAuthorMbqEtUser && $oMbqEtPm->oAuthorMbqEtUser->iconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtPm->oAuthorMbqEtUser->iconUrl->oriValue;
        }
        if ($oMbqEtPm->objsRecipientMbqEtUser) {
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $data['msg_to'] = $oMbqRdEtUser->returnApiArrDataUser($oMbqEtPm->objsRecipientMbqEtUser, true);
        } else {
            $data['msg_to'] = array();
        }
        return $data;
    }
    
    /**
     * return private message array api data
     *
     * @param  Array  $objsMbqEtPm
     * @param  Boolean  $returnHtml
     * @return  Array
     */
    public function returnApiArrDataPm($objsMbqEtPm, $returnHtml = true) {
        $data = array();
        foreach ($objsMbqEtPm as $oMbqEtPm) {
            $data[] = $this->returnApiDataPm($oMbqEtPm, $returnHtml);
        }
        return $data;
    }
    
    /**
     * get private message objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtPm() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one private message by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtPm() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * return private message box api data
     *
     * @param  Object  $oMbqEtPmBox
     * @return  Array
     */
    public function returnApiDataPmBox($oMbqEtPmBox) {
        $data = array();
        if ($oMbqEtPmBox->boxId->hasSetOriValue()) {
            $data['box_id'] = (string) $oMbqEtPmBox->boxId->oriValue;
        }
        if ($oMbqEtPmBox->boxName->hasSetOriValue()) {
            $data['box_name'] = (string) $oMbqEtPmBox->boxName->oriValue;
        }
        if ($oMbqEtPmBox->msgCount->hasSetOriValue()) {
            $data['msg_count'] = (int) $oMbqEtPmBox->msgCount->oriValue;
            $data['total_message_count'] = (int) $oMbqEtPmBox->msgCount->oriValue;
        }
        if ($oMbqEtPmBox->unreadCount->hasSetOriValue()) {
            $data['unread_count'] = (int) $oMbqEtPmBox->unreadCount->oriValue;
            $data['total_unread_count'] = (int) $oMbqEtPmBox->unreadCount->oriValue;
        }
        if ($oMbqEtPmBox->boxType->hasSetOriValue()) {
            $data['box_type'] = (string) $oMbqEtPmBox->boxType->oriValue;
        }
        return $data;
    }
    
    /**
     * return private message box array api data
     *
     * @param  Array  $objsMbqEtPmBox
     * @return  Array
     */
    public function returnApiArrDataPmBox($objsMbqEtPmBox) {
        $data = array();
        foreach ($objsMbqEtPmBox as $oMbqEtPmBox) {
            $data[] = $this->returnApiDataPmBox($oMbqEtPmBox);
        }
        return $data;
    }
    
    /**
     * get private message box objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtPmBox() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one private message box by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtPmBox() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>