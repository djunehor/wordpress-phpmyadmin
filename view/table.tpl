<link type='text/css' href='<?php echo FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
    <h2>ZacWP PhpMyAdmin - Table List</h2>
    <h3>Database Name: <?php echo $db_name ?></h3>

    <?php
	if ($key_word != "") {
		echo "<div class='updated'><p>Found " . number_format($total) . " results for: $key_word &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='" . $this->url['list'] . "'>Exit Search</a></p></div>";
}
?>
<div class='subsubsub'>
    <a class='page-title-action' href='<?php echo $this->url['add_table'] ?>'>Add New</a>&nbsp;&nbsp;
</div>

<?php if (empty($result)) { ?>
<table class='wp-list-table widefat'><tr><th>No results found.</th></tr></table>

<?php } else { ?>
<table class='wp-list-table widefat fixed'>
    <thead>
    <th>S/N</th>
    <th>Table Name</th>
    <th>Actions</th>
    <th></th>
    </thead>
    <tbody>
    <?php
		$row_bgcolor = array('simple-table-manager-list-all-odd', 'simple-table-manager-list-all-even');	// decorate rows
		$row_bgcolor_index = 0;
		$sn = 1;
		foreach ($result as $row ){
		//get table name without prefix
		$tName = $row->{"Tables_in_$db_name"};
		$ar = explode("_", $tName);
        unset($ar[0]);
        $tab = implode("_", $ar);

			echo "<tr>";
    echo "<td class='" . $row_bgcolor[$row_bgcolor_index] . "'>" . $sn++ . "</td>";
    echo "<td class='" . $row_bgcolor[$row_bgcolor_index] . "' nowrap><a href='" . $this->url['list'] . '&#038table_name=' . $tName . "'>$tName</a></td>";
    if(!in_array($tab, $wpTables)) echo "<td class='" . $row_bgcolor[1] . "' nowrap><a href='" . $this->url['edit_table'] . '&#038table_name=' . $tName . "'>Edit</a></td>";
    echo "</tr>";
    $row_bgcolor_index = ($row_bgcolor_index + 1) % count($row_bgcolor);
    }
    ?>
    </tbody>
</table>

<div class='tablenav bottom'>
    <div class='tablenav-pages'>
        <span class='displaying-num'>Total <?php echo number_format($total) ?></span>
        <span class='pagination-links'>

		</span>
    </div><br class='clear' />
</div>
<?php } ?>
</div>