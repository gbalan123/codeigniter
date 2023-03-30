<?php 
namespace App\Models\Admin;//path
use CodeIgniter\Model;
class Pricemodel extends Model
{

	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
	}

	//count
	public function record_count() {
		return $this->db->table('prices')->countAll();
	}
	

}