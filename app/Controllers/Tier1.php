<?php

namespace App\Controllers;
use DateTimeZone;
use DateTime;
use DateInterval;
use App\Libraries\Acl_auth;
use Config\MY_Lang;
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
use Config\Oauth;

/**
 * 
 * @property Ciqrcode $ciqrcode
 * 
 */

class Tier1 extends BaseController {

    function __construct() {
		//pagiation
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
        $this->Ciqrcode = new Ciqrcode();
        $this->encrypter = \Config\Services::encrypter();
        $this->Placement_model = new Placementmodel();
        $this->session = \Config\Services::session();
        $this->passwordhash = new \Config\PasswordHash(8,FALSE);
        if(null !== $this->session->get('user_id')){
            $organisation_id = $this->session->get('user_id');
            $this->data['product_eligiblity'] = $this->Placement_model->get_product_eligiblity($organisation_id);
            $group_ids  = $this->data['product_eligiblity'];
        }

        $data['languages'] = $this->cmsmodel->get_language();
        $this->lang_switch($this->lang->lang());

        define("EVENT_END_TIME_ADD_HOUR", "+4 hours");
        helper('qrcodepath');
        helper('corepdf_helper');
        helper('efs_path_helper');
        helper('core_certificate_language_helper');
        helper('corepdf_extended_helper');
        helper('corepdf_helper');
        helper('parts_helper');
        helper('higherpdf_helper');
        helper('downtime_helper');
        helper('percentage_helper');
        helper('benchmarkpdf_helper');
        helper('cms');
        helper('zendesk');
        $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
        $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');

        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            $controller = explode("/",$url['home_page_url']);
            if($controller['0'] == "tier1"){
                // only allow users with 'learner' role to access all methods in this controller
                $this->acl_auth->restrict_access('tier1');
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
        //WP-1391 zendesk Tier1 user add if not
        if(isset($this->zendesk_access) && $this->zendesk_access == 1){
            $user_id = $this->session->get('user_id');
            $zendesk_wp_user = @get_zendesk_user_list($this->session->get('user_id'));
            $is_active = @zendesk_user_is_active($user_id);
            if($zendesk_wp_user == false && isset($is_active) && $is_active['status'] == 1){
                @zendesk_user_create($this->zendesk_domain_url,$user_id,"Create","Tier1 Login");
            }
        }
    }

    function index() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        } else {
            return redirect()->to(site_url('tier1/dashboard')); 
        }
    }
    public function dashboard() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
		$data['institute_search_item'] = (isset($_GET['institute_search']) && $_GET['institute_search'] != '') ? $_GET['institute_search'] : '';
		$data['institute_data'] = $this->institute_list();
        $data['languages'] = $this->cmsmodel->get_language();
        echo  view('tier1/header');
        echo  view('tier1/menus', $data);
        echo  view('tier1/dashboard', $data);
        echo  view('tier1/footer');

    }
    //log out from session
    function logout() {
        if($this->session->get('Yellowfin_loginSessionId') != ''){
            return redirect()->to('report/logoutUser'); 
        }  
        $success = $this->acl_auth->logout();
        $this->session->keepFlashdata('messages', lang('app.language_distributor_logout_success_msg'));
        return redirect()->to('/'); 
        
    }
	//switching languages
    function lang_switch($lang_code) {
        $allanguages = $this->cmsmodel->get_language();

        foreach ($allanguages as $language) {
            if ($lang_code === $language->code) {
                //$this->lang->load('tier', strtolower($language->name));
            }
        }
    }
	
	public function institute_list(){

        $institute_search_item = (isset($_GET['institute_search']) && $_GET['institute_search'] != '') ? $_GET['institute_search'] : '';
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $tier2_institutions_segment = array_search('dashboard',$TotalSegment_array,true);

        $segment = $tier2_institutions_segment + 2;
        $pager = "";
        $total_rows = $this->usermodel->record_institute_user_count(trim($institute_search_item));

        if($total_rows > 10){
            $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
            $this->pager->makeLinks($page+1, $perPage, $total_rows, 'default_full', $segment, 'pagination_tier1_institution_list');
            $offset = $page * $perPage;
            $pager = $this->pager;
        }
        $data = array(
            'institute_search_item' => $institute_search_item,
            'institute_users' => $this->usermodel->fetch_institute_users($perPage, $offset, trim($institute_search_item)),
            'pagination' => $pager,
        );      
        return $data;
	}
	
	public function redirect_school(){
		if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
		if(null !== $this->request->getPost() && $this->request->getPost('tier_id') && $this->request->getPost('tier_id') != ''){
		
            $builder = $this->db->table('institution_tier_admins');
			$builder->select('admin_user_id');
			$builder->where('institution_tier_id', $this->request->getPost('tier_id'));
			$query = $builder->get();
			
			if ($query->getNumRows() > 0) {
				$result = $query->getRow();
				$admin_user_id = $result->admin_user_id;
				
                $builder = $this->db->table('institution_tiers');
				$builder->select('organization_name');
				$builder->where('id', $this->request->getPost('tier_id'));
				$query = $builder->get();
				$tier_result = $query->getRow();
				$institute_name = $tier_result->organization_name;
				$this->session->set('selected_tierid', $this->request->getPost('tier_id'));
				$this->session->set('logged_tier1_userid', $this->session->get('user_id'));
				$this->session->set('user_id', $admin_user_id);
				$this->session->set('institute_name', $institute_name);
	
				$result = array('success' => '1');
				echo json_encode($result);
			}
		}
		
	}
	
	public function set_session(){
		$this->session->set('tier_selected_option', $this->request->getPost('selected_option'));
		print_r($this->session->get()); die;
	}
	
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

            $rules = [
                'firstname' => [
                    'label'  => lang('app.language_site_booking_screen2_label_first_name'),
                    'rules'  => 'required|max_length[100]|serbia_username_check',
                    'errors' => [
                        'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                    ]
                ],
                'secondname' => [
                    'label'  => lang('app.language_site_booking_screen2_label_second_name'),
                    'rules'  => 'required|max_length[100]|serbia_username_check',
                    'errors' => [
                        'serbia_username_check' => lang('app.language_site_booking_screen2_firstname_check'),
                    ]
                ],
            ];
            if($userdata['user_email'] != $this->request->getPost('email') ){
                $rules['email'] = [
                    'label'  => lang('app.language_site_booking_screen2_label_email_address'),
                    'rules'  => 'required|max_length[254]|isemail_check|is_unique[users.email]',
                        'errors' => [
                            'isemail_check' => lang('app.form_validation_valid_email'),
                        ]
                    ];
            }
            //WP-1271 - Change email address - ends
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                $this->data['validation'] = $this->validator;
            } else {
                $profiledata = array('name' => $this->request->getPost('firstname')." ".$this->request->getPost('secondname'),'firstname' => $this->request->getPost('firstname'), 'lastname' => $this->request->getPost('secondname'), 'email' => $this->request->getPost('email'));

                if ($this->usermodel->update_profile($profiledata)) {
                    $this->session->set('user_firstname', $this->request->getPost('firstname'));
                    $this->session->set('user_lastname', $this->request->getPost('secondname'));
                    $this->session->set('user_email', $this->request->getPost('email'));//WP-1271 - Change email address
                    $this->session->set('username', $this->request->getPost('email'));//WP-1271 - Change email address
                    //WP-1391 zendesk Tier1 profile update
                    if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                            $user_id = $userdata['user_id'];
                            zendesk_profile_update($user_id,"Tier1 Update");   
                        }
                    $this->session->setFlashdata('messages', lang('app.language_admin_profile_updated_success_msg'));
                    return redirect()->to(site_url('tier1/profile'));
                }
            }
        }

         //change password
         $this->passwordhash = new \Config\PasswordHash(8,FALSE);
         if ($this->request->getPost('changepass_submit')) {
             $this->session->set('tabprofile', FALSE);
             $this->session->set('tabpass', TRUE);
                 $rules = [
                        'current_password' => [
                            'label'  => 'Current Password',
                            'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                            'errors' => [
                                'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                            ]
                        ],
                        'new_password' => [
                            'label'  => 'New Password',
                            'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                            'errors' => [
                                'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                            ]
                        ],
                        'confirm_new_password' => [
                            'label'  => 'Confirm New Password',
                            'rules'  => 'required|max_length[100]|min_length[8]|matches[new_password]'
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
                     return redirect()->to(site_url('tier1/profile/'.$this->session->get('tabpass')));
                 } else {
                     $passwordata = array('password' => $this->passwordhash->HashPassword($this->request->getPost('new_password')));
                     if ($this->usermodel->update_profile($passwordata)) {
                         $this->session->set('password', $this->request->getPost('new_password'));
                         $this->session->setFlashdata('messages', lang('app.language_site_change_password_updated_success_msg'));
                         $this->data['validation'] = $this->validator;
                         return redirect()->to(site_url('tier1/profile/'.$this->session->get('tabpass')));
                     }
                 }
             }
         }
         $data['languages'] = $this->cmsmodel->get_language();
        echo view('tier1/header');
        echo view('tier1/menus', $data);
        echo view('tier1/profile', $this->data);
        echo view('tier1/footer');
    }

     //WP-1392 Fetch sso url via zendesk jwt token
	function get_zend_desk_url_changing(){
        $user_id = $this->request->getPost('tier_id');  
			$location = @get_zend_desk_url($user_id);
			echo $location;
            die;
	}

}
