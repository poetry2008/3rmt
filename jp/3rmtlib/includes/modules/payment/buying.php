<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
class buying extends basePayment  implements paymentInterface  {
  private  $selection = NULL;
  var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer, $show_payment_info, $show_add_comment;
  //取得配置
/*------------------------------
 功能：加载银行转账(买入)方法设置
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  function loadSpecialSettings($site_id = 0)
  {
    $this->code        = 'buying';
    $this->show_payment_info = 0;
    $this->show_add_comment = 1; 
  }
/*-----------------------------
 功能：JS验证
 参数：无
 返回值：JS验证是否成功(boolean)
 ----------------------------*/
  function javascript_validation() {
    return false;
  }
/*----------------------------
 功能：编辑银行转账(买入)
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：返回银行转账类型数据(array)
 ---------------------------*/
  function fields($theData=false, $back=false){
    if ($back) {
    return array(
                 array(
                       "code"=>'bank_name',
                       "title"=>TS_TEXT_BANK_NAME,
                       "field"=>tep_draw_input_field('bank_name', $theData['bank_name'], 'size="45"'),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       ),
                 array(
                       "code"=>'bank_shiten',
                       "title"=>TS_TEXT_BANK_SHITEN,
                       "field"=>tep_draw_input_field('bank_shiten', $theData['bank_shiten'], 'size="45"'),
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
                       "field"=>tep_draw_input_field('bank_kouza_num', $theData['bank_kouza_num'], 'size="45"'),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_IS_NUMBER)
                       ),
                 array(
                       "code"=>'bank_kouza_name',
                       "title"=>TS_TEXT_BANK_KOUZA_NAME,
                       "field"=>tep_draw_input_field('bank_kouza_name', $theData['bank_kouza_name'], 'size="45"').((!$back)?'<br>'.TS_TEXT_BANK_KOUZA_NAME_READ:''),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       ),
                 );
    } else {
     $input_text_id = ' class="input_text" ';
     if(NEW_STYLE_WEB===true){
       $style_width = 'style="width:231px;"';
     }else{
       $style_width = '';
     }
    return array(
                 array(
                       "code"=>'bank_name',
                       "title"=>TS_TEXT_BANK_NAME,
                       "field"=>tep_draw_input_field('bank_name', $theData['bank_name'],''.$style_width.$input_text_id.''),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_NAME 
                       ),
                 array(
                       "code"=>'bank_shiten',
                       "title"=>TS_TEXT_BANK_SHITEN,
                       "field"=>tep_draw_input_field('bank_shiten',
                         $theData['bank_shiten'],''.$style_width.$input_text_id),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_SHITEN 
                       ),
                 array(
                       "code"=>'bank_kamoku',
                       "title"=>TS_TEXT_BANK_KAMOKU,
                       "field"=>
                       tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_F
                         ,(($back==false)?($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F):(!isset($theData['bank_kamoku'])?true:($theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_F)))
                       ) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_F.'&nbsp;&nbsp;'.  tep_draw_radio_field('bank_kamoku',TS_TEXT_BANK_SELECT_KAMOKU_T
                         ,$theData['bank_kamoku']==TS_TEXT_BANK_SELECT_KAMOKU_T) . '&nbsp;' . TS_TEXT_BANK_SELECT_KAMOKU_T,
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_KAMOKU 

                       ),
                 array(
                       "code"=>'bank_kouza_num',
                       "title"=>TS_TEXT_BANK_KOUZA_NUM,
                       "field"=>tep_draw_input_field('bank_kouza_num', $theData['bank_kouza_num'],''.$style_width.$input_text_id.''),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_IS_NUMBER),
                       "error_msg" => array(TS_TEXT_BANK_ERROR_KOUZA_NUM, TS_TEXT_BANK_ERROR_KOUZA_NUM2) 
                       ),
                 array(
                       "code"=>'bank_kouza_name',
                       "title"=>TS_TEXT_BANK_KOUZA_NAME,
                       "field"=>tep_draw_input_field('bank_kouza_name', $theData['bank_kouza_name'],''.$style_width.$input_text_id.'').((!$back)?'<br>'.TS_TEXT_BANK_KOUZA_NAME_READ:''),
                       "rule"=>basePayment::RULE_NOT_NULL,
                       "error_msg" => TS_TEXT_BANK_ERROR_KOUZA_NAME 
                       ),
                 );
    }
  }
