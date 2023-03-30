<?php 

namespace App\Models\Admin;//path
use CodeIgniter\Model;
class Tdsmodel extends Model {

	public $db;
    public function __construct()
	{
		helper('cms');
        helper('percentage_helper');
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
	}

    public function sw_weighting_current($type = FALSE, $table = FALSE){
        $builder = $this->db->table('tds_setting_'.$table.'_weight as SWCV');
        $builder->select('SWCV.version');
        $builder->where('type', $type);
        $builder->orderBy("id","ASC");
        $builder->limit(1);
        $query = $builder->get();
        return $query->getRowArray();
    }

        public function get_all_version_control($type = FALSE, $course = FALSE, $lookup = FALSE){
            $array = array('type' => $type, 'course' => $course, 'lookup' => $lookup);
            $builder = $this->db->table('tds_setting_version_control as SWVC');
            $builder->select('SWVC.form_id,SWVC.date,SWVC.version');
            $builder->where($array);
            $builder->orderBy("id","DESC");
            $query = $builder->get();
            return $query->getResult();
        }
       

        public function get_sw_ability_current($type = FALSE, $table = FALSE){
            $builder = $this->db->table('tds_setting_'.$table.'_swability as SWC');
            $builder->select('SWC.version');
            $builder->where('type', $type);
            $builder->orderBy("id","ASC");
            $builder->limit(1);
            $query =  $builder->get();
            return $query->getRowArray();
        }

        public function get_cefr_ability_current(){
            $builder = $this->db->table('tds_setting_cefrlevel as SCL');
            $builder->select('SCL.version');
            $builder->orderBy("id","ASC");
            $builder->limit(1);
            $query = $builder->get();
            return $query->getRowArray();
        }

        public function get_tds_benchmark_success_logs(){

            $query = $this->db->query('SELECT * FROM (
                        SELECT * FROM tds_logs where status = 1 ORDER BY id DESC LIMIT 5
                    ) sub
                    ORDER BY id DESC');
    
            return $query->getResult();
        }
        
        public function fetch_tds_benchmark_tasks($limit, $start) {

            $builder = $this->db->table('tds_tasks');
            $builder->select('tds_tasks.start,tds_tasks.end, tds_tasks.local_uri,tds_tasks.task_id');
            $builder->limit($limit, $start);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }

        public function tds_record_count() {
            return $this->db->table('tds_tasks')->countAll();
        }


    public function sent_count() {

        $builder = $this->db->table('email_log');
        $builder->where('status', 1);
        $result = $builder->countAllResults();
        return $result;
    }
    public function get_datewise_mailcount($limit, $start, $startdate=FALSE, $enddate=FALSE)
    {
        $builder = $this->db->table('email_log');
        $builder->select('*');
        $builder->where('DATE(datetime) >=', $startdate);
        $builder->where('DATE(datetime) <=', $enddate);
        $builder->where('status', 1);
        $builder->limit($limit, $start);
        $result = $builder->get()->getResultArray();
        return $result;
        
    }
    public function export_data_download($startdate, $enddate)
    {
        
        $builder = $this->db->table('email_log');
        $builder->select('*');
        $builder->where('DATE(datetime) >=', $startdate);
        $builder->where('DATE(datetime) <=', $enddate);
        $builder->where('status', 1);
        $result = $builder->get()->getResultArray();
        return $result;
        
    }

    public function get_tds_benchmark_failure_logs(){
        $query = $this->db->query('SELECT * FROM (
                    SELECT * FROM tds_logs where status = 0 ORDER BY id DESC LIMIT 5
                ) sub
                ORDER BY id DESC');
        return $query->getResult();
    }
    
    public function get_rl_ability_current($type = FALSE, $table = FALSE){

        $array = array('type' => $type,'test_type'=> "tds");
        $builder = $this->db->table('tds_setting_'.$table.'_rlability as rla');
        $builder->select('rla.form_code,rla.version');
        $builder->where($array);
        $builder->groupBy("form_code");
        $builder->orderBy("id","DESC");
        $builder->limit(1);
        $query = $builder->get();
        return $query->getRowArray();
    }
        
