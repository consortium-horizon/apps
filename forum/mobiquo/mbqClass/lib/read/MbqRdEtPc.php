<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtPc');

/**
 * private conversation read class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtPc extends MbqBaseRdEtPc {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtPc, $pName, $mbqOpt = array()) {
        switch ($pName) {
            case 'oFirstMbqEtPcMsg':
            if ($oMbqEtPc->firstMsgId->hasSetOriValue()) {
                $oMbqRdEtPcMsg = MbqMain::$oClk->newObj('MbqRdEtPcMsg');
                if ($objsMbqEtPcMsg = $oMbqRdEtPcMsg->getObjsMbqEtPcMsg($oMbqEtPc, array('case' => 'byPc', 'pcMsgIds' => array($oMbqEtPc->firstMsgId->oriValue)))) {
                    $oMbqEtPc->oFirstMbqEtPcMsg = $objsMbqEtPcMsg[0];
                }
            }
            break;
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get private conversation objs
     *
     * $mbqOpt['case'] = 'all' means get my all data.
     * $mbqOpt['case'] = 'byConvIds' means get data by conversation ids.$var is the ids.
     * $mbqOpt['case'] = 'byObjsStdPc' means get data by objsStdPc.$var is the objsStdPc.
     * @return  Mixed
     */
    public function getObjsMbqEtPc($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'all') {
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                require_once(MBQ_APPEXTENTION_PATH.'ExttMbqConversationModel.php');
                $oExttMbqConversationModel = new ExttMbqConversationModel();
                $arr = $oExttMbqConversationModel->exttMbqGetPcs(MbqMain::$oCurMbqEtUser->userId->oriValue, $oMbqDataPage->startNum, $oMbqDataPage->numPerPage);
                $objsStdPc = $arr['pcs']->Result();
                $oMbqDataPage->totalNum = $arr['total'];
                /* common begin */
                $mbqOpt['case'] = 'byObjsStdPc';
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
                return $this->getObjsMbqEtPc($objsStdPc, $mbqOpt);
                /* common end */
            }
        } elseif ($mbqOpt['case'] == 'byConvIds') {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqConversationModel.php');
            $oExttMbqConversationModel = new ExttMbqConversationModel();
            $arr = $oExttMbqConversationModel->exttMbqGetPcs(MbqMain::$oCurMbqEtUser->userId->oriValue, '', '', '', array('convIds' => $var));
            $objsStdPc = $arr['pcs']->Result();
            /* common begin */
            $mbqOpt['case'] = 'byObjsStdPc';
            return $this->getObjsMbqEtPc($objsStdPc, $mbqOpt);
            /* common end */
        } elseif ($mbqOpt['case'] == 'byObjsStdPc') {
            $objsStdPc = $var;
            /* common begin */
            $objsMbqEtPc = array();
            foreach ($objsStdPc as $oStdPc) {
                $objsMbqEtPc[] = $this->initOMbqEtPc($oStdPc, array('case' => 'oStdPc'));
            }
            /* load objsRecipientMbqEtUser property and make relative property */
            $oConversationModel = new ConversationModel();
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            foreach ($objsMbqEtPc as &$oMbqEtPc) {
                $objsStdPcRecipient = $oConversationModel->GetRecipients($oMbqEtPc->convId->oriValue)->Result();
                $tempUserIds = array();
                foreach ($objsStdPcRecipient as $oStdPcRecipient) {
                    $tempUserIds[] = $oStdPcRecipient->UserID;
                }
                $objsRecipientMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser($tempUserIds, array('case' => 'byUserIds'));
                foreach ($objsRecipientMbqEtUser as &$oRecipientMbqEtUser) {
                    foreach ($objsStdPcRecipient as $oStdPcRecipient) {
                        if ($oRecipientMbqEtUser->userId->oriValue == $oStdPcRecipient->UserID) {
                            $oRecipientMbqEtUser->mbqBind['oStdPcRecipient'] = $oStdPcRecipient;
                            break;
                        }
                    }
                }
                $oMbqEtPc->objsRecipientMbqEtUser = $objsRecipientMbqEtUser;
                $oMbqEtPc->participantCount->setOriValue(count($oMbqEtPc->objsRecipientMbqEtUser));
                $oMbqEtPc->canInvite->setOriValue(MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.canInvite.range.no'));
                foreach ($oMbqEtPc->objsRecipientMbqEtUser as $oTempRecipientMbqEtUser) {
                    if (($oTempRecipientMbqEtUser->userId->oriValue == MbqMain::$oCurMbqEtUser->userId->oriValue) && !$oTempRecipientMbqEtUser->mbqBind['oStdPcRecipient']->Deleted) {
                        $oMbqEtPc->canInvite->setOriValue(MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.canInvite.range.yes'));
                    }
                }
            }
            /* load oFirstMbqEtPcMsg property and make relative property */
            foreach ($objsMbqEtPc as &$oMbqEtPc) {
                $this->makeProperty($oMbqEtPc, 'oFirstMbqEtPcMsg');
                if ($oMbqEtPc->oFirstMbqEtPcMsg) {
                    //$oMbqEtPc->convTitle->setOriValue($oMbqEtPc->oFirstMbqEtPcMsg->msgContent->tmlDisplayValueNoHtml);
                    $oMbqEtPc->convTitle->setOriValue(MbqMain::$oMbqCm->exttSubstr($oMbqEtPc->oFirstMbqEtPcMsg->msgContent->tmlDisplayValueNoHtml, 0, 15));
                }
            }
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                $oMbqDataPage->datas = $objsMbqEtPc;
                return $oMbqDataPage;
            } else {
                return $objsMbqEtPc;
            }
            /* common end */
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one private conversation by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdPc' means init private conversation by StdPc obj
     * @return  Mixed
     */
    public function initOMbqEtPc($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdPc') {
            $oMbqEtPc = MbqMain::$oClk->newObj('MbqEtPc');
            $oMbqEtPc->convId->setOriValue($var->ConversationID);
            $oMbqEtPc->totalMessageNum->setOriValue($var->CountMessages);
            $oMbqEtPc->startUserId->setOriValue($var->InsertUserID);
            $oMbqEtPc->startConvTime->setOriValue(strtotime($var->DateInserted));
            $oMbqEtPc->lastUserId->setOriValue($var->LastMessageUserID);
            $oMbqEtPc->lastConvTime->setOriValue(strtotime($var->DateLastMessage));
            $oMbqEtPc->newPost->setOriValue($var->CountNewMessages ? true : false);
            $oMbqEtPc->firstMsgId->setOriValue($var->FirstMessageID);
            $oMbqEtPc->deleteMode->setOriValue(MbqBaseFdt::getFdt('MbqFdtPc.MbqEtPc.deleteMode.range.hard-delete'));
            $oMbqEtPc->mbqBind['oStdPc'] = $var;
            return $oMbqEtPc; 
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * get unread private conversations number
     *
     * @return  Integer
     */
    public function getUnreadPcNum() {
        if (MbqMain::hasLogin()) {
            $oConversationModel = new ConversationModel();
            return $oConversationModel->CountUnread(MbqMain::$oCurMbqEtUser->userId->oriValue);
        } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . 'Need login!');
        }
    }
  
}

?>