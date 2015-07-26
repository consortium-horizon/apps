<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class PDO
 */
class PDO extends \PDO {

    static function getConnection() {
        $dbhost = $GLOBALS['database_host'];
        $dbuser = $GLOBALS['database_user'];
        $dbpass = $GLOBALS['database_password'];
        $dbname = $GLOBALS['database_name'];
        $dbh = new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $dbh;
    }

}
