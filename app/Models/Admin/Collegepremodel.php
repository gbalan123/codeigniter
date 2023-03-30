<?php 

namespace App\Models\Admin;//path
use CodeIgniter\Model;
class Collegepremodel extends Model
{
	
	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
	}

    public function get_collegepre_settings()
    {
        $builder = $this->db->table('collegepre_settings');
        $builder->select('*');
        $query = $builder->get();
        return $query->getRowArray();
    }

    public function get_cron_mailto()
    {       
        $builder = $this->db->table('cron_mails');
        $builder->select('cron_mailto,status');
        $query = $builder->get();
        return $query->getRowArray();
    }
    
            
    public function get_collegepre_success_logs()
    {
           $query =  $this->db->query('SELECT * FROM (
                SELECT * FROM collegepre_logs where status = 1 ORDER BY id DESC LIMIT 5
            ) sub
          ORDER BY id DESC');
          return $query->getResult();
    }
    public function get_collegepre_failure_logs()
    {
           $query =  $this->db->query('SELECT * FROM (
                SELECT * FROM collegepre_logs where status = 0 ORDER BY id DESC LIMIT 5
            ) sub
            ORDER BY id DESC');
         return $query->getResult();
    }
	
    public function get_higher_formcodes($product_group = FALSE){
        if($product_group != FALSE){
            if($product_group === 'collegepre'){
                $builder = $this->db->table('collegepre_forms');
                $builder->select('collegepre_forms.form_code,collegepre_forms.id');
                $builder->where('test_name', "Higher");
            }elseif ($product_group === 'tds'){
                $builder = $this->db->table('tds_test_detail');
                $builder->select('tds_test_detail.test_formid, tds_test_detail.id');
                $builder->where('test_product_id', 10);
                $builder->where('status', 1);
            }
            $query = $builder->get();
            return $query->getResult();
        }
    }

    public function get_collegepre_higher_success_logs(){
        $query = $this->db->query('SELECT * FROM (
                    SELECT * FROM collegepre_higher_logs where status = 1 ORDER BY id DESC LIMIT 5
                ) sub
                ORDER BY id DESC');
        return $query->getResult();
    }
    public function get_collegepre_higher_failure_logs(){
        $query = $this->db->query('SELECT * FROM (
                    SELECT * FROM collegepre_higher_logs where status = 0 ORDER BY id DESC LIMIT 5
                ) sub
                ORDER BY id DESC');
        return $query->getResult();
    }

}