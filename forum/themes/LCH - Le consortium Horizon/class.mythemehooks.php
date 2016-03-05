<?php defined('APPLICATION') or die();
/**
 * Sample implementation of a theme hooks class to show
 * the use of custom Smarty plugins.
 */
class MyThemeThemeHooks implements Gdn_IPlugin {
    /**
     * Setup function is needed for this class, so don't delete it!
     *
     * @return bool Dummy return value.
     */
    public function setup() {
        return true;
    }
    /**
     * This function hooks the Smarty init to add our directory
     * containing our custom Smarty functions
     *
     * @param object $sender Smarty object.
     * @return void
     */
    public function gdn_smarty_init_handler($sender) {
        // add directory "/themes/MyTheme/SmartyPlugins/"
        $sender->plugins_dir[] = dirname(__FILE__).DS.'SmartyPlugins';
    }

    // Add JS files
    public function Base_Render_Before($Sender) {
       $Sender->AddJsFile('knockout.js');
       $Sender->AddJsFile('stickySidebar.js');
       $Sender->AddJsFile('init.js');
       $Sender->AddJsFile('converse.min.js');
    }

    //  add css?
    public function AssetModel_StyleCss_Handler($Sender) {
        // $Sender->AddCssFile('converse.min.css', 'themes/LCH - Le consortium Horizon');
        // $Sender->AddCssFile('converse.custom.css', 'themes/LCH - Le consortium Horizon');
    }

    public function Base_AfterBody_Handler($Sender, $Args) {
//         $UserName = Gdn::Session()->User->Name;
//         echo "
// <script>
// require(['converse'], function (converse) {
//     converse.plugins.add('myplugin', {
//         overrides: {
//             onConnected: function () {
//                 // Override the onConnected method in converse.js
//                 this._super.onConnected();
//                 var converse = this._super.converse;
//                 var jid = 'bar@chat.consortium-horizon.com';
//                 var chatbox = converse.rooms.get(jid);
//                 if (!chatbox) {
//                     converse.rooms.open(jid);
//                 }
//             },
//         },
//         initialize: function() {},
//     });
//     converse.initialize({
//         bosh_service_url: '/http-bind/',
//         prebind_url: '/forum/xmpp',
//         keepalive: true,
//         i18n: locales.fr, // Refer to ./locale/locales.js to see which locales are supported
//         show_controlbox_by_default: false,
//         roster_groups: true,
//         authentication: 'prebind',
//         jid: '{$UserName}@consortium-horizon.com/vanilla',
//         fullname: '{$UserName}',
//         show_only_online_users: true,
//         allow_registration: false,
//         auto_list_rooms: true,
//         message_carbons: true,
//         hide_offline_users: true,
//         debug: true,
//         hide_muc_server: true,
//         ping_interval: 60,
//         allow_otr: false,
//         auto_subscribe: true,
//         message_archiving: 'always',
//     });
//     var chatroom = converse.rooms.get('bar@chat.consortium-horizon.com');
//     if (!chatroom)
//     {
//         converse.rooms.open('bar@chat.consortium-horizon.com');
//         chatroom = converse.rooms.get('bar@chat.consortium-horizon.com');
//         chatroom.minimize();
//     }
// });
// </script>";
        //$Sender->InformMessage("XMPP BINDING: ".implode($sessionInfo));
    }

}
