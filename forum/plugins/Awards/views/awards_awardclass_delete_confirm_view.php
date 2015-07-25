<?php if (!defined('APPLICATION')) exit();
/*
{licence}
*/
?>
<div class="Aelia AwardsPlugin ConfirmationDialog">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset>
		<div class="Title">
			<h3><?php echo T('Confirmation'); ?></h3>
		</div>
		<div class="Info">
			<?php
				$AwardClassName = $this->Form->GetValue('AwardClassName');
				$AwardClassDescription = $this->Form->GetValue('AwardClassDescription');
				echo Wrap(sprintf(T('You are about to delete <span class="Name" title="%s">%s</span> Award Class.'),
													$AwardClassDescription,
													$AwardClassName),
									'p');
				echo Wrap(T('This operation cannot be undone! Proceed?'),
									'p',
									array('class' => 'Warning'));
			?>
		</div>
		<div>
			<?php
				echo $this->Form->Hidden('AwardClassID');
				echo $this->Form->Hidden('AwardClassName');
				echo $this->Form->Button(T('OK'), array('Name' => 'OK',));
				echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
			?>
		</div>
	</fieldset>
	<?php
		 echo $this->Form->Close();
	?>
</div>
