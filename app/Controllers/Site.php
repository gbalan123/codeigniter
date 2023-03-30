<?php

namespace App\Controllers;

use App\Models\Admin\Cmsmodel;
use App\Models\Site\Bookingmodel;
use App\Models\Admin\Emailtemplatemodel;
use App\Models\Usermodel;
use App\Models\Admin\Productmodel;
use App\Models\Admin\Tdsmodel;
use App\Models\School\Eventmodel;
use App\Models\Admin\Placementmodel;
use Config\MY_Lang;
use Config\SiteConfig;
use Config\Oauth;
use App\Config\Efsfilepath;
use App\Controllers\BaseController;
use App\Libraries\Acl_auth;
use App\Libraries\Encryptinc;
use App\Libraries\Mobiledetect;
use App\Libraries\Ciqrcode;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use App\Helpers\efs_path_helper;


class Site extends BaseController
{
    
    function __construct() {
        //acl auth library
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();

        $this->validation =  \Config\Services::validation();
        $this->email = \Config\Services::email();
        $this->request = \Config\Services::request();

        $this->oauth = new \Config\Oauth();
        $this->lang = new \Config\MY_Lang();
        $this->emailtemplatemodel = new Emailtemplatemodel();

        $this->acl_auth = new Acl_auth();
        $this->encryptinc_library = new Encryptinc(); 
        $this->encrypter = \Config\Services::encrypter();
        $this->mobiledetect = new Mobiledetect(); 
        $this->ciqrcode = new Ciqrcode();

        $this->cmsmodel = new Cmsmodel();
        $this->bookingmodel = new Bookingmodel();
        $this->usermodel = new Usermodel();
        $this->placementmodel = new placementmodel();
        $this->productmodel = new Productmodel();
        $this->tdsmodel = new Tdsmodel();
        $this->eventmodel = new Eventmodel();

        helper('core_certificate_language_helper');
        helper('percentage_helper');
        helper('qrcodepath');
        helper('corepdf_helper');
        helper('corepdf_extended_helper');
        helper('url');
        helper('downtime_helper');
        helper('efs_path_helper');
        helper('parts_helper');
        helper('higherpdf_helper');
        helper('zendesk');
        $efsfilepath = new \Config\Efsfilepath();
        $this->efsfilepath = $efsfilepath->get_Efs_path();
        $this->efs_charts_results_path = $this->efsfilepath->efs_charts_results;
        $this->efs_custom_log_path =  $this->efsfilepath->efs_custom_log;
        define("LOG_FILE_TDS", $this->efs_custom_log_path . "tds.txt");
        define("LOG_FILE_TDS_STEPCHECK", $this->efs_custom_log_path . "tds_stepcheck.txt");
        define("LOG_FILE_TDS_REGISTER", $this->efs_custom_log_path . "tds_register.txt");
        define("LOG_FILE_TDS_PLACEMENT", $this->efs_custom_log_path . "tds_placement.txt");
        define("LOG_FILE_TDS_LAUNCHURL", $this->efs_custom_log_path . "tds_launchurl.txt");
        define("LOG_FILE_TDS_STATUS", $this->efs_custom_log_path ."tds_status.txt");
        define("LOG_FILE_LOGOUT", $this->efs_custom_log_path . "tds_logout.txt");
        $this->data['helplinks'] = $this->cmsmodel->helplinks();	

        //set cats primary settings
	    if($this->placementmodel->get_learner_type()){
            //$this->data['learnertype'] = $this->placement_model->get_learner_type();
            $learnerProdType = $this->placementmodel->get_learner_product_type($this->session->get('user_id'));
            $learner_type = array('learnertype' => $this->placementmodel->get_learner_type(),'learnerprodtype' => $learnerProdType);
            $this->session->set($learner_type);
        }

        $this->data['primary_placement_sessions'] = $this->placementmodel->get_primary_placement_sessions();
        
        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            $controller = explode("/",$url['home_page_url']);
            if($controller[0] == "site"){
                // only allow users with 'learner' role to access all methods in this controller
                $this->acl_auth->restrict_access('learner');
            }else{
                $urlchange =  $url;
                $urlrewrite = implode(" ",$urlchange);
                header("Location: " . site_url($urlrewrite), TRUE, 302);
				exit;
            }
        }

        //WP - 1363 -Downtime holding page
        $downtime = @downtime_maintenance_page();
        if($downtime === TRUE){
            echo view('maintenance_page');
            die;
        }

        $this->data['languages'] = $this->cmsmodel->get_language();
        $this->data['organization_data'] = $this->session->get('organization_data');
        $session_datas = $this->data['organization_data'];
        if(!empty($session_datas)){
            $organisation_id = $session_datas['organization_id'];
            $this->data['product_eligiblity'] = $this->placementmodel->get_product_eligiblity($organisation_id);
        }

    }

    function index($slug = NULL) {

        if (!empty($slug)) {
			if($slug == 'terms-conditions'){
                return redirect()->to('pages/terms_conditions'); 
			}
			elseif($slug == 'about-us'){
                return redirect()->to('pages/about_us');
			}else{
                return redirect()->to('pages/'.$slug);
			}			
        }

        $this->data['languages'] = $this->cmsmodel->get_language();
        $this->data['lang_code'] = $this->request->getLocale();
        echo view('site/header', $this->data);
        echo view('site/menus', $this->data);
        echo view('site/index');
        echo view('site/footer',$this->data);
    }

