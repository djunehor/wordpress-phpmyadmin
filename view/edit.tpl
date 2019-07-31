<link type='text/css' href='<?php echo FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Edit</h2>
<h3>Table Name: <?php echo $table_name ?></h3>

<?php 
	if ($status == "error") {
		echo "<div class='error'><p>$message</p></div>";
	} else if ($status == "success") {
		echo "<div class='updated'><p>$message</p></div>";
	}
?>

	<div class='subsubsub'>
	<a href="<?php echo $this->url['list'] ?>&table_name=<?php echo $table_name; ?>">&lt;&lt; Return to list</a>
	</div>
		
<?php if (!empty($row)) { ?>
		<form method='post' action='<?php echo $this->url['edit'] ?>'>
		<table class='wp-list-table widefat fixed'>
<?php
		require_once("util.php");

		//get actual table name
		$ar = explode("_", $table_name);
        unset($ar[0]);
        $tab = implode("_", $ar);

		foreach ($row as $name => $value) {
			if ($name == $primary_key || ($tab == 'users' && $name == 'user_pass')) {
				echo "<tr><th class='simple-table-manager'>$name *</th><td><input type='text' readonly='readonly' name='$name' value='$value'/></td></tr>";
			} else {
				echo "<tr><th class='simple-table-manager'>$name</th><td>" . data_type2html_input($columns[$name], $name, $value) . "</td></tr>";
			}
		}
?>
		</table>
		<div class="tablenav bottom">
		<input type='submit' name='update' value='Update' class='button'>&nbsp;
		<?php if($tab == 'users' && $id == 1) {
			//don't show delete
			} else { ?>
			<input type='submit' name='delete' value='Delete' class='button' onclick="return confirm('Are you sure you want to delete this record?')">
			<?php } ?>
		</div>
		<input type="hidden" name="id" value="<?php echo $id ?>">
		</form>
<?php } ?>
</div>