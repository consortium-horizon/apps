<?php if (!defined('APPLICATION')) exit();

// Vanilla 2.1 compatibility:
if (!function_exists('PanelHeading')) {
	function PanelHeading($content, $attributes = '') {
		return Wrap($content, 'h4', $attributes);
	}
}

if (!function_exists('WriteDiscussionEvent')) {
	function WriteDiscussionEvent($Discussion, $Prefix = null) {
	?>
	
		<span class="Title">
			<span class="MItem"><?php echo Gdn_Format::Date($Discussion->DiscussionEventDate, '%e %B %Y'); ?></span>
			<?php echo Anchor(Gdn_Format::Text($Discussion->Name, false), DiscussionUrl($Discussion).($Discussion->CountCommentWatch > 0 ? '#Item_'.$Discussion->CountCommentWatch : ''), 'DiscussionLink'); ?>
		</span>
		<!--
			<div class="Meta">
				<span class="MItem">
					<?php echo Gdn_Format::Date($Discussion->DiscussionEventDate, '%e %B %Y'); ?>
				</span>
			</div>
		-->

	<?php
	}
}
?>

<div class="Box BoxDiscussionEvents">
	<?php echo PanelHeading(t('Upcoming Events')); ?>
	<ul class="PanelInfo PanelDiscussionEvents DataList">
		<li class="<?php echo CssClass($Discussion); ?>">
		<?php
		foreach ($this->Data('DiscussionEvents')->Result() as $Discussion) {
			WriteDiscussionEvent($Discussion);
		}
		?>
		</li>
	</ul>
</div>