<link type='text/css' href='<?php echo ZACWP_PMA_FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
    <h2>ZacWP PhpMyAdmin - Custom Query</h2>
    <h4 style="color: red">Actions performed here cannot be undone!</h4>
    <?php
    if(isset($status) && isset($message)) {
      echo "<div class='$status'><p>$message</p></div>";
}
?>
<div class='subsubsub'>
    <a class='page-title-action' href='<?php echo $this->url['list_table'] ?>'>&lt;&lt; Return to list</a>
</div>

<form method='post' name='custom_query' action='<?php echo $this->url['custom_query'] ?>'>
<input name="zacwp_table_custom_query_nonce" value="<?php echo $custom_nonce; ?>" type="hidden">
<table class='wp-list-table widefat fixed'>

    <tr><th class='simple-table-manager'>Query</th><td><textarea name='zacwp_custom_query'></textarea></td></tr>
    <tr><th class='simple-table-manager'>WP Password</th><td><input type="password" name='zacwp_my_password'></td></tr>

</table>
<div class="tablenav bottom">
    <input type='submit' name='run-query' value='Run Query' class='button button-primary' />&nbsp;
</div>
</form>
</div>

</div>