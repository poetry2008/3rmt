<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
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
        <td>
         <?php 
          $site_query = tep_db_query("select id from ".TABLE_SITES);
          $site_list_array = array();
          while($site_array = tep_db_fetch_array($site_query)){
                $site_list_array[] = $site_array['id'];
          }
          tep_show_site_filter('stats_products_purchased.php',false,$site_list_array);
         ?> 
         <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php 
            if(!isset($_GET['type']) || $_GET['type'] == ''){
                      $_GET['type'] = 'asc';
            }
            if($stats_type == ''){
                 $stats_type = 'asc';
            }
            if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                  $stats_str = 'products_ordered desc, products_name asc';
            }else if($_GET['sort'] == 'products_name'){
                  if($_GET['type'] == 'desc'){
                      $stats_str = 'products_name desc';
                      $stats_type = 'asc';
                   }else{
                      $stats_str = 'products_name asc';
                      $stats_type = 'desc';
                   }
            }else if($_GET['sort'] == 'products_ordered'){
                  if($_GET['type'] == 'desc'){
                      $stats_str = 'products_ordered desc, products_name asc';
                      $stats_type = 'asc';
                   }else{
                      $stats_str = 'products_ordered asc, products_name desc';
                      $stats_type = 'desc';
                   }
            }else if($_GET['sort'] == 'rownum'){
                  if($_GET['type'] == 'desc'){
                      $stats_str = 'rownum desc';
                      $stats_type = 'asc';
                   }else{
                      $stats_str = 'rownum asc';
                      $stats_type = 'desc';
                   }
            }
            if($_GET['sort'] == 'products_name'){
                    if($_GET['type'] == 'desc'){
                         $products_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                     }else{
                         $products_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                     }
            }
            if($_GET['sort'] == 'products_ordered'){
                    if($_GET['type'] == 'desc'){
                         $products_ordered = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                     }else{
                         $products_ordered = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                     }
            }
            if($_GET['sort'] == 'rownum'){
                    if($_GET['type'] == 'desc'){
                         $rownum = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                     }else{
                         $rownum = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                     }
            }
            $stats_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0'); 
            $notice_box = new notice_box('','',$stats_table_params);
            $stats_table_row = array(); 
            $stats_title_row = array();
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox">');
            if(isset($_GET['sort']) && $_GET['sort'] == 'rownum'){
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=rownum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_NUMBER.$rownum.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=rownum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_NUMBER.$rownum.'</a>');
            }
            if($_GET['sort'] == 'products_name'){
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=products_name&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_PRODUCTS.$products_name.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=products_name&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_PRODUCTS.$products_name.'</a>');
            }
            if($_GET['sort'] == 'products_ordered'){
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => 
            '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=products_ordered&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_PURCHASED.$products_ordered.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort=products_ordered&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_PURCHASED.$products_ordered.'</a>');
            }
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
            $stats_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $stats_title_row);
  if (isset($_GET['page']) && $_GET['page'] > 1) {
    $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  } else {
    $rows = 0;
  }
  $products_query_raw = "
    select * from (select (@mycnt := @mycnt + 1) as rownum,products_ordered,products_name from(select p.products_id, 
           p.products_ordered as products_ordered, 
           pd.products_name as products_name
    from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where pd.products_id = p.products_id 
      and pd.language_id = '" . $languages_id. "' 
      and pd.site_id ='0'
      and p.products_ordered > 0 
    group by pd.products_id ) g order by products_ordered desc,products_name asc) z order by ".$stats_str;
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
  tep_db_query("set @mycnt=0");
  $products_query = tep_db_query($products_query_raw);
  $stats_num = tep_db_num_rows($products_query);
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
   if($_GET['id'] == $products['rownum']){
       $stats_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
   }else{
       $stats_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
   }    
   $onlick = 'onClick="document.location.href=\''.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED,'sort='.$_GET['sort'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$products['rownum'].'&type='.$_GET['type']).'\'"';
   $stats_info = array();
   $stats_info[] = array(
       'params' => 'class="dataTableContent"',
       'text'   => '<input type="checkbox" disabled="disabled">'
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => $products['rownum']
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'search='.$products['products_name']) . '">' . $products['products_name'] . '</a>'
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT, 'report=5&site_id=&is_select=1&add_product_categories_id='.$current_category_id.'&cid='.$current_category_id.'&pid='.$products['products_id'].'&products_id='.$products['products_id'].'&startY='.date('Y',$first['first']).'&startM='.date('m',$first['first']).'&startD='.date('d',$first['first']).'&method=1&detail=2&endY='.date('Y').'&endM='.date('m').'&endD='.date('d').'&export=0&bflag='.(tep_get_bflag_by_product_id($products['products_id']) == 0 ? 1 : 2).'&status=success&sort=4&compare=0&max=', 'NONSSL') . '">' . $products['products_ordered']. '</a>'
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent" align="right"',
       'text'   => tep_image('images/icons/info_gray.gif') 
       );
   $stats_table_row[] = array('params' => $stats_params, 'text' => $stats_info);
  }
   $notice_box->get_contents($stats_table_row);
   $notice_box->get_eof(tep_eof_hidden());
   echo $notice_box->show_notice();
?>
           </td>
          </tr>
        </table>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                   <td>
                    <?php
                       if($stats_num > 0){
                           if($ocertify->npermission >= 15){
                               echo '<select disabled="disabled">';
                               echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';
                               echo '<option value="1">'.TEXT_CONTENTS_DELETE_ACTION.'</option>';
                               echo '</select>';
                            }
                        }else{
                               echo TEXT_DATA_EMPTY;
                        }
                     ?>
                    </td>
                  </tr>
              <tr>
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><div class="td_box"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'id'))); ?></div></td>
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
