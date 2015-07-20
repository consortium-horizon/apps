<?php
/**
 * modified from WhosOnline plugin version1.3
 * ExttMbqWhosOnlineModule extended from WhosOnlineModule
 * add method exttMbqGetUsers()
 * 
 * @since  2012-11-2
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqWhosOnlineModule extends WhosOnlineModule {

	public function __construct(&$Sender = '') {
		parent::__construct($Sender);
	}
	
	public function exttMbqGetUsers() { //must call $this->GetData() method before call this method
	    return $this->_OnlineUsers;
	}
	
}

?>