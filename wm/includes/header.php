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
<script src="../Scripts/swfobject_modified.js" type="text/javascript"></script>

<div id="title">
  <?php
  if (!isset($_GET['cPath'])) $_GET['cPath'] = NULL; //del notice
  if (!isset($_GET['products_id'])) $_GET['products_id'] = NULL; //del notice
  if ($_GET['cPath']) {
    echo $seo_category['seo_name'] . ' RMT <a href="javascript:void(0);" onkeypress="SomeJavaScriptCode" style="cursor:hand" onclick="if (document.all) {window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">RMT総合サイト ワールドマネーをお気に入りに追加して下さい！</a>' . "\n";
  } elseif ($_GET['products_id']) {
    echo ds_tep_get_categories((int)$_GET['products_id'],1) . 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all) {window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">総合サイト ワールドマネーをお気に入りに追加して下さい！</a>' . "\n";
  } else {
    echo 'RMT <a href="javascript:void(0);" style="cursor:hand" onkeypress="SomeJavaScriptCode" onclick="if (document.all) {window.external.AddFavorite(location.href, document.title)} else {window.sidebar.addPanel(document.title, location.href, null)}">RMT総合サイト ワールドマネーをお気に入りに追加して下さい！</a>' . "\n";
  }  
?>
</div>
<div id="header">
  <div id="header_flash">
    <ul>
      <li class="header_flash_left"></li>
      <li class="header_flash_content">
        <table cellpadding="0" cellspacing="0" border="0" summary="logo">
          <tr>
            <td>
              <a href="http://rmt.worldmoney.jp/"><img src="images/design/logo.jpg" width="200" height="103" alt="RMT" ></a>
            </td>
            <td>
                <object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="662" height="103">
                  <param name="movie" value="images/design/header_flash.swf" >
                  <param name="quality" value="high" >
                  <param name="wmode" value="opaque" >
                  <param name="swfversion" value="8.0.35.0" >
                  <param name="expressinstall" value="Scripts/expressInstall.swf" >
                </object>
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="images/design/header_flash.swf" width="662" height="103">                  
                  <param name="quality" value="high" >
                  <param name="wmode" value="opaque" >
                  <param name="swfversion" value="8.0.35.0" >
                  <param name="expressinstall" value="Scripts/expressInstall.swf" >
                </object>
                <!--<![endif]-->
            </td>
          </tr>
        </table>
      </li>
      <li class="header_flash_right"></li>
    </ul>
  </div>
  <div id="header_Menu">
    <ul>
      <li class="header_menu_left"></li>
      <li class="header_menu_content">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="menu box" style="margin-top: 11px;">
          <tr>
            <td width="432">
              <div style="width: 432px; height: 43px; overflow:hidden;">
                <?php echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get')."\n"; ?>
                <table style="margin-top: 3px;" cellpadding="2" cellspacing="2" summary="search">
                  <tr>
                    <td><img class="middle" src="images/design/button/search_text.gif" width="76" height="25" alt="RMT検索"></td>
                    <td>
                      <?php
// --- get categoris list ( parent_id = 0 ) --- //
  $cat1 = '';
  if ($_GET['cPath']) {
    $cat0 = explode('_', $_GET['cPath']);
  } elseif ($_GET['products_id']) {
    $cat_products = tep_get_product_path($_GET['products_id']);
    $cat0 = explode('_', $cat_products);
  }
  if (!isset($cat0[0])) $cat0[0] = NULL; //del notice
  $cat1 = $cat0[0];
  // ccdd
  $categories_parent0_query = tep_db_query("
      select * 
      from (
        select c.categories_id, 
               c.categories_status, 
               cd.categories_name,
               c.sort_order,
               cd.site_id
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '0' 
        and c.categories_status != '1' 
        and c.categories_id = cd.categories_id 
        and cd.language_id = '" . (int)$languages_id . "' 
        order by sort_order, cd.categories_name, cd.site_id DESC
      ) c
      where site_id = '0'
         or site_id = '".SITE_ID."'
      group by categories_id
      order by sort_order, categories_name
      ");
  $categories_array = '<select name="categories_id" class="header_search_select">'."\n";
  $categories_array .= '<option value=""';
  if($cat1 == '') {
    $categories_array .= ' selected';
  }
  $categories_array .= '>全てのゲーム</option>'."\n";
  while($categories_parent0 = tep_db_fetch_array($categories_parent0_query)) {
    $categories_array .= '<option value="'.$categories_parent0['categories_id'].'"';
    if($cat1 == $categories_parent0['categories_id']) {
      $categories_array .= ' selected';
    }
    $categories_array .= '>'.$categories_parent0['categories_name'].'</option>'."\n";
  }
  $categories_array .= '</select>'."\n";
  echo $categories_array ;
// --- end add--- //
?>
                    </td>
                    <td><?php echo tep_draw_input_field('keywords', 'RMT', 'class="header_search_input"'); ?></td>
                    <td>
                      <input name="imageField" type="submit" class="header_search_submit" value="" alt="検索">
                    </td>
                  </tr>
                </table>
                <?php 
            echo '<input type="hidden" name="search_in_description" value="1">';
            echo '<input type="hidden" name="inc_subcat" value="1">';
            echo tep_hide_session_id(); 
        ?>
                </form>
              </div>
            </td>
            <td align="right" width="372">
              <div style=" width:372px; overflow: hidden; height: 43px;">
                <table summary="menu">
                  <tr>
                    <td>
                      <a href="<?php echo tep_href_link('rss.php') ; ?>" class="header_menu_1">
                      <?php //echo tep_image(DIR_WS_IMAGES.'design/button/rss.gif','RSS') ; ?>
                      </a>
                      <a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'','SSL') ; ?>" class="header_menu_2">
                      <?php //echo tep_image(DIR_WS_IMAGES.'design/button/shopping_cart.gif',HEADER_TITLE_CART_CONTENTS);?>
                      </a>
                      <a href="<?php echo tep_href_link(FILENAME_CHECKOUT_PRODUCTS,'','SSL') ; ?>" class="header_menu_3">
                      <?php //echo tep_image(DIR_WS_IMAGES.'design/button/checkout.gif',HEADER_TITLE_CHECKOUT);?>
                      </a>
                    </td>
                    <td width="88" align="right" style="background-repeat: no-repeat;"><span id="jk-shoppingcart" style="font-size:12px"><?php echo $currencies->format($cart->show_total());?></span></td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
        </table>
      </li>
      <li class="header_menu_right"></li>
    </ul>
  </div>
  <div class="header_Navigation">
    <ul>
      <li class="header_navigation_left"></li>
      <li class="header_navigation_content">
        <p class="header_Navigation_p">
          <a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a>
          &nbsp;&nbsp;<?php echo $breadcrumb->trail(' &raquo; '); ?></p>
      </li>
      <li class="header_navigation_right"></li>
    </ul>
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
