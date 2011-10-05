<?php
/*
   $Id$
 */

// 代金引換払い(手数料が購入金額に連動)
class rakuten_bank {
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer;
  var $arrs2d = array('１' => '1', '２' => '2', '３' => '3', '４' => '4', 
       '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', '０' => '0','－' => '-');

  // class constructor
  function rakuten_bank($site_id = 0) {
    global $order;

    $this->site_id = $site_id;

    $this->code        = 'rakuten_bank';
    $this->title       = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_DESCRIPTION;
    $this->sort_order  = MODULE_PAYMENT_RAKUTEN_BANK_SORT_ORDER;
    $this->enabled     = ((MODULE_PAYMENT_RAKUTEN_BANK_STATUS == 'True') ? true : false);

    if ((int)MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();

  }

  // class methods
  function update_status() {
    global $order;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_RAKUTEN_BANK_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_RAKUTEN_BANK_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

  // 代引手数料を計算する
  function calc_fee($total_cost) {
    $table_fee = split("[:,]" , MODULE_PAYMENT_RAKUTEN_BANK_COST);
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
      $this->s_error = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_OVERFLOW_ERROR;
    }

    return $f_find;
  }

  // class methods
  function javascript_validation() {
    return false;
  }

  function selection() {
    global $currencies;
    global $order;

    $total_cost = $order->info['total'];      // 税金も含めた代金の総額
    $f_result = $this->calc_fee($total_cost); // 手数料
/*  
    //commented by rekam
    $added_hidden = $f_result
      ? tep_draw_hidden_field('codt_fee', $this->n_fee).tep_draw_hidden_field('cod_total_cost', $total_cost)
      : tep_draw_hidden_field('codt_fee_error', $this->s_error);
*/
    $added_hidden = ''; // added by rekam
    if (!empty($this->n_fee)) {
      $s_message = $f_result ? (MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE . '&nbsp;' . $currencies->format($this->n_fee)) : ('<font color="#FF0000">' . $this->s_error . '</font>');
    } else {
      $s_message = $f_result ? '': ('<font color="#FF0000">' . $this->s_error . '</font>');
    }
    //$s_message = $f_result ? (MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE . '&nbsp;' . $currencies->format($this->n_fee)) : ('<font color="#FF0000">' . $this->s_error . '</font>');
    $email_default_str = ''; 
    /*
       if (isset($_SESSION['customer_emailaddress'])) {
       $email_default_str = $_SESSION['customer_emailaddress']; 
       }
     */
    //if(SITE_ID == 1){
      $selection = array(
          'id' => $this->code,
          'module' => $this->title,
          'fields' => array(array('title' => MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS,
              'field' => ''),
            array('title' => '<div id="ctelnumber" class="rowHide rowHide_'.$this->code.'"
              style="display:none;">'.MODULE_PAYMENT_RAKUTEN_INFO_TEXT.'<div
              class="ctelnumber_input">'.MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT.'<div
              class="con_telnumber_input">'.tep_draw_input_field('rakuten_telnumber', $email_default_str, 'onpaste="return false"').'  '.MODULE_PAYMENT_RAKUTEN_MUST_INPUT.'</div></div></div>', 
              'field' => '' 
              ), 
            array('title' => '<div id="catelnumber" class="rowHide rowHide_'.$this->code.'"
              style="display:none;"><div
              class="ctelnumber_input_validate">'.MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT.'<div
              class="con_telnumber_input_validate">'.tep_draw_input_field('rakuten_telnumber_again', $email_default_str, 'onpaste="return false"').'  '.MODULE_PAYMENT_RAKUTEN_MUST_INPUT.'</div></div></div>',
              'field' => '' 
              ), 
            array('title' => $s_message,
              'field' => $added_hidden)
            )
          );
    /*
    }else{
      $selection = array(
          'id' => $this->code,
          'module' => $this->title,
          'fields' => array(array('title' => MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS,
              'field' => ''),
            array('title' => '<div id="cemail" '.$this->code.'" id="cemail" class= "rowHide rowHide_'.$this->code.'"
              style="display:none;">'.MODULE_PAYMENT_RAKUTEN_INFO_TEXT.MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT.'<div
              class="con_telnumber_input">'.tep_draw_input_field('rakuten_telnumber', $email_default_str, 'onpaste="return false"').MODULE_PAYMENT_RAKUTEN_MUST_INPUT.'</div></div>', 
              'field' => '' 
              ), 
            array('title' => '<div id="caemail" '.$this->code.'" id="cemail" class= "rowHide rowHide_'.$this->code.'"
              style="display:none;">'.MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT.'<div
              class="con_telnumber_input_validate">'.tep_draw_input_field('rakuten_telnumber_again', $email_default_str, 'onpaste="return false"').MODULE_PAYMENT_RAKUTEN_MUST_INPUT.'</div><p>'.MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FOOTER.'</p></div>',
              'field' => '' 
              ), 
            array('title' => $s_message,
              'field' => $added_hidden)
            )
          );
   }
   */

    return $selection;
  }

  function pre_confirmation_check() {
    global $_POST;
    /* 
       if($_POST['rakuten_store_l_name'] == "" || $_POST['rakuten_store_f_name'] == "" || $_POST['rakuten_store_tel'] == ""){
       $payment_error_return = 'payment_error=' . $this->code ;
       tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
       }else{
       return false;
       }
     */ 
    if ($_POST['rakuten_telnumber'] == "" || $_POST['rakuten_telnumber_again'] == "") {
      $payment_error_return = 'payment_error=' . $this->code ;
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));

    } else if
      (!preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", strtr($_POST['rakuten_telnumber'], $this->arrs2d))||
       !preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", strtr($_POST['rakuten_telnumber_again'], $this->arrs2d))){
        $payment_error_return = 'payment_error=' . $this->code ;
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT,
              $payment_error_return.'&type=nom', 'SSL', true, false));
      } else if (strtr($_POST['rakuten_telnumber'], $this->arrs2d) != strtr($_POST['rakuten_telnumber_again'], $this->arrs2d)) {
        $payment_error_return = 'payment_error=' . $this->code; 
        $redirect_url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return . '&type=noe', 'SSL', true, false);
        //do for &type turn into &amp;type url ,fix it afterlater
        $url_test = explode('?',$redirect_url);
        if ($url_test[1] == 'payment_error=rakuten_bank&amp;type=noe')
        {
          $url_test[1] = 'payment_error=rakuten_bank&type=noe';
          $redirect_url = $url_test[0] .'?'. $url_test[1]; 
        }
        //do for &type turn into &amp;type url ,fix it afterlater
        //tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return . '&type=noe', 'SSL', true, false));
        tep_redirect($redirect_url);

      } 
  }
  
  function preorder_confirmation_check() {
    global $_POST;
    if ($_POST['rakuten_telnumber'] == "" || $_POST['rakuten_telnumber_again'] == "") {
      return 3;
    } else if
      (!preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", strtr($_POST['rakuten_telnumber'], $this->arrs2d))||
       !preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", strtr($_POST['rakuten_telnumber_again'], $this->arrs2d))){
        return 3; 
      } else if (strtr($_POST['rakuten_telnumber'], $this->arrs2d) != strtr($_POST['rakuten_telnumber_again'], $this->arrs2d)) {
        return 1;
      } 
  }

  function confirmation() {
    global $currencies;
    global $_POST;

    $s_result = !$_POST['codt_fee_error'];

    if (!empty($_POST['codt_fee'])) {
      //$s_message = $s_result ? (MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE . '&nbsp;' . $currencies->format($_POST['codt_fee'])) : ('<font color="#FF0000">' . $_POST['codt_fee_error'] . '</font>');
      $s_message = $s_result ? '' : ('<font color="#FF0000">' . $_POST['codt_fee_error'] . '</font>');
    } else {
      $s_message = $s_result ? '' : ('<font color="#FF0000">' . $_POST['codt_fee_error'] . '</font>');
    }

    if (!empty($_POST['codt_fee'])) {
      $confirmation = array(
          'title' => $this->title,
          'fields' => array(array('title' => MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS_CON,
              'field' => ''),
            array('title' =>
              MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER.MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT.strtr($_POST['rakuten_telnumber'], $this->arrs2d),
              'field' => ''),
            array('title' => $s_message,
              'field' => ''),
            )
          );
    } else {
      $confirmation = array(
          'title' => $this->title,
          'fields' => array(array('title' =>
              MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER.MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT.strtr($_POST['rakuten_telnumber'], $this->arrs2d),
              'field' => ''),
            array('title' => $s_message,
              'field' => ''),
            )
          );
    }

    return $confirmation;
  }

  function process_button() {
    global $currencies;
    global $_POST;
    global $order, $point;

    // 追加 - 2007.01.05 ----------------------------------------------
    $total = $order->info['total'];
    if ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true')
        && ($payment == 'cod_table')
        && isset($_POST['codt_fee'])
        && (0 < intval($_POST['codt_fee']))) {
      $total += intval($_POST['codt_fee']);
    }

    //Add point
    if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
        && (0 < intval($point))) {
      $total -= intval($point);
    }   

    if(MODULE_ORDER_TOTAL_CONV_STATUS == 'true' && ($payment == 'rakuten_bank')) {
      $total += intval($_POST['codt_fee']);
    }
    // 追加 - 2007.01.05 ----------------------------------------------

    // email_footer に使用する文字列
    $s_message = $_POST['codt_fee_error']
      ? $_POST['codt_fee_error']
      : sprintf(MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER,
          $currencies->format($total),
          $currencies->format($_POST['codt_fee']));

    return tep_draw_hidden_field('codt_message', $s_message)
      . tep_draw_hidden_field('rakuten_telnumber', $_POST['rakuten_telnumber']) 
      . tep_draw_hidden_field('codt_fee',$_POST['codt_fee']); // for ot_codt
  }

  function before_process() {
    global $_POST;

  }

  function after_process() {
    return false;
  }

  function get_error() {
    global $_POST,$_GET;

    if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
      if (isset($_GET['type']) && $_GET['type'] == 'noe')
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOE;
      }
      else if (isset($_GET['type']) && $_GET['type'] == 'nom')
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOM;
      }
      else
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE;
      }

      return array('title' => '楽天銀行 エラー!',
          'error' => $error_message);

    }else{
      return false;
    }
  }
  
  function get_preorder_error($error_type) {
      if ($error_type == 1)
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOE;
      }
      else if ($error_type == 2)
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOM;
      }
      else
      {
        $error_message = MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE;
      }
    return $error_message; 
  }

  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_RAKUTEN_BANK_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('楽天銀行を有効にする', 'MODULE_PAYMENT_RAKUTEN_BANK_STATUS', 'True', '楽天銀行による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('加盟店コード', 'MODULE_PAYMENT_RAKUTEN_BANK_IP', '', '加盟店コードの設定をします。', '6', '2', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('接続URL', 'MODULE_PAYMENT_RAKUTEN_BANK_URL', '', '接続URLの設定をします。', '6', '6', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_RAKUTEN_BANK_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_RAKUTEN_BANK_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0' , now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_RAKUTEN_BANK_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '4', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_RAKUTEN_BANK_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
      例：0,3000
      0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_RAKUTEN_BANK_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ',now(), ".$this->site_id.");");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_RAKUTEN_BANK_PREORDER_SHOW', 'True', '予約注文で楽天銀行を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
  }

  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }

  function keys() {
    /*
       'MODULE_PAYMENT_RAKUTEN_BANK_IP', 
       'MODULE_PAYMENT_RAKUTEN_BANK_URL', 
     */
    return array( 
        'MODULE_PAYMENT_RAKUTEN_BANK_STATUS', 
        'MODULE_PAYMENT_RAKUTEN_BANK_LIMIT_SHOW', 
        'MODULE_PAYMENT_RAKUTEN_BANK_PREORDER_SHOW', 
        'MODULE_PAYMENT_RAKUTEN_BANK_ZONE', 
        'MODULE_PAYMENT_RAKUTEN_BANK_ORDER_STATUS_ID' , 
        'MODULE_PAYMENT_RAKUTEN_BANK_SORT_ORDER', 
        'MODULE_PAYMENT_RAKUTEN_BANK_COST', 
        'MODULE_PAYMENT_RAKUTEN_BANK_MONEY_LIMIT');
  }
  function replace_for_telnumber($str){
    return str_replace('-','',strtr($str,$this->arrs2d));
  }
}
?>
