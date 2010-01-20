<?php
// iimy,gm,wm to 3rmt

define('RMT_DB_HOST', 'localhost');
define('RMT_DB_USER', 'root');
define('RMT_DB_PASS', '123456');
define('RMT_DB_NAME', 'test_rmt');
define('R3MT_DB_NAME', 'test_3rmt');

define('R3MT_JP_ID', '1');
define('R3MT_GM_ID', '2');
define('R3MT_WM_ID', '3');

echo "connect database\n";

$_link  = mysql_connect(RMT_DB_HOST, RMT_DB_USER, RMT_DB_PASS) or die('3');
mysql_query('set names utf8');
// init finish

cptable('products');

// functions
function i($str) {
  return iconv('euc-jp', 'utf-8', $str);
}

function p($table){
  echo $table . "\t\t\tDone\n";
}

function rq($sql){
  mysql_select_db(RMT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $e . "\n";
  }
  return $q;
}

function r3q($sql){
  mysql_select_db(R3MT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $sql . "\n";
    echo $e . "\n";
  }
  return $q;
}

function get_sites(){
  return array('jp', 'gm', 'wm');
}

function cptable($rtable, $r3table = null){
  $fields = array();
  $q = rq('describe '.$rtable);
  while ($f = mysql_fetch_array($q)) {
    $fields[] = $f['Field'];
  }
}

function cp3table($table){
}
