<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'setflag':
        tep_db_query("update `".TABLE_OPTION_ITEM."` set `status` = '".(int)$_GET['flag']."' where id = '".$_GET['item_id']."'"); 
        tep_redirect(tep_href_link(FILENAME_OPTION_ITEM, 'group_id='.$_GET['group_id']));  
        break;
      case 'update':
      case 'insert':
        $error = false;
        if (empty($_POST['front_title'])) {
          $error = true;
        }
        if (empty($_POST['title'])) {
          $error = true;
        }
        
        $option_array = array(); 
        if (isset($_POST['itext'])) {
          $option_array['itext'] = tep_db_prepare_input($_POST['itext']); 
        }
        
        if (isset($_POST['itextarea'])) {
          $option_array['itextarea'] = tep_db_prepare_input($_POST['itextarea']); 
        }
        
        if (isset($_POST['require'])) {
          $option_array['require'] = tep_db_prepare_input($_POST['require']); 
        }
        
        if (isset($_POST['dselect'])) {
          $d_str = substr($_POST['dselect'], 3); 
        } 
        
        foreach ($_POST as $pekey => $pevalue) {
          if (preg_match('/^(op_)\d{1,}/',$pekey)) {
            if (empty($pevalue)) {
              unset($_POST[$pekey]); 
            }
          } 
        } 
        
        $o_s_array = array();
        $o_num = 0; 
        
        foreach ($_POST as $pskey => $psvalue) {
          if (preg_match('/^(op_)\d{1,}/',$pskey)) {
            $o_str = substr($pskey, 3);
            if (isset($d_str)) {
              if ($o_str == $d_str) {
                $s_num = $o_num; 
              }
            }
            $o_s_array[] = $psvalue; 
            $o_num++; 
          }
        }
        
        if (!empty($o_s_array)) {
          $option_array['se_option'] = $o_s_array; 
        }
        
        if (isset($s_num)) {
          $option_array['se_num'] = $s_num; 
        }
       
        if (isset($_POST['secomment'])) {
          $option_array['secomment'] = $_POST['secomment']; 
        }
        
        if (isset($_POST['icomment'])) {
          $option_array['icomment'] = $_POST['icomment']; 
        }
        
        if (isset($_POST['iline'])) {
          if (is_numeric($_POST['iline'])) {
            if ($_POST['iline'] == 0) {
              $option_array['iline'] = 1; 
            } else {
              $option_array['iline'] = $_POST['iline']; 
            }
          } else {
            $option_array['iline'] = 1; 
          }
        }
       
        if (isset($_POST['icomment'])) {
          $option_array['ictype'] = $_POST['ictype']; 
        }

        if (isset($_POST['imax_num'])) {
          $option_array['imax_num'] = $_POST['imax_num']; 
        }
        
        if (!$error) {
          if ($_GET['action'] == 'update') {
            $option_array['eid'] = $_POST['item_id'];
            $update_sql = "update `".TABLE_OPTION_ITEM."` set `title` =
              '".tep_db_prepare_input($_POST['title'])."', `front_title` =
              '".tep_db_prepare_input($_POST['front_title'])."', `option` =
              '".tep_db_prepare_input(serialize($option_array))."', `type` =
              '".tep_db_prepare_input(strtolower($_POST['type']))."', `price` =
              '".tep_db_prepare_input($_POST['price'])."', `sort_num` =
              '".tep_db_prepare_input((int)$_POST['sort_num'])."' where id =
              '".$_POST['item_id']."'"; 
            tep_db_query($update_sql); 
          } else if ($_GET['action'] == 'insert') {
            $insert_sql = "insert into `".TABLE_OPTION_ITEM."` values(NULL,
              '".$_GET['group_id']."', '".tep_db_prepare_input($_POST['title'])."',
              '".tep_db_prepare_input($_POST['front_title'])."',
              '".tep_db_prepare_input(tep_get_random_option_item_name())."', '',
              '".tep_db_prepare_input(serialize($option_array))."',
              '".tep_db_prepare_input(strtolower($_POST['type']))."',
              '".tep_db_prepare_input($_POST['price'])."', '1',
              '".tep_db_prepare_input((int)$_POST['sort_num'])."', '".date('Y-m-d H:i:s',time())."')"; 
             tep_db_query($insert_sql); 
             $item_id = tep_db_insert_id(); 
             $option_array['eid'] = $item_id;
             tep_db_query("update `".TABLE_OPTION_ITEM."` set `option` = '".tep_db_prepare_input(serialize($option_array))."' where `id` = '".$item_id."'");
          }
        }
        tep_redirect(tep_href_link(FILENAME_OPTION_ITEM, 'group_id='.$_GET['group_id'])); 
        break; 
      case 'deleteconfirm':
        tep_db_query('delete from '.TABLE_OPTION_ITEM.' where id = \''.$_GET['item_id'].'\''); 
        tep_redirect(tep_href_link(FILENAME_OPTION_ITEM, 'group_id='.$_GET['group_id'])); 
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
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
<script text="text/javascript">
function create_option_item(gid)
{
  $.ajax({
    url: 'ajax_orders.php?action=new_item',
    data:'group_id='+gid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      $('#show_item_info').html(data); 
      $('#show_item_info').show(); 
    }
  });
}