/*--------------------------
 功能：确认检查前台支付方法 
 参数：无
 返回值：是否检查成功(boolean)
 -------------------------*/
  function pre_confirmation_check() {
    return true;
  }
/*--------------------------------
 功能：检查预约支付方法 
 参数：无
 返回值：返回支付方法值名(string)
 ------------------------------*/
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
/*----------------------------
 功能：确认支付方法
 参数：无
 返回值：支付方法数据(array)
 ----------------------------*/  
  function confirmation() {
    global $currencies;
    global $_POST;
    global $order;
      
    $s_result = !$_POST['buying_order_fee_error'];
    $this->calc_fee($order->info['total']);
    $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['buying_order_fee_error'].'</font>'); 
    return array(
                 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
                 'fields' => array(
                                   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
                                   array('title' => $s_message, 'field' => '')  
                                   )           
                 );


  }
/*----------------------
 功能：检查购买的商品
 参数：无
 返回值：显示购买总计(string)
 ----------------------*/
  function check_buy_goods() {
    global $cart;
    return $cart->show_total() > 0;
  }
/*----------------------
 功能：判断支付方法返回的信息
 参数：无
 返回值：隐藏INPUT购买订单的信息(string)
 ---------------------*/
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
  }
/*-----------------------
 功能：购买前
 参数：无
 返回值：无
 ----------------------*/
  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['buying_order_message']);
  }
/*-----------------------
 功能：购买后
 参数：无
 返回值：false(boolean)
 ----------------------*/
  function after_process() {
    return false;
  }
/*----------------------
 功能：获取错误
 参数：无
 返回值：错误信息(boolean/array)
 ----------------------*/
  function get_error() {
    global $_POST, $_GET;
    
    if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
      $error_message = get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYING_TEXT_ERROR_MESSATE',$this->site_id);
      return array('title' => $this->title.' エラー!', 'error' => $error_message);
    } else {
      return false;
    }
  }
/*----------------------
 功能：获取预约错误
 参数：$error_type(string) 错误类型
 返回值：错误信息(string)
 ---------------------*/
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
/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BUYING_STATUS' and site_id = '".$this->site_id."'");
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
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
      configuration_key, configuration_value, configuration_description,
      configuration_group_id, sort_order, set_function, date_added,user_added,
      site_id) values ('買い取りを有効にする', 'MODULE_PAYMENT_BUYING_STATUS',
        'True', '銀行振込による支払いを受け付けますか?', '6', '1',
        'tep_cfg_select_option(array(\'True\', \'False\'), ', now(),
          '".$_SESSION['user_name']."',".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_BUYING_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_BUYING_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_BUYING_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_BUYING_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_BUYING_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_BUYING_PREORDER_SHOW', 'True', '予約注文で銀行振込(買い取り)を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_BUYING_IS_GET_POINT', 'True', 'ポイント', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_BUYING_POINT_RATE', '0', 'ポイント還元率', '6', '0', now(), ".$this->site_id.")");
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
 功能：编辑购买方法
 参数：无
 返回值：购买方法数据(array)
 ----------------------------*/
  function keys() {
    return array(
		 'MODULE_PAYMENT_BUYING_STATUS', 
		 'MODULE_PAYMENT_BUYING_LIMIT_SHOW', 
                 'MODULE_PAYMENT_BUYING_PREORDER_SHOW',
                 'MODULE_PAYMENT_BUYING_IS_GET_POINT',
                 'MODULE_PAYMENT_BUYING_POINT_RATE',
		 'MODULE_PAYMENT_BUYING_ZONE', 
                 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', 
		 'MODULE_PAYMENT_BUYING_PREORDER_STATUS_ID',
		 'MODULE_PAYMENT_BUYING_SORT_ORDER', 
		 'MODULE_PAYMENT_BUYING_COST', 
		 'MODULE_PAYMENT_BUYING_MONEY_LIMIT',
		 );
  }
