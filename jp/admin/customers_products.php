<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES.'currencies.php');
  $currencies = new currencies(2);
  $product_history_query_raw = "select o.orders_id, o.customers_name, o.date_purchased, o.orders_status from ".TABLE_ORDERS." o where o.customers_id = '".$_GET['cID']."' order by o.date_purchased desc";
  $product_history_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $product_history_query_raw, $product_history_numrows);
  $product_history_query = tep_db_query($product_history_query_raw);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table>
    </td>
    <td valign="top">   
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE;?></td> 
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT);?></td> 
            </tr>
        </table>
        <table id="orders_list_table" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_NAME;?></td> 
                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE;?></td> 
                        <td style="table-layout:fixed;width:120px;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NAME;?></td> 
                        <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_PRICE;?></td> 
                        <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NUM;?></td> 
                        <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_COST;?></td> 
                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS;?></td> 
                      </tr>
                      <?php 
                      while ($product_history = tep_db_fetch_array($product_history_query)) {
                      ?>
                        <?php
                        $product_list_query = tep_db_query("select op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and o.orders_id = '".$product_history['orders_id']."'");
                        ?>
                        <?php
                        $i = 1; 
                        $product_list_total = tep_db_num_rows($product_list_query); 
                        while ($product_list_res = tep_db_fetch_array($product_list_query)) {
                        ?>
                        <tr class="dataTableRow">
                          <?php
                          if ($product_list_total == $i) {
                            $style_str = 'border-bottom: 1px solid #000;padding-top:5px;'; 
                          } else {
                            $style_str = 'padding-top:5px;'; 
                          }
                          ?>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                          <?php 
                          if ($i == 1) {
                            echo $product_history['customers_name'];
                          } else {
                            echo '&nbsp;';
              }
                          ?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                          <?php 
                          if ($i == 1) {
                            echo tep_datetime_short($product_history['date_purchased']);
                          } else {
                echo '&nbsp;';
              }
                          ?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top">
                          <?php echo $product_list_res['products_name'];?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                          <?php echo $currencies->format($product_list_res['final_price']);?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                          <?php echo $product_list_res['products_quantity'].PRODUCT_NUM_TEXT;?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                          <?php echo $currencies->format($product_list_res['products_quantity']*$product_list_res['final_price']);?> 
                          </td>
                          <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                          <?php
                          $orders_status_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = ".(int)$product_history['orders_status']); 
                          $orders_status_res = tep_db_fetch_array($orders_status_query); 
                          if ($i == 1) {
                            echo $orders_status_res['orders_status_name']; 
                          } else {
                echo '&nbsp;';
              }
                          ?>
                          </td>
                        </tr> 
                        <?php
                          $i++; 
                        }
                        ?>
                      <?php
                      }
                      ?>
                      <tr>
                       <td colspan="7">
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="smallText" valign="top"><?php echo $product_history_split->display_count($product_history_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                            <td class="smallText" align="right"><?php echo $product_history_split->display_links($product_history_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></td>
                          </tr>
                        </table>
                       </td>
                      </tr>
                      <tr>
                        <td colspan="7" align="right">
                        <a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, str_replace('cpage', 'page', tep_get_all_get_params(array('page'))));?>"><?php echo tep_image_button('button_back.gif', IMAGE_BACK);?></a> 
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
    </td>
  </tr>
</table>
<?php require(DIR_WS_INCLUDES.'footer.php');?>
</body>
</html>
