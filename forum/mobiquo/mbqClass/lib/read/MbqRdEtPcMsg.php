<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtPcMsg');

/**
 * private conversation message read class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtPcMsg extends MbqBaseRdEtPcMsg {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtPcMsg, $pName, $mbqOpt = array()) {
        switch ($pName) {
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get private conversation message objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byPc' means get data by private conversation obj.$var is the private conversation obj,$mbqOpt['pcMsgIds'] means get data in these ids.
     * $mbqOpt['case'] = 'byObjsStdPcMsg' means get data by objsStdPcMsg.$var is the objsStdPcMsg.
     * @return  Mixed
     */
    public function getObjsMbqEtPcMsg($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byPc') {
            $oMbqEtPc = $var;
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                $startNum = $oMbqDataPage->startNum;
                $numPerPage = $oMbqDataPage->numPerPage;
            } else {
                $startNum = 0;
                $numPerPage = 0;
            }
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqConversationMessageModel.php');
            $oExttMbqConversationMessageModel = new ExttMbqConversationMessageModel();
            if ($mbqOpt['pcMsgIds']) {
                $arr = $oExttMbqConversationMessageModel->exttMbqGetPcMsgs($oMbqEtPc->convId->oriValue, MbqMain::$oCurMbqEtUser->userId->oriValue, $startNum, $numPerPage, '', array('pcMsgIds' => $mbqOpt['pcMsgIds']));
            } else {
                $arr = $oExttMbqConversationMessageModel->exttMbqGetPcMsgs($oMbqEtPc->convId->oriValue, MbqMain::$oCurMbqEtUser->userId->oriValue, $startNum, $numPerPage, '');
            }
            $objsStdPcMsg = $arr['pcMsgs']->Result();
            $totalNum = $arr['total'];
            /* common begin */
            $mbqOpt['case'] = 'byObjsStdPcMsg';
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage->totalNum = $totalNum;
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
            }
            return $this->getObjsMbqEtPcMsg($objsStdPcMsg, $mbqOpt);
            /* common end */
        } elseif ($mbqOpt['case'] == 'byObjsStdPcMsg') {
            $objsStdPcMsg = $var;
            /* common begin */
            $objsMbqEtPcMsg = array();
            $authorUserIds = array();
            foreach ($objsStdPcMsg as $oStdPcMsg) {
                $authorUserIds[] = $oStdPcMsg->InsertUserID;
                $objsMbqEtPcMsg[] = $this->initOMbqEtPcMsg($oStdPcMsg, array('case' => 'oStdPcMsg'));
            }
            /* load oAuthorMbqEtUser property */
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $objsAuthorMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser($authorUserIds, array('case' => 'byUserIds'));
            foreach ($objsMbqEtPcMsg as &$oMbqEtPcMsg) {
                foreach ($objsAuthorMbqEtUser as $oAuthorMbqEtUser) {
                    if ($oMbqEtPcMsg->msgAuthorId->oriValue == $oAuthorMbqEtUser->userId->oriValue) {
                        $oMbqEtPcMsg->oAuthorMbqEtUser = $oAuthorMbqEtUser;
                        break;
                    }
                }
            }
            /* common end */
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                $oMbqDataPage->datas = $objsMbqEtPcMsg;
                return $oMbqDataPage;
            } else {
                return $objsMbqEtPcMsg;
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one private conversation message by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdPcMsg' means init private conversation message by oStdPcMsg
     * @return  Mixed
     */
    public function initOMbqEtPcMsg($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdPcMsg') {
            $var->FormatBody = Gdn_Format::To($var->Body, $var->Format);
            $oMbqEtPcMsg = MbqMain::$oClk->newObj('MbqEtPcMsg');
            $oMbqEtPcMsg->msgId->setOriValue($var->MessageID);
            $oMbqEtPcMsg->convId->setOriValue($var->ConversationID);
            $oMbqEtPcMsg->msgContent->setOriValue($var->Body);
            $oMbqEtPcMsg->msgContent->setAppDisplayValue($var->FormatBody);
            $oMbqEtPcMsg->msgContent->setTmlDisplayValue($this->processPcMsgContentForDisplay($var->Body, true));
            $oMbqEtPcMsg->msgContent->setTmlDisplayValueNoHtml($this->processPcMsgContentForDisplay($var->Body, false));
            $oMbqEtPcMsg->msgAuthorId->setOriValue($var->InsertUserID);
            $oMbqEtPcMsg->postTime->setOriValue(strtotime($var->DateInserted));
            $oMbqEtPcMsg->mbqBind['oStdPcMsg'] = $var;
            return $oMbqEtPcMsg;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * process content for display in mobile app
     *
     * @params  String  $content
     * @params  Boolean  $returnHtml
     * @return  String
     */
    public function processPcMsgContentForDisplay($content, $returnHtml) {
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        return $oMbqRdEtForumPost->processContentForDisplay($content, $returnHtml);
    }
    
    /**
     * get_quote_conversation
     *
     * @param  Object  $oMbqEtPcMsg
     * @return  Mixed
     */
    public function getQuoteConversation($oMbqEtPcMsg) {
        /* modified from MbqRdEtForumPost::getQuotePostContent() */
        $content = preg_replace('/.*<a href="#tapatalkQuoteEnd"><\/a>/is', '', $oMbqEtPcMsg->msgContent->oriValue);
        $userDisplayName = $oMbqEtPcMsg->oAuthorMbqEtUser ? $oMbqEtPcMsg->oAuthorMbqEtUser->getDisplayName() : '';
        $ret = "[quote=\"$userDisplayName\"]".trim($content)."[/quote]\n\n";
        return $ret;
    }
  
}

?>