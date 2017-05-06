<?php
require_once('BaseController.php');
require_once(APP_PATH . 'library/CacheLibrary.php');        

class AdminController extends BaseController
{
    public function init()
    {
        parent::init();
        //if($this->getUser()->uid != 1){
            //header("HTTP/1.0 404 Not Found");
            //die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p><p>The requested URL was not found on this server.</p><p>Please go <a href="javascript: history.back(1)">back</a> and try again.</p><hr /><p>Powered by: <a href="'.APP_URL.'">'.APP_URL.'</a></p></body></html>');
        //}        
            
    }
    public function indexAction(){
        $DB_USER = 'homistc';
        $DB_PASS = 'thucuc2011';

        if($tcTG >= 45 || $tcSTT >= 1624){
            $DB_USER = 'homistc2016';
            $DB_PASS = 'thucuc2016';
        }

        $conn = $this->openConnectOracle($DB_USER,$DB_PASS);
        $a = array("'A00'","'A01'","'A02'","'A03'","'A04'","'A05'","'A06'","'A07'","'A08'","'A09'");
        $b = array("'B00'","'B01'","'B02'","'B03'","'B04'","'B05'","'B06'","'B07'","'B08'","'B09'");

        for($i = 10;$i < 100;$i++){
            $a[] = "'A".$i."'";
            $b[] = "'B".$i."'";
        }

        $a = implode(',', $a);
        $b = implode(',', $b);

        $result = array();

        $param = $a.','.$b;

        $sql_ma_benh = "SELECT MA_BENH,TEN_BENH FROM MA_BENH";

        $maBenhQuery = oci_parse($conn, $sql_ma_benh);
        oci_execute($maBenhQuery);

        while ($row = oci_fetch_array($maBenhQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {

            $maBenh = $row['MA_BENH'];
            $tenBenh = $row['TEN_BENH'];

            $sql_count_m = "SELECT PHIEU_KHAM_ID FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH = '$maBenh' AND BENH_NHAN.GIOI_TINH = 'M' AND BENH_NHAN.NGAY_SINH < '01/01/2000'";

            $sql_count_sex = "SELECT PHIEU_KHAM_ID FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH = '$maBenh' AND BENH_NHAN.GIOI_TINH = 'F' AND BENH_NHAN.NGAY_SINH < '01/01/2000'";

            $sql_count_kids = "SELECT PHIEU_KHAM_ID FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH = '$maBenh' AND BENH_NHAN.NGAY_SINH >= '01/01/2000'";

            $sql_count_kids5 = "SELECT PHIEU_KHAM_ID FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH = '$maBenh' AND BENH_NHAN.NGAY_SINH >= '01/01/2010'";

            $sql_count_total = "SELECT count(*) as count FROM BENH_AN WHERE NGAY_RA >= '01/10/2014' AND NGAY_RA <= '30/09/2015' AND BENH_CHINH = '$maBenh'";

            $total_female = 0;
            $total_kids = 0;
            $total_kids5 = 0;
            $total_male = 0;
            $female = array();
            $kids = array();
            $kids5 = array();
            $male = array();

            $countMQuery = oci_parse($conn, $sql_count_m);
            oci_execute($countMQuery);

            while ($r = oci_fetch_array($countMQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $total_male++;
                $male[] = "'".$r['PHIEU_KHAM_ID']."'";
            }

            $male = implode(',', $male);

            $countSexQuery = oci_parse($conn, $sql_count_sex);
            oci_execute($countSexQuery);

            while ($r = oci_fetch_array($countSexQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $total_female++;
                $female[] = "'".$r['PHIEU_KHAM_ID']."'";
            }

            $female = implode(',', $female);
            
            $countKidsQuery = oci_parse($conn, $sql_count_kids);
            oci_execute($countKidsQuery);

            while ($r = oci_fetch_array($countKidsQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $total_kids++;
                $kids[] = "'".$r['PHIEU_KHAM_ID']."'";
            }
           
            $kids = implode(',', $kids);

            //var_dump($kids);

            $countKidsQuery5 = oci_parse($conn, $sql_count_kids5);
            oci_execute($countKidsQuery5);

            while ($r = oci_fetch_array($countKidsQuery5, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $total_kids5++;
                $kids5[] = "'".$r['PHIEU_KHAM_ID']."'";
            }
            

            $kids5 = implode(',', $kids5);

            $sql_count_sex_dt = "SELECT count(*) as count FROM KHOA_DT WHERE PHIEU_KHAM_ID IN ($sql_count_sex)";

            $sql_count_kids_dt = "SELECT count(*) as count FROM KHOA_DT WHERE PHIEU_KHAM_ID IN ($kids)";

            $sql_count_m_dt = "SELECT count(*) as count FROM KHOA_DT WHERE PHIEU_KHAM_ID IN ($male)";

            $sql_count_kids5_dt = "SELECT count(*) as count FROM KHOA_DT WHERE PHIEU_KHAM_ID IN ($kids5)";

            //echo $sql_count_kids_dt;

            if($male != ''){
                $array_count_m_dt = oci_parse($conn,$sql_count_m_dt);
                oci_execute($array_count_m_dt);
                $r = oci_fetch_row($array_count_m_dt);
                if($r != false){
                    $total_male_dt = $r[0];
                }
            }else{
                $total_male_dt = 0;
            }

            if($total_female != 0){

                $array_count_sex_dt = oci_parse($conn,$sql_count_sex_dt);
                oci_execute($array_count_sex_dt);
                $r = oci_fetch_row($array_count_sex_dt);

                if($r != false){

                    $total_female_dt = $r[0];
                }
            }else{
                $total_female_dt = 0;
            }

            if($kids != ''){

                $array_count_kids_dt = oci_parse($conn,$sql_count_kids_dt);
                oci_execute($array_count_kids_dt);
                $r = oci_fetch_row($array_count_kids_dt);
                if($r != false){
                    $total_kids_dt = $r[0];
                }
            }else{
                $total_kids_dt = 0;
            }


            if($kids5 != ''){
                $array_count_kids5_dt = oci_parse($conn,$sql_count_kids5_dt);
                oci_execute($array_count_kids5_dt);
                $r = oci_fetch_row($array_count_kids5_dt);
                if($r != false){
                    $total_kids5_dt = $r[0];
                }
            }else{
                $total_kids5_dt = 0;
            }
            $array_count_total = oci_parse($conn,$sql_count_total);
            oci_execute($array_count_total);
            $r = oci_fetch_row($array_count_total);
            if($r != false){
                $total = $r[0];
            }
            
            $result[] = array(
                'TEN_BENH'=>$row['TEN_BENH'],
                'MA_BENH'=>$row['MA_BENH'],
                'total_female'=>$total_female,
                'total_kids'=>$total_kids,
                'total'=>$total,
                'total_dt'=>$total_male_dt + $total_female_dt,
                'total_female_dt'=>$total_female_dt,
                'total_kids_dt'=>$total_kids_dt,
                'total_kids5_dt'=>$total_kids5_dt,
            );

        }

        //var_dump($result);

        
        /*
        $sql_count_sex = "SELECT count(*) as count FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH IN ($param) AND BENH_NHAN.GIOI_TINH = 'F'";

        $sql_count_kids = "SELECT count(*) as count FROM BENH_AN INNER JOIN BENH_NHAN ON BENH_AN.BENH_NHAN_ID = BENH_NHAN.BENH_NHAN_ID WHERE BENH_AN.NGAY_RA >= '01/10/2014' AND BENH_AN.NGAY_RA <= '30/09/2015' AND BENH_AN.BENH_CHINH IN ($param) AND BENH_NHAN.NGAY_SINH >= '01/01/2000'";

        $sql_count_total = "SELECT count(*) as count FROM BENH_AN WHERE NGAY_RA >= '01/10/2014' AND NGAY_RA <= '30/09/2015' AND BENH_CHINH IN ($param)";

        echo $sql_count_sex;

        echo "<br>";

        echo $sql_count_kids;

        echo "<br>";

        echo $sql_count_total;
        */
        $this->view->result = $result;
        $this->closeConnectOracle($conn);

        /*$yeuCauQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID,TEN_PHIEU,TABLE_NAME,TABLE_ID,NGAY_YCAU,BSY_YCAU_ID FROM YEU_CAU WHERE PHIEU_KHAM_ID = '$PHIEU_KHAM_ID'");
        oci_execute($yeuCauQuery);

        while ($row = oci_fetch_array($yeuCauQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $arrayYeuCau[] = array(
                'PHIEU_KHAM_ID'=>$row['PHIEU_KHAM_ID'],
                'TEN_PHIEU'=>$row['TEN_PHIEU'],
                'TABLE_NAME'=>$row['TABLE_NAME'],
                'TABLE_ID'=>$row['TABLE_ID'],
                'NGAY_YCAU'=>$row['NGAY_YCAU'],
                'BSY_YCAU_ID'=>$row['BSY_YCAU_ID'],
            );
        }*/
    }
}