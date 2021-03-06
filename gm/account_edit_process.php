<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_ACCOUNT_EDIT));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($_POST['action']) || ($_POST['action'] != 'process')) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
  }

  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    if (!isset($_POST[$col])) $_POST[$col] =NULL;
    $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
  }

  if (!isset($_POST['gender'])) $_POST['gender'] =NULL;
  $gender = tep_db_prepare_input($_POST['gender']);
  $firstname = tep_db_prepare_input($_POST['firstname']);
  $lastname = tep_db_prepare_input($_POST['lastname']);
  //add
  if (!isset($_POST['firstname_f'])) $_POST['firstname_f'] =NULL;
  $firstname_f = tep_db_prepare_input($_POST['firstname_f']);
  if (!isset($_POST['lastname_f'])) $_POST['lastname_f'] =NULL;
  $lastname_f = tep_db_prepare_input($_POST['lastname_f']);
  $dob = tep_db_prepare_input($_POST['dob']);
  $email_address = tep_db_prepare_input($_POST['email_address']);
  $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
  $old_email_address = tep_db_prepare_input($_POST['old_email']);
  $telephone = tep_db_prepare_input($_POST['telephone']);
  $fax = tep_db_prepare_input($_POST['fax']);
  $newsletter = tep_db_prepare_input($_POST['newsletter']);
  $password = tep_db_prepare_input($_POST['password']);
  $confirmation = tep_db_prepare_input($_POST['confirmation']);
  if (!isset($_POST['street_address'])) $_POST['street_address'] =NULL;
  $street_address = tep_db_prepare_input($_POST['street_address']);
  if (!isset($_POST['company'])) $_POST['company'] =NULL;
  $company = tep_db_prepare_input($_POST['company']);
  if (!isset($_POST['suburb'])) $_POST['suburb'] =NULL;
  $suburb = tep_db_prepare_input($_POST['suburb']);
  $postcode = tep_db_prepare_input($_POST['postcode']);
  if (!isset($_POST['city'])) $_POST['city'] =NULL;
  $city = tep_db_prepare_input($_POST['city']);
  if (!isset($_POST['zone_id'])) $_POST['zone_id'] =NULL;
  $zone_id = tep_db_prepare_input($_POST['zone_id']);
  if (!isset($_POST['state'])) $_POST['state'] =NULL;
  $state = tep_db_prepare_input($_POST['state']);
  $country = tep_db_prepare_input($_POST['country']);
 
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

  if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
    $error = true;
    $entry_password_error = true;
  } else {
    $entry_password_error = false;
  }

  if ($password !== $confirmation) {
    $error = true;
    $entry_password_error = true;
    $entry_password_confirmation_error = true;
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

  if (!$entry_password_error) {
    $ex_customers_raw = tep_db_query("select customers_password from ".TABLE_CUSTOMERS." where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
    $ex_customers = tep_db_fetch_array($ex_customers_raw);
    if ($ex_customers) {
      if (tep_validate_password($password, $ex_customers['customers_password'])) {
        $error = true;
        $entry_password_error = true;
        $entry_password_confirm_same_error = true;
      }
    }
  }

 
  $check_email_query = tep_db_query("select count(*) as total from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id != '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
  $check_email = tep_db_fetch_array($check_email_query);
  if ($check_email['total'] > 0) {
    $error = true;
    $entry_email_address_exists = true;
  } else {
    $entry_email_address_exists = false;
  }

  if ($error == true) {
    $processed = true;

    include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EDIT_PROCESS);

    $breadcrumb->add(NAVBAR_TITLE_FIRST, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    $breadcrumb->add(NAVBAR_TITLE_SECOND, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text -->
<div id="layout" class="yui3-u">
   <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
  		 <?php include('includes/search_include.php');?>
<div id="main-content">
  <h2><?php echo HEADING_TITLE ; ?></h2>
 <?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?>

  <div> 
    <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
      <tr> 
        <td>
        <?php include(DIR_WS_MODULES . 'account_details.php'); ?>
        <input type="hidden" name="old_email" value="<?php echo $_POST['old_email'];?>"> 
        </td> 
      </tr> 
      <tr> 
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <tr> 
        <td colspan="2">
          <table class="botton-continue" border="0" width="100%" cellspacing="0" cellpadding="2"> 
            <tr> 
              <td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '',
  'SSL') . '">' . tep_image_button('button_back.gif',
  IMAGE_BUTTON_BACK,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?></td> 
              <td align="right"><?php echo tep_image_submit('button_continue.gif',
                  IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"'); ?></td> 
            </tr> 
          </table>
        </td> 
      </tr> 
    </table>
    </div>
    </form> 
</div>
</div>
        <?php include('includes/float-box.php');?>
		</div>
<!-- body_text_eof --> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>
<?php
  } else {
    $sql_data_array = array('new_customers_firstname' => $firstname,
                            'new_customers_lastname' => $lastname,
                            'new_customers_newsletter' => $newsletter,
                            'new_email_address' => $email_address,
                            'send_mail_time' => time(),
                            'new_customers_password' => tep_encrypt_password($password));
    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
   
    $edit_cus_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = ".tep_db_input($customer_id)." and site_id = '".SITE_ID."'");
    $edit_cus_res = tep_db_fetch_array($edit_cus_raw);
    if ($edit_cus_res) {
      if ($edit_cus_res['customers_email_address'] == $email_address) {
        $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $old_email_address,
                                'customers_telephone' => $telephone,
                                'customers_newsletter' => $newsletter,
                                'new_email_address' => $email_address,
                                'send_mail_time' => time(),
                                'customers_password' => tep_encrypt_password($password));

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");

        $sql_data_array = array('entry_street_address' => $street_address,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
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

      
      tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customer_id) . "' and address_book_id = '" . tep_db_input($customer_default_address_id) . "'");
      tep_db_query("update `" . TABLE_CUSTOMERS_INFO . "` set `customer_last_resetpwd` = '".date('Y-m-d H:i:s', time())."' where `customers_info_id` = '" . tep_db_input($customer_id) . "'");
      tep_db_query("update `" . TABLE_CUSTOMERS . "` set `reset_success` = '1' where `customers_id` = '" . tep_db_input($customer_id) . "'");
      
      unset($_SESSION['reset_flag']); 
        tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
      }
    } 
    
    tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");

    $customer_country_id = $country;
    $customer_zone_id = $zone_id;
    
    $mail_name = tep_get_fullname($firstname, $lastname);  
    $ac_email_srandom = md5(time().$customer_id.$email_address); 
    
    tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".tep_db_input($customer_id)."'"); 
    
    $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
    $new_str_array = array(
        HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom,
        $mail_name, 
        STORE_NAME,
        HTTP_SERVER
        ); 
    
    $email_text = str_replace($old_str_array, $new_str_array, ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT);  
    $ed_email_text = str_replace('${SITE_NAME}', STORE_NAME, ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE); 
    tep_mail($mail_name, $email_address, $ed_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    
    $acu_cud = $customer_id;
    tep_session_register('acu_cud');
    tep_redirect(tep_href_link('ac_mail_finish.php', '', 'SSL'));
  
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
