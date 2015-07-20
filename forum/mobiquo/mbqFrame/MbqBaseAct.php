<?php

defined('MBQ_IN_IT') or exit;

/**
 * action base class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseAct {
    
    public $data;   /* data need return.reference to MbqMain::$data */
    public $supportLevels;  /* the levels can be supported,default is level 3 */
    public $currLevel;  /* current supported level degree,default is level 3 */
    
    public function __construct() {
        $this->data = & MbqMain::$data;
        $this->supportLevels = array(3);
        $this->currLevel = 3;
    }
    
    /**
     * action implement
     */
    abstract protected function actionImplement();
  
}

?>