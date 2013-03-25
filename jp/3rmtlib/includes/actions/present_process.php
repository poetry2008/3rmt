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
    
    if (ACCOUNT_GENDER == 'true') {
    //gender
    if (($gender == 'm') || ($gender == 'f')) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    //名是否小于指定长度
    $error = true;
    $entry_firstname_error = true;
    } else {
    $entry_firstname_error = false;
    }
  
    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    //姓是否小于指定长度
    $error = true;
    $entry_lastname_error = true;
    } else {
    $entry_lastname_error = false;
    }
    
    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    //邮箱地址是否小于指定长度
    $error = true;
    $entry_email_address_error = true;
    } else {
    $entry_email_address_error = false;
    }
  
    if (!tep_validate_email($email_address)) {
    //邮箱地址是否符合规范
    $error = true;
    $entry_email_address_check_error = true;
    } else {
    $entry_email_address_check_error = false;
    }
  
    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    //街道地址是否小于指定长度
    $error = true;
    $entry_street_address_error = true;
    } else {
    $entry_street_address_error = false;
    }
  
    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    //邮政编码是否小于指定长度
    $error = true;
    $entry_post_code_error = true;
    } else {
    $entry_post_code_error = false;
    }
  
    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    //城市是否小于指定长度
    $error = true;
    $entry_city_error = true;
    } else {
    $entry_city_error = false;
    }
  
    if (ACCOUNT_STATE == 'true') {
    //state
    if ($entry_country_error == true) {
      $entry_state_error = true;
    } else {
      $zone_id = 0;
      $entry_state_error = false;
      $check_query = tep_db_query("
          select count(*) as total 
          from " . TABLE_ZONES . " 
          where zone_country_id = '" . tep_db_input($country) . "'
      ");
      $check_value = tep_db_fetch_array($check_query);
      $entry_state_has_zones = ($check_value['total'] > 0);
      if ($entry_state_has_zones == true) {
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
  
    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      //电话号码是否小于指定长度 
      $error = true;
      $entry_telephone_error = true;
    } else {
      $entry_telephone_error = false;
    }
    
    if(!empty($password)) {
    //password check
    $passlen = strlen($password);
      if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $entry_password_error = true;
      } else {
      $entry_password_error = false;
      }
    
      if ($password != $confirmation) {
      //password confirmation check
      $error = true;
      $entry_password_error = true;
      }
    }
  
    if(!empty($password)) {
    //check_email_count for regist user
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
    // end check
    if($error == false) {
      //会员注册希望（密码没输入的情况）
      if(!empty($password)) {
        //会员注册处理
        $sql_data_array = array('customers_firstname' => $firstname,
                  'customers_lastname' => $lastname,
                  'customers_email_address' => $email_address,
                  'customers_telephone' => $telephone,
                  'customers_fax' => $fax,
                  'customers_newsletter' => 1,
                  'customers_password' => tep_encrypt_password($password),
                  'customers_default_address_id' => 1,
                  'site_id' => SITE_ID
                  );
  
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);
  
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);
  
        $customer_id = tep_db_insert_id();
  
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
  
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
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
  
        if (SESSION_RECREATE == 'True') { 
          tep_session_recreate();
        }
      
        $pc_id = $customer_id;
        
        //临时插入信息到session里
        
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
      } else {
        //游客（只是用于此次）
        $pc_id = 0;
        tep_session_register('pc_id');
        
        //临时插入信息到session
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
    
