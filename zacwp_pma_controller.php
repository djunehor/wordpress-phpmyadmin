<?php

/**
 * Controller Class
 *
 */
class ZacWP_PMA_Controller {
	
	private $model;
	private $rows_per_page;
	private $csv;
	private $slug;
	private $url;

	public function __construct($table_name = null) {
		
		// read settings
		$settings = parse_ini_file(file_exists(ZACWP_PMA_FILE_INI ) ? ZACWP_PMA_FILE_INI : ZACWP_PMA_FILE_INI_DEFAULT);
		$this->rows_per_page = $settings['rows_per_page'];
		
		// csv settings
		$this->csv['file_name'] = $settings['csv_file_name'];
		$this->csv['encoding']  = $settings['csv_encoding'];
		
		// database
		if($table_name) $this->model = new ZacWp_Model($table_name);

		// slugs & menu
		$this->slug['list']     = $settings['base_slug'] . '_list';
		$this->slug['list_table']     = $settings['base_slug'] . '_list_table';
		$this->slug['add']      = $settings['base_slug'] . '_add';
		$this->slug['add_table']      = $settings['base_slug'] . '_add_table';
		$this->slug['edit']     = $settings['base_slug'] . '_edit';
		$this->slug['edit_table']     = $settings['base_slug'] . '_edit_table';
		$this->slug['settings'] = $settings['base_slug'] . '_settings';

		add_action('init', array($this, 'zacwp_export_csv'));
		add_action('admin_menu', array($this, 'zacwp_add_menu'));

		$this->url['list_table']     = admin_url('admin.php?page=' . $this->slug['list_table']);
		$this->url['list']     = admin_url('admin.php?page=' . $this->slug['list']);
		$this->url['edit']     = admin_url('admin.php?page=' . $this->slug['edit']);
		$this->url['edit_table']     = admin_url('admin.php?page=' . $this->slug['edit_table']);
		$this->url['add']      = admin_url('admin.php?page=' . $this->slug['add']);
		$this->url['add_table']      = admin_url('admin.php?page=' . $this->slug['add_table']);
		$this->url['settings'] = admin_url('admin.php?page=' . $this->slug['settings']);
	}

