<?php if(!defined('APPLICATION')) exit();


$RoleModel = new RoleModel();
$AvailableRoles = $RoleModel->Get();

/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('UserRolesRule', $this->Data['RulesSettings']);

$UserRolesSettings = GetValue('AnyRoles', $RuleSettings);
$this->Form->SetFormValue('AnyRoles_Enabled', (int)GetValue('Enabled', $UserRolesSettings));
$this->Form->SetFormValue('AnyRoles_Roles', GetValue('Roles', $UserRolesSettings));

?>
<div id="UserRolesRule" class="Rule">
	<?php //echo Wrap(T('User Roles'), 'h4'); ?>
	<ul>
		<li>
			<div class="AnyRoles clearfix">
			<?php
				echo '<div class="FieldLabel">';
				UserRolesRule::RenderRuleField($this->Form->CheckBox('AnyRoles_Enabled',
																														 T('User has <strong>any</strong> of these Roles')));
				echo '</div>';

				echo '<div class="FieldValue">';
				UserRolesRule::RenderRuleField($this->Form->CheckBoxList('AnyRoles_Roles',
																																 $AvailableRoles,
																																 null,
																																 array('ValueField' => 'RoleID',
																																			 'TextField' => 'Name',)));
				echo '</div>';
			?>
			</div>
		</li>
		<li>
			<div class="NoRoles clearfix">
			<?php
				echo '<div class="FieldLabel">';
				UserRolesRule::RenderRuleField($this->Form->CheckBox('NoRoles_Enabled',
																														 T('User has <strong>none</strong> of these Roles')));
				echo '</div>';

				echo '<div class="FieldValue">';
				UserRolesRule::RenderRuleField($this->Form->CheckBoxList('NoRoles_Roles',
																																 $AvailableRoles,
																																 null,
																																 array('ValueField' => 'RoleID',
																																			 'TextField' => 'Name',)));
				echo '</div>';
			?>
			</div>
		</li>
	</ul>
</div>
