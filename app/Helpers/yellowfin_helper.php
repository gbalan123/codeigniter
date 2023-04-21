<?php 
use App\Models\Usermodel;


//WP-1380 Start
function yellowfin($userId){

    $db = \Config\Database::connect();
    $usermodel = new Usermodel();
    $oauth = new \Config\Oauth();

    if($userId != ""){
         apicall();
         $builder = $db->table('users');
         $builder->select('*');
         $builder->where('id', $userId);
         $query = $builder->get();
         $user_results = $query->getRow();
         if($query->getNumRows() > 0){
            $userEmail =  $user_results->email;
            $role_details = current($usermodel->chk_role($userId));
            $webserviceAdmin = 'cpt@adminyf.com';
            $webserviceAdminPassword = $oauth->catsurl('yellowfin_admin_token');
            $base_url = $GLOBALS['base_url'];
            $userToLogin['userId'] = $userEmail;    	        	    
            $AdministrationServiceRequest['function'] = 'GETUSER';
            $AdministrationServiceRequest['person'] = $userToLogin;
            $AdministrationServiceRequest['loginId'] = $webserviceAdmin; 
            $AdministrationServiceRequest['password'] = $webserviceAdminPassword; 
            $AdministrationServiceRequest['orgId'] = 1;
            $AdministrationServiceRequest['ntlm']=false;
            $response_get = doWebserviceCall($AdministrationServiceRequest);
            $data_get_status = $response_get->statusCode;
            $response_get_json = json_encode($response_get);
            $status_get = $data_get_status == 'SUCCESS' ? 1 : 0 ;
            $yellowfin_log = array(
                'user_id' =>$userId,
                'email' => $userEmail,
                'tier_type' => $role_details['name'],
                'purpose' => 'GETUSER',
                'response' => $data_get_status,
                'response_message' => $response_get_json,
                'status' => $status_get
            );

            $builder = $db->table('yellowfin_remove_logs');
			$builder->insert($yellowfin_log); 

            if($data_get_status == 'SUCCESS'){
                $base_url = $GLOBALS['base_url'];   
                $userToLogin['userId'] = $userEmail;	    
                        
                $AdministrationServiceRequest['function'] = 'DELETEUSER';
                $AdministrationServiceRequest['person'] = $userToLogin;
                $AdministrationServiceRequest['loginId'] = $webserviceAdmin; 
                $AdministrationServiceRequest['password'] = $webserviceAdminPassword; 
                $AdministrationServiceRequest['orgId'] = 1;
                $AdministrationServiceRequest['ntlm']=false;
                $response_delete = doWebserviceCall($AdministrationServiceRequest);
                $data_delete_status = $response_delete->statusCode;
                $response_delete_json = json_encode($response_delete);
                $status_delete = $data_delete_status == 'SUCCESS' ? 1 : 0 ;
                $yellowfin_log2 = array(
                    'user_id' =>$userId,
                    'email' => $userEmail,
                    'tier_type' => $role_details['name'],
                    'purpose' => 'DELETEUSER',
                    'response' => $data_delete_status,
                    'response_message' => $response_delete_json,
                    'status' => $status_delete
                );
            
                $builder = $db->table('yellowfin_remove_logs');
			    $builder->insert($yellowfin_log2); 
            }else{
                log_message('error', "No Yellowfin user - " .print_r("user_email- ".$userEmail." user_id- ".$userId,true));
            }  
        }else{
            log_message('error', "No Webportal users - " .print_r("user_id- ".$userId,true));
        }    
    }else{
        log_message('error', "Yellowfin remove user - " .print_r("user_email- ".$userEmail." user_id- ".$userId,true));
    }
    
}

function doWebserviceCall($rsr){
    try {
        $rs =  $GLOBALS['client']->remoteAdministrationCall($rsr);
    }
    catch (Exception $e)
    {
        echo "Error! <br>";
        echo $e -> getMessage();
        echo 'Last response: '.  $GLOBALS['client']->__getLastResponse();
        return null;
    }
    return $rs;
}
function apicall (){
    $oauth_api = new \Config\Oauth();
    ini_set('soap.wsdl_cache_enabled', 0);
    ini_set('soap.wsdl_cache_ttl', 900);
    ini_set('default_socket_timeout', 15);
     
    $wsdl_url = $oauth_api->catsurl('yellowfin_baseurl').'services/AdministrationService?wsdl';
    $base_url = $oauth_api->catsurl('yellowfin_baseurl');
   
     
     $client = new SoapClient($wsdl_url);
     $GLOBALS['client'] = $client;
     $GLOBALS['base_url'] = $base_url;
 }
 // WP-1380 end

 ?>