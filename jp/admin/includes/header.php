<?php
/*
   $Id$
 */

if (isset($messageStack) && $messageStack->size > 0) {
  echo $messageStack->output();
}
?>
  <script type="text/javascript">
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
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'ordermenu':
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'managermenu':
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'redirecturl':
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('ordermenu').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        break;
    }
  }
}
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td><?php echo tep_image(DIR_WS_CATALOG .DIR_WS_IMAGES . ADMINPAGE_LOGO_IMAGE, STORE_NAME, '', ''); ?></td>
<td align="right">
<?php echo HEADER_TEXT_SITE_NAME;?>&nbsp;<b>
<?php
//var_dump($ocertify->npermission);
echo "<a href =
'".tep_href_link(basename($GLOBALS['PHP_SELF']),'action=re_login','NONSSL')."'>";
$user_info = tep_get_user_info($ocertify->auth_user);
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
</td>
</tr>
<?php
if(preg_match("/".FILENAME_ORDERS."/",$PHP_SELF)){
  echo tep_minitor_info();
}
?>

<tr class="headerBar">
<td colspan='2'>
<table width="100%">
<tr>
<td class="headerBarContent">&nbsp;&nbsp;<?php 
if (isset($ocertify->npermission) || $ocertify->npermission) {
  echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a>';
}
?></td>
<td class="headerBarContent" align="right">
<?php 
if (!isset($ocertify->npermission) || $ocertify->npermission >= 7) {
  echo '
    <table>
    <tr>
    <td><a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '"
    class="headerLink">'.HEADER_TEXT_ORDERS.'</a>&nbsp;|</td>
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
      <td align="left"> &nbsp;<a class="headerLink" href="javascript:void(0);"
      onclick="toggle_header_menu(\'ordermenu\')">'.HEADER_TEXT_ORDERMENU.'</a>&nbsp;|<br>
      <table class="menu01" id="ordermenu" cellpadding="0" cellspacing="0">
      <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link('create_order.php', '',
    'NONSSL').'">'.HEADER_TEXT_CREATE_ORDER.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01" href="'.tep_href_link('create_order2.php',
    '', 'NONSSL').'">'.HEADER_TEXT_CREATE_ORDER2.'</a></td>
      </tr>
      </table>
      </td>
      <td><a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" 
      class="headerLink">'.HEADER_TEXT_CUSTOMERS.'</a>&nbsp;|</td>
      <td>&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, '', 'NONSSL') .
      '" class="headerLink">'.HEADER_TEXT_LATEST_NEWS.'</a>&nbsp;|</td>
      <td>&nbsp;<a href="' . tep_href_link('micro_log.php', '', 'NONSSL') . '" class="headerLink"
      >'.HEADER_TEXT_MICRO_LOG.'</a>&nbsp;|</td>


      <td align="left">
      &nbsp;<a class="headerLink" href="javascript:void(0);"
      onclick="toggle_header_menu(\'managermenu\')">'.HEADER_TEXT_MANAGERMENU.'</a>&nbsp;|<br>
      <table class="menu01" id="managermenu" cellpadding="0" cellspacing="0">
      <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link(FILENAME_PW_MANAGER, '',
    'NONSSL').'">'.HEADER_TEXT_PW_MANAGER.'</a></td>
      </tr>
      <tr>
      <td class="menu01"><a class="t_link01"
      href="'.tep_href_link('changepwd.php?execute_password='.TEXT_ECECUTE_PASSWORD_USER."&userslist=".$ocertify->auth_user, '', 'NONSSL').'">'.HEADER_TEXT_USERS.'</a></td>
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
  echo '</table>';
  echo ' 
    <td>&nbsp;
  <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') .
    '?execute_logout_user=1" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a></td></tr></table>';
} else {
  echo '|&nbsp;
  <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') .
    '?execute_logout_user=1" class="headerLink">'.HEADER_TEXT_LOGOUT.'</a>';
}
?>
</td>
</tr>
</table>

</td>
</tr>
</table>
