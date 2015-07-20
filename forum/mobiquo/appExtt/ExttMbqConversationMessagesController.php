<?php

require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDummyMenu.php');

/**
 * ExttMbqConversationMessagesController extended from MessagesController
 * add method exttMbqStartConversation() modified from method Add().
 * add method exttMbqAddMessage() modified from method AddMessage().
 * modify method Initialize()
 * 
 * @since  2012-11-6
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqConversationMessagesController extends MessagesController {
   
   /**
    * Highlight route and include JS, CSS, and modules used by all methods.
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
   
   /**
    * Start a new conversation.
    *
    * @since 2.0.0
    * @access public
    *
    * @param string $Recipient Username of the recipient.
    * @param  Object  $oMbqEtPc
    */
   public function exttMbqStartConversation($Recipient = '', &$oMbqEtPc) {
      $this->Form = $this->Form ? $this->Form : new Gdn_Form();
      $this->ConversationModel = $this->ConversationModel ? $this->ConversationModel : new ConversationModel();
      $this->ConversationMessageModel = $this->ConversationMessageModel ? $this->ConversationMessageModel : new ConversationMessageModel();
      /* make post as page post */
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqPostController.php');
      $oExttMbqPostController = new ExttMbqPostController();
      $oExttMbqPostController->Initialize();
      $_POST['Conversation/TransientKey'] = '';
      $_POST['Conversation/hpt'] = '';
      $_POST['Conversation/To'] = implode(',', $oMbqEtPc->userNames->oriValue);
      $_POST['Conversation/Body'] = $oExttMbqPostController->exttMbqConvertBbcodeQuote($oMbqEtPc->convContent->oriValue);
      $_POST['Conversation/Start_Conversation'] = 'Start Conversation';
      $_POST['DeliveryType'] = 'VIEW';
      $_POST['DeliveryMethod'] = 'JSON';
      $this->Form->SetFormValue('TransientKey', $_POST['Conversation/TransientKey']);
      $this->Form->SetFormValue('hpt', $_POST['Conversation/hpt']);
      $this->Form->SetFormValue('To', $_POST['Conversation/To']);
      $this->Form->SetFormValue('Body', $_POST['Conversation/Body']);
      $this->Form->SetFormValue('Start_Conversation', $_POST['Conversation/Start_Conversation']);
      $this->Form->SetFormValue('DeliveryType', $_POST['DeliveryType']);
      $this->Form->SetFormValue('DeliveryMethod', $_POST['DeliveryMethod']);
    
      $this->Form->SetModel($this->ConversationModel);
      
      //if ($this->Form->AuthenticatedPostBack()) {
         $RecipientUserIDs = array();
         $To = explode(',', $this->Form->GetFormValue('To', ''));
         $UserModel = new UserModel();
         foreach ($To as $Name) {
            if (trim($Name) != '') {
               $User = $UserModel->GetByUsername(trim($Name));
               if (is_object($User))
                  $RecipientUserIDs[] = $User->UserID;
            }
         }
         $this->Form->SetFormValue('RecipientUserID', $RecipientUserIDs);
         $ConversationID = $this->Form->Save($this->ConversationMessageModel);
         if (!$ConversationID) {
            MbqError::alert('', MBQ_ERR_INFO_SAVE_FAIL, '', MBQ_ERR_APP);
         } else {
            $oMbqEtPc->convId->setOriValue($ConversationID);
         }
         /*
         if ($ConversationID !== FALSE) {
            $Target = $this->Form->GetFormValue('Target', 'messages/'.$ConversationID);
            
            $this->RedirectUrl = Url($Target);
         }
         */
      /*
      } else {
         if ($Recipient != '')
            $this->Form->SetFormValue('To', $Recipient);
      }
      if ($Target = Gdn::Request()->Get('Target'))
            $this->Form->AddHidden('Target', $Target);

      $this->Render();
      */      
   }
   
   
   
   /**
    * Add a message to a conversation.
    *
    * @since 2.0.0
    * @access public
    * 
    * @param int $ConversationID Unique ID of the conversation.
    * @param  Object  $oMbqEtPcMsg
    * @param  Object  $oMbqEtPc
    */
   public function exttMbqAddMessage($ConversationID = '', &$oMbqEtPcMsg, $oMbqEtPc) {
      $this->Form = $this->Form ? $this->Form : new Gdn_Form();
      $this->ConversationMessageModel = $this->ConversationMessageModel ? $this->ConversationMessageModel : new ConversationMessageModel();
      /* make post as page post */
      require_once(MBQ_APPEXTENTION_PATH.'ExttMbqPostController.php');
      $oExttMbqPostController = new ExttMbqPostController();
      $oExttMbqPostController->Initialize();
      $_POST['ConversationMessage/TransientKey'] = '';
      $_POST['ConversationMessage/hpt'] = '';
      $_POST['ConversationMessage/ConversationID'] = $oMbqEtPcMsg->convId->oriValue;
      $_POST['ConversationMessage/Body'] = $oExttMbqPostController->exttMbqConvertBbcodeQuote($oMbqEtPcMsg->msgContent->oriValue);
      $_POST['DeliveryType'] = 'VIEW';
      $_POST['DeliveryMethod'] = 'JSON';
      $_POST['ConversationMessage/Send_Message'] = 'Send Message';
      $_POST['Conversation/BodyLastMessageID'] = 'Message_'.$oMbqEtPc->mbqBind['oStdPc']->LastMessageID;
      $this->Form->SetFormValue('TransientKey', $_POST['ConversationMessage/TransientKey']);
      $this->Form->SetFormValue('hpt', $_POST['ConversationMessage/hpt']);
      $this->Form->SetFormValue('ConversationID', $_POST['ConversationMessage/ConversationID']);
      $this->Form->SetFormValue('Body', $_POST['ConversationMessage/Body']);
      $this->Form->SetFormValue('DeliveryType', $_POST['DeliveryType']);
      $this->Form->SetFormValue('DeliveryMethod', $_POST['DeliveryMethod']);
      $this->Form->SetFormValue('Send_Message', $_POST['ConversationMessage/Send_Message']);
      $this->Form->SetFormValue('BodyLastMessageID', $_POST['Conversation/BodyLastMessageID']);
      
      $this->Form->SetModel($this->ConversationMessageModel);
      if (is_numeric($ConversationID) && $ConversationID > 0)
         $this->Form->AddHidden('ConversationID', $ConversationID);
      
      //if ($this->Form->AuthenticatedPostBack()) {
         $ConversationID = $this->Form->GetFormValue('ConversationID', '');
         $NewMessageID = $this->Form->Save();
         if ($NewMessageID) {
            $oMbqEtPcMsg->msgId->setOriValue($NewMessageID);
         } else {
            MbqError::alert('', MBQ_ERR_INFO_SAVE_FAIL, '', MBQ_ERR_APP);
         }
      /*
         if ($NewMessageID) {
            if ($this->DeliveryType() == DELIVERY_TYPE_ALL)
               Redirect('messages/'.$ConversationID.'/#'.$NewMessageID);
               
            $this->SetJson('MessageID', $NewMessageID);
            // If this was not a full-page delivery type, return the partial response
            // Load all new messages that the user hasn't seen yet (including theirs)
            $LastMessageID = $this->Form->GetFormValue('LastMessageID');
            if (!is_numeric($LastMessageID))
               $LastMessageID = $NewMessageID - 1;
            
            $Session = Gdn::Session();
            $Conversation = $this->ConversationModel->GetID($ConversationID, $Session->UserID);   
            $MessageData = $this->ConversationMessageModel->GetNew($ConversationID, $LastMessageID);
            $this->Conversation = $Conversation;
            $this->MessageData = $MessageData;

            $this->View = 'messages';
         } else {
            // Handle ajax based errors...
            if ($this->DeliveryType() != DELIVERY_TYPE_ALL)
               $this->ErrorMessage($this->Form->Errors());
         }
      }
      $this->Render();      
      */
   }
   
}

?>