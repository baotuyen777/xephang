<?php 
class SessionManager
{
	public $db;
	public $session_enabled = true;
         
    public function __construct()
    {
        $this->db = FrameworkRegistry::get('session');             
    }
    		
	public function open($save_path, $name)
	{
		return true;
	}
	
	public function close()
	{	
		return true;
	}
	
	public function read($id)
	{           
		if ( ! $this->session_enabled) {
			$user = $this->getAnonymousUser();
			FrameworkRegistry::set('user', $user);
			return '';
		}        
        register_shutdown_function('session_write_close');                  
		if ( ! isset($_COOKIE[session_name()])) {
			$user = $this->getAnonymousUser();
			FrameworkRegistry::set('user', $user);
			return '';
		}                                                      
		$sql = 'SELECT * FROM sessions WHERE sid = ?';        
                $s = $this->db->fetchRow($sql, $id);         
                if ($s && $s->uid > 0) {
                    $u = new stdClass();
                    $u->uid = $s->uid;
                    $u->name = $s->name;                        
                    $u->session = $s->session;                                        
                } else {
                    $session = isset($s->session) ? $s->session : '';
                    $u = $this->getAnonymousUser($session);
                }

                FrameworkRegistry::set('user', $u);
                return $u->session;       
	}
	
	public function write($id, $sessionData)
	{   
		if ( ! $this->session_enabled) {
			return TRUE;
		}                            		
		$u = FrameworkRegistry::get('user');        
		if (empty($_COOKIE[session_name()]) && empty($sessionData)) {
			return TRUE;
		}                           
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];                          
        }                           
		$sql = 'SELECT sid FROM sessions WHERE sid = ?';
                $sid = $this->db->fetchOne($sql, $id);            
                if ($sid) {             
                    if ($u->uid || $sessionData || count($_COOKIE)) {
                        $data = array($u->uid, $u->name, $ip, time(), $sessionData, $id);                    
                        $sql = 'UPDATE sessions SET uid = ?, name = ?, hostname = ?, timestamp = ?, session = ? WHERE sid = ?';                
                        $this->db->query($sql, $data);                                        
                    }                                                                                                
                } else {                                                                      
                    $data = array($id, $u->uid, $u->name, $ip, time(), $sessionData);            
                    $sql = 'INSERT INTO sessions (sid, uid, name, hostname, timestamp, session) VALUES (?, ?, ?, ?, ?, ?)';
                    $this->db->query($sql, $data);              
                }         
                return true;    
	}
	
	public function destroy($id)
	{     
		if ( ! $this->session_enabled) {
			return true;
		}    		
		$sql = 'DELETE FROM sessions WHERE sid = ?';
		$this->db->query($sql, $id);       
		return true; 
	}
	                                  
	public function gc($maxLifetime)
	{     
		if (!$this->session_enabled) {
			return true;
		}                            		
		$sql = 'DELETE FROM sessions WHERE timestamp < ?'; 
        $this->db->query($sql, time()-$maxLifetime);           
		return true;  
	}
	
	public function getAnonymousUser($session='')
	{
		$user = new stdClass();
                $user->uid = 0;  
                $user->name = '';                     
                $user->hostname = $_SERVER['REMOTE_ADDR'];        
                $user->session = $session;      
                return $user;
	}
	
	public function updateLastLogin($uid)
	{   
		if (!$this->session_enabled) {
			return;
		}                             		
		$sql = 'UPDATE users SET login = ? WHERE uid = ?';
		$this->db->query($sql, array(time(), $uid));  
	}
	
	public function regenerateSessionID()
	{      
		if (!$this->session_enabled) {
			return;
		}            		
		$old_session_id = session_id();
		session_regenerate_id();
		$sql = 'UPDATE sessions SET sid = ? WHERE sid = ?';
		$this->db->query($sql, array(session_id(), $old_session_id)); 
	}
	
	private function unpack($obj) {
		if ($obj->data && $data = unserialize($obj->data)) {
			foreach ($data as $key => $value) {
				if (!isset($obj->$key)) {
					$obj->$key = $value;
				}
			}
		}
		return $obj;
	}  
}  