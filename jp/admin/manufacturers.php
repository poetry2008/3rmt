<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');

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
if(!isset($_GET['sort']) || $_GET['sort'] == ''){
   $manufacturers_str = 'manufacturers_name';
}else if($_GET['sort'] == 'm_name'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'manufacturers_name desc';
      $manufacturers_type = 'asc';
   }else{
      $manufacturers_str = 'manufacturers_name asc';
      $manufacturers_type = 'desc';
   }
}else if($_GET['sort'] == 'last_modified'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'last_modified desc';
      $manufacturers_type = 'asc';
   }else{
      $manufacturers_str = 'last_modified asc';
      $manufacturers_type = 'desc';
   }
}

  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
/*----------------------------------
 case 'insert'  添加制造商
 case 'save'    更新制造商
 case 'deleteconfirm' 删除制造商
 ---------------------------------*/
    case 'insert':
    case 'save':
      $manufacturers_id = tep_db_prepare_input($_GET['mID']);
      $manufacturers_name = tep_db_prepare_input($_POST['manufacturers_name']);

      $sql_data_array = array('manufacturers_name' => $manufacturers_name);


      $manufacturers_image = tep_get_uploaded_file('manufacturers_image');
      $image_directory = tep_get_local_path(tep_get_upload_dir().'manufacturers/');
      $manufacturers_image['size'] = $manufacturers_image['size'] / 1024 / 1024;
      if($manufacturers_image['size'] >= ini_get('upload_max_filesize')
          ||($manufacturers_image['size']==0&&$manufacturers_image['name']!='')
          ||empty($_POST)){
        $_SESSION['error_image'] = TEXT_IMAGE_MAX;
        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
        exit;
      }else{
      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array('date_added' => 'now()','last_modified' => 'now()','user_added' => $_POST['user_added'],'user_update' => $_POST['user_update'],'manufacturers_alt' => $_POST['manufacturers_alt']);
        $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
        $manufacturers_id = tep_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array('last_modified' => 'now()','user_update' => $_POST['user_update'],'manufacturers_alt' => $_POST['manufacturers_alt']);
        $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
        tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      }
      if (is_uploaded_file($manufacturers_image['tmp_name'])) {
        if (!is_writeable($image_directory)) {
          if (is_dir($image_directory)) {
            $messageStack->add_session(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $image_directory), 'error');
          } else {
            $messageStack->add_session(sprintf(ERROR_DIRECTORY_DOES_NOT_EXIST, $image_directory), 'error');
          }
        } else {
          tep_db_query("update " . TABLE_MANUFACTURERS . " set manufacturers_image = '" . $manufacturers_image['name'] . "' where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
          tep_copy_uploaded_file($manufacturers_image, $image_directory);
        }
      }

      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $manufacturers_url_array = $_POST['manufacturers_url'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('manufacturers_url' => tep_db_prepare_input($manufacturers_url_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                   'languages_id' => $language_id);
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . tep_db_input($manufacturers_id) . "' and languages_id = '" . $language_id . "'");
        }
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('manufacturers');
      }
      tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
      }

      break;
    case 'deleteconfirm':
        if(!empty($_POST['manufacturers_id'])){
           foreach($_POST['manufacturers_id'] as $ge_key => $ge_value){
            tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $ge_value . "'");
            tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $ge_value . "'");
           } 
        }
        $manufacturers_id = tep_db_prepare_input($_GET['mID']);
        $manufacturer_query = tep_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
        $manufacturer = tep_db_fetch_array($manufacturer_query);
        $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $manufacturer['manufacturers_image'];
        if (file_exists($image_location)) @unlink($image_location);

      tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      if ($_GET['delete_products'] == 'on') {
        $products_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
        while ($products = tep_db_fetch_array($products_query)) {
          tep_remove_product($products['products_id']);
        }
      } else {
        tep_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('manufacturers');
      }

      tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
      break;
  }
