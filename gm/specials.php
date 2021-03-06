<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SPECIALS);
  if (isset($_GET['page'])) {
    if (!preg_match('/^\d+$/', $_GET['page'])) {
      forward404(); 
    }
  }
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SPECIALS));
?>
<?php page_head();?>
</head>
<body>
<!-- header --> 
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<div id="main">
<!-- body_text -->
<div class="yui3-u" id="layout">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
  <div id="main-content">
  <h2><?php echo HEADING_TITLE; ?></h2>
             <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
  <?php
  $specials_query_raw = "
  select * 
  from (
    select p.products_id, 
           pd.products_name, 
           p.products_price, 
           p.products_price_offset, 
           p.products_small_sum, 
           p.products_tax_class_id, 
           p.products_date_added,
           p.products_bflag, 
           pd.products_status, 
           pd.site_id
    from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where 
      (p.products_price_offset != 0 and not isnull(p.products_price_offset) or p.products_small_sum != '') 
      and p.products_id not in".tep_not_in_disabled_products()." 
      and p.products_id = pd.products_id 
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
  
  $specials_query = tep_db_query($specials_query_raw);
  
  if (($specials_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
          <tr>
            <td>
              <br>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText"><?php echo $specials_split->display_count($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
                </tr>
              </table>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText"><?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                </tr>
              </table>
            </td>
          </tr>
<?php
  }
?>
          <tr>
            <td>
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
<?php
  $row = 0;
  while ($specials = tep_db_fetch_array($specials_query)) {
    $row++;
    $img_array =
      tep_products_images($specials['products_id'],$specials['site_id']);
    echo '<td align="center" width="33%" class="smallText"><a href="' .
      tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .
          $specials['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/'
      .$img_array[0], $specials['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"') . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $specials['products_id']) . '">' . $specials['products_name'] . '</a><br>';
      
        echo '<span id="' . $specials['products_id'] . '"><a
          href="'.tep_href_link(FILENAME_PRODUCT_INFO,'products_id='.$specials['products_id']).'"
          return false;">'.TEXT_BUY_ORDERS_LINK.'</a></span><br>';
      
        echo '<s>' .
          $currencies->display_price(tep_get_price($specials['products_price'], $specials['products_price_offset'], $specials['products_small_sum'], $specials['products_bflag']), tep_get_tax_rate($specials['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($specials['products_price'], $specials['products_price_offset'], $specials['products_small_sum']), tep_get_tax_rate($specials['products_tax_class_id'])) . '</span></td>' . "\n";
    if ((($row / 3) == floor($row / 3))) {
?>
                </tr>
                <tr>
              <td>&nbsp;</td>
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
                              </tr>
                            </table>
                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                              <tr>
                                <td class="smallText"><?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                              </tr>
                            </table>
                        </td>
                    </tr>
<?php
    }
?>
        </table>
      </div>
      </div>
 <?php include('includes/float-box.php');?>

        </div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<div id="dis_clist"></div>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
