<?php 

namespace App\Models\Teacher;

use CodeIgniter\Model;
use App\Models\Usermodel;
use App\Models\Site\Bookingmodel;
class Teachermodel extends Model
{

    public function __construct()
	{
        $this->db = \Config\Database::connect();
        $this->usermodel = new Usermodel();
        $this->session = \Config\Services::session();
        $this->bookingmodel = new Bookingmodel();
        $this->request = \Config\Services::request();
        helper('percentage_helper');
        helper('downtime_helper');
	
	}
    /* Function To get user_tiers */
    public function get_user_tiers() {

        if($this->session->get('user_id')){
           $builder = $this->db->table('institution_tier_users');
           $builder->select('institution_tiers.*');
           $builder->join('users', 'institution_tier_users.user_id = users.id');
           $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
           $builder->join('institution_teachers', 'institution_teachers.institutionId = institution_tier_users.user_id');
           $builder->where('institution_teachers.teacherId', $this->session->get('user_id'));
           $query = $builder->get();
           return $query->getRowArray();
        }
   }
    /* Function To get teachers class with active learners */
    function get_teacher_class_active_learners() {
        
        $builder = $this->db->table('classes');
        $builder->select('*');
        $builder->join('teacher_classes', 'teacher_classes.classId = classes.classId');
        $builder->join('institution_teachers', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId');
        $builder->where('institution_teachers.institutionId', $this->session->get('user_id'));
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $row) {
                $rowArr[$row->teacherId][] = $row;
            }
            return $rowArr;
        } else {
            return [];
        }
}

    /* Function To get teacher_classes */
    function get_teacher_classes($institutionTeacherId = FALSE) {

        if ($institutionTeacherId != FALSE):
            $builder = $this->db->table('teacher_classes');
            $builder->select('teacher_classes.*');
            $builder->where('teacher_classes.institutionTeacherId', $institutionTeacherId);
            $query = $builder->get();
                return $query->getResult();
        endif;

    }

    /* Function To get record_classes_count */
    public function record_classes_count($search_item, $classtatus = false) {
        $builder = $this->db->table('teacher_classes');
        $builder->join('institution_teachers', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId');
        $builder->join('classes', 'classes.classId = teacher_classes.classId');
        $builder->where('institution_teachers.teacherId', $this->session->get('user_id'));
		if(!empty($classtatus)) {
			$builder->where('classes.status', $classtatus);
		}		
        $count = $builder->countAllResults();
        return $count;
    }
    /* Function To fetch_classes */
    public function fetch_classes($limit, $start, $search_item, $classtatus = false) {

        $builder = $this->db->table('teacher_classes');
        $builder->limit($limit, $start);
        
        $builder->join('institution_teachers', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId');
        $builder->join('classes', 'classes.classId = teacher_classes.classId');
        $builder->where('institution_teachers.teacherId', $this->session->get('user_id'));
		if(!empty($classtatus)) {
			$builder->where('classes.status', $classtatus);
		}
        $builder->orderBy("classes.classId", "DESC");
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $row) {
                $rowArr[] = $row;
            }
            return $rowArr;
        }
        return FALSE;
    }

    /* Function To get class_association_learner_ids */
    function get_class_association_learner_ids()
    {
        $teacherData = $this->usermodel->get_teacher($this->session->get('user_id'));
        if(!empty($teacherData))
        {
            $tier_id = $this->usermodel->get_user_tier_id($teacherData['institutionId']);

            $query = $this->db->query("SELECT `student_classes`.`userId`, `student_classes`.`thirdparty_id` FROM `student_classes` JOIN `teacher_classes` ON `teacher_classes`.`teacherClassId` = `student_classes`.`teacherClassId` JOIN `institution_teachers` ON `institution_teachers`.`institutionTeacherId` = `teacher_classes`.`institutionTeacherId` JOIN `institution_tier_users` ON `institution_tier_users`.`user_id` = `institution_teachers`.`institutionId` WHERE `institution_tier_users`.`institutionTierId` = " . $tier_id . "");
            if ($query->getNumRows() > 0) {
                $results = $query->getResult();
                foreach ($results as $result):
                    $rowData[] = $result->thirdparty_id;
                endforeach;
                return $rowData;
            }else {
                return [];
            }
        }
    }
    /* Function To get consumed_tokens_by_institution */
    function get_consumed_tokens_by_institution()
    {
        $search_item = (isset($_GET['search']) && $_GET['search'] != '') ? $_GET['search'] : '';
        if (strpos($search_item, substr($search_item, -1)) !== false) {
            $search_item = preg_replace('/\\\\/', '', $search_item);
        }
        $teacherData = $this->usermodel->get_teacher($this->session->get('user_id'));
      
        $tierData = $this->get_user_tiers();

		 if (isset($teacherData) && !empty($teacherData) && $search_item != '') {
             
            $query =$this->db->query("SELECT `CL`.`TR_fname`, `CL`.`TR_lname`, `CL`.`class_name`, `tokens`.`token`, `UP`.`id`, `UP`.`firstname`, `UP`.`lastname`, `UP`.`email`, `UP`.`username`, `UP`.`dob`, `UP`.`last_thirdparty_id`, `UP`.`userproducts_id`,`institution_tier_users`.`institutionTierId`,`institution_tiers`.`organization_name` FROM `tokens` 
            JOIN `school_orders` ON `school_orders`.`id` = `tokens`.`school_order_id` 
                LEFT JOIN `institution_tier_users` ON `institution_tier_users`.`user_id` = `school_orders`.`school_user_id`
                LEFT JOIN `institution_tiers` ON `institution_tiers`.`id` = `institution_tier_users`.`institutionTierId`
            JOIN ( SELECT o1.id, o1.lastname,o1.email, o1.username, o1.dob, o1.firstname, o2.userproducts_id, o2.last_thirdparty_id FROM users o1 
            JOIN ( SELECT user_id,MAX(thirdparty_id) as last_thirdparty_id, MAX(id) as userproducts_id FROM user_products WHERE user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id) GROUP BY user_id ORDER BY user_products.id DESC ) o2 on o1.id = o2.user_id WHERE (o1.name LIKE '%".$search_item."%') OR (o1.email  LIKE '".$search_item."')) UP ON `UP`.`last_thirdparty_id` = `tokens`.`thirdparty_id` 
            LEFT JOIN `tds_results` ON `tds_results`.`candidate_id` = `UP`.`last_thirdparty_id` 
            LEFT JOIN `collegepre_results` ON `collegepre_results`.`thirdparty_id` = `UP`.`last_thirdparty_id` 
            LEFT JOIN `collegepre_higher_results` ON `collegepre_higher_results`.`thirdparty_id` = `UP`.`last_thirdparty_id` 
            LEFT JOIN `collegepre_formcodes`ON `collegepre_formcodes`.`form_id` = `collegepre_results`.`form_id`
            LEFT JOIN (SELECT cu.firstname as TR_fname,cu.lastname as TR_lname, c1.englishTitle as class_name, c2.classId, c2.teacherClassId, c3.thirdparty_id FROM classes c1 
            JOIN teacher_classes c2 on c1.classId = c2.classId 
            JOIN institution_teachers ct on ct.institutionTeacherId = c2.institutionTeacherId 
            JOIN users cu on cu.id = ct.teacherId JOIN student_classes c3 on c3.teacherClassId = c2.teacherClassId ) CL ON `CL`.`thirdparty_id` = `tokens`.`thirdparty_id` WHERE `institution_tier_users`.`institutionTierId` = ".$tierData['id']." AND `school_orders`.`order_type` != 'under13' AND `tokens`.`type_of_token` != 'benchmarktest' AND `tokens`.`is_used` = '1' AND ((`collegepre_results`.`thirdparty_id` IS NULL) OR (`collegepre_formcodes`.`type` = 'Practice test')) AND (`collegepre_higher_results`.`thirdparty_id` IS NULL) AND (`tds_results`.`candidate_id` IS NULL)  
            UNION 
            DISTINCT SELECT `CL`.`TR_fname`, `CL`.`TR_lname`, `CL`.`class_name`,'u13token', `UP`.`id`, `UP`.`firstname`, `UP`.`lastname`, `UP`.`email`, `UP`.`username`, `UP`.`dob`, `UP`.`last_thirdparty_id`, `UP`.`userproducts_id`,`institution_tier_users`.`institutionTierId`,`institution_tiers`.`organization_name` FROM `instituition_learners` 
        JOIN ( SELECT o1.id, o1.lastname,o1.email,o1.username, o1.dob, o1.firstname, o2.userproducts_id, o2.last_thirdparty_id, o2.user_id FROM users o1 
        JOIN ( SELECT user_id,MAX(thirdparty_id) as last_thirdparty_id, MAX(id) as userproducts_id FROM user_products WHERE user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id) GROUP BY user_id ORDER BY  user_products.id DESC ) o2 on o1.id = o2.user_id WHERE (o1.name LIKE '%".$search_item."%') OR (o1.email  LIKE '".$search_item."')) UP ON `UP`.`user_id` = `instituition_learners`.`user_id` 
         LEFT JOIN `institution_tier_users` ON `institution_tier_users`.`user_id` = `instituition_learners`.`instituition_id`
         LEFT JOIN `institution_tiers` ON `institution_tiers`.`id` = `institution_tier_users`.`institutionTierId`
         LEFT JOIN `tds_results` ON `tds_results`.`candidate_id` = `UP`.`last_thirdparty_id` 
         LEFT JOIN `collegepre_results` ON `collegepre_results`.`thirdparty_id` = `UP`.`last_thirdparty_id` 
        LEFT JOIN `collegepre_higher_results` ON `collegepre_higher_results`.`thirdparty_id` = `UP`.`last_thirdparty_id` 
        LEFT JOIN `collegepre_formcodes`ON `collegepre_formcodes`.`form_id` = `collegepre_results`.`form_id`
        LEFT JOIN (SELECT cu.firstname as TR_fname,cu.lastname as TR_lname, c1.englishTitle as class_name, c2.classId, c2.teacherClassId, c3.thirdparty_id, c3.userId FROM classes c1 
        JOIN teacher_classes c2 on c1.classId = c2.classId 
        JOIN institution_teachers ct on ct.institutionTeacherId = c2.institutionTeacherId 
        JOIN users cu on cu.id = ct.teacherId 
        JOIN student_classes c3 on c3.teacherClassId = c2.teacherClassId ) CL ON `CL`.`userId` = `instituition_learners`.`user_id` WHERE `institution_tier_users`.`institutionTierId` = ".$tierData['id']." AND ((`collegepre_results`.`thirdparty_id` IS NULL) OR (`collegepre_formcodes`.`type` = 'Practice test')) AND (`collegepre_higher_results`.`thirdparty_id` IS NULL)   AND (`tds_results`.`candidate_id` IS NULL) "
        );

		if ($query->getNumRows() > 0) {
            $thirdparty_id = array();
            $rowArr = array();
			foreach ($query->getResult() as $key=>$value) {
                if(!in_array($value->last_thirdparty_id, $thirdparty_id)){
                    $thirdparty_id[] = $value->last_thirdparty_id;
                    $product_values =  $this->get_level_progress($value->userproducts_id);
                    if($product_values->course_type != "Higher"){
                        /* practice test */
                       $value->practice_details = $this->get_practice_test_details($value->last_thirdparty_id , $product_values->course_type);
                    }
                    $value->course_type = $product_values->course_type;
                    $value->level = $product_values->name; 
                    $value->course_progress = $product_values->course_progress; 
                    $rowArr[$key] = $value;
                }
            }
			return $rowArr;
		}		
		} else {
            return [];
        } 

    }

    /* Function To get level_progress */
    function get_level_progress($product_id) {
        if($product_id > 0){
            $query =  $this->db->query('SELECT P.course_type,P.name,UP.course_progress FROM user_products UP join products P on UP.product_id = P.id WHERE UP.id ='.$product_id);
            $product_details = $query->getRow();
            return $product_details;
        }
    }
    /* Function To get practice_test_details */
    function get_practice_test_details($thirdparty_id,$course_type){
        $tokens = new \stdClass();
            /* collegepre primary */
            if($course_type == "Primary"){
                if ($thirdparty_id != NULL && $thirdparty_id != 0) {

                    $builder = $this->db->table('collegepre_results');
                    $builder->select('collegepre_results.*');
                    $builder->join('collegepre_formcodes', 'collegepre_formcodes.form_id = collegepre_results.form_id', 'left');
                    $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                    $builder->where('collegepre_formcodes.type = "Practice test"');
                    $query = $builder->get();
                    $result_practice = $query->getRow();
                    if ($result_practice != NULL) {
                    if (count($result_practice) > 0) {
                        $practice_result_details['percent'] = @get_primary_results($result_practice->section_one, $result_practice->section_two);
                        $tokens->collegepre_primary_results  = $practice_result_details['percent']['percentage']; 
                    }else{
                        $tokens->collegepre_primary_results  = "";   
                    }
                   }else{
                     $tokens->collegepre_primary_results  = "";   
                   }
                } 
            }
            /* collegepre core */
            $query = $this->db->query('SELECT thirdparty_id,session_number FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $thirdparty_id . '" ');
            $results = $query->getResultArray();
            $tokens->practiceresults = $results;       
        
        /* TDS PRACTICE TEST RESULTS */
        $practice_tds_results = $this->bookingmodel->tds_practice_detail($thirdparty_id , 'practice');
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
            /* TDS-366 */
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
        /* WP-1308 Starts */
        $tds_test_query = $this->db->query('SELECT candidate_id,test_type FROM `tds_tests` WHERE candidate_id = "' . $thirdparty_id . '" AND test_type = "practice" ');
        if ($tds_test_query->getNumRows() > 0) { 
            $tokens->practice_count = $tds_test_query->getNumRows();
        }else{
            if($course_type == "Primary"){
                $tokens->practice_count = 1;
            }else{
                $cp_test_query = $this->db->query('SELECT CB.test_number, CB.thirdparty_id FROM collegepre_batch_add CB JOIN collegepre_formcodes CF ON CF.test_number = CB.test_number WHERE CB.thirdparty_id = "' . $thirdparty_id . '" AND CF.type = "Practice test"');
                if ($cp_test_query->getNumRows() > 0) {
                    $tokens->practice_count = $cp_test_query->getNumRows();
                }else{
                    $tokens->practice_count = 0;
                }
            }
        }
        return $tokens;
    }
	

    /* Function To get classes management */
    function get_class($class_id = FALSE) {
        if ($class_id != FALSE):
            $builder = $this->db->table('teacher_classes');
            $builder->select('classes.*, teacher_classes.teacherClassId');
            $builder->join('institution_teachers', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId');
            $builder->join('classes', 'classes.classId = teacher_classes.classId');
            $builder->where('institution_teachers.teacherId', $this->session->get('user_id'));
            $builder->where('classes.classId', $class_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }
        endif;
    }
    /* Function To get record_learners_count  */
    function record_learners_count($teacherClassId)
    {
        if ($teacherClassId != FALSE){
            $product_types_rec = array('cats_core_or_higher','cats_core','cats_higher','catslevel');
            $builder = $this->db->table('student_classes');
            $builder->join('users', 'student_classes.userId = users.id');
            $builder->join('tokens', 'tokens.user_id = student_classes.userId');
            $builder->where('student_classes.teacherClassId', $teacherClassId);
            $builder->whereIn('tokens.type_of_token', $product_types_rec);
            $builder->where('tokens.is_used', '1');
            $count = $builder->countAllResults();
            return $count;
        }else{}
      
    }

    /* Teacher Dashboard : To create group unique name validation  */
    public function check_cls_name() {

        $teacher = $this->usermodel->get_teacher($this->session->get('user_id'));
        $class_name = $this->request->getPost('classname');

        $builder = $this->db->table('teacher_classes');
        $builder->select('classes.*');
        $builder->join('classes', 'classes.classId = teacher_classes.classId', 'left');
        $builder->where('teacher_classes.institutionTeacherId = "' . $teacher['institutionTeacherId'] . '"');
        $builder->where('classes.englishTitle = "'.$class_name.'"');
        $query = $builder->get();
        return $query;
    }

    /* Function To insert class  */
    public function insert_class($data) {

        $this->db->transStart();
        $builder = $this->db->table('classes');
        $builder->insert( $data);
        $insert_id = $this->db->insertID();

        /* class teacher relations */
        $teacher = $this->usermodel->get_teacher($this->session->get('user_id'));
        $dataClass = array('classId' => $insert_id, 'institutionTeacherId' => $teacher['institutionTeacherId']);
        $builder = $this->db->table('teacher_classes');
        $builder->insert( $dataClass);
        $this->db->transComplete();

        return $insert_id;
    }
    /* Function To update class  */
    public function update_class($id, $dataClass) {
        $builder = $this->db->table('classes');
        $builder->where('classId', $id);
        $affectedrows = $builder->update($dataClass);
        if ($affectedrows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* Function To get student_classes */
    public function get_student_classes($learner_id = FALSE, $thirdparty_id = FALSE, $teacherClassId = FALSE) {
        if($teacherClassId != FALSE && $learner_id != FALSE && $thirdparty_id != FALSE){
            $builder = $this->db->table('student_classes');
            $builder->select('student_classes.*');
            $builder->where('thirdparty_id', $thirdparty_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }
        }
    }
    /* Function To fetch_learners */
    public function fetch_learners($limit, $start, $teacherClassId) {

        $product_types = array('cats_core_or_higher','cats_core','cats_higher','catslevel');
        $builder = $this->db->table('student_classes');
        $builder->limit($limit, $start);
        $builder->join('users', 'student_classes.userId = users.id');
        $builder->join('tokens', 'tokens.user_id = student_classes.userId');
        $builder->where('student_classes.teacherClassId', $teacherClassId);
        $builder->whereIn('tokens.type_of_token', $product_types);
        $builder->where('tokens.is_used', '1');
        $builder->orderBy("users.firstname", "ASC");
        $query = $builder->get();

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $row) {
                $rowArr[] = $row;
            }
            return $rowArr;
        }
        return FALSE;
    }
    /* Function To update no_in_class_by_teacher_class_id */
    public function update_no_in_class_by_teacher_class_id($status, $teacherClassId = FALSE) {
        if ($teacherClassId != FALSE && $status != '') {
            $builder = $this->db->table('classes');
            $builder->select('classes.*');
            $builder->join('teacher_classes', 'classes.classId = teacher_classes.classId');
            $builder->where('teacher_classes.teacherClassId', $teacherClassId);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                $class = $query->getRowArray();
                if($status == 'add'):
                    $dataClass = array('number_in_class' => $class['number_in_class'] + 1);
                elseif($status == 'remove'):
                    $dataClass = array('number_in_class' => $class['number_in_class'] - 1);
                endif;
                $builder->where('classId', $class['classId']);
                $builder->update($dataClass);
            }
        }
    }

    /* Function to get student classes by teacher Class id */
    public function get_student_class_by_student_class_id($studentClassId = FALSE) {
        if($studentClassId != FALSE){
            $builder = $this->db->table('student_classes');
            $builder->select('student_classes.*');
            $builder->where('studentClassId', $studentClassId);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }
        }
    }
}




