<?php
class FrameworkDatabase
{
    private $server   = ""; 
    private $user     = ""; 
    private $pass     = ""; 
    private $database = "";    
    
    private $error = "";
    private $errno = 0;
        
    private $affected_rows = 0;
    private $connectionId = FALSE;    
    private $queryId = FALSE;
    private $resultId = FALSE;
    public $profiler = array();    
    private $_dbg = FALSE;
   
    public function __construct($server, $user, $pass, $database, $dbg = FALSE)
    {            
        $this->_dbg = $dbg;        
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;        
    }
    
    public function connect($newLink = FALSE)
    {
        $this->connectionId = @mysql_connect($this->server, $this->user, $this->pass, $newLink);  
        if ( ! $this->connectionId){
            showError('Could not connect to server: <b>' . $this->server . '</b>');
        }           
        if( ! @mysql_select_db($this->database, $this->connectionId)){
            showError('Could not open database: <b>'. $this->database . '</b>.');
        }  
        mysql_query("SET NAMES 'utf8';", $this->connectionId);
    }  
    
    public function query($sql, $binds = FALSE, $return_object = TRUE)
    {
        $this->connect();
        if ($sql == ''){
            showError('Invalid Query');
            return FALSE;
        }        
        if ($binds !== FALSE){            
            $sql = $this->_compileBinds($sql, $binds);            
        }
        if ($this->_dbg){  
            list($sm, $ss) = explode(' ', microtime());   
        }        
        $resultId = $this->_execute($sql);
        if (FALSE === $resultId){                
            $errorNo = $this->_errorNumber();
            $errorMsg = $this->_errorMessage();   
            showError('Error: ' . $errorNo . '<br />' . 'Messages: ' . $errorMsg);    
            return FALSE;
        } 
        if ($this->_dbg){ 
            list($em, $es) = explode(' ', microtime()); 
            $elapsed = number_format(($em + $es) - ($sm + $ss), 4);   
            if ( ! is_array($binds)){
                $binds = array($binds);
            }
            $profile = new stdClass();
            $profile->query = $sql;            
            $profile->params = $binds;
            $profile->elapsed = $elapsed;
            $this->profiler[] = $profile;
        }
        if ($this->_isWriteType($sql) === TRUE){ 
            return TRUE;
        }                  
        return $resultId;
    } 
    
    public function fetchAll($sql, $binds = FALSE)
    {
        $resultId = $this->query($sql, $binds);                      
        $result = array();                      
        if ($resultId){            
            while ($row = mysql_fetch_object($resultId)){                                
                $result[] = $row;
            }
        }        
        return $result;               
    }
    
    public function fetchRow($sql, $binds = FALSE)
    {
        $result = $this->fetchAll($sql, $binds);
        if (count($result) == 0){
            return FALSE;
        }    
        return $result[0];
    }
    
    public function fetchOne($sql, $binds = FALSE)
    {
        $row = $this->fetchRow($sql, $binds);
        if ( $row === FALSE){
            return FALSE;
        }
        $val = array_shift(get_object_vars($row));            
        return $val;
    }   
    
    public function lastInsertId()
    {
        return mysql_insert_id();
    }
    
    private function _execute($sql)
    {           
        return @mysql_query($sql, $this->connectionId);
    }
    
    private function _compileBinds($sql, $binds)
    {
        if (strpos($sql, '?') === FALSE){
            return $sql;
        }                                       
        if ( ! is_array($binds)){
            $binds = array($binds);
        }                       
        $segments = explode('?', $sql);
        if (count($binds) >= count($segments)){
            $binds = array_slice($binds, 0, count($segments) - 1);
        }                                                     
        $result = $segments[0];
        $i = 0;
        foreach ($binds as $bind){
            $result .= $this->escape($bind);
            $result .= $segments[++$i];
        }               
        return $result;
    }
    
    public function escape($str)
    {
        if (is_string($str)){
            $str = "'" . $this->escapeStr($str) . "'";
        } elseif (is_bool($str)){
            $str = ($str === FALSE) ? 0 : 1;
        } elseif (is_null($str)){
            $str = 'NULL';
        }        
        return $str;
    }
    
    public function escapeStr($str)    
    {    
        if (is_array($str)){
            foreach($str as $key => $val){
                $str[$key] = $this->escape_str($val, $like);
            }           
            return $str;
        }
        if (function_exists('mysql_real_escape_string') AND is_resource($this->connectionId)){
            $str = mysql_real_escape_string($str, $this->connectionId);
        } elseif (function_exists('mysql_escape_string')){
            $str = mysql_escape_string($str);
        } else{
            $str = addslashes($str);
        }
        return $str;
    }
    
    private function _errorMessage()
    {
        return mysql_error($this->connectionId);
    }
    
    private function _errorNumber()
    {
        return mysql_errno($this->connectionId);
    }
    
    private function _isWriteType($sql)
    {
        if ( ! preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql)){
            return FALSE;
        }
        return TRUE;
    }    
}