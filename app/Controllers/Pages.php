<?php
namespace App\Controllers;
use Config\MY_Lang;
use App\Models\Admin\Cmsmodel;
use App\Controllers\BaseController;
class Pages extends BaseController {

    protected static $loginAttribute = 'email';
    public $data;
    public $languages;
    
    function __construct() {
		helper('form');
		helper('downtime_helper');
		helper('percentage_helper');
		//initialization of request
        $this->request = \Config\Services::request();
	
		//model initialization
        $this->cmsmodel = new Cmsmodel();
		$this->data['lang_code'] = $this->request->getLocale();
        // ini_set('max_execution_time', 300); //300 seconds = 5 minu
        // parent::__construct();
        date_default_timezone_set('GMT');
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) 
		{
            // last request was more than 2 hours ago
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


        //WP - 1363 -Downtime holding page
        $downtime = @downtime_maintenance_page();
        if($downtime === TRUE){
            echo view('maintenance_page');
            die;
        }

    }
	
	public function about_us(){
        $this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/about-us', $this->data);
		echo view('site/footer');
	}
	 
    // privacy Notice
	public function privacy_notice(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/privacy_notice', $this->data);
		echo view('site/footer');
	}
	
	// Terms and conditions
	public function terms_conditions(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/terms-conditions', $this->data);
		echo view('site/footer');
	}
	
	// About our Courses & Tests - name changed as CATs Steps
	  public function cats_steps(){
	    $data['languages'] = $this->cmsmodel->get_language();
		$data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $data);
		echo view('site/menus', $data);
		echo view('pages/cats_steps', $data);
		echo view('site/footer');
	}

	// CATs Step expert say
	public function cats_step_expert_say(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_steps_expert_say', $this->data);
		echo view('site/footer');
	}

	// CATs Step learner say
	public function cats_step_learner_say(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_steps_learner_say', $this->data);
		echo view('site/footer');
	}
	
	// About CATS Step Networks
	public function cats_step_networks(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_step_networks', $this->data);
		echo view('site/footer');
	}
	
	// Information for Educator - name changed as CATs Solution
	public function cats_solution(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_solution', $this->data);
		echo view('site/footer');
	}
	
	// Benchmarking and Verification Services - name changed as CATs StepCheck
	public function cats_stepcheck(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck', $this->data);
		echo view('site/footer');
	}
        
	public function cats_stepcheck_employers(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_employers', $this->data);
		echo view('site/footer');
	}

	public function cats_stepcheck_education(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_education', $this->data);
		echo view('site/footer');
	}
        
	public function cats_stepcheck_goverment(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_goverment', $this->data);
		echo view('site/footer');
	}
        
	public function cats_stepcheck_format(){	
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_format', $this->data);
		echo view('site/footer');
	}
}
