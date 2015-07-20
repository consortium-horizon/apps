<?php

require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDummyMenu.php');

/**
 * ExttMbqDiscussionController extended from DiscussionController
 * add method exttMbqGetTopicPosts() modified from method Index().
 * modify method Initialize()
 * 
 * @since  2012-10-15
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqDiscussionController extends DiscussionController {
   
   /**
    * Default single discussion display.
    * 
    * @since 2.0.0
    * @access public
    * 
    * @param int $DiscussionID Unique discussion ID
    * @param string $DiscussionStub URL-safe title slug
    * @param int $Offset How many comments to skip
    * @param int $Limit Total comments to display
    * @param Object  $oMbqDataPage
    */
   public function exttMbqGetTopicPosts($DiscussionID = '', $DiscussionStub = '', $Offset = '', $Limit = '', $oMbqDataPage) {
      $Offset = $oMbqDataPage->startNum;
      $Limit = $oMbqDataPage->numPerPage;
      $this->DiscussionModel = new DiscussionModel();
      $this->CommentModel = new CommentModel();
      // Setup head
      $Session = Gdn::Session();
      $this->AddJsFile('jquery.ui.packed.js');
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('options.js');
      $this->AddJsFile('bookmark.js');
      $this->AddJsFile('discussion.js');
      $this->AddJsFile('autosave.js');
      
      // Load the discussion record
      $DiscussionID = (is_numeric($DiscussionID) && $DiscussionID > 0) ? $DiscussionID : 0;
      if (!array_key_exists('Discussion', $this->Data))
         $this->SetData('Discussion', $this->DiscussionModel->GetID($DiscussionID), TRUE);
         
      if(!is_object($this->Discussion)) {
         //throw new Exception(sprintf(T('%s Not Found'), T('Discussion')), 404);
         MbqError::alert('', "Need valid topic id!", '', MBQ_ERR_APP);
      }
      
      // Check permissions
      //$this->Permission('Vanilla.Discussions.View', TRUE, 'Category', $this->Discussion->PermissionCategoryID);
      if (!Gdn::Session()->CheckPermission('Vanilla.Discussions.View', TRUE, 'Category', $this->Discussion->PermissionCategoryID)) {
        MbqError::alert('', '', '', MBQ_ERR_APP);
      }
      $this->SetData('CategoryID', $this->CategoryID = $this->Discussion->CategoryID, TRUE);
      $this->SetData('Breadcrumbs', CategoryModel::GetAncestors($this->CategoryID));
      
      // Setup
      $this->Title($this->Discussion->Name);

      // Actual number of comments, excluding the discussion itself
      $ActualResponses = $this->Discussion->CountComments - 1;
      // Define the query offset & limit
      if (!is_numeric($Limit) || $Limit < 0)
         $Limit = C('Vanilla.Comments.PerPage', 50);

      $OffsetProvided = $Offset != '';
      list($Offset, $Limit) = OffsetLimit($Offset, $Limit);

      // If $Offset isn't defined, assume that the user has not clicked to
      // view a next or previous page, and this is a "view" to be counted.
      // NOTE: This has been moved to an event fired from analyticstick.
//      if ($Offset == '')
//         $this->DiscussionModel->AddView($DiscussionID, $this->Discussion->CountViews);

      $this->Offset = $Offset;
      if (C('Vanilla.Comments.AutoOffset')) {
         if ($this->Discussion->CountCommentWatch > 0 && $OffsetProvided == '')
            $this->AddDefinition('LocationHash', '#Item_'.$this->Discussion->CountCommentWatch);

         if (!is_numeric($this->Offset) || $this->Offset < 0 || !$OffsetProvided) {
            // Round down to the appropriate offset based on the user's read comments & comments per page
            $CountCommentWatch = $this->Discussion->CountCommentWatch > 0 ? $this->Discussion->CountCommentWatch : 0;
            if ($CountCommentWatch > $ActualResponses)
               $CountCommentWatch = $ActualResponses;

            // (((67 comments / 10 perpage) = 6.7) rounded down = 6) * 10 perpage = offset 60;
            $this->Offset = floor($CountCommentWatch / $Limit) * $Limit;
         }
         if ($ActualResponses <= $Limit)
            $this->Offset = 0;

         if ($this->Offset == $ActualResponses)
            $this->Offset -= $Limit;
      } else {
         if ($this->Offset == '')
            $this->Offset = 0;
      }

      if ($this->Offset < 0)
         $this->Offset = 0;

      //wztmdf
      $this->Offset = $oMbqDataPage->startNum;  //fixed pagination issue

      // Set the canonical url to have the proper page title.
      $this->CanonicalUrl(Url(ConcatSep('/', 'discussion/'.$this->Discussion->DiscussionID.'/'. Gdn_Format::Url($this->Discussion->Name), PageNumber($this->Offset, $Limit, TRUE)), TRUE));

      // Load the comments
      $this->SetData('CommentData', $this->CommentModel->Get($DiscussionID, $Limit, $this->Offset), TRUE);
      $this->SetData('Comments', $this->CommentData);

      // Make sure to set the user's discussion watch records
      $this->CommentModel->SetWatch($this->Discussion, $Limit, $this->Offset, $this->Discussion->CountComments);

      // Build a pager
      $PagerFactory = new Gdn_PagerFactory();
		$this->EventArguments['PagerType'] = 'Pager';
		$this->FireEvent('BeforeBuildPager');
      $this->Pager = $PagerFactory->GetPager($this->EventArguments['PagerType'], $this);
      $this->Pager->ClientID = 'Pager';
         
      $this->Pager->Configure(
         $this->Offset,
         $Limit,
         $ActualResponses,
         'discussion/'.$DiscussionID.'/'.Gdn_Format::Url($this->Discussion->Name).'/%1$s'
      );
      $this->FireEvent('AfterBuildPager');
      
      // Define the form for the comment input
      $this->Form = Gdn::Factory('Form', 'Comment');
      $this->Form->Action = Url('/vanilla/post/comment/');
      $this->DiscussionID = $this->Discussion->DiscussionID;
      $this->Form->AddHidden('DiscussionID', $this->DiscussionID);
      $this->Form->AddHidden('CommentID', '');

      // Retrieve & apply the draft if there is one:
      $DraftModel = new DraftModel();
      $Draft = $DraftModel->Get($Session->UserID, 0, 1, $this->Discussion->DiscussionID)->FirstRow();
      $this->Form->AddHidden('DraftID', $Draft ? $Draft->DraftID : '');
      if ($Draft)
         $this->Form->SetFormValue('Body', $Draft->Body);
      
      // Deliver JSON data if necessary
      if ($this->_DeliveryType != DELIVERY_TYPE_ALL) {
         $this->SetJson('LessRow', $this->Pager->ToString('less'));
         $this->SetJson('MoreRow', $this->Pager->ToString('more'));
         $this->View = 'comments';
      }
      
		// Inform moderator of checked comments in this discussion
		$CheckedComments = $Session->GetAttribute('CheckedComments', array());
		if (count($CheckedComments) > 0)
			ModerationController::InformCheckedComments($this);

      // Add modules
      $this->AddModule('NewDiscussionModule');
      $this->AddModule('CategoriesModule');
      $this->AddModule('BookmarkedModule');

      // Report the discussion id so js can use it.      
      $this->AddDefinition('DiscussionID', $DiscussionID);
      
      $this->FireEvent('BeforeDiscussionRender');
      return $this->CommentData;
      $this->Render();
   }
   
   /**
    * Highlight route.
    *
    * Always called by dispatcher before controller's requested method.
    * 
    * @since 2.0.0
    * @access public
    */
   public function Initialize() {
      $this->Menu = new ExttMbqDummyMenu();
      parent::Initialize();
   }
   
}

?>