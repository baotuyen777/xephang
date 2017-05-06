<?php   
require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');        

class HomeController extends BaseController
{
    public function init()
    {
        parent::init();              
    }

    
    
    public function indexAction()
    {   
        $this->checkLogin();  
    	$user = $this->getUser();
		$permission = require APP_PATH.('config/permission.php');
		if(array_key_exists($user->uid,$permission)){
			redirect("/room");
		}else{
			redirect("/le-tan");
		}
    } 

    public function serviceAction(){
        $no = (int)$this->getQuery('no',0);
        if($no < 1)
            exit;

        $DB_USER = 'homistc2016';
        $DB_PASS = 'thucuc2016';

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);

        //$arrayQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID FROM HDON WHERE HDON_ID = '$no'");
        /*$arrayQuery = oci_parse($conn, "SELECT TABLE_NAME,SUM(THANH_TIEN) AS THANH_TIEN FROM
        (SELECT 'PHÍ KHÁM SCC' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID = 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT 'PHÍ KHÁM' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID <> 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT CASE WHEN TABLE_NAME = 'CDHA_NS' THEN 'CĐHA' ELSE CASE WHEN TABLE_NAME='XNGHIEM' THEN 'XÉT NGHIỆM' ELSE
        CASE WHEN TABLE_NAME='DVU_KHAC' THEN 'DỊCH VỤ KHÁC' ELSE 'CHI PHÍ GIƯỜNG ĐT' END END END AS TABLE_NAME, 
        SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('CDHA_NS','XNGHIEM','DVU_KHAC','GIUONG_DT') AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT 'PTTM' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN PHIEU_MO b ON a.TABLE_ID=b.PHIEU_MO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('PHIEU_MO') AND c.MA_LMO='P15' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME
        UNION
        SELECT 'PTTM' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN TTHUAT_NHO b ON a.TABLE_ID=b.TTHUAT_NHO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('TTHUAT_NHO') AND c.MA_LMO='1' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME)
        GROUP BY TABLE_NAME");*/
        $arrayQuery = oci_parse($conn, "SELECT TABLE_NAME,SUM(THANH_TIEN) AS THANH_TIEN FROM
        (SELECT 'PHÍ KHÁM SCC' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID = 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT 'PHÍ KHÁM' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID <> 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT CASE WHEN TABLE_NAME = 'CDHA_NS' THEN 'CĐHA' ELSE CASE WHEN TABLE_NAME='XNGHIEM' THEN 'XÉT NGHIỆM' ELSE
        CASE WHEN TABLE_NAME='DVU_KHAC' THEN 'DỊCH VỤ KHÁC' ELSE 'CHI PHÍ GIƯỜNG ĐT' END END END AS TABLE_NAME, 
        SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('CDHA_NS','XNGHIEM','DVU_KHAC','GIUONG_DT') AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT 'PTTM' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN PHIEU_MO b ON a.TABLE_ID=b.PHIEU_MO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('PHIEU_MO') AND c.MA_LMO='P15' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME
        UNION
        SELECT 'PTTT' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN PHIEU_MO b ON a.TABLE_ID=b.PHIEU_MO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('PHIEU_MO') AND c.MA_LMO <> 'P15' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME
        UNION
        SELECT 'PTTM' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN TTHUAT_NHO b ON a.TABLE_ID=b.TTHUAT_NHO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('TTHUAT_NHO') AND c.MA_LMO='1' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME
        UNION
        SELECT 'PTTT' AS TABLE_NAME, SUM(a.BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU a INNER JOIN TTHUAT_NHO b ON a.TABLE_ID=b.TTHUAT_NHO_ID
        INNER JOIN MA_MO c ON b.MA_MO=c.MA_MO
        WHERE a.TABLE_NAME IN('TTHUAT_NHO') AND c.MA_LMO <> '1' AND a.HDON_ID =$no GROUP BY a.TABLE_NAME)
        GROUP BY TABLE_NAME");

        oci_execute($arrayQuery);
        $output = array();
        while ($row = oci_fetch_array($arrayQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $input = new stdClass();
            $input->TABLE_NAME = $row['TABLE_NAME'];
            $input->VALUE = $row['THANH_TIEN'];
            $output[] = $input;
        }
     
        echo json_encode($output);exit;
        /*if($row != false){
            $PHIEU_KHAM_ID = $row[0];

            $arrayQueryResult = oci_parse($conn, "SELECT YEU_CAU.BPHAN_YCAU_ID,YEU_CAU.BPHAN_THIEN_ID,HDON_CTIET.TEN_MUC,HDON_CTIET.BNHAN_TRA FROM YEU_CAU INNER JOIN HDON_CTIET ON YEU_CAU.YEU_CAU_ID = HDON_CTIET.YEU_CAU_ID WHERE YEU_CAU.PHIEU_KHAM_ID = '$PHIEU_KHAM_ID'");
            oci_execute($arrayQueryResult);

            $ouput = array();

            while ($rowResult = oci_fetch_array($arrayQueryResult, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $maBenh = $rowResult['MA_BENH'];
                $tenBenh = $rowResult['TEN_BENH'];

                $ouput[] = array(
                    'TEN_MUC'=>$rowResult['TEN_MUC'],
                    'BNHAN_TRA'=>$rowResult['BNHAN_TRA'],
                    'BPHAN_YCAU_ID'=>$rowResult['BPHAN_YCAU_ID'],
                    'BPHAN_THIEN_ID'=>$rowResult['BPHAN_THIEN_ID'],
                );
            }
            $this->closeConnectOracle($conn);
            header('Content-Type: application/json');
            echo json_encode($ouput);
            exit;
        }*/
    }  

    /*public function serviceAction(){
        $no = (int)$this->getQuery('no',0);
        if($no < 1)
            exit;

        $DB_USER = 'homistc2016';
        $DB_PASS = 'thucuc2016';

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);

        $arrayQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID FROM HDON WHERE HDON_ID = '$no'");
        oci_execute($arrayQuery);
        $row = oci_fetch_row($arrayQuery);

        if($row != false){
            $PHIEU_KHAM_ID = $row[0];

            $arrayQueryResult = oci_parse($conn, "SELECT YEU_CAU.BPHAN_YCAU_ID,YEU_CAU.BPHAN_THIEN_ID,HDON_CTIET.TEN_MUC,HDON_CTIET.BNHAN_TRA FROM YEU_CAU INNER JOIN HDON_CTIET ON YEU_CAU.YEU_CAU_ID = HDON_CTIET.YEU_CAU_ID WHERE YEU_CAU.PHIEU_KHAM_ID = '$PHIEU_KHAM_ID'");
            oci_execute($arrayQueryResult);

            $ouput = array();

            while ($rowResult = oci_fetch_array($arrayQueryResult, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $maBenh = $rowResult['MA_BENH'];
                $tenBenh = $rowResult['TEN_BENH'];

                $ouput[] = array(
                    'TEN_MUC'=>$rowResult['TEN_MUC'],
                    'BNHAN_TRA'=>$rowResult['BNHAN_TRA'],
                    'BPHAN_YCAU_ID'=>$rowResult['BPHAN_YCAU_ID'],
                    'BPHAN_THIEN_ID'=>$rowResult['BPHAN_THIEN_ID'],
                );
            }
            $this->closeConnectOracle($conn);
            header('Content-Type: application/json');
            echo json_encode($ouput);
            exit;
        }
    }*/ 

    public function revenueAction(){
        $id = (int)$this->getQuery('id',0);
        $from_date = htmlspecialchars($this->getQuery('from_date',''));
        $to_date = htmlspecialchars($this->getQuery('to_date',''));

        if($id < 1 || $from_date == '' || $to_date == '')
            exit;

        $DB_USER = 'homistc2016';
        $DB_PASS = 'thucuc2016';

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);

        $queryYEUCAU = oci_parse($conn, "SELECT YEU_CAU_ID FROM YEU_CAU WHERE BPHAN_THIEN_ID = '$id' AND NGAY_THIEN >=  to_date('$from_date', 'YYYY-MM-DD HH24:MI:SS') AND NGAY_THIEN <= to_date('$to_date', 'YYYY-MM-DD HH24:MI:SS') AND HDON_ID is not null");

        oci_execute($queryYEUCAU);
        $count = 0;
        $total = 0;
        $YEU_CAU_ID = array();
        while ($row = oci_fetch_array($queryYEUCAU, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $count++;
            $YEU_CAU_ID[] = $row['YEU_CAU_ID'];
        }
        
        if(count($YEU_CAU_ID) > 0){
            $YEU_CAU_ID = implode(',', $YEU_CAU_ID);
            $queryRevenue = oci_parse($conn,"SELECT sum(BNHAN_TRA) FROM HDON_CTIET WHERE YEU_CAU_ID in ($YEU_CAU_ID) AND TRANG_THAI <> 'H' AND TRANG_THAI <> 'C'");
            oci_execute($queryRevenue);
            $rowRevenue = oci_fetch_row($queryRevenue);

            if($rowRevenue != false)
                $total = $rowRevenue[0];
        }
        $this->closeConnectOracle($conn);
        $ouput = array(
            'count'=>$count,
            'total'=>$total
        );
        header('Content-Type: application/json');
        echo json_encode($ouput);
        exit;
    }
    public function serviceidAction(){
        $sid = htmlspecialchars($this->getQuery('sid',''));
        $from_date = htmlspecialchars($this->getQuery('from_date',''));
        $to_date = htmlspecialchars($this->getQuery('to_date',''));

        if($sid  == '' || $from_date == '' || $to_date == '')
            exit;

        $DB_USER = 'homistc2016';
        $DB_PASS = 'thucuc2016';

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);

        $queryYEUCAU = oci_parse($conn, "SELECT YEU_CAU_ID FROM YEU_CAU WHERE GHI_CHU = '$sid' AND NGAY_THIEN >=  to_date('$from_date', 'YYYY-MM-DD HH24:MI:SS') AND NGAY_THIEN <= to_date('$to_date', 'YYYY-MM-DD HH24:MI:SS') AND HDON_ID is not null AND TRANG_THAI <> 'H' AND TRANG_THAI <> 'C' AND BNHAN_TRA <> 0");

        oci_execute($queryYEUCAU);
        $count = 0;
        $total = 0;
        $YEU_CAU_ID = array();
        $table = '';
        while ($row = oci_fetch_array($queryYEUCAU, OCI_ASSOC+OCI_RETURN_NULLS)) {
            if($row['YEU_CAU_ID'] != ''){
                $count++;
                $YEU_CAU_ID[] = $row['YEU_CAU_ID'];
            }
            //$table = $row['TABLE_NAME'];
        }
        if(count($YEU_CAU_ID) > 0){
            $YEU_CAU_ID = implode(',', $YEU_CAU_ID);
            //echo "SELECT sum(BNHAN_TRA) FROM HDON_CTIET WHERE YEU_CAU_ID in ($YEU_CAU_ID) AND TRANG_THAI <> 'C' AND TRANG_THAI <> 'H'";
            $queryRevenue = oci_parse($conn,"SELECT sum(BNHAN_TRA),COUNT(YEU_CAU_ID) FROM HDON_CTIET WHERE YEU_CAU_ID in ($YEU_CAU_ID) AND TRANG_THAI <> 'C' AND TRANG_THAI <> 'H'");
            oci_execute($queryRevenue);
            $rowRevenue = oci_fetch_row($queryRevenue);

            if($rowRevenue != false){
                $total = $rowRevenue[0];
                $count = $rowRevenue[1];
            }
        }
        $this->closeConnectOracle($conn);
        $ouput = array(
            'count'=>$count,
            'total'=>$total
        );
        header('Content-Type: application/json');
        echo json_encode($ouput);
        exit;
    }

    public function serviceallAction(){
        $from_date = htmlspecialchars($this->getQuery('from_date',''));
        $to_date = htmlspecialchars($this->getQuery('to_date',''));

        if($from_date == '' || $to_date == '')
            exit;

        $DB_USER = 'homistc2016';
        $DB_PASS = 'thucuc2016';

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);
        
        //echo "SELECT HDON_CTIET.BNHAN_TRA AS BNHAN_TRA,YEU_CAU.GHI_CHU AS GHI_CHU FROM YEU_CAU INNER JOIN HDON_CTIET ON YEU_CAU.YEU_CAU_ID = HDON_CTIET.YEU_CAU_ID WHERE YEU_CAU.NGAY_THIEN >=  to_date('$from_date', 'YYYY-MM-DD HH24:MI:SS') AND YEU_CAU.NGAY_THIEN <= to_date('$to_date', 'YYYY-MM-DD HH24:MI:SS') AND YEU_CAU.HDON_ID is not null AND HDON_CTIET.TRANG_THAI = 'K'";

        $queryYEUCAU = oci_parse($conn, "SELECT HDON_CTIET.BNHAN_TRA AS BNHAN_TRA,YEU_CAU.GHI_CHU AS GHI_CHU, PHIEU_KHAM.BHYT_YN AS BHYT, PHIEU_KHAM.DTUYEN_YN AS DTUYEN FROM YEU_CAU INNER JOIN HDON_CTIET ON YEU_CAU.YEU_CAU_ID = HDON_CTIET.YEU_CAU_ID LEFT JOIN PHIEU_KHAM ON YEU_CAU.PHIEU_KHAM_ID = PHIEU_KHAM.PHIEU_KHAM_ID WHERE YEU_CAU.NGAY_THIEN >=  to_date('$from_date', 'YYYY-MM-DD HH24:MI:SS') AND YEU_CAU.NGAY_THIEN <= to_date('$to_date', 'YYYY-MM-DD HH24:MI:SS') AND YEU_CAU.HDON_ID is not null AND HDON_CTIET.TRANG_THAI <> 'C' AND HDON_CTIET.TRANG_THAI <> 'H' AND HDON_CTIET.BNHAN_TRA <> 0 AND YEU_CAU.GHI_CHU is not null");

        oci_execute($queryYEUCAU);
        $dataOut = array();
        while ($row = oci_fetch_array($queryYEUCAU, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $dataOut[$row['GHI_CHU']]['total'] = $dataOut[$row['GHI_CHU']]['total'] + $row['BNHAN_TRA'];
            $dataOut[$row['GHI_CHU']]['count']++;
            $dataOut[$row['GHI_CHU']][$row['BHYT'].'-'.$row['DTUYEN']]++;
        }
        $this->closeConnectOracle($conn);
        header('Content-Type: application/json');
        echo json_encode($dataOut);
        exit;
    }
    
}
  
