<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'change_preorder_confirm.php');
  
  $breadcrumb->add(NAVBAR_CHANGE_PREORDER_TITLE, '');
  /*
 * 计算配送费用
 */
  foreach($_POST as $shipping_key=>$shipping_value){

    if(substr($shipping_key,0,3) == 'ad_'){

      $shipping_fee_array[] = $shipping_value;
    }
  }
  //计算商品的总价格及总重量
  $weight_total = 0;
  $money_total = 0;
  $shipping_products_query = tep_db_query("select * from ". TABLE_PREORDERS_PRODUCTS ." where orders_id='". $_POST['pid'] ."'");
  while($shipping_products_array = tep_db_fetch_array($shipping_products_query)){

    $shipping_products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $shipping_products_array['products_id'] ."'");
    $shipping_products_weight_array = tep_db_fetch_array($shipping_products_weight_query);
    tep_db_free_result($shipping_products_weight_query);
    $weight_total += $shipping_products_weight_array['products_weight']*$shipping_products_array['products_quantity'];
    $money_total += $shipping_products_array['final_price']*$shipping_products_array['products_quantity'];
  }
  tep_db_free_result($shipping_products_query);


$weight = $weight_total;

foreach($shipping_fee_array as $op_value){
  $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value ."' and status='0'");
  $city_num = tep_db_num_rows($city_query);

  $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value ."' and status='0'");
  $address_num = tep_db_num_rows($address_query);
  
  $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value ."' and status='0'");
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

$shipping_fee = $money_total > $free_value ? 0 : $weight_fee;

?>
<?php page_head();?>
<script type="text/javascript">
</script>
<script type="text/javascript" src="js/data.js"></script>
<script type="text/javascript">
<!--
var a_vars = Array();
var pagename='';
var visitesSite = 1;
var visitesURL = "<?php echo ($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER; ?>/visites.php";
<?php
  require(DIR_WS_ACTIONS.'visites.js');
