 <?php
/*
  $Id$

  用户信息页
*/

  require('includes/application_top.php');
//如果没有登陆 则在历史中加上此页，并跳转到登录页
  if (!tep_session_is_registered('customer_id')) {
 //   $navigation->set_snapshot();
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
$cart->remove_all();
 // $account_sql = "update customers set is_quited='1',quited_date=now() where customers_id='".$customer_id."'";
$customers_info            = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id='".$customer_id."'");
$account_created_date      = tep_db_query("select customers_info_date_account_created from ".TABLE_CUSTOMERS_INFO." where customers_info_id='$customer_id'");
$account_created_date_info = tep_db_fetch_array($account_created_date);
$customers_info_array      = tep_db_fetch_array($customers_info);
$customers_id              = $customers_info_array['customers_id'];
$customers_firstname       = addslashes($customers_info_array['customers_firstname']);
$customers_lastname        = addslashes($customers_info_array['customers_lastname']);
$customers_firstname_f     = addslashes($customers_info_array['customers_firstname_f']);
$customers_lastname_f      = addslashes($customers_info_array['customers_lastname_f']);
$customers_email_address   = addslashes($customers_info_array['customers_email_address']);
$site_id                   = $customers_info_array['site_id'];
$customers_info_date_account_created = $account_created_date_info['customers_info_date_account_created'];
$customers_quited_info = "insert into ".TABLE_CUSTOMERS_EXIT." (id,customers_id,customers_firstname,customers_lastname,customers_firstname_f,customers_lastname_f,customers_email_address,site_id,customers_info_date_account_created,quited_date) values (NULL,'".$customers_id."','".$customers_firstname."','".$customers_lastname."','".$customers_firstname_f."','".$customers_lastname_f."','".$customers_email_address."','".$site_id."','".$customers_info_date_account_created."',now())";
tep_db_query($customers_quited_info);
$account_del_sql = "delete from ".TABLE_CUSTOMERS." where customers_id='".$customer_id."'";
tep_db_query($account_del_sql);
$account_info_del_sql = "delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id='".$customer_id."'";
tep_db_query($account_info_del_sql);
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name'); //Add Japanese osCommerce
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('comment_emailaddress');
  $navigation->clear_snapshot();

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
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
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
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
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