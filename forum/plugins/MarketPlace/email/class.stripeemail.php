<?php if (!defined('APPLICATION')) exit();

class StripeEmail {
    
    public static $Mailer;
    
    public static function Email($Email,$Subject,$Body){
        if(!self::$Mailer){
            $Mailer = new Gdn_Email();
            self::$Mailer = $Mailer;
        }else{
            $Mailer = self::$Mailer;
        }
        $Mailer->Clear();
        $Mailer->To($Email);
        $Mailer->Subject($Subject);
        $Mailer->Message($Body);
        $Mailer->Send();
    }
    
    public static function PaymentReicept($Email, $Payment){
        $Subject = T('Payment has been received');
        self::Email($Email, $Subject, self::PaymentReceivedBody($Payment,$Email)); 
        
    }
    
    public static function InvoicePaymentReicept($Email, $Invoice){
        $Subject = T('Payment has been received');
        self::Email($Email, $Subject, self::InvoicePaymentReceivedBody($Invoice, $Email)); 
        
    }
    
    public static function InvoicePaymentFailed($Email, $Invoice){
        $Subject = T('Payment has failed');
        self::Email($Email, $Subject, self::InvoicePaymentFailedBody($Invoice, $Email)); 
    }
    
    public static function InvoicePaymentImminent($Email, $Invoice){
        $Subject = T('Payment will be taken');
        self::Email($Email, $Subject, self::InvoicePaymentImminentBody($Invoice, $Email)); 
    }
    
    public static function InvoicePaymentFailedBody($Invoice, $Customer) {
        $Subscription = $Invoice->lines->subscriptions[0];
        $Start = Gdn_Format::Date($Subscription->period->start,'%B %e, %Y');
        $End = Gdn_Format::Date($Subscription->period->end,'%B %e, %Y');
        $Total = sprintf('%01.2f',$Invoice->total/100.0);
        $SubscriptionUrl = Url('/profile/subscription',TRUE);

        $Message =  <<<EOD
This is a message to inform, we have been unable to take payment 
on your subscription, for whatever reason. Please check your card is 
still active and you have the means to make payment.

If we are unable to take payment again over the next couple
of days, your subscription will not be renewed. However you can
always resubscribe.

-------------------------------------------------
SUBSCRIPTION

Email: {$Customer->email}
Plan: {$Subscription->plan->name}
Amount: {$Total} (USD)

For service between {$Start} and {$End}

You can manage your subscriptions here:
{$SubscriptionUrl}

-------------------------------------------------

EOD;


        return T('Marketplace.StripeInvoiceFailedMsg', $Message);
    }
    
    public static function InvoicePaymentReceivedBody($Invoice, $Email){
        $Subscription = $Invoice->lines->subscriptions[0];
        $Start = Gdn_Format::Date($Subscription->period->start,'%B %e, %Y');
        $End = Gdn_Format::Date($Subscription->period->end,'%B %e, %Y');
        $Total = sprintf('%01.2f',$Invoice->total/100.0);
        $SubscriptionUrl = Url('/profile/subscription',TRUE);
        $Message =  <<<EOD
This is a receipt for your subscription. This is only a receipt,
no payment is due. Thanks You!

-------------------------------------------------
SUBSCRIPTION

Email: {$Email}
Plan: {$Subscription->plan->name}
Amount: {$Total} (USD)

For service between {$Start} and {$End}

You can manage your subscriptions here:
{$SubscriptionUrl}

-------------------------------------------------

EOD;


        return T('Marketplace.StripeInvoiceReiceptMsg', $Message);
    }
    
    public static function InvoicePaymentImminentBody($Invoice, $Email){
        $Subscription = $Invoice->lines->subscriptions[0];
        $Start = Gdn_Format::Date($Subscription->period->start,'%B %e, %Y');
        $End = Gdn_Format::Date($Subscription->period->end,'%B %e, %Y');
        $Total = sprintf('%01.2f',$Invoice->total/100.0);
        $SubscriptionUrl = Url('/profile/subscription',TRUE);
        $Message =  <<<EOD
Payment is about to be taken on the upcoming period.

-------------------------------------------------
SUBSCRIPTION

Email: {$Email}
Plan: {$Subscription->plan->name}
Amount: {$Total} (USD)

For service between {$Start} and {$End}

You can manage your subscriptions here:
{$SubscriptionUrl}

-------------------------------------------------

EOD;


        return T('Marketplace.StripePaymentImminentMsg', $Message);
    }
    
    public static function PaymentReceivedBody($Charge, $Email) {
        $Amount = sprintf('$%0.2f', $Charge->amount / 100.0);
        $Message = <<<EOD

A payment has been charged successfully.

-------------------------------------------------
PAYMENT

Email: {$Email}
Amount: {$Amount} (USD)

-------------------------------------------------

EOD;
        return T('Marketplace.StripeReiceptMsg', $Message);
    }

}
