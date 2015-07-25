<?php if (!defined('APPLICATION')) exit();



// TODO This view is only a placeholder to demonstrate how a plugin could implement a page showing the status of the overrides it requires
?>
<div class="FoundationPlugin">
	<div class="Header">
		<?php include('admin_header.php'); ?>
	</div>
	<div class="Content">
		<?php
			include 'overrideslist_view.php';
		?>
	</div>
</div>
