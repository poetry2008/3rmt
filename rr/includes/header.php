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
<div id="header"> <div class="h_title">
<?php
  if (!isset($_GET['cPath'])) $_GET['cPath'] = NULL; //del notice
  if (!isset($_GET['products_id'])) $_GET['products_id'] = NULL; //del notice
  if ($_GET['cPath']) {
    echo $seo_category['seo_name'] . ' RMT <a href="javascript:void(0);" onkeypress="SomeJavaScriptCode" style="cursor:hand" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">'.TEXT_HEADER_CATEGORY_TITLE.'</a>' . "\n";
  } elseif ($_GET['products_id']) {
    echo ds_tep_get_categories((int)$_GET['products_id'],1) . 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all) {window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href,null)}">'.TEXT_HEADER_PRODUCT_TITLE.'</a>' . "\n";
  } else {
    echo 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all){window.external.AddFavorite(location.href, document.title)} else{window.sidebar.addPanel(document.title, location.href, null)}">'.TEXT_HEADER_CATEGORY_TITLE.'</a>' . "\n";
  } 
?>
</div>
<div id="title"><img src="images/design/banner.jpg" alt="banner"></div>
  <div id="header_Menu">
      <div class="header_menu_content">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="menu box">
          <tr>
            <td width="432">
                <?php echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get')."\n"; ?>
                <table cellpadding="0" cellspacing="0" summary="search" class="search_info">
                  <tr>
                    <td width="80">&nbsp;</td>
<?php 
  $cat1 = '';
  if ($_GET['cPath']) {
    $cat0 = explode('_', $_GET['cPath']);
  } elseif ($_GET['products_id']) {
    $cat_products = tep_get_product_path($_GET['products_id']);
    $cat0 = explode('_', $cat_products);
  }
  if (!isset($cat0[0])) $cat0[0] = NULL; //del notice
  $cat1 = $cat0[0];
  
  $categories_parent0_query = tep_db_query("
      select * 
      from (
        select c.categories_id, 
               cd.categories_status, 
               cd.categories_name,
               c.sort_order,
               cd.site_id
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '0' 
        and c.categories_id = cd.categories_id 
        and cd.language_id = '" . (int)$languages_id . "' 
        order by sort_order, cd.categories_name, cd.site_id DESC
      ) c
      where site_id = '0'
         or site_id = '".SITE_ID."'
      group by categories_id
      having c.categories_status != '1' and c.categories_status != '3' 
      order by sort_order, categories_name
      ");
  $categories_array = '<select name="categories_id" class="header_search_select">'."\n";
  $categories_array .= '<option value=""';
  if($cat1 == '') {
    $categories_array .= ' selected';
  }
  $categories_array .= '>'.TEXT_HEADER_ALL_CATEGORY.'</option>'."\n";
  while($categories_parent0 = tep_db_fetch_array($categories_parent0_query)) {
    $categories_array .= '<option value="'.$categories_parent0['categories_id'].'"';
    if($cat1 == $categories_parent0['categories_id']) {
      $categories_array .= ' selected';
    }
    $categories_array .= '>'.$categories_parent0['categories_name'].'</option>'."\n";
  }
  $categories_array .= '</select>'."\n";
  //echo $categories_array ;
// --- end add--- //
?>
                    <td><?php echo tep_draw_input_field('keywords', 'RMT', 'class="header_search_input"'); ?></td>
                    <td>
                      <input name="imageField" type="submit" class="header_search_submit" value="" alt="<?php echo TEXT_SEARCH_ALT;?>">
                    </td>
                  </tr>
                </table>
                <?php 
            echo '<input type="hidden" name="search_in_description" value="1">';
            echo '<input type="hidden" name="inc_subcat" value="1">';
        ?>
                </form>
            </td>
            <td class="header_m_login" align="right">     
                <a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'','SSL') ; ?>" class="header_menu_2"><?php echo BOX_HEADING_SHOPPING_CART;?></a>
            </td>
            <td>          
                <a href="<?php echo tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES,'','SSL') ; ?>" class="header_menu_3"><?php echo TEXT_CHECKOUT_LINK;?></a>
            </td>
            <td align="right">
                <div class="header_menu_4"><span id="jk-shoppingcart" style="font-size:12px"><?php echo $currencies->format($cart->show_total());?></span></div>
            </td>
            <td width="40">&nbsp;</td>
          </tr>
        </table>
    </div>
  </div>
  <div class="header_Navigation">
          <a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a>
          &nbsp;&nbsp;
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
