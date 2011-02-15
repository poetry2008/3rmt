<?php
$hosturl = 'localhost'; //mysql url
$database_name = 'maker_3rmt';//database name
$username = 'root'; //mysql username
$password = '123456';//mysql user's password

$fix_ca_num = 1; //set insert category num
$fix_pro_num = 1; //set insert product num
$fix_user_num = 1; //set insert user num
$fix_order_num = 1; //set insert order num

echo "this script has four steps in total"."\n";


//connect mysql
$con = mysql_connect($hosturl, $username, $password) or die('can not connect database');
mysql_select_db($database_name);
mysql_query("set names utf8");

//get sites info
$site_arr = array();
$site_url = array();
$site_query = mysql_query("select * from sites");
while ($site_res = mysql_fetch_array($site_query)) {
  $site_arr[] = $site_res['id'];
  $site_url[] = $site_res['url'];
}

//add category
$ca_name_random_str = 'testdata_'.getRandomStr(5);

for ($cnum = 0; $cnum<$fix_ca_num; $cnum++) {
  $ca_romaji_str = getRandomCategoryRomaji();
  
  //insert categories 
  $ca_insert_sql = "insert into categories values (NULL, '0', NULL, '0', '0', '".date('Y-m-d H:i:s', time())."', NULL)";
  mysql_query($ca_insert_sql);
  
  $cid = mysql_insert_id();
  
  //insert categories_description 
  $ca_des_insert_sql = "insert into categories_description values ('".$cid."', '0', '4', '".$ca_name_random_str."', '', NULL, NULL, '', '', '', '', '', '', '', '".$ca_romaji_str."', '0')";
  mysql_query($ca_des_insert_sql);
}

echo "add category success"."\n";

//add product
$product_arr = array();
for ($pnum = 0; $pnum<$fix_pro_num; $pnum++) {
  $pa_romaji_str = getRandomProductRomaji();
  //$manufacturer_id =  getRandomManufacturer();
  
  //insert products 
  $pro_insert_sql = "insert into products values (NULL, '100', '".getRandomStr(3)."', '0', '0', '0', '1000', NULL, NULL, NULL, NULL, '1200.0000', '', '".date('Y-m-d H:i:s', time())."', NULL, '".date('Y-m-d H:i:s', time())."', '0.00', '1', '0', '0', '0', '0', '0', '', '', '".getRandomStr(3).'//'.getRandomStr(3)."', 'testdata_".getRandomStr(10).'//'.getRandomStr(3)."', 'testdata_".getRandomStr(10).'//'.getRandomStr(3)."', 'testdata_".getRandomStr(10).'//'.getRandomStr(3)."', 'testdata_".getRandomStr(10)."', '0', '100', NULL, NULL)"; 
  mysql_query($pro_insert_sql);
  $pid = mysql_insert_id();
  $product_arr[] = $pid;
  
  //insert products_description
  $pro_des_insert_sql = "insert into products_description values ('".$pid."', '4', 'testdata_".getRandomStr(6)."', 'testdata_".getRandomStr(10)."', '0', '', '0', '".$pa_romaji_str."', '1')";
  mysql_query($pro_des_insert_sql);

  //make product to link category 
  $sel_category_query = mysql_query("select *, RAND() as c from categories order by c limit 1");
  $sel_category_res = mysql_fetch_array($sel_category_query);
  mysql_query("insert into products_to_categories values('".$pid."', '".$sel_category_res['categories_id']."')");
}

echo "add products success"."\n";

//add customer
$cus_password = '3d59d30c41b9330f4b6a6593b683e8a5:c2'; //123456
foreach ($site_arr as $cskey => $csvalue) {
  for ($csnum = 0; $csnum < $fix_user_num; $csnum++) {
    $email_str = getRandomCustomerEmail($csvalue);
    
    //insert customers 
    $cus_insert_sql = "insert into customers values (NULL, '', 'test".getRandomStr(5)."', '".getRandomStr(5)."', '', '', '0000-00-00 00:00:00', '".$email_str."', '1', '', NULL, '".$cus_password."', '1', '0', '".$csvalue."', '0', NULL)";
    mysql_query($cus_insert_sql);
    $cus_id = mysql_insert_id();
    
    //insert customers_info 
    $cus_info_insert_sql = "insert into customers_info (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values('".$cus_id."', '0', now())";
    mysql_query($cus_info_insert_sql);
  }
}


