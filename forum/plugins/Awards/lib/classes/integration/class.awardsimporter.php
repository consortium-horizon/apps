<?php if(!defined('APPLICATION')) exit();


/**
 * Handles the import of Awards, Award Classes and related images.
 */
class AwardsImporter extends BaseIntegration {
	// @var string Temporary folder where compressed data is extracted before the import.
	private $_TempFolder;

	// @Var array A list of all the files that have been imported/copied by during the Import process.
	private $_ImportedFiles = array();

	// @var array A list of Award Classes used to categorise imported Awards.
	private $_AwardClassesForImport = array();

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

	/**
	 * Verifies that an archive's checksum matches the one calculated from its
	 * content.
	 *
	 * @param string OriginalChecksum The checksum stored within the archive.
	 * @return bool True, if the checksums match, False otherwise.
	 */
	private function VerifyArchiveChecksum($OriginalChecksum) {
		$this->Log()->info($this->StoreMessage(sprintf(T('Verifying archive checksum...'))));
		$FileHashes = array();

		$ImageFiles = $this->GetFiles($this->_TempFolder);
		//var_dump($ImageFiles); die();
		foreach($ImageFiles as $ImageFile) {
			$FileHashes[] = md5_file($ImageFile);
		}
		//var_dump($ImageFiles, $FileHashes);
		sort($FileHashes);
		$CalculatedChecksum = md5(implode(',', $FileHashes));
		//var_dump($CalculatedChecksum, $OriginalChecksum);
		$this->Log()->info($this->StoreMessage(sprintf(T('Original Checksum: %s. Calculated checksum: %s.'),
																									 $OriginalChecksum,
																									 $CalculatedChecksum)));

		if($CalculatedChecksum != $OriginalChecksum) {
			$this->Log()->info($this->StoreMessage(T('Checksum verification failed.')));
			return false;
		}
		else {
			$this->Log()->info($this->StoreMessage(T('Checksum verification passed.')));
			return true;
		}
	}

	/**
	 * Extracts the content of a zip file to a temporary folder.
	 *
	 * @param string FileName The name of the file to uncompress.
	 * @return int An integer value indicating the result of the operation.
	 */
	private function ExtractData($FileName) {
		$this->Log()->info($this->StoreMessage(sprintf(T('Extracting data from file "%s"...'),
																										 $FileName)));
		if(!file_exists($FileName)) {
			$this->Log()->error($this->StoreMessage(T('File does not exist')));
			return AWARDS_ERR_FILE_NOT_FOUND;
		}

		// Create a temporary folder for the data to import
		$this->_TempFolder = '/tmp/' . (string)uniqid('awards_import_', true);
		$this->Log()->debug($this->StoreMessage(sprintf(T('Creating temporary folder "%s"...'),
																										$this->_TempFolder)));
		//var_dump($this->_TempFolder);

		if(!mkdir($this->_TempFolder)) {
			$LogMsg = T('Could not create temporary folder.');
			$this->Log()->error($this->StoreMessage($LogMsg));
			return AWARDS_ERR_COULD_NOT_CREATE_FOLDER;
		}

		// Extract the data and the images
		$Zip = new ZipArchive();
		// Open source Zip File
		$ZipResult = $Zip->open($FileName);
		if($ZipResult !== true) {
			$this->Log()->error($this->StoreMessage(sprintf(T('Error opening zip file "%s"...'),
																											$FileName)));
			return $ZipResult;
		}
		$this->Log()->info($this->StoreMessage(T('Extracting data...')));
		if($Zip->extractTo($this->_TempFolder) === false) {
			$LogMsg = sprintf(T('Could not extract data to temporary folder "%s".'),
												$this->_TempFolder);
			return AWARDS_ERR_COULD_NOT_EXTRACT_EXPORTDATA;
		}

		$ExportInfo = json_decode($Zip->getArchiveComment());
		$Zip->close();

		$this->Log()->info($this->StoreMessage(T('Data extraction completed.')));

		// Verify archive integrity
		if(!$this->VerifyArchiveChecksum($ExportInfo->MD5)) {
			return AWARDS_ERR_CHECKSUM_ERROR;
		};

		return AWARDS_OK;
	}

