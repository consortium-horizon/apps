<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
   <?php echo T('Add a new group.'); ?>
</div>
<div class="FilterMenu">
   <?php
	$Session = Gdn::Session();
	$EditUser = $Session->CheckPermission('Garden.Users.Edit');
	echo $this->Form->Open(array('action' => Url('plugin/groups/add')));
	echo $this->Form->Errors();
    echo '<p>';
    echo $this->Form->TextBox('Plugin.Groups.Name');
    echo $this->Form->Button(T('Add Group'));
    echo '</p>';
	echo $this->Form->Close();
   ?>
</div>
<?php 
if (C('Plugins.Groups.Enabled')) {
   echo "<h3>".T('Active Groups')."</h3>\n";
   echo '<div class="GroupContent">';
   $NumGroups = count($this->GroupList);
   if (!$NumGroups) {
      echo T("No groups have been created yet.");
   } else {
      echo $NumGroups." ".Plural($NumGroups,"item","items")." in active list\n";
      foreach ($this->GroupList as $item) {
?>
            <div class="GroupItem">
               <?php
                  ksort($GroupList,SORT_STRING);
               ?>
                        <div class="GroupTitleCell">
                           <div class="GroupItemName"><?php echo $item['Name']; ?></div>
                           <div class="GroupActions">
                              <?php 
								 echo Anchor(T('Edit'), 'plugin/groups/edit/'.$item['GroupID'], 'Popup SmallButton');
							     echo Anchor(T('Delete'), 'plugin/groups/delete/'.$item['GroupID'], 'RemoveItem SmallButton');
                                 
                              ?>
                           </div>
                        </div>
              
            </div>
   <?php
         }
      }
   ?>
</div>
<?php } 
?>