<?php if(!defined('APPLICATION')) exit();

	// The following HTML will be displayed when the DataSet is empty.
	$OutputForEmptyDataSet = Wrap(T('Award not found.'),
																'div',
																array('class' => 'NoResultsFound',)
																);

	$AwardData = GetValue('AwardData', $this->Data);
	$UserAwardData = GetValue('UserAwardData', $this->Data);
	//var_dump($AwardData);
?>
<div class="Aelia AwardsPlugin">
	<div class="AwardDetails clearfix">
		<?php
			if(empty($AwardData)) {
				echo $OutputForEmptyDataSet;
			}
			else {
				$AwardImage = Img($AwardData->AwardImageFile,
													array('alt' => $AwardData->AwardClassName,
																'class' => 'AwardImage Large ' . $AwardData->AwardClassCSSClass));
				echo Wrap($AwardImage,
									'div',
									array('class' => 'AwardImageWrapper'));
				echo '<div class="TextWrapper">';
				echo Wrap($AwardData->AwardName, 'h1', array('class' => 'AwardName'));
				echo Wrap($AwardData->AwardDescription, 'p', array('class' => 'AwardDescription'));
				echo '<div class="TotalTimesAwarded">';
				// Check if term should be "person" of "people", depending on how many Users earned the Award
				$UsersTerm = Plural($AwardData->TotalTimesAwarded, 'person', 'people');
				echo Wrap(sprintf(T('%d %s have earned this Award.'),
													$AwardData->TotalTimesAwarded,
													$UsersTerm));

				echo '</div>';

				echo '</div>';
			}
		?>
	</div>
	<?php
		if(!empty($UserAwardData)) {
			echo '<div class="YouEarned">';

			echo $UserPhoto = UserPhoto(UserBuilder(Gdn::Session()->User), 'UserPhoto');
			
			echo Wrap(sprintf(T('You earned this Award on %s.'),
												Gdn_Format::Date($UserAwardData->DateAwarded, T('Date.DefaultFormat'))));

			echo '</div>';
		}

		$RecentAwardRecipientsModule = GetValue('RecentAwardRecipientsModule', $this->Data);
		if(isset($RecentAwardRecipientsModule)) {
			echo '<div class="RecentRecipients">';
			echo $RecentAwardRecipientsModule->ToString();
			echo '</div>';
		}
	?>
</div>
