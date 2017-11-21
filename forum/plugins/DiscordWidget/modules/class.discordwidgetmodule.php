<?php
if (!defined('APPLICATION')) exit();

/**
 * Discord Widget module
 *
 * @package DiscordWidget
 * @author <a href="http://yaypaul.com">YayPaul (Paul West)</a>
 * @copyright 2017 Paul West.
 * @license MIT
 * @since 1.0
 */

/**
 * Renders the Discord Widget. Built for use in a side panel.
 */
class DiscordWidgetModule extends Gdn_Module {
   
    public function AssetTarget() {
        return 'Panel';
    }

    public function ToString() {  

        $ServerID = c( 'Plugin.DiscordWidget.ServerID' );
        $Theme = c( 'Plugin.DiscordWidget.Theme' );
        $Width = c( 'Plugin.DiscordWidget.Width' );
        $Height = c( 'Plugin.DiscordWidget.Height' );

        if( $ServerID == '' ) return '';

        echo '<div class="Box">';
        echo '<iframe 
            src="https://discordapp.com/widget?id=' . $ServerID . '&theme=' . $Theme . '" 
            width="' . $Width . '" 
            height="' . $Height . '" 
            class="DiscordWidget__iframe" 
            allowtransparency="true" 
            frameborder="0"></iframe>';
        echo '</div>';

    }
}

?>
