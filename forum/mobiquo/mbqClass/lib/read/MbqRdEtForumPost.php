<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdEtForumPost');

/**
 * forum post read class
 * 
 * @since  2012-8-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdEtForumPost extends MbqBaseRdEtForumPost {
    
    public function __construct() {
    }
    
    public function makeProperty(&$oMbqEtForumPost, $pName, $mbqOpt = array()) {
        switch ($pName) {
            case 'byOAuthorMbqEtUser':   /* make properties by oAuthorMbqEtUser */
            if ($oMbqEtForumPost->oAuthorMbqEtUser) {
                if ($oMbqEtForumPost->oAuthorMbqEtUser->iconUrl->hasSetOriValue()) {
                    $oMbqEtForumPost->authorIconUrl->setOriValue($oMbqEtForumPost->oAuthorMbqEtUser->iconUrl->oriValue);
                }
            }
            break;
            default:
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_PNAME . ':' . $pName . '.');
            break;
        }
    }
    
    /**
     * get forum post objs
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'byTopic' means get data by forum topic obj.$var is the forum topic obj.
     * $mbqOpt['case'] = 'byPostIds' means get data by post ids.$var is the ids.
     * $mbqOpt['case'] = 'byObjsStdForumPost' means get data by objsStdForumPost.$var is the objsStdForumPost.
     * $mbqOpt['case'] = 'byReplyUser' means get data by reply user.$var is the MbqEtUser obj.
     * @return  Mixed
     */
    public function getObjsMbqEtForumPost($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'byTopic') {
            $oMbqEtForumTopic = $var;
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                if ($oMbqDataPage->curPage == 1) {
                    $oMbqDataPage->numPerPage = $oMbqDataPage->numPerPage - 1;
                    $oMbqDataPage->lastNum = $oMbqDataPage->lastNum - 1;
                } else {
                    $oMbqDataPage->startNum = $oMbqDataPage->startNum - 1;
                    $oMbqDataPage->lastNum = $oMbqDataPage->startNum + $oMbqDataPage->numPerPage - 1;
                }
                require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionController.php');
                $oExttMbqDiscussionController = new ExttMbqDiscussionController();
                $oExttMbqDiscussionController->Initialize();
                $objsStdForumPost = $oExttMbqDiscussionController->exttMbqGetTopicPosts($oMbqEtForumTopic->topicId->oriValue, '', '', '', $oMbqDataPage)->Result();
                /* common begin */
                $mbqOpt['case'] = 'byObjsStdForumPost';
                $mbqOpt['oMbqDataPage'] = $oMbqDataPage;
                if ($oMbqDataPage->curPage == 1) {
                    $oMbqDataPage = $this->getObjsMbqEtForumPost($objsStdForumPost, $mbqOpt);
                    $oMbqDataPage->datas = array_merge(array($oMbqEtForumTopic->oDummyFirstMbqEtForumPost), $oMbqDataPage->datas);
                    return $oMbqDataPage;
                } else {
                    return $this->getObjsMbqEtForumPost($objsStdForumPost, $mbqOpt);
                }
                /* common end */
            }
        } elseif ($mbqOpt['case'] == 'byPostIds') {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } elseif ($mbqOpt['case'] == 'byReplyUser') {
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                /* modified from MbqRdForumSearch::forumAdvancedSearch case=>'searchPost' */
                require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
                $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
                $subSqlCanReadTopicIds = $oExttMbqDiscussionModel->exttMbqGetTopics('', '', '', NULL, array('onlyGetSqlForTopicIds' => true));
                $oCommentModel = new CommentModel();
                $oSql = $oCommentModel->SQL;
                $dbPre = $oSql->Database->DatabasePrefix;
                $subSqlPost = "select CommentID as postId, DateInserted from ".$dbPre."Comment as mbqC where mbqC.InsertUserID = '".addslashes($var->userId->oriValue)."' and mbqC.DiscussionID in ($subSqlCanReadTopicIds)";
                $sqlCount = "select count(postId) as totalNum from ($subSqlPost) as data";
                $oMbqDataPage->totalNum = $oSql->Query($sqlCount)->FirstRow()->totalNum;
                $sql = "$subSqlPost order by DateInserted desc limit $oMbqDataPage->startNum,$oMbqDataPage->numPerPage";
                $records = $oSql->Query($sql)->Result();
                foreach ($records as $r) {
                    if ($oMbqEtForumPost = $this->initOMbqEtForumPost($r->postId, array('case' => 'byPostId'))) {
                        $oMbqDataPage->datas[] = $oMbqEtForumPost;
                    }
                }
                return $oMbqDataPage;
            }
        } elseif ($mbqOpt['case'] == 'byObjsStdForumPost') {
            $objsStdForumPost = $var;
            /* common begin */
            $objsMbqEtForumPost = array();
            $authorUserIds = array();
            $topicIds = array();
            foreach ($objsStdForumPost as $oStdForumPost) {
                $objsMbqEtForumPost[] = $this->initOMbqEtForumPost($oStdForumPost, array('case' => 'oStdForumPost'));
            }
            foreach ($objsMbqEtForumPost as $oMbqEtForumPost) {
                $authorUserIds[$oMbqEtForumPost->postAuthorId->oriValue] = $oMbqEtForumPost->postAuthorId->oriValue;
                $topicIds[$oMbqEtForumPost->topicId->oriValue] = $oMbqEtForumPost->topicId->oriValue;
            }
            /* load oMbqEtForumTopic property and oMbqEtForum property */
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            $objsMbqEtFroumTopic = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($topicIds, array('case' => 'byTopicIds'));
            foreach ($objsMbqEtFroumTopic as $oNewMbqEtFroumTopic) {
                foreach ($objsMbqEtForumPost as &$oMbqEtForumPost) {
                    if ($oNewMbqEtFroumTopic->topicId->oriValue == $oMbqEtForumPost->topicId->oriValue) {
                        $oMbqEtForumPost->oMbqEtForumTopic = $oNewMbqEtFroumTopic;
                        if ($oMbqEtForumPost->oMbqEtForumTopic->oMbqEtForum) {
                            $oMbqEtForumPost->oMbqEtForum = $oMbqEtForumPost->oMbqEtForumTopic->oMbqEtForum;
                            $oMbqEtForumPost->forumId->setOriValue($oMbqEtForumPost->oMbqEtForum->forumId->oriValue);
                        }
                    }
                }
            }
            /* load post author */
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $objsAuthorMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser($authorUserIds, array('case' => 'byUserIds'));
            $postIds = array();
            foreach ($objsMbqEtForumPost as &$oMbqEtForumPost) {
                $postIds[] = $oMbqEtForumPost->postId->oriValue;
                foreach ($objsAuthorMbqEtUser as $oAuthorMbqEtUser) {
                    if ($oMbqEtForumPost->postAuthorId->oriValue == $oAuthorMbqEtUser->userId->oriValue) {
                        $oMbqEtForumPost->oAuthorMbqEtUser = $oAuthorMbqEtUser;
                        break;
                    }
                }
            }
            foreach ($objsMbqEtForumPost as &$oMbqEtForumPost) {
                $this->makeProperty($oMbqEtForumPost, 'byOAuthorMbqEtUser');
            }
            if (MbqMain::$oMbqAppEnv->check3rdPluginEnabled('FileUpload')) {
                /* load attachment */
                $oMbqRdEtAtt =  MbqMain::$oClk->newObj('MbqRdEtAtt');
                $objsMbqEtAtt = $oMbqRdEtAtt->getObjsMbqEtAtt($postIds, array('case' => 'byForumPostIds'));
                foreach ($objsMbqEtAtt as $oMbqEtAtt) {
                    foreach ($objsMbqEtForumPost as &$oMbqEtForumPost) {
                        if ($oMbqEtForumPost->postId->oriValue == $oMbqEtAtt->postId->oriValue) {
                            $oMbqEtForumPost->objsMbqEtAtt[] = $oMbqEtAtt;
                            $oMbqEtForumPost->objsNotInContentMbqEtAtt[] = $oMbqEtAtt;
                        }
                    }
                }
                /* load objsNotInContentMbqEtAtt */
                //
            }
            /* load objsMbqEtThank property and make related properties/flags */
            //
            /* make other properties */
            foreach ($objsMbqEtForumPost as &$oMbqEtForumPost) {
                $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.no'));  //default
                if (MbqMain::hasLogin()) {
                    if ($oMbqEtForumPost->mbqBind['oStdForumPost']->InsertUserID != MbqMain::$oCurMbqEtUser->userId->oriValue) {
                        if (Gdn::Session()->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', $oMbqEtForumPost->oMbqEtForumTopic->mbqBind['oStdForumTopic']->PermissionCategoryID)) {
                            $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                        }
                    } else {
                         $EditContentTimeout = C('Garden.EditContentTimeout', -1);
                         $CanEdit = $EditContentTimeout == -1 || strtotime($oMbqEtForumPost->mbqBind['oStdForumPost']->DateInserted) + $EditContentTimeout > time();
                         if (!$CanEdit) {
                            if (Gdn::Session()->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', $oMbqEtForumPost->oMbqEtForumTopic->mbqBind['oStdForumTopic']->PermissionCategoryID))
                                $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                         } else {
                                $oMbqEtForumPost->canEdit->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'));
                         }
                    }
                }
            }
            /* common end */
            if ($mbqOpt['oMbqDataPage']) {
                $oMbqDataPage = $mbqOpt['oMbqDataPage'];
                $oMbqDataPage->datas = $objsMbqEtForumPost;
                return $oMbqDataPage;
            } else {
                return $objsMbqEtForumPost;
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * init one forum post by condition
     *
     * @param  Mixed  $var
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'oStdForumPost' means init forum post by oStdForumPost
     * $mbqOpt['case'] = 'byPostId' means init forum post by post id
     * @return  Mixed
     */
    public function initOMbqEtForumPost($var, $mbqOpt) {
        if ($mbqOpt['case'] == 'oStdForumPost') {
            $var->FormatBody = Gdn_Format::To($var->Body, $var->Format);
            $oMbqEtForumPost = MbqMain::$oClk->newObj('MbqEtForumPost');
            $oMbqEtForumPost->postId->setOriValue($var->CommentID);
            $oCommentModel = new CommentModel();
            $oMbqEtForumPost->position->setOriValue($oCommentModel->GetOffset($var) + 2);   //!!!
            $oMbqEtForumPost->topicId->setOriValue($var->DiscussionID);
            $oMbqEtForumPost->postTitle->setOriValue('');
            $oMbqEtForumPost->postContent->setOriValue($var->Body);
            $oMbqEtForumPost->postContent->setAppDisplayValue($var->FormatBody);
            $oMbqEtForumPost->postContent->setTmlDisplayValue($this->processContentForDisplay($var->FormatBody, true));
            $oMbqEtForumPost->postContent->setTmlDisplayValueNoHtml($this->processContentForDisplay($var->FormatBody, false));
            $oMbqEtForumPost->shortContent->setOriValue(MbqMain::$oMbqCm->getShortContent($oMbqEtForumPost->postContent->tmlDisplayValue));
            $oMbqEtForumPost->postAuthorId->setOriValue($var->InsertUserID);
            $oMbqEtForumPost->postTime->setOriValue(strtotime($var->DateInserted));
            $oMbqEtForumPost->mbqBind['oStdForumPost'] = $var;
            return $oMbqEtForumPost;
        } elseif ($mbqOpt['case'] == 'byPostId') {
            if (is_numeric($var)) {
                $oCommentModel = new CommentModel();
                $oStdForumPost = $oCommentModel->GetID($var);
                if ($oStdForumPost && $oStdForumPost->CommentID) {
                    $objsMbqEtForumPost = $this->getObjsMbqEtForumPost(array($oStdForumPost), array('case' => 'byObjsStdForumPost'));
                    return $objsMbqEtForumPost[0];
                } else {
                    return false;
                }
            } else {    /* oDummyFirstMbqEtForumPost */
                $arr = explode('_', $var);
                $topicId = $arr[1];
                $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
                if ($oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($topicId, array('case' => 'byTopicId'))) {
                    return $oMbqEtForumTopic->oDummyFirstMbqEtForumPost;
                } else {
                    return false;
                }
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
    
    /**
     * process content for display in mobile app
     *
     * @params  String  $content
     * @params  Boolean  $returnHtml
     * @return  String
     */
    public function processContentForDisplay($content, $returnHtml) {
        /*
        support bbcode:url/img/quote
        support html:br/i/b/u/font+color(red/blue)
        <strong> -> <b>
        attention input param:return_html
        attention output param:post_content
        */
        $post = $content;
    	if($returnHtml){
    		//$post = str_replace("&", '&amp;', $post);
    		//$post = str_replace("<", '&lt;', $post);
    		//$post = str_replace(">", '&gt;', $post);
    		/*
    		$post = str_ireplace("[b]", '<b>', $post);
    		$post = str_ireplace("[/b]", '</b>', $post);
    		$post = str_ireplace("[i]", '<i>', $post);
    		$post = str_ireplace("[/i]", '</i>', $post);
    		$post = str_ireplace("[u]", '<u>', $post);
    		$post = str_ireplace("[/u]", '</u>', $post);
    		*/
    		$post = str_replace("\r", '', $post);
    		//$post = str_replace("\n", '<br />', $post);
    		//$post = str_ireplace('[hr]', '<br />____________________________________<br />', $post);
            $post = str_ireplace('<hr />', '<br />____________________________________<br />', $post);
    	    $post = str_ireplace('<li>', "\t\t<li>", $post);
    	    $post = str_ireplace('</li>', "</li><br />", $post);
    	    $post = str_ireplace('</tr>', '</tr><br />', $post);
    	    $post = str_ireplace('</td>', "</td>\t\t", $post);
    	} else {
    	    $post = preg_replace('/<br \/>/i', "\n", $post);
    		//$post = str_ireplace('[hr]', "\n____________________________________\n", $post);
            $post = str_ireplace('<hr />', "\n____________________________________\n", $post);
    		//$post = strip_tags($post);
    		$post = html_entity_decode($post, ENT_QUOTES, 'UTF-8');
            // strip remaining bbcode
            //$post = preg_replace('/\[\/?.*?\]/i', '', $post);
    	}
    	$post = preg_replace('/<a .*?href="#tapatalkQuoteBegin-(.*?)">.*?<\/a>/i', '$1 wrote:[quote]', $post);
    	$post = preg_replace('/<a .*?href="#tapatalkQuoteEnd"><\/a>/i', '[/quote]', $post);
    	$post = preg_replace('/<img .*?src="(.*?)" .*?\/>/i', '[img]$1[/img]', $post);
    	$post = preg_replace('/<img .*?src="(.*?)".*?\/{0,1}>/i', '[img]$1[/img]', $post);   //for some special sites
    	$post = preg_replace('/<a .*?href="(.*?)".*?>(.*?)<\/a>/i', '[url=$1]$2[/url]', $post);
    	$post = preg_replace_callback('/\[url=\/mobiquo\.php(\?p\=.*?)\](.*?)\[\/url\]/i', create_function('$matches','return "[url=".MbqMain::$oMbqAppEnv->rootUrl."index.php".$matches[1]."]".$matches[2]."[/url]";'), $post);
    	$post = str_ireplace('<strong>', '<b>', $post);
    	$post = str_ireplace('</strong>', '</b>', $post);
    	$post = preg_replace_callback('/<span style=\"color:(\#.*?)\">(.*?)<\/span>/is', create_function('$matches','return MbqMain::$oMbqCm->mbColorConvert($matches[1], $matches[2]);'), $post);
    	$post = preg_replace('/<span style=\"color: (.*?);\">(.*?)<\/span>/is', '<font color="$1">$2</font>', $post);
    	$post = preg_replace('/<object .*?>.*?<embed src="(.*?)".*?><\/embed><\/object>/is', '[url=$1]$1[/url]', $post); /* for youtube content etc. */
    	//add site root url if url begin with /
    	$post = preg_replace('/\[url=(\/.*?)\](.*?)\[\/url\]/i', '[url='.substr(MbqMain::$oMbqAppEnv->rootUrl, 0, strlen(MbqMain::$oMbqAppEnv->rootUrl) - 1).'$1]$2[/url]', $post);
    	if ($returnHtml) {
    	    $post = str_ireplace('</div>', '</div><br />', $post);
    	    $post = str_ireplace('&nbsp;', ' ', $post);
    	    $post = strip_tags($post, '<br><i><b><u><font>');
    		/*
    		$post = str_replace("&", '&amp;', $post);
    		$post = str_replace("<", '&lt;', $post);
    		$post = str_replace(">", '&gt;', $post);
    		$post = str_ireplace("&lt;b&gt;", '<b>', $post);
    		$post = str_ireplace("&lt;/b&gt;", '</b>', $post);
    		$post = str_ireplace("&lt;i&gt;", '<i>', $post);
    		$post = str_ireplace("&lt;/i&gt;", '</i>', $post);
    		$post = str_ireplace("&lt;u&gt;", '<u>', $post);
    		$post = str_ireplace("&lt;/u&gt;", '</u>', $post);
    		$post = str_ireplace("&lt;br /&gt;", '<br />', $post);
    	    $post = preg_replace('/&lt;font (.*?)&gt;(.*?)&lt;\/font&gt;/i', '<font $1>$2</font>', $post);
    	    */
        } else {
    	    $post = strip_tags($post);
    		$post = str_replace("&", '&amp;', $post);
    		$post = str_replace("<", '&lt;', $post);
    		$post = str_replace(">", '&gt;', $post);
        }
    	$post = trim($post);
    	return $post;
    }
    
    /**
     * return quote post content
     *
     * @param  Object  $oMbqEtForumPost
     * @return  String
     */
    public function getQuotePostContent($oMbqEtForumPost) {
        /*
        $content = preg_replace('/.*<a href="#tapatalkQuoteEnd"><\/a>/i', '', $oMbqEtForumPost->postContent->oriValue);
        $userDisplayName = $oMbqEtForumPost->oAuthorMbqEtUser ? $oMbqEtForumPost->oAuthorMbqEtUser->getDisplayName() : '';
        $ret = '<a href="#tapatalkQuoteBegin-'.$userDisplayName.'"><font color="gray"><b><u>'.$userDisplayName.' wrote:</u></b></font></a><br />';
        $ret .= '<font color="gray"><i>'.trim($content).'</i></font>';
        $ret .= '<a href="#tapatalkQuoteEnd"></a>';
        */
        $content = preg_replace('/.*<a href="#tapatalkQuoteEnd"><\/a>/is', '', $oMbqEtForumPost->postContent->oriValue);
        $userDisplayName = $oMbqEtForumPost->oAuthorMbqEtUser ? $oMbqEtForumPost->oAuthorMbqEtUser->getDisplayName() : '';
        $ret = "[quote=\"$userDisplayName\"]".trim($content)."[/quote]\n\n";
        return $ret;
    }
    
    /**
     * return raw post content
     *
     * @param  Object  $oMbqEtForumPost
     * @return  String
     */
    public function getRawPostContent($oMbqEtForumPost) {
        //need convert quote html code to bbcode quote
        $content = $oMbqEtForumPost->postContent->oriValue;
    	$content = preg_replace('/<a .*?href="#tapatalkQuoteBegin-(.*?)">.*?<\/a>/i', '[quote="$1"]', $content);
    	$content = preg_replace('/<a .*?href="#tapatalkQuoteEnd"><\/a>/i', '[/quote]', $content);
    	return $content;
    }
  
}

?>