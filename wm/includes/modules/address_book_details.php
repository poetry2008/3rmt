<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  if (!isset($process)) $process = false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;

  // gender
  $male = ($entry['entry_gender'] == 'm') ? true : false;
  $female = ($entry['entry_gender'] == 'f') ? true : false;
  if ($process == true) {
      if ($gender_error == true) {
        $a_value = tep_draw_radio_field('gender', 'm', $male) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;'
         . tep_draw_radio_field('gender', 'f', $female) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . ENTRY_GENDER_ERROR;
      } else {
        $a_value = (($gender == 'm') ? MALE : FEMALE)
                   . tep_draw_hidden_field('gender');
      }
  } else {
      $a_value = tep_draw_radio_field('gender', 'm', $male) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;'
        . tep_draw_radio_field('gender', 'f', $female) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . ENTRY_GENDER_TEXT;
  }
  $address_form->setFormLine('gender',ENTRY_GENDER,$a_value);

  // firstname
  if ($process == true) {
    if ($firstname_error == true) {
      $a_value = tep_draw_input_field('firstname') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
    } else {
      $a_value = $firstname . tep_draw_hidden_field('firstname');
    }
  } else {
    $a_value = tep_draw_input_field('firstname', $entry['entry_firstname']) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname
  if ($process == true) {
    if ($lastname_error == true) {
      $a_value = tep_draw_input_field('lastname') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
    } else {
      $a_value = $lastname . tep_draw_hidden_field('lastname');
    }
  } else {
    $a_value = tep_draw_input_field('lastname', $entry['entry_lastname']) . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);

  // company
  if ($process == true) {
      if ($company_error == true) {
        $a_value = tep_draw_input_field('company') . '&nbsp;' . ENTRY_COMPANY_ERROR;
      } else {
        $a_value = $company . tep_draw_hidden_field('company');
      }
    } else {
      $a_value = tep_draw_input_field('company', $entry['entry_company']) . '&nbsp;' . ENTRY_COMPANY_TEXT;
  }
  $address_form->setFormLine('company',ENTRY_COMPANY,$a_value);
  
  // firstname_f
  if ($process == true) {
    if ($firstname_error == true) {
      $a_value = tep_draw_input_field('firstname_f') . '&nbsp;' . ENTRY_FIRST_NAME_F_ERROR;
    } else {
      $a_value = $firstname_f . tep_draw_hidden_field('firstname_f');
    }
  } else {
    $a_value = tep_draw_input_field('firstname_f', $entry['entry_firstname_f']) . '&nbsp;' . ENTRY_FIRST_NAME_F_TEXT;
  }
  $address_form->setFormLine('firstname_f',ENTRY_FIRST_NAME_F,$a_value);

  // lastname_f
  if ($process == true) {
    if ($lastname_error == true) {
      $a_value = tep_draw_input_field('lastname_f') . '&nbsp;' . ENTRY_LAST_NAME_F_ERROR;
    } else {
      $a_value = $lastname_f . tep_draw_hidden_field('lastname_f');
    }
  } else {
    $a_value = tep_draw_input_field('lastname_f', $entry['entry_lastname_f']) . '&nbsp;' . ENTRY_LAST_NAME_F_TEXT;
  }
  $address_form->setFormLine('lastname_f',ENTRY_LAST_NAME_F,$a_value);

  // street_address
  if ($process == true) {
    if ($street_address_error == true) {
      $a_value = tep_draw_input_field('street_address') . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
    } else {
      $a_value = $street_address . tep_draw_hidden_field('street_address');
    }
  } else {
    $a_value = tep_draw_input_field('street_address', $entry['entry_street_address']) . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT;
  } 
  $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$a_value);

  // suburb
  if ($process == true) {
      $a_value = $suburb . tep_draw_hidden_field('suburb');
  } else {
      $a_value = tep_draw_input_field('suburb', $entry['entry_suburb']) . '&nbsp;' . ENTRY_SUBURB_TEXT;
  } 
  $address_form->setFormLine('suburb',ENTRY_SUBURB,$a_value);

  // postcode
  if ($process == true) {
    if ($postcode_error == true) {
      $a_value = tep_draw_input_field('postcode') . '&nbsp;' . ENTRY_POST_CODE_ERROR;
    } else {
      $a_value = $postcode . tep_draw_hidden_field('postcode');
    }
  } else {
    $a_value = tep_draw_input_field('postcode', $entry['entry_postcode']) . '&nbsp;' . ENTRY_POST_CODE_TEXT;
  }
  $address_form->setFormLine('postcode',ENTRY_POST_CODE,$a_value);

  // city
  if ($process == true) {
    if ($city_error == true) {
      $a_value = tep_draw_input_field('city') . '&nbsp;' . ENTRY_CITY_ERROR;
    } else {
      $a_value = $city . tep_draw_hidden_field('city');
    }
  } else {
    $a_value = tep_draw_input_field('city', $entry['entry_city']) . '&nbsp;' . ENTRY_CITY_TEXT;
  }
  $address_form->setFormLine('city',ENTRY_CITY,$a_value);

  // state
  if ($process == true) {
      if ($entry_state_error == true) {
        if ($entry_state_has_zones == true) {
          $a_value = tep_get_zone_list('state', $country) . '&nbsp;' . ENTRY_STATE_ERROR;
        } else {
          $a_value = tep_draw_input_field('state') . '&nbsp;' . ENTRY_STATE_ERROR;
        }
      } else {
        $state = tep_get_zone_name($country, $zone_id, $state);
        $a_value = $state . tep_draw_hidden_field('zone_id') . tep_draw_hidden_field('state');
      }
  } else {
      $state = tep_get_zone_name($entry['entry_country_id'], $entry['entry_zone_id'], $entry['entry_state']);
      if ($address_form->inForm('country')) {
          $a_value = tep_draw_input_field('state', $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      } else {
          $a_value = tep_get_zone_list('state', $entry['entry_country_id'] ? $entry['entry_country_id'] : STORE_COUNTRY, $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      }
  } 
  $address_form->setFormLine('state',ENTRY_STATE,$a_value);

  // country
  if ($process == true) {
    if ($country_error == true) {
      $a_value = tep_get_country_list('country') . '&nbsp;' . ENTRY_COUNTRY_ERROR;
    } else {
      $a_value = tep_get_country_name($country) . tep_draw_hidden_field('country');
    }
  } else {
    $a_value = tep_get_country_list('country', $entry['entry_country_id']) . '&nbsp;' . ENTRY_COUNTRY_TEXT;
  }
  $address_form->setFormLine('country',ENTRY_COUNTRY,$a_value);
  $a_hidden = tep_draw_hidden_field('country',$entry['entry_country_id'] ? $entry['entry_country_id'] : STORE_COUNTRY);
  $address_form->setFormHidden('country',$a_hidden); // in case without country

// 2003-06-06 add_telephone
  // telephone
  if ($process == true) {
      if ($telephone_error == true) {
          $a_value = tep_draw_input_field('telephone') . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
      } else {
          $a_value = $telephone . tep_draw_hidden_field('telephone');
      }
  } else {
      $a_value = tep_draw_input_field('telephone', $entry['entry_telephone']) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;
  }
  $address_form->setFormLine('telephone',ENTRY_TELEPHONE_NUMBER,$a_value);

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
  $address_form->printCategoryPersonal();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php
  if (ACCOUNT_COMPANY == 'true') {
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
<?php
  $address_form->printCategoryCompany();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
<?php
  $address_form->printCategoryAddress();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
