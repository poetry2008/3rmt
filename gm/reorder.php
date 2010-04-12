<?php
require('includes/application_top.php');

define('HEADING_TITLE', '再配達依頼');
define('MINUTES', 20);

$breadcrumb->add('再配達フォーム', tep_href_link('reorder.php'));
?>
<?php page_head();?>
</head>
<body>
<div id="main">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="l_menu">
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- body_text //-->
<!--left-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; ');?></div>
  <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
  <?php if ($_POST) {
  include('../admin/includes/classes/order.php');

  if(isset($_POST['order_id'])){
  	$oID    = tep_db_prepare_input($_POST['order_id']);
  } else {
  	$oID    = tep_db_prepare_input($_POST['order_id_1']).'-'.tep_db_prepare_input($_POST['order_id_2']);
  }
  
  $cEmail = tep_db_prepare_input($_POST['email']);
  
  $o      = new order($oID);
  $order  = tep_db_fetch_array(tep_db_query("select * from `".TABLE_ORDERS."` where `orders_id` = '".$oID."' and `customers_email_address` = '".$cEmail."' and site_id = '".SITE_ID."'"));


  if ($order) {
    if (isset($_POST['hour'])){
      $date   = tep_db_prepare_input($_POST['date']);
      $hour   = tep_db_prepare_input($_POST['hour']);
      $minute = tep_db_prepare_input($_POST['minute']);
      
      $comment = tep_db_prepare_input($_POST['comment']);

      $datetime = $date.' '.$hour.':'.$minute;
      $time     = strtotime($datetime);

      if (in_array($order['orders_status'], array(2,5,6,7,8))) {
        // status can not change
        echo '<div class="comment">ご入力いただきました登録情報は、既に発送が完了している、または、ご注文がキャンセルとなっております。 <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="前に戻る" title="前に戻る"></a></div></div>';
      } else if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
        // time error
        echo '<div class="comment">取引時間は前もって一時間以上に設定してください <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="TOPに戻る" title="TOPに戻る"></a></div></div><div>';
      } else {
        // update time
        if ($date && $hour && $minute) {
          tep_db_query("update `".TABLE_ORDERS."` set `orders_status`='17' ,`torihiki_date` = '".$datetime."' WHERE `orders_id`='".$order_id."' and site_id = '".SITE_ID."'");
          // insert a history
          $sql = "INSERT INTO `".TABLE_ORDERS_STATUS_HISTORY."` (`orders_status_history_id` ,`orders_id` ,`orders_status_id` ,`date_added` ,`customer_notified` ,`comments`) VALUES (NULL , '".$order_id."', '17', '".date("Y-m-d H:i:s")."', '1', '".mysql_real_escape_string($comment)."');";
          tep_db_query($sql);
        }

        // update character
        if (isset($_POST['character']) && is_array($_POST['character'])){
          foreach(tep_db_prepare_input($_POST['character']) as $pid=>$character){
            tep_db_query("update `".TABLE_ORDERS_PRODUCTS."` set `products_character`='".$character."' where `orders_id`='".$oID."' and `products_id`='".$pid."'");
          }
        }
        // update attributes
        if($o->products){
          foreach($o->products as $p){
            if($p['attributes']){
              foreach($p['attributes'] as $a) {
                if(isset($_POST['id'][$p['id']])) {
                  // old attribute
                  $attributes = tep_db_fetch_array(tep_db_query("select * from `".TABLE_PRODUCTS_ATTRIBUTES."` where `products_attributes_id`='".$a['attributes_id']."'"));
                  if(isset($_POST['id'][(int)$p['id']][(int)$attributes['options_id']]) && $_POST['id'][(int)$p['id']][(int)$attributes['options_id']]){
                    // new option
                    $option = tep_db_fetch_array(tep_db_query("select * from `".TABLE_PRODUCTS_OPTIONS."` where `products_options_id`='".$attributes['options_id']."'"));
                    // new attribute
                    $nattribute = tep_db_fetch_array(tep_db_query("select * from `".TABLE_PRODUCTS_ATTRIBUTES."` where `products_id`='".$p['id']."' and `options_id`='".$attributes['options_id']."' and `options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'"));
                    // new option value
                    $value = tep_db_fetch_array(tep_db_query("select * from `".TABLE_PRODUCTS_OPTIONS_VALUES."` where `products_options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'"));
                    // execute update`
                    $update_query = tep_db_query("update `".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."` set `products_options_values`='".$value['products_options_values_name']."',`attributes_id`='".$nattribute['products_attributes_id']."' where `orders_id`='".$oID."' and `products_options`='".$option['products_options_name']."' and `attributes_id`='".$a['attributes_id']."'");
                  }
                }
              }
            }
          }
        }
        echo '<div class="comment">注文内容の変更を承りました。電子メールをご確認ください。 <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="TOPに戻る" title="TOPに戻る"></a></div></div><div>';
        // sent mail to customer
        $mail    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_MAIL." where orders_status_id=17"));
        // $mail_title = "注文内容の変更を承りました";
        $mail_title   = $mail['orders_status_title'];
        $mail_content = $mail['orders_status_mail'];

  // load selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment($payment);

  # OrderNo
  $insert_id = $oID;
  
  $o = new order($oID);

  # Check
  $NewOidQuery = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'");
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
  $payment_modules->before_process();

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

  $order_totals = $order_total_modules->process();
  
  # Random

  
  # Select
  $cnt = strlen($NewOid);

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
          $attributes = tep_db_query($attributes_query);
        } else {
          $sql = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
        	where pa.products_id = '" . $o->products[$i]['id'] . "' 
        	and pa.options_id = '" . $o->products[$i]['attributes'][$j]['option_id'] . "' 
        	and pa.options_id = popt.products_options_id 
        	and pa.options_values_id = '" . $o->products[$i]['attributes'][$j]['value_id'] . "' 
        	and pa.options_values_id = poval.products_options_values_id 
        	and popt.language_id = '" . $languages_id . "' 
        	and poval.language_id = '" . $languages_id . "'";

          $attributes = tep_db_query($sql);
        }
        $attributes_values = tep_db_fetch_array($attributes);
        $products_ordered_attributes .= "\n" . $attributes_values['products_options_name'] 
        . str_repeat('　',intval((18-strlen($attributes_values['products_options_name']))/2))
        . '：' . $attributes_values['products_options_values_name'];
      }
    }
