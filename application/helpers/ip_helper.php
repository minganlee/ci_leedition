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
 * CodeIgniter IP Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		auiog413 <lbzqh156@gmail.com>
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

/**
 * get ip address location from taobao ip location web service
 *
 * Lets you get a location of ip address via taobao.com ip location web service
 * This helper need Spider library depend on curl extension
 *
 * @access	public
 * @param	string
 * @return	array
 */
if ( ! function_exists('ip_location'))
{
	function ip_location($ip_string = '')
	{
		if (valid_ip($ip_string))
		{
			$CI =& get_instance();

			$spider_config = array('url' => 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip_string);
			$CI->load->library('spider', $spider_config);
			$ip_info = $CI->spider->exec();
			if ($ip_info['http_code'] == 200)
			{
				return json_decode($ip_info['content'], true);
			}else{
				return array('code' => 1);
			}
		}else{
			return array('code' => 1);
		}
	}
}

/**
 * Judge a ip address string is a valid ip address
 *
 * @access	public
 * @param	string
 * @return	boolean
 *				true for valid
 *				false for invalid
 */
if ( ! function_exists('valid_ip'))
{
	function valid_ip($ip_string = ''){
		if (empty($ip_string))
		{
			return false;
		}

		if ( ! strcmp(long2ip(sprintf('%u',ip2long($ip_string))),$ip_string))
		{
		   return true;
		}else{
		   return false;
		}
	}
}

/* End of file ip_helper.php */
/* Location: ./application/helpers/ip_helper.php */