<?php if (!defined('APPLICATION')) exit();

$PluginInfo['SaveAllRoles'] = array(
    'Name' => 'Save All Roles',
    'Description' => 'Circumvents max_input_vars and enables you to save a larger number of roles and custom category permissions.',
    'Version' => '0.1',
    'Author' => 'Bleistivt'
);

class SaveAllRolesPlugin extends Gdn_Plugin {

    // Serialization technique by @rubo77: https://gist.github.com/rubo77/6815945
    // Adapted for Vanilla by @bleistivt

    public function RoleController_Initialize_Handler($Sender) {
        $this->MergeSerializedData();
        $Sender->AddJsFile('serializeform.js', 'plugins/SaveAllRoles');
    }

    public function Settingscontroller_Initialize_Handler($Sender) {
        if ($Sender->ResolvedPath == 'vanilla/settings/editcategory') {
            $this->MergeSerializedData();
            $Sender->AddJsFile('serializeform.js', 'plugins/SaveAllRoles');
        }
    }

    private function MergeSerializedData() {
        if (isset($_POST['serialized_data'])){
            $this->my_parse_str($_POST['serialized_data'], $params);
            unset($_POST['serialized_data']);
            $_POST += $params;
        }
    }

    private function my_parse_str($string, &$result) {
        if ($string === '') return false;
        $result = array();
        $pairs = explode('&', $string);
        foreach ($pairs as $pair) {
            parse_str($pair, $params);
            $k = key($params);
            if (!isset($result[$k])) $result += $params;
            else $result[$k] = array_merge_recursive($result[$k], $params[$k]);
        }
        return true;
    }

}
