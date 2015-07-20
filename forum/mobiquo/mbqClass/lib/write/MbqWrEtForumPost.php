<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseWrEtForumPost');

/**
 * forum post write class
 * 
 * @since  2012-8-21
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqWrEtForumPost extends MbqBaseWrEtForumPost {
    
    public function __construct() {
    }
    
    /**
     * add forum post
     *
     * @param  Mixed  $var($oMbqEtForumPost or $objsMbqEtForumPost)
     */
    public function addMbqEtForumPost(&$var) {
        if (is_array($var)) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } else {
            $var->postContent->setOriValue($this->exttConvertContentForSave($var->postContent->oriValue));
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqPostController.php');
            $oExttMbqPostController = new ExttMbqPostController();
            $oExttMbqPostController->Initialize();
            $oExttMbqPostController->exttMbqComment('', $var);
        }
    }
    
    /**
     * modify forum post
     *
     * @param  Mixed  $var($oMbqEtForumPost or $objsMbqEtForumPost)
     */
    public function mdfMbqEtForumPost(&$var) {
        if (is_array($var)) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } else {
            $var->postContent->setOriValue($this->exttConvertContentForSave($var->postContent->oriValue));
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqPostController.php');
            $oExttMbqPostController = new ExttMbqPostController();
            $oExttMbqPostController->Initialize();
            if ($var->isDummyForumPost->oriValue) {
                $oExttMbqPostController->exttMbqEditDiscussion('', '', $var);
            } else {
                $oExttMbqPostController->exttMbqEditComment('', '', $var);
            }
        }
    }
    
    /**
     * convert content for save
     *
     * @param  String  $content
     * @return  String
     */
    public function exttConvertContentForSave($content) {
        return preg_replace('/\[img\]([^\[]*?)\[\/img\]/i', '<img src="$1" />', $content);
    }
  
}

?>