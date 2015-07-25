<?php if(!defined('APPLICATION')) exit();


$RequiredWritableDirs = $this->Data['RequiredWritableDirs'];
?>
<div class="Aelia AwardsPlugin StatusPage">
	<div class="Header">
		<?php include('awards_admin_header.php'); ?>
	</div>
	<div class="Content">
		<div id="Directories">
			<?php
				echo Wrap(T('Directories'), 'h4');
				echo '<ul>';
				foreach($RequiredWritableDirs as $Dir) {
					if(is_dir($Dir)) {
						if(is_writable($Dir)) {
							$DirStatus = T('Writable');
							$CssClass = 'Writable';
						}
						else {
							$DirStatus = T('Not writable');
							$CssClass = 'NotWritable';
						}
					}
					else {
						$DirStatus = T('Not existing');
						$CssClass = 'NotWritable';
					}
					$DirStatus = Wrap($DirStatus,
										'span',
										array('class' => $CssClass));
					echo Wrap(sprintf('%s: %s',
														$Dir,
														$DirStatus),
										'li');
				}
				echo '</ul>';
			?>
		</div>
	</div>
</div>
<?php include('awards_admin_footer.php'); ?>
