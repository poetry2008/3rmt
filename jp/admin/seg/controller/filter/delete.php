<?php
require_once("./class/filter.php");
require_once("./class/record.php");
$conn = db::getConn();
$sf_id = $_GET['sf_id'];
$sql = "select record_siteurl url from site_filter where id ='".$sf_id."'";
$res = $conn->query($sql);
$row = $res->fetch_object();
$Record = new Record();
$Filter = new Filter();
$Record->edit($row->url);
$Filter->delete($sf_id);
header("Location: ".$_SERVER['SCRIPT_NAME']."?action=index&controller=filter");
