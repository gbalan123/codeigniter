<?php

namespace App\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

use DateTimeZone;
use DateTime;
use DateInterval;
use App\Libraries\Acl_auth;
use Config\MY_Lang;
use Config\Oauth;
use App\Models\School\Schoolmodel;
use App\Models\Admin\Placementmodel;
use App\Models\Admin\Cmsmodel;
use App\Models\Teacher\Teachermodel;
use App\Models\Usermodel;
use App\Models\School\Eventmodel;
use App\Models\School\Venuemodel;
use App\Models\Site\Bookingmodel;
use App\Libraries\Ciqrcode;
use App\Models\Admin\Emailtemplatemodel;
use App\Models\Admin\Productmodel;


/**
 * 
 * @property Ciqrcode $ciqrcode
 * 
 */

class School extends BaseController
{

    function __construct() {

        $this->acl_auth = new Acl_auth();
        $this->pager = service('pager'); 
        $this->acl_auth = new Acl_auth();
        $this->oauth = new \Config\Oauth();    
        $this->lang = new \Config\MY_Lang();  
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->validation =  \Config\Services::validation();
        $this->email = \Config\Services::email();
        $this->schoolmodel = new Schoolmodel();
        $this->eventmodel = new Eventmodel();
        $this->cmsmodel = new Cmsmodel();
        $this->bookingmodel = new Bookingmodel();
        $this->institutionTierId = $this->schoolmodel->get_user_tiers();
        $this->teachermodel = new Teachermodel();
        $this->usermodel = new Usermodel();
        $this->venuemodel = new Venuemodel();
        $this->emailtemplatemodel = new Emailtemplatemodel();
        $this->passwordhash = new \Config\PasswordHash(); 
        $this->Ciqrcode = new Ciqrcode();
        $this->encrypter = \Config\Services::encrypter();
        $this->Placement_model = new Placementmodel();
        $this->productmodel = new Productmodel();
        $this->session = \Config\Services::session();

        define("EVENT_END_TIME_ADD_HOUR", "+4 hours");
        helper('yellowfin');
        helper('qrcodepath');
        helper('efs_path_helper');
        helper('core_certificate_language_helper');
        helper('corepdf_extended_helper');
        helper('corepdf_helper');
        helper('parts_helper');
        helper('higherpdf_helper');
        helper('downtime_helper');
        helper('percentage_helper');
        helper('benchmarkpdf_helper');
        helper('primarypdf_helper');
        helper('cms');
        helper('csv_helper');
        helper('zendesk');
        helper('sendinblue');
        $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
        $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');

        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            $controller = explode("/",$url['home_page_url']);
            if($controller[0] == "school"){
                // only allow users with 'learner' role to access all methods in this controller
                $this->acl_auth->restrict_access('school');
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

        //yellowfin access
        $this->yellowfin_access = $this->oauth->catsurl('yellowfin_access');
        if(null !== $this->session->get('user_id')){
            $organisation_id = $this->session->get('user_id');
            $this->data['product_eligiblity'] = $this->Placement_model->get_product_eligiblity($organisation_id);
            $group_ids  = $this->data['product_eligiblity'];
            $this->product_eligiblity  = $this->data['product_eligiblity'];
        }
        $efsfilepath = new \Config\Efsfilepath();
        $this->efsfilepath = $efsfilepath->get_Efs_path();
        $this->efs_charts_results_path = $this->efsfilepath->efs_charts_results;

                
        //WP-1391 zendesk user add if not
        if(isset($this->zendesk_access) && $this->zendesk_access == 1){
            $user_id = (null !== $this->session->get('logged_tier1_userid') && $this->session->get('selected_tierid') != '') ? $this->session->get('logged_tier1_userid') : $this->session->get('user_id');
            $zendesk_wp_user = @get_zendesk_user_list($user_id);
            $is_active = @zendesk_user_is_active($user_id);
            if($zendesk_wp_user == false && isset($is_active) && $is_active['status'] == 1){
                @zendesk_profile_update($user_id,"School Login");
            }
        }
    }

    public function encrypt_password() {

        $tds_test_query = $this->db->query('SELECT * FROM password_tbl');
        $result = $tds_test_query->getResultArray();

        echo "<pre>";
        foreach($result  as $res){

            if(!empty($res['decrypt_password'])){
                $enc_pass = base64_encode($this->encrypter->encrypt($res['decrypt_password']));

                echo "<br>";
                echo " password visible";
                print_r($res['decrypt_password']);
                echo "<br>";
                echo "enc password";
                print_r($enc_pass);

                $data = array(
                    'password_visible' => $enc_pass
                );

                $builder = $this->db->table('users');
                $builder->where('id', $res['user_id']);
                $builder->update($data);
                

            }
        }
        exit;
    }


    public function set_session_tab() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
        if ($this->request->getPost('tab_active') == 'tab_orders') {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
			$this->session->remove('tab_results', TRUE);
			//$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_orders', TRUE);
        } elseif ($this->request->getPost('tab_active') == 'tab_teachers') {
            // echo 'tabteacher <br />';
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
            $this->session->remove('tab_results', TRUE);
            //$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_teachers', TRUE);
        } elseif ($this->request->getPost('tab_active') == 'tab_u13entries') {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
            $this->session->remove('tab_results', TRUE);
            //$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_u13entries', TRUE);
        } elseif ($this->request->getPost('tab_active') == 'tab_events') {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_venues', TRUE);
            $this->session->remove('tab_results', TRUE);
            //$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_events', TRUE);
            
        }
        elseif ($this->request->getPost('tab_active') == 'tab_venues') {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_results', TRUE);
            //$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_venues', TRUE);
        }
		elseif ($this->request->getPost('tab_active') == 'tab_results') {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_reports', TRUE);
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
            //$this->session->remove('tab_reset_tests', TRUE);
            $this->session->set('tab_results', TRUE);
        }
        else {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            if ($this->request->getPost('tab_active') == 'tab_reports') {
                $this->session->remove('tab_teachers', TRUE);
                $this->session->remove('tab_distributors', TRUE);
                $this->session->remove('tab_venues', TRUE);
                $this->session->remove('tab_events', TRUE);
                $this->session->remove('tab_results', TRUE);
                //$this->session->remove('tab_reset_tests', TRUE);
                $this->session->set('tab_reports', TRUE);
            } else {
                $this->session->set('tab_orders', TRUE);
                $this->session->remove('tab_reports', TRUE);
                $this->session->remove('tab_teachers', TRUE);
                $this->session->remove('tab_venues', TRUE);
                $this->session->remove('tab_events', TRUE);
                $this->session->remove('tab_results', TRUE);
                //$this->session->remove('tab_reset_tests', TRUE);
                $this->session->remove('tab_distributors', TRUE);
            }
        }
        $result = array('success' => '1', 'active' => $this->request->getPost('tab_active'),'segment'=> $this->request->getPost('segment'), 'order_string_query'=> $this->request->getPost('order_string_query'));
        echo json_encode($result);
    }

    function generate_random_string($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index() {
        return redirect()->to('school/dashboard');   
    }

    public function dashboard() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');   
        }

		/** Get Order types from TDS category **/
		$data['order_types'] = $this->get_tds_order_types();
        //WP-1354 - Search for a learner

		$order_search_item = (isset($_GET['order_list_search']) && $_GET['order_list_search'] != '') ? $_GET['order_list_search'] : '';
        
        if(!empty($order_search_item) || $order_search_item != ''){
            $srch_data = $this->search_order_list();
			$data['search_list_o16'] = $srch_data ;
			$data['search_order_pager'] =  $srch_data['pager'];
        }else{
			//WP-1354 -  - Ends
            $orders_data = $this->list_orders();
        }

		if (!empty($orders_data)) {
            $data['orders'] = $orders_data['results'];
            $data['orders_data_pager'] = $orders_data['pager'];
		} else {
			$data['orders'] = '';
			$data['orders_data_pager'] = '';
		}

        
		
		$distributors_data = $this->list_distributors();
        $distributors_highlight_data = $this->schoolmodel->list_distributors_highlight();

        if (!empty($distributors_data)) {
            $this->data['schoolC'] = $this;
            $this->data['make_default'] = $this->schoolmodel->get_defaults();
            $this->data['distributors'] = $distributors_data['results'];
            $this->data['distributors_highlight'] = $distributors_highlight_data;
           // $this->data['dist_links'] = $distributors_data['links'];
        } else {
            $this->data['schoolC'] = $this;
            $this->data['make_default'] = '';
            $this->data['distributors'] = '';
            $this->data['distributors_highlight'] = '';
            $this->data['dist_links'] = '';
        }
        
        // checking the  default distributor and saving distributor details in session
        if (!empty($this->data['make_default'])) {
            $this->session->set('distributor_id', $this->data['make_default']);
        }

