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
        echo "<h2>Les administrateurs<h2>";
        echo "<p>Si t'as un problème technique, réfléchi et vide ton cache ... sinon, demande leur de l'aide.</p>";
        echo "<ul>";
        foreach ($this->data('admins') as $key => $value) {
            echo "<li>";
            echo "<a href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
        }
        echo "</ul>";
    }

    if ($this->data('conseillers')) {
        echo "<h2>Les conseillers<h2>";
        echo "<p>Les plus vénérables mammifères du Consortium, ils assurent le bon fonctionnement de la guilde.</p>";
        echo "<ul>";
        foreach ($this->data('conseillers') as $key => $value) {
            echo "<li>";
            echo "<a href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
        }
        echo "</ul>";
    }
?>
