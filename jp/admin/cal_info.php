<?php
/*
  $Id$
*/
  require('includes/application_top.php');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <?php tep_site_head_list('cal_info.php');?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <?php
                  $orders_status_query = tep_db_query("select orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$languages_id."'"); 
                  while ($orders_status_res = tep_db_fetch_array($orders_status_query)) {
                  ?>
                  <td class="dataTableHeadingContent" align="center">&nbsp;&nbsp;<?php echo $orders_status_res['orders_status_name']; ?>&nbsp;&nbsp;</td>
                  <?php
                  }
                ?>
                  <td class="dataTableHeadingContent" align="center">&nbsp;&nbsp;<?php echo HEADER_ENTRY_CAL_CUSTOMERS;?>&nbsp;&nbsp;</td>
                  <td class="dataTableHeadingContent" align="center">&nbsp;&nbsp;<?php echo HEADER_ENTRY_CAL_PRODUCTS;?>&nbsp;&nbsp;</td>
                  <td class="dataTableHeadingContent" align="center">&nbsp;&nbsp;<?php echo HEADER_ENTRY_CAL_REVIEWS;?></td>
              </tr>
              <tr class="dataTableRow" onmouseout="this.className='dataTableRow'" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'">
              <?php
                $site_id = (isset($_GET['site_id'])?$_GET['site_id']:1); 
                
                $site_orders_status_query = tep_db_query("select orders_status_name, orders_status_id from ".TABLE_ORDERS_STATUS." where language_id = '".$languages_id."'"); 
                while ($site_orders_status_res = tep_db_fetch_array($site_orders_status_query)) {
                  $site_orders_pending_query = tep_db_query("select count(*) as count from ".TABLE_ORDERS." where site_id = ".$site_id." and orders_status = '".$site_orders_status_res['orders_status_id']."'"); 
                  $site_orders_pending_res = tep_db_fetch_array($site_orders_pending_query); 
                  ?>
                  <td class="dataTableContent" align="center"><?php echo $site_orders_pending_res['count'];?></td>
                  <?php
                } 
              ?>
                  <td class="dataTableContent" align="center">
                  <?php
                    $site_customers_query = tep_db_query("select count(*) as count from ".TABLE_CUSTOMERS." where site_id = ".$site_id); 
                    $site_customers_res = tep_db_fetch_array($site_customers_query); 
                    echo $site_customers_res['count']; 
                  ?>
                  </td>
                  <td class="dataTableContent" align="center">
                  <?php
                    $site_products_query = tep_db_query("select count(*) as count from ".TABLE_PRODUCTS); 
                    $site_products_res = tep_db_fetch_array($site_products_query); 
                    echo $site_products_res['count']; 
                  ?>
                  </td>
                  <td class="dataTableContent" align="center">
                  <?php
                    $site_reviews_query = tep_db_query("select count(*) as count from ".TABLE_REVIEWS." where site_id = ".$site_id); 
                    $site_reviews_res = tep_db_fetch_array($site_reviews_query); 
                    echo $site_reviews_res['count']; 
                  ?>
                  </td>
              
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
