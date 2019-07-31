<link type='text/css' href='<?php echo FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Add New</h2>
<h3>Table Name: <?php echo $table_name ?></h3>

	<div class='subsubsub'>
	<a href="<?php echo $this->url['list'] ?>&table_name=<?php echo $table_name; ?>">&lt;&lt; Return to list</a>
	</div>
		
		<form method='post' action='<?php echo $this->url['edit'] ?>'>
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