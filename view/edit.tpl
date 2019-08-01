<link type='text/css' href='<?php echo ZACWP_PMA_FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
<h2>ZacWP PhpMyAdmin - Edit Row</h2>
<h3>Table Name: <?php echo $table_name ?></h3>

	<?php
    if(isset($status) && isset($message)) {
      echo "<div class='$status'><p>$message</p></div>";
	}
	?>

	<div class='subsubsub'>
	<a class='page-title-action' href="<?php echo $this->url['list'] ?>&table_name=<?php echo $table_name; ?>">&lt;&lt; Return to list</a>
	</div>
		
<?php if (!empty($row)) {
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
 $url = $base_url . $_SERVER["REQUEST_URI"];
?>
		<form method='post' action='<?php echo $url; ?>'>
<input name="zacwp_table_update_row_nonce" value="<?php echo $edit_nonce; ?>" type="hidden">
<table class='wp-list-table widefat fixed'>
<?php
		require_once("util.php");

		//get actual table name
		$ar = explode("_", $table_name);
        unset($ar[0]);
        $tab = implode("_", $ar);

		foreach ($row as $name => $value) {
			if ($name == $primary_key || ($tab == 'users' && $name == 'user_pass')) {
				echo "<tr><th class='simple-table-manager'>$name *</th><td><input type='text' disabled name='$name' value='$value'/></td></tr>";
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
			<input name="zacwp_table_delete_row_nonce" value="<?php echo $delete_nonce; ?>" type="hidden">
			<input type='submit' name='delete' value='Delete' class='button' onclick="return confirm('Are you sure you want to delete this record?')">
			<?php } ?>
		</div>
		<input type="hidden" name="id" value="<?php echo $id ?>">
		</form>
<?php } ?>
</div>