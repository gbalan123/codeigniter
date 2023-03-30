<?php 
namespace App\Models\Site;//path
use CodeIgniter\Model;

class Bookingmodel extends Model
{
	public $db;
	public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
		$this->session = session();
	}

	public function get_country_by_toporder(){
		$builder = $this->db->table('countries');
		$builder->select('*');
		$builder->orderBy('adminOrder', 'ASC');
		$result = $builder->get()->getResult();
	    return $result;
	}

	public function get_country_by_othercountries(){
		$builder = $this->db->table('countries');
		$builder->select('*');
		$builder->where('countries.adminOrder' <> 0);
		$builder->orderBy('countryName', 'ASC');
		$result = $builder->get()->getResult();
		$builder->countAll();
		return $result;
	}

	public function get_regions($rd = FALSE){
	    if($rd != FALSE){
			$builder = $this->db->table('regions');
	        $builder->select('*');
	        $builder->where('regions.id', $rd);
	        $result = $builder->get()->getResult();
	    }else{
			$builder = $this->db->table('regions');
	        $builder->select('*');
	        $result = $builder->get()->getResult();
	    }
	    return $result;
	}

	public function get_institution_timezone($thirdparty_id = FALSE){
		$builder = $this->db->table('tokens');
    	$builder->select('institution_tiers.timezone');
    	$builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
    	$builder->join('institution_tier_users', 'institution_tier_users.user_id = school_orders.school_user_id');
    	$builder->join('institution_tiers', 'institution_tiers.id = institution_tier_users.institutionTierId');
    	$builder->where('tokens.thirdparty_id', $thirdparty_id);
    	$query = $builder->get();
    	if($query->getNumRows() > 0){
    	    $Institution_tier_timezone = $query->getResult();
    	    return $Institution_tier_timezone[0]->timezone;
    	}else{
    	    return false;
    	}
	}
	
	public function tokens_thirdparty_id($tokens_thirdparty_id){


		$builder = $this->db->table('user_products');
		$builder->select('user_products.course_progress');
		$builder->where('thirdparty_id', $tokens_thirdparty_id);
		$query = $builder->get();
		if($query->getNumRows() > 0){
    	    return $results = $query->getRow();
    	}else{
    	    return false;
    	}


	}

	public function collegepre_practicetest_results($tokens_thirdparty_id){
	$query = $this->db->query('SELECT * FROM  collegepre_practicetest_results WHERE thirdparty_id = "' . $tokens_thirdparty_id . '" ');
	$results = $query->getResultArray();
	}

	//result display setting from booking 
	public function result_display_higher($id){ 	
		$builder = $this->db->table('booking');
		$builder->select('booking.logit_values');
		$builder->where('booking.test_delivary_id', $id);
		$query = $builder->get();
		if( $query->getNumRows() > 0){
		    return $query->getRowArray();
		}else{
		    return false;
		}
	}

	public function tds_practice_detail($user_thirparty_id = false, $test_type = false){
		$builder = $this->db->table('tds_tests TT');
	    $builder->select('TT.token, TT.status, TPR.processed_data, TPR.candidate_id,TPR.result_date as practice_result_date');
            
		$builder->join('tds_practicetest_results TPR', 'TT.token = TPR.token', 'LEFT');
		$builder->where('TT.candidate_id', $user_thirparty_id);
	    $builder->where('TT.test_type', $test_type);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                return $query->getResultArray();
            }else{
                return FALSE;
            }
	}

	public function get_products($id=false){
		$builder = $this->db->table('user_products');		
		$builder->select('user_products.id as user_product_id, products.level, products.alp_id, products.moodle_course_id as course_id, products.id as product_id, products.name as product_name, products.course_type as course_type, user_products.purchased_date as DATE, user_products.country as COUNTRY, user_products.thirdparty_id as user_thirparty_id');
		$builder->join('products', 'user_products.product_id = products.id');
		$builder->where('user_products.user_id', $id);		
		$builder->where('user_products.payment_done', 1);		
		$builder->orderBy("user_products.id", "desc");	
		$query = $builder->get();
		return $query->getResultArray();
	}



	public function result_display_settings($id){
		$builder = $this->db->table('booking');	
		$builder->select('*');
		$builder->where('booking.test_delivary_id', $id);
		$query = $builder->get();
		if( $query->getNumRows() > 0){
		    return $query->getRowArray();
		}else{
			$builder = $this->db->table('result_display_settings');	
		    $builder->select('*');
		    $query = $builder->get();
		    return $query->getRowArray();
		}
	}
	
	
	public function get_delivery_type_by_thirdparty_id($thirdparty_id = FALSE){

	    if($thirdparty_id != FALSE){
			$builder = $this->db->table('booking');
            $builder->select('events.id,events.tds_option');
	        $builder->join('events', 'booking.event_id = events.id');
	        $builder->where('booking.test_delivary_id', $thirdparty_id);
	        $query =$builder->get();
	        if ($query->getNumRows() > 0) {
	            return $query->getResult();
	        }else{
	            return FALSE;
	        }
	    }
	}

	//get distributor details
	public function get_dist_all_by_token($token = false ,$check = false)
	{
		if($token != FALSE && $check != FALSE ){
			if($check == 'count'){
				$builder = $this->db->table('tokens');
				$builder->select('*');
				$builder->where('tokens.is_used', '0');
				$builder->where('tokens.token', strtoupper($token));
				$query =  $builder->get();
				$rowcount = $query->getNumRows();
				if($rowcount > 0):
					return TRUE;
				else:
					return FALSE;
				endif; 
			}elseif ($check == 'result') {
				$builder = $this->db->table('tokens');
				$builder->select('users.organization_name');
				$builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
				$builder->join('users', 'users.id = school_orders.school_user_id');
				$builder->where('tokens.is_used', '0');
				$builder->where('tokens.token', $token);
				$builder->limit(1);
				$query = $builder->get();
				return $query->getRowArray();
			}elseif($check == 'used'){
				$builder = $this->db->table('tokens');
				$builder->select('*');
				$builder->where('tokens.is_used', '1');
				$builder->where('tokens.token', strtoupper($token));
				$query = $builder->get();
				$rowcount = $query->getNumRows();
				if($rowcount > 0):
					return TRUE;
				else:
					return FALSE;
				endif; 
			}
		}			
	}

	// get all bookings of a user	
	public function get_bookings2($id = false, $test_deilvary_id = false)
	{

		$builder = $this->db->table('user_products');
		$builder->select('user_products.*, products.name, products.level, products.alp_id, products.course_type as course_type, booking.id as booking_id, booking.event_id as booking_event_id, booking.logit_values, booking.test_delivary_id as booking_test_delivary_id, booking.datetime as booking_date,events.start_date_time,events.end_date_time,events.notes,venues.venue_name,venues.address_line1,venues.address_line2,venues.city, booking.status as booking_status, booking.score_calculation_type_id as booking_score_calculation,  DATE_FORMAT(events.test_date,"%d %M %Y") as formatdate, collegepre_results.*, user_products.thirdparty_id as user_thirparty_id, users.user_app_id,collegepre_higher_results.section_one as higher_section_one, collegepre_higher_results.section_two as higher_section_two, collegepre_higher_results.candidate_id as higher_candidate_id, collegepre_higher_results.thirdparty_id as higher_thirdparty_id, tds_results.processed_data as tds_data,tds_results.candidate_id as tds_thirdparty_id, tds_results.token as tds_token,tds_results.course_type as tds_coursetype');
		$builder->join('products', 'products.id = user_products.product_id');		
		$builder->join('booking', 'booking.test_delivary_id = user_products.thirdparty_id', 'left');
        $builder->join('events', 'events.id = booking.event_id', 'left');
		$builder->join('venues', 'venues.id = events.venue_id', 'left');
		$builder->join('collegepre_results', 'booking.test_delivary_id = collegepre_results.thirdparty_id', 'left');
		$builder->join('collegepre_higher_results', 'booking.test_delivary_id = collegepre_higher_results.thirdparty_id', 'left'); //WP-1156 - Higher results
		$builder->join('tds_results', 'booking.test_delivary_id = tds_results.candidate_id', 'left');
        $builder->join('users', 'users.id = booking.user_id', 'left');	
		$builder->orderBy("user_products.id", "desc");	

                if($test_deilvary_id != false){
                    $builder->where('booking.test_delivary_id', $test_deilvary_id);		
                }else{
                    $builder->where('user_products.user_id', $this->session->get('user_id'));		
                }
		$query = $builder->get();
		$result = $query->getResultArray();
		return $result;		
	}


	public function get_tds_tests_detail($user_thirparty_id, $test_type){

		$builder = $this->db->table('tds_tests');
	    $builder->where('tds_tests.candidate_id', $user_thirparty_id);
	    $builder->where('tds_tests.test_type', $test_type);
	    $result = $builder->get()->getResult();
	    return $result;
	}

	// practice test	
	public function pract_test($id)
	{
		if($id) {
			$builder = $this->db->table('collegepre_practicetests');
			$builder->select('collegepre_practicetests.id, collegepre_practicetests.test_number, collegepre_practicetests.thirdparty_id, collegepre_practicetests.candidate_number, collegepre_practicetests.status,collegepre_practicetest_results.thirdparty_id as final_practice_thirdparty_id,collegepre_practicetest_results.status as practise_result_status');
			$builder->join('collegepre_practicetest_results', 'collegepre_practicetests.candidate_number = collegepre_practicetest_results.candidate_number','left');	
			$builder->where('collegepre_practicetests.thirdparty_id', $id);
			$query = $builder->get();
			$result = $query->getResultArray();
			return $result;
		}
		return false;
	}
	
	public function get_token_status_by_thirdpartyid($thirdparty_id = FALSE){
		if($thirdparty_id){
			$builder = $this->db->table('tokens');
			$builder->select('is_supervised,thirdparty_id');
			$builder->where('thirdparty_id', $thirdparty_id);
			$query = $builder->get();
			if($query->getNumRows() > 0){
				$result = $query->getRow();
				$is_supervised = $result->is_supervised;
				return $is_supervised;
			}else{
				return FALSE;
			}
		}
	}

	public function get_booking_date_time_details($thirdparty_id = FALSE){
		if($thirdparty_id != FALSE){
			$builder = $this->db->table('booking');
			$builder->select('events.start_date_time, events.end_date_time');
			$builder->join('events', 'events.id = booking.event_id');
			$builder->where('booking.test_delivary_id', $thirdparty_id);
			$builder->where('booking.status', 1);
			$query = $builder->get();
			return $query->getRowArray();
		}
	}

	public function get_step_level_by_stepcheck($user_app_id = FALSE){
		if($user_app_id){
			$builder = $this->db->table('step_level_by_stepcheck SLS');
			$builder->select('SLS.isPlacement');
			$builder->where('SLS.candidate_id', $user_app_id);
			$query = $builder->get();
			if($query->getNumRows() > 0){
				$result = $query->getRow();
				$is_Placement = $result->isPlacement;
				return $is_Placement;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function get_tokentype_by_thirdpartyid($thirdparty_id = FALSE){
		if($thirdparty_id){
			$builder = $this->db->table('tokens');
			$builder->select('tokens.type_of_token');
			$builder->where('tokens.thirdparty_id', $thirdparty_id);
			$query = $builder->get();
			if($query->getNumRows() > 0){
				$result = $query->getRow();
				$type_of_token = $result->type_of_token;
				return $type_of_token;
			}else{
				return FALSE;
			}
		}
	}

	public function view_higher_result($thirdparty_id = FALSE){
		if($thirdparty_id){
                    $delivery_type_option = $this->get_delivery_type_by_thirdparty_id($thirdparty_id);
                    $delivery_type = ($delivery_type_option) ? $delivery_type_option[0]->tds_option : "catstds"; 
                    if($delivery_type != NULL && $delivery_type == 'catstds'){
						$builder = $this->db->table('tds_results');
                        $builder->select('tds_results.candidate_id as thirdparty_id,tds_results.token,tds_results.course_type,tds_results.processed_data,booking.score_calculation_type_id');
                        $builder->join('booking', 'booking.test_delivary_id = tds_results.candidate_id');
                        $builder->where('tds_results.candidate_id', $thirdparty_id); 
                    }else{
						$builder = $this->db->table('collegepre_higher_results');
                        $builder->select('collegepre_higher_results.*, booking.score_calculation_type_id, booking.logit_values');
                        $builder->join('booking', 'booking.test_delivary_id = collegepre_higher_results.thirdparty_id');
                        $builder->where('collegepre_higher_results.thirdparty_id', $thirdparty_id); 
                    }
                    $query = $builder->get();
                    $result_value =  $query->getResult();
                    return $result_value;
		}else{
			return FALSE;
		}
	}

	// wp-1358 to check purchased date less than 200 days
	public function product_days($user_id = false){
		if($user_id != false){		
		    $checkdate = date('Y-m-d', strtotime("-200 days"));
			$builder = $this->db->table('user_products');
			$builder->select('user_products.product_id');
			$builder->where('user_products.thirdparty_id', $user_id);
			$builder->where("user_products.purchased_date < ",$checkdate);
			$query = $builder->get();
			if($query->getNumRows() > 0){
				return $query->getRowArray();
			}else{
				return false;
			}				
		}else{
			return false;
		}
	}
	

	function get_products_name($id) {
		if ($id) {
			$builder = $this->db->table('products');
			$builder->select('*');
			$builder->where('id', $id);
			$builder->where('id <', '13');		
			$query = $builder->get();
			return $query->getRowArray();
		}
	}

	function get_next_higher_level($product_id) {
		if($product_id == 12){
			$result =  array('product_id' => "13", 'level' => "max_higher", 'cefr_level' => "max_higher");
		}else{
			$level = $this->get_products_name($product_id + 1);
			$level['cefr_level'] = $this->get_products_name($product_id);

		  $result  = array('product_id' => $product_id + 1, 'level' => $level['name'], 'cefr_level' => $level['cefr_level']['level']);
		}
		return $result;
	}

	//saving payment details
	public function save_payment_details($details)
	{
		$builder = $this->db->table('payments');
		$builder->insert($details);
		return $this->db->insertID();	
	}
	//saving booking details of user
	public function save_booking_details($details)
	{
		$builder = $this->db->table('user_products');
		$builder->insert($details);
		return $this->db->insertID();		
	}

    //for attempt calc
    public function get_already_purchased_products($candidate_id = false, $product_id = false){
            
        if($candidate_id != false && $product_id != false){
				$builder = $this->db->table('user_products');
                $builder->select('count(user_products.id) as attempt_no, products.course_id,products.alp_id');
                $builder->join('products', 'user_products.product_id = products.id');
                $builder->where('user_products.user_id', $candidate_id);		
			    $builder->where('user_products.product_id', $product_id);
                $builder->groupBy('user_products.product_id');
			    $query = $builder->get();
                $result = $query->getResultArray();
                
            }elseif($candidate_id == false && $product_id != false){
				$builder = $this->db->table('products');
                $builder->select("*");
                $builder->where('id', $product_id);
                $query = $builder->get();
                $result = $query->getResultArray();
            }
            return $result;
    }

	//get practice test numnbers by course id number
	public function get_practice_numbers_by_course_id($product_id = false){
		
		if($product_id != false){
			$builder = $this->db->table('products');
			$builder->select();
			$builder->join('collegepre_forms', 'products.name = collegepre_forms.test_name');
			$builder->where('collegepre_forms.type', 'Practice test');
			$builder->where('products.id', $product_id);
			$query = $builder->get();
			$result = $query->getResult();
			return $result;
		}
		
    }
	
	/** WP-1202
	 * Function to get Practice Test details for CAT's TDS  
	 * @param boolean $product_id
	 * @return array
	 */
	public function get_tds_practice_numbers_by_course_id($product_id = false, $product_type = false){
		
		$builder = $this->db->table('products');
		$builder->select('*');
		$builder->join('tds_test_detail', 'products.id = tds_test_detail.test_product_id');
		$builder->join('tds_test_group', 'tds_test_detail.tds_group_id = tds_test_group.id');
		$builder->where('tds_test_group.test_type', 'Practice');
		$builder->where('tds_test_detail.status', 1);
		$builder->where('products.id', $product_id);
		$builder->orderBy("tds_test_detail.id", "desc");
		
		if($product_type === 'Core'){
			$tds_practice_query = $this->db->query('SELECT * FROM `tds_practice_test_settings` WHERE product_groups_id = "2" ');
			if ($tds_practice_query->getNumRows() > 0) {
			$result = $tds_practice_query->getRowObject();
				$practice_count = $result->no_of_practice_test;
				$builder->limit($practice_count);
			}else{
				$builder->limit(2);
			}
		}elseif ($product_type === 'Primary'){
			$tds_practice_query = $this->db->query('SELECT * FROM `tds_practice_test_settings` WHERE product_groups_id = "1" ');
			if ($tds_practice_query->getNumRows() > 0) {
				$result = $tds_practice_query->getRowObject();
				$practice_count = $result->no_of_practice_test;
				$builder->limit($practice_count);
			}else{
				$builder->limit(1);
			}
		}
	
		$query = $builder->get();//var_dump($product_type);echo $this->db->last_query();exit;
		$result = $query->getResult();
		return $result;
	}

	//get distributors
	public function get_distributors($dist_id = FALSE)
	{
		if($dist_id != FALSE ){
			$builder = $this->db->table('user_roles');
			$builder->select('users.firstname, users.lastname, users.distributor_name, users.address_line1, users.address_line2, users.city, users.postal_code, users.country, users.contact_number, users.currency');
			$builder->join('users', 'user_roles.users_id = users.id');
			$builder->join('roles', 'user_roles.roles_id = roles.id');
			$builder->where('roles.name', 'distributor');
			$builder->where('users.distributor_id', $dist_id);
			$query = $builder->get();
		}else{
			$builder = $this->db->table('user_roles');
			$builder->select('*');
			$builder->join('users', 'user_roles.users_id = users.id');
			$builder->join('roles', 'user_roles.roles_id = roles.id');
			$builder->where('roles.name', 'distributor');
			$query = $builder->get();
		}
		return $query->getResult();
	}

	public function save_catstds_practice_test($details){
		$builder = $this->db->table('tds_tests');
		$builder->insert($details);
		return $this->db->insertID();
	}

	/* WP-807 - hide opencourse based on school access to webversion for primary user 15-02-2018- start*/
	public function access_to_opencourse_primary($token_issued_organization_id){
	    if($token_issued_organization_id != FALSE){
	        $builder = $this->db->table('users');
	        $builder->select('users.access_to_webversion');
	        $builder->join('user_roles', 'users.id = user_roles.users_id');
            	$builder->join('roles', 'roles.id = user_roles.roles_id');
	        // $builder->from('users');
	        $builder->where('users.id', $token_issued_organization_id);
            	$builder->where('user_roles.roles_id', '4');
	        $query = $builder->get();
	        return $query->getResultArray();
	    }
	}
	/* WP-807 - ends*/

	// get all bookings of a user	
	public function get_bookings3($id = false)
	{
		$builder = $this->db->table('user_products');
		$builder->select('user_products.*, products.name, products.level,  products.alp_id, products.course_type as course_type, booking.id as booking_id, booking.event_id as booking_event_id, booking.test_delivary_id as booking_test_delivary_id, booking.datetime as booking_date, booking.status as booking_status, booking.score_calculation_type_id as booking_score_calculation,events.start_date_time,events.end_date_time,events.notes,venues.venue_name,venues.address_line1,venues.address_line2,venues.city');
		$builder->join('products', 'products.id = user_products.product_id');		
		$builder->join('booking', 'booking.test_delivary_id = user_products.thirdparty_id', 'left');
        $builder->join('events', 'events.id = booking.event_id', 'left');
        $builder->join('venues', 'venues.id = events.venue_id', 'left');
		$builder->orderBy("user_products.id", "desc");

		if($id != false)
		{
			$builder->where('user_products.product_id', $id);
			$builder->where('user_products.user_id', $this->session->get('user_id'));					
		}else{
			$builder->where('user_products.user_id', $this->session->get('user_id'));		
		}
		
		$query = $builder->get();
		$result = $query->getResultArray();
		return $result;		
	}

	//START DASH LOAD NEXT STEP SCREEN
	public function get_latest_product_details_dash($user_id = FALSE){
		if($user_id === FALSE){
			$user_id = $this->session->get('user_id');
		}
		$builder = $this->db->table('users');
		$builder->select('users.id, user_products.thirdparty_id, user_products.product_id,products.level');
		$builder->join('user_roles', 'user_roles.users_id = users.id');
		$builder->join('user_products', 'user_products.user_id = users.id', 'left');
		$builder->join('products', 'user_products.product_id = products.id', 'left');
		$builder->where('users.id', $user_id);
		$builder->orderBy('user_products.id',"desc");
		$builder->limit(1);
		$result = $builder->get()->getResult();
		return $result;
	}

	/**WP-1202
	 * GET CAT's TDS tes tdetails using Token 
	 * @param string $token
	 * @return array
	 */
	public function get_tds_tests_detail_by_token($token){
		$builder = $this->db->table('tds_tests');
	    $builder->where('tds_tests.token', $token);
	    $result = $builder->get()->getResult();
	    return $result;
	}
	/**WP-1202
	 * Update CAT's TDS test status when the practice test completed 
	 * @param array $data
	 * @param array $where
	 * @return boolean
	 */
	public function update_tds_tests_detail($data, $where){

		$builder = $this->db->table('tds_tests');
		$builder->where('tds_tests.token', $where);
	    $builder->update($data);

	    if( $this->db->affectedRows() > 0 ){
	        return TRUE;
	    }else{
	        return FALSE;
	    }
	}
	// To check benchmark entries already or not to avoid duplicate
	public function get_benchmark_details($token = FALSE, $user_id = FALSE){

		$builder = $this->db->table('benchmark_session');
		$builder->select('benchmark_session.user_id,benchmark_session.token');
		$builder->where('benchmark_session.user_id', $user_id);
		$builder->where('benchmark_session.token', $token);
		$query = $builder->get();
		$benchmark = $query->getResultArray();
		if($query->getNumRows() > 0){
			return TRUE;
		}else{
			return False;
		}
	}

}
?>