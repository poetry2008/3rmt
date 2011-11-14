<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_GET['pid'])) {
    $link_customer_email = ''; 
    $preorder_info_raw = tep_db_query("select orders_id, customers_id, customers_email_address from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."' and site_id = '".SITE_ID."'");    
    if (!tep_db_num_rows($preorder_info_raw)) {
      forward404(); 
    }
    $preorder_info_res = tep_db_fetch_array($preorder_info_raw);  
    
    $link_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_info_res['customers_id']."'");
    $link_customer_res = tep_db_fetch_array($link_customer_raw); 
    
    if ($link_customer_res) {
      $link_customer_email = $preorder_info_res['customers_email_address'];    
    }
  }

if(isset($_POST['login_type']) && $_POST['login_type'] == 'new') {
  tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT,'email_address='.$_POST['email_address']));
}else{ 

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    // tamura 2002/12/30 「全角」英数字を「半角」に変換
    $_POST['email_address'] = tep_an_zen_to_han($_POST['email_address']);

    $email_address = tep_db_prepare_input($_POST['email_address']);
    $password = tep_db_prepare_input($_POST['password']);
    
    if (isset($_GET['pid'])) {
      if ($link_customer_email == '') {
        $_GET['login'] = 'failture';
      } else {
        if ($email_address != $link_customer_email) {
          $_GET['login'] = 'failture';
        } else {
          if (!tep_validate_password($password, $link_customer_res['customers_password'])) {
            $_GET['login'] = 'failture';
          } else {
            if (SESSION_RECREATE == 'True') {
              tep_session_recreate();
            }
            
            $check_country_query = tep_db_query("
                SELECT entry_country_id, 
                       entry_zone_id 
                FROM " . TABLE_ADDRESS_BOOK . " 
                WHERE customers_id = '" . $link_customer_res['customers_id'] . "' 
                  AND address_book_id = '1'
            ");
            $check_country = tep_db_fetch_array($check_country_query);

            $customer_id = $link_customer_res['customers_id'];
            $customer_default_address_id = $link_customer_res['customers_default_address_id'];
            $customer_first_name = $link_customer_res['customers_firstname'];
            $customer_last_name = $link_customer_res['customers_lastname']; // 2003.03.08 Add Japanese osCommerce
            $customer_country_id = $check_country['entry_country_id'];
            $customer_zone_id = $check_country['entry_zone_id'];
            tep_session_register('customer_id');
            tep_session_register('customer_default_address_id');
            tep_session_register('customer_first_name');
            tep_session_register('customer_last_name'); // 2003.03.08 Add Japanese osCommerce
            tep_session_register('customer_country_id');
            tep_session_register('customer_zone_id');
            $customer_emailaddress = $email_address;
            tep_session_register('customer_emailaddress');

            $guestchk = $link_customer_res['customers_guest_chk'];
            tep_session_register('guestchk');

            $date_now = date('Ymd');
    //ccdd
            tep_db_query("
                UPDATE " . TABLE_CUSTOMERS_INFO . " 
                SET customers_info_date_of_last_logon = now(), 
                    customers_info_number_of_logons   = customers_info_number_of_logons+1 
                WHERE customers_info_id = '" . $customer_id . "'
            ");    
             $cart->restore_contents();
             tep_redirect(tep_href_link('change_preorder.php', 'pid='.$_GET['pid'], 'NONSSL'));
          }
        }
      }
    } else {

// Check if email exists
//ccdd
    $check_customer_query = tep_db_query("
        SELECT customers_id, 
               customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_email_address, 
               customers_default_address_id, 
               customers_guest_chk 
        FROM " . TABLE_CUSTOMERS .  " 
        WHERE customers_email_address = '" . tep_db_input($email_address) . "' 
          AND site_id = ".SITE_ID." AND is_active = 1");
    if (!tep_db_num_rows($check_customer_query)) {
      $_GET['login'] = 'fail';
    } else {
      $check_customer = tep_db_fetch_array($check_customer_query);
// Check that password is good
      if (!tep_validate_password($password, $check_customer['customers_password'])) {
        $_GET['login'] = 'fail';
      } else {
        if (SESSION_RECREATE == 'True') { // 2004/04/25 Add session management
          tep_session_recreate();
        }

//ccdd
        $check_country_query = tep_db_query("
            SELECT entry_country_id, 
                   entry_zone_id 
            FROM " . TABLE_ADDRESS_BOOK . " 
            WHERE customers_id = '" . $check_customer['customers_id'] . "' 
              AND address_book_id = '1'
        ");
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
        $customer_emailaddress = $email_address;
        tep_session_register('customer_emailaddress');
        $guestchk = $check_customer['customers_guest_chk'];
        tep_session_register('guestchk');

        $date_now = date('Ymd');
//ccdd
        tep_db_query("
            UPDATE " . TABLE_CUSTOMERS_INFO . " 
            SET customers_info_date_of_last_logon = now(), 
                customers_info_number_of_logons   = customers_info_number_of_logons+1 
            WHERE customers_info_id = '" . $customer_id . "'
        ");
            
    //POINT_LIMIT CHECK ポイントの有効期限チェック ds-style
    if(MODULE_ORDER_TOTAL_POINT_LIMIT != '0') {
//ccdd
      $plimit_count_query = tep_db_query("
          SELECT count(*) as cnt 
          FROM ".TABLE_ORDERS." 
          WHERE customers_id = '".$customer_id."' 
            AND site_id = '".SITE_ID."'
      ");
      $plimit_count = tep_db_fetch_array($plimit_count_query);
      
      if($plimit_count['cnt'] > 0) {
//ccdd
      $plimit_query = tep_db_query("
          SELECT date_purchased 
          FROM ".TABLE_ORDERS." 
          WHERE customers_id = '".$customer_id."' 
            AND site_id = '".SITE_ID."' 
          ORDER BY date_purchased DESC 
          LIMIT 1
      ");
      $plimit = tep_db_fetch_array($plimit_query);
      $p_year = substr($plimit['date_purchased'], 0, 4);
      $p_mon = substr($plimit['date_purchased'], 5, 2);
      $p_day = substr($plimit['date_purchased'], 8, 2);

      $now = time();
      $point_limit = mktime(0, 0, 0, $p_mon, $p_day+MODULE_ORDER_TOTAL_POINT_LIMIT, $p_year);
        if($now > $point_limit) {
//ccdd
          tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
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
}
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
?>
<?php page_head();?>
<script type="text/javascript"><!--
function session_win() {
  window.open("<?php echo tep_href_link(FILENAME_INFO_SHOPPING_CART, '', 'SSL'); ?>","info_shopping_cart","height=460,width=430,toolbar=no,statusbar=no,scrollbars=yes").focus();
}
//--></script>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h2 class="pageHeading">
<?php 
if (!isset($_GET['pid'])) {
  echo HEADING_TITLE;
} else {
  echo PREORDER_HEADING_TITLE; 
}
?> 
</h2>
<div class="box">
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process'.(isset($_GET['pid'])?'&pid='.$_GET['pid']:''), 'SSL')); ?>
      <table class="box_des" width="95%" border="0" align="center" cellpadding="0" cellspacing="0">

        <?php
  if (isset($_GET['login']) && ($_GET['login'] == 'fail')) {
    $info_message = TEXT_LOGIN_ERROR;
  } elseif (isset($_GET['login']) && ($_GET['login'] == 'failture')) { 
    $info_message = TEXT_PREORDER_LOGIN_ERROR;
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
      <?php
      if (isset($_GET['pid'])) {
      ?>
        <tr>
          <td> 
          <table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
          <tr>     
          <td colspan="2" valign="top" class="main">
            <table border="0" class="infoBox" summary="table" cellpadding="1" cellspacing="0" width="100%">
              <tr>
                <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents" summary="table">
                    <tr>
                      <td class="main" colspan="2">
                      <?php echo PREORDER_LOGIN_HEAD_TEXT;?> 
                      </td>
                    </tr>
                  </table> 
                </td>
              </tr>
            </table> 
          </td>
          </tr>
          </table> 
        </td>
        </tr>
      <?php }?> 
        <tr>
          <td><!--<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')); ?>-->
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2" valign="top" class="main">
              <?php if (!isset($_GET['pid'])) {?> 
              <b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
              <?php }?> 
              <table border="0" width="100%" cellspacing="0" cellpadding="1" class="formArea">
                <tr>
                  <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
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
                      <td class="main main_input"><?php echo tep_draw_input_field('email_address'); ?></td>
                    </tr>
                    <tr>
                      <td class="main"><b><?php echo ENTRY_PASSWORD; ?></b></td>
                      <td class="main main_input"><?php echo tep_draw_password_field('password'); ?></td>
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
        <td colspan="2"><div class="sep">&nbsp;</div></td>
      </tr>
            <tr>
              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td colspan="2" valign="top" class="main">
              <?php if (!isset($_GET['pid'])) {?> 
              <b><?php echo HEADING_NEW_CUSTOMER; ?></b>
              <?php }?> 
              </td>
            </tr>
            <?php if (!isset($_GET['pid'])) {?> 
            <tr>
              <td height="50%" colspan="2" valign="top"><table class="formArea" border="0" width="100%" cellspacing="0" cellpadding="1">
                <tr>
                  <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
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
                      <td align="right">
                      <?php 
                      if ($cart->count_contents() > 0) {
                        echo '<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
                      } else {
                        echo '<a href="javascript:void(0);" onclick="alert(\''.NOTICE_MUST_BUY_TEXT.'\');"><img src="includes/languages/japanese/images/buttons/button_continue.gif" alt="'.IMAGE_BUTTON_CONTINUE.'"></a>'; 
                      }
                      ?>
                      </td>
                    </tr>
                   <tr>
                      <td align="right">
                      <a href="<?php echo tep_href_link('send_mail.php', '', 'SSL');?>">メール受信テストをする</a> 
                      </td>
                   </tr>
                  </table></td>
                </tr>
              </table>
              </td>
            </tr>
            <?php }?> 
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
          <td class="smallText"><!--<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')); ?>-->
         
          </td>
        </tr>
      </table>
  <p class="box_des" style="margin-right:5px;">
    <i><strong>SSL認証</strong></i><br>
    当サイトでは、実在性の証明とプライバシー保護のため、日本ジオトラストのSSLサーバ証明書を使用し、SSL暗号化通信を実現しています。
    ブラウザのURLが「<?php echo HTTPS_SERVER;?>～」で始まるURLであることを確認ください。
    以下に掲載するジオトラスト発行済み スマートシールのクリックにより、サーバ証明書の検証結果をご確認ください。
  </p>
  <p align="center">
    <!-- GeoTrust Smart Icon tag. Do not edit. -->
    <script type="text/javascript" src="//smarticon.geotrust.com/si.js"></script>
    <!-- END of GeoTrust Smart Icon tag -->
  </p>
<div class="sep" style="margin-right:20px;">&nbsp;</div>
<?php echo TEXT_POINT ; ?>
      </form></div></div>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
