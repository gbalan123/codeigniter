<?php

namespace App\Controllers;

use App\Models\Usermodel;
use App\Models\School\Schoolmodel;
use App\Libraries\Acl_auth;
use Config\Oauth;
use SoapClient;

class Report extends BaseController {

	function __construct() {

		$this->schoolmodel = new Schoolmodel();
        $this->usermodel = new Usermodel();
		$this->acl_auth = new Acl_auth();
		$this->request = \Config\Services::request();
		$this->oauth = new \Config\Oauth();
	
		$this->session = \Config\Services::session();
		$this->db = \Config\Database::connect();
	}

		/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()	{
		ini_set('soap.wsdl_cache_enabled',0); 
        ini_set('soap.wsdl_cache_ttl',0);
		if (!$this->acl_auth->logged_in()) {
			return redirect()->to(site_url('/'));//Restrict direct access if user not logged in
		}
	    /*
	     You need to run this on a HTTP server with PHP and Soap enabled,
	     Apache + PHP (Google XAMPP for a quick easy server setup)
	     */
	    
		$this->apicall();
	    
	    $userDisplayName = $this->session->get('user_firstname') . ' ' . $this->session->get('user_lastname');
	    $this->session->set('userDisplayName', $userDisplayName);
	    /*
	     user to login; can be passed as parameters, fetched from cookies etc.
	     */
	    $tierDetails = $this->get_user_tiers();
	    $this->session->set('tierId', isset($tierDetails['id']) ? $tierDetails['id'] : ""); 
	    
	    $this->get_role_based_credentials();
   		
	    if(($this->session->get('userNameToLogin') != null) && ($this->session->get('userPasswordToLogin') != null)){
	        $userNameToLogin = $this->session->get('userNameToLogin');
	        $userPasswordToLogin = $this->session->get('userPasswordToLogin');	        
		}
	   
	    /*
	     Yellowfin user account with rights to perform web services calls:
	     */
	     $webserviceAdmin = 'cpt@adminyf.com';
	     $webserviceAdminPassword = $this->oauth->catsurl('yellowfin_admin_token');
	    
	    $this->session->set('webserviceAdmin', $webserviceAdmin);
	    $this->session->set('webserviceAdminPassword', $webserviceAdminPassword);
	    
		#Add user
		$this->getUser(@$userNameToLogin, @$userPasswordToLogin);
		die;
	}
	
	function apicall (){
		ini_set('soap.wsdl_cache_enabled', 0);
		ini_set('soap.wsdl_cache_ttl', 900);
		ini_set('default_socket_timeout', 15);
		 
		$wsdl_url = $this->oauth->catsurl('yellowfin_baseurl').'services/AdministrationService?wsdl';
		$base_url = $this->oauth->catsurl('yellowfin_baseurl');
	   
		 
		 $client = new SoapClient($wsdl_url);
		 $GLOBALS['client'] = $client;
		 $GLOBALS['base_url'] = $base_url;
	}

	public function get_user_tiers() {
		if($this->session->get('user_email')){
		   $user_id = ($this->session->get('logged_tier1_userid'))? $this->session->get('logged_tier1_userid') : $this->session->get('user_id');
		   $builder = $this->db->table('institution_tier_users'); 
		   $builder->select('institution_tiers.*');
		   $builder->join('users', 'institution_tier_users.user_id = users.id');
		   $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
		   $builder->where('institution_tier_users.user_id', $user_id);
		   $query = $builder->get();
		   return $query->getRowArray();
		}
   }

