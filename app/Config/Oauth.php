<?php 
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Oauth extends BaseConfig
{

    function __construct()
	{
		$this->db = \Config\Database::connect();		
	}

function catsurl($data){

    if($data == 'AMN_SES_CONFIG' || $data == 'COLLEGE_PRE_ERROR_CODES' || $data == 'SENDINBLUE_CONFIG'){
        $builder = $this->db->table('config_links');
        $builder->where('status',1);
        $builder->where('key_name',$data);
        $query = $builder->get();
        $record = $query->getResult();
        if ($query->getNumRows() > 0) {
            foreach($record as $oauthinfo):
                $linkinfo = $oauthinfo->key_value;
                $valinfo = json_decode($linkinfo, true);
             endforeach;
             return $valinfo;
        } else {
            return FALSE;
        }

    }else{

        $builder = $this->db->table('config_links');
        $builder->where('status',1);
        $builder->where('key_name',$data);
        $query = $builder->get();
        $record = $query->getResult();
        if ($query->getNumRows() > 0) {
            foreach($record as $oauthinfo):
                $linkinfo = $oauthinfo->key_value;
             endforeach;
             return $linkinfo;
        } else {
            return FALSE;
        }
    }

}

}