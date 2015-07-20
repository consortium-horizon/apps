<?php

MainApp::$oClk->includeClass('MbqEtForumTopic');

/**
 * forum topic class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumTopic extends MbqEtForumTopic {
    
    public $oMnEtForum;
    public $oFirstMnEtForumPost;
    public $oLastMnEtForumPost;
    /* the dummy first post of this topic when the topic itself is the first post.this post id format is:topic_topicId,for example:topic_123 */
    public $oDummyFirstMnEtForumPost;
    public $oAuthorMnEtUser;
    public $oLastReplyMnEtUser;
    public $objsMnEtAtt;
    public $objsMnEtForumPost;
    public $objsBreadcrumbMnEtForum;
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtForum = NULL;
        $this->oFirstMnEtForumPost = NULL;
        $this->oLastMnEtForumPost = NULL;
        $this->oDummyFirstMnEtForumPost = NULL;
        $this->oAuthorMnEtUser = NULL;
        $this->oLastReplyMnEtUser = NULL;
        $this->objsMnEtAtt = array();
        $this->objsMnEtForumPost = array();
        $this->objsBreadcrumbMnEtForum = NULL;
    }
  
}

?>