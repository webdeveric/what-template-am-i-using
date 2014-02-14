<?php
/*
Plugin Name: What Template Am I Using
Plugin URI: http://phplug.in/
Plugin Group: Utilities
Author: Eric King
Author URI: http://webdeveric.com/
Description: This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.
Version: 0.1.7

----------------------------------------------------------------------------------------------------

If you want to add your own information to the sidebar panel, you just need to create a class that
extends WTAIU_Panel.

Take a look at inc/core-panels.php for examples.

----------------------------------------------------------------------------------------------------

Here is how you can filter the handle text.

add_filter('wtaiu_handle_text', function( $text ){
	return 'Your Custom Text Here';
} );

*/

include __DIR__ . '/inc/PriorityQueueInsertionOrder.php';
include __DIR__ . '/inc/wtaiu-panel.php';
include __DIR__ . '/inc/core-panels.php';


class What_Template_Am_I_Using {

	const VERSION = '0.1.6';

	protected static $panels;
	protected static $user_data;

	public static function init(){

		self::$panels = new PriorityQueueInsertionOrder();

		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );

		add_action( 'init',									array( __CLASS__, 'setup' ) );
		add_action( 'admin_init',							array( __CLASS__, 'check_for_upgrade' ) );

		add_action( 'wp_ajax_wtaiu_save_data',				array( __CLASS__, 'wtaiu_save_data') );
		add_action( 'wp_ajax_wtaiu_save_close_sidebar',		array( __CLASS__, 'wtaiu_save_close_sidebar') );

