<?php if (!defined('APPLICATION')) exit();

$PluginInfo['PostOnRegister'] = array(
    'Name' => 'Post on register',
    'Description' => 'Create a new post each time a new user registers',
    'Version' => '0.1',
    'Author' => 'Vladvonvidden',
    'SettingsUrl' => '/dashboard/settings/postonregister',
    'SettingsPermission' => 'Garden.Settings.Manage',
);

$var="Aucun";
$annee="";

class PostOnRegister extends Gdn_Plugin {



    public function settingsController_PostOnRegister_create($sender)
    {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('/dashboard/settings/postonregister');
        $sender->setData('Title', t('Post on register settings'));
        $sender->setData('Description', t('Forum Tour Description'));

        $RegistrationFields = array();
        $counter = 1;

        foreach (c('ProfileExtender', array())['Fields'] as $key => $value) {
            if ($value['OnRegister'] == 1) {
                $RegistrationFields[$value['Name']] = $value['Label'];
            }
        }
        $sender->setData('RegistrationFields', $RegistrationFields);
        $sender->Render('settings', '', 'plugins/PostOnRegister');
    }

    // this is where the magic happen
    public function EntryController_registerBeforePassword_Handler($Sender, $Args) {

        // Check if user wanna join us
        echo '<div id="iWannaJoinNotif" class="registerNotification info">';
        echo '<input type="checkbox" id="iWannaJoin" name="iWannaJoin" value="iWannaJoin"> Je souhaite rejoindre la guilde (une candidature sera automatiquement créée)';
        echo '</div>';

        // The main form
        echo '<div id="lchForm" style="display: none;">';

        // Some instructions
        echo '<div class="registerNotification mainNotification info">
        <h2>Remplissez attentivement les champs demandés !</h2>
        <p>Notre communauté est constituée de personnes matures et responsables, toute faute de français, description attive ou manque de rigueur peut donc vous être préjudiciable.</p>
        <p>Les informations seront passées en revue par un modérateur puis utilisées pour créer votre post de candidature.</p>
        </div>';

        // How did you find us ?
        echo '<div id="howDidYouFindUs">';
        echo '<label for="howDidYouFindUs">Comment avez vous découvert le consortium horizon ?</label>';
        echo '<textarea id="howDidYouFindUsInput" name="howDidYouFindUs" class="required"></textarea>';
        echo '<div id="howDidYouFindUsKO" class="registerNotification danger" style="display: none;">Plus de précisions peut-être ? Le média par lequel vous nous avez connu était il bien écrit/réalisé ? Connaissez vous des joueurs du Consortium ? Comment vous ont ils présenté la guilde ?!</div>';
        echo '<div id="howDidYouFindUsOK" class="registerNotification success" style="display: none;">On apprécie toutes ces informations !</div>';
        echo '</div>';

        // Game list
        echo '<label for="gamelist">Pour quel jeu postulez vous ?</label>';
        echo '<select id="gamelist" name="gamelist">
          <option value="Arma 3">Arma 3</option>
          <option value="Albion Online">Albion Online</option>
          <option value="Planetside 2">Planetside 2</option>
          <option value="Star Citizen">Star Citizen</option>
          <option value="Diplomatie">Diplomatie</option>
          <option value="Autre">Autre</option>
        </select>';

        // Section autre
        echo '<div id="otherRegistrationSection" class="registrationSection" style="display: none;">';
        echo '<label for="otherGame">Merci de préciser</label>';
        echo '<input class="otherCustomField" type="text" name="otherGame">';
        echo '</div>';

        // Section planetside ($wag)
        echo '<div id="planetsideRegistrationSection" class="registrationSection" style="display: none;">';
        echo '<div class="planetside sectionPic"></div>';
        // Ask the in-game pseudo
        echo '<label for="planetsideUsername">Nom de l\'avatar en jeu</label>';
        echo '<input class="planetsideCustomField" type="text" name="planetsideUsername">';
        // Ask the favourite class
        echo '<label for="planetsideClass">Votre (ou vos) classe(s) de prédilection</label>';
        echo '<input type="checkbox" name="planetsideClassInf" value="Infiltrateur"> Infiltrateur';
        echo '<input type="checkbox" name="planetsideClassLA" value="Assaut léger"> Assaut léger';
        echo '<input type="checkbox" name="planetsideClassMedic" value="Médic"> Médic';
        echo '<input type="checkbox" name="planetsideClassIng" value="Ingénieur"> Ingénieur';
        echo '<input type="checkbox" name="planetsideClassHA" value="Assaut lourd"> Assaut lourd';
        echo '<input type="checkbox" name="planetsideClassXAM" value="Max"> Max';
        echo '</div>';

        // Other games list
        echo '<label for="gamelist">A quels jeux jouez vous également ? <div id="addGame">+</div> <div id="removeGame">-</div></label>';
        echo '<div id="moreGames"></div>';
        echo '<input type="text" name="moreGamesCount" id="moreGamesCount" value="0" style="display: none;">';

        // More about you
        echo '<div id="moreAboutYouSection">';
        echo '<label for="moreAboutYou">Dites nous en plus sur vous (c\'est important !)</label>';
        echo '<textarea id="moreAboutYouInput" name="moreAboutYou" class="required"></textarea>';
        echo '<div id="descriptionKO" class="registerNotification danger" style="display: none;">Cette description est bien succinte ! N\'y a t\'il rien d\'interressant à ajouter ?</div>';
        echo '<div id="descriptionOK" class="registerNotification success" style="display: none;">Belle présentation ! Merci d\'avoir pris le temps !</div>';
        echo '</div>';

        // close form
        echo '</div>';
    }

