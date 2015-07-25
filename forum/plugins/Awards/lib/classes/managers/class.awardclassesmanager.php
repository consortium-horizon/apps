<?php if(!defined('APPLICATION')) exit();


class AwardClassesManager extends BaseManager {
	/**
	 * Returns an instance of AwardClassesModel.
	 *
	 * @return AwardClassesModel An instance of AwardClassesModel.
	 * @see BaseManager::GetInstance()
	 */
	private function AwardClassesModel() {
		return $this->GetInstance('AwardClassesModel');
	}
	/**
	 * Class constructor.
	 *
	 * @return AwardClassesManager
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Prepares some Award Class Data to be used for cloning an Award Class. This
	 * method removes or alters all data that identifies an Award, so that the
	 * User will be forced to enter different details for the clone.
	 *
	 * @param stdClass Award Class Data An object containing Award Class data.
	 * @return stdClass The processed Award Class Data object.
	 */
	private function PrepareAwardClassDataForCloning(stdClass $AwardClassData) {
		//var_dump($AwardClassData);die();
		// Save references to source Award
		$AwardClassData->SourceAwardClassID = $AwardClassData->AwardClassID;
		$AwardClassData->SourceAwardClassName = $AwardClassData->AwardClassName;
		$AwardClassData->SourceAwardClassDescription = $AwardClassData->AwardClassDescription;

		// Unset and alter AwardClass key data, as clone will have to use its own
		unset($AwardClassData->AwardClassID);
		unset($AwardClassData->DateInserted);
		unset($AwardClassData->DateUpdated);

		$AwardClassData->AwardClassName = T('CLONE-') . $AwardClassData->AwardClassName;
		$AwardClassData->AwardClassDescription = T('CLONE-') . $AwardClassData->AwardClassDescription ;
		return $AwardClassData;
	}

	/**
	 * Transforms an Award Class Name into a valid CSS Class.
	 *
	 * @param string AwardClassName The Award Class Name to process.
	 * @return string A valid CSS Class, containing only alphanumeric characters,
	 * hyphens and underscores.
	 */
	private function TransformIntoCSSClass($AwardClassName) {
		$Result = preg_replace('/[^a-zA-Z0-9\s\-_]+/', '', trim($AwardClassName));
		$Result = str_replace(' ', '-', $Result);
		return $Result;
	}

