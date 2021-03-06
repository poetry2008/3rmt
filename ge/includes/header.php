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
<noscript>
<?php tep_output_warning(TEXT_JAVASCRIPT_ERROR);?> 
</noscript>
<div id="header">
<div class="header_div01">
<?php 
if (!isset($_GET['cPath'])) $_GET['cPath']= NULL;
if (!isset($_GET['products_id'])) $_GET['products_id']= NULL;
  if ($_GET['cPath']) {
    echo '<h1 class="header1">'.sprintf(TEXT_HEADER_TOP_TITLE_STRONG,$seo_category['seo_name']) . '</h1>' . "\n";
    echo '<p class="header2">' .  sprintf(TEXT_HEADER_TOP_TITLE,$seo_category['seo_name']) . '</p>' . "\n";
  } elseif ($_GET['products_id']) {
    echo '<h1 class="header1">' .sprintf(TEXT_HEADER_TOP_TITLE_STRONG,ds_tep_get_categories((int)$_GET['products_id'],1)). '</h1>' . "\n";
    echo '<p class="header2">' . sprintf(TEXT_HEADER_TOP_TITLE,ds_tep_get_categories((int)$_GET['products_id'],1)). '</p>' . "\n";
  } else {
    echo '<h1 class="header1">'.TEXT_HEADER_DEFAULT_TITLE.'</h1>' . "\n";
    echo '<p class="header2">'.TEXT_HEADER_DEFAULT_TITLE_CATEGORIES.'</p>' . "\n";
  }
?>
</div>
<script type='text/javascript' src="js/flash_rmt.js"></script>
<noscript>
  <a href="index.php"><img src="images/gm_picture.gif" width="790" height="185" alt="RMT" ></a>
</noscript>
<a name="top"></a>
</div>
<div id="h_menubar">
  <ul>
    <li><a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php 
    echo HEADER_TITLE_SITEMAP;?></a>|</li>
    <li><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'','SSL');?>"><?php 
    echo HEADER_TITLE_SHOPPING_CART;?></a>|</li>
    <li>
    <?php
      // add info romaji 
      $co_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = 'guide' and site_id = '".SITE_ID."'"); 
      $co_res = tep_db_fetch_array($co_query); 
      if ($co_res) { 
    ?>
    <a href="<?php echo info_tep_href_link($co_res['romaji']);?>"><?php 
    echo HEADER_TITLE_CONDITIONS;?></a>|
    <?php
      }
    ?>
    </li>
    <li>
    <?php
      // add info romaji 
      $gu_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = 'profile' and site_id = '".SITE_ID."'"); 
      $gu_res = tep_db_fetch_array($gu_query); 
      if ($gu_res) { 
    ?>
    <a href="<?php echo info_tep_href_link($gu_res['romaji']);?>"><?php echo 
    HEADER_TITLE_ABOUT_US;?></a>|
    <?php
      }
    ?>
    </li>
  <li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'','SSL');?>"><?php echo 
  HEADER_TITLE_CONTACT_US;?></a></li>
  </ul>
</div>
<script type="text/JavaScript">
<!--
function popupWindow(url) {
  window.open(url,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=750,height=300,screenX=150,screenY=150,top=150,left=150');
}
-->
</script>
<?php
  if (isset($_GET['error_message']) && tep_not_null($_GET['error_message'])) {
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="headerError">
  <tr>
    <td><?php echo htmlspecialchars(urldecode($_GET['error_message'])); ?></td>
  </tr>
</table>
<?php
  }
  if (isset($_GET['info_message']) && tep_not_null($_GET['info_message'])) {
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="headerError">
  <tr>
    <td><?php echo htmlspecialchars($_GET['info_message']); ?></td>
  </tr>
</table>
<?php
  }
?>
