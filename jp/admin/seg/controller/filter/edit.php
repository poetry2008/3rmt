<?php 
require_once('./class/filter.php');
require_once('./class/record.php');
if(isset($_POST['submit'])&&$_POST['submit']=="update"){
  $post = $_POST;
  $Filter = new Filter();
  $Filter->edit($post);
  $Record = new Record();
  if($post['state']==1){
    $Record->edit($post['record_siteurl'],0);
  }else{
    $Record->edit($post['record_siteurl'],1);
  }
  header("Location: ".$_SERVER['SCRIPT_NAME']."?action=index&controller=filter");
  exit;
}
$conn = db::getConn();
$sf_id = $_GET['sf_id'];
$sql = "select * from site_filter where id= '".$sf_id."'";
$res = $conn->query($sql);
$row = $res->fetch_object();



$template->display(array('row' => $row));
