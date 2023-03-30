<?php

namespace App\Models;//path

//namespace App\Models\Site;//path
use CodeIgniter\Model;
use App\Models\Site\Bookingmodel;
use Config\MY_Lang;
use Config\Site;

class Usermodel extends Model {
    
	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
        $this->session = session();

        $this->bookingmodel = new Bookingmodel();
        $this->mylang = new MY_Lang();
        $this->site = new Site();
        $this->request = \Config\Services::request();
	}


    //count
    public function record_institutions_count($search_item=FALSE) {
        $builder = $this->db->table('institution_tiers');
        $builder->select('institution_tiers.*,institutiongroup.*, tiers.id as  TierID, tiers.name as TierName, regions.name as regionName, countries.countryName' );
        //regions and countries
        $builder->join('regions', 'regions.regionCode = institution_tiers.region', 'left');
        $builder->join('countries', 'countries.countryCode = institution_tiers.country', 'left');        
        $builder->join('institutiongroup', 'institutiongroup.institutionGroupId = institution_tiers.organisation_type', 'left');
        //joining the tiers 
        $builder->join('tiers', ' institution_tiers.tierId = tiers.id', 'left');
        if($search_item != ''):
            $builder->like('institution_tiers.organization_name', $search_item);
        endif; 
        $count = $builder->countAllResults();	
		return $count;
                
    }

    public function fetch_institutions($limit, $start, $search_item) {

        $builder = $this->db->table('institution_tiers');
        $builder->limit($limit, $start);
        $builder->select('institution_tiers.*,institutiongroup.*, tiers.id as  TierID, tiers.name as TierName, regions.name as regionName, countries.countryName, COUNT(institution_tier_users.user_id) as usercount' );
      
        if($search_item != ''):
            $builder->like('institution_tiers.organization_name', $search_item);
        endif;
       
        //regions and countries
        $builder->join('regions', 'regions.regionCode = institution_tiers.region', 'left');
        $builder->join('countries', 'countries.countryCode = institution_tiers.country', 'left');
        
        $builder->join('institutiongroup', 'institutiongroup.institutionGroupId = institution_tiers.organisation_type', 'left');
        //joining the tiers 
        $builder->join('tiers', ' institution_tiers.tierId = tiers.id', 'left');
        $builder->join('institution_tier_users', ' institution_tiers.id = institution_tier_users.institutionTierId', 'left');
        
        if($this->request->getVar('order') == 'ASC'){
            $builder->orderBy("institution_tiers.organisation_type", "ASC");
        }elseif($this->request->getVar('order') == 'DESC'){
            $builder->orderBy("institution_tiers.organisation_type", "DESC");
        }else{
            $builder->orderBy("institution_tiers.id", "DESC");
        }
        $builder->groupBy("institution_tiers.id", "DESC");
       
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $row) {
                $rowArr[] = $row;
            }
            return $rowArr;
        }
        return FALSE;
    }

    public function username_exists($username) {
        $builder = $this->db->table('users');
        if (str_contains($username, '@')) {
            $builder->where('email', $username);
        } else {
            $builder->where('username', $username);
        }
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function email_exists($username) {
        $builder = $this->db->table('users');
        if (str_contains($username, '@')) {
            $builder->where('email', $username);
            $builder->where('google_id', '');
            $builder->where('facebook_id', '');
        } else {
            $builder->where('username', $username);
            $builder->where('google_id', '');
            $builder->where('facebook_id', '');
        }
        $query =  $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getResult();
        } else {
            return false;
        }
    }

    public function is_temp_pass_valid($temp_pass) {
        $builder = $this->db->table('users');
        $builder->where('temp_pass', $temp_pass);
      
        $query = $builder->get();
       
        if ($query->getNumRows() == 1) {
            return $query->getResultArray();
        } else {
            return FALSE;
        }
    }

    public function update_tempass($values) {
        $id = $values['id'];
        $data = array(
            'temp_pass' => $values['temp_pass']
        );
        if ($data) {
            $builder = $this->db->table('users');
            $builder->where('id', $id);
            $builder->update($data);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update_password($values) {
        $id = $values['id'];
        $data = array(
            'password' => $values['password']
        );
        if ($data) {
            $builder = $this->db->table('users');
            $builder->where('id', $id);
            $builder->update($data);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_user($identity) {
        if (!$identity) {
            return false;
        }
        if (str_contains($identity,'@')) {
            $user = 'email';
        } 
        else {
            $user =  'username';
        }
        $builder = $this->db->table('users');
        $builder->select('*');
        $builder->where($user ,$identity);
        $query = $builder->get()->getRowArray();
        return $query;
    }

    public function get_useradmin($identity) {
        if (!$identity) {
            return false;
        }
        $builder = $this->db->table('users');
        $builder->select('*');
        $builder->Where('username', $identity);
        $query = $builder->get()->getResult();
        return $query;
    }


    public function has_role($user,$role) {

        $builder = $this->db->table('user_roles');
        $builder->select('user_roles.roles_id, user_roles.users_id, roles.name');
        $builder->join('roles', 'roles.id = user_roles.roles_id');
        $builder->where('users_id', $user);
        $builder->where('roles.name', $role);
        $query = $builder->get();
        return $query->getResult();
    }


        //tiers details
        public function get_tiers($tierId = FALSE){
            if($tierId != FALSE){
                $builder = $this->db->table('institution_tiers');
                $builder->where('tierId', $tierId);
                $query =  $builder->get();
            }else{
                $builder = $this->db->table('tiers');
                $query = $builder->get();
            }
            return $query->getResult();
        }

        function get_institute_courseType($institute_id = FALSE) {
            if($institute_id!=FALSE):
                $builder = $this->db->table('institution_eligible_products');
                $builder->select('institution_eligible_products.group_id, institution_eligible_products.tds_option'); //Get tds_option WP-1202
                $builder->where('institutionTierId', $institute_id);
               $query = $builder->get();
               if ($query->getNumRows() > 0) {
                    return $query->getResultArray(); 
               }
           endif;
        }

            //get regions from country code
    public function get_regions_by_countrycode($cc) {
        if ($cc != FALSE) {
            $builder = $this->db->table('regions');
            $builder->select('regions.*');
            $builder->where('countryCode', $cc);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getResult();
            }else{
                return FALSE;
            }
        }else{
            $builder = $this->db->table('regions');
            $builder->where('countryCode', $cc);
            $query = $builder->get();
            return $query->getResult();
        } 
    }

    function get_institute($institute_id = FALSE) {
        if($institute_id!=FALSE):
            $builder = $this->db->table('institution_tiers');
            $builder->select('institution_tiers.*');
            $builder->where('id', $institute_id);
           $query = $builder->get();
           if ($query->getNumRows() > 0) {
                return $query->getRowArray(); 
           }
           
       endif;
    }

        //get the tier relations
        function get_tier_relations($institute_id = FALSE) {
            if($institute_id!=FALSE):
               $builder = $this->db->table('institution_internal_tiers');
               $builder ->select('institution_tiers.id as InstituteID, tiers.id as TierID');
               $builder ->join('institution_tiers', 'institution_internal_tiers.relinstitutionTierId = institution_tiers.id', 'left');
               $builder ->join('tiers', 'institution_tiers.tierId = tiers.id', 'left');
               $builder ->where('institution_internal_tiers.institutionTierId', $institute_id);
               $query = $builder->get();
               if ($query->getNumRows() > 0) {
                    $tierData = $query->getResult(); 
                    $fetchData = [];
                    foreach($tierData as $tierinfo):
                        $fetchData[$tierinfo->TierID] = $tierinfo;
                    endforeach;
                    return $fetchData;
               }
            endif;
        }

        public function insert_institute($data) {
            $data_course_types = $data['product_group'];
            unset($data['product_group']);
            $tds_options = $data['tds_options'];
            unset($data['tds_options']);
 
            $builder = $this->db->table('institution_tiers');
            $builder->insert($data);
            $insert_id = $this->db->insertID();
            
            if($data['tierId'] == 3){
                foreach ($data_course_types as $data_course_type) {

                    $datainstitution= [
                        'institutionTierId'=> $insert_id, 
                        'group_id'=> $data_course_type, 
                        'tds_option'=> $tds_options[$data_course_type]
                    ]; //Insert tds_option WP-1202
                    $builder = $this->db->table('institution_eligible_products');
                    $builder->insert($datainstitution);
                    //$insert_id = $this->db->insertID();
                }
            }
            return $insert_id;
        }
        public function chk_role($userid) {
            $builder = $this->db->table('user_roles');
            $builder->select('user_roles.roles_id, user_roles.users_id, roles.name');
            $builder->join('roles', 'roles.id = user_roles.roles_id');
            $builder->where('users_id', $userid);
            $query = $builder->get();
            return $query->getResultArray();
        }


        public function check_role($userid,$role) {
            $builder = $this->db->table('user_roles');
            $builder->select('user_roles.roles_id, user_roles.users_id, roles.name');
            $builder->join('roles', 'roles.id = user_roles.roles_id');
            $builder->where('users_id', $userid);
            $builder->where('roles.name',$role);
            $query = $builder->get();
            return $query->getResultArray();
        }

        public function update_profile($data) {
       
            if(!empty($data))
            {
                $userdata = $this->session->get();
                $builder = $this->db->table('users');
                $builder->where('id', $userdata['user_id']);
                $builder->update($data);
            }
           
            if(isset($data['firstname']) && isset($data['firstname'])){
                $data = array('user_name' => $data['firstname']." ".$data['lastname']);
                $builder = $this->db->table('tokens');
            $builder->where('user_id', $userdata['user_id']);
            $builder->update($data);
               // $this->db->update('tokens', array('user_name' => $data['firstname']." ".$data['lastname']), array('user_id' => $userdata['user_id']));
            }
            /* if($this->db->affected_rows()>0)
              {
              return TRUE;
              }else{
              return FALSE;
              } */
            return TRUE;
        }

        public function get_profile($id = FALSE) {
            if ($id != FALSE) {
                $builder = $this->db->table('users'); 
                $query = $builder->getWhere(['id' => $id]);
                return $query->getResult();
            } else {
                show_404();
            }
        }

        public function get_sectors($ministry_id = FALSE) {
            if ($ministry_id != FALSE):
                $builder = $this->db->table('ministry_sectors');
                $query = $builder->where('ministry_id', $ministry_id);
                $query = $builder->get();
                if ($query->getNumRows() > 0) {
                    return $query->getResultArray();
                } else {
                    return FALSE;
                }
            endif;
        }
    
        public function fetch_tier_userlist($tieruser_id, $condition, $search_item, $limit, $start) {
            $builder = $this->db->table('institution_tier_users');
            $builder->limit($limit, $start);
            $builder->select('institution_tier_users.id, institution_tier_users.institutionTierId, institution_tier_users.user_id,institution_tier_users.status, users.firstname, users.lastname, users.email, users.department, users.last_logged');
            if($search_item != ''){
                $builder->like('users.name', $search_item);
            }       
            $builder->join('users', 'users.id = institution_tier_users.user_id', 'left');        
            $builder->where('institution_tier_users.institutionTierId', $tieruser_id );
            $builder->where('institution_tier_users.user_id NOT IN (select admin_user_id from institution_tier_admins)');
            if($condition == 0 && $search_item == ''){
                $builder->where('institution_tier_users.status',1);
               }
            $query =  $builder->get();
            $rowArr = array();       
            if ($query->getNumRows() > 0) {
                foreach ($query->GetResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }

        public function tier_userlist_count($tieruser_id, $search_item = false) {
            $builder = $this->db->table('institution_tier_users');
            $builder->select('institution_tier_users.id, institution_tier_users.institutionTierId, institution_tier_users.user_id, users.firstname, users.lastname, users.email, users.department, users.last_logged');
            
            if($search_item != ''){
                $builder->like('users.name', $search_item);
            }       
            $builder->join('users', 'users.id = institution_tier_users.user_id', 'left');        
            $builder->where('institution_tier_users.institutionTierId', $tieruser_id );
            $count = $builder->countAllResults();
            return $count;
                    
        }	

        public function update_institute($id, $data) {
            $data_course_types = $data['product_group'];
            unset($data['product_group']);
            $tds_options = $data['tds_options'];
            unset($data['tds_options']);
            $builder = $this->db->table('institution_tiers');
			$builder->where('id', $id); 
			$builder->update($data);
            if($data['tierId'] == 3){
                $builder = $this->db->table('institution_eligible_products');
                $builder->where('institutionTierId', $id);
                $builder->delete();

                foreach ($data_course_types as $data_course_type) {


                    $datainstitution= [
                         'institutionTierId'=> $id, 
                         'group_id'=> $data_course_type,
                         'tds_option'=> $tds_options[$data_course_type]
                        ]; //Update tds_option WP-1202
                    $builder = $this->db->table('institution_eligible_products');
                    $builder->insert($datainstitution);


                } 
            }
         return TRUE;
         
        }

        public function check_test_form_id($data) {
            $builder = $this->db->table('tds_test_detail TTD');
            $builder->select('test_formid');
            $builder->where('test_formid',$data);
            $query =  $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getResultArray();
            }else{
                return FALSE;
            }
        }

        public function check_form_id_exist($form_id) {
            $builder = $this->db->table('tds_test_detail');
            $builder->select('*');
            $builder->where('test_formid', $form_id);
            $query =  $builder->get();
            return $results = $query->getResult();
        }

        public function get_valid_test_form_version() {
            $builder = $this->db->table('tds_test_forms');
            $builder->select('*');
            $query =  $builder->get();
            return $results = $query->getResult();
        }

        public function get_user_tiers() {
            if($this->session->get('user_id')){
                $builder = $this->db->table('institution_tier_users');
                $builder->select('institution_tiers.*');
                $builder->join('users', 'institution_tier_users.user_id = users.id');
                $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
                $builder->where('institution_tier_users.user_id', $this->session->get('user_id'));
               $query = $builder->get();
               return $query->getRowArray();
            }
       }

        public function fetch_teachers( $limit, $start, $search_item) {
            $tierData = $this->get_user_tiers();
            $builder = $this->db->table('users');
            $builder->limit($limit, $start);
            $builder->select('users.*, institution_teachers.*,   GROUP_CONCAT( COALESCE(teacher_classes.teacherClassId, "NULL") SEPARATOR "@" ) as teacher_class_id, teacher_classes.classid as teacher_class_classid, classes.classid as class_id, GROUP_CONCAT( COALESCE(`classes`.`classid`, "NULL") SEPARATOR "@") as class_ids,  classes.englishTitle as class_name, GROUP_CONCAT( COALESCE(`classes`.`englishTitle`, "NULL") SEPARATOR "@") as classes, GROUP_CONCAT( COALESCE(`classes`.`status`, "NULL") SEPARATOR "@") as class_status,institution_tier_users.institutionTierId,institution_tiers.organization_name');
            if ($search_item != ''):
                $builder->like('users.name', $search_item);
            endif;
            $builder->join('user_roles', 'user_roles.users_id = users.id');
            $builder->join('institution_teachers', 'institution_teachers.teacherId = users.id');
            $builder->join('teacher_classes', 'institution_teachers.institutionTeacherId = teacher_classes.institutionTeacherId', 'left');
            $builder->join('classes', 'teacher_classes.classid =  classes.classid', 'left');
            //for tier multiple users
            $builder->join('institution_tier_users', 'institution_tier_users.user_id = institution_teachers.institutionId','left');
            $builder->join('institution_tiers', 'institution_tiers.id = institution_tier_users.institutionTierId');
            $builder->where('user_roles.roles_id', '6');
            $builder->where('users.is_active', '1');
           // $this->db->where('institution_teachers.institutionId', $this->session->userdata('user_id'));
           $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
           $builder->groupBy('users.id');
           $builder->orderBy("users.id", "DESC");
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }     
//teachers management
    function get_teacher($teacher_id = FALSE) {

        if ($teacher_id != FALSE):
            $builder = $this->db->table('users');
            $builder->select('users.*,institution_teachers.institutionTeacherId, institution_teachers.institutionId');
            $builder->join('user_roles', 'user_roles.users_id = users.id');
            $builder->join('institution_teachers', 'institution_teachers.teacherId = users.id');
            $builder->where('user_roles.roles_id', '6');
            $builder->where('users.id', $teacher_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }

        endif;
    }

    function get_user_tier_id($institution_id)
    {
        if($this->session->get('user_id')){
            $builder = $this->db->table('institution_tier_users');
            $builder->select('institution_tiers.*');
            $builder->join('users', 'institution_tier_users.user_id = users.id');
            $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
            $builder->where('institution_tier_users.user_id', $institution_id);
            $query = $builder->get();
             if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $tier_id = $row->id;
                }
             }
            return $tier_id;
         }
    }
    // Insert Teacher
    public function insert_teacher($data) {

        // Insert User
        $this->db->transStart();
        $builder = $this->db->table('users');
        $builder->insert($data);
        $insert_id = $this->db->insertID();

        // User Roles
        $dataRoles = array('roles_id' => 6, 'users_id' => $insert_id);
        $builder = $this->db->table('user_roles');
        $builder->insert($dataRoles);

        //teacher school relations
        $dataTeacher = array('teacherId' => $insert_id, 'institutionId' => $this->session->get('user_id'), 'created_by' => $this->session->get('logged_tier1_userid'));

        $builder = $this->db->table('institution_teachers');
        $builder->insert($dataTeacher);

        // $this->db->insert('institution_teachers', $dataTeacher);
        $this->db->transComplete();
        return $insert_id;
    }

    // Update Function
    public function update_teacher($id, $data) {
        $builder = $this->db->table('users');
        $builder->where('id', $id);
        $builder->update($data );
        if (($this->db->affectedRows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_email_contect($slug)
    {
        $builder = $this->db->table('email_templates');
        $builder->select('email_templates.*');
        $builder->join('email_categories', 'email_categories.id = email_templates.category_id');
        $builder->where('email_templates.language_code', 'en');
        $builder->where('email_categories.category_slug', $slug);
        $query = $builder->get();
        return $query->getResult();

    }

    public function fetch_u3learners($limit, $start, $search_item) {

        $tierData = $this->get_user_tiers();

        $builder = $this->db->table('users');
        $builder->limit($limit, $start);
        $builder->select('users.id, users.name, users.username, users.dob, users.creation_time, users.user_app_id,institution_tier_users.institutionTierId,institution_tier_users.user_id ,institution_tiers.organization_name ,products.alp_id, products.name as productname,products.id as productid, products.course_type as product_course_type, instituition_learners.cats_product,user_products.thirdparty_id,user_products.course_progress,events.start_date_time,events.end_date_time, events.status as event_status, booking.status as booking_status,venues.city,booking.event_id,collegepre_practicetests.test_number,collegepre_practicetests.candidate_number');
        $builder->join('user_roles', 'user_roles.users_id = users.id');
        $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');
        $where  = 'user_products.user_id = users.id AND user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id)';
        $builder->join('user_products', $where, 'left');
        $builder->join('products', 'products.id = user_products.product_id', 'left');
        //join to fetch final test date and city
        $builder->join('booking', 'user_products.thirdparty_id = booking.test_delivary_id', 'left');
        $builder->join('events', 'booking.event_id = events.id', 'left');
        $builder->join('venues', 'venues.id = events.venue_id', 'left');
        $builder->join('collegepre_practicetests', 'user_products.thirdparty_id = collegepre_practicetests.thirdparty_id', 'left');
        $builder->join('institution_tier_users','institution_tier_users.user_id = instituition_learners.instituition_id','left');
        $builder->join('institution_tiers','institution_tiers.id = institution_tier_users.institutionTierId','left');
        if ($search_item != ''):
            $builder->like('users.name', $search_item);
        endif;
        $builder->where('user_roles.roles_id', '3');
        $builder->where('users.is_active', '1');
        $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
        $builder->groupBy("users.id");
        $builder->orderBy("users.id", "DESC");
        $query = $builder->get(); 

        $u16_details = $query->getResultArray();
        $u16_details_num = $query->getNumRows();

        if ($u16_details_num > 0) {
            foreach ($u16_details as $basic_details) {
                $practice_test_array[$basic_details['thirdparty_id']] = $this->get_result_u16learners_practice($basic_details['thirdparty_id'],$basic_details['cats_product']);
                $final_test_array[$basic_details['thirdparty_id']] = $this->get_result_u16learners_final($basic_details['thirdparty_id'], $basic_details['cats_product']);
                $practice_test_array_tds[$basic_details['thirdparty_id']] = $this->get_result_u16learners_practice_tds($basic_details['thirdparty_id'], 'practice', $basic_details['cats_product']);
            }     

            foreach ($u16_details as $key => $basic_details) {
                 //TDS-366
                 if(!empty($final_test_array[$basic_details['thirdparty_id']])){
                    if($final_test_array[$basic_details['thirdparty_id']]['final_result_status'] == 1){
                        if(!empty($final_test_array[$basic_details['thirdparty_id']]['final_result_candidate_id']) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $final_test_array[$basic_details['thirdparty_id']]['final_result_candidate_id'])){
                               $final_test_array[$basic_details['thirdparty_id']]['audio_reponses'] = 1;
                               $final_test_array[$basic_details['thirdparty_id']]['audio_available'] = False;                            
                        }else{
                            if(!empty($final_test_array[$basic_details['thirdparty_id']]['final_result_higherdata'])){
                              $processed_data_res = $final_test_array[$basic_details['thirdparty_id']]['final_result_higherdata'];
                              $token_audio = $final_test_array[$basic_details['thirdparty_id']]['final_result_token'];
                                $resultdate = $final_test_array[$basic_details['thirdparty_id']]['final_result_date'];;
                                $audioResponse = @get_audio_common($processed_data_res, $resultdate, $token_audio);
                                 $final_test_array[$basic_details['thirdparty_id']]['audio_reponses'] = $audioResponse['audio_reponses'];
                                 $final_test_array[$basic_details['thirdparty_id']]['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                 $final_test_array[$basic_details['thirdparty_id']]['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                             }
                           
                        }
                    }
                }
                $u16_details[$key]['practice_test']  =  $practice_test_array[$basic_details['thirdparty_id']];
                $u16_details[$key]['final_test']  =  $final_test_array[$basic_details['thirdparty_id']];
                $u16_details[$key]['practice_test_tds']  =  $practice_test_array_tds[$basic_details['thirdparty_id']];

                //WP-1308 Starts
                if($basic_details['thirdparty_id'] > 0){
                    $tds_test_query = $this->db->query('SELECT * FROM `tds_tests` WHERE candidate_id = "' . $basic_details['thirdparty_id'] . '" AND test_type = "practice" ');
                    if ($tds_test_query->getNumRows() > 0) {
                        $u16_details[$key]['practice_count'] = $tds_test_query->getNumRows();
                    }else{
                        $cp_test_query = $this->db->query('SELECT CB.* FROM collegepre_batch_add CB JOIN collegepre_formcodes CF ON CF.test_number = CB.test_number WHERE CB.thirdparty_id = "' . $basic_details['thirdparty_id'] . '" AND CF.type = "Practice test"');
                        if ($cp_test_query->getNumRows() > 0) {
                            $u16_details[$key]['practice_count'] = $cp_test_query->getNumRows();
                        }else{
                            $u16_details[$key]['practice_count'] = 0;
                        }
                    }
                }else{
                    if($basic_details['cats_product'] == 'cats_primary'){
                        $productgroup = 1;
                    }else{
                        $productgroup = 2;
                    }
                    $tds_practice_query = $this->db->query('SELECT * FROM `tds_practice_test_settings` WHERE product_groups_id = '.$productgroup);
                    if ($tds_practice_query->getNumRows() > 0) {
                        $result = $tds_practice_query->getRowObject();
                        $u16_details[$key]['practice_count'] = $result->no_of_practice_test;
                    }
                }
            }
        }
            return $u16_details;
       
    }

    public function school_temp_code_valid($temp_pass)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*');
        $builder->join('user_roles', 'user_roles.users_id = users.id');
        $where = "(user_roles.roles_id = '4' OR user_roles.roles_id = '5' OR user_roles.roles_id = '6' OR user_roles.roles_id = '8' OR user_roles.roles_id = '9')";
        $builder->where($where);
        $builder->where('activation_code', $temp_pass);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return FALSE;
		}
    }


    function get_result_u16learners_practice($thirdparty_id = FALSE, $product_type = FALSE) {

        $practice_result_details = null;
        // $this->load->helper('percentage');
        if ($product_type == "cats_core") {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $builder = $this->db->table('collegepre_practicetest_results');
                $builder->select('collegepre_practicetest_results.*');
                $builder->where('collegepre_practicetest_results.thirdparty_id = "' . $thirdparty_id . '"');
                $query = $builder->get();
                $result_practice = $query->getResultArray();
                if (count($result_practice) > 0) {
                    foreach ($result_practice as $fetch_session_number) {
                        $session_number[] = $fetch_session_number['session_number'];
                    }
                    if (count($result_practice) == 2) {
                        $practice_result_details['practice_test1'] = 1;
                        $practice_result_details['session_number1'] = $session_number[0];
                        $practice_result_details['practice_test2'] = 1;
                        $practice_result_details['session_number2'] = $session_number[1];
                    } elseif (count($result_practice) == 1) {
                        $practice_result_details['practice_test1'] = 1;
                        $practice_result_details['session_number1'] = $session_number[0];
                        $practice_result_details['practice_test2'] = 0;
                    }
                } else {
                    $practice_result_details['practice_test1'] = 0;
                    $practice_result_details['practice_test2'] = 0;
                }
            } else {
                $practice_result_details['practice_test1'] = 0;
                $practice_result_details['practice_test2'] = 0;
            }
            //return $practice_result_details;
        } elseif ($product_type == "cats_primary") {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $builder = $this->db->table('collegepre_results');
                $builder->select('collegepre_results.*');
                $builder->join('collegepre_formcodes', 'collegepre_formcodes.form_id = collegepre_results.form_id', 'left');
                $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                $builder->where('collegepre_formcodes.type = "Practice test"');    
                $query = $builder->get();
                $result_practice = $query->getRowObject();

                if (count((array)$result_practice) > 0) {

                    $practice_result_details['percent'] = @get_primary_results($result_practice->section_one, $result_practice->section_two);
                    $practice_result_details['practice_test'] = 1;    
                } else {
                    $practice_result_details['practice_test'] = 0;
                }
            } else {
                $practice_result_details['practice_test'] = 0;
            }

            //return $practice_result_details;
        }
        return $practice_result_details;
    }


    function get_result_u16learners_final($thirdparty_id = FALSE, $product_type = FALSE) {
        // $this->load->helper('percentage');
        if ($product_type == "cats_core") {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $course_details = $this->get_course_type_by_thirdparty_id($thirdparty_id); //WP-1156 - Higher results and Check if It Higher course
                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($thirdparty_id);
                if($course_details[0]->course_type === 'Higher'){
                    if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                        $builder = $this->db->table('tds_results');
                        $builder->select('tds_results.*');
                        $builder->where('tds_results.candidate_id = "' . $thirdparty_id . '"'); 
                    }else{
                        $builder = $this->db->table('collegepre_higher_results');
                        $builder->select('collegepre_higher_results.*,booking.logit_values');
                        $builder->join('booking', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id','left');
                        $builder->where('collegepre_higher_results.thirdparty_id = "' . $thirdparty_id . '"');  
                    }
                }else{
                    if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                        $builder = $this->db->table('tds_results');
                        $builder->select('tds_results.*');
                        $builder->where('tds_results.candidate_id = "' . $thirdparty_id . '"'); 
                    }else{
                        $builder = $this->db->table('collegepre_results');
                        $builder->select('collegepre_results.*');
                        $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                    }
                }
                $query =  $builder->get();
                $result_final = $query->getRowObject();
                if ($result_final) {
                    $final_test['final_result_status'] = 1;
                    $final_test['final_result_candidate_id'] = $result_final->candidate_id;
                    if(isset($result_final->token)){
                      $final_test['final_result_token'] = $result_final->token;
                      $final_test['final_result_higherdata'] = $result_final->processed_data;
                      //TDS-366 
                      $final_test['final_result_date'] = $result_final->result_date;
                    }elseif(isset($result_final->logit_values)){
                        $final_test['final_result_higherdata'] = $result_final->logit_values;
                    }
                } else {
                    $final_test['final_result_status'] = 0;
                }
            } else {
                $final_test['final_result_status'] = 0;
            }
            //return $final_test;
        } elseif ($product_type == "cats_primary") {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($thirdparty_id);
                if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                    $builder = $this->db->table('tds_results');
                    $builder->select('tds_results.*');
                    $builder->where('tds_results.candidate_id = "' . $thirdparty_id . '"'); 
                }else{
                    $builder = $this->db->table('collegepre_results');
                    $builder->select('collegepre_results.*');
                     $builder->join('collegepre_formcodes', 'collegepre_formcodes.form_id = collegepre_results.form_id', 'left');
                     $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                     $builder->where('collegepre_formcodes.type = "Live test"');
                }
                $query = $builder->get();
                $result_final = $query->getRowObject();
                if (count((array)$result_final) > 0) {
                    if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                        if(!empty($result_final->processed_data)) {
                            $processed_result = (array)json_decode($result_final->processed_data);
                            $final_test['percent'] = (array)$processed_result['overall'];
                            $final_test['final_result_status'] = 1;    
                        } else {
                            $final_test['final_result_status'] = 0;
                        }
                    } else {
                        $final_test['percent'] = @get_primary_results($result_final->section_one, $result_final->section_two);
                        $final_test['final_result_status'] = 1;
                    }
                } else {
                    $final_test['final_result_status'] = 0;
                }
            } else {
                $final_test['final_result_status'] = 0;
            }
        }
        return $final_test;
    }


    function get_result_u16learners_practice_tds($user_thirparty_id = FALSE, $test_type = FALSE, $course_type = FALSE) {
        if ($course_type == "cats_core" || $course_type == NULL || $course_type == '') {
            if ($user_thirparty_id != NULL && $user_thirparty_id != 0) {

                $builder = $this->db->table('tds_tests TT');
                $builder->select('TT.token, TT.status, TPR.processed_data, TPR.candidate_id, TPR.result_date as tds_result_date');
                // $this->db->from('tds_tests TT');
                 $builder->join('tds_practicetest_results TPR', 'TT.token = TPR.token', 'LEFT');
                 $builder->where('TT.candidate_id', $user_thirparty_id);
                 $builder->where('TT.test_type', $test_type);
                $query =  $builder->get();
                if($query->getNumRows() > 0){
                    $practice_tds_results = $query->getResultArray();

                    if(!empty($practice_tds_results)){
                            foreach($practice_tds_results as $key => $practice_tds_result){
                                if (strpos($practice_tds_result['token'], 'PT1_') !== false) {
                                    $tds_practice['practice_test1'] = $practice_tds_result;
                                }else{
                                    $tds_practice['practice_test2'] = $practice_tds_result;
                                }
                            }
                            //TDS-366
                            if(isset($tds_practice) || !empty($tds_practice)){  
                                if(!empty($tds_practice['practice_test1']) && !empty($tds_practice['practice_test1']['processed_data'])){
                                    $audioResponse = @get_audio_common($tds_practice['practice_test1']['processed_data'], $tds_practice['practice_test1']['tds_result_date'], $tds_practice['practice_test1']['token'],'practice');
                                    $tds_practice['practice_test1']['audio_reponses'] = $audioResponse['audio_reponses'];
                                    $tds_practice['practice_test1']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                    $tds_practice['practice_test1']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                    
                                } 
                                if(!empty($tds_practice['practice_test2']) && !empty($tds_practice['practice_test2']['processed_data'])){
                                    $audioResponse = @get_audio_common($tds_practice['practice_test2']['processed_data'], $tds_practice['practice_test2']['tds_result_date'], $tds_practice['practice_test2']['token'],'practice');
                                    $tds_practice['practice_test2']['audio_reponses'] = $audioResponse['audio_reponses'];
                                    $tds_practice['practice_test2']['audio_available'] = (!empty($audioResponse['audio_available'])) ? $audioResponse['audio_available'] : '';
                                    $tds_practice['practice_test2']['url'] = (!empty($audioResponse['url'])) ? $audioResponse['url'] : '';
                                    
                                }
                            }                    
                            
                           return $tds_practice;
                        }
                }  
            }   
        } elseif($course_type == 'cats_primary') {
            if ($user_thirparty_id != NULL && $user_thirparty_id != 0) {

                $builder = $this->db->table('tds_practicetest_results');
                $builder->select('tds_practicetest_results.*');
                $builder->where('tds_practicetest_results.candidate_id = "' . $user_thirparty_id . '"');
                $query = $builder->get();
                $result_practice = $query->getRowObject();
                if (count((array)$result_practice) > 0) {
                    if(!empty($result_practice->processed_data)) {
                        $processed_result = (array)json_decode($result_practice->processed_data);
                        $tds_practice['percent'] = (array)$processed_result['overall'];
                        $tds_practice['tds_practice_test'] = 1;
                    } else {
                        $tds_practice['tds_practice_test'] = 0;
                    }
                } else {
                    $tds_practice['tds_practice_test'] = 0;
                }
            } else {
                $tds_practice['tds_practice_test'] = 0;
            }
            return $tds_practice;
        }
    }


    /**WP-1156
     * Function to get course type according to the thirdpartyid
     * @param integer $thirdparty_id
     * @return array|boolean
     */
    public function get_course_type_by_thirdparty_id($thirdparty_id = FALSE){

        if($thirdparty_id != FALSE){
            $builder = $this->db->table('products');
            $builder->select('products.*,user_products.*,user_products.*');
            // $this->db->from('products');
            $builder->join('user_products', 'user_products.product_id = products.id');
            $builder->where('user_products.thirdparty_id', $thirdparty_id);
            $query =  $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getResult();
            }else{
                return FALSE;
            }
        }
    }


     /* U13 Learner */
     public function record_u13_learners_count($search_item = false) {
        $tierData = $this->get_user_tiers();

        $builder = $this->db->table('users');
        $builder->select('users.id, users.name, users.username, users.dob, users.creation_time, products.name as productname');
        $builder->join('user_roles', 'user_roles.users_id = users.id');
        $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');
        $where  = 'user_products.user_id = users.id AND user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id)';
        $builder->join('user_products', $where, 'left');
        $builder->join('products', 'products.id = user_products.product_id', 'left');
        //join to fetch final results 
        $builder->join('collegepre_results', 'user_products.thirdparty_id = collegepre_results.thirdparty_id', 'left');
        //join to fetch practise results on college_preresults
        $builder->join('collegepre_formcodes', 'collegepre_formcodes.form_id = collegepre_results.form_id', 'left');
        //join to fetch practice test results 
        $builder->join('collegepre_practicetest_results', 'user_products.thirdparty_id = collegepre_practicetest_results.thirdparty_id', 'left');
        $builder->join('institution_tier_users','institution_tier_users.user_id = instituition_learners.instituition_id','left');
        $builder->join('institution_tiers','institution_tiers.id = institution_tier_users.institutionTierId','left');
        if ($search_item != ''):
            $builder->like('users.name', $search_item);
        endif;
        $builder->where('user_roles.roles_id', '3');
        $builder->where('users.is_active', '1');
        $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
        //$this->db->where('instituition_learners.instituition_id', $this->session->userdata('user_id'));
        $builder->groupBy("users.id");
        $builder->orderBy("users.id", "DESC");

        $query =  $builder->get();
        $count = $query->getNumRows();

        return $count;
   }

        //WC- Get Purchased course list without finishing final test  
        function get_user_purchashed_course($userid = FALSE) {
            if ($userid != FALSE) {
                $builder = $this->db->table('user_products AS u');
                $builder->select('u.thirdparty_id, u.purchased_date, USER.user_app_id, u.product_id, u.course_progress, p.alp_id, p.level, p.name as product_name, p.alp_id, p.course_type,collegepre_higher_results.section_one as higher_section_one, collegepre_higher_results.section_two as higher_section_two, collegepre_higher_results.candidate_id as higher_candidate_id, collegepre_higher_results.thirdparty_id as higher_thirdparty_id, cr.section_one, cr.section_two, cr.candidate_id,cpr.section_one as practice_test_sc1,tds_results.token as tds_token,tds_results.candidate_id as tds_candidate_id, '
                        . 'GROUP_CONCAT(cr.section_one SEPARATOR "|") as section1_results,'
                        . 'GROUP_CONCAT(cr.section_two SEPARATOR "|") as section2_results,'
                        . 'GROUP_CONCAT(cr.session_number)as session_numbers,'
                        . 'GROUP_CONCAT(fc.type) as test_types,cr.section_one as final_test_sc1, tds_results.processed_data as tds_final_test_processed_data, tds_practicetest_results.processed_data as tds_practice_test_processed_data');
                        $builder->join('products AS p', 'p.id = u.product_id');
                        $builder->join('collegepre_results AS cr', 'u.thirdparty_id = cr.thirdparty_id', 'left');
                        $builder->join('collegepre_practicetest_results AS cpr', 'u.thirdparty_id = cpr.thirdparty_id', 'left');
                        $builder->join('collegepre_higher_results', 'u.thirdparty_id = collegepre_higher_results.thirdparty_id', 'left'); //WP-1202 - Higher results
                        $builder->join('tds_results', 'u.thirdparty_id = tds_results.candidate_id', 'left'); //WP-1276 - Higher results process - CATs TDS
                        $builder->join('tds_practicetest_results', 'u.thirdparty_id = tds_practicetest_results.candidate_id', 'left'); 
                        $builder->join('collegepre_formcodes AS fc', 'fc.form_id = cr.form_id OR fc.form_id = collegepre_higher_results.form_id', 'left');
                        $builder->join('users AS USER', 'USER.id = u.user_id', 'left');
                        $builder->where('u.user_id = ' . $userid);
                        $builder->orderBy('u.id', 'DESC');
                        $builder->groupBy("u.thirdparty_id");
                $purchase_result = $builder->get()->getResult();
                return $purchase_result;
            }
        }

           //count
    public function record_teachers_count($search_item) {
        $tierData = $this->get_user_tiers();
        // $this->db->from('users');
        $builder = $this->db->table('users');
        $builder->join('user_roles', 'user_roles.users_id = users.id');
        $builder->join('institution_teachers', 'institution_teachers.teacherId = users.id');
        //for tier multiple users
        $builder->join('institution_tier_users', 'institution_tier_users.user_id = institution_teachers.institutionId','left');
        $builder->join('institution_tiers', 'institution_tiers.id = institution_tier_users.institutionTierId');
        if ($search_item != ''):
            $builder->like('users.name', $search_item);
        endif;
        $builder->where('user_roles.roles_id', '6');
        $builder->where('users.is_active', '1');
        $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
        $count = $builder->countAllResults();
        return $count;
    }

    function get_u13learner($u13learner_id = FALSE) {

        if ($u13learner_id != FALSE) {
            $query = $this->db->query('SELECT ITU.user_id FROM institution_tier_users IT JOIN institution_tier_users ITU ON ITU.institutionTierId = IT.institutionTierId WHERE IT.user_id = '.$this->session->get('user_id'));
            $results = $query->getResultArray();
            if ($query->getNumRows() > 0) {
                $user_ids = [];
                
                foreach($results as $result):
                $user_ids[] = $result['user_id'];
                endforeach;
                
                $builder = $this->db->table('users');
                $builder->select('*');
                $builder->join('user_roles', 'user_roles.users_id = users.id');
                $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');
                $builder->where('user_roles.roles_id', '3');
                $builder->where('users.is_active', '1');
                // $builder->whereIn($this->session->get('user_id'), $user_ids);
                $builder->where('users.id', $u13learner_id);

                $query = $builder->get();
                if ($query->getNumRows() > 0) {
                    return $query->getRowArray();
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
    }

    function get_institute_productEligible($institute_id = FALSE) {
        if($institute_id!=FALSE):
        $builder = $this->db->table('user_roles');
        $builder->select('institution_eligible_products.group_id');
        $builder->from('institution_eligible_products');
        $builder->where('institutionTierId', $institute_id);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
                return $query->getResult(); 
        }
        endif;
    }

    //next level learners
    function get_nextlevel_u13learner($u13learner_ids = FALSE) {

        if (isset($u13learner_ids) && $u13learner_ids != FALSE && !empty($u13learner_ids)) {
            foreach ($u13learner_ids as $key => $u13learner_ids) {
                
                $builder = $this->db->table('users');
                $builder->select('users.id,user_products.thirdparty_id,user_products.product_id');
                // $builder->from('users');
                $builder->join('user_roles', 'user_roles.users_id = users.id');
                $builder->join('user_products', 'user_products.user_id = users.id', 'left');
                $builder->where('users.id', $u13learner_ids);
                $builder->orderBy('user_products.id',"desc");
                $builder->limit(1);
                $result = $builder->get()->getResult();

                $thirdparty_id  = $result[0]->thirdparty_id;
                $product_id = $result[0]->product_id;

                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($thirdparty_id);

                $builder = $this->db->table('users');
                if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                    $builder->select('users.id, users.name, users.username, instituition_learners.cats_product as product,user_products.product_id, products.level, products.group_id, tr.candidate_id as thirdparty_id, tr.token, tr.course_type, tr.processed_data as final_test');
                }else{
                    $builder->select('users.id, users.name, users.username, cr.thirdparty_id, instituition_learners.cats_product as product,user_products.product_id, cr.session_number as final_test, cr.candidate_id, products.level, products.group_id, cr.section_one, cr.section_two');
                }
            
                // $this->db->from('users');
                $builder->join('user_roles', 'user_roles.users_id = users.id');
                $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');
                $builder->join('user_products', 'user_products.user_id = users.id', 'left');
                $builder->join('products', 'user_products.product_id = products.id', 'left');
                if($product_id > 9 && $product_id < 13){

                    if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                        $builder->join('tds_results AS tr', 'user_products.thirdparty_id = tr.candidate_id', 'left');
                        // $this->db->join('collegepre_formcodes', 'cr.form_id = collegepre_formcodes.form_id');
                        $builder->where('tr.candidate_id', $thirdparty_id);
                    }else{
                        //join to fetch final results - higher
                        $builder->join('collegepre_higher_results AS cr', 'user_products.thirdparty_id = cr.thirdparty_id', 'left');
                        $builder->join('collegepre_formcodes', 'cr.form_id = collegepre_formcodes.form_id');
                        $builder->where('cr.thirdparty_id', $thirdparty_id);
                        $builder->where('collegepre_formcodes.type', 'Live test');
                    }
                }else{

                    if($delivery_type != NULL && $delivery_type[0]->tds_option == 'catstds'){
                        //join to fetch final results core - TDS
                        $builder->join('tds_results AS tr', 'user_products.thirdparty_id = tr.candidate_id', 'left');
                        $builder->where('tr.candidate_id', $thirdparty_id); 
                    }else{
                    //join to fetch final results core
                    $builder->join('collegepre_results AS cr', 'user_products.thirdparty_id = cr.thirdparty_id', 'left');
                    $builder->join('collegepre_formcodes', 'cr.form_id = collegepre_formcodes.form_id');
                    $builder->where('cr.thirdparty_id', $thirdparty_id);
                    $builder->where('collegepre_formcodes.type', 'Live test'); 
                    } 
                }
                // if($delivery_type != NULL && $delivery_type[0]->tds_option != 'catstds'){
                //     $this->db->where('collegepre_formcodes.type', 'Live test');
                // }
                $query = $builder->get();
                if ($query->getNumRows() > 0) {
                    $u13learners_records[$u13learner_ids] = $query->getRowArray();
                }else{
                    $builder = $this->db->table('users');
                    $builder->select('users.id, users.name, users.username');
                    //$this->db->from('users');
                    $builder->where('users.id', $u13learner_ids);
                    $query =  $builder->get();
                    
                    $u13learners_records[$u13learner_ids] = $query->getRowArray();
                
                }
            }            
            return $u13learners_records;
        }
    }

    function get_institute_productEligible_by_user($user_id = FALSE){
		if($user_id){
			$query = $this->db->query('SELECT SO.school_user_id,school_order_id,ITU.institutionTierId FROM tokens T JOIN school_orders SO ON SO.id = T.school_order_id JOIN institution_tier_users ITU ON ITU.user_id = SO.school_user_id WHERE T.user_id = '.$user_id);
            		$result = $query->getRowObject();
			$instituteTierId = $result->institutionTierId;
			
			if($instituteTierId){
                $builder = $this->db->table('institution_eligible_products');
				$builder->select('institution_eligible_products.group_id, institution_eligible_products.tds_option');
				$builder->where('institutionTierId', $instituteTierId);
				$query = $builder->get();
				if ($query->getNumRows() > 0) {
					return $query->getResult(); 
				}
			}
		}
	}

     
    // Fetch primary user details like personal, purchase and test
    public function fetch_u13learner_details($u13learner_id = FALSE, $user_id = FALSE) {
        if($u13learner_id != FALSE){       

            $builder = $this->db->table('institution_teachers');    
            $builder->select('institution_teachers.institutionId');
            $builder->where('institution_teachers.teacherId', $user_id);
            $q =  $builder->get();

            if ($q->getNumRows() > 0) {
                foreach ($q->getResultArray() as $row) {
                    $institutionId = $row['institutionId'];
                }
            }

            //access all under one tier
            if(is_null(@$institutionId)){
                 $tier_id = $this->get_user_tier_id_u13pdf($user_id);
            }else{
                 $tier_id = $this->get_user_tier_id_u13pdf($institutionId);
            }         
            
            $builder = $this->db->table('users');
            $builder->select('users.id, users.name, users.access_detail_language, products.level, products.name as productname, user_products.thirdparty_id, user_products.purchased_date, collegepre_results.candidate_id, collegepre_results.section_one, collegepre_results.section_two, collegepre_results.session_version, collegepre_results.result_date, institution_tier_users.institutionTierId as tier_id,events.start_date_time,events.end_date_time'); 
            $builder->join('user_roles', 'user_roles.users_id = users.id');
            $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');           
            $builder->join('user_products', 'user_products.user_id = users.id', 'left');
            $builder->join('products', 'products.id = user_products.product_id', 'left');
            $builder->join('institution_tier_users', 'institution_tier_users.user_id = instituition_learners.instituition_id', 'left'); //access all under one tier
                      
            //join to fetch final results
            $builder->join('collegepre_results', 'user_products.thirdparty_id = collegepre_results.thirdparty_id', 'left');
            $builder->join('collegepre_formcodes', 'collegepre_results.form_id = collegepre_formcodes.form_id');
            $builder->join('booking', 'user_products.thirdparty_id = booking.test_delivary_id', 'left'); //check if booking available for Under13 - WP-1167
            $builder->join('events', 'booking.event_id = events.id', 'left'); //check if booking available for Under13 - WP-1167
            $builder->where('user_roles.roles_id', '3');
            $builder->where('collegepre_formcodes.type', 'Live test');
            $builder->where('users.is_active', '1');
            $builder->where('users.id',  $u13learner_id);
            $builder->where('institution_tier_users.institutionTierId', $tier_id);//access all under one tier
            
            $builder->orderBy("user_products.id", "DESC");
            $query =  $builder->get();
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }
    }

    //qrcode show without session values
    public function get_user_tier_id_u13pdf($institution_id) {  
        $builder = $this->db->table('institution_tier_users');          
        $builder->select('institution_tiers.*');
        $builder->join('users', 'institution_tier_users.user_id = users.id');
        $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
        $builder->where('institution_tier_users.user_id', $institution_id);
        $query = $builder->get();
         if ($query->getNumRows() > 0) {
             foreach ($query->getResultArray() as $row) {
                $tier_id = $row['id'];
            }
         }
        return $tier_id;         
    }


    // Fetch primary user tds details like personal, purchase and test
    public function fetch_u13learner_details_tds($u13learner_id = FALSE, $user_id = FALSE, $u13thirdpartyid = FALSE) {
        if($u13learner_id != FALSE){      
            
            $builder = $this->db->table('institution_teachers');    
            $builder->select('institution_teachers.institutionId');
            $builder->where('institution_teachers.teacherId', $user_id);
            $q = $builder->get();
            if ($q->getNumRows() > 0) {
                foreach ($q->getResultArray() as $row) {
                    $institutionId = $row['institutionId'];
                }
            }            
            //access all under one tier
            if(is_null(@$institutionId)){
                 $tier_id = $this->get_user_tier_id_u13pdf($user_id);
            }else{
                 $tier_id = $this->get_user_tier_id_u13pdf($institutionId);
            }         
            
            $builder = $this->db->table('users');
            $builder->select('users.id, users.name, users.access_detail_language, products.level, products.name as productname, user_products.thirdparty_id, user_products.purchased_date, tds_results.candidate_id, tds_results.raw_responses, tds_results.processed_data, tds_results.result_date, institution_tier_users.institutionTierId as tier_id,events.start_date_time,events.end_date_time'); 
            $builder->join('instituition_learners', 'instituition_learners.user_id = users.id');           
            $builder->join('user_products', 'user_products.user_id = users.id', 'left');
            $builder->join('products', 'products.id = user_products.product_id', 'left');
            $builder->join('institution_tier_users', 'institution_tier_users.user_id = instituition_learners.instituition_id', 'left'); //access all under one tier
                      
            //join to fetch final results
            $builder->join('tds_results', 'user_products.thirdparty_id = tds_results.candidate_id', 'left');
            $builder->join('booking', 'user_products.thirdparty_id = booking.test_delivary_id', 'left'); //check if booking available for Under13 - WP-1167
            $builder->join('events', 'booking.event_id = events.id', 'left'); //check if booking available for Under13 - WP-1167
            $builder->where('user_products.thirdparty_id',  $u13thirdpartyid);
            $builder->where('institution_tier_users.institutionTierId', $tier_id);//access all under one tier
          
            $builder->orderBy("user_products.id", "DESC");
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }
    }




     //WC- Higher web client implementation
    public function get_higher_type_ids() {

        $builder = $this->db->table('products');
        $builder->select('products.id');
        $builder->where('products.course_type', 'Higher');
        $query = $builder->get();
         if ($query->getNumRows() > 0) {
           foreach ($query->getResultArray() as $row) {
               $result[] = $row['id'];
           }
        }
         return $result;
     }

    public function field_exists($field) {
        return $this->db->fieldExists($field,'users');
    }

    public function userinsert($data)
    {
        $builder = $this->db->table('users');
        $builder->insert($data);
        return TRUE;
    }

    public function record_institute_user_count($search_item){
		$user_id = $this->session->get('user_id');
		
        $builder = $this->db->table('institution_tier_users');
		$builder->select('institution_tier_users.institutionTierId');
		$builder->where('institution_tier_users.user_id = "'.$user_id.'"');
		$query = $builder->get();
		if ($query->getNumRows() > 0) {
			$result = $query->getRow();
			$tier_id = $result->institutionTierId;
		}else{
			return FALSE;
		}
		
        $builder = $this->db->table('institution_internal_tiers');
		$builder->select('institution_internal_tiers.institutionTierId,institution_tiers.*');
		$builder->join('institution_tiers', 'institution_tiers.id = institution_internal_tiers.institutionTierId');
		$builder->where('institution_internal_tiers.relinstitutionTierId = "'.$tier_id.'"');
		$builder->groupStart();
		$builder->like('institution_tiers.organization_name', $search_item);
		$builder->orLike('institution_tiers.address_line1', $search_item);
		$builder->orLike('institution_tiers.address_line2', $search_item);
		$builder->orLike('institution_tiers.address_line3', $search_item);
		$builder->groupEnd();
		$query = $builder->get(); 
		$count = $query->getNumRows();
        return $count;
	}

    public function fetch_institute_users($limit, $start, $search_item){
		$user_id = $this->session->get('user_id');
		
		$builder = $this->db->table('institution_tier_users');
		$builder->select('institution_tier_users.institutionTierId');
		$builder->where('institution_tier_users.user_id = "'.$user_id.'"');
		$query = $builder->get();
		if ($query->getNumRows() > 0) {
			$result = $query->getRow();
			$tier_id = $result->institutionTierId;
		}else{
			return FALSE;
		}
		
        $builder = $this->db->table('institution_internal_tiers');
		$builder->limit($limit, $start);
		$builder->select('institution_internal_tiers.institutionTierId,institution_tiers.*, regions.name as region_name');
        $builder->join('institution_tiers', 'institution_tiers.id = institution_internal_tiers.institutionTierId');
        $builder->join('regions', 'regions.regionCode = institution_tiers.region', 'left');
		$builder->where('institution_internal_tiers.relinstitutionTierId = "'.$tier_id.'"');
		$builder->groupStart();
		$builder->like('institution_tiers.organization_name', $search_item);
		$builder->orLike('institution_tiers.address_line1', $search_item);
		$builder->orLike('institution_tiers.address_line2', $search_item);
		$builder->orLike('institution_tiers.address_line3', $search_item);
		$builder->groupEnd();
		$query = $builder->get(); 
        $institute_details = $query->getResult();
		//echo '<pre>'; print_r($builder->last_query()); die;
		//echo '<pre>'; print_r($institute_details); die;
		
		return $institute_details;
	}

      //WC- Get Purchased course list without finishing final test
      function get_user_purchashed_course_api($userid = FALSE) {
        if ($userid != FALSE) { 
            $builder = $this->db->table('user_products AS u');
            $builder->select('u.thirdparty_id, u.purchased_date, USER.user_app_id, u.product_id, p.alp_id, p.level, p.name as product_name, p.alp_id, p.course_type');
            // $this->db->from('user_products AS u');
            $builder->join('products AS p', 'p.id = u.product_id');
            $builder->join('users AS USER', 'USER.id = u.user_id', 'left');
            $builder->where('u.user_id = ' . $userid);
            $builder->orderBy('u.id', 'DESC');
            $builder->limit(1);
            $purchase_result = $builder->get()->getResult();
            //condition to check final test availabel or not, return empty to disable & value to enable webclient
            $thirdparty_id  = @$purchase_result[0]->thirdparty_id;
            if(isset($thirdparty_id) && !empty($thirdparty_id)){
                $builder = $this->db->table('booking');
                $builder->select('events.id,events.tds_option');
                // $this->db->from('booking');
                $builder->join('events', 'booking.event_id = events.id');
                $builder->where('booking.test_delivary_id', $thirdparty_id);
                $delivary_option = $builder->get()->getRowArray();
                if (count((array)$delivary_option) > 0) {
                    if(!empty($delivary_option) && $delivary_option['tds_option']  == "catstds"){
                        $builder = $this->db->table('tds_results AS tr');
                        $builder->select('tr.course_type, tr.token');
                        // $this->db->from('tds_results AS tr');
                        $builder->where('tr.candidate_id = ' . $thirdparty_id);
                        $tds_results = $builder->get();
                        if ($tds_results->getNumRows() > 0) {
                            $purchase_result = [];
                        }
                    }else{
                        // $this->db->select('cr.section_one, cr.section_two, cr.candidate_id, fc.type');
                        if ($purchase_result[0]->course_type == "Higher") {
                            
                            // $this->db->from('collegepre_higher_results AS cr');
                            $builder = $this->db->table('collegepre_higher_results AS cr');
                            $builder->select('cr.section_one, cr.section_two, cr.candidate_id, fc.type');

                        } else {
                            // $this->db->from('collegepre_results AS cr');
                            $builder = $this->db->table('collegepre_results AS cr');
                            $builder->select('cr.section_one, cr.section_two, cr.candidate_id, fc.type');
                        }
                        $builder->join('collegepre_formcodes AS fc', 'fc.form_id = cr.form_id', 'left');
                        $builder->where('cr.thirdparty_id = ' . $thirdparty_id);
                        $purchase_results = $builder->get();
                        if ($purchase_results->getNumRows() > 0) {
                            $results = $purchase_results->getResult();
                            foreach($results as $result):
                            if($result->type == 'Live test'){
                                $purchase_result = [];
                            }
                            endforeach;
                        }
                    }
                }
            }
            return $purchase_result;
        }
    }

    
    public function renew_token($token) {
        $data = array();
        $data['app_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        $builder = $this->db->table('users');
        $builder->where('app_token', $token);
        $renew_token = $builder->update($data);
        if ($renew_token) {
            $renewtoken = array();
            $renewtoken['token'] = $data['app_token'];
            return $renewtoken;
        } else {
            return FALSE;
        }
    }
    
    public function disable_token($token) {
        $data = array();
        $data['app_token'] = '';
        $builder = $this->db->table('users');
        $builder->where('app_token', $token);
        $disable_token = $builder->update($data);
        if ($disable_token) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_expiry($data) {
        $query = $this->db->query('SELECT u.purchased_date from user_products AS u WHERE product_id = (SELECT id from products	WHERE alp_id = ' . $data->levelid . ') AND user_id = (SELECT id from users WHERE app_token = "' . $data->token . '")');
        $result = $query->getRow();

        if ($result) {
            $expiry = $result->purchased_date;
            return $expiry;
        } else {
            return FALSE;
        }
    }

    public function get_app_user($identity, $dtype) {
        /* get user deatails by email */
        if ($dtype == "email") {
            if (str_contains($identity, '@')) {
                $builder = $this->db->table('users');
                $builder->where('email', $identity);
                $query = $builder->get();
            } else {
                $builder = $this->db->table('users');
                $builder->where('username', $identity);
                $query = $builder->get();
            }
        }
        /* get user deatails by user unique token */ 
        elseif ($dtype == "token") {
            $builder = $this->db->table('users');
            $builder->where('user_app_id', $identity);
            $query = $builder->get();
        }
        /* get user deatails by facebook id */
        elseif ($dtype == "FACEBOOK") {
            $builder = $this->db->table('users');
            $builder->where('facebook_id', $identity);
            $query = $builder->get();
        }
        /* get user deatails by google id */ 
        elseif ($dtype == "GOOGLE") {
            $builder = $this->db->table('users');
            $builder->where('google_id', $identity);
            $query = $builder->get();
        }
        /* get user deatails by user unique token for webclient type */ 
        elseif ($dtype == "WEBCLIENT") {
            $builder = $this->db->table('users');
            $builder->where('user_app_id', $identity);
            $query = $builder->get();
        }
        $result = $query->getRow();
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }


        
}
