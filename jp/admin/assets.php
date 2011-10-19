<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$startDate = "";
$startDateG = 0;
  if ( isset($_GET['startD']) && ($_GET['startD']) && (tep_not_null($_GET['startD'])) ) 
{    $sDay = $_GET['startD'];
    $startDateG = 1;
  } else {
    $sDay = 1;
  }
  if ( isset($_GET['startM']) && ($_GET['startM']) && (tep_not_null($_GET['startM'])) ) 
{    $sMon = $_GET['startM'];
    $startDateG = 1;
  } else {
    $sMon = 1;
  }
  if ( isset($_GET['startY']) && ($_GET['startY']) && (tep_not_null($_GET['startY'])) ) 
{    $sYear = $_GET['startY'];
    $startDateG = 1;
  } else {
    $sYear = date("Y");
  }
  if ($startDateG) {
    $startDate = mktime(0, 0, 0, $sMon, $sDay+1, $sYear);
  } else {
    $startDate = mktime(0, 0, 0, date("m"), date("d")+1, date("Y")-1);
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
<title><?php echo ASSETS_TITLE;?></title>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    //one_time_pwd('<?php echo $page_name;?>');
    </script>
    <?php }?>
    <div class="assets_title">
    <?php echo ASSETS_TITLE;?>
    </div>
    <?php
    set_time_limit(0);
    //简易页面
    echo tep_draw_form('new_product', FILENAME_ASSETS,tep_get_all_get_params(
          array('site_id','bflag','product_categories_id')),'get');
    echo '<div class="assets_search_bar">';
    echo tep_site_pull_down_menu_with_all($_GET['site_id'], false, TEXT_SHOW_ALL);
    echo '&nbsp;&nbsp;';
    echo tep_draw_pull_down_menu('product_categories_id',tep_get_category_tree(),
        $current_category_id);
