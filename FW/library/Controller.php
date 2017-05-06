<?php 
class FrameWordFrontController
{     
    protected $layout = FALSE;
    protected $controllerPath;    
    protected $controller;
    protected $action;
    protected $params = array();
    private $_routes = array();

    public function __construct($defaultController = 'index', $defaultAction = 'index', $layout = FALSE){
        $this->controllerPath = APP_PATH . 'controllers/'; 
        $this->controller = $defaultController;
        $this->action = $defaultAction;
        $this->layout = $layout;       
    }
    
    public function dispatch()
    {
        $this->_parseHttpRequest()
             ->_doDispatch();    
    }
    
    private function _parseHttpRequest() 
    {
        $requri = $_SERVER['REQUEST_URI'];            
        $requri = trim($requri, '/');
        $requri = explode('?', $requri);        
        $requri = ($requri !== FALSE) ? $requri[0] : '';
        if ($this->_routes){            
            foreach ($this->_routes as $route){
                if ($route->match($requri)){
                    $this->controller = $route->controller;
                    $this->action = $route->action;
                    $this->params = $route->values;
                    return $this;
                }
            }
        }                        
        $p = $requri ? explode('/', $requri) : array();               
        $this->params = array(); 
        if (isset($p[0]) && $p[0]){
            $this->controller = strtolower($p[0]);    
        }                                                     
        if (isset($p[1]) && $p[1]){
            $this->action = strtolower($p[1]);
        }                                                 
        if (isset($p[2])){
            $this->params = array_slice($p,2);
        } 
        return $this;
    }     
        
    private function _doDispatch() 
    {
        $controllerClass = ucfirst($this->controller) .'Controller';
        $controllerFile = $this->controllerPath . $controllerClass . '.php';        
        if ( ! preg_match('#^[A-Za-z0-9_-]+$#', $this->controller) || ! file_exists($controllerFile)){
            show404('Controller file not found: ' . $controllerFile);
        }                                                                                       
        require($controllerFile);        
        $ctrl = new $controllerClass($this->controller, $this->action, $this->layout);
        $function = $this->action . 'Action';        
        if ( ! preg_match('#^[A-Za-z_][A-Za-z0-9_-]*$#', $function) || ! in_array($function, get_class_methods($ctrl))){
            show404('Invalid action name: ' . $function);
        }      
        if (in_array('init', get_class_methods($ctrl))){
            $ctrl->init();
        }     
        call_user_func_array(array($ctrl, $function),$this->params);        
        if ($ctrl->layout !== FALSE){            
            $ctrl->layout->content = $ctrl->view->fetch();            
            $ctrl->layout->render();            
        } else {
            $ctrl->view->render();
        }        
    }    
    
    public function addRoute($route)
    {
        $this->_routes[] = $route;
        return $this;
    }  
    
    public function addRoutes($routes){
    	$this->_routes = array_merge($this->_routes,$routes);
    }
}   

class FrameworkRoute
{
    private $_parts = array();
    private $_params = array();
    private $_values = array();
    private $_controller = 'index';   
    private $_action = 'index';   
    
    public function __construct($route = '', $controller =  'index', $action = 'index')
    {           
        $this->_controller = $controller;
        $this->_action = $action;
        $parts = explode('/', $route);        
        foreach ($parts as $pos => $part){
            if (substr($part, 0, 1) == ':' || substr($part, 0, 1) == '+'){
            	$this->_params[$pos] = array();
            	if (substr($part, 0, 1) == ':'){
            		$params = explode('-',$part);
            		foreach ($params as $order => $param){
            			$name = substr($param, 1);
                		$this->_params[$pos][$order] = $name;
            		}
            	}
            	
            	// extra information
            	if (substr($part, 0, 1) == '+'){
            		$params = $part;
            	}
            }
            $this->_parts[$pos] = $part;
        }               
    }
    
    public function match($route)
    {
        $route = trim($route, '/');      
        if ( ! $route){
            return FALSE;
        }                  
        $route = explode('?', $route);   
        $route = $route[0];    
        $parts = $route ? explode('/', $route) : array();
        foreach ($parts as $pos => $part){
            if ( ! array_key_exists($pos, $this->_parts)){
                return FALSE;
            }
            $name = isset($this->_params[$pos]) ? $this->_params[$pos] : NULL;      
            if ($name === NULL && $this->_parts[$pos] != $part){
                return FALSE;
            }    
            if ($name !== NULL && is_array($name)){
            	$params = explode('-',$part);
            	if (is_array($params)){
            		foreach ($params as $order => $param)
            		if (array_key_exists($order,$name))
                	$this->_values[$order] = $param;
            	}
            }     
        }    
        return TRUE;       
    }
    
    public function __get($var)
    {
        $var = '_' . $var;
        return $this->$var;
    }  
}

class FrameworkController
{       
    public $view = '';
    public $layout = FALSE;        
    
    public function __construct($controller, $action, $layout = '')
    {           
        $viewPath = APP_PATH . 'views/' . $controller . '/' . $action . '.phtml';        
        $this->view = new FrameworkView($viewPath);
        if ($layout){
            $this->layout = new FrameworkView($layout);
        }
    }
    
    public function render($path)
    {
        $path = APP_PATH . 'views/' . $path; 
        $this->view->setPath($path);
    }
        
    protected function disableLayout()
    {
        $this->layout = FALSE;
    }
    
    protected function setLayout($layout)
    {
        $this->layout->setPath($layout);    
    }     
            
    public function isPost()
    {
        if ('POST' == $this->getMethod()) {
            return true;
        }    
        return false;
    }         

    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }    
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }
    
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }
    
    public function getQuery($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }
    
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }
}

