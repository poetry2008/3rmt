<?php
/*
   $Id$
 */
require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION);
require(DIR_WS_ACTIONS.'checkout_confirmation.php');
$page_url_array = explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['shipping_page_str'] = end($page_url_array);
?>
<?php
if(isset($_SESSION['shipping_session_flag']) && $_SESSION['shipping_session_flag'] == true){
?>
<script type="text/javascript">
$(document).ready(function(){
  alert("<?php echo TEXT_SESSION_ERROR_ALERT;?>");
});  
</script>
<?php
unset($_SESSION['shipping_session_flag']);
}
?>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents"> 
      <?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post', 'onSubmit="return check_confirm_payment(\''.$payment.'\')"');?>
      <input type="hidden" name="carturl" id="carturl" value="<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>">
      <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>      
      <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                    </tr> 
                  </table></td> 
                <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                      <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                    </tr> 
                  </table></td> 
                <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                    </tr> 
                  </table></td> 
              </tr> 
              <tr> 
          <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_OPTION . '</a>'; ?></td> 
                <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
                <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PAYMENT . '</a>'; ?></td> 
                <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr>
            <td align="right">
              <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
                  <tr>
<?php
$fixed_option_list_array = array();
$fixed_option_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where status='0' and fixed_option!='0'");
 while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){
 
  $fixed_option_list_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
}
tep_db_free_result($fixed_option_query);

