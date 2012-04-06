<?php
/*
 * お届け時間指定グループの設定
 */
require('includes/application_top.php'); 
$action = $_GET['action'];

if(isset($action) && $action != ''){

  switch($action){

  case 'save':
    $products_id = tep_db_prepare_input($_POST['cid']);
    $products_name = tep_db_prepare_input($_POST['name']);
    $work_start_hour = tep_db_prepare_input($_POST['work_start_hour']);
    $work_start_min = tep_db_prepare_input($_POST['work_start_min']);
    $work_end_hour = tep_db_prepare_input($_POST['work_end_hour']);
    $work_end_min = tep_db_prepare_input($_POST['work_end_min']);
    $db_set_day = tep_db_prepare_input($_POST['db_set_day']);
    $shipping_time = tep_db_prepare_input($_POST['shipping_time']);
    $sort = tep_db_prepare_input($_POST['sort']);

    $work = array();
    foreach($work_start_hour as $w_key=>$w_value){
      
      if(trim($w_value) != ''){ 
        $w_value = (int)$w_value < 10 ? '0'.(int)$w_value : $w_value;
        $work_start_min[$w_key] = (int)$work_start_min[$w_key] < 10 ? '0'.(int)$work_start_min[$w_key] : $work_start_min[$w_key];
        $work_end_hour[$w_key] = (int)$work_end_hour[$w_key] < 10 ? '0'.(int)$work_end_hour[$w_key] : $work_end_hour[$w_key];
        $work_end_min[$w_key] = (int)$work_end_min[$w_key] < 10 ? '0'.(int)$work_end_min[$w_key] : $work_end_min[$w_key];
        $work[] = array($w_value.':'.$work_start_min[$w_key],$work_end_hour[$w_key].':'.$work_end_min[$w_key]);
      }
    } 
    $work_str = serialize($work);
    //这里判断是添加，还是修改
    if($products_id == ''){

       $products_sql = "insert into ". TABLE_PRODUCTS_SHIPPING_TIME .
                   " values(NULL".
                   ",'". $products_name .
                   "','". $work_str .
                   "','". $db_set_day .
                   "','". $shipping_time .
                   "','". $sort .                   
                   "','0')";

    }else{
      $products_sql = "update ". TABLE_PRODUCTS_SHIPPING_TIME .
                   " set ".
                   "name='". $products_name .
                   "',work='". $work_str .
                   "',db_set_day='". $db_set_day .
                   "',shipping_time='". $shipping_time .
                   "',sort='". $sort .
                   "' where id=". $products_id;
    }
    
    $products_query = tep_db_query($products_sql);

    if($products_query == true){
      
      tep_db_free_result($products_query);
      tep_db_close();
      header("location:products_shipping_time.php");

    }

    break;
  case 'del':
    if(isset($_GET['id']) && $_GET['id']){
      $products_id = $_GET['id'];
      $products_sql = "update ". TABLE_PRODUCTS_SHIPPING_TIME .
                   " set status='1' where id=".$products_id;
      $products_del_query = tep_db_query($products_sql);

      if($products_del_query == true){
      
        tep_db_free_result($products_del_query);
        tep_db_close();
        header("location:products_shipping_time.php");
      }

    }else{
      $products_id = $_POST['cid'];
      $products_sql = "delete from ". TABLE_PRODUCTS_SHIPPING_TIME .
                      " where id=".$products_id;
      $products_del_query = tep_db_query($products_sql);

      if($products_del_query == true){
      
        tep_db_free_result($products_del_query);
        tep_db_close();
        header("location:products_shipping_time.php");
      }
   }
    break;
  case 'res':

    $products_id = $_GET['id'];
    $products_sql = "update ". TABLE_PRODUCTS_SHIPPING_TIME .
                   " set status='0' where id=".$products_id;
    $products_del_query = tep_db_query($products_sql);

    if($products_del_query == true){
      tep_db_free_result($products_del_query);
      tep_db_close();
      header("location:products_shipping_time.php");
    }
    break;

  }  
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
div#show {
  left:18%;
  width:70%;
  position:absolute;
}
</style>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation //--> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof //-->
    </td></tr></table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
            <td valign="top"><div id="show" style="display:none"></div><table border="0" width="100%" cellspacing="0" cellpadding="2" id="group_list_box">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_1; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_2; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_4; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_5; ?></td>                
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_6; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_7; ?></td>
              </tr>
<?php
$even = 'dataTableSecondRow';
$odd  = 'dataTableRow';
$select_class = 'dataTableRowSelected';
$products_sql = "select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." order by sort asc,id asc";

$products_page = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_sql, $products_query_numrows);
$products_query = tep_db_query($products_sql);
$i = 0;
while($products_array = tep_db_fetch_array($products_query)){
  if((int)$_GET['id'] == $products_array['id'] || ($i == 0 && !isset($_GET['id']))){
    $nowColor = $select_class;
    $onmouseover = 'onmouseover="this.className=\'dataTableRowSelected\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$select_class.'\'"';
  }else{
    $nowColor = $i % 2 == 1 ? $even : $odd;
    $onmouseover = 'onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
  }
  $work_array = unserialize($products_array['work']);
  $work_str = '';
  foreach($work_array as $w_key=>$w_value){

    if($w_key < 3){
      $work_str .= $w_value[0].'～'.$w_value[1].'&nbsp;&nbsp;'; 
    }else{

      $work_str .= '......';
      break;
    }
  }


  echo '<tr class="'.$nowColor.'" '. $onmouseover .'>' . "\n";
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $products_array['id'] .'\'">'.$products_array['name'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $products_array['id'] .'\'">'.$work_str.'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $products_array['id'] .'\'">'. $products_array['db_set_day'] .'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $products_array['id'] .'\'">'. $products_array['sort'] .'</td>'; 
  echo '<td>';
  if($products_array['status'] == 0){
?>
  <img border="0" src="images/icon_status_green.gif" alt="" title="有効">
  <a title="無効にする" onclick="if(confirm('無効にしますか？')){check_on_products('del',<?php echo $products_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_red_light.gif"></a>

<?php
}else{
?>
  <a title="有効" onclick="if(confirm('有効にしますか？')){check_on_products('res',<?php echo $products_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_green_light.gif"></a>
<img border="0" alt="" src="images/icon_status_red.gif" title="無効にする">

<?php
}
?>
</td>
<?php
  echo '<td><a href="javascript:void(0);" onclick="show_text_products('. $products_array['id'] .',this);">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a></td>';
  echo '</tr>';
  $i++;
}

tep_db_free_result($products_query);
tep_db_close();
?>
<td colspan="4">
<?php echo $products_page->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?>
</td>
<td colspan="5" align="right">
<?php echo $products_page->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page'))); ?>
</td>
</tr>
<tr><td align="right" colspan="9"><button onclick="show_text_products(0,this);"><?php echo TABLE_BUTTON;?></button></td></tr>
</table></td></tr></table></td></tr>
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
