<?php if (!defined('APPLICATION')) exit();
/*
$CountFields = 0;
foreach ($Sender->RegistrationFields as $Field) {
	$CountFields++;
	echo '<li>';
		echo $Sender->Form->Hidden('CustomLabel[]', array('value' => $Field));
		echo $Sender->Form->Label($Field, 'CustomValue[]');
		echo $Sender->Form->TextBox('CustomValue[]');
	echo '</li>';
}*/
echo "<li>";
echo $this->Form->Label(T('Role'), 'Plugin.RegistrationRole.RoleID');
echo $this->Form->DropDown('Plugin.RegistrationRole.RoleID', $this->RegistrationRoles, array('IncludeNull' => FALSE));
echo "</li>";