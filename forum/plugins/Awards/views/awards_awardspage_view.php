<?php if(!defined('APPLICATION')) exit();


function RenderUserAward($AwardID, $UserAwardData) {
	$UserAward = GetValue($AwardID, $UserAwardData);
	if(empty($UserAward)) {
		echo '';
		return;
	}

	$UserAwardInfo = sprintf(T('You earned this Award on %s'),
													 Gdn_Format::Date($UserAward->DateAwarded, T('Date.DefaultFormat')));
	echo Wrap('Yes',
						'span',
						array('title' => $UserAwardInfo,
									'class' => 'Tick'));
}

// Indicates how many columns there are in the table that shows the list of
// Awards. It's mainly used to set the "colspan" attributes of
// single-valued table rows, such as Title, or the "No Results Found" message.
$AwardsTableColumns = 3;

// The following HTML will be displayed when the DataSet is empty.
$OutputForEmptyDataSet = Wrap(T('No Awards found.'),
															'td',
															array('colspan' => $AwardsTableColumns,
																		'class' => 'NoResultsFound',)
															);

$AwardsData = GetValue('AwardsData', $this->Data);
$UserAwardData = GetValue('UserAwardData', $this->Data);
//var_dump($UserAwardData);
?>
<div id="AwardsPage" class="Aelia AwardsPlugin">
	<div class="Header">
		<?php
			echo Wrap(T('Awards'), 'h1');
			// Render Award Class Filters
			AwardClassesManager::RenderAwardClassFilters(AWARDS_PLUGIN_AWARDS_PAGE_URL,
																									 GetValue('AwardClassID', $this->Data));
		?>
	</div>
	<div class="Content">
		<table id="AwardsList">
			<tbody>
				<?php
					if(empty($AwardsData) || ($AwardsData->NumRows() <= 0)) {
						echo Wrap($OutputForEmptyDataSet, 'tr');
					}
					else {
						foreach($AwardsData->Result() as $Award) {
							echo '<tr>';
							echo '<td class="UserAwardInfo">';
							RenderUserAward($Award->AwardID, $UserAwardData);
							echo '</td>';

							//var_dump($Award);die();
							$AwardImage = Img($Award->AwardImageFile,
																array('alt' => $Award->AwardName,
																			'class' => 'AwardImage Medium ' . $Award->AwardClassCSSClass));
							// Build link to Award page
							$AwardImgLink = Anchor($AwardImage,
																		 AWARDS_PLUGIN_AWARD_INFO_URL . '/' . $Award->AwardID,
																		 '');

							$AwardName = Wrap($Award->AwardName, 'h3', array('class' => 'AwardName'));
							// Build link to Award page
							$AwardNameLink = Anchor($AwardName,
																			AWARDS_PLUGIN_AWARD_INFO_URL . '/' . $Award->AwardID,
																			'');

							$TotalTimesAwarded = Wrap(T('x') . '&nbsp;' . $Award->TotalTimesAwarded,
																				'p',
																				array('class' => 'TotalTimesAwarded',
																							'title' => sprintf(T('%d User(s) earned this Award'),
																																 $Award->TotalTimesAwarded)));
							echo Wrap($AwardImgLink . $AwardNameLink . $TotalTimesAwarded,
												'td',
												array('class' => 'Name Cell'));

							$AwardDescription = Wrap($Award->AwardDescription, 'span', array('class' => 'AwardDescription'));
							echo Wrap($AwardDescription,
												'td',
												array('class' => 'Description Cell'));
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>
