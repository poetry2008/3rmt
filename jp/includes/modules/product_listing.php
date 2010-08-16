<?php
/*
  $Id$
*/

  // Product_listing.php Add
  define('LISTING_DISPLAY_OPTION','表示形式:');
  define('LISTING_SORT_BY','並び替え:');
  define('LISTING_PRICE_LOW','価格が安い');
  define('LISTING_PRICE_HIGHT','価格が高い');
  define('LISTING_TITLE_A_TO_Z','タイトル A - Z');
  define('LISTING_TITLE_Z_TO_A','タイトル Z - A');
  
  define('SORT_BY_IMAGE_TEXT','タイトルと画像');
  define('SORT_BY_IMAGE','画像のみ');
?>
<!--select searach -->
<table width="689"  border="0" cellpadding="1" cellspacing="1" bgcolor="#C0CEDD">
  <tr>
    <td height="25" align="center" <?php echo ($_GET['sort'] == '4a') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_GET['sort'] == '4a') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4a') ; ?>">タイトル順(A～)に並べる</a></td>
    <td height="25" align="center" <?php echo ($_GET['sort'] == '4d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_GET['sort'] == '4d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4d') ; ?>">タイトル順(Z～)に並べる</a></td>
    <td height="25" align="center" <?php echo ($_GET['sort'] == '5a') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_GET['sort'] == '5a') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5a') ; ?>">価格順(安い)に並べる</a></td>
    <td height="25" align="center" <?php echo ($_GET['sort'] == '5d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_GET['sort'] == '5d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5d') ; ?>">価格順(高い)に並べる</a></td>
    <td height="25" align="center" <?php echo ($_GET['sort'] == '9d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($_GET['sort'] == '9d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=9d') ; ?>">人気順に並べる</a></td>
  </tr>
</table>
<?php
  $listing_numrows_sql = $listing_sql;
  $listing_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
  // fix counted products
  $listing_numrows = tep_db_query($listing_numrows_sql);
  $listing_numrows = tep_db_num_rows($listing_numrows);

  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="689" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
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
      /*if (tep_not_null($listing['specials_new_products_price'])) {
        $price = '<s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $price = $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
      }*/
      //echo 'a';
      //print_r($listing);
      if (tep_get_special_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum'])) {
        $price = '<s>' . $currencies->display_price(tep_get_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum']), tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum']), tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $price = $currencies->display_price(tep_get_price($listing['products_price'], $listing['products_price_offset'], $listing['products_small_sum']), tep_get_tax_rate($listing['products_tax_class_id']));
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
      $description = strip_tags(mb_substr ($listing['products_description'],0,60));//maker
    
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
              <strong><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>'; ?></strong>
            </h3>
          </td>
          <td class="main" align="right">
            <p>1個<?php echo $price; ?>から</p>
          </td>
          <td class="main" align="right">
            <p><?php echo '残り&nbsp;<b>' . $listing['products_quantity'] . '</b>&nbsp;個'; ?></p>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="padding-left:5px; ">
            <p class="smallText">
<?php
if (!isset($listing['products_bflag'])) $listing['products_bflag'] = NULL;
if (!isset($listing['products_cflag'])) $listing['products_cflag'] = NULL;
  if($listing['products_bflag'] == '1') {
    # 買取商品
    echo $description . '..';
  } elseif ($listing['products_cflag'] == '0') {
    echo $description . '..';
  } else {
    # 販売商品
    echo $description . '..<br>表示在庫以上の注文は「<a href="' . tep_href_link(FILENAME_PREORDER, 'products_id=' . $listing['products_id']) . '">' . $products_name . $ten . 'を予約</a>」からお手続きください。';
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
          <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
<?php
  }
?>
</table>
<div id="dis_clist"></div>
