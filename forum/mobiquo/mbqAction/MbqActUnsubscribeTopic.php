<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActUnsubscribeTopic');

/**
 * unsubscribe_topic action
 * 
 * @since  2012-9-15
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActUnsubscribeTopic extends MbqBaseActUnsubscribeTopic {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        parent::actionImplement();
    }
  
}

?>