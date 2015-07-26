<?php if (!defined('APPLICATION')) exit();

if(!C('Plugins.Phplist.Autosubscribe')) {
    echo "<li>";
    echo $this->Form->CheckBox('Plugins.Phplist.OptIn', T('Subscribe to the newsletter'), array('checked' => TRUE));
    echo "</li>";
}

?>