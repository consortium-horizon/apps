<?php defined('APPLICATION') or die;?>

<h2>Bonjour à toi, <?= $this->data('name') ?>, qui consulte cette page en construction (petit gredin)</h2>

<?php
    if ($this->data('admins')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Admins</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les administrateurs</h2>";
        echo "<div class='Description'>Si t'as un problème technique, réfléchi et vide ton cache ... sinon, demande leur de l'aide.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('adminspics');
        foreach ($this->data('admins') as $key => $value) {
            echo "<li class='admins'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('conseillers')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>conseil</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les conseillers</h2>";
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
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Modos</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les modérateurs globaux</h2>";
        echo "<div class='Description'>Les gardiens du très saint banhammer.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('modospics');
        foreach ($this->data('modos') as $key => $value) {
            echo "<li class='modos'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('refAlbion')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Albion Online</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les référents Albion Online</h2>";
        echo "<div class='Description'>Description à compléter.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refAlbionpics');
        foreach ($this->data('refAlbion') as $key => $value) {
            echo "<li class='refAlbion'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('refArma')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Arma 3</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les référents Arma 3</h2>";
        echo "<div class='Description'>Description à compléter.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refArmapics');
        foreach ($this->data('refArma') as $key => $value) {
            echo "<li class='refArma'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('refPS2')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Planetside 2</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les référents Planetside 2</h2>";
        echo "<div class='Description'>Bande de casus ! Si vous avez des questions, vous n'aviez qu'a être meilleurs ! (utilisez le forum quand même !)</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refPS2pics');
        foreach ($this->data('refPS2') as $key => $value) {
            echo "<li class='refPS2'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('refSkyforge')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Skyforge</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les référents Skyforge</h2>";
        echo "<div class='Description'>Description à compléter.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refSkyforgepics');
        foreach ($this->data('refSkyforge') as $key => $value) {
            echo "<li class='refSkyforge'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    if ($this->data('refSC')) {
        echo "<div class='OrgContainer'>";
        echo "<div class='OrgContainerPic'><span>Star citizen</span></div>";
        echo "<div class='OrgContainerContent'>";
        echo "<h2 class='Title'>Les référents Star Citizen</h2>";
        echo "<div class='Description'>Description à compléter.</div>";
        echo "<ul>";
        $count = 0;
        $pics = $this->data('refSCpics');
        foreach ($this->data('refSC') as $key => $value) {
            echo "<li class='refSC'>";
            echo $pics[$count];
            echo "<a class='username' href='../forum/profile/" . $value->UserID . "/" . $value->Name ."'>" . $value->Name . "</a>";
            echo "</li>";
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }
?>
