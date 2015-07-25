<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Cookie Identity class. It extends standard Gdn_CookieIdentity class to
 * provide support for configurable session lifespan.
 */
class CookieIdentity extends \Gdn_CookieIdentity {
	// @var int The lifespan of a persistent sessions, in seconds.
	// Note: 2592000 is 60*60*24*30 or 30 days
	const DEFAULT_PERSISTENT_SESSION_LIFESPAN = 2592000;

	// @var int The lifespan of a volatile sessions, in seconds.
	// Note: 172800 is 60*60*24*2 or 2 days
	const DEFAULT_VOLATILE_SESSION_LIFESPAN = 172800;

	/**
	 * Generates the user's session cookie.
	 *
	 * @param int $UserID The unique id assigned to the user in the database.
	 * @param boolean $Persist Should the user's session remain persistent across visits?
	 */
	public function SetIdentity($UserID, $Persist = false) {
		parent::SetIdentity($UserID, $Persist);

		if(is_null($this->UserID)) {
			return;
		}

		if($Persist !== FALSE) {
			$CookieLifespan = C('Garden.Session.PersistentLifespan', self::DEFAULT_PERSISTENT_SESSION_LIFESPAN);
			$Expiration = time() + $CookieLifespan;
			$Expire = $Expiration;
		}
		else {
			$CookieLifespan = C('Garden.Session.VolatileLifespan', self::DEFAULT_VOLATILE_SESSION_LIFESPAN);
			$Expiration = time() + $CookieLifespan;
			// Note: setting $Expire to 0 will cause the cookie to die when the browser closes.
			$Expire = 0;
		}

		// Create the cookie
		$KeyData = $this->UserID . '-' . $Expiration;
		$this->_SetCookie($this->CookieName, $KeyData, array($this->UserID, $Expiration), $Expire);
	}
}
