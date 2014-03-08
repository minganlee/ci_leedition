<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		auiog413 <lbzqh156@gmail.com>
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Email Class
 *
 * Permits email to be sent using Mail, Sendmail, or SMTP.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		auiog413 <lbzqh156@gmail.com>
 * @link		http://codeigniter.com/user_guide/libraries/email.html
 */
class Spider
{
	/**
	 * User agent for spider to use
	 */
	private $_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36 CI_Leedition Spider';

	/**
	 * http request method for spider to use. Possible values: post|get|POST|GET, Caseless
	 */
	private $_method = 'GET';

	/**
	 * http cookie data to use when request a url
	 */
	private $_cookie = NULL;

	private $_timeout = '';

	private $_http_referer = '';

	private $_url = NULL;

	private $_post_fields = array();

	private $_returntransfer = 1;

	private $_file = '';

	private $_http_header = '';

	/**
	 * Constructor - Sets Spider Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		$this->_dependence();

		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		log_message('debug', "Spider Class Initialized");
	}

	/**
	 * check if curl extension is exists
	 * 
	 * @access private
	 * @return void
	 */
	private function _dependence()
	{
		if (!function_exists('curl_version'))
		{
			show_error('Your PHP Interpreter May Be Missing CURL extension.');
		}
	}

	/**
	 * init the spider's configure items
	 *
	 * @access public
	 * @param array
	 * @return void
	 */
	public function initialize($config = array())
	{
		if (!empty($config))
		{
			foreach ($config as $key => $value) {
				$key = '_' . $key;
				$this->$key = $value;
			}
		}
	}

	/**
	 *
	 */
	public function exec()
	{
		$return_array = array();

		$ch = curl_init();

		if ($this->_url == NULL)
		{
			show_error('Spider need URL to access.');
		}
		curl_setopt($ch, CURLOPT_URL, $this->_url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->_returntransfer);

		if ($this->_user_agent)
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $this->_user_agent);
		}
		
		if ($this->_cookie){
			curl_setopt($ch, CURLOPT_COOKIE, $this->_cookie);
		}

		if ($this->_method == 'POST')
		{
			curl_setopt($ch, CURLOPT_POST, true);
		}

		if (!empty($this->_post_fields))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_post_fields);
		}
 
		if ($this->_http_referer)
		{
			curl_setopt($ch, CURLOPT_REFERER, $this->_http_referer);
		}

		if ($this->_http_header)
		{
			curl_setopt($c, CURLOPT_HTTPHEADER, $this->_http_header);
		}
		
		if ($this->_file)
		{
			curl_setopt($ch, CURLOPT_FILE, fopen($this->_file,'wb'));
		}

		$return_array['content'] = curl_exec($ch);

		$return_array['info'] = curl_getinfo($ch);

		curl_close($ch);

		return $return_array;
	}

	public function post()
	{
		$this->set_method('POST');
		return $this->exec();
	}

	public function get()
	{
		$this->set_method('GET');
		return $this->exec();
	}

	public function set_cookies($cookie_array = array())
	{
		if (!empty($cookie))
		{
			$cookie_string = '';
			foreach ($cookie_array as $key => $value) {
				$cookie_string .= $key . '=' . $value . ';';
			}
			$this->_cookie = $cookie_string;
		}
	}

	public function set_user_agent($user_agent = '')
	{
		$this->_user_agent = $user_agent;
	}

	public function set_method($request_method = 'GET')
	{
		$this->_method = strtoupper($request_method);
	}

	public function set_url($request_url = '')
	{
		$this->_url = $request_url;
	}

	public function set_file($file_path = '')
	{
		//if ( ! is_dir($file_path))
		//{
			
		//}

		//if ( ! is_writable($file_path))
		//{
		//	return FALSE;
		//}
		$this->_file = $file_path;
	}

	public function set_post_fields($post_fields = array())
	{
		// $this->_post_fields 
	}

	public function set_http_header($http_headers = array())
	{
		// key=>value array,使用ucfirst处理key以便不用区分大小写
	}
}
// END Spider class

/* End of file Spider.php */
/* Location: ./application/libraries/Spider.php */