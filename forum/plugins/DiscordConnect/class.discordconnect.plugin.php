<?php
/**
 * @copyright 2017-2017 Pierre-Olivier Konecki
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package Discord
 */

// Define the plugin:
$PluginInfo['DiscordConnect'] = array(
    'Name' => 'Discord Social Connect',
    'Description' => 'Users link their forum account with Discord.',
    'Version' => '1.0.0',
    'RequiredApplications' => array('Vanilla' => '2.2'), //, 'api' => '0.4.0'
    //'RequiredTheme' => false,
    //'RequiredPlugins' => false,
    'MobileFriendly' => true,
    'SettingsUrl' => '/settings/discordconnect',
    'SettingsPermission' => 'Garden.Settings.Manage',
    //'HasLocale' => true,
    //'RegisterPermissions' => false,
    'Author' => "Pierre-Olivier Konecki",
    'AuthorEmail' => 'pkonecki@gmail.com',
    'AuthorUrl' => 'https://github.com/pkonecki',
    //'Hidden' => true,
    //'RequiresRegistration' => true,
    //'Icon' => 'discord.svg'
);

include 'hybridauth/src/autoload.php';



class DiscordConnectPlugin extends Gdn_Plugin {
    public static $ApplicationFolder = 'plugins/DiscordConnect';

    public function setup() {
    }

    static $Debug = true;
    static public function DebugLog($ParMessage)
    {
        if (DiscordConnectPlugin::$Debug)
            trace($ParMessage);
    }