	/**
	 * Loads a file containing exported Awards data in JSON format.
	 *
	 * @return string|bool A JSON representation of the data, or False on failure.
	 */
	private function LoadImportData() {
		$DataFileName = $this->_TempFolder . '/' . self::AWARD_DATA_FILE_NAME;
		$ImportData = file_get_contents($DataFileName);

		if($ImportData === false) {
			$this->Log()->error($this->StoreMessage(sprintf(T('Could not load data from file "%s"...'),
																											$DataFileName)));
		}
		return $ImportData;
	}

	/**
	 * Deletes temporary files and folders created during the import.
	 *
	 * @param int Result The Result of the import operation. Used to determine if
	 * only temporary directories should be cleaned up, or if everything has to be
	 * rolled back.
	 * @param bool TestingMode Indicates if the import was done in testing mode,
	 * in which case everything will be rolled back.
	 */
	private function Cleanup($Result, $TestingMode) {
		$this->Log()->info($this->StoreMessage(T('Cleaning up folders...')));

		$this->DelTree($this->_TempFolder);

		// If Import failed, delete all the images imported so far. This is done
		// so that the folders won't be polluted with "hanging" files
		if(($Result !== AWARDS_OK) || ($TestingMode === true)) {
			$this->DeleteImportedFiles();
		}
	}

	/**
	 * Import Awards' and Award Classes' images.
	 *
	 * @param string SubFolder the subfolder, inside the compressed archive, where
	 * the images to import are located.
	 * @param string DestinationFolder The destination folder where to copy the
	 * files.
	 * @param int DuplicateItemAction Indicates what to do when a file with the
	 * same name of the source exists in the destination folder. It can have the
	 * following values:
	 * - BaseIntegration::DUPLICATE_ACTION_SKIP
	 * - BaseIntegration::DUPLICATE_ACTION_OVERWRITE
	 * - BaseIntegration::DUPLICATE_ACTION_RENAME
	 * @return int An integer value indicating the result of the operation.
	 */
	private function ImportImages($SubFolder, $DestinationFolder, $DuplicateItemAction = self::DUPLICATE_ACTION_SKIP) {
		$ImagesFolder = $this->_TempFolder . '/images/' . $SubFolder;
		// Get list of images to import
		$FilesList = $this->GetFiles($ImagesFolder, false);
		//var_dump($FilesList);

		$Result = AWARDS_OK;
		foreach($FilesList as $File) {
			$this->Log()->info($this->StoreMessage(sprintf(T('Importing file "%s"...'),
																										 $File)));

			$FileInfo = pathinfo($File);
			//var_dump($FileInfo);die();

			$CopyFile = true;
			$DestinationFile = $DestinationFolder . '/' . $FileInfo['basename'];
			if(file_exists($DestinationFile)) {
				$this->Log()->info($this->StoreMessage(T('File already exists')));
				switch($DuplicateItemAction) {
					case self::DUPLICATE_ACTION_OVERWRITE:
						$this->Log()->info($this->StoreMessage(T('Overwriting')));
						break;
					case self::DUPLICATE_ACTION_RENAME:
						$this->Log()->info($this->StoreMessage(T('Renaming')));
						$DestinationFile = $DestinationFolder . '/' . $this->RandomRename($FileInfo['filename']) . '.' . $FileInfo['extension'];
						break;
					case self::DUPLICATE_ACTION_SKIP:
					default:
						$CopyFile = false;
						$this->Log()->info($this->StoreMessage(T('Skipping')));
						break;
				}
			}

			if($CopyFile === true) {
				if(copy($File, $DestinationFile) === false) {
					$this->Log()->error($this->StoreMessage(T('File copy failed.')));
					$Result = AWARDS_ERR_COULD_NOT_COPY_FILE;
					break;
				}
			}

			// Store the details of the imported file
			$ImportedFileInfo = new stdClass();
			$ImportedFileInfo->SourceFile = $File;
			$ImportedFileInfo->DestinationFile = $DestinationFile;
			//var_dump($ImportedFileInfo);die();

			$this->_ImportedFiles[$SubFolder . '/' . $FileInfo['basename']] = $ImportedFileInfo;
		}

		return $Result;
	}

