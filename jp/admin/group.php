<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  define('FILENAME_GROUP', 'group.php');
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id_sql)){
    $site_arr = $userslist['site_permission']; 
  }

  if($_GET['action'] == 'new_group'){
	if($_POST['group_name'] != ''){
		if($_GET['id'] == '-1' && is_numeric($_GET['parent_id'])){
			if(!empty($_POST['users_list'])){
				$all_users_id = implode('|||',$_POST['users_list'] );
			}else{
				$all_users_id = '';
			}
			$group_sql_array = array(
				'name' => $_POST['group_name'],
				'parent_id' => $_GET['parent_id'],
				'all_users_id' => $all_users_id,
				'create_time' => time()
			);
			tep_db_perform('groups', $group_sql_array);
			if($_GET['parent_id'] != '0' && !empty($_POST['users_list'])){
				$parent_id_flag = $_GET['parent_id'];
			   while($parent_id_flag != '0'){
				$parent_sql = 'select * from groups where id = "'.$parent_id_flag.'"';
				$parent_res = tep_db_fetch_array(tep_db_query($parent_sql));
				if($parent_res['all_users_id'] == '' || $parent_res['all_users_id'] == null){
					tep_db_query('update groups set all_users_id = "'.$all_users_id.'" where id = "'.$parent_res['id'].'"');
				}else{
					foreach(explode('|||',$all_users_id) as $value){
						if(!in_array($value,explode('|||',$parent_res['all_users_id']))){
							$parent_res['all_users_id'] .= '|||'.$value;	
						}
					}
					tep_db_query('update groups set all_users_id = "'.$parent_res['all_users_id'].'" where id = "'.$parent_res['id'].'"');
				}
				$parent_id_flag = $parent_res['parent_id'];
				unset($parent_res);
			   }
			}
		}
	}
	tep_redirect(tep_href_link('group.php?id='.$_GET['parent_id']));
  }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo GROUP_SETUP; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script>
var o_submit_single = true;
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
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
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
function group_ajax(ele,group_id,parent_group_id,group_name){
 var self_page = "<?php echo $_SERVER['PHP_SELF'];?>"
 $.ajax({
 url: 'ajax.php?&action=show_group_info',
 data: {group_id:group_id,parent_group_id:parent_group_id,group_name:group_name} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_latest_news").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
if(group_id != -1){
	if($(ele).parent().next()[0] === undefined){
		if($('#show_latest_news').height() > ($(ele).parent().parent().height() - $(ele).parent().height())){
			var topset = $(ele).offset().top + $(ele).height() + 3;
		}else{
			var topset = $(ele).offset().top - $('#show_latest_news').height();
		}
	}else{
		var topset = $(ele).offset().top + $(ele).height() + 3;
	}
	$('#show_latest_news').css('top', topset);
}
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(group_id == -1){
  $('#show_latest_news').css('top', $('#show_text_list').offset().top);
}
$('#show_latest_news').css('z-index','1');
$('#show_latest_news').css('left',leftset);
$('#show_latest_news').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_latest_news').css('display','none');
   o_submit_single = true;
}
function check_group(){
	document.forms.new_latest_group.submit();
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
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
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
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	        <td class="pageHeading">
			<?php echo GROUP_SETUP;?>
		</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_text_list">
          <tr>
            <td valign="top">
<?php
	$form_str = tep_draw_form('messages_checkbox', 'messages.php','action=delete_messages&messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page'].'&status='.$_GET['status'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
	$group_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
	$notice_box = new notice_box('','',$group_table_params);
	$group_table_row = array();
	$group_title_row = array();
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="">');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_NAME.'</a>');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_STATUS.'</a>');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_OPT.'</a>');
	$group_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $group_title_row);
	if($_GET['id'] == '' || !is_numeric($_GET['id'])){
		$group_id = 0;
	}else{
		$group_id = $_GET['id'];
	}
	$latest_group_query_raw = ' select *
                from groups where parent_id = "'.$group_id.'"';
	$latest_group_split = new splitPageResults($group_page, MAX_DISPLAY_SEARCH_RESULTS, $latest_group_query_raw, $latest_group_query_numrows);
	$latest_group_query = tep_db_query($latest_group_query_raw);
	$all_group_array = array();
	while ($latest_group = tep_db_fetch_array($latest_group_query)) {
		$even = 'dataTableSecondRow';
        	$odd  = 'dataTableRow';
        	if (isset($nowColor) && $nowColor == $odd) {
                	$nowColor = $even;
        	} else {
                	$nowColor = $odd;
        	}
		$group_params = 'id="'.$latest_group['id'].'" class="'.$nowColor.'" onclick="" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
		$group_info = array();
		$group_checkbox = '<input type="checkbox" name="group_id[]" value="'.$latest_group['id'].'">';
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $group_checkbox
        	);
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a  href="group.php?id='.$latest_group['id'].'"><img src="images/icons/folder.gif" border="0">&nbsp'.$latest_group['name'].'</a>'
        	);
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => 0
        	);
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a href="javascript:void(0)" onclick="group_ajax(this,\''.$latest_group['id'].'\',\''.$group_id.'\',\''.$latest_group['name'].'\')"><img src="images/icons/info_blink.gif" border="0"></a>'
        	);
		$group_table_row[] = array('params' => $group_params, 'text' => $group_info);
		$all_group_array[] = $latest_group;
	}
	$notice_box->get_form($form_str);
	$notice_box->get_contents($group_table_row);
	$notice_box->get_eof(tep_eof_hidden());
	echo $notice_box->show_notice();
?>
	<pre>
<?php
//	var_dump($all_group_array);
?>
	</pre>
	    </td>
              </tr>

            </table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:-10px;">
<tr>                 
                    <td valign="top" class="smallText">
                    <?php 
                    echo '<select name="messages_action" onchange="messages_change_action(this.value, \'messages_id[]\');">';
                    echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                    echo '</select>';
                    ?> 
                    </td>
                    <td align="right" class="smallText">
                   </td>
                  </tr>

                  <tr>
                    <td class="smallText" valign="top"><?php echo $latest_group_split->display_count($latest_group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $group_page, TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $latest_group_split->display_links($latest_group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $messages_page, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></div></td>
                  </tr>
                     <tr><td></td><td align="right">
                      <div class="td_button"><?php
                      //通过site_id判断是否允许新建
                     // if (trim($site_array[0]) != '') {
                      echo '&nbsp;<a href="javascript:void(0)" onclick="group_ajax(this,\'-1\',\''.$group_id.'\',\'\')">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>';
                     // }else{
                     // echo '&nbsp;' .tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"');
                     // } 
                      ?>
                    </div>
                     </td></tr>
                                  </table>
			</td>
          </tr>
        </table></td>
      </tr>
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
