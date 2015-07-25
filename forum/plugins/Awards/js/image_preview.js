/**
 * Displays the preview of an image before it is uploaded via a Input::File
 * field.
 *
 * @param DOM SourceInput The Input field containing the image.
 * @param string DestinationImgID The ID of the image element to update.
 */
function AutoPreview(SourceInput, DestinationImgID) {
	var DestinationImg = $('#' + DestinationImgID);

	if(SourceInput.files && SourceInput.files[0]) {
		var Reader = new FileReader();

		Reader.onload = function(Element) {
			DestinationImg.attr('src', Element.target.result);
		}

		Reader.readAsDataURL(SourceInput.files[0]);
	}
}
