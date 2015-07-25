<?php if (!defined('APPLICATION')) exit();
/*
{licence}
*/

?>
<div class="Aelia AwardsPlugin AssignAward">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset>
		<div class="Title">
			<h3><?php echo T('Assign Award'); ?></h3>
		</div>
		<div class="Info"><?php
			$AwardName = $this->Form->GetValue('AwardName');
			$AwardDescription = $this->Form->GetValue('AwardDescription');
			echo Wrap(sprintf(T('You are about to assign <span class="Name" title="%s">%s</span> Award. ' .
													'Please select the User(s) to whom you would like to assign it, then ' .
													'click "OK" to proceed.'),
												$AwardDescription,
												$AwardName),
								'p');
			echo Wrap(T('<strong>Note</strong>: currently, Awards cannot be revoked, therefore ' .
									'make sure you select the correct User(s). The Revoke feature will be ' .
									'Available in future versions of the plugin.'),
								'p',
								array('class' => 'Info'));

			echo Wrap(Wrap(T('This page is easier to use with JavaScript enabled. Please enable ' .
											 'to access all the features. If you prefer to continue without enabling ' .
											 'JavaScript, please follow the instructions below.'),
										 'p',
										 array('class' => 'Warning')),
								'noscript');
		?></div>
		<div>
			<ul class="Fields">
				<li class="WithScript"><?php
					echo $this->Form->Label(T('User Name'), 'UserName');
					echo Wrap(T('Type part of the User Name you are looking for. A list containing ' .
											'matching User Names will appear.'),
										'div',
										array('class' => 'Info',));
					echo $this->Form->TextBox('UserName');

					echo $this->Form->Label(T('Selected Users'), 'SelectedUsers');
					echo Wrap(T('This is the list of the Users who will receive the Award. ' .
											'<strong>Important</strong>: Users who already received it ' .
											'will not get another one.'),
										'div',
										array('class' => 'Info',));
					echo Wrap(Wrap('',
												 'div',
												 array('id' => 'SelectedUsers')),
										'div',
										array('class' => 'ui-widget'));
				?></li>
				<li class="NoScript"><?php
					// This field will contain the list of User IDs to whom the Award will
					// be assigned
					echo $this->Form->TextBox('UserIDList');
				?></li>
			</ul>
		</div>
		<div class="Buttons">
			<?php
				echo $this->Form->Hidden('AwardDataJSON');
				echo $this->Form->Button(T('OK'), array('Name' => 'OK',));
				echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
			?>
		</div>
	</fieldset>
	<?php
		 echo $this->Form->Close();
	?>
</div>
<?php include('awards_admin_footer.php'); ?>
