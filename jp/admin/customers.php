<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  require('includes/step-by-step/new_application_top.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
   } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_CUSTOMERS);
     $sql_site_where = 'site_id in ('.$show_list_str.')';
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
        $customers_sql = "insert into ".TABLE_CUSTOMERS."
          (customers_id,customers_firstname,customers_lastname,customers_firstname_f,customers_lastname_f,customers_email_address,customers_telephone,customers_fax,customers_newsletter,customers_gender,customers_dob,is_seal,pic_icon,is_send_mail,send_mail_time,reset_flag,reset_success,site_id,customers_password,origin_password)
          values
          (null,'".$customers_firstname."','".$customers_lastname."','".$customers_firstname_f."','".$customers_lastname_f."','".$customers_email_address."','".$customers_telephone."','".$customers_fax."','".$customers_newsletter."','".$customers_gender."','".$customers_dob."','".$customers_is_seal."','".$customers_pic_icon."','".$customers_is_send_mail."','".time()."','1','1','".$_POST['site_id']."','".$customers_password."','".$origin_password."')";
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
        tep_redirect(tep_href_link(FILENAME_CUSTOMERS,'site_id='.$_POST['site_id']));
        break;
      case 'update':
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
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script>
function check_password(){
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
   if(data == 1){
     email_error = 'true';
   }else{
     email_error = 'false';
   }
  }
 });
 }else{
     email_error = 'false';
 }
  if(email_error == 'true'){
    $("#error_email").html("<?php echo TEXT_ERROR_EMAIL;?>");
  }else if(check_is_active == 1 && (password != once_again_password || password == '')){
       $("#error_info").html("<?php echo TEXT_ERROR_INFO;?>"); 
  }else{
       document.forms.customers.submit();  
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
                       if (check_flag == true) {
                           document.del_customers.elements[customers_str][i].checked = true;
                           } else {
                           document.del_customers.elements[customers_str][i].checked = false;
                           }
                       }
                   }
             }
}
function delete_select_customers(customers_str){
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
         if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
               document.forms.del_customers.submit(); 
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
   delete_select_customers(r_str);
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
                check_password();
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

function show_customers(ele,cID,page,action_sid){
 site_id = '<?php echo (isset($_GET['site_id'])&&$_GET['site_id']!=''?($_GET['site_id']):'-1');?>';
 var search = $('#search').val();
 $.ajax({
 url: 'ajax.php?&action=edit_customers',
 data: {cID:cID,page:page,site_id:site_id,search:search,action_sid:action_sid} ,
 dataType: 'text',
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
  
  var customers_firstname_f = document.customers.customers_firstname_f.value;
  var customers_lastname_f = document.customers.customers_lastname_f.value;
  
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2" >
<?php
  if ($_GET['action'] != 'edit') {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><?php echo tep_draw_form('search', FILENAME_CUSTOMERS, tep_get_all_get_params(), 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search'); ?>
      <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
            <br><?php echo CUSTOMER_SEARCH_READ_TITLE;?> 
      </td>
          </form></tr>
        </table></td>
      </tr>
      <tr><td>
        <?php tep_show_site_filter(FILENAME_CUSTOMERS,false,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_customers_list">
          <tr>
            <td valign="top">
             <input type="hidden" id="search" value="<?php echo $_GET['search'];?>">
             <?php
              $customers_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
              $notice_box = new notice_box('','',$news_table_params);
              $customers_table_row = array();
              $customers_title_row = array();
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_customers(\'customers_id[]\');">'); 
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_SITE);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_MEMBER_TYPE);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_LASTNAME);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_FIRSTNAME);
              $customers_title_row[] = array('align' => 'right','params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_ACCOUNT_CREATED);
              $customers_title_row[] = array('align' =>'right' , 'params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_ACTION);
              $customers_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $customers_title_row);              
    $search = '';
    if ( isset($_GET['search']) && ($_GET['search']) && (tep_not_null($_GET['search'])) ) {
      $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
      $search = "and (c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%' or c.customers_firstname_f like '%" . $keywords . "%'  or c.customers_lastname_f like '%" . $keywords . "%')";
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
	     c.is_quited,
	     ci.user_update,
             ci.customers_info_date_account_created as date_account_created, 
             ci.customers_info_date_account_last_modified as date_account_last_modified, 
             ci.customers_info_date_of_last_logon as date_last_logon, 
             ci.customers_info_number_of_logons as number_of_logons 
      from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on
      c.customers_id = a.customers_id and c.customers_default_address_id =
      a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci where c.customers_id = ci.customers_info_id and " .$sql_site_where. " " . $search . " 
      order by c.customers_id DESC
    ";
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

    if($customers['customers_guest_chk'] == 1 || $customers['is_quited'] == 1) {
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
         $customers_checkbox = '<input type="checkbox" '.(($customers['is_active'] != '1')?'disabled="disabled"':'').' name="customers_id[]" value="'.$customers['customers_id'].'">';
      }else{
         $customers_checkbox = '<input disabled="disabled" type="checkbox" name="customers_id[]" value="'.$customers['customers_id'].'">';
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
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').htmlspecialchars($customers['customers_lastname']).(($customers['is_active'] != '1')?'</font>':'') 
          );
        $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').htmlspecialchars($customers['customers_firstname']).(($customers['is_active'] != '1')?'</font>':'') 
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent" align="right" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'current_cuid')).'current_cuid='.$customers['customers_id']).'\';"',
           'text'   => (($customers['is_active'] != '1')?'<font color="#999999">':'').tep_date_short($customers['date_account_created']).(($customers['is_active'] != '1')?'</font>':'') 
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent" align="right"',
           'text'   => '<a href="javascript:void(0)"
           onclick="show_customers(this,'.$customers['customers_id'].','.$_GET['page'].','.(isset($customers['site_id'])?$customers['site_id']:'-1').')">' . tep_get_signal_pic_info($customers['date_account_last_modified']) . '</a>'
          );
       $customers_table_row[] = array('params' => $customers_params, 'text' => $customers_info);
    }
    $news_form = tep_draw_form('del_customers',FILENAME_CUSTOMERS,'action=deleteconfirm&site_id='.$_GET['site_id'].'&page='.$_GET['page'].(isset($_GET['search'])?'&search='.$_GET['search']:''));
    $notice_box->get_form($news_form);
    $notice_box->get_contents($customers_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();

?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                     <?php 
                      if($customers_numrows > 0){
                      if($ocertify->npermission == 15){
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
                       if(in_array($show_list_array[0],$site_array)){
                       echo '&nbsp;<a href="javascript:void(0)" onclick="show_customers(this,-1,'.$_GET['page'].','.(isset($customers['site_id'])?$customers['site_id']:'-1').')">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>';
                       }else{
                       echo '&nbsp;' .tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"');
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
