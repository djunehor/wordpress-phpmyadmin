<link type='text/css' href='<?php echo ZACWP_PMA_FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Settings</h2>

	<?php
    if(isset($status) && isset($message)) {
      echo "<div class='$status'><p>$message</p></div>";
}
?>
	<div class='subsubsub'>
	<a class='page-title-action' href='<?php echo $this->url['list_table'] ?>'>&lt;&lt; Return to list</a>
	</div>
		
		<form method='post' name='settings' action='<?php echo $this->url['settings'] ?>'>
<input name="zacwp_table_setting_apply_nonce" value="<?php echo $apply_nonce; ?>" type="hidden">
<input name="zacwp_table_setting_restore_nonce" value="<?php echo $restore_nonce; ?>" type="hidden">
		<table class='wp-list-table widefat fixed'>

		<tr><th class='simple-table-manager'>Max rows on page</th><td><input type='number' name='zacwp_rows_per_page' value='<?php echo $settings['rows_per_page']; ?>'/></td></tr>
		<tr><th class='simple-table-manager'>CSV file name</th><td><input type='text' name='zacwp_csv_file_name' value='<?php echo str_replace(".csv", "", $settings['csv_file_name']); ?>'/></td></tr>
		<tr><th class='simple-table-manager'>CSV encoding</th><td><input type='text' name='zacwp_csv_encoding' value='<?php echo $settings['csv_encoding']; ?>'/></td></tr>

		</table>
		<div class="tablenav bottom">
		<input type='submit' name='apply' value='Apply Changes' class='button button-primary' />&nbsp;
		<input type='submit' name='restore' value='Restore Defaults' class='button' />
		</div>
		</form>
		</div>

</div>