<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActSubscribeTopic');

/**
 * subscribe_topic action
 * 
 * @since  2012-9-15
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActSubscribeTopic extends MbqBaseActSubscribeTopic {
    
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