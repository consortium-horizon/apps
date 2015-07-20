<?php

defined('MBQ_IN_IT') or exit;

/**
 * remove_attachment action
 * 
 * @since  2012-9-19
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActRemoveAttachment extends MbqBaseAct {
    
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
        $attId = MbqMain::$input[0];
        $forumId = MbqMain::$input[1];
        $groupId = MbqMain::$input[2];
        $postId = MbqMain::$input[3];
        $oMbqRdEtAtt = MbqMain::$oClk->newObj('MbqRdEtAtt');
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if (($oMbqEtAtt = $oMbqRdEtAtt->initOMbqEtAtt($attId, array('case' => 'byAttId'))) && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtAtt = MbqMain::$oClk->newObj('MbqAclEtAtt');
            if ($oMbqAclEtAtt->canAclRemoveAttachment($oMbqEtAtt, $oMbqEtForum)) {   //acl judge
                $oMbqWrEtAtt = MbqMain::$oClk->newObj('MbqWrEtAtt');
                $oMbqWrEtAtt->deleteAttachment($oMbqEtAtt);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid attachment id or forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>