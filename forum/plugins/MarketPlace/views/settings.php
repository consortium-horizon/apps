<?php if (!defined('APPLICATION')) exit(); ?>
<style>
.MarketButton{
    margin-top:5px!important; 
}

form span{
    display:inline!important;
}

.ProductListings{
    margin:20px;
    font-size:12px;
}

fieldset{
    border:1px solid #CCCCCC;
    width:350px;
    padding:5px;
    margin-bottom:6px;
}

label{
    display:block;
}

label.Inline{
    display:inline;
}

.Tabs ul{
    margin-bottom:20px;
}

.Tabs ul li {
    display: inline;
    margin: 2px;
    padding: 5px;
    
}
.Tabs ul li .SmallButton{
    text-align: center;
}
</style>
<h1><?php echo $this->Data['Title']; ?></h1>
<div class="Info">
   <?php echo $this->Data['Description']; ?>
</div>
<div>
<?php
    echo $this->Form->Open();
    if($this->Form->GetValue('Task')=='StoreConfig')
        echo $this->Form->Errors();
    $this->Form->AddHidden('Task','StoreConfig');
    echo $this->Form->Hidden('Task',array('value'=>'StoreConfig'));
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
            <h2><?php echo T('Store Settings'); ?></h2>
            <?php echo $this->Form->Label('Store Name'); ?>
        </li>
        <li>
            <?php
            echo $this->Form->TextBox('StoreName',array('value'=>C('Plugins.MarketPlace.StoreName','Store')));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Store URI'); ?>
        </li>
        <li>
            <?php
            echo Url('/',TRUE);
            echo $this->Form->TextBox('StoreURI',array('value'=>C('Plugins.MarketPlace.StoreURI','store')));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Product URI'); ?>
        </li>
        <li>
            <?php
            echo Url('/'.C('Plugins.MarketPlace.StoreURI','store'),TRUE);
            echo $this->Form->TextBox('ProductURI',array('value'=>C('Plugins.MarketPlace.ProductURI','item')));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Force SSL'); ?>
        </li>
        <li>
            <?php
                echo $this->Form->Dropdown('ForceSSL',array(T('off'),T('on')),array('value'=>C('Plugins.MarketPlace.ForceSSL')));
            ?>
         </li>
        <li>
            <?php echo $this->Form->Button('Save',array('class'=>'SmallButton MarketButton')); ?>
        </li>
        
    </ul>
   </div>
</div>
 <?php
      echo $this->Form->Close();
?>
<?php
    echo $this->Form->Open(array('id'=>'PayPalSettings'));
    if($this->Form->GetValue('Task')=='PayPalSettings')
        echo $this->Form->Errors();
    $this->Form->AddHidden('Task','PayPalSettings');
    echo $this->Form->Hidden('Task',array('value'=>'PayPalSettings'));
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
            <h2><?php echo T('PayPal Settings'); ?></h2>
            <?php echo $this->Form->Label('Name'); ?>
        </li>
        <li>
        <fieldset>
        <legend><?php echo T('Account Type') ?></legend>
            <?php
          echo $this->Form->DropDown('AccountType', array('Sandbox'=>'Sandbox','Live'=>'Live'),array('value'=>C('Plugins.MarketPlace.Gateway.PayPal.AccountType')?C('Plugins.MarketPlace.Gateway.PayPal.AccountType'):C('Plugins.PremiumAccounts.PayPalAccount','Sandbox')));
            ?>
        </fieldset>
         </li>
         <li>
            <?php
               echo $this->Form->Label('PayPal Account ID', 'Account');
               echo $this->Form->TextBox('Account', array('value'=>C('Plugins.MarketPlace.Gateway.PayPal.Account',C('Plugins.PremiumAccounts.PayPalAccount',''))));
           echo '<div class="Message">'.T('PayPal ID Message', '
            This is <u>not</u> your Paypal email, but another ID that will help protect your account and prevent spam. For information on where to find Paypal Business / Merchant ID  
            <a href="https://www.paypal.com/webapps/customerprofile/summary.view">click here</a>. You may need to upgrade your account to a Premier or Business account to access it.').'</div>';
            ?>
         </li>
         <li>
            <?php echo $this->Form->Button('Save', array('class' => 'SmallButton PremiumButton SliceSubmit')); ?>
         </li>
    </ul>
   </div>
</div>    
<?php
 echo $this->Form->Close();
?>
<?php
    echo $this->Form->Open(array('id'=>'StripeSettings'));
    if($this->Form->GetValue('Task')=='StripeSettings')
        echo $this->Form->Errors();
    $this->Form->AddHidden('Task','StripeSettings');
    echo $this->Form->Hidden('Task',array('value'=>'StripeSettings'));
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
            <h2><?php echo T('Stripe Settings'); ?></h2>
            <?php echo $this->Form->Label('Private API Key'); ?>
        </li>
        <li>
            <?php
                echo $this->Form->TextBox('PrivateKey',array('value'=>C('Plugins.MarketPlace.Gateway.Stripe.PrivateKey')));
            ?>
         <?php
            echo '<div class="Message">'.T('Stripe Settings Message', 'Create a <a href="https://stripe.com/">Stripe</a> account, you will find your API keys <a href="https://manage.stripe.com/#account/apikeys">here</a>.').'</div>';
        ?>
         </li>
        <li>
            <?php echo $this->Form->Label('Public API Key'); ?>
        </li>
        <li>
            <?php
                echo $this->Form->TextBox('PublicKey',array('value'=>C('Plugins.MarketPlace.Gateway.Stripe.PublicKey')));
            ?>
         </li>
         <li>

            <?php echo $this->Form->Button('Save', array('class' => 'SmallButton PremiumButton SliceSubmit')); ?>
         </li>
    </ul>
   </div>
</div>    
<?php
    echo $this->Form->Close();
    $this->FireEvent('GatewaySettings');
?>
</div>
