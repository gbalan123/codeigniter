<?php

namespace App\Controllers;
use SimpleXMLElement;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use CodeIgniter\Files\File;

use DateTimeZone;
use DateTime;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use App\Libraries\Zip;

use App\Controllers\BaseController;
use App\Libraries\Acl_auth;
use App\Libraries\Unzip;
use App\Libraries\Encryptinc;


use App\Models\Admin\Cmsmodel;
use App\Models\Admin\Bannermodel;
use App\Models\Admin\Brochuremodel;
use App\Models\Admin\Productmodel;
use App\Models\Admin\Pricemodel;


use App\Models\Site\Bookingmodel;
use App\Models\Admin\Emailtemplatemodel;
use App\Models\Admin\Tdsmodel;
use App\Models\School\Schoolmodel;
use App\Models\Admin\Collegepremodel;
use App\Models\Admin\Placementmodel;

use App\Models\Usermodel;
use App\Config\Efsfilepath;
use Config\Oauth;
use Config\Site;
use App\Libraries\Ciqrcode;

/**
 * 
 * @property Ciqrcode $ciqrcode
 * 
 */

class Admin extends BaseController
{


    function __construct() {
        $this->pager = service('pager'); 
        $this->acl_auth = new Acl_auth();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->cmsmodel = new Cmsmodel();

        $this->validation =  \Config\Services::validation();
        $this->pagination = \Config\Services::pager();

        $this->request = \Config\Services::request();
        
        $this->bannermodel = new Bannermodel();
        $this->brochuremodel = new Brochuremodel();
        $this->productmodel = new Productmodel();
        $this->pricemodel = new Pricemodel();
        $this->usermodel = new Usermodel();
        $this->bookingmodel = new Bookingmodel();
        $this->emailtemplatemodel = new Emailtemplatemodel();
        $this->tdsmodel = new Tdsmodel();
        $this->schoolmodel = new Schoolmodel();
        $this->collegepremodel = new Collegepremodel();
        $this->placementmodel = new Placementmodel();

        $this->unzip = new Unzip();
        $this->zip = new Zip();
        $this->oauth = new \Config\Oauth();
        $this->site = new Site();
        $auth = service('auth');
        $this->passwordhash = new \Config\PasswordHash();

        $this->encrypter = \Config\Services::encrypter();
        $this->encryptinc = new Encryptinc();
        $efsfilepath = new \Config\Efsfilepath();
        $this->efsfilepath = $efsfilepath->get_Efs_path();

        define("LOG_FILE_PDF", $this->efsfilepath->efs_custom_log . "pdf_log.txt");
        $this->lang = new \Config\MY_Lang();
        $this->email = \Config\Services::email();
        $this->pager = service('pager');
        $this->Ciqrcode = new Ciqrcode();
        $this->efs_bulk_download_path = $this->efsfilepath->efs_uploads_bulk_dwn;
        /* TDS API CALL PARAMETER */
        $this->tdsLaunchUrl = $this->oauth->catsurl('testDeliveryUrl');
        $this->tdsKey = $this->oauth->catsurl('testLaunchKey');
        $this->tdsReferrer = $this->oauth->catsurl('testReferrer');
        $this->today = date('Y-m-d');
        $this->tds_start = strtotime($this->today . "-2 days");
        $this->tds_end = strtotime(date('Y-m-d', strtotime($this->today . ' + 1 days')));
        /* yellowfin access */
        $this->yellowfin_access = $this->oauth->catsurl('yellowfin_access');
        helper('yellowfin');
        helper('primaryresultspdf');
        helper('coreresultsPDF');
        helper('core_certificate_language');
        helper('coreresults_extendedpdf');
        helper('higherresultsPDF');
        helper('benchmarkresultsPDF');
        helper(['form', 'url']);
        helper('downtime_helper');
        helper('percentage_helper');
        helper('parts_helper');
        helper('qrcodepath');
        helper('csv_helper');
        helper('efs_path_helper');
        helper("sendinblue");
        helper('zendesk');

      define("LOG_FILE_TDS_CRON", $this->efsfilepath->efs_custom_log."tds_cron_2hr.txt");
      define("LOG_FILE_TDS", $this->efsfilepath->efs_custom_log . "tds.txt");

        /* zendesk */
        $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
        $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');

        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            $controller = explode("/",$url['home_page_url']);
            if($controller[0] == "admin"){
                /* only allow users with 'learner' role to access all methods in this controller */
                $this->acl_auth->restrict_access('admin');
            }else{
                $urlchange =  $url;
                $urlrewrite = implode(" ",$urlchange);
                header("Location: " . site_url($urlrewrite), TRUE, 302);
				exit;
            }
        }
    }

    function index() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login')); 
        } else {
            return redirect()->to(site_url('admin/dashboard')); 
        }
    }

    /* username exists check */
    function username_exists($username) {
        $this->usermodel->username_exists($username);
    }
    /* Admin login check function */
    function login() {
        if ($this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/dashboard'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_login_page'),
            'admin_heading' => lang('app.language_admin_login_page')
        );

        if ($this->request->getPost() != NULL) {

            $this->site->identity_field;
            if (str_contains($this->request->getPost('username'), '@')) {
                $this->acl_auth->set_item('identity_field', 'email');
                $input_field = 'email';
            } else {
                $this->acl_auth->set_item('identity_field', 'username');
                $input_field = 'username';
            }

            if ($input_field == 'email') {
                $rules = [
                    'username' => [
                        'label'  => lang('app.language_admin_username'),
                        'rules'  => 'trim|required|valid_email|callback_username_exists',
                    ],
                ];
            } else {
                $rules = [
                    'username' => [
                        'label'  => lang('app.language_admin_username'),
                        'rules'  => 'trim|required',
                    ],
                ];
            }
            $rules = [
                'username' => [
                    'label'  => lang('app.language_admin_username'),
                    'rules'  => 'trim|required',
                ],
                'password' => [
                    'label'  => lang('app.language_admin_password'),
                    'rules'  => 'required',
                ]
			];

            if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
                echo view('admin/login',$data);
			}

            else{
     
                $username = $this->request->getPost('username');
                $password = trim($this->request->getPost('password'));
                $remember = FALSE;
                $this->site->identity_field;
                if (str_contains($username, '@')) {
                    $this->acl_auth->set_item('identity_field', 'email');
                    $user = trim($username);
                } else {
                    $this->acl_auth->set_item('identity_field', 'username');
                    $user = trim($username);
                    
                }
                /* This logs the user in and writes user_name, user_email and user_phone into session. Also sets a cookie to remember the user. */
                if ($this->request->getPost('remember')) {
                    $remember = TRUE;
                } else {
                    $remember = FALSE;
                }

                $session_data = array('name', 'email', 'username', 'id');
                $success = $this->acl_auth->login($user, $password, FALSE, $session_data);

                if($success) {
                    $this->session->setFlashdata('messages', lang('app.language_admin_login_success_msg'));
                    return redirect()->to(site_url('admin/dashboard')); 
                } 
                else{
                    $this->session->setFlashdata('errors', lang('app.language_admin_login_failure_msg'));
                    return redirect()->to(site_url('admin/login'));  
                }
            } 
           }
        else {
            echo view('admin/login',$data);
        }
    }
    /* Admin logout function */
    function logout() {
        $success = $this->acl_auth->logout();
        $this->session->setFlashdata('messages', lang('app.language_admin_logout_success_msg'));
        return redirect()->to(site_url('admin/login'));
    }
    /* Admin dashboard function */
    function dashboard() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }        
        $data = array(
            'admin_title' => lang('app.language_admin_dashboard'),
            'admin_heading' => lang('app.language_admin_dashboard'),
            'cms_count' => $this->cmsmodel->record_count(),
            'banner_count' => $this->bannermodel->record_count(),
            'brochure_count' => $this->brochuremodel->record_document_count(),
            'product_count' => $this->productmodel->record_count(),
            'price_count' => $this->pricemodel->record_count(),
            'institute_count' => $this->usermodel->record_institutions_count()
        );
        echo view('admin/dashboard',$data);     
     }

    /* Admin list_app menu pagination function */
     function list_applinks() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
           /* pagination */
            $perPage =  10;
            $offset = 0;
            $pager = "";
            $total_rows = $this->cmsmodel->app_links_count();
            if($total_rows > 10){
            $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
            $offset = $page * $perPage;
            $this->pager->makeLinks($page+1, $perPage, $total_rows);
            $pager = $this->pager;
            }

        $data=array(
        'admin_title' => lang('app.language_admin_list_applinks'),
        'admin_heading' => lang('app.language_admin_list_applinks'),
        'results' => $this->cmsmodel->fetch_app_links($perPage, $offset),
        'pager' => $pager
        );
        echo view('admin/list_applinks',$data);
            
    }
    /* Admin list_app menu list function */
    function applinks($id) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
                'admin_title' => lang('app.language_admin_update_applink'),
                'admin_heading' => lang('app.language_admin_update_applink'),
                'languages' => $this->cmsmodel->get_language(),
                'applinks' => $this->cmsmodel->get_applinks($id)
       );
       echo view('admin/applinks',$data);
    }
    /* Admin list_app menu add & edit function */
    function post_applink($id = FALSE) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        if (null !== $this->request->getPost()) {
            $rules =[
                'app_link' => [
                  'label'  => 'App link',
                  'rules'  => 'required|valid_url',
                  ],
            ];
        
            if ($this->validate($rules) == FALSE) {

                $this->session->setFlashdata('errors',   $this->validation->getError());
                if (!empty($id)) {
                    return redirect()->to("admin/applinks/".$id);
                } else {
                    return redirect()->to('admin/applinks'); 
                }
            } else {
                if ($id != FALSE) {
        
                    if ($this->cmsmodel->update_applink($id, $this->request->getPost()) === TRUE) {
                        $this->session->setFlashdata('messages', lang('app.language_admin_page_updated_success_msg'));
                        return redirect()->to(site_url('admin/list_applinks')); 
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_admin_page_updated_failure_msg'));
                        return redirect()->to('admin/applinks/' . $id); 
                    }
                } 
            }
        } else {
            return redirect()->to('admin/applinks'); 
        }
    }
    /* Admin product menu list function */
    function product() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_product_upload_label'),
            'admin_heading' => lang('app.language_admin_product_upload_label')
        );
        echo view('admin/product',$data); 
    }
   /* Admin common CSV dewnload function */
    public function download_csv($filename){
        $csv_file =$this->efsfilepath->efs_uploads_sample_csv.$filename . '.csv';
        return $this->response->download($csv_file, null);
    }
    /* Admin product memnu preview_product function */
    function previewproduct() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $lexer = new Lexer(new LexerConfig());
        $interpreter = new Interpreter();
        $interpreter->unstrict(); // Ignore row column count consistency

        $interpreter->addObserver(function(array $rows) use (&$csvArray) {

            if (!empty($rows)) {
                $csvArray[] = array('id' => @$rows[0], 'name' => @$rows[1], 'level' => @$rows[2], 'progression' => @$rows[3], 'pgroup' => @$rows[4], 'audience' => @$rows[5], 'count' => count($rows));
            }
        });

        $lexer->parse($_FILES['products_csv']['tmp_name'], $interpreter);
        echo json_encode($csvArray);
    }


        /* Admin product memnu list function */
        function listproducts() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $data = array(
                'admin_title' => lang('app.language_admin_list_products'),
                'admin_heading' => lang('app.language_admin_list_products'),
                'results' => $this->productmodel->fetch_product(),
            );
            echo view('admin/listproducts',$data); 
        }
        /* Admin product memnu edit function */
        function listproducts_edit($institution_id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($institution_id != FALSE) {
                $data = array(                
                    'admin_heading' => lang('app.language_admin_product_edit'),
                    'products' => $this->productmodel->get_product($institution_id),                
                );
            }
            echo view('admin/listproducts_edit',$data); 
        }
        /* Admin product memnu update function */
        function listproducts_update() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
              if (null !== $this->request->getMethod()) {        
                $rules =[
                    'product_status' => [
                      'label'  => lang('app.language_admin_product_active_status'),
                      'rules'  => 'trim|required',
                      ],
                   ];

                if (!$this->validate($rules)) {
                    $response['success'] = 0;
                    $errors = array(
                        'product_status' => $this->validation->showError('product_status'),
                    );
                    $response['errors'] = $errors;
                    echo json_encode($response);die;
                }

                else{
                    /* start primary placement settings */
                    $id = $this->request->getPost('product_id');
                    $product = $this->productmodel->get_product($id);
                    $status = $this->request->getPost('product_status');
                    $audience = $this->request->getPost('audience');
                    if($status == '0' && $audience == 'Primary'){
                         /* get product settings for linear */
                        $linear_settings = $this->placementmodel->get_placement_settings('linear');
                        $logit_values = unserialize($linear_settings['logit_values']);
                        $logit_values[str_replace('.','_',$product[0]->level)] = '';
                        $data_placements = array('logit_values' => serialize($logit_values));
                        $builder = $this->db->table('placement_settings');
                        $builder->where('id',3);
                        $builder->update($data_placements); 
                    }
                    $data = array('active' => $this->request->getPost('product_status'));
                    $builder = $this->db->table('products');
                    $builder->where('id',$this->request->getPost('product_id'));
                    $builder->update($data); 
                    echo json_encode(array('success' => 1, 'msg' => 'product updated successfully'));
             }
          }
        }
        /* Admin downtime_setting memnu list function */
        public function get_down_time() 
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }

        /* pagination */
        $perPage =  10;
        $offset = 0;
        $pager = "";
        $total_rows = count($this->cmsmodel->get_down_time_lists());
        if($total_rows > 10){
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $offset = $page * $perPage;
        $this->pager->makeLinks($page+1, $perPage, $total_rows);
        $pager = $this->pager;
        }

         $down_time_lists = $this->cmsmodel->get_down_time_lists($perPage, $offset);

            $data = array(
                'admin_title' => lang('app.language_admin_downtime'),
                'admin_heading' => lang('app.language_admin_add_downtime'), 
                'down_time_lists' => $down_time_lists,
                'ip_address_lists' => $this->cmsmodel->get_maintanence_ip_address(),
                'pager' => $pager
            );
            echo view('admin/add_down_time',$data);
        }
        /* Admin downtime_setting memnu ip_delete function */
        public function delete_ip_address()
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $ip_result = $this->cmsmodel->delete_ip_details($this->request->getPost('ip_id'));
            if($ip_result) {
                $this->session->setFlashdata('messages', lang('app.language_admin_downtime_ip_delete_success'));
                echo json_encode(array('success' => 1, 'msg' => 'IP address deleted'));
                die;
            } else {
                $this->session->setFlashdata('errors', lang('app.language_admin_downtime_ip_delete_failure'));
                echo json_encode(array('success' => 0, 'msg' => 'IP address not deleted'));
                die;
            }
        }
        /* Admin downtime_setting memnu ip_edit function */
        public function ip_add_edit($id=FALSE)
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id == FALSE) {
                $data = array(
                    'admin_heading' => lang('app.language_admin_downtime_ip_add'),
                    'ip_data' => FALSE
                );
            } else {
                $data = array(
                    'admin_heading' => lang('app.language_admin_downtime_ip_edit'),
                    'ip_data' => $this->cmsmodel->get_maintanence_ip_address($id)
                );
            }
            echo view('admin/add_edit_ip_address',$data);
       
        }
        /* Admin downtime_setting memnu ip_add function */
        public function post_ip_add_edit()
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to('admin/login');
            }
            if ($this->request->getPost() !== Null) {

                $rules =[
                    'ip_address' => [
                        'label'  => lang('app.language_admin_downtime_ip_address'),
                        'rules'  => 'required|custom_trim[ip_address]',
                        ],
                        'ip_name' => [
                            'label'  => lang('app.language_admin_downtime_ip_name'),
                            'rules'  => 'required|custom_trim[ip_name]',
                        ],
                ];
    
                if ($this->validate($rules) == FALSE) {
                    $response['success'] = 0;
                    $errors = array(
                        'ip_address' => $this->validation->showError('ip_address'),
                        'ip_name' => $this->validation->showError('ip_name')
                    );
                    $response['errors'] = $errors;
                    echo json_encode($response);die;
                }else{				
                    if($this->request->getPost('id') == FALSE){
                        $ip_data = array(   
                            'ip_address' => $this->request->getPost('ip_address'),  
                            'ip_name' => $this->request->getPost('ip_name')  
                        );
                        $ip_result = $this->cmsmodel->insert_ip_details($ip_data);
    
                        if($ip_result) {
                            $this->session->setFlashdata('success','IP address details added successfully');						
                            echo json_encode(array('success' => 1, 'msg' => 'IP address details added successfully'));
                            die;
                        }
                    } else {
                        $ip_data = [  
                            'ip_address' => $this->request->getPost('ip_address'),  
                            'ip_name' => $this->request->getPost('ip_name')  
                        ];
                        $ip_result = $this->cmsmodel->update_ip_details($ip_data, $this->request->getPost('id'));
                        if($ip_result) {
                            $this->session->setFlashdata('success','IP address details updated successfully');						
                            echo json_encode(array('success' => 1, 'msg' => 'IP address details updated successfully'));
                            die;
                        }
                    }
                }
            }
        }
    
        /* Admin downtime_setting popup function */
        public function down_time_popup($id=FALSE)
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if($id==FALSE) {
                $data = array(
                    'admin_heading' => lang('app.language_admin_downtime_add'), 
                );
            } else {
                $data = array(
                    'admin_heading' => lang('app.language_admin_downtime_edit'),
                    'downtime_data' => $this->cmsmodel->get_id($id),
                );
            }
            $data['timezones'] = $this->get_timezone_list();
            echo view('admin/down_time_popup',$data);
        }

        /* Admin common current timezone function */
        public function check_current_time_based_on_timezone()
        {
            $start_date = $this->request->getPost('start_date');
            $start_time = $this->request->getPost('start_time');
            $timezone = $this->request->getPost('timezone');
    
            $date = new DateTime("now", new DateTimeZone($timezone));
    
            $current_date = $date->format('m/d/Y');
            $current_time = $date->format('H:i');
    
            if($start_date < $current_date) {
                echo json_encode(array('success' => 1, 'msg' => 'Start time must be greater than or equal '.$current_date.' '.$current_time.''));
                die;
            } else if( $start_date == $current_date ) {
                if($start_time < $current_time) {
                    echo json_encode(array('success' => 1, 'msg' => 'Start time must be greater than or equal '.$current_date.' '.$current_time.''));
                    die;
                } else {
                    echo json_encode(array('success' => 0));
                    die;
                }
            } else {
                echo json_encode(array('success' => 0));
                die;
            }
        }
        /* Admin downtime_setting add function */
        public function save_downtime() 
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to('admin/login');
            }
            header('Content-Type: application/json');

            if(null !== $this->request->getPost()){
                $current_date = (new DateTime("now", new DateTimeZone($this->request->getPost('timezone'))))->format('m/d/Y H:i');
                $rules =[
                    'start_date' => [
                        'label'  => lang('app.language_school_event_test_date'),
                        'rules'  => 'trim|required',
                    ],
                    'end_date' => [
                        'label'  => lang('app.language_school_event_test_date'),
                        'rules'  => 'trim|required',
                    ],
                    'start_time' => [
                        'label'  => lang('app.language_school_event_start_time'),
                        'rules'  => 'trim|required|time_check|validate_current_time_based_on_timezone',
                        'errors' => [
                            'time_check' => 'Start time and end time should not be equal',
                            'validate_current_time_based_on_timezone' =>  'Start time must be greater than or equal '.$current_date,
                        ]
                    ],
                    'end_time' => [
                        'label'  => lang('app.language_school_event_end_time'),
                        'rules'  => 'trim|required|time_check|end_time_check',
                        'errors' => [
                            'time_check' => 'Start time and end time should not be equal',
                            'end_time_check' =>  'End time should be greater than start time',
                        ]
                    ],
                    'timezone' => [
                        'label'  => lang('app.language_admin_institutions_timezone'),
                        'rules'  => 'required',
                    ],
                    
                ];

                if (!$this->validate($rules)) {
                    $response['success'] = 0;
                    $errors = array(
                        'start_date' => $this->validation->showError('start_date'),
                        'end_date' => $this->validation->showError('end_date'),
                        'starttime_error' => $this->validation->showError('start_time'),
                        'endtime_error' => $this->validation->showError('end_time'),
                        'timezone' => $this->validation->showError('timezone'),
                    );

                    $response['errors'] = $errors;
                    echo json_encode($response);die;

                } else {
                    if($this->request->getPost('id') == FALSE) {
                        $tz_from = $this->request->getPost('timezone');
                        /* insert down_time */
                        if($this->request->getPost('start_date') && $this->request->getPost('end_date') ){
                            $start_date_array = strtotime($this->request->getPost('start_date'));
                            $start_date = date('Y-m-d', $start_date_array);
                            $end_date_array = strtotime($this->request->getPost('end_date'));
                            $end_date = date('Y-m-d', $end_date_array);
                        }
        
                        //convert date&time into UTC
                        $start_date_time = @utc_date_time($tz_from,$start_date,$this->request->getPost('start_time'));
                        $end_date_time = @utc_date_time($tz_from,$end_date,$this->request->getPost('end_time'));
                        $datatime = array(
                            'start_date_time' => $start_date_time,
                            'end_date_time' => $end_date_time,
                            'status' => 1,
                            'timezone' => $this->request->getPost('timezone')
                        );

                        $down_time = $this->cmsmodel->insert_downtime($datatime);
                        if($down_time)
                        {					
                            $this->session->setFlashdata('success','Downtime added successfully.');						
                            echo json_encode(array('success' => 1, 'msg' => 'Downtime added successfully.'));
                            die;
                        }
                        else
                        {
                            $this->session->setFlashdata('error','Please try again....');
                            echo json_encode(array('success' => 0, 'msg' => 'Downtime added failure.'));
                            die;                
                        }
                    } else {
                        $tz_from = $this->request->getPost('timezone');
                        /* insert down_time */
                        if($this->request->getPost('start_date') && $this->request->getPost('end_date') ){
                            $start_date_array = strtotime($this->request->getPost('start_date'));
                            $start_date = date('Y-m-d', $start_date_array);
                            $end_date_array = strtotime($this->request->getPost('end_date'));
                            $end_date = date('Y-m-d', $end_date_array);
                        }
        
                        $start_date_time = @utc_date_time($tz_from,$start_date,$this->request->getPost('start_time'));
                        $end_date_time = @utc_date_time($tz_from,$end_date,$this->request->getPost('end_time'));
                        $datatime = [
                            'start_date_time' => $start_date_time,
                            'end_date_time' => $end_date_time,
                            'timezone' => $this->request->getPost('timezone')
                        ];

                        $down_time = $this->cmsmodel->update_downtime($datatime, $this->request->getPost('id'));
                        if($down_time)
                        {
                            $this->session->setFlashdata('success','Downtime updated successfully.');						
                            echo json_encode(array('success' => 1, 'msg' => 'Downtime updated successfully.'));
                            die;
                        }
                        else
                        {
                            $this->session->setFlashdata('error','Please try again....');
                            echo json_encode(array('success' => 0, 'msg' => 'Downtime updated failure.'));
                            die;             
                        }
                    }
                     
                }
            }
        }
        /* Admin downtime_setting active and inactive popup function */
        public function status_changes() {
            if (null !== $this->request->getPost() && $this->request->getPost('status_id')) {
                $status_id = $this->request->getPost('status_id');
                if(!empty($status_id)){
                    $id_details = $this->cmsmodel->get_id($status_id);          
                    if($id_details){
                        $status = $id_details['status'];
                        echo json_encode(array('success' => 1, 'status' => $status, 'msg' => 'fetched'));
                        die;
                    }else{
                        $this->session->setFlashdata('errors', lang('app.language_admin_downtime_failure_msg'));
                        echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                        die; 
                    }
                }else{
                    $this->session->setFlashdata('errors', lang('app.language_admin_downtime_failure_msg'));
                    echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                    die;    
                }
            }
        }
    /* Admin downtime_setting active and inactive update function */
    public function status_update() {
	    if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
	    }
        if (null !== $this->request->getPost() && $this->request->getPost('status_id')) {
            $tier_user_admin_id = $this->request->getPost('status_id');
            if(!empty($tier_user_admin_id)){
                $admin_user_details = $this->cmsmodel->get_id($tier_user_admin_id);          
                if($admin_user_details){
                        $lang = $admin_user_details['status'] == 1 ? lang('app.language_admin_downtime_inactive') : lang('app.language_admin_downtime_active');
                        $status = $admin_user_details['status'] == 1 ? 0 : 1;
                        $updateData = array('status' => $status);
                        $status_update = $this->cmsmodel->status_update($updateData,$tier_user_admin_id); 
                        if ($status_update) {
                            $this->session->setFlashdata('messages', $lang);
                            echo json_encode(array('success' => 1, 'msg' => 'updated'));
                            die;
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_list_tier_users_failure_msg_update'));
                            echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                            die;
                        }
                }
            }
        }
    }
       /* Admin common timezone list dropdown function */
        public function get_timezone_list(){
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $timezoneIdentifiers = DateTimeZone::listIdentifiers();
            $utcTime = new DateTime('now', new DateTimeZone('UTC'));
                   
            $tempTimezones = [];
            foreach ($timezoneIdentifiers as $timezoneIdentifier) {
                $currentTimezone = new DateTimeZone($timezoneIdentifier);
                
                $tempTimezones[] = [
                    'offset' => (int)$currentTimezone->getOffset($utcTime),
                    'identifier' => $timezoneIdentifier
                ];
            }
                
            /* Sort the array by offset,identifier ascending */
            usort($tempTimezones, function($a, $b) {
                return ($a['offset'] == $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
            });
                    
            $timezoneList = [];
            foreach ($tempTimezones as $tz) {
                $sign = ($tz['offset'] > 0) ? '+' : '-';
                $offset = gmdate('H:i', abs($tz['offset']));
                $timezoneList[$tz['identifier']] = '(UTC ' . $sign . $offset . ') ' .
                $tz['identifier'];
            }
    
            return $timezoneList;
        }
        /* Admin institution menu pagination function */
        function institutions() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }

            $search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
            $perPage =  10;
            $offset = 0;
            $uri = current_url(true);
            $TotalSegment_array = ($uri->getSegments());
            $admin_institutions_segment = array_search('institutions',$TotalSegment_array,true);
            $segment = $admin_institutions_segment + 2;
            $pager = "";
            $total_rows = $this->usermodel->record_institutions_count(trim($search_item));
            if($total_rows > 10){
            $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
            $this->pager->makeLinks($page+1, $perPage, $total_rows, 'default_full', $segment, 'pagination_admin_institutions');
            $offset = $page * $perPage;
            $pager = $this->pager;
            }
            $data = array(
                'admin_title' =>  lang('app.language_admin_institutions_heading'),
                'admin_heading' => lang('app.language_admin_institutions_heading'),
                'search_item' => $search_item,
                'results' => $this->usermodel->fetch_institutions($perPage, $offset, trim($search_item)),
                'pagination' => $pager,
            );
            echo view('admin/listinstitutions',$data);
        }

 
    /* Admin institution menu list function */
    function institution($institution_id = FALSE) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        if ($institution_id == FALSE) {
            $data = array(
                'admin_title' => lang('app.language_admin_add_institution'),
                'admin_heading' => lang('app.language_admin_add_institution'),
                'tiers' => $this->usermodel->get_tiers(),
                'tiers_1' => $this->usermodel->get_tiers(1),
                'tiers_2' => $this->usermodel->get_tiers(2),
                'institution_groups' => $this->placementmodel->get_institution_group(),
                'countries' => $this->bookingmodel->get_country_by_toporder(),
				'otherCountries' => $this->bookingmodel->get_country_by_othercountries(),
		        'languages' => $this->cmsmodel->get_language(), 
                'product_group'	=> $this->cmsmodel->get_product_group(),
                'institute_courseType' => $this->usermodel->get_institute_courseType($institution_id),
                'institute' => $this->usermodel->get_institute($institution_id),
            );
        } else {
            $data = array(
                'admin_title' => lang('app.language_admin_viewedit_institution'),
                'admin_heading' => lang('app.language_admin_viewedit_institution'),
                'institution_groups' => $this->placementmodel->get_institution_group(),
                'sectors'   => $this->usermodel->get_sectors($institution_id),
                'tiers' => $this->usermodel->get_tiers(),
                'tiers_1' => $this->usermodel->get_tiers(1),
                'tiers_2' => $this->usermodel->get_tiers(2),
                'institution_groups' => $this->placementmodel->get_institution_group(),
                'countries' => $this->bookingmodel->get_country_by_toporder(),
				'otherCountries' => $this->bookingmodel->get_country_by_othercountries(),
                'institute' => $this->usermodel->get_institute($institution_id),
                'tier_relations' => $this->usermodel->get_tier_relations($institution_id),
                'languages'	=> $this->cmsmodel->get_language(),
                'product_group'	=> $this->cmsmodel->get_product_group(),
                'institute_courseType' => $this->usermodel->get_institute_courseType($institution_id),
            );
            $data['regions'] = $this->usermodel->get_regions_by_countrycode($data['institute']['country'], FALSE);
        }
        $data['timezones'] = $this->get_timezone_list();
        echo view('admin/institution',$data);
    } 
    /* Admin institution menu add function */
    function postinstitute($id = FALSE) {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }

        if ($id == FALSE) {
            $data = array(
                'admin_title' => lang('app.language_admin_add_institution'),
                'admin_heading' => lang('app.language_admin_add_institution'),
            );
        } else {
            $data = array(
                'admin_title' => lang('app.language_admin_viewedit_institution'),
                'admin_heading' => lang('app.language_admin_viewedit_institution'),
            );
            $institute = $this->usermodel->get_institute($id);
        }

        if ($this->request->getPost()) {
            $tds_options = $tds_valid = array();

            $rules =[
                'tier' => [
                    'label'  => 'Tier',
                    'rules'  => 'trim|required',
                ],
                'department' => [
                    'label'  => lang('app.language_admin_institutions_department'),
                    'rules'  => 'max_length[100]|department_check',
                    'errors' => [
                        'department_check' => lang('app.language_admin_institute_department_check'),
                    ],
                ],
                'address_line1' => [
                    'label'  => lang('app.language_admin_institutions_address_line1'),
                    'rules'  => 'required|max_length[100]|orgname_check',
                    'errors' => [
                        'orgname_check' => lang('app.language_admin_institute_orgname_check'),
                    ],
                ],
                'address_line2' => [
                    'label'  => lang('app.language_admin_institutions_address_line2'),
                    'rules'  => 'max_length[100]|orgname_check',
                    'errors' => [
                        'orgname_check' => lang('app.language_admin_institute_orgname_check'),
                    ],
                ],
                'address_line3' => [
                    'label'  => lang('app.language_admin_institutions_address_line3'),
                    'rules'  =>  'max_length[100]|orgname_check',
                    'errors' => [
                        'orgname_check' => lang('app.language_admin_institute_orgname_check'),
                    ],
                ],
                'postal_and_locality' => [
                    'label'  => lang('app.language_admin_institutions_postal_and_locality'),
                    'rules'  =>  'required|max_length[50]|orgname_check',
                    'errors' => [
                        'orgname_check' => lang('app.language_admin_institute_orgname_check'),
                    ],
                ],
                'country' => [
                    'label'  => lang('app.language_admin_institutions_country'),
                    'rules'  =>   'required',
                ],
                'timezone' => [
                    'label'  => lang('app.language_admin_institutions_timezone'),
                    'rules'  =>   'required',
                ]
            ];

            if (isset($institute) && $institute['organization_name'] == $this->request->getPost('organization_name')):

            else:
                $rules['organization_name'] = [
                        'label'  => lang('app.language_admin_institutions_name'),
                        'rules'  => 'trim|max_length[100]|required|orgname_check|is_unique[institution_tiers.organization_name]',
                        'errors' => [
                            'orgname_check' => lang('app.language_admin_institute_orgname_check'),
                            'is_unique' => 'An organisation with the same name already exists',
                        ],
                    ];
            endif;

            if ($this->request->getPost('tier') == '3'):

                $rules['institution_type'] = [
                        'label'  => 'Institution type',
                        'rules'  => 'required',
                     ];
                $rules['language'] = [
                       'label'  => lang('app.language_admin_institutions_language'),
                       'rules'  => 'required',
                    ];

                if ($this->request->getPost('product_group') === NULL) {
                    $rules['product_group'] = [
                        'label'  => 'Product group',
                        'rules'  => 'required',
                     ];

                }else{ /* WP-1202 TDS option added in Product eligibility section in Add/Update institution  */
                    foreach($this->request->getPost('product_group') as  $product_group_id){
                        if($product_group_id < 4){ /* Except Benchmarking Product eligibility */
                            $tds_options[$product_group_id] = 'catstds';
                        }elseif ($product_group_id == 4){ /* Default TDS for Benchmarking Product eligibility */
                            $tds_options[$product_group_id] = 'catstds'; 
                        }
                    }
                }

            endif;

            if ($this->request->getPost('country') != '') {
                $regions_available = $this->usermodel->get_regions_by_countrycode($this->request->getPost('country'), TRUE);
                if (isset($regions_available) && $regions_available) {

                    $rules['region'] = [
                        'label'  => 'Region',
                        'rules'  => 'required',
                     ];
                }
            }
            if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = array(
                    'tier' => $this->validation->showError('tier'),
                    'external_id' => $this->validation->showError('external_id'),
                    'institution_type' => $this->validation->showError('institution_type'),
                    'organization_name' => $this->validation->showError('organization_name'),
                    'department' => $this->validation->showError('department'),
                    'address_line1' => $this->validation->showError('address_line1'),
                    'address_line2' => $this->validation->showError('address_line2'),
                    'address_line3' => $this->validation->showError('address_line3'),
                    'postal_and_locality' => $this->validation->showError('postal_and_locality'),
                    'region' => $this->validation->showError('region'),
                    'country' => $this->validation->showError('country'),
                    'timezone' => $this->validation->showError('timezone'),
                    'language' => $this->validation->showError('language'),
                    'product_group' => $this->validation->showError('product_group'),
                );
                $response['errors'] = $errors;
                echo json_encode($response);
                die;
            } else {

                $datainstitutes = array(
                    'tierId' => $this->request->getPost('tier'),
                    'external_id' => $this->request->getPost('external_id'),
                    'organisation_type' => $this->request->getPost('institution_type'),
                    'organization_name' => $this->request->getPost('organization_name'),
                    'department' => $this->request->getPost('department'),
                    'address_line1' => $this->request->getPost('address_line1'),
                    'address_line2' => $this->request->getPost('address_line2'),
                    'address_line3' => $this->request->getPost('address_line3'),
                    'postal_and_locality' => $this->request->getPost('postal_and_locality'),
                    'region' => $this->request->getPost('region'),
                    'country' => $this->request->getPost('country'),
                    'timezone' => $this->request->getPost('timezone'),
                    'access_detail_language' => $this->request->getPost('language'),
                    'product_group' => $this->request->getPost('product_group[]'),
                    'tds_options' => $tds_options,
                );
                if ($id != FALSE) {

                    /* insert sectors for minstry level users */
                    if (isset($id) && $this->request->getPost('tier') == '3') {

                        $this->db->transStart();

                        $builder = $this->db->table('institution_internal_tiers');
                        $builder->delete(['institutionTierId' => $id]);

                        /* insert relationship with tier1 and tier2 data */
                        if (null !== $this->request->getPost('tier1') && $this->request->getPost('tier1')!=''):
                            
                            $reldataTier1 = [
                                'institutionTierId' => $id, 
                                'relinstitutionTierId' => $this->request->getPost('tier1')
                            ];
                   
                            $builder = $this->db->table('institution_internal_tiers');
                            $builder->insert($reldataTier1);

                        endif;
                        
                        if (null !== $this->request->getPost('tier2') && $this->request->getPost('tier2')!=''):
                            $reldataTier2 = [
                                 'institutionTierId' => $id,
                                 'relinstitutionTierId' => $this->request->getPost('tier2')
                                ];
                            $builder = $this->db->table('institution_internal_tiers');
                            $builder->insert($reldataTier2);
                        endif;

                        $this->db->transComplete();
                    }
                    /* update institute */
                    if ($this->usermodel->update_institute($id, $datainstitutes)) {
                        $this->session->setFlashdata('messages', lang('app.language_admin_institute_updated_success_msg'));
                        $dataSuccess = array('success' => 1, 'msg' => 'institute updated');
                        echo json_encode($dataSuccess);
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_admin_institute_nothing_to_update_msg'));
                        $dataFailure = array('success' => 1, 'msg' => 'institute not updated');
                        echo json_encode($dataFailure);
                        die;
                    }
                } else {
                    /* insert institute */
                    $institutionTierId = $this->usermodel->insert_institute($datainstitutes);
                    if ($institutionTierId > 0) {
                        /* insert sectors for minstry level users */
                        if (isset($institutionTierId) && $this->request->getPost('tier') == '3') {

                            $this->db->transStart();

                            $builder = $this->db->table('institution_internal_tiers');
                            $builder->delete(['institutionTierId' => $id]);
                            /* insert relationship with tier1 and tier2 data */
                            if (null != $this->request->getPost('tier1')):

                                $reldataTier1 = [
                                    'institutionTierId' => $institutionTierId, 
                                    'relinstitutionTierId' => $this->request->getPost('tier1')
                                ];
                                $builder = $this->db->table('institution_internal_tiers');
                                $builder->insert($reldataTier1);

                            endif;
                            if (null != $this->request->getPost('tier2')):

                                $reldataTier2 = [
                                    'institutionTierId' => $institutionTierId,
                                    'relinstitutionTierId' => $this->request->getPost('tier2')
                                ];
                                $builder = $this->db->table('institution_internal_tiers');
                                $builder->insert($reldataTier2);

                            endif; 
                             /* create hidden admin user for tier3 institute - Start */
                            $tieruser_admin = [
                                'firstname' => $this->request->getPost('organization_name'),
                                'lastname' => "Admin",
                                'department' => $this->request->getPost('department'),
                                'name' => $this->request->getPost('organization_name').' '."Admin"
                            ];
                            $tieruser_id_admin = $this->placementmodel->insert_tieruser($tieruser_admin);
                            if ($tieruser_id_admin > 0) {
                                /* $access_institutes = $this->request->getPost('access_institute'); */
                                $tier_id = $institutionTierId;
                                $tier_type = $this->request->getPost('tier');
                                if($tier_type == 3) $roleid = 4;

                                if(isset($roleid) && $roleid != ''){
                                    $tieruserrole = [
                                            'roles_id' => $roleid,
                                            'users_id' => $tieruser_id_admin
                                    ];
                                    $builder = $this->db->table('user_roles');
                                    $builder->insert($tieruserrole);
                                }

                                $institute_tier_user = [
                                    'institutionTierId' => $tier_id,
                                    'user_id' => $tieruser_id_admin
                                ];
                                $builder = $this->db->table('institution_tier_users');
                                $builder->insert($institute_tier_user);

                                
                                $institute_tier_admin = [
                                    'institution_tier_id' => $tier_id,
                                    'admin_user_id' => $tieruser_id_admin
                                ];
                                $builder = $this->db->table('institution_tier_admins');
                                $builder->insert($institute_tier_admin);
                                
                                /* WP-1104 Set a default distributor DD001 for a newly create School/Tier3 user. */
                                if($tier_type == 3){
                                    $school_distributor_values = [
                                        'school_user_id' => $tieruser_id_admin,
                                        'distributor_id' => 'DD001',
                                        'setdefault' => 1
                                    ];
                                    $builder = $this->db->table('school_distributor');
                                    $builder->insert($school_distributor_values);
                                } /* WP-1104 END */

                                $random_string = $this->generate_random_string(25);
                                $activation_date = time();
                                $expiration_date = strtotime("+7 day", $activation_date);

                                /* activation details */
                                if($tieruser_id_admin != 0){
                                    $dataActivation = [
                                        'email' => "admin+".$tieruser_id_admin."@catsstep.education",
                                        'activation_code' => $random_string, 
                                        'activation_date' => $activation_date, 
                                        'expiration_date' => $expiration_date
                                    ];
                                    $builder = $this->db->table('users');
                                    $builder->where('id', $tieruser_id_admin); 
                                    $builder->update($dataActivation);
                                }
                            } 
                                /* create hidden admin user for tier3 institute - Ends */
                            $this->db->transComplete();
                        }
                        $this->session->setFlashdata('messages', lang('app.language_admin_institute_added_success_msg'));
                        $dataSuccess = array('success' => 1, 'msg' => 'institute inserted');
                        echo json_encode($dataSuccess);
                        die;
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_admin_institute_added_failure_msg'));
                        $dataFailure = array('success' => 0, 'msg' => 'institute not inserted');
                        echo json_encode($dataFailure);
                        die;
                    }
                }
            }
        } else {
            $dataFailute = array('success' => 0, 'msg' => 'No post request made!');
            echo json_encode($dataFailute);
        }
    }

    /* get regions based in country selection */
    function get_regions($country_code = FALSE, $availablity = FALSE) {
        if ($country_code != FALSE) {

            $builder = $this->db->table('regions');
            $builder->where('countryCode', $country_code);
            $query = $builder->get();

            if ($availablity != FALSE) {
                if ($query->getNumRows() > 0) {
                    return true;
                }
            }

            $html = "<option value=''>Please select</option>";
            if ($query->getNumRows() > 0) {
                $regions = $query->getResult();
                foreach ($regions as $region):
                    $html .= "<option value='" . $region->regionCode . "'>" . $region->name . "</option>";
                endforeach;
                $succesData = array('success' => 1, 'html' => $html, 'available' => 1);
                echo json_encode($succesData);
                die;
            }else {
                $succesData = array('success' => 1, 'html' => $html, 'available' => 0);
                echo json_encode($succesData);
                die;
            }
        } else {
            $emptyData = array('success' => 0, 'html' => $html, 'available' => 0);
            echo json_encode($emptyData);
            die;
        }
    }

    /* Admin tire_user menu list function */
    public function list_tierusers($list_tierusers_id = FALSE){
    
        $search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
		if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login','refresh'));
        }
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login','refresh'));
        }
        /* WP-1385 starts */
        if($this->request->getPost('status') == 'active'){
            $this->session->set('tier_list', '0');
            $datares= array("url" => site_url("admin/list_tierusers/".$list_tierusers_id));
            echo json_encode($datares);
            exit;
        }elseif($this->request->getPost('status') == 'inactive'){
            $this->session->set('tier_list', '1');
            $datares= array("url" => site_url("admin/list_tierusers/".$list_tierusers_id));
            echo json_encode($datares);
            exit;
        }
        if($this->session->get('tier_list') == 1 && $search_item == ""){
            $valid = "";
            $condition = $this->session->get('tier_list');
        }elseif($search_item != ""){
            $valid = "";
            $condition = 0;
        }else{
            $valid = 'checked';
            $condition = 0;
        }

        $perPage =  10;
        $offset = 0;
        $pager = "";
        $total_rows = $this->usermodel->tier_userlist_count($list_tierusers_id, trim($search_item)); 
        if($total_rows > 10){
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $offset = $page * $perPage;
        $this->pager->makeLinks($page+1, $perPage, $total_rows);
        $pager = $this->pager;
        }
   
		$data = array(
            'admin_title' =>  lang('app.language_admin_tierusers_heading'),
            'admin_heading' => lang('app.language_admin_tierusers_heading'),
            'checked' => $valid,
            'search_item' => $search_item,
			'tier_id' => $list_tierusers_id,  
			'results' => $this->usermodel->fetch_tier_userlist($list_tierusers_id, $condition, trim($search_item), $perPage, $offset ),
            'pagination' => $pager,
        );;

        /* unset($_SESSION['tier_list']); */
        echo view('admin/list_tierusers',$data);
	}
        /* Admin institution get function */
        function institution_types($institution_id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if($institution_id == FALSE){
                    $data = array(
                    'admin_title' => lang('app.language_admin_institution_type'),
                    'admin_heading' => lang('app.language_admin_institution_types'),
                    'results' => $this->cmsmodel->institution_types(),
                );
            }else{
                $data = array(
                    'admin_title' => lang('app.language_admin_institution_type'),
                    'admin_heading' => lang('app.language_admin_institution_types'),
                    'results' => $this->cmsmodel->institution_types(),
                    'institution_value' => $this->cmsmodel->institution_types($institution_id),
                );
            }
            echo view('admin/institution_types',$data);
        }

        /* Admin institution_type delete function */
        function deleteInstitutionType($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id != FALSE) {
                $institution_tiers_detail = $this->cmsmodel->check_institution_tiers(false, $id);
                
                if(empty($institution_tiers_detail)){
                    if ($this->cmsmodel->delete_insitute_type($id)) {
                        $this->session->setFlashdata('messages', lang('app.language_admin_institution_type_removed'));
                        return redirect()->to(site_url('admin/institution_types'));
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_admin_institution_type_deleted_failure_msg'));
                        return redirect()->to(site_url('admin/institution_types'));
                    }
                }else{
                    $this->session->setFlashdata('errors', lang('app.language_admin_institution_type_associate_with_tier'));
                    return redirect()->to(site_url('admin/institution_types'));
                }    
            } else {
                $this->session->setFlashdata('errors', lang('app.language_admin_institution_type_deleted_failure_msg'));
                return redirect()->to(site_url('admin/institution_types'));
            }
        }
        /* Admin tire_user get function */
        public function tieruser(){
            $tieruser_id = isset($_GET['tieruserid'])?$_GET['tieruserid']:'';
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $tieruser_detail = $this->placementmodel->get_tieruser_detail_by_id($tieruser_id);
            if ($tieruser_id == '') {
                $data = array(
                    'admin_heading' => lang('app.language_admin_add_tieruser'),
                    'institution_groups' => $this->placementmodel->get_institution_group(),
                    'tier_id' => $_GET['tierid'],
                    'tieruser' => $tieruser_detail,
                    'tier_type' => $this->placementmodel->get_tier_type_by_tierid($_GET['tierid'])
                );
            } else {
              
                $data = array(
                    'admin_heading' => lang('app.language_admin_edit_tieruser'),
                    'institution_groups' => $this->placementmodel->get_institution_group(),
                    'tier_id' => $_GET['tierid'],
                    'tier_type' => $this->placementmodel->get_tier_type_by_tierid($_GET['tierid']),
                    'tieruser' => $tieruser_detail,
                    'institutionGroupId' => $this->placementmodel->get_institution_group_by_tieruser($tieruser_id)
                );
            }

            echo view('admin/tieruser',$data);
        }
        /* Admin institute _admin_lang get function */
        public function institute_admin_lang_status() {
            if (null !== $this->request->getPost() && $this->request->getPost('tier_user_admin_id')) {
                $tier_user_admin_id = $this->request->getPost('tier_user_admin_id');
                if(!empty($tier_user_admin_id)){
                    $admin_user_details = $this->tdsmodel->get_institute_admin_user($tier_user_admin_id);          
                    if($admin_user_details){
                        $status = $admin_user_details['status'];
                        echo json_encode(array('success' => 1, 'status' => $status, 'msg' => 'fetched'));
                        die;
                    }else{
                        $this->session->set_flashdata('errors', lang('language_admin_list_tier_users_failure_msg'));
                        echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                        die; 
                    }
                }else{
                    $this->session->set_flashdata('errors', lang('language_admin_list_tier_users_failure_msg'));
                    echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                    die;    
                }
            }
        }
        /* Admin institute _admin_lang active and inactive function */
        public function institute_admin_condition_change() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if (null !== $this->request->getPost() && $this->request->getPost('tier_user_admin_id')) {
                $tier_user_admin_id = $this->request->getPost('tier_user_admin_id');
                if(!empty($tier_user_admin_id)){
                    $admin_user_details = $this->tdsmodel->get_institute_admin_user($tier_user_admin_id);          
                    if($admin_user_details){
                            $lang = $admin_user_details['status'] == 1 ? lang('app.language_admin_list_tier_users_inactive') : lang('app.language_admin_list_tier_users_active');
                            $status = $admin_user_details['status'] == 1 ? 0 : 1;
                              //WP-1380 Start
                            if($status == 0 && $this->yellowfin_access == 1){
                                @yellowfin($tier_user_admin_id);
                            }
                               /* WP-1380 end */
                               /* zendesk */ 
                               if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                                if($status == 1){
                                    @zendesk_user_create($this->zendesk_domain_url,$tier_user_admin_id,"Create","Active");   
                                }else{
                                    @zendesk_user_delete($this->zendesk_domain_url,$tier_user_admin_id,"Delete","Inactive");
                                }
                            }
                            $updateData = [
                                'status' => $status
                             ];
                            $update_tieruser_id = $this->placementmodel->update_admin_user_details($tier_user_admin_id,$updateData);
                            if ($update_tieruser_id) {
                                $updateUserData = [
                                    'is_active' => $status
                                ];
             
                                $update_tieruser_id = $this->placementmodel->update_update_tieruser_id($tier_user_admin_id,$updateUserData);
                                $this->session->setFlashdata('messages', $lang);
                                echo json_encode(array('success' => 1, 'msg' => 'updated'));
                                die;
                            } else {
                                $this->session->setFlashdata('errors', lang('app.language_admin_list_tier_users_failure_msg_update'));
                                echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                                die;
                            }
                    }
                }
            }
        }


    /* Admin  post_tireuser add & edit function */
    public function post_tieruser($post_tieruser_id = FALSE){

            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            header('Content-Type: application/json');
            if (null !== $this->request->getPost()) {
                $tier_type = $this->request->getPost('tier_type');
                if($post_tieruser_id != FALSE){
                $tier_user_detail = $this->placementmodel->get_tieruser_detail_by_id($post_tieruser_id);
               }

               $rules =[
                'first_name' => [
                    'label' => lang('app.language_admin_tieruser_firstname'),
                    'rules'  => 'max_length[64]|required|name_check',
                    'errors' => [
                        'name_check' => lang('app.language_admin_institute_name_check'),
                    ]
                ],
                'second_name' => [
                    'label' => lang('app.language_admin_tieruser_second_name'),
                    'rules'  => 'max_length[64]|required|name_check',
                    'errors' => [
                        'name_check' => lang('app.language_admin_institute_name_check'),
                    ]
                ],
                'department' => [
                    'label'  => lang('app.language_admin_tieruser_department'),
                    'rules'  => 'required',
                ],
               ];

               if (isset($tier_user_detail) && $tier_user_detail->email == trim($this->request->getPost('email'))){

                }else{

                    $rules['email'] = [
                            'label'  => lang('app.language_admin_tieruser_email'),
                                'rules'  => 'required|max_length[254]|isemail_check|is_unique[users.email]',
                                'errors' => [
                                    'isemail_check' => lang('app.form_validation_valid_email'),
                                ]
                            ];
                    $rules['confirm_email'] = [
                                'label'  => lang('app.language_admin_tieruser_confirm_email'),
                                'rules'  => 'required|max_length[254]|matches[email]|isemail_check',
                                'errors' => [
                                    'matches' => 'The email and its confirm email are not the same',
                                    'isemail_check' => lang('app.form_validation_valid_email'),
                                ]
                            ];

                }
                if($tier_type != 3){
                    $rules['access_institute'] = [
                        'label'  => lang('app.language_admin_tieruser_access_institute'),
                        'rules'  => 'required',
                    ];
                }
                if (!$this->validate($rules)) {
                    $response['success'] = 0;

                    $errors = array(
                        'first_name' => $this->validation->showError('first_name'),
                        'second_name' => $this->validation->showError('second_name'),
                        'department' => $this->validation->showError('department'),
                        'email' => $this->validation->showError('email'),
                        'confirm_email' => $this->validation->showError('confirm_email'),
                        'access_institute' => $this->validation->showError('access_institute'),
                    );
                    $response['errors'] = $errors;
                    echo json_encode($response);
                    die;
                }else{

                    $tieruser = array(
                        'firstname' => $this->request->getPost('first_name'),
                        'lastname' => $this->request->getPost('second_name'),
                        'department' => $this->request->getPost('department'),
                        'email' => trim($this->request->getPost('email')),
                        'name' => $this->request->getPost('first_name').' '.$this->request->getPost('second_name')
                    );
                    
                    if($post_tieruser_id != FALSE){
    
                        $tier_id = $this->request->getPost('tier_id');
                        
                        /* Update tier user details */
                        $update_tieruser_id = $this->placementmodel->update_tieruser($tieruser,$post_tieruser_id);
                        if(isset($this->zendesk_access) && $this->zendesk_access == 1 && $tier_user_detail->is_active == 1){
                            @zendesk_user_update($this->zendesk_domain_url,$post_tieruser_id,"Update","Edit");
                        }
                        
                        if($tier_type != 3){
                            
                        /* Delete the institute group id which already exits */
                        $delete_tieruser = $this->placementmodel->delete_tieruser($post_tieruser_id);
                        
                        /* Insert institute group ids */
                            $access_institutes = $this->request->getPost('access_institute');
                            foreach($access_institutes as $institute):
                                $institute_user_type =  array(
                                    'institutionTierId' => $tier_id,
                                    'institutionGroupId' => $institute,
                                    'user_id' => $post_tieruser_id
                                );

                                $builder = $this->db->table('institution_user_types');
                                $builder->insert($institute_user_type);
                            endforeach;
                        }
                        if ($post_tieruser_id != '' && trim($this->request->getPost('email')) != trim($tier_user_detail->email)){
                            /* WP-1395 starts */
                            if($tier_user_detail->is_active == 1){
                                $random_string = $this->generate_random_string(25);
                                $activation_date = time();
                                $expiration_date = strtotime("+7 day", $activation_date);
                                
                                /* activation details */
                                $dataActivation = array('activation_code' => $random_string, 'activation_date' => $activation_date, 'expiration_date' => $expiration_date);
                                $builder = $this->db->table('users');
                                $builder->where(array('id' => $post_tieruser_id));
                                $builder->update($dataActivation);
    
                                /* send mail to teacher upon successful registration */
                                $dataMails = array('name' => $tieruser['firstname'] . ' ' . $tieruser['lastname'], 'mailto' => $tieruser['email'], 'link' => site_url('login/password_setup') . '/' . $random_string);
                                
                                @$this->send_mail_to_institute($dataMails);
                            } /* WP-1395 starts - end */
                            $this->session->setFlashdata('messages', lang('app.language_admin_tier_user_updated_success_msg'));
                            $response = array('success' => 1, 'msg' => 'Tier User updated');
                            echo json_encode($response);
                            die;
                            
                        }
                        
                        $this->session->setFlashdata('messages', lang('app.language_admin_tier_user_updated_success_msg'));
                        $response = array('success' => 1, 'msg' => 'Tier User updated');
                        echo json_encode($response);
                        die;
                        
                    }else{
                        
                        $tieruser_id = $this->placementmodel->insert_tieruser($tieruser);
                        if ($tieruser_id > 0) {
                            
                            $access_institutes =  $this->request->getPost('access_institute');
                            $tier_id =  $this->request->getPost('tier_id');
                            $tier_type =  $this->request->getPost('tier_type');
                            
                            if($tier_type == 1) $roleid = 8;
                            if($tier_type == 2) $roleid = 9;
                            if($tier_type == 3) $roleid = 4;
                            
                            if(isset($roleid) && $roleid != ''){
                                $tieruserrole = array(
                                    'roles_id' => $roleid,
                                    'users_id' => $tieruser_id
                                );
                                $tieruserrole = $this->placementmodel->tieruserrole($tieruserrole);
                            }
                            
                            $institute_tier_user = array(
                                'institutionTierId' => $tier_id,
                                'user_id' => $tieruser_id
                            );
                            
                            $institute_tier_user = $this->placementmodel->institute_tier_user($institute_tier_user);
                            
                            /*  WP-1104 Set a default distributor DD001 for a newly create School/Tier3 user. */
                            if($tier_type == 3){
                                $school_distributor_values = array(
                                    'school_user_id' => $tieruser_id,
                                    'distributor_id' => 'DD001',
                                    'setdefault' => 1
                                );
                                $school_distributor_values = $this->placementmodel->school_distributor_values($school_distributor_values);
                            } /* WP-1104 END */
                            
                            $random_string = $this->generate_random_string(25);
                            $activation_date = time();
                            $expiration_date = strtotime("+7 day", $activation_date);
                            
                            if($tier_type != 3){
                                foreach($access_institutes as $institute):
                                    $institute_user_type =  array(
                                        'institutionTierId' => $tier_id,
                                        'institutionGroupId' => $institute,
                                        'user_id' => $tieruser_id
                                    );
                                    $institute_user_type = $this->placementmodel->institute_user_type($institute_user_type);
                                endforeach;
                            }
                            
                                /* activation details */
                                $dataActivation = [
                                    'activation_code' => $random_string, 
                                    'activation_date' => $activation_date,
                                    'expiration_date' => $expiration_date
                                ];
                            $dataActivation = $this->placementmodel->dataActivation($dataActivation, $tieruser_id);

                            /* send mail to teacher upon successful registration */
                            $dataMails = array('name' => $tieruser['firstname'] . ' ' . $tieruser['lastname'], 'mailto' => $tieruser['email'], 'link' => site_url('login/password_setup') . '/' . $random_string);
                               
                            if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                                @zendesk_user_create($this->zendesk_domain_url,$tieruser_id,"Create","Add");
                            }
                            
                            @$this->send_mail_to_institute($dataMails);
                            $this->session->setFlashdata('messages', lang('app.language_admin_tier_user_added_success_msg'));

                            $response = array('success' => 1, 'msg' => 'Tier User inserted');
                            echo json_encode($response);
                            
                            die;
                        }
                    }
                }
                
            }
        }

        /* Admin Institute email sending function */
        function send_mail_to_institute($emailDetails = FALSE) {
            if($emailDetails != FALSE):
                 $config =  @get_email_config_provider(1);
             
                 $lang_code = $this->request->getLocale();
                 $template_email = $this->emailtemplatemodel->get_template_contents('institute-mail-activation',$lang_code);
                 $template_email_new = $this->email_lib('institute-mail-activation');
                 $label = array("##NAME##", "##LINK##");
                 $email_values = array($emailDetails['name'], $emailDetails['link']);
                 $replaced_content = str_replace($label, $email_values, $template_email_new);
                 $mail_message = $replaced_content;

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
                }
                else{ 
                 $this->email->initialize($config);
                 $this->email->setFrom($template_email['0']->from_email, $template_email['0']->display_name);
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
                /* log tables affected from here */
                $mail_log = array(
                    'from_address' => $template_email['0']->from_email,
                    'to_address' => $emailDetails['mailto'],
                    'response' => $sent_mail_log,
                    'status' => $sent_mail_status ? 1 : 0,
                    'purpose' => $template_email['0']->subject
                );
                $builder = $this->db->table('email_log'); 
                $builder->insert($mail_log);
             endif;  
        }
        /* Admin common email content get function */
        public function email_lib($slug = NULL){
            $parser = \Config\Services::renderer();
    
            if(!empty($slug)){
                
                $getEmailContent = $this->usermodel->get_email_contect($slug);
                $this->data['email_content'] = $getEmailContent[0]->content;
                $test_data = $parser->setData($this->data)->renderString($this->data['email_content']);
                return $test_data;
            }
        }

            
       /* Admin common random string generate function */    
        function generate_random_string($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        /* Admin region menu get function */
        function regions($region_id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if($region_id == FALSE){
                $data = array(
                    'admin_title' => lang('app.language_admin_region'),
                    'admin_heading' => lang('app.language_admin_region'),
                    'countries' => $this->bookingmodel->get_country_by_toporder(),
                    'otherCountries' => $this->bookingmodel->get_country_by_othercountries(),
                    'regionlists' => $this->bookingmodel->get_regions()
                );
            }else{
                $data = array(
                    'admin_title' => lang('app.language_admin_region'),
                    'admin_heading' => lang('app.language_admin_region'),
                    'countries' => $this->bookingmodel->get_country_by_toporder(),
                    'otherCountries' => $this->bookingmodel->get_country_by_othercountries(),
                    'region' => $this->cmsmodel->get_regions($region_id),
                    'regionlists' => $this->cmsmodel->get_regions()
                );
            }
            echo view('admin/regions',$data);    
        }

        /* Admin region menu delete function  */
        function deleteregion($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id != FALSE) {
                $regiondetail = $this->cmsmodel->get_regions($id);

              
                if(!empty($regiondetail)){
                    $regioncode = current($regiondetail)->regionCode;	         
                }
                $institution_tiers_detail = $this->cmsmodel->check_institution_tiers($regioncode, false);
          
                if(empty($institution_tiers_detail)){
                    if ($this->cmsmodel->delete_region($id)) {
                        $this->session->setFlashdata('messages', lang('app.language_admin_region_delete_success_msg'));
                        return redirect()->to(site_url('admin/regions')); 
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_admin_region_delete_failure_msg'));
                        return redirect()->to(site_url('admin/regions')); 
                    }
                }else{
                    $this->session->setFlashdata('errors', lang('app.language_admin_region_associate_with_tier'));
                    return redirect()->to(site_url('admin/regions')); 
                }	        
            } else {
                $this->session->setFlashdata('errors', lang('app.language_admin_region_delete_failure_msg'));
                return redirect()->to(site_url('admin/regions')); 
            }
        }

        /* Admin region menu add function */
        function postregion($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
        
            if (null !== $this->request->getPost()) {

                $rules = [
                    'region_name' => [
                        'label' => lang('app.language_admin_region_field'),
                        'rules'  => 'required|regex_match[^[a-zA-Z -]+$]',
                        'errors' => [
                            'regex_match' => lang('app.language_admin_region_name_check'),
                        ],
                    ]
                ];

                if ($id == FALSE) {

                    $rules = [
                        'country_code' => [
                            'label' => lang('app.language_admin_region_country_field'),
                            'rules'  => 'required',
                        ],
                        'region_name' => [
                            'label' => lang('app.language_admin_region_field'),
                            'rules'  => 'required|regex_match[^[a-zA-Z -]+$]',
                            'errors' => [
                                'regex_match' => lang('app.language_admin_region_name_check'),
                            ],
                        ],
                    ];

                }	        
                if ($this->validate($rules) == FALSE) {
                    $this->session->setFlashdata('errors', 'The Country field is required Only letters, spaces and hyphens are allowed.');
                    if ($id == FALSE) {
                        return redirect()->to(site_url('admin/regions'));
                    }else{
                        return redirect()->to(site_url('admin/regions/'.$id));
                    }
                } else {

                    if ($id != FALSE) {
                       if ($this->cmsmodel->update_region_name($id)) {
                           $this->session->setFlashdata('messages', lang('app.language_admin_region_update_success_msg'));
                       }
                       return redirect()->to(site_url('admin/regions')); 
                   } else {
                       if ($this->cmsmodel->insert_region() != '') {
                            $this->session->setFlashdata('messages', lang('app.language_admin_region_insert_success_msg'));
                            return redirect()->to(site_url('admin/regions')); 
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_region_insert_error_msg'));
                            return redirect()->to(site_url('admin/regions')); 
                        }
                    }
                 } 
                }
                else{
                    return redirect()->to(site_url('admin/regions')); 
                }
        }

        /* Admin email_category menu get function */
        function mailcategory($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id == FALSE) {
                $data = array(
                    'admin_title' => lang('app.language_admin_add_mail_category'),
                    'admin_heading' => lang('app.language_admin_add_mail_category'),
                    'languages' => $this->emailtemplatemodel->get_language(),
                    'categorynames' => $this->emailtemplatemodel->get_mailcategory()
                );
            } else {
                $data = array(
                    'admin_title' => lang('app.language_admin_update_mail_category'),
                    'admin_heading' => lang('app.language_admin_update_mail_category'),
                    'languages' => $this->emailtemplatemodel->get_language(),
                    'categorydatas' => $this->emailtemplatemodel->get_mailcategory($id),
                    'categorynames' => $this->emailtemplatemodel->get_mailcategory()
                );
            }
            echo view('admin/emailcategory',$data);
        }
        /* Admin email_category menu add & edit function */
        function postmailcategory($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if (null !== $this->request->getPost()) {
    
                $categorycheck  = $this->emailtemplatemodel->postmailcategorycheck(slugify($this->request->getPost('category_name')));
                if ($categorycheck > 0) {
                    $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_category_slug_exists_failure_msg'));
                    return redirect()->to(site_url('admin/mailcategory')); 
                }
    
                $rules =[
                    'category_name' => [
                      'label'  => lang('app.language_admin_page_category'),
                      'rules'  => 'required',
                      ],
                      'category_description' => [
                        'label'  => lang('app.language_admin_label_mail_category_description'),
                        'rules'  => 'required',
                        ],
              ];

              if (!$this->validate($rules)) {
                if ($this->validation->getError('category_name') != NULL) {
                    $error = 'The category Name field is reduired';
                 }
                 if ($this->validation->getError('category_description') != NULL) {
                     $error = 'The Category Description field is reduired';
                 }
                    $this->session->setFlashdata('errors', $error);
                    return redirect()->to(site_url('admin/mailcategory')); 
                } else {
                    if ($id != FALSE) {
                        if ($this->emailtemplatemodel->update_mail_category($id)) {
                            $this->session->setFlashdata('messages', lang('app.language_admin_mail_template_category_updated_success_msg'));
                            return redirect()->to('admin/mailcategory/' . $id); 
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_category_updated_failure_msg'));
                            return redirect()->to('admin/mailcategory/' . $id);
                        }
                    } else {
                        if ($this->emailtemplatemodel->insert_mail_category() != '') {
                            $this->session->setFlashdata('messages', lang('app.language_admin_mail_template_category_added_success_msg'));
                            return redirect()->to(site_url('admin/mailcategory')); 
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_category_added_failure_msg'));
                            return redirect()->to(site_url('admin/mailcategory')); 
                        }
                    }
                }
            } else {
                $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_category_added_failure_msg'));
                return redirect()->to(site_url('admin/mailcategory')); 
            }
        }
        /* Admin email_templete menu get function */
        function template($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id == FALSE) {
                $data = array(
                    'admin_title' => lang('app.language_admin_add_mail_template'),
                    'admin_heading' => lang('app.language_admin_add_mail_template'),
                    'languages' => $this->emailtemplatemodel->get_language(),
                    'templatenames' => $this->emailtemplatemodel->get_mailcategory()
                );
            } else {
    
                $data = array(
                    'admin_title' => lang('app.language_admin_update_mail_template'),
                    'admin_heading' => lang('app.language_admin_update_mail_template'),
                    'languages' => $this->emailtemplatemodel->get_language(),
                    'templatedatas' => $this->emailtemplatemodel->get_template($id),
                    'templatenames' => $this->emailtemplatemodel->get_mailcategory()
                );
            }
            echo view('admin/emailtemplate',$data);
        }
        /* Admin email_templete menu add function */
        function posttemplate($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if (null !== $this->request->getPost()) {
    
                $rules =[
                    'subject' => [
                      'label'  => lang('app.language_admin_label_mail_subject'),
                      'rules'  => 'required|max_length[1000]',
                      ],
                      'content' => [
                        'label'  => lang('app.language_admin_label_mail_content'),
                        'rules'  => 'required|max_length[10000]',
                        ],
                    ];

                if (!$this->validate($rules)) {

                    if ($this->validation->getError('subject') != NULL) {
                       $error = $this->validation->getError('subject');
                    }
                    if ($this->validation->getError('content') != NULL) {
                        $error = $this->validation->getError('content');
                    }
                    $this->session->setFlashdata('errors', $error);
                    if (!empty($id)) {
                        return redirect()->to('admin/template/' . $id);
                    } else {
                        return redirect()->to(site_url('admin/template')); 
                    }
                } else {
    
                    if ($id != FALSE) {
                        if ($this->emailtemplatemodel->update_email_templates($id) === TRUE) {
                            $this->session->setFlashdata('messages', lang('app.language_admin_mail_template_updated_success_msg'));
                            return redirect()->to(site_url('admin/listemailtemplates')); 
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_updated_failure_msg'));
                            return redirect()->to('admin/template/' . $id);
                        }
                    } else {
                        if ($this->emailtemplatemodel->check_exists_template($this->request->getPost('category_id'), $this->request->getPost('language_code'))) {
    
                            $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_translation_available_msg'));
                            return redirect()->to(site_url('admin/template')); 
                        }
                        if ($this->emailtemplatemodel->insert_email_templates() != '') {
                            $this->session->setFlashdata('messages', lang('app.language_admin_mail_template_added_success_msg'));
                            return redirect()->to(site_url('admin/listemailtemplates')); 
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_added_failure_msg'));
                            return redirect()->to(site_url('admin/template')); 
                        }
                    }
                }
            } else {
                return redirect()->to(site_url('admin/template')); 
            }
        }
        /* Admin email_templete menu delete function */
        function deletemailtemplate($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($id != FALSE) {
                if ($this->emailtemplatemodel->delete_email_templates($id)) {
                    $this->session->setFlashdata('messages', lang('app.language_admin_mail_template_deleted_success_msg'));
                    return redirect()->to(site_url('admin/listemailtemplates')); 
                } else {
                    $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_deleted_failure_msg'));
                    return redirect()->to(site_url('admin/listemailtemplates')); 
                }
            } else {
                $this->session->setFlashdata('errors', lang('app.language_admin_mail_template_deleted_failure_msg'));
                return redirect()->to(site_url('admin/listemailtemplates')); 
            }
        }
        /* Admin list_email_templete menu list function */
        function listemailtemplates() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }

        /* pagination */
        $perPage =  10;
        $offset = 0;
        $pager = "";
        $total_rows = $this->emailtemplatemodel->record_count();
        if($total_rows > 10){
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $offset = $page * $perPage;
        $this->pager->makeLinks($page+1, $perPage, $total_rows);
        $pager = $this->pager;
        }

            $data = array(
            'admin_title' => lang('app.language_admin_list_templates'),
            'admin_heading' => lang('app.language_admin_list_templates'),
            'results' =>  $this->emailtemplatemodel->fetch_template($perPage,$offset),
            'pager' => $pager
            );
            echo view('admin/listemailtemplates',$data);
        }
        /* Admin email_log menu count function */
        public function get_mail_count() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }

            $search_item = (isset($_GET['strd']) && $_GET['strd'] != '') ? $_GET['strd'] : '';
            $search_item1 = (isset($_GET['endd']) && $_GET['endd'] != '') ? $_GET['endd'] : '';
            $newDate1 = date("Y-m-d", strtotime($search_item));
            $newDate2 = date("Y-m-d", strtotime($search_item1));

            if ($newDate1 > $newDate2) {
                $this->session->setFlashdata('date_check','End date should be greater than Start date'); 
                 return redirect()->to(site_url('admin/get_mail_count'));
               }
    
         /* pagination */
         $perPage =  10;
         $offset = 0;
         $pager = "";
         $total_rows = $this->tdsmodel->get_date_search_mailcount($newDate1,$newDate2);
         if($total_rows > 10){
         $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
         $offset = $page * $perPage;
         $this->pager->makeLinks($page+1, $perPage, $total_rows);
         $pager = $this->pager;
         }

            $data = array(
                'admin_title' => lang('app.language_admin_email_logs'),
                'admin_heading' => lang('app.language_admin_email_logs'),
                'success' => $this->tdsmodel->sent_count(),
                'results' => $this->tdsmodel->get_datewise_mailcount($perPage, $offset, $newDate1,$newDate2),
                'mail_startdate' => $search_item,
                'mail_enddate' => $search_item1,
                'pager' => $pager
            );
    
            echo view('admin/mail_count',$data);
        }
    
        /* Admin email_log menu export_mail_excel function */
        public function export_mail_excel($strd, $endd) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $newDate1 = date("Y-m-d", strtotime($strd));
            $newDate2 = date("Y-m-d", strtotime($endd));
            if (!empty($newDate1)) {
                $tokens = $this->tdsmodel->export_data_download($newDate1,$newDate2);
                $delimiter = ",";
                $newline = "\r\n";
                if (count($tokens) > 0) {
                    $heading_array = array('id' => 'ID','from_address' => 'From', 'to_address' => 'To', 'datetime' => 'date','purpose' => 'Purpose','status' => 'Status','response' => 'Response' );
                    $results_array = $this->tdsmodel->export_data_download($newDate1,$newDate2);
                    array_unshift($results_array, $heading_array);
                    echo array_to_csv($results_array, 'Mail List for Log.csv');
                }
            } else {
                show_404();
            }
           
        }
        /* Admin testform_details menu edit function */
        public function testform_details_edit($formDetailId = FALSE){
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if ($formDetailId != FALSE) {
                $data = array(                
                    'admin_heading' => lang('app.language_admin_formdetail_add'),
                    'test_purposes' => $this->cmsmodel->get_tds_test_types(),
                    'test_data' => $this->cmsmodel->get_tds_test_detail_by_id($formDetailId),
                    'products' => $this->cmsmodel->get_tds_products()                
                );
            }
            echo view('admin/testform_details_edit',$data);
        }
        /* Admin testform_details menu pagination function */
        public function testform_details(){
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }


        /* pagination */
        $perPage =  10;
        $offset = 0;
        $pager = "";
        $total_rows = $this->cmsmodel->get_tds_test_details_count(); 
        if($total_rows > 10){
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $offset = $page * $perPage;
        $this->pager->makeLinks($page+1, $perPage, $total_rows);
        $pager = $this->pager;
        }

            $data = array(
                'admin_title' => lang('app.language_admin_testform_details'),
                'admin_heading' => lang('app.language_admin_testform_details'),
                'products' => $this->productmodel->fetch_product_for_version_allocation(),
                'testFormDetails' => $this->cmsmodel->get_tds_test_details($perPage, $offset),
                'pager' => $pager
            );
            echo view('admin/testform_details',$data);
    
        }
        /* Admin testform_details menu get function */
        public function testform_detail($formDetailId = FALSE){
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $this->tds_test_forms_scheduler();

            if ($formDetailId == FALSE) {
                $data = array(
                    'admin_heading' => lang('app.language_admin_formdetail_add'),
                    'test_purposes' => $this->cmsmodel->get_tds_test_types(),
                    'products' => $this->cmsmodel->get_tds_products(),
                );
            } else {
                $data = array(
                    'admin_heading' => lang('app.language_admin_formdetail_edit'),
                    'test_purposes' => $this->cmsmodel->get_tds_test_types(),
                    'test_data' => $this->cmsmodel->get_tds_test_detail_by_id($formDetailId),
                    'products' => $this->cmsmodel->get_tds_products(),
                );
            }
            echo view('admin/testform_detail',$data);
        }



    /* Scheduler job for fetch tds test forms */
    public function tds_test_forms_scheduler()
    {
        /* Api call for get test form details from tds side */
        $ch = curl_init();
        $url =  $this->oauth->catsurl('testDeliveryUrl').'TestForms'.'/'.'GetForms';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        $result_object = json_decode($output);

        if(!empty($result_object)) {
            foreach($result_object as $result) {
                $tes_test_array =  [
                                        'test_formid' => $result->TestFormId,
                                        'test_formversion' => $result->TestFormVersion,
                                        'tds_test_group_id' => $result->GroupID,
                                        'test_purpose' => $result->GroupName,
                                        'test_product_id' => $result->ProductID,
                                        'tds_test_reference' => $result->TestReference,
                                        'tds_test_type' => $result->TestType,
                                    ];

                $where_array =  [
                                    'test_formid' => $result->TestFormId,
                                    'test_formversion' => $result->TestFormVersion
                                ];

                $builder = $this->db->table('tds_test_forms');
                $builder->select('*');
                $builder->where($where_array);
                $check_form_id_exist = $builder->get()->getResult();
                
                if(!empty($check_form_id_exist)) {

                    $builder = $this->db->table('tds_test_forms');
                    $builder->where($where_array);
                    $builder->update($tes_test_array);

                } else {

                    $builder = $this->db->table('tds_test_forms');
                    $builder->insert($tes_test_array);

                }
            }
        } else {
            error_log(date('[Y-m-d H:i:s e] ') . "Failure: The form fetching from TDS server failed " . PHP_EOL, 3, LOG_FILE_TDS);
        }
    }
    /* Admin data sending to datawarehours function */
    public function send_testtype_to_dwh(){
		
        $builder = $this->db->table('testtypes');
        $builder->select('id,testGroupId,productId,title');
        $builder->where('syncStatus',0);
        $query =  $builder->get();
        $testtype_query_result = $query->getResult();

		$testtype_data = array(
			"token" => $this->oauth->catsurl('dwh_ws_token'),
			"testtypes" => $testtype_query_result
		);		
		
		$dataNeeded = json_encode($testtype_data);
		$response = $this->http_ws_call_testtype($dataNeeded);
		$parseData = json_decode($response);
		/* get_object_vars */
		$objArray = get_object_vars($parseData);
		
		if (isset($parseData) && is_array($objArray)) {

			if (isset($objArray['result']->code) && $objArray['result']->code == '0'){
                $data  = array(
                    "syncStatus" => '1'
                );		
                $builder = $this->db->table('testtypes');
                $builder->update($data, ['syncStatus' => 0]);
			}
		}
	}

    /* Push testtypes to DWH */
	function http_ws_call_testtype($data = FALSE) {
        if ($data != FALSE):
            $serverurl = $this->oauth->catsurl('dwh_ws_url_testtype');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $serverurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . urlencode($data) . '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            return $server_output;
        endif;
    }
        /* Admin test_version_allocation menu product get function */
        public function cats_test_allocation($product_id = FALSE){
	        if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if($product_id != FALSE){
                $data = array(
                    'admin_title' => lang('app.language_admin_test_version_allocation_cats'),
                    'admin_heading' => lang('app.language_admin_test_version_allocation_cats'),
                    'products' => $this->productmodel->fetch_product_for_version_allocation(),
                    'product_id' => $product_id,
                );
            }else{
                $data = array(
                    'admin_title' => lang('app.language_admin_test_version_allocation_cats'),
                    'admin_heading' => lang('app.language_admin_test_version_allocation_cats'),
                    'products' => $this->productmodel->fetch_product_for_version_allocation(),
                );
            }
            echo view('admin/test_allocation_cats',$data);
        }
        /* Admin view_learner_progrees menu product list function */
        function view_learner_progress()
        {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $institutes = $this->schoolmodel->school_lists();
            $institute_id = "";
            if(($this->request->getGet('institute'))){
                $institute_id = $this->request->getGet('institute');
            }         
             if(!empty($institute_id)){
                      /* pagination */
                      $perPage =  10;
                      $offset = 0;
                      $uri = current_url(true);
                      $TotalSegment_array = ($uri->getSegments());
                      $dashboard_segment = array_search('view_learner_progress',$TotalSegment_array,true);
                      $segment = $dashboard_segment + 2;
                      $pager = "";
                      $total = count((array)$this->schoolmodel->learner_progress($institute_id));
                      if($total > 10){
                      $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
                      $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_view_learner_progress');
                      $offset = $page * $perPage;
                      $pager = $this->pager;
                      }
    
                $token_data = array(
                    'results' => $this->schoolmodel->learner_progress($institute_id, $perPage, $offset),
                    'pager' => $pager
                );
                if($token_data['results']){
                foreach ($token_data['results'] as $tokens) {
                    if ($tokens->thirdparty_id > 0) {
                        $tokens->timezone = $this->bookingmodel->get_institution_timezone($tokens->thirdparty_id);
     
                        $results  = $this->bookingmodel->tokens_thirdparty_id($tokens->thirdparty_id);
                        if ($results) {
                            if($results->course_progress != NULL){
                               $tokens->progress = round($results->course_progress);
                            }
                        }
                        if($tokens->course_type == "Higher"){
                            $higher_bookings = $this->bookingmodel->result_display_higher($tokens->thirdparty_id);
                            if($higher_bookings != FALSE){
                                if(!empty($higher_bookings['logit_values'])){
                                    $cal_level =  $cal_score  = '';
                                    $base_scores = json_decode($higher_bookings['logit_values']);
                                    $cal_level = $base_scores->overall->level;
                                    $cal_score = $base_scores->overall->score;
                                    if(!empty($cal_level)){
                                            $tokens->cefr_level = substr($cal_level, 0, 2);
                                    }
                                    if(!empty($cal_score)){
                                            $tokens->cats_scale = $cal_score;
                                    }
                                }else{
                                    $tokens->cefr_level = 'Not available';
                                    $tokens->cats_scale = 'Not available';
                                }
                            }
                        } elseif($tokens->course_type == "Core"){
                           /* Practice Test collegepre */
                            $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens->thirdparty_id . '" ');
                            $results = $query->getResultArray();
    
                            if(!empty($results)){
                                $tokens->practiceresults = $results;
                                if(!empty($results['0'])){
                                    $practice_test_1 = $this->gen_practicetest_result($results['0']['session_number'], $results['0']['thirdparty_id'] );
                                    if(!empty($practice_test_1)){
                                                    $tokens->practicetest1 = $practice_test_1;
                                    }
                                }
                                if(!empty($results['1'])){
                                    $practice_test_2 = $this->gen_practicetest_result($results['1']['session_number'], $results['1']['thirdparty_id'] );
                                    if(!empty($practice_test_2)){
                                                    $tokens->practicetest2 = $practice_test_2;
                                    }
                                }		
                            }
                            /* Practice Test TDS */
                            $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id, 'practice');
                            if(isset($practice_tds_results) && $practice_tds_results != FALSE){
                                foreach($practice_tds_results as $key => $practice_tds_result){
                                    if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                        $tds_practice['practice_test1'] = $practice_tds_result;
                                    }else{
                                        $tds_practice['practice_test2'] = $practice_tds_result;
                                    }
                                }
                                $practice_tests_tds = $this->gen_practicetest_result_tds($tds_practice);
                                if(!empty($practice_tests_tds) && $practice_tests_tds != FALSE){
                                    $tokens->practicetest1_tds = $practice_tests_tds['practice_msg1'];
                                    $tokens->practicetest2_tds = $practice_tests_tds['practice_msg2'];
                                }
                            }
                             /**** COLLEGEPRE CORE STARTs Final Test****/
                             /*  getting score out of 50 core */
                            if(!empty($tokens->section_one) && !empty($tokens->section_two)){
    
    
                                $section1 = json_decode($tokens->section_one);
                                $section2 = json_decode($tokens->section_two);
                                $section1_result = $this->process_results($section1->item);
                                $section2_result = $this->process_results($section2->item);
    
                                $questions = count($section1_result) + count($section2_result);
                                $score = array_sum($section1_result) + array_sum($section2_result);
                                $score_for_lookup = $score / $questions;
                                if(!empty($score)){
                                    $tokens->score_outof50 = $score;
                                }
                                /* score on CATs scale */
                                if(!empty($score)){
                                    $product_level = array('11' => 'A1.1', '12' => 'A1.2', '13' => 'A1.3', '21' => 'A2.1', '22' => 'A2.2', '23' => 'A2.3', '31' => 'B1.1', '32' => 'B1.2', '33' => 'B1.3', '41' => 'B2.1', '42' => 'B2.2', '43' => 'B2.3');
                                    $thirdPartyId = $tokens->thirdparty_id;
                                    $course_id = substr($thirdPartyId, 10, 2);
                                    $level = $product_level[$course_id];
                                    $for = 'viewresult';
                                    $cefr_val_threshold = $this->get_cefr_threshold($level, $score, $tokens->thirdparty_id, $for);
                                    $expploded_val = explode("-", $cefr_val_threshold);		
                                    $cefr_val = $expploded_val['0'];
                                    $cats_scale = $expploded_val['1'];
                                    if(!empty($cefr_val)){
                                            $tokens->cefr_level = $cefr_val;
                                    }
                                    if(!empty($cats_scale)){
                                            $tokens->cats_scale = $cats_scale;
                                    }							
                                } /**** COLLEGEPRE CORE ENDS****/
                                
                            }elseif(!empty($tokens->tds_candidate_id)&& !empty($tokens->processed_data)){
                                /**** TDS CORE STARTs Final Test****/
                                $processed_data = json_decode($tokens->processed_data, true);
                                $tokens->score_outof50 = $processed_data['total']['score'];
                                $tokens->cefr_level = $processed_data['overall']['level'];
                                $tokens->cats_scale = $processed_data['overall']['score'];
                            }  
                        }elseif($tokens->course_type == "Primary"){
                            $tokens->practicetest2 = 'Not available';
                            if(!empty($tokens->form_id)){
                                $builder = $this->db->table('collegepre_results as CR');
                                $builder->select('CR.form_id,CR.section_one,CR.section_two,FC.type');
                                $builder->join('collegepre_formcodes as FC', 'FC.form_id = CR.form_id', 'left');
                                $builder->where('CR.thirdparty_id = "' . $tokens->thirdparty_id . '"');
                                $query = $builder->get();
                                $result_primary = $query->getResult();
                                if($query->getNumRows() > 0){
                                    foreach($result_primary as $primary){
                                        if($primary->type == "Live test"){
                                           $live_score = @get_primary_results($primary->section_one, false);
                                        }else{
                                                $practice_score = @get_primary_results($primary->section_one, false);
                                        }
                                    }
                                    if(!empty($live_score)){
                                        $tokens->score_outof50 = $live_score['score'].'/'.$live_score['total'];
                                        $tokens->cefr_level = $tokens->level;
                                        $tokens->cats_scale = $live_score['percentage'];
                                    }
    
                                    if(!empty($practice_score)){
                                        $tokens->practicetest1 = $practice_score['percentage'];
                                    } 
                                }
                            }else{
                                /* Practice Test TDS primary */
                                $practice_tds_results = $this->bookingmodel->tds_practice_detail($tokens->thirdparty_id, 'practice');
                                if(!empty($practice_tds_results) && $practice_tds_results != FALSE){
                                    foreach($practice_tds_results as $key => $practice_tds_result){
                                        if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                            $processed_data_practice = json_decode($practice_tds_result['processed_data'], true);
                                            if ($processed_data_practice != NULL) {
                                                $tokens->practicetest1_tds = $processed_data_practice['overall']['percentage'];
                                            }
                                            else{
                                                $tokens->practicetest1_tds = '';
    
                                            }
                                        }
                                    }
                                }
    
                                if(!empty($tokens->tds_candidate_id)&& !empty($tokens->processed_data)){
                                    /**** TDS Primary STARTs Final Test****/
                                    $processed_data = json_decode($tokens->processed_data, true);
                                    $tokens->score_outof50 = $processed_data['overall']['score'].'/'.$processed_data['overall']['outof'];
                                    $tokens->cefr_level = $tokens->level;
                                    $tokens->cats_scale = $processed_data['overall']['percentage'];
                                }
                            } 
                        }
                    }
                 }
    
                }
    
                $data = array(
                    'admin_title' => lang('app.language_school_view_learner_progress'),
                    'admin_heading' => lang('app.language_school_view_learner_progress'),
                    'school_lists' => $institutes,
                    'firstclick' => 'nosearch',
                    'tokens' => $token_data['results'],
                    'token_links' => $token_data['pager'],
                    'institute_id' => $this->request->getGet('institute'),
                    'aftersubmit' => '1',
                );
            }
            else{
    
                $data = array(
                    'admin_title' => lang('app.language_school_view_learner_progress'),
                    'admin_heading' => lang('app.language_school_view_learner_progress'),
                    'school_lists' => $institutes,
                    'firstclick' => 'nosearch',
                    'tokens' => '',
                    'token_links' => '',
                    'institute_id' => $this->request->getGet('institute'),
                    'aftersubmit' => '0'
                );
                
            }
    
            echo view('admin/view_learner_progress',$data);
        }

    /* Admin common process result function */
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
    /* Admin practicetest_result get function */
    public function gen_practicetest_result_tds($tds_practices = false){
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
               }else{
                   $data['practice_msg2'] = "Not taken";
               }
               return $data;
        }else{
            return FALSE;
        } 
    }
    

       /* Admin weighting_table get function */
        public function sw_weighting_table() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $speaking_weight_current = $this->tdsmodel->sw_weighting_current($type = "speaking", "stepcheck");
            $writing_weight_current = $this->tdsmodel->sw_weighting_current($type = "writing", "stepcheck");
    
            $speaking_weight_versions = $this->tdsmodel->get_all_version_control($type = "speaking", "stepcheck","weighting");
            $writing_weight_versions = $this->tdsmodel->get_all_version_control($type = "writing", "stepcheck","weighting");
            
            $data = array(
                'admin_title' => lang('app.language_admin_tds_speaking_writing_weighting'),
                'admin_heading' => lang('app.language_admin_tds_speaking_weighting'),
                'speaking_weight_current' => $speaking_weight_current,
                'writing_weight_current' => $writing_weight_current,
                'speaking_version_details' => $speaking_weight_versions,
                'writing_version_details' =>   $writing_weight_versions,
            );
    
            echo view('admin/sw_weighting_tds',$data);
        }
        /* Admin speaking_adjusting_table get function */
        public function speaking_adjusting_table() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $speaking_ability_current = $this->tdsmodel->get_sw_ability_current($type = "speaking", "stepcheck");
            $speaking_ability_version_details = $this->tdsmodel->get_all_version_control($type = "speaking", "stepcheck", "ability");
            $data = array(
                'admin_title' => lang('app.language_admin_tds_speaking_adjusting_ability'),
                'admin_heading' => lang('app.language_admin_tds_speaking_ability'),
                'speaking_ability_current' => $speaking_ability_current,
                'speaking_ability_version_details' => $speaking_ability_version_details,
                'post_type' => "tds",
            );
            echo view('admin/speaking_ability_tds',$data);
        }
        /* Admin writing_adjusting_table get function */
        public function writing_adjusting_table() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $writing_ability_current = $this->tdsmodel->get_sw_ability_current($type = "writing", "stepcheck");
            $writing_ability_version_details = $this->tdsmodel->get_all_version_control($type = "writing", "stepcheck","ability");
            $data = array(
                'admin_title' => lang('app.language_admin_tds_writing_adjusting_ability'),
                'admin_heading' => lang('app.language_admin_tds_writing_ability'),
                'writing_ability_current' => $writing_ability_current,
                'writing_ability_version_details' => $writing_ability_version_details,
                'post_type' => "tds",
            );
            echo view('admin/writing_ability_tds',$data);
        }
        /* Admin cefr_ability_table  get function */
        public function cefr_ability_table() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $cefr_ability_versions = $this->tdsmodel->get_all_version_control("all", "all","scale");
            $cefr_ability_current = $this->tdsmodel->get_cefr_ability_current();
            $data = array(
                'admin_title' => lang('app.language_admin_tds_menu_cefr'),
                'admin_heading' => lang('app.language_admin_tds_cefr_ability'),
                'cefr_ability_versions' => $cefr_ability_versions,
                'cefr_ability_current' => $cefr_ability_current,
                'post_type' => "tds",
            );
            echo view('admin/cats_cefr_level',$data);
        }
        /* Admin cron job get function */
        public function tds_jobs() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $collegepresettings = $this->collegepremodel->get_collegepre_settings();
            $data = array(
                'admin_title' => lang('app.language_admin_scheduler'),
                'admin_heading' => lang('app.language_admin_scheduler'),
                'mail_to' => $this->collegepremodel->get_cron_mailto(),
                'url' => $collegepresettings['url'],
                'suffix' => $collegepresettings['suffix'],
                'success_logs' => $this->tdsmodel->get_tds_benchmark_success_logs(),
                'failure_logs' => $this->tdsmodel->get_tds_benchmark_failure_logs(),
            );
            echo view('admin/gettdsjobs',$data);
        }
        /* Admin raw_result get function */
        function tds_raw_results() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            
         /* pagination */
         $perPage =  10;
         $offset = 0;
         $uri = current_url(true);
         $TotalSegment_array = ($uri->getSegments());
         $admin_segment = array_search('admin',$TotalSegment_array,true);
         $segment = $admin_segment + 3;
         $pager = "";
         $total = $this->tdsmodel->tds_record_count();
         if($total > 10){
            $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
            $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_tds_raw_results');
            $offset = $page * $perPage;
            $pager = $this->pager;
         }

            $data = array(
                'admin_title' => lang('app.language_admin_formal_tds_view_raw_result'),
                'admin_heading' => lang('app.language_admin_formal_tds_view_raw_result'),
                'results' => $this->tdsmodel->fetch_tds_benchmark_tasks($perPage, $offset),
                'pager' => $pager
            );
            echo view('admin/listtdsrawresults',$data);
        }
        /* Admin set placement test menu get function */
        public function list_adaptive_placement_test(){
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $data = array(
                'admin_title' =>  lang('app.language_admin_set_placement_heading'),
                'admin_heading' => lang('app.language_admin_set_placement_heading'),
                'placement_details' => $this->placementmodel->fetch_placement_test_details_core(),
                'active_placement' => $this->placementmodel->fetch_active_placement_core(),
            );
            echo view('admin/listplacementdetails',$data);
        }
        /* Admin primary_question_upload menu list function */
        function primary_question_upload_form() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }

            $data = array(
                'admin_title' => lang('app.language_admin_question_bank_upload'),
                'admin_heading' => lang('app.language_admin_question_bank_upload'),
                'results' => $this->placementmodel->fetch_placement_tests(),
                'linear_details' => $this->placementmodel->get_bank_details('linear'),
            );
    
            echo view('admin/primary_question_upload',$data);
        }
       /* Admin primary_placement_configs menu get function */
        function primary_placement_configs() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $data = array(
                    'admin_title' =>  'Primary placement test settings',
                    'admin_heading' => 'Primary placement test settings',
                    'products_primary'  => $this->productmodel->get_product(FALSE, 'Primary'),
                    'settings' => $this->placementmodel->get_result_settings_version($type='placement', $course='Primary', 'placement_settings'),
                    'primary_placement_all_versions' => $this->placementmodel->get_all_result_version_control('', $type='placement', $course='Primary', 'placement_settings_all'),
                    'primary_placement_current_version' => $this->placementmodel->get_currrent_result_version_control('placement_settings')
        
            );
            echo view('admin/primary_placement_configs',$data);
        }
       /* Admin primary_placement_configs_details  get function */
        public function get_primary_placement_configs_details() {
            $version = $this->request->getPost('value');
            $results = $this->placementmodel->get_all_result_version_control($version, $type='placement', $course='Primary', 'placement_settings_all');
            
            foreach($results as $result) {
                $logit_values = unserialize($result->logit_values);
                foreach($logit_values as $key => $value) {
                    $dataLogit[$key] = $value;
                }
                $data['logit_values'] = $dataLogit;
                $data['message'] = $result->message;
            }
            echo json_encode(array('data' => $data));
        }

    /* linear export responses */
    function linear_export() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200); /* 300 seconds = 5 minutes */


        $data = array(
            'admin_title' => lang('app.language_admin_adp_export'),
            'admin_heading' => lang('app.language_admin_adp_export'),
        );

        if ($this->request->getPost()) {


            $start_date = strtotime(str_replace('/', '-', $this->request->getPost('start_date')));
            $end_date = strtotime(str_replace('/', '-', $this->request->getPost('end_date')));

            if ($start_date != '' && $end_date != ''):
                if ($end_date < $start_date):
                    $this->session->setFlashdata('errors', lang('app.language_admin_placement_e_date_less_msg'));
                    return redirect()->to(site_url('admin/linear_export'));
                endif;

                if ($start_date < strtotime("11/04/2016")):
                    $this->session->setFlashdata('errors', "Export data available after 04 November 2016");
                    return redirect()->to(site_url('admin/linear_export'));
                endif;

                $placement_sessions = $this->placementmodel->get_primary_placement_all_sessions($start_date, $end_date);
            else:

                $this->session->setFlashdata('errors', lang('app.language_admin_placement_e_date_and_s_date_required_msg'));
                return redirect()->to(site_url('admin/linear_export'));

            endif;
            if (in_array('report', @$this->request->getPost('export_style'))) {
                if (empty($placement_sessions)) {
                    $this->session->setFlashdata('errors', lang('app.language_admin_placement_session_no_users_msg'));
                    return redirect()->to(site_url('admin/linear_export'));
                }
                $this->do_primary_report_styled_csv($placement_sessions);
            }   
        }
        echo view('admin/linear_export_form',$data);
    }
    

         /* report style primary */
         function do_primary_report_styled_csv($placement_sessions) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
          
            foreach ($placement_sessions as $session):
              $responses = unserialize($session->user_answers);
              foreach ($responses as $a => $b):
                    $reponses_array[] = @array('id' => $session->user_id, 'item_id' => $a, 'score' => $b['u_score'], 'response' => $b['u_value'], 'task_level' => $session->task_level, 'recommended_level' => $session->recommended_level, 'final_score' => $session->score,'school' => $session->token_issued_organization_name, 'datetime' => date('d-m-Y', $session->datetime));
              endforeach;
            endforeach;
          
    
            $heading_array = array('id' => 'Candidate ID', 'item_id' => 'Item ID', 'score' => 'Score', 'response' => 'Response', 'task_level' => 'Task Level', 'recommended_level' => 'Recommended level', 'final_score' => 'Final Score', 'school' => 'School', 'datetime' => 'Date');
            array_unshift($reponses_array, $heading_array);
    
            echo array_to_csv($reponses_array, 'Primary Placement Test Candidate\'s Responses Report Style - Date as on-' . date('d-m-Y') . '.csv');
            die;
        }


                public function getPracticetestJobs() {     
                    if (!$this->acl_auth->logged_in()) {
                        return redirect()->to(site_url('admin/login'));
                    }
                    $collegepresettings = $this->collegepremodel->get_collegepre_settings();
                $data = array(
                    'admin_title' => lang('app.language_admin_scheduler'),
                    'admin_heading' => lang('app.language_admin_scheduler'),
                    'mail_to' => $this->collegepremodel->get_cron_mailto(),
                    'url' => $collegepresettings['url'],
                    'suffix' => $collegepresettings['suffix'],
                    'success_logs' => $this->collegepremodel->get_collegepre_success_logs(),
                    'failure_logs' => $this->collegepremodel->get_collegepre_failure_logs(),
                    'test_type' => 'practice',
                );
                
                echo view('admin/getjobs',$data);
            }
            /* Admin core_sw_weighting_table get function */
            public function core_sw_weighting_table() {
 
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $sp_core_weigthing = $this->tdsmodel->get_all_version_control($type = "speaking", "core","weighting");
                $wr_core_weigthing = $this->tdsmodel->get_all_version_control($type = "writing", "core","weighting");
                $sp_core_weigthing_current = $this->tdsmodel->sw_weighting_current($type = "speaking", "core");
                $wr_core_weigthing_current = $this->tdsmodel->sw_weighting_current($type = "writing", "core");
        
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_speaking_writing_weighting'),
                    'admin_heading' => lang('app.language_admin_tds_speaking_weighting'),
                    'sp_core_weigthing_current' => $sp_core_weigthing_current,
                    'wr_core_weigthing_current' => $wr_core_weigthing_current,
                    'sp_core_weigthing_details' => $sp_core_weigthing,
                    'wr_core_weigthing_details' => $wr_core_weigthing,
                );
        
                echo view('admin/sw_weighting_core',$data);
            }
            /* Admin core_speaking_adjusting_table get function */
            public function core_speaking_adjusting_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $speaking_ability_current = $this->tdsmodel->get_sw_ability_current($type = "speaking", "core");
                $speaking_ability_version_details = $this->tdsmodel->get_all_version_control($type = "speaking", "core", "ability");
        
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_speaking_adjusting_ability'),
                    'admin_heading' => lang('app.language_admin_tds_speaking_ability'),
                    'speaking_ability_current' => $speaking_ability_current,
                    'speaking_ability_core_details' => $speaking_ability_version_details,
                    'post_type' => "core",
                );
                echo view('admin/speaking_ability_core',$data);
            }
            /* Admin core_writing_adjusting_table get function */
            public function core_writing_adjusting_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $writing_core_ability_current = $this->tdsmodel->get_sw_ability_current($type = "writing", "core");
                $writing_ability_version_details = $this->tdsmodel->get_all_version_control($type = "writing", "core", "ability");
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_writing_adjusting_ability'),
                    'admin_heading' => lang('app.language_admin_tds_writing_ability'),
                    'writing_core_ability_current' => $writing_core_ability_current,
                    'writing_core_ability_version_details' => $writing_ability_version_details,
                    'post_type' => "core",
                );
                echo view('admin/writing_ability_core',$data);
            }
            /* Admin core_cefr_ability_table get function */
            public function core_cefr_ability_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $cefr_ability_versions = $this->tdsmodel->get_all_version_control("all", "all","scale");
                $cefr_ability_current = $this->tdsmodel->get_cefr_ability_current();
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_menu_cefr'),
                    'admin_heading' => lang('app.language_admin_tds_cefr_ability'),
                    'cefr_ability_versions' => $cefr_ability_versions,
                    'cefr_ability_current' => $cefr_ability_current,
                    'post_type' => "core",
                );
                echo view('admin/cats_cefr_level',$data);
        
            }

            public function getJobs() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $collegepresettings = $this->collegepremodel->get_collegepre_settings();
                $output = shell_exec('crontab -l');
                $data = array(
                    'admin_title' => lang('app.language_admin_scheduler'),
                    'admin_heading' => lang('app.language_admin_scheduler'),
                    'jobs' => self::stringToArray($output),
                    'mail_to' => $this->collegepremodel->get_cron_mailto(),
                    'url' => $collegepresettings['url'],
                    'suffix' => $collegepresettings['suffix'],
                    'success_logs' => $this->collegepremodel->get_collegepre_success_logs(),
                    'failure_logs' => $this->collegepremodel->get_collegepre_failure_logs(),
                );
        
                echo view('admin/getjobs', $data);
            }
 
            /* Admin common string to array function */
            static private function stringToArray($jobs = '') {
                $array = explode("\r\n", trim($jobs)); /* trim() gets rid of the last \r\n */
                foreach ($array as $key => $item) {
                    if ($item == '') {
                        unset($array[$key]);
                    }
                }
                return $array;
            }

           /* Admin result_display_settings get function */
            public function result_display_settings() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $data = array(
                    'admin_title' => lang('app.language_admin_formal_test_display'),
                    'admin_heading' => lang('app.language_admin_formal_test_display'),
                    'settings' => $this->placementmodel->get_result_settings_version($type='final', $course='Core', 'result_display_settings'),
                    'result_dispaly_all_versions' => $this->placementmodel->get_all_result_version_control('', $type='final', $course='Core', 'result_display_settings_all'),
                    'result_dispaly_current_version' => $this->placementmodel->get_currrent_result_version_control('result_display_settings')
                );
                echo view('admin/result_display_settings',$data);
        
            }
            /* Admin higher_sw_weighting_table get function */
            public function higher_sw_weighting_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $higher_current_sp_weight = $this->tdsmodel->sw_weighting_current($type = "speaking", "higher");
                $higher_current_wr_weight = $this->tdsmodel->sw_weighting_current($type = "writing", "higher");
                $higher_speaking_weight_versions = $this->tdsmodel->get_all_version_control($type = "speaking", "higher","weighting");
                $higher_writing_weight_versions = $this->tdsmodel->get_all_version_control($type = "writing", "higher","weighting");
        
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_speaking_writing_weighting'),
                    'admin_heading' => lang('app.language_admin_tds_speaking_weighting'),
                    'higher_current_sp_weight' => $higher_current_sp_weight,
                    'higher_current_wr_weight' => $higher_current_wr_weight,
                    'higher_sp_version_details' => $higher_speaking_weight_versions,
                    'higher_wr_version_details' => $higher_writing_weight_versions,
                );
                echo view('admin/sw_weighting_higher',$data);
        
            }
            /* Admin higher_speaking_adjusting_table get function */
            public function higher_speaking_adjusting_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $speaking_ability_current = $this->tdsmodel->get_sw_ability_current($type = "speaking", "higher");
                $speaking_ability_version_details = $this->tdsmodel->get_all_version_control($type = "speaking", "higher", "ability");
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_speaking_adjusting_ability'),
                    'admin_heading' => lang('app.language_admin_tds_speaking_ability'),
                    'hr_speaking_ability_current' => $speaking_ability_current,
                    'hr_speaking_ability_version_details' => $speaking_ability_version_details,
                    'post_type' => "higher",
                );
                echo view('admin/speaking_ability_higher',$data);
            }
            /* Admin higher_writing_adjusting_table get function */
            public function higher_writing_adjusting_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $writing_higher_ability_current = $this->tdsmodel->get_sw_ability_current($type = "writing", "higher");
                $writing_ability_version_details = $this->tdsmodel->get_all_version_control($type = "writing", "higher", "ability");
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_writing_adjusting_ability'),
                    'admin_heading' => lang('app.language_admin_tds_writing_ability'),
                    'writing_higher_ability_current' => $writing_higher_ability_current,
                    'writing_ability_version_details' => $writing_ability_version_details,
                    'post_type' => "higher",
                );
        
                echo view('admin/writing_ability_higher',$data);
            }
            /* Admin tds_reading_ability_table get function */
            public function tds_reading_ability_table($id = false) {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $data['tds_formids'] = $this->collegepremodel->get_higher_formcodes('tds');
                $versions_higher_reading = $this->tdsmodel->get_all_version_control("reading","higher","ability");
                $current_reading_formid = $this->tdsmodel->get_rl_ability_current("reading","higher");
                $data = array(
                    'admin_title' => lang('app.language_admin_higher_reading_top_title'),
                    'admin_heading' => lang('app.language_admin_higher_reading_title'),
                    'tds_formids' => $data['tds_formids'],
                    'current_reading_higher' => $current_reading_formid,
                    'versions_higher_reading' => $versions_higher_reading,
                    'id' => $id,
                );
                echo view('admin/tds_reading_ability',$data);
            }
            /* Admin tds_listening_ability_table get function */
            public function tds_listening_ability_table($id = false) {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $data['tds_formids'] = $this->collegepremodel->get_higher_formcodes('tds');
                $versions_higher_listening = $this->tdsmodel->get_all_version_control("listening","higher","ability");
                $current_listening_formid = $this->tdsmodel->get_rl_ability_current("listening","higher");
                $data = array(
                    'admin_title' => lang('app.language_admin_higher_listening_top_title'),
                    'admin_heading' => lang('app.language_admin_higher_listening_title'),
                    'current_listening_higher' => $current_listening_formid,
                    'versions_higher_listening' => $versions_higher_listening,
                    'tds_formids' => $data['tds_formids'],
                    'id' => $id,
                );
                echo view('admin/tds_listening_ability',$data);
            }
           /* Admin higher_cefr_ability_table get function */
            public function higher_cefr_ability_table() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $cefr_ability_versions = $this->tdsmodel->get_all_version_control("all", "all","scale");
                $cefr_ability_current = $this->tdsmodel->get_cefr_ability_current();
                $data = array(
                    'admin_title' => lang('app.language_admin_tds_menu_cefr'),
                    'admin_heading' => lang('app.language_admin_tds_cefr_ability'),
                    'cefr_ability_versions' => $cefr_ability_versions,
                    'cefr_ability_current' => $cefr_ability_current,
                    'post_type' => "higher",
                );
                echo view('admin/cats_cefr_level',$data);
            } 

            public function higher_jobs() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                $collegepresettings = $this->collegepremodel->get_collegepre_settings();
                $data = array(
                    'admin_title' => lang('app.language_admin_scheduler'),
                    'admin_heading' => lang('app.language_admin_scheduler'),
                    'mail_to' => $this->collegepremodel->get_cron_mailto(),
                    'url' => $collegepresettings['url'],
                    'suffix' => $collegepresettings['suffix'],
                    'success_logs' => $this->collegepremodel->get_collegepre_higher_success_logs(),
                    'failure_logs' => $this->collegepremodel->get_collegepre_higher_failure_logs(),
                );
                echo view('admin/gethigherjobs',$data);
            }

    /* Admin update_unit_progress menu get function */
    public function update_unit_progress()
    {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_update_unit_progress'),
            'admin_heading' => lang('app.language_admin_update_unit_progress'),
        );
        echo view('admin/update_unit_progress',$data);
    }

    /* Admin testform_details_update menu add & edit function */
    public function testform_details_update($id = FALSE) {
            
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        header('Content-Type: application/json');
        if (Null !== $this->request->getPost()) {

            $rules =[
                'form_version' => [
                    'label'  => lang('app.language_admin_testform_version'),
                    'rules'  => 'max_length[4]|required|check_integer',
                     ]
                ];
            if($this->request->getPost('replace_id') == ''){
                if ($this->request->getPost('active_status') === NULL) { 
                    $rules['active_status'] = [
                        'label'  => lang('app.language_admin_test_status'),
                        'rules'  => 'required',
                    ];
                }
            }
            if($id == FALSE){
                    $rules['form_id'] = [
                        'label'  => lang('app.language_admin_testform_id'),
                        'rules'  => 'max_length[4]|required|check_integer',
                    ];
                if ($this->request->getPost('test_purpose') == '1' ) {   
                    $rules['test_name'] = [
                        'label'  => lang('app.language_admin_testform_name'),
                        'rules'  => 'max_length[60]|required',
                    ];
                }
                if ($this->request->getPost('test_parts') === NULL ) { 
                    $rules['test_parts'] = [
                        'label'  => lang('app.language_admin_test_parts'),
                        'rules'  => 'required',
                    ];
                }
                if ($this->request->getPost('replace_id') == '' ) { 
                    $rules['test_purpose'] = [
                        'label'  => lang('app.language_admin_testform_purpose'),
                        'rules'  => 'required',
                    ];
                    $rules['test_type'] = [
                        'label'  => lang('app.language_admin_testform_type'),
                        'rules'  => 'required',
                    ];
                }
                if ($this->request->getPost('test_purpose') == '3' || $this->request->getPost('test_purpose') == '4') {
                    $rules['test_purpose'] = [
                        'label'  => lang('app.language_admin_testform_purpose'),
                        'rules'  => 'required',
                    ];
                }
            }

            if ($this->validate($rules) == FALSE) {
                $response['success'] = 0;
                $errors = [
                    'form_id' => $this->validation->showError('form_id'),
                    'form_version' => $this->validation->showError('form_version'),
                    'test_type' => $this->validation->showError('test_type'),
                    'test_name' => $this->validation->showError('test_name'),
                    'test_parts' => $this->validation->showError('test_parts'),
                    'active_status' => $this->validation->showError('active_status'),
                    'test_purpose' => $this->validation->showError('test_purpose'),
                    'test_product' => $this->validation->showError('test_product'),
                ];

                $response['errors'] = $errors;
                echo json_encode($response);die;
            }
        
        else{

            if($id == FALSE){
                  /* Check Test Form ID is unique */
                    $builder = $this->db->table('tds_test_detail TTD');
                    $builder->select('test_formid');
                    $builder->where('test_formid', $this->request->getPost('form_id')); 
                    $formid_result = $builder->get();
                    if($formid_result->getNumRows() > 0){
                        $this->session->setFlashdata('errors','Test Form Id Already Exist');	
                        echo json_encode(array('success' => 1, 'msg' => 'Test Form Id Already Exist'));
                        die;
                    }
        
            if($this->request->getPost('test_purpose') == 1){
                $builder = $this->db->table('tds_test_detail TTD');
                $builder ->select('SUBSTRING(test_slug, 19) as detail_count', FALSE);
                $builder ->where('TTD.tds_group_id', $this->request->getPost('test_purpose'));
                $builder ->orderBy('TTD.id', 'DESC');
                $query = $builder->get();
                $rowdata = $query->getRow();
                $result =  $rowdata->detail_count;
                $count = $result + 1;
                $test_slug = 'benchmarking_type_'.$count;
            }else{
    
                $builder = $this->db->table('tds_test_group');
                $builder ->select('test_group_slug');
                $builder ->where('id',$this->request->getPost('test_purpose'));
                $query = $builder->get()->getRow();
                $test_slug =  $query->test_group_slug;
            }
    
            if($this->request->getPost('test_purpose') == 5){ 
                if($this->request->getPost('test_type') == 'Adaptive'){
                    $testtype_id = 48;
                }elseif($this->request->getPost('test_type') == 'Linear'){
                    $testtype_id = 49;
                }else{
                    $testtype_id = 0;
                }
                
            }else{
                $testtype_id = 0;
            }
    
            $testdetail = [
                'tds_group_id' => $this->request->getPost('test_purpose'),
                'test_name' => $this->request->getPost('test_name'),
                'test_formid' => $this->request->getPost('form_id'),
                'test_formversion' => $this->request->getPost('form_version'),
                'test_type' => $this->request->getPost('test_type'),
                'test_product_id' => ($this->request->getPost('test_product')) ? $this->request->getPost('test_product') : 0,
                'testtypes_id' => $testtype_id,
                'test_slug' => $test_slug,
                'parts' => json_encode($this->request->getPost('test_parts')),
                'status' => $this->request->getPost('active_status'),
            ];
    
            $builder = $this->db->table('tds_test_detail');
            $builder->insert($testdetail);
            $insert_detail = $this->db->insertID();
    
            if ($insert_detail) {
    
                $test_detail_id = $insert_detail;
                if ($this->request->getPost('test_purpose') == 1) {
                            
                    $testtype_detail = [
                        'title' => $this->request->getPost('test_name'),
                        'testGroupId' => '3'
                    ];	
    
                    $builder = $this->db->table('testtypes');
                    $builder->insert($testtype_detail);
                    $insert_testtype_detail = $this->db->insertID();
                    
                    if ($insert_testtype_detail) {
                        $testtypes_id = $insert_testtype_detail;
                        $builder = $this->db->table('tds_test_detail');
                        $builder->where('id',$test_detail_id);
                        $builder->set('testtypes_id', $testtypes_id);
                        $builder->update();
                        $send_testtype = $this->send_testtype_to_dwh();
                    }
                }
    
                elseif ($this->request->getPost('test_purpose') == 3 || $this->request->getPost('test_purpose') == 4){
                            
                    $testgroupid = 0;
                    
                    if ($this->request->getPost('test_purpose') == 3) $testgroupid =  4;
                    if ($this->request->getPost('test_purpose') == 4) $testgroupid =  2;
                    
                    $test_product_id = $this->request->getPost('test_product');
                    
    
                    $builder = $this->db->table('testtypes');
                    $builder ->select('id');
                    $builder->where('productId',$test_product_id);
                    $builder->where('testGroupId',$testgroupid);
    
                    $testtype_query_result = $builder->get();
                    
                    if ($testtype_query_result->getNumRows() > 0){
    
                        $testtype_query_result_data = $testtype_query_result->getRow();
                        $testtypes_id = $testtype_query_result_data->id;
                        
                        $builder = $this->db->table('tds_test_detail');
                        $builder->where('id',$test_detail_id);
                        $builder->set('testtypes_id', $testtypes_id);
                        $builder->update();
    
                    }else{
                                                        
                        $testtype_detail = [
                            'title' => $this->request->getPost('test_name'),
                            'testGroupId' => $testgroupid
                        ];	
                            
                        $builder = $this->db->table('testtypes');
                        $builder->insert($testtype_detail);
                        $insert_testtype_detail = $this->db->insertID();
              
                        if ($insert_testtype_detail) {
                            $testtypes_id = $insert_testtype_detail;
                            $builder = $this->db->table('tds_test_detail');
                            $builder->where('id',$test_detail_id);
                            $builder->set('testtypes_id', $testtypes_id);
                            $builder->update();
                            $send_testtype = $this->send_testtype_to_dwh();
                        } 
                    }
    
    
                }
    
                if(null !== $this->request->getPost('replace_id') && $this->request->getPost('replace_id') != ''){
                    $updateid = $this->request->getPost('replace_id');
                    if($updateid != '' && $updateid != '0'){
    
                        $builder = $this->db->table('tds_test_detail');
                        $builder->where('id',$updateid);
                        $builder->set('status',0);
                        $builder->update();
    
                        $builder = $this->db->table('tds_test_detail');
                        $builder->select('tds_test_detail.test_formid');
                        $builder->where('tds_test_detail.id', $updateid);
                            $query = $builder->get();
                            $result = $query->getRow();
                            if($query->getNumRows() > 0){
                                $test_formid = $result->test_formid;
                                $builder = $this->db->table('tds_tests');
                                $builder->set('test_formid',$this->request->getPost('form_id'));
                                $builder->set('test_formversion',$this->request->getPost('form_version'));
                                $builder->where('test_formid',$test_formid);
                                $builder->where('status',0);
                                $builder->update();
                            }
                    }
                }
                $this->session->setFlashdata('success','Test Form Details added successfully');
                echo json_encode(array('success' => 1, 'msg' => 'Test Form Details added successfully'));
                die;
            }
          }
            else{
                
                if($this->request->getPost('active_status') == 0){
                    $test_detail_id = $this->request->getPost('id');
                    $test_detail = $this->cmsmodel->get_tds_test_detail_by_id($test_detail_id);
                    $test_formid = $test_detail['test_formid'];
                    /* Check the form id is already associated to any test ssession */
                    $formid_exist = $this->cmsmodel->check_formid_already_associated($test_formid);
                    if($formid_exist != NULL){
                        if($test_detail['test_slug'] == 'final'){
                            $response['success'] = 0;
                            $errors = [
                                'form_already_exist' => '<p>'.lang('app.language_admin_test_form_id_already_associated').'</p>',
                            ];						
                            $response['errors'] = $errors;
                            echo json_encode($response);die;
                        }
                        elseif($test_detail['test_slug'] == 'practice'){
              
                            $response['success'] = 0;
                            $errors = array(
                                'test_type' => 'practice',
                                'test_detail_id' => $test_detail['id'],
                            );						
                            $response['errors'] = $errors;
                            echo json_encode($response);die;
                        }
                        
                    }
                    
                    /* Check the form id is active placement form for core/higher */
                    if($test_detail['test_slug'] == 'placement' && $test_detail['test_type'] == 'Adaptive'){
                        $formid_active_placement = $this->cmsmodel->check_formid_active_placement($test_detail_id);
                        if($formid_active_placement){
                            $response['success'] = 0;
                            $errors = array(
                                'active_placement' => '1',
                            );						
                            $response['errors'] = $errors;
                            echo json_encode($response);die;
                        }
                    }
                }
                
                $testdetail = [
                    'test_formversion' => $this->request->getPost('form_version'),
                    'status' => $this->request->getPost('active_status'),
                ];
                $builder = $this->db->table('tds_test_detail');
                $builder->where('id', $this->request->getPost('id'));
            
                if ($builder->update($testdetail)) {
                    
                    if(isset($test_detail) && $test_detail['test_slug'] == 'final' && $test_detail['test_formid'] != '0'){
                        if($this->request->getPost('active_status') == 0){
    
                            $builder = $this->db->table('tds_allocation_formcode');
                            $builder->set('status',0);
                            $builder->where('form_code', $test_detail['test_formid']);
                            $builder->update();
    
                        }
                    }
                    $this->session->setFlashdata('success','Test Form Details updated successfully');						
                    echo json_encode(array('success' => 1, 'msg' => 'Test Form Details updated successfully'));
                    die;
                }
                
                
                else{
                    $this->session->setFlashdata('errors',lang('language_admin_institute_nothing_to_update_msg'));	
                    echo json_encode(array('success' => 1, 'msg' => lang('language_admin_institute_nothing_to_update_msg')));
                    die;	
                }
            }
    
        }
    
    }
    
    }
    
    /* Admin valid_test_form_id get function */
    public function get_valid_test_form_id()
    {
        $test_product_id = $this->request->getPost('test_product_id');
        $test_group_id = $this->request->getPost('test_purpose');
        $test_detail_id = ($this->request->getPost('test_detail_id')) ? $this->request->getPost('test_detail_id') : '';

        $where_array = array();
        if($test_group_id != '' && $test_product_id == '') {
            $where_array = array(
                'tds_test_group_id' => $test_group_id
            );
        } else if($test_group_id != '' && $test_product_id != ''){
            
            /* check product id higher */
            $builder = $this->db->table('products');
            $builder->select('course_type');
            $builder->where('id', $test_product_id);

            $check_higher = $builder->get()->getRowArray();

            $higher_product_array = array();
            if($check_higher['course_type'] == 'Higher') {
                $builder = $this->db->table('products');
                $builder->select('id');
                $builder->where('course_type', 'Higher');
                $higher_products = $builder->get()->getResult();
                
                foreach($higher_products as $higher_product) {
                    $higher_product_array[] =  $higher_product->id;
                }

                $where_array = [
                    'tds_test_group_id' => $test_group_id
                ];
            } else {
                $where_array = [
                    'test_product_id' => $test_product_id,
                    'tds_test_group_id' => $test_group_id
                ];
            }
        }

        if(!empty($where_array)) {
            $builder = $this->db->table('tds_test_forms');
            $builder->select('*');
            if($test_group_id != '' && $test_product_id != '' && $check_higher['course_type'] == 'Higher') {
                $builder->where($where_array);
                $builder->groupStart();
                foreach($higher_product_array as $higher_product_id) {
                    $builder->orWhere('test_product_id', $higher_product_id);
                }
                $builder->groupEnd();
            } else {
                $builder->where($where_array);
            }
            $results =  $builder->get()->getResult();
            if(!empty($results)) {
                foreach($results as $result) {
                    $form_id[''] = 'Please select';
                    $form_id[$result->test_formid] = $result->test_formid;
                }
                $extraAttr="class='form-control' id='form_id'";
                echo form_dropdown('form_id', $form_id, '', $extraAttr);
            } else if(count($results) == 0 && isset($test_detail_id) && $test_detail_id != '') {
                $test_detail_data = $this->cmsmodel->get_tds_test_detail_by_id($test_detail_id);
                $form_id_array[''] = 'Please select';
                $form_id_array[$test_detail_data['test_formid']] = $test_detail_data['test_formid'];
                $extraAttr="class='form-control' id='form_id'";
                echo form_dropdown('form_id', $form_id_array, '', $extraAttr);
            }
        }
    }
    
    /* Admin test_product get function */
    public function get_test_product()
    {
        $test_purpose = $this->request->getPost('test_purpose');
        $product_results = $this->cmsmodel->get_tds_products_based_purpose($test_purpose);
        foreach($product_results as $result) {
            $products[''] = 'Please select';
            if($result->id == 10) {
                $products[$result->id] = 'Step Higher';
            }elseif($result->id <= 9 || $result->id > 12) {
                $products[$result->id] = $result->name;
            }
        }
        $extraAttr="class='form-control' id='test_product' onchange='getFormId()' ";
        echo form_dropdown('test_product', $products, '', $extraAttr);
    }
    /* Admin form_id_exist check function */
    public function check_form_id_exist()
    {
        $form_id = $this->request->getPost('form_id');
        $results = $this->usermodel->check_form_id_exist($form_id);
        if(!empty($results)) {
            echo json_encode(['response' => 'success']);
        } else {
            echo json_encode(['response' => 'failure']);
        }
    }
    /* Admin test_form_version valis get function */
    public function get_valid_test_form_version()
    {
        $test_detail_id = $this->request->getPost('test_detail_id');
        $edit = $this->request->getPost('edit');

        if($test_detail_id && $edit) {
            $test_detail_data = $this->cmsmodel->get_tds_test_detail_by_id($test_detail_id);
            $test_product_id = $test_detail_data['test_product_id'];
            $test_group_id = $test_detail_data['tds_group_id'];
            $form_id = $test_detail_data['test_formid'];
            $form_version = $test_detail_data['test_formversion'];
        } else {
            $test_product_id = $this->request->getPost('test_product_id');
            $test_group_id = $this->request->getPost('test_purpose');
            $form_id = $this->request->getPost('form_id');
            $form_version = $this->request->getPost('form_version');
        }

        $where_array = array();
        if($test_product_id != '0' &&$test_product_id != '' && $test_group_id != '' && $form_id != '') {

            /* check product id higher */
            $builder = $this->db->table('products');
            $builder->select('course_type');
            $builder->where('id', $test_product_id);
            $check_higher = $builder->get()->getRowArray();
            $higher_product_array = array();
            if($check_higher['course_type'] == 'Higher') {

                $builder = $this->db->table('products');
                $builder->select('id');
                $builder->where('course_type', 'Higher');
                $higher_products =  $builder->get()->getResult();
                
                foreach($higher_products as $higher_product) {
                    $higher_product_array[] =  $higher_product->id;
                }

                $where_array =  [
                    'tds_test_group_id' => $test_group_id,
                    'test_formid' => $form_id
                ];
            } else {
                $where_array = [
                    'test_product_id' => $test_product_id,
                    'tds_test_group_id' => $test_group_id,
                    'test_formid' => $form_id
                ];
            }
        }
        else if($test_product_id == '' || $test_product_id == '0' && $test_group_id != '' && $form_id != '') {
            $where_array = [
                'tds_test_group_id' => $test_group_id,
                'test_formid' => $form_id
            ];
        }
        if(!empty($where_array)) {

            if($test_product_id != '0' && $test_group_id != '' && $test_product_id != '' && $form_id != '' && $check_higher['course_type'] == 'Higher') {
                $builder = $this->db->table('tds_test_forms');
                $builder->where('test_formid',$form_id);
                $builder->where('tds_test_group_id',$test_group_id);
                $builder->select('*');
                $builder->groupStart();
                foreach($higher_product_array as $higher_product_id) {
                    $builder->orWhere('test_product_id', $higher_product_id);
                }
                $builder->groupEnd();

                $query = $builder->get();
                $results = $query->getResult();

            } else{
            $builder = $this->db->table('tds_test_forms');
            $builder->select('*');
            $builder->where('test_formid',$form_id);
            $builder->where('tds_test_group_id',$test_group_id);
            $query = $builder->get();
            $results = $query->getResult();
            }

            if(!empty($results)) {
                foreach($results as $result) {
                    $form_version_array[""] = 'Please select';
                    $form_version_array[$result->test_formversion] = $result->test_formversion;
                }
                $extraAttr="class='form-control' id='test_purpose'";
                if($edit && $form_version != '') {
                    $selected = $form_version;
                } else {
                   $selected = '';
                }
                echo form_dropdown('form_version', $form_version_array, $selected, $extraAttr);
            } else {
                $extraAttr="class='form-control' id='test_purpose'";
                if($edit && $test_detail_id) {
                    $form_version_array[""] = 'Please select';
                    $form_version_array[$form_version] = $form_version;
                    $selected = $form_version;
                } 
                echo form_dropdown('form_version', $form_version_array, $selected, $extraAttr);
            }
        }
    }
        
    /* Admin set_placement_test menu active and inactive function */
    public function set_adaptive_placement(){
		if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
		
		if (null !== $this->request->getPost()) {
            
            $rules = [
                'testformdetail_id' => [
                    'label'  => lang('app.language_admin_testform_version'),
                    'rules'  => 'trim|required',
                    ],
                ];               
            if ($this->validate($rules) == FALSE) {
                $this->session->setFlashdata('errors','Select one form to make active');
                $data['validation'] = $this->validator;
                return redirect()->to(site_url('admin/list_adaptive_placement_test'));
              }

            else{
				
				$placement_query = $this->db->query('SELECT * FROM tds_placement_active_form WHERE test_type = "Adaptive"');
				$placement_result = $placement_query->getRow();
				if($placement_query->getNumRows() > 0){
					$placement_active_id = 	$placement_result->id;
                        $builder = $this->db->table('tds_placement_active_form');
                        $builder->set('tds_test_detail_id',$this->request->getPost('testformdetail_id'), false);
                        $builder->where('id',$placement_active_id);
                        $builder->update();

					if ($this->db->affectedRows() > 0) {
						$this->session->setFlashdata('success','Placement test made active successfully');
                        return redirect()->to(site_url('admin/list_adaptive_placement_test'));
					}else{
						$this->session->setFlashdata('errors','No Changes made');
                        return redirect()->to(site_url('admin/list_adaptive_placement_test'));
					}
				}else{
				
					$placement_detail = array(
						'tds_test_detail_id' => $this->request->getPost('testformdetail_id'),
						'test_type' => 'Adaptive',
					);

                        $builder = $this->db->table('tds_placement_active_form');
                        $builder ->insert($placement_detail);
					if ($this->db->affectedRows() > 0) {
						$this->session->setFlashdata('success','Placement test made active successfully');
                        return redirect()->to(site_url('admin/list_adaptive_placement_test'));
					}
				}
				
			}
		}
    }
   
    /* Admin primary_placement_configs menu add function */
    public function add_primary_placement_configs() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        if ($this->request->getPost()) {
            $array = $this->request->getPost();
            $flag = true;
            foreach($array as $k => $v){
                if($v < 0){
                    $flag = false;
                }
            }
            if($flag === true){ /* Check Whole number or not */
                $count = count($array);
                $array = array_values($array);
                
                $y = $n = 0;
                for($k=$count; $k>0; $k--){
                    if($k>1){
                        if($array[$k-1] > $array[$k-2]){
                            $y = $y+1;
                        }else{
                            $n = $n+1;
                        }
                    }
                }
                if($n > 0){ /* check ascending order or not */
                    $response['success'] = 0;
                    $response['errors'] = lang('app.language_admin_placement_configs_error_msg');
                    echo json_encode($response);die;
                } else {
                
                    $serialize_data = serialize(array(
                        'A0_1' => $this->request->getPost('A0_1'),
                        'A0_2' => $this->request->getPost('A0_2'),
                        'A0_3' => $this->request->getPost('A0_3'),
                        'A1_1' => $this->request->getPost('A1_1'),
                        'A1_2' => $this->request->getPost('A1_2'),
                        'A1_3' => $this->request->getPost('A1_3'),
                        'A2_1' => $this->request->getPost('A2_1'),
                        'A2_2' => $this->request->getPost('A2_2'),
                        'A2_3' => $this->request->getPost('A2_3')
                    ));
        
                   

                    $current_version = $this->db->query('SELECT * FROM placement_settings WHERE id = 3');
                    $new_version = (count($current_version->getRowArray()) > 0) ? $current_version->getRowArray()['version'] + 1 : 1;
                    $dataResult = array(
                        'initial_difficulty' => $current_version->getRowArray()['initial_difficulty'],
                        'set_limit' => $current_version->getRowArray()['set_limit'],
                        'time_limit' => $current_version->getRowArray()['time_limit'],
                        'logit_values' => $serialize_data,
                        'version' => $new_version
                    );

                    $builder = $this->db->table('placement_settings');
                    $builder->where('id', 3);
                    $builder->update($dataResult);

                    $builder = $this->db->table('placement_settings_all');
                    $builder->insert($dataResult);
   
        
                    $dataResultReason = array(
                        'version' => $new_version,
                        'type' => 'placement',
                        'course' => 'Primary',
                        'message' => $this->request->getPost('message'),
                    );

                    $builder = $this->db->table('result_display_settings_version_control');
                    $builder->insert($dataResultReason);

                    $this->session->setFlashdata('success',lang('app.language_admin_placement_configs_success_msg'));						
                    echo json_encode(array('success' => 1, 'msg' => lang('app.language_admin_placement_configs_success_msg')));
                    die;
                }
            }
        } else {
            $data = array(
                'admin_title' =>  'Add primary placement test settings',
                'admin_heading' => 'Add primary placement test settings',
                'products_primary'  => $this->productmodel->get_product(FALSE, 'Primary')
            );
        }        
        echo view('admin/add_primary_placement_configs',$data);
    }


    /* code for post values to db both writing and speaking ability */
    public function post_ability() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $look_up_type = "ability";
        if($this->request->getPost("type") == "tds"){
            $table_name = "tds_setting_stepcheck_swability";
            $table_name_all = "tds_setting_stepcheck_swability_all";
            $course_type = "";
        }elseif($this->request->getPost("type") == "higher"){
            $table_name = "tds_setting_higher_swability";
            $table_name_all = "tds_setting_higher_swability_all";
            $course_type = "higher_";
        }elseif($this->request->getPost("type") == "core"){
            $table_name = "tds_setting_core_swability";
            $table_name_all = "tds_setting_core_swability_all";
            $course_type = "core_";
        }

        if($this->request->getPost("ability") == "speaking"){
            $upload_path = $this->efsfilepath->efs_result_csv.'speakingability';
            $type = "speaking";
            $ability = "speaking_ability";
            $view_table = "admin/".$course_type."speaking_adjusting_table";
            $success_msg = lang('app.language_admin_tds_speaking_ability_success');
        } elseif($this->request->getPost("ability") == "writing"){; 
          $upload_path = $this->efsfilepath->efs_result_csv. 'writingability'; 
          $type = "writing";
          $ability = "writing_ability";
          $view_table = "admin/".$course_type."writing_adjusting_table";
          $success_msg = lang('app.language_admin_tds_writing_ability_success');
        }
    
        if ($this->request->getFile("speaking_ability")) {
    
            $rules =[
                'speaking_ability' => 'uploaded[speaking_ability]|max_size[speaking_ability,10000]|ext_in[speaking_ability,csv]',
            ];

        }else{
           
            $rules =[
                'writing_ability' => 'uploaded[writing_ability]|max_size[writing_ability,10000]|ext_in[writing_ability,csv]',
            ];
        }

        $rules1 =[
            'text_area_sp' => [
              'label'  => 'text_area_sp',
              'rules'  => 'required',
              ],
        ];

        if (!$this->validate($rules1)) {
            $this->session->setFlashdata('errors', lang('app.language_admin_weighting_reason'));
            return redirect()->to(site_url($view_table));
        }

        $message=trim($this->request->getPost("text_area_sp"));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
            return redirect()->to(site_url($view_table));
        }else{

            $speaking_ability=$this->request->getFile("speaking_ability");
            $writing_ability=$this->request->getFile("writing_ability");
    
            $file_details = isset($speaking_ability) ? $speaking_ability : $writing_ability;
          
            if ($file_details != NuLL) {
    
                $newName = $file_details->getRandomName();
                $file_details->move($upload_path, $newName);
                $filepath = "$upload_path/"."$newName";
            }
    
            $uploaded_data = 
            [
                'file_name' => $file_details->getName(),
                'details' => $file_details,
                'full_path' => $filepath,
                'orig_name' => $file_details->getClientName(),
            ];

            /* save file details */
            $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));
            /* $insert_details_id = $this->db->insert('formcodes_details', $data_formcode_file); */

            $lexer = new Lexer(new LexerConfig());
            $interpreter = new Interpreter();
            $interpreter->unstrict(); /* Ignore row column count consistency */
            $interpreter->addObserver(function(array $rows) use (&$csvArray) {
            
                if (!empty($rows)) {
                    if (count($rows) == 2):
                        $csvArray[] = array('Adjusted score' => @$rows[0], 'Ability estimate' => @$rows[1]);
                    endif;
                }
            });

            $lexer->parse($uploaded_data['full_path'], $interpreter);
            if (!empty($csvArray) && is_numeric(substr($csvArray[0]['Adjusted score'], 0, 1))) {
                $queryMain = $this->db->query('SELECT * FROM ' .$table_name . ' WHERE type = "' . $type . '"');
                $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                
                foreach ($csvArray as $csvdata) {
                    /* main table */
                    $insMaindata_abilitys[] = array(
                            'adjusted_score' => @$csvdata['Adjusted score'],
                            'ability_estimate' => @$csvdata['Ability estimate'],
                            'type' => @$type,
                            'version' => $new_version 
                        );
                }
                
                $queryMain = $this->db->query('SELECT * FROM ' .$table_name . ' WHERE type = "' . $type . '"');
                if($queryMain->getNumRows() > 0){
                    $builder = $this->db->table($table_name);
                    $builder->where('type', $type);
                    $builder->delete();
                    if($this->db->affectedRows() > 0){
                    foreach($insMaindata_abilitys as $insMaindata_ability){
                        $builder1 = $this->db->table($table_name);  
                        $builder1->insert($insMaindata_ability); 

                        $builder2 = $this->db->table($table_name_all);  
                        $builder2->insert($insMaindata_ability); 
                        }    
                    }
                }else{
                    foreach($insMaindata_abilitys as $insMaindata_ability){
                    $builder1 = $this->db->table($table_name);  
                    $builder1->insert($insMaindata_ability); 

                    $builder2 = $this->db->table($table_name_all);  
                    $builder2->insert($insMaindata_ability);   
                    }
                }
                $version_details = array(
                    'type' => @$type,
                    'course'=> ($this->request->getPost("type") == "tds") ? "stepcheck" : $this->request->getPost("type"),
                    'lookup'=> $look_up_type,
                    'version' => $new_version,
                    'message' => $message
                );
                $builder = $this->db->table('tds_setting_version_control');  
                $builder->insert($version_details);  
                $this->session->setFlashdata('messages', $success_msg);
                return redirect()->to(site_url($view_table));
            }else{
                $this->session->setFlashdata('errors', 'No rows in CSV!');
                return redirect()->to(site_url($view_table));
            }
         }
    }
        /* ajax fetch data from  ability table - stepcheck, core, higher */
        public function sw_ability_version() {
            $version = $this->request->getPost('value');
            $type = $this->request->getPost('type');
            $course = $this->request->getPost('course');
            $lookup = $this->request->getPost('lookup');
            $sw_ability_databy_version = $this->tdsmodel->get_all_sw_ability_by_version($type, $course, $version, $lookup);
            echo json_encode(array('valid' => isset($sw_ability_databy_version['lookup']) ? $sw_ability_databy_version['lookup'] : "", 'notes' => isset($sw_ability_databy_version['reason']['message']) ? $sw_ability_databy_version['reason']['message'] : ""));
        }

        /* ajax fetch data from  weighting table - stepcheck, core, higher */
        public function sw_weighting_version() {
            $version = $this->request->getPost('value');
            $type = $this->request->getPost('type');
            $course = $this->request->getPost('course');
            $lookup = $this->request->getPost('lookup');
            $sw_weight_databy_version = $this->tdsmodel->get_all_sw_weighting_by_version($type, $course, $version,$lookup);
            echo json_encode(array('valid' => isset($sw_weight_databy_version['lookup']) ? $sw_weight_databy_version['lookup'] : "", 'notes' => isset($sw_weight_databy_version['reason']['message']) ? $sw_weight_databy_version['reason']['message'] : ""));
        }

       /* ajax fetch data from  ability table - stepcheck, core, higher */
        public function cefr_scale_values() {
            $version = $this->request->getPost('value');
            $type = $this->request->getPost('type');
            $course = $this->request->getPost('course');
            $lookup = $this->request->getPost('lookup');
            $cefr_values = $this->tdsmodel->get_all_cefr_ability($type, $course, $version, $lookup);
            echo json_encode(array('valid' => isset($cefr_values['lookup']) ? $cefr_values['lookup'] : "", 'notes' => isset($cefr_values['reason']['message']) ? $cefr_values['reason']['message'] : ""));
        }
       
       /* code for weighting table for both writing and speaking - Both TDS and Higher */
        public function post_weighting() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            $lookup = "weighting";
            if($this->request->getPost("type") == "tds"){
                $table_name = "tds_setting_stepcheck_weight";
                $table_name_all = "tds_setting_stepcheck_weight_all";
                $redirect_url = 'admin/sw_weighting_table';
            }elseif($this->request->getPost("type") == "higher"){
                $table_name = "tds_setting_higher_weight";
                $table_name_all = "tds_setting_higher_weight_all";
                $redirect_url = 'admin/higher_sw_weighting_table';
            }elseif($this->request->getPost("type") == "core"){
                $table_name = "tds_setting_core_weight";
                $table_name_all = "tds_setting_core_weight_all";
                $redirect_url = 'admin/core_sw_weighting_table';
            }
            if($this->request->getPost("weighting") == "speaking"){
                $upload_path = $this->efsfilepath->efs_result_csv.'speakingweighting';
                $type = "speaking";
                $weighting = "speaking_weighting";
                $starting_letter = "S";
                $success_msg = lang('app.language_admin_tds_speaking_weighting_success');
            } elseif($this->request->getPost("weighting") == "writing"){
            $upload_path = $this->efsfilepath->efs_result_csv.'writingweighting';
            $type = "writing";
            $weighting = "writing_weighting";
            $starting_letter = "W";
            $success_msg = lang('app.language_admin_tds_writing_weighting_success');
            }

            if ($this->request->getFile("speaking_weighting")) 
                {
                    $rules =[
                        'speaking_weighting' => 'uploaded[speaking_weighting]|max_size[speaking_weighting,10000]|ext_in[speaking_weighting,csv]',
                    ];
                }elseif($this->request->getFile("writing_weighting")){

                    $rules =[
                        'writing_weighting' => 'uploaded[writing_weighting]|max_size[writing_weighting,10000]|ext_in[writing_weighting,csv]',
                    ];
                }

                $rules1 =[
                    'text_area_sp' => [
                    'label'  => 'text_area_sp',
                    'rules'  => 'required',
                    ],
            ];

            if (!$this->validate($rules1)) {
                $this->session->setFlashdata('errors', lang('app.language_admin_weighting_reason'));
                return redirect()->to(site_url($redirect_url));
            }

            $message=trim($this->request->getPost("text_area_sp"));
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
                return redirect()->to(site_url($redirect_url));
            }else{
            
                $speaking_weighting=$this->request->getFile("speaking_weighting");
                $writing_weighting=$this->request->getFile("writing_weighting");

                $file_details = isset($speaking_weighting) ? $speaking_weighting : $writing_weighting;
            
                if ($file_details != NuLL) {

                    $newName = $file_details->getRandomName();
                    $file_details->move($upload_path, $newName);
                    $filepath = "$upload_path/"."$newName";
                }

                $uploaded_data = 
                [
                    'file_name' => $file_details->getName(),
                    'details' => $file_details,
                    'full_path' => $filepath,
                    'orig_name' => $file_details->getClientName(),
                ];

                /* save file details */
                $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));
                /* $insert_details_id = $this->db->insert('formcodes_details', $data_formcode_file); */

                $lexer = new Lexer(new LexerConfig());
                $interpreter = new Interpreter();
                $interpreter->unstrict(); /* Ignore row column count consistency */
                $interpreter->addObserver(function(array $rows) use (&$csvArray) {
                
                    if (!empty($rows)) {
                        if (count($rows) == 2):
                            $csvArray[] = array('Number' => @$rows[0], 'Out of Score' => @$rows[1]);
                        endif;
                    }
                });

                $lexer->parse($uploaded_data['full_path'], $interpreter);
                if (!empty($csvArray) && substr($csvArray[0]['Number'], 0, 1) == $starting_letter) {
                    $queryMain = $this->db->query('SELECT * FROM ' .$table_name . ' WHERE type = "' . $type . '"');
                    $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                    
                    foreach ($csvArray as $csvdata) {
                        /* main table */
                        $insMaindata_weightings[] = array(
                                'qnumber' => @$csvdata['Number'],
                                'weight' => @$csvdata['Out of Score'],
                                'type' => @$type,
                                'version' => $new_version 
                            );
                    }
                    
                    $queryMain = $this->db->query('SELECT * FROM ' .$table_name . ' WHERE type = "' . $type . '"');
                    if($queryMain->getNumRows() > 0){
                        $builder = $this->db->table($table_name);
                        $builder->where('type', $type);
                        $builder->delete();
                        if($this->db->affectedRows() > 0){
                        foreach($insMaindata_weightings as $insMaindata_weighting){
                            $builder1 = $this->db->table($table_name);  
                            $builder1->insert($insMaindata_weighting); 

                            $builder2 = $this->db->table($table_name_all);  
                            $builder2->insert($insMaindata_weighting); 
                            }    
                        }
                    }else{
                        foreach($insMaindata_weightings as $insMaindata_weighting){
                        $builder1 = $this->db->table($table_name);  
                        $builder1->insert($insMaindata_weighting); 

                        $builder2 = $this->db->table($table_name_all);  
                        $builder2->insert($insMaindata_weighting);   
                        }
                    }
                    $version_details = array(
                        'type' => @$type,
                        'course'=> ($this->request->getPost("type") == "tds") ? "stepcheck" : $this->request->getPost("type"),
                        'lookup'=> $lookup,
                        'version' => $new_version,
                        'message' => $message
                    );
                    $builder = $this->db->table('tds_setting_version_control');  
                    $builder->insert($version_details);  
                    $this->session->setFlashdata('messages', $success_msg);
                    return redirect()->to(site_url($redirect_url));
                }
                else {
                $this->session->setFlashdata('errors', 'No rows in CSV!');
                return redirect()->to(site_url($redirect_url));
            }

        }

    }
      /* Admin cefr_ability add function */
       public function post_cefr_ability() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
           
            $upload_path = $this->efsfilepath->efs_result_csv .'cefrability';

                $rules =[
                    'cefr_ability' => 'uploaded[cefr_ability]|max_size[cefr_ability,10000]|ext_in[cefr_ability,csv]',
                ];

                $rules1 =[
                    'text_area_sp' => [
                    'label'  => 'text_area_sp',
                    'rules'  => 'required',
                    ],
                ];

            if (!$this->validate($rules1)) {
                $this->session->setFlashdata('errors', lang('app.language_admin_weighting_reason'));
                return redirect()->to(site_url('admin/cefr_ability_table'));
            }

            $message=trim($this->request->getPost("text_area_sp"));
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
                return redirect()->to(site_url('admin/cefr_ability_table'));
            }else{

                $cefr_ability = $this->request->getFile("cefr_ability");
                $file_details = $cefr_ability;

                if ($file_details != NuLL) {

                    $newName = $file_details->getRandomName();
                    $file_details->move($upload_path, $newName);
                    $filepath = "$upload_path/"."$newName";
                }
        
                $uploaded_data = 
                [
                    'file_name' => $file_details->getName(),
                    'details' => $file_details,
                    'full_path' => $filepath,
                    'orig_name' => $file_details->getClientName(),
                ];
 
                /* save file details */
                $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));
                /* $insert_details_id = $this->db->insert('formcodes_details', $data_formcode_file); */

                $lexer = new Lexer(new LexerConfig());
                $interpreter = new Interpreter();
                $interpreter->unstrict(); /* Ignore row column count consistency */
                $interpreter->addObserver(function(array $rows) use (&$csvArray) {
                   
                    if (!empty($rows)) {
                        if (count($rows) == 3):
                            $csvArray[] = array('CATs scale' => @$rows[0], 'CATs/CEFR level' => @$rows[1],'Ability estimate' => @$rows[2]);
                        endif;
                    }
                });
        
                $lexer->parse($uploaded_data['full_path'], $interpreter);
                if (!empty($csvArray)) {
                    $queryMain = $this->db->query('SELECT * FROM tds_setting_cefrlevel order by id desc');
                    $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                    /* remove data folder and table */
                    $builder = $this->db->table('tds_setting_cefrlevel');
			        $builder->truncate();
                    foreach ($csvArray as $csvdata) {
                        /* main table */
                        $insMaindata = array(
                            'scale' => $csvdata['CATs scale'],
                            'cefr_level' => $csvdata['CATs/CEFR level'],
                            'ability_estimate' => $csvdata['Ability estimate'],
                            'version' => $new_version,
                        );
                        $builder1 = $this->db->table('tds_setting_cefrlevel');  
                        $builder1->insert($insMaindata); 
 
                        $builder2 = $this->db->table('tds_setting_cefrlevel_all');  
                        $builder2->insert($insMaindata); 
                    }
                    $version_details = array(
                        'type' => "all",
                        'course'=> "all",
                        'lookup' => "scale",
                        'version' => $new_version,
                        'message' => $message
                    );
                    $builder = $this->db->table('tds_setting_version_control');  
                    $builder->insert($version_details);  
                    $this->session->setFlashdata('messages', 'CATs Step scale/CEFR level details updated');
                    return redirect()->to(site_url('admin/cefr_ability_table'));
                }else {
                    $this->session->setFlashdata('errors', 'No rows in CSV!');
                    return redirect()->to(site_url('admin/cefr_ability_table'));
              }

            }

        }
        /* Admin higher_cefr_ability add function */
        public function higher_post_cefr_ability() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
    
            $upload_path = $this->efsfilepath->efs_result_csv.'cefrability';

            $rules =[
                'cefr_ability' => 'uploaded[cefr_ability]|max_size[cefr_ability,10000]|ext_in[cefr_ability,csv]',
            ];

            $message=trim($this->request->getPost("text_area_sp"));
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
                return redirect()->to(site_url('admin/higher_cefr_ability_table'));
            }else{

                $cefr_ability = $this->request->getFile("cefr_ability");
                $file_details = $cefr_ability;
        
                if ($file_details != NuLL) {
        
                    $newName = $file_details->getRandomName();
                    $file_details->move($upload_path, $newName);
                    $filepath = "$upload_path/"."$newName";
                }
        
                $uploaded_data = 
                [
                    'file_name' => $file_details->getName(),
                    'details' => $file_details,
                    'full_path' => $filepath,
                    'orig_name' => $file_details->getClientName(),
                ];
                
                /* save file details */
                $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));
        
                $lexer = new Lexer(new LexerConfig());
                $interpreter = new Interpreter();
                $interpreter->unstrict(); /* Ignore row column count consistency */
                $interpreter->addObserver(function(array $rows) use (&$csvArray) {
                    
                    if (!empty($rows)) {
                        if (count($rows) == 3):
                            $csvArray[] = array('CATs scale' => @$rows[0], 'CATs/CEFR level' => @$rows[1],'Ability estimate' => @$rows[2]);
                        endif;
                    }
                });
        
                $lexer->parse($uploaded_data['full_path'], $interpreter);
                if (!empty($csvArray)) {
                    $queryMain = $this->db->query('SELECT * FROM tds_setting_cefrlevel order by id desc');
                    $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                    /* remove data folder and table */
                    $builder = $this->db->table('tds_setting_cefrlevel');
                    $builder->truncate();
                    foreach ($csvArray as $csvdata) {
                        /* main table */
                        $insMaindata = array(
                            'scale' => $csvdata['CATs scale'],
                            'cefr_level' => $csvdata['CATs/CEFR level'],
                            'ability_estimate' => $csvdata['Ability estimate'],
                            'version' => $new_version,
                        );
                        $builder1 = $this->db->table('tds_setting_cefrlevel');  
                        $builder1->insert($insMaindata); 
        
                        $builder2 = $this->db->table('tds_setting_cefrlevel_all');  
                        $builder2->insert($insMaindata); 
                    }
                    $version_details = array(
                        'type' => "all",
                        'course'=> "all",
                        'lookup' => "scale",
                        'version' => $new_version,
                        'message' => $message
                    );
                    $builder = $this->db->table('tds_setting_version_control');  
                    $builder->insert($version_details);  
                    $this->session->setFlashdata('messages', 'CATs Step scale/CEFR level details updated');
                    return redirect()->to(site_url('admin/higher_cefr_ability_table'));
                }else {
                    $this->session->setFlashdata('errors', 'No rows in CSV!');
                    return redirect()->to(site_url('admin/higher_cefr_ability_table'));
                }
            }
        }
        /* Admin core_cefr_ability add function */
        public function core_post_cefr_ability() {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
        
            $upload_path = $this->efsfilepath->efs_result_csv.'cefrability';
            
            $rules =[
                'cefr_ability' => 'uploaded[cefr_ability]|max_size[cefr_ability,10000]|ext_in[cefr_ability,csv]',
            ];

            $message=trim($this->request->getPost("text_area_sp"));
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
                return redirect()->to(site_url('admin/core_cefr_ability_table'));
            }else{

                $cefr_ability = $this->request->getFile("cefr_ability");
                $file_details = $cefr_ability;
        
                if ($file_details != NuLL) {
        
                    $newName = $file_details->getRandomName();
                    $file_details->move($upload_path, $newName);
                    $filepath = "$upload_path/"."$newName";
                }
        
                $uploaded_data = 
                [
                    'file_name' => $file_details->getName(),
                    'details' => $file_details,
                    'full_path' => $filepath,
                    'orig_name' => $file_details->getClientName(),
                ];
        
                /* save file details */
                $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));
        
                $lexer = new Lexer(new LexerConfig());
                $interpreter = new Interpreter();
                $interpreter->unstrict(); /* Ignore row column count consistency */
                $interpreter->addObserver(function(array $rows) use (&$csvArray) {
                   
                    if (!empty($rows)) {
                        if (count($rows) == 3):
                            $csvArray[] = array('CATs scale' => @$rows[0], 'CATs/CEFR level' => @$rows[1],'Ability estimate' => @$rows[2]);
                        endif;
                    }
                });
        
                $lexer->parse($uploaded_data['full_path'], $interpreter);
                if (!empty($csvArray)) {
                    $queryMain = $this->db->query('SELECT * FROM tds_setting_cefrlevel order by id desc');
                    $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                    /* remove data folder and table */
                    $builder = $this->db->table('tds_setting_cefrlevel');
                    $builder->truncate();
                    foreach ($csvArray as $csvdata) {
                        /* main table */
                        $insMaindata = array(
                            'scale' => $csvdata['CATs scale'],
                            'cefr_level' => $csvdata['CATs/CEFR level'],
                            'ability_estimate' => $csvdata['Ability estimate'],
                            'version' => $new_version,
                        );
                        $builder1 = $this->db->table('tds_setting_cefrlevel');  
                        $builder1->insert($insMaindata); 
        
                        $builder2 = $this->db->table('tds_setting_cefrlevel_all');  
                        $builder2->insert($insMaindata); 
                    }
                    $version_details = array(
                        'type' => "all",
                        'course'=> "all",
                        'lookup' => "scale",
                        'version' => $new_version,
                        'message' => $message
                    );
                    $builder = $this->db->table('tds_setting_version_control');  
                    $builder->insert($version_details);  
                    $this->session->setFlashdata('messages', 'CATs Step scale/CEFR level details updated');
                    return redirect()->to(site_url('admin/core_cefr_ability_table'));
                }else {
                    $this->session->setFlashdata('errors', 'No rows in CSV!');
                    return redirect()->to(site_url('admin/core_cefr_ability_table'));
              }
        
            }
    
        }

    /* ajax fetch data from  ability table - higher */
    public function rl_ability_version() {
        $value = explode("_", $this->request->getPost('value'));
        $form_code = $value[0];
        $version = isset($value[1]) ? $value[1] : "";
        $type = $this->request->getPost('type');
        $course = $this->request->getPost('course');
        $lookup = $this->request->getPost('lookup');
        $cefr_values = $this->tdsmodel->get_all_rl_ability_by_version($type, $course, $version, $form_code ,$lookup);
        echo json_encode(array('valid' => isset($cefr_values['lookup']) ? $cefr_values['lookup'] : "", 'notes' => isset($cefr_values['reason']['message']) ? $cefr_values['reason']['message'] : ""));
    }

    /* WP-1276
     * Function to upload Reading and Listening ablity lookup table
     */
    public function post_tds_rl_ability() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $test_type = 'tds';
        if ($this->request->getPost("ability") == "reading") {
            $upload_path = $this->efsfilepath->efs_result_csv .'readingability';
            $type = "reading";
            $ability = "reading_ability";
            $view_table = "admin/tds_reading_ability_table";
            $success_msg = lang('app.language_admin_higher_reading_ability_success');
        } elseif ($this->request->getPost("ability") == "listening") {
            $upload_path = $this->efsfilepath->efs_result_csv .'listeningability';
            $type = "listening";
            $ability = "listening_ability";
            $view_table = "admin/tds_listening_ability_table";
            $success_msg = lang('app.language_admin_higher_listening_ability_success');
        }

        if ($this->request->getPost('tds_test_formid') == NULL) {
            $this->session->setFlashdata('errors', 'No Formcodes Selected or Formcodes Not Available');
            return redirect()->to(site_url($view_table));

        }else{
            $id = $this->request->getPost('tds_test_formid');
            $queryMain = $this->db->query('SELECT tds_test_detail.test_formid FROM tds_test_detail WHERE id = "' . $id . '"');
            $test_formid = $queryMain->getRowArray();
            $test_formid_string = implode(" ", $test_formid);
        }

        if ($this->request->getFile("reading_ability")) {
    
            $rules =[
                'reading_ability' => 'uploaded[reading_ability]|max_size[reading_ability,10000]|ext_in[reading_ability,csv]',
            ];

        }elseif($this->request->getFile("listening_ability")){
        
            $rules =[
                'listening_ability' => 'uploaded[listening_ability]|max_size[listening_ability,10000]|ext_in[listening_ability,csv]',
            ];
        }

        $rules1 =[
            'text_area_sp' => [
            'label'  => 'text_area_sp',
            'rules'  => 'required',
            ],
        ];

        if (!$this->validate($rules1)) {
            $this->session->setFlashdata('errors', lang('app.language_admin_weighting_reason'));
            return redirect()->to(site_url($view_table));
        }
        $message=trim($this->request->getPost("text_area_sp"));

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
            return redirect()->to(site_url($view_table));
        }else{

            $reading_ability = $this->request->getFile("reading_ability");
            $listening_ability = $this->request->getFile("listening_ability");
    
            $file_details = isset($reading_ability) ? $reading_ability : $listening_ability;
            
            if ($file_details != NuLL) {
    
                $newName = $file_details->getRandomName();
                $file_details->move($upload_path, $newName);
                $filepath = "$upload_path/"."$newName";
            }
    
            $uploaded_data = 
            [
                'file_name' => $file_details->getName(),
                'details' => $file_details,
                'full_path' => $filepath,
                'orig_name' => $file_details->getClientName(),
            ];

            /* save file details */
            $data_formcode_file = array('file_name' => $uploaded_data['file_name'], 'details' => ($uploaded_data));

            $lexer = new Lexer(new LexerConfig());
            $interpreter = new Interpreter();
            $interpreter->unstrict(); /* Ignore row column count consistency */
            $interpreter->addObserver(function(array $rows) use (&$csvArray) {
            
                if (!empty($rows)) {
                    if (count($rows) == 2):
                        $csvArray[] = array('Adjusted score' => @$rows[0], 'Ability estimate' => @$rows[1]);
                    endif;
                }
            });

            $lexer->parse($uploaded_data['full_path'], $interpreter);
            if (!empty($csvArray) && is_numeric(substr($csvArray[0]['Adjusted score'], 0, 1))) {
                $queryMain = $this->db->query('SELECT * FROM tds_setting_higher_rlability WHERE type = "' . $type . '" AND form_code = "' .$test_formid_string . '"');
                $new_version = (count((array)$queryMain->getRowArray()) > 0) ? $queryMain->getRowArray()['version'] + 1 : 1;
                
                foreach ($csvArray as $csvdata) {
                    /*main table */
                    $insMaindata_abilitys[] = array(
                        'form_code' => $test_formid_string,
                        'score' => @$csvdata['Adjusted score'],
                        'ability_estimate' => $csvdata['Ability estimate'],
                        'type' => $type,
                        'test_type' => $test_type,
                        'version' => $new_version
                    );
                }
                
                $queryMain = $this->db->query('SELECT * FROM tds_setting_higher_rlability WHERE type = "' . $type . '" AND form_code = "' .$test_formid_string . '" AND test_type = "' .$test_type . '"');
                if($queryMain->getNumRows() > 0){
                    $builder = $this->db->table('tds_setting_higher_rlability');
                    $builder->where('type',$type); 
                    $builder->where('form_code',$test_formid_string); 
                    $builder->where('test_type',$test_type); 
                    $builder->delete();
                    if($this->db->affectedRows() > 0){
                    foreach($insMaindata_abilitys as $insMaindata_ability){
                        $builder1 = $this->db->table('tds_setting_higher_rlability');  
                        $builder1->insert($insMaindata_ability); 

                        $builder2 = $this->db->table('tds_setting_higher_rlability_all');  
                        $builder2->insert($insMaindata_ability); 
                        }    
                    }
                    }else{
                    foreach($insMaindata_abilitys as $insMaindata_ability){
                    $builder1 = $this->db->table('tds_setting_higher_rlability');  
                    $builder1->insert($insMaindata_ability); 

                    $builder2 = $this->db->table('tds_setting_higher_rlability_all');  
                    $builder2->insert($insMaindata_ability);   
                    }
                }
                $version_details = array(
                    'type' => @$type,
                    'course'=> "higher",
                    'lookup' => "ability",
                    'form_id'=> $test_formid_string,
                    'version' => $new_version,
                    'message' => $message
                );
                $builder = $this->db->table('tds_setting_version_control');  
                $builder->insert($version_details);  
                $this->session->setFlashdata('messages', $success_msg);
                return redirect()->to(site_url($view_table."/".$id));
            }else{
                $this->session->setFlashdata('errors', 'No rows in CSV!');
                return redirect()->to(site_url($view_table));
            }
        }

     }

    /* Admin profile update function */
    function profile() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $userdata = $this->session->get();
        $data = array(
            'admin_title' => lang('app.language_admin_profile'),
            'admin_heading' => lang('app.language_admin_profile'),
            'profiledatas' => $this->usermodel->get_profile($userdata['user_id']),
        );

        if (null !== $this->request->getPost()) {

            if ($this->request->getPost('btn-email')) {
                $profile = array('email' => $this->request->getPost('email'));
                if ($this->usermodel->update_profile($profile)) {
                    $this->session->setFlashdata('messages', lang('app.language_admin_profile_updated_success_msg'));
                    return redirect()->to(site_url('admin/profile'));
                }
            } elseif ($this->request->getPost('btn-password')) {

                $rules = [
                    'current_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_current_password'),
                        'rules'  => 'custom_trim[current_password]|required',
                        'errors' => [
                                'required' => lang('app.form_validation_required'),
                            ],
                        ],
                    'new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_new_password'),
                        'rules'  => 'custom_trim[new_password]|required|min_length[8]',
                        'errors' => [
                                'required' => lang('app.form_validation_required'),
                            ],
                        ],
                    'confirm_new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_confirm_new_password'),
                        'rules'  => 'custom_trim[confirm_new_password]|required|matches[new_password]',
                        'errors' => [
                                'required' => lang('app.form_validation_required'),
                            ],
                        ],
                    ];

                if (!$this->validate($rules)) {

                    $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                    $data['validation'] = $this->validator;
                } else {
                
                    if (!$this->passwordhash->CheckPassword($this->request->getPost('current_password'),$data['profiledatas'][0]->password)) {
                        $this->session->setFlashdata('errors', lang('app.language_site_change_password_current_password_invalid_msg'));
                        return redirect()->to(site_url('admin/profile'));
                    } else {

                        $passwordata = array('password' => $this->passwordhash->HashPassword($this->request->getPost('new_password')));

                        if ($this->usermodel->update_profile($passwordata)) {
                            $this->session->setFlashdata('messages', lang('app.language_site_change_password_updated_success_msg'));
                            return redirect()->to(site_url('admin/profile'));
                        }
                    }
                }
            }
        }
        echo view('admin/profile', $data);
    }


    /* TDS-349 Result release for one result slower than expected - try to fetch every 2hrs - END */
    public function view_xml($taskid){
        $xml_f= $taskid .'/'.$taskid . '.xml';
        $xml_file =$this->efsfilepath->efs_uploads_tds.$xml_f;
        header("Content-type: text/xml");
        $xml_file1 = @file_get_contents($xml_file);
        print_r($xml_file1);
        exit();
    }


    /* WP-1397 start */
    function tds_placement_configs() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');
        }
            $data = array(
                'admin_title' => lang('app.language_admin_placement_configs'),
                'admin_heading' => lang('app.language_admin_placement_configs'),
                'products' => $this->tdsmodel->get_products_placement_setting('cats_core_or_higher'),
                'active_product' => $this->tdsmodel->get_tds_placement_level_settings(1,0),
                'institutions' => $this->tdsmodel->fetch_institution_details(),
            );
        if ($this->request->getPost()) {

            $postarray = $this->request->getPost();	

                    $rules = [
                        'product_id' => [
                            'label'  => lang('app.language_admin_testform_version_product'),
                            'rules'  => 'trim|required',
                            ],
                        'institution_id' => [
                            'label'  => lang('app.language_ministry_dashboard_lbl_institution'),
                            'rules'  => 'trim|required',
                            ],
                    ];               
                    if ($this->validate($rules) == FALSE) {
                        $data['product_ids'] = $postarray['product_id'];
                        $data['institution_ids'] = $postarray['institution_id'];
                        $data['validation'] = $this->validator;
                      }
                
                else{              
                if(isset($postarray['product_id']) && $postarray['product_id'] != '' && $postarray['institution_id'] != ''){
                    $status = (isset($postarray['product_status'])) ? $postarray['product_status'] : 0;
                    if($status){
                        if($status == 2){
                            $status = 0;
                        }
                        $active_product = $this->tdsmodel->get_tds_placement_level_settings(0, $postarray['institution_id']);
                        $institution_row = $this->tdsmodel->fetch_institution_details($postarray['institution_id']);
                        if($active_product != Null && $active_product['institution_id'] == $postarray['institution_id']){
                            $product_details = $this->tdsmodel->get_products_by_id($postarray['product_id']);
                            $dataarray = array('institution_id' =>$postarray['institution_id'], 'institution_name'=>$institution_row->organization_name, 'product_id' => $postarray['product_id'], 'product_name' => $product_details->name, 'product_level' => $product_details->level, 'status' => $status);
                            $builder = $this->db->table('tds_placement_level_settings');
                            $builder->where('institution_id', $postarray['institution_id']);
                            $builder->update($dataarray);
                            if(isset($postarray['reload']) && $postarray['reload'] == 'load'){
                            $this->session->setFlashdata('messages', lang('app.language_admin_placement_configs_success_msg'));
                            return redirect()->to(site_url('admin/tds_placement_configs'));
                            }else{
                            $this->session->setFlashdata('success',lang('app.language_admin_placement_configs_success_msg'));						
                            echo json_encode(array('success' => 1, 'msg' => lang('app.language_admin_placement_configs_success_msg')));
                            die;
                            }

                        }
                        elseif((isset($active_product['institution_id'])) ? $active_product['institution_id'] : "" != $postarray['institution_id']){
                        $product_details = $this->tdsmodel->get_products_by_id($postarray['product_id']);
                        $dataarray = array('institution_id' =>$postarray['institution_id'],'institution_name'=>$institution_row->organization_name, 'product_id' => $postarray['product_id'], 'product_name' => $product_details->name, 'product_level' => $product_details->level, 'status' => $status);
                        $builder = $this->db->table('tds_placement_level_settings');
                        $builder->insert($dataarray);
                        if( $this->db->insertID()){
                            $this->session->setFlashdata('messages', lang('app.language_admin_placement_configs_success_msg_insert'));
                            return redirect()->to(site_url('admin/tds_placement_configs'));
                        }else{
                            $this->session->setFlashdata('messages', lang('app.language_admin_placement_configs_success_msg_insert_error'));
                            return redirect()->to(site_url('admin/tds_placement_configs'));
                        }
                        }
                    }
                }else
                {
                    return redirect()->to(site_url('admin/tds_placement_configs'));
                
                }
             }
        }   

        echo view('admin/tds_placement_configs', $data);

        } 
            /* Admin placement_institute_status get function */
            public function tds_placement_institute_status() {
                if (null !== $this->request->getPost() && $this->request->getPost('institution_id')) {
                    $institution_id = $this->request->getPost('institution_id');
                    if(!empty($institution_id)){
                        $tds_placement_details = $this->tdsmodel->get_tds_placement_level_settings(0, $institution_id);          
                        if($tds_placement_details){
                            $status = $tds_placement_details['status'];
                            echo json_encode(array('success' => 1, 'status' => $status, 'msg' => 'fetched'));
                            die;
                        }else{
                            $this->session->set_flashdata('errors', lang('app.language_admin_list_tier_users_failure_msg'));
                            echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                            die; 
                        }
                    }else{
                        $this->session->set_flashdata('errors', lang('app.language_admin_list_tier_users_failure_msg'));
                        echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                        die;    
                    }
                }
            }

            /* Admin placement_institute_status update function */
            public function tds_placement_institute_status_change() {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                if (null !== $this->request->getPost() && $this->request->getPost('institution_id')) {
                    $institution_id = $this->request->getPost('institution_id');
                    if(!empty($institution_id)){
                        $tds_placement_details = $this->tdsmodel->get_tds_placement_level_settings(0, $institution_id);          
                        if($tds_placement_details){
                                $lang = $tds_placement_details['status'] == 1 ? lang('app.language_admin_placement_configs_inactive') : lang('app.language_admin_placement_configs_active');
                                $status = $tds_placement_details['status'] == 1 ? 0 : 1;

                                $updateData = array('status' => $status);
                                $builder = $this->db->table('tds_placement_level_settings');
                                $builder->where('institution_id', $institution_id);
                                if ($builder->update($updateData)) {
                                    $this->session->setFlashdata('messages', $lang);
                                    echo json_encode(array('success' => 1, 'msg' => 'updated'));
                                    die;
                                } else {
                                    $this->session->setFlashdata('errors', lang('app.language_admin_list_tier_users_failure_msg_update'));
                                    echo json_encode(array('success' => 0, 'msg' => 'Not updated'));
                                    die;
                                }
                        }
                    }
                }
            }
            /* Admin tds_placement_configs edit function */
            public function tds_placement_configs_edit($id=FALSE)
            {
                if (!$this->acl_auth->logged_in()) {
                    return redirect()->to(site_url('admin/login'));
                }
                if ($id != FALSE) {
        
                    $data = array(
                        'admin_title' => lang('app.language_admin_placement_configs'),
                        'admin_heading' => lang('app.language_admin_placement_configs'),
                        'products' => $this->tdsmodel->get_products_placement_setting('cats_core_or_higher'),
                        'active_product' => $this->tdsmodel->get_tds_placement_level_settings(0, $id)
                    );
                } 
                echo view('admin/edit_tds_placement_configs', $data);

            }
            /* Admin tds_placement_institute delete function */
            public function tds_placement_institute_delete()
            {
                if (null !== $this->request->getPost() && $this->request->getPost('institution_id')) {
                    $institution_id = $this->request->getPost('institution_id');
                    if($institution_id != NULL){
                     $tds_placement_details = $this->tdsmodel->delete_tds_placement($institution_id);          
                if ($tds_placement_details) {
                    $this->session->setFlashdata('messages', lang('app.language_admin_placement_configs_success_msg_delete'));
                    echo json_encode(array('success' => 1, 'msg' => 'IP address deleted'));
                    die;
                } else {
                    $this->session->setFlashdata('errors', lang('app.language_admin_placement_configs_success_msg_delete_error'));
                    echo json_encode(array('success' => 0, 'msg' => 'IP address not deleted'));
                    die;
                }
             } 
            }
        }

    /* Admin product menu add function */
    function postproduct() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }

        if (null !== $this->request->getPost('data')) {
            $data['csvdata'] = json_decode($this->request->getPost('data'));
            $level_cats = array(
                'A1.1' => array('alp_id' => 109, 'course_id' => 11),
                'A1.2' => array('alp_id' => 107, 'course_id' => 12),
                'A1.3' => array('alp_id' => 110, 'course_id' => 13),
                'A2.1' => array('alp_id' => 111, 'course_id' => 21),
                'A2.2' => array('alp_id' => 108, 'course_id' => 22),
                'A2.3' => array('alp_id' => 113, 'course_id' => 23),
                'B1.1' => array('alp_id' => 114, 'course_id' => 31),
                'B1.2' => array('alp_id' => 112, 'course_id' => 32),
                'B1.3' => array('alp_id' => 115, 'course_id' => 33),
                'B2.1' => array('alp_id' => 2000, 'course_id' => 41),
                'B2.2' => array('alp_id' => 2002, 'course_id' => 42),
                'B2.3' => array('alp_id' => 2001, 'course_id' => 43),
            );

            if (!empty($data['csvdata'])) {

                foreach ($data['csvdata'] as $csvdata) {

                    $query = $this->db->query('SELECT * FROM products WHERE id ="' . intval($csvdata->id) . '" LIMIT 1');

                    if ($query->getNumRows() > 0) {

                        $csvdata->alp_id = $level_cats[$csvdata->level]['alp_id'];
                        $csvdata->course_id = $level_cats[$csvdata->level]['course_id'];

                        $data = [
                            'id' => $csvdata->id,
                            'name' => $csvdata->name,
                            'level' => $csvdata->level,
                            'progression' => $csvdata->progression,
                            'pgroup' => $csvdata->pgroup,
                            'audience' => $csvdata->audience,
                            'alp_id' => $csvdata->alp_id,
                            'course_id' => $csvdata->course_id,
                        ]; 
                        $productupdate = $this->productmodel->productupdate($csvdata->id,$data);
                       
 
                    } else {

                            $csvdata->alp_id = $level_cats[$csvdata->level]['alp_id'];
                            $csvdata->course_id = $level_cats[$csvdata->level]['course_id'];
                            $data = [
                                'id' => $csvdata->id,
                                'name' => $csvdata->name,
                                'level' => $csvdata->level,
                                'progression' => $csvdata->progression,
                                'pgroup' => $csvdata->pgroup,
                                'audience' => $csvdata->audience,
                                'alp_id' => $csvdata->alp_id,
                                'course_id' => $csvdata->course_id,
                            ]; 
        
                            $productinsert = $this->productmodel->productinsert($data); 
                      
                    }
                }
                $this->session->setFlashdata('messages', lang('app.language_admin_product_added_success_msg'));
                echo json_encode(array('success' => 1, 'msg' => 'product success'));
            } 
            else {
                $this->session->setFlashdata('errors', lang('app.language_admin_product_added_failure_msg'));
                echo json_encode(array('success' => 0, 'msg' => 'product failure'));
            }
        }
    }

    /* Admin download_content_json for primary placement upload function */
    function download_content_json($mode = false) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_placement_configs'),
            'admin_heading' => lang('app.language_admin_placement_configs')
        );
        /* $json = file_get_contents(base_url("/public/adaptive/test.json")); */
        switch ($mode) {
            case 'linear':
                try {
                    $json = @file_get_contents($this->efsfilepath->efs_linear_path . "test.json", FILE_USE_INCLUDE_PATH);
                    $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode(@$json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);
                } catch (Exception $ex) {
                    $this->session->setFlashdata('errors', "Content unavailable");
                    return redirect()->to(site_url('admin/primary_question_upload_form'));
                }

                break;
            default:
                $json = file_get_contents($this->efsfilepath->efs_adaptive_path . "test.json", FILE_USE_INCLUDE_PATH);
                $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);

                break;
        }
        echo view('admin/part_template_header');
        echo "<script type='text/javascript'>
                function hideTd(className){
                    var elements = document.getElementsByClassName(className);
                    for(var i = 0, length = elements.length; i < length; i++) {
                       if( elements[i].textContent == ''){
                          elements[i].style.display = 'none';
                       } 
                    }

                  }
                hideTd('modal-dialog'); 
                //document.getElementByClass('modal-dialog').style.display = 'none';
                document.addEventListener('DOMContentLoaded', function() {
                       document.getElementsByClassName('modal-dialog').style.display = 'none';
}              , true);</script>
            ";
         if ($mode == 'linear') {
            echo '<h1 class="text-center">Preview of linear bank tasks from database.</h1><hr>';
        } else {
            echo '<h1 class="text-center">Preview of tasks from database.</h1><hr>';
        }
        if($mode == 'linear'){
            
            $i = 1;
            foreach ($jsonIterator as $key => $val) {
                echo '<h2 class="text-center">Total questions count : ' . $val['questions'] . '</h2><hr>';
                $screens = $val['screens'];

                foreach ($screens as $k => $v):
                    echo '<p class="text-center"><strong>Screen Id</strong> : <span class="badge">' . $v['screenid'] . '</span><p>';
                    $this->get_part_template($v['screenid'], $v, $mode,"efs_linear_path");
                    echo '<hr>';
                    $i++;
                endforeach;
            }
        }
        
    }

    /* bring view to html */
    /* set template according to PART id */
    public function get_part_template($partgourp, $screens, $from_where ,$linear_path) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
       
        if ($from_where == 'linear') {
                $data['screen'] = $screens;
                $data['linear_path'] = $linear_path;
                switch ($screens['part']) {
                    case '1611':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                     case '1612':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1613':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1614':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1627':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1628':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1629':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                    case '1630':
                        $template = view('admin/linear_skeleton/' . $screens['part'], $data); 
                        break;
                }
                echo $template;
        }else{
            echo 'Non Linear Part';
        }
    }

    /* linear bank upload */
    function primary_question_upload() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
            
        }

        $upload_path = $this->efsfilepath->efs_linear_path;

        $rules =[
            'question_linear_bank' => 'uploaded[question_linear_bank]|ext_in[question_linear_bank,zip]',
        ];

        if (!$this->validate($rules)) {

            $error = array('errors' => lang('app.upload_invalid_filetype'), 'admin_title' => lang('app.language_admin_question_bank_upload'), 'admin_heading' => lang('app.language_admin_question_bank_upload'));
            $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
            return redirect()->to(site_url('admin/primary_question_upload_form'));
            /* echo view('admin/primary_question_upload', $error); */

        } else {
            $file_details= $this->request->getFile("question_linear_bank");

            if ($file_details != NuLL) {
                $newName = $file_details->getName();
                $file_details->move($upload_path, $newName);
                $filepath = "$upload_path"."$newName";
            }
            $uploaded_data = 
            [
                'file_name' => $file_details->getName(),
                'details' => $newName,
                'full_path' => $filepath,
                'orig_name' => $file_details->getClientName(),
            ];

            $this->unzip->allow(array('json', 'svg', 'png', 'html', 'mp3', 'mp4'));

            if (@file_get_contents("zip://".$uploaded_data['full_path']."#test.json") && $this->unzip->extract($uploaded_data['full_path'])) {
                
                /* upload zip details */
                $jsonTestOverallData = file_get_contents($this->efsfilepath->efs_linear_path . "test.json", FILE_USE_INCLUDE_PATH);
                $jsonTestData = @json_decode($jsonTestOverallData);
               
                $data_questions = [
                    'testid' => @$jsonTestData->test->testagaid, 
                    'file_name' =>  $uploaded_data['orig_name'], 
                    'details' => serialize($uploaded_data),
                    'uploaded_at' => strtotime(date('d-m-Y h:i:s')), 
                    'from_where' => 'linear'
                ];
                $builder = $this->db->table('question_bank_details');  
                $insert_details_id = $builder->insert($data_questions); 
          
                /* remove data folder and table */
                $builder = $this->db->table('linear_bank');
                $deletedata= $builder->truncate();
                /* insert linear bank details to DB starts */
                if ($insert_details_id != '') {
                    $json = file_get_contents($this->efsfilepath->efs_linear_path . "test.json", FILE_USE_INCLUDE_PATH);
                    $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);
                    foreach ($jsonIterator as $key => $val) {

                            $builder = $this->db->table('linear_bank');
                            $builder->where('testagaid',$val['testagaid']);
                            $builder->delete();

                            $inscreendata = [
                                'testid' => $val['testid'],
                                'testagaid' => $val['testagaid'],
                                'note' => $val['note'],
                                'questions' => $val['questions'],
                                'level' => $val['level'],
                                'screens' => serialize($val['screens']),
                                'from_where' => 'linear',
                                'date_added' => strtotime(date('Y-m-d'))
                            ];
                            $builder = $this->db->table('linear_bank');  
                            $insert_screeens_id = $builder->insert($inscreendata); 
                    }
                }
                /* insert linear bank details to DB ends */
                $this->session->setFlashdata('messages', lang('app.language_admin_question_upload_success_msg'));
            }else {
                unlink($uploaded_data['full_path']);
                $this->session->setFlashdata('errors', lang('app.language_admin_question_upload_failure_msg'));
            }

            return redirect()->to(site_url('admin/primary_question_upload_form'));
        }
    }
    /* Admin preview_content_json for primary placement upload function */
    public function preview_content_json($mode = false) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_placement_configs'),
            'admin_heading' => lang('app.language_admin_placement_configs')
        );
        switch ($mode) {
             case 'linear':
                try {
                    $json = @file_get_contents($this->efsfilepath->efs_linear_preview_path . "test.json", FILE_USE_INCLUDE_PATH);
                    $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode(@$json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);
                } catch (Exception $ex) {
                    $this->session->setFlashdata('errors', "Content unavailable");
                    return redirect()->to(site_url('admin/primary_question_upload_form'));
                }

                break;
            default:
                $json = file_get_contents(FCPATH . "/public/adaptive_preview/test.json", FILE_USE_INCLUDE_PATH);
                $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::CATCH_GET_CHILD);

                break;
        }
        echo view('admin/part_template_header');
        echo "<script type='text/javascript'>
                function hideTd(className){
                    var elements = document.getElementsByClassName(className);
                    for(var i = 0, length = elements.length; i < length; i++) {
                       if( elements[i].textContent == ''){
                          elements[i].style.display = 'none';
                       } 
                    }

                  }
                hideTd('modal-dialog'); 
                //document.getElementByClass('modal-dialog').style.display = 'none';
                document.addEventListener('DOMContentLoaded', function() {
                       document.getElementsByClassName('modal-dialog').style.display = 'none';
}              , true);</script>
             <style>
                input[type='radio'], input[type='checkbox'] {
                    transform: scale(1.4);
                }
                .radio label, .checkbox label {
                    line-height: 22px;
                }
             </style>";

        if ($mode == 'linear') {
            if ($this->session->get('linear_preview_filepath')) {
                echo '<h1 class="text-center">Preview of linear bank tasks from ' . '"' . $this->session->get('linear_preview_filepath') . '"</h1>';
            } else {
                echo '<h1 class="text-center">Preview of linear bank tasks from upload.</h1><hr>';
            }
        } else {
            echo '<h1 class="text-center">Preview of all tasks from upload</h1><hr>';
        }
        
        if($mode == 'linear'){
            
            $i = 1;
            foreach ($jsonIterator as $key => $val) {
                echo '<h2 class="text-center">Total questions count : ' . $val['questions'] . '</h2><hr>';
                $screens = $val['screens'];

                foreach ($screens as $k => $v):
                    echo '<p class="text-center"><strong>Screen Id</strong> : <span class="badge">' . $v['screenid'] . '</span><p>';
                    $this->get_part_template($v['screenid'], $v, $mode , "efs_linear_preview_path");
                    echo '<hr>';
                    $i++;
                endforeach;
            }
        }
    }
    /* Admin fileUpload function */
    public function fileUpload($type = null) {
        $upload_path =  $this->efsfilepath->efs_linear_preview_path;

        $rules =[
            'question_linear_bank' => 'uploaded[question_linear_bank]|ext_in[question_linear_bank,zip]',
        ];
        if (!$this->validate($rules)) {
            $error = array('errors' => lang('app.upload_invalid_filetype'), 'admin_title' => lang('app.language_admin_question_bank_upload'), 'admin_heading' => lang('app.language_admin_question_bank_upload'));
            $this->session->setFlashdata('errors', lang('app.upload_invalid_filetype'));
            echo json_encode(array('status' => 'failure', 'message' => 'file not supported in linear'));
            exit;
        }else{

        if ($type == 'linear'){
            $file_details = $this->request->getFile('question_linear_bank');

            if ($file_details != NuLL) {
                $newName = $file_details->getClientName();
                $file_details->move($upload_path, $newName);
                $filepath = "$upload_path"."$newName";;
            }
            $data = 
            [
                'file_name' => $file_details->getClientName(),
                'full_path' => $filepath
            ];
            $this->unzip->allow(array('json', 'svg', 'png','mp3','html'));

            if (@file_get_contents("zip://" . $data['full_path'] . "#test.json")) {
                $this->unzip->extract($data['full_path']);
                $this->session->remove('linear_preview_filepath');
                $this->session->set('linear_preview_filepath', $data['file_name']);
                echo json_encode(array('status' => 'success'));
                exit;
            } else {
                    echo json_encode(array('status' => 'failure', 'message' => 'test.json not found in linear'));
                    exit;
                }
         }
      }
    }

       /* Post value to update unit progress */
       public function post_update_unit_progress()
       {
           header('Content-Type: application/json');
           if(!empty($_POST)) {

               $update_for = $this->request->getPost('update_for');
               $user_id = $this->request->getPost('user_id');
               $unit_id = $this->request->getPost('unit_id');
               $level_id = $this->request->getPost('level_id');

               $rules = [
                'update_for' => [
                    'label'  => lang('app.language_admin_unit_progress_update_for'),
                    'rules'  => 'required',
                    ],
                'user_id' => [
                    'label'  => lang('app.language_admin_unit_progress_user_id'),
                    'rules'  => 'required|check_user_id',
                    'errors' => [
                        'check_user_id' => 'User ID must be 10 digit number.'
                    ]
                    ],
			    ];

                if($update_for == 1) {
                    $rules = [
                        'unit_id' => [
                            'label'  => lang('app.language_admin_unit_progress_unit_id'),
                            'rules'  => 'required|numeric',
                            ],
                            'update_for' => [
                                'label'  => lang('app.language_admin_unit_progress_update_for'),
                                'rules'  => 'required',
                                ],
                            'user_id' => [
                                'label'  => lang('app.language_admin_unit_progress_user_id'),
                                'rules'  => 'required|check_user_id',
                                'errors' => [
                                    'check_user_id' => 'User ID must be 10 digit number.'
                                ]
                                ],
                        ];
                }
                if($update_for == 2) {
                    $rules = [
                        'level_id' => [
                            'label'  => lang('app.language_admin_unit_progress_level_id'),
                            'rules'  => 'required|numeric',
                            ],
                            'update_for' => [
                                'label'  => lang('app.language_admin_unit_progress_update_for'),
                                'rules'  => 'required',
                                ],
                            'user_id' => [
                                'label'  => lang('app.language_admin_unit_progress_user_id'),
                                'rules'  => 'required|check_user_id',
                                'errors' => [
                                    'check_user_id' => 'User ID must be 10 digit number.'
                                ]
                                ],
                        ];
                }
              
                if ($this->validate($rules) == FALSE) {
                    $response['success'] = 0;
                    $errors = array(
                        'update_for' => $this->validation->showError('update_for'),
                        'user_id' => $this->validation->showError('user_id'),
                        'unit_id' => $this->validation->showError('unit_id'),
                        'level_id' =>$this->validation->showError('level_id'),
                    );
                    $response['errors'] = $errors;
                    echo json_encode($response);die;
                }

                 else {
                   /* Get user language */
                   $builder = $this->db->table('users');
                   $builder->select('language_id');
                   $builder->where('user_app_id', $user_id);
                   $query = $builder->get();
                   if ($query->getNumRows() > 0) {
                    $language = $query->getRowArray();
                   }else{
                    $language['language_id'] = '';
                    }
                  $dataNeeded = array("token" => "cts47a7264afdwh", "userid" => $user_id, 'language' => $language['language_id']);
                  if($update_for == 1) {
                       $dataNeeded['unitid'] = $unit_id;
                   } else {
                       $dataNeeded['courseid'] = $level_id;
                   }
                   $data_string = json_encode($dataNeeded);
                   $response = $this->http_ws_call_update_unit_progress($data_string);
                   echo json_encode((array)json_decode($response));die;
               }
           }
       }
   
       /* Curl function to get update unit progress */
       function http_ws_call_update_unit_progress($data = FALSE) {
           if ($data != FALSE) {
               $serverurl = $this->oauth->catsurl('update_unit_progress');
               $ch = curl_init();
               curl_setopt($ch, CURLOPT_URL, $serverurl);
               curl_setopt($ch, CURLOPT_POST, 1);
               curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . $data . '');
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               $server_output = curl_exec($ch);
               return $server_output;
           }
       }     
      /* Admin result_display_settings add function */
       public function add_result_display_settings() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');    
        }

        if ($this->request->getPost()) {

            $rules = [
				'lower_threshold' => 'trim|required',
				'passing_threshold' => 'trim|required',
                'message' => 'trim|required'
			];
            
            if (!$this->validate($rules)) {
                $response['success'] = 0;
                $errors = array(
                    'add_lower_threshold' => $this->validation->showError('lower_threshold'),
                    'add_passing_threshold' => $this->validation->showError('passing_threshold'),
                    'add_message' => $this->validation->showError('message')
                );
                
                $response['errors'] = $errors;
                echo json_encode($response);die;
            } else {

                $serialize_data = serialize(array(
                    'A1.1' => $this->request->getPost('A1_1'),
                    'A1.2' => $this->request->getPost('A1_2'),
                    'A1.3' => $this->request->getPost('A1_3'),
                    'A2.1' => $this->request->getPost('A2_1'),
                    'A2.2' => $this->request->getPost('A2_2'),
                    'A2.3' => $this->request->getPost('A2_3'),
                    'B1.1' => $this->request->getPost('B1_1'),
                    'B1.2' => $this->request->getPost('B1_2'),
                    'B1.3' => $this->request->getPost('B1_3'),
                    'B2.1' => $this->request->getPost('B2_1'),
                    'B2.2' => $this->request->getPost('B2_2'),
                    'B2.3' => $this->request->getPost('B2_3'),
                )); 
    
                $current_version = $this->db->query('SELECT version FROM result_display_settings');
                $new_version = (count((array)$current_version->getRowArray()) > 0) ? $current_version->getRowArray()['version'] + 1 : 1;
                $dataResult = [
                    'lower_threshold' => $this->request->getPost('lower_threshold'),
                    'passing_threshold' => $this->request->getPost('passing_threshold'),
                    'logit_values' => $serialize_data,
                    'version' => $new_version
                ];
    
                $builder = $this->db->table('result_display_settings');
                $builder->truncate();

                $builder = $this->db->table('result_display_settings');
                $builder->insert($dataResult);

                $builder = $this->db->table('result_display_settings_all');
                $builder->insert($dataResult);

                $dataResultReason = [
                    'version' => $new_version,
                    'type' => 'final',
                    'course' => 'Core',
                    'message' => $this->request->getPost('message'),
                ];
    
                $builder = $this->db->table('result_display_settings_version_control');
                $builder->insert($dataResultReason);
    
                $this->session->setFlashdata('success',lang('app.language_admin_formal_test_display_settings'));						
                echo json_encode(array('success' => 1, 'msg' => lang('app.language_admin_formal_test_display_settings')));
                die;
            }
        } else {
            $data = array(
                'admin_title' => lang('app.language_admin_add_formal_test_display'),
                'admin_heading' => lang('app.language_admin_add_formal_test_display'),
            );
        }        
        echo view('admin/add_result_display_settings',$data);
    } 
    /* Admin result_display_settings get function */
    public function get_result_display_settings_details() {

        $version = $this->request->getPost('value');
        $results = $this->placementmodel->get_all_result_version_control($version, $type='final', $course='Core', 'result_display_settings_all');
        
        foreach($results as $result) {
            $data['lower_threshold'] = $result->lower_threshold;
            $data['passing_threshold'] = $result->passing_threshold;
            $logit_values = unserialize($result->logit_values);
            foreach($logit_values as $key => $value) {
                $logitArray = explode('.', $key);
                $dataLogit[$logitArray[0].'_'.$logitArray[1]] = $value;
            }
            $data['logit_values'] = $dataLogit;
            $data['message'] = $result->message;
        }
        echo json_encode(array('data' => $data));
    }


    /*
	 Function to get form codes according to the Product levels via AJAX WP-1202
	*/
	public function get_form_codes_cats(){
	    if($this->request->getPost()){
	        if($this->request->getPost('cats_product_id') != ''){
	            $active_form_code_html =  $all_form_code_html = '';
	            $form_codes = $this->productmodel->fetch_formcodes_cats($this->request->getPost('cats_product_id'));
	            
	            if($form_codes != ''){
	                if(isset($form_codes['available_form_codes_cats']) && $form_codes['available_form_codes_cats'] != ''){
	                    foreach($form_codes['available_form_codes_cats'] as $key => $form_code){
	                        $prev_exposure_cnt = ($this->productmodel->get_exposure_count($form_code->test_formid, $this->request->getPost('product_id')) != FALSE) ? $this->productmodel->get_exposure_count($form_code->test_formid, $this->request->getPost('product_id')) : 0;
	                        $class_active = ($key === 0) ? 'active' : '';
	                        $all_form_code_html .= '<li><label><input type="checkbox" data-order="' . $key . '" class="draggable-item ' . $class_active . '" value="' . $form_code->test_formid . '||' . $prev_exposure_cnt .'">' . $form_code->test_formid . ' (' . $prev_exposure_cnt .')</label></li>';
	                    }
	                }
	                if(isset($form_codes['active_form_codes_cats']) && $form_codes['active_form_codes_cats'] != ''){
	                    foreach($form_codes['active_form_codes_cats'] as $key => $form_code){
	                        $prev_exposure_cnt = $form_code->previous_exposure + $form_code->current_exposure;
	                        $class_active = ($key === 0) ? 'active' : '';
	                        $active_form_code_html .= '<li><label><input type="checkbox" data-order="' . $key . '"class=" draggable ' . $class_active . '" value="' . $form_code->form_code . '||' . $prev_exposure_cnt . '">'. $form_code->form_code . ' (' . $prev_exposure_cnt . ')<input type="hidden" name="active_form_codes[]" value="' . $form_code->form_code .'||' . $prev_exposure_cnt . '"/></label></li>';
	                    }
	                }
	            }
	            
	            if($all_form_code_html == '' && $active_form_code_html == ''){
	                $form_codes_data = array('success' => 0, 'msg' => 'Form codes for the selected product is not available');
	                echo json_encode($form_codes_data);
	                die;
	            }else{
	                $form_codes_data = array('success' => 1, 'all_form_code_html' => $all_form_code_html, 'active_form_code_html' => $active_form_code_html, 'number_of_exposure' => $form_codes['number_of_exposure'], 'allocation_rule' => $form_codes['allocation_rule']);
	                echo json_encode($form_codes_data);
	                die;
	            }
	            
	        }
	    }
	}

	/**
	 * Function to save or update the test version allocation rule for CAT's in the system  WP-1202
	 */
	public function set_cats_test_allocation(){
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');    
        }

	    if(null != $this->request->getPost() && $this->request->getPost() != ''){
	        $test_allocation_datas = $this->request->getPost();
	        $product_id = (isset($test_allocation_datas['cats_product_id'])) ? $test_allocation_datas['cats_product_id'] : null;
	        $allocation_rule = (isset($test_allocation_datas['allocation_rule_cats'])) ? $test_allocation_datas['allocation_rule_cats'] : null;
	        $active_form_codes = (isset($test_allocation_datas['active_form_codes'])) ? $test_allocation_datas['active_form_codes'] : null;
	        $exposure_counts = (isset($test_allocation_datas['active_exposure'])) ? $test_allocation_datas['active_exposure'] : null;
	        $number_of_exposure = (isset($test_allocation_datas['number_of_exposure_cats'])) ? $test_allocation_datas['number_of_exposure_cats'] : '';
	        $tds_option = $test_allocation_datas['tds_option'];
	        
            $rules =[
                'number_of_exposure_cats' => [
                  'label'  => lang('app.language_admin_testform_id'),
                  'rules'  => 'check_integer|required',
                  ],
            ];

	        if ($allocation_rule === 'scheduled' && (!$this->validate($rules))) {
	            $this->session->setFlashdata('errors', lang('app.language_admin_no_of_exposure_error_msg'));
                return redirect()->to('admin/cats_test_allocation/'.$product_id);  
	        }
	        
	        if($product_id === null || $allocation_rule === null /* || $active_form_codes == null */){
                return redirect()->to('admin/cats_test_allocation/'.$product_id); 
	        }else{
	            $tds_allocation_data = [
	                'product_id' => $product_id,
	                'allocation_rule' => $allocation_rule,
	                'number_of_exposure' => $number_of_exposure,
	                'tds_option' => $tds_option,
                ];
	            
	            $check_product_exist = $this->productmodel->check_tds_allocation_product_exist($product_id, $tds_option);
	            
	            if($check_product_exist){
	                $tds_allocation_details = $this->productmodel->get_allocation_details($product_id, $tds_option);
	                $tds_allocation_id = $tds_allocation_details[0]->id; //tds allocation id (auto increment id)
	                $flag = FALSE;
	                $tds_active_formcodes = $this->productmodel->get_tds_active_formcodes($product_id, $tds_option);
	                
	                if($active_form_codes != ''){
	                    foreach($active_form_codes as $key => $active_form_code){
	                        $form_code_order = $key;
	                        $active_form_code = explode('||', $active_form_code);
	                        $form_code = $active_form_code[0];
	                        $exposure_count = $active_form_code[1];
	                        $tds_active_formcode_details = $this->productmodel->get_tds_active_formcode_details($form_code, $tds_allocation_id);
	                        
	                        if(in_array($form_code, $tds_active_formcodes)){
	                            $previous_exposure = $tds_active_formcode_details[0]->previous_exposure + $tds_active_formcode_details[0]->current_exposure;
	                            $tds_allocation_formcode_datas  = [
	                                'tds_allocation_id' => $tds_allocation_id,
	                                'form_code' => $form_code,
	                                'form_code_order' => $form_code_order,
	                                'previous_exposure' => $previous_exposure,
	                                'current_exposure' => 0,
	                                'total_exposure' => $number_of_exposure,
	                                'status' => 1,
                                ];
	                            $this->productmodel->update_tds_allocation_form_code($tds_allocation_formcode_datas);
	                            if (($key = array_search($form_code, $tds_active_formcodes)) !== false) {
	                                unset($tds_active_formcodes[$key]);
	                            }
	                        }else{
	                            $tds_allocation_formcode_datas  = [
	                                'tds_allocation_id' => $tds_allocation_id,
	                                'form_code' => $form_code,
	                                'form_code_order' => $form_code_order,
	                                'previous_exposure' => $exposure_count,
	                                'current_exposure' => 0,
	                                'total_exposure' => $number_of_exposure,
	                                'status' => 1,
                                ];
                                $builder = $this->db->table('tds_allocation_formcode');
                                $builder->insert($tds_allocation_formcode_datas);
                            	                        }
	                    }
	                    $flag = TRUE;
	                }
	                
	                if($tds_active_formcodes != ''){
	                    foreach($tds_active_formcodes as $key => $tds_active_formcode){
                            $builder = $this->db->table('tds_allocation_formcode');
	                        $builder->set('tds_allocation_formcode.status', 0);
	                        $builder->where('tds_allocation_formcode.form_code', $tds_active_formcode);
	                        $builder->update();

	                    }
	                }
	                
	                if ($this->productmodel->update_tds_allocation($tds_allocation_data, $tds_option) || $flag = TRUE) {
	                    $this->session->setFlashdata('messages', lang('app.language_admin_test_allocation_updated_success_msg'));
                        return redirect()->to('admin/cats_test_allocation/'.$product_id);
	                }
	            }else{
                    $builder = $builder->table('tds_allocation');
	                if( $builder->insert($tds_allocation_formcode_datas)){
	                    $insert_id = $this->db->insertID();
	                    if($active_form_codes != ''){
	                        foreach($active_form_codes as $key => $active_form_code){
	                            $active_form_code = explode('||', $active_form_code);
	                            $form_code = $active_form_code[0];
	                            $exposure_count = $active_form_code[1];
	                            $tds_allocation_formcode_datas  = [
	                                'tds_allocation_id' => $insert_id,
	                                'form_code' => $form_code,
	                                'form_code_order' => $key,
	                                'previous_exposure' => $exposure_count,
	                                'current_exposure' => 0,
	                                'total_exposure' => $number_of_exposure,
                                ];

                                $builder = $this->db->table('tds_allocation_formcode');
                                $builder->insert($tds_allocation_formcode_datas);

	                        }
	                    }
	                    $this->session->setFlashdata('messages', lang('language_admin_set_test_allocation_success_msg'));
                        return redirect()->to('admin/cats_test_allocation/'.$product_id); 
	                }else{
	                    $this->session->setFlashdata('errors', lang('language_admin_set_test_allocation_error_msg'));
	                    return redirect()->to('admin/cats_test_allocation'); 
	                }
	            }
	        }
	    }
	}

    /* wp-1220 */
    /* Remove incomplete test leaners */
    public function remove_incomplete_test_leaners(){


        if (null !== $this->request->getPost() && $this->request->getPost('thirdpartyIDs')) {

            $thirdpartyIDArray = $this->request->getPost('thirdpartyIDs');
            $this->db->transBegin();
            foreach($thirdpartyIDArray as $third_party_id) {

                $thirdparty_id = $this->encryptinc->decode($third_party_id);
                $builder = $this->db->table('booking');
                $builder->select('id as booking_id, event_id');
                $builder->where('test_delivary_id', $thirdparty_id);
                $query = $builder->get();
                $resultData = $query->getRowArray();
                
                /* plus the capacity of event */
                $builder = $this->db->table('events');
                $builder->where('id',$resultData['event_id']);
                $builder->set('capacity','capacity + 1');
                $builder->update();

                $this->reset_leaner_count($resultData['booking_id']);

                /* remove the user from booking for further booking */
                $builder = $this->db->table('booking');
                $builder->delete(['id' => $resultData['booking_id']]);

                if($thirdparty_id){
                    log_message('error', "Admin - Removed Learner from booking Reset test - " .print_r($thirdparty_id." -event id ".$resultData['event_id'],true));
                    $result_msg = $this->delete_from_tds_tests($thirdparty_id);
                }
            }
            $this->db->transComplete();
            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();
            } else {
                $this->db->transCommit();
                $this->session->setFlashdata('messages', lang('app.language_incomplete_test_user_deleted_success_msg'));
                echo json_encode(array('success' => 1, 'msg' => 'Removed incomplete test leaners'));
                die;
            }
        }

    }

    /* Admin reset_leaner_count get function */
    public function reset_leaner_count($booking_id=FALSE) {
        $instituion_user_id = $this->session->get('user_id');
        $logged_tier1_userid = $this->session->get('logged_tier1_userid');
        if($booking_id) {
            $builder = $this->db->table('booking');
            $builder->select('user_id, product_id');
            $builder->where('id', $booking_id);
            $query = $builder->get();
            $user_results = $query->getRowArray();

            if(!empty($user_results)) {
                if($logged_tier1_userid != '' && isset($logged_tier1_userid)) { 
                    $reset_data_array = [
                        'user_id' => $user_results['user_id'],
                        'product_id' => $user_results['product_id'],
                        'instituition_user_id' => $instituion_user_id,
                        'reset_by_tier' => $logged_tier1_userid
                    ];
                } else {
                    $reset_data_array = [
                        'user_id' => $user_results['user_id'],
                        'product_id' => $user_results['product_id'],
                        'instituition_user_id' => $instituion_user_id
                    ];
                }
                /* Check if user exist in tds_reset_test_learners table */
                $builder = $this->db->table('tds_reset_test_learners');
                $builder->select('*');
                $builder->where($reset_data_array);
                $reset_results =  $builder->get()->getResult();
    
                if(!empty($reset_results)) {
                    /* Increase one count for no_of_reset column */
                    $builder = $this->db->table('tds_reset_test_learners');
                    $builder->set('no_of_reset','no_of_reset + 1');
                    $builder->where($reset_data_array);
                    $builder->update();
                } else {
                    /* Insert new record in tds_reset_test_learners table */
                    $reset_data_array['no_of_reset'] = 1;
                    if($logged_tier1_userid != '' && isset($logged_tier1_userid)) {
                        $reset_data_array['reset_by_tier'] = $logged_tier1_userid;
                    }
                    $builder = $this->db->table('tds_reset_test_learners');
                    $builder->insert($reset_data_array);
                }
            }
        }
        
    }

    /* Admin from_tds_tests delete function */
    public function delete_from_tds_tests($thirdparty_id = FALSE){
            if($thirdparty_id){

               $builder = $this->db->table('tds_tests TT');
               $builder->select('TT.*');
               $builder->join('tds_test_detail TTD', 'TTD.test_formid = TT.test_formid');
               $builder->where('TT.candidate_id', $thirdparty_id);
               $builder->where('TT.test_type','final');
               $builder->where('TT.response_msg', NULL);
               $Query =  $builder->get();;
               $Querygetrow = $Query->getRow();

                if ($Query->getNumRows() > 0) {
                    $testnumber = $Querygetrow->test_formid;
                    $batchid = $Querygetrow->id;
                    if($batchid && $batchid != '0'){
                        $builder = $this->db->table('tds_tests');
                        if ( $builder->delete(['id' => $batchid])) { 
                                     $builder = $this->db->table('tds_allocation_formcode');
                                     $builder->set('total_exposure','total_exposure + 1');
                                     $builder->set('current_exposure','current_exposure - 1');
                                     $builder->where('form_code', $testnumber);
                                     $builder->update();
      
                        }
                    }
                }
            }
        }

        /* Admin Institution add function */
        function postInstitution($id = FALSE) {
            if (!$this->acl_auth->logged_in()) {
                return redirect()->to(site_url('admin/login'));
            }
            if (null !== $this->request->getPost()) {
                $rules = [
                    'institution_types' => 'required'
                ];
                 if (!$this->validate($rules)) {
                    $this->session->setFlashdata('errors', 'The field is required');
                    return redirect()->to(site_url('admin/institution_types'));
                } 
                else {
                     if ($id != FALSE) {
                       if ($this->cmsmodel->institution_types_update($id)) {
                           $this->session->setflashdata('messages', lang('app.language_admin_update_institution'));
                           return redirect()->to(site_url('admin/institution_types'));
                       } else {
                           $this->session->setflashdata('errors', lang('app.language_admin_institution_type_update_failure_msg'));
                           return redirect()->to('admin/institution_types/' . $id); 
                       }
                    } else {
                       if ($this->cmsmodel->insertInstitutionType()) {
                            $this->session->setFlashdata('messages', lang('app.language_admin_institution_insert_success_msg'));
                            return redirect()->to(site_url('admin/institution_types'));
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_admin_institution_insert_failure_msg'));
                            return redirect()->to(site_url('admin/institution_types'));
                        }
                    } 
                }                         
                
            }
        }
    

    /* Admin Reset final testfunction */
    function reset_final_test($id = FALSE)
    {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        
        $institutes = $this->schoolmodel->school_lists();
        $institute_id = "";
        
        if(( $this->request->getGet('institute') != NULL ) || (null != @$_GET['h_institute'])){

            $sess_ins = $this->request->getGet('institute');
            
            if(!empty($sess_ins)){
                $institute_id = $this->request->getGet('institute');
            } elseif(null != @$_GET['h_institute']) {
                $institute_id = @$_GET['h_institute'];
            }
        }

			if(!empty($institute_id)){
				$search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';

                /* pagination */
                $perPage =  10;
                $offset = 0;
                $uri = current_url(true);
                $TotalSegment_array = ($uri->getSegments());
                $admin_reset_segment = array_search('reset_final_test',$TotalSegment_array,true);
                $segment = $admin_reset_segment + 2;
                $pager = "";
                $total = count((array)$this->fetch_incomplete_test_leaners(0, 0, trim($search_item), $institute_id));
                if($total > 10){
                $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
                $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_reset_final_test');
                $offset = $page * $perPage;
                $pager = $this->pager;
                }

                $data = array(
                    'admin_title' => lang('app.language_admin_view_reset_final_test'),
					'admin_heading' => lang('app.language_admin_view_reset_final_test'),
                    'school_lists' => $institutes,
                    'firstclick' => 'nosearch',
                    'reset_results' => $this->fetch_incomplete_test_leaners($perPage, $offset, trim($search_item), $institute_id),
                    'institute_id' => $institute_id,
                    'is_reset_data' => '1',
                    'search_item' => $search_item,
                    'links' => $pager
                );	
            }
        else {
            $data = array(
                'admin_title' => lang('app.language_admin_view_reset_final_test'),
                'admin_heading' => lang('app.language_admin_view_reset_final_test'),
                'school_lists' => $institutes,
                'firstclick' => 'nosearch',
                'reset_results' => '',
                'institute_id' => '',
                'is_reset_data' => '',
                'links' => "",
                'search_item' => '',
                'links' => ""
            );
        } 


        echo view('admin/reset_final_test',$data);
    }

    /* Admin email_senders_settings get function */
    function email_senders_settings() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $data = array(
            'admin_title' => lang('app.language_admin_email_senders_setting'),
            'admin_heading' => lang('app.language_admin_email_senders_setting'),
            'email_categorys' => $this->tdsmodel->get_email_category(),
            'senders' => $this->tdsmodel->get_email_sender_types(),
            'senders_list' => $this->tdsmodel->get_email_sender_list()
        );
        echo view('admin/email_sender_settings',$data);
    }
    /* Admin email_senders_settings update function */
    function email_senders_update() {
        if (null !== $this->request->getPost()) {
            $category_id = $this->request->getPost('category_id');
            $sender_id = $this->request->getPost('sender_id');
            $sender_value = 'sender_value_'.$category_id;
            if(isset($sender_id) && !empty($sender_id)){
                $sender_list_status = $this->tdsmodel->get_email_sender_list_by_category($category_id);
                if(!empty($sender_list_status) && count($sender_list_status) > 0){
                    $data = array('sender_id' => $sender_id);
                    $builder = $this->db->table('email_sender_list');
                    $builder->where('id', $sender_list_status['id']);
                    $builder->update($data); 
                    error_log(date('[Y-m-d H:i:s e]') .'Email_sender_settings_update: Table primary id - '. $sender_list_status['id'] . ' Category_id - ' .$sender_list_status['category_id']. ' Sender_id - ' .$sender_id .PHP_EOL, 3, LOG_FILE_TDS_CRON); 
                }else{
                    $data = array('sender_id' => $sender_id, 'category_id' => $category_id);
                    $builder = $this->db->table('email_sender_list');
                    $builder->insert($data);
                }
                $this->session->setFlashdata('messages', 'Sender updated successfully');
                echo json_encode(array('success' => 1, 'msg' => 'Sender updated successfully'));
            }else{
                $response['success'] = 0;
                $errors = array($sender_value => "<p>The Sender field is required.</p>");
                $response['errors'] = $errors;
                echo json_encode($response);die;  
            }            
        }
   }

   
    /* generate practice test results */
    public function gen_practicetest_result($test_number = false, $thirdPartyId = false ){  
        if($thirdPartyId != false && $test_number != false){
             $query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE session_number = "' .  $test_number . '"  AND thirdparty_id = "' .  $thirdPartyId . '" LIMIT 1');
             if ($query->getNumRows() > 0) {
                $results  = $query->getRowArray();
                $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                /* user and product info */
                $user_app_id = substr($thirdPartyId,0,10);
                $course_id   = substr($thirdPartyId,10,2);
                $attempt_no  =   substr($thirdPartyId,12,2);
                
                /* get part setup */
                $part_setups  = @_part_setup($course_id);
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
                 if($green_or_orange >= 7 ){
                     $messsage_d = lang('app.language_dashboard_practice_test_icon_a');
                 }else{
                     $messsage_d = lang('app.language_dashboard_practice_test_icon_b');
                 }
             }else{
                $messsage_d = 'Not taken';
             }
             return $messsage_d;
         }
     }
    /* get two sections merged array */
    function _get_two_sections($section1, $section2){
        $parse_section_one_data = json_decode($section1);
        $parse_section_two_data = json_decode($section2);
        foreach($parse_section_one_data->item as $items):
            foreach($items->question as $question):
                 if(isset($question->score)):
                     $first_section[] = $question->score;
                 elseif(isset($question->{'@attributes'}->score)):
                     $first_section[] = $question->{'@attributes'}->score;
                 else:    
                 endif;
            endforeach;
        endforeach;
        foreach($parse_section_two_data->item as $items):
            foreach($items->question as $question):
                 if(isset($question->score)):
                     $second_section[] = $question->score;
                 elseif(isset($question->{'@attributes'}->score)):
                     $second_section[] = $question->{'@attributes'}->score;
                 else:    
                 endif;
            endforeach;
        endforeach;
        $merge_two_sections = array_merge($first_section, $second_section);
        array_unshift($merge_two_sections,"");
        unset($merge_two_sections[0]);
       return  $merge_two_sections; 
}

/* Admin cefr_threshold get function fro result processing */
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
        'B1.3',
        'B2.1',
        'B2.2',
        'B2.3'
    );
    $res_score_settings = $this->bookingmodel->result_display_settings($testdelivary_id);
    $base_scores = @unserialize($res_score_settings['logit_values']);
    
    if ($base_scores !== false) {
        if ($score >= $res_score_settings['passing_threshold']) {
            $cal_score = $base_scores[$level] + $score;
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
    } else {
        $cal_level =  $cal_score  = '';
        if(!empty($res_score_settings['logit_values'])){
            $base_scores = json_decode($res_score_settings['logit_values']);
            $cal_level = $base_scores->overall->level;
            $cal_score = $base_scores->overall->score;
        }
        return $cal_level . '-' . $cal_score;
    }
}

	    /* get institution data */
		public function getInstitutionData($institute_id = FALSE)
		{
			$builder = $this->db->table('institution_tiers');
			$builder->select('institution_tiers.*');
			$builder->where('institution_tiers.id', $institute_id);
			return $query = $builder->get()->getRowArray();
		}
    /* wp-1220 */
    /* fetch incomplete test leaners */
    public function fetch_incomplete_test_leaners($limit=FALSE, $start=FALSE, $search_item='', $institutionid = FALSE) {
        $current_utc = @get_current_utc_details();
        $institutionData = $this->getInstitutionData($institutionid);
        /* minus 3 hours from current timestamp */
        $check_event_end_time = $current_utc['current_utc_timestamp'] - (3 * 60 * 60);
        $tz_to = isset($institutionData['timezone']) ? $institutionData['timezone'] : "";
        $builder = $this->db->table('booking as booking');
        $builder->limit($limit, $start);
        $builder->select('booking.id as booking_id, booking.test_delivary_id as thirdparty_id, users.id, users.firstname, users.lastname, users.username, users.email, products.name as level,events.id as event_id, events.start_date_time, events.end_date_time, school_orders.order_type, users.name');
        $builder->join('event_institution as event_institution', 'event_institution.event_id = booking.event_id', 'left');
        $builder->join('institution_tier_users as institution_tier_users', 'institution_tier_users.user_id = event_institution.institution_user_id', 'left');
        $builder->join('events as events', 'events.id = booking.event_id', 'left');
        $builder->join('users as users', 'users.id = booking.user_id', 'left');
        $builder->join('tds_tests as tds_tests', 'tds_tests.candidate_id = booking.test_delivary_id', 'left');
        $builder->join('products as products', 'products.id = booking.product_id', 'left');
        $builder->join('tokens as tokens', 'tokens.thirdparty_id = booking.test_delivary_id', 'left');
        $builder->join('school_orders as school_orders', 'school_orders.id = tokens.school_order_id', 'left');
        $builder->where('tds_tests.status', 0);
        $builder->where('tds_tests.test_type', 'final');
        $builder->where('events.end_date_time <', $check_event_end_time);
        $builder->where('events.tds_option', "catstds");
        $builder->where('institution_tier_users.institutionTierId', $institutionid);
        $builder->where('tds_tests.candidate_id NOT IN (select candidate_id from tds_results)',NULL,FALSE);
        if($search_item != ''){
            $builder->where("IF(`school_orders`.`order_type` = 'under13', `users`.`username` LIKE '%".$search_item."%' OR `users`.`name` LIKE '%".$search_item."%' , 1=1)");
            $builder->where("IF(`school_orders`.`order_type` = '', `users`.`email` LIKE  '%".$search_item."%' OR `users`.`name` LIKE '%".$search_item."%', 1=1)");
        }
        $builder->orderBy('booking.id', 'DESC');
        $builder->groupBy('users.id');
        $results = $builder->get()->getResult();

        $resetTest = array();
        $i = 0;      
        foreach($results as $result) 
        {
            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $result->start_date_time, $result->end_date_time);
           
            $resetTest[$i] = (object)array(
                'id' => $result->id,
                'thirdparty_id' => $result->thirdparty_id,
                'booking_id' => $result->booking_id,
                'event_id' => $result->event_id,
                'name' => $result->name,
                'firstname' => $result->firstname,
                'lastname' => $result->lastname,
                'username' => $result->username,
                'email' => $result->email,
                'level' => $result->level,
                'order_type' => $result->order_type,
                'event_date' => $institution_zone_values['institute_event_date'],
                'start_time' => $institution_zone_values['institute_start_time'],
                'end_time' => $institution_zone_values['institute_end_time']
            );
            $i++;
        }
        return $resetTest;
    }

    /* Admin server cron job function*/
    public function saveJobs($jobs = array()) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login'));
        }
        $output = shell_exec('crontab -l');
        $job = self::stringToArray($output);
        if ($this->request->getPost('first_run') > $this->request->getPost('second_run') || $this->request->getPost('first_run') > $this->request->getPost('third_run')) {
            $this->session->setFlashdata('errors', lang('app.language_admin_first_run_error'));
            return redirect()->to(site_url('admin/getJobs'));
        } elseif ($this->request->getPost('second_run') < $this->request->getPost('first_run') || $this->request->getPost('second_run') > $this->request->getPost('third_run')) {
            $this->session->setFlashdata('errors', lang('app.language_admin_second_run_error'));
            return redirect()->to(site_url('admin/getJobs'));
        } elseif ($this->request->getPost('third_run') < $this->request->getPost('first_run') || $this->request->getPost('third_run') < $this->request->getPost('second_run')) {
            $this->session->setFlashdata('errors', lang('app.language_admin_third_run_error'));
            return redirect()->to(site_url('admin/getJobs'));
        } else {
            $fourth = $this->request->getPost('third_run') + 1;
            $jobs = array(
                '*/1 * * * * php /var/www/html/index.php en admin send_adaptive_data_to_dataware_house',
				'*/1 * * * * php /var/www/html/index.php en admin send_linear_data_to_dataware_house',
                '0 0 * * * php /var/www/html/index.php en admin remove_booking_without_result',
                '0 ' . $this->request->getPost('first_run') . ' * * * php /var/www/html/index.php en admin getTDSBenchmarkResult',
                '0 ' . $this->request->getPost('second_run') . ' * * * php /var/www/html/index.php en admin get_tds_benchmark_result_after_two_hours',
                '0 ' . $this->request->getPost('third_run') . ' * * * php /var/www/html/index.php en admin get_tds_benchmark_result_after_three_hours',
                '0 ' . $fourth . ' * * * php /var/www/html/index.php en admin get_tds_benchmark_result_after_all_hours',
                '*/5 * * * * php /var/www/html/index.php en admin download_results',
                '0 0 * * * php /var/www/html/index.php en admin delete_download_results',
                '*/15 * * * * php /var/www/html/index.php en admin getTDSImmediateBenchmarkResult',
                '0 */2 * * * php /var/www/html/index.php en admin getTDSImmediateBenchmarkResultTwoHours',
                '*/1 * * * * php /var/www/html/index.php en admin send_tds_end_of_test_data_to_dwh',
                '*/1 * * * * php /var/www/html/index.php en admin send_tds_practicetest_data_to_dwh',
                '*/1 * * * * php /var/www/html/index.php en admin send_tds_stepcheck_data_to_dwh',
                '*/1 * * * * php /var/www/html/index.php en admin send_tds_placement_data_to_dwh',
            );

            /* mail status updated */
            $updata = array(
                'status' => ($this->request->getPost('mailto_check')) ? 1 : 0,
                'cron_mailto' => $this->request->getPost('cron_mailto')
            );
            $builder = $this->db->table('cron_mails');
            $builder->where('id', '1');
            $builder->update($updata);

            /* update collegepre url settings */
            $urlupdata = array(
                'url' => trim($this->request->getPost('url'))
            );
            $builder = $this->db->table('collegepre_settings');
            $builder->where('id', '1');
            $builder->update($urlupdata);

            $output = shell_exec('echo "' . self::arrayToString($jobs) . '" | crontab -');
            $this->session->setFlashdata('messages', lang('app.language_admin_job_success_msg'));
            return redirect()->to(site_url('admin/getJobs'));
            /* sudo cat /etc/passwd | sed 's/^\([^:]*\):.*$/sudo crontab -u \1 -l 2>\&1/' | grep -v "no crontab for" | sh */
        }
    }
    /* Admin common result download function */
    public function download_results(){
	   /* Select query from download_results where status is inactive */
       ini_set('max_execution_time', 300); /* 300 seconds = 5 minutes */
        $download_results = $this->cmsmodel->get_results_download(0);
        if($download_results){
            foreach ($download_results as $result) {
                $task_id = $result->id;
                $institution_id = $result->institution_user_id;
                $start_date = $result->start_date;
                $end_date = $result->end_date;
                $file_name = $result->start_date . '_' . $result->end_date;
                error_log(date('[Y-m-d H:i e]') . 'Download_results,  Task id - '. $task_id .', Start date - '.  $start_date .', End date - '. $end_date .', institution id - '. $institution_id .PHP_EOL, 3, LOG_FILE_TDS_CRON);
                if ($result->product_group == 'primary') {
                    $generatepdf = $this->generatePrimaryPdf($institution_id, $start_date, $end_date, $file_name, $task_id);
                }
                if ($result->product_group == 'core') {
                    $generatepdf = $this->generateCorePdf($institution_id, $start_date, $end_date, $file_name, $task_id);
                }
                if ($result->product_group == 'higher') {
                    $generatepdf = $this->generateHigherPdf($institution_id, $start_date, $end_date, $file_name, $task_id);
                }
                if ($result->product_group == 'stepcheck') {
                    $generatepdf = $this->generateBenchmarkPdf($institution_id, $start_date, $end_date, $file_name, $task_id);
                }
            }
        }
        
    }


        /* WP-1197 - bulk pdf download for Primary - Starts */
        public function generatePrimaryPdf($institution_id = FALSE, $start_date = FALSE, $end_date = FALSE, $file_name = FALSE, $task_id = FALSE){
            if($institution_id && $start_date && $end_date && $task_id){
                $primary_results = $this->cmsmodel->get_primary_details($institution_id,$start_date,$end_date);
                if($primary_results){
                    foreach($primary_results as $result){
                        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $result->candidate_id)) {
                            if ($result->section_one != '') {
                                $percentage = @get_primary_results(@$result->section_one, $result->section_two);
                            }
                            /* QR generation - WP-1221 */
                            $qr_code_url = $google_url = '';
                            $qrcode_params = @generateQRCodePath('school', 'primary', $result->candidate_id, false);
                            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                                $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                                $qr_result = json_decode($qrcode);
                                $qr_code_url = $qr_result->qrcode_abs;
                                $google_url = $qr_result->url;
                                error_log(date('[Y-m-d H:i e]') . ' Primary QR '. $qrcode . PHP_EOL, 3, LOG_FILE_PDF);
                            }
                            
                            $level = substr($result->level, 1);
                            @$primary_results[0]->values = $result;
                            @$primary_results[0]->leveltext = _part_setup($level); 
                            $primary_results[0]->percentage = $percentage['percentage'];
                            $router = service('router');
                            $primary_results[0]->u13controller = $router->controllerName(); 
                            $primary_results[0]->qr_url = $qr_code_url;
                            $primary_results[0]->google_url = $google_url;
                            $primary_results[0]->file_name = @$file_name;
                            $primary_results[0]->task_id = $task_id;
                            @generatePrimaryResultsZipPDF($primary_results);
    
                            /* read the files created by dompdf */
                            $this->zip->read_file($this->efs_bulk_download_path.$task_id.'/'.$file_name.'/'.$result->thirdparty_id.'.pdf');	
    
                        } elseif(!empty($result->tds_candidate_id)){
                            if ($result->tds_processed_data != '') {
                                $processed_final_result = (array)json_decode($result->tds_processed_data);
                                $percentage = (array)$processed_final_result['overall'];
                            } 
                            /* QR generation - WP-1221 */
                            $qr_code_url = $google_url = '';
                            $qrcode_params = @generateQRCodePath('school', 'primary', $result->tds_candidate_id, false);
                            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                                $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                                $qr_result = json_decode($qrcode);
                                $qr_code_url = $qr_result->qrcode_abs;
                                $google_url = $qr_result->url;
                                error_log(date('[Y-m-d H:i e]') . ' Primary QR '. $qrcode . PHP_EOL, 3, LOG_FILE_PDF);
                            }
    
                            $tz_to = $this->bookingmodel->get_institution_timezone($result->tds_candidate_id);
                            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $result->start_date_time, $result->end_date_time);
                            $primary_results[0]->date_of_exam = $institution_zone_values['institute_event_date'];
                            
                            $level = substr($result->level, 1);
                            @$primary_results[0]->values = $result;
                            @$primary_results[0]->leveltext = _part_setup($level);
                            $primary_results[0]->percentage = $percentage['percentage'];
                            $router = service('router');
                            $primary_results[0]->u13controller = $router->controllerName();
                            $primary_results[0]->qr_url = $qr_code_url;
                            $primary_results[0]->google_url = $google_url;
                            $primary_results[0]->file_name = @$file_name;
                            $primary_results[0]->task_id = $task_id;
                            @generatePrimaryTdsResultsZipPDF($primary_results);  
                            
                            /* read the files created by dompdf */
                            $this->zip->read_file($this->efs_bulk_download_path.$task_id.'/'.$file_name.'/'.$result->tds_candidate_id.'.pdf');	
                        }
                    }
                    if($this->zip->archive($this->efs_bulk_download_path.$task_id.'/'.$file_name.'.zip')){
                        /* readed files archived into zip */
                        $this->zip->clear_data();
                        delete_files($this->efs_bulk_download_path.$task_id.'/'.$file_name, true); /* delete all files/folders */
                        /* delete and remove folder created by dompdf */
                        rmdir($this->efs_bulk_download_path.$task_id.'/'.$file_name);
                        /* update table after zip file created */
                        $zip_name = $file_name.".zip";
                        $data = array('file_name' => $zip_name, 'status' => 1);

                        $builder = $this->db->table('results_download');
                        $builder->where('id', $task_id);
                        $builder->update($data); 

                        error_log(date('[Y-m-d H:i e]') . ' Primary ZIP created with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                    }else{
                        error_log(date('[Y-m-d H:i e]') . ' Primary ZIP created fails with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                    }
                }
                
            } 
        }


            /* WP-1197 - bulk pdf download for Core - Starts */
        public function generateCorePdf($institution_id = FALSE, $start_date = FALSE, $end_date = FALSE, $file_name = FALSE, $task_id = FALSE){
            if($institution_id && $start_date && $end_date && $task_id){
                $core_results = $this->cmsmodel->get_core_details($institution_id,$start_date,$end_date);
                if($core_results){
                foreach($core_results as $result){
                    if(!empty($result->tds_candidateId)){
                        $this->core_certificate_pdf($result->tds_candidateId,$file_name,$task_id); 
                    }else{
                        $this->core_certificate_pdf($result->candidate_id,$file_name,$task_id);
                    }
                        /* read the files created by dompdf */
                        $this->zip->read_file($this->efs_bulk_download_path.$task_id.'/'.$file_name.'/'.$result->thirdparty_id.'.pdf');
                }
                    if($this->zip->archive($this->efs_bulk_download_path.$task_id.'/'.$file_name.'.zip')){
                        /* readed files archived into zip */
                        
                        $this->zip->clear_data();
                        delete_files($this->efs_bulk_download_path.$task_id.'/'.$file_name, true); /* delete all files/folders */
                        /* delete and remove folder created by dompdf */
                        rmdir($this->efs_bulk_download_path.$task_id.'/'.$file_name);
                        /* update table after zip file created */
                        $zip_name = $file_name.".zip";
                        $data = array('file_name' => $zip_name, 'status' => 1);

                        $builder = $this->db->table('results_download');
                        $builder->where('id', $task_id);
                        $builder->update($data); 
                        error_log(date('[Y-m-d H:i e]') . ' Core ZIP created with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                    }else{
                        error_log(date('[Y-m-d H:i e]') . ' Core ZIP created fails with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                    }
                }
            }
        }

        /* Result pdf generation for user core */
        public function core_certificate_pdf($candidate_id = False, $file_name = False, $task_id = False) {
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
                    
                    /* Generate and save Graph for bulk download */
                    $this->gengraph($thirdPartyId);
                    $chartname = $this->efsfilepath->efs_charts_results . $thirdPartyId. ".png";
                    
                    /* QR generation - WP-1221 */
                    $qr_code_url = $google_url = '';
                    $qrcode_params = @generateQRCodePath('school', 'core', $coreresults['candidate_id'], false);
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
                        'task_id' => $task_id,
                        'file_name' => $file_name,
                        'score' => $score_as_string,
                        'cefr_level' => $cefr_val,
                        'qr_code' => $qr_code_url,
                        'google_url' => $google_url,
                        'chart_name' => $chartname
                    );
                    @generatecoreResultsZipPDF($this->data['values_core_pdf']);
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
                        /* Graph generation for PDF if not avilable - WP-1279 */
                        $chartname = $this->efsfilepath->efs_charts_results . $candidate_id. ".png";
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
                    /* QR generation - WP-1221 */
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
                            'task_id' => $task_id,
                            'file_name' => $file_name,
                            'score' => $score_as_string,
                            'cefr_level' => $cefr_val,
                            'qr_code' => $qr_code_url,
                            'google_url' => $google_url,
                            'chart_name' => $chartname
                        );
                        @generatecoreResultsZipPDF($this->data['values_core_pdf']);  
                    }else{
                        $query = $this->db->query('SELECT passing_threshold FROM `result_display_settings` LIMIT 1');
                        $threshold = $query->getRowArray();
                        $result_status = ($threshold['passing_threshold'] <= (isset($processed_data['total']['score']) ? $processed_data['total']['score'] : ""))? "Pass": "Not achieved the level of the test";
                        /* $result_status = ($threshold['passing_threshold'] <= ($processed_data['total']['score']))? "Pass": "Not achieved the level of the test"; */
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
                        $sp_wr_types= $this->get_level_type(isset($tds_cefrlevel[0]['ability_estimate']) ? $tds_cefrlevel[0]['ability_estimate'] : "", isset($tds_cefrlevel[1]['ability_estimate']) ? $tds_cefrlevel[1]['ability_estimate'] : "",$speaking_ability,$writing_ability); 
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
                            'task_id' => $task_id,
                            'file_name' => $file_name,
                            'score' => $score_as_string,
                            'cefr_level' => $cefr_val,
                            'qr_code' => $qr_code_url,
                            'google_url' => $google_url,
                            /* WP-1319 - Changes to Core results process */
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
                        @generatecoreExtendedResultsZipPDF($this->data['values_core_pdf']);
                    }
                }
            }  
        }

         /* WP-1272 - bulk pdf download for StepCheck - Extend download results options ENDS */
    
         public function gengraph($thirdPartyId = false, $verify_view = false) {
             /* Include the library, for bar graph and QR, so use JpGraph's library - WP-1221 */
            require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph.php');
            require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph_bar.php');

            if ($thirdPartyId != false) {
                $query = $this->db->query('SELECT * FROM  collegepre_results WHERE thirdparty_id = "' . $thirdPartyId . '" LIMIT 1');
                
                if ($query->getNumRows() > 0) {
                    $results = $query->getRowArray();
                    $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
                    
                    /* user and product info */
                    $user_app_id = substr($thirdPartyId, 0, 10);
                    $course_id = substr($thirdPartyId, 10, 2);
                    $attempt_no = substr($thirdPartyId, 12, 2);
                    
                    /* get part setup */
                    $part_setups = _part_setup($course_id);
                    foreach ($part_setups as $part):
                        $labels[] = $part['part'];
                        $scores[] = number_format(array_sum(array_slice($score_sections, $part['start'] - 1, $part['length'], true)) / $part['count'], 2);
                    endforeach;
                    
                    
                    $ydata = $scores; /* [5,4,4,3,5,4,3,3,1,3]; */
                    
                    /* Create the graph. */
                    /* One minute timeout for the cached image */
                    /* INLINE_NO means don't stream it back to the browser. */
                    $graph = new \Graph(898,449, "auto");
                    $graph->SetScale('textint',0,5);
                    $graph->SetMargin(60,20,50,140);
                    $graph->SetFrame(false);
                    $graph->SetBox(false);
                    $graph->graph_theme = null;
                    
                    /* Create a bar pot */
                    $bplot = new \BarPlot($ydata);
                    $bplot->SetWidth(18);
                    $bplot->SetFillColor("#E5E5E5");
                    $bplot->SetWeight(0);
                    
                    $graph->Add($bplot);
                    $array = $labels; /* ["Listening & Reading 1","Listening & Speaking 2","Listening & Writing 3 ","Reading & Speaking 4","Reading & Speaking 5","Reading 6","Reading 7 ","Reading 8","Reading & Writing 9","Reading & Writing 10"]; */
                    
                    /* x-axis components */
                    $graph->xaxis->SetTickLabels($array);
                    $graph->xaxis->SetLabelAngle(60);
                    $graph->xaxis->SetTitle("Skills and Part",'center');
                    $graph->xaxis->SetTitleMargin(110);
                    $graph->xaxis->scale->ticks->SetColor('lightgray');
                    $graph->xaxis->SetColor('#696969');
                    $graph->xaxis->title->SetColor('#666666');
                    
                    /* y-axis components */
                    $graph->ygrid->SetFill(false);
                    $graph->ygrid->SetColor('lightgray');
                    $graph->yaxis->title->Set("Score");
                    $graph->yaxis->scale->ticks->SetColor('lightgray');
                    $graph->yaxis->SetColor('#696969');
                    $graph->yaxis->title->SetColor('#666666');
                    
                    /* Send back the HTML page which will call this script again
                       to retrieve the image.
                       $graph->Stroke(); 
                    */
                    @unlink($this->efsfilepath->efs_charts_results.$thirdPartyId.".png");
                    $graph->Stroke($this->efsfilepath->efs_charts_results.$thirdPartyId.".png");
                    
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
        /* Include the library, for bar graph and QR, so use JpGraph's library - WP-1221 */
        require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph.php');
        require_once (APPPATH.'/Libraries/JpGraph/src/jpgraph_bar.php');

        if ($score_sections != FALSE && $thirdPartyId != FALSE) {
            /* user and product info */
            $user_app_id = substr($thirdPartyId, 0, 10);
            $course_id = substr($thirdPartyId, 10, 2);
            $attempt_no = substr($thirdPartyId, 12, 2);
            
            /* get part setup */
            $part_setups = _part_setup($course_id);
            foreach ($part_setups as $part):
            $labels[] = $part['part'];
            $scores[] = number_format(array_sum(array_slice($score_sections, $part['start'] - 1, $part['length'], true)) / $part['count'], 2);
            endforeach;
            
            $ydata = $scores; /* [5,4,4,3,5,4,3,3,1,3]; */
            /*
                Create the graph.
                One minute timeout for the cached image
                INLINE_NO means don't stream it back to the browser.
            */
            $graph = new \Graph(898,449, "auto");
            $graph->SetScale('textint',0,5);
            $graph->SetMargin(60,20,50,140);
            $graph->SetFrame(false);
            $graph->SetBox(false);
            $graph->graph_theme = null;
            
            /* Create a bar pot */
            $bplot = new \BarPlot($ydata);
            $bplot->SetWidth(18);
            $bplot->SetFillColor("#E5E5E5");
            $bplot->SetWeight(0);
            
            $graph->Add($bplot);
            $array = $labels; /* ["Listening & Reading 1","Listening & Speaking 2","Listening & Writing 3 ","Reading & Speaking 4","Reading & Speaking 5","Reading 6","Reading 7 ","Reading 8","Reading & Writing 9","Reading & Writing 10"]; */
            
            /* x-axis components */
            $graph->xaxis->SetTickLabels($array);
            $graph->xaxis->SetLabelAngle(60);
            $graph->xaxis->SetTitle("Skills and Part",'center');
            $graph->xaxis->SetTitleMargin(110);
            $graph->xaxis->scale->ticks->SetColor('lightgray');
            $graph->xaxis->SetColor('#696969');
            $graph->xaxis->title->SetColor('#666666');
            
            /* y-axis components */
            $graph->ygrid->SetFill(false);
            $graph->ygrid->SetColor('lightgray');
            $graph->yaxis->title->Set("Score");
            $graph->yaxis->scale->ticks->SetColor('lightgray');
            $graph->yaxis->SetColor('#696969');
            $graph->yaxis->title->SetColor('#666666');
            
            /* 
                Send back the HTML page which will call this script again
                to retrieve the image.
                $graph->Stroke();
            */
            @unlink($this->efsfilepath->efs_charts_results.$thirdPartyId.".png");
            $graph->Stroke($this->efsfilepath->efs_charts_results.$thirdPartyId.".png");
        }
    }
        /* Admin get level type function for result processing */
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

         /* WP-1197 - bulk pdf download for HIGHER - Starts */
         public function generateHigherPdf($institution_id = FALSE,$start_date = FALSE, $end_date = FALSE, $file_name = FALSE, $task_id = FALSE){
                if($institution_id && $start_date && $end_date){
                    $higher_results = $this->cmsmodel->get_higher_details($institution_id,$start_date,$end_date);
                    if($higher_results){
                        foreach($higher_results as $result){
                        if($result->tds_candidateId == NULL && $result->tds_token == NULL){;
                            $this->higher_certificate_pdf_helper($result->candidate_id, $token = False, $file_name,$task_id); 
                        }else{
                            $this->higher_certificate_pdf_helper($result->tds_candidateId, $result->tds_token, $file_name,$task_id); 
                        }
                            
                            /* read the files created by dompdf */
                            $this->zip->read_file($this->efs_bulk_download_path.$task_id.'/'.$file_name.'/'.$result->thirdparty_id.'.pdf');
                        }
                            if($this->zip->archive($this->efs_bulk_download_path.$task_id.'/'.$file_name.'.zip')){
                                /* readed files archived into zip */
                                
                                $this->zip->clear_data();
                                delete_files($this->efs_bulk_download_path.$task_id.'/'.$file_name, true); /* delete all files/folders */
                                /* delete and remove folder created by dompdf */
                                rmdir($this->efs_bulk_download_path.$task_id.'/'.$file_name);
                                $zip_name = $file_name.".zip";
                                $data = array('file_name' => $zip_name, 'status' => 1);
                                $builder = $this->db->table('results_download');
                                $builder->where('id', $task_id);
                                $builder->update($data); 
                                error_log(date('[Y-m-d H:i e]') . ' Higher ZIP created with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                            }else{
                                error_log(date('[Y-m-d H:i e]') . ' Higher ZIP created fails with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                            }
                    }       
                }
            }

       /* WP-1191 - 4 skills benchmarking results statement -certificate shown codes - ##ends## */
        public function higher_certificate_pdf_helper($candidate_id = False, $token = False, $file_name = False, $task_id = False) {
            $values_higher_pdf = $this->process_results_higher($candidate_id, $token);
            /* QR generation - WP-1221 */
            $qr_code_url = $google_url = '';
            $qrcode_params = @generateQRCodePath('school', 'higher', $candidate_id, $token, false);
            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                $qrcode_higher = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);
                $qr_result_higher = json_decode($qrcode_higher);
                $qr_code_url = $qr_result_higher->qrcode_abs;
                $google_url = $qr_result_higher->url;
                error_log(date('[Y-m-d H:i e]') . ' Higher QR '. $qrcode_higher . PHP_EOL, 3, LOG_FILE_PDF);
            }
            
            $this->data['pdf_download_higher'] = array(
                'data' => $values_higher_pdf,
                'file_name' => $file_name,
                'task_id' => $task_id,
                'qr_code_url' => $qr_code_url,
                'google_url' => $google_url,
            );
            
            @generatehigherResultsZipPDF($this->data['pdf_download_higher']);
        }
        /* Admin higher_results process function */
         public function process_results_higher($candidate_id = False, $token = False) {
            $result_tds_higher = "";
            if($candidate_id && $token){
                /* tds higher pdf */
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
                /* collegepre higher pdf */
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
        /* Admin higher_results_skill_content process function */
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

        /* WP-1272 - bulk pdf download for StepCheck - Extend download results options Starts */
        public function generateBenchmarkPdf($institution_id = FALSE,$start_date = FALSE, $end_date = FALSE, $file_name = FALSE, $task_id = FALSE){
            if($institution_id && $start_date && $end_date){
                $bench_results = $this->cmsmodel->get_tds_benchmark_results_admin($institution_id,$start_date,$end_date);
                if($bench_results){
                    foreach($bench_results as $result){
                        $this->benchmark_certificate_pdf_helper($result->candidate_id,$result->token,$file_name,$task_id);
                        /* read the files created by dompdf */
                        $this->zip->read_file($this->efs_bulk_download_path.$task_id.'/'.$file_name.'/'.$result->candidate_id.'-'.$result->token.'.pdf');
                    }
                        if($this->zip->archive($this->efs_bulk_download_path.$task_id.'/'.$file_name.'.zip')){
                            /* readed files archived into zip */
                            
                            $this->zip->clear_data();
                            delete_files($this->efs_bulk_download_path.$task_id.'/'.$file_name, true); /* delete all files/folders */
                            /* delete and remove folder created by dompdf */
                            rmdir($this->efs_bulk_download_path.$task_id.'/'.$file_name);
                            $zip_name = $file_name.".zip";
                            $data = array('file_name' => $zip_name, 'status' => 1);
                            $builder = $this->db->table('results_download');
                            $builder->where('id', $task_id);
                            $builder->update($data);
                            error_log(date('[Y-m-d H:i e]') . ' StepCheck ZIP created with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                        }else{
                            error_log(date('[Y-m-d H:i e]') . ' StepCheck ZIP created fails with '. $institution_id . ' ' . $start_date . ' ' . $end_date . ' ' . $file_name . ' ' . $task_id . ' ' . PHP_EOL, 3, LOG_FILE_PDF);
                        }
                }       
            }
        }

            /* WP-1272 - Extend download results options */
        public function benchmark_certificate_pdf_helper($candidate_id = False, $token = False, $file_name = False, $task_id = False) {
            $values_bench_pdf = $this->process_results_benchmark($candidate_id,$token);
            /* QR generation - WP-1221 */
            $qr_code_url = $google_url = '';
            $qrcode_params = @generateQRCodePath('school', 'benchmark', $candidate_id, $token);
            if($qrcode_params['short_url'] != FALSE && strlen($qrcode_params['short_url']) > 0){
                $qrcode = $this->genqrcode($qrcode_params['short_url'], $qrcode_params['file_abs_path']);                
                $qr_result = json_decode($qrcode);
                $qr_code_url = $qr_result->qrcode_abs;
                $google_url = $qr_result->url;
            }
            
            $this->data['pdf_download_results'] = array(
                'data' => $values_bench_pdf,
                'file_name' => $file_name,
                'task_id' => $task_id,
                'qr_code_url' => $qr_code_url,
                'google_url' => $google_url,
            );
            @generatebenchmarkResultsZipPDF($this->data['pdf_download_results']);
        }
       /* Admin benchmark_results_process function */
        public function process_results_benchmark($id = false, $token = false) {
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
          $this->data['pdf_benchmark_results'] = array(
              'id' => $result_users['candidate_id'],
              'user_name' => ucfirst($result_users['name']),
              'result_date' => date("d F Y", $result_users['datetime']),
              'benchmark_tds_data' => $json_to_array,
              'lang_content_level' => $content_array_level,
              'token' => $result_users['token'],
          );
          return $this->data['pdf_benchmark_results'];
        }
        /* Admin benchmark_results_skill_content_process function */
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

        /* Admin bulk download qrcode function */
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

    /** WP-1221
     * Function to delete all downloaded PDF Zip files both Database and Directory  
     */
        public function delete_download_results(){
            /* Select query from download_results where status is 1 */
            $download_results = $this->cmsmodel->get_results_download(1); 
            if($download_results){
                foreach ($download_results as $result) {
                    $created_date = strtotime(date("Y-m-d", strtotime($result->created_on)));
                    $two_days_before_date = strtotime(date("Y-m-d", strtotime("-2 day")));
                    if($created_date < $two_days_before_date){
                        $id = $result->id;
                        if($this->cmsmodel->delete_results_download($id)){
                            $dirpath = $this->efs_bulk_download_path.$id.'/';
                            $this->delete_dir($dirpath);
                        }
                    }
                }
            }
        }

       /**WP-1221
         * Function to delete a file with directory
         * @param string $path
         * @return boolean
         */
        public function delete_dir($path){
            if (is_dir($path) === true){
                $files = array_diff(scandir($path), array('.', '..'));
                foreach ($files as $file){
                    $this->delete_dir(realpath($path) . '/' . $file);
                }
                return rmdir($path);
            }else if (is_file($path) === true){
                return unlink($path);
            }
            return false;
        }
	
	static private function arrayToString($jobs = array()) {
        $string = implode("\r\n", $jobs);
        return $string;
    }
	
    /**WP-1156
    * Function to Save TDS benchmark CRON jobs timming 
    */
    public function save_tds_jobs($jobs = array()) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('admin/login');
        }
        
        $output = shell_exec('crontab -l');
        $job = self::stringToArray($output);
            $jobs = array(
                //'*/5 * * * * php /var/www/html/index.php admin download_results',
                //'0 0 * * * php /var/www/html/index.php admin delete_download_results'
                
            );
            
            $output = shell_exec('echo "' . self::arrayToString($jobs) . '" | crontab -');

            echo "Runned Successfully";
            /* sudo cat /etc/passwd | sed 's/^\([^:]*\):.*$/sudo crontab -u \1 -l 2>\&1/' | grep -v "no crontab for" | sh */
    }

    /* TDS Result processing by 15mins cron */
    function getTDSImmediateBenchmarkResult($task_id= FALSE, $response_request_count= FALSE, $recursive = FALSE, $attempt = FALSE) {
        ini_set('max_execution_time', 300); /* 300 seconds = 5 minutes */
        $check_result_status_count_stepcheck = $check_result_status_count_course = 0 ;
        
        /* Get the result_status count */
        $check_result_status_count_stepcheck = $this->tdsmodel->check_result_status_count_stepcheck();
        $check_result_status_count_course = $this->tdsmodel->check_result_status_count_course();
        $check_result_status_count = $check_result_status_count_stepcheck + $check_result_status_count_course;
        if($check_result_status_count > 0){
            /* Update Attempt number */
            if(!$recursive){
                if($check_result_status_count_stepcheck > 0){
                   $this->tdsmodel->update_attempt_no_stepcheck();
                }
                if($check_result_status_count_course > 0){
                    $this->tdsmodel->update_attempt_no_course();
                }
            }
            /* TDS API calls starts */
            $cd = date('Y-m-d H:i:00');
            $tds_start = strtotime("$cd, -30 minutes");
            $tds_end = strtotime($cd);
            $function_name = 'getTDSImmediateBenchmarkResult';
            $log_file_name = LOG_FILE_TDS;
            $this->tds_result_api_call_by_time($tds_start,$tds_end,$function_name,$log_file_name,$task_id,$response_request_count,$recursive,$attempt);
        }
    }

    /* TDS-349 Result release for one result slower than expected - try to fetch every 2hrs - START */
    function getTDSImmediateBenchmarkResultTwoHours($task_id= FALSE, $response_request_count= FALSE, $recursive = FALSE, $attempt = FALSE) {
        $tds_test_table = $benckmark_table = array();
        
        $current_start_date = strtotime(date('Y-m-d'));
        $current_end_date = strtotime(date('Y-m-d 23:59:59'));

        $builder = $this->db->table('tds_tests TT');
        $builder->select('TT.test_date');
        $builder->join('tds_results TR', 'TR.token = TT.token', 'left');
        $builder->join('tds_placement_results TPR', 'TPR.token = TT.token', 'left');
        $builder->join('tds_practicetest_results TPTR', 'TPTR.token = TT.token', 'left');
        $builder->where('TT.status', 1);
        $builder->where('TT.attempt_no', 2);
        $builder->where('TT.result_status', 0);
        $builder->where('TT.test_date >=', $current_start_date);
        $builder->where('TT.test_date <=', $current_end_date);
        $builder->where('TR.token =', NULL);
        $builder->where('TPR.token =', NULL);
        $builder->where('TPTR.token =', NULL);
        $query = $builder->get();
        $tds_test_table = $query->getResultArray();

        $builder = $this->db->table('benchmark_session BS');
        $builder->select('BS.datetime as test_date');
        $builder->join('tds_benchmark_results TBR', 'TBR.token = BS.token', 'left');
        $builder->where('BS.attempt_no', 2);
        $builder->where('BS.result_status', 0);
        $builder->where('BS.datetime >=', $current_start_date);
        $builder->where('BS.datetime <=', $current_end_date);
        $builder->where('TBR.token =', NULL);
        $query = $builder->get();
        $benckmark_table = $query->getResultArray();
       
        $num_tds_table = count($tds_test_table);
        $num_benckmark_table = count($benckmark_table);
        if($num_tds_table > 0 || $num_benckmark_table > 0){
            $over_all = array_merge($tds_test_table,$benckmark_table);
            $time_array= array_column($over_all, 'test_date');

            $min_time = min($time_array);
            $tds_start = strtotime('-30 minutes',$min_time);

            ini_set('max_execution_time', 300); /* 300 seconds = 5 minutes */
            if($tds_start != NULL){
                $cd = date('Y-m-d H:i:00');
                $tds_end = strtotime($cd);
                
                error_log(date('[Y-m-d H:i:s e]') .'2Hours Cron START Time '. $tds_start . ' END Time ' .$tds_end .PHP_EOL, 3, LOG_FILE_TDS_CRON);
                $function_name = 'getTDSImmediateBenchmarkResultTwoHours';
                $log_file_name = LOG_FILE_TDS_CRON;
                $this->tds_result_api_call_by_time($tds_start,$tds_end,$function_name,$log_file_name,$task_id,$response_request_count,$recursive,$attempt);
            }
        }

    }

    /**WP-1127, WP-1276, WP-1279, APP-5 and APP-6, WP-1301
	 * Function to Process CATs TDS Benchmark Result Placement Core and Higher results process by CRON 
	 * @param integer $attempt
	 * @return boolean
	 */
    function getTDSBenchmarkResult($task_id = FALSE, $response_request_count= FALSE, $recursive = FALSE, $attempt = FALSE) {
        ini_set('max_execution_time', 300); /* 300 seconds = 5 minutes */
        
        $attempt = ($attempt != false) ? $attempt : '';
        
        $tds_start = $this->tds_start;
        $tds_end = $this->tds_end;
        $function_name = 'getTDSBenchmarkResult';
        $log_file_name = LOG_FILE_TDS;
        $this->tds_result_api_call_by_time($tds_start,$tds_end,$function_name,$log_file_name,$task_id,$response_request_count,$recursive,$attempt);
    }

    /* Admin result process call_by_time function */
    function tds_result_api_call_by_time($tds_start,$tds_end,$function_name,$log_file_name,$task_id,$response_request_count,$recursive,$attempt){
        $tds_url = $this->tdsLaunchUrl;
        $tds_testlaunchkey = $this->tdsKey;
        $tds_testreferrer = $this->tdsReferrer;
        $tds_request_result_url = $tds_url . "RequestResults?key=" . $tds_testlaunchkey . "&referrer=" . $tds_testreferrer . "&startdate=" . $tds_start . "&enddate=" . $tds_end;        
        /* Restrict the recursive function call upto 30 times(5 minutes) */
        if($response_request_count != ''){
            if($response_request_count >= 30){
                error_log(date('[Y-m-d H:i:s e] ') . "Success: The Function called recursively by 30 times(5 minutes) with Task id - " . $this->session->get('task_id') . PHP_EOL, 3, $log_file_name);
                exit();
            }
        }
                                                                        
        if ($task_id != '') {
            $reponse_request_data = new \stdClass;
            $reponse_request_data->Status = 'OK';
            $reponse_request_data->TaskId = $task_id;
            $response_request_count = $response_request_count + 1 ;
        } else {
            /* TDS API call-1 based on start and end date */
            $reponse_request_data = (object) $this->http_post_tds($tds_request_result_url);
            $response_request_count = 0;
        }
        if ($reponse_request_data->Status) {
            if (isset($reponse_request_data->TaskId) && $reponse_request_data->TaskId != '') {
                $task_id =  $reponse_request_data->TaskId;
                $this->session->set('task_id', $reponse_request_data->TaskId);
                if($response_request_count == 0) {
                    error_log(date('[Y-m-d H:i:s e] ') . "Success: Task ID was started - $task_id | Start time - $tds_start | End time - $tds_end" . PHP_EOL, 3, $log_file_name);
                }
                $tds_result_status_url = $tds_url . "ResultStatus?key=" . $tds_testlaunchkey . "&taskid=" . $task_id;
                /* TDS API call-2 based on TaskId */
                $reponse_data = (object) $this->http_post_tds($tds_result_status_url);
                if ($reponse_data->Status) {
                    /* conditions based on TDS "ResultsStatus" - 0,16,32,64 */
                    if ($reponse_data->ResultsStatus == 64) {
                        error_log(date('[Y-m-d H:i:s e] ') . "Success: DATA " . serialize($reponse_data) . PHP_EOL, 3, $log_file_name);
                        if (isset($reponse_data->Url) && $reponse_data->Url != '' && $reponse_data->RecordCount > 0) {
                            if (! @copy($reponse_data->Url, $this->efsfilepath->efs_uploads_tds . $task_id . ".zip")) {
                                $errors = error_get_last();
                                $ins_logs = array(
                                 'task_id' => $task_id,
                                 'date_run' => strtotime(date('d-m-Y')),
                                 'time_run' => date('H:i'),
                                 'timezone' => date('e'),
                                 'status' => 0,
                                 'attempt' => ($attempt != '') ? $attempt : '1',
                                 'start_date' => $tds_start,
                                 'end_date' => $tds_end,
                                 'message' => 'The task is completed but ZIP is not copied to server due to TDS zip file or WP folder write permission.'
                                 );
                                 $this->tdsmodel->insert_tds_logs($ins_logs);
                                 error_log(date('[Y-m-d H:i:s e] ') . "Error: The task is completed but ZIP is not copied to server due to TDS zip file or WP folder write permission " . serialize($errors) . PHP_EOL, 3, $log_file_name);
                            } else {
                                $zip = new \ZipArchive();
                                $res = $zip->open($this->efsfilepath->efs_uploads_tds . $task_id . ".zip");
                                if ($res === TRUE) {
                                    if (! $zip->extractTo($this->efsfilepath->efs_uploads_tds . $task_id . "/")) {
                                        $errors = error_get_last();
                                        $ins_logs = array(
                                            'task_id' => $task_id,
                                            'date_run' => strtotime(date('d-m-Y')),
                                            'time_run' => date('H:i'),
                                            'timezone' => date('e'),
                                            'status' => 0,
                                            'attempt' => ($attempt != '') ? $attempt : '1',
                                            'start_date' => $tds_start,
                                            'end_date' => $tds_end,
                                            'message' => 'The task is completed but unable to extract ZIP file.'
                                        );
                                        $this->tdsmodel->insert_tds_logs($ins_logs);
                                        error_log(date('[Y-m-d H:i:s e] ') . "Error: The task is completed but unable to extract a ZIP file" . serialize($errors) . PHP_EOL, 3, $log_file_name);
                                    } else {
                                        /* Entry for tds_tasks table */
                                        $check_tds_tasks = $this->tdsmodel->check_tds_tasks($tds_start, $tds_end);   
                                        if ($check_tds_tasks === FALSE) {
                                            $insdata = array(
                                                'task_id' => $task_id,
                                                'start' => $tds_start,
                                                'end' => $tds_end,
                                                'status' => $reponse_data->ResultsStatus,
                                                'count' => $reponse_data->RecordCount,
                                                'live_uri' => $reponse_data->Url,
                                                'local_uri' => $task_id . '.zip',
                                                'size' => $reponse_data->Size,
                                                'checksum' => $reponse_data->Checksum
                                            );
                                            $builder = $this->db->table('tds_tasks'); 
                                            $builder->insert($insdata);
                                        }
                                        /* Result processing starts */
                                        $local_xml_file = $this->efsfilepath->efs_uploads_tds . $task_id . "/" . $task_id . ".xml";
                                        $xml = simplexml_load_file($local_xml_file, null, LIBXML_NOCDATA) or error_log(date('[Y-m-d H:i:s e] ') . "Success: XML parse error" . PHP_EOL, 3, $log_file_name);
                                        
                                        /* RESULT Processing of placement,practice,final, stepchek mainly in following function */
                                        $this->tds_result_processing($task_id,$xml,$tds_start,$tds_end);

                                        error_log(date('[Y-m-d H:i:s e] ') . "Success: XML received  " . $reponse_data->Url . PHP_EOL, 3, $log_file_name);
                                        if($function_name == "getTDSBenchmarkResult"){
                                            $ins_logs = array(
                                                'task_id' => $task_id,
                                                'date_run' => strtotime(date('d-m-Y')),
                                                'time_run' => date('H:i'),
                                                'timezone' => date('e'),
                                                'status' => 0,
                                                'start_date' => $tds_start,
                                                'end_date' => $tds_end,
                                            );
                                    
                                            $ins1_1_logs = array_merge($ins_logs, array('status' => 1,'attempt' => 1,'message' => 'XML received'));
                                            $ins2_1_logs = array_merge($ins_logs, array('status' => 0,'attempt' => 1,'message' => 'Failure attempt from 1'));
                                            $ins2_2_logs = array_merge($ins_logs, array('status' => 1,'attempt' => 2,'message' => 'XML received'));
                                            $ins3_1_logs = array_merge($ins_logs, array('status' => 0,'attempt' => 1,'message' => 'Failure attempt from 1'));
                                            $ins3_2_logs = array_merge($ins_logs, array('status' => 0,'attempt' => 2,'message' => 'Failure attempt from 2'));
                                            $ins3_3_logs = array_merge($ins_logs, array('status' => 1,'attempt' => 3,'message' => 'XML received'));
                                            
                                            if ($attempt == 2) {       
                                                $this->tdsmodel->insert_tds_logs($ins2_1_logs);
                                                $this->tdsmodel->insert_tds_logs($ins2_2_logs);
                                            } elseif ($attempt == 3) {
                                                $this->tdsmodel->insert_tds_logs($ins3_1_logs);
                                                $this->tdsmodel->insert_tds_logs($ins3_2_logs);
                                                $this->tdsmodel->insert_tds_logs($ins3_3_logs);
                                            } else {
                                                $this->tdsmodel->insert_tds_logs($ins1_1_logs);
                                            }
                                        }
                                        $this->session->remove('task_id');
                                        $this->session->remove('response_request_count');
                                        echo json_encode($reponse_data);
                                    }
                                }else {
                                    $errors = error_get_last();
                                    $ins_logs = array(
                                        'task_id' => $task_id,
                                        'date_run' => strtotime(date('d-m-Y')),
                                        'time_run' => date('H:i'),
                                        'timezone' => date('e'),
                                        'status' => 0,
                                        'attempt' =>  ($attempt != '') ? $attempt : '1',
                                        'start_date' => $tds_start,
                                        'end_date' => $tds_end,
                                        'message' => 'The task is completed but unable to open a ZIP file.'
                                    );
                                    $this->tdsmodel->insert_tds_logs($ins_logs);
                                    error_log(date('[Y-m-d H:i:s e] ') . "Error: The task is completed but unable to open a ZIP file" . serialize($errors) . PHP_EOL, 3, $log_file_name);
                                }
                            }
                        }else {
                            error_log(date('[Y-m-d H:i:s e] ') . "Success: The task is completed but no data (No Zip Url) is received " . PHP_EOL, 3, $log_file_name);
                            $query = $this->db->query("SELECT * FROM tds_logs where status = 1 AND start_date = '" . $tds_start . "' AND end_date = '" . $tds_end . "' LIMIT 1");
                            if ($query->getNumRows() > 0) {
                                error_log(date('[Y-m-d H:i:s e] ') . "Error: Multiple log for the same timestamp " . PHP_EOL, 3, $log_file_name);
                            } else {
                                $ins_logs = array(
                                'task_id' => $task_id,
                                'date_run' => strtotime(date('Y-m-d')),
                                'time_run' => date('H:i'),
                                'timezone' => date('e'),
                                'status' => 1,
                                'attempt' => ($attempt != '') ? $attempt : '1',
                                'start_date' => $tds_start,
                                'end_date' => $tds_end,
                                'message' => 'The task is completed but no data is retrieved'
                                );
                                $this->tdsmodel->insert_tds_logs($ins_logs);
                            }
                        }
                    }elseif ($reponse_data->ResultsStatus == 0) {
                        error_log(date('[Y-m-d H:i:s e] ') . "Error: The task is failed due to ResultsStatus 0" . PHP_EOL, 3, $log_file_name);
                        if($function_name == "getTDSBenchmarkResult"){
                            $this->getTDSBenchmarkResult($task_id, $response_request_count, $recursive,$attempt);
                        }
                    }elseif ($reponse_data->ResultsStatus == 16) {
                        sleep(10);
                        error_log(date('[Y-m-d H:i:s e] ') . "Error: ResultsStatus:16 so trying multiple times" . PHP_EOL, 3, $log_file_name);
                        $this->$function_name($task_id, $response_request_count, $recursive = true,$attempt);     
                    }elseif ($reponse_data->ResultsStatus == 32) {
                           sleep(10);
                           /* time extended to fetch large user count zip files - example more than 400users in 3dates */
                        error_log(date('[Y-m-d H:i:s e] ') . "Success: The task is being processed ResultsStatus:32" . PHP_EOL, 3, $log_file_name);
                        $this->$function_name($task_id, $response_request_count, $recursive = true,$attempt);
                    }
                }else {
                    error_log(date('[Y-m-d H:i:s e] ') . "Error: No Status " . serialize($reponse_data) . " API Send Data " . serialize($tds_request_result_url). PHP_EOL, 3, $log_file_name); 
                    if($function_name == "getTDSBenchmarkResult"){
                        $this->getTDSBenchmarkResult($task_id, $response_request_count, $recursive,$attempt);
                    }  
                }

            } else {
                error_log(date('[Y-m-d H:i:s e] ') . "Error: No Task id,   DATA" . serialize($reponse_request_data). " API Send Data " . serialize($tds_request_result_url). PHP_EOL, 3, $log_file_name);
            }
        }else {
            if($function_name == "getTDSBenchmarkResult"){
                $this->getTDSBenchmarkResult($task_id, $response_request_count, $recursive,$attempt);
            }else{
                error_log(date('[Y-m-d H:i:s e] ') . "Error: RequestResults API fail Data" . serialize($reponse_request_data)  . PHP_EOL, 3, $log_file_name);
            }
            return false;
        }

    }
    
    /**WP-1127
     * Function CRON to get a result from TDS
     * @param string $url
     * @return mixed
     */
    function http_post_tds($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, TRUE);
    }

    /* Admin tds_result_processing function */
    function tds_result_processing($task_id, $xml,$tds_start,$tds_end){
        foreach ($xml->children() as $res) {
            /* Variable declaration */
            $score = $processed_data = $label_score_arr = array();
            $is_stepcheck = $this->tdsmodel->is_stepcheck($res['testformid']);
            if($is_stepcheck){
                $check_tds_stepcheck_test_status = $this->tdsmodel->check_tds_stepcheck_test_status($res['candidate'], $res['token']); //Check Stepcheck/Benchmark test ended successfully
                                                   
                if($check_tds_stepcheck_test_status){
                    $query = $this->db->query('SELECT candidate_id FROM tds_benchmark_results WHERE candidate_id = "' . $res['candidate'] . '"   AND  token = "' . $res['token'] . '" LIMIT 1');
                    if ($query->getNumRows() > 0) {
                        $candidate_xml_update_data = array(
                            'task_id' => $task_id,
                            'testinstance_id' => $res['testinstanceid'],
                            'testform_id' => $res['testformid'],
                            'testform_version' => $res['testformversion'],
                            'candidate_id' => $res['candidate'],
                            'token' => $res['token'],
                            'raw_responses' => json_encode($res->responses),
                            'raw_abilities' => json_encode($res->abilities)
                        );
                        $builder = $this->db->table('tds_benchmark_results');
                        $builder->update($candidate_xml_update_data, array('candidate_id' => $res['candidate'],'token' => $res['token']));
                    } else {
                        if ($res['candidate'] != '' && $this->exists_user_by_candidate_id($res['candidate'])) {
                            $candidate_xml_insert_data = array(
                                'task_id' => $task_id,
                                'testinstance_id' => $res['testinstanceid'],
                                'testform_id' => $res['testformid'],
                                'testform_version' => $res['testformversion'],
                                'candidate_id' => $res['candidate'],
                                'token' => $res['token'],
                                'raw_responses' => json_encode($res->responses),
                                'raw_abilities' => json_encode($res->abilities)
                            );
                            $builder = $this->db->table('tds_benchmark_results');
                            $builder->insert($candidate_xml_insert_data);

                            $score = $this->score_calculation($res->responses, $res->abilities, 'tds', $is_stepcheck->test_type);                            
                            $stepcheck_lookup = $this->tdsmodel->lookup_all_version($score,"stepcheck");                            
                            /* Processed data or Result update */
                            $details = array(
                                'processed_data' => json_encode($score),
                                'lookup_versions' => json_encode($stepcheck_lookup)
                            );
                            /* update stepcheck processed data */
                            $builder = $this->db->table('tds_benchmark_results');
                            $builder->where('candidate_id', $res['candidate']);
                            $builder->where('token', $res['token']);
                            $builder->update($details);
                            
                            /*
                              Placement session update - code removed not in use ci3 
                              Benchmark session update - result status update 
                            */
                            if(isset($score['overall']) && $score['overall'] != ''){
                                $benchmark_session_updata = array(
                                    'benchmark_cefr_level' => $score['overall']['level'],
                                    'result_status' => 1
                                );
                                $builder = $this->db->table('benchmark_session');
                                $builder->where('user_app_id', $res['candidate']);
                                $builder->where('token', $res['token']);
                                $builder->update($benchmark_session_updata);
                            }
                        }
                    }
                }/* Check Stepcheck/Benchmark test ended successfully - END */
            }else{
                $course_type = $this->tdsmodel->get_course_type($res['testformid']);
                if($course_type){
                    $check_tds_test_status = $this->tdsmodel->check_tds_test_status($res['candidate'], $res['token']); /* Check Core, Higher Practice, Final test ended successfully */
                    if($check_tds_test_status){
                        if(count(explode("_", $res['token'])) == 1  && $course_type->test_slug == 'final'){ /* Check Token/Test type Practice or Final or Placement */
                            $results_tablename = 'tds_results';
                        }else{
                            $results_tablename = 'tds_practicetest_results';
                        }
                        
                        $tds_results = $this->tdsmodel->check_tds_results($results_tablename, $res['candidate'], $res['token']);
                        if ($tds_results) {
                            $candidate_xml_update_data = array(
                                'task_id' => $task_id,
                                'testinstance_id' => $res['testinstanceid'],
                                'testform_id' => $res['testformid'],
                                'testform_version' => $res['testformversion'],
                                'candidate_id' => $res['candidate'],
                                'token' => $res['token'],
                                'course_type' => $course_type->course_type,
                                'raw_responses' => json_encode($res->responses)
                            );
                            $where = array(
                                'candidate_id' => $res['candidate'],
                                'token' => $res['token']
                            );
                            $this->tdsmodel->update_tds_results($results_tablename, $candidate_xml_update_data, $where);
                        } else {
                            if ($res['candidate'] != '' && $this->exists_user($res['candidate'])) {
                                $candidate_xml_insert_data = array(
                                    'task_id' => $task_id,
                                    'testinstance_id' => $res['testinstanceid'],
                                    'testform_id' => $res['testformid'],
                                    'testform_version' => $res['testformversion'],
                                    'candidate_id' => $res['candidate'],
                                    'token' => $res['token'],
                                    'course_type' => $course_type->course_type,
                                    'raw_responses' => json_encode($res->responses)
                                );
                                $this->tdsmodel->insert_tds_results($results_tablename, $candidate_xml_insert_data);
                                /* Higher Final Test Calculation */
                                if($course_type->course_type === "Higher"){
                                    /* Score or Performance calculation */
                                    $score = $this->score_calculation($res->responses, $res['testformid'], 'tds', $course_type->course_type ); 
                                    /* lookup column calculation */                               
                                    $higher_lookup = $this->tdsmodel->lookup_all_version($score,"higher",$res['testformid']);
                                    /* Processed data or Result update */
                                    $details = array(
                                        'processed_data' => json_encode($score),
                                        'lookup_versions' => json_encode($higher_lookup)
                                    );
                                    $where = array(
                                        'candidate_id' => $res['candidate'],
                                        'token' => $res['token']
                                    );
                                    $this->tdsmodel->update_tds_results($results_tablename, $details, $where);
                                    
                                    if($course_type->test_slug === "final"){
                                        $details = array(
                                            'logit_values' => json_encode($score),
                                        );
                                        $builder = $this->db->table('booking');
                                        $builder->where('test_delivary_id', $res['candidate']);
                                        $builder->update($details);
                                    }
                                }
                                /* Core Practice and Final Test Calculation */
                                if($course_type->course_type === "Core"){
                                    $level = $course_type->level;
                                   
                                    $min_ability_estimate = $this->tdsmodel->get_min_ability_estimate_by_level($level); //WP-1319
                                    $result_display_settings = $this->placementmodel->get_result_display_settings();
                                    $score_calculation_types = $this->tdsmodel->get_score_calculation_type($res['candidate']);
                                    $tds_skills = $this->tds_separate_skill($res->responses);
                                    
                                    /* Ability calculation using score in CEFR table */
                                    if($tds_skills['reading']['score'] > 0){
                                        $cefr_level_reading = $this->tdsmodel->get_cats_cefr_level_by_score($tds_skills['reading']['score'], "tds");
                                    }elseif($tds_skills['reading']['score'] == 0){
                                        $cefr_level_reading = $this->tdsmodel->get_cats_cefr_level_by_score_zero();
                                    }
                                    
                                    if($tds_skills['listening']['score'] > 0){
                                        $cefr_level_listening = $this->tdsmodel->get_cats_cefr_level_by_score($tds_skills['listening']['score'], "tds");
                                    }elseif($tds_skills['listening']['score'] == 0){
                                        $cefr_level_listening = $this->tdsmodel->get_cats_cefr_level_by_score_zero();
                                    }
                                    
                                    /* WP-1319 */
                                    $extend_score = $this->score_calculation($res->responses, $res['testformid'], 'tds', $course_type->course_type );
                                    
                                    if($course_type->test_slug == 'practice'){
                                        /* WP-1319 Changes to Core results process -- Added speaking and writing(SU, SA) */
                                        if(isset($extend_score['speaking'])){
                                            $scoreArray['speaking'] = $extend_score['speaking'];
                                        }
                                        if(isset($extend_score['writing'])){
                                            $scoreArray['writing'] = $extend_score['writing'];
                                        }
                                        
                                        /* Score Calculation as per Core Final test calculation */
                                        $scoreArray['reading'] = array(
                                            'score' => $tds_skills['reading']['score'],
                                            'outof' => $tds_skills['reading']['question'],
                                            'ability' => $cefr_level_reading[0]->ability_estimate,
                                            'level' => $cefr_level_reading[0]->cefr_level
                                        );
                                        $scoreArray['listening'] = array(
                                            'score' => $tds_skills['listening']['score'],
                                            'outof' => $tds_skills['listening']['question'],
                                            'ability' => $cefr_level_listening[0]->ability_estimate,
                                            'level' => $cefr_level_listening[0]->cefr_level
                                        );
                                        
                                        $total_score = $tds_skills['reading']['score'] + $tds_skills['listening']['score'];
                                        $total_qus = $tds_skills['reading']['question'] + $tds_skills['listening']['question'];
                                        $score_for_lookup = $total_score/$total_qus;
                                        $score_txt = $total_score.'/'.$total_qus;
                                        
                                        $scoreArray['total'] = array(
                                            'score' => $total_score,
                                            'outof' => $total_qus
                                        );
                                        
                                        /* if ($score_calculation_types) { */
                                        $scoreArray['overall'] = $this->get_cefr_tds(2, $level, $total_score, $score_for_lookup, $score_txt, $result_display_settings);
                                        /* } */
                                        
                                        // Score or Performance calculation */
                                        $part_setups  = _part_setup($course_type->course_id);
                                        $green_or_orange = 0;
                                        foreach($part_setups as $part){
                                            $label  = preg_replace("/\d+$/","",$part['part']);
                                            $score  = number_format(array_sum(array_slice($tds_skills['lr_sections'], $part['start']-1,$part['length'],true))/$part['count'], 2);
                                            if($score >= 3){
                                                $green_or_orange =  $green_or_orange + 1;
                                            }
                                            $label_score_arr[] = array( $label, $score, ($score >= 3 ) ? '1' : '0');
                                        }
                                        
                                        /* WP-1319 */
                                        if(isset($extend_score['speaking'])){
                                            $label_score_arr[] = ($scoreArray['speaking']['ability'] >= $min_ability_estimate->ability_estimate) ? array("Speaking", $scoreArray['speaking']['ability'], 1) : array("Speaking", $scoreArray['speaking']['ability'], 0);
                                        }
                                        if(isset($extend_score['writing'])){
                                            $label_score_arr[] = ($scoreArray['writing']['ability'] >= $min_ability_estimate->ability_estimate) ? array("Writing", $scoreArray['writing']['ability'], 1) : array("Writing", $scoreArray['writing']['ability'], 0);
                                        }
                                        
                                        $processed_data['score'] = $scoreArray;
                                        $processed_data['green_or_orange'] = $green_or_orange;
                                        $processed_data['results'] = $label_score_arr;
                                        
                                    }else{
                                        /* WP-1319 Changes to Core results process -- Added speaking and writing(SU, SA) */
                                        if(isset($extend_score['speaking'])){
                                            $score['speaking'] = $extend_score['speaking'];
                                        }
                                        if(isset($extend_score['writing'])){
                                            $score['writing'] = $extend_score['writing'];
                                        }
                                        
                                        $score['reading'] = array(
                                            'score' => $tds_skills['reading']['score'],
                                            'outof' => $tds_skills['reading']['question'],
                                            'ability' => $cefr_level_reading[0]->ability_estimate,
                                            'level' => $cefr_level_reading[0]->cefr_level
                                        );
                                        $score['listening'] = array(
                                            'score' => $tds_skills['listening']['score'],
                                            'outof' => $tds_skills['listening']['question'],
                                            'ability' => $cefr_level_listening[0]->ability_estimate,
                                            'level' => $cefr_level_listening[0]->cefr_level
                                        );
                                        
                                        $total_score = $tds_skills['reading']['score'] + $tds_skills['listening']['score'];
                                        $total_qus = $tds_skills['reading']['question'] + $tds_skills['listening']['question'];
                                        $score_for_lookup = $total_score/$total_qus;
                                        $score_txt = $total_score.'/'.$total_qus;
                                        
                                        $score['total'] = array(
                                            'score' => $total_score,
                                            'outof' => $total_qus
                                        );
                                        
                                        /* Score or Performance calculation */
                                        if ($score_calculation_types) {
                                            $score['overall'] = $this->get_cefr_tds(2, $level, $total_score, $score_for_lookup, $score_txt, $result_display_settings);
                                        }

                                        $processed_data = $score;
                                    }
                                    
                                    /* Tds-365 Reading and listening subrows in core_test_instances - base score added */
                                    if(isset($processed_data) && !empty($processed_data)){
                                        if((array_key_exists("reading", $processed_data) || array_key_exists("listening", $processed_data))){
                                            $tds_base_score = $this->get_base_sources($level,$processed_data);
                                            $processed_data['reading']['clientScale'] = $tds_base_score['reading']['clientScale'];
                                            $processed_data['reading']['ability'] = $tds_base_score['reading']['ability'];
                                            $processed_data['reading']['level'] = $tds_base_score['reading']['level'];

                                            $processed_data['listening']['clientScale'] = $tds_base_score['listening']['clientScale'];
                                            $processed_data['listening']['ability'] = $tds_base_score['listening']['ability'];
                                            $processed_data['listening']['level'] = $tds_base_score['listening']['level'];
                                        }
                                        if($course_type->test_slug === "practice"){
                                            if(array_key_exists("reading", $processed_data['score']) || array_key_exists("listening", $processed_data['score'])){
                                                $tds_base_score = $this->get_base_sources($level,$processed_data['score']);
                                                    $processed_data['score']['reading'] = array(
                                                        'score' => $tds_skills['reading']['score'],
                                                        'outof' => $tds_skills['reading']['question'],
                                                        'clientScale' => $tds_base_score['reading']['clientScale'],
                                                        'ability' => $tds_base_score['reading']['ability'],
                                                        'level' => $tds_base_score['reading']['level']
                                                    );
                                                    $processed_data['score']['listening'] = array(
                                                        'score' => $tds_skills['listening']['score'],
                                                        'outof' => $tds_skills['listening']['question'],
                                                        'clientScale' => $tds_base_score['listening']['clientScale'],
                                                        'ability' => $tds_base_score['listening']['ability'],
                                                        'level' => $tds_base_score['listening']['level']
                                                    );     
                                            }
                                        }    
                                    }
                                    /* Processed data or Result update */
                                    $processed_values = ($course_type->test_slug === "practice") ? $processed_data['score'] : $processed_data;
                                    $core_lookup = $this->tdsmodel->lookup_all_version($processed_values,"core");
                                    if($course_type->test_slug === "final"){
                                        $score_details = array(
                                            'processed_data' => json_encode($processed_data),
                                            'lookup_versions' => json_encode($core_lookup),
                                            'pdf_template_version' => 2
                                        );
                                    }else{
                                        $score_details = array(
                                            'processed_data' => json_encode($processed_data),
                                            'lookup_versions' => json_encode($core_lookup)
                                        );
                                    }
                                    $where = array(
                                        'candidate_id' => $res['candidate'],
                                        'token' => $res['token']
                                    );
                                    $this->tdsmodel->update_tds_results($results_tablename, $score_details, $where);
                                    
                                    if($course_type->test_slug === "final"){
                                        $details = array(
                                            'lower_threshold' => $result_display_settings['lower_threshold'],
                                            'passing_threshold' => $result_display_settings['passing_threshold'],
                                            'logit_values' => $result_display_settings['logit_values'],
                                        );
                                        $builder = $this->db->table('booking');
                                        $builder->where('test_delivary_id', $res['candidate']);
                                        $builder->update($details);
                                    }
                                }
                                /* Primary practice and final percentage calculation */
                                if($course_type->course_type === "Primary"){
                                    $tds_primary_result = $this->get_primary_results_tds($res->responses);
                                    /* Processed data or Result update */
                                    $score_details = array(
                                        'processed_data' => json_encode($tds_primary_result)
                                    );
                                    $where = array(
                                        'candidate_id' => $res['candidate'],
                                        'token' => $res['token']
                                    );
                                    $this->tdsmodel->update_tds_results($results_tablename, $score_details, $where);
                                    /* ci3 booking table - primary final test code no need - so removed */
                                }
                                /* Result status update in tds_tests - common primary,core,higher */
                                $result_status = array(
                                    'result_status' => 1
                                );

                                $builder = $this->db->table('tds_tests');
                                $builder->where('candidate_id', $res['candidate']);
                                $builder->where('token', $res['token']);
                                $builder->update($result_status);
                                /* Primary practice and final percentage calculation ENDS */
                            }
                        }
                    } /* Check Core, Higher Practice, Final test ended successfully - END */
                }else{

                    /*
                        WP-1301 - TDS Placement Test Result processing 
                        Check then Placement test ended successfully
                    */
                    $check_tds_test_status = $this->tdsmodel->check_tds_test_status($res['candidate'], $res['token']);
                    if($check_tds_test_status){
                        $test_type = $this->tdsmodel->get_test_type($res['testformid']);
                        $tds_placement_results = $this->tdsmodel->check_tds_results("tds_placement_results", $res['candidate'], $res['token']);
                        if ($tds_placement_results) {
                            $candidate_xml_update_data = array(
                                'task_id' => $task_id,
                                'testinstance_id' => $res['testinstanceid'],
                                'testform_id' => $res['testformid'],
                                'testform_version' => $res['testformversion'],
                                'candidate_id' => $res['candidate'],
                                'token' => $res['token'],
                                'test_type' => $test_type->test_type,
                                'raw_responses' => json_encode($res->responses),
                                'raw_abilities' => json_encode($res->abilities)
                            );
                            $where = array(
                                'candidate_id' => $res['candidate'],
                                'token' => $res['token']
                            );
                            $this->tdsmodel->update_tds_results("tds_placement_results", $candidate_xml_update_data, $where);
                        } else {
                            if ($res['candidate'] != '' && $this->exists_user_by_candidate_id($res['candidate'])) {
                                $is_reading_ability = FALSE;
                                $raw_ability = $res->abilities;
                                
                                /* Get Adjusted ablity score for Reading */
                                if (is_object($raw_ability) && $raw_ability != '') {
                                    foreach ($raw_ability->ability as $value) {
                                        $value = current($value); //deprecated need to change
                                        if ($value['skill'] === "R") {
                                            $is_reading_ability = TRUE;
                                        }
                                    }
                                }
                                /* Check the Placement result XML have Reading ability or result */
                                if($is_reading_ability){
                                    $this->db->transBegin();
                                    /* Get Minimum and Maximum ablity estimate in CEFR level */
                                    $ability_estimate_in_cats_cefr_level = $this->tdsmodel->get_minmax_ability_estimate_in_cats_cefr_level();
                                    $max_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->max_ability_estimate;
                                    $min_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->min_ability_estimate;
                                    
                                    /* Insert Placement result data */
                                    $candidate_xml_insert_data = array(
                                        'task_id' => $task_id,
                                        'testinstance_id' => $res['testinstanceid'],
                                        'testform_id' => $res['testformid'],
                                        'testform_version' => $res['testformversion'],
                                        'candidate_id' => $res['candidate'],
                                        'token' => $res['token'],
                                        'test_type' => $test_type->test_type,
                                        'raw_responses' => json_encode($res->responses),
                                        'raw_abilities' => json_encode($res->abilities)
                                    );
                                    $this->tdsmodel->insert_tds_results("tds_placement_results", $candidate_xml_insert_data);
                                    
                                    $token_split = explode('_', $res['token']);
                                    $token_details = $this->tdsmodel->get_token_detail($token_split[1], 'placement');
                                    
                                    $raw_ability = $res->abilities;
                                    /* Get Adjusted ablity score for Reading */
                                    if (is_object($raw_ability) && $raw_ability != '') {
                                        foreach ($raw_ability->ability as $value) {
                                            $value = current($value); /* deprecated need to change */
                                            if ($value['skill'] === "R") {
                                                $adjusted_score_ablity_reading = round($value['ability'], 2);
                                            }
                                        }
                                    }
                                    
                                    /* Calculate Reading and Overall Score */
                                    if (isset($adjusted_score_ablity_reading)) {
                                        
                                        $achieved_score_ablity_reading = $adjusted_score_ablity_reading;
                                        
                                        if($adjusted_score_ablity_reading == 0 || $adjusted_score_ablity_reading < $min_ability_estimate_in_cats_cefr_level){
                                            $adjusted_score_ablity_reading = $min_ability_estimate_in_cats_cefr_level;
                                        }
                                        if($adjusted_score_ablity_reading > $max_ability_estimate_in_cats_cefr_level){
                                            $adjusted_score_ablity_reading = $max_ability_estimate_in_cats_cefr_level;
                                        }
                                        
                                        $cefr_level_reading = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_reading, 'tds');
                                        
                                        if ($cefr_level_reading != FALSE) {
                                            $score['achieved_level'] = array(
                                                'score' => $cefr_level_reading[0]->scale,
                                                'level' => $cefr_level_reading[0]->cefr_level,
                                                'ability' => $achieved_score_ablity_reading
                                            );
                                        }
                                        
                                        $test_product = $this->tdsmodel->get_tds_placement_level_settings(1, $token_details->institutionTierId);
                                        if($test_product){ /* Placement test level setting enabled in Admin */
                                            $recommended_level = $test_product['product_level'];
                                            $recommended_product_level = $recommended_level;
                                            $cats_recommended = $this->tdsmodel->get_cats_cefr_score_by_level($recommended_level, "tds");
                                            $recommended_score = $cats_recommended->scale;
                                            $recommended_ablity = $cats_recommended->ability_estimate;
                                        }else{
                                            /* Check Product Eligibility and set Recommended level for learner */
                                            $achieved_level = $score['achieved_level']['level'];
                                            $recommended_score = $score['achieved_level']['score'];
                                            $recommended_ablity = $adjusted_score_ablity_reading; /* $score['achieved_level']['ability']; */
                                            
                                            $eligible_products = $this->tdsmodel->get_eligible_products_by_token_type($token_details->type_of_token);
                                            $cats_core_end_score = $this->tdsmodel->get_cats_cefr_max_score_by_level("B1.3", "tds"); //450 or Stepahead3 level max score in CFER level table
                                            
                                            if($token_details->type_of_token === "cats_core_or_higher"){
                                                /* $recommended_level = (in_array($achieved_level, $eligible_products)) ? $achieved_level : current($eligible_products); */
                                                $recommended_level = $achieved_level;
                                            }elseif($token_details->type_of_token === "cats_higher"){
                                                if($recommended_score >= $cats_core_end_score->scale){
                                                    $recommended_level = $achieved_level;
                                                }else{
                                                    $recommended_level =  current($eligible_products);
                                                }
                                            }else{
                                                $recommended_level = (in_array($achieved_level, $eligible_products)) ? $achieved_level : end($eligible_products);
                                            }
                                            
                                            if ($recommended_score > $cats_core_end_score->scale){
                                                if($achieved_level != $recommended_level){
                                                    $cats_recommended = $this->tdsmodel->get_cats_cefr_max_score_by_level($recommended_level, "tds");
                                                    $recommended_score = $cats_recommended->scale;
                                                    $recommended_ablity = $cats_recommended->ability_estimate;
                                                }
                                                $recommended_product_level = ($token_details->type_of_token != "cats_core") ? "B2.1" : $recommended_level;
                                            }else{
                                                if($achieved_level != $recommended_level){
                                                    $cats_recommended = $this->tdsmodel->get_cats_cefr_score_by_level($recommended_level, "tds");
                                                    $recommended_score = $cats_recommended->scale;
                                                    $recommended_ablity = $cats_recommended->ability_estimate;
                                                }
                                                $recommended_product_level = $recommended_level;
                                            }
                                        }
                                        
                                        $score['reading'] = array(
                                            'score' => $recommended_score,
                                            'level' => $recommended_level,
                                            'ability' => $recommended_ablity
                                        );
                                        
                                        $score['overall'] = array(
                                            'score' => $recommended_score,
                                            'level' => $recommended_level,
                                            'dwh_scale' => $recommended_score,
                                            'dwh_level' => $recommended_level,
                                            'dwh_ability' => $recommended_ablity,
                                        );
                                    }
                                    
                                    /* Processed data/Result, Recommend level, ablility update */
                                    $placement_lookup = $this->tdsmodel->lookup_all_version($score);
                                    $details = array(
                                        'processed_data' => json_encode($score),
                                        'ability' => $achieved_score_ablity_reading,
                                        'recommended_level' => $recommended_product_level,
                                        'lookup_versions' => json_encode($placement_lookup)                                                                            
                                    );
                                    $builder = $this->db->table('tds_placement_results');
                                    $builder->where('candidate_id', $res['candidate']);
                                    $builder->where('token', $res['token']);
                                    $builder->update($details);
                                    /* Generate Thirdparty/Test Delivery id */
                                    $recommended_product = $this->tdsmodel->get_product_details_by_recommended_level($recommended_product_level);
                                    $attempt_results = $this->bookingmodel->get_already_purchased_products($token_details->user_id, $recommended_product->id);
                                    if(!empty($attempt_results)){
                                        $attempt_no = $attempt_results[0]['attempt_no'];
                                        $test_delivary_id = $res['candidate'].$attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
                                    }else{
                                        $no_attempt_results = $this->bookingmodel->get_already_purchased_products(false, $recommended_product->id);
                                        $attempt_no = 1;
                                        $test_delivary_id  = $res['candidate'].$no_attempt_results[0]['course_id'].sprintf("%02d", $attempt_no);
                                    }
                                   
                                    /* Insert Payment details in payments table */
                                    $school_order_details = $this->tdsmodel->get_distributor_id($token_details->school_order_id);
                                    $product_details = $this->tdsmodel->product_price_details($recommended_product->id, $school_order_details->distributor_id);
                                    $distributor_amount = ($product_details->overall_fees * $product_details->distributor_fees)/100 ;
                                    $admin_amount = ($product_details->overall_fees * $product_details->cats_fees)/100 ;
                                    $details = array(
                                        'payment_method' => 'token',
                                        'user_id' => $token_details->user_id,
                                        'distributor_id' => $product_details->distributor_id,
                                        'total_amount' => $distributor_amount + $admin_amount,
                                        'distributor_amount' => $distributor_amount,
                                        'admin_amount' => $admin_amount,
                                        'currency' => 'USD',
                                        'payment_success' => 'success'
                                    );
                                    $payment_detail = $this->bookingmodel->save_payment_details($details);
                                    /* Insert User Product details in user_products table */
                                    $distributor_details = $this->tdsmodel->get_default_distributor_details($product_details->distributor_id);
                                    $detail = array(
                                        'user_id' => $token_details->user_id,
                                        'distributor_id' => $product_details->distributor_id,
                                        'product_id' => $recommended_product->id,
                                        'thirdparty_id' => $test_delivary_id,
                                        'city' => $distributor_details->city,
                                        'country' => $distributor_details->country,
                                        'purchased_date' => @date("Y:m:d h:m:s"),
                                        'payment_id' => $payment_detail,
                                        'payment_done' => 1
                                    );
                                    $this->bookingmodel->save_booking_details($detail);
                                    /* Insert Practice test entries - Practice test entry in Tds_tests based on count */
                                    if($test_delivary_id != ''){
                                        $practice_tests = $this->bookingmodel->get_tds_practice_numbers_by_course_id($recommended_product->id, "Core");
                                        if(count($practice_tests) > 0){
                                            foreach ($practice_tests as $key => $practice_test){
                                                if($practice_test->course_type != 'Higher'){
                                                    $practice_key = $key+1;
                                                    $insData = array('test_formid' => $practice_test->test_formid,'test_formversion' => $practice_test->test_formversion, 'candidate_id' => $test_delivary_id, 'token' => "PT".$practice_key."_".$token_split[1], 'test_type'=> $practice_test->test_type);
                                                    $this->bookingmodel->save_catstds_practice_test($insData);
                                                }
                                            }
                                        }
                                    }
                                    /* Update token status is_used - token table update */
                                    $user_details = $this->tdsmodel->get_user_details($token_details->user_id);
                                    $updata = array(
                                        'user_name' => $user_details->firstname . ' ' . $user_details->lastname,
                                        'product_id' => $recommended_product->id,
                                        'level' => $recommended_product->name,
                                        'thirdparty_id' => $test_delivary_id,
                                        'redeem_payment_id' => $payment_detail,
                                        'is_used' => '1',
                                        'used_time' => time()
                                    );
                                    $builder = $this->db->table('tokens');
                                    $builder->where('token', $token_split[1]);
                                    $builder->update($updata);
                                    /* Final test entries for unsupervised learner - booking table and tds test (final) */
                                    if(!$token_details->is_supervised){
                                        $booking_detail = $this->get_booking_detail($test_delivary_id);
                                        $this->save_booking($booking_detail);
                                    }
                                    /* Result status update in tds_tests */
                                    $result_status = array('result_status' => 1);
                                    $builder = $this->db->table('tds_tests');    
                                    $builder->where('candidate_id', $res['candidate']);
                                    $builder->where('token', $res['token']);
                                    $builder->update($result_status);
                                    
                                    /* to find user under13 or over13 */
                                    $learner_type = $this->tdsmodel->get_learner_type_by_userid($user_details->id);
                                    
                                    /* Commit all queries and send mail to learner if no error */
                                    if ($this->db->transStatus() === FALSE) {
                                        $this->db->transRollback();
                                        $errors = error_get_last();
                                        $ins_logs = array(
                                            'task_id' => $task_id,
                                            'date_run' => strtotime(date('d-m-Y')),
                                            'time_run' => date('H:i'),
                                            'timezone' => date('e'),
                                            'status' => 0,
                                            /* 'attempt' => ($attempt != '') ? $attempt : ($attempt != '') ? $attempt : '1', */
                                            'start_date' => $tds_start,
                                            'end_date' => $tds_end,
                                            'message' => 'Error in placement test result processing and other table entries - All query and entries are rollbacked.'
                                        );
                                        $this->tdsmodel->insert_tds_logs($ins_logs);
                                        error_log(date('[Y-m-d H:i:s e] ') . "Error: Placement test result processing and other table entries. " . serialize($errors) . PHP_EOL, 3, LOG_FILE_TDS);
                                    } else {
                                        $this->db->transCommit();
                                        if($learner_type == "over13"){ /* Email send only for Over13 learner WP-1301 */
                                            $config =  @get_email_config_provider(3);
                                            $app_links = $this->cmsmodel->getmail_applinks($recommended_product_level);
                                            $template_email = $this->emailtemplatemodel->get_template_contents('learner-registration-confirmation', $this->lang->lang());
                                            $template_email_new = $this->email_lib('learner-registration-confirmation');
                                            $label = array("##NAME##", "##ANDROID_LINK##", "##IOS_LINK##");
                                            $email_values = array($user_details->firstname." ". $user_details->lastname, $app_links['0']->app_link, $app_links['1']->app_link);
                                            $replaced_content = str_replace($label, $email_values, $template_email_new);
                                            $mail_message = $replaced_content;
                                            if(isset($config['smtp_user']) && $config['smtp_user'] == "Api-Key:"){
                                                $sendSmtpEmail['subject'] = $template_email['0']->subject;
                                                $sendSmtpEmail['htmlContent'] = $mail_message;
                                                $sendSmtpEmail['sender'] = array('name' => $template_email['0']->display_name, 'email' => $template_email['0']->from_email);
                                                $sendSmtpEmail['to'] = array(array('email' => $user_details->email));
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
                                                $this->email->setFrom($template_email['0']->from_email, $template_email['0']->display_name);
                                                $this->email->setTo($user_details->email);
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
                                            /* log tables affected from here */
                                            $mail_log = array(
                                                'from_address' => $template_email['0']->from_email,
                                                'to_address' => $user_details->email,
                                                'response' => $sent_mail_log,
                                                'status' => $sent_mail_status ? 1 : 0,
                                                'purpose' => $template_email['0']->subject
                                            );
                                            $builder = $this->db->table('email_log'); 
                                            $builder->insert($mail_log);
                                        }
                                    }
                                }/* Check the Placement result XML have Reading ability or result End */
                            }
                        }
                    }
                } 
            }  
        }
    }

    /**WP-1127 * Function to check the user exist in system according to the environment */
    function exists_user_by_candidate_id($candidate_id = FALSE) {
        
        if ($candidate_id != FALSE) {
            if (preg_match("/^[1-9]\d*$/", $candidate_id, $match)) {
                $builder = $this->db->table('users');
                $builder->where('user_app_id', $candidate_id);
                $q = $builder->get();
                if ($q->getNumRows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**WP-1301 * Function to get booking details by thirdparty id to insert Final test details for un supervised user */
    public function get_booking_detail($thirdparty_id = FALSE){
        if($thirdparty_id){
            $query = $this->db->query('SELECT product_id,user_id FROM user_products WHERE thirdparty_id = '.$thirdparty_id);
            $result = $query->getRow();
            if ($query->getNumRows() > 0) {
                $product_id = $result->product_id;
                $user_id = $result->user_id;
                
                /* get attempt number and update the booking */
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
    /* Function to get already_booking data */
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

    /* WP-1301 * Function to save booking entries for un supervised user final test */
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

    /**WP-1301 * Function to insert final test batch entry in tds_test table for un supervised user */
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

    /**WP-1301 Function to get final test form id and form version by course id for un supervised user */
    public function get_finaltest_number_by_course_id($arrData = FALSE){
        $product_id = $arrData['product_id'];
        if($product_id){
            $builder = $this->db->table('tds_allocation');
            $builder->where('tds_allocation.product_id', $product_id);
            $builder->where('tds_allocation.tds_option', 'catstds');
            $alloc_query = $builder->get();
            $result = $alloc_query->getRow();
            if ($alloc_query->getNumRows() > 0) {
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
                    
                    /* Random allocation starts */
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
    /* Function to get token_by_thirdpartyid */
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
    /* Function to get_primary_results_tds */
    public function get_primary_results_tds($raw_responses = FALSE) {
        unset($reading_array);
        unset($listening_array);
        unset($score_array);
        unset($outof_array);
        unset($processed_data);

        if ($raw_responses != FALSE ) {
            if (isset($raw_responses) && !empty($raw_responses)) {
                foreach ($raw_responses->response as $response){
                    foreach ((array)$response as $key => $value){
                        if(isset($value['skill']) &&  $value['skill'] == 'R') {
                            $reading_array['score'][] = $value['score'];
                            $reading_array['outof'][] = $value['outof'];
                        }
                    
                        if(isset($value['skill']) &&  $value['skill'] == 'L') {
                            $listening_array['score'][] = $value['score'];
                            $listening_array['outof'][] = $value['outof'];
                        }
    
                        if(isset($value['score'])) {
                            $score_array[] = $value['score'];
                        }
    
                        if(isset($value['outof'])) {
                            $outof_array[] = $value['outof'];
                        }
                    }      
                }
            }
    
            if (isset($outof_array) && !empty($outof_array) && isset($score_array) && !empty($score_array)) {
                $processed_data = array();
                if(isset($reading_array) && !empty($reading_array)) {
                    $reading_score = array_sum($reading_array['score']);
                    $reading_outof = array_sum($reading_array['outof']);
                    $processed_data['reading'] = array('score' => $reading_score, 'outof' => $reading_outof);
                }
                if(isset($listening_array) && !empty($listening_array)) {
                    $listening_score = array_sum($listening_array['score']);
                    $listening_outof = array_sum($listening_array['outof']);
                    $processed_data['listening'] = array('score' => $listening_score, 'outof' => $listening_outof);
                }
                if(isset($score_array) && !empty($score_array) && isset($outof_array) && !empty($outof_array)) {
                    $overall_score = array_sum($score_array);
                    $overall_outof = array_sum($outof_array);
                    $percentage = number_format(($overall_score / $overall_outof) * 100);
                    $processed_data['overall'] = array('score' => $overall_score, 'outof' => $overall_outof, 'percentage' => $percentage . '%');
                }
            } else {
                $processed_data['overall'] = array('score' => 0, 'outof' => 0, 'percentage' => 0 . '%');
            }
            
            return $processed_data;
        }
    }
    /* Function to check user*/
    function exists_user($thirdparty_id = FALSE){
        if($thirdparty_id != FALSE){
            $user_app_id = substr($thirdparty_id,0,10);
            $builder = $this->db->table('users');
            $builder->where('user_app_id', $user_app_id);
            $q = $builder->get();
            if ($q->getNumRows() > 0) {
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /**WP-1127, WP-1276 * Function to calculate the score for CATs TDS Step check, Higher results process*/
    function score_calculation($responses = FALSE, $type_or_ability = FALSE, $source = FALSE, $course_type = FALSE) {
        if ($responses != FALSE) {
            $score = array();
            $speaking_data = $writing_data = $speaking_weight_setting_array = $writing_weight_setting_array = $collegepre_data = array();
            $speaking_weight_total = $writing_weight_total = $speaking_weight_setting_total = $writing_weight_setting_total = 0;
            $adjusted_score_reading = $adjusted_score_listening = $collegepre_weight_total = $collegepre_weight_setting_total = 0;
            $overall = 0;
            //$outof_write = 10;
            if ($source === 'tds') {
                if($course_type === 'StepCheck'){
                    //Get weight setting for speaking and writing
                    $speaking_weight_setting_array = $this->tdsmodel->get_weight_setting('speaking', $source, strtolower($course_type));
                    $writing_weight_setting_array = $this->tdsmodel->get_weight_setting('writing', $source, strtolower($course_type));
                    
                    $speaking_weight_setting_total = array_sum($speaking_weight_setting_array);
                    $writing_weight_setting_total = array_sum($writing_weight_setting_array);

                    //Get Minimum and Maximum ablity estimate in CEFR level
                    $ability_estimate_in_cats_cefr_level = $this->tdsmodel->get_minmax_ability_estimate_in_cats_cefr_level();
                    $max_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->max_ability_estimate;
                    $min_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->min_ability_estimate;
                    
                    /* Split the Speaking and Writing scores data if available */
                    foreach ($responses->response as $key => $value) {
                        $value = current($value);
                        if ($value['skill'] === "S") {
                            if ($value['outof'] > 0) {
                                $speaking_data[][$value['outof']] = $value['score'];
                            }
                        }
                        if ($value['skill'] === "W") {
                            if ($value['outof'] > 0) {
                                $writing_data[][$value['outof']] = $value['score']/$value['outof'];
                            }
                        }
                    }
                    
                    /* Get Adjusted ablity score for Reading and Listening if availble */
                    if (is_object($type_or_ability) && $type_or_ability != '') {
                        foreach ($type_or_ability->ability as $key => $value) {
                            $value = current($value);
                            if ($value['skill'] === "R") {
                                $adjusted_score_ablity_reading = round($value['ability'], 2);
                            }
                            if ($value['skill'] === "L") {
                                $adjusted_score_ablity_listening = round($value['ability'], 2);
                            }
                        }
                    }
                    
                    /* Process speaking calculation -- (1*0.8 + 1*0.9 + 1*0.7 + 7*0.5) */
                    if ($speaking_data != "" && count($speaking_data) > 0) {
                        foreach ($speaking_data as $key => $value) {
                            if(isset($speaking_weight_setting_array[$key])){
                                $speaking_weight_total = $speaking_weight_total + (current($value) * $speaking_weight_setting_array[$key]);
                               
                            }else{
                                error_log(date('[Y-m-d H:i:s e] ') . "Error: There is a problem Stepcheck specking score calculation - From  - " . date('d-m-Y', strtotime($this->today)) . ' - The data processed for Stepcheck Specking Score calculation is error!' . PHP_EOL, 3, LOG_FILE_TDS);
                                break;
                            }
                        }
                   
                    }
                    
                    /* Process writing calculation -- (10*0.8 + 20*0.9) */
                    if ($writing_data != "" && count($writing_data) > 0) {
                        foreach ($writing_data as $key => $value) {
                            $writing_weight_total = $writing_weight_total + (current($value) * $writing_weight_setting_array[$key]);
                        }
                    }
                    
                    /* Calculate Speaking Score */
                    if($speaking_weight_setting_total != 0 && count($speaking_data) > 0){
                        $speaking_weight = round(($speaking_weight_total / $speaking_weight_setting_total), 2);
                    }
                    
                    if (isset($speaking_weight) && count($speaking_data) > 0) {
                        
                        if(count($speaking_data) > 0 && $speaking_weight == 0){
                            $adjusted_score_ablity_speaking = $min_ability_estimate_in_cats_cefr_level;
                        }else{
                            $adjusted_score_ablity_speaking = $this->tdsmodel->get_adjusted_score_ablity($speaking_weight, 'speaking', strtolower($course_type));
                        }
                        
                        if ($adjusted_score_ablity_speaking != FALSE && $adjusted_score_ablity_speaking > 0) {
                            $cefr_level_speaking = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_speaking, $source);
                            
                            if ($cefr_level_speaking != FALSE) {
                                $score['speaking'] = array(
                                    'score' => $cefr_level_speaking[0]->scale,
                                    'level' => $cefr_level_speaking[0]->cefr_level,
                                    'ability' => $adjusted_score_ablity_speaking
                                );
                            }
                        }else{
                            $cefr_level_speaking = $this->tdsmodel->get_cats_cefr_level($min_ability_estimate_in_cats_cefr_level, $source);
                            
                            if ($cefr_level_speaking != FALSE) {
                                $score['speaking'] = array(
                                    'score' => $cefr_level_speaking[0]->scale,
                                    'level' => $cefr_level_speaking[0]->cefr_level,
                                    'ability' => $min_ability_estimate_in_cats_cefr_level
                                );
                            }
                        }
                    }
                    
                    /* Calculate Writing Score */
                    if($writing_weight_setting_total != 0 && count($writing_data) > 0){
                        $writing_weight = round(($writing_weight_total / $writing_weight_setting_total), 2);
                    }
                    
                    if (isset($writing_weight) && count($writing_data) > 0) {
                        
                        if(count($writing_data) > 0 && $writing_weight == 0){
                            $adjusted_score_ablity_writing = $min_ability_estimate_in_cats_cefr_level;
                        }else{
                            $adjusted_score_ablity_writing = $this->tdsmodel->get_adjusted_score_ablity($writing_weight, 'writing', strtolower($course_type));
                        }
                        
                        if ($adjusted_score_ablity_writing != FALSE && $adjusted_score_ablity_writing > 0) {
                            $cefr_level_writing = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_writing, $source);
                            
                            if ($cefr_level_writing != FALSE) {
                                $score['writing'] = array(
                                    'score' => $cefr_level_writing[0]->scale,
                                    'level' => $cefr_level_writing[0]->cefr_level,
                                    'ability' => $adjusted_score_ablity_writing
                                );
                            }
                        }else{
                            $cefr_level_writing = $this->tdsmodel->get_cats_cefr_level($min_ability_estimate_in_cats_cefr_level, $source);
                            
                            if ($cefr_level_writing != FALSE) {
                                $score['writing'] = array(
                                    'score' => $cefr_level_writing[0]->scale,
                                    'level' => $cefr_level_writing[0]->cefr_level,
                                    'ability' => $min_ability_estimate_in_cats_cefr_level
                                );
                            }
                        }
                    }
                    
                    /* Calculate Reading Score */
                    if (isset($adjusted_score_ablity_reading)) {
                        
                        if($adjusted_score_ablity_reading == 0){
                            $adjusted_score_ablity_reading = $min_ability_estimate_in_cats_cefr_level;
                        }
                        if($adjusted_score_ablity_reading > $max_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_reading = $max_ability_estimate_in_cats_cefr_level;
                        }
                        
                        $cefr_level_reading = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_reading, $source);
                        
                        if ($cefr_level_reading != FALSE) {
                            $score['reading'] = array(
                                'score' => $cefr_level_reading[0]->scale,
                                'level' => $cefr_level_reading[0]->cefr_level,
                                'ability' => $adjusted_score_ablity_reading
                            );
                        }
                    }
                    
                    /* Calculate Listening Score */
                    if (isset($adjusted_score_ablity_listening)) {
                        
                        if($adjusted_score_ablity_listening == 0){
                            $adjusted_score_ablity_listening = $min_ability_estimate_in_cats_cefr_level;
                        }
                        if($adjusted_score_ablity_listening > $max_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_listening = $max_ability_estimate_in_cats_cefr_level;
                        }
                        
                        $cefr_level_listening = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_listening, $source);
                        
                        if ($cefr_level_listening != FALSE) {
                            $score['listening'] = array(
                                'score' => $cefr_level_listening[0]->scale,
                                'level' => $cefr_level_listening[0]->cefr_level,
                                'ability' => $adjusted_score_ablity_listening
                            );
                        }
                    }

                    foreach($score as $v){
                   
                        $overall = $overall + $v['score'];
                    }
                    
                    if(count($score) != 0){
                        $overall_score = round(($overall / count($score)));
                    }
                    
                    /* Calculate Over all Score */
                    if (isset($overall_score)) {
                        if($overall_score == 0){
                            $cefr_level_overall_zero = $this->tdsmodel->get_cats_cefr_level_by_score_zero();
                            $tds_scale = 0;
                            $tds_level = 'A1.1';
                            $tds_ability = $cefr_level_overall_zero[0]->ability_estimate;
                        }else{
                            $cefr_level_overall = $this->tdsmodel->get_cats_cefr_level_by_score($overall_score, $source);
                            if ($cefr_level_overall != FALSE) {
                                $tds_scale = $cefr_level_overall[0]->scale;
                                $tds_level = $cefr_level_overall[0]->cefr_level;
                                $tds_ability = $cefr_level_overall[0]->ability_estimate;
                            }
                        }
                        $score['overall'] = array(
                            'score' => $tds_scale,
                            'level' => $tds_level,
                            'dwh_scale' => $tds_scale,
                            'dwh_level' => $tds_level,
                            'dwh_ability' => $tds_ability,
                        );
                    }
                    
                }else{
                    $exam_type = '';
                    $form_code = $type_or_ability;
                    
                    $speaking_weight_setting_array = $this->tdsmodel->get_weight_setting('speaking', $source, strtolower($course_type));
                    $writing_weight_setting_array = $this->tdsmodel->get_weight_setting('writing', $source, strtolower($course_type));
                    $speaking_weight_setting_total = array_sum($speaking_weight_setting_array);
                    $writing_weight_setting_total = array_sum($writing_weight_setting_array);
                    
                    /* Get Minimum and Maximum ablity estimate in CEFR level */
                    $ability_estimate_in_cats_cefr_level = $this->tdsmodel->get_minmax_ability_estimate_in_cats_cefr_level();
                    $max_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->max_ability_estimate;
                    $min_ability_estimate_in_cats_cefr_level = $ability_estimate_in_cats_cefr_level->min_ability_estimate;
                    
                    foreach ($responses->response as $key => $value) {
                        $value = current($value);
                        
                        if ($value['skill'] === "S") {
                            if ($value['outof'] > 0) {
                                $speaking_data[][$value['outof']] = $value['score'];
                            }
                        }
                        if ($value['skill'] === "W") {
                            if ($value['outof'] > 0) {
                                $writing_data[][$value['outof']] = $value['score']/$value['outof'];
                            }
                        }
                        
                        if ($value['skill'] === "R") {
                            $adjusted_score_reading = $adjusted_score_reading + $value['score'];
                        }
                        
                        if ($value['skill'] === "L") {
                            $adjusted_score_listening = $adjusted_score_listening + $value['score'];
                        }
                    }
                    
                    /* Process speaking calculation -- (1*0.8 + 1*0.9 + 1*0.7 + 7*0.5) */
                    if ($speaking_data != "" && count($speaking_data) > 0) {
                        foreach ($speaking_data as $key => $value) {

                            if(isset($speaking_weight_setting_array[$key])){
                                $speaking_weight_total = $speaking_weight_total + (current($value) * $speaking_weight_setting_array[$key]);
                            }else{
                                error_log(date('[Y-m-d H:i:s e] ') . "Error: There is a problem Step Higher specking score calculation - From  - " . date('d-m-Y', strtotime($this->today)) . ' - The data processed for Step Higher Specking Score calculation is error!' . PHP_EOL, 3, LOG_FILE_TDS);
                                break;
                            }


                        }
                    }
                    
                    /* Process writing calculation -- (10*0.8 + 20*0.9) */
                    if ($writing_data != "" && count($writing_data) > 0) {
                        foreach ($writing_data as $key => $value) {
                            $writing_weight_total = $writing_weight_total + (current($value) * $writing_weight_setting_array[$key]);
                        }
                    }
                    
                    /* Calculate Speaking Score */
                    if($speaking_weight_setting_total != 0 && count($speaking_data) > 0){
                        $speaking_weight = round(($speaking_weight_total / $speaking_weight_setting_total), 2);
                    }
                   
                    if (isset($speaking_weight) && count($speaking_data) > 0) {
                        
                        if(count($speaking_data) > 0 && $speaking_weight == 0){
                            $adjusted_score_ablity_speaking = $min_ability_estimate_in_cats_cefr_level;
                        }else{
                            $adjusted_score_ablity_speaking = $this->tdsmodel->get_adjusted_score_ablity($speaking_weight, 'speaking', strtolower($course_type));
                        }  
                        
                        if ($adjusted_score_ablity_speaking != FALSE && $adjusted_score_ablity_speaking > 0) {
                            $cefr_level_speaking = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_speaking, $source);
                            
                            if ($cefr_level_speaking != FALSE) {
                                $score['speaking'] = array(
                                    'score' => $cefr_level_speaking[0]->scale,
                                    'level' => $cefr_level_speaking[0]->cefr_level,
                                    'ability' => $adjusted_score_ablity_speaking
                                );
                            }
                        }else{
                            $cefr_level_speaking = $this->tdsmodel->get_cats_cefr_level($min_ability_estimate_in_cats_cefr_level, $source);
                            
                            if ($cefr_level_speaking != FALSE) {
                                $score['speaking'] = array(
                                    'score' => $cefr_level_speaking[0]->scale,
                                    'level' => $cefr_level_speaking[0]->cefr_level,
                                    'ability' => $min_ability_estimate_in_cats_cefr_level
                                );
                            }
                        }
                    }
                    
                    /* Calculate Writing Score */
                    if($writing_weight_setting_total != 0 && count($writing_data) > 0){
                        $writing_weight = round(($writing_weight_total / $writing_weight_setting_total), 2);
                    }
                    
                    if (isset($writing_weight) && count($writing_data) > 0) {
                        
                        if(count($writing_data) > 0 && $writing_weight == 0){
                            $adjusted_score_ablity_writing = $min_ability_estimate_in_cats_cefr_level;
                        }else{
                            $adjusted_score_ablity_writing = $this->tdsmodel->get_adjusted_score_ablity($writing_weight, 'writing', strtolower($course_type));
                        }
                        
                        if ($adjusted_score_ablity_writing != FALSE && $adjusted_score_ablity_writing > 0) {
                            $cefr_level_writing = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_writing, $source);
                            
                            if ($cefr_level_writing != FALSE) {
                                $score['writing'] = array(
                                    'score' => $cefr_level_writing[0]->scale,
                                    'level' => $cefr_level_writing[0]->cefr_level,
                                    'ability' => $adjusted_score_ablity_writing
                                );
                            }
                        }else{
                            $cefr_level_writing = $this->tdsmodel->get_cats_cefr_level($min_ability_estimate_in_cats_cefr_level, $source);
                            
                            if ($cefr_level_writing != FALSE) {
                                $score['writing'] = array(
                                    'score' => $cefr_level_writing[0]->scale,
                                    'level' => $cefr_level_writing[0]->cefr_level,
                                    'ability' => $min_ability_estimate_in_cats_cefr_level
                                );
                            }
                        }
                    }
                    
                    /* Calculate Reading Score */
                    if (isset($adjusted_score_reading)) {
                        
                        $adjusted_score_ablity_reading = $this->tdsmodel->get_adjusted_score_ablity_by_formcode($adjusted_score_reading, 'reading', $form_code, $source, 'higher');
                        
                        if($adjusted_score_ablity_reading < $min_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_reading = $min_ability_estimate_in_cats_cefr_level;
                        }elseif ($adjusted_score_ablity_reading > $max_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_reading = $max_ability_estimate_in_cats_cefr_level;
                        }

                        $cefr_level_reading = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_reading, $source);
                        if ($cefr_level_reading != FALSE) {
                            $score['reading'] = array(
                                'score' => $cefr_level_reading[0]->scale,
                                'level' => $cefr_level_reading[0]->cefr_level,
                                'ability' => $adjusted_score_ablity_reading
                            );
                        }
                    }
                    
                    /* Calculate Listening Score */
                    if (isset($adjusted_score_listening)) {
                        
                        $adjusted_score_ablity_listening = $this->tdsmodel->get_adjusted_score_ablity_by_formcode($adjusted_score_listening, 'listening', $form_code, $source, 'higher');
                        
                        if($adjusted_score_ablity_listening < $min_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_listening = $min_ability_estimate_in_cats_cefr_level;
                        }elseif ($adjusted_score_ablity_listening > $max_ability_estimate_in_cats_cefr_level){
                            $adjusted_score_ablity_listening = $max_ability_estimate_in_cats_cefr_level;
                        }
                        
                        $cefr_level_listening = $this->tdsmodel->get_cats_cefr_level($adjusted_score_ablity_listening, $source);
                        
                        if ($cefr_level_listening != FALSE) {
                            $score['listening'] = array(
                                'score' => $cefr_level_listening[0]->scale,
                                'level' => $cefr_level_listening[0]->cefr_level,
                                'ability' => $adjusted_score_ablity_listening
                            );
                        }
                    }
                    
                    foreach($score as $v){
                        $overall = $overall + $v['score'];
                    }
                    
                    if(count($score) != 0){
                        $overall_score = round(($overall / count($score)));
                    }
                    
                    /* Calculate Over all Score */
                    if (isset($overall_score)) {
                        if($overall_score == 0){
                            $cefr_level_overall_zero = $this->tdsmodel->get_cats_cefr_level_by_score_zero();
                            $tds_scale = 0;
                            $tds_level = 'A1.1';
                            $tds_ability = $cefr_level_overall_zero[0]->ability_estimate;
                        }else{
                            $cefr_level_overall = $this->tdsmodel->get_cats_cefr_level_by_score($overall_score, $source);
                            if ($cefr_level_overall != FALSE) {
                                $tds_scale = $cefr_level_overall[0]->scale;
                                $tds_level = $cefr_level_overall[0]->cefr_level;
                                $tds_ability = $cefr_level_overall[0]->ability_estimate;
                            }
                        }
                        $score['overall'] = array(
                            'score' => $tds_scale,
                            'level' => $tds_level,
                            'dwh_scale' => $tds_scale,
                            'dwh_level' => $tds_level,
                            'dwh_ability' => $tds_ability,
                        );
                    }
                }
            }
            return $score;
        }
    }

    /**WP-1279 * Function to split or seperate TDS responses */
    function tds_separate_skill($responses){
        $score_sections = $reading_section = $listening_section = array();
        $score_reading = $score_listening = $qus_reading = $qus_listening = 0;
        
        foreach ($responses->response as $key => $value) {
            $value = current($value);
            if ($value['skill'] === "R") {
                $score_reading = $score_reading + $value['score'];
                $qus_reading = $qus_reading + 1;
                $reading_section[] = $value['score'];
            }
            
            if ($value['skill'] === "L") {
                $score_listening = $score_listening + $value['score'];
                $qus_listening = $qus_listening + 1;
                $listening_section[] = $value['score'];
            }
        }
        
        /* Merge two section for score calculation */
        $merge_two_sections = array_merge($listening_section, $reading_section);
        array_unshift($merge_two_sections, "");
        unset($merge_two_sections[0]);
        
        $score_sections['reading']['score'] = $score_reading;
        $score_sections['reading']['question'] = $qus_reading;
        
        $score_sections['listening']['score'] = $score_listening;
        $score_sections['listening']['question'] = $qus_listening;
        
        $score_sections['lr_sections'] = $merge_two_sections;
        return $score_sections;
    }

    /** WP-1279
     * Function to calculate CEFR value by result type "Logit" and "Threshold"
     * @param integer $score_calculation_type_id
     * @param string $level
     * @param integer $score
     * @param float $average
     * @param string $score_txt
     * @param array $result_display_settings
     * @return array
     */
    public function get_cefr_tds($score_calculation_type_id, $level, $score, $average, $score_txt, $result_display_settings){
        $overall = array();
        $ability_estimate = array();
        $cefr_array = array('A1.1', 'A1.2', 'A1.3', 'A2.1', 'A2.2', 'A2.3', 'B1.1', 'B1.2', 'B1.3');
        if($score_calculation_type_id == 1){ /* Result type as Logit */
            /* currently not in use */
            $overall = array(
                'score' => '',
                'level' => 'Logit calculation not in use',
                'result_type' => 'logit',
                'dwh_scale' => NULL,
                'dwh_level' => NULL,
                'dwh_ability' => NULL,
                'dwh_result' => -1,
            );
        }elseif($score_calculation_type_id == 2){ /* Result type as Threshold */
            
            $base_scores = unserialize($result_display_settings['logit_values']);
            if ($score >= $result_display_settings['passing_threshold']) {
                $overall_score = $base_scores[$level] + $score;
                $overall_level = $level;
                /* APP-5 & APP-6 calculate scale and evel to send DWH */
                $tds_level = $overall_level;
                $tds_scale = $overall_score;
                $core_status = 1;
            } elseif (($score < $result_display_settings['passing_threshold']) && ($score > $result_display_settings['lower_threshold'])) {
                $key = array_search($level, $cefr_array);
                if ($key > 0) {
                    $cal_level = $cefr_array[$key - 1];
                    /* changes done based on https://catsuk.atlassian.net/browse/WP-526 */
                    $cal_score = $base_scores[$cal_level] + $result_display_settings['passing_threshold'];
                    $tds_level = $cal_level; /* APP-5 & APP-6 calculate level to send DWH */
                } else {
                    $cal_level = $cefr_array[$key];
                    /* changes done based on https://catsuk.atlassian.net/browse/WP-512 */
                    $cal_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
                    $cal_score = $score;
                    $tds_level = $level; /* APP-5 & APP-6 calculate level to send DWH */
                }
                $overall_score = $cal_score;
                $overall_level = $cal_level;
                $core_status = 0;
                $tds_scale = $cal_score; /* APP-5 & APP-6 calculate scale to send DWH */
            } else {
                /* changes done based on https://catsuk.atlassian.net/browse/WP-512 */
                $overall_score = '';
                $overall_level = 'You have not achieved the pass level for the exam and we are unable to award you a result.';
                $tds_scale = NULL;
                $tds_level = NULL;
                $ability_estimate = NULL;
                $core_status = -1;
            }
            if($tds_scale !== NULL && $tds_level !== NULL && $ability_estimate !== NULL){
                /* APP-5 & APP-6 calculate ablity to send DWH */
                if($tds_scale == 0){
                    $cefr_level_overall = $this->tdsmodel->get_cats_cefr_level_by_score_zero();
                }else{
                    $cefr_level_overall = $this->tdsmodel->get_cats_cefr_level_by_score($tds_scale, "tds");
                }
                $ability_estimate = $cefr_level_overall[0]->ability_estimate;
            }/* TDS-365 Null conditions - End */
            $overall = array(
                'score' => $overall_score,
                'level' => $overall_level,
                'result_type' => 'threshold',
                'dwh_scale' => $tds_scale,
                'dwh_level' => $tds_level,
                'dwh_ability' => $ability_estimate,
                'dwh_result' => $core_status,
            );
        }
        return $overall;
    }

    /* Tds-365 Reading and listening subrows in core_test_instances - base score added */
    function get_base_sources($level,$processed_data) { 
        $result_display_settings = $this->placementmodel->get_result_display_settings();
        $base_scores_values = unserialize($result_display_settings['logit_values']);
        /* TDS-365 Null conditions- start */
        if($processed_data['total']['score'] < $result_display_settings['lower_threshold']){
            $score['reading'] = array(
                'clientScale' =>  NULL,
                'ability' => NULL,
                'level' => NULL
            );
            $score['listening'] = array(
                'clientScale' => NULL,
                'ability' => NULL,
                'level' => NULL
            );/* TDS-365 Null conditions - End */
        }else{
            if (array_key_exists($level,$base_scores_values)){
                $base_scores = $base_scores_values[$level];
            }
            if(isset($processed_data['listening']['score']) && ($processed_data['listening']['score'] > 0)){
                $lscore = $processed_data['listening']['score'] + $base_scores;
                $lability = $this->tdsmodel->get_cats_cefr_level_by_score($lscore, "tds");
                $score['listening'] = array(
                    'clientScale' =>  $lscore,
                    'ability' => $lability[0]->ability_estimate,
                    'level' => $lability[0]->cefr_level
                );
            }else{
                $lscore = $processed_data['listening']['score'] + $base_scores;
                $lability = $this->tdsmodel->get_cats_cefr_level_by_score($lscore, "tds");
                $score['listening'] = array(
                    'clientScale' =>  $lscore,
                    'ability' => $lability[0]->ability_estimate,
                    'level' => $lability[0]->cefr_level
                );
            }
            if(isset($processed_data['reading']['score']) && ($processed_data['reading']['score'] > 0)){
                $rscore = $processed_data['reading']['score'] + $base_scores;
                $rability = $this->tdsmodel->get_cats_cefr_level_by_score($rscore, "tds");
                $score['reading'] = array(
                    'clientScale' =>  $rscore,
                    'ability' => $rability[0]->ability_estimate,
                    'level' => $rability[0]->cefr_level
                );
            }else{
                $rscore = $processed_data['reading']['score'] + $base_scores;
                $rability = $this->tdsmodel->get_cats_cefr_level_by_score($rscore, "tds");
                $score['reading'] = array(
                    'clientScale' =>  $rscore,
                    'ability' => $rability[0]->ability_estimate,
                    'level' => $rability[0]->cefr_level
                );
            }
        }
        return $score;
    }
    
    /**
	 * Function to Process TDS Benchmark Result by CRON after two hour
	 */
	function get_tds_benchmark_result_after_two_hours() {
	    $query = $this->db->query('SELECT task_id FROM tds_tasks WHERE start = "' . $this->tds_start . '" AND  end = "' . $this->tds_end . '" LIMIT 1');
	    if ($query->getNumRows() > 0) {
	        error_log(date('[Y-m-d H:i:s e] ') . "Success: Execution After Second Run - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . ' - The data processed already for this date range!' . PHP_EOL, 3, LOG_FILE_TDS);
	    } else {
	        error_log(date('[Y-m-d H:i:s e] ') . "Running from Second Run - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . PHP_EOL, 3, LOG_FILE_TDS);
	        $this->getTDSBenchmarkResult(FALSE, FALSE, FALSE, 2);
	    }
	}	
	
	/**
	 * Function to Process TDS Benchmark Result by CRON after three hour
	 */
	function get_tds_benchmark_result_after_three_hours() {
	    $query = $this->db->query('SELECT task_id FROM tds_tasks WHERE start = "' . $this->tds_start . '" AND  end = "' . $this->tds_end . '" LIMIT 1');
	    if ($query->getNumRows() > 0) {
	        error_log(date('[Y-m-d H:i:s e] ') . "Success: Execution After Third Run - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . ' - The data processed already for this date range!' . PHP_EOL, 3, LOG_FILE_TDS);
	    } else {
	        error_log(date('[Y-m-d H:i:s e] ') . "Running from Third Run - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . PHP_EOL, 3, LOG_FILE_TDS);
	        $this->getTDSBenchmarkResult(FALSE, FALSE, FALSE, 3);
	    }
	}

    /**
	 * Function to Process TDS Benchmark Result by CRON after all hour
	 */
	function get_tds_benchmark_result_after_all_hours() {
	    $mail_to = $this->collegepremodel->get_cron_mailto();
	    $query = $this->db->query('SELECT task_id FROM tds_logs WHERE start_date = "' . $this->tds_start . '" AND  end_date = "' . $this->tds_end . '" AND status = 1  LIMIT 1');
	    if ($query->getNumRows() > 0) {
	        error_log(date('[Y-m-d H:i:s e] ') . "Success: Execution After Fourth Run - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . ' - The data processed already for this date range!' . PHP_EOL, 3, LOG_FILE_TDS);
	    } else {
	        /* insert to logs for failure attempts */
	        $ins1_logs = array('task_id' => 'TASK#', 'date_run' => strtotime(date('d-m-Y')), 'time_run' => date('H:i:s'), 'timezone' => date('e'), 'status' => 0, 'attempt' => 1, 'start_date' => $this->tds_start, 'end_date' => $this->tds_end, 'message' => 'Failure attempt from 1');
	        $this->tdsmodel->insert_tds_logs($ins1_logs);
	        $ins2_logs = array('task_id' => 'TASK#', 'date_run' => strtotime(date('d-m-Y')), 'time_run' => date('H:i:s'), 'timezone' => date('e'), 'status' => 0, 'attempt' => 2, 'start_date' => $this->tds_start, 'end_date' => $this->tds_end, 'message' => 'Failure attempt from 2');
	        $this->tdsmodel->insert_tds_logs($ins2_logs);
	        $ins3_logs = array('task_id' => 'TASK#', 'date_run' => strtotime(date('d-m-Y')), 'time_run' => date('H:i:s'), 'timezone' => date('e'), 'status' => 0, 'attempt' => 3, 'start_date' => $this->tds_start, 'end_date' => $this->tds_end, 'message' => 'Failure attempt from 3');
	        $this->tdsmodel->insert_tds_logs($ins3_logs);

	        if ($mail_to['status'] != '0') {
	            error_log(date('[Y-m-d H:i e] ') . "Running from Fourth Run for sending email to admin - From  - " . date('d-m-Y', $this->tds_start) . '  To  ' . date('d-m-Y', $this->tds_end) . PHP_EOL, 3, LOG_FILE_TDS);
	            
	            $data = array(
	                'success_logs' => $this->tdsmodel->get_tds_benchmark_success_logs(),
	                'failure_logs' => $this->tdsmodel->get_tds_benchmark_failure_logs(),
	            );

                $config =  @get_email_config_provider(5);
                $mail_content = view('admin/cron_template',$data);
                $mail_message = $mail_content;
                $subject = 'Job scheduling TDS Result logs from ' . base_url() . ' - IP : ' . $_SERVER['SERVER_ADDR'] . '  on -' . date('d-m-Y');
                $from_address = 'noreply@catsstep.education';
                if(isset($config['smtp_user']) && $config['smtp_user'] == "Api-Key:"){
                    $sendSmtpEmail['subject'] = $subject;
                    $sendSmtpEmail['htmlContent'] = $mail_message;
                    $sendSmtpEmail['sender'] = array('name' => 'noreply', 'email' => $from_address);
                    $sendSmtpEmail['to'] = array(array('email' => $mail_to['cron_mailto']));
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
                    $this->email->setFrom($from_address, 'noreply');
                    $this->email->setTo($mail_to['cron_mailto']);
                    $this->email->setMailtype("html");
                    $this->email->setNewline("\r\n");
                    $this->email->setCrlf("\r\n");
                    $this->email->setSubject($subject);
	                $this->email->setMessage($mail_message);
                    if ($this->email->send()) {
                            $sent_mail_status = true;
                            $sent_mail_log = 'success';
                    }else{
                        $sent_mail_status = false;
                        $sent_mail_log = json_encode($this->email->printDebugger());  
                    }
                }
                /* log tables affected from here */
                $mail_log = array(
                    'from_address' => $from_address,
                    'to_address' => $mail_to['cron_mailto'],
                    'response' => $sent_mail_log,
                    'status' => $sent_mail_status ? 1 : 0,
                    'purpose' => $subject
                );
                $builder = $this->db->table('email_log'); 
                $builder->insert($mail_log);
	            exit();
	        }
	    }
	}

     /** APP-5 and APP-6
     * Function to execute a schedule job to push TDS test data to DWH
     * @param boolean $data
     * @return mixed
     */
    function http_tds_ws_call($data = FALSE) {
        if ($data != FALSE){
            
            $serverurl = $this->oauth->catsurl('tds_dwh_ws_url');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $serverurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . urlencode($data) . '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);          
            
            return $server_output;
        }
    }
    
    /** APP-5 and APP-6, WP-1301
     * Function to send Placement data to Datawarehouse server
    */
    public function send_tds_placement_data_to_dwh() {

        header('Content-Type: application/json');
        $score_data = $skill = $skills = $abilities = $candidate_result_data = array();
        $test_type = $test_group = '';

        $query = $this->db->query('SELECT * FROM tds_placement_results WHERE candidate_id != "9999999999" AND status = 0 AND sync_status != 1 ORDER BY id DESC LIMIT 1');
        if ($query->getNumRows() > 0) {

            $result = $query->getRowObject();            
            if ($result->candidate_id != '' && $this->exists_user($result->candidate_id)){
                if((strlen($result->token) > 9) && (strpos($result->token, '_') !== false)){
                    $placement_token = explode('_', $result->token);
                    $token = end($placement_token);
                }

                $token_details = $this->tdsmodel->get_token_detail($token, 'placement');
                $test_details = $this->tdsmodel->get_tds_test_date($result->token, $result->candidate_id);

                $test_type = 48; /* $tds_test_details->testtypes_id; */
                $test_group = 1; /* Hard coded value */
                $raw_responses = json_decode($result->raw_responses);
                $raw_abilities = json_decode($result->raw_abilities);
                $processed_data = json_decode($result->processed_data);
               
                if(!empty($raw_responses) &&  count(array($raw_responses)) > 0){
                    if(isset($raw_responses->response) && count($raw_responses->response) > 0){
                        foreach ($raw_responses->response as $response){
                            /* added skill writing for WP-1358 and speaking for TDS-368 */
                            if($response->{'@attributes'}->skill == "S"){
                                $skill["speaking"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "W"){
                                $skill["writing"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "R"){
                                $skill["reading"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "L"){
                                $skill["listening"][] = $response;
                            }
                        }

                        if(!empty($skill["speaking"]) && count($skill["speaking"]) > 0){
                            $processed_data_speaking = (isset($processed_data->speaking)) ? $processed_data->speaking : "";
                            $skills[] =  $this->tds_skills_score_calculation($skill["speaking"], "speaking", $processed_data_speaking, 'stepcheck');
                        }
                        if(!empty($skill["writing"]) && count($skill["writing"]) > 0){
                            $processed_data_writing = (isset($processed_data->writing)) ? $processed_data->writing : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["writing"], "writing", $processed_data_writing, 'stepcheck');
                        }
                        if(!empty($skill["reading"]) && count($skill["reading"]) > 0){
                            $processed_data_reading = (isset($processed_data->reading)) ? $processed_data->reading : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["reading"], "reading", $processed_data_reading, false);
                        }
                        if(!empty($skill["listening"]) && count($skill["listening"]) > 0){
                            $processed_data_listening = (isset($processed_data->listening)) ? $processed_data->listening : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["listening"], "listening", $processed_data_listening, false);
                        }
                    }
                }

                if(!empty($raw_abilities) && count(array($raw_abilities)) > 0){
                    if((isset($raw_abilities->ability)) && (count(array($raw_abilities->ability)) > 0)){
                        foreach ($raw_abilities->ability as $ability){
                            if($test_details == false){
                                $abilities[] = array(
                                    "skill" => $ability->{'@attributes'}->skill,
                                    "ability" => $ability->{'@attributes'}->ability
                                );
                            }else{
                                $abilities[] = array(
                                    "skill" => $ability->skill,
                                    "ability" => $ability->ability
                                );
                            }                   
                           
                        }
                    }
                }

                $client_scale = (isset($processed_data->overall->dwh_scale)) ? $processed_data->overall->dwh_scale : "";
                $final_ability = (isset($processed_data->overall->dwh_ability)) ? $processed_data->overall->dwh_ability : "";
                $cats_level =  (isset($processed_data->overall->dwh_level)) ? $processed_data->overall->dwh_level : "";
                if(!empty($test_details)){
                     $testdate= $test_details->test_date; 
                }else{ 
                    $testdate=strtotime($result->result_date);
                }

                $score_data[] = array(
                    "userId" => $result->candidate_id,
                    "token" => $result->token,
                    "testType" => $test_type,
                    "testGroup" => $test_group,
                    "testId" => $result->testform_id . "_" . $result->testform_version,
                    "tokenId" => $token_details->id,
                    "testDate" => $testdate,
                    "institutionTierId" => $token_details->institutionTierId,
                    "isPlacement" => 1,
                    "clientScale" => $client_scale,
                    "ability" => $final_ability,
                    "catsLevel" => $cats_level,
                    "tdsInstanceId" =>  $result->testinstance_id,
                    "candidateId" => $result->candidate_id,
                    "tdsFormId" => $result->testform_id,
                    "tdsFormVersion" => $result->testform_version,
                    "skills" => $skills,
                    "abilities" => $abilities
                );

                $candidate_result_data = array(
                    "token" => $this->oauth->catsurl('dwh_ws_token'),
                    "scoreData" => $score_data
                );
               
                try {
                    /*
                        DS-269 - Duplicate rows in core_test_instances in data warehouse - START
                        Change the sync status as 1
                    */

                    $data = array('sync_status' => '1');
                    $builder = $this->db->table('tds_placement_results');
                    $builder->where('id',$result->id);
                    $builder->update($data);


                    /* TDS-269 - Duplicate rows in core_test_instances in data warehouse - END */
                    $data = json_encode($candidate_result_data);
                    $response = $this->http_tds_ws_call($data);
                    $parse_data = json_decode($response);
                    if(isset($parse_data) && !empty($parse_data)){
                        /* get_object_vars */
                        $objArray = get_object_vars($parse_data);
                        if (isset($parse_data) && is_array($objArray)) {
                            if (isset($objArray['result']->code) && $objArray['result']->code == '0'){
                         
                                $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                $builder = $this->db->table('tds_dwh_logs');
                                $builder->insert($data);
                         
                                $data = array('status' => '1','sync_status' => '2','response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                $builder = $this->db->table('tds_placement_results');
                                $builder->where('id', $result->id);
                                $builder->update($data);


                            }else{
                                if( $result->push_attempt >= 2){

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '-1','sync_status' => '0','response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message);
                                    $builder = $this->db->table('tds_placement_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                    $this->send_mail_tds_to_dwh_score_push_fail('Placement Test', $result);

                                }else{

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '0');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '0','sync_status' => '0','response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                    $builder = $this->db->table('tds_placement_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);
                                }
                            }
                        }
                    }else{


                        $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '-1');
                        $builder = $this->db->table('tds_dwh_logs');
	                    $builder->insert($data);


                        $data = array('status' => '-1','sync_status' => '0','push_attempt' => $result->push_attempt + 1);
                        $builder = $this->db->table('tds_placement_results');
                        $builder->where('id', $result->id);
                        $builder->update($data);
                    }
                } catch (\Exception $ex) {
                    
                }
                
            }else{

                $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $objArray, 'attempt' => $result->push_attempt + 1, 'status' => '-1');
                $builder = $this->db->table('tds_dwh_logs');
                $builder->insert($data);

                $this->db->update('tds_placement_results', array('status' => '-1','sync_status' => '0','push_attempt' => $result->push_attempt + 1), array('id' => $result->id));

                $data = array('status' => '-1','sync_status' => '0','push_attempt' => $result->push_attempt + 1);
                $builder = $this->db->table('tds_placement_results');
                $builder->where('id', $result->id);
                $builder->update($data);
            }
            
        }
    }


    /** APP-5 and APP-6
     * Function to send Stepcheck data to Datawarehouse server
     */
    public function send_tds_stepcheck_data_to_dwh() {
        header('Content-Type: application/json');
        $score_data = $skill = $skills = $abilities = $candidate_result_data = array();
        $test_type = $test_group = '';
        $query = $this->db->query('SELECT * FROM tds_benchmark_results WHERE candidate_id != "9999999999" AND status = 0 AND sync_status != 1 ORDER BY id DESC LIMIT 1');
        
        if ($query->getNumRows() > 0) {
            $result = $query->getRowObject();
            if ($result->candidate_id != '' && $this->exists_user($result->candidate_id)){
                $token_details = $this->tdsmodel->get_token_detail($result->token, 'stepcheck');
                $test_details = $this->tdsmodel->get_stepcheck_date($result->token, $result->candidate_id);
                $tds_test_details = $this->tdsmodel->get_tds_test_type_by_formid($result->testform_id, $result->testform_version);
                $test_type = $tds_test_details->testtypes_id;
                $test_group = 3; /* Hard coded value */
                $raw_responses = json_decode($result->raw_responses);
                $raw_abilities = json_decode($result->raw_abilities);
                $processed_data = json_decode($result->processed_data);
                
                if(!empty($raw_responses) && count(array($raw_responses)) > 0){
                    if(isset($raw_responses->response) && count($raw_responses->response) > 0){
                        foreach ($raw_responses->response as $response){
                            if($response->{'@attributes'}->skill == "S"){
                                $skill["speaking"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "W"){
                                $skill["writing"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "R"){
                                $skill["reading"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "L"){
                                $skill["listening"][] = $response;
                            }
                        }
                        if(!empty($skill["speaking"]) && count($skill["speaking"]) > 0){
                            $processed_data_speaking = (isset($processed_data->speaking)) ? $processed_data->speaking : "";
                            $skills[] =  $this->tds_skills_score_calculation($skill["speaking"], "speaking", $processed_data_speaking, 'stepcheck');
                        }
                        if(!empty($skill["writing"]) && count($skill["writing"]) > 0){
                            $processed_data_writing = (isset($processed_data->writing)) ? $processed_data->writing : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["writing"], "writing", $processed_data_writing, 'stepcheck');
                        }
                        if(!empty($skill["reading"]) && count($skill["reading"]) > 0){
                            $processed_data_reading = (isset($processed_data->reading)) ? $processed_data->reading : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["reading"], "reading", $processed_data_reading, false);
                        }
                        if(!empty($skill["listening"]) && count($skill["listening"]) > 0){
                            $processed_data_listening = (isset($processed_data->listening)) ? $processed_data->listening : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["listening"], "listening", $processed_data_listening, false);
                        }
                    }
                }
                
                if(!empty($raw_abilities) && count(array($raw_abilities)) > 0){
                    if((isset($raw_abilities->ability)) && (count((array) $raw_abilities->ability) > 0)){
                        foreach ($raw_abilities->ability as $ability){
                            $abilities[] = array(
                                "skill" => $ability->{'@attributes'}->skill,
                                "ability" => $ability->{'@attributes'}->ability
                            );
                        }
                    }
                }
                
                $client_scale = (isset($processed_data->overall->dwh_scale)) ? $processed_data->overall->dwh_scale : "";
                $final_ability = (isset($processed_data->overall->dwh_ability)) ? $processed_data->overall->dwh_ability : "";
                $cats_level =  (isset($processed_data->overall->dwh_level)) ? $processed_data->overall->dwh_level : "";
                
                $score_data[] = array(
                    "userId" => $result->candidate_id,
                    "token" => $result->token,
                    "testType" => $test_type,
                    "testGroup" => $test_group, 
                    "testId" => $result->testform_id . "_" . $result->testform_version,
                    "tokenId" => $token_details->id,
                    "testDate" => $test_details->datetime,
                    "institutionTierId" => $token_details->institutionTierId,
                    "isPlacement" => 0,
                    "clientScale" => $client_scale,
                    "ability" => $final_ability,
                    "catsLevel" => $cats_level,
                    "tdsInstanceId" =>  $result->testinstance_id,
                    "candidateId" => $result->candidate_id,
                    "tdsFormId" => $result->testform_id,
                    "tdsFormVersion" => $result->testform_version,
                    "skills" => $skills,
                    "abilities" => $abilities
                );
                
                $candidate_result_data = array(
                    "token" => $this->oauth->catsurl('dwh_ws_token'),
                    "scoreData" => $score_data
                );
                
                try {
                    /*
                        TDS-269 - Duplicate rows in core_test_instances in data warehouse - START
                        Change the sync status as 1
                    */
                    $data = array('sync_status' => '1');
                    $builder = $this->db->table('tds_benchmark_results');
                    $builder->where('id',$result->id);
                    $builder->update($data);

                    /* TDS-269 - Duplicate rows in core_test_instances in data warehouse - END */
                    $data = json_encode($candidate_result_data);
                    $response = $this->http_tds_ws_call($data);

                    $parse_data = json_decode($response);
                    if(isset($parse_data) && !empty($parse_data)){
                        /* get_object_vars */
                        $objArray = get_object_vars($parse_data);
                        if (isset($parse_data) && is_array($objArray)) {
                            if (isset($objArray['result']->code) && $objArray['result']->code == '0'){

                                $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                $builder = $this->db->table('tds_dwh_logs');
                                $builder->insert($data);

                                $data = array('status' => '1', 'sync_status' => '2', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                $builder = $this->db->table('tds_benchmark_results');
                                $builder->where('id', $result->id);
                                $builder->update($data);

                            }else{
                                if( $result->push_attempt >= 2){

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '-1', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message);
                                    $builder = $this->db->table('tds_benchmark_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                    $this->send_mail_tds_to_dwh_score_push_fail('StepCheck Test', $result);

                                }else{

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '0');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '0', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                    $builder = $this->db->table('tds_benchmark_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                }
                            }
                        }
                    }else{

                        $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '-1');
                        $builder = $this->db->table('tds_dwh_logs');
                        $builder->insert($data);

                        $data = array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                        $builder = $this->db->table('tds_benchmark_results');
                        $builder->where('id', $result->id);
                        $builder->update($data);

                    }
                } catch (\Exception $ex) {
                    
                }
                
            }else{

                $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $objArray, 'attempt' => $result->push_attempt + 1, 'status' => '-1');
                $builder = $this->db->table('tds_dwh_logs');
                $builder->insert($data);


                $data = array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                $builder = $this->db->table('tds_benchmark_results');
                $builder->where('id', $result->id);
                $builder->update($data);
            }
            
        }
    }


    /** APP-5 & APP-6
     * Function to calculate score and outof for the TDS Skills (Speaking, Writing, Reading and Listening)
     * @param array $skill_responses
     * @param string $skill
     * @param object $processed_data
     * @return array $skill_set
     */
    function tds_skills_score_calculation($skill_responses, $skill, $processed_data, $course_type = false ){
        
        $skill_score = $skill_outof = $itemid = 0;
        $speaking_qnumber = 1;
        $groupitem = $findgroup = $subtask = $parsed_value = array();
        
        foreach($skill_responses as $key => $value){
            $itemid = $value->{'@attributes'}->itemid;
            if(in_array($itemid, $groupitem)){
                $findgroup[] = $itemid;
            }
            $groupitem[] = $itemid;
        }

        foreach($skill_responses as $key => $value){
            if(in_array($value->{'@attributes'}->itemid, $findgroup)){
                if($skill == 'writing'){ /* Apply weight for writing skill and calculate score. */
                    $weight = $this->tdsmodel->get_weight_by_qnumer($key+1, $skill, $course_type);
                    $score = $value->{'@attributes'}->score/$value->{'@attributes'}->outof;
                    
                    $outof = 1 * $weight;
                    $score_with_weight = $score * $weight;
                }elseif($skill == 'speaking'){ /* Apply weight for speaking skill and calculate score. */
                    if ($value->{'@attributes'}->outof > 0) {
                        $weight = $this->tdsmodel->get_weight_by_qnumer($speaking_qnumber, $skill, $course_type);
                        $score = $value->{'@attributes'}->score;
                        $outof = $value->{'@attributes'}->outof * $weight;
                        $score_with_weight = $score * $weight;
                        $speaking_qnumber = $speaking_qnumber + 1;
                    }else{
                        $score_with_weight = $outof = 0;
                    }
                }else{ /* calculate score for reading and listening. */
                    $score_with_weight = $value->{'@attributes'}->score;
                    $outof = $value->{'@attributes'}->outof;
                }
              
                $ans = (array) $value->answer;
                $correct_ans = (array) $value->key;
                $skill_given_answer = (empty($ans)) ? "" : $value->answer; /* Avoid empty object */
                $skill_correct_answer = (empty($correct_ans)) ? "" : $value->key; /* Avoid empty object */
                $subtask[$value->{'@attributes'}->itemid][] = array(
                    "itemId" => $value->{'@attributes'}->itemid,
                    "itemVersion" => $value->{'@attributes'}->itemversion,
                    "tdsInteractionId" => $value->{'@attributes'}->interactionid,
                    "difficulty" => $value->{'@attributes'}->difficulty,
                    "calibrated" => $value->{'@attributes'}->calibrated,
                    "score" => $score_with_weight,
                    "outof" => $outof,
                    "skill" => $value->{'@attributes'}->skill,
                    "givenAnswer" => $skill_given_answer,
                    "correctAnswer" => $skill_correct_answer,
                );
            }
        }

        foreach($skill_responses as $key => $value){
            if($skill == 'writing'){ /*Apply weight for writing skill and calculate score. */
                $weight = $this->tdsmodel->get_weight_by_qnumer($key+1, $skill, $course_type);
                $score = $value->{'@attributes'}->score/$value->{'@attributes'}->outof;
                $outof = 1 * $weight;
                $score_with_weight = $score * $weight;
            }elseif($skill == 'speaking'){ /* Apply weight for speaking skill and calculate score. */
                if ($value->{'@attributes'}->outof > 0) {
                    $weight = $this->tdsmodel->get_weight_by_qnumer($speaking_qnumber, $skill, $course_type);
                    $score = $value->{'@attributes'}->score;
                    $outof = $value->{'@attributes'}->outof * $weight;
                    $score_with_weight = $score * $weight;
                    $speaking_qnumber = $speaking_qnumber + 1;
                }else{
                    $score_with_weight = $outof = 0;
                } 
            }else{ /* calculate score for reading and listening. */
                $score_with_weight = $value->{'@attributes'}->score;
                $outof = $value->{'@attributes'}->outof;
            }

            $skill_score = $skill_score + $score_with_weight;
            $skill_outof = $skill_outof + $outof;
            $skill_name = $value->{'@attributes'}->skill;

            $ans = (array) $value->answer;
            $correct_ans = (array) $value->key;
            $skill_given_answer = (empty($ans)) ? "" : $value->answer; /* Avoid empty object */
            $skill_correct_answer = (empty($correct_ans)) ? "" : $value->key; /* Avoid empty object */
            
            if(!in_array($value->{'@attributes'}->itemid, $findgroup)){
                $skill_response[] = array(
                    "itemId" => $value->{'@attributes'}->itemid,
                    "itemVersion" => $value->{'@attributes'}->itemversion,
                    "tdsInteractionId" => $value->{'@attributes'}->interactionid,
                    "difficulty" => $value->{'@attributes'}->difficulty,
                    "calibrated" => $value->{'@attributes'}->calibrated,
                    "score" => $score_with_weight,
                    "outof" => $outof,
                    "skill" => $value->{'@attributes'}->skill,
                    "givenAnswer" => $skill_given_answer,
                    "correctAnswer" => $skill_correct_answer,
                );
            }else{
                $score_val = $outof_val = 0;
                if(!in_array($value->{'@attributes'}->itemid, $parsed_value)){
                    foreach($subtask[$value->{'@attributes'}->itemid] as $val){
                        $outof_val = $outof_val + $val['outof'];
                        $score_val = $score_val + $val['score'];
                    }
                    $skill_response[] = array(
                        "itemId" => $value->{'@attributes'}->itemid,
                        "itemVersion" => $value->{'@attributes'}->itemversion,
                        "score" => $score_val,
                        "outof" => $outof_val,
                        "subtask" => $subtask[$value->{'@attributes'}->itemid],
                    );
                    $parsed_value[] = $value->{'@attributes'}->itemid;
                }
            }
        }
        /*
            Tds-365 Reading and listening subrows in core_test_instances - base score added
            $skill_client_scale = (isset($processed_data->score)) ? $processed_data->score : "";
        */
        if($course_type == 'core' && $skill != 'writing' && $skill != 'speaking'){
            $skill_client_scale =  $processed_data->clientScale;
            $skill_level = $processed_data->level;
            $skill_ability = $processed_data->ability;
        }else{
            $skill_client_scale = (isset($processed_data->score)) ? $processed_data->score : "";
            $skill_level = (isset($processed_data->level)) ? $processed_data->level : "";
            $skill_ability = (isset($processed_data->ability)) ? $processed_data->ability : "";
        }
        
        $tds_test_type = $this->tdsmodel->get_tds_test_type_by_skill($skill);
        
        $skill_set = array(
            "skill" => $skill_name,
            "testType" => $tds_test_type->id,
            "clientScale" => $skill_client_scale,
            "ability" => $skill_ability,
            "catsLevel" => $skill_level,
            "score" => $skill_score,
            "outof" => $skill_outof,
            "responses" => $skill_response,
        );

        if($course_type == 'primary') {
            $skill_set['clientScale'] = NULL;
            $skill_set['ability'] = NULL;
            $skill_set['catsLevel'] = NULL;
        }

        return $skill_set;
    }

    /** APP-5 and APP-6
    * Function to send Practice test data to Data warehouse server
    */
    public function send_tds_practicetest_data_to_dwh() {
        header('Content-Type: application/json');
        $score_data = $skill = $skills = $abilities = $candidate_result_data = array();
        $query = $this->db->query('SELECT * FROM tds_practicetest_results WHERE candidate_id != "99999999999999" AND status = 0 AND sync_status != 1 ORDER BY id DESC LIMIT 1');
        
        if ($query->getNumRows() > 0) {
            $result = $query->getRowObject();
            if ($result->candidate_id != '' && $this->exists_user($result->candidate_id)){
                
                if((strlen($result->token) > 9) && (strpos($result->token, '_') !== false)){
                    $practice_token = explode('_', $result->token);
                    $token = end($practice_token);
                }
                
                $user_id = substr($result->candidate_id, 0, -4);
                $token_details = $this->tdsmodel->get_token_detail($token);
                $cats_level = (isset($token_details->level)) ? $token_details->level : '';
                $test_details = $this->tdsmodel->get_tds_test_date($result->token, $result->candidate_id);
                $tds_test_details = $this->tdsmodel->get_tds_test_type($token_details->product_id, 4);
                $test_type = $tds_test_details->id;
                $test_group = $tds_test_details->testGroupId;
                $raw_responses = json_decode($result->raw_responses);
                $raw_abilities = json_decode($result->raw_abilities);
                $processed_data = json_decode($result->processed_data);
                $processed_data = (isset($processed_data->score)) ? $processed_data->score : "";
                
                if(!empty($raw_responses) && count(array($raw_responses)) > 0){
                    if(isset($raw_responses->response) && count($raw_responses->response) > 0){
                        foreach ($raw_responses->response as $response){
                            if($response->{'@attributes'}->skill == "S"){
                                $skill["speaking"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "W"){
                                $skill["writing"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "R"){
                                $skill["reading"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "L"){
                                $skill["listening"][] = $response;
                            }
                        }
                        if(!empty($skill["speaking"]) && count($skill["speaking"]) > 0){
                            $processed_data_speaking = (isset($processed_data->speaking)) ? $processed_data->speaking : "";
                            $skills[] =  $this->tds_skills_score_calculation($skill["speaking"], "speaking", $processed_data_speaking, strtolower($result->course_type));
                        }
                        if(!empty($skill["writing"]) && count($skill["writing"]) > 0){
                            $processed_data_writing = (isset($processed_data->writing)) ? $processed_data->writing : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["writing"], "writing", $processed_data_writing, strtolower($result->course_type));
                        }
                        if(!empty($skill["reading"]) && count($skill["reading"]) > 0){
                            $processed_data_reading = (isset($processed_data->reading)) ? $processed_data->reading : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["reading"], "reading", $processed_data_reading, strtolower($result->course_type));
                        }
                        if(!empty($skill["listening"]) && count($skill["listening"]) > 0){
                            $processed_data_listening = (isset($processed_data->listening)) ? $processed_data->listening : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["listening"], "listening", $processed_data_listening, strtolower($result->course_type));
                        }
                    }
                }
                
                if(!empty($raw_abilities) && count(array($raw_abilities))  > 0){
                    if((isset($raw_abilities->ability)) && (count($raw_abilities->ability) > 0)){
                        foreach ($raw_abilities->ability as $ability){
                            $abilities[] = array(
                                "skill" => $ability->{'@attributes'}->skill,
                                "ability" => $ability->{'@attributes'}->ability
                            );
                        }
                    }
                }                
                               
                $client_scale = (isset($processed_data->overall->dwh_scale)) ? $processed_data->overall->dwh_scale : NULL;
                $final_ability = (isset($processed_data->overall->dwh_ability)) ? $processed_data->overall->dwh_ability : NULL;
                $cats_level =  (isset($processed_data->overall->dwh_level)) ? $processed_data->overall->dwh_level : NULL;
                
                $score_data[] = array(
                    "userId" => $user_id,
                    "token" => $result->token,
                    "testType" => $test_type,
                    "testGroup" => $test_group,
                    "testId" => $result->testform_id . "_" . $result->testform_version,
                    "tokenId" => $token_details->id,
                    "testDate" => $test_details->test_date,
                    "institutionTierId" => $token_details->institutionTierId,
                    "isPlacement" => 0,
                    "clientScale" => $client_scale,
                    "ability" => $final_ability,
                    "catsLevel" => $cats_level,
                    "tdsInstanceId" =>  $result->testinstance_id,
                    "candidateId" => $result->candidate_id,
                    "tdsFormId" => $result->testform_id,
                    "tdsFormVersion" => $result->testform_version,
                    "courseType" => $result->course_type,
                    "skills" => $skills,
                    "abilities" => $abilities
                );
                
                $candidate_result_data = array(
                    "token" => $this->oauth->catsurl('dwh_ws_token'),
                    "scoreData" => $score_data 
                );
                
                try {
                    /*
                        TDS-269 - Duplicate rows in core_test_instances in data warehouse - START
                        Change the sync status as 1
                    */
                    $data = array('sync_status' => '1');
                    $builder = $this->db->table('tds_practicetest_results');
                    $builder->where('id',$result->id);
                    $builder->update($data);

                    /* TDS-269 - Duplicate rows in core_test_instances in data warehouse - END */
                    $data = json_encode($candidate_result_data);
                    $response = $this->http_tds_ws_call($data);

                    $parse_data = json_decode($response);
                    if(isset($parse_data) && !empty($parse_data)){
                        /* get_object_vars */
                        $objArray = get_object_vars($parse_data);
                        if (isset($parse_data) && is_array($objArray)) {
                            if (isset($objArray['result']->code) && $objArray['result']->code == '0'){

                                $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                $builder = $this->db->table('tds_dwh_logs');
                                $builder->insert($data);

                                $data = array('status' => '1', 'sync_status' => '2', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                $builder = $this->db->table('tds_practicetest_results');
                                $builder->where('id', $result->id);
                                $builder->update($data);


                            }else{
                                if( $result->push_attempt >= 2){

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '-1', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message);
                                    $builder = $this->db->table('tds_practicetest_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                    $this->send_mail_tds_to_dwh_score_push_fail('practice Test', $result);
                                }else{

                                    $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '0');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);


                                    $data =  array('status' => '0', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                    $builder = $this->db->table('tds_practicetest_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                }
                            }
                        }
                    }else{

                        $data = array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '-1');
                        $builder = $this->db->table('tds_dwh_logs');
                        $builder->insert($data);

                        $data =   array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                        $builder = $this->db->table('tds_practicetest_results');
                        $builder->where('id', $result->id);
                        $builder->update($data);
                    }
                } catch (\Exception $ex) {
                    
                }
                
            }else{

                $data =   array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                $builder = $this->db->table('tds_practicetest_results');
                $builder->where('id', $result->id);
                $builder->update($data);
            }
        }
    }



    /** APP-5 and APP-6
     * Function to send mail to admin if send TDS test data to dwh fails after 3 attempt
     * @param string $test_type
     * @param string $message
     */
    function send_mail_tds_to_dwh_score_push_fail($test_type, $result ){
        $sub_data = isset($result->course_type) ? $result->course_type.' ' : "";
        $subject = "Failed push score to DWH - $sub_data" . ucfirst($test_type) . " - ". base_url();
        $mail_message = '<html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        </head>
                        <body>
                            <p>Push score to Data warehouse fails , Please see below the details</p>
                            <p>Token: ' . $result->token . ', Candidate Id: ' . $result->candidate_id .' , DateTime: ' . date('d-m-Y H:i:s') . '</p>
                        </body>';

        $from_address = "noreply@catsstep.education";
        $from_name = "noreply";
        $to_address = "vasanth.r@changepond.com";
        $config = @get_email_config_provider(5);
        if(isset($config['smtp_user']) && $config['smtp_user'] == "Api-Key:"){
            
            $sendSmtpEmail['subject'] = $subject;
            $sendSmtpEmail['htmlContent'] = $mail_message;
            $sendSmtpEmail['sender'] = array('name' => $from_name, 'email' => $from_address);
            $sendSmtpEmail['to'] = array(array('email' => $to_address));
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
            $this->email->setFrom($from_address, $from_name);
            $this->email->setTo($to_address);
            $this->email->setMailtype("html");
            $this->email->setNewline("\r\n");
            $this->email->setCrlf("\r\n");
            $this->email->setSubject($subject);
            $this->email->setMessage($mail_message);
            if ($this->email->send()) {
                $sent_mail_status = true;
                $sent_mail_log = 'success';
            }else{
                $sent_mail_status = false;
                $sent_mail_log = json_encode($this->email->printDebugger());  
            }
        }
        $mail_log = ['from_address' => $from_address, 'to_address' => $to_address, 'response' => $sent_mail_log, 'status' => $sent_mail_status ? 1 : 0, 'purpose' => $subject];
        $builder = $this->db->table('email_log')->insert($mail_log);
    }


    /** APP-5 and APP-6
    * Function to send End of level test data both Core, Higher to Data warehouse server
    */
    public function send_tds_end_of_test_data_to_dwh() {
        header('Content-Type: application/json');
        $score_data = $skill = $skills = $abilities = $candidate_result_data = array();
        $test_type = $test_group = '';
        
        $query = $this->db->query('SELECT * FROM tds_results WHERE candidate_id != "99999999999999" AND status = 0 AND sync_status != 1 ORDER BY id DESC LIMIT 1');
        
        if ($query->getNumRows() > 0) {
            $result = $query->getRowObject(); 
            if ($result->candidate_id != '' && $this->exists_user($result->candidate_id)){
                
                $user_id = substr($result->candidate_id, 0, -4);
                $token_details = $this->tdsmodel->get_token_detail($result->token);
                $cats_level = (isset($token_details->level)) ? $token_details->level : '';
                $test_details = $this->tdsmodel->get_tds_test_date($result->token, $result->candidate_id);
                $tds_test_details = $this->tdsmodel->get_tds_test_type($token_details->product_id, 2);
                $test_type = $tds_test_details->id;
                $test_group = $tds_test_details->testGroupId;
                $raw_responses = json_decode($result->raw_responses);
                $raw_abilities = json_decode($result->raw_abilities);
                $processed_data = json_decode($result->processed_data);
                
                if(!empty($raw_responses) && count(array($raw_responses)) > 0){
                    if(isset($raw_responses->response) && count($raw_responses->response) > 0){
                        foreach ($raw_responses->response as $response){
                            if($response->{'@attributes'}->skill == "S"){
                                $skill["speaking"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "W"){
                                $skill["writing"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "R"){
                                $skill["reading"][] = $response;
                            }elseif($response->{'@attributes'}->skill == "L"){
                                $skill["listening"][] = $response;
                            }
                        }
                        
                        if(!empty($skill["speaking"]) && count($skill["speaking"]) > 0){
                            $processed_data_speaking = (isset($processed_data->speaking)) ? $processed_data->speaking : "";
                            $skills[] =  $this->tds_skills_score_calculation($skill["speaking"], "speaking", $processed_data_speaking, strtolower($result->course_type));
                        }
                        if(!empty($skill["writing"]) && count($skill["writing"]) > 0){
                            $processed_data_writing = (isset($processed_data->writing)) ? $processed_data->writing : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["writing"], "writing", $processed_data_writing, strtolower($result->course_type));
                        }
                        if(!empty($skill["reading"]) && count($skill["reading"]) > 0){
                            $processed_data_reading = (isset($processed_data->reading)) ? $processed_data->reading : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["reading"], "reading", $processed_data_reading, strtolower($result->course_type));
                        }
                        if(!empty($skill["listening"]) && count($skill["listening"]) > 0){
                            $processed_data_listening = (isset($processed_data->listening)) ? $processed_data->listening : "";
                            $skills[] = $this->tds_skills_score_calculation($skill["listening"], "listening", $processed_data_listening, strtolower($result->course_type));
                        }
                    }
                }
                
                if(!empty($raw_abilities) && count(array($raw_abilities)) > 0){
                    if((isset($raw_abilities->ability)) && (count($raw_abilities->ability) > 0)){
                        foreach ($raw_abilities->ability as $ability){
                            $abilities[] = array(
                                "skill" => $ability->{'@attributes'}->skill,
                                "ability" => $ability->{'@attributes'}->ability
                            );
                        }
                    }
                }

                $client_scale = (isset($processed_data->overall->dwh_scale)) ? $processed_data->overall->dwh_scale : NULL;
                $final_ability = (isset($processed_data->overall->dwh_ability)) ? $processed_data->overall->dwh_ability : NULL;
                $cats_level =  (isset($processed_data->overall->dwh_level)) ? $processed_data->overall->dwh_level : NULL;
                $dwh_result = (isset($processed_data->overall->dwh_result)) ? $processed_data->overall->dwh_result : NULL;
                $score_data[] = array(
                    "userId" => $user_id,
                    "token" => $result->token,
                    "testType" => $test_type,
                    "testGroup" => $test_group,
                    "testId" => $result->testform_id . "_" . $result->testform_version,
                    "tokenId" => $token_details->id,
                    "isSupervised" => $token_details->is_supervised,
                    "testDate" => $test_details->test_date,
                    "institutionTierId" => $token_details->institutionTierId,
                    "isPlacement" => 0,
                    "clientScale" => $client_scale,
                    "ability" => $final_ability,
                    "catsLevel" => $cats_level,
                    "dwh_result" => $dwh_result,
                    "tdsInstanceId" =>  $result->testinstance_id,
                    "candidateId" => $result->candidate_id,
                    "tdsFormId" => $result->testform_id,
                    "tdsFormVersion" => $result->testform_version,
                    "courseType" => $result->course_type,
                    "skills" => $skills,
                    "abilities" => $abilities
                );

                $candidate_result_data = array(
                    "token" => $this->oauth->catsurl('dwh_ws_token'),
                    "scoreData" => $score_data
                );
                
                try {
                    /*
                        TDS-269 - Duplicate rows in core_test_instances in data warehouse - START
                        Change the sync status as 1
                    */
                    $data = array('sync_status' => '1');
                    $builder = $this->db->table('tds_results');
                    $builder->where('id',$result->id);
                    $builder->update($data);

                    /* TDS-269 - Duplicate rows in core_test_instances in data warehouse - END */
                    $data = json_encode($candidate_result_data);
                    $response = $this->http_tds_ws_call($data);


                    $parse_data = json_decode($response);
                    if(isset($parse_data) && !empty($parse_data)){
                        /* get_object_vars */
                        $objArray = get_object_vars($parse_data);
                        if (isset($parse_data) && is_array($objArray)) {
                            if (isset($objArray['result']->code) && $objArray['result']->code == '0'){

                                $data =  array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                $builder = $this->db->table('tds_dwh_logs');
                                $builder->insert($data);

                                $data = array('status' => '1', 'sync_status' => '2', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                $builder = $this->db->table('tds_results');
                                $builder->where('id', $result->id);
                                $builder->update($data);

                            }else{
                                if( $result->push_attempt >= 2){

                                    $data =   array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '1');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);
                                    $data = array('status' => '-1', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message);
                                    $builder = $this->db->table('tds_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                    $this->send_mail_tds_to_dwh_score_push_fail('Final Test', $result);
                                }else{

                                    $data =   array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '0');
                                    $builder = $this->db->table('tds_dwh_logs');
                                    $builder->insert($data);

                                    $data = array('status' => '0', 'sync_status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $result->push_attempt + 1);
                                    $builder = $this->db->table('tds_results');
                                    $builder->where('id', $result->id);
                                    $builder->update($data);

                                }
                            }
                        }
                    }else{

                        $data =   array('task_id' => $result->task_id, 'token' => $result->token, 'response' => $response, 'attempt' => $result->push_attempt + 1, 'push_status' => '-1');
                        $builder = $this->db->table('tds_dwh_logs');
                        $builder->insert($data);

                        $data = array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                        $builder = $this->db->table('tds_results');
                        $builder->where('id', $result->id);
                        $builder->update($data);
                    }
                } catch (\Exception $e) {
                    
                }
                
            }else{
                $data = array('status' => '-1', 'sync_status' => '0', 'push_attempt' => $result->push_attempt + 1);
                $builder = $this->db->table('tds_results');
                $builder->where('id', $result->id);
                $builder->update($data);

            }
       }
    }


    /* prepare WP to send linear data to Datawarehouse */
	public function send_linear_data_to_dataware_house() {
    	header('Content-Type: application/json');
    	$query = $this->db->query('SELECT * FROM  primary_placement_session WHERE status = 0 ORDER BY id DESC LIMIT 1');

    	if ($query->getNumRows() > 0) {
    		$placement_sessions = $query->getResult();
    		$make_new = [];
    		$new_answer_set = [];
    		$set_array = [];
    		$miaw = '1';
			$i=1;

    		foreach ($placement_sessions as $session): 
                $responses = unserialize($session->user_answers);
                $make_new[] = array('id' => $session->id, 'push_attempt' => $session->push_attempt, 'testid' => $session->testid, 'datetime' => $session->datetime, 'recommended_level' => $session->recommended_level, 'user_id' => $session->user_id, 'token_issued_organization_id' => $session->token_issued_organization_id, 'token' => $session->token, 'token_datetime' => $session->datetime, 'task_level' => $session->task_level, 'school' => $session->token_issued_organization_name,'score'=>$session->score);
                $miaw++;
    		endforeach; 

    		foreach ($responses as $aKey => $aValue):
                $questions = [array(
                '@attributes' => array('id' => '', 'mode' => '', 'result' => '', 'score' => $responses[$aKey]['u_score'], 'data-questionNumber' => '0' . $i),
                'raw' => $responses[$aKey]['u_value'],
                'reference' => new \ArrayObject(),
                )];
        
                $sections['item'][] = array('@attributes' => array('id' => '', 'screenId' => $responses[$aKey]['screenid'],'ability'=> '' ,'tasklevel'=>$make_new[0]['task_level'], 'totalOpenTimes' => '', 'totalOpenDuration' => ''),'question' => $questions,);
    		$i++;
    		endforeach; 

    		foreach ($make_new as $mainKey => $mainValue):
                /* APP-5 codes to datawarehose(core_instance_table)  */
                $outOf = count($sections['item']);

                $builder = $this->db->table('tokens');
                $builder->select('tokens.id as token_id,school_orders.school_user_id,institution_tier_users.institutionTierId');
                $builder->join('school_orders', 'tokens.school_order_id = school_orders.id');
                $builder->join('institution_tier_users', 'school_orders.school_user_id = institution_tier_users.user_id');
                $builder->where('tokens.token', $mainValue['token']);
                $query = $builder->get();
                if ($query->getNumRows() > 0) {
                    $core_instance = $query->getRowArray();
                    $tokenTier = $core_instance['institutionTierId'];
                    $tokenId = $core_instance['token_id'];
                    $primary = $this->db->query("SELECT logit_values  FROM `placement_settings` WHERE `id` = 3 LIMIT 1");
                    if ($primary->getNumRows() > 0) {
                        $logit_primary_serialize = $primary->getRowArray();
                        if(isset($mainValue['recommended_level']) && !empty($mainValue['recommended_level'])){
                            $logit_values = @unserialize($logit_primary_serialize['logit_values']); 
                            $client_scale = @$logit_values[str_replace('.', '_', $mainValue['recommended_level'])];
                        } 
                    }
                }

                /* APP-5 codes to datawarehose(core_instance_table) - ENDS */
                $candidate_result_data['response'] = array(
                    "token"     => $this->oauth->catsurl('dwh_ws_token'),
                    'session'   =>  array('@attributes' => array(
                                    'id' => '',
                                    'version' => '',
                                    'number' => '',
                                    'test' => 'linear',
                                    'testId' => $make_new[$mainKey]['testid'],
                                    'deviceId' => '',
                                    'dateTime' => $make_new[$mainKey]['datetime'],
                                    )),
                    'candidate' =>  array('@attributes' => array(
                                    'id' => $make_new[$mainKey]['user_id'],
                                    'finalAbilityLevel' => $make_new[$mainKey]['score'],
                                    'recommendedLevel' => $make_new[$mainKey]['recommended_level'],
                                    'task_level' => $make_new[$mainKey]['task_level'],
                    )),
    				'form' => array('@attributes' => array( 'id' => '' )),

                    /* APP-5 codes to datawarehose(core_instance_table)  */
                    "core_instance_data"=> array(
                        "userId"=> $mainValue['user_id'],
                        "testType"=> 2,
                        "testId"=> $session->testid,
                        "tokenId"=> isset($tokenId) ? $tokenId : '',
                        "testDate"=> $mainValue['token_datetime'],
                        "institutionTierId"=> isset($tokenTier) ? $tokenTier : '',
                        "isPlacement"=> "1",
                        "clientScale"=> isset($client_scale) ? $client_scale : '',
                        "ability"=> '',
                        "catsLevel"=> $mainValue['recommended_level'],
                        "score"=> $mainValue['score'],
                        "outOf"=> $outOf,

                    ),
                    /* APP-5 codes to datawarehose(core_instance_table) - ENDS */
    				'section' => array($sections, new \ArrayObject())
    		    );


    		/* send data to datawarehouse webservice */
    		try {
    			$dataNeeded = json_encode($candidate_result_data);
    			$response = $this->http_ws_call($dataNeeded);

    			$parseData = json_decode($response);
    			
    			/* get_object_vars */
    			$objArray = get_object_vars($parseData);
    			if (isset($parseData) && is_array($objArray)) {
    				if (isset($objArray['result']->code) && $objArray['result']->code == '0'):

                        $data = array('status' => '1', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $make_new[$mainKey]['push_attempt'] + 1);
                        $builder = $this->db->table('primary_placement_session');
                        $builder->where('id', $make_new[$mainKey]['id']);
                        $builder->update($data);

    				else:
                        if( $make_new[$mainKey]['push_attempt'] > 3):

                        $data = array('status' => '1', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message);
                        $builder = $this->db->table('primary_placement_session');
                        $builder->where('id', $make_new[$mainKey]['id']);
                        $builder->update($data);    

                        else:

                        $data = array('status' => '0', 'response_code' => $objArray['result']->code, 'response_message' => $objArray['result']->message, 'push_attempt' => $make_new[$mainKey]['push_attempt'] + 1);
                        $builder = $this->db->table('primary_placement_session');
                        $builder->where('id', $make_new[$mainKey]['id']);
                        $builder->update($data);     

                        endif;
    				
    				endif;
    			}
    		} catch (\Exception $ex) {
    			
    		}
    		endforeach;
    	} else {
    		
        }
        /* $this->send_linear_data_only_core_instance(); */
    }

    /* function for datawarehouse Api call */
    function http_ws_call($data = FALSE) {
        if ($data != FALSE):
        
            $serverurl = $this->oauth->catsurl('dwh_ws_url');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $serverurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . urlencode($data) . '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            return $server_output;
        endif;
    }

    /* cron to remove previous booking without results starts */
    function remove_booking_without_result() {
        /* Remove booking for the TDS - Users */
        $this->db->transBegin();
        $results = $this->db->table('booking')
            ->join('users', 'booking.user_id = users.id', 'LEFT')
            ->join('events', 'booking.event_id = events.id', 'LEFT')
            ->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id', 'LEFT')
            ->join('tds_tests', 'booking.test_delivary_id = tds_tests.candidate_id', 'LEFT')
            ->where('tds_results.candidate_id IS NULL')
            ->where(['tds_tests.test_type' => 'final', 'tds_tests.status' => '0', 'events.tds_option' => 'catstds'])
            ->where("`events`.`start_date_time` <= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -2 DAY))")
            ->select(['events.id as eid', 'events.tds_option', 'booking.id', 'booking.product_id', 'events.start_date_time as event_date', 'booking.test_delivary_id', 'users.firstname', 'users.lastname', 'users.email'])
            ->orderBy('booking.id', 'DESC')
            ->get()->getResult();
        if (isset($results) && !empty($results)) {
            foreach ($results as $result){
                $this->db->table('events')->update(['capacity' => 'capacity + 1'], ['id' => $result->eid]); // plus the capacity of event
                $remove_thirdparty_id = $result->test_delivary_id;
                $this->db->table('booking')->where('id', $result->id)->delete(); //remove the user from booking for further booking

                if($remove_thirdparty_id) {
                    log_message('error', "Admin - Removed Learner booking on cron - " .print_r($remove_thirdparty_id." -event id ".$result->eid,true));
                    $this->delete_from_tds_tests($remove_thirdparty_id);
                }
            }
        }
        $this->db->transComplete();
        ($this->db->transStatus() === FALSE) ? $this->db->transRollback() : $this->db->transCommit();
    }
    /* function for manual_result_fetch */
    public function manual_result_fetch($task_id=false)
    {
        $tds_start  = $this->request->getVar('start');
        $tds_end    = $this->request->getVar('end');
        if($task_id && !empty($tds_start) && !empty($tds_end)) {
            $tds_url                = $this->tdsLaunchUrl;
            $tds_testlaunchkey      = $this->tdsKey;
            $Manual_run_url = $this->request->getPath()."?start=$tds_start&end=$tds_end";
            $tds_result_status_url  = $tds_url . "ResultStatus?key=" . $tds_testlaunchkey . "&taskid=" . $task_id;
            $reponse_data           = (object) $this->http_post_tds($tds_result_status_url);
            $log_file_name          = LOG_FILE_TDS;
            $zip_file               = $this->efsfilepath->efs_uploads_tds . $task_id;
            
            if($reponse_data->ResultsStatus == 64) {
                if (isset($reponse_data->Url) && $reponse_data->Url != '' && $reponse_data->RecordCount > 0) {
                    if (@copy($reponse_data->Url, "$zip_file.zip")) {
                        $zip = new \ZipArchive();
                        $res = $zip->open("$zip_file.zip");
                        if ($res === TRUE) {
                            if ($zip->extractTo("$zip_file/")) {
                                $local_xml_file = "$zip_file/$task_id.xml";
                                $xml = simplexml_load_file($local_xml_file, null, LIBXML_NOCDATA) or error_log(date('[Y-m-d H:i:s e] ') . "Success: XML parse error" . PHP_EOL, 3, $log_file_name);
                                /* RESULT Processing of placement,practice,final, stepchek mainly in following function */
                                $this->tds_result_processing($task_id,$xml,$tds_start,$tds_end);
                                error_log(date('[Y-m-d H:i:s e] ') . "Success: XML received by Manual run, URL- $Manual_run_url " . $reponse_data->Url . PHP_EOL, 3, $log_file_name);
                                echo "Manual Run Completed.";
                            }
                        } else echo "File Not opened.";
                    } else echo "File not copied.";
                } else {
                    echo "File not available in TDS.";
                    echo "<pre>"; print_r($reponse_data); echo "</pre>"; exit;
                }
            } else {
                echo "<pre>"; print_r($reponse_data->ResultsStatus); echo "</pre>";
                echo "<pre>"; print_r($reponse_data); echo "</pre>"; exit;
            }
        } else echo "Please check Task ID or start date or end date.";
        exit;
    }


}

