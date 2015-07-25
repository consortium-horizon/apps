
jQuery(document).ready(function(){
	var TabsList = $('<ul>');
	var ImageWrapper = $('.AwardImageWrapper');
	var ImageElement = ImageWrapper.find('.AwardImage');
	var OriginalImage = ImageElement.attr('src');

	// Add a link, which will be transformed into a Tab, for each Rule
	$('.Tab').each(function() {
		var Element = $(this);
		// Find the Label to use to create a Tab for the Group
		var Label = Element.find('.Label').first()
		Label.hide();

		// Extract the text from the Label
		var LabelText = Label.html();

		// Add a link that will become a Tab
		var MenuLink = $('<a>')
			.attr('href', '#' + Element.attr('id'))
			.html(LabelText)

		TabsList.append($('<li>').html(MenuLink));
	});

	// Prepend the Tabs just before the first Rule Group
	var TabsElement = $('.AwardsPlugin').find('.Tabs').first();
	TabsElement.prepend(TabsList);
	TabsElement.tabs();

	// Handle clearing of new image, restoring original one
	ImageWrapper.delegate('#RestoreImage', 'click', function() {
		ClearFileField('#Form_Picture');
		ImageWrapper.removeClass('Preview');
		ImageElement.attr('src', OriginalImage);
		// Remove the element eventually used to display the Preview Image in IE
		$('#ImagePreviewIE').remove();
	});

	// Display a Preview when a new Image has been selected
	$('#Form_Picture').change(function() {
		if($(this).val()) {
			ImageWrapper.addClass('Preview');
			$('#Form_PreUploadedImageFile').val('');

			// Display preview of image
			AutoPreview(this, 'AwardImagePreview');
		}
	});

	// Configure and initialise the Server Side File Browser, which can be used
	// to select a previously uploaded image
	var ServerSideFileBrowserCfg = {
		root: '/',
		script: 'browsedir'
	};
	$('#ServerSideBrowser').fileTree(ServerSideFileBrowserCfg, function(SelectedFile) {
		ClearFileField('#Form_Picture');
		// Remove the element eventually used to display the Preview Image in IE
		$('#ImagePreviewIE').remove();

		var ImageFile = gdn.definition('path_uploads') + SelectedFile;
		$('#Form_PreUploadedImageFile').val(ImageFile);
		ImageElement.attr('src', gdn.url('/uploads' + SelectedFile));
		ImageWrapper.addClass('Preview');
  });
});
