<?php   
require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');        

class RoomController extends BaseController
{
	public function init()
    {
        parent::init();        
        $this->checkLogin();
        $permission = require APP_PATH.('config/permission.php');
        $user = $this->getUser();
        if(!array_key_exists($user->uid,$permission)){
            redirect('/');
        }        
    }
    
    public function indexAction()
    {   
        $permission = require APP_PATH.('config/permission.php');
     	$user = $this->getUser();

        $room = $permission[$user->uid];

     	$status = (int)$this->getQuery('status',0);
     	$id = (int)$this->getQuery('id',0);

     	$bNhanModel = $this->loadModel('BnhanModel');

     	if($status == 1 && $id != 0){
     		$bNhanModel->updateToTable('BENH_NHAN_QUEUE',array('status'=>1),'BENH_NHAN_ID='.$id);
            $bNhanModel->updateToTable('BENH_NHAN',array('status'=>1),'BENH_NHAN_ID='.$id);
     		redirect('/room');
     	}
        if($status == 2 && $id != 0){
            $bNhanModel->deleteFromTable('BENH_NHAN_QUEUE','BENH_NHAN_ID='.$id);
            $bNhanModel->updateToTable('BENH_NHAN',array('status'=>2),'BENH_NHAN_ID='.$id);
            redirect('/room');
        }

        $bNhan = $bNhanModel->getBNhanByRoom($room);

        $BnArray = array();
        foreach($bNhan as $value){
            $BnArray[$value->sort] = $value->BENH_NHAN_ID;
        }

        if($this->isPost()){
            $action = htmlspecialchars($this->getPost("action",""));
            $oldIndex = (int)$this->getPost("oldindex",0);
            $newIndex = (int)$this->getPost("newindex",0);

            if($action == 'drag' && $oldIndex >= 0 && $newIndex >= 0 && $oldIndex != $newIndex){
                $oldSort = $bNhan[$oldIndex]->sort;
                $newSort = $bNhan[$newIndex]->sort;
                $oldId = $bNhan[$oldIndex]->BENH_NHAN_ID;
                $newId = $bNhan[$newIndex]->BENH_NHAN_ID;

                $cacheValue = $BnArray[$oldSort];
                unset($BnArray[$oldSort]);

                $start = 0;
                if($oldIndex > $newIndex){
                    $start = 1;
                }else{
                    $start = -1;
                }
                $newBnArray = array();
                foreach ($BnArray as $key => $value) {
                    if($key >= $newSort){
                        $newBnArray[$key+1] = $value;
                    }

                    if($newSort > $oldSort){
                        if($key <= $newSort){
                            $newBnArray[$key - 1] = $value;
                        }
                    }

                }
                $newBnArray[$newSort] = $cacheValue;

                $count = 0;
                foreach($newBnArray as $key=>$value){

                    /*if($key >= $newSort){
                        $count++;
                        $key = $key+$count;
                    }*/
        
                    if($key < 1)
                        $key = 1;

                    $bNhanModel->updateSort($key,$value);
                }
                exit();
            }

            if($action == 'swap'){
                $arrayBnhanId = $this->getPost("arrayBnhanId","");
                $room = (int)$this->getPost("room",0);
                if($room != 0 && count($arrayBnhanId) > 0){
                    foreach ($arrayBnhanId as $value) {
                        $sort = $bNhanModel->getSTTNow($room);
                        if($sort == false){
                            $sort = 1;
                        }else{
                            $sort = $sort->sort + 1;
                        }
                        $bNhanModel->updateRoom($room,$sort,$value);
                    }
                    exit;
                }
            }
        }

        /*if($this->isPost()){
            $action = htmlspecialchars($this->getPost("action",""));
            $oldIndex = (int)$this->getPost("oldindex",0);
            $newIndex = (int)$this->getPost("newindex",0);

            if($action == 'drag' && $oldIndex >= 0 && $newIndex >= 0 && $oldIndex != $newIndex){
                $oldSort = $bNhan[$oldIndex]->sort;
                $newSort = $bNhan[$newIndex]->sort;
                $oldId = $bNhan[$oldIndex]->BENH_NHAN_ID;
                $newId = $bNhan[$newIndex]->BENH_NHAN_ID;

                $bNhanModel->updateSort($newSort,$oldId);
                $bNhanModel->updateSort($oldSort,$newId);
                exit();

            }
        }*/
        $this->view->r = $room;
     	$this->view->bNhan = $bNhan;
    }
    public function queueAction(){
        $this->layout->isQueue = 2;
        $permission = require APP_PATH.('config/permission.php');
        $user = $this->getUser();

        $room = $permission[$user->uid];

        $bNhanModel = $this->loadModel('BnhanModel');
        $bNhan = $bNhanModel->getBNhanByRoom($room);
        
        $this->view->r = $room;
        $this->view->bNhan = $bNhan;
    }
}