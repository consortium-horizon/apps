/**
 * Displays the preview of an image before it is uploaded via a Input::File
 * field. The function declared in this method is used only in Internet Explorer,
 * which doesn't support the FileReader() method.
 *
 * @param DOM SourceInput The Input field containing the image.
 * @param string DestinationImgID The ID of the image element to update.
 */
function AutoPreview(SourceInput, DestinationImgID) {
	var DestinationImage = $('#' + DestinationImgID);
	var ImagePreviewIE = DestinationImage.siblings('#ImagePreviewIE');
	if(ImagePreviewIE.length <= 0) {
		var ImagePreviewIE = $('<div>').attr('id', 'ImagePreviewIE');
	}
	DestinationImage.after(ImagePreviewIE);

  ImagePreviewIE[0].filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = $(SourceInput).val();
}
