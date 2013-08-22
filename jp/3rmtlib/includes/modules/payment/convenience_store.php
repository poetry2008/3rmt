<?php
/*
  $Id$
*/

// 货到付款（手续费与购买价格连动）
require_once (DIR_WS_CLASSES . 'basePayment.php');
class convenience_store extends basePayment  implements paymentInterface  { 
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer, $show_payment_info;
/*------------------------------
 功能：加载便利店结算
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  function loadSpecialSettings($site_id = 0){
    $this->site_id = $site_id;
    $this->code        = 'convenience_store';
    $this->footer      = TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER;
    $this->show_payment_info = 0;

  }
/*----------------------------
 功能：编辑便利店结算
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：返回便利店结算类型数据(array)
 ---------------------------*/
  function fields($theData=false, $back=false){
    if ($back) {
    return array(
                 array(
                       "code"=>'con_email',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                       "field"=>tep_draw_input_field('con_email', (isset($theData['con_email'])?$theData['con_email']:((isset($_GET['Customer_mail'])?$_GET['Customer_mail']:''))), 'size="45"'),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_EMAIL),
                       )
     
                ); 
    } else {
    global $order;
    $total_cost = $order->info['total'];
    $point_value = $_SESSION['h_point'] == '' ? 0 : (int)$_SESSION['h_point'];
    $shipping_fee = $_SESSION['h_shipping_fee'] == '' ? 0 : (int)$_SESSION['h_shipping_fee'];
    $f_result = $this->calc_fee($total_cost-$point_value+$shipping_fee);
    $added_hidden = tep_draw_hidden_field('code_fee', $f_result); 
    $input_text_id = ' class="input_text" ';
    if(NEW_STYLE_WEB===true){
      $style_width = 'style="width:231px"'; 
    }
    $default_value = ''; 
    $default_again_value = ''; 
    if (isset($_SESSION['customer_emailaddress'])) {
      $default_value = $_SESSION['customer_emailaddress']; 
      $default_again_value = $_SESSION['customer_emailaddress']; 
    } else {
      if (isset($theData['convenience_email'])) {
        $default_value = $theData['convenience_email']; 
      } else {
        if (isset($theData['from'])) {
          $default_value = $theData['from']; 
        }
      }
      
      if (isset($theData['convenience_email_again'])) {
        $default_again_value = $theData['convenience_email_again']; 
      } else {
        if (isset($theData['from'])) {
          $default_again_value = $theData['from']; 
        }
      }
    }
    return array(
                 array(
                       "code"=>'convenience_email',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                       "field"=>tep_draw_input_field('convenience_email', $default_value,'onpaste="return false"'.$style_width.$input_text_id.'').'&nbsp;&nbsp;'.TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT, 
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_EMAIL),
                       "error_msg" => array(TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE, TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE)),
                 array(
                       "code"=>'convenience_email_again',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT,
                       "field"=>tep_draw_input_field('convenience_email_again', $default_again_value,'onpaste="return false"'.$style_width.$input_text_id.'').'&nbsp;&nbsp;'.TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT,
                       "rule"=>array(basePayment::RULE_NOT_NULL, basePayment::RULE_SAME_TO,basePayment::RULE_EMAIL),
                       "params_code"=>'convenience_email',
                       "error_msg" => array(TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE, TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE, TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE) 
                       ),
                 array(
                       "code"=>'',
                       "title"=>'',
                       "field"=>$added_hidden,
                       "rule"=>'',
                       ),
                 );
    }
  }
  // class methods
/*-----------------------------
 功能：JS验证
 参数：无
 返回值：JS验证是否成功(boolean)
 ----------------------------*/
  function javascript_validation() {
    return false;
  }
/*---------------------------------
 功能： 检查后台便利店结算电子邮件
 参数：无
 返回值：判断是否检查成功(boolean)
 ---------------------------------*/
  function checkAdminSelection(){
    if(isset($_POST['con_email']) and !empty($_POST['con_email'])){
      return true;
    }else {
      return false;
    }
  }
