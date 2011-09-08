<?php
/*
  $Id$
*/

  require(DIR_WS_MODULES . 'sort_products.php');
?>
<!--select searach -->
<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#dddddd">
  <tr>
    <td><a <?php echo ($_COOKIE['sort'] == '4a' or !isset($_COOKIE['sort'])) ? 'class="product_listing_link_01" style="background: url(images/design/box/product_listing_sort_02.gif)"' : 'class="product_listing_link" style="background: url(images/design/box/product_listing_sort_01.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('4a');">タイトル順(A～)に並べる</a></td>
    <td><a <?php echo ($_COOKIE['sort'] == '4d') ? 'class="product_listing_link_01" style="background: url(images/design/box/product_listing_sort_02.gif)"' : 'class="product_listing_link" style="background: url(images/design/box/product_listing_sort_01.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('4d');">タイトル順(Z～)に並べる</a></td>
    <td><a <?php echo ($_COOKIE['sort'] == '5a') ?  'class="product_listing_link_01" style="background: url(images/design/box/product_listing_sort_02.gif)"' : 'class="product_listing_link" style="background: url(images/design/box/product_listing_sort_01.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('5a');">価格順(安い)に並べる</a></td>
    <td><a <?php echo ($_COOKIE['sort'] == '5d') ?  'class="product_listing_link_01" style="background: url(images/design/box/product_listing_sort_02.gif)"' : 'class="product_listing_link" style="background: url(images/design/box/product_listing_sort_01.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('5d');">価格順(高い)に並べる</a></td>
    <td><a <?php echo ($_COOKIE['sort'] == '9d') ?  'class="product_listing_link_01" style="background: url(images/design/box/product_listing_sort_02.gif)"' : 'class="product_listing_link" style="background: url(images/design/box/product_listing_sort_01.gif)"' ; ?> href="javascript:void(0)" onclick="change_sort_type('9d');">人気順に並べる</a></td>
  </tr>
</table>
<?php
  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="product_list_page">
  <tr>
    <td align="left">
  <?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
    </td>
    <td align="right">
    &nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('sort', 'page', 'info', 'x', 'y'))); ?>&nbsp;
    </td>
  </tr>
</table>
<?php
  }
?>
<!--select searach_eof// -->
<table border="0" width="100%" cellspacing="0" cellpadding="0"  class="product_list_info">
  <tr>
    <td>
<?php
  if ($listing_numrows > 0) {
  
    $listing_query = tep_db_query($listing_sql);
    //echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">'."\n" ;
    //echo   '<tr>'."\n";
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
        $BUY_NOW = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', TEXT_BUY . $listing['products_name'] . TEXT_NOW) . '</a>&nbsp;';
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
      $description = strip_tags(mb_substr (replace_store_name($listing['products_description']),0,120));//maker
    
      $row++;
      $col++;
?>

      <table border="0" cellspacing="0" cellpadding="0" class="product_listing_content">
        <tr>
          <td rowspan="2" width="<?php echo SMALL_IMAGE_WIDTH?>" style="padding-right:8px; " align="center">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.tep_image(DIR_WS_IMAGES . 'products/' . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>' ; ?>
          </td>
          <td class="main" style="padding-left:5px; ">
           <h3>
              <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
              <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>'; ?>
            </h3>
          </td>
          <td class="main" align="right">
            <?php
              if (isset($has_ca_single)) {
                if (!$has_ca_single) {
            ?>
            <p>1個<?php echo $price; ?>から</p>
            <?php
                }
              } else {
            ?>
            <p>1個<?php echo $price; ?>から</p>
            <?php
              }
            ?>
          </td>
          <td class="main" align="right">
            <p><?php echo '残り&nbsp;<b>' . tep_show_quantity($listing['products_quantity']) . '</b>&nbsp;個'; ?></p>
          </td>
        </tr>
        <tr>
          <?php if (true) {?> 
          <td colspan="2" style="padding-left:5px; ">
            <p class="smallText">
<?php
if (!isset($listing['products_bflag'])) $listing['products_bflag'] = NULL;//del notice
if (!isset($listing['products_cflag'])) $listing['products_cflag'] = NULL;//del notice
  if($listing['products_bflag'] == '1') {
    # 買取商品
    echo $description . '..';
  } elseif ($listing['products_cflag'] == '0') {
    echo $description . '..';
  } else {
    # 販売商品
    echo $description . '.. 表示在庫以上の注文は「<a href="' . tep_href_link(FILENAME_PREORDER, 'products_id=' . $listing['products_id']) . '">' . $products_name . $ten . 'を予約</a>」からお手続きください。';
  }
?>
            </p>
          </td>
          <?php }?> 
          <td align="right" colspan="3">
            <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) ; ?>" class="button_order"></a>
          </td>
        </tr>
      </table>
      <div class="dot"></div>
<?php
  }
  //echo '</tr>';
  echo '    </td>' . "\n" . '  </tr>' . "\n";
  } else {
?>
  <tr class="productListing-odd">
    <td class="smallText">&nbsp;<?php echo (isset($_GET['manufacturers_id']) ? TEXT_NO_PRODUCTS2 : TEXT_NO_PRODUCTS); ?>&nbsp;</td>
  </tr>
<?php
  }
?>
<?php
  if ( ($listing_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<?php
  }
?>
</table>
      <table border="0" width="100%" cellspacing="0" cellpadding="2" class="product_list_page">
        <tr>
          <td align="left"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
          <td align="right">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('sort', 'page', 'info', 'x', 'y'))); ?>&nbsp;</td>
        </tr>
      </table>
<div id="dis_clist"></div>
