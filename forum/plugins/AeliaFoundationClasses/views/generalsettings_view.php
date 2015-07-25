<?php if (!defined('APPLICATION')) exit();


?>
<div class="FoundationPlugin">
	<div class="Header">
		<?php include('admin_header.php'); ?>
	</div>
	<div class="Content">
		<div>
			<?php
				echo Wrap(T('General Settings'), 'h3');
				// TODO Implement General Settings page
			?>
		</div>
		<div>
			<span><?php echo Wrap(T('This plugin does not require configuration.'),
														'div',
														array('class' => 'Info')); ?></span>
		</div>
	</div>
</div>