$ad_post = '';
$ad_num = 0;
$ad_array = $_SESSION['options'];
if(array_key_exists($fixed_option_list_array[3],$ad_array)){

    $ad_post = $ad_array[$fixed_option_list_array[3]][1];
    $ad_num = 3;
}elseif(array_key_exists($fixed_option_list_array[2],$ad_array)){

    $ad_post = $ad_array[$fixed_option_list_array[2]][1];
    $ad_num = 2; 
}elseif(array_key_exists($fixed_option_list_array[1],$ad_array)){
    $ad_post = $ad_array[$fixed_option_list_array[1]][1];
    $ad_num = 1;
}  
?>
                      <td class="main"><b><?php echo TEXT_CONFIRMATION_READ;?></b></td>
                        <td class="main" align="right"><a href="javascript:void(0);" onClick="confirm_session_error(<?php echo $ad_num;?>,'<?php echo $ad_post;?>');"><?php echo tep_image_button('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER);?></a></td>
                    </tr>
                </table>
            </td>
        </tr>
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents"> 
              <tr> 
                <?php
  if ($sendto != false) {
?> 
    <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
    <td class="main"><?php echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr> 
    <tr> 
    <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td> 
    </tr> 
    <?php
    if ($order->info['shipping_method']) {
      ?> 
      <tr> 
        <td class="main"><?php echo '<b>' . HEADING_SHIPPING_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                    </tr> 
                    <tr> 
                      <td class="main"><?php echo $order->info['shipping_method']; ?></td> 
                    </tr> 
                    <?php
    }
?> 
    </table></td> 
    <?php
  }
?> 
<td width="<?php echo (($sendto != false) ? '70%' : '100%'); ?>" valign="top">
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                   
                          <?php
  if (sizeof($order->info['tax_groups']) > 1) {
    ?> 
    <tr> 
    <td class="main" colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td> 
    </tr> 
    <?php
  } else {
    ?> 
    <tr> 
      <td class="main"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                          </tr>
                <tr><td><table width="100%"> 
                          <?php
  }
  /**************/
for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
  $product_info = tep_get_product_by_id((int)$order->products[$i]['id'], SITE_ID, $languages_id);
    
    echo '          <tr>' . "\n" .
         '            <td id="confirmation_product_num_info" align="right" valign="top">' .  $order->products[$i]['qty'] . '&nbsp;'.NUM_UNIT_TEXT .  (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($order->products[$i]['qty'], (int)$order->products[$i]['id']) ? '<br><span style="font-size:10px">'.  tep_get_full_count_in_order2($order->products[$i]['qty'], (int)$order->products[$i]['id']) .'</span>': '') . '</td>' . "\n" .
         '            <td class="main" valign="top">' . $order->products[$i]['name'];
    
  if ($order->products[$i]['price'] < 0) {
    echo ' (<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax'])).'</font>'.JPMONEY_UNIT_TEXT.')';
  } else {
    echo ' ('.$currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax']).')';
  }

  if (STOCK_CHECK == 'true') {
    echo tep_check_stock((int)$order->products[$i]['id'], $order->products[$i]['qty']);
  }
  $all_show_option_id = array();
  $all_show_option = array();
  $option_item_order_sql = "select it.id from ".TABLE_PRODUCTS."
  p,".TABLE_OPTION_ITEM." it 
  where p.products_id = '".(int)$order->products[$i]['id']."' 
  and p.belong_to_option = it.group_id 
  and it.status = 1
  order by it.sort_num,it.title";
  $option_item_order_query = tep_db_query($option_item_order_sql);
  while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
    $all_show_option_id[] = $show_option_row_item['id'];
  }
  if ( (isset($order->products[$i]['op_attributes'])) && (sizeof($order->products[$i]['op_attributes']) > 0) ) {
    for ($j=0, $n2=sizeof($order->products[$i]['op_attributes']); $j<$n2; $j++) {  
      $all_show_option[$order->products[$i]['op_attributes'][$j]['item_id']] 
      = $order->products[$i]['op_attributes'][$j];
      /*
      $op_price = tep_get_show_attributes_price($order->products[$i]['op_attributes'][$j]['item_id'], $order->products[$i]['op_attributes'][$j]['group_id'], $order->products[$i]['op_attributes'][$j]['value']); 
       
      echo '<br><small>&nbsp;<i> - ' .  $order->products[$i]['op_attributes'][$j]['front_title'] . ': ' .  str_replace(array("<br>", "<BR>"), '', $order->products[$i]['op_attributes'][$j]['value']);
      if ($op_price != '0') {
        echo ' ('.$currencies->format($op_price).')'; 
      }
      echo '</i></small>';
      */
    }
  }
  
  if ( (isset($order->products[$i]['ck_attributes'])) && (sizeof($order->products[$i]['ck_attributes']) > 0) ) {
   for ($jk=0, $n3=sizeof($order->products[$i]['ck_attributes']); $jk<$n3; $jk++) {
      $all_show_option[$order->products[$i]['ck_attributes'][$jk]['item_id']] 
      = $order->products[$i]['ck_attributes'][$jk];
      /*
      $cop_price = tep_get_show_attributes_price($order->products[$i]['ck_attributes'][$jk]['item_id'], $order->products[$i]['ck_attributes'][$jk]['group_id'], $order->products[$i]['ck_attributes'][$jk]['value']); 
      echo '<br><small>&nbsp;<i> - ' .  $order->products[$i]['ck_attributes'][$jk]['front_title'] . ': ' .  str_replace(array("<br>", "<BR>"), '', $order->products[$i]['ck_attributes'][$jk]['value']);
      if ($cop_price != '0') {
        echo ' ('.$currencies->format($cop_price).')'; 
      }
      echo '</i></small>';
      */
    }
  }
  // new option list 
  foreach($all_show_option_id as $t_item_id){
      $op_price = tep_get_show_attributes_price( $all_show_option[$t_item_id]['item_id'],
        $all_show_option[$t_item_id]['group_id'], $all_show_option[$t_item_id]['value']); 
      if(trim($all_show_option[$t_item_id]['value']) != ''){
        echo '<br><small>&nbsp;<i> - ' . $all_show_option[$t_item_id]['front_title'] .
             ': ' .  str_replace(array("<br>", "<BR>"), '', $all_show_option[$t_item_id]['value']);
        if ($op_price != '0') {
          if ($op_price < 0) {
            echo ' (<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format($op_price)).'</font>'.JPMONEY_UNIT_TEXT.')'; 
          } else {
            echo ' ('.$currencies->format($op_price).')'; 
          }
        }
        echo '</i></small>';
      }

  }
  echo '</td>' . "\n";

  if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right" width="55">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

  echo '            <td class="main" align="right" valign="top" width="55">';
  if ($order->products[$i]['final_price'] < 0) {
    echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty'])).'</font>'.JPMONEY_UNIT_TEXT;
  } else {
    echo $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']);
    }
    echo '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<?php
