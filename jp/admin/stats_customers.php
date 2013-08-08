<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 's.id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $show_list_array = explode('-',$_GET['site_id']);
  } else {
      $show_list_str = tep_get_setting_site_info(FILENAME_STATS_CUSTOMERS);
      $sql_site_where = 's.id in ('.$show_list_str.')';
      $show_list_array = explode(',',$show_list_str);
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
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
        <?php tep_show_site_filter(FILENAME_STATS_CUSTOMERS,true,array(0));?>
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
              $stats_str = 'ordersum desc';
            }else if($_GET['sort'] == 'site_name'){
              if($_GET['type'] == 'desc'){ 
                $stats_str = 'romaji desc';
                $stats_type = 'asc';
              }else{
                $stats_str = 'romaji asc';
                $stats_type = 'desc';
              }
            }else if($_GET['sort'] == 'customers'){
              if($_GET['type'] == 'desc'){ 
                $stats_str = 'customers_lastname desc';
                $stats_type = 'asc';
              }else{
                $stats_str = 'customers_lastname asc';
                $stats_type = 'desc';
              }
            }else if($_GET['sort'] == 'ordersum'){
              if($_GET['type'] == 'desc'){ 
                $stats_str = 'ordersum desc';
                $stats_type = 'asc';
              }else{
                $stats_str = 'ordersum asc';
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
            }else if($_GET['sort'] == 'customers_f'){
              if($_GET['type'] == 'desc'){ 
                $stats_str = 'customers_firstname desc';
                $stats_type = 'asc';
              }else{
                $stats_str = 'customers_firstname asc';
                $stats_type = 'desc';
              }
            }
            if($_GET['sort'] == 'site_name'){
                 if($_GET['type'] == 'desc'){
                      $site_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                  }else{
                      $site_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                  }
            }
            if($_GET['sort'] == 'customers'){
                 if($_GET['type'] == 'desc'){
                      $customers = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                  }else{
                      $customers = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                  }
            }
            if($_GET['sort'] == 'customers_f'){
                 if($_GET['type'] == 'desc'){
                      $customers_f = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                  }else{
                      $customers_f = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                  }
            }
            if($_GET['sort'] == 'ordersum'){
                 if($_GET['type'] == 'desc'){
                      $ordersum = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                  }else{
                      $ordersum = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
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
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=rownum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_NUMBER.$rownum.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=rownum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_NUMBER.$rownum.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'site_name'){ 
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=site_name&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_SITE.$site_name.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=site_name&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_SITE.$site_name.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'customers'){ 
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=customers&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_LASTNAME.$customers.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=customers&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_LASTNAME.$customers.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'customers_f'){ 
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=customers_f&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_FIRSTNAME.$customers_f.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=customers_f&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_FIRSTNAME.$customers_f.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'ordersum'){ 
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=ordersum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type='.$stats_type).'">'.TABLE_HEADING_TOTAL_PURCHASED.$ordersum.'</a>');
            }else{
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort=ordersum&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'&type=desc').'">'.TABLE_HEADING_TOTAL_PURCHASED.$ordersum.'</a>');
            }
            $stats_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
            $stats_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $stats_title_row);
    if (isset($_GET['page']) and $_GET['page'] > 1) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
    $customers_query_raw = "select * from (select (@mycnt := @mycnt + 1) as rownum,ordersum,romaji,customers_firstname,customers_email_address,customers_lastname from(select c.customers_firstname, 
                                 c.customers_lastname as customers_lastname, 
                                 sum(op.products_quantity * op.final_price) as
                                 ordersum ,c.customers_email_address as
                                 customers_email_address ,
                                 s.romaji
                          from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS . " o , " . TABLE_SITES . " s
                          where c.customers_id = o.customers_id 
                            and o.orders_id = op.orders_id 
                            and c.site_id = s.id
                            and " . $sql_site_where . "
                          group by c.customers_firstname, c.customers_lastname) g";
                          $customers_query_raw .= ' order by ordersum
                          desc,customers_firstname, customers_lastname,romaji asc ';
                          $customers_query_raw .= " ) z order by ".$stats_str;
  $customers_query_raw2 = $customers_query_raw;
  $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
  
  // fix counted customers
  $customers_count_query = tep_db_query($customers_query_raw2);
  $customers_query_numrows = tep_db_num_rows($customers_count_query);
  tep_db_query("set @mycnt=0");
  $customers_query = tep_db_query($customers_query_raw);
  $rows = 0;
  $stats_num = tep_db_num_rows($customers_query);
  while ($customers = tep_db_fetch_array($customers_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
    $onlick = 'onClick="document.location.href=\''.tep_href_link(FILENAME_STATS_CUSTOMERS,'sort='.$_GET['sort'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$customers['rownum'].'&type='.$_GET['type']).'\'"'; 
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
   if($_GET['id'] == $customers['rownum']){
   $stats_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
   }else{
   $stats_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"'; 
   }
   $stats_info = array();
   $stats_info[] = array(
       'params' => 'class="dataTableContent"',
       'text'   => '<input type="checkbox" disabled="disabled">'
       ); 
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => $customers['rownum']
       ); 
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => $customers['romaji']
       ); 
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, 'search=' . urlencode($customers['customers_lastname']), 'NONSSL') . '">' . $customers['customers_lastname'] . '</a>'
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent"'.$onlick,
       'text'   => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, 'search=' . urlencode($customers['customers_lastname']), 'NONSSL') . '">' . $customers['customers_firstname']. '</a>'
       );
   $stats_info[] = array(
       'params' => 'class="dataTableContent" align="right"'.$onlick,
       'text'   => '<a href="' .  tep_href_link(FILENAME_ORDERS,'email='.$customers['customers_email_address'].'&keywords=&search_type=os_2', 'NONSSL') . '">' .(($customers['ordersum'] < 0)?'<font color="#ff0000">':'').$currencies->format($customers['ordersum']).(($customers['ordersum'] < 0)?'</font>':'').'</a>'
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
                <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                <td class="smallText" align="right"><div class="td_box"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page','info', 'x', 'y', 'id'))); ?></div></td>
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
