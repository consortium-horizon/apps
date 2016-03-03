<?php
/**
 * Authentication plugin interface. Instantiate a subclass of AuthPlugin
 * and set $wgAuth to it to authenticate against some external tool.
 *
 * The default behavior is not to do anything, and use the local user
 * database for all authentication. A subclass can require that all
 * accounts authenticate externally, or use it only as a fallback; also
 * you can transparently create internal wiki accounts the first time
 * someone logs in who can be authenticated externally.
 *
 * Vanilla 2 compatibility by Pierre-Olivier Konecki (github.com/pkonecki)
 * Vanilla model by David Cramer
 * AuthPlugin extension by Daniel Gravenor c/o HolisticEarth.org
 * AuthPlugin original by Kai Backman
 *
 * @package MediaWiki
 */

// define('APPLICATION', 'Vanilla');
// define('APPLICATION_VERSION', '2.1.11');
// define('DS', '/');
// define('PATH_ROOT', '/var/www/webapps/current/forum/');
// ob_start();
// require_once(PATH_ROOT.DS.'bootstrap.php');
// ob_end_clean(); // clear any header output from vanila
// $Session = Gdn::Session();
// $Authenticator = Gdn::Authenticator();
// if ($Session->IsValid()) {
//    $Name = $Session->User->Name;
//    echo "You are logged in as $Name";
// }else{
//   echo "You are not logged in";
// }

define("PATH_LIBRARY","/var/www/webapps/current/forum/library");
require_once(PATH_LIBRARY."/core/class.passwordhash.php");
require_once("includes/AuthPlugin.php");

/**
 * Check the Vanilla session and automatically log the user into the wiki.
 *
 * @param User $user
 * @return bool
 * @public
 */
function AutoAuthenticateVanilla ($user_init_data, &$user)
{

        $user = $user_init_data;
        $Session = Gdn::Session();
        $Authenticator = Gdn::Authenticator();

        if ($Session->IsValid()) {
                $username = $Session->User->Name;
                // Convert to wiki standards
                $username = ucfirst(str_replace('_', '\'', $username));
                // Wiki doesn't allow [] and SMF does, SMF doesn't allow =" and Wiki does.
                // We do it like this so we can reverse it to find the original name if needed.
                $username = strtr($username, array('[' => '=', ']' => '"'));
                if (!($user->isLoggedIn() && $user->getName() == $username))
                {
                        $user->setId($user->idFromName($username));

                        // No ID we need to add this member to the wiki database.
                        if ($user->getID() == 0)
                        {
                                // getID clears out the name set above.
                                $user->setName($username);
                                $user->setEmail($user_settings['email_address']);
                                $user->setRealName($user_settings['real_name']);

                                // Let wiki know that their email has been verified.
                                $user->mEmailAuthenticated = wfTimestampNow();

                                // Finally create the user.
                                $user->addToDatabase();

                                // Some reason addToDatabase doesn't set options.  So we do this manually.
                                $user->saveSettings();
                        }
                }
                return true;
        }
        else {
                if ($user->isLoggedIn())
                        $user->logout();
                return false;
        }
}

/**
 * Redirect to login page
 *
 * @public
 */
function RedirectLoginVanilla() {
        header('Location: http://' . $wgVanillaAuth_LoginRedirect );
        exit();
}

class AuthPlugin_Vanilla extends AuthPlugin {

        // Create a persistent DB connection
        var $vanilla_database;

        function AuthPlugin_Vanilla($host, $username, $password, $dbname, $prefix) {
                global $wgHooks;
                $this->vanilla_database = mysql_pconnect($host, $username, $password);
                // if (!$this->vanilla_database)
                //         die("problème de connection DB");
                mysql_select_db($dbname, $this->vanilla_database);
                $this->vanilla_prefix = $prefix;
                // set the usergroups for those who can edit the wiki
                $this->allowed_usergroups = Array(11);
                // set the usergroups for the administrators
                $this->admin_usergroups = Array(1, 9);
                $this->user_rights = Array("sysop");
                // search pattern to only accept alphanumeric or underscore characters in usernames
                // if they have illegal characters, their name cannot exist, period
                $this->searchpattern = "/[^a-zA-Z0-9]+/";

                //$wgHooks['UserLoadFromSession'][] = 'AutoAuthenticateVanilla';
                //$wgHooks['UserLoginForm'][] = 'RedirectLoginVanilla';
                // $wgHooks['UserLogout'][] = 'UserLogoutVanilla';
        }

        /**
        * Check whether there exists a user account with the given name.
        * The name will be normalized to MediaWiki's requirements, so
        * you might need to munge it (for instance, for lowercase initial
        * letters).
        *
        * @param string $username
        * @return bool
        * @access public
        */
        function userExists($username ) {
                // if no illegal characters are found in their username, then check to see if they exist
                if (!preg_match($this->searchpattern, $username)) {
                        $username = addslashes($username);
                        $userrolessqlvalues = "(";
                        foreach($this->allowed_usergroups as $roleID)
                                $userrolessqlvalues .= $roleID.",";
                        $userrolessqlvalues = rtrim($userrolessqlvalues, ',');
                        $userrolessqlvalues .= ")";
                        $vanilla_find_user_query = "SELECT Name FROM " . $this->vanilla_prefix . "User as U, " . $this->vanilla_prefix . "UserRole as UR WHERE U.UserId=UR.UserID AND UR.RoleId IN ".$userrolessqlvalues." AND Name = '" . $username . "'";
                        $vanilla_find_result = mysql_query($vanilla_find_user_query, $this->vanilla_database);
                        // make sure that there is only one person with the username
                        if (mysql_num_rows($vanilla_find_result) == 1) {
                                $vanilla_userinfo = mysql_fetch_assoc($vanilla_find_result);
                                mysql_free_result($vanilla_find_result);
                                return true;
                        }
                }
                // if no one is registered with that username, or there are more than 1 entries
                // or they have illegal characters return false (they do not exist)
                die ("User Don't exists");
                return false;
        }