?>
&nbsp;&nbsp;
<select name="bflag">
<option value="1"<?php if($_GET['bflag'] == '1'){?> selected<?php }?>><?php echo TEXT_SELL;?>
</option>
<option value="2"<?php if($_GET['bflag'] == '2'){?> selected<?php }?>><?php echo TEXT_BUY;?>
</option>
</select>
<select name="sort_order">
<option value="" <?php
if(!isset($_GET['sort_order'])||$_GET['sort_order']==''){?>
  selected<?php }?>><?php echo TEXT_SORT_DATE;?>
  </option>
  <option value="price_asc" <?php
  if(isset($_GET['sort_order'])&&$_GET['sort_order']=='price_asc'){?>
    selected<?php }?>><?php echo TEXT_SORT_PRICE_ASC;?>
    </option>
    <option value="price_desc" <?php
    if(isset($_GET['sort_order'])&&$_GET['sort_order']=='price_desc'){?>
      selected<?php }?>><?php echo TEXT_SORT_PRICE_DESC;?>
      </option>
      </select>
      &nbsp;&nbsp;
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
          &nbsp;&nbsp;
          <?php //start date ?>
          <table cellpadding="0" cellspacing="0" border="0" class="assets_time">
                <tr>
                  <td><select name="startY" size="1">
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
                    <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?></option>
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
          &nbsp;&nbsp;
          <table cellpadding="0" cellspacing="0" border="0" class="assets_time"><tr><td>
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
    <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>">
    <?php echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?></option>
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


          <?php
          echo tep_html_element_submit(TEXT_SEARCH);
          echo '&nbsp;&nbsp;';
          ?>
          <input type="button" class="assets_input" value="<?php echo TEXT_ASSETS_PRINT;?>" onclick="window.print();">
          <?php
          echo '</div>';
          echo "</form>";
          if(isset($_GET['show_status'])&&$_GET['show_status']=='info'){
            $start = $_GET['startY']."-".$_GET['startM']."-".$_GET['startD'];
            $end = $_GET['endY']."-".$_GET['endM']."-".$_GET['endD'];
            $site_id = (isset($_GET['site_id'])&&$_GET['site_id'])?$_GET['site_id']:0;
            if(isset($_GET['bflag'])&&$_GET['bflag']==2){
              $bflag = 1;
            }else{
              $bflag = 0;
            }
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
            while($row_category_asset = tep_db_fetch_array($query_category_asset)){
              $temp_row = tep_get_all_asset_category_by_cid($row_category_asset['categories_id'],
                  $bflag,$site_id,$start,$end);
              $temp_row['categories_name'] = $row_category_asset['categories_name'];
              $i++;
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
              if($product['relate_id']==0){
                $tmp_arr['relate_date'] = tep_get_relate_date($product['products_id'],$site_id,$start,$end);
              }else{
                $tmp_arr['relate_date'] = $i; 
              }
              if(isset($_GET['sort_order'])&&$_GET['sort_order']){
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

            if(count($category_asset_arr)==0&&count($products)==0){
              echo "<div class='no_result'>".TEXT_NO_RESULT."</div>";
            }else{
              ?>
                <table cellpadding="0" cellspacing="1" border="0" width="99%" class="assets_box">
                <tr class="assets_text">
                <?php
                if(count($products)>0){
                  ?>
                    <td align="center"><b><?php echo TEXT_PRODUCTS_DATE;?></b></td>
                    <?php
                }
              ?>
                <td align="center" ><b><?php echo TEXT_PRODUCTS_NAME;?></b></td>
                <td align="center" width="10%"><b><?php echo TEXT_PRODUCTS_QUANTITY;?></b></td>
                <td align="center"><b><?php echo TEXT_PRODUCTS_PRICE;?></b></td>
                <td align="center"><b><?php echo TEXT_PRODUCTS_PRICE_SUM;?></b></td>
                </tr>
                <?php 
                foreach($sort_category_arr as $key => $v){
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
                  echo $category_asset_arr[$key]['quantity_all_product'];
                  echo "</td>";
                  echo "<td align='right'>";
                  if($category_asset_arr[$key]['quantity_all_product']!=0){
                    echo $currencies->format($category_asset_arr[$key]['asset_all_product']/$category_asset_arr[$key]['quantity_all_product']);
                  }else{
                    echo $currencies->format(0);
                  }
                  echo "</td>";
                  echo "<td align='right'>";
                  echo $currencies->format($category_asset_arr[$key]['asset_all_product']);
                  echo "</td>";
                  echo "</tr>";
                }
              foreach($sort_product_arr as $k => $v){
                if($all_product[$k]['error']){
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
                echo $all_product[$k]['products_name'];
                echo "</td>";
                echo "<td align='right'>";
                echo $all_product[$k]['products_real_quantity'];
                echo "</td>";
                echo "<td align='right'>";
                echo $currencies->format($all_product[$k]['price']);
                echo "</td>";
                echo "<td align='right'>";
                echo $currencies->format($all_product[$k]['asset_all_product']);
                echo "</td>";
                echo "</tr>";


              }
              ?>
                </table>
                <?php
            }
          }
   $all_product_info = tep_get_all_asset($start,$end);
   if(isset($_GET['show_status'])&&$_GET['show_status']=='info'){
     echo "<div class='asset_info'>";
   }else{
     echo "<div class='asset_easy'>";
   }
   echo TEXT_SUM_PRODUCT;
   echo ":&nbsp;";
   echo $all_product_info['all_product_quantity'];
   echo "&nbsp;&nbsp;";
   echo TEXT_AVG_PRICE;
   echo ":&nbsp;";
   echo $currencies->format($all_product_info['all_product_price']/$all_product_info['all_product_quantity']);
   echo "&nbsp;&nbsp;";
   echo TEXT_SUM_PRICE;
   echo ":&nbsp;";
   echo $currencies->format($all_product_info['all_product_price']);
   echo "&nbsp;&nbsp;";
   echo "</div>";
?>
<?php
?>
<br>
<div id="print_footer">
<?php
require(DIR_WS_INCLUDES . 'footer.php');
?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
