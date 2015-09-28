<?php if (!defined('APPLICATION')) exit();

$PluginInfo['postonregister'] = array(
    'Name' => 'Post on register',
    'Description' => 'Create a new post each time a new user registers',
    'Version' => '0.1',
    'Author' => 'Vladvonvidden',
);

// Globals
$var = "Aucun";
$quelJeu = "Aucun";
$comment = "Par Internet";
$plusSurVous = "je suis quelqu'un de distrait";

class postonregister extends Gdn_Plugin {

    public function entryController_RegisterValidation_handler($sender) {
        $GLOBALS['var'] = $sender->Form->_FormValues['Aqueljeujouezvousgalement'];
        $GLOBALS['quelJeu'] = $sender->Form->_FormValues['Pourqueljeupostulezvous'];
        $GLOBALS['comment'] = $sender->Form->_FormValues['CommentavezvousetconnaissanceduConsortiumHorizon'];
        $GLOBALS['plusSurVous'] = $sender->Form->_FormValues['Ditesnousenplussurvous'];
    }

    public function userModel_afterRegister_handler($sender, $Args) {
        // Get user ID from sender
        $userID = $sender->EventArguments['UserID'];
        // Retreive user object
        $user = $sender->GetID($userID);
        // Get UserName
        $name = GetValue('Name', $user, $Default = FALSE, $Remove = FALSE);
        // Get first visit date
        $date = Gdn_Format::ToDateTime();
        // Create new discussionModel
        $DiscussionModel = new DiscussionModel();
        // Feed it ! Feeeeeeeeed it !
        $SQL = Gdn::Database()->SQL();
        // Where you wanna insert the discussion (which category)
        $Discussion['CategoryID'] = '9';
        // Discussion Format (BBcode)
        $Discussion['Format'] = 'BBCode';
        // Discussion title
        $Discussion['Name'] = '[' . $GLOBALS['quelJeu'] . '] ' . (string) $name . ' [En attente de validation]';
        // Discussion content
        $Discussion['Body'] = '[b]Pour quel jeu en particulier postulez-vous dans la Guilde ?[/b]

        '
        . $GLOBALS['quelJeu'] .
        '

        [b]A quels autres jeux jouez-vous également ?[/b]

        '
        . $GLOBALS['var'] .
        '

        [b]Comment avez-eu connaissance du Consortium Horizon ?[/b]

        '
        . $GLOBALS['comment'] .
        '

        [b]Dites-en un peu plus sur vous :[/b]

        '
        . $GLOBALS['plusSurVous'] .
        '

        En attente de validation par un modérateur';
        // Date of creation
        $Discussion['DateInserted'] = $date;
        // Date of last comment
        $Discussion['DateLastComment'] = $date;
        // The author
        $Discussion['InsertUserID'] = $userID ;
        // Insert in the right category
        $DiscussionID = $SQL->Insert('Discussion', $Discussion);
        // If everything is ok, refresh discussion count
        if ($DiscussionID) { $DiscussionModel->UpdateDiscussionCount($Discussion['CategoryID']) ;}
    }
}
