<link type='text/css' href='<?php echo FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Settings</h2>
	
<?php 
	if ($status == "error") {
		echo "<div class='error'><p>$message</p></div>";
	} else if ($status == "success") {
		echo "<div class='updated'><p>$message</p></div>";
	}
?>
	<div class='subsubsub'>
	<a href='<?php echo $this->url['list_table'] ?>'>&lt;&lt; Return to list</a>
	</div>
		
		<form method='post' name='settings' action='<?php echo $this->url['settings'] ?>'>
		<table class='wp-list-table widefat fixed'>

		<tr><th class='simple-table-manager'>Max rows on page</th><td><input type='number' name='rows_per_page' value='<?php echo $this->rows_per_page ?>'/></td></tr>
		<tr><th class='simple-table-manager'>CSV file name</th><td><input type='text' name='csv_file_name' value='<?php echo $this->csv['file_name'] ?>'/></td></tr>
		<tr><th class='simple-table-manager'>CSV encoding</th><td><input type='text' name='csv_encoding' value='<?php echo $this->csv['encoding'] ?>'/></td></tr>

		</table>
		<div class="tablenav bottom">
		<input type='submit' name='apply' value='Apply Changes' class='button button-primary' />&nbsp;
		<input type='submit' name='restore' value='Restore Defaults' class='button' />
		</div>
		</form>
		</div>

</div>