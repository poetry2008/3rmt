<?php
/*
   $Id$
*/

  if (isset($messageStack) && $messageStack->size > 0) {
    echo $messageStack->output();
  }
?>
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
    echo '|&nbsp;
                         '.tep_siteurl_pull_down_menu().'
    &nbsp;|&nbsp;
      <a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="headerLink">' . BOX_CUSTOMERS_ORDERS . '</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link('telecom_unknow.php', '', 'NONSSL') . '" class="headerLink">決算管理</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, '', 'NONSSL') . '" class="headerLink">価格管理</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="headerLink">' . BOX_CUSTOMERS_CUSTOMERS . '</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="headerLink">商品登録</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(FILENAME_LATEST_NEWS, '', 'NONSSL') . '" class="headerLink">新着情報</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link('create_order.php', '', 'NONSSL') . '" class="headerLink">注文作成</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link('micro_log.php', '', 'NONSSL') . '" class="headerLink">引継メモ</a>
      &nbsp;|&nbsp;
      <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') . '?execute_logout_user=1" class="headerLink">ログアウト</a>';
    } else {
    echo '|&nbsp;
      <a href="' . tep_href_link(basename($GLOBALS['PHP_SELF']), '', 'NONSSL') . '?execute_logout_user=1" class="headerLink">ログアウト</a>';
    }
    ?>
    &nbsp;|&nbsp;
  </td>
  </tr>
  </table>
  
  </td>
  </tr>
</table>
