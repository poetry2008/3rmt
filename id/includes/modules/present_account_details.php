<?php
/*
  $Id$

*/

  $newsletter_array = array(array('id' => '1',
                                  'text' => ENTRY_NEWSLETTER_YES),
                            array('id' => '0',
                                  'text' => ENTRY_NEWSLETTER_NO));

  if (!isset($is_read_only)) $is_read_only = false;
  if (!isset($processed)) $processed = false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;

  // gender
  $male   = ($account['customers_gender'] == 'm') ? true : false;
  $female = ($account['customers_gender'] == 'f') ? true : false;
  if ($is_read_only == true) {
      $a_value = ($account['customers_gender'] == 'm') ? MALE : FEMALE;
  } elseif ($error == true) {
      if ($entry_gender_error == true) {
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
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_firstname'],false,true);
  } elseif ($error == true) {
      if ($entry_firstname_error == true) {
          $a_value = tep_draw_input_field('firstname','' , "class='input_text'") . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
      } else {
          $a_value = $firstname . tep_draw_hidden_field('firstname');
      }
  } else {
      $a_value = tep_draw_input_field('firstname', $account['customers_firstname'] , "class='input_text'") . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_lastname'],false,true);
  } elseif ($error == true) {
      if ($entry_lastname_error == true) {
          $a_value = tep_draw_input_field('lastname','' , "class='input_text'") . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
      } else {
          $a_value = $lastname . tep_draw_hidden_field('lastname');
      }
  } else {
      $a_value = tep_draw_input_field('lastname', $account['customers_lastname'] , "class='input_text'") . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);

  // dob
  if ($is_read_only == true) {
      $a_value = tep_date_short($account['customers_dob']);
  } elseif ($error == true) {
      if ($entry_date_of_birth_error == true) {
          $a_value = tep_draw_input_field('dob','' , "class='input_text'") . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
      //18歳未満登録禁止処理
    } elseif($entry_date_of_birth_error2 == true) {
          $a_value = tep_draw_input_field('dob','' , "class='input_text'") . '&nbsp;' . ENTRY_REGISTERED_AGE_ERROR;
    } else {
      $a_value = tep_draw_input_field('dob','' , "class='input_text'");
      }
  } else {
      $a_value = tep_draw_input_field('dob', tep_date_short($account['customers_dob']) , "class='input_text'") . '&nbsp;' . ENTRY_DATE_OF_BIRTH_TEXT;
  }
  $address_form->setFormLine('dob',ENTRY_DATE_OF_BIRTH,$a_value);

  // email_address
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_email_address'],false,true);
  } elseif ($error == true) {
      if ($entry_email_address_error == true) {
          $a_value = tep_draw_input_field('email_address','' , "class='input_text'") . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
      } elseif ($entry_email_address_check_error == true) {
          $a_value = tep_draw_input_field('email_address','' , "class='input_text'") . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      } elseif ($entry_email_address_exists == true) {
          $a_value = tep_draw_input_field('email_address','' , "class='input_text'") . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address', $account['customers_email_address'] , "class='input_text'") . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);
  // street_address
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['entry_street_address'],false,true);
  } elseif ($error == true) {
      if ($entry_street_address_error == true) {
          $a_value = tep_draw_input_field('street_address','' , "class='input_text'") . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
      } else {
          $a_value = $street_address . tep_draw_hidden_field('street_address');
      }
  } else {
      $a_value = tep_draw_input_field('street_address', $account['entry_street_address'] , "class='input_text'") . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT;
  }
  $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$a_value);

  // suburb
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['entry_suburb'],false,true);
  } elseif ($error == true) {
      if ($entry_suburb_error == true) {
          $a_value = tep_draw_input_field('suburb','' , "class='input_text'") . '&nbsp;' . ENTRY_SUBURB_ERROR;
      } else {
          $a_value = $suburb . tep_draw_hidden_field('suburb');
      }
  } else {
      $a_value = tep_draw_input_field('suburb', $account['entry_suburb'] , "class='input_text'") . '&nbsp;' . ENTRY_SUBURB_TEXT;
  }
  $address_form->setFormLine('suburb',ENTRY_SUBURB,$a_value);

  // postcode
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['entry_postcode'],false,true);
  } elseif ($error) {
      if ($entry_post_code_error == true) {
          $a_value = tep_draw_input_field('postcode','' , "class='input_text'") . '&nbsp;' . ENTRY_POST_CODE_ERROR;
      } else {
          $a_value = $postcode . tep_draw_hidden_field('postcode');
      }
  } else {
      $a_value = tep_draw_input_field('postcode', $account['entry_postcode'] , "class='input_text'") . '&nbsp;' . ENTRY_POST_CODE_TEXT;
  }
  $address_form->setFormLine('postcode',ENTRY_POST_CODE,$a_value);

  // city
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['entry_city'],false,true);
  } elseif ($error) {
      if ($entry_city_error == true) {
          $a_value = tep_draw_input_field('city' ,'', "class='input_text'") . '&nbsp;' . ENTRY_CITY_ERROR;
      } else {
          $a_value = $city . tep_draw_hidden_field('city');
      }
  } else {
      $a_value = tep_draw_input_field('city', $account['entry_city'] , "class='input_text'") . '&nbsp;' . ENTRY_CITY_TEXT;
  }
  $address_form->setFormLine('city',ENTRY_CITY,$a_value);

  // state
  if ($is_read_only == true) {
      $a_value = tep_get_zone_name($account['entry_country_id'], $account['entry_zone_id'], $account['entry_state']);
  } elseif ($error == true) {
      if ($entry_state_error == true) {
        if ($entry_state_has_zones == true) {
          $a_value = tep_get_zone_list('state', $country) . '&nbsp;' . ENTRY_STATE_ERROR;
        } else {
          $a_value = tep_draw_input_field('state','' , "class='input_text'") . '&nbsp;' . ENTRY_STATE_ERROR;
        }
      } else {
        $state = tep_get_zone_name($country, $zone_id, $state);
        $a_value = $state . tep_draw_hidden_field('zone_id') . tep_draw_hidden_field('state');
      }
  } else {
      $state = tep_get_zone_name($account['entry_country_id'], $account['entry_zone_id'], $account['entry_state']);
      if ($address_form->inForm('country')) {
          $a_value = tep_draw_input_field('state', $state , "class='input_text'") . '&nbsp;' . ENTRY_STATE_TEXT;
      } else {
          $a_value = tep_get_zone_list('state', $account['entry_country_id'] ? $account['entry_country_id'] : STORE_COUNTRY, $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      }
  }
  $address_form->setFormLine('state',ENTRY_STATE,$a_value);

  if ($account['entry_country_id']) { $country = $account['entry_country_id']; }
  else if (!$country) { $country = STORE_COUNTRY; } 
    echo tep_draw_hidden_field('country', STORE_COUNTRY);

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
<?php
  $address_form->printCategoryPersonal();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
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
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_CONTACT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main">&nbsp;
<?php
  if ($is_read_only == true) {
    echo tep_output_string($account['customers_telephone'],false,true);
  } elseif ($error == true) {
    if ($entry_telephone_error == true) {
      echo tep_draw_input_field('telephone','', "class='input_text'") . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
    } else {
      echo $telephone . tep_draw_hidden_field('telephone');
    }
  } else {
    echo tep_draw_input_field('telephone', $account['customers_telephone'], "class='input_text'") . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;
  }
?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main">&nbsp;
<?php
  if ($is_read_only == true) {
    echo tep_output_string($account['customers_fax'],false,true);
  } elseif ($processed == true) {
    echo $fax . tep_draw_hidden_field('fax');
  } else {
    echo tep_draw_input_field('fax', $account['customers_fax'], "class='input_text'") . '&nbsp;' . ENTRY_FAX_NUMBER_TEXT;
  }
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php
  if ($is_read_only == false) {
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_PASSWORD; ?></td>
  </tr>
  <tr>
    <td class="main">※このまま会員登録をご希望の場合はパスワードを入力してください。</td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_PASSWORD; ?></td>
            <td class="main">&nbsp;
<?php

    if ($error == true && !empty($password)) {
      if ($entry_password_error == true) {
        echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_ERROR;
      } else {
        echo PASSWORD_HIDDEN . tep_draw_hidden_field('password') . tep_draw_hidden_field('confirmation');
      }
    } else {
      echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
    }

?></td>
          </tr>
<?php
    if ( ($error == false) || ($entry_password_error == true) ) {
?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
            <td class="main">&nbsp;
<?php
     echo tep_draw_password_field('confirmation', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_CONFIRMATION_TEXT;
?></td>
          </tr>
<?php
    }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php
 }
?>
</table>
