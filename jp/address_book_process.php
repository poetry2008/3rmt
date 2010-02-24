<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if ( ($navigation->snapshot['page'] != FILENAME_ADDRESS_BOOK) || ($navigation->snapshot['page'] != FILENAME_CHECKOUT_ADDRESS) ) {
    $navigation->set_path_as_snapshot(1);
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'remove') && tep_not_null($HTTP_GET_VARS['entry_id']) ) {
    $entry_id = tep_db_prepare_input($HTTP_GET_VARS['entry_id']);
//ccdd
    tep_db_query("
DELETE FROM
 " . TABLE_ADDRESS_BOOK . " 
WHERE address_book_id = '" . tep_db_input($entry_id) . "' 
AND customers_id = '" . $customer_id . "'");
//ccdd
    tep_db_query(
"UPDATE " . TABLE_ADDRESS_BOOK . " 
SET address_book_id = address_book_id - 1
WHERE address_book_id > " . tep_db_input($entry_id)  . " AND customers_id = '" . $customer_id . "'"
);

    tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
  }

// Post-entry error checking when updating or adding an entry
  $process = false;
  if (isset($HTTP_POST_VARS['action']) && (($HTTP_POST_VARS['action'] == 'process') || ($HTTP_POST_VARS['action'] == 'update'))) {
    $process = true;
    $error = false;

    // tamura 2002/12/30 「全角」英数字を「半角」に変換
    $HTTP_POST_VARS['postcode'] = tep_an_zen_to_han($HTTP_POST_VARS['postcode']);

    $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);
    $company = tep_db_prepare_input($HTTP_POST_VARS['company']);
    $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
    $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
	
	$firstname_f = tep_db_prepare_input($HTTP_POST_VARS['firstname_f']);
    $lastname_f = tep_db_prepare_input($HTTP_POST_VARS['lastname_f']);
	
    $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);
    $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);
    $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);
    $city = tep_db_prepare_input($HTTP_POST_VARS['city']);
    $country = tep_db_prepare_input($HTTP_POST_VARS['country']);
    $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);
    $state = tep_db_prepare_input($HTTP_POST_VARS['state']);
// 2003-06-06 add_telephone
    $telephone = tep_db_prepare_input($HTTP_POST_VARS['telephone']);

    if (ACCOUNT_GENDER == 'true') {
      if (($gender == 'm') || ($gender == 'f')) {
        $gender_error = false;
      } else {
        $gender_error = true;
        $error = true;
      }
    }

    if (ACCOUNT_COMPANY == 'true') {
      if (strlen($company) < ENTRY_COMPANY_MIN_LENGTH) {
        $company_error = true;
        $error = true;
      } else {
        $company_error = false;
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $firstname_error = true;
      $error = true;
    } else {
      $firstname_error = false;
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $lastname_error = true;
      $error = true;
    } else {
      $lasttname_error = false;
    }
	
	if (strlen($firstname_f) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $firstname_f_error = true;
      $error = true;
    } else {
      $firstname_f_error = false;
    }

    if (strlen($lastname_f) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $lastname_f_error = true;
      $error = true;
    } else {
      $lasttname_f_error = false;
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $street_address_error = true;
      $error = true;
    } else {
      $street_address_error = false;
    }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $postcode_error = true;
      $error = true;
    } else {
      $postcode_error = false;
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
      $city_error = true;
      $error = true;
    } else {
      $city_error = false;
    }

    if (!$country) {
      $country_error = true;
      $error = true;
    } else {
      $country_error = false;
    }

