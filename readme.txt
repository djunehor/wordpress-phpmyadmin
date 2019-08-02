=== ZacWP PhpMyAdmin ===
Contributors: djunehor
Donate link: https://djunehor.com/djunehor
Tags: mysql, crud, table, database, simple, export, query
Requires at least: 4.6
Tested up to: 5.2
Stable tag: 1.0
Requires PHP: 5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

ZacWP PhpMyAdmin enables database management, custom queryand exporting them to CSV files through minimal database interface from your wp-admin page menu.

== Description ==
So, you're managing a website for a client and and need to make a quick edit on the DB, but don't have access to the server. This plugin allows you interact with the DB from wp-admin.

ZacWP PhpMyAdmin enables editing table records and exporting them to CSV files through minimal database interface from your wp-admin page menu.

* Simply CRUD table contents on your wp-admin screen
* Search and sort table records
* No knowledge on MySQL or PHP required
* Export table records to a CSV file
* User can edit table structure
* User can create new table
* User can run custom SQL query

== NOTE ==
* Admin is not allowed to modify wordpress default tables' structure.
* Admin is not allowed to change user password.
* Admin is not allowed to edit site and blog url so you don't get locked out admin panel.

== Installation ==

1. Upload the entire `zacwp-phpmyadmin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click `PhpMyAdmin Manager` in the admin sidebar

== Screenshots ==

1. `PhpMyAdmin Manager` menu in admin sidebar
2. View of all the tables on wordpress database
3. Add new table to database
4. Settings page to set export file name and rows per page
5. View all rows in a table
6. Edit a table row

== Frequently Asked Questions ==

= Can I customize this plugin =

This plugin is open-source, as such, you can customise as you want as long as you know what you're doing.

= Why is my password requested when I try to run custom query =

We try to validate the password against the currently logged in user to be double sure you know what you're doing. We don't save your password.

= I want some other features added to this plugin =

You can request for custom features by sending a mail to corporate@djunehor.com

== Changelog ==
= 1.0 =
* Stable release

== Upgrade Notice ==
