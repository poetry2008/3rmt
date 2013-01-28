<?php
/*
 * confirm session error
 */
require('includes/application_top.php');
if ($cart->count_contents(true) < 1) {
   echo 'no_count';
   exit;
}
$check_products_info = tep_check_less_product_option();
if (!empty($check_products_info)) {
   $notice_msg_array = array();
   $return_array = array(); 
   foreach ($check_products_info as $cpo_key => $cpo_value) {
     $tmp_cpo_info = explode('_', $cpo_value); 
     $notice_msg_array[] = tep_get_products_name($tmp_cpo_info[0]);  
   }
   $return_array[] = 'check success'; 
   $return_array[] = sprintf(NOTICE_LESS_PRODUCT_OPTION_TEXT, implode('、', $notice_msg_array));    
   echo implode('|||', $return_array);
   exit;
} 

if(!isset($_SESSION['cart']) || !isset($_SESSION['date']) || !isset($_SESSION['hour']) || !isset($_SESSION['min'])){
  echo 'error';
}else{

  //products shipping fee  
  $fixed_option_list_array = array();
  $fixed_option_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where status='0' and fixed_option!='0'");
  while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){
     $fixed_option_list_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
  }
  tep_db_free_result($fixed_option_query);


  $weight_count = 0;  
  $products_list_array = $cart->contents;
  foreach($products_list_array as $products_key=>$products_value){

    $products_id_array = explode('_',$products_key);
    $products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $products_id_array[0] ."'");
    $products_weight_array = tep_db_fetch_array($products_weight_query);
    tep_db_free_result($products_weight_query);
    $weight_count += $products_weight_array['products_weight']*$products_value['qty'];
  }

  if(isset($_POST['ad_num']) && $_POST['ad_num'] == 0 && $weight_count > 0){

    echo 'weight';
    exit;
  }

  if(isset($_POST['ad_num']) && $_POST['ad_num'] == 3){

    $country_city_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where name='".$_POST['ad_str']."' and status='0'");
    $country_city_search_array = tep_db_fetch_array($country_city_search_query);
    tep_db_free_result($country_city_search_query);
    $weight_limit = $country_city_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      
      echo $fixed_option_list_array[3].'|'.$_POST['ad_str'].'|'.$weight_limit;
    }
  }elseif(isset($_POST['ad_num']) && $_POST['ad_num'] == 2){
    $country_area_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where name='".$_POST['ad_str']."' and status='0'");
    $country_area_search_array = tep_db_fetch_array($country_area_search_query);
    tep_db_free_result($country_area_search_query);
    $weight_limit = $country_area_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      
      echo $fixed_option_list_array[2].'|'.$_POST['ad_str'].'|'.$weight_limit;
    }
  }elseif(isset($_POST['ad_num']) && $_POST['ad_num'] == 1){

    $country_fee_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where name='".$_POST['ad_str']."' and status='0'");
    $country_fee_search_array = tep_db_fetch_array($country_fee_search_query);
    tep_db_free_result($country_fee_search_query);
    $weight_limit = $country_fee_search_array['weight_limit'];

    if($weight_count > $weight_limit){

        
      echo $fixed_option_list_array[1].'|'.$_POST['ad_str'].'|'.$weight_limit;
    }
  }

}
?>