function check_item_info()
{
  var item_title = document.getElementById('title').value; 
  var item_front_title = document.getElementById('front_title').value; 
  
  $.ajax({
      url: 'ajax_orders.php?action=check_item',
      type: 'POST',
      dataType: 'text',
      data:'ititle='+item_title+'&ifront_title='+item_front_title,
      async:false,
      success: function (data) {
        var error_arr = data.split('||');
        $('#title_error').html(error_arr[0]);
        $('#front_error').html(error_arr[1]);
        if (data == '||') {
          document.forms.option_item.submit(); 
        }
      }
      });
}

function close_item_info()
{
  $('#show_item_info').html(''); 
  $('#show_item_info').hide(); 
}

function show_item_info(ele, item_id, gid)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax_orders.php?action=edit_item',      
    data: 'group_id='+gid+'&item_id='+item_id,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_item_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.body.scrollHeight) {
          offset =
          ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height();
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      }
      $('#show_item_info').show(); 
    }
  });
}

function show_link_item_info(item_id, gid)
{
  $.ajax({
    url: 'ajax_orders.php?action=edit_item',
    data:'item_id='+item_id+'&group_id='+gid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
    $('#show_item_info').html(data); 
      $('#show_item_info').show(); 
    }
  });
}

function change_option_item_type(item_id)
{
  var stype = document.getElementById('type').value;
  $.ajax({
     url: 'ajax_orders.php?action=change_item',   
     data: 'item_id='+item_id+'&stype='+stype, 
     type: 'POST',
     async: false,
     success: function(msg) {
       $('#show_select').html(msg); 
     }
  });
}

function del_option_select(ele)
{
  $(ele).parent().parent().remove();    
}

function add_option_select()
{
  var i_num = 1; 
  var html_str = '';

  $('#show_select').find('input[type=text]').each(function(i) {
    i_num = parseInt($(this).attr('name').substring('3')); 
  });   
  for (i=1; i<=5 ; i++) {
    i_num_add = i_num+i; 
    html_str += '<tr><td><?php echo TEXT_OPTION_ITEM_SELECT;?></td><td><input type="text" name="op_'+i_num_add+'" value="">&nbsp;<input type="radio" name="dselect" value="dp_'+i_num_add+'"></td><td><a href="javascript:void(0);" onclick="del_option_select(this);"><?php echo TEXT_OPTION_ITEM_DEL_LINK;?></a></td></tr>';   
  }
  $('#add_select').parent().before(html_str);
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
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    //one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading">
              <?php
               $option_group_raw = tep_db_query("select name from ".TABLE_OPTION_GROUP." where id = '".$_GET['group_id']."'");  
               $option_group = tep_db_fetch_array($option_group_raw);
               echo $option_group['name'];
              ?>
              </td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <div id="show_item_info" style="display:none;"></div> 
        <div align="right">
        <?php echo tep_draw_form('form', FILENAME_OPTION_GROUP, '', 'get');?>
        <input type="text" name="keyword" id="keyword">
        <input type="hidden" name="search" value="1">
        <?php echo tep_html_element_submit(IMAGE_SEARCH);?>
        </form>
        </div>
        <table id="item_list_box" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php 
                echo TABLE_HEADING_OPTION_ITEM_NAME; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_TITLE; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_TYPE; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_REQUIRE; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_CONTENT; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_PRICE; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_SORT_NUM; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_OPTION_ITEM_STATUS; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_OPTION_ITEM_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    $item_query_raw = 'select * from '.TABLE_OPTION_ITEM.' where group_id = \''.$_GET['group_id'].'\' order by created_at desc';
    
    $item_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $item_query_raw, $item_query_numrows);
    $item_query = tep_db_query($item_query_raw);
    while ($item = tep_db_fetch_array($item_query)) {
      $rows++;
      
      if ( ((!isset($_GET['item_id']) || !$_GET['item_id']) || ($_GET['item_id'] == $item['id'])) && (!isset($selected_item) || !$selected_item) && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $item;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($selected_item) && is_array($selected_item)) && ($item['id'] == $selected_item['id']) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
      } else {
        echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'">' . "\n";
      }
