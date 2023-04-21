<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Config\Oauth;
use App\Models\Usermodel;
use DateTimeZone;
use DateTime;
use Config\PasswordHash;

class Api extends ResourceController {

	function __construct() {
        date_default_timezone_set('GMT');
		$this->request = \Config\Services::request();
		$this->session = \Config\Services::session();
		$this->db = \Config\Database::connect();
        $this->oauth = new \Config\Oauth();
        $this->updateunitprogress = $this->oauth->catsurl('update_unit_progress');
		$this->usermodel = new Usermodel();	
		$this->phpass = new PasswordHash();	
	}

	public  function login(){

		$dataPost 	= json_decode($this->request->getPost('data'));
    	$result 			= array();
    	$result['code'] 	= '0';
    	/* Authenticate by username and password */
    	if (isset($dataPost->userid) != ""){
    		$user = $this->usermodel->get_app_user($dataPost->userid,"email");
    		if (str_contains($dataPost->userid, '@')) {
    		    $password = $dataPost->password;
    		} else {
    		    $password = strtoupper($dataPost->password);
    		}	
    		if( ! $user OR ! $this->phpass->CheckPassword($password, $user->password ) ){
    			$result['code'] 	= '1000';
    			$result['message'] 	= 'Login or Password Incorrect';
    		}
    	}
		/* Authenticate by social token id */
    	elseif (isset($dataPost->logintoken) != ""){
    		$user = $this->usermodel->get_app_user($dataPost->logintoken,$dataPost->logintokentype);
    		$dataPost->password = 'P@ssw0rd' . 'CMEDTWP';
    		if( ! $user ){
    			$result['code'] 	= '1011';
    			$result['message'] 	= 'User is not registered on CATs ';
    		}
    	}
		/* Authenticate by Appserver token Id */
    	elseif (isset($dataPost->token) != ""){
    		$user = $this->usermodel->get_app_user($dataPost->token,"token");
    		if( !$user ){
    			$result['code'] 	= '1001';
    			$result['message'] 	= 'Invalid Token';
    		}
    	}
		/* if Authentication fails */
		if( $result['code'] != "0" )
		{
			$response 	= array('result' => $result);
			return $this->response->setJSON($response);		
		}
		else
    	{
	    	$role_details = current($this->usermodel->chk_role($user->id));
	    	$current_user_role_id  = $role_details['roles_id'];
    	
	    	if (isset($current_user_role_id) != ""){

				/* Only the tier5 learners are allowed to login */
				if( $current_user_role_id == 3 ||  $current_user_role_id == 7  ){
					$login 				= array();
					$login['userid'] 	= $user->user_app_id;
					$login['language'] 	= $user->language_id;
					$login['locale'] 	= '0';
					$login['name'] 	= json_encode($user->firstname.' '.$user->lastname);
						
					$purchase_result = $this->usermodel->get_user_purchashed_course_api($user->id);
					$purchase = $purchased_course = array();
					
					if(!empty($purchase_result)){
						$purchase[0]['id'] = $purchase_result[0]->alp_id;
						$dateTime = new DateTime($purchase_result[0]->purchased_date);
						$timestamp = $dateTime->format('U');
						$purchase[0]['expiry'] = "".strtotime('+5 years', $timestamp)."";
					}

					$finalresponse 	= array('login' => $login,'purchase' => $purchase);
					return $this->response->setJSON($finalresponse);	 		
				}	
				else{
	    			$result['code'] 	= '1011';
	    			$result['message'] 	= 'User is not registered on CATs';
	    			$response 			= array('result' => $result);
					return $this->response->setJSON($response);	
	    		}
			}
		}
	}

