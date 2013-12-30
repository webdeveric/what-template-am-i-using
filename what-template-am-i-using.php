<?php
/*
Plugin Name: What Template Am I Using
Description: This plugin shows you what template is currently being used to render the current page/post/what ever.
Author: Eric King
Version: 0.1.3
Author URI: http://webdeveric.com/
*/

add_action('wp_footer', 'what_template_am_i_using');
function what_template_am_i_using(){
	if( current_user_can( 'edit_theme_options' ) ){
		global $template, $post;
		$data = array(
			'Template' => str_replace( ABSPATH, '/', print_r($template, true ) ),
			'Post Type' => isset( $post->post_type ) ? $post->post_type : '<strong>not set</strong>',
			'Front Page' => is_front_page() ? 'Yes' : 'No',
			'Home Page' => is_home() ? 'Yes' : 'No',
			'Server IP' => $_SERVER['SERVER_ADDR']
		);

		echo '<ul style="position:relative; clear:both; background:rgba(255,255,255,.5); color:#000; margin:0; padding:10px; font:icon; text-align:center;">';
		foreach( $data as $label => $value ){
			printf('<li>%s: %s</li>', $label, $value );
		}
		echo '</ul>';
	}
}