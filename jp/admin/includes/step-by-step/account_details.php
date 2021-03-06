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
  if (!isset($error)) $error= false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;


  // 性别 
  $male   = (isset($account['customers_gender']) && $account['customers_gender'] == 'm') ? true : false;
  $female = (isset($account['customers_gender']) && $account['customers_gender'] == 'f') ? true : false;
  if ($is_read_only == true) {
      $a_value = (isset($account['customers_gender']) && $account['customers_gender'] == 'm') ? MALE : FEMALE;
  } elseif ($error == true) {
      if (isset($entry_gender_error) && $entry_gender_error == true) {
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

  // firstname 名字1
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_firstname'],false,true);
  } elseif ($error == true) {
      if ($entry_firstname_error == true) {
          $a_value = tep_draw_input_field('firstname') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
      } else {
          $a_value = $firstname . tep_draw_hidden_field('firstname');
      }
  } else {
      $a_value = tep_draw_input_field('firstname', isset($account['customers_firstname']) ? $account['customers_firstname'] : '') . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname 名字2
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_lastname'],false,true);
  } elseif ($error == true) {
      if ($entry_lastname_error == true) {
          $a_value = tep_draw_input_field('lastname') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
      } else {
          $a_value = $lastname . tep_draw_hidden_field('lastname');
      }
  } else {
      $a_value = tep_draw_input_field('lastname', isset($account['customers_lastname'])?$account['customers_lastname']:'') . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);

  // dob
  if ($is_read_only == true) {
      $a_value = tep_date_short($account['customers_dob']);
  } elseif ($error == true) {
      if (isset($entry_date_of_birth_error) && $entry_date_of_birth_error == true) {
          $a_value = tep_draw_input_field('dob') . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
      } else {
          $a_value = $dob . tep_draw_hidden_field('dob');
      }
  } else {
      $a_value = tep_draw_input_field('dob', tep_date_short(isset($account['customers_dob'])?$account['customers_dob']:'')) . '&nbsp;' . ENTRY_DATE_OF_BIRTH_TEXT;
  }
  $address_form->setFormLine('dob',ENTRY_DATE_OF_BIRTH,$a_value);

  // 邮箱地址
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['customers_email_address'])?$account['customers_email_address']:'',false,true);
  } elseif ($error == true) {
      if ($entry_email_address_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
      } elseif ($entry_email_address_check_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      } elseif ($entry_email_address_exists == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address', isset($account['customers_email_address'])?$account['customers_email_address']:'') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);

  // 公司 
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['entry_company'])?$account['entry_company']:'',false,true);
    } elseif ($error == true) {
      if (isset($entry_company_error) && $entry_company_error == true) {
        $a_value = tep_draw_input_field('company') . '&nbsp;' . ENTRY_COMPANY_ERROR;
      } else {
        $a_value = $company . tep_draw_hidden_field('company');
      }
    } else {
      $a_value = tep_draw_input_field('company', isset($account['entry_company'])?$account['entry_company']:'') . '&nbsp;' . ENTRY_COMPANY_TEXT;
  }
  $address_form->setFormLine('company',ENTRY_COMPANY,$a_value);

  //  地址（街道）
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['entry_street_address'])?$account['entry_street_address']:'',false,true);
  } elseif ($error == true) {
      if (isset($entry_street_address_error) && $entry_street_address_error == true) {
          $a_value = tep_draw_input_field('street_address') . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
      } else {
          $a_value = $street_address . tep_draw_hidden_field('street_address');
      }
  } else {
      $a_value = tep_draw_input_field('street_address', isset($account['entry_street_address'])?$account['entry_street_address']:'') . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT;
  }
  $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$a_value);

  // suburb
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['entry_suburb'])?$account['entry_suburb']:'',false,true);
  } elseif ($error == true) {
      if (isset($entry_suburb_error) && $entry_suburb_error == true) {
          $a_value = tep_draw_input_field('suburb') . '&nbsp;' . ENTRY_SUBURB_ERROR;
      } else {
          $a_value = $suburb . tep_draw_hidden_field('suburb');
      }
  } else {
      $a_value = tep_draw_input_field('suburb', isset($account['entry_suburb'])?$account['entry_suburb']:'') . '&nbsp;' . ENTRY_SUBURB_TEXT;
  }
  $address_form->setFormLine('suburb',ENTRY_SUBURB,$a_value);

  //  邮编
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['entry_postcode'])?$account['entry_postcode']:'',false,true);
  } elseif ($error) {
      if (isset($entry_post_code_error ) && $entry_post_code_error == true) {
          $a_value = tep_draw_input_field('postcode') . '&nbsp;' . ENTRY_POST_CODE_ERROR;
      } else {
          $a_value = $postcode . tep_draw_hidden_field('postcode');
      }
  } else {
      $a_value = tep_draw_input_field('postcode', isset($account['entry_postcode'])?$account['entry_postcode']:'') . '&nbsp;' . ENTRY_POST_CODE_TEXT;
  }
  $address_form->setFormLine('postcode',ENTRY_POST_CODE,$a_value);

  // 城市
  if ($is_read_only == true) {
      $a_value = tep_output_string(isset($account['entry_city'])?$account['entry_city']:'',false,true);
  } elseif ($error) {
      if (isset($entry_city_error ) && $entry_city_error == true) {
          $a_value = tep_draw_input_field('city') . '&nbsp;' . ENTRY_CITY_ERROR;
      } else {
          $a_value = $city . tep_draw_hidden_field('city');
      }
  } else {
      $a_value = tep_draw_input_field('city', isset($account['entry_city'])?$account['entry_city']:'') . '&nbsp;' . ENTRY_CITY_TEXT;
  }
  $address_form->setFormLine('city',ENTRY_CITY,$a_value);

  // state
  if ($is_read_only == true) {
      $a_value = tep_get_zone_name($account['entry_country_id'], $account['entry_zone_id'], $account['entry_state']);
  } elseif ($error == true) {
      if (isset($entry_state_error) && $entry_state_error == true) {
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
      $state = '';
      if ($address_form->inForm('country')) {
          $a_value = tep_draw_input_field('state', $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      } else {
          $a_value = tep_get_zone_list('state', $account['entry_country_id'] ? $account['entry_country_id'] : STORE_COUNTRY, $state) . '&nbsp;' . ENTRY_STATE_TEXT;
      }
  }
  $address_form->setFormLine('state',ENTRY_STATE,$a_value);


	if (isset($account['entry_country_id']) && $account['entry_country_id']) { $country = $account['entry_country_id']; }
	else if (!$country) { $country = STORE_COUNTRY; } 
	  if ($is_read_only == true) {
	    $a_value = tep_get_country_name($account['entry_country_id']);
	  } elseif ($error == true) {
	    if (isset($entry_country_error) && $entry_country_error == true) {
	      $a_value = tep_get_country_list('country') . '&nbsp;' . ENTRY_COUNTRY_ERROR;
	    } else {
	      $a_value = tep_get_country_name($country) . tep_draw_hidden_field('country');
	    }
	  } else {
	    $a_value = tep_get_country_list('country', $account['entry_country_id']) . '&nbsp;' . ENTRY_COUNTRY_TEXT;
	  }
	  $address_form->setFormLine('country',ENTRY_COUNTRY,$a_value);
	  $a_hidden = tep_draw_hidden_field('country',$country);
	  $address_form->setFormHidden('country',$a_hidden); 
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">





  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_SITE; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table cellspacing="0" cellpadding="2" border="0">
        <tbody>
        <tr>
        <td class="main"><?php echo ENTRY_SITE;?>:</td>
        <td class="main">&nbsp;
<?php
  if ($is_read_only == true) {
    echo isset($account['site_id']) ? tep_get_site_romaji_by_id($account['site_id']) : '';
  } elseif ($error == true) {
    if (isset($entry_site_id_error ) && $entry_site_id_error == true) {
      echo tep_site_pull_down_menu() . '&nbsp;' . ENTRY_SITE_ERROR;
    } else {
      echo tep_site_pull_down_menu(isset($account['site_id']) ? $account['site_id'] : '');
    }
  } else {
    echo tep_site_pull_down_menu(isset($account['site_id']) ? $account['site_id'] : '');
  }
?></td>
</tr></tbody></table></td>
      </tr>
    </table></td>
  </tr>



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

  
<!--

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
      echo tep_draw_input_field('telephone') . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
    } else {
      echo $telephone . tep_draw_hidden_field('telephone');
    }
  } else {
    echo tep_draw_input_field('telephone', $account['customers_telephone']) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;
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
    echo tep_draw_input_field('fax', $account['customers_fax']) . '&nbsp;' . ENTRY_FAX_NUMBER_TEXT;
  }
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>

