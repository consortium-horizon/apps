<?php if (!defined('APPLICATION')) exit();?>
<h1>Discord</h1>
<?php
$Text = '';
$FormText = '';
if ($this->data('isConnected'))
{
    $Text = 'Connecté en tant que:<br><img alt="" border="0" src="'.$this->data('DiscordProfile')->{'photoURL'}.'" width="35" height="35"> '.$this->data('DiscordProfile')->{'displayName'};
    $FormText = 'Disconnect';
}
else
{
    $Text = 'Non connecté';
    $FormText = 'Connect';
}
echo $this->Form->Open();
echo $this->Form->Errors();
echo $Text;
$this->Form->AddHidden('Action', 'ToggleConnection', true);
echo $this->Form->getHidden();
echo $this->Form->Close($FormText);
echo $this->Form->Open();
echo $this->Form->Errors();
$this->Form->AddHidden('Action', 'SyncRole', true);
echo $this->Form->getHidden();
echo $this->Form->Close('Sync With Discord');
echo '<br>';
?>