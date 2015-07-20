<?php
/**
 * MnEtForumPost init class
 * 
 * @since  2013-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumPostInit Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * init MnEtForumPost object by record
     *
     * @param  Object  $recordUser
     */
    public function initMnEtForumPostByRecord($recordForumPost) {
        $oMnEtForumPost = MainApp::$oClk->newObj('MnEtForumPost');
        $oMnEtUserInit = MainApp::$oClk->newObj('MnEtUserInit');
        $oMnEtAttInit = MainApp::$oClk->newObj('MnEtAttInit');
        if (property_exists($recordForumPost, 'id')) {
            $oMnEtForumPost->postId->setOriValue($recordForumPost->id);
        }
        if (property_exists($recordForumPost, 'time')) {
            $oMnEtForumPost->postTime->setOriValue($recordForumPost->time);
        }
        if (property_exists($recordForumPost, 'author')) {
            $oMnEtForumPost->oAuthorMnEtUser = $oMnEtUserInit->initMnEtUserByRecord($recordForumPost->author);
        }
        if (property_exists($recordForumPost, 'content')) {
            $oMnEtForumPost->postContent->setTmlDisplayValue($recordForumPost->content);    //!!!
            $oMnEtForumPost->postContent->setMnDisplayValue($this->makeMnDisplayValue($recordForumPost->content));    //!!!
        }
        if (property_exists($recordForumPost, 'smiley_off')) {
            $oMnEtForumPost->allowSmilies->setOriValue(!$recordForumPost->smiley_off);      //!!!
        }
        if (property_exists($recordForumPost, 'preview')) {
            $oMnEtForumPost->shortContent->setOriValue($recordForumPost->preview);
        }
        if (property_exists($recordForumPost, 'attachs')) {
            $oMnEtForumPost->objsNotInContentMbqEtAtt = $oMnEtAttInit->initObjsMnEtAttByRecords($recordForumPost->attachs);
        }
        if (property_exists($recordForumPost, 'status') && $recordForumPost->status) {
            if (property_exists($recordForumPost->status, 'is_pending')) {
                $oMnEtForumPost->state->setOriValue((int) $recordForumPost->status->is_pending);    //!!!
            }
            if (property_exists($recordForumPost->status, 'is_deleted')) {
                $oMnEtForumPost->isDeleted->setOriValue($recordForumPost->status->is_deleted);
            }
            if (property_exists($recordForumPost->status, 'is_liked')) {
                $oMnEtForumPost->isLiked->setOriValue($recordForumPost->status->is_liked);
            }
            if (property_exists($recordForumPost->status, 'is_thanked')) {
                $oMnEtForumPost->isThanked->setOriValue($recordForumPost->status->is_thanked);
            }
        }
        if (property_exists($recordForumPost, 'permission') && $recordForumPost->permission) {
            if (property_exists($recordForumPost->permission, 'can_edit')) {
                $oMnEtForumPost->canEdit->setOriValue($recordForumPost->permission->can_edit);
            }
            if (property_exists($recordForumPost->permission, 'can_approve')) {
                $oMnEtForumPost->canApprove->setOriValue($recordForumPost->permission->can_approve);
            }
            if (property_exists($recordForumPost->permission, 'can_delete')) {
                $oMnEtForumPost->canDelete->setOriValue($recordForumPost->permission->can_delete);
            }
            if (property_exists($recordForumPost->permission, 'can_move')) {
                $oMnEtForumPost->canMove->setOriValue($recordForumPost->permission->can_move);
            }
            if (property_exists($recordForumPost->permission, 'can_like')) {
                $oMnEtForumPost->canLike->setOriValue($recordForumPost->permission->can_like);
            }
            if (property_exists($recordForumPost->permission, 'can_unlike')) {
                $oMnEtForumPost->canUnlike->setOriValue($recordForumPost->permission->can_unlike);
            }
            if (property_exists($recordForumPost->permission, 'can_thank')) {
                $oMnEtForumPost->canThank->setOriValue($recordForumPost->permission->can_thank);
            }
            if (property_exists($recordForumPost->permission, 'can_unthank')) {
                $oMnEtForumPost->canUnthank->setOriValue($recordForumPost->permission->can_unthank);
            }
            if (property_exists($recordForumPost->permission, 'can_report')) {
                $oMnEtForumPost->canReport->setOriValue($recordForumPost->permission->can_report);
            }
        }
        return $oMnEtForumPost;
    }
    
    /**
     * init objsMnEtForumPost by records
     *
     * @param  Array  $recordsForumPost
     */
    public function initObjsMnEtForumPostByRecords($recordsForumPost) {
        $objsMnEtForumPost = array();
        foreach ($recordsForumPost as $recordForumPost) {
            $objsMnEtForumPost[] = $this->initMnEtForumPostByRecord($recordForumPost);
        }
        return $objsMnEtForumPost;
    }
    
    /**
     * make mnDisplayValue
     *
     * @param  String  $content
     * @return  String
     */
    private function makeMnDisplayValue($content) { //TODO
        $retStr = $content;
        $retStr = preg_replace('/\[img\]([^\[]*?)\[\/img\]/i', '<img src="$1" />', $retStr);  //convert img bbcode
        $retStr = preg_replace('/\[url=([^\]]*?)\]([^\[]*?)\[\/url\]/i', '<a href="$1">$2</a>', $retStr);  //convert url bbcode
        $retStr = preg_replace('/\[quote\]/i', '<blockquote style="border:1px solid gray;">', $retStr);  //convert quote bbcode
        $retStr = preg_replace('/\[\/quote\]/i', '</blockquote>', $retStr);  //convert quote bbcode
    	$retStr = str_ireplace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $retStr);
        //convert smileys
        //TODO cases
        if (MainBase::apiIsVanilla2Site()) {
            $retStr = $this->convertSmileysForVanilla2EmotifyPlugin($retStr);
        } elseif (MainBase::apiIsVbulletin3Site()) {
            $retStr = $this->convertSmileysForVbulletin3($retStr);
        }
        return $retStr;
    }
    
    /**
     * convert smileys for vanilla 2 emotify plugin
     *
     * @param  String  $str
     * @return  String
     */
    private function convertSmileysForVanilla2EmotifyPlugin($str) {
        $url = MPF_C_APP_IMG_URL.'smileys/vanilla2/Emotify/';
        $retStr = $str;
        $retStr = preg_replace('/ \:\)\] /i', '<img src='.$url.'100.gif />', $retStr);
        $retStr = preg_replace('/ ;\)\) /i', '<img src='.$url.'71.gif />', $retStr);
        $retStr = preg_replace('/ \:\)&gt;\- /i', '<img src='.$url.'67.gif />', $retStr);
        $retStr = preg_replace('/ \:\)\) /i', '<img src='.$url.'21.gif />', $retStr);
        $retStr = preg_replace('/ \:\) /i', '<img src='.$url.'1.gif />', $retStr);
        $retStr = preg_replace('/ \:\(\|\) /i', '<img src='.$url.'51.gif />', $retStr);
        $retStr = preg_replace('/ \:\(\( /i', '<img src='.$url.'20.gif />', $retStr);
        $retStr = preg_replace('/ \:\( /i', '<img src='.$url.'2.gif />', $retStr);
        $retStr = preg_replace('/ ;\) /i', '<img src='.$url.'3.gif />', $retStr);
        $retStr = preg_replace('/ \:D /i', '<img src='.$url.'4.gif />', $retStr);
        $retStr = preg_replace('/ \;\;\) /i', '<img src='.$url.'5.gif />', $retStr);
        $retStr = preg_replace('/ &gt;\:D&lt; /i', '<img src='.$url.'6.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\/ /i', '<img src='.$url.'7.gif />', $retStr);
        $retStr = preg_replace('/ \:x /i', '<img src='.$url.'8.gif />', $retStr);
        $retStr = preg_replace('/ \:\\\"&gt; /i', '<img src='.$url.'9.gif />', $retStr);
        $retStr = preg_replace('/ \:P /i', '<img src='.$url.'10.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\* /i', '<img src='.$url.'11.gif />', $retStr);
        $retStr = preg_replace('/ \=\(\( /i', '<img src='.$url.'12.gif />', $retStr);
        $retStr = preg_replace('/ \:\-O /i', '<img src='.$url.'13.gif />', $retStr);
        $retStr = preg_replace('/ \:O\) /i', '<img src='.$url.'34.gif />', $retStr);
        $retStr = preg_replace('/ \:O /i', '<img src='.$url.'13.gif />', $retStr);
        $retStr = preg_replace('/ X\( /i', '<img src='.$url.'14.gif />', $retStr);
        $retStr = preg_replace('/ \:&gt; /i', '<img src='.$url.'15.gif />', $retStr);
        $retStr = preg_replace('/ B\-\) /i', '<img src='.$url.'16.gif />', $retStr);
        $retStr = preg_replace('/ \:\-S /i', '<img src='.$url.'17.gif />', $retStr);
        $retStr = preg_replace('/ \#\:\-S /i', '<img src='.$url.'18.gif />', $retStr);
        $retStr = preg_replace('/ &gt;\:\) /i', '<img src='.$url.'19.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\(\( /i', '<img src='.$url.'20.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\)\) /i', '<img src='.$url.'21.gif />', $retStr);
        $retStr = preg_replace('/ \:\| /i', '<img src='.$url.'22.gif />', $retStr);
        $retStr = preg_replace('/ \/\:\) /i', '<img src='.$url.'23.gif />', $retStr);
        $retStr = preg_replace('/ \=\)\) /i', '<img src='.$url.'24.gif />', $retStr);
        $retStr = preg_replace('/ O\:\-\) /i', '<img src='.$url.'25.gif />', $retStr);
        $retStr = preg_replace('/ \:\-B /i', '<img src='.$url.'26.gif />', $retStr);
        $retStr = preg_replace('/ \=; /i', '<img src='.$url.'27.gif />', $retStr);
        $retStr = preg_replace('/ I\-\) /i', '<img src='.$url.'28.gif />', $retStr);
        $retStr = preg_replace('/ 8\-\| /i', '<img src='.$url.'29.gif />', $retStr);
        $retStr = preg_replace('/ L\-\) /i', '<img src='.$url.'30.gif />', $retStr);
        $retStr = preg_replace('/ \:\-&amp; /i', '<img src='.$url.'31.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\$ /i', '<img src='.$url.'32.gif />', $retStr);
        $retStr = preg_replace('/ \[\-\( /i', '<img src='.$url.'33.gif />', $retStr);
        $retStr = preg_replace('/ 8\-\} /i', '<img src='.$url.'35.gif />', $retStr);
        $retStr = preg_replace('/ &lt;\:\-P /i', '<img src='.$url.'36.gif />', $retStr);
        $retStr = preg_replace('/ \(\:\| /i', '<img src='.$url.'37.gif />', $retStr);
        $retStr = preg_replace('/ \=P\~ /i', '<img src='.$url.'38.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\?\? /i', '<img src='.$url.'106.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\? /i', '<img src='.$url.'39.gif />', $retStr);
        $retStr = preg_replace('/ \#\-o /i', '<img src='.$url.'40.gif />', $retStr);
        $retStr = preg_replace('/ \=D&gt; /i', '<img src='.$url.'41.gif />', $retStr);
        $retStr = preg_replace('/ \:\-SS /i', '<img src='.$url.'42.gif />', $retStr);
        $retStr = preg_replace('/ \@\-\) /i', '<img src='.$url.'43.gif />', $retStr);
        $retStr = preg_replace('/ \:\^o /i', '<img src='.$url.'44.gif />', $retStr);
        $retStr = preg_replace('/ \:\-w /i', '<img src='.$url.'45.gif />', $retStr);
        $retStr = preg_replace('/ \:\-&lt; /i', '<img src='.$url.'46.gif />', $retStr);
        $retStr = preg_replace('/ &gt;\:P /i', '<img src='.$url.'47.gif />', $retStr);
        $retStr = preg_replace('/ &lt;\)\:\) /i', '<img src='.$url.'48.gif />', $retStr);
        $retStr = preg_replace('/ \:\@\) /i', '<img src='.$url.'49.gif />', $retStr);
        $retStr = preg_replace('/ 3\:\-O /i', '<img src='.$url.'50.gif />', $retStr);
        $retStr = preg_replace('/ \~\:&gt; /i', '<img src='.$url.'52.gif />', $retStr);
        $retStr = preg_replace('/ \@\}\;\- /i', '<img src='.$url.'53.gif />', $retStr);
        $retStr = preg_replace('/ \%\%\- /i', '<img src='.$url.'54.gif />', $retStr);
        $retStr = preg_replace('/ \*\*\=\= /i', '<img src='.$url.'55.gif />', $retStr);
        $retStr = preg_replace('/ \(\~\~\) /i', '<img src='.$url.'56.gif />', $retStr);
        $retStr = preg_replace('/ \~\O\) /i', '<img src='.$url.'57.gif />', $retStr);
        $retStr = preg_replace('/ \*\-\:\) /i', '<img src='.$url.'58.gif />', $retStr);
        $retStr = preg_replace('/ 8\-X /i', '<img src='.$url.'59.gif />', $retStr);
        $retStr = preg_replace('/ \=\:\) /i', '<img src='.$url.'60.gif />', $retStr);
        $retStr = preg_replace('/ &gt;\-\) /i', '<img src='.$url.'61.gif />', $retStr);
        $retStr = preg_replace('/ \:\-L /i', '<img src='.$url.'62.gif />', $retStr);
        $retStr = preg_replace('/ \[\-O&lt; /i', '<img src='.$url.'63.gif />', $retStr);
        $retStr = preg_replace('/ \$\-\) /i', '<img src='.$url.'64.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\\\" /i', '<img src='.$url.'65.gif />', $retStr);
        $retStr = preg_replace('/ b\-\( /i', '<img src='.$url.'66.gif />', $retStr);
        $retStr = preg_replace('/ \[\-X /i', '<img src='.$url.'68.gif />', $retStr);
        $retStr = preg_replace('/ \\\:D\/ /i', '<img src='.$url.'69.gif />', $retStr);
        $retStr = preg_replace('/ &gt;\:\/ /i', '<img src='.$url.'70.gif />', $retStr);
        $retStr = preg_replace('/ o\-&gt; /i', '<img src='.$url.'72.gif />', $retStr);
        $retStr = preg_replace('/ o\=&gt; /i', '<img src='.$url.'73.gif />', $retStr);
        $retStr = preg_replace('/ o\-\+ /i', '<img src='.$url.'74.gif />', $retStr);
        $retStr = preg_replace('/ \(\%\) /i', '<img src='.$url.'75.gif />', $retStr);
        $retStr = preg_replace('/ \:\-\@ /i', '<img src='.$url.'76.gif />', $retStr);
        $retStr = preg_replace('/ \^\:\)\^ /i', '<img src='.$url.'77.gif />', $retStr);
        $retStr = preg_replace('/ \:\-j /i', '<img src='.$url.'78.gif />', $retStr);
        $retStr = preg_replace('/ \(\*\) /i', '<img src='.$url.'79.gif />', $retStr);
        $retStr = preg_replace('/ \:\-c /i', '<img src='.$url.'101.gif />', $retStr);
        $retStr = preg_replace('/ \~\X\( /i', '<img src='.$url.'102.gif />', $retStr);
        $retStr = preg_replace('/ \:\-h /i', '<img src='.$url.'103.gif />', $retStr);
        $retStr = preg_replace('/ \:\-t /i', '<img src='.$url.'104.gif />', $retStr);
        $retStr = preg_replace('/ 8\-&gt; /i', '<img src='.$url.'105.gif />', $retStr);
        $retStr = preg_replace('/ \%\-\( /i', '<img src='.$url.'107.gif />', $retStr);
        $retStr = preg_replace('/ \:o3 /i', '<img src='.$url.'108.gif />', $retStr);
        $retStr = preg_replace('/ X\_X /i', '<img src='.$url.'109.gif />', $retStr);
        $retStr = preg_replace('/ \:\!\! /i', '<img src='.$url.'110.gif />', $retStr);
        $retStr = preg_replace('/ \\\m\/ /i', '<img src='.$url.'111.gif />', $retStr);
        $retStr = preg_replace('/ \:\-q /i', '<img src='.$url.'112.gif />', $retStr);
        $retStr = preg_replace('/ \:\-bd /i', '<img src='.$url.'113.gif />', $retStr);
        $retStr = preg_replace('/ \^\#\(\^ /i', '<img src='.$url.'114.gif />', $retStr);
        $retStr = preg_replace('/ \:bz /i', '<img src='.$url.'115.gif />', $retStr);
        $retStr = preg_replace('/ \:ar\! /i', '<img src='.$url.'pirate.gif />', $retStr);
        $retStr = preg_replace('/ \[\.\.\] /i', '<img src='.$url.'transformer.gif />', $retStr);
        return $retStr;
    }
    
    /**
     * convert smileys for vBulletin 3
     *
     * @param  String  $str
     * @return  String
     */
    private function convertSmileysForVbulletin3($str) {    //TODO
        return $str;
    }
    
}

?>