<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require('includes/languages/japanese/news.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
                        $sql_site_where = 'c.site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
                        $show_list_array = explode('-',$_GET['site_id']);
                } else {
                        $show_list_str = tep_get_setting_site_info(FILENAME_CAMPAIGN);
                        $sql_site_where = 'c.site_id in ('.$show_list_str.')';
                        $show_list_array = explode(',',$show_list_str);
                        $show_list_str1 = strtr($show_list_str,',','-');
                }
  if(isset($_GET['campaign_id'])&&$_GET['campaign_id']){
    $c_id = tep_db_prepare_input($_GET['campaign_id']);
  }else if(isset($_POST['campaign_id'])&&$_POST['campaign_id']){
    $c_id = tep_db_prepare_input($_POST['campaign_id']);
  }
  if (isset($_GET['action']) && $_GET['action']) {
	if(isset($_GET['st_id'])){
		$show_list_str2 = $_GET['st_id'];
	}else{
		$show_list_str2 = $show_list_str1;
	}
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'setflag' 设置优惠券状态   
   case 'update' 更新优惠券    
   case 'insert' 新建优惠券    
   case 'deleteconfirm' 删除优惠券    
------------------------------------------------------*/
      case 'setflag';
        tep_db_query("update `".TABLE_CAMPAIGN."` set `status` = '".(int)$_GET['flag']."' where id = '".$_GET['campaign_id']."'");
        tep_redirect(tep_href_link(FILENAME_CAMPAIGN, isset($_GET['site_id'])?('site_id='.$_GET['site_id'].'&sort='.$_GET['sort'].'&page='.$_GET['page'].'&type='.$_GET['type']):$show_list_str2.'&sort='.$_GET['sort'].'&page='.$_GET['page'].'&type='.$_GET['type'])); 
        break;
      case 'update':
      case 'insert':
        $error = false;
        if (empty($_POST['title'])) {
          $error = true;
          $title_error = TEXT_CAMPAIGN_TITLE_IS_NULL;
        }
        if (empty($_POST['name'])) {
          $error = true;
          $name_error = TEXT_CAMPAIGN_NAME_IS_NULL;
        }
        
        if (empty($_POST['keyword'])) {
          $error = true;
          $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
        } else {
          if (!preg_match('/^[0-9a-zA-Z]+$/', $_POST['keyword'])) {
            $error = true;
            $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
          } else {
            if (preg_match('/^[0-9]+$/', $_POST['keyword'])) {
              $error = true;
              $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
            } else {
              if ($_GET['action'] == 'update') {
                $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."' and id != '".$_POST['campaign_id']."'"); 
              } else {
                $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."'"); 
              }
              if (tep_db_num_rows($exists_cam_raw)) {
                $error = true;
                $keyword_error = TEXT_CAMPAIGN_KEYWORD_EXISTS;
              }
            }
          }
        }
        if (!preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday']) || !preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday'])) {
          $error = true;
          $date_error = TEXT_CAMPAIGN_DATE_WRONG;
        } else {
          $start_time = @strtotime($_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday'].' 00:00:00'); 
          $end_time = @strtotime($_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday'].' 00:00:00'); 
          if ($start_time > $end_time) {
            $error = true;
            $date_error = TEXT_CAMPAIGN_DATE_WRONG;
          }
        }
        if (!$error) {
         if ($_POST['type'] == 2) {
           $limit_value = 0-$_POST['limit_value']; 
         } else {
           $limit_value = $_POST['limit_value']; 
         }
         $percent_pos = strpos($_POST['point_value'], '%');
         if ($percent_pos !== false) {
           if($_POST['up_or_down'] == '+'){
              $point_value = $_POST['point_value']; 
           }else {
              $point_value = '-'.$_POST['point_value']; 
           }
         } else {
           if($_POST['up_or_down'] == '+'){
              $point_value = $_POST['point_value']; 
           }else{
              $point_value = 0-$_POST['point_value']; 
           }
         }
         $sql_data_array = array(
            'title' => tep_db_prepare_input($_POST['title']),
            'name' => tep_db_prepare_input($_POST['name']),
            'keyword' => tep_db_prepare_input($_POST['keyword']),
            'is_preorder' => tep_db_prepare_input($_POST['is_preorder']),
            'start_date' => tep_db_prepare_input($_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday']),
            'end_date' => tep_db_prepare_input($_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday']),
            'max_use' => tep_db_prepare_input($_POST['max_use']),
            'point_value' => tep_db_prepare_input($point_value),
            'limit_value' => tep_db_prepare_input($limit_value),
            'type' => tep_db_prepare_input($_POST['type']),
            'range_type' => tep_db_prepare_input($_POST['range_type']),
            );
         if($_GET['action']=='update'){
           $update_sql_date = array(
               'user_update' => $_SESSION['user_name'],
               'date_update' => 'now()'
               );
           $sql_data_array = tep_array_merge($sql_data_array,$update_sql_date);
           tep_db_perform(TABLE_CAMPAIGN, $sql_data_array, 'update', 'id = \'' .  $_POST['campaign_id']. '\' and site_id = \''.(int)$_GET['site_id'].'\'');
        }else if($_GET['action']=='insert'){
          $insert_sql_data = array(
              'created_at' => 'now()',
              'status' => '1',
              'site_id' => tep_db_prepare_input($_GET['site_id']),
              'user_added' => $_SESSION['user_name'], 
              'user_update'=> $_SESSION['user_name'],
              'date_update'=> 'now()'
              );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_CAMPAIGN, $sql_data_array);
        }
        if (isset($_GET['st_id'])) {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.$show_list_str2).'&sort='.$_GET['sort'].'&page='.$_GET['page'].'&type='.$_GET['type']);
        } else {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.$show_list_str2).'&sort='.$_GET['sort'].'&page='.$_GET['page'].'&type='.$_GET['type']);
        }
        }
        if ($_GET['action'] == 'update') {
          $_GET['action'] = 'edit_campaign'; 
        } else {
          $_GET['action'] = 'new_campaign'; 
        }
        break; 
      case 'deleteconfirm':
        tep_db_query('delete from '.TABLE_CAMPAIGN." where id = '".$_GET['campaign_id']."'"); 
        if (empty($_GET['site_id'])) {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN));
        } else {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.$_GET['site_id']));
        }
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
<script style="text/javascript">
var bw_height;
var client_height;
var scrollHeight;
$(document).ready(function() { 
 bw_height = $('.box_warp').height();
});
<?php //弹出页的前页/后页的链接?>
function show_link_campaign_info(cid, sid)
{
  $.ajax({
    url: 'ajax_orders.php?action=edit_campaign',     
    data:'cid='+cid+'&st_id='+sid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data); 
      $('#show_campaign_info').show(); 
    }
  });
}
var temp_id = '';
<?php //更新优惠券弹出页面?>
function show_campaign_info(ele, cid, sid)
{
  temp_id = cid;
  ele = ele.parentNode; 
  $.ajax({
    url: 'ajax_orders.php?<?php echo "sort=".$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page']?>&action=edit_campaign',     
    data:'cid='+cid+'&st_id='+sid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight+$('#show_campaign_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height();
          div_height =  $('#show_campaign_info').height();
          if((div_height+offset)>$('.box_warp').height()){
            $('.box_warp').height(div_height+offset);
          }else if ((div_height+offset)>bw_height){
            $('.box_warp').height(bw_height);
          }
          $('#show_campaign_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight;
          div_height =  $('#show_campaign_info').height();
          if((div_height+offset)<$('.box_warp').height()){
            if ((div_height+offset)>bw_height){
              $('.box_warp').height(div_height+offset);
            }else{
              $('.box_warp').height(bw_height);
            }
	  }else{
            $('.box_warp').height(div_height+offset);
	  }
          t_offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height();
          min_height = ($('#tep_site_filter').height()+$('.pageHeading').height());
          if ((div_height+offset)>bw_height&&t_offset>min_height){
            offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height();
          }
          $('#show_campaign_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight+$('#show_campaign_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height();
          div_height =  $('#show_campaign_info').height();
          if((div_height+offset)>$('.box_warp').height()){
            $('.box_warp').height(div_height+offset);
          }else if ((div_height+offset)>bw_height){
            $('.box_warp').height(bw_height);
          }
          $('#show_campaign_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight;
          div_height =  $('#show_campaign_info').height();
          if((div_height+offset)>$('.box_warp').height()){
            $('.box_warp').height(div_height+offset);
          }else if ((div_height+offset)>bw_height){
            $('.box_warp').height(div_height+offset);
          }
          $('#show_campaign_info').css('top', offset).show(); 
        }
      }
      $('#show_campaign_info').show(); 
    }
  });
}
window.onresize = show_campaign_info_offset; 
<?php //浏览器窗口缩放的函数?>
function show_campaign_info_offset(){
   var show_value = '';
   var box_warp = '';
   var box_warp_top = 0;
   var box_warp_left = 0;
   if(temp_id != ''){
       if($(".box_warp").offset()){
          box_warp = $(".box_warp").offset();
          box_warp_top = box_warp.top;
          box_warp_left = box_warp.left;
      }
   show_value = $("#show_value_" + temp_id).offset();
   $("#show_campaign_info").css('top',show_value.top+$("#show_value_" + temp_id).height()-box_warp_top);
   $("#show_campaign_info").css('left',show_value.left-box_warp_left);
 }
}
<?php //关闭弹出框?>
function close_campaign_info()
{
  $('#show_campaign_info').html('');  
  $('#show_campaign_info').hide(); 
}
<?php //新建优惠券弹出框?>
function show_new_campaign(std)
{
  $.ajax({
    url: 'ajax_orders.php?<?php echo "sort=".$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page']?>&action=new_campaign',     
    data: 'site_id='+std, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data);  
      $('#show_campaign_info').show(); 
    }
  });
}
<?php //检查信息是否正确?>
function check_campaign_info(cid, check_type, site_id)
{
   var chkObjs = document.getElementsByName("type");
   var type_value = 1; 
   for(var i=0;i<chkObjs.length;i++){
     if(chkObjs[i].checked){
       type_value = chkObjs[i].value; 
       break; 
     }
   }
   $.ajax({
    url: 'ajax_orders.php?action=check_campaign',     
    data:'title='+document.getElementById('title').value+'&name='+document.getElementById('name').value+'&keyword='+document.getElementById('keyword').value+'&syear='+document.getElementById('syear').value+'&smonth='+document.getElementById('smonth').value+'&sday='+document.getElementById('sday').value+'&eyear='+document.getElementById('eyear').value+'&emonth='+document.getElementById('emonth').value+'&eday='+document.getElementById('eday').value+'&max_use='+document.getElementById('max_use').value+'&point_value='+document.getElementById('point_value').value+'&limit_value='+document.getElementById('limit_value').value+'&type='+type_value+'&check='+check_type+'&site_id='+site_id+'&campaign_id='+cid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      var error_arr = data.split('|||'); 
      $('#title_error').html(error_arr[0]); 
      $('#name_error').html(error_arr[1]); 
      $('#keyword_error').html(error_arr[2]); 
      $('#date_error').html(error_arr[3]); 
      $('#max_use_error').html(error_arr[4]); 
      $('#point_value_error').html(error_arr[5]); 
      $('#limit_value_error').html(error_arr[6]); 
      if (data == '||||||||||||||||||') {
        <?php
        if ($ocertify->npermission == 31) {
        ?>
        document.forms.campaign.submit(); 
        <?php
        } else {
        ?>
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
              document.forms.campaign.submit(); 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.campaign.action),
                  async: false,
                  success: function(msg_info) {
                    document.forms.campaign.submit(); 
                  }
                }); 
              } else {
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              }
            }
          }
        });
        <?php 
        }
        ?>
      }
    }
  });
}
<?php //切换动作?>
function toggle_type_info(ele)
{
  if (ele.value == '1') {
    document.getElementById('type_symbol').innerHTML = '+'; 
  } else {
    document.getElementById('type_symbol').innerHTML = '-'; 
  }
}
<?php //执行动作?>
function change_show_site(site_id,flag,site_list,param_url,current_file){
var ele = document.getElementById("site_"+site_id);
var unshow_list = document.getElementById("unshow_site_list").value;
var last_site_list = document.getElementById("show_site_id").value;
$.ajax({
dataType: 'text',
type:"POST",
data:'param_url='+param_url+'&flag='+flag+'&site_list='+site_list+'&site_id='+site_id+'&current_file='+current_file+'&unshow_list='+unshow_list+'&last_site_list='+last_site_list,
async:false,
url: 'ajax_orders.php?action=select_all_site',
success: function(msg) {
if (msg != '') {
if (ele.className == 'site_filter_selected') {
ele.className='';
} else {
ele.className='site_filter_selected';
}
window.location.href = msg;
}
}
});
}
function toggle_campaign_action(c_permission, action_url_str)
{
  if (c_permission == 31) {
    window.location.href = action_url_str; 
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
          window.location.href = action_url_str; 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(action_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = action_url_str; 
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
        <div id="show_campaign_info" style="display:none;"></div>
        <?php tep_show_site_filter('campaign.php');?>
        <table id="campaign_list_box" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
	<?php
if(isset($_GET['sort'])){
	if($_GET['sort'] == 'site_id' || $_GET['sort'] == 'name' || $_GET['sort'] == 'title' || $_GET['sort'] == 'end_date' || $_GET['sort'] == 'keyword' || $_GET['sort'] == 'max_use' 	    || $_GET['sort'] == 'point_value' || $_GET['sort'] == 'cnt' || $_GET['sort'] == 'status' || $_GET['date_update']){
		
		$sort = $_GET['sort'];
	}else{
		$sort = 'created_at';
	}
    }else{
	$sort = 'created_at';
    }
    $present_type_name = '';
    if(isset($_GET['type'])){
	if($_GET['type'] == 'desc' || $_GET['type'] == 'asc'){
		$type = $_GET['type'];
	}else{
		$type = 'desc';
	}
	if($_GET['type'] == 'desc'){
        	$present_type_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
		$re_type = 'asc';
        }else{
        	$present_type_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
		$re_type = 'desc';
        }	
    }else{
	$type = 'desc';
    }
 
		$present_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
		$notice_box = new notice_box('','',$present_table_params);
		$present_table_row = array();
		$present_title_row = array();
		$present_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_present(\'presen     t_id[]\');">');
		if(isset($_GET['sort']) && $_GET['sort'] == 'site_id'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=site_id&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_SITE.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=site_id&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_SITE.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=title&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_TITLE.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=title&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_TITLE.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'name'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=name&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_NAME.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=name&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_NAME.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'keyword'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=keyword&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_KEYWORD.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=keyword&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_KEYWORD.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'end_date'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=end_date&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_EFFECT_DATE.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="left"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=end_date&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_EFFECT_DATE.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'max_use'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=max_use&page='.$_GET['page'].'&type='.$re_type).'">'.TABLE_HEADING_CAMPAIGN_USE_NUM.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=max_use&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_USE_NUM.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'point_value'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=point_value&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_VALUE.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=point_value&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_VALUE.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'status'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=status&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_STATUS.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=status&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_STATUS.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'cnt'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=cnt&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_FETCH_USE_NUM.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=cnt&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_FETCH_USE_NUM.'</a>');
                }
		if(isset($_GET['sort']) && $_GET['sort'] == 'date_update'){
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=date_update&page='.$_GET['page'].'&type='.$re_type.'&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_ACTION.$present_type_name.'</a>');
                }else{
                $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CAMPAIGN,'sort=date_update&page='.$_GET['page'].'&type=desc&site_id='.$_GET['site_id']).'">'.TABLE_HEADING_CAMPAIGN_ACTION.'</a>');
                }
		$present_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $present_title_row);
		
	$present_table_row[] = array('params' => 'class="dataTableRowSelected" id="" onmouseover="this.style.cursor=\'hand\'"','text' => '');
	$present_table_row[] = array('params' => 'class="" id="" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'','text' => '');
		$notice_box->get_contents($present_table_row);
		echo $notice_box->show_notice();
	?>
	    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    $rows = 0;
    
    $campaign_query_raw = '
        select count(c.id) as cnt,
	       c.id, 
               c.name, 
               c.title, 
               c.keyword, 
               c.end_date, 
               c.start_date, 
               c.is_preorder,
               c.max_use,
               c.point_value,
               c.used,
               c.created_at,
               c.status,
               c.site_id,
               c.date_update
        from ' . TABLE_CAMPAIGN . ' c left join customer_to_campaign c2c on c.id = c2c.campaign_id where '.$sql_site_where.' group by c.id
        order by '.$sort.' '.$type
    ;
    $campaign_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $campaign_query_raw, $campaign_query_numrows);
    $campaign_query = tep_db_query($campaign_query_raw);
    while ($campaign = tep_db_fetch_array($campaign_query)) {
      $rows++;
      if ( ((!isset($_GET['campaign_id']) || !$_GET['campaign_id']) || ($_GET['campaign_id'] == $campaign['id'])) && (!isset($selected_item) || !$selected_item) && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $campaign;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($selected_item) && is_array($selected_item)) && ($campaign['id'] == $selected_item['id']) ) {
        echo '              <tr class="dataTableRowSelected" id="show_value_'.$campaign['id'].'" onmouseover="this.style.cursor=\'hand\'">' . "\n";
      } else {
        echo '              <tr class="'.$nowColor.'" id="show_value_'.$campaign['id'].'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'">' . "\n";
      }