//------insert customer choosen option eof ----
    $total_weight += ($o->products[$i]['qty'] * $o->products[$i]['weight']);
    $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $o->products[$i]['qty'];
    $total_cost += $total_products_price;

    $products_ordered .= '注文商品　　　　　：' . $o->products[$i]['name'];
	if(tep_not_null($o->products[$i]['model'])) {
	  $products_ordered .= ' (' . $o->products[$i]['model'] . ')';
	}
	
    $_product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_attention_1,pd.products_attention_2,pd.products_attention_3,pd.products_attention_4,pd.products_attention_5,pd.products_description, p.products_model, p.products_quantity, p.products_image,p.products_image2,p.products_image3, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.products_bflag, p.products_cflag, p.products_small_sum from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . $o->products[$i]['id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' and pd.site_id = '".SITE_ID."'");
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . $languages_id . "' and site_id = '".SITE_ID."'");
    $product_info = tep_db_fetch_array($_product_info_query);
    $data1 = explode("//", $product_info['products_attention_1']);
	
	$products_ordered .= $products_ordered_attributes . "\n";
	//$products_ordered .= '個数          :' . $o->products[$i]['qty'] . "\n";
	$products_ordered .= '個数　　　　　　　：' . $o->products[$i]['qty'] . '個' . tep_get_full_count($o->products[$i]['qty'], $data1[1]) . "\n";
	//$products_ordered .= '単価          :' . $currencies->display_price($o->products[$i]['final_price'], $o->products[$i]['tax']) . "\n";
	//$products_ordered .= '小計          :' . $currencies->display_price($o->products[$i]['final_price'], $o->products[$i]['tax'], $o->products[$i]['qty']) . "\n";
	if(tep_not_null($o->products[$i]['character'])) {
	  $products_ordered .= 'キャラクター名　　：' . $o->products[$i]['character'] . "\n";
	}
	
	/*$add_products_query = tep_db_query("select products_description from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$o->products[$i]['id']."' and language_id = '" . $languages_id . "'");
	if(tep_db_num_rows($add_products_query)) {
	  $add_products = tep_db_fetch_array($add_products_query);
	  $description = explode("|-#-|", $add_products['products_description']);
	  if(tep_not_null($description[6])) {
	    $products_ordered .= str_replace("\n", "\n", strip_tags($description[6])) . "\n"; 
	  }
	}*/

	$products_ordered .= '------------------------------------------' . "\n";
  }
  
  # メール本文整形 --------------------------------------
  $email_order = '';

  $otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
  $ot = tep_db_fetch_array($otq);
  //$otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".$insert_id."'");
  //$op = tep_db_fetch_array($otq);

  //$email_order .= $o->customer['name'] . '様' . "\n\n";
  //$email_order .= 'この度は、' . STORE_NAME . 'をご利用いただき、誠にあり' . "\n";
  //$email_order .= 'がとうございます。' . "\n";
  //$email_order .= '下記の内容にてご注文を承りましたので、ご確認ください。' . "\n";
  //$email_order .= 'ご不明な点がございましたら、注文番号をご確認の上、' . "\n";
  //$email_order .= '「' . STORE_NAME . '」までお問い合わせください。' . "\n\n";

  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_order .= '▼注文番号　　　　：' . $insert_id . "\n";
  $email_order .= '▼注文日　　　　　：' . strftime(DATE_FORMAT_LONG) . "\n";
  $email_order .= '▼お名前　　　　　：' . $o->customer['name'] . "\n";
  $email_order .= '▼メールアドレス　：' . $o->customer['email_address'] . "\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n\n";
