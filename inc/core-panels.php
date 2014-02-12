<?php
class WTAIU_Template_Panel extends WTAIU_Panel {

	public function __construct(){
		parent::__construct( 'Template', 'wtaiu-template-panel' );
	}

	public function get_content(){
		global $template;
		return str_replace( get_theme_root(), '', $template );
	}

}



class WTAIU_General_Info_Panel extends WTAIU_Panel {

	public function __construct(){
		parent::__construct( 'General Information', 'wtaiu-general-info-panel' );
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

	public function __construct(){
		parent::__construct( 'Additional Files Used', 'wtaiu-additional-files-panel' );
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

	public function __construct(){
		parent::__construct( 'Sidebar Information', 'wtaiu-dynamic-sidebar-info-panel' );
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

	public function __construct( $label = 'Dependencies Used', $id = 'wtaiu-dependencies-panel' ){
		parent::__construct( $label, $id );
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
	public function __construct(){
		parent::__construct('Enqueued Scripts', 'wtaiu-enqueued-scripts');
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
	public function __construct(){
		parent::__construct('Enqueued Styles', 'wtaiu-enqueued-styles');
	}

	public function setup(){
		add_action( 'wp_footer', array( $this, 'find_enqueued_styles' ), 1 );
	}

	public function find_enqueued_styles(){
		global $wp_styles;
		$this->process_dependency_obj( $wp_styles );
	}

}


class WTAIU_IP_Addresses_Panel extends WTAIU_Panel {

	public function __construct(){
		parent::__construct( 'IP Addresses', 'wtaiu-ip-addresses-panel' );
		$this->default_open_state = 'closed';
	}

	public function activate(){
		$this->findPublicIP();
	}

	public function findPublicIP(){
		$ip_url = plugins_url( '/what-is-my-ip.php', __FILE__ );
		$response = wp_remote_get( $ip_url );
		if( ! is_wp_error( $response ) ){
			$ip = wp_remote_retrieve_body( $response );
			update_option( 'wtaiu-server-ip', $ip );
			return $ip;
		}
		return $response;
	}

	public function deactivate(){
		delete_option( 'wtaiu-server-ip' );
	}

	public function get_content(){
		$your_ip	= esc_html( $_SERVER['REMOTE_ADDR'] );
		$server_ip	= esc_html( $_SERVER['SERVER_ADDR'] );
		$dns_ip		= gethostbyname( $_SERVER['HTTP_HOST'] );
		$public_server_ip = get_option( 'wtaiu-server-ip', 'unknown' );

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


class WTAIU_Server_Info_Panel extends WTAIU_Panel {

	const VERSION = '0.1';

	public function __construct(){
		parent::__construct( 'Server Information', 'wtaiu-server-info-panel' );
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