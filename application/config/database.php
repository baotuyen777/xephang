<?php
$db['slaveDbCount'] = 1;    
$db['session']['server'] = 'localhost';
$db['session']['username'] = 'root';
$db['session']['password'] = '1';
$db['session']['database'] = 'tcportal'; 

$db['master']['server'] = 'localhost';
$db['master']['username'] = 'root';
$db['master']['password'] = '1';
$db['master']['database'] = 'tcqueue'; 
                                             
$db['slave1']['server'] = 'localhost';
$db['slave1']['username'] = 'root';
$db['slave1']['password'] = '1';
$db['slave1']['database'] = 'tcqueue'; 

return $db; 
