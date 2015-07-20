<?php

defined('MBQ_IN_IT') or exit;

define('MBQ_FRAMEWORK_VERSION', '0.2');
if (!defined('MBQ_PROTOCOL')) {
    define('MBQ_PROTOCOL', 'xmlrpc');   //default
}
/* error constant */
define('MBQ_ERR_TOP', 1);   /* the worst error that must stop the program immediately.we often use this constant in plugin development. */
define('MBQ_ERR_HIGH', 3);  /* serious error that must stop the program immediately for display in html page.we need not use this constant in plugin development,but can use it in other projects development perhaps. */
define('MBQ_ERR_NOT_SUPPORT', 5);  /* not support corresponding function error that must stop the program immediately. */
define('MBQ_ERR_APP', 7);   /* normal error that maked by program logic can be displayed,the program can works continue or not. */
define('MBQ_ERR_INFO', 9);  /* success info that maked by program logic can be displayed,the program can works continue or not. */
define('MBQ_ERR_TOP_NOIO', 11);  /* the worst error that must stop the program immediately and then the MbqIo is not valid,will output error info and stop the program immediately. */
define('MBQ_ERR_DEFAULT_INFO', 'You are not logged in or you do not have permission to do this action.');
define('MBQ_ERR_INFO_UNKNOWN_CASE', 'Unknown case value.');
define('MBQ_ERR_INFO_UNKNOWN_PNAME', 'Unknown property name.');
define('MBQ_ERR_INFO_UNKNOWN_ERROR', 'Unknown error.');
define('MBQ_ERR_INFO_NOT_ACHIEVE', 'Has not been achieved.');
define('MBQ_ERR_INFO_SAVE_FAIL', 'Can not save data.');
define('MBQ_ERR_INFO_DB_FAIL', 'Db error.');
define('MBQ_ERR_INFO_PARAMS_ERROR', 'Please input valid values.');
define('MBQ_ERR_INFO_NOT_SUPPORT', 'Your system or plugin looks do not support this feature.');
define('MBQ_ERR_INFO_NEED_PARAMS_FOR_REGISTRATION', 'Need valid info for registration.');
define('MBQ_ERR_INFO_REGISTRATION_FAIL', 'Registration failed.');
define('MBQ_ERR_INFO_REGISTRATION_SUCCESS', 'Registration success.');
define('MBQ_ERR_INFO_NOT_PERMIT_FOR_ADMIN', 'Can not do this action for administrator.');
define('MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE', 'This method need to be achieved in inherited classe.');
define('MBQ_RUNNING_NAMEPRE', 'mbqnamepre_'.mt_rand(2000000000, 2100000000).'_');   /* mobiquo running time vars name prefix,for example bbcode names. */
/* path constant */
require_once(MBQ_FRAME_PATH.'MbqError.php');
require_once(MBQ_FRAME_PATH.'MbqBaseMain.php');
require_once(MBQ_FRAME_PATH.'MbqClassLink.php');
require_once(MBQ_FRAME_PATH.'MbqValue.php');
define('MBQ_CLASS_PATH', MBQ_PATH.'mbqClass'.MBQ_DS);    /* class path */
define('MBQ_ENTITY_PATH', MBQ_FRAME_PATH.'entity'.MBQ_DS);    /* entity class path */
define('MBQ_FDT_PATH', MBQ_FRAME_PATH.'fdt'.MBQ_DS);    /* fdt class path */
define('MBQ_IO_PATH', MBQ_FRAME_PATH.'io'.MBQ_DS);    /* io class path */
define('MBQ_IO_HANDLE_PATH', MBQ_IO_PATH.'handle'.MBQ_DS);    /* io handle class path */
define('MBQ_LIB_PATH', MBQ_CLASS_PATH.'lib'.MBQ_DS);    /* lib class path */
define('MBQ_ACL_PATH', MBQ_LIB_PATH.'acl'.MBQ_DS);    /* acl class path */
define('MBQ_READ_PATH', MBQ_LIB_PATH.'read'.MBQ_DS);    /* read class path */
define('MBQ_WRITE_PATH', MBQ_LIB_PATH.'write'.MBQ_DS);    /* write class path */
define('MBQ_BASE_ACTION_PATH', MBQ_FRAME_PATH.'mbqBaseAction'.MBQ_DS);    /* base action class path */
define('MBQ_ACTION_PATH', MBQ_PATH.'mbqAction'.MBQ_DS);    /* action class path */
define('MBQ_BASE_ADV_ACTION_PATH', MBQ_FRAME_PATH.'mbqBaseAdvAction'.MBQ_DS);    /* base adv action class path */
define('MBQ_ADV_ACTION_PATH', MBQ_PATH.'mbqAdvAction'.MBQ_DS);    /* adv action class path */
define('MBQ_APPEXTENTION_PATH', MBQ_PATH.'appExtt'.MBQ_DS);    /* application extention path */
define('MBQ_CUSTOM_PATH', MBQ_PATH.'custom'.MBQ_DS);    /* user custom path */
define('MBQ_3RD_LIB_PATH', MBQ_FRAME_PATH.'3rdLib'.MBQ_DS);    /* 3rd lib path */
define('MBQ_BASE_LIB_PATH', MBQ_FRAME_PATH.'baseLib'.MBQ_DS);    /* base lib class path */
define('MBQ_BASE_ACL_PATH', MBQ_BASE_LIB_PATH.'baseAcl'.MBQ_DS);    /* bas acl class path */
define('MBQ_BASE_READ_PATH', MBQ_BASE_LIB_PATH.'baseRead'.MBQ_DS);    /* base read class path */
define('MBQ_BASE_WRITE_PATH', MBQ_BASE_LIB_PATH.'baseWrite'.MBQ_DS);    /* base write class path */
define('MBQ_BASE_PUSH_PATH', MBQ_FRAME_PATH.'basePush'.MBQ_DS);    /* base push class path */
define('MBQ_PUSH_PATH', MBQ_PATH.'push'.MBQ_DS);    /* push class path */

