<?php       
class FrameworkDatabase{
    
    private $_host   = ''; 
    private $_username     = ''; 
    private $_password     = ''; 
    private $_database = '';    
    private $_dbg = FALSE;             
    public $profiler = array();    
    private $_pdoObject = null;
    protected $_fetchMode = PDO::FETCH_OBJ;
    protected $_driverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");                       
    
    public function __construct($host, $username, $password, $database, $dbg = FALSE) 
    {     
        $this->_host = $host;
        $this->_username = $username;
        $this->_password = $password;
        $this->_database = $database;
        $this->_dbg = $dbg;                    
    }  
    
    private function _connect()
    {         
        if ($this->_pdoObject != null){
            return;    
        }          
        try {
            $connectionStr = 'mysql:dbname=' . $this->_database . ';host=' . $this->_host;        
            $this->_pdoObject = new PDO($connectionStr, $this->_username, $this->_password, $this->_driverOptions);    
        } catch (PDOException $e) {
            showError('Could not create PDO connection: <b>'. $e->getMessage() . '</b>.');          
        } 
    }     
    
    public function query($sql, $params = array())
    {           
        $statement = $this->_query($sql, $params);
        return $statement;
    }  
    
    public function fetchOne($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        return $statement->fetchColumn(0);
    }    
    
    public function fetchRow($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        return $statement->fetch($this->_fetchMode);
    }
     
    public function fetchAll($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        return $statement->fetchAll($this->_fetchMode);
    }

    public function lastInsertId($sequenceName = '')
    {
        return $this->_pdoObject->lastInsertId($sequenceName);
    } 
    
    private function _query($sql, $params = array())
    {
        if($this->_pdoObject == null) {
            $this->_connect();
        }                      
        if ($this->_dbg){  
            list($sm, $ss) = explode(' ', microtime());   
        }        
        $statement = $this->_pdoObject->prepare($sql, $this->_driverOptions);   
        if ( ! $statement) {
            $errorInfo = $this->_pdoObject->errorInfo();                                      
            throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }     
//        var_dump($sql);
        $paramsConverted = (is_array($params) ? ($params) : (array ($params )));  
        if (( ! $statement->execute($paramsConverted)) || ($statement->errorCode() != '00000')) {
            $errorInfo = $statement->errorInfo();                                                     
            throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }            
        if ($this->_dbg){ 
            list($em, $es) = explode(' ', microtime()); 
            $elapsed = number_format(($em + $es) - ($sm + $ss), 4); 
            $profile = new stdClass();
            $profile->query = $sql;            
            $profile->params = $paramsConverted;
            $profile->elapsed = $elapsed;
            $this->profiler[] = $profile;
        } 
        return $statement;
    }
    
    public function insert($table, array $bind)
    {
        if($this->_pdoObject == null) {
            $this->_connect();
        }         
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = '`'.$col.'`';
            $vals[] = $this->_pdoObject->quote($val);
        }         
        $sql = "INSERT INTO "
            . $table
            . ' (' . implode(', ', $cols) . ') '
            . 'VALUES (' . implode(', ', $vals) . ')';   
        $this->query($sql);                   
    }
    
    public function update($table, array $bind, $where = '')
    {
        echo 111;die;
        if($this->_pdoObject == null) {
            $this->_connect();
        }                 
        $set = array();
        foreach ($bind as $col => $val) {          
          $set[] = '`'.$col.'`' . ' = ' . $this->_pdoObject->quote($val);
        }                 
        if (is_array($where)) {
          $where = implode(' AND ', $where);
        }                 
        $sql = "UPDATE "
             . $table
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');
        $this->query($sql);                   
    }    
}
  
  