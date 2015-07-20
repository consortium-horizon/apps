<?php

defined('MBQ_IN_IT') or exit;

/**
 * entity base class
 * 
 * @since  2012-7-9
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseEntity {
    
    public $mbqBind;   /* binded data var comes from application,array data type.only used for application logic. */
    public $extt;   /* you can define any properties you need in this extt array */
    
    public function __construct() {
        $this->mbqBind = array();
        $this->extt = array();
    }
  
}

?>