echo "add customer success"."\n";

//get order status info
$order_status_arr = array();
$order_status_list_query = mysql_query('select * from orders_status where language_id = 4 order by orders_status_id asc');
while ($order_status_list = mysql_fetch_array($order_status_list_query)) {
  $order_status_arr[] = $order_status_list['orders_status_id'];
}

if (empty($product_arr)) {
   echo 'you must insert products';
   exit;
}

//add orders
$tmp_num = 0;
foreach ($site_arr as $oskey => $osvalue) {
  for ($onum = 0; $onum < $fix_order_num; $onum++) {
    foreach ($order_status_arr as $okey => $ovalue) {
      if ($ovalue != '1') {
        $order_last_modified = '\''.date('Y-m-d H:i:s', time()).'\''; 
      } else {
        $order_last_modified = 'NULL'; 
      }
      //insert orders
      $now_time = time();
      $after_time = strtotime('+'.$tmp_num.' seconds', $now_time);
        
      $orders_id = date('Ymd', $after_time).'-'.date('His', $after_time).ds_makeRandStr(2);
      //$orders_id = date('Ymd').'-'.date('His').ds_makeRandStr(2);
      
      $customers_info = getRandomCustomers($osvalue);
      $customers_id = $customers_info['customers_id'];
      $customers_name = $customers_info['customers_lastname'].' '.$customers_info['customers_firstname'];
      
      $customers_email = $customers_info['customers_email_address'];
      $customers_address_format_id = '6';
      $bill_name = $customers_name;
      $billing_address_format_id = '6';
      $date_purchased = 'now()';
      $currency_value = '1.000000';
      $horihiki_date = date('Y-m-d H:i', strtotime('+30 minutes')).':00';
      $orders_ref = $site_url[$oskey];
      $orders_ref_site = substr($site_url[$oskey], 7);

      $order_insert_sql = "insert into orders values('".$orders_id."', '".$osvalue."',
        '".$customers_id."', '".$customers_name."', '', '', '', '', '', '', '',
        'Japan', '', '".$customers_email."', '".$customers_address_format_id."', '',
        '', '', '', '', '', '', '', '', '', '0', '".$bill_name."', '', '', '', '', '',
        '', '', 'Japan', '', '".$billing_address_format_id."', '銀行振込', '', '', '',
        '', ".$order_last_modified.", ".$date_purchased.", '".$ovalue."', NULL, 'JPY', '".$currency_value."', '', '指定した時間どおりに取引して欲しい', '".$torihiki_date."', '0', '4', '--',
        '', '0', '".$orders_ref."', '".$orders_ref_site."', '192.168.88.103',
        'localhost', 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.6) Gecko/20100627 Firefox/3.6.6', NULL, '0', '0', '1', '0', '', '', '0', '' , '0', '0', '0',
        '0', '0', '0', 'en-us,en;q=0.5', '', '', NULL,
        NULL,NULL,NULL,NULL,NULL,NULL,'', NULL, NULL, '1', '', NULL)";
      mysql_query($order_insert_sql);
       
      //insert orders_products
      $product_info = getRandomProduct($product_arr);
      $products_id = $product_info['products_id'];
      $products_model = $product_info['products_model'];  
      $products_name = $product_info['products_name'];  
      $products_price = $product_info['products_price']; 
      $final_price = $products_price;
      $products_tax = '0.0000';
      $products_rate = '0';
      $products_character = '';
      
      $order_product_sql = "insert into orders_products values(NULL, '".$orders_id."', '".$products_id."', '".$products_model."', '".$products_name."', '".$products_price."', '".$final_price."', '".$products_tax."', '1', '".$products_rate."', '".$products_character."', '".$osvalue."', '".$horihiki_date ."')";
      mysql_query($order_product_sql);
    

      //insert orders_total
      $order_total_sql_1 = "insert into orders_total values(NULL, '".$orders_id."', 'ポイント割引:', '0円', '0.0000', 'ot_point', '4')";
      mysql_query($order_total_sql_1);
      
      $order_total_sql_2 = "insert into orders_total values(NULL, '".$orders_id."', '合計:', '<b>".(int)$products_price."円</b>', '".$products_price."', 'ot_total', '6')";
      mysql_query($order_total_sql_2);
     
      $order_total_sql_3 = "insert into orders_total values(NULL, '".$orders_id."', '小計:', '".(int)$products_price."円', '".$products_price."', 'ot_subtotal', '1')";
      mysql_query($order_total_sql_3);
      
      //insert orders_status_history 
      $orders_status_id = '1';
      $create_date = 'now()';
      $customer_notified = '1';
      $oh_status = '0';
      
      $order_status_history_sql = "insert into orders_status_history values (NULL, '".$orders_id."', '".$orders_status_id."', ".$create_date.", '".$customer_notified."', '', '".$oh_status."')";
      mysql_query($order_status_history_sql);
      
      $tmp_num++; 
    }
    $tmp_num++; 
  }
  $tmp_num++; 
}

