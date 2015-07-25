<?php if(!defined('APPLICATION')) exit();


/**
 * Extracts the User Information from an Awards and renders them.
 *
 * @param stdClass AwardsData The data of an Award earned by a User.
 */
function RenderUserInfo($AwardsData) {
	$UserObj = UserBuilder($AwardsData, '');
	$UserPhoto = UserPhoto($UserObj);
	$UserLink = UserAnchor($UserObj);

	echo Wrap($UserPhoto,
						'div',
						array('class' => 'UserPhoto'));
	echo Wrap($UserLink,
						'div',
						array('class' => 'UserLink'));
}

function RenderScores($TotalScore, $ClassScore, $ClassIDFilter) {
	$ScoreText = Wrap(sprintf(T('%d Points'), $TotalScore),
										'span',
										array('class' => 'Total'));
	// If data is filtered by Award Class, show the subtotal for
	// the class
	if(!empty($ClassIDFilter)) {
		$ScoreText .= Wrap(sprintf(T('(%d in this Class)'), $ClassScore),
											 'span',
											 array('class' => 'ClassTotal'));
	}
	echo Wrap($ScoreText,
						'div',
						array('class' => 'UserAwardsScore'));
}

// Indicates how many columns there are in the table that shows the list of
// Awards. It's mainly used to set the "colspan" attributes of
// single-valued table rows, such as Title, or the "No Results Found" message.
$UserAwardsTableColumns = 3;

// The following HTML will be displayed when the DataSet is empty.
$OutputForEmptyDataSet = Wrap(T('No data found.'),
															'td',
															array('colspan' => $UserAwardsTableColumns,
																		'class' => 'NoResultsFound',)
															);

$UserAwardsData = GetValue('UserAwardsData', $this->Data);
$AwardClassIDFilter = GetValue('AwardClassID', $this->Data);
//var_dump($AwardClassesData);
?>
<div id="AwardsLeaderboard" class="Aelia AwardsPlugin">
	<div class="Header">
		<?php
			echo Wrap(T('Awards Leaderboard'), 'h1');
			// Render Award Class Filters
			AwardClassesManager::RenderAwardClassFilters(AWARDS_PLUGIN_LEADERBOARD_PAGE_URL,
																									 $AwardClassIDFilter);
		?>
	</div>
	<div class="Content">
		<table id="TopUsers">
			<tbody>
				<?php
					if(empty($UserAwardsData) || ($UserAwardsData->NumRows() <= 0)) {
						echo Wrap($OutputForEmptyDataSet, 'tr');
					}
					else {
						$LastUserID = '';
						$LastUserTotalScore = 0;
						$LastUserClassScore = 0;
						foreach($UserAwardsData as $UserAward) {
							//var_dump($UserAward);
							if($UserAward->UserID != $LastUserID) {
								if(!empty($LastUserID)) {
									RenderScores($LastUserTotalScore, $LastUserClassScore, $AwardClassIDFilter);

									// Close previous User's row and open a new one
									echo '</td></tr>';

									$LastUserClassScore = 0;
								}

								// Save Current User and his total score
								$LastUserID = $UserAward->UserID;
								$LastUserTotalScore = $UserAward->TotalAwardsScore;

								echo '<tr>';
								// Display User information
								echo '<td class="UserInfo">';
								RenderUserInfo($UserAward);
								echo '</td>';
								// Open table cell for Awards List
								echo '<td class="Awards">';
							}

							// Add the awarded points for current Award to the Class total
							$LastUserClassScore += $UserAward->AwardedRankPoints;

							//var_dump($UserAward);die();
							$UserAwardImage = Img($UserAward->AwardImageFile,
																		array('alt' => $UserAward->AwardName,
																					'class' => 'AwardImage Medium ' . $UserAward->AwardClassCSSClass,
																					'title' => $UserAward->AwardName . ' ' .
																											sprintf(T('(%d points)'), $UserAward->AwardedRankPoints)));

							// Build link to Award page
							$UserAwardImgLink = Anchor($UserAwardImage,
																				 AWARDS_PLUGIN_AWARD_INFO_URL . '/' . $UserAward->AwardID,
																				 '');

							// Display Award
							echo Wrap($UserAwardImgLink,
												'span',
												array('class' => 'AwardImageWrapper'));
						} while($UserAward = $UserAwardsData->NextRow());

						// Display Score for last USer
						RenderScores($LastUserTotalScore, $LastUserClassScore, $AwardClassIDFilter);
						echo '</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>
