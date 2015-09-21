<?php if (!defined('APPLICATION')) exit();

$PluginInfo['ForumTour'] = array(
    'Name' => 'Forum tour',
    'Description' => 'Create a tutorial interface for your forum. #$wag',
    'Version' => '0.1',
    'Author' => 'Vladvonvidden',
    'SettingsUrl' => '/dashboard/settings/forumtour',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'License'=>"GNU GPL2"
);

class ForumTourPlugin extends Gdn_Plugin
{

    /**
    * Get title for a specific step.
    *
    * @param $Name
    * @return array
    */

    public function settingsController_ForumTour_create($sender)
    {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('/dashboard/settings/forumtour');
        $sender->setData('Title', t('Forum tour settings'));
        $sender->setData('Description', t('Forum Tour Description'));
        $sender->setData('ForumTour', c('ForumTour', array()));
        $sender->Render('settings', '', 'plugins/ForumTour');
    }

    /**
    * Add/edit a field.
    */
    public function SettingsController_ForumTourAddEdit_Create($sender, $args)
    {
        $sender->Permission('Garden.Settings.Manage');
        $sender->addSideMenu('/dashboard/settings/forumtour');
        $sender->SetData('Title', T('Ajouter'));
        $sender->SetData('Description', T('Ceci est la description'));

        $ForumTour = array();

        $title = $sender->Request->get('title');
        if ($title != '') {
            // First, we fetch _all_ steps from the config.
            $ForumTourConfig = c('ForumTour', array());
            // Then we walk through the steps one by one
            foreach ($ForumTourConfig as $key => $value) {
                // Check if one title matches the title we wanted to change
                if ($value['Title'] == $title) {
                    // If so, we have the part of the array that we want to change!
                    $ForumTour = $value;
                }
            }
        }

        if ($ForumTour == array() || $title == '') {
            $ForumTour['Title'] = 'Step Title';
            $ForumTour['Description'] = 'Step description';
            $ForumTour['XPosition'] = '0';
            $ForumTour['XPositionType'] = 'px';
            $ForumTour['YPosition'] = '0';
            $ForumTour['YPositionType'] = '%';
            $ForumTour['TooltipPosition'] = 'right';
            $ForumTour['PositionMethod'] = 'vanillaelement';
            $ForumTour['VanillaTarget'] = 'Panel';
            $ForumTour['CustomElement'] = 'Panel';
        }

        if ($sender->Form->authenticatedPostBack() === true) {
            // Here are some validations
            $sender->Form->validateRule('Title', 'ValidateRequired');
            $sender->Form->validateRule('Title', 'ValidateString');
            $sender->Form->validateRule('Description', 'ValidateRequired');
            $sender->Form->validateRule('Description', 'ValidateString');
            $sender->Form->validateRule('XPosition', 'ValidateRequired');
            $sender->Form->validateRule('XPosition', 'ValidateInteger');
            $sender->Form->validateRule('XPositionType', 'ValidateRequired');
            $sender->Form->validateRule('YPosition', 'ValidateRequired');
            $sender->Form->validateRule('YPosition', 'ValidateInteger');
            $sender->Form->validateRule('YPositionType', 'ValidateRequired');
            $sender->Form->validateRule('TooltipPosition', 'ValidateRequired');
            $sender->Form->validateRule('PositionMethod', 'ValidateRequired');
            $sender->Form->validateRule('VanillaTarget', 'ValidateRequired');
            $sender->Form->validateRule('CustomElement', 'ValidateRequired');

            if ($sender->Form->errorCount() == 0) {
                // YEAH! Zero errors!
                $ForumTourConfig = c('ForumTour', array());
                $postValues = $sender->Form->formValues();
                $index = -1;
                foreach ($ForumTourConfig as $key => $value) {
                    if ($value['Title'] == $postValues['Title']) {
                        // Store where to save the modified entry
                        $index = $key;
                    }
                }

                $ForumTour['Title'] = $postValues['Title'];
                $ForumTour['Description'] = $postValues['Description'];
                $ForumTour['PositionMethod'] = $postValues['PositionMethod'];
                $ForumTour['VanillaTarget'] = $postValues['VanillaTarget'];
                $ForumTour['CustomElement'] = $postValues['CustomElement'];
                $ForumTour['XPosition'] = $postValues['XPosition'];
                $ForumTour['XPositionType'] = $postValues['XPositionType'];
                $ForumTour['YPosition'] = $postValues['YPosition'];
                $ForumTour['YPositionType'] = $postValues['YPositionType'];
                $ForumTour['TooltipPosition'] = $postValues['TooltipPosition'];

                if ($index == -1) {
                    // This should be a new item which should be added to the list
                    $ForumTourConfig[] = $ForumTour;
                } else {
                    // Or replace an existing one if titles match
                    $ForumTourConfig[$index] = $ForumTour;
                }

                saveToConfig('ForumTour', $ForumTourConfig);

                $sender->StatusMessage = t('Your settings have been saved.');
                redirect(Gdn::request()->url('settings/forumtour'));
            }
        }

        $sender->Form->setData($ForumTour);
        $sender->Render('addedit', '', 'plugins/ForumTour');
    }

    /**
    * Remove a step.
    */
    public function SettingsController_ForumTourDelete_Create($sender, $args)
    {
        $sender->Permission('Garden.Settings.Manage');
        $ForumTour = array();
        $title = $sender->Request->get('title');
        if ($title != '') {
            // First, we fetch _all_ steps from the config.
            $ForumTourConfig = c('ForumTour', array());
            // Then we walk through the steps one by one
            foreach ($ForumTourConfig as $key => $value) {
                // Check if one title matches the title we wanted to change
                if ($value['Title'] == $title) {
                    unset($ForumTourConfig[$key]);
                }
            }
        }
        // Write back the new values to the config
        saveToConfig('ForumTour', $ForumTourConfig);
        // "Redirect" to the list view to show the short and cleaned list
        redirect(Gdn::request()->url('settings/forumtour'));
    }

    /**
    * Render the tutorial and load assets.
    */
    public function Base_Render_Before($sender, $args)
    {
        // If not on dashboard, then we display the tutorial
        if ($sender->MasterView != 'admin') {
            // Load the assets
            $sender->AddCssFile('forumtour.css', 'plugins/ForumTour');
            $sender->AddjsFile('forumtour.js', 'plugins/ForumTour');
            $sender->setData('ForumtourData', c('ForumTour', array()));
            // Load the module
            $sender->AddModule(new ForumTourMenuModule());
        } else {
            // Load the dashboard assets
            $sender->AddCssFile('forumtourAdmin.css', 'plugins/ForumTour');
            $sender->AddjsFile('forumtourAdmin.js', 'plugins/ForumTour');
        }
    }
}
