<?php
/*
   $Id$
 */
require('includes/application_top.php');
require(DIR_FS_ADMIN . '/classes/notice_box.php');
$is_u_disabled = false;
if ($ocertify->npermission != 31) {
  if (!empty($_SESSION['site_permission'])) {
    $tmp_u_array = explode(',', $_SESSION['site_permission']);
    if (!in_array('0', $tmp_u_array)) {
      $is_u_disabled = true;
    }
  } else {
    $is_u_disabled = true;
  }
}

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  /*-----------------------------------
   case 'insert_user_info' 创建用户  
   case 'update_user_info' 更新用户  
   case 'delete_select_user' 删除所选用户 
   case 'delete_user_confirm' 删除指定用户 
   case 'setflag' 设置用户状态 
   ----------------------------------*/
     case 'insert_user_info':
     case 'update_user_info':
       tep_isset_eof(); 
       $user_error = false;
       if (empty($_POST['userid'])) {
         $user_error = true; 
       } else {
         $userid_len = strlen($_POST['userid']); 
         if ($userid_len < 2) {
           $user_error = true; 
         } else {
           if (ereg('[[:print:]]', $_POST['userid']) == false) {
             $user_error = true; 
           }
         }
       }
       
       if (empty($_POST['name'])) {
         $user_error = true; 
       }
      
       if (isset($_POST['user_password']) && trim($_POST['user_password']) != '') {
         if (empty($_POST['user_password'])) {
           $user_error = true; 
         } else {
           $pwd_len = strlen($_POST['user_password']); 
           if ($pwd_len < 2) {
             $user_error = true; 
           } else {
             if (ereg('[[:print:]]', $_POST['user_password']) == false) {
               $user_error = true; 
             }
           }
         }
       }
     
       if (!empty($_POST['user_email'])) {
         if (!tep_validate_new_email($_POST['user_email'])) {
           $user_error = true; 
         }
       }
      
       if ($_GET['action'] == 'insert_user_info') {
         $exists_user_query = tep_db_query("select * from ".TABLE_USERS." where userid = '".$_POST['userid']."'"); 
         if (tep_db_num_rows($exists_user_query) > 0) {
           $user_error = true; 
         }
       } 
       
       if (!$user_error) {
         $permission_num = 7;
         $permission_list_str = '';
         switch ($_POST['u_permission']) {
           case 'admin':
             $permission_num = 15;
             break;
           case 'chief':
             $permission_num = 10;
             break;
         }
         if (!empty($_POST['user_permission_info'])) {
           $permission_list_str = implode(',', $_POST['user_permission_info']);
         }
         if ($_GET['action'] == 'insert_user_info') {
           $cryot_password = (string) crypt($_POST['user_password']); 
           tep_db_query("insert into `".TABLE_USERS."` values('".$_POST['userid']."', '".$cryot_password."', '".$_POST['name']."', '".$_POST['user_email']."', '".(isset($_POST['user_rule'])?$_POST['user_rule']:'')."', '".$_SESSION['user_name']."', '".date('Y-m-d H:i:s', time())."', '".$_SESSION['user_name']."', '".date('Y-m-d H:i:s', time())."', '1')");  
           tep_db_query("insert into `".TABLE_PERMISSIONS."` values ('".$_POST['userid']."', '".$permission_num."', '".$permission_list_str."')"); 
           if (!empty($_POST['ip_limit'])) {
             $ip_limit_array = explode("\n", $_POST['ip_limit']); 
             foreach ($ip_limit_array as $ip_key => $ip_value) {
               $split_ip = explode('.', $ip_value);
               $split_error = false; 
               if (count($split_ip) != 4) {
                 continue; 
               }
               if ((is_numeric(trim($split_ip[0])) || trim($split_ip[0]) == '*') && (is_numeric(trim($split_ip[1])) || trim($split_ip[1]) == '*') && (is_numeric(trim($split_ip[2])) || trim($split_ip[2]) == '*') && (is_numeric(trim($split_ip[3])) || trim($split_ip[3]) == '*')) {
               } else {
                 $split_error = true; 
               }
               if ($split_error) {
                 continue; 
               }
               $ip_insert_sql = "insert `user_ip` values('".$_POST['userid']."', '".$ip_value."')"; 
               tep_db_query($ip_insert_sql); 
             }
           }
         
           $update_users_query = tep_db_query("select configuration_description from ". TABLE_CONFIGURATION ." where configuration_key = 'PERSONAL_SETTING_ORDERS_SITE'");
           $update_users_res = tep_db_fetch_array($update_users_query);
           
           $users_setting_array = array();
           if($update_users_res['configuration_description'] != ''){
             $users_setting_array = unserialize($update_users_res['configuration_description']);
           }
           $users_setting_array[$_POST['userid']]['create_users'] = $_SESSION['user_name'];
           $users_setting_array[$_POST['userid']]['create_time'] = date('Y-m-d H:i:s');
           $update_users_str = serialize($users_setting_array);
           tep_db_query("update `". TABLE_CONFIGURATION ."` set `configuration_description` = '".$update_users_str."' where `configuration_key` = 'PERSONAL_SETTING_ORDERS_SITE'");
           if (isset($_POST['user_rule'])) {
             $exists_letter_query = tep_db_query("select * from ".TABLE_LETTERS." where letter = '".$_POST['letter']."'"); 
             $exists_letter_res = tep_db_fetch_array($exists_letter_query); 
             if (empty($exists_letter_res['userid'])) {
               if (make_rand_pwd($_POST['user_rule'])) {
                 tep_db_query("update `".TABLE_LETTERS."` set `userid` = '".$_POST['userid']."' where `letter` = '".$_POST['letter']."'"); 
               }
             }
           }
         } else if ($_GET['action'] == 'update_user_info') {
           $update_user_raw = tep_db_query("select * from ".TABLE_USERS." where userid = '".$_POST['userid']."'"); 
           $update_user_res = tep_db_fetch_array($update_user_raw); 
           if ($update_user_res['status'] == '1') {
             $rule_info_str = (isset($_POST['user_rule'])?$_POST['user_rule']:''); 
           } else {
             $rule_info_str = $update_user_res['rule']; 
           }
           if (isset($_POST['user_password']) && (trim($_POST['user_password']) != '')) {
             $cryot_password = (string) crypt($_POST['user_password']); 
             tep_db_query("update `".TABLE_USERS."` set `name` = '".$_POST['name']."', `email` = '".$_POST['user_email']."', `password` = '".$cryot_password."', `rule` = '".$rule_info_str."', `user_update` = '".$_SESSION['user_name']."', `date_update` = '".date('Y-m-d H:i:s', time())."' where `userid` = '".$_POST['userid']."'"); 
           } else {
             tep_db_query("update `".TABLE_USERS."` set `name` = '".$_POST['name']."', `email` = '".$_POST['user_email']."', `rule` = '".$rule_info_str."', `user_update` = '".$_SESSION['user_name']."', `date_update` = '".date('Y-m-d H:i:s', time())."' where `userid` = '".$_POST['userid']."'"); 
           }
           if (isset($_POST['u_permission'])) {
             if (trim($_POST['other_site']) != '') {
               $tmp_mege_array = array(); 
               $tmp_other_site = explode(',', $_POST['other_site']);  
               $tmp_origin_site = explode(',', $permission_list_str); 
               if ($permission_list_str != '') {
                 $tmp_merge_array = array_merge($tmp_origin_site, $tmp_other_site); 
               } else {
                 $tmp_merge_array = $tmp_other_site; 
               }
               $permission_list_str = implode(',', $tmp_merge_array); 
             } 
             tep_db_query("update `".TABLE_PERMISSIONS."` set `permission` = '".$permission_num."', `site_permission` = '".$permission_list_str."' where `userid` = '".$_POST['userid']."'"); 
           } else {
             $tmp_s_list_array = array();
             $tmp_s_list_array[] = 0;
             $tmp_s_list_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc"); 
             while ($tmp_s_list_res = tep_db_fetch_array($tmp_s_list_raw)) {
               $tmp_s_list_array[] = $tmp_s_list_res['id'];
             }
             tep_db_query("update `".TABLE_PERMISSIONS."` set `site_permission` = '".implode(',', $tmp_s_list_array)."' where `userid` = '".$_POST['userid']."'"); 
           }
           
           tep_db_query("delete from `user_ip` where `userid` = '".$_POST['userid']."'");
           if (!empty($_POST['ip_limit'])) {
             $ip_limit_arr = explode("\n", $_POST['ip_limit']); 
             foreach ($ip_limit_arr as $ip_key => $ip_value) {
               $split_ip = explode('.', $ip_value);
               $split_error = false; 
               if (count($split_ip) != 4) {
                 continue; 
               }
               if ((is_numeric(trim($split_ip[0])) || trim($split_ip[0]) == '*') && (is_numeric(trim($split_ip[1])) || trim($split_ip[1]) == '*') && (is_numeric(trim($split_ip[2])) || trim($split_ip[2]) == '*') && (is_numeric(trim($split_ip[3])) || trim($split_ip[3]) == '*')) {
               } else {
                 $split_error = true; 
               }
               if ($split_error) {
                 continue; 
               }
               $ip_insert_sql = "insert `user_ip` values('".$_POST['userid']."', '".$ip_value."')"; 
               tep_db_query($ip_insert_sql);
             }
           } 
           if ($update_user_res['status'] == '1') {
             tep_db_query("update `".TABLE_LETTERS."` set `userid` = null where `userid` = '".$_POST['userid']."'");   
           } 
           if (isset($_POST['user_rule'])) {
             if (make_rand_pwd($_POST['user_rule'])) {
               tep_db_query("update `".TABLE_LETTERS."` set `userid` = '".$_POST['userid']."' where `letter` = '".$_POST['letter']."'");   
             }
           }
         }
       }
       tep_redirect(tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_e_id', 'flag', 'site_id')))); 
       break;
     case 'delete_select_user':
     case 'delete_user_confirm':
       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         tep_isset_eof(); 
       }
       
       $delete_user_array = array();
       if ($_GET['action'] == 'delete_user_confirm') {
         $delete_user_array[] = $_GET['user_e_id']; 
       } else if ($_GET['action'] == 'delete_select_user') {
         if (isset($_POST['user_list_id'])) {
           $delete_user_array = $_POST['user_list_id']; 
         } 
       }
       
       if (!empty($delete_user_array)) {
         foreach ($delete_user_array as $u_d_key => $u_d_value) {
           tep_db_query("delete from ".TABLE_USERS." where userid = '".$u_d_value."'"); 
           tep_db_query("delete from ".TABLE_PERMISSIONS." where userid = '".$u_d_value."'"); 
           tep_db_query("delete from user_ip where userid = '".$u_d_value."'"); 
           tep_db_query("delete from notes where author = '".$u_d_value."'"); 
           tep_db_query("update ".TABLE_LETTERS." set userid = null where userid = '".$u_d_value."'"); 
           
           $orders_users_query = tep_db_query("select orders_id, read_flag from ". TABLE_ORDERS ." where read_flag != ''");
           while ($orders_users_array = tep_db_fetch_array($orders_users_query)) {
             $orders_users_info_array = explode('|||', $orders_users_array['read_flag']);   
             if (in_array($u_d_value, $orders_users_info_array)) {
               unset($orders_users_info_array[array_search($u_d_value, $orders_users_info_array)]);
               $orders_users_info_str = implode('|||', $orders_users_info_array);
               tep_db_query("update ". TABLE_ORDERS ." set read_flag='".$orders_users_info_str."' where orders_id='".$orders_users_array['orders_id']."'");
             }
           }

           $preorders_users_query = tep_db_query("select orders_id, read_flag from ". TABLE_PREORDERS ." where read_flag != ''");
           while ($preorders_users_array = tep_db_fetch_array($preorders_users_query)) {
             $preorders_users_info_array = explode('|||',$preorders_users_array['read_flag']);   
             if (in_array($u_d_value, $preorders_users_info_array)) {
               unset($preorders_users_info_array[array_search($u_d_value, $preorders_users_info_array)]);
               $preorders_users_info_str = implode('|||', $preorders_users_info_array);
               tep_db_query("update ". TABLE_PREORDERS ." set read_flag='".$preorders_users_info_str."' where orders_id='".$preorders_users_array['orders_id']."'");
             }
           }

           if (PERSONAL_SETTING_ORDERS_SITE != '') {
             $orders_site_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
             if (array_key_exists($u_d_value, $orders_site_array)) {
               unset($orders_site_array[$u_d_value]);
               $orders_site_str = serialize($orders_site_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_site_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SITE'");
             }
           }
  
           if (PERSONAL_SETTING_ORDERS_WORK != '') {
             $orders_work_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
             if (array_key_exists($u_d_value, $orders_work_array)) {
               unset($orders_work_array[$u_d_value]);
               $orders_work_str = serialize($orders_work_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_work_str."' where configuration_key='PERSONAL_SETTING_ORDERS_WORK'");
             }
           }

           if (PERSONAL_SETTING_ORDERS_SORT != '') {
             $orders_sort_array = unserialize(PERSONAL_SETTING_ORDERS_SORT);
             if (array_key_exists($u_d_value, $orders_sort_array)) {
               unset($orders_sort_array[$u_d_value]);
               $orders_sort_str = serialize($orders_sort_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_sort_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SORT'");
             }
           }

           if (PERSONAL_SETTING_PREORDERS_SITE != '') {
             $preorders_site_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
             if(array_key_exists($u_d_value, $preorders_site_array)){
               unset($preorders_site_array[$u_d_value]);
               $preorders_site_str = serialize($preorders_site_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_site_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SITE'");
             }
           }
  
           if (PERSONAL_SETTING_PREORDERS_WORK != '') {
             $preorders_work_array = unserialize(PERSONAL_SETTING_PREORDERS_WORK);
             if (array_key_exists($u_d_value, $preorders_work_array)) {
               unset($preorders_work_array[$u_d_value]);
               $preorders_work_str = serialize($preorders_work_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_work_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_WORK'");
             }
           }

  
           if (PERSONAL_SETTING_PREORDERS_SORT != '') {
             $preorders_sort_array = unserialize(PERSONAL_SETTING_PREORDERS_SORT);
             if (array_key_exists($u_d_value, $preorders_sort_array)) {
               unset($preorders_sort_array[$u_d_value]);
               $preorders_sort_str = serialize($preorders_sort_array);
               tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_sort_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SORT'");
             }
           } 
         }
       }
       tep_redirect(tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_e_id', 'user_info_id', 'site_id')))); 
       break;
     case 'setflag':
       tep_db_query("update `".TABLE_USERS."` set `status` = '".$_GET['flag']."', `user_update` = '".$_SESSION['user_name']."', `date_update` = '".date('Y-m-d H:i:s', time())."' where userid = '".$_GET['user_e_id']."'"); 
       tep_redirect(tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_e_id', 'flag', 'site_id')))); 
       break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
var box_warp_height = 0;
var origin_offset_symbol = 0;
var o_submit_single = true;
var first_new_single = true;
window.onresize = resize_user_page;
<?php //缩放页面?>
function resize_user_page()
{
  var s_offset = $('#show_popup_info').css('top'); 
  s_offset = s_offset.replace('px', '');
  tmp_s_offset = parseInt(s_offset, 10)
  if ($('#show_popup_info').height() + tmp_s_offset > $('.box_warp').height()) {
    $('.box_warp').height($('#show_popup_info').height() + tmp_s_offset); 
  }
}
<?php //选择动作?>
function user_change_action(current_value, change_info)
{
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (current_value == '1') {
        sel_num = 0;
        if (document.user_list_form.elements[change_info].length == null) {
          if (document.user_list_form.elements[change_info].checked == true) {
            sel_num = 1;
          }
        } else {
          for (i = 0; i < document.user_list_form.elements[change_info].length; i++) {
            if (document.user_list_form.elements[change_info][i].checked == true) {
              sel_num = 1;
              break;
            }
          }
        }
        if (sel_num == 1) {
          if (confirm('<?php echo TEXT_DEL_USER;?>')) {
            <?php
            if ($ocertify->npermission > 15) {
            ?>
              document.forms.user_list_form.submit(); 
            <?php
            } else {
            ?>
            if (tmp_msg_arr[0] == '0') {
              document.forms.user_list_form.submit(); 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.user_list_form.action),
                  async: false,
                  success: function(msg_info) {
                    document.forms.user_list_form.submit(); 
                  }
                }); 
              } else {
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                document.getElementsByName('user_list_action')[0].value = 0; 
              }
            }
            <?php
            }
            ?>
          } else {
            document.getElementsByName('user_list_action')[0].value = 0; 
          }
        } else {
          document.getElementsByName('user_list_action')[0].value = 0; 
          alert('<?php echo TEXT_USER_MUST_SELECT;?>'); 
        } 
      }
      
      
    }
  });
  
}
<?php //全选?>
function select_all_user()
{
  var check_flag = document.user_list_form.all_user_select.checked;
  
  if (document.user_list_form.elements['user_list_id[]']) {
    if (document.user_list_form.elements['user_list_id[]'].length == null) {
      if (check_flag == true) {
        document.user_list_form.elements['user_list_id[]'].checked = true;
      } else {
        document.user_list_form.elements['user_list_id[]'].checked = false;
      }
    } else {
      for (var i = 0; i < document.user_list_form.elements['user_list_id[]'].length; i++) {
        if (!document.user_list_form.elements['user_list_id[]'][i].disabled) {
          if (check_flag == true) {
            document.user_list_form.elements['user_list_id[]'][i].checked = true;
          } else {
            document.user_list_form.elements['user_list_id[]'][i].checked = false;
          }
        }
      }
    }
  } 
}

