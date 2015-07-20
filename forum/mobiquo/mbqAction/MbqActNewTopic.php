<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActNewTopic');

/**
 * new_topic action
 * 
 * @since  2012-8-19
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActNewTopic extends MbqBaseActNewTopic {
    
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