	/**
	 * Deletes all the imported files. This function is used during cleanup when
	 * the import fails, to ensure that no "hanging" files are left around.
	 */
	private function DeleteImportedFiles() {
		$this->Log()->info($this->StoreMessage(T('Deleting imported files...')));
		foreach($this->_ImportedFiles as $FileBaseName => $FileInfo) {
			$this->Log()->debug($this->StoreMessage(sprintf(T('Deleting File "%s"...'),
																											$FileInfo->DestinationFile)));
			if(unlink($FileInfo->DestinationFile)) {
				$this->Log()->debug($this->StoreMessage(T('Success.')));
			}
			else {
				$this->Log()->debug($this->StoreMessage(T('Failure.')));
			}
		}
	}

	/**
	 * Imports the Award Classes.
	 *
	 * @param stdClass ImportData An object containing Award Classes data.
	 * @param array ImportSettings An array of settings to use for the import.
	 * @return int An integer value indicating the result of the operation.
	 */
	private function ImportAwardClasses(stdClass $ImportData, array $ImportSettings) {
		$this->Log()->info($this->StoreMessage(T('Importing Award Classes...')));

		// Retrieve the action to take when an item is Duplicated
		$DuplicateItemAction = GetValue('DuplicateItemAction', $ImportSettings);

		// Copy the Award Classes images to the destination folder
		$Result = $this->ImportImages('awardclasses', AWARDS_PLUGIN_AWARDCLASSES_PICS_PATH, $DuplicateItemAction);

		if($Result == AWARDS_OK) {
			// Transform Award Classes object into an associative array. This is needed
			// because each Class' data must be passed as an associative array to the model
			$AwardClasses = json_decode(json_encode($ImportData->AwardClasses), true);

			foreach($AwardClasses as $AwardClass) {
				$AwardClassName = GetValue('AwardClassName', $AwardClass);
				$this->Log()->debug($this->StoreMessage(sprintf(T('Importing Award Class "%s"...'),
																												$AwardClassName)));

				$ExistingAwardClass = $this->AwardClassesModel()->GetAwardClassByName($AwardClassName)->FirstRow();

				if($ExistingAwardClass !== false) {
					$this->Log()->debug($this->StoreMessage(T('Award Class already exists')));
					switch($DuplicateItemAction) {
						case self::DUPLICATE_ACTION_OVERWRITE:
							$this->Log()->debug($this->StoreMessage(T('Overwriting')));
							$AwardClass['AwardClassID'] = GetValue('AwardClassID', $ExistingAwardClass);
							break;
						case self::DUPLICATE_ACTION_RENAME:
							$this->Log()->debug($this->StoreMessage(T('Renaming')));
							$AwardClass['AwardClassName'] = $this->RandomRename($AwardClassName);
							break;
						case self::DUPLICATE_ACTION_SKIP:
						default:
							$this->Log()->debug($this->StoreMessage(T('Skipping')));
							continue 2;
					}
				}

				// Replace the simple base name of the image with the full path and name
				// of the file that has been imported
				$AwardClassImage = GetValue('AwardClassImageFile', $AwardClass, '');
				if(!empty($AwardClassImage)) {
					//var_dump($AwardClassImage, $this->_ImportedFiles);die();
					$FileInfo = $this->_ImportedFiles['awardclasses/' . $AwardClassImage];
					$AwardClass['AwardClassImageFile'] = $FileInfo->DestinationFile;
					//var_dump($AwardClass['AwardClassImageFile']);die();
				}

				// Save Award Class
				if($this->AwardClassesModel()->Save($AwardClass) === false) {
					$this->Log()->error($this->StoreMessage(sprintf(T('Could not import Award Class. ' .
																														'Class details (JSON): %s.'),
																													json_encode($AwardClass))));
					$Result = AWARDS_ERR_COULD_NOT_IMPORT_AWARD_CLASS;
					break;
				}
			}
		}
		return $Result;
	}

