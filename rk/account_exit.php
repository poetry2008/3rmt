<?php
/*
  $Id$

  用户信息页
*/

  require('includes/application_top.php');
//如果没有登陆 则在历史中加上此页，并跳转到登录页
  if (!tep_session_is_registered('customer_id')) {
 //$navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

//如果是游客，则不在历史页中记录，仅是跳转 
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
//加载语言
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EXIT);
//在面包中加入此 连接
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_ACCOUNT_EXIT, '', 'SSL'));
if(isset($_GET['action']) && $_GET['action']=="quit_success"){
$breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EXIT, '', 'SSL'));
  }else{
$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT_EXIT, '', 'SSL'));
  }
if(isset($_GET['action']) && $_GET['action']=="quit_success" && isset($_GET['check_flag']) && $_GET['check_flag']!=""){
$cart->remove_all();
$update_customer_info = tep_db_query("update ".TABLE_CUSTOMERS." set is_quited='1',quited_date=now(),point='0',customers_guest_chk='1',customers_password='',origin_password='' where customers_id ='".$customer_id."'");
$account_info_del_sql = "delete from ".TABLE_CUSTOMERS_BASKET." where customers_id='".$customer_id."'";
tep_db_query($account_info_del_sql);
$update_customer_orders = tep_db_query("update ".TABLE_ORDERS." set customer_is_quited='1' where customers_id='".$customer_id."'");
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name'); 
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('comment_emailaddress');
  $navigation->clear_snapshot();
} 
?>
<?php page_head();?>
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
      <td valign="top" id="contents"><h1 class="pageHeading">
<?php 
  if(isset($_GET['action']) && $_GET['action']=="quit_success"){
    echo EXIT_SUCCESS_TITLE;
  }else{
    echo HEADING_TITLE;
  }
?>
</h1>
<div class="comment">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        
               <tr>
          <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
              <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="0" >

                <tr>
                  <td class="main"><table border="0" cellspacing="0" cellpadding="2" width="100%">
<?php
  if(isset($_GET['action']) && $_GET['action']=="quit_success" && isset($_GET['check_flag']) && $_GET['check_flag']!=""){
?>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_SUCCESS_COMMENT;?></td>
</tr>
<tr> 
              <td align="right"> <br> 
  
              <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?> </td> 
            </tr> 
<?php
  }else{
if(!isset($_GET['check_from']) ){
	tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL'));
	  }
    ?>
                    <tr>
                    <td class="main"><?php echo str_replace('${SITE_NAME}',STORE_NAME,TEXT_ACCOUNT_EXIT_COMMENT_SITE);?></td>
</tr>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_COMMENT_LOGIN;?></td>
</tr>
<tr>
<td class="main"><br><br><br></td>
</tr>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_COMMENT_POINT;?></td>
</tr>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_COMMENT_REGISTER;?></td>
</tr>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_COMMENT_EXIT;?></td>
</tr>
<tr>
<td class="main"><?php echo TEXT_ACCOUNT_EXIT_COMMENT_CONTACT;?></td>
</tr>
<tr>
<td class="main"><br><br><br><br><br><br><br></td>
</tr>
<?php
  }
?>
                  </table></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" align="center">
            <tr>
<?php
if(isset($_GET['action']) && $_GET['action']=="quit_success"){
?>
<td class="main">&nbsp;</td>
<?php
}else{
?>
<td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
               <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EXIT, 'action=quit_success&check_flag='.time(), 'SSL') . '" onclick="return confirm(\''.TEXT_ACCOUNT_EXIT_CONFIRM.'\');">' . tep_image_button('button_exit.gif', IMAGE_BUTTON_ACCOUNT_EXIT) . '</a>'; ?></td>
<td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_cancel.gif', TEXT_ACCOUNT_EXIT_CANCEL) . '</a>'; ?></td>
	       <?php
}
?>
            </tr>
          </table></td>
        </tr>
      </table>
</div>
<p class="pageBottom"></p>
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
