<?php
session_start();

ini_set('memory_limit', '512M');
ini_set("max_execution_time", "60000");

require 'stratusApi.class.php';

header('Content-Type: application/json;charset=utf-8');
header('Connection: Keep-Alive','Transfer-Encoding: chunked');
//header('Access-Control-Allow-Origin: *');
header('Vary: Accept-Encoding');

function get_http_response_code($url)
{
  $headers = get_headers($url);
  return substr($headers[0], 9, 3);
}

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 

$callName = null;
$error = "";

//The error message to be shown to the end user when an error happens during signup
$error_msg = "Something went wrong with your registration, the admins of this site have been informed and will fix it shortly";

//if app instance is in deploy_failure state, we can fix it, so we render this message to the end user
$warning_msg="It looks like we are experiencing a high volume of deployment requests. Your instance has been placed in our queue and you will receive an email when it is deployed";

$myFile = "log.log";
$fh = fopen($myFile, 'a');

$logData = "===========================================\n[".date("Y-m-d h:i:sa")."] Preparing params\n";
fwrite($fh, $logData);

if (isset($_REQUEST['call'])) {
  $callName = $_REQUEST['call'];
  $_params = array();
  // replace "-" in POST paramater names with "."
  if (isset($_POST)) {
    foreach ($_POST as $_key => $_val) {
       $_params[str_replace('-','.', $_key)] = $_val;
    }
  }
  $params = $_params;
}


/*
$logData = "";
foreach ($params as $key => $value) {
	$logData = $logData."params[".$key."]=>".$value."\n";
}
fwrite($fh, $logData);
*/

$logData = "[".date("Y-m-d h:i:sa")."] Preparing to call api->call(signup)\n";
fwrite($fh, $logData);

