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

    // public function Base_AfterSignIn_Handler($Sender, $Args){

    //     $Sender->InformMessage(implode("|",$Args));

    //     $Session = Gdn::Session();

    //     //Connect xmpp
    // }

    //public function RootController_xmpp_Create($Sender, $Args) {
    public function Base_AfterBody_Handler($Sender, $Args) {

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
        $sessionInfo['sid'];
        $sessionInfo['rid'];
        $sessionInfo['jid'];

        //$Sender->InformMessage("XMPP BINDING: ".implode($sessionInfo));
    }


}
