<?php
class WTAIU_Theme_Panel extends WTAIU_Panel {

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'Theme', 'wtaiu-theme-panel', $plugin_file );
		$this->default_open_state = 'closed';
	}

	public function get_content(){
		$theme =  wp_get_theme();
		$info = array();
		do{
			$info[] = $this->get_theme_info_html( $theme );
			$theme = $theme->parent();
		} while( $theme !== false );
		// WP currently only supports child themes, not grandchild themes. This loop should run at most two times.
		return implode( '', $info );
	}

	protected function get_theme_info_html( WP_Theme $theme ){

		$name			= $theme->display('Name');
		$version		= $theme->display('Version');
		$description	= $theme->display('Description');
		$desc_title		= esc_attr( $theme->get('Description') );
		$author			= $theme->display('Author');
		$screenshot		= $theme->get_screenshot();
		$thumbnail_style= $screenshot !== false ? sprintf('style="background-image:url(%s);"', $screenshot ) : '';
		$theme_url 		= network_admin_url( add_query_arg('theme', $theme->get_stylesheet(), 'themes.php') );

$output=<<<OUTPUT

<div class="theme-info" title="{$desc_title}">
	<a href="{$theme_url}" class="theme-screenshot" {$thumbnail_style}></a>
	<div class="theme-info-wrap">
		<h3 class="theme-info-header" title="{$name}">
		    <a href="{$theme_url}" class="theme-name">{$name}</a>
		</h3>
		<p class="theme-version">Version: {$version}</p>
		<p class="theme-author">By {$author}</p>
	</div>
</div>

OUTPUT;

		return $output;

	}


}


class WTAIU_Template_Panel extends WTAIU_Panel {

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'Template', 'wtaiu-template-panel', $plugin_file );
	}

	public function get_content(){
		global $template;
		return str_replace( get_theme_root(), '', $template );
	}

}



class WTAIU_General_Info_Panel extends WTAIU_Panel {

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'General Information', 'wtaiu-general-info-panel', $plugin_file );
		$this->author		= 'Eric King';
		$this->author_url	= 'http://webdeveric.com/';
		$this->version		= '0.1';
	}

	public function get_content(){
		global $post;
		$post_type = isset( $post, $post->post_type ) ? $post->post_type : 'not set';
		$front_page = is_front_page() ? 'Yes' : 'No';
		$home_page = is_home() ? 'Yes' : 'No';
		$is_404 = is_404() ? 'Yes' : 'No';
		$is_search = is_search() ? 'Yes' : 'No';

$info=<<<INFO
	<table class="info-table">
		<tbody>
			<tr>
				<th scope="row">Post Type</th>
				<td>{$post_type}</td>
			</tr>
			<tr>
				<th scope="row">Front</th>
				<td>{$front_page}</td>
			</tr>
			<tr>
				<th scope="row">Home</th>
				<td>{$home_page}</td>
			</tr>
			<tr>
				<th scope="row">404</th>
				<td>{$is_404}</td>
			</tr>
			<tr>
				<th scope="row">Search</th>
				<td>{$is_search}</td>
			</tr>
		</tbody>
	</table>
INFO;

		return $info;
	}

}



class WTAIU_Additional_Files_Panel extends WTAIU_Panel {

	protected $files;

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'Additional Files Used', 'wtaiu-additional-files-panel', $plugin_file );
		$this->files = array();
	}

	public function setup(){
		add_action( 'get_header',		array( $this, 'record_header' ), 10, 1 );
		add_action( 'get_footer',		array( $this, 'record_footer' ), 10, 1 );
		add_action( 'get_sidebar',		array( $this, 'record_sidebar' ), 10, 1 );
	}

	public function record_header( $name ){
		$this->files[] = isset( $name ) ? "header-{$name}.php" : 'header.php';
	}

	public function record_footer( $name ){
		$this->files[] = isset( $name ) ? "footer-{$name}.php" : 'footer.php';
	}

	public function record_sidebar( $name ){
		$this->files[] = isset( $name ) ? "sidebar-{$name}.php" : 'sidebar.php';
	}

	public function get_content(){
		global $wp_actions;

		foreach( $wp_actions as $action_name => $num ){
			$matches = array();
			if( preg_match('#get_template_part_(?<slug>.+)#', $action_name, $matches ) )
				$this->files[] = $matches['slug'];
		}

		if( ! empty( $this->files ) )
			return implode(', ', $this->files );
	}

}




