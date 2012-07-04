<?php
/*
 $Id$
*/
require('includes/application_top.php');
require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_REORDER);

$breadcrumb->add(TEXT_BREADCRUMB_TITLE, tep_href_link('reorder.php'));
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof -->
  <!-- body -->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="table">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation -->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof -->
      </td>
      <!-- body_text -->
      <td id="contents" valign="top">
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
<?php if ($_POST) {
  include(DIR_WS_CLASSES . 'admin_order.php');

  if(isset($_POST['order_id'])){
    $oID    = tep_db_prepare_input($_POST['order_id']);
  } else {
    $oID    = tep_db_prepare_input($_POST['order_id_1']).'-'.tep_db_prepare_input($_POST['order_id_2']);
  }
  
  $cEmail = tep_db_prepare_input($_POST['email']);
  $cEmail = str_replace("\xe2\x80\x8b", '', $cEmail);
  
  $o      = new order($oID);
  // ccdd
  $order  = tep_db_fetch_array(tep_db_query("
        select * 
        from `".TABLE_ORDERS."` 
        where site_id = '" . SITE_ID . "' 
          and `orders_id` = '".$oID."' 
          and `customers_email_address` = '".$cEmail."'
        "));

  if ($order) {
    if (isset($_POST['hour'])){
      $date   = tep_db_prepare_input($_POST['date']);
      $hour   = tep_db_prepare_input($_POST['hour']);
      $minute = tep_db_prepare_input($_POST['minute']);
      
      $comment = tep_db_prepare_input($_POST['comment']);

      $datetime = $date.' '.$hour.':'.$minute;
      $time     = strtotime($datetime);

      //if (in_array($order['orders_status'], array(2,5,6,7,8))) {
      if (tep_orders_status_finished($order['orders_status'])) {
        // status can not change
        echo '<div class="comment">'.TEXT_DELETE_ORDER_SUCCESS.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
      } else if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
        // time error
        echo '<div class="comment">'.TEXT_INFO_FOR_TRADE.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="'.TEXT_BACK_TO_TOP.'"></a></div></div>';
      } else {
        // update time
        if ($date && $hour && $minute) {
          // ccdd
          /*
          tep_db_query("
              update `".TABLE_ORDERS."` 
              set `orders_status`='17' ,
                  `torihiki_date` = '".$datetime."' ,
                  `last_modified` = now()
              WHERE `orders_id`='".$order_id."' 
                and site_id = '".SITE_ID."'
          ");
          orders_updated($order_id);
          last_customer_action();
          // insert a history
          $sql = "
            INSERT INTO `".TABLE_ORDERS_STATUS_HISTORY."` (
                `orders_status_history_id`,
                `orders_id` ,
                `orders_status_id` ,
                `date_added` ,
                `customer_notified` ,
                `comments`
              ) VALUES (
                NULL ,
                '".$order_id."', 
                '17', 
                '".date("Y-m-d H:i:s")."', 
                '1', 
                '".mysql_real_escape_string($comment)."'
              )
          ";
          // ccdd
          tep_db_query($sql);
          */
        }

        // update character
        if (isset($_POST['character']) && is_array($_POST['character'])){
          foreach($_POST['character'] as $pid=>$character){
            // ccdd
            tep_db_query("
                update `".TABLE_ORDERS_PRODUCTS."` 
                set `products_character`='".mysql_real_escape_string($character)."' 
                where `orders_id`='".$oID."' 
                  and `products_id`='".$pid."'
            ");
          }
        }
        // update attributes
        if($o->products){
          foreach($o->products as $p){
            if(isset($p['attributes']) && $p['attributes']){
              foreach($p['attributes'] as $a) {
                if(isset($_POST['id'][$p['id']])) {
                  // old attribute
                  // ccdd
                  $attributes = tep_db_fetch_array(tep_db_query("
                        select * 
                        from `".TABLE_PRODUCTS_ATTRIBUTES."` 
                        where `products_attributes_id`='".$a['attributes_id']."'
                  "));
                  if(isset($_POST['id'][(int)$p['id']][(int)$attributes['options_id']]) && $_POST['id'][(int)$p['id']][(int)$attributes['options_id']]){
                    // new option
                    // ccdd
                    $option = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_OPTIONS."` 
                          where `products_options_id`='".$attributes['options_id']."'
                    "));
                    // new attribute
                    // ccdd
                    $nattribute = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_ATTRIBUTES."` 
                          where `products_id`='".$p['id']."' 
                            and `options_id`='".$attributes['options_id']."' 
                            and `options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'
                    "));
                    // new option value
                    // ccdd
                    $value = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_OPTIONS_VALUES."` 
                          where `products_options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'
                    "));
                    // execute update`
                    // ccdd
                    $update_query = tep_db_query("
                        update `".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."` 
                        set `products_options_values`='".$value['products_options_values_name']."',
                            `attributes_id`='".$nattribute['products_attributes_id']."' 
                        where `orders_id`='".$oID."' 
                          and `products_options`='".$option['products_options_name']."' 
                          and `attributes_id`='".$a['attributes_id']."'
                    ");
                  }
                }
              }
            }
          }
        }
        //change order status and insert order status history
        if ($date && $hour && $minute) {
          tep_db_query("
              update `".TABLE_ORDERS."` 
              set `orders_status`='17' ,
                  `torihiki_date` = '".$datetime."' ,
                  `last_modified` = now()
              WHERE `orders_id`='".$order_id."' 
                and site_id = '".SITE_ID."'
          ");
          orders_updated($order_id);
          last_customer_action();
        }else{
          tep_db_query("
              update `".TABLE_ORDERS."` 
              set `orders_status`='17' ,
                  `last_modified` = now()
              WHERE `orders_id`='".$order_id."' 
                and site_id = '".SITE_ID."'
          ");
          orders_updated($order_id);
          last_customer_action();
        }
        tep_order_status_change($order_id,17);
          // insert a history
          $sql = "
            INSERT INTO `".TABLE_ORDERS_STATUS_HISTORY."` (
                `orders_status_history_id`,
                `orders_id` ,
                `orders_status_id` ,
                `date_added` ,
                `customer_notified` ,
                `comments`
              ) VALUES (
                NULL ,
                '".$order_id."', 
                '17', 
                '".date("Y-m-d H:i:s")."', 
                '1', 
                '".mysql_real_escape_string($comment)."'
              )
          ";
          // ccdd
          tep_db_query($sql);
        echo '<div class="comment">'.TEXT_CHANGE_ORDER_CONFIRM_EMAIL.' <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="'.TEXT_BACK_TO_TOP.'"></a></div></div>';
        // sent mail to customer
        // ccdd
        $mail    = tep_db_fetch_array(tep_db_query("
              select * 
              from ".TABLE_ORDERS_MAIL." 
              where orders_status_id=17 
                and (site_id='0' or site_id = '" . SITE_ID . "')
              order by site_id DESC
        "));
        // $mail_title = "注文内容の変更を承りました";
        $mail_title   = $mail['orders_status_title'];
        $mail_content = $mail['orders_status_mail'];

  // load selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment(isset($payment) ? $payment : '');

  # OrderNo
  $insert_id = $oID;
  
  $o = new order($oID);
  $payment_code = payment::changeRomaji($o->info['payment_method'], PAYMENT_RETURN_TYPE_CODE); 

  # Check
  // ccdd
  $NewOidQuery = tep_db_query("
      select count(*) as cnt 
      from ".TABLE_ORDERS." 
      where orders_id = '".$insert_id."' 
        and site_id = '".SITE_ID."'
  ");
  $NewOid = tep_db_fetch_array($NewOidQuery);
  
  # load the selected shipping module(convenience_store)
  /*if ($_SESSION['payment'] == 'convenience_store') {
    $convenience_sid = str_replace('-', "", $insert_id);
  
    $pay_comments = '取引コード' . $convenience_sid ."\n";
  $pay_comments .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $pay_comments .= '住所1:' . $_POST['convenience_store_address1'] ."\n";
  $pay_comments .= '住所2:' . $_POST['convenience_store_address2'] ."\n";
  $pay_comments .= '氏:' . $_POST['convenience_store_l_name'] ."\n";
  $pay_comments .= '名:' . $_POST['convenience_store_f_name'] ."\n";
  $pay_comments .= '電話番号:' . $_POST['convenience_store_tel'] ."\n";
  $pay_comments .= '接続URL:' . tep_href_link('convenience_store_chk.php', 'sid=' . $convenience_sid, 'SSL');
  
  $comments = $pay_comments ."\n".$comments;
  }
  */

// load the before_process function from the payment modules
  $payment_modules->before_process($payment_code);

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

  $order_totals = $order_total_modules->process();
  
  # Random

  
  # Select
  //$cnt = strlen($NewOid);

  // initialized for the email confirmation
  $products_ordered = '';
  $subtotal = 0;
  $total_tax = 0;

  for ($i=0, $n=sizeof($o->products); $i<$n; $i++) {
  //------insert customer choosen option to order--------
    $attributes_exist = '0';
    $products_ordered_attributes = '';
    if (isset($o->products[$i]['attributes'])) {
      for ($j=0, $n2=sizeof($o->products[$i]['attributes']); $j<$n2; $j++) {
        if (DOWNLOAD_ENABLED == 'true') {
          $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename 
                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                on pa.products_attributes_id=pad.products_attributes_id
                               where pa.products_id = '" . $o->products[$i]['id'] . "' 
                                and pa.options_id = '" . $o->products[$i]['attributes'][$j]['option_id'] . "' 
                                and pa.options_id = popt.products_options_id 
                                and pa.options_values_id = '" . $o->products[$i]['attributes'][$j]['value_id'] . "' 
                                and pa.options_values_id = poval.products_options_values_id 
                                and popt.language_id = '" . $languages_id . "' 
                                and poval.language_id = '" . $languages_id . "'";
          //ccdd
          $attributes = tep_db_query($attributes_query);
        } else {
          // ccdd
          $sql = "
            select popt.products_options_name, 
                   poval.products_options_values_name, 
                   pa.options_values_price, 
                   pa.price_prefix, 
                   pa.products_at_quantity, 
                   pa.products_attributes_id 
            from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
            where pa.products_id = '" . $o->products[$i]['id'] . "' 
              and pa.options_id = '" . $o->products[$i]['attributes'][$j]['option_id'] . "' 
              and pa.options_id = popt.products_options_id 
              and pa.options_values_id = '" . $o->products[$i]['attributes'][$j]['value_id'] . "' 
              and pa.options_values_id = poval.products_options_values_id 
              and popt.language_id = '" . $languages_id . "' 
              and poval.language_id = '" . $languages_id . "'
            ";

          $attributes = tep_db_query($sql);
        }
        $attributes_values = tep_db_fetch_array($attributes);
        $products_ordered_attributes .= "\n" . $attributes_values['products_options_name'] 
        . str_repeat('　',intval((18-strlen($attributes_values['products_options_name']))/2))
        . '：' . $attributes_values['products_options_values_name'];
      }
    }
//------insert customer choosen option eof ----
    $attribute_len_array = array();
    $attribute_max_len = 0;

    if (isset($o->products[$i]['op_attributes'])) {
      foreach ($o->products[$i]['op_attributes'] as $opa_l_order) {
        $attribute_len_array[] = mb_strlen($opa_l_order['option_info']['title'], 'utf-8'); 
      }
    }
    
    if (!empty($attribute_len_array)) {
      $attribute_max_len = max($attribute_len_array); 
    }
   
    if ($attribute_max_len < 4) {
      $attribute_max_len = 4; 
    }
    if (isset($o->products[$i]['op_attributes'])) {
      foreach ($o->products[$i]['op_attributes'] as $opa_order) {
        $products_ordered_attributes .= "\n" .$opa_order['option_info']['title'] .  str_repeat('　',intval(($attribute_max_len - mb_strlen($opa_order['option_info']['title'], 'utf-8')))) . '：' .  str_replace("<br>", "\n", $opa_order['option_info']['value']);
      }
    }
    if(isset($o->products[$i]['weight']) && isset($o->products[$i]['qty'])){
      $total_weight += ($o->products[$i]['qty'] * $o->products[$i]['weight']);
    }
    if(isset($o->products[$i]['qty'])) {
      $total_tax += tep_calculate_tax(
        isset($total_products_price)?$total_products_price:0, 
        (isset($products_tax)?$products_tax:0)
        ) * $o->products[$i]['qty'];
    }
    if(isset($total_cost)){
      $total_cost += isset($total_products_price)?$total_products_price:0;
    } else {
      $total_cost = 0;
    }

    $products_ordered .= TEXT_REORDER_ORDER_PRODUCT.str_repeat('　',intval(($attribute_max_len - mb_strlen(TEXT_REORDER_ORDER_PRODUCT, 'utf-8')))).'：' . $o->products[$i]['name'];
    if(tep_not_null($o->products[$i]['model'])) {
      $products_ordered .= ' (' . $o->products[$i]['model'] . ')';
    }
    
    // ccdd
    $product_info = tep_get_product_by_id($o->products[$i]['id'], SITE_ID ,$languages_id);
    
    $products_ordered .= $products_ordered_attributes . "\n";
    $products_ordered .= TEXT_REORDER_QTY_SUM.str_repeat('　',intval(($attribute_max_len - mb_strlen(TEXT_REORDER_QTY_SUM, 'utf-8')))).'：' . $o->products[$i]['qty'] . TEXT_REORDER_QTY . tep_get_full_count2($o->products[$i]['qty'], $o->products[$i]['id']) . "\n";
    if(tep_not_null($o->products[$i]['character'])) {
      $products_ordered .= TEXT_REORDER_CHARACTER . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($o->products[$i]['character']) : $o->products[$i]['character']) . "\n";
    }

    $products_ordered .= '------------------------------------------' . "\n";
  }
  
  # メール本文整形 --------------------------------------
  $email_order = '';

  // ccdd
  $otq = tep_db_query("
      select * 
      from ".TABLE_ORDERS_TOTAL." 
      where class = 'ot_total' 
        and orders_id = '".$insert_id."'
  ");
  $ot = tep_db_fetch_array($otq);
  $_datetime = $o->tori['date'];
  $_datetime = explode(' ',$_datetime);
  $_date = $_datetime[0];
  $_time = explode(':',$_datetime[1]);
  $_hour = $_time[0]; 
  $_minute = $_time[1];

  $email_order .= TEXT_REORDER_LINE . "\n";
  $email_order .= TEXT_REORDER_OID_EMAIL . $insert_id . "\n";
  $email_order .= TEXT_REORDER_TDATE_EMAIL . tep_date_long(time()) . "\n";
  $email_order .= TEXT_REORDER_NAME_EMAIL . $o->customer['name'] . "\n";
  $email_order .= TEXT_REORDER_EMAIL_EMAIL . $o->customer['email_address'] . "\n";
  $email_order .= TEXT_REORDER_LINE . "\n\n";
  $email_order .= TEXT_REORDER_PRODUCT_EMAIL . "\n";
  $email_order .= '------------------------------------------' . "\n";
  $email_order .= $products_ordered . "\n";
  $email_order .= TEXT_REORDER_TRADE_DATE . str_string($_date) . $_hour . TEXT_PRORDER_HOUR . $_minute . TEXT_REORDER_TWENTY_FOUR_HOUR . "\n";

  if ($comment) {
    $email_order .= TEXT_REORDER_COMMERN_EMAIL . "\n";
    $email_order .= $comment . "\n";
  }
  
  $mail_title = "[" . $order['orders_id'] . "]".TEXT_REORDER_TITLE_EMAIL;
  $email_order = str_replace(array('${NAME}', '${TIME}', '${CONTENT}', '${SITE_NAME}', '${SITE_URL}', '${SUPPORT_EMAIL}'), array($o->customer['name'], date('Y-m-d H:i:s'), $email_order, STORE_NAME, HTTP_SERVER, SUPPORT_EMAIL_ADDRESS), $mail_content);

  # メール本文整形 --------------------------------------
  // 2003.03.08 Edit Japanese osCommerce
  tep_mail($o->customer['name'], $o->customer['email_address'], $mail_title, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $mail_title, $email_order, $o->customer['name'], $o->customer['email_address'], '');
  }
      }
    } else if (tep_orders_status_finished($order['orders_status'])) {
        // status can not change
        echo '<div class="comment">'.TEXT_REORDER_COMMERN_STATUS.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
    } else {
        // edit order
?>
<div class="comment">
<div id='form'>
<form action="reorder.php" method="post" name="order">
<input type="hidden" name="dummy" value="<?php echo TEXT_REORDER_DUMMY_WIDTH;?>">
<input type='hidden' name='order_id' value='<?php echo $order['orders_id']?>' >
<input type='hidden' name='email' value='<?php echo $order['customers_email_address']?>' >
<div id="form_error" style="display:none"></div>
<table class="information_table" summary="table">
 <tr>
 <td width="130" bgcolor="#eeeeee"><?php echo TEXT_REORDER_OID_TITLE;?></td>
  <td><?php echo $order['orders_id']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_OID_NAME;?></td>
  <td><?php echo $order['customers_name']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
  <td><?php echo $order['customers_email_address']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_TRADE_NO_CHANGE;?></td>
  <td id='old_time'><?php echo tep_date_long(strtotime($order['torihiki_date']))?> <?php echo date('H:i', strtotime($order['torihiki_date']));?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_TRADE_CHANGE;?></td>
  <td>
   <select name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
<?php for($i=0;$i<7;$i++){?>
    <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo tep_date_long(time()+($i*86400));?></option>
<?php }?>
   </select>
   <select name='hour' id='new_hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
   </select>:
   <select name='minute' id='new_minute'>
    <option value=''>--</option>
   </select>
   <span id="date_error"></span>
   <div><?php echo TEXT_REORDER_TRADE_TEXT;?></div>
  </td>
 </tr>
</table>
<?php foreach($o->products as $key => $value){
  // for multi products
  ?>
<br>
<table class="information_table" id='product_<?php echo $value['id'];?>' summary="table">
 <tr>
 <td width="130" bgcolor="#eeeeee"><?php echo TEXT_REORDER_P_PRODUCT_NAME;?></td>
  <td name='products_names'><?php echo $value['name'];?></td>
 </tr>
<?php if($value['character']) {?>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_P_PRODUCT_CHARACTER;?></td>
  <td><input type='text' id='character_<?php echo $value['id'];?>' name='character[<?php echo $value['id'];?>]' value="<?php echo htmlspecialchars($value['character'])?>" class="input_text" ></td>
 </tr>
<?php }?>
<?php if(isset($value['attributes']) && $value['attributes'])
foreach ($value['attributes'] as $att) {?>
 <tr>
  <td bgcolor="#eeeeee"><?php echo $att['option'].TEXT_REORDER_NO_CHANGE;?></td>
  <td><?php echo $att['value'];?></td>
 </tr>
<?php }?>
 <?php
 // ccdd
        $products_attributes_query = tep_db_query("
            select count(*) as total 
            from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
            where patrib.products_id='" . $value['id'] . "' 
              and patrib.options_id = popt.products_options_id 
              and popt.language_id = '" . $languages_id . "'
        ");
        $products_attributes = tep_db_fetch_array($products_attributes_query);
        if (false) {
          //ccdd
          $products_options_name_query = tep_db_query("
              select distinct popt.products_options_id, 
                              popt.products_options_name 
              from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
              where patrib.products_id='" . $value['id'] . "' 
                and patrib.options_id = popt.products_options_id 
                and popt.language_id = '" . $languages_id . "'
              ");
          while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            echo '<tr><td bgcolor="#eeeeee">' . $products_options_name['products_options_name'] . TEXT_REORDER_CHANGE.'</td><td>' . "\n";
            // ccdd
            $products_options_query = tep_db_query("
                select pov.products_options_values_id, 
                       pov.products_options_values_name, 
                       pa.options_values_price, 
                       pa.price_prefix, 
                       pa.products_at_quantity, 
                       pa.products_at_quantity 
                from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
                where pa.products_id = '" . $value['id'] . "' 
                  and pa.options_id = '" . $products_options_name['products_options_id'] . "' 
                  and pa.options_values_id = pov.products_options_values_id 
                  and pov.language_id = '" . $languages_id . "' 
                order by pa.products_attributes_id
            ");
            while ($products_options = tep_db_fetch_array($products_options_query)) {
              if($products_options['products_at_quantity'] > 0) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                if ($products_options['options_values_price'] != '0') {
                  $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                }
              }
            }
            $products_options_array = array_merge(array(array('id' => '', 'text' => '--')), $products_options_array);
            echo tep_draw_pull_down_menu(
                'id['.$value['id'].'][' . $products_options_name['products_options_id'] . ']', 
                $products_options_array, 
                isset($cart->contents[$value['id']]['attributes'][$products_options_name['products_options_id']])?  $cart->contents[$value['id']]['attributes'][$products_options_name['products_options_id']]:'');
            echo '</td></tr>';
          }
          //echo '</table>';
        }
    ?>
</table>
<?php }?>
<br>
<table class="information_table" summary="table">
<tr>
<td width="130" bgcolor="#eeeeee"><?php echo TEXT_REORDER_COMMENT_TITLE;?></td>
<td><textarea name='comment' id='comment' rows="5"></textarea></td>
</tr>
</table>
<br>
<p align="center">
<input type='image' src="includes/languages/japanese/images/buttons/button_submit.gif" alt="<?php echo TEXT_REORDER_CONFRIM;?>" onClick="return orderConfirmPage();" >
<input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();return false;" >
</p>
</form>
</div>
<div id='confirm' style='display:none; text-align: center;'>
  <div id='confirm_content'></div>
  <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="<?php echo TEXT_REORDER_CONFRIM;?>" onClick="document.order.submit()" >
  <input type='image' src="includes/languages/japanese/images/buttons/button_back.gif" alt="<?php echo TEXT_BACK_TO_HISTORY;?>" onClick="document.getElementById('confirm').style.display='none';document.getElementById('form').style.display='block'" >
</div>
<script type="text/javascript" src='./js/order.js'></script>
<script type="text/javascript">
<!---
function orderConfirmPage(){
  document.getElementById('form_error').style.display = 'none';
  document.getElementById('date_error').style.display = 'none';
  document.getElementById('form_error').innerHTML = '';
  document.getElementById('date_error').innerHTML = '';
  // init
  productName  = new Array();
  oldCharacter = new Array();
  oldAttribute = new Array();
  text         = "";
  orderChanged = false;
  now          = new Date();
  nowMinutes   = now.getHours() * 60 + now.getMinutes();

  oldTime = '<?php echo tep_date_long(strtotime($order['torihiki_date']));?> <?php echo date('H:i', strtotime($order['torihiki_date']));?>';
  oldTime_value = '<?php echo strtotime($order['torihiki_date']);?>';
  today   = '<?php echo tep_date_long(time());?>';
  today_value = '<?php echo time();?>';
  
<?php foreach($o->products as $p){?>
  productName[<?php echo $p['id'];?>] = '<?php echo $p['name'];?>';
  oldCharacter[<?php echo $p['id'];?>] = "<?php echo htmlspecialchars(addslashes($p['character']));?>";
  oldAttribute[<?php echo $p['id'];?>] = new Array();
<?php   if($p['attributes'])foreach($p['attributes'] as $a){
          if($a['option_id'] != ''){
?>
  oldAttribute[<?php echo $p['id'];?>][<?php echo $a['option_id'];?>] = new Array('<?php echo $a['option'];?>', '<?php echo $a['value'];?>');
<?php   
          }          
        }
?>
<?php }?>
  text += "<table class='information_table' summary='table'>\n";
  text += "<tr><td bgcolor='#eeeeee' width='130'>\n";
  text += "<?php echo TEXT_REORDER_TRADE_NO_CHANGE;?>";
  text += "</td><td>\n";
  text += oldTime + "\n";
  text += "</td></tr><tr><td bgcolor='#eeeeee'>\n";
  
  dateChanged = (document.getElementById('new_date').options[document.getElementById('new_date').selectedIndex].value != ''
    && document.getElementById('new_hour').options[document.getElementById('new_hour').selectedIndex].value != ''
    && document.getElementById('new_minute').options[document.getElementById('new_minute').selectedIndex].value != '');
  
  orderChanged = orderChanged || dateChanged;

  text += "<?php echo TEXT_REORDER_TRADE_CHANGE;?></td><td>";
  
  if((document.getElementById('new_date').selectedIndex != 0 || document.getElementById('new_hour').selectedIndex != 0 || document.getElementById('new_minute').selectedIndex != 0) && !(document.getElementById('new_date').selectedIndex != 0 && document.getElementById('new_hour').selectedIndex != 0 && document.getElementById('new_minute').selectedIndex != 0)){
    document.getElementById('date_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_CHANGE_TRADE_SELECT;?></font>";
      document.getElementById('date_error').style.display = 'block';
      return false;
  }

  if(dateChanged){
    newTime = document.getElementById('new_date').options[document.getElementById('new_date').selectedIndex].innerHTML + " "
     + document.getElementById('new_hour').options[document.getElementById('new_hour').selectedIndex].innerHTML + ":"
     + document.getElementById('new_minute').options[document.getElementById('new_minute').selectedIndex].innerHTML 
     //+ ":00"

    if(document.getElementById('new_date').selectedIndex == 1 
      && ((document.getElementById('new_hour').options[document.getElementById('new_hour').selectedIndex].value * 60) + parseInt(document.getElementById('new_minute').options[document.getElementById('new_minute').selectedIndex].value)) < (nowMinutes + <?php echo MINUTES;?>)) 
    {
      // time error
      document.getElementById('date_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_JS_DATE_ERROR;?></font>";
      document.getElementById('date_error').style.display = 'block';
      return false;
    }
    text += newTime + "</td></tr></table><br >\n";
  } else {
    text += oldTime + "</td></tr></table><br >\n";
  }
  
  for(i in productName){
    text += "<table class='information_table' summary='table'>\n";
    text += "<tr><td width='130' bgcolor='#eeeeee'><?php echo TEXT_REORDER_P_PRODUCT_NAME;?></td><td>\n";
    text += productName[i] + "\n";
    text += "</td></tr>";

    if(oldCharacter[i] != ''){
      text += "<tr><td bgcolor='#eeeeee' width='130'>\n";
      text += "<?php echo TEXT_REORDER_BEFORE_CHANGE;?>";
      text += "</td><td>\n";
      text += oldCharacter[i] + "\n";
      text += "</td></tr>";
      text += "<tr><td bgcolor='#eeeeee'>\n";
      text += "<?php echo TEXT_REORDER_AFTER_CHANGE;?>";
      text += "</td><td>\n";
      if(document.getElementById('character_'+i)){
      text += document.getElementById('character_'+i).value.replace(/\</ig,"&lt;").replace(/\>/ig,"&gt;") + "\n";
      text += "</td></tr>";
      orderChanged = orderChanged || (oldCharacter[i] != document.getElementById('character_'+i).value);
      }
    }

    
    

    for(j in oldAttribute[i]){
      text += "<tr><td bgcolor='#eeeeee'>\n";
      text += oldAttribute[i][j][0] + "<?php echo TEXT_REORDER_NO_CHANGE;?>\n"
      text += "</td><td>\n";
      text += oldAttribute[i][j][1] + "\n";
      text += "</td></tr><tr><td bgcolor='#eeeeee'>\n";
      text += oldAttribute[i][j][0];
      text += "<?php echo TEXT_REORDER_CHANGE;?></td><td>\n";
      if(document.getElementById('id[' + i + '][' + j + ']')){
      if (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0) {
        text += document.getElementById('id[' + i + '][' + j + ']').options[document.getElementById('id[' + i + '][' + j + ']').selectedIndex].innerHTML + "\n";
      } else {
        text += oldAttribute[i][j][1] + "\n";
      }
      text += "</td></tr>\n";
      orderChanged = orderChanged || (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0);
      }
    }
    text += "</table><br >\n";
  }

  text += "<table class='information_table' summary='table'>\n"
  text += "<tr><td bgcolor='#eeeeee' width='130'>";
  text += "<?php echo TEXT_REORDER_COMMENT_TITLE;?>";
  text += "</td><td>\n";
  text += document.getElementById('comment').value.replace(/\</ig,"&lt;").replace(/\>/ig,"&gt;");
  text += "</td></tr>\n";
  text += "</table><br >\n"
  
  orderChanged = (orderChanged || document.getElementById('comment').value);
  

  var time_error = false;
  var new_date = document.getElementById("new_date");
  if(new_date.value == ''){
     if(oldTime_value <= today_value){
       time_error = true; 
     }
  } 
  // if order unchanged , does not commit
  if(!orderChanged){
    //alert('no change');
    document.getElementById('form_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_UNCHANGE_QTY;?></font>";
    document.getElementById('form_error').style.display = 'block';
  }

  if(time_error){
    document.getElementById('date_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_CHANGE_TRADE_SELECT;?></font>";
    document.getElementById('date_error').style.display = 'block';
  }
  if(!orderChanged || time_error){
    return false; 
  }
  document.getElementById('form').style.display = 'none';
  document.getElementById('confirm').style.display = 'block';
  document.getElementById('confirm_content').innerHTML = text;
  return false;
}
-->
</script>
<?php
    }
  } else {
    // has no order or info error
    echo '<div class="comment">'.TEXT_REORDER_NO_ORDER_ERROR.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt="前に戻る"></a></div></div>';
  }
?>
<?php } else {
  // enter basic order info
  ?>
<div class="comment">
<form action="reorder.php" method="post" name='order'>
<input type="hidden" name="dummy" value="<?php echo TEXT_REORDER_DUMMY_WIDTH;?>">   
<table class="information_table" summary="table">
 <tr>
 <td align="left" bgcolor='#eeeeee'><?php echo TEXT_REORDER_OID_TITLE;?></td>
  <td><input type='text' name='order_id_1' class="input_text" maxlength='8' style='width:80px' >-<input type='text' name='order_id_2' class="input_text" maxlength='8' style='width:80px' >
  <a href="/reorder2.php"><?php echo TEXT_REORDER_OID_FORGET;?></a><br >
  <font color='red' style='font-size:12px'><?php echo TEXT_REORDER_OID_TEXT_INFO;?></font>
  </td>
 </tr>
 <tr>
 <td align="left" bgcolor='#eeeeee'><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
  <td><input type='text' name='email' class="input_text" ></td>
 </tr>
 <tr>
  <td colspan='2' align="center">
   
  <input type='image' src="includes/languages/japanese/images/buttons/button_continue.gif" alt="<?php echo TEXT_REORDER_NEXT;?>">
  <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();return false;" >
  </td>
 </tr>
</table>
</form>
<?php }?>
    </div>
        <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof -->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation -->
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
