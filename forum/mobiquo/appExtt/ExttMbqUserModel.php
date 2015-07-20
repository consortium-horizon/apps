<?php
/**
 * ExttMbqUserModel extended from UserModel
 * modified method InsertForBasic()
 * modified method __construct()
 * modified method _Insert()
 * modified method Save()
 * modified method Register()
 * 
 * @since  2013-10-18
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqUserModel extends UserModel {
   
   /**
    * Class constructor. Defines the related database table name.
    */
   public function __construct() {
      parent::__construct();
   }
   
   /**
    * A convenience method to be called when inserting users (because users
    * are inserted in various methods depending on registration setups).
    */
   protected function _Insert($Fields, $Options = array()) {
      $this->EventArguments['InsertFields'] =& $Fields;
      $this->FireEvent('BeforeInsertUser');
      
      // Massage the roles for email confirmation.
      if (MbqMain::$oMbqAppEnv->otherParams['needNativeRegister']) {
          if (C('Garden.Registration.ConfirmEmail') && !GetValue('NoConfirmEmail', $Options)) {
             TouchValue('Attributes', $Fields, array());
             $ConfirmationCode = RandomString(8);
             $Fields['Attributes']['EmailKey'] = $ConfirmationCode;
             
             if (isset($Fields['Roles'])) {
                $Fields['Attributes']['ConfirmedEmailRoles'] = $Fields['Roles'];
             }
             $Fields['Roles'] = (array)C('Garden.Registration.ConfirmEmailRole');
          }
      }

      // Make sure to encrypt the password for saving...
      if (array_key_exists('Password', $Fields) && !array_key_exists('HashMethod', $Fields)) {
         $PasswordHash = new Gdn_PasswordHash();
         $Fields['Password'] = $PasswordHash->HashPassword($Fields['Password']);
         $Fields['HashMethod'] = 'Vanilla';
      }

      $Roles = GetValue('Roles', $Fields);
      unset($Fields['Roles']);
      
      if (array_key_exists('Attributes', $Fields) && !is_string($Fields['Attributes']))
            $Fields['Attributes'] = serialize($Fields['Attributes']);
      
      $UserID = $this->SQL->Insert($this->Name, $Fields);
      if (is_array($Roles)) {
         $this->SaveRoles($UserID, $Roles, FALSE);
      }

      // Approval registration requires an email confirmation.
      if ($UserID && isset($ConfirmationCode) && strtolower(C('Garden.Registration.Method')) == 'approval') {
         // Send the confirmation email.
         $this->SendEmailConfirmationEmail($UserID);
      }

      // Fire an event for user inserts
      $this->EventArguments['InsertUserID'] = $UserID;
      $this->EventArguments['InsertFields'] = $Fields;
      $this->FireEvent('AfterInsertUser');
      return $UserID;
   }

   public function Register($FormPostValues, $Options = array()) {
      $Valid = TRUE;
      $FormPostValues['LastIPAddress'] = Gdn::Request()->IpAddress();
      
      // Throw an error if the registering user has an active session
      if (Gdn::Session()->IsValid())
         $this->Validation->AddValidationResult('Name', 'You are already registered.');

      // Check for banning first.
      $Valid = BanModel::CheckUser($FormPostValues, $this->Validation, TRUE);

      // Throw an event to allow plugins to block the registration.
      unset($this->EventArguments['User']);
      $this->EventArguments['User'] = $FormPostValues;
      $this->EventArguments['Valid'] =& $Valid;
      $this->FireEvent('BeforeRegister');

      if (!$Valid)
         return FALSE; // plugin blocked registration

      switch (strtolower(C('Garden.Registration.Method'))) {
         case 'captcha':
            $UserID = $this->InsertForBasic($FormPostValues, GetValue('CheckCaptcha', $Options, TRUE), $Options);
            break;
         case 'approval':
            //if (MbqMain::$oMbqAppEnv->otherParams['needNativeRegister']) {  //!!!
                $UserID = $this->InsertForApproval($FormPostValues, $Options);
            //} else {
            //    $UserID = $this->InsertForBasic($FormPostValues, GetValue('CheckCaptcha', $Options, FALSE), $Options);
            //}
            break;
         //!!! The 'invitation' case can cause registration failed,so make it to basic method. wztmdf 20140223
         //case 'invitation':
            //$UserID = $this->InsertForInvite($FormPostValues, $Options);
            //break;
         case 'closed':
            $UserID = FALSE;
            $this->Validation->AddValidationResult('Registration', 'Registration is closed.');
            break;
         case 'basic':
         default:
            $UserID = $this->InsertForBasic($FormPostValues, GetValue('CheckCaptcha', $Options, FALSE), $Options);
            break;
      }
      return $UserID;
   }
   
   /**
    * Generic save procedure.
    */
   public function Save($FormPostValues, $Settings = FALSE) {
      
      // See if the user's related roles should be saved or not.
      $SaveRoles = GetValue('SaveRoles', $Settings);

      // Define the primary key in this model's table.
      $this->DefineSchema();

      // Add & apply any extra validation rules:
      if (array_key_exists('Email', $FormPostValues) && GetValue('ValidateEmail', $Settings, TRUE))
         $this->Validation->ApplyRule('Email', 'Email');

      // Custom Rule: This will make sure that at least one role was selected if saving roles for this user.
      if ($SaveRoles) {
         $this->Validation->AddRule('OneOrMoreArrayItemRequired', 'function:ValidateOneOrMoreArrayItemRequired');
         // $this->Validation->AddValidationField('RoleID', $FormPostValues);
         $this->Validation->ApplyRule('RoleID', 'OneOrMoreArrayItemRequired');
      }

      // Make sure that the checkbox val for email is saved as the appropriate enum
      if (array_key_exists('ShowEmail', $FormPostValues))
         $FormPostValues['ShowEmail'] = ForceBool($FormPostValues['ShowEmail'], '0', '1', '0');

      // Validate the form posted values
      $UserID = GetValue('UserID', $FormPostValues);
      $Insert = $UserID > 0 ? FALSE : TRUE;
      if ($Insert) {
         $this->AddInsertFields($FormPostValues);
      } else {
         $this->AddUpdateFields($FormPostValues);
      }
      
      $this->EventArguments['FormPostValues'] = $FormPostValues;
      $this->FireEvent('BeforeSaveValidation');

      $RecordRoleChange = TRUE;
      if ($this->Validate($FormPostValues, $Insert) && $this->ValidateUniqueFields(GetValue('Name', $FormPostValues), GetValue('Email', $FormPostValues), $UserID)) {
         $Fields = $this->Validation->ValidationFields(); // All fields on the form that need to be validated (including non-schema field rules defined above)
         $RoleIDs = GetValue('RoleID', $Fields, 0);
         $Username = GetValue('Name', $Fields);
         $Email = GetValue('Email', $Fields);
         $Fields = $this->Validation->SchemaValidationFields(); // Only fields that are present in the schema
         // Remove the primary key from the fields collection before saving
         $Fields = RemoveKeyFromArray($Fields, $this->PrimaryKey);
         
         if (!$Insert && array_key_exists('Password', $Fields)) {
            // Encrypt the password for saving only if it won't be hashed in _Insert()
            $PasswordHash = new Gdn_PasswordHash();
            $Fields['Password'] = $PasswordHash->HashPassword($Fields['Password']);
            $Fields['HashMethod'] = 'Vanilla';
         }
         
         // Check for email confirmation.
         if (MbqMain::$oMbqAppEnv->otherParams['needNativeRegister']) {
             if (C('Garden.Registration.ConfirmEmail') && !GetValue('NoConfirmEmail', $Settings)) {
                if (isset($Fields['Email']) && $UserID == Gdn::Session()->UserID && $Fields['Email'] != Gdn::Session()->User->Email && !Gdn::Session()->CheckPermission('Garden.Users.Edit')) {
                   $User = Gdn::Session()->User;
                   $Attributes = Gdn::Session()->User->Attributes;
                   
                   $ConfirmEmailRoleID = C('Garden.Registration.ConfirmEmailRole');
                   if (RoleModel::Roles($ConfirmEmailRoleID)) {
                      // The confirm email role is set and it exists so go ahead with the email confirmation.
                      $EmailKey = TouchValue('EmailKey', $Attributes, RandomString(8));
                      
                      if ($RoleIDs)
                         $ConfirmedEmailRoles = $RoleIDs;
                      else
                         $ConfirmedEmailRoles = ConsolidateArrayValuesByKey($this->GetRoles($UserID), 'RoleID');
                      $Attributes['ConfirmedEmailRoles'] = $ConfirmedEmailRoles;
    
                      $RoleIDs = (array)C('Garden.Registration.ConfirmEmailRole');
    
                      $SaveRoles = TRUE;
                      $Fields['Attributes'] = serialize($Attributes);
                   }
                } 
             }
         }
         
         $this->EventArguments['Fields'] = $Fields;
         $this->FireEvent('BeforeSave');
         
         // Check the validation results again in case something was added during the BeforeSave event.
         if (count($this->Validation->Results()) == 0) {
            // If the primary key exists in the validated fields and it is a
            // numeric value greater than zero, update the related database row.
            if ($UserID > 0) {
               // If they are changing the username & email, make sure they aren't
               // already being used (by someone other than this user)
               if (ArrayValue('Name', $Fields, '') != '' || ArrayValue('Email', $Fields, '') != '') {
                  if (!$this->ValidateUniqueFields($Username, $Email, $UserID))
                     return FALSE;
               }
               
               if (array_key_exists('Attributes', $Fields) && !is_string($Fields['Attributes'])) {
                  $Fields['Attributes'] = serialize($Fields['Attributes']);
               }
   
               $this->SQL->Put($this->Name, $Fields, array($this->PrimaryKey => $UserID));
   
               // Record activity if the person changed his/her photo.
               $Photo = ArrayValue('Photo', $FormPostValues);
               if ($Photo !== FALSE) {
                  if (GetValue('CheckExisting', $Settings)) {
                     $User = $this->GetID($UserID);
                     $OldPhoto = GetValue('Photo', $User);
                  }

                  if (isset($OldPhoto) && $OldPhoto != $Photo) {
                     if (strpos($Photo, '//'))
                        $PhotoUrl = $Photo;
                     else
                        $PhotoUrl = Gdn_Upload::Url(ChangeBasename($Photo, 'n%s'));

                     AddActivity($UserID, 'PictureChange', Img($PhotoUrl, array('alt' => T('Thumbnail'))));
                  }
               }
   
            } else {
               $RecordRoleChange = FALSE;
               if (!$this->ValidateUniqueFields($Username, $Email))
                  return FALSE;
   
               // Define the other required fields:
               $Fields['Email'] = $Email;
               
               $Fields['Roles'] = $RoleIDs;
               // Make sure that the user is assigned to one or more roles:
               $SaveRoles = FALSE;
   
               // And insert the new user.
               $UserID = $this->_Insert($Fields, $Settings);
   
               // Report that the user was created
               $Session = Gdn::Session();
               AddActivity(
                  $Session->UserID,
                  GetValue('ActivityType', $Settings, 'JoinCreated'),
                  T('Welcome Aboard!'),
                  $UserID
               );
            }
            // Now update the role settings if necessary.
            if ($SaveRoles) {
               // If no RoleIDs were provided, use the system defaults
               if (!is_array($RoleIDs))
                  $RoleIDs = Gdn::Config('Garden.Registration.DefaultRoles');
   
               $this->SaveRoles($UserID, $RoleIDs, $RecordRoleChange);
            }

            // Send the confirmation email.
            if (isset($EmailKey)) {
               $this->SendEmailConfirmationEmail((array)Gdn::Session()->User);
            }

            $this->EventArguments['UserID'] = $UserID;
            $this->FireEvent('AfterSave');
         } else {
            $UserID = FALSE;
         }
      } else {
         $UserID = FALSE;
      }
      
      // Clear cached user data
      if (!$Insert && $UserID) {
         $this->ClearCache($UserID, array('user'));
      }
      
      return $UserID;
   }
    
   /**
    * To be used for basic registration, and captcha registration
    */
   public function InsertForBasic($FormPostValues, $CheckCaptcha = TRUE, $Options = array()) {
      $RoleIDs = Gdn::Config('Garden.Registration.DefaultRoles');
      if (!is_array($RoleIDs) || count($RoleIDs) == 0)
         throw new Exception(T('The default role has not been configured.'), 400);

      $UserID = FALSE;

      // Define the primary key in this model's table.
      $this->DefineSchema();

      // Add & apply any extra validation rules.
      if (GetValue('ValidateEmail', $Options, TRUE))
         $this->Validation->ApplyRule('Email', 'Email');

      // TODO: DO I NEED THIS?!
      // Make sure that the checkbox val for email is saved as the appropriate enum
      if (array_key_exists('ShowEmail', $FormPostValues))
         $FormPostValues['ShowEmail'] = ForceBool($FormPostValues['ShowEmail'], '0', '1', '0');

      $this->AddInsertFields($FormPostValues);

      if ($this->Validate($FormPostValues, TRUE) === TRUE) {
         // Check for spam.
         //if (MbqMain::$cmd == 'register') {
             $Spam = SpamModel::IsSpam('Registration', $FormPostValues);
             if ($Spam) {
                $this->Validation->AddValidationResult('Spam', 'You are not allowed to register at this time.');
                return;
             }
         //}

         $Fields = $this->Validation->ValidationFields(); // All fields on the form that need to be validated (including non-schema field rules defined above)
         $Username = ArrayValue('Name', $Fields);
         $Email = ArrayValue('Email', $Fields);
         $Fields = $this->Validation->SchemaValidationFields(); // Only fields that are present in the schema
         $Fields['Roles'] = $RoleIDs;
         $Fields = RemoveKeyFromArray($Fields, $this->PrimaryKey);

         // If in Captcha registration mode, check the captcha value
         /*
         if ($CheckCaptcha && Gdn::Config('Garden.Registration.Method') == 'Captcha') {
            $CaptchaPublicKey = ArrayValue('Garden.Registration.CaptchaPublicKey', $FormPostValues, '');
            $CaptchaValid = ValidateCaptcha($CaptchaPublicKey);
            if ($CaptchaValid !== TRUE) {
               $this->Validation->AddValidationResult('Garden.Registration.CaptchaPublicKey', 'The reCAPTCHA value was not entered correctly. Please try again.');
               return FALSE;
            }
         }
         */

         if (!$this->ValidateUniqueFields($Username, $Email))
            return FALSE;

         // Define the other required fields:
         $Fields['Email'] = $Email;

         // And insert the new user
         $UserID = $this->_Insert($Fields, $Options);

         AddActivity(
            $UserID,
            'Join',
            T('Welcome Aboard!')
         );
      }
      return $UserID;
   }
   
}

?>