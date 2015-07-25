<?php if(!defined('APPLICATION')) exit();


/**
 * Session class. It extends standard Gdn_Session class to provide a transient
 * key for guest users as well.
 * Legacy class. Implemented to maintain backward compatibility after moving
 * Aelia classes to their own namespace.
 *
 * @see \Aelia\Session.
 */
class AeliaSession extends \Aelia\Session {
}
