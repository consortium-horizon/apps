<?php
/**
 * ExttMbqCategoriesController extended from CategoriesController
 * add method exttMbqGetForumTopics() modified from method Index().
 * modify method Initialize()
 * 
 * @since  2012-10-15
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqCategoriesController extends CategoriesController {
   
   /**
    * Show all discussions in a particular category.
    * 
    * @since 2.0.0
    * @access public
    * 
    * @param string $CategoryIdentifier Unique category slug or ID.
    * @param Object  $oMbqDataPage
    * @return  Object  
    */
   public function exttMbqGetForumTopics($CategoryIdentifier = '', $oMbqDataPage) {
      $Page = $oMbqDataPage->curPage - 1;   //page start from 0
      $Category = CategoryModel::Categories($CategoryIdentifier);
      
      if (empty($Category)) {
         if ($CategoryIdentifier)
            //throw NotFoundException();
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
      }
      $Category = (object)$Category;
			
		// Load the breadcrumbs.
      $this->SetData('Breadcrumbs', CategoryModel::GetAncestors(GetValue('CategoryID', $Category)));
      
      $this->SetData('Category', $Category, TRUE);

      // Setup head
      $this->AddCssFile('vanilla.css');
      //$this->Menu->HighlightRoute('/discussions');      
      if ($this->Head) {
         $this->Title(GetValue('Name', $Category, ''));
         $this->AddJsFile('discussions.js');
         $this->AddJsFile('bookmark.js');
         $this->AddJsFile('options.js');
         $this->AddJsFile('jquery.gardenmorepager.js');
         $this->Head->AddRss($this->SelfUrl.'/feed.rss', $this->Head->Title());
      }
      
      // Set CategoryID
      $this->SetData('CategoryID', GetValue('CategoryID', $Category), TRUE);
      
      // Add modules
      $this->AddModule('NewDiscussionModule');
      $this->AddModule('CategoriesModule');
      $this->AddModule('BookmarkedModule');
      
      // Get a DiscussionModel
      $DiscussionModel = new DiscussionModel();
      $Wheres = array('d.CategoryID' => $this->CategoryID);
      
      // Check permission
      //$this->Permission('Vanilla.Discussions.View', TRUE, 'Category', GetValue('PermissionCategoryID', $Category));
      if (!Gdn::Session()->CheckPermission('Vanilla.Discussions.View', TRUE, 'Category', GetValue('PermissionCategoryID', $Category))) {
        MbqError::alert('', '', '', MBQ_ERR_APP);
      }
      
      // Set discussion meta data.
      //$this->EventArguments['PerPage'] = C('Vanilla.Discussions.PerPage', 30);
      $this->EventArguments['PerPage'] = $oMbqDataPage->numPerPage;
      $this->FireEvent('BeforeGetDiscussions');
      list($Offset, $Limit) = OffsetLimit($Page, $this->EventArguments['PerPage']);
      
      if (!is_numeric($Offset) || $Offset < 0)
         $Offset = 0;
         
      $Offset = $oMbqDataPage->startNum;    //fixed pagination issue
      $Limit = $oMbqDataPage->numPerPage;
      $CountDiscussions = $DiscussionModel->GetCount($Wheres);
      $CountDiscussionsWithNoAnnouncements = $DiscussionModel->GetCount($Wheres, true);
      $this->SetData('CountDiscussions', $CountDiscussions);
      $this->SetData('_Limit', $Limit);
      $AnnounceData = $Offset == 0 ? $DiscussionModel->GetAnnouncements($Wheres) : new Gdn_DataSet();
      $this->SetData('AnnounceData', $AnnounceData, TRUE);
      $this->DiscussionData = $this->SetData('Discussions', $DiscussionModel->Get($Offset, $Limit, $Wheres));

      // Build a pager
      $PagerFactory = new Gdn_PagerFactory();
      $this->Pager = $PagerFactory->GetPager('Pager', $this);
      $this->Pager->ClientID = 'Pager';
      $this->Pager->Configure(
         $Offset,
         $Limit,
         $CountDiscussions,
         'categories/'.$CategoryIdentifier.'/%1$s'
      );
      $this->SetData('_PagerUrl', 'categories/'.rawurlencode($CategoryIdentifier).'/{Page}');
      $this->SetData('_Page', $Page);

      // Set the canonical Url.
      $this->CanonicalUrl(Url(ConcatSep('/', 'categories/'.GetValue('UrlCode', $Category, $CategoryIdentifier), PageNumber($Offset, $Limit, TRUE, FALSE)), TRUE));
      
      // Change the controller name so that it knows to grab the discussion views
      $this->ControllerName = 'DiscussionsController';
      // Pick up the discussions class
      $this->CssClass = 'Discussions';
      
      // Deliver JSON data if necessary
      if ($this->_DeliveryType != DELIVERY_TYPE_ALL) {
         $this->SetJson('LessRow', $this->Pager->ToString('less'));
         $this->SetJson('MoreRow', $this->Pager->ToString('more'));
         $this->View = 'discussions';
      }
      
      return array('total' => $CountDiscussions - $AnnounceData->NumRows(), 'topics' => $this->DiscussionData);
      // Render default view
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
      parent::Initialize();
   }      
}

?>