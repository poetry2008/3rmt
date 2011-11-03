<?php
require('includes/application_top.php');
if(is_numeric($_GET['id']) && is_numeric($_GET['x']) && is_numeric($_GET['y']) &&
    is_numeric($_GET['z'])){
$id = intval($_GET['id']);
$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);
$query = tep_db_query("select * from notes where id = '".$id."'");
$row = tep_db_fetch_array($query);
list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']);
tep_db_query("UPDATE notes SET xyz='".$x."|".$y."|".$z."|".$xlen."|".$ylen."' WHERE id=".$id);

echo "1";
}else if(isset($_GET['del_note'])&&$_GET['del_note']&&is_numeric($_GET['id']))
{
tep_db_query("delete from notes  WHERE id=".$_GET['id']);
tep_db_query("OPTIMIZE TABLE  `notes`");
}else if(isset($_POST['action'])&&$_POST['action']=='change_move'){
$query = tep_db_query("select * from notes where id = '".$id."'");
$row = tep_db_fetch_array($query);
list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']);
$xlen=$_POST['xlen'];
$ylen=$_POST['ylen'];
tep_db_query("UPDATE notes SET xyz='".$left."|".$top."|".$zindex."|".$xlen."|".$ylen."' WHERE id=".$id);
}else if(isset($_POST['action'])&&$_POST['action']=='save_text'){
  $id = $_POST['id'];
  $text = $_POST['text'];
  $query  = tep_db_query("update notes set content='".$text."' where id =
      '".$id."'");
  if($query){
    echo 1;
  }
}
?>
