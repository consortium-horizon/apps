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
   public function Setup() {
   }


    public function Base_AfterSignIn_Handler($Sender, $Args){
        
        $Sender->InformMessage(implode("|",$Args));

        $Session = Gdn::Session();

        //Connect xmpp
    }


    public function HomeController_BeforeRender_Handler($Sender, $Args){
        
        $Sender->InformMessage(implode("|",$Args));

        $Session = Gdn::Session();

        //auth xmpp
    }    


}
