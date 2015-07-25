<?php if(!defined('APPLICATION')) exit();


/* Rules Settings have to be "moved" manually to Form Values because they are
 * decoded from their JSON format and, therefore, returned as properties of an
 * object.
 */
$RuleSettings = GetValue('SampleRule', $this->Data['RulesSettings']);

// Set fields for "Received Sample"
$SampleSettings = GetValue('SampleSettings', $RuleSettings);

// Put the value of the various settings into the Form. The settings are loaded
// by the Rules Manager as a hierarchical array, which has to be "translated"
// into the original field names
$this->Form->SetFormValue('SampleSettings_Enabled', (int)GetValue('Enabled', $SampleSettings));
$this->Form->SetFormValue('SampleSettings_Amount', GetValue('Amount', $SampleSettings));

// Retrieve the missing requirements, if any. They will be displayed to the Admin
$MissingRuleRequirements = GetValue('SampleRule', GetValue('MissingRuleRequirements', $this->Data), array());

$ExtraCssClass = empty($MissingRuleRequirements) ? '' : 'Disabled';
?>
<div id="SampleRule" class="Rule clearfix <?php echo $ExtraCssClass; ?>">
	<div class="Fields">
		<ul>
			<li>
				<div class="Sample">
				<?php
					// GUIDE Output the Rule fields using SampleRule::RenderRuleField().

					/* IMPORTANT: You MUST use such method, as it will transform the field
					 * names so that, upon saving, they will be returned as a hierarchy,
					 * rather than a flat list of fields.
					 * The base Rule class will handle this automatically, giving you the
					 * fields for each Rule and saving you the effort of figuring out
					 * which fields belong to your Rule.
					 *
					 * HOW TO NAME THE FIELDS
					 * Your Rule's fields will be grouped in an array accessible as
					 * FormValues[SampleRule][]. To group them further, simply name them as
					 * "Group_FieldName".
					 *
					 * In this example, there are two fields called SampleSettings_Enabled
					 * and SampleSettings_Amount. When the Rule will receive the values to
					 * validate, the fields will be stored in an array, as follows:
					 * - FormValues[SampleRule][SampleSettings][Enabled]
					 * - FormValues[SampleRule][SampleSettings][Amount]
					 */
					SampleRule::RenderRuleField($this->Form->CheckBox('SampleSettings_Enabled', T('Sample - User has at least X something')));
					SampleRule::RenderRuleField($this->Form->TextBox('SampleSettings_Amount',
																													 array('class' => 'InputBox Numeric')));
				?>
				</div>
			</li>
		</ul>
	</div>
	<?php
		// Display missing requirements, if any. This will let Admin know if the Rule
		// cannot be used
		BaseAwardRule::RenderMissingRequirements($MissingRuleRequirements);
	?>
</div>