/********** Used For Contact_Us Page currently Not In Use
    public function response_sendMail($email = FALSE, $name = FALSE){
            $to_address = $email;
            $user_name['user_name'] = $name;
            $body =  view('contact_us',$user_name);
            $config = @get_email_config_provider(4);
            //email is service loaded in constructor
            $this->email->initialize($config);
            $this->email->setFrom('noreply@catsstep.education', 'CATS Step Team');
            $this->email->setTo($to_address);
            $this->email->setSubject('CATs Step - Contact us');
            $this->email->setMessage($body);   
            if($this->email->send())
            {
                $mail_log = array(
                    'from_address' => 'noreply@catsstep.education',
                    'to_address' => $to_address,
                    'response' => 'success',
                    'status' => 1,
                    'purpose' => 'CATs Step - Contact us'
                );
                $builder = $this->db->table('email_log');  
                $builder->insert($mail_log);
            }
            else
            {
                $mail_log = array(
                    'from_address' => 'noreply@catsstep.education',
                    'to_address' => $to_address,
                    'response' => $this->email->print_debugger(),
                    'status' => 0,
                    'purpose' => 'CATs Step - Contact us'
                );
                $builder = $this->db->table('email_log');  
                $builder->insert($mail_log);
                echo log_message('error', "Email not sent -".$to_address);
            }
    }
**********/

    public function mail_log($from_mail = FALSE,$to = FALSE,$subject = FALSE)
    {
        $mail_log = array(
                'from_address' => $from_mail,
                'to_address' => $to,
                'response' => 'success',
                'status' => 1,
                'purpose' => $subject
            );
            $this->db->insert('email_log', $mail_log);
    }

    public function email_lib($slug = NULL){
        $parser = \Config\Services::renderer();
		if(!empty($slug)){
			$query = $this->db->query('SELECT E.* FROM email_categories EC JOIN email_templates E ON E.category_id = EC.id WHERE E.language_code = "en" AND EC.category_slug = "'.$slug.'"');
            $results = $query->getRow();	
            $this->data['email_content'] = $results->content;
            $test_data = $parser->setData($this->data)->renderString($this->data['email_content']);
            return $test_data;
		}
	}

    public function login()
    {           
        $request = \Config\Services::request();
        header('Content-Type: application/json');
        if($request->getPost('login_submit'))
        {      
            if (filter_var($this->request->getPost('username'), FILTER_VALIDATE_EMAIL)) {
                $rules = $this->validate([
                    'username' => [
                        'label'  => lang('app.language_admin_username'),
                        'rules'  => 'required|valid_email|max_length[100]',
                    ]
                ]);

            }else{
                $rules = $this->validate([
                    'username' => [
                        'label'  => lang('app.language_site_booking_screen2_label_email_username_address'),
                        'rules'  => 'required|max_length[100]',
                    ],
                    'password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_password'),
                        'rules'  => 'required|max_length[20]',
                    ]
                ]);
                $upassword = strtoupper($this->request->getPost('password'));
            }
            $rules = $this->validate([
                'password' => [
                    'label'  => lang('app.language_site_booking_screen2_label_password'),
                    'rules'  => 'required|max_length[20]',
                ]
            ]);

            if (!$rules) 
                {
                $arrErrorData = array('success'=>0, 'msg' => $this->validation->listErrors()); 
                echo json_encode($arrErrorData);die;
                }
           else
           {
              //account suspended
                $account_data = $this->usermodel->get_user($this->request->getVar('username'));
                 if($this->request->getVar('username') && isset($account_data) && $account_data['is_active'] == '0'){ 
                     $school_admin_user_details = $this->tdsmodel->get_institute_admin_user($account_data['id']);
                  if($school_admin_user_details != NULL){
                   if($school_admin_user_details['status'] == 0){
                        $this->session->setFlashdata('errors', lang('app.language_school_admin_account_disabled'));
                        $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_school_admin_account_disabled'), 'login_type' => 'suspended');
                         echo json_encode($arrSuccessData);die;
                    }else{
                        $this->session->setFlashdata('errors', lang('app.langugage_account_suspended'));
                         $arrSuccessData = array('success'=> 1, 'msg' => lang('app.langugage_account_suspended'), 'login_type' => 'suspended');
                        echo json_encode($arrSuccessData);die;
                    }
                  }else{
                    $this->session->setFlashdata('errors', lang('app.langugage_account_suspended'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.langugage_account_suspended'), 'login_type' => 'suspended');
                   echo json_encode($arrSuccessData);die;

                  }
                 }
               $session_data = array('name', 'email', 'username', 'firstname', 'lastname', 'id', 'user_app_id', 'access_detail_language');
               $username = trim($this->request->getVar('username'));
               $userPassword = (isset($upassword) && !empty($upassword))?$upassword:$this->request->getPost('password');

               $success = $this->acl_auth->login($username,$userPassword,FALSE,$session_data);
               if($success)
               {

                $this->session->set(array('username' => $this->request->getPost('username'), 'password' => base64_encode($this->request->getPost('password').rand(999,9999))));
                $userole = $this->usermodel->chk_role($this->session->get('user_id'));
                $this->usermodel->update_profile(array('last_logged' => strtotime(date('d-m-Y h:i:s'))));
                if($this->request->getPost('zendesk') === "zendesk"){
                    if($userole['0']['name'] == 'learner' || $userole['0']['name'] == 'u13_learner'){
                        $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                        $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'learner');
                        echo json_encode($arrSuccessData);die;
                    }else{
                        $url = @get_zend_desk_url($this->session->get('user_id'));
                        $return_url = $this->request->getPost('return_url');
                        $redirect_url = $return_url != "false" ? $url."&return_to=".$return_url : $url;
                        $arrSuccessData = array('success'=> 1, 'url' => $redirect_url, 'login_type' => 'zendesk');
                        echo json_encode($arrSuccessData);die;
                    }
                }   
                if($userole['0']['name'] == 'school'){
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'school');
                    echo json_encode($arrSuccessData);die;
                } elseif($userole['0']['name'] == 'ministry'){
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'ministry');
                    echo json_encode($arrSuccessData);die;
                }elseif($userole['0']['name'] == 'teacher'){
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'teacher');
                    echo json_encode($arrSuccessData);die;
                }elseif($userole['0']['name'] == 'tier1'){
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'tier1');
                    echo json_encode($arrSuccessData);die;
                }elseif($userole['0']['name'] == 'tier2'){
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'tier2');
                    echo json_encode($arrSuccessData);die;
                }  
                else {
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    $arrSuccessData = array('success'=> 1, 'msg' => lang('app.language_site_booking_screen2_login_success_msg'), 'login_type' => 'learner');
                    echo json_encode($arrSuccessData);die;
                }
               }
               {
                $arrErrorData = array('success'=>0,  'msg' => lang('app.language_site_booking_screen2_login_failure_msg'));
                echo json_encode($arrErrorData);die;
            }

           }
        }
    }

    public function dashboard() {
        
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');   
        }
        $learnerType = $this->placementmodel->get_learner_type();

        if($learnerType == 'under13'){
    
            $orgData = $this->get_organizationid_by_primaryuser($this->session->get('user_id'));
            $tokenType = $this->getTokenType_by_learnerid($this->session->get('user_id'));
            $orgData['type_of_token'] = $tokenType;
            $tokenCode = $this->getTokenCode_by_learnerid($this->session->get('user_id'));

            // WP-1202 Get Token for Under13 (Primary/Core) when login using login popup
            $orgData['token'] = $tokenCode;
            $organizationData = array(
                'code' => $tokenCode, // WP-1202 Set Token in Session for Under13 (Primary/Core) when login using login popup
                'organization_data' => $orgData
            );
            $this->session->set($organizationData);
            $learnerProdType = $this->placementmodel->get_learner_product_type($this->session->get('user_id'));
            if($learnerProdType == 'cats_primary'){
                return redirect()->to('site/get-the-right-primary-level');   
            }
        }

        $this->session->remove('code');
        if($this->session->get('next_cats_pass')){
            $this->session->remove('next_cats_pass');
        }
        if($this->session->get('next_cats_nearly_failed')){
            $this->session->remove('next_cats_nearly_failed');
        }
        if($this->session->get('next_cats_failed')){
            $this->session->remove('next_cats_failed');
        }
    
        $userid = $this->session->get('user_id');

        $user_products = $this->bookingmodel->get_bookings2();
        $newarray = array();
        foreach ($user_products as $userproduct_key => $userproduct_val) {

            if((!empty($userproduct_val['user_thirparty_id'])) && ($userproduct_val['user_thirparty_id'] > 0)) {

                $launch_url = $this->oauth->catsurl('testDeliveryUrl');
                $launch_key = $this->oauth->catsurl('testLaunchKey');
                $referrer = $this->oauth->catsurl('testReferrer');
                $user_thirparty_id = $userproduct_val['user_thirparty_id'];
                $test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'practice');


                if(isset($test_delivery_details) && count($test_delivery_details) > 0){
                    foreach($test_delivery_details as $key => $test_delivery_detail){

                        $cats_launch_urls['practice_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $test_delivery_detail->test_formid, $test_delivery_detail->test_formversion, $user_thirparty_id, $test_delivery_detail->token);
                        $cats_launch_tokens['practice_test'.($key+1)] = $test_delivery_detail->token;
                        $cats_launch_btns['practice_test'.($key+1)] = ($test_delivery_detail->status == 1) ? 'disable' : 'enable';
                    }
                    $userproduct_val['practice_test']['launch_urls'] = $cats_launch_urls;
                    $userproduct_val['practice_test']['launch_tokens'] = $cats_launch_tokens;
                    $userproduct_val['practice_test']['launch_btns'] = $cats_launch_btns;
                    
                    //WP-1308 Starts
                    $tds_test_query = $this->db->query('SELECT * FROM `tds_tests` WHERE candidate_id = "' . $user_thirparty_id . '" AND test_type = "practice" ');
                    if ($tds_test_query->getNumRows() > 0) {
                        $userproduct_val['practice_test']['practice_test_count'] = $tds_test_query->getNumRows();
                    }
                    //WP-1308 Ends
                }
                // Tds practics test result processing
                $practice_tds_results = $this->bookingmodel->tds_practice_detail($user_thirparty_id, 'practice');
                if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                foreach($practice_tds_results as $key => $practice_tds_result){
                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                    $tds_practice['practice_test1'] = $practice_tds_result;
                }else{
                    $tds_practice['practice_test2'] = $practice_tds_result;
                }
                }
                $userproduct_val['tds_practice_results'] = $tds_practice;
                }

                // Get Practice test Launch details if CollegePre as tds 
                $practice_tests = $this->bookingmodel->pract_test($user_thirparty_id);
                if(($practice_tests != FALSE) && count($practice_tests) > 0){
                    foreach($practice_tests as $key => $practice_test){
                        $cp_launch_details['practice_test'.($key+1)] = array('test_number' => $practice_test['test_number'], 'candidate_number' => $practice_test['candidate_number'], 'final_result' => $practice_test['final_practice_thirdparty_id'],'practisce_status' => $practice_test['practise_result_status']);
                    }
                    $userproduct_val['practice_test']['launch_details'] = $cp_launch_details;
                }  
                     
                //WP-1301 - Final test arrangements tab section for unsupervised learner -- Dashboard
                if($userproduct_val['booking_id'] > 0 && $userproduct_val['booking_test_delivary_id'] != ''){
                    $supervised = $this->bookingmodel->get_token_status_by_thirdpartyid($user_thirparty_id);
                    if(!$supervised){
                        $final_test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                        if(isset($final_test_delivery_details) && count($final_test_delivery_details) > 0){
                            foreach($final_test_delivery_details as $key => $final_test_delivery_detail){

                                $final_cats_launch_urls['final_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $final_test_delivery_detail->test_formid, $final_test_delivery_detail->test_formversion, $user_thirparty_id, $final_test_delivery_detail->token);
                                $final_cats_launch_tokens['final_test'.($key+1)] = $final_test_delivery_detail->token;
                                $final_cats_launch_btns['final_test'.($key+1)] = ($final_test_delivery_detail->status == 1) ? 'disable' : 'enable';
                            }
                            $userproduct_val['final_test']['launch_urls'] = $final_cats_launch_urls;
                            $userproduct_val['final_test']['launch_tokens'] = $final_cats_launch_tokens;
                            $userproduct_val['final_test']['launch_btns'] = $final_cats_launch_btns;
                            $userproduct_val['final_test']['is_supervised'] = $supervised;
                        }
                    }
                }

                $userproduct_val['token_status'] = $this->bookingmodel->get_token_status_by_thirdpartyid($user_thirparty_id);

            /********** WP-1202 - Get and SET Final test Launch details **********/
            $institution_timezone = $this->bookingmodel->get_institution_timezone($user_thirparty_id);
            if($institution_timezone){
                
                $final_test_booking_details = $this->bookingmodel->get_booking_date_time_details($user_thirparty_id);
                if(isset($final_test_booking_details) && count($final_test_booking_details) > 0){
         
                    $current_utc_time = @get_current_utc_details();
                    $current_utc_timestamp = $current_utc_time['current_utc_timestamp'];
                    
                        if($current_utc_timestamp >= $final_test_booking_details['start_date_time'] && $current_utc_timestamp <= $final_test_booking_details['end_date_time']){
                            $userproduct_val['final_test_section'] = 'show';
                            // Get Final test Launch details if CAT's as tds
                            $final_test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                            if(isset($final_test_delivery_details) && count($final_test_delivery_details) > 0){
                                foreach($final_test_delivery_details as $key => $final_test_delivery_detail){

                                    $final_cats_launch_urls['final_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $final_test_delivery_detail->test_formid, $final_test_delivery_detail->test_formversion, $user_thirparty_id, $final_test_delivery_detail->token);
                                    $final_cats_launch_tokens['final_test'.($key+1)] = $final_test_delivery_detail->token;
                                    $final_cats_launch_btns['final_test'.($key+1)] = ($final_test_delivery_detail->status == 1) ? 'disable' : 'enable';
                                }
                                $userproduct_val['final_test']['launch_urls'] = $final_cats_launch_urls;
                                $userproduct_val['final_test']['launch_tokens'] = $final_cats_launch_tokens;
                                $userproduct_val['final_test']['launch_btns'] = $final_cats_launch_btns;
                            }
                            
                        }
                    }
                }
            }

            switch ($userproduct_val['product_id']) {
                case "1":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "2":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "3":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "4":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "5":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "6":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "7":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "8":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "9":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;	
                case "10":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "11":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;
                case "12":
                    $newarray[$userproduct_val['product_id']][] = $userproduct_val;
                    break;							
                default:
                    break;
            }
        }

        $products = $this->bookingmodel->get_products($userid);
        $products = $newarray;
        $next_prod = $newarray;
        krsort($next_prod);
        $count = 1;		
        foreach ($next_prod as $next_key => $next_val) {	
        if($count == 1) {	
            $highest_level_purchased = $next_key;	
        }
        $count++;
        }
        $this->session->remove('token_number');

  
        if ($this->acl_auth->logged_in()) {

            $this->data = $this->session->get();
            if (!empty($products)) {

                $this->data['products'] = $products;
                $this->data['highest_level_purchased'] = $highest_level_purchased;
                $this->data['languages'] = $this->cmsmodel->get_language();

                if (isset($course_data) && !empty($course_data)) {
                    $this->data['enrolstatus'] = json_encode($course_data);
                } else {
                    $this->data['enrolstatus'] = json_encode(array('p_id' => ''));
                }

                if (!empty($this->data['products'])) {
                    foreach ($this->data['products'] as $key => $val) {
                        $courses_array[$key] = $val['0']['name'];
                        break;
                    }
                }
                /*tds Final result pending or test not taken check*/
                $first_product = current($products);
                $user_thirparty_id = $first_product[0]['user_thirparty_id'];
                //for final test
                $final_link = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                if(isset($final_link) && !empty($final_link)){
                    $this->data['tds_final_result_status'] = $final_link['0']->status;
                }


            /* HIGHER NEXT LEVEL PROCESSING - STARTS */
            if ($highest_level_purchased > 9 && $highest_level_purchased < 13) {
                $first_product = current($products);
                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($first_product[0]['booking_test_delivary_id']);
                if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){

                    $builder = $this->db->table('booking');
                    $builder->select('booking.id, booking.user_id, booking.test_delivary_id, booking.product_id, tds_results.candidate_id as thirdparty_id, users.user_app_id');
                    $builder->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id');
                    $builder->join('users', 'users.id = booking.user_id', 'left');		
                    $builder->where('booking.user_id', $this->session->get('user_id'));
                    $builder->orderBy("booking.product_id", "desc");
                }else{
                     // next step block indicator	
                     $builder = $this->db->table('booking');
                     $builder->select('booking.id, booking.user_id, booking.test_delivary_id, booking.product_id, collegepre_higher_results.thirdparty_id, users.user_app_id');
                     $builder->join('collegepre_higher_results', 'booking.test_delivary_id = collegepre_higher_results.thirdparty_id');
                     $builder->join('users', 'users.id = booking.user_id', 'left');		
                     $builder->where('booking.user_id', $this->session->get('user_id'));
                     $builder->orderBy("booking.product_id", "desc"); 
                }
                
                $query = $builder->get();
                $booked_result_products = $query->getResultArray();
                if(!empty($booked_result_products)) {
                        foreach ($booked_result_products as $booked_product) {
                                $results_available[$booked_product['product_id']] = $booked_product['thirdparty_id'];
                        }
                }
                if(!empty($results_available)) {
                        $this->data['result_products'] = $results_available;
                }
                if ($first_product[0]['product_id'] == 12) {
                    $this->data['next_level_to_purchase'] = 13;
                } else {
                    $this->data['next_level_to_purchase'] = $first_product[0]['product_id'] + 1;
                }
                if ($first_product[0]['higher_section_one'] != NULL || $first_product[0]['tds_data'] != NULL) {
                   // $this->data['show_open_course'] = 'hide';
                    $this->data['show_book_next'] = 'show';
                } else {
                    $this->data['show_book_next'] = 'hide';
                   $this->data['show_open_course'] = 'show';
                }
                // benchmark values
                $builder = $this->db->table('benchmark_session');
                $builder->select('benchmark_session.id,benchmark_session.user_id,benchmark_session.token,benchmark_session.user_app_id,benchmark_session.benchmark_cefr_level,benchmark_session.datetime,benchmark_session.test_driver, from_unixtime(benchmark_session.datetime, "%d-%m-%Y") as formatdate,tokens.type_of_token ');
                $builder->join('tokens','tokens.token = benchmark_session.token');
                $builder->where('benchmark_session.user_id', $this->session->get('user_id'));
                $builder->orderBy('benchmark_session.datetime', "desc");
                $query = $builder->get();
                $benchmark = $query->getResultArray();
                if (!empty($benchmark)){
                        $this->data['benchmarks'] = $benchmark;
                }
            /* HIGHER NEXT LEVEL PROCESSING - ENDS */
            } else{
                /* CORE DASHBOARD STARTS */
                /* BENCHMARK VALUES CODE - STARTS */
                $builder = $this->db->table('benchmark_session');
                $builder->select('benchmark_session.id,benchmark_session.user_id,benchmark_session.token,benchmark_session.user_app_id,benchmark_session.benchmark_cefr_level,benchmark_session.datetime,benchmark_session.test_driver, from_unixtime(benchmark_session.datetime, "%d-%m-%Y") as formatdate,tokens.type_of_token ');
                $builder->join('tokens','tokens.token = benchmark_session.token');
                $builder->where('benchmark_session.user_id', $this->session->get('user_id'));
                $builder->orderBy('benchmark_session.datetime', "desc");
                $query = $builder->get();
                $benchmark = $query->getResultArray();
                if (!empty($benchmark)){
                        $this->data['benchmarks'] = $benchmark;
                }
                /* BENCHMARK VALUES CODE - ENDS */
                $first_product = current($products);
                
                /* NEXT LEVEL PROCESSING - CORE STARTS */
                $products[$first_product['0']['product_id']] = $first_product;	
                $this->data['products'] = $products;
                $up_thirparty_id = $first_product[0]['user_thirparty_id'];
                $delivery_type_dashboard = $this->get_delivery_type_by_thirdparty_id($up_thirparty_id);
                //$delivery_option = $delivery_type_dashboard[0]['tds_option'];
                $delivery_option = ($delivery_type_dashboard) ? $delivery_type_dashboard['tds_option'] : "catstds";
                $next_cat = $this->view_final_result($up_thirparty_id,$delivery_option);
                if($next_cat){
                    $this->data['show_open_course'] = 'hide';
                    $this->data['show_book_next']= 'show';	
                    if($delivery_option == 'catstds'){
                        $next_level_proccessed = $this->get_next_core_level($first_product[0]['level'],False, False,$next_cat['candidate_id']);
                    }else{
                        $next_level_proccessed = $this->get_next_core_level($first_product[0]['level'], $next_cat['section_one'], $next_cat['section_two'], $next_cat['thirdparty_id']);
                    } 
                    $this->data['next_level_to_purchase'] = $next_level_proccessed['product_id'];
                }else{
                   // $this->data['show_open_course'] = 'show';
                    $this->data['show_book_next']= 'hide';	
                }
                /* NEXT LEVEL PROCESSING - CORE ENDS */
            
                /* changes done to display final results in dashboard- ends */
            } 

            if ($this->detectDevice()) :
                $this->data['is_mobile'] = $this->detectDevice('mobile_os');	
            endif;
            $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE, TRUE);
            
            $userdata = $this->session->get();
            $this->data['profile'] = $this->usermodel->get_profile($userdata['user_id']);
            $learnerType = $this->placementmodel->get_learner_type();
            //WP-1285 Remove first and second questionnaires
            if($learnerType != 'under13'){
                $recent_type_token =  $this->placementmodel->get_questionaire_step_level();
                $this->data['recent_type_of_token'] = $recent_type_token;
            }


            $this->data['helplinks'] = $this->cmsmodel->helplinks();
            $this->data['list_apps'] = $this->cmsmodel->applinks();
            $this->data['higher_type_ids'] = $this->usermodel->get_higher_type_ids();
            $this->session->set('higher_type_ids', $this->usermodel->get_higher_type_ids());

            $purchased_course = array(); $recent_product_id = '';
            $purchase_result = $this->usermodel->get_user_purchashed_course($this->session->get('user_id'));
            
            if(!empty($purchase_result)){
                $i = 0;
                foreach($purchase_result as $data):
                    if(empty($data->section_one) && empty($data->section_two)){
                        $purchased_course[] = $data;
                    }
                    $i++;
                endforeach;
            }

            if(!empty($purchased_course)){
                $recent_product_id = $purchased_course[0]->product_id;
            }

            /*** For Under 13 users Button enable or disable starts ***/
            if($learnerType == 'under13'){
                $userId = $this->session->get('user_id');
                $prodid = $purchase_result[0]->product_id;
                $buttonEnable = $this->checkButtonEnableUnder13($prodid,$userId);
                if($buttonEnable == 'disable'){
                    $this->data['buttonEnable'] = 0;
                }elseif($buttonEnable == 'enable'){
                    $this->data['buttonEnable'] = 1;
                }
            }

             /*** Under 13 ends ***/
            $this->data['recent_product_id'] = $recent_product_id;
            $this->session->set('recent_product_id', $recent_product_id);

            //Encryption for Web client login call
            $this->data['encryptedToken'] = $this->encryptinc_library->encryptString($this->session->get('user_app_id'), $this->oauth->catsurl('dwh_ws_token'));  
            $this->data['tokenType'] = $this->getTokenType_by_learnerid($this->session->get('user_id'));
            $this->session->set('encryptedToken', $this->encryptinc_library->encryptString($this->session->get('user_app_id'), $this->oauth->catsurl('dwh_ws_token')));

            //WP-1142 - To get institution timezone
            if($recent_product_id != NULL){
                if($products[$recent_product_id][0]['booking_test_delivary_id']){
                    $institution_timezone = $this->bookingmodel->get_institution_timezone($products[$recent_product_id][0]['booking_test_delivary_id']);
                    $this->data['institution_timezone'] = $institution_timezone;
                }
            }
            $this->data['productEligible'] = $this->usermodel->get_institute_productEligible_by_user($this->session->get('user_id'));
            $speakingtests = $this->placementmodel->get_placement_session_test_details('speaking_test');
            if($speakingtests != FALSE){
                $this->data['speakingtests'] = $speakingtests;
            }

            $all_products = $this->productmodel->product_list();
            $this->data['all_products'] = $all_products;
            
            $learner_firstvisit_detail = $this->placementmodel->get_learner_firstvisit_detail($this->session->get('user_id'));
            
            if($learner_firstvisit_detail){
                $this->data['firstvisit_detail'] = $learner_firstvisit_detail;
            }

            $product_count = count($this->data['products']);
            if($product_count > 1){
                $this->data['is_first_product'] = 0;
            }else{
               $level_by_stepcheck = $this->bookingmodel->get_step_level_by_stepcheck($this->session->get('user_app_id'));
                if($level_by_stepcheck){
                    $this->data['is_first_product'] = 0;
                }else{
                    $this->data['is_first_product'] = 1;
                }
            }

            if($learnerType == 'under13'){
                //update first_visit to 1
                $update_firstvisit_under16 = $this->placementmodel->update_firstvisit_under16($this->session->get('user_id'));
            }
            
            $prod_tokentype	= $this->bookingmodel->get_tokentype_by_thirdpartyid($purchase_result[0]->thirdparty_id);
            if($prod_tokentype){
                $this->data['tokenType'] = 	$prod_tokentype;
            }
            // WP-1301 ends
            
            echo view('site/header', $this->data);
            echo view('site/menus', $this->data);
            echo view('site/dashboard', $this->data);
            echo view('site/footer', $this->data);

            }else {
            
                $placement_tests = $this->placementmodel->get_benchmark_session_test_details();
                $tds_placement_tests = $this->tdsmodel->get_tds_placement_session_test_details();
                if($placement_tests != FALSE){
                    $recent_placement_test = current($placement_tests);
                }else{
                    $recent_placement_test = '';
                }
                
                if($recent_placement_test != ''){
                    if (strpos($recent_placement_test['type_of_token'], 'benchmarking_type_') === 0) {
                            $token_type = 'benchmarking';
                    }else{
                            $token_type = '';
                    }
                    if($recent_placement_test['type_of_token'] === 'benchmarktest' || $token_type === 'benchmarking'){
                        
                        // benchmark values
                        $builder = $this->db->table('benchmark_session');
                        $builder->select('benchmark_session.id, benchmark_session.user_id, benchmark_session.token, benchmark_session.user_app_id, benchmark_session.benchmark_cefr_level, benchmark_session.datetime, benchmark_session.test_driver, from_unixtime(benchmark_session.datetime, "%d-%m-%Y") as formatdate,tokens.type_of_token' );
                        $builder->join('tokens','tokens.token = benchmark_session.token');
                        $builder->where('benchmark_session.user_id', $this->session->get('user_id'));
                        $builder->orderBy('benchmark_session.datetime', "desc");
                        $query = $builder->get();
                        $benchmark = $query->getResultArray();
                        
                        if (!empty($benchmark)){
                            $this->data['benchmarks'] = $benchmark;
                            
                            if($token_type != 'benchmarking'){
                                $userdata = $this->session->get();
                                $this->data['profile'] = $this->usermodel->get_profile($userdata['user_id']);
                                $recent_type_token =  $this->placementmodel->get_recent_type_of_token();
                                $this->data['recent_type_of_token'] = $recent_type_token;
                            }
                        } else {
                            $this->data['benchmarks'] = '';
                        }
                        
                        $speakingtests = $this->placementmodel->get_placement_session_test_details('speaking_test');
                        if($speakingtests != FALSE){
                            $this->data['speakingtests'] = $speakingtests;
                        }
                        if(isset($tds_placement_tests) && $tds_placement_tests != FALSE){
                            $this->data['tds_placement_tests'] = $tds_placement_tests;
                        }
                        $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
                        $this->data['languages'] = $this->cmsmodel->get_language();
                        echo view('site/header', $this->data);
                        echo view('site/menus', $this->data);
                        echo view('site/dashboard_empty', $this->data);
                        echo view('site/footer', $this->data);

                    }elseif($this->placementmodel->get_placement_session_test_details('speaking_test')){
                        // Speaking Test values
                        $speakingtests = $this->placementmodel->get_placement_session_test_details('speaking_test');
                        $this->data['speakingtests'] = $speakingtests;
                        if(isset($tds_placement_tests) && $tds_placement_tests != FALSE){
                            $this->data['tds_placement_tests'] = $tds_placement_tests;
                        }
                        $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
                        $this->data['languages'] = $this->cmsmodel->get_language();
                        echo view('site/header', $this->data);
                        echo view('site/menus', $this->data);
                        echo view('site/dashboard_speaking', $this->data);
                        echo view('site/footer', $this->data);
                    }else{
                        $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
                        $this->data['languages'] = $this->cmsmodel->get_language();
                        echo view('site/header', $this->data);
                        echo view('site/menus', $this->data);
                        echo view('site/dashboard_empty', $this->data);
                        echo view('site/footer', $this->data);
                    }
                }elseif($tds_placement_tests != FALSE){
                    $this->data['tds_placement_test'] = $tds_placement_tests;
                    $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
                    $this->data['languages'] = $this->cmsmodel->get_language();
                    echo view('site/header', $this->data);
                    echo view('site/menus', $this->data);
                    echo view('site/dashboard_tds_placement', $this->data);
                    echo view('site/footer', $this->data);
                }else{
                    $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
                    $this->data['languages'] = $this->cmsmodel->get_language();
                    echo view('site/header', $this->data);
                    echo view('site/menus', $this->data);
                    echo view('site/dashboard_empty', $this->data);
                    echo view('site/footer', $this->data);
                }
            }
        }
    }

    public function profile() {

    if(!$this->acl_auth->logged_in()){
        return redirect()->to('/');    
    }
		
	//Check for Cats primary
	$learnertype = $this->session->get('learnertype');
	if($learnertype == 'under13'){
    	return redirect()->to('/');   
	}

        $userdata = $this->session->get();
        $this->data['profile'] = $this->usermodel->get_profile($userdata['user_id']);
        if($this->session->get('tablang') || $this->session->get('tabpass') )
        	$this->session->set('tabprofile', false);
        else 
        	$this->session->set('tabprofile', true);
            if($this->request->getPost("profile_submit"))
            {
                $this->session->set('tabprofile', true);
                $this->session->set('tabpass', false);
                $this->session->set('tablang', false);

                $rules =[
                    'firstname' => [
                    'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                    'rules'  => 'required|max_length[100]|serbia_username_check',
                    'errors' => [
                        'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check')
                    ]
                    ],
                    'secondname' => [
                    'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                    'rules'  => 'required|max_length[100]|serbia_username_check',
                    'errors' => [
                        'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check')
                    ]
                    ]
                ];

                if($userdata['user_email'] != $this->request->getPost("email"))
                {
                    $rules =[
                        'firstname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                        'rules'  => 'required|max_length[100]|serbia_username_check',
                        'errors' => [
                            'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check')
                        ]
                        ],
                        'secondname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                        'rules'  => 'required|max_length[100]|serbia_username_check',
                        'errors' => [
                            'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check')
                        ]
                        ],
                        'email' => [
                        'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                        'rules'  => 'required|max_length[254]|is_unique[users.email]|valid_email',
                        ],
                    ];
                }
                if (!$this->validate($rules)) {
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                } else {
                    $profiledata = array('name' => $this->request->getPost('firstname')." ".$this->request->getPost('secondname'),'firstname' => $this->request->getPost('firstname'), 'lastname' => $this->request->getPost('secondname'), 'email' => $this->request->getPost('email'));
                if ($this->usermodel->update_profile($profiledata)) {
                    $this->session->set('user_firstname', $this->request->getPost('firstname'));
                    $this->session->set('user_lastname', $this->request->getPost('secondname'));
                    $this->session->set('user_email', $this->request->getPost('email'));//WP-1271 - Change email address
                    $this->session->set('user_username', $this->request->getPost('email'));//WP-1271 - Change email address
                    $this->session->set('username',$this->request->getPost('email'));//WP-1271 - Change email address
                    $this->session->setFlashdata('messages', lang('app.language_admin_profile_updated_success_msg'));
                    return redirect()->to('site/profile'); 
                }
                }
            }
            if($this->request->getPost("changepass_submit"))
            {
                $this->session->set('tabpass', TRUE);
                $this->session->set('tabprofile', false);
                $this->session->set('tablang', false);

                $rules =[
                    'current_password' => [
                      'label'  => lang('app.language_site_booking_screen2_label_current_password'),
                      'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                      'errors' => [
                        'new_password_check' => lang('app.language_site_booking_screen2_password_check')
                      ]
                      ],
                      'new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_new_password'),
                        'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                        'errors' => [
                            'new_password_check' => lang('app.language_site_booking_screen2_password_check')
                          ]
                      ],
                      'confirm_new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_confirm_new_password'),
                        'rules'  => 'required|min_length[8]|max_length[20]|matches[new_password]',
                        ]
                    ];

                if (!$this->validate($rules)) {
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                } else {
                    $user = $this->data['profile'];
                    $this->passwordhash = new \Config\PasswordHash(8,FALSE);
                    if (! $this->passwordhash->CheckPassword($this->request->getPost('current_password'),$user[0]->password)) {
                        $this->session->setFlashdata('errors', lang('app.language_site_change_password_current_password_invalid_msg'));
                        return redirect()->to('site/profile');
                    } else {
                        $passwordata = array('password' => $this->passwordhash->HashPassword($this->request->getPost('new_password')));
                        if ($this->usermodel->update_profile($passwordata)) {
                            $this->session->set('password', base64_encode($this->request->getPost('new_password').rand(999, 9999)));
                            $this->session->setFlashdata('messages', lang('app.language_site_change_password_updated_success_msg'));
                            return redirect()->to('site/profile');
                        }
                    }
                }
            }
            if($this->request->getPost("language_submit"))
            {
                $this->session->set('tablang', TRUE);
                $this->session->set('tabpass', false);
                $this->session->set('tabprofile', false);

                $rules =[
                    'language' => [
                      'label'  => lang('app.language_admin_language'),
                      'rules'  => 'required',
                      ],
                    ];

                if (!$this->validate($rules)) {
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                } else {
                    $profiledata = array('language_id' => intval(base64_decode($this->request->getPost('language'))));
                    if ($this->usermodel->update_profile($profiledata)) {
                        $builder = $this->db->table('user_products');
                        $builder->select('products.alp_id,tds_results.candidate_id,collegepre_results.thirdparty_id,collegepre_higher_results.thirdparty_id as higher_clgpre');
                        $builder->join('products', 'products.id = user_products.product_id');
                        $builder->join('tds_results', 'tds_results.candidate_id = user_products.thirdparty_id','left');
                        $builder->join('collegepre_results','collegepre_results.thirdparty_id = user_products.thirdparty_id','left');
                        $builder->join('collegepre_higher_results',  'collegepre_higher_results.thirdparty_id = user_products.thirdparty_id','left');
                        $builder->where('user_products.thirdparty_id LIKE "'.$this->session->get('user_app_id').'%" ');
                        $builder->orderBy("user_products.id", "DESC");
                        $builder->limit(1);
                        $query = $builder->get();
                        if ($query->getNumRows() > 0) {
                            $result = $query->getRowArray();
                            if($result['candidate_id'] === NULL && $result['thirdparty_id'] === NULL && $result['higher_clgpre'] === NULL){
                                log_message('error', "Language Updated From Learner Profile - " .print_r($this->session->get('user_app_id')." - ".intval(base64_decode($this->request->getPost('language'))),true));
                                $dwh_data["language"] = intval(base64_decode($this->request->getPost('language')));
                                $dwh_data["userid"] = $this->session->get('user_app_id');
                                $dwh_data["courseid"] = $result['alp_id'];
                                $response = $this->http_ws_call_update_unit_progress_language(json_encode($dwh_data));
                            }
                        }
                        $this->expire_mobile_tokens($this->session->get('user_app_id'));
                        $this->session->setFlashdata('messages', lang('app.lsetting_success_msg'));
                        return redirect()->to('site/profile');
                    }
                }
            }
        $learnerType = $this->placementmodel->get_learner_type();
        $this->data['lang_code'] = $this->lang->lang();
        $this->data['all_languages'] = $this->cmsmodel->get_language(FALSE,TRUE);
        echo view('site/header', $this->data);
        echo view('site/menus', $this->data);
        echo view('site/profile',$this->data);
        echo view('site/footer');
    }


    //Curl function to get update unit progress by language
    function http_ws_call_update_unit_progress_language($data = FALSE) {
        if ($data != FALSE) {
            $serverurl = $this->oauth->catsurl('update_unit_progress');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $serverurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . $data . '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            return $output;
        }
    }

    public function expire_mobile_tokens($learner_id = FALSE)
    { 
        if ($learner_id != FALSE) {
            $serverurl = $this->oauth->catsurl('mapi_url') . 'expiretokens';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$serverurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,'data={ "leanerid": "'.$learner_id.'", "expire": "1" }');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            return $server_output;
        }
    }

    public function logout()
    {
        error_log(date('[Y-m-d H:i:s e] ') . "Logout Test" . ", Email-" . $this->session->get('user_email') . ", user_app_id- " . $this->session->get('user_app_id') . PHP_EOL, 3, LOG_FILE_LOGOUT);        
        $this->acl_auth->logout();
        return redirect()->to('/'); 
    }

    public function detectDevice($mobile_type = False)
    {
         $detect =   $this->mobiledetect;
         $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
         if($mobile_type == 'mobile_os'){
             $array_device_model = array(
                 'device_os' => ($detect->isiOS() ? 'IOS' : 'Android'),
                 'device_type' => ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer')
             );
             return  $array_device_model;
         }
 
         if($deviceType != 'computer'):
            return TRUE;
         else:
            return FALSE;
         endif;
    }

    // Get the school details by primary user
    public function get_organizationid_by_primaryuser($userid = FALSE){
        IF($userid){
            $query = $this->db->query('SELECT IT.organization_name,IL.instituition_id as organization_id,IL.cats_product FROM users U JOIN instituition_learners IL ON IL.user_id = U.id JOIN users IU ON IU.id = IL.instituition_id JOIN institution_tier_users ITU ON IU.id = ITU.user_id JOIN institution_tiers IT ON ITU.institutionTierId = IT.id WHERE U.id = "'.$userid.'"');
            $result = $query->getRowArray();
            if($query->getNumRows() > 0){
                return $result;
            }else{
                return FALSE;
            }
        }
    }  

    public function getTokenCode_by_learnerid($userid = FALSE){
        if($userid){
            $query = $this->db->query('SELECT token FROM tokens WHERE is_used = 0 AND user_id = '.$userid);
            $result = $query->getRow();
            if($query->getNumRows() > 0){
            $tokenCode = $result->token;
            return $tokenCode;
            }else{
            return FALSE;
            }
        }
        else{
            return FALSE;
        }
    }

    public function getTokenType_by_learnerid($userid = FALSE){
        if($userid){
            $query = $this->db->query('SELECT type_of_token FROM tokens WHERE user_id = '.$userid);
            $result = $query->getRow();
            if($query->getNumRows() > 0){
            $tokenType = $result->type_of_token;
            return $tokenType;
            }else{
            return FALSE;
            }
        }
        else{
            return FALSE;
        }
    }

    //For malaysian view screen changing starts
    public function bookingscreen1() {

		if($this->acl_auth->logged_in() && !($this->session->get('show')=="errors")){
            $this->acl_auth->logout();
            return redirect()->to(site_url('site/is-cat-available-for-me'));
		}
        if($this->acl_auth->logged_in()){
			$userid = $this->session->get('user_id');
			$learnerType = $this->placementmodel->get_learner_type();
			
			if($learnerType == 'under13'){
				$orgData = $this->get_organizationid_by_primaryuser($this->session->get('user_id'));
				$tokenCode = $this->getTokenCode_by_learnerid($this->session->get('user_id'));
                $tokenType = $this->getTokenType_by_learnerid($this->session->get('user_id'));
				$orgData['token'] = $tokenCode;
				$orgData['type_of_token'] = $tokenType;
				$organizationData = array(
					'code' => $tokenCode,
					'organization_data' => $orgData
				);
				$this->session->set($organizationData);
				$learnerProdType = $this->placementmodel->get_learner_product_type($this->session->get('user_id'));
				if($learnerProdType == 'cats_primary'){
                    return redirect()->to(site_url('site/get-the-right-primary-level'));
				}else{
					$app_id = $this->placementmodel->get_booking_test($this->session->get('user_id'));
                if($app_id){
                  return redirect()->to(site_url('site/dashboard'));
                }else{
                    return redirect()->to(site_url('site/get-the-right-level'));
                }
					
				}
			}
		}	
        
        $formSubmit = $this->request->getPost('getStarted');
        if($formSubmit == "under13"){

            $data= array('languages' => $this->data['languages'], 'user_name' => $this->request->getPost('user_name'),'user_password' => $this->request->getPost('user_password'));
            if (null !== $this->request->getPost()) {

                $rules =[
                    'user_name' => [
                      'label'  => lang('app.language_primary_username'),
                      'rules'  => 'required',
                      ],
                      'user_password' => [
                        'label'  => lang('app.language_primary_password'),
                        'rules'  => 'required',
                        ],
                    ];

                    if ($this->validate($rules) == FALSE) {
                        
                    }else {
                        $username = $this->request->getPost('user_name');
                        $password = strtoupper($this->request->getPost('user_password'));
                        $user = $username;
                        $session_data = array('name', 'email', 'username', 'firstname', 'lastname', 'id', 'user_app_id');
                        $success = $this->acl_auth->login($user, $password, FALSE, $session_data);
                        $chkrole = $this->usermodel->has_role($this->session->get('user_id'),'learner');
                        if($success != NULL  && $chkrole != NULL ) {
                         //Get Token code for under13 learner
						$tokenCode = $this->getTokenCode_by_learnerid($this->session->get('user_id'));
                        $tokenType = $this->getTokenType_by_learnerid($this->session->get('user_id'));
						$orgData = $this->get_organizationid_by_primaryuser($this->session->get('user_id'));
						$orgData['token'] = $tokenCode;
						$orgData['type_of_token'] = $tokenType;
						$moves = array(
							'steps' => array('prev' => 'bookingscreen1', 'next' => 'bookingscreen3'),
							'code' => $tokenCode,
							'organization_data' => $orgData
						);
						$this->session->set($moves);

                        $learnerProdType = $this->placementmodel->get_learner_product_type($this->session->get('user_id'));
						if($learnerProdType == 'cats_primary'){
                            return redirect()->to(site_url('site/get-the-right-primary-level'));
						}else{

						$app_id2 = $this->placementmodel->get_booking_test($this->session->get('user_id'));
                            if($app_id2){
                                return redirect()->to(site_url('site/dashboard'));
                            }else{
                                return redirect()->to(site_url('site/get-the-right-level'));
                            }
						}
                        }else{
                            $this->session->setFlashdata('primaryerrors', lang('app.language_primary_login_failure_msg'));

                        }
                    }
            }

        }else{
            $formSubmitunder16 = $this->request->getPost('getStarted');
            if(isset($_GET['r']) && $_GET['r']=='1'){
                $this->session->setFlashdata('errors', "Session expired. Try login again and proceed!");
            }
            $data= array('languages' => $this->cmsmodel->get_language(), 'selectedToken' => $this->request->getPost('code'), 'is_mobile' => $this->detectDevice());
            $this->session->remove('code');
            if ($formSubmitunder16 == "over13") {
                $rules1 =[
                    'code' => [
                      'label'  => 'Code',
                      'rules'  => 'required',
                      ],
                 ];
                }else{
                    $rules1 =[];
                }
			if ($this->validate($rules1) == FALSE && $formSubmitunder16 == "over13") {
				$this->session->remove('code');
			}elseif($formSubmitunder16 == "over13"){

				$usedCheck = $this->bookingmodel->get_dist_all_by_token($this->request->getPost('code'), 'used');
				if($usedCheck){
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_already_used_code'));
                    return redirect()->to(site_url('site/is-cat-available-for-me'));
                  }
                $existsCheck = $this->bookingmodel->get_dist_all_by_token($this->request->getPost('code'), 'count');

                if($existsCheck){
					$disdata = $this->bookingmodel->get_dist_all_by_token($this->request->getPost('code'), 'result');
					$moves = array(
						'steps' => array('prev' => 'bookingscreen1', 'next' => 'bookingscreen1a'),
						'code' => $this->request->getPost('code'),
						'school_name' => ucfirst(@$disdata['organization_name']),
						'organization_data' => $this->get_organization_data_by_token($this->request->getPost('code')),
					);
					$this->session->set($moves);
                    return redirect()->to(site_url('site/signup-o-login'));
				}
                else{
					$this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_invalid_code'));
                    return redirect()->to(site_url('site/is-cat-available-for-me'));
				}
            }
        }
        return view('site/booking-screen1',$data);
    }

	public function get_ordertype_by_token($tokentype = FALSE){
		if($tokentype){
			$query = $this->db->query('SELECT * FROM tds_test_detail WHERE test_slug = "'.$tokentype.'"');
			$result = $query->getRow();
			if($query->getNumRows() > 0){
			   return 'benchmarking';
			}else{
			   return FALSE;
			}
		}else{
			return FALSE;
		}
	}

    public function bookingscreen2() {
        //Get Token order type        
        $token_data = $this->session->get('organization_data');
        if($token_data != NULL){
            $token_order_type = $this->get_ordertype_by_token($token_data['type_of_token']);
        }else{
            $token_order_type ='';
        }
  
        $data['languages'] = $this->cmsmodel->get_language();
        
            if (!($this->session->get('code'))) {
                return redirect()->to(site_url('site/is-cat-available-for-me'));
            }
            //redirect to benchmarktest
            if ($this->acl_auth->logged_in() && isset($data['organization_data']['type_of_token']) && $data['organization_data']['type_of_token'] == 'benchmarktest' ){
                $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                return redirect()->to(site_url('site/get-the-right-level'));
            }
            $organization_data = $this->session->get('organization_data');
            if ($this->acl_auth->logged_in() && $this->session->get('code')){

                $user_products = $this->bookingmodel->get_bookings2();
                $last_purchased_product = current($user_products);

            //wp-1358
                $benchmark_entry ='';
                if($token_order_type !='benchmarking' && $token_order_type !='speaking_test' && $token_order_type !='benchmarktest'){
                    $user_id=$this->session->get('user_id');
                    $benchmark_entry = $this->tdsmodel->get_benchmark($user_id);
                    $user_app_id = $this->session->get('user_app_id');
                    $isPlacement = $this->tdsmodel->getPlacement($user_app_id);
                } 

                // wp-1358 ends  
                if($last_purchased_product !=NULL){
                    if($last_purchased_product['booking_test_delivary_id'] != NULL){
                        $third_party_id = $last_purchased_product['booking_test_delivary_id']; 
                    }else{
                        $third_party_id = $last_purchased_product['user_thirparty_id'];  
                    }
                }
                
                if(!empty($last_purchased_product) && ($organization_data['type_of_token'] != 'benchmarktest') && ($organization_data['type_of_token'] != 'speaking_test') && ($token_order_type != 'benchmarking')) {
                
                    $token = $this->session->get('organization_data')['token'];
                    $user_id = $this->session->get('user_id'); 
                    $token_placement_check = $this->check_token_already_exist_for_next_level($token,$user_id);
                    $message = isset($token_placement_check['message']) ? $token_placement_check['message'] : '';
                    if ($message == 'already_used') {
                        $this->session->set('show', "errors");
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_already_used_code'));
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
                    }

                    $user_course = $this->usermodel->get_course_type_by_thirdparty_id($last_purchased_product['user_thirparty_id']);
                    $course_type = $user_course[0]->course_type;
                    $delivery_type_nextBook = $this->get_delivery_type_by_thirdparty_id($last_purchased_product['user_thirparty_id']);
                    $delivery_option_nextBook = ($delivery_type_nextBook) ? $delivery_type_nextBook['tds_option'] : "catstds";
                    if($course_type == "Higher"){
                        $results = $this->bookingmodel->view_higher_result($last_purchased_product['user_thirparty_id'], false);   
                    }else{
                        $results = $this->view_final_result($last_purchased_product['user_thirparty_id'],$delivery_option_nextBook);  
                    }

                    if($results != FALSE) {                                                
                        $product_details = $this->bookingmodel->product_days($last_purchased_product['user_thirparty_id']);
                        if(!empty($product_details) && !empty($benchmark_entry['overall'])){ 
                            if($product_details['product_id'] == 12 ){
                                $this->session->set('recent_cats_product_id', $product_details['product_id']);
                                $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                return redirect()->to(site_url('site/reached_top_level'));
                            }else{
                                $cats_core_end_score = $this->tdsmodel->get_cats_cefr_max_score_by_level("B1.3", "tds");
                                $level= ($benchmark_entry['overall']['score'] >  $cats_core_end_score->scale) ? "B2.1" : $benchmark_entry['overall']['level'];
                                /* TDS-368 StepCheck -> Step - Assigned level condition added to raise a level up */
                                $next_step_level = @get_next_level_byStepcheck($level,$benchmark_entry['overall']);
                                $product_array = $this->get_products($next_step_level);
                                                                        
                                $this->session->set('next_cats_product_id', $product_array['0']['id']);
                                $this->session->set('next_stepcheck_name', $product_array['0']['name']);
                                $this->session->set('next_stepcheck_cefr_level', $product_array['0']['level']);
                                $this->session->set('processed_data',  $benchmark_entry['benchmark_results_id']);                                                     
                                return redirect()->to(site_url('site/next_step'));
                            }                            
                        }else{

                            if($course_type == "Higher"){
                                $result_next_higher = $this->bookingmodel->get_next_higher_level($last_purchased_product['product_id']);
                                if($result_next_higher['product_id'] == 13){
                                    $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                    $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                    return redirect()->to(site_url('site/reached_top_level'));
                                }else{
                                    $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                    $this->session->set('next_cats_pass', 'pass');
                                    $this->session->set('next_cats_name', $result_next_higher['level']);
                                    $this->session->set('next_cats_product_id', $result_next_higher['product_id']);
                                    $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));  
                                    return redirect()->to(site_url('site/next_step'));
                                }
                            }else{
                                if($delivery_option_nextBook == 'catstds'){
                                    $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'],False, False,$results['candidate_id']);
                                }else{
                                    $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'], $results['section_one'], $results['section_two'], $results['thirdparty_id']);
                                }
                                if($next_level_proccessed['result'] == "fail"){
                                    $this->session->set('next_cats_failed', 'fail');
                                }elseif($next_level_proccessed['result'] == "average"){
                                    $this->session->set('next_cats_nearly_failed', 'fail');
                                }else{
                                $this->session->set('next_cats_pass', 'pass'); 
                                }
                                $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                $this->session->set('next_cats_name', $next_level_proccessed['level']);
                                $this->session->set('next_cats_product_id', $next_level_proccessed['product_id']);
                                $this->session->set('recent_cats_cefrlevel', $next_level_proccessed['cefr_val']);
                                $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                return redirect()->to(site_url('site/next_step'));
                            }
                        }
                    }else {
                        $this->session->setFlashdata('messages', lang('language_site_booking_screen2_login_success_msg'));
                        return redirect()->to(site_url('site/cannot_book'));
                    }

                }else if($last_purchased_product == false && !empty($benchmark_entry) && ($isPlacement == FALSE)){
                    if(isset($benchmark_entry['overall']) && !empty($benchmark_entry['overall'])){ 
                        $cats_core_end_score = $this->tdsmodel->get_cats_cefr_max_score_by_level("B1.3", "tds");                         
                        $level= ($benchmark_entry['overall']['score'] >  $cats_core_end_score->scale) ? "B2.1" : $benchmark_entry['overall']['level'];
                        /* TDS-368 StepCheck -> Step - Assigned level condition added to raise a level up */
                        $next_step_level = @get_next_level_byStepcheck($level,$benchmark_entry['overall']);
                        $product_array = $this->get_products($next_step_level);                                                               
                        $this->session->set('next_cats_product_id', $product_array['0']['id']);
                        $this->session->set('next_stepcheck_name', $product_array['0']['name']);
                        $this->session->set('next_stepcheck_cefr_level', $product_array['0']['level']);
                        $this->session->set('benchmark_placement', '1');
                        $this->session->set('processed_data',  $benchmark_entry['benchmark_results_id']);                       
                        return redirect()->to(site_url('site/next_step'));
                    }else{
                        $this->session->set('show', "errors");
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_login_failure_empty_score'));
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
                    }        
                }else{
                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                    return redirect()->to(site_url('site/get-the-right-level'));
                }
            }
        
        $data = array(
            'languages' => $this->data['languages'],
            'data' => '',
            'terms' => $this->cmsmodel->get_cms_contents('terms-conditions', $this->lang->lang())
        );
        $moves = array(
            'steps' => array('prev' => 'bookingscreen1a', 'next' => 'bookingscreen3'),
        );

        //wp-1361 changes
        if ($this->session->get('tabsignup')) {           
            $this->session->set('tabsignup', TRUE);
            $this->session->remove('tablogin', TRUE);
        } else {
            $this->session->set('tablogin', TRUE);
            $this->session->remove('tabsignup', TRUE);
        }
        $this->session->set($moves);

        if ($this->request->getPost('login_submit')) {

            $rules =[
                'username' => [
                'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                'rules'  => 'trim|required|valid_email|max_length[100]',
                ],
                'password_' => [
                    'label'  => lang('app.language_site_booking_screen2_label_password'),
                    'rules'  => 'trim|required|min_length[8]|max_length[20]',
                    ],
                ];

            if ($this->validate($rules) == FALSE) {
                $this->session->remove('tabsignup', TRUE);
                $this->session->set('tablogin', TRUE);
                $data['validation'] = $this->validator;
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_login_failure_empty_msg'));
                echo view('site/booking-screen2', $data);
            }
            else {

                $session_data = array('name', 'email', 'username', 'firstname', 'lastname', 'id', 'user_app_id');
                $success = $this->acl_auth->login($this->request->getPost('username'), $this->request->getPost('password_'), FALSE, $session_data);
                // School login after using token  - starts
                $userole = $this->usermodel->chk_role($this->session->get('user_id'));
                if(!empty($userole)){
                    if($userole['0']['name'] == 'school'){
                        $this->session->remove('user_id');
                        $this->session->remove('logged_in');
                        $this->session->remove('user_email');
                        $this->session->setFlashdata('wrong_login', 'You cannot login as school user using token');		
                        return redirect()->to(site_url('site'));
                    }
                }
                if ($success) {
                    $this->session->set(array('username' => $this->request->getPost('username'), 'password' => base64_encode($this->request->getPost('password_').rand(999,9999))));
                    $user_products = $this->bookingmodel->get_bookings2();

                    if($user_products != NULL){
                        $last_purchased_product = current($user_products);                    
                        $third_party_id = $last_purchased_product['user_thirparty_id'];
                    }else{
                        $last_purchased_product = '';                    
                        $third_party_id = '';
                    }
                    //wp-1358
                    $benchmark_entry ='';
                    if($token_order_type !='benchmarking' && $token_order_type !='speaking_test' && $token_order_type !='benchmarktest'){
                        $user_id=$this->session->get('user_id');
                        $benchmark_entry = $this->tdsmodel->get_benchmark($user_id);
                        $user_app_id = $this->session->get('user_app_id');
                        $isPlacement = $this->tdsmodel->getPlacement($user_app_id);
                    } 

                    if(!empty($last_purchased_product) && ($organization_data['type_of_token'] != 'benchmarktest') && ($organization_data['type_of_token'] != 'speaking_test') && ($token_order_type != 'benchmarking')) {
    
                        $token = $this->session->get('organization_data')['token'];
                        $user_id = $this->session->get('user_id'); 
                        $token_placement_check = $this->check_token_already_exist_for_next_level($token,$user_id);
                        $message = isset($token_placement_check['message']) ? $token_placement_check['message'] : '';
                        if ($message == 'already_used') {
                            $this->session->set('show', "errors");
                            $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_already_used_code'));
                            return redirect()->to(site_url('site/is-cat-available-for-me'));
                        }
                        $user_course = $this->usermodel->get_course_type_by_thirdparty_id($third_party_id);
                        $course_type = $user_course[0]->course_type;
                        $delivery_type_nextBook = $this->get_delivery_type_by_thirdparty_id($last_purchased_product['user_thirparty_id']);
                        $delivery_option_nextBook = ($delivery_type_nextBook) ? $delivery_type_nextBook['tds_option'] : "catstds";
                        if($course_type == "Higher"){
                            $results = $this->bookingmodel->view_higher_result($last_purchased_product['user_thirparty_id'], false);   
                        }else{
                        $results = $this->view_final_result($last_purchased_product['user_thirparty_id'],$delivery_option_nextBook);  
                        } 

                        if(!empty($results)) {
                            $product_details = $this->bookingmodel->product_days($last_purchased_product['user_thirparty_id']);
                            if(!empty($product_details) && !empty($benchmark_entry['overall'])){ 
                                if($product_details['product_id'] == 12){
                                    $this->session->set('recent_cats_product_id', $product_details['product_id']);
                                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                    return redirect()->to(site_url('site/reached_top_level'));
                                }else{
                                    $cats_core_end_score = $this->tdsmodel->get_cats_cefr_max_score_by_level("B1.3", "tds");
                                    $level= ($benchmark_entry['overall']['score'] >  $cats_core_end_score->scale) ? "B2.1" : $benchmark_entry['overall']['level'];
                                    /* TDS-368 StepCheck -> Step - Assigned level condition added to raise a level up */
                                    $next_step_level = @get_next_level_byStepcheck($level,$benchmark_entry['overall']);
                                    $product_array = $this->get_products($next_step_level);
                                    $this->session->set('next_cats_product_id', $product_array['0']['id']);
                                    $this->session->set('next_stepcheck_name', $product_array['0']['name']);
                                    $this->session->set('next_stepcheck_cefr_level', $product_array['0']['level']);
                                    $this->session->set('processed_data',  $benchmark_entry['benchmark_results_id']);                            
                                    return redirect()->to(site_url('site/next_step'));
                                }                                                
                            }else{
                            if($course_type == "Higher"){
                                $result_next_higher = $this->bookingmodel->get_next_higher_level($last_purchased_product['product_id']);
                                if($result_next_higher['product_id'] == 13){
                                    $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                    $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                    return redirect()->to(site_url('site/reached_top_level'));
                                }else{
                                    $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                    $this->session->set('next_cats_pass', 'pass');
                                    $this->session->set('next_cats_name', $result_next_higher['level']);
                                    $this->session->set('next_cats_product_id', $result_next_higher['product_id']);
                                    $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                                    $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                                    return redirect()->to(site_url('site/next_step'));	  
                                }
                            }else{
                                if($delivery_option_nextBook == 'catstds'){
                                    $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'],False, False,$results['candidate_id']);
                                }else{
                                    $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'], $results['section_one'], $results['section_two'], $results['thirdparty_id']);
                                }
                                $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                $this->session->set('next_cats_name', $next_level_proccessed['level']);
                                $this->session->set('next_cats_product_id', $next_level_proccessed['product_id']);
                                $this->session->set('recent_cats_cefrlevel', $next_level_proccessed['cefr_val']);
                                if($next_level_proccessed['result'] == "fail"){
                                    $this->session->set('next_cats_failed', 'fail');
                                }elseif($next_level_proccessed['result'] == "average"){
                                    $this->session->set('next_cats_nearly_failed', 'fail');
                                }else{
                                $this->session->set('next_cats_pass', 'pass'); 
                                }
                                $this->session->set('recent_cats_name', $last_purchased_product['name']);
                                $this->session->set('next_cats_name', $next_level_proccessed['level']);
                                $this->session->set('next_cats_product_id', $next_level_proccessed['product_id']);
                                $this->session->set('recent_cats_cefrlevel', $next_level_proccessed['cefr_val']);
                                $this->session->setFlashdata('messages', lang('language_site_booking_screen2_login_success_msg'));
                                return redirect()->to(site_url('site/next_step'));
                            }
                            }
                        }else { 
                            $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                            return redirect()->to(site_url('site/cannot_book'));
                        }
                    }else if(($last_purchased_product == false ) && !empty($benchmark_entry) && ($isPlacement == FALSE)){ 

                        // wp-1358  only benchmark condition
                        if(isset($benchmark_entry['overall']) && !empty($benchmark_entry['overall'])){                          
                            $cats_core_end_score = $this->tdsmodel->get_cats_cefr_max_score_by_level("B1.3", "tds");
                            $level= ($benchmark_entry['overall']['score'] >  $cats_core_end_score->scale) ? "B2.1" : $benchmark_entry['overall']['level'];
                            /* TDS-368 StepCheck -> Step - Assigned level condition added to raise a level up */
                            $next_step_level = @get_next_level_byStepcheck($level,$benchmark_entry['overall']);
                            $product_array = $this->get_products($next_step_level);                                                 
                            $this->session->set('next_cats_product_id', $product_array['0']['id']);
                            $this->session->set('next_stepcheck_name', $product_array['0']['name']);
                            $this->session->set('next_stepcheck_cefr_level', $product_array['0']['level']);
                            $this->session->set('benchmark_placement', '1');
                            $this->session->set('processed_data',  $benchmark_entry['benchmark_results_id']);    
                            return redirect()->to(site_url('site/next_step'));
                        }else{
    
                        $this->session->set('show', "errors");
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_login_failure_empty_score'));
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
                        }      
                    }else{
                        $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                        return redirect()->to(site_url('site/get-the-right-level'));
                        }
                }else {

                    $this->session->set('tablogin', TRUE);
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_login_failure_msg'));
                    return redirect()->to(site_url('site/signup-o-login'));
                }
            }
        }elseif ($this->request->getPost('register_submit')) {

            $rules =[
                'firstname' => [
                'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                'rules'  => 'trim|required|max_length[100]|serbia_username_check',
                'errors' => [
                    'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                ],
                ],
                'secondname' => [
                'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                'rules'  => 'trim|required|max_length[100]|serbia_username_check',
                'errors' => [
                    'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                ], 
                ],
                'email' => [
                'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                'rules'  => 'trim|required|max_length[254]|isemail_check|is_unique[users.email]|',
                'errors' => [
                    'isemail_check' => lang('app.form_validation_valid_email'),
                    'is_unique' => lang('app.language_email_exists'),
                ]
                ],
                'confirm_email' => [
                'label'  => lang('app.language_site_booking_screen2_label_confirm_email_address'),
                'rules'  => 'trim|required|max_length[254]|isemail_check|matches[email]|',
                'errors' => [
                    'isemail_check' => lang('app.form_validation_valid_email'),
                    'matches' => lang('app.language_site_booking_screen2_label_email_confirm_email_mismatch'),
                ]
                ],
                'password' => [
                'label'  => lang('app.language_site_booking_screen2_label_password'),
                'rules'  => 'trim|required|min_length[8]|max_length[20]|new_password_check',
                'errors' => [
                    'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                ]
                ],
                'confirm_password' => [
                'label'  => lang('app.language_site_booking_screen2_label_confirm_password'),
                'rules'  => 'trim|required|min_length[8]|max_length[20]|matches[password]|new_password_check',
                'errors' => [
                    'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                ]
                ],
                'terms' => [
                'label'  => lang('app.language_site_booking_screen2_label_terms'),
                'rules'  => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                $this->session->remove('tablogin', TRUE);
                $this->session->set('tabsignup', TRUE);
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                $data['validation'] = $this->validator;
                return view('site/booking-screen2',$data);

            }
            else {
                    $insdata = array(
                        'name' => $this->request->getPost('firstname') . ' ' . $this->request->getPost('secondname'),
                        'firstname' => $this->request->getPost('firstname'),
                        'lastname' => $this->request->getPost('secondname'),
                        'username' => preg_replace('/([^@]*).*/', '$1', $this->request->getPost('email')),
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'role' => 3,
                    );
                    if ($this->acl_auth->register($insdata)) {
                        error_log(date('[Y-m-d H:i:s e] ') . "User Register Details " . "Email- " . $this->request->getPost('email') . PHP_EOL, 3, LOG_FILE_TDS_REGISTER);
                        $this->session->set(array('username' => $this->request->getPost('email'), 'password' => base64_encode($this->request->getPost('password').rand(999,9999))));
                        $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_register_success_msg'));
                        return redirect()->to(site_url('site/get-the-right-level'));
                    }else {
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                        return redirect()->to(site_url('site/signup-o-login'));
                    }
                }
        }
        elseif ($this->request->getPost('permission')) {
            $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_facebook_email_error'));
            echo json_encode(array('success' => 1, 'message' => 'Not registered'));
        }
        else{
            $data['organization_data'] =  $this->session->get('organization_data');    
            return view('site/booking-screen2',$data);
        }
    }

    public function reached_top_level () {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('site/signup-o-login'));
        }
        if($this->session->get('recent_cats_name') && $this->session->get('recent_cats_cefrlevel')) {
            $data = array(
                'recent_cats_name' => $this->session->get('recent_cats_name'),
                'recent_cats_cefrlevel' => $this->session->get('recent_cats_cefrlevel'),
            );
            $this->session->remove('recent_cats_name');
            $this->session->remove('recent_cats_cefrlevel');
            return view('site/reached_top_level',$data);	
        }else{
            if($this->session->get('recent_cats_product_id')){
                $data['recent_cats_product_id']=$this->session->get('recent_cats_product_id');
                $this->session->remove('recent_cats_product_id');
                return view('site/reached_top_level',$data);
            }else{
                return redirect()->to(site_url('site/dashboard'));
            }
        }		
    }

    public function bookingscreen3() {

        if (!$this->acl_auth->logged_in()) {
            $this->session->set('show',"errors");
            $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_already_entered_code'));
            return redirect()->to(site_url('site/is-cat-available-for-me'));
        }	

        $typeoftoken = $this->session->get('organization_data')['type_of_token'];
        $token_order_type = $this->get_ordertype_by_token($typeoftoken);
        $data['languages'] = $this->cmsmodel->get_language();

        if(isset($typeoftoken) && $typeoftoken == 'benchmarktest'){
            // old benchmarktest Launch error session flashdata message added
            $data['launchTestService'] =  $this->oauth->catsurl('launchTestService');
            if($data['launchTestService']){
                $data['launchUrl'] = '';
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
            }
        }elseif (isset($typeoftoken) && $typeoftoken =='speaking_test'){
            // old Currently not in use only uat Launch error session flashdata message added
            $data['launchTestService'] =  $this->oauth->catsurl('launchTestService');
            if($data['launchTestService']){
                $data['launchUrl'] = "";
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
            }
        }elseif (isset($token_order_type) && $token_order_type =='benchmarking'){

            // Currently working stepcheck
            $data['launchTestService'] =  $this->oauth->catsurl('launchTestService');
            if($data['launchTestService']){
                $launchUrl =  $this->oauth->catsurl('testDeliveryUrl');
                $key =  $this->oauth->catsurl('testLaunchKey');
                $referrer =  $this->oauth->catsurl('testReferrer');
                $userAppId = $this->session->get('user_app_id');
                $token = $this->session->get('organization_data')['token'];
                $user_id = $this->session->get('user_id');
                    //check token already exist for any user
                    $token_check = $this->check_token_already_exist_foruser($token,$user_id);

                    if($token_check){
                        $launchUrl = $this->getLaunchUrl($userAppId, $launchUrl, $key, $referrer, $token, $typeoftoken);
                    }else{
                        $this->session->set('show',"errors");
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_already_used_code'));
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
                    }

                $data['launchUrl'] = $launchUrl;
                $data['test_type'] = 'benchmarking';
            }
        }else{
            //New TDS Placement Test - WP-1301
            $data['launchTestService'] =  $this->oauth->catsurl('launchTestService');
            if($data['launchTestService']){
                $launchUrl =  $this->oauth->catsurl('testDeliveryUrl');
                $key =  $this->oauth->catsurl('testLaunchKey');
                $referrer =  $this->oauth->catsurl('testReferrer');
                $userAppId = $this->session->get('user_app_id');
                $token = $this->session->get('organization_data')['token'];
                $user_id = $this->session->get('user_id');
                
                $token_placement_check = $this->check_token_already_exist_for_placement($token,$user_id);

                if(!$token_placement_check){
                        $this->session->set('show',"errors");
                        $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_already_entered_code'));
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
                }else{
                    $message = isset($token_placement_check['message'])?$token_placement_check['message']:'';
                    // already_used ---> code already assigned to another learner
                    // wrong_code ---> learner already entered some other code
                    if($message == 'already_used'){
                            $this->session->set('show',"errors");
                            $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_m1_already_used_code'));
                            return redirect()->to(site_url('site/is-cat-available-for-me'));
                    }elseif($message == 'wrong_code'){
                            $this->session->set('show',"errors");
                            $this->session->setFlashdata('errors', lang('app.language_site_booking_screen_already_entered_code'));
                            return redirect()->to(site_url('site/is-cat-available-for-me'));
                    }
                }
            if(isset($token) && !empty($token)){
                $placement_tds_token = "P_".$token;	
                $testDeliveryDetails = $this->tdsmodel->get_placement_delivery_detail($typeoftoken);
                $testFormId = $testFormVersion = "";
                if (isset($testDeliveryDetails) && !empty($testDeliveryDetails)) {
                    $testFormId = $testDeliveryDetails->test_formid;
                    $testFormVersion = $testDeliveryDetails->test_formversion;
                    $launchUrl = $this->getPlacementLaunchUrl($userAppId, $launchUrl, $key, $referrer, $placement_tds_token,$testFormId,$testFormVersion);
                }
                if(isset($launchUrl) && !empty($launchUrl)){
                    error_log(date('[Y-m-d H:i:s e] ') . "Placement Test Token & URL " . "Token- " . $placement_tds_token . ", LaunchUrl- " . $launchUrl . PHP_EOL, 3, LOG_FILE_TDS_LAUNCHURL);                    $tds_tests_datas = $this->tdsmodel->get_tds_tests_datas($placement_tds_token,$userAppId,"Placement",$testFormId,$testFormVersion);
                    if(isset($placement_tds_token)){
                        $this->session->remove('placement_tds_token');
                        $this->session->set('placement_tds_token', $placement_tds_token);
                    }
                    if($tds_tests_datas == FALSE){
                        $insData = array('test_formid' => $testFormId,'test_formversion' => $testFormVersion, 'candidate_id' => $userAppId, 'token' => $placement_tds_token, 'test_type'=> "Placement");
                        $this->tdsmodel->save_tds_placement_test_details($insData);
                    }
                } else {
                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
                }
                $data['launchUrl'] = $launchUrl;
                $data['placement_tds_token'] = $placement_tds_token;
            }
            }
        }

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('site/signup-o-login'));
        }	
        if (!($this->session->get('code')) && ($this->session->get('learnertype') == 'over13')) {
            return redirect()->to(site_url('site/is-cat-available-for-me'));
        }

        $data['organization_data'] =  $this->session->get('organization_data');

        if( isset($data['organization_data']['type_of_token']) && $data['organization_data']['type_of_token']!='benchmarktest' 
        && $data['organization_data']['type_of_token']!='speaking_test' && $token_order_type != 'benchmarking'){
            $userAppId = $this->session->get('user_app_id');
            $token = $this->session->get('organization_data')['token'];
            $placement_tds_token = "P_".$token;	
            $placement_test_status = $this->tdsmodel->check_placement_test();
            //WP-1301 to check tds_placement test is done or not
            $tds_tests_datas = "";
            $tds_tests_datas = $this->tdsmodel->get_tds_tests_datas($placement_tds_token,$userAppId,'placement',false,false);
            if(!empty($tds_tests_datas) && $tds_tests_datas['status'] == 1 && $tds_tests_datas['result_status'] == 0){
                $tds_tests_datas = TRUE;
            }

            if(isset($placement_test_status) && !empty($placement_test_status)){              
                $user_products = $this->bookingmodel->get_bookings2();
                $last_purchased_product = current($user_products);
                if(!empty($last_purchased_product)) {
                    $user_course = $this->usermodel->get_course_type_by_thirdparty_id($last_purchased_product['user_thirparty_id']);
                    $course_type = $user_course[0]->course_type;
                    $delivery_type_nextBook = $this->get_delivery_type_by_thirdparty_id($last_purchased_product['user_thirparty_id']);
                    $delivery_option_nextBook = ($delivery_type_nextBook) ? $delivery_type_nextBook[0]['tds_option'] : "catstds";
                    if($course_type == "Higher"){
                    $results = $this->bookingmodel->view_higher_result($last_purchased_product['user_thirparty_id'], false);   
                    }else{
                    $results = $this->view_final_result($last_purchased_product['user_thirparty_id'],$delivery_option_nextBook);  
                    }
                    if(!empty($results)) {
                        if($course_type == "Higher"){
                        $result_next_higher = $this->bookingmodel->get_next_higher_level($last_purchased_product['product_id']); 
                        if($result_next_higher['product_id'] == 13){
                            $this->session->set('recent_cats_name', $last_purchased_product['name']);
                            $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                            $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                            return redirect()->to(site_url('site/reached_top_level'));
                        }else{
                            $this->session->set('recent_cats_name', $last_purchased_product['name']);
                            $this->session->set('next_cats_pass', 'pass');
                            $this->session->set('next_cats_name', $result_next_higher['level']);
                            $this->session->set('next_cats_product_id', $result_next_higher['product_id']);
                            $this->session->set('recent_cats_cefrlevel', $result_next_higher['cefr_level']);
                            $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                            return redirect()->to(site_url('site/next_step'));	  
                        }   
                        }else{
                            if($delivery_option_nextBook == 'catstds'){
                                $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'],False, False,$results['candidate_id']);
                            }else{
                                $next_level_proccessed = $this->get_next_core_level($last_purchased_product['level'], $results['section_one'], $results['section_two'], $results['thirdparty_id']);
                            }
                            $this->session->set('recent_cats_name', $last_purchased_product['name']);
                            if($next_level_proccessed['result'] == "fail"){
                                $this->session->set('next_cats_failed', 'fail');
                            }elseif($next_level_proccessed['result'] == "average"){
                                $this->session->set('next_cats_nearly_failed', 'fail');
                            }else{
                            $this->session->set('next_cats_pass', 'pass'); 
                            }
                            $this->session->set('next_cats_name', $next_level_proccessed['level']);
                            $this->session->set('next_cats_product_id', $next_level_proccessed['product_id']);
                            $this->session->set('recent_cats_cefrlevel', $next_level_proccessed['cefr_val']);
                            $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                            return redirect()->to(site_url('site/next_step'));	
                        }
                    }   else {
                            $this->session->setFlashdata('messages', lang('app.language_site_booking_screen2_login_success_msg'));
                            return redirect()->to(site_url('site/cannot_book'));	
                        }
                }  
            }elseif($tds_tests_datas != "" && $tds_tests_datas === TRUE){
                return redirect()->to(site_url('site/dashboard'));	
            }
        }
        $rules =[
            'level' => [
            'label'  => 'Level',
            'rules'  => 'required',
            ],
        ];
        if (!$this->validate($rules)) {
            return view('site/booking-screen3',$data);

        }else{
            $this->session->remove('code');
            return redirect()->to(site_url('site/dashboard'));	
        }
    }

    public function cannot_book() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('site/signup-o-login'));	
        }
        $data = array(
            'data' => '',
        );
        return view('site/cannot_book',$data);
    }
    
    public function get_tds_launch_url($launch_url, $key, $referrer, $form_id, $form_version, $user_thirparty_id, $token){
            
        $loadTest_status = $this->get_token_institution($token);
        if($loadTest_status != FALSE){
            $xml_data = @file_get_contents($launch_url."launch?key=".$key."&referrer=".$referrer."&testformid=".$form_id."&testformversion=".$form_version."&candidateid=".$user_thirparty_id."&token=".$token."&AppRunMode=LoadTesting");
        }else{
            $xml_data = @file_get_contents($launch_url."launch?key=".$key."&referrer=".$referrer."&testformid=".$form_id."&testformversion=".$form_version."&candidateid=".$user_thirparty_id."&token=".$token);
        }
        $token_array = explode('_', $token);
        if(count($token_array) > 1){
            if(strpos($token_array[0], 'PT') !== false){
                $flash_error = "practice_error";
                $flash_error_txt = "Practice test";
            }
        }else{
            $flash_error = "final_error";
            $flash_error_txt = "Final test";
        }
        if($xml_data){
            $xml_decode_data = json_decode($xml_data);
            if(isset($xml_decode_data->Status) && $xml_decode_data->Status == "OK"){
                $launchUrl = $xml_decode_data->Url;
                return $launchUrl;
            }else{
                $this->session->setFlashdata($flash_error, lang('app.language_site_booking_screen3_tds_launch_error'));
                error_log(date('[Y-m-d H:i:s e] ') . "Error: There is a problem launching your " . $flash_error_txt . " with Form id- " . $form_id . ", Form version- "  . $form_version . ", Thirdparty id- ". $user_thirparty_id . " and Token- ". $token.  PHP_EOL, 3, LOG_FILE_TDS);
            }
        }else{
            $this->session->setFlashdata($flash_error, lang('app.language_site_booking_screen3_tds_launch_error'));
            error_log(date('[Y-m-d H:i:s e] ') . "Error: There is a problem launching your " . $flash_error_txt . " with Form id- " . $form_id . ", Form version- "  . $form_version . ", Thirdparty id- ". $user_thirparty_id . " and Token- ". $token.  PHP_EOL, 3, LOG_FILE_TDS);
        }
    }

    //Load test function to check token in the institution
    function get_token_institution($token){
        if(!empty($token)){

            $token_check = explode('_', $token);
            $placement_token = end($token_check);
            $result = $this->get_organization_data_by_token($placement_token);
            $builder = $this->db->table('config_load_test');
            $builder->select('status');
            $builder->where('institution_id', $result['organization_id']);
            $builder->where('status', 1);
            $query = $builder->get();
            if( $query->getNumRows() > 0){
               return TRUE;
            }else{
               return FALSE;         
            }
        }
    }

    //to get the school data by token
    public function get_organization_data_by_token($token = FALSE) {
        if ($token != FALSE):
            $builder = $this->db->table('tokens');
            $builder->select('tokens.token, tokens.type_of_token, users.id as organization_id, institution_tiers.organization_name');
            $builder->join('school_orders', 'tokens.school_order_id = school_orders.id');
            $builder->join('users', 'school_orders.school_user_id = users.id');
            $builder->join('institution_tier_users', 'users.id = institution_tier_users.user_id');
            $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
            $builder->where('tokens.token', $token);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                $resultData = $query->getRowArray();
                return $resultData;
            }
        endif;
    }

    public function view_final_result($thirdparty_id = false, $delivery_type = false){	
        if($delivery_type == "catstds"){
            $builder = $this->db->table('tds_results');
            $builder->select('tds_results.*, booking.score_calculation_type_id');
            $builder->join('booking', 'booking.test_delivary_id = tds_results.candidate_id');
            $builder->where('tds_results.candidate_id', $thirdparty_id); 
        }else{
            $builder = $this->db->table('collegepre_results');
            $builder->select('collegepre_results.*, booking.score_calculation_type_id');
            $builder->join('booking', 'booking.test_delivary_id = collegepre_results.thirdparty_id');
            $builder->where('collegepre_results.thirdparty_id', $thirdparty_id);
        }
        $query = $builder->get();
        if( $query->getNumRows() > 0){
            return $query->getRowArray();
        }else{
            return false;         
        }
    }

    public function get_delivery_type_by_thirdparty_id($thirdparty_id = FALSE){
        if($thirdparty_id != FALSE){
            $builder = $this->db->table('events');
            $builder->select('events.id,events.tds_option');
            $builder->join('booking', 'booking.event_id = events.id');
            $builder->where('booking.test_delivary_id', $thirdparty_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

    public function get_next_core_level($level= FALSE, $section_one = FALSE, $section_two = FALSE, $thirdparty_id= FALSE) {
        if($section_one != FALSE && $section_one != FALSE){
            $section1 = json_decode($section_one);
            $section2 = json_decode($section_two);
            $section1_result = $this->process_results($section1->item);
            $section2_result = $this->process_results($section2->item);
            $questions = count($section1_result) + count($section2_result);
            $score = array_sum($section1_result) + array_sum($section2_result);
            $score_for_lookup = $score / $questions;
        }else{
            $builder = $this->db->table('tds_results as TR');
            $builder->select('TR.processed_data as data');
            $builder->where('TR.candidate_id', $thirdparty_id);
            $builder->limit(1);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                $coreresults = $query->getRowArray();
                $tds_core_score = json_decode($coreresults['data'], true);
                $score = $tds_core_score['total']['score'];
            }
        }
        //$score = 9;
        $cefr_array = array(
            'A1.1',
            'A1.2',
            'A1.3',
            'A2.1',
            'A2.2',
            'A2.3',
            'B1.1',
            'B1.2',
            'B1.3',
            'B2.1',
            'B2.2',
            'B2.3'
        );

        $cefr_val_threshold = $this->get_cefr_threshold($level, $score, $thirdparty_id, $for = 'viewresult');
        $expploded_val = explode("-", $cefr_val_threshold);
        $cefr_val = $expploded_val['0'];
        //next level based on booking threshold values
            $res_score_settings = $this->bookingmodel->result_display_settings($thirdparty_id);
        if (!empty($res_score_settings)) {
            if ($score >= $res_score_settings['passing_threshold']) {
                $key = array_search($level, $cefr_array);

                if ($cefr_array[$key + 1]) {
                    $product_array = $this->get_products($cefr_array[$key + 1]);
                    if (!empty($product_array)) {
                        $next_product = $product_array['0']['name'];
                    }
                    $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'], 'cefr_level' => $product_array['0']['level'], 'result' => 'pass', 'cefr_val' => $cefr_val);
                }
            } elseif (($score < $res_score_settings['passing_threshold']) && ($score > $res_score_settings['lower_threshold'])) {
                $key = array_search($level, $cefr_array);
                $product_array = $this->get_products($cefr_array[$key]);
                if (!empty($product_array)) {
                    $next_product = $product_array['0']['name'];
                }
                $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'], 'cefr_level' => $product_array['0']['level'], 'result' => 'average', 'cefr_val' => $cefr_val);
            } else {
                $key = array_search($level, $cefr_array);
                if ($key == 0) {
                    $next_search_prod_id = $key;
                } elseif ($key == 1) {
                    $next_search_prod_id = $key - 1;
                } else {
                    $next_search_prod_id = $key - 1;
                }
                if ($cefr_array[$next_search_prod_id]) {
                    $product_array = $this->get_products($cefr_array[$next_search_prod_id]);
                    if (!empty($product_array)) {
                        $next_product = $product_array['0']['name'];
                    }
                    $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'], 'cefr_level' => $product_array['0']['level'], 'result' => 'fail', 'cefr_val' => $cefr_val );
                } else {
                    $result = array('level' => '-', 'score' => $score, 'product_id' => $product_array['0']['id'] );
                }
            }
        } else {
            $result = array('level' => '-', 'score' => $score);
        }
        return $result;
    }

    public function process_results($results) {
        if (!empty($results)) {
            $score = array();
            foreach ($results as $key => $val) {
                if ( count((array)$val->question) == '1') {
                    $score[] += $val->question->{'@attributes'}->score;
                } else {
                    foreach ($val->question as $key3 => $val3) {

                            if (isset($val3->score)) {
                                $score[] += $val3->score;

                            } else{
                                if (isset($val3->{'@attributes'})) {
                                    $score[] += $val3->{'@attributes'}->score;
                                }
                            }                        
                    }
                }
            }   
            return $score;
        } else {
            return false;
        }
    }   

    public function get_cefr_threshold ($level, $score, $testdelivary_id) {	
        $cefr_array = array(
                'A1.1',
                'A1.2',
                'A1.3',
                'A2.1',
                'A2.2',
                'A2.3',
                'B1.1',
                'B1.2',
                'B1.3'
            );

        $res_score_settings = $this->bookingmodel->result_display_settings($testdelivary_id);

        if( $res_score_settings['logit_values'] != NULL ){
            $base_scores = unserialize($res_score_settings['logit_values']); 
        }else{
            $base_scores = '';
        }
        
        if($score >= $res_score_settings['passing_threshold']) {

           // $cal_score = $base_scores[$level] + $score;
            if(!empty($base_scores[$level])){
                $cal_score = $base_scores[$level] + $score;
            }else{
                $cal_score =  $score;
            }

            $cal_level = $level;
            return $cal_level.'-'.$cal_score;	
        } elseif(($score < $res_score_settings['passing_threshold']) && ($score > $res_score_settings['lower_threshold'])) {
            $key = array_search($level, $cefr_array);
            if($key > 0){
                $cal_level = $cefr_array[$key - 1];
                // changes done based on https://catsuk.atlassian.net/browse/WP-526				
                $cal_score = $base_scores[$cal_level] + $res_score_settings['passing_threshold'];
            } else {
                $cal_level = $cefr_array[$key];			
                // changes done based on https://catsuk.atlassian.net/browse/WP-512				
                $cal_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
                $cal_score = $score;
            }
            return $cal_level.'-'.$cal_score;		
        } else {
                // changes done based on https://catsuk.atlassian.net/browse/WP-512	
                $cal_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
                $cal_score = '';
            return $cal_level.'-'.$cal_score;
        }
    }

    public function get_products($level) {
        if ($level) {
            $builder = $this->db->table('products');
            $builder->select('*');
            $builder->where('level', $level);
            $builder->where('id <', '13');		
            $query = $builder->get();
            return $query->getResultArray();
        }
    }

    public  function checkButtonEnableUnder13($prodid, $userId) {

        $builder = $this->db->table('user_products');
        $builder->select('user_products.thirdparty_id');
        $builder->join('users', 'users.id = user_products.user_id', 'left');
        $builder->where('user_products.user_id', $userId);
        $builder->where('user_products.product_id', $prodid);
        $builder->orderBy("user_products.id", "DESC");
        $thirdpartyquery = $builder->get();
        $thirdpartyresult = $thirdpartyquery->getResultArray();
        $thirdparty_id = current($thirdpartyresult);

        if ($prodid < 10) {
            $builder = $this->db->table('collegepre_results');
            $builder->select('collegepre_results.*');
            $builder->where('collegepre_results.thirdparty_id', $thirdparty_id['thirdparty_id']);
            $query = $builder->get();
            $result = $query->getResultArray();
        } else {
            $builder = $this->db->table('collegepre_higher_results');
            $builder->select('collegepre_higher_results.*');
            $builder->where('collegepre_higher_results.thirdparty_id', $thirdparty_id['thirdparty_id']);
            $query = $builder->get();
            $result = $query->getResultArray();
        }
        if ($query->getNumRows() > 0) {
            return 'disable';
        } else {
            return 'enable';
        }
    }

    public function check_token_already_exist_for_next_level($token = FALSE,$user_id = FALSE){
		if($token && $user_id){
            $builder = $this->db->table('tokens');
			$builder->select('tokens.user_id');
			$builder->where('token', $token);
			$query = $builder->get();
			$result = $query->getRow();
			if($query->getNumRows() > 0){	
                $code_response = array();
                $result_user_id = $result->user_id;
                if($result_user_id != 0){
                    $code_response['message'] = 'already_used';
                    return $code_response;
                }
			}
		}
	} 
 
	public function getLaunchUrl($userAppId, $launchUrl, $key, $referrer, $token, $tokentype){
	    $loadTest_status = $this->get_token_institution($token);
        $testDeliveryDetails = $this->placementmodel->get_test_delivery_detail($tokentype);
        if($testDeliveryDetails){
            $testFormId = $testDeliveryDetails->test_formid;
            $testFormVersion = $testDeliveryDetails->test_formversion;
            error_log(date('[Y-m-d H:i:s e] ') . "Stepcheck Test Launch Parameter " . "TestFormId- " . $testFormId . ", TestFormVersion- " . $testFormVersion . ", UserAppID- " . $userAppId . ", Token- "  . $token . ", TypeofToken- ". $tokentype . PHP_EOL, 3, LOG_FILE_TDS_STEPCHECK);

        }
            if($loadTest_status != FALSE){
                $xml_data = @file_get_contents($launchUrl."launch?key=".$key."&referrer=".$referrer."&testformid=".$testFormId."&testformversion=".$testFormVersion."&candidateid=".$userAppId."&token=".$token."&AppRunMode=LoadTesting");
            }else{
                $xml_data = @file_get_contents($launchUrl."launch?key=".$key."&referrer=".$referrer."&testformid=".$testFormId."&testformversion=".$testFormVersion."&candidateid=".$userAppId."&token=".$token);
             
            }
		if($xml_data){
			$xml_decode_data = json_decode($xml_data);
			if(isset($xml_decode_data->Status) && $xml_decode_data->Status == "OK"){
					$launchUrl = $xml_decode_data->Url;
					return $launchUrl;
			}else{
				$this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
			}
		}else{
			$this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));  
		}
	}


	public function check_token_already_exist_for_placement($token = FALSE,$user_id = FALSE){
		if($token && $user_id){

            $builder = $this->db->table('tokens');
			$builder->select('tokens.user_id');
			$builder->where('token', $token);
			$query = $builder->get();
			$result = $query->getRow();
			if($query->getNumRows() > 0){
				
				$code_response = array();
				
				$result_user_id = $result->user_id;
				if($result_user_id == 0){
					
					//check if any token in placement_session	
                    $builder = $this->db->table('users');
                    $builder->select('users.user_app_id');
                    $builder->where('id', $user_id);
                    $query = $builder->get();
                    $userresult = $query->getRow();				
	
					$user_app_id = $userresult->user_app_id;
					
		
                    $builder = $this->db->table('tds_tests');
                    $builder->select('*');
                    $builder->where('candidate_id', $user_app_id);
                    $placementquery = $builder->get();
                    $placementresult = $placementquery->getRow();	
					
					if ($placementquery->getNumRows() > 0){
						$placement_token = substr($placementresult->token, 2);
						if($token == $placement_token){
							return TRUE;
						}else{
							$code_response['message'] = 'wrong_code';
							return $code_response;
						}						
					}else{
						
						//check if any token already assigned to this learner in tokens table					
						$tokenquery = $this->db->query("SELECT id,token FROM tokens WHERE user_id = $user_id AND is_used = 0 AND  type_of_token in ('catslevel','cats_core','cats_core_or_higher','cats_higher')");
						$tokenresult = $tokenquery->getRow();
						
						if ($tokenquery->getNumRows() > 0){
							$exist_token = $tokenresult->token;
							$exist_token_id = $tokenresult->id;
							
							// unassign the existing token
							$exist_data = ['user_id' => '0'];
                            $builder = $this->db->table('tokens');
                            $builder->update($exist_data, ['token' => $exist_token]);
							
							//assign the new token
							$data = ['user_id' => $user_id];
                            $builder = $this->db->table('tokens');
                            $builder->update($data, ['token' => $token]);

							if($this->db->affectedRows() > 0){
								return TRUE;
							}else{
								return FALSE;
							}
							
						}else{

							$data = ['user_id' => $user_id];
                            $builder = $this->db->table('tokens');
                            $builder->update($data, ['token' => $token]);;

							if($this->db->affectedRows() > 0){
								return TRUE;
							}else{
								return FALSE;
							}
						}
					}
				}else{
					if($user_id != $result_user_id){
						$code_response['message'] = 'already_used';
						return $code_response;
					}else{
						return TRUE;
					}
				}
			}
		}
	}

    //New TDS Placement Test - WP-1301
    public function getPlacementLaunchUrl($userAppId, $launchUrl, $key, $referrer, $token,$testFormId,$testFormVersion) {
        //Load test condition added for launch url
         $loadTest_status = $this->get_token_institution($token);
         if($loadTest_status != FALSE){
             $xml_data = @file_get_contents($launchUrl . "launch?key=" . $key . "&referrer=" . $referrer . "&testformid=" . $testFormId . "&testformversion=" . $testFormVersion . "&candidateid=" . $userAppId . "&token=" . $token."&AppRunMode=LoadTesting");
         }else{
             $xml_data = @file_get_contents($launchUrl . "launch?key=" . $key . "&referrer=" . $referrer . "&testformid=" . $testFormId . "&testformversion=" . $testFormVersion . "&candidateid=" . $userAppId . "&token=" . $token);
         }
         if ($xml_data) {
            error_log(date('[Y-m-d H:i:s e] ') . "Placement Test Launch Parameter " . "TestFormId- " . $testFormId . ", TestFormVersion- " . $testFormVersion . ", UserAppID- " . $userAppId . ", Token- "  . $token . PHP_EOL, 3, LOG_FILE_TDS_PLACEMENT);
             $xml_decode_data = json_decode($xml_data);
             if (isset($xml_decode_data->Status) && $xml_decode_data->Status == "OK") {
                 $launchUrl = $xml_decode_data->Url;
                 return $launchUrl;
             } else {
                 $this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
             }
         } else {
             $this->session->setFlashdata('errors', lang('app.language_site_booking_screen3_tds_launch_error'));
         }
    }


    public function next_step() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('site/signup-o-login'));
        }	
        $this->data['organization_data'] = $this->session->get('organization_data');
        $session_datas = $this->data['organization_data'];
        if(!empty($session_datas)){
            $organisation_id = $session_datas['organization_id'];
            $this->data['product_eligiblity'] = $this->placementmodel->get_product_eligiblity($organisation_id);
        }	
		if($this->session->get('recent_cats_name') && $this->session->get('recent_cats_cefrlevel')) {
			$data = array(
                'recent_cats_name' => $this->session->get('recent_cats_name'),
                'recent_cats_cefrlevel' => $this->session->get('recent_cats_cefrlevel'),
                'next_cats_product' => $this->session->get('next_cats_name'),
                'product_eligiblity_institute' => $this->data['product_eligiblity'], 
			);
			$this->session->remove('recent_cats_name');
			$this->session->remove('recent_cats_cefrlevel');
			$this->session->remove('next_cats_name');
            echo view('site/next_step', $data);

		}else if($this->session->get('next_stepcheck_name') && $this->session->get('next_stepcheck_cefr_level')){
           $data = array(
               'next_stepcheck_name'=> $this->session->get('next_stepcheck_name'),
               'next_stepcheck_cefr_val'=> $this->session->get('next_stepcheck_cefr_level'),
               'processed_data'=> $this->session->get('processed_data'),
               'benchmark_placement'=> $this->session->get('benchmark_placement'),
               'product_eligiblity_institute' => $this->data['product_eligiblity'],
            );  
            $this->session->remove('next_stepcheck_name');
            $this->session->remove('next_stepcheck_cefr_level');
            $this->session->remove('benchmark_placement');
            $this->session->remove('processed_data');          
            echo view('site/next_step_stepcheck', $data);
        } else {
            return redirect()->to(site_url('site/dashboard'));
		}		
    }

    public function book_next_cat() {
        
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('site/signup-o-login'));
        }
		if($this->session->get('next_cats_pass')){
			$this->session->remove('next_cats_pass');
		}
		if($this->session->get('next_cats_nearly_failed')){
			$this->session->remove('next_cats_nearly_failed');
		}
		if($this->session->get('next_cats_failed')){
			$this->session->remove('next_cats_failed');
		}	

		if ($this->request->getPost()) { 
            $benchmark_placement=$this->request->getPost('benchmark_placement');   
            $benchmarkid=$this->request->getPost('benchmarkid'); 
			if($this->session->get('code')) {

                $builder = $this->db->table('tokens');
				$builder->select('tokens.id, tokens.token, tokens.school_order_id, tokens.generated_date, tokens.expiry, tokens.is_used, tokens.is_supervised, school_orders.distributor_id, school_orders.distributor_name');
				$builder->join('school_orders', 'school_orders.id = school_order_id');
				$builder->where('token', $this->session->get('code'));
				$query = $builder->get();
				$token_array = $query->getResultArray();

				if($token_array['0']['distributor_id']) {
					$this->session->set('distributor_id', $token_array['0']['distributor_id']);
                    $builder = $this->db->table('users');
					$builder->select('id, city, country');
					$builder->where('distributor_id', $token_array['0']['distributor_id']);
					$query = $builder->get();
					$dis_array = $query->getResultArray();

					if(!empty($dis_array)) {
						$this->session->set('distributor_city', $dis_array['0']['city']);
						$this->session->set('distributor_country', $dis_array['0']['country']);
					}
				}													
			}
			$is_supervised = $token_array[0]['is_supervised'];
           
			if($this->session->get('next_cats_product_id')) {
				$prodid = $this->session->get('next_cats_product_id');

				$product_details = $this->productmodel->product_details($prodid);

				$this->session->remove('next_cats_product_id');
				$this->session->remove('course_name');
				$this->session->remove('course_id');
				$this->session->remove('course_price');
				$this->session->remove('distributor_name');
				$this->session->remove('price');
				$this->session->remove('distributor_amount');
				$this->session->remove('admin_amount');
				
				if (!empty($product_details['0'])) {
					if(isset($product_details['0']['overall_fees']) && isset($product_details['0']['distributor_fees']) && isset($product_details['0']['cats_fees']))
					{
						$distributor_amount = ($product_details['0']['overall_fees'] * $product_details['0']['distributor_fees'])/100 ;
						$admin_amount = ($product_details['0']['overall_fees'] * $product_details['0']['cats_fees'])/100 ;				
						$moves = array(
							'course_name' => $product_details['0']['name'],
							'course_id' => $prodid,
							'course_price' => $product_details['0']['overall_fees'],
							'distributor_name' => $product_details['0']['distributor_name'],
							'distributor_amount' => $distributor_amount,
							'admin_amount' => $admin_amount,
							'currency' => $product_details['0']['currency'],
							'accept' => 'no_check',
						);
						$this->session->set($moves);
						$details = array(
							'payment_method' => 'token',
							'user_id' => $this->session->get('user_id'),
							'distributor_id' => $this->session->get('distributor_id'),
							'total_amount' => $this->session->get('distributor_amount') + $this->session->get('admin_amount'),
							'distributor_amount' => $this->session->get('distributor_amount'),
							'admin_amount' => $this->session->get('admin_amount'),
							'currency' => 'USD',						
							'payment_success' => 'success'						
						);
						$payment_detail = $this->bookingmodel->save_payment_details($details);                     
						if($payment_detail){
							$this->session->set('payment_id', $payment_detail);							
							//get third party id
							$arrData = array(
								'user_id'       => $this->session->get('user_id'),
								'product_id'    => $this->session->get('course_id'),
								'first_name'    => $this->session->get('user_firstname'),
								'last_name'     => $this->session->get('user_lastname'),
								'display_name'  => $this->session->get('user_firstname').' '.$this->session->get('user_lastname')                                                 
							);
							$thirdPartyId = $this->get_thirdparty_id($arrData);
            
							$detail = array(
								'user_id' => $this->session->get('user_id'),
								'distributor_id' => $this->session->get('distributor_id'),
								'product_id' => $this->session->get('course_id'),
								'thirdparty_id' => $thirdPartyId,
								'city' => $this->session->get('distributor_city'),
								'country' => $this->session->get('distributor_country'),
								'purchased_date' => @date("Y:m:d h:m:s"),
								'payment_id' => $this->session->get('payment_id'),
								'payment_done' => 1
							);																									
							$book_detail = $this->bookingmodel->save_booking_details($detail);
							
							$updata = array(
								'user_id' => $this->session->get('user_id'),
								'user_name' => $this->session->get('user_firstname').' '.$this->session->get('user_lastname'),
								'product_id' => $this->session->get('course_id'),
								'level' => $this->session->get('course_name'),
								'thirdparty_id' => $thirdPartyId,
								'redeem_payment_id' => $payment_detail,
								'is_used' => '1',
                                'used_time' => time()   
							);
                             //WP-1264 - Store token redemption date
                            $builder = $this->db->table('tokens');
                            $builder->update($updata, ['token' => $this->session->get('code')]);
                                                        
                            //insert data to batch process
                            $recent_type_token =  $this->placementmodel->get_recent_type_of_token();
                            $arrBatchData_over16 = array(
                                        'user_id'       => $this->session->get('user_id'),
                                        'product_id'    => $recent_type_token['product_id'],
                                        'gender'        => "N",
                                        'first_name'    => $this->session->get('user_firstname'),
                                        'last_name'     => $this->session->get('user_lastname'),
                                        'display_name'  => $this->session->get('user_firstname').' '.$this->session->get('user_lastname'),
                                        'token'         => $recent_type_token['token']
                                        );
                            $this->insert_to_batch($arrBatchData_over16);
                            //practice test codes done for OVER 16 core learner ends
                            
                            //Final test entries for unsupervised learner WP-1301
                            if(!$is_supervised){
                                $booking_detail = $this->get_booking_detail($thirdPartyId);
                                $this->save_booking($booking_detail);
                            }                           
                            //WP-1358 inserting new score 
                            if(!empty($benchmarkid)){               
                                $this->tdsmodel->benchmark_details($benchmarkid,$benchmark_placement,$thirdPartyId);                
                             }                            
                            // WP-1358 ends
							$card = 'token';
                            return redirect()->to(site_url('site/dashboard'));
						}	
					} else {
						$this->session->setFlashdata('message', "Please choose some other product or distributor as fees details are incomplete");
                        return redirect()->to(site_url('site/is-cat-available-for-me'));
					}
				} else {
					$moves = array(
						'price' => 'not present',
						'accept' => 'no_check',
					);
					$this->session->set($moves);
					$this->session->setFlashdata('message', "There is currently a problem with the booking process.  Please contact <a href='".site_url('site/contact')."' target='_blank'>CATs</a>, quoting ".$this->session->get('code')."");
                    return redirect()->to(site_url('site/is-cat-available-for-me'));					
					}				
			} else {
				$this->session->setFlashdata('message', "There is currently a problem with the booking process");
                return redirect()->to(site_url('site/is-cat-available-for-me'));			
			}			
		}
	}	   
    function get_thirdparty_id($arrData)
    {
         $attempt_results = $this->bookingmodel->get_already_purchased_products($arrData['user_id'], $arrData['product_id']);
         if(!empty($attempt_results)){
            $attempt_no = $attempt_results[0]['attempt_no'] + 1;
            $test_delivary_id = $this->session->get('user_app_id').$attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }else{
            $no_attempt_results = $this->bookingmodel->get_already_purchased_products(false, $arrData['product_id']);
            $attempt_no = 1;
            $test_delivary_id  = $this->session->get('user_app_id').$no_attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }
         return $test_delivary_id;
    }


    public function insert_to_batch($arrData)
    {       
        // WP-1202 Insert Practice test in "tds_tests" table for under13 (primary/Core), Core, Higher 
        $elegible_product_lists = $this->usermodel->get_institute_productEligible_by_user($arrData['user_id']);
        $current_product_details = $this->bookingmodel->get_products($arrData['user_id']);
        $current_product_type = $current_product_details[0]['course_type'];
        //Get TDS option for the current Product 
        foreach($elegible_product_lists as $elegible_product){
            $elegible_product_details = $this->eventmodel->get_eligible_productname($elegible_product->group_id);
            if($elegible_product_details[0]['name'] === $current_product_type){
                $product_tds_option = $elegible_product->tds_option;
            }
        }// WP-1202 Insert Practice test in "tds_tests" table for under13 (primary/Core), Core, Higher END
        
         $attempt_results = $this->bookingmodel->get_already_purchased_products($arrData['user_id'], $arrData['product_id']);
         if(!empty($attempt_results)){
            $attempt_no = $attempt_results[0]['attempt_no'];
            $test_delivary_id = $this->session->get('user_app_id').$attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }else{
            $no_attempt_results = $this->bookingmodel->get_already_purchased_products(false, $arrData['product_id']);
            $attempt_no = 1;
            $test_delivary_id  = $this->session->get('user_app_id').$no_attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }
         if($test_delivary_id != ''){
             if(isset($product_tds_option) && $product_tds_option === 'collegepre'){// WP-1202 Check TDS option 
                 $practice_tests = $this->bookingmodel->get_practice_numbers_by_course_id($arrData['product_id']);
                 foreach ($practice_tests as $practice_test):
                     $insData = array('test_number' => $practice_test->test_number,'first_name' => $arrData['first_name'], 'last_name' => $arrData['last_name'], 'display_name' => $arrData['display_name'], 'gender'=> $arrData['gender'], 'thirdparty_id' => $test_delivary_id );
                     $builder = $this->db->table('collegepre_batch_add');
                     $builder->insert($insData);
                 endforeach;
             }
             // WP-1202 Insert Practice test in "tds_tests" table for under13 (primary/Core), Core, Higher 
             if(isset($product_tds_option) && $product_tds_option === 'catstds'){ // WP-1202 Check TDS option
                 $practice_tests = $this->bookingmodel->get_tds_practice_numbers_by_course_id($arrData['product_id'], $current_product_type);
                 if(count($practice_tests) > 0){
                     foreach ($practice_tests as $key => $practice_test){
                         if($practice_test->course_type != 'Higher'){
                             $practice_key = $key+1;
                             $insData = array('test_formid' => $practice_test->test_formid,'test_formversion' => $practice_test->test_formversion, 'candidate_id' => $test_delivary_id, 'token' => "PT".$practice_key."_".$arrData['token'], 'test_type'=> $practice_test->test_type);
                             $this->bookingmodel->save_catstds_practice_test($insData);
                         }
                     }
                 }
             }
         }
         return $test_delivary_id;
    }

    //Final test entry for unsupervised token start WP1301
    /**WP-1301
     * Function to get booking details by thirdparty id to insert Final test details for un supervised user
     * @param integer $thirdparty_id
     * @return string[]|NULL[]|number[]|array[]|boolean
     */
    public function get_booking_detail($thirdparty_id = FALSE){
        if($thirdparty_id){

            $builder = $this->db->table('user_products');
            $builder->select('product_id,user_id');
            $builder->where('thirdparty_id', $thirdparty_id); 
            $query = $builder->get();
            $result = $query->getRow();
            if ($query->getNumRows() > 0) {
                $product_id = $result->product_id;
                $user_id = $result->user_id;
                
                //get attempt number and update the booking
                $attempt_results = $this->get_already_booking($user_id, $product_id);
                if(!empty($attempt_results) && $attempt_results){
                    $attempt_no = $attempt_results[0]['attempt_no'] + 1;
                }else{
                    $attempt_no = 1;
                }
                /* score calculation type */
                $builder = $this->db->table('score_calculation_type');
                $builder->select('*' );
                $builder->orderBy('id', "asc");
                $query = $builder->get();
                $result_calculation = $query->getResultArray();
                foreach($result_calculation as $res_cal) {
                    if($res_cal['active'] == 1) {
                        $score_cal_type = $res_cal['id'];
                    }
                }
                $bookingdetails = array(
                    'user_id'           => $user_id,
                    'event_id'          => "",
                    'product_id'        => $product_id,
                    'attempt_no'        => $attempt_no,
                    'test_delivary_id'  => $thirdparty_id,
                    'datetime'          => @date("Y-m-d H:i:s"),
                    'score_calculation_type_id' => $score_cal_type,
                    'status'			=> 1
                );
                return $bookingdetails;
            }else{
                return FALSE;
            }
        }
    }

    public function get_already_booking($candidate_id = false, $product_id = false){
        
        if($candidate_id != false && $product_id != false){
            $builder = $this->db->table('booking');
            $builder->select('products.level, products.course_id, booking.attempt_no, booking.test_delivary_id');
            $builder->join('products', 'booking.product_id = products.id');
            $builder->where('booking.user_id', $candidate_id);
            $builder->where('booking.product_id', $product_id);
            $builder->orderBy('booking.id', 'DESC');
            $builder->limit(1);
            $query = $builder->get();
            $result = $query->getResultArray();
            return $result;
            
        }else{
            return FALSE;
        }
    }

    /**WP-1301
     * Function to save booking entries for un supervised user final test
     * @param boolean $booking_detail
     */
    public function save_booking($booking_detail = FALSE){
        if(!empty($booking_detail['user_id']) && !empty($booking_detail['product_id'])){
            $userProfile = $this->usermodel->get_profile($booking_detail['user_id']);
            $arrBatchData = array(
                'user_id'       => $booking_detail['user_id'],
                'product_id'    => $booking_detail['product_id'],
                'test_delivary_id'  => $booking_detail['test_delivary_id'],
                'gender'        => $userProfile[0]->gender,
                'first_name'    => $userProfile[0]->firstname,
                'last_name'     => $userProfile[0]->lastname,
                'display_name'  => $userProfile[0]->firstname.' '.$userProfile[0]->lastname
            );
            
            $insert_batch = $this->insert_to_batch_finaltest($arrBatchData);
            if($insert_batch){
                $builder = $this->db->table('booking');
                $builder->insert($booking_detail);
            }
        }
    }

    /**WP-1301
     * Function to insert final test batch entry in tds_test table for un supervised user
     * @param array $arrData
     * @return boolean
     */
    public function insert_to_batch_finaltest($arrData) {
        if ($arrData != '') {
            $final_test_number = $this->get_finaltest_number_by_course_id($arrData);
            if($final_test_number){
                $token = $this->get_token_by_thirdpartyid($arrData['test_delivary_id']);
                if($token != ''){
                    $insData = array('test_formid' => $final_test_number['form_code'], 'test_formversion' => $final_test_number['form_version'], 'candidate_id' => $arrData['test_delivary_id'], 'token' => $token, 'test_type' => 'final');
                    $builder = $this->db->table('tds_tests');
                    $builder->insert($insData);

                }else{
                    return FALSE;
                }
                if ($this->db->affectedRows() > 0) {
                    if($final_test_number['form_code'] != '' && $final_test_number['form_code'] != '0'){

                        $builder = $this->db->table('tds_allocation_formcode');
                        $builder->set('total_exposure','total_exposure - 1', FALSE);
                        $builder->set('current_exposure','current_exposure + 1', FALSE);
                        $builder->where('form_code',$final_test_number['form_code']);
                        $builder->update();

                    }
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301
     * Function to get final test form id and form version by course id for un supervised user
     * @param boolean $arrData
     * @return NULL[]|array[]|boolean
     */
    public function get_finaltest_number_by_course_id($arrData = FALSE){
        $product_id = $arrData['product_id'];
        //$user_id = $arrData['user_id'];
        if($product_id){
            
            $builder = $this->db->table('tds_allocation');
            $builder->where('tds_allocation.product_id', $product_id);
            $builder->where('tds_allocation.tds_option', 'catstds');
            $alloc_query = $builder->get();
            $result = $alloc_query->getRow();
            if ($alloc_query->getRowArray() > 0) {
                $allocation_rule = $result->allocation_rule;
                $allocation_id = $result->id;
    
                if($allocation_rule == 'scheduled'){
                    $formcode_query = $this->db->query('SELECT * FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 ORDER BY form_code_order ASC');
                    $formcode_results = $formcode_query->getResult();
                    foreach($formcode_results as $formcode):
                    $exposure_remaining = $formcode->total_exposure;
                    $form_code = $formcode->form_code;
                    if($exposure_remaining > 0){
                        $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                        $testnumber_result = $testnumber_query->getRow();
                        $testnumber = array();
                        if ($testnumber_query->getNumRows() > 0) {
                            $testnumber['form_code'] = $form_code;
                            $testnumber['form_version'] = $testnumber_result->test_formversion;
                            return $testnumber;
                        }else{
                            return FALSE;
                        }
                    }
                    endforeach;
                    
                    // Random allocation starts
                    $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 order by RAND() limit 1');
                    $formcode_result = $formcode_query->getRow();
                    if ($formcode_query->getNumRows() > 0) {
                        $form_code = $formcode_result->form_code;
                        $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                        $testnumber_result = $testnumber_query->getRow();
                        $testnumber = array();
                        if ($testnumber_query->getNumRows() > 0) {
                            $testnumber['form_code'] = $form_code;
                            $testnumber['form_version'] = $testnumber_result->test_formversion;
                            return $testnumber;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                 
                    $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 order by RAND() limit 1');
                    //}
                    $formcode_result = $formcode_query->getRow();
                    if ($formcode_query->getNumRows() > 0) {
                        $form_code = $formcode_result->form_code;
                        $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                        $testnumber_result = $testnumber_query->getRow();
                        $testnumber = array();
                        if ($testnumber_query->getNumRows() > 0) {
                            $testnumber['form_code'] = $form_code;
                            $testnumber['form_version'] = $testnumber_result->test_formversion;
                            return $testnumber;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }
            }else{
                return FALSE;
            }
        }
    }
    public function get_token_by_thirdpartyid ($thirdparty_id = FALSE){
        $builder = $this->db->table('tokens');
        $builder->select('tokens.token');
        $builder->where('tokens.thirdparty_id', $thirdparty_id);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            $result = $query->getRow();
            $token = $result->token;
            return $token;
        }else{
            return FALSE;
        }
    }

    public function set_language_without_questionaire()
    {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
        $userdata = $this->session->get();
        $this->data['profile'] = $this->usermodel->get_profile($userdata['user_id']);
        //recent token details
        $recent_type_token =  $this->placementmodel->get_recent_type_of_token();
        if (null !== $this->request->getPost()) { 
            $rules =[
                'mylanguage' => [
                    'label'  => lang('app.language_questionarie2_label_error'),
                    'rules'  => 'trim|questionarie2_field_check',
                    'errors' => [
                        'questionarie2_field_check' => lang('app.language_questionarie2_label_error'),
                    ]
                ],
                'mygender' => [
                    'label'  => lang('app.language_questionarie2_label_error'),
                    'rules'  => 'trim|questionarie2_field_check',
                    'errors' => [
                        'questionarie2_field_check' => lang('app.language_questionarie2_label_error'),
                    ]
                ],
                'mydob.0' => [
                    'label'  => lang('app.language_questionarie2_label_error'),
                    'rules'  => 'questionarie2_field_check',
                    'errors' => [
                        'questionarie2_field_check' => lang('app.language_questionarie2_label_error'),
                    ]
                  ],
                  'mydob.1' => [
                    'label'  => lang('app.language_questionarie2_label_error'),
                    'rules'  => 'questionarie2_field_check',
                    'errors' => [
                        'questionarie2_field_check' => lang('app.language_questionarie2_label_error'),
                    ]
                  ],
                  'mydob.2' => [
                    'label'  => lang('app.language_questionarie2_label_error'),
                    'rules'  => 'questionarie2_field_check',
                    'errors' => [
                        'questionarie2_field_check' => lang('app.language_questionarie2_label_error'),
                    ]
                  ],
          ];

            if (!$this->validate($rules)) {
                $errors = array( 
                   'mylanguage'            => $this->validation->showError('mylanguage'),
                   'mygender'              => $this->validation->showError('mygender'),
                   'mydob'                 => $this->validation->showError('mydob.0') ?: $this->validation->showError('mydob.1') ?: $this->validation->showError('mydob.2'),
                );
                $response['errors'] = $errors;        
                echo json_encode($response);die;

            }else{
                $dob = $this->request->getPost('mydob');
                $updateUserdata = array(
                    'language_id' => intval(base64_decode($this->request->getPost('mylanguage'))),
                    'gender'      => base64_decode($this->request->getPost('mygender')),
                    'dob'         => strtotime($dob[0]."-".$dob[1]."-".$dob[2]),
                );

                $builder = $this->db->table('users');
                $builder->update($updateUserdata, ['id' => $userdata['user_id']]);
                
                //update collegepre gender id institution type is collegepre
               if(isset($recent_type_token['thirdparty_id']) && !empty($recent_type_token['thirdparty_id'])){

                   $update_query = $this->db->table('collegepre_batch_add')->select('id')->where('thirdparty_id', $recent_type_token['thirdparty_id'])->get();
                   if($update_query->getNumRows() > 0){
                       foreach($update_query->getResultArray() as $update_id){

                        $data = array(
                            'gender'  => $updateUserdata['gender']
                        );
                        $builder = $this->db->table('collegepre_batch_add');
                        $builder->update($data, ['id' => $update_id['id']]);
                       }
                   }
               }
                //questionare done update
                if(isset($recent_type_token) && isset($recent_type_token['questionnaire_done']) &&  $recent_type_token['questionnaire_done'] == 0 && isset($recent_type_token['token']) && $recent_type_token['token'] != ''):
                    $data = array(
                        'questionnaire_done' => 1,
                        'first_visit' => 1
                    );
                    $builder = $this->db->table('tokens');
                    $builder->update($data, ['token' => $recent_type_token['token']]);

                endif;
                
               if(intval(base64_decode($this->request->getPost('mylanguage'))) > 0 ){
                   $this->expire_mobile_tokens($this->session->get('user_app_id'));
                   $dataSuccess = array('success'=> 1, 'selected'=> $this->request->getPost('mylanguage'), 'msg' => 'language set');
                   echo json_encode($dataSuccess);die;
               }else{
                   $dataSuccess = array('success'=> 0 , 'selected'=> $this->request->getPost('mylanguage'), 'msg' => 'language not set');
                   echo json_encode($dataSuccess);die;
               }
            }
        }else{
            $dataFailute = array('success'=>0, 'selected'=> $this->request->getPost('mylanguage'), 'msg' => 'No post request made!');
            echo json_encode($dataFailute);
        }
    
    }

    function core_certificate($candidate_id = FALSE, $display = FALSE, $from = FALSE){

        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $candidate_id)) {

            $builder = $this->db->table('collegepre_results as CR');
            $builder->select('CR.candidate_name,CR.candidate_id,CR.thirdparty_id,CR.section_one,CR.section_two,CR.result_date,B.score_calculation_type_id,B.test_delivary_id,B.logit_values,B.test_delivary_id,B.product_id,B.event_id,E.start_date_time,E.end_date_time,P.name,P.level');
            $builder->join('booking as B', 'CR.thirdparty_id = B.test_delivary_id');
            $builder->join('products as P', 'B.product_id = P.id');
            $builder->join('events as E', 'B.event_id = E.id');
            $builder->where('CR.candidate_id', $candidate_id);
            $builder->limit(1);
            $query = $builder->get();
            $coreresults = $query->getRowArray();

            if (!empty($coreresults)) {
    
                $thirdPartyId = $coreresults['thirdparty_id'];
                $section1 = json_decode($coreresults['section_one']);
                $section2 = json_decode($coreresults['section_two']);
                $section1_result = $this->process_results($section1->item);
                $section2_result = $this->process_results($section2->item);
                $questions_count = count($section1_result) + count($section2_result);
                $score = array_sum($section1_result) + array_sum($section2_result);
                $for = 'viewresult';
                $cefr_val_threshold = $this->get_cefr_threshold($coreresults['level'], $score, $thirdPartyId, $for);
                $expploded_val = explode("-", $cefr_val_threshold);
                $cefr_val = $expploded_val['0'];
                $score_as_string = $expploded_val['1'];
                $result_type = 'threshold';
                $tz_to = $this->bookingmodel->get_institution_timezone($thirdPartyId);
                if(!empty($coreresults['start_date_time'])){
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $coreresults['start_date_time'], $coreresults['end_date_time']);
                    $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
                }else{
                    $event_date = NULL;
                }
                $examdate = $event_date ? $event_date : date('d-M-Y', strtotime($coreresults['result_date']));
                if($display){
                $graph_data = $this->gengraph($thirdPartyId,FALSE,$from);
                }else{
                  $graph_data = $this->gengraph($thirdPartyId,TRUE,$from);  
                }
                $this->data['results'] = array(
                    'display_popup' => $display,
                    'result_display' => $result_type,
                    'candidate_name' => $coreresults['candidate_name'],
                    'candidate_id' => $candidate_id,
                    'course_name' => $coreresults['name'],
                    'exam_date' => $examdate,
                    'is_supervised' => "Supervised",
                    'bar_graph' => $graph_data,
                    'thirdparty_id' => $thirdPartyId,
                    'score' => $score_as_string,
                    'cefr_level' => $cefr_val
                );

                echo  view('site/corecertificate_view_site',$this->data);
            }
        }elseif(!empty($candidate_id)){

            $builder = $this->db->table('tds_results as CR');
            $builder->select('U.name as candidate_name,CR.candidate_id,CR.processed_data,CR.result_date,CR.pdf_template_version,B.score_calculation_type_id,B.test_delivary_id,B.test_delivary_id,B.product_id,B.event_id,E.start_date_time,E.end_date_time,P.name,P.level,tokens.is_supervised');
            $builder->join('booking as B', 'CR.candidate_id = B.test_delivary_id');
            $builder->join('tokens', 'CR.token = tokens.token');
            $builder->join('users as U', 'B.user_id = U.id');
            $builder->join('products as P', 'B.product_id = P.id');
            $builder->join('events as E', 'B.event_id = E.id','left');
            $builder->where('CR.candidate_id', $candidate_id);
            $builder->limit(1);
            $query = $builder->get();
            $coreresults = $query->getRowArray();

            if (!empty($coreresults)) {
                $processed_data = json_decode($coreresults['processed_data'], true);
                $for = 'viewresult';
                $cefr_val = $processed_data['overall']['level'];
                $score_as_string = $processed_data['overall']['score'];
                $result_type = $processed_data['overall']['result_type'];
                $tz_to = $this->bookingmodel->get_institution_timezone($candidate_id);
                if(!empty($coreresults['start_date_time'])){
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $coreresults['start_date_time'], $coreresults['end_date_time']);
                    $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
                }else{
                    $event_date = NULL;
                }
                $examdate = $event_date ? $event_date : date('d-M-Y', strtotime($coreresults['result_date']));
                if($coreresults['pdf_template_version'] == 1){
                    if($display){
                        $graph_data = $this->gengraphtds($coreresults['candidate_id'],FALSE);
                    }else{
                        $graph_data = $this->gengraphtds($coreresults['candidate_id'],TRUE); 
                    }
                    $this->data['results'] = array(
                        'display_popup' => $display,
                        'result_display' => $result_type,
                        'candidate_name' => $coreresults['candidate_name'],
                        'candidate_id' => $candidate_id,
                        'course_name' => $coreresults['name'],
                        'exam_date' => $examdate,
                        'is_supervised' => ($coreresults['is_supervised'])? "Supervised": "Unsupervised",
                        'bar_graph' => $graph_data,
                        'thirdparty_id' => $candidate_id,
                        'score' => $score_as_string,
                        'cefr_level' => $cefr_val
                    );
                    echo view('site/corecertificate_view_site',$this->data);
                }else{
                    $query = $this->db->query('SELECT passing_threshold FROM `result_display_settings` LIMIT 1');
                    $threshold = $query->getRowArray();
                    $result_status = ($threshold['passing_threshold'] <= $processed_data['total']['score'])? "Pass": "Not achieved the level of the test";
                    $result_score = isset($processed_data['total']) ? round(($processed_data['total']['score'] / $processed_data['total']['outof']) * 100)."%": "";
                    $processed_data['listening']['outof'] = $processed_data['listening']['outof'] == 0 ? 1 : $processed_data['listening']['outof'];
                    $processed_data['reading']['outof'] = $processed_data['reading']['outof'] == 0 ? 1 : $processed_data['reading']['outof'];
                    $listening_score = isset($processed_data['listening']) ? round(($processed_data['listening']['score'] / $processed_data['listening']['outof']) * 100)."%" : "";
                    $reading_score = isset($processed_data['reading']) ? round(($processed_data['reading']['score'] / $processed_data['reading']['outof']) * 100)."%" : "";
                    
                    $query = $this->db->query('(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID LIMIT 1)
                                                UNION(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID DESC LIMIT 1)');
                    $tds_cefrlevel = $query->getResultArray();
                    $speaking_ability = isset($processed_data['speaking']) ? $processed_data['speaking']['ability'] : FALSE;
                    $writing_ability = isset($processed_data['writing']) ?  $processed_data['writing']['ability'] : FALSE;
                    if($speaking_ability || $writing_ability){
                       $sp_wr_types= $this->get_level_type($tds_cefrlevel[0]['ability_estimate'], $tds_cefrlevel[1]['ability_estimate'],$speaking_ability,$writing_ability); 
                       $core_extend_content = @get_content_core($sp_wr_types['speaking'], $sp_wr_types['writing'], $coreresults['level']);
                       $core_extend_content_speaking = isset($core_extend_content['speaking'])? $core_extend_content['speaking']: "";
                       $core_extend_content_writing = isset($core_extend_content['writing'])? $core_extend_content['writing']: "";  
                    }
                    $cefr_all_content = @get_level_contents($result_status, False);
                    $cefr_level_content = @get_level_contents(False, $cefr_val);
                    $this->data['results'] = array(
                        'display_popup' => $display,
                        'result_display' => $result_type,
                        'candidate_name' => $coreresults['candidate_name'],
                        'candidate_id' => $candidate_id,
                        'course_name' => $coreresults['name'],
                        'exam_date' => $examdate,
                        'is_supervised' => ($coreresults['is_supervised'])? "Supervised": "Unsupervised",
                        'thirdparty_id' => $candidate_id,
                        'score' => $score_as_string,
                        'cefr_level' => $cefr_val,
                        //WP-1319 - Changes to Core results process
                        'result_status' => isset($result_status)&& !empty($result_status) ? $result_status : "",
                        'result_score' => isset($result_score)&& !empty($result_score) ? $result_score : "",
                        'listening_score' => isset($listening_score)&& !empty($listening_score) ? $listening_score : "",
                        'reading_score' => isset($reading_score)&& !empty($reading_score) ? $reading_score : "",
                        'sp_wr_types' => isset($sp_wr_types)&& !empty($sp_wr_types) ? $sp_wr_types : "",
                        'speaking_content' => isset($speaking_ability)&& !empty($core_extend_content_speaking) ? $core_extend_content_speaking : "",
                        'writing_content' => isset($writing_ability)&& !empty($core_extend_content_writing) ? $core_extend_content_writing : "",
                        'cefr_all_content' => isset($cefr_all_content)&& !empty($cefr_all_content) ? $cefr_all_content : "",
                        'cefr_level_content' => isset($cefr_level_content)&& !empty($cefr_level_content) ? $cefr_level_content : "",
                    );
                    echo view('site/corecertificate_view_site_extended',$this->data);
                }
            }
        }else {
            echo 'Not a valid GUID';
        }
    }

    public function get_level_type($min_ability = FALSE, $max_ability = FALSE, $speaking_ability = FALSE, $writing_ability = FALSE) {
        if($speaking_ability != FALSE){
            if($min_ability <= $speaking_ability && $max_ability >= $speaking_ability){
                $ability['speaking'] = "At the level";
            }elseif($min_ability > $speaking_ability){
                $ability['speaking'] = "Below the level";
            }else{
                $ability['speaking'] = "Above the level";
            }
        }
        if($writing_ability != FALSE){
            if($min_ability <= $writing_ability && $max_ability >= $writing_ability){
                $ability['writing'] = "At the level";
            }elseif($min_ability > $writing_ability){
                $ability['writing'] = "Below the level";
            }else{
                $ability['writing'] = "Above the level";
            }
        }
        return $ability;
    }


     ///generate GRAPH
     public function gengraph($thirdPartyId = false, $verify_view = false, $from = false)
     {
        if($thirdPartyId != false){

            $query = $this->db->query('SELECT * FROM  collegepre_results WHERE thirdparty_id = "' . $thirdPartyId . '" LIMIT 1');
            if ($query->getRowArray() > 0) {
                $results = $query->getRowArray();

                $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                //user and product info
                $user_app_id = substr($thirdPartyId,0,10);
                $course_id   = substr($thirdPartyId,10,2);
                $attempt_no  =   substr($thirdPartyId,12,2);
                
                //get part setup
                $part_setups  = _part_setup($course_id);
                foreach($part_setups as $part):
                   $labels[] = $part['part'];
                   $scores[] = number_format(array_sum(array_slice($score_sections, $part['start']-1,$part['length'],true))/$part['count'], 2);
                 endforeach;
                 $labels = json_encode($labels);
                 $scores = json_encode($scores);
                 if($from != false){
                    $id = "#final-test-core-dash";
                 }else{
                     $id = "#final-test-core";
                 }
                 if($verify_view != false)
                 {
                     $embed_graph =  "<canvas id='graph_" . $thirdPartyId . "'  ></canvas>
                     <script>
                         var config = {
                             type: 'bar',
                             data: {
                                 labels: $labels,
                                 datasets: [{
                                     label: '',
                                     data :  $scores,   
                                     borderDash: [5, 5],
                                 }]
                             },
                             options: {
                                 responsive: true,
                                 title:{
                                     display:false,
                                     text:'Chart.js Line Chart'
                                 },
                                 legend: {
                                     display: false,
                                 },
                                 scales: {
                                     xAxes: [{
                                         display: true,
                                         scaleLabel: {
                                             display: true,
                                             labelString: 'Skills and Part',
                                             fontSize: 15
                                         },
                                         barPercentage: 0.4, 
                                         gridLines : {
                                             display : false
                                         }
                                     }],
                                     yAxes: [{
                                         display: true,
                                         scaleLabel: {
                                             display: true,
                                             labelString: 'Score',
                                             fontSize: 15
                                         },
                                         ticks: {
                                             suggestedMin: 0,
                                             stepSize:1,
                                             suggestedMax: 5,
                                         }
                                     }]
                                 }
                             }
                         };
 
                         window.onload = function() {
                             var ctx = document.getElementById('graph_" . $thirdPartyId . "').getContext('2d');
                             window.myLine = new Chart(ctx, config);
                         };
                     </script>";
                     return $embed_graph;
                 }else{
                     $embed_graph =  "<canvas id='graph_" . $thirdPartyId . "'  ></canvas>
                     <script>
                         var config = {
                             type: 'bar',
                             data: {
                                 labels: $labels,
                                 datasets: [{
                                     label: '',
                                     data :  $scores,   
                                     borderDash: [5, 5],
                                 }]
                             },
                             options: {
                                 responsive: true,
                                 title:{
                                     display:false,
                                     text:'Chart.js Line Chart'
                                 },
                                 legend: {
                                     display: false,
                                 },
                                  
                                 scales: {
                                     xAxes: [{
                                         display: true,
                                         scaleLabel: {
                                             display: true,
                                             labelString: 'Skills and Part'
                                         },
                                         barPercentage: 0.3, 
                                         gridLines : {
                                             display : false
                                         }
                                     }],
                                     yAxes: [{
                                         display: true,
                                         scaleLabel: {
                                             display: true,
                                             labelString: 'Score'
                                         },
                                         ticks: {
                                             suggestedMin: 0,
                                             stepSize:1,
                                             suggestedMax: 5,
                                         }
                                     }]
                                 },
                                 animation: {
                                     onComplete: function(animation) {
                                         var postdata_$thirdPartyId={
                                             thirdparty_id  : $thirdPartyId,
                                             file           : $('#graph_" . $thirdPartyId . "')[0].toDataURL()
                                         }                                                           
                                         $.post( '".site_url('site/save_chart')."', postdata_$thirdPartyId)
                                           .done(function( ret ) {
                                             console.log( 'Data status: Loaded successfully '+ret);
                                           })
                                           .fail(function( ret ) {
                                             console.log( 'Data status: error ');
                                         })
                                     }
                                 },
                             }
                         };
 
                         $(document).ready(function(){		            			
                             $('$id').on('shown.bs.modal', function (e) {
                                 var ctx = document.getElementById('graph_" . $thirdPartyId . "').getContext('2d');
                                 window.myLine = new Chart(ctx, config);
                             });
                         });
                     </script>";
                     return $embed_graph;
                 }
                 
             }else{
                echo 'ThirdParty ID not found!';
             }
         }
     }
     
     public function save_chart(){
        // Interpret data uri
        $uriPhp = 'data://' . substr( $this->request->getPost('file'), 5);
        // Get content
        $binary = file_get_contents($uriPhp);
        //  $file = $this->efs_charts_path.$this->input->post('thirdparty_id') .'.png';
        $file = $this->efs_charts_results_path .  $this->request->getPost('thirdparty_id') . '.png';
        // Save image
        file_put_contents($file, $binary);
        return 1;
     }
 

    //get two sections merged array
    function _get_two_sections($section1, $section2) {
        $parse_section_one_data = json_decode($section1);
        $parse_section_two_data = json_decode($section2);
        foreach ($parse_section_one_data->item as $items):
            foreach ($items->question as $question):
                if (isset($question->score)):
                    $first_section[] = $question->score;
                elseif (isset($question->{'@attributes'}->score)):
                    $first_section[] = $question->{'@attributes'}->score;
                else:
                endif;
            endforeach;
        endforeach;
        foreach ($parse_section_two_data->item as $items):
            foreach ($items->question as $question):
                if (isset($question->score)):
                    $second_section[] = $question->score;
                elseif (isset($question->{'@attributes'}->score)):
                    $second_section[] = $question->{'@attributes'}->score;
                else:
                endif;
            endforeach;
        endforeach;
        $merge_two_sections = array_merge($first_section, $second_section);
        array_unshift($merge_two_sections, "");
        unset($merge_two_sections[0]);
        return $merge_two_sections;
    }

    function core_certificate_pdf($candidate_id = FALSE){

        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $candidate_id)) {

            $builder = $this->db->table('collegepre_results as CR');
            $builder->select('CR.candidate_name,CR.candidate_id,CR.thirdparty_id,CR.section_one,CR.result_date,CR.section_two,B.score_calculation_type_id,B.test_delivary_id,B.logit_values,B.test_delivary_id,B.product_id,B.event_id,E.start_date_time,E.end_date_time,P.name,P.level');
            $builder->join('booking as B', 'CR.thirdparty_id = B.test_delivary_id');
            $builder->join('products as P', 'B.product_id = P.id');
            $builder->join('events as E', 'B.event_id = E.id');
            $builder->where('CR.candidate_id', $candidate_id);
            $builder->limit(1);
            $query = $builder->get();
            $coreresults = $query->getRowArray();

            if (!empty($coreresults)) {
                $thirdPartyId = $coreresults['thirdparty_id'];
                $section1 = json_decode($coreresults['section_one']);
                $section2 = json_decode($coreresults['section_two']);
                $section1_result = $this->process_results($section1->item);
                $section2_result = $this->process_results($section2->item);
                $questions_count = count($section1_result) + count($section2_result);
                $score = array_sum($section1_result) + array_sum($section2_result);
                $for = 'viewresult';
                $cefr_val_threshold = $this->get_cefr_threshold($coreresults['level'], $score, $thirdPartyId, $for);
                $expploded_val = explode("-", $cefr_val_threshold);
                $cefr_val = $expploded_val['0'];
                $score_as_string = $expploded_val['1'];
                $result_type = 'threshold';
                $tz_to = $this->bookingmodel->get_institution_timezone($thirdPartyId);
                if(!empty($coreresults['start_date_time'])){
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $coreresults['start_date_time'], $coreresults['end_date_time']);
                    $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
                }else{
                    $event_date = NULL;
                }
                $examdate = $event_date ? $event_date : date('d-M-Y', strtotime($coreresults['result_date']));
                // $chartname = $this->efs_charts_path . $thirdPartyId. ".png";
                $chartname = $this->efs_charts_results_path . $thirdPartyId. ".png";
                //QR generation - WP-1221
                $qr_code_url = $google_url = '';
                $qrcode_params = @generateQRCodePath('site', 'core', $coreresults['candidate_id'], false);
                if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                    $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                    $qr_result = json_decode($qrcode);
                    $qr_code_url = $qr_result->qrcode_abs;
                    $google_url = $qr_result->url;
                }
                $this->data['values_core_pdf'] = array(
                    'result_display' => $result_type,
                    'candidate_name' => $coreresults['candidate_name'],
                    'candidate_id' => $candidate_id,
                    'course_name' => $coreresults['name'],
                    'exam_date' => $examdate,
                    'is_supervised' => "Supervised",
                    'thirdparty_id' => $thirdPartyId,
                    'score' => $score_as_string,
                    'cefr_level' => $cefr_val,
                    'qr_code' => $qr_code_url,
                    'google_url' => $google_url,
                    'chart_name' => $chartname
                );
                @generatecoreResultsPDF($this->data['values_core_pdf']);
            }

        }elseif(!empty($candidate_id)){

            $builder = $this->db->table('tds_results as CR');
            $builder->select('U.name as candidate_name,CR.candidate_id,CR.raw_responses,CR.processed_data,CR.result_date,CR.pdf_template_version,B.score_calculation_type_id,B.test_delivary_id,B.test_delivary_id,B.product_id,B.event_id,E.start_date_time,E.end_date_time,P.name,P.level,tokens.is_supervised');
            $builder->join('booking as B', 'CR.candidate_id = B.test_delivary_id');
            $builder->join('tokens', 'CR.token = tokens.token');
            $builder->join('users as U', 'B.user_id = U.id');
            $builder->join('products as P', 'B.product_id = P.id');
            $builder->join('events as E', 'B.event_id = E.id','left');
            $builder->where('CR.candidate_id', $candidate_id);
            $builder->limit(1);
            $query = $builder->get();
            $coreresults = $query->getRowArray();

            if (!empty($coreresults)) {
                $processed_data = json_decode($coreresults['processed_data'], true);
                $cefr_val = $processed_data['overall']['level'];
                $score_as_string = $processed_data['overall']['score'];
                $result_type = $processed_data['overall']['result_type'];
                $tz_to = $this->bookingmodel->get_institution_timezone($candidate_id);
                if(!empty($coreresults['start_date_time'])){
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $coreresults['start_date_time'], $coreresults['end_date_time']);
                    $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
                }else{
                    $event_date = NULL;
                }
                $examdate = $event_date ? $event_date : date('d-M-Y', strtotime($coreresults['result_date']));
                if($coreresults['pdf_template_version'] == 1){
                    // Graph generation for PDF if not avilable - WP-1279 
                    $chartname = $this->efs_charts_results_path . $candidate_id. ".png";
                    if (!file_exists($chartname)){
                        $score = $reading_section = $listening_section = array();
                        $responses = json_decode($coreresults['raw_responses']);
                        if(is_object($responses->response)) {
                            $responses->response = array($responses->response);
                         }
                        foreach ($responses->response as $key => $value) {
                            $value = current($value);
                            if ($value->skill === "R") {
                                $reading_section[] = $value->score;
                            }
                            
                            if ($value->skill === "L") {
                                $listening_section[] = $value->score;
                            }
                        }
                        $merge_two_sections = array_merge($listening_section, $reading_section);
                        array_unshift($merge_two_sections, "");
                        unset($merge_two_sections[0]);
                        $score_sections = $merge_two_sections;
                        $this->gengraphtdspdf($score_sections, $coreresults['candidate_id']);
                    }
                }
                
                //QR generation - WP-1221
                $qr_code_url = $google_url = '';
                $qrcode_params = @generateQRCodePath('site', 'core', $coreresults['candidate_id'], false);
                if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                    $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                    $qr_result = json_decode($qrcode);
                    $qr_code_url = $qr_result->qrcode_abs;
                    $google_url = $qr_result->url;
                }
                if($coreresults['pdf_template_version'] == 1){
                $this->data['values_core_pdf'] = array(
                        'result_display' => $result_type,
                        'candidate_name' => $coreresults['candidate_name'],
                        'candidate_id' => $candidate_id,
                        'course_name' => $coreresults['name'],
                        'exam_date' => $examdate,
                        'is_supervised' => ($coreresults['is_supervised'])? "Supervised": "Unsupervised",
                        'thirdparty_id' => $candidate_id,
                        'score' => $score_as_string,
                        'cefr_level' => $cefr_val,
                        'qr_code' => $qr_code_url,
                        'google_url' => $google_url,
                        'chart_name' => $chartname
                    );
                    @generatecoreResultsPDF($this->data['values_core_pdf']); 
                }else{
                    $query = $this->db->query('SELECT passing_threshold FROM `result_display_settings` LIMIT 1');
                    $threshold = $query->getRowArray();
                    $result_status = ($threshold['passing_threshold'] <= $processed_data['total']['score'])? "Pass": "Not achieved the level of the test";
                    $result_score = isset($processed_data['total']) ? round(($processed_data['total']['score'] / $processed_data['total']['outof']) * 100)."%": "";
                    $processed_data['listening']['outof'] = $processed_data['listening']['outof'] == 0 ? 1 : $processed_data['listening']['outof'];
                    $processed_data['reading']['outof'] = $processed_data['reading']['outof'] == 0 ? 1 : $processed_data['reading']['outof'];
                    $listening_score = isset($processed_data['listening']) ? round(($processed_data['listening']['score'] / $processed_data['listening']['outof']) * 100)."%" : "";
                    $reading_score = isset($processed_data['reading']) ? round(($processed_data['reading']['score'] / $processed_data['reading']['outof']) * 100)."%" : "";
                    
                    $query = $this->db->query('(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID LIMIT 1)
                                                UNION(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID DESC LIMIT 1)');
                    $tds_cefrlevel = $query->getResultArray();
                    $speaking_ability = isset($processed_data['speaking']) ? $processed_data['speaking']['ability'] : FALSE;
                    $writing_ability = isset($processed_data['writing']) ?  $processed_data['writing']['ability'] : FALSE;
                    if($speaking_ability || $writing_ability){
                    $sp_wr_types= $this->get_level_type($tds_cefrlevel[0]['ability_estimate'], $tds_cefrlevel[1]['ability_estimate'],$speaking_ability,$writing_ability); 
                    $core_extend_content = @get_content_core($sp_wr_types['speaking'], $sp_wr_types['writing'], $coreresults['level']);
                    $core_extend_content_speaking = isset($core_extend_content['speaking'])? $core_extend_content['speaking']: "";
                    $core_extend_content_writing = isset($core_extend_content['writing'])? $core_extend_content['writing']: ""; 
                    }
                    $cefr_all_content = @get_level_contents($result_status, False);
                    $cefr_level_content = @get_level_contents(False, $cefr_val);
                    $this->data['values_core_pdf'] = array(
                        'result_display' => $result_type,
                        'candidate_name' => $coreresults['candidate_name'],
                        'candidate_id' => $candidate_id,
                        'course_name' => $coreresults['name'],
                        'exam_date' => $examdate,
                        'is_supervised' => ($coreresults['is_supervised'])? "Supervised": "Unsupervised",
                        'thirdparty_id' => $candidate_id,
                        'score' => $score_as_string,
                        'cefr_level' => $cefr_val,
                        'qr_code' => $qr_code_url,
                        'google_url' => $google_url,
                        //WP-1319 - Changes to Core results process
                        'result_status' => isset($result_status)&& !empty($result_status) ? $result_status : "",
                        'result_score' => isset($result_score)&& !empty($result_score) ? $result_score : "",
                        'listening_score' => isset($listening_score)&& !empty($listening_score) ? $listening_score : "",
                        'reading_score' => isset($reading_score)&& !empty($reading_score) ? $reading_score : "",
                        'sp_wr_types' => isset($sp_wr_types)&& !empty($sp_wr_types) ? $sp_wr_types : "",
                        'speaking_content' => isset($speaking_ability)&& !empty($core_extend_content_speaking) ? $core_extend_content_speaking : "",
                        'writing_content' => isset($writing_ability)&& !empty($core_extend_content_writing) ? $core_extend_content_writing : "",
                        'cefr_all_content' => isset($cefr_all_content)&& !empty($cefr_all_content) ? $cefr_all_content : "",
                        'cefr_level_content' => isset($cefr_level_content)&& !empty($cefr_level_content) ? $cefr_level_content : "",
                    );
                    @generatecoreextendedResultsPDF($this->data['values_core_pdf']);
                }
            }
        }else {
            echo 'Not a valid GUID';
        }
    }

    public function gengraphtds($thirdPartyId = false, $verify_view = false) {

        $reading_section = $listening_section = array();
        if ($thirdPartyId != false) {
            $query = $this->db->query('SELECT * FROM  tds_results WHERE candidate_id = "' . $thirdPartyId . '" LIMIT 1');
            if ($query->getNumRows() > 0) {
                $results = $query->getRowArray();
                $responses = json_decode($results['raw_responses']);
                if(is_object($responses->response)) {
                    $responses->response = array($responses->response);
                }
                foreach ($responses->response as $key => $value) {
                    $value = current($value);
                    if ($value->skill === "R") {
                        $reading_section[] = $value->score;
                    }
                    
                    if ($value->skill === "L") {
                        $listening_section[] = $value->score;
                    }
                }                
                
                $merge_two_sections = array_merge($listening_section, $reading_section);
                array_unshift($merge_two_sections, "");
                unset($merge_two_sections[0]);
                $score_sections = $merge_two_sections;
                
                //user and product info
                $user_app_id = substr($thirdPartyId, 0, 10);
                $course_id = substr($thirdPartyId, 10, 2);
                $attempt_no = substr($thirdPartyId, 12, 2);
                
                //get part setup
                $part_setups = _part_setup($course_id);
                //$labels[] = "";
                //$scores[] = "";
                foreach ($part_setups as $part):
                    //$sum_up_data[] = array('score' => array_sum(array_slice($score_sections, $part['start']-1,$part['length'],true))/$part['count'],'part' => $part['part']);
                    $labels[] = $part['part'];
                    $scores[] = number_format(array_sum(array_slice($score_sections, $part['start'] - 1, $part['length'], true)) / $part['count'], 2);
                endforeach;
                $labels = json_encode($labels);
                $scores = json_encode($scores);

                
                if ($verify_view != false) {
                    $embed_graph = "<canvas id='graph_" . $thirdPartyId . "'  ></canvas>
                    <script>
						var config = {
							type: 'bar',
							data: {
								labels: $labels,
								datasets: [{
									label: '',
									data :  $scores,   
									borderDash: [5, 5],
								}]
							},
							options: {
								responsive: true,
								title:{
									display:false,
									text:'Chart.js Line Chart'
								},
								legend: {
									display: false,
								},
								scales: {
									xAxes: [{
										display: true,
										scaleLabel: {
											display: true,
											labelString: 'Skills and Part',
											fontSize: 15
										},
										barPercentage: 0.4, 
										gridLines : {
											display : false
										}
									}],
									yAxes: [{
										display: true,
										scaleLabel: {
											display: true,
											labelString: 'Score',
											fontSize: 15
										},
										ticks: {
											suggestedMin: 0,
											stepSize:1,
											suggestedMax: 5,
										}
									}]
								}
							}
						};

						window.onload = function() {
							var ctx = document.getElementById('graph_" . $thirdPartyId . "').getContext('2d');
							window.myLine = new Chart(ctx, config);
						};
                    </script>";
                    return $embed_graph;
                } else {
                    $embed_graph = "<canvas id='graph_" . $thirdPartyId . "'  ></canvas>
                    <script>
						var config = {
							type: 'bar',
							data: {
								labels: $labels,
								datasets: [{
									label: '',
									data :  $scores,   
									borderDash: [5, 5],
								}]
							},
							options: {
								responsive: true,
								title:{
									display:false,
									text:'Chart.js Line Chart'
								},
								legend: {
									display: false,
								},
								 
								scales: {
									xAxes: [{
										display: true,
										scaleLabel: {
											display: true,
											labelString: 'Skills and Part'
										},
										barPercentage: 0.3, 
										gridLines : {
											display : false
										}
									}],
									yAxes: [{
										display: true,
										scaleLabel: {
											display: true,
											labelString: 'Score'
										},
										ticks: {
											suggestedMin: 0,
											stepSize:1,
											suggestedMax: 5,
										}
									}]
								},
								
							}
						};

						$(document).ready(function(){		            			
							//$('#myTestresultbox').on('shown.bs.modal', function (e) {
								var ctx = document.getElementById('graph_" . $thirdPartyId . "').getContext('2d');
								window.myLine = new Chart(ctx, config);
							//});
						});
                    </script>";
                    return $embed_graph;
                }
            } else {
                echo 'ThirdParty ID not found!';
            }
        }
    }


  function gengraphtdspdf($score_sections = FALSE, $thirdPartyId = FALSE) {

    require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph.php');
    require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph_bar.php');        

    if ($score_sections != FALSE && $thirdPartyId != FALSE) {
        //user and product info
        $user_app_id = substr($thirdPartyId, 0, 10);
        $course_id = substr($thirdPartyId, 10, 2);
        $attempt_no = substr($thirdPartyId, 12, 2);
        
        //get part setup
        $part_setups = _part_setup($course_id);
        foreach ($part_setups as $part):
        $labels[] = $part['part'];
        
        $scores[] = number_format(array_sum(array_slice($score_sections, $part['start'] - 1, $part['length'], true)) / $part['count'], 2);
        endforeach;
        
        $ydata = $scores; //[5,4,4,3,5,4,3,3,1,3];
        
        // Create the graph.
        // One minute timeout for the cached image
        // INLINE_NO means don't stream it back to the browser.
        $graph = new \Graph(898,449, "auto");
        $graph->SetScale('textint',0,5);
        $graph->SetMargin(60,20,50,140);
        $graph->SetFrame(false);
        $graph->SetBox(false);
        $graph->graph_theme = null;
        
        // Create a bar pot
        $bplot = new \BarPlot($ydata);
        $bplot->SetWidth(18);
        $bplot->SetFillColor("#E5E5E5");
        $bplot->SetWeight(0);
        
        $graph->Add($bplot);
        $array = $labels; //["Listening & Reading 1","Listening & Speaking 2","Listening & Writing 3 ","Reading & Speaking 4","Reading & Speaking 5","Reading 6","Reading 7 ","Reading 8","Reading & Writing 9","Reading & Writing 10"];
        
        //x-axis components
        $graph->xaxis->SetTickLabels($array);
        $graph->xaxis->SetLabelAngle(60);
        $graph->xaxis->SetTitle("Skills and Part",'center');
        $graph->xaxis->SetTitleMargin(110);
        $graph->xaxis->scale->ticks->SetColor('lightgray');
        $graph->xaxis->SetColor('#696969');
        $graph->xaxis->title->SetColor('#666666');
        
        //y-axis components
        $graph->ygrid->SetFill(false);
        $graph->ygrid->SetColor('lightgray');
        $graph->yaxis->title->Set("Score");
        $graph->yaxis->scale->ticks->SetColor('lightgray');
        $graph->yaxis->SetColor('#696969');
        $graph->yaxis->title->SetColor('#666666');
        
        // Send back the HTML page which will call this script again
        // to retrieve the image.
        //$graph->Stroke();
        @unlink($this->efs_charts_results_path.$thirdPartyId.".png");
        $graph->Stroke($this->efs_charts_results_path.$thirdPartyId.".png");
    }
    }

    public function genqrcode($short_url = false, $file_abs_path = false) {
        if($short_url != false && $file_abs_path != false){
            $params['data'] = $short_url;
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH . $file_abs_path;
            $this->ciqrcode->generate($params);
            $success_data = array('code' => 1000, 'url' => $short_url, 'qrcode' => $file_abs_path, 'qrcode_abs' => $file_abs_path, 'message' => 'QR code generated');
            return json_encode($success_data, JSON_PRETTY_PRINT);
        }else{
            $error_data = array('code' => 1002, 'message' => 'Not a valid GUID');
            return json_encode($error_data,JSON_PRETTY_PRINT);
        }
    }

    public function gen_practicetest_result_tds($token = false){
        if($token != false){
            $query = $this->db->query('SELECT * FROM  tds_practicetest_results WHERE token = "' .  $token . '" LIMIT 1');
            if ($query->getNumRows() > 0) {
                $practice_tds  = $query->getRowArray();
                $practice_data = json_decode($practice_tds['processed_data']);
                $data['green_or_orange']   =  $practice_data->green_or_orange;
                $data['results'] = $practice_data->results;
            
                //WP-1308 Starts
                $practice_token = substr($token, 3);
                $tds_test_query = $this->db->query('SELECT * FROM `tds_tests` WHERE token LIKE "%' . $practice_token . '%" AND test_type = "practice" ');
                if ($tds_test_query->getNumRows() > 0) {
                    $data['practice_test_count'] = $tds_test_query->getNumRows();
                }
                //WP-1308 Ends
                echo view('site/load_practice_test_results',$data);
            }else{
                echo 'ThirdParty ID /Test number not found!';
            }
        }
    }

    public function bookingscreen2a() {

        $data['languages'] = $this->cmsmodel->get_language();

        if (!$this->acl_auth->logged_in()) {
            if ($this->request->isAJAX()) {
                if (!empty($_SESSION['old_sess_id'])) {
                    $new_sess_id = $_SESSION['old_sess_id'];
                    session_write_close();

                    session_id($new_sess_id);
                    session_start();
                }
                if ($this->session->get('user_app_id') == '') {
                    $this->session->setFlashdata('errors', "Session expired. Try login again and proceed!");
                    echo json_encode(array('success' => 0, 'redirect' => site_url('site/is-cat-available-for-me'), 'message' => 'Session expired ! kindly do login and begin the session!'));
                    die;
                }
            }
            return redirect()->to('site/is-cat-available-for-me/'.$this->lang->lang());
        }
        //when a user taken linear test alrready we skip this step
        if(isset($this->data['primary_placement_sessions']['recommended_level']) && !empty($this->data['primary_placement_sessions'])){
           return redirect()->to(site_url('site/recommended-primary-level'));
        }
        
        try {

            $json = file_get_contents($this->efsfilepath->efs_linear_path.'test.json', FILE_USE_INCLUDE_PATH);
            $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);

            if (!empty($jsonIterator)) {
                foreach ($jsonIterator as $key => $val) {
                    $data['linear'] = $val;
                }
            } else {
                $this->session->setFlashdata('errors', 'CATs primary placement test is not available. Contact CATs Administrator.');
                return redirect()->to('site/is-cat-available-for-me');
            }
        } catch (\Exception $ex) {
            $this->session->setFlashdata('errors', 'CATs primary placement test is not available. Contact CATs Administrator.');
            return redirect()->to('site/is-cat-available-for-me');
        }
        
         if ($this->request->getPost()) {

            if ($this->encrypter->decrypt(base64_decode($this->request->getPost('token'))) != 'UNDER_13') {
                $this->session->setFlashdata('errors', "Token mismatch");
                echo json_encode(array('success' => 0, 'message' => 'Token mismatch', 'redirect' => site_url('site/get-the-right-primary-level')));
                die;
            }
            if(!isset($data['linear']) && empty($data['linear'])){
                $this->session->setFlashdata('errors', "CATs primary placement test not found.");
                echo json_encode(array('success' => 0, 'message' => 'CATs primary placement test not found.', 'redirect' => site_url('site/get-the-right-primary-level')));
                die;
            }elseif($this->request->getPost('user_reponses') && $this->encrypter->decrypt(base64_decode($this->request->getPost('token'))) == 'UNDER_13'){
                $organization_data = $this->session->get('organization_data');
                $user_id = $this->session->get('user_app_id');
                $u_answers = $this->request->getPost('user_reponses');
                
                $u_scores = 0;
                if(isset($u_answers) && !empty($u_answers)){
                    foreach($u_answers as $u_answer_k => $u_answer_v):
                        $u_screen_id = str_replace("answer_","",$u_answer_k);
                        $u_results[$u_screen_id] =  $this->get_primary_results($u_screen_id, $u_answer_v);
                        $u_scores += $u_results[$u_screen_id]['u_score']; 
                    endforeach;
                }

                $this->data['primary_placement_settings'] = $this->placementmodel->get_placement_settings('linear');
                //recommend level for score
                $logit_values = unserialize($this->data['primary_placement_settings']['logit_values']);

                $lookup_version = $this->data['primary_placement_settings']['version'];
                

                // primary place test - achievement level
                $logit_values = array_filter($logit_values, function($val){
                    return !is_null($val);
                });
                $final_ability = $u_scores;
                
                asort($logit_values);

                foreach ($logit_values as $ky => $ve) {
                    if ($ve > $final_ability) {
                        //$recommended_level_array[$ky] = $ve;
                        break;
                    }
                    $previousArr[$ky] = prev($logit_values);
                }
                
                if (isset($previousArr) && !empty($previousArr)) {
                    end($previousArr);
                } else {
                    reset($logit_values);
                    $key = key($logit_values);
                    $previousArr[$key] = reset($logit_values);
                }
                
                if (isset($logit_values) && !empty($logit_values)) {
                    $recommended_level = str_replace('_', '.', key($previousArr));
                }else{
                    $recommended_level = 'A0.1';
                }
                
                $product_details = $this->productmodel->get_product(FALSE, 'Primary', $recommended_level);
                $overall_records = array(
                    'user_id' => @$user_id,            
                    'testid' => @$data['linear']['testagaid'],
                    'user_answers' => serialize($u_results),
                    'score' => @$u_scores,
                    'task_level' => @$data['linear']['level'],
                    'recommended_level' => @$recommended_level,
                    'product_id' => @$product_details[0]->id,
                    'product_name' => @$product_details[0]->name,
                    'token' => @$organization_data['token'],
                    'token_issued_organization_id' => @$organization_data['organization_id'],
                    'token_issued_organization_name' => @$organization_data['organization_name'],
                    'datetime' => strtotime(date('d-m-Y')),
                    'lookup_versions' => $lookup_version
                );

                $query = $this->db->query('SELECT * FROM primary_placement_session WHERE user_id = "' . @$this->session->get('user_app_id') .'"  AND token = "' . @$organization_data['token'] .'" LIMIT 1');
                if ($query->getNumRows() > 0) {
                    $this->session->setFlashdata('success', "User took primary placement test already!");
                    echo json_encode(array('success' => 1, 'message' => 'Success', 'redirect' => site_url('site/recommended-primary-level')));die;
                } else {

                    $this->db->transStart();
                    $builder = $this->db->table('primary_placement_session');
                    $builder->insert($overall_records);
                    $primary_placement_id = $this->db->insertID();

                    //insert data to user_products
                    if($primary_placement_id != ''){
                        $arrData = array(
                            'user_id'       => $this->session->get('user_id'),
                            'product_id'    => @$product_details[0]->id
                        );
                        $thirdPartyId = $this->save_primary_data_to_user_products($arrData);
                        $arrTokenDataUpdate = array(
                            'user_name'  => $this->session->get('user_name'),
                            'product_id' => @$product_details[0]->id,
                            'level' => @$product_details[0]->name,
                            'thirdparty_id' => $thirdPartyId,
                            'is_used' => 1,
                            'used_time' => time() 
                        );
                        if($arrTokenDataUpdate){
                            $builder = $this->db->table('tokens');
                            $builder->where('token', $this->session->get('code'));
                            $builder->update($arrTokenDataUpdate);
                        }
                    }
                    //create practice test codes for under 13 primary learner 
                    $builder = $this->db->table('users');
                    $builder->select('gender');
                    $builder->where('id', $this->session->get('user_id'));
                    $query =  $builder->get();
                    $user_array = $query->getRowArray();
                    //insert data to batch process
                    $arrBatchData = array(
                        'user_id'       => $this->session->get('user_id'),
                        'product_id'    => @$product_details[0]->id,
                        'gender'        => $user_array['gender'],
                        'first_name'    => $this->session->get('user_firstname'),
                        'last_name'     => $this->session->get('user_lastname'),
                        'display_name'  => $this->session->get('user_firstname').' '.$this->session->get('user_lastname'),
                        'token'         => $this->session->get('code') // WP-1202 Pass token to insert Practice test in "tds_tests" table for under13 Primary
                        );
                    $test_practice_test =    $this->insert_to_batch($arrBatchData);
                    //practice test codes done for under 13 primary learner ends
                    $this->db->transComplete();
                    $this->session->setFlashdata('success', "User took primary placement test!");
                    echo json_encode(array('success' => 1, 'message' => 'Success', 'redirect' => site_url('site/recommended-primary-level')));die;
                }
            }else{
                $this->session->setFlashdata('errors', "Something went wrong");
                echo json_encode(array('success' => 0, 'message'=> 'Failure', 'redirect' => site_url('site/recommended-primary-level'), 'message' => 'Something went wrong.'));
                die;
            }
         }
         echo view('site/booking-screen2a', $data);
    }

    //Start Linear test
    public function check_session()
    {
        $_SESSION['old_sess_id']  = session_id();
         if ($this->request->isAJAX()) {
             if (!empty($_SESSION['old_sess_id'])) {
                 $new_sess_id = $_SESSION['old_sess_id'];
                 session_write_close();
                 session_id($new_sess_id);
                $this->session->start();
             }
             if ($this->session->get('user_app_id') == '') {
                 $this->session->setFlashdata('errors', "Session expired. Try login again and proceed!");
                 echo json_encode(array('session_found' => 0, 'redirect' => site_url('site/is-cat-available-for-me')));
                 die;
             }else{
                 echo json_encode(array('session_found' => 1, 'redirect' => '#'));
                 die;
             }
         }
    }

    function get_primary_results($screen_id, $u_answers)
    {
        try {
            $json = file_get_contents($this->efsfilepath->efs_linear_path.'test.json', FILE_USE_INCLUDE_PATH);
            $jsonIterator = new RecursiveIteratorIterator(
                    new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);
            if (!empty($jsonIterator)) {
                foreach ($jsonIterator as $key => $val) {
                    $screens = $val;
                }
            } else {
                $this->session->setFlashdata('errors', 'CATs primary placement test is not available. Contact CATs Administrator.');
                return redirect()->to('site/is-cat-available-for-me');
            }
        } catch (Exception $ex) {
            $this->session->setFlashdata('errors', 'CATs primary placement test is not available. Contact CATs Administrator.');
            return redirect()->to('site/is-cat-available-for-me');
        }
         
        if (isset($screens)) {
            foreach ($screens['screens'] as $lk => $lv):
                if($screen_id == $lv['screenid'] && isset($u_answers) && !empty($u_answers)):
                     switch ($lv['part']) {
                        case '1611':
                            $results = $this->get_ans_primary($u_answers, $lv, 'a_or_b_or_c');
                            break;
                        case '1612':
                            $results = $this->get_ans_primary($u_answers, $lv, 'a_or_b_or_c');
                            break;
                        case '1613':
                            $results = $this->get_ans_primary($u_answers, $lv, 'yes_or_no');
                            break;
                        case '1622':
                            $results = $this->get_ans_primary($u_answers, $lv, 'a_or_b_or_c');
                            break;
                        case '1627':
                            $results = $this->get_ans_primary($u_answers, $lv, 'yes_or_no');
                            break;
                        case '1628':
                            $results = $this->get_ans_primary($u_answers, $lv, 'yes_or_no');
                            break;
                        case '1629':
                           $results = $this->get_ans_primary($u_answers, $lv, 'dropdown_type');
                           break;
                       case '1630':
                           $results = $this->get_ans_primary($u_answers, $lv, 'text_type');
                            break;
                        default:
                            break;
                    }
                    return $results;
                endif;
            endforeach;
        }    
    }

    function get_ans_primary($u_answers = FALSE, $lv = FALSE, $type = FALSE) {
       
        if($type != FALSE && $type == 'yes_or_no'){
             if($u_answers != FALSE && $lv != FALSE){
                  if ($u_answers['YES'] === 'false' && $u_answers['NO'] === 'false') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers,'u_value' => '', 'u_score' => '0'); 
                  } elseif ($u_answers['YES'] === 'true' && $lv['questions'][0]['answerkey'] === 'YES') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => 'YES', 'u_score' => '1');
                  } elseif ($u_answers['NO'] === 'true' && $lv['questions'][0]['answerkey'] === 'NO') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers,'u_value' => 'NO', 'u_score' => '1');
                  }else {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => array_flip($u_answers)['true'], 'u_score' => '0');
                  } 
                  return $ans_results;
             }
        }elseif($type != FALSE && $type == 'a_or_b_or_c'){
            if($u_answers != FALSE && $lv != FALSE){
                if ($u_answers['A'] === 'false' && $u_answers['B'] === 'false' && $u_answers['C'] === 'false') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers,'u_value' => '', 'u_score' => '0');
                } elseif ($u_answers['A'] === 'true' && $lv['questions'][0]['answerkey'] === 'A') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => 'A', 'u_score' => '1');
                } elseif ($u_answers['B'] === 'true' && $lv['questions'][0]['answerkey'] === 'B') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => 'B', 'u_score' => '1');
                } elseif ($u_answers['C'] === 'true' && $lv['questions'][0]['answerkey'] === 'C') {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => 'C', 'u_score' => '1');
                } else {
                        $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => array_flip($u_answers)['true'], 'u_score' => '0');
                } 
                return $ans_results;
            }
        }elseif($type != FALSE && $type == 'dropdown_type'){
            if($u_answers != FALSE && $lv != FALSE){
                if ($u_answers[0] == $lv['questions'][0]['answerkey']) {
                    $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers[0], 'u_value' => $lv['questions'][0]['answerkey'], 'u_score' => '1');
                }else{
                    $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers[0], 'u_value' =>  $u_answers[0], 'u_score' => '0');
                }
                return $ans_results;
            }
        }elseif($type != FALSE && $type == 'text_type'){
            if($u_answers != FALSE && $lv != FALSE){
                preg_match_all("/\[(.*?)\]/", $lv['questions'][0]['missinganswer'], $matches);
                $u_answers  = @implode("",$u_answers);
                
                if(isset($u_answers) && strcasecmp($u_answers,$matches[1][0]) == 0){
                    $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => $u_answers, 'u_value' => @$u_answers, 'u_score' => '1');
                }  else {
                    $ans_results = array('screenid' => $lv['screenid'], 'u_answer' => @$u_answers, 'u_value' => @$u_answers, 'u_score' => '0');
                }
                return $ans_results;
            }
        }
    }

    //INSERT PRIMARY USER DATA TO USER PRODUCTS    
    function save_primary_data_to_user_products($arrData = FALSE)
    {
        if($arrData != FALSE):
                $distributorId = $this->placementmodel->get_distributor_by_learnerid($this->session->get('user_id'));
                if($distributorId){

                    $builder = $this->db->table('users');
                    $builder->select('id, city, country');
                    $builder->where('distributor_id', $distributorId);
                    $result = $builder->get();
                    $dis_array =  $result->getResultArray();

                }    
                $thirdPartyId = $this->get_thirdparty_id($arrData);
                $detail = array(
                    'user_id' => $this->session->get('user_id'),
                    'distributor_id' => $distributorId,
                    'product_id' => $arrData['product_id'],
                    'thirdparty_id' => $thirdPartyId,
                    'city' => $dis_array[0]['city'],
                    'country' => $dis_array[0]['country'],
                    'purchased_date' => @date("Y:m:d h:m:s"),
                    'payment_id' => '1',
                    'payment_done' => 1
                );
                $user_products_id = $this->bookingmodel->save_booking_details($detail);

                $data = array(
                    'payment_id' => $user_products_id
                );
                $builder = $this->db->table('user_products');
                $builder->where('id', $user_products_id);
                $builder->update($data);

            return $thirdPartyId;
        endif;
    }

    function bookingscreen2p()
    {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('site/is-cat-available-for-me');
        }

        $this->data['languages'] = $this->cmsmodel->get_language();

        //when a user not taken linear test 
        if(!isset($this->data['primary_placement_sessions']['recommended_level']) && empty($this->data['primary_placement_sessions'])){
           return redirect()->to('site/get-the-right-primary-level');
        }
        // Encryption for Web client login call for Primary user
        $this->data['encryptedToken'] = $this->encryptinc_library->encryptString($this->session->get('user_app_id'), $this->oauth->catsurl('dwh_ws_token'));
        $this->session->set('encryptedToken', $this->encryptinc_library->encryptString($this->session->get('user_app_id'),  $this->oauth->catsurl('dwh_ws_token')));
        
        /* WP-807 - hide opencourse based on school access to webversion primary user 15-02-2018 - start*/
        $token_issued_organization_id = $this->data['primary_placement_sessions']['token_issued_organization_id'];
        $show_opencourse = $this->bookingmodel->access_to_opencourse_primary($token_issued_organization_id);
        
        if(isset($show_opencourse['0'])){
            if($show_opencourse['0']['access_to_webversion'] == 1){
                $this->data['show_open_course'] = 'show';
            }
        }
        
        //Dinesh - still working
        $purchase_result = $this->usermodel->get_user_purchashed_course_api($this->session->get('user_id'));        
        if(!empty($purchase_result)){
            $this->data['buttonEnable'] = 1;            
        }else{
            $this->data['buttonEnable'] = 0;
        }
        //WP-1202 Get Product with Practice test details 
        $user_id = $this->session->get('user_id');
        $products = $this->bookingmodel->get_products($user_id);
        foreach($products as $productkey => $product){

            $launch_url = $this->oauth->catsurl('testDeliveryUrl');
            $launch_key = $this->oauth->catsurl('testLaunchKey');
            $referrer = $this->oauth->catsurl('testReferrer');
            $user_thirparty_id = $product['user_thirparty_id'];

            /********** WP-1202 - Get and SET Practice test Launch details **********/
            // Get Practice test Launch details if CAT's as tds
            $test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'practice');


            if(isset($test_delivery_details) && count($test_delivery_details) > 0){
                foreach($test_delivery_details as $key => $test_delivery_detail){

                    $cats_launch_urls['practice_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $test_delivery_detail->test_formid, $test_delivery_detail->test_formversion, $user_thirparty_id, $test_delivery_detail->token);
                    $cats_launch_tokens['practice_test'.($key+1)] = $test_delivery_detail->token;
                    $cats_launch_btns['practice_test'.($key+1)] = ($test_delivery_detail->status == 1) ? 'disable' : 'enable';
                }
                $products[$productkey]['practice_test']['launch_urls'] = $cats_launch_urls;
                $products[$productkey]['practice_test']['launch_tokens'] = $cats_launch_tokens;
                $products[$productkey]['practice_test']['launch_btns'] = $cats_launch_btns;
            }

            $institution_timezone = $this->bookingmodel->get_institution_timezone($user_thirparty_id);

            if($institution_timezone){
                $final_test_booking_details = $this->bookingmodel->get_booking_date_time_details($user_thirparty_id);
                if(isset($final_test_booking_details) && count($final_test_booking_details) > 0){
                    $current_utc_time = @get_current_utc_details();
                    
                    $current_utc_timestamp = $current_utc_time['current_utc_timestamp'];
                    
                    if($current_utc_timestamp >= $final_test_booking_details['start_date_time'] && $current_utc_timestamp <= $final_test_booking_details['end_date_time']){
                    $products[$productkey]['final_test_section'] = 'show';
                    // Get Final test Launch details if CAT's as tds
                    $final_test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                    if(isset($final_test_delivery_details) && count($final_test_delivery_details) > 0){
                        foreach($final_test_delivery_details as $key => $final_test_delivery_detail){
                            $final_cats_launch_urls['final_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $final_test_delivery_detail->test_formid, $final_test_delivery_detail->test_formversion, $user_thirparty_id, $final_test_delivery_detail->token);
                            $final_cats_launch_tokens['final_test'.($key+1)] = $final_test_delivery_detail->token;
                            $final_cats_launch_btns['final_test'.($key+1)] = ($final_test_delivery_detail->status == 1) ? 'disable' : 'enable';
                        }
                        $products[$productkey]['final_test']['launch_urls'] = $final_cats_launch_urls;
                        $products[$productkey]['final_test']['launch_tokens'] = $final_cats_launch_tokens;
                        $products[$productkey]['final_test']['launch_btns'] = $final_cats_launch_btns;
                    }
                    
                    }
                }
            }
        }
        
        if (!empty($products)) {
            $this->data['products'] = $products;
        }
        //WC-15 - Divert learners to app version for non-desktop access
        $this->data['list_apps'] = $this->cmsmodel->applinks();
        if($this->detectDevice()){
            $this->data['is_mobile'] = $this->detectDevice('mobile_os');
        }
        $this->session->set('products',$products);
        echo view('site/booking-screen2p', $this->data);
    } 

	public function placementresults($userappid = FALSE, $token = FALSE){

        if ($this->acl_auth->logged_in() && null !== $this->request->getVar('token')) {
			
            $token = $this->request->getVar('token');
            $userappid = $this->session->get('user_app_id');
            $launchUrl = $this->oauth->catsurl('testDeliveryUrl');
            $key = $this->oauth->catsurl('testLaunchKey');
            $referrer = $this->oauth->catsurl('testReferrer');
            $learnertype = $this->session->get('learnertype');
            $learnerprodtype = $this->session->get('learnerprodtype');

            if($this->get_organization_data_by_token($token) != NULL){
                $organization_data = $this->get_organization_data_by_token($token);
            }else{
                $organization_data['type_of_token'] = '';
            }

            if($this->get_ordertype_by_token($organization_data['type_of_token']) != NULL){
                $token_type = $this->get_ordertype_by_token($organization_data['type_of_token']);
            }else{
                $token_type = '';
            }
            // data
            if($organization_data['type_of_token'] == 'speaking_test'){
                //Speaking_test Token New condition to stop XML data error issue
                return redirect()->to(site_url('site/get-the-right-level'));
                // $xml_data =  file_get_contents($launchUrl."results?key=".$key."&referrer=".$referrer."&candidateid=".$userappid."&token=".$token);
            }else{
                $xml_data =  file_get_contents($launchUrl."TestStatus?key=".$key."&referrer=".$referrer."&token=".$token);
            }
            $xml_decode_data = json_decode($xml_data);
            $user_results = (array) $xml_decode_data;

			error_log(date('[Y-m-d H:i:s e] ') . $_SERVER['HTTP_USER_AGENT'] . "- Token : ".$token." - TestStatus API Response : " . $xml_data .  PHP_EOL, 3, LOG_FILE_TDS_STATUS);

            if(isset($xml_decode_data->Status) && $xml_decode_data->Status == "OK" && ( $xml_decode_data->TestStatus == "Marked" || $xml_decode_data->TestStatus == "Marks Pending")){

                $tds_tests_detail = $this->bookingmodel->get_tds_tests_detail_by_token($token);

                if(count($tds_tests_detail) > 0){
                    if($learnertype === 'under13' && $learnerprodtype === 'cats_primary'){

                        $data = ['status' => 1, 'response_msg' => $xml_data, 'test_date' =>time()];
                        if($this->bookingmodel->update_tds_tests_detail($data,$token)){
                            $this->session->setFlashdata('messages', lang('app.language_cats_tds_under16_test_thanks_msg'));
                            $this->session->set('tds_return_msg', true);
                            return redirect()->to(site_url('site/recommended-primary-level')); 

                        }else{
                            log_message('error', "Multiple hits from tds to webportal redirection - Token - " .$token);	
                            $this->session->setFlashdata('messages', lang('app.language_cats_tds_under16_test_thanks_msg'));
                            $this->session->set('tds_return_msg', true);
                            return redirect()->to(site_url('site/recommended-primary-level')); 
                        }
                    }else{

                        $data = ['status' => 1, 'response_msg' => $xml_data, 'test_date' =>time()];
                        if($this->bookingmodel->update_tds_tests_detail($data,$token)){
                            $this->session->setFlashdata('messages', lang('app.language_cats_tds_test_thanks_msg'));
                            $this->session->set('tds_return_msg', true);
                            return redirect()->to(site_url('site/dashboard')); 
                        }else{
                            log_message('error', "Multiple hits from tds to webportal redirection - Token - " .$token);	
                            $this->session->setFlashdata('messages', lang('app.language_cats_tds_test_thanks_msg'));
                            $this->session->set('tds_return_msg', true);
                            return redirect()->to(site_url('site/dashboard')); 
                        }
                    }
                    // core are primary placement test end
                }
                // benchmarking or stepcheck end test section 
                $query = $this->db->query('SELECT * FROM placement_session WHERE user_id = "' . $userappid . '" AND type_of_token = "'.$organization_data['type_of_token'].'" AND token = "'.$organization_data['token'].'" LIMIT 1');
   
                if ($query->getNumRows() > 0) {
                    if ($organization_data['type_of_token'] === 'benchmarktest' || $organization_data['type_of_token'] === 'speaking_test' || $token_type === 'benchmarking') {
                        return redirect()->to(site_url('site/dashboard')); 
                    } else {
                        return redirect()->to(site_url('site/get-the-right-level')); 
                    }
                }
                else{

                    if($token_type === 'benchmarking' || $organization_data['type_of_token'] === 'benchmarktest' || $organization_data['type_of_token'] === 'speaking_test'){
                        $benchmark_data = array(
                            'token'                 => $organization_data['token'],
                            'user_id'               => $this->session->get('user_id'),
                            'user_app_id'           => $this->session->get('user_app_id'),
                            'datetime'              => time(),
                            'pgroup'                => 'cats_core',
                            'status'                => 0,
                            'test_driver' 			=> 'RN',
                            'response_message'		=> $xml_data
                        );

                        $check_duplicate_benchmark = $this->bookingmodel->get_benchmark_details($organization_data['token'], $this->session->get('user_id'));

                        if(!$check_duplicate_benchmark){
                            $builder = $this->db->table('benchmark_session');
                            $builder->insert($benchmark_data);

                        }else{
                          log_message('error', "Benchmark - Multiple hits from tds to webportal redirection - Token - " .$organization_data['token']);	  
                        }
                     //token update data
                    $token_updata = array(
                        'is_used' => 1,
                        'used_time' => time(),
                        'questionnaire_done' => 1,
                        'user_name' => $this->session->get('user_firstname').' '.$this->session->get('user_lastname'),
                        'user_id' => $this->session->get('user_id'),
                    );

                    if ($organization_data['token'] != '0') {
                        $builder = $this->db->table('tokens');
                        $builder->where('token', $organization_data['token']);
                        $builder->update($token_updata);
                    }
                    $this->session->set('recent_type', 'StepCheck');
                    return redirect()->to(site_url('site/dashboard')); 
                }
                // ending 
                else {
                    return redirect()->to(site_url('site/get-the-right-level')); 
                }
            }
        }
        else {
            //Need to remove once speaking trail test gets proper response 
            if($organization_data['type_of_token'] === 'speaking_test'){
                $this->session->setFlashdata('errors', 'XML error occurred in the test.');
                return redirect()->to(site_url('site/get-the-right-level')); 
            }else{
                $this->session->setFlashdata('errors', $xml_decode_data->Message);
                return redirect()->to(site_url('site/get-the-right-level')); 
            }				
        }
    }
    else{
        $token = $this->request->getVar('token');
        $userappid = $this->session->get('user_app_id');
        error_log(date('[Y-m-d H:i:s e] ') . 'Session is timeout' . "- Token : ".$token. " - Userappid : ".$userappid.  PHP_EOL, 3, LOG_FILE_TDS_STATUS);
        $this->session->setFlashdata('errors', 'Your session is timeout, Please login again with the same token and give end test.');
        return redirect()->to(site_url('site/is-cat-available-for-me')); 
        }
    }

    public function check_token_already_exist_foruser($token = FALSE,$user_id = FALSE){
		if($token && $user_id){
            $builder = $this->db->table('tokens');
			$builder->select('tokens.user_id');
			$builder->where('token', $token);
			$query = $builder->get();
			$result = $query->getRow();
			if($query->getNumRows() > 0){
				$result_user_id = $result->user_id;
				if($result_user_id == 0){
					$data = ['user_id' => $user_id];
                    $builder = $this->db->table('tokens');
					$builder->where('token', $token);
					$builder->update($data);
					if($this->db->affectedRows() > 0){
						return TRUE;
					}else{
						return FALSE;
					}
				}else{
					if($user_id != $result_user_id){
						return FALSE;
					}else{
						return TRUE;
					}
				}
			}
		}
	}

    //validate each inputs of registrationm form
    public function  valdate_individually_data()
    {
        switch ($this->request->getPost('field')) {
            case 'reg_firstname':
                $rules =[
                    'firstname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                        'rules'  => 'required|max_length[100]|serbia_username_check',
                         'errors' => [
                             'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                         ],
                        ]
                    ];

                break;
            case 'reg_secondname':
                $rules =[
                    'secondname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                        'rules'  => 'required|max_length[100]|serbia_username_check',
                         'errors' => [
                             'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                         ],
                        ]
                    ];

                break;
            case 'reg_email':
                $rules =[
                    'email' => [
                        'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                        'rules'  => 'required|max_length[100]|isemail_check|is_unique[users.email]|valid_email',
                         'errors' => [
                             'isemail_check' => lang('app.form_validation_valid_email'),
                         ],
                        ]
                    ];
                break;
            case 'reg_confirm_email':

                $rules =[
                    'email' => [
                        'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                        'rules'  => 'required|max_length[100]|isemail_check|is_unique[users.email]|valid_email',
                         'errors' => [
                             'isemail_check' => lang('app.form_validation_valid_email'),
                         ]
                        ],
                        'confirm_email' => [
                            'label'  => lang('app.language_site_booking_screen2_label_confirm_email_address'),
                            'rules'  => 'required|max_length[100]|isemail_check|matches[email]|valid_email',
                             'errors' => [
                                 'isemail_check' => lang('app.form_validation_valid_email'),
                             ]
                            ]
                    ];
                break;
            case 'reg_password':
                $rules =[
                    'password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_password'),
                        'rules'  => 'required|min_length[8]|new_password_check',
                         'errors' => [
                             'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                         ],
                        ]
                    ];
                break;
            case 'reg_confirm_password':

                $rules =[
                    'password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_password'),
                        'rules'  => 'required|min_length[8]|new_password_check',
                         'errors' => [
                             'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                         ]
                        ],
                        'confirm_password' => [
                            'label'  => lang('app.language_site_booking_screen2_label_confirm_password'),
                            'rules'  => 'required|min_length[8]|new_password_check|matches[password]',
                             'errors' => [
                                 'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                             ]
                            ],
                    ];

                break;
            default:
            $rules =[
                ];
                break;
        }
            
        if (!$this->validate($rules)) {
            $response['success'] = 0;
            $errors = array(
                'firstname' => $this->validation->showError('firstname'),
                'secondname' => $this->validation->showError('secondname'),
                'email' => $this->validation->showError('email'),
                'confirm_email' => $this->validation->showError('confirm_email'),
                'password' => $this->validation->showError('password'),
                'confirm_password' => $this->validation->showError('confirm_password')
            );
            $response['errors'] = $errors;
            echo json_encode($response);
            die;
        } else {
            $response['success'] = 1;
            echo json_encode($response);
            die;
        }
    }

    //WC-15 - Divert learners to app version for non-desktop access
    public function play_store_link($id = false){ 
        if($id != false){
            $builder = $this->db->table('page_apps');
            $builder->select('app_link');
           
            $builder->where('page_apps.id', $id);
            $builder->limit(1);
            $query = $builder->get();
            $app_link = $query->getResultArray();
            $this->data['playstore'] = array(
                'url' => $app_link[0]['app_link']
            );
            echo view('site/playstore_popup', $this->data);
        }else{
            echo 'Not a valid device';
        }   
    }

    public function onchange_products() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }		
		$prodid = $this->request->getPost('productid');
		if($prodid) {
			
			/*** For Under 13 users Button enable or disable starts ***/
			$learnerType = $this->session->get('learnertype');
			if($learnerType == 'under13'){
				$userId = $this->session->get('user_id');
				$buttonEnable = $this->checkButtonEnableUnder13($prodid,$userId);
				if($buttonEnable == 'disable'){
					$this->data['buttonEnable'] = 0;
				}elseif($buttonEnable == 'enable'){
					$this->data['buttonEnable'] = 1;
				}
				$this->data['learnerType'] = 'under13';
			}
			/*** Under 13 ends ***/
			$user_products = $this->bookingmodel->get_bookings3($prodid);
			$count = 0;
			$resultdata = array();
			foreach($user_products as $booking) {
			    
                if((!empty($booking['thirdparty_id'])) && ($booking['thirdparty_id'] > 0)) {
                    
                    $launch_url = $this->oauth->catsurl('testDeliveryUrl');
                    $launch_key = $this->oauth->catsurl('testLaunchKey');
                    $referrer = $this->oauth->catsurl('testReferrer');
                    $user_thirparty_id = $booking['thirdparty_id'];

                    /********** WP-1202 - Get and SET Practice test Launch details **********/
                    // Get Practice test Launch details if CAT's as tds
                    $test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'practice');
                    if(isset($test_delivery_details) && count($test_delivery_details) > 0){
                        foreach($test_delivery_details as $key => $test_delivery_detail){
                            $cats_launch_urls['practice_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $test_delivery_detail->test_formid, $test_delivery_detail->test_formversion, $user_thirparty_id, $test_delivery_detail->token);
                            $cats_launch_tokens['practice_test'.($key+1)] = $test_delivery_detail->token;
                            $cats_launch_btns['practice_test'.($key+1)] = ($test_delivery_detail->status == 1) ? 'disable' : 'enable';
                        }
                        $booking['practice_test']['launch_urls'] = $cats_launch_urls;
                        $booking['practice_test']['launch_tokens'] = $cats_launch_tokens;
                        $booking['practice_test']['launch_btns'] = $cats_launch_btns;

                        //WP-1308 Starts
                        $tds_test_query = $this->db->query('SELECT * FROM `tds_tests` WHERE candidate_id = "' . $user_thirparty_id . '" AND test_type = "practice" ');

                        if ($tds_test_query->getNumRows() > 0) {
                            $booking['practice_test']['practice_test_count'] = $tds_test_query->getNumRows();
                        }
                        //WP-1308 Ends
                    }
                    
                    // Tds practics test result processing
                        $practice_tds_results = $this->bookingmodel->tds_practice_detail($user_thirparty_id, 'practice');
                        if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                            foreach($practice_tds_results as $key => $practice_tds_result){
                                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                    $tds_practice['practice_test1'] = $practice_tds_result;
                                }else{
                                    $tds_practice['practice_test2'] = $practice_tds_result;
                                }
                            }
                            $booking['tds_practice_results'] = $tds_practice;
                        }
                    
                    // Get Practice test Launch details if CollegePre as tds 
                    $practice_tests = $this->bookingmodel->pract_test($user_thirparty_id);
                    if(($practice_tests != FALSE) && count($practice_tests) > 0){
                        foreach($practice_tests as $key => $practice_test){
                    
                            $cp_launch_details['practice_test'.($key+1)] = array('test_number' => $practice_test['test_number'], 'candidate_number' => $practice_test['candidate_number'],'final_result' => $practice_test['final_practice_thirdparty_id'],'practisce_status' => $practice_test['practise_result_status']);
                
                        }

                        $booking['practice_test']['launch_details'] = $cp_launch_details;
                    }
                    
                    //WP-1301 - Final test arrangements tab section for unsupervised learner -- On change Dash
                    if($booking['booking_id'] > 0 && $booking['booking_test_delivary_id'] != ''){
                        $supervised = $this->bookingmodel->get_token_status_by_thirdpartyid($user_thirparty_id);
                        if(!$supervised){
                            $final_test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                            if(isset($final_test_delivery_details) && count($final_test_delivery_details) > 0){
                                foreach($final_test_delivery_details as $key => $final_test_delivery_detail){
                                    
                                    $final_cats_launch_urls['final_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $final_test_delivery_detail->test_formid, $final_test_delivery_detail->test_formversion, $user_thirparty_id, $final_test_delivery_detail->token);
                                    $final_cats_launch_tokens['final_test'.($key+1)] = $final_test_delivery_detail->token;
                                    $final_cats_launch_btns['final_test'.($key+1)] = ($final_test_delivery_detail->status == 1) ? 'disable' : 'enable';
                                }
                                $booking['final_test']['launch_urls'] = $final_cats_launch_urls;
                                $booking['final_test']['launch_tokens'] = $final_cats_launch_tokens;
                                $booking['final_test']['launch_btns'] = $final_cats_launch_btns;
                                $booking['final_test']['is_supervised'] = $supervised;
                            }
                        }
                    }
                    $booking['token_status'] = $this->bookingmodel->get_token_status_by_thirdpartyid($user_thirparty_id);
                    
                    /********** WP-1202 - Get and SET Final test Launch details **********/
                    $institution_timezone = $this->bookingmodel->get_institution_timezone($user_thirparty_id);
                    if($institution_timezone){
                        
                        $final_test_booking_details = $this->bookingmodel->get_booking_date_time_details($user_thirparty_id);
                        if(isset($final_test_booking_details) && count($final_test_booking_details) > 0){
                          
                            $current_utc_time = @get_current_utc_details();
                            $current_utc_timestamp = $current_utc_time['current_utc_timestamp'];
                            
                            if($current_utc_timestamp >= $final_test_booking_details['start_date_time'] && $current_utc_timestamp <= $final_test_booking_details['end_date_time']){
                                $booking['final_test_section'] = 'show';
                                // Get Final test Launch details if CAT's as tds
                                $final_test_delivery_details = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                                if(isset($final_test_delivery_details) && count($final_test_delivery_details) > 0){
                                    foreach($final_test_delivery_details as $key => $final_test_delivery_detail){
                                        $final_cats_launch_urls['final_test'.($key+1)] = $this->get_tds_launch_url($launch_url, $launch_key, $referrer, $final_test_delivery_detail->test_formid, $final_test_delivery_detail->test_formversion, $user_thirparty_id, $final_test_delivery_detail->token);
                                        $final_cats_launch_tokens['final_test'.($key+1)] = $final_test_delivery_detail->token;
                                        $final_cats_launch_btns['final_test'.($key+1)] = ($final_test_delivery_detail->status == 1) ? 'disable' : 'enable';
                                    }
                                    $booking['final_test']['launch_urls'] = $final_cats_launch_urls;
                                    $booking['final_test']['launch_tokens'] = $final_cats_launch_tokens;
                                    $booking['final_test']['launch_btns'] = $final_cats_launch_btns;
                                }                               
                            }
                        }
                    }
                    
                }
				
				if($booking['booking_test_delivary_id'] > 0) {		
					$third_party_id = $booking['booking_test_delivary_id'];
					$user_course = $this->usermodel->get_course_type_by_thirdparty_id($third_party_id);
					$course_type = $user_course[0]->course_type;
					$delivery_type_dash = $this->get_delivery_type_by_thirdparty_id($third_party_id);

					//$delivery_option_dash = $delivery_type_dash[0]['tds_option'];
					$delivery_option_dash = ($delivery_type_dash) ? $delivery_type_dash['tds_option'] : "catstds";
					if($course_type == 'Higher'){
						$this->data['course_type'] = 'Higher';
						$higherresults = $this->bookingmodel->view_higher_result($third_party_id);
					}else{						
						$results = $this->view_final_result($third_party_id,$delivery_option_dash);
					}
					if(!empty($results)) {
						$booking['results']['candidate_id'] = $results['candidate_id'];
						$booking['results']['third_partyid'] = $third_party_id;
					}
				}
				$newarray[] = $booking;						
			}						
			if(!empty($results)){
					$this->data['results'][$prodid]= $results;
			}if(!empty($higherresults)){
					$this->data['higherresults']= $higherresults;
			}               
                             
            //START DASH LOAD NEXT STEP SCREEN
            $get_latest_product = $this->bookingmodel->get_latest_product_details_dash($newarray[0]['user_id']);
            /*tds Final result pending or test not taken check*/
                $user_thirparty_id = $get_latest_product[0]->thirdparty_id;
                //for final test
                $final_link = $this->bookingmodel->get_tds_tests_detail($user_thirparty_id, 'final');
                if(isset($final_link) && !empty($final_link)){
                    $this->data['tds_final_result_status'] = $final_link['0']->status;
                }
            /*tds Final result pending or test not taken check - ENDS*/
            $this->data['show_book_next']= 'hide';
            if($get_latest_product[0]->thirdparty_id === $newarray[0]['thirdparty_id']){
                $user_course_dash = $this->usermodel->get_course_type_by_thirdparty_id($get_latest_product[0]->thirdparty_id);
                $course_type_dash = $user_course_dash[0]->course_type;
                $delivery_type_dash = $this->get_delivery_type_by_thirdparty_id($newarray[0]['thirdparty_id']);
                //$delivery_option_dash = $delivery_type_dash[0]['tds_option'];
                $delivery_option_dash = ($delivery_type_dash) ? $delivery_type_dash['tds_option'] : "catstds";
                if($course_type_dash == "Higher"){
                   $next_cat = $this->bookingmodel->view_higher_result($newarray[0]['thirdparty_id'], false); 
                }else{
                    $next_cat = $this->view_final_result($newarray[0]['thirdparty_id'],$delivery_option_dash);
                }
                if(!empty($next_cat)) {
                    if($course_type_dash == "Higher"){
                        // next step block indicator
                        if($delivery_option_dash != NULL && $delivery_option_dash == 'catstds'){
                            $builder = $this->db->table('booking');
                            $builder->select('booking.id, booking.user_id, booking.test_delivary_id, booking.product_id, tds_results.candidate_id as thirdparty_id,tds_results.token, users.user_app_id');
                            $builder->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id');
                            $builder->join('users', 'users.id = booking.user_id', 'left');		
                            $builder->where('booking.user_id', $this->session->get('user_id'));
                            $builder->orderBy("booking.product_id", "desc"); 
                        }else{
                            $builder = $this->db->table('booking');
                            $builder->select('booking.id, booking.user_id, booking.test_delivary_id, booking.product_id, collegepre_higher_results.thirdparty_id, users.user_app_id');
                            // $this->db->from('booking');
                            $builder->join('collegepre_higher_results', 'booking.test_delivary_id = collegepre_higher_results.thirdparty_id');
                            $builder->join('users', 'users.id = booking.user_id', 'left');		
                            $builder->where('booking.user_id', $this->session->get('user_id'));
                            $builder->orderBy("booking.product_id", "desc");
                        }
					
                        $query = $builder->get();
                        $booked_result_products = $query->getResultArray();
                        if(!empty($booked_result_products)) {
                                foreach ($booked_result_products as $booked_product) {
                                        $results_available[$booked_product['product_id']] = $booked_product['thirdparty_id'];
                                }
                        }
                        if(!empty($results_available)) {
                                $this->data['result_products'] = $results_available;
                                //Commented for WP-1060
                        }
                        if ($get_latest_product[0]->product_id == 12) {
                            $this->data['next_level_to_purchase'] = 13;
                        } else {
                            $this->data['next_level_to_purchase'] = $get_latest_product[0]->product_id + 1;
                        }

                        if (@$higherresults[0]->section_one != NULL) {
                            $this->data['show_book_next'] = 'show';
                            $this->data['highest_level_purchased'] = $get_latest_product[0]->product_id;
                        }elseif($higherresults[0]->processed_data != NULL){
                            $this->data['show_book_next'] = 'show';
                            $this->data['highest_level_purchased'] = $get_latest_product[0]->product_id;
                        }
                    }else{
                        $this->data['show_book_next']= 'show';
                        if($delivery_option_dash == 'catstds'){
                            $next_level_proccessed = $this->get_next_core_level($get_latest_product[0]->level,False, False,$next_cat['candidate_id']);
                        }else{
                            $next_level_proccessed = $this->get_next_core_level($get_latest_product[0]->level, $next_cat['section_one'], $next_cat['section_two'], $next_cat['thirdparty_id']);
                        }
                        $this->data['highest_level_purchased'] = $get_latest_product[0]->product_id; //last product purchased
                        $this->data['next_level_to_purchase'] = $next_level_proccessed['product_id'];
                    }
                    	
                }else{
                    $this->data['show_book_next']= 'hide';	
                }
                
                $this->data['productEligible'] = $this->usermodel->get_institute_productEligible_by_user($this->session->get('user_id'));
            } //END DASH LOAD NEXT STEP SCREEN
			
            $this->data['list_apps'] = $this->cmsmodel->applinks();		  
			$this->data['products'] = $newarray;

            //WP-1142 - To get institution timezone
            if($newarray[0]['thirdparty_id']){
                $institution_timezone = $this->bookingmodel->get_institution_timezone($newarray[0]['thirdparty_id']);
                $this->data['institution_timezone'] = $institution_timezone;
                
            }
			// WP-1301 Starts
            $this->data['tokenType'] = '';
			$prod_tokentype	= $this->bookingmodel->get_tokentype_by_thirdpartyid($newarray[0]['thirdparty_id']);
			if($prod_tokentype){
			$this->data['tokenType'] = 	$prod_tokentype;
			}
			$all_products = $this->productmodel->product_list();
			$this->data['all_products'] = $all_products;
            // WP-1301 ends
            // WC-15 - Divert learners to app version for non-desktop access
            if ($this->detectDevice()) :
                $this->data['is_mobile'] = $this->detectDevice('mobile_os');
            endif;
            return  view('site/dash', $this->data);
		}		
	}

    public function onchange_speaking_products() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');   
        }

	    $speakingid = $this->request->getPost('speakingid');
	    if($speakingid) {
            $builder = $this->db->table('placement_session');
	        $builder->select('id, user_id, token, datetime,  from_unixtime(datetime, "%d-%m-%Y") as formatdate' );
	        $builder->where('user_id', $this->session->get('user_app_id'));
	        $builder->where('id', $speakingid);
	        $builder->order_by('datetime', "desc");
	        $query = $builder->get();
	        $speaking = $query->getResultArray();
	        if(!empty($speaking)){
	            $this->data['speakingtests'] = $speaking;
	            return  $this->load->view('site/dash_speaking', $this->data);
	        }
	    }
	}

    public function onchange_benchmark_products() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');   
        }
		$benchmarkid = $this->request->getPost('benchmarkid');
		if($benchmarkid) {
            $builder = $this->db->table('benchmark_session');
			$builder->select('benchmark_session.id, benchmark_session.user_id, benchmark_session.token, benchmark_session.user_app_id, benchmark_session.benchmark_cefr_level, benchmark_session.datetime, benchmark_session.test_driver, from_unixtime(benchmark_session.datetime, "%d-%m-%Y") as formatdate,tokens.type_of_token' );
			$builder->join('tokens','tokens.token = benchmark_session.token');
			$builder->where('benchmark_session.user_id', $this->session->get('user_id'));
			$builder->where('benchmark_session.id', $benchmarkid);
			$builder->groupBy('benchmark_session.datetime', "desc");
			$query = $builder->get();
			$benchmark = $query->getResultArray();
							
			if(!empty($benchmark)){
				$this->data['benchmarks'] = $benchmark;
                return  view('site/dashboard_benchmark', $this->data);
			}													
		}
	}
    /**
     * AJAX Function to set current practice test token in session to check test status  
     */
    function set_tdstoken_sessions(){
        if($this->request->getPost()){
            $tds_token = base64_decode($this->request->getPost('latest_tds_token'));
            $latest_tds_token = $this->session->get('latest_tds_token');
            if(isset($latest_tds_token)){
                $this->session->remove('latest_tds_token');
                $this->session->set('latest_tds_token', $tds_token);
            }else{
                $this->session->set('latest_tds_token', $tds_token);
            }
        }
    }

    public function higher_certificate($candidate_id = FALSE, $token = False, $display_view = FALSE) {
        $result_tds_higher = "";
        if($candidate_id != FALSE  && $token != False && $token != "NULL"){
            //tds higher cerificate view
            $builder = $this->db->table('tds_results');
            $builder->select('users.name as candidate_name,tds_results.processed_data,tds_results.token,tds_results.candidate_id,DATE_FORMAT(tds_results.result_date,"%d %M %Y") as result_date,products.name,events.start_date_time,events.end_date_time,tokens.is_supervised');
            $builder->join('booking', 'tds_results.candidate_id = booking.test_delivary_id');
            $builder->join('tokens', 'tds_results.token = tokens.token');
            $builder->join('events', 'booking.event_id = events.id','left');
            $builder->join('products', 'booking.product_id = products.id');
            $builder->join('users', 'booking.user_id = users.id');
            $builder->where('tds_results.candidate_id', $candidate_id);
            $result = $builder->get();
            $higher_results =  $result_tds_higher = $result->getRowArray();
        }else{
            //collegepre higher cerificate view
            $builder = $this->db->table('collegepre_higher_results');
            $builder->select('collegepre_higher_results.candidate_name,collegepre_higher_results.candidate_id,DATE_FORMAT(collegepre_higher_results.result_date,"%d %M %Y") as result_date,collegepre_higher_results.thirdparty_id,products.name,events.start_date_time,events.end_date_time');
            $builder->join('booking', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id');
            $builder->join('events', 'booking.event_id = events.id','left');
            $builder->join('products', 'booking.product_id = products.id');
            $builder->where('collegepre_higher_results.candidate_id', $candidate_id);
            $result = $builder->get();
            $higher_results = $result_users_higher = $result->getRowArray();
            if ($result->getNumRows() > 0) {
                $query = $this->db->query('SELECT * FROM  booking WHERE test_delivary_id = "' . $result_users_higher['thirdparty_id'] . '" LIMIT 1');
                $results_higher = $query->getRowArray();
            }
        }
        if($result_tds_higher){
            $data = $result_tds_higher['processed_data'];
            $id = $higher_results['candidate_id'];
            $type = 'tds';
            $token = $higher_results['token'];
            $is_supervised = ($higher_results['is_supervised'])? "Supervised": "Unsupervised";
        }else{
            $data = $results_higher['logit_values'];
            $id = $higher_results['thirdparty_id'];
            $type = 'collegepre';
            $token = '';
            $is_supervised = "Supervised";
        }
        $json_to_array_higher = json_decode($data, true);
        $content_array_level_higher = $this->process_higher_skill_content($json_to_array_higher);
        $tz_to = $this->bookingmodel->get_institution_timezone($id);
        if(!empty($higher_results['start_date_time'])){
           $institution_zone_values = @get_institution_zone_from_utc($tz_to, $higher_results['start_date_time'], $higher_results['end_date_time']);
           $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
        }else{
            $event_date = NULL;
        }
        $this->data['higher_results_view'] = array(
            'candidate_id' => $higher_results['candidate_id'],
            'product_name'=> $higher_results['name'],
            'token'=> $token,
            'id' => $id,
            'user_name' => ucfirst($higher_results['candidate_name']),
            'result_date' => $higher_results['result_date'],
            'event_date' => $event_date,
            'higher_certificate_data' => $json_to_array_higher,
            'lang_content_level_higher' => $content_array_level_higher,
            'display' => $display_view,
            'is_supervised' => $is_supervised,
            'type' => $type,
        );
            echo view('site/highercertificate-view_site',$this->data);
    }

    public function process_higher_skill_content($results_higher_level) {
        if (!empty($results_higher_level)) {
            foreach ($results_higher_level as $key => $value){
                $lang_name_higher = $key . '_' .strtolower(substr($value['level'], 0, 2));
                $lang_value[$key][] = lang("app.language_school_higher_certificate_level_$lang_name_higher");
            }
            return $lang_value;
        } else {
            return false;
        }
    }

    // WP-1191 - 4 skills benchmarking results statement -certificate shown codes - ##ends##
    public function higher_certificate_pdf($candidate_id = False, $token = False) {
        $values_higher_pdf = $this->process_results_higher($candidate_id,$token);
        //QR generation - WP-1221
        $qr_code_url = $google_url = '';
        $qrcode_params = @generateQRCodePath('site', 'higher', $candidate_id, $token, false);
        if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
            $qrcode_higher = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
            $qr_result_higher = json_decode($qrcode_higher);
            $qr_code_url = $qr_result_higher->qrcode_abs;
            $google_url = $qr_result_higher->url;
        }
       
        $this->data['pdf_download_higher'] = array(
            'data' => $values_higher_pdf,
            'qr_code_url' => $qr_code_url,
            'google_url' => $google_url,
        );
        @generatehigherResultsPDF($this->data['pdf_download_higher']);
    }

    public function process_results_higher($candidate_id = false, $token = False) {
        $result_tds_higher = "";
        if($candidate_id && $token){
            //tds higher pdf
            $builder = $this->db->table('tds_results');
                $builder->select('users.name as candidate_name,tds_results.processed_data,tds_results.token,tds_results.candidate_id,DATE_FORMAT(tds_results.result_date,"%d %M %Y") as result_date,products.name,events.start_date_time,events.end_date_time,tokens.is_supervised');
                $builder->join('booking', 'tds_results.candidate_id = booking.test_delivary_id');
                $builder->join('tokens', 'tds_results.token = tokens.token');
                $builder->join('events', 'booking.event_id = events.id','left');
                $builder->join('products', 'booking.product_id = products.id');
                $builder->join('users', 'booking.user_id = users.id');
                $builder->where('tds_results.candidate_id', $candidate_id);
            $result =  $builder->get();
            $higher_results =  $result_tds_higher = $result->getRowArray();
        }else{
            //collegepre higher pdf
            $builder = $this->db->table('collegepre_higher_results');
            $builder->select('collegepre_higher_results.candidate_name,collegepre_higher_results.candidate_id,DATE_FORMAT(collegepre_higher_results.result_date,"%d %M %Y") as result_date,collegepre_higher_results.thirdparty_id,products.name,events.start_date_time,events.end_date_time');
            $builder->join('booking', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id');
            $builder->join('events', 'booking.event_id = events.id','left');
            $builder->join('products', 'booking.product_id = products.id');
            $builder->where('collegepre_higher_results.candidate_id', $candidate_id);
            $result = $builder->get();
            $higher_results = $result_users_higher = $result->getRowArray();
            if ($result->getNumRows() > 0) {
                $query = $this->db->query('SELECT * FROM  booking WHERE test_delivary_id = "' . $result_users_higher['thirdparty_id'] . '" LIMIT 1');
                $results_higher = $query->getRowArray();
            }
        }
        if($result_tds_higher){
            $data = $result_tds_higher['processed_data'];
            $id = $higher_results['candidate_id'];
            $type = 'tds';
            $token = $higher_results['token'];
            $is_supervised = ($higher_results['is_supervised'])? "Supervised": "Unsupervised";
        }else{
            $data = $results_higher['logit_values'];
            $id = $higher_results['thirdparty_id'];
            $type = 'collegepre';
            $token = '';
            $is_supervised = "Supervised";
        }

        $json_to_array_higher = json_decode($data, true);
        $content_array_level_higher = $this->process_higher_skill_content($json_to_array_higher);
        $tz_to = $this->bookingmodel->get_institution_timezone($id);
        if(!empty($higher_results['start_date_time'])){
            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $higher_results['start_date_time'], $higher_results['end_date_time']);
            $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
        }else{
            $event_date = NULL;
        }
        $this->data['pdf_higher_results'] = array(
            'candidate_id' => $higher_results['candidate_id'],
            'product_name'=> $higher_results['name'],
            'token'=> $token,
            'id' => $id,
            'user_name' => ucfirst($higher_results['candidate_name']),
            'result_date' => $higher_results['result_date'],
            'event_date' => $event_date,
            'higher_certificate_data' => $json_to_array_higher,
            'lang_content_level_higher' => $content_array_level_higher,
            'is_supervised' => $is_supervised,
            'type' => $type,
        );
        return $this->data['pdf_higher_results'];
    }

