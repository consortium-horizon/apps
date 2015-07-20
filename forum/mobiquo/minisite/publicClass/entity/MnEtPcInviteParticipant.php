<?php

MainApp::$oClk->includeClass('MbqEtPcInviteParticipant');

/**
 * private conversation invite participant class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtPcInviteParticipant extends MbqEtPcInviteParticipant {
    
    public $oMnEtPc;
    public $objsMnEtUser;
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtPc = NULL;
        $this->objsMnEtUser = array();
    }
  
}

?>