?>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].'&item_id='.$item['id']);?>'">
                <?php echo '&nbsp;' . $item['title']; ?>
                </td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].'&item_id='.$item['id']);?>'"><?php echo '&nbsp;' . $item['front_title']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].'&item_id='.  $item['id']);?>'">
                <?php 
                if ($item['type'] == 'text') {
                  echo OPTION_ITEM_OPTION_TEXT_TYPE; 
                } else if ($item['type'] == 'textarea'){
                  echo OPTION_ITEM_OPTION_TEXTAREA_TYPE; 
                } else if ($item['type'] == 'select'){
                  echo OPTION_ITEM_OPTION_SELECT_TYPE; 
                }
                ?> 
                </td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].  '&item_id=' .$item['id']);?>'">
                <?php 
                $item_option = @unserialize($item['option']); 
                if (isset($item_option['require']) && $item_option['require'] == '1') {
                  echo TEXT_OPTION_ITEM_IS_REQUIRE; 
                } else {
                  echo TEXT_OPTION_ITEM_IS_NOT_REQUIRE; 
                }
                ?>
                </td>
                
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].  '&item_id=' .$item['id']);?>'">
                <?php 
                if (isset($item_option['itext'])) {
                  echo $item_option['itext']; 
                } else if (isset($item_option['itextarea'])){
                  echo $item_option['itextarea']; 
                } else if (isset($item_option['se_option'])) {
                  if (is_array($item_option['se_option'])) {
                    if (!empty($item_option['se_option'])) {
                      foreach ($item_option['se_option'] as $sokey => $sovalue) {
                        echo $sovalue.'&nbsp;'; 
                      }
                    }
                  }
                }
                ?>
                </td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].  '&item_id=' .$item['id']);?>'"><?php echo '&nbsp;' .  $currencies->format($item['price']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].  '&item_id=' .$item['id']);?>'"><?php echo '&nbsp;' .  $item['sort_num']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_OPTION_ITEM, 'page='.$_GET['page'].'&group_id=' .$_GET['group_id'].  '&item_id=' .$item['id']);?>'">
                <?php
                if ($item['status'] == '1') {
                  echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' .  tep_href_link(FILENAME_OPTION_ITEM, 'action=setflag&flag=0&item_id=' .  $item['id'].'&group_id='.$_GET['group_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                } else {
                  echo '<a href="' . tep_href_link(FILENAME_OPTION_ITEM, 'action=setflag&flag=1&item_id=' .  $item['id'].'&group_id='.$_GET['group_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                }
                ?>
                </td>
                
                <td class="dataTableContent" align="right">
<?php
      echo '<a href="javascript:void(0);" onclick="show_item_info(this, \''.$item['id'].'\', \''.$_GET['group_id'].'\')">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;
    </td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $item_split->display_count($item_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $item_split->display_links($item_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'item_id'))); ?></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right" class="smallText">
                    <?php 
                    echo '&nbsp;<a href="'.tep_href_link(FILENAME_OPTION_GROUP, 'group_id='.$_GET['group_id']).'">' .tep_html_element_button(IMAGE_BACK) . '</a>'; 
                    echo '&nbsp;<a href="javascript:void(0);" onclick="create_option_item(\''.$_GET['group_id'].'\');">' .tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick=""') . '</a>'; 
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
    </table></td>
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
