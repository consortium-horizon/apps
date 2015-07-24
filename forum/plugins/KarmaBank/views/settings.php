<style>
    form ul li span{
        display:inline;
    }
    dl dt,.spec{
        font-weight:bold;
    }
    dl dd{
        padding-left:10px;
        padding-bottom:10px;
    }
    fieldset{
        border:1px solid #CCCCCC;
        width:350px;
        padding:5px;
        margin-bottom:6px;
    }
    
    #Content form input.KarmaButton{
        margin-left:380px;
        position:absolute;
        margin-top:-38px;
    }
    
</style>
<h1><?php echo $this->Data['Title']; ?></h1>
<div class="Info">
   <?php echo $this->Data['Description']; ?>
</div>
<div>
<?php
      echo $this->Form->Open();
      echo $this->Form->Errors();
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
                <?php   
            echo $this->Form->Label(T('KaramBank.Status','Status'));
            echo  $this->Data['Enabled'] ? T('KarmaBank.Enabled','Enabled') :T('KarmaBank.Disabled','Disabled') ; 
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label(T('KarmaBank.StartingBalance','Starting Balance:')); ?>
        </li>
        <li>
            <?php
            $this->Form->AddHidden('Task','AddStartingBalance');
            echo $this->Form->Hidden('Task',array('value'=>'AddStartingBalance'));
            echo $this->Form->TextBox('StartingBalance',array('value'=>C('Plugins.KarmaBank.StartingBalance'),'class'=>'SmallInput'));
            echo $this->Form->Button(T('KarmaBank.Set','Set'));
            ?>
        </li>
    </ul>
   </div>
</div>
<?php
      echo $this->Form->Close();
?>
<?php
      echo $this->Form->Open();
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
            <?php echo $this->Form->Label(T('KarmaBank.DisplayOptions','Display Options'));?>
        </li>
        <li>
            <fieldset>
                <?php 
                $this->Form->AddHidden('Task','DisplayOptions');
                echo $this->Form->Hidden('Task',array('value'=>'DisplayOptions'));
                echo $this->Form->CheckBox('CommentShowBalance');
                ?>
                <span><?php echo T('KarmaBank.ShowBalanceWithComments','Show Balance with Comment Author Meta')?></span>
            </fieldset>
            <?php echo $this->Form->Button(T('KarmaBank.Set','Set'),array('class'=>'Button KarmaButton'));?>
        </li>
        <li>
            
        </li>
    </ul>
   </div>
</div>
<?php
      echo $this->Form->Close();
?>
<?php
      echo $this->Form->Open();
?>
<div class="Configuration">
   <div class="ConfigurationForm">
      <ul>
         <li>
        <?php 
        echo $this->Form->Label(T('KarmaBank.DisplayRules','Karma Rules:')); 
        $this->Form->AddHidden('Task','AddRule');
        echo $this->Form->Hidden('Task',array('value'=>'AddRule'));
        ?>
     </li>
     <?php foreach ($this->Data['Rules'] As $Rule){ ?>
     <li>
        <?php
        echo '<span> '.T('KarmaBank.Meta','Meta').' </span>';
        echo '<span class="spec"> '.T($Rule->Condition).' </span>';
        if(GetValue('Option',$Rule))
            echo '<span class="spec"> '.T($Rule->Option).' </span>';
        echo '<span> '.T($Rule->Operation).' </span>';
        echo '<span class="spec"> '.$Rule->Target.' </span>';
        echo '<span> '.T('KarmaBank.MakeTransactionOf','Make transaction of').' </span>';
        echo '<span class="spec"> '.$Rule->Amount.' </span>';
        echo '<span> '.T('KarmaBank.Karma','Karma').' </span>';
        echo '<span> <a href="'.Url('settings/karmabank/remove/'.$Rule->RuleID).'" >'.T('KarmaBank.Remove','Remove').'</a> </span>';
        ?>
         </li>
     <?php } ?>
     <li>
        <?php  
            $Meta = array();
            foreach ($this->Data['Meta'] As $MetaI => $MetaV)
                $Meta[$MetaI]=T($MetaI);
            $Operations=array();
            foreach (array_keys($this->Data['Operations']) As $Operation)
                $Operations[$Operation]=T($Operation);
        echo '<span> '.T('KarmaBank.Meta','Meta').' </span>';
        echo $this->Form->Dropdown('Condition',$Meta);
        echo $this->Form->Dropdown('Operation',$Operations);
        echo $this->Form->TextBox('Target',array('class'=>'SmallInput'));
        echo '<span> '.T('KarmaBank.MakeTransactionOf','Make transaction of').' </span>';
        echo $this->Form->TextBox('Amount',array('class'=>'SmallInput'));
        echo '<span> '.T('KarmaBank.Karma','Karma').' </span>';
        echo $this->Form->Button(T('KarmaBank.AddRule','Add Rule'));
        ?>
         </li>
         <li>
        <?php echo $this->Form->Label(T('KarmaBank.MetaDescription','Meta Description:')); ?>
     </li>
         <li>
        <dl id="KarmaBankMetaDescriptions">
            <?php foreach ($this->Data['Meta'] As $MetaI => $MetaV){ 
                echo '<dt>'.$Meta[$MetaI].':</dt><dd>'.T($MetaV).'</dd>';
            }?>
        </dl>
         <li>
        <?php echo $this->Form->Label(T('KarmaBank.OperationsDescription','Operations Description:')); ?>
     </li>
         <li>
        <dl>
            <?php foreach ($this->Data['Operations'] As $OpI => $OpV){ 
                echo '<dt>'.T($OpI).':</dt><dd>'.T($OpV).'</dd>';
            }?>
        </dl>
        
     </li>
     <ul>
   </div>
</div>
 <?php
      echo $this->Form->Close();
?>
</div>
