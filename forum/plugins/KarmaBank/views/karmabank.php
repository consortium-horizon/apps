<?php if (!defined('APPLICATION')) exit(); ?>
<div class="Profile">
<?php
if ($this->Data['Balance']) {
?>
<ul class="DataList KarmaBank Karma">
<li class="Item KarmaTitles">
<div class="ItemContent">
<div class="Item"><?php echo T('KarmaBank.Transaction','Transaction') ?></div>
<div class="Description"><?php echo T('KarmaBank.Date','Date') ?></div>
<div class="Amount"> <span class="Karma"><?php echo T('KarmaBank.Karma','Karma') ?></span></div>
<div class="Clear"></div>
</div>
</li>
<?php foreach ($this->Data['Transactions'] As $Transaction) {
    $TransParts = split(' ',$Transaction->Type);
    $Trans=array();
    foreach($TransParts As $TransPart)
        $Trans[]=T(urldecode($TransPart));
    $TransOrder=T($Trans[0].'.Order',trim(join(' ',array_fill(0,count($Trans),'%s'))));
    $Trans = vsprintf($TransOrder,$Trans);
?>
<li class="Item KarmaTrans">
<div class="ItemContent">
<div class="Item"><?php echo $Trans ?></div>
<div class="Description">
<?php echo Gdn_Format::Date(strtotime($Transaction->Date),T('KarmaBank.DateDefaultFormat',T('Date.DefaultFormat')).' '.
T('KarmaBank.DateDefaultTimeFormat',T('Date.DefaultTimeFormat'))); ?>
</div>
<div class="Amount"><?php echo sprintf(T('KarmaBank.NumberFormat',"%01.2f"),$Transaction->Amount) ?><span class="Karma"></span></div>
<div class="Clear"></div>
</div>
</li>
<?php } ?>
<li class="Item KarmaBal">
<div class="ItemContent">
<div class="Item"><?php echo T('KarmaBank.Balance','Balance') ?></div>
<div class="Description"></div>
<div class="Amount"><?php echo sprintf(T('KarmaBank.NumberFormat',"%01.2f"),$this->Data['Balance']) ?><span class="Karma"><?php echo T('KarmaBank.Karma','Karma') ?></span></div>
<div class="Clear"></div>
</div>
</li>
</ul>
<?php
echo $this->Pager->Render();
} else {
   echo '<div class="Empty">'.T('KarmaBank.NoKarmaYet','You do not have any Karma yet.').'</div>';
}
if(Gdn::Session()->CheckPermission('Plugins.KarmaBank.RewardTax') || Gdn::Session()->User->Admin){
      echo $this->Form->Open();
      echo $this->Form->Errors();
?>
<div class="Configuration">
   <div class="ConfigurationForm">
      <ul>
        <li>
        <?php
        echo $this->Form->Label(T('KarmaBank.RewardTaxReason','Reason '),'RewardTaxReason');
        echo $this->Form->TextBox('RewardTaxReason',array('class'=>'SmallInput','maxlength'=>C('Plugins.KarmaBank.ReasonMaxLength',25)));
        echo $this->Form->Label(T('KarmaBank.Amount','Amount'),'RewardTax');
        echo $this->Form->TextBox('RewardTax',array('class'=>'SmallInput'));
        echo $this->Form->Button(T('KarmaBank.RewardTax','Reward / Tax'));
        ?>
        </li>
      </ul>
   </div>
</div>
<?php
    echo $this->Form->Close();
}
?>
</div>
