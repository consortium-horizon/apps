<?php if (!defined('APPLICATION')) exit();

$PluginInfo['postonregister'] = array(
   'Name' => 'Post on register',
   'Description' => 'Create a new post each time a new user registers',
   'Version' => '0.1',
   'Author' => 'Vladvonvidden',
);

class postonregister extends Gdn_Plugin {

  public function userModel_afterRegister_handler($sender, $Args) {
    var_dump($sender);
  }
}
