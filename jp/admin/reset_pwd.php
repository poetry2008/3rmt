<?php
require('includes/application_top.php');
if ($_GET['action'] == 'check_search') {
/*-------------------------------------
 功能: 检查搜索 
 参数：$_POST['start'](string) 开始检查
 参数：$_POST['end'](string) 结束检查
 ------------------------------------*/
  $error_msg = ''; 
  if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['start']) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['end'])) {
    $error_msg = SELECT_SEARCH_DATE_IS_WRONG; 
  } else {
    $start_array = explode('-', $_POST['start']); 
    $end_array = explode('-', $_POST['end']); 
    
    if (!checkdate($start_array[1], $start_array[2], $start_array[0]) || !checkdate($end_array[1], $end_array[2], $end_array[0])) {
      $error_msg = SELECT_SEARCH_DATE_IS_WRONG; 
    } else {
      $start_time = mktime(0, 0, 0, $start_array[1], $start_array[2], $start_array[0]); 
      $end_time = mktime(0, 0, 0, $end_array[1], $end_array[2], $end_array[0]); 
      if ($start_time >= $end_time) {
        $error_msg = SELECT_SEARCH_START_DATE_IS_LARGE; 
      }
    }
  }
  echo $error_msg; 
  exit;
} else if ($_GET['action'] == 'reset_all') {
  tep_db_query("update `".TABLE_CUSTOMERS."` set `reset_flag` = '0', `reset_success` = '0'"); 
  tep_db_query("update `".TABLE_OCONFIG."` set `value` = '' where `keyword` = 'reset_pwd_startdate'");  
  tep_db_query("update `".TABLE_OCONFIG."` set `value` = '' where `keyword` = 'reset_pwd_enddate'");  
  exit;
}
if($_SERVER['REQUEST_METHOD']=='POST'){
  if($_GET['type'] =='saveRange'){
    //检查是否合法后更新数据库，并把所有用户reset_flag设置成0 ,范围内的用户的 reset_flag设置成1 

    $reset_pwd_startdate = trim($_POST['start']);
    $reset_pwd_enddate   = trim($_POST['end']);
    tep_db_query('update  '.TABLE_OCONFIG.' set value = "'.
        $reset_pwd_startdate.'",user_update = "'.$_SESSION['user_name'].'",date_update = "'.date('Y-m-d H:i:s',time()).'" where  keyword = "reset_pwd_startdate"');
    tep_db_query('update  '.TABLE_OCONFIG.' set value = "'.$reset_pwd_enddate.'",user_update = "'.$_SESSION['user_name'].'",date_update = "'.date('Y-m-d H:i:s',time()).'" where  keyword = "reset_pwd_enddate"');

    $reset_pwd_startdate.=' 00:00:00';
    $reset_pwd_enddate.=' 00:00:00';

    tep_db_query('update  '.TABLE_CUSTOMERS_INFO .' ci ,'.TABLE_CUSTOMERS.' c  set c.reset_flag = 1 where ci.customers_info_date_account_created >
        "'.$reset_pwd_startdate. '" and c.customers_id = ci.customers_Info_Id and ci.customers_info_date_account_created < "'.$reset_pwd_enddate. '"' );

  }
  if($_GET['type'] == 'saveMsg') {
    //检查合法后 更新数据库即可
    $reset_pwd_title = $_POST['title'];
    $reset_pwd_content   = $_POST['content'];
    tep_db_query('update  '.TABLE_OCONFIG.' set value = "'.$reset_pwd_title.'",user_update = "'.$_SESSION['user_name'].'",date_update = "'.date('Y-m-d H:i:s',time()).'"  where  keyword = "reset_pwd_title"');
    tep_db_query('update  '.TABLE_OCONFIG.' set value = "'.$reset_pwd_content.'",user_update = "'.$_SESSION['user_name'].'",date_update = "'.date('Y-m-d H:i:s',time()).'"where  keyword = "reset_pwd_content"');
  }

}
require('includes/action/reset_pwd.php');


if (isset($_GET['action']) and $_GET['action']) {
  switch ($_GET['action']) {
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_RESET_PWD_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script>
<?php //打开日历 ?>
function open_new_calendar(c_type)
{
  var is_open = $('#toggle_open_'+c_type).val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open_'+c_type).val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar_"+c_type,
            width:'170px',

        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        if (c_type == 'start') {
          $("#start").val(dtdate.format(newDate)); 
        } else {
          $("#end").val(dtdate.format(newDate)); 
        }
        $('#toggle_open_'+c_type).val('0');
        $('#toggle_open_'+c_type).next().html('<div id="mycalendar_'+c_type+'"></div>');
      });
    });
  }
}
<?php //选择完日历 确认查询 ?>
function check_search_form()
{
  start_str = document.getElementById('start').value; 
  end_str = document.getElementById('end').value; 
  
  $.ajax({
    url: 'reset_pwd.php?action=check_search', 
    type:'POST',  
    data:'start='+start_str+'&end='+end_str,
    async:false,
    success: function(msg) {
      if (msg != '') {
        alert(msg); 
      } else {
        document.forms.search.submit(); 
      }
    }
  });
}
<?php //重置日历  ?>
function reset_customers_pwd(c_permission) {
  $.ajax({
    url: 'reset_pwd.php?action=reset_all',
    type: 'POST',
    async:false,
    success: function(msg) {
      if (c_permission == 31) {
        window.location.href = window.location.href; 
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
              window.location.href = window.location.href; 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link('reset_pwd.php');?>'),
                  async: false,
                  success: function(msg_info) {
                    window.location.href = window.location.href; 
                  }
                }); 
              } else {
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              }
            }
          }
        });
      }
    }
  });
}
<?php //提交表单?>
function check_reset_pwd_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.rp_form.submit(); 
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
          document.forms.rp_form.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.rp_form.action),
              async: false,
              success: function(msg_info) {
                document.forms.rp_form.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      }
    });
  }
}
</script>
<style type="text/css">
.yui3-y{
	position:absolute;
}
</style>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');

  </script>
    <?php }?>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
    <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
    <!-- left_navigation //-->
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    <!-- left_navigation_eof //-->
    </table></td>
    <!-- body_text //-->

    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
    <div class="compatible">
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr><td height="40" class="pageHeading"><?php echo TEXT_RESET_PWD_TITLE;?></td></tr>
    <tr>
    <td>
    <table border="0" cellpadding="2" cellspacing="0" width="100%"><tr><td>
    <form action='reset_pwd.php?type=saveRange' method='post' name="search">
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr><td colspan="4"><?php echo TEXT_RESET_PWD_INFO;?>
    </tr>
    <tr>
    <td width="120" nowrap><?php 
    echo TEXT_RESET_PWD_CUSTOMER_SET;?></td>
    <td width="260">
    <div class="yui3-skin-sam"> 
    <?php echo TEXT_RESET_PWD_START;?>&nbsp;&nbsp;<input type='text' id="start" name='start' value='<?php echo $reset_pwd_startdate;?>'/><a href="javascript:void(0);" onclick="open_new_calendar('start');"><img class='pcal' src="includes/calendar.png"></a> 
    <input type="hidden" name="toggle_open_start" value="0" id="toggle_open_start"> 
    <div class="yui3-y" id="new_yui3">
    <div id="mycalendar_start"></div> 
    </div>
    </div> 
    </td>
    <td width="260">
    <div class="yui3-skin-sam"> 
    <?php echo TEXT_RESET_PWD_END;?>&nbsp;&nbsp;<input type='text' id="end" name='end' value='<?php echo $reset_pwd_enddate;?>'/><a href="javascript:void(0);" onclick="open_new_calendar('end');"><img class='pcal' src="includes/calendar.png"></a>
    <input type="hidden" name="toggle_open_end" value="0" id="toggle_open_end"> 
    <div class="yui3-y" id="new_yui3">
    <div id="mycalendar_end"></div> 
    </div>
    </div> 
    </td>
    <td><input type='button' value="<?php echo TEXT_RESET_PWD_OK;?>" onclick="check_search_form();"></td>
    </tr>
    </table>
    </form>
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td>
        <?php
        if ($ocertify->npermission >= 15) { 
        ?>
        <input type="button" value="<?php echo RESET_BUTTON_TEXT;?>" onclick="reset_customers_pwd('<?php echo $ocertify->npermission;?>');">
        <?php
        }
        ?>   
      </td>
    </tr>
    </table> 
    <form action='reset_pwd.php?type=saveMsg' method='post' name="rp_form">
    <table border="0" cellpadding="2" cellspacing="0">
    <tr>
    <td colspan="2" class="pageHeading" height="40"><?php 
    echo TEXT_RESET_PWD_NEXT_TELL;?></td>
    </tr>
    <tr>
    <td width="120" nowrap><?php echo TEXT_RESET_PWD_LIST_TITLE;?></td> 
    <td> <input type='text' name='title' style="width:100%;" value='<?php echo $reset_pwd_title;?>'/></td>
    </tr>
    <tr>
    <td valign="top"><?php echo TEXT_RESET_PWD_LIST_CONTENT;?></td>
    <td><textarea name='content' rows="20" style="width:100%; margin-bottom:5px;" /><?php echo $reset_pwd_content;?></textarea>
    <br><?php echo TEXT_RESET_PWD_OTHER_TEXT;?></td>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td align="right">
    <a href="javascript:void(0);"><?php echo tep_html_element_button(TEXT_RESET_PWD_SAVE, 'onclick="check_reset_pwd_form(\''.$ocertify->npermission.'\');"');?></a>
    </td>
    </tr>
       </table>
    </form>
    <table border="0" cellpadding="2" cellpadding="0" width="100%">
    <tr>
    <td class="pageHeading" height="40"><?php echo TEXT_RESET_PWD_CHANGE;?></td>
    </tr>
    <tr>
    <td>
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent"><?php echo TEXT_RESET_PWD_SITE;?></td>
    <td class="dataTableHeadingContent"><?php echo TEXT_RESET_PWD_ALL_CUSTOMER;?></td>
    <td class="dataTableHeadingContent"><?php echo TEXT_RESET_PWD_CHANGE_RELY;?></td>
    <td class="dataTableHeadingContent"><?php echo TEXT_RESET_PWD_CHANGE_OVER;?></td>
    </tr>
    <tr class="dataTableSecondRow">
    <td><?php echo TEXT_RESET_PWD_ALL;?></td>
    <td><?php echo $all_member_count.NUM_UNIT_TEXT;?></td>
    <td><?php echo $need_reset_member_count.NUM_UNIT_TEXT;?></td>
    <td><?php echo $reset_done.NUM_UNIT_TEXT;?></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    </td></td></table>

    </td></tr>
     <?php
        if($_GET['type'] == 'saveRange'){
          $config_keyword = 'reset_pwd_startdate';  
        }else if($_GET['type'] == 'saveMsg'){
          $config_keyword = 'reset_pwd_title'; 
        }
       echo '<tr><td style="padding-top:10px;">'.TEXT_USER_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
       echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
       $config = tep_db_fetch_array(tep_db_query("select * from ".TABLE_OCONFIG." where `keyword` = '".$config_keyword."' ")); 
       if(tep_not_null($config['user_update'])){
       echo '<tr><td >'.TEXT_USER_UPDATE.'&nbsp;'.$config['user_update'].'</td></tr>';
       }else{
       echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
       }if(tep_not_null(tep_datetime_short($config['date_update']))){
       echo ' <tr><td style="padding-bottom:10px;">'.TEXT_DATE_UPDATE.'&nbsp;'.$config['date_update'].'</td></tr>';    
       }else{
       echo ' <tr><td style="padding-bottom:10px;">'.TEXT_DATE_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';    
       }
    ?>

    </table>
    </div>
    </div>
    </td>

    <!-- body_text_eof //-->
    </tr>
    </table>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <br>
    </body>
    </html>
    <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
