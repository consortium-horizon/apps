<?php
if(!defined('APPLICATION')) die();

?>
<h1><?php echo T('Group2Role wizard'); ?></h1>
<?php 
  if($this->ErrorMessage){
    echo '<div class="Messages Errors TestAddonErrors">';
    echo '<ul>';
    echo '<li>' . $this->ErrorMessage . '</li>';
    echo '</ul>';
    echo '</div>';
  }
  if($this->Message){
    echo '<div class="Messages">';
    echo '<ul>';
    echo '<li>' . $this->Message . '</li>';
    echo '</ul>';
    echo '</div>';
  }
?>
<div>
  <?php
  echo $this->Form->Open(array('Action' => Url('/settings/group2roleapply'), 'id' => 'Group_2_Role_Form'));
  echo $this->Form->Errors();
  ?>
  <ul>
    <li>
      <?php
        echo $this->Form->Label(T('Select a group'), 'Plugin.Role2Group.GroupID');
        echo $this->Form->DropDown('Plugin.Role2Group.GroupID', $this->Groups, array('IncludeNull' => FALSE));
      ?>
    </li>
    <li>
      <?php
        echo $this->Form->Label(T('Select a role'), 'Plugin.Role2Group.RoleID');
        echo $this->Form->DropDown('Plugin.Role2Group.RoleID', $this->Roles, array('IncludeNull' => FALSE));
      ?>
    </li>
    <li class="Buttons">
       <?php echo $this->Form->Button('Add selected role to every user in the selected group'); ?>
    </li>
  </ul>
  <?php echo $this->Form->Close(); ?>
 </div>
 <h1>More features and plugins</h1>
 <div style="padding-left:20px;">
<ul>
  <li>Do you find it useful? <b>Make a donation to the author</b> and <b>Leave a feedback on <a href="http://vanillaforums.org/discussions">vanillaforums.org</a></b></li>
  <li>Help development (new features), <b>make now a donation</b> (Button below)</li>
  <li>Do you have a suggestion o need help? <b>Write on <a href="http://vanillaforums.org/discussions">vanillaforums.org</a></b></li>
  <li>Do you want a custom plugin? <b>Donate or request custom plugin development (ask doesn't cost anything!)</b></li>
  <li>Yuo can alway contact me at <b>lifeisfoo@gmail.com</b></li>
  <li>You can find a complete list of my plugins <a href="http://vanillaforums.org/profile/addons/43188/lifeisfoo">at this page</a></li>
  <li><a href="https://github.com/lifeisfoo">My GitHub repository</a> contains latest changes and works</li>
</ul> Donate now!
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="7DV7D8VNG33B2">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
 </div>