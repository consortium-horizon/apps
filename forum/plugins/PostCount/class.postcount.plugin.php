<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['PostCount'] = array(
   'Name' => 'Post Count',
   'Description' => "This plugin shows each user's post count along with their messages.",
   'Version' => '1.0.2',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => FALSE,
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "Tim Gunter",
   'AuthorEmail' => 'tim@vanillaforums.com',
   'AuthorUrl' => 'http://www.vanillaforums.com'
);

class PostCountPlugin extends Gdn_Plugin {
   
   public function UserInfoModule_OnBasicInfo_Handler(&$Sender) {
      $UserID = $Sender->User->UserID;
      $PostCount = GetValue("PostCount", Gdn::Database()->Query(sprintf("SELECT COALESCE(u.CountComments,0) + COALESCE(u.CountDiscussions,0) AS PostCount FROM GDN_User u WHERE UserID = %d",$UserID))->FirstRow(DATASET_TYPE_ARRAY),0);
      echo "<dt>".T(Plural($PostCount, 'Posts', 'Posts'))."</dt>\n";
      echo "<dd>".number_format($PostCount)."</dd>";
   }
   
   public function DiscussionController_BeforeDiscussionRender_Handler(&$Sender) {
      $this->_CachePostCounts($Sender);
   }
   
   public function PostController_BeforeCommentRender_Handler(&$Sender) {
      $this->_CachePostCounts($Sender);
   }
   
   protected function _CachePostCounts(&$Sender) {
      $Discussion = $Sender->Data('Discussion');
      $Comments = $Sender->Data('CommentData');
      $UserIDList = array();
      
      if ($Discussion)
         $UserIDList[$Discussion->InsertUserID] = 1;
         
      if ($Comments && $Comments->NumRows()) {
         $Comments->DataSeek(-1);
         while ($Comment = $Comments->NextRow())
            $UserIDList[$Comment->InsertUserID] = 1;
      }
      
      $UserPostCounts = array();
      if (sizeof($UserIDList)) {
/*
         $PostCounts = Gdn::SQL()
            ->Select('u.UserID')
            ->Select('u.CountComments + u.CountDiscussions', FALSE, 'PostCount')
            ->From('User u')
            ->WhereIn('UserID', array_keys($UserIDList))
            ->Get();
*/
         $PostCounts = Gdn::Database()->Query(sprintf("
            SELECT 
               u.UserID,
               COALESCE(u.CountComments,0) + COALESCE(u.CountDiscussions,0) AS PostCount 
            FROM GDN_User u 
            WHERE UserID IN (%s)",implode(",",array_keys($UserIDList))));
            
         $PostCounts->DataSeek(-1);
         while ($UserPostCount = $PostCounts->NextRow())
            $UserPostCounts[$UserPostCount->UserID] = $UserPostCount->PostCount;
      }
      $Sender->SetData('Plugin-PostCount-Counts', $UserPostCounts);
   }
   
   public function DiscussionController_CommentInfo_Handler(&$Sender) {
      $this->_AttachPostCount($Sender);
   }
   
   public function PostController_CommentInfo_Handler(&$Sender) {
      $this->_AttachPostCount($Sender);
   }
   
   protected function _AttachPostCount(&$Sender) {
      $Posts = ArrayValue($Sender->EventArguments['Author']->UserID, $Sender->Data('Plugin-PostCount-Counts'));
      echo '<span>'.T(Plural(number_format($Posts),'Posts: %s','Posts: %s')).'</span>';
   }

   public function Setup() {
      // Nothing to do here!
   }
   
   public function Structure() {
      // Nothing to do here!
   }
         
}