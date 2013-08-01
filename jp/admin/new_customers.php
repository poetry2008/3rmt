<?php
/*
  $Id$
  
  统计某段时间内第一次完成订单的顾客，需要REFRESH刷新数据
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 'c.site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
  } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_NEW_CUSTOMERS);
     $sql_site_where = 'c.site_id in ('.$show_list_str.')';
     $show_list_array = explode(',',$show_list_str);
   }

  if (!isset($_GET['s_y'])){
    $_GET['s_y'] = date('Y');
  }
  if (!isset($_GET['s_m'])){
    $_GET['s_m'] = date('m');
  }
  if (!isset($_GET['s_d'])){
    $_GET['s_d'] = date('d');
  }
  if (!isset($_GET['e_y'])){
    $_GET['e_y'] = date('Y');
  }
  if (!isset($_GET['e_m'])){
    $_GET['e_m'] = date('m');
  }
  if (!isset($_GET['e_d'])){
    $_GET['e_d'] = date('d');
  }
  $startTime = $_GET['s_y'].'-'.$_GET['s_m'].'-'.$_GET['s_d'].' '.'00:00:00';
  $endTime   = $_GET['e_y'].'-'.$_GET['e_m'].'-'.$_GET['e_d'].' '.'23:59:59';
//refresh TBALE_CUSTOMERS.cusotmers_firstorderat
 if(isset($_GET['action'])&&$_GET['action']){
    switch ($_GET['action']){
/* -----------------------------------------------------
   case 'refresh' 更新用户的第一次完成订单的时间 
------------------------------------------------------*/
    case 'refresh':
   $ref_sql="update customers c , (SELECT o.customers_id as o_id ,min(osh.date_added) as osh_add
      FROM orders o LEFT JOIN orders_status_history as osh ON osh.orders_id = o.orders_id AND osh.orders_status_id in (2,5), customers c1
      WHERE  o.customers_id = c1.customers_id
        AND o.orders_status in (2,5)
      GROUP BY o.customers_id
      ORDER BY osh.date_added DESC ) as c2 set  c.customers_firstorderat = c2.osh_add where c.customers_id=c2.o_id";
