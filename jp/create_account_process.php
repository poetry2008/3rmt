<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT_PROCESS);

  if (!isset($_POST['action'])) {
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT));
  }
  $active_single = 0;
  // tamura 2002/12/30 「全角」英数字を「半角」に変換
  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    $_POST[$col] = isset($_POST[$col]) ? tep_an_zen_to_han($_POST[$col]) : '';
  }

  $gender         = isset($_POST['gender']) ? tep_db_prepare_input($_POST['gender']) : '';
  $firstname      = tep_db_prepare_input($_POST['firstname']);
  $lastname       = tep_db_prepare_input($_POST['lastname']);
  
  $firstname_f    = isset($_POST['firstname_f']) ? tep_db_prepare_input($_POST['firstname_f']) : '';
  $lastname_f     = isset($_POST['lastname_f']) ? tep_db_prepare_input($_POST['lastname_f']) : '';
  
  $dob            = tep_db_prepare_input($_POST['dob']);
  $email_address  = tep_db_prepare_input($_POST['email_address']);
  $telephone      = tep_db_prepare_input($_POST['telephone']);
  $fax            = tep_db_prepare_input($_POST['fax']);
  $newsletter     = tep_db_prepare_input($_POST['newsletter']);
  $password       = tep_db_prepare_input($_POST['password']);
  $confirmation   = tep_db_prepare_input($_POST['confirmation']);
  $street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']) : '';
  $company        = isset($_POST['company']) ? tep_db_prepare_input($_POST['company']) : '';
  $suburb         = isset($_POST['suburb']) ? tep_db_prepare_input($_POST['suburb']) : '';
  $postcode       = tep_db_prepare_input($_POST['postcode']);
  $city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']) : '';
  $zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']): '';
  $state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']) : '';
  $country        = tep_db_prepare_input($_POST['country']);
  $guestchk       = tep_db_prepare_input($_POST['guestchk']);

  $error = false; // reset error flag

  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }

  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  }

  if($guestchk == '0') {
    $passlen = strlen($password);
    if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $entry_password_error = true;
    } else {
      $entry_password_error = false;
    }

    if ($password != $confirmation) {
      $error = true;
      $entry_password_error = true;
    }
  }

  $noactive_single = false;

  if ($guestchk == 1) {
    $noactive_me_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and is_active = '0' and customers_guest_chk = '0' and site_id = '".SITE_ID."'"); 
    if (tep_db_num_rows($noactive_me_raw)) {
      $noactive_me_res = tep_db_fetch_array($noactive_me_raw); 
      $noactive_single = true;  
    }
  }
