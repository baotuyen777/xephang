<?php 
define('DB', '//172.20.96.197:1521/BVTC');
define('DB_USER', 'homistc');
define('DB_PASS', 'thucuc2011');
define('DB_CHAR', 'AL32UTF8');

$root = dirname(dirname(__FILE__)); 
define('APP_PATH', $root . DIRECTORY_SEPARATOR . '');
//require (APP_PATH.'const.inc');
require ('nusoap/nusoap.php');
