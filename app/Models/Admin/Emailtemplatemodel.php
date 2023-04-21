<?php

namespace App\Models\Admin;//path
use CodeIgniter\Model;

class Emailtemplatemodel extends Model {

    public $db;
    public function __construct() {
		helper('cms');
        $this->db = \Config\Database::connect();
        $this->request = \Config\Services::request();
    }
    /* Function to get language */
    public function get_language($lang_code = FALSE) {
        if ($lang_code != FALSE) {
            $builder = $this->db->table('language'); 
            $builder->where('code', $lang_code);
        } else {
            $builder = $this->db->table('language'); 
        }
        return  $builder->get()->getResult();
    }

    /* count WP-1384 */
    public function record_count() {
        $query =  $this->db->query('SELECT id FROM `email_templates` WHERE status = 1');
        $result_count = $query->getNumRows(); 
        return $result_count;
    }
    /* Function to get mailcategory */
    public function get_mailcategory($id = FALSE) {
        if ($id != FALSE) {
            $builder = $this->db->table('email_categories');
            $builder->where('id', $id); 

        }else {
            $builder = $this->db->table('email_categories');
            $builder->where('status', 1); 
        }
        return $result = $builder->get()->getResult();
    }
    /* Function to post mailcategorycheck */
    public function postmailcategorycheck($category_name) {
        $builder = $this->db->table("email_categories");
        $builder->where('category_slug',$category_name); 
        $result = $builder->get()->getNumRows();
        return $result ;
    }

    public function update_mail_category($id) {
        /* update to template table  */
        $data = [
            'category_name' => $_POST['category_name'],
            'category_description' => $_POST['category_description'],
            'category_slug' => slugify($_POST['category_name']),
            'created_at' => strtotime(date('d-m-Y h:i:s'))
        ];
        $builder = $this->db->table('email_categories');
        $builder->update($data, ['id' => $id]);
        return TRUE;

    }
    /* Function to insert mailcategorycheck */
    public function insert_mail_category() {
        /* insert to template table */
        $data = [
            'category_name' => $_POST['category_name'],
            'category_description' => $_POST['category_description'],
            'category_slug' => slugify($_POST['category_name']),
            'created_at' => strtotime(date('d-m-Y h:i:s'))
        ];
 
        $builder = $this->db->table('email_categories');
        $builder->insert($data);
        $result = $this->db->insertID();
        return $result;
    }
 
        /* Function todelete the email template by id */
        public function delete_email_templates($id = FALSE) {
            if ($id != FALSE) {
                $builder = $this->db->table('email_templates');
                $builder->where('id', $id);
                $builder->delete();
                return TRUE;
            }
        }
        /* Function to fetch_template */
        public function fetch_template( $limit=false, $start=false, $cat_id=false, $lang_code=false) {   
            $builder = $this->db->table('email_templates');
            if($this->request->getVar('order')== 'DESC') {
                if($this->request->getVar('val')== 'categ') {
                    $builder->orderBy("email_categories.category_name", "desc");
                }				
            }elseif($this->request->getVar('order')== 'ASC'){
                if($this->request->getVar('val')== 'categ'){
                    $builder->orderBy("email_categories.category_name", "asc");
                }
            
            }else{
                $builder->orderBy("email_categories.category_name", "asc");
            }		
           
            $builder->select('email_templates.*,email_categories.category_name, language.name as LANG_NAME ');
            $builder->join('email_categories', 'email_categories.id = email_templates.category_id');
            $builder->join('language', 'language.code = email_templates.language_code');
            $builder->limit($limit, $start);
            $builder->where('email_templates.status', 1);
            if(!empty($cat_id) && !empty($lang_code)){
                $builder->where('email_templates.category_id', $cat_id);
                $builder->where('email_templates.language_code', $lang_code);
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

    /* get template content for specific page */
    public function get_template_contents($slug = FALSE, $langcode = FALSE) {

        $builder = $this->db->table('email_categories');
        $builder->select('*');
        $builder->join('email_templates', 'email_categories.id = email_templates.category_id');
        $builder->where('email_categories.category_slug', $slug);
        $builder->where('email_templates.language_code', ($langcode != FALSE && $langcode != '') ? $langcode : 'en');
        $query = $builder->get();
        return $query->getResult();
    }
    /* get single Teempalte */
    public function get_template($id = FALSE) {
        $builder = $this->db->table('email_templates');
        if ($id != FALSE) {
            $builder->Where('id', $id);
            $query = $builder->get();
        } else {
            $query = $builder->get();
        }
        return $query->getResult();
    }

        /* update existing tempaltes */
        public function update_email_templates($id) {
       
            /* update to template translation table */
            $data = [
                'subject' => $this->request->getPost('subject'),
                'content' => html_entity_decode($this->request->getPost('content')),
                'display_name' => $this->request->getPost('display_name'),
                'from_email' => $this->request->getPost('from_email')
            ];
    
            $builder = $this->db->table('email_templates');
            $builder->update($data, ['id' => $id]);    
            return TRUE;
       
        }

    /* check if already exists template with language code */
    function check_exists_template($category_id = FALSE, $code = FALSE) {
        if ($category_id != FALSE && $code != FALSE) {

            $builder = $this->db->table('email_templates');
            $builder->where('category_id', $category_id);
            $builder->where('language_code', $code);
            $query = $builder->get();
            $result = $query->getResult();
            if (!empty($result)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($category_id != FALSE && $code == FALSE) {
            $query = $this->db->query('SELECT * FROM email_templates WHERE id ="' . intval($category_id) . '"');
            if ($query->getNumRows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /* insert new templates */
    public function insert_email_templates() {
        $datatemplate_trans = [
            'category_id' => $this->request->getPost('category_id'),
            'language_code' => $this->request->getPost('language_code'),
            'subject' => $this->request->getPost('subject'),
            'content' => html_entity_decode($this->request->getPost('content')),
            'display_name' => $this->request->getPost('display_name'),
            'from_email' => $this->request->getPost('from_email')
        ];
        /* insert to template translation table */
        $builder = $this->db->table('email_templates');
        $builder->insert($datatemplate_trans);
        return $this->db->insertID();
    }


}
