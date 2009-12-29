<?php
/*
  $Id: checkout_new_address.php,v 1.5 2004/05/16 02:55:05 suzukawa Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  if (!isset($process)) $process = false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;
  $address_form->setBoldTitle(true);

  // gender
  $male   = ($gender == 'm') ? true : false;
  $female = ($gender == 'f') ? true : false;
  if ($process == true) {
      if ($gender_error == true) {
        $a_value = tep_draw_radio_field('gender', 'm', $male) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;'
         . tep_draw_radio_field('gender', 'f', $female) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . ENTRY_GENDER_ERROR;
      } else {
        $a_value =  (($gender == 'm') ? MALE : FEMALE)
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
    $a_value = tep_draw_input_field('firstname') . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
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
    $a_value = tep_draw_input_field('lastname') . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);
  
  // firstname_f
  if ($process == true) {
    if ($firstname_error == true) {
      $a_value = tep_draw_input_field('firstname_f') . '&nbsp;' . ENTRY_FIRST_NAME_F_ERROR;
    } else {
      $a_value = $firstname_f . tep_draw_hidden_field('firstname_f');
    }
  } else {
    $a_value = tep_draw_input_field('firstname_f') . '&nbsp;' . ENTRY_FIRST_NAME_F_TEXT;
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
    $a_value = tep_draw_input_field('lastname_f') . '&nbsp;' . ENTRY_LAST_NAME_F_TEXT;
  }
  $address_form->setFormLine('lastname_f',ENTRY_LAST_NAME_F,$a_value);

  // company
  if ($process == true) {
      if ($company_error == true) {
        $a_value = tep_draw_input_field('company') . '&nbsp;' . ENTRY_COMPANY_ERROR;
      } else {
        $a_value = $company . tep_draw_hidden_field('company');
      }
  } else {
      $a_value = tep_draw_input_field('company') . '&nbsp;' . ENTRY_COMPANY_TEXT;
  }
  $address_form->setFormLine('company',ENTRY_COMPANY,$a_value);

  // street_address
  if ($process == true) {
    if ($street_address_error == true) {
      $a_value = tep_draw_input_field('street_address') . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
    } else {
      $a_value = $street_address . tep_draw_hidden_field('street_address');
    }
  } else {
    $a_value = tep_draw_input_field('street_address') . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT;
  }
  $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$a_value);

  // suburb
  if ($process == true) {
      $a_value = $suburb . tep_draw_hidden_field('suburb');
  } else {
      $a_value = tep_draw_input_field('suburb') . '&nbsp;' . ENTRY_SUBURB_TEXT;
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
    $a_value = tep_draw_input_field('postcode') . '&nbsp;' . ENTRY_POST_CODE_TEXT;
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
    $a_value = tep_draw_input_field('city') . '&nbsp;' . ENTRY_CITY_TEXT;
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
      $state = tep_get_zone_name($country, $zone_id, $state);
      if ($address_form->inForm('country')) {
          $a_value = tep_draw_input_field('state', $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      } else {
          $a_value = tep_get_zone_list('state', isset($country) ? $country : STORE_COUNTRY, $state) . '&nbsp;' . ENTRY_STATE_TEXT;
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
    $a_value = tep_get_country_list('country', (isset($country) ? $country : STORE_COUNTRY)) . '&nbsp;' . ENTRY_COUNTRY_TEXT;
  }
  $address_form->setFormLine('country',ENTRY_COUNTRY,$a_value);
  $a_hidden = tep_draw_hidden_field('country',(isset($country) ? $country : STORE_COUNTRY));
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
    $a_value = tep_draw_input_field('telephone') . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;
  }
  $address_form->setFormLine('telephone',ENTRY_TELEPHONE_NUMBER,$a_value);

  // start print
  echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
  $address_form->printCategoryPersonal();
  $address_form->printCategoryCompany();
  $address_form->printCategoryAddress();
  echo '</table>';
