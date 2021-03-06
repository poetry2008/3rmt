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
    // 全角的英数字改成半角
    $_POST['email_address'] = tep_an_zen_to_han($_POST['email_address']);
    $flag_error = false;
    $user_ip = explode('.',$_SERVER['REMOTE_ADDR']);
    $user_ip4 = 0;
    while (list($u_key, $u_byte) = each($user_ip)) {
      $user_ip4 = ($user_ip4 << 8) | (int)$u_byte;
    }
    //被封IP时间
    $user_ip_time_query = tep_db_query("select logintime from ". TABLE_USER_LOGIN ." where seal_ip='1' and address='{$user_ip4}' and loginstatus='p' and site_id='". SITE_ID ."' order by logintime desc limit 0,1");
    $user_ip_time_array = tep_db_fetch_array($user_ip_time_query);
    tep_db_free_result($user_ip_time_query);
    $seal_ip_time = $user_ip_time_array['logintime'];

    $user_time_query = tep_db_query("select max(logintime) as max_time from ". TABLE_USER_LOGIN ." where address='{$user_ip4}' and loginstatus='p' and site_id='". SITE_ID ."'");
    $user_time_array = tep_db_fetch_array($user_time_query);
    $user_max_time = $user_time_array['max_time'];
    tep_db_free_result($user_time_query);
    $user_query = tep_db_query("select loginstatus from ". TABLE_USER_LOGIN ." where address='{$user_ip4}' and loginstatus='p' and time_format(timediff(now(),logintime),'%H')<24 and site_id='". SITE_ID ."'");
    $user_num_rows = tep_db_num_rows($user_query);
    tep_db_free_result($user_query);
    //判断如果是新注册用户不受IP被封限制
    $new_user_query = tep_db_query("select ci.customers_info_date_account_created new_user_time,ci.customer_last_resetpwd resetpwd_time from ". TABLE_CUSTOMERS ." c left join ". TABLE_CUSTOMERS_INFO ." ci on c.customers_id = ci.customers_info_id where c.customers_email_address='{$_POST['email_address']}' and c.site_id='". SITE_ID ."'");
    $new_user_array = tep_db_fetch_array($new_user_query);
    tep_db_free_result($new_user_query);
    //新注册用户或者修改密码用户的特殊处理 start
    $edit_user_query = tep_db_query("select loginstatus from ". TABLE_USER_LOGIN ." where logintime>'{$new_user_array['new_user_time']}' and address='{$user_ip4}' and loginstatus='p' and account='{$_POST['email_address']}' and time_format(timediff(now(),logintime),'%H')<24 and site_id='". SITE_ID ."'");
    $edit_user_num_rows = tep_db_num_rows($edit_user_query);
    tep_db_free_result($edit_user_query);

    $edit_old_user_query = tep_db_query("select loginstatus from ". TABLE_USER_LOGIN ." where logintime>'{$new_user_array['resetpwd_time']}' and address='{$user_ip4}' and loginstatus='p' and account='{$_POST['email_address']}' and time_format(timediff(now(),logintime),'%H')<24 and site_id='". SITE_ID ."'");
    $edit_old_user_num_rows = tep_db_num_rows($edit_old_user_query);
    tep_db_free_result($edit_old_user_query);
    $new_user_time = strtotime($new_user_array['new_user_time']);
    $old_user_resetpwd_time = strtotime($new_user_array['resetpwd_time']);
    $old_user_time = strtotime($user_max_time);
    $flag_true = $old_user_resetpwd_time != false ? $old_user_resetpwd_time <= $old_user_time : false; 

    $seal_ip_flag = false; 
    if(strtotime($seal_ip_time)){
      $seal_ip_flag = strtotime($seal_ip_time.'+24 hour') >= time(); 
    } 
  if($edit_old_user_num_rows >= 5){
    if($flag_true == true){
      if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){
               
               if(!$seal_ip_flag){ 
                 tep_db_query("update ". TABLE_USER_LOGIN ." set seal_ip='1' where address='{$user_ip4}' and loginstatus='p' and logintime='$user_max_time' and site_id='". SITE_ID ."'");           
               }
               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    }

    if($new_user_time <= $old_user_time && $old_user_resetpwd_time == false){
        
        if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){

                
               if(!$seal_ip_flag){ 
                 tep_db_query("update ". TABLE_USER_LOGIN ." set seal_ip='1' where address='{$user_ip4}' and loginstatus='p' and logintime='$user_max_time' and site_id='". SITE_ID ."'"); 
               }
               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    }
  } 

  if($edit_user_num_rows >= 5 && $old_user_resetpwd_time == false){

    if($flag_true == true){
      if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){

               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    }

    if($new_user_time <= $old_user_time && $old_user_resetpwd_time == false){
        
        if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){

               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    }
  }

  if($edit_old_user_num_rows < 5 && $edit_user_num_rows < 5){
    if(!$seal_ip_flag && $user_num_rows == 5){ 
      tep_db_query("update ". TABLE_USER_LOGIN ." set seal_ip='1' where address='{$user_ip4}' and loginstatus='p' and logintime='$user_max_time' and site_id='". SITE_ID ."'");           
    }
    if($flag_true == true && ($old_user_resetpwd_time == true && $old_user_resetpwd_time <= strtotime($seal_ip_time))){

      if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){

               tep_db_query("delete from ". TABLE_USER_LOGIN ." where time_format(timediff(now(),logintime),'%H')>168 and site_id='". SITE_ID ."'");
               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    }

    if($new_user_time <= $old_user_time && $old_user_resetpwd_time == false && $new_user_time <= strtotime($seal_ip_time)){
        
        if($user_num_rows >= 5){
         
         $user_time = strtotime($user_max_time.'+24 hour');
         $user_now = time();
         
         if($user_time >= $user_now){
               
               tep_db_query("delete from ". TABLE_USER_LOGIN ." where time_format(timediff(now(),logintime),'%H')>168 and site_id='". SITE_ID ."'");
               $flag_error = true; 
               $_GET['login'] = 'ip_error';
         }
        
      }
    } 
  }

 if($flag_error == false){
    $email_address = tep_db_prepare_input($_POST['email_address']);
    $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
    $password = tep_db_prepare_input($_POST['password']);
    if (isset($_GET['pid'])) {
      if ($link_customer_email == '') {
        $_GET['login'] = 'failture';
      } else {
        if (strtolower($email_address) != strtolower($link_customer_email)) {
          $_GET['login'] = 'failture';
        } else {
          if (!tep_validate_password($password, $link_customer_res['customers_password'])) {
            $_GET['login'] = 'failture';
          } else {
            if($link_customer_res['reset_flag'] and $link_customer_res['reset_success']!=1 ){
	       $_SESSION['reset_flag'] = true;
               $_SESSION['reset_customers_id'] = $link_customer_res['customers_id'];
       	       tep_redirect(tep_href_link(FILENAME_DEFAULT));	    
	    }
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
            $customer_last_name = $link_customer_res['customers_lastname'];
            $customer_country_id = $check_country['entry_country_id'];
            $customer_zone_id = $check_country['entry_zone_id'];
            tep_session_register('customer_id');
            tep_session_register('customer_default_address_id');
            tep_session_register('customer_first_name');
            tep_session_register('customer_last_name');
            tep_session_register('customer_country_id');
            tep_session_register('customer_zone_id');
            $customer_emailaddress = $email_address;
            tep_session_register('customer_emailaddress');
            $guestchk = $link_customer_res['customers_guest_chk'];
            tep_session_register('guestchk');

            $date_now = date('Ymd');
            tep_db_query("
                UPDATE " . TABLE_CUSTOMERS_INFO . " 
                SET customers_info_date_of_last_logon = now(), 
                    customers_info_number_of_logons   = customers_info_number_of_logons+1 
                WHERE customers_info_id = '" . $customer_id . "'
                ");    
             $cart->restore_contents();
             tep_redirect(tep_href_link('change_preorder.php', 'pid='.$_GET['pid'], 'SSL'));
          }
        }
      }
    } else {

// Check if email exists
    $check_customer_query = tep_db_query("
        SELECT customers_id, 
               customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_email_address, 
               customers_default_address_id, 
               customers_guest_chk,
               reset_flag,
               reset_success
        FROM " . TABLE_CUSTOMERS .  " 
        WHERE customers_email_address = '" . tep_db_input($email_address) . "' 
          AND site_id = ".SITE_ID." AND is_active = 1 AND is_quited = 0");
    
    if (!tep_db_num_rows($check_customer_query)) {
      $_GET['login'] = 'fail';
      //把登录信息写入数据表 
            session_regenerate_id();  
            tep_db_query("
              INSERT INTO ". TABLE_USER_LOGIN ." 
              VALUES('". session_id() ."',now(),now(),'{$_POST['email_address']}','p','','{$user_ip4}','0','". SITE_ID ."') 
              ");
    } else {
      $check_customer = tep_db_fetch_array($check_customer_query);
      // Check that password is good
      if (!tep_validate_password($password, $check_customer['customers_password'])) {
        $_GET['login'] = 'fail';
        //把登录信息写入数据表 
            session_regenerate_id();  
            tep_db_query("
              INSERT INTO ". TABLE_USER_LOGIN ." 
              VALUES('". session_id() ."',now(),now(),'{$_POST['email_address']}','p','','{$user_ip4}','0','". SITE_ID ."') 
              ");
      } else {

	  if($check_customer['reset_flag'] and $check_customer['reset_success']!=1 ){
	       $_SESSION['reset_flag'] = true;
               $_SESSION['reset_customers_id'] = $check_customer['customers_id'];
       	       tep_redirect(tep_href_link(FILENAME_DEFAULT));	    
	  }
        if (SESSION_RECREATE == 'True') {
          tep_session_recreate();
        }
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
        $customer_last_name = $check_customer['customers_lastname'];
        $customer_country_id = $check_country['entry_country_id'];
        $customer_zone_id = $check_country['entry_zone_id'];
        $customer_emailaddress = $email_address; 
        $guestchk = $check_customer['customers_guest_chk'];
        tep_session_register('customer_id');
        tep_session_register('customer_default_address_id');
        tep_session_register('customer_first_name');
        tep_session_register('customer_last_name');
        tep_session_register('customer_country_id');
        tep_session_register('customer_zone_id');
        tep_session_register('customer_emailaddress');
        tep_session_register('guestchk');

        $date_now = date('Ymd');
        tep_db_query("
            UPDATE " . TABLE_CUSTOMERS_INFO . " 
            SET customers_info_date_of_last_logon = now(), 
                customers_info_number_of_logons   = customers_info_number_of_logons+1 
            WHERE customers_info_id = '" . $customer_id . "'
            ");
    
    //POINT_LIMIT CHECK 返点的有效期限判断
    if(MODULE_ORDER_TOTAL_POINT_LIMIT != '0') {
      $plimit_count_query = tep_db_query("
          SELECT count(*) as cnt 
          FROM ".TABLE_ORDERS." 
          WHERE customers_id = '".$customer_id."' 
            AND site_id = '".SITE_ID."'
      ");
      $plimit_count = tep_db_fetch_array($plimit_count_query);
      
      if($plimit_count['cnt'] > 0) {
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
          tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
        }
      }
    }

// restore cart contents
        $cart->restore_contents();
	if($_SESSION['referer']!=""){
		  tep_db_query("update ".TABLE_CUSTOMERS." set referer='".tep_db_prepare_input($_SESSION['referer'])."'   where customers_id='".$customer_id."'");
		                 }
        if (sizeof($navigation->snapshot) > 0) {
          if ($navigation->snapshot['page'] != 'change_preorder.php') {
            $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
            $navigation->clear_snapshot();
          } else {
            $origin_href = tep_href_link(FILENAME_DEFAULT); 
          }
          tep_redirect($origin_href);
        } else {
          tep_redirect(tep_href_link(FILENAME_DEFAULT));
        }
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
      <td id="contents" valign="top">
      <h1 class="pageHeading">
      <?php 
      if (!isset($_GET['pid'])) {
        echo HEADING_TITLE;
      } else {
        echo PREORDER_HEADING_TITLE; 
      }
      ?> 
      </h1>
      <?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process'.(isset($_GET['pid'])?'&pid='.$_GET['pid']:''), 'SSL')); ?>
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

<?php
  if(isset($_GET['login']) && ($_GET['login'] == 'ip_error')){
      $info_message = TEXT_LOGIN_IP_ERROR;
  }else{
    if (isset($_GET['login']) && ($_GET['login'] == 'fail')) {
      $info_message = TEXT_LOGIN_ERROR;
    } elseif (isset($_GET['login']) && ($_GET['login'] == 'failture')) { 
      $info_message = TEXT_PREORDER_LOGIN_ERROR;
    } elseif ($cart->count_contents()) {
      $info_message = TEXT_VISITORS_CART;
    }
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
            <table border="0" class="infoBox" summary="table" width="100%" cellpadding="1" cellspacing="0">
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
          <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2" valign="top" class="main">
              <?php if (!isset($_GET['pid'])) {?> 
              <b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
              <?php }?> 
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
                      <td class="main" width="93">&nbsp;<b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                      <td class="main"><?php echo tep_draw_input_field('email_address','','class="input_text"'); ?></td>
                    </tr>
                    <tr>
                      <td class="main"><b>&nbsp;<?php echo ENTRY_PASSWORD; ?></b></td>
                      <td class="main"><?php echo tep_draw_password_field('password','','class="input_text"'); ?></td>
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
              <td colspan="2" valign="top" class="main">
              <?php if (!isset($_GET['pid'])) {?> 
              <b><?php echo HEADING_NEW_CUSTOMER; ?></b>
              <?php }?> 
              </td>
            </tr>
            <?php if (!isset($_GET['pid'])) {?> 
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
                    <a href="<?php echo tep_href_link('send_mail.php', '', 'SSL');?>"><?php echo SEND_MAIL_TEST;?></a>
                    </td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
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
          <td class="smallText">
         
          </td>
        </tr>
      </table>
      <?php echo TEXT_LOGIN_SSL_READ;?>    
      <p align="center"> 

<!-- GeoTrust Smart Icon tag. Do not edit. -->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="//smarticon.geotrust.com/si.js"></SCRIPT>
<!-- END of GeoTrust Smart Icon tag -->

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
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
