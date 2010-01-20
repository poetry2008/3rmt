<?php
/*
  $Id: new_products.php,v 1.2 2003/05/02 12:02:47 ptosh Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
  <meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
*/
	$categories_path = explode('_', $HTTP_GET_VARS['cPath']);
	$_categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path[0]."' and language_id = '".$languages_id."'");
	$_categories = tep_db_fetch_array($_categories_query);
	$new_c_name = $_categories['categories_name'];

	if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
		$new_products_query = tep_db_query("select p.products_id, p.products_quantity, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
	} else {
		$new_products_query = tep_db_query("select distinct p.products_id, p.products_quantity, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . $new_products_category_id . "' and p.products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
	}
	
	$num_products = tep_db_num_rows($new_products_query);
  if (0 === $num_products) {
    $subcategories = array();
    $subcategory_query = tep_db_query("select * from " . TABLE_CATEGORIES . " where parent_id=" . $new_products_category_id);
    while($subcategory = tep_db_fetch_array($subcategory_query)){
      $subcategories[] = $subcategory['categories_id'];
    }
    if ($subcategories) {
      $new_products_query = tep_db_query("select distinct p.products_id, p.products_quantity, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id in (" . join(',', $subcategories) . ") and p.products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
      $num_products       = tep_db_num_rows($new_products_query);
    }
  }
	if (0 < $num_products) {
		$info_box_contents = array();
		$info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));
	
		//   new contentBoxHeading($info_box_contents);
	
		$row = 0;
		$col = 0;
?>
<!-- new_products //-->
<h3 class="pageHeading"><?php echo $new_c_name; ?>の新着商品</h3>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?php
		while ($new_products = tep_db_fetch_array($new_products_query)) {
			$product_query = tep_db_query("select products_name, products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $new_products['products_id'] . "' and language_id = '" . $languages_id . "'");
			$product_details = tep_db_fetch_array($product_query);
	
			$new_products['products_name'] = $product_details['products_name'];
			// edit 2009.5.14 maker
			//$description_array = explode("|-#-|", $product_details['products_description']); //maker
			//$description_view = strip_tags(mb_substr($description_array[0],0,110));
			$description_view = strip_tags(mb_substr($product_details['products_description'],0,110));
	
			//$new_products['products_description'] = strip_tags(mb_substr ($product_details['products_description'],0,125));
	
			$row ++;
			// if (($row/2) == floor($row/2)) { $margin = 'style="margin-right:20px;"'; }
			//      $info_box_contents[$row][$col] = array('align' => 'center',
			//                                             'params' => 'class="smallText" width="33%" valign="top"',
			//                                             'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"') . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . $new_products['products_name'] . '</a><br>' . $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])));
?>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="<?=SMALL_IMAGE_WIDTH?>" rowspan="2" style="padding-right:8px; " align="center">
						<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?>
					</td>
					<td height="40" colspan="2" valign="top" style="padding-left:5px; ">
						<p class="main">
							<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
							<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">'.$new_products['products_name'].'</a>'; ?><br>
							<span class="smallText"><?php echo $description_view; ?>...</span>
						</p>
					</td>
				</tr>
				<tr>
					<td class="main" style="padding-left:5px; ">
						<p class="main">
							<?php echo '残り&nbsp;' . number_format($new_products['products_quantity']) . '個'; ?>
							&nbsp;&nbsp;&nbsp;
							<span class="red"><?php echo $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) ; ?></span>
						</p>
					</td>
					<td align="right">
						<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_description.jpg',IMAGE_BUTTON_DEC,'81','24'); ?></a>
					</td>
				</tr>
			</table>
			<br>
			<div class="dot">&nbsp;</div>
		</td>
	</tr>			
<?php      
		/*	 if (($row/2) == floor($row/2)) {
		echo '</tr>'."\n".'<tr>' ;
		} else {
		echo '<td>'.tep_draw_separator('pixel_trans.gif', '10', '1').'</td>'."\n";
		}  
		*/
		}
	
		//new contentBox($info_box_contents);
		echo '</table>' . "\n";
if($num_products && 0){?>
<div align="right" style="padding: 5px 10px 0px 0px;">
    	<a href="/pl-<?php echo $categories_path[count($categories_path)-1];?>.html">more</a>
</div>
<?php }?>
<p class="pageBottom"></p>
<!-- new_products_eof //-->
<?php
	}
?>

