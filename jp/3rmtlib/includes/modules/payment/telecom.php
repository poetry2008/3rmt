<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
class telecom  extends basePayment  implements paymentInterface  { 
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer, $show_payment_info;

  // class constructor
  function loadSpecialSettings($site_id=0){
    $this->site_id = $site_id;
    $this->code        = 'telecom';    
    $this->form_action_url = MODULE_PAYMENT_TELECOM_CONNECTION_URL;
    $this->show_payment_info = 1;
  }
  function fields($theData=false, $back=false){
    if (!$back) { 
    global $order;
    $total_cost = $order->info['total'];
    $code_fee = $this->calc_fee($total_cost); 
    //if($code_fee<=0){
    //$added_hidden = tep_draw_hidden_field('code_fee', $code_fee);
    //}else{
    $added_hidden = tep_draw_hidden_field('code_fee', $code_fee);
    //}
    return array(
		 array(
		       "code"=>'',
		       "title"=>'',
		       "field"=>$added_hidden,
		       "rule"=>'',
		       "message"=>"",
		       ));
    }
  }
  function javascript_validation() {
    return false;
  }

  function pre_confirmation_check() {
    return true;
  }

  function confirmation() {
    global $currencies;
    global $_POST;
      
    $s_result = !$_POST['telecom_order_fee_error'];
     
    if (!empty($_SESSION['h_code_fee'])) {
      //$s_message = $s_result ? (MODULE_PAYMENT_TELECOM_TEXT_FEE . '&nbsp;' .  $currencies->format($_POST['telecom_order_fee'])):('<font color="#FF0000">'.$_POST['telecom_order_fee_error'].'</font>'); 
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['telecom_order_fee_error'].'</font>'); 
    } else {
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['telecom_order_fee_error'].'</font>'); 
    }
    return array(
		 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
		 'fields' => array(
				   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
				   array('title' => $s_message, 'field' => '')  
				   )           
		 );      

  }

    
  function process_button() { 
    global $order, $currencies, $currency;   
    global $point,$cart,$languages_id;

    // 追加 - 2007.01.05 ----------------------------------------------
    $total = $order->info['total'];
    $f_result = $this->calc_fee($total); 
    
    
    //Add point
    if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
        && (0 < intval($point))) {
      $total -= intval($point);
    }   
    
    $total += intval($this->n_fee); 
    // 追加 - 2007.01.05 ----------------------------------------------
    if (isset($_SESSION['campaign_fee'])) {
      $total += $_SESSION['campaign_fee']; 
    }

    if(isset($_SESSION['h_shipping_fee'])){
      $total += $_SESSION['h_shipping_fee']; 
    }
    
    #mail送信
      $mail_body = '仮クレジットカード注文です。'."\n\n";
    
    # ユーザー情報----------------------------
      $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    /*
      $mail_body .= '▼注文番号　　　　：2007****-********'."\n";
    */
    $mail_body .= '▼注文日　　　　　：' . tep_date_long(time())."\n";
    $mail_body .= '▼お名前　　　　　：' . $order->customer["lastname"] . ' ' . $order->customer["firstname"]."\n";
    $mail_body .= '▼メールアドレス　：' . $order->customer["email_address"]."\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼お支払金額　　　：' . $currencies->format($total) . "\n";
    $mail_body .= '▼お支払方法　　　：クレジットカード決済'."\n";
    
    # 商品内容----------------------------
      $mail_body .= '▼注文商品'."\n";
    $mail_body .= "\t" . '------------------------------------------'."\n";

    $products = $cart->get_products();
    
    
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $char_id = $products[$i]['id'];
      $mail_body .= '・' . $products[$i]['name'] . '×' . $products[$i]['quantity'] . "\n";
      $attributes_exist = ((isset($products[$i]['op_attributes'])) ? 1 : 0);

      if ($attributes_exist == 1) {
        foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
          $op_key_array = explode('_', $op_key);
          $option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
          $option_res = tep_db_fetch_array($option_query);
          if ($option_res) {
            $mail_body .= '└' . $option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $op_value) . "\n";
          }
        }
      }
    
      if (isset($products[$i]['ck_attributes'])) {
         foreach ($products[$i]['ck_attributes'] as $c_op_key => $c_op_value) {
           $c_op_array = explode('_', $c_op_key);
           $c_option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$c_op_array[0]."' and id = '".$c_op_array[2]."'"); 
           $c_option_res = tep_db_fetch_array($c_option_query);
           if ($c_option_res) {
             $mail_body .= '└' . $c_option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $c_op_value) . "\n";
           }
         }
       }
    }

    /*    
          foreach($order->products as $key => $val){
          $char_id = $val["id"];
          $mail_body .= "\t" . $val["name"] . '×' . $val["qty"] . '個（キャラクター名：' . $_SESSION["character"][$char_id] . '）' . "\n";
          $mail_body .= "\t" . 'オプション：不明・・・' . "\n";
          }
    */    
    $mail_body .= "\t" . '------------------------------------------'."\n";
    
    # お届け日時----------------------------
      $mail_body .= '▼お届け日時　　　　：' . $_SESSION["insert_torihiki_date"] . "\n";
    $mail_body .= '　　　　　　　　　：' . $_SESSION["torihikihouhou"] . "\n";
    
    # ユーザーエージェントなど----------------------------
      $mail_body .= "\n\n";
    $mail_body .= '■IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
    $mail_body .= '■ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
    $mail_body .= '■ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
    
    tep_mail('管理者', SENTMAIL_ADDRESS, '仮クレカ注文', $mail_body, '', '');
    
    $today = date("YmdHis");
    // telecom_option 文档中的$ID
    if (!isset($_SESSION['option'])) {
      $_SESSION['option'] = date('Ymd-His'). ds_makeRandStr(2);
    }
    $process_button_string = tep_draw_hidden_field('option', $_SESSION['option']) .
      tep_draw_hidden_field('clientip', MODULE_PAYMENT_TELECOM_KID) .
      tep_draw_hidden_field('money', $total) .
      tep_draw_hidden_field('redirect_url', tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL')) .
      tep_draw_hidden_field('redirect_back_url', tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL'));
    
    //tep_draw_hidden_field('redirect_url', HTTPS_SERVER . tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL')) .
    //tep_draw_hidden_field('redirect_back_url', HTTPS_SERVER . tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL'));
   
    $process_button_string .= tep_draw_hidden_field('telecom_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('code_fee', $_SESSION['h_code_fee']);
    return $process_button_string;
  }
  
  

  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['telecom_order_message']);
      
    return false;
  }

  function after_process() {
    return false;
  }

  function output_error() {
    return false;
  }

  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_TELECOM_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('TELECOM 支払いを有効にする', 'MODULE_PAYMENT_TELECOM_STATUS', 'True', 'TELECOM での支払いを受け付けますか?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_TELECOM_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('接続先URL', 'MODULE_PAYMENT_TELECOM_CONNECTION_URL', '', 'テレコムクレジット申込受付画面URLの設定をします。', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('番組コード', 'MODULE_PAYMENT_TELECOM_KID', '', '番組コードの設定をします。', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('戻り先URL(正常時)', 'MODULE_PAYMENT_OK_URL', 'checkout_process.php', '戻り先URL(正常時)の設定をします。', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('戻り先URL(キャンセル時)', 'MODULE_PAYMENT_NO_URL', 'checkout_payment.php', '戻り先URL(キャンセル時)の設定をします。', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_TELECOM_COST', '99999999999:*0', '決済手数料
例:
代金300円以下、30円手数料をとる場合　300:*0+30,
代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02,
代金1000円以上の場合、手数料を無料する場合　99999999:*0,
無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。
300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_TELECOM_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '3', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_TELECOM_PREORDER_SHOW', 'True', '予約注文でクレジットカード決済を表示します', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
  }

  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }

  function keys() {
    return array(
                 'MODULE_PAYMENT_TELECOM_STATUS',
                 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW',
                 'MODULE_PAYMENT_TELECOM_PREORDER_SHOW',
                 'MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID',
                 'MODULE_PAYMENT_TELECOM_SORT_ORDER',
                 'MODULE_PAYMENT_TELECOM_CONNECTION_URL',
                 'MODULE_PAYMENT_TELECOM_KID',
                 'MODULE_PAYMENT_OK_URL',
                 'MODULE_PAYMENT_NO_URL',
                 'MODULE_PAYMENT_TELECOM_COST',
                 'MODULE_PAYMENT_TELECOM_MONEY_LIMIT',
                 'MODULE_PAYMENT_TELECOM_MAILSTRING',
                 'MODULE_PAYMENT_TELECOM_PRINT_MAILSTRING',
);
  }
  
  //エラー
  function get_error() {
    global $_GET;
    
    $error_message = MODULE_PAYMENT_TELECOM_TEXT_ERROR_MESSAGE; 

    return array('title' => MODULE_PAYMENT_TELECOM_TEXT_ERROR,
                 'error' => $error_message);
  }

  function dealUnknow(&$sql_data_array){
    $telecom_option_ok = false;
    if ($_SESSION['option']) {
      $telecom_unknow = tep_db_fetch_array(tep_db_query("select * from telecom_unknow where `option`='".$_SESSION['option']."' and rel='yes'"));
      if ($telecom_unknow) {
        $sql_data_array['telecom_name']  = $telecom_unknow['username'];
        $sql_data_array['telecom_tel']   = $telecom_unknow['telno'];
        $sql_data_array['telecom_email'] = $telecom_unknow['email'];
        $sql_data_array['telecom_money'] = $telecom_unknow['money'];
        tep_db_query("update `telecom_unknow` set type='success' where `option`='".$_SESSION['option']."' and rel='yes' order by date_added limit 1");
        $telecom_option_ok = true;
      }
    }
  return $telecom_option_ok;
  }
  function getMailString($option='')
  {
    $email_printing_order ='';
  $email_printing_order .= 'この注文は【販売】です。' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '決済確認　　　　　●：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□ 無　→　仕入困難ならお客様へ電話' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '信用調査　　　　　　：□ 2回目以降　→　□ 常連（以下のチェック必要無）' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 1. 過去に本人確認をしている' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 2. 決済内容に変更がない' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 3. 短期間に高額決済がない' . "\n";
  $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
  $email_printing_order .= '　　　　　　　　　　　□ 初回　→　□ IP・ホストのチェック' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話確認をする' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 カード名義（カタカナ）＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話番号＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ カード名義・商品名・キャラ名一致' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 本人確認日：＿＿月＿＿日' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ 信用調査入力' . "\n";
  $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
  $email_printing_order .= '※ 疑わしい点があれば担当者へ報告をする　→　担当者＿＿＿＿の承諾を得た' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送　　　　　　　　：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　報告　□' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送完了メール送信　：□ 済' . "\n";
    $email_printing_order .=
    '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
    $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  return $email_printing_order;
  }
  
  function preorder_process_button($pid, $preorder_total) { 
    global $currencies;   
    global $languages_id;
   
    $preorder_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$pid."'");
    $preorder_info = tep_db_fetch_array($preorder_info_raw);
    
    $mail_body = '仮クレジットカード注文です。'."\n\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼注文日　　　　　：' . tep_date_long(time())."\n";
    $mail_body .= '▼お名前　　　　　：' . $preorder_info['customers_name']."\n";
    $mail_body .= '▼メールアドレス　：' . $preorder_info['customers_email_address']."\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼お支払金額　　　：' . $currencies->format($preorder_total) . "\n";
    $mail_body .= '▼お支払方法　　　：クレジットカード決済'."\n";
    
    $mail_body .= '▼注文商品'."\n";
    $mail_body .= "\t" . '------------------------------------------'."\n";
   
    $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
    $preorder_products_res = tep_db_fetch_array($preorder_products_raw); 
    if ($preorder_products_res) {
      $mail_body .= '・' . $preorder_products_res['products_name'] . '×' .  $preorder_products_res['products_quantity'] . "\n";
      
      $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'");
      while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
        $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info']));
        $mail_body .= '└' . $old_attr_info['title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $old_attr_info['value']) . "\n";
      }
      
      if (isset($_SESSION['preorder_option_info'])) {
        foreach ($_SESSION['preorder_option_info'] as $key => $value) {
        $i_option = explode('_', $key);
        $option_item_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$i_option[1]."' and id = '".$i_option[3]."'");
        $option_item = tep_db_fetch_array($option_item_raw); 
        if ($option_item) {
            $mail_body .= '└' . $option_item['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $value) . "\n";
          }
        }
      }
    }

    $mail_body .= "\t" . '------------------------------------------'."\n";
    
    $mail_body .= '▼お届け日時　　　　：' .  $_SESSION["preorder_info_date"].' '.$_SESSION["preorder_info_hour"].':'.$_SESSION["preroder_info_min"] .":00". "\n";
    $mail_body .= '　　　　　　　　　：' . $_SESSION["preorder_info_tori"] . "\n";
    
    $mail_body .= "\n\n";
    $mail_body .= '■IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
    $mail_body .= '■ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
    $mail_body .= '■ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
   
    tep_mail('管理者', SENTMAIL_ADDRESS, '仮クレカ注文', $mail_body, '', '');
    
    if (!isset($_SESSION['preorder_option'])) {
      $_SESSION['preorder_option'] = date('Ymd-His').ds_makeRandStr(2); 
    }
    $hidden_param_str = ''; 
    
    $hidden_param_str .= tep_draw_hidden_field('option', $_SESSION['preorder_option']);
    $hidden_param_str .= tep_draw_hidden_field('clientip', MODULE_PAYMENT_TELECOM_KID);
    $hidden_param_str .= tep_draw_hidden_field('money', $preorder_total);
    $hidden_param_str .= tep_draw_hidden_field('redirect_url', HTTP_SERVER.'/change_preorder_process.php');
    $hidden_param_str .= tep_draw_hidden_field('redirect_back_url', HTTP_SERVER.'/change_preorder.php?pid='.$preorder_info['check_preorder_str']);
    echo $hidden_param_str; 
  }
  
  function preorderDealUnknow(&$sql_data_array){
    $telecom_option_ok = false;
    if ($_SESSION['preorder_option']) {
      $telecom_unknow = tep_db_fetch_array(tep_db_query("select * from telecom_unknow where `option`='".$_SESSION['preorder_option']."' and rel='yes'"));
      if ($telecom_unknow) {
        $sql_data_array['telecom_name']  = $telecom_unknow['username'];
        $sql_data_array['telecom_tel']   = $telecom_unknow['telno'];
        $sql_data_array['telecom_email'] = $telecom_unknow['email'];
        $sql_data_array['telecom_money'] = $telecom_unknow['money'];
        tep_db_query("update `telecom_unknow` set type='success' where `option`='".$_SESSION['preorder_option']."' and rel='yes' order by date_added limit 1");
        $telecom_option_ok = true;
      }
    }
  return $telecom_option_ok;
  }
  function admin_process_pay_email($order,$total_price_mail){
    $email_credit = '';
    $email_credit .= $order->customer['name'] . '様' . "\n\n";
    $email_credit .= 'この度は、' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . 'をご利用いただき、誠にありがとうございます。' . "\n\n";
    $email_credit .= '注文番号' . $oID . 'の決済URLをお知らせいたします。' . "\n";
    $email_credit .= '下記URLをクリックし、クレジットカード決済を完了してください。' . "\n";
    $email_credit .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
    $email_credit .= 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl?clientip=76011&usrmail=' . $order->customer['email_address'] . '&money=' . $total_price_mail . "\n";
    $email_credit .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
    $email_credit .= '※ 上記URLをクリックしても決済ページが表示されない場合は、お手数ではご' . "\n";
    $email_credit .= 'ざいますが「改行」を取り除きブラウザに直接入力してアクセスしてください。' . "\n\n\n";
    $email_credit .= 'クレジットカード決済が成功しましたら、商品の手配に移らせていただきます。' . "\n";
    $email_credit .= "\n\n\n";
    $email_credit .= 'ご不明な点がございましたら、注文番号をご確認の上、' . "\n";
    $email_credit .= '「' . STORE_NAME . '」までお問い合わせください。' . "\n\n";
    $email_credit .= '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n";
    $email_credit .= COMPANY_NAME . "\n";
    $email_credit .= get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$order->info['site_id']) . "\n";
    $email_credit .= get_url_by_site_id($order->info['site_id']) . "\n";
    $email_credit .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
    return $email_credit;
  }

  function admin_get_payment_symbol(){

    return 1;
  }

}
?>
