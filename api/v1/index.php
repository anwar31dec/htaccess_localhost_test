<?php
echo 'Nazim';
exit;
function isValidJSON($str)
{
	json_decode($str);
	return json_last_error() == JSON_ERROR_NONE;
}
 
function _getRawBody()
{
    $body = file_get_contents('php://input'); 
    if (strlen(trim($body)) == 0 && isset($GLOBALS['HTTP_RAW_POST_DATA'])) { 
        $body = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], True);  
        return $body;
    }
    
    if (strlen(trim($body)) > 0 && isValidJSON($body)) {
       
        return json_decode($body, True);
		
    } else {
		$body=$_REQUEST;
		$body['file']=$_FILES; 
    }    
    
    $case = isset($body['dataFor']) ? $body['dataFor'] : '';
	
    switch ($case) {
        case "datatable":
            require('DataFormat.php');
            $body = DataFormat::DataFormatedForDatatable($body);
            break; 
        default:
            require('DataFormat.php');
            $body = DataFormat::FormPostData($body);           
            break;
    }  
    return $body;    
}

$params = _getRawBody();

//require_once('../../pdo_lib.php');
require('../../wp-content/themes/flatsome-child/pdo_lib.php');

$api_name = $_GET['method'];
//var_dump($_GET);
//exit;
$method = str_replace('-', '_', $api_name);

	
$jsonp = preg_match('/^[$A-Z_][0-9A-Z_$]*$/i', $_GET['callback']) ?	$_GET['callback'] :	false;

$callback = $_GET['callback'];

require_once('switcher.php');

if ( $jsonp && !empty($retNData) && $retNData != NULL ) { 
	echo $jsonp.'('. $retNData.');';
}else if ( !$jsonp && !empty($retNData) && $retNData != NULL ) {
	echo $retNData;
}