   function get_role_based_credentials(){
		if(($this->session->get('user_email')) != null){
			$user_id = ($this->session->get('logged_tier1_userid'))? $this->session->get('logged_tier1_userid') : $this->session->get('user_id');
			
			$role_details = current($this->usermodel->chk_role($user_id));
			$current_user_role_id  = $role_details['roles_id'];
			
			$GLOBALS['tier1'] = '';	        
			$GLOBALS['tier2'] = '';	        
			$GLOBALS['tier3'] = '';
			$GLOBALS['tier4'] = '';
			
			if(!empty($current_user_role_id)){
				if($current_user_role_id == 4){
					$userNameToLogin = $this->session->get('user_email');
					$userPasswordToLogin = $this->oauth->catsurl('yellowfin_tier_token');
					$GLOBALS['tier3'] = $this->session->get('tierId');
					$userController = 'school';
					$userRoleCode= 'YFREPORTCONSUMER';
				}elseif($current_user_role_id == 6){
					$userNameToLogin = $this->session->get('user_email');
					$userPasswordToLogin = $this->oauth->catsurl('yellowfin_tier_token');
					$GLOBALS['tier4'] = $this->session->get('user_id');
					$userController = 'teacher';
					$userRoleCode = 'TEACHERADMIN';
				}elseif($current_user_role_id == 9){
					$userNameToLogin = $this->session->get('user_email');
					$userPasswordToLogin = $this->oauth->catsurl('yellowfin_tier_token');
					$GLOBALS['tier2'] = $this->session->get('tierId');
					$userController = 'tier2';
					$userRoleCode = 'TIER2';
				}elseif($current_user_role_id == 8){
					$userNameToLogin = $this->session->get('user_email');
					$userPasswordToLogin = $this->oauth->catsurl('yellowfin_tier_token');
					$GLOBALS['tier1'] = $this->session->get('tierId');
					$userController = 'tier1';
					$userRoleCode = 'MINISTRY';
				}

				$this->session->set('userController', $userController);
				$this->session->set('userNameToLogin', $userNameToLogin);
				$this->session->set('userPasswordToLogin', $userPasswordToLogin);
				$this->session->set('userRoleCode', $userRoleCode);
			}
			
		}
    }

	/*
	 performing GETUSER call to check user exist
	 */
	
	function getUser($userName,$userPassword){
	    if (!$this->acl_auth->logged_in()) {
			return redirect()->to(site_url('/'));//Restrict direct access if user not logged in
		}
		
		$base_url = $GLOBALS['base_url'];
	    
	    $userToLogin['userId'] = $userName;
		$userToLogin['password'] = $userPassword;     	    
	    	    
	    $AdministrationServiceRequest['function'] = 'GETUSER';
	    $AdministrationServiceRequest['person'] = $userToLogin;
	    $AdministrationServiceRequest['loginId'] = $this->session->get('webserviceAdmin'); //$GLOBALS['webserviceAdmin'];
	    $AdministrationServiceRequest['password'] = $this->session->get('webserviceAdminPassword'); //$GLOBALS['webserviceAdminPassword'];
		$AdministrationServiceRequest['orgId'] = 1;
		$AdministrationServiceRequest['ntlm']=false;
		
		$response = $this->doWebserviceCall($AdministrationServiceRequest);
	    if ($response != null and strcmp($response->statusCode,'SUCCESS') == 0){

	        $sessionId = $this->loginUser($userName, $userPassword);	 

			if ($sessionId!=null) {
				setcookie('yellowfinLogin', true, time() + (43200), "/"); // 43200 sec = 12 hour
				
				$url = $base_url.'logon.i4?LoginWebserviceId='.$sessionId.'';
				
				header('Location: '.$url);	// uncomment to redirect
				
			} else echo "<br>Yellowfin Login Failed...<br>";
	    } else {
			$getuserName=$this->session->get('user_email');
			$getPassword=$this->oauth->catsurl('yellowfin_tier_token');
			$getEmail=$this->session->get('user_email');
			$getLastName=$this->session->get('user_lastname');
			$getFirstName=$this->session->get('user_firstname');
			$getRoleCode =$this->session->get('userRoleCode');
		
			//Add new user to yellowfin
			$response = $this->addUser($getuserName,$getPassword,$getEmail,$getLastName,$getFirstName,$getRoleCode);
			if ($response === false){
			echo "<br>Yellowfin login failed...<br>"; die;
			}
			else {
				$sessionId = $this->loginUser($userName, $userPassword);	   
		
				if ($sessionId!=null) {
					setcookie('yellowfinLogin', true, time() + (43200), "/"); // 43200 sec = 12 hour
					
					$url = $base_url.'logon.i4?LoginWebserviceId='.$sessionId.'';
					header('Location: '.$url);	// uncomment to redirect
					
				} else echo "<br>Yellowfin Login Failed...<br>";
			}
		}
	    
	}

