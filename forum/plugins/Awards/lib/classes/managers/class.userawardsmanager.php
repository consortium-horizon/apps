<?php if(!defined('APPLICATION')) exit();


/**
 * Controller for all operations regarding Awards earned by the Users.
 * This class covers all User centric operations, i.e. the ones against an
 * Award as it was earned by one or more Users. Operations on Awards and
 * Award Classes definitions are handled by AwardsManager class.
 *
 * @see AwardsManager.
 */
class UserAwardsManager extends BaseManager {
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
	 * Class constructor.
	 *
	 * @return AwardsManager
	 */
	public function __construct() {
		parent::__construct();
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
	 * Displays a User's Score calculated from the Awards he received. This function
	 * is used only when the Ranks plugin is not available.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 * @param int UserID The ID of the User.
	 */
	public function DisplayUserAwardsScore(AwardsPlugin $Caller, $Sender, $UserID) {
		$UserScore = $this->UserAwardsModel()->GetUserAwardsScore($UserID);

		echo '<div class="UserAwardsScore">';
		echo Wrap(sprintf(T('%d Points'),
											$UserScore));
		echo '</div>';
	}

	/**
	 * Renders the page displaying the list of all available Awards and Award
	 * Classes.
	 *
	 * @param AwardsPlugin Caller The Plugin which called the method.
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function AwardsLeaderboard(AwardsPlugin $Caller, $Sender) {
		$this->RemoveDashboardElements($Sender);
		// Add a class to help uniquely identifying this page
		$Sender->CssClass = 'AwardsLeaderboard';

		$Wheres = array();

		// Prepare the Award Class filter, if needed
		$AwardClassID = $Sender->Request->GetValue(AWARDS_PLUGIN_ARG_AWARDCLASSID);
		if(!empty($AwardClassID)) {
			$Wheres['VAUAL.AwardClassID'] = $AwardClassID;
			$Sender->SetData('AwardClassID', $AwardClassID);
		}

		// Load Awards Data
		$UserAwardsData = $this->UserAwardsModel()->GetTopUsers($Wheres);
		$Sender->SetData('UserAwardsData', $UserAwardsData);

		//var_dump($UserAwardsData->ResultArray(), $Wheres);

		// Load Award Classes data
		$AwardClassesData = $this->AwardClassesModel()->GetWhere(array(), array('AwardClassName asc'));
		$Sender->SetData('AwardClassesData', $AwardClassesData);

		// Retrieve the View to display the Awards
		$Sender->Render($Caller->GetView('awards_awardsleaderboard_view.php'));
	}
}
