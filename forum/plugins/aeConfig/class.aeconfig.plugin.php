<?php

$PluginInfo['aeConfig'] = array(
    'Name' => 'Advanced Editor Config',
    'Description' => 'Allows to configure the formatting options of the advanced editor.',
    'Version' => '0.2',
    'RequiredApplications' => array('Vanilla' => '>= 2.2'),
    'RequiredPlugins' => array('editor' => '>= 1.7.2'),
    'RequiredTheme' => false,
    'SettingsPermission' => 'Garden.Settings.Manage',
    'SettingsUrl' => '/dashboard/settings/aeconfig',
    'MobileFriendly' => true,
    'HasLocale' => false,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/44046/R_J',
    'License' => 'MIT'
);

/**
 * Allows configuring the formatting options of Vanillas advanced editor.
 *
 * @package aeConfig
 * @author Robin Jurinka
 * @license MIT
 */
class AEConfigPlugin extends Gdn_Plugin {
    /**
     * Add options to config when plugin is installed.
     *
     * @return void.
     */
    public function setup() {
        $editorPlugin = new EditorPlugin();
        $allowedEditorActions = $editorPlugin->getAllowedEditorActions();
        foreach ($allowedEditorActions as $action => $bool) {
            saveToConfig('aeConfig.'.$action, (bool)$bool);
        }
    }


    /**
     * Show all formatting options of the advanced editor.
     *
     * @param  settingsController $sender The sending controller.
     * @return void.
     * @package aeConfig
     * @since 0.1
     */
    public function settingsController_aeconfig_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        
        $sender->addSideMenu('dashboard/settings/plugins');
        $sender->setData('Title', t('Advanced Editor Settings'));

        $configuration = [];
        $editorPlugin = new EditorPlugin();
        $allowedEditorActions = $editorPlugin->getAllowedEditorActions();
        foreach ($allowedEditorActions as $action => $bool) {
            $configuration['aeConfig.'.$action] = [
                'Control' => 'CheckBox'
            ];
        }
        
        $configurationModule = new ConfigurationModule($sender);
        $configurationModule->initialize($configuration);
        $configurationModule->renderAll();
    }

    /**
     * Change editors default configuration based on the custom settings.
     *
     * @param baseController $sender The sending controller.
     * @param mixed $args EventArguments.
     * @return void.
     * @package aeConfig
     * @since 0.1
     */
    public function base_toolbarConfig_handler($sender, $args) {
        $configuration = c('aeConfig');
        if ($configuration) {
            $args['actions'] = $configuration;
        }
    }
}