class WTAIU_Dynamic_Sidebar_Info_Panel extends WTAIU_Panel {

	protected $sidebars;

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'Sidebar Information', 'wtaiu-dynamic-sidebar-info-panel', $plugin_file );
		$this->sidebars = array();
	}

	public function setup(){
		add_action( 'dynamic_sidebar_params',	array( $this, 'record_dynamic_sidebar_params' ), 10, 1 );
	}

	public function record_dynamic_sidebar_params( $params ){
		$sidebar_name = $params[0]['name'];
		if( ! array_key_exists( $sidebar_name, $this->sidebars ) )
			$this->sidebars[ $sidebar_name ] = array();
		$this->sidebars[ $sidebar_name ][] = $params[0]['widget_name'];
		return $params;
	}

	public function get_content(){

		if( empty( $this->sidebars ) )
			return 'No sidebar widgets found';

		$info = array();
		$info[] = '<dl class="info-list">';
		foreach( $this->sidebars as $sidebar_name => $widget_names ){
			$widgets = array();
			$widgets[] = sprintf( '<ul title="Widgets used in %s">', $sidebar_name );
			foreach( $widget_names as $widget_name ){
				$widgets[] = sprintf('<li>%1$s</li>', $widget_name );

			}
			$widgets[] = '</ul>';

			$this->label .= sprintf('', count( $this->dependencies ) );
			$info[] = sprintf('<dt>%1$s<span class="counter">(%2$d)</span></dt><dd>%3$s</dd>', $sidebar_name, count( $widget_names ), implode('', $widgets ) );
		}
		$info[] = '</dl>';
		return implode('', $info );
	}

}



class WTAIU_WP_Dependencies_Panel extends WTAIU_Panel {

	protected $dependencies;

	public function __construct( $label = 'Dependencies Used', $id = 'wtaiu-dependencies-panel', $plugin_file = '' ){
		parent::__construct( $label, $id, $plugin_file );
		$this->dependencies = array();
	}

	public function process_dependency_obj( WP_Dependencies $dep ){
		$deps = array_intersect_key( $dep->registered, $dep->groups );
		foreach( $deps as $d ){
			if( isset( $d->src ) && $d->src != '' )
				$this->dependencies[] = sprintf('<li><a href="%2$s">%1$s</a></li>', $d->handle, $d->src );
		}

		$this->label .= sprintf('<span class="counter">(%d)</span>', count( $this->dependencies ) );

	}

	public function get_content(){
		return '<ul title="This lists all enqueued files, not just enqueued files from your theme.">' . implode('', $this->dependencies ) . '</ul>';
	}

}



class WTAIU_Scripts_Panel extends WTAIU_WP_Dependencies_Panel {
	public function __construct( $plugin_file = '' ){
		parent::__construct('Enqueued Scripts', 'wtaiu-enqueued-scripts', $plugin_file);
	}

	public function setup(){
		add_action( 'wp_footer', array( $this, 'find_enqueued_scripts' ), 1 );
	}

	public function find_enqueued_scripts(){
		global $wp_scripts;
		$this->process_dependency_obj( $wp_scripts );
	}

}



class WTAIU_Styles_Panel extends WTAIU_WP_Dependencies_Panel {
	public function __construct( $plugin_file = '' ){
		parent::__construct('Enqueued Styles', 'wtaiu-enqueued-styles', $plugin_file );
	}

	public function setup(){
		add_action( 'wp_footer', array( $this, 'find_enqueued_styles' ), 1 );
	}

