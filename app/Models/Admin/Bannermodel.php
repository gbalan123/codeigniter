<?php 

namespace App\Models\Admin;//path

use CodeIgniter\Model;
class Bannermodel extends Model
{

	public $db;
    public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
	}
    /* Function to get record count */
	public function record_count() {
		return $this->db->table('banner_translation')->countAll();
	}

}