tep_db_query($ref_sql);
$ref_time=$logger->times[8];
$ref_num=mysql_affected_rows();
tep_redirect(tep_href_link(FILENAME_NEW_CUSTOMERS)."?r_t=".$ref_time."&&r_num=".$ref_num);
break;
}
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
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><?php echo tep_draw_form('search', FILENAME_NEW_CUSTOMERS, tep_get_all_get_params(), 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          </form></tr>
        </table>
        <p><?php echo NEW_CUSTOMERS_TITLE_TEXT;?></p>
    <!--ORDER EXPORT SCRIPT -->
    <form action="<?php echo tep_href_link(FILENAME_NEW_CUSTOMERS) ; ?>" method="get">
    <table  border="0" cellpadding="0" cellspacing="2">
    <tr>
      
      <td class="smallText">
      <?php echo NEW_CUSTOMERS_SEARCH_START;?> 
      <select name="s_y">
      <?php for($i=2005; $i<2020; $i++) { 
        if (isset($_GET['s_y']) && $i == $_GET['s_y']) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        }else{ 
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
      <?php echo YEAR_TEXT;?>
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { 
        if (isset($_GET['s_m']) && $i == $_GET['s_m']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{ 
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; 
        }  
      } ?>    
      </select>
      <?php echo MONTH_TEXT;?>
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if (isset($_GET['s_d']) && $i == $_GET['s_d']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
      <td width="80" align="center">～</td>
      <td class="smallText">
      <?php echo NEW_CUSTOMERS_SEARCH_END;?> 
      <select name="e_y">
      <?php
      for($i=2005; $i<2020; $i++) {
        if (isset($_GET['e_y']) && $i == $_GET['e_y']) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      <?php echo YEAR_TEXT;?>
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if (isset($_GET['e_m']) && $i == $_GET['e_m']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo MONTH_TEXT;?>
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if (isset($_GET['e_d']) && $i == $_GET['e_d']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
      <td><input type="submit" value="<?php echo IMAGE_SEARCH;?>"></td>
      </tr>
    </table>
    </form>
    <!--ORDER EXPORT SCRIPT EOF //-->
    <table  border="0" cellpadding="2" cellspacing="2" width="100%">
<tr>
<td align="left" width="200" >
<?php 
if(isset($_GET['r_t'])&&$_GET['r_t']){
$ref_s=$_GET['r_t']/1000;
echo  REFRESH_TIME.$ref_s."&nbsp".SECOND_TEXT;

      if(isset($_GET['r_num'])&&$_GET['r_num']){
        echo "</td><td>&nbsp".REFRESH_NUM.$_GET['r_num'];
      }

}
?>

</td>
</tr></table>        
        </td>
      </tr>
      <tr><td>
        <?php tep_show_site_filter(FILENAME_NEW_CUSTOMERS,true,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php 
            if(!isset($_GET['type']) || $_GET['type'] == ''){
                $_GET['type'] = 'asc';
            }
            if($new_type == ''){
               $new_type = 'asc';
            }
            if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                $new_str = 'date_account_created  DESC';
            }else if($_GET['sort'] == 'site_romaji'){
                  if($_GET['type'] == 'desc'){
                    $new_str = 'c.site_id desc';
                    $new_type = 'asc';
                  }else{
                    $new_str = 'c.site_id asc';
                    $new_type = 'desc';
                  }
            }else if($_GET['sort'] == 'customers_guest_chk'){
                  if($_GET['type'] == 'desc'){
                    $new_str = 'c.customers_guest_chk desc';
                    $new_type = 'asc';
                  }else{
                    $new_str = 'c.customers_guest_chk asc';
                    $new_type = 'desc';
                  }
            }else if($_GET['sort'] == 'customers_lastname'){
                  if($_GET['type'] == 'desc'){
                    $new_str = 'c.customers_lastname desc';
                    $new_type = 'asc';
                  }else{
                    $new_str = 'c.customers_lastname asc';
                    $new_type = 'desc';
                  }
            }else if($_GET['sort'] == 'customers_firstname'){
                  if($_GET['type'] == 'desc'){
                    $new_str = 'c.customers_firstname desc';
                    $new_type = 'asc';
                  }else{
                    $new_str = 'c.customers_firstname asc';
                    $new_type = 'desc';
                  }
            }else if($_GET['sort'] == 'date_account_created'){
                  if($_GET['type'] == 'desc'){
                    $new_str = 'date_account_created desc';
                    $new_type = 'asc';
                  }else{
                    $new_str = 'date_account_created asc';
                    $new_type = 'desc';
                  }
            }
            if($_GET['sort'] == 'site_romaji'){
                 if($_GET['type'] == 'desc'){
                     $site_romaji = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                     $site_romaji = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'customers_guest_chk'){
                 if($_GET['type'] == 'desc'){
                     $customers_guest_chk = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                     $customers_guest_chk = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'customers_lastname'){
                 if($_GET['type'] == 'desc'){
                     $customers_lastname = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                     $customers_lastname = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'customers_firstname'){
                 if($_GET['type'] == 'desc'){
                     $customers_firstname = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                     $customers_firstname = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'date_account_created'){
                 if($_GET['type'] == 'desc'){
                     $date_account_created = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                     $date_account_created = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
        if($_GET['s_y'] == ''){
           $_GET['s_y'] = date('Y');
        }
        if($_GET['s_m'] == ''){
           $_GET['s_m'] = date('m')-1;
        }
        if($_GET['s_d'] == ''){
           $_GET['s_d'] = date('d');
        }
        if($_GET['e_y'] == ''){
           $_GET['e_y'] = date('Y');
        }
        if($_GET['e_m'] == ''){
           $_GET['e_m'] = date('m');
        }
        if($_GET['e_d'] == ''){
           $_GET['e_d'] = date('d');
        }
            $new_able_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0'); 
            $notice_box = new notice_box('','',$new_table_params);
            $new_table_row = array();
            $new_title_row = array();
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox">');
            if(isset($_GET['sort']) && $_GET['sort'] == 'site_romaji'){
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=site_romaji&type='.$new_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_SITE.$site_romaji.'</a>');
            }else{
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=site_romaji&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_SITE.$site_romaji.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'customers_guest_chk'){
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_guest_chk&type='.$new_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.  TABLE_HEADING_MEMBER_TYPE.$customers_guest_chk.'</a>');
            }else{
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_guest_chk&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_MEMBER_TYPE.$customers_guest_chk.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'customers_lastname'){
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_lastname&type='.$new_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_LASTNAME.$customers_lastname.'</a>');
            }else{
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_lastname&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_LASTNAME.$customers_lastname.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'customers_firstname'){
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_firstname&type='.$new_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_FIRSTNAME.$customers_firstname.'</a>');
            }else{
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=customers_firstname&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_FIRSTNAME.$customers_firstname.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'date_account_created'){
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=date_account_created&type='.$new_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_ACCOUNT_CREATED.$date_account_created.'</a>');
            }else{
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort=date_account_created&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_ACCOUNT_CREATED.$date_account_created.'</a>');
            }
            $new_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
            $new_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $new_title_row);

    $customers_query_raw = "
      SELECT c.customers_id,
             c.customers_guest_chk,
             c.customers_lastname, 
             c.customers_firstname, 
             c.site_id,
             c.customers_email_address,
             ci.customers_info_date_account_created as date_account_created
      FROM customers c , ".TABLE_CUSTOMERS_INFO." ci
      WHERE c.customers_id = ci.customers_info_id and 
     c.`customers_firstorderat` < '" . $endTime . "' AND c.`customers_firstorderat` > '" . $startTime . "' ".' AND '.$sql_site_where."
      group by  c.customers_id  
      ORDER BY ".$new_str;
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    $new_num = tep_db_num_rows($customers_query);
    while ($customers = tep_db_fetch_array($customers_query)) {
      if($customers['customers_guest_chk'] == 1) {
        $type = TABLE_HEADING_MEMBER_TYPE_GUEST;
      } else {
        $type = TABLE_HEADING_MEMBER_TYPE_MEMBER;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if($_GET['id'] == $customers['customers_id']){
      $new_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
      }else{
      $new_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
      $new_info = array();
      $new_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<input type="checkbox" disabled="disabled">'
          );
      $onclick = 'onClick="document.location.href=\''.tep_href_link(FILENAME_NEW_CUSTOMERS,'sort='.$_GET['sort'].'&type='.$_GET['type'].'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&e_d='.$_GET['e_d']).'&id='.$customers['customers_id'].'\'"';
      $new_info[] = array(
          'params' => 'class="dataTableContent" '.$onclick,
          'text'   => tep_get_site_romaji_by_id($customers['site_id'])
          );
      $new_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => $type
          );
      $new_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($customers['customers_lastname'])
          );
      $new_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($customers['customers_firstname'])
          );
      $new_info[] = array(
          'params' => 'class="dataTableContent" align="right"'.$onclick,
          'text'   => tep_date_short($customers['date_account_created'])
          );
      $new_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => tep_image('images/icons/info_gray.gif')
          );
 
      $new_table_row[] = array('params' => $new_params, 'text' => $new_info);
    }
      $notice_box->get_contents($new_table_row);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                           <tr>
                             <td>
                             <?php
                                  if($new_num > 0){
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
                    <td class="smallText" align="right"><div class="td_box"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></div></td>
                  </tr>
<?php
                                                                      if (isset($_GET['search']) and tep_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><div class="td_button"><?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS) . '">' . tep_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></div></td>
                  </tr>
<?php
    }
?>
                </table>
			</td>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
