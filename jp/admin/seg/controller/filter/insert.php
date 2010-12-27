<?php 
require_once("./class/filter.php");
require_once("./class/record.php");
if(isset($_POST['submit'])&&$_POST['submit']=="insert"){
  $post = $_POST;
  $Filter = new Filter();
  $Record = new Record();
  $Filter->insert($post);
  if($post['state']==1){
    $Record->edit($post['record_siteurl'],0);
  }else{
    $Record->edit($post['record_siteurl'],1);
  }
  header("Location: index.php?action=index&controller=filter");
  exit;
}
$template->display();