if(!empty($_SESSION['options'])){
?>
<tr> 
<td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents"> 
  <tr> 
  <td>
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
  <td class="main" colspan="3"><b><?php echo TEXT_OPTIONS_TITLE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
  </tr>
<?php
  foreach($_SESSION['options'] as $key=>$value){
?>
  <tr>
  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main" width="150" valign="top"><?php echo $value[0]; ?>:</td>
  <td class="main"><?php echo $value[1]; ?><span id="<?php echo $key;?>"></span></td>
  </tr>
<?php
  }
?>
<?php
/*
 * 计算配送费用
 */


//$address = tep_db_prepare_input($_POST['address']);
//$country = tep_db_prepare_input($_POST['country']);
  $country_fee_array = array();
  $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

    $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
  }
  tep_db_free_result($country_fee_id_query);
$weight = $cart->weight;

foreach($_SESSION['options'] as $op_key=>$op_value){
  if($op_key == $country_fee_array[3]){
    $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value[1] ."' and status='0'");
    $city_num = tep_db_num_rows($city_query);
  }
  
  if($op_key == $country_fee_array[2]){
    $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value[1] ."' and status='0'");
    $address_num = tep_db_num_rows($address_query);
  }
  
  if($op_key == $country_fee_array[1]){ 
    $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value[1] ."' and status='0'");
    $address_country_num = tep_db_num_rows($country_query);
  }

if($city_num > 0 && $op_key == $country_fee_array[3]){
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
}elseif($address_num > 0 && $op_key == $country_fee_array[2]){
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
}elseif($address_country_num && $op_key == $country_fee_array[1]){
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

$_SESSION['weight_fee'] = $weight_fee;
$_SESSION['free_value'] = $free_value;
$shipping_fee = $cart->total-$_SESSION['h_point'] > $free_value ? 0 : $weight_fee;
?>

  </table>
</td>
</tr>
</table></td>
</tr> 
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<?php
}
?>
<tr> 
<td><table width="100%" border="0" cellspacing="0" cellpadding="2"  class="infoBoxContents">
  <tr>
  <td>
  <table cellpadding="0" cellspacing="0" border="0">
  <tr>
  <td class="main" colspan="3"><b><?php echo TEXT_TRADE_DATE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
  </tr>
  <tr>
  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main" width="150"><?php echo TEXT_EXPECT_TRADE_DATE; ?></td>
  <td class="main">
  <?php 
  echo str_string($date); 
  $date_info_array = explode('-', $date); 
  $tmp_date = date('D', mktime(0, 0, 0, $date_info_array[1], $date_info_array[2], $date_info_array[0]));  
  switch(strtolower($tmp_date)) {
     case 'mon':
       echo '（'.TEXT_DATE_MONDAY.'）'; 
       break;
     case 'tue':
       echo '（'.TEXT_DATE_TUESDAY.'）'; 
       break;
     case 'wed':
       echo '（'.TEXT_DATE_WEDNESDAY.'）'; 
       break;
     case 'thu':
       echo '（'.TEXT_DATE_THURSDAY.'）'; 
       break;
     case 'fri':
       echo '（'.TEXT_DATE_FRIDAY.'）'; 
       break;
     case 'sat':
       echo '（'.TEXT_DATE_STATURDAY.'）'; 
       break;
     case 'sun':
       echo '（'.TEXT_DATE_SUNDAY.'）'; 
       break;
     default:
       break;
  }
  ?>
  </td>
  </tr>
  <tr>
  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_EXPECT_TRADE_TIME; ?></td>
  <td class="main">
  <?php echo $start_hour; ?>
