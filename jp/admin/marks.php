<?php
/*
   $Id$
 */
require('includes/application_top.php');
require(DIR_FS_ADMIN . '/classes/notice_box.php');
if (isset($_GET['action'])) {
  switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'update_pic' 更新标记图片信息   
------------------------------------------------------*/
    case 'update_pic':
      tep_isset_eof(); 
      $pic_alt_text = $_POST['pic_alt'];
      $pic_sort_order = $_POST['sort_order'];
      $user_update_info = $_POST['user_update_info'];
      $sql_data_array = array(
            'pic_alt' => tep_db_prepare_input($pic_alt_text),
            'sort_order' => tep_db_prepare_input($pic_sort_order),
            'user_update' => tep_db_prepare_input($_SESSION['user_name']),
            'date_update' => 'now()' 
          );
      tep_db_perform(TABLE_CUSTOMERS_PIC_LIST, $sql_data_array, 'update', 'id=\''.tep_db_input($_POST['pic_id']).'\''); 
      tep_redirect(tep_href_link(FILENAME_MARKS)); 
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
<script type="text/javascript">
<?php //关闭弹出框?>
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}
<?php //计算弹出框的位置?>
function info_box_set(ele, current_belong)
{
  $.ajax({
    type:'POST',
    dataType: 'text',
    url: 'ajax_orders.php?action=get_top_layer',
    data: 'current_belong='+current_belong,
    async:false,
    success: function(msg) {
      ele = ele.parentNode;
      head_top = $('.compatible_head').height();
      box_warp_height = 0;
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          if (ele.offsetTop < $('#show_popup_info').height()) {
            offset = ele.offsetTop+$("#pic_list_table").position().top+ele.offsetHeight+head_top;
            box_warp_height = offset-head_top;
          } else {
            if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
              offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
            } else {
              offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
              offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
            }
            box_warp_height = offset-head_top;
          }
        } else {
          if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
            offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
          } else {
            offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
            offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
          }
        }
        $('#show_popup_info').css('top',offset);
     } else {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
           offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
         } else {
           offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
           offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
         }
         box_warp_height = offset-head_top;
       } else {
         offset = ele.offsetTop+$("#pic_list_table").position().top+ele.offsetHeight+head_top;
         box_warp_height = offset-head_top;
       }
       $('#show_popup_info').css('top',offset);
     }
     box_warp_height = box_warp_height + $('#show_popup_info').height();
     if ($('.show_left_menu').width()) {
       leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
     } else {
       leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
     }
     $('#show_popup_info').css('z-index', msg);
     $('#show_popup_info').css('left',leftset);
     if ($('.compatible').height()<box_warp_height) {
       $('.box_warp').css('height',box_warp_height);
     } else {
       $('.box_warp').css('height',$('.compatible').height());
     }
   }
  });
}
<?php //打开弹出页?>
function show_popup_info(ele, pic_id)
{
  url_str = 'ajax.php?action=show_update_pic_info';
  data_str = 'pic_id='+pic_id;
  $.ajax({
    type:'POST',    
    dataType:'text',
    async:false,
    url:url_str,
    data:data_str,
    success: function(msg) {
      $('#show_popup_info').html(msg);
      if (ele != '') {
        info_box_set(ele, '<?php echo $belong?>'); 
      }
      $('#show_popup_info').css('display', 'block'); 
    }
  });
}
<?php //提交动作?>
function toggle_marks_form(c_permission)
{
   if (c_permission == 31) {
     document.forms.pic.submit(); 
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
          document.forms.pic.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.pic.action),
              async: false,
              success: function(msg_info) {
                document.forms.pic.submit(); 
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
<?php
if (isset($_GET['eof']) && $_GET['eof'] == 'error') {
?>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<?php
}
?>
</head>
<?php
if (isset($_GET['eof']) && $_GET['eof'] == 'error') {
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message();">
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
  one_time_pwd('<?php echo $page_name;?>');
</script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<input type="hidden" name="show_info_id" value="show_popup_info" id="show_info_id">
<div id="show_popup_info" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
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
    <td width="100%" valign="top" id="categories_right_td">
      <div class="box_warp">
      <?php echo $notes;?>
        <div class="compatible">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="top">
                    <?php
                      $pic_info_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => 'id="pic_list_table"'); 
                      $notice_box = new notice_box('', '', $pic_info_table_params); 
                      $pic_info_table_title = array();
                      $pic_info_table_list_row = array();
                      $pic_info_table_title[] = array('align' => 'left', 'params' => 'class=dataTableHeadingContent', 'text' => TABLE_HEADING_MARKS_PIC_LIST_NAME);
                      $pic_info_table_title[] = array('align' => 'left', 'params' => 'class=dataTableHeadingContent', 'text' => TABLE_HEADING_MARKS_PIC_LIST_TITLE);
                      $pic_info_table_title[] = array('align' => 'left', 'params' => 'class=dataTableHeadingContent', 'text' => TABLE_HEADING_MARKS_PIC_LIST_SORT);
                      $pic_info_table_title[] = array('align' => 'right', 'params' => 'class=dataTableHeadingContent width="30"', 'text' => TABLE_HEADING_ACTION);
                      $pic_info_table_list_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $pic_info_table_title); 
                      
                      $pic_list_query = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc");
                      while ($pic_list_res = tep_db_fetch_array($pic_list_query)) {
                        $even = 'dataTableSecondRow';
                        $odd = 'dataTableRow';
                        if (isset($nowColor) && $nowColor == $odd) {
                          $nowColor = $even; 
                        } else {
                          $nowColor = $odd; 
                        }
                        $selected_m_single = false; 
                        if (isset($_GET['m_id'])) {
                          if ($_GET['m_id'] == $pic_list_res['id']) {
                            $nowColor = 'dataTableRowSelected'; 
                            $selected_m_single = true; 
                          }
                        }
                        $pic_info_table_single_row = array();
                        $pic_info_table_single_row[] = array('align' => 'left', 'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MARKS, 'm_id='.$pic_list_res['id']).'\';"', 'text' => tep_image(DIR_WS_IMAGES.'icon_list/'.$pic_list_res['pic_name']));
                        $pic_info_table_single_row[] = array('align' => 'left', 'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MARKS, 'm_id='.$pic_list_res['id']).'\';"', 'text' => $pic_list_res['pic_alt']);
                        $pic_info_table_single_row[] = array('align' => 'left', 'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MARKS, 'm_id='.$pic_list_res['id']).'\';"', 'text' => $pic_list_res['sort_order']);
                        $pic_date_info = (tep_not_null($pic_list_res['date_update']) && ($pic_list_res['date_update'] != '0000-00-00 00:00:00'))?$pic_list_res['date_update']:$pic_list_res['date_added'];
                        $pic_info_table_single_row[] = array('align' => 'right', 'params' => 'class="dataTableContent"', 'text' => '<a href="javascript:void(0);" onclick="show_popup_info(this, \''.$pic_list_res['id'].'\');">'.tep_get_signal_pic_info($pic_date_info).'</a>');
                        
                        if ($selected_m_single) {
                          $pic_info_table_list_row[] = array('params' => 'class="'.$nowColor.'" onmouseover="this.style.cursor=\'hand\'"', 'text' => $pic_info_table_single_row); 
                        } else {
                          $pic_info_table_list_row[] = array('params' => 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\'; this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"', 'text' => $pic_info_table_single_row); 
                        }
                      }
                      
                      $notice_box->get_contents($pic_info_table_list_row); 
                      echo $notice_box->show_notice(); 
                    ?>
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
