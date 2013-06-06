<?php
/*
  $Id$
*/
require('includes/application_top.php');
require(DIR_FS_ADMIN . '/classes/notice_box.php');

if (!isset($_GET['gID'])) {
  $first_configuration_group_query = tep_db_query("select * from ".TABLE_CONFIGURATION_GROUP." where visible = '1' order by sort_order limit 1"); 
  $first_configuration_group = tep_db_fetch_array($first_configuration_group_query);
  tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID='.$first_configuration_group['configuration_group_id']));
}
if (isset($_GET['action']) && $_GET['action']) {
if(isset($_GET['cID'])){
  if (!is_numeric($_GET['cID'])) {
    $tmp_cid_info = explode('_',$_GET['cID']);
    $tmp_cid = $tmp_cid_info[0];
  } else {
    $tmp_cid = $_GET['cID']; 
  }
  $tmp_gid = $_GET['gID']; 
  $cfg_isset_query = tep_db_query("
      select configuration_id from ".TABLE_CONFIGURATION." 
      where configuration_group_id = '" . $tmp_gid . "' 
      and configuration_id = '" . $tmp_cid . "'");
  $cfg_isset = tep_db_fetch_array($cfg_isset_query);
  if ($_GET['action'] != 'tdel') {
    forward404Unless($cfg_isset);
  }
}
if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
    switch  ($_GET['action']) {
/*----------------------------------------
 case 'save' 判断该配置是添加或者更新
 case 'tdel' 删除配置
 case 'edit' 修改配置
 ---------------------------------------*/
    case 'save': 
        $configuration_value = tep_db_prepare_input($_POST['configuration_value']);
        $cID = tep_db_prepare_input($_GET['cID']);
        if (!is_numeric($cID))
      {
    $exploded_cid = explode('_',$cID);
    $site_id = $exploded_cid[1];
  forward401Unless(editPermission($site_arr, $site_id));//权限不够 跳到401
    $config_id = $exploded_cid[0];
    $upfile_name = $_FILES["upfile"]["name"];
    $upfile = $_FILES["upfile"]["tmp_name"];
    if(file_exists($upfile)){
        $path = DIR_FS_CATALOG . DIR_WS_IMAGES . $upfile_name;
        move_uploaded_file($upfile, $path);
        $configuration_value = tep_db_input($upfile_name);
    }
    tep_db_query(
        "insert into  " . TABLE_CONFIGURATION . " (
`configuration_title`, 
`configuration_key`, 
`configuration_value`, 
`configuration_description`, 
`configuration_group_id`, 
`sort_order`, 
`last_modified`, 
`date_added`, 
`use_function`, 
`set_function`, 
`site_id` ,`user_added`,`user_update`)
 SELECT 
`configuration_title`, 
`configuration_key`, '".
        $configuration_value."',
`configuration_description`, 
`configuration_group_id`, 
`sort_order`, 
now(),
now(),
`use_function`, 
`set_function`, '".
        $site_id.
        "','".$_SESSION['user_name']."','".$_SESSION['user_name']."' FROM ".TABLE_CONFIGURATION." 
WHERE
`configuration_id` = ".$config_id);

        tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&cID=' . $config_id.'&action=edit&site_id='.$site_id));
      }
 $site_id= tep_get_conf_sid_by_id($cID);
        if($site_id['site_id'])    forward401Unless(editPermission($site_arr, $site_id['site_id']));//权限不够 跳到401
  //只在图像上传的时候
        $upfile_name = $_FILES["upfile"]["name"];
        $upfile = $_FILES["upfile"]["tmp_name"];
  if(file_exists($upfile)){
      $path = DIR_FS_CATALOG . DIR_WS_IMAGES . $upfile_name;
      move_uploaded_file($upfile, $path);
      $configuration_value = tep_db_input($upfile_name);
      tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($upfile_name) . "', last_modified = now(),user_update='".$_SESSION['user_name']."' where configuration_id = '" . tep_db_input($cID) . "'");
  }
  
  $exists_configuration_raw = tep_db_query("select configuration_key from ".TABLE_CONFIGURATION." where configuration_id = '".$cID."'");       
  $exists_configuration_res = tep_db_fetch_array($exists_configuration_raw);
  $signal_single = false; 
  if ($exists_configuration_res) {
    if ($exists_configuration_res['configuration_key'] == 'DS_ADMIN_SIGNAL_TIME') {
      $signal_single = true; 
    }
  }
  if ($signal_single) {
    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" .  tep_db_input(serialize($configuration_value)) . "', last_modified = now(),user_update='".$_SESSION['user_name']."' where configuration_id = '" . tep_db_input($cID) . "'");
  } else {
    if($hidden_configuration_value == null && $configuration_value != null){
      $update_user_added = 'user_added=\''.$_SESSION['user_name'].'\',date_added=now(),';  
    }
    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" .  tep_db_input($configuration_value) . "',".$update_user_added." last_modified = now(),user_update='".$_SESSION['user_name']."' where configuration_id = '" . tep_db_input($cID) . "'");
  }
  tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' .  tep_get_default_configuration_id_by_id($cID).'&site_id='.$site_id['site_id']));
  break;
    case 'tdel':
  $site_id= tep_get_conf_sid_by_id($cID);
  $two_id = explode('_',$_GET['cID']);
  $config_id =$two_id[0];
  $default_id = $two_id[1];

  tep_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_id = ".$config_id);
        tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&cID=' . $default_id.'&action=edit&site_id='.$site_id['site_id']));
break;


    }
}

