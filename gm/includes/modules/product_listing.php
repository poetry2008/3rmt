<?php
/*
  $Id$
  确认文件代码
*/

  require(DIR_WS_MODULES . 'sort_products.php');
  require(DIR_WS_LANGUAGES . $language . '/product_listing.php'); 
  $sort_default_str = 'a';
  $sort_name_str = 'a'; 
  $sort_price_str = 'a'; 
$sort_best_str = 'a';
$tmp_sort_str = substr($_COOKIE['sort'], 0, -1);
if ($tmp_sort_str == '4') {
	$sort_name_str = $sort_type == 'd' ? 'd':'a';  
} else if ($tmp_sort_str == '5') {
	$sort_price_str = $sort_type == 'd' ? 'd':'a';  
} else if ($tmp_sort_str == '9') {   
	  $sort_best_str = $sort_type == 'd' ? 'd':'a';
	      } else {                                 
		      if (empty($_COOKIE['sort'])) {
			      $sort_default_str = 'd';
		      } else {
			      $sort_default_str = $sort_type == 'd' ? 'd':'a';
		      }
			       }                    

 ?>
<!--select searach -->
<table cellspacing="0" cellpadding="0" border="0" class="sort" width="100%">
	<tr>
<td><a class="product_listing_link" <?php echo (!isset($_COOKIE['sort']) or $_COOKIE['sort'] == '100a' or $_COOKIE['sort'] == '100d') ? 'style="background:url(images/design/box/button_large_hover.gif)"' : 'style="background:url(images/design/box/button_large.gif)"' ; ?> href="javascript:void(0)"
    <?php echo $onmouse; ?>onclick="change_sort_type('100<?php echo $sort_default_str;?>');"><?php echo PRE_SORT_DEFAULT;?><img class="middle" src="images/design/box/triangle<?php if(empty($_COOKIE['sort'])){ echo $sort_default_str=='d' ? '_upward' : '_downward';} else {if($tmp_sort_str=='100'){echo $sort_default_str=='d' ? '_upward' : '_downward';}else{echo  '_upward';}}?>.png" hspace="3" alt=""></a></td>

    <td><a class="product_listing_link" <?php echo ($_COOKIE['sort'] == '4a' or $_COOKIE['sort'] == '4d') ? 'style="background:url(images/design/box/button_large_hover.gif)"' : 'style="background:url(images/design/box/button_large.gif)"' ; ?> href="javascript:void(0)"
    onclick="change_sort_type('4<?php echo $sort_name_str ?>');"><?php echo PRE_SORT_A;?><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='4'){echo $sort_name_str=='d'  ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></a></td>
    <td><a id="jingyi" class="product_listing_link" <?php echo ($_COOKIE['sort'] == '5a' or $_COOKIE['sort'] == '5d') ? 'style="background: url(images/design/box/button_large_hover.gif)"' : 'style="background: url(images/design/box/button_large.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('5<?php echo $sort_price_str?>');"><?php echo PRE_SORT_PRICE;?><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='5'){echo $sort_price_str=='d' ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></a></td>
    <td style="border-right:1px solid #666666;"><a class="product_listing_link" <?php echo ($_COOKIE['sort'] == '9d' or $_COOKIE['sort'] == '9a') ? 'style="background: url(images/design/box/button_large_hover.gif)"' : 'style="background: url(images/design/box/button_large.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('9<?php echo $sort_best_str ?>');"><?php echo PRE_SORT_PEOPLE;?><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='9'){echo $sort_best_str=='d' ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></a></td>
     </tr>
</table>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<!--    <tr>
      <?php
?>
      <td>&nbsp;</td>
      <td class="smallText" align="right"><b><?php echo LISTING_SORT_BY ; ?></b>
        <select name="select" onChange="if(options[selectedIndex].value) change_sort_type(options[selectedIndex].value)">
          <option value="4a"  <?php if($_COOKIE['sort'] == '4a') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_TITLE_A_TO_Z ; ?></option>
          <option value="4d"  <?php if($_COOKIE['sort'] == '4d') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_TITLE_Z_TO_A ; ?></option>
          <option value="5a"  <?php if($_COOKIE['sort'] == '5a') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_PRICE_LOW ; ?></option>
          <option value="5d"  <?php if($_COOKIE['sort'] == '5d') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_PRICE_HIGHT ; ?></option>
        </select></td>
    </tr>
-->  <?php
  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
  <tr>
    <td colspan="2">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td class="smallText"><?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('sort', 'page', 'info', 'x', 'y'))); ?></td>
        </tr>
      </table>
    </td>
  </tr>