?>
		<td class="dataTableHeadingContent" ><input disabled="disabled" type="checkbox" name="all_check"></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;'.tep_get_site_romaji_by_id($campaign['site_id']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' . $campaign['title']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' . $campaign['name']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' .  $campaign['keyword']; ?></td>
                <td class="dataTableContent"  onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
<?php
echo $campaign['start_date'].'～'.$campaign['end_date'];
?></td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                echo $campaign['max_use'];
                ?>
                </td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                echo $campaign['point_value'];
                ?>
                </td>
                <td class="dataTableContent" align="center">
                <?php
                if ($campaign['status'] == '1') {
                  echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggle_campaign_action(\''.$ocertify->npermission.'\', \'' .  tep_href_link(FILENAME_CAMPAIGN, 'action=setflag&flag=0&campaign_id=' . $campaign['id'].  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
                } else {
                  echo '<a href="javascript:void(0);" onclick="toggle_campaign_action(\''.$ocertify->npermission.'\', \'' . tep_href_link(FILENAME_CAMPAIGN, 'action=setflag&flag=1&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
                }
                ?>
                </td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                $cam_count_raw = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where campaign_id = '".$campaign['id']."'"); 
                $cam_count = tep_db_fetch_array($cam_count_raw);
                if ($cam_count['total']) {
                  echo $cam_count['total']; 
                } else {
                  echo '0'; 
                }
                ?>
                </td>
                <td class="dataTableContent" align="right">
<?php
      $campaigin_date_info = (tep_not_null($campaign['date_update']) && ($campaign['date_update'] != '0000-00-00 00:00:00'))?$campaign['date_update']:$campaign['created_at'];
      echo '<a href="javascript:void(0);" onclick="show_campaign_info(this, \''.$campaign['id'].'\', \''.(!empty($_GET['site_id'])?$_GET['site_id']:$show_list_str1).'\');">' .  tep_get_signal_pic_info($campaigin_date_info) . '</a>'; 
    ?>&nbsp;
    </td>
              </tr>
<?php
    }

?>
              <tr>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
		  <tr>
		    <td valign="top" class="smallText">
                    <?php
                    if($ocertify->npermission >= 15){
                    echo '<select disabled="disabled">';
                    echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                    echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                    echo '</select>';
                    }
                    ?>
                    </td>
                    <td align="right" class="smallText">
		  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $campaign_split->display_count($campaign_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CAMPAIGN); ?></td>
                    <td class="smallText" style="padding:0px;"  align="right"><?php echo $campaign_split->display_links($campaign_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'campaign_id'))); ?></td>
                  </tr>
                  <tr>
		    <td></td>
                    <td style="padding:0px;" align="right">
                    <?php 
                    echo '<a href="javascript:void(0);">' .tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="show_new_campaign(\''.(!empty($_GET['site_id'])?$_GET['site_id']:$show_list_str1).'\');"') . '</a>'; 
                    ?>
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
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
