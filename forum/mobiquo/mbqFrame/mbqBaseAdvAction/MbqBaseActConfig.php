<?php

defined('MBQ_IN_IT') or exit;

/**
 * config action
 * 
 * @since  2013-5-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActConfig extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        $api = (int) MbqMain::$input['get']['api'];
        $cfg = MbqMain::$oMbqConfig->getAllCfg();
        foreach ($cfg as $moduleName => $module) {
            foreach ($module as $k => $v) {
                if ($k !== 'module_name' && $k != 'module_version' && $k != 'module_enable') {
                    if (isset($this->data[$k])) {
                        MbqError::alert('', "Find repeat config $k!");
                    } else {
                        if ($v->isAdvCfgValueType()|| $v->isAllCfgValueType()) {
                            if ($v->hasSetOriValue()) {
                                $this->data[$k] = $v->oriValue;
                            } else {
                                MbqError::alert('', "Need set config $k!");
                            }
                        }
                    }
                }
            }
        }
    }
  
}

?>