<?php //创建用户?>
function create_user_info()
{
  $.ajax({
    url: 'ajax.php?action=new_user_info',     
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||');
      $('#show_popup_info').html(data_info_array[0]);
      $('#show_popup_info').css('z-index', data_info_array[1])
      
      if (first_new_single == true) {
        if ($('#show_popup_info').height() > box_warp_height) {
          $('.box_warp').height($('#show_popup_info').height()+$('.compatible_head').height());
        }
      }
      
      $('#show_popup_info').show();
      o_submit_single = true; 
    }
  });
}

<?php //关闭弹出页面?>
function close_user_info()
{
  first_new_single = true;
  $('#show_popup_info').html('');
  $('#show_popup_info').css('display', 'none');
  $('#show_popup_info').css('top', '');
  $('.box_warp').height('');
}

<?php //生成一次性密码?>
function user_preview_onetime_pwd()
{
  var letter_value = $('#letter').val();
  var rule_value = $('#user_rule').val();
  $.ajax({
    url: 'ajax_orders.php?action=generate_onetime_pwd',     
    type: 'POST',
    dataType: 'text',
    data:'letter_info='+letter_value+'&rule_info='+rule_value, 
    async:false,
    success: function (data) {
      $('#user_onetime').val(data); 
    }
  });
}

