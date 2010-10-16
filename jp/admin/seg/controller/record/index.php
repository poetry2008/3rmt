<?php
require_once("./class/helper_pager.php");
$page_size=20;
$page = isset($_GET['page'])&&$_GET['page']!=''?$_GET['page']:1;
if(isset($_GET['session_id'])&&$_GET['session_id']!=''){
  $table = "record rec";
  $conditions =
    isset($_GET['filter'])&&$_GET['filter']!=''?
    "rec.session_id='".$_GET['session_id']."'":
    "rec.show = '1' and "."rec.session_id='".
    $_GET['session_id']."'";
  $groupby = "rec.siteurl";
  $sortby = "created_at desc";
  $pager = new helper_pager($table,$page,$page_size,$conditions,$groupby,$sortby);
  $records = $pager->findAll();
$status =
isset($_GET['filter'])?$_GET['session_id']."&filter=all":$_GET['session_id'];
$template->display(array('records' => $records,'status' => $status,
      'pager' => $pager,
      'pageurl'=>'index.php?action=index&controller=record&session_id='.
      $status));
}else{
  $table = "record rec";
  $conditions = "1"; 
  $groupby = "rec.siteurl";
  $sortby = "created_at desc";
  $pager = new helper_pager($table,$page,$page_size,$conditions,$groupby,$sortby);
  $records = $pager->findAll();
   $template->display(array('records' => $records,'pager' => $pager,
         'pageurl'=>'index.php?action=index&controller=record'));
}
/*
$conn = db::getConn();
if(isset($_GET['session_id'])&&$_GET['session_id']!=''){
$sql = "select * from record rec where ";
$sql .= isset($_GET['filter'])&&$_GET['filter']?"":"rec.show = '1' and ";
$sql .= "rec.session_id='".$_GET['session_id']."'group by rec.siteurl order by id desc";
$result = $conn->query($sql);
$records = array();
while($record = $result->fetch_object()){
  $records[] = $record;
}
$status =
isset($_GET['filter'])?$_GET['session_id']."&filter=all":$_GET['session_id'];
$template->display(array('records' => $records,'status' => $status));
}else{
  $sql = "select * from record group by siteurl";
  $result = $conn->query($sql);
  $records = array();
  while($record = $result->fetch_object()){
    $records[] = $record;
  }
   $template->display(array('records' => $records));
}
*/
