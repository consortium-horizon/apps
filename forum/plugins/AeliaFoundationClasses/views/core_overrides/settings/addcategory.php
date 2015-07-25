<?php if (!defined('APPLICATION')) exit();

?>
<div class="FoundationPlugin">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset id="General">
		<h1><?php echo T('Add Category'); ?></h1>
		<ul>
			 <li>
					<div class="Info"><?php
						 echo Wrap(T('Categories are used to organize discussions.', '<strong>Categories</strong> allow you to organize your discussions.'), 'div');
					?></div>
			 </li>
			 <li>
					<?php
						 echo $this->Form->Label('Category', 'Name');
						 echo $this->Form->TextBox('Name');
					?>
			 </li>
			 <li id="UrlCode">
				<?php
				echo Wrap(T('Category Url:'), 'strong');
				echo ' ';
				echo Gdn::Request()->Url('category', TRUE);
				echo '/';
				echo Wrap(htmlspecialchars($this->Form->GetValue('UrlCode')));
				echo $this->Form->TextBox('UrlCode');
				echo '/';
				echo Anchor(T('edit'), '#', 'Edit');
				echo Anchor(T('OK'), '#', 'Save SmallButton');
				?>
			 </li>
			 <li>
					<?php
						 echo $this->Form->Label('Description', 'Description');
						 echo $this->Form->TextBox('Description', array('MultiLine' => TRUE));
					?>
			 </li>
			 <?php
			 echo $this->Form->Simple(
					$this->Data('_ExtendedFields', array()),
					array('Wrap' => array('', '')));
			 ?>
			<?php if(count($this->PermissionData) > 0) { ?>
			 <li id="Permissions">
					<?php
						 echo $this->Form->CheckBox('CustomPermissions', 'This category has custom permissions.');

						 echo '<div class="CategoryPermissions">';
						 echo T('Check all permissions that apply for each role');
						 echo $this->Form->CheckBoxGridGroups($this->PermissionData, 'Permission');
						 echo '</div>';
					?>
			 </li>
			<?php } ?>
		</ul>
	</fieldset>
	<fieldset id="Options">
		<legend><?php echo Wrap('Options', 'h4'); ?></legend>
		<?php
			$this->FireEvent('AfterCategoryAddOptions');
		?>
	</fieldset>
	<?php echo $this->Form->Close('Save'); ?>
</div>
