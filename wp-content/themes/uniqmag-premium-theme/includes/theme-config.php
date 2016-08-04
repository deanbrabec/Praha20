<?php
/**
 * Author Otange-Themes
 * http://wwww.different-themes.com
 */




/* -------------------------------------------------------------------------*
 * 								THEME MANAGEMENT							*
 * -------------------------------------------------------------------------*/
 
class DifferentThemesManagment {

	var $options = array();
	var $before_item_title = '<h2>';
	var $after_item_title = '</h2>';
	var $before_item = '<div class="row">';
	var $after_item = '</div>';
	private static $i = 0;
 
	function DifferentThemesManagment($themename, $themeslug, $type=false) {
		$this->themename=$themename;
		$this->themeslug=$themeslug;
		$this->type=$type;
	}

    private function i() {
        return ++self::$i;
    }		
    private function get_i() {
        return self::$i;
    }			
    private function set_i($val) {
        return self::$i=$val;
    }	

	function get_options() {
		return $this->options;
	}		

	function get_post_id() {
		global $post_id;
		return $post_id;
	}	

	function get_input_value($value, $default) {
		if(isset($value['input_value']) && $value['input_value'] && $value['home'] == "yes") {
			$input_value = $value['input_value'];
		} else {
			$input_value = $this->get_field_value($value['id'], $default);	
		}

		return $input_value;
	}	

	function add_options($option_arr) {
		foreach($option_arr as $option){
			$this->options[]=$option;
		}
	}

	function info_message($content) {
	?>
		<a href="javascript:{}" class="help"><img src="<?php echo esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.'ico-help-1.png');?>" /></a>
		<i class="popup-help popup-help-hidden trans-1">
			<a href="javascript:{}" class="close"></a>
			<?php echo balanceTags($content, true);?>
		</i>
	<?php
	}

	/* -------------------------------------------------------------------------*
	 * 							GET AMIN PAGE TYPE								*
	 * -------------------------------------------------------------------------*/

	function get_current_post_type() {
	  	global $post, $typenow, $current_screen;
		
	  	//we have a post so we can just get the post type from that
	  	if ( $post && $post->post_type )
	    	return $post->post_type;
	    
	  	//check the global $typenow - set in admin.php
	  	elseif( $typenow )
	    	return $typenow;
	    
	  	//check the global $current_screen object - set in sceen.php
	  	elseif( $current_screen && $current_screen->post_type )
	    	return $current_screen->post_type;
	  
	 	 //lastly check the post_type querystring
	  	elseif( isset( $_REQUEST['post_type'] ) )
	    	return sanitize_key( $_REQUEST['post_type'] );

		elseif (get_post_type(isset($_REQUEST['post'])))
	        return get_post_type($_REQUEST['post']);

	  	//we do not know the post type!
	  	return null;
	}

	/* -------------------------------------------------------------------------*
	 * 							CHECK AMIN PAGE TYPE								*
	 * -------------------------------------------------------------------------*/

	function page_type_check($value) {
		global $post_id;
		if(isset($value) && in_array($this->get_current_post_type(), $value) && !in_array('!blog', $value)) {
			return true;
		} elseif (isset($value) && in_array($this->get_current_post_type(), $value) && !in_array('blog', $value) && (in_array('!blog', $value) && $post_id != get_option('page_for_posts'))) {
			return true;
		} elseif (isset($value) && in_array('blog', $value) && $post_id == get_option('page_for_posts') && !in_array('!blog', $value)) {
			return true;
		}  elseif (isset($value) && !in_array($post_id,uniqmag_different_themes_get_page_array($value)) && in_array('blog', $value) && $post_id != get_option('page_for_posts') || in_array('!blog', $value) && $post_id != get_option('page_for_posts')) {
			return false;
		}  elseif (isset($value) && in_array($post_id,uniqmag_different_themes_get_page_array($value)) && in_array('blog', $value) && $post_id != get_option('page_for_posts') || in_array('!blog', $value) && $post_id != get_option('page_for_posts')) {
			return true;
		} elseif (isset($value) && in_array('!blog', $value) && $post_id == get_option('page_for_posts')) {
			return false;
		} elseif (isset($value) && in_array($post_id,uniqmag_different_themes_get_page_array($value))) {
			return true;
		} else {
			return false;
		}
	}
	/* -------------------------------------------------------------------------*
	 * 							CHECK AMIN PAGE TEMPLATE						*
	 * -------------------------------------------------------------------------*/

	function template_check($value) {
		global $post_id;
		if (!empty($value) && in_array($post_id,uniqmag_different_themes_get_page_array($value))) {
			return true;
		} else {
			return false;
		}
	}

	function print_heading($heading_text) {
		$theme_data = wp_get_theme();
		echo '<!-- BEGIN .main-wrapper -->
			<div class="main-wrapper clearfix">
			<!-- BEGIN .header -->
			<div class="header">
				<p>'.THEME_FULL_NAME.' <span>v'.$theme_data->Version.'</span></p>
				<div>
					<a href="#"><img src="'.esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL).'logo-ot-1.png" alt="Different Themes"/></a>
					<a href="http://www.themeforest.net/user/different-themes/portfolio?ref=different-themes" target="blank" class="more">'.$heading_text.'</a>
				</div>
			<!-- END .header -->
			</div>
			
			<form method="post" id="different-themes-options">';
		if ( function_exists('wp_nonce_field') ){
			wp_nonce_field('different-theme-update-options','different-theme-options');
		}
		echo '
		<div id="tabs">
		<!-- BEGIN .sidebar -->
		<div class="sidebar">
				<ul class="main-menu">';
					$i=0;
					foreach ($this->options as $value) {
						if($value['type']=='navigation'){
							echo '<li class="'.$value['slug'].'"><a href="'.esc_url('#tabs-'.$i).'" class="config_tab">'.$value['name'].'</a></li>';
							$i++;
						}
					}
		echo '</ul>		
			<!-- END .sidebar -->
			</div>

			';
					
				
	}
	
	function print_footer() {
	
		echo '
		
		</div>
		</form>
		<!-- END .main-wrapper -->
		</div>
		<div id="df-saved-content"></div>
';
	}
	
	function print_options(){
		$i=0;
		foreach ($this->options as $value) {
			$this->print_options_switch($value['type'], $value);
		}
	}
	
