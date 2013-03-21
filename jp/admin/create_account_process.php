<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ACCOUNT_PROCESS);
  if (!isset($_POST['action'])) {
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT));
  }

  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    $_POST[$col] = isset($_POST[$col]) ? tep_an_zen_to_han($_POST[$col]) : '';
  }

  $gender         = isset($_POST['gender']) ? tep_db_prepare_input($_POST['gender']):'';
  $firstname      = tep_db_prepare_input($_POST['firstname']);
  $lastname       = tep_db_prepare_input($_POST['lastname']);
  $dob            = tep_db_prepare_input($_POST['dob']);
  $email_address  = tep_db_prepare_input($_POST['email_address']);
  $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
  $telephone      = tep_db_prepare_input($_POST['telephone']);
  $fax            = tep_db_prepare_input($_POST['fax']);
  $newsletter     = tep_db_prepare_input($_POST['newsletter']);
  $password       = tep_db_prepare_input($_POST['password']);
  $confirmation   = tep_db_prepare_input($_POST['confirmation']);
  $street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']):'';
  $company        = isset($_POST['company'])?tep_db_prepare_input($_POST['company']):'';
  $suburb         = isset($_POST['suburb']) ?tep_db_prepare_input($_POST['suburb']):'';
  $postcode       = tep_db_prepare_input($_POST['postcode']);
  $city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']):'';
  $zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']):'';
  $site_id        = isset($_POST['site_id']) ? tep_db_prepare_input($_POST['site_id']):'';
  $state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']):'';
  $country        = isset($_POST['country']) ? tep_db_prepare_input($_POST['country']):'';

  $error = false; // reset error flag

  if (ACCOUNT_GENDER == 'true') {
    if (($gender == 'm') || ($gender == 'f')) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
  }

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

  if (in_array($site_id, tep_get_sites())) {
    $error = true;
    $entry_site_id_error = true;
  } else {
    $entry_site_id_error = false;
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
  $check_email = tep_db_query("
      select customers_email_address 
      from " . TABLE_CUSTOMERS . " 
      where customers_email_address = '" . tep_db_input($email_address) . "' 
        and site_id = '".$site_id."'
        
  ");
  if (tep_db_num_rows($check_email)) {
    $error = true;
    $entry_email_address_exists = true;
  } else {
    $entry_email_address_exists = false;
  }

  if ($error == true) {
    $processed = true;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title><?php echo TITLE ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = 'create_account.php'; 
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
  <?php echo tep_draw_form('account_edit', FILENAME_CREATE_ACCOUNT_PROCESS, 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
<?php
  require(DIR_WS_INCLUDES . 'step-by-step/account_details.php');
?>
        </td>
      </tr>
      <tr>
        <td align="right" class="main"><br><?php echo tep_image_submit('button_insert.gif', IMAGE_INSERT); ?></td>
      </tr>
    </table></form></td>
<!-- body_text_eof -->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
    </table></div></td>
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php
  } else {
    $sql_data_array = array('customers_firstname' => $firstname,
                            'customers_lastname' => $lastname,
                            'customers_email_address' => $email_address,
                            'customers_telephone' => $telephone,
                            'customers_fax' => $fax,
                            'customers_newsletter' => $newsletter,
                            'customers_password' => tep_encrypt_password($password),
                            'point' => 0,
                            'customers_default_address_id' => 1,
                            'site_id' => $site_id,
                            'customers_guest_chk' => 1);

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

    tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . tep_db_input($customer_id) . "', '0', now())");
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, 'customer_id=' . $customer_id, 'SSL'));
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