//取得了title
$cfg_group_query = tep_db_query("
      select configuration_group_title 
      from " . TABLE_CONFIGURATION_GROUP . " 
      where   configuration_group_id = '" . $_GET['gID'] . "'
  ");
$cfg_group = tep_db_fetch_array($cfg_group_query);
forward404Unless($cfg_group);
if(isset($_GET['cID'])){
  $cfg_isset_query = tep_db_query("
      select configuration_id from ".TABLE_CONFIGURATION." 
      where configuration_group_id = '" . $_GET['gID'] . "' 
      and  configuration_id = '" . $_GET['cID'] . "'
      and site_id='0'");
  $cfg_isset = tep_db_fetch_array($cfg_isset_query);
  forward404Unless($cfg_isset);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo constant('HEADING_TITLE_'.intval($_GET['gID'])); ?>
</title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
    <script language="javascript" src="includes/javascript/jquery_include.js"></script>
    <script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" >
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_text_configuration').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_text_configuration').css('display') != 'none') {
               $("#show_text_configuration").find('input:submit').first().trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_text_configuration').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_text_configuration').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_text_configuration(ele,gID,cID,site_id){
 $.ajax({
 url: 'ajax.php?&action=edit_configuration',
 data: {gID:gID,cID:cID,site_id:site_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_text_configuration").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_text_configuration').height()){
offset = ele.offsetTop+$("#show_configuration_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_text_configuration').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_configuration_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_text_configuration').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_text_configuration').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
}
$('#show_text_configuration').css('z-index','1');
$('#show_text_configuration').css('left',leftset);
$('#show_text_configuration').css('display', 'block');
  }
  }); 
}
function hidden_info_box(){
$('#show_text_configuration').css('display','none');
}
<?php //是否输入一次性密码?>
function update_configuration_info(c_permission)
{
  if (c_permission == 31) {
    document.forms.configuration.submit();
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
          document.forms.configuration.submit();
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.configuration.action),
              async: false,
              success: function(msg_info) {
                document.forms.configuration.submit();
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
<?php //无效设置?>
function set_invalid_configuration(c_permission, gid_info, cid_info)
{
  if (c_permission == 31) {
    window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_CONFIGURATION;?>'+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
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
          window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_CONFIGURATION;?>'+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_CONFIGURATION;?>'+'?action=tdel&gID='+gid_info+'&cID='+cid_info),
              async: false,
              success: function(msg_info) {
                window.location.href = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_CONFIGURATION;?>'+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
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
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/gID=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
    <!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof -->

    <!-- body -->
    <input type="hidden" name="show_info_id" value="show_text_configuration" id="show_info_id">
    <div id="show_text_configuration" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
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
          <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2"></td>
       </tr>
       <tr>
          <td>
             <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                   <td class="pageHeading"><?php echo constant($cfg_group ['configuration_group_title']); ?></td>
                   <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                </tr>
             </table>
          </td>
       </tr>
       <tr>
          <td>
             <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                   <td valign="top">
                   <?php echo tep_site_filter(FILENAME_CONFIGURATION, true);?>
                    <?php 
                     $configuration_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0','parameters'=>'id="show_configuration_list"');
                     $notice_box = new notice_box('','',$configuration_table_params);
                     $configuration_table_row = array();
                     $configuration_title_row = array();
                     $configuration_title_row[] = array('params' => 'class="dataTableHeadingContent" width="40%"', 'text' => TABLE_HEADING_CONFIGURATION_TITLE);
                     $configuration_title_row[] = array('params' => 'class="dataTableHeadingContent" width="40%"', 'text' => TABLE_HEADING_CONFIGURATION_VALUE);
                     $configuration_title_row[] = array('params' => 'class="dataTableHeadingContent" width="10%"', 'text' => FOREGROUND_TO_BACKGROUND);
                     $configuration_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right" width="10%"', 'text' => TABLE_HEADING_ACTION);
                     $configuration_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $configuration_title_row);
                    ?> 

<?php
                          //ccdd  只先默认的值 
$configuration_query = tep_db_query("
select 
    configuration_id, 
    configuration_title, 
    configuration_key, 
    configuration_value, 
    use_function,
    last_modified
from " . TABLE_CONFIGURATION . " 
where 
    configuration_group_id = '" . $_GET['gID'] . "' 
    and 
    `site_id` = '0'  order by sort_order"
               );
while ($configuration = tep_db_fetch_array($configuration_query)) {
  if(empty($_GET['site_id'])) {
    $_GET['site_id'] = 0;
  }
  $configuration_key_one = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = '".$configuration['configuration_key']."' and `site_id` = '".$_GET['site_id']."'");
  $configuration_key_row = tep_db_fetch_array($configuration_key_one);
  if($configuration['configuration_key'] != 'DS_ADMIN_SIGNAL_TIME'){
  $configuration['configuration_value'] = $configuration_key_row['configuration_value'];
  }
    if (tep_not_null($configuration['use_function'])) {
  $use_function = $configuration['use_function'];
   if (ereg('->', $use_function)) {
      $class_method = explode('->', $use_function);
      if (!is_object(${$class_method[0]})) {
    include(DIR_WS_CLASSES . $class_method[0] . '.php');
    ${$class_method[0]} = new $class_method[0]();
      }
      $cfgValue = tep_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
  } else {
      $cfgValue = tep_call_function($use_function, $configuration['configuration_value']);
  }
    } else {
  $cfgValue = $configuration['configuration_value'];
    }

    if (
        ((!isset($_GET['cID']) || !$_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) 
        && (!isset($cInfo) || !$cInfo) 
        && (!isset($_GET['action']) or substr($_GET['action'], 0, 3) != 'new')
    ) {
  $cfg_extra_query = tep_db_query("select  configuration_key, configuration_description, date_added, last_modified, use_function, set_function,user_added,user_update from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
  $cfg_extra= tep_db_fetch_array($cfg_extra_query);
  $cInfo_array = tep_array_merge($configuration, $cfg_extra);
  $cInfo = new objectInfo($cInfo_array);
    }
  $even = 'dataTableSecondRow';
  $odd  = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even; 
  } else {
    $nowColor = $odd; 
  }
    if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
   $configuration_params =  'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
   $configuration_params =  'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
    $configuration_info = array();
    if(constant($configuration['configuration_title']) == null){
      $configuration_title_constant = $configuration['configuration_title'];
    }else{
      $configuration_title_constant = constant($configuration['configuration_title']);
    }
    $configuration_title_image = explode("F_",$configuration_title_constant);
    $configuration_info[] = array( 
        'params' => 'class="dataTableContent" ',
        'text'   => str_replace('&nbsp;',' ',$configuration_title_image[1])
        );
    if ($configuration['configuration_key'] == 'DS_ADMIN_SIGNAL_TIME') {
      $tmp_setting_array = @unserialize(stripslashes($cfgValue));
       $configuration_key = '';
       $configuration_key .= SIGNAL_GREEN.':'.(int)($tmp_setting_array['green'][0].$tmp_setting_array['green'][1].$tmp_setting_array['green'][2].$tmp_setting_array['green'][3]);
       $configuration_key .= SIGNAL_YELLOW.':'.(int)($tmp_setting_array['yellow'][0].$tmp_setting_array['yellow'][1].$tmp_setting_array['yellow'][2].$tmp_setting_array['yellow'][3]);
       $configuration_key .= SIGNAL_RED.':'.(int)($tmp_setting_array['red'][0].$tmp_setting_array['red'][1].$tmp_setting_array['red'][2].$tmp_setting_array['red'][3]);
    } else {
       $configuration_key = mb_substr(htmlspecialchars($cfgValue),0,50); 
    }
    $configuration_info[] = array(
        'params' => 'class="dataTableContent" ',
        'text'   =>  $configuration_key
        );
    $configuration_info[] = array(
        'params' => 'class="dataTableContent" ',
        'text'   => $configuration_title_image[0] 
        );
 
   $configuration_date_info = (tep_not_null($configuration_key_row['last_modified']) && ($configuration_key_row['last_modified'] != '0000-00-00 00:00:00'))?$configuration_key_row['last_modified']:$configuration_key_row['date_added'];
   $configuration_info[] = array(
        'params' => 'class="dataTableContent" align="right"',
        'text'   => '<a href="javascript:void(0)" onclick="show_text_configuration(this,'.$_GET['gID'].','.$configuration['configuration_id'].','.$_GET['site_id'].');">'.tep_get_signal_pic_info($configuration_date_info).'</a>'
        );
    $configuration_table_row[] = array('params' => $configuration_params, 'text' => $configuration_info);
}
$notice_box->get_contents($configuration_table_row);
$notice_box->get_eof(tep_eof_hidden());
echo $notice_box->show_notice();
?>
</table></td>
          </tr>
        </table>
        </td>
      </tr>
    </table>
    </div></div></td>
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
