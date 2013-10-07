<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $show_list_array = explode('-',$_GET['site_id']);
  } else {
      $show_list_str = tep_get_setting_site_info('cal_info.php');
      $sql_site_where = 'site_id in ('.$show_list_str.')';
      $show_list_array = explode(',',$show_list_str);
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
    <div class="compatible"> 
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
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
        <?php tep_show_site_filter('cal_info.php',true,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php
            if(!isset($_GET['type']) || $_GET['type'] == ''){
                      $_GET['type'] = 'asc';
            }
            if($cal_type == ''){
                $cal_type = 'asc';
            }
            if($_GET['sort'] == 'project'){
                if($_GET['type'] == 'desc'){
                   $cal_type = 'asc';
                }else{
                   $cal_type = 'desc';
                }
            }else if($_GET['sort'] == 'number'){
                if($_GET['type'] == 'desc'){
                   $cal_type = 'asc';
                }else{
                   $cal_type = 'desc';
                }
            }
            if($_GET['sort'] == 'project'){
                if($_GET['type'] == 'desc'){
                   $project = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                   $project = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'number'){
                if($_GET['type'] == 'desc'){
                   $number = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                   $number = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            $list_status_arr = array(); 
            $list_num = 0; 
            //所有订单状态信息 
            $orders_status_sql = "select orders_status_name, orders_status_id from ".TABLE_ORDERS_STATUS." where language_id = '".$languages_id."' group by orders_status_name,orders_status_id";
            $cal_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_status_sql, $cal_query_numrows);
            $orders_status_query = tep_db_query($orders_status_sql); 
            while ($orders_status_res = tep_db_fetch_array($orders_status_query)) {
              $site_orders_pending_query = tep_db_query("select count(*) as count from ".TABLE_ORDERS." where ".$sql_site_where." and orders_status = '".$orders_status_res['orders_status_id']."'"); 
              $site_orders_pending_res = tep_db_fetch_array($site_orders_pending_query); 
              $list_status_arr[]= array($orders_status_res['orders_status_name'], $site_orders_pending_res['count'],'orders'); 
            } 
            //各个网站所拥有的顾客数量 
            $site_customers_query = tep_db_query("select count(*) as count
                from " . TABLE_CUSTOMERS . " c left join " . 
                TABLE_ADDRESS_BOOK . " a 
                on c.customers_id = a.customers_id 
                and c.customers_default_address_id = a.address_book_id, ".
                TABLE_CUSTOMERS_INFO." ci , ".TABLE_SITES." s 
                where c.customers_id = ci.customers_info_id 
                and c.site_id = s.id and " .  $sql_site_where); 
            $site_customers_res = tep_db_fetch_array($site_customers_query); 
             
            //商品数量 
            $site_products_query = tep_db_query("select count(*) as count from ".TABLE_PRODUCTS); 
                                $site_products_res = tep_db_fetch_array($site_products_query); 
            //各个网站的评论数量 
            $site_reviews_query = tep_db_query("select count(*) as count from ".TABLE_REVIEWS." where ".$sql_site_where); 
            $site_reviews_res = tep_db_fetch_array($site_reviews_query); 
            $cal_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
            $notice_box = new notice_box('','',$keywords_table_params);
            $cal_table_row = array();
            $cal_title_row = array();
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox">');
            if($_GET['sort'] == 'project'){
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('cal_info.php','sort=project&type='.$cal_type.(isset($_GET['id']) && $_GET['id']?'&id='.$_GET['id']:'')).'">'.TEXT_CAL_PROJECT.$project.'</a>');
            }else{
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('cal_info.php','sort=project&type=desc'.(isset($_GET['id']) && $_GET['id']?'&id='.$_GET['id']:'')).'">'.TEXT_CAL_PROJECT.$project.'</a>');
            }
            if($_GET['sort'] == 'number'){
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('cal_info.php','sort=number&type='.$cal_type.(isset($_GET['id']) && $_GET['id']?'&id='.$_GET['id']:'')).'">'.TEXT_CAL_NUMBER.$number.'</a>');
            }else{
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('cal_info.php','sort=number&type=desc'.(isset($_GET['id']) && $_GET['id']?'&id='.$_GET['id']:'')).'">'.TEXT_CAL_NUMBER.$number.'</a>');
            }
            $cal_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
            $cal_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $cal_title_row);
            $orders_p_status_sql = "select orders_status_name, orders_status_id from ".TABLE_PREORDERS_STATUS." where language_id = '".$languages_id."' group by orders_status_name,orders_status_id";
            $cal_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_p_status_sql, $cal_query_numrows);
            $orders_p_status_query = tep_db_query($orders_p_status_sql); 
            while ($orders_status_res = tep_db_fetch_array($orders_p_status_query)) {
              $site_orders_pending_query = tep_db_query("select count(*) as count from ".TABLE_PREORDERS." where ".$sql_site_where." and orders_status = '".$orders_status_res['orders_status_id']."' order by count desc"); 
              $site_orders_pending_res = tep_db_fetch_array($site_orders_pending_query); 
              $list_status_arr[]= array($orders_status_res['orders_status_name'], $site_orders_pending_res['count'],'preorders'); 
            } 
            //商品数量 
            $site_products_query = tep_db_query("select count(*) as count from ".TABLE_PRODUCTS); 
            $site_products_res = tep_db_fetch_array($site_products_query); 
            $list_status_arr[] = array(HEADER_ENTRY_CAL_PRODUCTS, $site_products_res['count'],'NULL'); 
            //各个网站的评论数量 
            $site_reviews_query = tep_db_query("select count(*) as count from ".TABLE_REVIEWS." where ".$sql_site_where); 
            $site_reviews_res = tep_db_fetch_array($site_reviews_query); 
            $list_status_arr[] = array(HEADER_ENTRY_CAL_REVIEWS, $site_reviews_res['count'],'NULL'); 
            //各个网站所拥有的顾客数量 
            $site_customers_query = tep_db_query("select count(*) as count
                from " . TABLE_CUSTOMERS . " c left join " . 
                TABLE_ADDRESS_BOOK . " a 
                on c.customers_id = a.customers_id 
                and c.customers_default_address_id = a.address_book_id, ".
                TABLE_CUSTOMERS_INFO." ci , ".TABLE_SITES." s 
                where c.customers_id = ci.customers_info_id 
                and c.site_id = s.id and " .  $sql_site_where); 
            $site_customers_res = tep_db_fetch_array($site_customers_query); 
            $list_status_arr[] = array(HEADER_ENTRY_CAL_CUSTOMERS, $site_customers_res['count'],'NULL'); 
            $list_arr = array();
            $list_array = array();
            $list_project = array();
            for($i = 0;$i < count($list_status_arr);$i++){
               $list_arr[] = $list_status_arr[$i][1];
               $list_array[] = $list_status_arr[$i][0];
               $list_type[] = $list_status_arr[$i][2];
            }
            if($_GET['sort'] == 'number'){
               $k = 0;
               if($_GET['type'] == 'desc'){
                 arsort($list_arr);
                 foreach($list_arr as $key => $value){
                      $list_status_arr[$k][0] = $list_array[$key];
                      $list_status_arr[$k][1] = $list_arr[$key];
                      $list_status_arr[$k][2] = $list_type[$key];
                      $k++;
                 }
               }else{
                 asort($list_arr);
                 foreach($list_arr as $key => $value){
                      $list_status_arr[$k][0] = $list_array[$key];
                      $list_status_arr[$k][1] = $list_arr[$key];
                      $list_status_arr[$k][2] = $list_type[$key];
                      $k++;
                 }
               }
            }
            if($_GET['sort'] == 'project'){
               $k = 0;
               if($_GET['type'] == 'desc'){
                 arsort($list_array);
                 foreach($list_array as $key => $value){
                      $list_status_arr[$k][0] = $list_array[$key];
                      $list_status_arr[$k][1] = $list_arr[$key];
                      $list_status_arr[$k][2] = $list_type[$key];
                      $k++;
                 }
               }else{
                 asort($list_array);
                 foreach($list_array as $key => $value){
                      $list_status_arr[$k][0] = $list_array[$key];
                      $list_status_arr[$k][1] = $list_arr[$key];
                      $list_status_arr[$k][2] = $list_type[$key];
                      $k++;
                 }
               }
            }
            for($i = 0; $i < count($list_status_arr);$i++) {
              if($nowColor == ''){ $nowColor = 'dataTableRow'; }
              $even = 'dataTableSecondRow';
              $odd  = 'dataTableRow';
              if (isset($nowColor) && $nowColor == $odd) {
                   $nowColor = $even;
              } else {
                   $nowColor = $odd;
              }
              if(!isset($_GET['sort'])){
              $onclik = 'onClick="document.location.href=\''.tep_href_link('cal_info.php','id='.$list_status_arr[$i][0].$list_status_arr[$i][2].$list_status_arr[$i][1].(isset($_GET['sort'])?'&sort='.$_GET['sort']:'').(isset($_GET['type'])?'&type='.$_GET['type']:'')).'\'"';
              if(isset($_GET['id']) && $_GET['id'] == $list_status_arr[$i][0].$list_status_arr[$i][2].$list_status_arr[$i][1]){
              $cal_params = 'class=" dataTableRowSelected " onmouseover="this.style.cursor=\'hand\'" '; 
              }else{
              $cal_params = 'class=" '.$nowColor.' " onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\' '.$nowColor.' \'"';
              }
              $cal_info = array();
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"',
                  'text'   => '<input type="checkbox" disabled="disabled">'
                  );
              if($list_status_arr[$i][2] == 'orders'){
                  $orders = TEXT_CAL_ORDER;
              }else if($list_status_arr[$i][2] == 'preorders'){
                  $orders = TEXT_CAL_PREORDER;
              }else if($list_status_arr[$i][2] == 'NULL'){
                  $orders = '';
              }
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][0].$orders
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][1]
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent" align="right"',
                  'text'   => tep_image('images/icons/info_gray.gif')
                  );
              $cal_table_row[] = array('params' => $cal_params, 'text' => $cal_info);
              }else if($_GET['sort'] == 'number'){
              $onclik = 'onClick="document.location.href=\''.tep_href_link('cal_info.php','id='.$list_status_arr[$i][0].$list_status_arr[$i][1].$list_status_arr[$i][2].(isset($_GET['sort'])?'&sort='.$_GET['sort']:'').(isset($_GET['type'])?'&type='.$_GET['type']:'')).'\'"';
              if(isset($_GET['id']) && $_GET['id'] == $list_status_arr[$i][0].$list_status_arr[$i][2].$list_status_arr[$i][1]){
              $cal_params = 'class=" dataTableRowSelected " onmouseover="this.style.cursor=\'hand\'" '; 
              }else{
              $cal_params = 'class=" '.$nowColor.' " onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\' '.$nowColor.' \'"';
              }
              $cal_info = array();
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"',
                  'text'   => '<input type="checkbox" disabled="disabled">'
                  );
              if($list_status_arr[$i][2] == 'orders'){
                  $orders = TEXT_CAL_ORDER;
              }else if($list_status_arr[$i][2] == 'preorders'){
                  $orders = TEXT_CAL_PREORDER;
              }else if($list_status_arr[$i][2] == 'NULL'){
                  $orders = '';
              }
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][0].$orders
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][1]
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent" align="right"',
                  'text'   => tep_image('images/icons/info_gray.gif')
                  );
              $cal_table_row[] = array('params' => $cal_params, 'text' => $cal_info);
              }else if($_GET['sort'] == 'project'){
              $onclik = 'onClick="document.location.href=\''.tep_href_link('cal_info.php','id='.$list_status_arr[$i][0].$list_status_arr[$i][2].$list_status_arr[$i][1].(isset($_GET['sort'])?'&sort='.$_GET['sort']:'').(isset($_GET['type'])?'&type='.$_GET['type']:'')).'\'"';
              if(isset($_GET['id']) && $_GET['id'] == $list_status_arr[$i][0].$list_status_arr[$i][2].$list_status_arr[$i][1]){
              $cal_params = 'class=" dataTableRowSelected " onmouseover="this.style.cursor=\'hand\'" '; 
              }else{
              $cal_params = 'class=" '.$nowColor.' " onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\' '.$nowColor.' \'"';
              }
              $cal_info = array();
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"',
                  'text'   => '<input type="checkbox" disabled="disabled">'
                  );
              if($list_status_arr[$i][2] == 'orders'){
                  $orders = TEXT_CAL_ORDER;
              }else if($list_status_arr[$i][2] == 'preorders'){
                  $orders = TEXT_CAL_PREORDER;
              }else if($list_status_arr[$i][2] == 'NULL'){
                  $orders = '';
              }
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][0].$orders
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent"'.$onclik,
                  'text'   => $list_status_arr[$i][1]
                  );
              $cal_info[] = array(
                  'params' => 'class="dataTableContent" align="right"',
                  'text'   => tep_image('images/icons/info_gray.gif')
                  );
              $cal_table_row[] = array('params' => $cal_params, 'text' => $cal_info);
              }
             }
             $notice_box->get_contents($cal_table_row);
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
             if( count($cal_table_row) > 0){
                if($ocertify->npermission >= 15){
                   echo '<select  disabled="disabled">';
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
              <td class="smallText" valign="top"><?php echo $cal_split->display_count(count($list_status_arr), MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
              </td>
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
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
