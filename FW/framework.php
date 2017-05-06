<?php   
function show404($msg = '') 
{
    header("HTTP/1.0 404 Not Found");
    die('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p><p>The requested URL was not found on this server.</p><p>Please go <a href="javascript: history.back(1)">back</a> and try again.</p><hr /><p>Powered by: <a href="http://"'.$_SERVER['SERVER_NAME'].'">'.$_SERVER['SERVER_NAME'].'</a></p></body></html>');
}

function showError($msg = '')
{
    header('HTTP/1.1 500 Internal Server Error');
    die('<html><head><title>500 Internal Server Error</title></head><body><h1>Internal Error</h1><p><hr /><p>Powered by: <a href="http://"'.$_SERVER['SERVER_NAME'].'">'.$_SERVER['SERVER_NAME'].'</a></p></body></html>');
}

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{               
    if ( ! preg_match('#^https?://#i', $uri)){
        $uri = APP_URL . $uri;        
    }                                            
    switch($method){
        case 'refresh': 
            header("Refresh:0;url=".$uri);
            break;
        default: 
            header("Location: ".$uri, TRUE, $http_response_code);
            break;
    }
    exit;
}    

require_once(FW_PATH . 'library/View.php');
require_once(FW_PATH . 'library/Controller.php');