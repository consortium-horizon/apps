jQuery(document).ready(function($) {
	$('.DiscussionEvent input[type=checkbox]').change(function() {
		$('.DiscussionEventDate').toggle($(this).is(":checked"));
	}).trigger('change');
});