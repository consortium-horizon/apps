<?php
/**
 * MnEtForumTopic read class
 * 
 * @since  2013-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumTopicRd Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * thread list
     *
     * @param  Array  $param
     * @return  Array
     */
    public function doThreadList($param) {
        $oMnCommon = MainApp::$oClk->newObj('MnCommon');
        $apiParam['get'] = array(
            'do' => 'forum'
        );
        $apiParam['get'] = array_merge($apiParam['get'], $param);
        $data = $oMnCommon->callApi($apiParam);
        if ($oMnCommon->callApiSuccess($data)) {
            $data = json_decode($data);
            $retData = array();
            $oMnEtForumInit = MainApp::$oClk->newObj('MnEtForumInit');
            $oMnEtForumTopicInit = MainApp::$oClk->newObj('MnEtForumTopicInit');
            if (property_exists($data, 'total')) {  //!!!
                $retData['total'] = $data->total;
            }
            if (property_exists($data, 'forum')) {
                $retData['forum'] = $oMnEtForumInit->initMnEtForumByRecord($data->forum);
            }
            if (property_exists($data, 'forums')) {
                $retData['forums'] = $oMnEtForumInit->initObjsMnEtForumByRecords($data->forums);
            }
            if (property_exists($data, 'topics')) {
                $retData['topics'] = $oMnEtForumTopicInit->initObjsMnEtForumTopicByRecords($data->topics);
            }
            $oMnDataPage = MainApp::$oClk->newObj('MnDataPage');
            //$oMnDataPage->initByTotal($param['page'], $param['perpage'], $retData['forum']->totalTopicNum->oriValue);
            $oMnDataPage->initByTotal($param['page'], $param['perpage'], $retData['total']);    //!!!
            $retData['oMnDataPage'] = $oMnDataPage;
            return $retData;
        } else {
            Error::alert(MPF_SITE_API_ERROR, __METHOD__ . ',line:' . __LINE__ . '.' . $oMnCommon->getApiErrorStr($data), ERR_HIGH);
        }
    }
    
    /**
     * get thread
     *
     * @param  Array  $param
     * @return  Array
     */
    public function doGetThread($param) {
        $oMnCommon = MainApp::$oClk->newObj('MnCommon');
        $apiParam['get'] = array(
            'do' => 'topic'
        );
        $apiParam['get'] = array_merge($apiParam['get'], $param);
        $data = $oMnCommon->callApi($apiParam);
        if ($oMnCommon->callApiSuccess($data)) {
            $data = json_decode($data);
            $retData = array();
            $oMnEtForumInit = MainApp::$oClk->newObj('MnEtForumInit');
            $oMnEtForumTopicInit = MainApp::$oClk->newObj('MnEtForumTopicInit');
            $oMnEtForumPostInit = MainApp::$oClk->newObj('MnEtForumPostInit');
            if (property_exists($data, 'navi')) {
                $retData['navi'] = $oMnEtForumInit->initObjsMnEtForumByRecords($data->navi);
            }
            if (property_exists($data, 'topic')) {
                $retData['topic'] = $oMnEtForumTopicInit->initMnEtForumTopicByRecord($data->topic);
            }
            if (property_exists($data, 'posts')) {
                $retData['posts'] = $oMnEtForumPostInit->initObjsMnEtForumPostByRecords($data->posts);
            }
            $oMnDataPage = MainApp::$oClk->newObj('MnDataPage');
            $oMnDataPage->initByTotal($param['page'], $param['perpage'], $retData['topic']->totalPostNum->oriValue);
            $retData['oMnDataPage'] = $oMnDataPage;
            return $retData;
        } else {
            Error::alert(MPF_SITE_API_ERROR, __METHOD__ . ',line:' . __LINE__ . '.' . $oMnCommon->getApiErrorStr($data), ERR_HIGH);
        }
    }
    
}

?>