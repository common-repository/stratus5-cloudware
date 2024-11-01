<?php
class StratusApi {
    
    //CUT AND PASTE THE LINE BELOW TO TEST FROM THE COMMAND LINE
    //curl -v -k -u pat.bolger@stratus5.com:Letmein123 -H "Content-Type: application/json" -d '{"firstName":"Pat","lastName":"Bolger","username":"pat3332@workclouds.com","password":"q1w2e3","orgName":"loadte2323st","sld":"loadtest","tld.id":"115","signUpPageId":"5","planCode":"suhHjAa0Wz"}' https://themefusion.stratus5.net/api/v1/signUp

    private $executionMode = 'console';
    private $resultsMode = 'ajax';
    //private $baseUrl = 'https://themefusion.stratus5.net/';
    private $baseUrl = '';
    private $username = '';
    private $password = '';
    private $domain = '';
		private $fh;
    
    public function __construct($resultsMode = 'ajax', $params = null, $clientUsername = null, $clientPassword = null) {
    	$this->baseUrl = "https://".$params['baseUrl']."/";
    	$this->username = $params['baseUsername']; // default username
    	$this->password = $params['basePassword'];; // default password
    	$this->domain = $params['domain']; // default password
      $customerDomain = $params['domain'] ;
      $this->domain = $customerDomain;
			$this->fh = fopen("log.log", 'a');
      
      if(isset($_SERVER['SERVER_PORT'])){
            $this->executionMode = 'browser';
        }
        $this->resultsMode = $resultsMode;

        if ($clientUsername && $clientPassword) {
            $this->username = $clientUsername;
            $this->password = $clientPassword;
        }

      }	 
    
    /**
     * Login post url getter.
     *
     * @return string - login post url
     */
    public function getLoginPostUrl() {
        return $this->baseUrl;
    }
    
    public function call($method, $params = null) {
        try {
            $curl = curl_init();
            $this->setHttpAuth($curl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8', 'Connection: Keep-Alive','Transfer-Encoding: chunked', 'Access-Control-Allow-Origin: *', 'Vary: Accept-Encoding')); 
            $methodName = $method.'Call';
    	    
            $result = $this->$methodName($curl, $params);
            //echo $result;
            
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            curl_close($curl);
            
            $errorCode = null;
            $errorDescr = '';
            $validationErrors = null;
            
            if ($httpStatus == 500) {
                $errorDescr = 'Error in stratus5 service - Possible bug';
            } elseif ($httpStatus == 403) {
                $errorDescr = 'Invalid signup page for credentials';
            } elseif ($httpStatus == 422) {
                $errorDescr = 'Validation failure';
                $validationErrors = $result;
            } elseif ($httpStatus == 401) {
                $errorDescr = 'Not Authorized, credentials not provided or not found in system';
            } elseif ($httpStatus == 404) {
                $errorDescr = 'Customer Account Not Found';
            } elseif ($httpStatus == 400 ) {
                $errorDescr = 'Validation failures';
                $validationErrors = $result;
            }
            if ($errorDescr) {
                $errorCode = $httpStatus;
            }
            
            if (!$errorCode) {
                if ($this->resultsMode == 'ajax') {
                    //echo $result;
                }
                return json_decode($result);
            }
            
            $error = array(
                'error' => true,
                'errorDetails' => array(
                    'code' => $errorCode,
                    'descr' => $errorDescr,
                    'validation' => null
                )
            );
            if ($validationErrors) {
                $error['errorDetails']['validation'] = json_decode($validationErrors);
            }
            if ($this->resultsMode == 'ajax') {
                //echo json_encode($error);
            }
            return $error;
            
        } catch (Exception $e) {
					fwrite($this->fh, "Catch error in api.call :".$e->getMessage()."\n");
        	echo $e->getMessage();
        }
    }
    
    private function setHttpAuth(& $curlInstance) {
        curl_setopt($curlInstance, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curlInstance, CURLOPT_USERPWD, $this->username.':'.$this->password);
        //print_r($curlInstance); 
    }
    
    /**
     * Call: Get list of sign up pages
     *
     * @param unknown_type $curl
     * @return unknown
     */
    private function getSignupPagesCall($curl) {
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl.'signupApi/v2/signup');
        return curl_exec($curl);
    }
    
    /**
     * Call: Get list of top level domains (TLD)
     *
     * @param unknown_type $curl
     * @return unknown
     */
    private function getTopLevelDomainsCall($curl) {
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl.'api/v1/signUp/tlds');
        return curl_exec($curl);
    }
    
    /**
     * Call: Get list of countries
     *
     * @param unknown_type $curl
     * @return unknown
     */
    private function getCountriesCall($curl) {
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl.'api/v1/signUp/countries');
        return curl_exec($curl);
    }
    
    /**
     * Call: Service sign up
     *
     * @param unknown_type $curl
     * @return unknown
     */
    private function signupCall($curl, $formData) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8', 'Connection: Keep-Alive','Transfer-Encoding: chunked', 'Access-Control-Allow-Origin: *', 'Vary: Accept-Encoding')); 
        //curl_setopt($curl, CURLOPT_HTTPHEADER, 'Transfer-Encoding:  chunked');
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl.'signupApi/v2/signup');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($formData));
    		//echo "Curl->signupCall: ".var_dump($curl)."\n";
				//echo "Curl-GetInfo: ".curl_getinfo($curl);
				$logData = " Calling ".$this->baseUrl."signupApi/v2/signup\n";
				fwrite($this->fh, $logData);
        return curl_exec($curl);
    }
    
    /**
     * Call: Check app instace is state
     *
     * @param unknown_type $curl
     * @return { status: "QUEUED_FOR_DEPLOYMENT|DEPLOYING|DEPLOYED|DEPLOY_FAILURE" }
     */
    private function instanceReadyCall($curl) {
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl.'/signupApi/v2/appStatus?domain='.$this->domain);
    	//echo "Curl->InstanceReady: ".var_dump($curl)."\n";
	//echo "Curl-GetInfo: ".curl_getinfo($curl);
        return curl_exec($curl);
    }
    
    private function recursive_array_search($needle,$haystack) { 
    foreach($haystack as $key=>$value) { 
        $current_key=$key; 
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value))) { 
            return $current_key; 
        } 
    } 
    return false; 
    } 
}



