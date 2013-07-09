<?php
/*
  $Id$

  用户信息页
*/

  require('includes/application_top.php');
//如果没有登陆 则在历史中加上此页，并跳转到登录页
  if (!tep_session_is_registered('customer_id')) {
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
  if(isset($_GET['action']) && $_GET['action'] == "quit_success"){
    $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EXIT, '', 'SSL'));
  }else{
    $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT_EXIT, '', 'SSL'));
  }

  if(isset($_GET['action']) && $_GET['action'] == "quit_success" && isset($_GET['check_flag']) && $_GET['check_flag']!=""){
    $cart->remove_all();
    $now_time = date('Y-m-d H:i:s', time()); 
    $customers_info_raw = tep_db_query("select c.*, ci.* from ".TABLE_CUSTOMERS." c, ".TABLE_CUSTOMERS_INFO." ci where c.customers_id = ci.customers_info_id and c.customers_id = '".$customer_id."'");
    $customers_info = tep_db_fetch_array($customers_info_raw); 
    $other_info = array(); 
    $other_info['reset_flag'] = $customers_info['reset_flag']; 
    $other_info['is_seal'] = $customers_info['is_seal']; 
    $other_info['is_send_mail'] = $customers_info['is_send_mail']; 
    $other_info['is_calc_quantity'] = $customers_info['is_calc_quantity']; 
    $other_info['last_login'] = $customers_info['customers_info_date_of_last_logon']; 
    $other_info['login_num'] = $customers_info['customers_info_number_of_logons']; 
   
    $before_date = (tep_not_null($customers_info['customers_info_date_account_created'])?$customers_info['customers_info_date_account_created']:'0000-00-00 00:00:00');
    $count_order_query = tep_db_query("select count(*) as order_total_num from ".TABLE_ORDERS." where customers_id = '".$customer_id."' and site_id = '".SITE_ID."' and date_purchased >= '".$before_date."'");    
    $count_order = tep_db_fetch_array($count_order_query); 
    $order_num = (!empty($count_order['order_total_num'])?$count_order['order_total_num']:0);  
   
    $count_review_query = tep_db_query("select count(*) as review_total from ".TABLE_REVIEWS." where customers_id = '".$customer_id."' and site_id = '".SITE_ID."' and date_added >= '".$before_date."'");
    $count_review = tep_db_fetch_array($count_review_query); 
    $review_num = (!empty($count_review['review_total'])?$count_review['review_total']:0);  
    
    $other_info['order_num'] = $order_num; 
    $other_info['review_num'] = $review_num; 
    $other_info['user_add'] = $customers_info['user_added']; 
    $other_info['user_add_date'] = $customers_info['customers_info_date_account_created']; 
    $other_info['user_update'] = $customers_info['user_update']; 
    $other_info['user_update_date'] = $customers_info['customers_info_date_account_last_modified']; 
    
    
    $sql_data_array = array(
         'customers_id' => $customer_id, 
         'site_id' => $customers_info['site_id'], 
         'customers_firstname' => $customers_info['customers_firstname'], 
         'customers_lastname' => $customers_info['customers_lastname'], 
         'customers_email' => $customers_info['customers_email_address'], 
         'customers_newsletter' => $customers_info['customers_newsletter'], 
         'point' => $customers_info['point'], 
         'pic_icon' => $customers_info['pic_icon'], 
         'customers_fax' => $customers_info['customers_fax'], 
         'quited_date' => $now_time, 
         'other_info' => serialize($other_info), 
         'created_at' => $now_time, 
        ); 
    tep_db_perform(TABLE_CUSTOMERS_EXIT_HISTORY, $sql_data_array); 
    tep_db_query("update ".TABLE_CUSTOMERS." set is_quited = '1', quited_date = '".$now_time."', point = '0', customers_guest_chk = '1', customers_password = '', origin_password = '', customers_newsletter = '0', reset_flag = '0', is_seal = '0', is_send_mail = '0', is_calc_quantity = '0', pic_icon = '', customers_fax = '', is_exit_history = '1' where customers_id = '".$customer_id."'");

    tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$customer_id."'");
    tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET_OPTIONS." where customers_id = '".$customer_id."'");

    tep_db_query("delete from ".TABLE_ADDRESS_HISTORY." where customers_id = '".$customer_id."'");
    
    tep_db_query("update ".TABLE_ORDERS." set is_gray = '1' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
    tep_db_query("update ".TABLE_PREORDERS." set is_gray = '1' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
  
    tep_db_query("update ".TABLE_CUSTOMERS_INFO." set customers_info_date_of_last_logon = NULL, customers_info_number_of_logons = 0, customers_info_date_account_created = '".$now_time."', customers_info_date_account_last_modified = '".$now_time."', global_product_notifications = 0, customer_last_resetpwd = '0000-00-00 00:00:00', user_update = '".tep_get_fullname($customers_info['customers_firstname'], $customers_info['customers_lastname'])."' where customers_info_id = '".$customer_id."'");
    
    tep_session_unregister('customer_id');
    tep_session_unregister('customer_default_address_id');
    tep_session_unregister('customer_first_name');
    tep_session_unregister('customer_last_name');
    tep_session_unregister('customer_country_id');
    tep_session_unregister('customer_zone_id');
    tep_session_unregister('comments');
    tep_session_unregister('customer_emailaddress');
    tep_session_unregister('guestchk');
    tep_session_unregister('shipping');
    tep_session_unregister('payment');
    tep_session_unregister('point');
    tep_session_unregister('get_point');
    tep_session_unregister('real_point');
    tep_session_unregister('date');
    tep_session_unregister('hour');
    tep_session_unregister('min');
    tep_session_unregister('insert_torihiki_date');
    tep_session_unregister('h_code_fee');
    tep_session_unregister('h_point');
    tep_session_unregister('start_hour');
    tep_session_unregister('start_min');
    tep_session_unregister('end_hour');
    tep_session_unregister('end_min');
    tep_session_unregister('ele');
    tep_session_unregister('address_option');
    tep_session_unregister('insert_torihiki_date_end');
    tep_session_unregister('address_show_list');
    tep_session_unregister('hc_point');
    tep_session_unregister('hc_camp_point');
    unset($_SESSION['options']);
    unset($_SESSION['options_type_array']);
    unset($_SESSION['weight_fee']);
    unset($_SESSION['free_value']);
    unset($_SESSION['option']);
    unset($_SESSION['referer_adurl']);
    unset($_SESSION['campaign_fee']);
    unset($_SESSION['camp_id']);
    unset($_SESSION['shipping_page_str']);
    unset($_SESSION['shipping_session_flag']);

    $navigation->clear_snapshot();
}
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
    <div id="l_menu">
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    </div>
      <!-- left_navigation_eof -->
      <!-- body_text -->
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
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
      </table></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
