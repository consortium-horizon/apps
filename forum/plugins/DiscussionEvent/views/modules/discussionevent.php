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
	<li class="<?php echo CssClass($Discussion); ?>">
		<div class="Title">
		<?php echo Anchor(Gdn_Format::Text($Discussion->Name, false), DiscussionUrl($Discussion).($Discussion->CountCommentWatch > 0 ? '#Item_'.$Discussion->CountCommentWatch : ''), 'DiscussionLink'); ?>
		</div><div class="Meta"><span class="MItem">
		<?php echo Gdn_Format::Date($Discussion->DiscussionEventDate, 'html'); ?>
		</span></div>
	</li>
	<?php
	}
}
?>

<div class="Box BoxDiscussionEvents">
	<?php echo PanelHeading(t('Upcoming Events')); ?>
	<ul class="PanelInfo PanelDiscussionEvents DataList">
		<?php
		foreach ($this->Data('DiscussionEvents')->Result() as $Discussion) {
			WriteDiscussionEvent($Discussion);
		}
		?>
	</ul>
</div>