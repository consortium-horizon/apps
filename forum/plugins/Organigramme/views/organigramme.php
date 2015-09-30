<?php defined('APPLICATION') or die;
if ($this->data('hasArguments')) {
    $linkSuggestion = Gdn_Theme::Link($this->SelfUrl, 'no parameters');
} else {
    $linkSuggestion = Gdn_Theme::Link($this->SelfUrl.'/Anonymous', 'parameters');
}
$alternative
?>
<h2>Bonjour à toi, <?= $this->data('name') ?>, qui consulte cette page en construction (petit gredin)</h2>

<?php
    if ($this->data('admins')) {
        echo "<ul>";
        foreach ($this->data('admins') as $key => $value) {
            echo "<li>";
            echo "<a href='../vanilla/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
        }
        echo "</ul>";
    }
?>
