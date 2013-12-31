<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  require('includes/step-by-step/new_application_top.php');
  //删除超时的未认证顾客
  tep_customers_not_certified_timeout();
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 'c.site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
   } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_CUSTOMERS);
     $sql_site_where = 'c.site_id in ('.$show_list_str.')';
     $show_list_array = explode(',',$show_list_str);
   }
  if(isset($_GET['site_id'])&&$_GET['site_id']==''){
     $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_CUSTOMERS));
   }
   $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
   while($userslist= tep_db_fetch_array($sites_id_sql)){
      $site_arr = $userslist['site_permission'];
   }
      $site_array = explode(',',$site_arr);
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
/*----------------------------
 case 'update'  更新客户信息
 case 'deleteconfirm' 确认删除客户信息 
 ---------------------------*/
      case 'insert':
        tep_isset_eof(); 
        $customers_firstname     = tep_db_prepare_input($_POST['customers_firstname']);
        $customers_lastname      = tep_db_prepare_input($_POST['customers_lastname']);
        $customers_firstname_f   = tep_db_prepare_input($_POST['customers_firstname_f']);
        $customers_lastname_f    = tep_db_prepare_input($_POST['customers_lastname_f']);
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $customers_email_address = str_replace("\xe2\x80\x8b", '',$customers_email_address);
        $customers_telephone     = tep_db_prepare_input($_POST['customers_telephone']);
        $customers_fax           = tep_db_prepare_input($_POST['customers_fax']);
        $customers_newsletter    = tep_db_prepare_input($_POST['customers_newsletter']);
        $customers_gender        = tep_db_prepare_input($_POST['customers_gender']);
        $customers_dob           = tep_db_prepare_input($_POST['customers_dob']);
        $customers_is_seal           = tep_db_prepare_input($_POST['is_seal']);
        $customers_pic_icon           = tep_db_prepare_input($_POST['pic_icon']);
        $customers_is_send_mail           = tep_db_prepare_input($_POST['is_send_mail']);
        $customers_is_calc_quantity          = tep_db_prepare_input($_POST['is_calc_quantity']);
        $customers_password      = tep_encrypt_password(tep_db_prepare_input($_POST['password']));
        $origin_password         = tep_encrypt_password(tep_db_prepare_input($_POST['password']));
        $customers_guest_chk     = tep_db_prepare_input($_POST['guest_radio']);
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
          $point = tep_db_prepare_input($_POST['point']);
        }
        if ($_POST['reset_flag'] == 'on') {
	$reset_flag = 1;
        $reset_success = 0;
        }else {
	$reset_flag = 0;
        }
        $customers_sql = "insert into ".TABLE_CUSTOMERS."
          (customers_id,customers_firstname,customers_lastname,customers_firstname_f,customers_lastname_f,customers_email_address,customers_telephone,customers_fax,customers_newsletter,customers_gender,customers_dob,is_seal,pic_icon,is_send_mail,send_mail_time,reset_flag,reset_success,site_id,customers_password,origin_password,customers_guest_chk,is_active,is_calc_quantity,point)
          values
          (null,'".$customers_firstname."','".$customers_lastname."','".$customers_firstname_f."','".$customers_lastname_f."','".$customers_email_address."','".$customers_telephone."','".$customers_fax."','".$customers_newsletter."','".$customers_gender."','".$customers_dob."','".$customers_is_seal."','".$customers_pic_icon."','".$customers_is_send_mail."','".time()."','".$reset_flag."','".$reset_success."','".$_POST['site_id']."','".$customers_password."','".$origin_password."','".$customers_guest_chk."','1','".$customers_is_calc_quantity."','".(int)$point."')";
        tep_db_query($customers_sql);
        $customer_id = tep_db_insert_id();
        $ac_email_srandom = md5(time().$customer_id.$customers_email_address);
        tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".$customer_id."'");
        $default_address_id   = tep_db_prepare_input($_POST['default_address_id']);
        $entry_street_address = tep_db_prepare_input($_POST['entry_street_address']);
        $entry_suburb         = tep_db_prepare_input($_POST['entry_suburb']);
        $entry_postcode       = tep_db_prepare_input($_POST['entry_postcode']);
        $entry_city           = tep_db_prepare_input($_POST['entry_city']);
        $entry_country_id     = tep_db_prepare_input($_POST['entry_country_id']);
        $entry_company        = tep_db_prepare_input($_POST['entry_company']);
        $entry_state          = tep_db_prepare_input($_POST['entry_state']);
        $entry_zone_id        = tep_db_prepare_input($_POST['entry_zone_id']);
        $entry_telephone      = tep_db_prepare_input($_POST['customers_telephone']);
        $sql_data_array = array( 'customers_id' => $customer_id,
                                'entry_firstname' => $customers_firstname,
                                'entry_lastname' => $customers_lastname,
                                'entry_street_address' => $entry_street_address,
                                'entry_postcode' => $entry_postcode,
                                'entry_city' => $entry_city,
                                'entry_country_id' => $entry_country_id,
                                'entry_telephone' => $entry_telephone);
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
        $customers_info_sql = "insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,customers_info_date_account_last_modified,user_update,user_added) values ('" . tep_db_input($customer_id) . "', '0', now(),now(),'".$_SESSION['user_name']."','".$_SESSION['user_name']."')";
        tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,customers_info_date_account_last_modified,user_update,user_added) values ('" . tep_db_input($customer_id) . "', '0', now(),now(),'".$_SESSION['user_name']."','".$_SESSION['user_name']."')");
        if(isset($_POST['check_order']) && $_POST['check_order'] != ''){
            if($_POST['check_order'] == 0){
              tep_redirect(tep_href_link('create_order.php','Customer_mail='.$customers_email_address.'&site_id='.$_POST['site_id']));
            }else if($_POST['check_order'] == 1){
              tep_redirect(tep_href_link('create_preorder.php','Customer_mail='.$customers_email_address.'&site_id='.$_POST['site_id']));
            }
        }else{
        tep_redirect(tep_href_link(FILENAME_CUSTOMERS,'site_id='.$_POST['site_id']));
        }
        break;
      case 'update':
        tep_isset_eof(); 
        $an_cols = array('customers_email_address','customers_telephone','customers_fax','customers_dob','entry_postcode');
        foreach ($an_cols as $col) {
          $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
        }
        $customers_id            = tep_db_prepare_input($_GET['cID']);
        $customers_firstname     = tep_db_prepare_input($_POST['customers_firstname']);
        $customers_lastname      = tep_db_prepare_input($_POST['customers_lastname']);
        $customers_firstname_f   = tep_db_prepare_input($_POST['customers_firstname_f']);
        $customers_lastname_f    = tep_db_prepare_input($_POST['customers_lastname_f']);
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $customers_email_address = str_replace("\xe2\x80\x8b", '',$customers_email_address);
        $customers_telephone     = tep_db_prepare_input($_POST['customers_telephone']);
        $customers_fax           = tep_db_prepare_input($_POST['customers_fax']);
        $customers_newsletter    = tep_db_prepare_input($_POST['customers_newsletter']);
        $customers_gender        = tep_db_prepare_input($_POST['customers_gender']);
        $customers_dob           = tep_db_prepare_input($_POST['customers_dob']);
        $customers_is_seal           = tep_db_prepare_input($_POST['is_seal']);
        
        $customers_pic_icon           = tep_db_prepare_input($_POST['pic_icon']);
        $customers_is_send_mail           = tep_db_prepare_input($_POST['is_send_mail']);
        $customers_is_calc_quantity          = tep_db_prepare_input($_POST['is_calc_quantity']);
        if ($_POST['reset_flag'] == 'on') {
	$reset_flag = 1;
        $reset_success = 0;
        }else {
	$reset_flag = 0;
        }

        $sql_data_array = array('customers_firstname'     => $customers_firstname,
                                'customers_lastname'      => $customers_lastname,
                                'customers_firstname_f'   => $customers_firstname_f,
                                'customers_lastname_f'    => $customers_lastname_f,
                                'customers_email_address' => $customers_email_address,
                                'customers_telephone'     => $customers_telephone,
                                'customers_fax'           => $customers_fax,
                                'reset_flag'           => $reset_flag,
                                'reset_success'           => $reset_success,
                                'customers_newsletter'    => $customers_newsletter,
                                'is_seal' => $customers_is_seal,
                                'pic_icon' => $customers_pic_icon,
                                'is_send_mail' => $customers_is_send_mail,
                                'is_calc_quantity' => $customers_is_calc_quantity,
                                );
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $customers_gender;
        if (ACCOUNT_DOB    == 'true') $sql_data_array['customers_dob']    = tep_date_raw($customers_dob);
        
        $customers = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".tep_db_input($customers_id)."'"));
        $check_email = tep_db_query("
            select customers_email_address 
            from " . TABLE_CUSTOMERS . " 
            where customers_email_address = '" . tep_db_input($customers_email_address) . "' 
              and customers_id <> '" . tep_db_input($customers['customers_id']) . "'
              and site_id = '".$customers['site_id']."'
              
        ");
        if (tep_db_num_rows($check_email)) {
          $messageStack->add_session(ERROR_EMAIL_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $customers_id));
        }
        //Add Point System
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
          $point = tep_db_prepare_input($_POST['point']);
          $sql_data_array['point'] = $point;
        }
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "'");

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now(),user_update='".$_POST['user_update']."' where customers_info_id = '" . tep_db_input($customers_id) . "'");

        $default_address_id   = tep_db_prepare_input($_POST['default_address_id']);
        $entry_street_address = tep_db_prepare_input($_POST['entry_street_address']);
        $entry_suburb         = tep_db_prepare_input($_POST['entry_suburb']);
        $entry_postcode       = tep_db_prepare_input($_POST['entry_postcode']);
        $entry_city           = tep_db_prepare_input($_POST['entry_city']);
        $entry_country_id     = tep_db_prepare_input($_POST['entry_country_id']);
        $entry_company        = tep_db_prepare_input($_POST['entry_company']);
        $entry_state          = tep_db_prepare_input($_POST['entry_state']);
        $entry_zone_id        = tep_db_prepare_input($_POST['entry_zone_id']);
        $entry_telephone      = tep_db_prepare_input($_POST['customers_telephone']);

        if ($entry_zone_id > 0) $entry_state = '';

        $sql_data_array = array('entry_firstname' => $customers_firstname,
                                'entry_lastname' => $customers_lastname,
                                'entry_street_address' => $entry_street_address,
                                'entry_postcode' => $entry_postcode,
                                'entry_city' => $entry_city,
                                'entry_country_id' => $entry_country_id,
                                'entry_telephone' => $entry_telephone);

        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $entry_company;
        if (ACCOUNT_SUBURB  == 'true') $sql_data_array['entry_suburb']  = $entry_suburb;
        if (ACCOUNT_STATE == 'true') {
          $sql_data_array['entry_state']   = $entry_state;
          $sql_data_array['entry_zone_id'] = $entry_zone_id;
        }
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "' and address_book_id = '" . tep_db_input($default_address_id) . "'");
        tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action'))));
        break;
      case 'deleteconfirm':
       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         tep_isset_eof(); 
       }
       if(!empty($_POST['customers_id'])){
          foreach($_POST['customers_id'] as $ge_key => $ge_value){
       if ($_POST['delete_reviews'] == 'on') {
          $reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS .  " where customers_id = '" . $ge_value . "'");
          while ($reviews = tep_db_fetch_array($reviews_query)) {
            tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $reviews['reviews_id'] . "'");
          }
          tep_db_query("delete from " . TABLE_REVIEWS . " where customers_id = '" .$ge_value. "'");
        } else {
          tep_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" .$ge_value. "'");
        }
        tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" .$ge_value. "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . $ge_value. "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $ge_value . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" .$ge_value . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $ge_value . "'");
        tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . $ge_value. "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_EXIT_HISTORY . " where customers_id = '" . $ge_value. "'");
          }
        }
        $customers_id = tep_db_prepare_input($_GET['cID']);
       
        if ($_POST['delete_reviews'] == 'on') {
          $reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where customers_id = '" . tep_db_input($customers_id) . "'");
          while ($reviews = tep_db_fetch_array($reviews_query)) {
            tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $reviews['reviews_id'] . "'");
          }
          tep_db_query("delete from " . TABLE_REVIEWS . " where customers_id = '" . tep_db_input($customers_id) . "'");
        } else {
          tep_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" . tep_db_input($customers_id) . "'");
        }

        tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_EXIT_HISTORY . " where customers_id = '" . tep_db_input($customers_id) . "'");

        tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')))); 
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo HEADING_TITLE; ?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
<?php //切换搜索?>
function toggle_search_content() {
   var search_type = $('#search_toggle').val();
   var origin_height = $('#show_customers_list').offset().top; 
   if (search_type == '1') {
     $('#search_toggle').val('2');
     $('#search_comment').css('display', 'block');
     $('#show_second_search').css('display', 'none');
     $('#show_first_search').css('display', 'block');
   } else {
     $('#search_toggle').val('1');
     $('#search_comment').css('display', 'none');
     $('#show_second_search').css('display', 'block');
     $('#show_first_search').css('display', 'none');
   }

   if ($('#show_customers').css('display') == 'block') {
     var new_height = $('#show_customers_list').offset().top; 
     var popup_height = parseInt($('#show_customers').css('top')); 
     var tmp_height = popup_height+(new_height-origin_height);
     
     $('#show_customers').css('top', tmp_height+'px');
   }
}
function check_guest(guest_value){
  if(guest_value == 1){
    $("#password_hide").hide(); 
    $("#reset_flag_hide").hide();
    $("#point_hide").hide();
  }else{
    $("#password_hide").show(); 
    $("#reset_flag_hide").show();
    $("#point_hide").show();
  }
  check_is_active =  $("#check_is_active").val();
  if(check_is_active == 1 && guest_value == 1){
    document.getElementById("check_is_active").value = 0;
  }else{
    document.getElementById("check_is_active").value = 1;
  }
}
function check_password(value, c_permission){
 post_email = $("#customers_email_address").val();
 post_site =  $("#customers_site_id").val();
 once_again_password = $("#once_again_password").val();
 check_is_active = $("#check_is_active").val();
 password = $("#password").val();
 customers_email_address_value = $("#customers_email_address_value").val();
 if(customers_email_address_value != post_email){
 $.ajax({
 url: 'ajax.php?action=check_email',
 data: {post_email:post_email,post_site:post_site} ,
 type: 'POST',
 dataType: 'text',
 async : false,
 success: function(data){
   data_info = data.split(",");
   if(data_info[1] == 1){
     email_error = 'true';
   }else{
     email_error = 'false';
   }
   if(data_info[0] == 1){
     check_email = 'true';
   }else{
     check_email = 'false';
   }
   }
 });
 }else{
 var email_error = 'false';
 var check_email = 'false';
 }
 customers_firstname = $("#customers_firstname").val();
 customers_lastname = $("#customers_lastname").val();
 
 var check_error = '';
if(check_is_active == 1){ 
 if(password == '' && once_again_password == ''){
   $("#error_info_o").html("<?php echo TEXT_ERROR_NULL;?>"); 
   $("#error_info_f").html("<?php echo TEXT_ERROR_NULL;?>"); 
   check_error = 'true';
 }else{
   $("#error_info_o").html(""); 
   $("#error_info_f").html(""); 
 }
}
 if(customers_firstname == ''){
    $("#customers_firstname_error").html("<?php echo TEXT_ERROR_NULL;?>");   
    check_error = 'true';
 }else{
    $("#customers_firstname_error").html("");   
 }
 if(customers_lastname == ''){
    $("#customers_lastname_error").html("<?php echo TEXT_ERROR_NULL;?>");
    check_error = 'true';
 }else{
    $("#customers_lastname_error").html("");
 }
 if(email_error == 'true' && post_email != ''){
    $("#error_email").html("<?php echo TEXT_ERROR_EMAIL;?>");
    check_error = 'true';
  }else{
    $("#error_email").html("");
  }
 if(check_email == 'true' && post_email != ''){
    $("#check_email").html("<?php echo TEXT_EMAIL_ADDRESS;?>");
    check_error = 'true';
 }else{
    $("#check_email").html("");
 }
 if(post_email == ''){
   $("#error_email_info").html("<?php echo TEXT_ERROR_NULL;?>");    
   check_error = 'true';
 }else{
   $("#error_email_info").html("");    
 }
 if(check_is_active == 1 && password != once_again_password){
    $("#error_info_o").html("<?php echo TEXT_ERROR_INFO;?>"); 
    check_error = 'true';
  }else{
    $("#error_info").html(""); 
  }
  if(value == 1){
   document.getElementById('check_order').value = 1;
  }else if(value == 0){
   document.getElementById('check_order').value = 0;
  }
  if(check_error != 'true'){
    if (c_permission == 31) {
      document.forms.customers.submit();  
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            document.forms.customers.submit();  
          } else {
            $('#button_save').attr('id', 'tmp_button_save'); 
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.customers.action),
                async: false,
                success: function(msg_info) {
                  document.forms.customers.submit();  
                }
              }); 
            } else {
              alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
            }
          }
        }
      });
    }
  }

}
function all_select_customers(customers_str){
      var check_flag = document.del_customers.all_check.checked;
          if (document.del_customers.elements[customers_str]) {
            if (document.del_customers.elements[customers_str].length == null){
                if (check_flag == true) {
                  document.del_customers.elements[customers_str].checked = true;
                 } else {
                  document.del_customers.elements[customers_str].checked = false;
                 }
                } else {
            for (i = 0; i < document.del_customers.elements[customers_str].length; i++){
                       if (!document.del_customers.elements[customers_str][i].disabled) { 
                         if (check_flag == true) {
                             document.del_customers.elements[customers_str][i].checked = true;
                         } else {
                             document.del_customers.elements[customers_str][i].checked = false;
                         }
                       }
                       }
                   }
             }
}
function delete_select_customers(customers_str, c_permission){
     sel_num = 0;
     if (document.del_customers.elements[customers_str].length == null) {
         if (document.del_customers.elements[customers_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_customers.elements[customers_str].length; i++) {
             if(document.del_customers.elements[customers_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
       <?php //判断选中的顾客是否有订单或预约存在?>
         var customers_id_list = '';
         var customers_id_list_all = '';
         for (i = 0; i < document.del_customers.elements[customers_str].length; i++) {
           if(document.del_customers.elements[customers_str][i].checked == true) {
             if(i < document.del_customers.elements[customers_str].length-1){
               customers_id_list += document.del_customers.elements[customers_str][i].value+',';
             }else{
               customers_id_list += document.del_customers.elements[customers_str][i].value; 
             }
           }
           if(i < document.del_customers.elements[customers_str].length-1){
             customers_id_list_all += document.del_customers.elements[customers_str][i].value+',';
           }else{
             customers_id_list_all += document.del_customers.elements[customers_str][i].value; 
           }
         }
         var customers_site_str = 'customers_site_id_list[]';
         var customers_site_id_list = '';
         for (i = 0; i < document.del_customers.elements[customers_site_str].length; i++) {
           if(i < document.del_customers.elements[customers_site_str].length-1){
             customers_site_id_list += document.del_customers.elements[customers_site_str][i].value+',';
           }else{
             customers_site_id_list += document.del_customers.elements[customers_site_str][i].value; 
           }
         }
         var customers_id_flag = false;
         var customers_id_confirm_str = '';
         $.ajax({
              url: 'ajax.php?&action=check_customers',   
              type: 'POST',
              dataType: 'text',
              data: 'customers_id_list='+customers_id_list+'&customers_site_id_list='+customers_site_id_list+'&customers_id_list_all='+customers_id_list_all, 
              async: false,
              success: function(msg) {
                if(msg != ''){
                  customers_id_flag = true;
                  customers_id_confirm_str = msg;
                }
              }
         });

         var customers_id_confirm_flag = false;
         if(customers_id_flag == true){

           if(confirm('<?php echo TEXT_CUSTOMERS_DELETE_CONFIRM_INFO;?>'+"\n"+customers_id_confirm_str)){

             if(confirm('<?php echo TEXT_DEL_NEWS;?>')){

               customers_id_confirm_flag = true;
             }
           }
         }else{

           if(confirm('<?php echo TEXT_DEL_NEWS;?>')){

             customers_id_confirm_flag = true;
           } 
         }
         if (customers_id_confirm_flag) {
           if (c_permission == 31) {
             document.forms.del_customers.submit(); 
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.del_customers.submit(); 
                } else {
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_customers.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_customers.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('customers_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('customers_action')[0].value = 0;
             alert('<?php echo TEXT_NEWS_MUST_SELECT;?>'); 
          }
}
<?php //选择动作?>
function customers_change_action(r_value, r_str) {
if (r_value == '1') {
   delete_select_customers(r_str, '<?php echo $ocertify->npermission;?>');
   }
}
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_customers').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_customers').css('display') != 'none') {
            if (o_submit_single){
                cid = $("#cid").val();
                $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_customers').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_customers').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_customers(ele,cID,page,action_sid,sort_name,sort_type,search_front,search_end,search_con,search_name,search_mail,search_type,search_char,search_blank,search_other,search_info,search_total){
 site_id = '<?php echo (isset($_GET['site_id'])&&$_GET['site_id']!=''?($_GET['site_id']):'-1');?>';
 var data_info_str = 'search_info=1';
 var origin_data_info_str = 'cID='+cID+'&page='+page+'&site_id='+site_id+'&search='+search_info+'&action_sid='+action_sid+'&customers_sort='+sort_name+'&customers_sort_type='+sort_type;
 if (search_front) {
   data_info_str += '&search_front='+search_front; 
 }
 if (search_end) {
   data_info_str += '&search_end='+search_end; 
 }
 if (search_con) {
   data_info_str += '&search_con='+search_con; 
 }
 if (search_name) {
   data_info_str += '&search_name='+search_name; 
 }
 if (search_mail) {
   data_info_str += '&search_mail='+search_mail; 
 }
 if (search_type) {
   data_info_str += '&search_type='+search_type; 
 }
 if (search_char) {
   data_info_str += '&search_char='+search_char; 
 }
 if (search_blank) {
   data_info_str += '&search_blank='+search_blank; 
 }
 if (search_other) {
   data_info_str += '&search_other='+search_other; 
 }
 if (search_total) {
   data_info_str += '&search_total='+search_total; 
 }
 $.ajax({
 url: 'ajax.php?&action=edit_customers&'+origin_data_info_str,
 data:data_info_str, 
 dataType: 'text',
 type: 'POST', 
 data: data_info_str, 
 async : false,
 success: function(data){
  $("div#show_customers").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(cID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_customers').height()){
offset = ele.offsetTop+$("#show_customers_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_customers').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_customers_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_customers').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_customers').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(cID == -1){
  $('#show_customers').css('top',$('#show_customers_list').offset().top);
}
$('#show_customers').css('z-index','1');
$('#show_customers').css('left',leftset);
$('#show_customers').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_customers').css('display','none');
}
function check_radio_status(r_ele)
{
  var s_radio_value = $("#s_radio").val(); 
  var n_radio_value = $(r_ele).val(); 
  
  if (s_radio_value == n_radio_value) {
    $(".table_img_list input[type='radio']").each(function(){
      $(this).attr("checked", false); 
    });
    $("#s_radio").val(''); 
  } else {
    $("#s_radio").val(n_radio_value); 
  } 
}
<?php //执行动作?>
function toggle_customers_action(c_url_str, c_permission)
{
  if (c_permission == 31) {
    window.location.href = c_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = c_url_str; 
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(c_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = c_url_str; 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
}
</script>
<?php
  // 编辑页面
  if (isset($_GET['action']) && $_GET['action'] == 'edit') {
?>
<script language="javascript"><!--
function isEmail( str ){  
var res_flag;
$.ajax({
    url: 'ajax_orders.php?action=validate_email',
    data: 'email='+str,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      res_flag = _data;      
    }
    });

  if(res_flag) return true; 
  return false; 
}
function resetStateText(theForm) {
  theForm.entry_state.value = '';
  if (theForm.entry_zone_id.options.length > 1) {
    theForm.entry_state.value = '<?php echo JS_STATE_SELECT; ?>';
  }
}

function resetZoneSelected(theForm) {
  if (theForm.entry_state.value != '') {
    theForm.entry_zone_id.selectedIndex = '0';
    if (theForm.entry_zone_id.options.length > 1) {
      theForm.entry_state.value = '<?php echo JS_STATE_SELECT; ?>';
    }
  }
}

function update_zone(theForm) {
  var NumState = theForm.entry_zone_id.options.length;
  var SelectedCountry = '';

  while(NumState > 0) {
    NumState--;
    theForm.entry_zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.entry_country_id.options[theForm.entry_country_id.selectedIndex].value;

<?php echo tep_js_zone_list('SelectedCountry', 'theForm', 'entry_zone_id'); ?>

  resetStateText(theForm);
}

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var customers_firstname = document.customers.customers_firstname.value;
  var customers_lastname = document.customers.customers_lastname.value;
<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
  var customers_email_address = document.customers.customers_email_address.value;  
  customers_email_address = customers_email_address.replace(/\u200b/g, '');
  document.customers.customers_email_address.value=customers_email_address;  

<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
  } else {
    error_message = error_message + "<?php echo JS_GENDER; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_firstname == "" || customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (customers_lastname == "" || customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }
<?php if (ACCOUNT_DOB == 'true') { ?>
  if (customers_dob == "" || customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_DOB; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_email_address == "" || customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
    error = 1;
  }
  if(!isEmail(customers_email_address)){
    error_message = error_message + "<?php echo JS_EMAIL_ADDRESS_MATCH_ERROR; ?>";
    error = 1;
  }
<?php if (ACCOUNT_STATE == 'true') { ?>
  if (document.customers.entry_zone_id.options.length <= 1) {
    if (document.customers.entry_state.value == "" || document.customers.entry_state.length < 4 ) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  } else {
    document.customers.entry_state.value = '';
    if (document.customers.entry_zone_id.selectedIndex == 0) {
       error_message = error_message + "<?php echo JS_ZONE; ?>";
       error = 1;
    }
  }
<?php } ?>

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}


function check_radio_status(r_ele)
{
  var s_radio_value = $("#s_radio").val(); 
  var n_radio_value = $(r_ele).val(); 
  
  if (s_radio_value == n_radio_value) {
    $(".table_img_list input[type='radio']").each(function(){
      $(this).attr("checked", false); 
    });
    $("#s_radio").val(''); 
  } else {
    $("#s_radio").val(n_radio_value); 
  } 
}

--></script>
<?php
  }
?>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=edit/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/cID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url;
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<?php if (isset($_GET['eof']) && $_GET['eof'] == 'error') { ?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message()">
<div id="popup_info">
<div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close"></div>
<span><?php echo TEXT_EOF_ERROR_MSG;?></span>
</div>
<div id="popup_box"></div>
<? } else {?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php }?>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input type="hidden" id="show_info_id" value="show_customers" name="show_info_id">
<div id="show_customers" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top" id="categories_right_td"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
<script type="text/javascript">
$(document).ready(function() {
<?php
   if(isset($_GET['email_address']) && isset($_GET['sid'])){

  ?>
    $("#create_customers").click();  
    $("#customers_site_id").val("<?php echo $_GET['sid'];?>");
    $("#customers_email_address").val('<?php echo $_GET['email_address'];?>');
  <?php
   }
?>
});
</script>
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2" >
<?php
  if ($_GET['action'] != 'edit') {
?>
      <tr>
        <td>
          <input type="hidden" name="search_toggle" id="search_toggle" value="<?php echo ($_GET['search'] == '1')?'1':'2';?>"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" valign="top">
            <table width="100%" border="0" cellspacing="5" cellpadding="0" class="search_space">
              <tr>
                <td>
                <?php echo HEADING_TITLE; ?>
                </td>
              </tr>
            </table>  
            </td>
            <td class="smallText" align="right" valign="top">
            <table width="100%" border="0" cellspacing="5" cellpadding="0" class="search_space">
              <tr>
                <td align="right">
                <a href="javascript:void(0);" onclick="toggle_search_content();"><?php echo IMAGE_SEARCH.'▼';?></a>
                </td>
              </tr>
            </table>  
            </td>
            <td class="smallText" align="right" id="search_content" width="230">
            <div id="show_second_search" style="<?php echo ($_GET['search'] == '1')?'display:block':'display:none';?>;"> 
            <?php echo tep_draw_form('search', FILENAME_CUSTOMERS, '', 'get'); ?>
            <table width="100%" border="0" cellspacing="5" cellpadding="0">
              <tr>
                <td align="left" nowrap="nowrap" width="10%">
                <?php echo CUSTOMERS_SEARCH_FRONT_TEXT;?> 
                </td>
                <td align="left" width="120">
                <?php echo tep_draw_input_field('search_front');?>  
                </td>
                <td align="left">
                <input type="hidden" name="search" value="1"> 
                <input type="submit" value="<?php echo IMAGE_SEARCH;?>"> 
                </td>
              </tr>
              <tr>
                <td align="left" nowrap="nowrap" width="10%">
                <?php echo CUSTOMERS_SEARCH_CONDITION_TEXT;?> 
                </td>
                <td colspan="2" align="left">
                <?php echo tep_draw_radio_field('search_con', '1', (($_GET['search'] == '1')?(($_GET['search_con'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_OR_TEXT;?> 
                <?php echo tep_draw_radio_field('search_con', '2', (($_GET['search_con'] == '2')?true:false)).'&nbsp;'.CUSTOMERS_SEARCH_AND_TEXT;?> 
                </td>
              </tr>
              <tr>
                <td align="left" nowrap="nowrap" width="10%">
                <?php echo CUSTOMERS_SEARCH_END_TEXT;?> 
                </td>
                <td colspan="2" align="left" width="120">
                <?php echo tep_draw_input_field('search_end');?>  
                </td>
              </tr>
              <tr>
                <td align="left" valign="top" nowrap="nowrap" width="10%">
                <?php echo CUSTOMERS_SEARCH_OPTION_TEXT;?> 
                </td>
                <td colspan="2">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td align="left" nowrap="nowrap">
                      <?php echo tep_draw_checkbox_field('search_name', '1', (($_GET['search'] == '1')?(($_GET['search_name'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_OPTION_NAME;?> 
                      <?php echo tep_draw_checkbox_field('search_type', '1', (($_GET['search'] == '1')?(($_GET['search_type'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_TYPE_TEXT;?> 
                      </td>
                    </tr>
                    <tr>
                      <td align="left" nowrap="nowrap">
                      <?php echo tep_draw_checkbox_field('search_mail', '1', (($_GET['search'] == '1')?(($_GET['search_mail'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_OPTION_MAIL;?> 
                      <?php echo tep_draw_checkbox_field('search_char', '1', (($_GET['search'] == '1')?(($_GET['search_char'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_CHARACTER_TEXT;?> 
                      </td>
                    </tr>
                    <tr>
                      <td align="left" nowrap="nowrap">
                      <?php echo tep_draw_checkbox_field('search_other', '1', (($_GET['search'] == '1')?(($_GET['search_other'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_OTHER_TEXT;?> 
                      <?php echo tep_draw_checkbox_field('search_blank', '1', (($_GET['search'] == '1')?(($_GET['search_blank'] == '1')?true:false):true)).'&nbsp;'.CUSTOMERS_SEARCH_BLANK_TEXT;?> 
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            </form>            
            </div> 
            <div id="show_first_search" style="<?php echo ($_GET['search'] != '1')?'display:block':'display:none';?>;"> 
            <?php echo tep_draw_form('search_form', FILENAME_CUSTOMERS, '', 'get'); ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>
                <?php echo CUSTOMERS_SEARCH_FRONT_TEXT.' '.tep_draw_input_field('search_total');?>
                <input type="hidden" name="search" value="2"> 
                <input type="submit" value="<?php echo IMAGE_SEARCH?>">
                </td> 
              </tr>
            </table>
            </form> 
            </div> 
            </td> 
          </tr>
          </table>
          <div id="search_comment" style="<?php echo ($_GET['search'] == '1')?'display:none;':'display:block';?>"> 
          <table width="100%" border="0" cellspacing="3" cellpadding="0">
            <tr>
              <td align="right">
              <?php echo CUSTOMER_SEARCH_READ_TITLE;?>
              </td>
            </tr>
          </table>
          </div> 
          </form>
        </td>
      </tr>
      <tr><td>
        <?php tep_show_site_filter(FILENAME_CUSTOMERS,false,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_customers_list">
          <tr>
            <td valign="top">
             <?php
              $customers_order_sort_name = ' customers_id'; 
              $customers_order_sort = 'desc'; 
              
              if (isset($_GET['customers_sort'])) {
                if ($_GET['customers_sort_type'] == 'asc') {
                  $type_str = '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>'; 
                  $tmp_type_str = 'desc'; 
                } else {
                  $type_str = '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>'; 
                  $tmp_type_str = 'asc'; 
                }
                switch ($_GET['customers_sort']) {
                  case 'site_id':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_SITE.$type_str.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' romaji'; 
                    break;
                  case 'm_type':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_MEMBER_TYPE.$type_str.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' customers_guest_chk'; 
                    break;
                  case 'has_exit':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type='.$tmp_type_str).'">'.BUTTON_EXIT_HISTORY_TEXT.$type_str.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' is_exit_history'; 
                    break;
                  case 'lastname':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_LASTNAME.$type_str.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' customers_lastname'; 
                    break;
                  case 'firstname':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_FIRSTNAME.$type_str.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' customers_firstname'; 
                    break;
                  case 'create_at':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_ACCOUNT_CREATED.$type_str.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' date_account_created'; 
                    break;
                  case 'update_at':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_ACTION.$type_str.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' date_account_last_modified'; 
                    break;
                  case 'order':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type='.$tmp_type_str).'">'.CUSTOMERS_HEADING_ORDER_TITLE.$type_str.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
                    $customers_order_sort_name = ' order_count'; 
                    break;
                  case 'preorder':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                    $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                    $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type='.$tmp_type_str).'">'.CUSTOMERS_HEADING_PREORDER_TITLE.$type_str.'</a>'; 
                    $customers_order_sort_name = ' preorder_count'; 
                    break;
                }
              }
              if (isset($_GET['customers_sort_type'])) {
                if ($_GET['customers_sort_type'] == 'asc') {
                  $customers_order_sort = 'asc'; 
                } else {
                  $customers_order_sort = 'desc'; 
                }
              }
              
              $customers_order_sql = $customers_order_sort_name.' '.$customers_order_sort; 
              if ($customers_order_sort_name != ' customers_id') {
                $customers_order_sql .= ' , customers_id desc'; 
              }
              if (!isset($_GET['customers_sort_type'])) {
                $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=m_type&customers_sort_type=desc').'">'.TABLE_HEADING_MEMBER_TYPE.'</a>'; 
                $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=has_exit&customers_sort_type=desc').'">'.BUTTON_EXIT_HISTORY_TEXT.'</a>'; 
                $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=lastname&customers_sort_type=desc').'">'.TABLE_HEADING_LASTNAME.'</a>'; 
                $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=firstname&customers_sort_type=desc').'">'.TABLE_HEADING_FIRSTNAME.'</a>'; 
                $customers_table_create_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=create_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACCOUNT_CREATED.'</a>'; 
                $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                $customers_table_order_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=order&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_ORDER_TITLE.'</a>'; 
                $customers_table_preorder_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=preorder&customers_sort_type=desc').'">'.CUSTOMERS_HEADING_PREORDER_TITLE.'</a>'; 
              }
              $customers_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
              $notice_box = new notice_box('','',$news_table_params);
              $customers_table_row = array();
              $customers_title_row = array();
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_customers(\'customers_id[]\');">'); 
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_id_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_type_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_order_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_preorder_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_exit_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_lastname_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_firstname_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_create_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53"','text' => $customers_table_update_str);
              $customers_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $customers_title_row);              
    $search_single = 0;
    $strip_blank_front_str = '';
    $strip_blank_end_str = '';
    
    $tmp_search_front_str = str_replace(array('　', ''), '', $_GET['search_front']); 
    $tmp_search_end_str = str_replace(array('　', ''), '', $_GET['search_end']);
    
    $search_front_str = $_GET['search_front']; 
    $search_end_str = $_GET['search_end'];
     
    $number_origin_array = array('０', '１', '２', '３', '４', '５', '６', '７', '８', '９');
    $number_new_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
    if ($_GET['search'] == '1') {
      if (($tmp_search_front_str != '') || ($tmp_search_end_str != '')) {
        if (isset($_GET['search_name']) || isset($_GET['search_mail']) || isset($_GET['search_other'])) {
          $search_single = 1;
        }
      }
    }
    if (isset($_GET['search_blank'])) {
      $strip_blank_front_str = 'replace(replace(replace(replace(replace(';
      $strip_blank_end_str = ', " ", ""), "　", ""), "\r\n", ""), "\r", ""), "\n", "")';
    }
   
    $search = ''; 
    if ($_GET['search'] == '2') {
      $search_single = 2;
      if (tep_not_null($_GET['search_total'])) {
        $keywords = tep_db_input(tep_db_prepare_input($_GET['search_total']));
        $keywords = explode(" ",$keywords);
        $key_search = '';
        foreach($keywords as $key => $key_value){
          $key_search .= 'c.customers_lastname like \'%'.$key_value.'%\' or c.customers_firstname like \'%'.$key_value.'%\' or c.customers_firstname_f like \'%'.$key_value.'%\'or c.customers_lastname_f like \'%'.$key_value.'%\'or ';
        }
        $search = "and (".$key_search." c.customers_email_address like '%" .  trim($_GET['search_total']) . "%' or c.customers_id = '".trim($_GET['search_total'])."')";
      }
    }
    
    if ($search_single == '1') {
        $front_condition_str = '';   
        $end_condition_str = '';   
        $front_condition_name_str = '';   
        $end_condition_name_str = '';   
        $front_condition_mail_str = '';   
        $end_condition_mail_str = '';   
        if (isset($_GET['search_name'])) {
          if (isset($_GET['search_type'])) {
            if ($search_front_str != '') {
              if (isset($_GET['search_char'])) {
                $front_condition_name_str .= '(concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              } else {
                $front_condition_name_str .= '(concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') like "%'.$search_front_str.'%")';   
              }
            }
           
            if ($search_end_str != '') {
              if (isset($_GET['search_char'])) {
                $end_condition_name_str .= '(concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              } else {
                $end_condition_name_str .= '(concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') like "%'.$search_end_str.'%")';   
              }
            }
          } else {
            if ($search_front_str != '') {
              $front_condition_name_str .= '(binary concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_condition_name_str .= '(binary concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') like "%'.$search_end_str.'%")';   
            }
            if (isset($_GET['search_char'])) {
              if ($search_front_str != '') {
                $front_condition_name_str .= ' or (concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              }
              if ($search_end_str != '') {
                $end_condition_name_str .= ' or (concat('.$strip_blank_front_str.'c.customers_lastname'.$strip_blank_end_str.','.$strip_blank_front_str.'c.customers_firstname'.$strip_blank_end_str.') COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              }
            } 
          }
        } 
        
        if (isset($_GET['search_mail'])) {
          if (isset($_GET['search_type'])) {
            if ($search_front_str != '') {
              if (isset($_GET['search_char'])) {
                $front_condition_mail_str .= '('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              } else {
                $front_condition_mail_str .= '('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
              }
            }
           
            if ($search_end_str != '') {
              if (isset($_GET['search_char'])) {
                $end_condition_mail_str .= '('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              } else {
                $end_condition_mail_str .= '('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
              }
            }
          } else {
            if ($search_front_str != '') {
              $front_condition_mail_str .= '(binary '.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_condition_mail_str .= '(binary '.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
            }
            if (isset($_GET['search_char'])) {
              if ($search_front_str != '') {
                $front_condition_mail_str .= ' or ('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              }
              if ($search_end_str != '') {
                $end_condition_mail_str .= ' or ('.$strip_blank_front_str.'c.customers_email_address'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              }
            } 
          }
        } 
        
        $tmp_find_name_str = substr($front_condition_name_str, 0, 4);
        $tmp_tmp_find_name_str = substr($end_condition_name_str, 0, 4);
        
        $tmp_find_mail_str = substr($front_condition_mail_str, 0, 4);
        $tmp_tmp_find_mail_str = substr($end_condition_mail_str, 0, 4);
      
        if ($tmp_find_name_str == ' and') {
          $front_condition_name_str = substr($front_condition_name_str, 4);
        }
        if ($tmp_tmp_find_name_str == ' and') {
          $end_condition_name_str = substr($end_condition_name_str, 4);
        }
        if ($tmp_find_mail_str == ' and') {
          $front_condition_mail_str = substr($front_condition_mail_str, 4);
        }
        if ($tmp_tmp_find_mail_str == ' and') {
          $end_condition_mail_str = substr($end_condition_mail_str, 4);
        }
     
        if ($front_condition_name_str != '') {
          if ($front_condition_mail_str != '') {
            $front_condition_str = '('.$front_condition_name_str.') or ('.$front_condition_mail_str.')'; 
          } else {
            $front_condition_str = $front_condition_name_str; 
          }
        } else {
          if ($front_condition_mail_str != '') {
            $front_condition_str = $front_condition_mail_str; 
          }
        }
        
        if ($end_condition_name_str != '') {
          if ($end_condition_mail_str != '') {
            $end_condition_str = '('.$end_condition_name_str.') or ('.$end_condition_mail_str.')'; 
          } else {
            $end_condition_str = $end_condition_name_str; 
          }
        } else {
          if ($end_condition_mail_str != '') {
            $end_condition_str = $end_condition_mail_str; 
          }
        }
       
        $sql_where_str = '';
        $tmp_find_str = substr($front_condition_str, 0, 4);
        $tmp_tmp_find_str = substr($end_condition_str, 0, 4);
       
        if ($tmp_find_str == ' and') {
          $front_condition_str = substr($front_condition_str, 4);
        }
        if ($tmp_tmp_find_str == ' and') {
          $end_condition_str = substr($end_condition_str, 4);
        }
        if ($_GET['search_con'] == '1') {
          if ($front_condition_str != '') {
            $sql_where_str = ' (('.$front_condition_str.')'; 
            if ($end_condition_str != '') {
              $sql_where_str .= ' or ('.$end_condition_str.')'; 
            }
            $sql_where_str .= ')'; 
          } else {
            if ($end_condition_str != '') {
              $sql_where_str .= ' ('.$end_condition_str.')'; 
            }
          }
        } else {
          if ($front_condition_str != '') {
            $sql_where_str = ' ('.$front_condition_str.')'; 
            if ($end_condition_str != '') {
              $sql_where_str .= ' and ('.$end_condition_str.')'; 
            }
          } else {
            if ($end_condition_str != '') {
              $sql_where_str .= ' ('.$end_condition_str.')'; 
            }
          }
        }
        $sql_where_str_and = '';
        if($sql_where_str!=''){
          $sql_where_str_and = ' and '.$sql_where_str;
        }
        
        $customers_query_raw = "
          select c.customers_id, 
                 c.site_id,
                 c.is_active,
                 c.customers_lastname, 
                 c.customers_firstname, 
                 c.customers_email_address, 
                 a.entry_country_id, 
                 c.customers_guest_chk,
                 ci.user_update,
                 ci.customers_info_date_account_created as date_account_created, 
                 ci.customers_info_date_account_last_modified as date_account_last_modified, 
                 ci.customers_info_date_of_last_logon as date_last_logon, 
                 ci.customers_info_number_of_logons as number_of_logons,
                 c.is_exit_history,
                 s.romaji
          from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on
          c.customers_id = a.customers_id and c.customers_default_address_id =
          a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci , ".TABLE_SITES." s where
          c.customers_id = ci.customers_info_id and c.site_id = s.id
          ".$sql_where_str_and." and " .$sql_site_where;
      if (isset($_GET['search_other'])) {
        //搜索其他 
        // table order info customer info and ref
        // order 要搜索的字段合集
        $front_order_customer_ref_info_str ='';
        $end_order_customer_ref_info_str ='';
        $front_order_customer_ref_info_arr =array();
        $end_order_customer_ref_info_arr =array();
        $sc_array = array('s.name',
            'o.torihiki_date',
            'o.torihiki_date_end',
            'o.orders_id',
            'o.customers_name',
            'o.customers_email_address',
            'o.payment_method',
            'o.date_purchased',
            'o.orders_ip',
            'o.orders_host_name',
            'o.orders_http_accept_language',
            'o.orders_system_language',
            'o.orders_user_language',
            'o.orders_screen_resolution',
            'o.orders_color_depth',
            'o.orders_flash_version',
            'o.orders_ref',
            'o.orders_comment',
            'o.shipping_fee',
            'o.code_fee');
        
        foreach($sc_array as $sc_v){
          if (isset($_GET['search_type'])) {
            if ($search_front_str != '') {
              if (isset($_GET['search_char'])) {
                $front_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              } else {
                $front_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
              }
            }
           
            if ($search_end_str != '') {
              if (isset($_GET['search_char'])) {
                $end_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              } else {
                $end_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
              }
            }
          } else {
            if ($search_front_str != '') {
              $front_order_customer_ref_info_arr[] = '(binary '.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_order_customer_ref_info_arr[] = '(binary '.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
            }
            if (isset($_GET['search_char'])) {
              if ($search_front_str != '') {
                $front_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              }
              if ($search_end_str != '') {
                $end_order_customer_ref_info_arr[] = '('.$strip_blank_front_str.$sc_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              }
            } 
          }

        }
        $front_order_customer_ref_info_str = implode(' or ',$front_order_customer_ref_info_arr);
        $end_order_customer_ref_info_str = implode(' or ',$end_order_customer_ref_info_arr);
        $order_where_sql = '';
        if ($_GET['search_con'] == '1') {
          $order_where_sql = ' (('.$front_order_customer_ref_info_str.') or ('.$end_order_customer_ref_info_str.'))';
        }else{
          $order_where_sql = ' (('.$front_order_customer_ref_info_str.') and ('.$end_order_customer_ref_info_str.'))';
        }
        if($front_order_customer_ref_info_str == ''){
          $order_where_sql = ' ('.$end_order_customer_ref_info_str.')';
        }
        if($end_order_customer_ref_info_str ==''){
          $order_where_sql = ' ('.$front_order_customer_ref_info_str.')';
        }
        $order_where_raw = 'select o.customers_id
        from '.TABLE_ORDERS.' o,'.TABLE_SITES.' s where 
        o.site_id = s.id and o.customers_id != 0 and '.$order_where_sql.' 
        group by o.customers_id';
        // 判断是否 查找订单信息
        $order_where_flag = false;
        if($temp_row_order = tep_db_fetch_array(tep_db_query($order_where_raw.' limit 1'))){
          $order_where_flag = true;
        }
        //信用调查
        $front_customer_fax_str ='';
        $end_customer_fax_str ='';
        if (isset($_GET['search_type'])) {
          if ($search_front_str != '') {
            if (isset($_GET['search_char'])) {
              $front_customer_fax_str .= '('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
            } else {
              $front_customer_fax_str .= '('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
            }
          }
         
          if ($search_end_str != '') {
            if (isset($_GET['search_char'])) {
              $end_customer_fax_str .= '('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
            } else {
              $end_customer_fax_str .= '('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
            }
          }
        } else {
          if ($search_front_str != '') {
            $front_customer_fax_str .= '(binary '.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
          }
          if ($search_end_str != '') {
            $end_customer_fax_str .= '(binary '.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
          }
          if (isset($_GET['search_char'])) {
            if ($search_front_str != '') {
              $front_customer_fax_str .= ' or ('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_customer_fax_str .= ' or ('.$strip_blank_front_str.'c.customers_fax'.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
            }
          } 
        }
        if ($_GET['search_con'] == '1') {
          $customer_where_sql = ' (('.$front_customer_fax_str.') or ('.$end_customer_fax_str.'))';
        }else{
          $customer_where_sql = ' (('.$front_customer_fax_str.') and ('.$end_customer_fax_str.'))';
        }
        if($front_customer_fax_str == ''){
          $customer_where_sql = ' ('.$end_customer_fax_str.') ';
        }
        if($end_customer_fax_str==''){
          $customer_where_sql = ' ('.$front_customer_fax_str.') ';
        }
        $customer_where_raw = 'select c.customers_id from '.TABLE_CUSTOMERS.' c where '.$customer_where_sql;
        $customer_fax_where_flag = false;
        if($temp_row_customer =  tep_db_fetch_array(tep_db_query($customer_where_raw.' limit 1'))){
          $customer_fax_where_flag = true;
        }
        //产品信息和价格
        $front_order_products_address_str ='';
        $end_order_products_address_str ='';
        $front_order_products_address_arr =array();
        $end_order_products_address_arr =array();
        $sc_op_array = array(
          'op.final_price',
          'op.products_name',
          'op.products_price',
          'op.products_tax',
          'op.products_quantity',
          'op.products_model',
          'opa.options_values_price',
          'opa.option_info',
          'ot.value');
        foreach($sc_op_array as $sc_op_v){
          if (isset($_GET['search_type'])) {
            if ($search_front_str != '') {
              if (isset($_GET['search_char'])) {
                $front_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              } else {
                $front_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
              }
            }
           
            if ($search_end_str != '') {
              if (isset($_GET['search_char'])) {
                $end_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              } else {
                $end_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
              }
            }
          } else {
            if ($search_front_str != '') {
              $front_order_products_address_arr[] = '(binary '.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_order_products_address_arr[] = '(binary '.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
            }
            if (isset($_GET['search_char'])) {
              if ($search_front_str != '') {
                $front_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              }
              if ($search_end_str != '') {
                $end_order_products_address_arr[] = '('.$strip_blank_front_str.$sc_op_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              }
            } 
          }
        }
        $front_order_products_address_str = implode(' or ',$front_order_products_address_arr);
        $end_order_products_address_str = implode(' or ',$end_order_products_address_arr);
        $order_pa_where_sql = '';
        if ($_GET['search_con'] == '1') {
          $order_pa_where_sql = ' (('.$front_order_products_address_str.') or ('.$end_order_products_address_str.'))';
        }else{
          $order_pa_where_sql = ' (('.$front_order_products_address_str.') and ('.$end_order_products_address_str.'))';
        }
        if($front_order_products_address_str==''){
          $order_pa_where_sql = ' ('.$end_order_products_address_str.') ';
        }
        if($end_order_products_address_str==''){
          $order_pa_where_sql = ' ('.$front_order_products_address_str.') ';
        }
        $order_pa_where_raw = 'select distinct o.customers_id from '.TABLE_ORDERS.' 
        o left join '.TABLE_ORDERS_PRODUCTS_ATTRIBUTES.' opa on o.orders_id =
        opa.orders_id ,'.TABLE_ORDERS_PRODUCTS.' op,'.  TABLE_ORDERS_TOTAL.' ot 
        where o.orders_id = op.orders_id 
        and o.orders_id = ot.orders_id and '.$order_pa_where_sql;
        // 判断是否 查找订单产品和订单价格
        $order_pa_where_flag = false;
        if($temp_row_order_pa = tep_db_fetch_array(tep_db_query($order_pa_where_raw.' limit 1'))){
          $order_pa_where_flag= true;
        }
        //订单状态
        $front_order_status_history_str ='';
        $end_order_status_history_str ='';
        $front_order_status_history_arr=array();
        $end_order_status_history_arr =array();
        $sc_oh_array = array(
          'osh.comments',
          'osh.user_added',
          'osh.date_added',
          'os.orders_status_name');
        foreach($sc_oh_array as $sc_oh_v){
          if (isset($_GET['search_type'])) {
            if ($search_front_str != '') {
              if (isset($_GET['search_char'])) {
                $front_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              } else {
                $front_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
              }
            }
           
            if ($search_end_str != '') {
              if (isset($_GET['search_char'])) {
                $end_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              } else {
                $end_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
              }
            }
          } else {
            if ($search_front_str != '') {
              $front_order_status_history_arr[] = '(binary '.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' like "%'.$search_front_str.'%")';   
            }
            if ($search_end_str != '') {
              $end_order_status_history_arr[] = '(binary '.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' like "%'.$search_end_str.'%")';   
            }
            if (isset($_GET['search_char'])) {
              if ($search_front_str != '') {
                $front_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_front_str.'%")';   
              }
              if ($search_end_str != '') {
                $end_order_status_history_arr[] = '('.$strip_blank_front_str.$sc_oh_v.$strip_blank_end_str.' COLLATE utf8_unicode_ci like "%'.$search_end_str.'%")';   
              }
            } 
          }
        }
        $front_order_status_history_str = implode(' or ',$front_order_status_history_arr);
        $end_order_status_history_str = implode(' or ',$end_order_status_history_arr);
        $order_oh_where_sql = '';
        if ($_GET['search_con'] == '1') {
          $order_oh_where_sql = ' (('.$front_order_status_history_str.') or ('.$end_order_status_history_str.'))';
        }else{
          $order_oh_where_sql = ' (('.$front_order_status_history_str.') and ('.$end_order_status_history_str.'))';
        }
        if($front_order_status_history_str==''){
          $order_oh_where_sql = ' ('.$end_order_status_history_str.') ';
        }
        if($end_order_status_history_str==''){
          $order_oh_where_sql = ' ('.$front_order_status_history_str.') ';
        }
        $order_oh_where_raw = 'select distinct o.customers_id from '.TABLE_ORDERS.' 
        o ,'.TABLE_ORDERS_STATUS_HISTORY.' osh ,'.TABLE_ORDERS_STATUS.' os 
        where o.orders_id = osh.orders_id and
        osh.orders_status_id = os.orders_status_id and '.$order_oh_where_sql;
        // 判断是否 查找订单产品和订单价格
        $order_oh_where_flag = false;
        if($temp_row_order_oh = tep_db_fetch_array(tep_db_query($order_oh_where_raw.' limit 1'))){
          $order_oh_where_flag= true;
        }
        $customers_query_raw_search_culom = "select distinct
                 c.customers_id, 
                 c.site_id,
                 c.is_active,
                 c.customers_lastname, 
                 c.customers_firstname, 
                 c.customers_email_address, 
                 a.entry_country_id, 
                 c.customers_guest_chk,
                 ci.user_update,
                 ci.customers_info_date_account_created as date_account_created, 
                 ci.customers_info_date_account_last_modified as date_account_last_modified, 
                 ci.customers_info_date_of_last_logon as date_last_logon, 
                 ci.customers_info_number_of_logons as number_of_logons,
                 c.is_exit_history,
                 s.romaji
        from ";
        $customers_query_raw_table = TABLE_CUSTOMERS . " c left join " .
          TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and
          c.customers_default_address_id = a.address_book_id left join 
          ".TABLE_ORDERS." o on c.customers_id = o.customers_id ";
        $order_oh_where_flag = false;
        $order_pa_where_flag = false;
        if($order_pa_where_flag){
          $customers_query_raw_table .= " left join ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa on o.orders_id = opa.orders_id ,";
          $customers_query_raw_table .= TABLE_ORDERS_PRODUCTS." op,".  TABLE_ORDERS_TOTAL." ot";
        }
        if($order_oh_where_flag){
          $customers_query_raw_table .= ", ".TABLE_ORDERS_STATUS_HISTORY." osh ,".TABLE_ORDERS_STATUS." os";
        }
        $customers_query_raw_table .= ", ".TABLE_CUSTOMERS_INFO." ci , ".TABLE_SITES." s ";

        $customers_query_raw_where = " where c.customers_id = ci.customers_info_id and c.site_id = s.id  and " .$sql_site_where;
        $where_column_arr = array();
        if($sql_where_str!=""){
          $where_column_arr[] = $sql_where_str;
        }
        if($order_where_flag){
          $customers_query_raw_where .= " and o.site_id = s.id and o.customers_id != 0 ";
          $where_column_arr[] = $order_where_sql;
        }
        if($customer_fax_where_flag){
          $where_column_arr[] = $customer_where_sql;
        }
        if($order_oh_where_flag){
          $customers_query_raw_where .= " and o.orders_id = osh.orders_id and osh.orders_status_id = os.orders_status_id ";
          $where_column_arr[] = $order_oh_where_sql;
        }
        if($order_pa_where_flag){
          $customers_query_raw_where .= " and o.orders_id = op.orders_id and o.orders_id = ot.orders_id ";
          $where_column_arr[] = $order_pa_where_sql;
        }
        if(!empty($where_column_arr)){
          $where_column_str = implode(' or ',$where_column_arr);
          $customers_query_raw_where .= " and (".$where_column_str.") ";
        }
        //所有的 检索判断 ".$sql_where_str."
        $customers_query_raw = $customers_query_raw_search_culom.$customers_query_raw_table.$customers_query_raw_where;
      }
    }
    
    if (($search_single == '0') || ($search_single == '2')) {
      $customers_query_raw = "
        select c.customers_id, 
               c.site_id,
               c.is_active,
               c.customers_lastname, 
               c.customers_firstname, 
               c.customers_email_address, 
               a.entry_country_id, 
               c.customers_guest_chk,
               ci.user_update,
               ci.customers_info_date_account_created as date_account_created, 
               ci.customers_info_date_account_last_modified as date_account_last_modified, 
               ci.customers_info_date_of_last_logon as date_last_logon, 
               ci.customers_info_number_of_logons as number_of_logons,
               c.is_exit_history,
               s.romaji
        from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on
        c.customers_id = a.customers_id and c.customers_default_address_id =
        a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci , ".TABLE_SITES." s where
        c.customers_id = ci.customers_info_id and c.site_id = s.id and "
        .$sql_site_where. " " .$search;
    }
    $customers_query_raw .= ' order by '.$customers_order_sql;
    // 订单 预约 次数处理
//    $customers_query_raw = "select t3.*,count(t3.customers_id) as preorder_count from (select t1.*,count(t1.customers_id) as order_count from (".$customers_query_raw.") t1 left join ".TABLE_ORDERS." t2 on t1.customers_id = t2.customers_id and t1.site_id = t2.site_id group by t1.customers_id) t3 left join ".TABLE_PREORDERS." t4 on t4.customers_id=t3.customers_id and t3.site_id = t4.site_id group by t3.customers_id order by ".$customers_order_sql;
    
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    $customers_numrows = tep_db_num_rows($customers_query);
    while ($customers = tep_db_fetch_array($customers_query)) {
      if (
          ((!isset($_GET['cID']) || !$_GET['cID']) || (@$_GET['cID'] == $customers['customers_id'])) 
          && (!isset($cInfo) || !$cInfo)
        ) {
        $country_query = tep_db_query("
            select countries_name 
            from " . TABLE_COUNTRIES . " 
            where countries_id = '" . $customers['entry_country_id'] . "'
        ");
        $country = tep_db_fetch_array($country_query);

        $reviews_query = tep_db_query("
            select count(*) as number_of_reviews 
            from " . TABLE_REVIEWS . " 
            where customers_id = '" . $customers['customers_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);

        $customer_info = tep_array_merge($country, $customers, $reviews);

        $cInfo_array = tep_array_merge($customers, $customer_info);
        $cInfo = new objectInfo($cInfo_array);
      }

    if($customers['customers_guest_chk'] == 1) {
      $type = TABLE_HEADING_MEMBER_TYPE_GUEST;
    } else {
      $type = TABLE_HEADING_MEMBER_TYPE_MEMBER;
    }

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
      if ($_GET['current_cuid'] == $customers['customers_id']) {
        $customers_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
      } else {
        $customers_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
      $customers_info = array();
      if(in_array($customers['site_id'],$site_array)){
         $customers_checkbox = '<input type="checkbox" '.(($customers['is_active'] != '1')?'disabled="disabled"':'').' name="customers_id[]" value="'.$customers['customers_id'].'"><input type="hidden" name="customers_site_id_list[]" value="'.$customers['site_id'].'">';
      }else{
         $customers_checkbox = '<input disabled="disabled" type="checkbox" name="customers_id[]" value="'.$customers['customers_id'].'"><input type="hidden" name="customers_site_id_list[]" value="'.$customers['site_id'].'">';
      }
      $customers_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $customers_checkbox 
          );
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').tep_get_site_romaji_by_id($customers['site_id']).(($customers['is_active'] != '1')?'</font>':'')
          );
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').$type.(($customers['is_active'] != '1')?'</font>':'') 
          );
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').'order'.(($customers['is_active'] != '1')?'</font>':'') 
          );
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').'preorder'.(($customers['is_active'] != '1')?'</font>':'') 
          );
      if ($customers['is_exit_history'] == '1') {
        $has_exit_history = CUSTOMERS_YES_TEXT; 
      } else {
        $has_exit_history = CUSTOMERS_NO_TEXT; 
      }
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').$has_exit_history.(($customers['is_active'] != '1')?'</font>':'') 
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').htmlspecialchars($customers['customers_lastname']).(($customers['is_active'] != '1')?'</font>':'') 
          );
        $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').htmlspecialchars($customers['customers_firstname']).(($customers['is_active'] != '1')?'</font>':'') 
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').tep_date_short($customers['date_account_created']).(($customers['is_active'] != '1')?'</font>':'') 
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent"', 'text'   => '<a href="javascript:void(0)" onclick="show_customers(this,'.$customers['customers_id'].','.$_GET['page'].','.(isset($customers['site_id'])?$customers['site_id']:'-1').', \''.(isset($_GET['customers_sort'])?$_GET['customers_sort']:'0').'\', \''.(isset($_GET['customers_sort_type'])?$_GET['customers_sort_type']:'0').'\', \''.(isset($_GET['search_front'])?$_GET['search_front']:'').'\',\''.(isset($_GET['search_end'])?$_GET['search_end']:'').'\',\''.(isset($_GET['search_con'])?$_GET['search_con']:'').'\',\''.(isset($_GET['search_name'])?$_GET['search_name']:'').'\',\''.(isset($_GET['search_mail'])?$_GET['search_mail']:'').'\',\''.(isset($_GET['search_type'])?$_GET['search_type']:'').'\',\''.(isset($_GET['search_char'])?$_GET['search_char']:'').'\',\''.(isset($_GET['search_blank'])?$_GET['search_blank']:'').'\',\''.(isset($_GET['search_other'])?$_GET['search_other']:'').'\',\''.(isset($_GET['search'])?$_GET['search']:'').'\',\''.(isset($_GET['search_total'])?$_GET['search_total']:'').'\')">' .  tep_get_signal_pic_info(isset($customers['date_account_last_modified']) && $customers['date_account_last_modified'] != null?$customers['date_account_last_modified']:$customers['date_account_created']) . '</a>');
       $customers_table_row[] = array('params' => $customers_params, 'text' => $customers_info);
    }
    $news_form = tep_draw_form('del_customers',FILENAME_CUSTOMERS,'action=deleteconfirm&site_id='.$_GET['site_id'].'&page='.$_GET['page'].(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['customers_sort'])?'&customers_sort='.$_GET['customers_sort']:'').(isset($_GET['customers_sort_type'])?'&customers_sort_type='.$_GET['customers_sort_type']:'').(isset($_GET['search_front'])?'&search_front='.$_GET['search_front']:'').(isset($_GET['search_end'])?'&search_end='.$_GET['search_end']:'').(isset($_GET['search_con'])?'&search_con='.$_GET['search_con']:'').(isset($_GET['search_name'])?'&search_name='.$_GET['search_name']:'').(isset($_GET['search_mail'])?'&search_mail='.$_GET['search_mail']:'').(isset($_GET['search_type'])?'&search_type='.$_GET['search_type']:'').(isset($_GET['search_char'])?'&search_char='.$_GET['search_char']:'').(isset($_GET['search_blank'])?'&search_blank='.$_GET['search_blank']:'').(isset($_GET['search_other'])?'&search_other='.$_GET['search_other']:'').(isset($_GET['search_total'])?'&search_total='.$_GET['search_total']:''));
    $notice_box->get_form($news_form);
    $notice_box->get_contents($customers_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();

?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="3" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                     <?php 
                      if($customers_numrows > 0){
                      if($ocertify->npermission >= 15){
                           echo '<select name="customers_action" onchange="customers_change_action(this.value, \'customers_id[]\');">';
                           echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                           echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                           echo '</select>';
                       }
                      }else{
                           echo TEXT_DATA_EMPTY;
                      }
                   ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID', 'current_cuid'))); ?></div></td>
                  </tr>
                  <tr>
                     <td align="right" colspan="2">
                       <?php  
                       //通过site_id判断是否允许新建
                       if(array_intersect($show_list_array,$site_array)){
                       echo '&nbsp;<a href="javascript:void(0)" onclick="show_customers(this,-1,'.$_GET['page'].','.(isset($customers['site_id'])?$customers['site_id']:'-1').', \''.(isset($_GET['customers_sort'])?$_GET['customers_sort']:'0').'\', \''.(isset($_GET['customers_sort_type'])?$_GET['customers_sort_type']:'0').'\', \''.(isset($_GET['search_front'])?$_GET['search_front']:'').'\',\''.(isset($_GET['search_end'])?$_GET['search_end']:'').'\',\''.(isset($_GET['search_con'])?$_GET['search_con']:'').'\',\''.(isset($_GET['search_name'])?$_GET['search_name']:'').'\',\''.(isset($_GET['search_mail'])?$_GET['search_mail']:'').'\',\''.(isset($_GET['search_type'])?$_GET['search_type']:'').'\',\''.(isset($_GET['search_char'])?$_GET['search_char']:'').'\',\''.(isset($_GET['search_blank'])?$_GET['search_blank']:'').'\',\''.(isset($_GET['search_other'])?$_GET['search_other']:'').'\',\''.(isset($_GET['search'])?$_GET['search']:'').'\',\''.(isset($_GET['search_total'])?$_GET['search_total']:'').'\');check_guest(1)">' .tep_html_element_button(IMAGE_NEW_PROJECT,'id="create_customers"') . '</a>';
                       }else{
                       echo '&nbsp;' .tep_html_element_button(IMAGE_NEW_PROJECT,'id="create_customers" disabled="disabled"');
                       }
                       ?>
                     </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
