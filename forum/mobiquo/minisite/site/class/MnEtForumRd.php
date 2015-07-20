<?php
/**
 * MnEtForum read class
 * 
 * @since  2013-8-6
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumRd Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * get forum tree structure
     *
     * @return  Array
     */
    public function getForumTree() {
        $oMnCommon = MainApp::$oClk->newObj('MnCommon');
        $apiParam['get'] = array(
            'do' => 'forums'
        );
        $data = $oMnCommon->callApi($apiParam);
        if ($oMnCommon->callApiSuccess($data)) {
            $oMnEtForumInit = MainApp::$oClk->newObj('MnEtForumInit');
            $objsMnEtForum = $oMnEtForumInit->initObjsMnEtForumByRecords(json_decode($data));
            return $objsMnEtForum;
        } else {
            Error::alert(MPF_SITE_API_ERROR, __METHOD__ . ',line:' . __LINE__ . '.' . $oMnCommon->getApiErrorStr($data), ERR_HIGH);
        }
    }
    
}

?>