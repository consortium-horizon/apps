<?php if (!defined('APPLICATION')) exit(); ?>
<h2><?php echo T("Edit group"); ?></h2>
<?php
   	$Session = Gdn::Session();
	$EditUser = $Session->CheckPermission('Garden.Users.Edit');
	echo $this->Form->Open(array('action' => Url('plugin/groups/edit')));
	echo $this->Form->Errors();
   	echo '<p>';
   	echo $this->Form->TextBox('Plugin.Groups.Name', array('value' => $this->Group->Name));
   	echo $this->Form->Hidden('Plugin.Groups.GroupID', array('value' => $this->Group->GroupID));
   	echo '</p>';
	echo '<p>';
   	echo $this->Form->Button(T('Edit Group'));
   	echo '</p>';
	echo $this->Form->Close();
?>