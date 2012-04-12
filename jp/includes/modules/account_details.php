 <?php
/*
  $Id$

  顾客详细信息页
*/
//设置是否有新信息的多语言 
  $newsletter_array = array(array('id' => '1',
                                  'text' => ENTRY_NEWSLETTER_YES),
                            array('id' => '0',
                                  'text' => ENTRY_NEWSLETTER_NO));

//如果没有设置 只读，则为否
  if (!isset($is_read_only)) $is_read_only = false;
  if (!isset($processed)) $processed = false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;

  // firstname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_firstname'],false,true);
  } elseif ($error == true) {
      if ($entry_firstname_error == true) {
          $a_value = tep_draw_input_field('firstname') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
      } else {
          $a_value = $firstname . tep_draw_hidden_field('firstname');
      }
  } else {
      $a_value = tep_draw_input_field('firstname', $account['customers_firstname']) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_lastname'],false,true);
  } elseif ($error == true) {
      if ($entry_lastname_error == true) {
          $a_value = tep_draw_input_field('lastname') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
      } else {
          $a_value = $lastname . tep_draw_hidden_field('lastname');
      }
  } else {
      $a_value = tep_draw_input_field('lastname', $account['customers_lastname']) . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);

  // email_address
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_email_address'],false,true);
  } elseif ($error == true) {
      if ($entry_email_address_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
      } elseif ($entry_email_address_check_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      } elseif ($entry_email_address_exists == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      } elseif ($entry_guest_not_active == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' .  ENTRY_GUEST_EMAIL_NOT_ACTIVE;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address', $account['customers_email_address']) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
<?php
  $address_form->printCategoryPersonal();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_OPTIONS; ?></td>
  </tr>
  <tr>
    <td class="main">
    <table border="0" width="100%" summary="table" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main">
        <table border="0" cellspacing="0" cellpadding="2" summary="table">
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
    echo tep_draw_pull_down_menu('newsletter', $newsletter_array, $account['customers_newsletter']) . '&nbsp;' . ENTRY_NEWSLETTER_TEXT;
  }
?></td>
          </tr>
<?php
  if ($is_read_only != true) {
    if(substr(basename($PHP_SELF),0,7) == 'create_') {
    $guestchk_array = array(array('id' => '0',
                                   'text' => ENTRY_ACCOUNT_MEMBER),
                              array('id' => '1',
                                   'text' => ENTRY_ACCOUNT_GUEST));
  
?>      
      <tr>
        <td class="main">&nbsp;<?php echo ENTRY_GUEST; ?></td>
<?php if (!isset($guestchk)) $guestchk = NULL;?>
        <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('guestchk', $guestchk_array, $guestchk, 'onchange="pass_hidd()"'); ?>&nbsp;&nbsp;<span class="red">※</span>&nbsp;会員登録をしないで購入することもできます。</td>
      </tr>
<?php
    } else {
    echo '<input type="hidden" name="guestchk" value="0" >';
  }
  }
?>
</table>
</td>
</tr>
</table>
</td>
</tr>

<?php
if (!isset($guestchk)) $guestchk = NULL;
  if($guestchk == '1') {
    $newpass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
  $password = $newpass;
  $confirmation = $newpass;
  }
  if ($is_read_only == false) {
?>

  <tr id="trpass1">
    <td class="formAreaTitle"><br><?php echo CATEGORY_PASSWORD; ?></td>
  </tr>

  <tr id="trpass2">
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_PASSWORD; ?></td>
            <td class="main">&nbsp;
<?php
    $p_error_show_str = ''; 
    if ($error == true) {
      if ($entry_password_confirm_same_error == true) { 
        $p_error_show_str = ENTRY_NO_USE_OLD_PASSWORD;
        echo tep_draw_password_field('password') . '&nbsp;' .  ENTRY_PASSWORD_TEXT;
      } else if ($entry_password_confirmation_error == true) { 
        $p_error_show_str = '&nbsp;<font color="red">'.ENTRY_PASSWORD_IS_DIFFERENT.'</font>';
        echo tep_draw_password_field('password') . '&nbsp;' . ENTRY_PASSWORD_TEXT;
      } else if($entry_password_error == true) {
        echo tep_draw_password_field('password') . '&nbsp;' . ENTRY_PASSWORD_TEXT;
        if (isset($entry_password_error_msg)) {
          $p_error_show_str = $entry_password_error_msg; 
        } else {
          $p_error_show_str = ENTRY_PASSWORD_ERROR;
        }
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
    <?php
    if ($p_error_show_str != '') { 
    ?>
    <tr>
      <td class="main">&nbsp;</td>  
      <td class="main" style="font-size:10px;">
      <?php echo '&nbsp;'.$p_error_show_str;?> 
      </td>  
    </tr>
    <?php }?> 
    <tr>
      <td class="main" colspan="2"><?php echo ENTRY_PASSWORD_INFORM_READ_TEXT;?></td> 
    </tr>
    </table></td>
      </tr>
    </table></td>
  </tr>
<?php
  }
?>

<?php
  #agreement_for_use
  if(basename($PHP_SELF) == 'create_account.php'){
    if (file_exists('./includes/agreement_for_use.txt') == 'true') {
      $file = file('includes/agreement_for_use.txt');
    $agreement_text = '';
  
    foreach($file as $key => $value) {
      $agreement_text .= $value;
    }
?>

  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_AGREEMENT; ?></td>
  </tr>

  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
      <tr>
        <td class="main">
<?php
  echo tep_draw_textarea_field('agreement', 'soft', '98%', '5', mb_convert_encoding($agreement_text, 'UTF-8', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
  echo '<br>';
  echo tep_draw_checkbox_field('agreement_chk', 'ok') . ENTRY_AGREEMENT_TEXT;
?>
        </td>
      </tr>
    </table></td>
      </tr>
    </table></td>
  </tr>
<?php
    }
  }
?>
</table>
<input type="hidden" name="country" value="107">
