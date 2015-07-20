<?php

defined('MBQ_IN_IT') or exit;

/**
 * m_delete_post action
 * 
 * @since  2012-9-26
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActMDeletePost extends MbqBaseAct {
    
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
        $postId = MbqMain::$input[0];
        $mode = (int) MbqMain::$input[1];
        if ($mode != 1 && $mode != 2) {
            MbqError::alert('', "Need valid mode!", '', MBQ_ERR_APP);
        }
        if ($mode == 2) {
            MbqError::alert('', "Sorry!Not support hard-delete a post!", '', MBQ_ERR_APP);
        }
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($postId, array('case' => 'byPostId'))) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclMDeletePost($oMbqEtForumPost, $mode)) {    //acl judge
                $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                $oMbqWrEtForumPost->mDeletePost($oMbqEtForumPost, $mode);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>