?>
//-->
</script>
</head>
<body><div align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
          <h1 class="pageHeading"><?php echo NAVBAR_CHANGE_PREORDER_TITLE;?></h1> 
          <div class="comment">
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
            <tr>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="30%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?></td> 
                    <td width="70%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="60%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td> 
                    <td><?php echo tep_image(DIR_WS_IMAGES.'checkout_bullet.gif');?></td> 
                    <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="70%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                    <td width="30%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?>
                    </td>
                  </tr>
                </table>  
              </td>
            </tr>
            <tr>
              <td align="left" width="20%" class="preorderBarFrom"><?php echo '<a href="javascript:void(0);" onclick="document.forms.order1.submit();" class="preorderBarFrom">'.PREORDER_TRADER_LINE_TITLE.'</a>';?></td> 
              <td align="center" width="60%" class="preorderBarcurrent"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="right" width="20%" class="preorderBarTo"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
            </tr>
          </table>
          <?php
          echo tep_draw_form('order', $form_action_url, 'post'); 
          ?>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="c_pay_info">
            <tr>
              <td class="main">
              <?php echo CHANGE_PREORDER_CONFIRM_BUTTON_INFO;?> 
              </td>
              <td class="main" align="right">
                <?php echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONTINUE);?> 
              </td>
            </tr>
          </table>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main">
                <?php echo PRORDER_CONFIRM_PRODUCT_INFO;?> 
              </td>
            </tr>
            <?php
              $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
            ?>
            <tr>
              <td class="main">
                <table width="100%"> 
                  <?php $preorder_product_res = tep_db_fetch_array($preorder_product_raw);?> 
                  <tr>
                    <td class="main">
                    <?php 
                    echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;
                    ?>
                    </td>                  
                    <td class="main">
                    <?php 
                    echo $preorder_product_res['products_name'];
                    if ($preorder_product_res['products_price'] != '0') {
                      echo ' ('.$currencies->display_price($preorder_product_res['products_price'], $preorder_product_res['products_tax']).')'; 
                    } else if ($preorder_product_res['final_price'] != '0') {
                      echo ' ('.$currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax']).')'; 
                    }
                    $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_POST['pid']."'"); 
                    while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
                      echo '<br>';  
                      $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info'])); 
                      echo $old_attr_info['title'].':'.$old_attr_info['value'];
                      if ($old_attr_res['options_values_price'] != '0') {
                        if ($preorder_product_res['products_price'] != '0') {
                          echo ' ('.$currencies->format($old_attr_res['options_values_price']*$preorder_product_res['products_quantity']).')'; 
                        } 
                      }
                    }
                    if (!empty($option_info_array)) {
                      echo '<br>';  
                      foreach ($option_info_array as $of_key => $of_value) {
                        $of_key_array = explode('_', $of_key); 
                        $option_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$of_key_array[3]."' and name = '".$of_key_array[1]."'");  
                        $option_item = tep_db_fetch_array($option_item_query); 
                        if ($option_item) {
                          echo $option_item['front_title'].':'.$of_value; 
                          if ($option_item['type'] == 'radio') {
                            $r_option_array = @unserialize($option_item['option']);
                            if (!empty($r_option_array['radio_image'])) {
                              foreach ($r_option_array['radio_image'] as $ro_key => $ro_value) {
                                if (trim($ro_value['title']) == trim($of_value)) {
                                  if ($ro_value['money'] != '') {
                                    echo ' ('.$currencies->format($ro_value['money']*$preorder_product_res['products_quantity']).')'; 
                                  }
                                  break; 
                                }
                              }
                            }
                          } else {
                            if ($option_item['price'] != '0') {
                              echo ' ('.$currencies->format($option_item['price']*$preorder_product_res['products_quantity']).')'; 
                            }
                          }
                          echo '<br>'; 
                        }
                      }
                    }
                    ?>
                    </td>                  
                    <td class="main">
                    <?php 
                    if (isset($preorder_total_info_array['final_price'])) {
                      if ($preorder_total_info_array['final_price'] < 0) {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_total_info_array['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity'])).'</font>'.JPMONEY_UNIT_TEXT; 
                      } else {
                        echo $currencies->display_price($preorder_total_info_array['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']); 
                      }
                    } else {
                      if ($preorder_product_res['final_price'] < 0) {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity'])).'</font>'.JPMONEY_UNIT_TEXT; 
                      } else {
                        echo $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']); 
                      }
                    }
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br>
<?php
                    if($weight_total > 0){
?>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main">
                <?php echo TEXT_ADDRESS;?> 
              </td>
            </tr>
            <tr>
              <td class="main">
                <table width="100%">
<?php
                    foreach($_POST as $ad_key=>$ad_value){

                      if(substr($ad_key,0,3)=='ad_' && $_POST[substr($ad_key,3)] != ''){

                        echo '<tr>';
                        echo '<td class="main">'. $_POST[substr($ad_key,3)] .':</td>';                  
                        echo '<td class="main">';
                        echo $_POST[$ad_key];
                        echo '<input type="hidden" name="'. $ad_key .'" value="'. $ad_value .'"></td>';
                        echo '</tr>';
                      }
                    }
?> 
                  
                </table> 
              </td>
            </tr>
          </table>
          <br> 
<?php
}
?>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main">
                <?php echo PRORDER_CONFIRM_FETCH_INFO;?> 
              </td>
            </tr>
            <tr>
              <td class="main">
                <table width="100%">  
                  <tr>
                    <td class="main"><?php echo PREORDER_CONFIRM_FETCH_TIME_DAY;?></td>                  
                    <td class="main">
                    <?php
                      if (!empty($_POST['date'])) {
                        $date_arr = explode('-', $_POST['date']); 
                        echo $date_arr[0].PREORDER_YEAR_TEXT.$date_arr[1].PREORDER_MONTH_TEXT.$date_arr[2].PREORDER_DAY_TEXT; 
                      }
                    ?>
                    </td>                  
                  </tr>
                  <tr>
                    <td class="main"><?php echo PREORDER_CONFIRM_FETCH_TIME_DATE;?></td>                  
                    <td class="main">
                    <?php
                    echo $_POST['start_hour'].PREORDER_HOUR_TEXT.$_POST['start_min'].PREORDER_MIN_TEXT.'~'.$_POST['end_hour'].PREORDER_HOUR_TEXT.$_POST['end_min'].PREORDER_MIN_TEXT; 
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="30%" valign="top">
                <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea_td"> 
                  <tr>
                    <td class="main"><?php echo CHANGE_ORDER_CONFIRM_PAYMENT;?></td>                  
                  </tr>
                  <tr>
                    <td class="main">
                    <?php echo $preorder_res['payment_method'];?> 
                    </td>                  
                  </tr>
                </table> 
              </td>
              <td width="70%" align="right" valign="top">
                <table border="0" cellpadding="2" cellspacing="0"> 
                  <?php
                  if (!empty($preorder_total_info_array['fee'])) {
                  ?>
                  <tr>
                    <td class="main" align="right"><?php echo CHANGE_PREORDER_HANDLE_FEE_TEXT;?></td> 
                    <td class="main" align="right"><?php echo $currencies->format_total($preorder_total_info_array['fee']);?></td> 
                  </tr>
                  <?php
                  } else {
                    if ($preorder_res['code_fee']) {
                  ?>
                  <tr>
                    <td class="main" align="right"><?php echo CHANGE_PREORDER_HANDLE_FEE_TEXT;?></td> 
                    <td class="main" align="right"><?php echo $currencies->format_total($preorder_res['code_fee']);?></td> 
                  </tr>
                  <?php
                    }
                  }
                  $total_param = '0'; 
                  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."' order by sort_order asc"); 
                  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) { 
                    if ($preorder_total_res['class'] == 'ot_total') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        if (isset($preorder_total_info_array['total'])) {
                          $total_param = number_format($preorder_total_info_array['total'], 0, '.', '')+$_SESSION['preorder_campaign_fee']; 
                        } else {
                          $total_param = number_format($preorder_total_res['value'], 0, '.', '')+$_SESSION['preorder_campaign_fee']; 
                        }
                      } else {
                        if (isset($preorder_total_info_array['total'])) {
                          $total_param = number_format($preorder_total_info_array['total'], 0, '.', '')-(int)$preorder_point; 
                        } else {
                          $total_param = number_format($preorder_total_res['value'], 0, '.', '')-(int)$preorder_point; 
                        }
                      }
                    }
                    if(isset($shipping_fee)){

                      $total_param += $shipping_fee;
                    }
                    
                  ?>
                  <?php
                    if ($preorder_total_res['class'] == 'ot_point') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        if ($_SESSION['preorder_campaign_fee'] == 0) {
                          continue; 
                        }
                      } else {
                        if ((int)$preorder_point == 0) {
                          continue; 
                        }
                      }
                    }
                  ?>
                  <tr>
                    <td class="main" align="right"><?php echo $preorder_total_res['title'];?></td>                  
                    <td class="main" align="right">
                    <?php 
                    if ($preorder_total_res['class'] == 'ot_point') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total(abs($_SESSION['preorder_campaign_fee']))).'</font>'.JPMONEY_UNIT_TEXT;
                      } else {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total((int)$preorder_point)).'</font>'.JPMONEY_UNIT_TEXT;
                      }
                    } else if ($preorder_total_res['class'] == 'ot_total') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        if (isset($preorder_total_info_array['total'])) {
                          echo $currencies->format_total($preorder_total_info_array['total']+(int)$_SESSION['preorder_campaign_fee']+(int)$shipping_fee);
                        } else {
                          echo $currencies->format_total($preorder_total_res['value']+(int)$_SESSION['preorder_campaign_fee']+(int)$shipping_fee);
                        }
                      } else {
                        if (isset($preorder_total_info_array['total'])) {
                          echo $currencies->format_total($preorder_total_info_array['total']-(int)$preorder_point+(int)$shipping_fee);
                        } else {
                          echo $currencies->format_total($preorder_total_res['value']-(int)$preorder_point+(int)$shipping_fee);
                        }
                      }
                    } else if($preorder_total_res['class'] == 'ot_subtotal') {
                      if (isset($preorder_total_info_array['subtotal'])) {
                        echo $currencies->format_total($preorder_total_info_array['subtotal']);
                      } else {
                        echo $currencies->format_total($preorder_total_res['value']);
                      }
                      
                      $shipping_fee_str = $shipping_fee == 0 ? TEXT_SHIPPING_FEE_NOT : $currencies->format_total($shipping_fee);
                      ?>
                    <tr>
                    <td class="main" align="right"><input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee;?>"><?php echo TEXT_SHIPPING_FEE;?></td> 
                    <td class="main" align="right"><?php echo $shipping_fee_str;?></td> 
                    </tr>
                    <?php
                    } else {
                      echo $currencies->format_total($preorder_total_res['value']);
                    }
                    ?>
                    </td>                  
                  </tr>
                <?php }?> 
                  <?php
