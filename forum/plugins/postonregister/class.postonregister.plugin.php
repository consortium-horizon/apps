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
    $date = Gdn_Format::ToDateTime();
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
    $Discussion['CategoryID'] = '1';
    // Discussion Format (BBcode)
    $Discussion['Format'] = 'BBCode';
    // Discussion title
    $Discussion['Name'] = '[' . (string) $game . '] <span class="username">' . (string) $name . '</span> [En attente de validation]';
    // Discussion content
    $Discussion['Body'] = '[b]Pour quel jeu en particulier postulez-vous dans la Guilde ?[/b]<br>'
               . $game .
               '<br>
               [b]A quels autres jeux jouez-vous également ?[/b]<br>'
               . $games .
               '<br>
               [b]Comment avez-eu connaissance du Consortium Horizon ?[/b]<br>'
               . $info .
               '<br>
               [b]Dites-en un peu plus sur vous :[/b]<br>'
               . $userInfo .
               '<br>
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
