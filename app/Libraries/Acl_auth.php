<?php 
namespace App\Libraries;
use Config\MY_Config; 
use App\Models\Usermodel;
use Config\Site;
use Config\PasswordHash;

/**
 * CodeIgniter ACL Class
 *
 * ACL library for CodeIgniter Framework
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author 		David Brandes <david.brandes at gmail.com>
 * @link 		https://github.com/brandesign/CodeIgniter-ACL
 * @copyright 	Copyright (c) 2012, David Brandes
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Acl_auth
{
	/**
	 * List of all errors
	 *
	 * @var array
	 */
	private $_errors = array();
	private $_config;
    public $billion;
        
	public function __construct()
	{
		$this->_config = new Site();
		$this->passwordhash = new PasswordHash();
		$this->user_model = new Usermodel();
		$this->session = session();
		$this->db = \Config\Database::connect();
		$this->billion = '1000300000';
	}

	public function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}

	/**
	* __get
	*
	* Enables the use of CI super-global without having to define an extra variable.
	*
	* @access public
	* @param $var
	* @return mixed
	*/
	// public function __get( $var )
	// {
	// 	return get_instance()->$var;
	// }

	/**
	 * register a new user
	 *
	 * @access public
	 * @param array
	 * @return bool
	 * @todo set error messages
	 **/

	public function register( $data )
	{
	
		if( ! array_key_exists($this->_config->identity_field, $data ) OR ! array_key_exists($this->_config->password_field, $data ) )
		{
			$this->set_error( 'register_failed' );
			return false;
		}
	
		$insert = array();

		foreach( $data as $field => $value )
		{
			if( $field == $this->_config->password_field )
			{

				$value = $this->passwordhash->HashPassword($value);
			}
			if( $this->user_model->field_exists($field))
			{
				$insert[$field] = $value;
			}
		}
             
		if( $id = $this->user_model->userinsert($insert) )
		{
			$learnerRole = array('roles_id'=> ($data['role'] != '' ) ? $data['role'] : 3, 'users_id' => $this->db->insertID());

			$builder = $this->db->table('user_roles');
			$builder->insert($learnerRole);
                        
                        //update the APP_USER ID
                        $learnerAppid = array('username' => (int)$this->billion + (int) $learnerRole['users_id'],  'user_app_id' => (int)$this->billion + (int) $learnerRole['users_id']);

						$builder = $this->db->table('users');
						$builder->where('id',$learnerRole['users_id']);
						$builder->update($learnerAppid);

			$session_data = array('firstname', 'lastname', 'email','username','id','user_app_id','country','organization_name');
			$this->login( $data['email'], $data['password'], TRUE, $session_data );
			return true;
		}
		else
		{
			$this->set_error('register_failed');
			return false;
		}
	}
	
	
	/**
	* register by google oauth
	*
	* @access public
	* @param array
	* @return bool
	* @todo set error messages
	**/

	
	
	/**
	* login by google
	*
	* @access public
	* @param string
	* @param string
	* @return bool
	**/
	
	/**
	 * login
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 **/
	public function login($identity, $password, $remember = FALSE, $session_data = array() )
	{
		$user = $this->user_model->get_user($identity);

		if( ! $user OR ! $this->passwordhash->CheckPassword($password,$user['password']) )
		{
			$this->set_error( 'login_failed' );
			return false;
		}
		$session = array(
			'user_id'	=> $user['id'],
			'user_firstname'	=> $user['firstname'],
			'user_lastname'	=> $user['lastname'],
			'country'	=> $user['country'],
            'organization_name' => $user['organization_name'],
			'user_app_id'	=> $user['user_app_id'],
			'logged_in'=> TRUE,
			'user_'.$this->_config->identity_field => $user['email']
		);

		$this->session->set( $session );
		return true;
	}

	/**
	 * logout
	 *
	 * @access public
	 * @return bool
	 **/
	public function logout()
	{
		$this->session->destroy();
		return true;
	}

	/**
	 * is the user logged in?
	 *
	 * @access public
	 * @return bool
	 **/

	public function logged_in()
	{
		$session = session();
		return (bool) $this->session->get('logged_in');
	}

	/**
	 * Send password reset
	 *
	 * @access public
	 * @param string
	 * @return bool
	 **/
	
    

	/**
	 * Check if reset token is valid
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 **/



	/**
	 * Confirm password reset
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @param string
	 * @return bool
	 **/
	public function set_new_password( $identity, $token, $newpass )
	{
		$user = $this->user_model->get_user( $identity );
		if( ! $user OR ! $this->check_reset_token( $identity, $token ) )
		{
			$this->set_error( 'reset_user_not_found' );
			return false;
		}

		$data = array(
			'reset_code' => NULL
			,'reset_time'=> NULL
			,$this->_config['password_field'] => $this->phpass->hash( $newpass )
		);

		if( $this->user_model->update( $user->id, $data ) )
		{
			$session = array();
			foreach( $user as $k => $v )
			{
				$session[] = $k;
			}
			$this->login( $identity, $newpass, $session );
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * generate reset code
	 *
	 * @access private
	 * @return string
	 **/
	private function _reset_code()
	{
		$ret = '';
		for( $x = 0; $x < 32; $x++ )
		{
			$chars = array(
				chr( mt_rand( 48, 57 ) )
				,chr( mt_rand( 64, 90 ) )
				,chr( mt_rand( 97, 122 ) )
			);
        	//$ret .= chr( mt_rand( 0, 255 ) );
        	$ret .= $chars[array_rand($chars)];
    	}
    	return $ret;
	}

	/**
	 * Checks if a user has a role
	 *
	 * @access public
	 * @param int
	 * @param string
	 * @return bool
	 **/

	public function has_role( $role, $user_id = NULL )
	{
		if( is_null( $user_id ) )
		{
			$user_id = $this->session->get('user_id');
		}
		return (bool) $this->user_model->has_role($user_id, $role);
	}

	/**
	 * Act if user has no access
	 *
	 * @access public
	 * @param string
	 * @param array
	 * @return void
	 * @todo allow to set some actions on denied access
	 **/

	public function restrict_access( $role, $actions = array() )
	{

		$has_role 	= false;
		switch($role)
		{
		    case 'guest':
			   $has_role = true;
			   break;
		    case 'logged_in':
			if( $this->logged_in() )
				{
					$has_role = true;

				}
				break;
			default:
				if( $this->logged_in() )
				{
					$has_role = $this->has_role($role);
				}
				break;
		}

		if(!$has_role)
		{

			if(!$this->logged_in())
			{
				//THIS IS DEFAULT
				if( strlen($this->_config->login_page) > 0 )
				{
					return redirect()->to($this->_config->login_page ); 
				}
				else if( strlen($this->_config->override) > 0 )
				{
					redirect( $this->_config->override );
				}
				else
				{
					echo"Unauthorized - another user or distributor or admin has logged in the same browser! 1";
					exit;
				}
			}
			else
			{
				if( strlen($this->_config->override) > 0 )
				{
					return redirect()->to($this->_config->override ); 
				}
				else
				{
					echo"Unauthorized - another user or distributor or admin has logged in the same browser! 3";
					exit;
				}
			}
		}
	}



	/**
	 * Set error message
	 *
	 * @access private
	 * @param string
	 * @return void
	 **/
	private function set_error( $error )
	{
		$this->_errors[] = $error;
	}

	/**
	 * Get error messages
	 *
	 * @access public
	 * @return array
	 **/
	public function errors()
	{
		foreach ( $this->_errors as $key => $error )
		{
			$this->_errors[$key] = $this->lang->line( $error ) ? $this->lang->line( $error ) : '##' . $error . '##';
		}
		return $this->_errors;
	}
}