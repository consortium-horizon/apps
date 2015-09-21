<!-- Template file used for settings view -->

<h1><?= $this->Data('Title') ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>

<div class="Wrap"><?= Anchor(T('Plugins.ForumTour.NewStep', 'New step'), 'settings/forumtouraddedit', 'SmallButton') ?></div>

<?php if (count($this->Data('ForumTour')) != 0) {
    echo "<table class='AltColumns'>";
    echo "<thead><tr>";
    echo "<th>".T('Plugins.ForumTour.Step', 'Step')."</th>";
    echo "<th>".T('Plugins.ForumTour.StepTitle', 'Title')."</th>";
    echo "<th>".T('Plugins.ForumTour.StepDescription', 'Description')."</th>";
    echo "<th>".T('Plugins.ForumTour.StepOptions', 'Options')."</th>";
    echo "</tr></thead>";
    echo "<tbody>";

    $counter = 1;

    foreach ($this->Data('ForumTour') as $ForumTour) {
        echo "<tr>";
        echo "<td>" . $counter . "</td>";
        echo "<td>" . $ForumTour['Title'] . "</td>";
        echo "<td>" . $ForumTour['Description'] . "</td>";
        echo "<td>" . Anchor(T('Plugins.ForumTour.StepEdit', 'Edit'), 'settings/forumtouraddedit?title='.rawurlencode($ForumTour['Title']), 'SmallButton') .
        Anchor(T('Plugins.ForumTour.StepDelete', 'Delete'), 'settings/forumtourdelete?title='.rawurlencode($ForumTour['Title']), 'SmallButton');
        echo "</td>";
        echo "</tr>";

        $counter++;
    }

    echo "</tbody></table>";

}
