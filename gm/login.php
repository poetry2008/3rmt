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
<h2 class="pageHeading"><?php echo HEADING_TITLE; ?></h2>
<div class="box">
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')); ?>
      <table class="box_des" width="95%" border="0" align="center" cellpadding="0" cellspacing="0">

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
          <td><!--<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')); ?>-->
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2" valign="top" class="main"><b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
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
              <td colspan="2" valign="top" class="main"><b><?php echo HEADING_NEW_CUSTOMER; ?></b></td>
            </tr>
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
