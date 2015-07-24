<?php if (!defined('APPLICATION')) exit();
echo '<h1>'.T('Karma Transaction').'</h1>';
?>
<table width="100%">
<tr>
<?php
foreach($this->Data['Trans'] As $TransN => $TransV){
    echo '<td>'.T($TransN).'</td>';
}
?>
</tr>
<tr>
<?php
foreach($this->Data['Trans'] As $TransN => $TransV){
    echo '<td>'.T($TransV).'</td>';
}
?>
</tr>
</table>

