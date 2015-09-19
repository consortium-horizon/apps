jQuery(document).ready(function($){

	function formatLayout(dropdown) {
		switch (dropdown.val()) {
			case "dom":
				$(".positioning").not(".positioning--dom").hide();
				$(".positioning--dom").show();
				break;
			case "vanillaelement":
				$(".positioning").not(".positioning--vanilla").hide();
				$(".positioning--vanilla").show();
				break;
			case "customelement":
				$(".positioning").not(".positioning--custom").hide();
				$(".positioning--custom").show();
				break;
			default:
		}
	}
	
	$('#Form_PositionMethod').on('change', function() {
		formatLayout($(this));
	});

	formatLayout($('#Form_PositionMethod'));
});
