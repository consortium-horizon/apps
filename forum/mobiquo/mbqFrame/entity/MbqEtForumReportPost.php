<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum report post class
 * 
 * @since  2012-7-12
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtForumReportPost extends MbqBaseEntity {
    
    public $reportId;
    public $forumId;
    public $topicId;
    public $postId;
    public $reportByUserId;
    public $reportTime;         /* timestamp */
    public $reason;
    
    public function __construct() {
        parent::__construct();
        $this->reportId = clone MbqMain::$simpleV;
        $this->forumId = clone MbqMain::$simpleV;
        $this->topicId = clone MbqMain::$simpleV;
        $this->postId = clone MbqMain::$simpleV;
        $this->reportByUserId = clone MbqMain::$simpleV;
        $this->reportTime = clone MbqMain::$simpleV;
        $this->reason = clone MbqMain::$simpleV;
    }
  
}

?>