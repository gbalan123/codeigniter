<?php

namespace App\Models\Admin;//path
use CodeIgniter\Model;

class Brochuremodel extends Model {

	public $db;
	public function __construct()
	{
		helper('cms');
        $this->db = \Config\Database::connect();
	
	}
    
    public function record_document_count() {
        return $this->db->table('documents')->countAll();
    }



}
