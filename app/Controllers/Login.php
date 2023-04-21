<?php

namespace App\Controllers;
use App\Models\Admin\Cmsmodel;
use App\Models\Usermodel;
use Config\MY_Lang;
use App\Models\Admin\Emailtemplatemodel;
use App\Controllers\BaseController;
use App\Libraries\Acl_auth;
use Config\Oauth;

class Login extends BaseController
{
    function __construct() {
        $this->db = \Config\Database::connect();
        $this->cmsmodel = new Cmsmodel();
        $this->oauth = new \Config\Oauth();
        $this->user_model = new Usermodel();
        $this->validation =  \Config\Services::validation();
        $this->request = \Config\Services::request();
        $this->lang = new \Config\MY_Lang();
        $this->email = \Config\Services::email();
        $this->session = \Config\Services::session();
        $this->passwordhash = new \Config\PasswordHash(8,FALSE);
        $this->emailtemplate_model = new Emailtemplatemodel();
        $this->acl_auth = new Acl_auth();
        $langcode = $this->lang->lang();
        helper('form');
        helper('cookie');
        helper('url');
        helper('downtime_helper');
		helper('percentage_helper');
        helper('sendinblue_helper');
        helper('zendesk');
        $this->data['languages'] = $this->cmsmodel->get_language();
        $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
        $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');
    }

    public function index()	{
        $return_url = isset($_GET['return_to']) && ($_GET['return_to'] != "") ? $_GET['return_to'] : FALSE;
        $user_id = $this->session->get('user_id');
        if(isset($this->zendesk_access) && $this->zendesk_access == 1 && $return_url != FALSE && isset($user_id) && $user_id !=""){
            $url = @get_zend_desk_url($user_id);
            $redirect_url = $return_url != FALSE ? $url."&return_to=".$return_url : $url;
            return redirect()->to($redirect_url);
        }elseif(isset($this->zendesk_access) && $this->zendesk_access == 1 && isset($user_id) && $user_id !=""){
            $url = @role_based_redirection();
            return redirect()->to($url['home_page_url']);
        }else{
            $langcode = $this->lang->lang();
            $this->data['languages'] =$this->cmsmodel->get_language();
            $this->data['cmsvalue'] =$this->cmsmodel->get_cms_contents('home', $langcode);
            $this->data['navbar'] =$this->cmsmodel->get_all_cms($langcode);

            echo  view('site/header');
            echo  view('site/home_menu', $this->data);
            echo  view('site/home_login');
            echo  view('site/footer');
        }  
	}
    /*support logout function */
    function support_logout() {
        if ($this->acl_auth->logged_in()) {
            $url = @role_based_redirection();
            return redirect()->to($url['home_page_url']);
        } else {
            return redirect()->to('/');
        }
    }
        /* Forgot password */
        function forgot_password() {
     
            if($this->request->getMethod() == 'post' && $this->request->getPost('email')) {
    
            $rules = [
                'email' => [
                    'label'  => lang('app.email'),
                    'rules'  => 'required|valid_email',
                ],
            ];
            if (!$this->validate($rules)) {
           
                echo  view('site/header');
                echo  view('site/menus', $this->data);
                echo  view('forgot_password', $this->data);
                echo  view('site/footer');
            } else {
                    $user_info = $this->user_model->email_exists($this->request->getPost('email'));

                    if (isset($user_info['0'])) {
                        $temppass = md5(uniqid());
                        $user_details = $user_info['0'];

                        if($user_details->is_active == 1){
                            $config =  @get_email_config_provider(2);
                            $lang_code = $this->lang->lang();
                            $template_email = $this->emailtemplate_model->fetch_template(false, false, 3, $lang_code);
                            $template_email_new = $this->email_lib('forgot-password');
                            $label = array("##FIRSTNAME##", "##LASTNAME##", "##USER_EMAIL##", "##RESET_URL##");
                            $values = array($user_details->firstname, $user_details->lastname, $user_details->email, site_url('login/reset_password') . '/' . $temppass);
                            $replaced_content = str_replace($label, $values, $template_email_new);
                            $mail_message = $replaced_content;

                            if(isset($config['smtp_user']) && $config['smtp_user'] == "Api-Key:"){
                                $sendSmtpEmail['subject'] = $template_email['0']->subject;
                                $sendSmtpEmail['htmlContent'] = $mail_message;
                                $sendSmtpEmail['sender'] = array('name' => $template_email['0']->display_name, 'email' => $template_email['0']->from_email);
                                $sendSmtpEmail['to'] = array(array('email' => $user_info['0']->email));
                                $data = json_encode($sendSmtpEmail, JSON_HEX_QUOT | JSON_HEX_TAG);
                                $response_data = @email_sendinblue(json_encode($sendSmtpEmail),$config);
                                $response = json_decode($response_data);
                                if(isset($response->messageId) && !empty($response->messageId)){
                                    $sent_mail_status = true;
                                    $sent_mail_log = $response_data;
                                }else{
                                    $sent_mail_status = false;
                                    $sent_mail_log = $response_data;
                                }
                            }else{ 
                                $this->email->initialize($config);
                                $this->email->setFrom($template_email['0']->from_email, $template_email['0']->display_name);
                                $this->email->setTo($user_info['0']->email);
                                $this->email->setMailtype("html");
                                $this->email->setNewline("\r\n");
                                $this->email->setCrlf("\r\n"); 
                                $this->email->setSubject($template_email['0']->subject);
                                $this->email->setMessage($mail_message);
                                if ($this->email->send()) {
                                     $sent_mail_status = true;
                                     $sent_mail_log = 'success';
                                }else{
                                    $sent_mail_status = false;
                                    $sent_mail_log = json_encode($this->email->printDebugger());  
                                }
                            }

                            /* log tables affected from here */
                            if ($sent_mail_status) {
                                $mail_log = array(
                                    'from_address' => $template_email['0']->from_email,
                                    'to_address' => $user_details->email,
                                    'response' => $sent_mail_log,
                                    'status' => 1,
                                    'purpose' => $template_email['0']->subject
                                );
                                $builder = $this->db->table('email_log');
                                $builder->insert($mail_log);
                                $values['id'] = $user_info['0']->id;
                                $values['temp_pass'] = $temppass;
                                log_message('error', "forgot password debug - " .$this->email->printDebugger());
                                if ($this->user_model->update_tempass($values)) {
                                    $this->session->setFlashdata('successmessage', lang('app.forgot_mail_success'));
                                    return redirect()->to('/');
                                }
                            } else {
                                $mail_log = array(
                                    'from_address' => $template_email['0']->from_email,
                                    'to_address' => $user_details->email,
                                    'response' => $sent_mail_log,
                                    'status' => 0,
                                    'purpose' => $template_email['0']->subject
                                );
                                $builder = $this->db->table('email_log');
                                $builder->insert($mail_log);
                                $this->session->setFlashdata('message', lang('app.forgot_mail_failure'));
                                log_message('error', "Email not send - " .$this->email->printDebugger());
                                return redirect()->to(current_url());
                            }



                        }else {
                            $this->session->setFlashdata('message', lang('app.language_site_forget_password_inactive'));
                            return redirect()->to(current_url());
                        } 
                    } else {
                        $this->session->setFlashdata('message', lang('app.forgot_mail_failure_db'));
                        return redirect()->to(current_url());
                    }
                }
            }else{
            echo  view('site/header');
            echo  view('site/menus', $this->data);
            echo  view('forgot_password', $this->data);
            echo  view('site/footer');
            }
        }

