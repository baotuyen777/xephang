<?php

error_reporting(E_ALL & ~E_NOTICE);
$client = new SOAPClient("http://125.212.207.127:9006/axis2/services/CardService?wsdl");


//$result = $client->__soapCall("cardRefCodeService", array("stt"=>"2815","tg"=>"0037"));

var_dump($client);exit;

//header('Content-Type: application/xml; charset=utf-8');
//echo $client->__getLastResponse();
