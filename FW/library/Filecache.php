<?php 
define('DIR_WRITE_MODE', 0777);
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); 

class FWFilecache 
{
    public $stats; 
    private $_cacheDir = '';
    private $_debug = FALSE;    
    
    public function __construct($cacheDir, $debug = FALSE)
    {          
        $this->_cacheDir = $cacheDir;   
        if ($debug){
            $this->stats = array(
                'cache_hit'  => array(), 
                'cache_miss' => array(), 
                'cache_set'  => array()
            );  
            $this->_debug = $debug; 
        }
    }
     
    public function get($key){
        $path = str_replace('_', '/', $key);
        $filePath = $this->_cacheDir . $path;   
        if (FALSE === ($cachedata = $this->read_file($filePath))){    
            if ($this->_debug){
                $this->_setCacheStats($key, 'cache_miss');
            }
            return FALSE;
        }
        if ($this->_debug){            
            $this->_setCacheStats($key, 'cache_hit');
        } 
        return unserialize($cachedata); 
    }  
    
    public function set($key, $data)
    {        
        $parts = explode('_', $key);
        $filename = array_pop($parts);             
        $dirPath = $this->_cacheDir . implode('/', $parts);  
        if ( ! @is_dir($dirPath)){             
            if ( ! @mkdir($dirPath, DIR_WRITE_MODE, TRUE)){ 
                return FALSE;
            }                         
            @chmod($dirPath, DIR_WRITE_MODE);            
        }
        if ($this->write_file($dirPath . '/' . $filename, serialize($data)) === FALSE){
            return FALSE;
        }        
        @chmod($this->_cacheDir . $key, DIR_WRITE_MODE);
        if ($this->_debug){
            $this->_setCacheStats($key, 'cache_set');
        } 
        return TRUE; 
    }
    
    public function delete($key)
    {
        $parts = explode('_', $key);
        $filename = array_pop($parts);             
        $dirPath = $this->_cacheDir . implode('/', $parts);     
        @unlink($dirPath . '/' . $filename);
    }
        
    public function read_file($file)
    {
        if ( ! file_exists($file)){
            return FALSE;
        }   
        return file_get_contents($file);        
    }
    
    public function write_file($file, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
    {
        if ( ! $fp = @fopen($file, $mode)){
            return FALSE;
        }                                         
        flock($fp, LOCK_EX);
        fwrite($fp, $data);
        flock($fp, LOCK_UN);
        fclose($fp);  
        return TRUE;
    }
    
    private function _setCacheStats($key, $status)
    {           
        $this->stats[$status][] = $key;                     
    }      
}
