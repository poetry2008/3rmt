<?php
/*
  $Id$

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
<div id="title">
<?php
if (!isset($HTTP_GET_VARS['cPath'])) $HTTP_GET_VARS['cPath'] = NULL;
if (!isset($HTTP_GET_VARS['products_id'])) $HTTP_GET_VARS['products_id'] = NULL;
	if ($HTTP_GET_VARS['cPath']) {
		echo $seo_category['seo_name'] . ' RMT ジャックポットは安全で安心・信頼できる取り引きを目指していきます。' . "\n";
	} elseif ($HTTP_GET_VARS['products_id']) {
		echo ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) . ' ジャックポットは安全で安心・信頼できる取り引きを目指していきます。' . "\n";
	} else {
		echo 'RMT専門店！ RMTジャックポットは安全で安心・信頼できる取り引きを目指していきます。' . "\n";
	}	
?>
</div>
<div id="header">
	<script type="text/javascript" src="js/rmt_flash.js"></script>
	<script type="text/javascript" src="js/images.js"></script>
	<noscript>
		<a href="index.php"><img src="images/rmt.gif" width="185" height="65" alt="RMT" ></a>
	</noscript>
	<div id="header_Menu">
		<table width="100%" style="height:39px;" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<?php echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get')."\n"; ?>
					<table>
						<tr>
							<td><img class="middle" src="images/design/button/search_text.jpg" width="53" height="12" alt="RMT検索"></td>
							<td>
<?php
// --- get categoris list ( parent_id = 0 ) --- //
	$cat1 = '';
	if ($HTTP_GET_VARS['cPath']) {
		$cat0 = explode('_', $HTTP_GET_VARS['cPath']);
	} elseif ($HTTP_GET_VARS['products_id']) {
		$cat_products = tep_get_product_path($HTTP_GET_VARS['products_id']);
		$cat0 = explode('_', $cat_products);
	}
if (!isset($cat0[0])) $cat0[0] = NULL;
	$cat1 = $cat0[0];
	$categories_parent0_query = tep_db_query("select c.categories_id, c.categories_status, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.site_id = '" . SITE_ID . "' and c.parent_id = '0' and c.categories_status = '0' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name and cd.site_id = '".SITE_ID."'");
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
							<td><?php echo tep_draw_input_field('keywords', 'RMT', 'size="20" maxlength="40"'); ?></td>
							<td><input name="imageField" type="image" src="images/design/button/search.jpg" alt="検索"></td>
						</tr>
					</table>
<?php 
	echo '<input type="hidden" name="search_in_description" value="1">';
	echo '<input type="hidden" name="inc_subcat" value="1">';
	echo tep_hide_session_id(); 
?>
					</form>
				</td>
				<td align="right">
					<table>
						<tr>
							<td><a href="<?php echo tep_href_link('rss.php') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/rss.jpg','RSS') ; ?></a></td>
							<td><a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART,'','NONSSL') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/shopping_cart.jpg',HEADER_TITLE_CART_CONTENTS);?></a></td>
							<td><a href="<?php echo tep_href_link(FILENAME_CHECKOUT_PRODUCTS,'','SSL') ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/checkout.jpg',HEADER_TITLE_CHECKOUT);?></a></td>
							<td width="117"  background="images/design/button/subtotal.jpg" align="right"><span id="jk-shoppingcart" style="font-size:12px"><?php echo $currencies->format($cart->show_total());?>&nbsp;&nbsp;&nbsp;</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="header_Navigation">
		<p class="header_Navigation_p"><a href="<?php echo tep_href_link(FILENAME_SITEMAP,'','NONSSL');?>"><?php echo HEADER_TITLE_SITEMAP ; ?></a>&nbsp;&nbsp;<?php echo $breadcrumb->trail(' &raquo; '); ?></p>
	</div>
</div>
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
<table width="900" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr class="headerInfo">
		<td class="headerInfo"><?php echo htmlspecialchars($HTTP_GET_VARS['info_message']); ?></td>
	</tr>
</table>
<?php
	}
?>
