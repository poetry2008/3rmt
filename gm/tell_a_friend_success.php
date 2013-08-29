<?php
/*
  $Id$
*/
  require('includes/application_top.php');

  if (tep_session_is_registered('customer_id')) {
    $account = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_email_address 
        from " . TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "' 
        and site_id = '".SITE_ID."'
    ");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
    $product_info_query = tep_db_query("
        select pd.products_name
        from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        where pd.products_status != '0' and pd.products_status != '3'
          and pd.products_id = '" .  (int)$_GET['products_id'] . "' 
          and pd.language_id = '" . $languages_id . "' 
          and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
        order by pd.site_id DESC
        limit 1
    ");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_TELL_A_FRIEND);

  if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL;
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_TELL_A_FRIEND, 'send_to=' . $_GET['send_to'] . '&products_id=' . $_GET['products_id']));
?>
<?php page_head();?>
<?php
header('Content-Type: text/html; charset=UTF-8');
header('Pragma: public');         
header('Expires: '.gmdate('D, d M Y H:i:s', 0).'GMT');
@header('Last-modified: ' . gmdate('D, d M Y H:i:s') . ' gmt');
@header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: private, post-check=0, pre-check=0, max-age=0', FALSE);
header('Pragma: no-cache');
header('Expires: 0');
?>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
  <!-- body_text //-->
     <div id="layout" class="yui3-u" >

      <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
        <?php include('includes/search_include.php');?>
      <div id="main-content">
<?php
  if ($valid_product == false) {
?>
    <h2><?php echo HEADING_TITLE_ERROR; ?></h2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" id="detail-table-noframe">
      <tr>
        <td><?php echo ERROR_INVALID_PRODUCT; ?></td>
      </tr>
    </table>
    <?php
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
?>
    <h2><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h2>
    <div class="tell_msg">
      <p><?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, stripslashes($product_info['products_name']), $_GET['friendemail']); ?></p>
      <div align="right" style="margin-top:20px;"> <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"') . '</a>'; ?>
      </div>
    </div>
<?php
    } 
?>
  </div>
</div>
  <?php include('includes/float-box.php');?>
  <!-- body_text_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
   <!-- footer_eof //-->
</div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
