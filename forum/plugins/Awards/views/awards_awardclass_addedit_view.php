<?php	if(!defined('APPLICATION')) exit();
/*
{licence}
*/

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
		$SourceAwardClassInfo = Wrap($Form->GetValue('SourceAwardClassName'),
																 'span',
																 array('class' => 'ClassInfo',
																			 'title' => $Form->GetValue('SourceAwardDescription'),));
		return sprintf(T('Clone Award Class "%s"'), $SourceAwardClassInfo);
	}

	return $Form->GetValue('AwardClassID') ? sprintf(T('Edit Award Class'), $Form->GetValue('AwardClassName')) : T('Add new Award Class');
}
?>
<div class="Aelia AwardsPlugin AwardClassEdit">
	<?php
		echo $this->Form->Open(array('enctype' => 'multipart/form-data'));
		echo $this->Form->Errors();

		echo $this->Form->Hidden('AwardClassID');
		// Field ImageFile contains the path and file name of the Image currently
		// associated with the Award Class. This field will only be populated when
		// an existing Award Class is being modified.
		echo $this->Form->Hidden('AwardClassImageFile');
		
	?>
	<fieldset id="AwardClass">
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
		<div id="Fields">
			<div class="Thresholds Help Aside"><?php
				echo Wrap(T('What are Award Classes?'),
									'h4',
									array('class' => 'Title',));
				echo Wrap(T('Award Classes are a neat way to organise Awards. By creating Classes, ' .
										'you can group Awards together, for example to divide the common Awards ' .
										'from the most prestigious ones.'),
									'div',
									array('class' => 'Info',));
				echo Wrap(T('The following is a generic set of Award Classes that could suit most Communities:'),
									'div',
									array('class' => 'Info',));
				echo '<ul>';
				echo Wrap(T('<strong>Bronze</strong>. These would be the most common, easy to achieve Awards'),
									'li');
				echo Wrap(T('<strong>Silver</strong>. These would be the uncommon Awards, granted to Users who show effort and participation.'),
									'li');
				echo Wrap(T('<strong>Gold</strong>. These would be the rarest Awards, obtained only by User who achieved excellent results.'),
									'li');
				echo Wrap(T('<strong>Special</strong>. These would be Awards that are assigned in particular circumstances, usually only by Administrators, rather than automatically.'),
									'li');
				echo '</ul>';
				echo Wrap(T('If you do not wish to use the features provided by Award Classes, ' .
										'simply create a single one without a background image and/or a score, ' .
										'and assign all Awards to it. You will be able to change this later, if needed.'),
									'div',
									array('class' => 'Info',));
			?></div>
			<ul>
				<li>
					<?php
						echo $this->Form->Label(T('Award Class Name'), 'AwardClassName');
						echo Wrap(T('Enter a name for the Award Class. It must be unique amongst the Award ' .
												'Classes and it must respect specifications for CSS class names (i.e. it ' .
												'can only contain letters, numbers, hyphens and underscores).'),
											'div',
											array('class' => 'Info',
														));
						echo $this->Form->TextBox('AwardClassName');

						if($this->Form->GetValue('AwardClassID', false)) {
							echo Wrap(sprintf(T('This class is currently being used by %d Awards.'),
																$this->Form->GetValue('TotalAwardsUsingClass')),
												'div',
												array('class' => 'ClassUsage'));
						}
					?>
				</li>
				<li>
					<?php
						echo $this->Form->Label(T('Award Class Description'), 'AwardClassDescription');
						echo Wrap(T('Enter a description for the Award. It will be displayed on the ' .
												'the public Awards page and it can be useful to give your Users an ' .
												'idea what type of Awards are included in the class. For example, a ' .
												'"Gold" class Award could be harder to achieve than a "Silver" class ' .
												'one.'),
											'div',
											array('class' => 'Info',
														));
						echo $this->Form->TextBox('AwardClassDescription',
																			array('multiline' => true,
																						'rows' => 5,
																						'cols' => 60,));
					?>
				</li>
				<li>
					<?php
						echo $this->Form->Label(T('Rank Points'), 'RankPoints');
						echo Wrap(T('Enter the amount of Rank Points to be granted to the Users who ' .
												'receive an Award using this class. These points are <strong>added</strong> ' .
												'to the ones granted by the Award itself.'),
											'div',
											array('class' => 'Info',));
						echo $this->Form->TextBox('RankPoints');
					?>
				</li>
				<li>
					<?php
						echo $this->Form->Label(T('CSS Class'), 'AwardClassCSSClass');
						echo Wrap(T('Enter a CSS Class to be used by the Award Class. It must respect ' .
												'specifications for CSS class names (i.e. it can only contain letters, ' .
												'numbers, hyphens and underscores). If you leave this field empty, the ' .
												'its value will be generated from the Award Class name.'),
											'div',
											array('class' => 'Info',
														));
						echo $this->Form->TextBox('AwardClassCSSClass');
					?>
				</li>
				<li class="clearfix">
					<?php
						echo $this->Form->Label(T('Award Class Picture'), 'Picture');
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
						$AwardClassImage = Wrap(Img($this->Form->GetValue('AwardClassImageFile', $DummyImageFile),
																				array('id' => 'AwardClassImagePreview',
																							'class' => 'AwardClassImage Large',)),
																		'div');
						echo Wrap($ImagePreviewOverlay .
											$AwardClassImage .
											$RestoreButton,
											'div',
											array('class' => 'AwardClassImageWrapper'));
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
				<li>
					<?php
						echo $this->Form->Label(T('Additional CSS'), 'AwardClassCSS');
						echo Wrap(T('Here you can enter CSS rules that will be applied to the Award Class. ' .
												'Every Award belonging to this class will automatically inherit the ' .
												'rules an it will be rendered accordingly. For example, if you specify ' .
												'a <code>border</code> style, all Awards in this class will have such ' .
												'border.'),
											'div',
											array('class' => 'Info',
														));
						echo Wrap(T('<strong>Important</strong>: just enter CSS commands without enclosing them in curly braces. ' .
												'The plugin will take care of doing it automatically.'),
											'div',
											array('class' => 'Info',
														));
						echo $this->Form->TextBox('AwardClassCSS',
																			array('multiline' => true,
																						'rows' => 5,
																						'cols' => 60,));
					?>
				</li>
			</ul>
		</div>
	</fieldset>
	<fieldset class="Buttons">
		<?php
			echo $this->Form->Button(T('Save'), array('Name' => 'Save',));
			echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
		?>
	</fieldset>
	<?php
		echo $this->Form->Close();
	?>
</div>
<?php include('awards_admin_footer.php'); ?>
