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
<div id="title">
<?php
  if (!isset($_GET['cPath'])) $_GET['cPath'] = NULL; //del notice
  if (!isset($_GET['products_id'])) $_GET['products_id'] = NULL; //del notice
  if ($_GET['cPath']) {
    echo $seo_category['seo_name'] . ' '.TEXT_HEADER_CATEGORY_TITLE. "\n";
  } elseif ($_GET['products_id']) {
    echo ds_tep_get_categories((int)$_GET['products_id'],1) . ' ' .TEXT_HEADER_PRODUCT_TITLE. "\n";
  } else {
    echo sprintf(TEXT_HEADER_OTHER_TITLE,STORE_NAME). "\n";
  } 
?>
</div>
<div id="header">
  <script type='text/javascript' src="js/flash_rmt.js"></script>
  <script type="text/javascript" src="js/images.js"></script>
  <noscript>
    <a href="index.php"><img src="images/jp_picture.gif" width="900" height="70" alt="RMT" ></a>
  </noscript>
   <div id="header_Menu">
    <table width="100%" style="height:39px;" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>
                <?php 
//this is nossl forever $request_type replace to NONSSL
echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '','NONSSL', false), 'get')."\n"; 
?>
          <table>
            <tr>
              <td><img class="middle" src="images/design/button/search_text.jpg" width="53" height="12" alt="<?php echo TEXT_HEADER_SEARCH_IMAGE_ALT;?>"></td>
              <td>
              </td>
              <td><?php echo tep_draw_input_field('keywords', TEXT_HEADER_SEARCH_MUST_INPUT, 'size="30" maxlength="40" id="skeywords" onclick="document.getElementById(\'skeywords\').value = \'\';"'); ?></td>
              <td><input name="imageField" type="image" src="images/design/button/search.jpg" alt="<?php echo TEXT_SEARCH_ALT;?>"></td>
            </tr>
          </table>
<?php 
  echo '<input type="hidden" name="search_in_description" value="1">';
  echo '<input type="hidden" name="inc_subcat" value="1">';
?>
          </form>
        </td>
        <td align="right">
          <table>
            <tr>
              <td><a href="<?php echo tep_href_link('rss.php') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/rss.jpg','RSS') ; ?></a></td>
              <td><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'','SSL') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/shopping_cart.jpg',HEADER_TITLE_CART_CONTENTS);?></a></td>
              <td><a href="<?php echo tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES,'','SSL') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/checkout.jpg',HEADER_TITLE_CHECKOUT);?></a></td>
              <td width="117"  style="background:url(images/design/button/subtotal.jpg);" align="right"><span id="jk-shoppingcart" style="font-size:12px"><?php echo $currencies->format($cart->show_total());?>&nbsp;&nbsp;&nbsp;</span></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
  <div class="header_Navigation">
    <p class="header_Navigation_p"><a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a>&nbsp;&nbsp;
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
    </p>
  </div>
</div>
<?php
  if (isset($_GET['error_message']) && tep_not_null($_GET['error_message'])) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr class="headerError">
    <td class="headerError"><?php echo htmlspecialchars(urldecode($_GET['error_message'])); ?></td>
  </tr>
</table>
<?php
  }
  if (isset($_GET['info_message']) && tep_not_null($_GET['info_message'])) {
?>
<table width="900" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr class="headerInfo">
    <td class="headerInfo"><?php echo htmlspecialchars($_GET['info_message']); ?></td>
  </tr>
</table>
<?php
  }
?>
