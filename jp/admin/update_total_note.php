<?php
set_time_limit(0);
include("includes/configure.php");
$connect=mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die('can not connect database');

mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo "starting...<br>";

$note_list_query = mysql_query("select * from notes where belong like '%modules.php?set=order_total%'");
while ($note_list = mysql_fetch_array($note_list_query)) {
  mysql_query("update `notes` set `belong` = 'module_total.php' where `id` = '".$note_list['id']."'"); 
}
echo "finish";
