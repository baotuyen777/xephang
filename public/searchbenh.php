<?php 
include 'header.php';  

$server = new soap_server();

$server->configureWSDL("tc","urn:tc");

$server->register("searchbenh",
    array("stt" => "xsd:string","tg"=>"xsd:string"),
    array("return" => "xsd:string"),
    "urn:tc",
    "urn:tc#searchbenh",
    "rpc",
    "encoded",
    "search by bnhan id");


if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA); 

function searchbenh($stt,$tg)
{
    $arrayOutput = array();
	$conn = openConnectOracle();
    $benhQuery = oci_parse($conn, "SELECT BENH_CHINH,BENH_KTHEO FROM PHIEU_KHAM WHERE SO_PHIEU_TG = '$tg' AND SO_PHIEU_STT = '$stt'");
    oci_execute($benhQuery);
    $row = oci_fetch_row($benhQuery);
    if($row != false){
        $arrayOutput['BENH_CHINH'] = getTenBenh($conn,$row[0]);
        $arrayOutput['BENH_KTHEO'] = getTenBenh($conn,$row[1]);
    }

	closeConnectOracle($conn);
    return json_encode($arrayOutput);
}

function getTenBenh($conn,$ma_benh){
    $query = oci_parse($conn, "SELECT TEN_BENH FROM MA_BENH WHERE MA_BENH = '$ma_benh'");
    oci_execute($query);
    $row = oci_fetch_row($query);
    if($row != false){
        return $row[0];    
    }
}

function openConnectOracle(){

    $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
        
    return $conn;
}

function closeConnectOracle($conn){
    oci_close($conn);
}