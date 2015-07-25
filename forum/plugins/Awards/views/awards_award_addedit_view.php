<?php	if(!defined('APPLICATION')) exit();
/*
{licence}
*/

/**
 * Renders the UI for an Award Rule and displays it in the page.
 *
 * @param array AwardRulesSections An array containing the various sections of
 * the page where the Rules will be displayed.
 * @param array AwardRule An array containing details of the Rule.
 * @see AwardRulesManager::GetRuleInfo().
 */
function AddRuleToUI(array &$AwardRulesSections, array &$AwardRule) {
	$RuleGroup = GetValue('Group', $AwardRule);
	$RuleType = GetValue('Type', $AwardRule);

	// Add the rule to the appropriate Group and Section
	$AwardRulesSections[$RuleGroup]->TypeSections[$RuleType]->Rules[] = $AwardRule['Instance'];
	$AwardRulesSections[$RuleGroup]->CountRules += 1;
}

/**
 * Determines current User action (i.e. Clone, Add or Edit) and returns a
 * description for it.
 *
 * @param Gdn_Form Form The form used by the page.
 * @param array Data An associative array of data passed to the page.
 * @return string The message describing current action.
 */
function GetCurrentAction(Gdn_Form $Form, $Data) {
	if(GetValue('Cloning', $Data)) {
		$SourceAwardInfo = Wrap($Form->GetValue('SourceAwardName'),
																 'span',
																 array('class' => 'Info',
																			 'title' => $Form->GetValue('SourceAwardDescription'),));
		return sprintf(T('Clone Award "%s"'), $SourceAwardInfo);
	}

	return $Form->GetValue('AwardID') ? sprintf(T('Edit Award'), $Form->GetValue('AwardName')) : T('Add new Award ');
}

// Retrieve the Sections to organise the Rules
$AwardRulesSections = $this->Data['AwardRulesSections'];

// The following HTML will be displayed when the Awards DataSet doesn't contain Rules
$OutputForNoRules = Wrap(T('No Award Rules installed.'),
												 'div',
													array('class' => 'NoResultsFound',));


