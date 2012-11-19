<?php
/*
   $Id$
 */

if (isset($messageStack) && $messageStack->size > 0) {
  echo $messageStack->output();
}
?>
<script type="text/javascript">
$(function() {
   setTimeout(function() {show_head_notice(1)}, 35000);
});

function check_exists_function(funcName){
  try{
    if(typeof(eval(funcName)) == "function") {
      return true;
    }
  }catch(e){
    return false;
  }
}

function calc_notice_time(leave_time, nid, start_calc)
{
  
  var now_timestamp = Date.parse(new Date());
  
  now_timestamp_str = now_timestamp.toString().substr(0, 10);

  now_timestamp_tmp = parseInt(now_timestamp_str);
  
  leave_time_diff = leave_time - now_timestamp_tmp;
  
  n_day = Math.floor(leave_time_diff / (24*3600)); 
  leave_time_tmp = leave_time_diff % (24*3600);
  leave_time_seconds = leave_time_tmp % 3600;
  n_hour = (leave_time_tmp - leave_time_seconds) / 3600;
  leave_time_minute = leave_time_seconds % 60;
  n_minute = (leave_time_seconds - leave_time_minute) / 60; 
  
  if (n_day < 10) {
    n_show_day = '0'+n_day; 
  } else {
    n_show_day = n_day; 
  }
  
  if (n_hour < 10) {
    n_show_hour = '0'+n_hour; 
  } else {
    n_show_hour = n_hour; 
  }
  
  if (n_minute < 10) {
    n_show_minute = '0'+n_minute; 
  } else {
    n_show_minute = n_minute; 
  }
  
  if (leave_time_diff <= 0) {
    n_show_day = '00'; 
    n_show_hour = '00'; 
    n_show_minute = '00';
    n_day = 0; 
    n_hour = 0;
    n_minute = 0;
  }
  
  if (document.getElementById('leave_time_'+nid)) {
    document.getElementById('leave_time_'+nid).innerHTML = n_show_day+'<?php echo DAY_TEXT;?>'+n_show_hour+'<?php echo HOUR_TEXT;?>'+n_show_minute+'<?php echo MINUTE_TEXT;?>'; 
    if ((n_hour == 0) && (n_minute == 0) && (n_day == 0)) {
      document.getElementById('leave_time_'+nid).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFB3B5'; 
      var n_node=document.getElementById('head_notice');  
      if (n_node.controls) {
        n_node.controls.play();  
      } else {
        if (check_exists_function('play')) {
          n_node.play();  
        }
      }
    }
    setTimeout(function(){calc_notice_time(leave_time, nid, 1)}, 5000); 
  } 
}

