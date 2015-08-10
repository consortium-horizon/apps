<?php if(!defined('APPLICATION')) exit();
/* 	Copyright 2014 Zachary Doll
 * 	This program is free software: you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation, either version 3 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 *
 * 	You should have received a copy of the GNU General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
$PluginInfo['YagaFeaturedBadges'] = array(
    'Name' => 'Yaga Featured Badges',
    'Description' => 'Adds user selectable featured badges shown on their profile page and author meta.',
    'Version' => '0.1',
    'RequiredApplications' => array('Yaga' => '1.0'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'MobileFriendly' => TRUE,
    'HasLocale' => TRUE,
    'RegisterPermissions' => FALSE,
    'Author' => 'Zachary Doll',
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://www.daklutz.com',
    'License' => 'GPLv3'
);

class YagaFeaturedBadges extends Gdn_Plugin {

  /**
   * Adds a link to the featured badges settings page on the profile
   *
   * @param object $Sender
   */
  public function ProfileController_AfterAddSideMenu_Handler($Sender) {
    $SideMenu = $Sender->EventArguments['SideMenu'];
    $Session = Gdn::Session();
    $ViewingUserID = $Session->UserID;
    if($Sender->User->UserID == $ViewingUserID) {
      $SideMenu->AddLink('Options', T('Featured Badges'), '/profile/featuredbadges', FALSE, array('class' => 'Popup'));
    }
    else {
      $SideMenu->AddLink('Options', T('Featured Badges'), '/profile/featuredbadges/' . $Sender->User->UserID . '/' . Gdn_Format::Url($Sender->User->Name), 'Garden.Users.Edit', array('class' => 'Popup'));
    }
  }

  /**
   * Renders the profile settings page on the profile
   *
   * @param object $Sender
   * @param array $Args
   */
  public function ProfileController_FeaturedBadges_Create($Sender) {
    $Sender->Permission('Garden.SignIn.Allow');
    $Sender->AddJsFile('jquery-ui-1.10.0.custom.min.js');
    $this->_AddResources($Sender);

    $UserID = $this->_GetUserIDFromSender($Sender);

    // Get the list of badges earned by the user
    $AvailableBadges = Yaga::BadgeAwardModel()->GetByUser($UserID);
    $Sender->SetData('Badges', $AvailableBadges);

    // Get the current featured badges
    $SelectedBadges = $this->_GetFeatureBadges($UserID);
    $CurrentBadges = array();
    $CurrentBadges['Badge1'] = $SelectedBadges[0]['BadgeID'];
    $CurrentBadges['Badge2'] = $SelectedBadges[1]['BadgeID'];
    $CurrentBadges['Badge3'] = $SelectedBadges[2]['BadgeID'];
    $Sender->Form->SetData($CurrentBadges);

    // Add the data needed by the view and form
    $Sender->SetData('Plugin-YagaFeaturedBadges-ForceEditing', ($UserID == Gdn::Session()->UserID) ? FALSE : $Sender->User->Name);
    $Sender->Form->AddHidden('UserID', $UserID);

    // If the form has been posted
    if($Sender->Form->AuthenticatedPostBack() !== FALSE) {
      $FormValues = $Sender->Form->FormValues();

      // Verify the user has earned the posted badges
      $AvailableBadgeIDs = ConsolidateArrayValuesByKey($AvailableBadges, 'BadgeID');
      $AvailableBadgeIDs[] = FALSE;

      if(in_array($FormValues['Badge1'], $AvailableBadgeIDs) && in_array($FormValues['Badge2'], $AvailableBadgeIDs) && in_array($FormValues['Badge3'], $AvailableBadgeIDs)) {
        $this->SetUserMeta($UserID, 'BadgeID1', $FormValues['Badge1']);
        $this->SetUserMeta($UserID, 'BadgeID2', $FormValues['Badge2']);
        $this->SetUserMeta($UserID, 'BadgeID3', $FormValues['Badge3']);
        $Sender->StatusMessage = T('Your changes have been saved.');
      }
      else {
        $Sender->Form->AddError('That badge is not available to be featured.');
      }
    }

    $Sender->SetData('FeaturedBadges', $this->_GetFeatureBadges($UserID));

    $Sender->Render($this->GetView('profile-settings.php'));
  }

  /**
   * Parse the request args to find the user id of the current profile
   * @param ProfileController $Sender
   * @return int The user ID
   */
  private function _GetUserIDFromSender($Sender) {
    $Args = $Sender->RequestArgs;
    $Session = Gdn::Session();
    $UserReference = GetValue(0, $Args, 0);
    $Username = GetValue(1, $Args, ' ');

    // default to the signed in user
    if($UserReference == 0 && $Username == ' ') {
      $UserReference = $Session->UserID;
      $Username = $Session->User->Name;
    }
    $Sender->GetUserInfo($UserReference, $Username);

    $ViewingUserID = $Session->UserID;
    $EditingUserID = $Sender->User->UserID;
    if($EditingUserID != $ViewingUserID) {
      $Sender->Permission('Garden.Users.Edit');
      $UserID = $Sender->User->UserID;
    }
    else {
      $UserID = $ViewingUserID;
    }

    return $UserID;
  }

  /**
   * Render a list of featured badges on author info in the discussion controller
   * @param DiscussionController $Sender
   */
  public function DiscussionController_AuthorInfo_Handler($Sender) {
    $UserID = $Sender->EventArguments['Author']->UserID;

    $Badges = $this->_GetFeatureBadges($UserID);

    foreach($Badges as $Badge) {
      if($Badge === FALSE) {
        continue;
      }
      echo Anchor(
              Img(
                      $Badge['Photo'], array('class' => 'ProfilePhoto FeaturedBadge')
              ), 'badges/detail/' . $Badge['BadgeID'] . '/' . Gdn_Format::Url($Badge['Name']), array('title' => $Badge['Name'])
      );
    }
  }

  /**
   * Display the featured badges on the profile controller
   * @param ProfileController $Sender
   */
  public function ProfileController_AfterUserInfo_Handler($Sender) {
    $UserID = $Sender->User->UserID;

    $Badges = $this->_GetFeatureBadges($UserID);

    $BadgeString = '';
    foreach($Badges as $Badge) {
      if($Badge !== FALSE) {
        $BadgeString .= Wrap(
                Anchor(
                        Img(
                                $Badge['Photo'], array('class' => 'FeaturedBadge')
                        ), 'badges/detail/' . $Badge['BadgeID'] . '/' . Gdn_Format::Url($Badge['Name']), array('title' => $Badge['Name'])
                ), 'li');
      }
    }

    if($BadgeString != '') {
      echo Wrap(
              Wrap(T('Featured Badges'), 'h2') .
              Wrap($BadgeString, 'ul', array('class' => 'Yaga FeaturedBadges')), 'div', array('class' => 'FeaturedBadgesWrap'));
    }
  }

  /**
   * Add the styles to the profile controller.
   * @param ProfileController $Sender
   */
  public function ProfileController_Render_Before($Sender) {
    $this->_AddResources($Sender, FALSE);
  }

  /**
   * Add the styles to the discussion controller
   * @param DiscussionController $Sender
   */
  public function DiscussionController_Render_Before($Sender) {
    $this->_AddResources($Sender, FALSE);
  }

  /**
   * Retrieve the featured badges for the given user ID
   * @param int $UserID
   * @return array An array containing the BadgeIDs of the featured badges
   */
  private function _GetFeatureBadges($UserID) {
    // Get the current list of featured badges
    $BadgeModel = Yaga::BadgeModel();
    $BadgeIDs = array();
    $BadgeIDs[] = $this->GetUserMeta($UserID, 'BadgeID1', array(), TRUE);
    $BadgeIDs[] = $this->GetUserMeta($UserID, 'BadgeID2', array(), TRUE);
    $BadgeIDs[] = $this->GetUserMeta($UserID, 'BadgeID3', array(), TRUE);
    $Badges = array();
    $Badges[] = $BadgeModel->GetID($BadgeIDs[0], DATASET_TYPE_ARRAY);
    $Badges[] = $BadgeModel->GetID($BadgeIDs[1], DATASET_TYPE_ARRAY);
    $Badges[] = $BadgeModel->GetID($BadgeIDs[2], DATASET_TYPE_ARRAY);
    return $Badges;
  }

  /**
   * Add the resources to the page
   * @param Gdn_Controller $Sender
   */
  private function _AddResources($Sender, $IncludeJS = TRUE) {
    if($IncludeJS) {
      $Sender->AddJsFile($this->GetResource('js/yagafeaturedbadges.js', FALSE, FALSE));
    }
    $Sender->AddCssFile($this->GetResource('design/yagafeaturedbadges.css', FALSE, FALSE));
  }
}
