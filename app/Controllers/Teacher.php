<?php

// use Carbon\Carbon;
// use Dompdf\Dompdf;

namespace App\Controllers;
use SimpleXMLElement;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use CodeIgniter\Files\File;

use DateTimeZone;
use DateTime;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use App\Controllers\BaseController;
use App\Libraries\Acl_auth;
use App\Libraries\Unzip;
use App\Models\Admin\Cmsmodel;
use App\Models\Admin\Bannermodel;
use App\Models\Admin\Brochuremodel;
use App\Models\Admin\Productmodel;
use App\Models\Admin\Pricemodel;
use App\Models\Admin\Tdsmodel;
use App\Models\Admin\Collegepremodel;
use App\Config\Efsfilepath;
use App\Models\School\Schoolmodel;
use App\Models\Admin\Placementmodel;
use App\Models\Teacher\Teachermodel;
use App\Models\Usermodel;
use App\Models\School\Venuemodel;
use App\Models\Site\Bookingmodel;
use App\Libraries\Ciqrcode;
use App\Models\Admin\Emailtemplatemodel;
use Config\Oauth;



class Teacher extends BaseController {

    function __construct() {
        $this->request = \Config\Services::request();
        $this->pager = service('pager'); 
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->acl_auth = new Acl_auth();
        $this->validation =  \Config\Services::validation();
        $this->pagination = \Config\Services::pager();
        $this->cmsmodel = new Cmsmodel();
        $this->bannermodel = new Bannermodel();
        $this->brochuremodel = new Brochuremodel();
        $this->productmodel = new Productmodel();
        $this->pricemodel = new Pricemodel();
        $this->usermodel = new Usermodel();
        $this->bookingmodel = new Bookingmodel();
        $this->teachermodel = new Teachermodel();
        $this->emailtemplatemodel = new Emailtemplatemodel();
        $this->tdsmodel = new Tdsmodel();
        $this->schoolmodel = new Schoolmodel();
        $this->collegepremodel = new Collegepremodel();
        $this->placementmodel = new Placementmodel();
        $efsfilepath = new \Config\Efsfilepath();
        $this->efsfilepath = $efsfilepath->get_Efs_path();
        $this->efs_charts_results_path =  $this->efsfilepath->efs_charts_results;
        $this->unzip = new Unzip();
        $this->oauth = new \Config\Oauth();
        $auth = service('auth');
        $this->passwordhash = new \Config\PasswordHash(8,FALSE);
        $this->encrypter = \Config\Services::encrypter();
        $this->ciqrcode = new Ciqrcode();
        $this->lang = new \Config\MY_Lang();

        $this->data['languages'] = $this->cmsmodel->get_language();
        $this->data['institutionTierId'] = $this->teachermodel->get_user_tiers();

		helper('downtime_helper');
		helper('percentage_helper');
        helper('higherpdf_helper');
        helper('qrcodepath');
        helper('efs_path_helper');
        helper('sendinblue_helper');
        helper('parts_helper');
        helper('corepdf_helper');
        helper('core_certificate_language_helper');
        helper('corepdf_extended_helper');
        helper('zendesk');
        helper('primarypdf_helper');
        $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
        $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');

        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            if($url == '') {
                return redirect()->to(site_url('/'));
            }
            $controller = explode("/",$url['home_page_url']);
            if($controller['0'] == "teacher"){
                // only allow users with 'learner' role to access all methods in this controller
                $this->acl_auth->restrict_access('teacher');
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
                //WP-1391 zendesk Teacher user add if not
        if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                $user_id = $this->session->get('user_id');
                $zendesk_wp_user = @get_zendesk_user_list($this->session->get('user_id'));
                $is_active = @zendesk_teacher_is_active($user_id);
                if($zendesk_wp_user == false && isset($is_active)){
                    @zendesk_user_create($this->zendesk_domain_url,$user_id,"Create","Teacher Login");
                }
            }   

    }

