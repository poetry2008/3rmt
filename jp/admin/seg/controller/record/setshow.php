<?php
require_once("./class/filter.php");
require_once("./class/record.php");
$conn = db::getConn();
$sf_id = $_GET['id'];
$sql = "select siteurl from record where id ='".$id."'";
$res = $conn->query($sql);
$row = $res->fetch_object();
$Filter = new Filter();
$Record = new Record();
$Record->edit($row->siteurl,0);
$arr = array('record_siteurl'=>$row->siteurl,'state'=>1);
$Filter->insert($arr);
if(isset($_GET['filter'])){
header("Location: /index.php?action=index&controller=record&filter=all&session_id="
    .$_GET['session_id']);
}else{
header("Location: /index.php?action=index&controller=record&session_id=".
    $_GET['session_id']);
}
