<?php

MainApp::$oClk->includeClass('MbqEtAtt');

/**
 * attachment class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtAtt extends MbqEtAtt {
    
    public $oMnEtUser; /* user who submit this attachment */
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtUser = NULL;
    }
    
    /**
     * judge is image
     *
     * @return  Boolean
     */
    public function isImage() {
        $suffix = strtolower(substr($this->uploadFileName->oriValue, strrpos($this->uploadFileName->oriValue, '.') + 1));
        if ($suffix == 'jpg' || $suffix == 'jpeg' || $suffix == 'png' || $suffix == 'bmp' || $suffix == 'gif') {
            return true;
        }
        return false;
    }
  
}

?>