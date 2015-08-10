<?php if(!defined('APPLICATION')) exit(); ?>
<h2><?php echo T('Featured Badges'); ?></h2>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();

if(isset($this->Data['Plugin-YagaFeaturedBadges-ForceEditing']) && $this->Data['Plugin-YagaFeaturedBadges-ForceEditing'] != FALSE) {
  ?>
  <div class="Warning"><?php echo sprintf(T("You are editing %s's featured badges."), $this->Data['Plugin-YagaFeaturedBadges-ForceEditing']); ?></div>
  <?php
}
?>
<div id="FeaturedBadgeFallback">
  <div class="Info">
    <?php
    echo T('FeaturedBadges.Noscript.Instructions');
    ?>
  </div>
  <ul>
    <li>
      <?php
      echo $this->Form->Label('Badge 1', 'Badge1');
      echo $this->Form->TextBox('Badge1');
      ?>
    </li>
    <li>
      <?php
      echo $this->Form->Label('Badge 2', 'Badge2');
      echo $this->Form->TextBox('Badge2');
      ?>
    </li>
    <li>
      <?php
      echo $this->Form->Label('Badge 3', 'Badge3');
      echo $this->Form->TextBox('Badge3');
      ?>
    </li>
  </ul>
</div>
<div id="FeaturedBadgeUI" style="display:none;">
  <div class="Info">
    <?php
    echo T('FeaturedBadges.Normal.Instructions');
    ?>
  </div>
  <ul id="SelectedBadges">
    <?php
    $FeaturedBadges = $this->Data('FeaturedBadges', array());
    foreach($FeaturedBadges as $Badge) {
      if($Badge) {
        echo Wrap(Img($Badge['Photo'], array('title' => $Badge['Name'])), 'li', array('data-id' => $Badge['BadgeID']));
      }
      else {
        echo Wrap(T('No Badge'), 'li', array('title' => T('No Badge'), 'class' => 'EmptyBadge', 'data-id' => FALSE));
      }
    }
    ?>
  </ul>
</div>
<ul id="AvailableBadges">
  <?php
  // Show an empty badge as a way to remove
  echo Wrap(T('No Badge'), 'li', array('title' => T('No Badge'), 'class' => 'EmptyBadge', 'data-id' => FALSE));

  $Badges = $this->Data('Badges', array());
  foreach($Badges as $Badge) {
    echo Wrap(Img($Badge['Photo'], array('title' => $Badge['Name'] . ' - ' . $Badge['BadgeID'])), 'li', array('data-id' => $Badge['BadgeID']));
  }
  ?>
</ul>
<?php
echo $this->Form->Close('Save');
