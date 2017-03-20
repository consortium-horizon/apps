<?php if (!defined('APPLICATION')) die(); ?>

<h2><?php echo $this->User->Name ?></h2>

<?php echo $this->Form->Open() ?>
<?php echo $this->Form->Errors(); ?>
<?php echo $this->Form->CheckBoxGridGroups($this->PermissionData, 'UserPermissions'); ?>
<?php echo $this->Form->Button('Save'); ?>
<?php echo $this->Form->Button('Reset'); ?>
<?php echo $this->Form->Close(); ?>