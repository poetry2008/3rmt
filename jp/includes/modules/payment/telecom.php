<?php
/*
  TELECOM module
*/

  class telecom {
    var $code, $title, $description, $enabled;

// class constructor
    function telecom() {
      global $order, $HTTP_GET_VARS;

	  $this->code = 'telecom';
      $this->title = MODULE_PAYMENT_TELECOM_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_TELECOM_TEXT_DESCRIPTION;
	  $this->explain = MODULE_PAYMENT_TELECOM_TEXT_EXPLAIN;
      $this->sort_order = MODULE_PAYMENT_TELECOM_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_TELECOM_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = MODULE_PAYMENT_TELECOM_CONNECTION_URL;
	  
	  if(isset($HTTP_GET_VARS['submit_x']) || isset($HTTP_GET_VARS['submit_y'])){
	    $HTTP_GET_VARS['payment_error'] = 'telecom';
	  }
	  
	  $this->email_footer = MODULE_PAYMENT_TELECOM_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_TELECOM_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_TELECOM_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title,
				   'fields' => array(array('title' => $this->explain,'field' => '')));
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    
	function process_button() {	
      global $order, $currencies, $currency;   
      global $point,$cart,$languages_id;

      // �ɲ� - 2007.01.05 ----------------------------------------------
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
      // �ɲ� - 2007.01.05 ----------------------------------------------
	  
	  #mail����
	  $mail_body = '�����쥸�åȥ�������ʸ�Ǥ���'."\n\n";
	  
	  # �桼��������----------------------------
	  $mail_body .= '������������������������������������������'."\n";
	  $mail_body .= '����ʸ�ֹ桡��������2007****-********'."\n";
	  $mail_body .= '����ʸ��������������' . strftime(DATE_FORMAT_LONG)."\n";
	  $mail_body .= '����̾��������������' . $order->customer["lastname"] . ' ' . $order->customer["firstname"]."\n";
	  $mail_body .= '���᡼�륢�ɥ쥹����' . $order->customer["email_address"]."\n";
	  $mail_body .= '������������������������������������������'."\n";
	  $mail_body .= '������ʧ��ۡ�������' . $total . '��'."\n";
	  $mail_body .= '������ʧ��ˡ�����������쥸�åȥ����ɷ��'."\n";
	  
	  # ��������----------------------------
	  $mail_body .= '����ʸ����'."\n";
	  $mail_body .= "\t" . '------------------------------------------'."\n";

      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if (isset($products[$i]['attributes'])) {
          while (list($option, $value) = each($products[$i]['attributes'])) {
            echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
            $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity
                                        from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                        where pa.products_id = '" . $products[$i]['id'] . "'
                                         and pa.options_id = '" . $option . "'
                                         and pa.options_id = popt.products_options_id
                                         and pa.options_values_id = '" . $value . "'
                                         and pa.options_values_id = poval.products_options_values_id
                                         and popt.language_id = '" . $languages_id . "'
                                         and poval.language_id = '" . $languages_id . "'");
            $attributes_values = tep_db_fetch_array($attributes);

            $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
            $products[$i][$option]['options_values_id'] = $value;
            $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
            $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
            $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
		    $products[$i][$option]['products_at_quantity'] = $attributes_values['products_at_quantity'];
          }
        }
      }
	  
	  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
	    $char_id = $products[$i]['id'];
		$mail_body .= '��' . $products[$i]['name'] . '��' . $products[$i]['quantity'] . '(����饯����̾:' . $_SESSION["character"][$char_id] . ')' . "\n";
	    $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);

        if ($attributes_exist == 1) {
          reset($products[$i]['attributes']);
          while (list($option, $value) = each($products[$i]['attributes'])) {
            $mail_body .= '��' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . "\n";
          }
        }
	  }

/*	  
	  foreach($order->products as $key => $val){
	    $char_id = $val["id"];
		$mail_body .= "\t" . $val["name"] . '��' . $val["qty"] . '�ġʥ���饯����̾��' . $_SESSION["character"][$char_id] . '��' . "\n";
		$mail_body .= "\t" . '���ץ��������������' . "\n";
	  }
*/	  
	  $mail_body .= "\t" . '------------------------------------------'."\n";
	  
	  # �������----------------------------
	  $mail_body .= '�������������������' . $_SESSION["insert_torihiki_date"] . "\n";
	  $mail_body .= '��������������������' . $_SESSION["torihikihouhou"] . "\n";
	  
	  # �桼��������������Ȥʤ�----------------------------
	  $mail_body .= "\n\n";
	  $mail_body .= '��IP���ɥ쥹��������������' . $_SERVER["REMOTE_ADDR"] . "\n";
	  $mail_body .= '���ۥ���̾����������������' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
	  $mail_body .= '���桼��������������ȡ���' . $_SERVER["HTTP_USER_AGENT"] . "\n";
	  
	  tep_mail('������', SEND_EXTRA_ORDER_EMAILS_TO, '�����쥫��ʸ', $mail_body, '', '');
	  
	  $today = date("YmdHis");
	     
	  $process_button_string = tep_draw_hidden_field('clientip', MODULE_PAYMENT_TELECOM_KID) .
							   tep_draw_hidden_field('money', $total) .
							   tep_draw_hidden_field('redirect_url', tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL')) .
							   tep_draw_hidden_field('redirect_back_url', tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL'));
	  
      return $process_button_string;
    }
	

    function before_process() {
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_TELECOM_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('TELECOM ��ʧ����ͭ���ˤ���', 'MODULE_PAYMENT_TELECOM_STATUS', 'True', 'TELECOM �Ǥλ�ʧ��������դ��ޤ���?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ɽ���������', 'MODULE_PAYMENT_TELECOM_SORT_ORDER', '0', 'ɽ��������������Ǥ��ޤ����������������ۤɾ�̤�ɽ������ޤ�.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('�����ʸ���ơ�����', 'MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID', '0', '���ꤷ�����ơ��������������Ŭ�Ѥ���ޤ�.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('��³��URL', 'MODULE_PAYMENT_TELECOM_CONNECTION_URL', '', '�ƥ쥳�९�쥸�åȿ������ղ���URL������򤷤ޤ���', '6', '0', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('���ȥ�����', 'MODULE_PAYMENT_TELECOM_KID', '', '���ȥ����ɤ�����򤷤ޤ���', '6', '0', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('�����URL(�����)', 'MODULE_PAYMENT_OK_URL', 'checkout_process.php', '�����URL(�����)������򤷤ޤ���', '6', '0', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('�����URL(����󥻥��)', 'MODULE_PAYMENT_NO_URL', 'checkout_payment.php', '�����URL(����󥻥��)������򤷤ޤ���', '6', '0', now())");
	}

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
	  return array('MODULE_PAYMENT_TELECOM_STATUS', 'MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID', 'MODULE_PAYMENT_TELECOM_SORT_ORDER', 'MODULE_PAYMENT_TELECOM_CONNECTION_URL', 'MODULE_PAYMENT_TELECOM_KID', 'MODULE_PAYMENT_OK_URL', 'MODULE_PAYMENT_NO_URL');
    }
	
	//���顼
	function get_error() {
      global $HTTP_GET_VARS;
	  
      $error_message = MODULE_PAYMENT_TELECOM_TEXT_ERROR_MESSAGE;	

      return array('title' => MODULE_PAYMENT_TELECOM_TEXT_ERROR,
                   'error' => $error_message);
    }
	
  }
?>
