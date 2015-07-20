<?php
require_once('./AppConfig.php');

/** 
 * 主程序基类 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MainBase extends MainApp {
    
    public static $tapatalkPluginApiConfig;
    
    public function __construct() {
        parent::__construct();
        $this->regClass();
        self::$oAppConfig = self::$oClk->newObj('AppConfig');
        $cfg = self::$oAppConfig->getAppCfg();
        self::$oDb->prepareConnect($cfg['db']['dbName'], $cfg['db']['ip'], $cfg['db']['user'], $cfg['db']['pass']);
        /* 预包含的类 */
        self::$oClk->includeClass('FdtSite');
            /* from mobiquo */
        self::$oClk->includeClass('MbqValue');
        self::$oClk->includeClass('MbqBaseEntity');
        self::$oClk->includeClass('MbqBaseFdt');
        self::$oClk->includeClass('MbqFdtConfig');
        self::$oClk->includeClass('MbqFdtBase');
        self::$oClk->includeClass('MbqFdtUser');
        self::$oClk->includeClass('MbqFdtForum');
        self::$oClk->includeClass('MbqFdtPm');
        self::$oClk->includeClass('MbqFdtPc');
        self::$oClk->includeClass('MbqFdtLike');
        self::$oClk->includeClass('MbqFdtSubscribe');
        self::$oClk->includeClass('MbqFdtThank');
        self::$oClk->includeClass('MbqFdtFollow');
        self::$oClk->includeClass('MbqFdtFeed');
        self::$oClk->includeClass('MbqFdtAtt');
        self::$oClk->includeClass('MbqMain');
        self::$oClk->includeClass('MnDataPage');
        
        global $tapatalkPluginApiConfig;
        /* get config through api and culculate $tapatalkPluginApiConfig */
        if (!$tapatalkPluginApiConfig['nativeSiteUrl']) {
            $tapatalkPluginApiConfig['nativeSiteUrl'] = preg_replace('/(.*?)mobiquo\/minisite\/site/i', '$1', self::$oCf->currProtocol.'://'.MPF_C_MAIN_HOMEDOMAIN.MPF_C_PREURL); //!!!
        }
        if (!$tapatalkPluginApiConfig['url']) {
            $tapatalkPluginApiConfig['url'] = $tapatalkPluginApiConfig['nativeSiteUrl'].'mobiquo/tapatalk.php';
        }
        self::$tapatalkPluginApiConfig = & $tapatalkPluginApiConfig; //!!!
        $oMnCommon = MainApp::$oClk->newObj('MnCommon');
        $apiParam['get'] = array(
            'do' => 'config'
        );
        $data = $oMnCommon->callApi($apiParam);
        if ($oMnCommon->callApiSuccess($data)) {
            $data = json_decode($data);
            if (property_exists($data, 'version') && $data->version) {
                $tapatalkPluginApiConfig['apiConfig'] = $data;
            } else {
                Error::alert('needApiVersion', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find api version.", ERR_HIGH);
            }
        } else {
            Error::alert(MPF_SITE_API_ERROR, __METHOD__ . ',line:' . __LINE__ . '.' . $oMnCommon->getApiErrorStr($data), ERR_HIGH);
        }
        if (!$tapatalkPluginApiConfig['nativeSitePcModeUrl']) {
            //TODO cases
            if (self::apiIsVanilla2Site()) {
                $tapatalkPluginApiConfig['nativeSitePcModeUrl'] = $tapatalkPluginApiConfig['nativeSiteUrl'].'profile/nomobile';
            } elseif (self::apiIsVbulletin3Site()) {
                $tapatalkPluginApiConfig['nativeSitePcModeUrl'] = $tapatalkPluginApiConfig['nativeSiteUrl'].'index.php?exttMbqNoMobile=1';
                if ($tapatalkPluginApiConfig['apiConfig']->sys_version < '3.8.7') { //only support >= vb387p3 version
                    Error::alert('invalidSysVersion', "Sorry,minisite feature only support vb3.8.7 or higher version now.", ERR_HIGH);
                }
            } else {
                Error::alert('unknownTypeSite', __METHOD__ . ',line:' . __LINE__ . '.' . "Unknown type site.", ERR_HIGH);
            }
        }
        self::assign('tapatalkPluginApiConfig', self::$tapatalkPluginApiConfig);
    }
    
    /**
     * judge api is vanilla 2 site
     *
     * @return  Boolean
     */
    public static function apiIsVanilla2Site() {
        $arr = explode("_", self::$tapatalkPluginApiConfig['apiConfig']->version);
        return (($arr[0] === 'vn20') ? true : false);
    }
    
    /**
     * judge api is vBulletin 3 site
     *
     * @return  Boolean
     */
    public static function apiIsVbulletin3Site() {
        $arr = explode("_", self::$tapatalkPluginApiConfig['apiConfig']->version);
        return (($arr[0] === 'vb3x') ? true : false);
    }
    
    /**
     * 注册公共类，各个模块中都要用到的类（主要是些实体类及模块字段定义类和接口调用客户端类）
     */
    protected function regPublicClass() {
        parent::regPublicClass();
        $type = self::$oCt->getApp();
        /* SITE模块 */
            /* 实体类 */
                /* mobiquo entity class */
        self::$oClk->reg($type, 'MbqEtSysStatistics', MBQ_ENTITY_PATH.'MbqEtSysStatistics.php');
        self::$oClk->reg($type, 'MbqEtUser', MBQ_ENTITY_PATH.'MbqEtUser.php');
        self::$oClk->reg($type, 'MbqEtForum', MBQ_ENTITY_PATH.'MbqEtForum.php');
        self::$oClk->reg($type, 'MbqEtForumSmilie', MBQ_ENTITY_PATH.'MbqEtForumSmilie.php');
        self::$oClk->reg($type, 'MbqEtForumTopic', MBQ_ENTITY_PATH.'MbqEtForumTopic.php');
        self::$oClk->reg($type, 'MbqEtForumReportPost', MBQ_ENTITY_PATH.'MbqEtForumReportPost.php');
        self::$oClk->reg($type, 'MbqEtForumPost', MBQ_ENTITY_PATH.'MbqEtForumPost.php');
        self::$oClk->reg($type, 'MbqEtAtt', MBQ_ENTITY_PATH.'MbqEtAtt.php');
        self::$oClk->reg($type, 'MbqEtPc', MBQ_ENTITY_PATH.'MbqEtPc.php');
        self::$oClk->reg($type, 'MbqEtPcMsg', MBQ_ENTITY_PATH.'MbqEtPcMsg.php');
        self::$oClk->reg($type, 'MbqEtPcInviteParticipant', MBQ_ENTITY_PATH.'MbqEtPcInviteParticipant.php');
        self::$oClk->reg($type, 'MbqEtPm', MBQ_ENTITY_PATH.'MbqEtPm.php');
        self::$oClk->reg($type, 'MbqEtReportPm', MBQ_ENTITY_PATH.'MbqEtReportPm.php');
        self::$oClk->reg($type, 'MbqEtPmBox', MBQ_ENTITY_PATH.'MbqEtPmBox.php');
        self::$oClk->reg($type, 'MbqEtSubscribe', MBQ_ENTITY_PATH.'MbqEtSubscribe.php');
        self::$oClk->reg($type, 'MbqEtThank', MBQ_ENTITY_PATH.'MbqEtThank.php');
        self::$oClk->reg($type, 'MbqEtFollow', MBQ_ENTITY_PATH.'MbqEtFollow.php');
        self::$oClk->reg($type, 'MbqEtLike', MBQ_ENTITY_PATH.'MbqEtLike.php');
        self::$oClk->reg($type, 'MbqEtFeed', MBQ_ENTITY_PATH.'MbqEtFeed.php');
                /* entity class extended from mobiquo entity class */
        self::$oClk->reg($type, 'MnEtSysStatistics', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtSysStatistics.php');
        self::$oClk->reg($type, 'MnEtUser', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtUser.php');
        self::$oClk->reg($type, 'MnEtForum', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtForum.php');
        self::$oClk->reg($type, 'MnEtForumSmilie', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtForumSmilie.php');
        self::$oClk->reg($type, 'MnEtForumTopic', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtForumTopic.php');
        self::$oClk->reg($type, 'MnEtForumReportPost', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtForumReportPost.php');
        self::$oClk->reg($type, 'MnEtForumPost', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtForumPost.php');
        self::$oClk->reg($type, 'MnEtAtt', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtAtt.php');
        self::$oClk->reg($type, 'MnEtPc', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtPc.php');
        self::$oClk->reg($type, 'MnEtPcMsg', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtPcMsg.php');
        self::$oClk->reg($type, 'MnEtPcInviteParticipant', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtPcInviteParticipant.php');
        self::$oClk->reg($type, 'MnEtPm', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtPm.php');
        self::$oClk->reg($type, 'MnEtReportPm', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtReportPm.php');
        self::$oClk->reg($type, 'MnEtPmBox', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtPmBox.php');
        self::$oClk->reg($type, 'MnEtSubscribe', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtSubscribe.php');
        self::$oClk->reg($type, 'MnEtThank', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtThank.php');
        self::$oClk->reg($type, 'MnEtFollow', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtFollow.php');
        self::$oClk->reg($type, 'MnEtLike', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtLike.php');
        self::$oClk->reg($type, 'MnEtFeed', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnEtFeed.php');
            /* 模块字段定义类 */
        self::$oClk->reg($type, 'FdtSite', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_FDT) . 'FdtSite.php');
                /* from mobiquo */
        self::$oClk->reg($type, 'MbqValue', MBQ_FRAME_PATH.'MbqValue.php');
        self::$oClk->reg($type, 'MbqBaseEntity', MBQ_FRAME_PATH.'MbqBaseEntity.php');
        self::$oClk->reg($type, 'MbqBaseFdt', MBQ_FRAME_PATH.'MbqBaseFdt.php');
        self::$oClk->reg($type, 'MbqDataPage', MBQ_FRAME_PATH.'MbqDataPage.php');
                /* mobiquo fdt class */
        self::$oClk->reg($type, 'MbqFdtConfig', MBQ_FDT_PATH.'MbqFdtConfig.php');
        self::$oClk->reg($type, 'MbqFdtBase', MBQ_FDT_PATH.'MbqFdtBase.php');
        self::$oClk->reg($type, 'MbqFdtUser', MBQ_FDT_PATH.'MbqFdtUser.php');
        self::$oClk->reg($type, 'MbqFdtForum', MBQ_FDT_PATH.'MbqFdtForum.php');
        self::$oClk->reg($type, 'MbqFdtPm', MBQ_FDT_PATH.'MbqFdtPm.php');
        self::$oClk->reg($type, 'MbqFdtPc', MBQ_FDT_PATH.'MbqFdtPc.php');
        self::$oClk->reg($type, 'MbqFdtLike', MBQ_FDT_PATH.'MbqFdtLike.php');
        self::$oClk->reg($type, 'MbqFdtSubscribe', MBQ_FDT_PATH.'MbqFdtSubscribe.php');
        self::$oClk->reg($type, 'MbqFdtThank', MBQ_FDT_PATH.'MbqFdtThank.php');
        self::$oClk->reg($type, 'MbqFdtFollow', MBQ_FDT_PATH.'MbqFdtFollow.php');
        self::$oClk->reg($type, 'MbqFdtFeed', MBQ_FDT_PATH.'MbqFdtFeed.php');
        self::$oClk->reg($type, 'MbqFdtAtt', MBQ_FDT_PATH.'MbqFdtAtt.php');
            /* 客户端接口类 */
            /* other class */
        self::$oClk->reg($type, 'MbqMain', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MbqMain.php');
        self::$oClk->reg($type, 'MnDataPage', self::$oCf->getPath(MPF_C_APP_CLASS_PATH_ET).'MnDataPage.php');
    }
    
    /**
     * 注册应用类，模块中独有的类
     */
    protected function regAppClass() {
        parent::regAppClass();
        $type = self::$oCt->getApp();
        /* 实体读/写/初始化类 */
        self::$oClk->reg($type, 'MnEtAttInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtAttInit.php');
        self::$oClk->reg($type, 'MnEtAttRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtAttRd.php');
        self::$oClk->reg($type, 'MnEtFeedInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtFeedInit.php');
        self::$oClk->reg($type, 'MnEtFeedRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtFeedRd.php');
        self::$oClk->reg($type, 'MnEtFollowInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtFollowInit.php');
        self::$oClk->reg($type, 'MnEtFollowRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtFollowRd.php');
        self::$oClk->reg($type, 'MnEtForumInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumInit.php');
        self::$oClk->reg($type, 'MnEtForumRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumRd.php');
        self::$oClk->reg($type, 'MnEtForumPostInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumPostInit.php');
        self::$oClk->reg($type, 'MnEtForumPostRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumPostRd.php');
        self::$oClk->reg($type, 'MnEtForumReportPostInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumReportPostInit.php');
        self::$oClk->reg($type, 'MnEtForumReportPostRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumReportPostRd.php');
        self::$oClk->reg($type, 'MnEtForumSmilieInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumSmilieInit.php');
        self::$oClk->reg($type, 'MnEtForumSmilieRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumSmilieRd.php');
        self::$oClk->reg($type, 'MnEtForumTopicInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumTopicInit.php');
        self::$oClk->reg($type, 'MnEtForumTopicRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtForumTopicRd.php');
        self::$oClk->reg($type, 'MnEtLikeInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtLikeInit.php');
        self::$oClk->reg($type, 'MnEtLikeRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtLikeRd.php');
        self::$oClk->reg($type, 'MnEtPcInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcInit.php');
        self::$oClk->reg($type, 'MnEtPcRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcRd.php');
        self::$oClk->reg($type, 'MnEtPcInviteParticipantInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcInviteParticipantInit.php');
        self::$oClk->reg($type, 'MnEtPcInviteParticipantRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcInviteParticipantRd.php');
        self::$oClk->reg($type, 'MnEtPcMsgInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcMsgInit.php');
        self::$oClk->reg($type, 'MnEtPcMsgRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPcMsgRd.php');
        self::$oClk->reg($type, 'MnEtPmBoxInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPmBoxInit.php');
        self::$oClk->reg($type, 'MnEtPmBoxRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPmBoxRd.php');
        self::$oClk->reg($type, 'MnEtPmInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPmInit.php');
        self::$oClk->reg($type, 'MnEtPmRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtPmRd.php');
        self::$oClk->reg($type, 'MnEtReportPmInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtReportPmInit.php');
        self::$oClk->reg($type, 'MnEtReportPmRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtReportPmRd.php');
        self::$oClk->reg($type, 'MnEtSubscribeInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtSubscribeInit.php');
        self::$oClk->reg($type, 'MnEtSubscribeRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtSubscribeRd.php');
        self::$oClk->reg($type, 'MnEtSysStatisticsInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtSysStatisticsInit.php');
        self::$oClk->reg($type, 'MnEtSysStatisticsRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtSysStatisticsRd.php');
        self::$oClk->reg($type, 'MnEtThankInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtThankInit.php');
        self::$oClk->reg($type, 'MnEtThankRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtThankRd.php');
        self::$oClk->reg($type, 'MnEtUserInit', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtUserInit.php');
        self::$oClk->reg($type, 'MnEtUserRd', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnEtUserRd.php');
        /* 权限判断类 */
        /* 实体校验类 */
        /* 主程序代理类 */
        /* 工具类 */
        self::$oClk->reg($type, 'MnCommon', self::$oCf->getPath(MPF_C_APP_CLASS_PATH) . 'MnCommon.php');
        /* 服务端接口类 */
    }
    
    /**
     * 选择rewrite方式
     *
     * @param  String  $v  为'php'表示用php程序仿rewrite，为'normal'表示通过http服务器配置rewrite，为'none'表示不启用rewrite；
     */
    final protected function selectRewrite($v = 'php') {
        parent::selectRewrite($v);
    }
    
    /**
     * 判断是否已经登录
     *
     * @return  Boolean
     */
    final public static function hasLogin() {
        return false;
    }
    
}

?>