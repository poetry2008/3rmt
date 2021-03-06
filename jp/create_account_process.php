<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT_PROCESS);

  if (!isset($_POST['action'])) {
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT));
  }
  $customers_array = array('customers_firstname','customers_lastname');
  $customers_strlen = tep_get_column_len(TABLE_CUSTOMERS,$customers_array);
  $active_single = 0;
  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
  }
  $gender         = tep_db_prepare_input($_POST['gender']);
  $firstname      = tep_db_prepare_input($_POST['firstname']);
  $lastname       = tep_db_prepare_input($_POST['lastname']);
  
  $firstname_f    = htmlspecialchars(tep_db_prepare_input($_POST['firstname_f']));
  $lastname_f     = htmlspecialchars(tep_db_prepare_input($_POST['lastname_f']));
  
  $dob            = tep_db_prepare_input($_POST['dob']);
  $email_address  = htmlspecialchars($_POST['email_address']);
  $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
  $telephone      = tep_db_prepare_input($_POST['telephone']);
  $fax            = tep_db_prepare_input($_POST['fax']);
  $newsletter     = tep_db_prepare_input($_POST['newsletter']);
  $password       = htmlspecialchars(tep_db_prepare_input($_POST['password']));
  $confirmation   = htmlspecialchars(tep_db_prepare_input($_POST['confirmation']));
  $street_address = tep_db_prepare_input($_POST['street_address']);
  $company        = tep_db_prepare_input($_POST['company']);
  $suburb         = tep_db_prepare_input($_POST['suburb']);
  $postcode       = tep_db_prepare_input($_POST['postcode']);
  $city           = tep_db_prepare_input($_POST['city']);
  $zone_id        = tep_db_prepare_input($_POST['zone_id']);
  $state          = tep_db_prepare_input($_POST['state']);
  $country        = tep_db_prepare_input($_POST['country']);
  $guestchk       = tep_db_prepare_input($_POST['guestchk']);
  $referer        = tep_db_prepare_input($_SESSION['referer']);
  $error = false; // reset error flag
  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  }else if(mb_strlen($firstname,'utf8') > $customers_strlen['customers_firstname']){
    $error = true;
    $strlen_firstname_error = true;
  }else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  }else if(mb_strlen($lastname,'utf8') > $customers_strlen['customers_lastname']){
    $error = true;
    $strlen_lastname_error = true;
  }else {
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
    $hicuizd = addslashes(trim($email_address));
  if(preg_match('/\/',$hicuizd)||preg_match('/\\/',$hicuizd)||preg_match('/\\\/',$hicuizd)){
    $error = true;
    $entry_email_address_check_error = true;
  }
  if($guestchk == '0') {
    $passlen = strlen($password);
    if(!(preg_match('/[a-zA-Z]/',$password) && preg_match('/[0-9]/',$password))){
      $error = true;
      $entry_password_english_error = true;
    }else{
      $entry_password_english_error = false; 
    }
    if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $entry_password_error = true;
    } else {
      $entry_password_error = false;
    }

    if ($password != $confirmation) {
      $error = true;
      $entry_password_error = true;
      $entry_password_confirmation_error = true;
    }
    
    if (empty($password) && empty($confirmation)) {
      $error = true;
      $entry_password_error = true;
    }
    
    if (!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $password)) {
        $error = true; 
        $entry_password_error = true;
        if (preg_match('/^[0-9]+$/', $password)) {
          $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM; 
        } else if (preg_match('/^[a-zA-Z0-9]+$/', $password)) {
          $entry_password_error_msg = ENTRY_PASSWORD_IS_ALPHA; 
        }
    }
    
    if (!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $confirmation)) {
        $error = true; 
        $entry_password_error = true;
        if (preg_match('/^[0-9]+$/', $confirmation)) {
          $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM; 
        } else if (preg_match('/^[a-zA-Z0-9]+$/', $confirmation)) {
          $entry_password_error_msg = ENTRY_PASSWORD_IS_ALPHA; 
        }
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
  if (!$noactive_single && !$error) { 
    $check_email = tep_db_query("select * from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_guest_chk = '0' and site_id = '".SITE_ID."'");
    if (tep_db_num_rows($check_email)) {
      $check_email_res = tep_db_fetch_array($check_email); 
      $re_mail_name = tep_get_fullname($check_email_res['customers_firstname'], $check_email_res['customers_lastname']);  
      $re_email_srandom = md5(time().$check_email_res['customers_id'].$check_email_res['customers_email_address']); 
      if (($check_email_res['is_active'] == 0) && $guestchk == 0) {
        $NewPass = $password;
        
        $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                  'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
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
        $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                  'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
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
        
        $mail_name = tep_get_fullname($firstname, $lastname);  
        $gu_email_srandom = md5(time().$customer_id.$email_address); 
        
        $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
        $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
        $new_str_array = array(
            HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom,
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
function pass_hidd(CI){
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
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
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
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
    </tr>
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
  <script>
  document.onreadystatechange=function(){
  var obj = document.getElementsByName("guestchk"); 
  for(i = 0;i < obj.length;i++)    { 
    if(obj[i].checked){ 
      CI = obj[i].value; 
    } 
  }      
  pass_hidd(CI);  
  }
  </script>
</body>
</html>
<?php
  } else {
    if($guestchk == '1') {
      $active_single = 2; 

      $check_cid = tep_db_query("select customers_id, is_active from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and site_id = '".SITE_ID."'");
      if(tep_db_num_rows($check_cid)) {
      $check = tep_db_fetch_array($check_cid);
      if ($check['is_active'] == 1) {
        $active_single = 0; 
      }
      $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
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

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id']);
	if($_SESSION['referer']!=""){
		  tep_db_query("update ".TABLE_CUSTOMERS." set referer='".tep_db_prepare_input($_SESSION['referer'])."'   where customers_id='".$customer_id."'");
        unset($_SESSION['referer']);
		                 }
      tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
    } else {
      $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                'customers_newsletter' => '0',
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '1',
                                'send_mail_time' => time(),
                                'referer' => $referer,
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

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
        tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,customers_info_date_account_last_modified,user_update,user_added) values ('" . tep_db_input($customer_id) . "', '0', now(),now(),'".tep_db_input(tep_get_fullname(mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'), mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8')))."','".tep_db_input(tep_get_fullname(mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'), mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8')))."')");
      }
  } else {
      $active_single = 1; 
      $check_cid = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and site_id = '".SITE_ID."'");
    if(tep_db_num_rows($check_cid)) {
    $check = tep_db_fetch_array($check_cid);
    if ($check['is_quited'] == '1') {
      tep_db_query("delete from  ".TABLE_USER_LOGIN." where account = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'"); 
    }
    $NewPass = $password;
    $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                'customers_newsletter' => $newsletter,
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '0',
                                'is_active' => '1',
                                'is_quited' => '0',
                                'quited_date' => 'null',
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

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = ' . $check['customers_id']);
        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
    } else {
      $NewPass = $password;
      $sql_data_array = array('customers_firstname' => mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'),
                                'customers_lastname' => mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8'),
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $email_address,
                                'customers_telephone' => $telephone,
                                'customers_newsletter' => $newsletter,
                                'customers_password' => tep_encrypt_password($NewPass),
                                'customers_default_address_id' => 1,
                                'customers_guest_chk' => '0',
                                'send_mail_time' => time(),
                                'site_id' => SITE_ID,
                                'origin_password' => $NewPass,
                                'referer' => $referer,
                                'point' => '0');

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

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

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
      tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,customers_info_date_account_last_modified,user_update,user_added) values ('" . tep_db_input($customer_id) . "', '0', now(),now(),'".tep_db_input(tep_get_fullname(mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'), mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8')))."','".tep_db_input(tep_get_fullname(mb_substr($firstname,0,$customers_strlen['customers_firstname'],'utf-8'), mb_substr($lastname,0,$customers_strlen['customers_lastname'],'utf-8')))."')");
    }
  }

    if (SESSION_RECREATE == 'True') {
      tep_session_recreate();
    }
    
    $mail_name = tep_get_fullname($firstname, $lastname);  
    if ($active_single == 1) {
      tep_session_register('customer_id');
      $cart->restore_contents();
      $cart_info_arr = array();
      $cart_info_arr = $cart->get_products();
      tep_session_unregister('customer_id'); 
      $ac_email_srandom = md5(time().$customer_id.$email_address); 
       
      $email_text = stripslashes($lastname.' '.$firstname).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
      $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
      $new_str_array = array(
          HTTP_SERVER.'/m_token.php?aid='.$ac_email_srandom, 
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
      $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
      $new_str_array = array(
          HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom,
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
    tep_session_register('customer_id');
    tep_session_register('customer_first_name');
    tep_session_register('customer_last_name');
    tep_session_register('customer_default_address_id');
    tep_session_register('customer_country_id');
    tep_session_register('customer_zone_id');
    $customer_emailaddress = $email_address;
    tep_session_register('customer_emailaddress');
    tep_session_register('guestchk');
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
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES, '', 'SSL'));
    } else {
      //注册用户邮件
      $create_users_mail_array = tep_get_mail_templates('C_CREAT_ACCOUNT',SITE_ID);
      $email_text .= $create_users_mail_array['contents'];
      $email_text = str_replace(array('${USER_MAIL}', '${PASSWORD}', '${SITE_URL}', '${HTTPS_SERVER}'), array($email_address, $password, HTTP_SERVER, HTTPS_SERVER), $email_text);
      
      $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($email_address)."' and site_id = '".SITE_ID."'"); 
      $customer_info = tep_db_fetch_array($customer_info_raw);
      $email_text = tep_replace_mail_templates($email_text,$email_address,$name);
      $subject = $create_users_mail_array['title'];
      $title_mode_array = array(
                             '${SITE_NAME}' 
                           );
      $title_replace_array = array(
                             STORE_NAME 
                           );
      $subject = str_replace($title_mode_array,$title_replace_array,$subject);
      if ($customer_info['is_send_mail'] != '1') {
        tep_mail($name, $email_address, $subject, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, '', 'SSL'));
    }
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
