<?php
  /**
   * $Id$
   *
   * 备忘录管理
   */
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'insert' 新建memo     
   case 'save' 更新memo     
   case 'deleteconfirm' 删除memo      
   case 'delete' 删除选中的memo
------------------------------------------------------*/
      case 'insert':
        $from = tep_db_prepare_input($_POST['from']);
        $users_list = tep_db_prepare_input($_POST['users_id_select']);
        $users_id = tep_db_prepare_input($_POST['users_id']);
        $users_id_array = array();
        $users_id_str = '';
        if($users_list == '1'){

          $users_id_array = array_unique(array_filter($users_id));
          $users_id_str = implode(',',$users_id_array);
        }

        $is_show = tep_db_prepare_input($_POST['is_show']);
        $pic_icon = tep_db_prepare_input($_POST['pic_icon']);
        $contents = tep_db_prepare_input($_POST['contents']);

        $sql_data_array = array(
           '`from`' => $from,
           '`to`' => $users_id_str, 
           'is_show' => $is_show,
           'icon' => $pic_icon,
           'contents' => $contents,
           'user_added' => $_SESSION['user_name'],
           'date_added'=> 'now()'
           );  
        tep_db_perform(TABLE_BUSINESS_MEMO, $sql_data_array);

        $memo_id = tep_db_insert_id();
           
        $sql_data_array = array(
           'type' => 1,
           'title' => $contents,
           'set_time' => 'now()',
           'from_notice' => $memo_id,
           'user' => $_SESSION['user_name'],
           'created_at' => 'now()'
          ); 
        tep_db_perform(TABLE_NOTICE, $sql_data_array);
  
        tep_redirect(tep_href_link(FILENAME_BUSINESS_MEMO));
        break;
      case 'save':
        $is_show = tep_db_prepare_input($_POST['is_show']);
        $pic_icon = tep_db_prepare_input($_POST['pic_icon']);
        $contents = tep_db_prepare_input($_POST['contents']); 
        $memo_id = tep_db_prepare_input($_POST['memo_id']);
        $param_str = tep_db_prepare_input($_POST['param_str']);

        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set read_flag = '', is_show='".$is_show."',icon='".$pic_icon."',contents='".$contents."',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($memo_id) . "'");

        tep_db_query("update " . TABLE_NOTICE . " set title='".$contents."' where from_notice = '" . tep_db_input($memo_id) . "' and type='1'");
        tep_redirect(tep_href_link(FILENAME_BUSINESS_MEMO, $param_str));
        break;
      case 'deleteconfirm':
        $memo_id = tep_db_prepare_input($_POST['memo_id']);
        $param_str = $_POST['param_str'];
        tep_db_query("update " . TABLE_NOTICE . " set is_show='0' where from_notice = '" . tep_db_input($memo_id) . "' and type='1'");
        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set deleted='1' where id = '" . tep_db_input($memo_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BUSINESS_MEMO, $param_str));
        break;
      case 'delete':
        $memo_id_list = tep_db_prepare_input($_POST['memo_list_id']);
        $param_str = $_GET['page'];

        foreach($memo_id_list as $memo_id){
          tep_db_query("update " . TABLE_NOTICE . " set is_show='0' where from_notice = '" . tep_db_input($memo_id) . "' and type='1'");
          tep_db_query("update " . TABLE_BUSINESS_MEMO . " set deleted='1' where id = '" . tep_db_input($memo_id) . "'"); 
        }
        tep_redirect(tep_href_link(FILENAME_BUSINESS_MEMO, 'page='.$param_str));
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
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
<?php //快捷键监听?>
$(document).ready(function() { 
var box_warp_height = $(".box_warp").height();
  $(document).keyup(function(event) {
    if (event.which == 27) {
      if ($("#show_popup_info").css("display") != "none") {
        hidden_info_box();     
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
        if ($("#memo_prev")) {
          $("#memo_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#memo_next")) {
          $("#memo_next").trigger("click");
        }
      }
    }
  });    
});
var origin_offset_symbol = 0;
window.onresize = resize_option_page;
var o_submit_single = true;
<?php //窗口缩放事件?>
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
  box_warp_height = $(".box_warp").height(); 
}