		/*
	 sending the request to Yellowfin server
	 */
	
	function doWebserviceCall($rsr){
	    try {
	        $rs =  $GLOBALS['client']->remoteAdministrationCall($rsr);
	    }
	    catch (\Exception $e)
	    {
	        echo "Error! <br>";
	        echo $e -> getMessage();
	        echo 'Last response: '.  $GLOBALS['client']->__getLastResponse();
	        return null;
	    }
	    return $rs;
	}

		/*
	 performing LOGINUSER call to get logon token
	 */
	
	function loginUser($userName,$userPassword){
	    if (!$this->acl_auth->logged_in()) {
			return redirect()->to(site_url('/'));//Restrict direct access if user not logged in
		}
	    
	    $userToLogin['userId'] = $userName;
	    $userToLogin['password'] = $userPassword;   
		
		$GLOBALS['tier1'] = ($GLOBALS['tier1']) ? $GLOBALS['tier1'] : '%';
		$GLOBALS['tier2'] = ($GLOBALS['tier2']) ? $GLOBALS['tier2'] : '%';
		$GLOBALS['tier3'] = ($GLOBALS['tier3']) ? $GLOBALS['tier3'] : '%';
		$GLOBALS['tier4'] = ($GLOBALS['tier4']) ? $GLOBALS['tier4'] : '%';
	    
	    
	    $parameters =  array ("ENTRY=VIEWDASHBOARD","disablefooter=TRUE","disablesidenav=TRUE","disablelogoff=TRUE","SOURCEFILTER_Tier1=" . $GLOBALS['tier1'] . "","SOURCEFILTER_Tier2=" . $GLOBALS['tier2'] . "","SOURCEFILTER_Tier3=".$GLOBALS['tier3']."","SOURCEFILTER_Tier4=".$GLOBALS['tier4']."");
	    $userParam = implode($parameters);
	    
	    $AdministrationServiceRequest['function'] = 'LOGINUSER';
	    $AdministrationServiceRequest['person'] = $userToLogin;
	    $AdministrationServiceRequest['loginId'] = $this->session->get('webserviceAdmin');
	    $AdministrationServiceRequest['password'] = $this->session->get('webserviceAdminPassword');
	    $AdministrationServiceRequest['orgId'] = 1;
	    $AdministrationServiceRequest['ntlm'] = false;
	    $AdministrationServiceRequest['parameters'] = $parameters;
	    $AdministrationServiceRequest['customParameters'] = 'disableheader,' . $this->session->get('userDisplayName') . ',' . $this->session->get('userController');
	    
	    $response = $this->doWebserviceCall($AdministrationServiceRequest);
	    $this->session->set('Yellowfin_loginSessionId', $response->loginSessionId);
		
		/* Change session user_id - Admin view of institution - START*/
		$user_id = ($this->session->get('logged_tier1_userid'))? $this->session->get('logged_tier1_userid') : $this->session->get('user_id');
		$role_details = current($this->usermodel->chk_role($user_id));
		$current_user_role_id  = $role_details['roles_id'];
		if($current_user_role_id == 8 || $current_user_role_id == 9) {
			if($this->session->get('logged_tier1_userid') != null) {
				$tier_userid = $this->session->get('logged_tier1_userid');
				$this->session->set('user_id', $tier_userid);
				$this->session->remove('selected_tierid');
				$this->session->remove('logged_tier1_userid');
			}	
		}
		/* Change session user_id - Admin view of institution - END*/
	   
	    if ($response != null and strcmp($response->statusCode,'SUCCESS') == 0){
	        $this->session->set('Yellowfin_loginSessionId', $response->loginSessionId);
	        return $response->loginSessionId;
	    }
	    
	    return null;
	    
	}

