<?php
/*
	JP、GM共通ファイル
*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo tep_image(DIR_WS_CATALOG .DIR_WS_IMAGES . ADMINPAGE_LOGO_IMAGE, STORE_NAME, '', ''); ?></td>
    <td align="right">
		株式会社iimy&nbsp;<b>
		<?php
			if ($ocertify->npermission == 15) {
				echo '<font color="blue">Admin</font>';
			} elseif ($ocertify->npermission == 10) {
				echo '<font color="red">Chief</font>';
			} else {
				echo 'Staff';
			}
		?>
		</b>&nbsp;でログインしています。&nbsp;
	</td>
  </tr>
  <tr class="headerBar">
    <td class="headerBarContent">&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a>'; ?></td>
    <td class="headerBarContent" align="right">
		<?php echo '|&nbsp;&nbsp;
                         '.tep_siteurl_pull_down_menu().'
		&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="headerLink">' . BOX_CUSTOMERS_ORDERS . '</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="headerLink">' . BOX_CUSTOMERS_CUSTOMERS . '</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="headerLink">商品登録</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link(FILENAME_LATEST_NEWS, '', 'NONSSL') . '" class="headerLink">' . BOX_TOOLS_LATEST_NEWS . '</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link('create_order.php', '', 'NONSSL') . '" class="headerLink">注文作成</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') . '?execute_logout_user=1" class="headerLink">ログアウト</a>';
//			<a href="' . tep_catalog_href_link() . '" class="headerLink" target="_blank">' . HEADER_TITLE_ONLINE_CATALOG . '</a>
		?>
		&nbsp;&nbsp;|&nbsp;&nbsp;
	</td>
  </tr>
</table>