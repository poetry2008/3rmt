<?php
/*
   $Id$
 */
require('includes/application_top.php');
$_POST = $_SESSION['preorder_products_list'];
if(isset($_GET['action']) && $_GET['action'] == 'check'){

  if(!isset($_SESSION['submit_flag'])){

    echo 'true';
  }else{
    echo 'false'; 
  }
  exit;
}
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER_CONFIRMATION);
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);

$product_info_raw = tep_db_query("
    select pd.products_id, pd.products_name, pd.products_status, pd.romaji, pd.preorder_status 
    from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where pd.products_id = '" . $_POST['products_id'] . "' 
      and pd.language_id = '" . $languages_id . "' 
      and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
    order by pd.site_id DESC
    limit 1
");
$valid_product = (tep_db_num_rows($product_info_raw) > 0);

if (!$valid_product) {
  forward404(); 
}
$product_info_res = tep_db_fetch_array($product_info_raw);

if ($product_info_res['preorder_status'] != '1') {
  forward404(); 
}
$ca_path = tep_get_product_path($product_info_res['products_id']);
if (tep_not_null($ca_path)) {
  $ca_path_array = tep_parse_category_path($ca_path); 
}
if (isset($ca_path_array)) {
  for ($cnum = 0, $ctnum=sizeof($ca_path_array); $cnum<$ctnum; $cnum++) {
    $categories_query = tep_db_query("
        select categories_name 
        from " .  TABLE_CATEGORIES_DESCRIPTION . " 
        where categories_id = '" .  $ca_path_array[$cnum] . "' 
          and language_id='" . $languages_id . "' 
          and (site_id = ".SITE_ID." or site_id = 0)
        order by site_id DESC
        limit 1" 
    );
    if (tep_db_num_rows($categories_query) > 0) {
      $categories_info = tep_db_fetch_array($categories_query); $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
    } else {
      break;
    }
  }
}

$breadcrumb->add($product_info_res['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$product_info_res['products_id']));
$breadcrumb->add(HEADING_TITLE);

page_head();
?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function check_error(){

  $.ajax({
     url: '<?php echo FILENAME_PREORDER_CONFIRMATION;?>?action=check',
     data: '',
     type: 'POST',
     dataType: 'text',
     async : false,
     success: function(data){
       if(data == 'false'){
         document.forms.preorder_confirmation.submit();
       }else{
         alert('<?php echo TEXT_SUBMIT_ERROR;?>');
         document.location.href='<?php echo FILENAME_CREATE_INDEX;?>'; 
       }
     }
  }); 
}
$(document).ready(function(){
<?php
if(!isset($_SESSION['submit_flag'])){
?>
  alert('<?php echo TEXT_SUBMIT_ERROR;?>');
  document.location.href='<?php echo FILENAME_CREATE_INDEX;?>';
<?php
}
?>
});
</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<?php echo tep_draw_form('preorder_confirmation', tep_href_link(FILENAME_PREORDER_PROCESS,'action=process','SSL'), 'post', '');?>
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
<tr> 
<td width="<?php echo BOX_WIDTH; ?>" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
<!-- left_navigation_eof //--> </td> 
<!-- body_text //--> 
<td valign="top" id="contents"> <div class="pageHeading"><?php echo HEADING_TITLE ; ?></div>      
<div class="comment">
<table class="product_info_box" border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
  <td>
<?php
//提交表单处理
if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
      $_SESSION['preorder_products_list'] = array_merge($_SESSION['preorder_products_list'],$_POST);
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']);
      foreach ($_POST as $p_key => $p_value) {
        if ($p_key != 'x' && $p_key != 'y') {
          echo tep_draw_hidden_field($p_key, stripslashes($p_value)); 
        }
      }
      $product_query = tep_db_query("select products_price, products_price_offset, products_tax_class_id, products_small_sum from ".TABLE_PRODUCTS." where products_id = '".$_POST['products_id']."'"); 
      $product_res = tep_db_fetch_array($product_query);
      $preorder_subtotal = 0; 
      if ($product_res) {
        $products_tax = tep_get_tax_rate($product_res['products_tax_class_id']); 
        $products_price = tep_get_final_price($product_res['products_price'], $product_res['products_price_offset'], $product_res['products_small_sum'], $_POST['quantity']); 
        $preorder_subtotal = tep_add_tax($products_price, $products_tax) * $_POST['quantity']; 
      }
      echo tep_draw_hidden_field('preorder_subtotal', $preorder_subtotal);  
    }
?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
  <tr> 
  <td width="20%">
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
  <tr> 
  <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
  <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
  </tr> 
  </table></td>  
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
  </table>
  
  
  </td> 
</tr>
<td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_PREORDER_PAYMENT, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PAYMENT . '</a>'; ?></td> 
<td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
<td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
<tr> 
<td class="main"><b><?php echo TEXT_CONFIRMATION_READ;?></b></td> 
<td class="main" align="right"><a href="javascript:void(0);" onClick="check_error();"><?php echo tep_image_button('button_preorder.gif', IMAGE_BUTTON_PREORDER);?></a></td> 
</tr> 
</table>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
<?php
//获取商品相关信息
$product_info = tep_get_product_by_id((int)$_POST['products_id'], SITE_ID, $languages_id);
?>
  <tr class="infoBoxContents"> 
<td width="<?php echo (($sendto != false) ? '70%' : '100%'); ?>" valign="top">
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
  <tr> 
  <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
      <td class="main" colspan="3"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_preorder_href_link($product_info['products_id'], $product_info['romaji']) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr> 
<?php
//商品option信息
$preorder_option_array = array();
$preorder_option_price = 0;
foreach($_POST as $key=>$value){

  if(substr($key,0,3) == 'op_'){

    $preorder_key_array = explode('_',$key);
    $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$preorder_key_array[3]."' and group_id = '".$preorder_key_array[2]."'");
    $item_res = tep_db_fetch_array($item_raw);
    tep_db_free_result($item_raw);
    $preorder_option_array[] = array(
                                     'front_title'=>$item_res['front_title'], 
                                     'item_id'=>$preorder_key_array[3],
                                     'group_id'=>$preorder_key_array[2],
                                     'value'=>$value,
                                     'price'=>$item_res['price']
                                   );
    $preorder_option_price += $item_res['price'];
  }
}
$preorder_products_array = array();
//商品信息
$tax_address_query = tep_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '1'");
$tax_address = tep_db_fetch_array($tax_address_query);
$preorder_products_array = array(
                                    'qty'=>$_POST['quantity'],
                                    'name'=>$_POST['products_name'],
                                    'search_name'=>$_POST['products_name'],
                                    'model'=>$product_info['products_model'], 
                                    'tax'=>tep_get_tax_rate($product_info['products_tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']), 
                                    'tax_description'=>tep_get_tax_description($product_info['products_tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                                    'price'=>0,
                                    'final_price'=>0,
                                    'weight'=>$product_info['products_weight'],
                                    'id'=>$_POST['products_id'],
                                    'op_attributes'=>$preorder_option_array,
                                    );
  //商品信息列表    
  echo '          <tr>' . "\n" .
    '            <td align="right" valign="top" class="confirmation_product_num_info">' .
    $preorder_products_array['qty'] . '&nbsp;'. NUM_UNIT_TEXT.  (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($preorder_products_array['qty'], (int)$preorder_products_array['id']) ? '<br><span style="font-size:10px">'.  tep_get_full_count_in_order2($preorder_products_array['qty'], (int)$preorder_products_array['id']) .'</span>': '') . '</td>' . "\n" .
    '            <td class="main" valign="top">' . $preorder_products_array['name'];
  if ($preorder_products_array['price'] < 0) {
    echo ' (<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_products_array['price'], $preorder_products_array['tax'])).'</font>'.JPMONEY_UNIT_TEXT.')';
  } else {
    echo ' ('.$currencies->display_price($preorder_products_array['price'], $preorder_products_array['tax']).')';
  }
 
  $all_show_option_id = array();
  $all_show_option = array();
  $option_item_order_sql = "select it.id from ".TABLE_PRODUCTS."
  p,".TABLE_OPTION_ITEM." it 
  where p.products_id = '".(int)$preorder_products_array['id']."' 
  and p.belong_to_option = it.group_id 
  and it.status = 1
  order by it.sort_num,it.title";
  $option_item_order_query = tep_db_query($option_item_order_sql);
  while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
    $all_show_option_id[] = $show_option_row_item['id'];
  }
  if ( (isset($preorder_products_array['op_attributes'])) && (sizeof($preorder_products_array['op_attributes']) > 0) ) {
    for ($j=0, $n2=sizeof($preorder_products_array['op_attributes']); $j<$n2; $j++) {  
      $all_show_option[$preorder_products_array['op_attributes'][$j]['item_id']] 
      = $preorder_products_array['op_attributes'][$j];
      
    }
  }
  
  if ( (isset($preorder_products_array['ck_attributes'])) && (sizeof($preorder_products_array['ck_attributes']) > 0) ) {
   for ($jk=0, $n3=sizeof($preorder_products_array['ck_attributes']); $jk<$n3; $jk++) {
      $all_show_option[$preorder_products_array['ck_attributes'][$jk]['item_id']] 
      = $preorder_products_array['ck_attributes'][$jk];
      
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
          $op_price = 0;
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
  echo '            <td class="main" align="right" valign="top">';
  if ($preorder_products_array['final_price'] < 0) {
    echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty'])).'</font>'.JPMONEY_UNIT_TEXT;
  } else {
    echo $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty']);
  }
  echo '</td>' . "\n" .
    '          </tr>' . "\n";
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<?php
$payment = $_POST['pre_payment'];
if (!$payment_modules->moduleIsEnabled($payment)){
  //判断支付方法是否存在， 支付方法是否被允许 
  $_SESSION['payment_error'] = ERROR_NO_PAYMENT_MODULE_SELECTED;
  tep_redirect(tep_href_link(FILENAME_PREORDER_PAYMENT, '', 'SSL'));
}
$payment_selection = $payment_modules->selection();
$pay_info_array = $payment_modules->specialOutput($payment, true, $_SESSION['preorder_products_list']);
$payment_modules->deal_other_info($payment, $_POST); 
//支付方法相关信息
if (!empty($pay_info_array)) {
?>
<tr>
<td>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
  <tr class="infoBoxContents"> 
  <td>
  <table width="100%" cellspacing="0" cellpadding="2" border="0">
  <tr>
  <td class="main" colspan="3">
  <b><?php echo $pay_info_array[0];?></b>

<?php
echo '<a href="' .  tep_href_link(FILENAME_PREORDER_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>';
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

<?php
}
?>
<tr> 
<td style="color: #000; font-size: 12px; padding: 10px 10px 10px 8px; background: url(images/design/box/dot.gif) bottom repeat-x;">&nbsp;<b><?php echo HEADING_BILLING_INFORMATION; ?></b></td> 
</tr> 
<tr> 
<td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
<tr class="infoBoxContents"> 
<td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
<tr> 
<td class="main" colspan="2"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_PREORDER_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
</tr> 
<tr> 
<td width="10"></td>
<td class="main" width="125"><?php echo payment::changeRomaji($payment); ?></td> 
</tr> 
</table></td> 
<td width="70%" valign="top" align="right">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr><td class="main" align="right"><?php echo HEADING_SUBTOTAL;?>:</td><td class="main" align="right" width="25%">
<?php
//小计
if ($preorder_products_array['final_price'] < 0) {
    echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty'])).'</font>'.JPMONEY_UNIT_TEXT;
  } else {
    echo $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty']);
  }
?>
</td></tr>
<tr><td class="main" align="right"><?php echo HEADING_TOTAL;?>:</td><td class="main" align="right">
<?php
//合计
if ($preorder_products_array['final_price'] < 0) {
    echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty'])).'</font>'.JPMONEY_UNIT_TEXT;
  } else {
    echo $currencies->display_price($preorder_products_array['final_price'], $preorder_products_array['tax'], $preorder_products_array['qty']);
  }
?>
</td></tr>
</table>
</td> 
</tr> 
</table></td> 
</tr> 
<?php
//支付方法关联信息
if (is_array($payment_modules->modules)) {

  if ($confirmation = $payment_modules->confirmation($payment)) {
    ?> 
      <tr> 
      <td style="color: #000; font-size: 12px; padding: 10px 10px 10px 8px; background: url(images/design/box/dot.gif) bottom repeat-x;">&nbsp;<b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td> 
      </tr> 
      <tr> 
      <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
      <tr class="infoBoxContents"> 
      <td><table border="0" cellspacing="0" cellpadding="2"> 
      <tr> 
      <td class="main" colspan="4"><?php
      echo $confirmation['title']; ?></td> 
      </tr> 
      <?php
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
      <?php

      ?>
      </table></td> 
      </tr> 
      </table></td> 
      </tr> 
      <?php
  }
}
?>  
<?php
//顾客备注信息
if (tep_not_null($_POST['yourmessage'])) {
  ?>  
    <tr> 
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
    </tr> 
    <tr> 
    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
    <tr> 
    <td class="main"><?php echo '<b>' . HEADING_ORDER_COMMENTS . '</b> <a href="' . tep_href_link(FILENAME_PREORDER_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr>
    <tr class="infoBoxContents"> 
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
    <td class="main"><div class="payment_comment"><?php echo nl2br(htmlspecialchars($_POST['yourmessage'])); ?></div></td> 
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
<td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
<tr> 
<td class="main"><b><?php echo TEXT_CONFIRMATION_READ;?></b></td>
<td align="right" class="main"> <?php
if (is_array($payment_modules->modules)) {
  echo $payment_modules->process_button($payment);
}
echo '<a href="javascript:void(0);" onclick="check_error();">';
echo tep_image_button('button_preorder.gif', IMAGE_BUTTON_PREORDER) . '</a></form>' . "\n";
?> </td> 
</tr> 
</table></td> 
</tr> 
</table>
</div>
<p class="pageBottom"></p>
</td> 
<!-- body_text_eof //--> 
<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
<!-- right_navigation_eof //--> 
</td> 
</tr>
</table> 
<!-- body_eof //--> 
<!-- footer //--> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
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