/*--------------------------
 功能：处理评论
 参数：$comment(string) 评论
 参数：$session_paymentinfo_name(string) 支付方法名称
 返回值：返回评论信息和支付银行信息(array)
 -------------------------*/
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
    $res_arr = array('comment'=>$comment,'payment_bank_info'=>$payment_bank_info,'payment_info'=>$bbbank); 
    return $res_arr;
  }
/*-----------------------------
 功能：输出支付方法
 参数：$session_paymentinfo_name(string) 支付方法名称
 返回值：支付方法数组(array)
 ----------------------------*/
  function specialOutput($session_paymentinfo_name)
  {
    $buying_info_array = array(); 
    $bank_name = tep_db_prepare_input($_SESSION[$session_paymentinfo_name]['bank_name']);
    $bank_shiten = tep_db_prepare_input($_SESSION[$session_paymentinfo_name]['bank_shiten']);
    $bank_kamoku = tep_db_prepare_input($_SESSION[$session_paymentinfo_name]['bank_kamoku']);
    $bank_kouza_num = tep_db_prepare_input($_SESSION[$session_paymentinfo_name]['bank_kouza_num']);
    $bank_kouza_name = tep_db_prepare_input($_SESSION[$session_paymentinfo_name]['bank_kouza_name']);
   
    $buying_info_array[] = TS_TABLE_HEADING_BANK;
    $buying_info_array[] = array(TS_TEXT_BANK_NAME, $bank_name);
    $buying_info_array[] = array(TS_TEXT_BANK_SHITEN, $bank_shiten);
    $buying_info_array[] = array(TS_TEXT_BANK_KAMOKU, $bank_kamoku);
    $buying_info_array[] = array(TS_TEXT_BANK_KOUZA_NUM, $bank_kouza_num);
    $buying_info_array[] = array(TS_TEXT_BANK_KOUZA_NAME, $bank_kouza_name);
    
    return $buying_info_array; 
  } 
/*-----------------------------------
 功能：后台添加信息 
 参数：$sql_data_array(string) SQL数据
 返回值：无
 ----------------------------------*/  
  function admin_add_additional_info(&$sql_data_array)
  {
      global $_POST; 
      $sql_data_array['bank_info'] = $_POST['bank_name'].'<<<|||'.$_POST['bank_shiten'].'<<<|||'.$_POST['bank_kamoku'].'<<<|||'.$_POST['bank_kouza_num'].'<<<|||'.$_POST['bank_kouza_name']; 
  }
/*---------------------------------
 功能：后台处理评论信息
 参数：$order_info(string) 订单信息
 返回值：评论信息(string)
 --------------------------------*/  
  function admin_deal_comment($order_info)
  {
     $bank_info_array = explode('<<<|||', $order_info['bank_info']); 
     return TS_TEXT_BANK_NAME.$bank_info_array[0]."\n".TS_TEXT_BANK_SHITEN.$bank_info_array[1]."\n".TS_TEXT_BANK_KAMOKU.$bank_info_array[2]."\n".TS_TEXT_BANK_KOUZA_NUM.$bank_info_array[3]."\n".TS_TEXT_BANK_KOUZA_NAME.$bank_info_array[4]; 
  }

/*--------------------------------
 功能：处理预约额外的评论
 参数：$pInfo(string) 银行数组输出
 参数：$sql_data_array(string) SQL数据
 返回值：评论信息(string)
 -------------------------------*/  
  function deal_preorder_additional($pInfo, &$sql_data_array)
  {
    $bbbank = TS_TEXT_BANK_NAME . $pInfo['bank_name'] . "\n";
    $bbbank .= TS_TEXT_BANK_SHITEN . $pInfo['bank_shiten'] . "\n";
    $bbbank .= TS_TEXT_BANK_KAMOKU . $pInfo['bank_kamoku'] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NUM . $pInfo['bank_kouza_num'] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NAME . $pInfo['bank_kouza_name'];
    
    $comment = $bbbank ."\n".$pInfo['yourmessage'];
    return $comment;
  }
