<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<h1><?php echo T("Utilisateurs en ligne"); ?></h1>
      <div class="Info"><?php echo T('Ou doit on afficher la liste ?'); ?></div>
      <table class="AltRows">
         <thead>
            <tr>
               <th><?php echo T('Sections'); ?></th>
               <th class="Alt"><?php echo T('Description'); ?></th>
            </tr>
         </thead>
         <tbody>
               <tr>
                  <th><?php
                     echo $this->Form->Radio('Plugins.OnlineNow.Location.Show', "Toutes", array('value' => 'every', 'selected' => 'selected'));
                  ?></th>
                  <td class="Alt"><?php echo T("Afficher le panneau sur toutes les pages."); ?></td>
               </tr>
                <tr>
                     <th><?php
                        echo $this->Form->Radio('Plugins.OnlineNow.Location.Show', "Discussion", array('value' => "discussion"));
                     ?></th>
                     <td class="Alt"><?php echo T("Afficher le panneau sur la page des discussions seulement"); ?></td>
                </tr>
         </tbody>
      </table>
			<table class="AltRows">  
         <tbody>
               <tr>
                  <th><?php
                     echo $this->Form->Checkbox('Plugins.OnlineNow.Hide', "Masquer pour les utilisateurs externes");
                  ?></th>
               </tr>             
         </tbody>
      </table>
      <table class="AltRows">
         <thead>
            <tr>
               <th><?php echo T('Fréquence'); ?></th>
               <th class="Alt"><?php echo T('En secondes'); ?></th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <th><?php echo T('Taux de rafraichissement'); ?></th>
               <td class="Alt"><?php echo $this->Form->TextBox('Plugins.OnlineNow.Frequency'); ?></td>
            </tr>
         </tbody>
      </table>

<?php echo $this->Form->Close('Sauvegarder');