	function print_options_switch($switchValue, $arrayValue) {
		$i = self::get_i();
		switch ($switchValue) {
			case 'navigation':
				self::i();
			break;
			case 'tab':
				$this->print_tab($arrayValue, $i);
			break;
			case 'closetab':
				$this->print_closetab($arrayValue, $i);
			break;
			case 'meta_sub_navigation':
				$this->print_meta_sub_navigation($arrayValue, $i);
			break;
			case 'sub_navigation':
				$this->print_subnavigation($arrayValue, $i);
			break;
			case 'sub_tab':
				$this->print_subtab($arrayValue, $i);
			break;
			case 'meta_sub_tab':
				$this->print_meta_sub_tab($arrayValue, $i);
			break;
			case 'closesubtab':
				$this->print_closesubtab($arrayValue, $i);
			break;
			case 'homepage_set_test':
				$this->print_homepagesettest($arrayValue);
			break;
			case 'upload':
				$this->print_upload($arrayValue);
			break;
			case 'title':
				$this->print_title($arrayValue);
			break;
			case 'input':
				$this->print_input($arrayValue);
			break;
			case 'input_hidden':
				$this->print_input_hidden($arrayValue);
			break;
			case 'checkbox':
				$this->print_checkbox($arrayValue);
			break;
			case 'select':
				$this->print_select($arrayValue);
			break;
			case 'multiple_select':
				$this->print_multiple_select($arrayValue);
			break;
			case 'save':
				$this->print_save($arrayValue);
			break;
			case 'row':
				$this->print_row($arrayValue);
			break;
			case 'close':
				$this->print_close($arrayValue);
			break;
			case 'textarea':
				$this->print_textarea($arrayValue);
			break;
			case 'pages':
				$this->print_pages($arrayValue);
			break;
			case 'categories':
				$this->print_categories($arrayValue);
			break;
			case 'radio':
				$this->print_radio($arrayValue);
			break;
			case 'slide_order':
				$this->print_slide_order($arrayValue);
			break;
			case 'sidebar_order':
				$this->print_sidebar_order($arrayValue);
			break;
			case 'datepicker':
				$this->print_datepicker($arrayValue);
			break;
			case 'text':
				$this->print_text($arrayValue);
			break;
			case 'text_2':
				$this->print_text_2($arrayValue);
			break;
			case 'add_text':
				$this->print_add_text($arrayValue);
			break;
			case 'sidebar':
				$this->print_sidebar($arrayValue);
			break;
			case 'google_font_select':
				$this->print_google_font_select($arrayValue);
			break;
			case 'homepage_blocks':
				$this->print_homepage_blocks($arrayValue);
			break;
			case 'meta_block':
				$this->print_meta_block($arrayValue);
			break;
			case 'sidebar_select':
				$this->print_sidebar_select($arrayValue);
			break;
			case 'icon_select':
				$this->print_icon_select($arrayValue);
			break;
			case 'scroller':
				$this->print_scroller($arrayValue);
			break;
			case 'color':
				$this->print_color($arrayValue);
			break;
			case 'unique':
				$this->print_unique($arrayValue);
			break;
			case 'user':
				$this->print_user($arrayValue);
			break;
			case 'layer_slider_select':
				$this->print_layer_slider_select($arrayValue);
			break;
			case 'export_content':
				$this->print_export_content($arrayValue);
			break;
			case 'import_content':
				$this->print_import_content($arrayValue);
			break;
			case 'gallery_tab':
				$this->print_gallery_tab($arrayValue);
			break;
		}
	}

	
	function print_tab($value, $i){
		echo '

		<div id="tabs-'.($i-1).'" ><div class="content">';
	}		

	
	/**
	*	Prints a text.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "text",
	*		"text" => "Your text comes here",
	*	)
	*/
	function print_text($value){
		if ( isset ( $value['protected'][0]["id"] ) || isset ( $value['std'] ) ) {
			$protected_value = $this->get_field_value( $value['protected'][0]["id"], $value['std'] );
		}
		
		if ( isset ( $value['protected'][1]["id"] ) || isset ( $value['std'] ) ) {
			$protected_value_1 = $this->get_field_value( $value['protected'][1]["id"], $value['std'] );
		}
		
		if ( isset ( $value['id'] ) || isset ( $value['std'] ) ) {
			$input_value = $this->get_field_value( $value['id'], $value['std'] );
		}
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || (!isset($value['protected'][0]["id"]))) {

			
			echo '<div id="theme_documentation" style="min-height:300px;">
			<div style="margin-left:33px; margin-right:25px;">
			'.$value['text'].'	
			</div>								
			</div>';
		}
	}	
	
	/**
	*	Prints a text.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "text_2",
	*		"text" => "Your text comes here",
	*	)
	*/
	function print_text_2($value){
		if ( isset ( $value['protected'][0]["id"] ) || isset ( $value['std'] ) ) {
			$protected_value = $this->get_field_value( $value['protected'][0]["id"], $value['std'] );
		}
		
		if ( isset ( $value['protected'][1]["id"] ) || isset ( $value['std'] ) ) {
			$protected_value_1 = $this->get_field_value( $value['protected'][1]["id"], $value['std'] );
		}
		
		if ( isset ( $value['id'] ) || isset ( $value['std'] ) ) {
			$input_value = $this->get_field_value( $value['id'], $value['std'] );
		}
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || (!isset($value['protected'][0]["id"]))) {

			
			echo '<div id="theme_documentation" style="margin-bottom:10px;">
			<div style="margin-left:0px; margin-right:25px;">
			'.$value['text'].'	
			</div>								
			</div>';
		}
	}
	/**
	*	Prints a layer_slider_select box.
	*/
	function print_layer_slider_select($value) {
	
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);
			
			// Get WPDB Object
			global $wpdb;

			// Table name
			$table_name = $wpdb->prefix . "layerslider";
			
			// Get sliders
			$sliders = $wpdb->prepare("SELECT * FROM %s 
										WHERE flag_hidden = '0' 
										AND flag_deleted = '0' 
										ORDER BY id ASC 
										LIMIT 200", $table_name);
			$sliders = $wpdb->get_results($sliders);

			
		?>
		<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix <?php echo esc_attr($value['class']);?>"><?php } else { ?>
			<div class="input-item-full-width clearfix <?php echo esc_attr($value['class']);?>">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
		<?php
			if (isset($value['info'])) {
				$this->info_message($value['info']);
			}
		?>
			<span class="select">
				<select name="<?php echo esc_attr($value['id']);?>" class="page-builder-input">
				<?php if(!empty($sliders)) : ?>
				<?php foreach($sliders as $key => $item) : ?>
				<?php $name = empty($item->name) ? 'Unnamed' : $item->name; ?>
				<?php if($input_value == $item->id) { $selected='selected="selected"'; } else { $selected=''; } ?>
					<option value="<?php echo esc_attr($item->id); ?>" <?php echo esc_html($selected);?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if(empty($sliders)) : ?>
					<option value=""><?php esc_html_e("You didn't create a slider yet.",'uniqmag'); ?></option>
				<?php endif; ?>
				
				</select>
			</span>
		</div>							
		<?php
	

		}
	}
		
	/**
	*	Prints a sub navigation.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "sub_navigation",
	*		"subname"=>array(
	*			array("slug"=>"documentation", "name"=>"Documentation")
	*		)
	*	)
	*/
	function print_meta_sub_navigation($value, $i){
		global $post_id;

		if($value['subname']){
			echo '<div class="otpost-tabbed-wrapper meta_sub_tabs">';
			echo '<div class="tabs clearfix">';
			echo '<ul class="otpost-tabbed-menu">';
			foreach($value['subname'] as $subname ){
				if(isset($subname['page_type']) && $this->page_type_check($subname['page_type'])==true || !isset($subname['page_type'])) { //meta box check
					if((isset($subname['skip_templates']) && $this->template_check($subname['skip_templates'])==false) || !isset($subname['skip_templates'])) { //meta box check
						if(isset($subname['hide_in']) && (in_array($this->get_current_post_type(),$subname['hide_in']))) {
						} else {
							echo '<li><a href="'.esc_url('#sub_tabs-'.($i-1).'-'.$subname['slug']).'"><i class="dashicons '.$subname['icon'].'"></i>&nbsp;&nbsp;'.$subname['name'].'</a></li>';
						}
					}
				}
			}
			echo '</ul></div>';
	 	}
	}

	function print_meta_sub_tab($value, $i){
		global $post_id;
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
				if(isset($value['hide_in']) && (in_array($this->get_current_post_type(),$value['hide_in']))) {

				} else {
					echo '<div id="sub_tabs-'.($i-1).'-'.$value['slug'].'" class="otpost-tabbed-blocks">';
				}
			}
		}
	}			
	/**
	*	Prints a sub navigation.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "sub_navigation",
	*		"subname"=>array(
	*			array("slug"=>"documentation", "name"=>"Documentation")
	*		)
	*	)
	*/
	function print_subnavigation($value, $i){
		if($value['subname']){
			echo '<div class="sub_tabs">';
			echo '<div class="tabs clearfix">';
			echo '<ul>';
			foreach($value['subname'] as $subname){
				echo '<li><a href="'.esc_url('#sub_tabs-'.($i-1).'-'.$subname['slug']).'" class="config_stab" >'.$subname['name'].'</a></li>';
			}
			echo '</ul></div>';
	 	}
	}
	
	function print_subtab($value, $i){
		echo '<div id="sub_tabs-'.($i-1).'-'.$value['slug'].'">';
	}	

	
	/**
	*	Check if homepage is set.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "homepage_set_test",
	*		"title" => "Set up Your Homepage and post page!",
	*		"desc" => "	<p><b>You have not selected the correct template page for homepage.</b></p>
	*		<p>Please make sure, you choose template \"Homepage\".</p>
	*		<br/>
	*		<ul>
	*			<li>Current front page: <a href='".esc_url(get_permalink(get_option('page_on_front')))."'>".get_the_title(get_option('page_on_front'))."</a></li>
	*			<li>Current blog page: <a href'".esc_url(get_permalink(get_option('page_for_posts')))."'>".get_the_title(get_option('page_for_posts'))."</a></li>
	*		</ul>",
	*		"desc_2" => "<p><b>You have NOT enabled homepage.</b></p>
	*		<p>To use custom homepage, you must first create two <a href='".esc_url(home_url('/'))."/wp-admin/post-new.php?post_type=page'>new pages</a>, and one of them assign to \"<b>Homepage</b>\" template.Give each page a title, but avoid adding any text.</p>
	*		<p>Then enable homepage  in <a href='".esc_url(home_url('/'))."/wp-admin/options-reading.php'>wordpress reading settings</a> (See \"Front page displays\" option). Select your previously created pages from both dropdowns and save changes.</p>"
	*	)
	*/
	function print_homepagesettest($value){
		echo balanceTags($this->before_item_title.$value['title'].$this->after_item_title,true);
						$homepage = get_option('show_on_front');
						if(get_option( 'page_on_front')) {
							$meta = get_post_custom_values("_wp_page_template",get_option( 'page_on_front'));
						}
						
						if($homepage == "page" && $meta[0] == "template-homepage.php") { global $has_homepage; $has_homepage=true; } else { $has_homepage=false; }
						
						if($has_homepage) {
							echo '<ul style="margin:0 0 0 33px;">
										<li>Front page: <a href="'.esc_url(get_permalink(get_option("page_on_front"))).'">'.get_the_title(get_option("page_on_front")).'</a></li>
										<li>Blog page: <a href="'.esc_url(get_permalink(get_option("page_for_posts"))).'">'.get_the_title(get_option("page_for_posts")).'</a></li>
								</ul>';						
						} elseif ($homepage == "page") {
							echo '<div id="theme_documentation">
									<div style="margin-left:33px; margin-right:25px;">
											'.$value['desc'].'
										</div>
									</div>';
						} else {
							echo '<div id="theme_documentation">
										<div style="margin-left:33px; margin-right:25px;">
											'.$value['desc_2'].'								
										</div>
									</div>';
						}


	}	
	/**
	*	Check if homepage is set.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*	"type" => "homepage_blocks",
	*	"title" => "Homepage Blocks:",
	*	"id" => $different_themes_managment->themeslug."_homepage_blocks",
	*	"blocks" => array(
	*		array(
	*			"title" => "Quote Text",
	*			"type" => "homepage_quote_text",
	*			"options" => array(
	*				array( "type" => "textarea", "id" => $different_themes_managment->themeslug."_homepage_quote_text", "sub_title" => "Text:" ),
	*			),
	*		),
	*		array(
	*			"title" => "Main Article News Block",
	*			"type" => "homepage_main_news_block",
	*			"options" => array(
	*				array(
	*					"type" => "categories",
	*					"id" => $different_themes_managment->themeslug."_homepage_main_news_block_post",
	*					"taxonomy" => "category",
	*					"title" => "Set News Category"
	*				),
	*				array(
	*					"type" => "categories",
	*					"id" => $different_themes_managment->themeslug."_homepage_main_news_block_gal",
	*					"taxonomy" => "gallery-cat",
	*					"title" => "Set Gallery Category"
	*				),
	*	
	*			),
	*		)
	*	)
	*
	*/
	function print_homepage_blocks($value){
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			
			//pagebuilder saved layout 
			$pagebuilder_layout = get_post_meta( $this->get_post_id(), "_".THEME_NAME."_pagebuilder_layout", true );

			//sidebar settings
			$var = array(
				"title" => esc_html__('Sidebar','uniqmag'),
				"default" => array(
					array('', esc_html__('Default','uniqmag')),
				),
				"id" => "_".$this->themeslug."_layout_sidebar_select",
				"home" => "yes"
			);
					
			ob_start();
			$this->print_sidebar_select($var);
			$layoutSidebar = ob_get_contents();
			ob_end_clean();

			//background settings
			$var = array(
				"title" => esc_html__('Background Color:','uniqmag'),
				"options" => array(
					array("slug"=>"F9F9F9", "name"=>esc_html__("Gray",'uniqmag')), 
					array("slug"=>"ffffff", "name"=>esc_html__("White",'uniqmag')), 
				),
				"id" => "highcriteria_background_color",
				"home" => "yes"
			);
					
			ob_start();
			$this->print_select($var);
			$columnBackgroundSelect = ob_get_contents();
			ob_end_clean();


			echo balanceTags($this->before_item).'



					<div id="pagebuilder-block-popup" title="'.esc_html__("Columns & Layouts",'uniqmag').'" style="display:none;">
						<div class="note" style="display:none;">'.esc_html__("Block Added..",'uniqmag').'</div>

						<h2 class="layout-title">'.esc_html__("Layouts",'uniqmag').'</h2>
						<ul class="columns block-columns clearfix inactive-columns" id="available-homepage-layouts">
							<li class="inactive-column column layout" data-paragraph-class="content-block has-sidebar" data-id="homepageLayout_1">
								<div class="paragraph-row">
									<div class="column4 column-content is-sidebar" data-type="column4">'.$layoutSidebar.'</div>
									<div class="column8 column-content is-content" data-type="column8"><ul class="pagebuilder-column-container"></ul><a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a></div>
								</div>
							</li>
							<li class="inactive-column column layout" data-paragraph-class="content-block has-sidebar" data-id="homepageLayout_2">
								<div class="paragraph-row">
									<div class="column8 column-content is-content" data-type="column8"><ul class="pagebuilder-column-container"></ul><a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a></div>
									<div class="column4 column-content is-sidebar" data-type="column4">'.$layoutSidebar.'</div>
								</div>
							</li>
							<!--
							<li class="inactive-column column layout" data-paragraph-class="content-block" data-id="homepageLayout_3">
								<div class="paragraph-row">
									<div class="column2 column-content is-sidebar" data-type="column2">'.$layoutSidebar.'</div>
									<div class="column7 column-content is-content" data-type="column7 double"><ul class="pagebuilder-column-container"></ul><a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a></div>
									<div class="column3 column-content is-sidebar" data-type="column3">'.$layoutSidebar.'</div>
								</div>
							</li>
							<li class="inactive-column column layout" data-paragraph-class="content-block" data-id="homepageLayout_4">
								<div class="paragraph-row">
									<div class="column7 column-content is-content" data-type="column7 double"><ul class="pagebuilder-column-container"></ul><a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a></div>
									<div class="column2 column-content is-sidebar" data-type="column2">'.$layoutSidebar.'</div>
									<div class="column3 column-content is-sidebar" data-type="column3">'.$layoutSidebar.'</div>
								</div>
							</li>
							<li class="inactive-column column layout" data-paragraph-class="content-block" data-id="homepageLayout_5">
								<div class="paragraph-row">
									<div class="column3 column-content is-sidebar" data-type="column3">'.$layoutSidebar.'</div>
									<div class="column2 column-content is-sidebar" data-type="column2">'.$layoutSidebar.'</div>
									<div class="column7 column-content is-content" data-type="column7 double"><ul class="pagebuilder-column-container"></ul><a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a></div>
								</div>
							</li>
							-->
						</ul>

						<h2>'.esc_html__("Columns",'uniqmag').'</h2>

						<ul class="columns block-columns clearfix inactive-columns" id="available-homepage-columns">';
							$inactive_column_class = "inactive-column column";

							if ( isset($value['background_settings']) ) {
								$inactive_column_class.= " background-settings";
							}

							echo'<li class="'.esc_attr($inactive_column_class).'" data-paragraph-class="content-block" data-id="homepageColumn_0">';
								
								if(isset($value['background_settings'])) {
									echo'<a href="#" alt="'.esc_attr__("Background Settings",'uniqmag').'"  title="'.esc_attr__("Background Settings",'uniqmag').'" class="column-background"><img src="'.esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.'btn-columns-4.png').'"></a>';
								}
							echo 	'<div class="paragraph-row">
									<div class="column12 column-content" data-type="column12">';
										if(isset($value['background_settings'])) {
										
										echo '<div class="pagebuilder-column-background block-content-settings" title="'.esc_html__("Column Background Settings",'uniqmag').'" style="display:none; position: absolute !important;">
												<div class="ui-dialog-titlebar">
													<span class="ui-dialog-title">'.esc_html__("Column Background Settings",'uniqmag').'</span>
													<a href="javascript:void(0)" class="close-seetings-box">'.esc_html__("Close",'uniqmag').'</a>
												</div>';

												foreach($value['background_settings']['options'] as $blockOption) {
													$this->print_options_switch($blockOption["type"],$blockOption);
												}

										echo '
											<input type="submit" value="'.esc_attr__("Save",'uniqmag').'" data-loading="'.esc_html__("Saving..",'uniqmag').'" data-saved="'.esc_html__("Save",'uniqmag').'" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>
										</div>';
										}
										echo '<ul class="pagebuilder-block-container"></ul>
									</div>
								</div>
							</li>
							<li class="inactive-column column" data-paragraph-class="content-block" data-id="homepageColumn_1">
								<div class="paragraph-row">
									<div class="column6 column-content" data-type="column6"><ul class="pagebuilder-block-container"></ul></div>
									<div class="column6 column-content" data-type="column6"><ul class="pagebuilder-block-container"></ul></div>
								</div>
							</li>
							<!--
							<li class="inactive-column column" data-paragraph-class="content-block" data-id="homepageColumn_2">
								<div class="paragraph-row">
									<div class="column4 column-content" data-type="column4"><ul class="pagebuilder-block-container"></ul></div>
									<div class="column8 column-content" data-type="column8"><ul class="pagebuilder-block-container"></ul></div>
								</div>
							</li>
							<li class="inactive-column column" data-paragraph-class="content-block" data-id="homepageColumn_3">
								<div class="paragraph-row">
									<div class="column8 column-content" data-type="column8"><ul class="pagebuilder-block-container"></ul></div>
									<div class="column4 column-content" data-type="column4"><ul class="pagebuilder-block-container"></ul></div>
								</div>
							</li>
							<li class="inactive-column column" data-paragraph-class="content-block" data-id="homepageColumn_4">
								<div class="paragraph-row">
									<div class="column4 column-content" data-type="column4"><ul class="pagebuilder-block-container"></ul></div>
									<div class="column4 column-content" data-type="column4"><ul class="pagebuilder-block-container"></ul></div>
									<div class="column4 column-content" data-type="column4"><ul class="pagebuilder-block-container"></ul></div>
								</div>
							</li>
							-->
						</ul>
					</div>
					<h2>'.$this->before_item_title.$value['title'].$this->after_item_title.'</h2>
					<ul class="blocks block-available clearfix inactive-blocks" id="available-homepage-blocks">';
						foreach ($value['blocks'] as $block) {
						echo '
						<li class="inactive-block component" rel="'.$block['type'].'" title="'.$block['title'].'">
							<div class="blocks-content clearfix" title="'.$block['title'].'">
								<a href="javascript:{}" class="button move" title="'.$block['title'].'">'.esc_html__("Edit",'uniqmag').'</a>
								<a href="javascript:{}" class="button edit">'.esc_html__("Edit",'uniqmag').'</a>';
								if(isset($block['image'])) {
									echo '<img src="'.esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.$block['image']).'" alt="'.esc_attr($block['title']).'" class="feature-img">';
								}
								

						echo	'<strong>'.$block['title'].'</strong>';
						if(isset($block['description'])) {
							echo	'<span>'.$block['description'].'</span>';	
						}
						
						echo	'<div class="block-content-settings" style="display:none;">
									<div class="ui-dialog-titlebar">
										<span class="ui-dialog-title">'.$block['title'].'</span>
										<a href="javascript:void(0)" class="close-seetings-box">'.esc_html__("Close",'uniqmag').'</a>
									</div>';
										foreach($block['options'] as $blockOption) {
											$this->print_options_switch($blockOption["type"],$blockOption);
										}
								echo '
									<input type="submit" value="'.esc_html__("Save",'uniqmag').'" data-loading="'.esc_html__("Saving..",'uniqmag').'" data-saved="'.esc_html__("Save",'uniqmag').'" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>
								</div>
							</div>
						</li>
						';
							
						}
				echo'
					</ul>';
					?>

					<div class="message-1">
						<?php esc_html_e("To add content to your Hompage, drag &amp; drop blocks from Available to Active area ",'uniqmag');?>
					</div>

					<h2><?php esc_html_e("Active homepage blocks",'uniqmag');?></h2>
						<div class="pagebuilder-blocks-wrapper">
							<ul class="settings ui-layout-center blocks block-active clearfix ui-droppable pagebuilder-blocks" data-fields="<?php echo (isset($pagebuilder_layout->fieldCount)) ? $pagebuilder_layout->fieldCount : 0 ;?>" style="min-height:100px;" id="active-homepage-blocks">

						<?php
								//print_r($pagebuilder_layout);
								if($pagebuilder_layout) {
									//foreach columns
									foreach ($pagebuilder_layout->columnRows as $columRows) {
										$active_column_class = "active-column column";

										if ( isset($v['background_settings']) && (strpos($columRows->row,'homepageLayout') == false) && $columRows->row=="homepageColumn_0") {
											$active_column_class.= " background-settings";
										}

										if ((strpos($columRows->row,'homepageLayout') !== false)) { 
											$active_column_class.= " layout";
										}

										
						?>
									<li class="<?php echo esc_attr($active_column_class);?>" data-id="<?php echo esc_attr($columRows->row);?>" data-paragraph-class="<?php echo esc_attr($columRows->row_class);?>">
										<?php if (isset($v['background_settings']) && (strpos($columRows->row,'homepageLayout') == false) && $columRows->row=="homepageColumn_0") { ?>
											<a href="#" alt="<?php esc_attr_e("Background Settings",'uniqmag');?>"  title="<?php esc_attr_e("Background Settings",'uniqmag');?>" class="column-background"><img src="<?php echo esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.'btn-columns-4.png');?>"></a>
										<?php } ?>

										<div class="paragraph-row">

						<?php
										//foreach column rows
										$rowCount = 0;
										foreach ($columRows->columns as $row) {

						?>
										<div class="<?php if(isset($row->columnID)) { echo esc_attr($row->columnID); } else { echo esc_attr($row->layoutID); } ?> column-content<?php if (isset($row->columnID) && (strpos($columRows->row,'homepageLayout') !== false) && ($row->columnID==="column4" ||$row->columnID==="column3" || $row->columnID==="column2")) {?> is-sidebar<?php } else if(strpos($columRows->row,'homepageLayout') !== false) { ?> is-content<?php } ?>" data-type="<?php if(isset($row->columnID)) { echo esc_attr($row->columnID); } else { echo esc_attr($row->layoutID); } ?>">
						<?php if (isset($v['background_settings']) && (strpos($columRows->row,'homepageLayout') == false) && $columRows->row=="homepageColumn_0") { ?>
											<div class="pagebuilder-column-background block-content-settings" title="<?php esc_html_e("Column Background Settings",'uniqmag');?>" style="display:none; position: absolute !important;">
												<div class="ui-dialog-titlebar">
													<span class="ui-dialog-title"><?php esc_html_e("Column Background Settings",'uniqmag');?></span>
													<a href="javascript:void(0)" class="close-seetings-box"><?php esc_html_e("Close",'uniqmag');?></a>
												</div>
				<?php


												//foreach column background settings
												foreach($this->options as $v) {
													if( $v['type'] == "homepage_blocks" ) {
														$n=0;
														if( $v['background_settings']['type'] == 'column_background_settings' ) {
															foreach($v['background_settings']['options'] as $vv) {
																if(isset($vv['id'])) {
																	
																	foreach($row->contentBlocksSettings as $key => $value) {
																		//check if the names are similar
																		if (strpos($key,$vv['id']) !== false) {
																			$v['background_settings']['options'][$n]['id'] = $key;

																			if(!is_array($value)) {
																				$v['background_settings']['options'][$n]['input_value'] = addslashes($value);
																			} else {
																				$v['background_settings']['options'][$n]['input_value'] = $value;
																			}
																			$opt = $vv['type'];
																			$optType =$v['background_settings']['options'][$n];
																			
																			$this->print_options_switch($opt,$optType);
																			break;	
																		}
																	}
																}
																$n++;
															}
														}
													}
												}
												
													
				?>									
												
												<input type="submit" value="<?php esc_attr_e("Save",'uniqmag');?>" data-loading="<?php esc_html_e("Saving..",'uniqmag');?>" data-saved="<?php esc_html_e("Save",'uniqmag');?>" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>
											</div>
						<?php } ?>
						<?php
											if ((strpos($columRows->row,'homepageLayout') === false) && !isset($row->layoutID)) {
						?>

												<ul class="pagebuilder-block-container">
						<?php

													//foreach column row blocks
													foreach ($row->contentBlocks as $rowBlock) {
														//foreach block details
														foreach($this->options as $v) {
															if( $v['type'] == "homepage_blocks" ) {
																foreach($v['blocks'] as $vv) {
																	if( $vv['type'] == $rowBlock->blocksContentName ) {
																		$optTitle = $vv['title'];
																		$description = $vv['description'];
																		$image = $vv['image'];
																	}
																}
															}
														}
						?>
													<li class="active-block component dropped" rel="<?php echo esc_attr($rowBlock->blocksContentName);?>" style="opacity: 1; z-index: 0; ">
														<div class="blocks-content clearfix">
															<a href="javascript:{}" class="button edit"><?php esc_html_e("Edit",'uniqmag');?></a>
															<?php if(isset($image)) { ?>
																<img src="<?php echo esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.$image);?>" alt="<?php echo esc_attr($optTitle);?>" class="feature-img">
															<?php } ?>
															<div></div>
															<strong><?php echo esc_html($optTitle);?></strong>
															<?php if(isset($description)) { ?>
																<span><?php echo esc_html($description);?></span>
															<?php } ?>
															<div class="block-content-settings" style="display:none;">
																<div class="ui-dialog-titlebar">
																	<span class="ui-dialog-title"><?php echo esc_html($optTitle);?></span>
																	<a href="javascript:void(0)" class="close-seetings-box"><?php esc_html_e("Close",'uniqmag');?></a>
																</div>
						<?php

																//foreach column row block fields
																foreach($this->options as $v) {
																	if( $v['type'] == "homepage_blocks" ) {
																		foreach($v['blocks'] as $vv) {
																			if( $vv['type'] == $rowBlock->blocksContentName ) {
																				$n=0;
																				foreach($vv['options'] as $vvv) {
																					if(isset($vvv['id'])) {

																						foreach($rowBlock->blocksContent as $key => $value) {
																							//check if the names are similar
																							if (strpos($key,$vvv['id']) !== false) {
																								$vv['options'][$n]['id'] = $key;
																								if(!is_array($value)) {
																									$vv['options'][$n]['input_value'] = addslashes($value);
																								} else {
																									$vv['options'][$n]['input_value'] = $value;
																								}
																								
																								$opt = $vvv['type'];
																								$optType =$vv['options'][$n];
																								$this->print_options_switch($opt,$optType);
																								break;	
																							}
																						}
																					}
																				$n++;
																				}
																			}
																		}
																	}
																}
							?>									<input type="submit" value="<?php esc_attr_e("Save",'uniqmag');?>" data-loading="<?php esc_html_e("Saving..",'uniqmag');?>" data-saved="<?php esc_html_e("Save",'uniqmag');?>" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>
															</div>
														</div>
													</li>
						<?php	
													}
						?>
												</ul>
						<?php 
										
											}

											if ((strpos($columRows->row,'homepageLayout') !== false) && isset($row->columnID) && ($row->columnID=="column4" ||$row->columnID=="column3" || $row->columnID=="column2")){ 
												//get the selected sidebar val
												if(isset($columRows->columns[$rowCount])) {
													$selectedSidebar = $columRows->columns[$rowCount]->contentBlocks[0]->SidebarName;
												}

												$var = array(
													"title" => esc_html__('Sidebar','uniqmag'),
													"default" => array(
														array('', esc_html__('Default','uniqmag')),
													),
													"id" => "_".$this->themeslug."_layout_sidebar_select",
													"home" => "yes",
													"input_value" => $selectedSidebar
												);
														
												ob_start();
												$this->print_sidebar_select($var);
												$layoutSidebar = ob_get_contents();
												ob_end_clean();

												//pintr the sider select
												echo balanceTags($layoutSidebar, true);

											} 
											if ((strpos($columRows->row,'homepageLayout') !== false) && isset($row->layoutID)){ 
						?>
												<ul class="pagebuilder-column-container">
													<?php
														//print_r($row);
														//foreach layoutRows

														foreach ($row->layoutRows as $layoutRows) {
													?>
														<li class="active-column column" data-id="<?php echo esc_attr($layoutRows->row);?>">

															<div class="paragraph-row">
																<?php
																	//foreach columns
																	foreach ($layoutRows->layoutColumns as $layoutColumns) {
																?>
																	<div class="<?php if(isset($layoutColumns->columnID)) { echo esc_attr($layoutColumns->columnID); } ?> column-content" data-type="<?php if(isset($layoutColumns->columnID)) { echo esc_attr($layoutColumns->columnID); } ?>">
																		<ul class="pagebuilder-block-container">
																		<?php
																			//foreach column row blocks
																			foreach ($layoutColumns->contentBlocks as $rowBlock) {
																				//foreach block details
																				foreach($this->options as $v) {
																					if( $v['type'] == "homepage_blocks" ) {
																						foreach($v['blocks'] as $vv) {
																							if( $vv['type'] == $rowBlock->blocksContentName ) {
																								$optTitle = $vv['title'];
																								$description = $vv['description'];
																								$image = $vv['image'];
																							}
																						}
																					}
																				}
																		?>
																				<li class="active-block component dropped" rel="<?php echo esc_attr($rowBlock->blocksContentName);?>" style="opacity: 1; z-index: 0; ">
																					<div class="blocks-content clearfix">
																						<a href="javascript:{}" class="button edit"><?php esc_html_e("Edit",'uniqmag');?></a>
																						<?php if(isset($image)) { ?>
																							<img src="<?php echo esc_url(UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL.$image);?>" alt="<?php echo esc_attr($optTitle);?>" class="feature-img">
																						<?php } ?>
																						<div></div>
																						<strong><?php echo esc_html($optTitle);?></strong>
																						<?php if(isset($description)) { ?>
																							<span><?php echo esc_html($description);?></span>
																						<?php } ?>
																						<div class="block-content-settings" style="display:none;">
																							<div class="ui-dialog-titlebar">
																								<span class="ui-dialog-title"><?php echo esc_html($optTitle);?></span>
																								<a href="javascript:void(0)" class="close-seetings-box"><?php esc_html_e("Close",'uniqmag');?></a>
																							</div>
																		<?php
																						//print_r($rowBlock);
																						//foreach column row block fields
																						foreach($this->options as $v) {
																							if( $v['type'] == "homepage_blocks" ) {
																								foreach($v['blocks'] as $vv) {
																									if( $vv['type'] == $rowBlock->blocksContentName ) {
																										$n=0;
																										foreach($vv['options'] as $vvv) {
																											if(isset($vvv['id'])) {
																												foreach($rowBlock->blocksContent as $key => $value) {
																													//check if the names are similar
																													if (strpos($key,$vvv['id']) !== false) {
																														$vv['options'][$n]['id'] = $key;
																														if(!is_array($value)) {
																															$vv['options'][$n]['input_value'] = html_entity_decode($value);
																														} else {
																															$vv['options'][$n]['input_value'] = $value;
																														}
																														
																														$opt = $vvv['type'];
																														$optType =$vv['options'][$n];
																														$this->print_options_switch($opt,$optType);
																														break;	
																													}
																												}
																											}
																										$n++;
																										}
																									}
																								}
																							}
																						}
																			?>		
																						<input type="submit" value="<?php esc_attr_e("Save",'uniqmag');?>" data-loading="<?php esc_html_e("Saving..",'uniqmag');?>" data-saved="<?php esc_html_e("Save",'uniqmag');?>" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>
																						</div>
																					</div>
																				</li>
																		<?php	
																			}
																		?>
																		</ul>
																	</div>

																<?php 
																	}				
																	
																?>	
															</div>
														</li>	
													<?php 
														} 
													?>
												</ul>
						<?php
											} 
						?>
						<?php
											if ((strpos($columRows->row,'homepageLayout') !== false) && isset($row->layoutID)) {
						?>
												<a href="javascript:void(0);" class="pagebuilder-block-popup-open"><?php esc_html_e("Click to add block layout",'uniqmag');?></a>
						<?php
											}
						?>
										</div>
						<?php
											$rowCount++;
										}

						?>
									</div>
								</li>
						<?php
									}
								}
									echo'
										
										</ul>
										<div class="builder-actions">
											<div id="pagebuilder-block-remover">'.esc_html__("Drag block here to delete it",'uniqmag').'</div>
											<a href="javascript:void(0);" class="pagebuilder-block-popup-open">'.esc_html__("Click to add block layout",'uniqmag').'</a>
										</div>
									</div>
									<h2>
										'.esc_html__("Pagebuilder Import/Export",'uniqmag').'
									</h2>

	';
				echo ''.$this->print_export_content(array(
									"type" => "export_content",
									"title" => esc_html__("Export Settings",'uniqmag'),
									"section" => "pagebuilder",
									"id" => THEME_NAME."_export")
								);				
				echo ''.$this->print_import_content(array(
									"type" => "import_content",
									"title" => esc_html__("Import Settings",'uniqmag'),
									"section" => "pagebuilder",
									"id" => THEME_NAME."_import")
								);



				echo ''.$this->after_item;
				echo '<input type="submit" value="'.esc_attr__("Save",'uniqmag').'" data-loading="'.esc_attr__("Saving..",'uniqmag').'" data-saved="'.esc_attr__("Save",'uniqmag').'" id="df-submit-home" class="button button-primary button-large" style="float:right;"/>	';
		}
	}	


	/**
	*	
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------

	*
	*/
	function print_meta_block($value){
		echo '<input type="hidden" name="sticky_meta_box_nonce" value="', esc_attr(wp_create_nonce("different-themes")), '" />';
		
		foreach ($value['blocks'] as $block) {
			foreach($this->options as $v) {
				if( $v['type'] == "meta_block" ) {
					foreach($v['blocks'] as $vv) {
						$n=0;
						if(isset($vv['options'])){
							foreach($vv['options'] as $vvv) {
								
								$opt = $vvv['type'];
								$optType =$vv['options'][$n];
								$this->print_options_switch($opt,$optType);	
							
							$n++;
							}
						}
					}
				}
			}
		}
	}
	
	function print_unique($value){
		echo '<div id="unique-block" style="display:none;">&nbsp;</div>';
	}

	/**
	*	Prints a export field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "export",
	*		"title" => "Export Theme Options",
	*		"id" => $different_themes_managment->themeslug."_export"
	*	)
	*/
	function print_export_content($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);
			

		?>

		<div class="input-item-full-width clearfix">
			<label><?php echo esc_html($value['title']);?></label>
				<span>
					<div class="export">
						<span id="<?php echo esc_attr($value['id']);?>" class="action export-content" data-type="<?php echo esc_attr($value['section']);?>" style="width:100px; float:right; z-index:500;"><?php esc_html_e("Export",'uniqmag');?></span>
					</div>
				</span>
			</div>
		<?php 
	
		}
	}		

	/**
	*	Prints a export field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "import",
	*		"title" => "Export Theme Options",
	*		"id" => $different_themes_managment->themeslug."_import"
	*	)
	*/
	function print_import_content($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);
		

		?>

		<div class="input-item-full-width clearfix">
			<label><?php echo esc_html($value['title']);?></label>
			<span>
				<div class="export">
					<span id="<?php echo esc_attr($value['id']);?>_button" class="action import-content" data-type="<?php echo esc_attr($value['section']);?>" style="width:100px; float:right; z-index:500;"><?php esc_html_e("Import",'uniqmag');?></span>
					<script type="text/javascript">
						jQuery(document).ready(function($){ loadUploader(jQuery("span#<?php echo esc_attr($value['id']);?>_button"), document.URL+'&df-export=upload&df-export-type=<?php echo esc_attr($value['section']);?>');});
					</script>
				</div>
			</span>
		</div>
		<?php 
	
		}
	}	

	
	/**
	*	Prints a upload field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "upload",
	*		"title" => "Add logo image",
	*		"info" => "Suggested image size is 166x28px",
	*		"id" => $different_themes_managment->themeslug."_logo"
	*	)
	*/
	function print_upload($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					$input_value = $this->get_input_value($value, $default);
			

				?>
				<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"<?php if(isset($value['compare_value'])) { echo ' data-compare-value="'.esc_attr($value['compare_value']).'"'; } ?>><?php } else { ?>
					<div class="input-item-full-width df-upload-panel-block clearfix"<?php if(isset($value['compare_value'])) { echo ' data-compare-value="'.esc_attr($value['compare_value']).'"'; } ?>>
				<?php } ?>
						<label><?php echo esc_html($value['title']);?></label>
				<?php
					if (isset($value['info'])) {
						$this->info_message($value['info']);
					}	
				?>
						<span>
							<div class="uploader-photo-wrapper">
								<div class="uploader">
									<input type="text" name="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" class="page-builder-input upload df-upload-field" style="width:199px;  <?php if( isset($value['home']) && $value['home'] == "yes" ) { ?>margin-left:483px;<?php } ?>" id="<?php echo esc_attr($value['id']);?>" />
									<span id="<?php echo esc_attr($value['id']);?>_button" class="action btn-upload df-upload-button" style="width:100px; float:right; z-index:500;"><?php esc_html_e("Choose File",'uniqmag');?></span>
								</div>

								<div class="uploader-photo<?php if($input_value != "") { echo " active"; } ?>">
									<span>
										<a href="#" class="delete">
											<img src="<?php echo UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL;?>delete.png" alt="<?php esc_html_e('Delete','uniqmag');?>">
										</a>
										<?php if($input_value != "") { ?>
											<?php 
												$image = uniqmag_different_themes_get_post_thumb(false, 200, 0, false, $input_value); 

											?>
											<img src="<?php echo esc_url($image['src']);?>" class="preview" alt="<?php esc_html_e('Previews','uniqmag');?>">
										<?php } else { ?>
											<img src="<?php echo UNIQMAG_DIFFERENT_THEME_IMAGE_CPANEL_URL;?>df-upload-tx.png" class="preview" alt="<?php esc_html_e('Previews','uniqmag');?>">
										<?php } ?>
									</span>
								</div>
							</div>
						</span>
					</div>
				<?php 
				}
			}
		}
	}		
	
	/**
	*	Prints a input field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "input",
	*		"title" => "Twitter Account Url:",
	*		"id" => $different_themes_managment->themeslug."_twitter",
	*		//if needed you can set a protection	
	*		"protected" => array(
	*			array("id" => $different_themes_managment->themeslug."_social_footer", "value" => "on")
	*		)
	*	)
	*/
	function print_input($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);
		
		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					$input_value = $this->get_input_value($value, $default);

					?>
					<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
						<div class="input-item-full-width clearfix">
					<?php } ?>
						<label><?php echo esc_html($value['title']);?></label>
					<?php
						if (isset($value['info'])) {
							$this->info_message($value['info']);
						}
					?>
						<span class="input-text"><input type="text" name="<?php echo esc_attr($value['id']);?>" id="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" <?php if(isset($value['number']) && $value['number'] == "yes") { ?>style="width: 46px;"<?php } ?> class="page-builder-input"/></span>
					</div>
					<?php
				}
			}
		}
	}			
	/**
	*	Prints a input field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "input_hidden",
	*		"id" => $different_themes_managment->themeslug."_twitter",
	*	
	*/
	function print_input_hidden($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
				$input_value = $this->get_input_value($value, $default);
			?>

				<input type="hidden" name="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" <?php if(isset($value['number']) && $value['number'] == "yes") { ?>style="width: 46px;"<?php } ?> class="page-builder-input"/>
			<?php
			}
		}
	}		
	/**
	*	Prints a color field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array( "type" => "color", "id" => $different_themes_managment->themeslug."_homepage_slogans_color", "title" => "Text Color:", "std" => "8f0029" ),
	*/
	function print_color($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
				$input_value = $this->get_input_value($value, $default);
			?>
			<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
			<div class="input-item-full-width clearfix">
			<?php } ?>
				<label style="width:158px;"><?php echo esc_html($value['title']);?></label>
				<span class="input-text">
					<input type="value"  class="page-builder-input color" name="<?php echo esc_attr($value['id']);?>"  id="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" style="width: 280px; border: #D1D1D1 1px solid; border-radius: 3px; outline: 0; font: 12px Arial, sans-serif; color: #333; padding: 7px 10px;"/>
				</span>
			
			</div>
			<?php
			}
		}
	}			
	
	/**
	*	Prints a checkbox.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "checkbox",
	*		"title" => "Show Social Networks Icons In Header",
	*		"id" => $different_themes_managment->themeslug."_social_header",
	*		//if needed you can set a protection	
	*		"protected" => array(
	*			array("id" => $different_themes_managment->themeslug."_social_footer", "value" => "on")
	*		),
	*		
	*	)
	*/
	function print_checkbox($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}

		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);

		
		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {

					if (isset($value['info'])) {
						$this->info_message($value['info']);
					}	
				?>	
				<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-half-width-inside clearfix df-checkbox"><?php } else { ?>
					<div class="input-item-half-width clearfix">
				<?php } ?>
				<?php if (isset($value['title'])) { ?>
					<label><?php echo esc_html($value['title']);?></label>
				<?php } ?>
				<?php
					$input_value = $this->get_input_value($value, $default);

					$n=0;



					foreach($value["options"] as $radio) {
						
						if(is_array($input_value)) {
							foreach($input_value as $_input_value) {
								if($_input_value == $radio["value"]) {

									$checked = 'checked="yes"'; 
									break;
								}  else {
									$checked = "";
								}
							}
						} else {
								if($input_value == $radio["value"]) {
									$checked = 'checked="yes"'; 
								}  else {
									$checked = "";
								}
						}





				?>		
						<div class="clearfix">
							<label><?php echo esc_html($radio["title"]);?></label>
							<span>
								<input type="checkbox" name="<?php echo esc_attr($value["id"]);?>" id="<?php echo esc_attr($value["id"]);?>"  value="<?php echo esc_attr($radio["value"]);?>" <?php echo esc_html($checked);?> class="page-builder-input"/>
							</span>
						</div>
				<?php
					$n++;
					}
				?>
				</div>	


				<?php	
				}		
			}		
		}		
	}		
	
	
	/**
	*	Prints a select box.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "select",
	*		"title" => "Blog List Style",
	*		"id" => $different_themes_managment->$different_themes_managment->themeslug."_news_style",
	*		"options"=>array(
	*			array("slug"=>"style_1", "name"=>"Style 1 (News and posts with big images)"), 
	*			array("slug"=>"style_2", "name"=>"Style 2 (News and posts with small images)"),
	*			),
	*		"std" => "style_1"
	*	)
	*/
	function print_select($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);
			
		?>
		<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix <?php if(isset($value['class'])) { echo esc_attr($value['class']); } ?>"><?php } else { ?>
			<div class="input-item-full-width clearfix <?php if(isset($value['class'])) { echo esc_attr($value['class']); } ?>">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
		<?php
			if (isset($value['info'])) {
				$this->info_message($value['info']);
			}
		?>
			<span class="select">
				<select name="<?php echo esc_attr($value['id']);?>" id="<?php echo esc_attr($value['id']);?>" class="page-builder-input">
					<?php
					foreach($value['options'] as $options) {
						if(htmlspecialchars($input_value) == $options['slug']) { $selected='selected="selected"'; } else { $selected=''; }
						echo '<option value="'.esc_attr($options['slug']).'" '.$selected.'>'.$options['name'].'</option>';
					}
					?>
				</select>
			</span>
		</div>							
		<?php
	

		}
	}
	
	/**
	*	Prints a select box.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "google_font_select",
	*		"title" => "Google Fonts",
	*		"id" => $different_themes_managment->$different_themes_managment->themeslug."__font",
	*		"sort" => "alpha",
	*		"default_font" => "Pacifico"
	*	)
	*/
	function print_google_font_select($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);

			$default_font = $value['default_font'];
			if($default_font) {
				$google_list = uniqmag_different_themes_get_google_fonts($value['sort']);
				if($google_list!=false) {
					$font_list = array_merge(array($default_font),$google_list);
				}
			} else {
				$google_list = uniqmag_different_themes_get_google_fonts($value['sort']);
				$font_list = uniqmag_different_themes_get_google_fonts($value['sort']);
			}


			$options = get_transient('df_font_options_'.$input_value);

			if($google_list!=false && !$options) {
				foreach($font_list as $key => $font) {
					if($input_value == $font) { $selected='selected="selected"'; } else { $selected=''; }
					if($key==0) {
						$options.= '<option value="'.esc_attr($font['font']).'">'.esc_html($font['font']).' '.esc_html($font['txt']).'</option>';
					} else {
						$options.= '<option value="'.esc_attr($font).'" '.esc_html($selected).'>'.esc_html($font).'</option>';
					}
				}
				set_transient( 'df_font_options_'.$input_value, $options, 3600 );
			} else {
				$options.= '<option value="">Something is Wrong with your server configuration.</option>';
			}


		?>
		<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix df-google-fonts"><?php } else { ?>
			<div class="input-item-full-width clearfix df-google-fonts">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
		<?php
			if (isset($value['info'])) {
				$this->info_message($value['info']);
			}
		?>
			<span class="select">
				<select name="<?php echo esc_attr($value['id']);?>" id="<?php echo esc_attr($value['id']);?>" class="page-builder-input google-fonts">
					<?php print $options;?>
				</select>
				<p class="font-preview">Grumpy wizards make toxic brew for the evil Queen and Jack.</p>
			</span>
		</div>	
		<?php
		}
	}
	
	/**
	*	Prints a textarea.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "textarea",
	*		"title" => "Homepage text block",
	*		"sub_title" => "Text:",
	*		"id" => $different_themes_managment->themeslug."_homepage_text_bock_txt",
	*		//if needed you can set a protection
	*		"protected" => array(
	*			array("id" => $different_themes_managment->themeslug."_homepage_text_blocks_enabled", "value" => "on")
	*		)
	*	),
	*/
	function print_textarea($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
				$input_value = $this->get_input_value($value, $default);

			?>
			<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
				<div class="input-item-full-width clearfix">
			<?php } ?>
					<label><?php echo esc_html($value['title']);?></label>
					<?php 
						if (isset($value['info'])) {
							$this->info_message($value['info']);
						}
					?>
					<span class="textarea">
						<?php if( isset($value['upload']) && $value['upload'] == "yes" ) { ?>
							<span id="<?php echo esc_attr($value['id']);?>_button" class="action btn-upload df-upload-button" style="width:100px; float:right; z-index:500;"><?php esc_html_e("Add Media",'uniqmag');?></span>
						<?php } ?>
						<textarea name="<?php echo esc_attr($value['id']);?>"  id="<?php echo esc_attr($value['id']);?>" class="page-builder-input df-textarea<?php if( isset($value['editor']) && $value['editor'] == "yes" ) { ?> df-tinymce<?php } ?>"><?php echo html_entity_decode(htmlentities($input_value));?></textarea>

					</span>
					<?php if(isset($value['sample'])) { ?>
						<label><strong><?php esc_html_e("Sample:",'uniqmag');?></strong> <?php echo uniqmag_different_themes_remove_html_slashes($value['sample']);?></label>
					<?php } ?>
				</div>
			<?php
			}
		}
	}
	
	/**
	*	Prints pages in a select box.
	*/
	function print_pages($value) {
	
		$protected_value = $this->get_field_value($value['protected'][0]["id"], $value['std']);
		$protected_value_1 = $this->get_field_value($value['protected'][1]["id"], $value['std']);
		$input_value = $this->get_field_value($value['id'], $value['std']);
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || (!isset($value['protected'][0]["id"]))) {
		
		echo balanceTags($this->before_item).
			'
						<div style="margin-left:33px;">
							<p>'.$value["title"].'<br/><br/></p>
								<select name="'.$value["id"].'"  class="page-builder-input styled">
								<option value="">Please select a page</option>';
									$pages = get_pages(); 
									foreach ($pages as $pagg) {
										$option = '<option value="'.esc_attr($pagg->ID).'"';
										if($pagg->ID == $input_value) { $option .= ' selected="selected" >'; } else { $option .= '>';} 
										$option .= $pagg->post_title;
										$option .= '</option>';
									}

				echo '</select>
					</div>
				'
			.$this->after_item;
		}
		
	}	
	
	/**
	*	Prints categories in a select box.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "categories",
	*		"id" => $different_themes_managment->themeslug."_homepage_main_news_block_post",
	*		"taxonomy" => "category",
	*		"title" => "Set News Category"
	*	),
	*/	
	function print_categories($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			$input_value = $this->get_input_value($value, $default);

		?>
		<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix <?php if(isset($value["class"])) echo esc_attr($value["class"]);?>"><?php } else { ?>
			<div class="input-item-full-width clearfix <?php echo esc_attr($value["class"]);?>">
		<?php } ?>
			<label><?php if(isset($value["title"])) echo esc_html($value['title']);?></label>

			<?php 
				$args = array(
					'class'					=> 'page-builder-input styled',
					'echo'					=> 1,
					'id'					=> esc_attr($value["id"]),
					'name'					=> esc_attr($value["id"]),
					'show_count'        	=> 1,
					'show_option_none' 		=> esc_html('Latest News', THEME_NAME),
					'option_none_value'  	=> '',
					'hierarchical'			=> 1,
					'taxonomy'          	=> esc_attr($value["taxonomy"]),
					'hide_empty'         	=> 0
				);

				if(isset($input_value)) {
					$args['selected'] = $input_value;
				}

			?>
			<span class="select">
				<?php wp_dropdown_categories( $args ); ?>
			</span>

		</div>
		<?php

		}
		
	}	
	/**
	*	Prints categories in a select box.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "multiple_select",
	*		"id" => $different_themes_managment->themeslug."_homepage_main_news_block_post",
	*		"taxonomy" => "category",
	*		"title" => "Set News Category"
	*	),
	*/	
	function print_multiple_select($value) {
		global $post_id;
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}

		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);
	
		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					$input_value = $this->get_input_value($value, $default);
					//convert to array, if it's not a array already
					if(!is_array($input_value)) {
						$inputArray = array();
						array_push($inputArray, $input_value);
						$input_value = $inputArray;
					}
					

					global $wpdb;
					$data = get_terms( $value["taxonomy"], 'hide_empty=0' );	
				?>
				<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside input-item-multiselect clearfix <?php if(isset($value["class"])) { echo esc_attr($value["class"]); } ?>"><?php } else { ?>
					<div class="input-item-full-width input-item-multiselect clearfix <?php if(isset($value["class"]))  { echo esc_attr($value["class"]); } ?>">
				<?php } ?>
					<label><?php echo esc_html($value['title']);?></label>
				<?php if(count($data) > 0) {?>
					<span class="select">
						<select <?php if( isset($value['home']) && $value['home'] == "yes" ) { ?>name="<?php echo esc_attr($value["id"]);?>"<?php } else { ?>name="<?php echo esc_attr($value["id"]);?>[]"<?php } ?> id="<?php echo esc_attr($value["id"]);?>" class="page-builder-input styled" multiple="multiple">
							<?php if(isset($value['default'])) { ?>
								<option value="<?php echo esc_attr($value['default'][0]);?>" <?php if((is_array($input_value) && in_array($value['default'][0],$input_value)) || !is_array($input_value)) { echo "selected"; } ?>><?php echo esc_html($value['default'][1]);?></options>
							<?php } ?>
							<?php 
								foreach($data as $d) {
									if(is_array($input_value) && in_array($d->term_id,$input_value)) { $selected=' selected'; } else { $selected=''; }
									echo "<option value=\"".esc_attr($d->term_id)."\" ".$selected.">".esc_html($d->name)."</option>";
								}
							?>
						</select>
					</span>
				<?php } ?>
				</div>
				<?php

				}
			}
		}
		
	}	
	/**
	*	Prints categories in a select box.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "user",
	*		"id" => $different_themes_managment->themeslug."_homepage_main_news_block_post",
	*		"title" => "Registered Users"
	*	),
	*/	
	function print_user($value) {
	
		$protected_value = $this->get_field_value($value['protected'][0]["id"], $value['std']);
		$protected_value_1 = $this->get_field_value($value['protected'][1]["id"], $value['std']);
		$input_value = $this->get_field_value($value['id'], $value['std']);
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || (!isset($value['protected'][0]["id"]))) {
			global $wpdb;
			$order = 'user_nicename';
			
			$query = $wpdb->prepare("SELECT * FROM {$wpdb->users } ORDER BY %s", $order);
    		$users = $wpdb->get_results($query);
		?>
		<?php if( $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
			<div class="input-item-full-width clearfix">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
			<span class="select">
				<select name="<?php echo esc_attr($value["id"]);?>"  id="<?php echo esc_attr($value["id"]);?>" class="page-builder-input styled">
					<option value="">Select a user</option>
			<?php 
				foreach($users as $user) :
					if($input_value==$user->ID) { $selected=' selected'; } else { $selected=''; }
					echo "<option value=\"".$user->ID."\" ".$selected.">".$user->user_nicename."</option>";
				endforeach;
			?>
				</select>
			</span>
		</div>
		<?php

		}
		
	}
	
	/**
	*	Prints radio buttons.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "radio",
	*		"title" => "Custom Homepage slider",
	*		"id" => $different_themes_managment->themeslug."_homepage_slider",
	*		"radio" => array(
	*			array("title" => "Enable slider:", "value" => "on"),
	*			array("title" => "Use single image:", "value" => "single"),
	*			array("title" => "Disable slider:", "value" => "off")
	*		),
	*		"std" => "off"
	*	)
	*/
	function print_radio($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);
		if (isset($value['protected'][0]["id"])) {
			$protected_value."==".$value['protected'][0]["value"];
		}

		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					$input_value = $this->get_input_value($value, $default);
				
					$n=0;
					foreach($value["radio"] as $radio) {
						$n++;
						$input_value = $this->get_input_value($value, $default);

						if($input_value == $radio["value"]) {
							$checked='checked="yes"'; 
						} else { 
							$checked=''; 
						}

						if($this->type == "meta" && isset($value['compare']) && $value['compare']!="custom") {
							$attr='  disabled="disabled"';
						} else {
							$attr = false;
						}
				?>
					<div class="input-item-half-width clearfix<?php if(isset($value['class'])) { echo " ".esc_attr($value['class']); }?>">
						<?php if(isset($radio["title"])) { ?>
							<label><?php echo uniqmag_different_themes_html_output($radio["title"]);?></label>
							<span><input<?php echo esc_html($attr);?> type="radio" name="<?php echo esc_attr($value["id"]);?>" id="<?php echo esc_attr($value["id"].'_'.$n);?>"  value="<?php echo esc_attr($radio["value"]);?>" <?php echo esc_html($checked);?> class="page-builder-input style-radio"/></span>
						<?php } else { ?>
							<label>
								<input<?php echo esc_html($attr);?> type="radio" name="<?php echo esc_attr($value["id"]);?>" id="<?php echo esc_attr($value["id"].'_'.$n);?>"  value="<?php echo esc_attr($radio["value"]);?>" <?php echo esc_html($checked);?> class="page-builder-input"/>
								<img src="<?php echo esc_url($radio["img"]);?>"/>
							</label>
						<?php } ?>
					</div>	
				<?php
					}
					if($attr!=false) {
				?>
						<div class="row-explenation">
							<span><?php esc_html_e("This options is disabled, to enable it you must set this option in <strong>theme management panel</strong> to custom.",'uniqmag');?></span>
						</div>
				<?php
					}		
				}		
			}		
		}		
	}
	
	/**
	*	Sidebar Order
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "sidebar_order",
	*		"title" => "Order Sidebars",
	*		"id" => THEME_NAME."_sidebar_name"
	*	),	
	*/
	function print_sidebar_order( $value ) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(((isset($protectedValue) && $protectedValue!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || ($protectedValue==false)) {

			$input_value = $this->get_input_value($value, $default);

					
			$saved_value = Different_Themes()->options->get( $value['id'].'s' );
			$saved_value = explode( "|*|", $saved_value );
		
		?>
				
		<ul class="blocks block-active clearfix" id="sidebar_order">
			<?php
			$i=0;
			foreach ( $saved_value as $sidebar ) {
				if ( $sidebar != "" ) {
					$i++;			
			?>
					<li class="row-item-full-width clearfix" id="recordsArray_<?php echo uniqmag_different_themes_convert_to_class($sidebar); ?>" alt="<?php echo esc_attr($sidebar); ?>">
						<div class="blocks-content clearfix" style="text-align: left;">
							<?php esc_html_e("Sidebar name:",'uniqmag');?> <b><?php echo esc_html($sidebar); ?></b><a href="javascript:{}" class="button edit sidebar-edit" id="edit-<?php echo uniqmag_different_themes_convert_to_class($sidebar); ?>" rel="<?php echo esc_attr($sidebar); ?>"><?php esc_html_e("Edit",'uniqmag');?></a><a href="javascript:{}" class="button delete sidebar-delete" id="delete-<?php echo uniqmag_different_themes_convert_to_class($sidebar); ?>"><?php esc_html_e("Delete",'uniqmag');?></a>
						</div>
					</li>
			<?php
				}	
			}			
			?>
		</ul>	
		
		<?php
		}
	}
	
	/**
	*	Prints a Add New Sidebar field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "add_text",
	*		"title" => "Add New Sidebar:",
	*		"id" => THEME_NAME."_sidebar_name"
	*		)
	*	)
	*/
	function print_add_text($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(((isset($protectedValue) && $protectedValue!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || ($protectedValue==false)) {

			$input_value = $this->get_input_value($value, $default);

		?>
		<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
			<div class="input-item-full-width clearfix">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
			<span class="input-text"><input class="page-builder-input" type="text" name="<?php echo esc_attr($value['id']); ?>" /></span>
		<?php
		}
		$saved_value = Different_Themes()->options->get( $value['id'].'s' );
		$saved_value = stripslashes($saved_value);

		echo '<input class="page-builder-input" type="hidden" name="'.$value['id'].'s" id="'.$value['id'].'s" value="'.$saved_value.'" />';
		?>
		</div>
		<input type="hidden" name="action" value="save" />
		<a href="javascript:{}" class="button-1 df-save-management" data-saved="<?php esc_html_e("Add/Save",'uniqmag');?>" data-loading="<?php esc_html_e("Adding...",'uniqmag');?>"><?php esc_html_e("Add/Save",'uniqmag');?></a>
			
		<?php
	}	
	
	/**
	*	Prints a sidebar select field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "sidebar_select",
	*		"title" => "Sidebar:",
	*		"id" => THEME_NAME."_sidebar_id"
	*		)
	*	)
	*/
	function print_sidebar_select($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if((isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false) || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					$input_value = $this->get_input_value($value, $default);

					$sidebar_names = Different_Themes()->options->get( THEME_NAME."_sidebar_names" );
					$sidebar_names = explode( "|*|", $sidebar_names );
				?>
				<?php if(isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
				<div class="input-item-full-width clearfix">
				<?php } ?>
					<label><?php echo esc_html($value['title']);?></label>
					<span class="select">
						<select name="<?php echo esc_attr($value["id"]);?>" class="page-builder-input styled <?php echo esc_attr($value["id"]);?>">
							<?php 
								if(isset($value['default']) && $value['default']!=false) {
									foreach($value['default'] as $defaults) { ?>
										<option value="<?php echo esc_attr($defaults[0]);?>" <?php if($input_value==$defaults[0]) { echo "selected=\"selected\""; } ?>><?php echo esc_html($defaults[1]);?></option>
							<?php 
									}
								} 
							?>

							<?php if(function_exists('is_woocommerce')) { ?>
								<option value="df_woocommerce" <?php if($input_value=="df_woocommerce") { echo "selected=\"selected\""; } ?>><?php esc_html_e("Woocommerce",'uniqmag');?></option>
							<?php } ?>
							<?php if(function_exists("is_bbpress")) { ?>
								<option value="df_bbpress" <?php if($input_value=="df_bbpress") { echo "selected=\"selected\""; } ?>><?php esc_html_e("bbPRess",'uniqmag');?></option>
							<?php } ?>
							<?php if(function_exists("is_buddypress")) { ?>
								<option value="df_buddypress" <?php if($input_value=="df_buddypress") { echo "selected=\"selected\""; } ?>><?php esc_html_e("BudyPress",'uniqmag');?></option>
							<?php } ?>
							
		
							<?php
								foreach ($sidebar_names as $sidebar_name) {
									if ( $input_value == $sidebar_name ) {
										$selected="selected=\"selected\"";
									} else { 
										$selected="";
									}
											
									if ( $sidebar_name != "" ) {
							?>
									<option value="<?php echo esc_attr($sidebar_name);?>" <?php echo esc_html($selected);?>><?php echo esc_html($sidebar_name);?></option>
							<?php
								}
							}
						?>
						</select>
					</span>
				<?php
					$saved_value = Different_Themes()->options->get( $value['id'].'s' );
					$saved_value = stripslashes($saved_value);
					echo '<input class="page-builder-input" type="hidden" name="'.$value['id'].'s" id="'.$value['id'].'s" value="'.$saved_value.'" />';
				?>
				</div>
				<?php
				}
			}
		}

		
	}	
	/**
	*	Prints a sidebar select field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "icon_select",
	*		"title" => "Icon:",
	*		"id" => THEME_NAME."_icon_id"
	*		)
	*	)
	*/
	function print_icon_select($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
				$input_value = $this->get_input_value($value, $default);

				$icons = Different_Themes()->icons->get_list();
			?>
			<?php if(isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
			<div class="input-item-full-width clearfix">
			<?php } ?>
				<label><?php echo esc_html($value['title']);?></label>
				<span class="select">
					<select name="<?php echo esc_attr($value["id"]);?>" class="page-builder-input styled">

						<?php
							foreach ($icons as $icon) {
								if ( $input_value == $icon ) {
									$selected="selected=\"selected\"";
								} else { 
									$selected="";
								}
										
								if ( $icon != "" ) {
						?>
								<option value="<?php echo esc_attr($icon);?>" <?php echo esc_html($selected);?>><?php echo esc_html($icon);?></option>
						<?php
								}
							}
						?>
					</select>
				</span>

			</div>
			<?php
			}
		}

		
	}

	/**
	*	Prints a input field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "datepicker",
	*		"title" => "Date:",
	*		"id" => $different_themes_managment->themeslug."_date",
	*		//if needed you can set a protection	
	*		"protected" => array(
	*			array("id" => $different_themes_managment->themeslug."_social_footer", "value" => "on")
	*		)
	*	)
	*/
	function print_datepicker($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
			if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
				$input_value = $this->get_input_value($value, $default);
			?>
			<?php if( isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
				<div class="input-item-full-width clearfix">
			<?php } ?>
				<label><?php echo esc_html($value['title']);?></label>
			<?php
				if (isset($value['info'])) {
					$this->info_message($value['info']);
				}
			?>
				<span class="input-text"><input class="page-builder-input" type="text" name="<?php echo esc_attr($value['id']);?>" id="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" <?php if(isset($value['number']) && $value['number'] == "yes") { ?>style="width: 46px;"<?php } ?>/></span>
			</div>
			<script>
			jQuery(document).ready(function() {
			    jQuery( "#<?php echo esc_attr($value['id']);?>" ).datepicker({
			        dateFormat: 'yy/mm/dd 00:00:00',
			        firstDay: 1
			    });
			});
			</script>
			<?php
			}
		}
	}			
	/**
	*	Prints a sidebar field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "sidebar",
	*		"title" => "Twitter Account Url:",
	*		"id" => $different_themes_managment->themeslug."_twitter",
	*		//if needed you can set a protection	
	*		"protected" => array(
	*			array("id" => $different_themes_managment->themeslug."_social_footer", "value" => "on")
	*		)
	*	)
	*/
	function print_sidebar($value) {
		$protected_value = $this->get_field_value($value['protected'][0]["id"], "");
		$protected_value_1 = $this->get_field_value($value['protected'][1]["id"], "");
		$input_value = $this->get_field_value($value['id'], $value['std']);
	
		$saved_value=Different_Themes()->options->get( $value['id'].'s' );
		$saved_value=stripslashes($saved_value);
		$saved_value=explode('|*|', $saved_value);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || (!isset($value['protected'][0]["id"]))) {
			echo balanceTags($this->before_item.$this->before_item_title.esc_html($value['title'])).'</span>
					';
					foreach ( $saved_value as $sidebar_name) {
						echo esc_html($sidebar_name)."<br/>";
					}
					echo ''
			.$this->after_item;
		}

	}
	/**
	*	Prints a sidebar field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array(
	*		"type" => "gallery_tab",
	*		"id" => $orange_themes_managment->themeslug,
	*	)
	*/
	function print_gallery_tab($value) {
		//add gallery tab if gallery plugin is active
		if(function_exists('different_theme_gallery_active')) {
			$i = self::get_i();
			$this->print_subtab(
				array(
					"type" => "sub_tab",
					"slug"=>'gallery'
				),$i
			);
			$this->print_row(
				array(
					"type" => "row"
				)
			);
			$this->print_title(
				array(
					"type" => "title",
					"title"=> esc_html__('Gallery Settings','uniqmag')
				)
			);
			$this->print_input(
				array(
					"type" => "input",
					"title" => esc_html__("Items per gallery page:",'uniqmag'),
					"id" => $value['id']."_gallery_items",
					"number" => "yes",
					"std" => "8"
				)
			);
			$this->print_close(
				array(
					"type" => "close"
				)
			);
			$this->print_row(
				array(
					"type" => "row"
				)
			);
			$this->print_title(
				array(
					"type" => "title",
					"title" => esc_html__("Show \"Similar Posts\" In Single Galley",'uniqmag')
				)
			);
			$this->print_radio(
				array(
					"type" => "radio",
					"id" => $value['id']."_similar_posts_gallery",
					"radio" => array(
						array("title" => esc_html__("Show:",'uniqmag'), "value" => "show"),
						array("title" => esc_html__("Hide:",'uniqmag'), "value" => "hide"),
						array("title" => esc_html__("Custom For Each Gallery Page:",'uniqmag'), "value" => "custom")
					),
					"std" => "custom"
				)
			);
			$this->print_close(
				array(
					"type" => "close"
				)
			);
			$this->print_save(
				array(
					"type" => "save",
					"title" => esc_html__("Save Changes",'uniqmag'),
				)
			);
			$this->print_closesubtab(
				array(
					"type" => "closesubtab"
				),$i
			);

		}
	}
	/**
	*	Prints a input field.
	*
	*	EXAMPLE:
	*	-----------------------------------------------------------------
	*	array( "type" => "scroller", "id" => $different_themes_managment->themeslug."_homepage_section_title_size", "title" => "Font Size:", "max" => "250" ),
	*/
	function print_scroller($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		if(((isset($protectedValue) && $protectedValue!="") && $protected_value==$value['protected'][0]["value"]) && $protected_value_1==$value['protected'][1]["value"] || ($protectedValue==false)) {


		$input_value = $this->get_input_value($value, $default);
		$input_value = (!$input_value ? 10 : $input_value);
		
		?>
		<?php if(isset($value['home']) && $value['home'] == "yes" ) { ?><div class="input-item-full-width-inside clearfix"><?php } else { ?>
		<div class="input-item-full-width clearfix">
		<?php } ?>
			<label><?php echo esc_html($value['title']);?></label>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					jQuery( ".<?php echo esc_attr($value['id']);?> > #slider-range-min-<?php echo esc_attr($value['id']);?>" ).slider({
						range: "min",
						value: <?php echo intval($input_value);?>,
						min: 1,
						max: <?php echo intval($value['max']);?>,
						slide: function( event, ui ) {
							jQuery(this).prev("input").val(ui.value);
						}
					});
				});
			</script>
			<span class="<?php echo esc_attr($value['id']);?> scroller-wrap">
				<input name="<?php echo esc_attr($value['id']);?>" value="<?php echo esc_attr($input_value);?>" data-value="<?php echo intval($input_value);?>" data-min="1" data-max="<?php echo esc_attr($value['max']);?>" class="page-builder-input scroller" type="text" id="<?php echo esc_attr($value['id']);?>" style="color: #f6931f;font-weight: bold;width: 50px;position: absolute;margin-left: -60px;margin-top: -1px;" readonly="readonly"/>
				<div id="slider-range-min-<?php echo esc_attr($value['id']);?>" class="slider-range-min slider-slider"></div>
			</span>
		</div>
		<?php 
		}

	}
	function print_save($value) {
	?>
		<div class="row">
			<input type="hidden" name="action" value="save" /><a href="javascript:{}" class="button-1 df-save-management" data-saved="<?php echo esc_attr($value['title']);?>" data-loading="<?php esc_html_e("Saving...",'uniqmag');?>"><?php echo esc_html($value['title']);?></a>
		</div>
	<?php
	}
	
	function print_title ( $value ) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}

		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);
		if(isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
					if(isset($value['home']) && $value['home']=="yes") {
						echo '<div class="input-item-full-width-inside clearfix">'.$this->before_item_title.$value["title"].$this->after_item_title.'</div>';
					} else {
						echo balanceTags($this->before_item_title.esc_html($value["title"]).$this->after_item_title, true);
					}
				}
			}
		}
	}
	
	function print_slide_order ( $value ) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		$protected_value = $this->get_field_value($protectedValue, $default);
		
		
		if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {
			
			if ( Different_Themes()->options->get(THEME_NAME."-slide-order-set") != "1" ) {
				$order = "";
			} else {
				$order = "&orderby=menu_order&order=ASC";
			}
	
			$cat = Different_Themes()->options->get($value['cat']);
			$count = $value['count'];
			$my_query = new WP_Query("cat=".$cat."&showposts=".$count.$order);
			?>
			<ul class="blocks block-active slider-sequence clearfix">
				<?php
				if ( $my_query->have_posts() ) : while ( $my_query->have_posts() ) : $my_query->the_post();
					global $post; 
					$thePostID = $post->ID;
					$image = uniqmag_different_themes_get_post_thumb($thePostID,45,45,THEME_NAME."_slider_image"); 			
				?>
					<li class="row-item-full-width clearfix" id="recordsArray_<?php echo intval($post->ID); ?>">
						<div class="blocks-content clearfix">
							<div class="image"><img src="<?php echo esc_url($image['src']); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /></div>
							<div class="text-content">
								<p><b><?php echo esc_html(get_the_title()); ?></b></p>
								<p><?php echo esc_html(get_the_excerpt()); ?></p>
							</div>
						</div>
					</li>
				<?php
				endwhile; else: 
				endif;				
				?>
			</ul>
			<?php
		}
	}

	
	function print_row($value) {
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
	
		if($this->type == "meta" && isset($value['compare']) && $value['compare']!="custom") {
			$class=' disabled';
		} else {
			$class = false;
		}

		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);
		
		if(isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check

				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {

					echo '<div class="row'.$class.'">';
				
				}
			}
		}
	}
		
	function print_closesubtab ( $value, $i ) {
		global $post_id;
		if(isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(isset($value['hide_in']) && (in_array($this->get_current_post_type(),$value['hide_in']))) {
				} else {
					echo '</div>';
				}
			}
		}
		
	}
	
	function print_closetab ( $value, $i ) {
		echo '</div></div></div>';
	}	
	
	function print_close($value){
		if(isset($value['std'])) {
			$default = $value['std'];
		} else {
			$default = false;
		}
		if(isset($value['protected'][0]["id"])) {
			$protectedValue = $value['protected'][0]["id"];
		} else {
			$protectedValue = false;
		}
		
		if(!isset($value['protected'][0]['dataType'])) $value['protected'][0]['dataType'] = 'meta';

		$protected_value = $this->get_field_value($protectedValue, $default, $value['protected'][0]['dataType']);

		if(isset($value['skip_templates']) && $this->template_check($value['skip_templates'])==false || !isset($value['skip_templates'])) { //meta box check
			if(isset($value['page_type']) && $this->page_type_check($value['page_type'])==true || !isset($value['page_type'])) { //meta box check
				if(((isset($value['protected'][0]["id"]) && $value['protected'][0]["id"]!="") && $protected_value==$value['protected'][0]["value"]) || (!isset($value['protected'][0]["id"]))) {

					echo '</div>';
				
				}
			}
		}
	}
	
	function get_field_value($id, $std, $type='meta'){
		global $post_id;
		if($this->type=="meta") {
			if($type=='meta') {
				if (get_post_meta ( $post_id, $id, true ) != "" ) { 
					return get_post_meta($post_id,$id,true);
				} else { 
					return stripslashes($std); 
				}
			} else {
				if ( Different_Themes()->options->get( $id ) != "") { 
					return stripslashes(Different_Themes()->options->get( $id )); 
				} else { 
					return stripslashes($std); 
				}
			}
		} else {
			if ( Different_Themes()->options->get( $id ) != "") { 
				if(is_array(Different_Themes()->options->get( $id ))) {
					return Different_Themes()->options->get( $id ); 
				} else {
					return stripslashes(Different_Themes()->options->get( $id )); 	
				}
				
			} else { 
				return stripslashes($std); 
			}
		}

	}
	
	
	function print_saved_message(){
		//echo '<div class="note_box" id="saved_box">'.$this->themename.' settings saved.</div>';	
	}
		
}




?>