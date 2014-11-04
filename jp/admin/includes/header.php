<?php
/*
   $Id$
 */
include_once(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
if (isset($messageStack) && $messageStack->size > 0) {
  echo $messageStack->output();
}
?>
<script languages="javascript" src="includes/javascript/common.js?v=<?php echo $back_rand_info;?>"></script>
<script type="text/javascript">

<?php
if(PERSONAL_SETTING_NOTIFICATION_SOUND == ''){
  echo 'var play_flag = true;'."\n";
}else{
  $play_array = unserialize(PERSONAL_SETTING_NOTIFICATION_SOUND);
  if(isset($play_array[$ocertify->auth_user])){
    if($play_array[$ocertify->auth_user] == '1'){
      echo 'var play_flag = true;'."\n";
    }else{
      echo 'var play_flag = false;'."\n";
    }
  }else{
    echo 'var play_flag = true;'."\n"; 
  }
}
if ($_SERVER['PHP_SELF'] != '/admin/preorders.php') {
?>
var cfg_head_last_customer_action = '<?php echo PREORDER_LAST_CUSTOMER_ACTION;?>';
var prev_head_customer_action = '';
var check_head_pre_o_single = '0';
<?php
}
?>
<?php
if ($_SERVER['PHP_SELF'] != '/admin/orders.php') {
?>
var cfg_ohead_last_customer_action = '<?php echo LAST_CUSTOMER_ACTION;?>';
var prev_ohead_customer_action = '';
var check_head_o_single = '0';
<?php
}
?>
var header_text_alert_link = '<?php echo HEADER_TEXT_ALERT_LINK?>';
</script>
<script languages="javascript" src="includes/javascript/header.js?v=<?php echo $back_rand_info;?>"></script>
<script type="text/javascript">
function hide_messages(){
        $('#show_all_messages_notice').children().remove();
        check_header_messages(true);
	if($('#show_all_messages_notice').css('display') == 'none'){
		$('#show_all_messages_notice').css('display', '');
	}else{
		$('#show_all_messages_notice').css('display', 'none');
	}
}
function delete_header_messages(messages_id){
	if(messages_id != '' && messages_id != null){
		$.post(
			"ajax.php?&action=delete_messages_header",
			{
				id:messages_id,
			},
			function(data){
				if(data == '1'){
					check_header_messages(false);
				}	
			}
		);
	}
}
function delete_header_messages_all(){
   if(confirm('<?php echo DELETE_ALL_NOTICE;?>')){	
	delete_notice('',0);
	var delete_num = 1;
	var messages_id_all = '';
	$('[name="messages_notice"]').each(function(){
		if($(this).attr('value') != '' && $(this).attr('value') != null){
			if($('[name="messages_notice"]').length > delete_num){
				messages_id_all += $(this).attr('value')+';';
				delete_num++;
			}else{
				messages_id_all += $(this).attr('value');
			}
		}
	});
	if(messages_id_all != '' && messages_id_all != null){
		$.post(
			"ajax.php?&action=delete_messages_header_all",
			{
				id_all:messages_id_all,
			},
			function(data){
				if(data == '1'){
					check_header_messages(false);
					
				}	
			}
		);
        }
        window.scrollTo(0,0);
   }
}


var timestamp = <?php echo time();?>;
function check_header_messages(show_flag){
	var messages_num = 0;
        var length_all;
	$.post(
		"ajax.php?&action=check_messages_header",
		{
    			sender_id:"<?php echo $ocertify->auth_user;?>",
                        show_all:show_flag,
  		},
  		function(data){
			$('#show_messages_notice').children().remove();
			$('#show_all_messages_notice').children().remove();
                        if(data != '0'){
                                var show_recent_messages_num = 0;
                                var show_all_messages_num = 0;
                                $.each(eval(data), function(){
                                        show_all_messages_num++;
					if(parseInt(this.timestamp) > parseInt(timestamp)){
                                                var bgcolor = '#FFFF33';
                                                show_recent_messages_num++;
					}else{
						var bgcolor = '#FFCC00';
					}
				var img_mark = '';
                                if(this.mark){
				$.each(this['mark'], function(){
					img_mark += '<img border="0" src="images/icon_list/icon_'+this+'.gif">'
				});
                                }
					if(img_mark !='')img_mark+='&nbsp&nbsp';
					var str_html='';
					if(messages_num == 0){
						if(this['type']=='messages'){
                                                str_html+='<table style="background:'+bgcolor+'" value='+this['id']+' name="messages_notice" width="100%" border="0" cellspacing="0" cellpadding="0"><tr height="26px" style="background:'+bgcolor+'"><td  width="80px" id="messages_head"><img src="images/icons/messages.png"></td><td width="136px">'+this['time']+'</td><td style="padding:0 0 0 6px">'+img_mark+'<a onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" style="color:#0000FF;" href="messages.php?id='+this['id']+'&page='+this['page']+'">'+this['content']+'</a></td><td width="50px" align="right"><a onclick="delete_header_messages('+this['id']+')" href="javascript:void(0);"><img alt="close" src="images/icons/bbs_del_one.png"></a></td></tr></table>';
                                                }else {
                                                str_html+='<table style="background:'+bgcolor+'" value='+this['id']+'  width="100%" border="0" cellspacing="0" cellpadding="0"><tr height="26px" style="background:'+bgcolor+'"><td  width="80px" id="messages_head">'+this['title']+'</td><td width="136px">'+this['time']+'</td><td style="padding:0 0 0 6px">'+img_mark+''+this['content']+'</td><td width="50px" align="right">'+this['delete']+'</td></tr></table>';
                                                }
                                if(this.hidden){
                                  str_html += this.hidden;
                                }
				$('#show_messages_notice').append(str_html);
				if(play_flag == true){
                                var long_sound = false;
					switch(this['type']){
					case 'messages':var notice_audio = document.getElementById('head_message'); break;
					case 'bulletin':var notice_audio = document.getElementById("head_message"); break;
					case 'button':var notice_audio = document.getElementById("head_message");break;
					case 'order':
                                          var notice_audio = document.getElementById("head_alarm");
                                          long_sound = true;
                                          break;
						}
                    if(!show_flag){
                    notice_audio.play();
                    if(long_sound){
                      setTimeout(function(){notice_audio.play();},700);
                    }else{
                      setTimeout(function(){notice_audio.play();},600);
                    }
                    }
                }
					}else{
					if(this['type']=='messages'){
                                          str_html+='<table style="background:'+bgcolor+'" value='+this['id']+' name="messages_notice" width="100%" border="0" cellspacing="0" cellpadding="0"><tr height="26px" style="background:'+bgcolor+'"><td width="80px"><img src="images/icons/messages.png"></td><td width="136px">'+this['time']+'</td><td style="padding:0 0 0 6px">'+img_mark+'<a onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" style="color:#0000FF;" href="messages.php?id='+this['id']+'&page='+this['page']+'">'+this['content']+'</a></td><td width="50px" align="right"><a onclick="delete_header_messages('+this['id']+')" href="javascript:void(0);"><img alt="close" src="images/icons/bbs_del_one.png"></a></td></tr></table>';
                                        }else{
                                          str_html+='<table style="background:'+bgcolor+'" value='+this['id']+' width="100%" border="0" cellspacing="0" cellpadding="0"><tr height="26px" style="background:'+bgcolor+'"><td width="80px">'+this['title']+'</td><td width="136px">'+this['time']+'</td><td style="padding:0 0 0 6px">'+img_mark+''+this['content']+'</td><td width="50px" align="right">'+this['delete']+'</td></tr></table>';
                                        }
                                if(this.hidden){
                                  str_html += this.hidden;
                                }
					$('#show_all_messages_notice').append(str_html);
					}
					messages_num++;
                                if(!show_flag){
                                  length_all = (this.length - 1);
                                }
                                });
			}
                        if(eval(data).length > 1||length_all>2){
                                if(eval(data).length > 1){
                                  length_all = eval(data).length-1;
                                }
                                var messages_background_color = '#FFCC00';
                                if(show_recent_messages_num == show_all_messages_num){

                                  messages_background_color = '#FFFF33';
                                }
                                if(!show_flag){
                                  $('#show_all_messages_notice').css('display','none');
                                }
				$('#messages_head').append('&nbsp;&nbsp;<a onclick="hide_messages();" onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" href="javascript:void(0);"><span>他'+length_all+'件</span></a>');
				$('#show_all_messages_notice').append('<table style="background:'+messages_background_color+'" width="100%" border="0" cellspacing="0" cellpadding="0"><tr style="background:'+messages_background_color+'"><td colspan="3" align="right"><a href="javascript:void(0);" onclick="delete_header_messages_all()"><img src="images/icons/bbs_del.png" onmousemove="this.src=\'images/icons/white_bbs_del.png\'" onmouseout="this.src=\'images/icons/bbs_del.png\'" ></a></td></tr></table>');
			};
  		}
	);
}
$(document).ready(function(){
  check_header_messages(false);
  setInterval(function(){check_header_messages(false)}, 60000);
});

<?php
if ($_SERVER['PHP_SELF'] != '/admin/preorders.php') {
?>
$(function(){
  setTimeout(function(){check_preorder_head()}, 70000);
});
<?php
}
?>
<?php
if ($_SERVER['PHP_SELF'] != '/admin/orders.php') {
?>
$(function(){
  setTimeout(function(){check_order_head()}, 90000);
});
<?php
}
?>
function change_attendance_login(uid) {

	if(confirm('<?php echo HEADER_LOGIN_REMIND?>')){
		$.post(
			"ajax.php?&action=change_attendance_login",
			{
				user_name:uid,
			},
			function(data){
				if(data){
					location=location;
				}
			}
		);
	}
}

function change_attendance_logout(uid) {
	if(confirm('<?php echo HEADER_LOGOUT_REMIND?>')){
		$.post(
			"ajax.php?&action=change_attendance_logout",
			{
				user_name:uid,
			},
			function(data){
				if(data){
					location=location;
				}
			}
		);

	}

}
function mouse_on(obj){
	obj.style.textDecoration="underline";
	obj.style.color="#333333";
}
function mouse_leave(obj){
	obj.style.textDecoration="none";
	obj.style.color="#0000FF";
}
</script>
<noscript>
<div class="messageStackError"><?php echo TEXT_JAVASCRIPT_ERROR;?></div> 
</noscript>
<div class="compatible_head">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="preorder_head">
<tr>
  <td colspan="2">
<div id="show_messages_notice"></div>
<div id="show_all_messages_notice" style="display:none; z-index:30000;"></div>
  </td>
</tr>
<tr>
<td><?php echo tep_image(DIR_WS_CATALOG .DIR_WS_IMAGES . ADMINPAGE_LOGO_IMAGE, STORE_NAME, '', ''); ?></td>
<td align="right" valign="bottom" width="60%">
<div class="header_space">
<?php
$languages = tep_get_languages();
$cur_page = split('\?', basename($_SERVER['SCRIPT_NAME'])); $cur_page = $cur_page[0];
$current_page_tp = split('\?', basename($_SERVER['SCRIPT_NAME'])); $current_page_tp = $current_page_tp[0];
if ($current_page_tp == FILENAME_CONFIGURATION) {
  $current_page_tp .= '?gID='.$_GET['gID'];
}

if ($current_page_tp == FILENAME_MODULES) {
  $current_page_tp .= '?set='.$_GET['set'];
}

if($_SESSION['text_language']=='japanese' || $_SESSION['text_language']==''){
     $help_language = 'jp';
}else if ($_SESSION['text_language']=='chinese'){
     $help_language = 'ch';
}else if ($_SESSION['text_language']=='vietnamese'){
     $help_language = 'vn';
}
//echo '<pre>';
//print_r($_SESSION);
//选择语言
echo '<select name="select_languages" onchange="change_language(this.value,\''.$cur_page.'\')">';
echo '<option value="ja"'.($_SESSION['text_language'] == 'japanese' ? ' selected="selected"' : '').'>'.TEXT_SELECT_LANGUAGES_JP.'</option>';
echo '<option value="ch"'.($_SESSION['text_language'] == 'chinese' ? ' selected="selected"' : '').'>'.TEXT_SELECT_LANGUAGES_CH.'</option>';
echo '<option value="vn"'.($_SESSION['text_language'] == 'vietnamese' ? ' selected="selected"' : '').'>'.TEXT_SELECT_LANGUAGES_VN.'</option>';
echo '</select>';

//控制声音
if (PERSONAL_SETTING_NOTIFICATION_SOUND == '') {
  $sound_flag = '1';
} else {
  $personal_sound_array = unserialize(PERSONAL_SETTING_NOTIFICATION_SOUND);
  if (array_key_exists($ocertify->auth_user, $personal_sound_array)) {
  $sound_flag = $personal_sound_array[$ocertify->auth_user]; 
  } else {
    $sound_flag = '1'; 
  }
}
$sound_img =($sound_flag==1)?'sound_high.png':'sound_mute.png'; 
echo '&nbsp;<a href="javascript:change_sound_flag('.$sound_flag.')" class="headerLink"><span id="sound_span"><img src="images/menu_icon/'.$sound_img.'" alt="img"></span></a>&nbsp';
echo '<a href="' . tep_href_link('help.php','help_page_name='.urlencode(str_replace('/admin/','',$current_page_tp)).'&help_language='.$help_language, 'NONSSL') . '" class="headerLink"  target="_blank"><img src="images/menu_icon/icon_help_info.gif" alt="img"></a>';
?>
</div>
<?php echo tep_draw_form('changepwd', FILENAME_CHANGEPWD,'','post','
    id=\'changepwd_form\'');
echo tep_draw_hidden_field("execute_password",TEXT_ECECUTE_PASSWORD_USER);
echo tep_draw_hidden_field("userslist",$ocertify->auth_user);
echo "</form>";
?>
<?php echo HEADER_TEXT_SITE_NAME;?>&nbsp;<b>
<?php
echo "<a style='text-decoration:underline;' class='head_link' href = '".tep_href_link(basename($_SERVER['PHP_SELF']),'action=re_login&num='.time(),'NONSSL')."'>";
$user_info = tep_get_user_info($ocertify->auth_user);
if (isset($ocertify) && $ocertify->npermission >= 15) {
  echo '<font color="blue">'.$user_info['name'].'</font>';
} elseif (isset($ocertify) && $ocertify->npermission == 10) {
  echo '<font color="red">'.$user_info['name'].'</font>';
} else {
  echo $user_info['name'];
}
echo "</a>";
?>
</b>
<?php
//打卡
	 $uid = $user_info['userid'];
	 $check_result=tep_check_show_login_logout($uid);
	 
if($check_result==0) { 
	echo sprintf(HEADER_ATTENDANCE_LOGOUT,'href="javascript:void(0);" onclick=change_attendance_login("'.$user_info['userid'].'") title="'.HEADER_ATTENDANCE_LOGIN_TITLE.'" style="text-decoration:underline;"');
}else {
	echo sprintf(HEADER_ATTENDANCE_LOGIN,'href="javascript:void(0);" onclick=change_attendance_logout("'.$user_info['userid'].'") title="'.HEADER_ATTENDANCE_LOGOUT_TITLE.'" style="text-decoration:underline;"');
}

if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.0")){

	?>
<embed id="head_warn" src="images/warn.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<embed id="head_alarm" src="images/alarm.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<embed id="head_message" src="images/message.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<?php
}else{
	?>
<audio id="head_warn" src="images/warn.mp3" ></audio>
<audio id="head_alarm" src="images/alarm.mp3" ></audio>
<audio id="head_message" src="images/message.mp3" ></audio>

<?php
}
?>

