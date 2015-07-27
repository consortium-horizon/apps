<?php if (!defined('APPLICATION')) exit();

$PluginInfo['Phplist'] = array(
    'Name' => 'Phplist',
    'Description' => 'Add a optin/optout checkbox at registration page or autosubscribe new users (double opt-in) to your Phplist newsletter.',
    'Version' => '0.1.0',
    'RequiredApplications' => array('Vanilla' => '2.0'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'SettingsUrl' => 'settings/phplist',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => "Arnaud Leymet",
    'AuthorEmail' => 'arnaud.leymet@gmail.com',
    'AuthorUrl' => 'http://arnley.com',
    'License' => 'GPL v3'
);

class PhplistPlugin extends Gdn_Plugin {
    
    public function SettingsController_Phplist_Create($Sender) {
        $Sender->Permission('Garden.Plugins.Manage');
        $Sender->AddSideMenu();
        $Sender->Title('Phplist');
        $ConfigurationModule = new ConfigurationModule($Sender);
        $ConfigurationModule->RenderAll = True;
        $Schema = array(
            'Plugins.Phplist.URL' => 
                array(
                    'LabelCode' => 'URL', 
                    'Control' => 'TextBox', 
                    'Default' => C('Plugins.Phplist.URL', '')
                ),
            'Plugins.Phplist.Login' => 
                array(
                    'LabelCode' => 'Login', 
                    'Control' => 'TextBox', 
                    'Default' => C('Plugins.Phplist.Login', '')
                ),
            'Plugins.Phplist.Password' => 
                array(
                    'LabelCode' => 'Password', 
                    'Control' => 'TextBox', 
                    'Default' => C('Plugins.Phplist.Password', '')
                ),
            'Plugins.Phplist.ListID' => 
                array(
                    'LabelCode' => 'List ID', 
                    'Control' => 'TextBox', 
                    'Default' => C('Plugins.Phplist.ListID', '')
                ),
            'Plugins.Phplist.Autosubscribe' => 
                array(
                    'LabelCode' => 'Auto subscribe new users (hide opt-in/out option at registration page).', 
                    'Control' => 'CheckBox', 
                    'Default' => C('Plugins.Phplist.Autosubscribe', '')
                )
        );
        $ConfigurationModule->Schema($Schema);
        $ConfigurationModule->Initialize();
        $Sender->View = dirname(__FILE__) . DS . 'views' . DS . 'phplistsettings.php';
        $Sender->ConfigurationModule = $ConfigurationModule;
        $Sender->Render();
    }
    
    public function PluginController_Phplist_Create($Sender) {
        $OptIn = (strcmp($Sender->Request->Post("OptIn", "0"), "1") == 0 ? TRUE : FALSE);
        $Sender->Permission('Garden.Plugins.Manage');
        $Action = ArrayValue('0', $Sender->RequestArgs, 'default');
        switch($Action){
            case "bulkSubscribe":
                self::bulk($OptIn);
                echo T("<b>Please go back with your browser.</b>");
                break;
        }
    }

    private function bulk($OptIn = TRUE) {

        include_once(dirname(__FILE__) . DS . 'phplistrestapi' . DS .'phplist_restapi_helper.php');

        $GLOBALS['tmpdir'] = '/tmp';

        $url = C('Plugins.Phplist.URL', '');
        $login = C('Plugins.Phplist.Login', '');
        $password = C('Plugins.Phplist.Password', '');
        $ListID = C('Plugins.Phplist.ListID', '');

        $Api = new phpListRestapi( $url );

        $result = $Api->login( $login, $password );
        if ($result->status != 'success'){
            echo $result->data->message;
            return;
        }

        $Sender->UserData = Gdn::SQL()->Select('User.Email')->From('User')->OrderBy('User.Name')->Where('Deleted',false)->Get();
        foreach ($Sender->UserData->Result() as $User) {
            $result = $Api->subscriberGetByEmail( $User->Email );
            if ($result->status != 'success'){
                    echo $result->data->message;
                    return;
            }
            $subscriber_id = $result->data->id;
            if (!$subscriber_id) {
                $result = $Api->subscriberAdd( $User->Email, 1, 1 );
                if ($result->status != 'success'){
                        echo $result->data->message;
                        return;
                }
                $subscriber_id = $result->data->id;

                $result = $Api->listSubscriberAdd( $ListID, $subscriber_id );
                if ($result->status != 'success'){
                        echo $result->data->message;
                        return;
                }
            }
        }
    }
    
    private function _SubscribeSingle($Controller, $EmailAddress, $ConfirmationEmail = TRUE) {
        if( !($Controller instanceof Controller) ) {
            $Controller = Gdn::Controller();
        }

        include_once(dirname(__FILE__) . DS . 'phplistrestapi' . DS .'phplist_restapi_helper.php');

        $GLOBALS['tmpdir'] = '/tmp';

        $url = C('Plugins.Phplist.URL', '');
        $login = C('Plugins.Phplist.Login', '');
        $password = C('Plugins.Phplist.Password', '');
        $ListID = C('Plugins.Phplist.ListID', '');

        $Api = new phpListRestapi( $url );

        $result = $Api->login( $login, $password );
        if ($result->status != 'success'){
            echo $result->data->message;
            return;
        }

        $result = $Api->subscriberGetByEmail( $User->Email );
        if ($result->status != 'success'){
                echo $result->data->message;
                return;
        }
        $subscriber_id = $result->data->id;
        if (!$subscriber_id) {
            $result = $Api->subscriberAdd( $EmailAddress, 1, 1, '#PasswordNotSet#' );
            if ($result->status != 'success'){
                    echo $result->data->message;
                    return;
            }
            $subscriber_id = $result->data->id;

            $result = $Api->listSubscriberAdd( $ListID, $subscriber_id );
            if ($result->status != 'success'){
                    echo $result->data->message;
                    $Controller->InformMessage(T('Subscription to our newsletter failed. Please try manually.') . T('EMSG=') . $result->data->message);
                    return;
            } else {
                if($ConfirmationEmail) {
                    $Controller->InformMessage(T('Please check our newsletter subscription confirmation email.'));
                }
            }
        }
    }

    public function UserModel_BeforeInsertUser_Handler($Sender) {
        if(C('Plugins.Phplist.Autosubscribe')) {
            // subscribe user using double optin email
            $this->_SubscribeSingle(
                $Sender, 
                $Sender->EventArguments['User']['Email']
            );
        } else {
            // subscribe only by user explicit request, no double optin email
            if($Sender->EventArguments['User']['Plugins.Phplist.OptIn']) {
                $this->_SubscribeSingle(
                    $Sender, 
                    $Sender->EventArguments['User']['Email'],
                    FALSE 
                );
            }
        }
    }

    /**
     * >= 2.1
     */
    public function EntryController_RegisterBeforePassword_Handler($Sender) {
        if(!C('Plugins.Phplist.Autosubscribe')) {
            echo Wrap(
                $Sender->Form->CheckBox('Plugins.Phplist.OptIn', T('Subscribe to the newsletter'), array('checked' => TRUE)),
                'li'
            );
        }
    }

    /**
    * 2.0 only - Replaces registration pages with custom pages (with optin/out selector)
    */
    public function EntryController_Render_Before($Sender) {
        if(version_compare(C('Vanilla.Version'), '2.1', '<')) {
            if (strtolower($Sender->RequestMethod) == 'register' 
                || strtolower($Sender->RequestMethod) == 'connect') { // only on registration/connect page
                $Sender->View = $this->GetView(strtolower($Sender->View).'.php');
            }
        }
    }
    
    public function Setup() {}
}