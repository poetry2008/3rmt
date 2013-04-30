<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'setflag' 设置新闻是否显示    
   case 'setfirst' 置顶    
   case 'delete_latest_news_confirm' 删除新闻    
   case 'insert_latest_news' 新建新闻    
   case 'update_latest_news' 更新新闻    
------------------------------------------------------*/
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['latest_news_id']) {
            tep_db_query("update " . TABLE_NEWS . " set status = '" .  $_GET['flag'] . "',update_editor = '".$_SESSION['user_name']."', latest_update_date = '".tep_db_prepare_input(time())."' where news_id = '" . $_GET['latest_news_id'] . "'");
          }
        }

        tep_redirect(tep_href_link(FILENAME_NEWS, (isset($_GET['site_id'])?('site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'')));
        break;
      case 'setfirst':
        $latest_news = tep_get_latest_news_by_id($_GET['latest_news_id']);
        if ($_GET['isfirst'] == '0') {
          if ($_GET['latest_news_id']) {
            tep_db_query("update " . TABLE_NEWS . " set isfirst = '" .  $_GET['isfirst'] . "',update_editor = '".$_SESSION['user_name']."',latest_update_date = '".tep_db_prepare_input(time())."' where news_id = '" . $_GET['latest_news_id'] . "'");
          }
        }
        if ($_GET['isfirst'] == '1') {
            if ($_GET['latest_news_id']) {
              tep_db_query("update " . TABLE_NEWS . " set isfirst = '" .  $_GET['isfirst'] . "',update_editor = '".$_SESSION['user_name']."',latest_update_date = '".tep_db_prepare_input(time())."' where news_id = '" . $_GET['latest_news_id'] . "'");
            }
        }
tep_redirect(tep_href_link(FILENAME_NEWS, (isset($_GET['site_id'])?('site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'')));

        break;
      case 'delete_latest_news_confirm':
        if(!empty($_POST['news_id'])){
            foreach ($_POST['news_id'] as $ge_key => $ge_value) {
            tep_db_query("delete from " . TABLE_NEWS . " where news_id = '" .$ge_value. "'");
            }
        }
        if ($_GET['latest_news_id']) {
          $latest_news_id = tep_db_prepare_input($_GET['latest_news_id']);
         tep_db_query("delete from " . TABLE_NEWS . " where news_id = '" . tep_db_input($latest_news_id) . "'");
        }
        tep_redirect(tep_href_link(FILENAME_NEWS, (isset($_GET['site_id'])?('site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'')));
        break;
      case 'insert_latest_news':
        if ($_POST['headline']) {
		
          $sql_data_array = array('headline'   => tep_db_prepare_input($_POST['headline']),
                                  'content'    => tep_db_prepare_input($_POST['content']),
				  'author'     => tep_db_prepare_input($_POST['author']),
				  'update_editor'=> tep_db_prepare_input($_POST['author']),
				  'latest_update_date' => tep_db_prepare_input(mktime()-3600),
                                  'news_image' => tep_db_prepare_input($_POST['news_image']),
                                  'news_image_description' => tep_db_prepare_input($_POST['news_image_description']),
                                  'date_added' => 'now()', //uses the inbuilt mysql function 'now'
                                  'site_id'    => tep_db_prepare_input($_POST['site_id']),
                                  'status'     => '1' );
          tep_db_perform(TABLE_NEWS, $sql_data_array);
          $news_id = tep_db_insert_id(); //not actually used ATM -- just there in case
        }
    
        $news_image = tep_get_uploaded_file('news_image');
        if (!empty($news_image['name'])) {
          $pic_rpos = strrpos($news_image['name'], ".");
          $pic_ext = substr($news_image['name'], $pic_rpos+1);
          $news_image_name = 'news'.time().".".$pic_ext;
          $news_image['name'] = $news_image_name; 
        } else {
          $news_image_name = ''; 
        }
        $image_directory = tep_get_local_path(tep_get_upload_dir($sql_data_array['site_id']) . 'news/');
    $path = 'news/';
    
    if (is_uploaded_file($news_image['tmp_name'])) {
          tep_db_query("update " . TABLE_NEWS . " set news_image = '" . $path . $news_image_name . "' where news_id = '" . $news_id . "'");
          tep_copy_uploaded_file($news_image, $image_directory);
        }
        
        tep_redirect(tep_href_link(FILENAME_NEWS, isset($_GET['site_id'])?('site_id='.$_GET['site_id']):''));
        break;

      case 'update_latest_news':
    
        if($_GET['latest_news_id']) {
          $latest_news = tep_get_latest_news_by_id($_GET['latest_news_id']);
          $sql_data_array = array('headline' => tep_db_prepare_input($_POST['headline']),
                                  'news_image_description' => tep_db_prepare_input($_POST['news_image_description']),
                                  'update_editor'  => tep_db_prepare_input($_POST['update_editor']), 
				  'latest_update_date' => tep_db_prepare_input(time()),
                                  'content'  => tep_db_prepare_input($_POST['content']) );
                                  
          tep_db_perform(TABLE_NEWS, $sql_data_array, 'update', "news_id = '" . tep_db_prepare_input($_GET['latest_news_id']) . "'");
        }
        $news_image = tep_get_uploaded_file('news_image');
        if (!empty($news_image['name'])) {
          $pic_rpos = strrpos($news_image['name'], ".");
          $pic_ext = substr($news_image['name'], $pic_rpos+1);
          $news_image_name = 'news'.time().".".$pic_ext;
          $news_image['name'] = $news_image_name; 
        } else {
          $news_image_name = ''; 
        }
        $image_directory = tep_get_local_path(tep_get_upload_dir($latest_news['site_id']) . 'news/');
    $path = 'news/';
    
    if (is_uploaded_file($news_image['tmp_name'])) {
      tep_db_query("
          update " . TABLE_NEWS . " 
          set news_image = '" . $path . $news_image_name . "',update_editor =
          '".$_SESSION['']."' 
          where news_id = '" . $_GET['latest_news_id'] . "'");
          tep_copy_uploaded_file($news_image, $image_directory);
    }
        tep_redirect(tep_href_link(FILENAME_NEWS, (isset($_GET['site_id'])?('site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'').(isset($_GET['latest_news_id'])?('&latest_news_id='.$_GET['latest_news_id']):'')));
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
<script>
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_latest_news').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_latest_news').css('display') != 'none') {
               $("#show_latest_news").find('input:button').first().trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

 function check_news_info(){
       var headline = document.getElementById('headline').value; 
       var content  = document.getElementById('content').value;
       var news_image_description = document.getElementById('news_image_description').value;
       $.ajax({
         url: 'ajax.php?action=edit_latest_news',
         type: 'POST',
         dataType: 'text',
         data:'headline='+headline+'&content='+content+'&news_image_description='+news_image_description, 
         async:false,
         success: function (data){
          if (headline != '') {
            document.forms.new_latest_news.submit(); 
          }else{
            $("#title_error").html('<?php echo TEXT_ERROR_NULL;?>'); 
          }
         }
        });
}
function all_select_news(news_str){
      var check_flag = document.del_news.all_check.checked;
         if (document.del_news.elements[news_str]) {
           if (document.del_news.elements[news_str].length == null){
                if (check_flag == true) {
                    document.del_news.elements[news_str].checked = true;
                   } else {
                       document.del_news.elements[news_str].checked = false;
                   }
            } else {
              for (i = 0; i < document.del_news.elements[news_str].length; i++){
                if (check_flag == true) {
                   document.del_news.elements[news_str][i].checked = true;
                } else {
                 document.del_news.elements[news_str][i].checked = false;
                }
               }
            }
          }
}

function delete_select_news(news_str){
         sel_num = 0;
         if (document.del_news.elements[news_str].length == null) {
              if (document.del_news.elements[news_str].checked == true){
                   sel_num = 1;
              }
         } else {
           for (i = 0; i < document.del_news.elements[news_str].length; i++) {
             if(document.del_news.elements[news_str][i].checked == true) {
                 sel_num = 1;
                 break;
             }
            }
         }
        if (sel_num == 1) {
           if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
              document.forms.del_news.submit(); 
           }else{
              document.getElementsByName('news_action')[0].value = 0;
           }
         } else {
            document.getElementsByName('news_action')[0].value = 0;
            alert('<?php echo TEXT_NEWS_MUST_SELECT;?>'); 
         }
}
function show_latest_news(ele,page,latest_news_id,site_id){
 $.ajax({
 url: 'ajax.php?&action=edit_latest_news',
 data: {page:page,latest_news_id:latest_news_id,site_id:site_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_latest_news").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(latest_news_id != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_latest_news').height()){
offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_latest_news').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_latest_news').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_latest_news').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(latest_news_id == -1){
  show_text_list = $('#show_text_list').offset();
  $('#show_latest_news').css('top',show_text_list.top);
}
$('#show_latest_news').css('z-index','1');
$('#show_latest_news').css('left',leftset);
$('#show_latest_news').css('display', 'block');
  }
  }); 
}
function hidden_info_box(){
   $('#show_latest_news').css('display','none');
}
<?php //选择动作?>
function news_change_action(r_value, r_str) {
 if (r_value == '1') {
     delete_select_news(r_str);
   }
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=new_latest_news/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/latest_news_id=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url.'?'.$belong_temp_array[0][0];
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input type="hidden" name="show_info_id" value="show_latest_news" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_latest_news"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (!isset($_GET['action']) && $_GET['action'] != 'new_latest_news') { 
?>
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
        <?php tep_site_filter(FILENAME_NEWS);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_text_list">
          <tr>
            <td valign="top">
             <?php
               $news_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
               $notice_box = new notice_box('','',$news_table_params);       
               $news_table_row = array();
               $news_title_row = array();
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_news(\'news_id[]\');">');
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_SITE);
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_LATEST_NEWS_HEADLINE);
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => TABLE_HEADING_DATE_ADDED);
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent" align="center"','text' => TABLE_HEADING_LATEST_NEWS_STATUS);
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent" align="center"','text' => TABLE_HEADING_LATEST_NEWS_ISFIRST);
               $news_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right" nowrap','text' => TABLE_HEADING_LATEST_NEWS_ACTION);
               $news_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $news_title_row);
    $rows = 0;

    //$latest_news_count = 0;
    $latest_news_query_raw = '
        select n.news_id, 
               n.headline, 
	       n.date_added,
	       n.author,
	       n.update_editor,
	       n.latest_update_date,
               n.content, 
               n.status, 
               n.news_image, 
               n.news_image_description, 
               n.isfirst,
               n.site_id
        from ' . TABLE_NEWS . ' n
        where 1 
        ' . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and (n.site_id = '" . intval($_GET['site_id']) . "') " : '') . '
        order by date_added desc
    ';
    $latest_news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $latest_news_query_raw, $latest_news_query_numrows);
    $latest_news_query = tep_db_query($latest_news_query_raw);
    
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      $rows++;
      
      if ( ((!isset($_GET['latest_news_id']) || !$_GET['latest_news_id']) || ($_GET['latest_news_id'] == $latest_news['news_id'])) && (!isset($selected_item) || !$selected_item) && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $latest_news;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($selected_item) && is_array($selected_item)) && ($latest_news['news_id'] == $selected_item['news_id']) ) {
        $news_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
      } else {
        $news_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
      $news_info = array();
      $news_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<input type="checkbox" name="news_id[]" value="'.$latest_news['news_id'].'">'
          );
      $news_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '&nbsp;'.tep_get_site_romaji_by_id($latest_news['site_id']) 
          );
      $news_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '&nbsp;' . $latest_news['headline']
          );
      $news_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '&nbsp;' . date("Y-m-d",strtotime($latest_news['date_added']))
          );
  if ($latest_news['status'] == '1') {
        $latest_news_status = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;<a href="' . tep_href_link(FILENAME_NEWS, 'page='.$_GET['page'].'&action=setflag&flag=0&latest_news_id=' . $latest_news['news_id']. (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
      } else {
        $latest_news_status = '<a href="' . tep_href_link(FILENAME_NEWS, 'page='.$_GET['page'].'&action=setflag&flag=1&latest_news_id=' . $latest_news['news_id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
      }
      $news_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   => $latest_news_status
          );
  if ($latest_news['isfirst']) {
     $latest_news_isfirst = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '<a href="' . tep_href_link(FILENAME_NEWS, 'action=setfirst&isfirst=0&latest_news_id=' . $latest_news['news_id']. (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '"> ' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
  } else {
     $latest_news_isfirst =  '<a href="' . tep_href_link(FILENAME_NEWS,'action=setfirst&isfirst=1&latest_news_id=' . $latest_news['news_id']. (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
 }

      $news_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   => $latest_news_isfirst
          );
      $news_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => '<a href="javascript:void(0)" onclick="show_latest_news(this,'.$_GET['page'].','.$latest_news['news_id'].','.(isset($_GET['site_id'])?($_GET['site_id']):'0').')">' .  tep_get_signal_pic_info(date('Y-m-d H:i:s',$latest_news['latest_update_date'])). '</a>'
          );
  $news_table_row[] = array('params' => $news_params, 'text' => $news_info);
  } 
  $news_form = tep_draw_form('del_news',FILENAME_NEWS,'action=delete_latest_news_confirm&site_id='.$_GET['site_id'].'&page='.$_GET['page']);
  $notice_box->get_form($news_form);
  $notice_box->get_contents($news_table_row);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
?>&nbsp;</td>
              </tr>

            </table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:-10px;">
<tr>                 
                    <?php 
                    if($_GET['site_id'] == null){
                      $site_id = 0;
                    }else{
                      $site_id = $_GET['site_id'];
                    }
                    $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
                    while($userslist= tep_db_fetch_array($sites_id)){
                         $site_permission = $userslist['site_permission'];
                    }
                    if(isset($site_permission)) $site_arr=$site_permission;//权限判断
                    else $site_arr="";
                    $site_array = explode(',',$site_arr);
                    ?>
                    <td valign="top" class="smallText">
                    <?php 
                    if($ocertify->npermission == 15){
                    if(in_array($site_id,$site_array)){
                       echo '<select name="news_action" onchange="news_change_action(this.value, \'news_id[]\');">';
                    }else{
                       echo '<select name="news_action" disabled="disabled">';
                     }
                    echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                    echo '</select>';
                    }
                    ?> 
                    </td>
                    <td align="right" class="smallText">
                   </td>
                  </tr>

                  <tr>
                    <td class="smallText" valign="top"><?php echo $latest_news_split->display_count($latest_news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $latest_news_split->display_links($latest_news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></div></td>
                  </tr>
                     <tr><td></td><td align="right">
                      <div class="td_button"><?php
                    if(in_array($site_id,$site_array)){
                      echo '&nbsp;<a href="javascript:void(0)" onclick="show_latest_news(this,'.$_GET['page'].',-1,'.$site_id.')">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; 
                    }else{
                      echo tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"'); 
                    }
                    ?>
                    </div>
                     </td></tr>
                                  </table>
			</td>
<?php
    $heading = array();
    $contents = array();
    switch (isset($_GET['action'])?$_GET['action']:null) {
/* -----------------------------------------------------
   case 'delete_latest_news' 右侧删除新闻页面    
   default 右侧默认页面     
------------------------------------------------------*/
      case 'delete_latest_news':
        $heading[] = array('text'   => TEXT_INFO_HEADING_DELETE_ITEM);
        
        $contents = array('form'    => tep_draw_form('news', FILENAME_NEWS, 'action=delete_latest_news_confirm'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'')) . tep_draw_hidden_field('latest_news_id', $_GET['latest_news_id']));
        $contents[] = array('text'  => TEXT_DELETE_ITEM_INTRO);
        $contents[] = array('text'  => '<br>' . $selected_item['headline']);
        
        $contents[] = array('align' => 'center',
                            'text'  => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_NEWS, 'latest_news_id=' . $selected_item['news_id'].(isset($_GET['page'])?('&page='.$_GET['page']):'')) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
        break;

      default:
        if ($rows > 0) {
          if (is_array($selected_item)) { 
            //an item is selected, so make the side box
            $heading[] = array('text' => $selected_item['headline']);

            $contents[] = array('align' => 'center', 
                                'text' => '<a href="' .  tep_href_link(FILENAME_NEWS, 'latest_news_id=' . $selected_item['news_id'] .  '&action=new_latest_news') .  (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'').'">' . tep_html_element_button(IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_NEWS, 'latest_news_id=' . $selected_item['news_id'] .  '&action=delete_latest_news'.  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').  (isset($_GET['page'])?('&page='.$_GET['page']):'')) .  '">' . tep_html_element_button(IMAGE_DELETE) .  '</a>');
            $contents[] = array('text' => '<br>' . $selected_item['content']);
if(tep_not_null($selected_item['author'])){
$contents[] = array('text' => TEXT_USER_ADDED.'&nbsp;'. $selected_item['author']);
}else{
$contents[] = array('text' => TEXT_USER_ADDED.'&nbsp;'. TEXT_UNSET_DATA);
}if(tep_not_null($selected_item['date_added'])){
$contents[] = array('text' => TEXT_DATE_ADDED. '&nbsp;'.$selected_item['date_added']);
}else{
$contents[] = array('text' => TEXT_DATE_ADDED. '&nbsp;'.TEXT_UNSET_DATA);
}if(tep_not_null($selected_item['update_editor'])){
$contents[] = array('text' => TEXT_USER_UPDATE. '&nbsp;'.$selected_item['update_editor']);
}else{
$contents[] = array('text' => TEXT_USER_UPDATE. '&nbsp;'.TEXT_UNSET_DATA);
}if(tep_not_null($selected_item['latest_update_date'])){
$contents[] = array('text' => TEXT_DATE_UPDATE. '&nbsp;'.date("Y-m-d H:i:s",$selected_item['latest_update_date']));
}else{
$contents[] = array('text' => TEXT_DATE_UPDATE. '&nbsp;'.TEXT_UNSET_DATA);
}
          }
        } else { // create category/product info
          $heading[] = array('text' => EMPTY_CATEGORY);

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, $parent_categories_name));
        }
        break;
    }

/*    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }*/
?>
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
