<?php

include_once get_stylesheet_directory() . '/includes/widgets/class-ys-widget-recent-posts.php';
add_action( 'widgets_init', function(){
	register_widget( 'YS_Widget_Recent_Posts' );
});

function ys_enqueue_scripts(){
    wp_enqueue_script('ys-scripts', get_stylesheet_directory_uri(). '/js/scripts.js', array('jquery'), null, true);
}
add_action( 'wp_enqueue_scripts', 'ys_enqueue_scripts' );

// Our custom post type function
function create_posttype() {

	register_post_type( 'workingplaces',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Pracovní místa' ),
				'singular_name' => __( 'Pracovní místa' )
			),
			'public' => true,
			'has_archive' => true,
                        'supports' => array( 'title','editor'),
                        'menu_icon' => 'dashicons-admin-users',
                        'rewrite' => array('slug' => 'pracovni-mista'),
		)
	);
        
        register_post_type( 'action',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Akce' ),
				'singular_name' => __( 'Akce' )
			),
			'public' => true, 
			'has_archive' => true,
                        'supports' => array( 'title','editor'),
                        'menu_icon' => 'dashicons-format-audio',
                        'rewrite' => array('slug' => 'akce'),
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

/**
 * Filter the except length to 20 characters.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function ys_custom_excerpt_length( $length ) {
    return 45;
}
add_filter( 'excerpt_length', 'ys_custom_excerpt_length', 999 );

