<?php
/**
 * ExttMbqConversationMessageModel extended from ConversationMessageModel
 * add method exttMbqGetPcMsgs() modified from method Get().
 * modify method __construct()
 * 
 * @since  2012-11-6
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqConversationMessageModel extends ConversationMessageModel {
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
    * Get messages by conversation.
    * 
    * Events: BeforeGet.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param int $ConversationID Unique ID of conversation being viewed.
    * @param int $ViewingUserID Unique ID of current user.
    * @param int $Offset Number to skip.
    * @param int $Limit Maximum to return.
    * @param array $Wheres SQL conditions.
    * @param array $mbqOpt
    $mbqOpt['pcMsgIds'] means get data in these ids.
    * @return Gdn_DataSet SQL results.
    */
   public function exttMbqGetPcMsgs($ConversationID, $ViewingUserID, $Offset = '0', $Limit = '', $Wheres = '', $mbqOpt = array()) {
      //if ($Limit == '') 
         //$Limit = Gdn::Config('Conversations.Messages.PerPage', 50);
      if (!$Offset && !$Limit) {
        $noLimit = true;
      }

      $Offset = !is_numeric($Offset) || $Offset < 0 ? 0 : $Offset;
      if (is_array($Wheres))
         $this->SQL->Where($Wheres);
         
      $this->FireEvent('BeforeGet');
      $this->SQL
         ->Select('cm.*')
         ->Select('iu.Name', '', 'InsertName')
         ->Select('iu.Email', '', 'InsertEmail')
         ->Select('iu.Photo', '', 'InsertPhoto')
         ->From('ConversationMessage cm')
         ->Join('Conversation c', 'cm.ConversationID = c.ConversationID')
         ->Join('UserConversation uc', 'c.ConversationID = uc.ConversationID and uc.UserID = '.$ViewingUserID, 'left')
         ->Join('User iu', 'cm.InsertUserID = iu.UserID', 'left')
         ->BeginWhereGroup()
         ->Where('uc.DateCleared is null') 
         ->OrWhere('uc.DateCleared <', 'cm.DateInserted', TRUE, FALSE) // Make sure that cleared conversations do not show up unless they have new messages added.
         ->EndWhereGroup()
         ->Where('cm.ConversationID', $ConversationID);
      if ($mbqOpt['pcMsgIds']) {
        $this->SQL->WhereIn('cm.MessageID', $mbqOpt['pcMsgIds']);
      }
      $mbqExttSqlNoLimit = $this->SQL->GetSelect();
      $oSql1 = clone $this->SQL;
      $arr['total'] = $this->SQL->Get()->NumRows();
      if ($noLimit) {
        $mbqExttSql = $mbqExttSqlNoLimit;
      } else {
        $mbqExttSql = "$mbqExttSqlNoLimit order by cm.DateInserted asc limit $Offset,$Limit";
      }
      $arr['pcMsgs'] = $oSql1->Query($mbqExttSql);
      return $arr;
      /*
      return $this->SQL
         ->Select('cm.*')
         ->Select('iu.Name', '', 'InsertName')
         ->Select('iu.Email', '', 'InsertEmail')
         ->Select('iu.Photo', '', 'InsertPhoto')
         ->From('ConversationMessage cm')
         ->Join('Conversation c', 'cm.ConversationID = c.ConversationID')
         ->Join('UserConversation uc', 'c.ConversationID = uc.ConversationID and uc.UserID = '.$ViewingUserID, 'left')
         ->Join('User iu', 'cm.InsertUserID = iu.UserID', 'left')
         ->BeginWhereGroup()
         ->Where('uc.DateCleared is null') 
         ->OrWhere('uc.DateCleared <', 'cm.DateInserted', TRUE, FALSE) // Make sure that cleared conversations do not show up unless they have new messages added.
         ->EndWhereGroup()
         ->Where('cm.ConversationID', $ConversationID)
         ->OrderBy('cm.DateInserted', 'asc')
         ->Limit($Limit, $Offset)
         ->Get();
      */
   }
   
}

?>