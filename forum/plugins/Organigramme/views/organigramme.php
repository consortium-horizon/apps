<?php defined('APPLICATION') or die;?>

<h2>Bonjour à toi, <?= $this->data('name') ?>, qui consulte cette page en construction (petit gredin)</h2>

<?php
    if ($this->data('admins')) {
        echo "<h2>Les administrateurs</h2>";
        echo "<p>Si t'as un problème technique, réfléchi et vide ton cache ... sinon, demande leur de l'aide.</p>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('adminspics');
        foreach ($this->data('admins') as $key => $value) {
            echo "<li>";
            echo $pics[$count];
            echo "<a href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
    }

    if ($this->data('conseillers')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Le conseil</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les Conseillers</h2>";
        echo "<div class='Description'>Les plus vénérables mammifères du Consortium, ils assurent le bon fonctionnement de la guilde.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('conseillerspics');
        foreach ($this->data('conseillers') as $key => $value) {
            echo "<li class='conseiller'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('modos')) {
        echo "<h2>Les modérateurs globaux</h2>";
        echo "<p>Les gardiens du très saint banhammer.</p>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('modospics');
        foreach ($this->data('modos') as $key => $value) {
            echo "<li>";
            echo $pics[$count];
            echo "<a href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
    }

    if ($this->data('refPS2')) {
        echo "<h2>Les référents Planetside 2</h2>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refPS2pics');
        foreach ($this->data('refPS2') as $key => $value) {
            echo "<li>";
            echo $pics[$count];
            echo "<a href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
    }
?>
