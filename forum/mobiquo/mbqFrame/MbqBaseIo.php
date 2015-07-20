<?php

defined('MBQ_IN_IT') or exit;

/**
 * input/output base class
 * 
 * @since  2012-7-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseIo {
    
    protected $protocol;    /* xmlrpc/json */
    protected $module;  /* module name */
    protected $cmd;   /* action command name,must unique in all action. */
    protected $input;   /* input params array */
    
    protected $data;    /* data need return */
    
    public function __construct() {
        $this->input = array();
        $this->data = array();
        MbqMain::$protocol = MbqBaseMain::$protocol = &$this->protocol; /* fixed bug:MbqBaseMain::$protocol is invalid when call MbqMain::isXmlRpcProtocol()/MbqMain::isJsonProtocol() */
        MbqMain::$module = &$this->module;
        MbqMain::$cmd = &$this->cmd;
        MbqMain::$input = &$this->input;
        
        MbqMain::$data = &$this->data;
    }
    
    /**
     * input data
     */
    abstract public function input();
    
    /**
     * output data
     */
    abstract public function output();
  
}

?>