        /**
        * Check if a username+password pair is a valid login.
        * The name will be normalized to MediaWiki's requirements, so
        * you might need to munge it (for instance, for lowercase initial
        * letters).
        *
        * @param string $username
        * @param string $password
        * @return bool
        * @access public
        */
        function authenticate($username, $password) {
                // if their name does not contain any illegal characters, let them try to login
                if (!preg_match($this->searchpattern, $username)) {
                        $username = addslashes($username);
                        $userrolessqlvalues = "(";
                        foreach($this->allowed_usergroups as $roleID)
                                $userrolessqlvalues .= $roleID.",";
                        $userrolessqlvalues = rtrim($userrolessqlvalues, ',');
                        $userrolessqlvalues .= ")";
                        $vanilla_find_user_query = "SELECT Password,  HashMethod FROM " . $this->vanilla_prefix . "User as U, " . $this->vanilla_prefix . "UserRole as UR WHERE U.UserId=UR.UserID AND UR.RoleId IN ".$userrolessqlvalues." AND Name = '" . $username . "'";
                        $vanilla_find_result = mysql_query($vanilla_find_user_query, $this->vanilla_database);
                        // if (!$vanilla_find_result)
                        //         die("THEM LELZ: ".$vanilla_find_user_query);
                        if (mysql_num_rows($vanilla_find_result) == 1) {
                                $vanilla_userinfo = mysql_fetch_assoc($vanilla_find_result);
                                mysql_free_result($vanilla_find_result);
                                $DbHash = $vanilla_userinfo['Password'];
                                $DbMethod = $vanilla_userinfo['HashMethod'];
                                $FormPassword = $password;
                                $passwordChecker = new Gdn_PasswordHash();
                                return $passwordChecker->CheckPassword($FormPassword, $DbHash, $DbMethod, $username);
                        }
                }
                return false;
        }

        /**
        * When a user logs in, optionally fill in preferences and such.
        * For instance, you might pull the email address or real name from the
        * external user database.
        *
        * The User object is passed by reference so it can be modified; don't
        * forget the & on your function declaration.
        *
        * @param User $user
        * @access public
        */
        function updateUser( &$user ) {
                # Override this and do something
                return false;
                $vanilla_find_user_query = "SELECT * FROM " . $this->vanilla_prefix . "User WHERE Name ='" . addslashes($user->mName) . "'";
                $vanilla_find_result = mysql_query($vanilla_find_user_query, $this->vanilla_database) or die("Could not find username");
                if(mysql_num_rows($vanilla_find_result) == 1) {
                        $vanilla_userinfo = mysql_fetch_assoc($vanilla_find_result);
                        mysql_free_result($vanilla_find_result);

                        if (in_array($vanilla_userinfo['RoleID'], $this->admin_usergroups) || $admin_secondary === true) {
                                // if a user is not a sysop, make them a sysop
                                if (!in_array("sysop", $user->getEffectiveGroups())) {
                                        $user->addGroup('sysop');
                                        return true;
                                }
                        }
                        // if the user is not an administrator, but they were, and they are still a sysop, remove their sysop status
                        if (!in_array($vanilla_userinfo['RoleID'], $this->admin_usergroups) && $admin_secondary === false) {
                                if (in_array("sysop", $user->getEffectiveGroups())) {
                                        $user->removeGroup('sysop');
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
        * Return true if the wiki should create a new local account automatically
        * when asked to login a user who doesn't exist locally but does in the
        * external auth database.
        *
        * If you don't automatically create accounts, you must still create
        * accounts in some way. It's not possible to authenticate without
        * a local account.
        *
        * This is just a question, and shouldn't perform any actions.
        *
        * @return bool
        * @access public
        */
        function autoCreate() {
                return true;
        }

        /**
        * Return true to prevent logins that don't authenticate here from being
        * checked against the local database's password fields.
        *
        * This is just a question, and shouldn't perform any actions.
        *
        * @return bool
        * @access public
        */
        function strict() {
                return true;
        }

        /**
        * When creating a user account, optionally fill in preferences and such.
        * For instance, you might pull the email address or real name from the
        * external user database.
        *
        * The User object is passed by reference so it can be modified; don't
        * forget the & on your function declaration.
        *
        * @param User $user
        * @access public
        */
        function initUser( &$user ) {
                $vanilla_find_user_query = "SELECT Email, RoleID FROM " . $this->vanilla_prefix . "User WHERE Name LIKE '" . addslashes($user->mName) . "'";
                $vanilla_find_result = mysql_query($vanilla_find_user_query, $this->vanilla_database);
                if(mysql_num_rows($vanilla_find_result) == 1) {
                        $vanilla_userinfo = mysql_fetch_assoc($vanilla_find_result);
                        mysql_free_result($vanilla_find_result);
                        $user->mEmail = $vanilla_userinfo['Email'];
                        $user->mEmailAuthenticated = wfTimestampNow();
                }
        }
}
?>
