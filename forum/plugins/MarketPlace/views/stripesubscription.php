<?php if (!defined('APPLICATION')) exit();
echo '<h1>'.sprintf(T('Credit Card Details for Subscription (%s Recuring)'),$this->Form->GetValue('SubscriptionPeriod')).'</h1>';
$this->Form->InputPrefix='';
echo $this->Form->Open(array('action'=>$this->Data['TransUrl'],'class'=>'payment-form'));
?>
<div class="Messages Errors"></div>
    <ul class="Stripe">
        <li>
            <?php 
                echo $this->Form->Label('Card Number');
                
            ?>
        </li>
        <li>
            <?php 
                echo $this->Form->TextBox('CardNumber',array('name'=>'','class'=>'card-number InputBox', 'autocomplete'=>'off', 'size'=>20)); 
                echo Wrap(T('Shown in blue square'),'div',array('class'=>'Expl'));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('CVC'); ?>
        </li>
        <li>
            <?php 
                echo $this->Form->TextBox('Card Verification Code',array('name'=>'','class'=>'card-cvc InputBox','autocomplete'=>'off', 'size'=>3, 'maxlength'=>4, 'style'=>'width:auto;'));
                echo Wrap(T('Various possible locations shown in red squares'),'div',array('class'=>'Expl'));
             ?>
        </li>
        <li>
            <?php
                echo $this->Form->Label('Expiration Date (MM/YYYY)');
             ?>
        </li>
        <li>
            <?php 
                $Months = array('01','02','03','04','05','06','07','08','09','10','11','12');
                $Years = range(date('Y'),date('Y',strtotime('+10 Years')));
                echo $this->Form->Dropdown('ExpiryMonth',array_combine(array_values($Months),array_values($Months)),array('name'=>'', 'class'=>'card-expiry-month')),
                '/',
                $this->Form->Dropdown('ExpiryYear',array_combine(array_values($Years),array_values($Years)),array('name'=>'', 'class'=>'card-expiry-year'));
                echo Wrap(T('Possible location shown in green square'),'div',array('class'=>'Expl'));
            ?> 
            
        </li>
        <li class="Submit">
            <?php 
                $this->Form->AddHidden('Currency',$this->Form->GetValue('Currency'));
                echo $this->Form->Hidden('Currency',array('value'=>$this->Form->GetValue('Currency')));
                $this->Form->AddHidden('Quantity',$this->Form->GetValue('Quantity'));
                echo $this->Form->Hidden('Quantity',array('value'=>$this->Form->GetValue('Quantity')));
                echo $this->Form->Button('Submit Details',array('name'=>'')); 
                echo $this->Form->Hidden('ProductSlug',array('value'=>$this->Form->GetValue('ProductSlug')));

                
            ?>
        </li>
    </ul>
<?php
echo $this->Form->Close();
