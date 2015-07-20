<?php

/**
 * ExttMbqPostController extended from PostController
 * add method exttMbqDiscussion() modified from method Discussion().
 * add method exttMbqComment() modified from method Comment().
 * add method exttMbqEditDiscussion() modified from method EditDiscussion().
 * add method exttMbqEditComment() modified from method EditComment().
 * 
 * @since  2012-10-23
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqPostController extends PostController {
    
   /**
    * convert bbcode quote to custom quote html 
    */
   public function exttMbqConvertBbcodeQuote($content) {
      $content = preg_replace_callback('/\[quote=\"(.*?)\"\](.*?)\[\/quote\]/is', create_function('$matches','return "[quote=\"".$matches[1]."\"]<font color=\"gray\"><i>".trim($matches[2])."</i></font>[/quote]";'), $content);
      $content = preg_replace('/\[quote=\"(.*?)\"\]/i', '<a href="#tapatalkQuoteBegin-$1"><font color="gray"><b><u>$1 wrote:</u></b></font></a><br />', $content);
      $content = preg_replace('/\[\/quote\]/i', '<a href="#tapatalkQuoteEnd"></a>', $content);
      return $content;
   }
   
   /**
    * Create or update a discussion.
    *
    * @since 2.0.0
    * @access public
    * 
    * @param int $CategoryID Unique ID of the category to add the discussion to.
    * @param  Object  $obj:$oMbqEtForumTopic for create topic or $oMbqEtForumPost(oDummyFirstMbqEtForumPost) for edit topic
    */
   public function exttMbqDiscussion($CategoryID = '', &$objMbqEt) {
      if (get_class($objMbqEt) == 'MbqEtForumTopic') {
        $mbqExttIsCreate = true;    //is create topic
      } elseif (get_class($objMbqEt) == 'MbqEtForumPost') {
        $mbqExttIsEdit = true;  //is edit topic
      } else {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid obj!');
      }
      $CategoryID = $objMbqEt->forumId->oriValue;
      $this->Form = $this->Form ? $this->Form : new Gdn_Form();
      $this->DiscussionModel = $this->DiscussionModel ? $this->DiscussionModel : new DiscussionModel();
      $this->CommentModel = $this->CommentModel ? $this->CommentModel : new CommentModel();
      // Override CategoryID if categories are disabled
      $UseCategories = $this->ShowCategorySelector = (bool)C('Vanilla.Categories.Use');
      if (!$UseCategories) 
         $CategoryID = 0;
         
      // Setup head
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('post.js');
      $this->AddJsFile('autosave.js');
      
      $Session = Gdn::Session();
      
      // Set discussion, draft, and category data
      $DiscussionID = isset($this->Discussion) ? $this->Discussion->DiscussionID : '';  /* used for edit */
      $DraftID = isset($this->Draft) ? $this->Draft->DraftID : 0;       /* used for edit */
      $this->CategoryID = isset($this->Discussion) ? $this->Discussion->CategoryID : $CategoryID;
      $Category = CategoryModel::Categories($this->CategoryID);
      if ($Category)
         $this->Category = (object)$Category;
      else
         $this->Category = NULL;

      if ($UseCategories)
			$CategoryData = CategoryModel::Categories();
      
      // Check permission 
      if (isset($this->Discussion)) {   //used for edit topic
         $Foo = 'bar';
         // Permission to edit
         if ($this->Discussion->InsertUserID != $Session->UserID)
            //$this->Permission('Vanilla.Discussions.Edit', TRUE, 'Category', $this->Category->PermissionCategoryID);
            if (!Gdn::Session()->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $this->Category->PermissionCategoryID))
                MbqError::alert('', '', '', MBQ_ERR_APP);

         // Make sure that content can (still) be edited.
         $EditContentTimeout = C('Garden.EditContentTimeout', -1);
         $CanEdit = $EditContentTimeout == -1 || strtotime($this->Discussion->DateInserted) + $EditContentTimeout > time();
         if (!$CanEdit)
            //$this->Permission('Vanilla.Discussions.Edit', TRUE, 'Category', $this->Category->PermissionCategoryID);
            if (!Gdn::Session()->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $this->Category->PermissionCategoryID))
                MbqError::alert('', '', '', MBQ_ERR_APP);

         $this->Title(T('Edit Discussion'));
      } else {  //used for create topic
         // Permission to add
         //$this->Permission('Vanilla.Discussions.Add');
         if (!Gdn::Session()->CheckPermission('Vanilla.Discussions.Add'))
             MbqError::alert('', '', '', MBQ_ERR_APP);
         $this->Title(T('Start a New Discussion'));
      }
      
      // Set the model on the form
      $this->Form->SetModel($this->DiscussionModel);
      /*
      if ($this->Form->AuthenticatedPostBack() === FALSE) {
         // Prep form with current data for editing
         if (isset($this->Discussion)) {
            $this->Form->SetData($this->Discussion);
         } elseif (isset($this->Draft))
            $this->Form->SetData($this->Draft);
         elseif ($this->Category !== NULL)
            $this->Form->SetData(array('CategoryID' => $this->Category->CategoryID));
            
      } else { // Form was submitted
      */
         // Save as a draft?
         if (MbqMain::$cmd == 'new_topic') {
             $FormValues = array(   //make form values
                "TransientKey" => '',
                "hpt" => '',
                "DiscussionID" => '',
                "DraftID" => '0',
                "Name" => $objMbqEt->topicTitle->oriValue,
                "CategoryID" => $objMbqEt->forumId->oriValue,
                "Body" => $this->exttMbqConvertBbcodeQuote($objMbqEt->topicContent->oriValue),
                "Post_Discussion" => "Post Discussion"
             );
         } elseif (MbqMain::$cmd == 'save_raw_post') {
             $FormValues = array(   //make form values
                "TransientKey" => '',
                "hpt" => '',
                "DiscussionID" => $objMbqEt->topicId->oriValue,
                "DraftID" => '0',
                "Name" => $objMbqEt->postTitle->oriValue,
                "CategoryID" => $objMbqEt->forumId->oriValue,
                "Body" => $this->exttMbqConvertBbcodeQuote($objMbqEt->postContent->oriValue),
                "Save" => "Save"
             );
         } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid cmd!');
         }
         foreach ($FormValues as $k => $v) {
            $this->Form->SetFormValue($k, $v);
         }
         $FormValues = $this->Form->FormValues();
         if (APPLICATION_VERSION >= '2.0.18.8') {   //compatible with 2.0.18.8 version
            $FormValues = $this->DiscussionModel->FilterForm($FormValues);
         }
         $this->DeliveryType(GetIncomingValue('DeliveryType', $this->_DeliveryType));
         if ($DraftID == 0)
            $DraftID = $this->Form->GetFormValue('DraftID', 0);
            
         $Draft = $this->Form->ButtonExists('Save Draft') ? TRUE : FALSE;
         $Preview = $this->Form->ButtonExists('Preview') ? TRUE : FALSE;
         if (!$Preview) {
            if (!is_object($this->Category) && isset($FormValues['CategoryID']))
               $this->Category = $CategoryData[$FormValues['CategoryID']];

            if (is_object($this->Category)) {
               // Check category permissions.
               if ($this->Form->GetFormValue('Announce', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Announce', TRUE, 'Category', $this->Category->PermissionCategoryID))
                  //$this->Form->AddError('You do not have permission to announce in this category', 'Announce');
                  MbqError::alert('', 'You do not have permission to announce in this category!', '', MBQ_ERR_APP);

               if ($this->Form->GetFormValue('Close', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Close', TRUE, 'Category', $this->Category->PermissionCategoryID))
                  //$this->Form->AddError('You do not have permission to close in this category', 'Close');
                  MbqError::alert('', 'You do not have permission to close in this category!', '', MBQ_ERR_APP);

               if ($this->Form->GetFormValue('Sink', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Sink', TRUE, 'Category', $this->Category->PermissionCategoryID))
                  //$this->Form->AddError('You do not have permission to sink in this category', 'Sink');
                  MbqError::alert('', 'You do not have permission to sink in this category!', '', MBQ_ERR_APP);

               if (!$Session->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Category', $this->Category->PermissionCategoryID))
                  //$this->Form->AddError('You do not have permission to start discussions in this category', 'CategoryID');
                  MbqError::alert('', '', '', MBQ_ERR_APP);
            }

            // Make sure that the title will not be invisible after rendering
            $Name = trim($this->Form->GetFormValue('Name', ''));
            if ($Name != '' && Gdn_Format::Text($Name) == '')
               //$this->Form->AddError(T('You have entered an invalid discussion title'), 'Name');
               MbqError::alert('', "You have entered an invalid discussion title!", '', MBQ_ERR_APP);
            else {
               // Trim the name.
               $FormValues['Name'] = $Name;
               $this->Form->SetFormValue('Name', $Name);
            }

            if ($this->Form->ErrorCount() == 0) {
               if ($Draft) {
                  $DraftID = $this->DraftModel->Save($FormValues);
                  $this->Form->SetValidationResults($this->DraftModel->ValidationResults());
               } else {
                  //$DiscussionID = $this->DiscussionModel->Save($FormValues, $this->CommentModel);
                  $objMbqEt->topicId->setOriValue($DiscussionID = $this->DiscussionModel->Save($FormValues, $this->CommentModel));
                  $this->Form->SetValidationResults($this->DiscussionModel->ValidationResults());
                  if ($DiscussionID > 0 && $DraftID > 0)
                     $this->DraftModel->Delete($DraftID);
                  if ($DiscussionID == SPAM) {
                     $this->StatusMessage = T('Your post has been flagged for moderation.');
                     //$this->Render('Spam');
                     $objMbqEt->state->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.state.range.postOkNeedModeration'));
                     return;
                  } else {
                     $objMbqEt->state->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.state.range.postOk'));
                  }
               }
            }
         } else {
            // If this was a preview click, create a discussion/comment shell with the values for this comment
            $this->Discussion = new stdClass();
            $this->Discussion->Name = $this->Form->GetValue('Name', '');
            $this->Comment = new stdClass();
            $this->Comment->InsertUserID = $Session->User->UserID;
            $this->Comment->InsertName = $Session->User->Name;
            $this->Comment->InsertPhoto = $Session->User->Photo;
            $this->Comment->DateInserted = Gdn_Format::Date();
            $this->Comment->Body = ArrayValue('Body', $FormValues, '');
            
            $this->EventArguments['Discussion'] = &$this->Discussion;
            $this->EventArguments['Comment'] = &$this->Comment;
            $this->FireEvent('BeforeDiscussionPreview');

            if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
               $this->AddAsset('Content', $this->FetchView('preview'));
            } else {
               $this->View = 'preview';
            }
         }
         if ($this->Form->ErrorCount() > 0) {
            // Return the form errors
            $this->ErrorMessage($this->Form->Errors());
         } else if ($DiscussionID > 0 || $DraftID > 0) {
            // Make sure that the ajax request form knows about the newly created discussion or draft id
            $this->SetJson('DiscussionID', $DiscussionID);
            $this->SetJson('DraftID', $DraftID);
            
            if (!$Preview) {
               // If the discussion was not a draft
               if (!$Draft) {
                  // Redirect to the new discussion
                  $Discussion = $this->DiscussionModel->GetID($DiscussionID);
                  $this->EventArguments['Discussion'] = $Discussion;
                  $this->FireEvent('AfterDiscussionSave');
                  
                  if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
                     //Redirect('/discussion/'.$DiscussionID.'/'.Gdn_Format::Url($Discussion->Name));
                  } else {
                     $this->RedirectUrl = Url('/discussion/'.$DiscussionID.'/'.Gdn_Format::Url($Discussion->Name));
                  }
               } else {
                  // If this was a draft save, notify the user about the save
                  $this->InformMessage(sprintf(T('Draft saved at %s'), Gdn_Format::Date()));
               }
            }
         }
      /*
      }
      */
      
      // Add hidden fields for editing
      $this->Form->AddHidden('DiscussionID', $DiscussionID);
      $this->Form->AddHidden('DraftID', $DraftID, TRUE);
      
      $this->FireEvent('BeforeDiscussionRender');
      
      // Render view (posts/discussion.php or post/preview.php)
      //$this->Render();
   }
   
   /**
    * Edit a discussion (wrapper for PostController::Discussion). 
    *
    * Will throw an error if both params are blank.
    *
    * @since 2.0.0
    * @access public
    * 
    * @param int $DiscussionID Unique ID of the discussion to edit.
    * @param int $DraftID Unique ID of draft discussion to edit.
    * @param  Object  $oMbqEtForumPost(oDummyFirstMbqEtForumPost)
    */
   public function exttMbqEditDiscussion($DiscussionID = '', $DraftID = '', &$oMbqEtForumPost) {
      $DiscussionID = $oMbqEtForumPost->topicId->oriValue;
      $this->DiscussionModel = $this->DiscussionModel ? $this->DiscussionModel : new DiscussionModel();
      if ($DraftID != '') {
         $this->Draft = $this->DraftModel->GetID($DraftID);
         $this->CategoryID = $this->Draft->CategoryID;
      } else {
         $this->Discussion = $this->DiscussionModel->GetID($DiscussionID);
         $this->CategoryID = $this->Discussion->CategoryID;
      }
      
      // Set view and render
      $this->View = 'Discussion';
      //$this->Discussion($this->CategoryID);
      $this->exttMbqDiscussion($this->CategoryID, $oMbqEtForumPost);
   }
   
   /**
    * Create or update a comment.
    *
    * @since 2.0.0
    * @access public
    * 
    * @param int $DiscussionID Unique ID to add the comment to. If blank, this method will throw an error.
    * @param  Object  $oMbqEtForumPost
    */
   public function exttMbqComment($DiscussionID = '', &$oMbqEtForumPost) {
      $DiscussionID = $oMbqEtForumPost->topicId->oriValue;
      $this->Form = $this->Form ? $this->Form : new Gdn_Form();
      $this->DiscussionModel = $this->DiscussionModel ? $this->DiscussionModel : new DiscussionModel();
      $this->CommentModel = $this->CommentModel ? $this->CommentModel : new CommentModel();
      // Get $DiscussionID from RequestArgs if valid
      if ($DiscussionID == '' && count($this->RequestArgs))
         if (is_numeric($this->RequestArgs[0]))
            $DiscussionID = $this->RequestArgs[0];
            
      // If invalid $DiscussionID, get from form.
      $this->Form->SetModel($this->CommentModel);
      $DiscussionID = is_numeric($DiscussionID) ? $DiscussionID : $this->Form->GetFormValue('DiscussionID', 0);
      
      // Set discussion data
      $this->DiscussionID = $DiscussionID;
      $this->Discussion = $Discussion = $this->DiscussionModel->GetID($DiscussionID);      
            
      // Setup head
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('post.js');
      $this->AddJsFile('autosave.js');
      
      // Setup comment model, $CommentID, $DraftID
      $Session = Gdn::Session();
      $CommentID = isset($this->Comment) && property_exists($this->Comment, 'CommentID') ? $this->Comment->CommentID : '';  /* used for edit */
      $DraftID = isset($this->Comment) && property_exists($this->Comment, 'DraftID') ? $this->Comment->DraftID : '';    /* used for edit */
      $this->EventArguments['CommentID'] = $CommentID;
      $this->EventArguments['DraftID'] = $DraftID;
      
      // Determine whether we are editing
      $Editing = $CommentID > 0 || $DraftID > 0;
      $this->EventArguments['Editing'] = $Editing;
      
      // If closed, cancel & go to discussion
      if ($Discussion->Closed == 1 && !$Editing)
         //Redirect('discussion/'.$DiscussionID.'/'.Gdn_Format::Url($Discussion->Name));
         MbqError::alert('', '', '', MBQ_ERR_APP);
      
      // Add hidden IDs to form
      $this->Form->AddHidden('DiscussionID', $DiscussionID);
      $this->Form->AddHidden('CommentID', $CommentID);
      $this->Form->AddHidden('DraftID', $DraftID, TRUE);
      
      // Check permissions
      if ($Editing) {
         // Permisssion to edit
         if ($this->Comment->InsertUserID != $Session->UserID)
            //$this->Permission('Vanilla.Comments.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID);
            if (!Gdn::Session()->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID))
                MbqError::alert('', '', '', MBQ_ERR_APP);
            
         // Make sure that content can (still) be edited.
         $EditContentTimeout = C('Garden.EditContentTimeout', -1);
         $CanEdit = $EditContentTimeout == -1 || strtotime($this->Comment->DateInserted) + $EditContentTimeout > time();
         if (!$CanEdit)
            //$this->Permission('Vanilla.Comments.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID);
            if (!Gdn::Session()->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID))
                MbqError::alert('', '', '', MBQ_ERR_APP);

      } else {
         // Permission to add
         //$this->Permission('Vanilla.Comments.Add', TRUE, 'Category', $Discussion->PermissionCategoryID);
         if (!Gdn::Session()->CheckPermission('Vanilla.Comments.Add', TRUE, 'Category', $Discussion->PermissionCategoryID))
             MbqError::alert('', '', '', MBQ_ERR_APP);
      }

      /*
      if ($this->Form->AuthenticatedPostBack() === FALSE) {
         // Form was validly submitted
         if (isset($this->Comment))
            $this->Form->SetData($this->Comment);
            
      } else {
      */
         // Save as a draft?
         if (MbqMain::$cmd == 'reply_post') {
             $FormValues = array(   //make form values
                "TransientKey" => '',
                "hpt" => '',
                "DiscussionID" => $oMbqEtForumPost->topicId->oriValue,
                "CommentID" => '',
                "DraftID" => '',
                "Body" => $this->exttMbqConvertBbcodeQuote($oMbqEtForumPost->postContent->oriValue),
                "LastCommentID" => '0'
             );
         } elseif (MbqMain::$cmd == 'save_raw_post') {
             $FormValues = array(   //make form values
                "TransientKey" => '',
                "hpt" => '',
                "DiscussionID" => $oMbqEtForumPost->topicId->oriValue,
                "CommentID" => $oMbqEtForumPost->postId->oriValue,
                "DraftID" => '',
                "Body" => $this->exttMbqConvertBbcodeQuote($oMbqEtForumPost->postContent->oriValue),
                "LastCommentID" => $oMbqEtForumPost->oMbqEtForumTopic->mbqBind['oStdForumTopic']->LastCommentID
             );
         } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid cmd!');
         }
         foreach ($FormValues as $k => $v) {
            $this->Form->SetFormValue($k, $v);
         }
         $FormValues = $this->Form->FormValues();
         if (APPLICATION_VERSION >= '2.0.18.8') {   //compatible with 2.0.18.8 version
            $FormValues = $this->CommentModel->FilterForm($FormValues);
         }
         if ($DraftID == 0)
            $DraftID = $this->Form->GetFormValue('DraftID', 0);
         
         $Type = GetIncomingValue('Type');
         $Draft = $Type == 'Draft';
         $this->EventArguments['Draft'] = $Draft;
         $Preview = $Type == 'Preview';
         if ($Draft) {
            $DraftID = $this->DraftModel->Save($FormValues);
            $this->Form->AddHidden('DraftID', $DraftID, TRUE);
            $this->Form->SetValidationResults($this->DraftModel->ValidationResults());
         } else if (!$Preview) {
            $Inserted = !$CommentID;
            $CommentID = $this->CommentModel->Save($FormValues);
            $oMbqEtForumPost->postId->setOriValue($CommentID);

            // The comment is now half-saved.
            if (is_numeric($CommentID) && $CommentID > 0) {
               if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
                  $this->CommentModel->Save2($CommentID, $Inserted, TRUE, TRUE);
               } else {
                  $this->JsonTarget('', Url("/vanilla/post/comment2/$CommentID/$Inserted"), 'Ajax');
               }

               // $Discussion = $this->DiscussionModel->GetID($DiscussionID);
               $Comment = $this->CommentModel->GetID($CommentID);

               $this->EventArguments['Discussion'] = $Discussion;
               $this->EventArguments['Comment'] = $Comment;
               $this->FireEvent('AfterCommentSave');
               $oMbqEtForumPost->state->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.state.range.postOk'));
            } elseif ($CommentID === SPAM) {
               $this->StatusMessage = T('Your post has been flagged for moderation.');
               $oMbqEtForumPost->state->setOriValue(MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.state.range.postOkNeedModeration'));
            }
            
            $this->Form->SetValidationResults($this->CommentModel->ValidationResults());
            if ($CommentID > 0 && $DraftID > 0)
               $this->DraftModel->Delete($DraftID);
         }
         
         // Handle non-ajax requests first:
         if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
            if ($this->Form->ErrorCount() == 0) {
               // Make sure that this form knows what comment we are editing.
               if ($CommentID > 0)
                  $this->Form->AddHidden('CommentID', $CommentID);
               
               // If the comment was not a draft
               if (!$Draft) {
                  // Redirect to the new comment.
                  if ($CommentID > 0)
                     //Redirect("discussion/comment/$CommentID/#Comment_$CommentID");
                     return;
                  elseif ($CommentID == SPAM) {
                     $this->SetData('DiscussionUrl', '/discussion/'.$DiscussionID.'/'.Gdn_Format::Url($Discussion->Name));
                     $this->View = 'Spam';
           
                  }
               } elseif ($Preview) {
                  // If this was a preview click, create a comment shell with the values for this comment
                  $this->Comment = new stdClass();
                  $this->Comment->InsertUserID = $Session->User->UserID;
                  $this->Comment->InsertName = $Session->User->Name;
                  $this->Comment->InsertPhoto = $Session->User->Photo;
                  $this->Comment->DateInserted = Gdn_Format::Date();
                  $this->Comment->Body = ArrayValue('Body', $FormValues, '');
                  $this->AddAsset('Content', $this->FetchView('preview'));
               } else {
                  // If this was a draft save, notify the user about the save
                  $this->InformMessage(sprintf(T('Draft saved at %s'), Gdn_Format::Date()));
               }
            }
         } else {
            // Handle ajax-based requests
            if ($this->Form->ErrorCount() > 0) {
               // Return the form errors
               $this->ErrorMessage($this->Form->Errors());
            } else {
               // Make sure that the ajax request form knows about the newly created comment or draft id
               $this->SetJson('CommentID', $CommentID);
               $this->SetJson('DraftID', $DraftID);
               
               if ($Preview) {
                  // If this was a preview click, create a comment shell with the values for this comment
                  $this->Comment = new stdClass();
                  $this->Comment->InsertUserID = $Session->User->UserID;
                  $this->Comment->InsertName = $Session->User->Name;
                  $this->Comment->InsertPhoto = $Session->User->Photo;
                  $this->Comment->DateInserted = Gdn_Format::Date();
                  $this->Comment->Body = ArrayValue('Body', $FormValues, '');
                  $this->View = 'preview';
               } elseif (!$Draft) { // If the comment was not a draft
                  // If Editing a comment 
                  if ($Editing) {
                     // Just reload the comment in question
                     $this->Offset = 1;
                     $this->SetData('CommentData', $this->CommentModel->GetIDData($CommentID), TRUE);
                     // Load the discussion
                     $this->ControllerName = 'discussion';
                     $this->View = 'comments';
                     
                     // Also define the discussion url in case this request came from the post screen and needs to be redirected to the discussion
                     $this->SetJson('DiscussionUrl', Url('/discussion/'.$DiscussionID.'/'.Gdn_Format::Url($this->Discussion->Name).'/#Comment_'.$CommentID));
                  } else {
                     // If the comment model isn't sorted by DateInserted or CommentID then we can't do any fancy loading of comments.
                     $OrderBy = GetValueR('0.0', $this->CommentModel->OrderBy());
                     $Redirect = !in_array($OrderBy, array('c.DateInserted', 'c.CommentID'));
							$DisplayNewCommentOnly = $this->Form->GetFormValue('DisplayNewCommentOnly');

                     if (!$Redirect) {
                        // Otherwise load all new comments that the user hasn't seen yet
                        $LastCommentID = $this->Form->GetFormValue('LastCommentID');
                        if (!is_numeric($LastCommentID))
                           $LastCommentID = $CommentID - 1; // Failsafe back to this new comment if the lastcommentid was not defined properly

                        // Don't reload the first comment if this new comment is the first one.
                        $this->Offset = $LastCommentID == 0 ? 1 : $this->CommentModel->GetOffset($LastCommentID);
                        // Do not load more than a single page of data...
                        $Limit = C('Vanilla.Comments.PerPage', 50);

                        // Redirect if the new new comment isn't on the same page.
                        $Redirect |= !$DisplayNewCommentOnly && PageNumber($this->Offset, $Limit) != PageNumber($Discussion->CountComments - 1, $Limit);
                     }

                     if ($Redirect) {
                        // The user posted a comment on a page other than the last one, so just redirect to the last page.
                        $this->RedirectUrl = Gdn::Request()->Url("discussion/comment/$CommentID/#Comment_$CommentID", TRUE);
                        $this->CommentData = NULL;
                     } else {
                        // Make sure to load all new comments since the page was last loaded by this user
								if ($DisplayNewCommentOnly)
									$this->SetData('CommentData', $this->CommentModel->GetIDData($CommentID), TRUE);
								else 
									$this->SetData('CommentData', $this->CommentModel->GetNew($DiscussionID, $LastCommentID), TRUE);

                        $this->SetData('NewComments', TRUE);
                        $this->ControllerName = 'discussion';
                        $this->View = 'comments';
                     }
                     
                     // Make sure to set the user's discussion watch records
                     $CountComments = $this->CommentModel->GetCount($DiscussionID);
                     $Limit = is_object($this->CommentData) ? $this->CommentData->NumRows() : $Discussion->CountComments;
                     $Offset = $CountComments - $Limit;
                     $this->CommentModel->SetWatch($this->Discussion, $Limit, $Offset, $CountComments);
                  }
               } else {
                  // If this was a draft save, notify the user about the save
                  $this->InformMessage(sprintf(T('Draft saved at %s'), Gdn_Format::Date()));
               }
               // And update the draft count
               $UserModel = Gdn::UserModel();
               $CountDrafts = $UserModel->GetAttribute($Session->UserID, 'CountDrafts', 0);
               $this->SetJson('MyDrafts', T('My Drafts'));
               $this->SetJson('CountDrafts', $CountDrafts);
            }
         }
      /*
      }
      */
      
      // Include data for FireEvent
      if (property_exists($this,'Discussion'))
         $this->EventArguments['Discussion'] = $this->Discussion;
      if (property_exists($this,'Comment'))
         $this->EventArguments['Comment'] = $this->Comment;
         
      $this->FireEvent('BeforeCommentRender');
      
      // Render default view
      //$this->Render();
   }
   
   /**
    * Edit a comment (wrapper for PostController::Comment).
    *
    * Will throw an error if both params are blank.
    *
    * @since 2.0.0
    * @access public
    * 
    * @param int $CommentID Unique ID of the comment to edit.
    * @param int $DraftID Unique ID of the draft to edit.
    * @param  Object  $oMbqEtForumPost
    */
   public function exttMbqEditComment($CommentID = '', $DraftID = '', &$oMbqEtForumPost) {
      $CommentID = $oMbqEtForumPost->postId->oriValue;
      $this->Form = $this->Form ? $this->Form : new Gdn_Form();
      $this->CommentModel = $this->CommentModel ? $this->CommentModel : new CommentModel();
      if (is_numeric($CommentID) && $CommentID > 0) {
         $this->Form->SetModel($this->CommentModel);
         $this->Comment = $this->CommentModel->GetID($CommentID);
      } else {
         $this->Form->SetModel($this->DraftModel);
         $this->Comment = $this->DraftModel->GetID($DraftID);
      }
      $this->View = 'Comment';
      //$this->Comment($this->Comment->DiscussionID);
      $this->exttMbqComment($this->Comment->DiscussionID, $oMbqEtForumPost);
   }
   
}

?>