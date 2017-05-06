<?php

require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');

class AdvController extends BaseController {

    public function init() {
        parent::init();
        $this->checkLogin();
        $permission = require APP_PATH . ('config/permission.php');
        $user = $this->getUser();
        if (!array_key_exists($user->uid, $permission)) {
            redirect('/');
        }
        $this->advModel = $this->loadModel('AdvModel');
    }

    public function indexAction() {
        redirect(APP_URL . '/adv/list');
    }

    public function listAction() {
        $this->layout->isQueue = 1;

        $arrAllAdv = $this->advModel->getAll();
        $this->view->arrAllAdv = $arrAllAdv;
    }

    public function detailAction($id = false) {
        $this->layout->isQueue = 1;
        if ($this->isPost()) {
//            var_dump($file_upload);
            $upload = $this->upload($_FILES);
            if ($upload['status']) {
                $data = array(
                    'name' => $_POST['name'],
                    'orders' => $_POST['orders'],
                    'img' => $_FILES['img']['name'],
                    'status' => isset($_POST['status']) ? $_POST['status'] : 0,
                );
                if ($this->advModel->add($data)) {
                    redirect(APP_URL . '/adv');
                }
            } else {
                $this->view->mes = $upload['mes'];
            }
        }




        if ($id)
            $this->view->id = $id;
    }

    function upload($file_upload) {
        $target_dir = PUBLIC_PATH . "/upload/";
        $target_file = $target_dir . basename($file_upload["img"]["name"]);
        $status = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        $check = getimagesize($file_upload["img"]["tmp_name"]);
        if (!$check) {
            $mes = "Yêu cầu gửi lên 1 định dạng ảnh";
            $status = 0;
        }
// Check if file already exists
//        if (file_exists($target_file)) {
//            $mes = "Ảnh đã tồn tại";
//            $status = 0;
//        }
// Check file size
        if ($file_upload["img"]["size"] > 5000000) {
            $mes = "File quá lớn(<5Mb)";
            $status = 0;
        }
// Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $mes = "Chỉ sử dụng định dạng JPG, JPEG, PNG & GIF.";
            $status = 0;
        }
// Check if $status is set to 0 by an error
        if ($status != 0) {
            if (move_uploaded_file($file_upload["img"]["tmp_name"], $target_file)) {
                $mes = "" . basename($file_upload["img"]["name"]) . " tải lên thành công!.";
            } else {
                $mes = "Xảy ra lỗi trong quá trình upload file (phân quyền, sự cố mạng)";
            }
        }
        return array(
            'status' => $status,
            'mes' => $mes
        );
    }

}
