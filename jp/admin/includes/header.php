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
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo tep_image(DIR_WS_CATALOG .DIR_WS_IMAGES . ADMINPAGE_LOGO_IMAGE, STORE_NAME, '', ''); ?></td>
    <td align="right">
    株式会社iimy&nbsp;<b>
    <?php
      //var_dump($ocertify->npermission);
      if (isset($ocertify) && $ocertify->npermission == 15) {
        echo '<font color="blue">Admin</font>';
      } elseif (isset($ocertify) && $ocertify->npermission == 10) {
        echo '<font color="red">Chief</font>';
      } else {
        echo 'Staff';
      }
    ?>
    </b>&nbsp;でログインしています。&nbsp;
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
			<td><a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="headerLink">注文一覧</a>&nbsp;|</td>
      		<td><a href="' . tep_href_link('telecom_unknow.php', '', 'NONSSL') . '" class="headerLink">決算履歴</a>&nbsp;|</td>
      		<td onMouseOver="showmenu(\'tutorials\')" onMouseOut="hidemenu(\'tutorials\')" align="left">
				&nbsp;<a class="headerLink" href="javascript:void(0);">商品調整▼</a>&nbsp;|<br>
				<table class="menu01" id="tutorials" cellpadding="0" cellspacing="0">
					<tr>
						<td class="menu01"><a class="t_link01" href="'.tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL').'">商品登録</a></td>
					</tr>
					<tr>
						<td class="menu01"><a class="t_link01" href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN, '', 'NONSSL').'">価格調整</a></td>
					</tr>
					<tr>
						<td class="menu01"><a class="t_link01" href="'.tep_href_link(FILENAME_INVENTORY, '', 'NONSSL').'">在庫水準</a></td>
					</tr>				
				</table>
      		</td>
			<td onMouseOver="showmenu(\'ordermenu\')" onMouseOut="hidemenu(\'ordermenu\')" align="left">
				&nbsp;<a class="headerLink" href="javascript:void(0);">注文書▼</a>&nbsp;|<br>
				<table class="menu01" id="ordermenu" cellpadding="0" cellspacing="0">
					<tr>
						<td class="menu01"><a class="t_link01" href="'.tep_href_link('create_order.php', '', 'NONSSL').'">注文作成</a></td>
					</tr>
					<tr>
						<td class="menu01"><a class="t_link01" href="'.tep_href_link('create_order2.php', '', 'NONSSL').'">仕入作成</a></td>
					</tr>
				</table>
			</td>
	  		<td><a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="headerLink">顧客一覧</a>&nbsp;|</td>
      		<td>&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, '', 'NONSSL') . '" class="headerLink">新着情報</a>&nbsp;|</td>
      		<td>&nbsp;<a href="' . tep_href_link('micro_log.php', '', 'NONSSL') . '" class="headerLink">引継メモ</a>&nbsp;|</td>
			<td>
      &nbsp;'.tep_siteurl_pull_down_menu().' 
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') . '?execute_logout_user=1" class="headerLink">ログアウト</a></td></tr></table>';
    } else {
    echo '|&nbsp;
      <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') . '?execute_logout_user=1" class="headerLink">ログアウト</a>';
    }
    ?>
  </td>
  </tr>
  </table>
  
  </td>
  </tr>
</table>
