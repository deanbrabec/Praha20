<?php

class Simple_Slider {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version                 = '1.2.4';
	
	/**
	 * Array with default options
	 *
	 * @var array
	 */
	protected $_options             = array(
		'simple_slider' => array(
			'slideshow_config' => array(),
			'plugin_config' => array(
				'width'=>600,
				'height'=> 400,
				'slider_timeout' => 10,
				'slider_speed' => 1,
				'slider_effect' => 'scrollHorz'
			)
		)
		
	);
	
	/**
	 * Plugin work path
	 *
	 * @var string
	 */
	protected $_plugin_dir          = null;
	
	/**
	 * Settings url
	 *
	 * @var string
	 */
	protected $_settings_url        = null;


	
	/**
	 * Get option by setting name with default value if option is unexistent
	 *
	 * @param string $setting
	 * @return mixed
	 */
	protected function get_option($setting) {
	    if(is_array($this->_options[$setting])) {
	        $options = array_merge($this->_options[$setting], get_option($setting));
	    } else {
	        $options = get_option($setting, $this->_options[$setting]);
	    }

	    return $options;
	}
	
	/**
	 * Get array with options
	 *
	 * @return array
	 */
	private function get_options() {
		$options = array();
		
		// loop through default options and get user defined options
		foreach($this->_options as $option => $value) {
			$options[$option] = $this->get_option($option);
		}
		
		return $options;
	}
	
	/**
	 * Merge configuration array with the default one
	 *
	 * @param array $default
	 * @param array $opt
	 * @return array
	 */
	private function mergeConfArray($default, $opt) {
		foreach($default as $option => $values)	{
			if(!empty($opt[$option])) {
				$default[$option] = is_array($values) ? array_merge($values, $opt[$option]) : $opt[$option];
				$default[$option] = is_array($values) ? array_intersect_key($default[$option], $values) : $opt[$option];
			}
		}

		return $default;
    }
	
	/**
	 * Plugin installation method
	 */
	public function activate_simple_slider() {
		// record install time
		add_option('simple_slider_installed', time(), null, 'no');
				
		// loop through default options and add them into DB
		foreach($this->_options as $option => $value) {
			add_option($option, $value, null, 'no');	
		}
	}
	
	

	
	
}

?>