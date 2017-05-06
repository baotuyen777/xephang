<?php 
ini_set('mssql.charset', 'UTF-8');
include 'header.php';  

$server = new soap_server();

$server->configureWSDL("tc","urn:tc");

$server->register("access",
    array("stt" => "xsd:string","tg"=>"xsd:string"),
    array("return" => "xsd:string"),
    "urn:tc",
    "urn:tc#access",
    "rpc",
    "encoded",
    "Access Interface");


if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA); 

function access($stt,$tg)
{
    $stt = htmlspecialchars($stt);
    $tg = htmlspecialchars($tg);

    $arrayCDHANS = array();
    $arrayXNGHIEM = array();
    $arrayKHAMCHITIET = array();

    $arrayOutCDHANS = array();
    $arrayOutXETNGHIEM = array();
    $arrayOutInfo = array();
    $arrayOutCTIET = array();
    $arrayOutThuoc = array();

    $arrayOutPut = array(
        "reason"=>"",
        "past"=>"",
        "symptom"=>"",
        "clinical"=>array(
            "general"=>"",
            "organs"=>"",
        ),
        "diagnosis"=>"",
        "laboratory"=>"",
        "imaging"=>"",
        "icd"=>"",
        "diseases"=>"",
        "treatment"=>"",
        "note"=>"",
    );
    $benh_su = '';
    $tien_su = '';


    $conn = openConnectOracle($tg,$stt);
    $phieuKhamQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID,BENH_NHAN_ID,BPHAN_KHAM_ID,SO_DTHOAI,TIEN_SU,BENH_SU,CDOAN_RVIEN,TEN_BKTHEO FROM PHIEU_KHAM WHERE SO_PHIEU_TG = '$tg' AND SO_PHIEU_STT = '$stt'");
    oci_execute($phieuKhamQuery);
    $row = oci_fetch_row($phieuKhamQuery);
    if($row != false){
        $PHIEU_KHAM_ID = $row[0];
        $arrayOutInfo = getInfo($conn,$row[1],$row[2],$row[3]);
        $benh_su = $row[5];
        $tien_su = $row[4];
        $arrayOutPut['past'] = $tien_su;
        $arrayOutPut['symptom'] = $benh_su;
        $arrayOutPut['icd'] = $row[6];
        $arrayOutPut['diseases'] = $row[7];
    }

    $thuocXuatQuery = oci_parse($conn, "SELECT THUOC_XUAT_ID FROM THUOC_XUAT WHERE PHIEU_KHAM_ID = '$PHIEU_KHAM_ID'");
    oci_execute($thuocXuatQuery);
    $resultTHUOC = array();
    while ($row = oci_fetch_array($thuocXuatQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $THUOC_XUAT_ID = $row['THUOC_XUAT_ID'];
        $yeuCauChiTiet = oci_parse($conn, "SELECT MA_THUOC,SO_LUONG,CACH_DUNG,TU_TUC FROM THUOC_XUAT_CTIET WHERE THUOC_XUAT_ID = '$THUOC_XUAT_ID '");
            oci_execute($yeuCauChiTiet);
            while ($row = oci_fetch_array($yeuCauChiTiet, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $re = getNameThuoc($conn,$row['MA_THUOC']);
                $resultTHUOC[] = array(
                    'soluong'=>$row['SO_LUONG'],
                    'cachdung'=>$row['CACH_DUNG'],
                    'tutuc'=>$row['TU_TUC'],
                    'tenthuoc'=>$re['tenthuoc'],
                    'manuoc'=>$re['manuoc'],
                );
            }
            
    }
    $arrayOutThuoc = $resultTHUOC;

    //$PHIEU_KHAM_ID = 436773;
    $arrayYeuCau = array();
    $yeuCauQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID,TEN_PHIEU,TABLE_NAME,TABLE_ID,NGAY_YCAU,BSY_YCAU_ID FROM YEU_CAU WHERE PHIEU_KHAM_ID = '$PHIEU_KHAM_ID'");
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
    }

    foreach($arrayYeuCau as $yeucau){
        $table = $yeucau['TABLE_NAME'];
        $tableId = $yeucau['TABLE_NAME'].'_ID';
        $id = $yeucau['TABLE_ID'];
        if($table == 'XNGHIEM'){
            $yeuCauChiTiet = oci_parse($conn, "SELECT XNGHIEM_ID,PHIEU_KHAM_ID,MA_PHIEUXN,BSY_YCAU_ID,CDOAN_SBO,NGAY_YCAU,YEU_CAU,NHAN_XET,KET_LUAN,TABLE_NAME,TABLE_ID,BPHAN_THIEN_ID FROM $table WHERE $tableId = '$id'");
            oci_execute($yeuCauChiTiet);
            $rowYeuCauChiTiet = oci_fetch_row($yeuCauChiTiet);
            if($rowYeuCauChiTiet != false){
                $arrayXNGHIEM[] = array(
                    "XNGHIEM_ID"=>$rowYeuCauChiTiet[0],
                    "PHIEU_KHAM_ID"=>$rowYeuCauChiTiet[1],
                    "MA_PHIEUXN"=>$rowYeuCauChiTiet[2],
                    "BSY_YCAU_ID"=>$rowYeuCauChiTiet[3],
                    "CDOAN_SBO"=>$rowYeuCauChiTiet[4],
                    "NGAY_YCAU"=>$rowYeuCauChiTiet[5],
                    "YEU_CAU"=>$rowYeuCauChiTiet[6],
                    "NHAN_XET"=>$rowYeuCauChiTiet[7],
                    "KET_LUAN"=>$rowYeuCauChiTiet[8],
                    "TABLE_NAME"=>$rowYeuCauChiTiet[9],
                    "TABLE_ID"=>$rowYeuCauChiTiet[10],
                );
                //$retech = getXNGHIEMCT($conn,$rowYeuCauChiTiet[0]);
                $retech = getPID($rowYeuCauChiTiet[0]);
                $arrayOutXETNGHIEM[] = array(
                    'technical'=>$yeucau['TEN_PHIEU'],
                    'detail'=>$retech['detail'],
                    'CDOAN_SBO'=>$rowYeuCauChiTiet[4],
                    'BPHAN_THIEN'=>getNamePK($conn,$rowYeuCauChiTiet[11]),
                    "PHIEU_KHAM_ID"=>$rowYeuCauChiTiet[1],
                );

                //$arrayOutPut["clinical"]["general"] .= $rowYeuCauChiTiet[8];
                $arrayOutPut["laboratory"] .= getName($conn,$rowYeuCauChiTiet[2],null,1).'; ';
            }
        }

        if($table == 'CDHA_NS'){
            $yeuCauChiTiet = oci_parse($conn, "SELECT MA_PHIEUXN,LOAI_PHIEUXN,BSY_YCAU_ID,CDOAN_SBO,MO_TA,KET_LUAN,TABLE_NAME,TABLE_ID,CHI_TIET,BPHAN_THIEN_ID,MA_CDHA FROM $table WHERE $tableId = '$id'");
            oci_execute($yeuCauChiTiet);
            $rowYeuCauChiTiet = oci_fetch_row($yeuCauChiTiet);

            if($rowYeuCauChiTiet != false){
                $arrayCDHANS[] = array(
                    'MA_PHIEUXN'=>$rowYeuCauChiTiet[0],
                    'LOAI_PHIEUXN'=>$rowYeuCauChiTiet[1],
                    'BSY_YCAU_ID'=>$rowYeuCauChiTiet[2],
                    'CDOAN_SBO'=>$rowYeuCauChiTiet[3],
                    'MO_TA'=>$rowYeuCauChiTiet[4],
                    'KET_LUAN'=>$rowYeuCauChiTiet[5],
                    'TABLE_NAME'=>$rowYeuCauChiTiet[6],
                    'TABLE_ID'=>$rowYeuCauChiTiet[7],
                    'CHI_TIET'=>$rowYeuCauChiTiet[8],
                );
                $arrayOutCDHANS[] = array(
                    'request'=>$yeucau['TEN_PHIEU'],
                    'result'=>$rowYeuCauChiTiet[8],
                    'reason'=>$rowYeuCauChiTiet[5],
                    'CDOAN_SBO'=>$rowYeuCauChiTiet[3],
                    'BPHAN_THIEN'=>getNamePK($conn,$rowYeuCauChiTiet[9]),
                    'imaging'=>getName($conn,$rowYeuCauChiTiet[0],$rowYeuCauChiTiet[10],2)
                );

                //$arrayOutPut["clinical"]["organs"] .= $rowYeuCauChiTiet[5];
                $arrayOutPut["imaging"] .= getName($conn,$rowYeuCauChiTiet[0],$rowYeuCauChiTiet[10],2).'; ';
            }
        }




        /*if($table == 'THUOC_XUAT'){
            $yeuCauChiTiet = oci_parse($conn, "SELECT MA_THUOC,SO_LUONG,CACH_DUNG,TU_TUC FROM THUOC_XUAT_CTIET WHERE THUOC_XUAT_ID = '$id'");
            oci_execute($yeuCauChiTiet);
            while ($row = oci_fetch_array($yeuCauChiTiet, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $re = getNameThuoc($conn,$row['MA_THUOC']);
                $result[] = array(
                    'soluong'=>$row['SO_LUONG'],
                    'cachdung'=>$row['CACH_DUNG'],
                    'tutuc'=>$row['TU_TUC'],
                    'tenthuoc'=>$re['tenthuoc'],
                    'manuoc'=>$re['manuoc'],
                );
            }
            $arrayOutThuoc = $result;
        }*/
        if($table == 'KHAM_CTIET'){
            $yeuCauChiTiet = oci_parse($conn, "SELECT KHAM_CTIET_ID,PHIEU_KHAM_ID,NGAY_YCAU,YEU_CAU,LYDO_KHAM,CDOAN_SOBO,HUONG_GQ,BPHAN_THIEN_ID,GHI_CHU,TRIEU_CHUNG FROM $table WHERE $tableId = '$id'");
            oci_execute($yeuCauChiTiet);
            $rowYeuCauChiTiet = oci_fetch_row($yeuCauChiTiet);
            if($rowYeuCauChiTiet != false){
                $arrayKHAMCHITIET[] = array(
                    "KHAM_CTIET_ID"=>$rowYeuCauChiTiet[0],
                    "PHIEU_KHAM_ID"=>$rowYeuCauChiTiet[1],
                    "NGAY_YCAU"=>$rowYeuCauChiTiet[2],
                    "YEU_CAU"=>$rowYeuCauChiTiet[3],
                    "LYDO_KHAM"=>$rowYeuCauChiTiet[4],
                    "CDOAN_SOBO"=>$rowYeuCauChiTiet[5],
                    "HUONG_GQ"=>$rowYeuCauChiTiet[6],
                    "GHI_CHU"=>$rowYeuCauChiTiet[8],
                );

                $arrayOutCTIET['CDOAN_SOBO'] = $rowYeuCauChiTiet[5];
                $arrayOutCTIET['YEU_CAU'] = $yeucau['TEN_PHIEU'];
                $arrayOutCTIET['BPHAN_THIEN'] = getNamePK($conn,$rowYeuCauChiTiet[7]);
                $arrayOutCTIET['NGAY_YCAU'] = $rowYeuCauChiTiet[2];
                $arrayOutCTIET['PHIEU_KHAM_ID'] = $rowYeuCauChiTiet[1];

                $arrayOutPut["reason"] .= $rowYeuCauChiTiet[4];
                $arrayOutPut["diagnosis"] .= $rowYeuCauChiTiet[5];
                $arrayOutPut["treatment"] .= $rowYeuCauChiTiet[6];
                $arrayOutPut["note"] .= '';
                $arrayOutPut["clinical"]["organs"] = $rowYeuCauChiTiet[8];
                $arrayOutPut["clinical"]["general"] = $rowYeuCauChiTiet[9];
            }
        }
    }

    $outPut['INFO'] = $arrayOutInfo;
    $outPut['CDHA'] = $arrayOutCDHANS;
    $outPut['XN'] = $arrayOutXETNGHIEM;
    $outPut['KHAM_CTIET_RESULT'] = $arrayOutPut;
    $outPut['KHAM_CTIET'] = $arrayOutCTIET; 
    $outPut['THUOC'] = $arrayOutThuoc;

    closeConnectOracle($conn);
    return json_encode($outPut);
}
function getNameThuoc($conn,$ma_thuoc){
    $tenXNghiemQuery = oci_parse($conn, "SELECT MA_NUOC,TEN_THUOC FROM MA_THUOC WHERE MA_THUOC = '$ma_thuoc'");
    oci_execute($tenXNghiemQuery);
    $row = oci_fetch_row($tenXNghiemQuery);
    if($row != false){
        return array(
            'tenthuoc'=>$row[1],
            'manuoc'=>$row[0]
        );
    }
    return false;
}
function getNAMEXNCT($conn,$ma,$xn){
    $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_XN FROM GIA_PHIEUXN WHERE MA_PHIEUXN = '$ma' AND MA_XN = '$xn' AND PARENT = '$ma'");
    oci_execute($tenXNghiemQuery);
    $row = oci_fetch_row($tenXNghiemQuery);
    if($row != false){
        return $row[0];
    }
    return false;
}
function getNAMECDHAA($conn,$ma){
    $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_CDHA FROM GIA_PHIEUCDHA WHERE MA_CDHA = '$ma'");
    oci_execute($tenXNghiemQuery);
    $row = oci_fetch_row($tenXNghiemQuery);
    if($row != false){
        return $row[0];
    }
    return false;
}
function getName($conn,$ma,$xn = null,$type){
    if($type == 1){
        if($xn != null){
            $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_XN FROM GIA_PHIEUXN WHERE MA_PHIEUXN = '$ma' AND MA_XN = '$xn'");
        }else{
            $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_XN FROM GIA_PHIEUXN WHERE MA_PHIEUXN = '$ma'");
        }
    }
    
    if($type == 2){
        if($xn != null){
            $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_CDHA FROM GIA_PHIEUCDHA WHERE MA_PHIEUXN = '$ma' AND MA_CDHA = '$xn'"); 
        }else{
            $tenXNghiemQuery = oci_parse($conn, "SELECT TEN_CDHA FROM GIA_PHIEUCDHA WHERE MA_PHIEUXN = '$ma'");
        }
    }  

    oci_execute($tenXNghiemQuery);
    $row = oci_fetch_row($tenXNghiemQuery);
    if($row != false){
        return $row[0];
    }
    return false;
}
function getXNGHIEMCT($conn,$XNGHIEMID){
    $XNGHIEMCTIETQuery = oci_parse($conn, "SELECT MA_XN,MA_PHIEUXN,KET_QUA,GTRI_BTHUONG,DON_VI FROM XNGHIEM_CTIET WHERE XNGHIEM_ID = '$XNGHIEMID'");
    oci_execute($XNGHIEMCTIETQuery);
    $result = array();
    $count = 1;
    while ($row = oci_fetch_array($XNGHIEMCTIETQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
        if($count == 1){
            $result['tech'] = getNAMEXNCT($conn,$row['MA_PHIEUXN'],$row['MA_XN']);
        }
        $result['detail'][$count] = array(
            'ma'=>$row['MA_XN'],
            'xetnghiem'=>getName($conn,$row['MA_PHIEUXN'],$row['MA_XN'],1),
            'ketqua'=>$row['KET_QUA'],
            'trisobinhthuong'=>$row['GTRI_BTHUONG'],
            'donvi'=>$row['DON_VI'],
        );
        $count++;
    }
    return $result;
}

function getPID($xn_id){
    /*$conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
    //$alter_schema = "ALTER SESSION SET CURRENT_SCHEMA = HIS_LIS";
    $select_pid = "select PID from HIS_LIS.TBL_FORM where XNGHIEM_ID = $xn_id";
    oci_execute($select_pid);
    $row = oci_fetch_row($select_pid);
    $pid = array();
    if($row != false){
        $pid = $row[0];
    }*/

   $connection = mssql_connect("172.20.96.220:1433", "thongke", "thongkels" ) or die ("Cannot connect to Server");
        $selected = mssql_select_db('LabConnTC', $connection)or die("Cannot select DB"); 
            
        $sql=mssql_query("select * from tbl_Result where OrderID = $xn_id") or die(mssql_min_error_severity()); 
        $ct = mssql_num_rows($sql);
        $result = array();
        $count = 1;

        while ($row = mssql_fetch_array($sql, MSSQL_NUM)) {
            if($count == 1){
                //$result['tech'] = htmlspecialchars(utf8_encode($row[7]));
                $result['tech'] = (mb_detect_encoding($row[7], mb_detect_order(), true) === 'UTF-8') ? $row[7] : iconv('iso-8859-1', 'utf-8', $row[7]);
            }
            $result['detail'][$count] = array(
                'ma'=>(mb_detect_encoding($row[34], mb_detect_order(), true) === 'UTF-8') ? $row[34] : iconv('iso-8859-1', 'utf-8', $row[34]),
                'xetnghiem'=>(mb_detect_encoding($row[7], mb_detect_order(), true) === 'UTF-8') ? $row[7] : iconv('iso-8859-1', 'utf-8', $row[7]),
                'ketqua'=>(mb_detect_encoding($row[3], mb_detect_order(), true) === 'UTF-8') ? $row[3] : iconv('iso-8859-1', 'utf-8', $row[3]),
                'trisobinhthuong'=>(mb_detect_encoding($row[8], mb_detect_order(), true) === 'UTF-8') ? $row[8] : iconv('iso-8859-1', 'utf-8', $row[8]),
                'donvi'=>(mb_detect_encoding($row[5], mb_detect_order(), true) === 'UTF-8') ? $row[5] : iconv('iso-8859-1', 'utf-8', $row[5]),
            );
            $count++;
        }
    return $result;
}

function getInfo($conn,$BENH_NHAN_ID,$BPHAN_KHAM_ID,$SO_DTHOAI){
    $outInfo = array();
    $infoQuery = oci_parse($conn, "SELECT BENH_NHAN.TEN_BNHAN,BENH_NHAN.NGAY_SINH,BENH_NHAN.GIOI_TINH,BENH_NHAN.DUONG_PHO,BENH_NHAN.SO_NHA,MA_TPHO.TEN_TPHO,MA_HUYEN.TEN_HUYEN FROM BENH_NHAN LEFT JOIN MA_TPHO ON BENH_NHAN.MA_TPHO = MA_TPHO.MA_TPHO LEFT JOIN MA_HUYEN ON BENH_NHAN.MA_HUYEN = MA_HUYEN.MA_HUYEN WHERE BENH_NHAN.BENH_NHAN_ID = '$BENH_NHAN_ID'");
    oci_execute($infoQuery);
    $info = oci_fetch_row($infoQuery);
    if($info != false){
        $address = $info[4].' '.$info[3].' '.$info[6].' '.$info[5];
        $outInfo['TEN_BNHAN'] = $info[0];
        $outInfo['NGAY_SINH'] = $info[1];
        $outInfo['GIOI_TINH'] = $info[2];
        $outInfo['DUONG_PHO'] = $address;
        $outInfo['BENH_NHAN_ID'] = $BENH_NHAN_ID;
        $outInfo['PHONE'] = $SO_DTHOAI;

    }

    $partQuery = oci_parse($conn, "SELECT TEN_BPHAN FROM MA_BOPHAN WHERE MA_BOPHAN_ID = '$BPHAN_KHAM_ID'");
    oci_execute($partQuery);
    $part = oci_fetch_row($partQuery);
    if($part != false){
        $outInfo['TEN_BPHAN'] = $part[0];
    }

    return $outInfo;
}

function getNamePK($conn,$BPHAN_THIEN_ID){
    $partQuery = oci_parse($conn, "SELECT TEN_BPHAN FROM MA_BOPHAN WHERE MA_BOPHAN_ID = '$BPHAN_THIEN_ID'");
    oci_execute($partQuery);
    $part = oci_fetch_row($partQuery);
    if($part != false){
        $outInfo = $part[0];
    }

    return $outInfo;
}

function openConnectOracle($tg,$stt){

    if($tg >= 45 && $stt >= 1624){
        $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
    }else{
        $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
        //$conn = oci_connect(DB_USER, DB_PASS, DB, DB_CHAR);
    }
        
    return $conn;
}

function closeConnectOracle($conn){
    oci_close($conn);
}

