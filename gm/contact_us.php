<?php
/*
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  //forward 404
if (isset($_GET['products_id'])) {
  $_404_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id
      = " . intval($_GET['products_id']));
  $_404 = tep_db_fetch_array($_404_query);

  forward404Unless($_404);
}
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONTACT_US);
  
  $mail_text = '';
  
  //product_idを取得した場合商品名を呼び出す
  if (isset($_GET['products_id'])) {
    $product_info_query = tep_db_query("select pd.products_name from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" .  (int)$_GET['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and pd.site_id = '".SITE_ID."'");
    $product_info = tep_db_fetch_array($product_info_query);
  }
  
  //「商品名」についてのお問い合わせ
  define('HEADING_TITLE_CONTACT', 'についてのお問い合わせ');
  define('TITLE_CONTACT_END', 'について');
  define('EMAIL_SEPARATOR', '-----------------------------------------------' . "\n\n");
  
  
  $error = false;
  if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
    if (tep_validate_email(trim($_POST['email']))) {
      $mail_text .= $_POST['name'] . ' 様からお問い合わせを承りました。' . "\n";
      $mail_text .= 'メールアドレス：' . $_POST['email'] . "\n\n";
      $mail_text .= $_POST['enquiry'];
      tep_mail(STORE_OWNER, SUPPORT_EMAIL_ADDRESS, EMAIL_SUBJECT, $mail_text, $_POST['name'], $_POST['email']);
      tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
    } else {
      $error = true;
    }
  }
  
  //prouct_idを取得した場合の処理（題名を商品名についてのお問い合わせに変更）
  if (isset($_GET['action']) && ($_GET['action'] == 'send_p')) {
    if (tep_validate_email(trim($_POST['email']))) {
      $mail_text .= $_POST['name'] . ' 様からお問い合わせを承りました。' . "\n";
      $mail_text .= 'メールアドレス：' . $_POST['email'] . "\n\n";
      $mail_text .= $_POST['enquiry'];
      tep_mail(STORE_OWNER, SUPPORT_EMAIL_ADDRESS, $_POST['email_title'] . HEADING_TITLE_CONTACT, $mail_text, $_POST['name'], $_POST['email']);
      tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
    } else {
      $error = true;
    }
  }
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONTACT_US));
?>
<?php page_head();?>
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
      <h1 class="pageHeading"><?php
  if (isset($_GET['products_id'])) {
    echo $product_info['products_name'] . HEADING_TITLE_CONTACT;
  }else{
    echo HEADING_TITLE;
  }
?></h1> 
      <div> 
        <table border="0" class="box_des" width="100%" cellspacing="0" cellpadding="0"> 
<?php
  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?> 
          <tr> 
            <td>
              <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="2" align="center"> 
                <tr> 
                  <td class="main"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE, '0', '0', 'align="left"') . TEXT_SUCCESS; ?></td> 
                </tr> 
                <tr> 
                  <td align="right"><br> 
                    <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a>
                  </td> 
                </tr> 
              </table>
            </td> 
          </tr> 
<?php
  } else {
?> 
          <tr> 
            <td>
<?php 
  if (isset($_GET['products_id'])) {
    //product_idを取得した場合のフォームのアクション先
    echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send_p'));
  } else {
    //通常
    echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send'));
  }
?> 
              <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"><?php echo ENTRY_NAME; ?><br> 
                  <?php if (!isset($last_name)) $last_name=NULL;?>
                  <?php if (!isset($first_name)) $first_name=NULL;?>
                    <?php echo tep_draw_input_field('name', ($error ? $_POST['name'] : (($language == 'japanese') ? ($last_name . ' ' . $first_name) : ($first_name . ' ' . $last_name)))); // 2003.03.10 Edit Japanese osCommerce ?>
                  </td> 
                </tr> 
                <tr> 
                  <?php if (!isset($email_address)) $email_address=NULL;?>
                  <td class="main"><?php echo ENTRY_EMAIL; ?><br> 
                    <?php echo tep_draw_input_field('email', ($error ? $_POST['email'] : $email_address), 'size="30"'); if ($error) echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR; ?></td> 
                  </tr> 
                <tr> 
                  <td class="main"><?php echo ENTRY_ENQUIRY; ?></td> 
                </tr> 

                <tr> 
                  <td><?php 
  if (isset($_GET['products_id'])) {
    $product_name = $product_info['products_name']; //変数に商品名を格納
    //product_idを取得した場合
    echo tep_draw_hidden_field('email_title', $product_name); 
  } 
  if (!isset($product_name)) $product_name=NULL;
?> <?php echo tep_draw_textarea_field('enquiry', 'soft', 50, 15, '■　' . $product_name . TITLE_CONTACT_END . "\n" . EMAIL_SEPARATOR, 'style="width:400px"'); ?></td> 
                </tr> 
                <tr> 
                  <td class="main" align="right" style="padding-right:20px;"><br> 
                    <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
                  </td> 
                </tr> 
              </table> 
              </form>
            </td> 
          </tr> 
<?php
  }
?> 
        </table> 
      </div>
    </div>
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
