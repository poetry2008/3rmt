<?php
require_once("./class/helper_pager.php");
$page_size=20;
$page = isset($_GET['page'])&&$_GET['page']!=''?$_GET['page']:1;
$pageurl = "index.php?action=index&controller=filter";
$table = "site_filter";
$pager = new helper_pager($table,$page,$page_size);
$filters = $pager->findAll();
/*
$conn = db::getConn();

$result = $conn->query("select * from site_filter");

$filters = array();
while($filter = $result->fetch_object()){
  $filters[] = $filter;
}
*/
$template->display(array('filters' => $filters,
                         'pager' => $pager,
                         'pageurl' => $pageurl));
