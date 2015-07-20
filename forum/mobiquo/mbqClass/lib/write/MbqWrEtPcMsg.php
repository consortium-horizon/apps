<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseWrEtPcMsg');

/**
 * private conversation message write class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqWrEtPcMsg extends MbqBaseWrEtPcMsg {
    
    public function __construct() {
    }
    
    /**
     * add private conversation message
     *
     * @param  Object  $oMbqEtPcMsg
     * @param  Object  $oMbqEtPc
     */
    public function addMbqEtPcMsg(&$oMbqEtPcMsg, $oMbqEtPc) {
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqConversationMessagesController.php');
        $oExttMbqConversationMessagesController = new ExttMbqConversationMessagesController();
        $oExttMbqConversationMessagesController->Initialize();
        $oExttMbqConversationMessagesController->exttMbqAddMessage('', $oMbqEtPcMsg, $oMbqEtPc);
    }
  
}

?>