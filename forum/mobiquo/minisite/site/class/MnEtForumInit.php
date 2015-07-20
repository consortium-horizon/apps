<?php
/**
 * MnEtForum init class
 * 
 * @since  2013-8-6
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumInit Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * init MnEtForum object by record
     *
     * @param  Object  $recordForum
     */
    public function initMnEtForumByRecord($recordForum) {
        $oMnEtForum = MainApp::$oClk->newObj('MnEtForum');
        if (property_exists($recordForum, 'id')) {
            $oMnEtForum->forumId->setOriValue($recordForum->id);
        }
        if (property_exists($recordForum, 'name')) {
            $oMnEtForum->forumName->setOriValue($recordForum->name);
        }
        if (property_exists($recordForum, 'description')) {
            $oMnEtForum->description->setOriValue($recordForum->description);
        }
        if (property_exists($recordForum, 'parent')) {
            $oMnEtForum->parentId->setOriValue($recordForum->parent);
        }
        if (property_exists($recordForum, 'icon')) {
            $oMnEtForum->logoUrl->setOriValue($recordForum->icon);
        }
        if (property_exists($recordForum, 'link_url')) {
            $oMnEtForum->url->setOriValue($recordForum->link_url);
        }
        if (property_exists($recordForum, 'unread_num')) {
            $oMnEtForum->unreadTopicNum->setOriValue($recordForum->unread_num);
        }
        if (property_exists($recordForum, 'topic_count')) {
            $oMnEtForum->totalTopicNum->setOriValue($recordForum->topic_count);
        }
        if (property_exists($recordForum, 'post_count')) {
            $oMnEtForum->totalPostNum->setOriValue($recordForum->post_count);
        }
        if (property_exists($recordForum, 'password')) {
            $oMnEtForum->isProtected->setOriValue($recordForum->password);
        }
        if (property_exists($recordForum, 'sub_only')) {
            $oMnEtForum->subOnly->setOriValue($recordForum->sub_only);
        }
        if (property_exists($recordForum, 'is_follow')) {
            $oMnEtForum->isSubscribed->setOriValue($recordForum->is_follow);
        }
        if (property_exists($recordForum, 'can_follow')) {
            $oMnEtForum->canSubscribe->setOriValue($recordForum->can_follow);
        }
        if (property_exists($recordForum, 'can_post')) {
            $oMnEtForum->canPost->setOriValue($recordForum->can_post);
        }
        if (property_exists($recordForum, 'require_prefix')) {
            $oMnEtForum->requirePrefix->setOriValue($recordForum->require_prefix);
        }
        if (property_exists($recordForum, 'prefixes')) {
            $newPrefixes = array();
            foreach ($recordForum->prefixes as $prefixe) {
                $newPrefixes[] = (array) $prefixe;
            }
            $oMnEtForum->prefixes->setOriValue($newPrefixes);
        }
        if (property_exists($recordForum, 'child')) {
            if ($recordForum->child) {
                $this->recurInitObjsSubMnEtForumByChild($oMnEtForum->objsSubMnEtForum, $recordForum->child);
            }
        }
        return $oMnEtForum;
    }
    
    /**
     * recur init objsSubMnEtForum by child
     *
     * @param  Array  $objsSubMnEtForum
     * @param  Array  $child  forum child
     */
    private function recurInitObjsSubMnEtForumByChild(&$objsSubMnEtForum, $child) {
        foreach ($child as $recordForum) {
            $objsSubMnEtForum[] = $this->initMnEtForumByRecord($recordForum);
        }
    }
    
    /**
     * init objsMnEtForum by records
     *
     * @param  Array  $recordsForum
     */
    public function initObjsMnEtForumByRecords($recordsForum) {
        $objsMnEtForum = array();
        foreach ($recordsForum as $recordForum) {
            $objsMnEtForum[] = $this->initMnEtForumByRecord($recordForum);
        }
        return $objsMnEtForum;
    }
    
}

?>