	public function find_enqueued_styles(){
		global $wp_styles;
		$this->process_dependency_obj( $wp_styles );
	}

}


class WTAIU_IP_Addresses_Panel extends WTAIU_Debug_Panel {

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'IP Addresses', 'wtaiu-ip-addresses-panel', $plugin_file );
		$this->default_open_state = 'closed';
	}

	public function activate(){
		$this->find_public_ip();
	}

	public function find_public_ip(){
		/*
			The same script that runs ip.phplug.in is included in what-is-my-ip.php.
			If you don't want to use my IP finding site, you can use one of these alternatives.
				http://bot.whatismyipaddress.com/
				http://curlmyip.com/
				http://icanhazip.com/
		*/

		$find_public_ip_url = apply_filters('wtaiu_find_public_ip_url', 'http://ip.phplug.in/' );

		$args = array(
			'user-agent' => sprintf(
				'WordPress/%s; What Template Am I Using/%s; %s',
				get_bloginfo( 'version' ),
				What_Template_Am_I_Using::VERSION,
				get_bloginfo( 'url' )
			)
		); 

		$response = wp_remote_get( $find_public_ip_url, $args );
		if( ! is_wp_error( $response ) ){
			$ip = wp_remote_retrieve_body( $response );
			// The response body is expected to be a plain text IP address only.
			update_site_option( 'wtaiu-server-ip', $ip );
			return $ip;
		}
		return $response;
	}

	public function get_public_server_ip(){
		$ip = get_site_option( 'wtaiu-server-ip', '' );
		if( $ip != '' )
			return $ip;

		$ip = $this->find_public_ip();
		if( ! is_wp_error( $ip ) )
			return $ip;

		return 'unknown';
	}

	public function deactivate(){
		delete_option( 'wtaiu-server-ip' );
	}

	public function get_content(){
		$your_ip	= esc_html( $_SERVER['REMOTE_ADDR'] );
		$server_ip	= esc_html( $_SERVER['SERVER_ADDR'] );
		$dns_ip		= gethostbyname( $_SERVER['HTTP_HOST'] );
		$public_server_ip = $this->get_public_server_ip();

$info=<<<INFO

	<table class="info-table">
		<tbody>
			<tr>
				<th scope="row" title="This is \$_SERVER['REMOTE_ADDR']">Your IP</th>
				<td>{$your_ip}</td>
			</tr>
			<tr>
				<th scope="row" title="This is \$_SERVER['SERVER_ADDR']">Server IP</th>
				<td>{$server_ip}</td>
			</tr>
			<tr>
				<th scope="row" title="This is the IP that you connect to when visiting {$_SERVER['HTTP_HOST']}">Server Public IP</th>
				<td>{$public_server_ip}</td>
			</tr>
			<tr>
				<th scope="row" title="DNS lookup for {$_SERVER['HTTP_HOST']}">Domain IP (DNS)</th>
				<td>{$dns_ip}</td>
			</tr>
		</tbody>
	</table>

INFO;

		return $info;
	}

}


class WTAIU_Server_Info_Panel extends WTAIU_Debug_Panel {

	// const VERSION = '0.1';

	public function __construct( $plugin_file = '' ){
		parent::__construct( 'Server Information', 'wtaiu-server-info-panel', $plugin_file );
		$this->default_open_state = 'closed';
	}

	/*
	// If you need to add your own assets, do it here.
	public function setup(){
		wp_enqueue_style('server-info-panel', plugins_url( '/css/server-info-panel.css', __FILE__ ), array('wtaiu'), self::VERSION );
	}
	*/

	public function get_content(){
		$info = array();
		$info[] = '<dl class="info-list">';
		foreach( $_SERVER as $key => $value )
			$info[] = sprintf('<dt>%1$s</dt><dd>%2$s</dd>', $key, $value );
		$info[] = '</dl>';
		return implode('', $info );
	}

}