		add_action( 'personal_options',						array( __CLASS__, 'profile_options'), 10, 1 );
		add_action( 'personal_options_update',				array( __CLASS__, 'update_profile_options'), 10, 1 );
		add_action( 'edit_user_profile_update',				array( __CLASS__, 'update_profile_options'), 10, 1 );
	}

	public static function check_for_upgrade(){

		$wtaiu_db_version = get_option('wtaiu-version', '0.1.4' );

		if( version_compare( $wtaiu_db_version, self::VERSION, '<' ) ){

			switch( $wtaiu_db_version ){
				case '0.1.4':
				case '0.1.5':

					$users = get_users( array(
						'role' => 'administrator',
						'fields' => 'ID'
					) );

					foreach( $users as $user_id )
						update_user_meta( $user_id, 'wtaiu_show_sidebar', '1' );

				break;
			}

			update_site_option('wtaiu-version', self::VERSION );

		}

	}

	public static function setup(){
		if( ! is_admin() && current_user_can( 'edit_theme_options' ) ){
			$user = wp_get_current_user();
			if( $user->wtaiu_show_sidebar == '1' ){
				self::enqueue_assets();
				add_action( 'wp_footer', array( __CLASS__, 'output' ), PHP_INT_MAX );
			}
		}
	}

	public static function activate(){
		// Make the sidebar shown for the person that activated the plugin.
		// Everyone else has to visit their profile page to enable the sidebar if they want to see it.
		$user_id = get_current_user_id();
		if( $user_id > 0 ){
			update_user_meta( $user_id, 'wtaiu_show_sidebar', '1' );
		}

		foreach( self::$panels as $panel ){
			$panel->activate();
		}

	}

	public static function deactivate(){

		delete_site_option( 'wtaiu-version' );

		$meta_keys = array(
			'wtaiu_sidebar_data',
			'wtaiu_show_sidebar'
		);
		foreach( $meta_keys as $key ){
			delete_metadata( 'user', 0, $key, '', true );
		}

		foreach( self::$panels as $panel ){
			$panel->deactivate();
		}

	}

	public static function update_profile_options( $user_id ){
		if( current_user_can( 'edit_user', $user_id ) && user_can( $user_id, 'edit_theme_options' ) )
			update_user_meta( $user_id, 'wtaiu_show_sidebar', filter_has_var( INPUT_POST, 'wtaiu_show_sidebar' ) ? '1' : '0' );
	}

	public static function profile_options( $user ){
		if( ! user_can( $user, 'edit_theme_options' ) )
			return;
	?>
		<tr>
			<th scope="row"><?php _e('<abbr title="What Template Am I Using?">WTAIU</abbr> Sidebar')?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">Sidebar</legend>
					<label for="wtaiu_show_sidebar"><input type="checkbox" name="wtaiu_show_sidebar" id="wtaiu_show_sidebar" value="1" <?php checked('1', $user->wtaiu_show_sidebar ); ?> /> <?php _e('Show the sidebar when viewing site'); ?></label>
				</fieldset>
			</td>
		</tr>
	<?php
	}

	public static function wtaiu_save_data(){
		$user_id = get_current_user_id();

		$data = array();
		if( filter_has_var( INPUT_POST, 'open' ) && $_POST['open'] == 1 || $_POST['open'] == 0 )
			$data['open'] = (int)$_POST['open'];

		if( filter_has_var( INPUT_POST, 'panels' ) && is_array( $_POST['panels'] ) )
			$data['panels'] = $_POST['panels'];

		update_user_meta( $user_id, 'wtaiu_sidebar_data', $data );
		wp_send_json_success();
		die();
	}

	public static function wtaiu_save_close_sidebar(){
		$user_id = get_current_user_id();
		delete_user_meta( $user_id, 'wtaiu_show_sidebar' );
		wp_send_json_success();
		die();
	}

	public static function getPanels(){
		return self::$panels;
	}

	public static function addPanel( WTAIU_Panel $panel, $priority = 1 ){
		self::$panels->insert( $panel, $priority );
	}

	public static function removePanel( WTAIU_Panel $panel ){
		self::$panels->remove( $panel );
	}

	public static function enqueue_assets(){
		wp_enqueue_style('wtaiu', plugins_url( '/css/dist/what-template-am-i-using.min.css', __FILE__ ), array('dashicons', 'open-sans'), self::VERSION );
		wp_enqueue_script('wtaiu', plugins_url( '/js/dist/what-template-am-i-using.min.js', __FILE__ ), array('jquery', 'jquery-ui-sortable' ), self::VERSION );

		self::$user_data = get_user_meta( get_current_user_id(), 'wtaiu_sidebar_data', true );

		wp_localize_script('wtaiu', 'wtaiu', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'data' => self::$user_data
		) );
	}

	public static function output(){
		
		self::$panels->setExtractFlags( SplPriorityQueue ::EXTR_DATA );


		$sidebar_open = isset( self::$user_data, self::$user_data['open'] ) && self::$user_data['open'] == 1;

		$user_panels = isset( self::$user_data, self::$user_data['panels'] ) ? self::$user_data['panels'] : array();

		// var_dump( self::$user_data );

		$items = array();
		$sorted_items = array();

		foreach( self::$panels as $panel ){
			$label = $panel->get_label();
			$content = $panel->get_content();
			$id	= $panel->get_id();
			
			$extra_class = '';

			if( isset( $user_panels[ $id ] ) ){
				$extra_class = $user_panels[ $id ] == 1 ? 'open' : 'closed';
			} else {
				$extra_class = $panel->getDefaultOpenState();
			}

			$items[ $id ] = sprintf('<li class="panel %4$s" id="%3$s">
				<div class="panel-header">
					<div class="label">%1$s</div><div class="open-toggle-button"></div>
				</div>
				<div class="content">%2$s</div>
			</li>', $label, $content, $id, $extra_class );
		}

		foreach( $user_panels as $id => $open ){
			if( isset( $items[ $id ] ) ){
				$sorted_items[ $id ] = $items[ $id ];
				unset( $items[ $id ] );
			}
		}

		?>
		<div id="wtaiu" <?php if( $sidebar_open ) echo 'class="open"'; ?>>
			<a id="wtaiu-handle" title="Click to toggle"><span><?php echo apply_filters('wtaiu_handle_text', 'What Template Am I Using?' ); ?></span></a>
			<a id="wtaiu-close" title="Click to remove from page"></a>

			<menu type="context" id="wtaiu-context-menu">
				<menuitem
					type="command"
					icon="<?php echo plugins_url( '/imgs/up-arrow.png', __FILE__ ); ?>"
					label="Close all panels"
					class="close-all"
				></menuitem>
				<menuitem
					type="command"
					icon="<?php echo plugins_url( '/imgs/down-arrow.png', __FILE__ ); ?>"
					label="Open all panels"
					class="open-all"
				></menuitem>
			</menu>

			<ul id="wtaiu-data">
				<?php
					// Print out the sorted items.
					echo implode('', $sorted_items );
					// Print out any remaining items that may have been added to the sidebar after the user had saved their sort preference.
					echo implode('', $items );
				?>
			</ul>
		</div>
		<?php
	}

}

What_Template_Am_I_Using::init();
What_Template_Am_I_Using::addPanel( new WTAIU_Template_Panel(), 100 );
What_Template_Am_I_Using::addPanel( new WTAIU_General_Info_Panel(), 100 );
What_Template_Am_I_Using::addPanel( new WTAIU_Additional_Files_Panel(), 100 );
What_Template_Am_I_Using::addPanel( new WTAIU_Dynamic_Sidebar_Info_Panel(), 100 );
What_Template_Am_I_Using::addPanel( new WTAIU_Scripts_Panel(), 100 );
What_Template_Am_I_Using::addPanel( new WTAIU_Styles_Panel(), 100 );

if( WP_DEBUG ){
	What_Template_Am_I_Using::addPanel( new WTAIU_IP_Addresses_Panel(), 100 );
	What_Template_Am_I_Using::addPanel( new WTAIU_Server_Info_Panel(), 100 );
}