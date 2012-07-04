<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
//    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EXIT);

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
  tep_session_unregister('customer_last_name'); //Add Japanese osCommerce
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
<h1 class="pageHeading">
<?php 
  if(isset($_GET['action']) && $_GET['action']=="quit_success"){
 echo EXIT_SUCCESS_TITLE;
  }else{
  echo HEADING_TITLE;
  }
?>
</h1>
      <table class="box_des" width="95%" border="0" cellpadding="0" cellspacing="0">
<tr>
                  <td class="main"><table class="box_des" border="0" cellspacing="0" cellpadding="2" width="100%">
<?php
if(isset($_GET['action']) && $_GET['action']=="quit_success" && isset($_GET['check_flag']) && $_GET['check_flag']!=""){
	  ?>
<tr>
<td class="main">今まで、RMTジャックポットでご利用いただいてありがとうございます。</td>
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
                      <td class="main">「退会」とは、RMTジャックポットでご利用いただいているすべてのサービスを終了させる手続きのことです。</td>
</tr>
<tr>
   <td class="formAreaTitle">
<br>
手順
</td>
                    </tr>
<tr>
   <td class="main">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="box_des">
<tr>
<td class="main" valign="top" width="10">
&nbsp;&nbsp;1
</td>
<td class="main">
ログインメールアドレス、パスワードを入力し て、「ログイン」をクリックする。
</td>
</tr>
<tr>
<td class="main">
</td>
<td class="main">
お客さまのログイン状態によっては、この画面が表示されず、すぐに次の手順の画面になることもあります。<br>
</td>
</tr>
<tr>
<td class="main" valign="top">
 &nbsp;&nbsp;2
</td>
<td class="main">
お客様情報、ご利用中のサービスをを確認し、内容が正しければ、「退会する」ボタンをクリックする。<br> 
</td>
</tr>
<tr>
<td class="main" valign="top">
 &nbsp;&nbsp;3
</td>
<td class="main">
「本当に退会しますか？」という確認メッセージが表示されます。<br>
「OK」→退会申し込み完了画面へ<br>
「キャンセル」→そのまま<br> 
</td></tr></table>
</td>
                    </tr>
<tr>

<td class="formAreaTitle">
<br>
注意点
</td>
</tr>
<tr>
<td class="main">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="box_des">
<tr>
<td class="main" valign="top" width="10">
 &nbsp;&nbsp;1
</td>
<td class="main">
RMTジャックポットから退会すると、退会日が記録され、次回同じメールアドレスで会員になったとしても、前退会日以前の注文履歴を非表示とします。<br>
</td>
</tr>
<tr>
<td class="main" valign="top">
 &nbsp;&nbsp;2
</td>
<td class="main">
RMTジャックポットから退会すると、会員でなくなり、ゲストになります。<br>
</td>
</tr>
<tr>
<td class="main" valign="top">
 &nbsp;&nbsp;3
</td>
<td class="main">
RMTジャックポットから退会すると、会員の残ったポイントも利用できなくなり、0になります。<br>
</td>
</tr>
</table>
</td>
</tr>
<?php
  }
?>
<tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" align="center" class="box_des">
            <tr>
<?php
if(isset($_GET['action']) && $_GET['action']=="quit_success"){
?>

<?php
}else{
?>
<td class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
               <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EXIT, 'action=quit_success&check_flag='.time(), 'SSL') . '" onclick="return confirm(\'本当に退会しますか？\');">' . tep_image_button('button_exit.gif', IMAGE_BUTTON_ACCOUNT_EXIT) . '</a>'; ?></td>
	       <?php
}
?>
            </tr>
          </table></td>
        </tr>

</table>
</td></tr>
              </table></div>
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
