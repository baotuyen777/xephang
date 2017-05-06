<?php   
require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');        

class HomeController extends BaseController
{
    public function init()
    {
        parent::init();              
    }
    
	public function sqlAction(){
        $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
        //$alter_schema = "ALTER SESSION SET CURRENT_SCHEMA = HIS_LIS";
        $select_pid = "select PID from HIS_LIS.TBL_FORM where ORDERID = 522028";
        oci_execute($select_pid);
        $row = oci_fetch_row($select_pid);
        $pid = array();
        if($row != false){
            $pid = $row[0];
        }

        $connection = mssql_connect("172.20.96.220:1433", "thongke", "thongkels") or die ("Cannot connect to Server");
        $selected = mssql_select_db('LabConnTC', $connection)or die("Cannot select DB"); 
            
        $sql=mssql_query("select * from tbl_Result where OrderID = 522028") or die(mssql_min_error_severity()); 
        $ct = mssql_num_rows($sql);
        $result = array();
        $count = 1;
        while ($row = mssql_fetch_array($sql, MSSQL_NUM)) {
            if($count == 1){
                $result['tech'] = $row[7];
            }
            $result['detail'][$count] = array(
                'ma'=>$row[34],
                'xetnghiem'=>$row[7],
                'ketqua'=>$row[3],
                'trisobinhthuong'=>$row[8],
                'donvi'=>$row[5],
            );
            $count++;
        }

        echo "<pre>";
        var_dump($result);
        echo "</pre>";
        exit;

        
        // Connect to MSSQL
        $connection = mssql_connect("172.20.96.220:1433", "thongke", "thongkels") or die ("Cannot connect to Server");
        $selected = mssql_select_db('LabConnTC', $connection)or die("Cannot select DB"); 
        
        $sql=mssql_query("select * from tbl_Result where OrderID = 522028") or die(mssql_min_error_severity()); 
        $ct = mssql_num_rows($sql);
        echo '<meta charset="utf-8">';
        while ($row = mssql_fetch_array($sql, MSSQL_NUM)) {
            echo "<pre>";
            var_dump($row);
            echo "</pre>";
        }
        exit;
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
        $no = htmlspecialchars($this->getQuery('no',''));

        if($no == '')
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

        /*$arrayQuery = oci_parse($conn, "SELECT TABLE_NAME,SUM(THANH_TIEN) AS THANH_TIEN FROM
        (SELECT 'PHI KHAM SCC' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID = 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT 'PHI KHAM' AS TABLE_NAME, SUM(BNHAN_TRA) AS THANH_TIEN FROM YEU_CAU
        WHERE TABLE_NAME IN('KHAM_CTIET') AND BPHAN_THIEN_ID <> 1509 AND HDON_ID =$no GROUP BY TABLE_NAME
        UNION
        SELECT CASE WHEN TABLE_NAME = 'CDHA_NS' THEN 'CDHA' ELSE CASE WHEN TABLE_NAME='XNGHIEM' THEN 'XET NGHIEM' ELSE
        CASE WHEN TABLE_NAME='DVU_KHAC' THEN 'DICH VU KHAC' ELSE 'CHI PHI GIUONG DT' END END END AS TABLE_NAME, 
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
        GROUP BY TABLE_NAME");*/

        /*$arrayQuery = oci_parse($conn, "SELECT TABLE_NAME,SUM(BNHAN_TRA) AS THANH_TIEN FROM
            (SELECT CASE WHEN BPHAN_YCAU_ID IN (1403,1404,1806,1063) THEN 'PTTM' ELSE
            CASE WHEN BPHAN_YCAU_ID IN (1509,1510) THEN 'SCC' ELSE 'KHOA_KCB' END END AS TABLE_NAME
            , BNHAN_TRA from YEU_CAU WHERE HDON_ID IN (SELECT HDON_ID FROM HDON WHERE SO_HDON='$no') 
            AND TABLE_NAME NOT IN ('THUOC_XUAT','VTU_XUAT','HCHAT_XUAT'))
            GROUP BY TABLE_NAME");*/

        $arrayQuery = oci_parse($conn, "SELECT TABLE_NAME,SUM(BNHAN_TRA) AS THANH_TIEN,SUM(BHIEM_TRA) AS BH_TRA  FROM
            (SELECT CASE WHEN BPHAN_THIEN_ID IN (1403,1404,969,1063,1086,1673,1322) THEN 'PTTM' ELSE
            CASE WHEN BPHAN_THIEN_ID IN (1509,1510) THEN 'SCC' ELSE 'KHOA_KCB' END END AS TABLE_NAME
            , BNHAN_TRA, BHIEM_TRA from YEU_CAU WHERE HDON_ID IN (SELECT HDON_ID FROM HDON WHERE SO_HDON='$no') 
            AND TABLE_NAME NOT IN ('THUOC_XUAT','VTU_XUAT','HCHAT_XUAT'))
            GROUP BY TABLE_NAME");
      
        oci_execute($arrayQuery);
        $output = array();
        $total = 0;
        while ($row = oci_fetch_array($arrayQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $input = new stdClass();
            $input->TABLE_NAME = $row['TABLE_NAME'];
            $input->VALUE = $row['THANH_TIEN'];
            $output[] = $input;
            $total = $total + $row['THANH_TIEN'] + $row['BH_TRA'];
        }
        if(count($output) > 0){
            //echo json_encode($output);exit;
        }else{
            $noRecord = new stdClass();
            $noRecord->TABLE_NAME = null;
            $noRecord->VALUE = null;
            $output[] = $noRecord;
            //echo json_encode($output);exit;
        }
        $result = new stdClass();
        $result->dtdvkt = $total;
        echo json_encode($result);exit;
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
  
