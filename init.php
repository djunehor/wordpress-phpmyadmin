<?php
/*
Plugin Name: ZacWP PhpMyAdmin
Description: Enables CRUD on tables and records and exporting them to CSV files through minimal database interface from your wp-admin page menu.
Version: 1.0
Author: Zacchaeus Bolaji
Author URI: https://github.com/makinde2013
*/

define('ZACWP_PMA_FILE_INI', dirname(__FILE__) . '/config/settings.ini');
define('ZACWP_PMA_FILE_INI_DEFAULT', dirname(__FILE__) . '/config/settings.ini.default');
define('ZACWP_PMA_FILE_CSS',  plugin_dir_url(__FILE__) . "/style/style-admin.css");
define('ZACWP_PMA_FILE_VIEW_LIST',  dirname(__FILE__) . "/view/list.tpl");
define('ZACWP_PMA_TABLE_VIEW_LIST',  dirname(__FILE__) . "/view/table.tpl");
define('ZACWP_PMA_FILE_VIEW_SETTINGS',  dirname(__FILE__) . "/view/settings.tpl");
define('ZACWP_PMA_FILE_VIEW_EDIT',  dirname(__FILE__) . "/view/edit.tpl");
define('ZACWP_PMA_FILE_VIEW_ADD',  dirname(__FILE__) . "/view/add.tpl");
define('ZACWP_PMA_FILE_TABLE_ADD',  dirname(__FILE__) . "/view/add-table.tpl");
define('ZACWP_PMA_FILE_TABLE_EDIT',  dirname(__FILE__) . "/view/edit-table.tpl");
define('ZACWP_PMA_DELIMITER', ',');
define('ZACWP_PMA_NEW_LINE', "\r\n");
define('ZACWP_PMA_NEW_ID_HINT', " - Edit new ID");

require_once("zacwp_pma_controller.php");
require_once("zacwp_pma_model.php");

function zacwp_run_at_activation() {
	if(file_exists(ZACWP_PMA_FILE_INI_DEFAULT) && !file_exists(ZACWP_PMA_FILE_INI)) {
		$default = file_get_contents(ZACWP_PMA_FILE_INI_DEFAULT);
		try{file_put_contents(ZACWP_PMA_FILE_INI, $default);}
		catch (\Exception $exception) {}
	}
}
function zacwp_run_at_deactivation() {
	if(file_exists(ZACWP_PMA_FILE_INI)) unlink( ZACWP_PMA_FILE_INI );
}
register_activation_hook( __FILE__, 'zacwp_run_at_activation' );
register_deactivation_hook( __FILE__, 'zacwp_run_at_deactivation' );

$tableName = isset($_GET) && isset($_GET['table_name']) ? sanitize_text_field($_GET['table_name']) : null;
$control = new ZacWP_PMA_Controller(isset($_GET) && isset($_GET['table_name']) ? filter_var($_GET['table_name']) : null);

?>