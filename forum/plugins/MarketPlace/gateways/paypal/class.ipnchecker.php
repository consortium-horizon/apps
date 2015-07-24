<?php /*if (!defined('APPLICATION')) exit();*/
class IpnChecker {

    public $Sandbox = FALSE;
    public $Secure = FALSE;
    public $Follow = FALSE;    
    
    private $StatusCode = '';
    private $Reply = '';

    protected function NotifyValidate(){
        
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            header('Allow: POST', TRUE, 405);
            throw new Exception("Invalid HTTP request method.");
        }
    
        $PayPalHome = ($this->Secure ? 'https://' : 'http://' ).($this->Sandbox ? 'www.sandbox.' : 'www.').'paypal.com/cgi-bin/webscr';
        
        $Post = http_build_query($_POST,'','&');
        
        $NotifyHandler = curl_init($ParserUrl);
        
        curl_setopt($NotifyHandler, CURLOPT_URL, $PayPalHome);
       
        curl_setopt($NotifyHandler, CURLOPT_TIMEOUT, 30);
        curl_setopt($NotifyHandler, CURLOPT_FOLLOWLOCATION, $this->Follow);
        curl_setopt($NotifyHandler, CURLOPT_POST, TRUE);
        curl_setopt($NotifyHandler, CURLOPT_POSTFIELDS, 'cmd=_notify-validate&'.$Post);
        curl_setopt($NotifyHandler, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($NotifyHandler, CURLOPT_HEADER, TRUE);
        if(defined('CURL_SSLVERSION_TLSv1') && $this->Secure)
            curl_setopt($NotifyHandler, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);

        $Response = curl_exec($NotifyHandler);
        list($Header, $Body) = explode("\r\n\r\n", $Response, 2);
        $this->Reply = trim($Body);
        $this->StatusCode = curl_getinfo($NotifyHandler, CURLINFO_HTTP_CODE);
        
        
        if($this->StatusCode==0 || $this->Reply === FALSE){
            $ErrorCode = curl_errno($NotifyHandler);
            $Error = curl_error($NotifyHandler);
            
            throw new Exception("cURL error: {$Error} [{$ErrorCode}]");
        }
        
        
    }
    
    public function Verify() {
    
        $this->NotifyValidate(); 
        
        if ($this->StatusCode != 200) {
            throw new Exception("Paypal Listener: {$this->StatusCode} response was returned");
        }
        
        if ($this->Reply == "VERIFIED") {
            return TRUE;
        } else if ($this->Reply == "INVALID") {
            return FALSE;
        } else {
            throw new Exception("Paypal Listener: Couldn't understand reply");
        }
    }
    
    public function Diagnose() {
        $Post = var_export($_POST, TRUE);
        $DiagnoseText = <<<EOT
Status Code -> {$this->StatusCode}
Reply -> {$this->Reply}
Post Values -> {$Post}
EOT;
        return $DiagnoseText;
    }
    
}