        /* Reset password */
        function reset_password($temppass = false) {

            if (!empty($temppass)) {
                $result = $this->user_model->is_temp_pass_valid($temppass);
                if ($result) {
                    $this->data['token'] = trim($result['0']['temp_pass']);
                    $this->data['id'] = $result['0']['id'];
                } else {
                    $this->session->setFlashdata('reset_failure', lang('app.reset_token_mismatch'));
                    return redirect()->to('login/reset_password');
                }
            }
            
            if (isset($_POST['newpassword']) && isset($_POST['confirmpassword'])) {

              
                $rules = [
                    'newpassword' => [
                        'label'  => lang('app.new_password'),
                        'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                        'errors' => [
                            'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                        ],
                    ],
                    'confirmpassword' => [
                        'label'  => lang('app.confirm_password'),
                        'rules'  => 'required|min_length[8]|matches[newpassword]|new_password_check',
                        'errors' => [
                            'new_password_check' => lang('app.language_site_booking_screen2_password_check'),
                        ],
                    ],
                ];
    
            if (!$this->validate($rules)) {
                $form_data = $this->request->getPost();
                if (!empty($form_data['id']) && !empty($form_data['token'])) {
                    $this->data['token'] = $form_data['token'];
                    $this->data['id'] = $form_data['id'];
                }
                echo view('site/header');
                echo view('site/menus', $this->data);
                echo view('reset_password', $this->data);
                echo view('site/footer');
            } else {
                $form_data = $this->request->getPost();
                if (!empty($form_data['id']) && !empty($form_data['token'])) {
                    $this->data['token'] = $form_data['token'];
                    $this->data['id'] = $form_data['id'];
                }
                if (!empty($form_data['id']) && !empty($form_data['token']) && !empty($form_data['confirmpassword']) && !empty($form_data['newpassword'])) {
                    $this->passwordhash = new \Config\PasswordHash(8,FALSE);
                    $values['id'] = $form_data['id'];
                    $values['password'] = $this->passwordhash->HashPassword($form_data['newpassword']);
                    if ($this->user_model->update_password($values)) {
                        $this->session->setFlashdata('reset_success', lang('app.reset_success'));
                        return redirect()->to('/');
                    } else {
                        $this->session->setFlashdata('reset_failure', lang('app.reset_unable'));
                        return redirect()->to('login/reset_password');
                    }
                } else {
                    $this->session->setFlashdata('reset_failure', lang('app.reset_cannot'));
                    return redirect()->to('login/reset_password');
                }
            }
        }
        else{
            echo view('site/header');
            echo view('site/menus', $this->data);
            echo view('reset_password', $this->data);
            echo view('site/footer'); 
        }
        }
    /*Get email content function */
    public function email_lib($slug = NULL){
        $parser = \Config\Services::renderer();
		if(!empty($slug)){
			$query = $this->db->query('SELECT E.* FROM email_categories EC JOIN email_templates E ON E.category_id = EC.id WHERE E.language_code = "en" AND EC.category_slug = "'.$slug.'"');
            $results = $query->getRow();	
            $this->data['email_content'] = $results->content;
            $test_data = $parser->setData($this->data)->renderString($this->data['email_content']);
            return $test_data;
		}
	}
    /* Password setup for school users */
    function password_setup($temppass = false) {

        $this->data['languages'] = $this->cmsmodel->get_language();
        
        if($this->request->getPost('hiddenvalue') == 1)
        {
            $rules = [
                'password' => [
                    'label'  => 'Password',
                    'rules'  => 'required|min_length[8]|max_length[20]|new_password_check',
                    'errors' => [
                        'new_password_check' => lang('app.language_site_booking_screen2_password_check')
                    ]
                ],
                'confirmpassword' => [
                    'label'  => 'Confirm password',
                    'rules'  => 'required|min_length[8]|max_length[20]|matches[password]|new_password_check',
                    'errors' => [
                        'new_password_check' => lang('app.language_site_booking_screen2_password_check')
                    ]
                ],
            ];
        }else{
            $rules = array();
        }
        if (!empty($temppass)) {
            $result = $this->user_model->school_temp_code_valid($temppass);
            if ($result) {
				$current_time = time();
				if($current_time >= $result[0]['expiration_date']){
					$this->session->setFlashdata('reset_failure', lang('app.password_setup_token_expired'));
                    return redirect()->to('/');
				} else {
					$this->data['token'] = trim($result[0]['activation_code']);
					$this->data['id'] = $result[0]['id'];
				}
            } else {
                $this->session->setFlashdata('reset_failure', lang('app.reset_token_mismatch'));
                return redirect()->to(site_url('login/password_setup')); 
            }
        }

        if (!$this->validate($rules)) {

            $this->data['validation'] = $this->validator;

            $form_data = $this->request->getPost();

            if (!empty($form_data['id']) && !empty($form_data['token'])) {
                $this->data['token'] = $form_data['token'];
                $this->data['id'] = $form_data['id'];
            }

            echo view('site/header');
            echo view('site/menus', $this->data);
            echo view('password_setup', $this->data);
            echo view('site/footer');
        }
        else{

            $form_data = $this->request->getPost();
            if (!empty($form_data['id']) && !empty($form_data['token'])) {
                $this->data['token'] = $form_data['token'];
                $this->data['id'] = $form_data['id'];
            }
            if (!empty($form_data['id']) && !empty($form_data['token']) && !empty($form_data['confirmpassword']) && !empty($form_data['password'])) {
                
                $values['id'] = $form_data['id'];
                
                $values['password'] = $this->passwordhash->HashPassword($form_data['password']);

                if ($this->user_model->update_password($values)) {
                    $this->session->setFlashdata('reset_success', lang('app.reset_success'));
                    return redirect()->to(site_url());
                } else {
                    $this->session->setFlashdata('reset_failure', lang('app.reset_unable'));
                    return redirect()->to(site_url('login/password_setup'));
                }
            } else {
                $this->session->setFlashdata('reset_failure', lang('app.reset_cannot'));
                return redirect()->to(site_url('login/password_setup'));
            }
        }
    } 
    
    
  
}
