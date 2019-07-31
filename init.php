<?php
/*
Plugin Name: ZacWP PhpMyAdmin
Description: Enables CRUD on tables and records and exporting them to CSV files through minimal database interface from your wp-admin page menu.
Version: 1.0
Author: Zacchaeus Bolaji
Author URI: https://github.com/makinde2013
*/

define('FILE_INI', dirname(__FILE__) . '/config/settings.ini');
define('FILE_INI_DEFAULT', dirname(__FILE__) . '/config/settings.ini.default');
define('FILE_CSS',  plugin_dir_url(__FILE__) . "/style/style-admin.css");
define('FILE_VIEW_LIST',  dirname(__FILE__) . "/view/list.tpl");
define('TABLE_VIEW_LIST',  dirname(__FILE__) . "/view/table.tpl");
define('FILE_VIEW_SETTINGS',  dirname(__FILE__) . "/view/settings.tpl");
define('FILE_VIEW_EDIT',  dirname(__FILE__) . "/view/edit.tpl");
define('FILE_VIEW_ADD',  dirname(__FILE__) . "/view/add.tpl");
define('FILE_TABLE_ADD',  dirname(__FILE__) . "/view/add-table.tpl");
define('FILE_TABLE_EDIT',  dirname(__FILE__) . "/view/edit-table.tpl");
define('DELMITER', ',');
define('NEW_LINE', "\r\n");
define('NEW_ID_HINT', " - Edit new ID");

require_once("controller.php");
require_once("model.php");

$control = new Controller(isset($_GET) && isset($_GET['table_name']) ? filter_var($_GET['table_name']) : null);

?>