<?php //检查用户信息是否正确?>
function check_user_info(user_id, stype)
{
  var userid_info_str = $('#userid').val(); 
  var user_info_name = $('#name').val(); 
  var user_info_email = $('#user_email').val(); 
  var url_info_str = ''; 
  if ($('#user_password')) {
    var user_info_pwd = $('#user_password').val(); 
    url_info_str = 'user_info_id='+user_id+'&stype='+stype+'&userid_info_str='+userid_info_str+'&user_info_name='+user_info_name+'&user_info_pwd='+user_info_pwd+'&user_info_email='+user_info_email; 
  } else {
    url_info_str = 'user_info_id='+user_id+'&stype='+stype+'&userid_info_str='+userid_info_str+'&user_info_name='+user_info_name+'&user_info_email='+user_info_email; 
  }
  if ($('#user_rule')) {
    var user_rule_str = $('#user_rule').val(); 
    url_info_str += '&user_rule='+user_rule_str; 
  }
  $.ajax({
    url: 'ajax_orders.php?action=check_user_info',     
    type: 'POST',
    dataType: 'text',
    data: url_info_str, 
    async:false,
    success: function (data) {
      user_error_arr = data.split('|||');
      $('#userid_error').html(user_error_arr[0]); 
      $('#name_error').html(user_error_arr[1]); 
      $('#password_error').html(user_error_arr[2]); 
      $('#email_error').html(user_error_arr[3]); 
      $('#rule_error').html(user_error_arr[4]); 
      if (data == '||||||||||||') {
        <?php
        if ($ocertify->npermission > 15) {
        ?>
        document.forms.new_user_form.submit(); 
        <?php
        } else {
        ?>
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
              document.forms.new_user_form.submit(); 
            } else {
              $("#button_save").attr('id', 'tmp_button_save'); 
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.new_user_form.action),
                  async: false,
                  success: function(msg_info) {
                    document.forms.new_user_form.submit(); 
                  }
                }); 
              } else {
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
              }
            }
          }
        });
        <?php
        }
        ?>
      }
    }
  });
}

