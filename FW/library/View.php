<?php
class FrameworkView {
    protected $_file = '';
    protected $_vars = array();
    protected $_noRender = FALSE;

    public function __construct($file = '',$vars='')  {
        if ($file){
            $this->_file = $file;
        }              
        if (is_array($vars)){
            $this->_vars=$vars;
        }                
        return $this;
    }

    public function __set($key,$var) {
        return $this->set($key,$var);
    } 
    
    public function set($key, $var)
    {
        $this->_vars[$key] = $var;
    }
    
    public function getPath()
    {
        return $this->_file;
    }   
    
    public function setPath($file)
    {
        $this->_file = $file;
    }
    
    public function fetch() 
    {     
        if ($this->_noRender){
            return FALSE;
        }                                      
        extract($this->_vars);
        ob_start();
        require($this->_file);
        return ob_get_clean();
    }

    public function partial($file, $vars = '') {
        if (is_array($vars)){
            extract($vars);
        } 
        ob_start();
        require(APP_PATH . 'views/' . $file);
        return ob_get_clean();
    }

    public function render($vars = '') {
        if ($this->_noRender){
            return FALSE;
        }                                      
        if (is_array($vars)){
            $this->_vars = array_merge($this->_vars,$vars);
        }                                                            
        extract($this->_vars); 
        require($this->_file);
    }  
    
    public function setNoRender()
    {
        $this->_noRender = TRUE;
    }      
}