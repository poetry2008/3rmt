<?php
/*
   $Id$
 */

if (isset($messageStack) && $messageStack->size > 0) {
  echo $messageStack->output();
}
?>
<script languages="javascript" src="includes/javascript/common.js?v=<?php echo $back_rand_info;?>"></script>
<script type="text/javascript">
<?php
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
</script>
<noscript>
<div class="messageStackError"><?php echo TEXT_JAVASCRIPT_ERROR;?></div> 
</noscript>
<div id="hidden_mp3"></div>
<div class="compatible_head">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="preorder_head">
<tr>
  <td colspan="2">
  <div id="show_head_notice">
<?php echo tep_get_notice_info();?>
</div>
<div id="show_all_notice" style="display:none; z-index:30000;"></div>
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
echo "<a href=".tep_href_link($cur_page,tep_get_all_get_params(array('language')).
    "language=".'ja')."><font size=3px><b>JP</b></font></a>&nbsp;";
echo "<a href=".tep_href_link($cur_page,tep_get_all_get_params(array('language')).
    "language=".'ch')."><font size=3px><b>CH</b></font></a>&nbsp;";
echo '<a href="' . tep_href_link('help.php', 'help_page_name='.urlencode(str_replace('/admin/','',$current_page_tp)), 'NONSSL') . '" class="headerLink"  target="_blank"><img src="images/menu_icon/icon_help_info.gif" alt="img"></a>';
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
echo "<a class='head_link' href = '".tep_href_link(basename($_SERVER['PHP_SELF']),'action=re_login&num='.time(),'NONSSL')."'>";
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
</b>&nbsp;<?php echo HEADER_TEXT_LOGINED;?>&nbsp;
<?php
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
      href="add_note.php?author='.$ocertify->auth_user.'&belong='.$belong.'"
      id="fancy">'.TEXT_ADD_NOTE.'</a></td></tr>


      <tr>
      <td class="menu01"><a class="t_link01"
       href="' . tep_href_link('business_memo.php', '', 'NONSSL') . '"
      >'.HEADER_TEXT_MICRO_LOG.'</a></td>
      </tr><tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PW_MANAGER, '',
    'NONSSL').'">'.HEADER_TEXT_PW_MANAGER.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      onclick="javascript:goto_changepwd(\'changepwd_form\', \''.FILENAME_CHANGEPWD.'\')"
      href="javascript:void(0);">'.HEADER_TEXT_USERS.'</a>';
?>
<?php 
  echo '</td>
     </tr>    
     <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PERSONAL_SETTING, '',
        'NONSSL').'">'.HEADER_TEXT_PERSONAL_SETTING.'</a>';
        
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