<?php //删除动作?>
function select_memo_change(value,memo_list_id,c_permission)
{
  sel_num = 0;
  if (document.edit_memo_form.elements[memo_list_id].length == null) {
    if (document.edit_memo_form.elements[memo_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_memo_form.elements[memo_list_id].length; i++) {
      if (document.edit_memo_form.elements[memo_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm('<?php echo TEXT_MEMO_EDIT_CONFIRM;?>')) {
      if (c_permission == 31) {
        document.edit_memo_form.action = "<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
        document.edit_memo_form.submit(); 
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
              document.edit_memo_form.action = "<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
              document.edit_memo_form.submit(); 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>'),
                  async: false,
                  success: function(msg_info) {
                    document.edit_memo_form.action = "<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
                    document.edit_memo_form.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("edit_memo_list")[0].value = 0;
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              }
            }
          }
        });
      }
    }else{

      document.getElementsByName("edit_memo_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_memo_list")[0].value = 0;
    alert("<?php echo TEXT_MEMO_EDIT_MUST_SELECT;?>"); 
  }
}

<?php //全选动作?>
function all_select_memo(memo_list_id)
{
  var check_flag = document.edit_memo_form.all_check.checked;
  if (document.edit_memo_form.elements[memo_list_id]) {
    if (document.edit_memo_form.elements[memo_list_id].length == null) {
      if (check_flag == true) {
        document.edit_memo_form.elements[memo_list_id].checked = true;
      } else {
        document.edit_memo_form.elements[memo_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_memo_form.elements[memo_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_memo_form.elements[memo_list_id][i].checked = true;
        } else {
          document.edit_memo_form.elements[memo_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //选中，取消单选按钮?>
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

<?php //追加个别或所有用户?>
function setting_users(value){

  var users_list = document.getElementsByName("users_id[]");
  var users_list_length = users_list.length;

  if(value == 0){ 
    for(var i=0;i<=users_list_length-1;i++){
    
      document.getElementsByName("users_id[]")[i].disabled = true;
    }
    $("#add_users").attr('disabled',true);
  }else{
    for(var i=0;i<=users_list_length-1;i++){
    
      document.getElementsByName("users_id[]")[i].disabled = false;
    } 
    $("#add_users").attr('disabled',false);
  }
}

<?php //追加选项?>
function add_users_select(ele){

  var users_list = $("#users_list").html();
  var html_str = '';
  html_str += '<tr>';
  html_str += '<td align="left" width="25%" nowrap="nowrap">&nbsp;</td>';
  html_str += '<td align="left" nowrap="nowrap">'+users_list+'</td>';
  html_str += '<td align="left" nowrap="nowrap">&nbsp;</td>'; 
  html_str += '</tr>';
  $(ele).parent().parent().parent().before(html_str);
}

<?php //等待元素隐藏?> 
function read_time(){
    
  $("#wait").hide();
}

<?php //给memo加标识?> 
function change_read(id,user){
  var memo_id = document.getElementById("memo_"+id); 
  var memo_id_src = memo_id.src;
  var memo_id_src_array = new Array();
  var flag = 0;
  memo_id_src_array = memo_id_src.split("/"); 
  if(memo_id_src_array[memo_id_src_array.length-1] == 'green_right.gif'){

    flag = 1;
  }
  $.ajax({
         type: "POST",
         data: 'id='+id+'&user='+user+'&flag='+flag,
         beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
         async:false,
         url: 'ajax.php?action=read_flag',
         success: function(msg) {
         if(flag == 0){
             memo_id.src="images/icons/green_right.gif";
             memo_id.title=" <?php echo TEXT_FLAG_CHECKED;?> ";
             memo_id.alt="<?php echo TEXT_FLAG_CHECKED;?>";
         }else{
             memo_id.src="images/icons/gray_right.gif";
             memo_id.title=" <?php echo TEXT_FLAG_UNCHECK;?> ";
             memo_id.alt="<?php echo TEXT_FLAG_UNCHECK;?>";
         }
         $('body').css('cursor','');
         setTimeout('read_time()',500);
         }
  }); 
}
<?php //编辑memo信息?>
function show_memo_info(ele, memo_id, i_param_str)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_memo',      
    data: 'memo_id='+memo_id+'&param_str='+i_param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#memo_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#memo_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#memo_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#memo_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#memo_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
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

<?php //编辑memo的上一个，下一个信息?>
function show_link_memo_info(memo_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_memo',      
    data: 'memo_id='+memo_id+'&param_str='+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}

<?php //隐藏弹出页面?>
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}

<?php //memo内容添加?>
function create_memo_check(c_permission){
  if (c_permission == 31) {
    document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=insert');?>';
    document.create_memo_form.submit();
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
          document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=insert');?>';
          document.create_memo_form.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=insert');?>'),
              async: false,
              success: function(msg_info) {
                document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=insert');?>';
                document.create_memo_form.submit();
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

<?php //memo内容编辑?>
function edit_memo_check(c_permission){
  if (c_permission == 31) {
    document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=save');?>';
    document.edit_memo.submit();
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
          document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=save');?>';
          document.edit_memo.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=save');?>'),
              async: false,
              success: function(msg_info) {
                document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=save');?>';
                document.edit_memo.submit();
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

<?php //删除memo?>
function close_memo(c_permission){
  if (c_permission == 31) {
    document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=deleteconfirm');?>';
    document.edit_memo.submit();
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
          document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=deleteconfirm');?>';
          document.edit_memo.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=deleteconfirm');?>'),
              async: false,
              success: function(msg_info) {
                document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BUSINESS_MEMO, 'action=deleteconfirm');?>';
                document.edit_memo.submit();
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

<?php //新建memo?>
function create_memo(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax.php?action=create_memo',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
      setting_users(0);
    }
  }); 
}
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
<?php
  $site_query = tep_db_query("select id from ".TABLE_SITES);
  $site_list_array = array();
  while($site_array = tep_db_fetch_array($site_query)){

    $site_list_array[] = $site_array['id'];
  }
  tep_db_free_result($site_query);
  echo tep_show_site_filter(FILENAME_BUSINESS_MEMO,false,$site_list_array);
?>
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="memo_list_box">
          <tr>
            <td valign="top">
<?php 
  echo tep_draw_form('edit_memo_form',FILENAME_BUSINESS_MEMO, '', 'post');
  $memo_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => ''); 
  $notice_box = new notice_box('', '', $memo_table_params); 
  $memo_table_row = array();
  $memo_title_row = array();
                  
  //memo列表  
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_memo(\'memo_list_id[]\');" name="all_check">');
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_ICON);
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_READ_FLAG);
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_FROM);
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_TO); 
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_CONTENTS);
  $memo_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TEXT_MEMO_CREATE_TIME);
  $memo_title_row[] = array('align' => 'left','params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => TABLE_HEADING_ACTION);
                    
  $memo_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $memo_title_row);   

  //获取图标信息
  $icon_list_array = array();
  $icon_query = tep_db_query("select id,pic_name,pic_alt from ". TABLE_CUSTOMERS_PIC_LIST);
  while($icon_array = tep_db_fetch_array($icon_query)){

    $icon_list_array[$icon_array['id']] = array('name'=>$icon_array['pic_name'],'alt'=>$icon_array['pic_alt']);
  }
  tep_db_free_result($icon_query);

  $memo_query_raw = "select * from " . TABLE_BUSINESS_MEMO . " where deleted='0' order by date_added desc";
  $memo_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $memo_query_raw, $memo_query_numrows);
  $memo_query = tep_db_query($memo_query_raw);
  if(tep_db_num_rows($memo_query) == 0){
    $memo_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
    $memo_table_row[] = array('params' => '', 'text' => $memo_data_row);  
  }
  while ($memo = tep_db_fetch_array($memo_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $memo['id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($memo);
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($memo['id'] == $cInfo->id) ) {
      $memo_item_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $memo_item_params = '<tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $memo_item_info = array();  
    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" value="'.$memo['id'].'" name="memo_list_id[]">'   
                          );
    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUSINESS_MEMO, 'page=' . $_GET['page'] . '&cID=' . $memo['id']) . '\'"', 
                          'text' => $memo['icon'] != 0 ? tep_image(DIR_WS_IMAGES.'icon_list/'.$icon_list_array[$memo['icon']]['name'],$icon_list_array[$memo['icon']]['alt']) : ''  
                          ); 

    $read_flag_str_array = array();
    $read_flag_str_array = explode(',',$memo['read_flag']);
    if($memo['read_flag'] == ''){
      $memo_read = '<a onclick="change_read(\''.$memo['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$memo['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>'; 
    }else{

      if(in_array($ocertify->auth_user,$read_flag_str_array)){

        $memo_read = '<a onclick="change_read(\''.$memo['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$memo['id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif"></a>';
      }else{

        $memo_read = '<a onclick="change_read(\''.$memo['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$memo['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>';
      }
    }    
    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $memo_read 
                        );

    $users_info = tep_get_user_info($memo['from']);
    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUSINESS_MEMO, 'page=' . $_GET['page'] . '&cID=' . $memo['id']) . '\'"', 
                          'text' => $users_info['name'] 
                        );

    $to_users_array = explode(',',$memo['to']);
    $to_users_temp_array = array();
    foreach($to_users_array as $to_value){

      $to_users_info = tep_get_user_info($to_value);
      $to_users_temp_array[] = $to_users_info['name'];
    }
    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUSINESS_MEMO, 'page=' . $_GET['page'] . '&cID=' . $memo['id']) . '\'"', 
                          'text' => $memo['to'] != '' ? mb_strlen($memo['to'],'utf-8') > 30 ? mb_substr(implode('；',$to_users_temp_array),0,30,'utf-8').'...' : implode('；',$to_users_temp_array) : 'ALL' 
                        );

    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUSINESS_MEMO, 'page=' . $_GET['page'] . '&cID=' . $memo['id']) . '\'"', 
                          'text' => nl2br($memo['contents'])
                        );

    $memo_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUSINESS_MEMO, 'page=' . $_GET['page'] . '&cID=' . $memo['id']) . '\'"', 
                          'text' => $memo['date_added'] 
                        );

    $memo_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="javascript:void(0);" onclick="show_memo_info(this, \''.$memo['id'].'\', \'page='.$_GET['page'].'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($memo['date_update'] != '' && $memo['date_update'] != '0000-00-00 00:00:00' ? $memo['date_update'] : $memo['date_added'])))).'</a>' 
                          ); 
                      
    $memo_table_row[] = array('params' => $memo_item_params, 'text' => $memo_item_info);

  }

  $form_str = tep_draw_form('memo_list', FILENAME_BUSINESS_MEMO, tep_get_all_get_params(array('action')).'action=del_select_memo');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($memo_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                  <td class="smallText" valign="top">
                  <?php
                  if($ocertify->npermission >= 15 && tep_db_num_rows($memo_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select name="edit_memo_list" onchange="select_memo_change(this.value,\'memo_list_id[]\',\''.$ocertify->npermission.'\');">';
                    echo '<option value="0">'.TEXT_MEMO_EDIT_SELECT.'</option>';
                    echo '<option value="1">'.TEXT_MEMO_EDIT_DELETE.'</option>';
                    echo '</select>';
                    echo '</div>';
                  }
                  ?>
                  </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $memo_split->display_count($memo_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BUSINESS_MEMO); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $memo_split->display_links($memo_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php echo '<a href="javascript:void(0);" onclick="create_memo(this);">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?></div></td>
                  </tr>
          </table>
	  </td>
          </tr>
        </table></form></td>
      </tr>
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
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