//ccdd
  if (!$noactive_single) { 
    $check_email = tep_db_query("select * from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_guest_chk = '0' and site_id = '".SITE_ID."'");
    if (tep_db_num_rows($check_email)) {
      $check_email_res = tep_db_fetch_array($check_email); 
      $re_mail_name = tep_get_fullname($check_email_res['customers_firstname'], $check_email_res['customers_lastname']);  
      $re_email_srandom = md5(time().$check_email_res['customers_id'].$check_email_res['customers_email_address']); 
      if (($check_email_res['is_active'] == 0) && $guestchk == 0) {
        $NewPass = $password;
        
        $sql_data_array = array('customers_firstname' => $firstname,
                                  'customers_lastname' => $lastname,
                                  'customers_firstname_f' => $firstname_f,
                                  'customers_lastname_f' => $lastname_f,
                                  'customers_email_address' => $email_address,
                                  'customers_telephone' => $telephone,
                                  'customers_newsletter' => $newsletter,
                                  'customers_password' => tep_encrypt_password($NewPass),
                                  'customers_default_address_id' => 1,
                                  'customers_guest_chk' => '0',
                                  'send_mail_time' => time(),
                                  'origin_password' => $NewPass, 
                                  'point' => '0');

          if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
          if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

          tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $check_email_res['customers_id'] . ' and site_id = ' . SITE_ID);

          $customer_id = $check_email_res['customers_id'];
      
          $sql_data_array = array('customers_id' => $customer_id,
                                  'address_book_id' => 1,
                                  'entry_firstname' => $firstname,
                                  'entry_lastname' => $lastname,
                                  'entry_firstname_f' => $firstname_f,
                                  'entry_lastname_f' => $lastname_f,
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

          tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $check_email_res['customers_id']);
          tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
        
        $me_cud = $check_email_res['customers_id']; 
        tep_session_register('me_cud');
        tep_redirect(tep_href_link('member_auth.php', '', 'SSL')); 
      }
      $error = true;
      $entry_email_address_exists = true;
    } else {
      $entry_email_address_exists = false;
    }
  } 
  
  $guest_isactive_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and customers_guest_chk = '1' and site_id = '".SITE_ID."'");
  $guest_isactive_res = tep_db_fetch_array($guest_isactive_raw); 
  if ($guest_isactive_res) {
    if ($guest_isactive_res['is_active'] == 0) {
      if ($guestchk == 1) {
        $error = true; 
        $entry_guest_not_active = true; 
        $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
        $sql_data_array = array('customers_firstname' => $firstname,
                                  'customers_lastname' => $lastname,
                                  'customers_firstname_f' => $firstname_f,
                                  'customers_lastname_f' => $lastname_f,
                                  'customers_email_address' => $email_address,
                                  'customers_telephone' => $telephone,
                                  'customers_newsletter' => '0',
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
                                  'entry_firstname_f' => $firstname_f,
                                  'entry_lastname_f' => $lastname_f,
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

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $guest_isactive_res['customers_id']);
        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
        
        $mail_name = tep_get_fullname($fistname, $lastname);  
        $gu_email_srandom = md5(time().$customer_id.$email_address); 
        
        $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
        $email_text .= str_replace('${URL}', HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom, GUEST_LOGIN_EMAIL_CONTENT);  
        tep_mail($mail_name, $email_address, GUEST_LOGIN_EMAIL_TITLE, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        
        tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$gu_email_srandom."' where `customers_id` = '".$customer_id."'"); 
        
        $pa_gud = $guest_isactive_res['customers_id']; 
        tep_session_register('pa_gud');
        tep_redirect(tep_href_link('non-member_auth.php', '', 'SSL')); 
      } else {
        $check_again_email = tep_db_query("select * from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id <> '" .  tep_db_input($customer_id) . "' and customers_guest_chk = '0' and site_id = '".SITE_ID."'");
        if (tep_db_num_rows($check_again_email)) {
          $error = true;
          $entry_email_address_exists = true;
        }
      }
    } else {
      $entry_guest_not_active = false; 
    }
  } else {
    $entry_guest_not_active = false; 
  }
  
  if ($error == true) {
    $processed = true;

    $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CREATE_ACCOUNT));
    $breadcrumb->add(NAVBAR_TITLE_2);
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
<script type="text/javascript">
function pass_hidd(){
  var idx = document.account_edit.elements["guestchk"].selectedIndex;
  var CI = document.account_edit.elements["guestchk"].options[idx].value;
  
  if(CI == '0'){
    document.getElementById('trpass1').style.display = "";
    document.getElementById('trpass2').style.display = "";
  }else{
    document.getElementById('trpass1').style.display = "none";
    document.getElementById('trpass2').style.display = "none";
  }
}
</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_CREATE_ACCOUNT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0">  
            <tr> 
              <td><?php include(DIR_WS_MODULES . 'account_details.php'); ?></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
          </div></form> 
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php
  } else {
    if($guestchk == '1') {
      $active_single = 2; 
      # Guest
      //ccdd
      $check_cid = tep_db_query("select customers_id, is_active from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and site_id = '".SITE_ID."'");
      if(tep_db_num_rows($check_cid)) {
        # Guest & 2回目以上 //==============================================
      $check = tep_db_fetch_array($check_cid);
      if ($check['is_active'] == 1) {
        $active_single = 0; 
      }
      $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                //'customers_fax' => $fax,
                                'customers_newsletter' => '0',
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '1',
                                'send_mail_time' => time(),
                                'point' => '0');

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id'] .' and site_id = '.SITE_ID);

        $customer_id = $check['customers_id'];

        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => 1,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_firstname_f' => $firstname_f,
                                'entry_lastname_f' => $lastname_f,
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
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id']);
      # //Guest & 2回目以上 ==============================================
      //ccdd
      tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
    } else {
      # Guest & 1回目 //==================================================
      $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                //'customers_fax' => $fax,
                                'customers_newsletter' => '0',
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '1',
                                'send_mail_time' => time(),
                                'site_id' => SITE_ID,
                                'point' => '0');

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

        $customer_id = tep_db_insert_id();

        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => 1,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_firstname_f' => $firstname_f,
                                'entry_lastname_f' => $lastname_f,
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
      # Guest & 1回目 //==================================================
        //ccdd
        tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . tep_db_input($customer_id) . "', '0', now())");
      }
    } else {
      # Member
      //ccdd
      $active_single = 1; 
      $check_cid = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and site_id = '".SITE_ID."'");
    if(tep_db_num_rows($check_cid)) {
      # Member & 2回目以上 //==============================================
    $check = tep_db_fetch_array($check_cid);
    $NewPass = $password;
      
    $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                //'customers_fax' => $fax,
                                'customers_newsletter' => $newsletter,
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '0',
                                'is_active' => '1',
                                'send_mail_time' => time(),
                                'origin_password' => $NewPass,
                                'point' => '0');
         
        if ($check['customers_guest_chk'] == '1' && $check['is_active'] == '0') {
          $sql_data_array['is_active'] = 0; 
        }
        
        if ($check['customers_guest_chk'] == '1' && $check['is_active'] == '1') {
          $active_single = 0; 
        }
        
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id'].' and site_id = '.SITE_ID);

        $customer_id = $check['customers_id'];

        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => 1,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_firstname_f' => $firstname_f,
                                'entry_lastname_f' => $lastname_f,
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
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id']);
      # //Member & 2回目以上 ==============================================
        //ccdd
        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
    } else {
      # Member & 1回目 //==================================================
      $NewPass = $password;
      $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                //'customers_fax' => $fax,
                                'customers_newsletter' => $newsletter,
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '0',
                                'send_mail_time' => time(),
                                'site_id' => SITE_ID,
                                'origin_password' => $NewPass,
                                'point' => '0');

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

        $customer_id = tep_db_insert_id();

        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => 1,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_firstname_f' => $firstname_f,
                                'entry_lastname_f' => $lastname_f,
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
    # Member & 1回目 //==================================================
      //ccdd
      tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . tep_db_input($customer_id) . "', '0', now())");
    }
  }

    if (SESSION_RECREATE == 'True') { // 2004/04/25 Add session management
      tep_session_recreate();
    }
    
    $mail_name = tep_get_fullname($fistname, $lastname);  
    if ($active_single == 1) {
      tep_session_register('customer_id');
      $cart->restore_contents();
      $cart_info_arr = array();
      $cart_info_arr = $cart->get_products();
      tep_session_unregister('customer_id'); 
      $ac_email_srandom = md5(time().$customer_id.$email_address); 
       
       $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
      $email_text .= str_replace('${URL}', HTTP_SERVER.'/m_token.php?aid='.$ac_email_srandom, ACTIVE_ACCOUNT_EMAIL_CONTENT);  
      tep_mail($mail_name, $email_address, ACTIVE_ACCOUNT_EMAIL_TITLE, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
       
      tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".$customer_id."'"); 
      $me_cud = $customer_id; 
      tep_session_register('me_cud');
      if (!empty($cart_info_arr)) {
        foreach ($cart_info_arr as $ci_key => $ci_value) {
          $cart->add_cart($ci_value['products_id'], $ci_value['quantity']); 
        }
      }
      tep_redirect(tep_href_link('member_auth.php', '', 'SSL')); 
    } else if ($active_single == 2){
      tep_session_register('customer_id');
      $cart->restore_contents();
      $cart_info_arr = array();
      $cart_info_arr = $cart->get_products();
      tep_session_unregister('customer_id');
      
      $gu_email_srandom = md5(time().$customer_id.$email_address); 
      
      $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
      $email_text .= str_replace('${URL}', HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom, GUEST_LOGIN_EMAIL_CONTENT);  
      tep_mail($mail_name, $email_address, GUEST_LOGIN_EMAIL_TITLE, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      
      tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$gu_email_srandom."' where `customers_id` = '".$customer_id."'"); 
      
      $pa_gud = $customer_id; 
      tep_session_register('pa_gud');
      
      if (!empty($cart_info_arr)) {
        foreach ($cart_info_arr as $ci_key => $ci_value) {
          $cart->add_cart($ci_value['products_id'], $ci_value['quantity']); 
        }
      }
      
      tep_redirect(tep_href_link('non-member_auth.php', '', 'SSL')); 
    }

    $customer_first_name         = $firstname;
    $customer_last_name          = $lastname;
    $customer_default_address_id = 1;
    $customer_country_id         = $country;
    $customer_zone_id            = $zone_id;
    $customer_emailaddress       = $email_address;

    tep_session_register('customer_id');
    tep_session_register('customer_first_name');
    tep_session_register('customer_last_name');
    tep_session_register('customer_default_address_id');
    tep_session_register('customer_country_id');
    tep_session_register('customer_zone_id');
    tep_session_register('guestchk');
    tep_session_register('customer_emailaddress');

    // restore cart contents
    $cart->restore_contents();

    // build the message content
    $name = tep_get_fullname($firstname,$lastname);

    if (ACCOUNT_GENDER == 'true') {
       if ($_POST['gender'] == 'm') {
         $email_text = EMAIL_GREET_MR;
       } else {
         $email_text = EMAIL_GREET_MS;
       }
    } else {
      $email_text = EMAIL_GREET_NONE;
    }

    if($guestchk == '1') {
      # For Guest
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'));
    } else {
      # For Member
      //$email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;
      $email_text .= C_CREAT_ACCOUNT ;
      $email_text = str_replace(array('${MAIL}', '${PASS}'), array($email_address, $password), $email_text);
      tep_mail($name, $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, '', 'SSL'));
    }
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
