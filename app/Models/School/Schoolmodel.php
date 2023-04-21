<?php

namespace App\Models\School;//path
use CodeIgniter\Model;
use App\Models\Site\Bookingmodel;

class Schoolmodel extends Model {

	public $db;
	public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
        $this->bookingmodel = new Bookingmodel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        helper('percentage_helper');
        helper('downtime_helper');
	
	}
    
    /* Function to get school_lists */
    public function school_lists() {

        $builder = $this->db->table('user_roles');
        $builder->select('institution_tiers.id, institution_tiers.organization_name');
        $builder->join('users', 'user_roles.users_id = users.id');
        $builder->join('roles', 'user_roles.roles_id = roles.id');
        $builder->join('institution_tier_users', 'users.id = institution_tier_users.user_id');
        $builder->join('institution_tiers', 'institution_tier_users.institutionTierId = institution_tiers.id');
        $builder->where('roles.name', 'school');
        $builder->orderBy("users.organization_name", "asc");
        $builder->groupBy("institution_tiers.id");
        $result = $builder->get()->getResult();
		return $result;
    }

	/* WP-1388 Start */
	function get_is_used_count($id = false){
		$query = $this->db->query('SELECT count(is_used) as is_used_count FROM `tokens` WHERE is_used = 1 and school_order_id ='.$id);
		$result = $query->getResult();
		if($query->getRowArray() > 0){
			return $result;
		}else{
			return FALSE;
		} 
	}//WP-1388 End

    /* admin view for learner_progress */
    public function learner_progress($instituitionid, $limit = false, $start = false) {

        $product_types = array('cats_core_or_higher','cats_core','cats_higher','cats_primary','catslevel');
        if (!empty($instituitionid)) {


            $builder = $this->db->table('institution_tier_users');
            $builder ->select('institution_tier_users.user_id');
            $builder ->where('institution_tier_users.institutionTierId', $instituitionid);

            $query = $builder->get();
            $tier_users = $query->getResultArray();

            foreach($tier_users as $user){
                $users_tier[] = $user['user_id'];
            }
            if(!empty($users_tier)){
                $builder = $this->db->table('tokens');
                $builder->select('tokens.id, tokens.token, tokens.type_of_token, tokens.user_name, tokens.level, tokens.generated_date, tokens.expiry, tokens.thirdparty_id, school_orders.order_name, school_orders.type_of_token,  DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date, booking.event_id, events.start_date_time,events.tds_option, venues.city, collegepre_results.section_one , collegepre_results.section_two, collegepre_results.candidate_id,collegepre_results.form_id, users.email, users.firstname, users.lastname, users.user_app_id,products.alp_id,products.level,products.course_type,collegepre_higher_results.thirdparty_id as higher_cp, tds_results.candidate_id as tds_candidate_id,tds_results.course_type as type_tds,tds_results.processed_data');
                $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
                $builder->join('institution_tier_users', 'school_orders.school_user_id = institution_tier_users.user_id');
                $builder->join('booking', 'booking.test_delivary_id = tokens.thirdparty_id', 'left');
                $builder->join('events', 'events.id = booking.event_id', 'left');
                $builder->join('venues', 'venues.id = events.venue_id', 'left');
                $builder->join('users', 'users.id = tokens.user_id', 'left');
                $builder->join('products', 'products.id = tokens.product_id', 'left');
                $builder->join('collegepre_results', 'collegepre_results.thirdparty_id = booking.test_delivary_id', 'left');
                $builder->join('collegepre_higher_results', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id', 'left');
                $builder->join('tds_results', 'tds_results.candidate_id = booking.test_delivary_id', 'left');
                $builder->whereIn('tokens.type_of_token', $product_types);
                $builder->where('tokens.thirdparty_id >', "1");
                $builder->whereIn('school_orders.school_user_id', $users_tier);
                $builder->groupBy("tokens.token"); //to control primary double entry
                $builder->limit($limit, $start);
                $query = $builder->get();
                return $query->getResult();
            }else{
                return false; 
            } 
        } else {
            return false;
        }
    }
    /* Function to get list_distributors_highlight */
    public function list_distributors_highlight() {
        $tierData = $this->get_user_tiers();
        $builder = $this->db->table('user_roles');
        $builder->select('*');
        $builder->join('users', 'user_roles.users_id = users.id');
        $builder->join('roles', 'user_roles.roles_id = roles.id');
        $builder->where('roles.name', 'distributor');
        $builder->where('users.country',$tierData['country']);
        $query = $builder->get();
        return $query->getResult();
    }
    /* Function to get user_tiers */
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
    
    /* Function to insert school_distributor */
    public function insert_defaults($defaults_arr) {

        $builder = $this->db->table('school_distributor');
        $builder->insert( $defaults_arr);
        return $this->db->insertID();
    }

    /* Function to fetch orders using pagination */
    public function fetch_orders($limit = false, $start = false) {

        $tierData = $this->get_user_tiers();
        $builder = $this->db->table('school_orders');
        $builder->select('school_orders.id, school_orders.school_user_id, school_orders.order_name, school_orders.type_of_token, school_orders.number_of_tests, DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date, school_orders.payment_done,tds_test_detail.test_name,school_payments.payexecute_result, school_payments.payment_success,institution_tier_users.institutionTierId,institution_tiers.organization_name');
        $builder->join('tds_test_detail', 'tds_test_detail.test_slug = school_orders.type_of_token','left');
        $builder->join('school_payments', 'school_payments.school_order_id = school_orders.id');
        $builder->join('institution_tier_users','institution_tier_users.user_id = school_orders.school_user_id');
        $builder->join('institution_tiers','institution_tiers.id = institution_tier_users.institutionTierId');
        $builder->where('school_payments.payment_success', 'success');
        $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
        $builder->whereNotIn('order_type', array('under13'));
        // $builder->orderBy("school_orders.order_date", "desc");
        $builder->limit($limit, $start);
        
        if ($this->request->getVar('order') == 'ASC') {
             $builder->orderBy("school_orders.order_date", "asc");
        } else {
            $builder->orderBy("school_orders.order_date", "desc");
        }

        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $row) {
                $rowArr[] = $row;
            }
            return $rowArr;
        }
        return FALSE;
    }

    /* Function to check is_used token based on order count */ 
    public function gettokens_status($orderid = FALSE, $order_count = FALSE){
	    if($orderid != FALSE && $order_count != FALSE){
            $builder = $this->db->table('tokens');
            $builder->select('tokens.is_used');
            $builder->where('tokens.is_used', 1);
	        $builder->where('tokens.school_order_id', $orderid);
            $query = $builder->get();
	        if ($query->getNumRows() > 0 && $query->getNumRows() == $order_count) {
	            return TRUE;
	        }else{
	            return FALSE;
	        }
	    }else{
           return FALSE; 
        }
	}
    /* Function to get result_status */
    public function get_result_status($orderid = false, $type_of_token = false) {
        if (!empty($orderid)) {
             $token_type = substr($type_of_token, 0, 12);
             
            if($token_type == "benchmarking"){
                $token_type = "benchmarking";
            }else{
                $token_type = $type_of_token;
            }
            $builder = $this->db->table('tokens');
            if($token_type == "benchmarking" || $token_type == "speaking_test" || $token_type == "benchmarktest"){
                $builder->select('tokens.id');
            }else{
                $builder->select('tokens.id,tds_results.token,CR.thirdparty_id,CHR.thirdparty_id as higher_id');
            }
         
            if($token_type == "benchmarking" || $token_type == "speaking_test" || $token_type == "benchmarktest"){
                $builder->join('tds_benchmark_results', 'tds_benchmark_results.token = tokens.token');
            }else{ 
                $builder->join('tds_results', 'tds_results.token = tokens.token', 'left');
                $builder->join('collegepre_results CR', 'CR.thirdparty_id = tokens.thirdparty_id', 'left');
                $builder->join('collegepre_higher_results CHR', 'CHR.thirdparty_id = tokens.thirdparty_id', 'left');
            }
            $builder->where('tokens.school_order_id', $orderid);
            $builder->where('tokens.is_used', 1);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                if($token_type == "benchmarking" || $token_type == "speaking_test" || $token_type == "benchmarktest"){
                    return True;
                }else{
                    foreach($query->getResultArray() as $values){
                        if ($values['token'] != NULL || $values['thirdparty_id'] != NULL || $values['higher_id'] != NULL) {
                                return true;
                        }
                    }
                }
                
            }else {
                return false;
            }
        } 
    }
    /* Function to get token_access_type */
    public function token_access_type($orderid = false) {
        if (!empty($orderid)) {
            $builder = $this->db->table('tokens');
            $builder->select('tokens.is_supervised');
         
            $builder->where('tokens.school_order_id', $orderid);
            $builder->limit(1);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                return $query->getRowArray();
            }else {
                return false;
            }
        }else{
           return false; 
        }
    }
    /* Function to get pdf_result_tasks */
    public function get_pdf_result_tasks($institution_user_id = FALSE){
	    if($institution_user_id != FALSE){
            $builder = $this->db->table('results_download');
            $builder->where('results_download.institution_user_id', $institution_user_id);
	        $query =  $builder->get();
	        if ($query->getNumRows() > 0) {
	            return $query->getResult();
	        }else{
	            return FALSE;
	        }
	    }
    }
	 /* Function to get tokens_count */
	 public function gettokens_count($orderid = false) {

		$builder = $this->db->table('school_orders');
        $builder->select('number_of_tests');
        $builder->where('id', $orderid);
        $result = $builder->get()->getRowArray();
		return $result['number_of_tests'];
    }
	/* Function to get tokens_view */
	public function gettokens_view($orderid, $limit = false, $start = false) {
		
        $tierData = $this->get_user_tiers();
        if (!empty($orderid)) {
			
            $query = $this->db->query('SELECT type_of_token FROM school_orders WHERE id = '.$orderid);
            $result = $query->getRowObject();
			
			
            if($result != NULL){
                $token_type = $result->type_of_token;    


                if($token_type != "cats_core" && $token_type != "cats_higher" && $token_type != "cats_core_or_higher" && $token_type != "catslevel" && $token_type != "benchmarktest" && $token_type != "speaking_test"){

                    
					$builder = $this->db->table('tokens');
                    $builder->limit($limit, $start);
                    $builder ->select('tokens.id, tokens.token, tokens.type_of_token, tokens.user_name, tokens.level, tokens.generated_date, tokens.expiry, tokens.thirdparty_id, tokens.is_used, school_orders.order_name, tds_test_detail.test_name, school_orders.type_of_token,  DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date,tds_benchmark_results.token as result_token,tds_benchmark_results.candidate_id,users.email, users.user_app_id,"benchmarking" as order_type,FROM_UNIXTIME(benchmark_session.datetime,"%d-%M-%Y") AS result_date,tds_benchmark_results.processed_data,tds_benchmark_results.result_date as tbr_date');
               
                    $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
                    $builder->join('benchmark_session', 'tokens.token = benchmark_session.token','left');
                    $builder->join('tds_benchmark_results', 'tds_benchmark_results.token = tokens.token', 'left');
                    $builder->join('tds_test_detail', 'tokens.type_of_token = tds_test_detail.test_slug');
                    $builder->join('users', 'users.id = tokens.user_id', 'left');
                    $builder->where('tokens.school_order_id', $orderid);

                    // if(!empty($_GET['page'])){
                    //     $page_no = $_GET['page'];
                    //     $builder->limit(10,$page_no * 10);
                    //  }else{
                    //     $builder->limit(10,0 * 10);
                    //  }

					
                    if ($this->request->getVar('order') == 'DESC') {
                        if ($this->request->getVar('val') == 'product') {
                            $builder->orderBy("tokens.level", "desc");
                        } elseif ($this->request->getVar('val') == 'token') {
                            $builder->orderBy("tokens.token", "desc");
                        } elseif ($this->request->getVar('val') == 'test_date') {
                            $builder->orderBy("events.start_date_time", "desc");
                        }
                    } elseif ($this->request->getVar('order') == 'ASC') {
                        if ($this->request->getVar('val') == 'product') {
                            $builder->orderBy("tokens.level", "asc");
                        } elseif ($this->request->getVar('val') == 'token') {
                            $builder->orderBy("tokens.token", "asc");
                        } elseif ($this->request->getVar('val') == 'test_date') {
                             $builder->orderBy("events.start_date_time", "asc");
                        }
                    } else {
                        $builder->orderBy("tokens.id", "desc");
                    } 
					
                    $query = $builder->get();
                    return $query->getResult();
                }else{ 

                   
					$builder = $this->db->table('tokens');
                    $builder->limit($limit, $start);
                    $builder->select('tokens.id, tokens.token, tokens.type_of_token, tokens.user_name, tokens.level, tokens.generated_date, tokens.expiry, tokens.thirdparty_id, school_orders.order_name, school_orders.type_of_token,  DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date, booking.event_id,booking.status as booking_status,booking.event_id,booking.logit_values as final_data,events.start_date_time,events.end_date_time,events.status as event_status, venues.city, collegepre_results.section_one , collegepre_results.section_two, collegepre_results.candidate_id, benchmark_session.benchmark_cefr_level, users.email, users.user_app_id, products.alp_id, products.id as productid, products.course_type, collegepre_higher_results.section_one as higher_section_one, collegepre_higher_results.section_two as higher_section_two, collegepre_higher_results.candidate_id as higher_candidate_id,tds_results.token as tds_token, tds_results.candidate_id as tds_candidate_id, tds_results.processed_data as tds_data, tds_results.course_type as tds_course_type, tds_results.result_date as tbr_date');
                   
                    $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
                    $builder->join('benchmark_session', 'benchmark_session.token = tokens.token', 'left');
                    $builder->join('booking', 'booking.test_delivary_id = tokens.thirdparty_id', 'left');
                    $builder->join('events', 'events.id = booking.event_id', 'left');
                    $builder->join('venues', 'venues.id = events.venue_id', 'left');
                    $builder->join('users', 'users.id = tokens.user_id', 'left');
                    $builder->join('products', 'products.id = tokens.product_id', 'left');
                    $builder->join('collegepre_results', 'collegepre_results.thirdparty_id = booking.test_delivary_id', 'left');
                    $builder->join('collegepre_higher_results', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id', 'left'); //WP-1156 - Higher results
                    $builder->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id', 'left');
                    //for tier multiple users
                    $builder->join('institution_tier_users','institution_tier_users.user_id = school_orders.school_user_id');
                    $builder->join('institution_tiers','institution_tiers.id = institution_tier_users.institutionTierId');
                    $builder->where('tokens.school_order_id', $orderid);
					
				
                    $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');

                    if ($this->request->getVar('order') == 'DESC') {
                        if ($this->request->getVar('val') == 'product') {
                            $builder->orderBy("tokens.level", "desc");
                        } elseif ($this->request->getVar('val') == 'token') {
                            $builder->orderBy("tokens.token", "desc");
                        } elseif ($this->request->getVar('val') == 'test_date') {
                            $builder->orderBy("events.start_date_time", "desc");
                        }
                    } elseif ($this->request->getVar('order') == 'ASC') {
                        if ($this->request->getVar('val') == 'product') {
                            $builder->orderBy("tokens.level", "asc");
                        } elseif ($this->request->getVar('val') == 'token') {
                            $builder->orderBy("tokens.token", "asc");
                        } elseif ($this->request->getVar('val') == 'test_date') {
                            $builder->orderBy("events.start_date_time", "asc");
                        }
                    } else {
                        $builder->orderBy("tokens.id", "desc");
                    } 

                    $query =  $builder->get();
                    return $query->getResult();
                }
            }else {
                return false;
             }  
        }else {
            return false;
        }
    }
	
    /* Function to get school_distributor */
    public function get_defaults() {
		$query = $this->db->query('SELECT * FROM `school_distributor` WHERE setdefault = 1 and school_user_id ='.$this->session->get('user_id'));
		$result = $query->getRowArray();

		if($query->getRowArray() > 0){
			return $result;
		}else{
			return FALSE;
		} 
    }
	
    /* Function to get record_count user_roles */
    public function record_count() {
		
       $tierData = $this->get_user_tiers();
	   $builder = $this->db->table('user_roles');
	   $builder->select('*');
	   $builder->join('users', 'user_roles.users_id = users.id');
	   $builder->join('roles', 'user_roles.roles_id = roles.id');
	   $builder->where('roles.name', '1');
	   $builder->where('users.country', $tierData['country']);
	   $query1 = $builder->get();
	   
	   $query2 = $query1->getNumRows();
	   return $query2;
	   /*return $this->db->count_all_results(); */
    }
	
	/* Function to fetch useing pagination */
    public function fetch_distributor($limit = false, $start = false) {

        $tierData = $this->get_user_tiers();
		 
		$builder = $this->db->table('user_roles');
		$builder->select('*');
		$builder->join('users', 'user_roles.users_id = users.id');
		$builder->join('roles', 'user_roles.roles_id = roles.id');
		$builder->where('roles.name', 'distributor');
		$builder->where('users.country',$tierData['country']);
		$builder->where('users.distributor_all_price_set', '1');
		
		if ($this->request->getVar('order') == 'DESC' ) {
            $builder->orderBy("users.distributor_name", "desc");
        } else {
            $builder->orderBy("users.distributor_name", "asc");
        }  

		$builder->orderBy("users.distributor_name", "asc");
		$query = $builder->get();
        if ($query->getNumRows() > 0) {
            foreach ($query->getResultArray() as $row) {
                $rowArr[] = $row;
            }
            return $rowArr;
        }
        return FALSE;
		
    }
	
    /* Function to save payment_details */
    public function save_payment_details($details) {
		$builder = $this->db->table('school_payments');
        $builder->insert($details);
        return $this->db->insertID();
    }
	/* Function to get over16_search list */
	public function get_over16_list_search($search_item = false, $limit = false, $start = false) {
        $tierData = $this->get_user_tiers();
        if (!empty($search_item)) {
            
			$builder = $this->db->table('tokens  T');

            $builder->limit($limit, $start);	
            $builder->select('U.email, U.user_app_id,T.token, T.type_of_token,T.user_name, T.level, T.is_supervised, T.thirdparty_id, T.is_used, 
            SO.type_of_token,B.event_id,B.status as booking_status,B.event_id,P.alp_id, P.id as productid, P.course_type,
            E.start_date_time,E.end_date_time,E.status as event_status,V.city,TTD.test_name,
            CPR.section_one , CPR.section_two, CPR.candidate_id,CHR.section_one as higher_section_one, CHR.section_two as higher_section_two, CHR.candidate_id as higher_candidate_id,
            TBR.token as TBR_token,TBR.candidate_id as TBR_candidate_id,TR.token as tds_token, TR.candidate_id as tds_candidate_id, TR.course_type as tds_course_type,
            BS.benchmark_cefr_level as BS_level,TBR.processed_data,TBR.result_date as tbr_date,TR.processed_data as tds_data,TR.result_date as tds_result_date');
            
            $builder->join('school_orders SO', 'SO.id = T.school_order_id');
            $builder->join('booking B', 'B.test_delivary_id = T.thirdparty_id', 'left');
            $builder->join('events E', 'E.id = B.event_id', 'left');
            $builder->join('venues V', 'V.id = E.venue_id', 'left');
            $builder->join('users U','U.id = T.user_id', 'left');
            $builder->join('products P', 'P.id = T.product_id', 'left');
            $builder->join('collegepre_results CPR', 'CPR.thirdparty_id = B.test_delivary_id', 'left');
            $builder->join('collegepre_higher_results CHR', 'CHR.thirdparty_id = B.test_delivary_id', 'left'); //WP-1156 - Higher results
            $builder->join('tds_results TR', 'B.test_delivary_id = TR.candidate_id', 'left');
            //for tier multiple users
            $builder->join('institution_tier_users ITU','ITU.user_id = SO.school_user_id');
            $builder->join('institution_tiers IT','IT.id = ITU.institutionTierId');
            //for search all learners in code order 
            $builder->join('benchmark_session BS', 'T.token = BS.token','left');
            $builder->join('tds_benchmark_results TBR', 'TBR.token = T.token', 'left');
            $builder->join('tds_test_detail TTD', 'T.type_of_token = TTD.test_slug','left');
            $builder->where('ITU.institutionTierId = "'.$tierData['id'].'"');
            $builder->where('T.user_id NOT IN (SELECT `user_id` FROM `instituition_learners`)', NULL, FALSE);
            $where = "(U.email LIKE '%$search_item%' ESCAPE '!' OR T.token LIKE '%$search_item%' ESCAPE '!' OR U.name LIKE '%$search_item%' ESCAPE '!')";
            $builder->where($where);
			$builder->groupBy('T.token');
        
			 $data = $builder->get()->getResult();
            return $data;
        }
    }
	
	/* Function to get tokens_export */
    public function tokens_export($orderid) {
        if (!empty($orderid)) {
			
			$builder = $this->db->table('tokens');
            $builder->select('tokens.id, tokens.token,  school_orders.order_name,  DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date');
            $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
            $builder->where('tokens.school_order_id', $orderid);
            $builder->orderBy("tokens.id", "desc");
            $query = $builder->get();
            return $query->getResultArray();
        } else {
            return false;
        }
    }
	
	
	/* WP-1374 query to fetch all details of order ids */
    public function gettokens_view_download($code_order_ids = false){
        if (!empty($code_order_ids)) {
            $arrayCode = explode(',', $code_order_ids);
            $tierData = $this->get_user_tiers();
            $builder = $this->db->table('tokens');
            $builder->select('tokens.id, tokens.token, tokens.type_of_token, tokens.user_name, tokens.level, tokens.generated_date, tokens.expiry, tokens.thirdparty_id, school_orders.order_name, school_orders.type_of_token,  DATE_FORMAT(school_orders.order_date, "%d-%M-%Y") as order_date, booking.event_id,booking.status as booking_status,booking.event_id,booking.logit_values as final_data,events.start_date_time,events.end_date_time,events.status as event_status, venues.city, collegepre_results.section_one , collegepre_results.section_two, collegepre_results.candidate_id, benchmark_session.benchmark_cefr_level, users.email, users.user_app_id, products.alp_id, products.id as productid, products.course_type, collegepre_higher_results.section_one as higher_section_one, collegepre_higher_results.section_two as higher_section_two, collegepre_higher_results.candidate_id as higher_candidate_id,tds_results.token as tds_token, tds_results.candidate_id as tds_candidate_id, tds_results.processed_data as tds_data, tds_results.course_type as tds_course_type,tds_test_detail.test_name,FROM_UNIXTIME(benchmark_session.datetime,"%d-%M-%Y") AS result_date,tds_benchmark_results.processed_data as benchmark_data');
            $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
            $builder->join('benchmark_session', 'tokens.token = benchmark_session.token','left');
            $builder->join('tds_benchmark_results', 'tds_benchmark_results.token = tokens.token', 'left');
            $builder->join('tds_test_detail', 'tokens.type_of_token = tds_test_detail.test_slug','left');
            $builder->join('booking', 'booking.test_delivary_id = tokens.thirdparty_id', 'left');
            $builder->join('events', 'events.id = booking.event_id', 'left');
            $builder->join('venues', 'venues.id = events.venue_id', 'left');
            $builder->join('users', 'users.id = tokens.user_id', 'left');
            $builder->join('products', 'products.id = tokens.product_id', 'left');
            $builder->join('collegepre_results', 'collegepre_results.thirdparty_id = booking.test_delivary_id', 'left');
            $builder->join('collegepre_higher_results', 'collegepre_higher_results.thirdparty_id = booking.test_delivary_id', 'left'); //WP-1156 - Higher results
            $builder->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id', 'left');
            //for tier multiple users
            $builder->join('institution_tier_users','institution_tier_users.user_id = school_orders.school_user_id');
            $builder->join('institution_tiers','institution_tiers.id = institution_tier_users.institutionTierId');
            $builder->whereIn('tokens.school_order_id',$arrayCode);
            $builder->where('institution_tier_users.institutionTierId = "'.$tierData['id'].'"');
            return $builder->get()->getResult();
        } else {
            return FALSE;
        }
    }
    /* Function to get record_class_learners_count */
    function record_class_learners_count($teacher_class_id)
    {
        $builder = $this->db->table('student_classes');
        $builder->select('tokens.token, users.id, users.name, users.username, users.dob, users.creation_time, users.user_app_id, users.email, users.firstname, users.lastname, user_products.thirdparty_id, ,products.alp_id, products.id as productid,products.name as level,GROUP_CONCAT(collegepre_practicetest_results.session_number) as practice_tests,collegepre_results.session_number as final_test,collegepre_results.candidate_id, instituition_learners.cats_product, booking.event_id, events.test_date, venues.city');
        $where = 'user_products.thirdparty_id = student_classes.thirdparty_id AND user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id)'; // Student count for a class - WP-1177
        $builder->join('user_products', $where, 'left');
        $builder->join('users', 'users.id = user_products.user_id', 'left');
        $builder->join('products', 'products.id = user_products.product_id', 'left');
        //join to fetch final results 
        $builder->join('collegepre_results', 'user_products.thirdparty_id = collegepre_results.thirdparty_id', 'left');
        //join to fetch practice test results 
        $builder->join('collegepre_practicetest_results', 'user_products.thirdparty_id = collegepre_practicetest_results.thirdparty_id', 'left');
        $builder->join('instituition_learners', 'instituition_learners.user_id = student_classes.userId', 'left');
        $builder->join('tokens', 'student_classes.thirdparty_id  = tokens.thirdparty_id', 'left');// Student count for a class - WP-1177

        $builder->join('booking', 'booking.test_delivary_id = student_classes.thirdparty_id', 'left');// Student count for a class - WP-1177
        //to bring the last record that learner purchased

        $builder->join('events', 'events.id = booking.event_id', 'left');
        $builder->join('venues', 'venues.id = events.venue_id', 'left');

        $builder->where('student_classes.teacherClassId', $teacher_class_id);
        $builder->where('users.is_active', '1');
        $builder->groupBy("student_classes.userId");
        $query = $builder->get();
        $count = $query->getNumRows();
        return $count;
    }
    /* Function to get class_learner_details */
    public function get_class_learner_details($limit, $start, $teacher_class_id) {

        $builder = $this->db->table('student_classes');
        $builder->select('tokens.token, users.id, users.name, users.username, users.dob, users.creation_time, users.user_app_id, users.email, users.firstname, users.lastname, user_products.thirdparty_id, user_products.course_progress,products.alp_id, products.id as productid,products.name as productname, products.course_type as product_course_type, booking.status as booking_status, collegepre_practicetests.test_number,collegepre_practicetests.candidate_number, instituition_learners.cats_product,student_classes.studentClassId, booking.event_id, events.test_date,events.start_date_time,events.end_date_time,events.status as event_status, venues.city');
        $where = 'user_products.thirdparty_id = student_classes.thirdparty_id AND user_products.id IN(SELECT MAX(user_products.id)FROM user_products GROUP BY user_products.user_id)';
        //join to fetch practise results on college_preresults
        $builder->join('user_products', $where, 'left');
        $builder->join('users', 'users.id = user_products.user_id', 'left');
        $builder->join('products', 'products.id = user_products.product_id', 'left');
        $builder->join('collegepre_practicetests', 'user_products.thirdparty_id = collegepre_practicetests.thirdparty_id', 'left');
        $builder->join('tds_practicetest_results', 'user_products.thirdparty_id = tds_practicetest_results.candidate_id', 'left');
        $builder->join('instituition_learners', 'instituition_learners.user_id = student_classes.userId', 'left');
        $builder->join('tokens', 'student_classes.thirdparty_id  = tokens.thirdparty_id', 'left');

        $builder->join('booking', 'booking.test_delivary_id = student_classes.thirdparty_id', 'left');
        $builder->join('events', 'events.id = booking.event_id', 'left');
        $builder->join('venues', 'venues.id = events.venue_id', 'left');

        $builder->where('student_classes.teacherClassId', $teacher_class_id);
        $builder->where('users.is_active', '1');
        $builder->groupBy("users.id");
        $builder->limit($limit, $start);
        if ($this->request->getVar('name') == 'ASC') {
            $builder->orderBy("users.name", "asc");
        } else {
            $builder->orderBy("users.name", "desc");
        }

        $query = $builder->get();
        $u16_details = $query->getResultArray();
        if ($query->getNumRows() > 0) {
            foreach ($u16_details as $basic_details) {
                $practice_test_array[$basic_details['thirdparty_id']] = $this->get_result_u16learners_practice($basic_details['thirdparty_id'], $basic_details['cats_product']);
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
                $u16_details[$key]['practice_test'] = $practice_test_array[$basic_details['thirdparty_id']];
                $u16_details[$key]['final_test'] = $final_test_array[$basic_details['thirdparty_id']];
                $u16_details[$key]['practice_test_tds'] = $practice_test_array_tds[$basic_details['thirdparty_id']];

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
                }
            }
        }

        return $u16_details;
    }
    /* Function to get result_u16learners_practice */
    function get_result_u16learners_practice($thirdparty_id = FALSE, $product_type = FALSE) {
        $practice_result_details = array();
        if ($product_type == "cats_core" || $product_type == NULL || $product_type == '') {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {

                $builder = $this->db->table('collegepre_practicetest_results');
                $builder->select('collegepre_practicetest_results.*');
                $builder->where('collegepre_practicetest_results.thirdparty_id = "' . $thirdparty_id . '"');
                $query = $builder->get();
                //echo $this->db->last_query();
                $result_practice = $query->getResultArray();
                //print '<pre>'; print_r($result_practice); print '</pre>';
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

            return $practice_result_details;
        } elseif ($product_type == "cats_primary") {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $builder = $this->db->table('collegepre_results');
                $builder->select('collegepre_results.*');
                $builder->join('collegepre_formcodes', 'collegepre_formcodes.form_id = collegepre_results.form_id', 'left');
                $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                $builder->where('collegepre_formcodes.type = "Practice test"');
                
                $query = $builder->get();
                $result_practice = $query->getRow();
                if (count((array)$result_practice) > 0) {
                    $practice_result_details['percent'] = @get_primary_results($result_practice->section_one, $result_practice->section_two);
                    $practice_result_details['practice_test'] = 1;    
                } else {
                    $practice_result_details['practice_test'] = 0;
                }
            } else {
                $practice_result_details['practice_test'] = 0;
            }

            return $practice_result_details;
        }
    }
    /* Function to get result_u16learners_practice_tds */
    function get_result_u16learners_practice_tds($user_thirparty_id = FALSE, $test_type = FALSE, $course_type = FALSE) {
        if ($course_type == "cats_core" || $course_type == NULL || $course_type == '') {
            if ($user_thirparty_id != NULL && $user_thirparty_id != 0) {
                $builder = $this->db->table('tds_tests TT');
                $builder->select('TT.token, TT.status, TPR.processed_data, TPR.candidate_id, TPR.result_date as tds_result_date');
                $builder->join('tds_practicetest_results TPR', 'TT.token = TPR.token', 'LEFT');
                $builder->where('TT.candidate_id', $user_thirparty_id);
                $builder->where('TT.test_type', $test_type);
                $query = $builder->get();
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
        }elseif($course_type == 'cats_primary') {
            if ($user_thirparty_id != NULL && $user_thirparty_id != 0) {
                $builder = $this->db->table('tds_practicetest_results');
                $builder->select('tds_practicetest_results.*');
                $builder->where('tds_practicetest_results.candidate_id = "' . $user_thirparty_id . '"');
                $query = $builder->get();
                $result_practice = $query->getRow();
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
    /* Function to get result_u16learners_final */
    function get_result_u16learners_final($thirdparty_id = FALSE, $product_type = FALSE) {
        $final_test = array();
        if ($product_type == "cats_core" || $product_type == NULL || $product_type == '') {
            if ($thirdparty_id != NULL && $thirdparty_id != 0) {
                $course_details = $this->get_course_type_by_thirdparty_id($thirdparty_id); //WP-1156 - Higher results and Check if It Higher course
                $delivery_type_option = $this->get_delivery_type_by_thirdparty_id($thirdparty_id); //WP-1276 - Tds Higher results
              $delivery_type = ($delivery_type_option) ? $delivery_type_option[0]->tds_option : "catstds"; 
                if($course_details[0]->course_type === 'Higher'){
                    if($delivery_type != NULL && $delivery_type == 'catstds'){
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
                     if($delivery_type != NULL && $delivery_type == 'catstds'){
                        $builder = $this->db->table('tds_results');
                        $builder->select('tds_results.*');
                        $builder->where('tds_results.candidate_id = "' . $thirdparty_id . '"'); 
                    }else{
                        $builder = $this->db->table('collegepre_results');
                        $builder->select('collegepre_results.*');
                        $builder->where('collegepre_results.thirdparty_id = "' . $thirdparty_id . '"');
                    }
                }
                $query = $builder->get();
                $result_final = $query->getRow();
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
                }
                else {
                    $final_test['final_result_status'] = 0;
                }
            } else {
                $final_test['final_result_status'] = 0;
            }
            return $final_test;
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
                $result_final = $query->getRow();
                if ($query->getNumRows() > 0) {
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

            return $final_test;
        }
    }

    /* Function to get course_type_by_thirdparty_id */
    public function get_course_type_by_thirdparty_id($thirdparty_id = FALSE){
	    if($thirdparty_id != FALSE){
            $builder = $this->db->table('products');
            $builder->select('products.*,user_products.*');
	        $builder->join('user_products', 'user_products.product_id = products.id');
	        $builder->where('user_products.thirdparty_id', $thirdparty_id);
	        $query = $builder->get();
	        if ($query->getNumRows() > 0) {
	            return $query->getResult();
	        }else{
	            return FALSE;
	        }
	    }
	}
    /* Function to get delivery_type_by_thirdparty_id */
    public function get_delivery_type_by_thirdparty_id($thirdparty_id = FALSE){
	    if($thirdparty_id != FALSE){
            $builder = $this->db->table('booking');
            $builder->select('events.id,events.tds_option');
	        $builder->join('events', 'booking.event_id = events.id');
	        $builder->where('booking.test_delivary_id', $thirdparty_id);
	        $query = $builder->get();
	        if ($query->getNumRows() > 0) {
	            return $query->getResult();
	        }else{
	            return FALSE;
	        }
	    }
	}

    /* Get access details for primary users by user id */
    public function get_access_details($userid = FALSE) {
        if ($userid) {
            $userid = implode(", ", $userid);
            $query = $this->db->query('SELECT U.username,U.password_visible,U.firstname,U.lastname,U.dob,U.access_detail_language,IL.cats_product FROM users U JOIN instituition_learners IL ON IL.user_id = U.id WHERE U.id IN (' . $userid . ')');
            $result = $query->getResult();
            if ($query->getNumRows() > 0) {
                return $result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    
    /* Function to check if already exists default */
    function check_exists_defaults($dist_id = FALSE ) {
        if ($dist_id != FALSE) {

            $builder = $this->db->table('school_distributor');
            $builder->select('*');
            $builder->where('distributor_id', $distributor_id);
            $builder->where('school_user_id', $this->session->get('user_id'));
            $query = $builder->get();
            $result =  $query->getResult();

            if (!empty($result)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /* Function to make defaults for others to 0 */
    public function update_other_to_default_zero() {
        // $this->db->update('school_distributor', array('setdefault' => 0), array('school_user_id' => $this->session->userdata('user_id')));

        $builder = $this->db->table('school_distributor');
        $builder->where('school_user_id', $this->session->userdata('user_id'));
        $builder->update(array('setdefault' => 0));
			
        return TRUE;
    }
    /* Function to get session_details */
    public function get_session_details($session_id = FALSE){
		if($session_id){			
			$result = array();  	
			$detailquery = $this->db->query('SELECT E.start_date_time,E.end_date_time,V.venue_name,E.capacity,E.fixed_capacity FROM events E JOIN venues V ON V.id = E.venue_id WHERE E.id = '.$session_id);
			$detailresult = $detailquery->getRow();
			if ($detailquery->getNumRows() > 0) {
				$result['detail'] = $detailresult;
			}else{
				$result['detail'] = '';
			}			
			
			$productquery = $this->db->query('SELECT P.id,P.name,P.course_type FROM events E JOIN event_products EP ON EP.event_id = E.id JOIN products P ON P.id = EP.product_id WHERE E.id = '.$session_id);
            $productresult = $productquery->getResult();
			if ($productquery->getNumRows() > 0) {
                $result['products'] = $productresult;
            } else {
                $result['products'] = '';
            }
			
			$allocatedquery = $this->db->query('SELECT COUNT(*) AS allocated FROM booking WHERE event_id = '.$session_id.' AND status = 1');
			$allocatedresult = $allocatedquery->getRow();
			if ($allocatedquery->getNumRows() > 0) {
                $result['allocated'] = $allocatedresult->allocated;
            } else {
                $result['allocated'] = 0;
            }
   
			return $result;
		}
	}
    /* Function to get learner_allocation_details */
    public function get_learner_allocation_details($session_products = FALSE, $session_product_group = FALSE, $search_item = FALSE, $filter_items = FALSE){
		if($session_products){
			$userid = $this->session->get('user_id');
			$institution_users = $this->get_instituition_tier_users_by_userid($userid);
			$learner_details = $this->get_learner_details_allocation($session_products, $institution_users, $session_product_group, $search_item, $filter_items);
			if($learner_details){
				return $learner_details;
			}else{
				return FALSE;
			}
		}
	}
    /* Function to get instituition_tier_users_by_userid */
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
    /* Function to get learner_details_allocation */
    public function get_learner_details_allocation($products = FALSE, $institutionusers = FALSE, $session_product_group = FALSE, $search_item = FALSE, $filter_items = FALSE){
		if($products && $institutionusers){
		    /* WP-1122 - Filter & search implementation in learner allocation page START */
		    $search_where = $filter_where = "";
		    if($search_item != FALSE){
		        $search_where = " AND (U.firstname LIKE '%".$search_item."%' OR U.lastname LIKE '%".$search_item."%' OR U.username LIKE '%".$search_item."%' OR U.email LIKE '%".$search_item."%')";
		    }
		    if($filter_items != FALSE){
		        $teacher_where = $ppm_where = "";
		        if(array_key_exists("level",$filter_items)){
		            $products = $filter_items['level'];
		        }
		        if(array_key_exists("teacher",$filter_items)){
		          $teacher = $filter_items['teacher']; 
		          $teacher_where = " AND TC.teacherClassId IN (".implode(',',$teacher).")";
		        }
		        if(array_key_exists("ppm",$filter_items)){
		            $ppm = $filter_items['ppm'];
		            $ppm_where = ($ppm > 0) ? " AND UP.course_progress > ".$ppm."" : "";
		        }
		        $filter_where = $teacher_where . " " . $ppm_where; 
		    }
		    /* WP-1122 - Filter & search implementation in learner allocation page END */
			if($session_product_group && $session_product_group == 'Primary'){
				$final_thirdpartyids = $this->get_finaltest_thirdpartyid_for_primary($institutionusers,$products);
				if($final_thirdpartyids){
					$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,UP.course_progress,P.name AS level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,"under13" AS order_type FROM instituition_learners IL 
					JOIN user_products UP ON UP.user_id = IL.user_id 
					JOIN products P ON P.id = UP.product_id 
					JOIN users U ON U.id = IL.user_id
					LEFT JOIN student_classes SC ON SC.thirdparty_id = UP.thirdparty_id 
					LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
                    LEFT JOIN classes C ON C.classId = TC.teacherClassId
					LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
					LEFT JOIN users TU ON TU.id = IT.teacherId
					WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_primary" AND UP.product_id IN ('.implode(",",$products).') AND UP.thirdparty_id NOT IN ('.implode(",",$final_thirdpartyids).') AND UP.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1)' . $search_where . $filter_where);
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}else{
					$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,UP.course_progress,P.name AS level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,"under13" AS order_type FROM instituition_learners IL 
					JOIN user_products UP ON UP.user_id = IL.user_id 
					JOIN products P ON P.id = UP.product_id 
					JOIN users U ON U.id = IL.user_id
					LEFT JOIN student_classes SC ON SC.thirdparty_id = UP.thirdparty_id 
					LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
                    LEFT JOIN classes C ON C.classId = TC.teacherClassId
					LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
					LEFT JOIN users TU ON TU.id = IT.teacherId
					WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_primary" AND UP.product_id IN ('.implode(",",$products).') AND UP.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1)' . $search_where . $filter_where);
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}
			}else{
				$final_thirdpartyids = $this->get_finaltest_thirdpartyid_for_core($institutionusers,$products);
				if($final_thirdpartyids){
					$query = $this->db->query('SELECT SO.id,SO.order_type,T.user_id,T.thirdparty_id,T.product_id,T.level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,UP.course_progress FROM school_orders SO
							JOIN tokens T ON T.school_order_id = SO.id
							JOIN users U ON U.id = T.user_id
							JOIN user_products UP ON UP.thirdparty_id = T.thirdparty_id 
							LEFT JOIN student_classes SC ON SC.thirdparty_id = T.thirdparty_id
							LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
							LEFT JOIN classes C ON C.classId = TC.teacherClassId
							LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
							LEFT JOIN users TU ON TU.id = IT.teacherId
							WHERE SO.school_user_id IN ('.implode(",",$institutionusers).') AND T.product_id IN ('.implode(",",$products).') AND T.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1) AND UP.thirdparty_id NOT IN ('.implode(",",$final_thirdpartyids).')  AND T.thirdparty_id > 0' . $search_where . $filter_where . ' ORDER BY UP.id DESC');
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}else{
					$query = $this->db->query('SELECT SO.id,SO.order_type,T.user_id,T.thirdparty_id,T.product_id,T.level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,UP.course_progress FROM school_orders SO
							JOIN tokens T ON T.school_order_id = SO.id
							JOIN users U ON U.id = T.user_id
							JOIN user_products UP ON UP.thirdparty_id = T.thirdparty_id 
							LEFT JOIN student_classes SC ON SC.thirdparty_id = T.thirdparty_id
							LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
							LEFT JOIN classes C ON C.classId = TC.teacherClassId
							LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
							LEFT JOIN users TU ON TU.id = IT.teacherId
							WHERE SO.school_user_id IN ('.implode(",",$institutionusers).') AND T.product_id IN ('.implode(",",$products).') AND T.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1) AND T.thirdparty_id > 0' . $search_where . $filter_where . ' ORDER BY UP.id DESC');
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}
			}
		}
	}

    /* Function to Supervisor dropdown fetching */
    public function supervisor_details_fetch($session_products = FALSE, $session_product_group = FALSE){
		if($session_products){
			$userid = $this->session->get('user_id');
			$institution_users = $this->get_instituition_tier_users_by_userid($userid);
			$learner_details = $this->supervisor_details_fetch_allocation($session_products, $institution_users, $session_product_group);
			if($learner_details){
				return $learner_details;
			}else{
				return FALSE;
			}
		}
	}

    /* Function to get supervisor_details_fetch_allocation */
    public function supervisor_details_fetch_allocation($products = FALSE, $institutionusers = FALSE, $session_product_group = FALSE){
        $search_where = $filter_where = "";
        if($products && $institutionusers){
			if($session_product_group && $session_product_group == 'Primary'){
				$final_thirdpartyids = $this->get_finaltest_thirdpartyid_for_primary($institutionusers,$products);
				if($final_thirdpartyids){
					$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,UP.course_progress,P.name AS level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,"under13" AS order_type FROM instituition_learners IL 
					JOIN user_products UP ON UP.user_id = IL.user_id 
					JOIN products P ON P.id = UP.product_id 
					JOIN users U ON U.id = IL.user_id
					LEFT JOIN student_classes SC ON SC.thirdparty_id = UP.thirdparty_id 
					LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
                    LEFT JOIN classes C ON C.classId = TC.teacherClassId
					LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
					LEFT JOIN users TU ON TU.id = IT.teacherId
					WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_primary" AND UP.product_id IN ('.implode(",",$products).') AND UP.thirdparty_id NOT IN ('.implode(",",$final_thirdpartyids).') AND UP.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1)' . $search_where . $filter_where);
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}else{
					$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,UP.course_progress,P.name AS level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,"under13" AS order_type FROM instituition_learners IL 
					JOIN user_products UP ON UP.user_id = IL.user_id 
					JOIN products P ON P.id = UP.product_id 
					JOIN users U ON U.id = IL.user_id
					LEFT JOIN student_classes SC ON SC.thirdparty_id = UP.thirdparty_id 
					LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
                    LEFT JOIN classes C ON C.classId = TC.teacherClassId
					LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
					LEFT JOIN users TU ON TU.id = IT.teacherId
					WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_primary" AND UP.product_id IN ('.implode(",",$products).') AND UP.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1)' . $search_where . $filter_where);
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}
			}else{
				$final_thirdpartyids = $this->get_finaltest_thirdpartyid_for_core($institutionusers,$products);
				if($final_thirdpartyids){
					$query = $this->db->query('SELECT SO.id,SO.order_type,T.user_id,T.thirdparty_id,T.product_id,T.level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,UP.course_progress FROM school_orders SO
							JOIN tokens T ON T.school_order_id = SO.id
							JOIN users U ON U.id = T.user_id
							JOIN user_products UP ON UP.thirdparty_id = T.thirdparty_id 
							LEFT JOIN student_classes SC ON SC.thirdparty_id = T.thirdparty_id
							LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
							LEFT JOIN classes C ON C.classId = TC.teacherClassId
							LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
							LEFT JOIN users TU ON TU.id = IT.teacherId
							WHERE SO.school_user_id IN ('.implode(",",$institutionusers).') AND T.product_id IN ('.implode(",",$products).') AND T.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1) AND UP.thirdparty_id NOT IN ('.implode(",",$final_thirdpartyids).')  AND T.thirdparty_id > 0' . $search_where . $filter_where . ' ORDER BY UP.id DESC');
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}else{
					$query = $this->db->query('SELECT SO.id,SO.order_type,T.user_id,T.thirdparty_id,T.product_id,T.level,U.firstname,U.lastname,U.username,U.email,SC.teacherClassId,C.englishTitle,TC.institutionTeacherId,IT.teacherId,TU.firstname as teacherfirstname,TU.lastname as teacherlastname,UP.course_progress FROM school_orders SO
							JOIN tokens T ON T.school_order_id = SO.id
							JOIN users U ON U.id = T.user_id
							JOIN user_products UP ON UP.thirdparty_id = T.thirdparty_id 
							LEFT JOIN student_classes SC ON SC.thirdparty_id = T.thirdparty_id
							LEFT JOIN teacher_classes TC ON TC.teacherClassId = SC.teacherClassId
							LEFT JOIN classes C ON C.classId = TC.teacherClassId
							LEFT JOIN institution_teachers IT ON IT.institutionTeacherId = TC.institutionTeacherId
							LEFT JOIN users TU ON TU.id = IT.teacherId
							WHERE SO.school_user_id IN ('.implode(",",$institutionusers).') AND T.product_id IN ('.implode(",",$products).') AND T.thirdparty_id NOT IN (SELECT test_delivary_id FROM booking WHERE status = 1) AND T.thirdparty_id > 0' . $search_where . $filter_where . ' ORDER BY UP.id DESC');
					$result = $query->getResult();
					if ($query->getNumRows() > 0) {
						return $result;
					} else {
						return FALSE;
					}
				}
			}
		}
    }
    /* Function to get finaltest_thirdpartyid_for_primary */
    public function get_finaltest_thirdpartyid_for_primary($institutionusers = FALSE,$products = FALSE){
		if($products && $institutionusers){	
			$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,P.name AS level,CR.candidate_name,CF.type,TR.candidate_id FROM instituition_learners IL 
				JOIN user_products UP ON UP.user_id = IL.user_id 
				JOIN products P ON P.id = UP.product_id LEFT 
                JOIN tds_results TR ON TR.candidate_id = UP.thirdparty_id LEFT
				JOIN collegepre_results CR ON CR.thirdparty_id = UP.thirdparty_id LEFT
				JOIN collegepre_formcodes CF ON CF.form_id = CR.form_id 
				WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_primary" AND UP.product_id IN ('.implode(",",$products).') AND (CF.type = "Live test" OR TR.candidate_id is NOT NULL)');
            $finalresults = $query->getResult();
			if ($query->getNumRows() > 0) {
				$thirdparty_ids = array();
				foreach($finalresults as $result):
					$thirdparty_ids[] = $result->thirdparty_id;
				endforeach;
				return $thirdparty_ids;
			} else {
				return FALSE;
			}
		}
	}
    /* Function to get finaltest_thirdpartyid_for_core */
    public function get_finaltest_thirdpartyid_for_core($institutionusers = FALSE,$products = FALSE){
		if($products && $institutionusers){	
			$query = $this->db->query('SELECT IL.user_id,UP.thirdparty_id,P.name AS level,CR.candidate_name,CF.type,TR.candidate_id FROM instituition_learners IL 
				JOIN user_products UP ON UP.user_id = IL.user_id 
				JOIN products P ON P.id = UP.product_id LEFT
                JOIN tds_results TR ON TR.candidate_id = UP.thirdparty_id LEFT
				JOIN collegepre_results CR ON CR.thirdparty_id = UP.thirdparty_id LEFT
				JOIN collegepre_formcodes CF ON CF.form_id = CR.form_id				
				WHERE IL.instituition_id IN ('.implode(",",$institutionusers).') AND IL.cats_product = "cats_core" AND UP.product_id IN ('.implode(",",$products).') AND (CF.type = "Live test" OR TR.candidate_id is NOT NULL)');
            $finalresults = $query->getResult();
			if ($query->getNumRows() > 0) {
				$thirdparty_ids = array();
				foreach($finalresults as $result):
					$thirdparty_ids[] = $result->thirdparty_id;
				endforeach;
				return $thirdparty_ids;
			} else {
				return FALSE;
			}
		}
	}
    /* Function to get learner_alloted_details */
    public function get_learner_alloted_details($session_id = FALSE,$session_product_group = FALSE){
		if($session_id && $session_product_group){
			if($session_product_group == 'Primary'){
				$query = $this->db->query('SELECT B.product_id,B.test_delivary_id AS thirdparty_id,B.user_id,U.firstname,U.lastname,U.email,U.username,U.password_visible,P.name AS product_name,"under13" AS order_type FROM booking B 
					JOIN users U ON U.id = B.user_id
					JOIN products P ON P.id = B.product_id
					WHERE B.event_id = '.$session_id.' AND B.status = 1');//To get final test test number and candidate number
				$result = $query->getResult();
				if ($query->getNumRows() > 0) {
					return $result;
				} else {
					return FALSE;
				}
			}else{		
				$query = $this->db->query('SELECT B.product_id,B.test_delivary_id AS thirdparty_id,B.user_id,U.firstname,U.lastname,U.email,U.username,U.password_visible,P.name AS product_name,SO.order_type FROM booking B 
					JOIN users U ON U.id = B.user_id
					JOIN products P ON P.id = B.product_id
					JOIN tokens T ON T.thirdparty_id = B.test_delivary_id
					JOIN school_orders SO ON SO.id = T.school_order_id
					WHERE B.event_id = '.$session_id.' AND B.status = 1');//To get final test test number and candidate number
				$result = $query->getResult();
				if ($query->getNumRows() > 0) {
					return $result;
				} else {
					return FALSE;
				}
			}
			
		}	
	}
    /* Function to get course_lists */
    public function get_course_lists($tablename = FALSE, $type = FALSE){
	    if($type != FALSE && $tablename != FAlse ){
            $builder = $this->db->table($tablename);
	        if($tablename == 'tds_test_detail'){
	            $builder->where('tds_test_detail.tds_group_id', $type);
	        }elseif($tablename == 'products'){
	            $builder->where('products.course_type', $type);
	            $builder->where('products.active', 1);
	        }
	        $query = $builder->get();
	        if ($query->getNumRows() > 0) {
	            return $query->getResult();
	        }else{
	            return FALSE;
	        }
	    }
	}
    /* Function to get tds_benchmark_results */
    public function get_tds_benchmark_results($result_startdate,$result_enddate){
		if($result_startdate && $result_enddate){
			
			$startdate = strtotime(str_replace('/', '-', $result_startdate." 00:00:00"));
            $enddate = strtotime(str_replace('/', '-', $result_enddate." 23:59:59"));
			
			if($this->session->get('user_id')){
				$institution_users = $this->get_instituition_tier_users_by_userid($this->session->get('user_id'));
				$institutionusers = implode(", ", $institution_users);

				$query = $this->db->query('SELECT B.token,TTD.test_name,FROM_UNIXTIME(B.datetime, "%Y-%m-%d") AS result_date,B.datetime,TR.processed_data,U.firstname,U.lastname,U.email FROM benchmark_session B 
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

    /**WP-1221
	 * Function to insert result download datas
	 * @param array $results_arr
	 * @return integer
	 */
	public function insert_results_download($results_arr) {
        $builder = $this->db->table('results_download');
	    $builder->insert($results_arr);
	    return $this->db->insertID();
	}
    
 
}