    public function get_institute_admin_user($user_id = FALSE){
        if($user_id != FALSE){
            $query = $this->db->query('SELECT * FROM institution_tier_users WHERE user_id ='.intval($user_id));
            $tier_admin_status = $query->getResult(); 
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

    
    public function get_all_sw_ability_by_version($type = FALSE, $table = FALSE, $version = FALSE, $lookup = FALSE){
        $array = array('type' => $type, 'version' => $version);
        $builder = $this->db->table('tds_setting_'.$table.'_swability_all as SWA');
        $builder->select('SWA.*');
        $builder->where($array);
        $builder->orderBy("id","ASC");
        $query = $builder->get();
        $result['lookup'] = $query->getResult();
        if($lookup != FALSE){
        $array1 = array('type' => $type, 'course' => $table, 'lookup' => $lookup, 'version' => $version); 
        $builder = $this->db->table('tds_setting_version_control as SWVC');
        $builder->select('SWVC.message');
        $builder->where($array1);            		
        $query1 = $builder->get();
        $result['reason'] = $query1->getRowArray();
        }
        return $result;
    }
    public function get_all_sw_weighting_by_version($type = FALSE, $table = FALSE, $version = FALSE ,$lookup = FALSE){
        $array = array('type' => $type, 'version' => $version);
        $builder = $this->db->table('tds_setting_'.$table.'_weight_all');
        $builder->select('tds_setting_'.$table.'_weight_all.*');
        $builder->where($array);
        $builder->orderBy("id","ASC");
        $query = $builder->get();
        $result['lookup'] = $query->getResult();
        if($lookup != FALSE){
        $array1 = array('type' => $type, 'course' => $table, 'lookup' => $lookup, 'version' => $version);  
        $builder = $this->db->table('tds_setting_version_control as SWVC');
        $builder->select('SWVC.message');
        $builder->where($array1);
        $query1 = $builder->get();
        $result['reason'] = $query1->getRowArray();
        }
        return $result;
    }

    public function get_all_cefr_ability($type = FALSE, $table = FALSE, $version = FALSE, $lookup = FALSE){
        $array = array('version' => $version);
        $builder = $this->db->table('tds_setting_'.$table.'_all as SCLA');
        $builder->select('SCLA.*');
        $builder->where($array);
        $builder->orderBy("id","ASC");
        $query =  $builder->get();
        $result['lookup'] = $query->getResult();
        if($lookup != FALSE){
        $array1 = array('type' => $type,'lookup' => $lookup, 'version' => $version);  
        $builder = $this->db->table('tds_setting_version_control as SWVC');
        $builder->select('SWVC.message');
        $builder->where($array1);            		
        $query1 = $builder->get();
        $result['reason'] = $query1->getRowArray();
        }
        return $result;
    }

    public function get_all_rl_ability_by_version($type = FALSE, $table = FALSE, $version = FALSE, $form_code = FALSE, $lookup = FALSE){
        $array = array('type' => $type, 'version' => $version,'form_code'=>$form_code);
        $builder = $this->db->table('tds_setting_'.$table.'_rlability_all as SWA');
        $builder->select('SWA.*');
        $builder->where($array);
        $builder->orderBy("id","ASC");
        $query = $builder->get();
        $result['lookup'] = $query->getResult();
        if($lookup != FALSE){
        $array1 = array('type' => $type, 'course' => $table, 'lookup' => $lookup, 'version' => $version, 'form_id' => $form_code);  
        $builder = $this->db->table('tds_setting_version_control as SWVC');
        $builder->select('SWVC.message');
        $builder->where($array1);            		
        $query1 = $builder->get();
        $result['reason'] = $query1->getRowArray();
        }
        return $result;
    }

    public function get_products_placement_setting($type_of_token = FALSE){
        if($type_of_token != FALSE){
            
            $builder = $this->db->table('products');
            if($type_of_token == "cats_core_or_higher"){
                $builder->where('products.course_type', 'Core');
                $builder->orWhere('products.course_type', 'Higher');
            }elseif($type_of_token == "cats_higher"){
                $builder->where('products.course_type', 'Higher');
            }else{
                $builder->where('products.course_type', 'Core');
            }
            
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $row_array[] = $row;
                }
                return $row_array;
            }else{
                return FALSE;
            }
        }
    }

