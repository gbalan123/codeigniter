<?php 

namespace App\Models\Admin;//path
use CodeIgniter\Model;

class Productmodel extends Model
{

	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
		$this->session = session();
	}

	//count
	public function record_count() {
		return $this->db->table('products')->countAll();
	}
	//fetch useing pagination
	public function fetch_product() {

		$builder = $this->db->table('products');
		$builder->select('products.*');
		$query = $builder->get();
		if ($query->getNumRows() > 0) {
			foreach ($query->getResult() as $row) {
				$rowArr[] = $row;
			}
			return $rowArr;
		}
	}
	
	//get single product
	public function get_product($id = FALSE,  $audience = FALSE, $level = FALSE)
	{
		
			if($id != FALSE){
					$builder = $this->db->table('products');
			        $builder->where('id',$id);
					$query = $builder->get();

				}else if($audience != FALSE && $level == FALSE){
					$builder = $this->db->table('products');
					$builder->where('audience',$audience);
					$query = $builder->get();

                }else if($audience != FALSE && $level != FALSE){
					$builder = $this->db->table('products');
                    $builder->where('audience', $audience);
					$builder->where('level', $level);
					$query = $builder->get();

                }else{
					$builder = $this->db->table('products');
					$query = $builder->get();
				}
		return $query->getResult();
	}


	public function fetch_product_for_version_allocation() {

		$builder = $this->db->table('products');
	    $builder->select('products.*');
	    $builder->where('products.active', 1);
		$result = $builder->get()->getResult();
		return $result;
	}
	
    public function placement_settings($data_placements = FALSE) {

		if( $data_placements != FALSE){
			$builder = $this->db->table('placement_settings');
			$builder->where('id',3); 
			$builder->update($data_placements);
			return TRUE;
		}
	}


	public function new_version($table_name,$type) {
		$builder = $this->db->table($table_name);
		$builder->where('type',$type); 
		$query = $builder->get()->getRowArray();
		return $query;	
	}
	public function queryMain($table_name,$type) {
		$builder = $this->db->table($table_name);
		$builder->where('type',$type); 
		$query = $builder->get()->getRowArray();
		return $query;
	}

	public function queryMaindelete($table_name,$type) {

		if( $type != FALSE){
			$builder = $this->db->table($table_name);
			$builder->where('type',$type); 
			$builder->delete();
			return TRUE;
		}
	}

	public function table_nameinsert($table,$data){

        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	public function table_name_allinsert($table,$data){
        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	public function table_nameinsert2($table,$data){
			$builder = $this->db->table($table);
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return $insert_id;
	}

	public function table_name_allinsert2($table,$data){

        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	public function version_detailsinsert($data){

        $builder = $this->db->table('tds_setting_version_control');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	//speaking_weighting Function 
	public function new_versionspeaking_weighting($table_name,$type) {
		$builder = $this->db->table($table_name);
		$builder->where('type',$type); 
		$query = $builder->get()->getRowArray();
		return $query;	
	}
	public function queryMainspeakingweighting($table_name,$type) {
		$builder = $this->db->table($table_name);
		$builder->where('type',$type); 
		$query = $builder->get()->getRowArray();
		return $query;
	}
	public function queryMaindeletequeryMainspeakingweighting($table_name,$type) {

		if( $type != FALSE){
			$builder = $this->db->table($table_name);
			$builder->where('type',$type); 
			$builder->delete();
			return TRUE;
		}
	}
	public function table_nameinsertspeakingweighting($table,$data){

        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	public function table_name_allinsertspeakingweighting($table,$data){
        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	public function table_nameinsertspeakingweighting2($table,$data){
		$builder = $this->db->table($table);
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
    }

	public function table_name_allinsertspeakingweighting2($table,$data){
        $builder = $this->db->table($table);
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}
	public function version_detailsinsertspeakingweighting($data){
        $builder = $this->db->table('tds_setting_version_control');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}

	//post_cefr_ability Function 
	public function new_versionpost_cefr_ability() {
		$builder = $this->db->table('tds_setting_cefrlevel');
		$builder->select('tds_setting_cefrlevel.*');
		$query = $builder->get()->getRowArray();
		return $query;	
	}

	public function queryMainspost_cefr_ability() {
			$builder = $this->db->table('tds_setting_cefrlevel');
			$builder->truncate();
			return TRUE;
	}
	public function table_nameinsertpost_cefr_ability($data){
        $builder = $this->db->table('tds_setting_cefrlevel');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}
	public function table_name_allinsertpost_cefr_ability($data){
        $builder = $this->db->table('tds_setting_cefrlevel_all');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}
	public function version_detailsinsertpost_cefr_ability($data){
        $builder = $this->db->table('tds_setting_version_control');
        $builder->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
	}
	//reading_ability Function 
	public function test_formidtds_test_detail($id) {

		$builder = $this->db->query('SELECT tds_test_detail.test_formid FROM tds_test_detail WHERE id = "' . $id . '"');
		$result = $builder->getRowArray();
		return $result;
	}

	
		public function new_versionreading_ability($type,$test_formid_string) {
			$builder = $this->db->table('tds_setting_higher_rlability');
			$builder->where('type',$type); 
			$builder->where('form_code',$test_formid_string); 
			$query = $builder->get()->getRowArray();
			return $query;	
		}

		public function queryMainreading_ability($type,$test_formid_string,$test_type) {
			$builder = $this->db->table('tds_setting_higher_rlability');
			$builder->where('type',$type); 
			$builder->where('form_code',$test_formid_string); 
			$builder->where('test_type',$test_type); 
			$query = $builder->get()->getNumRows();
			return $query;	
		}

		public function queryMaindeletereading_ability($type,$test_formid_string,$test_type) {

				$builder = $this->db->table('tds_setting_higher_rlability');
				$builder->where('type',$type); 
				$builder->where('form_code',$test_formid_string); 
				$builder->where('test_type',$test_type); 
				$builder->delete();
				return TRUE;
		}
		public function table_nameinsertreading_ability($data){
			$builder = $this->db->table('tds_setting_higher_rlability');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return $insert_id;
		}
		public function table_name_allinsertreading_ability($data){
			$builder = $this->db->table('tds_setting_higher_rlability_all');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return $insert_id;
		}
		public function version_detailsinsertreading_ability($data){
			$builder = $this->db->table('tds_setting_version_control');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return $insert_id;
		}

		public function productupdate($id,$csvdata) {

			if( $id != FALSE){
				$builder = $this->db->table('products');
				$builder->where('id',$id);
				$builder->update($csvdata);
				return TRUE;
			}
		}
		public function productinsert($data){
			$builder = $this->db->table('products');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return TRUE;
		}

		public function uploadquestionbank($data){
			$builder = $this->db->table('question_bank_details');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return TRUE;
		}

		public function inscreendata($data){

			$builder = $this->db->table('linear_bank');
			$builder->insert($data);
			$insert_id = $this->db->insertID();
			return TRUE;
		}

		public function get_allocation_details($product_id = FALSE, $source = FALSE){
			$builder = $this->db->table('tds_allocation');
			$builder->select('tds_allocation.*');
			$builder->where('tds_allocation.product_id', $product_id);
			$builder->where('tds_allocation.tds_option', $source);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				return $query->getResult();
			}else {
				return FALSE;
			}
		}

		public function fetch_product_details($product_id = FALSE){
			$builder = $this->db->table('products');
			$builder->select('products.*');
			$builder->where('products.id', $product_id);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				return $query->getResult();
			}else {
				return FALSE;
			}
		}

		public function get_active_form_codes($product_id = FALSE, $source = FALSE){
			$builder = $this->db->table('tds_allocation_formcode');
			$builder->select('tds_allocation_formcode.*');
			$builder->join('tds_allocation', 'tds_allocation.id = tds_allocation_formcode.tds_allocation_id');
			$builder->where('tds_allocation.product_id', $product_id);
			$builder->where('tds_allocation_formcode.status', 1);
			$builder->where('tds_allocation.tds_option', $source);
			$builder->orderBy("tds_allocation_formcode.form_code_order", "ASC");
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				$query_result = $query->getResult();
				foreach ($query_result as $query_data){
					$active_form_codes['active_form_codes'][] = $query_data;
				}
				if($active_form_codes != ''){
					return $active_form_codes['active_form_codes'];
				}else{
					return FALSE;
				}
			}else {
				return FALSE;
			}
		}

		public function fetch_formcodes_cats($product_id) {
			$form_codes =  array();
			$allocation_details = $this->get_allocation_details($product_id, 'catstds');
			$product_details = $this->fetch_product_details($product_id);
			if($product_details != FALSE){
				$product_group = $product_details[0]->course_type;
			}
			
			$form_codes['number_of_exposure'] = ($allocation_details != FALSE) ? $allocation_details[0]->number_of_exposure : '';
			$form_codes['allocation_rule'] = ($allocation_details != FALSE) ? $allocation_details[0]->allocation_rule : '';
			
			$active_form_codes = $this->get_active_form_codes($product_id, 'catstds');
			
			if($active_form_codes != FALSE){
				$form_codes['active_form_codes_cats'] = $active_form_codes;
				foreach($active_form_codes as $active_form_code){
					$active_form_codes_array[] =  $active_form_code->form_code;
				}
				if($product_group === 'Higher'){
					$product_id = 10;
				}
				$builder = $this->db->table('tds_test_detail');
				$builder->select('tds_test_detail.*');
				$builder->join('products', 'products.id = tds_test_detail.test_product_id');
				$builder->join('tds_test_group', 'tds_test_group.id = tds_test_detail.tds_group_id');
				$builder->where('tds_test_detail.status', 1);
				$builder->where('products.id', $product_id);
				$builder->where('tds_test_group.test_type', 'Final');
				$builder->whereNotIn('tds_test_detail.test_formid', $active_form_codes_array);
				$query = $builder->get();
				
				if ($query->getNumRows() > 0) {
					foreach ($query->getResult() as $row) {
						$form_codes['available_form_codes_cats'][] = $row;
					}
				}
			}else{
				if($product_group === 'Higher'){
					$product_id = 10;
				}
				$builder = $this->db->table('tds_test_detail');
				$builder->select('tds_test_detail.*');
				$builder->join('products', 'products.id = tds_test_detail.test_product_id');
				$builder->join('tds_test_group', 'tds_test_group.id = tds_test_detail.tds_group_id');
				$builder->where('products.id', $product_id);
				$builder->where('tds_test_detail.status', 1);
				$builder->where('tds_test_group.test_type', 'Final');
				
				$query = $builder->get();
				
				if ($query->getNumRows() > 0) {
					foreach ($query->getResult() as $row) {
						$form_codes['available_form_codes_cats'][] = $row;
					}
				}
			}
			
			if($form_codes != ''){
				return $form_codes;
			}else{
				return FALSE;
			}
		}

		public function get_exposure_count($form_code = FALSE,  $product_id = FALSE){
			$form_code_exposure = FALSE;
			if($form_code != FALSE && $product_id != FALSE){
				$builder = $this->db->table('tds_allocation_formcode');
				$builder->select('tds_allocation_formcode.*');
				$builder->join('tds_allocation', 'tds_allocation.id = tds_allocation_formcode.tds_allocation_id');
				$builder->where('tds_allocation.product_id', $product_id);
				$builder->where('tds_allocation_formcode.form_code', $form_code);
				$query = $builder->get();
				if ($query->getNumRows() > 0) {
					$query_result = $query->getResult();
					$form_code_exposure =  $query_result[0]->previous_exposure + $query_result[0]->current_exposure;
				}else{
					$product_details = $this->fetch_product_details($product_id);

					$builder->select('count(*) as form_code_exposure');
					if($product_details[0]->course_type === 'Higher'){
						$builder = $this->db->table('collegepre_higher_results');
						$builder->join('collegepre_forms', 'collegepre_forms.form_id = collegepre_higher_results.form_id');
					}else{
						$builder = $this->db->table('collegepre_results');
						$builder->join('collegepre_forms', 'collegepre_forms.form_id = collegepre_results.form_id');
					}

					$builder->where('collegepre_forms.form_code', $form_code);
					$query = $builder->get();
					
					if ($query->getNumRows() > 0) {
						$query_result = $query->getResult();
						$form_code_exposure =  $query_result[0]->form_code_exposure;
					}
				}
			}
			return $form_code_exposure;
		}


		public function check_tds_allocation_product_exist($id = FALSE, $source = FALSE){
			if($id != FALSE){
				$builder = $this->db->table('tds_allocation');
				$builder->select('tds_allocation.product_id');
				$builder->where('tds_allocation.product_id', $id);
				$builder->where('tds_allocation.tds_option', $source);
				$query = $builder->get();
				if ($query->getNumRows() > 0) {
					return TRUE;
				}else {
					return FALSE;
				}
			}
		}

		public function get_tds_active_formcodes($product_id = FALSE, $source = FALSE){
			$builder = $this->db->table('tds_allocation_formcode');
			$builder->select('tds_allocation_formcode.*');
			$builder->join('tds_allocation', 'tds_allocation.id = tds_allocation_formcode.tds_allocation_id');
			$builder->where('tds_allocation.product_id', $product_id);
			$builder->where('tds_allocation.tds_option', $source);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				foreach ($query->getResult() as $row){
					$existing_form_codes[] = $row->form_code;
				}
				return $existing_form_codes;
			}else {
				return FALSE;
			}
		}

		public function get_tds_active_formcode_details($form_code = FALSE, $tds_allocation_id = FALSE){
			$builder = $this->db->table('tds_allocation_formcode');
			$builder->select('tds_allocation_formcode.*');
			$builder->where('tds_allocation_formcode.form_code', $form_code);
			$builder->where('tds_allocation_formcode.tds_allocation_id', $tds_allocation_id);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
				return $query->getResult();
			}else {
				return FALSE;
			}
		}

		public function update_tds_allocation($tds_allocation_data, $source){
			if($tds_allocation_data != ''){
				$builder = $this->db->table('tds_allocation');
				$builder->update($tds_allocation_data, ['product_id' => $tds_allocation_data['product_id'], 'tds_option' => $source]);
				return TRUE;
			}
		}

		public function update_tds_allocation_form_code($tds_allocation_formcode_data){
			if($tds_allocation_formcode_data != ''){

				$builder = $this->db->table('tds_allocation_formcode');
				$builder->update($tds_allocation_formcode_data, ['form_code' => $tds_allocation_formcode_data['form_code'], 'tds_allocation_id' => $tds_allocation_formcode_data['tds_allocation_id']]);
				return TRUE;
			}
		}


				//getting product list for book and pay
	public function product_list($limit = FALSE, $start = FALSE) {

		$builder = $this->db->table('products');
		$builder->limit($limit, $start);
		$builder->select('id, name');
        $builder->where('products.audience','General');
		$builder->orderBy("products.progression", "asc");
		$query = $builder->get();

		if ($query->getNumRows() > 0) {
			foreach ($query->getResult() as $row) {
				$rowArr[] = $row;
			}
			return $rowArr;
		}
		return FALSE;
	}
	//get single product details
	public function product_details($id )
	{
		if($id != FALSE){
			$builder = $this->db->table('products');
			$builder->select('products.id, products.name, prices.distributor_id, prices.overall_fees, prices.distributor_fees, prices.cats_fees, users.distributor_name, users.currency');
			$builder->join('prices', 'prices.product_id = products.id');
			$builder->join('users', 'users.distributor_id = prices.distributor_id');
			$builder->where('products.id',$id);
			$builder->where('users.distributor_id',$this->session->get('distributor_id'));
			$query = $builder->get();
		}else{
			return FALSE;
		}		
		return $query->getResultArray();
	}

	public function get_product_details($id = false){
		if($id != FALSE){
			$builder = $this->db->table('products');
			$builder->select('products.id, products.name');
			$builder->where('products.id',$id);
			$query = $builder->get();
			return $query->getResultArray();
		}else{
			return FALSE;
		}		
	}

}