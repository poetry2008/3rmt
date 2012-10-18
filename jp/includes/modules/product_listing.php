<?php
/*
  $Id$
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
<table width=" 100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#C0CEDD" class="sort">
  <tr>
  <td height="25" align="center" <?php echo ($_COOKIE['sort'] == '100a' or !isset($_COOKIE['sort']) or $_COOKIE['sort'] == '100d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_COOKIE['sort'] == '100a' or $_COOKIE['sort'] == '100d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><a href="javascript:void(0)" onclick="change_sort_type('100<?php echo $sort_default_str;?>');"><?php echo PRE_SORT_DEFAULT?></a><img class="middle" src="images/design/box/triangle<?php if(empty($_COOKIE['sort'])){ echo $sort_default_str=='d' ? '_upward' : '_downward';} else {if($tmp_sort_str=='100'){echo $sort_default_str=='d' ? '_upward' : '_downward';}else{echo  '_upward';}}?>.png" hspace="3" alt=""></td>
  <td height="25" align="center" <?php echo ($_COOKIE['sort'] == '4a' or $_COOKIE['sort'] == '4d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_COOKIE['sort'] == '4a' or $_COOKIE['sort'] == '4d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><a href="javascript:void(0)" onclick="change_sort_type('4<?php echo $sort_name_str;?>');"><?php echo PRE_SORT_A?></a><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='4'){echo $sort_name_str=='d' ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></td>
  <td height="25" align="center" <?php echo ($_COOKIE['sort'] == '5a' or $_COOKIE['sort'] == '5d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_COOKIE['sort'] == '5a' or $_COOKIE['sort'] == '5d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><a href="javascript:void(0)" onclick="change_sort_type('5<?php echo $sort_price_str;?>');"><?php echo PRE_SORT_PRICE?></a><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='5'){echo $sort_price_str=='d' ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></td>
  <td height="25" align="center" <?php echo ($_COOKIE['sort'] == '9d' or $_COOKIE['sort'] == '9a') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_COOKIE['sort'] == '9d' or $_COOKIE['sort'] == '9a') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><a href="javascript:void(0)" onclick="change_sort_type('9<?php echo $sort_best_str;?>');"><?php echo PRE_SORT_PEOPLE?></a><img class="middle" src="images/design/box/triangle<?php if($tmp_sort_str=='9'){echo $sort_best_str=='d' ? '_upward' : '_downward';}else{echo '_upward';}?>.png" hspace="3" alt=""></td>
  </tr>
</table>
<?php
  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('sort', 'page', 'info', 'x', 'y')));?></td>
  </tr>
</table>
<?php
  }
?>
<!--select searach_eof// -->
<div class="underline">&nbsp;</div>
<table border="0" width="689" cellspacing="0" cellpadding="0">
  <tr>
    <td>
<?php
  if ($listing_numrows > 0) {
  
    $listing_query = tep_db_query($listing_sql);
    $row = 0 ;
    $col = 0 ;

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
        //$BUY_NOW = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', TEXT_BUY . $listing['products_name'] . TEXT_NOW) . '</a>&nbsp;';
      } else {
        $BUY_NOW = STOCK_MARK_PRODUCT_OUT_OF_STOCK;
      }
      //product_name
      if(mb_strlen($listing['products_name']) > 40) {
        $products_name = mb_substr($listing['products_name'],0,40);
        $ten = '..';
      }else{
        $products_name = $listing['products_name'];
        $ten = '';
      }
      $description = strip_tags(mb_substr (replace_store_name($listing['products_description']),0,60));//maker
    
      $row++;
      $col++;
?>
      <!-- products_id <?php //echo listing['products_id'];?>-->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td rowspan="2" width="<?php echo SMALL_IMAGE_WIDTH;?>" style="padding-right:8px; " align="center">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.tep_image(DIR_WS_IMAGES . 'products/' . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>' ; ?>
          </td>
          <td class="main" style="padding-left:5px; ">
            <h3>
              <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
              <?php echo '<a class="bold" href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>'; ?>
            </h3>
          </td>
          <td class="main" align="right">
            <?php
              if (isset($has_ca_single)) {
                if (!$has_ca_single) {
            ?>
            <p>1<?php echo PRE_NUM_QTY.$price.PRE_FORM; ?></p>
            <?php
                }
              } else {
            ?>
            <p>1<?php echo PRE_NUM_QTY.$price.PRE_FORM; ?></p>
            <?php
              }
            ?>
          </td>
          <td class="main" align="right">
            <p><?php echo PRE_SURPLUS.'&nbsp;<b>'.tep_show_quantity($listing['products_quantity']).'</b>&nbsp;'.PRE_NUM_QTY; ?></p>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="padding-left:5px; ">
            <p class="smallText">
<?php
if (!isset($listing['products_bflag'])) $listing['products_bflag'] = NULL;
if (!isset($listing['products_cflag'])) $listing['products_cflag'] = NULL;
  echo $description . '..';
  if ($listing['preorder_status'] == '1') {
    echo '<br>'.PRE_HOPE_QTY.'<a href="'.tep_preorder_href_link($listing['products_id'], $listing['romaji']).'">'.$products_name.$ten.PRE_HOPE_PRE; 
  }
?>
            </p>
          </td>
          <td align="right">
            <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_order.gif',IMAGE_BUTTON_DEC);?></a>
          </td>
        </tr>
      </table>
      <br>
      <div class="dot">&nbsp;</div>
<?php
  }
  echo '    </td>' . "\n" . ' </tr>' . "\n";
  } else {
?>
  <tr class="productListing-odd">
        <?php if (!defined('TEXT_NO_PRODUCTS2')) define('TEXT_NO_PRODUCTS2', NULL);?>
    <td class="smallText">&nbsp;<?php echo (isset($_GET['manufacturers_id']) ? TEXT_NO_PRODUCTS2 : TEXT_NO_PRODUCTS); ?>&nbsp;</td>
  </tr>
<?php
  }
?>
  <tr>
    <td><div class="underline">&nbsp;</div></td>
  </tr>
<?php
  if ( ($listing_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
  <tr>
    <td>
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
<div id="dis_clist"></div>
