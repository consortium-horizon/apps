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

class ForumTourPlugin extends Gdn_Plugin {

  /**
  * Get title for a specific step.
  *
  * @param $Name
  * @return array
  */

  public function settingsController_ForumTour_create($sender) {
    $sender->permission('Garden.Settings.Manage');
    $sender->addSideMenu('/dashboard/settings/forumtour');

    // To make some things more clear, I rewrite some lines
    // $sender->title(t('Forum tour settings'));
    // $sender->description('Forum Tour Description');
    $sender->setData('Title', t('Forum tour settings'));
    $sender->setData('Description', t('Forum Tour Description'));

    // This one gets all config values that are prefixed with 'ForumTour' and stores them for later access. It defaults to an empty array if there is no information in the config.
    $sender->setData('ForumTour', c('ForumTour', array()));

    /*
    Some explanation concerning "setData". It is used to store some values that could be later on used in the view. In the philosophy of MVC, the view should not fetch any data from other sources. So it's only source is what is provided to the view. In Vanilla this is done by using $sender->setData('key', 'value') in the controller and $value = $this->Data('key') in the view. $sender->title() and $sender->description() are only shortcodes for the corresponding key.
    Getting data from the config is from its character more a "Model" action so I did this here in the controller.
    */

    $sender->Render('settings', '', 'plugins/ForumTour');
  }

  /**
  * Add/edit a field.
  */
  public function SettingsController_ForumTourAddEdit_Create($sender, $args) {
    $sender->Permission('Garden.Settings.Manage');
    $sender->addSideMenu('/dashboard/settings/forumtour');

    $sender->SetData('Title', T('Ajouter'));
    $sender->SetData('Description', T('Ceci est la description'));

    // No need to add a form, because we extend settingsController who already attaches a form
    // No need to add a validation since model can "lend" some validation functions
    // No need for the configuration model, since that is only for complex save processes to the config and we only do a simple save :)

    $ForumTour = array();
    // If something has been passed to our function, we want to _edit_ something

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
    // If a step should be added, nothing is passed to this function so we fill it with dummy values. This would be a good place to set "defaults" for new entries
    // If something failed at the if condition above, we fill in blank values.
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
    // Now we handle what happens after the save button has been pressed
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

    // The only "magic" happens here. If we have an array where the "key" corresponds to the name of the form controlls, the values of that array are prepopulation the form! Vanilla is great...
    $sender->Form->setData($ForumTour);

    $sender->Render('addedit', '', 'plugins/ForumTour');
  }

  /**
  * Remove a step.
  */
  public function SettingsController_ForumTourDelete_Create($sender, $args) {
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


  // Render the tutorial on the forum
  public function Base_Render_Before($sender, $args) {
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
