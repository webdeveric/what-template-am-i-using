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

$info=<<<INFO
	<table class="info-table">
		<thead>
			<tr>
				<th>Post Type</th>
				<th>Front</th>
				<th>Home</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{$post_type}</td>
				<td>{$front_page}</td>
				<td>{$home_page}</td>
			</tr>
		</tbody>
	</table>
INFO;

		return $info;
	}

}



class WTAIU_Additional_Files_Panel extends WTAIU_Panel {

	private $files;

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
	}

	public function get_content(){
		$your_ip = $_SERVER['REMOTE_ADDR'];
		$server_ip = $_SERVER['SERVER_ADDR'];

$info=<<<INFO
	<table class="info-table">
		<thead>
			<tr>
				<th>Your IP</th>
				<th>Server IP</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{$your_ip}</td>
				<td>{$server_ip}</td>
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
	}

	public function setup(){
		wp_enqueue_style('server-info-panel', plugins_url( '/css/server-info-panel.css', __FILE__ ), array('wtaiu'), self::VERSION );
	}

	public function get_content(){
		$info = array();
		$info[] = '<dl id="server-info-list">';
		foreach( $_SERVER as $key => $value )
			$info[] = sprintf('<dt>%1$s</dt><dd>%2$s</dd>', $key, $value );
		$info[] = '</dl>';
		return implode('', $info );
	}

}