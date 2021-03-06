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
  <div class="h_title">
  <?php
   
  if (!isset($_GET['cPath'])) $_GET['cPath'] = NULL; //del notice
  if (!isset($_GET['products_id'])) $_GET['products_id'] = NULL; //del notice
  if ($_GET['cPath']) {
    echo $seo_category['seo_name'] . ' RMT <a href="javascript:void(0);" onkeypress="SomeJavaScriptCode" style="cursor:hand" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">'.TEXT_HEADER_CATEGORY_TITLE.'</a>' . "\n";
  } elseif ($_GET['products_id']) {
    echo ds_tep_get_categories((int)$_GET['products_id'],1) . 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all) {window.external.AddFavorite(location.href,document.title)} else {window.sidebar.addPanel(document.title, location.href,null)}">'.TEXT_HEADER_PRODUCT_TITLE.'</a>' . "\n";
  } else {
    echo 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">'.TEXT_HEADER_CATEGORY_TITLE.'</a>' . "\n";
  }  
?>
  </div>
  
<?php
if (tep_session_is_registered('customer_id')) {
  if ($guestchk == '1') {
?>
<div id="title">
  <table cellpadding="0" cellspacing="0" border="0" class="top_right">
  <tr><td>
  	 <ul>
<li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US, '', 'SSL');?>"><?php echo BOX_INFORMATION_CONTACT;?></a></li>
        <li>|</li>
        <li><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>"><?php echo HEADER_TITLE_CART_CONTENTS;?></a></li>
        <li>|</li>
        <li><a href="<?php echo  tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES, '', 'SSL');?>"><?php echo TEXT_CHECKOUT_LINK;?></a></li>
</ul>
</td></tr>
<tr><td align="right" class="header_w_time"><img src="images/design/header_worktime.gif" alt="worktime"></td></tr>
<tr><td align="right" class="header_w_time02"><img src="images/design/work_img01.gif" alt="<?php echo TEXT_HEADER_WORK_PIC_ALT;?>"><img src="images/design/work_img02.gif" alt="<?php echo TEXT_HEADER_OTHER_WORK_PIC_ALT;?>"></td></tr>
         </table>
    </div>
  </div>
<?php
 } else {
 ?>
   <div id="title">    
  <table cellpadding="0" cellspacing="0" border="0" class="top_right">
  <tr><td>
   <ul class="login_list">
          <li class="login_list01"><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') .  '">' . HEADER_TITLE_LOGOFF . '</a>'."\n"; ?></li>
        <li>|</li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'."\n"; ?></li>
        <li>|</li>
		  <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'."\n"; ?></li>
        <li>|</li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'."\n"; ?></li>
        <li>|</li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'."\n"; ?></li>
           </ul>
</td>
</tr>
<tr><td align="right" class="header_w_time"><img src="images/design/header_worktime.gif" alt="worktime"></td></tr>
<tr><td align="right" class="header_w_time02"><img src="images/design/work_img01.gif" alt="<?php echo TEXT_HEADER_WORK_PIC_ALT;?>"><img src="images/design/work_img02.gif" alt="<?php echo TEXT_HEADER_OTHER_WORK_PIC_ALT;?>"></td></tr>
   </table> 
           </div>
           </div>
 <?php
 }
}else {
?>
<div id="title">
  <table cellpadding="0" cellspacing="0" border="0" class="top_right">
  <tr><td class="top_right_01">
        <div class="top_right_02"><a href="<?php echo tep_href_link(FILENAME_LOGIN, '', 'SSL');?>"><?php echo HEADER_TITLE_LOGIN;;?></a></div>
     	<div class="top_right_03"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'', 'SSL');?>"><?php echo TEXT_HEADER_CREATE_ACCOUNT;?></a></div>
 </td></tr>
<tr><td align="right" class="header_w_time"><img src="images/design/header_worktime.gif" alt="worktime"></td></tr>
<tr><td align="right" class="header_w_time02"><img
src="images/design/work_img01.gif" alt="<?php echo TEXT_HEADER_WORK_PIC_ALT;?>"><img src="images/design/work_img02.gif" alt="<?php echo TEXT_HEADER_OTHER_WORK_PIC_ALT;?>"></td></tr>
  </table>
  </div>
  </div>
<?php  } ?>
  <div class="header_Navigation">
  		<ul>
        	<li><a href="<?php echo tep_href_link(FILENAME_DEFAULT)?>"><?php echo TEXT_HEADER_INDEX_LINK;?></a></li>
            <li>|</li>
            <?php
            $summary_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and site_id = ".SITE_ID." and romaji = 'companyprofile'"); 
            $summary_res = tep_db_fetch_array($summary_query); 
            if ($summary_res) {
            ?>
            <li><a href="<?php echo info_tep_href_link($summary_res['romaji']);?>"><?php echo $summary_res['heading_title']?></a></li>
            <li>|</li>
            <?php
            }
            ?>
            <?php
            //$faq_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and site_id = ".SITE_ID." and romaji = 'faq'"); 
            //$faq_info_res = tep_db_fetch_array($faq_info_query); 
            //if ($faq_info_res) {
            ?>
            <li><a href="<?php echo HTTP_SERVER.'/faq/';?>">FAQ</a></li>
            <li>|</li>
            <?php
            //}
            ?>
            <li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US, '', 'SSL')?>"><?php echo BOX_INFORMATION_CONTACT?></a></li>
            <li>|</li>
            <li><a href="<?php echo tep_href_link('reorder.php')?>"><?php echo RIGHT_ORDER_TEXT;?></a></li>
            <li>|</li>
            <li><a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a></li>
        </ul>
        <div class="header_bread">
        <?php 
        if ($_SERVER['PHP_SELF'] == '/change_preorder_confirm.php') {
          echo '<a href="'.tep_href_link(FILENAME_DEFAULT,'','NONSSL').'" class="headerNavigation">'.HEADER_TITLE_TOP.'</a>'; 
          echo ' &raquo; ';
          echo '<a href="javascript:void(0);" class="headerNavigation" onclick="document.forms.order1.submit();">'.CHANGE_PREORDER_BREADCRUMB_FETCH.'</a>';
          echo ' &raquo; ';
          echo NAVBAR_CHANGE_PREORDER_TITLE; 
        } else {
          echo $breadcrumb->trail(' &raquo; '); 
        }
        ?>
        </div>
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
