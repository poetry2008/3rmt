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
<!-- header //--> 
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
                <!-- left_navigation //-->
<div id="l_menu">
                <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
                <!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
 
             <table border="0" width="95%" cellspacing="0" cellpadding="0"> 
  <?php
  $specials_query_raw = "
  select * 
  from (
    select p.products_id, 
           pd.products_name, 
           p.products_price, 
           p.products_tax_class_id, 
           p.products_image, 
           pd.site_id,
           s.specials_new_products_price ,
           s.specials_date_added
    from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s 
    where p.products_id not in".tep_not_in_disabled_products()." 
      and p.products_status = '1' 
      and s.products_id = p.products_id 
      and p.products_id = pd.products_id 
      and pd.language_id = '" . $languages_id . "' 
      and s.status = '1' 
    ORDER by pd.site_id DESC
    ) p
  where site_id = '0'
     or site_id = '".SITE_ID."'
  group by products_id
  order by specials_date_added DESC
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
            </tr>
            <tr>
          <td class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
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
        echo '<td align="center" width="33%" class="smallText"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $specials['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $specials['products_image'], $specials['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"') . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $specials['products_id']) . '">' . $specials['products_name'] . '</a><br>';
        
        echo '<span id="' . $specials['products_id'] . '"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO,'products_id='.$specials['products_id'].'&action=buy_now').'" onClick="sendData(\'' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $specials['products_id']) . '\',\'' . displaychange . '\',\'' . $specials['products_id'] . '\'); return false;"><img src="images/design/button/button_order.gif" border="0"></a></span><br>';
        
        echo '<s>' . $currencies->display_price($specials['products_price'], tep_get_tax_rate($specials['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price($specials['specials_new_products_price'], tep_get_tax_rate($specials['products_tax_class_id'])) . '</span></td>' . "\n";
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
                                    <tr>
                                    <td class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $specials_split->display_links($specials_numrows, MAX_DISPLAY_SPECIAL_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
<?php
    }
?>
                </table>
                </div>
<!-- body_text_eof //-->
<!-- right_navigation //-->
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<!-- right_navigation_eof //-->
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</div>

<div id="dis_clist"></div>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