/**
 * plugin config base class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseConfig {
    
    /* plugin config,many dimensions array.will be calculated with $this->cfg default value and MbqMain::$customConfig and MbqMain::$oMbqAppEnv and the plugin support degree. */
    protected $cfg;

    public function __construct() {
        $this->cfg = array();
        $this->regClass();
        MbqMain::$oClk->includeClass('MbqError');
        MbqMain::$oClk->includeClass('MbqBaseAct');
        MbqMain::$oClk->includeClass('MbqBaseAppEnv');
        MbqMain::$oClk->includeClass('MbqBaseCm');
        MbqMain::$oClk->includeClass('MbqBaseIo');
        MbqMain::$oClk->includeClass('MbqValue');
        MbqMain::$oClk->includeClass('MbqBaseEntity');
        MbqMain::$oClk->includeClass('MbqBaseFdt');
        MbqMain::$oClk->includeClass('MbqBaseRd');
        MbqMain::$oClk->includeClass('MbqBaseWr');
        MbqMain::$oClk->includeClass('MbqBaseAcl');
        /* include fdt class */
        MbqMain::$oClk->includeClass('MbqFdtConfig');
        MbqMain::$oClk->includeClass('MbqFdtBase');
        MbqMain::$oClk->includeClass('MbqFdtUser');
        MbqMain::$oClk->includeClass('MbqFdtForum');
        MbqMain::$oClk->includeClass('MbqFdtPm');
        MbqMain::$oClk->includeClass('MbqFdtPc');
        MbqMain::$oClk->includeClass('MbqFdtLike');
        MbqMain::$oClk->includeClass('MbqFdtSubscribe');
        MbqMain::$oClk->includeClass('MbqFdtThank');
        MbqMain::$oClk->includeClass('MbqFdtFollow');
        MbqMain::$oClk->includeClass('MbqFdtFeed');
        MbqMain::$oClk->includeClass('MbqFdtAtt');
    }
    
    /**
     * regist classes
     */
    protected function regClass() {
        /* frame class */
        MbqMain::$oClk->reg('MbqBaseAct', MBQ_FRAME_PATH.'MbqBaseAct.php');
        MbqMain::$oClk->reg('MbqBaseAppEnv', MBQ_FRAME_PATH.'MbqBaseAppEnv.php');
        MbqMain::$oClk->reg('MbqBaseCm', MBQ_FRAME_PATH.'MbqBaseCm.php');
        MbqMain::$oClk->reg('MbqBaseConfig', MBQ_FRAME_PATH.'MbqBaseConfig.php');
        MbqMain::$oClk->reg('MbqBaseIo', MBQ_FRAME_PATH.'MbqBaseIo.php');
        MbqMain::$oClk->reg('MbqBaseMain', MBQ_FRAME_PATH.'MbqBaseMain.php');
        MbqMain::$oClk->reg('MbqClassLink', MBQ_FRAME_PATH.'MbqClassLink.php');
        MbqMain::$oClk->reg('MbqCookie', MBQ_FRAME_PATH.'MbqCookie.php');
        MbqMain::$oClk->reg('MbqError', MBQ_FRAME_PATH.'MbqError.php');
        MbqMain::$oClk->reg('MbqSession', MBQ_FRAME_PATH.'MbqSession.php');
        MbqMain::$oClk->reg('MbqValue', MBQ_FRAME_PATH.'MbqValue.php');
        MbqMain::$oClk->reg('MbqBaseEntity', MBQ_FRAME_PATH.'MbqBaseEntity.php');
        MbqMain::$oClk->reg('MbqBaseFdt', MBQ_FRAME_PATH.'MbqBaseFdt.php');
        MbqMain::$oClk->reg('MbqBaseRd', MBQ_FRAME_PATH.'MbqBaseRd.php');
        MbqMain::$oClk->reg('MbqBaseWr', MBQ_FRAME_PATH.'MbqBaseWr.php');
        MbqMain::$oClk->reg('MbqBaseAcl', MBQ_FRAME_PATH.'MbqBaseAcl.php');
        MbqMain::$oClk->reg('MbqDataPage', MBQ_FRAME_PATH.'MbqDataPage.php');
        /* other class */
        MbqMain::$oClk->reg('MbqCm', MBQ_PATH.'MbqCm.php');
        MbqMain::$oClk->reg('MbqAppEnv', MBQ_PATH.'MbqAppEnv.php');
        /* entity class */
        MbqMain::$oClk->reg('MbqEtSysStatistics', MBQ_ENTITY_PATH.'MbqEtSysStatistics.php');
        MbqMain::$oClk->reg('MbqEtUser', MBQ_ENTITY_PATH.'MbqEtUser.php');
        MbqMain::$oClk->reg('MbqEtForum', MBQ_ENTITY_PATH.'MbqEtForum.php');
        MbqMain::$oClk->reg('MbqEtForumSmilie', MBQ_ENTITY_PATH.'MbqEtForumSmilie.php');
        MbqMain::$oClk->reg('MbqEtForumTopic', MBQ_ENTITY_PATH.'MbqEtForumTopic.php');
        MbqMain::$oClk->reg('MbqEtForumReportPost', MBQ_ENTITY_PATH.'MbqEtForumReportPost.php');
        MbqMain::$oClk->reg('MbqEtForumPost', MBQ_ENTITY_PATH.'MbqEtForumPost.php');
        MbqMain::$oClk->reg('MbqEtAtt', MBQ_ENTITY_PATH.'MbqEtAtt.php');
        MbqMain::$oClk->reg('MbqEtPc', MBQ_ENTITY_PATH.'MbqEtPc.php');
        MbqMain::$oClk->reg('MbqEtPcMsg', MBQ_ENTITY_PATH.'MbqEtPcMsg.php');
        MbqMain::$oClk->reg('MbqEtPcInviteParticipant', MBQ_ENTITY_PATH.'MbqEtPcInviteParticipant.php');
        MbqMain::$oClk->reg('MbqEtPm', MBQ_ENTITY_PATH.'MbqEtPm.php');
        MbqMain::$oClk->reg('MbqEtReportPm', MBQ_ENTITY_PATH.'MbqEtReportPm.php');
        MbqMain::$oClk->reg('MbqEtPmBox', MBQ_ENTITY_PATH.'MbqEtPmBox.php');
        MbqMain::$oClk->reg('MbqEtSubscribe', MBQ_ENTITY_PATH.'MbqEtSubscribe.php');
        MbqMain::$oClk->reg('MbqEtThank', MBQ_ENTITY_PATH.'MbqEtThank.php');
        MbqMain::$oClk->reg('MbqEtFollow', MBQ_ENTITY_PATH.'MbqEtFollow.php');
        MbqMain::$oClk->reg('MbqEtLike', MBQ_ENTITY_PATH.'MbqEtLike.php');
        MbqMain::$oClk->reg('MbqEtFeed', MBQ_ENTITY_PATH.'MbqEtFeed.php');
        /* fdt class */
        MbqMain::$oClk->reg('MbqFdtConfig', MBQ_FDT_PATH.'MbqFdtConfig.php');
        MbqMain::$oClk->reg('MbqFdtBase', MBQ_FDT_PATH.'MbqFdtBase.php');
        MbqMain::$oClk->reg('MbqFdtUser', MBQ_FDT_PATH.'MbqFdtUser.php');
        MbqMain::$oClk->reg('MbqFdtForum', MBQ_FDT_PATH.'MbqFdtForum.php');
        MbqMain::$oClk->reg('MbqFdtPm', MBQ_FDT_PATH.'MbqFdtPm.php');
        MbqMain::$oClk->reg('MbqFdtPc', MBQ_FDT_PATH.'MbqFdtPc.php');
        MbqMain::$oClk->reg('MbqFdtLike', MBQ_FDT_PATH.'MbqFdtLike.php');
        MbqMain::$oClk->reg('MbqFdtSubscribe', MBQ_FDT_PATH.'MbqFdtSubscribe.php');
        MbqMain::$oClk->reg('MbqFdtThank', MBQ_FDT_PATH.'MbqFdtThank.php');
        MbqMain::$oClk->reg('MbqFdtFollow', MBQ_FDT_PATH.'MbqFdtFollow.php');
        MbqMain::$oClk->reg('MbqFdtFeed', MBQ_FDT_PATH.'MbqFdtFeed.php');
        MbqMain::$oClk->reg('MbqFdtAtt', MBQ_FDT_PATH.'MbqFdtAtt.php');
        /* base lib class and lib class */
            /* base read class */
        MbqMain::$oClk->reg('MbqBaseRdEtForum', MBQ_BASE_READ_PATH.'MbqBaseRdEtForum.php');
        MbqMain::$oClk->reg('MbqBaseRdEtUser', MBQ_BASE_READ_PATH.'MbqBaseRdEtUser.php');
        MbqMain::$oClk->reg('MbqBaseRdEtForumTopic', MBQ_BASE_READ_PATH.'MbqBaseRdEtForumTopic.php');
        MbqMain::$oClk->reg('MbqBaseRdEtForumPost', MBQ_BASE_READ_PATH.'MbqBaseRdEtForumPost.php');
        MbqMain::$oClk->reg('MbqBaseRdEtAtt', MBQ_BASE_READ_PATH.'MbqBaseRdEtAtt.php');
        MbqMain::$oClk->reg('MbqBaseRdForumSearch', MBQ_BASE_READ_PATH.'MbqBaseRdForumSearch.php');
        MbqMain::$oClk->reg('MbqBaseRdEtSysStatistics', MBQ_BASE_READ_PATH.'MbqBaseRdEtSysStatistics.php');
        MbqMain::$oClk->reg('MbqBaseRdEtThank', MBQ_BASE_READ_PATH.'MbqBaseRdEtThank.php');
        MbqMain::$oClk->reg('MbqBaseRdEtPc', MBQ_BASE_READ_PATH.'MbqBaseRdEtPc.php');
        MbqMain::$oClk->reg('MbqBaseRdEtPcMsg', MBQ_BASE_READ_PATH.'MbqBaseRdEtPcMsg.php');
        MbqMain::$oClk->reg('MbqBaseRdEtPm', MBQ_BASE_READ_PATH.'MbqBaseRdEtPm.php');
            /* read class */
        MbqMain::$oClk->reg('MbqRdEtForum', MBQ_READ_PATH.'MbqRdEtForum.php');
        MbqMain::$oClk->reg('MbqRdEtUser', MBQ_READ_PATH.'MbqRdEtUser.php');
        MbqMain::$oClk->reg('MbqRdEtForumTopic', MBQ_READ_PATH.'MbqRdEtForumTopic.php');
        MbqMain::$oClk->reg('MbqRdEtForumPost', MBQ_READ_PATH.'MbqRdEtForumPost.php');
        MbqMain::$oClk->reg('MbqRdEtAtt', MBQ_READ_PATH.'MbqRdEtAtt.php');
        MbqMain::$oClk->reg('MbqRdForumSearch', MBQ_READ_PATH.'MbqRdForumSearch.php');
        MbqMain::$oClk->reg('MbqRdEtSysStatistics', MBQ_READ_PATH.'MbqRdEtSysStatistics.php');
        MbqMain::$oClk->reg('MbqRdEtThank', MBQ_READ_PATH.'MbqRdEtThank.php');
        MbqMain::$oClk->reg('MbqRdEtPc', MBQ_READ_PATH.'MbqRdEtPc.php');
        MbqMain::$oClk->reg('MbqRdEtPcMsg', MBQ_READ_PATH.'MbqRdEtPcMsg.php');
        MbqMain::$oClk->reg('MbqRdEtPm', MBQ_READ_PATH.'MbqRdEtPm.php');
            /* base write class */
        MbqMain::$oClk->reg('MbqBaseWrEtForumTopic', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtForumTopic.php');
        MbqMain::$oClk->reg('MbqBaseWrEtForumPost', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtForumPost.php');
        MbqMain::$oClk->reg('MbqBaseWrEtAtt', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtAtt.php');
        MbqMain::$oClk->reg('MbqBaseWrEtForum', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtForum.php');
        MbqMain::$oClk->reg('MbqBaseWrEtUser', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtUser.php');
        MbqMain::$oClk->reg('MbqBaseWrEtPc', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtPc.php');
        MbqMain::$oClk->reg('MbqBaseWrEtPcMsg', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtPcMsg.php');
        MbqMain::$oClk->reg('MbqBaseWrEtPm', MBQ_BASE_WRITE_PATH.'MbqBaseWrEtPm.php');
            /* write class */
        MbqMain::$oClk->reg('MbqWrEtForumTopic', MBQ_WRITE_PATH.'MbqWrEtForumTopic.php');
        MbqMain::$oClk->reg('MbqWrEtForumPost', MBQ_WRITE_PATH.'MbqWrEtForumPost.php');
        MbqMain::$oClk->reg('MbqWrEtAtt', MBQ_WRITE_PATH.'MbqWrEtAtt.php');
        MbqMain::$oClk->reg('MbqWrEtForum', MBQ_WRITE_PATH.'MbqWrEtForum.php');
        MbqMain::$oClk->reg('MbqWrEtUser', MBQ_WRITE_PATH.'MbqWrEtUser.php');
        MbqMain::$oClk->reg('MbqWrEtPc', MBQ_WRITE_PATH.'MbqWrEtPc.php');
        MbqMain::$oClk->reg('MbqWrEtPcMsg', MBQ_WRITE_PATH.'MbqWrEtPcMsg.php');
        MbqMain::$oClk->reg('MbqWrEtPm', MBQ_WRITE_PATH.'MbqWrEtPm.php');
            /* base acl class */
        MbqMain::$oClk->reg('MbqBaseAclEtForum', MBQ_BASE_ACL_PATH.'MbqBaseAclEtForum.php');
        MbqMain::$oClk->reg('MbqBaseAclEtForumTopic', MBQ_BASE_ACL_PATH.'MbqBaseAclEtForumTopic.php');
        MbqMain::$oClk->reg('MbqBaseAclEtForumPost', MBQ_BASE_ACL_PATH.'MbqBaseAclEtForumPost.php');
        MbqMain::$oClk->reg('MbqBaseAclEtAtt', MBQ_BASE_ACL_PATH.'MbqBaseAclEtAtt.php');
        MbqMain::$oClk->reg('MbqBaseAclEtUser', MBQ_BASE_ACL_PATH.'MbqBaseAclEtUser.php');
        MbqMain::$oClk->reg('MbqBaseAclEtPc', MBQ_BASE_ACL_PATH.'MbqBaseAclEtPc.php');
        MbqMain::$oClk->reg('MbqBaseAclEtPcMsg', MBQ_BASE_ACL_PATH.'MbqBaseAclEtPcMsg.php');
        MbqMain::$oClk->reg('MbqBaseAclEtPm', MBQ_BASE_ACL_PATH.'MbqBaseAclEtPm.php');
            /* acl class */
        MbqMain::$oClk->reg('MbqAclEtForum', MBQ_ACL_PATH.'MbqAclEtForum.php');
        MbqMain::$oClk->reg('MbqAclEtForumTopic', MBQ_ACL_PATH.'MbqAclEtForumTopic.php');
        MbqMain::$oClk->reg('MbqAclEtForumPost', MBQ_ACL_PATH.'MbqAclEtForumPost.php');
        MbqMain::$oClk->reg('MbqAclEtAtt', MBQ_ACL_PATH.'MbqAclEtAtt.php');
        MbqMain::$oClk->reg('MbqAclEtUser', MBQ_ACL_PATH.'MbqAclEtUser.php');
        MbqMain::$oClk->reg('MbqAclEtPc', MBQ_ACL_PATH.'MbqAclEtPc.php');
        MbqMain::$oClk->reg('MbqAclEtPcMsg', MBQ_ACL_PATH.'MbqAclEtPcMsg.php');
        MbqMain::$oClk->reg('MbqAclEtPm', MBQ_ACL_PATH.'MbqAclEtPm.php');
        /* I/O class */
        MbqMain::$oClk->reg('MbqIo', MBQ_IO_PATH.'MbqIo.php');
        MbqMain::$oClk->reg('MbqIoHandleXmlrpc', MBQ_IO_HANDLE_PATH.'MbqIoHandleXmlrpc.php');
        MbqMain::$oClk->reg('MbqIoHandleJson', MBQ_IO_HANDLE_PATH.'MbqIoHandleJson.php');
        MbqMain::$oClk->reg('MbqIoHandleAdvJson', MBQ_IO_HANDLE_PATH.'MbqIoHandleAdvJson.php');
        /* base action class */
        MbqMain::$oClk->reg('MbqBaseActAvatar', MBQ_BASE_ACTION_PATH.'MbqBaseActAvatar.php');
        MbqMain::$oClk->reg('MbqBaseActGetConfig', MBQ_BASE_ACTION_PATH.'MbqBaseActGetConfig.php');
        MbqMain::$oClk->reg('MbqBaseActGetForum', MBQ_BASE_ACTION_PATH.'MbqBaseActGetForum.php');
        MbqMain::$oClk->reg('MbqBaseActGetTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetTopic.php');
        MbqMain::$oClk->reg('MbqBaseActGetThread', MBQ_BASE_ACTION_PATH.'MbqBaseActGetThread.php');
        MbqMain::$oClk->reg('MbqBaseActLogin', MBQ_BASE_ACTION_PATH.'MbqBaseActLogin.php');
        MbqMain::$oClk->reg('MbqBaseActGetInboxStat', MBQ_BASE_ACTION_PATH.'MbqBaseActGetInboxStat.php');
        MbqMain::$oClk->reg('MbqBaseActGetUnreadTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetUnreadTopic.php');
        MbqMain::$oClk->reg('MbqBaseActGetSubscribedTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetSubscribedTopic.php');
        MbqMain::$oClk->reg('MbqBaseActGetSubscribedForum', MBQ_BASE_ACTION_PATH.'MbqBaseActGetSubscribedForum.php');
        MbqMain::$oClk->reg('MbqBaseActGetUserTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetUserTopic.php');
        MbqMain::$oClk->reg('MbqBaseActGetUserReplyPost', MBQ_BASE_ACTION_PATH.'MbqBaseActGetUserReplyPost.php');
        MbqMain::$oClk->reg('MbqBaseActGetUserInfo', MBQ_BASE_ACTION_PATH.'MbqBaseActGetUserInfo.php');
        MbqMain::$oClk->reg('MbqBaseActNewTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActNewTopic.php');
        MbqMain::$oClk->reg('MbqBaseActReplyPost', MBQ_BASE_ACTION_PATH.'MbqBaseActReplyPost.php');
        MbqMain::$oClk->reg('MbqBaseActGetQuotePost', MBQ_BASE_ACTION_PATH.'MbqBaseActGetQuotePost.php');
        MbqMain::$oClk->reg('MbqBaseActMarkAllAsRead', MBQ_BASE_ACTION_PATH.'MbqBaseActMarkAllAsRead.php');
        MbqMain::$oClk->reg('MbqBaseActGetLatestTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetLatestTopic.php');
        MbqMain::$oClk->reg('MbqBaseActGetParticipatedTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActGetParticipatedTopic.php');
        MbqMain::$oClk->reg('MbqBaseActLogoutUser', MBQ_BASE_ACTION_PATH.'MbqBaseActLogoutUser.php');
        MbqMain::$oClk->reg('MbqBaseActSearchTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActSearchTopic.php');
        MbqMain::$oClk->reg('MbqBaseActSearchPost', MBQ_BASE_ACTION_PATH.'MbqBaseActSearchPost.php');
        MbqMain::$oClk->reg('MbqBaseActUploadAttach', MBQ_BASE_ACTION_PATH.'MbqBaseActUploadAttach.php');
        MbqMain::$oClk->reg('MbqBaseActGetRawPost', MBQ_BASE_ACTION_PATH.'MbqBaseActGetRawPost.php');
        MbqMain::$oClk->reg('MbqBaseActSaveRawPost', MBQ_BASE_ACTION_PATH.'MbqBaseActSaveRawPost.php');
        MbqMain::$oClk->reg('MbqBaseActGetOnlineUsers', MBQ_BASE_ACTION_PATH.'MbqBaseActGetOnlineUsers.php');
        MbqMain::$oClk->reg('MbqBaseActSubscribeForum', MBQ_BASE_ACTION_PATH.'MbqBaseActSubscribeForum.php');
        MbqMain::$oClk->reg('MbqBaseActUnsubscribeForum', MBQ_BASE_ACTION_PATH.'MbqBaseActUnsubscribeForum.php');
        MbqMain::$oClk->reg('MbqBaseActSubscribeTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActSubscribeTopic.php');
        MbqMain::$oClk->reg('MbqBaseActUnsubscribeTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActUnsubscribeTopic.php');
        MbqMain::$oClk->reg('MbqBaseActRemoveAttachment', MBQ_BASE_ACTION_PATH.'MbqBaseActRemoveAttachment.php');
        MbqMain::$oClk->reg('MbqBaseActReportPost', MBQ_BASE_ACTION_PATH.'MbqBaseActReportPost.php');
        MbqMain::$oClk->reg('MbqBaseActThankPost', MBQ_BASE_ACTION_PATH.'MbqBaseActThankPost.php');
        MbqMain::$oClk->reg('MbqBaseActMStickTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMStickTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMCloseTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMCloseTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMDeleteTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMDeleteTopic.php');
	MbqMain::$oClk->reg('MbqBaseActMCloseReport', MBQ_BASE_ACTION_PATH.'MbqBaseActMCloseReport.php');
	MbqMain::$oClk->reg('MbqBaseActMMergeTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMMergeTopic.php');
	MbqMain::$oClk->reg('MbqBaseActMUnbanUser', MBQ_BASE_ACTION_PATH.'MbqBaseActMUnbanUser.php');
	MbqMain::$oClk->reg('MbqBaseActMMergePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMMergePost.php');
	MbqMain::$oClk->reg('MbqBaseActMMarkAsSpam', MBQ_BASE_ACTION_PATH.'MbqBaseActMMarkAsSpam.php');
	MbqMain::$oClk->reg('MbqBaseActMGetReportPost', MBQ_BASE_ACTION_PATH.'MbqBaseActMGetReportPost.php');
	MbqMain::$oClk->reg('MbqBaseActMGetModerateTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMGetModerateTopic.php');
	MbqMain::$oClk->reg('MbqBaseActMGetModeratePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMGetModeratePost.php');
	MbqMain::$oClk->reg('MbqBaseActMGetDeleteTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMGetDeleteTopic.php');
	MbqMain::$oClk->reg('MbqBaseActMGetDeletePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMGetDeletePost.php');
        MbqMain::$oClk->reg('MbqBaseActMUndeleteTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMUndeleteTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMDeletePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMDeletePost.php');
        MbqMain::$oClk->reg('MbqBaseActMUndeletePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMUndeletePost.php');
        MbqMain::$oClk->reg('MbqBaseActMMoveTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMMoveTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMMovePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMMovePost.php');
        MbqMain::$oClk->reg('MbqBaseActMRenameTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMRenameTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMApproveTopic', MBQ_BASE_ACTION_PATH.'MbqBaseActMApproveTopic.php');
        MbqMain::$oClk->reg('MbqBaseActMApprovePost', MBQ_BASE_ACTION_PATH.'MbqBaseActMApprovePost.php');
        MbqMain::$oClk->reg('MbqBaseActMBanUser', MBQ_BASE_ACTION_PATH.'MbqBaseActMBanUser.php');
        MbqMain::$oClk->reg('MbqBaseActNewConversation', MBQ_BASE_ACTION_PATH.'MbqBaseActNewConversation.php');
        MbqMain::$oClk->reg('MbqBaseActReplyConversation', MBQ_BASE_ACTION_PATH.'MbqBaseActReplyConversation.php');
        MbqMain::$oClk->reg('MbqBaseActInviteParticipant', MBQ_BASE_ACTION_PATH.'MbqBaseActInviteParticipant.php');
        MbqMain::$oClk->reg('MbqBaseActGetConversations', MBQ_BASE_ACTION_PATH.'MbqBaseActGetConversations.php');
        MbqMain::$oClk->reg('MbqBaseActGetConversation', MBQ_BASE_ACTION_PATH.'MbqBaseActGetConversation.php');
        MbqMain::$oClk->reg('MbqBaseActGetQuoteConversation', MBQ_BASE_ACTION_PATH.'MbqBaseActGetQuoteConversation.php');
        MbqMain::$oClk->reg('MbqBaseActDeleteConversation', MBQ_BASE_ACTION_PATH.'MbqBaseActDeleteConversation.php');
        MbqMain::$oClk->reg('MbqBaseActReportPm', MBQ_BASE_ACTION_PATH.'MbqBaseActReportPm.php');
        MbqMain::$oClk->reg('MbqBaseActCreateMessage', MBQ_BASE_ACTION_PATH.'MbqBaseActCreateMessage.php');
        MbqMain::$oClk->reg('MbqBaseActGetBoxInfo', MBQ_BASE_ACTION_PATH.'MbqBaseActGetBoxInfo.php');
        MbqMain::$oClk->reg('MbqBaseActGetBox', MBQ_BASE_ACTION_PATH.'MbqBaseActGetBox.php');
        MbqMain::$oClk->reg('MbqBaseActGetMessage', MBQ_BASE_ACTION_PATH.'MbqBaseActGetMessage.php');
        MbqMain::$oClk->reg('MbqBaseActGetQuotePm', MBQ_BASE_ACTION_PATH.'MbqBaseActGetQuotePm.php');
        MbqMain::$oClk->reg('MbqBaseActDeleteMessage', MBQ_BASE_ACTION_PATH.'MbqBaseActDeleteMessage.php');
        MbqMain::$oClk->reg('MbqBaseActMarkPmUnread', MBQ_BASE_ACTION_PATH.'MbqBaseActMarkPmUnread.php');
	MbqMain::$oClk->reg('MbqBaseActMarkPmRead', MBQ_BASE_ACTION_PATH.'MbqBaseActMarkPmRead.php');
        MbqMain::$oClk->reg('MbqBaseActGetThreadByPost', MBQ_BASE_ACTION_PATH.'MbqBaseActGetThreadByPost.php');
        MbqMain::$oClk->reg('MbqBaseActGetThreadByUnread', MBQ_BASE_ACTION_PATH.'MbqBaseActGetThreadByUnread.php');
        MbqMain::$oClk->reg('MbqBaseActSignIn', MBQ_BASE_ACTION_PATH.'MbqBaseActSignIn.php');
        MbqMain::$oClk->reg('MbqBaseActPrefetchAccount', MBQ_BASE_ACTION_PATH.'MbqBaseActPrefetchAccount.php');
        MbqMain::$oClk->reg('MbqBaseActUpdatePassword', MBQ_BASE_ACTION_PATH.'MbqBaseActUpdatePassword.php');
        MbqMain::$oClk->reg('MbqBaseActUpdateEmail', MBQ_BASE_ACTION_PATH.'MbqBaseActUpdateEmail.php');
        MbqMain::$oClk->reg('MbqBaseActForgetPassword', MBQ_BASE_ACTION_PATH.'MbqBaseActForgetPassword.php');
        MbqMain::$oClk->reg('MbqBaseActRegister', MBQ_BASE_ACTION_PATH.'MbqBaseActRegister.php');
        /* action class */
        MbqMain::$oClk->reg('MbqActAvatar', MBQ_ACTION_PATH.'MbqActAvatar.php');
        MbqMain::$oClk->reg('MbqActGetConfig', MBQ_ACTION_PATH.'MbqActGetConfig.php');
        MbqMain::$oClk->reg('MbqActGetForum', MBQ_ACTION_PATH.'MbqActGetForum.php');
        MbqMain::$oClk->reg('MbqActGetTopic', MBQ_ACTION_PATH.'MbqActGetTopic.php');
        MbqMain::$oClk->reg('MbqActGetThread', MBQ_ACTION_PATH.'MbqActGetThread.php');
        MbqMain::$oClk->reg('MbqActLogin', MBQ_ACTION_PATH.'MbqActLogin.php');
        MbqMain::$oClk->reg('MbqActGetInboxStat', MBQ_ACTION_PATH.'MbqActGetInboxStat.php');
        MbqMain::$oClk->reg('MbqActGetUnreadTopic', MBQ_ACTION_PATH.'MbqActGetUnreadTopic.php');
        MbqMain::$oClk->reg('MbqActGetSubscribedTopic', MBQ_ACTION_PATH.'MbqActGetSubscribedTopic.php');
        MbqMain::$oClk->reg('MbqActGetSubscribedForum', MBQ_ACTION_PATH.'MbqActGetSubscribedForum.php');
        MbqMain::$oClk->reg('MbqActGetUserTopic', MBQ_ACTION_PATH.'MbqActGetUserTopic.php');
        MbqMain::$oClk->reg('MbqActGetUserReplyPost', MBQ_ACTION_PATH.'MbqActGetUserReplyPost.php');
        MbqMain::$oClk->reg('MbqActGetUserInfo', MBQ_ACTION_PATH.'MbqActGetUserInfo.php');
        MbqMain::$oClk->reg('MbqActNewTopic', MBQ_ACTION_PATH.'MbqActNewTopic.php');
        MbqMain::$oClk->reg('MbqActReplyPost', MBQ_ACTION_PATH.'MbqActReplyPost.php');
        MbqMain::$oClk->reg('MbqActGetQuotePost', MBQ_ACTION_PATH.'MbqActGetQuotePost.php');
        MbqMain::$oClk->reg('MbqActMarkAllAsRead', MBQ_ACTION_PATH.'MbqActMarkAllAsRead.php');
        MbqMain::$oClk->reg('MbqActGetLatestTopic', MBQ_ACTION_PATH.'MbqActGetLatestTopic.php');
        MbqMain::$oClk->reg('MbqActGetParticipatedTopic', MBQ_ACTION_PATH.'MbqActGetParticipatedTopic.php');
        MbqMain::$oClk->reg('MbqActLogoutUser', MBQ_ACTION_PATH.'MbqActLogoutUser.php');
        MbqMain::$oClk->reg('MbqActSearchTopic', MBQ_ACTION_PATH.'MbqActSearchTopic.php');
        MbqMain::$oClk->reg('MbqActSearchPost', MBQ_ACTION_PATH.'MbqActSearchPost.php');
        MbqMain::$oClk->reg('MbqActUploadAttach', MBQ_ACTION_PATH.'MbqActUploadAttach.php');
        MbqMain::$oClk->reg('MbqActGetRawPost', MBQ_ACTION_PATH.'MbqActGetRawPost.php');
        MbqMain::$oClk->reg('MbqActSaveRawPost', MBQ_ACTION_PATH.'MbqActSaveRawPost.php');
        MbqMain::$oClk->reg('MbqActGetOnlineUsers', MBQ_ACTION_PATH.'MbqActGetOnlineUsers.php');
        MbqMain::$oClk->reg('MbqActSubscribeForum', MBQ_ACTION_PATH.'MbqActSubscribeForum.php');
        MbqMain::$oClk->reg('MbqActUnsubscribeForum', MBQ_ACTION_PATH.'MbqActUnsubscribeForum.php');
        MbqMain::$oClk->reg('MbqActSubscribeTopic', MBQ_ACTION_PATH.'MbqActSubscribeTopic.php');
        MbqMain::$oClk->reg('MbqActUnsubscribeTopic', MBQ_ACTION_PATH.'MbqActUnsubscribeTopic.php');
        MbqMain::$oClk->reg('MbqActRemoveAttachment', MBQ_ACTION_PATH.'MbqActRemoveAttachment.php');
        MbqMain::$oClk->reg('MbqActReportPost', MBQ_ACTION_PATH.'MbqActReportPost.php');
        MbqMain::$oClk->reg('MbqActThankPost', MBQ_ACTION_PATH.'MbqActThankPost.php');
        MbqMain::$oClk->reg('MbqActMStickTopic', MBQ_ACTION_PATH.'MbqActMStickTopic.php');
        MbqMain::$oClk->reg('MbqActMCloseTopic', MBQ_ACTION_PATH.'MbqActMCloseTopic.php');
        MbqMain::$oClk->reg('MbqActMDeleteTopic', MBQ_ACTION_PATH.'MbqActMDeleteTopic.php');
	MbqMain::$oClk->reg('MbqActMCloseReport', MBQ_ACTION_PATH.'MbqActMCloseReport.php');
	MbqMain::$oClk->reg('MbqActMMergeTopic', MBQ_ACTION_PATH.'MbqActMMergeTopic.php');
	MbqMain::$oClk->reg('MbqActMUnbanUser', MBQ_ACTION_PATH.'MbqActMUnbanUser.php');
	MbqMain::$oClk->reg('MbqActMMergePost', MBQ_ACTION_PATH.'MbqActMMergePost.php');
	MbqMain::$oClk->reg('MbqActMMarkAsSpam', MBQ_ACTION_PATH.'MbqActMMarkAsSpam.php');
	MbqMain::$oClk->reg('MbqActMGetReportPost', MBQ_ACTION_PATH.'MbqActMGetReportPost.php');
	MbqMain::$oClk->reg('MbqActMGetModerateTopic', MBQ_ACTION_PATH.'MbqActMGetModerateTopic.php');
	MbqMain::$oClk->reg('MbqActMGetModeratePost', MBQ_ACTION_PATH.'MbqActMGetModeratePost.php');
	MbqMain::$oClk->reg('MbqActMGetDeleteTopic', MBQ_ACTION_PATH.'MbqActMGetDeleteTopic.php');
	MbqMain::$oClk->reg('MbqActMGetDeletePost', MBQ_ACTION_PATH.'MbqActMGetDeletePost.php');
        MbqMain::$oClk->reg('MbqActMUndeleteTopic', MBQ_ACTION_PATH.'MbqActMUndeleteTopic.php');
        MbqMain::$oClk->reg('MbqActMDeletePost', MBQ_ACTION_PATH.'MbqActMDeletePost.php');
        MbqMain::$oClk->reg('MbqActMUndeletePost', MBQ_ACTION_PATH.'MbqActMUndeletePost.php');
        MbqMain::$oClk->reg('MbqActMMoveTopic', MBQ_ACTION_PATH.'MbqActMMoveTopic.php');
        MbqMain::$oClk->reg('MbqActMMovePost', MBQ_ACTION_PATH.'MbqActMMovePost.php');
        MbqMain::$oClk->reg('MbqActMRenameTopic', MBQ_ACTION_PATH.'MbqActMRenameTopic.php');
        MbqMain::$oClk->reg('MbqActMApproveTopic', MBQ_ACTION_PATH.'MbqActMApproveTopic.php');
        MbqMain::$oClk->reg('MbqActMApprovePost', MBQ_ACTION_PATH.'MbqActMApprovePost.php');
        MbqMain::$oClk->reg('MbqActMBanUser', MBQ_ACTION_PATH.'MbqActMBanUser.php');
        MbqMain::$oClk->reg('MbqActNewConversation', MBQ_ACTION_PATH.'MbqActNewConversation.php');
        MbqMain::$oClk->reg('MbqActReplyConversation', MBQ_ACTION_PATH.'MbqActReplyConversation.php');
        MbqMain::$oClk->reg('MbqActInviteParticipant', MBQ_ACTION_PATH.'MbqActInviteParticipant.php');
        MbqMain::$oClk->reg('MbqActGetConversations', MBQ_ACTION_PATH.'MbqActGetConversations.php');
        MbqMain::$oClk->reg('MbqActGetConversation', MBQ_ACTION_PATH.'MbqActGetConversation.php');
        MbqMain::$oClk->reg('MbqActGetQuoteConversation', MBQ_ACTION_PATH.'MbqActGetQuoteConversation.php');
        MbqMain::$oClk->reg('MbqActDeleteConversation', MBQ_ACTION_PATH.'MbqActDeleteConversation.php');
        MbqMain::$oClk->reg('MbqActReportPm', MBQ_ACTION_PATH.'MbqActReportPm.php');
        MbqMain::$oClk->reg('MbqActCreateMessage', MBQ_ACTION_PATH.'MbqActCreateMessage.php');
        MbqMain::$oClk->reg('MbqActGetBoxInfo', MBQ_ACTION_PATH.'MbqActGetBoxInfo.php');
        MbqMain::$oClk->reg('MbqActGetBox', MBQ_ACTION_PATH.'MbqActGetBox.php');
        MbqMain::$oClk->reg('MbqActGetMessage', MBQ_ACTION_PATH.'MbqActGetMessage.php');
        MbqMain::$oClk->reg('MbqActGetQuotePm', MBQ_ACTION_PATH.'MbqActGetQuotePm.php');
        MbqMain::$oClk->reg('MbqActDeleteMessage', MBQ_ACTION_PATH.'MbqActDeleteMessage.php');
        MbqMain::$oClk->reg('MbqActMarkPmUnread', MBQ_ACTION_PATH.'MbqActMarkPmUnread.php');
	MbqMain::$oClk->reg('MbqActMarkPmRead', MBQ_ACTION_PATH.'MbqActMarkPmRead.php');
        MbqMain::$oClk->reg('MbqActGetThreadByPost', MBQ_ACTION_PATH.'MbqActGetThreadByPost.php');
        MbqMain::$oClk->reg('MbqActGetThreadByUnread', MBQ_ACTION_PATH.'MbqActGetThreadByUnread.php');
        MbqMain::$oClk->reg('MbqActSignIn', MBQ_ACTION_PATH.'MbqActSignIn.php');
        MbqMain::$oClk->reg('MbqActPrefetchAccount', MBQ_ACTION_PATH.'MbqActPrefetchAccount.php');
        MbqMain::$oClk->reg('MbqActUpdatePassword', MBQ_ACTION_PATH.'MbqActUpdatePassword.php');
        MbqMain::$oClk->reg('MbqActUpdateEmail', MBQ_ACTION_PATH.'MbqActUpdateEmail.php');
        MbqMain::$oClk->reg('MbqActForgetPassword', MBQ_ACTION_PATH.'MbqActForgetPassword.php');
        MbqMain::$oClk->reg('MbqActRegister', MBQ_ACTION_PATH.'MbqActRegister.php');
        /* base adv action class */
        MbqMain::$oClk->reg('MbqBaseActConfig', MBQ_BASE_ADV_ACTION_PATH.'MbqBaseActConfig.php');
        MbqMain::$oClk->reg('MbqBaseActForums', MBQ_BASE_ADV_ACTION_PATH.'MbqBaseActForums.php');
        MbqMain::$oClk->reg('MbqBaseActForum', MBQ_BASE_ADV_ACTION_PATH.'MbqBaseActForum.php');
        MbqMain::$oClk->reg('MbqBaseActTopic', MBQ_BASE_ADV_ACTION_PATH.'MbqBaseActTopic.php');
        /* adv action class */
        MbqMain::$oClk->reg('MbqActConfig', MBQ_ADV_ACTION_PATH.'MbqActConfig.php');
        MbqMain::$oClk->reg('MbqActForums', MBQ_ADV_ACTION_PATH.'MbqActForums.php');
        MbqMain::$oClk->reg('MbqActForum', MBQ_ADV_ACTION_PATH.'MbqActForum.php');
        MbqMain::$oClk->reg('MbqActTopic', MBQ_ADV_ACTION_PATH.'MbqActTopic.php');
        /* add extended classes */
        require_once(MBQ_CUSTOM_PATH.'customAddExttClass.php');
        
        /* add independent push class */
        require_once(MBQ_BASE_PUSH_PATH.'TapatalkBasePush.php');
        require_once(MBQ_PUSH_PATH.'TapatalkPush.php');
    }
    
    /**
     * init cfg
     */
    protected function initCfg() {
        /* base/user/forum/pm/pc/like/subscribe/thank/follow/feed */
        $this->cfg['base'] = $this->cfg['user'] = $this->cfg['forum'] = $this->cfg['pm'] = $this->cfg['pc'] = $this->cfg['like'] = $this->cfg['subscribe'] = $this->cfg['thank'] = $this->cfg['follow'] = $this->cfg['like'] = $this->cfg['feed'] = array();
      /* base config includes some global setting */
        $this->cfg['base']['sys_version'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.all'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.sys_version.default')));
        $this->cfg['base']['version'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.all'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.version.default'))); /* Tapatalk plugin version. Plugin developers: Set "version=dev" in order to get your development environment verified by the Tapatalk Network. */
        $this->cfg['base']['api_level'] = clone MbqMain::$simpleV;
        $this->cfg['base']['is_open'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.is_open.default')));  /* false: service is not available / true: service is available.  */
        $this->cfg['base']['inbox_stat'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.inbox_stat.default')));  /* Return "1" if the plugin support pm and subscribed topic unread number since last check time. */
        $this->cfg['base']['announcement'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.announcement.default')));    /* This instructs the app to hide/show the "Announcement" tab in topic view */
        $this->cfg['base']['disable_bbcode'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.disable_bbcode.default')));    /* disable bbcode function flag */
        $this->cfg['base']['push'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.push.default')));
        $this->cfg['base']['push_type'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.push_type.default')));
        $this->cfg['base']['api'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.base.api.default'))); /* Supported API Signature. */
      /* user */
        $this->cfg['user']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.module_enable.default')));    /* enable module flag */
        $this->cfg['user']['reg_url'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.reg_url.default')));     /* regist url on web page */
        $this->cfg['user']['guest_okay'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.guest_okay.default'))); /* false: guest access is not allowed / true: guess access is allowed. */
        $this->cfg['user']['anonymous'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.anonymous.default'))); /* Return 1 if plugin support anonymous login. */
        $this->cfg['user']['guest_whosonline'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.guest_whosonline.default'))); /* Return "1" if guest user can see who is currently online */
        $this->cfg['user']['avatar'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.avatar.default')));
        $this->cfg['user']['support_md5'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.support_md5.default')));     /* Return 1 to indicate the plugin support md5 password.  */
        $this->cfg['user']['get_smilies'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.get_smilies.default')));    /* Return 1 if the plugin support function get_smilies */
        $this->cfg['user']['advanced_online_users'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.advanced_online_users.default')));    /* Return 1 if the plugin support get_online_users with forum and thread filter, and also pagination */
        $this->cfg['user']['user_id'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.user_id.default')));    /* Indicate the function get_participated_topic / get_user_info / get_user_topic / get_user_reply_post support request with user id. */
        $this->cfg['user']['upload_avatar'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.upload_avatar.default')));    /* can upload avatar flag. */
        $this->cfg['user']['sign_in'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.sign_in.default')));
        $this->cfg['user']['inappreg'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.inappreg.default')));
        $this->cfg['user']['sso_login'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.sso_login.default')));
        $this->cfg['user']['sso_signin'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.sso_signin.default')));
        $this->cfg['user']['sso_register'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.sso_register.default')));
        $this->cfg['user']['native_register'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.user.native_register.default')));
      /* forum */
        $this->cfg['forum']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.module_enable.default')));
        $this->cfg['forum']['report_post'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.report_post.default')));    /* return 1 to indicate the plugin support report_post function. */
        $this->cfg['forum']['goto_post'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.goto_post.default')));
        $this->cfg['forum']['goto_unread'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.goto_unread.default')));
        $this->cfg['forum']['mark_read'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mark_read.default')));    /* This is to indicate if the forum system support function mark_all_as_read */
        $this->cfg['forum']['mark_forum'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mark_forum.default')));    /* This is to indicate if function mark_all_as_read can accept a parameter as forum id to mark a specified forum as read. */
        $this->cfg['forum']['no_refresh_on_post'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.no_refresh_on_post.default')));
        $this->cfg['forum']['subscribe_forum'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.subscribe_forum.default')));    /* this is to indicate this forum system supports "Sub-Forum Subscription" feature.  */
        $this->cfg['forum']['get_latest_topic'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_latest_topic.default')));
        $this->cfg['forum']['get_id_by_url'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_id_by_url.default')));
        $this->cfg['forum']['delete_reason'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.delete_reason.default')));
        $this->cfg['forum']['mod_approve'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mod_approve.default')));    /* This is to indicate this forum system supports a centralized view to list all topics / posts pending to be approved. */
        $this->cfg['forum']['mod_delete'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mod_delete.default')));    /* This is to indicate this forum system supports a centralized view to list all topics / posts that has been soft-deleted, allowing moderator to undelete topics / posts. */
        $this->cfg['forum']['mod_report'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mod_report.default')));    /* This is to indicate this forum system supports a centralized view to list all topics / posts that have been reported by the users and need moderator attention. */
        $this->cfg['forum']['guest_search'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.guest_search.default')));    /* Returns "1" if guest user can search in this forum without logging in. This is helpful as the app can enable search function under guest mode */
        $this->cfg['forum']['subscribe_load'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.subscribe_load.default')));    /* Return "1" if get_subscribed_topic support pagination.  */
        $this->cfg['forum']['subscribe_topic_mode'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.subscribe_topic_mode.default')));    /* It indicates the plugin support notification type option when do subscribe topic. */
        $this->cfg['forum']['subscribe_forum_mode'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.subscribe_forum_mode.default')));
        $this->cfg['forum']['min_search_length'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.min_search_length.default')));    /* Minimum string length for search_topic / search_post / search within forum. */
        $this->cfg['forum']['multi_quote'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.multi_quote.default')));    /* Return 1 is the plugin support multi quote. Check more in get_quote_post */
        $this->cfg['forum']['default_smilies'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.default_smilies.default')));    /* Forum default smilie set support. */
        $this->cfg['forum']['can_unread'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.can_unread.default')));    /* If it set to "0", indicate this forum does not support Unread feature. */
        $this->cfg['forum']['get_forum'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_forum.default')));    /* Return 1 if the plugin support function get_forum with two parameters for description control and sub forum id filter. */
        $this->cfg['forum']['get_topic_status'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_topic_status.default')));    /* Return 1 if the plugin support function get_topic_status */
        $this->cfg['forum']['get_participated_forum'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_participated_forum.default')));   /* Return 1 if the plugin support function get_participated_forum */
        $this->cfg['forum']['get_forum_status'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.get_forum_status.default')));    /* Return 1 if the plugin support function get_forum_status */
        $this->cfg['forum']['advanced_search'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.advanced_search.default')));    /* Return 1 if the plugin support function search */
        $this->cfg['forum']['mark_topic_read'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.mark_topic_read.default')));    /* Return 1 if the plugin support function mark_topic_read */
        $this->cfg['forum']['advanced_delete'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.advanced_delete.default')));    /* Return '1' if the plugin support both soft and hard delete. */
        $this->cfg['forum']['first_unread'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.first_unread.default')));    /* returns "0" if this forum system does not support First Unread feature. Assume "1" if missing. */
        $this->cfg['forum']['max_attachment'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.max_attachment.default')));    /* return the max attachment num can be submitted when submit topic/post. */
        $this->cfg['forum']['soft_delete'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.soft_delete.default')));    /* support soft delete flag. */
        $this->cfg['forum']['system'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.system.default'))); /* Forum system name */
        $this->cfg['forum']['offline'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.offline.default'))); /* Forum is offline or not */
        $this->cfg['forum']['private'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.private.default'))); /* Forum is private for member only or not */
        $this->cfg['forum']['charset'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.charset.default'))); /* Forum system charset */
        $this->cfg['forum']['timezone'] = MbqMain::$oClk->newObj('MbqValue', array('cfgValueType' => MbqBaseFdt::getFdt('MbqFdtConfig.otherDefine.cfgValueType.range.adv'), 'oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.forum.timezone.default'))); /* Forum system default timezone for guest. Sample: -1, 9.5 */
      /* pm */
        $this->cfg['pm']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['pm']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['pm']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pm.module_enable.default')));
        $this->cfg['pm']['report_pm'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pm.report_pm.default')));
        $this->cfg['pm']['pm_load'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pm.pm_load.default')));   /* Return "1" if get_box support pagination. */
        $this->cfg['pm']['mark_pm_unread'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pm.mark_pm_unread.default')));   /* Return 1 if the plugin support function mark_pm_unread */
      /* pc */
        $this->cfg['pc']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['pc']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['pc']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pc.module_enable.default')));
        $this->cfg['pc']['conversation'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.pc.conversation.default')));  /* Return 1 if the plugin support conversation pm */
      /* like */
        $this->cfg['like']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['like']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['like']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.like.module_enable.default')));
      /* subscribe */
        $this->cfg['subscribe']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['subscribe']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['subscribe']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.subscribe.module_enable.default')));
        $this->cfg['subscribe']['mass_subscribe'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.subscribe.mass_subscribe.default'))); /* Return 1 if the plugin support id 'ALL' in subscribe_topic / subscribe_forum / unsubscribe_topic / unsubscribe_forum */
      /* thank */
        $this->cfg['thank']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['thank']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['thank']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.thank.module_enable.default')));
      /* follow */
        $this->cfg['follow']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['follow']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['follow']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.follow.module_enable.default')));
      /* feed */
        $this->cfg['feed']['module_name'] = clone MbqMain::$simpleV;
        $this->cfg['feed']['module_version'] = clone MbqMain::$simpleV;
        $this->cfg['feed']['module_enable'] = MbqMain::$oClk->newObj('MbqValue', array('oriValue' => MbqBaseFdt::getFdt('MbqFdtConfig.feed.module_enable.default')));
    }
    
    /**
     * calculate the final config
     */
    protected function calCfg() {
        /* include custom config */
        require_once(MBQ_CUSTOM_PATH.'commonConfig.php');
        if (MbqMain::isXmlRpcProtocol() || MbqMain::isJsonProtocol()) {
            require_once(MBQ_CUSTOM_PATH.'customConfig.php');
        } elseif (MbqMain::isAdvJsonProtocol()) {
            require_once(MBQ_CUSTOM_PATH.'customAdvConfig.php');
        } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Invalid protocol.");
        }
      /* replace part config through MbqMain::$customConfig */
        foreach (MbqMain::$customConfig as $moduleKey => $module) {
            if (isset($this->cfg[$moduleKey])) {
                foreach ($module as $itemKey => $item) {
                    if (isset($this->cfg[$moduleKey][$itemKey])) {
                        $this->cfg[$moduleKey][$itemKey]->setOriValue($item);
                    } else {
                        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find module:$moduleKey,item:$itemKey in config!");
                    }
                }
            } else {
                MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find module:$moduleKey in config!");
            }
        }
    }
    
    /**
     * return $this->cfg.if not necessary,you should use $this->getCfg() method instead of $this->getAllCfg() method!
     *
     * @return  Array
     */
    public function getAllCfg() {
        return $this->cfg;
    }
    
    /**
     * return corresponding config value
     *
     * @param  String  $cfgPath
     * @return  fixed  if is set return the corresponding config value,else alert error info.
     */
    public function getCfg($cfgPath) {
        $arr = explode(".", $cfgPath);
        $count = count($arr);
        if (is_array($arr) && $count > 0) {
            switch ($count) {
                case 1:
                    if (isset($this->cfg[$arr[0]])) {
                        return $this->cfg[$arr[0]];
                    }
                break;
                case 2:
                    if (isset($this->cfg[$arr[0]][$arr[1]])) {
                        return $this->cfg[$arr[0]][$arr[1]];
                    }
                break;
                case 3:
                    if (isset($this->cfg[$arr[0]][$arr[1]][$arr[2]])) {
                        return $this->cfg[$arr[0]][$arr[1]][$arr[2]];
                    }
                break;
                case 4;
                    if (isset($this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                        return $this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]];
                    }
                break;
                case 5;
                    if (isset($this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]])) {
                        return $this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]];
                    }
                break;
                case 6;
                    if (isset($this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]])) {
                        return $this->cfg[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]];
                    }
                break;
                default:
                break;
            }
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find config $cfgPath!");
        } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find config $cfgPath!");
        }
    }
    
    /**
     * test plugin is open
     *
     * @return  Boolean
     */
    public function pluginIsOpen() {
        if (MbqMain::isXmlRpcProtocol())
            return ($this->cfg['base']['is_open']->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.base.is_open.range.yes')) ? true : false;
        elseif (MbqMain::isJsonProtocol())
            return ($this->cfg['forum']['offline']->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.offline.range.no')) ? true : false;
        else
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Invalid protocol.");
    }
    
    /**
     * test module is enable
     *
     * @param  String  module name
     * @return  Boolean
     */
    public function moduleIsEnable($moduleName) {
        if (isset($this->cfg[$moduleName])) {
            if (isset($this->cfg[$moduleName]['module_enable'])) {
                if ($this->cfg[$moduleName]['module_enable']->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.'.$moduleName.'.module_enable.range.enable')) {
                    return true;
                }
            }
            return false;
        } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Invalid module name $moduleName!");
        }
    }
  
}

?>