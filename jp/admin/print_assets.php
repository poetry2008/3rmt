<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies(2);
$bflag =0 ;
$min_order_date_sql = "SELECT date_purchased
FROM  ".TABLE_ORDERS." 
ORDER BY  `date_purchased` ASC 
LIMIT 1";
$min_order_date_query = tep_db_query($min_order_date_sql);
$min_order_date_row = tep_db_fetch_array($min_order_date_query);
$min_order_date_value = $min_order_date_row['date_purchased'];

$startDate = "";
$startDateG = 0;
if ( isset($_GET['startD']) && ($_GET['startD']) && (tep_not_null($_GET['startD'])) ) 
{    $sDay = $_GET['startD'];
  $startDateG = 1;
}
if ( isset($_GET['startM']) && ($_GET['startM']) && (tep_not_null($_GET['startM'])) ) 
{    $sMon = $_GET['startM'];
  $startDateG = 1;
}
if ( isset($_GET['startY']) && ($_GET['startY']) && (tep_not_null($_GET['startY'])) ) 
{    $sYear = $_GET['startY'];
  $startDateG = 1;
}
if ($startDateG) {
  if(date('Y',strtotime($min_order_date_value))== $sYear&& 
  date('m',strtotime($min_order_date_value))== $sMon&& 
  date('d',strtotime($min_order_date_value))== $sDay+1){
    $startDate = strtotime($min_order_date_value);
    $start_time = date('H:i:s',strtotime($min_order_date_value));
  }else{
    $start_time = date('H:i:s');
    $startDate = mktime(0, 0, 0, $sMon, $sDay+1, $sYear);
  }

} else {
  $start_time = date('H:i:s',strtotime($min_order_date_value));
  $startDate = strtotime($min_order_date_value);
}

