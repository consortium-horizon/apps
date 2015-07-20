<?php

defined('MBQ_IN_IT') or exit;

/**
 * attachment read class
 * 
 * @since  2012-8-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtAtt extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return attachment api data
     *
     * @param  Object  $oMbqEtAtt
     * @return  Array
     */
    public function returnApiDataAttachment($oMbqEtAtt) {
        if (MbqMain::isJsonProtocol()) return $this->returnJsonApiDataAttachment($oMbqEtAtt);
        $data = array();
        if ($oMbqEtAtt->attId->hasSetOriValue()) {
            $data['attachment_id'] = (string) $oMbqEtAtt->attId->oriValue;
        }
        if ($oMbqEtAtt->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtAtt->groupId->oriValue;
        }
        if ($oMbqEtAtt->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtAtt->forumId->oriValue;
        }
        if ($oMbqEtAtt->postId->hasSetOriValue()) {
            $data['post_id'] = (string) $oMbqEtAtt->postId->oriValue;
        }
        if ($oMbqEtAtt->filtersSize->hasSetOriValue()) {
            $data['filters_size'] = (int) $oMbqEtAtt->filtersSize->oriValue;
            //$data['filesize'] = (int) $oMbqEtAtt->filtersSize->oriValue;
        }
        if ($oMbqEtAtt->contentType->hasSetOriValue()) {
            $data['content_type'] = (string) $oMbqEtAtt->contentType->oriValue;
        }
        if ($oMbqEtAtt->thumbnailUrl->hasSetOriValue()) {
            $data['thumbnail_url'] = (string) $oMbqEtAtt->thumbnailUrl->oriValue;
        }
        if ($oMbqEtAtt->url->hasSetOriValue()) {
            $data['url'] = (string) $oMbqEtAtt->url->oriValue;
        }
        return $data;
    }
    public function returnJsonApiDataAttachment($oMbqEtAtt) {
        $data = array();
        if ($oMbqEtAtt->attId->hasSetOriValue()) {
            $data['attachment_id'] = (string) $oMbqEtAtt->attId->oriValue;
        }
        if ($oMbqEtAtt->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtAtt->groupId->oriValue;
        }
        if ($oMbqEtAtt->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtAtt->forumId->oriValue;
        }
        if ($oMbqEtAtt->postId->hasSetOriValue()) {
            $data['post_id'] = (string) $oMbqEtAtt->postId->oriValue;
        }
        if ($oMbqEtAtt->filtersSize->hasSetOriValue()) {
            $data['filters_size'] = (int) $oMbqEtAtt->filtersSize->oriValue;
            //$data['filesize'] = (int) $oMbqEtAtt->filtersSize->oriValue;
        }
        if ($oMbqEtAtt->contentType->hasSetOriValue()) {
            $data['content_type'] = (string) $oMbqEtAtt->contentType->oriValue;
        }
        if ($oMbqEtAtt->thumbnailUrl->hasSetOriValue()) {
            $data['thumbnail_url'] = (string) $oMbqEtAtt->thumbnailUrl->oriValue;
        }
        if ($oMbqEtAtt->url->hasSetOriValue()) {
            $data['url'] = (string) $oMbqEtAtt->url->oriValue;
        }
        return $data;
    }
    /**
     * return attachment json api data
     *
     * @param  Object  $oMbqEtAtt
     * @return  Array
     */
    protected function returnAdvJsonApiDataAttachment($oMbqEtAtt) {
        $data = array();
        if ($oMbqEtAtt->attId->hasSetOriValue()) {
            $data['id'] = (string) $oMbqEtAtt->attId->oriValue;
        }
        if ($oMbqEtAtt->uploadFileName->hasSetOriValue()) {
            $data['name'] = (string) $oMbqEtAtt->uploadFileName->oriValue;
            $data['type'] = (string) MbqMain::$oMbqCm->getMimeType($oMbqEtAtt->uploadFileName->oriValue);
        }
        if ($oMbqEtAtt->filtersSize->hasSetOriValue()) {
            $data['size'] = (int) $oMbqEtAtt->filtersSize->oriValue;
        }
        if ($oMbqEtAtt->url->hasSetOriValue()) {
            $data['url'] = (string) $oMbqEtAtt->url->oriValue;
        }
        if ($oMbqEtAtt->thumbnailUrl->hasSetOriValue()) {
            $data['thumbnail'] = (string) $oMbqEtAtt->thumbnailUrl->oriValue;
        }
        return $data;
    }
    
    /**
     * return attachment array api data
     *
     * @param  Array  $objsMbqEtAtt
     * @return  Array
     */
    public function returnApiArrDataAttachment($objsMbqEtAtt) {
        $data = array();
        foreach ($objsMbqEtAtt as $oMbqEtAtt) {
            $data[] = $this->returnApiDataAttachment($oMbqEtAtt);
        }
        return $data;
    }
    
    /**
     * get attachment objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtAtt() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one attachment by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtAtt() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>