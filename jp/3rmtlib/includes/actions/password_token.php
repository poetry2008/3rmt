<?php
  require(DIR_WS_LANGUAGES . $language . '/password_token.php');
  
  if (!isset($_GET['pud'])) {
    forward404(); 
  }
  $customers_raw = tep_db_query("select customers_id, created_at, is_update from customers_password_info where random_num='".$_GET['pud']."'"); 
  if (!tep_db_num_rows($customers_raw)) {
    forward404(); 
  }
  $customers_res = tep_db_fetch_array($customers_raw);
  
  $now_time = time();
  $pa_time = strtotime($customers_res['created_at']);
 
  if ($customers_res['is_update'] == '1') {
    forward404(); 
  }
  
  $customers_info_raw = tep_db_query("select site_id, customers_password from customers where customers_id = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
  if (!tep_db_num_rows($customers_info_raw)) {
    forward404(); 
  }
  
  $customers_info_res = tep_db_fetch_array($customers_info_raw);
  
  $error = false; 
  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    if (($_POST['u_password'] == '') || ($_POST['up_password'] == '')) {
      $error = true; 
      $error_msg = UPDATE_ENTRY_PASSWORD_WORNG; 
    }
    
    if ($_POST['u_password'] != $_POST['up_password']) {
      $error = true; 
      $error_msg = UPDATE_ENTRY_PASSWORD_IS_NOT_SAME; 
    }
    
    $passlen = strlen($_POST['u_password']);
    $pass_a_len = strlen($_POST['up_password']);
    if (($passlen < ENTRY_PASSWORD_MIN_LENGTH) || ($pass_a_len < ENTRY_PASSWORD_MIN_LENGTH)) {
      $error = true; 
      $error_msg = UPDATE_ENTRY_PASSWORD_LEN_SHORT; 
    }
   
    if (!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $_POST['u_password'])) {
      $error = true; 
      if (preg_match('/^[0-9]+$/', $_POST['u_password'])) {
        $error_msg = UPDATE_ENTRY_PASSWORD_IS_NUM; 
      } else if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['u_password'])) {
        $error_msg = UPDATE_ENTRY_PASSWORD_IS_ALPHA; 
      } else {
        $error_msg = UPDATE_ENTRY_PASSWORD_WORNG; 
      }
    }
    
    if (!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $_POST['up_password'])) {
      $error = true; 
      if (preg_match('/^[0-9]+$/', $_POST['up_password'])) {
        $error_msg = UPDATE_ENTRY_PASSWORD_IS_NUM; 
      } else if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['up_password'])) {
        $error_msg = UPDATE_ENTRY_PASSWORD_IS_ALPHA; 
      } else {
        $error_msg = UPDATE_ENTRY_PASSWORD_WORNG; 
      }
    }
    
    
    if (!$error) {
      $crypted_password = tep_encrypt_password($_POST['u_password']);
      
      if (!tep_validate_password($_POST['u_password'], $customers_info_res['customers_password'])) {
        tep_db_query("
              update `" . TABLE_CUSTOMERS . "` 
              set `customers_password` = '" . $crypted_password . "', `reset_success` = '1'  
              where `customers_id` = '" . $customers_res['customers_id'] . "'
        ");
        
        tep_db_query("update `" . TABLE_CUSTOMERS_INFO . "` set `customer_last_resetpwd` = '".date('Y-m-d H:i:s', time())."' where `customers_info_id` = '" . tep_db_input($customers_res['customers_id']) . "'");
     
        tep_db_query("update `customers_password_info` set `is_update` = '1' where `customers_id` = '".$customers_res['customers_id']."'");
        
        unset($_SESSION['reset_flag']);
        
        tep_redirect(tep_href_link('password_success.php'));
      } else {
        $error = true; 
        $error_msg = UPDATE_ENTRY_NO_USE_OLD_PASSWORD; 
      }
    }
  }
?>