	/*
	 * API call updates for demographic data stored on the web portal for a given user
	*/
    public function setdemogdata(){
        $dataPost   = $this->request->getPost('data');
		$resdata 		= json_decode($dataPost);
		$demog 			= isset($resdata->demog)?($resdata->demog):'';
		$languageid 	= isset($demog[0]->languageid)?($demog[0]->languageid):'';
		$result			= array();
        if(isset($resdata->leanerid)){
            $data["language_id"] = $languageid;
            $builder = $this->db->table('users');
            $builder->where('user_app_id',$resdata->leanerid);
            $update_result = $builder->update($data); 
			if($update_result){
				log_message('error', "Language Updated From Webclient or Mobile - " .print_r($dataPost,true));
                $builder = $this->db->table('user_products');
				$builder->join('products', 'products.id = user_products.product_id');
				$builder->where('user_products.thirdparty_id LIKE "'.$resdata->leanerid.'%" ');
				$builder->orderBy("user_products.id", "DESC");
				$builder->limit(1);
				$query = $builder->get();
				if ($query->getNumRows() > 0) {
					$result = $query->getRowArray();
					$dwh_data["language"] = $languageid;
					$dwh_data["userid"] = $resdata->leanerid;
					$dwh_data["courseid"] = $result['alp_id'];	
					$response = $this->http_ws_call_update_unit_progress_language(json_encode($dwh_data));
				}
				return $this->response->setJSON([true]);
			}else{
				return $this->response->setJSON([false]);
			}	
        }
        else{
			return $this->response->setJSON([false]);
        }
    }
	/* API call renewtoken check function */
	public function renewtoken(){
		$dataPost 	= json_decode($this->request->getPost('data'));
		$renew		= $dataPost->renew;
		$token		= $dataPost->token;
		if($renew == 1){
			$renewtoken = $this->usermodel->renew_token($token);
			if($renewtoken){
				$finalresponse 	= array('renewtoken' => $renewtoken);
				return $this->response->setJSON($finalresponse);
			}
		}
		elseif($renew == 0){
			$disabletoken = $this->usermodel->disable_token($token);
			if($disabletoken){
				$result				= array();
				$result['code'] 	= '0';
				$result['message'] 	= 'Success';
				$finalresponse 	= array('result' => $result);
				return $this->response->setJSON($finalresponse);
			}
		}
	}
    /* API call for expiry token check function */
	public function getexpiry(){

		$data 	= json_decode($this->request->getPost('data'));
		$expiry = $this->usermodel->get_expiry($data);
		$dateTime 				= new DateTime($expiry);
		$timestamp				= $dateTime->format('U');
		$expiry 				= "".strtotime('+5 years', $timestamp)."";
		if($expiry){
			$finalresponse 	= array('expiry' => $expiry);
			return $this->response->setJSON($finalresponse);
		}
		
	}
    /* API call for purchase course function */
	public function purchase(){

		$dataPost 	= json_decode($this->request->getPost('data'));
		$builder = $this->db->table('users');
		$builder->select('id, firstname, lastname');
		$builder->where('user_app_id = "'.trim($dataPost->userid).'"');
		$query = $builder->get();
		$user_details = $query->getResult();
        $username = array();
		foreach($user_details as $detail):
		$userid = $detail->id;
		$username['name'] = json_encode($detail->firstname.' '.$detail->lastname);
		endforeach;
		$purchase_result = $this->usermodel->get_user_purchashed_course_api($userid);
    	$purchase = array();
    		
    	if(!empty($purchase_result)){
    		$purchase[0]['id'] = $purchase_result[0]->alp_id;
    		$dateTime = new DateTime($purchase_result[0]->purchased_date);
    		$timestamp = $dateTime->format('U');
    		$purchase[0]['expiry'] = "".strtotime('+5 years', $timestamp)."";
    	}
		
		$finalresponse = array('purchase' => $purchase,'name'=> $username);
		return $this->response->setJSON($finalresponse);
		
	}
	/* API call for push progress function */
	public function push_progresswp(){		
		if(isset($_POST)){
	
			$dataPost = json_decode($this->request->getPost('data'));
			$token = $progress = $userid = $courseid = '';
			$timestamp = date("U");
			$dataPostcheck = (array)$dataPost;
			if(array_key_exists("token", $dataPostcheck)  && array_key_exists("progress", $dataPostcheck)  && array_key_exists("userid", $dataPostcheck)  && array_key_exists("courseid", $dataPostcheck)){
				$token = $dataPost->token;
				$progress = $dataPost->progress;
				$userid = $dataPost->userid;
				$courseid = $dataPost->courseid;

				if($this->oauth->catsurl('dwh_ws_token') === $token){
					$builder = $this->db->table('user_products');
					$builder->select('user_products.id');
					$builder->join('products', 'products.id = user_products.product_id');
					$builder->where('products.alp_id', $courseid);
					$builder->where('user_products.thirdparty_id LIKE "'.$userid.'%" ');
					$builder->orderBy("user_products.id", "DESC");
					$builder->limit(1);
					$query = $builder->get();
					if ($query->getNumRows() > 0) {
						$result = $query->getResultArray();
						$result = current($result);
						$data["course_progress"] = $progress;
						if($result['id'] != '' && $result['id'] != '0'){
							$builder = $this->db->table('user_products');
							$builder->where('id', $result['id']);
							$update_result = $builder->update($data);
						}
						if($update_result){
							$finalresponse = array('result' => array('code' => '0', 'message' => 'Success'), 'ts'=> $timestamp);
							return $this->response->setJSON($finalresponse);
						} 
					}else{
						$finalresponse = array('result' => array('code' => '1007', 'message' => 'Invalid value '), 'ts'=> $timestamp);
						return $this->response->setJSON($finalresponse);
					}
				}else{
					$finalresponse = array('result' => array('code' => '1001', 'message' => 'Invalid Token'), 'ts'=> $timestamp);
					return $this->response->setJSON($finalresponse);
				}
				
			}else{
				$finalresponse = array('result' => array('code' => '1003', 'message' => 'Missing Parameter'), 'ts'=> $timestamp);
				return $this->response->setJSON($finalresponse);
			}
		}
	}