function expend_all_notice(aid)
{
  if ($('#show_all_notice').css('display') == 'none') {
    $('#show_all_notice').css('display', 'block');
    $.ajax({
      url: 'ajax_notice.php?action=show_all_notice',     
      data: 'aid='+aid, 
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        $('#show_all_notice').html(data); 
      }
    });
  } else {
    $('#show_all_notice').css('display', 'none');
    $('#show_all_notice').html(''); 
  }
}
function delete_alarm_notice(nid, e_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=delete_alarm',
      data: 'nid='+nid,
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        $('#show_all_notice').css('display', 'none');
        $('#show_all_notice').html(''); 
        show_head_notice(0);
      } 
      });
}
function delete_micro_notice(nid, e_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=delete_micro',
      data: 'nid='+nid,
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        $('#show_all_notice').css('display', 'none');
        $('#show_all_notice').html(''); 
        show_head_notice(0);
      } 
      });
}
function show_head_notice(no_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=show_head_notice',
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        if (data != '') {
          data_info = data.split('|||');
          
          if (document.getElementById('leave_time_'+data_info[2])) {
            if (data_info[0] != document.getElementById('more_single').value) {
              $('#show_head_notice').html(data_info[3]); 
            }
          } else {
            $('#show_head_notice').html(data_info[3]); 
          }
          
          if (data_info[1] <= 0) {
            document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFB3B5'; 
          } else {
            orgin_bg = document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background; 
            if (orgin_bg.indexOf('rgb(255, 179, 181)') > 0) {
              document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFFFFF'; 
            }
          }
        } else {
          $('#show_head_notice').html(data); 
          orgin_bg = document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background; 
          if (orgin_bg.indexOf('rgb(255, 179, 181)') > 0) {
            document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFFFFF'; 
          }
        }
        
        if (no_type == 1) {
          setTimeout(function() {show_head_notice(1)}, 35000);
        }
      } 
      });
}
function showmenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="visible"
}
function hidemenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="hidden"
}
function toggle_header_menu(elmnt)
{
  if (document.getElementById(elmnt).style.visibility == 'visible') {
    document.getElementById(elmnt).style.visibility="hidden";
  } else {
    document.getElementById(elmnt).style.visibility="visible";

    switch (elmnt) {
      case 'tutorials':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'ordermenu':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'managermenu':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break; 
      case 'redirecturl':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        break;
      case 'headerorder':
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        break;
    }
  }
}
function goto_changepwd(id){
  document.getElementById(id).action="<?php echo FILENAME_CHANGEPWD;?>";
  document.getElementById(id).submit();
  return false; 
}
<?php
if ($_SERVER['PHP_SELF'] != '/admin/preorders.php') {
?>
var cfg_head_last_customer_action = '<?php echo PREORDER_LAST_CUSTOMER_ACTION;?>';
var prev_head_customer_action = '';
function playHeadSound()  
{  
  var hnode=document.getElementById('head_sound');  
  if(hnode!=null)  
  {  
   if (hnode.controls) {
    hnode.controls.play();  
   } else {
    hnode.play();  
   }
  }
}
function check_preorder_head() {
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=last_customer_action',
    success: function(last_head_customer_action) {
      if (last_head_customer_action != cfg_head_last_customer_action && prev_head_customer_action != last_head_customer_action){
        $('.preorder_head').css('background-color', '#83dc94');
        prev_head_customer_action = last_head_customer_action;
        playHeadSound();
      }
      }
  });
  setTimeout(function(){check_preorder_head()}, 70000);
}

$(function(){
  setTimeout(function(){check_preorder_head()}, 70000);
});

<?php
}
?>
<?php
if ($_SERVER['PHP_SELF'] != '/admin/orders.php') {
?>
var cfg_ohead_last_customer_action = '<?php echo LAST_CUSTOMER_ACTION;?>';
var prev_ohead_customer_action = '';
function playOrderHeadSound()  
{  
  var ohnode=document.getElementById('head_warn');  
  if(ohnode!=null)  
  {  
   if (ohnode.controls) {
    ohnode.controls.play();  
   } else {
    ohnode.play();  
   }
  }
}
function check_order_head() {
  $.ajax({
    dataType: 'text',
    url: 'ajax_orders.php?action=last_customer_action',
    success: function(last_ohead_customer_action) {
      if (last_ohead_customer_action != cfg_ohead_last_customer_action && prev_ohead_customer_action != last_ohead_customer_action){
        $('.preorder_head').css('background-color', '#ffcc99');
        prev_ohead_customer_action = last_ohead_customer_action;
        playOrderHeadSound();
      }
      }
  });
  setTimeout(function(){check_order_head()}, 90000);
}

$(function(){
  setTimeout(function(){check_order_head()}, 90000);
});

<?php
}
?>
</script>
<noscript>
<div class="messageStackError"><?php echo TEXT_JAVASCRIPT_ERROR;?></div> 
</noscript>
<div class="compatible_head">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="preorder_head">
<tr>
  <td colspan="2">
  <div id="show_head_notice">
<?php echo tep_get_notice_info();?>
</div>
<div id="show_all_notice" style="display:none;"></div>
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
/*
foreach($languages as $key => $val){
echo "<a href=".tep_href_link($cur_page,tep_get_all_get_params(array('language'))."language=".$val['code'])."><font size=3px><b>".strtoupper($val['code']=='ja'?'jp':$val['code'])."</b></font></a>&nbsp;";
}
*/
echo "<a href=".tep_href_link($cur_page,tep_get_all_get_params(array('language')).
    "language=".'ja')."><font size=3px><b>JP</b></font></a>&nbsp;";
