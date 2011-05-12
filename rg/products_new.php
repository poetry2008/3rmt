<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCTS_NEW);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCTS_NEW));
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
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
      <td valign="top" id="contents">
        <div class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif"><h1><?php echo HEADING_TITLE ; ?></h1></div>
        <div class="comment">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <?php
  $products_new_query_raw = "
select *
from (
  select p.products_id, 
         pd.products_name, 
         pd.site_id,
         pd.products_status, 
         p.products_image, 
         p.products_price, 
         p.products_price_offset, 
         p.products_small_sum, 
         p.products_tax_class_id, 
         p.products_date_added, 
         m.manufacturers_name 
  from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'
  order by pd.site_id DESC
  ) p
where site_id = '0'
   or site_id = ".SITE_ID." 
group by products_id
having p.products_status != '0' and p.products_status != '3'
order by products_date_added DESC, products_name
  ";
  $products_new_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_NEW, $products_new_query_raw, $products_new_numrows);

  // ccdd
  $products_new_query = tep_db_query($products_new_query_raw);
  while ($products_new = tep_db_fetch_array($products_new_query)) {
    $products_new_array[] = array('id' => $products_new['products_id'],
                                  'name' => $products_new['products_name'],
                                  'image' => $products_new['products_image'],
                                  'price' => $products_new['products_price'],
                                  'price_offset' => $products_new['products_price_offset'],
                                  'small_sum' => $products_new['products_small_sum'],
                                  'tax_class_id' => $products_new['products_tax_class_id'],
                                  'date_added' => tep_date_long($products_new['products_date_added']),
                                  'manufacturer' => $products_new['manufacturers_name']);
  }

  require(DIR_WS_MODULES  . 'products_new.php');
?>
              </td>
            </tr>
            <?php
  if (($products_new_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
            <tr>
              <td><br>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $products_new_split->display_count($products_new_numrows, MAX_DISPLAY_PRODUCTS_NEW, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?>
                      <?php echo $products_new_split->display_links($products_new_numrows, MAX_DISPLAY_PRODUCTS_NEW, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php
  }
?>
          </table>
        </div>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
