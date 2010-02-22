<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

  $error = false;
  $process = false;
  if (isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'submit')) {
// process a new billing address
    if (tep_not_null($HTTP_POST_VARS['firstname']) && tep_not_null($HTTP_POST_VARS['lastname']) && tep_not_null($HTTP_POST_VARS['street_address'])) {
      $process = true;

      $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);
      $company = tep_db_prepare_input($HTTP_POST_VARS['company']);
      $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
      $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
	  
	  //add
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

      if (strlen($country) < 1) {
        $country_error = true;
        $error = true;
      } else {
        $country_error = false;
      }

      if (ACCOUNT_STATE == 'true') {
        if ($entry_country_error == true) {
          $entry_state_error = true;
        } else {
          $zone_id = 0;
          $entry_state_error = false;
          $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "'");
          $check_value = tep_db_fetch_array($check_query);
          $entry_state_has_zones = ($check_value['total'] > 0);
          if ($entry_state_has_zones == true) {
            $zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "' and zone_name = '" . tep_db_input($state) . "'");
            if (tep_db_num_rows($zone_query) == 1) {
              $zone_values = tep_db_fetch_array($zone_query);
              $zone_id = $zone_values['zone_id'];
            } else {
              $error = true;
              $entry_state_error = true;
            }
          } else {
            if (!$state) {
              $error = true;
              $entry_state_error = true;
            }
          }
        }
      }

// 2003-06-06 add_telephone
      if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $telephone_error = true;
        $error = true;
      } else {
        $telephone_error = false;
      }

      if ($error == false) {
        $next_id_query = tep_db_query("select max(address_book_id) as address_book_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "'");
        if (tep_db_num_rows($next_id_query)) {
          $next_id = tep_db_fetch_array($next_id_query);
          $entry_id = $next_id['address_book_id']+1;
        } else {
          $entry_id = 1;
        }

// 2003-06-06 add_telephone
        $sql_data_array = array('customers_id' => $customer_id,
                                'address_book_id' => $entry_id,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
								
								//add
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

        if (!tep_session_is_registered('billto')) tep_session_register('billto');

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

        $billto = $entry_id;

        if (tep_session_is_registered('payment')) tep_session_unregister('payment');

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      }
// process the selected billing destination
    } elseif (isset($HTTP_POST_VARS['address'])) {
      $reset_payment = false;
      if (tep_session_is_registered('billto')) {
        if ($billto != $HTTP_POST_VARS['address']) {
          if (tep_session_is_registered('payment')) {
            $reset_payment = true;
          }
        }
      } else {
        tep_session_register('billto');
      }

      $billto = $HTTP_POST_VARS['address'];

      $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $billto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] == '1') {
        if ($reset_payment == true) tep_session_unregister('payment');
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      } else {
        tep_session_unregister('billto');
      }
// no addresses to select from - customer decided to keep the current assigned address
    } else {
      if (!tep_session_is_registered('billto')) tep_session_register('billto');
      $billto = $customer_default_address_id;

      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
  }

