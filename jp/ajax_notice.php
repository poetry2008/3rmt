<?php
require('includes/application_top.php');

if ($_GET['action'] == 'process') {
  if (isset($_SESSION['reset_customers_id'])) {
    $reset_customer_id = $_SESSION['reset_customers_id']; 
    $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$reset_customer_id."' and site_id = '".SITE_ID."'"); 
    $customers = tep_db_fetch_array($customers_raw); 
    if ($customers) {
        if ($customers['customers_guest_chk'] == '0') {
          $random_str = md5(time().$customers['customers_id'].$customers['customers_email_address']); 
          
          $send_url = HTTP_SERVER.'/password_token.php?pud='.$random_str;
          $exists_password_raw = tep_db_query("select customers_id from customers_password_info where customers_id = '".$customers['customers_id']."'"); 
          if (tep_db_num_rows($exists_password_raw)) {
            tep_db_query("update `customers_password_info` set `customers_email` = '".$customers['customers_email_address']."', `customers_ip` = '".$_SERVER["REMOTE_ADDR"]."', `random_num` = '".$random_str."', `created_at` = '".date('Y-m-d H:i:s',time())."', `is_update` = '0' where `customers_id` = '".$customers['customers_id']."'"); 
          } else {
            tep_db_query("insert into `customers_password_info` values('".$customers['customers_id']."', '".$customers['customers_email_address']."', '".$_SERVER["REMOTE_ADDR"]."', '".$random_str."', '".date('Y-m-d H:i:s',time())."', '0')");
          }

          //密码重置邮件
          $password_mail_array = tep_get_mail_templates('SEND_PASSWORLD_POPUP_EMAIL_CONTENT',SITE_ID); 
          $email_body = $password_mail_array['contents'];
          $email_body = str_replace('${PASSWORD_RESET_URL}', $send_url, $email_body);
          $email_body = str_replace('${SITE_NAME}', STORE_NAME, $email_body);
          $email_body = str_replace('${SITE_URL}', HTTP_SERVER, $email_body);
          $email_body = str_replace('${IP}', $_SERVER["REMOTE_ADDR"], $email_body);
          $email_body = str_replace('${USER_NAME}', tep_get_fullname($customers['customers_firstname'], $customers['customers_lastname']), $email_body);
          $email_body = tep_replace_mail_templates($email_body,$customers['customers_email_address'],tep_get_fullname($customers['customers_firstname'],$customers['customers_lastname']));
          tep_mail(tep_get_fullname($customers['customers_firstname'],$customers['customers_lastname']), $customers['customers_email_address'], str_replace('${SITE_NAME}', STORE_NAME, $password_mail_array['title']), $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
          
    }
  }
  echo 'success';
} else if ($_GET['action'] == 'check_pre_op') {
  $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pre_pid']."'");
  $preorder_products = tep_db_fetch_array($preorder_products_raw);
  if (tep_pre_check_less_product_option($preorder_products['products_id'])) {
    $products_name = tep_get_products_name($preorder_products['products_id']); 
    if (!tep_not_null($products_name)) {
      $products_name = $preorder_products['products_name']; 
    }
    echo sprintf(mb_substr(NOTICE_LESS_PRODUCT_OPTION_TEXT,0,17,'utf-8'), $products_name)."\r\n".mb_substr(NOTICE_LESS_PRODUCT_OPTION_TEXT,-27,27,'utf-8');
    exit;
  }
  echo 'success';
} else if ($_GET['action'] == 'check_pre_products_op') {
  $op_info_array = array(); 
  if (!empty($op_info_str)) {
    $op_tmp_array = explode('<<<<<<', $op_info_str); 
    foreach ($op_tmp_array as $key => $value) {
      $tmp_value_array = explode('||||||', $value); 
      $op_info_array[$tmp_value_array[0]] = $tmp_value_array[1]; 
    }
  }
  if (tep_pre_check_less_product_option_by_products_info($op_info_array, $_POST['products_id_str'])) {
    $products_name = tep_get_products_name($_POST['products_id_str']); 
    if (!tep_not_null($products_name)) {
      $products_name = $preorder_products['products_name']; 
    }
    echo sprintf(mb_substr(NOTICE_LESS_PRODUCT_OPTION_TEXT,0,17,'utf-8'), $products_name)."\r\n".mb_substr(NOTICE_LESS_PRODUCT_OPTION_TEXT,-27,27,'utf-8');
    exit; 
  }
  echo 'success';
}

