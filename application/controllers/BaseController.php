<?php
require_once(FW_PATH . 'library/Memcache.php');

class BaseController extends FrameworkController
{   
    public function init()
    {   
        $this->layout->user = $this->getUser(); 
        $this->_sweepFlashData();
        $this->_markFlashData();
    } 

    public function checkLogin(){
        $user = $this->getUser();
        if($user->uid < 1){
            redirect("/user/login");
        }  
    }
    
    protected function getUser()
    {
        $user = FrameworkRegistry::get('user');
        //$user->uid=5;
        //$user->name='ahha';
        return $user;
    }
    
    public function flashData($item)
    {
        $key = $item . ':old:';
        if (isset($_SESSION['flash'][$key])){
            return $_SESSION['flash'][$key]; 
        }
        return NULL;    
    }
    public function loadModel($name)
    {
        require_once APP_PATH.('models/'.$name.'.php');
        $model = new $name();
        return $model;
    }
    public function setFlashData($item, $value)
    {
        $key = $item . ':new:';
        $_SESSION['flash'][$key] = $value;   
    }
    
    public function openConnectOracle($DB_USER,$DB_PASS){
        $DB = '//172.20.96.197:1521/BVTC';
        //$DB_USER = 'homistc';
        //$DB_PASS = 'thucuc2011';
        $DB_CHAR = 'AL32UTF8';

        $conn = oci_connect($DB_USER, $DB_PASS, $DB, $DB_CHAR);
        
        return $conn;
    }

    public function closeConnectOracle($conn){
        oci_close($conn);
    }

    private function _markFlashData()
    {
        if (isset($_SESSION['flash'])){
            foreach($_SESSION['flash'] as $key => $value){
                $newKey = str_replace(':new:', ':old:', $key);
                $_SESSION['flash'][$newKey] = $value;                
                unset($_SESSION['flash'][$key]);
            }    
        }    
    }
    
    private function _sweepFlashData()
    {
        if (isset($_SESSION['flash'])){
            foreach($_SESSION['flash'] as $key => $value){
                if (strpos($key, ':old:') !== FALSE){                    
                    unset($_SESSION['flash'][$key]);    
                }                                                   
            }    
        }        
    }  
    public function searchCondToQuery($searchCond){
        $ret = "";
        if(is_array($searchCond) && count($searchCond) >0){

            foreach ($searchCond as $key=>$val){
                if($val!="")
                    $ret .= ($ret!=""?"&":"")."$key=$val";
            }
        }
        return $ret;
    }           
}
