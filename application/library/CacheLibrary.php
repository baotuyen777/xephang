<?php
class CacheLibrary
{
    public static $_prefix = 'account_mvod_';
    
    public static function getCache($key)
    {           
        if ( ! MEMCACHE_ENABLED){
            return FALSE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcache');  
        return $cacheEngine->get($key);        
    }
    
    public static function setCache($key, $data, $timeout = 1200)
    {
        if ( ! MEMCACHE_ENABLED){
            return TRUE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcache');  
        $cacheEngine->set($key, $data, $timeout);
    }
    
    public static function deleteCache($key)
    {
        if ( ! MEMCACHE_ENABLED){
            return TRUE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcache');  
        $cacheEngine->delete($key);
    }
    
    public static function replaceCache($key, $data)
    {
        if ( ! MEMCACHE_ENABLED){
            return TRUE;    
        }
        $cacheEngine = FrameworkRegistry::get('memcache');  
        $cacheEngine->replace($key, $data);    
    }   
    
    public static function getDbCache($key)
    {           
        if ( ! MEMCACHEDB_ENABLED){
            return FALSE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcachedb');  
        return $cacheEngine->get($key);        
    }
    
    public static function setDbCache($key, $data, $timeout = 1200)
    {
        if ( ! MEMCACHEDB_ENABLED){
            return TRUE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcachedb');  
        $cacheEngine->set($key, $data, $timeout);
    }
    
    public static function deleteDbCache($key)
    {
        if ( ! MEMCACHEDB_ENABLED){
            return TRUE;    
        }
        $key = self::$_prefix . $key;
        $cacheEngine = FrameworkRegistry::get('memcachedb');  
        $cacheEngine->delete($key);
    }
    
    public static function replaceDbCache($key, $data)
    {
        if ( ! MEMCACHE_ENABLED){
            return TRUE;    
        }
        $cacheEngine = FrameworkRegistry::get('memcachedb');  
        $cacheEngine->replace($key, $data);    
    }                 
}