<br>
</td>
</tr>
<?php
if(preg_match("/".FILENAME_ORDERS."/",$PHP_SELF)){
  echo tep_minitor_info();
}
?>

<tr class="headerBar">
<td colspan='3'>
<table width="100%">
<tr>
<td class="headerBarContent">&nbsp;<?php 
if($current_page_tp == "modules.php"){
  preg_match("#set=[^&]+#",$_SERVER["REQUEST_URI"],$set_mod_array);
 $current_page_tp .= "?".$set_mod_array[0];
}
if($current_page_tp == "configuration.php") {
  preg_match("#gID=[^&]+#",$_SERVER["REQUEST_URI"],$set_mod_array);
 $current_page_tp .= "?".$set_mod_array[0];
}
if (isset($ocertify->npermission) || $ocertify->npermission) {
  echo '&nbsp<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a>';
}
?></td>
<td class="headerBarContent" align="right">
<?php 
if (!isset($ocertify->npermission) || $ocertify->npermission >= 7) {
  echo '
    <table>
    <tr>';
  $href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
    echo '
    <td><a class="headerLink" href="javascript:void(0);" onclick="toggle_header_menu(\'headerorder\')">'.HEADER_TEXT_ORDER_INFO.'</a>&nbsp;|<br>
    <table class="menu01" id="headerorder" cellpadding="0" cellspacing="0">
        <tr>
      <td class="menu01"><a class="t_link01" href="'.tep_href_link(FILENAME_ORDERS, '', 'NONSSL').'">'.HEADER_TEXT_ORDERS.'</a></td> 
    </tr>
    <tr>
      <td class="menu01"><a class="t_link01" href="'.tep_href_link(FILENAME_PREORDERS, '', 'NONSSL').'">'.HEADER_TEXT_PREORDERS.'</a></td> 
    </tr>
<tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link('create_order.php', '',
    'NONSSL').'">'.HEADER_TEXT_CREATE_ORDER.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01" href="'.tep_href_link('create_preorder.php',
    '', 'NONSSL').'">'.HEADER_TEXT_CREATE_PREORDER.'</a></td>
      </tr> 

    </table> 
    </td>
    <td><a href="' . tep_href_link('telecom_unknow.php', '', 'NONSSL') . '" class="headerLink"
    >'.HEADER_TEXT_TELECOM_UNKNOW.'</a>&nbsp;|</td>
    <td align="left">
    &nbsp;<a class="headerLink" href="javascript:void(0);" onclick="toggle_header_menu(\'tutorials\')"
    >'.HEADER_TEXT_TUTORIALS.'</a>&nbsp;|<br>
    <table class="menu01" id="tutorials" cellpadding="0" cellspacing="0">
    <tr>
    <td class="menu01"><a class="t_link01"
    href="'.tep_href_link(FILENAME_CATEGORIES, '',
    'NONSSL').'">'.HEADER_TEXT_CATEGORIES.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_INVENTORY, '',
    'NONSSL').'">'.HEADER_TEXT_INVENTORY.'</a></td>
      </tr>       
      </table>
      </td>
      <td><a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" 
      class="headerLink">'.HEADER_TEXT_CUSTOMERS.'</a>&nbsp;|</td>
      <td>&nbsp;<a href="' . tep_href_link(FILENAME_NEWS, '', 'NONSSL') .
      '" class="headerLink">'.HEADER_TEXT_LATEST_NEWS.'</a>&nbsp;|</td>
      
      <td align="left">
      &nbsp;<a class="headerLink" href="javascript:void(0);"
      onclick="toggle_header_menu(\'managermenu\')">'.HEADER_TEXT_MANAGERMENU.'</a>&nbsp;|<br>
      <table class="menu01" id="managermenu" cellpadding="0" cellspacing="0">
      ';
        echo '
      <tr><td class="menu01"><a class="t_link01" 
      href="'.tep_href_link(FILENAME_SEARCH, '', 'NONSSL').'"
      >'.BOX_TOOLS_SEARCH.'</a></td></tr>
	<tr><td class="menu01"><a class="t_link01" href="'.tep_href_link('messages.php', '','NONSSL').'">'.MESSAGES_PAGE_LINK_NAME.'</a></td></tr>	
      <tr><td class="menu01"><a class="t_link01" 
      href="add_note.php?author='.$ocertify->auth_user.'&belong='.$belong.'"
      id="fancy">'.TEXT_ADD_NOTE.'</a></td></tr>


      <tr>
      <td class="menu01"><a class="t_link01"
       href="' . tep_href_link('bulletin_board.php', '', 'NONSSL') . '"
      >'.HEADER_TEXT_BULLETIN.'</a></td>
	  </tr>
	  <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PW_MANAGER, '',
    'NONSSL').'">'.HEADER_TEXT_PW_MANAGER.'</a></td>
      </tr>
	  <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_ROSTER_RECORDS, '',
    'NONSSL').'">'.ROSTER_TITLE_TEXT.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      onclick="javascript:goto_changepwd(\'changepwd_form\', \''.FILENAME_CHANGEPWD.'\')"
      href="javascript:void(0);">'.HEADER_TEXT_USERS.'</a>';
