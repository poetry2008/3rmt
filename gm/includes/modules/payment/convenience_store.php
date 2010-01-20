<?php
/*
  $Id: convenience_store.php,v 1.5 2003/09/17 00:54:27 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// 代金引換払い(手数料が購入金額に連動)
  class convenience_store {
    var $code, $title, $description, $enabled;
    var $n_fee, $s_error;
    var $email_footer;

// class constructor
    function convenience_store() {
      global $order;

      $this->code = 'convenience_store';
      $this->title       = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION;
      $this->sort_order  = MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_CONVENIENCE_STORE_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->email_footer = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_CONVENIENCE_STORE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_CONVENIENCE_STORE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      $table_fee = split("[:,]" , MODULE_PAYMENT_CONVENIENCE_STORE_COST);
      $f_find = false;
      $this->n_fee = 0;
      for ($i = 0; $i < count($table_fee); $i+=2) {
        if ($total_cost <= $table_fee[$i]) {
          $this->n_fee = $table_fee[$i+1];
          $f_find = true;
          break;
        }
      }
      if ( !$f_find ) {
        $this->s_error = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_OVERFLOW_ERROR;
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

      $added_hidden = $f_result
          ? tep_draw_hidden_field('codt_fee', $this->n_fee).tep_draw_hidden_field('cod_total_cost', $total_cost)
          : tep_draw_hidden_field('codt_fee_error', $this->s_error);

      $s_message = $f_result
        ? (MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE . '&nbsp;' . $currencies->format($this->n_fee))
        : ('<font color="#FF0000">' . $this->s_error . '</font>');

      $selection = array(
          'id' => $this->code,
          'module' => $this->title,
          'fields' => array(array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS,
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE,
                                  'field' => tep_draw_input_field('convenience_store_zip_code')),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS,
                                  'field' => tep_draw_input_field('convenience_store_address1','','maxlength="25"') . ' 25文字まで'),
							array('title' => '',
                                  'field' => tep_draw_input_field('convenience_store_address2','','maxlength="25"') . ' 25文字まで'),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME,
                                  'field' => tep_draw_input_field('convenience_store_l_name')),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME,
                                  'field' => tep_draw_input_field('convenience_store_f_name')),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL,
                                  'field' => tep_draw_input_field('convenience_store_tel') . 'ハイフンなし'),
                            array('title' => $s_message,
                                  'field' => $added_hidden))
      );

      return $selection;
    }

    function pre_confirmation_check() {
      global $HTTP_POST_VARS;
	  
	  if($HTTP_POST_VARS['convenience_store_zip_code'] == "" || $HTTP_POST_VARS['convenience_store_address1'] == "" || $HTTP_POST_VARS['convenience_store_l_name'] == "" || $HTTP_POST_VARS['convenience_store_f_name'] == "" || $HTTP_POST_VARS['convenience_store_tel'] == ""){
	    $payment_error_return = 'payment_error=' . $this->code ;
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
	  }else{
	    return false;
	  }
    }

    function confirmation() {
      global $currencies;
      global $HTTP_POST_VARS;

      $s_result = !$HTTP_POST_VARS['codt_fee_error'];
      $s_message = $s_result
        ? (MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE . '&nbsp;' . $currencies->format($HTTP_POST_VARS['codt_fee']))
        : ('<font color="#FF0000">' . $HTTP_POST_VARS['codt_fee_error'] . '</font>');

      $confirmation = array(
          'title' => $this->title,
          'fields' => array(array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS,
                                  'field' => ''),
                            array('title' => $s_message,
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE . $HTTP_POST_VARS['convenience_store_zip_code'],
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS . $HTTP_POST_VARS['convenience_store_address1'] . "&nbsp;" . $HTTP_POST_VARS['convenience_store_address2'],
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME . $HTTP_POST_VARS['convenience_store_l_name'],
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME . $HTTP_POST_VARS['convenience_store_f_name'],
                                  'field' => ''),
							array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL . $HTTP_POST_VARS['convenience_store_tel'],
                                  'field' => ''),
                           )
      );
	  
      return $confirmation;
    }

    function process_button() {
      global $currencies;
      global $HTTP_POST_VARS;
      global $order, $point;

      // 追加 - 2007.01.05 ----------------------------------------------
      $total = $order->info['total'];
      if ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true')
          && ($payment == 'cod_table')
          && isset($HTTP_POST_VARS['codt_fee'])
          && (0 < intval($HTTP_POST_VARS['codt_fee']))) {
        $total += intval($HTTP_POST_VARS['codt_fee']);
      }
	  
	  //Add point
      if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
          && (0 < intval($point))) {
        $total -= intval($point);
      }	  
	  
	  if(MODULE_ORDER_TOTAL_CONV_STATUS == 'true' && ($payment == 'convenience_store')) {
        $total += intval($HTTP_POST_VARS['codt_fee']);
	  }
      // 追加 - 2007.01.05 ----------------------------------------------
	  
      // email_footer に使用する文字列
      $s_message = $HTTP_POST_VARS['codt_fee_error']
        ? $HTTP_POST_VARS['codt_fee_error']
        : sprintf(MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER,
            $currencies->format($total),
            $currencies->format($HTTP_POST_VARS['codt_fee']));

	  return tep_draw_hidden_field('codt_message', $s_message)
           . tep_draw_hidden_field('codt_fee',$HTTP_POST_VARS['codt_fee']) // for ot_codt
		   . tep_draw_hidden_field('convenience_store_zip_code',$HTTP_POST_VARS['convenience_store_zip_code'])
		   . tep_draw_hidden_field('convenience_store_address1',$HTTP_POST_VARS['convenience_store_address1'])
		   . tep_draw_hidden_field('convenience_store_address2',$HTTP_POST_VARS['convenience_store_address2'])
		   . tep_draw_hidden_field('convenience_store_l_name',$HTTP_POST_VARS['convenience_store_l_name'])
		   . tep_draw_hidden_field('convenience_store_f_name',$HTTP_POST_VARS['convenience_store_f_name'])
		   . tep_draw_hidden_field('convenience_store_tel',$HTTP_POST_VARS['convenience_store_tel']);
    }

    function before_process() {
      global $HTTP_POST_VARS;

      $this->email_footer = $HTTP_POST_VARS['codt_message'];
    }

    function after_process() {
      return false;
    }

    function get_error() {
      global $HTTP_POST_VARS,$HTTP_GET_VARS;

      if (isset($HTTP_GET_VARS['payment_error']) && (strlen($HTTP_GET_VARS['payment_error']) > 0)) {
        $error_message = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE;
        
		return array('title' => 'コンビニ決済 エラー!',
                     'error' => $error_message);
		
	  }else{
	    return false;
	  }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('コンビニ決済を有効にする', 'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS', 'True', 'コンビニ決済による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now());");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('加盟店コード', 'MODULE_PAYMENT_CONVENIENCE_STORE_IP', '', '加盟店コードの設定をします。', '6', '2', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('接続URL', 'MODULE_PAYMENT_CONVENIENCE_STORE_URL', '', '接続URLの設定をします。', '6', '6', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('決済手数料', 'MODULE_PAYMENT_CONVENIENCE_STORE_COST', '999999:200', '決済手数料. 例: 9999:315,29999:420,99999:630,299999:1050 ... 9999円まで315円, 29999円まで420円, ...', '6', '3', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('表示の整列順', 'MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0' , now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('適用地域', 'MODULE_PAYMENT_CONVENIENCE_STORE_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '4', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('初期注文ステータス', 'MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array(
          'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS',
		  'MODULE_PAYMENT_CONVENIENCE_STORE_IP',
		  'MODULE_PAYMENT_CONVENIENCE_STORE_URL',
          'MODULE_PAYMENT_CONVENIENCE_STORE_COST',
          'MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER',
          'MODULE_PAYMENT_CONVENIENCE_STORE_ZONE',
          'MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID'
      );
    }
  }
?>