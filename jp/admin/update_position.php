<?php
require('includes/application_top.php');
if(is_numeric($_GET['id']) && is_numeric($_GET['x']) && is_numeric($_GET['y']) &&
    is_numeric($_GET['z'])){
$id = intval($_GET['id']);
$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);

tep_db_query("UPDATE notes SET xyz='".$x."|".$y."|".$z."' WHERE id=".$id);

echo "1";
}else if(isset($_GET['del_note'])&&$_GET['del_note']&&is_numeric($_GET['id']))
{
tep_db_query("delete from notes  WHERE id=".$_GET['id']);
tep_db_query("OPTIMIZE TABLE  `notes`");
}
?>
