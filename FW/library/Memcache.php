<?php
class FrameworkMemcache 
{       
    public $stats = array('cache_hit' => array(), 'cache_miss' => array(), 'cache_set' => array());   
         
    public function __construct($servers, $expire)
    {   
        $this->config['memcache_servers'] = $servers;
        $this->expire = $expire;
        $this->connected_servers = array();       
        $this->_connect();                                                                                          
    }

    public function _connect()
    {   
        if ( function_exists('memcache_connect') ){
            $this->memcache = new Memcache;
            $this->_connect_memcache();  
        } 
    }

    public function _connect_memcache()
    {           
        if ( ! empty($this->config['memcache_servers'])){                
            $error_display = ini_get('display_errors');
            $error_reporting = ini_get('error_reporting');                 
            ini_set('display_errors', "Off");
            ini_set('error_reporting', 0);  
            foreach ( $this->config['memcache_servers'] as $server ){                
                if ($this->memcache->addServer($server['host'], $server['port'])){
                    $this->connected_servers[] = $server;
                }               
            }     
            ini_set('display_errors', $error_display);
            ini_set('error_reporting', $error_reporting);  
        }
    }

    public function get($key)
    {              
        if (empty($this->connected_servers)){            
            return FALSE;
        }                                   
        $data = $this->memcache->get($key);             
        if ($data !== FALSE){               
            $this->_setCacheStats($key, 'cache_hit');               
        } else {
            $this->_setCacheStats($key, 'cache_miss');            
        }                                                             
        return $data;
    }

    public function set($key, $data, $expire = NULL)
    {                                                                             
        if (empty($this->connected_servers)){
            return false;
        }
        $e = ($expire === NULL) ? $this->expire : $expire;
        $this->memcache->set($key, $data, 0, $e);            
        $this->_setCacheStats($key, 'cache_set');            
    }

    public function replace($key, $data)                                   
    {                                                                              
        if ( empty($this->connected_servers) ){
            return false;
        }                                     
        $this->memcache->replace($key, $data, 0, $this->expire);                
    }

    public function delete($key, $when = 0)
    {                                                        
        if (empty($this->connected_servers)){
            return false;
        }                                   
        $this->memcache->delete($key, $when);              
    }
    
    private function _setCacheStats($key, $status)
    {           
        $this->stats[$status][] = $key;                     
    }
}     