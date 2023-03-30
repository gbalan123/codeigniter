<?php 

namespace App\Controllers;

class Script extends BaseController
{

    function __construct() {
        $this->encrypter = \Config\Services::encrypter();
        $this->db = \Config\Database::connect();
        helper('percentage_helper');
    }

    function encrypt_mail(){
		$username = "Your_key";
		$passw = "Your_value";
		$enuser_name = base64_encode($this->encrypter->encrypt($username));
		$enuser_password = base64_encode($this->encrypter->encrypt($passw));
		echo "encrypt username- ".$enuser_name." encrypt password- ".$enuser_password;echo"<br />";echo"<br />";
		$deuser_name = $this->encrypter->decrypt(base64_decode($enuser_name));
		$deuser_password = $this->encrypter->decrypt(base64_decode($enuser_password));
		echo "decrypt username- ".$deuser_name." encrypt password- ".$deuser_password;
	}

	// Encrypt For Ci4
    public function encrypt_password() {

        $tds_test_query = $this->db->query('SELECT * FROM password_tbl where status = "0" LIMIT 500 ');
        $result = $tds_test_query->getResultArray();

        echo "<pre>";
        $count = 0;
        foreach($result  as $res){

            if(!empty($res['decrypt_password'])){
                $enc_pass = base64_encode($this->encrypter->encrypt($res['decrypt_password']));

                echo "<br>";
                echo "Password visible : ";print_r($res['decrypt_password']); 
				echo "<br>";
                echo "Encrypt password : "; print_r($enc_pass);

                // update encrypt password for user table
                $data = array(
                    'password_visible' => $enc_pass
                );
                $builder = $this->db->table('users');
                $builder->where('id', $res['user_id']);
                $builder->update($data);

                // Update status for password_tbl
                $data2 = array(
                    'status' => 1
                );
                $builder = $this->db->table('password_tbl');
                $builder->where('user_id', $res['user_id']);
                $builder->update($data2);
                
                $count++;    
            }
        }
        echo "<br>";echo "<br>";
        echo 'Total affected Count : ';
        echo $count;
        exit;
    }

    // Decrypt For Ci3 code (Not working on ci4)
    public function decrypt_password() {

        $tds_test_query = $this->db->query('SELECT * FROM users');
        $result = $tds_test_query->result_array();

        echo "<pre>";
        $count = 0;
        foreach($result  as $res){

            if(!empty($res['password_visible'])){

                // $enc_pass = base64_encode($this->encrypter->encrypt($res['password_visible']));
                $dec_pass = $this->encrypt->decode($res['password_visible']);

                echo "<br>";
                echo " password visible : ";  print_r($res['password_visible']);
                echo "<br>";
                echo "Decrypt password : "; print_r($dec_pass);

                $data = array(
                    'user_id'  => $res['id'],
                    'decrypt_password' => $dec_pass 
                );
                $this->db->insert('password_tbl', $data); 
                
                $count++;  
            }
        }
        echo $count;
    }



    function email_config_decrypt_ci4(){

		// echo "<pre>";
		for ($x = 1; $x <= 5; $x++) {
			// echo "Decrypt username :".$this->encrypt->decode($enuser_name[$x])."<br>";
			// echo "Decrypt Password :". $this->encrypt->decode($enuser_password[$x])." <br><br>";
			$config =  $this->get_email_config_provider_decrypt($x);
			print_r(json_encode($config));
			echo "<br>";echo "<br>";
		}exit;

	}

    function get_email_config_provider_decrypt($category_id){
        $encrypter = \Config\Services::encrypter();
        // $tdsmodel = new Tdsmodel();
        $sender_provider_data = $this->get_email_sender_provider_by_category($category_id);
        $config = json_decode($sender_provider_data[0]['key_value'], true);
        $smtp_config_array_decrypt = array();
        foreach($config as $key => $value){
            if($key == 'smtp_user'){
                  $value = $encrypter->decrypt(base64_decode($value)); 
            }
            if($key == 'smtp_pass'){
                  $value = $encrypter->decrypt(base64_decode($value));
            }
            $smtp_config_array_decrypt += [$key => $value];
        }
        return $smtp_config_array_decrypt;      
    }

    function get_email_sender_provider_by_category($category_id)
    {
        $builder = $this->db->table('email_service_provider ESP');
        $builder->select('ESP.key_name,ESP.key_value');
        // $builder->join('email_service_provider ESP', 'ESL.sender_id = ESP.id');
        $builder->where('ESP.id', $category_id);
        $builder->orderBy('ESP.id', 'Asc');
        $builder->limit(1);
        $query = $builder->get();
        $result= $query->getResultArray();
        return $result; 
    }


   

}
