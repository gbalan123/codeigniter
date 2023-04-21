<?php 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once (APPPATH.'Libraries/firebase/php-jwt/src/JWT.php');
require_once (APPPATH.'Libraries/firebase/php-jwt/src/Key.php');





    function db_connect_zendesk(){
        $db = \Config\Database::connect();
        return $db;
    }

    	//WP-1391 api curl function
	function curlWrap($url, $data, $action){
		$oauth = new \Config\Oauth();
		$encrypter = \Config\Services::encrypter();
		$str = $encrypter->decrypt(base64_decode($oauth->catsurl('zendesk_curl_api')));
		$encode_str = base64_encode($str);
		$headers = array(
			'Content-type: application/json',
			"Authorization: Basic $encode_str",
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		// Http curl calls based on documentation
		//GET /api/v2/users/{user_id}/identities
		//PUT /api/v2/users/{user_id}/identities/{user_identity_id}

		if($action === "POST"){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}elseif($action === "PUT"){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}elseif($action === "DELETE"){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}

    	//WP-1391 api create zendesk user function
	function zendesk_user_create($domain_url,$user_id,$action,$purpose_type){
		//create zendesk user by wp userId as externalid
		$user_details = get_user_by_id($user_id);
		if($user_details){
			if($action == "Update"){
				$url = $domain_url."search.json?query=external_id:".$user_details['user']['external_id'];
				$response_external_id_data = curlWrap($url,false,"Search");
				if(isset($response_external_id_data) && ($response_external_id_data->count > 0)){
					$json_array = $user_details;
					unset($json_array['user']['email']);	
					unset($json_array['user']['verified']);	
				}else{
					$json_array = $user_details;
				}	
			}else{
				$json_array = $user_details;
			}
			$json = json_encode($json_array);
			$zend_user_details = get_zendesk_user_list($user_id);
			$zendesk_create_response = curlWrap($domain_url."create_or_update", $json, "POST");
			if(isset($zendesk_create_response) && isset($zendesk_create_response->user)){
				//create zendesk_logs
				zendesk_data_logs($zendesk_create_response,$action,$purpose_type,$user_details);
				zendesk_data_list($zendesk_create_response,$action,$purpose_type);	
				return $zendesk_create_response;
			}else{
				$reason = "User Create Error"; 
				@zendesk_log_file($user_details['user']['email'], $user_id, $zendesk_create_response,$reason);
				return $zendesk_create_response;
			}	
		}else{
			$reason = "Zendesk User ADD not in list"; 
			@zendesk_log_file(false, $user_id, false,$reason);
		}
	}


    	//WP-1391 api update zendesk user function
	function zendesk_user_update($domain_url,$user_id,$action,$purpose_type){
		$wp_user_data = get_user_by_id($user_id);
        $db = db_connect_zendesk();
		// to update name api call
		$update_response = zendesk_user_create($domain_url,$user_id,$action,$purpose_type);
		if(isset($update_response->user)){
			$zendesk_user = $update_response->user;
		}
		if(isset($zendesk_user) && $wp_user_data['user']['email'] != $zendesk_user->email){
			//To get identity to update primary email call
			$url = $domain_url.$zendesk_user->id."/identities";
			$response_search_data = curlWrap($url,false,"Search");
			if(isset($response_search_data)&& ($response_search_data->count > 0) && isset($response_search_data->identities)){
				//search log
				zendesk_update_logs($response_search_data,$purpose_type,$wp_user_data,"Search");
				$zendesk_user = current($response_search_data->identities);
				//update primary email by identity
				$update_url = $url."/".$zendesk_user->id;
				$update_json['identity'] = array('value' => $wp_user_data['user']['email']);
				$update_json = json_encode($update_json);
				$zendesk_update_response = curlWrap($update_url, $update_json, "PUT");
				//mail update log
				zendesk_update_logs($zendesk_update_response,$purpose_type,$wp_user_data,"Email_Update");
				$zendesk_identity = $zendesk_update_response->identity;
				$update_identity_id = array(
					'zendesk_identity_id' => $zendesk_user->id,
					'email' => $zendesk_identity->value,
				);
                $builder = $db->table('zendesk_users_list');
                $builder->where('user_id', $user_id);
                $builder->update($update_identity_id);
			}else{
				$reason = "Zendesk User identities not Found"; 
				@zendesk_log_file(false, $user_id, $response_search_data,$reason);
			}
		}else{
			$reason = "Zendesk User No email change update"; 
			@zendesk_log_file($wp_user_data['user']['email'], $user_id, $update_response,$reason);
		}
    }

    function zendesk_profile_update($user_id,$purpose_type){
        $db = db_connect_zendesk();
        $oauth = new \Config\Oauth();
        $domain_url = $oauth->catsurl('zendesk_domain_url');
        $zendesk_user = get_zendesk_user_list($user_id);
        if($zendesk_user){
            zendesk_user_update($domain_url,$user_id,"Update",$purpose_type);
        }else{
            zendesk_user_create($domain_url,$user_id,"Create",$purpose_type);
        } 
    }


    function zendesk_user_is_active($user_id = FALSE){
        if($user_id != FALSE){
            $db = db_connect_zendesk();
            $query = $db->query('SELECT * FROM institution_tier_users WHERE user_id ='.intval($user_id));
            $tier_admin_status = $query->getResult(); 
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

	function zendesk_teacher_is_active($user_id = FALSE){
        if($user_id != FALSE){
            $db = db_connect_zendesk();
            $query = $db->query('SELECT * FROM institution_teachers WHERE teacherId ='.intval($user_id));
            $tier_admin_status = $query->getResult(); 
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

    function get_zendesk_user_list($user_id){
        $db = db_connect_zendesk();
        $builder = $db->table('zendesk_users_list');
        $builder->select('zendesk_id,status');
        $builder->where('user_id', $user_id);
        $builder->orderBy('id', 'desc');
        $query = $builder->get();
        $user_results = $query->getRowArray();
        if($query->getNumRows() > 0){
            return $user_results;
        }else{
            return false;
        }
    }

    	////WP-1391 fetch webportal user function
	function get_user_by_id($user_id){
		$db = db_connect_zendesk();
        $builder = $db->table('users');
		$builder->select('name,id,email');
		$builder->where('id', $user_id);
		$query = $builder->get();
		$user_results = $query->getRowArray();
		
		if($query->getNumRows() > 0){
			$zendesk_user_data['user'] = array(
				'name' => $user_results['name'],
				'email' => $user_results['email'],
				'external_id' => $user_results['id'],
				'role' => 'end-user',
				'verified'=> true 
        	);
			return $zendesk_user_data;
		}else{
			return false;
		}
	}


    	//WP-1391 Logs function create,delete one log, 1(name) or 3(email)log for update
	function zendesk_data_logs($response,$action,$purpose_type,$user_details){
		$zendesk_user = $response->user;
		$db = db_connect_zendesk();
		$zendesk_log = array(
			'zendesk_id' => $zendesk_user->id,
			'user_id' => $user_details['user']['external_id'],
			'email' => $user_details['user']['email'],
			'name' => $zendesk_user->name,
			'purpose' => $action,
			'purpose_type' => $purpose_type,
			'response' => json_encode($response),
			'response_message' => "Success",
			'status' => 1
		);
        $builder = $db->table('zendesk_logs');
        $builder->insert($zendesk_log);
	}

    	//WP-1391 zendesk user maintaining function
	function zendesk_data_list($response,$action){
		$zendesk_user = $response->user;
		$db = db_connect_zendesk();
		$user_exist = get_zendesk_user_list($zendesk_user->external_id);
		if($action === "Create" || $action === "Update"){
			$zendesk_user_list = array(
				'zendesk_id' => $zendesk_user->id,
				'user_id' => $zendesk_user->external_id,
				'name' => $zendesk_user->name,
				'email' => $zendesk_user->email,
				'status' => 1
			);
			if($zendesk_user->external_id > 0){
				if($user_exist){
                    $builder = $db->table('zendesk_users_list');
                    $builder->where('user_id', $zendesk_user->external_id);
                    $builder->update($zendesk_user_list);
				}else{
                    $builder = $db->table('zendesk_users_list');
                    $builder->insert($zendesk_user_list);
				}
			}
		}elseif($action === "Delete"){
			if($zendesk_user->id > 0){
                $builder = $db->table('zendesk_users_list');
                $builder->where('zendesk_id', $zendesk_user->id);
                $builder->delete();
			}
		}	
	}

    function zendesk_update_logs($response,$purpose_type,$wp_user_data,$action){
		$zendesk_user = ($action == "Search") ? current($response->identities) : $response->identity;
		$db = db_connect_zendesk();
		$zendesk_log = array(
			'zendesk_id' => $zendesk_user->id,
			'user_id' => $wp_user_data['user']['external_id'],
			'email' => $zendesk_user->value,
			'name' => NULL,
			'purpose' => $action,
			'purpose_type' => $purpose_type,
			'response' => json_encode($response),
			'response_message' => "Success",
			'status' => 1
		);
        $builder = $db->table('zendesk_logs');
        $builder->insert($zendesk_log);
	}


    function zendesk_log_file($email, $user_id, $response,$reason){
        $efsfile = new \Config\Efsfilepath();
        $efsfilepath = $efsfile->get_Efs_path();
		$efs_custom_log_path = $efsfilepath->efs_custom_log;
		define("LOG_FILE_ZENDESK", $efs_custom_log_path . "zendesk_log.txt");
		error_log(date('[Y-m-d H:i e]') ."user_id- ".$user_id. " email- ". $email. " reason- ".$reason. " response- ".  print_r($response,true).PHP_EOL, 3, LOG_FILE_ZENDESK);
	}

    	//WP-1391 api delete zendesk user function
	function zendesk_user_delete($domain_url,$user_id,$action,$purpose_type){
		$user_details = get_zendesk_user_list($user_id);
		$wp_user_data = get_user_by_id($user_id);
		if($user_details){
			$delete_url = $domain_url.$user_details['zendesk_id'];
			$zendesk_delete_response = curlWrap($delete_url, false, "DELETE");
			if(isset($zendesk_delete_response) && isset($zendesk_delete_response->error)){
				$reason = "User Delete Error"; 
				@zendesk_log_file($wp_user_data['user']['email'], $user_id, $zendesk_delete_response,$reason);
			}else{
				//create zendesk_logs
				zendesk_data_logs($zendesk_delete_response,$action,$purpose_type,$wp_user_data);
				zendesk_data_list($zendesk_delete_response,$action);
			}			
		}else{
			$reason = "Zendesk User Inactive not in list"; 
			@zendesk_log_file($wp_user_data['user']['email'], $user_id, false,$reason);
		}
	}


	//WP-1392 Fetch sso url via zendesk jwt token
	function get_zend_desk_url($user_id){
		$oauth = new \Config\Oauth();
		$encrypter = \Config\Services::encrypter();
		$user_details = get_user_by_id($user_id);
		if($user_details){
			$email = $user_details['user']['email'];
			$name = $user_details['user']['name'];
			$external_id = $user_details['user']['external_id'];
			
			$payload = array(
				'name' => $name,
				'email' => $email,
				'external_id' => $external_id,
				'iat' => time(),
				'jti' => md5($now . rand())
			);

			// $subdomain = 'alssl';
        	$domain_url = $oauth->catsurl('zendesk_domain_url');
			$subdomain = str_replace(".zendesk.com/api/v2/users/","",$domain_url);

			$key = $encrypter->decrypt(base64_decode($oauth->catsurl('zendesk_sso')));
			$jwt = JWT::encode($payload, $key, 'HS256');
			$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
			$location = $subdomain . ".zendesk.com/access/jwt?jwt=" . $jwt;
			return $location;
		}
	}


		//WP-1393 zendesk web widget
	function get_web_widget_token($user_id){
		$oauth = new \Config\Oauth();
		$encrypter = \Config\Services::encrypter();
		$user_details = get_user_by_id($user_id);
		if($user_details){
			$name = $user_details['user']['name'];
			$email = $user_details['user']['email'];
			$payload = array(
				'name' => $name,
				'email' => $email,
				'iat' => time(),
				'jti' => time()
			);
			$key = $encrypter->decrypt(base64_decode($oauth->catsurl('zendesk_widget')));
			$token = JWT::encode($payload, $key, 'HS256');
			return $token;
		}
	}
    
