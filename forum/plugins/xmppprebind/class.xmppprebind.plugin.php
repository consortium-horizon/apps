<?php if (!defined('APPLICATION')) exit;

$PluginInfo['xmppprebind'] = array(
    'Name'        => "XMPP-Prebind",
    'Description' => "XMPP-Prebind allows you to establish and maintain a bosh connection to a xmpp server",
    'Version'     => '1.0.0',
    'PluginUrl'   => 'https://github.com/pkonecki/vanilla-xmpp-prebind',
    'Author'      => "Pierre-Olivier Konecki",
    'AuthorEmail' => 'pkonecki@gmail.com',
    'AuthorUrl'   => 'https://github.com/pkonecki',
    'License'     => 'MIT',
    'RequiredApplications' => array('Vanilla' => '2.x.x')
);

/**
 * XMPP-Prebind Plugin
 *
 * @author    Pierre-Olivier Konecki <pkonecki@gmail.com>
 * @copyright 2014 (c) Pierre-Olivier Konecki
 * @license   MIT
 * @package   XMPP-Prebind
 * @since     1.0.0
 */
class XMPPPrebindPlugin extends Gdn_Plugin
{

    public function __construct() {
        require_once(dirname(__FILE__).'/library/XmppPrebind.php');
    }

    public function Setup() {
    }

    public function RootController_xmpp_Create($Sender, $Args) {
        if (!Gdn::Session()->IsValid())
            return;

        $UserName = Gdn::Session()->User->Name;
        $UserID = Gdn::Session()->UserID;

        $SecretKey = "Secret";
        $Secret = $this->GetUserMeta($UserID, $SecretKey, NULL, true);
        if (!$Secret || empty($Secret)){
            $Secret = md5(uniqid(rand(), true));
            $this->SetUserMeta($UserID, $SecretKey, $Secret );
        }

        $xmppPrebind = new XmppPrebind('consortium-horizon.com', 'http://www.consortium-horizon.com/http-bind/', 'vanilla'.rand(), false, false);
        try{
            $xmppPrebind->connect($UserName, $Secret);
            $xmppPrebind->auth();
        } catch (XmppPrebindException $e) {
            //echo $e->getMessage()."<br>";
            //$Sender->InformMessage($e->getMessage());
            return;
        }

        $sessionInfo = $xmppPrebind->getSessionInfo(); // array containing sid, rid and jid
        $sid = $sessionInfo['sid'];
        $rid = $sessionInfo['rid'];
        $jid = $sessionInfo['jid'];
        header('Content-Type: application/json');
        echo json_encode($sessionInfo);
    }

    public function assetModel_styleCss_handler($Sender) {
        $Sender->addCssFile('mini.css', 'plugins/xmppprebind');
    }

    public function base_render_before($Sender, $Args) {
        $Session = Gdn::Session();
            if ($Session->IsValid()) {
                $UserName = Gdn::Session()->User->Name;
                $UserID = Gdn::Session()->UserID;

                $SecretKey = "Secret";
                $Secret = $this->GetUserMeta($UserID, $SecretKey, NULL, true);
                if (!$Secret || empty($Secret)){
                    $Secret = md5(uniqid(rand(), true));
                    $this->SetUserMeta($UserID, $SecretKey, $Secret );
                }
                $Sender->addJsFile('mini.js', 'plugins/xmppprebind');
                $js = '


                  jQuery(document).ready(function() {
                    JAPPIX_STATIC = "/forum/plugins/xmppprebind/";
                    HOST_BOSH = "https://www.consortium-horizon.com/http-bind/";
                    JappixMini.launch({
                      connection: {
                        user: \''.$UserName.'\',
                        password: \''.$Secret.'\',
                        domain: \'consortium-horizon.com\'
                      },
                      application: {
                        network: {
                          autoconnect: false
                        },
                        interface: {
                          showpane: true,
                          animate: true
                        },
                        groupchat: {
                            open: ["bar@chat.consortium-horizon.com"]
                        }
                      }
                    });
                  });
                ';
                $Sender->Head->addTag('script', array('type' => 'text/javascript', '_sort' => 100), $js);

            }
    }

}
