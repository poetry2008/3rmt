<!-- latest_news //-->
<div align="center" class="marginTop5"><?php echo tep_image(DIR_WS_IMAGES.'design/product_new.gif','What\'s New');?></div>
<table width="498"  border="0" align="center" cellpadding="0" cellspacing="0">
<tr><td>



<?php
  $latest_news_query = tep_db_query('SELECT * from ' . TABLE_LATEST_NEWS . ' WHERE status = 1 ORDER BY date_added DESC LIMIT ' . MAX_DISPLAY_LATEST_NEWS);

  if (!tep_db_num_rows($latest_news_query)) { // there is no news
    echo '<!-- ' . TEXT_NO_LATEST_NEWS . ' -->';
  } else {
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => TABLE_HEADING_LATEST_NEWS);
 //   new contentBoxHeading($info_box_contents);

    $info_box_contents = array();
    $row = 0;
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
	  $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', $latest_news['headline'], '15', '15');
	  } else {
	  $latest_news_image = '';
	  }
	  
	  $info_box_contents[$row] = array('align' => 'left',
                                       'params' => 'class="smallText" valign="top"',
									   'text' =>
                                       tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');
      echo '<tr><td class="dotBorder"><h3 class="Tlist">'.tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . '</a></h3></td></tr>';
	  $row++;
    }
  //  new contentBox($info_box_contents);

 }

 
?> 
</table>
<!-- latest_news_eof //--> 


<?php
/*
  $Id: whats_new.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

?>
<!-- whats_new //-->
<style type="text/css">
<!--
#small_description {
	text-align: left;
	text-indent: 1pt;
}
#what_new_price {
	color: #D3565C;
}
#whats_new_line_border {
	border-right-width: 1px;
	border-right-style: solid;
	border-right-color: #B3B3B3;
}
-->
</style>

<div align="center" class="marginTop5"><?php echo tep_image(DIR_WS_IMAGES.'design/new_product.gif','What\'s New');?></div>
<!-- whats_new_eof //-->
<table width="498" border="0" align="center" cellpadding="10" cellspacing="5">
  <tr>
    <td width="33%" class="smallText" id="whats_new_line_border"><?php

  if ($random_product = tep_random_select("select products_id, products_image, products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_status = '1' order by products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {


    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);
    $random_product['products_description'] = tep_get_products_description($random_product['products_id']);
    
	$info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);


    if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
    }

    //$info_box_contents = array();
    $info_box_contents= array('align' => 'center',
                                 'text' => '<center><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></center><br><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . $random_product['products_name'] . '</a><br><span id="what_new_price">' . $whats_new_price.'</span><br><small id="small_description">'.substr(strip_tags($random_product['products_description']),0,100).'...</small>');

    echo $info_box_contents['text'];
	//new infoBox($info_box_contents);
  }
?></td>
    <td width="33%" class="smallText" id="whats_new_line_border">      <?php

  if ($random_product = tep_random_select("select products_id, products_image, products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_status = '1' order by products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {


    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);
    $random_product['products_description'] = tep_get_products_description($random_product['products_id']);
    
	$info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);


    if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
    }

    //$info_box_contents = array();
    $info_box_contents= array('align' => 'center',
                                 'text' => '<center><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></center><br><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . $random_product['products_name'] . '</a><br>' . $whats_new_price.'<br><small id="small_description">'.substr(strip_tags($random_product['products_description']),0,100).'...</small>');

    echo $info_box_contents['text'];
	//new infoBox($info_box_contents);
  }
?>    </td>
    <td width="33%" class="smallText">      <?php

  if ($random_product = tep_random_select("select products_id, products_image, products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_status = '1' order by products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {


    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);
    $random_product['products_description'] = tep_get_products_description($random_product['products_id']);
    
	$info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);


    if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
    }

    //$info_box_contents = array();
    $info_box_contents= array('align' => 'center',
                                 'text' => '<center><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></center><br><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id'].'ref='.$_GET['ref']) . '">' . $random_product['products_name'] . '</a><br>' . $whats_new_price.'<br><small id="small_description">'.substr(strip_tags($random_product['products_description']),0,100).'...</small>');

    echo $info_box_contents['text'];
	//new infoBox($info_box_contents);
  }
?>    </td>
  </tr>
</table>
<br><br>
<?php
/*
  $Id: also_purchased_products.php,v 1.1.1.1 2003/02/20 01:03:54 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  
  
		$categories_tab_query1 = tep_db_query("select c.categories_id, c.parent_id, c.categories_image, cd.categories_name, cd.categories_meta_text, cd.categories_image2 from categories c, categories_description cd where c.categories_id = cd.categories_id and c.parent_id = '0' and  cd.language_id='" . (int)$languages_id ."' order by sort_order");
?>
<!-- categories_banner_text //-->
<?php echo '<div align="center">'.tep_image(DIR_WS_IMAGES.'design/item_lineup.gif').'</div>';?>
<table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
<tr> 
  <?php 
	$number_of_categories = 0 ;
	$col = 0 ;
	while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
      echo '<td valign="top"><a href="'.tep_href_link(FILENAME_DEFAULT,'cPath=' .$cbt['categories_id'].'ref='.$_GET['ref']).'">'.tep_image(DIR_WS_IMAGES.$cbt['categories_image2'],$cbt['categories_name']).'</a>'."\n";
              $cbt_dec = explode(',',$cbt['categories_meta_text']);
              for($i=0; $i < sizeof($cbt_dec); $i++) {
			     if($cbt_dec[$i] != ''){
				  echo '<h3 class="Tlist">'.strip_tags(substr($cbt_dec[$i],0,36)).'..</h3>'."\n";
			     }
			  }	
	  echo  ' </td>'."\n";
		 
		 if (($number_of_categories/2) == floor($number_of_categories/2)) {
           echo '</tr>'."\n".'<tr>' ;
         } else {
		   echo '<td>'.tep_draw_separator('pixel_trans.gif', '18', '1').'</td>'."\n";
		 }  
      } 
  ?> 
</tr> 
</table> 
<!-- categories_banner_text_eof //--> 






































?>