?>
<?php 
  echo '</td>
     </tr>    
      </table>
      </td>



      <td align="left">
      ';
  echo '&nbsp;<a href="javascript:void(0);" class="headerLink"
    onclick="toggle_header_menu(\'redirecturl\')">'.HEADER_TEXT_REDIRECTURL.'</a>&nbsp;|<br>'; 
  $site_link_query = tep_db_query('select * from '.TABLE_SITES);
  echo '<table id="redirecturl" cellspacing="0" cellpadding="0" class="menu01" style="visibility: hidden;">'; 
  while ($site_link = tep_db_fetch_array($site_link_query)) {
    echo '<tr><td class="menu01">'; 
    echo '<a href="'.$site_link['url'].'" target="_blank" class="t_link01">'.$site_link['name'].'</a>'; 
    echo '</td></tr>'; 
  }
  echo '</table></td>';
  echo ' 
    <td>&nbsp;
  <a href="' . tep_href_link(str_replace('/admin/','',$_SERVER['SCRIPT_NAME']), 'execute_logout_user=1&num='.time(), 'NONSSL') .  '" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a>&nbsp;</td></tr></table>';
} else {
  echo '|&nbsp;
  <a href="' . tep_href_link(str_replace('/admin/','',$_SERVER['SCRIPT_NAME']), 'execute_logout_user=1&num='.time(), 'NONSSL') .  '" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a>&nbsp;';
}
?>

</td>
</tr>
</table>

</td>
</tr>
</table></div>
