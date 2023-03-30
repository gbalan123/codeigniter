<?php

namespace App\Models\School;//path
use CodeIgniter\Model;

class Eventmodel extends Model {

	public $db;
	public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
		$this->request = \Config\Services::request();
	
	}

    public function get_eligible_productname($ids = FALSE) {

        if (is_array($ids)) {  $id = $ids; } else { $id[] = $ids; }
        
        $builder = $this->db->table('product_groups');
        $builder->select('product_groups.name');
        $builder->whereIn('product_groups.id', $id);
        $query = $builder->get();
        return $query->getResultArray();
    }

 //Sprint-38 to display products in add events
    public function get_institution_tier($id = FALSE) {
        $builder = $this->db->table('institution_tier_users');
        $builder->select('institution_tier_users.institutionTierId');
        $builder->where('institution_tier_users.user_id', $id);
        $query = $builder->get();
        if ($query) {
            $institution_id = $query->getRowArray();
            $institutionId = @implode(",", $institution_id);
        }
        return $institutionId;
    }

       //count
    public function record_count($show_events_condition = false, $current_utc_timestamp = false) {
        $institutionId = $this->get_institution_tier($this->session->get('user_id'));
        if ($institutionId) {
            $builder = $this->db->table('events');
            $builder->select('events.*,GROUP_CONCAT(products.name) as product_name,  venues.venue_name, event_institution.institution_user_id');
            $builder->groupBy('events.id');
            $builder->join('event_products', 'events.id = event_products.event_id  ');
            $builder->join('venues', 'venues.id = events.venue_id');
            $builder->join('products', 'products.id = event_products.product_id ');
            $builder->join('event_institution', 'events.id = event_institution.event_id ');
            $builder->join('institution_tier_users', 'event_institution.institution_user_id = institution_tier_users.user_id');
            $builder->where('institution_tier_users.institutionTierId', $institutionId);
            if ($show_events_condition == 0) {
                $builder->where('events.start_date_time >=', $current_utc_timestamp);  
            } 
            $builder->where('events.status', 1);
            $query = $builder->get();
            $count = count($query->getResultArray());
            return $count;
        }
    }

        //fetch using pagination
        public function fetch_events($limit = false, $start = false, $show_events_condition = false, $current_utc_timestamp = false) {
            $institutionId = $this->get_institution_tier($this->session->get('user_id'));
            if ($institutionId) {
                $builder = $this->db->table('events');
                $builder->select('events.*,GROUP_CONCAT(products.name) as product_name,venues.venue_name,venues.address_line1,venues.city,countries.countryName,venues.first_name,venues.email,venues.contact_no,venues.location_URL,venues.notes, event_institution.institution_user_id');
                $builder->groupBy('events.id');
                $builder->join('event_products', 'events.id = event_products.event_id  ');
                $builder->join('venues', 'venues.id = events.venue_id');
                $builder->join('countries', 'venues.country = countries.countryCode');
                $builder->join('products', 'products.id = event_products.product_id ');
                $builder->join('event_institution', 'events.id = event_institution.event_id ');
                $builder->join('institution_tier_users', 'event_institution.institution_user_id = institution_tier_users.user_id');
                $builder->where('institution_tier_users.institutionTierId', $institutionId);
                $builder->limit($limit, $start);
                if ($show_events_condition == 0) {
                    $builder->where('events.start_date_time >=', $current_utc_timestamp);  
                }
                $builder->where('events.status', 1);
                if ($this->request->getGet('events') == 'DESC') {
                    $builder->orderBy("events.start_date_time", "desc");
                } elseif ($this->request->getGet('events') == 'ASC') {
                    $builder->orderBy("events.start_date_time", "asc");
                } else {
                    $builder->orderBy("events.start_date_time", "desc");
                }
                $query = $builder->get();
                if ($query->getNumRows() > 0) {
                    foreach ($query->getResultArray() as $row) {
                        $rowArr[] = $row;
                    }
                    return $rowArr;
                }
            }
            return FALSE;
        }

         //get count of allocated learners in institution
         public function fetch_event_allocated_learners($event_id = FALSE) {
            if($event_id != FALSE){
                $builder = $this->db->table('booking');
                $builder->select('user_id, product_id, test_delivary_id');
                $builder->where('booking.event_id', $event_id);
                $builder->where('booking.status', 1);
                $query = $builder->get();
                $result = $query->getResultArray();
                $count = count($query->getResultArray());
                return $count;
            }
        }

        public function get_venues($id = FALSE) {
            $institutionId = $this->get_institution_tier($id);
            if ($institutionId) {
                $builder = $this->db->table('venues');
                $builder->orderBy("venues.id", "asc");
                $builder->select('venues.id, venues.venue_name,venue_institution.institution_user_id,institution_tier_users.institutionTierId');
                $builder->join('venue_institution', 'venues.id = venue_institution.venue_id');
                $builder->join('institution_tier_users', 'venue_institution.institution_user_id = institution_tier_users.user_id');
                $builder->where('institution_tier_users.institutionTierId', $institutionId);
                $venueTier = $builder->get();
            }
            return $venueTier->getResult();
        }

        //get venue details for hover
        public function fetch_event_by_id($event_id = FALSE) {
            if($event_id != FALSE){
                $builder = $this->db->table('events');
                $builder->select('id,start_date_time,end_date_time');
                $builder->where('events.id', $event_id);
                $query = $builder->get();
                $result = $query->getResultArray();
                $result = current($result);
                return $result;
            }
         }

        public function get_products_for_events() {
            $builder = $this->db->table('products');
            $builder->select('products.name,products.course_type');
            $builder->where('products.active', 1);
            $query = $builder->get();
            return $query->getResultArray();
        }

        public function get_products_for_events_version($product_group_edit = FALSE,$tds_option_edit = FALSE)  {
            $institutionId = $this->get_institution_tier($this->session->get('user_id'));
            $builder = $this->db->table('institution_eligible_products');
            $builder->select('institution_eligible_products.tds_option,products.id,products.course_type');
            $builder->join('products', 'institution_eligible_products.group_id = products.group_id');
            $builder->where('institution_eligible_products.institutionTierId', $institutionId);
            $builder->where('products.active', 1);
            $query_institution = $builder->get();
            $institution_tds_options = $query_institution->getResultArray();
            $type_primary = $type_core = $type_higher = "";
            $primary = $core = $higher = array();           
            foreach ($institution_tds_options as $institution_tds_option) {
               if ($institution_tds_option['course_type'] == "Primary") {
                   $product_ids_primary[] = $institution_tds_option['id'];
                   if ($product_group_edit == FALSE || $product_group_edit[0]['course_type'] == 'Primary') {
                       if ($tds_option_edit) {
                           $type_primary = $tds_option_edit;
                       } else {
                           $type_primary = $institution_tds_option['tds_option'];
                       }
                   }
               }
               if ($institution_tds_option['course_type'] == "Core") {
                   $product_ids_core[] = $institution_tds_option['id'];
                   if ($product_group_edit == FALSE || $product_group_edit[0]['course_type'] == 'Core') {
                       if ($tds_option_edit) {
                           $type_core = $tds_option_edit;
                       } else {
                           $type_core = $institution_tds_option['tds_option'];
                       }
                   }
               }
               if ($institution_tds_option['course_type'] == "Higher") {
                   $product_ids_higher[] = $institution_tds_option['id'];
                   if ($product_group_edit == FALSE || $product_group_edit[0]['course_type'] == 'Higher') {
                       if ($tds_option_edit) {
                           $type_higher = $tds_option_edit;
                       } else {
                           $type_higher = $institution_tds_option['tds_option'];
                       }
                   }
               }
           }
           if($type_primary != ""){
                $primary = $this->get_products_tds_allocated($product_ids_primary,$type_primary);
            }if($type_core != ""){
                $core = $this->get_products_tds_allocated($product_ids_core,$type_core);
            }if($type_higher != ""){
               $higher = $this->get_products_tds_allocated($product_ids_higher,$type_higher); 
            }
           $tds_products = array_merge($primary, $core, $higher);
           return $tds_products;
       }

        public function get_products_tds_allocated($product_ids = FALSE, $type = FALSE) {
            $builder = $this->db->table('tds_allocation');
            $builder->select('tds_allocation.product_id');
            $builder->join('tds_allocation_formcode', 'tds_allocation.id = tds_allocation_formcode.tds_allocation_id  ');
            $builder->where('tds_allocation_formcode.status', 1);
            $builder->where('tds_allocation.tds_option', $type);
            $builder->whereIn('tds_allocation.product_id', $product_ids);
            $builder->groupBy('tds_allocation.product_id');
            $query = $builder->get();
            return $query->getResult();
        }

        public function get_product_by_group() {
            $builder = $this->db->table('product_groups');
            $builder->select('*');
            $builder->whereIn('product_groups.id', array(1, 2, 3));
            $query = $builder->get();
            if ($query->getNumRows() > 0) {
                foreach ($query->getResultArray() as $row) {
                    $product_name[] = $row['name'];
                }
                foreach ($product_name as $key => $value) {
                    $check[] = $value;
                }
                $builder = $this->db->table('products');
                $builder->select('products.name,products.course_type,products.id');
                $builder->where('products.active', 1);
                $builder->whereIn('products.course_type', $check);
                $builder->orderBy('products.course_id', "ASC");
                $query2 = $builder->get();
                $result = $query2->getResult();
                $groupType = array();
                foreach ($result as $type) {
                    if ($type->course_type == 'Primary') {
                        $groupType['Primary'][$type->id] = $type->name;
                    } elseif ($type->course_type == 'Core') {
                        $groupType['Core'][$type->id] = $type->name;
                    } elseif ($type->course_type == 'Higher') {
                        $groupType['Higher'][$type->id] = $type->name;
                    }
                }
                return $groupType;
            }
        }

        //insert event
        public function insert_event($dataevents = FALSE) {
            if (isset($dataevents) && $dataevents != FALSE && !empty($dataevents)) {
                $builder = $this->db->table('events');
                $builder->insert($dataevents);
                return $this->db->insertID();
            }
        }

        //insert event products	
         public function insert_event_products($data = FALSE) {
            if (isset($data) && $data != FALSE && !empty($data)) {
                $builder = $this->db->table('event_products');
                $builder->insertBatch($data);
                return true;
            }
        }

         //insert event products	
        public function insert_event_institution($event_data = FALSE) {
            if (isset($event_data) && $event_data != FALSE && !empty($event_data)) {
                $builder = $this->db->table('event_institution');
                $builder->insert($event_data);
                return true;
            }
        }

        public function get_products_for_events_allocation($product = FALSE) {
             $builder = $this->db->table('products');
             $builder->select('products.id');
             $builder->join('tds_allocation', 'products.id = tds_allocation.product_id');
             $builder->where('products.course_type', $product);
             $builder->where('products.active', 1);
            $query =  $builder->get();
            $results = array_map('current',$query->getResultArray());
            return count($results);
        }

            //fetch event details
        public function event_details($id = FALSE) {
            $builder = $this->db->table('events');
            $builder->select('events.*,event_products.event_id as ep_event_id, event_products.product_id as ep_product_id, venues.venue_name, products.name as product_name');
            $builder->join('event_products', 'events.id = event_products.event_id');
            $builder->join('venues', 'venues.id = events.venue_id ', 'left');
            $builder->join('products', 'products.id = event_products.product_id ', 'left');
            $builder->where('events.id', $id);
            $query = $builder->get();

            if ($query->getNumRows() > 0) {
                foreach ($query->getResult() as $row) {
                    $rowArr[] = $row;
                }
                return $rowArr;
            }
            return FALSE;
        }

         //get product course type for edit
        public function get_product_group_name($product_id = FALSE) {
            //$this->db->order_by("progression", "asc");
            $builder = $this->db->table('products');
            $builder->select('course_type');
            $builder->whereIn('products.id', $product_id);
            $query = $builder->get();
            return $query->getResultArray();
        }

        //update event
         public function update_event($id = FALSE, $dataevents = FALSE) {
            if (isset($id) && $id != FALSE && !empty($id)) {
                $builder = $this->db->table('events');
                $builder->where('id', $id);
                $builder->update($dataevents);
                $builder = $this->db->table('event_products');
                $builder->where('event_id', $id);
                $builder->delete();
                return TRUE;
            }
        }
        //get products
        public function get_products() {
            $builder = $this->db->table('products');
            $builder->orderBy("progression", "asc");
            $builder->select('id, name');
            $query = $builder->get();
            return $query->getResultArray();
        }
 
}