// if no billing destination address was selected, use their own address as default
  if (!tep_session_is_registered('billto')) {
    $billto = $customer_default_address_id;
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT_ADDRESS);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));
?>
<?php page_head();?>
<script type="text/javascript"><!--
var selected;

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_address.address[0]) {
    document.checkout_address.address[buttonSelect].checked=true;
  } else {
    document.checkout_address.address.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var firstname = document.checkout_address.firstname.value;
  var lastname = document.checkout_address.lastname.value;
  
  var firstname_f = document.checkout_address.firstname_f.value;
  var lastname_f = document.checkout_address.lastname_f.value;
  
  var street_address = document.checkout_address.street_address.value;
  var postcode = document.checkout_address.postcode.value;
  var city = document.checkout_address.city.value;
<?php // 2003-06-06 add_telephone ?>
  var telephone = document.checkout_address.telephone.value;

  if (firstname == '' && lastname == '' && street_address == '') {
    return true;
  }

<?php
 if (ACCOUNT_GENDER == 'true') {
?>
  if (document.checkout_address.elements['gender'].type != "hidden") {
    if (document.checkout_address.gender[0].checked || document.checkout_address.gender[1].checked) {
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
  if (document.checkout_address.state.value == "" || document.checkout_address.state.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
     error_message = error_message + "<?php echo JS_STATE; ?>";
     error = 1;
  }
<?php
  }
?>

  if (document.checkout_address.country.value == 0) {
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
      <td valign="top" id="contents"><?php echo tep_draw_form('checkout_address', tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"'); ?><h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <?php
  if ($process == true) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><b><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS_PROBLEM; ?></b></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice"> 
                  <tr class="infoBoxNoticeContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td class="main" width="100%" valign="top"><?php echo TEXT_NEW_PAYMENT_ADDRESS_PROBLEM; ?></td> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
  }

  if ($process == false) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_ADDRESS; ?></b></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                  <tr class="infoBoxContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td class="main" width="50%" valign="top"><?php echo TEXT_SELECTED_PAYMENT_DESTINATION; ?></td> 
                          <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2"> 
                              <tr> 
                                <td class="main" align="center" valign="top"><?php echo '<b>' . TITLE_PAYMENT_ADDRESS . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td> 
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                <td class="main" valign="top"><?php echo tep_address_label($customer_id, $billto, true, ' ', '<br>'); ?></td> 
                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                              </tr> 
                            </table></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
    $addresses_count_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id != '" . $billto . "'");
    $addresses_count = tep_db_fetch_array($addresses_count_query);

    if ($addresses_count['total'] > 0) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><b><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></b></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                  <tr class="infoBoxContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td class="main" width="50%" valign="top"><?php echo TEXT_SELECT_OTHER_PAYMENT_DESTINATION; ?></td> 
                          <td class="main" width="50%" valign="top" align="right"><?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                        <?php
      $radio_buttons = 0;

// 2003-06-06 add_telephone
      $addresses_query = tep_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id, entry_telephone as telephone from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "'");
      while ($addresses = tep_db_fetch_array($addresses_query)) {
        $format_id = tep_get_address_format_id($addresses['country_id']);
?> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                              <?php
       if ($addresses['address_book_id'] == $billto) {
          echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        } else {
          echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        }
?> 
                              <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                <td class="main" colspan="2"><b><?php echo tep_output_string_protected(tep_get_fullname($addresses['firstname'],$addresses['lastname'])); ?></b></td> 
                                <td class="main" align="right"><?php echo tep_draw_radio_field('address', $addresses['address_book_id'], ($addresses['address_book_id'] == $billto)); ?></td> 
                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                              </tr> <tr> 
                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                <td colspan="3"><table border="0" cellspacing="0" cellpadding="2"> 
                                    <tr> 
                                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                      <td class="main"><?php echo tep_address_format($format_id, $addresses, true, ' ', ', '); ?></td> 
                                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                    </tr> 
                                  </table></td> 
                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                              </tr> 
                            </table></td> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                        <?php
        $radio_buttons++;
      }
?> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
    }
  }
  if ($addresses_count['total'] < MAX_ADDRESS_BOOK_ENTRIES) {
?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><b><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS; ?></b></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                  <tr class="infoBoxContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td class="main" width="100%" valign="top"><?php echo TEXT_CREATE_NEW_PAYMENT_ADDRESS; ?></td> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                        <tr> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                              <tr> 
                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                                <td> <?php require(DIR_WS_MODULES . 'checkout_new_address.php'); ?> </td> 
                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                              </tr> 
                            </table></td> 
                          <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
  }
?> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                  <tr class="infoBoxContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                          <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                          <td class="main" align="right"><?php echo tep_draw_hidden_field('action', 'submit') . tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
  if ($process == true) {
?> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
            </tr> 
            <?php
  }
?> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                        <tr> 
                          <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                        <tr> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                          <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                    <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                        <tr> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                  <tr> 
                    <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td> 
                    <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
                    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
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
