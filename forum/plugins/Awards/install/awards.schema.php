<?php if(!defined('APPLICATION')) exit();


require('plugin.schema.php');

class AwardsSchema extends PluginSchema {
	/**
	 * Create the table which will store the list of configured Award Classes.
	 */
	protected function create_awardclasses_table() {
		Gdn::Structure()
			->Table('AwardClasses')
			->PrimaryKey('AwardClassID')
			->Column('AwardClassName', 'varchar(100)', false, 'unique')
			->Column('AwardClassDescription', 'text')
			->Column('AwardClassImageFile', 'text', true)
			->Column('AwardClassCSSClass', 'varchar(100)', false)
			->Column('AwardClassCSS', 'text', true)
			->Column('RankPoints', 'uint', 0)
			->Column('DateInserted', 'datetime', false)
			->Column('InsertUserID', 'int', true)
			->Column('DateUpdated', 'datetime', true)
			->Column('UpdateUserID', 'int', true)
			->Set(false, false);
	}

	/**
	 * Create the table which will store the list of configured Awards.
	 */
	protected function create_awards_table() {
		Gdn::Structure()
			->Table('Awards')
			->PrimaryKey('AwardID')
			->Column('AwardClassID', 'int', false)
			->Column('AwardName', 'varchar(100)', false, 'unique')
			->Column('AwardDescription', 'text')
			// Field "Recurring" indicates if an Award could be assigned multiple
			// times. The value of this field will be determined by inspecting the
			// Rules for the assignment of the Award. If rules contains at least one
			// recurring criterion (e.g. "every X posts") the Award will be a
			// Recurring one.
			//
			// Examples
			// - Award 1 contains rule "Assign Award X when User votes for the first
			//   time". This is NOT a recurring Award, as User can vote for the first
			//   time only once.
			// - Award 2 contains rule "Assign Award Y for every year of subscription".
			//   This is a recurring Award, as User would get it every year. For
			//   such reason, the rules must be processed every time, even if the Award
			//   was already assigned.
			->Column('Recurring', 'uint', 0, 'index')
			->Column('RulesSettings', 'text')
			->Column('AwardIsEnabled', 'uint', 1, 'index')
			->Column('AwardImageFile', 'text')
			->Column('RankPoints', 'uint', 0)
			->Column('DateInserted', 'datetime', false)
			->Column('InsertUserID', 'int', true)
			->Column('DateUpdated', 'datetime', true)
			->Column('UpdateUserID', 'int', true)
			->Set(false, false);

		$this->AddForeignKey('Awards', 'FK_Awards_AwardClasses', array('AwardClassID'),
												'AwardClasses', array('AwardClassID'));
	}

	/**
	 * Create the table which will store the association between Users and their
	 * Awards.
	 */
	protected function create_userawards_table() {
		Gdn::Structure()
			->Table('UserAwards')
			->PrimaryKey('UserAwardID')
			// Fields UserID and AwardID should be indexed. This will be done during
			// the creation of Foreign Keys on such fields
			->Column('UserID', 'int', false)
			->Column('AwardID', 'int', false)
			->Column('AwardedRankPoints', 'uint', 0)
			->Column('TimesAwarded', 'uint', 0)
			->Column('Status', 'uint', 0)
			->Column('DateInserted', 'datetime', false)
			->Column('InsertUserID', 'int', true)
			->Column('DateUpdated', 'datetime', true)
			->Column('UpdateUserID', 'int', true)
			->Set(false, false);

		$this->AddForeignKey('UserAwards', 'FK_UserAwards_User', array('UserID'),
												'User', array('UserID'));
		$this->AddForeignKey('UserAwards', 'FK_UserAwards_Awards', array('AwardID'),
												'Awards', array('AwardID'), 'CASCADE');
		$this->CreateIndex('UserAwards', 'IX_DateInserted', array('`DateInserted` DESC'));
	}

	/**
	 * Creates a View that returns a list of the configured Awards.
	 */
	protected function create_awardslist_view() {
		$Px = $this->Px;
		$Sql = "
		SELECT
			A.AwardID
			,A.AwardClassID
			,A.AwardName
			,A.AwardDescription
			,A.Recurring
			,A.AwardIsEnabled
			,A.AwardImageFile
			,A.RankPoints
			,A.DateInserted
			,A.DateUpdated
			,A.RulesSettings
			,AC.AwardClassName
			,AC.AwardClassCSSClass
			,AC.AwardClassImageFile
			,AC.RankPoints AS AwardClassRankPoints
		FROM
			{$Px}Awards A
			JOIN
			{$Px}AwardClasses AC ON
				(AC.AwardClassID = A.AwardClassID)
		";
		$this->Construct->View('v_awards_awardslist', $Sql);
	}

	/**
	 * Creates a View that returns a list of the configured Award Classes.
	 */
	protected function create_awardclasseslist_view() {
		$Px = $this->Px;
		$Sql = "
		SELECT
			AC.AwardClassID
			,AC.AwardClassName
			,AC.AwardClassDescription
			,AC.AwardClassImageFile
			,AC.AwardClassCSSClass
			,AC.AwardClassCSS
			,AC.RankPoints
			,AC.DateInserted
			,AC.DateUpdated
			,COUNT(A.AwardID) AS TotalAwardsUsingClass
		FROM
			{$Px}AwardClasses AC
			LEFT JOIN
			{$Px}Awards A ON
				(A.AwardClassID = AC.AwardClassID)
		GROUP BY
			AC.AwardClassID
			,AC.AwardClassName
			,AC.AwardClassDescription
			,AC.AwardClassImageFile
			,AC.AwardClassCSSClass
			,AC.AwardClassCSS
			,AC.RankPoints
			,AC.DateInserted
			,AC.DateUpdated
		";
		$this->Construct->View('v_awards_awardclasseslist', $Sql);
	}

	/**
	 * Creates a View that returns a list of the configured Awards.
	 */
	protected function create_userawardslist_view() {
		$Px = $this->Px;
		$Sql = "
			SELECT
				UA.UserID
				,UA.UserAwardID
				,UA.DateInserted AS DateAwarded
				,UA.AwardedRankPoints
				,UA.TimesAwarded
				,UA.Status
				,A.AwardID
				,A.AwardName
				,A.AwardDescription
				,A.Recurring
				,A.AwardIsEnabled
				,A.AwardImageFile
				,A.RankPoints
				,A.DateInserted
				,A.DateUpdated
				,AC.AwardClassID
				,AC.AwardClassName
				,AC.AwardClassCSSClass
				,AC.AwardClassImageFile
				,AC.RankPoints AS AwardClassRankPoints
			FROM
				{$Px}UserAwards UA
				JOIN
				{$Px}Awards A ON
					(A.AwardID = UA.AwardID)
				JOIN
				{$Px}AwardClasses AC ON
					(AC.AwardClassID = A.AwardClassID)
		";
		$this->Construct->View('v_awards_userawardslist', $Sql);
	}

	/**
	 * Create all the Database Objects in the appropriate order.
	 */
	protected function CreateObjects() {
		$this->create_awardclasses_table();
		$this->create_awards_table();
		$this->create_userawards_table();

		$this->create_awardslist_view();
		$this->create_awardclasseslist_view();
		$this->create_userawardslist_view();
	}

	/**
	 * Delete the Database Objects.
	 */
	protected function DropObjects() {
		$this->DropView('v_awards_userawardslist');
		$this->DropView('v_awards_awardclasseslist');
		$this->DropView('v_awards_awardlist');

		$this->DropTable('UserAwards');
		$this->DropTable('Awards');
		$this->DropTable('AwardClasses');
	}
}