        $dis_id = $this->session->get('distributor_id');
        if (!empty($dis_id)) {
            $builder = $this->db->table('users');
            $builder->select('distributor_id, distributor_name, currency, paypal_account');
            $builder->where('distributor_id', $dis_id['distributor_id']);
            $query = $builder->get();
            $dis_detail = $query->getResultArray();
	
            if (!empty($dis_detail)) {
                $orderdata = array('distributor_id' => $dis_id['distributor_id'], 'distributor_name' => $dis_detail['0']['distributor_name'], 'distributor_paypal' => $dis_detail['0']['paypal_account']);
                
                
                $this->session->set('orderdata', $orderdata);
            }
        }
        if (empty($dis_id)) {
			$institutionid = $this->institutionTierId;
			$builder = $this->db->table('institution_tier_users');
			$builder->select('school_distributor.id,school_distributor.distributor_id,institution_tier_users.*');
			$builder->join('school_distributor', 'institution_tier_users.user_id = school_distributor.school_user_id');
			$builder->where('institution_tier_users.institutionTierId', $institutionid['id']);
			$builder->where('school_distributor.setdefault',1);
			// $this->db->group_by('school_distributor.school_user_id');
			$builder->orderBy('school_distributor.id', 'desc');
			$builder->limit(1);
			$query = $builder->get();
			$default_distributor_id = $query->getResultArray();

            if (!empty($default_distributor_id)) {
                $this->schoolmodel->insert_defaults(array('school_user_id' => $this->session->get('user_id'), 'distributor_id' => $default_distributor_id[0]['distributor_id'], 'setdefault' => 1));
            }
        }
        //setting Tabs active
        //setting Tabs active
		if (intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_orders')) == 1 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_venues')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->set('tab_orders', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->remove('tab_venues', TRUE);           
        } elseif (intval($this->session->get('tab_distributors')) == 1 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_venues')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->set('tab_distributors', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->remove('tab_venues', TRUE);            
        } elseif (intval($this->session->get('tab_teachers')) == 1 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_venues')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->set('tab_teachers', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->remove('tab_venues', TRUE);           
        } elseif (intval($this->session->get('tab_u13entries')) == 1 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_venues')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->set('tab_u13entries', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->remove('tab_venues', TRUE);            
        } elseif (intval($this->session->get('tab_events')) == 1 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_venues')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->set('tab_events', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->remove('tab_venues', TRUE);            
        }
        elseif (intval($this->session->get('tab_venues')) == 1 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_results')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_results', TRUE);
            $this->session->set('tab_venues', TRUE);            
        }
		elseif (intval($this->session->get('tab_results')) == 1 && intval($this->session->get('tab_u13entries')) == 0 && intval($this->session->get('tab_orders')) == 0 && intval($this->session->get('tab_distributors')) == 0 && intval($this->session->get('tab_events')) == 0 && intval($this->session->get('tab_teachers')) == 0 && intval($this->session->get('tab_venues')) == 0 ) {
            $this->session->remove('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
            $this->session->set('tab_results', TRUE);           
        }
        else {
            $this->session->set('tab_orders', TRUE);
            $this->session->remove('tab_distributors', TRUE);
            $this->session->remove('tab_teachers', TRUE);
            $this->session->remove('tab_u13entries', TRUE);
            $this->session->remove('tab_events', TRUE);
            $this->session->remove('tab_venues', TRUE);
            $this->session->remove('tab_results', TRUE);            
        }
		
        // school order results available column code starts
        if ($data['orders']) {
            foreach ($data['orders'] as $values) {
                $values->tokens_status = $this->schoolmodel->gettokens_status($values->id,$values->number_of_tests);
                $tokens = $this->schoolmodel->get_result_status($values->id,$values->type_of_token);
                $values->results = $tokens ? lang('app.language_school_label_available') : lang('app.language_school_label_not_available') ;
                $order_access_type = $this->schoolmodel->token_access_type($values->id);
                if ($order_access_type != NULL) {
                    $values->is_supervised = $order_access_type['is_supervised'];
                }else{
                    $values->is_supervised ="";
                }


                $newvalues[] = $values;
            }
            // $this->data['orders'] = $newvalues;
            $data['orders'] = $newvalues;
        }
        

        $tierdata =  $this->institutionTierId;

        $organisation_type = $this->db->query('SELECT organisation_type FROM `institution_tiers` WHERE id="' . $tierdata['id'] . '"');
        if ($organisation_type->getNumRows() > 0) {
            $result = $organisation_type->getRowArray();
            $this->data['organisation_type'] = $result['organisation_type'];
        }
        $department = $this->db->query('SELECT * from departments');
        $this->data['department'] = $department->getResult();
        $level_of_study = $this->db->query('SELECT * from level_of_study');
        $this->data['level_of_study'] = $level_of_study->getResult();

        //Report form submit
        if ($this->request->getPOST('search_submit') || $this->session->get('searchresult')) {
            if ($this->request->getPOST('search_submit')) {
                $this->report_creation($this->data['organisation_type']);
            } else {
                $this->session->remove('searchresult');
                //$this->session->setFlashdata('errors', 'No records found!');
                return redirect()->to('school/dashboard');
            }
        }
		$institutionTierId = $this->institutionTierId['id'];
		$data['institute_courseType'] = $this->usermodel->get_institute_courseType($institutionTierId);
		
        //to view teachers area
        $data['search_item'] = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
        $data['teachersData'] = $this->teachers();
        //to view venue data
        $data['venueData'] = $this->listvenues(); // WP-1120
         //to view test events area
        $data['test_events_data'] = $this->listevents();
        $data['u13learner_data'] = $this->u13_learners();

        // to view tokens of class
        $data['show_view'] = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
        $data['classid'] = (isset($_GET['classid']) && $_GET['classid'] != '') ? $_GET['classid'] : '';
        $data['class_name'] = (isset($_GET['class_name']) && $_GET['class_name'] != '') ? $_GET['class_name'] : '';

        $data['teacher_class_id'] = (isset($_GET['teacher_class_id']) && $_GET['teacher_class_id'] != '') ? $_GET['teacher_class_id'] : '';
        
        if(!empty($data['teacher_class_id'])){ 
            $data['class_learners'] = $this->class_learners();
         }

        $access_language = $this->cmsmodel->institute_access_language_by_tierusers($this->session->get('user_id'));
        $data['access_language_id'] = ($access_language['access_detail_language'] > 0) ? $access_language['access_detail_language'] : 1;

        // $institutionTierId = $this->data['institutionTierId']['id'];

        $data['institute_courseType'] = $this->usermodel->get_institute_courseType($institutionTierId);
       

        $data['institutionTierId'] = $this->institutionTierId;
        //WP-1221 - PDF Results download pending tasks
        $product_eligiblity_array = $this->eventmodel->get_eligible_productname($this->data['product_eligiblity']);
        $data['courses'] = array();//WP-1204
        foreach($product_eligiblity_array as $product_eligiblity){
            if($product_eligiblity['name'] === 'StepCheck'){
                $data['courses']['StepCheck'] = $this->schoolmodel->get_course_lists('tds_test_detail', 1); //WP-1197
            }else{
                $data['courses'][$product_eligiblity['name']] = $this->schoolmodel->get_course_lists('products', $product_eligiblity['name']);//WP-1197
            }
        }

        $data['pdf_result_tasks'] = $this->schoolmodel->get_pdf_result_tasks($this->session->get('user_id'));
        $data['languages'] = $this->cmsmodel->get_language();

        echo view('school/header');
        echo view('school/menus',$data);
        echo view('school/dashboard',$data);
        echo view('school/footer');


    }

     //profile update
     public function profile($password_change=FALSE) {

        if (!$this->acl_auth->logged_in()) {
        return redirect()->to(site_url('/')); 
        }

        $userdata = $this->session->get();
        $this->data['profile'] = $this->usermodel->get_profile($userdata['user_id']);

        if($password_change) {
            $this->session->set('tabprofile', FALSE);
            $this->session->set('tabpass', TRUE);
        } else {
            $this->session->set('tabprofile', TRUE);
            $this->session->set('tabpass', false);
        }

        if ($this->request->getPost('profile_submit')) {
			
			$this->session->set('tabprofile', TRUE);
        	$this->session->set('tabpass', false);
           
            //WP-1271 - Change email address 
            if($userdata['user_email'] != trim($this->request->getPost('email')) ){
                
                $rules = [
                'firstname' => [
                    'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                    'rules'  => 'max_length[64]|required|serbia_username_check',
                        'errors' => [
                            'serbia_username_check' => lang('app.language_school_profile_firstname_check')
                        ],
                    ],
                    'secondname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                        'rules'  => 'max_length[64]|required|serbia_username_check',
                        'errors' => [
                            'serbia_username_check' => lang('app.language_school_profile_second_check')
                        ]
                        ],
                    'email' => [
                        'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                        'rules'  => 'required|max_length[100]|is_unique[users.email]',
                    ],
                ]; 
            }else{
               
                $rules = [
                    'firstname' => [
                        'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                        'rules'  => 'max_length[64]|required|serbia_username_check',
                            'errors' => [
                                'serbia_username_check' => lang('app.language_school_profile_firstname_check')
                            ]
                        ],
                        'secondname' => [
                            'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                            'rules'  => 'max_length[64]|required|serbia_username_check',
                            'errors' => [
                                'serbia_username_check' => lang('app.language_school_profile_second_check')
                            ]
                        ],   
        
                ]; 

            }
            //WP-1271 - Change email address - ends
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                $this->data['validation'] = $this->validator;
             }else {
                $profiledata = array('name' => $this->request->getPost('firstname')." ".$this->request->getPost('secondname'),'firstname' => $this->request->getPost('firstname'), 'lastname' => $this->request->getPost('secondname'), 'email' => $this->request->getPost('email'));

                if ($this->usermodel->update_profile($profiledata)) {
                    $this->session->set('user_firstname', $this->request->getPost('firstname'));
                    $this->session->set('user_lastname', $this->request->getPost('secondname'));
                    $this->session->set('user_email', $this->request->getPost('email'));//WP-1271 - Change email address
                    $this->session->set('username', $this->request->getPost('email'));//WP-1271 - Change email address
                    
                       //WP-1391 zendesk School profile update
                       if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                        $user_id = $userdata['user_id'];
                        zendesk_profile_update($user_id,"School Update");   
                    }

                    $this->session->setFlashdata('messages', lang('app.language_admin_profile_updated_success_msg'));
                    return redirect()->to(site_url('school/profile'));
                }
            }
        }

        //change password
        if ($this->request->getPost('changepass_submit')) {
			
			$this->session->set('tabprofile', FALSE);
        	$this->session->set('tabpass', TRUE);
            $rules = [
                'current_password' => [
                    'label'  =>  lang('app.language_site_booking_screen2_label_current_password'),
                    'rules'  => 'required|min_length[8]|max_length[100]|new_password_check',
                    'errors' => [
                        'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                    ]
                ],
                'new_password' => [
                    'label'  =>  lang('app.language_site_booking_screen2_label_new_password'),
                    'rules'  => 'required|min_length[8]|max_length[100]|new_password_check',
                    'errors' => [
                        'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                    ]
                ],
                'confirm_new_password' => [
                    'label'  =>  lang('app.language_site_booking_screen2_label_confirm_new_password'),
                    'rules'  => 'required|max_length[100]|min_length[8]|matches[new_password]',
                ]        
            ];

            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                $this->data['validation'] = $this->validator;
            } else {

                $user = $this->data['profile'];
                if (!$this->passwordhash->CheckPassword($this->request->getPost('current_password'), $user[0]->password)) {
                    $this->session->setFlashdata('errors', lang('app.language_site_change_password_current_password_invalid_msg'));
                    $this->data['validation'] = $this->validator;
                    return redirect()->to(site_url('school/profile/'.$this->session->get('tabpass')));
                } else {
                    $passwordata = array('password' => $this->passwordhash->HashPassword($this->request->getPost('new_password')));
                    if ($this->usermodel->update_profile($passwordata)) {
                        $this->session->set('password', $this->request->getPost('new_password'));
                        $this->session->setFlashdata('messages', lang('app.language_site_change_password_updated_success_msg'));
                        $this->data['validation'] = $this->validator;
                        return redirect()->to(site_url('school/profile/'.$this->session->get('tabpass')));
                    }
                }
            }
        }

        $this->data['languages'] = $this->cmsmodel->get_language();
        echo view('school/header');
        echo view('school/menus',$this->data);
        echo view('school/profile',$this->data);
        echo view('school/footer');	

    }
	

    public function get_tds_order_types(){
		$query = $this->db->query('SELECT test_slug, test_name, status FROM tds_test_detail WHERE tds_group_id = 1 OR tds_group_id = 2');
		$result = $query->getResult();
		if($query->getRowArray() > 0){
			return $result;
		}else{
			return FALSE;
		}
	}

    function list_orders() {

        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        if($this->schoolmodel->fetch_orders() != Null )
        {
            $total = count($this->schoolmodel->fetch_orders());
        }else{
            $total = 0;
        }
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_list_orders');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }

       $distributor_data = array(
           'results' => $this->schoolmodel->fetch_orders($perPage, $offset),
           'pager' => $pager
       );
       return $distributor_data;
       
    }

    public function teachers() {

        $search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
       
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $this->usermodel->record_teachers_count(trim($search_item));
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_teachers');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }
        $data = array(
            'search_item' => $search_item,
            'class_associated_data' => $this->teachermodel->get_teacher_class_active_learners(),
            'teachers' => $this->usermodel->fetch_teachers($perPage, $offset, trim($search_item)),
            'pager' => $pager
        );
        return $data;
    }

	public function listvenues() {
	    if(!$this->acl_auth->logged_in()){
            return redirect()->to(site_url('school')); 
	    }
         //pagination
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $this->venuemodel->record_count();
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_list_venues');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }
	    $data = array(
	        'distributor_title' => lang('app.language_distributor_list_venues'),
	        'distributor_heading' => lang('app.language_distributor_list_venues'),
	        'results' => $this->venuemodel->fetch_venue($perPage, $offset),
            'pager' => $pager
	    );
	    return $data;
	}

    //log out from session
    public function logout() {        
            if($this->session->get('Yellowfin_loginSessionId') != ''){
                return redirect()->to('report/logoutUser');
            }        
            $success = $this->acl_auth->logout();
            $this->session->setFlashdata('messages', lang('app.language_admin_logout_success_msg'));
            return redirect()->to('/');
    }

	public function tokenlist($orderid=false) {
	
        if(!$this->acl_auth->logged_in()){
	        return redirect()->to(site_url('school')); 
	    }

        if(!$orderid){
            return redirect()->to('school/dashboard');
        }

        if (!empty($orderid)) {
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $tokenlist_segment = array_search('tokenlist',$TotalSegment_array,true);
        $segment = $tokenlist_segment + 3;
        $pager = "";
        $total = $this->schoolmodel->gettokens_count($orderid);
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(5)) ? $this->request->uri->getSegment(5) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_token_list');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }

	    $token_data = array(
                'results' => $this->schoolmodel->gettokens_view($orderid,$perPage, $offset),
                'pager' => $pager
            );
			
            if($token_data['results']){

                foreach ($token_data['results'] as $tokens) {
                    $order_type = (!empty($tokens->type_of_token)) ? $tokens->type_of_token : $tokens->order_type;
                    $tokentype = array("benchmarktest","speaking_test");
                    // TDS-366 condition for benchmark and tds,collgepre finaltest results
						if(isset($order_type) && (!in_array($order_type, $tokentype))) {
							
							if(!empty($tokens->processed_data) || !empty($tokens->tds_data)){
								$processed_data_res = (!empty($tokens->tds_data)) ? $tokens->tds_data : $tokens->processed_data;
								$token_audio = (!empty($tokens->result_token)) ? $tokens->result_token : $tokens->tds_token ;
                                $audioResponse = @get_audio_common($processed_data_res, $tokens->tbr_date, $token_audio,false);
                                $tokens->audio_reponses = $audioResponse['audio_reponses'];
                                $tokens->audio_available = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                $tokens->url = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                            } 
							if(!empty($tokens->candidate_id) || !empty($tokens->higher_candidate_id)){
								$_candidate_id = (!empty($tokens->candidate_id)) ? $tokens->candidate_id : $tokens->higher_candidate_id;
								if(preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $_candidate_id)){
									$tokens->audio_reponses = 1;
									$tokens->audio_available = False;                       
                                }                        
							}                       
                        }                        
							
                    if ($tokens->thirdparty_id > 0) {
						
						$query = $this->db->query('SELECT thirdparty_id,session_number FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
						$results = $query->getResultArray();
                        $tokens->practiceresults = $results;
						
                        //TDS PRACTICE TEST RESULTS
                        $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id , 'practice');
						
                        if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                            foreach($practice_tds_results as $key => $practice_tds_result){
                                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                    $tds_practice['practice_test1'] = $practice_tds_result;
                                }else{
                                    $tds_practice['practice_test2'] = $practice_tds_result;
                                }
                            }
                           $tokens->practiceresults_tds = $tds_practice;
                        }
                         //TDS-366
                        if(isset($order_type) && (!in_array($order_type, $tokentype))) {
                            if(isset($tokens->practiceresults_tds) || !empty($tokens->practiceresults)){  
                                if(!empty($tokens->practiceresults[0]) && !empty($tokens->practiceresults['0']['session_number'])){
                                    $tokens->practiceresults[0]['audio_reponses'] = 1;
                                }                      
                                if(!empty($tokens->practiceresults[1]) && !empty($tokens->practiceresults['1']['session_number'])){
                                    $tokens->practiceresults[1]['audio_reponses'] = 1;
                                }  
                                if(!empty($tokens->practiceresults_tds['practice_test1']) && !empty($tokens->practiceresults_tds['practice_test1']['processed_data'])){
                                    $audioResponse = @get_audio_common($tokens->practiceresults_tds['practice_test1']['processed_data'], $tokens->practiceresults_tds['practice_test1']['practice_result_date'], $tokens->practiceresults_tds['practice_test1']['token'],'practice');
                                    $tokens->practiceresults_tds['practice_test1']['audio_reponses'] = $audioResponse['audio_reponses'];
                                    $tokens->practiceresults_tds['practice_test1']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                    $tokens->practiceresults_tds['practice_test1']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                    
                                } 
                                if(!empty($tokens->practiceresults_tds['practice_test2']) && !empty($tokens->practiceresults_tds['practice_test2']['processed_data'])){
                                    $audioResponse = @get_audio_common($tokens->practiceresults_tds['practice_test2']['processed_data'], $tokens->practiceresults_tds['practice_test2']['practice_result_date'], $tokens->practiceresults_tds['practice_test2']['token'],'practice');
                                    $tokens->practiceresults_tds['practice_test2']['audio_reponses'] = $audioResponse['audio_reponses'];
                                    $tokens->practiceresults_tds['practice_test2']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                    $tokens->practiceresults_tds['practice_test2']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                    
                                }
                            }                    
                        }  
                          
                        // progress information from moodle
                        /*$dataNeeded = array("token" => "cts47a7264afdwh", "courseid" => $tokens->alp_id, "userid" => $tokens->user_app_id);
                        $data_string = json_encode($dataNeeded);
                        $response = $this->http_ws_call($data_string);
                        $res_json = json_decode($response);
                        if (!empty($res_json)) {
                            if (isset($res_json->result->coursegrade)) {
                                $tokens->progress = round($res_json->result->coursegrade);
                            }
                        }*/
                        $query = $this->db->query('SELECT user_products.course_progress FROM user_products WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
						
                        $results = $query->getRowObject();

                            if($results->course_progress != NULL){
                                $tokens->course_progress = round($results->course_progress); 
                            }

                        //WP-1308 Starts
                        $tds_test_query = $this->db->query('SELECT candidate_id,test_type FROM `tds_tests` WHERE candidate_id = "' . $tokens->thirdparty_id . '" AND test_type = "practice" ');
                        if ($tds_test_query->getNumRows() > 0) { 
                            $tokens->practice_count = $tds_test_query->getNumRows();
                        }else{
                            $cp_test_query = $this->db->query('SELECT CB.test_number, CB.thirdparty_id FROM collegepre_batch_add CB JOIN collegepre_formcodes CF ON CF.test_number = CB.test_number WHERE CB.thirdparty_id = "' . $tokens->thirdparty_id . '" AND CF.type = "Practice test"');
                            if ($cp_test_query->getNumRows() > 0) {
                                $tokens->practice_count = $cp_test_query->getNumRows();
                            }else{
                                $tokens->practice_count = 0;
                            }
                        }
                        
                    }else{
                        $tds_practice_query = $this->db->query('SELECT no_of_practice_test FROM `tds_practice_test_settings` WHERE product_groups_id = "2" ');
                        if ($tds_practice_query->getNumRows() > 0) {
                            $result = $tds_practice_query->getRowObject();
                            $tokens->practice_count = $result->no_of_practice_test;
                        }
                    }
                }

				$data['institutionTierId'] =  $this->institutionTierId;
                $data['tokens'] = $token_data['results'];
                $data['tokenlst_data_pager'] = $token_data['pager'];
            } else {
                $data['tokens'] = '';
                $data['token_links'] = '';
                $data['tokenlst_data_pager'] = '';
              }
        } else {
            $data['tokens'] = '';
            $data['token_links'] = '';
            $data['tokenlst_data_pager'] = '';
        }
		
        $data['languages'] = $this->cmsmodel->get_language();

		echo view('school/header');
        echo view('school/menus',$data);
        echo view('school/tokenlists',$data);
        echo view('school/footer');		
    }
	
	function list_distributors() {
	$distributor_data = array(
            'results' => $this->schoolmodel->fetch_distributor()    
        );
        return $distributor_data;
    }
	
	
	 public function ordertest() {
       if ($this->request->getPOST()) {
            if ($this->request->getPOST('order_name')) {
                $is_supervised = ($this->request->getPOST('is_supervised') != null) ? $this->request->getPOST('is_supervised') : "1";
                $order_session = $this->session->get('orderdata');
                $order_post = array('order_name' => $this->request->getPOST('order_name'), 'order_desc' => $this->request->getPOST('order_desc'), 'number_of_tests' => $this->request->getPOST('number_of_tests'), 'type_of_token' => $this->request->getPOST('type_of_token'), 'is_supervised' => $is_supervised);
                $order_merge = array_merge($order_session, $order_post);
                $this->session->set('orderdata', $order_merge);
            }
        }
    }
	
	
	// order payment
    public function order_pay() {

        if ($this->session->get('distributor_id')) {
			
			$dis_id = $this->session->get('distributor_id');
			$builder1 = $this->db->table('prices');
            $builder1->select('*');
            $builder1->where('distributor_id', $dis_id['distributor_id']);
            $query1 = $builder1->get();
            $dis_price = $query1->getResultArray();

            if ($this->session->get('orderdata')) {

                if ($this->request->getPost('payment_method') == 'paypal') {

                } else {

                    // school order creation in database
                    $order_details = array(
                        'school_user_id' => $this->session->get('user_id'),
                        'distributor_id' => $this->session->get('orderdata')['distributor_id'],
                        'distributor_name' => $this->session->get('orderdata')['distributor_name'],
                        'order_name' => $this->session->get('orderdata')['order_name'],
                        'order_desc' => $this->session->get('orderdata')['order_desc'],
                        'number_of_tests' => $this->session->get('orderdata')['number_of_tests'],
                        'type_of_token' => $this->session->get('orderdata')['type_of_token'],
                        'order_date' => date('Y-m-d H:i:s'),
                        'created_by' => $this->session->get('logged_tier1_userid'),
                        'payment_done' => '1'
                    );

					$builder2 = $this->db->table('school_orders');
					$builder2->insert( $order_details);
					$school_order_id =  $this->db->insertID();

                    // school order payment details in database
                    $details = array(
                        'payment_method' => 'none',
                        'school_order_id' => $school_order_id,
                        'distributor_id' => $this->session->get('orderdata')['distributor_id'],
                        'payment_success' => 'success'
                    );

                    $payment_detail = $this->schoolmodel->save_payment_details($details);
                    $this->session->set('payment_id', $payment_detail);
                    if (!empty($payment_detail)) {
                        for ($i = 0; $i < $this->session->get('orderdata')['number_of_tests']; $i++) {
                            $token = $this->getToken(9);
							$token_details = array(
								'token' => $token,
								'school_order_id' => $school_order_id,
								'generated_date' => date('Y-m-d'),
								'expiry' => strtotime(date('d-m-Y')),
							    'type_of_token' => $this->session->get('orderdata')['type_of_token'],
							    'is_supervised' => $this->session->get('orderdata')['is_supervised'],
							);					
							$builder3 = $this->db->table('tokens');
							$builder3->insert($token_details);							
                        }
                    }
					
					return redirect()->to(site_url('school/success')); 
                }
            } else {
                $this->session->set('failure', 'Required details not available');
				return redirect()->to(site_url('school/dashboard'));
            }
        } else {
            $this->session->set('failure', 'Please set the default distributor');
			return redirect()->to(site_url('school/dashboard'));
        }
    }
	
	
	public function getToken($length = '9') {
        $token = "";
        $codeAlphabet = "ACEFGHKMNPRTUVWXY";
        $codeAlphabet.= "34679";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }
		$builder = $this->db->table('tokens');
        $builder->select('id, token');
        $builder->where('token', $token);
        $chk_tokens = $builder->get()->getResultArray();

        if ($chk_tokens) {
            $this->getToken(9);
        }else{
            return $token;
        }
    }
	
	public function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1)
            return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }
	
	public function success() {
        if (($this->session->get('paypal_paykey')) || ($this->session->get('payment_id'))) {
			
			$builder = $this->db->table('school_payments');
			$builder->select('*');
			$builder->where('id', $this->session->get('payment_id'));
			$school_payments = $builder->get()->getResultArray();
			
            if (!empty($school_payments)) {
                $this->session->set('school_orderid', $school_payments['0']['school_order_id']);
            }
            $this->session->remove('orderdata');
            $this->session->remove('paypal_paykey');
            $this->session->remove('payment_id');

            $data['languages'] = $this->cmsmodel->get_language();

			echo view('school/header');
			echo view('school/menus', $data);
			echo view('school/order4');
			echo view('school/footer');

        } else {
			return redirect()->to(site_url('school/dashboard'));
        }
    }
	
	
	public function search_order_list() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
        $order_search_item = (isset($_GET['order_list_search']) && $_GET['order_list_search'] != '') ? $_GET['order_list_search'] : '';

        if($this->schoolmodel->get_over16_list_search(trim($order_search_item)) != Null )
        {
            $srch_ord_lst_cnt = count($this->schoolmodel->get_over16_list_search(trim($order_search_item)));

        }else{
            $srch_ord_lst_cnt = 0;
        }

        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $srch_ord_lst_cnt;
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_search_order_list');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }

        $over16_search_list = $this->schoolmodel->get_over16_list_search(trim($order_search_item),$perPage, $offset);

        if(count((array)$over16_search_list) > 0){

            foreach ($over16_search_list as $tokens) {

                $tokentype = array("benchmarktest","speaking_test");
                    if(isset($tokens->type_of_token) && (!in_array($tokens->type_of_token, $tokentype))) {
                        if(!empty($tokens->processed_data) || !empty($tokens->tds_data)){
                            $processed_data_res = (!empty($tokens->tds_data)) ? $tokens->tds_data : $tokens->processed_data;
                            $_token = (!empty($tokens->result_token)) ? $tokens->result_token : $tokens->tds_token ;
                            $result_date = (!empty($tokens->tbr_date)) ? $tokens->tbr_date : $tokens->tds_result_date ;
                            $audioResponse = @get_audio_common($processed_data_res, $result_date, $_token);
                            $tokens->audio_reponses = $audioResponse['audio_reponses'];
                            $tokens->audio_available = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                            $tokens->url = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                        } 
                        if(!empty($tokens->candidate_id) || !empty($tokens->higher_candidate_id)){
                            $_candidate_id = (!empty($tokens->candidate_id)) ? $tokens->candidate_id : $tokens->higher_candidate_id;
                            if(preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $_candidate_id)){
                                $tokens->audio_reponses = 1;
                                $tokens->audio_available = False;                       
                            }                        
                        }                       
                    }

                if ($tokens->thirdparty_id > 0) {

                    $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getResultArray();
                    $tokens->practiceresults = $results;

                    //TDS PRACTICE TEST RESULTS
                    $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id , 'practice');
                    if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                        foreach($practice_tds_results as $key => $practice_tds_result){
                            if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                $tds_practice['practice_test1'] = $practice_tds_result;
                            }else{
                                $tds_practice['practice_test2'] = $practice_tds_result;
                            }
                        }
                        $tokens->practiceresults_tds = $tds_practice;
                    }

                    if(isset($tokens->type_of_token) && (!in_array($tokens->type_of_token, $tokentype))) {
                        if(isset($tokens->practiceresults_tds) || !empty($tokens->practiceresults)){  
                            if(!empty($tokens->practiceresults[0]) && !empty($tokens->practiceresults['0']['session_number'])){
                                $tokens->practiceresults[0]['audio_reponses'] = 1;
                            }                      
                            if(!empty($tokens->practiceresults[1]) && !empty($tokens->practiceresults['1']['session_number'])){
                                $tokens->practiceresults[1]['audio_reponses'] = 1;
                            }  
                            if(!empty($tokens->practiceresults_tds['practice_test1']) && !empty($tokens->practiceresults_tds['practice_test1']['processed_data'])){
                                $audioResponse = @get_audio_common($tokens->practiceresults_tds['practice_test1']['processed_data'], $tokens->practiceresults_tds['practice_test1']['practice_result_date'], $tokens->practiceresults_tds['practice_test1']['token'],'practice');
                                $tokens->practiceresults_tds['practice_test1']['audio_reponses'] = $audioResponse['audio_reponses'];
                                $tokens->practiceresults_tds['practice_test1']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                $tokens->practiceresults_tds['practice_test1']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                
                            } 
                            if(!empty($tokens->practiceresults_tds['practice_test2']) && !empty($tokens->practiceresults_tds['practice_test2']['processed_data'])){
                                $audioResponse = @get_audio_common($tokens->practiceresults_tds['practice_test2']['processed_data'], $tokens->practiceresults_tds['practice_test2']['practice_result_date'], $tokens->practiceresults_tds['practice_test2']['token'],'practice');
                                $tokens->practiceresults_tds['practice_test2']['audio_reponses'] = $audioResponse['audio_reponses'];
                                $tokens->practiceresults_tds['practice_test2']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                $tokens->practiceresults_tds['practice_test2']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                
                            }
                        }                    
                    } 
                    $query = $this->db->query('SELECT user_products.course_progress FROM user_products WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getRow();
                    if (!empty($results)) {
                        if($results->course_progress != NULL){
                            $tokens->course_progress = round($results->course_progress); 
                        }
                    }

                    //WP-1308 Starts
                    $tds_test_query = $this->db->query('SELECT * FROM `tds_tests` WHERE candidate_id = "' . $tokens->thirdparty_id . '" AND test_type = "practice" ');
                    if ($tds_test_query->getNumRows() > 0) {
                        $tokens->practice_count = $tds_test_query->getNumRows();
                    }else{
                        $cp_test_query = $this->db->query('SELECT CB.* FROM collegepre_batch_add CB JOIN collegepre_formcodes CF ON CF.test_number = CB.test_number WHERE CB.thirdparty_id = "' . $tokens->thirdparty_id . '" AND CF.type = "Practice test"');
                        if ($cp_test_query->getNumRows() > 0) {
                            $tokens->practice_count = $cp_test_query->getNumRows();
                        }else{
                            $tokens->practice_count = 0;
                        }
                    }
                    
                }else{

                    $tds_practice_query = $this->db->query('SELECT * FROM `tds_practice_test_settings` WHERE product_groups_id = "2" ');
                    if ($tds_practice_query->getNumRows() > 0) {
                        $result = $tds_practice_query->getRowObject();
						
                        $tokens->practice_count = $result->no_of_practice_test;
                    }
                }
            }
            $data = array(
                'search_item' => $order_search_item,
                'search_list' => $over16_search_list,
                'pager' => $pager
            );

        }else{
             $data = array(
                'search_item' => $order_search_item,
                'search_list' => "",
                'total_links' => "",
                'pager' => $pager
            );
        }
        return $data;
    }

	//export tokens to excel
    public function export_tokens($order_id) {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
		
        if (!empty($order_id)) {
            $tokens = $this->schoolmodel->tokens_export($order_id);
            $delimiter = ",";
            $newline = "\r\n";
            if (count($tokens) > 0) {
                // $this->load->helper('csv');
                $heading_array = array('id' => '#', 'token' => 'code', 'order_name' => 'ordername', 'order_date' => 'date');
                $results_array = $this->schoolmodel->tokens_export($order_id);
                array_unshift($results_array, $heading_array);
                echo array_to_csv($results_array, 'Code List for order.csv');
				exit;
								
            }
        } else {
            show_404();
        }
    }
	
	//WP-1374 csv export
    public function export_orders($order_ids){
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }

        if (!empty($order_ids)) {
            $code_order_ids = urldecode($order_ids);
            $token_data = array(
                'results' => $this->schoolmodel->gettokens_view_download($code_order_ids)
            );
            
            foreach ($token_data['results'] as $tokens) {
                if ($tokens->thirdparty_id > 0) {

                    $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getResult();
                    $tokens->practiceresults = $results;
                    
                    $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id , 'practice');
                        if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                            foreach($practice_tds_results as $key => $practice_tds_result){
                                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                    $tds_practice['practice_test1'] = $practice_tds_result;
                                }else{
                                    $tds_practice['practice_test2'] = $practice_tds_result;
                                }
                            }
                            $tokens->practice_tests_tds = $this->practicetest_result_tds_export($tds_practice);
                        }
                    $query = $this->db->query('SELECT user_products.course_progress FROM user_products WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getRowObject();

                    if (count((array)$results) > 0) {
                        if($results->course_progress != NULL){
                            $tokens->progress = round($results->course_progress); 
                        }
                    }
                        
                }
            }
            $this->data['tokens'] = $token_data['results'];
            $for_export = $this->data['tokens'];
            $newarray = array();
            $count = 0;
            if (!empty($for_export)) {
                foreach ($for_export as $key => $val) {
                    $token_type = substr($val->type_of_token, 0, 12);
                    if(isset($val->type_of_token) && $token_type == "benchmarking"){
                        if (!empty($val->token)) {
                        $newarray[$count]['token'] = $val->token;
                    } else {
                        $newarray[$count]['token'] = 'Not available';
                    }
                        if (!empty($val->user_name)) {
                        $newarray[$count]['learner_name'] = $val->user_name;
                    } else {
                        $newarray[$count]['learner_name'] = 'Not available';
                    }
                        if (!empty($val->email)) {
                        $newarray[$count]['learner_email'] = $val->email;
                    } else {
                        $newarray[$count]['learner_email'] = 'Unregistered';
                    }
                    
                        if (!empty($val->test_name)) {
                        $newarray[$count]['test_name'] = $val->test_name;
                    } else {
                        $newarray[$count]['test_name'] = 'Not available';
                    }                    
                    if(!empty($val->benchmark_cefr_level)){
                        $newarray[$count]['level'] = $val->benchmark_cefr_level;
                    }else {
                        $newarray[$count]['level'] = 'Not available';
                    }
                    $newarray[$count]['progress'] = 'Not available';
                    $newarray[$count]['practice_test1'] = 'Not available';
                    $newarray[$count]['practice_test2'] = 'Not available';
                    
                    $newarray[$count]['test_booking'] = 'Not available';
                    $newarray[$count]['score_out_50'] = 'Not available';
                    $newarray[$count]['cefr_level'] = 'Not available';
                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                        $processed_data = json_decode($val->benchmark_data);
                        //echo '<pre>'; print_r($processed_data->reading->level); die;
                    
                        if (isset($processed_data->listening) && !empty($processed_data->listening->level) && ($processed_data->listening->score >= 0)) {
                        $newarray[$count]['cefr_listening'] = $processed_data->listening->level;
                        $newarray[$count]['listening_score'] = $processed_data->listening->score;
                    } else {
                        $newarray[$count]['cefr_listening'] = 'Not available';
                        $newarray[$count]['listening_score'] = 'Not available';
                    }
                        if (isset($processed_data->reading) && !empty($processed_data->reading->level) && ($processed_data->reading->score >= 0)) {
                        $newarray[$count]['cefr_reading'] = $processed_data->reading->level;
                        $newarray[$count]['reading_score'] = $processed_data->reading->score;
                    } else {
                        $newarray[$count]['cefr_reading'] = 'Not available';
                        $newarray[$count]['reading_score'] = 'Not available';
                    }
                        if (isset($processed_data->speaking) && !empty($processed_data->speaking->level) && ($processed_data->speaking->score >= 0)) {
                        $newarray[$count]['cefr_speaking'] = $processed_data->speaking->level;
                        $newarray[$count]['speaking_score'] = $processed_data->speaking->score;
                    } else {
                        $newarray[$count]['cefr_speaking'] = 'Not available';
                        $newarray[$count]['speaking_score'] = 'Not available';
                    }
                        if (isset($processed_data->writing) && !empty($processed_data->writing->level) && ($processed_data->writing->score >= 0)) {
                        $newarray[$count]['cefr_writing'] = $processed_data->writing->level;
                        $newarray[$count]['writing_score'] = $processed_data->writing->score;
                    } else {
                        $newarray[$count]['cefr_writing'] = 'Not available';
                        $newarray[$count]['writing_score'] = 'Not available';
                    }
                        if (isset($processed_data->overall) && !empty($processed_data->overall->level) && ($processed_data->overall->score >= 0)) {
                        $newarray[$count]['cefr_overall'] = $processed_data->overall->level;
                        $newarray[$count]['cefr_score'] = $processed_data->overall->score;
                    } else {
                        $newarray[$count]['cefr_overall'] = 'Not available';
                        $newarray[$count]['cefr_score'] = 'Not available';
                    }
                    }
                    if(isset($val->type_of_token) && $token_type != "benchmarking"){
                    //   $newarray[$count]['id'] = $val->id;
                        $newarray[$count]['token'] = $val->token;
                        if (!empty($val->user_name)) {
                            $newarray[$count]['learner_name'] = $val->user_name;
                        } else {
                            $newarray[$count]['learner_name'] = 'Not available';
                        }
                        if (!empty($val->email)) {
                            $newarray[$count]['learner_email'] = $val->email;
                        } else {
                            $newarray[$count]['learner_email'] = 'Unregistered';
                        }
                        
                        $newarray[$count]['test_name'] = 'Not available';

                        if (!empty($val->level)) {
                            $newarray[$count]['level'] = $val->level;
                        }elseif(!empty($val->benchmark_cefr_level)){
                            $newarray[$count]['level'] = $val->benchmark_cefr_level;
                        }else {
                            $newarray[$count]['level'] = 'Not available';
                        }
                        if (isset($val->progress)) {
                            $newarray[$count]['progress'] = round($val->progress) ."%";
                        } else {
                            $newarray[$count]['progress'] = 'Not available';
                        }
                        
                        if (isset($val->course_type) && $val->course_type == "Core") {
                                // practice test results for display in excel
                            if (isset($val->practiceresults) && !empty($val->practiceresults)) {

                                if ((isset($val->practiceresults['0']->session_number)) && (isset($val->practiceresults['0']->thirdparty_id))) {

                                    $greenorange_val = $this->gen_practicetest_result($val->practiceresults['0']->session_number, $val->practiceresults['0']->thirdparty_id, 'for_export');
                                    if ($greenorange_val >= 7) {
                                        $newarray[$count]['practice_test1'] = 'Ready to move on';
                                    } else {
                                        $newarray[$count]['practice_test1'] = 'More practice required';
                                    }
                                } else {
                                    $newarray[$count]['practice_test1'] = 'Not taken';
                                }
                                // practice test2
                                if ((isset($val->practiceresults['1']->session_number)) && (isset($val->practiceresults['1']->thirdparty_id))) {
                                    $greenorange_val = $this->gen_practicetest_result($val->practiceresults['1']->session_number, $val->practiceresults['1']->thirdparty_id, 'for_export');
                                    if ($greenorange_val >= 7) {
                                        $newarray[$count]['practice_test2'] = 'Ready to move on';
                                    } else {
                                        $newarray[$count]['practice_test2'] = 'More practice required';
                                    }
                                } else {
                                    $newarray[$count]['practice_test2'] = 'Not taken';
                                }
                            }elseif(isset($val->practice_tests_tds) && !empty($val->practice_tests_tds)){
                                $newarray[$count]['practice_test1'] = $val->practice_tests_tds['practice_msg1'];
                                $newarray[$count]['practice_test2'] = $val->practice_tests_tds['practice_msg2'];
                            }else {
                                $newarray[$count]['practice_test1'] = 'Not taken';
                                $newarray[$count]['practice_test2'] = 'Not taken';
                            }

                            // Test Booking date
                            if (!empty($val->start_date_time)) {
                                $tz_to = $this->institutionTierId['timezone'];
                                $institution_zone_values = @get_institution_zone_from_utc($tz_to, $val->start_date_time, $val->end_date_time);
                                $newarray[$count]['test_booking'] = $institution_zone_values['institute_event_date'];
                            } else {
                                $newarray[$count]['test_booking'] = 'Not available';
                            }

                            if(!empty($val->tds_candidate_id) && $val->tds_course_type == "Core"){
                                // final test results for display in excel - TDS
                                if(!empty($val->tds_data)){
                                    $processed_data = json_decode($val->tds_data, true);
                                    $newarray[$count]['score_out_50'] = $processed_data['total']['score'];
                                    $newarray[$count]['cefr_level'] = $processed_data['overall']['level'];
                                    $newarray[$count]['score_on_cats_scale'] = $processed_data['overall']['score']; 
                                }else{
                                    $newarray[$count]['score_out_50'] = 'Not available';
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                }
                            }else{
                                // final test results for display in excel - COLLEGEPRE
                                if (!empty($val->section_one) && !empty($val->section_two)) {
                                    $section1 = json_decode($val->section_one);
                                    $section2 = json_decode($val->section_two);
                                    $section1_result = $this->process_results($section1->item);
                                    $section2_result = $this->process_results($section2->item);

                                    $questions = count($section1_result) + count($section2_result);
                                    $score = array_sum($section1_result) + array_sum($section2_result);
                                    $score_for_lookup = $score / $questions;
                                    $newarray[$count]['score_out_50'] = $score;
                                } else {
                                    $newarray[$count]['score_out_50'] = 'Not available';
                                }
                                if (!empty($val->section_one) && !empty($val->section_two)) {
                                    $section1 = json_decode($val->section_one);
                                    $section2 = json_decode($val->section_two);
                                    $section1_result = $this->process_results($section1->item);
                                    $section2_result = $this->process_results($section2->item);
                                    $questions = count($section1_result) + count($section2_result);
                                    $score = array_sum($section1_result) + array_sum($section2_result);

                                    $product_level = array('11' => 'A1.1', '12' => 'A1.2', '13' => 'A1.3', '21' => 'A2.1', '22' => 'A2.2', '23' => 'A2.3', '31' => 'B1.1', '32' => 'B1.2', '33' => 'B1.3');
                                    $thirdPartyId = $val->thirdparty_id;
                                    $course_id = substr($thirdPartyId, 10, 2);
                                    $level = $product_level[$course_id];
                                    $for = 'export';
                                    $cefr_val_threshold = $this->get_cefr_threshold($level, $score, $thirdPartyId, $for);
                                    $expploded_val = explode("-", $cefr_val_threshold);
                                    $cefr_val = $expploded_val['0'];
                                    $score_as_string = $expploded_val['1'];

                                    $newarray[$count]['cefr_level'] = $cefr_val;
                                    $newarray[$count]['score_on_cats_scale'] = $score_as_string;
                                } else {
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                } 
                            } 
                        }elseif(isset($val->course_type) && $val->course_type == "Higher") {
                            $newarray[$count]['practice_test1'] = 'Not available';
                            $newarray[$count]['practice_test2'] = 'Not available';
                            // Test Booking date
                            if (!empty($val->start_date_time)) {
                                $tz_to = $this->institutionTierId['timezone'];
                                $institution_zone_values = @get_institution_zone_from_utc($tz_to, $val->start_date_time, $val->end_date_time);
                                $newarray[$count]['test_booking'] = $institution_zone_values['institute_event_date'];
                            } else {
                                $newarray[$count]['test_booking'] = 'Not available';
                            }
                            $newarray[$count]['score_out_50'] = 'Not available';
                            $higher_bookings = $this->bookingmodel->result_display_higher($val->thirdparty_id);
                            if($higher_bookings != FALSE){
                                if(!empty($higher_bookings['logit_values'])){
                                    $cal_level =  $cal_score  = '';
                                    $base_scores = json_decode($higher_bookings['logit_values']);
                                    $cal_level = $base_scores->overall->level;
                                    $cal_score = $base_scores->overall->score;
                                    if(!empty($cal_level)){
                                        $newarray[$count]['cefr_level'] = substr($cal_level, 0, 2);
                                    }
                                    if(!empty($cal_score)){
                                        $newarray[$count]['score_on_cats_scale'] = $cal_score;
                                    }
                                }else{
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                }
                            }
                        }
                        $newarray[$count]['cefr_listening'] = 'Not available';
                        $newarray[$count]['listening_score'] = 'Not available';
                        $newarray[$count]['cefr_reading'] = 'Not available';
                        $newarray[$count]['reading_score'] = 'Not available';
                        $newarray[$count]['cefr_speaking'] = 'Not available';
                        $newarray[$count]['speaking_score'] = 'Not available';
                        $newarray[$count]['cefr_writing'] = 'Not available';
                        $newarray[$count]['writing_score'] = 'Not available';
                        $newarray[$count]['cefr_overall'] = 'Not available';
                        $newarray[$count]['cefr_score'] = 'Not available';
                    }
                    $count ++;
                }
                $heading_array = array('token' => 'Code','learner_name' => 'Learner name', 'learner_email' => 'Learner email','test_name' => 'Type of test taken','level' => 'Level','progress' => 'Progress', 'practice_test1' => 'Practice test 1', 'practice_test2' => 'Practice test 2', 'test_booking' => 'Test booking', 'score_out_50' => 'Score out of 50', 'cefr_level' => 'CEFR', 'score_on_cats_scale' => 'Score on CATs Step scale', 'cefr_listening' => 'CEFR level for Listening','listening_score' => 'Position on CATs scale for Listening','cefr_reading' => 'CEFR level for Reading', 'reading_score' => 'Position on CATs scale for Reading','cefr_speaking' => 'CEFR level for Speaking','speaking_score' => 'Position on CATs scale for Speaking','cefr_writing' => 'CEFR level for Writing', 'writing_score' => 'Position on CATs scale for Writing','cefr_overall' => 'CEFR level overall', 'cefr_score' => 'Position on CATs scale for overall');
                    array_unshift($newarray, $heading_array);
            }
            echo array_to_csv($newarray, 'Codelist of Orders - Date as on-' . date('d-m-Y') . '.csv');
            exit;
        } else {
            $this->data['tokens'] = '';
            $this->data['token_links'] = '';
        }
    }


    public function practicetest_result_tds_export($tds_practices = false){
        if($tds_practices != false){
               $practice1 = !empty($tds_practices['practice_test1']['processed_data']) ? json_decode($tds_practices['practice_test1']['processed_data']) : FALSE;
               $practice2 = !empty($tds_practices['practice_test2']['processed_data']) ? json_decode($tds_practices['practice_test2']['processed_data']) : FALSE;
               if(!empty($practice1)){
                   $p1_score = $practice1->green_or_orange;
                    if($p1_score >= 7 ){
                        $data['practice_msg1'] = lang('app.language_dashboard_practice_test_icon_a');
                    }else{
                        $data['practice_msg1'] = lang('app.language_dashboard_practice_test_icon_b');
                    }
               }else{
                   $data['practice_msg1'] = "Not taken";
               }
               if(!empty($practice2)){
                    $p2_score = $practice2->green_or_orange;
                    if($p2_score >= 7 ){
                        $data['practice_msg2'] = lang('app.language_dashboard_practice_test_icon_a');
                    }else{
                        $data['practice_msg2'] = lang('app.language_dashboard_practice_test_icon_b');
                    }
               }
			   else{
                   $data['practice_msg2'] = "Not Available";
               }
               return $data;
        }else{
            return FALSE;
        } 
    }


    public function export_codes($orderid) {

		if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
        if (!empty($orderid)) {
            $token_data = array(
                'results' => $this->schoolmodel->gettokens_view($orderid)
            );
            foreach ($token_data['results'] as $tokens) {
                if ($tokens->thirdparty_id > 0) {
                    $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getResultArray();
                    $tokens->practiceresults = $results;
                    
                    $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id , 'practice');
                        if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                            foreach($practice_tds_results as $key => $practice_tds_result){
                                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                    $tds_practice['practice_test1'] = $practice_tds_result;
                                }else{
                                    $tds_practice['practice_test2'] = $practice_tds_result;
                                }
                            }
                           $tokens->practice_tests_tds = $this->practicetest_result_tds_export($tds_practice);
                            /*if(!empty($practice_tests_tds) && $practice_tests_tds != FALSE){
                                $tokens->practicetest1_tds = $practice_tests_tds['practice_msg1'];
                                $tokens->practicetest2_tds = $practice_tests_tds['practice_msg2'];
                            }*/
                        }
                 
                    
                    $query = $this->db->query('SELECT user_products.course_progress FROM user_products WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                    $results = $query->getRow();
                    

                    if (count((array)$results) > 0) {
                        if($results->course_progress != NULL){
                            $tokens->progress = round($results->course_progress); 
                        }
                    }
                        
                }
            }
            $this->data['tokens'] = $token_data['results'];
            $for_export = $this->data['tokens'];
            $newarray = array();
            $count = 0;
            if (!empty($for_export)) {
                foreach ($for_export as $key => $val) {
                    if(isset($val->order_type) && $val->order_type == "benchmarking"){
                        if (!empty($val->token)) {
	                    $newarray[$count]['token'] = $val->token;
	                } else {
	                    $newarray[$count]['token'] = 'Not available';
	                }
                        if (!empty($val->user_name)) {
	                    $newarray[$count]['learner_name'] = $val->user_name;
	                } else {
	                    $newarray[$count]['learner_name'] = 'Not available';
	                }
                        if (!empty($val->email)) {
	                    $newarray[$count]['email'] = $val->email;
	                } else {
	                    $newarray[$count]['email'] = 'Not available';
	                }
                        if (!empty($val->result_date)) {
	                    $newarray[$count]['result_date'] = $val->result_date;
	                } else {
	                    $newarray[$count]['result_date'] = 'Not available';
	                }
                        if (!empty($val->test_name)) {
	                    $newarray[$count]['test_name'] = $val->test_name;
	                } else {
	                    $newarray[$count]['test_name'] = 'Not available';
	                }

                       //echo '<pre>'; print_r($val->processed_data); die;
                        $processed_data = json_decode($val->processed_data);

                        if (isset($processed_data->listening) && !empty($processed_data->listening->level) && ($processed_data->listening->score >= 0)) {
	                    $newarray[$count]['cefr_listening'] = $processed_data->listening->level;
                        $newarray[$count]['listening_score'] = $processed_data->listening->score;
	                } else {
	                    $newarray[$count]['cefr_listening'] = 'Not available';
                        $newarray[$count]['listening_score'] = 'Not available';
	                }
                        if (isset($processed_data->reading) && !empty($processed_data->reading->level) && ($processed_data->reading->score >= 0)) {
	                    $newarray[$count]['cefr_reading'] = $processed_data->reading->level;
                        $newarray[$count]['reading_score'] = $processed_data->reading->score;
	                } else {
	                    $newarray[$count]['cefr_reading'] = 'Not available';
                        $newarray[$count]['reading_score'] = 'Not available';
	                }
                        if (isset($processed_data->speaking) && !empty($processed_data->speaking->level) && ($processed_data->speaking->score >= 0)) {
	                    $newarray[$count]['cefr_speaking'] = $processed_data->speaking->level;
                        $newarray[$count]['speaking_score'] = $processed_data->speaking->score;
	                } else {
	                    $newarray[$count]['cefr_speaking'] = 'Not available';
                        $newarray[$count]['speaking_score'] = 'Not available';
	                }
                        if (isset($processed_data->writing) && !empty($processed_data->writing->level) && ($processed_data->writing->score >= 0)) {
	                    $newarray[$count]['cefr_writing'] = $processed_data->writing->level;
                        $newarray[$count]['writing_score'] = $processed_data->writing->score;
	                } else {
	                    $newarray[$count]['cefr_writing'] = 'Not available';
                        $newarray[$count]['writing_score'] = 'Not available';
	                }
                        if (isset($processed_data->overall) && !empty($processed_data->overall->level) && ($processed_data->overall->score >= 0)) {
	                    $newarray[$count]['cefr_overall'] = $processed_data->overall->level;
                        $newarray[$count]['cefr_score'] = $processed_data->overall->score;
	                } else {
	                    $newarray[$count]['cefr_overall'] = 'Not available';
                        $newarray[$count]['cefr_score'] = 'Not available';
	                }
                    }else{
                        $newarray[$count]['id'] = $val->id;
                        $newarray[$count]['token'] = $val->token;
                        if (!empty($val->user_name)) {
                            $newarray[$count]['learner_name'] = $val->user_name;
                        } else {
                            $newarray[$count]['learner_name'] = 'Not available';
                        }
                        if (!empty($val->email)) {
                            $newarray[$count]['learner_email'] = $val->email;
                        } else {
                            $newarray[$count]['learner_email'] = 'Unregistered';
                        }
                        if (!empty($val->level)) {
                            $newarray[$count]['level'] = $val->level;
                        }elseif(!empty($val->benchmark_cefr_level)){
                            $newarray[$count]['level'] = $val->benchmark_cefr_level;
                        }else {
                            $newarray[$count]['level'] = 'Not available';
                        }
                        if (isset($val->progress)) {
                            $newarray[$count]['progress'] = round($val->progress) ."%";
                        } else {
                            $newarray[$count]['progress'] = 'Not available';
                        }

                        if (isset($val->course_type) && $val->course_type == "Core") {
                             // practice test results for display in excel
                            if (isset($val->practiceresults) && !empty($val->practiceresults)) {
                                // echo "<pre>"; print_r($val->practiceresults[0]->session_number); die;
                                // practice test1
                                if ((isset($val->practiceresults['0']['session_number'])) && (isset($val->practiceresults['0']['thirdparty_id']))) {
                                    $greenorange_val = $this->gen_practicetest_result($val->practiceresults['0']['session_number'], $val->practiceresults['0']['thirdparty_id'], 'for_export');
                                    if ($greenorange_val >= 7) {
                                        $newarray[$count]['practice_test1'] = 'Ready to move on';
                                    } else {
                                        $newarray[$count]['practice_test1'] = 'More practice required';
                                    }
                                } else {
                                    $newarray[$count]['practice_test1'] = 'Not taken';
                                }
                                // practice test2
                                if ((isset($val->practiceresults['1']['session_number'])) && (isset($val->practiceresults['1']['thirdparty_id']))) {
                                    $greenorange_val = $this->gen_practicetest_result($val->practiceresults['1']['session_number'], $val->practiceresults['1']['thirdparty_id'], 'for_export');
                                    if ($greenorange_val >= 7) {
                                        $newarray[$count]['practice_test2'] = 'Ready to move on';
                                    } else {
                                        $newarray[$count]['practice_test2'] = 'More practice required';
                                    }
                                } else {
                                    $newarray[$count]['practice_test2'] = 'Not taken';
                                }
                            }elseif(isset($val->practice_tests_tds) && !empty($val->practice_tests_tds)){
                                $newarray[$count]['practice_test1'] = $val->practice_tests_tds['practice_msg1'];
								$newarray[$count]['practice_test2'] = $val->practice_tests_tds['practice_msg2'];
                            }else {
                                $newarray[$count]['practice_test1'] = 'Not taken';
                                $newarray[$count]['practice_test2'] = 'Not taken';
                            }

                            // Test Booking date
                            if (!empty($val->start_date_time)) {
                                // $tz_to = $this->data['institutionTierId']['timezone'];
                                $tz_to = $this->institutionTierId['timezone'];
                                $institution_zone_values = @get_institution_zone_from_utc($tz_to, $val->start_date_time, $val->end_date_time);
                                $newarray[$count]['test_booking'] = $institution_zone_values['institute_event_date'];
                            } else {
                                $newarray[$count]['test_booking'] = 'Not available';
                            }

                            if(!empty($val->tds_candidate_id) && $val->tds_course_type == "Core"){
                                // final test results for display in excel - TDS
                                if(!empty($val->tds_data)){
                                   $processed_data = json_decode($val->tds_data, true);
                                    $newarray[$count]['score_out_50'] = $processed_data['total']['score'];
                                    $newarray[$count]['cefr_level'] = $processed_data['overall']['level'];
                                    $newarray[$count]['score_on_cats_scale'] = $processed_data['overall']['score']; 
                                }else{
                                    $newarray[$count]['score_out_50'] = 'Not available';
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                }
                            }else{
                               // final test results for display in excel - COLLEGEPRE
                                if (!empty($val->section_one) && !empty($val->section_two)) {
                                    $section1 = json_decode($val->section_one);
                                    $section2 = json_decode($val->section_two);
                                    $section1_result = $this->process_results($section1->item);
                                    $section2_result = $this->process_results($section2->item);

                                    $questions = count($section1_result) + count($section2_result);
                                    $score = array_sum($section1_result) + array_sum($section2_result);
                                    $score_for_lookup = $score / $questions;
                                    $newarray[$count]['score_out_50'] = $score;
                                } else {
                                    $newarray[$count]['score_out_50'] = 'Not available';
                                }
                                if (!empty($val->section_one) && !empty($val->section_two)) {
                                    $section1 = json_decode($val->section_one);
                                    $section2 = json_decode($val->section_two);
                                    $section1_result = $this->process_results($section1->item);
                                    $section2_result = $this->process_results($section2->item);
                                    $questions = count($section1_result) + count($section2_result);
                                    $score = array_sum($section1_result) + array_sum($section2_result);

                                    $product_level = array('11' => 'A1.1', '12' => 'A1.2', '13' => 'A1.3', '21' => 'A2.1', '22' => 'A2.2', '23' => 'A2.3', '31' => 'B1.1', '32' => 'B1.2', '33' => 'B1.3');
                                    $thirdPartyId = $val->thirdparty_id;
                                    $course_id = substr($thirdPartyId, 10, 2);
                                    $level = $product_level[$course_id];
                                    $for = 'export';
                                    $cefr_val_threshold = $this->get_cefr_threshold($level, $score, $thirdPartyId, $for);
                                    $expploded_val = explode("-", $cefr_val_threshold);
                                    $cefr_val = $expploded_val['0'];
                                    $score_as_string = $expploded_val['1'];

                                    $newarray[$count]['cefr_level'] = $cefr_val;
                                    $newarray[$count]['score_on_cats_scale'] = $score_as_string;
                                } else {
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                } 
                            } 
                        }elseif(isset($val->course_type) && $val->course_type == "Higher") {
                            $newarray[$count]['practice_test1'] = 'Not available';
                            $newarray[$count]['practice_test2'] = 'Not available';
                            // Test Booking date
                            if (!empty($val->start_date_time)) {
                                // $tz_to = $this->data['institutionTierId']['timezone'];
                                $tz_to = $this->institutionTierId['timezone'];
                                $institution_zone_values = @get_institution_zone_from_utc($tz_to, $val->start_date_time, $val->end_date_time);
                                $newarray[$count]['test_booking'] = $institution_zone_values['institute_event_date'];
                            } else {
                                $newarray[$count]['test_booking'] = 'Not available';
                            }
                            $newarray[$count]['score_out_50'] = 'Not available';
                            $higher_bookings = $this->bookingmodel->result_display_higher($val->thirdparty_id);
                            if($higher_bookings != FALSE){
                                if(!empty($higher_bookings['logit_values'])){
                                    $cal_level =  $cal_score  = '';
                                    $base_scores = json_decode($higher_bookings['logit_values']);
                                    $cal_level = $base_scores->overall->level;
                                    $cal_score = $base_scores->overall->score;
                                    if(!empty($cal_level)){
                                        $newarray[$count]['cefr_level'] = substr($cal_level, 0, 2);
                                    }
                                    if(!empty($cal_score)){
                                        $newarray[$count]['score_on_cats_scale'] = $cal_score;
                                    }
                                }else{
                                    $newarray[$count]['cefr_level'] = 'Not available';
                                    $newarray[$count]['score_on_cats_scale'] = 'Not available';
                                }
                            }
                        }
                    }
                    $count ++;
                }
                if(isset($for_export[0]->order_type) && $for_export[0]->order_type == "benchmarking"){
                    $heading_array = array('token' => 'Code','learner_name' => 'Learner name', 'learner_email' => 'Learner email', 'result_date' => 'Date of test', 'test_name' => 'Type of test taken', 'cefr_listening' => 'CEFR level for Listening','listening_score' => 'Position on CATs scale for Listening','cefr_reading' => 'CEFR level for Reading', 'reading_score' => 'Position on CATs scale for Reading','cefr_speaking' => 'CEFR level for Speaking','speaking_score' => 'Position on CATs scale for Speaking','cefr_writing' => 'CEFR level for Writing', 'writing_score' => 'Position on CATs scale for Writing','cefr_overall' => 'CEFR level overall', 'cefr_score' => 'Position on CATs scale for overall');
                }else{
				   $heading_array = array('id' => '#', 'token' => 'Code', 'learner_name' => 'Learner name', 'learner_email' => 'Learner email', 'level' => 'Level', 'progress' => 'Progress', 'practice_test1' => 'Practice test 1', 'practice_test2' => 'Practice test 2', 'test_booking' => 'Test booking', 'score_out_50' => 'Score out of 50', 'cefr_level' => 'CEFR', 'score_on_cats_scale' => 'Score on CATs Step scale');  
                }
                 array_unshift($newarray, $heading_array);
            }

            echo array_to_csv($newarray, 'Codelist of Order ' . $for_export['0']->order_name . '- Date as on-' . date('d-m-Y') . '.csv');
            // echo array_to_csv($newarray, 'Codelist of Order ' . $for_export['0']->order_name . '- Date as on-' . date('d-m-Y') . '.csv');
            exit;
        } else {
            $this->data['tokens'] = '';
            $this->data['token_links'] = '';
        }
    }

    
    //generate practice test results
    public function gen_practicetest_result($test_number = false, $thirdPartyId = false, $purpose = false) {

       //  $this->load->helper('parts');
        if ($thirdPartyId != false && $test_number != false) {

            $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE session_number = "' . $test_number . '"  AND thirdparty_id = "' . $thirdPartyId . '" LIMIT 1');
            if ($query->getNumRows() > 0) {
                $results = $query->getRowArray();

                $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                //user and product info
                $user_app_id = substr($thirdPartyId, 0, 10);
                $course_id = substr($thirdPartyId, 10, 2);
                $attempt_no = substr($thirdPartyId, 12, 2);

                //get part setup
                $part_setups = _part_setup($course_id);
                $green_or_orange = 0;
                foreach ($part_setups as $part):
                    $label = preg_replace("/\d+$/", "", $part['part']);
                    $score = number_format(array_sum(array_slice($score_sections, $part['start'] - 1, $part['length'], true)) / $part['count'], 2);
                    if ($score >= 3) {
                        $green_or_orange = $green_or_orange + 1;
                    }
                    $label_score_arr[] = array($label, $score, ($score >= 3 ) ? '1' : '0');
                endforeach;
                //$labels = json_encode($labels);
                //$scores = json_encode($scores);
                if ($purpose == 'for_export') {
                    return $green_or_orange;
                }
                $data['green_or_orange'] = $green_or_orange;
                $data['results'] = $label_score_arr;


                // $mail_content = $this->parser->parse('site/load_practice_test_results', $data);

                echo  view('site/load_practice_test_results',$data);

               
                       

            } else {
                if ($purpose == 'for_export') {
                    return 0;
                }
                echo 'ThirdParty ID /Test number not found!';
            }
        }
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

        if(empty($parse_section_two_data)){
            $merge_two_sections = array_merge($first_section);
        }else{
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
        }

        
        array_unshift($merge_two_sections, "");
        unset($merge_two_sections[0]);
        return $merge_two_sections;
    }

    function core_certificate($candidate_id = FALSE){

         // $candidate_id =  '5C0F7DFA-3265-383A-34D5-DD5B45959336';
        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $candidate_id)) {

            $builder = $this->db->table('collegepre_results as CR');
            $builder->select('CR.candidate_name,CR.candidate_id,CR.thirdparty_id,CR.section_one,CR.section_two,CR.result_date,B.score_calculation_type_id,B.test_delivary_id,B.logit_values,B.test_delivary_id,B.product_id,B.event_id,E.test_date,E.start_date_time,E.end_date_time,P.name,P.level');
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

                if(!empty($section2)){
                    $section2_result = $this->process_results($section2->item);
                    $questions_count = count($section1_result) + count($section2_result);
                    $score = array_sum($section1_result) + array_sum($section2_result);
                }else{   
                    $questions_count = count($section1_result);
                    $score = array_sum($section1_result);
                }

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
                $graph_data = $this->gengraph($thirdPartyId);
                $data['results'] = array(
                    'result_display' => $result_type,
                    'candidate_name' => $coreresults['candidate_name'],
                    'candidate_id' => $candidate_id,
                    'course_name' => $coreresults['name'],
                    'exam_date' => $examdate,
                    'bar_graph' => $graph_data,
                    'thirdparty_id' => $thirdPartyId,
                    'score' => $score_as_string,
                    'is_supervised' => "Supervised",
                    'cefr_level' => $cefr_val
                );
                echo  view('school/corecertificate_view_school',$data);
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
                $cefr_val = isset($processed_data['overall']['level']) ? $processed_data['overall']['level'] : "";
                $score_as_string = isset($processed_data['overall']['score']) ? $processed_data['overall']['score'] : "";
                $result_type = isset($processed_data['overall']['result_type']) ? $processed_data['overall']['result_type'] : "";
                $tz_to = $this->bookingmodel->get_institution_timezone($candidate_id);
                if(!empty($coreresults['start_date_time'])){
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $coreresults['start_date_time'], $coreresults['end_date_time']);
                    $event_date = date('d F Y', strtotime($institution_zone_values['institute_event_date']));
                }else{
                    $event_date = NULL;
                }
                $examdate = $event_date ? $event_date : date('d-M-Y', strtotime($coreresults['result_date']));
                if($coreresults['pdf_template_version'] == 1){

                    $graph_data = $this->gengraphtds($coreresults['candidate_id']); 
                    $data['results'] = array(
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
                    echo  view('school/corecertificate_view_school',$data);
                }else{
                    $query = $this->db->query('SELECT passing_threshold FROM `result_display_settings` LIMIT 1');
                    $threshold = $query->getRowArray();
                    $result_status = ($threshold['passing_threshold'] <= $processed_data['total']['score'])? "Pass": "Not achieved the level of the test";
                    $result_score = isset($processed_data['total']) ? round(($processed_data['total']['score'] / $processed_data['total']['outof']) * 100)."%": "";
                    $processed_data['listening']['outof'] = $processed_data['listening']['outof'] == 0 ? 1 : $processed_data['listening']['outof'];
                    $processed_data['reading']['outof'] = $processed_data['reading']['outof'] == 0 ? 1 : $processed_data['reading']['outof'];
                    $listening_score = isset($processed_data['listening']) ? round(($processed_data['listening']['score'] / $processed_data['listening']['outof'] ) * 100)."%" : "";
                    $reading_score = isset($processed_data['reading']) ? round(($processed_data['reading']['score'] / $processed_data['reading']['outof']) * 100)."%" : "";

                    $query = $this->db->query('(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID LIMIT 1)
                                                UNION(SELECT id, ability_estimate FROM tds_setting_cefrlevel WHERE cefr_level = "' . $coreresults['level'] . '" ORDER BY ID DESC LIMIT 1)');

                    $tds_cefrlevel = $query->getResultArray();

                    $speaking_ability = isset($processed_data['speaking']) ? $processed_data['speaking']['ability'] : FALSE;
                    $writing_ability = isset($processed_data['writing']) ?  $processed_data['writing']['ability'] : FALSE;
                    if($speaking_ability || $writing_ability){
                       $sp_wr_types= $this->get_level_type($tds_cefrlevel[0]['ability_estimate'], $tds_cefrlevel[1]['ability_estimate'],$speaking_ability,$writing_ability); 
                       
                       if(empty($sp_wr_types['writing'])){
                        $sp_wr_types['writing'] = '';
                       }
                       $core_extend_content = @get_content_core($sp_wr_types['speaking'], $sp_wr_types['writing'], $coreresults['level']);
                       $core_extend_content_speaking = isset($core_extend_content['speaking'])? $core_extend_content['speaking']: "";
                       $core_extend_content_writing = isset($core_extend_content['writing'])? $core_extend_content['writing']: "";
                    }
                    $cefr_all_content = @get_level_contents($result_status, False);
                    $cefr_level_content = @get_level_contents(False, $cefr_val);
                    $data['results'] = array(
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
                    echo  view('school/corecertificate_view_school_extended',$data);
                }
            }
        }else {
            echo 'Not a valid GUID';
        }
    }

    public function process_results($results) {
        if (!empty($results)) {
            $score = array();
            foreach ($results as $key => $val) {

                // countALL($val->question)
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

    public function get_cefr_threshold($level, $score, $testdelivary_id, $for = false) {
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

        //$res_score_settings = $this->placement_model->get_result_display_settings();
        $res_score_settings = $this->bookingmodel->result_display_settings($testdelivary_id);
        $base_scores = unserialize($res_score_settings['logit_values']);

        if ($score >= $res_score_settings['passing_threshold']) {

            if(!empty($base_scores[$level])){
                $cal_score = $base_scores[$level] + $score;
            }else{
                $cal_score =  $score;
            }

            $cal_level = $level;
            return $cal_level . '-' . $cal_score;
        } elseif (($score < $res_score_settings['passing_threshold']) && ($score > $res_score_settings['lower_threshold'])) {
            $key = array_search($level, $cefr_array);
            if ($key > 0) {
                $cal_level = $cefr_array[$key - 1];
                // changes done based on https://catsuk.atlassian.net/browse/WP-526				
                $cal_score = $base_scores[$cal_level] + $res_score_settings['passing_threshold'];
            } else {
                $cal_level = $cefr_array[$key];
                // changes done based on https://catsuk.atlassian.net/browse/WP-512				
                $cal_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
                $cal_score = $score;
            }
            return $cal_level . '-' . $cal_score;
        } else {
            // changes done based on https://catsuk.atlassian.net/browse/WP-512	
            $cal_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
            $cal_score = '';
            if (!empty($for)) {
                if ($for == 'export') {
                    $cal_score = $base_scores[$level] + $score;
                }
            }
            return $cal_level . '-' . $cal_score;
        }
    }

      ///generate GRAPH
      public function gengraph($thirdPartyId = false, $verify_view = false) {

        //$this->load->helper('parts');
        if ($thirdPartyId != false) {
            $query = $this->db->query('SELECT * FROM  collegepre_results WHERE thirdparty_id = "' . $thirdPartyId . '" LIMIT 1');
            
            
            if ($query->getNumRows() > 0) {
                $results = $query->getRowArray();

                if(empty($results['section_two']) && $results['section_two']  == 'null' ){
                    $score_sections = $this->_get_two_sections($results['section_one']);
                }else{
                    $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                }

                // $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                //user and product info
                $user_app_id = substr($thirdPartyId, 0, 10);
                $course_id = substr($thirdPartyId, 10, 2);
                $attempt_no = substr($thirdPartyId, 12, 2);

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
								animation: {
									onComplete: function(animation) {
										var postdata_$thirdPartyId={
											thirdparty_id  : $thirdPartyId,
											file           : $('#graph_" . $thirdPartyId . "')[0].toDataURL()
										}                                                           
										$.post( '" . site_url('school/save_chart_13') . "', postdata_$thirdPartyId)
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


     //TDS Practice Test popup
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
				
				// $mail_content =  $this->parser->parse('site/load_practice_test_results',$data);
                echo  view('site/load_practice_test_results',$data);
			}else{
				echo 'ThirdParty ID /Test number not found!';
            }
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


    public function higher_certificate($candidate_id = false, $token = false) {
        $result_tds_higher = "";
        if($candidate_id && $token){
            //tds higher cerificate view
            $builder = $this->db->table('tds_results');
            $builder->select('users.name as candidate_name,tds_results.processed_data,tds_results.token,tds_results.candidate_id,DATE_FORMAT(tds_results.result_date,"%d %M %Y") as result_date,products.name,events.start_date_time,events.end_date_time,tokens.is_supervised');
            $builder->join('booking', 'tds_results.candidate_id = booking.test_delivary_id');
            $builder->join('tokens', 'tds_results.token = tokens.token');
            $builder->join('events', 'booking.event_id = events.id','left');
            $builder->join('products', 'booking.product_id = products.id');
            $builder->join('users', 'booking.user_id = users.id');
            $builder->where('tds_results.candidate_id', $candidate_id);
            $result =   $builder->get();
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
            if ($this->db->affectedRows() > 0) {
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

        $higher_results_view = array(
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

        $data = array(
            'higher_results_view' => $higher_results_view,
        );

        echo  view('school/highercertificate-view_school',$data);
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

    // WP-1191 - 4 skills benchmarking results statement -certificate shown codes - ##start##
    public function benchmark_certificate($id = false, $token = false) {
        $query = $this->db->query('SELECT * FROM  tds_benchmark_results WHERE candidate_id = "' . $id . '" and token = "' . $token . '" LIMIT 1');
        $results_tds = $query->getRowArray();
        if ($this->db->affectedRows() > 0) {
            $builder = $this->db->table('users');
            $builder->select('users.name,benchmark_session.datetime,tds_benchmark_results.candidate_id,tds_benchmark_results.token');
            $builder->join('tds_benchmark_results', 'users.user_app_id = tds_benchmark_results.candidate_id');
            $builder->join('benchmark_session', 'tds_benchmark_results.token = benchmark_session.token');
            $builder->join('tokens', 'tokens.token = tds_benchmark_results.token');
            $builder->where('tokens.token', $results_tds['token']);
            $builder->where('users.user_app_id', $results_tds['candidate_id']);
            $result = $builder->get();
            $result_users = $result->getRowArray();
        }
        $json_to_array = json_decode($results_tds['processed_data'],true);
        $content_array_level = $this->process_benchmark_skill_content($json_to_array);
        $data['tds_benchmark_results'] = array(
            'id' => $result_users['candidate_id'],
            'token' => $result_users['token'],
            'user_name' => ucfirst($result_users['name']),
            'result_date' => date("d F Y", $result_users['datetime']),
            'benchmark_tds_data' => $json_to_array,
            'lang_content_level' => $content_array_level,
        );       
        echo view('school/benchmarkcertificate-view', $data);
    }

    public function process_benchmark_skill_content($results_benchmark_level) {
        if (!empty($results_benchmark_level)) {
            foreach ($results_benchmark_level as $key => $value){
                $lang_name = $key . '_' .strtolower(substr($value['level'], 0, 2));
                $lang_value[$key][] = lang("app.language_school_benchmark_certificate_level_$lang_name");
            }
            return $lang_value;
        } else {
            return false;
        }
    }

    function core_certificate_pdf($candidate_id = FALSE){
        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $candidate_id)) {
            $builder = $this->db->table('collegepre_results as CR');
            $builder->select('CR.candidate_name,CR.candidate_id,CR.thirdparty_id,CR.section_one,CR.result_date,CR.section_two,B.score_calculation_type_id,B.test_delivary_id,B.logit_values,B.test_delivary_id,B.product_id,B.event_id,E.test_date,E.start_date_time,E.end_date_time,P.name,P.level');
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
                $chartname = $this->efs_charts_results_path . $thirdPartyId. ".png";
                
                //QR generation - WP-1221
                $qr_code_url = $google_url = '';
                $qrcode_params = @generateQRCodePath('school', 'core', $coreresults['candidate_id'], false);

                if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                    $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                    $qr_result = json_decode($qrcode);
                    $qr_code_url = $qr_result->qrcode_abs;
                    $google_url = $qr_result->url;
                }

                $data['values_core_pdf'] = array(
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
                @generatecoreResultsPDF($data['values_core_pdf']);
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
                $cefr_val = isset($processed_data['overall']['level']) ? $processed_data['overall']['level'] : "";
                $score_as_string = isset($processed_data['overall']['score']) ? $processed_data['overall']['score'] : "";
                $result_type = isset($processed_data['overall']['result_type']) ? $processed_data['overall']['result_type'] : "";
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

                        // Pdf generation
                         $this->gengraphtdspdf($score_sections, $coreresults['candidate_id']);
                        // $this->gengraphtds($coreresults['candidate_id']);
                    }
                }   

                //QR generation - WP-1221
                $qr_code_url = $google_url = '';
                $qrcode_params = @generateQRCodePath('school', 'core', $coreresults['candidate_id'], false);
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
                    $result_status = ($threshold['passing_threshold'] <= $processed_data['total']['score'] ? $processed_data['total']['score'] : "")? "Pass": "Not achieved the level of the test";
                    $result_score = isset($processed_data['total']) ? round(($processed_data['total']['score'] / $processed_data['total']['outof']) * 100)."%": "";
                    $processed_data['listening']['outof'] = $processed_data['listening']['outof'] == 0 ? 1 : $processed_data['listening']['outof'];
                    $processed_data['reading']['outof'] = $processed_data['reading']['outof'] == 0 ? 1 : $processed_data['reading']['outof'];
                    $listening_score = isset($processed_data['listening']) ? round(($processed_data['listening']['score'] / $processed_data['listening']['outof'] ) * 100)."%" : "";
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

                    $data['values_core_pdf'] = array(
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
                    @generatecoreextendedResultsPDF($data['values_core_pdf']);
                }
            }
        }else {
            echo 'Not a valid GUID';
        }
    }

    public function genqrcode($short_url = false, $file_abs_path = false) {
        if($short_url != false && $file_abs_path != false){
            $params['data'] = $short_url;
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH . $file_abs_path;
            $this->Ciqrcode->generate($params);
            $success_data = array('code' => 1000, 'url' => $short_url, 'qrcode' => $file_abs_path, 'qrcode_abs' => $file_abs_path, 'message' => 'QR code generated');
            return json_encode($success_data, JSON_PRETTY_PRINT);
        }else{
            $error_data = array('code' => 1002, 'message' => 'Not a valid GUID');
            return json_encode($error_data,JSON_PRETTY_PRINT);
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


    /** WP-1279
     * Function to generate a graph for Core learner and saved as image
     * @param array $score_sections
     * @param integer $thirdPartyId
     */
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


    public function save_chart_13() {
        
        // Interpret data uri
        $uriPhp = 'data://' . substr($this->request->getPost('file'), 5);
        // Get content
        $binary = file_get_contents($uriPhp);
        $file = $this->efs_charts_results_path .  $this->request->getPost('thirdparty_id') . '.png';
        // Save image
        file_put_contents($file, $binary);
        return 1;
    }

    // WP-1191 - 4 skills benchmarking results statement -certificate shown codes - ##ends##
    // WP-1276 - Higher results process - CATs TDS
    public function higher_certificate_pdf($candidate_id = False, $token = False) {
        $values_higher_pdf = $this->process_results_higher($candidate_id,$token);
        //QR generation - WP-1221
        $qr_code_url = $google_url = '';
        $qrcode_params = @generateQRCodePath('school', 'higher', $candidate_id, $token, false);
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

   // redirect to teacher file
   public function teacher()
   {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
       header('Content-Type: application/json');

       if($this->request->getPost('teacher_id') && $this->request->getPost('teacher_id') != '')
       {
           $teacher_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('teacher_id')));
           $data = array(
               'teacher_title' => lang('app.language_school_viewedit_teacher'),
               'teacher_heading' => lang('app.language_school_viewedit_teacher'),
               'teacher' => $this->usermodel->get_teacher($teacher_id),
           );
       }else{
           $data = array(
               'teacher_title' => lang('app.language_school_add_teacher'),
               'teacher_heading' => lang('app.language_school_add_teacher'),
           );
       }
       $arrayData = array('success' => 1, 'html' => view('school/teacher',$data));
       echo json_encode($arrayData);
       die;
   }    


   function postteacher()
   {
       
        $this->session = \Config\Services::session();

       if (!$this->acl_auth->logged_in()) {
           return redirect()->to('admin/login');
       }
       header('Content-Type: application/json');

       if (null !== $this->request->getPost()) {

           if(!empty($this->request->getPost('teacher_id')))
           {
               $id =  $this->encrypter->decrypt(base64_decode($this->request->getPost('teacher_id')));
               $teacher = $this->usermodel->get_teacher($id);
           }else{
              $id = $this->request->getPost('teacher_id');
              $teacher = $this->usermodel->get_teacher($id);
           }

           $rules = [
                'firstname' => [
                    'label'  => lang('app.language_admin_institutions_first_name'),
                    'rules'  => 'trim|required|max_length[50]|regex_match[^[a-zA-Z -]+$]',
                    'errors' => [
                        'regex_match' => lang('app.language_teacher_name_check'),
                    ]
                ],
                'lastname' => [
                    'label'  => lang('app.language_admin_institutions_second_name'),
                    'rules'  => 'trim|required|max_length[50]|regex_match[^[a-zA-Z -]+$]',
                    'errors' => [
                        'regex_match' => lang('app.language_teacher_name_check'),
                    ],
                ],
                'department' => [
                    'label'  => lang('app.language_admin_institutions_department'),
                    'rules'  => 'trim|max_length[100]|regex_match[^[0-9:a-zA-Z() _-]+$|^$]',
                    'errors' => [
                        'regex_match' => lang('app.language_admin_institute_department_check'),
                    ],
                ],
           ];

            if (isset($teacher) && $teacher['email'] == $this->request->getPost('email')):

           else:
                $rules['email'] = [
                    'label'  => lang('app.language_admin_institutions_email_address'),
                    'rules'  => 'required|max_length[100]|is_unique[users.email]|isemail_check',
                    'errors' => [
                        'isemail_check' => lang('app.form_validation_valid_email'),
                    ]
                    ];
                $rules['confirm_email'] = [
                    'label'  => lang('app.language_admin_institutions_confirm_email_address'),
                    'rules'  => 'required|max_length[100]|matches[email]|isemail_check',
                    'errors' => [
                        'matches' => 'The email and its confirm email are not the same',
                        'isemail_check' => lang('app.form_validation_valid_email'),
                    ],
                    ];
           endif;

           if (!$this->validate($rules)) {
               $response['success'] = 0;
               $errors = array(
                   'firstname'         => $this->validation->showError('firstname'),
                   'lastname'          => $this->validation->showError('lastname'),
                   'department'        => $this->validation->showError('department'),
                   'email'             => $this->validation->showError('email'),
                   'confirm_email'     => $this->validation->showError('confirm_email'),
               );
               $response['errors'] = $errors;
               echo json_encode($response);die;
           }else{

               // Validation
               $datainstitutes = array(
                   'name' => $this->request->getPost('firstname') . ' ' . $this->request->getPost('lastname'),
                   'firstname' => $this->request->getPost('firstname'),
                   'lastname' => $this->request->getPost('lastname'),
                   'department' => $this->request->getPost('department'),
                   'email' => $this->request->getPost('email')
               );

               $random_string = $this->generate_random_string(25);
               $activation_date = time();
               $expiration_date = strtotime("+7 day", $activation_date);

                if (isset($teacher) && !empty($teacher)) {

                   //update teacher
                    if ($this->usermodel->update_teacher($id, $datainstitutes)) {
                        if (isset($id) && !empty($id)) {
                           $logged_tier_user = $this->session->get('logged_tier1_userid');
                           $last_modified_user = isset($logged_tier_user) && !empty($logged_tier_user) ? $logged_tier_user : $this->session->get('user_id');
                           $data = array('last_modified_by' => $last_modified_user);
                           $builder = $this->db->table('institution_teachers');
                           $builder->update($data, ['teacherId' => $id]);
                        }
                        //send mail to institute while changing new email address
                        if ($id != '' && trim($this->request->getPost('email')) != trim($teacher['email']))
                        {
                            //activation details
                            $dataActivation = array('activation_code' => $random_string, 'activation_date' => $activation_date, 'expiration_date' => $expiration_date);
                            $builder = $this->db->table('users');
                            $builder->update($dataActivation, ['id' => $id]);

                            $dataMails = array('name' => $datainstitutes['firstname'] . ' ' . $datainstitutes['lastname'], 'mailto' => $datainstitutes['email'], 'link' => site_url('login/password_setup') . '/' . $random_string);
                            @$this->send_mail_to_teacher($dataMails);
                        }
                           //WP-1391 zendesk Teacher profile update
                        if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                            $user_id = $id;
                            zendesk_profile_update($user_id,"School Supervisor Edit");   
                        }
                       $this->session->setFlashdata('messages', lang('app.language_school_teacher_updated_success_msg'));
                       $dataSuccess = array('success' => 1, 'msg' => 'Teacher updated');
                       echo json_encode($dataSuccess);

                   } else {
                       $this->session->setFlashdata('errors', lang('app.language_school_teacher_nothing_to_update_msg'));
                       $dataFailure = array('success' => 1, 'msg' => 'Teacher not updated');
                       echo json_encode($dataFailure);
                       die;
                   }
               }else{
                   //insert institute
                   $teacher_id = $this->usermodel->insert_teacher($datainstitutes);
                   if ($teacher_id > 0) 
                   {
                       //activation details
                       $dataActivation = array('activation_code' => $random_string, 'activation_date' => $activation_date, 'expiration_date' => $expiration_date);
                       $builder = $this->db->table('users');
                       $builder->update($dataActivation, ['id' => $teacher_id]);

                       // Send Email
                       $dataMails = array('name' => $datainstitutes['firstname'] . ' ' . $datainstitutes['lastname'], 'mailto' => $datainstitutes['email'], 'link' => site_url('login/password_setup') . '/' . $random_string);
                       @$this->send_mail_to_teacher($dataMails);
                       //  End Email

                       //WP-1391 zendesk user add Teacher
                       if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                            $user_id = $teacher_id;
                            zendesk_profile_update($user_id,"School Supervisor Add");   
                        }
            
                       $this->session->setFlashdata('messages', lang('app.language_school_teacher_added_success_msg'));

                       $dataSuccess = array('success' => 1, 'msg' => 'Teacher inserted');
                       echo json_encode($dataSuccess);
                       die;
                   } else {
                       $this->session->setFlashdata('errors', lang('app.language_school_teacher_added_failure_msg'));
                       $dataFailure = array('success' => 0, 'msg' => 'Teacher not inserted');
                       echo json_encode($dataFailure);
                       die;
                    }
               }
           }
       } 
       else {
           $dataFailute = array('success' => 0, 'msg' => 'No post request made!');
           echo json_encode($dataFailute);
       }
   }



    // Sending Email
    function send_mail_to_teacher($emailDetails = FALSE) {

        if ($emailDetails != FALSE){

            $name = $emailDetails['name'];
            $mail = $emailDetails['mailto'];
            $link = site_url('login/password_setup') . '/' . $random_string;
            $builder = $this->db->table('users'); 
            $query   = $builder->getWhere(['id' => 1])->getRow(); 
            $to = $query->email; 
            $template_email = $this->emailtemplatemodel->get_template_contents('supervisor-mail-activation',$this->request->getLocale());
            $template_email_new = $this->email_lib('supervisor-mail-activation');
            $label = array("##NAME##", "##LINK##");
            $email_values = array($emailDetails['name'], $emailDetails['link']);
            $replaced_content = str_replace($label, $email_values,$template_email_new);
            $mail_message = $replaced_content;
            $config = @get_email_config_provider(4);

            if(isset($config['smtp_user']) && $config['smtp_user'] == "Api-Key:"){
                $sendSmtpEmail['subject'] = $template_email['0']->subject;
                $sendSmtpEmail['htmlContent'] = $mail_message;
                $sendSmtpEmail['sender'] = array('name' => $template_email['0']->display_name, 'email' => $template_email['0']->from_email);
                $sendSmtpEmail['to'] = array(array('email' => $emailDetails['mailto']));
                $data = json_encode($sendSmtpEmail, JSON_HEX_QUOT | JSON_HEX_TAG);
                $response_data = @email_sendinblue(json_encode($sendSmtpEmail),$config);
                $response = json_decode($response_data);
                if(isset($response->messageId) && !empty($response->messageId)){
                    $sent_mail_status = true;
                    $sent_mail_log = $response_data;
                }else{
                    $sent_mail_status = false;
                    $sent_mail_log = $response_data;
                }
            }else{ 

            $this->email->initialize($config);
            $this->email->setFrom('noreply@catsstep.education', 'CATs Step Team');
            $this->email->setTo($emailDetails['mailto']);
            $this->email->setMailtype("html");
            $this->email->setNewline("\r\n");
            $this->email->setCrlf("\r\n");
            $this->email->setSubject($template_email['0']->subject);
            $this->email->setMessage($mail_message);

            if ($this->email->send()) {
                $sent_mail_status = true;
                $sent_mail_log = 'success';
            }else{
                $sent_mail_status = false;
                $sent_mail_log = json_encode($this->email->printDebugger());  
            }

            }

       //log tables affected from here
        $mail_log = array(
            'from_address' => $template_email['0']->from_email,
            'to_address' => $emailDetails['mailto'],
            'response' => $sent_mail_log,
            'status' => $sent_mail_status ? 1 : 0,
            'purpose' => $template_email['0']->subject
        );
        $builder = $this->db->table('email_log'); 
        $builder->insert($mail_log);

        }
    }


    //remove teacher from institute
    function remove_teacher() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }        
        header('Content-Type: application/json');
        if (null !== $this->request->getPost() && $this->request->getPost('teacher_id')) {

            $teacherId = $this->encrypter->decrypt(base64_decode($this->request->getPost('teacher_id')));
            $teacherData = $this->usermodel->get_teacher($teacherId);
            //WP-1380 Start 
            if($this->yellowfin_access == 1){
                @yellowfin($teacherId);
            }
            //WP-1380 end
            $teacherClassData = $this->teachermodel->get_teacher_classes($teacherData['institutionTeacherId']);
            $this->db->transStart();
            if (isset($teacherClassData) && !empty($teacherClassData)) {
                foreach ($teacherClassData as $classD){

                    $builder = $this->db->table('classes');
                    $builder->where(array('classId' => $classD->classId));
                    $builder->delete();

                    $builder = $this->db->table('student_classes');
                    $builder->where(array('teacherClassId' => $classD->teacherClassId));
                    $builder->delete();

                    $builder = $this->db->table('teacher_classes');
                    $builder->where(array('teacherClassId' => $classD->teacherClassId));
                    $builder->delete();
                }
            }
            $builder = $this->db->table('user_roles');
            $builder->where(['users_id' => $teacherData['id']]);
            $deleteTeacher = $builder->delete();

            $builder = $this->db->table('users');
            $builder->where(['id' => $teacherData['id']]);
            $deleteTeacher = $builder->delete();
            if ($deleteTeacher > 0) {
                $this->db->transComplete();
                //WP-1391 zendesk Teacher delete
                if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                        $user_id = $teacherData['id'];
                        @zendesk_user_delete($this->zendesk_domain_url,$user_id,"Delete","Supervisor Delete");  
                }  
                $this->session->setFlashdata('messages', lang('app.language_school_teacher_removed_success_msg'));
                echo json_encode(array('success' => 1, 'msg' => 'Removed from institute'));
            } else {
                $this->session->setFlashdata('errors', lang('app.language_school_teacher_removed_failure_msg'));
                echo json_encode(array('success' => 0, 'msg' => 'Not removed from institute'));
            }
        }
    }

    public function email_lib($slug = NULL){
        $parser = \Config\Services::renderer();

        if(!empty($slug)){
            
            $getEmailContent = $this->usermodel->get_email_contect($slug);
            $this->data['email_content'] = $getEmailContent[0]->content;
            $test_data = $parser->setData($this->data)->renderString($this->data['email_content']);
            return $test_data;
        }
    }


    // WP-1191 - 4 skills benchmarking results statement - pdf shown codes - ##starts##
    public function benchmark_certificate_pdf() {
        $id = base64_decode($this->request->getPostGet('q'));
        $token = base64_decode($this->request->getPostGet('t'));
        $values_bench_pdf = $this->process_results_benchmark($id,$token);
        //QR generation - WP-1221
        $qr_code_url = $google_url = '';
        $qrcode_params = @generateQRCodePath('school', 'benchmark', $id, $token);
        if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
            $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);                
            $qr_result = json_decode($qrcode);
            $qr_code_url = $qr_result->qrcode_abs;
            $google_url = $qr_result->url;
        } 
        $this->data['pdf_download_results'] = array(
            'data' => $values_bench_pdf,
            'qr_code_url' => $qr_code_url,
            'google_url' => $google_url,
        );
        @generatebenchmarkResultsPDF($this->data['pdf_download_results']);
    }

    public function process_results_benchmark($id = false, $token = false) {
        $query = $this->db->query('SELECT * FROM  tds_benchmark_results WHERE candidate_id = "' . $id . '" and token = "' . $token . '" LIMIT 1');
        $results_tds = $query->getRowArray();

        if ($query->getNumRows() > 0) {
            $builder = $this->db->table('users');
            $builder->select('users.name,benchmark_session.datetime,tds_benchmark_results.candidate_id');
            $builder->join('tds_benchmark_results', 'users.user_app_id = tds_benchmark_results.candidate_id');
            $builder->join('benchmark_session', 'tds_benchmark_results.token = benchmark_session.token');
            $builder->join('tokens', 'tokens.token = tds_benchmark_results.token');
            $builder->where('tokens.token', $results_tds['token']);
            $builder->where('users.user_app_id', $results_tds['candidate_id']);
            $result = $builder->get();
            $result_users = $result->getRowArray();
        }
        $json_to_array = json_decode($results_tds['processed_data'],true);
        $content_array_level = $this->process_benchmark_skill_content($json_to_array);
        $this->data['pdf_benchmark_results'] = array(
            'id' => $result_users['candidate_id'],
            'user_name' => ucfirst($result_users['name']),
            'result_date' => date("d F Y", $result_users['datetime']),
            'benchmark_tds_data' => $json_to_array,
            'lang_content_level' => $content_array_level,
        );
        return $this->data['pdf_benchmark_results'];
    }


    public function u13_learners() {
        $u13learner_search_item = (isset($_GET['u13learner_search']) && $_GET['u13learner_search'] != '') ? $_GET['u13learner_search'] : '';
       
        //pagination
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $this->usermodel->record_u13_learners_count(trim($u13learner_search_item));
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_u13_learners');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }
        $data = array(
            'u13learner_search_item' => $u13learner_search_item,
            'u13_learners' => $this->usermodel->fetch_u3learners($perPage, $offset, trim($u13learner_search_item)),
            'pager' => $pager
        );

        /* Course progress for U13 learners */
        if (!empty($data['u13_learners'])) {
            $i = 0;
            foreach ($data['u13_learners'] as $tokens) {
                if ($tokens['user_app_id'] > 0) {
                    //get history of userproducts
                    $num_history_query = $this->db->query('SELECT id FROM  user_products WHERE user_id = "' . $tokens['id'] . '" ');

                    $num_history_results = $num_history_query->getNumRows();
                    if ($num_history_results > 0) {
                        $data['u13_learners'][$i]['num_history_results'] = $num_history_results;
                    } else {
                        $data['u13_learners'][$i]['num_history_results'] = 0;
                    }
                   
                }
                $i++;
            }
            $data['u13_learners'] = $data['u13_learners'];
        }

        return $data;
    }

    //get venue
	public function venue(){
	    if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
	    }
	    if (null !==  $this->request->getPost() &&  $this->request->getPost('venue_id') &&  $this->request->getPost('venue_id')!= '') {
			$venue_id =  $this->encrypter->decrypt(base64_decode($this->request->getPost('venue_id')));
	        $data = array(
	            'distributor_title' => lang('app.language_distributor_update_venue'),
	            'distributor_heading' => lang('app.language_distributor_update_venue'),
	            'venuedatas' => $this->venuemodel->get_venue($venue_id),
                'venueCountry' => $this->venuemodel->fetch_insitution_country(),
	        );
	    }else{
	        $data = array(
	            'distributor_title' => lang('app.language_distributor_add_venue'),
	            'distributor_heading' => lang('app.language_distributor_add_venue'),
                'venueCountry' => $this->venuemodel->fetch_insitution_country(),
	        );
	    }
	    $arrayData = array('success' => 1, 'html' => view('school/venue', $data));
	    echo json_encode($arrayData);
	    die;
	}

    //check venue
	public function checkvenue() {
	    if ($this->request->getPost('venue_id') != '' && $this->request->getPost('venue_name') != '') {
	        $venue_details = $this->venuemodel->get_venue($this->request->getPost('venue_id'), slugify($this->request->getPost('venue_name')));
	        if (!empty($venue_details)) {
	            $isAvailable = true;
	        } else {
	            $venue_details = $this->venuemodel->get_venue($id = FALSE, slugify($this->request->getPost('venue_name')));
	            if (!empty($venue_details)) {
	                $isAvailable = false;
	            } else {
	                $isAvailable = true;
	            }
	        }
	    } elseif ($this->request->getPost('venue_name') != '') {
	        $venue_details = $this->venuemodel->get_venue($id = FALSE, slugify($this->request->getPost('venue_name')));
	        if (!empty($venue_details)) {
	            $isAvailable = false;
	        } else {
	            $isAvailable = true;
	        }
	    }
	    echo json_encode(array(
	        'valid' => $isAvailable,
	        'data' => $venue_details
	    ));
	}

    public function check_event_venue() {
        $venueId = $this->encrypter->decrypt(base64_decode($this->request->getPost('venue_id')));
        $current_utc_data = @get_current_utc_details();
		$current_utc_timestamp = $current_utc_data['current_utc_timestamp'];
        $values = $this->venuemodel->check_exists_venue_in_event_alert(intval($venueId),$current_utc_timestamp);
        echo json_encode(array('success' => $values));die;
    }

    public function city_check() {
        if ($this->request->getPost('city')) {
            if (!preg_match("/^[a-z\s]+$/i", $this->request->getPost('city'))) {
                $isAvailable = false;
            } else {
                $isAvailable = true;
            }
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }

    public function firstname_check() {    
	    if ($this->request->getPost('firstname')) {
	        if (!preg_match("/^[a-zA-Z0-9 ]+$/", $this->request->getPost('firstname'))) {
	            $isAvailable = false;
	        } else {
	            $isAvailable = true;
	        }
	        echo json_encode(array(
	            'valid' => $isAvailable,
	        ));
	    }
	}

    public function lastname_check() {
        if ($this->request->getPost('lastname')) {
            if (!preg_match("/^[a-zA-Z0-9 ]+$/", $this->request->getPost('lastname'))) {
                $isAvailable = false;
            } else {
                $isAvailable = true;
            }
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }


    	//add/update venue
	public function postvenue() {
	    if (null !== $this->request->getPost()) {
	        $country = $this->venuemodel->fetch_insitution_country();
            $datavenues = array(
                'distributor_id' => 0,
                'venue_name' => $this->request->getPost('venue_name'),
                'venue_slug' => slugify($this->request->getPost('venue_name')),
                'address_line1' => $this->request->getPost('address_line1'),
                'address_line2' => $this->request->getPost('address_line2'),
                'city' => $this->request->getPost('city'),
                'country' => $country[0]->countryCode,
                'area_code' => $this->request->getPost('area_code'),
                'first_name' => $this->request->getPost('firstname'),
                'last_name' => $this->request->getPost('lastname'),
                'email' => $this->request->getPost('email'),
                'contact_no' => $this->request->getPost('contact_no'),
                'location_URL' => $this->request->getPost('location_URL'),
                'notes' => $this->request->getPost('notes')
            );
            $venue_id = $this->request->getPost('venue_id');
			if(isset($venue_id) && $venue_id != ''){
				$venue = $this->venuemodel->get_venue($venue_id);
			}
			
            if (isset($venue) && !empty($venue)) {
                //update to venue
                if($this->venuemodel->update_venue( $venue_id, $datavenues )){
                     if (isset($venue_id) && !empty($venue_id)) {
                        $logged_tier_user = $this->session->get('logged_tier1_userid');
                        $last_modified_user = isset($logged_tier_user) && !empty($logged_tier_user) ? $logged_tier_user : $this->session->get('user_id');
                        $data = array('last_modified_by' => $last_modified_user);
                            $builder = $this->db->table('venue_institution');
                            $builder->where('venue_id', $venue_id);
                            $builder->update($data);
                        }
                    $this->session->setFlashdata( 'messages', lang('app.language_distributor_venue_updated_success_msg'));
                    $dataSuccess = array('success' => 1, 'msg' => 'updated successfully');
                    echo json_encode($dataSuccess);
                    die;
                } else{
                    $dataSuccess = array('success' => 1, 'msg' => 'Not updated');
                    echo json_encode($dataSuccess);
                    die;
                }
            } else{
				//insert to venue
				$venueid = $this->venuemodel->insert_venue($datavenues);
                if($venueid != null){
                    $dataInstitution = array(
                        'venue_id' => $venueid,
                        'institution_user_id' => $this->session->get('user_id'),
                        'created_by' => $this->session->get('logged_tier1_userid')
                    );
                    $this->venuemodel->insert_venue_institution($dataInstitution);
                    $this->session->setFlashdata('messages', lang('app.language_distributor_venue_added_success_msg'));
                    $dataSuccess = array('success' => 1, 'msg' => 'Venue inserted');
                    echo json_encode($dataSuccess);
                    die;
                }else{
                    $this->session->setFlashdata('errors', lang('app.language_distributor_venue_added_failure_msg'));
                    $dataFailure = array('success' => 1, 'msg' => 'Venue not inserted');
                    echo json_encode($dataFailure);
                    die;
                }
            }
	    } else{
	        $dataFailute = array('success' => 1, 'msg' => 'No post request made!');
	        echo json_encode($dataFailute);
	    }
	}

    //remove venue from institute
	public function remove_venue() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/'));
	    }
        if (null !== $this->request->getPost() && $this->request->getPost('venue_id')) {
            $venue_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('venue_id')));
            if(!empty($venue_id)){
                $result = $this->db->query('SELECT * FROM events WHERE venue_id ='.intval($venue_id));
                $count_events = count($result->getResult());            
                if($count_events > 0){
                    $builder = $this->db->table('events');
                    $builder->set('status', '0', false);
                    $builder->where('venue_id', $venue_id);
                    $builder->update();
                }
                $builder = $this->db->table('venues');
                $builder->where('id', $venue_id);
                $builder->delete();
                $builder = $this->db->table('venue_institution');
                $builder->where('venue_id', $venue_id);
                $builder->delete();
            }
            if ($this->db->affectedRows() > 0) {
                $this->session->setFlashdata('messages', lang('app.language_distributor_venue_deleted_success_msg'));
                echo json_encode(array('success' => 1, 'msg' => 'Removed from institute'));
                die;
            } else {
                $this->session->setFlashdata('errors', lang('app.language_school_teacher_removed_failure_msg'));
                echo json_encode(array('success' => 0, 'msg' => 'Not removed from institute'));
                die;
            }
        }
	}

    function class_learners() {
      
        $teacher_class_id = (isset($_GET['teacher_class_id']) && $_GET['teacher_class_id'] != '') ? $_GET['teacher_class_id'] : '';
    
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $this->schoolmodel->record_class_learners_count(trim($teacher_class_id));
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_class_learners');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }
        $data = array(
            'class_learners' => $this->schoolmodel->get_class_learner_details($perPage, $offset, trim($teacher_class_id)),
            'class_learners_links' => $pager
        );
        /* Course progress for U13 learners */
        if (!empty($data['class_learners'])) {
            $i = 0;
            foreach ($data['class_learners'] as $tokens) {
                if ($tokens['user_app_id'] > 0) {
                    //get history of userproducts
                    $num_history_query = $this->db->query('SELECT id FROM  user_products WHERE user_id = "' . $tokens['id'] . '" ');

                    $num_history_results = $num_history_query->getNumRows();
                    if ($num_history_results > 0) {
                       $data['class_learners'][$i]['num_history_results'] = $num_history_results;
                    } else {
                        $data['class_learners'][$i]['num_history_results'] = 0;
                    }
                }
                $i++;
            }
            $data['class_learners'] = $data['class_learners'];
        }
        return $data;
    }

    public function listevents()
	{
        
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
        
        if($this->request->getPost('status') == 'active'){
            $this->session->set('event_list', '0');
            $datares= array("events_show_condition" => "1", "url" => site_url("school/dashboard"));
            echo json_encode($datares);
            exit;
        }elseif($this->request->getPost('status') == 'inactive'){
            $this->session->set('event_list', '1');
            $datares= array("events_show_condition" => "0", "url" => site_url("school/dashboard"));
            echo json_encode($datares);
            exit;
        }else{
            $events_show_condition = 0;
        }
        if($this->session->get('event_list')){
            $events_show_condition = $this->session->get('event_list');
        }else{
            $events_show_condition = 0;
        }
        $tz_to = $this->institutionTierId['timezone'];
        $currentdatetime = new DateTime("now", new DateTimeZone($tz_to));
        $currentzonetime_date = $currentdatetime->format('Y-m-d');
        $currentzonetime_time = $currentdatetime->format('H:i');
        $currentime_to_dbtime =  $this->get_UTC_time($tz_to, $currentzonetime_time);
        $current_zone_timestamp = $currentzonetime_date;
        $current_utc_data = @get_current_utc_details();
        $current_utc_timestamp = $current_utc_data['current_utc_timestamp'];
        //Current UTC details ends

        //pagination
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $dashboard_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $dashboard_segment + 2;
        $pager = "";
        $total = $this->eventmodel->record_count($events_show_condition,$current_utc_timestamp);
        if($total > 10){
        $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
        $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_list_events');
        $offset = $page * $perPage;
        $pager = $this->pager;
        }
        $listevents = $this->eventmodel->fetch_events($perPage, $offset ,$events_show_condition,$current_utc_timestamp);
        $count_allocated_learners = array();
        if($listevents != '' && $listevents){
            foreach($listevents as $list){
                $count_allocated_learners[$list["id"]] = $this->eventmodel->fetch_event_allocated_learners($list["id"]);   
            }
        }
        
        $dist_id = $this->session->get('user_id');
        $venues = $this->eventmodel->get_venues($dist_id);
        $test_events_data = array(
            'event_title' => lang( 'app.language_school_event_list_test_title' ),
            'event_heading' => lang('app.language_school_event_list_test_title'),
            'results' => $listevents,
            'venues' => count($venues),
            'addedLearnersCount' => $count_allocated_learners,
            'pager' => $pager
        );
        return $test_events_data;
	}

    public function get_UTC_time($timezone, $time){
        $tz_to = 'UTC';
        $format = 'H:i';
        $t = new DateTime($time, new DateTimeZone($timezone));
        $t->setTimeZone(new DateTimeZone($tz_to));
        return $time = $t->format($format);
    }

    //Sprint-38 - Event tab in Insttitution code starts
    public function addevent(){
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
        $user_id = $this->session->get('user_id');
        $venues = $this->eventmodel->get_venues($user_id);
                $product_group_ids  = $this->data['product_eligiblity'];
                $eligible_product_name = $this->eventmodel->get_eligible_productname($product_group_ids);
        if(empty($venues)){
            $this->session->setFlashdata('error', lang('app.language_distributor_no_venue_available_msg'));
            return redirect()->to('school/listevents');
        }
        $venue_array = array();
        $venue_array[''] = 'Select';
        foreach ($venues as $v_key => $v_val){
            $venue_array[$v_val->id] = $v_val->venue_name;
        }

        $data = array(
        'distributor_title' => lang('app.language_school_event_add_test_title'),
        'distributor_heading' => lang('app.language_school_event_add_test_title'),
        'venues' => $venue_array,
        'eligible_product_name' => $eligible_product_name,
        'product_groups' => $this->cmsmodel->get_product_group(),
        'products' => $this->eventmodel->get_products_for_events(),
        'eligible_version_products' => $this->eventmodel->get_products_for_events_version(),
        'group_tds_type' => $this->tds_allocated_type(),
        'product_by_groups' => $this->eventmodel->get_product_by_group()
        );

        if($this->request->getPost()){
            $rules =[
            'product_group_name' => [
                'label'  => 'product Group',
                'rules'  => 'trim|required',
                ],
                'testdate' => [
                'label' => lang('app.language_school_event_test_date'),
                'rules' => 'trim|required',
                ],
                'starttime' => [
                'label' => lang('app.language_school_event_start_time'),
                'rules' => 'trim|required',
                ],
                'endtime' => [
                'label' => lang('app.language_school_event_end_time'),
                'rules' => 'trim|required',
                ],
                'capacity' => [
                'label' => lang('app.language_school_event_capacity'),
                'rules' => 'trim|required',
                ],
                'venue_id' => [
                'label' => lang('app.language_school_label_venue_name'),
                'rules' => 'trim|required',
                ]
            ];

            if ($this->request->getPost('product_group_values') === NULL) {
                $rules = [
                    'product_group_values' => [
                    'label'  => lang('app.language_school_event_please_choose_event_validation'),
                    'rules'  => 'trim|required',
                    ] 
                ];
            }
            
            if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = array(
                'product_group_name' => $this->validation->showError('product_group_name'),
                'product_group_values' => $this->validation->showError('product_group_values'),
                'starttime' => $this->validation->showError('starttime'),
                'endtime' => $this->validation->showError('endtime'),
                'capacity' => $this->validation->showError('capacity'),
                'venue_id' => $this->validation->showError('venue_id')
            );
                $response['errors'] = $errors;
                echo json_encode($response);
                die;
            } else{
                $venue_email = $this->fetch_venue_email($this->request->getPost('venue_id'));
                $product_group_name = $this->request->getPost('product_group_name');
                $product_group_values = $this->request->getPost('product_group_values');
                $tz_from =  $this->institutionTierId['timezone']; 
                if($this->request->getPost('testdate')){
                    $testdate_array = strtotime($this->request->getPost('testdate'));
                                $testdate = date('Y-m-d', $testdate_array);
                }
                            
                //convert date&time into UTC
                $start_date_time = $this->utc_start_time($tz_from,$testdate,$this->request->getPost('starttime'));
                $end_date_time = strtotime(EVENT_END_TIME_ADD_HOUR, $start_date_time);
                
                if($product_group_name == "Primary"){
                    $tds_option = $this->request->getPost('primary_tds_type');
                }if($product_group_name == "Core"){
                    $tds_option = $this->request->getPost('core_tds_type');
                }if($product_group_name == "Higher"){
                    $tds_option = $this->request->getPost('higher_tds_type');
                }
                //insert event
                $dataevents = array(
                    'distributor_id' => 0,
                    'start_date_time' => $start_date_time,
                    'end_date_time' => $end_date_time,
                    'venue_id' => $this->request->getPost('venue_id'),
                    'email' => !empty($venue_email['email'])? $venue_email['email'] : " ",
                    'capacity' => $this->request->getPost('capacity'),
                    'fixed_capacity' => $this->request->getPost('capacity'),
                    'notes' => trim($this->request->getPost('notes')),
                                    'tds_option' => $tds_option
                );
                $eventid = $this->eventmodel->insert_event($dataevents);
                            // insert event products
                if($eventid){
                    $count = 0;
                    foreach($product_group_values as $val) {
                        $event_products[$count]['event_id'] = $eventid;
                        $event_products[$count]['product_id'] = $val;
                        $count ++;
                    }
                    $this->eventmodel->insert_event_products($event_products);
                    $event_data = array(
                        'event_id' => $eventid,
                        'institution_user_id' => $this->session->get('user_id'),
                        'created_by' => $this->session->get('logged_tier1_userid')
                    );

                    $this->eventmodel->insert_event_institution($event_data);
                    $this->session->setFlashdata('messages', lang('app.language_school_add_event_success_msg'));
                    $dataSuccess = array('afteradd' => 1,'success' => 1, 'msg' => 'Event added successfully');
                    echo json_encode($dataSuccess);
                    die();
                    
                } else{
                    $this->session->setFlashdata('errors', lang('app.language_school_add_event_failure_msg'));
                    $dataSuccess = array('afteradd' => 1,'success' => 0, 'msg' => 'Event added failure');
                    echo json_encode($dataSuccess);
                    die();
                }
            }
            
        }
        $arrayData = array('success' => 1, 'html' => view('school/addevent', $data));
        echo json_encode($arrayData);
        die;
    }

    public function tds_allocated_type(){
        $institutionTierId = $this->institutionTierId['id'];
        $builder = $this->db->table('institution_eligible_products');
        $builder->select('institution_eligible_products.tds_option,products.id,products.course_type');
        $builder->join('products', 'institution_eligible_products.group_id = products.group_id');
        $builder->where('institution_eligible_products.institutionTierId', $institutionTierId);
        $builder->where('products.active', 1);
        $query_institution = $builder->get();
        $institution_tds_options = $query_institution->getResultArray();
        $type_primary = $type_core = $type_higher = array();
        
        foreach($institution_tds_options as $institution_tds_option){
            if($institution_tds_option['course_type'] == "Primary"){
                $type_primary["primary"] = $institution_tds_option['tds_option'];
            }

            if($institution_tds_option['course_type'] == "Core"){
                $type_core["core"] = $institution_tds_option['tds_option'];
            }
            if($institution_tds_option['course_type'] == "Higher"){
                $type_higher["higher"] = $institution_tds_option['tds_option'];
            }
        }
        $tds_products = array_merge($type_primary, $type_core, $type_higher);
        return $tds_products;
    }

    public function fetch_venue_email($venue_id = False){
        if($venue_id != False){
            $builder = $this->db->table('venues');
            $builder->select('venues.email');
            $builder->where('venues.id', $venue_id);
            $query = $builder->get();
            return $query->getRowArray();
        }
    }

    public function utc_start_time($timezone = FALSE,$event_date = FALSE, $start_time = FALSE) {
        if($event_date != FALSE && $start_time != FALSE){
            $format = 'Y-m-d H:i';
            $dateTime = new DateTime($event_date . " ". $start_time, new DateTimeZone($timezone));
            $utc_time= $dateTime->setTimeZone(new DateTimeZone('UTC'));
            $utc_start_timestamp = strtotime($utc_time->format($format));
            return $utc_start_timestamp;
        }
    }

    public function js_endtime_set(){
        if (($this->request->getPost('starttime') != '')  ) {
            $starttime = $this->request->getPost('starttime');
            $endtime = date('H:i',strtotime('+4 hour',strtotime($starttime)));
            echo json_encode(array(
                'endtime'=> $endtime
            ));	
        }
    }
    // end time validaiton

    public function js_end_time_lesser(){

        $msg = "";
        if(null == $this->request->getPost('testdate')){
            $isAvailable = FALSE;
            $msg = 'please fill the date first';				
        } else {
            if (($this->request->getPost('starttime') != '')) {
                $testdate = $this->request->getPost('testdate');
                $starttime = $this->request->getPost('starttime');
                $endtime = date('H:i',strtotime(EVENT_END_TIME_ADD_HOUR,strtotime($starttime)));				

                $testdate_array = explode('/', $testdate);
                $timestamp_testdate = strtotime($testdate);
                if(!empty($starttime)){
                    $starttime_array = explode(':', $starttime);
                    $timestamp_starttime = mktime($starttime_array['0'], $starttime_array['1'], 00, $testdate_array['0'], $testdate_array['1'], $testdate_array['2']);
                }
                if(!empty($endtime)){
                    $endtime_array = explode(':', $endtime);
                    $timestamp_endtime = mktime($endtime_array['0'], $endtime_array['1'], 00, $testdate_array['0'], $testdate_array['1'], $testdate_array['2']);
                }
                /*WP-1129 -Possible to add invalid session - alteration for start time check -- START*/
                    $currentdatetime = new DateTime("now", new DateTimeZone( $this->institutionTierId['timezone']));
                    $currentdatetime->add(new DateInterval('PT1H'));
                    $currentzonetime = $currentdatetime->format('Y-m-d H:i');
                    $currentzonedate = $currentdatetime->format('m/d/Y');
                    $currentzonetimemsg = $currentdatetime->format('m/d/Y H:i');
                    $currentzonetimestamp = strtotime($currentzonetime);
                    
                    if ((strtotime($testdate) === strtotime($currentzonedate)) && ($currentzonetimestamp > $timestamp_starttime)){
                        $isAvailable = FALSE;
                        $msg = lang('app.language_school_add_update_event_start_time_great_msg') . ' ' . $currentzonetimemsg;
                    }else {
                        $isAvailable = TRUE;
                    }
                /*WP-1129 -Possible to add invalid session - alteration for start time check -- END*/
            }
        }
        echo json_encode(array(
            'endtime'=> $endtime,
            'valid' => $isAvailable,
            'message' => $msg,
        ));		
    }

    //validations for date and time
    public function js_starttime_check(){
        //Function to avoid duplicate event creation
        if($_POST){
            $tz_from =  $this->institutionTierId['timezone'];
            $venueid = $_POST['venue_id'];
            $product_group_values = $_POST['product_group_values'];
            
            if($_POST['testdate']){
                $testdate_array = strtotime($_POST['testdate']);
                $testdate = date('Y-m-d', $testdate_array);
            }
                        
            //convert date&time into UTC
            $start_date_time = $this->utc_start_time($tz_from,$testdate,$_POST['starttime']);
            $end_date_time = strtotime(EVENT_END_TIME_ADD_HOUR, $start_date_time);
            if(!empty($_POST['eventid'])){
                $eventid = $_POST['eventid'];
            }
            
           
            
            if(!empty($start_date_time)){
                 $builder = $this->db->table('events');
                 $builder->select('events.id,events.start_date_time,events.end_date_time');
                 $builder->where('events.venue_id', $venueid);
                // $builder->join('event_products', 'events.id = event_products.event_id');
                 $builder->where('events.start_date_time <=', $start_date_time);
                 $builder->where('events.end_date_time >=', $start_date_time);
                // $builder->where_in('event_products.product_id', $event_products);
                if (!empty($eventid)) {
                     $builder->where('events.id !=', $eventid);
                }
                $query =  $builder->get();
                $result_array = $query->getResultArray();
                 if(count($result_array) >= 1 ){
                    $value = False;
		    } else {
                    $value = True;		
                }
                echo json_encode(array(
                    'valid' => $value,
                ));
            }
        }
    }

    //To show version allocation msg
    public function version_allocation_msg(){
        $product_group_name = $this->request->getPost('value'); 
        if($product_group_name == "Primary"){
        $count = $this->eventmodel->get_products_for_events_allocation($product_group_name);
        }elseif($product_group_name == "Core"){
        $count = $this->eventmodel->get_products_for_events_allocation($product_group_name);
        }else{
        $count = $this->eventmodel->get_products_for_events_allocation($product_group_name);
        }
    
        $is_count = ($count == 0) ? 0 : 1;
        echo json_encode(array(
            'valid' => $is_count,
        ));
    }

    //get event for edit
    public function editevent(){  
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
        $tz_to = $this->institutionTierId['timezone'];
         if (null !== $this->request->getPost() && $this->request->getPost('event_test_id') && $this->request->getPost('event_test_id') != ''){ 
             $event_test_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('event_test_id')));
             $this->session->set('event_id_edit', $event_test_id);
             $dist_id = $this->session->get('user_id');
             $details = $this->eventmodel->event_details($event_test_id);
             foreach($details as $val) {
                 $event_details['id']   		=  $val->id;
                 $institution_zone_values =  @get_institution_zone_from_utc($tz_to,$val->start_date_time,$val->end_date_time);	
                 $event_details['date']  = strtotime($institution_zone_values['institute_event_date']);
                 $event_details['start_time'] = $institution_zone_values['institute_start_time'];
                 $event_details['end_time'] = $institution_zone_values['institute_end_time'];
                 $event_details['db_utc_start_time'] = $val->start_date_time;
                 $event_details['capacity']   	= $val->capacity; 
                 $event_details['fixed_capacity']= $val->fixed_capacity;
                 $event_details['notes']      	= $val->notes;
                 $event_details['venue_id']      = $val->venue_id;
                 $event_details['venue']      	= $val->venue_name;
                 $event_details['email']      	= $val->email; 
                 $event_details['ep_event_id'] 	= $val->ep_event_id;  
                 $event_details['products'][ $val->ep_product_id] =   $val->product_name;
                 $event_details['tds_option'] 	= $val->tds_option;  
             }	
             $venueid = $event_details['venue_id'];
             $product_ids = $event_details['products'];
             $tds_option_edit = $event_details['tds_option'];
             $current_utc_data = @get_current_utc_details();
             $current_utc_timestamp = $current_utc_data['current_utc_timestamp'];
                 if($current_utc_timestamp > $event_details['db_utc_start_time']){
                     $event_is_past = 1;
                 }else{
                     $event_is_past = 0;
                 }
             $allproducts = $this->eventmodel->get_products();
             foreach($allproducts as $prod_key => $prod_val){
                 if(in_array($prod_val['id'], array_keys($event_details['products'])) ) {
                     $allproducts[$prod_key]['checked'] = 'yes';
                 }
             }
             $venues = $this->eventmodel->get_venues($dist_id);
             if(empty($venues)){
                 $this->session->setFlashdata('error', lang('app.language_distributor_no_venue_available_msg'));
                 redirect('distributor/listevents','refresh');
             } 
             $venue_array = array();
             $venue_array[''] = 'Select';
             foreach ($venues as $v_key => $v_val){
                 $venue_array[$v_val->id] = $v_val->venue_name;
             }
             foreach($product_ids as $p_key => $p_val){
                 $product_id[] = $p_key;
             }
             $data = array(
                 'distributor_title' => lang('app.language_distributor_event_update_test_title'),
                 'distributor_heading' => lang('app.language_distributor_event_update_test_title'),
                 'event_over' => $event_is_past,
                 'venues' => $venue_array,
                 'products' => $allproducts,
                 'product_group_name' => $product_group_edit = $this->eventmodel->get_product_group_name($product_id),
                 'product_by_groups' => $this->eventmodel->get_product_by_group(),
                 'product_ids' => $product_id,
                 'eligible_version_products' => $this->eventmodel->get_products_for_events_version($product_group_edit, $tds_option_edit),
                 'time_zone' => $this->institutionTierId['timezone'],
                 'results' => $event_details
             );
             $arrayData = array('success' => 1, 'html' => view('school/editevent', $data));
             echo json_encode($arrayData);
             die;
         }else{
             if($this->request->getPost() && !($this->request->getPost('event_test_id'))){
                $rules =[
                    'product_group_name' => [
                      'label'  => 'product Group',
                      'rules'  => 'trim|required',
                      ],
                      'starttime' => [
                        'label' => lang('app.language_school_event_start_time'),
                        'rules' => 'trim|required',
                      ],
                      'endtime' => [
                        'label' => lang('app.language_school_event_end_time'),
                        'rules' => 'trim|required',
                      ],
                      'capacity' => [
                        'label' => lang('app.language_school_event_capacity'),
                        'rules' => 'trim|required|names_check',
                      ],
                      'venue_id' => [
                        'label' => lang('app.language_school_label_venue_name'),
                        'rules' => 'trim|required',
                      ]
              ];

                   if ($this->request->getPost('product_group_values') === NULL) {
                        $rules = [
                            'product_group_values' => [
                            'label'  => lang('app.language_school_event_please_choose_event_validation'),
                            'rules'  => 'trim|required',
                            ] 
                        ];
                   }
             
             if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = array(
                 'product_group_name' => $this->validation->showError('product_group_name'),
                 'product_group_values' => $this->validation->showError('product_group_values'),
                 'starttime' => $this->validation->showError('starttime'),
                 'endtime' => $this->validation->showError('endtime'),
                 'capacity' => $this->validation->showError('capacity'),
                 'venue_id' => $this->validation->showError('venue_id')
             );
                $response['errors'] = $errors;
                echo json_encode($response);
                die;
            } else{
                 $product_ids = $this->request->getPost('product_group_values');
                 $tz_from_edit = $this->institutionTierId['timezone'];
                 $testdate = $this->request->getPost('testdate');
                 //convert date&time into UTC
                 $start_date_time = $this->utc_start_time($tz_from_edit,$testdate,$this->request->getPost('starttime'));
                 $end_date_time = strtotime(EVENT_END_TIME_ADD_HOUR, $start_date_time);
                 $event_id = $this->session->get('event_id_edit');	
                 // To update learners allocated part this updating events table based on capacity choosable
                 $evt_details = $this->eventmodel->event_details($event_id);
                 $previous_capacity = $evt_details[0]->fixed_capacity;
                 $current_capacity = $this->request->getPost('capacity');
                 if($current_capacity > $previous_capacity){
                     $update_capacity = $current_capacity - $previous_capacity;
                     $capacity_update = $evt_details[0]->capacity + $update_capacity;
                 }elseif($current_capacity < $previous_capacity){
                     $update_capacity = $previous_capacity - $current_capacity;
                     $capacity_update = $evt_details[0]->capacity - $update_capacity;
                 }else{
                     $capacity_update = $evt_details[0]->capacity;
                 }
                 
                 
                 if ($testdate) {
                     $testdate_array = strtotime($testdate);
                     $timestamp_testdate = date('Y-m-d', $testdate_array);
                 }
 
                 $dataevents = array(
                     'distributor_id' => 0,
                     'test_date' => NULL,
                     'start_time' => NULL,
                     'end_time' => NULL,
                     'start_date_time' => $start_date_time,
                     'end_date_time' => $end_date_time,
                     'venue_id' => $this->request->getPost('venue_id'),
                     'capacity' => $capacity_update,
                     'fixed_capacity' => $this->request->getPost('capacity'),
                     'notes' => trim($this->request->getPost('notes'))
                 );
                 if($this->eventmodel->update_event($event_id, $dataevents)){
                     $count = 0;
                     if(!empty($product_ids)){
                         foreach($product_ids as $val) {
                             $event_products[$count]['event_id'] = $event_id;
                             $event_products[$count]['product_id'] = $val;
                             $count ++;
                         }
                         $this->eventmodel->insert_event_products($event_products);
                             if (isset($event_id) && !empty($event_id)) {
                                 $logged_tier_user = $this->session->get('logged_tier1_userid');
                                 $last_modified_user = isset($logged_tier_user) && !empty($logged_tier_user) ? $logged_tier_user : $this->session->get('user_id');
                                 $data = array('last_modified_by' => $last_modified_user);
                                 $builder = $this->db->table('event_institution');
                                 $builder->where('event_id', $event_id);
                                 $builder->update($data);
                             }
                         $this->session->setFlashdata('messages', lang('app.language_school_update_event_success_msg'));
                         $dataSuccess = array('afteradd' => 1,'success' => 1, 'msg' => 'Event added successfully');
                         echo json_encode($dataSuccess);
                         die();
                     }else{
                         if (isset($event_id) && !empty($event_id)) {
                                 $logged_tier_user = $this->session->get('logged_tier1_userid');
                                 $last_modified_user = isset($logged_tier_user) && !empty($logged_tier_user) ? $logged_tier_user : $this->session->get('user_id');
                                 $data = array('last_modified_by' => $last_modified_user);
                                 $builder = $this->db->table('event_institution');
                                 $builder->where('event_id', $event_id);
                                 $builder->update($data);
                         }
                         $this->session->setFlashdata('messages', lang('app.language_school_update_event_success_msg'));
                         $dataSuccess = array('afteradd' => 1,'success' => 1, 'msg' => 'Event added successfully');
                         echo json_encode($dataSuccess);
                         die();
                     }
                 }
             }
           }
         }
     }

    //remove event 
    public function remove_event() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
        header('Content-Type: application/json');
        if (null !== $this->request->getPost() && $this->request->getPost('event_delete_id')){
            $eventDeleteId = $this->encrypter->decrypt(base64_decode($this->request->getPost('event_delete_id')));
            $this->db->transStart();
        if (($eventDeleteId != '0' && $eventDeleteId != '')) {
                $count = $this->eventmodel->fetch_event_allocated_learners($eventDeleteId);
                $eventquery_event = $this->db->query('SELECT tds_option FROM events WHERE id = '.$eventDeleteId);
                $eventresult_event = $eventquery_event->getRow();
                $tds_option_event = $eventresult_event->tds_option;
                if ($count > 0) {
                    $builder = $this->db->table('events');
                    $builder->where('id', $eventDeleteId);
                    $builder->delete();
                    $builder = $this->db->table('event_institution');
                    $builder->where('event_id', $eventDeleteId);
                    $builder->delete();
                    $builder = $this->db->table('event_products');
                    $builder->where('event_id', $eventDeleteId);
                    $builder->delete();
                    //updating booking table for learners unallocation in events
                    if ($this->db->affectedRows() > 0) {
                        $builder = $this->db->table('booking');
                        $builder->select('booking.test_delivary_id');
                        $builder->where('event_id', $eventDeleteId);
                        $builder->where('status', 1);
                        $query = $builder->get();
                    $thirdparty_ids_delete = $query->getResult();
                    foreach ($thirdparty_ids_delete as $thirdparty_id)
                    {
                        $thirdparty_id =  $thirdparty_id->test_delivary_id;
                        if ($tds_option_event == 'catstds') {
                            log_message('error', "Event Deleted with Learners - " .print_r($thirdparty_id." -Deleted event id ".$eventDeleteId." -Institute admin ".$this->session->get('user_id'),true));
                                $remove_success = $this->remove_from_tds_tests($thirdparty_id);
                            } else {
                                $remove_success = $this->remove_from_batch_finaltest($thirdparty_id);
                            }
                            
                        }
                    $builder = $this->db->table('booking');
                    $builder->set('status', '0', false);
                    $builder->where('event_id', $eventDeleteId);
                    $builder->update();
                    }
                    
                } else {
                    $builder = $this->db->table('events');
                    $builder->where('id', $eventDeleteId);
                    $builder->delete();
                    $builder = $this->db->table('event_institution');
                    $builder->where('event_id', $eventDeleteId);
                    $builder->delete();
                    $builder = $this->db->table('event_products');
                    $builder->where('event_id', $eventDeleteId);
                    $builder->delete();
                }
            }
            if ($this->db->affectedRows() > 0) {
                $this->db->transComplete();
                $this->session->setFlashdata('messages', lang('app.language_school_event_removed_success_msg'));
                echo json_encode(array('success' => 1, 'msg' => 'Removed from institute'));
                die;
            } else {
                $this->session->setFlashdata('errors', lang('app.language_school_event_removed_failure_msg'));
                echo json_encode(array('success' => 0, 'msg' => 'Not removed from institute'));
                die;
            }
        }
    }

    public function remove_from_tds_tests($thirdparty_id = FALSE){
        if($thirdparty_id){
            $query = $this->db->query('SELECT * FROM tds_tests WHERE candidate_id = '.$thirdparty_id.' AND test_type = "final"');
            $result = $query->getRow();
            if ($query->getNumRows() > 0) {
                $tds_test_id = $result->id;
                $test_formid = $result->test_formid;
                $builder = $this->db->table('tds_tests');
                $builder->where('id', $tds_test_id);
                $builder->delete();
                if ($this->db->affectedRows() > 0) {
                    $formquery = $this->db->query('SELECT form_code FROM tds_allocation_formcode WHERE form_code = "'.$test_formid.'"');
                    $formresult = $formquery->getRow();
                    if ($formquery->getNumRows() > 0) {
                        $form_code = $formresult->form_code;
                        if($form_code != '' && $form_code != '0'){
                        $builder = $this->db->table('tds_allocation_formcode');
                        $builder->set('total_exposure','total_exposure + 1', FALSE);
                        $builder->set('current_exposure','current_exposure - 1', FALSE);
                        $builder->where('form_code',$form_code);
                        $builder->update();
                        }
                    }
                }
            }
        }
    }

    public function remove_from_batch_finaltest($thirdparty_id = FALSE){
        if($thirdparty_id){
            $curl = new Curl();
            $query = $this->db->query('SELECT * FROM collegepre_livetests where thirdparty_id = '.$thirdparty_id);
            $result = $query->getRow();
            if ($query->getNumRows() > 0) {
                $livetest_id = $result->id;
                $candidateNumber = $result->candidate_number;
                $testNumber = $result->test_number;
                $candidateId = $result->candidate_id;
                $settingquery = $this->db->query('SELECT * FROM collegepre_settings');
                $settingsresult = $settingquery->getRow();
                if ($settingquery->getNumRows() > 0) {
                    $practice_token = $settingsresult->practice_token;
                    $practice_key = $settingsresult->practice_key;
                    $removeurl = $settingsresult->prefix.$settingsresult->url.$settingsresult->suffix.$settingsresult->service5;
                    $checksum = MD5("testNumber=$testNumber&candidateNumber=$candidateNumber&k=$practice_key");
                    $arrayParams = array(
                        'token' => $practice_token,
                        'testNumber' => $testNumber,
                        'candidateNumber' => $candidateNumber,
                        'checksum' => $checksum
                    );
                    $reqdata = $curl->post($removeurl, $arrayParams);
                    $resultData = json_decode($reqdata);
                    if (!empty($resultData)) {
                        $delete_logs = array(
                            'thirdparty_id' => $thirdparty_id,
                            'test_number' => $testNumber,
                            'candidate_id' => $candidateId,
                            'candidate_number' => $candidateNumber,
                            'message' => $reqdata,
                        );
                        $builder = $this->db->table('collegepre_delete_logs');
                        $builder->insert($delete_logs);
                        
                        if ($resultData->success) {
                            if($livetest_id && $livetest_id != '0'){
                                $builder = $this->db->table('collegepre_livetests');
                                $builder->where('id', $livetest_id);
                                $builder->delete();
                                $remove_from_batch = $this->remove_from_batch_add($thirdparty_id);
                            }
                            
                        } else {
                            if($livetest_id && $livetest_id != '0'){
                                $builder = $this->db->table('collegepre_livetests');
                                $builder->where('id', $livetest_id);
                                $builder->delete();
                                $remove_from_batch = $this->remove_from_batch_add($thirdparty_id);
                            }
                        }
                    }
                }
            }else{
                $remove_from_batch = $this->remove_from_batch_add($thirdparty_id);
            }
        }
    }

    public function remove_from_batch_add($thirdparty_id = FALSE){
        if($thirdparty_id){
            $batchquery = $this->db->query('SELECT CB.* FROM collegepre_batch_add CB
            JOIN collegepre_forms CF ON CF.test_number = CB.test_number
            WHERE CB.thirdparty_id = '.$thirdparty_id.' AND CF.type = "Live test"');
            $batchresult = $batchquery->getRow();
            if ($batchquery->getNumRows() > 0) {
                $testnumber = $batchresult->test_number;
                $batchid = $batchresult->id;
                if($batchid && $batchid != '0'){
                    $builder = $this->db->table('collegepre_batch_add');
                    $builder->where('id', $batchid);
                    $builder->delete();
                    if ($this->db->affectedRows() > 0) {
                        $formquery = $this->db->query('SELECT form_code FROM collegepre_forms WHERE test_number = "'.$testnumber.'"');
                        $formresult = $formquery->getRow();
                        if ($formquery->getNumRows() > 0) {
                            $form_code = $formresult->form_code;
                            if($form_code != '' && $form_code != '0'){
                                $builder = $this->db->table('tds_allocation_formcode');
                                $builder->set('total_exposure','total_exposure + 1', FALSE);
                                $builder->set('current_exposure','current_exposure - 1', FALSE);
                                $builder->where('form_code',$form_code);
                                $builder->update();
                            }
                        }
                    }
                }
            }
        }
    }
    
        public function learner_allocation($session_id = FALSE){
        
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('/'));
            }
            
            if($session_id){
                $allocate_method = (isset($_REQUEST['allocate']) && $_REQUEST['allocate'] != '') ? $_REQUEST['allocate'] : '';
                
                if($allocate_method == 'clear'){
                    $this->session->setFlashdata('enable_manual', '1');
                    return redirect()->to('school/learner_allocation/'.$session_id);
                }
                 
                if($allocate_method == 'manual'){
                    $thirdparty_ids = $this->request->getPost('thirdparty_ids');
                    if(isset($thirdparty_ids) && $thirdparty_ids != ''){
                        $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                        $eventresult = $eventquery->getRow();
                        if ($eventquery->getNumRows() > 0) {
                            $event_capcity = $eventresult->capacity;
                            if($event_capcity > 0){
                                foreach($thirdparty_ids as $thirdparty_id):
                                    $thirdpartyid = $this->encrypter->decrypt(base64_decode($thirdparty_id));
                                    $booking_detail = $this->get_booking_detail($thirdpartyid,$session_id);
                                    $savebooking = $this->save_booking($booking_detail,$session_id);
                                    if($savebooking == 0){
                                        break;
                                    }
                                endforeach;
                                $this->session->setFlashdata('successmessage', 'Learners successfully allocated to the test event.');
                                return redirect()->to('school/learner_allocation/'.$session_id);
                            }else{
                                $this->session->setFlashdata('errors', 'The test event is fully allocated.');
                                return redirect()->to('school/learner_allocation/'.$session_id);
                            }
                        }else{
                            return redirect()->to('school/learner_allocation/'.$session_id);
                        }
                            
                    }else{
                        $this->session->setFlashdata('errors', 'Select at least one learner');
                        return redirect()->to('school/learner_allocation/'.$session_id);
                    }
                }
                
                if($allocate_method == 'unallocate'){
                    $thirdparty_ids = $this->request->getPost('thirdparty_ids');
                    if(isset($thirdparty_ids) && $thirdparty_ids != ''){
                        foreach($thirdparty_ids as $thirdparty_id):
                            $thirdpartyid = $this->encrypter->decrypt(base64_decode($thirdparty_id));
                            $unallocate = $this->remove_booking($thirdpartyid,$session_id);
                        endforeach;
                        $this->session->setFlashdata('successmessage', 'Learners unallocated successfully');
                        return redirect()->to('school/learner_allocation/'.$session_id);
                    }else{
                        $this->session->setFlashdata('errors', 'Select at least one learner');
                        return redirect()->to('school/learner_allocation/'.$session_id);
                    }
                }
                
                $this->data['session_details'] = $this->schoolmodel->get_session_details($session_id);
                $this->data['session_id'] = $session_id;
                
                $starttime_timestamp = $this->data['session_details']['detail']->start_date_time;
                $endtime_timestamp = $this->data['session_details']['detail']->end_date_time;
                
                $tz_to = $this->institutionTierId['timezone'];
                $current_utc_details = @get_current_utc_details();
                $current_utc_timestamp = $current_utc_details['current_utc_timestamp']; 
                                            
                $institution_zone_values = @get_institution_zone_from_utc($tz_to, $starttime_timestamp, $endtime_timestamp);
                
                $this->data['event_date'] = $institution_zone_values['institute_event_date'];
                $this->data['start_time'] = $institution_zone_values['institute_start_time'];
                $this->data['end_time'] = $institution_zone_values['institute_end_time'];
                $this->data['current_utc_timestamp'] = $current_utc_timestamp;
                
                $session_products = array();
                foreach($this->data['session_details']['products'] as $products):
                    $session_products[] = $products->id;
                    $session_product_group = $products->course_type;
                endforeach;
                
                /* WP-1122 - Filter & search implementation in learner allocation page START */
                $learner_search_item  = $filter_items = FALSE;
                if(isset($_GET['search']) && $_GET['search'] != ''){
                    if(is_numeric($_GET['search'])){
                        $this->session->setFlashdata('errors', 'Please enter a valid name or user name');
                        return redirect()->to('school/learner_allocation/'.$session_id);
                    }else{
                        $learner_search_item  = trim($_GET['search']);
                    }
                }
                
                if($allocate_method == 'filter'){
                    if($this->request->getPost()){
                        $filter_items = $this->request->getPost();
                        $this->data['filter_items_data'] = $filter_items;
                    }
                }
                /* WP-1122 - Filter & search implementation in learner allocation page END */
                
                $this->data['learner_details'] = $this->schoolmodel->get_learner_allocation_details($session_products, $session_product_group, $learner_search_item, $filter_items);
                $this->data['supervisor_details'] = $this->schoolmodel->supervisor_details_fetch($session_products, $session_product_group);
                /* To auto allocate the learners */	
                if($allocate_method == 'auto'){
                    $learner_details = 	$this->data['learner_details'];
                    if($learner_details){
                        $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                        $eventresult = $eventquery->getRow();
                        if ($eventquery->getNumRows() > 0) {
                            $event_capcity = $eventresult->capacity;
                            if($event_capcity > 0){
                                foreach($learner_details as $learner):
                                    $thirdpartyid = $learner->thirdparty_id;
                                    $booking_detail = $this->get_booking_detail($thirdpartyid,$session_id);
                                    $savebooking = $this->save_booking($booking_detail,$session_id);
                                    if($savebooking == 0){
                                        break;
                                    }
                                endforeach;
                                $this->session->setFlashdata('successmessage', 'Learners successfully allocated to the test event.');
                                return redirect()->to('school/learner_allocation/'.$session_id);
                            }else{
                                $this->session->setFlashdata('errors', 'The test event is fully allocated.');
                                return redirect()->to('school/learner_allocation/'.$session_id);
                            }
                        }else{
                            return redirect()->to('school/learner_allocation/'.$session_id);
                        }
                        
                    }else{
                        $this->session->setFlashdata('errors', 'No learners to allocate');
                        return redirect()->to('school/learner_allocation/'.$session_id);	
                    }
                }
                
                $this->data['learner_alloted_details'] = $this->schoolmodel->get_learner_alloted_details($session_id,$session_product_group);
                
                $this->data['learner_search_item'] = $learner_search_item;
                $this->data['languages'] = $this->cmsmodel->get_language();

                echo view('school/header');
                echo view('school/menus', $this->data);
                echo view('school/learner_allocation', $this->data);
                echo view('school/footer');
            
            }else{
                return redirect()->to('school/dashboard/');
            }
        }

        public function get_booking_detail($thirdparty_id = FALSE,$session_id = FALSE){
            if($thirdparty_id && $session_id){
                $query = $this->db->query('SELECT product_id,user_id FROM user_products WHERE thirdparty_id = '.$thirdparty_id);
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
                    $builder->select('*');
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
                        'event_id'          => $session_id,
                        'product_id'        => $product_id,
                        'attempt_no'        => $attempt_no,
                        'test_delivary_id'  => $thirdparty_id,
                        'datetime'          => @date("Y-m-d H:i:s"),
                        'score_calculation_type_id' => $score_cal_type,
                        'status'			=> 1
                    );
                    return $bookingdetails;
                }
                else{
                    return FALSE;
                }
            }
        }

        	//get attempt no and test delivary id
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

        public function save_booking($booking_detail = FALSE,$session_id = FALSE){
            if(!empty($booking_detail['user_id']) && !empty($booking_detail['product_id'])){
                
                $query = $this->db->query('SELECT id FROM booking WHERE test_delivary_id = '.$booking_detail['test_delivary_id']);
                $result = $query->getRow();
                if ($query->getNumRows() > 0) {
                    $bookingid = $result->id;
                    if($bookingid != '0' && $bookingid != ''){
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
                        
                        $insert_batch = $this->insert_to_batch_finaltest($arrBatchData,$session_id);
                        
                        if($insert_batch){
                            $builder = $this->db->table('booking');
                            $builder->where('id',  $bookingid);
                            $builder->update($booking_detail);
                            if ($this->db->affectedRows() == '1') {
                                if($session_id && $session_id !='0'){
                                    $builder = $this->db->table('events');
                                    $builder->set('capacity','capacity - 1', false);
                                    $builder->where('id',$session_id);
                                    $builder->update();
                                    
                                    $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                                    $eventresult = $eventquery->getRow();
                                    if ($eventquery->getNumRows() > 0) {
                                        $event_capcity = $eventresult->capacity;
                                        return $event_capcity;
                                    }else{
                                        return FALSE;
                                    }
                                }
                            }
                        }else{
                            $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                            $eventresult = $eventquery->getRow();
                            if ($eventquery->getNumRows() > 0) {
                                $event_capcity = $eventresult->capacity;
                                return $event_capcity;
                            }else{
                                return FALSE;
                            }
                        }
                    }
                }else{
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
                    
                    $insert_batch = $this->insert_to_batch_finaltest($arrBatchData,$session_id);
                    
                    if($insert_batch){
                        $builder1 = $this->db->table('booking');
                        if($builder1->insert($booking_detail)){
                            if($session_id && $session_id !='0'){
                                $builder = $this->db->table('events');
                                $builder->set('capacity','capacity - 1', false);
                                $builder->where('id',$session_id);
                                $builder->update();
                                
                                $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                                $eventresult = $eventquery->getRow();
                                if ($eventquery->getNumRows() > 0) {
                                    $event_capcity = $eventresult->capacity;
                                    return $event_capcity;
                                }else{
                                    return FALSE;
                                }
                            
                            }
                        }
                    }else{
                        $eventquery = $this->db->query('SELECT capacity FROM events WHERE id = '.$session_id);
                        $eventresult = $eventquery->getRow();
                        if ($eventquery->getNumRows() > 0) {
                            $event_capcity = $eventresult->capacity;
                            return $event_capcity;
                        }else{
                            return FALSE;
                        }
                    }
                }
            }
        }

        public function insert_to_batch_finaltest($arrData,$session_id) {
            if ($arrData != '' && $session_id != '') {
                $final_test_number = $this->get_finaltest_number_by_course_id($arrData,$session_id);
                if($final_test_number){
                    if($final_test_number['tds_option'] == 'catstds'){
                        $token = $this->get_token_by_thirdpartyid($arrData['test_delivary_id']);
                        if($token != ''){
                            $insData = array('test_formid' => $final_test_number['form_code'], 'test_formversion' => $final_test_number['form_version'], 'candidate_id' => $arrData['test_delivary_id'], 'token' => $token, 'test_type' => 'final');
                            $builder = $this->db->table('tds_tests');
                            $builder->insert($insData);
                        }else{
                            return FALSE;
                        }
                    }else{
                        $insData = array('test_number' => $final_test_number['test_number'], 'first_name' => $arrData['first_name'], 'last_name' => $arrData['last_name'], 'display_name' => $arrData['display_name'], 'gender' => $arrData['gender'], 'thirdparty_id' => $arrData['test_delivary_id']);
                        $builder = $this->db->table('collegepre_batch_add');
                        $builder->insert($insData);
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
                }
                else{
                    return FALSE;
                }
            }
        }

        public function get_finaltest_number_by_course_id($arrData = FALSE,$session_id = FALSE){
            $product_id = $arrData['product_id'];
            $user_id = $arrData['user_id'];
            if($product_id && $session_id){
                $tds_query = $this->db->query('SELECT tds_option FROM events where id = '.$session_id);
                $tds_result = $tds_query->getRow();
                if ($tds_query->getNumRows() > 0) {
                $test_driver = $tds_result->tds_option;
                }
                $builder = $this->db->table('tds_allocation');
                $builder->where('tds_allocation.product_id', $product_id);
                $builder->where('tds_allocation.tds_option', $test_driver);
                $alloc_query = $builder->get();
                $result = $alloc_query->getRow();
                if ($alloc_query->getNumRows() > 0) {
                    $allocation_rule = $result->allocation_rule;
                    $allocation_id = $result->id;
                    $tds_option = $result->tds_option;
                    
                    //Higher version rule
                    $user_formid_taken = $this->get_user_formid_by_userid($product_id,$user_id,$test_driver);
                    
                    if($allocation_rule == 'scheduled'){
                        if($user_formid_taken){
                            $formcode_query = $this->db->query('SELECT * FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 AND form_code NOT IN ("' . implode('", "', $user_formid_taken) . '") ORDER BY form_code_order ASC');
                        }else{
                            $formcode_query = $this->db->query('SELECT * FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 ORDER BY form_code_order ASC');
                        }
                        $formcode_results = $formcode_query->getResult();
                        foreach($formcode_results as $formcode):
                            $exposure_remaining = $formcode->total_exposure;
                            $form_code = $formcode->form_code;
                            if($exposure_remaining > 0){
                                if($tds_option == 'catstds'){
                                    $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                                    $testnumber_result = $testnumber_query->getRow();
                                    $testnumber = array();
                                    if ($testnumber_query->getNumRows() > 0) {
                                        $testnumber['form_code'] = $form_code;
                                        $testnumber['form_version'] = $testnumber_result->test_formversion;
                                        $testnumber['tds_option'] = $tds_option;
                                        return $testnumber;
                                    }else{
                                        return FALSE;
                                    }
                                }else{
                                    $testnumber_query = $this->db->query('SELECT test_number FROM collegepre_forms WHERE form_code = "'.$form_code.'"');
                                    $testnumber_result = $testnumber_query->getRow();
                                    $testnumber = array();
                                    if ($testnumber_query->getNumRows() > 0) {
                                        $testnumber['test_number'] = $testnumber_result->test_number;
                                        $testnumber['form_code'] = $form_code;
                                        $testnumber['tds_option'] = $tds_option;
                                        return $testnumber;
                                    }else{
                                        return FALSE;
                                    }
                                }
                            }
                        endforeach;
                        // Random allocation starts
                        $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 order by RAND() limit 1');
                        $formcode_result = $formcode_query->getRow();
                        if ($formcode_query->getNumRows() > 0) {
                            $form_code = $formcode_result->form_code;
                            if($tds_option == 'catstds'){
                                $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                                $testnumber_result = $testnumber_query->getRow();
                                $testnumber = array();
                                if ($testnumber_query->getNumRows() > 0) {
                                    $testnumber['form_code'] = $form_code;
                                    $testnumber['form_version'] = $testnumber_result->test_formversion;
                                    $testnumber['tds_option'] = $tds_option;
                                    return $testnumber;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                $testnumber_query = $this->db->query('SELECT test_number FROM collegepre_forms WHERE form_code = "'.$form_code.'"');
                                $testnumber_result = $testnumber_query->getRow();
                                $testnumber = array();
                                if ($testnumber_query->getNumRows() > 0) {
                                    $testnumber['test_number'] = $testnumber_result->test_number;
                                    $testnumber['form_code'] = $form_code;
                                    $testnumber['tds_option'] = $tds_option;
                                    return $testnumber;
                                }else{
                                    return FALSE;
                                }
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        if($user_formid_taken){
                            $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 AND form_code NOT IN ("' . implode('", "', $user_formid_taken) . '") order by RAND() limit 1');
                            if ($formcode_query->getNumRows() <= 0) {
                                $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 order by RAND() limit 1');
                            }
                        }else{
                            $formcode_query = $this->db->query('SELECT form_code FROM tds_allocation_formcode where tds_allocation_id = '.$allocation_id.' AND status = 1 order by RAND() limit 1');
                        }
                        $formcode_result = $formcode_query->getRow();
                        if ($formcode_query->getNumRows() > 0) {
                            $form_code = $formcode_result->form_code;
                            if($tds_option == 'catstds'){
                                $testnumber_query = $this->db->query('SELECT test_formid,test_formversion FROM tds_test_detail WHERE test_formid = "'.$form_code.'"');
                                $testnumber_result = $testnumber_query->getRow();
                                $testnumber = array();
                                if ($testnumber_query->getNumRows() > 0) {
                                    $testnumber['form_code'] = $form_code;
                                    $testnumber['form_version'] = $testnumber_result->test_formversion;
                                    $testnumber['tds_option'] = $tds_option;
                                    return $testnumber;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                $testnumber_query = $this->db->query('SELECT test_number FROM collegepre_forms WHERE form_code = "'.$form_code.'"');
                                $testnumber_result = $testnumber_query->getRow();
                                $testnumber = array();
                                if ($testnumber_query->getNumRows() > 0) {
                                    $testnumber['test_number'] = $testnumber_result->test_number;
                                    $testnumber['form_code'] = $form_code;
                                    $testnumber['tds_option'] = $tds_option;
                                    return $testnumber;
                                }else{
                                    return FALSE;
                                }
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


        public function get_user_formid_by_userid($product_id = FALSE,$user_id= FALSE,$test_driver= FALSE){
            $course_type = $this->get_coursetype_by_productid($product_id);
            if($course_type == 'Higher'){
                if($product_id && $user_id && $test_driver){
                    //If Test Driver is Collegepre 
                    if($test_driver == 'collegepre'){
                        $userappid_query = $this->db->query('SELECT user_app_id FROM `users` WHERE id = '.$user_id);
                        $userappid_result = $userappid_query->getRow();
                        if ($userappid_query->getNumRows() > 0) {
                            $user_app_id = $userappid_result->user_app_id;
                            $formid_query = $this->db->query('SELECT CF.form_code FROM `collegepre_higher_results` AS CHR JOIN collegepre_formcodes CF ON CF.form_id = CHR.form_id WHERE `thirdparty_id` LIKE "%'.$user_app_id.'%"');
                            $formid_result = $formid_query->getResult();
                            if ($formid_query->getNumRows() > 0) {
                                $formid = array();
                                foreach($formid_result as $formids):
                                    $formid[] = $formids->form_code;
                                endforeach;
                                return $formid;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }
                    //If Test driver is CATS TDS code goes here
                    if($test_driver == 'catstds'){
                        $userappid_query = $this->db->query('SELECT user_app_id FROM `users` WHERE id = '.$user_id);
                        $userappid_result = $userappid_query->getRow();
                        if ($userappid_query->getNumRows() > 0) {
                            $user_app_id = $userappid_result->user_app_id;
                            $formid_query = $this->db->query('SELECT testform_id FROM `tds_results` WHERE `candidate_id` LIKE "%'.$user_app_id.'%"');
                            $formid_result = $formid_query->getResult();
                            if ($formid_query->getNumRows() > 0) {
                                $formid = array();
                                foreach($formid_result as $formids):
                                    $formid[] = $formids->testform_id;
                                endforeach;
                                return $formid;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }
                }
            }else{
                return FALSE;
            }
        }

        public function get_coursetype_by_productid($product_id = FALSE){
            if($product_id){
                $productid_query = $this->db->query('SELECT course_type FROM `products` WHERE id = '.$product_id);
                if ($productid_query->getNumRows() > 0) {
                    $productid_result = $productid_query->getRow();
                    $course_type = $productid_result->course_type;
                    return $course_type;
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

        public function remove_booking($thirdpartyid = FALSE,$session_id = FALSE){
            if($thirdpartyid && $session_id){
                if(($thirdpartyid != '0' && $thirdpartyid != '') && ($session_id != '0' && $session_id != '')){	   $eventquery = $this->db->query('SELECT tds_option FROM events WHERE id = '.$session_id);
                    $eventresult = $eventquery->getRow();
                    $tds_option = $eventresult->tds_option;
                    $builder = $this->db->table('booking');
                    $builder->set('status','0', false);
                    $builder->where('event_id',$session_id);
                    $builder->where('test_delivary_id',$thirdpartyid);
                    $builder->update();
                    if ($this->db->affectedRows() == '1') {
                        $builder = $this->db->table('events');
                        $builder->set('capacity','capacity + 1', false);
                        $builder->where('id',$session_id);
                        $builder->update();
                        if($tds_option == 'catstds'){
                            $this->remove_from_tds_tests($thirdpartyid);
                            log_message('error', "Learner Removed from event - " .print_r($thirdpartyid." - event id ".$session_id." -Institute admin ".$this->session->get('user_id'),true));
                        }else{
                            $this->remove_from_batch_finaltest($thirdpartyid);
                        }
                        
                        return TRUE;
                    }
                }
            }
        }

        	/**
	 * Function for generate excel for allocated learners in learner allocation page-- WP-1122
	 */
	public function learner_allocation_export($session_id) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/'));
        }

	    if (!empty($session_id)) {
	        $session_products = array();
	        $this->data['session_details'] = $this->schoolmodel->get_session_details($session_id);
	        $this->data['session_id'] = $session_id;
	        foreach($this->data['session_details']['products'] as $products):
    	        $session_products[] = $products->id;
    	        $session_product_group = $products->course_type;
	        endforeach;
	        
	        $learner_alloted_details = $this->schoolmodel->get_learner_alloted_details($session_id, $session_product_group);

	        $heading_array = array('name' => 'Name', 'username_email' => 'Username/Email', 'password' => 'Password', 'product_name' => 'Level');

	        $newarray = array();
	        $count = 0;
	        if (!empty($learner_alloted_details)) {
	            foreach ($learner_alloted_details as $key => $val) {
	                if (!empty($val->firstname) || !empty($val->lastname)) {
	                    $newarray[$count]['name'] = $val->firstname . ' ' . $val->lastname;
	                } else {
	                    $newarray[$count]['name'] = 'Not available';
	                }
	                
	                if($val->order_type == 'under13'){
	                    if (!empty($val->username)) {
	                        $newarray[$count]['username_email'] = $val->username;
	                    } else {
	                        $newarray[$count]['username_email'] = 'Not available';
	                    }
	                }else{
	                    if (!empty($val->email)) {
	                        $newarray[$count]['username_email'] = $val->email;
	                    } else {
	                        $newarray[$count]['username_email'] = 'Unregistered';
	                    }
	                }
					
					if($val->order_type == 'under13'){
	                    if (!empty($val->password_visible)) {
	                        $newarray[$count]['password'] =  $this->encrypter->decrypt(base64_decode($val->password_visible));
	                    } else {
	                        $newarray[$count]['password'] = 'Not available';
	                    }
	                }else{
	                    $newarray[$count]['password'] = 'Not available';
	                }
	                	                
	                if (!empty($val->product_name)) {
	                    $newarray[$count]['product_name'] = $val->product_name;
	                } else {
	                    $newarray[$count]['product_name'] = 'Not available';
	                }
	                
	                $count ++;
	            }
	            array_unshift($newarray, $heading_array);
	        }
	        echo array_to_csv($newarray, 'List of allocated learner - Date as on-' . date('d-m-Y') . '.csv');
	        exit;
	    }
	}

        function post_u13learner() {

            $group_ids = $this->product_eligiblity;  

            if($group_ids){
               if ((in_array('2', $group_ids)) && (in_array('3', $group_ids)) && ($this->request->getPost('cats_product') == 'cats_core')) {
                $type_of_token = "cats_core_or_higher";
               }
                elseif((in_array('2', $group_ids))&& ($this->request->getPost('cats_product') == 'cats_core')){
                    $type_of_token = "cats_core";
                }elseif((in_array('1', $group_ids))&& ($this->request->getPost('cats_product') == 'cats_primary')){
                    $type_of_token = "cats_primary";
                }
            }
    
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to('admin/login');
            }
            header('Content-Type: application/json');
    
            if (null !==  $this->request->getPost()) {
    
                if ($this->session->get('distributor_id')) {
                } else {
                    if ($this->request->getPost('cats_product') == 'cats_core' || $this->request->getPost('cats_product') == 'cats_primary') {
                        $distributors_data = $this->list_distributors();
    
                        if (!empty($distributors_data)) {
                            $dis_detail = $distributors_data['results']['0'];
                            if (!empty($dis_detail)) {
                                $this->session->set('distributor_id', $dis_detail->distributor_id);
                                if (!empty($dis_detail)) {
                                    $orderdata = array('distributor_id' => $this->session->get('distributor_id'), 'distributor_name' => $dis_detail->distributor_name, 'distributor_paypal' => $dis_detail->paypal_account);
                                    $this->session->set('orderdata', $orderdata);
                                }
                                $this->makedefault($this->session->get('distributor_id'));
                            }
                        }
                    }
                }
    
                /*   if ($this->input->post('submit_type') == 'add_continue') {
                    $this->session->set_userdata('submit_t', 'continue');
                }*/
    
                if(!empty($this->request->getPost('user_id'))){
                    $userid = $this->encrypter->decrypt(base64_decode($this->request->getPost('user_id')));
                }else{
                    $userid = '';
                }

                if (!empty($userid)) {
                    $rules =[
                        'firstname' => [
                          'label'  =>  lang('app.language_admin_institutions_first_name'),
                          'rules'  => 'trim|required|max_length[60]',
                          ],
                          'lastname' => [
                            'label'  => lang('app.language_admin_institutions_second_name'),
                            'rules'  => 'trim|required|max_length[60]',
                          ],

                          'mydob.0' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],
                          'mydob.1' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],
                          
                          'mydob.2' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],

                          'mygender' => [
                            'label'  => lang('app.language_admin_institutions_u13_gender'),
                            'rules'  => 'required',
                          ]
                    ];
                } else {
                    $rules =[
                        'firstname' => [
                          'label'  =>  lang('app.language_admin_institutions_first_name'),
                          'rules'  => 'trim|required|max_length[60]',
                          ],
                          'lastname' => [
                            'label'  => lang('app.language_admin_institutions_second_name'),
                            'rules'  => 'trim|required|max_length[60]',
                          ],
                         
                          'mydob.0' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],
                          'mydob.1' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],
                          
                          'mydob.2' => [
                            'label'  => lang('app.language_admin_institutions_dob'),
                            'rules'  => 'required',
                          ],

                          'mygender' => [
                            'label'  => lang('app.language_admin_institutions_u13_gender'),
                            'rules'  => 'required',
                          ],
                          'lang_acc_det' => [
                            'label'  => lang('app.language_admin_institutions_u13_lang_acc_details'),
                            'rules'  => 'required',
                          ],
                         
                          'cats_product' => [
                            'label'  =>  lang('app.language_admin_institutions_cats_product'),
                            'rules'  => 'required',
                          ],
                          'cats_data_protection_policy' => [
                            'label'  =>  lang('app.language_admin_institutions_cats_data_protection_policy'),
                            'rules'  => 'required',
                          ]                 
                    ];
                }
    
                if ($this->validate($rules) == FALSE) {
                    $response['success'] = 0;
                    $errors = array(
                        'firstname_u13' => $this->validation->showError('firstname'),
                        'lastname_u13' => $this->validation->showError('lastname'),
                        'mydob'  => ($this->validation->showError('mydob.0')) ? $this->validation->showError('mydob.0') : (($this->validation->showError('mydob.1')) ? $this->validation->showError('mydob.1') : (($this->validation->showError('mydob.2')) ? $this->validation->showError('mydob.2') : '')),
                        'cats_product' => $this->validation->showError('cats_product'),
                        'cats_data_protection_policy' => $this->validation->showError('cats_data_protection_policy'),
                        'mygender' => $this->validation->showError('mygender'),
                        'lang_acc_det' => $this->validation->showError('lang_acc_det'),
                    );
                    $response['errors'] = $errors;


                    // print_r($response['errors']);
                    // exit;
    
                    echo json_encode($response);
                    die;
                } else {
    
                    $mydob = $this->request->getPost('mydob');
                    $dob = strtotime($mydob[2] . "-" . $mydob[1] . "-" . $mydob[0]);
                    $findage = strtotime((date('Y-m-d', strtotime('-16 years'))));
                    $prodeligible = strtotime((date('Y-m-d', strtotime('-14 years'))));
    
                    if ($dob <= $findage) {
                       
                        $response['success'] = 0;
                        $errors = array(
                            'firstname_u13' => $this->validation->showError('firstname'),
                            'lastname_u13' => $this->validation->showError('lastname'),
                            'mydob' =>  lang('app.language_admin_institute_dob_over13'),
                            'cats_product' => $this->validation->showError('cats_product'),
                            'cats_data_protection_policy' => $this->validation->showError('cats_data_protection_policy'),
                            'mygender' => $this->validation->showError('mygender'),
                        );
                        $response['doberror'] = 1;
                        $response['errors'] = $errors;
    
                        echo json_encode($response);
                        die;
                    }
                    
                    if($dob >= $findage && $dob <= $prodeligible){
                        if($this->request->getPost('cats_product') == 'cats_primary'){
    
                            $institutionTierId = $this->institutionTierId['id'];
                            $productEligibility  = $this->usermodel->get_institute_courseType($institutionTierId);
                            
                            $productEligibility = isset($productEligibility) ? array_map('current', $productEligibility) : '';
                            
                            if (in_array('2', $productEligibility)) {
                            
                                $response['success'] = 0;
                                $errors = array(
                                    'firstname_u13' => $this->validation->showError('firstname'),
                                    'lastname_u13' => $this->validation->showError('lastname'),
                                    'mydob' => '',
                                    'cats_product' => 'The learner is 14 or older and cannot be entered for Primary. Select CATs Core and proceed.',
                                    'cats_data_protection_policy' => $this->validation->showError('cats_data_protection_policy'),
                                    'mygender' => $this->validation->showError('mygender'),
                                );
                                $response['proderror'] = 1;
                                $response['errors'] = $errors;
    
                                echo json_encode($response);
                                die;
                            }else{
                                $response['success'] = 0;
                                $errors = array(
                                    'firstname_u13' => $this->validation->showError('firstname'),
                                    'lastname_u13' => $this->validation->showError('lastname'),
                                    'mydob' => 'The learner is 14 or older and cannot be entered for Primary. Please add a learner who is below 14',
                                    'cats_product' =>$this->validation->showError('cats_product'),
                                    'cats_data_protection_policy' => $this->validation->showError('cats_data_protection_policy'),
                                    'mygender' => $this->validation->showError('mygender'),
                                );
                                $response['doberror'] = 1;
                                $response['errors'] = $errors;
    
                                echo json_encode($response);
                                die;
                            }
                        }
                    }
    
                    /* U13 username generation */
                    for ($char = 'AA'; $char < 'ZZ'; $char++) {
                        $char_array[] = $char;
                    }
                    $k = array_rand($char_array);
                    $rand_2_alphabet = $char_array[$k];
                    $allowedNumbers = range(2, 9);
                    $digits = array_rand($allowedNumbers, 4);
                    $rand4 = '';
                    foreach ($digits as $d) {
                        $rand4 .= $allowedNumbers[$d];
                    }
                    $exploded_username = explode(" ", trim($this->request->getPost('firstname')));
                    if (!empty($exploded_username['0'])) {
                        $username_exploded = $exploded_username['0'];
                    }
                    $u13_username = $username_exploded . $rand_2_alphabet . $rand4;
                    $u13_username = preg_replace('/[^A-Za-z0-9\-]/', '', utf8_decode ($u13_username)); //WP-1242 - Remove diacritics in username
                    
                    $builder = $this->db->table('users');
                    $builder->select('*');
                    $builder->where('users.username', $u13_username);
                    $builder->orWhere('users.email', $u13_username . '@catsstep.education');
                    $query = $builder->get();
    
                    $chk_uniq_username = $query->getResult();
    
                    while (!empty($chk_uniq_username)) {
                        /* U13 username generation */
                        for ($char = 'AA'; $char < 'ZZ'; $char++) {
                            $char_array[] = $char;
                        }
                        $k = array_rand($char_array);
                        $rand_2_alphabet = $char_array[$k];
                        $allowedNumbers = range(2, 9);
                        $digits = array_rand($allowedNumbers, 4);
                        $rand4 = '';
                        foreach ($digits as $d) {
                            $rand4 .= $allowedNumbers[$d];
                        }
                        $exploded_username = explode(" ", trim($this->input->post('firstname')));
                        if (!empty($exploded_username['0'])) {
                            $username_exploded = $exploded_username['0'];
                        }
                        $u13_username = $username_exploded . $rand_2_alphabet . $rand4;
    
                        // checking in database for same u3learner username and email	
                        $this->db->select('*');
                        $this->db->from('users');
                        $this->db->where('users.username', $u13_username);
                        $this->db->or_where('users.email', $u13_username . '@catsstep.education');
                        $query = $this->db->get();
                        $chk_uniq_username = $query->result();
                    }
    
                    /* U13 password generation */
                    $allowedNumbers_p = range(2, 9);
                    $digits_p = array_rand($allowedNumbers_p, 2);
                    $rand2 = '';
                    foreach ($digits_p as $d_p) {
                        $rand2 .= $allowedNumbers_p[$d_p];
                    }
                    $ran_string_p = $this->generateRandomString();
                    $u13_password = $ran_string_p . $rand2;
    
                    $password_encode =  base64_encode($this->encrypter->encrypt($u13_password));
    
    
                    $date = new DateTime();
                    $u13_learner = array(
                        'username' => $u13_username,
                        'name' =>$this->request->getPost('firstname') . ' ' . $this->request->getPost('lastname'),
                        'firstname' => $this->request->getPost('firstname'),
                        'lastname' => $this->request->getPost('lastname'),
                        'language_id' => 1,
                        'gender' => $this->request->getPost('mygender'),
                        'email' => $u13_username . '@catsstep.education',
                        // 'password' => $this->phpass($u13_password),
                        'password' => $this->passwordhash->HashPassword($u13_password),
                        'password_visible' => $password_encode,
                        'dob' => $dob,
                        'access_detail_language' => $this->request->getPost('lang_acc_det'),
                        'creation_time' => $date->getTimestamp(),
                    );
    
                    if (!empty($userid)) {
                        // update u13 learner
                        $u13_learner_update = array(
                            'name'      => $this->request->getPost('firstname') . ' ' . $this->request->getPost('lastname'),
                            'firstname' => $this->request->getPost('firstname'),
                            'lastname'  => $this->request->getPost('lastname'),
                            'dob'       => $dob,
                            'gender'    => $this->request->getPost('mygender'),
                        );
                        if ($this->usermodel->update_teacher($userid, $u13_learner_update)) {
                            if (isset($userid) && !empty($userid)) {
                                $logged_tier_user = $this->session->get('logged_tier1_userid');
                                $last_modified_user = isset($logged_tier_user) && !empty($logged_tier_user) ? $logged_tier_user : $this->session->get('user_id');
                                $data = array('last_modified_by' => $last_modified_user);
    
                                $builder = $this->db->table('instituition_learners');
                                $builder->where('user_id', $userid);
                                $builder->update($data);
                            }
                            $this->session->setFlashdata('messages', lang('app.language_school_u13learner_updated_success_msg'));
                            $dataSuccess = array('success' => 1, 'msg' => 'U13 learner updated');
                            echo json_encode($dataSuccess);
                            exit;
                        } else {
                            $this->session->setFlashdata('errors', 'No changes made.');
                            $dataSuccess = array('success' => 1, 'msg' => 'U13 learner updated');
                            echo json_encode($dataSuccess);
                            exit;
                        }
                    } else {
                        // add u13 learner
                        $builder = $this->db->table('users');
                        $builder->insert($u13_learner);
                        $last_inserted_userid = $this->db->insertID();
    
                        if ($last_inserted_userid) {
    
                            //update the APP_USER ID
                            $userappid = array('user_app_id' => (int) $this->acl_auth->billion + (int) $last_inserted_userid);
       
                            $builder = $this->db->table('users');
                            $builder->where('id', $last_inserted_userid);
                            $builder->update($userappid);
                            
                            $u13_learner_role = array(
                                'roles_id' => '3',
                                'users_id' => $last_inserted_userid,
                            );
    
                            $builder = $this->db->table('user_roles');
                            $builder->insert($u13_learner_role);
                           
                            $u13_learner_inst_learn = array(
                                'instituition_id' => $this->session->get('user_id'),
                                'user_id' => $last_inserted_userid,
                                'cats_product' => $this->request->getPost('cats_product'),
                                'created_by' => $this->request->getPost('logged_tier1_userid'),
                            );
    
                            $builder = $this->db->table('instituition_learners');
                            $builder->insert($u13_learner_inst_learn);
                            
                            // Placement test changes for under 13 learners
                            $schoolOrderId = $this->getPrimaySchoolOrderId($this->session->get('user_id'));
                            if($schoolOrderId){
                                $data['number_of_tests'] = ($schoolOrderId->number_of_tests) + 1;	
    
                                $builder = $this->db->table('school_orders');
                                $builder->where('id', $schoolOrderId->id);
                                $builder->update($data);
                                
                                $token = $this->getToken(9);
                                $token_details = array(
                                    'token' => $token,
                                    'school_order_id' => $schoolOrderId->id,
                                    'generated_date' => date('Y-m-d'),
                                    'expiry' => strtotime(date('d-m-Y')),
                                    'type_of_token' => $type_of_token,
                                    'user_id' => $last_inserted_userid
                                );
    
                                $builder = $this->db->table('tokens');
                                $builder->insert($token_details);
    
                            }else{
                                $order_details = array(
                                    'school_user_id' => $this->session->get('user_id'),
                                    'distributor_id' => $this->session->get('orderdata')['distributor_id'],
                                    'distributor_name' => $this->session->get('orderdata')['distributor_name'],
                                    'order_name' => 'Under13_Order',
                                    'order_desc' => 'Under13_Desc',
                                    'number_of_tests' => '1',
                                    'type_of_token' => 'catslevel',
                                    'order_date' => date('Y-m-d'),
                                    'payment_done' => '1',
                                    'order_type' => 'under13'
                                );
                                // $this->db->insert('school_orders', $order_details);
                                $builder = $this->db->table('school_orders');
                                $builder->insert($order_details);
                                
                                $school_order_id = $this->db->insertID();
                                
                                $paymentdetails = array(
                                    'payment_method' => 'none',
                                    'school_order_id' => $school_order_id,
                                    'distributor_id' => $this->session->get('orderdata')['distributor_id'],
                                    'payment_success' => 'success'
                                );

                                $payment_detail = $this->schoolmodel->save_payment_details($paymentdetails);
    
                                if (!empty($payment_detail)) {
                                    $token = $this->getToken(9);
                                    $token_details = array(
                                        'token' => $token,
                                        'school_order_id' => $school_order_id,
                                        'generated_date' => date('Y-m-d'),
                                        'expiry' => strtotime(date('d-m-Y')),
                                        'type_of_token' => $type_of_token,
                                        'user_id' => $last_inserted_userid
                                    );
                                    // $this->db->insert('tokens', $token_details);
                                    $builder = $this->db->table('tokens');
                                    $builder->insert($token_details);
    
                                }
                                
                            }
                            // Placement test changes for under 13 learners Ends
                        }
                        // $this->session->set_flashdata('messages', lang('language_school_u13learner_added_success_msg'));
                        $this->session->setFlashdata('messages', lang('app.language_school_u13learner_added_success_msg'));
                        $dataSuccess = array('success' => 1, 'msg' => 'U13 learner added');
                        if ( $this->request->getPost('submit_type') == 'add_continue') {
                            $this->session->set('submit_t', 'continue');
                        }
                        echo json_encode($dataSuccess);
                        exit;
                    }
                }
            } else {
                $dataFailute = array('success' => 0, 'msg' => 'No post request made!');
                echo json_encode($dataFailute);
            }
        }

         //set default distributor
        public function makedefault($distributorid = false) {
            
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to('/');
            }
            if (($this->request->getPost('dist_id')) || (!empty($distributorid))) {
                if (!empty($distributorid)) {
                    $distributor_id = $distributorid;
                    $dist_data = $this->bookingmodel->get_distributors($distributor_id);
                    if (!empty($dist_data)) {

                        $check = $this->schoolmodel->check_exists_defaults($distributor_id);
                        if ($check) {
                            //update all other distributor with status 0
                            $this->schoolmodel->update_other_to_default_zero();
                            //update the existing distributor with status 1
                            $default_u = $this->school_model->update_defaults($distributor_id, array('setdefault' => 1));
                        } else {
                            //update all other distributor with status 0
                            $this->schoolmodel->update_other_to_default_zero();
                            //insert a new distributor with status 1
                            $default_i = $this->school_model->insert_defaults(array('school_user_id' => $this->session->get('user_id'), 'distributor_id' => $distributor_id, 'setdefault' => 1));
                        }
                    }
                } else {
                    $distributor_id = base64_decode($this->input->post('dist_id'));
                    // checking the  default distributor and saving distributor details in session
                    $this->session->set('distributor_id', $distributor_id);
                    $dis_id = $this->session->get('distributor_id');
                    if (!empty($dis_id)) {
                        $this->db->select('distributor_id, distributor_name, currency, paypal_account');
                        $this->db->from('users');
                        $this->db->where('distributor_id', $this->session->get('distributor_id'));
                        $query = $this->db->get();
                        $dis_detail = $query->result();
                        if (!empty($dis_detail)) {
                            $orderdata = array('distributor_id' => $this->session->get('distributor_id'), 'distributor_name' => $dis_detail['0']->distributor_name, 'distributor_paypal' => $dis_detail['0']->paypal_account);
                            $this->session->set('orderdata', $orderdata);
                        }
                    }
                    $dist_data = $this->booking_model->get_distributors($distributor_id);
                    if (!empty($dist_data)) {

                        $institutions = $this->school_model->get_institution_count();
                        foreach($institutions as $institution){
                            $check = $this->school_model->check_exists_defaults_all_institution($distributor_id, $institution->user_id);
                            if ($check) {
                                //update all other distributor with status 0
                                //$this->school_model->update_other_to_default_zero();
                                //update the existing distributor with status 1

                                    $this->school_model->update_other_to_default_zero_all_institution($institution->user_id);
                                    $this->school_model->update_defaults_all_institution($distributor_id, array('setdefault' => 1), $institution->user_id);

                                //$default_u = $this->school_model->update_defaults($distributor_id, array('setdefault' => 1));
                            } else {
                                //update all other distributor with status 0
                                //$this->school_model->update_other_to_default_zero();
                                //insert a new distributor with status 1
                                //$institutions = $this->school_model->get_institution_count();
                                
                                $this->school_model->update_other_to_default_zero_all_institution($institution->user_id);
                                $this->school_model->insert_defaults(array('school_user_id' => $institution->user_id, 'distributor_id' => $distributor_id, 'setdefault' => 1));
                                                    
                                //$default_i = $this->school_model->insert_defaults(array('school_user_id' => $this->session->userdata('user_id'), 'distributor_id' => $distributor_id, 'setdefault' => 1));
                            }
                        }
                        $this->session->setFlashdata('messages', 'Set default success.');
                        $success_data = array('success' => 1);
                        echo json_encode($success_data);
                    } else {
                        $this->session->setFlashdata('messages', 'Set default failure.');
                        $failure_data = array('success' => 0);
                        echo json_encode($failure_data);
                    }
                }
            } 
            else {
                $this->session->setFlashdata('messages', 'Set default failure.');
                $failure_data = array('success' => 0);
                echo json_encode($failure_data);
            }
        }

        function post_checkdob(){
            $mydob = $this->request->getPost('mydob');
            $dob = strtotime($mydob[2] . "-" . $mydob[1] . "-" . $mydob[0]);
            $findage = strtotime((date('Y-m-d', strtotime('-16 years'))));
    
            if ($dob <= $findage) {
                $response['success'] = 0;
                $errors = array(
                    'mydob' => lang('app.language_admin_institute_dob_over13')
                );
                $response['doberror'] = 1;
                $response['errors'] = $errors;
    
                echo json_encode($response);
                die;
            }else{
                $response['success'] = 1;
                $errors = array(
                    'mydob' => ''
                );
                $response['errors'] = $errors;
                
                echo json_encode($response);
                die;
            }
        }


        
    function generateRandomString($length = 6) {
        $characters = 'ABCDEFGHIJKMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        $randomStringSample = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function getPrimaySchoolOrderId($instituteId){
		if($instituteId){
			$query = $this->db->query('SELECT id,number_of_tests FROM school_orders WHERE order_type = "under13" AND school_user_id = '.$instituteId);
			$result = $query->getRowObject();
			if($query->getNumRows() > 0){
				return $result;
			}else{
				return FALSE;
			}
		}
	}

    //get u3-learner
    function u13learner() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');
        }
        header('Content-Type: application/json');
        // $this->load->model('user_model');
        if (null !== $this->request->getPost() && $this->request->getPost('u13_learner_id') && $this->request->getPost('u13_learner_id') != ''):
            $u13_learner_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13_learner_id')));
            $data = array(
                'teacher_title' => lang('app.language_school_viewedit_u13learner'),
                'teacher_heading' => lang('app.language_school_viewedit_u13learner'),
                'u13_learner' => $this->usermodel->get_u13learner($u13_learner_id),
            );
        else:
            $data = array(
                'teacher_title' => lang('app.language_school_add_teacher'),
                'teacher_heading' => lang('app.language_school_add_teacher'),
            );
        endif;
        $arrayData = array('success' => 1, 'html' =>  view('school/u13learner', $data));
        echo json_encode($arrayData);
        die;
    }

    //get u13-learner
    function nextlevel() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');
        }
        header('Content-Type: application/json');

        $u13_learners = $this->request->getPost('u13_learner_ids');
        if (null !== $this->request->getPost() && $this->request->getPost('u13_learner_ids') != '' && !empty($u13_learners) ):
            foreach ($this->request->getPost('u13_learner_ids') as $key => $val) {
                // $u13_learner_ids[$key] = $this->encrypt->decode($val);                  
                $u13_learner_ids[$key] = $this->encrypter->decrypt(base64_decode($val));
            }

            $institutionTierId = $this->institutionTierId['id'];
			$institutecoursetype = $this->usermodel->get_institute_productEligible($institutionTierId);

			$productEligible = [];
			foreach($institutecoursetype as $coursetype):
				$productEligible[] = $coursetype->group_id;
			endforeach;
       
            $data = array(
                'u13_learners' => $this->usermodel->get_nextlevel_u13learner($u13_learner_ids),
                'u13_products' => $this->productmodel->get_product(FALSE, 'Primary', FALSE),
                'institute_courseType' => $productEligible,
            );
        endif;

        $arrayData = array('success' => 1, 'html' => view('school/nextlevel', $data));
        echo json_encode($arrayData);
        die;
    }


    public function post_nextlevel(){

		if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');
        }
        header('Content-Type: application/json');
        if (null !== $this->request->getPost()) {
			$catsLevel = $this->request->getPost('cats_level');
			$catsLevelCount = count(array_filter($catsLevel));

			if($catsLevelCount < 1){
                $rules =[
                    'cats_level[]' => [
                      'label'  =>  lang('app.language_school_next_cats_level'),
                      'rules'  => 'trim|required',
                      ],
                      'cats_data_protection_policy' => [
                        'label'  => lang('app.language_school_cats_data_protection_policy'),
                        'rules'  => 'trim|required',
                      ]         
                ];
			}else{
                $rules =[ 
                      'cats_data_protection_policy' => [
                        'label'  => lang('app.language_school_cats_data_protection_policy'),
                        'rules'  => 'trim|required',
                      ]         
                ];
            } 

			if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = array(
                    'cats_level' => $this->validation->showError('cats_level[]'),
                    'cats_data_protection_policy' => $this->validation->showError('cats_data_protection_policy'),
                );
                $response['errors'] = $errors;
                echo json_encode($response);
                die;

            }else{
				$learners = $this->request->getPost('learner_id');
				foreach($learners as $key=>$learner):
				$productId = $this->request->getPost('cats_level')[$key];
				if($productId){
					$schoolOrderId = $this->getPrimaySchoolOrderId($this->session->get('user_id'));
					if($schoolOrderId){
						$data['number_of_tests'] = ($schoolOrderId->number_of_tests) + 1;	

                        $builder = $this->db->table('school_orders');
                        $builder->where('id', $schoolOrderId->id);
                        $builder->update($data);

						if($productId <=9){
							$tokenType = 'cats_core';
						}elseif($productId > 9 && $productId < 13){
							$tokenType = 'cats_core_or_higher';
						}elseif($productId > 12){
							$tokenType = 'cats_primary';
						}
						
						$token = $this->getToken(9);
						$token_details = array(
							'token' => $token,
							'school_order_id' => $schoolOrderId->id,
							'generated_date' => date('Y-m-d'),
							'expiry' => strtotime(date('d-m-Y')),
							'type_of_token' => $tokenType,
							'user_id' => $learner
						);
                        $builder = $this->db->table('tokens');
                        $builder->insert($token_details);
					}
					else{
						$order_details = array(
							'school_user_id' => $this->session->get('user_id'),
							'distributor_id' => $this->session->get('orderdata')['distributor_id'],
							'distributor_name' => $this->session->get('orderdata')['distributor_name'],
							'order_name' => 'Under13_Order',
							'order_desc' => 'Under13_Desc',
							'number_of_tests' => '1',
							'type_of_token' => 'catslevel',
							'order_date' => date('Y-m-d'),
							'payment_done' => '1',
							'order_type' => 'under13'
						);

                        $builder = $this->db->table('school_orders');
                        $builder->insert($order_details);
                        $school_order_id = $this->db->insertID();

						$paymentdetails = array(
							'payment_method' => 'none',
							'school_order_id' => $school_order_id,
							'distributor_id' => $this->session->get('orderdata')['distributor_id'],
							'payment_success' => 'success'
						);
						$payment_detail = $this->schoolmodel->save_payment_details($paymentdetails);
						if (!empty($payment_detail)) {
							$token = $this->getToken(9);
							$token_details = array(
								'token' => $token,
								'school_order_id' => $school_order_id,
								'generated_date' => date('Y-m-d'),
								'expiry' => strtotime(date('d-m-Y')),
								'type_of_token' => $tokenType,
								'user_id' => $learner
							);
                            $builder = $this->db->table('tokens');
                            $builder->insert($token_details);
						}
					}
					
					$product_details = $this->productmodel->get_product_details($productId);
					$courseName = $product_details['0']['name'];
                    $distributor = $this->session->get('distributor_id');
					$details = array(
						'payment_method' => 'token',
						'user_id' => $learner,
						'distributor_id' => $distributor['distributor_id'],			
						'payment_success' => 'success'						
					);

					$payment_detail = $this->bookingmodel->save_payment_details($details);
					if($payment_detail){
						$userProfile = $this->usermodel->get_profile($learner);
						//get third party id data
						$arrData = array(
							'user_id'       => $learner,
							'user_app_id'	=> $userProfile[0]->user_app_id,	
							'product_id'    => $productId,
							'first_name'    => $userProfile[0]->firstname,
							'last_name'     => $userProfile[0]->lastname,
							'display_name'  => $userProfile[0]->firstname.' '.$userProfile[0]->lastname                                                 
						);
						$thirdPartyId = $this->get_thirdparty_id($arrData);
						
						$detail = array(
							'user_id' => $learner,
							'distributor_id' => $distributor['distributor_id'],
							'product_id' => $productId,
							'thirdparty_id' => $thirdPartyId,
							'city' => $this->session->get('distributor_city'),
							'country' => $this->session->get('distributor_country'),
							'purchased_date' => @date("Y:m:d h:m:s"),
							'payment_id' => $payment_detail,
							'payment_done' => 1
						);                      
						$book_detail = $this->bookingmodel->save_booking_details($detail);
						
						//create practice test codes for under 13 core learner 
						
						//insert data to batch process
						$arrBatchData = array(
                        'user_id'       => $learner,
                        'user_app_id'	=> $userProfile[0]->user_app_id,
                        'product_id'    => $productId,
                        'gender'        => $userProfile[0]->gender,
                        'first_name'    => $userProfile[0]->firstname,
                        'last_name'     => $userProfile[0]->lastname,
                        'display_name'  => $userProfile[0]->firstname.' '.$userProfile[0]->lastname,
                        'token'         => $token, // WP-1202 Pass token to insert Practice test in "tds_tests" table for under13 Primary/Core in NEXT level
                        );
						$this->insert_to_batch($arrBatchData);
						//practice test codes done for under 13 core learner ends
						
						$updata = array(
							'user_id' => $learner,
							'user_name' => $userProfile[0]->firstname.' '.$userProfile[0]->lastname,
							'product_id' => $productId,
							'level' => $courseName,
							'thirdparty_id' => $thirdPartyId,
							'redeem_payment_id' => $payment_detail,
							'is_used' => '1',
                            'used_time' => time()
						);

                        $builder = $this->db->table('tokens');
                        $builder->where('token', $token);
                        $builder->update($updata);
					}
				}
				endforeach;
				
				$response['success'] = 1;
				
                echo json_encode($response);
                die;
			}
		}		
	}

    function get_thirdparty_id($arrData)
    {
         $attempt_results = $this->bookingmodel->get_already_purchased_products($arrData['user_id'], $arrData['product_id']);
         if(!empty($attempt_results)){
            $attempt_no = $attempt_results[0]['attempt_no'] + 1;
            $test_delivary_id = $arrData['user_app_id'].$attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }else{
            $no_attempt_results = $this->bookingmodel->get_already_purchased_products(false, $arrData['product_id']);
            $attempt_no = 1;
            $test_delivary_id  = $arrData['user_app_id'].$no_attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
         }
         return $test_delivary_id;
    }

    function insert_to_batch($arrData) {
	    
	    // WP-1202 Insert Practice test in "tds_tests" table for under13 (primary/Core) in NEXT level
	    $elegible_product_lists = $this->usermodel->get_institute_productEligible_by_user($arrData['user_id']);
	    $current_product_details = $this->bookingmodel->get_products($arrData['user_id']);
	    $current_product_type = $current_product_details[0]['course_type'];
	    //Get TDS option for the current Product
	    foreach($elegible_product_lists as $elegible_product){
	        $elegible_product_details = $this->eventmodel->get_eligible_productname($elegible_product->group_id);
	        if($elegible_product_details[0]['name'] === $current_product_type){
	            $product_tds_option = $elegible_product->tds_option;
	        }
	    }// WP-1202 Insert Practice test in "tds_tests" table for under13 (primary/Core) in NEXT level END
	    
        $attempt_results = $this->bookingmodel->get_already_purchased_products($arrData['user_id'], $arrData['product_id']);
        if (!empty($attempt_results)) {
            $attempt_no = $attempt_results[0]['attempt_no'];
            $test_delivary_id = $arrData['user_app_id'] . $attempt_results[0]['course_id'] . sprintf("%02d", $attempt_no);
        } else {
            $no_attempt_results = $this->bookingmodel->get_already_purchased_products(false, $arrData['product_id']);
            $attempt_no = 1;
            $test_delivary_id = $arrData['user_app_id'] . $no_attempt_results[0]['course_id'] . sprintf("%02d", $attempt_no);
        }

        if ($test_delivary_id != '') {
            if(isset($product_tds_option) && $product_tds_option === 'collegepre'){// WP-1202 Check TDS option 
                $practice_tests = $this->booking_model->get_practice_numbers_by_course_id($arrData['product_id']);
                foreach ($practice_tests as $practice_test):
                    $insData = array('test_number' => $practice_test->test_number, 'first_name' => $arrData['first_name'], 'last_name' => $arrData['last_name'], 'display_name' => $arrData['display_name'], 'gender' => $arrData['gender'], 'thirdparty_id' => $test_delivary_id);
                    $builder = $this->db->table('collegepre_batch_add');
                    $builder->insert($insData);

                endforeach;
            }
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

    //export access details to pdf
    public function export_access_details($userid = FALSE) {
        $userid = $this->request->getPost('u13_learner_ids');
        if ($userid) {

            foreach ($userid as $id):
                $user_id[] = $this->encrypter->decrypt(base64_decode($id));
            endforeach;

            if (count($user_id) > 0) {
                $accessDetails = $this->schoolmodel->get_access_details($user_id);

                if ($accessDetails) {
                    $this->generateAccessDetailsPdf($accessDetails);
                }
            }
        } else {
            if ($this->acl_auth->logged_in()) {
                $instituteid = $this->session->get('user_id');
                $lastgenerated = $this->get_last_generated($instituteid);
                $accessDetails = $this->schoolmodel->get_access_details_by_institute($instituteid, $lastgenerated);
                if ($accessDetails) {
                    $date = new DateTime();
                    $genData['school_user_id'] = $instituteid;
                    $genData['date_generated'] = $date->getTimestamp();

                    $this->db->where('school_user_id', $instituteid);
                    $query = $this->db->get('learner_generated_details');

                    if ($query->num_rows() > 0) {
                        $this->db->where('school_user_id', $instituteid);
                        $this->db->update('learner_generated_details', $genData);
                    } else {
                        $this->db->insert('learner_generated_details', $genData);
                    }
                    $this->generateAccessDetailsPdf($accessDetails);
                } else {
                    $this->session->setFlashdata('access_errors', lang('app.language_access_details_error_msg'));
                    return redirect()->to('school/dashboard');
                }
            }
        }
    }

    public function generateAccessDetailsPdf($accessDetails = FALSE) {

        if ($accessDetails) {
              $dompdf = new Dompdf();

              $font_style_url = base_url('public/fonts/fonts.css');
              $logo_url = base_url('public/images/logo_new.png');
            $pdf_content = '<!DOCTYPE html>
			<html>
				<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
					<title>Access Details</title>
                    <link href="'.$font_style_url.'" rel="stylesheet" type="text/css">
				</head>
				<body style="margin: 0 auto; padding: 0;box-sizing: border-box;text-align: left;font: 14px Comic Sans Regular,Comic Sans Bold; line-height:14px;">';
            
                foreach ($accessDetails as $accessdetail):

                if ($accessdetail->access_detail_language == 1) {
                    $this->lang->lang('locale_lang', 'english');
                } elseif ($accessdetail->access_detail_language == 3) {
                    $this->lang->lang('locale_lang', 'malay');
                } elseif ($accessdetail->access_detail_language == 4) {
                    $this->lang->lang('locale_lang', 'srpski');
                } elseif ($accessdetail->access_detail_language == 10) {
                    $this->lang->lang('locale_lang', 'portuguese');
                } else {
                    $this->lang->lang('locale_lang', 'english');
                }
                

                if ($accessdetail->cats_product == 'cats_primary'):
                    $pdf_content .= '
					<table width="100%" style="padding: 10px 25px 10px 25px;">
						<tr>
							<td>
								 <table width="100%" >
									<tbody>
										<tr><td style="padding-bottom: 20px;border-bottom: 3px solid #1cbadf;"><img src="'.$logo_url.'" alt="logo"/></td></tr>
										<tr>
										<td>
										<table bgcolor="#fff" style="font-size: 16px; border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
										<tbody style="font-size:14px;">
										<tr><td style="font-family: DejaVu Sans, sans-serif;padding:5px 0 5px;"><b>' . lang('app.language_school_access_details_hello') . ' ' . $accessdetail->firstname . '</b></td></tr>
										<tr>
										<td style="font-family : Comic Sans Regular;padding: 5px 0px 5px;">' . lang('app.language_school_access_details_intro') . '</td>
										</tr>
										<tr>
											<td>
											<table>
											<tr>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:right;"><strong>' . lang('app.language_school_access_details_username') . ': </strong></td>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:left;"><strong>' . $accessdetail->username . '</strong></td>
												</tr>
											<tr>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:right;"><strong>' . lang('app.language_school_access_details_password') . ': </strong></td>
												  

												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:left;"><strong>' . $this->encrypter->decrypt(base64_decode($accessdetail->password_visible))  /* $accessdetail->password_visible*/ . '</strong></td>
												</tr>	
											</table>
											</td>
										</tr>
										</tbody>
										</table>
										</td>
										</tr>
										<tr>
										<td>
											<table style="font-size: 16px; border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
												<tbody style="font-size: 14px;">
													<tr><td style="font-family : Comic Sans Bold; padding:5px 0 10px;font-weight: bold;font-size: 16px;">' . lang('app.language_school_access_details_howtoaccess_title') . '</td></tr>
													<tr>
													<td style="font-family : Comic Sans Regular;padding: 5px 0px 10px;">' . lang('app.language_school_access_details_howtoaccess_primary_intro') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description1') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description2') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description3') . '</td>
													</tr>
													<tr><td style="font-family : Comic Sans Bold;padding:15px 0 10px;font-weight: bold;font-size: 16px;">' . lang('app.language_school_access_details_usingcats_title') . '</td></tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 0px 5px;">' . lang('app.language_school_access_details_usingcats_primary_description1') . '</td>
														</tr>
														<tr>
															<td style="font-family : Comic Sans Regular;padding: 5px 0px 10px;">' . lang('app.language_school_access_details_usingcats_primary_description2') . '</td>
														</tr>
														<tr>
															<td style="font-family : Comic Sans Regular;padding: 5px 0px 10px;font-size: 16px;">' . lang('app.language_school_access_details_footer_greeting') . '</td>
														</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table style=" border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
												<tr>
												<td style="font-family: DejaVu Sans, sans-serif;font-size:16px;text-align:left; line-height:16px;"><strong>' . lang('app.language_school_access_details_fullname') . ': ' . $accessdetail->firstname . ' ' . $accessdetail->lastname . '</strong></td>
												</tr>
												<tr>
												<td style="font-family: DejaVu Sans, sans-serif;font-size:16px;text-align:left; line-height:16px;"><strong>' . lang('app.language_school_access_details_dob') . ': ' . date('d-M-Y', $accessdetail->dob) . '</strong></td>
												</tr>	
											</table>
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>';
                endif;
                if ($accessdetail->cats_product == 'cats_core'):
                    $pdf_content .= '
					<table width="100%" style="padding: 10px 25px 10px 25px;">
						<tr>
							<td>
								 <table width="100%" >
									<tbody>
										<tr><td style="padding-bottom: 30px;border-bottom: 3px solid #1cbadf;"><img src="'.$logo_url.'" alt="logo"/></td></tr>
										<tr>
										<td>
										<table style="font-size: 16px; border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
										<tbody style="font-size:14px;">
										<tr><td style="font-family: DejaVu Sans, sans-serif;padding:5px 0 5px;"><b>' . lang('app.language_school_access_details_hello') . ' ' . $accessdetail->firstname . '</b></td></tr>
										<tr>
										<td style="font-family : Comic Sans Regular;padding: 5px 0px 5px;">' . lang('app.language_school_access_details_intro') . '</td>
										</tr>
										<tr>
											<td>
											<table>
											<tr>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:right;"><strong>' . lang('app.language_school_access_details_username') . ': </strong></td>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:left;"><strong>' . $accessdetail->username . '</strong></td>
												</tr>
											<tr>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:right;"><strong>' . lang('app.language_school_access_details_password') . ': </strong></td>
												  <td style="padding: 5px 0px;font-family: courier;font-size:20px;text-align:left;"><strong>' . $this->encrypter->decrypt(base64_decode($accessdetail->password_visible))  /* $accessdetail->password_visible*/. '</strong></td>
												</tr>	
											</table>
											</td>
										</tr>
										</tbody>
										</table>
										</td>
										</tr>
										<tr>
										<td>
											<table style="font-size: 16px; border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
												<tbody style="font-size: 14px;">
													<tr><td style="padding:5px 0 10px;font-weight: bold;font-size: 16px;">' . lang('app.language_school_access_details_howtoaccess_title') . '</td></tr>
													<tr>
													<td style="font-family : Comic Sans Bold;padding: 5px 0px 10px;">' . lang('app.language_school_access_details_howtoaccess_core_intro') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description1') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description2') . '</td>
													</tr>
													<tr>
														<td style="font-family : Comic Sans Regular;padding: 5px 20px;">' . lang('app.language_school_access_details_howtoaccess_primary_description3') . '</td>
													</tr>
													<tr><td style="font-family : Comic Sans Bold;padding:15px 0 10px;font-weight: bold;font-size: 16px;">' . lang('app.language_school_access_details_usingcats_title') . '</td></tr>
													<tr>
														<td style=" font-family : Comic Sans Regular;padding: 5px 0px 5px;">' . lang('app.language_school_access_details_usingcats_core_description1') . '</td>
														</tr>
														<tr>
															<td style="font-family : Comic Sans Regular;padding: 5px 0px 10px;">' . lang('app.language_school_access_details_usingcats_core_description2') . '</td>
														</tr>
														<tr>
															<td style="font-family : Comic Sans Regular;padding: 5px 0px 10px;font-size: 16px;">' . lang('app.language_school_access_details_footer_greeting') . '</td>
														</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table style=" border:1px solid #ccc; padding:10px 20px;margin-top: 20px;width:100%;border-radius: 10px;">
												<tr>
												<td style="font-family: DejaVu Sans, sans-serif;font-size:16px;text-align:left; line-height:16px;"><strong>' . lang('app.language_school_access_details_fullname') . ': ' . $accessdetail->firstname . ' ' . $accessdetail->lastname . '</strong></td>
												</tr>
												<tr>
												<td style="font-family: DejaVu Sans, sans-serif;font-size:16px;text-align:left; line-height:16px;"><strong>' . lang('app.language_school_access_details_dob') . ': ' . date('d-M-Y', $accessdetail->dob) . '</strong></td>
												</tr>	
											</table>
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>';
                endif;
            endforeach;
            $pdf_content .= '</body>
			</html>	';
           // echo $pdf_content; die;
            $name = lang('app.language_school_access_details_report') . " on - " . date("d-m-Y");

            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $dompdf = new Dompdf($options);
            //echo $pdf_content; die;
            $dompdf->loadHtml($pdf_content);
            // $dompdf->setPaper('A4', 'portrait');
            $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
            $dompdf->set_option('isHtml5ParserEnabled', true);

            $dompdf->render();
            $dompdf->stream($name);
            exit();
        }
    }

    public function get_last_generated($instituteid = FALSE) {
        if ($instituteid) {
            $query = $this->db->query('SELECT date_generated FROM learner_generated_details WHERE school_user_id = ' . $instituteid);
            $result = $query->getRowObject();
            if ($query->getNumRows() > 0) {
                return $result->date_generated;
            } else {
                return FALSE;
            }
        }
    }

    // Generate and download primary results PDF version
	public function primary_final_result(){
        
	    if (!$this->acl_auth->logged_in()) {
	        return redirect()->to('/');
	    }
	    if($this->request->getPost()){

	        $u13userid = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13id')));
	        $u13thirdpartyid = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13thirdpartyid')));
            //$this->lang->load('page_lang', 'english');
            if($u13thirdpartyid) {
                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($u13thirdpartyid);
            }

            if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                $userDetails = $this->usermodel->fetch_u13learner_details_tds($u13userid, @$this->session->get('user_id'), $u13thirdpartyid);

                if ($userDetails[0]->processed_data != '') {
                    $processed_result = (array)json_decode($userDetails[0]->processed_data);
                    $percentage = (array)$processed_result['overall'];
                }
            } else {
                $userDetails = $this->usermodel->fetch_u13learner_details($u13userid, @$this->session->get('user_id'));
	        
                if ($userDetails[0]->section_one != '') {
                    $percentage = @get_primary_results(@$userDetails[0]->section_one, @$userDetails[0]->section_two);
                }
            }

	        //QR generation - WP-1221
	        $qr_code_url = $google_url = '';
	        $qrcode_params = @generateQRCodePath('school', 'primary', $userDetails[0]->candidate_id, false);
	        if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
	            $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
	            $qr_result = json_decode($qrcode);
	            $qr_code_url = $qr_result->qrcode_abs;
	            $google_url = $qr_result->url;
	        }

            $tz_to = $this->bookingmodel->get_institution_timezone($userDetails[0]->thirdparty_id);
	        $institution_zone_values = @get_institution_zone_from_utc($tz_to, $userDetails[0]->start_date_time, $userDetails[0]->end_date_time);
            $userDetails[0]->date_of_exam = $institution_zone_values['institute_event_date'];
	        $level = substr( $userDetails[0]->level, 1);

	        $userDetails[0]->leveltext = _part_setup($level);
	        $userDetails[0]->percentage = $percentage['percentage'];

            $router = service('router'); 
            // $router->methodName(); 

            // call in class name
    	    // $userDetails[0]->u13controller = $router->methodName(); 
	        $userDetails[0]->u13controller = $router->methodName(); 

	        $userDetails[0]->qr_url = $qr_code_url;
            $userDetails[0]->google_url = $google_url;

            if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                @generatePrimaryTdsResultsPDF($userDetails);
            } else {
                @generatePrimaryResultsPDF($userDetails);
            }
	    }
	}

      //get purchased history
      function purchased_history() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('school'));
        }
        header('Content-Type: application/json');
        if (null !== $this->request->getPost()) {

            $user_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13_learner_id')));
            $purchased_results = $this->usermodel->get_user_purchashed_course($user_id);
            $data['purchased_results'] = $purchased_results;
            $dataSuccess = array('success' => 1, 'html' => view('school/purchase_history', $data));
            echo json_encode($dataSuccess);
            exit;
        }
    }

    public function redirect_tier(){
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/'));
        }
        $tier_userid = $this->session->get('logged_tier1_userid');

        $this->session->set('user_id', $tier_userid);
        $this->session->remove('selected_tierid');
        $this->session->remove('logged_tier1_userid');
        
        $role_details = current($this->usermodel->chk_role($tier_userid));

        $current_user_role_id  = $role_details['roles_id'];
        
        if($current_user_role_id == 8){
            $result = array('success' => '1', 'tier_type' => 'tier1');
        } elseif($current_user_role_id == 9) {
            $result = array('success' => '1', 'tier_type' => 'tier2');
        }
        echo json_encode($result);
    }

    public function export_tds_results(){
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/'));
        }
		
		if (null !== $this->request->getPost()) {
			
            $rules =[
                'product_type' => [
                  'label'  => lang('app.language_school_product_to_choose'),
                  'rules'  => 'trim|required',
                  ],
                  'result_startdate' => [
                    'label' => lang('app.language_school_result_startdate'),
                    'rules' => 'trim|required',
                  ],
                  'result_enddate' => [
                    'label' => lang('app.language_school_result_enddate'),
                    'rules' => 'trim|required',
                  ],
                  'result_type' => [
                    'label' => lang('app.language_school_result_export_type'),
                    'rules' => 'trim|required',
                  ]
          ];
			
			$data = array(
						'product_type' => $this->request->getPost('product_type'),
						'result_startdate' => $this->request->getPost('result_startdate'),
						'result_enddate' => $this->request->getPost('result_enddate'),
						'result_type' => $this->request->getPost('result_type'),
					);
			
			if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = array(
                    'product_type' => $this->validation->showError('product_type'),
                    'result_startdate' => $this->validation->showError('result_startdate'),
                    'result_enddate' => $this->validation->showError('result_enddate'),
                    'result_type' => $this->validation->showError('result_type')
                );

                $response['errors'] = $errors;
				
				$this->session->setFlashdata('result_data',$this->request->getPost());
                $this->session->setFlashdata('errors',$this->validation->getError('product_type').$this->validation->getError('result_startdate').$this->validation->getError('result_enddate').$this->validation->getError('result_type') );
				return redirect()->to(site_url('school/dashboard'));
            }else{
				$startdate = strtotime(str_replace('/', '-', $this->request->getPost('result_startdate'))); 
				$enddate = strtotime(str_replace('/', '-', $this->request->getPost('result_enddate')));
				$datediff = $enddate - $startdate;
				
				$period_days = round($datediff / (60 * 60 * 24));
				
				if($period_days > 30){
					$this->session->setFlashdata('result_data',$this->request->getPost());

					$this->session->setFlashdata('errors','Please select date range for one month' );
					return redirect()->to(site_url('school/dashboard'));
				}
				$result_type = $this->request->getPost('result_type');		
				$product_type = $this->request->getPost('product_type');		
				$result_startdate = $this->request->getPost('result_startdate');		
				$result_enddate = $this->request->getPost('result_enddate');	
				if($product_type == "stepcheck" &&  $result_type){
                                    if($result_type == 'csv'){
                                        $this->export_tds_results_csv($product_type,$result_startdate,$result_enddate);
                                        return redirect()->to(site_url('school/dashboard'));
                                    }else{
                                        $this->export_tds_results_pdf($this->session->get('user_id'), $product_type,$result_startdate,$result_enddate);
                                        return redirect()->to(site_url('school/dashboard'));
                                    }	
				}elseif ($product_type != "stepcheck" && $result_type == 'pdf'){ 
                    // WP-1221 - PDF bulk download
				    $this->export_tds_results_pdf($this->session->get('user_id'), $product_type, $result_startdate, $result_enddate);
                    return redirect()->to(site_url('school/dashboard'));
				}else{
				    $this->session->setFlashdata('errors','Un supported result format.' );
				    return redirect()->to(site_url('school/dashboard'));
				}
			}
		}
		
	}

    public function export_tds_results_csv($product_type,$result_startdate,$result_enddate){
		
		if($product_type == 'stepcheck'){
			$benchmark_results = $this->schoolmodel->get_tds_benchmark_results($result_startdate,$result_enddate);
			
			$heading_array = array('firstname' => 'First name','lastname' => 'Second name', 'email' => 'User name/Email', 'result_date' => 'Date of test', 'test_name' => 'Type of test taken', 'cefr_listening' => 'CEFR level for Listening','listening_score' => 'Position on CATs scale for Listening','cefr_reading' => 'CEFR level for Reading','reading_score' => 'Position on CATs scale for Reading','cefr_speaking' => 'CEFR level for Speaking','speaking_score' => 'Position on CATs scale for Speaking','cefr_writing' => 'CEFR level for Writing','writing_score' => 'Position on CATs scale for Writing','cefr_overall' => 'CEFR level overall','cefr_score' => 'Position on CATs scale for overall');
			
			$newarray = array();
	        $count = 0;
	        if (!empty($benchmark_results)) {
	            foreach ($benchmark_results as $key => $val) {
					if (!empty($val->firstname)) {
	                    $newarray[$count]['firstname'] = $val->firstname;
	                } else {
	                    $newarray[$count]['firstname'] = 'Not available';
	                }
					if (!empty($val->lastname)) {
	                    $newarray[$count]['lastname'] = $val->lastname;
	                } else {
	                    $newarray[$count]['lastname'] = 'Not available';
	                }
					if (!empty($val->email)) {
	                    $newarray[$count]['email'] = $val->email;
	                } else {
	                    $newarray[$count]['email'] = 'Not available';
	                }
					if (!empty($val->result_date)) {
	                    $newarray[$count]['result_date'] = $val->result_date;
	                } else {
	                    $newarray[$count]['result_date'] = 'Not available';
	                }
					if (!empty($val->test_name)) {
	                    $newarray[$count]['test_name'] = $val->test_name;
	                } else {
	                    $newarray[$count]['test_name'] = 'Not available';
	                }
					
					$processed_data = json_decode($val->processed_data);
					
					if (isset($processed_data->listening) && !empty($processed_data->listening->level) && ($processed_data->listening->score >= 0)) {
	                    $newarray[$count]['cefr_listening'] = $processed_data->listening->level;
                        $newarray[$count]['listening_score'] = $processed_data->listening->score;
	                } else {
	                    $newarray[$count]['cefr_listening'] = 'Not available';
                        $newarray[$count]['listening_score'] = 'Not available';
	                }
					if (isset($processed_data->reading) && !empty($processed_data->reading->level) && ($processed_data->reading->score >= 0)) {
	                    $newarray[$count]['cefr_reading'] = $processed_data->reading->level;
                        $newarray[$count]['reading_score'] = $processed_data->reading->score;
	                } else {
	                    $newarray[$count]['cefr_reading'] = 'Not available';
                        $newarray[$count]['reading_score'] = 'Not available';
	                }
					if (isset($processed_data->speaking) && !empty($processed_data->speaking->level) && ($processed_data->speaking->score >= 0)) {
	                    $newarray[$count]['cefr_speaking'] = $processed_data->speaking->level;
                        $newarray[$count]['speaking_score'] = $processed_data->speaking->score;
	                } else {
	                    $newarray[$count]['cefr_speaking'] = 'Not available';
                        $newarray[$count]['speaking_score'] = 'Not available';
	                }
					if (isset($processed_data->writing) && !empty($processed_data->writing->level) && ($processed_data->writing->score >= 0)) {
	                    $newarray[$count]['cefr_writing'] = $processed_data->writing->level;
                        $newarray[$count]['writing_score'] = $processed_data->writing->score;
	                } else {
	                    $newarray[$count]['cefr_writing'] = 'Not available';
                        $newarray[$count]['writing_score'] = 'Not available';
	                }
					if (isset($processed_data->overall) && !empty($processed_data->overall->level)  && ($processed_data->overall->score >= 0)) {
	                    $newarray[$count]['cefr_overall'] = $processed_data->overall->level;
                        $newarray[$count]['cefr_score'] = $processed_data->overall->score;
	                } else {
	                    $newarray[$count]['cefr_overall'] = 'Not available';
                        $newarray[$count]['cefr_score'] = 'Not available';
	                }
					
					$count ++;
				}
				
				array_unshift($newarray, $heading_array);
                echo array_to_csv($newarray, 'StepCheck Results - Date as on-' . date('d-m-Y') . '.csv');
	            exit;
			}else{
                    $this->session->setFlashdata('messages', lang('app.language_school_pdf_results_download_no_record'));
                    //return redirect()->to(site_url('school/dashboard'));
	    }
			
			
		}
	}

    public function export_tds_results_pdf($institution_user_id, $product_type, $result_startdate, $result_enddate){
	    $startdate = date('Y-m-d', strtotime(str_replace('/', '-', $result_startdate)));
	    $enddate = date('Y-m-d', strtotime(str_replace('/', '-', $result_enddate)));
	    $results_arr = array('institution_user_id' => $institution_user_id, 'start_date' => $startdate, 'end_date'=> $enddate, 'product_group'=> $product_type);
	    if ($product_type == 'primary') {
	        $check_results = $this->cmsmodel->get_primary_details($institution_user_id, $startdate, $enddate);
	    }
	    if ($product_type == 'core') {
	        $check_results = $this->cmsmodel->get_core_details($institution_user_id, $startdate, $enddate);
	    }
	    if ($product_type == 'higher') {
	        $check_results = $this->cmsmodel->get_higher_details($institution_user_id, $startdate, $enddate);
	    }
            if ($product_type == 'stepcheck') {
                $check_results = $this->schoolmodel->get_tds_benchmark_results($result_startdate,$result_enddate);
            }
	    if($check_results){
                $insert_results_download = $this->schoolmodel->insert_results_download($results_arr);
                $this->session->setFlashdata('messages', lang('app.language_school_pdf_results_download_success_msg'));
                //return redirect()->to(site_url('school/dashboard'));
	    }else{
	        $this->session->setFlashdata('messages', lang('app.language_school_pdf_results_download_no_record'));
	        //return redirect()->to(site_url('school/dashboard'));
	    }
	}

	// Verify QR code for Primary user
	public function u13qrverify($guid = false){
	    if(preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid)){
	        $query = $this->db->query('SELECT * FROM  collegepre_results WHERE candidate_id = "' .  $guid . '" LIMIT 1');
	        if ($query->getNumRows() > 0) {
	            $results = $query->getRowArray();
	            
	            $thirdPartyId = $results['thirdparty_id'];
	            $user_app_id    = substr($thirdPartyId,0,10);
	            $course_id      = substr($thirdPartyId,10,2);
	            $attempt_no     = substr($thirdPartyId,12,2);
	            //$u13userid      = substr($thirdPartyId,6,4);
	            $u13learnerquery = $this->db->query('SELECT * FROM  users WHERE user_app_id = "' .  $user_app_id . '" LIMIT 1');
	            if ($u13learnerquery->getNumRows() > 0) {
	                $u13learner = $u13learnerquery->getRowArray();
	            }
	            $instituitionquery = $this->db->query('SELECT * FROM  instituition_learners WHERE user_id = "' .  $u13learner['id'] . '" LIMIT 1');
	            if ($instituitionquery->getNumRows() > 0) {
	                $instituition = $instituitionquery->getRowArray();
	            }
	            
	            $userDetails = $this->usermodel->fetch_u13learner_details(@$u13learner['id'], @$instituition['instituition_id']);
	            
	            if ($userDetails[0]->section_one != '') {
	                $percentage = @get_primary_results(@$userDetails[0]->section_one, @$userDetails[0]->section_two);
	            }
	            $tz_to = $this->bookingmodel->get_institution_timezone($userDetails[0]->thirdparty_id);
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $userDetails[0]->start_date_time, $userDetails[0]->end_date_time);
                    $date_exam_event = $institution_zone_values['institute_event_date'];
	            $level = substr( $userDetails[0]->level, 1);
	            
	            //QR generation - WP-1221
	            $qr_code_url = $google_url = '';
	            $qrcode_params = @generateQRCodePath('school', 'primary', $userDetails[0]->candidate_id, false);
	            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
	                $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
	                $qr_result = json_decode($qrcode);
	                $qr_code_url = $qr_result->qrcode_abs;
	                $google_url = $qr_result->url;
	            }
	            
	            $this->data['results'] = array(
	                'userDetails' => (array)$userDetails[0],
	                'leveltext'   => @_part_setup($level),
	                'percentage' => $percentage['percentage'],
	                'qr_url' => $qr_code_url,
                    'date_exam_event' => $date_exam_event,
	                'google_url' => $google_url,
	            );
	            
                echo view('school/u13qrverify-view', $this->data);
	        }
        } elseif(!empty($guid)) { //tds_result
            $query = $this->db->query('SELECT * FROM  tds_results WHERE candidate_id = "' .  $guid . '" LIMIT 1');
	        if ($query->getNumRows() > 0) {
	            $results = $query->getRowArray();
	            
	            $thirdPartyId = $results['candidate_id'];
	            $user_app_id    = substr($thirdPartyId,0,10);
	            $course_id      = substr($thirdPartyId,10,2);
	            $attempt_no     = substr($thirdPartyId,12,2);
	            //$u13userid      = substr($thirdPartyId,6,4);
	            $u13learnerquery = $this->db->query('SELECT * FROM  users WHERE user_app_id = "' .  $user_app_id . '" LIMIT 1');
	            if ($u13learnerquery->getNumRows() > 0) {
	                $u13learner = $u13learnerquery->getRowArray();
	            }
	            $instituitionquery = $this->db->query('SELECT * FROM  instituition_learners WHERE user_id = "' .  $u13learner['id'] . '" LIMIT 1');
	            if ($instituitionquery->getNumRows() > 0) {
	                $instituition = $instituitionquery->getRowArray();
	            }
	            
	            $userDetails = $this->usermodel->fetch_u13learner_details_tds(@$u13learner['id'], @$instituition['instituition_id'], $thirdPartyId);
	            
	            if ($userDetails[0]->processed_data != '') {
                    $processed_result = (array)json_decode($userDetails[0]->processed_data);
                    $percentage = (array)$processed_result['overall'];
	            }
	            $tz_to = $this->bookingmodel->get_institution_timezone($userDetails[0]->thirdparty_id);
                    $institution_zone_values = @get_institution_zone_from_utc($tz_to, $userDetails[0]->start_date_time, $userDetails[0]->end_date_time);
                    $date_exam_event = $institution_zone_values['institute_event_date'];
	            $level = substr( $userDetails[0]->level, 1);
	            
	            //QR generation - WP-1221
	            $qr_code_url = $google_url = '';
	            $qrcode_params = @generateQRCodePath('school', 'primary', $userDetails[0]->candidate_id, false);
	            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
	                $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
	                $qr_result = json_decode($qrcode);
	                $qr_code_url = $qr_result->qrcode_abs;
	                $google_url = $qr_result->url;
	            }
	            
	            $this->data['results'] = array(
	                'userDetails' => (array)$userDetails[0],
	                'leveltext'   => @_part_setup($level),
	                'percentage' => $percentage['percentage'],
	                'qr_url' => $qr_code_url,
                    'date_exam_event' => $date_exam_event,
	                'google_url' => $google_url,
	            );
	            
                echo view('school/u13qrverify_tds_view', $this->data);
	        }
        } else{
	        echo 'Not a valid GUID';
	    }
	}

    public function zip_download($foldername = false, $filename = false){
        $filename = $filename . '.zip';
        header("Content-type: application/zip");
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $zip_path =$this->efsfilepath->efs_uploads_bulk_dwn.$foldername.'/'.$filename;
        $download_zip = @file_get_contents($zip_path);
        echo $download_zip;
        die;
    }

    //WP-1392 Fetch sso url via zendesk jwt token
    public function get_zend_desk_url_changing(){
        $user_id = $this->request->getPost('tier_id');  
        $location = @get_zend_desk_url($user_id);
        echo $location;
        die;
    }

 }
