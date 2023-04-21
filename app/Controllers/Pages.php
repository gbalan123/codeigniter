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
        $this->request = \Config\Services::request();
        $this->cmsmodel = new Cmsmodel();
		$this->data['lang_code'] = $this->request->getLocale();
        date_default_timezone_set('GMT');
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) 
		{
            session_unset();     
            session_destroy();  
        }
		/* update last activity time stamp */
        $_SESSION['LAST_ACTIVITY'] = time(); 
		
        /* WP - 1363 -Downtime holding page */
        $downtime = @downtime_maintenance_page();
        if($downtime === TRUE){
            echo view('maintenance_page');
            die;
        }

    }
	/* about_us cms page */
	public function about_us(){
        $this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/about-us', $this->data);
		echo view('site/footer');
	}
	 
    /* privacy Notice cms page */
	public function privacy_notice(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/privacy_notice', $this->data);
		echo view('site/footer');
	}
	
	/* Terms and conditions cms page */
	public function terms_conditions(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/terms-conditions', $this->data);
		echo view('site/footer');
	}
	
	/* About our Courses & Tests - name changed as CATs Steps cms page */
	  public function cats_steps(){
	    $data['languages'] = $this->cmsmodel->get_language();
		$data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $data);
		echo view('site/menus', $data);
		echo view('pages/cats_steps', $data);
		echo view('site/footer');
	}

	/* CATs Step expert say cms page */
	public function cats_step_expert_say(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_steps_expert_say', $this->data);
		echo view('site/footer');
	}

	/* CATs Step learner say cms page */
	public function cats_step_learner_say(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_steps_learner_say', $this->data);
		echo view('site/footer');
	}
	
	/* About CATS Step Networks cms page */
	public function cats_step_networks(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_step_networks', $this->data);
		echo view('site/footer');
	}
	
	/* Information for Educator - name changed as CATs Solution cms page */
	public function cats_solution(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_solution', $this->data);
		echo view('site/footer');
	}
	
	/* Benchmarking and Verification Services - name changed as CATs StepCheck cms page */
	public function cats_stepcheck(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck', $this->data);
		echo view('site/footer');
	}
    /* cats_stepcheck_employers cats cms page */
	public function cats_stepcheck_employers(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_employers', $this->data);
		echo view('site/footer');
	}
    /* cats_stepcheck_education cats cms page */ 
	public function cats_stepcheck_education(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_education', $this->data);
		echo view('site/footer');
	}
    /* cats_stepcheck_goverment cats cms page */ 
	public function cats_stepcheck_goverment(){
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_goverment', $this->data);
		echo view('site/footer');
	}
    /* cats_stepcheck_format cats cms page */ 
	public function cats_stepcheck_format(){	
		$this->data['languages'] = $this->cmsmodel->get_language();
		$this->data['lang_code'] = $this->request->getLocale();
		echo view('site/header', $this->data);
		echo view('site/menus', $this->data);
		echo view('pages/cats_stepcheck_format', $this->data);
		echo view('site/footer');
	}
}
