<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
class buying extends basePayment  implements paymentInterface  {
  private  $selection = NULL;
  var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer, $show_payment_info, $show_add_comment;
  //取得配置
  function loadSpecialSettings($site_id = 0)
  {
    $this->code        = 'buying';
    $this->show_payment_info = 0;
    $this->show_add_comment = 1; 
  }
  function javascript_validation() {
    return false;
  }
  function fields($theData=false, $back=false){
    if ($back) {
    return array(
                 array(
                       "code"=>'bank_name',
                       "title"=>TS_TEXT_BANK_NAME,
                       "field"=>tep_draw_input_field('bank_name', $theData['bank_name']),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       ),
                 array(
                       "code"=>'bank_shiten',
                       "title"=>TS_TEXT_BANK_SHITEN,
                       "field"=>tep_draw_input_field('bank_shiten', $theData['bank_shiten']),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       ),
                 array(
                       "code"=>'bank_kamoku',
                       "title"=>TS_TEXT_BANK_KAMOKU,
                       "field"=> tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_F ,(($back==false)?($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F):(!isset($theData['bank_kamoku'])?true:($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F)))) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_F.
                       tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_T ,$theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_T) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_T,
                       "rule"=>basePayment::RULE_NOT_NULL,

                       ),
                 array(
                       "code"=>'bank_kouza_num',
                       "title"=>TS_TEXT_BANK_KOUZA_NUM,
                       "field"=>tep_draw_input_field('bank_kouza_num', $theData['bank_kouza_num']),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_IS_NUMBER)
                       ),
                 array(
                       "code"=>'bank_kouza_name',
                       "title"=>TS_TEXT_BANK_KOUZA_NAME,
                       "field"=>tep_draw_input_field('bank_kouza_name', $theData['bank_kouza_name']).((!$back)?'<br>'.TS_TEXT_BANK_KOUZA_NAME_READ:''),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       ),
                 );
    } else {
    return array(
                 array(
                       "code"=>'bank_name',
                       "title"=>TS_TEXT_BANK_NAME,
                       "field"=>tep_draw_input_field('bank_name', $theData['bank_name']),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_NAME 
                       ),
                 array(
                       "code"=>'bank_shiten',
                       "title"=>TS_TEXT_BANK_SHITEN,
                       "field"=>tep_draw_input_field('bank_shiten', $theData['bank_shiten']),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_SHITEN 
                       ),
                 array(
                       "code"=>'bank_kamoku',
                       "title"=>TS_TEXT_BANK_KAMOKU,
                       "field"=> tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_F ,(($back==false)?($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F):(!isset($theData['bank_kamoku'])?true:($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F)))) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_F.
                       tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_T ,$theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_T) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_T,
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_KAMOKU 

                       ),
                 array(
                       "code"=>'bank_kouza_num',
                       "title"=>TS_TEXT_BANK_KOUZA_NUM,
                       "field"=>tep_draw_input_field('bank_kouza_num', $theData['bank_kouza_num']),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_IS_NUMBER),
                       "error_msg" => array(TS_TEXT_BANK_ERROR_KOUZA_NUM, TS_TEXT_BANK_ERROR_KOUZA_NUM2) 
                       ),
                 array(
                       "code"=>'bank_kouza_name',
                       "title"=>TS_TEXT_BANK_KOUZA_NAME,
                       "field"=>tep_draw_input_field('bank_kouza_name', $theData['bank_kouza_name']).((!$back)?'<br>'.TS_TEXT_BANK_KOUZA_NAME_READ:''),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_KOUZA_NAME 
                       ),
                 );
    }
  }

  function pre_confirmation_check() {
    return true;
  }
  /*
    $bank_name = tep_db_prepare_input($_POST['bank_name']);
    $bank_shiten = tep_db_prepare_input($_POST['bank_shiten']);
    $bank_kamoku = tep_db_prepare_input($_POST['bank_kamoku']);
    $bank_kouza_num = tep_db_prepare_input($_POST['bank_kouza_num']);
    $bank_kouza_name = tep_db_prepare_input($_POST['bank_kouza_name']);
  
    tep_session_register('bank_kouza_name');
    $_SESSION['bank_kamoku']      = $bank_kamoku;
    $_SESSION['bank_shiten']      = $bank_shiten;
    $_SESSION['bank_name']        = $bank_name;
    $_SESSION['bank_kouza_num']   = $bank_kouza_num;
    $_SESSION['bank_kouza_name']  = $bank_kouza_name;
    
    $payment_error_return = 'payment_error='.$this->code;
    if($bank_name == '') {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_NAME;
      tep_session_unregister('bank_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    if($bank_shiten == '') {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_SHITEN;
      tep_session_unregister('bank_shiten');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    if($bank_kamoku == '') {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_KAMOKU;
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    if($bank_kouza_num == '') {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_KOUZA_NUM;
      tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));

    }
    if (!preg_match("/^[0-9]+$/", $bank_kouza_num)) {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_KOUZA_NUM2;
      tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    } 
    if($bank_kouza_name == '') {
      $_SESSION['bank_error'] =true;
      $_SESSION['bank_error_info'] =TEXT_BANK_ERROR_KOUZA_NAME;
      tep_session_unregister('bank_kouza_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false)); 
    }

    return false;
  }
  */
  function preorder_confirmation_check() {
    global $_POST;
    
    $preorder_bank_name = tep_db_prepare_input($_POST['bank_name']);
    $preorder_bank_shiten = tep_db_prepare_input($_POST['bank_shiten']);
    $preorder_bank_kamoku = tep_db_prepare_input($_POST['bank_kamoku']);
    $preorder_bank_kouza_num = tep_db_prepare_input($_POST['bank_kouza_num']);
    $preorder_bank_kouza_name = tep_db_prepare_input($_POST['bank_kouza_name']);
  
    
    if($preorder_bank_name == '') {
      return 1; 
    }
    if($preorder_bank_shiten == '') {
      return 2; 
    }
    if($preorder_bank_kamoku == '') {
      return 3; 
    }
    if($preorder_bank_kouza_num == '') {
      return 4; 
    }
    if (!preg_match("/^[0-9]+$/", $preorder_bank_kouza_num)) {
      return 5; 
    } 
    if($preorder_bank_kouza_name == '') {
      return 6; 
    }

    return 0;
  }
  
  function confirmation() {
    global $currencies;
    global $_POST;
    global $order;
      
    $s_result = !$_POST['buying_order_fee_error'];
    $this->calc_fee($order->info['total']);
    if (!empty($this->n_fee)) {
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['buying_order_fee_error'].'</font>'); 
    } else {
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['buying_order_fee_error'].'</font>'); 
    }
    return array(
                 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
                 'fields' => array(
                                   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
                                   array('title' => $s_message, 'field' => '')  
                                   )           
                 );


  }

  function check_buy_goods() {
    global $cart;
    return $cart->show_total() > 0;
  }
  function process_button() {
    global $currencies;
    global $_POST; 
    global $order;
    $total = $order->info['total'];
    if ($payment == 'buying') {
      $total += intval($_POST['buying_order_fee']); 
    }
    $mailFooter = get_configuration_by_site_id_or_default("MODULE_PAYMENT_BUYING_TEXT_MAILFOOTER",$this->site_id);
    $s_message  = $_POST['buying_order_fee_error']?$_POST['buying_order_fee_error']:sprintf($mailFooter, $currencies->format($total), $currencies->format($_POST['buying_order_fee']));
      
    return tep_draw_hidden_field('buying_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('buying_order_fee', $_POST['buying_order_fee']);
    //return false;
  }

  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['buying_order_message']);
    //return false;
  }

  function after_process() {
    return false;
  }

  function get_error() {
    global $_POST, $_GET;
    
    if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
      $error_message = get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYING_TEXT_ERROR_MESSATE',$this->site_id);
      return array('title' => $this->title.' エラー!', 'error' => $error_message);
    } else {
      return false;
    }
  }

  function get_preorder_error($error_type) {
    switch ($error_type) {
      case '1':
        $error_msg =TS_TEXT_BANK_ERROR_NAME;
        break;
      case '2':
        $error_msg =TS_TEXT_BANK_ERROR_SHITEN;
        break;
      case '3':
        $error_msg =TS_TEXT_BANK_ERROR_KAMOKU;
        break;
      case '4':
        $error_msg =TS_TEXT_BANK_ERROR_KOUZA_NUM;
        break;
      case '5':
        $error_msg =TS_TEXT_BANK_ERROR_KOUZA_NUM2;
        break;
      case '6':
        $error_msg =TS_TEXT_BANK_ERROR_KOUZA_NAME;
        break;
      default:
        $error_msg = ''; 
        break;
    }
    return $error_msg; 
  }
  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BUYING_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('買い取りを有効にする', 'MODULE_PAYMENT_BUYING_STATUS', 'True', '銀行振込による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    //tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('お振込先:', 'MODULE_PAYMENT_BUYING_PAYTO', '', 'お振込先名義を設定してください.', '6', '1', now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_BUYING_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_BUYING_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_BUYING_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_BUYING_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_BUYING_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_BUYING_PREORDER_SHOW', 'True', '予約注文で銀行振込(買い取り)を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
  }

  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }

  function keys() {
    return array(
		 'MODULE_PAYMENT_BUYING_STATUS', 
		 'MODULE_PAYMENT_BUYING_LIMIT_SHOW', 
                 'MODULE_PAYMENT_BUYING_PREORDER_SHOW',
		 'MODULE_PAYMENT_BUYING_ZONE', 
		 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', 
		 'MODULE_PAYMENT_BUYING_SORT_ORDER', 
		 'MODULE_PAYMENT_BUYING_COST', 
		 'MODULE_PAYMENT_BUYING_MONEY_LIMIT',
		 'MODULE_PAYMENT_BUYING_MAILSTRING',
		 'MODULE_PAYMENT_BUYING_PRINT_MAILSTRING'
		 );
  }
  function getMailString($option=''){
    $email_printing_order ='';
    $email_printing_order .= '★★★★★★★★★★★★この注文は【買取】です。★★★★★★★★★★★★' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= 'キャラクターの有無　：□ 有　　｜　　□ 無　→　新規作成してお客様へ連絡' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '受領　※注意※　　●：＿＿月＿＿日' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　□ 報告' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '受領メール送信　　　：□ 済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '支払　　　　　　　　：＿＿月＿＿日　※総額5,000円未満は168円引く※' . "\n";
    $email_printing_order .= '　　　　　　　　　　　□ JNB　　□ eBank　　□ ゆうちょ' . "\n";
    $email_printing_order .= '　　　　　　　　　　　入金予定日＿＿月＿＿日　受付番号＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '支払完了メール送信　：□ 済　　　※追加文章がないか確認しましたか？※' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
    $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
    return $email_printing_order;
  }
  function dealComment($comment, $session_paymentinfo_name)
  {
    //global $bank_name,$bank_shiten,$bank_kamoku,$bank_kouza_num,$bank_kouza_name;
    if(isset($_SESSION[$session_paymentinfo_name]['bank_name'])) {
      $bbbank = TS_TEXT_BANK_NAME .   $_SESSION[$session_paymentinfo_name]['bank_name'] . "\n";
      $bbbank .= TS_TEXT_BANK_SHITEN .  $_SESSION[$session_paymentinfo_name]['bank_shiten'] . "\n";
      $bbbank .= TS_TEXT_BANK_KAMOKU .  $_SESSION[$session_paymentinfo_name]['bank_kamoku'] . "\n";
      $bbbank .= TS_TEXT_BANK_KOUZA_NUM .  $_SESSION[$session_paymentinfo_name]['bank_kouza_num'] . "\n";
      $bbbank .= TS_TEXT_BANK_KOUZA_NAME .  $_SESSION[$session_paymentinfo_name]['bank_kouza_name'];
    }else{
      global $_POST;
      $bbbank = TS_TEXT_BANK_NAME .  $_POST['bank_name'] . "\n";
      $bbbank .= TS_TEXT_BANK_SHITEN .  $_POST['bank_shiten'] . "\n";
      $bbbank .= TS_TEXT_BANK_KAMOKU .  $_POST['bank_kamoku'] . "\n";
      $bbbank .= TS_TEXT_BANK_KOUZA_NUM .  $_POST['bank_kouza_num'] . "\n";
      $bbbank .= TS_TEXT_BANK_KOUZA_NAME .  $_POST['bank_kouza_name'];
      $payment_bank_info['bank_name']        = $_POST['bank_name'];
      $payment_bank_info['bank_shiten']      = $_POST['bank_shiten'];
      $payment_bank_info['bank_kamoku']      = $_POST['bank_kamoku'];
      $payment_bank_info['bank_kouza_num']   = $_POST['bank_kouza_num'];
      $payment_bank_info['bank_kouza_name']  = $_POST['bank_kouza_name'];
    }
    $comment = $bbbank ."\n".$comment;
    $res_arr = array('comment'=>$comment,'payment_bank_info'=>$payment_bank_info); 
    return $res_arr;
  }
  function specialOutput()
  {
    $bank_name = tep_db_prepare_input($_POST['bank_name']);
    $bank_shiten = tep_db_prepare_input($_POST['bank_shiten']);
    $bank_kamoku = tep_db_prepare_input($_POST['bank_kamoku']);
    $bank_kouza_num = tep_db_prepare_input($_POST['bank_kouza_num']);
    $bank_kouza_name = tep_db_prepare_input($_POST['bank_kouza_name']);

    ?>
    <tr> 
       <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
       <tr class="infoBoxContents"> 
       <td>
       <table width="100%" class="table_ie" border="0" cellspacing="0" cellpadding="2">
       <tr>
       <td class="main" colspan="3"><b>
       <?php 
       echo TS_TABLE_HEADING_BANK; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; 
    ?></td>
    </tr>
        <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                                                                          <td class="main" width="30%"><?php echo TS_TEXT_BANK_NAME; ?></td>
                                                                                                                                                           <td class="main" width="70%"><?php echo $bank_name; ?></td>
                                                                                                                                                                                                                     </tr>
                                                                                                                                                                                                                     <tr>
                                                                                                                                                                                                                     <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                                                                                                                                                                                                                                                                                       <td class="main"><?php echo TS_TEXT_BANK_SHITEN; ?></td>
                                                                                                                                                                                                                                                                                                                                                              <td class="main"><?php echo $bank_shiten; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                              </tr>
                                                                                                                                                                                                                                                                                                                                                                                                              <tr>
                                                                                                                                                                                                                                                                                                                                                                                                              <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="main"><?php echo TS_TEXT_BANK_KAMOKU; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       <td class="main"><?php echo $bank_kamoku; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <td class="main"><?php echo TS_TEXT_BANK_KOUZA_NUM; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <td class="main"><?php echo $bank_kouza_num; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <td class="main"><?php echo TS_TEXT_BANK_KOUZA_NAME; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <td class="main"><?php echo $bank_kouza_name; ?></td>
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }
  
  function admin_add_additional_info(&$sql_data_array)
  {
      global $_POST; 
      $sql_data_array['bank_info'] = $_POST['bank_name'].'<<<|||'.$_POST['bank_shiten'].'<<<|||'.$_POST['bank_kamoku'].'<<<|||'.$_POST['bank_kouza_num'].'<<<|||'.$_POST['bank_kouza_name']; 
  }
  
  function admin_deal_comment($order_info)
  {
     $bank_info_array = explode('<<<|||', $order_info['bank_info']); 
     return TS_TEXT_BANK_NAME.$bank_info_array[0]."\n".TS_TEXT_BANK_SHITEN.$bank_info_array[1]."\n".TS_TEXT_BANK_KAMOKU.$bank_info_array[2]."\n".TS_TEXT_BANK_KOUZA_NUM.$bank_info_array[3]."\n".TS_TEXT_BANK_KOUZA_NAME.$bank_info_array[4]; 
  }

  
  function deal_preorder_additional($pInfo, &$sql_data_array)
  {
    $bbbank = TS_TEXT_BANK_NAME . '：' . $pInfo['bank_name'] . "\n";
    $bbbank .= TS_TEXT_BANK_SHITEN . '：' . $pInfo['bank_shiten'] . "\n";
    $bbbank .= TS_TEXT_BANK_KAMOKU . '：' . $pInfo['bank_kamoku'] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NUM . '：' . $pInfo['bank_kouza_num'] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NAME . '：' . $pInfo['bank_kouza_name'];
    
    $comment = $bbbank ."\n".$pInfo['yourmessage'];
    return $comment;
  }
  
}
?>
