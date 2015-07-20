<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtForumTopic');

/**
 * forum topic read class
 * 
 * @since  2012-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtForumTopic extends MbqBaseRdEtForumTopic {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtForumTopic, $pName, $mbqOpt = array()) {
        switch ($pName) {
            case 'oMbqEtForum':
            if ($oMbqEtForumTopic->forumId->hasSetOriValue()) {
                $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
                if ($objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($oMbqEtForumTopic->forumId->oriValue), array('case' => 'byForumIds'))) {
                    $oMbqEtForumTopic->oMbqEtForum = $objsMbqEtForum[0];
                }
            }
            break;
            case 'byOAuthorMbqEtUser':   /* make properties by oAuthorMbqEtUser */
            if ($oMbqEtForumTopic->oAuthorMbqEtUser) {
                if ($oMbqEtForumTopic->oAuthorMbqEtUser->iconUrl->hasSetOriValue()) {
                    $oMbqEtForumTopic->authorIconUrl->setOriValue($oMbqEtForumTopic->oAuthorMbqEtUser->iconUrl->oriValue);
                }
            }
            break;
            case 'oDummyFirstMbqEtForumPost':
            if ($oMbqEtForumTopic->mbqBind['oStdForumTopic']) {
                $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
                $var = $oMbqEtForumTopic->mbqBind['oStdForumTopic'];
                $oMbqEtForumPost = MbqMain::$oClk->newObj('MbqEtForumPost');
                $oMbqEtForumPost->isDummyForumPost->setOriValue(true);
                $oMbqEtForumPost->position->setOriValue(1);     //!!!
                $oMbqEtForumPost->postId->setOriValue('topic_'.$var->DiscussionID);
                $oMbqEtForumPost->topicId->setOriValue($var->DiscussionID);
                //$oMbqEtForumPost->postTitle->setOriValue($var->Name);
                $oMbqEtForumPost->postTitle->setOriValue(htmlspecialchars_decode($var->Name, ENT_QUOTES));
                $oMbqEtForumPost->postContent->setOriValue($var->Body);
                $oMbqEtForumPost->postContent->setAppDisplayValue($var->FormatBody);
                $oMbqEtForumPost->postContent->setTmlDisplayValue($oMbqRdEtForumPost->processContentForDisplay($var->FormatBody, true));
                $oMbqEtForumPost->postContent->setTmlDisplayValueNoHtml($oMbqRdEtForumPost->processContentForDisplay($var->FormatBody, false));
                $oMbqEtForumPost->shortContent->setOriValue(MbqMain::$oMbqCm->getShortContent($oMbqEtForumPost->postContent->tmlDisplayValue));
                $oMbqEtForumPost->postAuthorId->setOriValue($var->FirstUserID);
                $oMbqEtForumPost->postTime->setOriValue(strtotime($var->FirstDate));
                $oMbqEtForumPost->mbqBind['oStdForumTopic'] = $var;
                /* load oMbqEtForumTopic property and oMbqEtForum property */
                $oMbqEtForumPost->oMbqEtForumTopic = $oMbqEtForumTopic;
                $oMbqEtForumPost->oMbqEtForum = $oMbqEtForumTopic->oMbqEtForum;
                $oMbqEtForumPost->forumId->setOriValue($oMbqEtForumPost->oMbqEtForum->forumId->oriValue);
                /* load post author */
                $oMbqEtForumPost->oAuthorMbqEtUser = $oMbqEtForumTopic->oAuthorMbqEtUser;
                $oMbqRdEtForumPost->makeProperty($oMbqEtForumPost, 'byOAuthorMbqEtUser');
                if (MbqMain::$oMbqAppEnv->check3rdPluginEnabled('FileUpload')) {
                    /* load attachment */
                    $oMbqRdEtAtt =  MbqMain::$oClk->newObj('MbqRdEtAtt');
                    $objsMbqEtAtt = $oMbqRdEtAtt->getObjsMbqEtAtt(array($oMbqEtForumPost->postId->oriValue), array('case' => 'byForumPostIds'));
                    $oMbqEtForumPost->objsMbqEtAtt = $objsMbqEtAtt;
                    $oMbqEtForumPost->objsNotInContentMbqEtAtt = $objsMbqEtAtt;
                    /* load objsNotInContentMbqEtAtt */
                    //
                }
                /* load objsMbqEtThank property and make related properties/flags */
                //
                /* make other properties */
                $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.no'));  //default
                if (MbqMain::hasLogin()) {
                    if ($oMbqEtForumTopic->mbqBind['oStdForumTopic']->InsertUserID != MbqMain::$oCurMbqEtUser->userId->oriValue) {
                        if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $oMbqEtForumTopic->oMbqEtForum->mbqBind['oStdForumCategory']->PermissionCategoryID)) {
                            if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Category', $oMbqEtForumTopic->oMbqEtForum->mbqBind['oStdForumCategory']->PermissionCategoryID)) {
                                $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                            }
                        }
                    } else {
                         // Make sure that content can (still) be edited.
                         $EditContentTimeout = C('Garden.EditContentTimeout', -1);
                         $CanEdit = $EditContentTimeout == -1 || strtotime($oMbqEtForumTopic->mbqBind['oStdForumTopic']->DateInserted) + $EditContentTimeout > time();
                         if (!$CanEdit) {
                            if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $oMbqEtForumTopic->oMbqEtForum->mbqBind['oStdForumCategory']->PermissionCategoryID)) {
                                if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Category', $oMbqEtForumTopic->oMbqEtForum->mbqBind['oStdForumCategory']->PermissionCategoryID)) {
                                    $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                                }
                            }
                         } else {
                               if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Category', $oMbqEtForumTopic->oMbqEtForum->mbqBind['oStdForumCategory']->PermissionCategoryID)) {
                                   $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                               }
                         }
                    }
                }
                $oMbqEtForumTopic->oDummyFirstMbqEtForumPost = $oMbqEtForumPost;
            }
            break;
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get forum topic objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byForum' means get data by forum obj.$var is the forum obj.
     * $mbqOpt['case'] = 'subscribed' means get subscribed data.$var is the user id.
     * $mbqOpt['case'] = 'byObjsStdForumTopic' means get data by objsStdForumTopic.$var is the objsStdForumTopic.
     * $mbqOpt['case'] = 'byTopicIds' means get data by topic ids.$var is the ids.
     * $mbqOpt['case'] = 'byAuthor' means get data by author.$var is the MbqEtUser obj.
     * $mbqOpt['top'] = true means get sticky data.
     * $mbqOpt['notIncludeTop'] = true means get not sticky data.
     * @return  Mixed
     */
    public function getObjsMbqEtForumTopic($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byForum') {
            $oMbqEtForum = $var;
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                if ($mbqOpt['notIncludeTop']) {
                    require_once(MBQ_APPEXTENTION_PATH.'ExttMbqCategoriesController.php');
                    $oExttMbqCategoriesController = new ExttMbqCategoriesController();
                    $oExttMbqCategoriesController->Initialize();
                    $arr = $oExttMbqCategoriesController->exttMbqGetForumTopics($var->forumId->oriValue, $oMbqDataPage);
                    $objsStdForumTopic = $arr['topics']->Result();
                    $oMbqDataPage->totalNum = $arr['total'];
                } elseif ($mbqOpt['top']) {
                    require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
                    $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
                    $oExttMbqDiscussionModel->Watching = TRUE;
                    $arr = $oExttMbqDiscussionModel->exttMbqGetTopics($oMbqDataPage->startNum, $oMbqDataPage->numPerPage, '', NULL, array('forumId' => $var->forumId->oriValue,'onlyAnnouncements' => true));
                    $objsStdForumTopic = $arr['data']->Result();
                    $oMbqDataPage->totalNum = $arr['total'];
                } else {
                    MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
                }
                /* common begin */
                $mbqOpt['case'] = 'byObjsStdForumTopic';
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
                return $this->getObjsMbqEtForumTopic($objsStdForumTopic, $mbqOpt);
                /* common end */
            }
        } elseif ($mbqOpt['case'] == 'subscribed') {
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                //ref DiscussionsController::Bookmarked()
                $Wheres = array('w.Bookmarked' => '1', 'w.UserID' => $var);
                $DiscussionModel = new DiscussionModel();
                $DiscussionData = $DiscussionModel->Get(0, 1000, $Wheres);
                $objsStdForumTopic = $DiscussionData->Result();
                $oMbqDataPage->totalNum = count($objsStdForumTopic);
                /* common begin */
                $mbqOpt['case'] = 'byObjsStdForumTopic';
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
                return $this->getObjsMbqEtForumTopic($objsStdForumTopic, $mbqOpt);
                /* common end */
            }
        } elseif ($mbqOpt['case'] == 'byAuthor') {
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
                $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
                $oExttMbqDiscussionModel->Watching = TRUE;
                $arr = $oExttMbqDiscussionModel->exttMbqGetTopics($oMbqDataPage->startNum, $oMbqDataPage->numPerPage, '', NULL, array('authorUserId' => $var->userId->oriValue));
                $objsStdForumTopic = $arr['data']->Result();
                $oMbqDataPage->totalNum = $arr['total'];
                /* common begin */
                $mbqOpt['case'] = 'byObjsStdForumTopic';
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
                return $this->getObjsMbqEtForumTopic($objsStdForumTopic, $mbqOpt);
                /* common end */
            }
        } elseif ($mbqOpt['case'] == 'byTopicIds') {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
            $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
            $arr = $oExttMbqDiscussionModel->exttMbqGetTopics(0, count($var), '', NULL, array('topicIds' => $var));
            $objsStdForumTopic = $arr['data']->Result();
            /* common begin */
            $mbqOpt['case'] = 'byObjsStdForumTopic';
            return $this->getObjsMbqEtForumTopic($objsStdForumTopic, $mbqOpt);
            /* common end */
        } elseif ($mbqOpt['case'] == 'byObjsStdForumTopic') {
            $objsStdForumTopic = $var;
            /* common begin */
            $objsMbqEtForumTopic = array();
            $authorUserIds = array();
            $lastReplyUserIds = array();
            $forumIds = array();
            $topicIds = array();
            foreach ($objsStdForumTopic as $oStdForumTopic) {
                $objsMbqEtForumTopic[] = $this->initOMbqEtForumTopic($oStdForumTopic, array('case' => 'oStdForumTopic'));
            }
            foreach ($objsMbqEtForumTopic as $oMbqEtForumTopic) {
                $authorUserIds[$oMbqEtForumTopic->topicAuthorId->oriValue] = $oMbqEtForumTopic->topicAuthorId->oriValue;
                $lastReplyUserIds[$oMbqEtForumTopic->lastReplyAuthorId->oriValue] = $oMbqEtForumTopic->lastReplyAuthorId->oriValue;
                $forumIds[$oMbqEtForumTopic->forumId->oriValue] = $oMbqEtForumTopic->forumId->oriValue;
                $topicIds[$oMbqEtForumTopic->topicId->oriValue] = $oMbqEtForumTopic->topicId->oriValue;
            }
            /* load oMbqEtForum property */
            $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
            $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum($forumIds, array('case' => 'byForumIds'));
            foreach ($objsMbqEtForum as $oNewMbqEtForum) {
                foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                    if ($oNewMbqEtForum->forumId->oriValue == $oMbqEtForumTopic->forumId->oriValue) {
                        $oMbqEtForumTopic->oMbqEtForum = $oNewMbqEtForum;
                    }
                }
            }
            /* load topic author */
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $objsAuthorMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser($authorUserIds, array('case' => 'byUserIds'));
            foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                foreach ($objsAuthorMbqEtUser as $oAuthorMbqEtUser) {
                    if ($oMbqEtForumTopic->topicAuthorId->oriValue == $oAuthorMbqEtUser->userId->oriValue) {
                        $oMbqEtForumTopic->oAuthorMbqEtUser = $oAuthorMbqEtUser;
                        break;
                    }
                }
            }
            foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                $this->makeProperty($oMbqEtForumTopic, 'byOAuthorMbqEtUser');
            }
            /* load oLastReplyMbqEtUser */
            $objsLastReplyMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser($lastReplyUserIds, array('case' => 'byUserIds'));
            foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                foreach ($objsLastReplyMbqEtUser as $oLastReplyMbqEtUser) {
                    if ($oMbqEtForumTopic->lastReplyAuthorId->oriValue == $oLastReplyMbqEtUser->userId->oriValue) {
                        $oMbqEtForumTopic->oLastReplyMbqEtUser = $oLastReplyMbqEtUser;
                        break;
                    }
                }
            }
            foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                $this->makeProperty($oMbqEtForumTopic, 'oDummyFirstMbqEtForumPost');
            }
            /* make other properties */
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            foreach ($objsMbqEtForumTopic as &$oMbqEtForumTopic) {
                if ($oMbqAclEtForumPost->canAclReplyPost($oMbqEtForumTopic)) {
                    $oMbqEtForumTopic->canReply->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReply.range.yes'));
                } else {
                    $oMbqEtForumTopic->canReply->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReply.range.no'));
                }
            }
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                $oMbqDataPage->datas = $objsMbqEtForumTopic;
                return $oMbqDataPage;
            } else {
                return $objsMbqEtForumTopic;
            }
            /* common end */
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one forum topic by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdForumTopic' means init forum topic by StdForumTopic obj
     * $mbqOpt['case'] = 'byTopicId' means init forum topic by topic id
     * @return  Mixed
     */
    public function initOMbqEtForumTopic($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdForumTopic') {
            $var->FormatBody = Gdn_Format::To($var->Body, $var->Format);
            $oMbqEtForumTopic = MbqMain::$oClk->newObj('MbqEtForumTopic');
            $oMbqEtForumTopic->totalPostNum->setOriValue($var->CountComments + 1);
            $oMbqEtForumTopic->topicId->setOriValue($var->DiscussionID);
            $oMbqEtForumTopic->forumId->setOriValue($var->CategoryID);
            //$oMbqEtForumTopic->topicTitle->setOriValue($var->Name);
            $oMbqEtForumTopic->topicTitle->setOriValue(htmlspecialchars_decode($var->Name, ENT_QUOTES));
            $oMbqEtForumTopic->topicContent->setOriValue($var->Body);
            $oMbqEtForumTopic->shortContent->setOriValue(MbqMain::$oMbqCm->getShortContent($var->Body));
            $oMbqEtForumTopic->topicAuthorId->setOriValue($var->FirstUserID);
            $oMbqEtForumTopic->lastReplyAuthorId->setOriValue($var->LastUserID ? $var->LastUserID : $var->FirstUserID);
            //$oMbqEtForumTopic->postTime->setOriValue(strtotime($var->FirstDate));
            $oMbqEtForumTopic->postTime->setOriValue(strtotime($var->LastDate) ? strtotime($var->LastDate) : strtotime($var->FirstDate));
            $oMbqEtForumTopic->lastReplyTime->setOriValue(strtotime($var->LastDate));
            $oMbqEtForumTopic->replyNumber->setOriValue($var->CountComments);
            if (!MbqMain::$oMbqAppEnv->check3rdPluginEnabled('AllViewed')) {
                //the AllViewed plugin has conflict with our plugin,so always return false in topic new_post property
                $oMbqEtForumTopic->newPost->setOriValue($var->CountUnreadComments ? MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.newPost.range.yes') : MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.newPost.range.no'));
            }  else {
                $oMbqEtForumTopic->newPost->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.newPost.range.no'));
            }
            $oMbqEtForumTopic->viewNumber->setOriValue($var->CountViews);
            if ($var->Bookmarked) {
                $oMbqEtForumTopic->isSubscribed->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.isSubscribed.range.yes'));
            } else {
                $oMbqEtForumTopic->isSubscribed->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.isSubscribed.range.no'));
            }
            if (MbqMain::hasLogin()) {
                $oMbqEtForumTopic->canSubscribe->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canSubscribe.range.yes'));
            } else {
                $oMbqEtForumTopic->canSubscribe->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canSubscribe.range.no'));
            }
            $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
            $oMbqEtForumTopic->objsBreadcrumbMbqEtForum = $oMbqRdEtForum->getObjsBreadcrumbMbqEtForum($oMbqEtForumTopic->forumId->oriValue);    //for json
            $oMbqEtForumTopic->mbqBind['oStdForumTopic'] = $var;
            return $oMbqEtForumTopic;
        } elseif ($mbqOpt['case'] == 'byTopicId') {
            $topicId = $var;
            if ($objsMbqEtForumTopic = $this->getObjsMbqEtForumTopic(array($topicId), array('case' => 'byTopicIds'))) {
                return $objsMbqEtForumTopic[0];
            }
            return false;
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
  
}

?>