<?php
/*
  $Id$
*/

// 代金引換払い(手数料が購入金額に連動)
require_once (DIR_WS_CLASSES . 'basePayment.php');
class convenience_store extends basePayment  implements paymentInterface  { 
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer, $show_payment_info;
  function loadSpecialSettings($site_id = 0){
    $this->site_id = $site_id;
    $this->code        = 'convenience_store';
    $this->footer      = TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER;
    $this->show_payment_info = 0;

  }
  function fields($theData=false, $back=false){
    if ($back) {
    return array(
                 array(
                       "code"=>'con_email',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                       "field"=>tep_draw_input_field('con_email', (isset($theData['con_email'])?$theData['con_email']:((isset($_GET['Customer_mail'])?$_GET['Customer_mail']:'')))),
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_EMAIL),
                       )
     
                ); 
    } else {
    global $order;
    $total_cost = $order->info['total'];
    $f_result = $this->calc_fee($total_cost);
    $added_hidden = tep_draw_hidden_field('code_fee', $f_result); 
     if(NEW_STYLE_WEB===true){
       $input_text_id = ' class="input_text" ';
     }else{
       $input_text_id = '';
     }
    return array(
                 array(
                       "code"=>'convenience_email',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                       "field"=>tep_draw_input_field('convenience_email', (isset($_SESSION['customer_emailaddress'])?$_SESSION['customer_emailaddress']:$theData['convenience_email']),'onpaste="return false"'.$input_text_id.'').TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT, 
                       "rule"=>array(basePayment::RULE_NOT_NULL,basePayment::RULE_EMAIL),
                       "error_msg" => array(TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE, TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE)),
                 array(
                       "code"=>'convenience_email_again',
                       "title"=>TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT,
                       "field"=>tep_draw_input_field('convenience_email_again', isset($_SESSION['customer_emailaddress'])?$_SESSION['customer_emailaddress']:$theData['convenience_email_again'],'onpaste="return false"'.$input_text_id.'').TS_MODULE_PAYMENT_CONVENIENCE_MUST_INPUT,
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
  function javascript_validation() {
    return false;
  }

  function checkAdminSelection(){
    if(isset($_POST['con_email']) and !empty($_POST['con_email'])){
      return true;
    }else {
      return false;
    }
  }
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
    /*
    function selection($theData) {
      global $currencies;
      global $order;

      $total_cost = $order->info['total'];      // 税金も含めた代金の総額
      $f_result = $this->calc_fee($total_cost); // 手数料

      $added_hidden = $f_result
        ? tep_draw_hidden_field('codt_fee', $this->n_fee).tep_draw_hidden_field('cod_total_cost', $total_cost)
        : tep_draw_hidden_field('codt_fee_error', $this->s_error);
      if (!empty($this->n_fee)) {
        $s_message = $f_result ? (MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE . '&nbsp;' . $currencies->format($this->n_fee)) : ('<font color="#FF0000">' . $this->s_error . '</font>');
      } else {
        $s_message = $f_result ? '': ('<font color="#FF0000">' . $this->s_error . '</font>');
      }
      //$s_message = $f_result ? (MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE . '&nbsp;' . $currencies->format($this->n_fee)) : ('<font color="#FF0000">' . $this->s_error . '</font>');
      $email_default_str = ''; 
      if (isset($_SESSION['customer_emailaddress'])) {
        $email_default_str = $_SESSION['customer_emailaddress']; 
      }
      $selection = array(
                         'id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS,
                                                 'field' => ''),
                                           array('title' => '<div class="rowHide rowHide_'.$this->code.'" id="cemail" style="display:none;">'.MODULE_PAYMENT_CONVENIENCE_INFO_TEXT.'<div
                              class="cemail_input_info"><div class="cemail_front_text">'
                                                 .MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT.'</div><div
                              class="con_email01">'.tep_draw_input_field('convenience_email',
                                                                         $email_default_str, 'onpaste="return
                                false"').'
                              '.MODULE_PAYMENT_CONVENIENCE_MUST_INPUT."</div></div><div
                              class='cemail_input_info'><div
                              class='cemail_front_text'>"
                                                 .MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT."</div><div
                              class='con_email02'>".tep_draw_input_field('convenience_email_again',
                                                                         $email_default_str, 'onpaste="return false"').'
                              '.MODULE_PAYMENT_CONVENIENCE_MUST_INPUT.'</div></div></div>', 
                                                 'field' => '' 
                                                 ), 
                                           array('title' => '<div class="rowHide rowHide_'.$this->code.'" id="caemail" style="display:none;"><div class="cemail_input_02">'.'<div class="con_email02">'.'</div></div><p>'.MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER.'</p>'. $added_hidden.'</div>',
                                                 'field' => '' 
                                                 ), 
                                           array('title' => $s_message,
                                                 'field' => '')
                                           )
                         );

      return $selection;
      }*/
    function pre_confirmation_check() {
      return true;
    }
    /*    function pre_confirmation_check() {
      if ($_POST['convenience_email'] == "" || $_POST['convenience_email_again'] == "") {
        $payment_error_return = 'payment_error=' . $this->code ;
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
       
      } else if
          (!ereg("^([^@])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$_POST['convenience_email'])
           || !ereg("^([^@])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$_POST['convenience_email_again'])){
        $payment_error_return = 'payment_error=' . $this->code ;
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      } else if ($_POST['convenience_email'] != $_POST['convenience_email_again']) {
        $payment_error_return = 'payment_error=' . $this->code; 
        $redirect_url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return . '&type=noe', 'SSL', true, false);
        //do for &type turn into &amp;type url ,fix it afterlater
        $url_test = explode('?',$redirect_url);
        if ($url_test[1] == 'payment_error=convenience_store&amp;type=noe')
          {
            $url_test[1] = 'payment_error=convenience_store&type=noe';
            $redirect_url = $url_test[0] .'?'. $url_test[1]; 
          }
        //do for &type turn into &amp;type url ,fix it afterlater
        //tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return . '&type=noe', 'SSL', true, false));
        tep_redirect($redirect_url);
      
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
          return false; 
        } else {
          $payment_error_return = 'payment_error=' . $this->code . '&type=nom' ;

          //do for &type turn into &amp;type url ,fix it afterlater
          $redirect_url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false);
          $url_test = explode('?',$redirect_url);
          if ($url_test[1] == 'payment_error=convenience_store&amp;type=nom')
            {
              $url_test[1] = 'payment_error=convenience_store&type=nom';
              $redirect_url = $url_test[0] .'?'. $url_test[1]; 
            }

          //tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
          tep_redirect($redirect_url);
          //do for &type turn into &amp;type url ,fix it afterlater
        }
      }
    }
    */
    
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
    
    function confirmation() {
      global $currencies;
      global $_POST;

      $s_result = !$_POST['code_fee_error'];
      
      if (!empty($_SESSION['h_code_fee'])) {
        //$s_message = $s_result ? (MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE . '&nbsp;' . $currencies->format($_POST['codt_fee'])) : ('<font color="#FF0000">' . $_POST['codt_fee_error'] . '</font>');
        $s_message = $s_result ? '' : ('<font color="#FF0000">' . $_POST['code_fee_error'] . '</font>');
      } else {
        $s_message = $s_result ? '' : ('<font color="#FF0000">' . $_POST['code_fee_error'] . '</font>');
      }
      return array(
                   'title' => str_replace("#USER_MAIL#",$_SESSION['h_convenience_email'],nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION"))),
                   'fields' => array(
                                     array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
                                     array('title' => $s_message, 'field' => '')  
                                     )           
                   );

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
    
      if(MODULE_ORDER_TOTAL_CONV_STATUS == 'true' && ($payment == 'convenience_store')) {
        $total += intval($_SESSION['h_code_fee']);
      }
      // 追加 - 2007.01.05 ----------------------------------------------
    
      // email_footer に使用する文字列
      $s_message = $_POST['codt_fee_error']
        ? $_POST['codt_fee_error']
        : sprintf(MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER,
                  $currencies->format($total),
                  $currencies->format($_SESSION['h_code_fee']));

      return tep_draw_hidden_field('codt_message', $s_message)
        . tep_draw_hidden_field('convenience_email', $_SESSION['h_convenience_email']) 
        . tep_draw_hidden_field('code_fee',$_SESSION['h_code_fee']); // for ot_codt
    }

    function before_process() {
      global $_POST;

      $this->email_footer = $_POST['codt_message'];
    }

    function after_process() {
      return false;
    }

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

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('コンビニ決済を有効にする', 'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS', 'True', 'コンビニ決済による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
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
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }


    function keys() {
      return array( 
                   'MODULE_PAYMENT_CONVENIENCE_STORE_STATUS',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_IP',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_URL',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_COST',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_SORT_ORDER',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_ZONE',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_ORDER_STATUS_ID' ,
                   'MODULE_PAYMENT_CONVENIENCE_STORE_MONEY_LIMIT',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_MAILSTRING',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_PRINT_MAILSTRING_TITLE',
                   'MODULE_PAYMENT_CONVENIENCE_STORE_PRINT_MAILSTRING',
                    );
    }


    function dealComment($comment, $session_paymentinfo_name)
    {
      if($_POST['convenience_email']){ 
      $pay_comments = 'PCメールアドレス:'.$_POST['convenience_email']; 
      }else if($_POST['con_email']){
      $pay_comments = 'PCメールアドレス:'.$_POST['con_email']; 
      }else{
      $pay_comments = 'PCメールアドレス:'; 
      }
      $payment_bank_info['add_info'] = $pay_comments;
      $res_arr = array('comment'=> $pay_comments ."\n".$comment,
          'payment_bank_info' => $payment_bank_info);
      return $res_arr;
    }

    function preorder_process_button($pid, $preorder_total)
    {
    
    }
    function deal_preorder_additional($pInfo, &$sql_data_array)
    {
      $pay_comments = 'PCメールアドレス:'.$pInfo['convenience_email']; 
      $sql_data_array['cemail_text'] = $pay_comments; 
       
      return $pay_comments ."\n".$pInfo['yourmessage'];
    } 
    function checkPreorderConvEmail($email)
    {
      if (!empty($email)) {
        return true; 
      }
      return false; 
    }

    function getMailString($option=''){
      $email_printing_order .= 'この注文は【販売】です。' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□
      返答済' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□
      無　→　入金確認後仕入' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .=
        '入金確認　　　　　●：＿＿月＿＿日　→　金額は' .
        abs($option) . '円ですか？　□ はい' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .= '入金確認メール送信　：□ 済' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .=
        '発送　　　　　　　　：＿＿月＿＿日' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .= '残量入力→誤差有無　：□
      無　　｜　　□ 有　→　報告　□' . "\n";
      $email_printing_order .=
        '------------------------------------------------------------------------'
        . "\n";
      $email_printing_order .= '発送完了メール送信　：□
      済' . "\n";    
      $email_printing_order .=
        '------------------------------------------------------------------------' . "\n";
      $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
      $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
      return $email_printing_order;
    }

    function admin_add_additional_info(&$sql_data_array)
    {
      global $_POST; 
      $sql_data_array['cemail_text'] = 'PCメールアドレス:'.$_POST['con_email']; 
    }
    
    function admin_deal_comment($order_info)
    {
      return $order_info['cemail_text']; 
    }

    function deal_other_info($pInfo)
    {
      $_SESSION['h_convenience_email'] = $pInfo['convenience_email']; 
    }
  
    function get_preorder_add_info($order_info)
    {
      return $order_info['cemail_text'] ."\n".$order_info['comment_msg'];
    }

  function admin_show_payment_list($pay_info_array){

   global $_POST;
   $pay_array = explode("\n",trim($pay_info_array[0]));
   $bank_name = explode(':',$pay_array[0]);
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo 'document.getElementsByName("bank_name")[0].value = "'. $bank_name[1] .'";'."\n"; 
   $bank_shiten = explode(':',$pay_array[1]); 
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo 'document.getElementsByName("bank_shiten")[0].value = "'. $bank_shiten[1] .'";'."\n"; 
   $bank_kamoku = explode(':',$pay_array[2]);
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_CONVENIENCE_STORE_NORMAL || $bank_kamoku[1] == ''){
     echo 'document.getElementsByName("bank_kamoku")[0].checked = true;'."\n"; 
   }else{
     echo 'document.getElementsByName("bank_kamoku")[1].checked = true;'."\n"; 
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo 'document.getElementsByName("bank_kouza_num")[0].value = "'.$bank_kouza_num[1].'";'."\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   $bank_kouza_name[1] = isset($_POST['bank_kouza_name']) ? $_POST['bank_kouza_name'] : $bank_kouza_name[1];
   echo 'document.getElementsByName("bank_kouza_name")[0].value = "'.$bank_kouza_name[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[1]));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_SESSION['orders_update_products']['con_email']) ? $_SESSION['orders_update_products']['con_email'] : $con_email[1];
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   echo 'document.getElementsByName("con_email")[0].value = "'.$con_email[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[2]));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo 'document.getElementsByName("rak_tel")[0].value = "'.$rak_tel[1].'";'."\n";
  }
    
  function admin_get_customer_point($point_value,$customer_id){
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value .  " where customers_id = '" .$customer_id."' and customers_guest_chk = '0' ");
  } 

  function admin_get_payment_info($payment_info){
    $cemail_text = $payment_info;  
    return "cemail_text = '{$cemail_text}',";
  }

  function admin_get_payment_info_comment($customers_email,$site_id){

    $orders_status_history_query = tep_db_query("select payment_method,orders_id from ". TABLE_ORDERS ." where customers_email_address='". $customers_email ."' and site_id='".$site_id."' and payment_method='".TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE."' order by orders_id desc limit 0,1");
    $ordres_status_history_array = tep_db_fetch_array($orders_status_history_query);
    $orders_status_history_num_rows = tep_db_num_rows($orders_status_history_query);
    tep_db_free_result($orders_status_history_query);
    $orders_id = $orders_status_history_num_rows == 1 ? $ordres_status_history_array['orders_id'] : '';
    return array(1,$orders_id);
  }
  }
  ?>
