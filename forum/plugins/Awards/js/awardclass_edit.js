
jQuery(document).ready(function(){
	var ImageWrapper = $('.AwardClassImageWrapper');
	var ImageElement = ImageWrapper.find('.AwardClassImage');
	var OriginalImage = ImageElement.attr('src');

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
			AutoPreview(this, 'AwardClassImagePreview');
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
