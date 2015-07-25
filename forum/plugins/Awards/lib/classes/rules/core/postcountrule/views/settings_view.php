<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('PostCountRule', $this->Data['RulesSettings']);

$DiscussionsSettings = GetValue('Discussions', $RuleSettings);
$this->Form->SetFormValue('Discussions_Enabled', (int)GetValue('Enabled', $DiscussionsSettings));
$this->Form->SetFormValue('Discussions_Amount', GetValue('Amount', $DiscussionsSettings));

$CommentsSettings = GetValue('Comments', $RuleSettings);
$this->Form->SetFormValue('Comments_Enabled', (int)GetValue('Enabled', $CommentsSettings));
$this->Form->SetFormValue('Comments_Amount', GetValue('Amount', $CommentsSettings));
?>
<div class="Rule">
	<ul>
		<li>
			<div class="Discussions">
			<?php
				//echo Wrap(T('Discussions'), 'h4');
				PostCountRule::RenderRuleField($this->Form->CheckBox('Discussions_Enabled', T('User started at least X Discussions')));
				PostCountRule::RenderRuleField($this->Form->TextBox('Discussions_Amount',
																														array('class' => 'InputBox Numeric')));
			?>
			</div>
		</li>
		<li>
			<div class="Comments">
			<?php
				//echo Wrap(T('Comments'), 'h4');
				PostCountRule::RenderRuleField($this->Form->CheckBox('Comments_Enabled', T('User posted at least X Comments')));
				PostCountRule::RenderRuleField($this->Form->TextBox('Comments_Amount',
																														array('class' => 'InputBox Numeric')));
			?>
			</div>
		</li>
	</ul>
</div>
