<h1><?= $this->Data('Title') ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>

<div class="Wrap"><?= Anchor(t('New Tour'), 'settings/forumtouraddedit', 'SmallButton') ?></div>

<?php
if (count($this->Data('ForumTour')) != 0) {
?>
<table class="AltColumns">
  <thead>
    <tr>
      <th>Step</th>
      <th>Title</th>
      <th>Description</th>
      <th>Options</th>
    </tr>
  </thead>

  <tbody>
    <?php
    $counter = 1;
    foreach($this->Data('ForumTour') as $ForumTour) {
      ?>
      <tr>
        <td><?= $counter ?>.</td>
        <td><?= $ForumTour['Title'] ?></td>
        <td><?= $ForumTour['Description'] ?></td>
        <td>
          <?= Anchor(t('Editer l\'étape'), 'settings/forumtouraddedit?title='.rawurlencode($ForumTour['Title']), 'SmallButton') ?>
          <?= Anchor(t('Supprimer l\'étape'), 'settings/forumtourdelete?title='.rawurlencode($ForumTour['Title']), 'SmallButton') ?>
        </td>
      </tr>
      <?php
      $counter++;
    }
    ?>
  </tbody>
</table>
<?php
}
?>
