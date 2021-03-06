<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  define('MAX_DISPLAY_PRODUCTS',40);
  if(isset($_GET['action'])&&$_GET['action']){
     switch ($_GET['action']){
/* -----------------------------------------------------
   case 'refresh' 更新商品的在库状态   
------------------------------------------------------*/
       case 'refresh':
         //更新
         $all_products_sql = "select products_id,products_real_quantity as products_quantity from ".
           TABLE_PRODUCTS;
         $all_products_res = tep_db_query($all_products_sql);
         while($all_products_row = tep_db_fetch_array($all_products_res)){
           $all_products_row['products_quantity'] = tep_get_quantity($all_products_row['products_id']);
           $products_id = $all_products_row['products_id'];
           $products_quantity = $all_products_row['products_quantity'];
           $inventory_arr = tep_get_inventory($products_id);
           //判断 是否 属于范围 
           $status = "ok";
           if(!$inventory_arr['max']&&!$inventory_arr['min']){
             $status = "ok";
           }else{
             if($products_quantity < $inventory_arr['min']){
               $status = "down";
             }else if($products_quantity > $inventory_arr['max']){
               if($inventory_arr['max']){
               $status = "up";
               }else{
               $status = "ok";
               }
             }else if($products_quantity > $inventory_arr['min']&&
                 $products_quantity < $inventory_arr['max']){
               $status = "ok";
             }else{
               $status = "error";
             }
           }
           //更新数据
           tep_upload_products_to_inventory($products_id,$status);
         }
         break;
     }
     tep_redirect(tep_href_link(FILENAME_INVENTORY));
  
  }
  //查找 最后修改时间
  $inventory_sql = 'select max(last_date) as `update` from '.
  TABLE_PRODUCTS_TO_INVENTORY ;
  $inventory_res = tep_db_query($inventory_sql);
  if($inventory_date = tep_db_fetch_array($inventory_res)){
     $show_date = $inventory_date['update'];
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo INVENTORY_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
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
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text //-->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
<div class="compatible">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
?>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo INVENTORY_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <div>
        <?php
        //此处 添加时间 和 全部更新(Refresh)按钮
        if(isset($show_date)&&$show_date){
          echo $show_date;
        }else{
          echo NO_REFRESH;
        }
        echo "&nbsp;&nbsp;&nbsp;";
        echo "<a class='inv_refresh' href='". tep_href_link(FILENAME_INVENTORY,"action=refresh")
        ."'>".REFRESH."</a>";
        ?>
        </div>
        <table class="inventory_table" border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" height="24"><?php echo TEXT_PRODUCT_NAME;?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TEXT_PRODUCT_PRICE;?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TEXT_IMAGINARY;?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TEXT_PRODUCT_QUANTITY;?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TEXT_MAX_INVENTORY;?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TEXT_MIN_INVENTORY;?></td>
        </tr>
        <?php
         $products_query_rows = "select
           pd.products_name,
           p.products_real_quantity as products_quantity,
           p.products_virtual_quantity,
           p.products_price,
           p.products_bflag,
           p2i.products_id,
           p2i.inventory_status,
           p.relate_products_id
           from ".TABLE_PRODUCTS_TO_INVENTORY." p2i 
           left join ".TABLE_PRODUCTS." p 
           on p.products_id=p2i.products_id left join 
           ".TABLE_PRODUCTS_DESCRIPTION." pd on
           pd.products_id = p.products_id 
           where p2i.inventory_status not in ('ok','up','error') 
           and pd.site_id = '0' 
           order by p2i.cpath ASC";
         $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS,$products_query_rows,$products_query_numrows);
         $products_query = tep_db_query($products_query_rows);
         while($products = tep_db_fetch_array($products_query)){
           $products['products_quantity'] = tep_get_quantity($products['products_id']);
           if($products['products_bflag']){
             echo "<tr class='dataTableSecondRow inv_second'
               onmouseover='this.className=\"dataTableRowOver\";this.style.cursor=\"hand\"'
               onmouseout='this.className=\"dataTableSecondRow inv_second\"'>";
           }else{
             //买取
             echo "<tr class='dataTableRow' 
               onmouseover='this.className=\"dataTableRowOver\";this.style.cursor=\"hand\"'
               onmouseout='this.className=\"dataTableRow\"'>";
           }
         ?>
          <td style="border-bottom:1px solid #000000" height="24">
          <?php $inv = tep_get_inventory($products['products_id']); ?>
          <?php
          krsort($inv['cpath']);
          $link_cpath = implode('_',$inv['cpath']);
          $link_product_id = $products['products_id'];
          ?>
          <a
          href="categories.php?cPath=<?php echo $link_cpath;?>&pID=<?php
          echo $link_product_id;?>">
          <?php echo $products['products_name'];?></a>&nbsp;&nbsp;
          </td>
          <?php
  //架空
  $res_kaku=tep_db_query("select * from set_menu_list where
      products_id='".$products['products_id']."' ORDER BY set_list_id ASC");
  $i_cnt=0;
  while($col_kaku=tep_db_fetch_array($res_kaku)){
    $menu_datas[$i_cnt][0]=$col_kaku['products_id'];
    $menu_datas[$i_cnt][1]=tep_get_kakuukosuu_by_products_id($col_kaku['products_id']);
    $menu_datas[$i_cnt][2]=$col_kaku['kakaku'];
    $i_cnt++;
  }
  $imaginary=0;
  if(isset($products['products_virtual_quantity'])
    &&$products['products_virtual_quantity']!=0){
    $imaginary = $products['products_virtual_quantity'];
  }
  for($i=0;$i<$i_cnt;$i++){
    if($products['products_id']==$menu_datas[$i][0]){
      $imaginary     = $menu_datas[$i][1];
      $kakaku_treder = $menu_datas[$i][2];
      break;
    }else{
      $imaginary=0;
      $kakaku_treder=0;
    }
  }
          ?>
          <td style="border-bottom:1px solid #000000" align="right"><?php 
          $price = explode('.', $products['products_price']);
          echo $price[0];
          ;?>&nbsp;<?php echo TEXT_MONEY_SYMBOL;?></td>
          <td style="border-bottom:1px solid #000000" align="right"><?php echo $imaginary;?>&nbsp;<?php echo MONTHS;?></td>
          <td style="border-bottom:1px solid #000000" align="right"><?php echo
          $products['products_quantity'];?>&nbsp;<?php echo MONTHS;?>
          <?php
          if($products['inventory_status']=='up'){
            echo "<img src='images/icons/up.gif'>";
          }else if($products['inventory_status']=='down'){
            echo "<img src='images/icons/down01.gif'>";
          }
          ?>
          </td>
          <td style="border-bottom:1px solid #000000" align="right"><?php echo
          $inv['max']?$inv['max']:0;?>&nbsp;<?php echo MONTHS;?></td>
          <td style="border-bottom:1px solid #000000" align="right"><?php echo
          $inv['min']?$inv['min']:0;?>&nbsp;<?php echo MONTHS;?></td>
        </tr>
        <?php
         }
        ?>
        </table></td>
      </tr>
      <tr>
      <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="5">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo
            $products_split->display_count($products_query_numrows,
                MAX_DISPLAY_PRODUCTS, $_GET['page'],
                TEXT_DISPLAY_NUMBER_OF_NIVENTORY); ?></td>
            <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
      </tr>
<?php
?>
    </table>
    </div>
    </div>
    </td>
<!-- body_text_eof //-->
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
<?php



