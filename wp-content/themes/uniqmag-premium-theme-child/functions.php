<?php

include_once get_stylesheet_directory() . '/includes/widgets/class-ys-widget-recent-posts.php';
add_action( 'widgets_init', function(){
	register_widget( 'YS_Widget_Recent_Posts' );
});

function ys_enqueue_scripts(){
    wp_enqueue_script('ys-scripts', get_stylesheet_directory_uri(). '/js/scripts.js', array('jquery'), null, true);
}
add_action( 'wp_enqueue_scripts', 'ys_enqueue_scripts' );

