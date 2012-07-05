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
<body>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <div id="main"> 
        <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
      <!-- body_text //--> 
      <div id="layout" class="yui3-u"> 
          <div id="current">
          <?php   
            echo '<a href="'.HTTP_SERVER.'" class="headerNavigation">'.HEADER_TITLE_TOP.'</a>'; 
            echo ' <img src="images/point.gif"> ';
            echo '<a href="javascript:void(0);" class="headerNavigation" onclick="document.forms.order1.submit();">'.CHANGE_PREORDER_BREADCRUMB_FETCH.'</a>';
            echo ' <img src="images/point.gif"> ';
            echo NAVBAR_CHANGE_PREORDER_TITLE; 
          ?> 
          </div>
          <div id="main-content">
          <h2><?php echo NAVBAR_CHANGE_PREORDER_TITLE;?></h2> 
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="100%" class="preorder_title" align="center">
            <tr>
              <td width="33%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?></td> 
                    <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="33%">
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
              <td width="33%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                    <td width="50%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?>
                    </td>
                  </tr>
                </table>  
              </td>
            </tr>
            <tr>
              <td align="center" width="33%" class="preorderBarFrom"><?php echo '<a href="javascript:void(0);" onclick="document.forms.order1.submit();">'.PREORDER_TRADER_LINE_TITLE.'</a>';?></td> 
              <td align="center" width="33%" class="preorderBarcurrent"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="center" width="33%" class="preorderBarTo"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
            </tr>
          </table>
          <?php
          echo tep_draw_form('order', $form_action_url, 'post'); 
          ?>
            <div id="hm-checkout-warp"><div class="checkout-title"><b><?php echo TEXT_ORDERS_SUBMIT_ONE;?></b></div>
  <div class="checkout-bottom">
      

                    <input type="image" onMouseOver="this.src='includes/languages/japanese/images/buttons/button_confirm_order_hover.gif'" onMouseOut="this.src='includes/languages/japanese/images/buttons/button_confirm_order.gif'" alt="<?php echo TEXT_IMAGE_ALT_ONE;?>" src="includes/languages/japanese/images/buttons/button_confirm_order.gif"></div>  
  </div>
  	<div class="checkout-conent">
          <table width="100%" cellpadding="2" cellspacing="2" border="0">
            <tr>
              <td><b>
                <?php echo PRORDER_CONFIRM_PRODUCT_INFO;?> 
                </b>
              </td>
            </tr>
            <?php
              $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
            ?>
            <tr>
              <td>
                <table width="100%" style="font-size:14px;"> 
                  <?php $preorder_product_res = tep_db_fetch_array($preorder_product_raw);?> 
                  <tr>
                   <!-- <td> <?php //echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;?>
