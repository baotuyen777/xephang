<?php      
require_once(APP_PATH . 'config/const.inc');   
require_once(FW_PATH . 'framework.php'); 
require_once(FW_PATH . 'library/Registry.php');         
require_once(FW_PATH . 'library/Database.PDO.php');  
require_once(FW_PATH . 'library/Memcache.php');                   
                     
$GLOBALS['_DBG'] = 0;

class Bootstrap
{
    public static function run()
    {           
        ini_set('display_startup_errors', $GLOBALS['_DBG'] == 1);         
        Bootstrap::setupSession();  
        Bootstrap::setupMemcache();   
        Bootstrap::setupRouter();           
             
        if ($GLOBALS['_DBG'] == 1){ 
            //Bootstrap::profiler();        
        }         
    }       
    
    public static function setupSession()
    {        
        Bootstrap::setupSessionDb();         
        
        require_once(APP_PATH . 'library/SessionManager.php');           
        $sm = new SessionManager();       
                
        session_set_save_handler(
            array(&$sm, 'open'), 
            array(&$sm, 'close'), 
            array(&$sm, 'read'), 
            array(&$sm, 'write'), 
            array(&$sm, 'destroy'), 
            array(&$sm, 'gc'));  
                                 
        ini_set('session.name', SESi2isn12pas43_SESSION);
        ini_set('session.cookie_domain', SESi2isn12pas43_COOKIE); 
        ini_set('session.gc_probability', 1); 
        ini_set('session.gc_divisor', 5000); 
        
        session_start();  
    }        
      
    public static function setupSessionDb()
    {
        $db = require(APP_PATH . 'config/database.php');                  
        if ( ! FrameworkRegistry::isRegistered('session')){
            $sessionDb = new FrameworkDatabase($db['session']['server'], $db['session']['username'], $db['session']['password'], $db['session']['database'], $GLOBALS['_DBG']);            
            FrameworkRegistry::set('session', $sessionDb);
        }          
    }
            
    public static function setupMemcache()
    {                    
        $config = require(APP_PATH . 'config/memcache.php');        
        $memcache = new FrameworkMemcache($config['memcache_servers'], $config['memcache_expire']);                    
        $memcachedb = new FrameworkMemcache($config['memcachedb_servers'], $config['memcachedb_expire']);                    
        FrameworkRegistry::set('memcache', $memcache); 
        FrameworkRegistry::set('memcachedb', $memcachedb); 
    } 
    
    public static function setupRouter()
    {
        $router = new FrameWordFrontController('home', 'index', APP_PATH . 'layout/default.phtml');
        $routes = require_once APP_PATH.'config/route.php';
        $router->addRoutes($routes);
        $router->dispatch();
    }
         
    public static function profiler()
    {
        require_once(APP_PATH . 'library/Profiler.php');
        $profiler = new Profiler();
        $profiler->run();    
    }    
}  