    public function get_tds_placement_level_settings($status, $institution_id = FALSE){

        if($status != FALSE && $institution_id == FALSE){
            //it fetch all institution product with pagination
            $builder = $this->db->table('tds_placement_level_settings');
            $builder->orderBy("tds_placement_level_settings.id", "DESC");
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getResult();
            }else{
                return FALSE;
            }
        }
        elseif($status == FALSE && $institution_id != FALSE){
            //it fetch one institution with the help of institution id for edit
            $builder = $this->db->table('tds_placement_level_settings');
            $builder->select('tds_placement_level_settings.*, institution_tiers.id, institution_tiers.organization_name');
            $builder->join('institution_tiers', 'institution_tiers.id = tds_placement_level_settings.institution_id');
            $builder->where('tds_placement_level_settings.institution_id',$institution_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }elseif($status != FALSE && $institution_id != FALSE){
            //final result condition for placement test
            $builder = $this->db->table('tds_placement_level_settings');
            $builder->select('tds_placement_level_settings.product_level');
            $builder->join('institution_tiers', 'institution_tiers.id = tds_placement_level_settings.institution_id');
            $builder->where(array( 'tds_placement_level_settings.institution_id' => $institution_id, 'tds_placement_level_settings.status' => $status));
            
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }else{
                return FALSE;
            }
        }
    }

    public function fetch_institution_details($inst_id = false){
        if($inst_id != FALSE){
            $builder = $this->db->table('institution_tiers');
            $builder->where('id', $inst_id);
            $institute_row = $builder->get();
            return $institute_row->getRow();
        }else{
            $builder = $this->db->table('institution_tiers');
            $builder->select('institution_tiers.id, institution_tiers.organization_name' );
            $builder->orderBy("institution_tiers.organization_name", "ASC");
            $builder->where('institution_tiers.tierId', 3);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
            $institution = $query->getResult();
            return $institution;
            }
        }
    }
       
    /**WP-1301
     * Function to get product by product id placement test setting in Admin section
     * @param integer $id
     * @return array|boolean
     */
    public function get_products_by_id($id){
        if($id != FALSE){
            $builder = $this->db->table('products');
            $builder->where('id', $id);
            
            $query =  $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    public function delete_tds_placement($id) {
		if ($id != FALSE) {
             $builder = $this->db->table('tds_placement_level_settings');
			 $builder->where('institution_id', $id);
             $builder->delete();
			return TRUE;
		} else {
			return FALSE;
		}
	}

    public function get_tds_placement_session_test_details(){

        $builder = $this->db->table('tds_tests');
        $builder->select('tds_tests.*,from_unixtime(tds_tests.test_date, "%d-%m-%Y") as formatdate' );
        $builder->join('users', 'users.user_app_id = tds_tests.candidate_id');
        $builder->where('users.id', $this->session->get('user_id'));
        $builder->where('tds_tests.test_type',"placement");
        $builder->where('tds_tests.status',1);  
        $builder->where('tds_tests.result_status', 0);  
        $query = $builder->get();	    
        if($query->getNumRows() > 0){
            return $query->getResultArray();
        }else{
            return FALSE;
        }
    }

    // wp-1358 to check benchmark details within 200 days
    public function get_benchmark($user_id = false){
		if($user_id != false){

            $checkdate = date('Y-m-d', strtotime("-200 days"));
            $builder = $this->db->table('tds_benchmark_results AS TBR');
			$builder->select('TBR.id,TBR.raw_responses,TBR.raw_abilities,TBR.token,TBR.result_date,BS.result_status,TK.token,TBR.processed_data,TBR.lookup_versions');
			$builder->join('benchmark_session AS BS', 'TBR.token = BS.token');		
			$builder->join('tokens AS TK', 'TK.token = BS.token');			
			$builder->where('BS.result_status', 1);
			$builder->where('BS.user_id', $user_id);					
			$builder->where('TBR.result_date >', $checkdate);
			$builder->orderBy("TBR.result_date", "desc");		
			$builder->limit(1);			
			$query = $builder->get();
            $data =  $query->getRowArray();   
            if ($query->getNumRows() > 0) {
                $processed_data = json_decode($data['processed_data'],true);

                $lookup_versions = json_decode($data['lookup_versions'],true);               
                if(!empty($processed_data)){ 
                    $check_array=['speaking'=>'','writing'=>'','overall'=>''];
                    $difference=array_diff_key($processed_data,$check_array); 
                    if(!empty($difference)){
                        $score = $processed_data;                                          
                        $score['benchmark_results_id']=$data['id'];
                        return $score;
                    }else{
                        return false;
                    }
                }else{
                    $score['overall']='';
                    return $score;
                }             
            }else{
                return false;
            }
		}else{
			return false;
		}
	}
    function getPlacement($id = False){
        if($id){
            $builder = $this->db->table('tds_tests');
            $builder->select('candidate_id');
            $builder->where('tds_tests.candidate_id', $id);
            $builder->where('tds_tests.test_type', 'placement');
            $query = $builder->get();            
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }

        }
    }

    /**WP-1301
     * Function to get CAT's CEFR Score by level
     * @param string $level
     * @param string $source
     * @return array|boolean
     */
    public function get_cats_cefr_max_score_by_level($level = FALSE, $source = FALSE){
        if($level != FALSE && $source != FALSE){
            $builder = $this->db->table('tds_setting_cefrlevel');
            $builder->where('tds_setting_cefrlevel.cefr_level', $level);
            $builder->orderBy('tds_setting_cefrlevel.id', 'DESC');
            $builder->limit(1);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

        //New TDS Placement Test - WP-1301 Starts 
    //Query to fetch tds_test_detail table relted to tds placement test
    public function get_placement_delivery_detail($typeoftoken = false) {
        $token_types = ["catslevel","cats_core","cats_core_or_higher","cats_higher"];
        $test_type = in_array($typeoftoken, $token_types) ? "Adaptive" : FALSE;
        if($test_type != FALSE){
            $builder = $this->db->table('tds_placement_active_form AS TPAF');
            $builder->select('TTD.*');
            $builder->join('tds_test_detail AS TTD', 'TPAF.tds_test_detail_id = TTD.id');
            $builder->where('TPAF.test_type', $test_type);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            } else {
                return FALSE;
            }
        }
    }

    public function get_tds_tests_datas($placement_tds_token = false, $userAppId = false, $test_type = false, $testFormId = false, $testFormVersion = false) {
        if($placement_tds_token != FALSE && $userAppId != FALSE){
            $builder = $this->db->table('tds_tests AS TT');
            $builder->where('TT.token', $placement_tds_token);
            $builder->where('TT.candidate_id', $userAppId);
            $builder->where('TT.test_type', $test_type);
            $query = $builder->get();
            $data =  $query->getRowArray();
            if ($query->getNumRows() > 0) {
                if($data['test_formid'] != $testFormId || $data['test_formversion'] != $testFormVersion){
                    if($testFormId != false && $testFormVersion != false){
                        $updata_form_details = array('test_formid' => $testFormId,'test_formversion' => $testFormVersion);
                        $builder = $this->db->table('tds_tests');
                        $builder->update($updata_form_details, ['id' => $data['id']]);
                    }
                    $builder = $this->db->table('tds_tests');
                    $builder->select('*');
                    $builder->where('id', $data['id']);
                    $query = $builder->get();
                    return $update_query =  $query->getRowArray();
                }else{
                    return $data;
                }  
            } else {
                return FALSE;
            }
        }
    }

    public function save_tds_placement_test_details($insData) {

        $builder = $this->db->table('tds_tests');
        $builder ->insert($insData); // insert data into `institutiongroup` 
        return $this->db->insertID();
    }

    //get placement test of particular user
    public function check_placement_test(){
        $product_types = array('cats_core_or_higher','cats_core','cats_higher','catslevel');
        $builder = $this->db->table('user_products AS UP');
        $builder->select('UP.thirdparty_id');
        $builder->join('tokens AS TK', 'TK.user_id = UP.user_id');
        $builder->where('TK.user_id', $this->session->get('user_id'));
        $builder->where('TK.is_used', 1);
        $builder->whereIn('TK.type_of_token', $product_types);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getRowArray();
        } else {
            return FALSE;
        }
    }
    function benchmark_details($benchmarkid=false,$benchmark_placement=false,$thirdPartyId=false){
        if($benchmarkid){

            $builder = $this->db->table('tds_benchmark_results');
            $builder->select('*');
            $builder->where('id',$benchmarkid);
            $query = $builder->get();
            $data =  $query->getRowArray();           
            if ($query->getNumRows() > 0) {
                $processed_data = json_decode($data['processed_data'],true);                
                $lookup_versions = json_decode($data['lookup_versions'],true);
                if($lookup_versions != NULL){
                    if($lookup_versions['cefr_scale']){
                        $lookup_version =  array(
                        'cefr_scale' => $lookup_versions['cefr_scale']
                        );
                    }else{
                        $lookup_version =  array(
                        'cefr_scale' => "1"
                        ); 
                    }
                }else{
                    $lookup_version =  array(
                        'cefr_scale' => "1"
                    ); 
                }
                /* TDS-368 StepCheck -> Step - Assigned level added condition to raise one level up and calculates minimum score of the obtained level*/
                $cats_core_end_score = $this->get_cats_cefr_max_score_by_level("B1.3", "tds");                         
                $level= ($processed_data['overall']['score'] >  $cats_core_end_score->scale) ? "B2.1" : $processed_data['overall']['level']; 
                $next_step_level = @get_next_level_byStepcheck($level,$processed_data['overall']); 
                $score = $this->score_calculation_overall($processed_data,$next_step_level);  
                
                if($benchmark_placement == 1){
                    $token = "P_".$this->session->get('code');
                    $candidate_id = $this->session->get('user_app_id');
                }else{
                    $token = $this->session->get('code');
                    $candidate_id = $thirdPartyId;  
                }
                $benchmark_data = array(
                    'candidate_id' => $candidate_id,
                    'token' =>  $token,
                    'tds_stepcheck_results_id ' => $benchmarkid,
                    'processed_data' => json_encode($score), 
                    'isPlacement'=> $benchmark_placement                 
                );   

                $builder = $this->db->table('step_level_by_stepcheck');
                $builder->insert($benchmark_data);

                if($benchmark_placement == 1){          
                    $testtype=$this->get_test_type($data['testform_id']);
                    $insert_data= array(
                        'task_id' => $data['task_id'],
                        'testinstance_id' => $data['testinstance_id'],
                        'testform_id' => $data['testform_id'],
                        'testform_version' => $data['testform_version'],
                        'candidate_id' => $candidate_id,
                        'token' => $token,
                        'test_type' => $testtype->test_type,
                        'raw_responses' => $data['raw_responses'],
                        'raw_abilities' => $data['raw_abilities'],
                        'processed_data' => json_encode($score),
                        'lookup_versions' =>json_encode($lookup_version),
                        'ability' => $score['overall']['dwh_ability'],
                        'recommended_level' => $next_step_level,
                    );
                   $builder = $this->db->table('tds_placement_results');
                   $builder->insert($insert_data);
                }             
               
            }
            
        }

    }

    function score_calculation_overall($processed_data = false , $level = false){
        if($processed_data){
             /* TDS-368 StepCheck -> Step - Assigned level calculates minimum score of the obtained level*/
            $builder = $this->db->table('tds_setting_cefrlevel as AL');
            $builder->select('*');
            $builder->where('AL.cefr_level', $level);;
			$builder->orderBy("id", "ASC");		
			$builder->limit(1);
            $query = $builder->get();
            $data_result =  $query->getRowArray(); 
            $processed_data['overall']['score']  =  $data_result['scale'];
            $processed_data['overall']['level'] = $data_result['cefr_level'];
            $processed_data['overall']['dwh_scale']  =  $data_result['scale'];
            $processed_data['overall']['dwh_ability'] = $data_result['ability_estimate'];
            $processed_data['overall']['dwh_level'] = $data_result['cefr_level'];
            return $processed_data;
        
        }
       

    }


//result display setting
public function get_result_display_settings()
        {
            $builder = $this->db->table('result_display_settings');
			$builder->select('result_display_settings.*');
			$query = $builder->get();
			return $query->getRowArray();
        }
    /**WP-1301
     * Function to get Placement test form id belongs to Adaptive or Linear
     * @param integer $test_formid
     * @return array|boolean
     */
    public function get_test_type($test_formid = FALSE){
        if($test_formid != FALSE){
            $builder = $this->db->table('tds_test_detail');
            $builder->select('tds_test_detail.test_type');
            $builder->join('tds_test_group', 'tds_test_detail.tds_group_id = tds_test_group.id');
            $builder->where('tds_test_detail.test_formid', $test_formid);
            $builder->limit(1);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else {
                return FALSE;
            }
        }
    }

    public function get_date_search_mailcount($newDate1=FALSE, $newDate2=FALSE)
    {
        $builder = $this->db->table('email_log');
        $builder->select('*');
        $builder->where('DATE(datetime) >=', $newDate1);
        $builder->where('DATE(datetime) <=', $newDate2);
        $builder->where('status', 1);
        $query = $builder->get();
        $result= $query->getNumRows();
        return $result;
        
    }

    function get_email_sender_provider_by_category($category_id)
    {
        $builder = $this->db->table('email_sender_list ESL');
        $builder->select('ESP.key_name,ESP.key_value');
        $builder->join('email_service_provider ESP', 'ESL.sender_id = ESP.id');
        $builder->where('ESL.category_id', $category_id);
        $builder->orderBy('ESL.id', 'DESC');
        $builder->limit(1);
        $query = $builder->get();
        $result= $query->getResultArray();
        return $result; 
    }

    public function get_email_category(){
        $builder = $this->db->table('email_sender_categories');
        $builder->select('id,category_name');
        $builder->where('status', 1);
        $query = $builder->get();
        $result= $query->getResultArray();
        return $result; 
    }

    public function get_email_sender_types(){
        $builder = $this->db->table('email_service_provider');
        $builder->select('id,display_name');
        $builder->where('status', 1);
        $query = $builder->get();
        $result= $query->getResultArray();
        return $result; 
    }

    public function get_email_sender_list(){
        $builder = $this->db->table('email_sender_list ESL');
        $builder->select('EC.category_name,ES.display_name');
        $builder->join('email_sender_categories EC', 'ESL.category_id = EC.id');
        $builder->join('email_service_provider ES', 'ESL.sender_id = ES.id');
        $builder->where('ESL.status', 1);
        $builder->orderBy('ESL.id', 'ASC');
        $query = $builder->get();
        $result= $query->getResultArray();
        return $result; 
    }

    public function get_email_sender_list_by_category($category_id){
        $builder = $this->db->table('email_sender_list ESL');
        $builder->select('ESL.id,ESL.sender_id,ESL.category_id');
        $builder->where('ESL.category_id', $category_id);
        $builder->orderBy('ESL.id', 'DESC');
        $builder->limit(1);
        $query =$builder->get();
        $result= current($query->getResultArray());
        return $result; 
    }

    // TDS Result Processing - check queries
    public function check_result_status_count_stepcheck() {
        $builder = $this->db->table('benchmark_session');
        $builder->select('count(result_status) as result_status_count');
        $builder->where('benchmark_session.result_status', 0);
        $builder->where('benchmark_session.attempt_no <', 2);
        $query = $builder->get();
        $data = $query->getRow();
        
        if ($data->result_status_count > 0) {
            return $data->result_status_count;
        } else {
            return FALSE;
        }
    }

    public function check_result_status_count_course() {
        $builder = $this->db->table('tds_tests');
        $builder->select('count(result_status) as result_status_count');
        $builder->where('tds_tests.status', 1);
        $builder->where('tds_tests.result_status', 0);
        $builder->where('tds_tests.attempt_no <', 2);
        $query = $builder->get();
        $data = $query->getRow();
        
        if ($data->result_status_count > 0) {
            return $data->result_status_count;
        } else {
            return FALSE;
        }
    }

    public function update_attempt_no_stepcheck(){
        $builder = $this->db->table('benchmark_session');    
        $builder->where('result_status', 0);
        $builder->where('attempt_no <', 2);
        $builder->set('attempt_no', 'attempt_no+1', FALSE);
        $builder->update();
    }
    
    public function update_attempt_no_course(){
        $builder = $this->db->table('tds_tests'); 
        $builder->where('status', 1);
        $builder->where('result_status', 0);
        $builder->where('attempt_no <', 2);
        $builder->set('attempt_no', 'attempt_no+1', FALSE);
        $builder->update();
    }

    /**WP-1127
     * Function to insert logs for TDS result processing
     * @param array $ins_logs
     * @return integer
     */
    public function insert_tds_logs($ins_logs) {
        $builder = $this->db->table('tds_logs'); 
        $builder->insert($ins_logs);
        return $this->db->insertID();
    }

    public function check_tds_tasks($start = FALSE, $end = FALSE) {
        if ($start != FALSE && $end != FALSE) {
            $builder = $this->db->table('tds_tasks'); 
            $builder->select('tds_tasks.task_id');
            $builder->where('tds_tasks.start', $start);
            $builder->where('tds_tasks.end', $end);
            $builder->limit(1);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    public function is_stepcheck($test_formid = FALSE){
        if($test_formid != FALSE){
            $builder = $this->db->table('tds_test_detail'); 
            $builder->select('tds_test_detail.*, tds_test_group.test_type');
            $builder->join('tds_test_group', 'tds_test_detail.tds_group_id = tds_test_group.id');
            $builder->where('tds_test_detail.test_formid', $test_formid);
            $builder->where('tds_test_group.test_type', "StepCheck"); 
            $builder->limit(1);           
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else {
                return FALSE;
            }
        }
    }

    /** WP-1276
     * Function to get if the result form id belongs to Primary, Core, Higher or not
     * @param string $form_id
     * @return array|boolean
     */
    public function get_course_type($test_formid = FALSE){
        if($test_formid != FALSE){
            $builder = $this->db->table('tds_test_detail'); 
            $builder->select('tds_test_detail.*, products.id, products.name, products.level, products.course_type, products.course_id');
            $builder->join('tds_test_group', 'tds_test_detail.tds_group_id = tds_test_group.id');
            $builder->join('products', 'products.id = tds_test_detail.test_product_id');
            $builder->where('tds_test_detail.test_formid', $test_formid);
            $builder->limit(1);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else {
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function to check Core, Higher Placement Practice and Final test status (End Test) */
    public function check_tds_test_status($candidate_id, $token) {
        $builder = $this->db->table('tds_tests');
        $builder->select('candidate_id');
        $builder->where('candidate_id', $candidate_id);
        $builder->where('token', $token);
        $builder->where('status', 1);
        $builder->limit(1);
        $query = $builder->get();
        
        if ($query->getNumRows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**WP-1279 * Function to check result exist or not  */
    public function check_tds_results($tablename, $candidate_id, $token) {
        $builder = $this->db->table($tablename);
        $builder->select('candidate_id');
        $builder->where('candidate_id', $candidate_id);
        $builder->where('token', $token);
        $builder->limit(1);
        $query = $builder->get();
        
        if ($query->getNumRows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update_tds_results($tablename, $update_data, $where) {
        $builder = $this->db->table($tablename);
        $builder->update($update_data,$where);
    }

    /**WP-1199
     * Function to get minimum and maximum ablity estimate */
    public function get_minmax_ability_estimate_in_cats_cefr_level(){
        $builder = $this->db->table('tds_setting_cefrlevel');
        $builder->select("max(ability_estimate) as max_ability_estimate, min(ability_estimate) as min_ability_estimate");
        $query = $builder->get();
        
        if ($query->getNumRows() > 0) {
            return $query->getRow();
        }else{
            return FALSE;
        }
    }

    /**WP-1279 * Function to insert result from TDS      */
    public function insert_tds_results($tablename, $insert_data) {
        $builder = $this->db->table($tablename);
        $builder ->insert($insert_data); 
        return $this->db->insertID();
    }

    /** APP-5 and APP-6 * Function to get tokena and product details according to token */
    public function get_token_detail($token = false, $course_type = false) {
        $builder = $this->db->table('tokens');
        if($token != FALSE){
            $select = "";
            if($course_type != 'stepcheck' && $course_type != "placement"){
                $select = 'products.level, products.name as product_name, products.course_type, products.id as product_id';
                $builder->join('products', 'products.id = tokens.product_id');
            }
            $builder->select('tokens.*, ' . $select . ', institution_tier_users.institutionTierId, institution_tiers.tierId'); 
            $builder->join('school_orders', 'school_orders.id = tokens.school_order_id');
            $builder->join('institution_tier_users', 'institution_tier_users.user_id = school_orders.school_user_id');
            $builder->join('institution_tiers', 'institution_tiers.id = institution_tier_users.institutionTierId');
            $builder->where('tokens.token', $token);
            $query = $builder->get();
            $data =  $query->getRow();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }

    /** APP-5 and APP-6
     * Fuction to get TDS test date
     * @param boolean $token
     * @param boolean $candidate_id
     * @return array|boolean
     */
    public function get_tds_test_date($token = false, $candidate_id = false) {
        if($token != FALSE && $candidate_id != FALSE){

            $builder = $this->db->table('tds_tests');
            $builder->select('tds_tests.test_date');
            $builder->where('tds_tests.token', $token);
            $builder->where('tds_tests.candidate_id', $candidate_id);
            $query = $builder->get();
            $data =  $query->getRowObject();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }

    /** APP-5 and APP-6
     * Function to get weightage from question number
     * @param boolean $qnumber
     * @param boolean $type
     * @return integer|boolean
     */
    public function get_weight_by_qnumer($qnumber = false, $type = false, $table = false) {
        $q = ($type == 'writing') ? "W0" : "S0";

        $builder = $this->db->table('tds_setting_'.$table.'_weight');
        $builder->select('tds_setting_'.$table.'_weight.weight');
        // $this->db->from('tds_setting_'.$table.'_weight');
        $builder->where('tds_setting_'.$table.'_weight.qnumber', ($q.$qnumber));
        $builder->where('tds_setting_'.$table.'_weight.type', $type);
        $query = $builder->get();
        $data =  $query->getRowObject();
        
        if ($query->getNumRows() > 0) {
            $weight = $data->weight;
            return $weight;
        } else {
            return FALSE;
        }
    }

    /** APP-5 and App-6
     * Function to get test type by skill
     * @param boolean $skill
     * @return string|boolean
     */
    public function get_tds_test_type_by_skill($skill = false) {
        if($skill != FALSE){
            $builder = $this->db->table('testtypes');
            $builder->where('testtypes.title', ucfirst($skill));
            $query = $builder->get();
            $data =  $query->getRowObject();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }

    /**WP-1127 * Function to get CAT's CEFR level */
    public function get_cats_cefr_level($ablity = FALSE, $source = FALSE){
        if($ablity != FALSE && $source != FALSE){
            $builder = $this->db->table('tds_setting_cefrlevel');
            $builder->where('tds_setting_cefrlevel.ability_estimate', $ablity);
            $builder->orWhere('tds_setting_cefrlevel.ability_estimate >', $ablity);
            $builder->orderBy('tds_setting_cefrlevel.ability_estimate', 'ASC');
            $builder->limit(1);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getResult();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function to get CAT's CEFR Score by level */
    public function get_cats_cefr_score_by_level($level = FALSE, $source = FALSE){
        if($level != FALSE && $source != FALSE){
            $builder = $this->db->table('tds_setting_cefrlevel');
            $builder->where('tds_setting_cefrlevel.cefr_level', $level);
            $builder->orderBy('tds_setting_cefrlevel.id', 'ASC');
            $builder->limit(1);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function to get Eligible products by token type */
    public function get_eligible_products_by_token_type($type_of_token = FALSE){
        if($type_of_token != FALSE){
            $builder = $this->db->table('products');
            if($type_of_token == "cats_core_or_higher"){
                $builder->where('products.course_type', 'Core');
                $builder->orWhere('products.course_type', 'Higher');
            }elseif($type_of_token == "cats_higher"){
                $builder->where('products.course_type', 'Higher');
            }else{
                $builder->where('products.course_type', 'Core');
            }
            
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $row_array[] = $row->level;
                }
                return $row_array;
            }else{
                return FALSE;
            }
        }
    }

    public function lookup_all_version($processed_data = FALSE, $table = FALSE, $form_code = FALSE){
        unset($processed_data['overall']);
        $speaking_version_weight = $speaking_version_ability = $writing_version_weight = $writing_version_ability = $reading_ability = $listening_ability = $cefr_scale_version = $logit_values_final = 0;
        if (array_key_exists("speaking", $processed_data)){
            $speaking_version_weight = $this->sw_weighting_current("speaking",$table);
            $speaking_version_ability = $this->get_sw_ability_current("speaking",$table);
            $version_data_array["speaking_weight"] = ($speaking_version_weight) ? $speaking_version_weight['version'] : NULL;
            $version_data_array["speaking_ability"] = ($speaking_version_ability) ? $speaking_version_ability['version'] : NULL;
        }
        if(array_key_exists("writing", $processed_data)){
            $writing_version_weight = $this->sw_weighting_current("writing",$table);
            $writing_version_ability = $this->get_sw_ability_current("writing",$table);
            $version_data_array["writing_weight"] = ($writing_version_weight) ? $writing_version_weight['version'] : NULL;
            $version_data_array["writing_ability"] = ($writing_version_ability) ? $writing_version_ability['version'] : NULL;
        }
        if($table == "higher"){
            $reading_ability = $this->get_rl_ability_current_version("reading",$table,$form_code);
            $listening_ability = $this->get_rl_ability_current_version("listening",$table,$form_code);
            $version_data_array["reading_ability"] = ($reading_ability) ? $reading_ability['version'] : NULL;
            $version_data_array["listening_ability"] = ($listening_ability) ? $listening_ability['version'] : NULL;
        }
        $cefr_scale_version = $this->get_cefr_ability_current();
        $version_data_array["cefr_scale"] = ($cefr_scale_version) ? $cefr_scale_version['version'] : NULL;
        if($table == "core"){
            $current_version = $this->db->query('SELECT version FROM result_display_settings');
            $logit_values_final = $current_version->getRowArray();
            $version_data_array["logit_values"] = ($logit_values_final) ? $logit_values_final['version'] : NULL;
        }
        return $version_data_array;
    }

    public function get_rl_ability_current_version($type = FALSE, $table = FALSE, $form_code = FALSE){
        $array = array('type' => $type,'test_type'=> "tds",'form_code'=> "$form_code");
        $builder = $this->db->table('tds_setting_'.$table.'_rlability as rla');
        $builder->select('rla.version')->where($array)->groupBy("form_code")->orderBy("id","DESC")->limit(1);
        $query = $builder->get();
        return $query->getRowArray();
    }

    /**WP-1301 * Function to get product details by level and token type */
	public function get_product_details_by_recommended_level($level = FALSE){
        if($level != FALSE){
            $builder = $this->db->table('products');
            $builder->where('products.level', $level);
            $builder->groupStart();
            $builder->where('products.course_type', 'Core');
            $builder->orWhere('products.course_type', 'Higher');
            $builder->groupEnd();
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function to get distributer id and school order details using school order id */
    public function get_distributor_id($school_order_id){
        if($school_order_id != FALSE){
            $builder = $this->db->table('school_orders');
            $builder->where('school_orders.id', $school_order_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function Product price details by product id and distributor id */
    public function product_price_details($id = FALSE, $distributor_id = FALSE){
	    if($id != FALSE && $distributor_id != FALSE){
            $builder = $this->db->table('products');
			$builder->select('products.id, products.name, prices.distributor_id, prices.overall_fees, prices.distributor_fees, prices.cats_fees, users.distributor_name, users.currency');
			$builder->join('prices', 'prices.product_id = products.id');
			$builder->join('users', 'users.distributor_id = prices.distributor_id');
			$builder->where('products.id',$id);
			$builder->where('users.distributor_id', $distributor_id);
			$query = $builder->get();
			if ($query->getNumRows() > 0) {
			    return $query->getRow();
			}else{
			    return FALSE;
			}
		}
	}

    /**WP-1301 * Function to etch distributer user details in user table */
    public function get_default_distributor_details($distributor_id){
        if($distributor_id != FALSE){
            $builder = $this->db->table('users as U');
            $builder->select('U.city,U.country,U.email,U.distributor_id,U.distributor_name,U.distributor_all_price_set');
            $builder->where('U.distributor_id', $distributor_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1301 * Function to get user details */
    public function get_user_details($user_id){
        if($user_id != FALSE){
            $builder = $this->db->table('users');
            $builder->where('users.id', $user_id);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    
    /**WP-1301 * Function to get Learner type (Under13/Over13) by user id */
    function get_learner_type_by_userid($id = FALSE){
        if($id != FALSE){
            $builder = $this->db->table('instituition_learners');
            $builder->where('instituition_learners.user_id', $id);
            $query = $builder->get();
            if($query->getNumRows() > 0){
                return 'under13';
            }else{
                return 'over13';
            }
        }
    }

    /**WP-1127, WP-1325 Function to get weight setting for speaking and writing */
    public function get_weight_setting($type = FALSE, $source = FALSE, $table = FALSE){
        if($type != FALSE && $source != FALSE && $table != FALSE){
            $builder = $this->db->table('tds_setting_'.$table.'_weight');
            $builder->where('tds_setting_'.$table.'_weight.type', $type);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $row_array[] = $row->weight;
                }
                return $row_array;
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1127 * Function to get adjusted score ablity for speaking and writing */
    public function get_adjusted_score_ablity($adjusted_acore = FALSE, $type = FALSE , $table = FALSE){
        if($adjusted_acore != FALSE && $type != FALSE && $table != FALSE){
            $builder = $this->db->table('tds_setting_'.$table.'_swability');
            $builder->select('tds_setting_'.$table.'_swability.ability_estimate');
            $builder->where('tds_setting_'.$table.'_swability.adjusted_score', $adjusted_acore);
            $builder->where('tds_setting_'.$table.'_swability.type', $type);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                $res = $query->getRow();
                return (float)$res->ability_estimate;
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1156, WP-1276 * Function to get adjusted score ablity for CATs TDS and Collegepre Higher by using Form codes/ Test Formd id */
    public function get_adjusted_score_ablity_by_formcode($adjusted_acore = FALSE, $type = FALSE, $form_code = FALSE, $source = FALSE, $table = FALSE){
        if($type != FALSE && $form_code != FALSE && $table != FALSE){
            $builder = $this->db->table('tds_setting_'.$table.'_rlability');
            $builder->select('tds_setting_'.$table.'_rlability.ability_estimate');
            $builder->where('tds_setting_'.$table.'_rlability.score', $adjusted_acore);
            $builder->where('tds_setting_'.$table.'_rlability.type', $type);
            $builder->where('tds_setting_'.$table.'_rlability.form_code', $form_code);
            $builder->where('tds_setting_'.$table.'_rlability.test_type', $source);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                $res = $query->getRow();
                return (float)$res->ability_estimate;
            }else{
                return FALSE;
            }
        }
    }

    public function get_cats_cefr_level_by_score_zero(){
        $builder = $this->db->table('tds_setting_cefrlevel');
        $builder->where('tds_setting_cefrlevel.scale', 0);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getResult();
        }else{
            return FALSE;
        }
    }

    /**WP-1127 * Function to get CAT's CEFR level by score or scale */
    public function get_cats_cefr_level_by_score($score = FALSE, $source = FALSE){
        if($score != FALSE && $source != FALSE){
            $builder = $this->db->table('tds_setting_cefrlevel');
            $builder->where('tds_setting_cefrlevel.scale', $score);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getResult();
            }else{
                return FALSE;
            }
        }
    }

    /**WP-1319 * Function to get Minimum ability for the level */
    public function get_min_ability_estimate_by_level($level = FALSE){
        if($level != FALSE){
            $builder = $this->db->table('tds_setting_cefrlevel');
            $builder->selectMin('ability_estimate');
            $builder->where('tds_setting_cefrlevel.cefr_level', $level);
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                return $query->getRow();
            }else{
                return FALSE;
            }
        }
    }

    /** WP-1279 * Function to get score calculation type id by user Thirdparty id */
    public function get_score_calculation_type($thirdparty_id) {
        $builder = $this->db->table('booking');
        $builder->select('booking.score_calculation_type_id');
        $builder->where('booking.test_delivary_id', $thirdparty_id);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getRow();
        } else {
            return FALSE;
        }
    }

    /**WP-1301 * Function to check Stepcheck/Benchmark status (End Test)  */
    public function check_tds_stepcheck_test_status($candidate_id, $token) {
        $builder = $this->db->table('benchmark_session');
        $builder->select('user_app_id');
        $builder->where('user_app_id', $candidate_id);
        $builder->where('token', $token);
        $builder->limit(1);
        $query = $builder->get();
        
        if ($query->getNumRows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /** APP-5 and APP-6
     * Function to get Stepcheck test date
     * @param boolean $token
     * @param boolean $candidate_id
     * @return array|boolean
     */
    public function get_stepcheck_date($token = false, $candidate_id = false) {
        if($token != FALSE && $candidate_id != FALSE){

            $builder = $this->db->table('benchmark_session');
            $builder->select('benchmark_session.datetime');
            $builder->where('benchmark_session.token', $token);
            $builder->where('benchmark_session.user_app_id', $candidate_id);
            $query = $builder->get();
            $data =  $query->getRowObject();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }


    public function get_tds_test_type_by_formid($form_id = false, $form_version = false, $type = false) {
        if($form_id != FALSE && $form_version != FALSE){

            $builder = $this->db->table('tds_test_detail');
            $builder->where('tds_test_detail.test_formid', $form_id);
            /* $this->db->where('tds_test_detail.test_formversion', $form_version); */
            $query = $builder->get();
            $data =  $query->getRowObject();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }

    /** App-5 and APP-6
     * Function to get test type by product name
     * @param boolean $product_name
     * @param boolean $group_id
     * @return array|boolean
     */
    public function get_tds_test_type($product_id = false, $group_id = false) {
        if($product_id != FALSE && $group_id != FALSE){

            $builder = $this->db->table('testtypes');
            $builder->where('testtypes.productId', $product_id);
            $builder->where('testtypes.testGroupId', $group_id);
            $query = $builder->get();
            $data =  $query->getRowObject();
            
            if ($query->getNumRows() > 0) {
                return $data;
            } else {
                return FALSE;
            }
        }
    }

}