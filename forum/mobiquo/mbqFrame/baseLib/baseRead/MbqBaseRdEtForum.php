<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum read class
 * 
 * @since  2012-8-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtForum extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return forum api data
     *
     * @param  Object  $oMbqEtForum
     * @return  Array
     */
    public function returnApiDataForum($oMbqEtForum) {
        if (MbqMain::isJsonProtocol()) return $this->returnJsonApiDataForum($oMbqEtForum);
        $data = array();
        if ($oMbqEtForum->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForum->forumId->oriValue;
        }
        if ($oMbqEtForum->forumName->hasSetOriValue()) {
            $data['forum_name'] = (string) $oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForum->description->hasSetOriValue()) {
            $data['description'] = (string) $oMbqEtForum->description->oriValue;
        }
        if ($oMbqEtForum->totalTopicNum->hasSetOriValue()) {
            $data['total_topic_num'] = (int) $oMbqEtForum->totalTopicNum->oriValue;
        }
        if ($oMbqEtForum->parentId->hasSetOriValue()) {
            $data['parent_id'] = (string) $oMbqEtForum->parentId->oriValue;
        }
        if ($oMbqEtForum->logoUrl->hasSetOriValue()) {
            $data['logo_url'] = (string) $oMbqEtForum->logoUrl->oriValue;
        }
        if ($oMbqEtForum->newPost->hasSetOriValue()) {
            $data['new_post'] = (boolean) $oMbqEtForum->newPost->oriValue;
        }
        if ($oMbqEtForum->isProtected->hasSetOriValue()) {
            $data['is_protected'] = (boolean) $oMbqEtForum->isProtected->oriValue;
        }
        if ($oMbqEtForum->isSubscribed->hasSetOriValue()) {
            $data['is_subscribed'] = (boolean) $oMbqEtForum->isSubscribed->oriValue;
        }
        if ($oMbqEtForum->canSubscribe->hasSetOriValue()) {
            $data['can_subscribe'] = (boolean) $oMbqEtForum->canSubscribe->oriValue;
        } else {
            $data['can_subscribe'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canSubscribe.default');
        }
        if ($oMbqEtForum->url->hasSetOriValue()) {
            $data['url'] = (string) $oMbqEtForum->url->oriValue;
        }
        if ($oMbqEtForum->subOnly->hasSetOriValue()) {
            $data['sub_only'] = (boolean) $oMbqEtForum->subOnly->oriValue;
        }
        if ($oMbqEtForum->canPost->hasSetOriValue()) {
            $data['can_post'] = (boolean) $oMbqEtForum->canPost->oriValue;
        } else {
            $data['can_post'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canPost.default');
        }
        if ($oMbqEtForum->unreadStickyCount->hasSetOriValue()) {
            $data['unread_sticky_count'] = (int) $oMbqEtForum->unreadStickyCount->oriValue;
        }
        if ($oMbqEtForum->unreadAnnounceCount->hasSetOriValue()) {
            $data['unread_announce_count'] = (int) $oMbqEtForum->unreadAnnounceCount->oriValue;
        }
        if ($oMbqEtForum->requirePrefix->hasSetOriValue()) {
            $data['require_prefix'] = (boolean) $oMbqEtForum->requirePrefix->oriValue;
        }
        if ($oMbqEtForum->prefixes->hasSetOriValue()) {
            $tempArr = array();
            foreach ($oMbqEtForum->prefixes as $prefix) {
                $tempArr[] = array('prefix_id' => (string) $prefix['id'], 'prefix_display_name' => (string) $prefix['name']);
            }
            $data['prefixes'] = (array) $tempArr;
        }
        if ($oMbqEtForum->canUpload->hasSetOriValue()) {
            $data['can_upload'] = (boolean) $oMbqEtForum->canUpload->oriValue;
        } else {
            $data['can_upload'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canUpload.default');
        }
        $data['child'] = array();
        $this->recurMakeApiTreeDataForum($data['child'], $oMbqEtForum->objsSubMbqEtForum);
        return $data;
    }
    public function returnJsonApiDataForum($oMbqEtForum) {
        $data = array();
        if ($oMbqEtForum->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForum->forumId->oriValue;
        }
        if ($oMbqEtForum->forumName->hasSetOriValue()) {
            $data['forum_name'] = (string) $oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForum->description->hasSetOriValue()) {
            $data['description'] = (string) $oMbqEtForum->description->oriValue;
        }
        if ($oMbqEtForum->totalTopicNum->hasSetOriValue()) {
            $data['total_topic_num'] = (int) $oMbqEtForum->totalTopicNum->oriValue;
        }
        if ($oMbqEtForum->parentId->hasSetOriValue()) {
            $data['parent_id'] = (string) $oMbqEtForum->parentId->oriValue;
        }
        if ($oMbqEtForum->logoUrl->hasSetOriValue()) {
            $data['logo_url'] = (string) $oMbqEtForum->logoUrl->oriValue;
        }
        if ($oMbqEtForum->newPost->hasSetOriValue()) {
            $data['new_post'] = (boolean) $oMbqEtForum->newPost->oriValue;
        }
        if ($oMbqEtForum->isProtected->hasSetOriValue()) {
            $data['is_protected'] = (boolean) $oMbqEtForum->isProtected->oriValue;
        }
        if ($oMbqEtForum->isSubscribed->hasSetOriValue()) {
            $data['is_subscribed'] = (boolean) $oMbqEtForum->isSubscribed->oriValue;
        }
        if ($oMbqEtForum->canSubscribe->hasSetOriValue()) {
            $data['can_subscribe'] = (boolean) $oMbqEtForum->canSubscribe->oriValue;
        } else {
            $data['can_subscribe'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canSubscribe.default');
        }
        if ($oMbqEtForum->url->hasSetOriValue()) {
            $data['url'] = (string) $oMbqEtForum->url->oriValue;
        }
        if ($oMbqEtForum->subOnly->hasSetOriValue()) {
            $data['sub_only'] = (boolean) $oMbqEtForum->subOnly->oriValue;
        }
        if ($oMbqEtForum->canPost->hasSetOriValue()) {
            $data['can_post'] = (boolean) $oMbqEtForum->canPost->oriValue;
        } else {
            $data['can_post'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canPost.default');
        }
        if ($oMbqEtForum->unreadStickyCount->hasSetOriValue()) {
            $data['unread_sticky_count'] = (int) $oMbqEtForum->unreadStickyCount->oriValue;
        }
        if ($oMbqEtForum->unreadAnnounceCount->hasSetOriValue()) {
            $data['unread_announce_count'] = (int) $oMbqEtForum->unreadAnnounceCount->oriValue;
        }
        if ($oMbqEtForum->requirePrefix->hasSetOriValue()) {
            $data['require_prefix'] = (boolean) $oMbqEtForum->requirePrefix->oriValue;
        }
        if ($oMbqEtForum->prefixes->hasSetOriValue()) {
            $tempArr = array();
            foreach ($oMbqEtForum->prefixes as $prefix) {
                $tempArr[] = array('prefix_id' => (string) $prefix['id'], 'prefix_display_name' => (string) $prefix['name']);
            }
            $data['prefixes'] = (array) $tempArr;
        }
        if ($oMbqEtForum->canUpload->hasSetOriValue()) {
            $data['can_upload'] = (boolean) $oMbqEtForum->canUpload->oriValue;
        } else {
            $data['can_upload'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canUpload.default');
        }
        $data['child'] = array();
        $this->recurMakeApiTreeDataForum($data['child'], $oMbqEtForum->objsSubMbqEtForum);
        return $data;
    }
    /**
     * return forum json api data
     *
     * @param  Object  $oMbqEtForum
     * @return  Array
     */
    protected function returnAdvJsonApiDataForum($oMbqEtForum) {
        $data = array();
        if ($oMbqEtForum->forumId->hasSetOriValue()) {
            $data['id'] = (string) $oMbqEtForum->forumId->oriValue;
        }
        if ($oMbqEtForum->forumName->hasSetOriValue()) {
            $data['name'] = (string) $oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForum->description->hasSetOriValue()) {
            $data['description'] = (string) $oMbqEtForum->description->oriValue;
        }
        if ($oMbqEtForum->parentId->hasSetOriValue()) {
            $data['parent'] = (string) $oMbqEtForum->parentId->oriValue;
        }
        if ($oMbqEtForum->logoUrl->hasSetOriValue()) {
            $data['icon'] = (string) $oMbqEtForum->logoUrl->oriValue;
        }
        if ($oMbqEtForum->url->hasSetOriValue()) {
            $data['link_url'] = (string) $oMbqEtForum->url->oriValue;
        }
        if ($oMbqEtForum->unreadTopicNum->hasSetOriValue()) {
            $data['unread_num'] = (int) $oMbqEtForum->unreadTopicNum->oriValue;
        }
        if ($oMbqEtForum->totalTopicNum->hasSetOriValue()) {
            $data['topic_count'] = (int) $oMbqEtForum->totalTopicNum->oriValue;
        }
        if ($oMbqEtForum->totalPostNum->hasSetOriValue()) {
            $data['post_count'] = (int) $oMbqEtForum->totalPostNum->oriValue;
        }
        if ($oMbqEtForum->isProtected->hasSetOriValue()) {
            $data['password'] = (boolean) $oMbqEtForum->isProtected->oriValue;
        }
        if ($oMbqEtForum->subOnly->hasSetOriValue()) {
            $data['sub_only'] = (boolean) $oMbqEtForum->subOnly->oriValue;
        }
        if ($oMbqEtForum->isSubscribed->hasSetOriValue()) {
            $data['is_follow'] = (boolean) $oMbqEtForum->isSubscribed->oriValue;
        }
        if ($oMbqEtForum->canSubscribe->hasSetOriValue()) {
            $data['can_follow'] = (boolean) $oMbqEtForum->canSubscribe->oriValue;
        } else {
            $data['can_follow'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canSubscribe.default');
        }
        if ($oMbqEtForum->canPost->hasSetOriValue()) {
            $data['can_post'] = (boolean) $oMbqEtForum->canPost->oriValue;
        } else {
            $data['can_post'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canPost.default');
        }
        if ($oMbqEtForum->requirePrefix->hasSetOriValue()) {
            $data['require_prefix'] = (boolean) $oMbqEtForum->requirePrefix->oriValue;
        }
        if ($oMbqEtForum->prefixes->hasSetOriValue()) {
            $data['prefixes'] = (array) $oMbqEtForum->prefixes->oriValue;
        }
        $data['child'] = array();
        $this->recurMakeApiTreeDataForum($data['child'], $oMbqEtForum->objsSubMbqEtForum);
        return $data;
    }
    
    /**
     * recur make forum tree api data
     *
     * @param  Array  $dataChild
     * @param  Array  $objsSubMbqEtForum
     */
    protected function recurMakeApiTreeDataForum(&$dataChild, $objsSubMbqEtForum) {
        $j = 0;
        foreach ($objsSubMbqEtForum as $oMbqEtForum) {
            $dataChild[$j] = $this->returnApiDataForum($oMbqEtForum);
            $j ++;
        }
    }
    
    /**
     * return forum tree api data
     *
     * @param  Array  $tree  forum tree
     * @return  Array
     */
    public function returnApiTreeDataForum($tree) {
        $data = array();
        $i = 0;
        foreach ($tree as $oMbqEtForum) {
            $data[$i] = $this->returnApiDataForum($oMbqEtForum);
            $i ++;
        }
        return $data;
    }
    
    /**
     * get forum tree structure
     *
     * @return  Array
     */
    public function getForumTree() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get forum objs
     *
     * @return  Array
     */
    public function getObjsMbqEtForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one forum by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get breadcrumb forums
     *
     * @return Array
     */
    public function getObjsBreadcrumbMbqEtForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
    * get sub forums in a special forum
    *
    * @return Array
    */
    public function getObjsSubMbqEtForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }

}

?>