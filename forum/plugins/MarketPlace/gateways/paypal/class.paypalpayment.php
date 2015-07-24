<?php if (!defined('APPLICATION')) exit();
class PayPalPayment {    
    
    public function CheckIPN($UserID,$Product,$TransactionID){
        $Checker = new IpnChecker();
        $Checker->Sandbox=C('Plugins.MarketPlace.Gateway.PayPal.AccountType')!=='Live'?true:false;
        $Checker->Secure=!C('Plugins.MarketPlace.Gateway.PayPal.NoSSL');
        
        try {
            $Verified = $Checker->Verify();
        } catch (Exception $e) {
            LogMessage(__FILE__,__LINE__,'PayPalPayment','CheckIPN', 'payment_exception:'.$e->getMessage());
            return array('status'=>'error','silent'=>true);
        }    
        if(!$Verified){
           LogMessage(__FILE__,__LINE__,'PayPalPayment','CheckIPN', 'payment_not_verified:'.$Checker->Diagnose());
        }else{
           return $this->ProcessPayment($_POST,$UserID,$Product,$TransactionID);
        }
    }
    
    private function ProcessPayment($Payment,$UserID,$Product,$TransactionID){
        if(empty($Payment))
            return array('status'=>'error','silent'=>true);
        if(@empty($Payment['txn_id'])){
            LogMessage(__FILE__,__LINE__,'PayPalPayment','ProcessPayment', 'payment_no_txn_id:user_id->'.@$Payment['item_number']);
            return array('status'=>'error','silent'=>true);
        }
        if(@empty($Payment['item_number'])){
            LogMessage(__FILE__,__LINE__,'PayPalPayment','ProcessPayment', 'payment_no_user_id:txn_id->'.$Payment['txn_id']);
            return array('status'=>'error','silent'=>true);
        }
        $Currency=$Payment['mc_currency'];
        $PriceDenom = Gdn_Format::Unserialize($Product->PriceDenominations);
        $Price = GetValue($Currency,$PriceDenom);
        $Quantity = MarketPlaceAPI::GetQuantityRemote($TransactionID,'quantity');
        $Price =  $Price*$Quantity;
        $GatewayTransactionID=$Payment['txn_id'];
        
        $TransactionLog = new MarketTransactionModel();
        $Log=$TransactionLog->GetLatest($UserID,$Product->Slug,$TransactionID,$GatewayTransactionID);
        if($Log && $Log->Status=='payment_complete'){
            return array('status'=>'complete','silent'=>true);
        }
        
        if($Payment['payment_status']=='Pending'  && (!$Log || !$Log->Status=='payment_pending')){
            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'PayPal', $GatewayTransactionID,'payment_pending',  'reason:'.$Payment['pending_reason']);
            return array('status'=>'pending','silent'=>true);
        }else if($Payment['payment_status']!='Completed'){
            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'PayPal', $GatewayTransactionID,'payment_incomplete',  'payment_status:'.$Payment['payment_status'].',reason:'.$Payment['reason_code']);
            return array('status'=>'incomplete','silent'=>true);
        }
        $Required=array(
                'txn_type'=>'web_accept',
                'item_name'=>$Product->Slug,
                'receiver_id'=>C('Plugins.MarketPlace.Gateway.PayPal.Account'),
                'mc_currency'=>$Currency,
                'mc_gross'=>sprintf("%01.2f",$Price),
                'quantity'=>$Quantity
        );
                  
        $Mismatch =array();
        $PaymentComp= array();
        foreach ($Required As $RequireI => $RequireV)
            if(trim($Payment[$RequireI])!=$RequireV)
                $Mismatch[$RequireI]=$RequireI.'->'.trim($Payment[$RequireI]).' doesn\'t match '.$RequireV;

        foreach($Payment as $PaymentI =>$PaymentV)
                $PaymentComp[$PaymentI]=$PaymentI.'->'.trim($PaymentV);
                

        if(empty($Mismatch)){//Completed
            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'PayPal', $GatewayTransactionID,'payment_complete', 'payment_complete:'. implode('|',$PaymentComp));    
            return array('status'=>'success','silent'=>true);
        }else{
            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'PayPal', $GatewayTransactionID,'payment_invalid', 'payment_mismatch:'.implode('|',$Mismatch));
            return array('status'=>'invalid','silent'=>true);
        }        
    }
}    
?>
