<?php

defined('MBQ_IN_IT') or exit;

/**
 * search_post action
 * 
 * @since  2012-8-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActSearchPost extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $startNum = (int) MbqMain::$input[1];
        $lastNum = (int) MbqMain::$input[2];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $filter = array(
            'keywords' => MbqMain::$input[0],
            'searchid' => MbqMain::$input[3],
            'page' => $oMbqDataPage->curPage,
            'perpage' => $oMbqDataPage->numPerPage
        );
        $filter['showposts'] = 1;
        if (strlen(MbqMain::$input[0]) < MbqBaseFdt::getFdt('MbqFdtConfig.forum.min_search_length.default')) {
            MbqError::alert('', "Search words too short!", '', MBQ_ERR_APP);
        }
        $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
        if ($oMbqAclEtForumPost->canAclSearchPost()) {    //acl judge
            $oMbqRdForumSearch = MbqMain::$oClk->newObj('MbqRdForumSearch');
            $oMbqDataPage = $oMbqRdForumSearch->forumAdvancedSearch($filter, $oMbqDataPage, array('case' => 'searchPost'));
            $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
            $this->data['total_post_num'] = (int) $oMbqDataPage->totalNum;
            $this->data['posts'] = $oMbqRdEtForumPost->returnApiArrDataForumPost($oMbqDataPage->datas);
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>