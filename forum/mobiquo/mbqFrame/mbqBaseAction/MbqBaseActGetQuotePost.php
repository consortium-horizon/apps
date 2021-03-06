<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_quote_post action
 * 
 * @since  2012-8-12
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetQuotePost extends MbqBaseAct {
    
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
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($postId, array('case' => 'byPostId'))) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclGetQuotePost($oMbqEtForumPost)) {    //acl judge
                $this->data = $oMbqRdEtForumPost->returnApiDataForumPost($oMbqEtForumPost);
                $this->data['post_content'] = $oMbqRdEtForumPost->getQuotePostContent($oMbqEtForumPost);
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>