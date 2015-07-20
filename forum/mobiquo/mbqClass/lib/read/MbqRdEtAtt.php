<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtAtt');

/**
 * attachment read class
 * 
 * @since  2012-8-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtAtt extends MbqBaseRdEtAtt {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtAtt, $pName, $mbqOpt = array()) {
        switch ($pName) {
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get attachment objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byForumPostIds' means get data by forum post ids.$var is the ids.
     * @return  Mixed
     */
    public function getObjsMbqEtAtt($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byForumPostIds') {
            $postIds = $var;
            $objsMbqEtAtt = array();
            if (MbqMain::$oMbqAppEnv->check3rdPluginEnabled('FileUpload')) {
                $oMediaModel = new MediaModel();
                if (count($var) == 1 && !is_numeric($var[0])) { //topic attachment
                    $arr = explode('_', $var[0]);
                    $topicId = $arr[1];
                    $data = $oMediaModel->PreloadDiscussionMedia($topicId, array());
                } else {    //post attachment
                    $data = $oMediaModel->PreloadDiscussionMedia(NULL, $var);
                }
                foreach ($data->Result() as $oStdAtt) {
                    $objsMbqEtAtt[] = $this->initOMbqEtAtt($oStdAtt, array('case' => 'oStdAtt'));
                }
            }
            return $objsMbqEtAtt;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one attachment by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdAtt' means init attachment by oStdAtt
     * @return  Mixed
     */
    public function initOMbqEtAtt($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdAtt') {
            if (MbqMain::$oMbqAppEnv->check3rdPluginEnabled('FileUpload')) {
                $oMbqEtAtt = MbqMain::$oClk->newObj('MbqEtAtt');
                $oMbqEtAtt->attId->setOriValue($var->MediaID);
                if ($var->ForeignTable == 'comment') {
                    $oMbqEtAtt->postId->setOriValue($var->ForeignID);
                } elseif ($var->ForeignTable == 'discussion') {
                    $oMbqEtAtt->postId->setOriValue('topic_'.$var->ForeignID);
                }
                $oMbqEtAtt->filtersSize->setOriValue($var->Size);
                $oMbqEtAtt->uploadFileName->setOriValue($var->Name);
                $oMbqEtAtt->attType->setOriValue(MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.attType.range.forumPostAtt'));
                $ext = MbqMain::$oMbqCm->getFileExtension($oMbqEtAtt->uploadFileName->oriValue);
                if ($ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png' || $ext == 'jpg') {
                    $contentType = MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.contentType.range.image');
                } elseif ($ext == 'pdf') {
                    $contentType = MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.contentType.range.pdf');
                } else {
                    $contentType = MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.contentType.range.other');
                }     
                $oMbqEtAtt->contentType->setOriValue($contentType);
                $oMbqEtAtt->thumbnailUrl->setOriValue(MbqMain::$oMbqAppEnv->rootUrl.'uploads'.$var->Path);
                $oMbqEtAtt->url->setOriValue(MbqMain::$oMbqAppEnv->rootUrl.'uploads'.$var->Path);
                $oMbqEtAtt->userId->setOriValue($var->InsertUserID);
                $oMbqEtAtt->mbqBind['oStdAtt'] = $var;
            }
            return $oMbqEtAtt;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
  
}

?>