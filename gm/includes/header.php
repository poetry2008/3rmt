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
<div id="out_id" style="display:none;">
    <div class="seach-close"><img onclick="search_close_header()" alt="close" src="images/seach_close.png" width="48" height="44"></div>     
   <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '"
  >' .
   HEADER_TITLE_MY_ACCOUNT . '</a><br>
 '; ?> 
 <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '" >' .
   MYACCOUNT_EDIT . '</a><br> '; ?> 
   <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') .
   '" >' . MYACCOUNT_HISTORY . '</a>'; ?><br>
   <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL')
   . '" >' . MYACCOUNT_NOTIFICATION . '</a>'; ?><br>
   <?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"  >'
   . TEXT_HEADER_SHOPPING_CART_LINK . '</a>'; ?><br>
   <?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '" >' . HEADER_TITLE_LOGOFF . '</a>'; ?>
   
</div>   
<?php
if(!preg_match('/product_info/',$_SERVER['SCRIPT_NAME'])){
?>
<script type="text/javascript" src="banner/js_banner/jquery-1.5.2.js"></script>
<?php
}
?>
<div class="yui3-g" id="header-top">

<?php
if (!isset($_GET['cPath'])) $_GET['cPath']= NULL;
if (!isset($_GET['products_id'])) $_GET['products_id']= NULL;
  if ($_GET['cPath']) {
    echo '<div class="yui3-u-1-3"><div id="business-hour"><img src="images/header_time.gif" alt="time" >' .  sprintf(TEXT_HEADER_TOP_TITLE, $seo_category['seo_name']).'</div></div>' . "\n";
  } elseif ($_GET['products_id']) {
    echo '<div class="yui3-u-1-3"><div id="business-hour"><img src="images/header_time.gif" alt="time" >' . sprintf(TEXT_HEADER_TOP_TITLE, ds_tep_get_categories((int)$_GET['products_id'],1)) . '</div></div>' . "\n";
  } else {
    echo '<div class="yui3-u-1-3"><div id="business-hour"><img src="images/header_time.gif" alt="time">'.TEXT_HEADER_TOP_ANOTHER_TITLE.'</div></div>' . "\n";
  }
?>
	<div class="yui3-u-1-3" id="logo"><a href=""><img src="images/logo.gif"
        onmouseover="this.src='images/logo_hover.gif'"
        onmouseout="this.src='images/logo.gif'"  alt="logo"></a></div>
	<div class="yui3-u-1-3" id="header-payment"><img src="images/header_payment.gif" width="368" height="46" alt="payment"></div>
</div>

<div class="yui3-u" id="header-nav">

  <ul>										  
    <li class="space-1-7"><a href="<?php echo
    tep_href_link(FILENAME_SHOPPING_CART,'','SSL');?>"><img src="images/shopping.png" alt="shopping"onmouseOver="this.src='images/shopping_hover.png'"onmouseOut="this.src='images/shopping.png'" ></a></li>
    <li class="space-2-7">
    <?php
      // add info romaji 
      $co_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = 'guide' and site_id = '".SITE_ID."'"); 
      $co_res = tep_db_fetch_array($co_query); 
      if ($co_res) { 
    ?>
    <a href="<?php echo info_tep_href_link($co_res['romaji']);?>"><img src="images/space.png" alt="space" onmouseOver="this.src='images/space_hover.png'" onmouseOut="this.src='images/space.png'"></a></li>
    <?php
      }
    ?>
   
	<li class="space-3-7"><a href="<?php echo HTTP_SERVER;?>/info/starting_rmt.html"><img src="images/getin.png" alt="qetin"onmouseOver="this.src='images/getin_hover.png'"onmouseOut="this.src='images/getin.png'"></a></li>
	<li class="space-4-7">&nbsp;</li>
  <li class="space-5-7"><a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'','NONSSL');?>"><img src="images/quick.png" alt="quick"onmouseOver="this.src='images/quick_hover.png'"onmouseOut="this.src='images/quick.png'"></a></li>
  <li class="space-6-7">
     <a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><img src="images/login.png" alt="login"onmouseOver="this.src='images/login_hover.png'"onmouseOut="this.src='images/login.png'"></a></li>
    <?php
   if(tep_session_is_registered('customer_id') && $guestchk != '1'){
    ?>
    <li class="space-7-7" >
     <?php echo '<img id="login_click"
     src="images/MENBERS_after.png"
     onmouseOver="this.src=\'images/MENBERS_after_hover.png\'"
     onmouseout="this.src=\'images/MENBERS_after.png\'" onclick="lg()" >'; ?>
         </li>




   <?php
   }else{
   ?>
 
  <li class="space-7-7">
      <a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img
      src="images/MENBERS.png"
      alt="menbers"onmouseOver="this.src='images/MENBERS_hover.png';"onmouseOut="this.src='images/MENBERS.png';"></a> 
            
      </li>
       
      <?php }?>
      
      </ul>

</div>
</div>
<script type="text/JavaScript">
<!--
function popupWindow(url) {
  window.open(url,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=750,height=300,screenX=150,screenY=150,top=150,left=150');
}
//-->
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
<?php
  }
?>
