<?php
include('includes/application_top.php');
$notes = '';  
$left ='';  
$top ='';  
$zindex ='';  
$belong = str_replace('/admin/','',$_POST['belong']);
$user_info = $_POST['author'];
$tarrow = $_POST['tarrow'];
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$user_info."'))  order by id desc");
$note_arr = array();
$height_arr = array();
while($row=tep_db_fetch_array($query)){
  list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']); 
  if($tarrow == 'open'){
  $left = (int)$left+125;
  $left = (string)$left;
 $xyz= $left."|".$top."|".$zindex."|".$xlen."|".$ylen;
 $update_query = tep_db_query("update notes set xyz='".$xyz."' where id='".$row['id']."'");
  }else{
  $left = (int)$left-125;
  $left = (string)$left;
 $xyz= $left."|".$top."|".$zindex."|".$xlen."|".$ylen;
 $update_query = tep_db_query("update notes set xyz='".$xyz."' where id='".$row['id']."'");
  }

}

?>


