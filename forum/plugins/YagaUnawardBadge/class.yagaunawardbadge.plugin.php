<?php if (!defined('APPLICATION')) exit();

$PluginInfo['YagaUnawardBadge'] = array(
    'Name' => 'Yaga Unaward Badge',
    'Description' => 'Adds the option for users that can give out badges to take them away.',
    'Version' => '0.2',
    'RequiredApplications' => array('Yaga' => '1.0'),
    'MobileFriendly' => true,
    'Author' => 'Bleistivt',
    'AuthorUrl' => 'http://bleistivt.net',
    'License' => 'GNU GPL2'
);

class YagaUnawardBadgePlugin extends Gdn_Plugin {

    public function ProfileController_UnawardBadges_Create($Sender, $UserID, $UserName = '') {
        $Sender->Permission('Yaga.Badges.Add');

        if (!$User = Gdn::Usermodel()->GetID($UserID)) {
            throw NotFoundException('User');
        }

        $Sender->GetUserInfo($UserID, $UserName);
        $Sender->SetData('Badges', Yaga::BadgeAwardModel()->GetByUser($UserID));
        $Sender->SetData('UserID', $UserID);
        $Sender->SetData('Title', T('Unaward Badges'));

        $Sender->Render('unaward', '', 'plugins/YagaUnawardBadge');
    }

    public function BadgeController_Unaward_Create($Sender, $BadgeAwardID) {
        $Sender->Permission('Yaga.Badges.Add');

        if (!$BadgeAward = Yaga::BadgeAwardModel()->GetID($BadgeAwardID)) {
            throw NotFoundException('BadgeAward');
        }
        $Badge = Yaga::BadgeModel()->GetID($BadgeAward->BadgeID);

        $Sender->SetData('Badgename', $Badge->Name);
        $Sender->SetData('Username', Gdn::Usermodel()->GetID($BadgeAward->UserID)->Name);

        if ($Sender->Form->AuthenticatedPostBack()) {
            Gdn::SQL()->Delete('BadgeAward', array('BadgeAwardID' => $BadgeAwardID), 1);
            Gdn::SQL()
                ->Update('User')
                ->Set('CountBadges', 'CountBadges - 1', false)
                ->Where('UserID', $BadgeAward->UserID)
                ->Put();
            if ($Badge) {
                if (method_exists('Yaga', 'GivePoints')) {
                    Yaga::GivePoints($BadgeAward->UserID, -1 * $Badge->AwardValue, 'Badge');
                } else {
                    UserModel::GivePoints($BadgeAward->UserID, -1 * $Badge->AwardValue, 'Badge');
                }
            }
            $Sender->InformMessage(T('The badge was successfully removed from this user.'));
            $Sender->JsonTarget('#BadgeAward-'.$BadgeAwardID, '', 'Remove');
        }
        $Sender->SetData('Title', T('Unaward Badge'));
        $Sender->Render('delete', '', 'plugins/YagaUnawardBadge');
    }

    public function ProfileController_BeforeProfileOptions_Handler($Sender) {
        if(!C('Yaga.Badges.Enabled') || !CheckPermission('Yaga.Badges.Add')) return;

        $Sender->EventArguments['ProfileOptions'][] = array(
            'Text' => Sprite('SpAdminActivities SpNoBadge') . ' ' . T('Unaward Badges'),
            'Url' => '/profile/unawardbadges/'.$Sender->User->UserID.'/'.Gdn_Format::Url($Sender->User->Name)
        );
    }

}
