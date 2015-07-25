<?php if (!defined('APPLICATION')) exit();
/*
{licence}
*/

?>
<div class="Aelia AwardsPlugin Export">
	<div class="Header">
		<?php include('awards_admin_header.php'); ?>
	</div>
	<div class="Content">
		<?php
			echo $this->Form->Open();
			echo $this->Form->Errors();
		?>
		<div class="Info"><?php
			echo Wrap(T('Here you can export your Awards and Award Classes to an ' .
									'external file, which you can then import in another forum.'),
								'p');
		?></div>
		<div class="clearfix">
			<div class="Column">
				<ul>
					<li><?php
						echo $this->Form->Label(T('Export Label (optional)'), 'ExportLabel');
						echo Wrap(T('This information is for your reference ' .
												'only.'),
											'div',
											array('class' => 'Info',));
						echo $this->Form->TextBox('ExportLabel');
					?></li>
					<li><?php
						echo $this->Form->Label(T('Export Description (optional)'), 'ExportDescription');
						echo Wrap(T('This information is for your reference ' .
												'only.'),
											'div',
											array('class' => 'Info',));
						echo $this->Form->TextBox('ExportDescription',
																			array('MultiLine' => true,
																						'class' => 'TextBox'));
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
						echo $this->Form->Checkbox('ExportClasses', T('Export Award Classes.'));
									echo Wrap(T('Here you can export your Awards and Award Classes to an ' .
									'external file, which you can then import in another forum.'),
								'span');
					?></li>
				</ul>
			</div>
		</div>
		<div class="Buttons">
			<?php
				echo $this->Form->Button(T('Export'), array('Name' => 'Export',));
			?>
		</div>
		<?php
			 echo $this->Form->Close();
		?>
		<?php
			// Display output section after an export has been completed
			$ExportResult = GetValue('ExportResult', $this->Data, null);
			$OutputCssClass = $ExportResult !== null ? '' : 'Hidden';
		?>
		<div id="Output" class="clearfix <?php echo $OutputCssClass; ?>">
			<div class="Header">
				<?php
					$ResultMessage = ($ExportResult === AWARDS_OK) ? T('Success.') : T('Failure.');
					echo Wrap(T('Export completed.') . ' ' . $ResultMessage, 'h2');
					if(isset($ExportResult) && ($ExportResult !== AWARDS_OK)) {
						echo Wrap(sprintf(T('Operation failed. Export result code: %d.'),
															$ExportResult),
											'p',
											array('class' => 'Warning'));
					}
				?>
			</div>
			<div class="Column">
				<div id="DownloadInfo">
				<?php
					$ZipFileName = GetValue('ZipFileName', $this->Data);

					if(!empty($ZipFileName)) {
						echo Wrap(T('Export file ready. Click to download.'),
											'h4',
											array('class' => 'Title'));
						echo Anchor($ZipFileName,
												AWARDS_PLUGIN_EXPORT_URL . '/' . $ZipFileName,
												'Download');
					}
				?>
				</div>
			</div>
			<div class="Column">
				<div id="MessageLog">
					<?php
						echo Wrap(T('Export Log'),
											'h4',
											array('class' => 'Title'));

						$Messages = GetValue('ExportMessages', $this->Data);
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
