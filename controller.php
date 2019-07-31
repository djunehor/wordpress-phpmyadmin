<?php

/**
 * Controller Class
 *
 */
class Controller {
	
	private $model;
	private $rows_per_page;
	private $csv;
	private $slug;
	private $url;

	public function __construct($table_name = null) {
		
		// read settings
		$settings = parse_ini_file(FILE_INI);
		$this->rows_per_page = $settings['rows_per_page'];
		
		// csv settings
		$this->csv['file_name'] = $settings['csv_file_name'];
		$this->csv['encoding']  = $settings['csv_encoding'];
		
		// database
		if($table_name) $this->model = new Model($table_name);

		// slugs & menu
		$this->slug['list']     = $settings['base_slug'] . '_list';
		$this->slug['list_table']     = $settings['base_slug'] . '_list_table';
		$this->slug['add']      = $settings['base_slug'] . '_add';
		$this->slug['add_table']      = $settings['base_slug'] . '_add_table';
		$this->slug['edit']     = $settings['base_slug'] . '_edit';
		$this->slug['edit_table']     = $settings['base_slug'] . '_edit_table';
		$this->slug['settings'] = $settings['base_slug'] . '_settings';
		
		add_action('init', array($this, 'export_csv'));
		add_action('admin_menu', array($this, 'add_menu'));

		$this->url['list_table']     = admin_url('admin.php?page=' . $this->slug['list_table']);
		$this->url['list']     = admin_url('admin.php?page=' . $this->slug['list']);
		$this->url['edit']     = admin_url('admin.php?page=' . $this->slug['edit']);
		$this->url['edit_table']     = admin_url('admin.php?page=' . $this->slug['edit_table']);
		$this->url['add']      = admin_url('admin.php?page=' . $this->slug['add']);
		$this->url['add_table']      = admin_url('admin.php?page=' . $this->slug['add_table']);
		$this->url['settings'] = admin_url('admin.php?page=' . $this->slug['settings']);
	}

