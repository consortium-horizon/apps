<?php if (!defined('APPLICATION')) exit(); 
function DrawButtons($Sender,$PriceDenominations,$Product,$TransactionID){
    ob_start();
    ?>
        <table>
            <tr>
        <?php 
            $PreCondCallback=GetValueR($Product->ProductType.'.PreCondCallback', $Sender->MarketPlace->Plgn->API()->ProductTypes);
            $Message='';
            if($PreCondCallback){
                $PreCond=call_user_func($PreCondCallback,Gdn::Session()->UserID,$Product);
                if($PreCond['status']!='pass'){
                    $Message=$PreCond['errormsg'];
                }
            }
            if($Message){
                echo '<td>'.$Message.'</td>';
            }else{
                foreach ($Sender->MarketPlace->Plgn->API()->Gateways As $GateWayN => $Gateway){
                    if(!GetValue($GateWayN,$Product->EnabledGateways,1)) continue;
                    $FormCallBack = $Gateway['FormCallback'];
                    
                    foreach($Gateway['Currencies'] As $Currency=>$CurrencyV){
                        if(GetValue($Currency,$PriceDenominations)){
                            $Sender->Form->IDPrefix=$GateWayN.$Currency;
                            $Price=$PriceDenominations[$Currency];
                            echo '<td>';
                            call_user_func($FormCallBack,$Sender,$TransactionID,$Product,$Currency,$Price);
                            echo '</td>';
                        }
                    }
                }
            }
        ?>
            </tr>
        </table>
    <?php
    $Buttons=ob_get_contents();
    ob_end_clean();
    return $Buttons;
}
