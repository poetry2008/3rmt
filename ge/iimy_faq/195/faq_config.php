<?
// GAME NUMBER
$site_name = 'gm';
$game_number = '195';

// SET MYSQL INFORMATION
$mysql_host = "localhost";
$mysql_database = "iimycatalog";
$mysql_username = "iimyadm";
$mysql_password = "Tomo0120Y";

// CONNECT TO MYSQL DATABASE
$mysql_connect = mysql_connect("$mysql_host", "$mysql_username", "$mysql_password");
mysql_select_db("$mysql_database");

// SELECT ADMIN INFO
$admin_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$site_name."_faq_admin"));
?>