-->

  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_OPTIONS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_NEWSLETTER; ?></td>
            <td class="main">&nbsp;
<?php
  if ($is_read_only == true) {
    if ($account['customers_newsletter'] == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
  } elseif ($processed == true) {
    if ($newsletter == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
    echo tep_draw_hidden_field('newsletter');  
  } else {
    echo tep_draw_pull_down_menu('newsletter', $newsletter_array, isset($account['customers_newsletters'])?$account['customers_newsletter']:'') . '&nbsp;' . ENTRY_NEWSLETTER_TEXT;
  }
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>

<!--

<?php
  if ($is_read_only == false) {
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_PASSWORD; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_PASSWORD; ?></td>
            <td class="main">&nbsp;
<?php
    if ($error == true) {
      if ($entry_password_error == true) {
        echo tep_draw_password_field('password') . '&nbsp;' . ENTRY_PASSWORD_ERROR;
      } else {
        echo PASSWORD_HIDDEN . tep_draw_hidden_field('password') . tep_draw_hidden_field('confirmation');
      }
    } else {
      echo tep_draw_password_field('password') . '&nbsp;' . ENTRY_PASSWORD_TEXT;
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
      echo tep_draw_password_field('confirmation') . '&nbsp;' . ENTRY_PASSWORD_CONFIRMATION_TEXT;
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

-->

</table>
