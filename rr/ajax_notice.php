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
            tep_db_query("update `customers_password_info` set `customers_email` = '".$customers['customers_email_address']."', `customers_ip` = '".$_SERVER["REMOTE_ADDR"]."', `random_num` = '".$random_str."', `created_at` = '".date('Y-m-d H:i:s',time())."' where `customers_id` = '".$customers['customers_id']."'"); 
          } else {
            tep_db_query("insert into `customers_password_info` values('".$customers['customers_id']."', '".$customers['customers_email_address']."', '".$_SERVER["REMOTE_ADDR"]."', '".$random_str."', '".date('Y-m-d H:i:s',time())."')");
          }
          
          $email_body = SEND_PASSWORLD_EMAIL_CONTENT;
          $email_body = str_replace('${URL}', $send_url, $email_body);
          $email_body = str_replace('${SITE_NAME}', STORE_NAME, $email_body);
          $email_body = str_replace('${SITE_URL}', HTTP_SERVER, $email_body);
          $email_body = str_replace('${IP}', $_SERVER["REMOTE_ADDR"], $email_body);
          $email_body = str_replace('${NAME}', tep_get_fullname($customers['customers_firstname'], $customers['customers_lastname']), $email_body);
         
          tep_mail(tep_get_fullname($customers['customers_firstname'],$customers['customers_lastname']),
              $customers['customers_email_address'], str_replace('${SITE_NAME}', STORE_NAME, SEND_PASSWORLD_EMAIL_TITLE), $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
          
    }
  }
  echo 'success';
}