/*
  if ($op['value'] > 0) {
    $email_order .= '▼ポイント割引　　：' . $op['text'] . "\n";
  }
  $email_order .= '▼お支払金額　　　：' . strip_tags($ot['text']) . "\n";
  $email_order .= '▼お支払方法　　　：' . $o->info['payment_method'] . "\n\n";
*/
  $email_order .= '▼注文商品' . "\n";
  $email_order .= '------------------------------------------' . "\n";
  $email_order .= $products_ordered . "\n";

  //$email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_order .= '▼取引日時　　　　：' . str_string($date) . $hour . '時' . $minute . '分　（24時間表記）' . "\n";
  //$email_order .= '　　　　　　　　　：' . $torihikihouhou . "\n";
  

  if ($comment) {
    $email_order .= '▼備考　　　　　　：' . "\n";
    //$email_order .= tep_db_output($comment) . "\n";
    $email_order .= htmlspecialchars($comment) . "\n";
  }
  
  
  //$email_order .= "\n\n\n";
  //$email_order .= '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n";
  //$email_order .= '株式会社 iimy' . "\n";
  //$email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
  //$email_order .= HTTP_SERVER . "\n";
  //$email_order .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";

  $mail_title = "[" . $order['orders_id'] . "]再配達確認メール【" . STORE_NAME . "】";
  $email_order = str_replace(array('${NAME}', '${TIME}', '${CONTENT}'), array($o->customer['name'], date('Y-m-d H:i:s'), $email_order), $mail_content);

  # メール本文整形 --------------------------------------
  // 2003.03.08 Edit Japanese osCommerce
  tep_mail($o->customer['name'], $o->customer['email_address'], $mail_title, nl2br($email_order), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  //echo nl2br($email_order);
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $mail_title, nl2br($email_order), $o->customer['name'], $o->customer['email_address'], '');
  }
      }
    } else if (in_array($order['orders_status'], array(2,5,6,7,8))) {
        // status can not change
        echo '<div class="comment">ご指定の注文番号は受付できません。 <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="前に戻る" title="前に戻る"></a></div></div><div>';
    } else {
        // edit order
?>
  <div class="comment">
    <div id='form'>
      <form action="reorder.php" method="post" name="order">
        <input type="hidden" name="dummy" value="あいうえお眉幅">
        <input type='hidden' name='order_id' value='<?php echo $order['orders_id']?>' >
        <input type='hidden' name='email' value='<?php echo $order['customers_email_address']?>' >
        <div id="form_error" style="display:none">
        </div>
        <table class="information_table" summary="table">
          <tr>
            <td width="130">注文番号</td>
            <td><?php echo $order['orders_id']?></td>
          </tr>
          <tr>
            <td>お名前</td>
            <td><?php echo $order['customers_name']?></td>
          </tr>
          <tr>
            <td>メールアドレス</td>
            <td><?php echo $order['customers_email_address']?></td>
          </tr>
          <tr>
            <td>取引日時（変更前）</td>
            <td id='old_time'><?php echo strftime(DATE_FORMAT_LONG, strtotime($order['torihiki_date']))?>
              <?php echo date('H:i', strtotime($order['torihiki_date']));?></td>
          </tr>
          <tr>
            <td>取引日時（変更後）</td>
            <td>
              <select name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
                <?php for($i=0;$i<7;$i++){?>
                <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo strftime(DATE_FORMAT_LONG, time()+($i*86400));?></option>
                <?php }?>
              </select>
              <select name='hour' id='new_hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
              </select>
              :
              <select name='minute' id='new_minute'>
                <option value=''>--</option>
              </select>
              <span id="date_error"></span>
              <br >
              <font color="red">ご希望のお時間に添えない場合は、弊社より「取引時間」をご連絡させていただきます。</font>
            </td>
          </tr>
        </table>
        <?php foreach($o->products as $key => $value){
  // for multi products
  ?>
        <br>
        <table class="information_table" id='product_<?php echo $value['id'];?>' summary="table">
          <tr>
            <td width="130">商品名</td>
            <td name='products_names'><?php echo $value['name'];?></td>
          </tr>
          <?php if($value['character']) {?>
          <tr>
            <td>キャラクター名</td>
            <td>
              <input type='text' id='character_<?php echo $value['id'];?>' name='character[<?php echo $value['id'];?>]' value='<?php echo $value['character']?>' class="input_text" >
            </td>
          </tr>
          <?php }?>
          <?php if($value['attributes'])foreach ($value['attributes'] as $att) {?>
          <tr>
            <td><?php echo $att['option'];?>(変更前)</td>
            <td><?php echo $att['value'];?></td>
          </tr>
          <?php }?>
          <?php
 // 補竃斌瞳奉來和性崇
        $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $value['id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
        $products_attributes = tep_db_fetch_array($products_attributes_query);
        if ($products_attributes['total'] > 0) {
              //echo '<table border="0" cellpadding="2" cellspacing="0">';
          $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . $value['id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
          while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            echo '<tr><td>' . $products_options_name['products_options_name'] . '(変更後)</td><td>' . "\n";
            $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_at_quantity from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . $value['id'] . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "' order by pa.products_attributes_id");
            while ($products_options = tep_db_fetch_array($products_options_query)) {
              //add products_at_quantity - ds-style
              if($products_options['products_at_quantity'] > 0) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                if ($products_options['options_values_price'] != '0') {
                  $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                }
              }
            }
            $products_options_array = array_merge(array(array('id' => '', 'text' => '--')), $products_options_array);
            echo tep_draw_pull_down_menu('id['.$value['id'].'][' . $products_options_name['products_options_id'] . ']', $products_options_array, $cart->contents[$value['id']]['attributes'][$products_options_name['products_options_id']]);
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
            <td width="130">備考</td>
            <td>
              <textarea name='comment' id='comment'></textarea>
            </td>
          </tr>
        </table>
        <br>
        <p align="center">
          <input type='image' src="includes/languages/japanese/images/buttons/button_submit.gif" alt="確認する" title="確認する" onClick="return orderConfirmPage();" >
          <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="クリア" title="クリア" onClick="javascript:document.order.reset();return false;" >
        </p>
      </form>
    </div>
    <div id='confirm' style='display:none; text-align:center;'>
      <div id='confirm_content' style='text-align:left;'>
      </div>
      <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="確定する" title="確定する" onClick="document.order.submit()" >
      <input type='image' src="includes/languages/japanese/images/buttons/button_back.gif" alt="前に戻る" title="前に戻る" onClick="document.getElementById('confirm').style.display='none';document.getElementById('form').style.display='block'" >
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

	oldTime = '<?php echo strftime(DATE_FORMAT_LONG, strtotime($order['torihiki_date']));?> <?php echo date('H:i', strtotime($order['torihiki_date']));?>';
	today   = '<?php echo strftime(DATE_FORMAT_LONG, time());?>';
	
<?php foreach($o->products as $p){?>
	productName[<?php echo $p['id'];?>] = '<?php echo $p['name'];?>';
	oldCharacter[<?php echo $p['id'];?>] = '<?php echo $p['character'];?>';
	oldAttribute[<?php echo $p['id'];?>] = new Array();
<?php   if($p['attributes'])foreach($p['attributes'] as $a){?>
	oldAttribute[<?php echo $p['id'];?>][<?php echo $a['option_id'];?>] = new Array('<?php echo $a['option'];?>', '<?php echo $a['value'];?>');
<?php   }?>
<?php }?>
	text += "<table class='information_table' summary='table'>\n";
	text += "<tr><td width='130'>\n";
	text += "取引日時（変更前）";
	text += "</td><td>\n";
	text += oldTime + "\n";
	text += "</td></tr><tr><td>\n";
	
	dateChanged = (document.getElementById('new_date').options[document.getElementById('new_date').selectedIndex].value != ''
		&& document.getElementById('new_hour').options[document.getElementById('new_hour').selectedIndex].value != ''
		&& document.getElementById('new_minute').options[document.getElementById('new_minute').selectedIndex].value != '');
	
	orderChanged = orderChanged || dateChanged;

	text += "取引日時（変更後）</td><td>";
	
	if((document.getElementById('new_date').selectedIndex != 0 || document.getElementById('new_hour').selectedIndex != 0 || document.getElementById('new_minute').selectedIndex != 0) && !(document.getElementById('new_date').selectedIndex != 0 && document.getElementById('new_hour').selectedIndex != 0 && document.getElementById('new_minute').selectedIndex != 0)){
			document.getElementById('date_error').innerHTML = "<br> <font color='red'>【取引日時（変更後）】を選択してください。</font>";
			document.getElementById('date_error').style.display = 'inline';
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
			document.getElementById('date_error').innerHTML = "<br><font color='red'>取引時間は現在時刻より20分後以降を選択してください。</font>";
			document.getElementById('date_error').style.display = 'inline';
			return false;
		}
		text += newTime + "</td></tr></table><br >\n";
	} else {
		text += oldTime + "</td></tr></table><br >\n";
	}
	
	for(i in productName){
		text += "<table class='information_table' summary='table'>\n";
		text += "<tr><td width='130'>商品名</td><td>\n";
		text += productName[i] + "\n";
		text += "</td></tr>";

		if(oldCharacter[i] != ''){
			text += "<tr><td width='130'>\n";
			text += "キャラクター名(変更前)";
			text += "</td><td>\n";
			text += oldCharacter[i] + "\n";
			text += "</td></tr>";
			text += "<tr><td>\n";
			text += "キャラクター名(変更後)";
			text += "</td><td>\n";
			text += document.getElementById('character_'+i).value + "\n";
			text += "</td></tr>";
			orderChanged = orderChanged || (oldCharacter[i] != document.getElementById('character_'+i).value);
		}

		
		

		for(j in oldAttribute[i]){
			text += "<tr><td>\n";
			text += oldAttribute[i][j][0] + "(変更前)\n"
			text += "</td><td>\n";
			text += oldAttribute[i][j][1] + "\n";
			text += "</td></tr><tr><td>\n";
			text += oldAttribute[i][j][0];
			text += "(変更後)</td><td>\n";
			if (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0) {
				text += document.getElementById('id[' + i + '][' + j + ']').options[document.getElementById('id[' + i + '][' + j + ']').selectedIndex].innerHTML + "\n";
			} else {
				text += oldAttribute[i][j][1] + "\n";
			}
			text += "</td></tr>\n";
			orderChanged = orderChanged || (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0);
		}
		text += "</table><br >\n";
	}

	text += "<table class='information_table' summary='table'>\n"
	text += "<tr><td width='130' align='left' style='wdith:130px; text-align:left;'>";
	text += "備考";
	text += "</td><td align='left' style='wdith:130px; text-align:left;'>\n";
	text += document.getElementById('comment').value;
	text += "</td></tr>\n";
	text += "</table><br >\n"
	
	orderChanged = (orderChanged || document.getElementById('comment').value);
	
	// if order unchanged , does not commit
	if(!orderChanged){
		//alert('no change');
		document.getElementById('form_error').innerHTML = "<font color='red'>変更箇所がございません。</font>";
		document.getElementById('form_error').style.display = 'block';
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
    echo '<div class="comment">"注文番号" または"メールアドレス" が一致しませんでした。<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="前に戻る" title="前に戻る"></a></div></div><div>';
  }
?>
    <?php } else {
  // enter basic order info
  ?>
    <div class="comment">
      <form action="reorder.php" method="post" name='order'>
        <input type="hidden" name="dummy" value="あいうえお眉幅">
        <table class="information_table" summary="table">
          <tr>
            <td align="left">注文番号</td>
          </tr>
          <tr>
            <td>
              <input type='text' name='order_id_1' class="input_text" maxlength='8' style='width:80px' >
              -
              <input type='text' name='order_id_2' class="input_text" maxlength='8' style='width:80px' >
              <a href="/reorder2.php">注文番号忘れた?</a>
              <br >
              <font color='red' style='font-size:12px'>例：20******-********<br >
              注文書に記載された20から始まる8桁の数字-8桁の数字をご入力ください。</font>
            </td>
          </tr>
          <tr>
            <td align="left">メールアドレス</td>
          </tr>
          <tr>
            <td>
              <input type='text' name='email' class="input_text" >
            </td>
          </tr>
          <tr>
            <td colspan='2' align="center" style="text-align:center;">
              <input type='image' src="includes/languages/japanese/images/buttons/button_continue.gif" alt="次へ進む" title="次へ進む" >
              <input type='image' src="includes/languages/japanese/images/buttons/button_reset_01.gif" alt="クリア" title="クリア" onClick="javascript:document.order.reset();return false;" >
            </td>
          </tr>
        </table>
      </form>
      <?php }?>
    </div>
    <p class="pageBottom"></p>
  </div>
  <!--right-->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
  </div>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