</td>-->
<td width="20%">     <?php echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;?>  <?php echo '<br>'.tep_get_full_count2($preorder_product_res['products_quantity'], $preorder_product_res['products_id']);?> 
</td>
                               
                    <td style="padding-left:5px;">
                    <?php 
                    echo $preorder_product_res['products_name'];
                    if (isset($_POST['op_id'])) {
                      echo '<br>';  
                      foreach ($_POST['op_id'] as $key => $value) {
                        $pro_option_raw = tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$key."' and language_id = '".$languages_id."'"); 
                        $pro_option_res = tep_db_fetch_array($pro_option_raw); 
                        
                        $pro_option_value_raw = tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$value."' and language_id = '".$languages_id."'");
                        $pro_option_value_res = tep_db_fetch_array($pro_option_value_raw); 
                        echo $pro_option_res['products_options_name'].':'.$pro_option_value_res['products_options_values_name']; 
                        echo '<br>';  
                      }
                    }
                    ?>
                    </td>                  
                    <td align="right">
                    <?php 
                    if ($preorder_product_res['final_price'] < 0) {
                      echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity'])).'</font>'.JPMONEY_UNIT_TEXT; 
                    } else {
                      echo $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']); 
                    }
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0">
            <tr>
              <td><b>
                <?php echo PRORDER_CONFIRM_FETCH_INFO;?>
                </b> 
              </td>
            </tr>
            <tr>
              <td>
                <table width="100%" style="font-size:14px;"> 
                  <tr>
                    <td width="20%"><?php echo PREORDER_CONFIRM_FETCH_TIME_READ;?></td>                  
                    <td>
                    <?php echo $_POST['torihikihouhou'];?> 
                    </td>                  
                  </tr>
                  <tr>
                    <td><?php echo PREORDER_CONFIRM_FETCH_TIME_DAY;?></td>                  
                    <td>
                    <?php
                      if (!empty($_POST['date'])) {
                        $date_arr = explode('-', $_POST['date']); 
                        echo $date_arr[0].PREORDER_YEAR_TEXT.$date_arr[1].PREORDER_MONTH_TEXT.$date_arr[2].PREORDER_DAY_TEXT; 
                        $_SESSION['date'] = $_POST['date'];
                        $_SESSION['date_array'] =  $date_arr[0].PREORDER_YEAR_TEXT.$date_arr[1].PREORDER_MONTH_TEXT.$date_arr[2].PREORDER_DAY_TEXT; 
                      }
                    ?>
                    </td>                  
                  </tr>
                  <tr>
                    <td><?php echo PREORDER_CONFIRM_FETCH_TIME_DATE;?></td>                  
                    <td>
                    <?php
                    echo $_POST['hour'].PREORDER_HOUR_TEXT.$_POST['min'].PREORDER_MIN_TEXT;
                         $_SESSION['hour'] = $_POST['hour'];
                         $_SESSION['min'] = $_POST['min'];
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
           <table width="100%" cellpadding="2" cellspacing="2" border="0">
                  <tr>
                    <td width="20%">
          <?php echo PREORDER_CONFIRM_CHARACTER;?>
                     </td>
                     <td style="padding-left:6px;"><?php echo $_POST['p_character'];?></td>
                   </tr>
            </table>
          <table width="100%" cellpadding="2" cellspacing="2" border="0">
            <tr>
              <td width="30%" valign="top">
                <table width="100%" cellpadding="2" cellspacing="2" border="0" style="font-size:14px;"> 
                  <tr>
                    <td><b><?php echo CHANGE_ORDER_CONFIRM_PAYMENT;?></b></td>                  
                  </tr>
                  <tr>
                    <td>
                    <?php echo $preorder_res['payment_method'];?> 
                    </td>                  
                  </tr>
                </table> 
              </td>
              <td width="70%" align="right" valign="top">
                <table border="0" cellpadding="2" cellspacing="0" style="font-size:14px;"> 
                  <?php
                  if ($preorder_res['code_fee']) {
                  ?>
                  <tr>
                    <td align="right"><?php echo CHANGE_PREORDER_HANDLE_FEE_TEXT;?></td> 
                    <td align="right"><?php echo $currencies->format_total($preorder_res['code_fee']);?></td> 
                  </tr>
                  <?php
                  }
                  $total_param = '0'; 
                  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."' order by sort_order asc"); 
                  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) { 
                    if ($preorder_total_res['class'] == 'ot_total') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        $total_param = number_format($preorder_total_res['value'], 0, '.', '')+$_SESSION['preorder_campaign_fee']; 
                      } else {
                        $total_param = number_format($preorder_total_res['value'], 0, '.', '')-(int)$preorder_point; 
                      }
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
                    <td align="right"><?php echo $preorder_total_res['title'];?></td>                  
                    <td align="right">
                    <?php 
                    if ($preorder_total_res['class'] == 'ot_point') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total(abs($_SESSION['preorder_campaign_fee']))).'</font>'.JPMONEY_UNIT_TEXT;
                      } else {
                        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total((int)$preorder_point)).'</font>'.JPMONEY_UNIT_TEXT;
                      }
                    } else if ($preorder_total_res['class'] == 'ot_total') {
                      if (isset($_SESSION['preorder_campaign_fee'])) {
                        echo $currencies->format_total($preorder_total_res['value']+(int)$_SESSION['preorder_campaign_fee']);
                      } else {
                        echo $currencies->format_total($preorder_total_res['value']-(int)$preorder_point);
                      }
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
     
    $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
    }
  }
  //----------------------------------------------
  
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
                    <td align="right">
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
                    <td align="right"><?php echo (int)$preorder_get_point.'&nbsp;P';?></td> 
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
            </div>
            <div id="hm-checkout-warp">
            <div class="checkout-title"><b><?php echo TEXT_ORDERS_SUBMIT_ONE;?></b></div>
            <div class="checkout-bottom">
      <?php
      $payment_modules->preorder_process_button($con_payment_code, $_POST['pid'], $total_param);
                  ?>
      <input type="image" onMouseOver="this.src='includes/languages/japanese/images/buttons/button_confirm_order_hover.gif'" onMouseOut="this.src='includes/languages/japanese/images/buttons/button_confirm_order.gif'" alt="<?php echo TEXT_IMAGE_ALT_ONE;?>" src="includes/languages/japanese/images/buttons/button_confirm_order.gif">
</div>
</div>
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
      <!-- body_text_eof //--> 
        <?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //--> 
  <!-- footer //--> 

  <!-- footer_eof //--> 
</div>
<?php include("includes/float-box.php");?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<object>
<noscript>
<img src="visites.php" alt="Statistics" style="border:0">
</noscript>
</object>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
