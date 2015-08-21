<?php if (!defined('APPLICATION')) exit(); 

echo Wrap($this->Data('Title'), 'h1');
echo '<div id="Badges" class="Box Badges">';
echo '<div class="DismissMessage InfoMessage">'.T('Click on the badge you want to take away from this user.').'</div>';
echo '<div class="PhotoGrid">';
echo Wrap(sprintf(T('Badges of %s'), UserAnchor(Gdn::UserModel()->GetID($this->Data('UserID')))), 'h4');
foreach($this->Data('Badges') as $Badge) {
    echo Anchor(
        Img(
            $Badge['Photo'],
            array('class' => 'ProfilePhoto')
        ),
        'yaga/badge/unaward/'.$Badge['BadgeAwardID'],
        array('title' => $Badge['Name'], 'class' => 'Popup', 'id' => 'BadgeAward-'.$Badge['BadgeAwardID'])
    );
}
echo '</div>';
echo '</div>';
