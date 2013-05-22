<?php
  /**
   * $Id$
   *
   * PC管理
   */
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'insert' 新建按钮     
   case 'save' 更新按钮     
   case 'deleteconfirm' 删除按钮      
   case 'delete' 批量删除按钮
------------------------------------------------------*/
      case 'insert':
        $buttons_name = tep_db_prepare_input($_POST['buttons_name']);
        $sort_order = tep_db_prepare_input($_POST['sort_order']);

        $t_query = tep_db_query("select * from ". TABLE_BUTTONS . " where buttons_name = '" . $buttons_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res) {
          $messageStack->add_session(TEXT_BUTTONS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_BUTTONS, 'cPath=&action=new'));
        }
        tep_db_query("insert into " . TABLE_BUTTONS . " (buttons_name, sort_order,user_added,date_added,user_update,date_update) values ('" . tep_db_input($buttons_name) . "','" . tep_db_input($sort_order) . "','".$_SESSION['user_name']."',now(),'','')");
        tep_redirect(tep_href_link(FILENAME_BUTTONS));
        break;
      case 'save':
        $buttons_id = tep_db_prepare_input($_POST['buttons_id']);
        $buttons_name = tep_db_prepare_input($_POST['buttons_name']);
        $sort_order = tep_db_prepare_input($_POST['sort_order']);
        $param_str = $_POST['param_str'];
        
        $t_query = tep_db_query("select * from ". TABLE_BUTTONS . " where buttons_name = '" . $buttons_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res && $t_res['buttons_id'] != $buttons_id) {
          $messageStack->add_session(TEXT_BUTTONS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_BUTTONS, 'page='.$param_str));
        }
        tep_db_query("update " . TABLE_BUTTONS . " set buttons_name = '" . tep_db_input($buttons_name) . "',sort_order = '" . tep_db_input($sort_order) . "' ,user_update='".$_SESSION['user_name']."',date_update=now() where buttons_id = '" . tep_db_input($buttons_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BUTTONS, 'page='.$param_str));
        break;
      case 'deleteconfirm':
        $buttons_id = tep_db_prepare_input($_POST['buttons_id']);
        $param_str = $_POST['param_str'];
        tep_db_query("delete from " . TABLE_BUTTONS . " where buttons_id = '" . tep_db_input($buttons_id) . "'");
        tep_db_query("delete from " . TABLE_ORDERS_TO_BUTTONS . " where buttons_id = '" . tep_db_input($buttons_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BUTTONS, 'page='.$param_str));
        break;
      case 'delete':
        $buttons_id_list = tep_db_prepare_input($_POST['buttons_list_id']);
        $param_str = $_GET['page'];

        foreach($buttons_id_list as $buttons_id){
          tep_db_query("delete from " . TABLE_BUTTONS . " where buttons_id = '" . tep_db_input($buttons_id) . "'");
          tep_db_query("delete from " . TABLE_ORDERS_TO_BUTTONS . " where buttons_id = '" . tep_db_input($buttons_id) . "'");   
        }
        tep_redirect(tep_href_link(FILENAME_BUTTONS, ($param_str != '' ? 'page='.$param_str : '')));
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
        if ($("#buttons_prev")) {
          $("#buttons_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#buttons_next")) {
          $("#buttons_next").trigger("click");
        }
      }
    }
  });    
});
var box_warp_height = 0;
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
function select_buttons_change(value,buttons_list_id)
{
  sel_num = 0;
  if (document.edit_buttons_form.elements[buttons_list_id].length == null) {
    if (document.edit_buttons_form.elements[buttons_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_buttons_form.elements[buttons_list_id].length; i++) {
      if (document.edit_buttons_form.elements[buttons_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm('<?php echo TEXT_BUTTONS_EDIT_CONFIRM;?>')) {
      document.edit_buttons_form.action = "<?php echo FILENAME_BUTTONS.'?action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : '');?>";
      document.edit_buttons_form.submit(); 
    }else{

      document.getElementsByName("edit_buttons_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_buttons_list")[0].value = 0;
    alert("<?php echo TEXT_BUTTONS_EDIT_MUST_SELECT;?>"); 
  }
}

<?php //全选动作?>
function all_select_buttons(buttons_list_id)
{
  var check_flag = document.edit_buttons_form.all_check.checked;
  if (document.edit_buttons_form.elements[buttons_list_id]) {
    if (document.edit_buttons_form.elements[buttons_list_id].length == null) {
      if (check_flag == true) {
        document.edit_buttons_form.elements[buttons_list_id].checked = true;
      } else {
        document.edit_buttons_form.elements[buttons_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_buttons_form.elements[buttons_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_buttons_form.elements[buttons_list_id][i].checked = true;
        } else {
          document.edit_buttons_form.elements[buttons_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //编辑buttons信息?>
function show_buttons_info(ele, buttons_id, i_param_str)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_buttons',      
    data: 'buttons_id='+buttons_id+'&param_str='+i_param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#buttons_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#buttons_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight;
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

<?php //编辑buttons的上一个，下一个信息?>
function show_link_buttons_info(buttons_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_buttons',      
    data: 'buttons_id='+buttons_id+'&param_str='+param_str,
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

<?php //buttons内容保存时的验证?>
function edit_buttons_check(action){

  var buttons_name = document.getElementsByName("buttons_name")[0];
  var buttons_name_value = buttons_name.value;
  buttons_name_value = buttons_name_value.replace(/\s/g,"");

  if(buttons_name_value == ''){

    $("#buttons_name_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_BUTTONS_MUST_INPUT;?></font>');
  }else{
    if(action == 'save'){
      document.edit_buttons.action = '<?php echo FILENAME_BUTTONS;?>?action='+action;
      document.edit_buttons.submit();
    }else{
      document.create_buttons.action = '<?php echo FILENAME_BUTTONS;?>?action='+action; 
      document.create_buttons.submit();
    } 
  }
}

<?php //删除buttons?>
function delete_buttons(){

  document.edit_buttons.action = '<?php echo FILENAME_BUTTONS;?>?action=deleteconfirm';
  document.edit_buttons.submit();
}

<?php //新建buttons?>
function create_buttons_info(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax.php?action=create_buttons',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
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
  echo tep_show_site_filter(FILENAME_BUTTONS,false,$site_list_array);
?>
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php
  $buttons_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => 'id="buttons_list_box"'); 
  $notice_box = new notice_box('', '', $buttons_table_params); 
  $buttons_table_row = array();
  $buttons_title_row = array();
                  
  //buttons列表 
  $buttons_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_check" onclick="all_select_buttons(\'buttons_list_id[]\');">');
  $buttons_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_BUTTONS_NAME);
  $buttons_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_BUTTONS_ORDER);
  $buttons_title_row[] = array('align' => 'right','params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_ACTION);
                    
  $buttons_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $buttons_title_row);   


  $buttons_query_raw = "select * from " . TABLE_BUTTONS . " order by sort_order asc";
  $buttons_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $buttons_query_raw, $buttons_query_numrows);
  $buttons_query = tep_db_query($buttons_query_raw);
  while ($buttons = tep_db_fetch_array($buttons_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $buttons['buttons_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($buttons);
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($buttons['buttons_id'] == $cInfo->buttons_id) ) {
      $buttons_item_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $buttons_item_params = '<tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $buttons_item_info = array(); 
    $buttons_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="buttons_list_id[]" value="'.$buttons['buttons_id'].'">' 
                          );
    $buttons_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUTTONS, 'page=' . $_GET['page'] . '&cID=' . $buttons['buttons_id']) . '\'"', 
                          'text' => $buttons['buttons_name'] 
                          ); 
                      
    $buttons_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BUTTONS, 'page=' . $_GET['page'] . '&cID=' . $buttons['buttons_id']) . '\'"', 
                          'text' => $buttons['sort_order'] 
                        );

    $buttons_item_info[] = array(
                          'align' => 'right', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="javascript:void(0);" onclick="show_buttons_info(this, \''.$buttons['buttons_id'].'\', \''.$_GET['page'].'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($buttons['date_update'] != '' && $buttons['date_update'] != '0000-00-00 00:00:00' ? $buttons['date_update'] : $buttons['date_added'])))).'</a>' 
                          ); 
                      
    $buttons_table_row[] = array('params' => $buttons_item_params, 'text' => $buttons_item_info);

  }

  $form_str = tep_draw_form('edit_buttons_form', FILENAME_BUTTONS, tep_get_all_get_params(array('action')).'action=del_select_buttons');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($buttons_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                  <td class="smallText" valign="top">
                  <?php
                  if($ocertify->npermission == 15 && tep_db_num_rows($buttons_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select name="edit_buttons_list" onchange="select_buttons_change(this.value,\'buttons_list_id[]\');">';
                    echo '<option value="0">'.TEXT_BUTTONS_EDIT_SELECT.'</option>';
                    echo '<option value="1">'.TEXT_BUTTONS_EDIT_DELETE.'</option>';
                    echo '</select>';
                    echo '</div>';
                  }
                  ?>
                  </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $buttons_split->display_count($buttons_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BUTTONS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $buttons_split->display_links($buttons_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php echo '<a href="javascript:void(0);" onclick="create_buttons_info(this);">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?></div></td>
                  </tr>
          </table>
	  </td>
          </tr>
        </table></td>
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
