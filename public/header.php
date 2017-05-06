<?php 
$ip = '';

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $ip = $_SERVER["REMOTE_ADDR"];                          
} 

if($ip != '42.112.18.105')
	exit;

define('DB', '//172.20.96.197:1521/BVTC');
define('DB_USER', 'homistc');
define('DB_PASS', 'thucuc2011');
define('DB_CHAR', 'AL32UTF8');
define('DB_USER_2016', 'homistc2016');
define('DB_PASS_2016', 'thucuc2016');

$root = dirname(dirname(__FILE__)); 
define('APP_PATH', $root . DIRECTORY_SEPARATOR . '');
//require (APP_PATH.'const.inc');
require ('nusoap/nusoap.php');
