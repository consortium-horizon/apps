<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2010 Luc Brouard
*/

// Define the plugin:
$PluginInfo['UnreadDiscussion'] = array(
   'Description' => 'Add an option to unread discussion.',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.0.17.8'), 
   'PluginUrl' => 'http://vanillaforums.org/addon/503/unreaddiscussion',
   'Author' => "Luc Brouard",
   'AuthorEmail' => 'bean@subtitles.toh.info',
   'AuthorUrl' => 'http://subtitles.toh.info'
);

class UnreadDiscussionPlugin extends Gdn_Plugin {
    // To add in http://vanilla2/discussions
    public function DiscussionsController_Render_Before(&$Sender) {
        $Sender->AddJsFile('/plugins/UnreadDiscussion/unreaddiscussion.js');
    }

    // To have unread links in http://vanilla2/discussions
    public function DiscussionsController_DiscussionOptions_Handler(&$Sender) {
	$Session = Gdn::Session();
	$Discussion = $Sender->EventArguments['Discussion'];
        $Sender->Options .= '<li>'.Anchor(T('Unread'), 'vanilla/discussion/unread/'.$Discussion->DiscussionID.'/'.$Session->TransientKey().'?Target='.urlencode($Sender->SelfUrl), 'UnreadDiscussion') . '</li>';
       	$Sender->StatusMessage = &$Sender->EventArguments[0];
    }

    // Action to mark as unread after click the unread link in option
    public function DiscussionController_Unread_Create(&$Sender, $Args) {
        $DiscussionID = $Args[0];
        $TransientKey = $Args[1];
        $Session = Gdn::Session();
        $State = '0';
        if (
         is_numeric($DiscussionID)
         && $DiscussionID > 0
         && $Session->UserID > 0
         && $Session->ValidateTransientKey($TransientKey)
        ) {
          $SQL = Gdn::SQL();
          $SQL
             ->Update('UserDiscussion')
             ->Set('CountComments', '0')
             ->Set('DateLastViewed', NULL)
             ->Where('UserID', $Session->UserID)
             ->Where('DiscussionID', $DiscussionID)
             ->Put();

          $State = '1';
        }
        $Sender->SetJson('State', $State);
        //$Sender->SetJson('LinkText', Translate($State ? 'PhpRead' : 'PhpUnread'));
        $Sender->StatusMessage = T('Discussion is now unread.');
        $Sender->Render();
    }

    public function Setup() {
        // This setup method should trigger errors when it encounters them - the plugin manager will catch the errors...
    }

}