    /**
     *
     *
     * @param $Path
     * @param bool $Post
     * @return mixed
     * @throws Gdn_UserException
     */
    public function botapi($Path, $Params = false, $Type = 'GET') {
        // Build the url.
        $Url = 'https://discordapp.com/api/'.ltrim($Path, '/');
        $AccessToken = c('Plugins.DiscordConnect.BotToken');
        if (!$AccessToken || $AccessToken == '') {
            throw new Gdn_UserException("You don't have a valid Discord connection.");
        }

        $PostData = NULL;
        if ($Params != false) {
            if ($Type == 'GET')
            {
                $Url .= "?".http_build_query($Params);
            }
            else
            {
                $PostData = json_encode($Params);
            }
        }
        $this->DebugLog($Type.': '.$Url.' '.$PostData);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $Url, 
            CURLOPT_HTTPHEADER     => array('Authorization: Bot '.$AccessToken, "Content-Type: application/json", 'Content-Length: ' . strlen($PostData)), 
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_VERBOSE        => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => $Type,
        ));

        if ($PostData != NULL)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
        }

        $Response = curl_exec($ch);

        $HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        $this->DebugLog($HttpCode);

        Gdn::controller()->setJson('Type', $ContentType);

        if (strpos($ContentType, 'javascript') !== false) {
            $Result = json_decode($Response, true);

            if (isset($Result['error'])) {
                //Gdn::dispatcher()->passData('FacebookResponse', $Result);
                throw new Gdn_UserException($Result['error']['message']);
            }
        } else {
            $Result = $Response;
        }

        return $Result;
    }

    public function ProfileController_AddProfileTabs_Handler($Sender) {
        $Session = Gdn::Session();
        if (!$Session->IsValid())
            return;
        $SessionUserID = (int)$Session->UserID;
        if (is_object($Sender->User) && ($Sender->User->UserID > 0) && $Sender->User->UserID == $SessionUserID) {
            $UserID = $Sender->User->UserID;
            $DiscordLabel = Sprite('SpDiscord', 'SpMyDrafts Sprite') . ' ' . T('Discord');

            $Sender->AddProfileTab(T('Articles'),
                    'profile/discord/' . $Sender->User->UserID . '/' . rawurlencode($Sender->User->Name), 'Discord',
                    //'profile/discord/', 'Discord',
                    $DiscordLabel);
        }
    }

        /**
     *
     *
     * @param ProfileController $Sender
     * @param type $UserReference
     * @param type $Username
     * @param type $Code
     */
    public function profileController_discord_create($Sender, $UserReference = '', $Username = '', $Code = false) {

        // Session
        $Session = Gdn::Session();

        if (!$Session->IsValid())
            return;
        $UserID = $Session->UserID;

        $Action = 'Nothing';
        if ($Sender->Form->authenticatedPostBack() === true) {
            $Data = $Sender->Form->FormValues();
            $Action = $Data['Action'];
        }

        // Conf
        $redirectUri = url('/profile/discord?hauth.done=Discord', true);
        $config = [
            //Location where to redirect users once they authenticate with Facebook
            //For this example we choose to come back to this same script
            'callback' => $redirectUri,

            //Facebook application credentials
            'keys' => [
                'id'     => c('Plugins.DiscordConnect.ApplicationID'), //Required: your Facebook application id
                'secret' => c('Plugins.DiscordConnect.Secret')  //Required: your Facebook application secret
            ]
        ];

        // UserProfile from cache:
        $userProfile = unserialize($this->GetUserMeta($UserID, 'DiscordProfile', '', TRUE));
        $isConnected = false;
        if (!is_a($userProfile, 'Hybridauth\User\Profile') )
        {
            $this->DebugLog('Need fetch profile');
            if( $Action == 'ToggleConnection' || $Code)
            {
                //AccessToken
                $accessToken = unserialize($this->GetUserMeta($UserID, 'AccessToken', '', TRUE));
                //$accessToken = '';
                //Auth
                $adapter = new Hybridauth\Provider\Discord($config);

                if (is_array($accessToken) && $accessToken['expires_at'] > time() )
                {
                    $adapter->SetAccessToken($accessToken);
                    $this->DebugLog('used accessToken :'.serialize($accessToken));
                }
                else
                {
                    $adapter->authenticate();
                    $accessToken = $adapter->getAccessToken();
                    $this->SetUserMeta($UserID, 'AccessToken', serialize($accessToken) );
                    $this->DebugLog('used auth, new accessToken: '.serialize($accessToken));
                }

                //Data
                $isConnected = true;
                $userProfile = $adapter->getUserProfile();
                $this->SetUserMeta($UserID, 'DiscordProfile', serialize($userProfile) );
                $this->SetUserMeta($UserID, 'DiscordUserID', (int)$userProfile->{'identifier'} );
                redirect(url('/profile/discord',true));
            }
        }
        else if( $Action == 'ToggleConnection')
        {
            $this->SetUserMeta($UserID, 'DiscordProfile', NULL );
            $this->SetUserMeta($UserID, 'AccessToken', NULL );
            $adapter = new Hybridauth\Provider\Discord($config);
            $adapter->Disconnect();
            $isConnected = false;
        }
        else
        {
            $isConnected = true;
        }

        // SyncRole
        if ($Action == 'SyncRole')
        {
            $UserModel = new UserModel();
            $UserRoleData = $UserModel->getRoles($UserID)->resultArray();
            $rolesnames = array();
            foreach($UserRoleData as $key => $role)
                $rolesnames[] = $role['Name'];

            $GuildId = c('Plugins.DiscordConnect.GuildId');

            $discordroles = json_decode($this->botapi('/guilds/'.$GuildId.'/roles'), true);
            foreach ( $discordroles as $discordrole  )
            {
                if (in_array($discordrole['name'], $rolesnames ))
                {
                    $this->botapi('/guilds/'.$GuildId.'/members/'.$userProfile->{'identifier'}.'/roles/'.$discordrole['id'], NULL, 'PUT');
                }
            }
        }

        // View
        $Sender->EditMode(false);
        $Sender->GetUserInfo($UserReference, $Username, $UserID);
        $Sender->SetData('isConnected', $isConnected);
        $Sender->SetData('DiscordProfile', $userProfile);
        $Sender->_SetBreadcrumbs(T('Discord'), '/profile/discord');
        $Sender->SetTabView('Discord', dirname(__FILE__).DS.'views'.DS.'profile.php', 'Profile', 'Dashboard');

        $Sender->Render();
    }

    //Settings
    public function settingsController_DiscordConnect_create($Sender) {
        $Sender->permission('Garden.Settings.Manage');

        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->setField(array(
            'Plugins.DiscordConnect.ApplicationID',
            'Plugins.DiscordConnect.Secret',
            'Plugins.DiscordConnect.BotToken',
            'Plugins.DiscordConnect.GuildId',
        ));
        $Sender->Form->setModel($ConfigurationModel);

        if ($Sender->Form->authenticatedPostBack()) {
            if ($Sender->Form->save() !== false) {
                $Sender->informMessage(sprite('Check', 'InformSprite').T('Your changes have been saved.'), 'Dismissable AutoDismiss HasSprite');
            }
        } else {
            $Sender->Form->setData($ConfigurationModel->Data);
        }

        $Sender->title(T('DiscordConnect Settings'));
        $Sender->addSideMenu();
        $Sender->render($this->GetView('settings.php'));
    }


}
