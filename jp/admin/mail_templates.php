<?php
  /**
   * $Id$
   *
   * 邮件模板管理
   */
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');
  
  if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'save' 更新mail templates     
------------------------------------------------------*/ 
      case 'save':
        $mail_id = tep_db_prepare_input($_POST['mail_id']);
        $templates_title = tep_db_prepare_input($_POST['templates_title']); 
        $title = tep_db_prepare_input($_POST['title']);
        $contents = tep_db_prepare_input($_POST['contents']);  
        $param_str = tep_db_prepare_input($_POST['url']);

        tep_db_query("update " . TABLE_MAIL_TEMPLATES . " set templates_title='".$templates_title."',title='".$title."',contents='".$contents."',contents='".$contents."',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($mail_id) . "'");

        tep_redirect(tep_href_link(FILENAME_MAIL_TEMPLATES, $param_str));
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
var box_warp_height = '';
<?php //快捷键监听?>
$(document).ready(function() { 
box_warp_height = $(".box_warp").height();
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
        if ($("#mail_prev")) {
          $("#mail_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#mail_next")) {
          $("#mail_next").trigger("click");
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

<?php //编辑mail templates信息?>
function show_mail_info(ele, mail_id, i_param_str, url)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_mail',      
    data: 'mail_id='+mail_id+'&param_str='+i_param_str+'&url='+url+'<?php echo isset($_GET['search']) ? '&search='.$_GET['search'] : '';?>'+'<?php echo isset($_GET['site_id']) ? '&site_id='.$_GET['site_id'] : '';?>'+'<?php echo isset($_GET['order_sort']) ? '&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type'] : '';?>',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
          offset = ele.offsetTop+$('#mail_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#mail_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#mail_list_box').position().top+ele.offsetHeight;
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

<?php //编辑mail的上一个，下一个信息?>
function show_link_mail_info(mail_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_mail',      
    data: 'mail_id='+mail_id+'&param_str='+param_str+'<?php echo isset($_GET['search']) ? '&search='.$_GET['search'] : '';?>'+'<?php echo isset($_GET['site_id']) ? '&site_id='.$_GET['site_id'] : '';?>'+'<?php echo isset($_GET['order_sort']) ? '&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type'] : '';?>',
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

<?php //mail内容编辑?>
function edit_mail_check(c_permission){

  var error = false;
  var mail_name = document.getElementsByName("templates_title")[0];
  var mail_name_value = mail_name.value;
  mail_name_value = mail_name_value.replace(/\s/g,"");
  if(mail_name_value == ''){

    error = true; 
    $("#mail_name_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_MAIL_MUST_INPUT;?></font>');
  }else{
    $("#mail_name_error").html('<?php echo TEXT_FIELD_REQUIRED;?>'); 
  }
  var mail_title = document.getElementsByName("title")[0];
  var mail_title_value = mail_title.value;
  mail_title_value = mail_title_value.replace(/\s/g,"");
  if(mail_title_value == ''){

    error = true; 
    $("#mail_title_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_MAIL_MUST_INPUT;?></font>');
  }else{
    $("#mail_title_error").html('<?php echo TEXT_FIELD_REQUIRED;?>'); 
  }
  var mail_contents = document.getElementsByName("contents")[0];
  var mail_contents_value = mail_contents.value;
  mail_contents_value = mail_contents_value.replace(/\s/g,"");
  if(mail_contents_value == ''){

    error = true; 
    $("#mail_contents_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_MAIL_MUST_INPUT;?></font>');
  }else{
    $("#mail_contents_error").html('<?php echo TEXT_FIELD_REQUIRED;?>'); 
  }
  if(error == false){
  if (c_permission == 31) {
    document.edit_mail.action = '<?php echo tep_href_link(FILENAME_MAIL_TEMPLATES, 'action=save');?>';
    document.edit_mail.submit();
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
          document.edit_mail.action = '<?php echo tep_href_link(FILENAME_MAIL_TEMPLATES, 'action=save');?>';
          document.edit_mail.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_MAIL_TEMPLATES, 'action=save');?>'),
              async: false,
              success: function(msg_info) {
                document.edit_mail.action = '<?php echo tep_href_link(FILENAME_MAIL_TEMPLATES, 'action=save');?>';
                document.edit_mail.submit();
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
          <tr><?php echo tep_draw_form('search', FILENAME_MAIL_TEMPLATES,'', 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          <td class="smallText" align="right"><?php echo (isset($_GET['site_id'])?'<input type="hidden" name="site_id" value="'.$_GET['site_id'].'">':'').HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search'); ?>
          <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
          <br><?php echo TEXT_MAIL_SEARCH_READ_TITLE;?> 
          </td>
          </form></tr>
        </table></td>
      </tr>
      <tr>
        <td>
<?php 
  // 获取网站的 romaji
  $site_id_name_array = array(0=>'all');
  $site_name_query = tep_db_query("select id,romaji from ".TABLE_SITES);
  while($site_name_array = tep_db_fetch_array($site_name_query)){

    $site_id_name_array[$site_name_array['id']] = $site_name_array['romaji'];
  }
  tep_db_free_result($site_name_query);
  echo tep_show_site_filter(FILENAME_MAIL_TEMPLATES,false);
?>
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:90%;min-width:550px;margin-left:0;display:none;"></div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="mail_list_box">
          <tr>
            <td valign="top">
<?php 
  echo tep_draw_form('edit_mail_form',FILENAME_MAIL_TEMPLATES, '', 'post');
  $mail_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => ''); 
  $notice_box = new notice_box('', '', $mail_table_params); 
  $mail_table_row = array();
  $mail_title_row = array();
                  
  //mail templates列表  
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_mail(\'mail_list_id[]\');" name="all_check" disabled="disabled">');
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=site_id&order_type='.($_GET['order_sort'] == 'site_id' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MAIL_SITE_ID.($_GET['order_sort'] == 'site_id' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'site_id' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=name&order_type='.($_GET['order_sort'] == 'name' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MAIL_NAME.($_GET['order_sort'] == 'name' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'name' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=title&order_type='.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MAIL_TITLE.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=contents&order_type='.($_GET['order_sort'] == 'contents' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MAIL_CONTENTS.($_GET['order_sort'] == 'contents' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'contents' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
  $mail_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=scope&order_type='.($_GET['order_sort'] == 'scope' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MAIL_USE_SCOPE.($_GET['order_sort'] == 'scope' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'scope' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $mail_title_row[] = array('align' => 'left','params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_MAIL_TEMPLATES,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=action&order_type='.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TABLE_HEADING_ACTION.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
                    
  $mail_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $mail_title_row);   

  $site_romaji_str = '';
  $field_str = '*';
  //排序处理
  if(isset($_GET['order_sort']) && $_GET['order_sort'] != '' && isset($_GET['order_type']) && $_GET['order_type'] != ''){
    switch($_GET['order_sort']){

    case 'site_id':
      $order_sort = 's.romaji';
      $order_type = $_GET['order_type'];
      $site_romaji_str = ' mt left join (select id,romaji from '. TABLE_SITES .' union select 0,\'all\') s on mt.site_id=s.id ';
      $field_str = 'mt.id id,mt.flag flag,mt.site_id site_id,mt.templates_title templates_title,mt.use_description use_description,mt.title title,mt.contents contents,mt.contents_description contents_description,mt.use_scope use_scope,mt.user_added user_added,mt.date_added date_added,mt.user_update user_update,mt.date_update date_update';
      break;
    case 'name':
      $order_sort = 'templates_title';
      $order_type = $_GET['order_type'];
      break;
    case 'title':
      $order_sort = 'title';
      $order_type = $_GET['order_type'];
      break;
    case 'contents':
      $order_sort = 'contents';
      $order_type = $_GET['order_type'];
      break;
    case 'scope':
      $order_sort = 'use_scope';
      $order_type = $_GET['order_type'];
      break; 
    case 'action':
      $order_sort = 'date_update';
      $order_type = $_GET['order_type'];
      break;
    default:
      $order_sort = 'id';
      $order_type = 'asc';
      break;
    }
  }else{
    $order_sort = 'id';
    $order_type = 'asc'; 
  }

  $site_id_str = '';
  if(isset($_GET['site_id']) && $_GET['site_id'] != ''){

    $site_id_array = explode('-',$_GET['site_id']);
    $site_id_str = implode(',',$site_id_array);
    $site_id_str = ' where site_id in ('.$site_id_str.')';
  }else{
    
    $show_site_query = tep_db_query("select site from ". TABLE_SHOW_SITE ." where page='".FILENAME_MAIL_TEMPLATES."'"); 
    $show_site_array = tep_db_fetch_array($show_site_query);
    tep_db_free_result($show_site_query);
    $site_id_array = explode('-',$show_site_array['site']);
    $site_id_str = implode(',',$site_id_array);
    $site_id_array = array_filter($site_id_array);
    if(!empty($site_id_array)){
      $site_id_str = ' where site_id in ('.$site_id_str.')';
    }else{
      $site_id_str = ''; 
    }
  } 

  $keyword = '';
  if(isset($_GET['search']) && trim($_GET['search']) != ''){

    $keyword = $_GET['search'];
    if(isset($_GET['site_id']) && $_GET['site_id'] != ''){
      $keyword_str = " and (templates_title like '%".$keyword."%' or title like '%".$keyword."%' or contents like '%".$keyword."%')";
    }else{
      if(count($site_id_array) < 1){
        $keyword_str = " where templates_title like '%".$keyword."%' or title like '%".$keyword."%' or contents like '%".$keyword."%'"; 
      }else{

        $keyword_str = " and (templates_title like '%".$keyword."%' or title like '%".$keyword."%' or contents like '%".$keyword."%')"; 
      }
    }
  }
  $mail_query_raw = "select ".$field_str." from " . TABLE_MAIL_TEMPLATES . $site_romaji_str . $site_id_str . $keyword_str ." order by ".$order_sort." ".$order_type;
  $mail_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $mail_query_raw, $mail_query_numrows);
  $mail_query = tep_db_query($mail_query_raw);
  if(tep_db_num_rows($mail_query) == 0){
    $mail_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
    $mail_table_row[] = array('params' => '', 'text' => $mail_data_row);  
  }
  //前台、后台图标
  $use_scope_array = array(
                           tep_image(DIR_WS_IMAGES . 'icon_frontend.gif',IMAGE_ICON_FRONTENT),
                           tep_image(DIR_WS_IMAGES . 'icon_backend.gif',IMAGE_ICON_BACKEND), 
                           tep_image(DIR_WS_IMAGES . 'icon_frontend.gif',IMAGE_ICON_FRONTENT).'&nbsp;'.tep_image(DIR_WS_IMAGES . 'icon_backend.gif',IMAGE_ICON_BACKEND)
                           );

  while ($mail = tep_db_fetch_array($mail_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $mail['id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($mail);
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($mail['id'] == $cInfo->id) ) {
      $mail_item_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $mail_item_params = '<tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $mail_item_info = array();  
    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" value="'.$mail['id'].'" name="mail_list_id[]" disabled="disabled">'   
                          );
    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('x', 'y', 'cID')) . '&cID=' . $mail['id']) . '\'"', 
                          'text' => $site_id_name_array[$mail['site_id']]  
                          );     
    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('x', 'y', 'cID')) . '&cID=' . $mail['id']) . '\'"', 
                          'text' => $mail['templates_title'] 
                        );

    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('x', 'y', 'cID')) . '&cID=' . $mail['id']) . '\'"', 
                          'text' => $mail['title'] 
                        ); 
    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('x', 'y', 'cID')) . '&cID=' . $mail['id']) . '\'"', 
                          'text' => (mb_strlen($mail['contents']) > 50 ? mb_substr($mail['contents'],0,50,'utf-8').'...' : nl2br($mail['contents'])) 
                        );  
    $mail_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('x', 'y', 'cID')) . '&cID=' . $mail['id']) . '\'"', 
                          'text' => $use_scope_array[$mail['use_scope']]
                        );
    $mail_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="javascript:void(0);" onclick="show_mail_info(this, \''.$mail['id'].'\', \'page='.$_GET['page'].'\',\''.str_replace('&','|||',tep_get_all_get_params(array('x', 'y'))).'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($mail['date_update'] != '' && $mail['date_update'] != '0000-00-00 00:00:00' ? $mail['date_update'] : $mail['date_added'])))).'</a>' 
                          ); 
                      
    $mail_table_row[] = array('params' => $mail_item_params, 'text' => $mail_item_info);

  }

  $form_str = tep_draw_form('mail_list', FILENAME_MAIL_TEMPLATES, tep_get_all_get_params(array('action')).'action=del_select_mail');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($mail_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
?>
           <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
           <tr>
              <td class="smallText" valign="top">
                  <?php
                  if($ocertify->npermission >= 15 && tep_db_num_rows($mail_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select name="edit_mail_list" disabled="disabled">';
                    echo '<option value="0">'.TEXT_MAIL_EDIT_SELECT.'</option>';
                    echo '</select>';
                    echo '</div>';
                  }
                  ?>
              </td>
              </tr>
           <tr>
              <td class="smallText" valign="top"><?php echo $mail_split->display_count($mail_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MAIL_TEMPLATES); ?></td>
              <td class="smallText" align="right"><div class="td_box"><?php echo $mail_split->display_links($mail_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('x', 'y', 'page'))); ?></div></td>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
