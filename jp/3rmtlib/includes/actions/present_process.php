<?php
/*
  $Id$
*/
$gender = tep_db_prepare_input($_POST['gender']);
    $firstname = tep_db_prepare_input($_POST['firstname']);
    $lastname = tep_db_prepare_input($_POST['lastname']);
    $dob = tep_db_prepare_input($_POST['dob']);
    $email_address = tep_db_prepare_input($_POST['email_address']);
    $telephone = tep_db_prepare_input($_POST['telephone']);
    $fax = tep_db_prepare_input($_POST['fax']);
    $newsletter = tep_db_prepare_input($_POST['newsletter']);
    $password = tep_db_prepare_input($_POST['password']);
    $confirmation = tep_db_prepare_input($_POST['confirmation']);
    $street_address = tep_db_prepare_input($_POST['street_address']);
    $company = tep_db_prepare_input($_POST['company']);
    $suburb = tep_db_prepare_input($_POST['suburb']);
    $postcode = tep_db_prepare_input($_POST['postcode']);
    $city = tep_db_prepare_input($_POST['city']);
    $zone_id = tep_db_prepare_input($_POST['zone_id']);
    $state = tep_db_prepare_input($_POST['state']);
    $country = tep_db_prepare_input($_POST['country']);
    
    $goods_id = $_GET['goods_id'];
    
    // start check
    $error = false;
    //-------------------------------------------------------
    
    //gender
    if (ACCOUNT_GENDER == 'true') {
    if (($gender == 'm') || ($gender == 'f')) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
    }

    //first_name
    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
    } else {
    $entry_firstname_error = false;
    }
  
    //last_name
    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
    } else {
    $entry_lastname_error = false;
    }
    
    //email-1
    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
    } else {
    $entry_email_address_error = false;
    }
  
    //email-2
    if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
    } else {
    $entry_email_address_check_error = false;
    }
  
    //street_address
    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_street_address_error = true;
    } else {
    $entry_street_address_error = false;
    }
  
    //postcode
    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $entry_post_code_error = true;
    } else {
    $entry_post_code_error = false;
    }
  
    //city
    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $entry_city_error = true;
    } else {
    $entry_city_error = false;
    }
  
    //state
    if (ACCOUNT_STATE == 'true') {
    if ($entry_country_error == true) {
      $entry_state_error = true;
    } else {
      $zone_id = 0;
      $entry_state_error = false;
//ccdd
      $check_query = tep_db_query("
          select count(*) as total 
          from " . TABLE_ZONES . " 
          where zone_country_id = '" . tep_db_input($country) . "'
      ");
      $check_value = tep_db_fetch_array($check_query);
      $entry_state_has_zones = ($check_value['total'] > 0);
      if ($entry_state_has_zones == true) {
//ccdd
      $zone_query = tep_db_query("
          select zone_id 
          from " . TABLE_ZONES . " 
          where zone_country_id = '" . tep_db_input($country) . "' 
            and zone_name = '" . tep_db_input($state) . "'
      ");
      if (tep_db_num_rows($zone_query) == 1) {
        $zone_values = tep_db_fetch_array($zone_query);
        $zone_id = $zone_values['zone_id'];
      } else {
//ccdd
        $zone_query = tep_db_query("
            select zone_id 
            from " . TABLE_ZONES . " 
            where zone_country_id = '" . tep_db_input($country) . "' 
              and zone_code = '" . tep_db_input($state) . "'
        ");
        if (tep_db_num_rows($zone_query) == 1) {
          $zone_values = tep_db_fetch_array($zone_query);
          $zone_id = $zone_values['zone_id'];
        } else {
          $error = true;
          $entry_state_error = true;
        }
      }
      } else {
        if ($state == false) {
          $error = true;
          $entry_state_error = true;
        }
      }
    }
    }
  
    //telephone
    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;
      $entry_telephone_error = true;
    } else {
      $entry_telephone_error = false;
    }
    
    //password check
    if(!empty($password)) {
      //password( lengh )
    $passlen = strlen($password);
      if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $entry_password_error = true;
      } else {
      $entry_password_error = false;
      }
    
    //password confirmation check
      if ($password != $confirmation) {
      $error = true;
      $entry_password_error = true;
      }
    }
  
    //check_email_count for regist user
    if(!empty($password)) {
//ccdd
      $check_email = tep_db_query("
          select customers_email_address 
          from " .  TABLE_CUSTOMERS . " 
          where customers_email_address = '" .  tep_db_input($email_address) . "' 
            and customers_id <> '" .  tep_db_input($customer_id) . "' 
            and site_id = '".SITE_ID."'");
      if (tep_db_num_rows($check_email)) {
      $error = true;
      $entry_email_address_exists = true;
      } else {
      $entry_email_address_exists = false;
      }
    }
    //-----------------------------------
    // end check
    if($error == false) {
      //会員登録希望（パスワードが入力されていた場合）
      if(!empty($password)) {
        //会員登録処理
        $sql_data_array = array('customers_firstname' => $firstname,
                  'customers_lastname' => $lastname,
                  'customers_email_address' => $email_address,
                  'customers_telephone' => $telephone,
                  'customers_fax' => $fax,
                  //'customers_newsletter' => $newsletter,
                  'customers_newsletter' => 1,
                  'customers_password' => tep_encrypt_password($password),
                  'customers_default_address_id' => 1,
                  'site_id' => SITE_ID
                  );
  
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);
  
        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);
  
        $customer_id = tep_db_insert_id();
  
        // 2003-06-06 add_telephone
        $sql_data_array = array('customers_id' => $customer_id,
                  'address_book_id' => 1,
                  'entry_firstname' => $firstname,
                  'entry_lastname' => $lastname,
                  'entry_street_address' => $street_address,
                  'entry_postcode' => $postcode,
                  'entry_city' => $city,
                  'entry_country_id' => $country,
                  'entry_telephone' => $telephone);
  
        if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
        if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
        if (ACCOUNT_STATE == 'true') {
          if ($zone_id > 0) {
            $sql_data_array['entry_zone_id'] = $zone_id;
            $sql_data_array['entry_state'] = '';
          } else {
            $sql_data_array['entry_zone_id'] = '0';
            $sql_data_array['entry_state'] = $state;
          }
        }
  
        // ccdd
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
        //ccdd
        tep_db_query("
            insert into " . TABLE_CUSTOMERS_INFO . " (
              customers_info_id, 
              customers_info_number_of_logons, 
              customers_info_date_account_created
            ) values (
              '" . tep_db_input($customer_id) . "', 
              '0', 
              now())
        ");
  
        if (SESSION_RECREATE == 'True') { // 2004/04/25 Add session management
          tep_session_recreate();
        }
      
        $pc_id = $customer_id;
        
        //セッション内に情報を一時的に挿入
        
        $customer_default_address_id = 1;
        $customer_first_name = $firstname;
        $customer_last_name  = $lastname;
        $customer_country_id = $country;
        $customer_zone_id    = $zone_id;
        $customer_emailaddress = $email_address;
        $guestchk = 0;

        tep_session_register('pc_id');
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
        tep_session_register('telephone');
        tep_session_register('street_address');
        tep_session_register('suburb');
        tep_session_register('postcode');
        tep_session_register('city');
        tep_session_register('zone_id');
      
        tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
      } else {//ゲスト（該当する回の応募のみ）
        $pc_id = 0;
        tep_session_register('pc_id');
        
        //セッション内に情報を一時的に挿入
        tep_session_register('firstname');
        tep_session_register('lastname');
        tep_session_register('email_address');
        tep_session_register('telephone');
        tep_session_register('street_address');
        tep_session_register('suburb');
        tep_session_register('postcode');
        tep_session_register('city');
        tep_session_register('zone_id');
      }
      tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
    }
    