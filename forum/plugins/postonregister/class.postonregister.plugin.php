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
$age = "1984";

class postonregister extends Gdn_Plugin {

    /**
    * Load assets.
    */
    public function Base_Render_Before($sender, $args)
    {
        // Load the assets if not admin
        if ($sender->MasterView != 'admin') {
            $sender->AddCssFile('postonregister.css', 'plugins/postonregister');
        }
    }

    /**
    * Custom notification on registration page.
    */
    public function EntryController_registerBeforePassword_Handler($Sender, $Args) {
        echo '<div class="registerNotification">
        <h2>Remplissez attentivement les champs demandés !</h2>
        <p>Notre communauté est constituée de personnes matures et responsables, toute faute de français, description attive ou manque de rigueur peut donc vous être préjudiciable.</p>
        <p>Les informations seront passées en revue par un modérateur puis utilisées pour créer votre post de candidature.</p>
        </div>';
    }

    /**
    * Get form values.
    */
    public function entryController_RegisterValidation_handler($sender) {
        $GLOBALS['var'] = $sender->Form->_FormValues['Aqueljeujouezvousgalement'];
        $GLOBALS['quelJeu'] = $sender->Form->_FormValues['Pourqueljeupostulezvous'];
        $GLOBALS['comment'] = $sender->Form->_FormValues['CommentavezvousetconnaissanceduConsortiumHorizon'];
        $GLOBALS['plusSurVous'] = $sender->Form->_FormValues['Ditesnousenplussurvous'];

        // get the age of the new applicant
        $dateString =$sender->Form->_FormValues['DateOfBirth_Year'].'-'.$sender->Form->_FormValues['DateOfBirth_Month'].'-'.$sender->Form->_FormValues['DateOfBirth_Day'];
        $diff = abs(strtotime(date('Y-m-d')) - strtotime($dateString));
        $GLOBALS['age'] = floor($diff / (365*60*60*24));
    }

    /**
    * Create new discussion.
    */
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

        [b]Quel âge avez vous ? ?[/b]

        '
        . $GLOBALS['age'] .' ans.'.
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

        [color=#FF0000]En attente de validation par un modérateur[/color]';
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
