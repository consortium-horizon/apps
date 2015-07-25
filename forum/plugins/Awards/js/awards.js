
jQuery(document).ready(function(){

});

/**
 * Clears a File Field.
 *
 * @param string Selector The jQuery Selector to use to find the File Field.
 */
function ClearFileField(Selector) {
	var ImageInput = $(Selector);
	ImageInput.replaceWith(ImageInput = ImageInput.val('').clone(true));
}
