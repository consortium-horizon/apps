<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['YagaRuleAddition'] = array(
   'Name' => 'Yaga Rule Addition',
   'Description' => 'Add rules to Yaga Application',
   'Version' => '1.0',
   'Author' => "Yaga"
);

class YagaRuleAdditionPlugin extends Gdn_Plugin {
    public function Yaga_AfterGetRules_Handler($Sender) {
      $Rule = new DiscussionWordMention();
      $Sender->EventArguments['Rules'][get_class($Rule)] = $Rule->Name();
    }
}
