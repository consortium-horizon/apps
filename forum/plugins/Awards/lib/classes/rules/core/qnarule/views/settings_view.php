<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('QnARule', $this->Data['RulesSettings']);

$ReceivedAcceptedAnswersSettings = GetValue('ReceivedAcceptedAnswers', $RuleSettings);
$this->Form->SetFormValue('ReceivedAcceptedAnswers_Enabled', (int)GetValue('Enabled', $ReceivedAcceptedAnswersSettings));
$this->Form->SetFormValue('ReceivedAcceptedAnswers_Amount', GetValue('Amount', $ReceivedAcceptedAnswersSettings));

$MissingRuleRequirements = GetValue('QnARule', GetValue('MissingRuleRequirements', $this->Data), array());

//var_dump($MissingRuleRequirements);die();
$ExtraCssClass = empty($MissingRuleRequirements) ? '' : 'Disabled';
?>
<div id="ThanksRule" class="Rule clearfix <?php echo $ExtraCssClass; ?>">
	<div class="Fields">
		<ul>
			<li>
				<div class="ReceivedAcceptedAnswers">
				<?php
					//echo Wrap(T('ReceivedAcceptedAnswers'), 'h4');
					QnARule::RenderRuleField($this->Form->CheckBox('ReceivedAcceptedAnswers_Enabled', T('User received at least X "Accepted Answer"')));
					QnARule::RenderRuleField($this->Form->TextBox('ReceivedAcceptedAnswers_Amount',
																													 array('class' => 'InputBox Numeric')));
				?>
				</div>
			</li>
		</ul>
	</div>
	<?php BaseAwardRule::RenderMissingRequirements($MissingRuleRequirements); ?>
</div>
