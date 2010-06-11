<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  //require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_MANUFAXTURERS);

  define('NAVBAR_TITLE', 'ゲームメーカー一覧');
  define('HEADING_TITLE', 'ゲームメーカー一覧');
  define('TEXT_MORE', 'このメーカー一覧の商品一覧へ');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('manufacturers.php'));
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
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td id="contents" valign="top">
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?>:RMT</h1>
                <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php 
  $manufacturer_query_raw = "select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi  where  m.manufacturers_id = mi.manufacturers_id and languages_id = '" . $languages_id . "' order by manufacturers_name";
  $manufacturer_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturer_query_raw, $manufacturer_numrows);
  //ccdd
  $manufacturer_query = tep_db_query($manufacturer_query_raw);

  if (($manufacturer_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
                <tr>
                  <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td class="smallText"><?php echo $manufacturer_split->display_count($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                        <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $manufacturer_split->display_links($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                      </tr>
                    </table>
                    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10') . "\n"; ?>
                  </td>
                </tr>
<?php
  }
?>
                <tr>
                  <td>
<?php
while ($manufacturer = tep_db_fetch_array($manufacturer_query)){
  //ccdd
  $products_query = tep_db_query("
      select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, p.products_price_offset, p.products_small_sum
      from " . TABLE_PRODUCTS . " p 
      where  p.products_id not in".tep_not_in_disabled_products()." and products_status != '0' and manufacturers_id = '".$manufacturer['manufacturers_id']."' 
      order by p.products_date_added desc 
      limit 5
  ");
  if (tep_db_num_rows($products_query)) {

    echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td width="120" class="smallText" valign="top">' . tep_image(DIR_WS_IMAGES.$manufacturer['manufacturers_image'],$manufacturer['manufacturers_name']) . '<h3><strong>'.$manufacturer['manufacturers_name'].'</strong></h3><!-- '.substr(strip_tags($manufacturer['manufacturers_url']),0,100) .'... --></td>' . "\n";
    echo '    <td>' . "\n";
  
    echo '      <table width="100%" border="0" cellspacing="2" cellpadding="0">' . "\n";
    echo '        <tr>' . "\n";
    while($products = tep_db_fetch_array($products_query)) {
      $products['products_name'] = tep_get_products_name($products['products_id']);
      $products['products_description'] = tep_get_products_description($products['products_id']);
       if (tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) {
         $products_price = '<s>' . $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) . '</span>';
       } else {
         $products_price = $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']));
       }
      echo '<td align="center" valign="top" class="smallText" width="20%"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products['products_id']) . '">'.tep_image2(DIR_WS_IMAGES.$products['products_image'],$products['products_name'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"').'<br>' .$products['products_name'] . '</a><br>'.$products_price.'<!-- '.strip_tags(substr($products['products_description'],0,50)).' --></td>'."\n";
    }
    echo '        </tr>' . "\n";
    echo '      </table>' . "\n";
    echo '    </td>' . "\n";
    echo '  </tr>' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td colspan="2" align="right" class="smallText">' . '<a href="'.tep_href_link(FILENAME_DEFAULT,'manufacturers_id=' . $manufacturer['manufacturers_id']).'">'.TEXT_MORE.'</a></td>'."\n";
    echo '  </tr>' . "\n";
    echo '</table><br><div class="dot">&nbsp;</div>' . "\n";
  }
}
?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (tep_db_num_rows($manufacturer_query)) {
?>
                      <tr>
                        <td class="smallText"><?php echo $manufacturer_split->display_count($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                        <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $manufacturer_split->display_links($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                      </tr>
<?php
  }
?>
                      <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
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
