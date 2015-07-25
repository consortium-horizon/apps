<?php


namespace Aelia;
if (!defined('APPLICATION')) exit();

use ThauEx\SimpleHtmlDom\SHD;

/**
 * Base Form class. It extends standard Gdn_Form class by adding convenience
 * methods to manage Forms.
 */
class Form extends \Gdn_Form {
	// @var Logger The Logger used by the class.
	private $_Log;

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = \LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}

	/**
	 * Returns a boolean value indicating if the current page has an
	 * authenticated postback. It validates the postback by looking at a
	 * transient value that was rendered using $this->Open() and submitted with
	 * the form. This method an also just validate the Transient Key, which makes
	 * it useful to validate forms posted by anonymous (non logged in) users.
	 *
	 * @param bool ValidateUser Indicates if the method should validate the User
	 * as well (i.e. it check will fail if ser is not logged in).
	 * @return bool
	 * @link http://en.wikipedia.org/wiki/Cross-site_request_forgery.
	 */
	public function AuthenticatedPostBack($ValidateUser = true) {
		$KeyName = $this->InputPrefix . '/TransientKey';
		$PostBackKey = isset($_POST[$KeyName]) ? $_POST[$KeyName] : FALSE;
		$Session = \Gdn::Session();
		return $Session->ValidateTransientKey($PostBackKey, $ValidateUser);
	}

	/**
	 * If a field has to become a grouped field, it parses its HTML and changes
	 * its name accordingly.
	 *
	 * @param string FieldHTML The HTML for the field to be processed.
	 * @param bool GroupField A flag that indicates if the field should become a
	 * grouped field.
	 * @return string The processed HTML for the field.
	 */
	protected function ProcessFieldGrouping($FieldHTML, $GroupField) {
		if($GroupField != false) {
			$FieldHTML = $this->RenameFieldToCreateGroups($FieldHTML);
		}
		return $FieldHTML;
	}

	/**
	 * Returns the XHTML for a text-based input. To create a grouped field, simply
	 * separate each subgroup with an underscore (e.g. "Group_Subgroup1_Subgroup2").
	 *
	 * @param string $FieldName The name of the field that is being displayed/posted
	 * with this input. It should related directly to a field name in $this->_DataArray.
	 * @param array $Attributes An associative array of attributes for the input.
	 * ie. maxlength, onclick, class, etc
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::TextBox()
	 */
	public function TextBox($FieldName, $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::TextBox($FieldName, $Attributes, $GroupField);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns XHTML for a checkbox input element.
	 *
	 * @param string $FieldName Name of the field that is being displayed/posted with this input.
	 * It should related directly to a field name in $this->_DataArray.
	 * @param string $Label Label to place next to the checkbox.
	 * @param array $Attributes Associative array of attributes for the input.
	 * (e.g. onclick, class). Setting 'InlineErrors' to FALSE prevents error
	 * message even if $this->InlineErrors is enabled.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::CheckBox()
	 */
	public function CheckBox($FieldName, $Label = '', $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::CheckBox($FieldName, $Label, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns the XHTML for a list of checkboxes.
	 *
	 * @param string $FieldName Name of the field being posted with this input.
	 * @param mixed $DataSet Data to fill the checkbox list. Either an associative
	 * array or a database dataset. ex: RoleID, Name from GDN_Role.
	 * @param mixed $ValueDataSet Values to be pre-checked in $DataSet. Either an
	 * associative array or a database dataset. ex: RoleID from GDN_UserRole for a
	 * single user.
	 * @param array $Attributes	An associative array of attributes for the select.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::CheckBoxList()
	 */
	public function CheckBoxList($FieldName, $DataSet, $ValueDataSet = null, $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::CheckBoxList($FieldName, $DataSet, $ValueDataSet, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns the XHTML for a list of checkboxes; sorted into groups related to
	 * the TextField value of the dataset.
	 *
	 * @param string $FieldName The name of the field that is being displayed/posted with this input. It
	 * should related directly to a field name in a user junction table.
	 * ie. LUM_UserRole.RoleID
	 * @param mixed $DataSet The data to fill the options in the select list.
	 * Either an associative array or a database dataset. ie. RoleID, Name from
	 * LUM_Role.
	 * @param mixed $ValueDataSet The data that should be checked in $DataSet.
	 * Either an associative array or a database dataset. ie. RoleID from
	 * LUM_UserRole for a single user.
	 * @param array $Attributes An associative array of attributes for the select.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::CheckBoxGrid()
	 */
	public function CheckBoxGrid($FieldName, $DataSet, $ValueDataSet, $Attributes, $GroupField = false) {
		$FieldHTML = parent::CheckBoxGrid($FieldName, $DataSet, $ValueDataSet, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns XHTML for a standard date input control.
	 *
	 * @param string $FieldName The name of the field that is being displayed/posted with this input. It
	 *	 should related directly to a field name in $this->_DataArray.
	 * @param array $Attributes An associative array of attributes for the input,
	 * e.g. onclick, class.
	 * Special attributes:
	 *	 YearRange, specified in yyyy-yyyy format. Default is 1900 to current year.
	 *	 Fields, array of month, day, year. Those are only valid values. Order matters.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::Date()
	 */
	public function Date($FieldName, $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::Date($FieldName, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns XHTML for a select list.
	 *
	 * @param string $FieldName The name of the field that is being displayed/posted
	 * with this input. It should related directly to a field name in $this->_DataArray.
	 * ie. RoleID
	 * @param mixed $DataSet The data to fill the options in the select list.
	 * Either an associative array or a database dataset.
	 * @param array $Attributes An associative array of attributes for the select.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::DropDown()
	 */
	public function DropDown($FieldName, $DataSet, $Attributes = false, $GroupField = false) {
		// If dropdown is a multi-select one, ensure that field name ends with "[]".
		// This will allow PHP to return it as an array and catch all selected values,
		// rather than just one of them
		if(GetValue('multiple', $Attributes)) {
			$Attributes['multiple'] = 'multiple';
			if(!StringEndsWith($FieldName, '[]')) {
				$FieldName .= '[]';
			}

			// If no value is passed explicitly, we can extract it from the form's
			// values However, since the field is a multi-select, the field name
			// contains two angle brackets to allow PHP to treat it as an array. The
			// underlying field name, though, is without angle brackets. We therefore
			// remove the trailing brackets to find the real field name, and fetch
			// the value from the form
			if(GetValue('value', $Attributes) === false) {
				$RealFieldName = substr($FieldName, 0, strlen($FieldName)-2);
				$Attributes['value'] = $this->GetValue($RealFieldName, GetValue('Default', $Attributes));
			}
		}

		$FieldHTML = parent::DropDown($FieldName, $DataSet, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns the xhtml for a hidden input.
	 *
	 * @param string $FieldName The name of the field that is being hidden/posted
	 * with this input. It should related directly to a field name in $this->_DataArray.
	 * @param array $Attributes An associative array of attributes for the input.
	 * ie. maxlength, onclick, class, etc
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::Hidden()
	 */
	public function Hidden($FieldName, $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::Hidden($FieldName, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns the xhtml for a standard input tag.
	 *
	 * @param string $FieldName The name of the field that is being displayed/posted
	 * with this input. It should related directly to a field name in $this->_DataArray.
	 * @param string $Type The type attribute for the input.
	 * @param array $Attributes An associative array of attributes for the input.
	 * (e.g. maxlength, onclick, class) Setting 'InlineErrors' to FALSE prevents
	 * error message even if $this->InlineErrors is enabled.
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::Input()
	 */
	public function Input($FieldName, $Type = 'text', $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::Input($FieldName, $Type, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Returns XHTML for a radio input element.
	 *
	 * Provides way of wrapping Input() with a label.
	 *
	 * @param string $FieldName Name of the field that is being displayed/posted
	 * with this input. It should related directly to a field name in $this->_DataArray.
	 * @param string $Label Label to place next to the radio.
	 * @param array $Attributes Associative array of attributes for the input
	 * (e.g. onclick, class). Special values 'Value' and 'Default' (see RadioList).
	 * @param bool GroupField Indicates if field name should be processed to create
	 * a grouped field.
	 * @return string
	 * @see Gdn_Form::Radio()
	 */
	public function Radio($FieldName, $Label = '', $Attributes = false, $GroupField = false) {
		$FieldHTML = parent::Radio($FieldName, $Label, $Attributes);
		return $this->ProcessFieldGrouping($FieldHTML, $GroupField);
	}

	/**
	 * Parses the HTML generated for a field and renames it to create a grouped
	 * field, such as Group[Subgroup1][Sibgroup2] etc.
	 *
	 * Why should you use this methiod?
	 * The Garden framework doesn't allow grouped fields out of the box, and
	 * replaces square brackets with their url-encoded equivalent, thus denying
	 * the possibility of leveraging PHP automatic grouping.
	 *
	 * @param string FieldHTML The HTML generated by one of this class' methods,
	 * such as TextBox, Checkbox, etc.
	 * @return string The processed HTML, with the field names replaced by their
	 * grouped equivalent.
	 */
	protected function RenameFieldToCreateGroups($FieldHTML) {
		$HTMLObj = SHD::strGetHtml($FieldHTML);
		foreach($HTMLObj->find('input[name],select[name],textarea[name]') as $Input) {
			// For each Checkbox, Garden framework adds a hidden field named
			// "Checkboxes[]", to keep track of all checkboxes, whether they have been
			// ticked or not. For such field, the name that we have to replace is stored
			// in its "value" attribute.
			if(strcasecmp($Input->name, 'Checkboxes[]') == 0){
				$InputAttribute = 'value';
			}
			else {
				$InputAttribute = 'name';
			}

			// The last element of InputNameParts contains the Field Name to process
			$InputNameParts	= explode('/', $Input->{$InputAttribute});
			$FieldName = array_pop($InputNameParts);

			// Process field name by transforming it into a hierarchical name
			$InputNameParts[] = $this->FieldNameToGroupedFieldName($FieldName);

			// Replace the name in the processed HTML Input element
			$Input->{$InputAttribute} = implode('/', $InputNameParts);
		}

		return $HTMLObj;
	}

	/**
	 * Replaces an input name with a hierarchical name, to allow grouping inputs
	 * once they are submitted.
	 * The result will be a name attribute such as Rules[RuleClass][GroupName][FieldName].
	 *
	 * @param string FieldName The name of the field to rename. It can be a simple
	 * field name (e.g. "MyField") or a hierarchical name. In latter case, the
	 * hierarchy must be indicated by underscores (e.g. Group_Field, Group_Subgroup_Field).
	 * @return string The new field name, in format "Rules[RuleClass][GroupName][FieldName]".
	 */
	protected function FieldNameToGroupedFieldName($FieldName) {
		$FieldName = urldecode($FieldName);

		// If FieldName ends with brackets, it means that it's an array field. In
		// such case, the brackets must be removed before the field is renamed. They
		// will be added again later
		if(($FieldIsArray = StringEndsWith($FieldName, '[]')) == true) {
			$FieldName = substr($FieldName, 0, (strlen($FieldName) - 2));
		}

		/* Split the field into its sub-parts. Rule field names should be declared
		 * as follows:
		 * - Simple fields - MyField, SomeField, etc.
		 * - Grouped fields - Group_Field1, Group_Field2, etc. Separator must be an
		 *	underscore.
		 */
		$FieldNameParts = explode('_', $FieldName);

		/* Reformat Field Name as follows:
		 * - First field name part will group all the fields under a single
		 *	array.
		 * - Second field part and onwards will be enclosed in brackets, to represent
		 *	sub-groups.
		 *
		 * The result will be a field named as follows:
		 * FirstFieldPart[SecondFieldPart][ThirdFieldPart][FourthFieldPart](etc)
		 *
		 * Using this convention, PHP will automatically create nested array of
		 * fields and pass them into the $_POST variable. This will allow to group
		 * all the fields belonging together without having to search for
		 * them by using substring.
		 */
		$FirstFieldPart = array_shift($FieldNameParts);
		$NewFieldName = $FirstFieldPart . '[' . implode('][', $FieldNameParts) . ']';

		// If field is an array, add back the brackets that were removed previously
		if($FieldIsArray) {
			$NewFieldName .= '[]';
		}

		return $NewFieldName;
	}

	/**
	 * Checks $this->FormValues() to see if the specified button translation
	 * code was submitted with the form (helps figuring out what button was
	 * pressed to submit the form when there is more than one button available).
	 *
	 * @param string $ButtonCodes The translation code of the button to check for,
	 * or an array of codes.
	 * @return boolean
	 *
	 * @see \Gdn_Form::ButtonExists()
	 */
	public function ButtonExists($ButtonCodes) {
		if(!is_array($ButtonCodes)) {
			$ButtonCodes = array($ButtonCodes);
		}

		foreach($ButtonCodes as $Code) {
			$NameKey = $this->EscapeString($Code);
			if(array_key_exists($Code, $this->FormValues())) {
				return true;
			}
		}
		return false;
	}
}
