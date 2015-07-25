<?php if(!defined('APPLICATION')) exit();

	// Indicates how many columns there are in the table that shows the list of
	// configured Awards. It's mainly used to set the "colspan" attributes of
	// single-valued table rows, such as Title, or the "No Results Found" message.
	$AwardsTableColumns = 6;

	// The following HTML will be displayed when the DataSet is empty.
	$OutputForEmptyDataSet = Wrap(T('No Awards configured.'),
																'td',
																array('colspan' => $AwardsTableColumns,
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
				echo Wrap(T('Here you can configure the Awards that can be assigned to ' .
										'Forum Users.'), 'p');
			?>
		</div>
		<div class="FilterMenu">
		<?php
			echo Anchor(T('Add Award'), AWARDS_PLUGIN_AWARD_ADDEDIT_URL, 'Button');
		?>
		</div>
		<div class="ClassFilters">
		<?php
			// Render Award Class Filters
			AwardClassesManager::RenderAwardClassFilters(AWARDS_PLUGIN_AWARDS_LIST_URL,
																									 GetValue('AwardClassID', $this->Data));
		?>
		</div>
		<table id="AwardsList" class="display AltRows">
			<thead>
				<tr>
					<th class="Image"><?php echo T('Icon'); ?></th>
					<th class="Description"><?php echo T('Award Name'); ?></th>
					<th><?php echo T('Class'); ?></th>
					<th class="RankPoints"><?php echo T('Rank Points'); ?></th>
					<th class="TimesAwarded"><?php echo T('Times Awarded'); ?></th>
					<th class="Enabled"><?php echo T('Enabled?'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php
					$AwardsDataSet = GetValue('AwardsDataSet', $this->Data);

					// If DataSet is empty, just print a message.
					if(empty($AwardsDataSet) || ($AwardsDataSet->NumRows() <= 0)) {
						echo Wrap($OutputForEmptyDataSet, 'tr');
					}
					
					// Output the details of each row in the DataSet
					foreach($AwardsDataSet as $Award) {
						echo "<tr>\n";
						if(empty($Award->AwardImageFile)) {
							$ImageCellContent = Gdn_Format::Text(T('None'));
						}
						else {
							$ImageCellContent = Img($Award->AwardImageFile,
																			array('class' => 'AwardImage Medium ' . $Award->AwardClassCSSClass,
																						'alt' => $Award->AwardName));
						}
						echo Wrap($ImageCellContent,
											'td',
											array('class' => 'Image',));

						// Output Award Name and Description
						$AwardName = Wrap(Gdn_Format::Text($Award->AwardName),
															'div',
															array('class' => 'Name',));
						$AwardDescription = Wrap(Gdn_Format::Text($Award->AwardDescription),
															'div',
															array('class' => 'Description',));

						echo Wrap($AwardName . $AwardDescription, 'td');
						echo Wrap(Gdn_Format::Text($Award->AwardClassName), 'td');

						// Calculate and format total points that will be given by the Award
						$TotalAwardRankPoints = Wrap($Award->RankPoints + $Award->AwardClassRankPoints,
																				 'div',
																				 array('class' => 'Total'));
						// Format the points given by the Award and by its Class
						$AwardRankPoints = Wrap(sprintf(T('<span class="Amount">%d</span> from Award'),
																						$Award->RankPoints),
																		'li');
						$AwardClassRankPoints = Wrap(sprintf(T('<span class="Amount">%d</span> from Award Class'),
																								 $Award->AwardClassRankPoints),
																				 'li');

						$AwardRankPointsDetail = Wrap($AwardRankPoints . $AwardClassRankPoints,
																					'ul',
																					array('class' => 'Detail'));
						echo Wrap($TotalAwardRankPoints . $AwardRankPointsDetail, 'td', array('class' => 'RankPoints',));

						//echo Wrap(Gdn_Format::Text($Award->AwardDescription), 'td', array('class' => 'Description',));
						echo Wrap(Gdn_Format::Text($Award->TotalTimesAwarded), 'td', array('class' => 'TimesAwarded',));

						// Output "Enabled" indicator
						$EnabledText = ($Award->AwardIsEnabled == 1) ? T('Yes') : T('No');

						// Display a convenient link to enable/disable the Award with a single click
						$EnabledText = Anchor(Gdn_Format::Text($EnabledText),
																	sprintf('%s?%s=%d&%s=%d',
																					AWARDS_PLUGIN_AWARD_ENABLE_URL,
																					AWARDS_PLUGIN_ARG_AWARDID,
																					$Award->AwardID,
																					AWARDS_PLUGIN_ARG_ENABLEFLAG,
																					($Award->AwardIsEnabled == 1 ? 0 : 1)),
																	'EnableLink',
																	array('title' => T('Click here to change Award status (Enabled/Disabled).'),)
																	);

						echo Wrap($EnabledText,
											'td',
											array('class' => 'Enabled',)
											);

						echo "<td class=\"Buttons\">\n";
						// Output Add/Edit button
						echo Anchor(T('Assign'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARD_ASSIGN_URL,
																AWARDS_PLUGIN_ARG_AWARDID,
																Gdn_Format::Url($Award->AwardID)),
												'Button AssignAward');
						// Output Add/Edit button
						echo Anchor(T('Edit'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARD_ADDEDIT_URL,
																AWARDS_PLUGIN_ARG_AWARDID,
																Gdn_Format::Url($Award->AwardID)),
												'Button AddEditAward');
						// Output Clone button
						echo Anchor(T('Clone'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARD_CLONE_URL,
																AWARDS_PLUGIN_ARG_AWARDID,
																Gdn_Format::Url($Award->AwardID)),
												'Button CloneAward');
						// Output Delete button
						echo Anchor(T('Delete'),
												sprintf('%s?%s=%s',
																AWARDS_PLUGIN_AWARD_DELETE_URL,
																AWARDS_PLUGIN_ARG_AWARDID,
																Gdn_Format::Url($Award->AwardID)),
												'Button DeleteAward');
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
