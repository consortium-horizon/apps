<?php if(!defined('APPLICATION')) exit();

	// Indicates how many columns there are in the table that shows the list of
	// configured Award Classes. It's mainly used to set the "colspan" attributes of
	// single-valued table rows, such as Title, or the "No Results Found" message.
	$AwardClassesTableColumns = 5;

	// The following HTML will be displayed when the DataSet is empty.
	$OutputForEmptyDataSet = Wrap(T('No Award Classes configured.'),
																'td',
																array('colspan' => $AwardClassesTableColumns,
																			'class' => 'NoResultsFound',)
																);
?>
<div class="Aelia AwardsPlugin">
	<div class="Header">
		<?php include('awards_admin_header.php'); ?>
	</div>
	<div class="Content">
		<?php
			echo $this->Form->Open();
			echo $this->Form->Errors();
		?>
		<div class="Info">
			<?php
				echo Wrap(T('Here you can configure the Award Classes. Classes are useful to group the ' .
										'Awards, for example to distinguish between the ones easyto obtain from the ' .
										'more difficult ones.'), 'p');
			?>
		</div>
		<div class="FilterMenu">
		<?php
			echo Anchor(T('Add Award Class'), AWARDS_PLUGIN_AWARDCLASS_ADDEDIT_URL, 'Button');
		?>
		</div>
		<table id="AwardClassesList" class="display AltRows">
			<thead>
				<tr>
					<th class="Image"><?php echo T('Background Image'); ?></th>
					<th class="Description"><?php echo T('Award Class Description'); ?></th>
					<th class="RankPoints"><?php echo T('Rank Points'); ?></th>
					<th class="TotalAwardsUsingClass"><?php echo T('Awards using Class'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php
					$AwardClassesDataSet = GetValue('AwardClassesDataSet', $this->Data);

					// If DataSet is empty, just print a message.
					if(empty($AwardClassesDataSet) || ($AwardClassesDataSet->NumRows() <= 0)) {
						echo Wrap($OutputForEmptyDataSet, 'tr');
					}
					
					// Output the details of each row in the DataSet
					foreach($AwardClassesDataSet as $AwardClass) {
						echo "<tr>\n";
						if(empty($AwardClass->AwardClassImageFile)) {
							$ImageCellContent = Gdn_Format::Text(T('None'));
						}
						else {
							$ImageCellContent = Img($AwardClass->AwardClassImageFile,
														array('class' => 'AwardClassImage Medium ',
																	'alt' => $AwardClass->AwardClassName));
						}
						echo Wrap($ImageCellContent,
											'td',
											array('class' => 'Image',));

						// Output Award Class Name and Description
						$AwardClassName = Wrap(Gdn_Format::Text($AwardClass->AwardClassName),
																	 'div',
																	 array('class' => 'Name',));
						$AwardClassDescription = Wrap(Gdn_Format::Text($AwardClass->AwardClassDescription),
																					'div',
																					array('class' => 'Description',));

						echo Wrap($AwardClassName . $AwardClassDescription, 'td');
						echo Wrap(Gdn_Format::Text($AwardClass->RankPoints), 'td', array('class' => 'RankPoints',));
						echo Wrap(Gdn_Format::Text($AwardClass->TotalAwardsUsingClass), 'td', array('class' => 'TotalAwardsUsingClass',));

						echo "<td class=\"Buttons\">\n";
						// Output Add/Edit button
						echo Anchor(T('Edit'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARDCLASS_ADDEDIT_URL,
																AWARDS_PLUGIN_ARG_AWARDCLASSID,
																Gdn_Format::Url($AwardClass->AwardClassID)),
												'Button AddEditAwardClass');
						// Output Add/Edit button
						echo Anchor(T('Clone'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARDCLASS_CLONE_URL,
																AWARDS_PLUGIN_ARG_AWARDCLASSID,
																Gdn_Format::Url($AwardClass->AwardClassID)),
												'Button AddEditAwardClass');
						// Display the delete button only if Class is not being used by any Award
						if($AwardClass->TotalAwardsUsingClass <= 0) {
							// Output Delete button
							echo Anchor(T('Delete'),
													sprintf('%s?%s=%s',
																	AWARDS_PLUGIN_AWARDCLASS_DELETE_URL,
																	AWARDS_PLUGIN_ARG_AWARDCLASSID,
																	Gdn_Format::Url($AwardClass->AwardClassID)),
													'Button DeleteAwardClass');
						}
						else {
							echo Wrap(T('Cannot delete'),
												'span',
												array('class' => 'Button Disabled',
															'title' => sprintf(T('Award Class "%s" cannot be deleted ' .
																									 'because there are still Awards using it.'),
																								 GetValue('AwardClassName', $AwardClass))));
						}
						echo "</td>\n";
						echo "</tr>\n";
					}
				?>
			 </tbody>
		</table>
		<?php
			echo $this->Form->Close();
		?>
	</div>
</div>
<?php include('awards_admin_footer.php'); ?>