		/*
	 cerating new Yellowfin user account
	 */
	
	function addUser($userName, $userPassword, $userEmail, $userLastName, $userFirstName, $userRoleCode){

	    $user['userId'] = $userName;
	    $user['password'] = $userPassword;
	    $user['emailAddress'] = $userEmail;
	    $user['lastName'] = $userLastName;
	    $user['firstName'] = $userFirstName;
		$user['roleCode'] = $userRoleCode;
     
	    $AdministrationServiceRequest['function'] = 'ADDUSER';
	    $AdministrationServiceRequest['person'] = $user;
	    $AdministrationServiceRequest['loginId'] = $this->session->get('webserviceAdmin');
	    $AdministrationServiceRequest['password'] = $this->session->get('webserviceAdminPassword');
	    $AdministrationServiceRequest['orgId'] = 1;
	    $AdministrationServiceRequest['ntlm'] = false;
	    
		$response = $this->doWebserviceCall($AdministrationServiceRequest);
     
	    if ($response != null and strcmp($response->statusCode,'SUCCESS') == 0) return true;
	    
	    return false;
	    
	}

		/*
	 performing LOGOUTUSER call 
	 */
	
	function logoutUser(){
	    if (!$this->acl_auth->logged_in()) {
	        $cookie_name = 'yellowfinLogin';
	        if(!isset($_COOKIE[$cookie_name])) {
	            return redirect()->to($this->oauth->catsurl('yellowfin_baseurl')."index_mi.jsp");
	        } else {
	            setcookie("yellowfinLogin", "", time() - 3600, "/");
	            return redirect()->to(base_url());
	        }
	    }
	    $this->apicall();
	    
	    $this->get_role_based_credentials();
	    
	    if(($this->session->get('userNameToLogin') != null) && ($this->session->get('userPasswordToLogin') != null)){
	        $userNameToLogin = $this->session->get('userNameToLogin');
	        $userPasswordToLogin = $this->session->get('userPasswordToLogin');
	    }
	    $userToLogin['userId'] = $userNameToLogin;
	    $userToLogin['password'] = $userPasswordToLogin;
	    $AdministrationServiceRequest['loginId'] = $this->session->get('webserviceAdmin'); //$GLOBALS['webserviceAdmin'];
	    $AdministrationServiceRequest['password'] = $this->session->get('webserviceAdminPassword'); //$GLOBALS['webserviceAdminPassword'];
	    $AdministrationServiceRequest['orgId'] = 1;
	    $AdministrationServiceRequest['function'] = 'LOGOUTUSER';
	    $AdministrationServiceRequest['person'] = $userToLogin;
	    $AdministrationServiceRequest['LoginSessionId'] = $this->session->get('Yellowfin_loginSessionId');
	    $AdministrationServiceRequest['ntlm']=false;
		
	    $response = $this->doWebserviceCall($AdministrationServiceRequest);
	    if(NULL != $this->session->get('userController')){
	        setcookie("yellowfinLogin", "", time() - 3600, "/");
	        if ($response!=null and strcmp($response->statusCode,'SUCCESS')==0){
	            $this->session->remove('Yellowfin_loginSessionId');
	            return redirect()->to($this->session->get('userController')."/logout");
	        }else{
	            $this->session->remove('Yellowfin_loginSessionId');
	            return redirect()->to($this->session->get('userController')."/logout");
	        }
	    }
		
	    return null;
	}
	

}