// Check if we're configuring a new appender or editing an existing one.
$AwardID = $this->Form->GetValue('AwardID');
$IsNewAward = empty($AwardID) ? true : false;
?>
<div class="Aelia AwardsPlugin AwardEdit">
	<?php
		echo $this->Form->Open(array('enctype' => 'multipart/form-data'));
		echo $this->Form->Errors();

		echo $this->Form->Hidden('AwardID');
		// Field ImageFile contains the path and file name of the Image currently
		// associated with the Award. This field will only be populated when an
		// existing Award is being modified.
		echo $this->Form->Hidden('AwardImageFile');
	?>
	<fieldset id="Award">
		<legend class="Title">
			<?php
				echo Wrap(GetCurrentAction($this->Form, $this->Data), 'h1');
			?>
			<div class="Buttons Top">
				<?php
					echo $this->Form->Button(T('Save'), array('Name' => 'Save',));
					echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
				?>
			</div>
		</legend>
		<div class="Tabs">
			<div id="AwardInfo" class="Tab">
				<h2 class="Label"><?php echo T('Award Details'); ?></h2>
				<h3><?php echo T('Options'); ?></h3>
				<ul id="Fields">
					<li>
						<?php
							// Set IsEnabled to True if we're adding a new Award
							if($IsNewAward) {
								$this->Form->SetValue('AwardIsEnabled', 1);
							}
							echo $this->Form->CheckBox('AwardIsEnabled',
																				 T('<strong>Award is Enabled</strong>. Disabled Awards are ' .
																					 'not assigned automatically, but they can be assigned ' .
																					 'manually. They will be displayed for Users who ' .
																					 'already obtained them.'),
																				 array('value' => 1,));
							echo Wrap(T('<strong>Note</strong>: if you wish to prevent the Award from being ' .
													'processed as soon as you save it, make sure to <strong>uncheck</strong> ' .
													'this box. This will allow you to review the Award at a later time and ' .
													'enable it only when it will be time for it to be processed.'),
												'div',
												array('class' => 'Info',));
						?>
					</li>
					<li>
						<?php
							echo $this->Form->Label(T('Award Name'), 'AwardName');
							echo Wrap(T('Enter a name for the Award. It must be unique amongst the Awards.'),
												'div',
												array('class' => 'Info',));
							echo $this->Form->TextBox('AwardName');
						?>
					</li>
					<li>
						<?php
							echo $this->Form->Label(T('Award Description'), 'AwardDescription');
							$AwardsPageLink = Anchor(T('public Awards page'),
																			 AWARDS_PLUGIN_AWARDS_PAGE_URL,
																			 'Standard',
																			 array('title' => T('View public Awards Page.')));
							echo Wrap(sprintf(T('Enter a description for the Award. It will be displayed in ' .
																	'the %s.'),
																$AwardsPageLink),
												'div',
												array('class' => 'Info',
															));
							echo $this->Form->TextBox('AwardDescription',
																				array('multiline' => true,
																							'rows' => 5,
																							'cols' => 60,));
						?>
					</li>					<li>
						<?php
							echo $this->Form->Label(T('Award Class'), 'AwardClassID');
							echo Wrap(T('The Award Class allows to group the Awards. For example, it could ' .
													'be possible to create Gold Awards, Silver Awards and Bronze Awards ' .
													'to give Users an idea of how difficult is to achieve them.'),
												'div',
												array('class' => 'Info',));

							echo $this->Form->DropDown('AwardClassID',
																				 GetValue('AwardClasses', $this->Data),
																				 array('id' => 'AwardClassID',
																							 'ValueField' => 'AwardClassID',
																							 'TextField' => 'AwardClassName'));
						?>
					</li>
					<li>
						<?php
							echo $this->Form->Label(T('Rank Points'), 'RankPoints');
							echo Wrap(T('Enter the amount of Rank Points to give to Users who receive ' .
													'the Award. These points will be used by <a class="Standard" href="#" title="Rankings ' .
													'Plugin has not been released, yet">Rankings Plugin</a> to assign ' .
													'Users titles, permissions, etc.'),
												'div',
												array('class' => 'Info',));
							echo $this->Form->TextBox('RankPoints');
						?>
					</li>
					<li class="clearfix">
						<?php
							echo $this->Form->Label(T('Award Picture'), 'Picture');
						?>
						<div class="ImageColumn">
						<?php
							echo Wrap(T('Current Image'), 'h5');

							// Overlay that will inform user when an image is a Preview
							$ImagePreviewOverlay = Wrap(T('Preview'),
																	 'div',
																	 array('id' => 'ImageOverlay'));
							// Button to restore original image, discarding the one selected for upload
							$RestoreButton = $this->Form->Button(T('Restore original'),
																									 array('id' => 'RestoreImage',
																												 'class' => 'SmallButton',
																												 'type' => 'button'));

							// Dummy Image to use when none has been selected
							$DummyImageFile = AWARDS_PLUGIN_UI_PICS_PATH . '/dummy-award-img.png';
							$AwardImage = Wrap(Img($this->Form->GetValue('AwardImageFile', $DummyImageFile),
																		 array('id' => 'AwardImagePreview',
																					 'class' => 'AwardImage Large',)),
																 'div');

							echo Wrap($ImagePreviewOverlay .
												$AwardImage .
												$RestoreButton,
												'div',
												array('class' => 'AwardImageWrapper'));
						?>
						</div>
						<div class="ImageSelector">
							<?php
								echo Wrap(T('Upload new Image'), 'h5');
								
								echo Wrap(sprintf(T('Select an image on your computer (2mb max) to be used as ' .
																		'an icon for the Award. Image will be resized to %dx%d (width ' .
																		'x height) pixels.'),
																	PictureManager::DEFAULT_IMAGE_WIDTH,
																	PictureManager::DEFAULT_IMAGE_HEIGHT),
													'p');
								echo Wrap(T('<strong>Important</strong>: if you upload a file with the same '.
														'name of one you uploaded before, the old file will be overwritten.'),
													'p');
								echo $this->Form->Input('Picture', 'file');
							?>
						</div>
						<div class="ImageSelector">
							<?php
								echo Wrap(T('Replace Image with a previously uploaded file'), 'h5');
								echo Wrap(sprintf(T('Select a file that was uploaded in the <strong>Uploads</strong> ' .
																		'folder (%s).'),
																	realpath(PATH_UPLOADS)),
													'p');
								echo $this->Form->Hidden('PreUploadedImageFile');
							?>
							<div id="ServerSideBrowser" class="FileTreeContainer">

							</div>
						</div>
					</li>
				</ul>
			</div>
			<?php
				$AwardRules = GetValue('AwardRules', $this->Data, array());

				if(empty($AwardRules)) {
					echo $OutputForNoRules;
					// If there are no Rules, empty the Rule Sections, to avoid looping
					// through them for nothing (they would not be rendered anyway)
					$AwardRulesSections = array();
				}

				// Load the Configuration UI for each rule and add it to the appropriate
				// section
				foreach($AwardRules as $AwardRule) {
					//var_dump($AwardRule);
					//var_dump($this);die();
					AddRuleToUI($AwardRulesSections, $AwardRule);
				}

				// Render each Rule's Configuration UI
				foreach($AwardRulesSections as $GroupID => $GroupInfo) {
					if($GroupInfo->CountRules <= 0) {
						continue;
					}

					// Render the Rule Group section
					echo '<fieldset id="RuleGroup-' . $GroupID. '" class="RuleGroup Tab">';
					echo Wrap($GroupInfo->Label . '&nbsp;' . T('Rules'),
										'h2',
										array('class' => 'Label')
										);

					// Render each Rule Type Section
					foreach($GroupInfo->TypeSections as $TypeID => $TypeInfo) {
						// Don't render empty sections
						if(empty($TypeInfo->Rules)) {
							continue;
						}

						// Render Rule Type section
						echo '<div class="RuleType">';
						echo Wrap($TypeInfo->Label,
											'h3',
											array('class' => 'Label')
											);

						echo '<ul class="Rules">';

						// Render the Rule's Configuration UI
						foreach($TypeInfo->Rules as $AwardRule) {
							echo '<li>';
							include($AwardRule->GetConfigUI($this));
							echo '</li>';
						}

						echo '</ul>';
						echo '</div>';
					}
					echo '</div>'; // Rule Group Fieldset
				}
			?>
			<div class="Buttons">
				<?php
					echo $this->Form->Button(T('Save'), array('Name' => 'Save',));
					echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
				?>
			</div>
		</div> <!-- End Tabs Container -->
	</fieldset>
	<?php
		echo $this->Form->Close();
	?>
</div>
<?php include('awards_admin_footer.php'); ?>
