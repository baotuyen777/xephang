<?php 
include 'header.php';  

$server = new soap_server();

$server->configureWSDL("tc","urn:tc");

$server->register("cronjob",
    array("start_time" => "xsd:string","end_time"=>"xsd:string"),
    array("return" => "xsd:string"),
    "urn:tc",
    "urn:tc#cronjob",
    "rpc",
    "encoded",
    "cronjob everyOneHour");


if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA); 

function cronjob($start_time,$end_time)
{
	$arrayOutput = array();
	$conn = openConnectOracle();
    $phieuKhamQuery = oci_parse($conn, "SELECT SO_PHIEU_TG,SO_PHIEU_STT FROM PHIEU_KHAM WHERE NGAY_KTHUC >=  to_date('$start_time', 'YYYY-MM-DD HH24:MI:SS') AND NGAY_KTHUC < to_date('$end_time', 'YYYY-MM-DD HH24:MI:SS') AND TRANG_THAI = 'K'");
    oci_execute($phieuKhamQuery);
    while ($row = oci_fetch_array($phieuKhamQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $arrayPhieuKham[] = array(
            'SO_PHIEU_TG'=>$row['SO_PHIEU_TG'],
            'SO_PHIEU_STT'=>$row['SO_PHIEU_STT'],
        );
    }
    /*$client = new SOAPClient("http://172.20.99.100/access.php?wsdl", array('trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY));
    foreach($arrayPhieuKham as $phieuKham){
    	$arrayOutput[] = $client->__soapCall("access", array("stt"=>$phieuKham['SO_PHIEU_STT'],"tg"=>$phieuKham['SO_PHIEU_TG']));
	}
    */
	closeConnectOracle($conn);
    return json_encode($arrayPhieuKham);
}

function openConnectOracle(){

    $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
        
    return $conn;
}

function closeConnectOracle($conn){
    oci_close($conn);
}