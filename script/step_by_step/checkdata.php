<?php

define('RMT_DB_HOST', 'localhost');
define('RMT_DB_USER', 'root');
define('RMT_DB_PASS', '123456');
define('RMT_DB_NAME', 'maker_rmt');
define('R3MT_DB_NAME', 'test_3rmt2');

$sites =  array('jp', 'gm', 'wm');
$faq = array('168', '169', '170', '171', '177', '178', '179', '190', '195');

define('R3MT_JP_ID', '1');
define('R3MT_GM_ID', '2');
define('R3MT_WM_ID', '3');

$_link  = mysql_connect(RMT_DB_HOST, RMT_DB_USER, RMT_DB_PASS) or die('3');

mysql_query('set names utf8');
//address_format
cmptable('wm_address_format', 'address_format');
//banners
cmp3table('banners');
//banners_history
//#cache
//calendar
cmp3table('calendar');
//categories
cmptable('categories');
//categories_description
//categories_rss
cmptable('categories_rss');
//color
//color_to_products
//configuration
cmp3table('configuration');
//configuration_group
cmptable('wm_configuration_group', 'configuration_group');
//contents
cmp3table('contents');
//countries
cmptable('iimy_countries', 'countries');
//currencies
cmptable('wm_currencies', 'currencies');
//faq_categories
//faq_questions
//geo_zones
cmptable('wm_geo_zones', 'geo_zones');
//image_document_types
cmptable('wm_image_document_types', 'image_document_types');
//image_documents
//information_page
cmp3table('information_page');
//languages
cmptable('wm_languages', 'languages');
//latest_news
cmp3table('latest_news');
//login
cmptable('login');
//mail_magazine
cmp3table('mail_magazine');
//manufacturers
cmptable('manufacturers');
//manufacturers_info
cmptable('manufacturers_info');
//newsletters
cmp3table('newsletters');
//orders_mail
//orders_status
cmptable('wm_orders_status', 'orders_status');
//permissions
cmptable('permissions');
//present_goods
//products
cmptable('products');
//products_attributes
cmptable('products_attributes');
//products_attributes_download
cmptable('products_attributes_download');
//products_description
//products_notifications
//products_options
cmptable('products_options');
//products_options_values
cmptable('products_options_values');
//products_options_values_to_products_options
cmptable('products_options_values_to_products_options');
//products_to_categories
cmptable('products_to_categories');
//products_to_image_documents
//products_to_tags
cmptable('products_to_tags');
//reviews
//reviews_description
//#sessions
//#sites
//specials
cmptable('specials');
//sql_log
//tags
cmptable('tags');
//tax_class
cmptable('wm_tax_class', 'tax_class');
//tax_rates
cmptable('wm_tax_rates', 'tax_rates');
//users
cmptable('users');
//whos_online
//zones
cmptable('wm_zones', 'zones');
//zones_to_geo_zones
cmptable('wm_zones_to_geo_zones', 'zones_to_geo_zones');


//customers
//customers_info
//address_book
//customers_basket
//customers_basket_attributes
//orders
//orders_products
//orders_products_attributes
//orders_products_download
//orders_status_history
//orders_total
//present_applicant



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

function table_prefix($s){
  if(strtoupper($s) == 'JP'){
    return 'iimy_';
  } else 
    return $s.'_';
}
function site_id($s){
  return constant(strtoupper('r3mt_'.$s.'_id'));
}

function slog($log) {
}