<?php echo TIME_HOUR_TEXT;?>
<?php echo $start_min; ?>
<?php echo TIME_MIN_TEXT;?>
<?php echo TEXT_TIME_LINK;?>
<?php echo $end_hour; ?>
<?php echo TIME_HOUR_TEXT;?>
<?php echo $end_min; ?>
<?php echo TIME_MIN_TEXT;?>
</td>
</tr>
</table>

</td>
</tr>
</table></td>
</tr> 
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<?php

$pay_info_array = $payment_modules->specialOutput($payment);

if (!empty($pay_info_array)) {
?>
<tr>
<td>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents"> 
  <tr> 
  <td>
  <table width="100%" cellspacing="0" cellpadding="2" border="0">
  <tr>
  <td class="main" colspan="3">
  <b><?php echo $pay_info_array[0];?></b>

<?php
echo '<a href="' .  tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>';
?>
</td></tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main" width="150">
<?php echo $pay_info_array[1][0];?>
</td>
<td class="main">
<?php echo $pay_info_array[1][1];?>
</td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main">
<?php echo $pay_info_array[2][0];?>
</td>
<td class="main">
<?php echo $pay_info_array[2][1];?>
</td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main">
<?php echo $pay_info_array[3][0];?>
</td>
<td class="main">
<?php echo $pay_info_array[3][1];?>
</td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main">
<?php echo $pay_info_array[4][0];?>
</td>
<td class="main">
<?php echo $pay_info_array[4][1];?>
</td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main">
<?php echo $pay_info_array[5][0];?>
</td>
<td class="main">
<?php echo $pay_info_array[5][1];?>
</td></tr></table>
</td></tr></table>
</td>
</tr>
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<?php
}
?>
<tr> 
<td  style="color: #000; font-size: 12px; padding: 10px; background: url(images/design/box/dot.gif) bottom repeat-x;"> <b><?php echo HEADING_BILLING_INFORMATION; ?></b> </td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<tr> 
<td><table class="infoBoxContents" width="100%" border="0" cellspacing="0" cellpadding="2"> 
<tr>
<td width="30%" valign="top"><table border="0" cellspacing="0" cellpadding="2"> 
<tr> 
<td class="main"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
</tr> 
<tr> 
<td class="table_spacing"><?php echo payment::changeRomaji($order->info['payment_method']); ?></td> 

