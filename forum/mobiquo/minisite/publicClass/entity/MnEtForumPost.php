<?php

MainApp::$oClk->includeClass('MbqEtForumPost');

/**
 * forum post class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumPost extends MbqEtForumPost {
    
    public $oMnEtForum;
    public $oMnEtForumTopic;
    public $oAuthorMnEtUser;
    public $objsMnEtAtt;           /* the all attachment objs in this post. */
    public $objsNotInContentMnEtAtt;   /* the attachement objs not in the content of this post. */
    public $objsMnEtThank;
    public $objsMnEtLike;
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtForum = NULL;
        $this->oMnEtForumTopic = NULL;
        $this->oAuthorMnEtUser = NULL;
        $this->objsMnEtAtt = array();
        $this->objsMnEtThank = array();
        $this->objsMnEtLike = array();
        $this->objsNotInContentMnEtAtt = array();
    }
  
}

?>