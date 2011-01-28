<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_ACTIONS.'login.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
?>
<?php page_head();?>
<script language="javascript" type="text/javascript"><!--
function session_win() {
  window.open("<?php echo tep_href_link(FILENAME_INFO_SHOPPING_CART, '', 'SSL'); ?>","info_shopping_cart","height=460,width=430,toolbar=no,statusbar=no,scrollbars=yes").focus();
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
  if (isset($_GET['login']) && ($_GET['login'] == 'fail')) {
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
ブラウザのURLが「<?php echo HTTPS_SERVER;?>～」で始まるURLであることを確認ください。
以下に掲載するグローバルサイン発行済み サイトシールのクリックにより、サーバ証明書の検証結果をご確認ください。   
</p>
         <p align="center"> 
<!-- GlobalSign SiteSeal tag. Do not edit. -->
<span id="ss_img_wrapper_130-66_flash_ja"><a href="http://jp.globalsign.com/" target="_blank"><img alt="SSL　グローバルサインのサイトシール" border="0" id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_130-66_ja.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_130-66_ja.js"></script>
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
