<?php
require_once 'BaseController.php';
class UserController extends BaseController
{
    public function loginAction()
    {           
        $user = $this->getUser();
        if ($user->uid > 0){
            redirect('/');
        }
        $this->view->loginFailed = FALSE;        
        if ($this->isPost()){            
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password = md5($password);
            $remember = $this->getPost('saveme');            
            $db = FrameworkRegistry::get('session');
            $sql = 'SELECT * FROM users WHERE name = ? AND pass = ?';
            $u = $db->fetchRow($sql, array($username, $password));            
            if ($u){                 
                $u = $this->unpack($u);                
                $sess = new SessionManager();
                $sess->updateLastLogin($u->uid);                
                if ($remember == 1){
                    $cookieParams = session_get_cookie_params();
                    session_set_cookie_params(
                            PHOTO_SESSION_TTL,
                            $cookieParams['path'],
                            $cookieParams['domain'],
                            $cookieParams['secure']
                            );
                }
                $sess->regenerateSessionID();                 
                FrameworkRegistry::set("user", $u);  
                redirect('/');              
            } else {
                $this->view->loginFailed = TRUE;
            } 
        } 
        $u = $this->getUser();                
        $this->view->user = $u;
    }
    
    public function logoutAction()
    { 
        $u = $this->getUser();
        if ($u->uid == 0){
            exit();
        }                          
        session_destroy();
        $sess = new SessionManager();        
        $u = $sess->getAnonymousUser();
        FrameworkRegistry::set('user', $u);                 
        redirect('/');
    }
    
    private function unpack($obj) {
        if ($obj->data && $data = unserialize($obj->data)) 
        {
            foreach ($data as $key => $value) {
                if (!isset($obj->$key)) {
                    $obj->$key = $value;
                }
            }
        }
        return $obj;
    }
    
    public function testAction()
    {
        echo date('d/m/Y H:i:s', 1299297530);
        echo "\n";
        echo date('d/m/Y H:i:s', 1299300846);            
        echo "<br/>";
        echo date('d/m/Y H:i:s', 1299298233);
        echo "\n";
        echo date('d/m/Y H:i:s', 1299319581);     
        $this->view->setNoRender();
        $this->disableLayout();
    }
}
?>