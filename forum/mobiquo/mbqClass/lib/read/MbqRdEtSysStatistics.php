<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtSysStatistics');

/**
 * system statistics read class
 * 
 * @since  2012-9-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtSysStatistics extends MbqBaseRdEtSysStatistics {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtSysStatistics, $pName, $mbqOpt = array()) {
        switch ($pName) {
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * init system statistics by condition
     *
     * @return  Object
     */
    public function initOMbqEtSysStatistics() {
        $oMbqEtSysStatistics = MbqMain::$oClk->newObj('MbqEtSysStatistics');
        if (!MbqMain::$oMbqAppEnv->check3rdPluginEnabled('WhosOnline')) {
            $oMbqEtSysStatistics->forumTotalOnline->setOriValue(0);
            $oMbqEtSysStatistics->forumGuestOnline->setOriValue(0);
        } else {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqWhosOnlineModule.php');
            $oExttMbqWhosOnlineModule = new ExttMbqWhosOnlineModule();
            $oExttMbqWhosOnlineModule->GetData();
            $oMbqEtSysStatistics->forumTotalOnline->setOriValue($oExttMbqWhosOnlineModule->exttMbqGetUsers()->NumRows());
            $oMbqEtSysStatistics->forumGuestOnline->setOriValue(0);
        }
        return $oMbqEtSysStatistics;
    }
  
}

?>