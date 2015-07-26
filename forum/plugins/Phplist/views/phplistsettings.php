<?php if (!defined('APPLICATION')) die(); ?>

<?php $this->ConfigurationModule->Render(); ?>

<?php
echo $this->Form->Open(array('action' => Url('plugin/phplist/bulkSubscribe')));
echo $this->Form->Errors();
echo "<ul>";
echo "<li>";
echo '<label for="bulk_description">' . T("Bulk subscribe") . '</label>';
echo "<span>";
echo T('This will subscribe all users. If you have many users this will take some time.') . ' ';
echo T('Existing users will be updated (actually nothing can be modified since only email field is sent).').' ';
echo "</span>";
echo "</li>";
echo "<li>";
echo $this->Form->CheckBox('OptIn', T('Send opt-in email?'), array('checked' => 'checked'));
echo "</li>";
echo "</ul>";
echo $this->Form->Button(T('Bulk subscribe'));
echo "</p>";
echo $this->Form->Close();
?>