$endDate = "";
$endDateG = 0;
if ( isset($_GET['endD']) && ($_GET['endD']) && (tep_not_null($_GET['endD'])) ) {
  $eDay = $_GET['endD'];
  $endDateG = 1;
} else {
  $eDay = 1;
}
if ( isset($_GET['endM']) && ($_GET['endM']) && (tep_not_null($_GET['endM'])) ) {
  $eMon = $_GET['endM'];
  $endDateG = 1;
} else {
  $eMon = 1;
}
if ( isset($_GET['endY']) && ($_GET['endY']) && (tep_not_null($_GET['endY'])) ) {
  $eYear = $_GET['endY'];
  $endDateG = 1;
} else {
  $eYear = date("Y");
}
if ($endDateG) {
  $endDate = mktime(0, 0, 0, $eMon, $eDay + 1, $eYear);
} else {
  $endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link media="print" rel="stylesheet" type="text/css" href="includes/print_assets.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript">
  window.print();
</script>
<title><?php echo ASSETS_TITLE;?></title>
</head>
<body>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr>
<td width="100%" valign="top">
<div class="print_assets_title">
<?php echo ASSETS_TITLE;?>
</div>
<?php
set_time_limit(0);
//简易页面
if(isset($_GET['pid'])&&$_GET['pid']!=''){
  //product history info
  $now_time = date('H:i:s');
  $start = $_GET['startY']."-".$_GET['startM']."-".$_GET['startD']." ".$start_time;
  $end = $_GET['endY']."-".$_GET['endM']."-".$_GET['endD']." ".$now_time;
  $site_id = (isset($_GET['site_id'])&&$_GET['site_id'])?$_GET['site_id']:0;
  $product_history_sql =
    tep_get_order_history_sql_by_pid($_GET['pid'],$start,$end,$_GET['sort_order']);
  $product_history_query = tep_db_query($product_history_sql);
  ?>
  <?php
  if(tep_db_num_rows($product_history_query)){
    ?>
      <table width="99%" cellpadding="0" cellspacing="1" border="0" class="assets_box">
      <tr class="assets_text">
      <td align="center"><?php echo TEXT_PRODUCTS_DATE;?></td>
      <td align="center"><?php echo TEXT_ORDER_PRODUCTS_NAME;?></td>
      <td align="center"><?php echo TEXT_ORDER_PRODUCTS_QUANTITY;?></td>
      <td align="center"><?php echo TEXT_ORDER_PRODUCTS_PRICE;?></td>
      </tr>
      <?php
      while($row = tep_db_fetch_array($product_history_query)){
        ?>
          <tr class="assets_c">
          <td><?php echo $row['torihiki_date'];?></td>
          <td><?php echo $row['products_name'];?></td>
          <td align="right"><?php echo $row['products_quantity'];?></td>
          <td align="right"><?php echo $currencies->format($row['final_price']);?></td>
          </tr>
          <?php
      }
    ?>
      </table>
      <?php
  }else{
    echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
  }
}else{
  ?>
  <?php
  $now_time = date('H:i:s');
  $start = $_GET['startY']."-".$_GET['startM']."-".$_GET['startD']." ".$start_time;
  $end = $_GET['endY']."-".$_GET['endM']."-".$_GET['endD']." ".$now_time;
  $site_id = (isset($_GET['site_id'])&&$_GET['site_id'])?$_GET['site_id']:0;
  if(isset($_GET['search'])&&$_GET['search']==1){
  $sql_category_asset = " select c.categories_id,cd.categories_name from
    ".TABLE_CATEGORIES." c 
    ,".TABLE_CATEGORIES_DESCRIPTION." cd 
    where parent_id = '".$_GET['product_categories_id']."' 
    and cd.site_id='0' and c.categories_id = cd.categories_id ";
  if(!isset($_GET['sort_order'])||$_GET['site_id']==''){
    $sql_category_asset .= " order by cd.categories_name asc ";
  }
  $query_category_asset = tep_db_query($sql_category_asset);
  $category_asset_arr = array();
  $i=-1000;
  $sort_category_arr = array();
  $all_quantity = 0;
  $all_avg_price = 0;
  $all_asset_price = 0;
  $all_true_row =0;
  while($row_category_asset = tep_db_fetch_array($query_category_asset)){
    $temp_row = tep_get_all_asset_category_by_cid($row_category_asset['categories_id'],
        $bflag,$site_id,$start,$end);
    $temp_row['categories_name'] = $row_category_asset['categories_name'];
    $i++;
    if($temp_row['quantity_all_product']!=0){
      $all_true_row ++;
      $all_quantity += $temp_row['quantity_all_product'];
    }
    $all_avg_price += $temp_row['avg_price'];
    $all_asset_price += $temp_row['asset_all_product'];
    if(isset($_GET['sort_order'])&&$_GET['sort_order']!=''){
      if($temp_row['asset_all_product'] == 0){
        $sort_category_arr[$row_category_asset['categories_id']]=$i;
      }else{
        $sort_category_arr[$row_category_asset['categories_id']]=
          $temp_row['asset_all_product'];
      }
    }else{
      $sort_category_arr[$row_category_asset['categories_id']]=$i;
    }
    $category_asset_arr[$row_category_asset['categories_id']] = $temp_row;
  }
  if(isset($_GET['sort_order'])){
    if($_GET['sort_order']=='price_asc'){
      asort($sort_category_arr);
    }else if($_GET['sort_order']=='price_desc'){
      arsort($sort_category_arr);
    }else{
      asort($sort_category_arr);
    }
  }
  $products = tep_get_product_by_category_id($_GET['product_categories_id']
      ,$bflag,$site_id);
  $i=-1000;
  $all_product = array();
  $sort_product_arr = array();
  foreach($products as $product){
    $i++;
    $tmp_arr = array();
    $tmp_arr = tep_get_all_asset_product_by_pid($product['products_id'],
        $bflag,$site_id,$start,$end);
    $tmp_arr['products_name'] = $product['products_name'];
    $tmp_arr['products_real_quantity'] = $product['products_real_quantity'];
    if($tmp_arr['quantity_all_product']!=0){
      $all_true_row ++;
      $all_quantity += $tmp_arr['quantity_all_product'];
    }
    $all_avg_price += $tmp_arr['price'];
    $all_asset_price += $tmp_arr['asset_all_product'];

    if($product['relate_id']==0){
      $tmp_arr['relate_date'] = tep_get_relate_date($product['products_id'],$site_id,$start,$end);
    }else{
      $tmp_arr['relate_date'] = $i; 
    }
    if(isset($_GET['sort_order'])&&$_GET['sort_order']==''){
      $sort_product_arr[$product['products_id']] =
        $tmp_arr['relate_date'];
    }else{
      $sort_product_arr[$product['products_id']] =
        $tmp_arr['asset_all_product'];
    }
    $all_product[$product['products_id']] = $tmp_arr;
  }
  if(isset($_GET['sort_order'])){
    if($_GET['sort_order']=='price_asc'){
      asort($sort_product_arr);
    }else if($_GET['sort_order']=='price_desc'){
      arsort($sort_product_arr);
    }else{
      arsort($sort_product_arr);
    }
  }
  if(isset($_GET['show_status'])&&$_GET['show_status']=='info'){

    if(count($category_asset_arr)==0&&count($products)==0){
      echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
    }else{
      ?>
        <div class="assets_print_bottom">
        <table cellpadding="0" cellspacing="1" border="0" width="99%" class="assets_box">
        <tr class="assets_text">
        <?php
        if(count($products)>0){
          ?>
            <td align="center"><b><?php echo TEXT_PRODUCTS_DATE;?></b></td>
            <?php
        }
      ?>
        <td align="center"><b><?php echo TEXT_PRODUCTS_NAME;?></b></td>
        <td align="center" width="18%"><b><?php echo TEXT_PRODUCTS_QUANTITY;?></b></td>
        <td id='product_price' align="center" width="18%"><b><?php 
        echo TEXT_PRODUCTS_PRICE;?></b></td>
        <td id='product_price_sum' align="center" width="18%"><b><?php 
        echo TEXT_PRODUCTS_PRICE_SUM;?></b></td>
        </tr>
        <?php 
        foreach($sort_category_arr as $key => $v){
          echo "<tr class='assets_c'>";
          if(count($products)!=0){
            echo "<td>";
            echo "</td>";
          }
          echo "<td>";
          echo $category_asset_arr[$key]['categories_name'];
          echo "</td>";
          echo "<td align='right'>";
          echo $category_asset_arr[$key]['quantity_all_product'].TEXT_ROW;
          echo "</td>";
          echo "<td align='right'>";
          echo $currencies->format($category_asset_arr[$key]['avg_price']);
          echo "</td>";
          echo "<td align='right'>";
          echo $currencies->format($category_asset_arr[$key]['asset_all_product']);
          echo "</td>";
          echo "</tr>";
        }
      foreach($sort_product_arr as $k => $v){
        echo "<tr class='assets_c'>";
        echo "<td>";
        if($all_product[$k]['relate_date']>0){
          echo $all_product[$k]['relate_date'];
        }
        echo "</td>";
        echo "<td>";
        echo $all_product[$k]['products_name'];
        echo "</td>";
        echo "<td align='right'>";
        echo $all_product[$k]['products_real_quantity'].TEXT_ROW;
        echo "</td>";
        echo "<td align='right'>";
        echo $currencies->format($all_product[$k]['price']);
        echo "</td>";
        echo "<td align='right'>";
        echo $currencies->format($all_product[$k]['asset_all_product']);
        echo "</td>";
        echo "</tr>";


      }
    }
  }else{
    if(count($category_asset_arr)==0&&count($products)==0){
    echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
    }
  }
  ?>
  <?php
    //$all_product_info = tep_get_all_asset($start,$end);
    $all_product_info =
      tep_get_all_asset_category_by_cid($_GET['product_categories_id'],
          $bflag,$site_id,$start,$end);
    if(isset($_GET['show_status'])&&$_GET['show_status']=='info'
        &&(count($category_asset_arr)!=0||count($products)!=0)){
      echo "<tr class='assets_c'>";
      echo "<td rowspan='3' ";
      if(count($products)>0){
        echo "colspan='3' ";
      }else{
        echo "colspan='2' ";
      }
      echo "></td>";
      echo "<td id='info_title_td' class='assets_bottom_info_left'>";
      echo TEXT_SUM_PRODUCT;
      echo "</td>";
      echo "<td id='info_value_td' class='assets_bottom_info'>";
      echo $all_quantity.TEXT_ROW;
      echo "</td>";
      echo "</tr>";
      echo "<tr class='assets_c'>";
      echo "<td class='assets_bottom_info_left'>";
      echo TEXT_AVG_PRICE;
      echo "</td>";
      echo "<td class='assets_bottom_info'>";
      echo $currencies->format(@($all_avg_price/$all_true_row));
      echo "</td>";
      echo "<tr class='assets_c'>";
      echo "<td class='assets_bottom_info_left'>";
      echo TEXT_SUM_PRICE;
      echo "</td>";
      echo "<td class='assets_bottom_info'>";
      echo $currencies->format($all_asset_price);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
    }else{
      if(count($category_asset_arr)!=0||count($products)!=0){
      echo "<table cellpadding='0' cellspacing='1' border='0' width='99%' class='asset_easy'>";
      echo "<tr class='assets_c'>";
      echo "<td class='asstes_easy_left'>";
      echo TEXT_SUM_PRODUCT;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo $all_quantity.TEXT_ROW;
      echo "</td>";
      echo "</tr>";
      echo "<tr class='assets_c'>";
      echo "<td  class='asstes_easy_left'>";
      echo TEXT_AVG_PRICE;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo $currencies->format(@($all_avg_price/$all_true_row));
      echo "</td>";
      echo "<tr class='assets_c'>";
      echo "<td  class='asstes_easy_left'>";
      echo TEXT_SUM_PRICE;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo $currencies->format($all_asset_price);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      }
    }
  }
}
?>
</div>
</td></tr></table>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>