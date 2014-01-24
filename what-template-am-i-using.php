<?php
/*
Plugin Name: What Template Am I Using
Description: This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.
Author: Eric King
Version: 0.1.4
Author URI: http://webdeveric.com/
*/

// This plugin only needs to run on the front end of the site.
if( is_admin() )
	return;

class What_Template_Am_I_Using {

	const VERSION = '0.1.4';

	private static $queue;

	public static function init(){
		add_action('init', array( __CLASS__, 'setup' ) );
	}

	public static function setup(){
		if( current_user_can( 'edit_theme_options' ) ){
			
			self::$queue = new SplPriorityQueue();
			
			self::enqueue_assets();

			add_filter('what_template_am_i_using_data', array( __CLASS__, 'default_data' ), 10, 1 );
			add_filter('what_template_am_i_using_data', array( __CLASS__, 'find_template_parts' ), 10, 1 );

			add_action('wp_print_scripts', array( __CLASS__, 'print_scripts_hook' ) );
			add_action('wp_print_styles', array( __CLASS__, 'print_styles_hook' ) );
			
			add_action('wp_footer', array( __CLASS__, 'output' ) );
		}
	}

	public static function print_scripts_hook(){
		add_filter('what_template_am_i_using_data', array( __CLASS__, 'find_enqueued_scripts' ), 10, 1 );
	}

	public static function print_styles_hook(){
		add_filter('what_template_am_i_using_data', array( __CLASS__, 'find_enqueued_styles' ), 10, 1 );
	}

	public static function enqueue_assets(){
		wp_enqueue_style('what-template-am-i-using', plugins_url( '/css/what-template-am-i-using.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_script('what-template-am-i-using', plugins_url( '/js/what-template-am-i-using.js', __FILE__ ), array('jquery'), self::VERSION, true );
	}

	public static function default_data( SplPriorityQueue $queue ){
		global $template, $post;
		// A SplPriorityQueue is basically a max heap so the higher values are the first to be retreived.
		$queue->insert( array( 'Template' => str_replace( get_theme_root(), '', $template ) ), 100 );
		$queue->insert( array( 'Post Type' => isset( $post, $post->post_type ) ? $post->post_type : 'not set' ), 90 );
		$queue->insert( array( 'Front Page' => is_front_page() ? 'Yes' : 'No' ), 80 );
		$queue->insert( array( 'Home Page' => is_home() ? 'Yes' : 'No' ), 80 );
		$queue->insert( array( 'Server IP' => $_SERVER['SERVER_ADDR'] ), 70 );
		return $queue;
	}

	public static function find_template_parts( SplPriorityQueue $queue ){
		global $wp_actions;
		$template_parts = array();
		foreach( $wp_actions as $action_name => $num ){
			$matches = array();
			if( preg_match('#get_template_part_(?<slug>.+)#', $action_name, $matches ) )
				$template_parts[] = $matches['slug'];
		}
		if( ! empty( $template_parts ) )
			$queue->insert( array( 'Template Parts Used' => implode(', ', $template_parts ) ), 99 );
		return $queue;
	}

	public static function process_dependency_obj( SplPriorityQueue $queue, WP_Dependencies $dep, $label, $priority = 10 ){
		$deps = array_intersect_key( $dep->registered, $dep->groups );
		$items = array();
		foreach( $deps as $d ){
			if( isset( $d->src ) && $d->src != '' )
				$items[] = sprintf('<li><a href="%2$s">%1$s</a></li>', $d->handle, $d->src );
		}
		$queue->insert( array( $label => '<ul>' . implode('', $items ) . '</ul>' ), $priority );
		return $queue;
	}

	public static function find_enqueued_scripts( SplPriorityQueue $queue ){
		global $wp_scripts;
		return self::process_dependency_obj( $queue, $wp_scripts, 'Enqueued Scripts', 11 );
	}

	function find_enqueued_styles( SplPriorityQueue $queue ){
		global $wp_styles;
		return self::process_dependency_obj( $queue, $wp_styles, 'Enqueued Styles', 10 );
	}

	public static function output(){
		?>
		<div id="what-template-am-i-using">
			<a id="what-template-am-i-using-handle" title="Click to toggle"><span>What Template Am I Using?</span></a>
			<a id="what-template-am-i-using-close" title="Click to remove from page">&times;</a>
			<dl id="what-template-am-i-using-data">
				<?php 
					apply_filters('what_template_am_i_using_data', self::$queue );
					foreach( self::$queue as $data ){
						foreach( $data as $label => $value )
							printf('<dt>%s</dt><dd>%s</dd>', $label, $value );	
					}
				?>
			</dl>
		</div>
		<?php
	}
}

What_Template_Am_I_Using::init();

/**
	This is here to show you how to extend what is shown in the panel.
	Something like this would be put in your theme's function.php file or into a plugin.
*/
function what_template_am_i_using_server_data( SplPriorityQueue $queue ){
	$queue->insert( array( 'Your IP' => $_SERVER['REMOTE_ADDR'] ), 70 );
	$queue->insert( array( 'Server Software' => $_SERVER['SERVER_SOFTWARE'] ), 65 );
	$queue->insert( array( 'PHP Version' => phpversion() ), 64 );
	return $queue;
}
add_filter('what_template_am_i_using_data', 'what_template_am_i_using_server_data', 10, 1 );
