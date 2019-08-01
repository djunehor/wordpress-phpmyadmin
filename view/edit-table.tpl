<link type='text/css' href='<?php echo ZACWP_PMA_FILE_CSS ?>' rel='stylesheet' />
<div class='wrap'>
    <h2>ZacWP PhpMyAdmin - Edit Table</h2>
    <h3>Table Name: <?php echo $table_name; ?></h3>

    <div class='subsubsub'>
        <a href="<?php echo $this->url['list_table'] ?>">&lt;&lt; Return to list</a>
    </div>

    <?php
    if(isset($status) && isset($message)) {
      echo "<div class='$status'><p>$message</p></div>";
    }

    ?>
<form method='post' action='<?php echo $this->url['edit_table'].'&table_name='.$table_name;?>'>
<input name="zacwp_table_edit_nonce" value="<?php echo $edit_nonce; ?>" type="hidden">

<table class='wp-list-table widefat fixed' id='zacwp-add-table'>
    <?php
		require_once("util.php");

        echo "<tr><th class='simple-table-manager'>Table Name</th><td><input class='form-control' type='text' readonly name='new_table_name' value='".$table_name."'></td></tr>";
    foreach ($columns as $col) {
    $colType = preg_replace("/[^A-Za-z]/", '', $col->Type);
        echo "<tr id='table-column-".$col->Field."'>" .
        "<th class='simple-table-manager'><input class='form-control' type='text' name='tab-col[name][]' value='".$col->Field."' ".(in_array($col->Field, $static) ? 'disabled' : '')." required></th>" .
        "<td><select class='form-control' name='tab-col[type][]' ".(in_array($col->Field, $static) ? 'disabled' : '')." required>" .
                "<option ".(strtoupper($colType) == 'INT' ? 'selected' : '')." value='INT'>Number</option>" .
                "<option ".(strtoupper($colType) == 'VARCHAR' ? 'selected' : '')." value='VARCHAR'>Short text (max 255)</option>" .
                "<option ".(strtoupper($colType) == 'TEXT' ? 'selected' : '')." value='TEXT'>Long Text</option>" .
                "<option ".(strtoupper($colType) == 'DATE' ? 'selected' : '')." value='DATE'>Date</option>" .
                "<option ".(strtoupper($colType) == 'TIMESTAMP' ? 'selected' : '')." value='TIMESTAMP'>Timestamp</option>" .
                " </select></td>";
        echo "<td><button ".( in_array($col->Field, $static) ? 'disabled' : '')." type='button' onclick=\"removeField('table-column-".$col->Field."')\">Remove</button></td>";

        echo "</tr>";
    }
    echo "<tr><th class='simple-table-manager'></th><td><button type='button' onclick='addField()' class='btn btn-success'>Add Column</button></td></tr>";

    ?>
</table>
<div class="tablenav bottom">
    <input type='submit' name='update-table' value='Update' class='button' required>
</div>
</form>
</div>

<script>
    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    function addField() {
        var id = makeid(6);
        var string = "<tr id='table-form-"+id+"'>" +
            "<th class='simple-table-manager'><input class='form-control' type='text' name='tab-col[name][]' placeholder='column name' required></th>" +
            "<td><select class='form-control' name='tab-col[type][]' required>" +
            "<option value='INT'>Number</option>" +
            "<option value='VARCHAR'>Short text (max 255)</option>" +
            "<option value='TEXT'>Long Text</option>" +
            "<option value='DATE'>Date</option>" +
            " </select></td>" +
            "<td><button type='button' onclick='removeField(\"table-form-"+id+"\")'>Remove</button></td>"+
            "</tr>";

        var element = document.getElementById("zacwp-add-table");
        element.insertAdjacentHTML('beforeend', string);
    }

    function removeField(elementId) {
        var element = document.getElementById(elementId);
        console.log(JSON.stringify(element, null, 4))
        element.parentNode.removeChild(element);
    }
</script>