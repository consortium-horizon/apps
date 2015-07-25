<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('PhotogenicRule', $this->Data['RulesSettings']);

$PhotogenicSettings = GetValue('Photogenic', $RuleSettings);
$this->Form->SetFormValue('Photogenic_Enabled', (int)GetValue('Enabled', $PhotogenicSettings));

$MissingRuleRequirements = GetValue('PhotogenicRule', GetValue('MissingRuleRequirements', $this->Data), array());

//var_dump($MissingRuleRequirements);die();
$ExtraCssClass = empty($MissingRuleRequirements) ? '' : 'Disabled';
?>
<div id="PhotogenicRule" class="Rule clearfix <?php echo $ExtraCssClass; ?>">
	<div class="Fields">
		<ul>
			<li>
				<div class="Photogenic">
				<?php
					//echo Wrap(T('Photogenic'), 'h4');
					PhotogenicRule::RenderRuleField(
						$this->Form->CheckBox('Photogenic_Enabled',
																	T('User uploaded a profile picture'))
					);
				?>
				</div>
			</li>
		</ul>
	</div>
	<?php BaseAwardRule::RenderMissingRequirements($MissingRuleRequirements); ?>
</div>
