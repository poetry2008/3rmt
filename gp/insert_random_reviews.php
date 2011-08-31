<?php

set_time_limit(0);
ob_implicit_flush(true);
ob_end_clean();


ini_set('display_errors', 'On');

$host_url = "localhost";

$database_name = "maker_3rmt";

$user_name = "maker";

$user_pass = "123456";

define('URL_SUB_SITE', '3id.kthiz.200.com');

$con = mysql_connect($host_url, $user_name, $user_pass);

if (!$con) {
  echo 'can not connect database'; 
  exit; 
}


mysql_select_db($database_name);
mysql_query("set names utf8");

$insert_num = 300000;

function get_random_site($site_id = null) 
{
  if ($site_id) {
    $random_site_raw = mysql_query("select *, RAND() as b from sites where id != '".$site_id."' order by b limit 1");
  } else {
    $random_site_raw = mysql_query("select *, RAND() as b from sites order by b limit 1");
  }
  return mysql_fetch_array($random_site_raw); 
}

function tep_rand($min = null, $max = null) {
  static $seeded;

  if (!isset($seeded)) {
    mt_srand((double)microtime()*1000000);
    $seeded = true;
  }

  if (isset($min) && isset($max)) {
    if ($min >= $max) {
      return $min;
    } else {
      return mt_rand($min, $max);
    }
  } else {
    return mt_rand();
  }
}

function tep_random_select($query) {
  $random_info = '';
  
  $random_query = mysql_query($query);
  $num_rows = mysql_num_rows($random_query);
  
  if ($num_rows > 0) {
    $random_row = tep_rand(0, ($num_rows - 1));
    mysql_data_seek($random_query, $random_row);
    $random_info = mysql_fetch_array($random_query);
  }

  return $random_info;
}

function get_random_top_category($site_id)
{
   $random_category_sql = "select * from (select c.categories_id, c.parent_id, cd.romaji, cd.categories_status, cd.categories_name, cd.site_id from categories c, categories_description cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id = '4' order by site_id DESC) c where site_id = '0' or site_id = '".$site_id."' group by categories_id having c.categories_status != '1' and c.categories_status != '3'";
   return tep_random_select($random_category_sql); 
}

function get_child_category_by_parent_id($parent_id, $site_id)
{
  $child_category_array = array(); 
  
  $child_category_raw = mysql_query("select * from (select c.categories_id, c.parent_id, cd.romaji, cd.categories_status, cd.categories_name, cd.site_id from categories c, categories_description cd where c.parent_id = '".$parent_id."' and c.categories_id = cd.categories_id and cd.language_id = '4' order by site_id DESC) c where site_id = '0' or site_id = '".$site_id."' group by categories_id having c.categories_status != '1' and c.categories_status != '3'"); 
  
  while ($child_category = mysql_fetch_array($child_category_raw)) {
    $child_category_array[] = $child_category['categories_id']; 
    
    $child_child_category_raw = mysql_query("select * from (select c.categories_id, c.parent_id, cd.romaji, cd.categories_status, cd.categories_name, cd.site_id from categories c, categories_description cd where c.parent_id = '".$child_category['categories_id']."' and c.categories_id = cd.categories_id and cd.language_id = '4' order by site_id DESC) c where site_id = '0' or site_id = '".$site_id."' group by categories_id having c.categories_status != '1' and c.categories_status != '3'"); 
    
    while ($child_child_category = mysql_fetch_array($child_child_category_raw)) {
      $child_category_array[] = $child_child_category['categories_id']; 
    }
  }
  
  return $child_category_array;
}

function get_random_product_by_category_ids($category_array, $site_id)
{
   $random_product_sql = "select * from (select pd.products_status, pd.products_id, pd.products_name, pd.site_id from products_description pd, products_to_categories p2c where pd.products_id = p2c.products_id and p2c.categories_id in (".implode(',', $category_array).") and pd.language_id = '4' order by pd.site_id DESC) c where site_id = '0' or site_id = '".$site_id."' group by products_id having c.products_status != '0' and c.products_status != '3'";
   return tep_random_select($random_product_sql); 
}

$calc_num = 1;

while (true) {
  $random_site = get_random_site();
  $site_id = $random_site['id'];
  
  $random_top_category = get_random_top_category($site_id);  
  
  if ($random_top_category) {
    $child_category_array = get_child_category_by_parent_id($random_top_category['categories_id'], $site_id); 
    array_push($child_category_array, $random_top_category['categories_id']); 

    $random_product = get_random_product_by_category_ids($child_category_array, $site_id); 
    if ($random_product) {
       $other_random_site = get_random_site($site_id); 
       $other_site_id = $other_random_site['id']; 
       
       $other_random_top_category = get_random_top_category($other_site_id); 
       
       if ($other_random_top_category) {
         $insert_reviews_sql = 'insert into `reviews` values(NULL, '.$random_product['products_id'].', NULL, \'匿名\', 5, \''.date('Y-m-d H:i:s', time()).'\', \'0000-00-00 00:00:00\', 0, '.$site_id.', 1, NULL)';
         
         mysql_query($insert_reviews_sql); 
         
         $insert_reviews_id = mysql_insert_id(); 
         
         $reviews_description = '';
         switch ($other_site_id) {
           case '1': 
           case '2': 
           case '3':
             $reviews_description = $other_random_top_category['categories_name'].' '.$other_random_site['url'].'/rmt/c-'.$other_random_top_category['categories_id'].'.html'; 
             break;
           case '4':
             $reviews_description = $other_random_top_category['categories_name'].' '.'http://'.urlencode($other_random_top_category['romaji']).'.'.URL_SUB_SITE.'/'; 
             break;
           default:
             $reviews_description = $other_random_top_category['categories_name'].' '.$other_random_site['url'].'/'.urlencode($other_random_top_category['romaji']).'/'; 
             break;
         }
         
         $insert_reviews_des_sql = 'insert into `reviews_description` values('.$insert_reviews_id.', 4, \''.$reviews_description.'\')';
         mysql_query($insert_reviews_des_sql); 
         
         $calc_num++;  
      }
    }
  }
  if ($calc_num > $insert_num) {
    break; 
  }
  echo $calc_num.'<br>';
  ob_flush();
  flush();
}
echo 'finish';