	/**
	 * Retrieved the Award Class ID for an Award, using the Award Class name. If
	 * a matching entry is not found, a Default value is used.
	 *
	 * @param string AwardClassName The name of the Class.
	 * @param string DefaultAwardClassID The default Award Class ID to use when
	 * a match is not found.
	 * @return int An Award Class ID.
	 */
	private function GetClassForAward($AwardClassName, $DefaultAwardClassID) {
		$this->Log()->debug($this->StoreMessage(sprintf(T('Retrieving ID of Award Class "%s".'),
																										$AwardClassName)));

		$this->_AwardClassesForImport = array();

		// Retrieve the ID from the cache or, if it's not cached, from the database
		$AwardClassID = GetValue($AwardClassName, $this->_AwardClassesForImport, GetValue('AwardClassID', $this->AwardClassesModel()->GetAwardClassByName($AwardClassName)->FirstRow()));

		// If Award Class ID could not be found either in the cache or in the
		// database, use the default one
		$AwardClassID = empty($AwardClassID) ? $DefaultAwardClassID : $AwardClassID;

		// Cache the Award Class ID for later use
		$this->_AwardClassesForImport[$AwardClassName] = $AwardClassID;

		return $AwardClassID;
	}

	/**
	 * Imports the Awards.
	 *
	 * @param stdClass ImportData An object containing Awards data.
	 * @param array ImportSettings An array of settings to use for the import.
	 * @return int An integer value indicating the result of the operation.
	 */
	private function ImportAwards(stdClass $ImportData, array $ImportSettings) {
		$this->Log()->info($this->StoreMessage(T('Importing Awards...')));

		// Retrieve the action to take when an item is Duplicated
		$DuplicateItemAction = GetValue('DuplicateItemAction', $ImportSettings);
		// Retrieve the default Award Class ID to use when a class matching the one
		// specified in Awards' data is not found
		$DefaultAwardClassID = GetValue('DefaultAwardClassID', $ImportSettings);

		// Copy the Awards images to the destination folder
		$Result = $this->ImportImages('awards', AWARDS_PLUGIN_AWARDS_PICS_PATH, $DuplicateItemAction);

		if($Result == AWARDS_OK) {
			// Transform Awards object into an associative array. This is needed
			// because each ' data must be passed as an associative array to the model
			$Awards = json_decode(json_encode($ImportData->Awards), true);

			foreach($Awards as $Award) {
				//var_dump($Award);die();

				$AwardName = GetValue('AwardName', $Award);
				$this->Log()->debug($this->StoreMessage(sprintf(T('Importing Award "%s"...'),
																												$AwardName)));

				$ExistingAward = $this->AwardsModel()->GetAwardByName($AwardName)->FirstRow();

				if($ExistingAward !== false) {
					$this->Log()->debug($this->StoreMessage(T('Award already exists')));
					switch($DuplicateItemAction) {
						case self::DUPLICATE_ACTION_OVERWRITE:
							$this->Log()->debug($this->StoreMessage(T('Overwriting')));
							$Award['AwardID'] = GetValue('AwardID', $ExistingAward);
							break;
						case self::DUPLICATE_ACTION_RENAME:
							$this->Log()->debug($this->StoreMessage(T('Renaming')));
							$Award['AwardName'] = $this->RandomRename($AwardName);
							break;
						case self::DUPLICATE_ACTION_SKIP:
						default:
							$this->Log()->debug($this->StoreMessage(T('Skipping')));
							continue 2;
					}
				}

				// Retrieve the ID of the Award Class from its name
				$Award['AwardClassID'] = $this->GetClassForAward(GetValue('AwardClassName', $Award),
																												 $DefaultAwardClassID);

				// Awards are imported as Disabled, to prevent them from being assigned
				// as soon as the import is completed
				$Award['AwardIsEnabled'] = 0;

				// Replace the simple base name of the image with the full path and name
				// of the file that has been imported
				$AwardImage = GetValue('AwardImageFile', $Award, '');
				if(!empty($AwardImage)) {
					//var_dump($AwardImage, $this->_ImportedFiles);die();
					$FileInfo = $this->_ImportedFiles['awards/' . $AwardImage];
					$Award['AwardImageFile'] = $FileInfo->DestinationFile;
					//var_dump($Award['AwardImageFile']);die();
				}

				// Save Award
				if($this->AwardsModel()->Save($Award) === false) {
					$this->Log()->error($this->StoreMessage(sprintf(T('Could not import Award . ' .
																														'Award details (JSON): %s.'),
																													json_encode($Award))));
					$Result = AWARDS_ERR_COULD_NOT_IMPORT_AWARD;
					break;
				}
			}
		}
		return $Result;
	}


