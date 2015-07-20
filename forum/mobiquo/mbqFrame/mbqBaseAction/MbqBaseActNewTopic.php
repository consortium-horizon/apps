<?php

defined('MBQ_IN_IT') or exit;

/**
 * new_topic action
 * 
 * @since  2012-8-19
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActNewTopic extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $oMbqEtForumTopic = MbqMain::$oClk->newObj('MbqEtForumTopic');
        $oMbqEtForumTopic->forumId->setOriValue(MbqMain::$input[0]);
        $oMbqEtForumTopic->topicTitle->setOriValue(MbqMain::$input[1]);
        $oMbqEtForumTopic->topicContent->setOriValue(MbqMain::$input[2]);
        $oMbqEtForumTopic->prefixId->setOriValue(MbqMain::$input[3]);
        if (isset(MbqMain::$input[4])) $oMbqEtForumTopic->attachmentIdArray->setOriValue((array) MbqMain::$input[4]);
        if (isset(MbqMain::$input[5])) $oMbqEtForumTopic->groupId->setOriValue(MbqMain::$input[5]);
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($oMbqEtForumTopic->forumId->oriValue), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            if ($oMbqAclEtForumTopic->canAclNewTopic($oMbqEtForum)) {    //acl judge
                $oMbqWrEtForumTopic = MbqMain::$oClk->newObj('MbqWrEtForumTopic');
                $oMbqWrEtForumTopic->addMbqEtForumTopic($oMbqEtForumTopic);
                $state = $oMbqEtForumTopic->state->oriValue;
                $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
                $this->data['result'] = true;
                $data1 = $oMbqRdEtForumTopic->returnApiDataForumTopic($oMbqEtForumTopic);
                MbqMain::$oMbqCm->mergeApiData($this->data, $data1);
                $this->data['state'] = $oMbqEtForumTopic->state->oriValue;
                $oTapatalkPush = new TapatalkPush();
                $oTapatalkPush->callMethod('doPushNewTopic', array(
                    'oMbqEtForumTopic' => $oMbqEtForumTopic
                ));
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>