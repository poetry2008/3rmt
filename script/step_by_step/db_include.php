<?php
// iimy,gm,wm to 3rmt
/*

执行顺序
other
jp
gm
wm

   */
// @todo configuration default value

define('RMT_DB_HOST', 'localhost');
define('RMT_DB_USER', 'root');
define('RMT_DB_PASS', '123456');
define('RMT_DB_NAME', 'maker_rmt');
define('R3MT_DB_NAME', 'test_3rmt2');

define('JP_FS', '/home/maker/project/JP/');
define('GM_FS', '/home/maker/project/GM/');
define('WM_FS', '/home/maker/project/WM/');
define('3RMT_FS', '/home/maker/project/3rmt/');











$sites =  array('jp', 'gm', 'wm');
$faq   = array('168', '169', '170', '171', '177', '178', '179', '190', '195');
$delete_configuration = array(
    'AFFILIATE_PAYMENT_ORDER_MIN_STATUS',
    'AFFILIATE_VALUE',
    'AFFILIATE_BILLING_TIME',
    'AFFILIATE_COOKIE_LIFETIME',
    'AFFILIATE_EMAIL_ADDREDD',
    'AFFILIATE_PERCENT',
    'AFFILIATE_THRESHOLD',
    'AFFILIATE_TIER_LEVELS',
    'AFFILIATE_TIER_PERCENTAGE',
    'AFFILIATE_USE_BANK',
    'AFFILIATE_USE_CHECK',
    'AFFILIATE_USE_PAYPAL',
    'MAX_DISPLAY_AFFILIATE_NEWS'
    );

define('R3MT_JP_ID', '1');
define('R3MT_GM_ID', '2');
define('R3MT_WM_ID', '3');

//echo "connect database\n";
$_link  = mysql_connect(RMT_DB_HOST, RMT_DB_USER, RMT_DB_PASS) or die('3');

mysql_query('set names utf8');

function rq($sql){
  //echo RMT_DB_NAME;
  mysql_select_db(RMT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $sql . "#\n";
    echo $e . "#\n";
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

/**
 * 
 */
function cptable($rtable, $r3table = null){
  if (!$r3table) $r3table = $rtable;
  $r3fields = $rfields = array();
  r3q("truncate $r3table");
  $q = r3q('describe '.$r3table);
  while ($f = mysql_fetch_array($q)) {
    $r3fields[] = $f['Field'];
  }
  $q = rq('describe '.$rtable);
  while ($f = mysql_fetch_array($q)) {
    $rfields[] = $f['Field'];
  }
  if(array_diff($rfields, $r3fields))
    echo "$rtable -> $r3table diff fields : " . join(',', array_diff($rfields, $r3fields)) . "\n";
  $q = rq("select * from " . $rtable);
  while($r = mysql_fetch_array($q)){
    $values = array();
    $sql = "insert into $r3table (`" . join('`,`', $r3fields) . "`) values ('";
    foreach($r3fields as $f){
      //if ($r[f]) 
        $values[] = mysql_real_escape_string($r[$f]);
      //else 
        //$values[] = 'Null';
    }
    $sql .= join("','", $values);
    $sql .= "')";
    r3q($sql);
  }
  print("$r3table \n");
}

function cp3table($table){
  global $sites;
  r3q("truncate $table");
  $fields = array();
  $q = r3q('describe '.$table);
  while ($f = mysql_fetch_array($q)) {
    $fields[] = $f['Field'];
  }
  foreach($sites as $s){
    $q = rq("select * from " . table_prefix($s) . $table);
    while($r = mysql_fetch_array($q)){
      $values = array();
      $sql = "insert into $table (`" . join('`,`', $fields) ."`) values ('";
      foreach($fields as $k => $f){
        if($k === 0){
          $values[] = null;
        }elseif($f == 'site_id'){
          $values[] = constant(strtoupper('r3mt_'.$s.'_id'));
        }else{
          //if ($r[$f]) 
          $values[] = mysql_real_escape_string($r[$f]);
          //else 
          //$values[] = 'Null';
        }
      }
      $sql .= join("','", $values);
      $sql .= "')";
      r3q($sql);
    }
  }
  print("$table \n");
}

function table_prefix($s){
  if(strtoupper($s) == 'JP'){
    return 'iimy_';
  } else 
    return $s.'_';
}
 
function site_id($s){
  return constant(strtoupper('r3mt_'.$s.'_id'));
}

function cp($s,$t) {
  return copy($s, $t);
}

function fs($s){
  return constant(strtoupper($s.'_fs'));
}

function cmptable($rtable, $r3table = null) {
  if (!$r3table) $r3table = $rtable;
  $q = r3q('describe '.$r3table);
  while ($f = mysql_fetch_array($q)) {
    $r3fields[] = $f['Field'];
  }
  $q = rq('describe '.$rtable);
  while ($f = mysql_fetch_array($q)) {
    $rfields[] = $f['Field'];
  }
  if(array_diff($rfields, $r3fields))
    echo "$rtable -> $r3table diff fields : " . join(',', array_diff($rfields, $r3fields)) . "\n";
  $q = rq("select * from " . $rtable);
  while($r = mysql_fetch_array($q)){
    $values = array();
    $sql = "select * from $r3table where ";
    foreach($r3fields as $f){
      $values[] = $f . " = '" . mysql_real_escape_string($r[$f]) . "'";
    }
    $sql .= join(" and ", $values);
    if(mysql_num_rows(r3q($sql)) != 1){
      echo "not found:\t" . $sql . "\n";
    }
  }
  print("$r3table \n");
}

function cmp3table($table){
  global $sites;
  foreach($sites as $s){
    $fields = array();
    $q = rq('describe '.table_prefix($s).$table);
    while ($f = mysql_fetch_array($q)) {
      $fields[] = $f['Field'];
    }
    //print_r($fields);
    $q = rq("select * from " . table_prefix($s) . $table);
    while($r = mysql_fetch_array($q)){
      $values = array();
      $sql = "select * from $table where ";
      foreach($fields as $k => $f){
        if($k === 0){
          //$values[] = null;
        } else if (
            $f == 'last_modified'
            or $f == 'date_added' 
            or $f == 'lastupdate' 
            or $f == 'expires_date'
            or $f == 'date_scheduled'
            or $f == 'date_status_change'
          ){
        }else{
          $values[] = " " . $f . " = '" . mysql_real_escape_string($r[$f]) . "' ";
        }
      }
      $values[] = " site_id = '".site_id($s)."' ";
      $sql .= join(" and ", $values);
      if(mysql_num_rows(r3q($sql)) != 1) {
        echo $s . ':' . $sql . "\n";
        echo $s . ':' . $table . ':' . $r[$fields[0]] . "\n";
      }
    }
  }
  print("$table \n");
}

function m($str) {
  return mysql_real_escape_string($str);
}