echo "add order success"."\n";
echo "finish";

function getRandomStr($length = 10)
{
  $return_str = ''; 
  $str_arr = array('a', 'b',  'c',  'd',  'e',  'f',  'g',  'h',  'i',  'j',  'k', 'l',  'm',  'n',  'o',  'p',  'q',  'r',  's',  't',  'u',  'v',  'w',  'x', 'y',  'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

  for ($i=0; $i<$length; $i++) {
    $return_str .= $str_arr[rand(0, 35)]; 
  }
  return $return_str;
}

function getRandomCategoryRomaji()
{
  $romaji_str = '';
  
  while (true) {
    $random_tmp_str = getRandomStr(6);
    $romaji_exist_query = mysql_query("select cd.romaji from categories c, categories_description cd  where c.categories_id = cd.categories_id and cd.site_id = '0' and c.parent_id = '0' and cd.romaji = '".$random_tmp_str."' and cd.language_id = '4'");  
    if (!mysql_num_rows($romaji_exist_query)) {
      $romaji_str = $random_tmp_str; 
      break; 
    }
  }
  return $romaji_str;
}

function getRandomProductRomaji() 
{
  $romaji_str = '';
  while (true) {
    $random_tmp_str = getRandomStr(6);
    $romaji_exist_query = mysql_query("select * from products_description where language_id = '4' and site_id = '0' and romaji = '".$random_tmp_str."'");
    if (!mysql_num_rows($romaji_exist_query)) {
      $romaji_str = $random_tmp_str; 
      break; 
    }
  }
  return $romaji_str;
}

function getRandomCustomerEmail($site_id)
{
  $email_str = '';
  while (true) {
    $email_random_str = 'testdata_'.getRandomStr(6).'@gmail.com'; 
    $email_exist_query = mysql_query("select * from customers where site_id = '".$site_id."' and customers_email_address = '".$email_random_str."'"); 
    if (!mysql_num_rows($email_exist_query)) {
      $email_str = $email_random_str;
      break; 
    }
  }
  return $email_str;
}

function getRandomCustomers($site_id)
{
  $cus_random_query = mysql_query("select *, RAND() as c from customers where customers_guest_chk = '0' and site_id = '".$site_id."' order by c limit 1");   
  return mysql_fetch_array($cus_random_query); 
}

function getRandomProduct($product_arr)
{
   $pro_query = mysql_query("select p.products_id, p.products_model, pd.products_name, p.products_price, p.products_tax_class_id, RAND() AS c from products p, products_description pd where p.products_id = pd.products_id and pd.site_id = '0' and p.products_id in (".implode(',', $product_arr).") order by c limit 1");
   
   return mysql_fetch_array($pro_query);
}

function ds_makeRandStr($len = 2) 
{
  $strElem = "0123456789";
  
  $strElemArray = preg_split("//", $strElem, 0, PREG_SPLIT_NO_EMPTY);

  $retStr = "";

  srand((double)microtime() * 100000);
  
  for ($i=0; $i<$len; $i++) {
    $retStr .= $strElemArray[array_rand($strElemArray, 1)]; 
  }
  
  return $retStr;
}

function getRandomManufacturer()
{
  $manufacturer_query = mysql_query('select * , RAND() as c from manufacturers order by c limit 1');
  
  $manufacturer = mysql_fetch_array($manufacturer_query);
  if ($manufacturer) {
    return $manufacturer['manufacturers_id']; 
  }
  return 0;
}
?>
