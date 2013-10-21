<?php
/*
  $Id$
*/
    $gender = tep_db_prepare_input($_POST['gender']);
    $firstname = tep_db_prepare_input($_POST['firstname']);
    $lastname = tep_db_prepare_input($_POST['lastname']);
    $dob = tep_db_prepare_input($_POST['dob']);
    $email_address = tep_db_prepare_input($_POST['email_address']);
    $newsletter = tep_db_prepare_input($_POST['newsletter']);
    $password = tep_db_prepare_input($_POST['password']);
    $confirmation = tep_db_prepare_input($_POST['confirmation']);
    $company = tep_db_prepare_input($_POST['company']);
    $suburb = tep_db_prepare_input($_POST['suburb']);
    $zone_id = tep_db_prepare_input($_POST['zone_id']);
    $state = tep_db_prepare_input($_POST['state']);
    $country = tep_db_prepare_input($_POST['country']);
    
    $goods_id = $_GET['goods_id'];
    
    // start check
    $error = false;

    //address info
    $options_comment = array();
    $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
    while($address_required = tep_db_fetch_array($address_query)){
    
      $options_comment[$address_required['name_flag']] = $address_required['comment'];
    }
    tep_db_free_result($address_query); 

    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        if($options_comment[substr($p_key,3)] == $p_value){

          $_POST[$p_key] = '';
        }
      } 
    }
    if (!$hm_option->check()) {
      foreach ($_POST as $p_key => $p_value) {
        $op_single_str = substr($p_key, 0, 3);
        if ($op_single_str == 'op_') {
          if($options_comment[substr($p_key,3)] == $p_value){

            $p_value = '';
          }
          $option_info_array[$p_key] = tep_db_input($p_value); 
        } 
      }
    }else{ 
      $error = true;
    }
    
    if (ACCOUNT_GENDER == 'true') {
    //gender
    if (($gender == 'm') || ($gender == 'f')) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
    }

    if (!tep_session_is_registered('customer_id') && strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    //名是否小于指定长度
    $error = true;
    $entry_firstname_error = true;
    } else {
    $entry_firstname_error = false;
    }
  
    if (!tep_session_is_registered('customer_id') && strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    //姓是否小于指定长度
    $error = true;
    $entry_lastname_error = true;
    } else {
    $entry_lastname_error = false;
    }
    
    if (!tep_session_is_registered('customer_id') && strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    //邮箱地址是否小于指定长度
    $error = true;
    $entry_email_address_error = true;
    } else {
    $entry_email_address_error = false;
    }
  
    if (!tep_session_is_registered('customer_id') && !tep_validate_email($email_address)) {
    //邮箱地址是否符合规范
    $error = true;
    $entry_email_address_check_error = true;
    } else {
    $entry_email_address_check_error = false;
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
  
    if(!empty($password)) {
    //password check
      $passlen = strlen($password); 
      if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $entry_password_error = true;
      } else {
      $entry_password_error = false;
      }
    
      if(!(preg_match('/[a-zA-Z]/',$password) && preg_match('/[0-9]/',$password))){
        $error = true;
        $entry_password_english_error = true;
      }else{
        $entry_password_english_error = false; 
      }

      if ($password != $confirmation) {
      //password confirmation check
      $error = true;
      $entry_password_confirmation_error = true;
      }else{
      $entry_password_confirmation_error = false; 
      }
    }
  
    if(!empty($password)) {
    //check_email_count for regist user
      $check_email = tep_db_query("
          select customers_email_address 
          from " .  TABLE_CUSTOMERS . " 
          where customers_email_address = '" .  tep_db_input($email_address) . "' 
            and customers_id <> '" .  tep_db_input($customer_id) . "' 
            and customers_guest_chk = '0' 
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
      $address_options = array();
      $options_type_array = array();
      foreach($option_info_array as $key=>$value){

        $address_options[substr($key,3)] = array($_POST[substr($key,3)],$value);
      }
      foreach($_POST as $post_key=>$post_value){

        if(substr($post_key,0,5) == "type_"){

          $options_type_array[substr($post_key,5)] = $post_value;
        }
      }
      $_SESSION['address_present'] = $address_options;
      $_SESSION['present_type_array'] = $options_type_array;
      if(tep_session_is_registered('customer_id')){
        $pc_id = $_SESSION['customer_id'];
        tep_session_register('pc_id');
        tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL')); 
      }
      //会员注册希望（密码没输入的情况）
      if(!empty($password)) {
        //会员注册处理
        $guest_is_active_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and customers_guest_chk = '1' and site_id = '".SITE_ID."'");
        $guest_is_active_res = tep_db_fetch_array($guest_is_active_raw); 
        $guest_is_active_num = tep_db_num_rows($guest_is_active_raw);
        $sql_data_array = array('customers_firstname' => $firstname,
                  'customers_lastname' => $lastname,
                  'customers_email_address' => $email_address,
                  'customers_newsletter' => 1,
                  'customers_password' => tep_encrypt_password($password),
                  'customers_default_address_id' => 1,
                  'customers_guest_chk' => '0',
                  'send_mail_time' => time(),
                  'origin_password' => $password,
                  'point' => '0',
                  'site_id' => SITE_ID
                  );
  
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        if($guest_is_active_num == 0){ 
          tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);
          $customer_id = tep_db_insert_id();
        }else{
          tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $guest_is_active_res['customers_id'] . ' and site_id = ' . SITE_ID); 
          $customer_id = $guest_is_active_res['customers_id'];
        }
 
        $sql_data_array = array('customers_id' => $customer_id,
                  'address_book_id' => 1,
                  'entry_firstname' => $firstname,
                  'entry_lastname' => $lastname,
                  'entry_country_id' => $country);
  
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

        if($guest_is_active_num == 0){ 
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
        }else{
          tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $guest_is_active_res['customers_id']);
          tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'"); 
        }
  
        if (SESSION_RECREATE == 'True') { 
          tep_session_recreate();
        }

      if($guest_is_active_res['is_active'] == 0){
        //邮件验证
        $mail_name = tep_get_fullname($firstname, $lastname);  
        tep_session_unregister('customer_id'); 
        $ac_email_srandom = md5(time().$customer_id.$email_address); 
       
        $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
        $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
        $new_str_array = array(
          HTTP_SERVER.'/m_token.php?aid='.$ac_email_srandom.'&goods_id='.$goods_id, 
          $mail_name,
          STORE_NAME,
          HTTP_SERVER
          ); 
        //会员邮件认证
        $users_mail_array = tep_get_mail_templates('ACTIVE_ACCOUNT_EMAIL_CONTENT',SITE_ID);
        $email_text .= str_replace($old_str_array, $new_str_array, $users_mail_array['contents']);  
        $ac_email_text = str_replace('${SITE_NAME}', STORE_NAME, $users_mail_array['title']);  
      
        $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'"); 
        $customer_info = tep_db_fetch_array($customer_info_raw);

        $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name); 
        if ($customer_info['is_send_mail'] != '1') {
          tep_mail($mail_name, $email_address, $ac_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
       
        tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".$customer_id."'"); 
      }
      $me_cud = $customer_id; 
      tep_session_register('me_cud'); 

        
      //临时插入信息到session里
        
      $customer_default_address_id = 1;
      $customer_first_name = $firstname;
      $customer_last_name  = $lastname;
      $customer_country_id = $country;
      $customer_zone_id    = $zone_id;
      $customer_emailaddress = $email_address;
      $guestchk = 0;

      if($guest_is_active_res['is_active'] == 1){
        tep_session_register('customer_id'); 
      }
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
      if($guest_is_active_res['is_active'] == 0){
        tep_redirect(tep_href_link('member_auth.php', '', 'SSL')); 
      }else{
        $pc_id = $guest_is_active_res['customers_id'];
        tep_session_register('pc_id');
        tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL')); 
      }

      } else {

        //邮件认证
        $guest_isactive_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'");
        $guest_isactive_res = tep_db_fetch_array($guest_isactive_raw); 
        $guest_isactive_num = tep_db_num_rows($guest_isactive_raw);
        if ($guest_isactive_num) {
          if($guest_isactive_res['customers_guest_chk'] == '1'){
            if ($guest_isactive_res['is_active'] == 0) {
              $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
              $sql_data_array = array('customers_firstname' => $firstname,
                                  'customers_lastname' => $lastname,
                                  'customers_email_address' => $email_address,
                                  'customers_password' => tep_encrypt_password($NewPass),
                                  'customers_default_address_id' => 1,
                                  'customers_guest_chk' => '1',
                                  'send_mail_time' => time(),
                                  'point' => '0');
          
              if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
              if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

              tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $guest_isactive_res['customers_id'] . ' and site_id = ' . SITE_ID);

              $customer_id = $guest_isactive_res['customers_id'];
      
              $sql_data_array = array('customers_id' => $customer_id,
                                  'address_book_id' => 1,
                                  'entry_firstname' => $firstname,
                                  'entry_lastname' => $lastname,
                                  'entry_country_id' => $country
                                  );

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

              tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $guest_isactive_res['customers_id']);
              tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
        
              $mail_name = tep_get_fullname($firstname, $lastname);  
              $gu_email_srandom = md5(time().$customer_id.$email_address); 
        
              $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
              $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
              $new_str_array = array(
                HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom.'&goods_id='.$goods_id,
                $mail_name, 
                STORE_NAME,
                HTTP_SERVER
              ); 
              //游客邮件认证
              $guest_mail_array = tep_get_mail_templates('GUEST_LOGIN_EMAIL_CONTENT',SITE_ID);
              $email_text .= str_replace($old_str_array, $new_str_array, $guest_mail_array['contents']);  
              $gu_email_text = str_replace('${SITE_NAME}', STORE_NAME, $guest_mail_array['title']);
              $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name);
              tep_mail($mail_name, $email_address, $gu_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        
              tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$gu_email_srandom."' where `customers_id` = '".$customer_id."'"); 
        
              $pa_gud = $guest_isactive_res['customers_id']; 
              tep_session_register('pa_gud');
              tep_redirect(tep_href_link('non-member_auth.php', '', 'SSL'));  
           }
        }else{
          if ($guest_isactive_res['is_active'] == 0) {

            $re_mail_name = tep_get_fullname($firstname, $lastname);  
            $re_email_srandom = md5(time().$guest_isactive_res['customers_id'].$email_address); 
        
            $sql_data_array = array('customers_firstname' => $firstname,
                                  'customers_lastname' => $lastname,
                                  'customers_email_address' => $email_address,
                                  'customers_default_address_id' => 1,
                                  'customers_guest_chk' => '0',
                                  'send_mail_time' => time(),
                                  'point' => '0');

            if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
            if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

            tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' .$guest_isactive_res['customers_id'] . ' and site_id = ' . SITE_ID);

            $customer_id = $guest_isactive_res['customers_id'];
      
            $sql_data_array = array('customers_id' => $customer_id,
                                  'address_book_id' => 1,
                                  'entry_firstname' => $firstname,
                                  'entry_lastname' => $lastname,
                                  'entry_country_id' => $country
                                  );

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

            tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $customer_id);
            tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");

           //邮件验证
           $mail_name = tep_get_fullname($firstname, $lastname);  
           tep_session_unregister('customer_id'); 
           $ac_email_srandom = md5(time().$customer_id.$email_address); 
       
           $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
           $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
           $new_str_array = array(
             HTTP_SERVER.'/m_token.php?aid='.$ac_email_srandom.'&goods_id='.$goods_id, 
             $mail_name,
             STORE_NAME,
             HTTP_SERVER
           ); 
           //会员邮件认证
           $users_mail_array = tep_get_mail_templates('ACTIVE_ACCOUNT_EMAIL_CONTENT',SITE_ID);
           $email_text .= str_replace($old_str_array, $new_str_array, $users_mail_array['contents']);  
           $ac_email_text = str_replace('${SITE_NAME}', STORE_NAME, $users_mail_array['title']);  
      
           $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'"); 
           $customer_info = tep_db_fetch_array($customer_info_raw);

           $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name); 
           if ($customer_info['is_send_mail'] != '1') {
             tep_mail($mail_name, $email_address, $ac_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
           }
       
           tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".$customer_id."'"); 
           $me_cud = $customer_id; 
           tep_session_register('me_cud'); 
        
           //临时插入信息到session里
        
           $customer_default_address_id = 1;
           $customer_first_name = $firstname;
           $customer_last_name  = $lastname;
           $customer_country_id = $country;
           $customer_zone_id    = $zone_id;
           $customer_emailaddress = $email_address;
           $guestchk = 0;

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
        
           tep_redirect(tep_href_link('member_auth.php', '', 'SSL'));
         }  
      }
    } else {
      $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_email_address' => $email_address,
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '1',
                                'send_mail_time' => time(),
                                'site_id' => SITE_ID,
                                'point' => '0');

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

        $customer_id = tep_db_insert_id();

        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => 1,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_country_id' => $country
                                );

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
        tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,customers_info_date_account_last_modified,user_update,user_added) values ('" . tep_db_input($customer_id) . "', '0', now(),now(),'".tep_get_fullname($firstname, $lastname)."','".tep_get_fullname($firstname, $lastname)."')");
      $mail_name = tep_get_fullname($firstname, $lastname);
      tep_session_unregister('customer_id');
      
      $gu_email_srandom = md5(time().$customer_id.$email_address); 
      
      $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
      $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
      $new_str_array = array(
          HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom.'&goods_id='.$_GET['goods_id'],
          $mail_name, 
          STORE_NAME,
          HTTP_SERVER
          ); 
      //游客邮件认证
      $guest_mail_array = tep_get_mail_templates('GUEST_LOGIN_EMAIL_CONTENT',SITE_ID);
      $email_text .= str_replace($old_str_array, $new_str_array, $guest_mail_array['contents']);  
      $gu_email_text = str_replace('${SITE_NAME}', STORE_NAME, $guest_mail_array['title']);
      
      $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'"); 
      $customer_info = tep_db_fetch_array($customer_info_raw);

      $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name); 
      if ($customer_info['is_send_mail'] != '1') {
        tep_mail($mail_name, $email_address, $gu_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
      
      tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$gu_email_srandom."' where `customers_id` = '".$customer_id."'"); 
      
      $pa_gud = $customer_id; 
      tep_session_register('pa_gud'); 
      tep_session_register('firstname');
      tep_session_register('lastname');
      tep_session_register('email_address');
      tep_session_register('suburb');
      tep_session_register('zone_id');
      tep_redirect(tep_href_link('non-member_auth.php', '', 'SSL'));
      }  
    }
    if(!empty($password)){
      //临时插入信息到session里
        
      $customer_default_address_id = 1;
      $customer_first_name = $firstname;
      $customer_last_name  = $lastname;
      $customer_country_id = $country;
      $customer_zone_id    = $zone_id;
      $customer_emailaddress = $email_address;
      $guestchk = 0;

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
    }else{
      tep_session_register('firstname');
      tep_session_register('lastname');
      tep_session_register('email_address');
      tep_session_register('suburb');
      tep_session_register('zone_id'); 
    }
    $pc_id = $guest_isactive_res['customers_id'];
    tep_session_register('pc_id');
    tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
  }
    