    public function entryController_RegisterValidation_handler($sender) {
        $dateString =$sender->Form->_FormValues['DateOfBirth_Year'].'-'.$sender->Form->_FormValues['DateOfBirth_Month'].'-'.$sender->Form->_FormValues['DateOfBirth_Day'];
        $diff = abs(strtotime(date('Y-m-d')) - strtotime($dateString));
        $years = floor($diff / (365*60*60*24));
    }

    public function userModel_afterRegister_handler($sender, $Args) {
        var_dump($sender->EventArguments['User']);
        // Does the user wanna joind the guild ?
        if ($sender->EventArguments['User']['iWannaJoin']!=NULL) {

            // get generic info
            $game = $sender->EventArguments['User']['gamelist'];
            if ($game=="Autre") {
                $game = $sender->EventArguments['User']['otherGame'];
            }
            $howDidYouFindUs = $sender->EventArguments['User']['howDidYouFindUs'];
            $moreAboutYou = $sender->EventArguments['User']['moreAboutYou'];
            $moreGamesCount = intval($sender->EventArguments['User']['moreGamesCount']);

            //PS2 Data
            if ($game=="Planetside 2") {
                $ps2Username = '  ([u]Pseudo en jeu :[/u] '. $sender->EventArguments['User']['planetsideUsername'] .')';
                $ps2Inf = $sender->EventArguments['User']['planetsideClassInf'];
                $ps2LA = $sender->EventArguments['User']['planetsideClassLA'];
                $ps2Medic = $sender->EventArguments['User']['planetsideClassMedic'];
                $ps2Ing = $sender->EventArguments['User']['planetsideClassIng'];
                $ps2HA = $sender->EventArguments['User']['planetsideClassHA'];
                $ps2Max = $sender->EventArguments['User']['planetsideClassXAM'];
            }

            // Check if there's some other game data
            $otherGameInfo ="";
            if ($moreGamesCount > 0) {

                $otherGameNames = "";

                for ($i=0; $i < $moreGamesCount; $i++) {
                    $secondaryGame = $sender->EventArguments['User']['secondaryGame'.$i];
                    $otherGameNames = $otherGameNames . $secondaryGame . "; ";
                }
                $otherGameInfo = '

                [b]A quels autres jeux jouez-vous également ?[/b]

                '. $otherGameNames;

            }

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
            $Discussion['CategoryID'] = '1';
            // Discussion Format (BBcode)
            $Discussion['Format'] = 'BBCode';
            // Discussion title
            $Discussion['Name'] = '[' . $game . '] ' . $name . ' [En attente de validation]';

            // Check if there's some section specific data
            $gameInfo="";
            if ($game =="Planetside 2") {
                $gameInfo = '

                [b]Mes classes de prédilection : [/b]

                '. ($ps2Inf ? "Infiltré; " : '') . ($ps2LA ? "Assaut léger; " : '') . ($ps2Medic ? "Médic; " : '') . ($ps2Ing ? "Ingénieur; " : '') . ($ps2HA ? "Assaut lourd;  " : '') . ($ps2Max ? "Max; " : '') ;

            }

            // Discussion content
            $Discussion['Body'] = '[b]Pour quel jeu en particulier postulez-vous dans la Guilde ?[/b]

            '
            . $game . $ps2Username

            . $gameInfo

            . $otherGameInfo .

            '

            [b]Comment avez-eu connaissance du Consortium Horizon ?[/b]

            '
            . $howDidYouFindUs .
            '

            [b]Dites-en un peu plus sur vous :[/b]

            '
            . $moreAboutYou .
            '


            ------------------------------------------------------------------------

            [color=#0012ff][b]Rappel de Mr Robot[/b] -> tu peux te connecter à mumble en utilisant les informations suivantes :
            - Adresse : mumble.consortium-horizon.com
            - Port : 64738
            - Username/mdp : les tiens sur le forum, une fois ta candidature validée[/color]

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

    /**
    * Load assets.
    */
    public function Base_Render_Before($sender, $args)
    {
        // Load the assets if not admin
        if ($sender->MasterView != 'admin') {
            $sender->AddCssFile('postonregister.css', 'plugins/PostOnRegister');
            $sender->AddJsFile('postonregister.js', 'plugins/PostOnRegister');
        }
    }
}
