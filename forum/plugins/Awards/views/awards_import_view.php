<?php if (!defined('APPLICATION')) exit();
/*
{licence}
*/

?>
<div class="Aelia AwardsPlugin Import">
	<div class="Header">
		<?php include('awards_admin_header.php'); ?>
	</div>
	<div class="Content">
		<?php
			echo $this->Form->Open(array('enctype' => 'multipart/form-data'));
			echo $this->Form->Errors();
		?>
		<div class="Info"><?php
			echo Wrap(T('Here you can import your Awards and Award Classes to an ' .
									'external file, which you can then import in another forum.'),
								'p');
		?></div>
		<div class="clearfix">
			<div class="Column">
				<ul>
					<li><?php
						echo $this->Form->Label(T('File to import'), 'FileToImport');
						echo Wrap(T('Select a file previously exported by the Awards plugin ' .
												'to import in this forum. Such file will be uploaded and ' .
												'processed automatically.'),
											'p');
						echo $this->Form->Input('FileToImport', 'file');
					?></li>
				</ul>
			</div>
			<div class="Column">
				<?php
					echo Wrap(T('Options'),
										'h4',
										array('class' => 'OptionsLabel'));
				?>
				<ul>
					<li><?php
						echo $this->Form->Checkbox('ImportClasses',
																			 T('Import Award Classes.'),
																			 array('value' => 1,
																						 'checked' => true));
									echo Wrap(T('Import Award Classes, if Source File contains them. If this is left ' .
									'unchecked, all Awards will be put under the Default Award Class.'),
								'span');
					?></li>
					<li><?php
						echo $this->Form->Label(T('Default Award Class'), 'DefaultAwardClassID');
						$AwardClassesPageLink = Anchor(T('Award Classes page'), AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
						echo Wrap(sprintf(T('Select Award Class to which imported Awards ' .
																'will be assigned when their original Class ' .
																'does not exist. If the dropdown list is empty, ' .
																'please go to %s to create one, then come back to ' .
																'this page.'),
															$AwardClassesPageLink),
											'div',
											array('class' => 'Info',));
						echo $this->Form->DropDown('DefaultAwardClassID',
																			 GetValue('AwardClasses', $this->Data, array()),
																			 array('ValueField' => 'AwardClassID',
																						 'TextField' => 'AwardClassName'));
					?></li>
					<li><?php
						echo $this->Form->Label(T('If an item is duplicated'), 'DuplicateItemAction');
						echo Wrap(T('This information is for your reference ' .
												'only.'),
											'div',
											array('class' => 'Info',));
						echo $this->Form->DropDown('DuplicateItemAction',
																			 GetValue('DuplicateItemActions', $this->Data, array()),
																			 array());
					?></li>
				</ul>
			</div>
		</div>
		<div class="Buttons">
			<?php
				echo $this->Form->Button(T('Import'),
																 array('Name' => 'Import',
																			 'Title' => 'Import Awards and Award Classes data.'));
				echo $this->Form->Button(T('Test Import'),
																 array('Name' => 'TestImport',
																			 'Title' => 'Tests the import Awards and Award Classes data. ' .
																									'It performs the same operations as the Import button, but ' .
																									 'all changes are undone at the end of the import.'));
			?>
		</div>
		<?php
			 echo $this->Form->Close();
		?>
		<?php
			// Display output section after an import has been completed
			$ImportResult = GetValue('ImportResult', $this->Data, null);
			$OutputCssClass = $ImportResult !== null ? '' : 'Hidden';
		?>
		<div id="Output" class="clearfix <?php echo $OutputCssClass; ?>">
			<div class="Header">
				<?php
					$ResultMessage = ($ImportResult === AWARDS_OK) ? T('Success.') : T('Failure.');
					echo Wrap(T('Import completed.') . ' ' . $ResultMessage, 'h2');
					if(isset($ImportResult) && ($ImportResult !== AWARDS_OK)) {
						echo Wrap(sprintf(T('Operation failed. Import result code: %d.'),
															$ImportResult),
											'p',
											array('class' => 'Warning'));
					}
				?>
			</div>
			<div class="Column">
				<div id="MessageLog">
					<?php
						echo Wrap(T('Import Log'),
											'h4',
											array('class' => 'Title'));

						$Messages = GetValue('ImportMessages', $this->Data);
						if(!empty($Messages)) {
							echo '<div id="Messages">';
							echo '<ul>';
							echo '<li>';
							echo implode('</li><li>', $Messages);
							echo '</li>';
							echo '</ul>';
							echo '</div>';
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php include('awards_admin_footer.php'); ?>
</div>
