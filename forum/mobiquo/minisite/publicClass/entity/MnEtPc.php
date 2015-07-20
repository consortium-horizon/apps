<?php

MainApp::$oClk->includeClass('MbqEtPc');

/**
 * private conversation class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtPc extends MbqEtPc {
    
    public $objsRecipientMnEtUser;   /* users be invited to join this private conversation */
    public $objsMnEtPcMsg;
    public $oFirstMnEtPcMsg;
    
    public function __construct() {
        parent::__construct();
        
        $this->objsRecipientMnEtUser = array();
        $this->objsMnEtPcMsg = array();
        $this->oFirstMnEtPcMsg = NULL;
    }
  
}

?>