<?php if (!defined('APPLICATION')) exit();

$PluginInfo['postonregister'] = array(
   'Name' => 'Post on register',
   'Description' => 'Create a new post each time a new user registers',
   'Version' => '0.1',
   'Author' => 'Vladvonvidden',
);

class postonregister extends Gdn_Plugin {

  public function userModel_afterRegister_handler($sender, $Args) {
    // Get user ID from sender
    $userID = $sender->EventArguments['UserID'];
    // Retreive user object
    $user = $sender->GetID($userID);
    // Get UserName
    $name = GetValue('Name', $user, $Default = FALSE, $Remove = FALSE);
    // Get first visit date
    $date = '2015-08-11 16:59:00';
    $thedate = Gdn_Format::ToDateTime();
    ob_start();
    var_dump($thedate);
    $dumpDate = ob_get_clean();
    //GetValue('DateFirstVisit', $user, $Default = FALSE, $Remove = FALSE)
    // Get additionnal info (using profile extender)
    $userMeta = Gdn::UserMetaModel()->GetUserMeta($userID, 'Profile%');
    $game = val('Profile.Pourqueljeuxpostulezvous', $userMeta);
    $games = val('Profile.Aquelsjeujouezvousgalement', $userMeta);
    $info = val('Profile.CommentavezvouseuconnaissanceduConsortiumHorizon', $userMeta);
    $userInfo = val('Profile.Ditesnousenplussurvous', $userMeta);
    // Create new discussionModel
    $DiscussionModel = new DiscussionModel();
    // Feed it ! Feeeeeeeeed it !
    $SQL = Gdn::Database()->SQL();
    // Where you wanna insert the discussion (which category)
    $Discussion['CategoryID'] = '9';
    // Discussion title
    $Discussion['Name'] = 'Candidature de <span class="username">' . (string) $name . '</span> pour: ' . (string) $game . ' [En attente de validation]';
    // Discussion content
    $Discussion['Body'] = '<h4>Pour quel jeu en particulier postulez-vous dans la Guilde ?</h4><br>'
               . $game .
               '<br>
               <h4>A quels autres jeux jouez-vous également ?</h4><br>'
               . $games .
               '<br>
               <h4>Comment avez-eu connaissance du Consortium Horizon ?</h4><br>'
               . $info .
               '<br>
               <h4>Dites-en un peu plus sur vous :</h4><br>'
               . $userInfo .
               '<br>
               <h4>La date que j ai mis en fixe est :</h4><br>'
               . $date .
               '<br>
               <h4>La date que je veux balancer est :</h4><br>'
               . $thedate .
               '<br>
               <h4>Et le dump me donne :</h4><br>'
               . $dumpDate .
               '<br>
               En attente de validation par un modérateur';
    // Date of creation
    $Discussion['DateInserted'] = $thedate; // '2015-08-09 00:00:00' is working SO WTF
    // The author
    $Discussion['InsertUserID'] = $userID ;
    // Insert in the right category
    $DiscussionID = $SQL->Insert('Discussion', $Discussion);
    // If everything is ok, refresh discussion count
    if ($DiscussionID) { $DiscussionModel->UpdateDiscussionCount($Discussion['CategoryID']) ;}
  }
}
