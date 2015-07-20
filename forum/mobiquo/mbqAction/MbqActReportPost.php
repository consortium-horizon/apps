<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActReportPost');

/**
 * report_post action
 * 
 * @since  2012-9-23
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActReportPost extends MbqBaseActReportPost {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        //parent::actionImplement();
        $this->data['result'] = true;   //dummy report post
    }
  
}

?>