/*-------------------------------
 功能：处理预约邮件选项
 参数：$mailoption(string) 邮件选项
 参数：$pInfo(string) 银行数组输出
 返回值：无
 ------------------------------*/
  function preorder_deal_mailoption(&$mailoption, $pInfo)
  {
    $bank_info_array = explode('<<<|||', $pInfo['bank_info']); 
    $mailoption['BANK_NAME'] = $bank_info_array[0]; 
    $mailoption['BRANCH_NAME'] = $bank_info_array[1]; 
    $mailoption['TYPE_OF_ACCOUNT'] = $bank_info_array[2]; 
    $mailoption['ACCOUNT_NUMBER'] = $bank_info_array[3]; 
    $mailoption['ACCOUNT_HOLDERS_NAME'] = $bank_info_array[4]; 
  }
/*--------------------------
 功能：处理邮件选项
 参数：$mailoption(string) 邮件选项
 参数：$session_paymentinfo_name(string) 支付方法名称
 返回值：无
 -------------------------*/
  function deal_mailoption(&$mailoption, $session_paymentinfo_name)
  {
    $mailoption['BANK_NAME'] = $_SESSION[$session_paymentinfo_name]['bank_name']; 
    $mailoption['BRANCH_NAME'] = $_SESSION[$session_paymentinfo_name]['bank_shiten']; 
    $mailoption['TYPE_OF_ACCOUNT'] = $_SESSION[$session_paymentinfo_name]['bank_kamoku']; 
    $mailoption['ACCOUNT_NUMBER'] = $_SESSION[$session_paymentinfo_name]['bank_kouza_num']; 
    $mailoption['ACCOUNT_HOLDERS_NAME'] = $_SESSION[$session_paymentinfo_name]['bank_kouza_name']; 
  }
/*-------------------------
 功能：处理预约信息 
 参数：$pInfo(string) 银行数组输出
 参数：$sql_data_array(string) SQL数据
 返回值：无
 ------------------------*/ 
  function deal_preorder_info($pInfo, &$sql_data_array) {
    $sql_data_array['bank_info'] = $pInfo['bank_name'].'<<<|||'.$pInfo['bank_shiten'].'<<<|||'.$pInfo['bank_kamoku'].'<<<|||'.$pInfo['bank_kouza_num'].'<<<|||'.$pInfo['bank_kouza_name']; 
  }
/*-------------------------
 功能：处理后台邮件选项
 参数：$mailoption(string) 邮件选项
 参数：$oID(string) ID值
 返回值：无
 ------------------------*/
  function admin_deal_mailoption(&$mailoption, $oID)
  {
    if(isset($_SESSION['payment_bank_info'][$oID])&& !empty($_SESSION['payment_bank_info'][$oID])){
      $mailoption['BANK_NAME'] = $_SESSION['payment_bank_info'][$oID]['bank_name']; 
      $mailoption['BRANCH_NAME']      = $_SESSION['payment_bank_info'][$oID]['bank_shiten'] ;  
      $mailoption['TYPE_OF_ACCOUNT']      = $_SESSION['payment_bank_info'][$oID]['bank_kamoku'];
      $mailoption['ACCOUNT_NUMBER'] = $_SESSION['payment_bank_info'][$oID]['bank_kouza_num'] ;
      $mailoption['ACCOUNT_HOLDERS_NAME'] = $_SESSION['payment_bank_info'][$oID]['bank_kouza_name'];
      $mailoption['ADD_INFO'] = $_SESSION['payment_bank_info'][$oID]['add_info'];
    }
  }