<?php //弹出页面?>
function show_user_info(ele, user_id, param_str)
{
  ele = ele.parentNode;
  first_new_single = false;
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_user_info',      
    data: 'user_e_id='+user_id+'&'+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (ele.offsetTop < $('#show_popup_info').height()) {
           offset = ele.offsetTop+$("#user_list_box").position().top+ele.offsetHeight;
           box_warp_height = offset;
         } else {
           if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#user_list_box").position().top-1)) {
             offset = ele.offsetTop+$("#user_list_box").position().top-1-$('#show_popup_info').height();
           } else {
             offset = ele.offsetTop+$("#user_list_box").position().top+$(ele).height();
             offset = offset + parseInt($('#user_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
           }
           box_warp_height = offset;
         }
       } else {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#user_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#user_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#user_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#user_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
      }
      $('#show_popup_info').css('top',offset);
      } else {
      if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#user_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#user_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#user_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#user_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
        box_warp_height = offset;
      } else {
        offset = ele.offsetTop+$("#user_list_box").position().top+ele.offsetHeight;
        box_warp_height = offset;
      }
      $('#show_popup_info').css('top',offset);
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      o_submit_single = true;
    }
  });
  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}
<?php //显示用户信息?>
function show_link_user_info(user_id, other_param)
{
  other_param = decodeURIComponent(other_param);
  $.ajax({
    url: 'ajax.php?action=edit_user_info',      
    data: 'user_e_id='+user_id+'&'+other_param,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
      o_submit_single = true;
    } 
  });
}
<?php //删除单个用户?>
function delete_fix_user(user_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      <?php
      if ($ocertify->npermission > 15) {
      ?>
      if (confirm('<?php echo TEXT_DEL_USER;?>')) {
        window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS;?>'+'?action=delete_user_confirm&user_e_id='+user_id+'&'+param_str;  
      }
      <?php
      } else {
      ?>
      if (confirm('<?php echo TEXT_DEL_USER;?>')) {
        if (tmp_msg_arr[0] == '0') {
          window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS;?>'+'?action=delete_user_confirm&user_e_id='+user_id+'&'+param_str;  
        } else {
          $("#button_save").attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS;?>'+'?action=delete_user_confirm&user_e_id='+user_id+'&'+param_str),
              async: false,
              success: function(msg_info) {
                window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS;?>'+'?action=delete_user_confirm&user_e_id='+user_id+'&'+param_str;  
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
          }
        }
      } 
      <?php
      }
      ?>
    }
  });
}
<?php //设置用户状态?>
function set_user_flag(current_uid, flag_num, o_param)
{
  o_param = decodeURIComponent(o_param);
  <?php
    if ($ocertify->npermission > 15) {
  ?>
    window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS.'?action=setflag';?>'+'&flag='+flag_num+'&user_e_id='+current_uid+'&'+o_param; 
  <?php
    } else {
  ?>
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
        window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS.'?action=setflag';?>'+'&flag='+flag_num+'&user_e_id='+current_uid+'&'+o_param; 
      } else {
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS.'?action=setflag';?>'+'&flag='+flag_num+'&user_e_id='+current_uid+'&'+o_param),
            async: false,
            success: function(msg_info) {
              window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_USERS.'?action=setflag';?>'+'&flag='+flag_num+'&user_e_id='+current_uid+'&'+o_param; 
            }
          }); 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
        }
      }
    }
  });
  <?php
    }
  ?>
}
$(function() {
  box_warp_height = $('.box_warp').height();    
});
<?php //监听事件?>
$(document).ready(function() {
  $(document).keyup(function(event) {
     if (event.which == 27) {
       if ($("#show_popup_info").css("display") != "none") {
         close_user_info();
         o_submit_single = true;
       }
     }
  
     if (event.which == 13) {
       if ($("#show_popup_info").css("display") != "none") {
         if (o_submit_single) {
           $("#button_save").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 37) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#user_prev")) {
           $("#user_prev").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 39) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#user_next")) {
           $("#user_next").trigger("click"); 
         }
       }
     }
  });    
});
</script>
<?php 
$belong = FILENAME_USERS;
require("includes/note_js.php");
?>
</head>
<?php
if (isset($_GET['eof']) && $_GET['eof'] == 'error') {
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message()">
<div id="popup_info">
<div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close"></div>
<span><?php echo TEXT_EOF_ERROR_MSG;?></span>
</div>
<div id="popup_box"></div>
<?php
} else {
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php
}
?>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
</script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation -->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof -->
      </table>
    </td>
    <!-- body_text -->
    <td width="100%" valign="top">
      <div class="box_warp">
      <?php echo $notes;?>
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="100%" height="40">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">
                <?php echo HEADING_TITLE;?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <?php
              $site_list_array = array(); 
              $show_site_list_array = array(); 
              $site_list_info_query = tep_db_query("select * from ".TABLE_SITES);    
               
              while ($site_list_info = tep_db_fetch_array($site_list_info_query)) {
                $site_list_array[$site_list_info['id']] = $site_list_info['romaji']; 
                $show_site_list_array[] = $site_list_info['id']; 
              }
              echo tep_show_site_filter(FILENAME_USERS, false, $show_site_list_array); 
            ?>
            <div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div> 
            <div id="toggle_width" style="min-width:726px;"></div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                <?php
                  $user_table_name_str = ''; 
                  $user_table_id_str = ''; 
                  $user_table_permission_str = ''; 
                  $user_table_site_permission_str = ''; 
                  $user_table_status_str = ''; 
                  $user_table_operate_str = ''; 
                  $user_order_sort_name = ' u.name';
                  $user_order_sort = 'asc';
                  if (isset($_GET['user_sort'])) {
                    if ($_GET['user_sort_type'] == 'asc') {
                      $type_str = '<font color="#facb9c">'.TEXT_SORT_ASC.'</font>'.'<font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>'; 
                      $tmp_type_str = 'desc'; 
                    } else {
                      $type_str = '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font>'.'<font color="#facb9c">'.TEXT_SORT_DESC.'</font>'; 
                      $tmp_type_str = 'asc'; 
                    }
                    switch ($_GET['user_sort']) {
                       case 'user_name':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type='.$tmp_type_str).'">'.TABLE_USER_INFO_NAME.$type_str.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                         $user_order_sort_name = ' u.name';
                         break;
                       case 'user_id':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type='.$tmp_type_str).'">ID'.$type_str.'</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                         $user_order_sort_name = ' u.userid';
                         break;
                       case 'user_permission':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type='.$tmp_type_str).'">'.TABLE_USER_INFO_PERMISSION.$type_str.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                         $user_order_sort_name = ' p.permission';
                         break;
                       case 'user_site_permission':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type='.$tmp_type_str).'">'.TABLE_USER_INFO_SITE_PERMISSION.$type_str.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                          $user_order_sort_name = ' p.site_permission';
                         break;
                       case 'user_status':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type='.$tmp_type_str).'">'.TABLE_USER_INFO_STATUS.$type_str.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                         $user_order_sort_name = ' u.status';
                         break;
                       case 'user_update':
                         $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                         $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                         $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                         $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                         $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                         $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type='.$tmp_type_str).'">'.TABLE_USER_INFO_ACTION.$type_str.'</a>'; 
                         $user_order_sort_name = ' u.date_update';
                         break;
                    }
                  }
                  if (isset($_GET['user_sort_type'])) {
                    if ($_GET['user_sort_type'] == 'asc') {
                      $user_order_sort = 'asc';
                    } else {
                      $user_order_sort = 'desc';
                    }
                  }
                  $user_order_sql = $user_order_sort_name.' '.$user_order_sort;

                  $user_info_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => 'id="user_list_box"'); 
                  $notice_box = new notice_box('', '', $user_info_params);  
                  
                  $user_table_title_row = array();
                  $user_table_info_row = array();
                  
                  if (!isset($_GET['user_sort_type'])) {
                    $user_table_name_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_name&user_sort_type=desc').'">'.TABLE_USER_INFO_NAME.'</a>'; 
                    $user_table_id_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_id&user_sort_type=desc').'">ID</a>'; 
                    $user_table_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_PERMISSION.'</a>'; 
                    $user_table_site_permission_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_site_permission&user_sort_type=desc').'">'.TABLE_USER_INFO_SITE_PERMISSION.'</a>'; 
                    $user_table_status_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_status&user_sort_type=desc').'">'.TABLE_USER_INFO_STATUS.'</a>'; 
                    $user_table_operate_str = '<a href="'.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id', 'user_sort', 'user_sort_type')).'user_sort=user_update&user_sort_type=desc').'">'.TABLE_USER_INFO_ACTION.'</a>'; 
                  }
                  $user_table_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_user_select" onclick="select_all_user();">');
                  $user_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_name_str);
                  $user_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_id_str);
                  $user_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_permission_str);
                  $user_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_site_permission_str);
                  $user_table_title_row[] = array('align' => 'center','params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_status_str);
                  $user_table_title_row[] = array('align' => 'right','params' => 'class="dataTableHeadingContent_order"', 'text' => $user_table_operate_str);
               
                  $user_table_info_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $user_table_title_row);

                  $user_list_query_raw = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and p.permission <= '" . $ocertify->npermission . "' order by ".$user_order_sql; 
                  $user_list_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $user_list_query_raw, $user_list_query_numrows);
                  $user_list_query = tep_db_query($user_list_query_raw); 
                  
                  
                  while ($user_list_info = tep_db_fetch_array($user_list_query)) {
                    $user_list_row = array();
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if (isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even; 
                    } else {
                      $nowColor = $odd; 
                    }
                    if ($_GET['user_info_id'] == $user_list_info['userid']) {
                      $user_list_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
                    } else {
                      $user_list_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                    }
                    
                    $user_list_row[] = array(
                        'params' => 'class="dataTableContent"', 
                        'text' => '<input type="checkbox" name="user_list_id[]" value="'.$user_list_info['userid'].'"'.(($is_u_disabled)?' disabled="disabled"':($user_list_info['permission'] == '31')?'disabled="disabled"':'').'>' 
                        ); 
                    $user_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id')).'user_info_id='.$user_list_info['userid']).'\'"', 
                        'text' => $user_list_info['name'] 
                        ); 
                    
                    $user_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id')).'user_info_id='.$user_list_info['userid']).'\'"', 
                        'text' => $user_list_info['userid'] 
                        ); 
                    
                    $user_permission_str = ''; 
                    $user_permission_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$user_list_info['userid']."'");
                    $user_permission_info = tep_db_fetch_array($user_permission_query); 
                    switch ($user_permission_info['permission']) {
                      case '15':
                        $user_permission_str = 'Admin';    
                        break;
                      case '10':
                        $user_permission_str = 'Chief';    
                        break;
                      case '7':
                        $user_permission_str = 'Staff';    
                        break;
                      case '31':
                        $user_permission_str = 'Root';    
                        break;
                    }
                    $user_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id')).'user_info_id='.$user_list_info['userid']).'\'"', 
                        'text' => $user_permission_str 
                        ); 
                    
                    $user_site_permission_str = '';
                    $site_permission_array = explode(',', $user_permission_info['site_permission']); 
                    $show_user_site = array(); 
                    
                    if (!empty($site_permission_array)) {
                      foreach ($site_permission_array as $s_key => $s_value) {
                         if ($s_value != '0') {
                           if (isset($site_list_array[$s_value])) {
                             $show_user_site[] = $site_list_array[$s_value]; 
                           }
                         } else {
                           $show_user_site[] = 'all'; 
                         }
                      }
                    }
                    if (!empty($show_user_site)) {
                      $user_site_permission_str = implode(',', $show_user_site);
                    }
                    $user_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_USERS, tep_get_all_get_params(array('action', 'user_info_id', 'site_id')).'user_info_id='.$user_list_info['userid']).'\'"', 
                        'text' => $user_site_permission_str 
                        ); 
                    
                    $user_status_str = ''; 
                    
                    if ($user_list_info['status'] == '1') {
                      if ($is_u_disabled || ($user_list_info['permission'] == '31')) {
                        $user_status_str = tep_image(DIR_WS_IMAGES.'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN).'&nbsp;&nbsp'.tep_image(DIR_WS_IMAGES.'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT); 
                      } else {
                        $user_status_str = tep_image(DIR_WS_IMAGES.'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN).'&nbsp;&nbsp<a href="javascript:void(0);" onclick="set_user_flag(\''.$user_list_info['userid'].'\', \'0\', \''.urlencode(tep_get_all_get_params(array('action', 'flag', 'site_id'))).'\')">'.tep_image(DIR_WS_IMAGES.'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT).'</a>'; 
                      }
                    } else {
                      if ($is_u_disabled || ($user_list_info['permission'] == '31')) {
                        $user_status_str = tep_image(DIR_WS_IMAGES.'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT).'&nbsp;&nbsp;'.tep_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED); 
                      } else {
                        $user_status_str = '<a href="javascript:void(0);" onclick="set_user_flag(\''.$user_list_info['userid'].'\', \'1\', \''.urlencode(tep_get_all_get_params(array('action', 'flag', 'site_id'))).'\')">'.tep_image(DIR_WS_IMAGES.'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT).'</a>&nbsp;&nbsp;'.tep_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED); 
                      }
                    }
                    $user_list_row[] = array(
                        'align' => 'center', 
                        'params' => 'class="dataTableContent"', 
                        'text' => $user_status_str 
                        ); 
                   
                    $user_date_info = (tep_not_null($user_list_info['date_update']) && ($user_list_info['date_update'] != '0000-00-00 00:00:00'))?$user_list_info['date_update']:$user_list_info['date_added'];
                    $user_list_row[] = array(
                        'align' => 'right', 
                        'params' => 'class="dataTableContent"', 
                        'text' => '<a href="javascript:void(0);" onclick="show_user_info(this, \''.$user_list_info['userid'].'\', \''.urlencode(tep_get_all_get_params(array('action', 'site_id'))).'\');">'.tep_get_signal_pic_info($user_date_info).'</a>' 
                        ); 
                    $user_table_info_row[] = array('params' => $user_list_params, 'text' => $user_list_row);
                  }
                 
                  $form_str = tep_draw_form('user_list_form', FILENAME_USERS, tep_get_all_get_params(array('user_info_id', 'action', 'site_id')).'action=delete_select_user');
                  $notice_box->get_form($form_str); 
                  $notice_box->get_contents($user_table_info_row);
                  $notice_box->get_eof(tep_eof_hidden()); 
                  echo $notice_box->show_notice();
                ?>
                </td>
              </tr>
              <tr>
                <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                    <?php
                      if ($ocertify->npermission >= 15) {
                    ?>
                    <tr>
                      <td colspan="2">
                      <select name="user_list_action" onchange="user_change_action(this.value, 'user_list_id[]')">
                        <option value="0"><?php echo USER_LIST_SELECT_ACTION;?></option> 
                        <option value="1"><?php echo USER_LIST_DELETE_ACTION;?></option> 
                      </select>
                      </td>
                    </tr>
                    <?php
                      } 
                    ?>
                    <tr>
                      <td class="smallText" valign="top">
                      <?php 
                        echo $user_list_split->display_count($user_list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_USER_LIST); 
                      ?>
                      </td>
                      <td class="smallText" align="right">
                      <div class="td_box">
                      <?php 
                        echo $user_list_split->display_links($user_list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'user_info_id', 'site_id'))); 
                      ?>
                      </div>
                      </td>
                    </tr> 
                    <tr>
                      <td class="smallText" valign="top">&nbsp;</td>
                      <td align="right" class="smallText">
                      <?php
                        if ($is_u_disabled) {
                          echo '<a href="javascript:void(0);">'.tep_html_element_button(NEW_USER_BUTTON_TEXT, 'disabled="disabled"').'</a>'; 
                        } else {
                          echo '<a href="javascript:void(0);" onclick="create_user_info();">'.tep_html_element_button(NEW_USER_BUTTON_TEXT).'</a>'; 
                        }
                      ?>
                      </td>
                    </tr>
                  </table> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      </div>
      </div>
      <!-- body_text_eof -->
    </td>
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