// 2003-06-06 add_telephone
    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $telephone_error = true;
      $error = true;
    } else {
      $telephone_error = false;
    }

    if (ACCOUNT_STATE == 'true') {
      if ($entry_country_error == true) {
        $entry_state_error = true;
      } else {
        $zone_id = 0;
        $entry_state_error = false;
//ccdd
        $check_query = tep_db_query("SELECT count(*) as total FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . tep_db_input($country) . "'");
        $check_value = tep_db_fetch_array($check_query);
        $entry_state_has_zones = ($check_value['total'] > 0);
        if ($entry_state_has_zones == true) {
//ccdd
          $zone_query = tep_db_query("SELECT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . tep_db_input($country) . "' AND zone_name = '" . tep_db_input($state) . "'");
          if (tep_db_num_rows($zone_query) == 1) {
            $zone_values = tep_db_fetch_array($zone_query);
            $zone_id = $zone_values['zone_id'];
          } else {
//ccdd
            $zone_query = tep_db_query("SELECT zone_id FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . tep_db_input($country) . "' AND zone_code = '" . tep_db_input($state) . "'");
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

    if ($error == false) {
// 2003-06-06 add_telephone
      $sql_data_array = array('entry_firstname' => $firstname,
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

      $entry_id = tep_db_prepare_input($HTTP_POST_VARS['entry_id']);
      if ($HTTP_POST_VARS['action'] == 'update') {
        // ccdd
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '" . tep_db_input($entry_id) . "' AND customers_id ='" . tep_db_input($customer_id) . "'");
      } else {
        $sql_data_array['customers_id'] = $customer_id;
        $sql_data_array['address_book_id'] = $entry_id;
        // ccdd
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

// Go back to where we came from
        if (sizeof($navigation->snapshot) > 0) {
          $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
          $navigation->clear_snapshot();

          tep_redirect($origin_href);
        }
      }

      tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'modify') && tep_not_null($HTTP_GET_VARS['entry_id'])) {
// 2003-06-06 add_telephone
//ccdd
    $entry_query = tep_db_query("
SELECT 
    entry_gender, 
    entry_company,
    entry_firstname, 
    entry_lastname, 
    entry_firstname_f, 
    entry_lastname_f, 
    entry_street_address, 
    entry_suburb, 
    entry_postcode, 
    entry_city, 
    entry_state, 
    entry_zone_id, 
    entry_country_id,
    entry_telephone 
FROM " . TABLE_ADDRESS_BOOK . " 
WHERE 
    customers_id = '" . $customer_id . "' 
AND 
   address_book_id = '" . $HTTP_GET_VARS['entry_id'] . "'"
);
    $entry = tep_db_fetch_array($entry_query);
  } else {
    $entry = array('entry_country_id' => STORE_COUNTRY);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADDRESS_BOOK_PROCESS);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

  if ( (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'modify')) || (isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'update') && tep_not_null($HTTP_POST_VARS['entry_id'])) ) {
    $breadcrumb->add(NAVBAR_TITLE_MODIFY_ENTRY, tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'action=modify&entry_id=' . ((isset($HTTP_GET_VARS['entry_id'])) ? $HTTP_GET_VARS['entry_id'] : $HTTP_POST_VARS['entry_id']), 'SSL'));
  } else {
    $breadcrumb->add(NAVBAR_TITLE_ADD_ENTRY, tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
  }
?>
<?php page_head();?>
<script type="text/javascript"><!--
function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var firstname = document.add_entry.firstname.value;
  var lastname = document.add_entry.lastname.value;
  
  var firstname_f = document.add_entry.firstname_f.value;
  var lastname_f = document.add_entry.lastname_f.value;
  
  var street_address = document.add_entry.street_address.value;
  var postcode = document.add_entry.postcode.value;
  var city = document.add_entry.city.value;
<?php // 2003-06-06 add_telephone ?>
  var telephone = document.add_entry.telephone.value;

<?php
 if (ACCOUNT_GENDER == 'true') {
?>
  if (document.add_entry.elements['gender'].type != "hidden") {
    if (document.add_entry.gender[0].checked || document.add_entry.gender[1].checked) {
    } else {
      error_message = error_message + "<?php echo JS_GENDER; ?>";
      error = 1;
    }
  }
<?php
 }
?>
  if (firstname == "" || firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (lastname == "" || lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }
  
  if (firstname_f == "" || firstname_f.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME_F; ?>";
    error = 1;
  }

  if (lastname_f == "" || lastname_f.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME_F; ?>";
    error = 1;
  }

  if (street_address == "" || street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_ADDRESS; ?>";
    error = 1;
  }

  if (postcode == "" || postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_POST_CODE; ?>";
    error = 1;
  }

  if (city == "" || city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_CITY; ?>";
    error = 1;
  }
<?php
  if (ACCOUNT_STATE == 'true') {
?>
  if (document.add_entry.state.value == "" || document.add_entry.state.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
     error_message = error_message + "<?php echo JS_STATE; ?>";
     error = 1;
  }
<?php
  }
?>

  if (document.add_entry.country.value == 0) {
    error_message = error_message + "<?php echo JS_COUNTRY; ?>";
    error = 1;
  }

<?php // 2003-06-06 add_telephone ?>
  if (telephone == '' || telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_TELEPHONE; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
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
      <td valign="top" id="contents"><?php echo tep_draw_form('add_entry', tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"'); ?> 
        <h1 class="pageHeading"><?php echo (isset($HTTP_GET_VARS['action']) && $HTTP_GET_VARS['action'] == 'modify') ? HEADING_TITLE_MODIFY_ENTRY : HEADING_TITLE_ADD_ENTRY; ?></h1> 
        
        <div> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td> <?php include(DIR_WS_MODULES . 'address_book_details.php'); ?></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'modify') && tep_not_null($HTTP_GET_VARS['entry_id'])) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="2" cellpadding="0"> 
                  <tr> 
                    <td class="main"><?php echo tep_draw_hidden_field('action', 'update') . tep_draw_hidden_field('entry_id', $HTTP_GET_VARS['entry_id']) . '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td class="main" align="center"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'action=remove&entry_id=' . $HTTP_GET_VARS['entry_id'], 'SSL') . '">' . tep_image_button('button_delete.gif', IMAGE_BUTTON_DELETE) . '</a>'; ?></td> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
    } elseif (isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'update') && tep_not_null($HTTP_POST_VARS['entry_id'])) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="2" cellpadding="0"> 
                  <tr> 
                    <td class="main"><?php echo tep_draw_hidden_field('action', 'update') . tep_draw_hidden_field('entry_id', $entry_id) . '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
    } else {
      if (sizeof($navigation->snapshot) > 0) {
        $back_link = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
      } else {
        $back_link = tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
      }
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><?php echo '<a href="' . $back_link . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td align="right" class="main"><?php echo tep_draw_hidden_field('entry_id', (isset($HTTP_GET_VARS['entry_id']) ? $HTTP_GET_VARS['entry_id'] : $entry_id)) . tep_draw_hidden_field('action', 'process') . tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
    }
?> 
          </table> 
          </form> 
        </div></td> 
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