	public function add_menu() {
		add_menu_page('PhpMyAdmin Table Manager - Table List', 'PhpMyAdmin Manager', 'manage_options', $this->slug['list_table'], array($this, 'table_all'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table', 'Table', 'manage_options', $this->slug['list'], array($this, 'list_all'));
		add_submenu_page($this->slug['list_table'], 'PhpMyAdmin Table Manager - Add Table', 'Add Table', 'manage_options', $this->slug['add_table'], array($this, 'add_table'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Add Record', 'Add Record', 'manage_options', $this->slug['add'], array($this, 'add_new'));
		add_submenu_page($this->slug['list_table'], 'PhpMyAdmin Table Manager - Settings', 'Settings', 'manage_options', $this->slug['settings'], array($this, 'settings'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table - Edit Record', 'Edit', 'manage_options', $this->slug['edit'], array($this, 'edit'));
		add_submenu_page(null, 'PhpMyAdmin Table Manager - Table Edit', 'Edit Table', 'manage_options', $this->slug['edit_table'], array($this, 'edit_table'));
	}

	public function table_all() {

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
		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);

		// key word search
		$key_word = "";
		if (isset($_POST['search']))	$key_word = $_POST['search'];
		if (isset($_GET['search']))		$key_word = $_GET['search'];

		$key_word = stripslashes_deep($key_word);

		// order by
		$order_by = "";
		$order = "";
		if (isset($_GET['orderby'])) {
			$order_by = $_GET['orderby'];
			$order = $_GET['order'];
		}

		// manage record quantity
		$begin_row = 0;
		if (isset($_GET['beginrow'])){
			if (is_numeric($_GET['beginrow'])){
				$begin_row = $_GET['beginrow'];
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


		include(TABLE_VIEW_LIST);
	}

	public function list_all() {
		// export csv via post
		$task_id = mt_rand();
		$_SESSION['export'] = $task_id;
		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);
		
		// key word search
		$key_word = "";
		if (isset($_POST['search']))	$key_word = $_POST['search'];
		if (isset($_GET['search']))		$key_word = $_GET['search'];
		
		$key_word = stripslashes_deep($key_word);
		
		// order by
		$order_by = "";
		$order = "";
		if (isset($_GET['orderby'])) {
			$order_by = $_GET['orderby'];
			$order = $_GET['order'];
		}
		
		// manage record quantity
		$begin_row = 0;
		if (isset($_GET['beginrow'])){	
			if (is_numeric($_GET['beginrow'])){
				$begin_row = $_GET['beginrow'];
			}
		}
		$total = $this->model->count_rows($key_word);	// count all data rows
		$next_begin_row = $begin_row + $this->rows_per_page;
		if ($total < $next_begin_row) $next_begin_row = $total;
		$last_begin_row = $this->rows_per_page * (floor(($total - 1) / $this->rows_per_page));
		
		// stuff to display
		$table_name = $this->model->get_table_name();
		$primary_key = $this->model->get_primary_key();
		$columns = $this->model->get_columns();
		$result = $this->model->select($key_word, $order_by, $order, $begin_row, $this->rows_per_page);
		
		include(FILE_VIEW_LIST);
	}

	public function add_table() {

		include(FILE_TABLE_ADD);
	}

	public function add_new() {
		
		$table_name = $this->model->get_table_name();
		$primary_key = $this->model->get_primary_key();
		$columns = $this->model->get_columns();
		$new_id = $this->model->get_new_candidate_id();
		
		include(FILE_VIEW_ADD);
	}

	public function export_csv() {
		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);
		if (isset($_GET['export'])) {
			
			// output contents
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . $this->csv['file_name']);
			
			// field names
			foreach ($this->model->get_columns() as $name => $type) {
				print($name . DELMITER);
			}
			print(NEW_LINE);
			
			// data
			foreach ($this->model->select_all() as $row ){
				foreach ($row as $k => $v ) {
					$str = preg_replace('/"/', '""', $v);
					print("\"" . mb_convert_encoding($str, $this->csv['encoding'], 'UTF-8')."\"" . DELMITER);
		      	}
				print(NEW_LINE);
			}
			exit;
		}
	}

	public function edit_table() {

		global $wpdb;
		$message = "";
		$status = "";

		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);

		if (isset($_GET['id']))		$id = $_GET['id'];
		if (isset($_POST['id']))	$id = $_POST['id'];

		// on update
		else if(isset($_POST['update-table'])) {
			$table_name = $this->model->get_table_name();
			$static = ['id', 'created_at', 'updated_at'];

			try{
			$columns = $this->model->get_columns();
			$cols = $_POST['tab-col'];

			foreach ($cols['name'] as $i => $value) {
				if(in_array($columns['name'][$i], $static)) continue;
				$this->add_not_exist($table_name, $cols['name'][$i], $cols['type'][$i]);
			}
			$newColumns = $this->model->get_columns();

			foreach ($columns as $column) {

				if(!in_array($column, $newColumns) && in_array($columns['name'][$i], $static)) {
					$this->drop_if_exist($table_name, $column);
				}
			}
			$message = "Table successfully updated";
			$status  = "updated"; 
			} catch (\Exception $exception) {
				$message = "Error updating table : ".$exception->getMessage();
				$status = "error";
			}
			include(FILE_TABLE_EDIT);
			//on create table
		}  else if(isset($_POST['add-table'])) {
			$table = $_POST['new_table_name'];
			if($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
				$message = "Table $table already exists!";
				$status = "error";

			} elseif(!isset($_POST['tab-col'])) {
				$message = "Table column(s) not specified!";
				$status = "error";
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

					$cols = $_POST['tab-col'];

					foreach ( $cols['name'] as $i => $value ) {
						$this->add_not_exist( $table, $cols['name'][ $i ], $cols['type'][ $i ] );
					}


					$message = "Table successfully created";
					$status  = "success";
				} catch ( \Exception $exception ) {
					$message = $exception->getMessage();
					$status  = "updated"; 
				}
			}
			include(FILE_TABLE_ADD);
		} else {
			$table_name = $this->model->get_table_name();
			$primary_key = $this->model->get_primary_key();
			$columns = $this->model->get_column_attributes();
			$new_id = $this->model->get_new_candidate_id();
			include(FILE_TABLE_EDIT);
		}


	}

	public function add_not_exist($table, $field, $type)
	{
		global $wpdb;
		$results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");
		if (!count($results))
		{
			$sql = "ALTER TABLE  `".$table."` ADD `".$field."` ";
			$sql .= $type == 'VARCHAR' ? $type."(255)" : $type == 'INT' ? $type."(11)" : $type;
			$sql .= " NULL AFTER `updated_at`";
			$wpdb->query($sql);
		}
	}

	public function drop_if_exist ($table, $field)
	{
		global $wpdb;
		$results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");
		if (count($results))
		{
			$sql = "ALTER TABLE `$table` DROP `$field`;";
			$wpdb->query($sql);
		}
	}

	public function edit() {

		$message = "";
		$status = "";

		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);

		$id = "";
		if (isset($_GET['id']))		$id = $_GET['id'];
		if (isset($_POST['id']))	$id = $_POST['id'];

		// on update
		if (isset($_POST['update'])) {
			if ($this->model->update($_POST)) {
				$message = "Record successfully updated";
				$status  = "updated"; 
			} else {
				$message = "No rows were affected";
				$status = "error";
			}
			
		// on delete
		} else if(isset($_POST['delete'])) {
			if ($this->model->delete($id)) {
				$message = "Record successfully deleted";
				$status  = "updated"; 
			} else {
				$message = "Error deleting record";
				$status = "error";
			}
		
		// on insert via add new page
		} else if(isset($_POST['add'])) {
			$id = $this->model->insert($_POST);
			
			if ("" != $id) {
				$message = "Record successfully inserted";
				$status  = "updated"; 
			} else {
				$message = "Error inserting record";
				$status = "error";
			}
			//on delete table
		}
		
		$table_name = $this->model->get_table_name();
		$primary_key = $this->model->get_primary_key();
		$columns = $this->model->get_columns();		
		$row = $this->model->get_row($id);

		include(FILE_VIEW_EDIT);
	}

	public function settings() {
		
		// read settings file
		$settings = parse_ini_file(FILE_INI);
		if(isset($_POST)) $_POST = filter_input_array(INPUT_POST);
		if(isset($_GET)) $_GET = filter_input_array(INPUT_GET);
		$status = "";
		$message = "";
		
		// update ini file
		if (isset($_POST['apply'])) {
			
			// check table validity
			$message = $this->model->validate($_POST['table_name']);
			if ($message != "") {
				$status = "error";

			} else {
				
				// gather new setting params
				$settings['rows_per_page'] = $_POST['rows_per_page'];
				$settings['csv_file_name'] = $_POST['csv_file_name'];
				$settings['csv_encoding'] = $_POST['csv_encoding'];
				
				// switch table
				$this->model = new Model($settings['table_name']);
				
				// update ini file
				$fp = fopen(FILE_INI, 'w');
				foreach ($settings as $k => $v){
					if (false == fputs($fp, "$k = $v" . NEW_LINE)) {
						$status = "error";
					}
				}
				fclose($fp);
				
				$status  = "updated"; 
				$message = "Settings successfully changed";
			}

		// restore ini file with default settings
		} else if (isset($_POST['restore'])) {
			
			if (file_exists(FILE_INI_DEFAULT) ) {
				copy(FILE_INI_DEFAULT, FILE_INI);
				$settings = parse_ini_file(FILE_INI);
				$this->model = new Model($settings['table_name']);
				
				$status  = "updated"; 
				$message = "Default settings successfully restored";

			} else {
				$status = "error";
				$message = "Error: default config file not found";
			}
		}

		$this->rows_per_page = $settings['rows_per_page'];
		
		// csv settings
		$this->csv['file_name'] = $settings['csv_file_name'];
		$this->csv['encoding']  = $settings['csv_encoding'];
		
		include(FILE_VIEW_SETTINGS);
	}
	
}
?>