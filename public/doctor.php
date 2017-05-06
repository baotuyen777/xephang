<?php 
include 'header.php';  

$server = new soap_server();

$server->configureWSDL("tc","urn:tc");

$server->register("doctor",
    array(),
    array("return" => "xsd:string"),
    "urn:tc",
    "urn:tc#doctor",
    "rpc",
    "encoded",
    "get doctors of TC hospital");


if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA); 

function doctor()
{
	$conn = openConnectOracle();
    $doctorQuery = oci_parse($conn, "SELECT NHAN_VIEN.NHAN_VIEN_ID as NHAN_VIEN_ID,NHAN_VIEN.TEN_NV as TEN_NV,MA_BOPHAN.TEN_BPHAN as TEN_BPHAN FROM NHAN_VIEN LEFT JOIN MA_BOPHAN ON NHAN_VIEN.MA_BOPHAN_ID = MA_BOPHAN.MA_BOPHAN_ID WHERE NHAN_VIEN.CHUYEN_MON = 'BS'");
    oci_execute($doctorQuery);
    while ($row = oci_fetch_array($doctorQuery, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $arrayOutput[] = array(
            'NHAN_VIEN_ID'=>$row['NHAN_VIEN_ID'],
            'TEN_NV'=>$row['TEN_NV'],
            'TEN_BPHAN'=>$row['TEN_BPHAN'],
        );
    }

	closeConnectOracle($conn);
    return json_encode($arrayOutput);
}

function openConnectOracle(){

    $conn = oci_connect(DB_USER_2016, DB_PASS_2016, DB, DB_CHAR);
        
    return $conn;
}

function closeConnectOracle($conn){
    oci_close($conn);
}