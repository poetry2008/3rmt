<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
class telecom  extends basePayment  implements paymentInterface  { 
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer, $show_payment_info;

  // class constructor
/*------------------------------
 功能：加载信用卡结算支付方法设置
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  function loadSpecialSettings($site_id=0){
    $this->site_id = $site_id;
    $this->code        = 'telecom';    
    $this->form_action_url = MODULE_PAYMENT_TELECOM_CONNECTION_URL;
    $this->show_payment_info = 1;
  }
/*----------------------------
 功能：编辑信用卡结算
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：返回乐天银行类型数据(array)
 ---------------------------*/
  function fields($theData=false, $back=false){
    if (!$back) { 
    global $order;
    $total_cost = $order->info['total'];
    $code_fee = $this->calc_fee($total_cost); 
    $added_hidden = tep_draw_hidden_field('code_fee', $code_fee);
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
/*--------------------------
 功能：JS验证
 参数：无
 返回值：验证成功(boolean) 
 -------------------------*/
  function javascript_validation() {
    return false;
  }
/*--------------------------
 功能：确认检查前台支付方法 
 参数：无
 返回值：是否检查成功(boolean)
 -------------------------*/
  function pre_confirmation_check() {
    return true;
  }
/*----------------------------
 功能：确认支付方法
 参数：无
 返回值：支付方法数据(array)
 ----------------------------*/  
  function confirmation() {
    global $currencies;
    global $_POST;
      
    $s_result = !$_POST['telecom_order_fee_error'];
    $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['telecom_order_fee_error'].'</font>'); 
    return array(
		 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
		 'fields' => array(
				   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
				   array('title' => $s_message, 'field' => '')  
				   )           
		 );      

  }
/*----------------------
 功能：判断支付方法返回的信息
 参数：无
 返回值：隐藏INPUT购买订单的信息(string)
 ---------------------*/
  function process_button() { 
    global $order, $currencies, $currency;   
    global $point,$cart,$languages_id;


    $total = $order->info['total'];
    $f_result = $this->calc_fee($total); 
    
    
    //Add point
    if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
        && (0 < intval($point))) {
      $total -= intval($point);
    }   
    
    $total += intval($this->n_fee); 

    if (isset($_SESSION['campaign_fee'])) {
      $total += $_SESSION['campaign_fee']; 
    }

    if(isset($_SESSION['h_shipping_fee'])){
      $total += $_SESSION['h_shipping_fee']; 
    }

    $products = $cart->get_products();
    
    $order_product_list = '';    
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $char_id = $products[$i]['id'];
      $order_product_list .= '・' . $products[$i]['name'] . '×' . $products[$i]['quantity'] . "\n";
      $attributes_exist = ((isset($products[$i]['op_attributes'])) ? 1 : 0);

      if ($attributes_exist == 1) {
        foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
          $op_key_array = explode('_', $op_key);
          $option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
          $option_res = tep_db_fetch_array($option_query);
          if ($option_res) {
            $order_product_list .= '└' . $option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $op_value) . "\n";
          }
        }
      }
    
      if (isset($products[$i]['ck_attributes'])) {
         foreach ($products[$i]['ck_attributes'] as $c_op_key => $c_op_value) {
           $c_op_array = explode('_', $c_op_key);
           $c_option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$c_op_array[0]."' and id = '".$c_op_array[2]."'"); 
           $c_option_res = tep_db_fetch_array($c_option_query);
           if ($c_option_res) {
             $order_product_list .= '└' . $c_option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $c_op_value) . "\n";
           }
         }
       }
    }
    
    $mail_mode = array(
        '${C_NAME}',
        '${C_EMAIL}',
        '${O_DATE}',
        '${ORDER_TOTAL}',
        '${ORDER_PRODUCT_LIST}',
        '${PO_DATE}',
        '${PO_TIME}',
        '${C_IP}',
        '${C_ANGET}',
        '${C_HOST}',
        '${PAYMENT_METHOD}'
        );
    $mail_value = array(
        $order->customer["lastname"] . ' '. $order->customer["firstname"],
        $order->customer["email_address"],
        tep_date_long(time()),
        $currencies->format($total),
        $order_product_list,
        $_SESSION["insert_torihiki_date"],
        $_SESSION["torihikihouhou"],
        $_SERVER["REMOTE_ADDR"],
        @gethostbyaddr($_SERVER["REMOTE_ADDR"]),
        $_SERVER["HTTP_USER_AGENT"],
        payment::changeRomaji($this->code,PAYMENT_RETURN_TYPE_TITLE)
        );
    
    $process_button_template = tep_get_mail_templates('MODULE_PAYMENT_CARD_CONFRIMTION_EMAIL_CONTENT',SITE_ID);
    $mail_body = str_replace($mail_mode,$mail_value,$process_button_template['contents']);
    tep_mail('TS_MODULE_PAYMENT_TELECOM_MAIL_TO_NAME', SENTMAIL_ADDRESS, $process_button_template['title'], $mail_body, '', '');
    
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
    $process_button_string .= tep_draw_hidden_field('telecom_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('code_fee', $_SESSION['h_code_fee']);
    return $process_button_string;
  }
  
  

