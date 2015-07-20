<?php

MainApp::$oClk->includeClass('MbqEtForum');

/**
 * forum class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForum extends MbqEtForum {
    
    public $oParentMnEtForum;  /* parent forum */
    public $objsSubMnEtForum;  /* sub forums */
    
    public function __construct() {
        parent::__construct();
        
        $this->oParentMnEtForum = NULL;
        $this->objsSubMnEtForum = array();
    }
    
    /**
     * get forum url in minisite
     */
    public function getForumUrl() {
        if (MPF_C_APPNAME == MPF_C_APPNAME_SITE) {
            if ($this->url->oriValue) {
                return $this->url->oriValue;
            } elseif ($this->isProtected->oriValue) {
                //TODO cases
                if (MainBase::apiIsVbulletin3Site()) {
                    return MainBase::$tapatalkPluginApiConfig['nativeSiteUrl'].'forumdisplay.php?f='.$this->forumId->oriValue.'&exttMbqNoMobile=1';
                } else {
                    Error::alert('unknownTypeSite', __METHOD__ . ',line:' . __LINE__ . '.' . "Unknown type site.", ERR_HIGH);
                }
            } else {
                return MainApp::$oCf->makeUrl(MPF_C_APPNAME_SITE, 'MainTopic.php', 'threadList', array('fid' => $this->forumId->oriValue), true);
            }
        } else {
            Error::alert('invalid app', __METHOD__ . ',line:' . __LINE__ . '.' . "Invalid app.", ERR_HIGH);
        }
    }
    
    /**
     * get forum url title
     */
    public function getForumUrlTitle() {
        if (MPF_C_APPNAME == MPF_C_APPNAME_SITE) {
            if ($this->isProtected->oriValue) {
                return '[This is a password protected forum,will link to the native forum page.]'.$this->forumName->oriValue;
            } else {
                return $this->forumName->oriValue;
            }
        } else {
            Error::alert('invalid app', __METHOD__ . ',line:' . __LINE__ . '.' . "Invalid app.", ERR_HIGH);
        }
    }
  
}

?>