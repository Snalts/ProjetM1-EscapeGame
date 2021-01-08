<?php

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class iPanorama_List_Table_Items extends WP_List_Table {
	function __construct() {
		parent::__construct(array(
			'singular'=> IPANORAMA_PLUGIN_NAME . '_item',
			'plural' => IPANORAMA_PLUGIN_NAME . '_items',
			'ajax' => false
		));
	}
	
	function handle_row_actions( $post, $column_name, $primary ) {
		return '';
	}
	
	function joinPaths() {
		$paths = array();
		
		foreach(func_get_args() as $arg) {
			if($arg !== '') {
				$paths[] = $arg;
			}
		}
		
		return preg_replace('#/+#','/',join('/', $paths));
	}
	
	function filesystem_method() {
		return 'direct';
	}
	
	function request_filesystem_credentials() {
		return true;
	}
	
	function getFileSystem() {
		global $wp_filesystem;
		$result = true;
		
		if(!$wp_filesystem) {
			require_once(ABSPATH . '/wp-admin/includes/file.php');
			
			add_filter('filesystem_method', array( $this, 'filesystem_method'));
			add_filter('request_filesystem_credentials', array( $this, 'request_filesystem_credentials'));
			
			$credentials = request_filesystem_credentials(site_url(), '', true, false, null );
			
			$result = WP_Filesystem($credentials);
			
			remove_filter('filesystem_method', array( $this, 'filesystem_method'));
			remove_filter('request_filesystem_credentials', array( $this, 'request_filesystem_credentials'));
		}
		
		if($result)
			return $wp_filesystem;
		return null;
	}
	
	function column_default($item, $column_name){
		switch($column_name){
			case 'title':
			case 'active':
			case 'shortcode':
			case 'author':
			case 'editor':
			case 'created':
			case 'modified':
			case 'id':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}
	
	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s">',
			/*%1$s*/ $this->_args['singular'],
			/*%2$s*/ $item['id']
		);
	}
	
	function column_title($item) {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED);
		
		if(current_user_can('manage_options') || get_current_user_id()==$item['author']) {
			$actions = array(
				'edit'      => sprintf('<a href="?page=%s&id=%s">%s</a>', IPANORAMA_PLUGIN_NAME . '_item', $item['id'], esc_html__('Edit', IPANORAMA_PLUGIN_NAME)),
				'copy'      => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">%s</a>', $page, 'duplicate', $item['id'], wp_create_nonce(IPANORAMA_PLUGIN_NAME), esc_html__('Duplicate', IPANORAMA_PLUGIN_NAME)),
				'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">%s</a>', $page, 'delete', $item['id'], wp_create_nonce(IPANORAMA_PLUGIN_NAME), esc_html__('Delete', IPANORAMA_PLUGIN_NAME)),
			);
			
			return sprintf('<a href="?page=%1$s&id=%2$s" class="row-title">%3$s</a> %4$s',
				/*%1$s*/ IPANORAMA_PLUGIN_NAME . '_item',
				/*%2$s*/ $item['id'],
				/*%3$s*/ $item['title'],
				/*%4$s*/ $this->row_actions($actions)
			);
		} else {
			$actions = array();
			return sprintf('<strong>%1$s</strong>',
				/*%1$s*/ $item['title']
			);
		}
	}
	
	function column_active($item) {
		if(current_user_can('manage_options') || get_current_user_id()==$item['author']) {
			return sprintf(
				'<div class="ipanorama-toggle ipanorama-%1$s" data-id="%2$s">&nbsp;</div>',
				/*%1$s*/ ($item['active'] ? 'checked' : 'unchecked'),
				/*%2$s*/ $item['id']
			);
		} else {
			return sprintf(
				'<div class="ipanorama-toggle ipanorama-readonly ipanorama-%1$s" data-id="%2$s">&nbsp;</div>',
				/*%1$s*/ ($item['active'] ? 'checked' : 'unchecked'),
				/*%2$s*/ $item['id']
			);
		}
	}
	
	function column_shortcode($item) {
		return sprintf('<code>[ipano id="%1$s"]</code>',
			/*%1$s*/ $item['id']
		);
	}
	
	function column_author( $item ) {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
		$args = array(
			'page'   => $page,
			'author' => $item['author']
		);
		$url = add_query_arg($args, 'admin.php');
		
		return sprintf(
			'<a href="%1$s">%2$s</a>',
			/*%1$s*/ esc_url( $url ),
			/*%2$s*/ get_the_author_meta('display_name', $item['author'])
		);
	}
	
	function column_editor( $item ) {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
		$args = array(
			'page'   => $page,
			'editor' => $item['editor']
		);
		$url = add_query_arg($args, 'admin.php');
		
		return sprintf(
			'<a href="%1$s">%2$s</a>',
			/*%1$s*/ esc_url( $url ),
			/*%2$s*/ get_the_author_meta('display_name', $item['editor'])
		);
	}
	
	function column_created( $item ) {
		$m_time = mysql2date( esc_html__( 'Y/m/d g:i:s a' ), $item['created'] );
		$h_time = mysql2date( esc_html__( 'Y/m/d' ), $item['created'] );
		
		return sprintf(
			'<abbr title="%1$s">%2$s</abbr>',
			/*$1%s*/ $m_time,
			/*$2%s*/ $h_time
		);
	}
	
	function column_modified( $item ) {
		$m_time = mysql2date( esc_html__( 'Y/m/d g:i:s a' ), $item['modified'] );
		$h_time = mysql2date( esc_html__( 'Y/m/d' ), $item['modified'] );
		
		return sprintf(
			'<abbr title="%1$s">%2$s</abbr>',
			/*$1%s*/ $m_time,
			/*$2%s*/ $h_time
		);
	}
	
	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox">',
			'title'     => esc_html__('Title', IPANORAMA_PLUGIN_NAME),
			'active'    => esc_html__('Active', IPANORAMA_PLUGIN_NAME),
			'shortcode' => esc_html__('Shortcode', IPANORAMA_PLUGIN_NAME),
			'author'    => esc_html__('Author', IPANORAMA_PLUGIN_NAME),
			'editor'    => esc_html__('Editor', IPANORAMA_PLUGIN_NAME),
			'created'   => esc_html__('Created', IPANORAMA_PLUGIN_NAME),
			'modified'  => esc_html__('Modified', IPANORAMA_PLUGIN_NAME)
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$columns = array(
			'title'     => array('title',false),
			'active'    => array('active',false),
			'author'    => array('author',false),
			'editor'    => array('editor',false),
			'created'   => array('created',false),
			'modified'  => array('modified',false)
		);
		return $columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete'
		);
		return $actions;
	}
	
	function process_bulk_action() {
		$access = false;
		if( isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
			$nonce  = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
			$action = 'bulk-' . $this->_args['plural'];
			
			if(wp_verify_nonce($nonce, $action)) {
				$access = true;
			}
		}
		
		if(!$access) {
			return;
		}
		
		if('delete' === $this->current_action()) {
			global $wpdb;
			$table = $wpdb->prefix . IPANORAMA_PLUGIN_NAME;
			
			$items = filter_input(INPUT_GET, $this->_args['singular'], FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
			foreach($items as $id) {
				$result = false;
				
				$query = $wpdb->prepare( 'SELECT * FROM ' . $table . ' WHERE id=%s', $id);
				$item = $wpdb->get_row($query, OBJECT);
				if($item && (current_user_can('manage_options') || get_current_user_id()==$item->author) ) {
					$result = $wpdb->delete($table, ['id'=>$id], ['%d']);
					
					//======================================
					// [filemanager] delete file
					if(result && wp_is_writable(IPANORAMA_PLUGIN_UPLOAD_DIR)) {
						$dir_root = $this->joinPaths(IPANORAMA_PLUGIN_UPLOAD_DIR, $item->id);
						
						$wp_filesystem = $this->getFileSystem();
						
						if($wp_filesystem) {
							if($wp_filesystem->is_dir($dir_root)) {
								$wp_filesystem->rmdir($dir_root, true);
							}
						}
					}
					//======================================
				}
			}
		}
	}
	
	function prepare_items() {
		$this->process_bulk_action();
		
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden = array();
		
		$itemsPerPage = 20;
		$currentPage = ($this->get_pagenum()-1) * $itemsPerPage;
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// make sql query
		global $wpdb;
		
		$table = $wpdb->prefix . IPANORAMA_PLUGIN_NAME;
		$orderby = (isset($_GET['orderby']) ? filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING ) : 'id');
		$order = (isset($_GET['order']) ? filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING ) : 'desc');
		$author = (isset($_GET['author']) ? filter_input( INPUT_GET, 'author', FILTER_SANITIZE_NUMBER_INT ) : NULL);
		$editor = (isset($_GET['editor']) ? filter_input( INPUT_GET, 'editor', FILTER_SANITIZE_NUMBER_INT ) : NULL);
		
		$sql = '';
		$total_items = 0;
		
		// database operations
		if(current_user_can('manage_options')) {
			if($author) {
				$sql = 'SELECT * FROM ' . $table . ' WHERE author=' . $author . ' ORDER BY ' . $orderby . ' ' . $order . ' LIMIT ' . $currentPage . ',' . $itemsPerPage;
				$total_items = $wpdb->query('SELECT id FROM ' . $table . ' WHERE author=' . $author);
			} else if($editor) {
				$sql = 'SELECT * FROM ' . $table . ' WHERE editor=' . $editor . ' ORDER BY ' . $orderby . ' ' . $order . ' LIMIT ' . $currentPage . ',' . $itemsPerPage;
				$total_items = $wpdb->query('SELECT id FROM ' . $table . ' WHERE editor=' . $editor);
			} else {
				$sql = 'SELECT * FROM ' . $table . ' ORDER BY ' . $orderby . ' ' . $order . ' LIMIT ' . $currentPage . ',' . $itemsPerPage;
				$total_items = $wpdb->query('SELECT id FROM ' . $table);
			}
		} else {
			$author = get_current_user_id();
			
			$sql = 'SELECT * FROM ' . $table . ' WHERE author=' . $author . ' ORDER BY ' . $orderby . ' ' . $order . ' LIMIT ' . $currentPage . ',' . $itemsPerPage;
			$total_items = $wpdb->query('SELECT id FROM ' . $table . ' WHERE author=' . $author);
		}
		
		$this->items = $wpdb->get_results($sql, 'ARRAY_A');
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil($total_items / $itemsPerPage)
		));
	}
}
?>