</tr> 
</table></td> 
<td width="70%" valign="top" align="right"><table border="0" cellspacing="0" cellpadding="2"> 
<?php
if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  if(@$_SESSION['h_point'] < $order->info['subtotal']) {
    $point = isset($_SESSION['h_point'])?$_SESSION['h_point']:0;
    } else {
      if ($payment_modules->is_get_point(payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE))) {
        $point = 0; 
      } else {
        $point = $order->info['subtotal'];
      }
    }
    $real_point = $point;
    tep_session_register('real_point');
    tep_session_register('point');
  }
  
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    $order_total_modules->process();
    echo $order_total_modules->output();
}
if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  // 计算各个不同顾客的返点率从这开始============================================================
  if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
    //规定期间内，计算订单合计金额------------
    $ptoday = date("Y-m-d H:i:s", time());
    $pstday_array = getdate();
    $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));

    $total_buyed_date = 0;
    
    $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."' and site_id = ".SITE_ID);
    if(tep_db_num_rows($customer_level_total_query)) {
      while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
        $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
        $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);

        $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
        $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);

        $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
      }
    }
    //----------------------------------------------
    //计算返点率----------------------------------
    if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
      $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
      for($j=0; $j<sizeof($back_rate_array); $j++) {
        $back_rate_array2 = explode(",", $back_rate_array[$j]);
        if($back_rate_array2[2] <= $total_buyed_date) {
          $back_rate = $back_rate_array2[1];
          $back_rate_name = $back_rate_array2[0];
      }
    }
    } else {
      $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      if($back_rate_array[2] <= $total_buyed_date) {
        $back_rate = $back_rate_array[1];
        $back_rate_name = $back_rate_array[0];
      }
    }
    //----------------------------------------------
    $point_rate = $back_rate;
  } else {
    $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
  }
  
  $point_rate = $payment_modules->get_point_rate($payment);
  if ($order->info['subtotal'] > 0) {
    if (isset($_SESSION['campaign_fee'])) {
      $get_point = ($order->info['subtotal'] + $_SESSION['campaign_fee']) * $point_rate;
    } else {
      $get_point = ($order->info['subtotal'] - (int)$point) * $point_rate;
    }
  } else {
    if (isset($_SESSION['campaign_fee'])) {
      $get_point = (abs($order->info['subtotal'])+abs($_SESSION['campaign_fee'])) * $point_rate;
    } else {
      $get_point = abs($order->info['subtotal']) * $point_rate;
    }
  }
  if ($guestchk == '1') {
    $get_point = 0;
  }
  tep_session_register('get_point');
  if(isset($customer_id)&&tep_is_member_customer($customer_id)){
  echo '<tr>' . "\n";
  if (!tep_only_buy_product()) {
    echo '<td align="right" class="main">'.TEXT_POINT_NOW.'</td>' . "\n";
  } else {
    if ($get_point == 0) {
      echo '<td align="right" class="main">'.TS_TEXT_POINT_NOW_TWO.'</td>' . "\n";
    } else {
      echo '<td align="right" class="main">'.TEXT_POINT_NOW.'</td>' . "\n";
    }
  } 

  echo '<td align="right" class="main">'.(int)$get_point.'&nbsp;P</td>' . "\n";
  echo '</tr>' . "\n";
  }
}
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<?php
if (is_array($payment_modules->modules)) {

  if ($confirmation = $payment_modules->confirmation($payment)) {
    ?> 
      <tr> 
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <tr> 
      <td style="color: #000; font-size: 12px; padding: 10px; background: url(images/design/box/dot.gif) bottom repeat-x;"><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td> 
      </tr> 
      <tr> 
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <tr> 
      <td><table class="infoBoxContents"> 
      <tr> 
      <td><table border="0" cellspacing="0" cellpadding="2"> 
      <tr> 
      <td class="main" colspan="4"><?php
      echo $confirmation['title']; ?></td> 
      </tr> 
      <?php
                    if (!isset($confirmation['fields'])) $confirmation['fields'] = NULL;
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
        ?> 
          <tr> 
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
          <td class="main"><?php echo $confirmation['fields'][$i]['title']; ?></td> 
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
          <td class="main"><?php echo $confirmation['fields'][$i]['field']; ?></td> 
          </tr> 
          <?php
      }
    ?> 
                  </table></td> 
              </tr> 
            </table></td> 
        </tr> 
        <?php
    }
  }
?> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
        <?php
  if (tep_not_null($order->info['comments'])) {
?> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> </tr> 
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
 				        <tr> 
          <td class="main"><?php echo '<b>' . HEADING_ORDER_COMMENTS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
        </tr> 

              <tr> 
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                    <tr> 
                      <td class="main"><?php echo nl2br(htmlspecialchars($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']); ?></td> 
                    </tr> 
                  </table></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
        <?php
  }
?> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
              <tr> 
              <td class="main"><b><?php echo TEXT_CONFIRMATION_READ;?></b></td>
                <td align="right" class="main"> <?php


  

  if (is_array($payment_modules->modules)) {
    echo $payment_modules->process_button($payment);
  }
  echo '<a href="javascript:void(0);" onclick="confirm_session_error('.$ad_num.',\''.$ad_post.'\');">';
  echo tep_image_button('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</a></form>' . "\n";
?> </td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td>
      <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr>  
        </table>
        </div>
        <p class="pageBottom"></p>
        </td> 
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> 
      </td> 
    </tr>
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
<!-- visites --> 
<object>
<noscript>
<img src="visites.php" alt="Statistics" style="border:0" />
</noscript>
</object>
<!-- visites -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
