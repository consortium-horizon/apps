<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtForum');

/**
 * forum read class
 * 
 * @since  2012-8-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtForum extends MbqBaseRdEtForum {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtForum, $pName, $mbqOpt = array()) {
        switch ($pName) {
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get forum tree structure
     *
     * @return  Array
     */
    public function getForumTree() {
        $oCategoryModel = new CategoryModel();
        $oCategoryModel->Watching = TRUE;
        $objsStdForumCategory = $oCategoryModel->GetFull()->Result();
        $tree = array();
        $newTree = array();
        $i = 0;
        foreach ($objsStdForumCategory as $oStdForumCategory) {
            if ($oStdForumCategory->CategoryID != -1 && $oStdForumCategory->ParentCategoryID == -1) {
                $tree[$i]['obj'] = $oStdForumCategory;
                $tree[$i]['children'] = array();
                $this->exttRecurGetStdForumCategoryTree($objsStdForumCategory, $tree[$i]);
                $i ++;
            }
        }
        foreach ($tree as $item) {
            $id = $item['obj']->CategoryID;
            $newTree[$id] = $this->initOMbqEtForum($item['obj'], array('case' => 'oStdForumCategory'));
            $this->exttRecurInitObjsSubMbqEtForum($newTree[$id], $item['children'], array('case' => 'objsStdForumCategory'));
        }
        return $newTree;
    }
    /**
     * recursive get StdForumCategory tree
     *
     * @param  Array  $objsStdForumCategory
     * @param  Array  $treeI
     */
    private function exttRecurGetStdForumCategoryTree(&$objsStdForumCategory, &$treeI) {
        $j = 0;
        foreach ($objsStdForumCategory as $oStdForumCategory) {
            if ($oStdForumCategory->ParentCategoryID == $treeI['obj']->CategoryID) {
                $treeI['children'][$j]['obj'] = $oStdForumCategory;
                $treeI['children'][$j]['children'] = array();
                $this->exttRecurGetStdForumCategoryTree($objsStdForumCategory, $treeI['children'][$j]);
                $j ++;
            }
        }
    }
    /**
     * recursive init objsSubMbqEtForum
     *
     * @param  Object  $oMbqEtForum  the object need init objsSubMbqEtForum
     * @param  Array  
     * @param  Array  $mbqOpt
     * $mbqOpt['objsStdForumCategory'] means init forum by StdForumCategory objs
     */
    private function exttRecurInitObjsSubMbqEtForum(&$oMbqEtForum, $arr, $mbqOpt) {
        $i = 0;
        foreach ($arr as $item) {
            $oMbqEtForum->objsSubMbqEtForum[$i] = $this->initOMbqEtForum($item['obj'], array('case' => 'oStdForumCategory'));
            $oMbqEtForum->objsSubMbqEtForum[$i]->oParentMbqEtForum = clone $oMbqEtForum;    //!!!
            $this->exttRecurInitObjsSubMbqEtForum($oMbqEtForum->objsSubMbqEtForum[$i], $item['children'], array('case' => 'objsStdForumCategory'));
            $i ++;
        }
    }
    
    /**
     * get breadcrumb forums
     *
     * @param  Integer  $forumId
     * @return Array
     */
    public function getObjsBreadcrumbMbqEtForum($forumId) { //for json
        $tree = MbqMain::$oMbqAppEnv->returnForumTree();
        $objsBreadcrumbMbqEtForum = array();
        foreach ($tree as $oMbqEtForum) {
            if ($oMbqEtForum->forumId->oriValue == $forumId) {
                $oFindMbqEtForum = $oMbqEtForum;
                break;
            } else {
                $ret = $this->exttRecurFindForum($oMbqEtForum->objsSubMbqEtForum, $forumId);
                if ($ret) {
                    $oFindMbqEtForum = $ret;
                    break;
                }
            }
        }
        if ($oFindMbqEtForum) {
            $tempObjsBreadcrumbMbqEtForum[0] = clone $oFindMbqEtForum;
            $tempObjsBreadcrumbMbqEtForum[0]->objsSubMbqEtForum = array();  //!!! clear sub forums for output breadcrumb
            $this->exttRecurMakeTempObjsBreadcrumbMbqEtForum($tempObjsBreadcrumbMbqEtForum, $oFindMbqEtForum);
            $objsBreadcrumbMbqEtForum =  array_reverse($tempObjsBreadcrumbMbqEtForum);
            return $objsBreadcrumbMbqEtForum;
        } else {
            return array();
        }
    }
    /**
     * recursive find forum
     *
     * @param  Array  $objsSubMbqEtForum
     * @param  String  $forumId
     * @return  Mixed
     */
    private function exttRecurFindForum($objsSubMbqEtForum, $forumId) {
        foreach ($objsSubMbqEtForum as $oMbqEtForum) {
            if ($oMbqEtForum->forumId->oriValue == $forumId) {
                $oFindMbqEtForum = $oMbqEtForum;
                break;
            } else {
                $ret = $this->exttRecurFindForum($oMbqEtForum->objsSubMbqEtForum, $forumId);
                if ($ret) {
                    $oFindMbqEtForum = $ret;
                    break;
                }
            }
        }
        if ($oFindMbqEtForum) return $oFindMbqEtForum;
        else return false;
    }
    /**
     * recur make $tempObjsBreadcrumbMbqEtForum
     *
     * @param  Array  $tempObjsBreadcrumbMbqEtForum
     * @param  Object  $oFindMbqEtForum
     */
    private function exttRecurMakeTempObjsBreadcrumbMbqEtForum(&$tempObjsBreadcrumbMbqEtForum, $oFindMbqEtForum) {
        if ($oFindMbqEtForum->oParentMbqEtForum) {
            $i = count($tempObjsBreadcrumbMbqEtForum);
            $tempObjsBreadcrumbMbqEtForum[$i] = clone $oFindMbqEtForum->oParentMbqEtForum;
            $tempObjsBreadcrumbMbqEtForum[$i]->objsSubMbqEtForum = array();  //!!! clear sub forums for output breadcrumb
            $this->exttRecurMakeTempObjsBreadcrumbMbqEtForum($tempObjsBreadcrumbMbqEtForum, $oFindMbqEtForum->oParentMbqEtForum);
        }
    }
    
    /**
     * get forum objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byForumIds' means get data by forum ids.$var is the ids.
     * @return  Array
     */
    public function getObjsMbqEtForum($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byForumIds') {
            $oCategoryModel = new CategoryModel();
            $oCategoryModel->Watching = TRUE;
            $objsStdForumCategory = $oCategoryModel->GetFull()->Result();
            $newObjsStdForumCategory = array();
            foreach ($var as $id) {
                foreach ($objsStdForumCategory as $oStdForumCategory) {
                    if ($id == $oStdForumCategory->CategoryID) {
                        $newObjsStdForumCategory[] = $oStdForumCategory;
                    }
                }
            }
            $objsMbqEtForum = array();
            foreach ($newObjsStdForumCategory as $oStdForumCategory) {
                $objsMbqEtForum[] = $this->initOMbqEtForum($oStdForumCategory, array('case' => 'oStdForumCategory'));
            }
            /* make other properties */
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            foreach ($objsMbqEtForum as &$oMbqEtForum) {
                if ($oMbqAclEtForumTopic->canAclNewTopic($oMbqEtForum)) {
                    $oMbqEtForum->canPost->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canPost.range.yes'));
                } else {
                    $oMbqEtForum->canPost->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.canPost.range.no'));
                }
            }
            return $objsMbqEtForum;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one forum by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdForumCategory' means init forum by StdForumCategory obj
     * @return  Mixed
     */
    public function initOMbqEtForum($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdForumCategory') {
            $oMbqEtForum = MbqMain::$oClk->newObj('MbqEtForum');
            $oMbqEtForum->forumId->setOriValue($var->CategoryID);
            $oMbqEtForum->forumName->setOriValue($var->Name);
            $oMbqEtForum->description->setOriValue($var->Description);
            $oMbqEtForum->totalTopicNum->setOriValue($var->CountAllDiscussions);
            $oMbqEtForum->parentId->setOriValue($var->ParentCategoryID);
            $oMbqEtForum->subOnly->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForum.subOnly.range.no'));
            $oMbqEtForum->mbqBind['oStdForumCategory'] = $var;
            return $oMbqEtForum;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * get sub forums in a special forum
     *
     * @return Array
     */
    public function getObjsSubMbqEtForum($forumId) {    //for json
        $objsSubMbqEtForum = array();
        $tree = MbqMain::$oMbqAppEnv->returnForumTree();
        foreach ($tree as $oMbqEtForum) {
            if ($oMbqEtForum->forumId->oriValue == $forumId) {
                return $oMbqEtForum->objsSubMbqEtForum;
            } else {
                if ($oNewMbqEtForum = $this->exttRecurFindForum($oMbqEtForum->objsSubMbqEtForum, $forumId)) {
                    return $oNewMbqEtForum->objsSubMbqEtForum;
                }
            }
        }
        return $objsSubMbqEtForum;
    }
  
}

?>