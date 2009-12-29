<?php
/*
  $Id: login.php,v 1.4 2004/04/25 02:29:00 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

if(isset($HTTP_POST_VARS['login_type']) && $HTTP_POST_VARS['login_type'] == 'new') {
  tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT,'email_address='.$HTTP_POST_VARS['email_address']));
}else{ 

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process')) {
    // tamura 2002/12/30 「全角」英数字を「半角」に変換
    $HTTP_POST_VARS['email_address'] = tep_an_zen_to_han($HTTP_POST_VARS['email_address']);

    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
    $password = tep_db_prepare_input($HTTP_POST_VARS['password']);

// Check if email exists
    $check_customer_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_password, customers_email_address, customers_default_address_id, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "'");
    if (!tep_db_num_rows($check_customer_query)) {
      $HTTP_GET_VARS['login'] = 'fail';
    } else {
      $check_customer = tep_db_fetch_array($check_customer_query);
// Check that password is good
      if (!tep_validate_password($password, $check_customer['customers_password'])) {
        $HTTP_GET_VARS['login'] = 'fail';
      } else {
        if (SESSION_RECREATE == 'True') { // 2004/04/25 Add session management
          tep_session_recreate();
        }

        $check_country_query = tep_db_query("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $check_customer['customers_id'] . "' and address_book_id = '1'");
        $check_country = tep_db_fetch_array($check_country_query);

        $customer_id = $check_customer['customers_id'];
        $customer_default_address_id = $check_customer['customers_default_address_id'];
        $customer_first_name = $check_customer['customers_firstname'];
        $customer_last_name = $check_customer['customers_lastname']; // 2003.03.08 Add Japanese osCommerce
        $customer_country_id = $check_country['entry_country_id'];
        $customer_zone_id = $check_country['entry_zone_id'];
        tep_session_register('customer_id');
        tep_session_register('customer_default_address_id');
        tep_session_register('customer_first_name');
        tep_session_register('customer_last_name'); // 2003.03.08 Add Japanese osCommerce
        tep_session_register('customer_country_id');
        tep_session_register('customer_zone_id');

		$guestchk = $check_customer['customers_guest_chk'];
		tep_session_register('guestchk');

        $date_now = date('Ymd');
        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . $customer_id . "'");
		
		//POINT_LIMIT CHECK ポイントの有効期限チェック ds-style
		if(MODULE_ORDER_TOTAL_POINT_LIMIT != '0') {
		  $plimit_count_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where  customers_id = '".$customer_id."'");
		  $plimit_count = tep_db_fetch_array($plimit_count_query);
		  
		  if($plimit_count['cnt'] > 0) {
		  $plimit_query = tep_db_query("select date_purchased from ".TABLE_ORDERS." where customers_id = '".$customer_id."' order by date_purchased desc limit 1");
		  $plimit = tep_db_fetch_array($plimit_query);
		  $p_year = substr($plimit['date_purchased'], 0, 4);
		  $p_mon = substr($plimit['date_purchased'], 5, 2);
		  $p_day = substr($plimit['date_purchased'], 8, 2);

		  $now = time();
		  $point_limit = mktime(0, 0, 0, $p_mon, $p_day+MODULE_ORDER_TOTAL_POINT_LIMIT, $p_year);
		    if($now > $point_limit) {
			  tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."'");
			}
		  }
		}

// restore cart contents
        $cart->restore_contents();

        if (sizeof($navigation->snapshot) > 0) {
          $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
          $navigation->clear_snapshot();
          tep_redirect($origin_href);
        } else {
          tep_redirect(tep_href_link(FILENAME_DEFAULT));
        }
      }
    }
  }
}
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
?>
<?php page_head();?>
<script language="javascript" type="text/javascript"><!--
function session_win() {
  window.open("<?php echo tep_href_link(FILENAME_INFO_SHOPPING_CART); ?>","info_shopping_cart","height=460,width=430,toolbar=no,statusbar=no,scrollbars=yes").focus();
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
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"><!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td id="contents" valign="top"><h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1><?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')); ?>
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

        <?php
  if (isset($HTTP_GET_VARS['login']) && ($HTTP_GET_VARS['login'] == 'fail')) {
    $info_message = TEXT_LOGIN_ERROR;
  } elseif ($cart->count_contents()) {
    $info_message = TEXT_VISITORS_CART;
  }

  if (isset($info_message)) {
?>
        <tr>
          <td class="smallText"><?php echo $info_message; ?></td>
        </tr>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
        <?php
  }
?>
        <tr>
          <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2" valign="top" class="main"><b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
              <table border="0" width="100%" cellspacing="0" cellpadding="1" class="infoBox">
                <tr>
                  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents">
                    <tr>
                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                      <td class="main" colspan="2"><?php echo TEXT_RETURNING_CUSTOMER; ?></td>
                    </tr>
                    <tr>
                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                      <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                      <td class="main"><?php echo tep_draw_input_field('email_address'); ?></td>
                    </tr>
                    <tr>
                      <td class="main"><b><?php echo ENTRY_PASSWORD; ?></b></td>
                      <td class="main"><?php echo tep_draw_password_field('password'); ?></td>
                    </tr>
                    <tr>
                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                      <td class="smallText" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></td>
                    </tr>
                    <tr align="right">
                      <td colspan="2"><?php echo tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN); ?></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td colspan="2" valign="top" class="main"><b><?php echo HEADING_NEW_CUSTOMER; ?></b></td>
            </tr>
            <tr>
              <td height="50%" colspan="2" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="1" class="infoBox">
                <tr>
                  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents">
                    <tr>
                      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top"><?php echo TEXT_NEW_CUSTOMER . '<br><br>' . TEXT_NEW_CUSTOMER_INTRODUCTION; ?></td>
                    </tr>
                    <tr>
                      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                      <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                    </tr>
                    <tr>
                    <td align="right">
                    <?php echo '<a href="' . tep_href_link(FILENAME_SEND_MAIL, '', 'SSL') .
                    '">' . SEND_MAIL. '</a>';?>
                    </td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top"></td>
              <td width="50%" align="right" valign="top"></td>
            </tr>
            <tr>
              <td align="right" valign="top"></td>
              <td align="right" valign="top"></td>
            </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td class="smallText">
         
          </td>
        </tr>
      </table>
<p>
<i><strong>SSL認証</strong></i><br>
当サイトでは、実在性の証明とプライバシー保護のため、グローバルサインのSSLサーバ証明書を使用し、SSL暗号化通信を実現しています。
ブラウザのURLが「<?=HTTPS_SERVER;?>〜」で始まるURLであることを確認ください。
以下に掲載するグローバルサイン発行済み サイトシールのクリックにより、サーバ証明書の検証結果をご確認ください。		
</p>
         <p align="center"> 
<!-- GlobalSign SiteSeal tag. Do not edit. -->
<span id="___sitess__alt_img_wrapper">
<a href="http://jp.globalsign.com/" target=_blank><img alt="SSL　グローバルサインのサイトシール" border=0 id="___sitess__alt_img"
src="//seal.globalsign.com/SiteSeal/images/seal_noscript_jp.gif"></a>
</span>
<script type="text/javascript" src="//seal.globalsign.com/SiteSeal/siteSeal.js" defer></script>
<!-- End of GlobalSign SiteSeal Tag -->

        </p>
<div class="underline"></div>
<?php echo TEXT_POINT ; ?>			
      </form>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"><!-- right_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      <!-- right_navigation_eof //-->
      </td>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
