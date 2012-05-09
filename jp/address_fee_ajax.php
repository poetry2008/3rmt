<?php
/*
 * 计算配送费用
 */

require('includes/application_top.php');

$address = tep_db_prepare_input($_POST['address']);
$country = tep_db_prepare_input($_POST['country']);
$weight = tep_db_prepare_input($_POST['weight']);

$address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $address ."'");
$address_num = tep_db_num_rows($address_query);

if($address_num > 0){
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

  $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $country ."'");
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

$weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;

$free_value = $address_free_value != '' ? $address_free_value : $country_free_value;

$shipping_fee = $cart->total > $free_value ? 0 : $weight_fee;
echo $flag;
echo '<br />';
echo '<input type="hidden" name="weight_fee" value="'. $weight_fee .'">今回のお届け料金は<font color="red"><b>'. $shipping_fee .'</b></font>円です。';
echo '<br />';
echo '<input type="hidden" name="free_value" value="'. $free_value .'">※お買い物の総額は<font color="red"><b>'. $free_value .'</b></font>円以上にお買いいただければ、お届け料金は無料になります。';
echo '<br />';
tep_db_close();
?>
