<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('ThanksRule', $this->Data['RulesSettings']);

$ReceivedThanksSettings = GetValue('ReceivedThanks', $RuleSettings);
$this->Form->SetFormValue('ReceivedThanks_Enabled', (int)GetValue('Enabled', $ReceivedThanksSettings));
$this->Form->SetFormValue('ReceivedThanks_Amount', GetValue('Amount', $ReceivedThanksSettings));

$MissingRuleRequirements = GetValue('ThanksRule', GetValue('MissingRuleRequirements', $this->Data), array());

//var_dump($MissingRuleRequirements);die();
$ExtraCssClass = empty($MissingRuleRequirements) ? '' : 'Disabled';
?>
<div id="ThanksRule" class="Rule clearfix <?php echo $ExtraCssClass; ?>">
	<div class="Fields">
		<ul>
			<li>
				<div class="ReceivedThanks">
				<?php
					//echo Wrap(T('ReceivedThanks'), 'h4');
					ThanksRule::RenderRuleField($this->Form->CheckBox('ReceivedThanks_Enabled', T('User received at least X Thanks')));
					ThanksRule::RenderRuleField($this->Form->TextBox('ReceivedThanks_Amount',
																													 array('class' => 'InputBox Numeric')));
				?>
				</div>
			</li>
		</ul>
	</div>
	<?php BaseAwardRule::RenderMissingRequirements($MissingRuleRequirements); ?>
</div>