/*-----------------------------
 功能：获取预约添加信息 
 参数：$order_info(string) 订单信息
 返回值：预约添加信息(string)
 ----------------------------*/
  function get_preorder_add_info($order_info)
  {
    $buying_info = explode('<<<|||', $order_info['bank_info']); 
    $bbbank = TS_TEXT_BANK_NAME . $buying_info[0] . "\n";
    $bbbank .= TS_TEXT_BANK_SHITEN . $buying_info[1] . "\n";
    $bbbank .= TS_TEXT_BANK_KAMOKU . $buying_info[2] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NUM . $buying_info[3] . "\n";
    $bbbank .= TS_TEXT_BANK_KOUZA_NAME . $buying_info[4];
    
    $_SESSION['preorder_payment_info'] = $bbbank; 
    $comment = $bbbank ."\n".$order_info['comment_msg'];
    return $comment;
  }
/*----------------------------
 功能：显示后台支付方法列表 
 参数：$pay_info_array(array) 支付信息数组
 参数：$default_email_info(string) 默认邮件地址
 参数：$is_show(boolean) 是否显示默认值 
 返回值：无
 ---------------------------*/  
  function admin_show_payment_list($pay_info_array, $default_email_info, $is_show){

   global $_POST;
   global $_GET;
   $pay_array = explode("\n",trim($pay_info_array[0]));
   $bank_name = explode(':',$pay_array[0]);
   if (!$is_show) {
     $bank_name[1] = ''; 
   }
   $bank_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_name'] : $bank_name[1];
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo <<<EOT
   if (document.getElementsByName("bank_name")[0]) {
     document.getElementsByName("bank_name")[0].value = "$bank_name[1]"; 
   }
EOT;
   echo "\n";
   $bank_shiten = explode(':',$pay_array[1]); 
   if (!$is_show) {
     $bank_shiten[1] = ''; 
   }
   $bank_shiten[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'] : $bank_shiten[1];
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo <<<EOT
   if (document.getElementsByName("bank_shiten")[0]) {
     document.getElementsByName("bank_shiten")[0].value = "$bank_shiten[1]"; 
   }
EOT;
   echo "\n";
   $bank_kamoku = explode(':',$pay_array[2]);
   if (!$is_show) {
     $bank_kamoku[1] = ''; 
   }
   $bank_kamoku[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'] : $bank_kamoku[1];
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_TEXT_BANK_SELECT_KAMOKU_F || $bank_kamoku[1] == ''){
   echo <<<EOT
   if (document.getElementsByName("bank_kamoku")[0]) {
     document.getElementsByName("bank_kamoku")[0].checked = true; 
   }
EOT;
   echo "\n";
   }else{
   echo <<<EOT
   if (document.getElementsByName("bank_kamoku")[1]) {
     document.getElementsByName("bank_kamoku")[1].checked = true; 
   }
EOT;
   echo "\n";
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   if (!$is_show) {
     $bank_kouza_num[1] = ''; 
   }
   $bank_kouza_num[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'] : $bank_kouza_num[1];
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo <<<EOT
   if (document.getElementsByName("bank_kouza_num")[0]) {
     document.getElementsByName("bank_kouza_num")[0].value = "$bank_kouza_num[1]";
   }
EOT;
   echo "\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   if (!$is_show) {
     $bank_kouza_name[1] = ''; 
   }
   $bank_kouza_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'] : $bank_kouza_name[1];
   $bank_kouza_name[1] = isset($_POST['bank_kouza_name']) ? $_POST['bank_kouza_name'] : $bank_kouza_name[1];
   echo <<<EOT
   if (document.getElementsByName("bank_kouza_name")[0]) {
     document.getElementsByName("bank_kouza_name")[0].value = "$bank_kouza_name[1]";
   }
EOT;
   echo "\n";
   $pay_array = explode("\n",trim($pay_info_array[1]));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['con_email']) ? $_SESSION['orders_update_products'][$_GET['oID']]['con_email'] : $con_email[1];
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   $tmp_email = (!empty($con_email[1])?$con_email[1]:$default_email_info); 
   echo <<<EOT
   if (document.getElementsByName("con_email")[0]) {
     document.getElementsByName("con_email")[0].value = "$tmp_email";
   }
EOT;
   echo "\n";
   $pay_array = explode("\n",trim($pay_info_array[2]));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['rak_tel']) ? $_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'] : $rak_tel[1];
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo <<<EOT
   if (document.getElementsByName("rak_tel")[0]) {
     document.getElementsByName("rak_tel")[0].value = "$rak_tel[1]";
   }
EOT;
   echo "\n";
   
   echo <<<EOT
   if (document.getElementsByName("con_email")[0]) {
     $("input[name='con_email']").blur(function(){
       var con_email = document.getElementsByName("con_email")[0].value;
       orders_session('con_email',con_email);
     });
   }
   if (document.getElementsByName("bank_name")[0]) {
     $("input[name='bank_name']").blur(function(){
       var payment_value = document.getElementsByName("bank_name")[0].value;
       orders_session('bank_name',payment_value);
     });
   }
   if (document.getElementsByName("bank_shiten")[0]) {
     $("input[name='bank_shiten']").blur(function(){
       var payment_value = document.getElementsByName("bank_shiten")[0].value;
       orders_session('bank_shiten',payment_value);
     });
   }
   if (document.getElementsByName("bank_kamoku")[0]) {
     $("input[name='bank_kamoku']").click(function(){
       if(document.getElementsByName("bank_kamoku")[0].checked == true){
         var payment_value = document.getElementsByName("bank_kamoku")[0].value;
       }else{
        var payment_value = document.getElementsByName("bank_kamoku")[1].value; 
       }
       orders_session('bank_kamoku',payment_value);
     });
   }
   if (document.getElementsByName("bank_kouza_num")[0]) {
     $("input[name='bank_kouza_num']").blur(function(){
       var payment_value = document.getElementsByName("bank_kouza_num")[0].value;
       orders_session('bank_kouza_num',payment_value);
     });
   }
   if (document.getElementsByName("bank_kouza_name")[0]) {
     $("input[name='bank_kouza_name']").blur(function(){
       var payment_value = document.getElementsByName("bank_kouza_name")[0].value;
       orders_session('bank_kouza_name',payment_value);
     });
   }
   if (document.getElementsByName("rak_tel")[0]) {
     $("input[name='rak_tel']").blur(function(){
       var payment_value = document.getElementsByName("rak_tel")[0].value;
       orders_session('rak_tel',payment_value);
     });
   }
EOT;
   echo "\n";
  }