<?php
  }
?>
</table>
<!--select searach_eof// -->


<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
 <?php
if ($listing_numrows > 0) {
   
    $listing_query = tep_db_query($listing_sql);
    $row = 0 ;
    $col = 0 ;
    $i=1;
  while ($listing = tep_db_fetch_array($listing_query)) {
  //price
      $p_bflag = tep_get_bflag_by_product_id($listing['products_id']); 
      if (tep_get_special_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum'])) {
        $price = '<s>' .
          $currencies->display_price(tep_get_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum'], $p_bflag), tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum']), tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $price = $currencies->display_price(tep_get_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum'], $p_bflag), tep_get_tax_rate($listing['products_tax_class_id']));
      }
      //buynow
      if($listing['products_quantity'] > 0) {
        if(!defined('TEXT_BUY')) define('TEXT_BUY', NULL);
        if(!defined('TEXT_NOW')) define('TEXT_NOW', NULL);
        $BUY_NOW = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', TEXT_BUY . $listing['products_name'] . TEXT_NOW) . '</a>&nbsp;';
      } else {
        $BUY_NOW = STOCK_MARK_PRODUCT_OUT_OF_STOCK;
      }
      //product_name
      if(mb_strlen($listing['products_name']) > 35) {
        $products_name = mb_substr($listing['products_name'],0,35);
        $ten = '..';
      }else{
        $products_name = $listing['products_name'];
        $ten = '';
      }

      $description = strip_tags(mb_substr (replace_store_name($listing['products_description']),0,60));//maker
    
      $row++;
      $col++;
      ?>



<td valign="top" width="25%" class="product_info">
<table border="0" width="100%" cellspacing="0" top="0"  cellpadding="0" id="product-box">
      <!-- products_id <?php echo $listing['products_id'];?>-->
      <tr>  
          <td rowspan="1" width="8%" align="center" valign="top">
            <?php  echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.tep_image(DIR_WS_IMAGES . 'products/' . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>' ; ?>
          </td>
         <td valign="top"><?php echo '<a class="bold" href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>';?>
          </td>
      </tr>
      <tr>
          <td colspan="2">
          <?php
              if (isset($has_ca_single)) {
                if (!$has_ca_single) {
            ?>
            1<?php echo PRE_NUM_QTY.$price.PRE_FORM; ?>
            <?php
                }
              } else {
            ?>
            1<?php echo PRE_NUM_QTY.$price.PRE_FORM; ?>
            <?php
              }
            ?>
          </td>
         </tr>
          <tr>
          <td colspan="2">
             <?php echo PRE_SURPLUS.'&nbsp;<b>' . tep_show_quantity($listing['products_quantity']) . '</b>&nbsp;'.PRE_NUM_QTY; ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
<?php
    echo $description . '&nbsp;..';
    if ($listing['preorder_status'] == '1') {
      echo '&nbsp;&nbsp;'.PRE_HOPE_QTY.'<a href="' .  tep_preorder_href_link($listing['products_id'], $listing['romaji']) . '">' . $products_name . $ten .PRE_HOPE_PRE;
    }
?>
      
          </td>
          </tr>
         </table> 
         </td>
    <?php
     if($i%4==0){
     echo '</tr><tr>'; 
     }
     $i++;
  }
  ?>
    
    <?php
   } else {
?>
  <tr>
    <td>&nbsp;<?php echo (isset($_GET['manufacturers_id']) ? TEXT_NO_PRODUCTS2 : TEXT_NO_PRODUCTS); ?>&nbsp;</td>
  </tr>
<?php
  }
?>
</table>
<?php
  if ( ($listing_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
  </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('sort', 'page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }
?>