	private function GetAwardsData() {
		$this->Log()->info($this->StoreMessage(T('Importing Awards...')));
		// Import the Awards
		$ImagesToImport = array();
		$Awards = $this->AwardsModel()->Get()->Result();

		foreach($Awards as $Award) {
			$this->Log()->info($this->StoreMessage(sprintf(T('Processing Award "%s"...'),
																											 $Award->AwardName)));
			$Award = $this->CleanupData($Award);

			$ImagesToImport[] = PATH_ROOT . '/' . $Award->AwardImageFile;
			// Remove path info from the image
			$Award->AwardImageFile = basename($Award->AwardImageFile);
		}
		$ImportData->Awards = &$Awards;

		$Result = new stdClass();
		$Result->ImagesToImport = &$ImagesToImport;
		$Result->Data = &$Awards;
		return $Result;
	}

	/**
	 * Imports Awards, Award Classes and related images.
	 *
	 * @param array ImportSettings Settings to use for the import.
	 * @return int An integer value indicating the result of the operation.
	 */
	public function ImportData($ImportSettings) {
		//var_dump($ImportSettings);die();
		$this->_Messages = array();
		$this->_ImportedFiles = array();

		$TestingMode = isset($ImportSettings['TestImport']);
		if($TestingMode) {
			$this->Log()->info($this->StoreMessage(T('TESTING MODE - No changes will be saved.')));
		}

		$this->Log()->info($this->StoreMessage(T('Importing Awards...')));

		$Result = $this->ExtractData(GetValue('FileName', $ImportSettings));

		// Load the data to import from the exported JSON
		$ImportData = $this->LoadImportData();
		if($ImportData === false) {
			$Result = AWARDS_ERR_COULD_NOT_LOAD_DATA_FILE;
		}

		if($Result === AWARDS_OK) {
			// Decode the JSON string into an object
			$ImportData = json_decode($ImportData);
			//var_dump($ImportData); die();

			Gdn::Database()->BeginTransaction();
			try {
				if(GetValue('ImportClasses', $ImportSettings, 1)) {
					// Import Award Classes
					$Result = $this->ImportAwardClasses($ImportData, $ImportSettings);
				}

				// Import Awards
				if($Result === AWARDS_OK) {
					$Result = $this->ImportAwards($ImportData, $ImportSettings);
				}

				// Use a transaction to either save ALL data (Awards and Award Classes)
				// successfully, or none of it. This will prevent partial saves and
				// reduce inconsistencies
				if(($Result === AWARDS_OK) && (!$TestingMode)) {
					Gdn::Database()->CommitTransaction();
				}
				else {
					Gdn::Database()->RollbackTransaction();
				}
			}
			catch(Exception $e) {
				Gdn::Database()->RollbackTransaction();
				$ErrorMsg = sprintf(T('Exception occurred while importing Awards data. ' .
																								'Error: %s. Trace: %s'),
																							$e->getMessage(),
																							$e->getTraceAsString());
				$this->Log()->error($this->StoreMessage($ErrorMsg));

				$Result = AWARDS_ERR_EXCEPTION_OCCURRED;
			}
		}

		$this->Cleanup($Result, $TestingMode);

		if($Result === AWARDS_OK) {
			$this->Log()->info($this->StoreMessage(T('Import completed successfully.')));
		}
		else {
			$this->Log()->info($this->StoreMessage(T('Operation aborted.')));
		}

		if($TestingMode) {
			$this->Log()->info($this->StoreMessage(T('TESTING MODE COMPLETE.')));
		}

		return $Result;
	}
}
