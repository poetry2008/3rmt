<?php
require_once("./class/helper_pager.php");
require_once('./class/mission.php');
$pagesize = 20;
$page = isset($_GET['page'])&&$_GET['page']!=''?$_GET['page']:1;
$pageurl = "index.php?";
$table = "mission";
$conditions = "1";
$sortby ="id desc";
$pager = new helper_pager($table,$page,$pagesize,$conditions,null,$sortby);
$missions= $pager->findAll('mission');
/*
$conn = db::getConn();

$result = $conn->query("select m.id,m.name,m.keyword from mission m order by m.id desc");
$missions = array();
while($m = $result->fetch_object('mission')){
  $missions[] = $m;
  echo $m->name ;
  echo "---";
  echo $m->keyword;
  echo "---";
  echo $m->enabled;
  echo "---";
  echo $m->getStatus();
  echo "<a target=\"_blank","\" href='index.php?action=start&controller=mission&id=",$m->id,"'>start</a>";

  echo "</br>";
}
*/


$template->display(array('missions' => $missions,'pager'=>$pager,'pageurl'=>$pageurl));





?>