	/**
	 * Renders the AwardClasses List page.
	 *
	 * @param AwardsPlugin Caller The Plugin who called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardClassesList(AwardsPlugin $Caller, Gdn_Controller $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		
		$AwardClassesDataSet = $this->AwardClassesModel()->Get();
		

		$Sender->SetData('AwardClassesDataSet', $AwardClassesDataSet);

		$Sender->Render($Caller->GetView('awards_awardclasseslist_view.php'));
	}

	/**
	 * Renders the page to Add/Edit an Award Class.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardClassAddEdit(AwardsPlugin $Caller, Gdn_Controller $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARDCLASS_ADDEDIT_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		// Load jQuery UI
		$this->LoadJQueryUI($Sender);

		$Sender->AddCssFile('jqueryFileTree.css', 'plugins/AeliaFoundationClasses/js/jqueryFileTree');
		$Sender->AddJsFile('jqueryFileTree.js', 'plugins/AeliaFoundationClasses/js/jqueryFileTree');

		$Sender->AddJsFile('image_preview.js', 'plugins/Awards/js');
		$Sender->AddJsFile('image_preview_ie.js', 'plugins/Awards/js', array('IE' => 'gte IE 8'));

		// Load auxiliary files
		$Sender->AddJsFile('awardclass_edit.js', 'plugins/Awards/js');

		// Retrieve the Award Class ID passed as an argument (if any)
		$AwardClassID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDCLASSID, null);

		// Set Award Class Data in the form
		$Sender->Form->SetModel($this->AwardClassesModel());
		// Display inline errors
		$Sender->Form->ShowErrors();

		if(!empty($AwardClassID)) {
			$AwardClassData = $this->AwardClassesModel()->GetAwardClassByID($AwardClassID)->FirstRow();
			//var_dump($AwardClassData);
			$Sender->Form->SetData($AwardClassData);
		}

		// If seeing the form for the first time...
		if($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Just show the form with the default values
		}
		else {
			$Data = $Sender->Form->FormValues();

			// If User Canceled, go back to the List
			if(GetValue('Cancel', $Data, false)) {
				Redirect(AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
			}

			// Validate PostBack
			// The field named "Save" is actually the Save button. If it exists, it means
			// that the User chose to save the changes.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['Save']) {
				try {
					// Retrieve the URL of the Picture associated with the Award.
					$ImageFile = PictureManager::GetPictureURL(AWARDS_PLUGIN_AWARDCLASSES_PICS_PATH,
																										 'Picture',
																										 $Sender->Form->GetFormValue('AwardClassImageFile'));
					// Add the Picture URL to the Form
					$Sender->Form->SetFormValue('AwardClassImageFile', $ImageFile);
				}
				catch(Exception $e) {
					$Sender->Form->AddError($e->getMessage());
				}

				Gdn::Database()->BeginTransaction();
				try{
					// If a CSS Class was not specified for the Award Class, generate one
					// from its name
					$AwardClassCSSClass = $Sender->Form->GetValue('AwardClassCSSClass');
					if(empty($AwardClassCSSClass)) {
						$AwardClassCSSClass = $this->TransformIntoCSSClass($Sender->Form->GetValue('AwardClassName'));
						$Sender->Form->SetFormValue('AwardClassCSSClass', $AwardClassCSSClass);
					}

					// Save AwardClasses settings
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
					$this->Log()->error($ErrorMsg = sprintf(T('Exception occurred while saving Award Class. ' .
																									'Award Class Name: %s. Error: %s.'),
																								$Sender->Form->GetFormValue('AwardClassName'),
																								$e->getMessage()));
					throw $e;
				}

				if($Saved) {
					$Sender->InformMessage(T('Your changes have been saved.'));
					$Caller->FireEvent('ConfigChanged');

					// Once changes have been saved, redirect to the main page
					//Redirect(AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
					$this->AwardClassesList($Caller, $Sender);
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

		// Retrieve the View that will be used to configure the Award Class
		$Sender->Render($Caller->GetView('awards_awardclass_addedit_view.php'));
	}

	/**
	 * Renders the page to Clone an Award Class.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardClassClone(AwardsPlugin $Caller, $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_AWARDCLASS_CLONE_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		// Retrieve the Award ID passed as an argument (if any)
		$AwardClassID = $Sender->Request->Get(AWARDS_PLUGIN_ARG_AWARDCLASSID, null);
		// Can't continue without an Award Class ID
		if(empty($AwardClassID)) {
			Redirect(AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
		}

		// Load Award Class Data
		$AwardClassData = $this->AwardClassesModel()->GetAwardClassByID($AwardClassID)->FirstRow();
		if(empty($AwardClassData)) {
			$this->Log()->error(sprintf(T('Requested cloning of invalid Award Class ID: %d. Request by User %s (ID: %d).'),
																	$AwardClassID,
																	Gdn::Session()->User->Name,
																	Gdn::Session()->UserID));
			Redirect(AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
		}

		$AwardClassData = $this->PrepareAwardClassDataForCloning($AwardClassData);
		// Set a flag that will inform the User that he is cloning an Award
		$Sender->SetData('Cloning', 1);

		$Sender->Form->SetData($AwardClassData);

		/* Replace the destination URI with the one used to Add/Edit and Award Class.
		 * This will allow the Controller to handle the cloned Award as if it were
		 * a normal, new one.
		 */
		$Sender->Request->WithURI(AWARDS_PLUGIN_AWARDCLASS_ADDEDIT_URL);
		/* Remove the Award Class ID from the Request, so that the Add/Edit controller
		 * won't overwrite the source Award Class.
		 */
		$Sender->Request->SetValueOn(Gdn_Request::INPUT_GET, AWARDS_PLUGIN_ARG_AWARDCLASSID, null);
		$this->AwardClassAddEdit($Caller, $Sender);
	}

	/**
	 * Renders the page to Delete an Award Class.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardClassDelete(AwardsPlugin $Caller, Gdn_Controller $Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Awards.Manage');

		$Sender->Form->SetModel($this->AwardClassesModel());

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Retrieve the Award Class ID passed as an argument (if any)
			$AwardClassID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDCLASSID, null);

			// Load the data of the Award Class to be edited, if an Award Class ID
			$AwardClassData = $this->AwardClassesModel()->GetAwardClassByID($AwardClassID)->FirstRow(DATASET_TYPE_ARRAY);

			// If Class is in use, prevent its deletion
			if(GetValue('TotalAwardsUsingClass', $AwardClassData) > 0) {
				$Sender->Form->AddError(sprintf(T('Award Class "%s" cannot be deleted because there are still Awards using it.'),
																				GetValue('AwardClassName', $AwardClassData)));
				$this->AwardClassesList($Caller, $Sender);
			}

			//var_dump($AwardClassID, $AwardClassData);
			$Sender->Form->SetData($AwardClassData);

			// Apply the config settings to the form.
			$Sender->Render($Caller->GetView('awards_awardclass_delete_confirm_view.php'));
		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			// The field named "OK" is actually the OK button. If it exists, it means
			// that the User confirmed the deletion.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Sender->Form->ButtonExists('OK')) {
				// Delete Award Class
				$this->AwardClassesModel()->Delete($Sender->Form->GetValue('AwardClassID'));
				$this->Log()->info(sprintf(T('User %s (ID: %d) deleted Award "%s" (ID: %d).'),
																		Gdn::Session()->User->Name,
																		Gdn::Session()->User->UserID,
																		GetValue('AwardClassName', $Data),
																		GetValue('AwardClassID', $Data)
																		));

				$Sender->InformMessage(T('Award Class deleted.'));
				$Caller->FireEvent('ConfigChanged');
			}
			// Render AwardClasses List page
			Redirect(AWARDS_PLUGIN_AWARDCLASSES_LIST_URL);
		}
	}

	/**
	 * Generates the CSS file containing the styles for the configured Award
	 * classes. If file already exists, it's overwritten.
	 *
	 * @param Gdn_Pluggable Sender Sending controller instance.
	 */
	public function GenerateAwardClassesCSS(Gdn_Pluggable $Sender) {
		$AwardClassesDataSet = $this->AwardClassesModel()->Get();

		// Prepare the notice to put at the beginning of the generated CSS file
		$CSSEntries = array(T("/**\n" .
													"* This file contains all the CSS Classes related derived from Award Classes and\n" .
													"* it's generated automatically by Awards Plugin. Don't change it manually,\n" .
													"* all changes will be overwritten by the Plugin.\n" .
													"*/\n"));

		// Add CSS for each Class
		foreach($AwardClassesDataSet as $AwardClassData) {
			// Use the Award Class Name as a CSS Class

			// Generate class for Award Images when they are displayed in the Activity
			// page. Such page doesn't allow to assign a specific CSS Class to the images,
			// therefore we have to use a trick to assign the propers styles to them, by
			// assigning a special class to their container.
			$CSSDeclaration = '.Activities .AwardActivity.' . $AwardClassData->AwardClassCSSClass . ' a.Photo img' . ",\n";
			// Generate class for Award Images. It's assigned to every award image in most places
			$CSSDeclaration .= 'img.' . $AwardClassData->AwardClassCSSClass . " {\n";

			// Add the background image using the uploaded image
			if(!empty($AwardClassData->AwardClassImageFile)) {
				$CSSDeclaration .= "background: url(\"" . Url($AwardClassData->AwardClassImageFile) . "\") top left no-repeat;\n";
			}
			// Add the rest of the CSS
			$CSSDeclaration .= $AwardClassData->AwardClassCSS . "\n}\n";

			$CSSEntries[] = $CSSDeclaration;
		}

		if(!$this->WriteToFile(AWARDS_PLUGIN_AWARDCLASSES_CSS_FILE, implode("\n", $CSSEntries))) {
			$this->Log()->error(T('Award Classes CSS file could not be updated. Please save an ' .
														'Award Class again to regenerate it.'));
		}
	}

	/**
	 * Renders some links that will allow to filter the view by Award Class.
	 *
	 * @param string PageURL The URL of the Page where the filters are applied.
	 * @param int CurrentAwardClassID The ID of the currently selected Award Class.
	 * If empty, the view is considered unfiltered.
	 */
	public static function RenderAwardClassFilters($PageURL, $CurrentAwardClassID, array $OrderBy = array('RankPoints desc')) {
		// Retrieve Award Classes
		$AwardClassesModel = new AwardClassesModel();
		$AwardClassesData = $AwardClassesModel->GetWhere(array(), $OrderBy);

		if(empty($AwardClassesData)) {
			return '';
		}

		echo '<div class="Filters Tabs">';
		echo '<ol id="ClassFilters">';
		$CssClass = empty($CurrentAwardClassID) ? 'Active' : '';
		echo Wrap(Anchor(T('All'),
										 $PageURL),
							'li',
							array('class' => 'FilterItem ' . $CssClass));

		// Render a filter for each Award Class
		foreach($AwardClassesData as $UserAwardClass) {
			$CssClass = ($CurrentAwardClassID === $UserAwardClass->AwardClassID) ? 'Active' : '';
			$FilterAnchor = Anchor($UserAwardClass->AwardClassName,
														 $PageURL . '?' . AWARDS_PLUGIN_ARG_AWARDCLASSID . '=' . $UserAwardClass->AwardClassID);
			echo Wrap($FilterAnchor,
								'li',
								array('class' => 'FilterItem ' . $CssClass));
		}
		echo '</ol>';
		echo '</div>';
	}
}
