<?php
/*
  $Id$

*/

  require('includes/application_top.php');
//如果没有登陆 则在历史中加上此页，并跳转到登录页
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

//如果是游客，则不在历史页中记录，仅是跳转 
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
//加载语言
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT);
//在面包中加入此 连接
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<!-- header --> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <div id="main">
  <!-- left_navigation -->
      <!-- left_navigation_eof -->
      <!-- body_text -->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
 <?php include('includes/search_include.php');?>
 <div id="main-content">
<h2><?php echo HEADING_TITLE ; ?></h2>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" id="detail-table-noframe">
      <?php
//设置只读  用户第一次访问此页，并不想修改信息
  $is_read_only       = true;

 
  $account_query = tep_db_query("
      SELECT c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_firstname_f, c.customers_lastname_f, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_zone_id, a.entry_state, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter
      FROM " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " a 
      WHERE c.customers_id = '" . $customer_id . "' AND a.customers_id = c.customers_id AND a.address_book_id = '" .  $customer_default_address_id . "' AND  c.site_id = ".SITE_ID);
  $account              = tep_db_fetch_array($account_query);
  $account_single = true;
   require(DIR_WS_MODULES . 'account_details.php');
?>
        <?php 
    //Point
    if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') { 
    $point_query = tep_db_query("
        select point 
        from " . TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "'
    ");
    $point = tep_db_fetch_array($point_query);
    ?>    
         <tr>
            <td colspan="2" align="left"><h3><?php echo  TEXT_POINT_NOW;?></h3></td>
         </tr>
          <tr>
             <td align="left" width="20%"><?php echo TEXT_POINT_ADD; ?></td>
             <td align="left">&nbsp;<?php echo $point['point'] . " P"; ?></td>
          </tr>
        <?php } ?>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="botton-continue">
            <tr>
              <td align="center"><?php echo '<a href="' .
              tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' .
              tep_image_button('button_history.gif',
                  IMAGE_BUTTON_HISTORY,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_history.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_history_hover.gif\'"') . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EXIT, 'check_from='.time(), 'SSL') . '">' . tep_image_button('button_out.gif', IMAGE_BUTTON_CUSTOMERS_EXIT,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_out.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_out_hover.gif\'"') . '</a>'; ?></td>
              <td align="center"><?php echo '<a href="' .
              tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' .
              tep_image_button('button_notifications.gif',
                  IMAGE_BUTTON_NOTIFICATIONS,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_notifications.gif\'"
                  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_notifications_hover.gif\'"') . '</a>'; ?></td>
              <td align="center"><?php echo '<a href="' .
              tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' .
              tep_image_button('button_edit_account.gif',
                  IMAGE_BUTTON_EDIT_ACCOUNT,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_edit_account.gif\'"
                  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_edit_account_hover.gif\'"') . '</a>'; ?></td>
            </tr>
          </table>
      </div></div>
    
      <?php include('includes/float-box.php');?>
</div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
