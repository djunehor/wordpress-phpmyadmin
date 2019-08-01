<link type='text/css' href='<?php echo ZACWP_PMA_FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Add Row</h2>
<h3>Table Name: <?php echo $table_name ?></h3>

	<div class='subsubsub'>
	<a class='page-title-action' href="<?php echo $this->url['list'] ?>&table_name=<?php echo $table_name; ?>">&lt;&lt; Return to list</a>
	</div>
	<?php
    if(isset($status) && isset($message)) {
      echo "<div class='$status'><p>$message</p></div>";
	}
	?>
		
		<form method='post' action='<?php echo $this->url['edit'] ?>&table_name=<?php echo $table_name; ?>'>
	<input name="zacwp_table_add_row_nonce" value="<?php echo $add_nonce; ?>" type="hidden">
		<table class='wp-list-table widefat fixed'>
<?php
		require_once("util.php");
		foreach ($columns as $name => $type) {
			if ($name == $primary_key) {
				echo "<tr><th class='simple-table-manager'>$name *</th><td>" . data_type2html_input($columns[$name], $name, $new_id) . "</td></tr>";
			} else {
				echo "<tr><th class='simple-table-manager'>$name</th><td>" . data_type2html_input($columns[$name], $name, '') . "</td></tr>";
			}
		}
?>
		</table>
		<div class="tablenav bottom">
		<input type='submit' name='add' value='Add' class='button'>
		</div>
		</form>
</div>