<?php
/*
  $Id$
*/
if(isset($_POST['login_type']) && $_POST['login_type'] == 'new') {
  tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT,'email_address='.$_POST['email_address']));
}else{ 
  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    // tamura 2002/12/30 「全角」英数字を「半角」に変換
    $_POST['email_address'] = tep_an_zen_to_han($_POST['email_address']);

    $email_address = tep_db_prepare_input($_POST['email_address']);
    $password = tep_db_prepare_input($_POST['password']);

// Check if email exists
//ccdd
    $check_customer_query = tep_db_query("
        SELECT customers_id, 
               customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_email_address, 
               customers_default_address_id, 
               customers_guest_chk 
        FROM " . TABLE_CUSTOMERS .  " 
        WHERE customers_email_address = '" . tep_db_input($email_address) . "' 
          AND site_id = ".SITE_ID);

    if (!tep_db_num_rows($check_customer_query)) {
      $_GET['login'] = 'fail';
    } else {
      $check_customer = tep_db_fetch_array($check_customer_query);
      // Check that password is good
      if (!tep_validate_password($password, $check_customer['customers_password'])) {
        $_GET['login'] = 'fail';
      } else {
        if (SESSION_RECREATE == 'True') {
          tep_session_recreate();
        }
//ccdd
        $check_country_query = tep_db_query("
            SELECT entry_country_id, 
                   entry_zone_id 
            FROM " . TABLE_ADDRESS_BOOK . " 
            WHERE customers_id = '" . $check_customer['customers_id'] . "' 
              AND address_book_id = '1'
        ");
        $check_country = tep_db_fetch_array($check_country_query);

        $customer_id                 = $check_customer['customers_id'];
        $customer_default_address_id = $check_customer['customers_default_address_id'];
        $customer_first_name         = $check_customer['customers_firstname'];
        $customer_last_name          = $check_customer['customers_lastname']; // 2003.03.08 Add Japanese osCommerce
        $customer_country_id         = $check_country['entry_country_id'];
        $customer_zone_id            = $check_country['entry_zone_id'];
        $customer_emailaddress       = $email_address; 
        $guestchk                    = $check_customer['customers_guest_chk'];
        tep_session_register('customer_id');
        tep_session_register('customer_default_address_id');
        tep_session_register('customer_first_name');
        tep_session_register('customer_last_name'); // 2003.03.08 Add Japanese osCommerce
        tep_session_register('customer_country_id');
        tep_session_register('customer_zone_id');
        tep_session_register('customer_emailaddress');
        tep_session_register('guestchk');

        $date_now = date('Ymd');
//ccdd
        tep_db_query("
            UPDATE " . TABLE_CUSTOMERS_INFO . " 
            SET customers_info_date_of_last_logon = now(), 
                customers_info_number_of_logons   = customers_info_number_of_logons+1 
            WHERE customers_info_id = '" . $customer_id . "'
        ");
    
    //POINT_LIMIT CHECK ポイントの有効期限チェック ds-style
    if(MODULE_ORDER_TOTAL_POINT_LIMIT != '0') {
//ccdd
      $plimit_count_query = tep_db_query("
          SELECT count(*) as cnt 
          FROM ".TABLE_ORDERS." 
          WHERE customers_id = '".$customer_id."' 
            AND site_id = '".SITE_ID."'
      ");
      $plimit_count = tep_db_fetch_array($plimit_count_query);
      
      if($plimit_count['cnt'] > 0) {
//ccdd
      $plimit_query = tep_db_query("
          SELECT date_purchased 
          FROM ".TABLE_ORDERS." 
          WHERE customers_id = '".$customer_id."' 
            AND site_id = '".SITE_ID."' 
          ORDER BY date_purchased DESC 
          LIMIT 1
      ");
      $plimit = tep_db_fetch_array($plimit_query);
      $p_year = substr($plimit['date_purchased'], 0, 4);
      $p_mon = substr($plimit['date_purchased'], 5, 2);
      $p_day = substr($plimit['date_purchased'], 8, 2);

      $now = time();
      $point_limit = mktime(0, 0, 0, $p_mon, $p_day+MODULE_ORDER_TOTAL_POINT_LIMIT, $p_year);
        if($now > $point_limit) {
//ccdd
          tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
        }
      }
    }

// restore cart contents
        $cart->restore_contents();

        if (sizeof($navigation->snapshot) > 0) {
          $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
          $navigation->clear_snapshot();
          tep_redirect($origin_href);
        } else {
          tep_redirect(tep_href_link(FILENAME_DEFAULT));
        }
      }
    }
  }
}