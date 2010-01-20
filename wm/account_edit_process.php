<?php
/*
  $Id: account_edit_process.php,v 1.3 2003/06/09 00:54:22 suzukawa Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
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

  if (!isset($HTTP_POST_VARS['action']) || ($HTTP_POST_VARS['action'] != 'process')) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
  }

  // tamura 2002/12/30 「全角」英数字を「半角」に変換
  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    $HTTP_POST_VARS[$col] = tep_an_zen_to_han($HTTP_POST_VARS[$col]);
  }

  $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);
  $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
  $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
  //add
  $firstname_f = tep_db_prepare_input($HTTP_POST_VARS['firstname_f']);
  $lastname_f = tep_db_prepare_input($HTTP_POST_VARS['lastname_f']);
  $dob = tep_db_prepare_input($HTTP_POST_VARS['dob']);
  $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
  $telephone = tep_db_prepare_input($HTTP_POST_VARS['telephone']);
  $fax = tep_db_prepare_input($HTTP_POST_VARS['fax']);
  $newsletter = tep_db_prepare_input($HTTP_POST_VARS['newsletter']);
  $password = tep_db_prepare_input($HTTP_POST_VARS['password']);
  $confirmation = tep_db_prepare_input($HTTP_POST_VARS['confirmation']);
  $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);
  $company = tep_db_prepare_input($HTTP_POST_VARS['company']);
  $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);
  $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);
  $city = tep_db_prepare_input($HTTP_POST_VARS['city']);
  $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);
  $state = tep_db_prepare_input($HTTP_POST_VARS['state']);
  $country = tep_db_prepare_input($HTTP_POST_VARS['country']);

  $error = false; // reset error flag
/*
  if (ACCOUNT_GENDER == 'true') {
    if ( ($gender == 'm') || ($gender == 'f') ) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
  }
*/
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
/*
  if (ACCOUNT_DOB == 'true') {
    if (checkdate(substr(tep_date_raw($dob), 4, 2), substr(tep_date_raw($dob), 6, 2), substr(tep_date_raw($dob), 0, 4))) {
      $entry_date_of_birth_error = false;
    } else {
      $error = true;
      $entry_date_of_birth_error = true;
    }
  }
*/

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
/*
  if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_street_address_error = true;
  } else {
    $entry_street_address_error = false;
  }
*/
/*
  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $entry_post_code_error = true;
  } else {
    $entry_post_code_error = false;
  }
*/
/*
  if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $entry_city_error = true;
  } else {
    $entry_city_error = false;
  }
*/
/*
  if (!is_numeric($country)) {
    $error = true;
    $entry_country_error = true;
  } else {
    $entry_country_error = false;
  }
*/
/*
  if (ACCOUNT_STATE == 'true') {
    if ($entry_country_error) {
      $entry_state_error = true;
    } else {
      $zone_id = 0;
      $entry_state_error = false;
      $country_check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "'");
      $country_check = tep_db_fetch_array($country_check_query);
      if ($entry_state_has_zones = ($country_check['total'] > 0)) {
        $match_zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "' and zone_name = '" . tep_db_input($state) . "'");
        if (tep_db_num_rows($match_zone_query) == 1) {
          $match_zone = tep_db_fetch_array($match_zone_query);
          $zone_id = $match_zone['zone_id'];
        } else {
          $match_zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "' and zone_code = '" . tep_db_input($state) . "'");
          if (tep_db_num_rows($match_zone_query) == 1) {
            $match_zone = tep_db_fetch_array($match_zone_query);
            $zone_id = $match_zone['zone_id'];
          } else {
            $error = true;
            $entry_state_error = true;
          }
        }
      } elseif (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
        $error = true;
        $entry_state_error = true;
      }
    }
  }
*/
/*
  if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
    $error = true;
    $entry_telephone_error = true;
  } else {
    $entry_telephone_error = false;
  }
*/
  if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
    $error = true;
    $entry_password_error = true;
  } else {
    $entry_password_error = false;
  }

  if ($password != $confirmation) {
    $error = true;
    $entry_password_error = true;
  }

  $check_email_query = tep_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and customers_id != '" . tep_db_input($customer_id) . "'");
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

    $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div class="comment"> 
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
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
          </form> 
        </div>
        <p class="pageBottom"></p>
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
    $sql_data_array = array('customers_firstname' => $firstname,
                            'customers_lastname' => $lastname,
							//add
							'customers_firstname_f' => $firstname_f,
                            'customers_lastname_f' => $lastname_f,
                            'customers_email_address' => $email_address,
                            'customers_telephone' => $telephone,
                            //'customers_fax' => $fax,
                            'customers_newsletter' => $newsletter,
                            'customers_password' => tep_encrypt_password($password));

    if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
    if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customer_id) . "'");

// 2003-06-06 add_telephone
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

    tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");

    $customer_country_id = $country;
    $customer_zone_id = $zone_id;

    tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
