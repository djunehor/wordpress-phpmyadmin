<?php
	

/**
 * Model Class
 *
 */
class ZacWP_Model {
	
	private $db;
	private $table_name;
	private $columns;
	private $primary_key;
	private $pk_is_int;

	/**
	 * Constructor
	 *
	 * @param $table_name
	 */
	public function __construct($table_name) {
		$this->init($table_name);
	}

	/**
	 * Sets table name, column info and primary key
	 *
	 * @param $table_name
	 */
	public function init($table_name) {
		
		// set schema & table
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $table_name;

		// collect column information
		$this->db->get_row("SELECT * FROM `$this->table_name`");	//dummy
		$this->columns = array();
		foreach ($this->db->get_col_info('name') as $i => $name) {
			$this->columns[$name] = $this->db->col_info[$i]->type;
		}
		
		// find primary key & verify whether its type is integer
		$row = $this->db->get_row("SHOW FIELDS FROM `$this->table_name` WHERE `Key` = 'PRI'");
		$this->primary_key = is_object($row->Field) ? $row->Field : null;
		$this->pk_is_int = false;
		if (is_object($row->Type) && stristr($row->Type, 'int')) {
			$this->pk_is_int = true;
		}
	}
	
	/**
     * Returns primary key
     *
     */
	public function get_primary_key() {
		return $this->primary_key;
	}
	
	/**
     * Returns table name
     * 
     */
	public function get_table_name() {
		return $this->table_name;
	}
	
	/**
     * Returns array of column names & types
     * 
     */
	public function get_columns() {
		return $this->columns;
	}

	public function get_column_attributes() {
		return $this->db->get_results("DESCRIBE `$this->table_name`");
	}
	
	/**
     * Returns candidate id for new record
     * 
     */
	public function get_new_candidate_id() {
		$new_id = "";

		// autoincrement if pk is integer
		if ($this->pk_is_int) {
			$new_id = $this->db->get_var("SELECT MAX(`$this->primary_key`)+1 FROM `$this->table_name`");
		} else {
			$new_id = $this->db->get_var("SELECT MAX(`$this->primary_key`) FROM `$this->table_name`");
			$new_id .= ZACWP_PMA_NEW_ID_HINT;
		}
		if ($new_id == "")	$new_id = "1";
		return $new_id;
	}
	
	/**
     * Select all data
     * 
     */
	public function select_all() {
		return $this->db->get_results("SELECT * FROM `$this->table_name`");
	}

	/**
	 * Select certain data
	 *
	 * @param $key_word
	 * @param $order_by
	 * @param $order
	 * @param $begin_row
	 * @param $end_row
	 *
	 * @return
	 */
	public function select($key_word, $order_by, $order, $begin_row, $end_row) {
		
		$where_qry = $this->generate_where_query($key_word);
		$order_qry = $this->generate_order_query($order_by, $order);
		$sql = "SELECT * FROM `$this->table_name` $where_qry $order_qry LIMIT $begin_row, $end_row";

		return $this->db->get_results($sql);
	}

	/**
	 * Returns total row count
	 *
	 * @param string $key_word
	 *
	 * @return
	 */
	public function count_rows($key_word = "") {
		
		$where_qry = $this->generate_where_query($key_word);		
		$sql = "SELECT COUNT(*) FROM `$this->table_name` $where_qry";
		
		return $this->db->get_var($sql);
	}

	/**
	 * Generates where sql query
	 *
	 * @param $key_word
	 *
	 * @return string
	 */
     private function generate_where_query($key_word) {
		$qry = "";
		if ($key_word != "") {
			$like_statements = array();
			foreach ($this->columns as $name => $type) {
				$like_statements[] = $this->db->prepare(" `$name` LIKE '%%%s%%'", $key_word);
			}
			$qry = " WHERE " . implode(" OR ", $like_statements);
		}
		return $qry;
	 }

	/**
	 * Generates order by sql query
	 *
	 * @param $order_by
	 * @param $order
	 *
	 * @return string
	 */
     private function generate_order_query($order_by, $order) {
     	$qry = "";
		if ($order_by != "") {
			$order = esc_sql($order);
			$order_by = esc_sql($order_by);
			$qry = " ORDER BY `$order_by` $order";
		}
		return $qry;
	 }

	/**
	 * Returns single row
	 *
	 * @param $id
	 *
	 * @return
	 */
	public function get_row($id) {
		$sql = $this->db->prepare("SELECT * FROM `$this->table_name` WHERE `$this->primary_key` = '%s'", $id);
		return $this->db->get_row($sql);
	}

	/**
	 * Adds new record
	 *
	 * @param $vals
	 *
	 * @param array $exclude
	 *
	 * @return mixed|string
	 */
	public function insert($vals, $exclude = []) {

		// collect insert values and strip slashes
		$insert_vals = array();
		foreach ($this->columns as $name => $type) {
			if(in_array($name, $exclude)) continue;
			$insert_vals[$name] = stripslashes_deep($vals[$name]);
		}


		// check if pk already exists
		$sql = $this->db->prepare("SELECT `$this->primary_key` FROM `$this->table_name` WHERE `$this->primary_key` = '%s'", $insert_vals[$this->primary_key]);
		$exists = $this->db->get_var($sql);

		// insert
		if ($exists == "") {
			if ($this->db->insert($this->table_name, $insert_vals)) {
				return $insert_vals[$this->primary_key];
			}
		}
		return false;
	}

	/**
	 * Updates record
	 *
	 * @param $vals
	 *
	 * @return bool
	 */
	public function update($vals) {
		
		// collect update values and strip slashes
		$update_vals = array();
		foreach ($this->columns as $name => $type) {
			$update_vals[$name] = stripslashes_deep($vals[$name]);
		}
		
		// update
		if ($this->db->update($this->table_name, $update_vals, array($this->primary_key => $vals[$this->primary_key]))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Deletes record
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete($id) {
		$sql = $this->db->prepare("DELETE FROM `$this->table_name` WHERE `$this->primary_key` = '%s'", $id);
		if ($this->db->query($sql)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks validity of a table
	 *
	 * @param $table_name
	 *
	 * @return string
	 */
	public function validate($table_name) {
		
		// gather column information & verify errors if any
		$err_msg = "";
		$results = $this->db->get_results("SHOW KEYS FROM `$table_name` WHERE `Key_name` = 'PRIMARY'");
		if (1 < $this->db->num_rows) {
			$err_msg = "Error: table $table_name has multiple primary keys";
		
		} else if ($results[0]->Seq_in_index != 1) {
			$err_msg = "Error: table $table_name's primary key is not set at first column";
		
		}
		
		return $err_msg;
	}
	
	/**
     * Get list of available tables in schema
     *
     */
    public function get_table_options() {
    	$options = array();
    	foreach ($this->db->get_results("SHOW TABLES") as $row ){
			foreach ($row as $k => $v){
				$options[] = $v;
			}
		}
		return $options;
	}
}
?>