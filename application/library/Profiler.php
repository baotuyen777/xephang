<?php
class Profiler
{
    protected $_view;
    
    public function __construct()
    {
        $this->_view = new FrameworkView(APP_PATH . 'views/profiler/profiler.phtml');
    }
    
    private function _dbProfiler()
    {
        $dbs = array();    
        $registry = FrameworkRegistry::getInstance();    
        foreach (get_object_vars($registry) as $name => $object){        
            if (is_object($object) && ($object instanceof FrameworkDatabase)){            
                $dbs[$name] = $object;            
            }        
        }
        return $this->_view->partial('profiler/database.phtml', array('dbs' => $dbs));
    }
    
    private function _memcacheProfiler()
    {
        $registry = FrameworkRegistry::getInstance();          
        return $this->_view->partial('profiler/memcache.phtml', array('memcache' => $registry->memcache));       
    }   
    
    private function _memcacheDbProfiler()
    {
        $registry = FrameworkRegistry::getInstance();                                     
        return $this->_view->partial('profiler/memcachedb.phtml', array('memcache' => $registry->memcachedb));       
    }             
    
    private function _sessionProfiler()
    {
        return $this->_view->partial('profiler/session.phtml');
    }
    
    public function run()
    {
        $this->_view->dbProfiler = $this->_dbProfiler();
        $this->_view->memcacheProfiler = $this->_memcacheProfiler();    
        $this->_view->memcacheDbProfiler = $this->_memcacheDbProfiler();    
        $this->_view->sessionProfiler = '';
        $this->_view->render();
    }
}