/*--------------------------------
 功能：获取后台购买支付方法
 参数：$mailoption(string) 邮件选项
 参数：$comment_arr(string) 评论数组
 返回值：无
 -------------------------------*/
  function admin_get_payment_buying(&$mailoption,$comment_arr){

    $mailoption['BANK_NAME']        = $comment_arr['payment_bank_info']['bank_name'];      
    $mailoption['BRANCH_NAME']      = $comment_arr['payment_bank_info']['bank_shiten'] ;   
    $mailoption['TYPE_OF_ACCOUNT']      = $comment_arr['payment_bank_info']['bank_kamoku'];    
    $mailoption['ACCOUNT_NUMBER']   = $comment_arr['payment_bank_info']['bank_kouza_num'] ;
    $mailoption['ACCOUNT_HOLDERS_NAME']  = $comment_arr['payment_bank_info']['bank_kouza_name'];
    $mailoption['ADD_INFO']         = $comment_arr['add_info'];
  }
/*-------------------------------
 功能：获取后台购买支付方法类型
 参数：buying_type(string) 购买方法类型
 返回值：判断获取类型是否成功(boolean)
 ------------------------------*/
  function admin_get_payment_buying_type($buying_type){

    if($buying_type == TS_TEXT_BANK_KAMOKU){

      return true;
    } 

    return false;
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
/*-----------------------------
 功能：后台获得取点 
 参数：无
 返回值：点值(string)
 ----------------------------*/  
  function admin_get_fetch_point(){
    return 0;
  }
/*---------------------------
 功能：获取后台支付方法信息
 参数：$payment_info(string) 支付信息
 返回值：支付银行信息(string)
 --------------------------*/
  function admin_get_payment_info($payment_info){
    global $_POST;
    $bank_name = $_POST['bank_name'];
    $bank_shiten = $_POST['bank_shiten'];
    $bank_kamoku =$_POST['bank_kamoku'];
    $bank_kouza_num = $_POST['bank_kouza_num'];
    $bank_kouza_name = $_POST['bank_kouza_name'];
    $bank_info_array = array($bank_name,$bank_shiten,$bank_kamoku,$bank_kouza_num,$bank_kouza_name);
    $bank_info = implode('<<<|||',$bank_info_array);
    return "bank_info = '{$bank_info}',";
  }
/*----------------------------
 功能：获取后台支付方法信息评论 
 参数：$customers_email(string) 顾客的邮件
 参数：$site_id(string) SITE_ID值
 参数：$orders_type(string) 订单类型
 参数：$gray_single(int) 信息
 返回值：支付方法的订单ID(string) 
 ---------------------------*/
  function admin_get_payment_info_comment($customers_email,$site_id,$orders_type,$gray_single){

    $orders_type_str = $orders_type == 1 ? TABLE_ORDERS : TABLE_PREORDERS;
    $exists_single = false; 
    $customers_info_raw = tep_db_query("select c.*, ci.customers_info_date_account_created from ".TABLE_CUSTOMERS." c, ".TABLE_CUSTOMERS_INFO." ci where c.customers_id = ci.customers_info_id and c.site_id = '".$site_id."' and c.customers_email_address = '".$customers_email."'"); 
    $customers_info = tep_db_fetch_array($customers_info_raw);
    if ($gray_single == 0 || $gray_single == 2) {
      if ($customers_info) {
        $exists_single = true; 
      } 
    }
    if ($exists_single) {
      $orders_status_history_temp_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_BUYING_TEXT_TITLE."' and is_gray != '1' and is_guest = '0' and date_purchased >= '".$customers_info['customers_info_date_account_created']."' limit 0,1");
    } else {
      $orders_status_history_temp_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_BUYING_TEXT_TITLE."' and is_gray != '1' and is_guest = '0' limit 0,1");
    }
    $orders_num_rows = tep_db_num_rows($orders_status_history_temp_query);
    tep_db_free_result($orders_status_history_temp_query);
    if($orders_num_rows > 0){
      if ($exists_single) {
        $orders_status_history_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_BUYING_TEXT_TITLE."' and is_gray != '1' and is_guest = '0' and date_purchased >= '".$customers_info['customers_info_date_account_created']."' order by orders_id desc limit 0,1");
      } else {
        $orders_status_history_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_BUYING_TEXT_TITLE."' and is_gray != '1' and is_guest = '0' order by orders_id desc limit 0,1");
      }
      $ordres_status_history_array = tep_db_fetch_array($orders_status_history_query);
      $orders_status_history_num_rows = tep_db_num_rows($orders_status_history_query);
      tep_db_free_result($orders_status_history_query);
    }
    $orders_id = $orders_status_history_num_rows == 1 ? $ordres_status_history_array['orders_id'] : '';
    
    if ($customers_info) {
      if ($customers_info['customers_guest_chk'] == '1') {
        return array(0,'');
      }
    } else {
      return array(0,'');
    }
    
    return array(0,$orders_id);
  }
/*---------------------------
 功能：得到点值
 参数：无
 返回值：得到点值(boolean)
 --------------------------*/  
  function is_get_point()
  {
    return true; 
  }
/*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYING_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYING_POINT_RATE', (int)$site_id); 
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
    return MODULE_PAYMENT_BUYING_POINT_RATE; 
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
/*---------------------------
 功能：是否显示信息 
 参数：$payment(string) 支付方法
 返回值：是否显示(boolean)
 --------------------------*/
  function admin_is_show_info()
  {
    return true;
  }
}
?>
