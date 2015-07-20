<?php
/**
 * MnEtUser init class
 * 
 * @since  2013-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtUserInit Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * init MnEtUser object by record
     *
     * @param  Object  $recordUser
     */
    public function initMnEtUserByRecord($recordUser) {
        $oMnEtUser = MainApp::$oClk->newObj('MnEtUser');
        if (property_exists($recordUser, 'id')) {
            $oMnEtUser->userId->setOriValue($recordUser->id);
        }
        if (property_exists($recordUser, 'name')) {
            $oMnEtUser->userName->setOriValue($recordUser->name);
        }
        if (property_exists($recordUser, 'avatar')) {
            $oMnEtUser->iconUrl->setOriValue($recordUser->avatar);
        } else {
            //TODO,need define default user icon
            
        }
        if (property_exists($recordUser, 'online')) {
            $oMnEtUser->isOnline->setOriValue($recordUser->online);
        }
        return $oMnEtUser;
    }
    
    /**
     * init objsMnEtUser by records
     *
     * @param  Array  $recordsUser
     */
    public function initObjsMnEtUserByRecords($recordsUser) {
        $objsMnEtUser = array();
        foreach ($recordsUser as $recordUser) {
            $objsMnEtUser[] = $this->initMnEtUserByRecord($recordUser);
        }
        return $objsMnEtUser;
    }
    
}

?>