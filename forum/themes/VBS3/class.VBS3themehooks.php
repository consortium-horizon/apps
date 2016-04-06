<?php if (!defined('APPLICATION')) exit();

/**
 * This theme was ported from Vanilla Forums Inc's hosted theme "Bootstrap" it has been modified to merge with Bootstrap 3 ( from 2.3 ) and has been designed for use on the OS model.
 *
 * @copyright Vanilla Forums
 * @author Chris Ireland (Adapation)
 */
class VBS3ThemeHooks extends Gdn_Plugin
{
    // Add the meta viewport tag for mobile ajustments
    public function Base_Render_Before($Sender)
    {
        $Sender->Head->AddTag('meta', array('name' => 'viewport', 'content' => 'initial-scale=1'));
    }

    public function Gdn_Dispatcher_AfterAnalyzeRequest_Handler($Sender)
    {
        // Remove plugins so they don't mess up layout or functionality.
        if (in_array($Sender->Application(), array('vanilla', 'conversations')) && IsMobile() || ($Sender->Application() == 'dashboard' && in_array($Sender->Controller(), array('Activity', 'Profile', 'Search')))) {
            Gdn::PluginManager()->RemoveMobileUnfriendlyPlugins();
        }

        SaveToConfig('Garden.Format.EmbedSize', '240x135', FALSE);
    }
}