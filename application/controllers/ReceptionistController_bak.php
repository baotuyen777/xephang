<?php   
require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');        

class ReceptionistController extends BaseController
{
    public function init()
    {
        parent::init();   
        $this->checkLogin();  
        $permission = require_once APP_PATH.('config/permission.php');
        $user = $this->getUser();
        if(array_key_exists($user->uid,$permission)){
            //redirect('/');
        }           
    }
    
    public function indexAction()
    {   
        $msg = '';
        $tc = '';
        if($this->isPost()){
         	$tc = htmlspecialchars($this->getPost('tc',''));
            $room = (int)$this->getPost('room',0);
         	if($tc != '' && $room != 0){
         		$tcExplode = explode('-', $tc);
         		$tcTG = $tcExplode[0];
         		$tcSTT = $tcExplode[2];

         		$conn = $this->openConnectOracle();
                    $arrayQuery = oci_parse($conn, "SELECT BENH_NHAN_ID FROM PHIEU_KHAM WHERE SO_PHIEU_TG = '$tcTG' AND SO_PHIEU_STT = '$tcSTT'");
                    oci_execute($arrayQuery);
                    $row = oci_fetch_row($arrayQuery);
                    if($row != false){
                            $benhNhanID = $row[0];
                            $arrayQueryInfo = oci_parse($conn, "SELECT BENH_NHAN_ID,MA_BNHAN,TEN_BNHAN,NGAY_SINH,GIOI_TINH FROM BENH_NHAN WHERE BENH_NHAN_ID = '$benhNhanID'");
                            oci_execute($arrayQueryInfo);

                            $info = oci_fetch_row($arrayQueryInfo);
                            if($info != false){
                                
                                    $benhNhanID = $info[0];
                                    $maBNhan = $info[1];
                                    $tenBNhan = $info[2];
                                    $ngaySinh = $info[3];
                                    $gioiTinh = $info[4];

                                    $receptionistModel = $this->loadModel('BnhanModel');
                                    $sort = $receptionistModel->getSTTNow($room);
                                    if($sort == false){
                                        $sort = 1;
                                    }else{
                                        $sort = $sort->sort + 1;
                                    }

                                    $arrayBenhNhan = array(
                                        'BENH_NHAN_ID'=>$benhNhanID,
                                        'MA_BNHAN'=>$maBNhan,
                                        'TEN_BNHAN'=>$tenBNhan,
                                        'NGAY_SINH'=>$ngaySinh,
                                        'GIOI_TINH'=>$gioiTinh,
                                        'tc'=>$tc,
                                        'status'=>0,
                                        'created'=>time(),
                                        'updated'=>time(),
                                        'room'=>$room
                                    );

                                    $arrayBenhNhanQueue = array(
                                        'BENH_NHAN_ID'=>$benhNhanID,
                                        'TEN_BNHAN'=>$tenBNhan,
                                        'ROOM'=>$room,
                                        'status'=>0,
                                        'tc'=>$tc,
                                        'created'=>time(),
                                        'sort'=>$sort
                                    );

                                    
                                    $receptionistModel->insertToTable('BENH_NHAN',$arrayBenhNhan);
                                    $receptionistModel->insertToTable('BENH_NHAN_QUEUE',$arrayBenhNhanQueue);
                                    $msg = "Bệnh nhân đã được đưa vào danh sách đợi phòng khám số $room";
                                
                            }else{
                                $msg = "Không tìm thấy thông tin bệnh nhân.";
                            }
                        
                    }else{
                        $msg = "Không tìm thấy thông tin bênh nhân.";
                    }
                
         	  $this->closeConnectOracle($conn);
            }
        }

        $permission = require APP_PATH.('config/permission.php');
        $user = $this->getUser();

        $room = $permission[$user->uid];

        $this->view->r = $room;
        $this->view->tc = $tc;
        $this->setFlashData("msg",$msg);
        $this->view->msg = $this->flashData("msg");

    }  
    public function viewAction(){
        $room = (int)$this->getQuery('room',1);

        $bNhanModel = $this->loadModel('BnhanModel');

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
        }
        $this->view->roomID = $room;
        $this->view->bNhan = $bNhan;
    } 
    public function dragAction(){

    }
    public function listAction(){
        $page = (int)$this->getQuery('page',1);
        $search = array();

        $search['TEN_BNHAN'] = trim($this->getQuery('TEN_BNHAN', ''));
        $search['tc'] = trim($this->getQuery('tc', ''));
        $search['status'] = (int)$this->getQuery('status', -1);

        $bNhanModel = $this->loadModel("BnhanModel");
        $this->view->search = $search;
        $listBNhan = $bNhanModel->searchBNhan($search,$page);
        $this->view->list = $listBNhan;

        $page_total = ceil((int) count($listBNhan) / COUNT_ITEMS_PER_PAGE);
        $this->view->totalPages = $page_total;
        $this->view->$page_total = $page;
        $this->view->url = '/danh-sach';
        $this->view->ext_param = $ext_params;
        $this->view->pager = $this->view->partial('pager.phtml', array('totalPages' => $page_total, '$page_total' => $page, 'ext_param' => $ext_params));
    } 
}

  
