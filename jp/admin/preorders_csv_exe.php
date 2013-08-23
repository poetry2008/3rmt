<?php
/*
  $Id$

*/
 require('includes/application_top.php');
//one time pwd 
$http_referer = $_SERVER['HTTP_REFERER'];
$http_referer_arr = explode('?',$_SERVER['HTTP_REFERER']);
$http_referer_arr = explode('admin',$http_referer_arr[0]);
$request_page_name = '/admin'.$http_referer_arr[1];
$request_one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$request_page_name."'";
$request_one_time_query = tep_db_query($request_one_time_sql);
$request_one_time_arr = array();
$request_one_time_flag = false; 
while($request_one_time_row = tep_db_fetch_array($request_one_time_query)){
  $request_one_time_arr[] = $request_one_time_row['check_value'];
  $request_one_time_flag = true; 
}
if ($ocertify->npermission != 31) {
  if (count($request_one_time_arr) == 1 && $request_one_time_arr[0] == 'admin' && $ocertify->npermission != 15){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!$request_one_time_flag && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!in_array('onetime', $request_one_time_arr) && $ocertify->npermission != 15) {
    if (!(in_array('chief', $request_one_time_arr)&&in_array('staff', $request_one_time_arr))) {
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
      if ($ocertify->npermission == 10 && in_array('staff', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
    }
  }
}
//end one time pwd

// 创建CSV文件名

  $filename = ((isset($_POST['site_id'])&&$_POST['site_id']) ?
      (tep_get_site_romaji_by_id(intval($_POST['site_id'])).'_') :'')."preorders_".date("Ymd_His", time()).".csv";

//获取下载范围
  $s_y = $_POST['s_y'] ; //起始日　年
  $s_m = $_POST['s_m'] ; //起始日　月
  $s_d = $_POST['s_d'] ; //起始日　日
  $start = $s_y.$s_m.$s_d ;
  
  $e_y = $_POST['e_y'] ; //结束日　年
  $e_m = $_POST['e_m'] ; //结束日　月
  $e_d = $_POST['e_d'] ; //结束日　日
  $end = $e_y.$e_m.$e_d ;
   
      $csv_query = tep_db_query("
          select o.*, op.*, s.romaji
          from ".TABLE_PREORDERS." o, ".TABLE_PREORDERS_PRODUCTS." op, ".TABLE_SITES." s
          where o.orders_id = op.orders_id 
            and o.site_id = s.id
            and o.date_purchased >= '" . $start . "' 
            and o.date_purchased <= '" . $end . "' 
            ".(isset($_POST['preorder_status']) && $_POST['preorder_status'] ? ("and o.orders_status = '".(int)$_POST['preorder_status'] . "'") : '')."
            ".(isset($_POST['site_id']) && $_POST['site_id'] ? ("and o.site_id = '".(int)$_POST['site_id'] . "'") : '')."
          order by o.orders_id, op.orders_products_id
      ");

  header("Content-Type: application/force-download");
  header('Pragma: public');
  header('Content-Disposition: attachment; filename='.$filename);

  $csv_header = (isset($_POST['site_id']) && $_POST['site_id']?'"'.ENTRY_SITE.'",':'').TEXT_PREORDERS_CSV;
  $c_sql = tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT' and configuration_value = 'mag_orders'"));
  if($c_sql > 0){
     tep_db_query("update ".TABLE_CONFIGURATION." set last_modified = now(),user_update = '".$_SESSION['user_name']."' where configuration_key ='DATA_MANAGEMENT' and configuration_value = 'mag_orders'");
  }else{
     tep_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key,configuration_value,last_modified,date_added,user_update,user_added) values ('DATA_MANAGEMENT','mag_orders',now(),now(),'".$_SESSION['user_name']."','".$_SESSION['user_name']."')");
  }
  print chr(0xEF).chr(0xBB).chr(0xBF);
  print $csv_header."\r\n";

  while ($csv_orders = tep_db_fetch_array($csv_query)) {

    $csv_ot_query = tep_db_query("
        select * 
        from ".TABLE_PREORDERS_TOTAL." 
        where orders_id = '".$csv_orders['orders_id']."'
    ");

    $ot_shipping       = "";
    $ot_shipping_title = "";
    $ot_awardpoints    = "";
    $ot_codt           = "";
    $ot_loworderfee    = "";
    $ot_tax            = "";
    $ot_total          = "";
    $ot_redemptions      = "";
    $ot_redemptions_flag = 0;
    $ot_redemptions_alt  = "";

    while ($csv_ot_orders = tep_db_fetch_array($csv_ot_query)) {

      if($csv_ot_orders['class'] == "ot_shipping") { $ot_shipping = $csv_ot_orders['value']; $ot_shipping_title = mb_split(" [\(（]",$csv_ot_orders['title']); }
      if($csv_ot_orders['class'] == "ot_awardpoints") { $ot_awardpoints = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_codt") { $ot_codt = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_loworderfee") { $ot_loworderfee = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_tax") { $ot_tax = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_total") { $ot_total = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_point") { $ot_redemptions = $csv_ot_orders['value']; $ot_redemptions_flag = 1; $ot_redemptions_alt = 1; }

    }

    $csv_sh_query = tep_db_query("
        select comments 
        from ".TABLE_PREORDERS_STATUS_HISTORY." 
        where orders_id = '".$csv_orders['orders_id']."' 
        order by orders_status_history_id 
        limit 0,1");
    $csv_sh_orders = tep_db_fetch_array($csv_sh_query);

    if(!isset($start_id) || !$start_id){ $start_id = $csv_orders['orders_id']; }
    $end_id = $csv_orders['orders_id'];

    $csv  = isset($_POST['site_id']) && $_POST['site_id'] ? ('"'.$csv_orders['romaji'].'",' ):'';
    $csv .=  '"'.precsv($csv_orders['orders_id']).'"';
    $csv .= ',"'.precsv($csv_orders['date_purchased']).'"';
    $csv .= ',"'.precsv($csv_orders['products_name']).'"';
    $csv .= ',"'.precsv($csv_orders['products_id']).'"';
    $csv .= ',"'.precsv($csv_orders['products_model']).'"';
    $csv .= ',"'.precsv($csv_orders['products_quantity']).'"';
    $csv .= ',"'.precsv($csv_orders['final_price']*1).'"';

    $csv_op  = "";

    $csv_op_query = tep_db_query("
        select * 
        from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." 
        where orders_products_id = ".$csv_orders['orders_products_id']." 
        order by orders_products_attributes_id");

    while ($csv_op_orders = tep_db_fetch_array($csv_op_query)) {

      $csv_op .= precsv($csv_op_orders['products_options'])."\t".precsv($csv_op_orders['products_options_values'])."\t";

      if($csv_op_orders['price_prefix'] == "-") {
        $csv_op .= $csv_op_orders['price_prefix'].precsv($csv_op_orders['options_values_price']*1)."\t";
      } else {
        $csv_op .= precsv($csv_op_orders['options_values_price']*1)."\t";
      }
    }

    $csv .= ',"'.$csv_op.'"';

    $csv .= ',"'.precsv($csv_orders['customers_id']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['customers_name_kana']) ? $csv_orders['customers_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['customers_email_address']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_country']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_state']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_city']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_company']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['billing_name_kana']) ? $csv_orders['billing_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['billing_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_country']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_state']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_city']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_company']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['delivery_name_kana']) ? $csv_orders['delivery_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['delivery_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_country']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_state']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_city']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_company']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['payment_method']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_type']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_number']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_owner']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_expires']).'"';
    $csv .= ',"'.precsv(isset($ot_shipping_title[0]) ? $ot_shipping_title[0]: '').'"';
    $csv .= ',"'.precsv(str_replace(array("\r","\n","\t"),array("","",""),$csv_sh_orders['comments'])).'"';
if(defined('JPTAX') && JPTAX == "on"){
    $csv .= ',"'.precsv($ot_total+$ot_redemptions-$ot_shipping-$ot_codt-$ot_loworderfee).'"';
} else {
    $csv .= ',"'.precsv($ot_total+$ot_redemptions-$ot_shipping-$ot_codt-$ot_loworderfee-$ot_tax).'"';
}
    $csv .= ',"'.precsv($ot_shipping*1).'"';
    $csv .= ',"'.precsv($ot_codt*1).'"';
    $csv .= ',"'.precsv($ot_loworderfee*1).'"';
    $csv .= ',"'.precsv($ot_tax*1).'"';
    $csv .= ',"'.precsv($ot_total*1).'"';
    $csv .= ',"'.precsv($ot_redemptions_flag).'"';
    $csv .= ',"'.precsv($ot_redemptions_alt).'"';
    $csv .= ',"'.precsv($ot_redemptions*1).'"';
    $csv .= ',"'.precsv($ot_total).'"';

    print $csv."\r\n";

  }
  function precsv($query) {
    global $result;
    $result = $query;
    $result = str_replace('"', '""', $result);
    return $result;    
  }

?>
