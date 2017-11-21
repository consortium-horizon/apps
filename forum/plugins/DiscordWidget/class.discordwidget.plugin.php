<?php

$PluginInfo[ 'DiscordWidget' ] = [
    'Name' => 'Discord Widget',
    'Description' => 'Creates a settings page to setup your Discord widget and adds it to your sidebar.',
    'Version' => '1.0',
    'RequiredApplications' => array( 'Vanilla' => '2.3' ),
    'SettingsUrl' => '/dashboard/settings/discordwidget',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'RequiredPlugins' => FALSE,
    'RequiredTheme' => FALSE,
    'MobileFriendly' => TRUE,
    'RegisterPermissions' => array(
        'Plugins.DiscordWidget.Add',
        'Plugins.DiscordWidget.Manage',
        'Plugins.DiscordWidget.Notify',
        'Plugins.DiscordWidget.View'
    ),
    'Author' => '<a href="http://yaypaul.com">YayPaul (Paul West)</a>',
    'AuthorUrl' => 'https://open.vanillaforums.com/profile/YayPaul',
    'License' => 'MIT'
];

/**
 * Discord Widget
 *
 * Plugin that creates a settings page to setup your 
 * Discord widget and adds it to your sidebar.
 *
 * @package DiscordWidget
 * @author <a href="http://yaypaul.com">YayPaul (Paul West)</a>
 * @copyright 2017 Paul West.
 * @license MIT
 */
class DiscordWidget extends Gdn_Plugin {

	/**
     * Plugin constructor
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function __construct() {

    }

    /**
     * Plugin setup
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function setup() {

        // Set up the plugin's default values
        saveToConfig( 'Plugin.DiscordWidget.ServerID', "" );
        saveToConfig( 'Plugin.DiscordWidget.Theme', "dark" );
        saveToConfig( 'Plugin.DiscordWidget.Width', "300" );
        saveToConfig( 'Plugin.DiscordWidget.Height', "400" );
        saveToConfig( 'Plugin.DiscordWidget.ForceTop', "bottom" );

        // Trigger database changes
        $this->structure();

    }

    /**
     * Plugin Structure
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function structure() {

    }

    /**
     * Plugin cleanup
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function onDisable() {

        removeFromConfig( 'Plugin.DiscordWidget.ServerID' );
        removeFromConfig( 'Plugin.DiscordWidget.Theme' );
        removeFromConfig( 'Plugin.DiscordWidget.Width' );
        removeFromConfig( 'Plugin.DiscordWidget.Height' );
        removeFromConfig( 'Plugin.DiscordWidget.ForceTop' );

        // Never delete from the database OnDisable.
        // Usually, you want re-enabling a plugin to be as if it was never off.

    }

    /**
     * CSS/JS Event Hooks
     *
     * @param $Sender Sending controller instance
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function assetModel_styleCss_handler( $Sender ) {
        $Sender->addCssFile('view.css', 'plugins/DiscordWidget');
    }

	/**
	 * Default action on /discussion/poll is not found
	 *
	 * @param $Sender Sending controller instance
	 *
     * @package DiscordWidget
     * @since 1.0
	 */
	public function controller_index( $Sender ) {

		//shift request args for implied method
		array_unshift( $Sender->RequestArgs, NULL );
		$this->Controller_Results( $Sender );

	}

    /**
     * Discord Widget Controller
     *
     * @param $Sender Sending controller instance
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function settingsController_DiscordWidget_create( $Sender ) {

    	// Prevent non-admins from accessing this page
        $Sender->permission( 'Garden.Settings.Manage' );
        $Sender->addCSSFile('settings.css', 'plugins/DiscordWidget');
        $Sender->setData( 'PluginDescription',$this->getPluginKey( 'Description' ) );

        $Sender->Form = new Gdn_Form();

        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel( $Validation );
        $ConfigurationModel->setField( array(
            'Plugin.DiscordWidget.ServerID'	=> '',
            'Plugin.DiscordWidget.Theme' => 'dark',
            'Plugin.DiscordWidget.Width' => '300',
            'Plugin.DiscordWidget.Height' => '400',
            'Plugin.DiscordWidget.ForceTop' => 'bottom'
        ) );

        // Set the model on the form.
        $Sender->Form->setModel( $ConfigurationModel );

        // If seeing the form for the first time...
        if( $Sender->Form->authenticatedPostBack() === false ) {
            // Apply the config settings to the form.
            $Sender->Form->setData( $ConfigurationModel->Data );
		}
		else {
            //Validate
            $ConfigurationModel->Validation->applyRule( 'Plugin.DiscordWidget.ServerID', 'Required' );
            //Save
            $Saved = $Sender->Form->save();
            //if( $Saved ) {
                $Sender->StatusMessage = t( "Your changes have been saved." );
            
                //Sort module position based on setting
                $ModuleSort = c( 'Modules.Vanilla.Panel' );
                $ModuleSort = preg_replace( '/\DiscordWidgetModule\b/', '', $ModuleSort); //Remove module from list
                $ModuleSort = array_filter( $ModuleSort ); //Clear empty slots
                $ForceTop = c( 'Plugin.DiscordWidget.ForceTop' );
                if( $ForceTop == 'top' ){
                    array_unshift( $ModuleSort, 'DiscordWidgetModule' ); //Add module to front
                }
                SaveToConfig( 'Modules.Vanilla.Panel', $ModuleSort ); //Save the setting
            //}
        }

        $Sender->addSideMenu( '/dashboard/settings/discordwidget' );
        $Sender->title( 'Discord Widget' );
        $Sender->render( $this->getView( 'settings.php' ) );

    }

    /**
     * Discord Widget Renderer
     *
     * @param $Sender Sending controller instance
     *
     * @package DiscordWidget
     * @since 1.0
     */
    public function Base_Render_Before( $Sender ) {

        $Controller = $Sender->ControllerName;
        $ShowOnController = array(
            'discussioncontroller',
            'categoriescontroller',
            'discussionscontroller',
            'profilecontroller',
            'activitycontroller'
        );

        if( !InArrayI( $Controller, $ShowOnController ) ) return; 

        $Sender->AddCssFile( 'view.css', 'plugins/DiscordWidget' );

        $DiscordWidgetModule = new DiscordWidgetModule( $Sender );
        $Sender->AddModule( $DiscordWidgetModule );

    }

}

?>