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
<title><?php echo HEADING_TITLE; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
        </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" style="margin-bottom:5px;">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="10%"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PURCHASED; ?></td>
              </tr>
<?php
  if (isset($_GET['page']) && $_GET['page'] > 1) {
    $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  } else {
    $rows = 0;
  }
  $products_query_raw = "
    select p.products_id, 
           p.products_ordered, 
           pd.products_name 
    from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where pd.products_id = p.products_id 
      and pd.language_id = '" . $languages_id. "' 
      and pd.site_id ='0'
      and p.products_ordered > 0 
    group by pd.products_id 
    order by p.products_ordered DESC, pd.products_name";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);

  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
    
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    //生成跳转到商品管理页面的URL
    $products_categories_id = tep_get_products_parent_id($products['products_id']);
    $categories_url_id = get_link_parent_category($products_categories_id);
    $categories_url_array = explode('_',$categories_url_id);

    $current_category_id = end($categories_url_array);
    $products_id_query = tep_db_query("select products_id from (
                             select p.products_id,pd.site_id,p.sort_order,pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
                             where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' 
                             and p.products_id = p2c.products_id 
                             and p2c.categories_id = '" . $current_category_id . "'
                             order by site_id DESC
                           ) c where site_id = 0 
                           group by products_id 
                           order by sort_order, products_name, products_id");
   $products_id_array = array();
   $products_num = 0;
   $products_page_flag = 1;
   while($products_query_array  = tep_db_fetch_array($products_id_query)){

     if($products_num - (MAX_DISPLAY_PRODUCTS_ADMIN*$products_page_flag-1) == 1){
  
       $products_page_flag++; 
     }
     $products_id_array[$products_page_flag][] = $products_query_array['products_id'];
     $products_num++;
   }
   tep_db_free_result($products_id_query);

   foreach($products_id_array as $products_key=>$products_value){

     if(in_array($products['products_id'],$products_value)){

       $page = $products_key;
       break;
     }
   }
   $firstQuery = tep_db_query("select UNIX_TIMESTAMP(min(date_purchased)) as first FROM " . TABLE_ORDERS . " o left join ".TABLE_ORDERS_PRODUCTS." op on o.orders_id=op.orders_id where op.products_id='".$products['products_id']."'");
   $first = tep_db_fetch_array($firstQuery);
   tep_db_free_result($firstQuery);
?>
              <tr class="<?php echo $nowColor;?>" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='<?php echo $nowColor;?>'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$products['products_id'].'&site_id=0', 'NONSSL'); ?>'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$products['products_id'].'&site_id=0', 'NONSSL') . '">' . $products['products_name'] . '</a>'; ?></td>
                <td class="dataTableContent" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=5&site_id=&is_select=1&add_product_categories_id='.$current_category_id.'&cid='.$current_category_id.'&pid='.$products['products_id'].'&products_id='.$products['products_id'].'&startY='.date('Y',$first['first']).'&startM='.date('m',$first['first']).'&startD='.date('d',$first['first']).'&method=1&detail=2&endY='.date('Y').'&endM='.date('m').'&endD='.date('d').'&export=0&bflag='.(tep_get_bflag_by_product_id($products['products_id']) == 0 ? 1 : 2).'&status=success&sort=4&compare=0&max=', 'NONSSL') . '">' . $products['products_ordered']. '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
          </tr>
        </table>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><div class="td_box"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
              </tr>
            </table>
		</td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