/*-----------------------
 功能：支付前
 参数：无
 返回值：无
 ----------------------*/
  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['telecom_order_message']);
      
    return false;
  }
/*-----------------------
 功能：支付后
 参数：无
 返回值：false(boolean)
 ----------------------*/
  function after_process() {
    //update telecom_unknow table by order telecom_option
    if(isset($_SESSION['insert_id'])){
      $new_insert_id = $_SESSION['insert_id'];
      $t_otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class =
          'ot_total' and orders_id = '".$new_insert_id."' limit 1");
      $t_ot = tep_db_fetch_array($t_otq);
      $t_total = abs($t_ot['value']);
      $t_orderq = tep_db_query("select telecom_option  from ".TABLE_ORDERS." 
          where orders_id = '".$new_insert_id."' limit 1");
      $t_order = tep_db_fetch_array($t_orderq);
      if(isset($_SESSION['option_list'])
          &&is_array($_SESSION['option_list'])){
        $option_list = array_reverse($_SESSION['option_list']);
        foreach($option_list as $option_row){
          $option_exist_sql = "select * from telecom_unknow 
            where `option`='".$option_row."' limit 1";
          $option_exist_query = tep_db_query($option_exist_sql);
          if($option_exist_row = tep_db_fetch_array($option_exist_query)){
            $option_temp_total = $option_exist_row['money'];
            $option_temp_id = $option_exist_row['option'];
            if($option_temp_id == $t_order['telecom_option']){
              break;
            }
            if($option_temp_total == $t_total
                &&$option_temp_id != $t_order['telecom_option']){
              tep_db_query("update ".TABLE_ORDERS." set 
                telecom_name = '".$option_exist_row['username']."',
                telecom_tel = '".$option_exist_row['telno']."',
                telecom_email = '".$option_exist_row['email']."',
                telecom_money = '".$option_temp_total."',
                telecom_option = '".$option_temp_id."'
                where orders_id = '".$new_insert_id."'");
                tep_db_query("update `telecom_unknow` set type='success' where `option`='".  $option_temp_id."' and rel='yes' ");
              break;
            }

          }
        }
      }
      //重复的OPTION 但是金额不同的时候 调用下面代码
      tep_db_query("update `telecom_unknow` set type='hide' where `option`='".
          $t_order['telecom_option']."' and rel='yes' ");
      $t_query = tep_db_query("select * from `telecom_unknow` where `option`='".
          $t_order['telecom_option']."' and rel='yes' order by date_added");
      while($t_row = tep_db_fetch_array($t_query)){
        if($t_row['money'] == $t_total){
          if(tep_db_query("update `telecom_unknow` set type='success' where id =
                '".$t_row['id']."'")){
            tep_db_query("update ".TABLE_ORDERS." set 
                telecom_name = '".$t_row['username']."',
                telecom_tel = '".$t_row['telno']."',
                telecom_email = '".$t_row['email']."' 
                where orders_id = '".$new_insert_id."'");
            break;
          }
        }
      }
    }
    return false;
  }
/*--------------------------
 功能：输出错误 
 参数：无
 返回值：是否输出错误信息(boolean)
 -------------------------*/
  function output_error() {
    return false;
  }

/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_TELECOM_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }
/*--------------------------------
 功能：添加支付方法的SQL
 参数：无
 返回值：无
 -------------------------------*/
  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added,user_added, site_id) values ('TELECOM 支払いを有効にする', 'MODULE_PAYMENT_TELECOM_STATUS', 'True', 'TELECOM での支払いを受け付けますか?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
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
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_TELECOM_IS_GET_POINT', 'True', 'ポイント', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_TELECOM_POINT_RATE', '0.01', 'ポイント還元率', '6', '0', now(), ".$this->site_id.")");
  }
/*------------------------------
 功能：删除SQL
 参数：无
 返回值：无
 -----------------------------*/
  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }
