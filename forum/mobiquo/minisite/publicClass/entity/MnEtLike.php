<?php

MainApp::$oClk->includeClass('MbqEtLike');

/**
 * like class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtLike extends MbqEtLike {
    
    public $oMnEtUser; /* user who like this */
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtUser = NULL;
    }
  
}

?>