<?php
$config['memcache_expire'] = 3600;  
$config['memcache_servers'] = array(
    array(
        'host' => 'localhost',
        'port' => 11211
    )
);
$config['memcachedb_expire'] = 3600;  
$config['memcachedb_servers'] = array(
    array(
        'host' => 'localhost',
        'port' => 21201
    )
);
return $config;

