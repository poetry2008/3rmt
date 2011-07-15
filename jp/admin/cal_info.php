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
            <td valign="top">
            <?php
            $list_status_arr = array(); 
            $list_num = 0; 
            $orders_status_query = tep_db_query("select orders_status_name, orders_status_id from ".TABLE_ORDERS_STATUS." where language_id = '".$languages_id."'"); 
            while ($orders_status_res = tep_db_fetch_array($orders_status_query)) {
              $site_id = (isset($_GET['site_id'])?$_GET['site_id']:1); 
              $site_orders_pending_query = tep_db_query("select count(*) as count from ".TABLE_ORDERS." where site_id = ".$site_id." and orders_status = '".$orders_status_res['orders_status_id']."'"); 
              $site_orders_pending_res = tep_db_fetch_array($site_orders_pending_query); 
              $list_status_arr[]= array($orders_status_res['orders_status_name'], $site_orders_pending_res['count']); 
            } 
            $site_customers_query = tep_db_query("select count(*) as count from ".TABLE_CUSTOMERS." where site_id = ".$site_id); 
            $site_customers_res = tep_db_fetch_array($site_customers_query); 
             
            $list_status_arr[] = array(HEADER_ENTRY_CAL_CUSTOMERS, $site_customers_res['count']); 
            
            $site_products_query = tep_db_query("select count(*) as count from ".TABLE_PRODUCTS); 
                                $site_products_res = tep_db_fetch_array($site_products_query); 
            $list_status_arr[] = array(HEADER_ENTRY_CAL_PRODUCTS, $site_products_res['count']); 
            
            $site_reviews_query = tep_db_query("select count(*) as count from ".TABLE_REVIEWS." where site_id = ".$site_id); 
            $site_reviews_res = tep_db_fetch_array($site_reviews_query); 
            $list_status_arr[] = array(HEADER_ENTRY_CAL_REVIEWS, $site_reviews_res['count']); 
             
             foreach ($list_status_arr as $list_key => $list_value) {
              if ($list_num == 0) {
                $list_head_str = '<tr class="dataTableHeadingRow">';
                $list_data_str = '<tr class="dataTableRow">';
                echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
              }
              $list_head_str .= '<td class="dataTableHeadingContent" align="center">'.$list_value[0].'</td>';    
              $list_data_str .= '<td class="dataTableContent" align="center">'.$list_value[1].'</td>'; 
              $list_num++;
              if ($list_num % 13 == 0) {
                echo $list_head_str.'</tr>'; 
                echo $list_data_str.'</tr>'; 
                echo '</table><br><br>'; 
                echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
                $list_head_str = '<tr class="dataTableHeadingRow">';
                $list_data_str = '<tr class="dataTableRow">';
              }
            }
            if ($list_num % 13 != 0) {
              echo $list_head_str.'</tr>'; 
              echo $list_data_str.'</tr>'; 
              echo '</table>'; 
            } else {
              echo '</table>'; 
            }
            ?> 
            </table> 
            </td>
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
