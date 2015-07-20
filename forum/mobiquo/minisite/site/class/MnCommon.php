<?php

/**
 * common functions
 * 
 * @since  2013-8-7
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnCommon Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * call json api
     *
     * @param  Array  $param
     * @return  Mixed
     * $param['get']  means get data
     * $param['post']  means post data
     * @return  String
     */
    public function callApi($param) {
        $tapatalkPluginApiConfig = MainBase::$tapatalkPluginApiConfig;
        if (!$param['get']) $param['get'] = array();
        if (!$param['post']) $param['post'] = array();
        foreach ($param['get'] as $k => $v) {
            if ($getUrl) $getUrl .= "&$k=".urlencode($v);
            else $getUrl = "$k=".urlencode($v);
        }
        $debugCallType = isset($_GET['debugCallType']) ? $_GET['debugCallType'] : '';
        $apiUrl = $tapatalkPluginApiConfig['url']."?$getUrl";
        if (function_exists('curl_init') || $debugCallType == 1) { //curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //some old php environments do not support this patameter,example error:Warning: curl_setopt() [function.curl-setopt]: CURLOPT_FOLLOWLOCATION cannot be activated when safe_mode is enabled or an open_basedir is set in .../site/class/MnCommon.php on line 41
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param['post']);
            if ($_COOKIE) {
                foreach($_COOKIE as $k => $v) {
                    if( $cookies) {
                        $cookies .= ";".$k."=".urlencode($v);
                    } else {
                        $cookies = $k."=".urlencode($v);
                    }
                }
                curl_setopt($ch, CURLOPT_COOKIE, $cookies);
            }
            if ($ip = MainApp::$oCf->getIp()) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('CLIENT-IP:'.$ip, 'X-FORWARDED-FOR:'.$ip));
            }
            $strRet = curl_exec($ch);
            curl_close ($ch);
        } elseif (@ini_get('allow_url_fopen') || $debugCallType == 2) {    //socket
            $strRet = $this->_getContentsWithSocket($apiUrl, $param['post']);
        } elseif (function_exists('file_get_contents') || $debugCallType == 3) {   //file_get_contents,only support get parameters
            $strRet = @file_get_contents($apiUrl);
        } else {
            
        }
        return ((string) $strRet);
    }
    /**
	 * Get file contents (with sockets)
	 * ref ipb classFileManagement.php
	 *
	 * @param	string		File location
	 * @param	array 		Data to post (automatically converts to POST request)
	 * @return	@e string
	 */
	protected function _getContentsWithSocket( $file_location, $post_array=array() )
	{
	    
	    $tempObj = new stdClass();
	    $tempObj->userAgent = $_SERVER['HTTP_USER_AGENT'];
	    
		//-------------------------------
		// INIT
		//-------------------------------
		
		$data				= null;
		
		//-------------------------------
		// Parse URL
		//-------------------------------
		
		$url_parts = @parse_url($file_location);
		
		if ( ! $url_parts['host'] )
		{
			$tempObj->errors[] = "No host found in the URL '{$file_location}'!";
			return FALSE;
		}
		
		//-------------------------------
		// Finalize
		//-------------------------------
		
		$host = $url_parts['host'];
	 	$port = ( isset($url_parts['port']) ) ? $url_parts['port'] : ( $url_parts['scheme'] == 'https' ? 443 : 80 );

	 	//-------------------------------
	 	// Tidy up path
	 	//-------------------------------
	 	
	 	if ( !empty( $url_parts["path"] ) )
		{
			$path = $url_parts["path"];
		}
		else
		{
			$path = "/";
		}
 
		if ( !empty( $url_parts["query"] ) )
		{
			$path .= "?" . $url_parts["query"];
		}
	 	
	 	//-------------------------------
	 	// Open connection
	 	//-------------------------------
	 	
	 	if ( ! $fp = @fsockopen( $url_parts['scheme'] == 'https' ? "ssl://" . $host : $host, $port, $errno, $errstr, 15 ) )
	 	{
			$tempObj->errors[] = "Could not establish a connection with {$host}";
			return FALSE;
		
		}
		else
		{
			$final_carriage	= ( $tempObj->auth_req or $tempObj->auth_raw ) ? "" : "\r\n";
			
			$userAgent = ( $tempObj->userAgent ) ? "\r\nUser-Agent: " . $tempObj->userAgent : '';
			
			//-----------------------------------------
			// Are we posting?
			//-----------------------------------------
			
			if( $post_array )
			{
				if ( is_array( $post_array ) )
				{
					$post_back	= array();
					foreach ( $post_array as $key => $val )
					{
						$post_back[] = $tempObj->key_prefix . $key . '=' . urlencode($val);
					}
					$post_back_str	= implode( '&', $post_back);
				}
				else
				{
					$post_back_str = $post_array;
				}
				
				$header	= "POST {$path} HTTP/1.0\r\nHost:{$host}\r\nContent-Type: application/x-www-form-urlencoded\r\nConnection: Keep-Alive{$userAgent}\r\nContent-Length: " . strlen($post_back_str) . "\r\n{$final_carriage}{$post_back_str}";
			}
			else
			{
				$header	= "GET {$path} HTTP/1.0\r\nHost:{$host}\r\nConnection: Keep-Alive{$userAgent}\r\n{$final_carriage}";
			}

			if ( ! fputs( $fp, $header ) )
			{
				$tempObj->errors[] = "Unable to send request to {$host}!";
				return FALSE;
			}
			
			if ( $tempObj->auth_req )
			{
				if ( $tempObj->auth_user && $tempObj->auth_pass )
				{
					$header = "Authorization: Basic ".base64_encode("{$tempObj->auth_user}:{$tempObj->auth_pass}")."\r\n\r\n";
					
					if ( ! fputs( $fp, $header ) )
					{
						$tempObj->errors[] = "Authorization Failed!";
						return FALSE;
					}
				}
			}
			elseif ( $tempObj->auth_raw )
			{
				$header = $tempObj->auth_raw."\r\n\r\n";
					
				if ( ! fputs( $fp, $header ) )
				{
					$tempObj->errors[] = "Authorization Failed!";
					return FALSE;
				}
			}
		}

		@stream_set_timeout( $fp, 20 );
		
		$status = @stream_get_meta_data($fp);
		
		while( ! feof($fp) && ! $status['timed_out'] )		
		{
			$data	.= fgets( $fp, 8192 );
			$status	= stream_get_meta_data($fp);
		}
		
		fclose ($fp);
		
		//-------------------------------
		// Strip headers
		//-------------------------------
		
		// HTTP/1.1 ### ABCD
		$tempObj->http_status_code = substr( $data, 9, 3 );
		$tempObj->http_status_text = substr( $data, 13, ( strpos( $data, "\r\n" ) - 13 ) );

		//-----------------------------------------
		// Try to deal with chunked..
		//-----------------------------------------
		
		$_chunked	= false;
		
		if( preg_match( '/Transfer\-Encoding:\s*chunked/i', $data ) )
		{
			$_chunked	= true;
		}

		$tmp	= preg_split("/\r\n\r\n/", $data, 2);
		$data	= trim($tmp[1]);
		
		$tempObj->raw_headers	= trim($tmp[0]);
		
		//-----------------------------------------
		// Easy way out :P
		//-----------------------------------------
		
		if( $_chunked )
		{
			$lines	= explode( "\n", $data );
			array_pop($lines);
			array_shift($lines);
			$data	= implode( "\n", $lines );
		}

 		return $data;
	}
    
    /**
     * judge call api success
     *
     * @param  Mixed  result returned by api
     * @return  Boolean
     */
    public function callApiSuccess($strRet) {
        if ($strRet) {
            $data = json_decode($strRet);
            if ($data !== NULL) {
                if ($data->result === false) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * return api error array,this method only used when call api failed
     */
    public function getApiError($strRet) {
        if ($strRet) {
            return json_decode($strRet);
        } else {
            $arr = array(
                'result' => false,
                'error' => 'Unknown error.Maybe net is too slow.',
                'code' => MBQ_ERR_TOP
            );
            return json_decode(json_encode($arr));
        }
    }
    
    /**
     * return api error string for display,this method only used when call api failed
     */
    public function getApiErrorStr($strRet) {
        $o = $this->getApiError($strRet);
        return "Error info:$o->error,error code:$o->code.";
    }
    
}

?>