<?php
/*
  $Id: whats_new.php,v 1.31 2003/02/10 22:31:09 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/


  $random_product_query = "select products_id, products_image, products_tax_class_id, products_price,manufacturers_id from " . TABLE_PRODUCTS . " where products_status = '1' order by products_date_added desc limit " . MAX_RANDOM_SELECT_NEW;

  
  if($random_product = tep_random_select($random_product_query)) {
  
  
?>
<?php
    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);


    if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
    }

    echo '
	<div id="n_border">
	<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], ds_convert_Ajax($random_product['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'align="left"').'</a>	
	<table border="0" cellspacing="4" cellpadding="0">
      <tr>
        <td width="100" class="smallText">商品名</td>
        <td class="main"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']).'">'.ds_convert_Ajax($random_product['products_name']).'</a></td>
      </tr>
      <tr>
        <td class="smallText">メーカー</td>
        <td class="main"><a href="'.tep_href_link(FILENAME_DEFAULT,'manufacturers_id='.$random_product['manufacturers_id']).'" title="'.ds_convert_Ajax(ds_tep_get_manufactures($random_product['manufacturers_id'],1)).'の商品を探す">'.ds_convert_Ajax(ds_tep_get_manufactures($random_product['manufacturers_id'],1)).'</a></td>
      </tr>
      <tr>
        <td class="smallText">販売価格</td>
        <td class="main"><span class="stockWarning">'.ds_convert_Ajax($whats_new_price).'</span></td>
      </tr>
      <tr>
        <td colspan="2"><small>'.ds_convert_Ajax(mb_substr(ds_tep_get_description($random_product['products_id']),0,50)).'</small></td>
        </tr>
      <tr>
        <td colspan="2" align="right"><small><a href="'.tep_href_link(FILENAME_PRODUCTS_NEW,'',NONSSL).'">オススメ商品一覧<img src="images/design/right.gif" width="13" height="13" hspace="3" align="absmiddle" border="0"></a></small></td>
        </tr>
		</table>
	</div>';
?>
<?php
  }
?>
