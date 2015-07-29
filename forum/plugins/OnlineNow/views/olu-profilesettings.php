<?php if (!defined('APPLICATION')) exit(); ?>
<h2><?php echo T('Qui est en ligne ? - Réglages'); ?></h2>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
   <li>
      <?php
         echo $this->Form->Label('Réglages');
         echo $this->Form->CheckBox('Plugin.OnlineNow.Invisible','Me rendre invisible ? (Votre avatar disparaîtra de la liste)');
      ?>
   </li>

</ul>
<?php echo $this->Form->Close('Sauvegarder');