if ($callName && $callName == 'signup' && $_POST['token'] == "u7y3cohysyiqDT3y9t1hVp32szhIsXlXdW7HTFh1")
{  
  $clientUsername = null;
  $clientPassword = null;
  $requireCreditCard = $params['requireCreditCard'];
  
  $customerDomain = $params['domain'] ;
  if (strpos($customerDomain, 'stratus5.net') !== false)
    $customerDomain = $params['organization']."-".$params['domain'];
  else
    $customerDomain = $params['organization'].".".$params['domain'];
  $custom = 'themes:'.$params['themeUrls'].';plugins:'.$params['pluginUrls'];
  $custom = str_replace(' ', '', $custom);
  $callParams = array(
    'firstName' => $params['firstname'],
    'lastName' => $params['lastname'],
    'username' => $params['email'],
    'email' => $params['email'],
    'password' => $params['password'],
    'orgName' => $params['organization'],
    'sldAndSubdomain' => $params['organization'],
  	'domain' => $customerDomain,
    'adminlogin' => $params['adminlogin'],
    'tld.id' => $params['tldid'],
    'planCode' => $params['planCode'],
    'custom' => $custom,
  	'baseUrl' => $params['baseUrl'],
    'baseUsername' => $_SESSION['baseUsername'],
    'basePassword' => $_SESSION['basePassword'],
    'bgimage' => $params['bgimage']
  );

  $logData = "[".date("Y-m-d h:i:sa")."] requireCreditCard is ".$requireCreditCard."\n".$params."\n";
  fwrite($fh, $logData);
  
  if ($requireCreditCard=="y") {
  	$callParams['nameOnCard'] = $params['nameoncard'];
  	$callParams['cardNumber'] = $params['cardnumber'];
  	$callParams['expMonth'] = $params['expmonth'];
  	$callParams['expYear'] = $params['expyear'];
    $callParams['cvn'] = $params['cvn'];
  }  
  
  $signupOrg=" [".$params['organization']."] ";
  
  $printParams=$callParams;
  $cardNumber = $printParams['cardNumber'];
  if ($cardNumber != null && $cardNumber.length>5) {
  	$printParams['cardNumber']="********".substr($cardNumber, - 4);
  }
  $logData = "[".date("Y-m-d h:i:sa")."] Call of api->call(signup) with params:\n".json_encode($printParams)."\n";
  fwrite($fh, $signupOrg.$logData);

  $api = new StratusApi('object', $callParams, $clientUsername, $clientPassword);
  $result = $api->call('signup', $callParams);
  $clientUsername = $params['email'];
  $clientPassword = $params['password'];
  
  $logData = "[".date("Y-m-d h:i:sa")."] Api->call(signup) RETURNED.\nStarting polling to check if instance is ready.\n";
  fwrite($fh, $signupOrg.$logData);

  if ($result && is_array($result) && isset($result['error'])) {  
		$res = print_r($result, true);
		fwrite($fh, $signupOrg."Result from api->call is an error: ".$res."\n");
		fwrite($fh, $signupOrg."***WARNING: S5-core is not going to create any instance, as this failed early at the signup.\n");
  	if($error == "") {
			if ($result['error'] == 1 && isset($result['errorDetails']) && is_array($result['errorDetails'])) { // we had an error and error details
				$logData = "pass 1.\n";
				fwrite($fh, $signupOrg.$logData);
				
				if (isset($result['errorDetails']['validation']) 
							&& isset($result['errorDetails']['validation']->errors)
							//&& is_array($result['errorDetails']['validation']['errors'])
  				) {
					$logData = "pass 2.\n";
					fwrite($fh, $signupOrg.$logData);

					if (isset ($result['errorDetails']['validation']->message) ) {
						$logData = "pass message.\n";
						fwrite($fh, $signupOrg.$logData);
						$error = $result['errorDetails']['validation']->message;
						
					} else {
						$validation_error=$result['errorDetails']['validation']->errors[0];
						if (isset($validation_error) 
	  						&& isset($validation_error->field) 
	  						&& isset($validation_error->rejectedValue)
	  						&& isset($validation_error->code)
	  					) {
							$logData = "pass 3.\n";
							fwrite($fh, $signupOrg.$logData);
	
							if (
									$validation_error->field=='cardNumber'   
	  							|| $validation_error->field=='email' 
	  							|| $validation_error->field=='expYear' 
	  							|| $validation_error->field=='expMonth' 
	  							|| $validation_error->field=='cvn' 
	  					) {
								$error_details = $validation_error->field;
							} else {
								$error_details = $validation_error->rejectedValue." is ".$validation_error->code." for field ".$validation_error->field;
							}
							$error = $error_details;
						}
					} 					
				}			
			}  else {
						$logData = "no pass.\n";
						fwrite($fh, $signupOrg.$logData);
				$error = $error_msg;
			}		
  		
  	}
      //$error = "Signup Error: ".$result['error'];
      // We will not expose the error to the end user, rather log it only
      //$error = $error_msg;
  		unset($api);        
  } else if ($result && is_object($result) && isset($result->id)) { // signup success, poll for app instance status    
    unset($api);
    $error =  "SUCCESS";
    fwrite($fh, $signupOrg."Customer Registration sucess\n");
  }
  else
  { // unknown error
    $result = json_encode(
      array('error' => true,
        'errorDetails' => array(
          'code' => 0,
          'descr' => 'Unknown error',
          'validation' => null
        )
      ));
    if($error == "")
      //$error = "Unknown Error: ".json_decode($result, true);
			$error = $err_msg;
			fwrite($fh, $signupOrg."Unknown Error: ".json_decode($result, true)."\n");
    unset($api);
  }
}

$logData = "[".date("Y-m-d h:i:sa")."] Polling ($counter times) to check if Instance is ready JUST FINISHED.\n";
fwrite($fh, $signupOrg.$logData);

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);

$logData = "[".date("Y-m-d h:i:sa")."] CONCLUSION: ".$error." (".round($totaltime, 2)."secs)\n";
fwrite($fh, $signupOrg.$logData);

fclose($fh);

echo $error;
?>
