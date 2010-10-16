<?php
require_once("./class/helper_pager.php");
$page_size=20;
$page = isset($_GET['page'])&&$_GET['page']!=''?$_GET['page']:1;
$table = "record";
$groupby = "siteurl";
$conditions = '`show`="1"';
$sortby = "created_at desc";
$pager = new helper_pager($table,$page,$page_size,$conditions,$groupby,$sortby);
$pageurl = "index.php?action=all&controller=backlink";
$records = $pager->findAll();
/*
$conn = db::getConn();
$sql = "select * from record where `show`='1' 
        group by siteurl 
        order by created_at desc";
$result = $conn->query($sql);
$records = array();
while($record = $result->fetch_object()){
  $records[] = $record;
}
*/
$template->display(array('records' => $records,
                         'pageurl' => $pageurl,
                         'pager' => $pager));