    function index() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        } else {
            return redirect()->to(site_url('teacher/dashboard','refresh')); 
        }
    }

    public function dashboard() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
        //setting Tabs active
        if (intval($this->session->get('tab_classes')) == 0 && intval($this->session->get('tab_reports')) == 1) {
            $this->session->remove('tab_classes', TRUE);
            $this->session->set('tab_reports', TRUE);
        } elseif (intval($this->session->get('tab_classes')) == 1 && intval($this->session->get('tab_reports')) == 0) {
            $this->session->remove('tab_reports', TRUE);
            $this->session->set('tab_classes', TRUE);
        } else {
            $this->session->set('tab_classes', TRUE);
            $this->session->remove('tab_reports', TRUE);
        }
        $this->data['search_item'] = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';

		//Show only active classes
		if($this->request->getPost()){
			if($this->request->getPost('status')){
				if($this->request->getPost('status') == 'notactive'){
					$this->session->set('class_status', '0');
					$datares= array("class_status" => "0");
					echo json_encode($datares);
					exit;	
				} elseif($this->request->getPost('status') == 'active'){
					$this->session->set('class_status', '1');
					$datares= array("class_status" => "1");
					echo json_encode($datares);	
					exit;		
				}
			}
		} else {			
			if($this->session->get('class_status') == '1'){	
				//to view classes area
				$this->data['classesData'] = $this->classes('active');
			} elseif($this->session->get('class_status') == '0'){ 		
				//to view classes area
				$this->data['classesData'] = $this->classes('notactive');			
			} else {				
				//to view classes area
				$this->data['classesData'] = $this->classes('active');				
			}
		}
        //already associated learners
        $this->data['student_associated_classes'] = $this->teachermodel->get_class_association_learner_ids();
        //search learners from insitituion where the teacher belongs
        $this->data['searchData'] = $this->teachermodel->get_consumed_tokens_by_institution();

		//search learners producing empty results
		if(!empty($this->data['search_item'])){
			if(empty($this->data['searchData'])){
				$this->session->setFlashdata('failure', 'No results were produced for the search term entered.');
			}else{
                $this->session->setFlashdata('failure', '');
            }
		}
        //get individual classes
        $myclass_id = (isset($_GET['class']) && $_GET['class'] != '') ? base64_decode($_GET['class']) : '';
        $this->data['teachingClass'] = $this->teachermodel->get_class($myclass_id);

		// token view of class
		if(!empty($myclass_id)) {

            $builder = $this->db->table('teacher_classes');
			$builder->select('*');
			$builder->join('institution_teachers', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId');
			$builder->where('classId', $myclass_id);
			$query = $builder->get();
			$teacher_class_det = $query->getResultArray();			
            if(!empty($teacher_class_det)) {
				$teacher_class_id = $teacher_class_det['0']['teacherClassId'];
				$institution_id = $teacher_class_det['0']['institutionId'];
			}
            $this->data['class_learners'] = $this->class_learners($teacher_class_id);
        }

        //to view learners area
        $this->data['learnersData'] = $this->learners($this->data['teachingClass']);

        //add learners to class
        if (null !== $this->request->getPost() && $this->request->getPost('learner_id') && $this->request->getPost('teacherClassId')) {

            $learner_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('learner_id')));
            $thirdparty_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('thirdparty_id')));
            $teacherClassId = $this->encrypter->decrypt(base64_decode($this->request->getPost('teacherClassId')));

            header('Content-Type: application/json');
            $student_classes = $this->teachermodel->get_student_classes($learner_id, $thirdparty_id, $teacherClassId);

            if (isset($student_classes) && !empty($student_classes)) {
                $this->session->setFlashdata('errors', lang('app.language_teacher_learner_added_already'));
                echo json_encode(array('success' => 0, 'msg' => 'Learner already added to class'));
                die;                     
            } else {   
                //no in class increase   
                $this->teachermodel->update_no_in_class_by_teacher_class_id('add',$teacherClassId);
                $this->session->setFlashdata('messages', lang('app.language_teacher_learner_added_to_class'));
                $insData = array('userId' => $learner_id,'thirdparty_id' => $thirdparty_id,  'teacherClassId' => $teacherClassId);  

                $builder = $this->db->table('student_classes');
                $builder->insert($insData);

                $studentClassId = $this->db->insertID();
                echo json_encode(array('success' => 1, 'msg' => 'Learner added to class'));
                die;
            }
        }else{
        }
        $data['languages'] = $this->cmsmodel->get_language();

        echo view('teacher/header');
        echo view('teacher/menus', $this->data);
        echo view('teacher/dashboard', $this->data);
        echo view('teacher/footer');
    }

    function class_learners($teacher_class_id) {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }    
       
        $perPage =  10 ;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $leaners_segment = array_search('dashboard',$TotalSegment_array,true);

        $segment = $leaners_segment + 2;
        $pager = "";
        $total = $this->schoolmodel->record_class_learners_count(trim($teacher_class_id));
        if($total > 10){
           
            $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
            $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_classleaners');
            $offset = $page * $perPage;
            $pager = $this->pager;
        }
        $data = array(
            'class_learners' => $this->schoolmodel->get_class_learner_details($perPage, $offset, trim($teacher_class_id)),
            'pager' => $pager
        );	

        /* Course progress for U13 learners */
        if (!empty($data['class_learners'])) {
            $i = 0;
            foreach ($data['class_learners'] as $tokens) {
                if ($tokens['user_app_id'] > 0) {
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

    function classes($class_status = false) {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }

        $search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
        $perPage =  10;
        $offset = 0;
        $uri = current_url(true);
        $TotalSegment_array = ($uri->getSegments());
        $groups_segment = array_search('dashboard',$TotalSegment_array,true);
        $segment = $groups_segment + 2;
        $pager = "";

        if($class_status == 'active') {
            $status_class = 1;
            $total = $this->teachermodel->record_classes_count(trim($search_item), $status_class);
        } elseif($class_status == 'notactive') {
            $status_class = 0;
            $total = $this->teachermodel->record_classes_count(trim($search_item), $status_class);
        }
        else {			
            $total = $this->teachermodel->record_classes_count(trim($search_item));
        }
        if($total > 10){
            $page = (int)(($this->request->uri->getSegment(4)) ? $this->request->uri->getSegment(4) : 1)-1;
            $this->pager->makeLinks($page+1, $perPage, $total, 'default_full', $segment, 'pagination_groups');
            $offset = $page * $perPage;
            $pager = $this->pager;
            }

        if(!empty($class_status)) {
            if($class_status == 'active') {
                $status_class = 1;
            } elseif ($class_status == 'notactive') {
                $status_class = 0;
            }
            $data = array(
                'search_item' => $search_item,
                'classes' => $this->teachermodel->fetch_classes($perPage, $offset, trim($search_item), $status_class),
                'pager' => $pager
            );		
        } else {
            $data = array(
                'search_item' => $search_item,
                'classes' => $this->teachermodel->fetch_classes($perPage, $offset, trim($search_item), $status_class),
                'pager' => $pager
            );
        }
        return $data;
    }


    function learners($teachingClass) {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
        $config = array();
        $config["base_url"] = site_url("teacher/dashboard");

        if($teachingClass)
        {
            $perPage =  10;
            $offset = 0;
            $pager = "";
            $total_rows = $this->teachermodel->record_learners_count($teachingClass['teacherClassId']);
            if($total_rows > 10){
            $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
            $offset = $page * $perPage;
            $this->pager->makeLinks($page+1, $perPage, $total_rows);
            $pager = $this->pager;
            }
            $data = array(
                'learners' => $this->teachermodel->fetch_learners($perPage, $offset, $teachingClass['teacherClassId']),
            );
            return $data;
        }else{
            return false;
        }
           
    }
    
    //get teacher
    function class_view() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
        if (null !== $this->request->getPost() && $this->request->getPost('class_id') && $this->request->getPost('class_id') != ''):
            $class_id = $this->request->getPost('class_id');
			$mark_inactive_disp = 'no';
			if($class_id){
                $builder = $this->db->table('teacher_classes');
				$builder->select('*');
				$builder->where('teacher_classes.ClassId', $class_id);
				$query = $builder->get();
				$teacher_class = $query->getResultArray();
				if(!empty($teacher_class)) {
					$teacher_class_id = $teacher_class['0']['teacherClassId'];
				}
				if($teacher_class_id){
                    $builder = $this->db->table('student_classes');
					$builder->select('*');
					$builder->where('student_classes.teacherClassId', $teacher_class_id);
					$query = $builder->get();
					$student_class = $query->getResultArray();					
					if(!empty($student_class)){
						foreach ($student_class as $st_cl_val) {
                            $builder = $this->db->table('user_products');
							$builder->select('*');
							$builder->join('collegepre_results', 'collegepre_results.thirdparty_id = user_products.thirdparty_id', 'left');
							$builder->where('user_products.user_id', $st_cl_val['userId']);
							$builder->where('user_products.thirdparty_id !=', 0 );
							$query = $builder->get();
							$user_details = $query->getResultArray();
							if(!empty($user_details)) {
								foreach($user_details as $u_key =>$u_val){
									$cl_all_userdetails[] = $u_val;					
									if(empty($u_val['section_one']) && empty($u_val['section_one'])) {
										$mark_inactive_disp = 'yes' ;
									}
								}
							}		
						}			
					} else {
						$mark_inactive_disp = 'yes' ;
					}
				}
			}
	
            $data = array(
                'class_title' => lang('app.language_teacher_viewedit_class'),
                'class_heading' => lang('app.language_teacher_viewedit_class'),
                'class' => $this->teachermodel->get_class($class_id),
				'enable' => $mark_inactive_disp
            );
        else:
            $data = array(
                'class_title' => lang('app.language_teacher_add_class'),
                'class_heading' => lang('app.language_teacher_add_class'),
            );
        endif;
        $arrayData = array('success' => 1,  'html' => view('teacher/class',$data));
        echo json_encode($arrayData);
        die;
    }

    // Post Class for particular Teacher
    function postclass() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to('/');
        }
        header('Content-Type: application/json');

        if (null !== $this->request->getPost()) {

            $id = $this->request->getPost('class_id');
            $class = $this->teachermodel->get_class($id);

            if(isset($class) && !empty($class)){

                if(trim($class['englishTitle']) == trim($this->request->getPost('classname'))){
                    $rules = [
                        'classname' => [
                            'label'  => lang('app.language_teacher_class_label_name'),
                            'rules'  => 'trim|max_length[64]|required|regex_match[^[0-9:,a-zA-Z(). _-]+$]',
                            'errors' => [
                                'regex_match' => lang('app.language_teacher_class_name_check'),
                                'required' => "The Group name field is required.",
                            ],
                          ],
                     ];
                     
                }else{
                    $rules = [
                        'classname' => [
                            'label'  => lang('app.language_teacher_class_label_name'),
                            'rules'  => 'trim|max_length[64]|required|regex_match[^[0-9:,a-zA-Z(). _-]+$]|class_names_check',
                            'errors' => [
                                'regex_match' => lang('app.language_teacher_class_name_check'),
                                'required' => "The Group name field is required.",
                                'is_unique' => "The Group name must contain a unique value..",
                            ],
                          ],        
                    ];
                }
            }else{
                $rules = [
                    'classname' => [
                        'label'  => lang('app.language_teacher_class_label_name'),
                        'rules'  => 'trim|max_length[64]|required|regex_match[^[0-9:,a-zA-Z(). _-]+$]|class_names_check',
                        'errors' => [
                            'regex_match' => lang('app.language_teacher_class_name_check'),
                            'required' => "The Group name field is required.",
                            'is_unique' => "The Group name must contain a unique value..",
                        ],
                      ],     
                 ];
            }
            
            if (!$this->validate($rules)) {
                $response['success'] = 0;
                $errors = array(
                    'classname'         => $this->validation->showError('classname'),
                );
                $response['errors'] = $errors;
                echo json_encode($response);die;
            }
            else {
                $status = ($this->request->getPost('status') && $this->request->getPost('status') == 'inactive') ? '0' : '1';
                $dataClass = array(
                    'englishTitle' => $this->request->getPost('classname'),
                    'l1Title' => $this->request->getPost('classname'),
                    'date_created' => strtotime(date('d-m-Y h:i:s')),
                    'status' => $status
                );
                //echo $status;die;
                if (isset($class) && !empty($class)) {
                    //note : this is not needed for update class data
                    unset($dataClass['date_created']);
					if($status == '0') {
						$canupdate = 'yes';	
					} else {
						$canupdate = 'no';					
					}
					if($id){
                        $builder = $this->db->table('teacher_classes');
						$builder->select('*');
						$builder->where('teacher_classes.classId', $id);
						$query = $builder->get();
						$teacher_class = $query->getResultArray();
                        if(!empty($teacher_class)) {						
							$teacher_class_id = $teacher_class['0']['teacherClassId'];
						}
						if($teacher_class_id){
                            $builder = $this->db->table('student_classes');
							$builder->select('*');
							$builder->where('student_classes.teacherClassId', $teacher_class_id);
							$query = $builder->get();
							$student_class = $query->getResultArray();					
							if(!empty($student_class)){
								foreach ($student_class as $st_cl_val) {
                                    $builder = $this->db->table('user_products');
									$builder->select('*');
									$builder->join('collegepre_results', 'collegepre_results.thirdparty_id = user_products.thirdparty_id', 'left');
									$builder->where('user_products.user_id', $st_cl_val['userId']);
									$builder->where('user_products.thirdparty_id !=', 0 );
									$query = $builder->get();
									$user_details = $query->getResultArray();
									if(!empty($user_details)) {
										foreach($user_details as $u_key =>$u_val){
											$cl_all_userdetails[] = $u_val;					
											if(empty($u_val['section_one']) && empty($u_val['section_one'])) {
												$canupdate = 'yes' ;
											}
										}
									}		
								}			
							} else {
								$canupdate = 'yes' ;
							}
						}
					}
		
                    if($canupdate == 'no') {
                        $this->session->setFlashdata('errors', lang('app.language_teacher_class_nothing_to_update_msg'));
                        $dataFailure = array('success' => 1, 'msg' => 'Sorry cannot update class');
                        echo json_encode($dataFailure);
                        die;					

                    } else {
                        //update class
                        if ($this->teachermodel->update_class($id, $dataClass)) {

                            $this->session->setFlashdata('messages', lang('app.language_teacher_class_updated_success_msg'));
                            $dataSuccess = array('success' => 1, 'msg' => 'Class updated');
                            echo json_encode($dataSuccess);
                        } else {
                            $this->session->setFlashdata('errors', lang('app.language_teacher_class_nothing_to_update_msg'));
                            $dataFailure = array('success' => 1, 'msg' => 'Class not updated');
                            echo json_encode($dataFailure);
                            die;
                            }
                        }
                } else {
                    //insert class
                    $class_id = $this->teachermodel->insert_class($dataClass);
                    if ($class_id > 0) {
                        $this->session->setFlashdata('messages', lang('app.language_teacher_class_added_success_msg'));
                        $dataSuccess = array('success' => 1, 'msg' => 'Class inserted');
                        echo json_encode($dataSuccess);
                        die;
                    } else {
                        $this->session->setFlashdata('errors', lang('app.language_teacher_class_added_failure_msg'));
                        $dataFailure = array('success' => 0, 'msg' => 'Class not inserted');
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

    //remove classes from teacher
    function remove_class() {
        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
        
        header('Content-Type: application/json');
        if (null !== $this->request->getPost() && $this->request->getPost('class_id')){
            // $classId = $this->encrypt->decode($this->request->getPost('class_id'));
            $classId = $this->request->getPost('class_id');
            $teacherClassData = $this->teachermodel->get_class($classId);

            $this->db->transStart();
            $builder = $this->db->table('student_classes');
            $builder->where(array('teacherClassId' => $teacherClassData['teacherClassId']));
            $deletedRows = $builder->delete();

            $builder = $this->db->table('teacher_classes');
            $builder->where( array('classId' => $classId));
            $deletedRows =  $builder->delete();

            $builder = $this->db->table('classes');
            $builder->where(array('classId' => $classId));
            $deletedRows = $builder->delete();

            if ($deletedRows > 0) {
                $this->db->transComplete();
                $this->session->setFlashdata('messages', lang('app.language_teacher_class_removed'));
                echo json_encode(array('success' => 1, 'msg' => 'Removed from class'));
            } else {
                $this->session->setFlashdata('errors', lang('app.language_teacher_class_not_removed'));
                echo json_encode(array('success' => 0, 'msg' => 'Not removed from class'));
            }
            $this->db->transComplete(); 
        }
    }

    function remove_learner() {

        if (!$this->acl_auth->logged_in()) {
            return   redirect()->to(site_url('/')); 
        	}

        $students =  $this->request->getPost('studentClassId');
        header('Content-Type: application/json');
        if(isset($students) && !empty($students)){
            foreach($students as $studentdata)
            {		
                $studentClassId = $studentdata;
                $studentClassData  = $this->teachermodel->get_student_class_by_student_class_id($studentClassId);
                if(isset($studentClassData) && !empty($studentClassData)){
                    $this->teachermodel->update_no_in_class_by_teacher_class_id($studentClassData['teacherClassId'], 'remove');
                }
                $builder = $this->db->table('student_classes');
                $delete_leaners =  $builder->delete(array('studentClassId' => $studentClassId));
            }
            if ($delete_leaners > 0) {
                if((count($students)) > 1){
                    $alertMessage = lang('app.language_teacher_learners_removed_from_class');
                }else{
                    $alertMessage = lang('app.language_teacher_learner_removed_from_class');
                }
                $this->session->setFlashdata('messages', $alertMessage);
                echo json_encode(array('success' => 1, 'msg' => 'Removed from class'));	
            } else {
                $this->session->setFlashdata('errors', lang('app.language_teacher_learner_not_removed_from_class'));
                echo json_encode(array('success' => 0, 'msg' => 'Not removed from class'));
            }
        } 
    }  

     //log out from session
     function logout() {
        if($this->session->get('Yellowfin_loginSessionId') != ''){
            return redirect()->to('report/logoutUser'); 
        } 
        $success = $this->acl_auth->logout();

        $this->session->keepFlashdata('messages', lang('app.language_distributor_logout_success_msg'));
        return redirect()->to(site_url('/site/dashboard')); 
    }

    //profile update
    public function profile($password_change=FALSE) {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('/')); 
        }
        $userdata = $this->session->get();
        $data['profile'] = $this->usermodel->get_profile($userdata['user_id']);

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
                          //WP-1391 zendesk Teacher profile update
                          if(isset($this->zendesk_access) && $this->zendesk_access == 1){
                            $user_id = $userdata['user_id'];
                            zendesk_profile_update($user_id,"Teacher Update");   
                        }
                    $this->session->setFlashdata('messages', lang('app.language_admin_profile_updated_success_msg'));
                    return redirect()->to(site_url('teacher/profile/'));
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
                        'label'  => lang('app.language_site_booking_screen2_label_current_password'),
                        'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                        'errors' => [
                            'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                        ]
                    ],
                    'new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_new_password'),
                        'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                        'errors' => [
                            'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                        ]
                    ],
                    'confirm_new_password' => [
                        'label'  => lang('app.language_site_booking_screen2_label_confirm_new_password'),
                        'rules'  => 'required|max_length[100]|min_length[8]|matches[new_password]'
                    ]        
            ];
            if (!$this->validate($rules)) {
                $this->session->setFlashdata('errors', lang('app.language_site_booking_screen2_register_failure_msg'));
                $this->data['validation'] = $this->validator;
            } else {
                $user = $data['profile'];
                
                if (!$this->passwordhash->CheckPassword($this->request->getPost('current_password'), $user[0]->password)) {
                    $this->session->setFlashdata('errors', lang('app.language_site_change_password_current_password_invalid_msg'));
                    $this->data['validation'] = $this->validator;
                    return redirect()->to(site_url('teacher/profile/'.$this->session->get('tabpass')));
                } else {
                    $passwordata = array('password' => $this->passwordhash->HashPassword($this->request->getPost('new_password')));
                    if ($this->usermodel->update_profile($passwordata)) {
                        $this->session->set('password', $this->request->getPost('new_password'));
                        $this->session->setFlashdata('messages', lang('app.language_site_change_password_updated_success_msg'));
                        $this->data['validation'] = $this->validator;
                        return redirect()->to(site_url('teacher/profile/'.$this->session->get('tabpass')));
                    }
                }
            }
        }
        $data['languages'] = $this->cmsmodel->get_language();
        echo view('teacher/header');
        echo view('teacher/menus', $data);
        echo view('teacher/profile', $data);
        echo view('teacher/footer');
    }
    //get purchased history
    function purchased_history() {

        if (!$this->acl_auth->logged_in()) {
            return redirect()->to(site_url('admin/login')); 
        }
        header('Content-Type: application/json');

        if (null !== $this->request->getPost()) {
            $user_id = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13_learner_id')));
            $purchased_results = $this->usermodel->get_user_purchashed_course($user_id);
            $data['purchased_results'] = $purchased_results;
            $dataSuccess = array('success' => 1, 'html' => view('teacher/purchase_history', $data));
            echo json_encode($dataSuccess);
            exit;
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

    //generate practice test results
    public function gen_practicetest_result($test_number = false, $thirdPartyId = false, $purpose = false) {

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
                if ($purpose == 'for_export') {
                    return $green_or_orange;
                }
                $data['green_or_orange'] = $green_or_orange;
                $data['results'] = $label_score_arr;
                echo view('site/load_practice_test_results',$data);

            } else {
                if ($purpose == 'for_export') {
                    return 0;
                }
                echo 'ThirdParty ID /Test number not found!';
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
                 
                echo view('site/load_practice_test_results',$data);
             }else{
                echo 'ThirdParty ID /Test number not found!';
             }
         }
     }

    // WP-1276 - Higher results process - CATs TDS
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
            'is_supervised' => $is_supervised,
            'type' => $type,
        );

        echo view ('teacher/highercertificate-view_teacher', $this->data);
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

      // WP-1276 - Higher results process - CATs TDS
      public function higher_certificate_pdf($candidate_id = False, $token = False) {
        $values_higher_pdf = $this->process_results_higher($candidate_id,$token);
        //QR generation - WP-1221
        $qr_code_url = $google_url = '';
        $qrcode_params = @generateQRCodePath('teacher', 'higher', $candidate_id, $token, false);
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
           $result = $builder->get();
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


public function set_session_tab() {
    if (!$this->acl_auth->logged_in()) {
        return redirect()->to(site_url('/')); 
    }
    if ($this->request->getPost('tab_active') == 'tab_classes') {
        $this->session->remove('tab_reports', TRUE);
        $this->session->set('tab_classes', TRUE);
    } elseif ($this->request->getPost('tab_active') == 'tab_reports') {
        $this->session->remove('tab_classes', TRUE);
        $this->session->set('tab_reports', TRUE);
    } else {
        $this->session->remove('tab_reports', TRUE);
        $this->session->set('tab_classes', TRUE);
    }
    $result = array('success' => '1', 'active' => $this->request->getPost('tab_active'));
    echo json_encode($result);
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

     $res_score_settings = $this->bookingmodel->result_display_settings($testdelivary_id);

        if($res_score_settings['logit_values'] != NULL){
            $base_scores = unserialize($res_score_settings['logit_values']);
        }else{
            $builder = $this->db->table('result_display_settings');	
		    $builder->select('*');
		    $query = $builder->get();
		    $res_score_settings = $query->getRowArray();
            $base_scores = unserialize($res_score_settings['logit_values']);
        }
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
}

   ///generate GRAPH
   public function gengraph($thirdPartyId = false, $verify_view = false) {
    if ($thirdPartyId != false) {
        $query = $this->db->query('SELECT * FROM  collegepre_results WHERE thirdparty_id = "' . $thirdPartyId . '" LIMIT 1');
        if ($query->getNumRows() > 0) {
            $results = $query->getRowArray();
            $score_sections = $this->_get_two_sections($results['section_one'], $results['section_two']);
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
                            animation: {
                                onComplete: function(animation) {
                                    var postdata_$thirdPartyId={
                                        thirdparty_id  : $thirdPartyId,
                                        file           : $('#graph_" . $thirdPartyId . "')[0].toDataURL()
                                    }                                                           
                                    $.post( '" . site_url('teacher/save_chart_13') . "', postdata_$thirdPartyId)
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

function core_certificate($candidate_id = FALSE){

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
            $graph_data = $this->gengraph($thirdPartyId);
            $this->data['results'] = array(
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
            return view('teacher/corecertificate_view_teacher',$this->data);
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
                $graph_data = $this->gengraphtds($coreresults['candidate_id']);
                $this->data['results'] = array(
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
                return view('teacher/corecertificate_view_teacher',$this->data);
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
                 return view('teacher/corecertificate_view_teacher_extended',$this->data);
             }
        }
    }else {
        echo 'Not a valid GUID';
    }
}


function core_certificate_pdf($candidate_id = FALSE){

    if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $candidate_id)) {
        $builder = $this->db->table('collegepre_results as CR');
        $builder ->select('CR.candidate_name,CR.candidate_id,CR.thirdparty_id,CR.section_one,CR.result_date,CR.section_two,B.score_calculation_type_id,B.test_delivary_id,B.logit_values,B.test_delivary_id,B.product_id,B.event_id,E.test_date,E.start_date_time,E.end_date_time,P.name,P.level');
        $builder ->join('booking as B', 'CR.thirdparty_id = B.test_delivary_id');
        $builder ->join('products as P', 'B.product_id = P.id');
        $builder ->join('events as E', 'B.event_id = E.id');
        $builder ->where('CR.candidate_id', $candidate_id);
        $builder ->limit(1);
        $query = $builder ->get();
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
            $qrcode_params = @generateQRCodePath('teacher', 'core', $coreresults['candidate_id'], false);
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
            $qrcode_params = @generateQRCodePath('teacher', 'core', $coreresults['candidate_id'], false);
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

    //WP-1392 Fetch sso url via zendesk jwt token
    function get_zend_desk_url_changing(){
        $user_id = $this->request->getPost('tier_id');  
        $location = @get_zend_desk_url($user_id);
        echo $location;
        die;
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

	// Generate and download primary results PDF version
    public function primary_final_result(){
	    if (!$this->acl_auth->logged_in()) {
	        return redirect()->to('/');
	    }
        if($this->request->getPost()){
	        $u13userid = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13id')));
	        $u13thirdpartyid = $this->encrypter->decrypt(base64_decode($this->request->getPost('u13thirdpartyid')));

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
            $qrcode_params = @generateQRCodePath('teacher', 'primary', $userDetails[0]->candidate_id, false);
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
                $qrcode_params = @generateQRCodePath('teacher', 'primary', $userDetails[0]->candidate_id, false);
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
                
                echo view('teacher/u13qrverify-view', $this->data);
            }
        } elseif(!empty($guid)){ //tds_result
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
                $qrcode_params = @generateQRCodePath('teacher', 'primary', $userDetails[0]->candidate_id, false);
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
                
                echo view('teacher/u13qrverify_tds_view', $this->data);
            }
        }
        else{
            echo 'Not a valid GUID';
        }
    }


}