if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
  
  $ptoday = date("Y-m-d H:i:s", time());
  $pstday_array = getdate();
  $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
  
  $total_buyed_date = 0;
  // ccdd
  $customer_level_total_query = tep_db_query("select * from preorders where customers_id = '".$preorder_res['customers_id']."' and date_purchased >= '".$pstday."' and site_id = ".SITE_ID);
  if(tep_db_num_rows($customer_level_total_query)) {
    while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
      $cltotal_subtotal_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
    $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
  
      $cltotal_point_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
    $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
    if (isset($preorder_total_info_array['subtotal'])) {
      $total_buyed_date += ($preorder_total_info_array['subtotal'] - $cltotal_point['value']);
    } else {
      $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
    }
    }
  }
  //----------------------------------------------
  
  //丛傅唯を纷换----------------------------------
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
// ここまでカスタマ〖レベルに炳じたポイント丛傅唯换叫============================================================
  if ($preorder_subtotal > 0) {
    if (isset($_SESSION['preorder_campaign_fee'])) {
      $preorder_get_point = ($preorder_subtotal + $_SESSION['preorder_campaign_fee']) * $point_rate;
    } else {
      $preorder_get_point = ($preorder_subtotal - (int)$preorder_point) * $point_rate;
    }
  } else {
    if (isset($payment_modules->modules[strtoupper($con_payment_code)]->show_point)) {
      $show_point_single = true; 
      if (isset($_SESSION['preorder_campaign_fee'])) {
        $preorder_get_point = abs($preorder_subtotal)+abs($_SESSION['preorder_campaign_fee']);
      } else {
        $preorder_get_point = abs($preorder_subtotal);
      }
    } else {
      $preorder_get_point = 0;
    }
  }
  
  if ($is_guest_single) {
    $preorder_get_point = 0;
  }
  
  if (!tep_session_is_registered('preorder_get_point')) {
    tep_session_register('preorder_get_point');
  }
}
                  ?>
                  <tr>
                    <td class="main" align="right">
                    <?php 
                    if (isset($show_point_single)) {
                      if ($preorder_get_point == 0) {
                        echo CHANGE_PREORDER_POINT_TEXT_BUY;
                      } else {
                        echo CHANGE_PREORDER_POINT_TEXT;
                      }
                    } else {
                      echo CHANGE_PREORDER_POINT_TEXT;
                    }
                    ?>
                    </td> 
                    <td class="main" align="right"><?php echo (int)$preorder_get_point.'&nbsp;P';?></td> 
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="c_pay_info">
            <tr>
              <td class="main">
              <?php echo CHANGE_PREORDER_CONFIRM_BUTTON_INFO;?> 
              </td>
              <td class="main" align="right">
                <?php
                $payment_modules->preorder_process_button($con_payment_code, $_POST['pid'], $total_param); 
                ?>
                <?php echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONTINUE);?> 
              </td>
            </tr>
          </table> 
          </form> 
          <?php 
          echo tep_draw_form('order1', tep_href_link('change_preorder.php?pid='.$check_preorder_str));
          foreach ($_POST as $post_key => $post_value) {
            if ($post_key == 'action' || $post_key == 'x' || $post_key == 'y') {
              continue; 
            }
            if (is_array($post_value)) {
              foreach ($post_value as $ps_key => $ps_value) {
                echo tep_draw_hidden_field($post_key.'['.$ps_key.']', $ps_value); 
              }
            } else {
              echo tep_draw_hidden_field($post_key, $post_value); 
            }
          }
          echo '</form>';
          ?> 
          </div>
          <p class="pageBottom"></p>
      </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div>
</div>
<object>
<noscript>
<img src="visites.php" alt="Statistics" style="border:0">
</noscript>
</object>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
