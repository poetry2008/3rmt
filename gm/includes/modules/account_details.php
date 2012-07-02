<?php
/*
  $Id$
  ファイルコードを確認
*/
  $newsletter_array = array(array('id' => '1',
                                  'text' => ENTRY_NEWSLETTER_YES),
                            array('id' => '0',
                                  'text' => ENTRY_NEWSLETTER_NO));

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
      $a_value = tep_draw_input_field('firstname',
                                      $account['customers_firstname'],
                                      "class='input_text' style='width:40%'") . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
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
      $a_value = tep_draw_input_field('lastname',
                                      $account['customers_lastname'],"class='input_text'
                                      style='width:40%'") . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
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
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_GUEST_EMAIL_NOT_ACTIVE;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address',
          $account['customers_email_address'],"class='input_text' style='width:40%'") . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);
?>
  <tr>
    <td width="20%"><h3><?php echo CATEGORY_PERSONAL; ?></h3></td>
    <td></td>
  </tr>
                                          <?php
  $address_form->printCategoryPersonal();
?>
 
<!--start-->
</table>
<table class="content_account" width="100%" cellspacing="0" cellpadding="0"
border="0">
<tr>
    <td><h3><?php echo CATEGORY_OPTIONS; ?></h3></td>
      <td></td>
       </tr>
             <tr>
                <td width="20%"><?php echo ENTRY_NEWSLETTER; ?></td>
                <td>&nbsp;<?php
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
?>
                </td>
                              </tr>
       <?php
  if ($is_read_only == true) {
    //Not View
  } else {
    if(substr(basename($PHP_SELF),0,7) == 'create_') {
    $guestchk_array = array(array('id' => '0',
                                   'text' => ENTRY_ACCOUNT_MEMBER),
                              array('id' => '1',
                                   'text' => ENTRY_ACCOUNT_GUEST));
  
?>
              <tr>
                <td><?php echo ENTRY_GUEST; ?></td>
                <td>&nbsp;<?php echo tep_draw_pull_down_menu('guestchk', $guestchk_array, $guestchk, 'onchange="pass_hidd()"'); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><font color="#fff"><span class="redtext">※</span>&nbsp;<?php echo TEXT_SPAN_TITLE;?></font></td>
              </tr>
  </table>

   <table width="100%" cellspacing="0" cellpadding="0" border="0" class="content_account"> 
              <?php
    } else {
    echo '<tr><td colspan="2"><input type="hidden" name="guestchk" value="0" ></td></tr>';
  }
  }
?>


   <?php
  if($guestchk == '1') {
    $newpass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
    $password = $newpass;
    $confirmation = $newpass;
  }
  if ($is_read_only == false) {
?>
  <tr id="trpass1">
    <td width="20%">
      <h3><?php echo CATEGORY_PASSWORD; ?></h3></td>
      <td></td>
  </tr>
 <tr id="trpass2">
     <td colspan="2">
       <table width="100%">
             
       <tr>
                <td width="20%"><?php echo ENTRY_PASSWORD; ?></td>
                <td>&nbsp;<?php
 if(preg_match("/[a-zA-Z]/",$_POST['password']) ||
     preg_match("/[0-9]/",$_POST['password'])){

      
        $p_error_show_str = ''; 
    if ($error == true) {
              if ($entry_password_confirm_same_error == true) { 
        echo tep_draw_password_field('password','',"id='input_text_short'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
        $p_error_show_str = ENTRY_NO_USE_OLD_PASSWORD;
      } else if ($entry_password_confirmation_error == true) { 
       echo tep_draw_password_field('password','',"id='input_text_short'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
        $p_error_show_str = '';
      
      } else if($entry_password_error == true) {
        echo tep_draw_password_field('password','',"id='input_text_short'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
       
        if (isset($entry_password_error_msg)) {
          $p_error_show_str = $entry_password_error_msg; 
        } else {
          $p_error_show_str = ENTRY_PASSWORD_ERROR;
        }
             } else {
        echo PASSWORD_HIDDEN . tep_draw_hidden_field('password') . tep_draw_hidden_field('confirmation');
      }

        } else {
                            echo tep_draw_password_field('password','',"id='input_text_short'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
                    }
?>

       </td>
     </tr>
      
               <?php
 
    if ( ($error == false) || ($entry_password_error == true) ) {
?>
              <tr>
                <td><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
                <td>&nbsp;<?php
      echo tep_draw_password_field('confirmation', '', "id='input_text_short'") .
      '&nbsp;' . ENTRY_PASSWORD_CONFIRMATION_TEXT;
?>                </td>
              </tr>
              <?php
    }  
?>
<?php
  } 
else{
                   echo tep_draw_password_field('password','',"id='input_text_short'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
?>
             <tr>
                <td><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
                <td>&nbsp;<?php
      echo tep_draw_password_field('confirmation', '', "id='input_text_short'") .
      '&nbsp;' . ENTRY_PASSWORD_CONFIRMATION_TEXT;
?>                </td>
              </tr>
<?php
}
?>
    </table>
    </td>
    </tr>
         
    <?php
            if($_POST['password'] != $_POST['confirmation']){
        echo "<tr><td></td><td style='font-size:14px'><font
      color=\"red\">&nbsp;&nbsp;".ENTRY_PASSWORD_IS_DIFFERENT."</td></tr>";
    }

  if ($p_error_show_str != '') {
   ?>  
   <tr>
     <td class="main">&nbsp;</td>
     <td class="main" style="font-size:14px;">
     <?php echo '&nbsp;'.$p_error_show_str;?> 
     </td>
   </tr>
  <?php 
  } 
  ?>
    <tr>
      <td class="main" colspan="2"><?php echo ENTRY_PASSWORD_INFORM_READ_TEXT;?></td>
    </tr>
  <?php
  }
?>

<!--end-->
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
</table> 
<table width="100%" cellspacing="0" cellpadding="2" border="0" class="content_account"> 
  <tr>
     <td colspan="2" width="20%"><h3><?php echo CATEGORY_AGREEMENT; ?></h3></td>
  </tr>
<tr>
                <td colspan="2" width="100%">
                                                <?php
  echo tep_draw_textarea_field('agreement', 'soft', '48', '20',
      mb_convert_encoding($agreement_text, 'UTF-8', 'ASCII, JIS, UTF-8, EUC-JP,
        SJIS'),'style="width:90%;"');
  echo ' </td></tr>';
  echo '<tr><td colspan="2" style=" padding:10px 0 5px 0;">';
  echo tep_draw_checkbox_field('agreement_chk', 'ok') . ENTRY_AGREEMENT_TEXT;
?>
     </td>
              </tr>

   <?php
    }
  }

?>
<input type="hidden" name="country" value="107">