if(isset($_SESSION['error_image'])&&$_SESSION['error_image']){
  $messageStack->add_session($_SESSION['error_image'], 'error');
  unset($_SESSION['error_image']);
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
<script type="text/javascript">
function all_select_manufacturers(manufacturers_str){
      var check_flag = document.del_manufacturers.all_check.checked;
          if (document.del_manufacturers.elements[manufacturers_str]) {
            if (document.del_manufacturers.elements[manufacturers_str].length == null){
                if (check_flag == true) {
                  document.del_manufacturers.elements[manufacturers_str].checked = true;
                 } else {
                  document.del_manufacturers.elements[manufacturers_str].checked = false;
                 }
                } else {
            for (i = 0; i < document.del_manufacturers.elements[manufacturers_str].length; i++){
                       if (!document.del_manufacturers.elements[manufacturers_str][i].disabled) { 
                         if (check_flag == true) {
                             document.del_manufacturers.elements[manufacturers_str][i].checked = true;
                         } else {
                             document.del_manufacturers.elements[manufacturers_str][i].checked = false;
                         }
                       }
                       }
                   }
             }
}
function delete_select_manufacturers(manufacturers_str, c_permission){
     sel_num = 0;
     if (document.del_manufacturers.elements[manufacturers_str].length == null) {
         if (document.del_manufacturers.elements[manufacturers_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_manufacturers.elements[manufacturers_str].length; i++) {
             if(document.del_manufacturers.elements[manufacturers_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
         if (confirm('<?php echo TEXT_DEL_MANUFACTURERS;?>')) {
           if (c_permission == 31) {
             document.forms.del_manufacturers.submit(); 
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
                  document.forms.del_manufacturers.submit(); 
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_manufacturers.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_manufacturers.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('manufacturers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                    setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('manufacturers_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('manufacturers_action')[0].value = 0;
            alert('<?php echo TEXT_MANUFACTURERS_MUST_SELECT;?>'); 
          }
}
<?php //选择动作?>
function manufacturers_change_action(r_value, r_str) {
  if (r_value == '1') {
     delete_select_manufacturers(r_str, '<?php echo $ocertify->npermission;?>');
  }
}
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_manufacturers').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
        <?php //回车?>
        if ($('#show_manufacturers').css('display') != 'none') {
          if(o_submit_single){
             $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_manufacturers').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_manufacturers').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
function show_manufacturers(ele,mID,page){
 var sort = $("#sort").val();
 var type = $("#type").val();
 $.ajax({
 url: 'ajax.php?&action=edit_manufacturers',
 data: {mID:mID,page:page,sort:sort,type:type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_manufacturers").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(mID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_manufacturers').height()){
offset = ele.offsetTop+$("#show_manufacturers_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
  offset = offset+2;
} 
$('#show_manufacturers').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_manufacturers').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_manufacturers').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(mID == -1){
  $('#show_manufacturers').css('top',$('#show_manufacturers_list').offset().top);
}
$('#show_manufacturers').css('z-index','1');
$('#show_manufacturers').css('left',leftset);
$('#show_manufacturers').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
  $('#show_manufacturers').css('display','none');
}
function check_del(mID,page,c_permission){
  if(confirm('<?php echo TEXT_DEL_MANUFACTURERS;?>')){
  if (c_permission == 31) {
     window.location.href="<?php echo tep_href_link(FILENAME_MANUFACTURERS);?>?page="+page+"&mID="+mID+"&action=deleteconfirm";
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
          window.location.href="<?php echo tep_href_link(FILENAME_MANUFACTURERS);?>?page="+page+"&mID="+mID+"&action=deleteconfirm";
        } else {
          $("#button_save").attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_CONTENTS);?>?page='+page+'&site_id='+site_id+'&cID='+cID+'&act=deleteconfirm'),
              async: false,
              success: function(msg_info) {
                window.location.href="<?php echo tep_href_link(FILENAME_MANUFACTURERS);?>?page="+page+"&mID="+mID+"&action=deleteconfirm";
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
  } 
}
<?php //提交动作?>
function toggle_manufacturers_form(c_permission){
  var manufacturers_name = $("#manufacturers_name").val();
  if(manufacturers_name == ''){
     $("#manufacturers_name_error").html("<?php echo TEXT_PLEASE_MANUFACTURERS_NAME; ?>");   
  }else{
    if (c_permission == 31) {
    document.forms.manufacturers.submit(); 
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
          document.forms.manufacturers.submit(); 
        } else {
          $("#button_save").attr('id', 'tmp_button_save');
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.manufacturers.action),
              async: false,
              success: function(msg_info) {
                document.forms.manufacturers.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
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
<?php 
if($error_image){
echo '<div style="background-color:#FF0000;font-size:12px;padding:2px;">'.$error_image.'</div>';
}
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<input id="show_info_id" type="hidden" name="show_info_id" value="show_manufacturers">
<div id="show_manufacturers" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
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
         <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_manufacturers_list">
          <tr>
           <td>
<?php 
           echo '<input type="hidden" id="sort" value="'.$_GET['sort'].'"><input type="hidden" id="type" value="'.$_GET['type'].'">';
           if($_GET['sort'] == 'm_name'){
               if($_GET['type'] == 'desc'){
                   $m_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                   $m_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
           }
           if($_GET['sort'] == 'last_modified'){
               if($_GET['type'] == 'desc'){
                   $last_modified = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                   $last_modified = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
           }
           $manufacturers_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
           $notice_box = new notice_box('','',$news_table_params);
           $manufacturers_table_row = array();
           $manufacturers_title_row = array();
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" '.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'').'onclick="all_select_manufacturers(\'manufacturers_id[]\');">');
           if(isset($_GET['sort']) && $_GET['sort'] == 'm_name'){
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=m_name&type='.$manufacturers_type).'">'.TABLE_HEADING_MANUFACTURERS.$m_name.'</a>');
           }else{
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=m_name&type=desc').'">'.TABLE_HEADING_MANUFACTURERS.$m_name.'</a>');
           }
           if(isset($_GET['sort']) && $_GET['sort'] == 'last_modified'){
           $manufacturers_title_row[] = array('params' =>
               'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=last_modified&type='.$manufacturers_type).'">'.TABLE_HEADING_ACTION.$last_modified.'</a>');
           }else{
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=last_modified&type=desc').'">'.TABLE_HEADING_ACTION.$last_modified.'</a>');
           }
           $manufacturers_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $manufacturers_title_row);
  $manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified,user_added,user_update from " .  TABLE_MANUFACTURERS . " order by ".$manufacturers_str;
  $manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturers_query_raw, $manufacturers_query_numrows);
  $manufacturers_query = tep_db_query($manufacturers_query_raw);
  $manufacturers_numrows = tep_db_num_rows($manufacturers_query);
  while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
    if (((!isset($_GET['mID']) || !$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!isset($mInfo) || !$mInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $manufacturer_products_query = tep_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
      $manufacturer_products = tep_db_fetch_array($manufacturer_products_query);

      $mInfo_array = tep_array_merge($manufacturers, $manufacturer_products);
      $mInfo = new objectInfo($mInfo_array);
    }

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if ( isset($mInfo) and (is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id) ) {
      $manufacturers_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $manufacturers_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
    $manufacturers_info = array();
    $manufacturers_checkbox = '<input type="checkbox" name="manufacturers_id[]"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'').' value="'.$manufacturers['manufacturers_id'].'">';
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent"',
        'text'   => $manufacturers_checkbox
        );
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MANUFACTURERS,'mID='.$manufacturers['manufacturers_id']).'\';"',
        'text'   => $manufacturers['manufacturers_name'] 
        );
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent" align="right"',
        'text'   => '<a href="javascript:void(0)" onclick="show_manufacturers(this,'.$manufacturers['manufacturers_id'].','.$_GET['page'].')">' .  tep_get_signal_pic_info(isset($manufacturers['last_modified']) && $manufacturers['last_modified'] != null?$manufacturers['last_modified']:$manufacturers['date_added']) . '</a>'
        );
    $manufacturers_table_row[] = array('params' => $manufacturers_params, 'text' => $manufacturers_info);
  }
     $manufacturers_form = tep_draw_form('del_manufacturers',FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm');
     $notice_box->get_form($manufacturers_form);
     $notice_box->get_contents($manufacturers_table_row);
     $notice_box->get_eof(tep_eof_hidden());
     echo $notice_box->show_notice();
?>
    </table>
        </td>
          </tr>
            </table>
             <table border="0" width="100%" cellspacing="0" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                      <?php
                        if($manufacturers_numrows > 0){
                          if($ocertify->npermission >= 15){
                            echo '<select name="manufacturers_action" onchange="manufacturers_change_action(this.value, \'manufacturers_id[]\');">';
                            echo '<option value="0">'.TEXT_MANUFACTURERS_SELECT_ACTION.'</option>';
                            echo '<option value="1">'.TEXT_MANUFACTURERS_DELETE_ACTION.'</option>';
                            echo '</select>';
                           }
                         }else{
                            echo TEXT_DATA_EMPTY;
                         }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
                    <td class="smallText" align="right">
					<div class="td_box">
					<?php echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
                  </tr>
				  <?php
  if (!isset($_GET['action']) || $_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText">
                  <div class="td_button">
                     <?php
                     if($is_u_disabled){
                     echo tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"'); 
                     }else{ 
                     echo '<a href="javascript:void(0)" onclick="show_manufacturers(this,-1,'.$_GET['page'].')">' . tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; 
                     } 
                     ?>
                  </div>
                </td>
              </tr>
<?php
  }
?>
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