//generate practice test results
        public function gen_practicetest_result($test_number = false, $thirdPartyId = false )
        {
     
           if($thirdPartyId != false && $test_number != false){
                $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE session_number = "' .  $test_number . '"  AND thirdparty_id = "' .  $thirdPartyId . '" LIMIT 1');
                if ($query->getNumRows() > 0) {
                   $results  = $query->getRowArray();
                   $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                   //user and product info
                   $user_app_id = substr($thirdPartyId,0,10);
                   $course_id   = substr($thirdPartyId,10,2);
                   $attempt_no  =   substr($thirdPartyId,12,2);
                   //get part setup
                   $part_setups  = _part_setup($course_id);
                   $green_or_orange = 0;
                   foreach($part_setups as $part):
                      $label  = preg_replace("/\d+$/","",$part['part']); 
                      $score  = number_format(array_sum(array_slice($score_sections, $part['start']-1,$part['length'],true))/$part['count'], 2);
                      if($score >= 3){
                          $green_or_orange =  $green_or_orange + 1;
                      }
                      $label_score_arr[] = array( $label, $score, ($score >= 3 ) ? '1' : '0');
                    endforeach;
                    $data['green_or_orange']   =  $green_or_orange;
                    $data['results'] = $label_score_arr;
                    echo view('site/load_practice_test_results',$data);
                }else{
                   echo 'ThirdParty ID /Test number not found!';
                }
            }
        }

    public function keepalive(){
        $response = array();
        $response['Status'] = 'OK';
        echo json_encode($response); die;
    }

}