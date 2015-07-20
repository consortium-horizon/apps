<?php
/**
 * ExttMbqDiscussionModel extended from DiscussionModel
 * add method exttMbqGetTopics() modified from method Get().
 * modify method __construct()
 * 
 * @since  2012-10-17
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqDiscussionModel extends DiscussionModel {
   
   /**
    * Class constructor. Defines the related database table name.
    * 
    * @since 2.0.0
    * @access public
    */
   public function __construct() {
      parent::__construct();
   }
   
   /**
    * Gets the data for multiple discussions based on the given criteria.
    * 
    * Sorts results based on config options Vanilla.Discussions.SortField
    * and Vanilla.Discussions.SortDirection.
    * Events: BeforeGet, AfterAddColumns.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param int $Offset Number of discussions to skip.
    * @param int $Limit Max number of discussions to return.
    * @param array $Wheres SQL conditions.
    * @param array $AdditionalFields Allows selection of additional fields as Alias=>Table.Fieldname.
    * $mbqOpt['topicIds'] means get topics with these topicIds
    * $mbqOpt['forumId'] means get topics in this forum(not include sub-forum topics)
    * $mbqOpt['noAnnouncements'] = true means get topics not include announcements
    * $mbqOpt['onlyAnnouncements'] = true means get topics only include announcements
    * $mbqOpt['unread'] = true means get unread topics
    * $mbqOpt['participated'] = true means get participated topics
    * $mbqOpt['onlyGetSqlForTopicIds'] = true means only get sql of topic ids
    * $mbqOpt['authorUserId'] means get topics created by author user id,the $mbqOpt['authorUserId'] is the author user id
    * @return Gdn_DataSet SQL result.
    */
   public function exttMbqGetTopics($Offset = '0', $Limit = '', $Wheres = '', $AdditionalFields = NULL, $mbqOpt = array()) {
      if ($Limit == '') 
         //$Limit = Gdn::Config('Vanilla.Discussions.PerPage', 50);
         $Limit = 0;    /* no limit */

      $Offset = !is_numeric($Offset) || $Offset < 0 ? 0 : $Offset;
      
      $Session = Gdn::Session();
      $UserID = $Session->UserID > 0 ? $Session->UserID : 0;
      $this->DiscussionSummaryQuery($AdditionalFields, FALSE);
      if(strpos($this->SQL->GetSelect(), 'd.*') !== FALSE)
      {
      }
      else
      {
          $this->SQL->Select('d.*');
      }
      if ($UserID > 0) {
         $this->SQL
            ->Select('w.UserID', '', 'WatchUserID')
            ->Select('w.DateLastViewed, w.Dismissed, w.Bookmarked')
            ->Select('w.CountComments', '', 'CountCommentWatch')
            ->Join('UserDiscussion w', 'd.DiscussionID = w.DiscussionID and w.UserID = '.$UserID, 'left');
      } else {
			$this->SQL
				->Select('0', '', 'WatchUserID')
				->Select('now()', '', 'DateLastViewed')
				->Select('0', '', 'Dismissed')
				->Select('0', '', 'Bookmarked')
				->Select('0', '', 'CountCommentWatch')
				->Select('d.Announce','','IsAnnounce');
      }
		
		$this->AddArchiveWhere($this->SQL);
      
      
      //$this->SQL->Limit($Limit, $Offset); //moved to before $this->SQL->Get() method
      
      $this->EventArguments['SortField'] = C('Vanilla.Discussions.SortField', 'd.DateLastComment');
      $this->EventArguments['SortDirection'] = C('Vanilla.Discussions.SortDirection', 'desc');
		$this->EventArguments['Wheres'] = &$Wheres;
		$this->FireEvent('BeforeGet'); // @see 'BeforeGetCount' for consistency in results vs. counts
      
      $IncludeAnnouncements = FALSE;
      if (strtolower(GetValue('Announce', $Wheres)) == 'all') {
         $IncludeAnnouncements = TRUE;
         unset($Wheres['Announce']);
      }

      if (is_array($Wheres))
         $this->SQL->Where($Wheres);
      
		// Get sorting options from config
		$SortField = $this->EventArguments['SortField'];
		if (!in_array($SortField, array('d.DiscussionID', 'd.DateLastComment', 'd.DateInserted')))
			$SortField = 'd.DateLastComment';
		
		$SortDirection = $this->EventArguments['SortDirection'];
		if ($SortDirection != 'asc')
			$SortDirection = 'desc';
			
		$this->SQL->OrderBy($SortField, $SortDirection);
		
		//$mbqOpt relative
		$mbqExttDbPre = $this->Database->DatabasePrefix;
		if ($mbqOpt['topicIds']) {
		    $this->SQL->WhereIn('d.DiscussionID', $mbqOpt['topicIds']);
		}
		if ($mbqOpt['noAnnouncements']) {
		    if ($UserID)
		        $this->SQL->Where('(d.Announce =', '0 || (d.Announce = 1 and w.Dismissed = 1))', false, false);
		    else
		        $this->SQL->Where('(d.Announce =', '0)', false, false);
		} else {
		    $IncludeAnnouncements = TRUE;
		}
		if ($mbqOpt['onlyAnnouncements']) {
		    $IncludeAnnouncements = TRUE;
		    if ($UserID)
		        $this->SQL->Where('(d.Announce = ', '1 and (w.Dismissed = 0 || ISNULL(w.Dismissed)))', false, false);
		    else
		        $this->SQL->Where('(d.Announce = ', '1)', false, false);
		}
		if ($mbqOpt['unread']) {
		    $this->SQL->Where('(d.DateLastComment >', 'w.DateLastViewed || ISNULL(w.DateLastViewed))', false, false);
		}
		if ($mbqOpt['participated']) {
		    $this->SQL->Where('d.DiscussionID in', '(select DiscussionID from ((select DiscussionID from '.$mbqExttDbPre."Discussion where InsertUserID = '{$UserID}') union all (select DiscussionID from ".$mbqExttDbPre."Comment where InsertUserID = '{$UserID}')) as did)", false, false);
		}
		if ($mbqOpt['authorUserId']) {
		    $this->SQL->Where('d.InsertUserID = ', '\''.addslashes($mbqOpt['authorUserId']).'\'', false, false);
		}
		
      // Set range and fetch
      $mbqExttSqlNoLimit = $this->SQL->GetSelect();
	  if ($mbqOpt['forumId']) {
	      if (preg_match('/d.CategoryID in \((.*?)\)/', $mbqExttSqlNoLimit, $matches)) {
	        $tempIds = explode(',', $matches[1]);
	        foreach ($tempIds as &$tempId) {
	            $tempId = trim(str_replace('\'', '', $tempId));
	        }
	        if (in_array($mbqOpt['forumId'], $tempIds)) {   //acl check
	            $mbqExttSqlNoLimit = preg_replace('/d\.CategoryID in \(.*?\)/', 'd.CategoryID = \''.addslashes($mbqOpt['forumId']).'\'', $mbqExttSqlNoLimit, 1);
	        } else {
	            MbqError::alert('', 'You looks has not this permission to read this forum!');
	        }
	      } else {
	        $this->SQL->WhereIn('d.CategoryID', array($mbqOpt['forumId']));
	        $mbqExttSqlNoLimit = $this->SQL->GetSelect();
	      }
	  }
      //remove repeated fields in sql for make $mbqExttCountSql,these repeated fields can cause sql syntax error as Duplicate column names.
      if (stripos($mbqExttSqlNoLimit, ' d.*, ') !== false) {
        $mbqExttSqlNoLimit = preg_replace('/d\.Type as \`Type\`,/', '', $mbqExttSqlNoLimit, 1);
        $mbqExttSqlNoLimit = preg_replace('/d\.CountBookmarks as \`CountBookmarks\`,/', '', $mbqExttSqlNoLimit, 1);
        $mbqExttSqlNoLimit = preg_replace('/d\.Body as \`Body\`,/', '', $mbqExttSqlNoLimit, 1);
        $mbqExttSqlNoLimit = preg_replace('/d\.Format as \`Format\`,/', '', $mbqExttSqlNoLimit, 1);
      }   
      //remove the redundant =,it can cause sql syntax error.
      if ($mbqOpt['participated']) {
        $mbqExttSqlNoLimit = preg_replace('/d\.DiscussionID in \= \(select DiscussionID from \(\(select DiscussionID from/', 'd.DiscussionID in (select DiscussionID from ((select DiscussionID from', $mbqExttSqlNoLimit, 1);
      }
      if ($mbqOpt['onlyGetSqlForTopicIds']) {
        $mbqExttSqlNoLimit = "select DiscussionID from ($mbqExttSqlNoLimit) as data";
        return $mbqExttSqlNoLimit;
      }
      $mbqExttCountSql = "select count(*) as totalNum from ($mbqExttSqlNoLimit) as data";
      //$this->SQL->Limit($Limit, $Offset);
      //$Data = $this->SQL->Get();
      if ($Limit) {
        $Data = $this->SQL->Query($mbqExttSqlNoLimit." limit $Offset,$Limit");
      } else {
        if ($Offset == 0) {
            $Data = $this->SQL->Query($mbqExttSqlNoLimit);
        } else {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        }
      }
      //$this->SQL->Reset();
      $topicsNum = $this->SQL->Query($mbqExttCountSql)->FirstRow()->totalNum;
         
      // If not looking at discussions filtered by bookmarks or user, filter announcements out.
      if (!$IncludeAnnouncements) {
         if (!isset($Wheres['w.Bookmarked']) && !isset($Wheres['d.InsertUserID']))
            $this->RemoveAnnouncements($Data);
      }
		
		// Change discussions returned based on additional criteria	
		$this->AddDiscussionColumns($Data);
      
      // Join in the users.
      Gdn::UserModel()->JoinUsers($Data, array('FirstUserID', 'LastUserID'));
      CategoryModel::JoinCategories($Data);
//      print_r($Data);
//      die();
		
      if (C('Vanilla.Views.Denormalize', FALSE))
         $this->AddDenormalizedViews($Data);
      
		// Prep and fire event
		$this->EventArguments['Data'] = $Data;
		$this->FireEvent('AfterAddColumns');
		
		//return $Data;
		return array('total' => $topicsNum, 'data' => $Data);
   }
   
}

?>