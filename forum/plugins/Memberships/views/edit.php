<?php if (!defined('APPLICATION')) exit(); ?>
<h2><?php echo T("Assign user to group"); ?></h2>

<?php
   	$Session = Gdn::Session();
	$EditUser = $Session->CheckPermission('Garden.Users.Edit');
	echo $this->Form->Open(array('action' => Url('plugin/memberships/edit')));
	echo $this->Form->Errors();
   	echo '<p>';
   	echo $this->Form->Label($this->UserInQuestion['Name'], 'Plugin.Memberships.GroupID');
   	echo $this->Form->DropDown('Plugin.Memberships.GroupID', $this->Groups, array('value' => $this->OldMembership['OldGroupID']));
   	echo $this->Form->Hidden('Plugin.Memberships.UserID', array('value' => $this->UserInQuestion['UserID']));
   	echo '</p>';
	echo '<p>';
   	echo $this->Form->Button(T('Assign User to Group'));
   	echo '</p>';
	echo $this->Form->Close();
  ?>