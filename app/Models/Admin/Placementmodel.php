<?php 


namespace App\Models\Admin;//path
use CodeIgniter\Model;
use App\Libraries\Acl_auth;

class Placementmodel extends Model
{

	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();

        $this->acl_auth = new Acl_auth();
        $this->session = session();
        $this->router = service('router');
	}


    function get_learner_type(){
		if($this->acl_auth->logged_in()){
            $userid = $this->session->get('user_id');
            $builder = $this->db->table('instituition_learners');
            $builder->where('user_id', $userid);
			if($builder->countAllResults() > 0){
				return 'under13';
			}else{
				return 'over13';
			}
		}else{
			$this->session->remove('learnertype');
            $method = $this->router->methodName();
			if($method == 'bookingscreen2'){
				return 'over13';
			}else{
				return FALSE;
			}
		}
	}
	
	//count
	public function record_count() {
		return $this->db->count_all("question_bank_details");
	}
        //product eligibility WP-1060
    public function get_product_eligiblity($organisation_id = FALSE) {
        if ($organisation_id) {

            $builder = $this->db->table('institution_tier_users');
            $builder->select('institutionTierId');
            $builder->where('user_id', $organisation_id);

            $query = $builder->get();
            $result_arr = $query->getRowArray();
            $institutionTierId = $result_arr['institutionTierId'];
            $builder = $this->db->table('institution_eligible_products');
            $builder->select('group_id,institutionTierId');
            $builder->where('institutionTierId', $institutionTierId);
            $query = $builder->get();
            $group_ids = array_map("current",$query->getResultArray());
            return $group_ids;
        }
    }
   
    //  WP-1060 ends
    //fetch useing pagination
	public function fetch_placement_tests() {

        $builder = $this->db->table('question_bank_details');
		$builder->select('question_bank_details.*');
        $builder->orderBy("id","desc");
		$query = $builder->get();

		if ($query->getNumRows() > 0) {
			foreach ($query->getResult() as $row) {
				$rowArr[] = $row;
			}
			return $rowArr;
		}
		return FALSE;
	}
	



        public function get_currrent_result_version_control($table_name){

            $builder = $this->db->table($table_name);
            $builder->select('version');
            if($table_name == 'placement_settings') {
                $builder->where('id', 3);
            }
            $builder->orderBy("id","ASC")->limit(1);
            $query = $builder->get();
            return $query->getRowArray();
        }

        public function get_all_result_version_control($version=FALSE, $type=FALSE, $course=FALSE, $table_name=FALSE){
           
            $builder = $this->db->table('result_display_settings_version_control as rdsv');
            $builder->select('tb.*, rdsv.version, rdsv.message, rdsv.date');
            $builder->join($table_name.' as tb', 'tb.version = rdsv.version');
            if($version) {
                $builder->where('rdsv.version', $version);
            }
            if($type) {
                $builder->where('rdsv.type', $type);
            }
            if($course) {
                $builder->where('rdsv.course', $course);
            }
            $builder->orderBy("rdsv.id","DESC");
            $query = $builder->get();
            return $query->getResult();
        }


        public function get_bank_details($type){

            $builder = $this->db->table('question_bank_details');
            $builder->select('question_bank_details.*');
            $builder->where('from_where',$type);
            $builder->orderBy("id","desc");
            $builder->limit(1);
            $query = $builder->get();
             return $query->getRowArray();
        }
        
	public function fetch_active_placement_core(){

        $builder = $this->db->table('tds_placement_active_form');
		$builder->select('tds_placement_active_form.*');
		$builder->where('tds_placement_active_form.test_type', 'Adaptive');
		$query = $builder->get();
		if($query->getNumRows() > 0){
			$result = $query->getRow();
			$active_id = $result->tds_test_detail_id;
			return $active_id;
		}else{
			return FALSE;
		}
	}

    public function fetch_placement_test_details_core(){

        $builder = $this->db->table('tds_test_detail');
		$builder->select('tds_test_detail.*');
		$builder->where('tds_test_detail.test_slug', 'placement');
		$builder->where('tds_test_detail.test_type', 'Adaptive');
        $builder->orderBy("tds_test_detail.id", "ASC");
		$query = $builder->get();
		//print_r($this->db->last_query()); die;
		if($query->getNumRows() > 0){
			$result = $query->getResult();
			return $result;
		}else{
			return FALSE;
		}
	}

    public function get_result_settings_version($type=FALSE, $course=FALSE, $table_name=FALSE)
    {

        $builder = $this->db->table($table_name.' as tb');
        $builder->select('tb.*, rdsv.message');
        $builder->join('result_display_settings_version_control as rdsv', 'rdsv.version = tb.version');
        if($table_name == 'placement_settings') {
            $builder->where('tb.id', 3);
        }
        if($type) {
            $builder->where('rdsv.type', $type);
        }
        if($course) {
            $builder->where('rdsv.course', $course);
        }
        $query = $builder->get();
        return $query->getRowArray();
    }

    function get_institution_group() {
       
        $builder = $this->db->table('institutiongroup');
        $builder->select('*');
        $builder->orderBy("englishTitle", "ASC");
        $query = $builder->get();
        return $query->getResult();   
        
    } 
    public function get_tier_type_by_tierid($id = FALSE){
		if($id){
			$query = $this->db->query('SELECT * FROM institution_tiers WHERE id = '.$id);
			$result = $query->getRow();
			if($query->getNumRows() > 0){
				$tiertype = $result->tierId;
				return $tiertype;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function insert_tieruser($data){
        $builder = $this->db->table('users');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function tieruserrole($data){
        $builder = $this->db->table('user_roles');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function institute_tier_user($data){
        $builder = $this->db->table('institution_tier_users');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}
    public function school_distributor_values($data){
        $builder = $this->db->table('school_distributor');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function institute_user_type($data){
        $builder = $this->db->table('institution_user_types');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function dataActivation($data,$id) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('users');
			$builder->where('id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

    public function get_tieruser_detail_by_id($id = FALSE){
		if($id){
			$query = $this->db->query('SELECT * FROM users WHERE id = '.$id);
			$result = $query->getRow();
			if($query->getNumRows() > 0){
				return $result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

    function get_institution_group_by_tieruser($id) {
    
    	// $builder = $this->db->table('institution_user_types');
        // $builder->select('*');
        // $builder->Where("user_id",$id);
        // $query = $builder->get();
        // return $query->getResultArray();   

        $query = $this->db->query('SELECT institutionGroupId FROM institution_user_types WHERE user_id = '.$id);
        $results = $query->getResult();
        if($query->getNumRows() > 0){
            $institutionGroupId  = array();	
            foreach($results as $result):
                $institutionGroupId[] = $result->institutionGroupId;
            endforeach;
            return $institutionGroupId;
        }else{
            return FALSE;
        }
    } 

    public function update_admin_user_details($id,$data) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('institution_tier_users');
			$builder->where('user_id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

    public function update_update_tieruser_id($id,$data) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('users');
			$builder->where('id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function update_tieruser($data,$id){
        $builder = $this->db->table('users');
        $builder->where('id', $id); 
        $builder->update($data);
		return TRUE;
	}

    public function delete_tieruser($id) {
		if (isset($id) && $id != FALSE) {
			 $builder = $this->db->table('institution_user_types');
			 $builder->where('id', $id);
             $builder->delete();
			return TRUE;
		} else {
			return FALSE;
		}
	}

    public function insert_access_institutes($data){
        $builder = $this->db->table('institution_user_types');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}



    public function get_placement_query(){
        $query = $this->db->query('SELECT * FROM tds_placement_active_form WHERE test_type = "Adaptive"');
        return $query->getRow();
    }

    public function get_placement_update($data,$id){

        $builder = $this->db->table('tds_placement_active_form');
        $builder->where('id', $id); 
        $builder->set('tds_test_detail_id',$data);
		return TRUE;
	}

    public function get_placement_insert($data){
        $builder = $this->db->table('tds_placement_active_form');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function current_version(){
        $query = $this->db->query('SELECT * FROM placement_settings WHERE id = 3');
        return $query->getRowArray();

    }

    public function dataResult_update($data) {
		if ($data != FALSE) {
			 $builder = $this->db->table('placement_settings');
			 $builder->where('id',3);
             $builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}
    public function dataResult_insert($data){
        $builder = $this->db->table('placement_settings_all');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function dataResultReason($data){
        $builder = $this->db->table('result_display_settings_version_control');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

    public function get_primary_placement_all_sessions($start_date = false, $end_date = false)
    {
        $builder = $this->db->table('primary_placement_session');
        $builder ->select('primary_placement_session.*');
            if($start_date && $end_date):
                $builder ->where('datetime >=', $start_date);
                $builder ->where('datetime <=', $end_date);
            endif;
            $query = $builder->get();
            return $query->getResult();
           
    }

    public function get_placement_settings($mode = false)
    {
        if($mode != FALSE && $mode == 'linear'){
            $builder = $this->db->table('placement_settings');
            $builder->select('placement_settings.*');
            $builder->where('id', 3);
            $query = $builder->get();
            return $query->getRowArray(); 
        }else{
            $builder = $this->db->table('placement_settings');
            $builder->select('placement_settings.*'); 
            $builder->where('id', 1);
            $query = $builder->get();
            return $query->getRowArray(); 
      
        }
    }

	function get_learner_product_type($userid = FALSE){
		if($userid){
			$query = $this->db->query('SELECT cats_product FROM instituition_learners WHERE user_id = "'.$userid.'"');
			$result = $query->getRow();
			if($query->getNumRows() > 0){
			$catsProduct = $result->cats_product;
			return $catsProduct;
			}else{
			return FALSE;
			}
		}else{
			return FALSE;
		}
	}

    function get_booking_test($app_id = false) {
        if ($app_id) {
            $query = $this->db->query('SELECT thirdparty_id FROM user_products WHERE user_id = ' . $app_id);
            $results = $query->getResult();
            if ($query->getNumRows() > 0) {
                return $results;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

        //Questionaire only for step_levels
        public function get_questionaire_step_level(){
            $builder = $this->db->table('user_products up');
            $builder->select('tk.type_of_token,tk.questionnaire_done,tk.token, tk.product_id' );
            $builder->join('tokens tk', 'tk.thirdparty_id = up.thirdparty_id');
            $builder->where('up.user_id', $this->session->get('user_id'));
            $builder->where('tk.is_used', 1);
            $builder->where('tk.questionnaire_done', 0);
            $builder->orderBy('up.id', 'DESC');
            $builder->limit(1);
            $query = $builder->get();
            return $query->getRowArray();
        }
        public function get_placement_session_test_details($type_of_token = FALSE){	   
            
            $builder = $this->db->table('placement_session');
            $builder->select('placement_session.id, users.user_app_id, placement_session.token, placement_session.type_of_token, placement_session.datetime, from_unixtime(placement_session.datetime, "%d-%m-%Y") as formatdate' );
            $builder->join('users', 'users.user_app_id = placement_session.user_id');
            $builder->where('users.id', $this->session->get('user_id'));
            if($type_of_token != FALSE){
                $builder->where('placement_session.type_of_token', $type_of_token);
            }
            $builder->orderBy('datetime', "desc");
            $query = $builder->get();	    
            if($query->getNumRows() > 0){
                return $query->getResultArray();
            }else{
                return FALSE;
            }
        }
        public function get_learner_firstvisit_detail($userid = FALSE){
            if($userid){

                $builder = $this->db->table('tokens');
                $builder->select('tokens.first_visit,tokens.level');
                $builder->where('tokens.user_id', $userid);
                $builder->where('tokens.is_used', '1');
                $builder->where('tokens.questionnaire_done', '0');
                $builder->where('tokens.first_visit', '0');
                $query = $builder->get();
                if($query->getNumRows() > 0){
                    $result = $query->getRow();
                    return $result;
                }else{
                    return FALSE;
                }
            }
        }
        public function update_firstvisit_under16($userid = FALSE){
            if($userid){
                $data = array(
                    'first_visit'   => 1
                );
                if($userid != '0' && $userid !=''){
                    $builder = $this->db->table('tokens');
                    $builder->where('is_used',  $userid);
                    $builder->where('first_visit', '0');
                    $builder->update($data);
                    return TRUE;
                }
            }
        }
        public function get_benchmark_session_test_details($type_of_token = FALSE){	 
            $builder = $this->db->table('benchmark_session');   
	        $builder->select('benchmark_session.id, tokens.token, tokens.type_of_token, benchmark_session.datetime, from_unixtime(benchmark_session.datetime, "%d-%m-%Y") as formatdate' );
            $builder->join('tokens','tokens.token = benchmark_session.token');
            $builder->where('benchmark_session.user_id', $this->session->get('user_id'));
            $builder->orderBy('benchmark_session.datetime', "desc");
	    if($type_of_token != FALSE){
	        $builder->where('tokens.type_of_token', $type_of_token);
	    }
        $builder->orderBy('datetime', "desc");
	    $query = $builder->get();	    
	    if($query->getNumRows() > 0){
	        return $query->getResultArray();
	    }else{
	        return FALSE;
	    }
	}

            //get placement test of last  token  type
        public function get_recent_type_of_token()
        {
                $builder = $this->db->table('tokens');
                $builder->select('tokens.type_of_token,tokens.thirdparty_id,tokens.questionnaire_done, tokens.token, tokens.product_id, users.id, users.country, users.organisation_type, users.organization_name' );
                $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
                $builder->join('users', 'users.id = school_orders.school_user_id');
                $builder->where('tokens.user_id', $this->session->get('user_id'));
                $builder->where('tokens.questionnaire_done', 0);
                $builder->orderBy('tokens.id', 'DESC');
                $builder->limit(1);
                $query = $builder->get();
                return $query->getRowArray();
        }

       function get_spm_english_result($group = FALSE) {
        if($group != FALSE):
            $builder = $this->db->table('tokens');
            $builder->select('*');
            $builder->where('institutionGroupId', $group);
           $query = $builder->get();
           return $query->getResult();   
        endif;
    }  
    function get_departments($group = FALSE) {
        if($group != FALSE):
            $builder = $this->db->table('departments');
            $builder->select('*');
            $builder->where('institutionGroupId', $group);
            $builder->orderBy("englishTitle", "ASC");
            $query = $builder->get();
            return $query->getResult();
        endif;
    } 
     
    function get_level_of_study($group = FALSE) {
        if($group != FALSE):
            $builder = $this->db->table('level_of_study');
            $builder->select('*');
            $builder->where('institutionGroupId', $group);
            $builder->orderBy("englishTitle", "ASC");
           $query = $builder->get();
           return $query->getResult();   
        endif;
    }
    function get_reason_for_english_with_translations() {
        $builder = $this->db->table('reason_for_learning_english_translations');
        $builder->select('reason_for_learning_english.*, reason_for_learning_english_translations.translatedTitle');
        $builder->join('reason_for_learning_english', 'reason_for_learning_english_translations.reasonForLearningEnglishId = reason_for_learning_english.id');
        $builder->where('reason_for_learning_english_translations.language', 'en');
        $builder->orderBy("englishTitle", "ASC");
        $query = $builder->get();
        return $query->getResult();
    } 
    function get_no_of_years_in_english_with_translations() {

        $builder = $this->db->table('no_of_years_learning_english_translations');
        $builder->select('no_of_years_learning_english.*, no_of_years_learning_english_translations.translatedTitle');
        $builder->join('no_of_years_learning_english', 'no_of_years_learning_english_translations.noOfYearsLearningEnglishId = no_of_years_learning_english.id');
        $builder->where('no_of_years_learning_english_translations.language','en');
        $query = $builder->get();
        return $query->getResult();
    }

    function get_access_english_with_translations() {

        $builder = $this->db->table('access_any_medium_of_english_translations');
        $builder->select('access_any_medium_of_english.*, access_any_medium_of_english_translations.translatedTitle');
        $builder->join('access_any_medium_of_english', 'access_any_medium_of_english_translations.accessAnyMediumOfEnglishId = access_any_medium_of_english.id');
        $builder->where('access_any_medium_of_english_translations.language','en');
        $builder->orderBy("id", "ASC");
        $query = $builder->get();
        return $query->getResult();
    }

    function get_learning_english_perweek_with_translations() {

        $builder = $this->db->table('learning_english_per_week_translations');
        $builder->select('learning_english_per_week.*, learning_english_per_week_translations.translatedTitle');
        $builder->join('learning_english_per_week', 'learning_english_per_week_translations.learningEnglishPerWeekId = learning_english_per_week.id');
        $builder->where('learning_english_per_week_translations.language', 'en');
        $query = $builder->get();
        return $query->getResult();
    }
    function get_difficulties_in_english_with_translations() {
        $builder = $this->db->table('how_difficult_english_translations');
        $builder->select('how_difficult_english.*, how_difficult_english_translations.translatedTitle');
        $builder->join('how_difficult_english', 'how_difficult_english_translations.howDifficultEnglishId = how_difficult_english.id');
        $builder->where('how_difficult_english_translations.language', 'en');
        $query = $builder->get();
        return $query->getResult();
    }
    //for universities
    function get_muet_result($group = FALSE) {
        if($group != FALSE):
            $builder = $this->db->table('muet_result');
            $builder ->select('*');
            $builder ->where('institutionGroupId', $group);
            $query = $this->db->get();
            return $query->getResult(); 
        endif;
    } 

        //get faculties
        function get_faculites($institutionId = FALSE)
        {
            if($institutionId != FALSE):
                $builder = $this->db->table('institution_faculties');
                $builder->select('faculties.englishTitle, institution_faculties.institutionFacultyId');
                $builder->join('users', 'users.id = institution_faculties.institutionId');
                $builder->join('faculties', 'faculties.facultyId = institution_faculties.facultyId');
                $builder->where('users.id', $institutionId);
                $builder->orderBy("faculties.englishTitle", "ASC");
            else:
                $builder = $this->db->table('institution_faculties');
                $builder->select('faculties.englishTitle, institution_faculties.institutionFacultyId');
                $builder->join('faculties', 'faculties.facultyId = institution_faculties.facultyId');
                $builder->orderBy("faculties.englishTitle", "ASC");
            endif;
            $query = $builder->get();
            return $query->getResult();
        }

            //get user university details
    function get_user_unive_details($user_id = FALSE, $institution_id = FALSE) {
        if($user_id != FALSE && $institution_id != FALSE):
            $builder = $this->db->table('user_university_details');
            $builder->select('user_university_details.*');
            $builder->where("user_id", $user_id);
            $builder->where("institution_id", $institution_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        endif;
        
    }
        //get user university details completely
    function get_user_univ_faculty_department_programme_details($user_univ = FALSE)
    {
        if($user_univ != FALSE):
            $builder = $this->db->table('departments_v1_programmes');
            $builder->select('departments_v1_programmes.*');
            $builder->where('departments_v1_programmes.departmentProgrammeId', $user_univ['departmentProgrammeId']);
            $query_p = $builder->get();
            $programmeData = $query_p->getRowArray();
            if(isset($programmeData) && !empty($programmeData)):
                $builder = $this->db->table('faculty_departments');
                $builder->select('faculty_departments.*');
                $builder->where('faculty_departments.facultyDepartmentId', $programmeData['facultyDepartmentId']);
                $query_d = $this->db->get();
                $departmentData = $query_d->getRowArray();
                if(isset($departmentData) && !empty($departmentData)):
                    $builder = $this->db->table('institution_faculties');
                    $builder->select('institution_faculties.*');
                    $builder->where('institution_faculties.institutionFacultyId', $departmentData['institutionFacultyId']);
                    $query_f = $this->db->get();
                    $facultyData = $query_f->getRowArray();
                endif;
            endif;
        endif;
        return array('facultyData' => @$facultyData, 'departmentData' => @$departmentData, 'programmeData' => @$programmeData);
    }

        //get university departments
    function get_univ_departments($institutionFacultyId = FALSE)
    {
        if($institutionFacultyId != FALSE):
            $builder = $this->db->table('faculty_departments');
            $builder->select('departments_v1.englishTitle, faculty_departments.facultyDepartmentId');
            $builder->join('departments_v1', 'departments_v1.departmentId = faculty_departments.departmentId');
            $builder->where('faculty_departments.institutionFacultyId', $institutionFacultyId);
            $builder->orderBy("departments_v1.englishTitle", "ASC");
        endif;
        $query = $builder->get();
        return $query->getResult();
    }
        //get university programmesa
    function get_univ_programmes($facultyDepartmentId = FALSE)
    {
        if($facultyDepartmentId != FALSE):
            $builder = $this->db->table('departments_v1_programmes');
            $builder->select('programmes.englishTitle, departments_v1_programmes.departmentProgrammeId');
            $builder->join('programmes', 'programmes.programmeId = departments_v1_programmes.programmeId');
            $builder->where('departments_v1_programmes.facultyDepartmentId', $facultyDepartmentId);
            $builder->orderBy("programmes.englishTitle", "ASC");
        endif;
        $query = $builder->get();
        return $query->getResult();
    }


		//result display setting
		public function get_result_display_settings()
        {
            $builder = $this->db->table('result_display_settings');
			$builder->select('result_display_settings.*');
			$query = $builder->get();
			return $query->getRowArray();
        }


    public function get_test_delivery_detail($testFormType){
        $builder = $this->db->table('tds_test_detail');
        $builder->select('tds_test_detail.*');
        $builder->where('test_slug', $testFormType);
        $query = $builder->get();
        $result = $query->getRow();
        if($query->getNumRows() > 0){
    		return $result;
		}else{
    		return FALSE;
		}
	}

    function get_distributor_by_learnerid($userid = FALSE){
		if($userid){
			$query = $this->db->query('SELECT SD.distributor_id FROM instituition_learners IL JOIN school_distributor SD ON SD.school_user_id = IL.instituition_id WHERE IL.user_id = "'.$userid.'" AND SD.setdefault = 1');
			$result = $query->getRowObject();
			if($query->getNumRows() > 0){
			$distributorId = $result->distributor_id;
			return $distributorId;
			}else{
			return FALSE;
			}
		}else{
			return FALSE;
		}
	}

    public function get_primary_placement_sessions()
    {     
        $builder = $this->db->table('primary_placement_session');  
        $builder->select('primary_placement_session.*');
        // $this->db->from('primary_placement_session');
        $builder->where('user_id', $this->session->get('user_app_id'));
        $query = $builder->get();
        //echo $this->db->getLastQuery(); die;
        return $query->getRowArray();
    }

    public function get_product_group_settings($active = false)
    {
        if($active != false){
            $builder = $this->db->table('product_group_settings');  
            $builder->select('*');
            $builder->where('active', $active);
            $query = $builder->get();
        }else{
            $builder = $this->db->table('product_group_settings'); 
            $builder->select('*');
            $query = $builder->get();
        }
        return $query->getResult();
    }

}

