<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SPECIALS);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SPECIALS));
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><span><?php echo HEADING_TITLE ; ?><?php echo STORE_NAME;?>の特価商品</span></h1>
        <div class="comment">
                <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box">
<?php
$specials_caid_arr = tep_ff_get_categories_id_by_parent_id(FF_CID);
if (empty($specials_caid_arr)) {
  $specials_caid_arr = array(FF_CID);
} else {
  array_push($specials_caid_arr, FF_CID);
}
  $specials_query_raw = "
  select * 
  from (
    select p.products_id, 
           pd.products_name, 
           p.products_price, 
           p.products_price_offset, 
           p.products_small_sum, 
           p.products_tax_class_id, 
           p.products_image, 
           pd.products_status, 
           p.products_date_added,
           pd.site_id
    from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where 
      (p.products_price_offset != 0 and not isnull(p.products_price_offset) or p.products_small_sum != '') 
      and p.products_id not in".tep_not_in_disabled_products()." 
      and p.products_id = pd.products_id 
      and p.products_id = p2c.products_id
      and p2c.categories_id in (".implode(',', $specials_caid_arr).")
      and pd.language_id = '" . $languages_id . "' 
    ORDER by pd.site_id DESC
    ) p
  where site_id = '0'
     or site_id = '".SITE_ID."'
  group by products_id
  having p.products_status != '0' and p.products_status != '3'
  order by products_date_added DESC
  ";
  $specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SPECIAL_PRODUCTS, $specials_query_raw, $specials_numrows);
  //ccdd
  $specials_query = tep_db_query($specials_query_raw);
  
  if (($specials_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
          <tr>
            <td>
              <br>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText"><?php echo $specials_split->display_count($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
                  <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                </tr>
              </table>
            </td>
          </tr>
<?php
  }
?>
          <tr>
            <td>
              <table border="0" width="100%" cellspacing="0" cellpadding="10">
                <tr>
<?php
  $row = 0;
  while ($specials = tep_db_fetch_array($specials_query)) {
    $row++;
    echo '<td align="center" width="33%" class="smallText" style="vertical-align:top;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $specials['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $specials['products_image'], $specials['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"') . '<br>' . $specials['products_name'] . '<br>';
    
    echo '<img src="images/design/button/button_order.gif" width="69" height="17" alt="注文する"></a><br>';
    
    echo '<s>' . $currencies->display_price(tep_get_price($specials['products_price'], $specials['products_price_offset'], $specials['products_small_sum']), tep_get_tax_rate($specials['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($specials['products_price'], $specials['products_price_offset'], $specials['products_small_sum']), tep_get_tax_rate($specials['products_tax_class_id'])) . '</span></td>' . "\n";
    if ((($row / 3) == floor($row / 3))) {
?>
                </tr>
                <tr>
<?php
    }    
  }
?>
                </tr>
              </table>
            </td>
          </tr>
<?php
  if (($specials_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
          <tr>
            <td>
              <br>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText"><?php echo $specials_split->display_count($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
                  <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                </tr>
              </table>
            </td>
          </tr>
<?php
  }
?>
        </table>
                </div>
                <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
