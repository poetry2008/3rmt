<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/m_token.php');
  
  $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where check_login_str = '".$_GET['aid']."' and site_id = '".SITE_ID."' and customers_guest_chk = '0'");
  $customers_res = tep_db_fetch_array($customers_raw);
  
  if ($customers_res) {
    $now_time = time(); 
    if (($now_time - $customers_res['send_mail_time']) > 60*60*24*3) {
      if ($customers_res['is_active'] == 0) {
        tep_db_query("delete from ".TABLE_CUSTOMERS." where customers_id = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$customers_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id = '".$customers_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$customers_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$customers_res['customers_id']."'");
      }
      
      tep_redirect(tep_href_link('account_timeout.php')); 
    } else {
      $email_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']); 
      
     $email_text = stripslashes($customers_res['customers_lastname'].' '.$customers_res['customers_firstname']).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
       
      $email_text .= C_CREAT_ACCOUNT;
      $email_text = str_replace(array('${MAIL}', '${PASS}'), array($customers_res['customers_email_address'], $customers_res['origin_password']), $email_text); 
      if ($customers_res['is_active'] == 0) {
        tep_mail($email_name, $customers_res['customers_email_address'], EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
      }
      tep_db_query("update ".TABLE_CUSTOMERS." set `is_active` = 1 where customers_id = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
      
      $address_book_raw = tep_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".$customers_res['customers_id']."'"); 
      $address_book_res = tep_db_fetch_array($address_book_raw); 
      $customer_id = $customers_res['customers_id'];  
      $customer_first_name = $customers_res['customers_firstname'];
      $customer_last_name = $customers_res['customers_lastname']; 
      $customer_default_address_id = 1;
      $customer_country_id = $address_book_res['entry_country_id'];
      $customer_zone_id = $address_book_res['entry_zone_id'];
      $guestchk = $customers_res['customers_guest_chk'];
      tep_session_register('customer_id');
      tep_session_register('customer_first_name');
      tep_session_register('customer_last_name');
      tep_session_register('customer_default_address_id');
      tep_session_register('customer_country_id');
      tep_session_register('customer_zone_id');
      $customer_emailaddress = $customers_res['customers_email_address'];
      tep_session_register('customer_emailaddress');
      tep_session_register('guestchk');
      $cart->restore_contents(); 
    }
    tep_redirect(tep_href_link('create_account_success.php', '', 'SSL')); 
  } else {
    tep_redirect(tep_href_link('account_timeout.php')); 
  }

?>
