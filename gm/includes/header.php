<?php
/*
  $Id: header.php,v 1.2 2003/07/04 01:14:11 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// check if the 'install' directory exists, and warn of its existence
  if (WARN_INSTALL_EXISTENCE == 'true') {
    if (file_exists(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/install')) {
      tep_output_warning(WARNING_INSTALL_DIRECTORY_EXISTS);
    }
  }
  // check if the configure.php file is writeable
  if (WARN_CONFIG_WRITEABLE == 'true') {
    if ( (file_exists(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php')) && (is_writeable(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php')) ) {
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
<div class="header_div01">
<?php 
  if ($HTTP_GET_VARS['cPath']) {
    echo '<p class="header1"><strong>' . $seo_category['seo_name'] . '-RMT</strong> �¤������֡��ǰ���-�������̲ߤη������</p>' . "\n";
    echo '<p class="header2">' . $seo_category['seo_name'] . '��������-RMT-������ޥ͡�</p>' . "\n";
  } elseif ($HTTP_GET_VARS['products_id']) {
    echo '<p class="header1"><strong>' . ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) . '</strong> �¤������֡��ǰ���-�������̲ߤη������</p>' . "\n";
    echo '<p class="header2">' . ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) . '��������-RMT-������ޥ͡�</p>' . "\n";
  } else {
    echo '<h1 class="header1">RMT �ǰ���-�����ƥ�η������</h1>' . "\n";
    echo '<p class="header2">FF11����͡�����2����åɥ��ȡ���AION�������� - RMT������ޥ͡�</p>' . "\n";
  }
?>
</div>
<?php
/*
  if ($HTTP_GET_VARS['cPath']) {
    echo '<p class="header1"><strong>' . $seo_category['seo_name'] . '-RMT</strong> �¤������֡��ǰ���-�������̲ߤη������</p>' . "\n";
    echo '<p class="header2">' . $seo_category['seo_name'] . '��������-RMT-������ޥ͡�</p>' . "\n";
  } elseif ($HTTP_GET_VARS['products_id']) {
    echo '<p class="header1"><strong>' . ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) . '</strong> �¤������֡��ǰ���-�������̲ߤη������</p>' . "\n";
    echo '<p class="header2">' . ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) . '��������-RMT-������ޥ͡�</p>' . "\n";
  } else {
    echo '<p class="header1"><strong>RMT</strong>�ǰ���-�����ƥ�η������</p>' . "\n";
    echo '<p class="header2">FF11����͡�����2����åɥ��ȡ���AION�������� - RMT������ޥ͡�</p>' . "\n";
  }
*/
?>
<script type="text/javascript">
<!--
fflag=0;
if (document.layers || document.all || document.getElementById) {
 if (!fflag && document.all && !window.opera && navigator.userAgent.indexOf('Win')>-1) {
 document.write('<scr' + 'ipt type="text/vbscript"\> \n');
 document.write('on error resume next \n');
 document.write('fflag=( IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6")))\n');
 document.write('<' + '/scr' + 'ipt> \n');
 }
 else if (navigator.plugins['Shockwave Flash']) fflag=1;
 }

if (fflag==0)
{
 document.write('<a href="index.php"><img alt="RMT" src="images/design/oc/header.jpg" width="790" height="185" border="0"><'+'/a>');
}else {
 if (document.all && !window.opera)
 document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="790" height="185">'+
 '<param name="movie" value="images/design/header/header.swf" />'+
 '<param name="wmode" value="transparent" />'+
 '<param name="quality" value="high" />'+
 '<'+'/object>'+
 '');
 else
 document.write('<object type="application/x-shockwave-flash" data="images/design/header/header.swf" width="790" height="185">'+
 '<param name="wmode" value="transparent" />'+
 '<param name="quality" value="high" />'+
 '<'+'/object>'+
 '');
 }
-->
</script>
<a name="top"></a>
</div>
<div id="h_menubar">
  <ul>
    <li><a href="<?php echo tep_href_link(FILENAME_SITEMAP,'',NONSSL);?>">�����ȥޥå�</a>|</li>
    <li><a href="<?php echo tep_href_link(FILENAME_LATEST_NEWS,'',NONSSL);?>">���Τ餻</a>|</li>
    <li><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'',NONSSL);?>">����åԥ󥰥�����</a>|</li>
    <li>
    <?php
      // add info romaji 
      $co_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '26'"); 
      $co_res = tep_db_fetch_array($co_query); 
      if ($co_res) { 
    ?>
    <a href="<?php echo info_tep_href_link($co_res['romaji']);?>">��������ˡ</a>|
    <?php
      }
    ?>
    </li>
    <li>
    <?php
      // add info romaji 
      $gu_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '25'"); 
      $gu_res = tep_db_fetch_array($gu_query); 
      if ($gu_res) { 
    ?>
    <a href="<?php echo info_tep_href_link($gu_res['romaji']);?>">��ҳ���</a>|
    <?php
      }
    ?>
    </li>
  <li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'',NONSSL);?>">���䤤��碌</a></li>
  </ul>
</div>
<script type="text/JavaScript">
<!--
function popupWindow(url) {
  window.open(url,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=750,height=300,screenX=150,screenY=150,top=150,left=150');
}
//-->
</script>
<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message'])) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr class="headerError">
    <td class="headerError"><?php echo htmlspecialchars(urldecode($HTTP_GET_VARS['error_message'])); ?></td>
  </tr>
</table>
<?php
  }
  if (isset($HTTP_GET_VARS['info_message']) && tep_not_null($HTTP_GET_VARS['info_message'])) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr class="headerInfo">
    <td class="headerInfo"><?php echo htmlspecialchars($HTTP_GET_VARS['info_message']); ?></td>
  </tr>
</table>
<?php
  }
?>
