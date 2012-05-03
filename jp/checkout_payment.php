<?php
/*
  $Id$
*/
require_once "includes/application_top.php";
/*
 * 计算配送费用
 */
$weight = $cart->weight;

foreach($_SESSION['options'] as $op_value){
  $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value[1] ."' and status='0'");
  $city_num = tep_db_num_rows($city_query);

  $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value[1] ."' and status='0'");
  $address_num = tep_db_num_rows($address_query);
  
  $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value[1] ."' and status='0'");
  $address_country_num = tep_db_num_rows($country_query);

if($city_num > 0){
  $city_array = tep_db_fetch_array($city_query);
  tep_db_free_result($city_query);
  $city_free_value = $city_array['free_value'];
  $city_weight_fee_array = unserialize($city_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($city_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $city_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $city_weight_fee = $weight <= $key ? $value : 0;
    }

    if($city_weight_fee > 0){

      break;
    }
  }
}elseif($address_num > 0){
  $address_array = tep_db_fetch_array($address_query);
  tep_db_free_result($address_query);
  $address_free_value = $address_array['free_value'];
  $address_weight_fee_array = unserialize($address_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($address_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $address_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $address_weight_fee = $weight <= $key ? $value : 0;
    }

    if($address_weight_fee > 0){

      break;
    }
  }
}else{
  if($address_country_num > 0){
  $country_array = tep_db_fetch_array($country_query);
  tep_db_free_result($country_query);
  $country_free_value = $country_array['free_value'];
  $country_weight_fee_array = unserialize($country_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($country_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $country_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $country_weight_fee = $weight <= $key ? $value : 0;
    }

    if($country_weight_fee > 0){

      break;
    }
  }
  }
}

}
if($city_weight_fee != ''){

  $weight_fee = $city_weight_fee;
}else{
  $weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;
}

if($city_free_value != ''){

  $free_value = $city_free_value;
}else{
  $free_value = $address_free_value != '' ? $address_free_value : $country_free_value;
}

$shipping_fee = $cart->total > $free_value ? 0 : $weight_fee;
require_once DIR_WS_ACTIONS."checkout_payment.php";
$order->info['total'] = $order->info['total'] + $shipping_fee;
require_once "checkout_payment_template.php";
