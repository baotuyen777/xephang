<?php 
include 'header.php';  

$server = new soap_server();

$server->configureWSDL("tc","urn:tc");

$server->register("info",
    array("stt" => "xsd:string","tg"=>"xsd:string"),
    array("return" => "xsd:string"),
    "urn:tc",
    "urn:tc#info",
    "rpc",
    "encoded",
    "Info Interface");


if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA); 

function info($stt,$tg)
{
	$stt = htmlspecialchars($stt);
    $tg = htmlspecialchars($tg);

    $arrayOutInfo = array();

    $conn = openConnectOracle($tg,$stt);
    $phieuKhamQuery = oci_parse($conn, "SELECT PHIEU_KHAM_ID,BENH_NHAN_ID,BPHAN_KHAM_ID,SO_DTHOAI FROM PHIEU_KHAM WHERE SO_PHIEU_TG = '$tg' AND SO_PHIEU_STT = '$stt'");
    oci_execute($phieuKhamQuery);
    $row = oci_fetch_row($phieuKhamQuery);
    if($row != false){
        $PHIEU_KHAM_ID = $row[0];
        $arrayOutInfo = getInfo($conn,$row[1],$row[2],$row[3]);
    }

    closeConnectOracle($conn);
    return json_encode($arrayOutInfo);
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

function openConnectOracle($tg,$stt){

    if($tg >= 45 && $stt >= 1624){
        $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
    }else{

        $conn = oci_connect(DB_USER, DB_PASS, DB, DB_CHAR);
    }
        
    return $conn;
}

function closeConnectOracle($conn){
    oci_close($conn);
}