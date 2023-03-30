<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
* Author: https://roytuts.com
*/

class Site extends BaseConfig {
	
    public $identity_field = 'email';
    public $password_field = 'password';
    public $user_table = 'users';
    public $user_model = 'user_model';

    public $admin_mail = '';
    public $admin_name = '';
    public $reset_subject = '';
    public $reset_template = '';

    public $override = '';
    public $login_page = '';



}