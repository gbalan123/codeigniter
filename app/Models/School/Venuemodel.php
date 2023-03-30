<?php 

namespace App\Models\School;//path

use CodeIgniter\Model;
class Venuemodel extends Model
{

    public function __construct()
	{
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
	
	}

	public function get_institution_tier($id) {
		$builder = $this->db->table('institution_tier_users');
	   $builder->select('institution_tier_users.institutionTierId');
	   $builder->where('institution_tier_users.user_id', $id);
	    $query =$builder->get();
	    $institutionId = @implode(",",$query->getRowArray());
	    return $institutionId;
	}

	//fetch venue useing pagination
	public function fetch_venue($limit, $start) {
		$institutionId = $this->get_institution_tier($this->session->get('user_id'));
		if($institutionId){
			// $this->db->limit($limit, $start);
			$builder = $this->db->table('venues');
			$builder->select('venues.*');
			$builder->join('venue_institution', 'venues.id = venue_institution.venue_id  ');
			$builder->join('institution_tier_users', 'venue_institution.institution_user_id = institution_tier_users.user_id');
			$builder->where('institution_tier_users.institutionTierId', $institutionId);
			$builder->where('venues.status',1);
			$builder->orderBy("id", "desc");
			$builder->limit($limit, $start);
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

	//get single venue
	public function get_venue($id = FALSE, $slug = FALSE)
	{
		$veninstitutionId = $this->get_institution_tier($this->session->get('user_id'));
		if($id != FALSE &&  $slug == FALSE){
			  $builder = $this->db->table('venues');
			  $query = $builder->getWhere(array('id'=>$id));
		}elseif($id != FALSE && $slug != FALSE){
			  $builder = $this->db->table('venues');
			  $query = $builder->getWhere(array('id' => $id, 'venue_slug' => $slug ));
		}elseif($id == FALSE && $slug != FALSE){
			$builder = $this->db->table('venues');
			$builder->select('venue_slug');
			$builder->join('venue_institution','venues.id = venue_institution.venue_id');
			$builder->join('institution_tier_users','venue_institution.institution_user_id = institution_tier_users.user_id');
			$builder->where('venues.venue_slug',$slug);
			$builder->where('institution_tier_users.institutionTierId',$veninstitutionId);
			$query = $builder->get();
		}else{
			$query = $builder->get('venues');
		}
		return $query->getResult();
	}

	public function fetch_insitution_country(){
        $institutionId = $this->get_institution_tier($this->session->get('user_id'));
		$builder = $this->db->table('institution_tiers');
      	$builder->select('countries.countryName,countries.countryCode');
        $builder->join('countries', 'institution_tiers.country = countries.countryCode');
        $builder->where('institution_tiers.id', $institutionId);
        $query =$builder->get();
        return $query->getResult();
    }

	public function update_venue($id, $datavenues)
	{
		if($id != '' && $id != '0'){
			$builder = $this->db->table('venues');
			$builder->where('id',  $id);
			$builder->update($datavenues);
			if($this->db->affectedRows() > 0)
			{
				return TRUE;
			}else{
				return FALSE;
			}
		}	
	}

	public function insert_venue($datavenues)
	{	
		$builder = $this->db->table('venues');
        $builder->insert($datavenues);
        return $this->db->insertID();
	}

	public function insert_venue_institution($dataInstitution)
	{
		$builder = $this->db->table('venue_institution');
        $builder->insert($dataInstitution);
        return $this->db->insertID();
	}

	//venue count
	public function record_count() {
	    $institutionId = $this->get_institution_tier($this->session->get('user_id'));
	    if($institutionId){
			$builder = $this->db->table('venues');
	        $builder->select('venues.*');
	        $builder->join('venue_institution', 'venues.id = venue_institution.venue_id  ');
	        //$builder->join('venue_institution.institution_user_id', $this->session->userdata('user_id'));
	        $builder->join('institution_tier_users', 'venue_institution.institution_user_id = institution_tier_users.user_id');
	        $builder->where('institution_tier_users.institutionTierId', $institutionId);
	        $builder->where('venues.status',1);
	        //   $builder->order_by("id", "desc");
	    }
	    $count = $builder->countAllResults();
	   // print_r($count);
	    //echo $builder->last_query();die;
	    return $count;
	}  


	//for alert message of delete in venue
	function check_exists_venue_in_event_alert($venue_id = FALSE, $current_utc_timestamp = FALSE){
		if($venue_id != FALSE){ 
			$query = $this->db->query('SELECT * FROM events WHERE venue_id ="'.$venue_id.'"');
			$count = count($query->getResult());
			if($count > 0){
				$i = 0;
				foreach($query->getResult() as $results){
					$event_end_time = $results->end_date_time;
					if($event_end_time > $current_utc_timestamp){
						$i++;
					}
				}
				if($i > 0){
					return 3; //Venue with Future Events
				}else{
					return 2; //"Venue with past event with learners"
				}
			}else{
				return 1; //"Venue with no event"
			}
		}else{
			return FALSE;
		}
	}

}
