<?php if(!defined('APPLICATION')) exit();


/**
 * Controller for all operations regarding Awards and Award Classes.
 * This class covers all Award-centric operations, i.e. the ones against an
 * Award/Award Class definition. Operations on data related the Awards earned by
 * Users (top scorers, hall of fame, etc) are handled by UserAwardsManager class.
 *
 * @see UserAwardsManager.
 */
class AwardsManager extends BaseManager {

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
	 * Returns an instance of UserAwardsModel.
	 *
	 * @return AwardsModel An instance of UserAwardsModel.
	 * @see BaseManager::GetInstance()
	 */
	private function UserAwardsModel() {
		return $this->GetInstance('UserAwardsModel');
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
	 * Returns an instance of UserModel.
	 *
	 * @return AwardsModel An instance of UserModel.
	 * @see BaseManager::GetInstance()
	 */
	private function UserModel() {
		return $this->GetInstance('UserModel');
	}

	/**
	 * Returns an instance of AwardsImporter.
	 *
	 * @return AwardsImporter An instance of AwardsImporter.
	 * @see BaseManager::GetInstance()
	 */
	private function AwardsImporter() {
		return $this->GetInstance('AwardsImporter');
	}

	/**
	 * Prepares some Award Data to be used for cloning an Award. This method
	 * removes or alters all data that identifies an Award, so that the User will
	 * be forced to enter different details for the clone.
	 *
	 * @param stdClass AwardData An object containing Award data.
	 * @return stdClass The processed Award Data object.
	 */
	private function PrepareAwardDataForCloning(stdClass $AwardData) {
		// Save references to source Award
		$AwardData->SourceAwardID = $AwardData->AwardID;
		$AwardData->SourceAwardName = $AwardData->AwardName;
		$AwardData->SourceAwardDescription = $AwardData->AwardDescription;

		// Unset and alter Award key data, as clone will have to use its own
		unset($AwardData->AwardID);
		unset($AwardData->DateInserted);
		unset($AwardData->DateUpdated);

		$AwardData->AwardName = T('CLONE-') . $AwardData->AwardName;
		$AwardData->AwardDescription = T('CLONE-') . $AwardData->AwardDescription ;
		return $AwardData;
	}

	/**
	 * Class constructor.
	 *
	 * @return AwardsManager
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Renders the Awards List (Admin) page.
	 *
	 * @param AwardsPlugin Caller The Plugin who called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardsList(AwardsPlugin $Caller, Gdn_Controller $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARDS_LIST_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		$Wheres = array();
		// Prepare the Award Class filter, if needed
		$AwardClassID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDCLASSID);
		if(!empty($AwardClassID)) {
			$Wheres['VAAL.AwardClassID'] = $AwardClassID;
			$Sender->SetData('AwardClassID', $AwardClassID);
		}

		
		$AwardsDataSet = $this->AwardsModel()->GetWithTimesAwarded($Wheres, array('AwardName asc'));
		

		$Sender->SetData('AwardsDataSet', $AwardsDataSet);

		$Sender->Render($Caller->GetView('awards_awardslist_view.php'));
	}

	/**
	 * Decodes the JSON containing the configuration for each Rule and adds its
	 * data to an array, in form of objects. Each object will contain the
	 * configuration for a Rule.
	 *
	 * @param Gdn_DataSet AwardDataSet The DataSet containing the configuration
	 * for an Award. Each row should contain a "RuleClass" entry, associated to
	 * a JSON string with the Rule Configuration.
	 * @return array An associative array of RuleClass => Object, where each
	 * object contains the configuration for the Rule.
	 */
	private function GetRulesSettings(stdClass $AwardData) {
		// Decode the JSON containing Rules Settings for processing
		$RulesSettings = json_decode($AwardData->RulesSettings);

		// If no settings are found, reflect it by returning an empty array
		if(empty($RulesSettings)) {
			return array();
		}

		$Result = array();
		foreach($RulesSettings as $RuleClass => $Settings) {
			$Result[$RuleClass] = $Settings;
		}

		//var_dump($AwardData->RulesSettings, $Result); die();

		return $Result;
	}

	/**
	 * Prepares a hierarchy of Rule Groups and Sections, which will be used to
	 * render each Rule's settings section in the appropriate part of the page.
	 * Each Group will contain Sections, and each Section will contain the
	 * configuration UI of one or more Rules.
	 *
	 * @return array An associative array containing the hierarchy of Rules Groups
	 * and Sections.
	 */
	private function PrepareAwardRulesSections() {
		$Result = array();
		foreach(AwardRulesManager::$RuleGroups as $GroupID => $GroupLabel) {
			$GroupSection = new stdClass();
			$GroupSection->Label = $GroupLabel;
			$GroupSection->TypeSections = array();
			$GroupSection->CountRules = 0;

			foreach(AwardRulesManager::$RuleTypes as $TypeID =>$TypeLabel) {
				$TypeSection = new stdClass();
				$TypeSection->Label = $TypeLabel;
				$TypeSection->Rules = array();

				$GroupSection->TypeSections[$TypeID] = $TypeSection;
			}

			$Result[$GroupID] = $GroupSection;
		}

		return $Result;
	}

	/**
	 * Loads and returns the available Award Classes.
	 *
	 * @return Gdn_DataSet A DataSet containing the available Award Classes.
	 */
	protected function GetAwardClasses() {
		// Retrieve all available Award Classes
		$AwardClassesModel = new AwardClassesModel();
		return $AwardClassesModel->Get();
	}

	/**
	 * Examines the values posted via the Award Add/Edit form to determine which
	 * image will be used for the Award. The image is stored directly in the
	 * Sender's form values.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 * @return int An integer value indicating the result of the operation.
	 */
	private function _DetermineAwardImage($Sender) {
		$PreUploadedImageFile = $Sender->Form->GetFormValue('PreUploadedImageFile');
		// Check if a pre-uploaded image should be used for the Award
		if(!empty($PreUploadedImageFile)) {
			$DestinationFile = AWARDS_PLUGIN_AWARDS_PICS_PATH . '/' . basename($PreUploadedImageFile);

			$Result = PictureManager::CopyImage($PreUploadedImageFile, $DestinationFile);
			if($Result === AWARDS_OK) {
				$Sender->Form->SetFormValue('AwardImageFile', $DestinationFile);
			}
			else {
				$this->Log()->error(sprintf(T('Could not use pre-uploaded image file "%s" for the Award. ' .
																			'Error code: %d.'),
																		$PreUploadedImageFile,
																		$Result));
				$Sender->Form->AddError(sprintf(T('Could not use selected pre-uploaded file. ' .
																					'Please make sure that destination directory ' .
																					'(%s) exists and is writable, and that selected file ' .
																					'is an image.'),
																				AWARDS_PLUGIN_AWARDS_PICS_PATH));
			}
		}
		else {
			// Check if a new image has been uploaded for the Award
			try {
				// Retrieve the URL of the Picture associated with the Award.
				$ImageFile = PictureManager::GetPictureURL(AWARDS_PLUGIN_AWARDS_PICS_PATH,
																									 'Picture',
																									 $Sender->Form->GetFormValue('AwardImageFile'));
				// Add the Picture URL to the Form
				$Sender->Form->SetFormValue('AwardImageFile', $ImageFile);
				$Result = AWARDS_OK;
			}
			catch(Exception $e) {
				$Sender->Form->AddError($e->getMessage());
				$Result = AWARDS_ERR_EXCEPTION_OCCURRED;
			}
		}
		return $Result;
	}

	/**
	 * Renders the page to Add/Edit an Award.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardAddEdit(AwardsPlugin $Caller, $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARD_ADDEDIT_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		// Load jQuery UI
		$this->LoadJQueryUI($Sender);

		$Sender->AddCssFile('jqueryFileTree.css', 'plugins/AeliaFoundationClasses/js/jqueryFileTree');
		$Sender->AddJsFile('jqueryFileTree.js', 'plugins/AeliaFoundationClasses/js/jqueryFileTree');
		$Sender->AddJsFile('image_preview.js', 'plugins/Awards/js');
		$Sender->AddJsFile('image_preview_ie.js', 'plugins/Awards/js', array('IE' => 'gte IE 8'));

		// Load auxiliary files
		$Sender->AddJsFile('award_edit.js', 'plugins/Awards/js');


		// Retrieve the Award ID passed as an argument (if any)
		$AwardID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDID, null);

		// Set Award Data in the form
		$Sender->Form->SetModel($this->AwardsModel());
		// Display inline errors
		$Sender->Form->ShowErrors();

		// Load Award Classes
		$Sender->SetData('AwardClasses', $this->GetAwardClasses());

		//var_dump($Sender->Form);

		if(!empty($AwardID)) {
			// Load Award Data
			$AwardData = $this->AwardsModel()->GetAwardByID($AwardID)->FirstRow();
			$Sender->Form->SetData($AwardData);

			$Sender->SetData('RulesSettings', $this->GetRulesSettings($AwardData));
		}

		// If seeing the form for the first time...
		if($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Just show the form with the default values
		}
		else {
			// If User Canceled, go back to the List
			if($Sender->Form->ButtonExists('Cancel')) {
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}

			$Data = $Sender->Form->FormValues();

			// Validate PostBack
			// The field named "Save" is actually the Save button. If it exists, it means
			// that the User chose to save the changes.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Sender->Form->ButtonExists('Save')) {
				// Determine the image to use for the Award
				$this->_DetermineAwardImage($Sender);

				// Validate settings for Award Rules
				$RulesSettingsOK = $Caller->RulesManager()->ValidateRulesSettings($Sender->Form);
				if($RulesSettingsOK) {
					Gdn::Database()->BeginTransaction();

					try{
						// Convert the Rules settings to JSON and add it to the data to be saved
						$JSONRulesSettings = $Caller->RulesManager()->RulesSettingsToJSON($Sender->Form);
						$Sender->Form->SetFormValue('RulesSettings', $JSONRulesSettings);

						// If there are no Rule Settings, the Award is forcibly disabled.
						// Without any Rule configuration it would never be assigned, anyway
						//var_dump($JSONRulesSettings);
						if(empty($JSONRulesSettings)) {
							$Sender->Form->SetFormValue('AwardIsEnabled', 0);
						}

						// Save Awards settings
						$Saved = $Sender->Form->Save();

						// Use a transaction to either save ALL data (Award and Rules)
						// successfully, or none of it. This will prevent partial saves and
						// reduce inconsistencies
						if($Saved) {
							Gdn::Database()->CommitTransaction();
						}
						else {
							Gdn::Database()->RollbackTransaction();
						}
					}
					catch(Exception $e) {
						Gdn::Database()->RollbackTransaction();
						$this->Log()->error($ErrorMsg = sprintf(T('Exception occurred while saving Award configuration. ' .
																										'Award Name: %s. Error: %s.'),
																									$Sender->Form->GetFormValue('AwardName'),
																									$e->getMessage()));
						throw $e;
					}

					if($Saved) {
						$Sender->InformMessage(T('Your changes have been saved.'));
						$Caller->FireEvent('ConfigChanged');

						// Once changes have been saved, redirect to the main page
						Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
						//return $this->AwardsList($Caller, $Sender);
					}
					else {
						/* If data has been posted back and it contains errors, extract the
						 * Rules information from it and pass as a separated field to the
						 * Sender. This will allow the Rules to pick it up automatically.
						 */
						$Sender->SetData('RulesSettings', GetValue('Rules', $Data));

						// If Saving failed for no apparent reason (no error messages),
						// suggest the User to have a look at the Log to see if there are
						// more details about the reason of the failure
						if($Sender->Form->ErrorCount() <= 0) {
							$Sender->Form->AddError(T('Data could not be saved. Please check the Log to see ' .
																				'more details about the issue.'));
						}
					}
				}
			}
		}

		// Pass the list of installed rules to the View, so that it can ask each
		// one to render its configuration section
		$Sender->SetData('AwardRules', $Caller->RulesManager()->GetRules());

		// Builds a structure that will be used to group the Rules in sections
		$Sender->SetData('AwardRulesSections', $this->PrepareAwardRulesSections());

		// Add some definitions
		$Sender->AddDefinition('path_uploads', PATH_UPLOADS);

		// Retrieve the View that will be used to configure the Award
		$Sender->Render($Caller->GetView('awards_award_addedit_view.php'));
	}

	/**
	 * Renders the page to Clone an Award.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardClone(AwardsPlugin $Caller, $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARD_CLONE_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		// Retrieve the Award ID passed as an argument (if any)
		$AwardID = $Sender->Request->Get(AWARDS_PLUGIN_ARG_AWARDID, null);
		// Can't continue without an Award ID
		if(empty($AwardID)) {
			Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
		}

		// Load Award Data
		$AwardData = $this->AwardsModel()->GetAwardByID($AwardID)->FirstRow();
		if(empty($AwardData)) {
			$this->Log()->error(sprintf(T('Requested cloning of invalid Award ID: %d. Request by User %s (ID: %d).'),
																	$AwardID,
																	Gdn::Session()->User->Name,
																	Gdn::Session()->UserID));
			Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
		}

		$AwardData = $this->PrepareAwardDataForCloning($AwardData);
		// Set a flag that will inform the User that he is cloning an Award
		$Sender->SetData('Cloning', 1);

		// Pre-populate the form with data from the source Award
		$Sender->Form->SetData($AwardData);
		$Sender->SetData('RulesSettings', $this->GetRulesSettings($AwardData));

		/* Replace the destination URI with the one used to Add/Edit and Award. This
		 * will allow the Controller to handle the cloned Award as if it were a
		 * normal, new one.
		 */
		$Sender->Request->WithURI(AWARDS_PLUGIN_AWARD_ADDEDIT_URL);
		/* Remove the Award ID from the Request, so that the Add/Edit controller
		 * won't overwrite the source Award.
		 */
		$Sender->Request->SetValueOn(Gdn_Request::INPUT_GET, AWARDS_PLUGIN_ARG_AWARDID, null);
		$this->AwardAddEdit($Caller, $Sender);
	}

	/**
	 * Renders the page to Delete an Award.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardDelete(AwardsPlugin $Caller, $Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		$Sender->Form->SetModel($this->AwardsModel());

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Retrieve the Award ID passed as an argument
			$AwardID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDID, null);

			// Cannot proceed without an Award ID
			if(empty($AwardID)) {
				// Render Awards List page
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}

			// Load the data of the Award to be deleted, if an Award ID is passed
			$AwardData = $this->AwardsModel()->GetAwardByID($AwardID)->FirstRow(DATASET_TYPE_ARRAY);

			// Cannot proceed without a valid Award
			if(empty($AwardData)) {
				// Render Awards List page
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}


			//var_dump($AwardID, $AwardData);
			$Sender->Form->SetData($AwardData);

			// Apply the config settings to the form.
			$Sender->Render($Caller->GetView('awards_award_delete_confirm_view.php'));
		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			// The field named "OK" is actually the OK button. If it exists, it means
			// that the User confirmed the deletion.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Sender->Form->ButtonExists('OK')) {
				// Delete Award
				$this->AwardsModel()->Delete($Sender->Form->GetValue('AwardID'));
				$this->Log()->info(sprintf(T('User %s (ID: %d) deleted Award "%s" (ID: %d).'),
																		Gdn::Session()->User->Name,
																		Gdn::Session()->User->UserID,
																		GetValue('AwardName', $Data),
																		GetValue('AwardID', $Data)
																		));

				$Sender->InformMessage(T('Award deleted.'));
			}
			// Render Awards List page
			Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
		}
	}

	/**
	 * Renders the page to Assign an Award to one or more Users.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardAssign(AwardsPlugin $Caller, $Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		$Sender->Form->SetModel($this->AwardsModel());

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Retrieve the Award ID passed as an argument
			$AwardID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDID, null);

			// Cannot proceed without an Award ID
			if(empty($AwardID)) {
				// Render Awards List page
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}

			// Load the data of the Award to be deleted, if an Award ID is passed
			$AwardData = $this->AwardsModel()->GetAwardByID($AwardID)->FirstRow();

			// Cannot proceed without a valid Award
			if(empty($AwardData)) {
				// Render Awards List page
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}

			// Load jQuery UI
			$this->LoadJQueryUI($Sender);
			$this->LoadJQueryUIStyles($Sender);

			// Load auxiliary files
			$Sender->AddJsFile('award_assign.js', 'plugins/Awards/js');

			//var_dump($AwardID, $AwardData);
			$Sender->Form->SetData($AwardData);
			// The full AwardData object will be needed later to assign the Award
			$Sender->Form->SetValue('AwardDataJSON', json_encode($AwardData));

			// Add some definitions
			$Sender->AddDefinition('AwardID', $AwardID);
			$Sender->AddDefinition('View_Profile', T('View Profile'));
			$Sender->AddDefinition('Remove_User', T('Remove User'));
			$Sender->AddDefinition('User_Received_Award', T('Received the Award on '));
		}
		else {
			// If User Canceled, go back to the List
			if($Sender->Form->ButtonExists('Cancel')) {
				Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
			}

			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			// The field named "OK" is actually the OK button. If it exists, it means
			// that the User confirmed the deletion.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Sender->Form->ButtonExists('OK')) {
				$AwardData = json_decode(GetValue('AwardDataJSON', $Data));
				$UserIDsToAssign = array_filter(explode(',', GetValue('UserIDList', $Data)));

				// Make sure that at least one User is selected
				if(empty($UserIDsToAssign)) {
					$Sender->Form->AddError(T('You must select at least one User.'));
				}

				// If no errors are found, try to save the data
				if($Sender->Form->ErrorCount() <= 0) {
					$Requester = Gdn::Session()->User;
					$this->Log()->info(sprintf(T('Starting manual assignment of Awards to Users. ' .
																			 'Operation started by %s (ID: %d)...'),
																		 $Requester->Name,
																		 $Requester->UserID));
					Gdn::Database()->BeginTransaction();
					try {
						foreach($UserIDsToAssign as $UserID) {
							// Non-recurring Awards can be assigned only once
							if(!$AwardData->Recurring && $this->UserHasAward($UserID, $AwardData->AwardID)) {
								$this->Log()->info(sprintf(T('User %d already got the Award. Skipping.'),
																					 $UserID));
								continue;
							}

							// Assign the Award and log the Result
							$Saved = $this->AssignAward($UserID, $AwardData, BaseAwardRule::ASSIGN_ONE);

							if(!$Saved) {
								$ErrorMsg = sprintf(T('Could not assign Award "%s" to User ID: %d.') . ' ' . T('Operation aborted.'),
																		$AwardData->AwardName,
																		$UserID);
								$this->Log()->error($ErrorMsg);
								$Sender->Form->AddError($ErrorMsg);

								// At the first error, stop assigning the Award and abort the transaction
								break;
							}
						}

						// Use a transaction to either save ALL data successfully, or
						// none of it. This will prevent partial saves and reduce inconsistencies
						if($Saved) {
							Gdn::Database()->CommitTransaction();
						}
						else {
							Gdn::Database()->RollbackTransaction();
						}
					}
					catch(Exception $e) {
						Gdn::Database()->RollbackTransaction();
						$this->Log()->error($ErrorMsg = sprintf(T('Exception occurred while assigning Awards. ' .
																										'Award Name: %s. Error: %s. Backtrace: %s'),
																									$AwardData->AwardName,
																									$e->getMessage(),
																									$e->getTraceAsString()));
						throw $e;
					}

					if($Saved) {
						$Sender->InformMessage(T('Your changes have been saved.'));

						// Once changes have been saved, redirect to the main page
						Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
						//return $this->AwardsList($Caller, $Sender);
					}
					else {
						// If Saving failed for no apparent reason (no error messages),
						// suggest the User to have a look at the Log to see if there are
						// more details about the reason of the failure
						if($Sender->Form->ErrorCount() <= 0) {
							$Sender->Form->AddError(T('Data could not be saved. Please check the Log to see ' .
																				'more details about the issue.'));
						}
					}

				}
			}
		}
		// Render the page
		$Sender->Render($Caller->GetView('awards_award_assign_view.php'));
	}

	
	protected function ServeExportFile(Gdn_Controller $Sender, $FileName) {
		$BaseName = basename($FileName);
		$FileName = AWARDS_PLUGIN_EXPORT_PATH . '/' . $BaseName;
		if(!file_exists($FileName)) {
			$ErrorMsg = sprintf(T('Invalid export file requested: "%s".'),
													$FileName);
			$this->Log()->error($ErrorMsg);
			throw new InvalidArgumentException($ErrorMsg);
		}


		$this->Log()->info(sprintf(T('Serving Export file "%s"...'),
															 $FileName));
		header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=' . $BaseName);
    header('Content-Length: ' . filesize($FileName));
		readfile($FileName);
		$this->Log()->info(T('Operation completed.'));
	}

	/**
	 * Renders the page to export Awards and Awards Classes.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function Export(AwardsPlugin $Caller, $Sender) {
		$FileName = Gdn::Request()->Filename();
		if(!empty($FileName) && ($FileName !== 'default')) {
			$this->ServeExportFile($Sender, $FileName);
		}

		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_EXPORT_URL);
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Just Load auxiliary files
			$Sender->AddJsFile('awards_export.js', 'plugins/Awards/js');

		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Sender->Form->ButtonExists('Export')) {
				// Export data
				$AwardsExporter = new AwardsExporter();

				$ExportSettings = $Sender->Form->FormValues();
				$ExportResult = $AwardsExporter->ExportData($ExportSettings);
				$Sender->SetData('ExportResult', $ExportResult);
				$Sender->SetData('ZipFileName', basename($AwardsExporter->GetZipFileName()));
				$Sender->SetData('ExportMessages', $AwardsExporter->GetMessages());

			}
		}
		// Render the page
		$Sender->Render($Caller->GetView('awards_export_view.php'));
	}

	/**
	 * Validates the parameters received for the Import.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 * @param string Output. The file that will have to be imported.
	 * return int An integer value indicating the result of the validation.
	 */
	private function _ValidateImport(Gdn_Controller $Sender, &$FileToImport) {
		$Upload = new Gdn_Upload();
		$TmpUploadedFile = $Upload->ValidateUpload('FileToImport', false);

		if(!$TmpUploadedFile) {
			$Sender->Form->AddError(T('No file was uploaded. Please select and upload the file ' .
																'to import.'));
			return AWARDS_ERR_INVALID_FILE_TO_IMPORT;
		}

		try {
			$FileName = $_FILES['FileToImport']['name'];

			// Create directory for Import, if it doesn't exist
			$ImportPath = realpath(PATH_UPLOADS . '/' . AWARDS_PLUGIN_IMPORT_PATH);
			if(!is_dir($ImportPath)) {
				if(!mkdir($ImportPath, 0775)) {
					$ErrorMsg = sprintf(T('Could not create import directory "%s". ' .
																				'Please create it manually and make it writable.'),
															$ImportPath);
					$this->Log()->error($ErrorMsg);
					throw new Exception($ErrorMsg);
				}
			}

			$DestinationFile = AWARDS_PLUGIN_IMPORT_PATH . '/' . $FileName;
			//var_dump($DestinationFile);die();
			$FileInfo = $Upload->SaveAs($TmpUploadedFile, $DestinationFile);
			$FileToImport = PATH_UPLOADS . '/' . $FileInfo['Name'];
			//var_dump($FileToImport);die();
		}
		catch(Exception $e) {
			$ErrorMsg = $e->getMessage();
			$this->Log()->error($ErrorMsg);
			$Sender->Form->AddError($ErrorMsg);
			return AWARDS_ERR_EXCEPTION_OCCURRED;
		}

		return AWARDS_OK;
	}

	/**
	 * Renders the page to import Awards and Awards Classes.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function Import(AwardsPlugin $Caller, $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_IMPORT_URL);
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Just Load auxiliary files
			$Sender->AddJsFile('awards_import.js', 'plugins/Awards/js');

		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && ($Sender->Form->ButtonExists('Import') || $Sender->Form->ButtonExists('TestImport'))) {
				// Export data
				$ImportSettings = $Sender->Form->FormValues();

				$FileToImport = '';
				if($this->_ValidateImport($Sender, $FileToImport) === AWARDS_OK) {
					//$ImportSettings['FileName'] = 'C:\Users\d.zanella\Documents\Projects\Web\personal\Vanilla Forums\Plugins\AwardsPlugin\Awards\export\vanilla_awards_20130420001210.zip';
					$ImportSettings['FileName'] = $FileToImport;

					$ImportResult = $this->AwardsImporter()->ImportData($ImportSettings);
					$Sender->SetData('ImportResult', $ImportResult);
					$Sender->SetData('ImportMessages', $this->AwardsImporter()->GetMessages());

					// Fire ConfigChanged event to regenerate CSS file for Awards Classes
					if($ImportResult == AWARDS_OK) {
						$Caller->FireEvent('ConfigChanged');
					}

				}
			}
		}

		// Load Award Classes
		$Sender->SetData('AwardClasses', $this->GetAwardClasses());
		$Sender->SetData('DuplicateItemActions', $this->AwardsImporter()->DuplicateItemActions());

		// Render the page
		$Sender->Render($Caller->GetView('awards_import_view.php'));
	}

	/**
	 * Loads and configures the Recent Award Recipients module, which will display
	 * a list of the last Users who earned an Award.
	 *
 	 * @param Controller Sender Sending controller instance.
 	 * @return RecentAwardRecipientsModule An instance of the module.
 	 */
	private function LoadRecentAwardRecipientsModule($Sender, $AwardID) {
		$RecentAwardRecipientsModule = new RecentAwardRecipientsModule($Sender);
		$RecentAwardRecipientsModule->LoadData($AwardID);
		return $RecentAwardRecipientsModule;
	}

	/**
	 * Renders the page displaying the details of an Award and the list of the
	 * Users who already earned it.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardInfo(AwardsPlugin $Caller, $Sender) {
		$this->RemoveDashboardElements($Sender);
		// Add a class to help uniquely identifying this page
		$Sender->CssClass = 'AwardInfo';

		// Load Award Data
		$AwardID = GetValue(1, $Sender->RequestArgs);
		if(!empty($AwardID)) {
			//$AwardData = $this->AwardsModel()->GetAwardByID($AwardID)->FirstRow();
			$AwardData = $this->AwardsModel()
												->GetWithTimesAwarded(array('VAAL.AwardID' => $AwardID),
																							array('VAAL.AwardName asc'))
												->FirstRow();
			$Sender->SetData('AwardData', $AwardData);
			$Sender->SetData('RecentAwardRecipientsModule', $this->LoadRecentAwardRecipientsModule($Sender, $AwardID));
		}

		// Load details of Award as earned by the User
		if(!empty($AwardData) && Gdn::Session()->IsValid()) {
			$UserAwardData = $this->UserAwardsModel()->GetUserAwardData(Gdn::Session()->UserID, $AwardID);
			$Sender->SetData('UserAwardData', $UserAwardData);
		}

		// Retrieve the View to display the Award details
		$Sender->Render($Caller->GetView('awards_award_info_view.php'));
	}

	/**
	 * Renders the page displaying the list of all available Awards and Award
	 * Classes.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardsPage(AwardsPlugin $Caller, $Sender) {
		$this->RemoveDashboardElements($Sender);
		// Add a class to help uniquely identifying this page
		$Sender->CssClass = 'AwardsFrontendList';

		// Display only enabled Awards
		$Wheres = array('VAAL.AwardIsEnabled' => 1);

		// Prepare the Award Class filter, if needed
		$AwardClassID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDCLASSID);
		if(!empty($AwardClassID)) {
			$Wheres['VAAL.AwardClassID'] = $AwardClassID;
			$Sender->SetData('AwardClassID', $AwardClassID);
		}

		// Load Awards Data
		$AwardsData = $this->AwardsModel()->GetWithTimesAwarded($Wheres, array('VAAL.AwardName asc'));
		$Sender->SetData('AwardsData', $AwardsData);

		// Load the Awards earned by the User
		$UserAwardData = array();
		if(Gdn::Session()->IsValid()) {
			$UserAwardsDataSet = $this->UserAwardsModel()->GetForUser(Gdn::Session()->UserID)->Result();

			// Re-key the resulting dataset, so that the AwardID is the key for the
			// User Award data
			if(!empty($UserAwardsDataSet)) {
				foreach($UserAwardsDataSet as $UserAward) {
					$UserAwardData[$UserAward->AwardID] = $UserAward;
				}
			}
		}
		$Sender->SetData('UserAwardData', $UserAwardData);

		// Retrieve the View to display the Awards
		$Sender->Render($Caller->GetView('awards_awardspage_view.php'));
	}

	/**
	 * Enables or disables an Award.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardEnable(AwardsPlugin $Caller, $Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		$AwardID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDID, null);
		$EnableFlag = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_ENABLEFLAG, null);

		if(is_numeric($AwardID) && is_numeric($EnableFlag)) {
			if($this->AwardsModel()->EnableAward((int)$AwardID, (int)$EnableFlag)) {
				$Sender->InformMessage(T('Your changes have been saved.'));
			};
		}

		// Render Awards List page
		Redirect(AWARDS_PLUGIN_AWARDS_LIST_URL);
	}

	/**
	 * Process the Award Rules for current User.
	 *
	 * @param AwardsPlugin Caller The Plugin who called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function ProcessAwards(AwardsPlugin $Caller, Gdn_Controller $Sender) {
		// Can't process the Awards if no User is logged in
		if(!Gdn::Session()->IsValid()) {
			return;
		}

		// Retrieve ID of logged in User
		$UserID = Gdn::Session()->UserID;

		// Retrieve the list of Awards still available to the User
		$AvailableAwardsDataSet = $this->AwardsModel()->GetAvailableAwards($UserID);

		// Debug - Rules to process
		//var_dump($AvailableAwardsDataSet->Result());

		foreach($AvailableAwardsDataSet->Result() as $AwardData) {
			$this->Log()->debug(sprintf(T('Processing Award "%s"...'), $AwardData->AwardName));
			//var_dump($AwardData->AwardName);

			/* Retrieve the settings to be passed to the Rules to determine if the
			 * Award should be assigned
			 */
			$RulesSettings = $this->GetRulesSettings($AwardData);

			$AwardAssignmentCount = $Caller->RulesManager()->ProcessRules($UserID, $RulesSettings);
			$this->Log()->debug(sprintf(T('Assigning Award %d time(s).'), $AwardAssignmentCount));

			//var_dump($AwardData, $AwardAssignmentCount);

			// Assign Award to User, if needed
			if($AwardAssignmentCount > 0) {
				$this->AssignAward($UserID, $AwardData, $AwardAssignmentCount);
			}
		}
	}

	/**
	 * Checks if a User already got an Award.
	 *
	 * @param int UserID The User ID of the Award recipient.
	 * @param int AwardID The ID of the Award.
	 * @return bool True if the User already got the Award, False otherwise.
	 */
	protected function UserHasAward($UserID, $AwardID) {
		return ($this->UserAwardsModel()->GetUserAwardData($UserID, $AwardID) !== false);
	}

	/**
	 * Assigns an Award to a User and updates the Activity log to notify him.
	 *
	 * @param int UserID The User ID of the Award recipient.
	 * @param stdClass AwardData An object containing Award data.
	 * @param int AwardAssignmentCount The amount of times that the Award should be
	 * assigned. It can be more than one for recurring Awards.
	 * @return mixed The ID User Award record, or false on failure.
	 */
	protected function AssignAward($UserID, stdClass $AwardData, $AwardAssignmentCount) {
		$UserAwardFields = array(
			'UserID' => $UserID,
			'AwardID' => $AwardData->AwardID,
			'AwardedRankPoints' => $AwardData->RankPoints + $AwardData->AwardClassRankPoints,
			'TimesAwarded' => $AwardAssignmentCount,
			'Status' => AwardsModel::STATUS_ASSIGNED,
		);

		//var_dump("Assigning Award", $AwardAssignmentCount, $AwardData);

		$UserAwardID = $this->UserAwardsModel()->Save($UserAwardFields);
		if($UserAwardID !== false) {
			$this->Log()->debug(T('Adding Award assignment Activity...'));
			// Log the fact that Award has been assigned to the User
			$ActivityLogResult = AddActivity($UserID,
																			 AwardsPlugin::ACTIVITY_AWARDEARNED,
																			 $AwardData->AwardDescription,
																			 $UserID,
																			 // Instead of a Route, save only the Award ID. It will be used to
																			 // join with UserAwards table to retrieve additional information
																			 // when displaying the Activity
																			 $AwardData->AwardID);

			if($ActivityLogResult === false) {
				$this->Log()->error(T('Activity was not saved correctly.'));
			}
			else {
				$this->Log()->debug(T('Done.'));
			}
		}

		return $UserAwardID;
	}
}
