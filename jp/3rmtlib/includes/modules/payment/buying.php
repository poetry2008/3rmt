<?php
/*
  $Id$
*/

  class buying {
    var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer;
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
  <td class="main" colspan="3"><b><?php echo TABLE_HEADING_BANK; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main" width="30%"><?php echo TEXT_BANK_NAME; ?></td>
    <td class="main" width="70%"><?php echo $bank_name; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_SHITEN; ?></td>
    <td class="main"><?php echo $bank_shiten; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KAMOKU; ?></td>
    <td class="main"><?php echo $bank_kamoku; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KOUZA_NUM; ?></td>
    <td class="main"><?php echo $bank_kouza_num; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KOUZA_NAME; ?></td>
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
// class constructor
    function buying($site_id = 0) {
      global $order;
      
      $this->site_id = $site_id;

      $this->code        = 'buying';
      $this->title       = MODULE_PAYMENT_BUYING_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_BUYING_TEXT_DESCRIPTION;
      $this->explain       = MODULE_PAYMENT_BUYING_TEXT_EXPLAIN;
      $this->sort_order  = MODULE_PAYMENT_BUYING_SORT_ORDER;
      $this->enabled     = ((MODULE_PAYMENT_BUYING_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_BUYING_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_BUYING_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    
      $this->email_footer = MODULE_PAYMENT_BUYING_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_BUYING_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_BUYING_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }
    
    function calc_fee($total_cost) {
      $table_fee = split("[:,]" , MODULE_PAYMENT_BUYING_COST);
      $f_find = false;
      $this->n_fee = 0;
      for ($i = 0; $i < count($table_fee); $i+=2) {
        if ($total_cost <= $table_fee[$i]) { 
          $additional_fee = $total_cost.$table_fee[$i+1]; 
          @eval("\$additional_fee = $additional_fee;"); 
          //$this->n_fee = $table_fee[$i+1]; 
          if (is_numeric($additional_fee)) {
            $this->n_fee = intval($additional_fee); 
          } else {
            $this->n_fee = 0; 
          }
          $f_find = true;
          break;
        }
      }
      if ( !$f_find ) {
        $this->s_error = MODULE_PAYMENT_BUYING_TEXT_OVERFLOW_ERROR;
      }

      return $f_find;
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      global $currencies;
      global $order;
      
      $total_cost = $order->info['total'];
      $f_result = $this->calc_fee($total_cost); 
      
      $added_hidden = $f_result ? tep_draw_hidden_field('buying_order_fee', $this->n_fee):tep_draw_hidden_field('buying_order_fee_error', $this->s_error);
      
      if (!empty($this->n_fee)) {
        $s_message = $f_result ? (MODULE_PAYMENT_BUYING_TEXT_FEE . '&nbsp;' .  $currencies->format($this->n_fee)):('<font color="#FF0000">'.$this->s_error.'</font>'); 
      } else {
        $s_message = $f_result ? '':('<font color="#FF0000">'.$this->s_error.'</font>'); 
      }
      return array('id' => $this->code,
                   'module' => '銀行振込(買い取り)',
                   'fields' => array(
				     array(
					   'title' => $s_message, 
					   'field' => $added_hidden
					   ) , 

				     array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.
					   '<div class="cemail_input_01">'.
					   TEXT_BANK_NAME.
					   '</div>'.
					   '<div class="con_email01">'.
					   tep_draw_input_field('bank_name', '').
					   '</div></div>', 
					   'field' => '',
					   ) ,
				     array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.
					   '<div class="cemail_input_01">'.
					   TEXT_BANK_SHITEN.
					   '</div>'.
					   '<div class="con_email01">'.
					   tep_draw_input_field('bank_shiten', '').
					   '</div></div>', 
					   'field' => '',
					   ) ,
				     array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.
					   '<div class="cemail_input_01">'.
					   TEXT_BANK_KAMOKU.
					   '</div>'.
					   '<div class="con_email01">'.
					   tep_draw_radio_field('bank_kamoku',TEXT_BANK_SELECT_KAMOKU_F ,$bank_sele_f) . '&nbsp;' . TEXT_BANK_SELECT_KAMOKU_F.
					   tep_draw_radio_field('bank_kamoku',TEXT_BANK_SELECT_KAMOKU_T ,$bank_sele_t) . '&nbsp;' . TEXT_BANK_SELECT_KAMOKU_T.
					   '</div></div>', 
					   'field' => '',
					   ) ,
				     array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.
					   '<div class="cemail_input_01">'.
					   TEXT_BANK_KOUZA_NUM.
					   '</div>'.
					   '<div class="con_email01">'.
					   tep_draw_input_field('bank_kouza_num', '').
					   '</div></div>', 
					   'field' => '',
					   ) ,
				     array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.
					   '<div class="cemail_input_01">'.
					   TEXT_BANK_KOUZA_NAME.
					   '</div>'.
					   '<div class="con_email01">'.
					   tep_draw_input_field('bank_kouza_name', '').
					   '</div></div>', 
					   'field' => '',
					   ) ,
					 array(
					   'title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;"></div>', 
					   'field' => '',
					   ) ,

				     )
      );
    }

    function pre_confirmation_check() {

  $bank_name = tep_db_prepare_input($_POST['bank_name']);
  $bank_shiten = tep_db_prepare_input($_POST['bank_shiten']);
  $bank_kamoku = tep_db_prepare_input($_POST['bank_kamoku']);
  $bank_kouza_num = tep_db_prepare_input($_POST['bank_kouza_num']);
  $bank_kouza_name = tep_db_prepare_input($_POST['bank_kouza_name']);
  
  tep_session_register('bank_name');
  tep_session_register('bank_shiten');
  tep_session_register('bank_kamoku');
  tep_session_register('bank_kouza_num');
  tep_session_register('bank_kouza_name');
  
  if($bank_name == '') {
    tep_session_unregister('bank_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_NAME), 'SSL'));
  }
  if($bank_shiten == '') {
    tep_session_unregister('bank_shiten');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_SHITEN), 'SSL'));
  }
  if($bank_kouza_num == '') {
    tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NUM), 'SSL'));
  }
  if (!preg_match("/^[0-9]+$/", $bank_kouza_num)) {
    tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NUM2), 'SSL'));
  } 
  if($bank_kouza_name == '') {
    tep_session_unregister('bank_kouza_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NAME), 'SSL'));
  }


      return false;
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
      
      if (!empty($this->n_fee)) {
        return array(
            'title' => MODULE_PAYMENT_BUYING_TEXT_DESCRIPTION,
            'fields' => array(array('title' => MODULE_PAYMENT_BUYING_TEXT_PROCESS,
                                    'field' => ''),
                              array('title' => $s_message, 'field' => '')  
                       )           
            );
      } else {
        if ($this->check_buy_goods()) {
          return array(
              'title' => MODULE_PAYMENT_BUYING_TEXT_DESCRIPTION,
              'fields' => array(
                                array('title' => MODULE_PAYMENT_BUYING_TEXT_SHOW, 'field' => ''),  
                                array('title' => $s_message, 'field' => '')  
                         )           
              );
        } else {
          return array(
              'title' => MODULE_PAYMENT_BUYING_TEXT_DESCRIPTION,
              'fields' => array(array('title' => $s_message, 'field' => '')  
                         )           
              );
        }
      }
      //return false;
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
      
      $s_message = $_POST['buying_order_fee_error']?$_POST['buying_order_fee_error']:sprintf(MODULE_PAYMENT_BUYING_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_POST['buying_order_fee']));
      
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
        $error_message = MODULE_PAYMENT_BUYING_TEXT_ERROR_MESSATE;
        
        return array('title' => 'コンビニ決済 エラー!', 'error' => $error_message);
      } else {
        return false;
      }
      //return false;
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
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }

    function keys() {
      return array('MODULE_PAYMENT_BUYING_STATUS', 'MODULE_PAYMENT_BUYING_LIMIT_SHOW', 'MODULE_PAYMENT_BUYING_ZONE', 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', 'MODULE_PAYMENT_BUYING_SORT_ORDER', 'MODULE_PAYMENT_BUYING_COST', 'MODULE_PAYMENT_BUYING_MONEY_LIMIT');
    }
    function getMailStrign($option=''){
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
      
      return $email_printing_order;
    }
  }
?>
