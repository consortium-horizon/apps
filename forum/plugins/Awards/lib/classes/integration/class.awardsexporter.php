<?php if(!defined('APPLICATION')) exit();


/**
 * Handles the export of Awards, Award Classes and related images.
 */
class AwardsExporter extends BaseIntegration {
	private $_ZipFileName = array();

	/**
	 * Returns the full path and name of the Zip file created by the Exporter.
	 *
	 * @return string The full name of the file created by the exporter.
	 */
	public function GetZipFileName() {
		return $this->_ZipFileName;
	}

	/**
	 * Returns an instance of AwardsModel.
	 *
	 * @return AwardsModel An instance of AwardsModel.
	 * @see BaseManager::GetInstance()
	 */
	private function AwardsModel() {
		return $this->GetInstance('AwardsModel');
	}

	/**
	 * Returns an instance of AwardClassesModel.
	 *
	 * @return AwardsModel An instance of AwardClassesModel.
	 * @see BaseManager::GetInstance()
	 */
	private function AwardClassesModel() {
		return $this->GetInstance('AwardClassesModel');
	}

	/* @var string The version of the export data. It will be used in the future
	 * to distinguish exports created by different versions of the plugin.
	 */
	const EXPORT_V1 = '1';

	/**
	 * Compresses Awards Data, Award Classes Data and related images into a ZIP
	 * file.
	 *
	 * @param stdClass ExportData An object containing the Awards and Award Classes
	 * Data.
	 * @param array ImagesToExport An array of image file names.
	 * @return int A value that indicates the result of the operation.
	 */
	private function CompressData(stdClass $ExportData, array $ImagesToExport) {
		$FileHashes = array();

		
		$this->_ZipFileName = AWARDS_PLUGIN_EXPORT_PATH . '/vanilla_awards_' . (string)date('YmdHis', $ExportData->ExportInfo->RawTimeStamp) . '.zip';

		// Export and compress the data and the images
		$Zip = new ZipArchive();
		$this->Log()->info($this->StoreMessage(sprintf(T('Compressing data into file "%s"...'),
																										 $this->_ZipFileName)));
		// Create destination Zip File
		$ZipResult = $Zip->open($this->_ZipFileName, ZIPARCHIVE::OVERWRITE);
		if($ZipResult !== true) {
			$this->Log()->error($this->StoreMessage(sprintf(T('Error creating zip file "%s"...'),
																											$this->_ZipFileName)));
			return $ZipResult;
		}
		$this->Log()->info($this->StoreMessage(T('Storing Awards data...')));

		// Store the Awards data in JSON format
		$ExportDataFileName = self::AWARD_DATA_FILE_NAME;

		// Store Awards data, in JSON format
		$ExportDataJSON = json_encode($ExportData);
		if($Zip->addFromString($ExportDataFileName, $ExportDataJSON) === false) {
			$this->Log()->error($this->StoreMessage(T('Error storing export data.')));
			return AWARDS_ERR_COULD_NOT_COMPRESS_EXPORTDATA;
		}
		// Store hash of data
		$FileHashes[] = md5($ExportDataJSON);

		$this->Log()->info($this->StoreMessage(T('Storing images...')));

		// Store images in Zip file
		foreach($ImagesToExport as $DirName => $ImageFiles) {
			$LocaDirName = 'images/' . $DirName;
			$Zip->addEmptyDir($LocaDirName);
			$this->Log()->info($this->StoreMessage(sprintf(T('Storing files in folder "%s"...'),
																										 $LocaDirName)));

			// Remove duplicate images (i.e. images that are used by more than one entity)
			$ImageFiles = array_unique($ImageFiles);
			foreach($ImageFiles as $ImageFile) {
				$this->Log()->info($this->StoreMessage(sprintf(T('Storing image file "%s"...'),
																											 $ImageFile)));
				if(!$Zip->addFile($ImageFile, $LocaDirName . '/' . basename($ImageFile))) {
					$this->Log()->error($this->StoreMessage(sprintf(T('Error storing file "%s"...'),
																													$ImageFile)));
					return AWARDS_ERR_COULD_NOT_COMPRESS_IMAGE;
				};

				// Store hash of each image
				$FileHashes[] = md5_file($ImageFile);
			}
		}

		$this->Log()->info($this->StoreMessage(T('Calculating MD5 Checksum...')));
		// Store the checksum of the compressed data into the archive
		sort($FileHashes);
		$ArchiveHash = md5(implode(',', $FileHashes));

		$ExportData->ExportInfo->MD5 = $ArchiveHash;
		$Zip->setArchiveComment(json_encode($ExportData->ExportInfo));

		$Zip->close();
		return AWARDS_OK;
	}

