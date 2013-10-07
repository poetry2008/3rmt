<?php
/*
  $Id$
*/
    require(DIR_WS_CLASSES . 'payment.php');
    $payment_modules = payment::getInstance(SITE_ID);
    $error = false;

    //处理表单数据  
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['quantity'])) {
      $quantity_error = true;
      $error = true;
    } else {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !is_numeric(tep_an_zen_to_han($_POST['quantity']))) {
        $quantity_error = true;
        $error = true;
      } else {
       if (isset($_GET['action']) && ($_GET['action'] == 'process') && (tep_an_zen_to_han($_POST['quantity']) <= 0)) {
        $quantity_error = true;
        $error = true;
       } else {
        $quantity_error = false;
       }
      }
    }
     
    if (tep_session_is_registered('customer_id')) {
      $from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
      $from_email_address = $account_values['customers_email_address'];
    } else {
if (!isset($_POST['firstname'])) $_POST['firstname'] = NULL; //del notice
if (!isset($_POST['lastname'])) $_POST['lastname'] = NULL; //del notice
if (!isset($_POST['from'])) $_POST['from'] = NULL; //del notice
      $first_name = $_POST['firstname'];
      $last_name = $_POST['lastname'];
      $from_name = tep_get_fullname($_POST['firstname'], $_POST['lastname']); 
      $from_email_address = $_POST['from'];
    }
    
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
        $fromemail_error = true;
        $error = true;
        } else {
          $fromemail_error = false;
        }
      }
    
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($last_name)) {
        $lastname_error = true;
        $error = true;
      } else {
        $lasttname_error = false;
      }
      
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($first_name)) {
        $firstname_error = true;
        $error = true;
      } else {
        $firstname_error = false;
      }
    } 
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['pre_payment'])) {
      $payment_error = true;
      $error = true;
    } else {
      $payment_error = false;
    }
     
    if (!empty($_POST['pre_payment'])) {
      $sn_type = $payment_modules->preorder_confirmation_check($_POST['pre_payment']); 
      if ($sn_type) {
        $sn_error_info = $payment_modules->get_preorder_error($_POST['pre_payment'], $sn_type); 
        $error = true; 
        $payment_error = true;
        $payment_error_str = $sn_error_info; 
      } else {
        $payment_error = false;
      }
    }

    //生成预约订单 
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) { 
      unset($_SESSION['submit_flag']); 
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']); 
      $preorder_id = date('Ymd').'-'.date('His').tep_get_preorder_end_num(); 
      $redirect_single = 0; 
      $max_op_len = 0;
      $max_op_array = array();
      $mail_option_str = '';
      foreach ($_POST as $mo_key => $mo_value) {
        $m_op_str = substr($mo_key, 0, 3);
        if ($m_op_str == 'op_') {
          $m_op_info = explode('_', $mo_key); 
          $item_m_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$m_op_info['1']."' and id = '".$m_op_info[3]."'"); 
          $item_m_res = tep_db_fetch_array($item_m_raw);
          if ($item_m_res) {
            $max_op_array[] = mb_strlen($item_m_res['front_title'], 'utf-8'); 
          }
        }
      }
      
      if (!empty($max_op_array)) {
        $max_op_len = max($max_op_array);
      }
      foreach ($_POST as $mao_key => $mao_value) {
        $ma_op_str = substr($mao_key, 0, 3);
        if ($ma_op_str == 'op_') {
          $ma_op_info = explode('_', $mao_key); 
          $item_f_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$ma_op_info['1']."' and id = '".$ma_op_info[3]."'"); 
          $item_f_res = tep_db_fetch_array($item_f_raw);
          if ($item_f_res) {
            $mail_option_str .= $item_f_res['front_title'].str_repeat('　', intval($max_op_len - mb_strlen($item_f_res['front_title'], 'utf-8'))).'：'.str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", stripslashes($mao_value))."\n"; 
          }
        }
      }
      
      if (tep_session_is_registered('customer_id')) {
          //预约完成邮件认证
          $preorders_mail_array = tep_get_mail_templates('PREORDER_MAIL_CONTENT',SITE_ID);
          $preorder_email_text = $preorders_mail_array['contents']; 
          
          $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAYMENT}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_NUMBER}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}'); 
        
          $payment_name_class = new $_POST['pre_payment'];
          $payment_name_str = $payment_name_class->title;
          
          $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'].NUM_UNIT_TEXT.' '.tep_get_full_count2($_POST['quantity'],$_POST['products_id']), $payment_name_str, tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage'], $mail_option_str);
          
          $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
          
          $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, $preorders_mail_array['title']); 
          $preorder_email_text = tep_replace_mail_templates($preorder_email_text,$account_values['customers_email_address'],tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
          if ($account_values['is_send_mail'] != '1') {
            tep_mail(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), $account_values['customers_email_address'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
            tep_mail('', SENTMAIL_ADDRESS, $preorder_email_subject, $preorder_email_text, tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), $account_values['customers_email_address']); 
          }
      } else {
        $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".$_POST['from']."' and site_id = '".SITE_ID."'");    
        if (tep_db_num_rows($exists_customer_raw)) {
          $exists_customer_res = tep_db_fetch_array($exists_customer_raw); 
          if ($exists_customer_res['is_active'] == 0) {
            $redirect_single = 1; 
            $tmp_customer_id = $exists_customer_res['customers_id']; 
            $encode_param_str = md5(time().$exists_customer_res['customers_id'].$_POST['from']); 
            $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$encode_param_str; 
            $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
            $new_str_array = array(
                $active_url, 
                $from_name, 
                STORE_NAME,
                HTTP_SERVER
                ); 
            //预约邮件认证
            $preorder_mail_array = tep_get_mail_templates('PREORDER_MAIL_ACTIVE_CONTENT',SITE_ID); 
            $preorder_email_text = str_replace($old_str_array, $new_str_array, $preorder_mail_array['contents']); 
            $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, $preorder_mail_array['title']); 
            $unactive_customers_single = true; 
            $send_to_owner = true;  
            tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$encode_param_str."' where customers_id = '".$exists_customer_res['customers_id']."'");  
          } else {
            //预约完成邮件认证
            $preorders_mail_array = tep_get_mail_templates('PREORDER_MAIL_CONTENT',SITE_ID);
            $preorder_email_text = $preorders_mail_array['contents']; 
            
            $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAYMENT}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_NUMBER}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}'); 
            
            $payment_name_class = new $_POST['pre_payment'];
            $payment_name_str = $payment_name_class->title;
              
            $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'].NUM_UNIT_TEXT.' '.tep_get_full_count2($_POST['quantity'],$_POST['products_id']), $payment_name_str, $from_name, STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage'], $mail_option_str);
            
            $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
            
            $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, $preorders_mail_array['title']); 
            $exists_email_single = true;     
          
            if ($exists_customer_res['is_send_mail'] == '1') {
              $c_is_send_mail = true; 
            }
          }
        } else {
          $tmp_customer_id = tep_create_tmp_guest($_POST['from'], $_POST['lastname'], $_POST['firstname']); 
          $redirect_single = 1; 
          $send_to_owner = true;  
          $encode_param_str = md5(time().$tmp_customer_id.$_POST['from']); 
          $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$encode_param_str; 
          
          $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
          $new_str_array = array(
              $active_url, 
              $from_name, 
              STORE_NAME,
              HTTP_SERVER
              ); 
          //预约邮件认证
          $preorder_mail_array = tep_get_mail_templates('PREORDER_MAIL_ACTIVE_CONTENT',SITE_ID);
          $preorder_email_text = str_replace($old_str_array, $new_str_array, $preorder_mail_array['contents']); 
          $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, $preorder_mail_array['title']); 
          tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$encode_param_str."' where customers_id = '".$tmp_customer_id."'");  
        }
        $preorder_email_text = tep_replace_mail_templates($preorder_email_text,$_POST['from'],$from_name); 
        if (!isset($c_is_send_mail)) {
          tep_mail($from_name, $_POST['from'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
        }
        if (isset($send_to_owner)) {
          tep_mail('', SENTMAIL_ADDRESS, $preorder_email_subject, $preorder_email_text, $from_name, $_POST['from']); 
        }
      }
      
      $send_preorder_id = $preorder_id;
      tep_session_register('send_preorder_id');
      if (isset($exists_email_single)) {
        tep_create_preorder_info($_POST, $preorder_id, $exists_customer_res['customers_id'], $tmp_customer_id, true); 
      } else {
        if (isset($unactive_customers_single)) {
          tep_create_preorder_info($_POST, $preorder_id, $customer_id, $tmp_customer_id, true); 
        } else {
          tep_create_preorder_info($_POST, $preorder_id, $customer_id, $tmp_customer_id); 
        }
      }
      if (!$redirect_single) {
        //unset session
        unset($_SESSION['preorder_products_list']);
        unset($_SESSION['submit_flag']);
        if(tep_session_is_registered('customer_id')){
          tep_redirect(tep_href_link(FILENAME_PREORDER_SUCCESS));
        }else{
          tep_redirect(tep_href_link(FILENAME_PREORDER_SUCCESS,'from='.$_POST['from'])); 
        }
      } else {
        tep_redirect(tep_href_link('non-preorder_auth.php'));
      }
    }
?>