echo "<a href=".tep_href_link($cur_page,tep_get_all_get_params(array('language')).
    "language=".'ch')."><font size=3px><b>CH</b></font></a>&nbsp;";
echo '<a href="' . tep_href_link('help.php', 'info_romaji='.urlencode(str_replace('/admin/','',$current_page_tp)), 'NONSSL') . '" class="headerLink"  target="_blank"><img src="images/menu_icon/icon_help_info.gif" alt="img"></a>';
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
echo "<a href =
'".tep_href_link(basename($GLOBALS['PHP_SELF']),'action=re_login&num='.time(),'NONSSL')."'>";
$user_info = tep_get_user_info($ocertify->auth_user);
$_SESSION['user_name'] = $user_info['name'];
if (isset($ocertify) && $ocertify->npermission == 15) {
  echo '<font color="blue">'.$user_info['name'].'</font>';
} elseif (isset($ocertify) && $ocertify->npermission == 10) {
  echo '<font color="red">'.$user_info['name'].'</font>';
} else {
  echo $user_info['name'];
}
echo "</a>";
?>
</b>&nbsp;<?php echo HEADER_TEXT_LOGINED;?>&nbsp;
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
<td class="headerBarContent">&nbsp;&nbsp;<?php 
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
      href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN, '',
    'NONSSL').'">'.HEADER_TEXT_CATEGORIES_ADMIN.'</a></td>
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
      <td>&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, '', 'NONSSL') .
      '" class="headerLink">'.HEADER_TEXT_LATEST_NEWS.'</a>&nbsp;|</td>
      
      <td align="left">
      &nbsp;<a class="headerLink" href="javascript:void(0);"
      onclick="toggle_header_menu(\'managermenu\')">'.HEADER_TEXT_MANAGERMENU.'</a>&nbsp;|<br>
      <table class="menu01" id="managermenu" cellpadding="0" cellspacing="0">
      ';
         echo '
      <tr><td class="menu01"><a class="t_link01" 
      href="add_note.php?author='.$ocertify->auth_user.'&belong='.$belong.'"
      id="fancy">'.TEXT_ADD_NOTE.'</a></td></tr>


      <tr>
      <td class="menu01"><a class="t_link01"
       href="' . tep_href_link('micro_log.php', '', 'NONSSL') . '"
      >'.HEADER_TEXT_MICRO_LOG.'</a></td>
      </tr><tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PW_MANAGER, '',
    'NONSSL').'">'.HEADER_TEXT_PW_MANAGER.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      onclick="javascript:goto_changepwd(\'changepwd_form\')"
      href="javascript:void(0);">'.HEADER_TEXT_USERS.'</a>';
?>
<?php 
  echo '</td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PERSONAL_SETTING, '',
        'NONSSL').'">'.HEADER_TEXT_PERSONAL_SETTING.'</a>';
if ($_SERVER['PHP_SELF'] != '/admin/preorders.php') {
?>
<embed id="head_sound" src="images/presound.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<?php
}
?>
<?php
if ($_SERVER['PHP_SELF'] != '/admin/orders.php') {
?>
<embed id="head_warn" src="images/warn.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<?php
}
?>
<embed id="head_notice" src="images/notice.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
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
  <a href="' . tep_href_link(str_replace('/admin/','',$_SERVER['SCRIPT_NAME']), 'execute_logout_user=1&num='.time(), 'NONSSL') .  '" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a></td></tr></table>';
} else {
  echo '|&nbsp;
  <a href="' . tep_href_link(str_replace('/admin/','',$_SERVER['SCRIPT_NAME']), 'execute_logout_user=1&num='.time(), 'NONSSL') .  '" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a>';
}
?>

</td>
</tr>
</table>

</td>
</tr>
</table></div>
