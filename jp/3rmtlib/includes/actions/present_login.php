<?php
/*
  $Id$
*/
    //全角转半角 
    $_POST['email_address'] = tep_an_zen_to_han($_POST['email_address']);

    $email_address = tep_db_prepare_input($_POST['email_address']);
    $password = tep_db_prepare_input($_POST['password']);
    $goods_id = $_GET['goods_id'];
      
    //check
    $login_error = false;
    $check_customer_query = tep_db_query("
        select customers_id, 
               customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_email_address, 
               customers_default_address_id 
        from " .  TABLE_CUSTOMERS . " 
        where customers_email_address = '" .  tep_db_input($email_address) . "' 
          and site_id = '".SITE_ID."' AND is_active = 1 AND is_quited = 0
      ");
      if (tep_db_num_rows($check_customer_query)) {
        //判断该顾客是否存在 
        $check_customer = tep_db_fetch_array($check_customer_query);
        // Check that password is good
        if (tep_validate_password($password, $check_customer['customers_password'])) {
          $pc_id = $check_customer['customers_id'];
      tep_session_register('pc_id');
      $customers_query = tep_db_query("
          select * 
          from ".TABLE_CUSTOMERS." 
          where customers_id = '".$pc_id."' 
            and site_id = '".SITE_ID."'
      ");
      $customers_result = tep_db_fetch_array($customers_query);
      $address_query = tep_db_query("
          select * 
          from ".TABLE_ADDRESS_BOOK." 
          where customers_id = '".$pc_id."' 
            and address_book_id = '1'
      ");
      $address_result = tep_db_fetch_array($address_query);

      $customer_id                 = $customers_result['customers_id'];
      $customer_default_address_id = $customers_result['customers_default_address_id'];
      $customer_first_name         = $customers_result['customers_firstname'];
      $customer_last_name          = $customers_result['customers_lastname']; 
      $customer_country_id         = $customers_result['entry_country_id'];
      $customer_zone_id            = $customers_result['entry_zone_id'];
      $customer_emailaddress       = $email_address; 
      $guestchk                    = $customers_result['customers_guest_chk'];
      $firstname                   = $customers_result['customers_firstname'];
      $lastname                    = $customers_result['customers_lastname'];
      $email_address               = $customers_result['customers_email_address'];
      $suburb                      = $address_result['entry_suburb'];
      $zone_id                     = $address_result['entry_zone_id'];

        //临时插入信息到session里
      tep_session_register('customer_id');
      tep_session_register('customer_default_address_id');
      tep_session_register('customer_first_name');
      tep_session_register('customer_last_name');
      tep_session_register('customer_country_id');
      tep_session_register('customer_zone_id');
      tep_session_register('customer_emailaddress');
      tep_session_register('guestchk');
      
      tep_session_register('firstname');
      tep_session_register('lastname');
      tep_session_register('email_address');
      tep_session_register('suburb');
      tep_session_register('zone_id');
      
      } else {
        $login_error = true;
        $_GET['login'] = 'fail';
      }
    } else {
      $login_error = true;
      $_GET['login'] = 'fail';
    }
    
    if($login_error == false) tep_redirect(tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$goods_id, 'SSL'));