/*-----------------------------
 功能：编辑信用卡结算支付方法
 参数：无
 返回值：信用卡结算支付方法数据(array)
 ----------------------------*/
  function keys() {
    return array(
                 'MODULE_PAYMENT_TELECOM_STATUS',
                 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW',
                 'MODULE_PAYMENT_TELECOM_PREORDER_SHOW',
                 'MODULE_PAYMENT_TELECOM_IS_GET_POINT',
                 'MODULE_PAYMENT_TELECOM_POINT_RATE',
                 'MODULE_PAYMENT_TELECOM_ORDER_STATUS_ID',
                 'MODULE_PAYMENT_TELECOM_PREORDER_STATUS_ID',
                 'MODULE_PAYMENT_TELECOM_SORT_ORDER',
                 'MODULE_PAYMENT_TELECOM_CONNECTION_URL',
                 'MODULE_PAYMENT_TELECOM_KID',
                 'MODULE_PAYMENT_OK_URL',
                 'MODULE_PAYMENT_NO_URL',
                 'MODULE_PAYMENT_TELECOM_COST',
                 'MODULE_PAYMENT_TELECOM_MONEY_LIMIT',
);
  }
  
  //错误
/*----------------------
 功能：获取错误
 参数：无
 返回值：错误信息(boolean/array)
 ----------------------*/
  function get_error() {
    global $_GET;
    
    $error_message = MODULE_PAYMENT_TELECOM_TEXT_ERROR_MESSAGE; 

    return array('title' => MODULE_PAYMENT_TELECOM_TEXT_ERROR,
                 'error' => $error_message);
  }
/*------------------------
 功能：更新信用卡结算
 参数：$sql_data_array(string) SQL数据
 返回值：判断是否更新成功(boolean)
 -----------------------*/
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
/*----------------------------------
  功能：信用卡结算预约按钮
  参数：$pid(string) 预约ID
  参数：$preorder_total(string) 预约总额
  返回值：无
  ---------------------------------*/
  function preorder_process_button($pid, $preorder_total) { 
    global $currencies;   
    global $languages_id;
   
    $preorder_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$pid."'");
    $preorder_info = tep_db_fetch_array($preorder_info_raw);
    
    $preorder_product_list = '';    
    $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
    $preorder_products_res = tep_db_fetch_array($preorder_products_raw); 
    if ($preorder_products_res) {
      $preorder_product_list .= '・' . $preorder_products_res['products_name'] . '×' .  $preorder_products_res['products_quantity'] . "\n";
      
      $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'");
      while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
        $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info']));
        $preorder_product_list .= '└' . $old_attr_info['title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $old_attr_info['value']) . "\n";
      }
      
      if (isset($_SESSION['preorder_option_info'])) {
        foreach ($_SESSION['preorder_option_info'] as $key => $value) {
        $i_option = explode('_', $key);
        $option_item_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$i_option[1]."' and id = '".$i_option[3]."'");
        $option_item = tep_db_fetch_array($option_item_raw); 
        if ($option_item) {
            $preorder_product_list .= '└' . $option_item['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $value) . "\n";
          }
        }
      }
    }
    
    $mail_mode = array(
        '${C_NAME}',
        '${C_EMAIL}',
        '${O_DATE}',
        '${ORDER_TOTAL}',
        '${ORDER_PRODUCT_LIST}',
        '${PO_DATE}',
        '${PO_TIME}',
        '${C_IP}',
        '${C_ANGET}',
        '${C_HOST}',
        '${PAYMENT_METHOD}'
        );
    $mail_value = array(
        $preorder_info['customers_name'],
        $preorder_info['customers_email_address'],
        tep_date_long(time()),
        $currencies->format($preorder_total),
        $preorder_product_list,
        $_SESSION["preorder_info_date"].' '.$_SESSION["preorder_info_hour"].':'.$_SESSION["preroder_info_min"] .":00",
        $_SESSION["preorder_info_tori"],
        $_SERVER["REMOTE_ADDR"],
        @gethostbyaddr($_SERVER["REMOTE_ADDR"]),
        $_SERVER["HTTP_USER_AGENT"],
        payment::changeRomaji($this->code,PAYMENT_RETURN_TYPE_TITLE)
        );
    

    $process_button_template = tep_get_mail_templates('MODULE_PAYMENT_CARD_CONFRIMTION_EMAIL_CONTENT',SITE_ID);
    $mail_body = str_replace($mail_mode,$mail_value,$process_button_template['contents']);
    tep_mail('TS_MODULE_PAYMENT_TELECOM_MAIL_TO_NAME', SENTMAIL_ADDRESS, $process_button_template['title'], $mail_body, '', '');
 



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
/*------------------------------
 功能：更新预约信用卡结算
 参数：$sql_data_array(string) SQL数据
 返回值：判断是否更新成功(boolean) 
 -----------------------------*/  
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
/*-----------------------------
 功能：处理后台支付电子邮件 
 参数：$order(string) 订单数组
 参数：$total_price_mail(string) 总价邮件
 返回值：返回处理之后的电子邮件(string) 
 ----------------------------*/
  function admin_process_pay_email($order,$total_price_mail){
    $email_template = tep_get_mail_templates('PAYMENT_ADMIN_CREDIT_EMAIL_CONTENT',SITE_ID);
    $email_key = array(
        '${C_NAME}',
        '${STORE_NAME}',
        '${ORDER_ID}',
        '${C_EMAIL}',
        '${O_TOTAL}',
        '${COMPANY_NAME}',
        '${SUPPORT_EMAIL_ADDRESS}',
        '${SITE_URL}',
        );
    $email_value = array(
        $order->customer['name'],
        get_configuration_by_site_id('STORE_NAME',$order->info['site_id']),
        $oID,
        $order->customer['email_address'],
        $total_price_mail,
        COMPANY_NAME,
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$order->info['site_id']),
        get_url_by_site_id($order->info['site_id'])
        );
    $email_credit = str_replace($email_key,$email_value,$email_template['contents']);
    return $email_credit;
  }
