<?php
set_time_limit(0);
include("includes/configure.php");
$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");
echo 'start...<br>';
$user_id_str = 'root'; //userid
$user_pwd_str = 'root'; //password
$user_name_str = 'Root'; //username

$user_pwd = (string) crypt($user_pwd_str);
mysql_query("insert `users` values('".$user_id_str."', '".$user_pwd."', '".$user_name_str."', '', '', '', '".date('Y-m-d H:i:s', time())."', '', '".date('Y-m-d H:i:s', time())."', '1')");
$site_list_query = mysql_query("select * from sites order by id asc");
$site_list_array = array();
$site_list_array[] = 0;
while ($site_list = mysql_fetch_array($site_list_query)) {
  $site_list_array[] = $site_list['id'];
}
sort($site_list_array);
$site_list_str = implode(',', $site_list_array);

mysql_query("insert `permissions` values('".$user_id_str."', '31', '".$site_list_str."')");

mysql_query("insert `user_ip` values('".$user_id_str."', '*.*.*.*')");
echo 'finish';
?>
