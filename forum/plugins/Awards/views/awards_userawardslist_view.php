<?php if(!defined('APPLICATION')) exit();


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
				echo Wrap(T('Here you can view the list of Awards earned by a specific User, ' .
										'and revoke one or more of them, if needed.'), 'p');
			?>
		</div>
		<ul>
			<li><?php
				
				

				echo Wrap('Page not yet implemented.', 'h2');

				
				
			?></li>
		</ul>
		<?php
			 echo $this->Form->Close();
		?>
	</div>
</div>
<?php include('awards_admin_footer.php'); ?>
