<?php
/*
  $Id$
*/

// check if the 'install' directory exists, and warn of its existence
  if (WARN_INSTALL_EXISTENCE == 'true') {
    if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/install')) {
      tep_output_warning(WARNING_INSTALL_DIRECTORY_EXISTS);
    }
  }
  // check if the configure.php file is writeable
  if (WARN_CONFIG_WRITEABLE == 'true') {
    if ( (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) && (is_writeable(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) ) {
      tep_output_warning(WARNING_CONFIG_FILE_WRITEABLE);
    }
  }
// check if the session folder is writeable
  if (WARN_SESSION_DIRECTORY_NOT_WRITEABLE == 'true') {
    if (STORE_SESSIONS == '') {
      if (!is_dir(tep_session_save_path())) {
        tep_output_warning(WARNING_SESSION_DIRECTORY_NON_EXISTENT);
      } elseif (!is_writeable(tep_session_save_path())) {
        tep_output_warning(WARNING_SESSION_DIRECTORY_NOT_WRITEABLE);
      }
    }
  }
// check session.auto_start is disabled
  if ( (function_exists('ini_get')) && (WARN_SESSION_AUTO_START == 'true') ) {
    if (ini_get('session.auto_start') == '1') {
      tep_output_warning(WARNING_SESSION_AUTO_START);
    }
  }
  if ( (WARN_DOWNLOAD_DIRECTORY_NOT_READABLE == 'true') && (DOWNLOAD_ENABLED == 'true') ) {
    if (!is_dir(DIR_FS_DOWNLOAD)) {
      tep_output_warning(WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT);
    }
  }
?>
<div id="header">
  <div class="h_title">
  <?php
   
  if (!isset($_GET['cPath'])) $_GET['cPath'] = NULL; //del notice
  if (!isset($_GET['products_id'])) $_GET['products_id'] = NULL; //del notice
  if ($_GET['cPath']) {
    echo $seo_category['seo_name'] . ' RMT <a href="javascript:void(0);" onkeypress="SomeJavaScriptCode" style="cursor:hand" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">RMT総合サイト '.STORE_NAME.'をお気に入りに追加して下さい！</a>' . "\n";
  } elseif ($_GET['products_id']) {
    echo ds_tep_get_categories((int)$_GET['products_id'],1) . 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all) {window.external.AddFavorite(location.href,document.title)} else {window.sidebar.addPanel(document.title, location.href,null)}">総合サイト '.STORE_NAME.'をお気に入りに追加して下さい！</a>' . "\n";
  } else {
    echo 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">RMT総合サイト '.STORE_NAME.'をお気に入りに追加して下さい！</a>' . "\n";
  }  
?>
  </div>
  
<?php
if (tep_session_is_registered('customer_id')) {
?>
<div id="title">
  <table cellpadding="0" cellspacing="0" border="0" class="top_right">
  <tr><td>
  	 <ul>
<li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US);?>">お問い合わせ</a></li>
        <li>|</li>
        <li><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>">カートを見る</a></li>
        <li>|</li>
        <li><a href="<?php echo  tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL');?>">レジへ進む
  </a></li>
</ul>
</td></tr>
  <tr><td>
  <div class="header_list"><div class="header_list_title">小計</div><span><?php echo $currencies->format($cart->show_total());?></span></div>
  	 </td></tr></table>
    </div>
  </div>
<?php
}else {
?>
<div id="title">
  <table cellpadding="0" cellspacing="0" border="0" class="top_right">
  <tr><td>
  	 <ul>
     	<li><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'', 'SSL');?>">無料会員登録</a></li>
        <li>|</li>
        <?php /*
        <li><a href="#">ヘルプ</a></li>
        <li>|</li>
        */?>
        <li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US);?>">お問い合わせ</a></li>
        <li>|</li>
        <li><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>">カートを見る</a></li>
        <li>|</li>
        <li><a href="<?php echo  tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL');?>">レジへ進む  </a></li>
        <li>|</li>
        <li><a href="<?php echo tep_href_link(FILENAME_LOGIN, '', 'SSL');?>">ログイン</a></li>
 </ul>
 </td></tr>
 <tr><td>
  <div class="header_list"><div class="header_list_title">小計</div><span><?php echo $currencies->format($cart->show_total());?></span></div>
  </td></tr>
  </table>
  </div>
  </div>
<?php  } ?>
  <div class="header_Navigation">
          <a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a>
          &nbsp;&nbsp;<?php echo $breadcrumb->trail(' &raquo; '); ?>
</div>
<?php
  if (isset($_GET['error_message']) && tep_not_null($_GET['error_message'])) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" summary="headerError">
  <tr class="headerError">
    <td class="headerError"><?php echo htmlspecialchars(urldecode($_GET['error_message'])); ?></td>
  </tr>
</table>
<?php
  }
  if (isset($_GET['info_message']) && tep_not_null($_GET['info_message'])) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" summary="headerInfo">
  <tr class="headerInfo">
    <td class="headerInfo"><?php echo htmlspecialchars($_GET['info_message']); ?></td>
  </tr>
</table>
<?php
  }
?>
