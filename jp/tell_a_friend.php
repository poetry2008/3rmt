<?php
/*
  $Id: tell_a_friend.php,v 1.4 2004/05/26 06:18:27 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (tep_session_is_registered('customer_id')) {
    $account = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  $valid_product = false;
  if (isset($HTTP_GET_VARS['products_id'])) {
    $product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_TELL_A_FRIEND);
  //require(DIR_WS_LANGUAGES . '/affiliate_japanese.php');

if (!isset($HTTP_GET_VARS['send_to'])) $HTTP_GET_VARS['send_to']=NULL;
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_TELL_A_FRIEND, 'send_to=' . $HTTP_GET_VARS['send_to'] . '&products_id=' . $HTTP_GET_VARS['products_id']));
?>
<?php page_head();?>
</head>
<body> 
<div align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" id="body_text"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
      <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <?php
  if ($valid_product == false) {
?> 
      <p class="main"><?php echo HEADING_TITLE_ERROR; ?><br> 
        <?php echo ERROR_INVALID_PRODUCT; ?> </p> 
      <?php
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
?> 
      <h1 class="pageHeading"><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h1> 
      
      <?php
    $error = false;

    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && !tep_validate_email(trim($HTTP_POST_VARS['friendemail']))) {
      $friendemail_error = true;
      $error = true;
    } else {
      $friendemail_error = false;
    }

    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && empty($HTTP_POST_VARS['friendname'])) {
      $friendname_error = true;
      $error = true;
    } else {
      $friendname_error = false;
    }

    if (tep_session_is_registered('customer_id')) {
      $from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
      $from_email_address = $account_values['customers_email_address'];
    } else {
      if (!isset($HTTP_POST_VARS['yourname'])) $HTTP_POST_VARS['yourname']=NULL;
      $from_name = $HTTP_POST_VARS['yourname'];
      if (!isset($HTTP_POST_VARS['from'])) $HTTP_POST_VARS['from']=NULL;
      $from_email_address = $HTTP_POST_VARS['from'];
    }
      
    if (!tep_session_is_registered('customer_id')) {
      if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
        $fromemail_error = true;
        $error = true;
      } else {
        $fromemail_error = false;
      }
    }

    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && empty($from_name)) {
      $fromname_error = true;
      $error = true;
    } else {
      $fromname_error = false;
    }

    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && ($error == false)) {
      $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $from_name, STORE_NAME);
      $email_body = sprintf(TEXT_EMAIL_INTRO, $HTTP_POST_VARS['friendname'], $from_name, $HTTP_POST_VARS['products_name'], STORE_NAME) . "\n\n";

      if (tep_not_null($HTTP_POST_VARS['yourmessage'])) {
        $email_body .= $HTTP_POST_VARS['yourmessage'] . "\n\n";
      }

      $email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id'])) . "\n\n" .
                     sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

      tep_mail($HTTP_POST_VARS['friendname'], $HTTP_POST_VARS['friendemail'], $email_subject, stripslashes($email_body), '', $from_email_address);
?> 
      <div> 
        <p class="main"><?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, stripslashes($HTTP_POST_VARS['products_name']), $HTTP_POST_VARS['friendemail']); ?></p> 
        <div align="right"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div> 
      </div> 
      <?php
    } else {
      if (tep_session_is_registered('customer_id')) {
        $your_name_prompt = tep_output_string_protected(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
        if (!isset($HTTP_GET_VARS['yourname'])) $HTTP_GET_VARS['yourname']=NULL;
        $your_name_prompt = tep_draw_input_field('yourname', (($fromname_error == true) ? $HTTP_POST_VARS['yourname'] : $HTTP_GET_VARS['yourname']));
        if ($fromname_error == true) $your_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
        if (!isset($HTTP_GET_VARS['from'])) $HTTP_GET_VARS['from']=NULL;
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $HTTP_POST_VARS['from'] : $HTTP_GET_VARS['from']));
        if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?> 
      <?php echo tep_draw_form('email_friend', tep_href_link(FILENAME_TELL_A_FRIEND, 'action=process&products_id=' . $HTTP_GET_VARS['products_id'])) . tep_draw_hidden_field('products_name', $product_info['products_name']); ?> 
      <table width="100%" cellpadding="0" cellspacing="0" border="0"> 
        <tr> 
          <td class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></td> 
        </tr> 
        <tr> 
          <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea"> 
            <tr> 
              <td class="main"><table border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td> 
                  <td class="main"><?php echo $your_name_prompt; ?></td> 
                </tr> 
                <tr> 
                  <td class="main"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td> 
                  <td class="main"><?php echo $your_email_address_prompt; ?></td> 
                </tr> 
              </table></td> 
            </tr> 
          </table></td> 
        </tr> 
        <tr> 
          <td class="formAreaTitle"><br> 
          <?php echo FORM_TITLE_FRIEND_DETAILS; ?></td> 
        </tr> 
        <tr> 
          <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea"> 
            <tr> 
              <td class="main"><table border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"><?php echo FORM_FIELD_FRIEND_NAME; ?></td> 
                  <?php if (!isset($HTTP_GET_VARS['friendname'])) $HTTP_GET_VARS['friendname']=NULL;?>
                  <?php if (!isset($HTTP_POST_VARS['friendname'])) $HTTP_POST_VARS['friendname']=NULL;?>
                  <td class="main"><?php echo tep_draw_input_field('friendname', (($friendname_error == true) ? $HTTP_POST_VARS['friendname'] : $HTTP_GET_VARS['friendname'])); if ($friendname_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';?></td> 
                </tr> 
                <tr> 
                  <td class="main"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td> 
                  <td class="main"><?php echo tep_draw_input_field('friendemail', (($friendemail_error == true) ? $HTTP_POST_VARS['friendemail'] : $HTTP_GET_VARS['send_to'])); if ($friendemail_error == true) echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR; ?></td> 
                </tr> 
              </table></td> 
            </tr> 
          </table></td> 
        </tr> 
        <tr> 
          <td class="formAreaTitle"><br> 
          <?php echo FORM_TITLE_FRIEND_MESSAGE; ?></td> 
        </tr> 
        <tr> 
          <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea"> 
            <tr> 
              <td><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td> 
            </tr> 
          </table></td> 
        </tr> 
        <tr> 
          <td><br> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
              <td align="right" class="main"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
            </tr> 
          </table></td> 
        </tr> 
      </table> 
      </form> 
      <?php
		}
}
?></td> 
      <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border"><!-- left_navigation //--> 
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
    </tr> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
