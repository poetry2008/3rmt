<?php 

require('includes/application_top.php');

function show_effective_number($str,$str_end=TEXT_MONEY_SYMBOL,$count=2){ 
  if("$str" == 0){
    return TEXT_UNSET_DATA;
  }
  if($str<1){
    $str = $str+1;
    $arr = str_split($str);
    $add_flag = false;
    $i=0;
    foreach($arr as $value){
      if($add_flag){
        if($value!=0){
          break;
        }
        $i++;
      }
      if($value=='.'){
        $add_flag = true;
      }
    }
    $i = $i+$count;
    for($j=$count;$j>0;$j--){
      if(substr($str,$i+1,1)==0){
        $i--;
      }else{
        break;
      }
    }
    return '0.'.substr($str,2,$i).$str_end;
  }
  $arr = explode('.',$str);
  if(count($arr)==1){
    return $str.$str_end;
  }else{
    $arr_end = str_split($arr[1]);
    $index=0;
    foreach($arr_end as $end){
      if($end!=0){
        $index+=2;
        break;
      }
      $index++;
    }
    if($index>$count+1){
      return $arr[0].$str_end;
    }
    for($j=$index;$j>0;$j--){
      if(substr($arr[1],$index-1,1)==0){
        $index--;
      }else{
        break;
      }
    }
    return $arr[0].'.'.substr($arr[1],0,$index).$str_end;
  }
}

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
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
<link media="print" rel="stylesheet" type="text/css" href="includes/print_assets.css?v=<?php echo $back_rand_info?>">
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<title><?php echo ASSETS_TITLE;?></title>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
    </script>
    <?php }?>
    <div class='header'>
    <?php
    require(DIR_WS_INCLUDES . 'header.php');
    ?>
    </div>
    <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
    <tr>
    <?php
      echo '<td width="' . BOX_WIDTH . '" valign="top">';
      echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
      require(DIR_WS_INCLUDES . 'column_left.php');
      echo '</table>';
      echo '</td>';
    ?>
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible">
<?php
if(isset($_GET['pid'])&&$_GET['pid']!=''){
  echo "<div class = 'title_breadcreumb_div'>";
}
?>
<div class="assets_title">
<?php echo ASSETS_TITLE;?>
</div>
<?php
set_time_limit(0);
//简易页面
if(isset($_GET['pid'])&&$_GET['pid']!=''){
  ?>
<div class="breadcreumb_asset">
<?php 
  echo TEXT_TOP;
  echo '&nbsp;&gt;&gt;&nbsp;';
  echo tep_output_generated_category_path_asset($_GET['pid'], 'product');
  echo '&nbsp;&gt;&gt;&nbsp;';
  echo  tep_get_products_name($_GET['pid'],0,$_GET['site_id']);
?>
</div>
</div>
<div class="assets_top_text_product">
<?php echo TEXT_ASSETS_INFO;?>
</div>
<?php
  //product history info
  $now_time = date('H:i:s');
  $start = $_GET['startY']."-".$_GET['startM']."-".$_GET['startD']." ".$start_time;
  $end = $_GET['endY']."-".$_GET['endM']."-".$_GET['endD']." ".$now_time;
  $site_id = (isset($_GET['site_id'])&&$_GET['site_id'])?$_GET['site_id']:0;
  $product_history_sql =
    tep_get_order_history_sql_by_pid($_GET['pid'],$start,$end,$_GET['sort_order']);
  $product_history_query = tep_db_query($product_history_sql);
  ?>
  <div class='product_history_info'>
    <input type="button" class="assets_input" value="<?php echo
    TEXT_ASSETS_PRINT;?>" onclick="window.open('<?php echo 
        tep_href_link(FILENAME_PRINT_ASSETS,tep_get_all_get_params());?>');">
  </div>
  <?php
  if(tep_db_num_rows($product_history_query)){
    ?>
      <table width="99%" cellpadding="0" cellspacing="1" border="0" class="assets_table">
      <tr class="assets_text">
      <td align="center" style="height:20px;"><?php echo TEXT_PRODUCTS_DATE;?></td>
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
          <td align="right"><?php echo $currencies->format($row['final_price'])=='0'.TEXT_MONEY_SYMBOL?show_effective_number($row['final_price']):$currencies->format($row['final_price']);?></td>
          </tr>
          <?php
      }
    ?>
      </table>
      <?php
  }else{
    echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
  }
  echo "<div class='product_history_back'>";
  echo "<a href='".
    tep_href_link(FILENAME_ASSETS,tep_get_all_get_params(array('pid')))."'>";
  echo tep_html_element_button(IMAGE_BACK); 
  echo "</a>";
  echo "</div>";
}else{
  ?>
<div class="assets_top_text">
<?php echo TEXT_ASSETS_INFO;?>
</div>
<?php
  echo tep_draw_form('new_product', FILENAME_ASSETS,tep_get_all_get_params(
        array('site_id','bflag','product_categories_id')),'get');
  echo '<div class="assets_search_bar">';
  echo "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";
  echo "<tr>";
  echo "<td>";
  echo TEXT_SEARCH_SITE;
  echo "<br>";
  echo tep_site_pull_down_menu_with_all($_GET['site_id'], false, TEXT_SHOW_ALL);
  echo "</td>";
  echo "<td>";
  echo TEXT_SEARCH_WHERE;
  echo "<br>";
  ?>
    <select name="show_status">
    <option value="easy" <?php
    if(!isset($_GET['show_status'])||$_GET['show_status']=='easy'){?>
      selected<?php }?>><?php echo TEXT_STATUS_EASY;?>
        </option>
        <option value="info" <?php
        if(isset($_GET['show_status'])&&$_GET['show_status']=='info'){?>
          selected<?php }?>><?php echo TEXT_STATUS_INFO;?>
            </option>
            </select>

<?php
  echo "</td>";
  echo "<td>";
  echo TEXT_SEARCH_DATE;
  echo "<br>";
  ?>
    <?php //start date ?>
    <table cellpadding="0" cellspacing="0" border="0" class="assets_time">
    <tr>
    <td nowrap >
    <?php echo TEXT_SEARCH_DATE_START."&nbsp;&nbsp";?>
    </td><td>
    <select name="startY" size="1">
    <?php
    if ($startDate) {
      $y = date("Y") - date("Y", $startDate- 60* 60 * 24);
    } else {
      $y = 0;
    }
  for ($i = 10; $i >= 0; $i--) {
    ?>
      <option<?php if ($y == $i) echo " selected"; ?>><?php echo date("Y") - $i; ?></option>
      <?php
  }
  ?>
    </select></td>
    <td><select name="startM" size="1">
    <?php
    if ($startDate) {
      $m = date("n", $startDate- 60* 60 * 24);
    } else {
      $m = date("n");
    }
  for ($i = 1; $i < 13; $i++) {
    ?>
      <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
      <?php
  }
  ?>
    </select></td>
    <td><select name="startD" size="1">
    <?php
    if ($startDate) {
      $j = date("j", $startDate- 60* 60 * 24);
    } else {
      $j = 1;
    }
  for ($i = 1; $i < 32; $i++) {
    ?>
      <option<?php if ($j == $i) echo " selected"; ?>><?php echo $i; ?></option>
      <?php
  }
  ?>
    </select></td>
    </tr>
    </table>
    </div>
    </td>
    <td valign="bottom">
    <input type="hidden" name="search" value="1">
    <?php
    echo tep_html_element_submit(TEXT_SEARCH,'','assets_submit');
  ?>
    </td>
    </tr>
    <tr>
    <td>
    <?php echo TEXT_SEARCH_CATEGORY;?>
    <br>
    <?php
    echo tep_draw_pull_down_menu('product_categories_id',tep_get_category_tree(),
        $current_category_id);
  ?>
    </td>
    <td>
    <?php echo TEXT_SEARCH_ORDER;?><br>
    <select name="sort_order">
    <option value="" <?php
    if(!isset($_GET['sort_order'])||$_GET['sort_order']==''){?>
      selected<?php }?>><?php echo TEXT_SORT_DATE;?>
        </option>
        <option value="price_desc" <?php
        if(isset($_GET['sort_order'])&&$_GET['sort_order']=='price_desc'){?>
          selected<?php }?>><?php echo TEXT_SORT_PRICE_ASC;?>
            </option>
            <option value="price_asc" <?php
            if(isset($_GET['sort_order'])&&$_GET['sort_order']=='price_asc'){?>
              selected<?php }?>><?php echo TEXT_SORT_PRICE_DESC;?>
                </option>
                </select>
                </td>
    <td><br>
    <table cellpadding="0" cellspacing="0" border="0" class="assets_time"><tr><td
    nowrap>
    <?php echo TEXT_SEARCH_DATE_END."&nbsp;&nbsp";?>
    </td><td>
    <select name="endY" size="1">
    <?php
    if ($endDate) {
      $y = date("Y") - date("Y", $endDate - 60* 60 * 24);
    } else {
      $y = 0;
    }
  for ($i = 10; $i >= 0; $i--) {
    ?>
      <option<?php if ($y == $i) echo " selected"; ?>>
      <?php echo date("Y") - $i; ?></option>
      <?php
  }
  ?>
    </select>
    </td>
    <td><select name="endM" size="1">
    <?php
    if ($endDate) {
      $m = date("n", $endDate - 60* 60 * 24);
    } else {
      $m = date("n");
    }
  for ($i = 1; $i < 13; $i++) {
    ?>
      <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"> <?php echo $i; ?></option>
      <?php
  }
  ?>
    </select></td>
    <td><select name="endD" size="1">
    <?php
    if ($endDate) {
      $j = date("j", $endDate - 60* 60 * 24);
    } else {
      $j = date("j");
    }
  for ($i = 1; $i < 32; $i++) {
    ?>
      <option<?php if ($j == $i) echo " selected"; ?>>
      <?php echo $i; ?></option>
      <?php
  }
  ?>
    </select></td>
    </tr>
    </table>
    </td>
    <td valign="bottom">
    <?php ?>
    <input type="button" class="assets_input" value="<?php echo
    TEXT_ASSETS_PRINT;?>" onclick="window.open('<?php echo 
        tep_href_link(FILENAME_PRINT_ASSETS,tep_get_all_get_params());?>');">
    </td>
    </tr>
    </table>
                <?php
                echo '</div>';
  echo "</form>";
  $now_time = date('H:i:s');
  $start = $_GET['startY']."-".$_GET['startM']."-".$_GET['startD']." ".$start_time;
  $end = $_GET['endY']."-".$_GET['endM']."-".$_GET['endD']." ".$now_time;
  $site_id = (isset($_GET['site_id'])&&$_GET['site_id'])?$_GET['site_id']:0;
  $sort = (isset($_GET['sort_order'])&&$_GET['sort_order'])?$_GET['sort_order']:'';
  if(isset($_GET['search'])&&$_GET['search']==1){
  //检索信息列表 
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
  $all_true_row =0;
  $products_price_sum = 0;
  $products_quantity_sum = 0;
  while($row_category_asset = tep_db_fetch_array($query_category_asset)){
    $temp_row = tep_get_all_asset_category_by_cid($row_category_asset['categories_id'],
        $bflag,$site_id,$start,$end,$sort);
    $temp_row['categories_name'] = $row_category_asset['categories_name'];
    $i++;
    if($temp_row['quantity_all_product']!=0){
      $all_true_row ++;
      $temp_quantity_all_product = $temp_row['quantity_all_product'];
      $all_quantity += "$temp_quantity_all_product";
      $products_quantity_num = 0;
      $products_price_total = 0;
      foreach($temp_row['products_info'] as $info_value){
        if($info_value['orders_id']!=''){
         $products_quantity_value = $info_value['products_quantity'];
         $products_quantity_num += "$products_quantity_value";
         if($products_quantity_num > $temp_row['real_all_product']){

            $products_quantity_value = "$products_quantity_value" - ("$products_quantity_num" - "$temp_quantity_all_product");
         }
         $products_quantity_sum += "$products_quantity_value"; 
         $abs_price = abs($products_quantity_value*$info_value['final_price']);
         $products_price_total += "$abs_price";
        }
      }
      $products_price_sum += "$products_price_total";
    }
    if(isset($_GET['sort_order'])&&$_GET['sort_order']!=''&&false){
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
  //设置默认排序
  asort($sort_category_arr);
  $products = tep_get_product_by_category_id($_GET['product_categories_id']
      ,$bflag,$site_id);
  $i=-1000;
  $all_product = array();
  $sort_product_arr = array();
  foreach($products as $product){
    $i++;
    $tmp_arr = array();
    $tmp_arr = tep_get_all_asset_product_by_pid($product['products_id'],
        $bflag,$site_id,$start,$end,$sort);
    $tmp_arr['products_name'] = $product['products_name'];
    $tmp_arr['products_real_quantity'] = tep_get_quantity($product['products_id']);
    if($tmp_arr['quantity_all_product'] != 0){
      $all_true_row ++;
      $temp_quantity_all_product = $tmp_arr['quantity_all_product'];
      $all_quantity += "$temp_quantity_all_product";
      $products_quantity_num = 0;
      $products_price_total = 0;
      foreach($tmp_arr['products_info'] as $info_value){

        if($products_quantity_num >= $tmp_arr['quantity_all_product']){

          break;
        }
        $products_quantity_value = $info_value['products_quantity'];
        $products_quantity_num += "$products_quantity_value";
        if($products_quantity_num > $tmp_arr['quantity_all_product']){

          $products_quantity_value = "$products_quantity_value" - ("$products_quantity_num" - "$temp_quantity_all_product");
        }
        $abs_price =  abs($products_quantity_value*$info_value['final_price']);
        $products_price_total += "$abs_price";
        $products_quantity_sum += "$products_quantity_value";
      }
      $products_price_sum += "$products_price_total";
    }

    if($product['relate_id']==0){
      $tmp_arr['relate_date'] = tep_get_relate_date($product['products_id'],$site_id,$start,$end);
    }else{
      $tmp_arr['relate_date'] = $i; 
    }
    if(isset($_GET['sort_order'])&&$_GET['sort_order']==''||true){
      $sort_product_arr[$product['products_id']] =
        $tmp_arr['relate_date'];
    }else{
      $sort_product_arr[$product['products_id']] =
        $tmp_arr['asset_all_product'];
    }
    $all_product[$product['products_id']] = $tmp_arr;
  }
  //默认排序
  arsort($sort_product_arr);
  if(isset($_GET['show_status'])&&$_GET['show_status']=='info'){
    //详细显示
    if(count($category_asset_arr)==0&&count($products)==0){
      echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
    }else{
      ?>
        <table cellpadding="0" cellspacing="1" border="0" width="99%" class="assets_table">
        <tr class="assets_text">
        <?php
        if(count($products)>0){
          ?>
            <td align="center" style="height:20px;"><b><?php echo TEXT_PRODUCTS_DATE;?></b></td>
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
        //商品总价
        foreach($sort_category_arr as $key => $v){
          if($category_asset_arr[$key]['quantity_all_product'] == 0){

            continue;
          }
          $products_quantity_num = 0;
          $products_info_str = array();
          $products_price_total = 0;
          $products_quantity_total = 0;
          $products_info_arr = array();
          $products_show_info = array();
          $products_show_num = array();
          $products_type_sum_arr = array();
          $parent_show_num = $category_asset_arr[$key]['quantity_all_product'];
          $temp_parent_show_num = $category_asset_arr[$key]['quantity_all_product'];
          $temp_show_sum = 0;
          foreach($category_asset_arr[$key]['products_info'] as $info_value){

            if($temp_show_sum >= $temp_parent_show_num){

              break;
            }
            if(!in_array($info_value['products_id'],$products_info_arr)){
              $products_info_arr[] = $info_value['products_id'];
              $products_type_arr[] = $info_value['products_quantity'];
              $products_show_info[$info_value['products_id']] = array();
              $products_info_str[$info_value['products_id']] = array();
            }
            $info_str_temp = '';
            if($info_value['orders_id']==''){
              $info_str_temp .= '<tr class="assets_error">';
            }else{
              $info_str_temp .= '<tr class="assets_c">';
            }
            if(count($products) > 0){
              $info_str_temp .= '<td>&nbsp;</td>';
            }
            if($info_value['orders_id']!=''){
              $temp_products_quantity = $info_value['products_quantity'];
              $products_quantity_num += "$temp_products_quantity";
            $info_str_temp .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;<a style="color: rgb(0, 0, 255);" target="_black" href="'.FILENAME_ORDERS.'?oID='.$info_value['orders_id'].'&action=edit">'.$info_value['orders_id'].'</a></td>';
            $products_quantity_value = $info_value['products_quantity'];
           if($products_quantity_num > $category_asset_arr[$key]['real_all_product']){

              $real_all_product_category = $category_asset_arr[$key]['real_all_product'];
              $products_quantity_value = "$products_quantity_value" - ("$products_quantity_num" - "$real_all_product_category");
            }
            $info_str_temp .= '<td align="right">'.show_effective_number($products_quantity_value,TEXT_ROW).'</td>';
            $info_str_temp .= '<td align="right">'.($currencies->format(abs($info_value['final_price']))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(abs($info_value['final_price'])):$currencies->format(abs($info_value['final_price']))).'</td>';
            $info_str_temp .= '<td align="right">'.($currencies->format(abs($products_quantity_value*$info_value['final_price']))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(abs($products_quantity_value*$info_value['final_price'])):$currencies->format(abs($products_quantity_value*$info_value['final_price']))).'</td>';
            $info_str_temp .= '</tr>';
            $products_temp_info['price_totle'] = abs($products_quantity_value*$info_value['final_price']);
            $products_temp_info['quantity'] = $products_quantity_value; 
            $products_show_info[$info_value['products_id']][] = $products_temp_info; 
            $products_info_str[$info_value['products_id']][] = $info_str_temp;
            $products_show_num[$info_value['products_id']][] = $products_quantity_value;
            $temp_show_sum  += "$products_quantity_value";
            $abs_price = abs($products_quantity_value*$info_value['final_price']);
            $products_price_total += "$abs_price";
            $products_quantity_total += "$products_quantity_value";
            }
          }
          if($category_asset_arr[$key]['error']){
            echo "<tr class='assets_error'>";
          }else{
            echo "<tr class='assets_c'>";
          }
          if(count($products)!=0){
            echo "<td>";
            echo "</td>";
          }
          echo "<td>";
          echo $category_asset_arr[$key]['categories_name'];
          echo "</td>";
          echo "<td align='right'>";
          echo show_effective_number($category_asset_arr[$key]['quantity_all_product'],TEXT_ROW);
          echo "</td>";
          echo "<td align='right'>";
          echo $currencies->format($products_price_total/$products_quantity_total)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_total/$products_quantity_total):$currencies->format($products_price_total/$products_quantity_total);
          echo "</td>";
          echo "<td align='right'>";
          echo $currencies->format($products_price_total)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_total):$currencies->format($products_price_total);
          echo "</td>";
          echo "</tr>";
          $sub_i = 0;
          foreach($products_info_arr as $p_key => $p_value){
            $p_temp_price_total = 0;
            $p_temp_quantity = 0;
            $show_all_products_num = tep_get_quantity($p_value);
            $temp_show_all_products_num = $show_all_products_num;
            $sub_i += $show_all_products_num;
            $sum_flag = true;;
            $i=0;
            foreach($products_show_num[$p_value] as $sub_num){
              $i+=$sub_num;
              $sum_flag = false;
            }
            $show_all_products_num = "$show_all_products_num"-"$i";
            foreach($products_show_info[$p_value] as $show_info){
              if($show_info['quantity']==0||$show_info['price_totle']==0){
                continue;
              }
              $show_price_total = $show_info['price_totle'];
              $show_quantity = $show_info['quantity'];
              $p_temp_price_total += "$show_price_total";
              $p_temp_quantity += "$show_quantity";
            }
            if($category_asset_arr[$key]['error']||$show_all_products_num==$temp_show_all_products_num){
              echo "<tr class='assets_error'>";
            }else{
              echo "<tr class='assets_c'>";
            }
            if(count($products)!=0){
              echo "<td>";
              echo "</td>";
            }

            echo "<td>";
            echo '&nbsp;&nbsp;'.tep_get_products_name($p_value);
            echo "</td>";
            echo "<td align='right'>";
            echo show_effective_number(tep_get_quantity($p_value),TEXT_ROW);
            echo "</td>";
            echo "<td align='right'>";
            echo $currencies->format($p_temp_price_total/$p_temp_quantity)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($p_temp_price_total/$p_temp_quantity):$currencies->format($p_temp_price_total/$p_temp_quantity);
            echo "</td>";
            echo "<td align='right'>";
            echo $currencies->format($p_temp_price_total)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($p_temp_price_total):$currencies->format($p_temp_price_total);
            echo "</td>";

            echo "</tr>";
            foreach($products_info_str[$p_value] as $show_str){
              echo $show_str;
            }
            if($sum_flag){
              foreach($products_type_arr as $type_sum){
                $show_all_products_num = "$show_all_products_num"-"$type_sum";
              }
            }
            if($show_all_products_num>0&&!$category_asset_arr[$key]['error']&&$show_all_products_num!=$temp_show_all_products_num){
              echo "<tr class='assets_error'>";
              if(count($products)!=0){
                echo "<td>";
                echo "</td>";
              }
              echo "<td>";
              echo '&nbsp;&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA;
              echo "</td>";
              echo "<td align='right'>";
              echo show_effective_number($show_all_products_num,TEXT_ROW);
              echo "</td>";
              echo "<td align='right'>";
              echo TEXT_UNSET_DATA;
              echo "</td>";
              echo "<td align='right'>";
              echo TEXT_UNSET_DATA;
              echo "</td>";
              echo "</tr>";
            }
          }
          $parent_show_num = "$parent_show_num"-"$sub_i";
          if($parent_show_num>0&&!$category_asset_arr[$key]['error']){
            echo "<tr class='assets_error'>";
            if(count($products)!=0){
              echo "<td>";
              echo "</td>";
            }
            echo "<td>";
            echo TEXT_UNSET_DATA;
            echo "</td>";
            echo "<td align='right'>";
            echo show_effective_number($parent_show_num,TEXT_ROW);
            echo "</td>";
            echo "<td align='right'>";
            echo TEXT_UNSET_DATA;
            echo "</td>";
            echo "<td align='right'>";
            echo TEXT_UNSET_DATA;
            echo "</td>";
            echo "</tr>";
          }
        }
      foreach($sort_product_arr as $k => $v){
        if($all_product[$k]['products_real_quantity'] == 0){

          continue;   
        }
        $products_quantity_num = 0;
        $products_info_str = '';
        $products_price_total = 0;
        $products_quantity_total = 0;
        $show_all_products_num = $all_product[$k]['products_real_quantity'];
        $temp_show_all_products_num = $show_all_products_num;
        foreach($all_product[$k]['products_info'] as $info_value){

            if($products_quantity_num >= $all_product[$k]['quantity_all_product']){

              break;
            }
            $temp_products_quantity = $info_value['products_quantity'];
            $products_quantity_num += "$temp_products_quantity"; 
            $products_info_str .= '<tr class="assets_c">';
            if(count($products) > 0){
              $products_info_str .= '<td>&nbsp;</td>';
            }
            $products_info_str .= '<td>&nbsp;&nbsp;<a style="color: rgb(0, 0, 255);" target="_black" href="'.FILENAME_ORDERS.'?oID='.$info_value['orders_id'].'&action=edit">'.$info_value['orders_id'].'</a></td>';
            $products_quantity_value = $info_value['products_quantity'];
            if($products_quantity_num > $all_product[$k]['quantity_all_product']){
              $products_quantity_all = $all_product[$k]['quantity_all_product'];

              $products_quantity_value = "$products_quantity_value"  - ("$products_quantity_num" - "$products_quantity_all");
            }
            $products_info_str .= '<td align="right">'.show_effective_number($products_quantity_value,TEXT_ROW).'</td>';
            $products_info_str .= '<td align="right">'.($currencies->format(abs($info_value['final_price']))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(abs($info_value['final_price'])):$currencies->format(abs($info_value['final_price']))).'</td>';
            $products_info_str .= '<td align="right">'.($currencies->format(abs($products_quantity_value*$info_value['final_price']))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(abs($products_quantity_value*$info_value['final_price'])):$currencies->format(abs($products_quantity_value*$info_value['final_price']))).'</td>';
            $products_info_str .= '</tr>';
            $show_all_products_num = "$show_all_products_num" - "$products_quantity_value";
            $abs_price = abs($products_quantity_value*$info_value['final_price']);
            $products_price_total += "$abs_price";
            $products_quantity_total += "$products_quantity_value";
        }
        if($all_product[$k]['error']||$show_all_products_num==$temp_show_all_products_num){
          echo "<tr class='assets_error'>";
        }else{
          echo "<tr class='assets_c'>";
        }
        echo "<td>";
        if($all_product[$k]['relate_date']>0){
          echo $all_product[$k]['relate_date'];
        }
        echo "</td>";
        echo "<td>";
        echo "<a class='asset_product_link' href='"
          .tep_href_link(FILENAME_ASSETS,tep_get_all_get_params()."&pid=".$k)."'>";
        echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);
        echo "</a>";
        echo $all_product[$k]['products_name'];
        echo "</td>";
        echo "<td align='right'>";
        echo show_effective_number($all_product[$k]['products_real_quantity'],TEXT_ROW);
        echo "</td>";
        echo "<td align='right'>";
        echo $currencies->format($products_price_total/$products_quantity_total)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_total/$products_quantity_total):$currencies->format($products_price_total/$products_quantity_total);
        echo "</td>";
        echo "<td align='right'>";
        echo $currencies->format($products_price_total)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_total):$currencies->format($products_price_total);
        echo "</td>";
        echo "</tr>";
        echo $products_info_str;
        if($show_all_products_num>0&&!$all_product[$k]['error']&&$show_all_products_num!=$temp_show_all_products_num){
              echo "<tr class='assets_error'>";
              echo "<td>";
              echo "</td>";
              echo "<td>";
              echo '&nbsp;&nbsp;'.TEXT_UNSET_DATA;
              echo "</td>";
              echo "<td align='right'>";
              echo show_effective_number($show_all_products_num,TEXT_ROW);
              echo "</td>";
              echo "<td align='right'>";
              echo TEXT_UNSET_DATA;
              echo "</td>";
              echo "<td align='right'>";
              echo TEXT_UNSET_DATA;
              echo "</td>";
              echo "</tr>";
        }

      }
    }
  }else{
    if(count($category_asset_arr)==0&&count($products)==0){
    echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
    }
  }
  ?>
  <?php
    if(isset($_GET['show_status'])&&$_GET['show_status']=='info'
        &&(count($category_asset_arr)!=0||count($products)!=0)){
      //详细显示并且分类数量或者商品数量不能全为空 
      echo "<tr class='assets_c'>";
      echo "<td rowspan='3' ";
      if(count($products)>0){
        echo "colspan='3' ";
      }else{
        echo "colspan='2' ";
      }
      echo " style='border:solid #676767;border-width:0px ;padding:2px;'></td>";
      echo "<td id='info_title_td' class='assets_bottom_info_left' align='right'>";
      echo TEXT_SUM_PRODUCT;
      echo "</td>";
      echo "<td id='info_value_td' class='assets_bottom_info'>";
      echo show_effective_number($all_quantity,TEXT_ROW);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='assets_c'>";
      echo "<td class='assets_bottom_info_left' align='right'>";
      echo TEXT_AVG_PRICE;
      echo "</td>";
      echo "<td class='assets_bottom_info'>";
      echo $currencies->format(@($products_price_sum/$products_quantity_sum))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(@($products_price_sum/$products_quantity_sum)):$currencies->format(@($products_price_sum/$products_quantity_sum));
      echo "</td>";
      echo "<tr class='assets_c'>";
      echo "<td class='assets_bottom_info_left' align='right'>";
      echo TEXT_SUM_PRICE;
      echo "</td>";
      echo "<td class='assets_bottom_info'>";
      echo $currencies->format($products_price_sum)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_sum):$currencies->format($products_price_sum);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
    }else{
      if(count($category_asset_arr)!=0||count($products)!=0){
      //分类数量或者商品数量不能全为空
      echo "<table cellpadding='0' cellspacing='1' border='0' width='99%' class='asset_easy'>";
      echo "<tr class='assets_c'>";
      echo "<td class='asstes_easy_left' align='right'>";
      echo TEXT_SUM_PRODUCT;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo show_effective_number($all_quantity,TEXT_ROW);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='assets_c'>";
      echo "<td  class='asstes_easy_left' align='right'>";
      echo TEXT_AVG_PRICE;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo $currencies->format(@($products_price_sum/$products_quantity_sum))=='0'.TEXT_MONEY_SYMBOL?show_effective_number(@($products_price_sum/$products_quantity_sum)):$currencies->format(@($products_price_sum/$products_quantity_sum));
      echo "</td>";
      echo "<tr class='assets_c'>";
      echo "<td  class='asstes_easy_left' align='right'>";
      echo TEXT_SUM_PRICE;
      echo "</td>";
      echo "<td class='asstes_easy_text'>";
      echo $currencies->format($products_price_sum)=='0'.TEXT_MONEY_SYMBOL?show_effective_number($products_price_sum):$currencies->format($products_price_sum);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      }
    }
  }
}
?>
</div>
</td>
</tr>
</table>
<div id="print_footer">
<?php
require(DIR_WS_INCLUDES . 'footer.php');
?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
