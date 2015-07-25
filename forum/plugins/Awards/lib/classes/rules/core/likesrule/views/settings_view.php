<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('LikesRule', $this->Data['RulesSettings']);

// Set fields for "Received Likes"
$ReceivedLikesSettings = GetValue('ReceivedLikes', $RuleSettings);
$this->Form->SetFormValue('ReceivedLikes_Enabled', (int)GetValue('Enabled', $ReceivedLikesSettings));
$this->Form->SetFormValue('ReceivedLikes_Amount', GetValue('Amount', $ReceivedLikesSettings));

// Set fields for "Likes to Posts Ratio"
$LikesToPostsRatioSettings = GetValue('LikesToPostsRatio', $RuleSettings);
$this->Form->SetFormValue('LikesToPostsRatio_Enabled', (int)GetValue('Enabled', $LikesToPostsRatioSettings));
$this->Form->SetFormValue('LikesToPostsRatio_Amount', GetValue('Amount', $LikesToPostsRatioSettings));

$MissingRuleRequirements = GetValue('LikesRule', GetValue('MissingRuleRequirements', $this->Data), array());

//var_dump($MissingRuleRequirements);die();
$ExtraCssClass = empty($MissingRuleRequirements) ? '' : 'Disabled';
?>
<div id="LikesRule" class="Rule clearfix <?php echo $ExtraCssClass; ?>">
	<div class="Fields">
		<ul>
			<li>
				<div class="ReceivedLikes">
				<?php
					LikesRule::RenderRuleField($this->Form->CheckBox('ReceivedLikes_Enabled', T('User received at least X Likes')));
					LikesRule::RenderRuleField($this->Form->TextBox('ReceivedLikes_Amount',
																													 array('class' => 'InputBox Numeric')));
				?>
				</div>
			</li>
			<li>
				<div class="LikesToPostsRatio">
				<?php
					LikesRule::RenderRuleField($this->Form->CheckBox('LikesToPostsRatio_Enabled',
																													 T('User\'s Likes to Messages ratio is at least X')));
					LikesRule::RenderRuleField($this->Form->TextBox('LikesToPostsRatio_Amount',
																													 array('class' => 'InputBox Numeric')));
				?>
				</div>
			</li>
		</ul>
	</div>
	<?php BaseAwardRule::RenderMissingRequirements($MissingRuleRequirements); ?>
</div>
