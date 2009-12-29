<?php
/*
  $Id: reviews.php,v 1.37 2003/06/09 22:20:28 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

//require('includes/application_top.php');
?>
<!-- reviews //-->
<?php
  $random_select = "select r.reviews_id, r.reviews_rating, p.products_id, p.products_image,p.manufacturers_id, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and r.reviews_status = '1'";
  $random_select .= " order by r.reviews_id desc limit " . MAX_RANDOM_SELECT_REVIEWS;
  $random_product = tep_random_select($random_select);


  if ($random_product) {
// display random review box
    $review_query = tep_db_query("select substring(reviews_text, 1, 60) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$random_product['reviews_id'] . "' and languages_id = '" . (int)$languages_id . "'");
    $review = tep_db_fetch_array($review_query);

    echo '
	<div id="n_border">
	<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], ds_convert_Ajax($random_product['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'align="left"') . '</a>	
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
        <td class="smallText">評価</td>
        <td class="main">'.tep_image(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' ,ds_convert_Ajax( sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating']))).'</td>
      </tr>
      <tr>
        <td colspan="2"><small><img src="images/design/review.gif" width="26" height="27" align="right">'.ds_convert_Ajax(tep_break_string(tep_output_string_protected($review['reviews_text']), 15, '-<br>')).'</small></td>
        </tr>
    </table>
	</div>';
  } else {
    $info_box_contents[] = array('text' => BOX_REVIEWS_NO_REVIEWS);
  }
?>

<!-- reviews_eof //-->