/*--------------------------------
 功能：选择后台便利店
 参数：无
 返回值：便利店信息(array)
 -------------------------------*/
    function adminSelection()
    {
      return array(
                   'code'=>$this->code,
                   'fields'=>array(
                                   array(
                                         "title"=>"EMAIL",
                                         "field"=>"<input type='text' name='con_email' />",
                                         "message"=>"",
                                         )));
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
      
      if ($_POST['convenience_email'] == "" || $_POST['convenience_email_again'] == "") {
        return 3; 
      } else if
          (!ereg("^([^@])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$_POST['convenience_email'])
           || !ereg("^([^@])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$_POST['convenience_email_again'])){
        return 3; 
      } else if ($_POST['convenience_email'] != $_POST['convenience_email_again']) {
        return 1; 
      } else {
        $pc_email_single = false;
        $pc_email_again_single = false;

        $pc_pos = strrpos($_POST['convenience_email'], '@');
        $pc_new_email = substr($_POST['convenience_email'], $pc_pos+1);
        if (preg_match('/^(docomo\.|softbank\.|i\.softbank\.|disney\.|ezweb\.|vodafone\.|.*\.vodafone\.|biz\.ezweb\.|.*biz\.ezweb\.|ezweb\.|sky\.ttk\.|sky\.tkc\.|sky\.tu\-ka\.|pdx\.|emnet\.)(.*)$/i', $pc_new_email)) {
          $pc_email_single = true; 
        }
        
        $pc_apos = strrpos($_POST['convenience_email_again'], '@');
        $pc_anew_email = substr($_POST['convenience_email_again'], $pc_apos+1);
        if (preg_match('/^(docomo\.|softbank\.|i\.softbank\.|disney\.|ezweb\.|vodafone\.|.*\.vodafone\.|biz\.ezweb\.|.*biz\.ezweb\.|ezweb\.|sky\.ttk\.|sky\.tkc\.|sky\.tu\-ka\.|pdx\.|emnet\.)(.*)$/i', $pc_anew_email)) {
          $pc_email_again_single = true; 
        }
        
        if (!$pc_email_single && !$pc_email_again_single) {
          return 0; 
        } else {
          return 2; 
        }
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

      $s_result = !$_POST['code_fee_error'];
      
      $s_message = $s_result ? '' : ('<font color="#FF0000">' . $_POST['code_fee_error'] . '</font>');
      return array(
                   'title' => str_replace("#USER_MAIL#",$_SESSION['h_convenience_email'],nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION"))),
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
      global $currencies;
      global $_POST;
      global $order, $point;


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
    
      if(MODULE_ORDER_TOTAL_CONV_STATUS == 'true' && ($payment == 'convenience_store')) {
        $total += intval($_SESSION['h_code_fee']);
      }

    
      // email_footer 里使用的字符串
      $s_message = $_POST['codt_fee_error']
        ? $_POST['codt_fee_error']
        : sprintf(MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER,
                  $currencies->format($total),
                  $currencies->format($_SESSION['h_code_fee']));

      return tep_draw_hidden_field('codt_message', $s_message)
        . tep_draw_hidden_field('convenience_email', $_SESSION['h_convenience_email']) 
        . tep_draw_hidden_field('code_fee',$_SESSION['h_code_fee']); // for ot_codt
    }
/*------------------------------
 功能：便利店结算前 
 参数：无
 返回值：无
 -----------------------------*/
    function before_process() {
      global $_POST;

      $this->email_footer = $_POST['codt_message'];
    }
/*------------------------------
 功能：便利店结算后
 参数：无
 参数：无
 -----------------------------*/
    function after_process() {
      return false;
    }
/*----------------------
 功能：获取错误
 参数：无
 返回值：错误信息(boolean/array)
 ----------------------*/
    function get_error() {
      global $_POST,$_GET;

      if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
        if (isset($_GET['type']) && $_GET['type'] == 'noe')
          {
            $error_message = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE;
          }
        else if (isset($_GET['type']) && $_GET['type'] == 'nom')
          {
            $error_message = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOM;
          }
        else
          {
            $error_message = MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE;
          }
        
        return array('title' => $this->title.' エラー!',
                     'error' => $error_message);
    
      }else{
        return false;
      }
    }
 /*----------------------
 功能：获取预约错误
 参数：$error_type(string) 错误类型
 返回值：错误信息(string)
 ---------------------*/
    function get_preorder_error($error_type) {
      if ($error_type == 1)
        {
          $error_message = TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE;
        }
      else if ($error_type == 2)
        {
          $error_message = TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOM;
        }
      else
        {
          $error_message = TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE;
        }
      return $error_message; 
    }
/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS' and site_id = '".$this->site_id."'");
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
        site_id) values ('コンビニ決済を有効にする',
          'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS', 'True',
          'コンビニ決済による支払いを受け付けますか?', '6', '1',
          'tep_cfg_select_option(array(\'True\', \'False\'),
            ',now(),'".$_SESSION['user_name']."', ".$this->site_id.");");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('加盟店コード', 'MODULE_PAYMENT_CONVENIENCE_STORE_IP', '', '加盟店コードの設定をします。', '6', '2', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('接続URL', 'MODULE_PAYMENT_CONVENIENCE_STORE_URL', '', '接続URLの設定をします。', '6', '6', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_CONVENIENCE_STORE_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0' , now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_CONVENIENCE_STORE_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '4', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_CONVENIENCE_STORE_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ',now(), ".$this->site_id.");");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW', 'True', '予約注文でコンビニ決済を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_CONVENIENCE_STORE_IS_GET_POINT', 'True', 'ポイント', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_CONVENIENCE_STORE_POINT_RATE', '0.01', 'ポイント還元率', '6', '0' , now(), ".$this->site_id.")");
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
 功能：编辑便利店结算方法
 参数：无
 返回值：便利店结算方法数据(array)
 ----------------------------*/
    function keys() {
      return array( 
                   'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_IS_GET_POINT',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_POINT_RATE',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_IP',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_URL',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_COST',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_ZONE',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID' ,
                   'MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_STATUS_ID' ,
                   'MODULE_PAYMENT_CONVENIENCE_STORE_MONEY_LIMIT',
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
      if($_POST['convenience_email']){ 
      $pay_comments = 'PCメールアドレス:'.str_replace("\xe2\x80\x8b",'',$_POST['convenience_email']); 
      }else if($_POST['con_email']){
      $pay_comments = 'PCメールアドレス:'.str_replace("\xe2\x80\x8b",'',$_POST['con_email']); 
      }else{
      $pay_comments = 'PCメールアドレス:'; 
      }
      $payment_bank_info['add_info'] = $pay_comments;
      $res_arr = array('comment'=> $pay_comments ."\n".$comment,
          'payment_bank_info' => $payment_bank_info, 'payment_info' => $pay_comments);
      return $res_arr;
    }
/*---------------------------
 功能：便利店预约按钮
 参数：$pid(string) 预约ID
 参数：$preorder_total(string) 预约总额
 返回值：无
 --------------------------*/
    function preorder_process_button($pid, $preorder_total)
    {
    
    }
/*--------------------------------
 功能：处理预约额外的评论
 参数：$pInfo(string) 银行数组输出
 参数：$sql_data_array(string) SQL数据
 返回值：评论信息(string)
 -------------------------------*/  
    function deal_preorder_additional($pInfo, &$sql_data_array)
    {
      $pay_comments = 'PCメールアドレス:'.$pInfo['convenience_email']; 
      $sql_data_array['cemail_text'] = $pay_comments; 
       
      return $pay_comments ."\n".$pInfo['yourmessage'];
    } 
/*-------------------------------
 功能：检查预约的电子邮箱
 参数：$email(string) 电子邮箱
 返回值：检查电子邮箱是否正确(boolean)
 ------------------------------*/
    function checkPreorderConvEmail($email)
    {
      if (!empty($email)) {
        return true; 
      }
      return false; 
    }
/*-----------------------------------
 功能：后台添加信息 
 参数：$sql_data_array(string) SQL数据
 返回值：无
 ----------------------------------*/  
    function admin_add_additional_info(&$sql_data_array)
    {
      global $_POST; 
      $sql_data_array['cemail_text'] = 'PCメールアドレス:'.$_POST['con_email']; 
    }
/*---------------------------------
 功能：后台处理评论信息
 参数：$order_info(string) 订单信息
 返回值：评论信息(string)
 --------------------------------*/  
    function admin_deal_comment($order_info)
    {
      return $order_info['cemail_text']; 
    }
/*--------------------------------
 功能：处理其他信息 
 参数：$pInfo(string) 便利店数组
 返回值：无
 -------------------------------*/
    function deal_other_info($pInfo)
    {
      $_SESSION['h_convenience_email'] = $pInfo['convenience_email']; 
    }
/*-----------------------------
 功能：获取预约添加信息 
 参数：$order_info(string) 订单信息
 返回值：预约添加信息(string)
 ----------------------------*/
 function get_preorder_add_info($order_info)
    {
      $_SESSION['preorder_payment_info'] = $order_info['cemail_text']; 
      return $order_info['cemail_text'] ."\n".$order_info['comment_msg'];
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
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_CONVENIENCE_STORE_NORMAL || $bank_kamoku[1] == ''){
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
    
/*------------------------------
 功能：获取后台顾客点值 
 参数：$point_value(string) 点值
 参数：$customer_id(string) 顾客ID
 返回值：无
 -----------------------------*/
  function admin_get_customer_point($point_value,$customer_id){
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value .  " where customers_id = '" .$customer_id."' and customers_guest_chk = '0' ");
  } 

/*---------------------------
 功能：获取后台支付方法信息
 参数：$payment_info(string) 支付信息
 返回值：支付银行信息(string)
 --------------------------*/
  function admin_get_payment_info($payment_info){
    $cemail_text = $payment_info;  
    return "cemail_text = '{$cemail_text}',";
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
      $orders_status_history_temp_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE."' and is_gray != '1' and date_purchased >= '".$customers_info['customers_info_date_account_created']."' limit 0,1");
    } else {
      $orders_status_history_temp_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE."' and is_gray != '1' limit 0,1");
    }
    $orders_num_rows = tep_db_num_rows($orders_status_history_temp_query);
    tep_db_free_result($orders_status_history_temp_query);
    if($orders_num_rows > 0){
      if ($exists_single) {
        $orders_status_history_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE."' and is_gray != '1' and date_purchased >= '".$customers_info['customers_info_date_account_created']."' order by orders_id desc limit 0,1");
      } else {
        $orders_status_history_query = tep_db_query("select payment_method,orders_id from ". $orders_type_str ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE."' and is_gray != '1' order by orders_id desc limit 0,1");
      }
      $ordres_status_history_array = tep_db_fetch_array($orders_status_history_query);
      $orders_status_history_num_rows = tep_db_num_rows($orders_status_history_query);
      tep_db_free_result($orders_status_history_query);
    }
    $orders_id = $orders_status_history_num_rows == 1 ? $ordres_status_history_array['orders_id'] : '';
    return array(1,$orders_id);
  }
  
/*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_CONVENIENCE_STORE_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_CONVENIENCE_STORE_POINT_RATE', (int)$site_id); 
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
    return MODULE_PAYMENT_CONVENIENCE_STORE_POINT_RATE; 
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