	/* Webclient changes for Login using CURL --START  */
	public function sso_login() {
		if(isset($_POST)){
			$logintokentype = 'WEBCLIENT';
			$url = $this->oauth->catsurl('webclient_url').'api/ssologin.php';
			$params = array('data' => json_encode(array(
				'logintoken' => base64_encode($_SESSION['user_app_id']),
				'logintokentype' => $logintokentype,
				'ssowckey' => $this->oauth->catsurl('dwh_ws_token'),
			)) );
		}
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		$output = curl_exec($ch);
		curl_close($ch);
		
		echo $output;
		die();
	}
	/* Webclient changes for Logout using CURL --START  */
	public function sso_logout() {               
		if(isset($_POST)){
			$url = $this->oauth->catsurl('webclient_url').'api/ssologout.php';
			$params = array('data' => json_encode(array(
				'logintoken' => base64_encode($_SESSION['user_app_id']),
			)) );
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		$output = curl_exec($ch);
		curl_close($ch);
		
		echo $output;
		die();
	}/* Webclient changes --END */

     /* Curl function to get update unit progress by language */
    function http_ws_call_update_unit_progress_language($data = FALSE) {
		if ($data != FALSE) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->updateunitprogress);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . $data . '');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			return $output;
		}
	}

	/* App-9 Api for fetch leaners details */
	public function leaner_detail(){ 
        $user_app_id = $_GET['user_app_id'];
		$builder = $this->db->table('users as u');
		$builder->select('u.firstname, u.lastname, u.username, u.email, it.organization_name, so.order_type');
		$builder->join('tokens as t', 't.user_id = u.id', 'left');
		$builder->join('school_orders as so', 'so.id = t.school_order_id', 'left');
		$builder->join('institution_tier_users as itu', 'itu.user_id = so.school_user_id', 'left');
		$builder->join('institution_tiers as it', 'it.id = itu.institutionTierId', 'left');
		$builder->where('u.user_app_id', $user_app_id);
		$builder->groupBy('u.user_app_id');
		$results = $builder->get()->getRowArray();
		return $this->response->setJSON($results, 200);
	}

}