/*-------------------------
 功能：获取支付标志值
 参数：无
 返回值：标志值(string) 
 ------------------------*/
  function admin_get_payment_symbol(){

    return 1;
  }
/*----------------------------
 功能：显示后台支付方法列表 
 参数：$pay_info_array(array) 支付信息数组
 返回值：无
 ---------------------------*/  
  function admin_show_payment_list($pay_info_array){

   global $_POST;
   global $_GET;
   $pay_array = explode("\n",trim($pay_info_array[0]));
   $bank_name = explode(':',$pay_array[0]);
   $bank_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_name'] : $bank_name[1];
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo 'document.getElementsByName("bank_name")[0].value = "'. $bank_name[1] .'";'."\n"; 
   $bank_shiten = explode(':',$pay_array[1]); 
   $bank_shiten[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'] : $bank_shiten[1];
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo 'document.getElementsByName("bank_shiten")[0].value = "'. $bank_shiten[1] .'";'."\n"; 
   $bank_kamoku = explode(':',$pay_array[2]);
   $bank_kamoku[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'] : $bank_kamoku[1];
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_TELECOM_NORMAL || $bank_kamoku[1] == ''){
     echo 'document.getElementsByName("bank_kamoku")[0].checked = true;'."\n"; 
   }else{
     echo 'document.getElementsByName("bank_kamoku")[1].checked = true;'."\n"; 
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   $bank_kouza_num[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'] : $bank_kouza_num[1];
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo 'document.getElementsByName("bank_kouza_num")[0].value = "'.$bank_kouza_num[1].'";'."\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   $bank_kouza_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'] : $bank_kouza_name[1];
   $bank_kouza_name[1] = isset($_POST['bank_kouza_name']) ? $_POST['bank_kouza_name'] : $bank_kouza_name[1];
   echo 'document.getElementsByName("bank_kouza_name")[0].value = "'.$bank_kouza_name[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[1]));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['con_email']) ? $_SESSION['orders_update_products'][$_GET['oID']]['con_email'] : $con_email[1];
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   echo 'document.getElementsByName("con_email")[0].value = "'.$con_email[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[2]));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['rak_tel']) ? $_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'] : $rak_tel[1];
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo 'document.getElementsByName("rak_tel")[0].value = "'.$rak_tel[1].'";'."\n";
   echo <<<EOT
   $("input[name='con_email']").blur(function(){
     var con_email = document.getElementsByName("con_email")[0].value;
     orders_session('con_email',con_email);
   });
   $("input[name='bank_name']").blur(function(){
     var payment_value = document.getElementsByName("bank_name")[0].value;
     orders_session('bank_name',payment_value);
   });
   $("input[name='bank_shiten']").blur(function(){
     var payment_value = document.getElementsByName("bank_shiten")[0].value;
     orders_session('bank_shiten',payment_value);
   });
   $("input[name='bank_kamoku']").click(function(){
     if(document.getElementsByName("bank_kamoku")[0].checked == true){
       var payment_value = document.getElementsByName("bank_kamoku")[0].value;
     }else{
      var payment_value = document.getElementsByName("bank_kamoku")[1].value; 
     }
     orders_session('bank_kamoku',payment_value);
   });
   $("input[name='bank_kouza_num']").blur(function(){
     var payment_value = document.getElementsByName("bank_kouza_num")[0].value;
     orders_session('bank_kouza_num',payment_value);
   });
   $("input[name='bank_kouza_name']").blur(function(){
     var payment_value = document.getElementsByName("bank_kouza_name")[0].value;
     orders_session('bank_kouza_name',payment_value);
   });
   $("input[name='rak_tel']").blur(function(){
     var payment_value = document.getElementsByName("rak_tel")[0].value;
     orders_session('rak_tel',payment_value);
   });
