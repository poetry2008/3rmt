<?php
/*
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
  <meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
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
<table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr>
      <?php
// optional Product List Filter
if(basename($PHP_SELF) == FILENAME_DEFAULT) {
   if (PRODUCT_LIST_FILTER > 0 && !empty($filterlist_sql)) {
      $filterlist_query = tep_db_query($filterlist_sql);
      if (tep_db_num_rows($filterlist_query) > 1) {
        echo '            <td class="smallText" align="right">' . TEXT_SHOW . '<select size="1" onChange="if(options[selectedIndex].value) window.location.href=(options[selectedIndex].value)">';
        if (isset($HTTP_GET_VARS['manufacturers_id'])) {
          $arguments = 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'];
        } else {
          $arguments = 'cPath=' . $cPath;
        }
        $arguments .= '&sort=' . $HTTP_GET_VARS['sort'];

        $option_url = tep_href_link(FILENAME_DEFAULT, $arguments);

        if (!isset($HTTP_GET_VARS['filter_id'])) {
          echo '<option value="' . $option_url . '" SELECTED>' . TEXT_ALL . '</option>';
        } else {
          echo '<option value="' . $option_url . '">' . TEXT_ALL . '</option>';
        }

        echo '<option value="">---------------</option>';
        while ($filterlist = tep_db_fetch_array($filterlist_query)) {
          $option_url = tep_href_link(FILENAME_DEFAULT, $arguments . '&filter_id=' . $filterlist['id']);
          if (isset($HTTP_GET_VARS['filter_id']) && ($HTTP_GET_VARS['filter_id'] == $filterlist['id'])) {
            echo '<option value="' . $option_url . '" SELECTED>' . $filterlist['name'] . '</option>';
          } else {
            echo '<option value="' . $option_url . '">' . $filterlist['name'] . '</option>';
          }
        }
        echo '</select></td>' . "\n";
      }
    }
 }
?>
      <td class="smallText"><b><?php echo LISTING_SORT_BY ; ?></b>
        <select name="select" onChange="if(options[selectedIndex].value) window.location.href=(options[selectedIndex].value)">
          <option value="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5a') ; ?>"  <?php if($HTTP_GET_VARS['sort'] == '5a') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_PRICE_LOW ; ?></option>
          <option value="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5d') ; ?>"  <?php if($HTTP_GET_VARS['sort'] == '5d') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_PRICE_HIGHT ; ?></option>
          <option value="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4a') ; ?>"  <?php if($HTTP_GET_VARS['sort'] == '4a') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_TITLE_A_TO_Z ; ?></option>
          <option value="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4d') ; ?>"  <?php if($HTTP_GET_VARS['sort'] == '4d') {echo 'SELECTED' ;}else{ echo '';} ?>><?php echo LISTING_TITLE_Z_TO_A ; ?></option>
        </select></td>
    </tr>
  <?php
  $listing_numrows_sql = $listing_sql;
  $listing_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
  // fix counted products
  $listing_numrows = tep_db_query($listing_numrows_sql);
  $listing_numrows = tep_db_num_rows($listing_numrows);
  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
  <tr>
    <td colspan="2"><div class="sep">&nbsp;</div>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
          <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
<?php
  }
?>
</table>
<!--select searach_eof// -->
<div class="underline">&nbsp;</div>
<table border="0" width="600" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php
if ($listing_numrows > 0) {

    $listing_query = tep_db_query($listing_sql);
  echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">'."\n" ;
    $row = 0 ;
    $col = 0 ;

  while ($listing = tep_db_fetch_array($listing_query)) {
  //price
      if (tep_not_null($listing['specials_new_products_price'])) {
        $price = '<s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $price = $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
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

      $description = strip_tags(mb_substr ($listing['products_description'],0,60));//maker
    
      $row++;
      $col++;
?>
      <!-- products_id <?php echo $listing['products_id'];?>-->
      <tr><td>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td rowspan="2" width="<?php echo SMALL_IMAGE_WIDTH;?>" style="padding-right:8px; " align="center">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>' ; ?>
          </td>
          <td style="padding-left:5px; "><p class="main"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="middle" alt="img"><strong><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>';?></strong></p>
          </td>
          <td align="right">
            <p class="main">1個<?php echo $price; ?>から</p>
          </td>
          <td width="90" align="right">
            <p class="main"><?php echo '残り&nbsp;<b>' . $listing['products_quantity'] . '</b>&nbsp;個'; ?></p>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="padding-left:5px; ">
            <p class="smallText">
<?php
if (!isset($listing['products_bflag'])) $listing['products_bflag'] = NULL;//delnotice
if (!isset($listing['products_cflag'])) $listing['products_cflag'] = NULL;//delnotice
  if($listing['products_bflag'] == '1') {
    # 買取商品
    echo $description . '..';
  } elseif ($listing['products_cflag'] == '0') {
    echo $description . '..';
  } else {
    # 販売商品
    echo $description . '..&nbsp;&nbsp;ご希望の数量がない場合は「<a href="' . tep_href_link(FILENAME_PREORDER, 'products_id=' . $listing['products_id']) . '">' . $products_name . $ten . 'を予約する</a>」をクリック';
  }
?>
            </p>
          </td>
          <td align="right">
            <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_order.gif', '注文はこちら');?></a>
          </td>
        </tr>
      </table>
    <br>
    <div class="dot">&nbsp;</div>
    </td></tr>
    <?php
  }
  echo '</table>' ;


    echo '    </td>' . "\n" .
         '  </tr>' . "\n";
  } else {
?>
  <tr class="productListing-odd">
    <td class="smallText">&nbsp;<?php echo (isset($HTTP_GET_VARS['manufacturers_id']) ? TEXT_NO_PRODUCTS2 : TEXT_NO_PRODUCTS); ?>&nbsp;</td>
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
          <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
          <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
<?php
  }
?>
</table>