	public function zacwp_add_menu() {
		add_menu_page('PhpMyAdmin Table Manager - Table List', 'PhpMyAdmin Manager', 'manage_options', $this->slug['list_table'], array($this, 'zacwp_table_all'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table', 'Table', 'manage_options', $this->slug['list'], array($this, 'zacwp_list_all'));
		add_submenu_page($this->slug['list_table'], 'PhpMyAdmin Table Manager - Add Table', 'Add Table', 'manage_options', $this->slug['add_table'], array($this, 'zacwp_add_table'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Add Record', 'Add Record', 'manage_options', $this->slug['add'], array($this, 'zacwp_add_new'));
		add_submenu_page($this->slug['list_table'], 'PhpMyAdmin Table Manager - Settings', 'Settings', 'manage_options', $this->slug['settings'], array($this, 'zacwp_settings'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table - Edit Record', 'Edit', 'manage_options', $this->slug['edit'], array($this, 'zacwp_edit'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table Edit', 'Edit Table', 'manage_options', $this->slug['edit_table'], array($this, 'zacwp_edit_table'));
	}

	public function zacwp_table_all() {

		if (current_user_can('manage_options')) {
			global $wpdb;
			// export csv via post
		$task_id = mt_rand();
		$_SESSION['export'] = $task_id;
		$wpTables = [
			'comments',
			'commentmeta',
			'links',
			'usermeta',
			'users',
			'term_taxonomy',
			'terms',
			'term_relationships',
			'postmeta',
			'posts',
			'options'
		];

		// key word search
		$key_word = "";
		if (isset($_POST['search']))	$key_word = sanitize_text_field($_POST['search']);
		if (isset($_GET['search']))		$key_word = sanitize_text_field($_GET['search']);

		$key_word = stripslashes_deep($key_word);

		// order by
		$order_by = "";
		$order = "";
		if (isset($_GET['orderby'])) {
			$order_by = sanitize_text_field($_GET['orderby']);
			$order = sanitize_text_field($_GET['order']);
		}

		// manage record quantity
		$begin_row = 0;
		if (isset($_GET['beginrow'])){
			if (is_numeric($_GET['beginrow'])){
				$begin_row = filter_var($_GET['beginrow'], FILTER_SANITIZE_NUMBER_INT);
			}
		}
		$result = $wpdb->get_results('SHOW TABLES');
		$total = count($result);	// count all data rows
		$next_begin_row = $begin_row + $this->rows_per_page;
		if ($total < $next_begin_row) $next_begin_row = $total;
		$last_begin_row = $this->rows_per_page * (floor(($total - 1) / $this->rows_per_page));

		// stuff to display
		$db_name = $wpdb->dbname;
		$primary_key = null;

		include(ZACWP_PMA_TABLE_VIEW_LIST);
		}
	}

	public function zacwp_list_all($status = false, $message = false) {
		if (current_user_can('manage_options')) {
			// export csv via post
			$task_id            = mt_rand();
			$_SESSION['export'] = $task_id;

			// key word search
			$key_word = "";
			if ( isset( $_POST['search'] ) ) {
				$key_word = sanitize_text_field( $_POST['search'] );
			}
			if ( isset( $_GET['search'] ) ) {
				$key_word = sanitize_text_field( $_GET['search'] );
			}

			$key_word = stripslashes_deep( $key_word );

			// order by
			$order_by = "";
			$order    = "";
			if ( isset( $_GET['orderby'] ) ) {
				$order_by = $_GET['orderby'];
				$order    = sanitize_text_field( $_GET['order'] );
			}

			// manage record quantity
			$begin_row = 0;
			if ( isset( $_GET['beginrow'] ) && is_numeric( $_GET['beginrow'] ) ) {
				$begin_row = filter_var( $_GET['beginrow'], FILTER_SANITIZE_NUMBER_INT );
			}
			$total          = $this->model->count_rows( $key_word );    // count all data rows
			$next_begin_row = $begin_row + $this->rows_per_page;
			if ( $total < $next_begin_row ) {
				$next_begin_row = $total;
			}
			$last_begin_row = $this->rows_per_page * ( floor( ( $total - 1 ) / $this->rows_per_page ) );

			// stuff to display
			$table_name  = $this->model->get_table_name();
			$primary_key = $this->model->get_primary_key();
			$columns     = $this->model->get_columns();
			$result      = $this->model->select( $key_word, $order_by, $order, $begin_row, $this->rows_per_page );

			include( ZACWP_PMA_FILE_VIEW_LIST );
		}
	}

	public function zacwp_add_table() {
		$add_nonce = wp_create_nonce( 'zacwp_table_add_table' );
		include(ZACWP_PMA_FILE_TABLE_ADD);
	}

	public function zacwp_add_new($status = false, $message = false) {

		$table_name = $this->model->get_table_name();
		$primary_key = $this->model->get_primary_key();
		$columns = $this->model->get_columns();
		$new_id = $this->model->get_new_candidate_id();
		$add_nonce = wp_create_nonce( 'zacwp_table_add_row' );

		include(ZACWP_PMA_FILE_VIEW_ADD);
	}

	public function zacwp_export_csv() {

		if (isset($_GET['export']) && current_user_can('manage_options')) {

			// output contents
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . $this->csv['file_name']);

			// field names
			foreach ($this->model->get_columns() as $name => $type) {
				print($name . ZACWP_PMA_DELIMITER);
			}
			print(ZACWP_PMA_NEW_LINE);

			// data
			foreach ($this->model->select_all() as $row ){
				foreach ($row as $k => $v ) {
					$str = preg_replace('/"/', '""', $v);
					print("\"" . mb_convert_encoding($str, $this->csv['encoding'], 'UTF-8')."\"" . ZACWP_PMA_DELIMITER);
		      	}
				print(ZACWP_PMA_NEW_LINE);
			}
			exit;
		}
	}

	public function zacwp_edit_table() {

		if (current_user_can('manage_options')) {
			global $wpdb;
			$static     = [ 'id', 'created_at', 'updated_at' ];


			if ( isset( $_POST['update-table'] )
			     && isset($_POST['zacwp_table_edit_nonce'])
			     && wp_verify_nonce( sanitize_text_field($_REQUEST['zacwp_table_edit_nonce']), 'zacwp_table_edit_table' )
			) {
				$table_name = $this->model->get_table_name();
				$oldColumns = $this->model->get_columns();
				$columns = $this->model->get_column_attributes();
				try {
					$cols    = sanitize_post($_POST['tab-col']);

					foreach ( $cols['name'] as $i => $value ) {
						if ( in_array( $cols['name'][$i], $static ) ) {
							continue;
						}
						$this->zacwp_add_not_exist( $table_name, $cols['name'][ $i ], $cols['type'][ $i ] );
					}

					$new = new ZacWP_Model($table_name);
					$columns = $new->get_column_attributes();

					foreach ( $oldColumns as $name => $value ) {

						//column not in newly sent column and not static
						if ( ! in_array( $name, $cols['name'] ) && !in_array( $name, $static ) ) {
							$this->zacwp_drop_if_exist( $table_name, $name );
						}
					}
					$updated = new ZacWP_Model($table_name);
					$columns = $updated->get_column_attributes();

					$message = "Table successfully updated";
					$status  = "updated";
				}
				catch ( \Exception $exception ) {
					$message = "Error updating table : " . $exception->getMessage();
					$status  = "error";
				}


				$edit_nonce = wp_create_nonce( 'zacwp_table_edit_table' );
				include( ZACWP_PMA_FILE_TABLE_EDIT );
				//on create table
			}
			else if ( isset( $_POST['add-table'] )
			          && isset($_POST['zacwp_table_add_nonce'])
			          && wp_verify_nonce( sanitize_text_field($_REQUEST['zacwp_table_add_nonce']), 'zacwp_table_add_table' )
			           ) {
				$table = $wpdb->prefix.sanitize_text_field($_POST['new_table_name']);
				if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) == $table ) {
					$message = "Table $table already exists!";
					$status  = "error";

				} elseif ( ! isset( $_POST['tab-col'] ) ) {
					$message = "Table column(s) not specified!";
					$status  = "error";
				} else {

					try {
						$sql = "CREATE TABLE `$table` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
						if ( $wpdb->query( $sql ) ) {
							$wpdb->query( "ALTER TABLE `$table`
  ADD PRIMARY KEY (`id`);" );
							$wpdb->query( "ALTER TABLE `$table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;" );
						}

						$cols = sanitize_post($_POST['tab-col']);

						foreach ( $cols['name'] as $i => $value ) {
							$this->zacwp_add_not_exist( $table, $cols['name'][ $i ], $cols['type'][ $i ] );
						}


						$message = "Table <b>$table</b> successfully created";
						$status  = "updated";
					}
					catch ( \Exception $exception ) {
						$message = $exception->getMessage();
						$status  = "error";
					}
				}
				$add_nonce = wp_create_nonce( 'zacwp_table_add_table' );
				include( ZACWP_PMA_FILE_TABLE_ADD );
			}
			else {
				$table_name  = $this->model->get_table_name();
				$primary_key = $this->model->get_primary_key();
				$columns     = $this->model->get_column_attributes();
				$new_id      = $this->model->get_new_candidate_id();

				$edit_nonce = wp_create_nonce( 'zacwp_table_edit_table' );
				include( ZACWP_PMA_FILE_TABLE_EDIT );
			}
		}

	}

	public function zacwp_add_not_exist($table, $field, $type)
	{
		global $wpdb;
		$results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");
		if (!count($results))
		{
			$type = strtoupper($type);
			$sql = "ALTER TABLE  `".$table."` ADD `".$field."` ";
			$sql .= $type == 'VARCHAR' ? $type."(255)" : $type == 'INT' ? $type."(11)" : $type;
			$sql .= " NULL";
			$wpdb->query($sql);
		}
	}

	public function zacwp_drop_if_exist ($table, $field)
	{
		global $wpdb;
		$results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");
		if (count($results))
		{
			$sql = "ALTER TABLE `$table` DROP COLUMN `$field`;";
			$wpdb->query($sql);
		}
	}

	public function zacwp_edit() {

		if ( current_user_can( 'manage_options' ) ) {
			if ( isset( $_GET['id'] ) ) {
				$id = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT );;
			}
			if ( isset( $_POST['id'] ) ) {
				$id = filter_var( $_POST['id'], FILTER_SANITIZE_NUMBER_INT );
			}

			// on update
			if ( isset( $_POST['update'] )
			     && isset( $_POST['zacwp_table_update_row_nonce'] )
			     && wp_verify_nonce( sanitize_text_field( $_REQUEST['zacwp_table_update_row_nonce'] ), 'zacwp_table_update_row' )

			) {
				if ( $this->model->update( $_POST ) ) {
					$message = "Record successfully updated";
					$status  = "updated";
				} else {
					$message = "No rows were affected";
					$status  = "error";
				}
				$this->zacwp_edit_show($id, $status, $message);

				// on delete
			} else if ( isset( $_POST['delete'] )
			            && isset( $_POST['zacwp_table_delete_row_nonce'] )
			            && wp_verify_nonce( sanitize_text_field( $_REQUEST['zacwp_table_delete_row_nonce'] ), 'zacwp_table_delete_row' )

			) {
				if ( $this->model->delete( $id ) ) {
					$message = "Record successfully deleted";
					$status  = "updated";
				} else {
					$message = "Error deleting record";
					$status  = "error";
				}

				$this->zacwp_list_all($status, $message);

				// on insert via add new page
			} else if ( isset( $_POST['add'] )
			            && isset( $_POST['zacwp_table_add_row_nonce'] )
			            && wp_verify_nonce( sanitize_text_field( $_REQUEST['zacwp_table_add_row_nonce'] ), 'zacwp_table_add_row' )

			) {
				$data = sanitize_post( $_POST );
				$id = $this->model->insert( $data, ['created_at', 'updated_at'] );

				if ( $id ) {
					$message = "Record successfully inserted";
					$status  = "updated";
				} else {
					$message = "Error inserting record";
					$status  = "error";
				}
				//on insert row
				$this->zacwp_add_new($status, $message);
			}
			else {
				$this->zacwp_edit_show($id);
			}
		}
	}

	public function  zacwp_edit_show($id, $status = false, $message = false){
		$table_name = $this->model->get_table_name();
		$primary_key = $this->model->get_primary_key();
		$columns = $this->model->get_columns();
		$row = $this->model->get_row($id);
		$edit_nonce = wp_create_nonce( 'zacwp_table_update_row' );
		$delete_nonce = wp_create_nonce('zacwp_table_delete_row');

		include(ZACWP_PMA_FILE_VIEW_EDIT);
	}

	public function zacwp_settings() {
		
		// read settings file
		if(current_user_can('manage_options')) {
			$settings = parse_ini_file( ZACWP_PMA_FILE_INI );

			// update ini file
			if ( isset( $_POST['apply'] )
			     && isset( $_POST['zacwp_table_setting_apply_nonce'] )
			     && wp_verify_nonce( sanitize_text_field( $_REQUEST['zacwp_table_setting_apply_nonce'] ), 'zacwp_table_setting_apply' )

			) {

				// check table validity
				if(!isset($_POST['zacwp_rows_per_page'])
					|| !isset($_POST['zacwp_csv_file_name'])
					|| !isset($_POST['zacwp_csv_encoding'])
				) {
					$status = "error";
					$message = "All fields are required!";
				}
				elseif ( !is_numeric($_POST['zacwp_rows_per_page']) ) {
					$status = "error";
					$message = "Invalid rows per page!";
				} elseif ( !preg_match('/^[a-z0-9_]/', $_POST['zacwp_csv_file_name'])
				|| strpos(trim($_POST['zacwp_csv_file_name']), ' ') !== false
				) {
					$status = "error";
					$message = 'The csv name is invalid. File name can only contain "a-z", "0-9" and "_". Also the files must be lowercase.';
				}  else {

					// gather new setting params
					$settings['rows_per_page'] = sanitize_text_field($_POST['zacwp_rows_per_page']);
					$settings['csv_file_name'] = sanitize_text_field($_POST['zacwp_csv_file_name']).'.csv';
					$settings['csv_encoding'] = sanitize_text_field($_POST['zacwp_csv_encoding']);

					// update ini file
					$fp = fopen( ZACWP_PMA_FILE_INI, 'w' );
					foreach ( $settings as $k => $v ) {
						if ( false == fputs( $fp, "$k = $v" . ZACWP_PMA_NEW_LINE ) ) {
							$status = "error";
							$message = "Failed to save $k";
							break;
						}
						$status  = "updated";
						$message = "Settings successfully changed";
					}
					fclose( $fp );


				}

				// restore ini file with default settings
			}
			else if ( isset( $_POST['restore'] )
			          && isset( $_POST['zacwp_table_setting_restore_nonce'] )
			          && wp_verify_nonce( sanitize_text_field( $_REQUEST['zacwp_table_setting_restore_nonce'] ), 'zacwp_table_setting_restore' )

			) {

				if ( file_exists( ZACWP_PMA_FILE_INI_DEFAULT ) ) {
					copy( ZACWP_PMA_FILE_INI_DEFAULT, ZACWP_PMA_FILE_INI );
					$settings    = parse_ini_file( ZACWP_PMA_FILE_INI );

					$status  = "updated";
					$message = "Default settings successfully restored";

				} else {
					$status  = "error";
					$message = "Error: default config file not found";
				}
			}
			$settings    = parse_ini_file( ZACWP_PMA_FILE_INI );

			$apply_nonce = wp_create_nonce('zacwp_table_setting_apply');
			$restore_nonce = wp_create_nonce('zacwp_table_setting_restore');
			include( ZACWP_PMA_FILE_VIEW_SETTINGS );
		}
	}

	public function zacwp_valid_csv_name($name) {
		$array = explode(",", $name);
		if(count($array) != 2) return false;
		if(strtolower($array[1])) return false;
	}
	
}
?>