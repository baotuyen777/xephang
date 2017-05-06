<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

date_default_timezone_set("Asia/Bangkok");
$mem_start = memory_get_usage(); 
list($sm, $ss) = explode(' ', microtime());   
       
$root = dirname(dirname(__FILE__)); 
   
define('FW_PATH', $root . DIRECTORY_SEPARATOR . 'FW/');
define('APP_PATH', $root . DIRECTORY_SEPARATOR . 'application/');
define('PUBLIC_PATH', $root . DIRECTORY_SEPARATOR . 'public/');
define('APP_URL', 'http://xephang.local');

require_once(APP_PATH . 'bootstrap.php');    

Bootstrap::run();                  

if ($GLOBALS['_DBG']==1){
    list($em, $es) = explode(' ', microtime()); 
    $elapsed = number_format(($em + $es) - ($sm + $ss), 4);
    $mem_end = memory_get_usage();
    echo '<div style="border-top: 1px solid #BBBBBB; margin-bottom: 3px; margin-top: 3px;"></div>';
    echo '<div style="font-weight: bold; padding-left: 10px;">This page was created in ' . $elapsed . ' seconds</div>'; 
    echo '<div style="padding-left: 10px;">The memory allocated is ' . number_format($mem_start/1024/1024, 2) . '/' . number_format($mem_end/1024/1024, 2) . '</div>';    
}                            

