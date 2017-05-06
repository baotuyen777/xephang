<?php           
class BaseModel
{       
    public function getMasterDb()
    { 
        $db = require(APP_PATH . 'config/database.php');          
        if ( !FrameworkRegistry::isRegistered('master')){
            $masterDb = new FrameworkDatabase($db['master']['server'], $db['master']['username'], $db['master']['password'], $db['master']['database'], $GLOBALS['_DBG']);            
            FrameworkRegistry::set('master', $masterDb);
        }  
        return FrameworkRegistry::get('master');      
    } 
    
    public function getSlaveDb()
    {
        $db = require(APP_PATH . 'config/database.php');
        $i = rand(1, $db['slaveDbCount']);
        $slave = 'slave' . $i;
        if ( ! FrameworkRegistry::isRegistered($slave)){
            $slaveDb = new FrameworkDatabase($db[$slave]['server'], $db[$slave]['username'], $db[$slave]['password'], $db[$slave]['database'], $GLOBALS['_DBG']);
            FrameworkRegistry::set($slave, $slaveDb);
        }                    
        return FrameworkRegistry::get($slave);          
    } 
    public function insertToTable($table, $data){
        $db = $this->getMasterDb();
        $db->insert($table,$data);
        return $db->lastInsertId();
    }
    public function updateToTable($table,$data,$where){
        $db = $this->getMasterDb();
        $db->update($table,$data,$where);
    }
    public function deleteFromTable($table,$where){
        $db = $this->getMasterDb();
        $sql = "DELETE FROM $table WHERE $where";
        $db->query($sql);
    }
}                                                   
