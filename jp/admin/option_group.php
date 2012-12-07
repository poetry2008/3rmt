<?php
/*
  $Id$
*/
  require('includes/application_top.php');


  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'update':
      case 'insert':
        $error = false;
        if (empty($_POST['name'])) {
          $error = true; 
        }
        
        if (empty($_POST['title'])) {
          $error = true; 
        }
       
        if (!$error) {
          $sql_data_array = array(
              'name' => tep_db_prepare_input($_POST['name']),
              'title' => tep_db_prepare_input($_POST['title']),
              'comment' => tep_db_prepare_input($_POST['comment']),
              'is_preorder' => tep_db_prepare_input($_POST['is_preorder']),
              'sort_num' => tep_db_prepare_input($_POST['sort_num']),
              );  
          if ($_GET['action'] == 'update') {
            $update_sql_data = array('user_update' => $_SESSION['user_name'],'date_update' => 'now()');
            $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
            tep_db_perform(TABLE_OPTION_GROUP, $sql_data_array, 'update', 'id=\''.$_POST['group_id'].'\'');
          } else if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('created_at' => 'now()','user_added' =>
                $_SESSION['user_name'],'user_update' => $_SESSION['user_name'],'date_update' => 'now()'); 
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data); 
            tep_db_perform(TABLE_OPTION_GROUP, $sql_data_array); 
          }
        }
        $param_str = substr(tep_get_all_get_params(array('group_id', 'action')), 0, -1);
        tep_redirect(tep_href_link(FILENAME_OPTION_GROUP, $param_str)); 
        break; 
      case 'deleteconfirm':
        tep_db_query('delete from '.TABLE_OPTION_GROUP.' where id = \''.$_GET['group_id'].'\''); 
        tep_db_query('delete from '.TABLE_OPTION_ITEM.' where group_id = \''.$_GET['group_id'].'\''); 
        tep_db_query('update `'.TABLE_PRODUCTS.'` set `belong_to_option` = \'\' where `belong_to_option` = \''.$_GET['group_id'].'\''); 
        $param_str = substr(tep_get_all_get_params(array('group_id', 'action')), 0, -1);
        tep_redirect(tep_href_link(FILENAME_OPTION_GROUP, $param_str)); 
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
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script text="text/javascript">
function create_option_group()
{
  $.ajax({
    url: 'ajax_orders.php?action=new_group',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      $('#show_group_info').html(data); 
      $('#show_group_info').show(); 
    }
  });
}

function check_group_info(gid, type)
{
  
  var group_name = document.getElementById('name').value; 
  var group_title = document.getElementById('title').value; 
  $.ajax({
    url: 'ajax_orders.php?action=check_group',
    type: 'POST',
    dataType: 'text',
    data:'type='+type+'&gname='+group_name+'&gid='+gid+'&gtitle='+group_title, 
    async:false,
    success: function (data){
      var error_arr = data.split('||'); 
      $('#name_error').html(error_arr[0]); 
      $('#title_error').html(error_arr[1]); 
      if (data == '||') {
        document.forms.option_group.submit(); 
      }
    }
  });
  
}

function close_group_info()
{
  $('#show_group_info').html(''); 
  $('#show_group_info').hide(); 
}

function show_group_info(ele, gid, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax_orders.php?action=edit_group',      
    data: 'group_id='+gid+'&'+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_group_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.body.scrollHeight) {
          offset =
          ele.offsetTop+$('#group_list_box').position().top-$('#show_group_info').height()-$('#offsetHeight').height();
          $('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        }
      } else {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#group_list_box').position().top-$('#show_group_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        }
      }
      $('#show_group_info').show(); 
    }
  });
}

function show_link_group_info(gid, k_str)
{
  $.ajax({
    url: 'ajax_orders.php?action=edit_group',
    data:'group_id='+gid+'&keyword='+k_str, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
    $('#show_group_info').html(data); 
      $('#show_group_info').show(); 
    }
  });
}

$(function() {
      function format(group) {
          return group.name;
      }
      $("#keyword").autocomplete('ajax_orders.php?action=search_group', {
        multipleSeparator: '',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format(item);
        }
      }).result(function(e, item) {
      });
});
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right">
              <div id="show_group_info" style="display:none;"></div> 
              <div>
              <?php echo tep_draw_form('form', FILENAME_OPTION_GROUP, '', 'get');?> 
              <input type="text" name="keyword" id="keyword">
              <input type="hidden" name="search" value="1">
              <?php echo tep_html_element_submit(IMAGE_SEARCH);?>
              </form> 
              </div>      
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading" align="left"><?php echo HEADING_TITLE; ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <table id="group_list_box" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_GROUP_NAME; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_GROUP_TITLE; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_GROUP_IS_PREORDER; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_GROUP_SORT_NUM; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_OPTION_GROUP_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    if (isset($_GET['search'])) {
      if ($_GET['search'] == '2') {
        $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name = \''.tep_replace_full_character($_GET['keyword']).'\' order by sort_num,name asc';
      } else {
        $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name like \'%'.tep_replace_full_character($_GET['keyword']).'%\' order by sort_num,name asc';
      }
    } else {
      $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' order by sort_num,name asc';
    }
    
    $group_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $group_query_raw, $group_query_numrows);
    $group_query = tep_db_query($group_query_raw);
    while ($group = tep_db_fetch_array($group_query)) {
      $rows++;
      
      if ( ((!isset($_GET['group_id']) || !$_GET['group_id']) || ($_GET['group_id'] == $group['id'])) && (!isset($selected_item) || !$selected_item) && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $group;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($selected_item) && is_array($selected_item)) && ($group['id'] == $selected_item['id']) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
      } else {
        echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'">' . "\n";
      }
?>
                <td align="left" class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_GROUP, 'page='.$_GET['page'].'&group_id=' .  $group['id']);?>'">
                <a href="<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'group_id='.$group['id'].(isset($_GET['page'])?'&gpage='.$_GET['page']:''));?>"><?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
                <?php echo '&nbsp;' . $group['name']; ?>
                </td>
                <td align="left" class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_GROUP, 'page='.$_GET['page'].'&group_id=' .  $group['id']);?>'"><?php echo '&nbsp;' . $group['title']; ?></td>
                <td align="left" class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_GROUP, 'page='.$_GET['page'].'&group_id=' .  $group['id']);?>'">
                <?php
                if ($group['is_preorder']) {
                  echo OPTION_GROUP_IS_PREORDER; 
                } else {
                  echo OPTION_GROUP_IS_NOT_PREORDER; 
                }
                ?>
                </td>
                <td class="dataTableContent" align="left" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_GROUP, 'page='.$_GET['page'].'&group_id=' .  $group['id']);?>'"><?php echo '&nbsp;' . $group['sort_num']; ?></td>
                
                <td class="dataTableContent" align="right">
<?php
echo '<a href="javascript:void(0);" onclick="show_group_info(this, \''.$group['id'].'\', \''.urlencode(tep_get_all_get_params(array('group_id', 'action'))).'\')">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;
    </td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $group_split->display_count($group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP); ?></td>
                    <td class="smallText" align="right"><?php echo $group_split->display_links($group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'group_id'))); ?></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right" class="smallText">
                    <?php 
                    echo '&nbsp;<a href="javascript:void(0);" onclick="create_option_group();">' .tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick=""') . '</a>'; 
                    ?>
                    &nbsp;
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </td></tr></table>
     </td></tr></table>
    </div></div>
    <!-- body_text_eof -->

</td></tr></table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
