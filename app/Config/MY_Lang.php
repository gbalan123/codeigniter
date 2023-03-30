<?php  
namespace Config;
use CodeIgniter\Config\BaseConfig;

class MY_Lang extends BaseConfig {

	/**************************************************
	 configuration
	***************************************************/

	// languages
	var $languages = array(
		'en' => 'english',
		'ms' => 'malay',
        'sr' => 'srpski',
	    'pt' => 'portuguese',
		'ta' => 'tamil',
        'my' => 'burmese'
            
	);

	// special URIs (not localized)
	var $special = array (
		"/"
	);
	
	// where to redirect if no language in URI
	var $default_uri = ''; 

	/**************************************************/
	
	function __construct()
	{
		global $CFG;
		$uri = service('uri');
		$this->request = \Config\Services::request();
		$this->request->setLocale($this->default_lang());
		$segment    = $this->request->uri->getSegment(1);
		if (isset($this->languages[$segment]) && $this->languages[$segment] == $this->languages[$this->default_lang()])	// URI with language -> ok
		{
			$language = $this->languages[$segment];
			$CFG['language'] = $language;
		} else {
			$segment    = $this->request->uri->getSegment(1);
			if(!in_array($segment, (new App())->supportedLocales))
			{
				$path = $uri->setPath($this->request->getPath())->getPath();
				$Query = ($this->request->uri->getQuery()) ? "?".$this->request->uri->getQuery() : '';
				header("Location: " . site_url($path.$Query), TRUE, 302);
				exit;
			}
			else
			{
				$path = $uri->setPath($this->request->getPath())->getPath();
				$New = explode('/', $path);
				unset($New[0]);
				$Query = ($this->request->uri->getQuery()) ? "?".$this->request->uri->getQuery() : '';
				header("Location: " . site_url(implode('/', $New).$Query), TRUE, 302);
				exit;
			}
		}
	}

	// get current language
	// ex: return 'en' if language in CI config is 'english' 

	function lang()
	{
		global $CFG;	
		
    if($CFG != NULL){
	   $language = $CFG['language'];
		$lang = array_search($language, $this->languages);
		if ($lang)
		{
			return $lang;
		}
		}else{

			$lang = service('request')->getLocale();
			return $lang;

		}
	
	}
	
	function is_special($uri)
	{
		$exploded = explode('/', $uri);
		if (in_array($exploded[0], $this->special))
		{
			return TRUE;
		}
		if(isset($this->languages[$uri]))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	function switch_uri($lang)
	{

		$uri = uri_string(); // Output: admin
        
		if ($uri != "")
		{
			$exploded = explode('/', $uri);
		
			if($exploded[0] == $this->lang())
			{
				$exploded[0] = $lang;
			}
			if($exploded[0] != $this->lang())
			{
				$exploded[0] = $lang;
			}
			$uri = implode('/',$exploded);
		}

		return $uri;
	}
	
	// is there a language segment in this $uri?
	function has_language($uri)
	{
		$first_segment = NULL;
		
		$exploded = explode('/', $uri);
		if(isset($exploded[0]))
		{
			if($exploded[0] != '')
			{
				$first_segment = $exploded[0];
			}
			else if(isset($exploded[1]) && $exploded[1] != '')
			{
				$first_segment = $exploded[1];
			}
		}
		
		if($first_segment != NULL)
		{
			return isset($this->languages[$first_segment]);
		}
		
		return FALSE;
	}
	

		// default language: first element of $this->languages
	function default_lang()
	{
		helper('cookie');
		$lang = ($this->request->getCookie('lang')) ? $this->request->getCookie('lang') : 'en';
		return $lang;
	}
	
	// add language segment to $uri (if appropriate)
	function localized($uri)
	{
		if($this->has_language($uri)
				|| $this->is_special($uri)
				|| preg_match('/(.+)\.[a-zA-Z0-9]{2,4}$/', $uri))
		{

		}
		else
		{
			$uri = $this->lang() . '/' . $uri;
			
		}
		return $uri;
	}

	public function set_item($item, $value)
	{

		$this->config[$item] = $value;
	}

	function site_url($uri = '',  $protocol = NULL)
	{	

		if (is_array($uri))
		{
			$uri = implode('/', $uri);
		}
		else
		{
			$uri = $this->localized($uri);			
		}

		return site_url($uri);
	}

	
}

/* End of file */
