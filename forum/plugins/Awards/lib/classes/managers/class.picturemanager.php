<?php if(!defined('APPLICATION')) exit();


/**
 * Implements some methods to manage the pictures required by the plugin.
 */
class PictureManager extends BaseManager {
	const DEFAULT_IMAGE_WIDTH = 100;
	const DEFAULT_IMAGE_HEIGHT = 100;

	/**
	 * Retrieves the picture file uploaded with a form and returns the full URL
	 * to it. If a file has not been uploaded, the the method builds a URL uses
	 * a default picture file name to build the URL.
	 *
	 * @param string DestinationDir The destination directory where the picture
	 * wille be saved.
	 * @param string PictureField The name of the form field containing the
	 * picture.
	 * @param string DefaultPictureURL The Picture URL to return by default if
	 * no picture was uploaded.
	 * @return string The URL of the uploaded picture, or the default Picture URL.
	 * @throws An Exception if the the uploaded picture is not valid, or if it
	 * could not be saved.
	 */
	public static function GetPictureURL($DestinationDir, $PictureField = 'Picture', $DefaultPictureURL = null) {
		// If no file was uploaded, return the value of the Default Picture field
		if(!array_key_exists($PictureField, $_FILES) ||
			 empty($_FILES['Picture']['name'])) {
			return $DefaultPictureURL;
		}

		$UploadImage = new Gdn_UploadImage();

		// Validate the upload
		$TmpImage = $UploadImage->ValidateUpload('Picture');
		$TargetImage = $UploadImage->GenerateTargetName(PATH_LOCAL_UPLOADS, '', TRUE);

		// Save the uploaded image
		$ParsedValues = $UploadImage->SaveImageAs($TmpImage,
																						basename($TargetImage),
																						self::DEFAULT_IMAGE_HEIGHT,
																						self::DEFAULT_IMAGE_WIDTH,
																						array('Crop' => true));

		$UploadedFileName = $UploadImage->GetUploadedFileName();
		$PictureFileName = realpath($DestinationDir) . '/' . $UploadedFileName;

		/* Move the uploaded file into a subfolder inside plugin's folder. This
		 * will allow to easily export all Awards' pictures by simply copying the
		 * whole folder plugin.
		 * Note: it's not necessary to use move_uploaded_file() because such
		 * command was already invoked by Gdn_UploadImage::SaveAs(). The file we
		 * are moving here is, therefore.
		 */
		$TempPictureFileName = PATH_LOCAL_UPLOADS . '/' . pathinfo($ParsedValues['SaveName'], PATHINFO_BASENAME);
		//var_dump($TempPictureFileName, $PictureFileName);die();
		if(rename($TempPictureFileName, $PictureFileName) === false) {
			$Msg = sprintf('Could not rename file "%s" to "%s". Please make sure ' .
										 'that the destination directory exists and that it is writable',
										 $ParsedValues['SaveName'],
										 $PictureFileName);
			throw new Exception($Msg);
		}

		// Build a picture URL from the uploaded file
		return $DestinationDir . '/' . $UploadedFileName;
	}

	/**
	 * Checks if a file is a valid image.
	 *
	 * @param string FileName The file to check.
	 * @return bool True, if FileName is a valid image, False otherwise.
	 */
	public static function IsValidImage($FileName) {
		return (getimagesize($FileName) !== false);
	}

	/**
	 * Copies an image file from a source to a destination. It returns an error if
	 * file is not an image.
	 *
	 * @param string SourceFile The source file.
	 * @param string DestinationFile The destination file.
	 * @return int A code indicating the result of the operation.
	 */
	public static function CopyImage($SourceFile, $DestinationFile) {
		if(!self::IsValidImage($SourceFile)) {
			return AWARDS_ERR_FILE_NOT_AN_IMAGE;
		}

		return (copy($SourceFile, $DestinationFile) === true) ? AWARDS_OK : AWARDS_ERR_COULD_NOT_COPY_FILE;
	}
}