	/**
	 * Returns an object containing some metadata related to the Export operation
	 * about to be performed.
	 *
	 * @param array Settings An array of settings to use for the export.
	 * @return stdClass An object containing the export metadata.
	 */
	private function GenerateExportMetaData(array $Settings) {
		$this->Log()->info($this->StoreMessage(T('Preparing Export MetaData...')));

		// Store Export metadata
		$ExportMetaData = new stdClass();
		$ExportMetaData->ExportFormat = self::EXPORT_V1;
		$ExportMetaData->Label = GetValue('ExportLabel', $Settings, '');
		$ExportMetaData->Description = GetValue('ExportDescription', $Settings, '');
		$ExportMetaData->RawTimeStamp = now();
		$ExportMetaData->TimeStamp = date('Y-m-d H:i:s', $ExportMetaData->RawTimeStamp);

		return $ExportMetaData;
	}

	/**
	 * Retrieves and returns the Award Classes data to be exported.
	 *
	 * @return stdClass An object containing Award Classes data and a list of the
	 * images used by the Classes.
	 */
	private function GetAwardClassesData() {
		$this->Log()->info($this->StoreMessage(T('Exporting Award Classes...')));

		$ImagesToExport = array();
		$AwardClasses = $this->AwardClassesModel()->Get()->Result();

		foreach($AwardClasses as $AwardClass) {
			$this->Log()->info($this->StoreMessage(sprintf(T('Processing Award Class "%s"...'),
																											 $AwardClass->AwardClassName)));
			$AwardClass = $this->CleanupData($AwardClass, array('AwardClassID', 'TotalAwardsUsingClass'));

			// Skip Classes without an image
			if(empty($AwardClass->AwardClassImageFile)) {
				continue;
			}
			$ImagesToExport[] = PATH_ROOT . '/' . $AwardClass->AwardClassImageFile;
			// Remove path info from the image
			$AwardClass->AwardClassImageFile = basename($AwardClass->AwardClassImageFile);
		}
		$this->Log()->info($this->StoreMessage(T('OK')));

		$Result = new stdClass();
		$Result->ImagesToExport = &$ImagesToExport;
		$Result->Data = &$AwardClasses;
		return $Result;
	}

	/**
	 * Retrieves and returns the Awards data to be exported.
	 *
	 * @return stdClass An object containing Awards data and a list of the
	 * images used by the Awards.
	 */
	private function GetAwardsData() {
		$this->Log()->info($this->StoreMessage(T('Exporting Awards...')));
		// Export the Awards
		$ImagesToExport = array();
		$Awards = $this->AwardsModel()->Get()->Result();

		foreach($Awards as $Award) {
			$this->Log()->info($this->StoreMessage(sprintf(T('Processing Award "%s"...'),
																											 $Award->AwardName)));
			$Award = $this->CleanupData($Award, array('AwardID', 'AwardIsEnabled', 'AwardClassID', 'AwardClassImageFile', 'AwardClassRankPoints'));

			$ImagesToExport[] = PATH_ROOT . '/' . $Award->AwardImageFile;
			// Remove path info from the image
			$Award->AwardImageFile = basename($Award->AwardImageFile);
		}
		$ExportData->Awards = &$Awards;

		$Result = new stdClass();
		$Result->ImagesToExport = &$ImagesToExport;
		$Result->Data = &$Awards;
		return $Result;
	}

	/**
	 * Exports Awards, Award Classes and their images to a compressed file.
	 *
	 * @param array ExportSettings An array of settings to be used for the export.
	 * @return int An integer value indicating the result of the operation.
	 */
	public function ExportData(array $ExportSettings) {
		$this->_Messages = array();
		$this->Log()->info($this->StoreMessage(T('Exporting Awards...')));

		// Create the result object
		$ExportData = new stdClass();

		// Generate some metadata about the export
		$ExportData->ExportInfo = $this->GenerateExportMetaData($ExportSettings);

		// Initialise the list of image files to be exported
		$ImagesToExport = array();

		// If requested, export Award Classes
		if(GetValue('ExportClasses', $ExportSettings) == 1) {
			$AwardClassesData = $this->GetAwardClassesData();

			$ImagesToExport['awardclasses'] = &$AwardClassesData->ImagesToExport;
			$ExportData->AwardClasses = &$AwardClassesData->Data;
		}

		// Export the Awards
		$AwardsData = $this->GetAwardsData();
		$ImagesToExport['awards'] = &$AwardsData->ImagesToExport;
		$ExportData->Awards = &$AwardsData->Data;

		// Generate compressed file with all the data and the images
		$Result = $this->CompressData($ExportData, $ImagesToExport);

		$this->Log()->info($this->StoreMessage(T('Export completed successfully.')));
		return $Result;
	}
}