EOT;
   echo "\n";
  }
/*------------------------------
 功能：获取后台顾客点值 
 参数：$point_value(string) 点值
 参数：$customer_id(string) 顾客ID
 返回值：无
 -----------------------------*/
  function admin_get_customer_point($point_value,$customer_id){
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value .  " where customers_id = '" .$customer_id."' and customers_guest_chk = '0' ");
  } 
/*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_TELECOM_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_TELECOM_POINT_RATE', (int)$site_id); 
  }
/*----------------------
 功能：后台计算得到的点数
 参数：$orders_id(string) 订单ID
 参数：$point_rate(string) 点率
 参数：$site_id(string) SITE_ID值
 返回值：点数(string) 
 ---------------------*/
  function admin_calc_get_point($orders_id, $point_rate, $site_id)
  {
    $order_point_raw = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".$orders_id."'"); 
    $order_point = tep_db_fetch_array($order_point_raw); 
    
    $order_subtotal_raw = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".$orders_id."'"); 
    $order_subtotal = tep_db_fetch_array($order_subtotal_raw); 
  
    $order_campaign_raw = tep_db_query("select campaign_fee from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$orders_id."' and site_id = '".$site_id."'");
    $order_campaign = tep_db_fetch_array($order_campaign_raw);
    
    if ($order_subtotal['value'] > 0) {
      if ($order_campaign) {
        return ($order_subtotal['value'] + $order_campaign['campaign_fee']) * $point_rate; 
      } else {
        return ($order_subtotal['value'] - (int)$order_point['value']) * $point_rate; 
      }
    } else {
      if ($order_campaign) {
        return (abs($order_subtotal['value']) + abs($order_campaign['campaign_fee'])) * $point_rate; 
      } else {
        return abs($order_subtotal['value']) * $point_rate; 
      }
    }
  }
/*--------------------------
 功能：获取点率
 参数：无
 返回值：点率(string)
 -------------------------*/  
  function get_point_rate()
  {
    return MODULE_PAYMENT_TELECOM_POINT_RATE; 
  }
/*--------------------------
 功能：后台获得评论
 参数：$comment(string) 评论
 返回值：评论信息(string)
 -------------------------*/
  function admin_get_comment($comment){

    $payment_comment_array = array(TS_TEXT_BANK_NAME,
                                   TS_TEXT_BANK_SHITEN,
                                   TS_TEXT_BANK_KAMOKU,
                                   TS_TEXT_BANK_KOUZA_NUM,
                                   TS_TEXT_BANK_KOUZA_NAME,
                                   TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                                   TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL
                                 );
    $comment_array = explode("\n",$comment);
    $comment_str_array = array();
    $comment_str = '';
    foreach($comment_array as $value){

      $value_array = explode(':',$value); 
      if(!(in_array($value_array[0].':',$payment_comment_array) && trim($value_array[1]) == '')){

        $comment_str_array[] = $value;
      }
    }
    $comment_str = implode("\n",$comment_str_array);
    return $comment_str;
  }
}
?>
