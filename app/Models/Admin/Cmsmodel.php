<?php
namespace App\Models\Admin;//path
use CodeIgniter\Model;
use DateTimeZone;
use DateTime;

class Cmsmodel extends Model
{

	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
		$this->request = \Config\Services::request();
	}

	public function record_count() {
		return $this->db->table('cms_translation')->countAll();
	}

	/* applinks starts*/
	public function app_links_count() {
		return $this->db->table('page_apps')->countAll();
	}

	public function fetch_app_links($limit, $start) {

		$builder = $this->db->table('page_apps');
		$builder->select('page_apps.*');
		$builder->limit($limit, $start);
		$result = $builder->get()->getResult();
		return $result;
	}

	public function get_language($lang_code = FALSE, $all = FALSE)
	{
		if($lang_code != FALSE){
			$builder = $this->db->table('language')->where('code', $lang_code); 
                }elseif($all != FALSE){
					$builder = $this->db->table('language');
                    $query   = $builder->get(); 
                }else{
					$builder = $this->db->table('language');        // 'mytablename' is the name of your table
					$builder->select('*');       // names of your columns
					$builder->where('content_status', 1);                // where clause
		}
		return  $builder->get()->getResult();
	}

	public function get_applinks($id = FALSE)
	{
		if($id != FALSE){
			$builder = $this->db->table('page_apps');
			$builder->Where('id',$id);
			$query = $builder->get();
		}else{
			$builder = $this->db->table('page_apps');
			$query = $builder->get();
		}
		return $query->getResult();
	}
	
	public function update_applink($id = FALSE, $datasend = FALSE) {
		if($id != FALSE && $datasend != FALSE){
			$builder = $this->db->table('page_apps');
			$builder->where('id', $id); 
			$builder->update($datasend);
			return TRUE;
		}
	}

	public function get_maintanence_ip_address($id=FALSE){
		$builder = $this->db->table('scheduled_maintenance_ip_address');
		$builder->select('*');
		$builder->orderBy('id', 'DESC');
		if($id) {
			$builder->where('id', $id);
			$result = $builder->get()->getRowArray();
		} else {
			$result = $builder->get()->getResultArray();
		}
		if(!empty($result)){
			return $result;
		}else{
			return FALSE;
		}
	}

	public function get_id($id = FALSE){
        if($id != FALSE){
            $builder = $this->db->query('SELECT * FROM scheduled_maintenance WHERE id ='.intval($id));
            if ($builder->getNumRows() > 0) {
                return $builder->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

	public function institution_types($rd=FALSE){
		if($rd != FALSE){
			$builder = $this->db->table('institutiongroup');
			$builder->select('*');
		  
	      $builder->where(array('institutiongroupid' => $rd));
	      $builder->orderBy("institutiongroup.englishTitle", "ASC");
	      $query = $builder->get();
	    } else{
			$builder = $this->db->table('institutiongroup');
			$builder->select('*');
		  $builder->orderBy("institutiongroup.englishTitle", "ASC");
		  $query = $builder->get();
	    }
	    return $query->getResult();
	}

	public function get_regions($rd = FALSE){
	    if($rd != FALSE){
			$builder = $this->db->table('regions');
	        $builder->select('*');
	        $builder->where('regions.id', $rd);
	        $query = $builder->get();
	    }else{
			$builder = $this->db->table('regions');
	        $builder->select('*');
	        $query = $builder->get();
	    }
	    return $query->getResult();
	}

	public function get_tds_test_details($limit, $start){
		$builder = $this->db->table('tds_test_detail');
		$builder->select('tds_test_detail.*,tds_test_group.test_type as purpose,products.name as product,products.course_type as course_type');
		$builder->join('tds_test_group', 'tds_test_group.id = tds_test_detail.tds_group_id');
        $builder->join('products', 'tds_test_detail.test_product_id = products.id','left');
        $builder->orderBy("tds_test_detail.id", "ASC");
		$builder->limit($limit, $start);
		$result = $builder->get()->getResult();
		return $result;
	}

	public function get_tds_test_types(){
		$query = $this->db->query('SELECT * FROM tds_test_group WHERE status = 1');
		$result = $query->getResult();
		if($query->getNumRows() > 0){
			return $result;
		}else{
			return FALSE;
		}
	}

	public function get_tds_test_detail_by_id($test_detailid){
		if($test_detailid){
			$query = $this->db->query('SELECT * FROM tds_test_detail WHERE id = '.$test_detailid);
			$result = $query->getRowArray();
			if($query->getNumRows() > 0){
				return $result;
			}else{
				return FALSE;
			}
		}
	}

	public function get_tds_products(){
		$query = $this->db->query('SELECT id,name,course_type FROM products WHERE active = 1 ORDER BY group_id ASC, id ASC');
		$result = $query->getResult();
		if($query->getNumRows() > 0){
			return $result;
		}else{
			return FALSE;
		}
	}

	public function delete_ip_details($id=FALSE) {
		if (isset($id) && $id != FALSE) {
			 $builder = $this->db->table('scheduled_maintenance_ip_address');
			 $builder->where('id', $id);
             $builder->delete();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function insert_ip_details($data = FALSE) {
		if (isset($data) && $data != FALSE) {
			$builder = $this->db->table('scheduled_maintenance_ip_address');
			$builder->insert($data);
			return  $result = $this->db->insertID();

		} else {
			return FALSE;
		}
	}

	public function update_ip_details($data = FALSE, $id=FALSE) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('scheduled_maintenance_ip_address');
			$builder->where('id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function insert_downtime($datatime = FALSE) {

		if (isset($datatime) && $datatime != FALSE && !empty($datatime)) {
			$builder = $this->db->table('scheduled_maintenance');
			$builder->insert($datatime);
			return  $result = $this->db->insertID();
		}
	}

	public function update_downtime($data = FALSE, $id=FALSE) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('scheduled_maintenance');
			$builder->where('id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

		public function get_down_time_lists($limit=FALSE, $start=FALSE){

		$current_utc_time = @get_current_utc_details();
		$current_utc_timestamp = $current_utc_time['current_utc_timestamp'];
		$builder = $this->db->table('scheduled_maintenance');
		$builder->select('*');
		$builder->orderBy('start_date_time', 'ASC');
		$builder->where('end_date_time >', $current_utc_timestamp);
		if(isset($limit) && isset($start)) {
			$builder->limit($limit, $start);
		}
        $query = $builder->get();
        return $query->getResultArray();
	}

	
	public function status_update($data = FALSE, $id=FALSE) {
		if (isset($id) && $id != FALSE) {
			$builder = $this->db->table('scheduled_maintenance');
			$builder->where('id', $id); 
			$builder->update($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function get_product_group()
	{
		$builder = $this->db->table('product_groups');
		$builder->select('*');
		$query = $builder->get();
		return $query->getResultArray();
	}

	public function institution_types_update($id)
	{
		$data = [
			'englishTitle' => $_POST['institution_types'],
			'l1Title' => $_POST['institution_types']
		];
		if ($id != FALSE) {
		$builder = $this->db->table('institutiongroup');
		$builder->where('institutionGroupId', $id); 
		$builder->update($data);
		return TRUE;
	} else {
		return FALSE;
	}

	}

	public function insertInstitutionType(){ 

		$data = [
			'englishTitle' => $_POST['institution_types'],
			'l1Title' => $_POST['institution_types']
		]; 
		$builder = $this->db->table('institutiongroup');
		$builder ->insert($data); // insert data into `institutiongroup` 
		return $this->db->insertID();
		//table 
	}

		// check region in institution_tiers table while delete
		public function check_institution_tiers($regioncode = FALSE, $organisation_type = FALSE){

			$builder = $this->db->table('institution_tiers');
			$builder->select('*');
			if($regioncode != FALSE){	        
				$builder->where('institution_tiers.region', $regioncode);	        
			}
			if($organisation_type != FALSE){
				$builder->where('institution_tiers.organisation_type', $organisation_type);
			}
			$query = $builder->get();
			return $query->getResult();
		}

						
	public function delete_insitute_type($id=FALSE) {
		if ( $id != FALSE) {
			 $builder = $this->db->table('institutiongroup');
			 $builder->where('institutiongroupid', $id);
             $builder->delete();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	// delete region
	public function delete_region($id = FALSE){

		if ( $id != FALSE) {
			$builder = $this->db->table('regions');
			$builder->where('id', $id);
			$builder->delete();
		   return TRUE;
	   } else {
		   return FALSE;
	   }
	}
		// insert region
		public function insert_region(){

			   $data = [
				'countryCode'=> $_POST['country_code'],
				 'name'=> $_POST['region_name']
			    ];
				$builder = $this->db->table('regions');
				$builder ->insert($data); // insert data into `institutiongroup` 
				$regionCode = $_POST['country_code'] . $this->db->insertID();
				$lastinsertid = $this->db->insertID();
				$dataregioncode = [
					'regionCode'=> $regionCode
					];
				$builder = $this->db->table('regions');
				$builder->where('id', $this->db->insertID()); 
				$builder->update($dataregioncode);

				if( $this->db->affectedRows() > 0 ){
					return $lastinsertid;
				}else{
					return FALSE;
				}
		}
	
		public function update_region_name($id) {

			$data = [
				'name' => $_POST['region_name']
			]; 
			if ($id != FALSE) {
				$builder = $this->db->table('regions');
				$builder->where('id', $id); 
				$builder->update($data);
				return TRUE;
			} else {
				return FALSE;
			}
		}

		
	public function get_tds_products_based_purpose($test_purpose){

		$builder = $this->db->table('products');
		$builder->select('id,name,course_type');
		$builder->where('active', 1);
		if($test_purpose == 3) {
			$builder->where('course_type !=', 'Higher');
		}
		$builder->orderBy('group_id', 'ASC');
		$builder->orderBy('id', 'ASC');
		$result = $builder->get()->getResult();
		if(!empty($result)){
			return $result;
		}else{
			return FALSE;
		}
	}

	public function test_group_id($test_product_id){
		$builder = $this->db->table('products');
		$builder->select('course_type');
		$builder->where('id', $test_product_id);
		return $check_higher = $builder->get()->getRowArray();
	}

	public function higher_products(){
		$builder = $this->db->table('products');
		$builder->select('id');
		$builder->where('course_type', 'Higher');
		$higher_products = $builder->get()->getResult();
	}

	public function test_product_id($check_higher,$where_array){
		$builder = $this->db->table('tds_test_forms');
		$builder->select('*');

		if($check_higher == 'Higher') {
			$builder->where($where_array);
		} else {
			$builder->where($where_array);
		}
		$results = $builder->get()->getResult();
	}

	public function check_formid_already_associated($test_formid = FALSE){
		if($test_formid){
			$builder = $this->db->table('tds_tests');
			$builder->select('*');
			$builder->where('test_formid',$test_formid);
			$builder->where('status',0);
			$results = $builder->get()->getResult();
			if($results != Null){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function fetch_incomplete_test_leaners($search_item='', $institutionid = FALSE) {

        $current_utc = $this->get_current_utc_details();
        $institutionData = $this->getInstitutionData($institutionid);
        //minus 3 hours from current timestamp
        $check_event_end_time = $current_utc['current_utc_timestamp'] - (3 * 60 * 60);
        $tz_to = $institutionData['timezone'];
		$builder = $this->db->table('booking as booking');
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
            $institution_zone_values = $this->get_institution_zone_from_utc($tz_to, $result->start_date_time, $result->end_date_time);
           
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

	    //get institution data 
		public function getInstitutionData($institute_id = FALSE)
		{
			$builder = $this->db->table('institution_tiers');
			$builder->select('institution_tiers.*');
			$builder->where('institution_tiers.id', $institute_id);
			return $query = $builder->get()->getRowArray();
		}

		function get_institution_zone_from_utc($time_zone = FALSE,$utc_start_date_time= FALSE,$utc_end_date_time= FALSE){
			$date_format_start = date("d F Y H:i:s", $utc_start_date_time);
			$date_format_end = date("d F Y H:i:s", $utc_end_date_time);
			$utc_start_time = new DateTime($date_format_start, new DateTimeZone('UTC'));
			$utc_end_time = new DateTime($date_format_end, new DateTimeZone('UTC'));
			
			$institute_zone_start_timestamp = $utc_start_time->setTimeZone(new DateTimeZone($time_zone));
			$institute_zone_end_timestamp = $utc_end_time->setTimeZone(new DateTimeZone($time_zone));
			
			$institute_zone_date = $institute_zone_start_timestamp->format('d-M-Y');
			$institute_zone_start_time = $institute_zone_start_timestamp->format('H:i');
			$institute_zone_end_time = $institute_zone_end_timestamp->format('H:i');
			
			$institution_event_data = array(
				'institute_event_date' =>  $institute_zone_date,
				'institute_start_time' =>  $institute_zone_start_time,
				'institute_end_time' =>  $institute_zone_end_time
				);
			return $institution_event_data;
		}

	// Check active placement form to make inactive
	public function check_formid_active_placement($test_detailid = FALSE){
		if($test_detailid){

			$builder = $this->db->table('tds_placement_active_form');
			$builder->select('*');
			$builder->where('tds_test_detail_id', $test_detailid);
			$builder->where('test_type','Adaptive');
			$builder->get();
			$result = $builder->getResult();
			if($builder->getNumRows() > 0){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function helplinks () {
		$builder = $this->db->table('helplinks');
		$builder->select('*');
		$query = $builder->get();
		return $query->getResultArray();		
	}

	function institute_access_language_by_tierusers($user_id = FAlSE)
	{
		if ($user_id != FALSE) {
			$builder = $this->db->table('institution_tiers');
            $builder->select('institution_tiers.access_detail_language,institution_tiers.organization_name,institution_tier_users.user_id');
            $builder->join('institution_tier_users', 'institution_tier_users.institutionTierId = institution_tiers.id');
            $builder->where('institution_tier_users.user_id', $user_id);
            $query = $builder->get();
            return $query->getRowArray();
        }
	}

	public function applinks () {
		$builder = $this->db->table('page_apps');
		$builder->select('*');
		 $query = $builder->get();
		 return $query->getResultArray();		
	 }

	 public function get_tds_test_details_count(){

		$builder = $this->db->table('tds_test_detail');
		$builder->select('tds_test_detail.*,tds_test_group.test_type');
		$builder->join('tds_test_group', 'tds_test_group.id = tds_test_detail.tds_group_id');
		$query = $builder->get();
		if($query->getNumRows() > 0){
			$result = $query->getNumRows();
			return $result;
		}else{
			return FALSE;
		}
	}

		//get CMS content for specific page
		public function get_cms_contents($slug = FALSE, $langcode = FALSE)
		{
			
			$builder = $this->db->table('cms');
			$builder->select('cms.id as cms_id, cms.page_name, cms_translation.id, cms_translation.language_code,  cms_translation.meta_title, cms_translation.meta_description, cms_translation.meta_keywords, cms_translation.title, cms_translation.content,cms_translation.content_v2, cms_translation.link ');
			$builder->join('cms_translation', 'cms.id = cms_translation.cms_id');
			$builder->where('cms.page_slug', $slug);		
			$builder->where('cms_translation.language_code', $langcode);		
			$query = $builder->get();
			return $query->getResult();
		}

		public function get_primary_details($institution_id = FALSE, $start_date = FALSE, $end_date = FALSE){
            if($start_date != FALSE && $end_date != FALSE){
                $start_date = $start_date." "."00:00:00";
                $end_date = $end_date." "."23:59:59";
            }
            $tier_id = $this->get_user_tiers($institution_id);
            //Select results query for primary by institution_id, start_date, end_date
			$builder = $this->db->table('instituition_learners');
            $builder->select('instituition_learners.user_id,user_products.thirdparty_id,products.level,products.name as productname,collegepre_results.*, users.name as p_user_name, tds_results.candidate_id as tds_candidate_id, tds_results.processed_data as tds_processed_data, tds_results.result_date as tds_result_date, events.start_date_time, events.end_date_time');
            $builder->join('users','instituition_learners.user_id = users.id');
            $builder->join('user_products','instituition_learners.user_id = user_products.user_id');
            $builder->join('collegepre_results','user_products.thirdparty_id = collegepre_results.thirdparty_id', 'left');
			$builder->join('collegepre_formcodes','collegepre_results.form_id = collegepre_formcodes.form_id', 'left');
			$builder->join('tds_results','tds_results.candidate_id = user_products.thirdparty_id', 'left');
			$builder->join('booking', 'user_products.thirdparty_id = booking.test_delivary_id', 'left');
            $builder->join('events', 'booking.event_id = events.id', 'left'); 
            $builder->join('products','products.id = user_products.product_id');
            $builder->join('institution_tier_users','instituition_learners.instituition_id = institution_tier_users.user_id');
			$where = "(`collegepre_results`.`result_date` >= '$start_date'  AND `collegepre_results`.`result_date` <= '$end_date' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ."  AND `collegepre_formcodes`.`type` = 'Live test' AND `instituition_learners`.`cats_product` = 'cats_primary')
			OR (`tds_results`.`result_date` >= '$start_date' AND `tds_results`.`result_date` <= '$end_date' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ."  AND `instituition_learners`.`cats_product` = 'cats_primary')";
			$builder->where($where);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                $result = $query->getResult();
                return $result;
            }else{
                return FALSE;
            }
		}

	       //To fectch tier id using institution id
		public function get_user_tiers($institution_id = False) {
			$builder = $this->db->table('institution_tier_users');
            $builder->select('institution_tiers.*');
            $builder->join('users', 'institution_tier_users.user_id = users.id');
            $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
            $builder->where('institution_tier_users.user_id', $institution_id);
            $query = $builder->get();
            return $query->getRowArray();
        }

		public function get_core_details($institution_id = FALSE, $start_date = FALSE, $end_date = FALSE, $course = "Core"){
            if($start_date != FALSE && $end_date != FALSE){
                $start_date = $start_date." "."00:00:00";
                $end_date = $end_date." "."23:59:59";
            }
            $tier_id = $this->get_user_tiers($institution_id);
            //Select results query for Core by institution_id, start_date, end_date (both TDS and COLLEGEPRE)
			$builder = $this->db->table('school_orders');
            $builder->select('school_orders.id,tokens.thirdparty_id,collegepre_results.result_date,collegepre_results.candidate_id, tds_results.candidate_id as tds_candidateId,tds_results.token as tds_token,tds_results.course_type as tds_test_type, products.course_type');
            $builder->join('tokens','school_orders.id = tokens.school_order_id');
            $builder->join('collegepre_results','tokens.thirdparty_id = collegepre_results.thirdparty_id', 'left');
            $builder->join('tds_results','tokens.thirdparty_id = tds_results.candidate_id', 'left');
            $builder->join('products','tokens.product_id = products.id');
            $builder->join('institution_tier_users','school_orders.school_user_id = institution_tier_users.user_id');
            $where = "(`collegepre_results`.`result_date` >= '$start_date'  AND `collegepre_results`.`result_date` <= '$end_date' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ." AND `products`.`course_type` = 'Core' AND tokens.thirdparty_id!= 0)
                    OR (`tds_results`.`result_date` >= '$start_date' AND `tds_results`.`result_date` <= '$end_date' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ." AND `tds_results`.`course_type` = 'Core' AND `products`.`course_type` = 'Core' AND tokens.thirdparty_id!= 0)";
            $builder->where($where);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                $result = $query->getResult();
                return $result;
            }else{
                return FALSE;
            }
		}

		public function get_higher_details($institution_id = FALSE, $start_date = FALSE, $end_date = FALSE){
            if($start_date != FALSE && $end_date != FALSE){
                $start_date = $start_date." "."00:00:00";
                $end_date = $end_date." "."23:59:59";
            }
            $tier_id = $this->get_user_tiers($institution_id);
            //Select results query for Higher by institution_id, start_date, end_date
			$builder = $this->db->table('school_orders');
            $builder->select('school_orders.id,tokens.thirdparty_id,collegepre_higher_results.result_date,collegepre_higher_results.candidate_id, tds_results.candidate_id as tds_candidateId,tds_results.token as tds_token,tds_results.course_type as tds_test_type');
            $builder->join('tokens','school_orders.id = tokens.school_order_id');
            $builder->join('collegepre_higher_results','tokens.thirdparty_id = collegepre_higher_results.thirdparty_id', 'left');
            $builder->join('tds_results','tokens.thirdparty_id = tds_results.candidate_id', 'left');
            $builder->join('products','tokens.product_id = products.id');
            $builder->join('institution_tier_users','school_orders.school_user_id = institution_tier_users.user_id');
            $where = "(`collegepre_higher_results`.`result_date` >= '$start_date'  AND `collegepre_higher_results`.`result_date` <= '$end_date' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ."  AND `products`.`course_type` = 'Higher' AND tokens.thirdparty_id!= 0)
                    OR (`tds_results`.`result_date` >= '$start_date' AND `tds_results`.`result_date` <= '$end_date' AND `tds_results`.`course_type` = 'Higher' AND `institution_tier_users`.`institutionTierId` = ". $tier_id['id'] ."  AND `products`.`course_type` = 'Higher' AND tokens.thirdparty_id!= 0)";
            $builder->where($where);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                $result = $query->getResult();
                return $result;
            }else{
                return FALSE;
            }
		}

		public function get_results_download($status){
			$builder = $this->db->table('results_download');
            $builder->select('results_download.*');
            $builder->where('results_download.status',$status);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                $result = $query->getResult();
                return $result;
            }else{
                return FALSE;
            }
	}


		/** WP-1221
	 * Function to delete a download result record
	 * @param integer $id
	 * @return boolean
	 */
	public function delete_results_download($id = FALSE){
	    if($id != FALSE){
			$builder = $this->db->table('results_download');
			$builder->where('id', $id);
			$builder->delete();
	        if($this->db->affectedRows() > 0){
	            return TRUE;
	        }else{
	            return FALSE;
	        }
	    }
	}

	public function get_tds_benchmark_results_admin($institution_id,$result_startdate,$result_enddate){
		if($result_startdate && $result_enddate){
			
			$startdate = strtotime(str_replace('/', '-', $result_startdate));
			$enddate = strtotime(str_replace('/', '-', $result_enddate));
			
			if($institution_id){
				$institution_users = $this->get_instituition_tier_users_by_userid($institution_id);
				$institutionusers = implode(", ", $institution_users);
				
				$query = $this->db->query('SELECT B.token,T.thirdparty_id,TR.candidate_id,TTD.test_name,FROM_UNIXTIME(B.datetime, "%Y-%m-%d") AS result_date,B.datetime,TR.processed_data,U.firstname,U.lastname,U.email FROM benchmark_session B 
				JOIN tds_benchmark_results TR ON TR.token = B.token
				JOIN tokens T ON T.token = B.token
				JOIN tds_test_detail TTD ON TTD.test_slug = T.type_of_token
				JOIN users U ON U.id = B.user_id
				WHERE T.school_order_id IN (SELECT id FROM school_orders WHERE school_user_id IN ('.$institutionusers.')) AND B.datetime BETWEEN "'.$startdate.'" AND "'.$enddate.'"');
				
				$result = $query->getResult();
				if ($query->getNumRows() > 0) {
					return $result;
				} else {
					return FALSE;
				}
			}
		}
	}

    public function get_instituition_tier_users_by_userid($userid = FALSE){
		if($userid){
			$query = $this->db->query('SELECT user_id FROM institution_tier_users WHERE institutionTierId = (SELECT `institutionTierId` FROM `institution_tier_users` WHERE `user_id` = '.$userid.')');
			$result = $query->getResult();
			if ($query->getNumRows() > 0) {
				$users = array();
				foreach($result as $user):
					$users[] = $user->user_id;
				endforeach;
                return $users;
            } else {
                return FALSE;
            }
		}
	}

		public function down_time_details($current_utc_timestamp=FALSE){

			$builder = $this->db->table('scheduled_maintenance as SM');
			$builder->select('*');
			$builder->where('SM.start_date_time <= ',$current_utc_timestamp);
			$builder->where('SM.end_date_time > ',$current_utc_timestamp);
			$builder->where('SM.status', 1);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				return TRUE;
			}else{
				return FALSE;
			}
		}

		public function whitelisted_ip(){
			$current_ip = $this->request->getServer('HTTP_X_FORWARDED_FOR') ?? null;
			$builder = $this->db->table('scheduled_maintenance_ip_address')->where('ip_address', $current_ip)->get()->getNumRows();
			if ($builder > 0) {
				return TRUE;
			}else{
				return FALSE;
			}
		}
	
	//send mail
    public function getmail_applinks($level = FALSE) {
        if ($level != FALSE) {
            //current course type
			$builder = $this->db->table('products');
            $builder->select('products.course_type');
            $builder->where('products.level', $level);
            $query = $builder->get();
            $res = $query->getRowArray();
            $course_type = $res['course_type'];
            //compare with view section
            $view_section = strtolower($course_type) . "_section";
			$builder = $this->db->table('page_apps');
            $builder->select('page_apps.view_section,page_apps.platform,page_apps.app_link');
            $builder->where('page_apps.view_section', $view_section);
            $links = $builder->get();
            return $links->getResult();
        }
    }

	//get all CMS based on language
	public function get_all_cms($langcode = FALSE){
		$builder = $this->db->table('cms');
		$builder->select('cms.id as cms_id, cms.page_name, cms.page_slug, cms_translation.id, cms_translation.language_code, cms_translation.meta_title, cms_translation.meta_keywords, cms_translation.meta_description, cms_translation.title, cms_translation.content,cms_translation.content_v2, cms_translation.link ');
		$builder->join('cms_translation', 'cms.id = cms_translation.cms_id');
		if(!empty($langcode)){
			$builder->where('cms_translation.language_code', $langcode);
		} else {
			$builder->where('cms_translation.language_code', 'en');
		}
		$builder->orderBy("cms.id", "asc");
		$query = $builder->get();

		return $query->getResult();
	}

}