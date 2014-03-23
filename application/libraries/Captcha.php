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
 * CodeIgniter Captcha Class
 *
 * Captcha Class to replace Captcha Helper.
 *
 * @package		CodeIgniter
 * @subpackage          Libraries
 * @category            Libraries
 * @author		auiog413 <lbzqh156@gmail.com>
 * @link		http://codeigniter.com/user_guide/libraries/email.html
 */
class Captcha
{
	/**
	 * word(s) to use in captcha image
	 */
	private $_word = '';
        
        /**
         * length of word to generate, 8 as default
         */
        private $_word_length = 8;
        
	/**
	 * captcha image path to create image file
	 */
	private $_image_path = 'statics/captcha/';

	/**
	 * captcha image url
	 */
	private $_image_url = '/statics/captcha/';

	/**
	 * captcha image height
	 */
	private $_image_width = '180';

	/**
	 * captcha image height
	 */
	private $_image_height = '35';

        /**
         * path of char font to use
         */
        private $_font_path = 'statics/fonts/CONSOLA.TTF';
        
        /**
         * font size
         */
        private $_font_size = '18';
        
        /**
	 * expire time of captcha
	 */
	private $_expiration = '7200';
        
        /**
         * captcha background color, use HTML color
         */
        private $_bg_color = '';
        
        /**
         * captcha image border color, use HTML color
         */
        private $_border_color = '';
        
        /**
         * captcha image text color, use HTML color
         */
        private $_text_color = '';
        
        /**
         * shadow color, use HTML color
         */
        private $_shadow_color = '';
        
        /**
         * grid color, use HTML color
         */
        private $_grid_color = '';
        
        /**
         * Letters case
         *
         * Available value: upper,lower,origin(default)
         */
        private $_letters_case = 'origin';
        
        /**
         * Store Captcha data to DB OR Cache OR NOT
         * 
         * $this->validate method is depend on this property
         * set to TRUE to make $this->validate available
         */
        private $_persistence = FALSE;
        
        /**
         * Which way you want to store captcha data
         * 
         * available ways: session, db, cache
         */
        private $_persistence_way = 'session';

        /**
	 * Constructor - Sets Captcha Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		$this->_dependence();
                $this->initialize($config);
		log_message('debug', "Captcha Class Initialized");
	}

	/**
	 * check if gd extension is exists
	 * 
	 * @access private
	 * @return void
	 */
	private function _dependence()
	{
		if ( !extension_loaded('gd'))
                {
                    show_error('Oops! Your PHP Interpreter May Be Missing GD extension.');
                }
	}

	/**
	 * init the captcha's configure items
	 *
	 * @access public
	 * @param array
	 * @return void
	 */
	public function initialize($config = array())
	{
		if ( ! empty($config))
		{
			foreach ($config as $key => $value) {
				$key = '_' . $key;
				$this->$key = $value;
			}
		}
                
                if ($this->_word != '')
                {
                        $this->_word_length = strlen($this->_word);
                }
                
                if ( ! @is_dir($this->_image_path))
                {
                        $this->_image_path = 'statics/captcha/';
                }
                
                if ( ! is_writable($this->_image_path))
                {
                        show_error('Oops! We don\'t have permission to write contents to path ' . $this->_image_path);
                }
                
                if ( ! file_exists($this->_font_path))
                {
                        $this->_font_path = 'statics/fonts/CONSOLA.TTF';
                }
	}

        public function create()
        {
		// -----------------------------------
		// Remove old images
		// -----------------------------------

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		$current_dir = @opendir($this->_image_path);

		while ($filename = readdir($current_dir))
		{
			if ($filename != "." and $filename != ".." and $filename != "index.html")
			{
				$name = str_replace(".jpg", "", $filename);

				if (($name + $this->_expiration) < $now)
				{
					@unlink($this->_image_path.$filename);
				}
			}
		}

		@closedir($current_dir);

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------
                if ($this->_word == '')
                {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$str = '';
			for ($i = 0; $i < $this->_word_length; $i++)
			{
				$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
			}

			$this->_word = $str;
                }

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------

		$angle	= ($this->_word_length >= 6) ? rand(-($this->_word_length-6), ($this->_word_length-6)) : 0;
		$x_axis	= rand(6, (360/$this->_word_length)-16);
		$y_axis = ($angle >= 0 ) ? rand($this->_image_height, $this->_image_width) : rand(6, $this->_image_height);

		// -----------------------------------
		// Create image
		// -----------------------------------

		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		if (function_exists('imagecreatetruecolor'))
		{
			$im = imagecreatetruecolor($this->_image_width, $this->_image_height);
		}
		else
		{
			$im = imagecreate($this->_image_width, $this->_image_height);
		}

		// -----------------------------------
		//  Assign colors
		// -----------------------------------

		$bg_color		= imagecolorallocate ($im, 255, 255, 255);
		$border_color	= imagecolorallocate ($im, 153, 102, 102);
		$text_color		= imagecolorallocate ($im, 26, 60, 168);
		$grid_color		= imagecolorallocate($im, 142, 204, 227);
		$shadow_color	= imagecolorallocate($im, 255, 240, 240);

		// -----------------------------------
		//  Create the rectangle
		// -----------------------------------

		ImageFilledRectangle($im, 0, 0, $this->_image_width, $this->_image_height, $bg_color);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------

		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0; $i < ($circles * $points) - 1; $i++)
		{
			$theta = $theta + $thetac;
			$rad = $radius * ($i / $points );
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta )) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------
                $use_font = ($this->_font_path != '' AND file_exists($this->_font_path) AND function_exists('imagettftext')) ? TRUE : FALSE;

                if ($use_font == FALSE)
                {
                        $x = rand(0, $this->_image_width/($this->_word_length/1.5));
                        $y = 0;
                }
                else
                {
                        $x = rand(0, $this->_image_width/($this->_word_length/1.5));
                        $y = $this->_font_size+2;
                }

                for ($i = 0; $i < $this->_word_length; $i++)
                {
                        if ($use_font == FALSE)
                        {
                                $y = rand(0 , $this->_image_height/2);
                                imagestring($im, $this->_font_size, $x, $y, substr($this->_word, $i, 1), $text_color);
                                $x += ($this->_font_size*2);
                        }
                        else
                        {
                                $y = rand($this->_image_height/2, $this->_image_height-3);
                                imagettftext($im, $this->_font_size, $angle, $x, $y, $text_color, $this->_font_path, substr($this->_word, $i, 1));
                                $x += $this->_font_size;
                        }
                }

		// -----------------------------------
		//  Create the border
		// -----------------------------------

		imagerectangle($im, 0, 0, $this->_image_width-1, $this->_image_height-1, $border_color);

		// -----------------------------------
		//  Generate the image
		// -----------------------------------

		$img_name = $now.'.jpg';

		ImageJPEG($im, $this->_image_path.$img_name);

		$img = "<img src=\"$this->_image_url$img_name\" width=\"$this->_image_width\" height=\"$this->_image_height\" style=\"border:0;\" alt=\" \" />";

		ImageDestroy($im);

		return array('word' => $this->_word, 'time' => $now, 'image' => $img);
        }
        
        /**
         * TODO: 
         * @param string $submited_captcha
         * @param boolean $destory
         */
        public function validate($submited_captcha, $destory = FALSE)
        {
                
        }
}
// END Captcha class

/* End of file Captcha.php */
/* Location: ./application/libraries/Captcha.php */