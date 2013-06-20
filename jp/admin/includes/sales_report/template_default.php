<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo SR_HEADING_TITLE;?> </title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
<?php //显示相应分类下的商品列表?>
function change_products(id,products_id){
  
  $.ajax({
         type: "POST",
         data: 'id='+id+'&products_id='+products_id,
         async:false,
         url: 'ajax.php?action=products_list',
         success: function(data) {

           $("#products_list").html(data); 
           $("#c_id").val(id);
         }
  });
}

<?php //使商品列表可用或不可用?>
function products_list_show(num){

  if(num == 0){

    $("#categories_id").attr('disabled',true); 
    $("#products_id_list").attr('disabled',true);
  }else{

    $("#categories_id").attr('disabled',false); 
    $("#products_id_list").attr('disabled',false);
  }
}

<?php //保存商品ID?>
function save_products_id(value){

  $("#p_id").val(value);
}

$(document).ready(function() {
<?php
if(isset($_GET['add_product_categories_id']) && $_GET['add_product_categories_id'] != '' && isset($_GET['products_id']) && $_GET['products_id'] != ''){
?>
  change_products(<?php echo $_GET['add_product_categories_id'];?>,<?php echo $_GET['products_id'];?>);
<?php
}else if(isset($_GET['cid']) && $_GET['cid'] != '' && isset($_GET['pid']) && $_GET['pid'] != ''){
?>
  change_products(<?php echo $_GET['cid'];?>,<?php echo $_GET['pid'];?>);
<?php
}
if(isset($_GET['is_select']) && $_GET['is_select'] == '0'){
?>
  products_list_show(0);  
<?php
}
if(isset($_GET['order_sort']) && $_GET['order_sort'] != ''){
  if(isset($_GET['ID']) && $_GET['ID'] != ''){
?>
  $("#i_<?php echo $_GET['ID'];?>").attr('class','dataTableRowSelected');
  $("#i_<?php echo $_GET['ID'];?>").attr('onmouseover',"this.style.cursor='hand';"); 
  $("#i_<?php echo $_GET['ID'];?>").attr('onmouseout',''); 
<?php
  }
  if(isset($_GET['PID']) && $_GET['PID'] != ''){
?>
  $("#p_<?php echo $_GET['PID'];?>").attr('class','dataTableRowSelected');
  $("#p_<?php echo $_GET['PID'];?>").attr('onmouseover',"this.style.cursor='hand';"); 
  $("#p_<?php echo $_GET['PID'];?>").attr('onmouseout','');
<?php
  }
}
?>
});
</script>
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
      <?php
      require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation -->
      <?php
      require(DIR_WS_INCLUDES . 'column_left.php');
?>
      <!-- left_navigation_eof -->
    </table></td>
    <!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
    <div class="compatible"> 
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan=2><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo  SR_HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="2"><form action="" method="get">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="menuBoxHeading"><?php echo SR_REPORT_START_DATE; ?><br>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><select name="startY" size="1" style="margin:0 5px 0 0;">
                    <?php
      if ($startDate) {
        $y = date("Y") - date("Y", $startDate);
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
                  <td><select name="startM" size="1" style="margin:0 5px 0 0;">
                    <?php
      if ($startDate) {
        $m = date("n", $startDate);
      } else {
        $m = 1;
      }
      for ($i = 1; $i < 13; $i++) {
?>
                    <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?></option>
                    <?php
      }
?>
                  </select></td>
                  <td><select name="startD" size="1" style="margin:0 5px 0 0;">
                    <?php
      if ($startDate) {
        $j = date("j", $startDate);
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
              </td>
              <td class="menuBoxHeading">
              <?php echo SR_REPORT_DETAIL; ?><br>
              <select name="detail" size="1" style="margin:0 5px 0 0;">
                <!--<option value="0"<?php if ($srDetail == 0) echo " selected"; ?>><?php echo  SR_DET_HEAD_ONLY; ?></option>-->
                <option value="1"<?php if ($srDetail == 1) echo " selected"; ?>><?php echo  SR_DET_DETAIL; ?></option>
                <option value="2"<?php if ($srDetail == 2) echo " selected"; ?>><?php echo  SR_DET_DETAIL_ONLY; ?></option>
              </select>
              </td>
              <td align="left" class="menuBoxHeading"><?php echo SR_OPTION_TYPE;?><br>
              <select name="report" style="margin-left:0;">
              <option value="1"<?php if ($srView == 1) echo " selected"; ?>><?php echo SR_REPORT_TYPE_YEARLY; ?></option>
              <option value="2"<?php if ($srView == 2) echo " selected"; ?>><?php echo SR_REPORT_TYPE_MONTHLY; ?></option>
              <option value="3"<?php if ($srView == 3) echo " selected"; ?>><?php echo SR_REPORT_TYPE_WEEKLY; ?></option>
              <option value="4"<?php if ($srView == 4) echo " selected"; ?>><?php echo SR_REPORT_TYPE_DAILY; ?></option>
              <option value="5"<?php if ($srView == 5) echo " selected"; ?>><?php echo SR_REPORT_TYPE_ORDERS; ?></option>
              </select> 
              </td>
              <td align="left" class="menuBoxHeading"><?php echo SR_TITLE_FUNCTION;?><br>
              <select name="method" size="1" style="margin-left:0;">
                <option value="0"<?php if ($srMethod == 0) echo " selected"; ?>><?php echo SR_TITLE_DEAL_DAY;?></option>
                <option value="1"<?php if ($srMethod == 1) echo " selected"; ?>><?php echo SR_TITLE_ORDER_DAY;?></option>
              </select>
              </td>
          </tr>

          <tr>
              <td class="menuBoxHeading"><?php echo SR_REPORT_END_DATE; ?><br>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><select name="endY" size="1" style="margin:0 5px 0 0;">
                    <?php
    if ($endDate) {
      $y = date("Y") - date("Y", $endDate - 60* 60 * 24);
    } else {
      $y = 0;
    }
    for ($i = 10; $i >= 0; $i--) {
?>
                    <option<?php if ($y == $i) echo " selected"; ?>><?php echo
date("Y") - $i; ?></option>
                    <?php
    }
?>
                  </select></td>
                  <td><select name="endM" size="1" style="margin:0 5px 0 0;">
                    <?php
    if ($endDate) {
      $m = date("n", $endDate - 60* 60 * 24);
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
                  <td><select name="endD" size="1" style="margin:0 5px 0 0;">
                    <?php
    if ($endDate) {
      $j = date("j", $endDate - 60* 60 * 24);
    } else {
      $j = date("j");
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
              </td>
              <td align="left" class="menuBoxHeading"><?php echo SR_REPORT_STATUS_FILTER; ?><br>
              <select name="status" size="1" style="margin:0 5px 0 0;">
                <option value="success"<?php if ($srStatus == 'success') echo " selected";?>><?php echo SR_TITLE_ORDER_FINISH;?></option>
                <option value="0"<?php if ($srStatus == '0') echo " selected";?>><?php echo SR_REPORT_ALL; ?></option>
                <?php
                        foreach ($sr->status as $value) {
?>
                <option value="<?php echo $value["orders_status_id"]?>"<?php if ($srStatus == $value["orders_status_id"]) echo " selected"; ?>><?php echo $value["orders_status_name"] ; ?></option>
                <?php
                         }
?>
              </select>
              <br>
              </td>
              <td class="menuBoxHeading">
              <?php echo SR_TITLE_CATEGORY;?><br>
              <select name="bflag" style="margin-left:0;">
                <option value="0"<?php if(!$_GET['bflag']){?> selected<?php }?>><?php echo SR_SELECT_ALL;?></option>
                <option value="1"<?php if($_GET['bflag'] == '1'){?> selected<?php }?>><?php echo SR_OPTION_SALE;?></option>
                <option value="2"<?php if($_GET['bflag'] == '2'){?> selected<?php }?>><?php echo SR_OPTION_BUY;?></option>
              </select>&nbsp;
              </td>
             <td align="left" class="menuBoxHeading"><?php echo SR_REPORT_EXP; ?><br>
              <select name="export" size="1" style="margin-left:0;">
                <option value="0" selected><?php echo  SR_EXP_NORMAL; ?></option>
                <option value="1"><?php echo  SR_EXP_CSV; ?></option>
              </select>
              </td> 
            </tr>
            <tr>
              <td valign="top">
              <?php 
                  echo '<div class="box_radio"><span>'.SR_SORT_VAL1.'</span>';
                  $checked = '';
                  if(isset($_GET['is_select'])){

                    if($_GET['is_select'] == '1'){

                      $checked = ' checked="checked"';
                    }
                  }else{
                    $checked = ' checked="checked"'; 
                  }
                ?>
                <?php echo '<input type="radio" name="is_select" value="1"'.$checked.' onclick="products_list_show(1);"><span>'.SR_PRODUCTS_SELECT.'</span>';?>
                <?php echo '<input type="radio" name="is_select" value="0"'.(isset($_GET['is_select']) && $_GET['is_select'] == '0' ? ' checked="checked"' : '').' onclick="products_list_show(0);"><span>'.SR_PRODUCTS_NOT_SELECT;?></span></div>
                <?php echo tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), (isset($_GET['add_product_categories_id']) ? $_GET['add_product_categories_id'] : $_GET['cid']), 'style="margin:0 0 2px 0;" id="categories_id" onChange="change_products(this.value);"');
                echo '<input type="hidden" name="cid" id="c_id" value="'.(isset($_GET['add_product_categories_id']) ? $_GET['add_product_categories_id'] : $_GET['cid']).'"><input type="hidden" name="pid" id="p_id" value="'.(isset($_GET['products_id']) ? $_GET['products_id'] : $_GET['pid']).'">';
                ?><br>
              <span id="products_list"></span> 
              </td>  
              <td align="left" class="menuBoxHeading" valign="top"><?php echo SR_REPORT_SORT; ?><br>
              <select name="sort" size="1" style="margin-left:0;">
                <option value="0"<?php if ($srSort == 0) echo " selected"; ?>><?php echo  SR_SORT_VAL0; ?></option>
                <option value="1"<?php if ($srSort == 1) echo " selected"; ?>><?php echo  SR_SORT_VAL1; ?></option>
                <option value="2"<?php if ($srSort == 2) echo " selected"; ?>><?php echo  SR_SORT_VAL2; ?></option>
                <option value="3"<?php if ($srSort == 3) echo " selected"; ?>><?php echo  SR_SORT_VAL3; ?></option>
                <option value="4"<?php if ($srSort == 4) echo " selected"; ?>><?php echo  SR_SORT_VAL4; ?></option>
                <option value="5"<?php if ($srSort == 5) echo " selected"; ?>><?php echo  SR_SORT_VAL5; ?></option>
                <option value="6"<?php if ($srSort == 6) echo " selected"; ?>><?php echo  SR_SORT_VAL6; ?></option>
              </select>
              <br>
              </td>
              <td align="left" class="menuBoxHeading" valign="top"><?php echo SR_REPORT_COMP_FILTER; ?><br>
              <select name="compare" size="1" style="margin-left:0;">
                <option value="0" <?php if ($srCompare == SR_COMPARE_NO) echo "selected"; ?>><?php echo SR_REPORT_COMP_NO; ?></option>
                <option value="1" <?php if ($srCompare == SR_COMPARE_DAY) echo "selected"; ?>><?php echo SR_REPORT_COMP_DAY; ?></option>
                <option value="2" <?php if ($srCompare == SR_COMPARE_MONTH) echo "selected"; ?>><?php echo SR_REPORT_COMP_MONTH; ?></option>
                <option value="3" <?php if ($srCompare == SR_COMPARE_YEAR) echo "selected"; ?>><?php echo SR_REPORT_COMP_YEAR; ?></option>
              </select>
              <br>
              </td>
              <td align="left" class="menuBoxHeading" valign="top"><?php echo SR_REPORT_MAX; ?><br>
              <select name="max" size="1" style="margin-left:0;"> 
                <option value=""<?php if ($srMax == '') echo " selected"; ?>>0</option>
                <option value="25"<?php if ($srMax == 25) echo " selected"; ?>>25</option>
                <option value="50"<?php if ($srMax == 50) echo " selected"; ?>>50</option>
                <option value="100"<?php if ($srMax == 100) echo " selected"; ?>>100</option>
                <option value="200"<?php if ($srMax == 200) echo " selected"; ?>>200</option>
                <option value="all"<?php if ($srMax == 'all') echo " selected"; ?>><?php echo SR_REPORT_ALL; ?></option>
              </select>
              </td>
              
              </tr>
              <tr>
              <td colspan="4" class="menuBoxHeading" align="right"><input type="submit" value="<?php echo SR_REPORT_SEND; ?>">
              </td>
            </tr>

              </table>
              </td>
             
        </form>
      </tr>
      <tr>
        <td width=100% valign=top>

  <?php if ($_GET or true){ ?>
  
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><?php tep_show_site_filter(FILENAME_STATS_SALES_REPORT,false,array(0));?><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=date&order_type='.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_TABLE_HEADING_DATE.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
<?php
                if(isset($_GET['report']) && $_GET['report'] == 5){
?>
<td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=orders&order_type='.($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_TABLE_HEADING_ORDERS_TITLE.($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
<?php
                }else{
?>
                <td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'sort')).'sort='.($_GET['sort'] == 2 ? 1 : 2)).'">'.SR_SORT_VAL1.(isset($_GET['sort']) && $_GET['sort'] == '2' ? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : (isset($_GET['sort']) && $_GET['sort'] == 1 ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
<?php
                }
                if(isset($_GET['report']) && $_GET['report'] == 5){
?>
                <td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=pname&order_type='.($_GET['order_sort'] == 'pname' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_SORT_VAL1.($_GET['order_sort'] == 'pname' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'pname' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
<?php
                }else{
?>
                <td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=orders&order_type='.($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_TABLE_HEADING_ORDERS.($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'orders' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
<?php
                }
?>
                <td class="dataTableHeadingContent_order" align="left"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=num&order_type='.($_GET['order_sort'] == 'num' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_TABLE_HEADING_ITEMS.($_GET['order_sort'] == 'num' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'num' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
                <td class="dataTableHeadingContent_order" align="right"><?php echo  '<a href="'.tep_href_link(FILENAME_STATS_SALES_REPORT,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=price&order_type='.($_GET['order_sort'] == 'price' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.SR_TABLE_HEADING_REVENUE.($_GET['order_sort'] == 'price' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'price' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'; ?></td>
              </tr>
<?php
if($_GET['report'] != 5){
$sum = 0;
$orders_sum = 0;
$products_point_sum = 0; 
$row_num =0;
$show_list_array = array();
$show_orders_array = array();
$show_products_array = array();
$show_price_array = array();
$show_i = 0;
while ($sr->hasNext()) {
  $info = $sr->next();
  $last = sizeof($info) - 1;
  $orders_num = $info[0]['order'];
  $products_num = isset($info[$last - 1]['totitem'])?$info[$last - 1]['totitem']:'';
  $price_num = isset($info[$last - 1]['totsum'])?$info[$last - 1]['totsum']:'';
  $show_orders_array[$show_i] = $orders_num;
  $show_products_array[$show_i] = $products_num;
  $show_price_array[$show_i] = $price_num;
  
  $even = 'dataTableSecondRow';
  $odd  = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even; 
  } else {
    $nowColor = $odd; 
  }
  if(!isset($_GET['order_sort'])){
    if($_GET['ID'] != '' && $_GET['ID'] == $show_i){
?>
              <tr id="i_<?php echo $show_i;?>" class="dataTableRowSelected" onmouseover="this.style.cursor='hand'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'ID=' . $show_i);?>'">
<?
    }else{
?>
              <tr id="i_<?php echo $show_i;?>" class="<?php echo $nowColor;?>" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='<?php echo $nowColor;?>'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'ID=' . $show_i);?>'">
<?php
    }
  }
    $show_list_array[$show_i] = '<tr id="i_'.$show_i.'" class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\''.tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'ID=' . $show_i).'\'">'; 
    switch ($srView) {
    case '3':
  if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent" align="left"><?php echo tep_date_long(date("Y-m-d\ H:i:s", $sr->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr->showDateEnd)); ?></td>
<?php
  }
  $show_list_array[$show_i] .= '<td class="dataTableContent" align="left">'.tep_date_long(date("Y-m-d\ H:i:s", $sr->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr->showDateEnd)).'</td>'; 
        break;
    case '4':
  if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent" align="left"><?php echo tep_date_long(date("Y-m-d\ H:i:s", $sr->showDate)); ?></td>
<?php
  }
  $show_list_array[$show_i] .= '<td class="dataTableContent" align="left">'.tep_date_long(date("Y-m-d\ H:i:s", $sr->showDate)).'</td>'; 
        break;
    default;
  if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent" align="left"><?php echo tep_date_short(date("Y-m-d\ H:i:s", $sr->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr->showDateEnd)); ?></td>
<?php
  }
  $show_list_array[$show_i] .= '<td class="dataTableContent" align="left">'.tep_date_short(date("Y-m-d\ H:i:s", $sr->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr->showDateEnd)).'</td>'; 
    }
  if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent" align="right">&nbsp;</td><td class="dataTableContent" align="left"><?php echo $info[0]['order']; ?></td>
<?php 
  }
    $show_list_array[$show_i] .= '<td class="dataTableContent" align="right">&nbsp;</td><td class="dataTableContent" align="left">'.$info[0]['order'].'</td>'; 
    $orders_sum += $info[0]['order'];
  if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent" align="left"><?php echo isset($info[$last - 1]['totitem'])?$info[$last - 1]['totitem']:''; ?></td>
<?php 
  }
    $show_list_array[$show_i] .= '<td class="dataTableContent" align="left">'.(isset($info[$last - 1]['totitem'])?$info[$last - 1]['totitem']:'').'</td>'; 
                if(isset($info[$last - 1]['totitem'])){
                  $products_point_sum += $info[$last - 1]['totitem'];
                }
  if(!isset($_GET['order_sort'])){
?> 
  <td class="dataTableContent" align="right"><?php 
  }
    $show_list_array[$show_i] .= '<td class="dataTableContent" align="right">'; 
    if ($info[$last - 1]['totsum'] < 0) {
  if(!isset($_GET['order_sort'])){
    echo '<font color="red">'.
      str_replace(TEXT_MONEY_SYMBOL,'',
          $currencies->format(isset($info[$last - 1]['totsum'])?$info[$last - 1]['totsum']:'')).
          '</font>'.TEXT_MONEY_SYMBOL;
  }
    $show_list_array[$show_i] .= '<font color="red">'.
      str_replace(TEXT_MONEY_SYMBOL,'',
          $currencies->format(isset($info[$last - 1]['totsum'])?$info[$last - 1]['totsum']:'')).
          '</font>'.TEXT_MONEY_SYMBOL; 
    } else {
   if(!isset($_GET['order_sort'])){
    echo str_replace(TEXT_MONEY_SYMBOL,'',
      $currencies->format(isset($info[$last - 1]['totsum'])?$info[$last - 1]['totsum']:''))
      .TEXT_MONEY_SYMBOL;
   }
    $show_list_array[$show_i] .= str_replace(TEXT_MONEY_SYMBOL,'',
      $currencies->format(isset($info[$last - 1]['totsum'])?$info[$last - 1]['totsum']:''))
      .TEXT_MONEY_SYMBOL; 
  }
    $t += $info[$last - 1]['totsum'];
  if(!isset($_GET['order_sort'])){
  ?></td>
              </tr>
<?php
  }
    $show_list_array[$show_i] .= '</td></tr>'; 
if (isset($srDetail)){
  $row_num++;
    for ($i = 0; $i < $last; $i++) {
      if ($srMax === 'all' or $i < $srMax) {
    //生成跳转到商品管理页面的URL
    $products_categories_id = tep_get_products_parent_id($info[$i]['pid']);
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

     if(in_array($info[$i]['pid'],$products_value)){

       $page = $products_key;
       break;
     }
   }
        if(!isset($_GET['order_sort'])){
          if($_GET['PID'] != '' && $_GET['PID'] == $show_i.'_'.$i){
?>
              <tr id="p_<?php echo $show_i.'_'.$i;?>" class="dataTableRowSelected" onmouseover="this.style.cursor='hand'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'PID=' . $show_i.'_'.$i);?>'">  
<?php
          }else{
?>
              <tr id="p_<?php echo $show_i.'_'.$i;?>" class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'PID=' . $show_i.'_'.$i);?>'">
<?php
          } 
?>
                <td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent" align="left"><a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$info[$i]['pid'].'&site_id=0', 'NONSSL'); ?>"><?php echo $info[$i]['pname']; ?></a>
                </td><td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent" align="left"><?php echo $info[$i]['pquant']; ?></td>
<?php
        }
        $show_list_array[$show_i] .= '<tr id="p_'.$show_i.'_'.$i.'" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''.tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID', 'PID')).'PID=' . $show_i.'_'.$i).'\'">
                <td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent" align="left"><a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$info[$i]['pid'].'&site_id=0', 'NONSSL').'">'.$info[$i]['pname'].'</a>
                </td><td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent" align="left">'.$info[$i]['pquant'].'</td>';
        if ($srDetail == 2) {
        if(!isset($_GET['order_sort'])){
?>
  <td class="dataTableContent" align="right"><?php 
        }
        $show_list_array[$show_i] .= '<td class="dataTableContent" align="right">';
        if ($info[$i]['psum'] < 0) {
          if(!isset($_GET['order_sort'])){
                    echo '<font color="red">'.
                      str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info[$i]['psum'])).
                      '</font>'.TEXT_MONEY_SYMBOL; 
          }
                    $show_list_array[$show_i] .= '<font color="red">'.
                      str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info[$i]['psum'])).
                      '</font>'.TEXT_MONEY_SYMBOL;
        } else {
          if(!isset($_GET['order_sort'])){
                    echo str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info[$i]['psum'])) 
                      .TEXT_MONEY_SYMBOL;
          }
                    $show_list_array[$show_i] .= str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info[$i]['psum'])) 
                      .TEXT_MONEY_SYMBOL;
                  }
         if(!isset($_GET['order_sort'])){       ?></td>
<?php
         }
         $show_list_array[$show_i] .= '</td>';
        } else { 
        if(!isset($_GET['order_sort'])){
?>
                <td class="dataTableContent">&nbsp;</td>
<?php
        }
        $show_list_array[$show_i] .= '<td class="dataTableContent">&nbsp;</td>';
        }
        if(!isset($_GET['order_sort'])){
?>
                
              </tr>
<?php
        }
        $show_list_array[$show_i] .= '</tr>';
      }
    }
  }
$show_i++;
}

if($_GET['order_sort'] == 'date'){

  if($_GET['order_type'] == 'desc'){
    krsort($show_list_array);
    foreach($show_list_array as $show_list_value){

      echo $show_list_value;
    }
  }else{
    foreach($show_list_array as $show_list_value){

      echo $show_list_value;
    } 
  }
}else if($_GET['order_sort'] == 'orders'){

  if($_GET['order_type'] == 'desc'){
    arsort($show_orders_array);
    $show_key_orders = array_keys($show_orders_array);
    $k = 0;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      }
      echo $show_list_array[$show_list_value];
      $k++;
    }
  }else{
    asort($show_orders_array);
    $show_key_orders = array_keys($show_orders_array);
    $k = 0;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      }
      echo $show_list_array[$show_list_value];
      $k++;
    } 
  }
}else if($_GET['order_sort'] == 'num'){

  if($_GET['order_type'] == 'desc'){
    arsort($show_products_array);
    $show_key_orders = array_keys($show_products_array);
    $k = 0;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      }  
      echo $show_list_array[$show_list_value];
      $k++;
    }
  }else{
    asort($show_products_array);
    $show_key_orders = array_keys($show_products_array);
    $k = 0;;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      } 
      echo $show_list_array[$show_list_value];
      $k++;
    } 
  }
}else if($_GET['order_sort'] == 'price'){

  if($_GET['order_type'] == 'desc'){
    arsort($show_price_array);
    $show_key_orders = array_keys($show_price_array);
    $k = 0;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      }  
      echo $show_list_array[$show_list_value];
      $k++;
    }
  }else{
    asort($show_price_array);
    $show_key_orders = array_keys($show_price_array);
    $k = 0;
    foreach($show_key_orders as $show_list_value){

      if($k % 2 == 0){

        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableSecondRow"/','<tr id="i_$1" class="dataTableRow"',$show_list_array[$show_list_value]);
      }else{
        $show_list_array[$show_list_value] = preg_replace('/<tr id="i_(.*?)" class="dataTableRow"/','<tr id="i_$1" class="dataTableSecondRow"',$show_list_array[$show_list_value]); 
      }  
      echo $show_list_array[$show_list_value];
      $k++;
    } 
  }
}
}else{
  $info = $sr->next();
  $t = 0;
  $orders_sum = 0;
  $products_point_sum = 0;
  $orders_i = 0;
  $row_num = 0;
  foreach($info as $info_value){

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }  
    if($_GET['ID'] != '' && $_GET['ID'] == $orders_i){
?> 
<tr class="dataTableRowSelected" onmouseover="this.style.cursor='hand'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID')).'ID=' . $orders_i);?>'">
<?php
    }else{
?>
<tr class="<?php echo $nowColor;?>" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='<?php echo $nowColor;?>'" onclick="document.location.href='<?php echo tep_href_link(FILENAME_STATS_SALES_REPORT, tep_get_all_get_params(array('x', 'y', 'ID')).'ID=' . $orders_i);?>'">
<?php
    }
    //生成跳转到商品管理页面的URL
    $products_categories_id = tep_get_products_parent_id($info_value['pid']);
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

     if(in_array($info_value['pid'],$products_value)){

       $page = $products_key;
       break;
     }
   }
?>
      <td class="dataTableContent" align="left"><?php echo tep_date_long(date("Y-m-d\ H:i:s", strtotime($info_value['date_purchased']))); ?></td>
      <td class="dataTableContent" align="left"><?php echo '<a href="'.tep_href_link(FILENAME_ORDERS, 'keywords='.$info_value['orders_id'].'&search_type=orders_id', 'NONSSL').'">'.$info_value['orders_id']; ?></a></td>
      <td class="dataTableContent" align="left"><?php echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$info_value['pid'].'&site_id=0', 'NONSSL').'">'.$info_value['pname'];?></a></td>
      <td class="dataTableContent" align="left"><?php echo $info_value['pquant']; ?></td>
      <td class="dataTableContent" align="right"><?php echo $info_value['psum'] < 0 ? '<font color="red">'.str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info_value['psum'])).'</font>'.TEXT_MONEY_SYMBOL : str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info_value['psum'])).TEXT_MONEY_SYMBOL; ?></td> 
   </tr>
<?php
    $orders_i++;
    $products_point_sum += $info_value['pquant'];
    $t += $info_value['psum'];
  }
  $orders_sum = $orders_i;
  $row_num = $orders_i;
}
?>
<tr>
<td class="dataTableContent" align="right"></td>
<td class="dataTableContent" align="right"></td>
<td class="dataTableContent" align="right"><?php echo
SR_ORDERS_SUM.$orders_sum.SR_ONE_ORDERS;?></td>
<td class="dataTableContent" align="right"><?php echo
SR_PRODUCTS_POINT_SUM.$products_point_sum.SR_POINT;?></td>
<td class="dataTableContent" align="right"><?php echo SR_MONEY_SUM.
($t<0?'<font color="red">':'');?><?php echo str_replace(TEXT_MONEY_SYMBOL,'',
    $currencies->format($t));?><?php echo
($t<0?'</font>':'');
echo TEXT_MONEY_SYMBOL;
?></td></tr>
<tr>
<td class="dataTableContent" align="right"></td>
<td class="dataTableContent" align="right"></td>
<td class="dataTableContent" align="right"><?php 
echo AVG_ORDERS_SUM;
echo str_replace(TEXT_MONEY_SYMBOL,'',$avg_currencies->format($orders_sum/$row_num)) == 1 && $_GET['report'] == 5 ? '-' : str_replace(TEXT_MONEY_SYMBOL,'',$avg_currencies->format($orders_sum/$row_num));
echo SR_ONE_ORDERS;?></td>
<td class="dataTableContent" align="right"><?php 
echo AVG_PRODUCTS_POINT_SUM;
echo str_replace(TEXT_MONEY_SYMBOL,'',$avg_currencies->format($products_point_sum/$row_num));
echo SR_POINT;?></td>
<td class="dataTableContent" align="right"><?php echo AVG_MONEY_SUM.
($t<0?'<font color="red">':'');?><?php 
echo str_replace(TEXT_MONEY_SYMBOL,'',$avg_currencies->format($t/$row_num))
;?><?php echo ($t<0?'</font>':'');
echo TEXT_MONEY_SYMBOL;?></td>
</tr>

<?php
if ($srCompare > SR_COMPARE_NO) {
?>
              <tr>
                <td colspan="7" class="dataTableContent"><?php echo SR_TEXT_COMPARE; ?></td>
              </tr>
<?php
if($_GET['report'] != 5){
  $sum = 0;
  while ($sr2->hasNext()) {
    $info = $sr2->next();
    $last = sizeof($info) - 1;
  ?>
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
                <?php
      switch ($srView) {
        case '3':
  ?>
                <td class="dataTableContent" align="right"><?php echo tep_date_long(date("Y-m-d\ H:i:s", $sr2->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr2->showDateEnd)); ?></td>
                <?php
          break;
        case '4':
  ?>
                <td class="dataTableContent" align="right"><?php echo tep_date_long(date("Y-m-d\ H:i:s", $sr2->showDate)); ?></td>
                <?php
          break;
        default;
  ?>
                <td class="dataTableContent" align="right"><?php echo tep_date_short(date("Y-m-d\ H:i:s", $sr2->showDate)) . " - " . tep_date_short(date("Y-m-d\ H:i:s", $sr2->showDateEnd)); ?></td>
                <?php
      }
  ?>
                <td class="dataTableContent" align="right"><?php echo $info[0]['order']; ?></td>
                <td class="dataTableContent" align="right"><?php 
                if(isset($info[$last - 1]['totitem']) ) 
                echo $info[$last - 1]['totitem']; 
  ?></td>
                <td class="dataTableContent" align="right"><?php 
                if(isset($info[$last - 1]['totsum']) ) {
                  if ($info[$last - 1]['totsum']<0) {
                    echo '<font color="red">'.
                      str_replace(TEXT_MONEY_SYMBOL,'',
                      $currencies->format($info[$last - 1]['totsum'])).
                      '</font>'.TEXT_MONEY_SYMBOL;
                  } else {
                    echo str_replace(TEXT_MONEY_SYMBOL,'',
                      $currencies->format($info[$last - 1]['totsum'])).
                      TEXT_MONEY_SYMBOL;
                  }
                }
    ?></td>
              </tr>
              <?php
    if ($srDetail) {
      for ($i = 0; $i < $last; $i++) {
        if ($srMax === 'all' or $i < $srMax) {
    //生成跳转到商品管理页面的URL
    $products_categories_id = tep_get_products_parent_id($info[$i]['pid']);
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

     if(in_array($info[$i]['pid'],$products_value)){

       $page = $products_key;
       break;
     }
   }
  ?>
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
                <td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent" align="left"><a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$info[$i]['pid'].'&site_id=0', 'NONSSL'); ?>"><?php echo $info[$i]['pname']; ?></a>
                </td>
                <td class="dataTableContent" align="right"><?php echo $info[$i]['pquant']; ?></td>
                <?php
            if ($srDetail == 2) {?>
                <td class="dataTableContent" align="right"><?php 
                  if ($info[$i]['psum'] < 0) {
                    echo '<font color="red">'.
                      str_replace(TEXT_MONEY_SYMBOL,'',
                      $currencies->format($info[$i]['psum'])).
                      '</font>'.TEXT_MONEY_SYMBOL; 
                  } else {
                    echo str_replace(TEXT_MONEY_SYMBOL,'',
                      $currencies->format($info[$i]['psum'])).TEXT_MONEY_SYMBOL; 
                  }
                   ?></td>
                <?php
            } else { ?>
                <td class="dataTableContent">&nbsp;</td>
                <?php
            }
  ?>
                
              </tr>
              <?php
        }
      }
    }
  }
}else{

  $info = $sr2->next(); 
  foreach($info as $info_value){

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }  
    //生成跳转到商品管理页面的URL
    $products_categories_id = tep_get_products_parent_id($info_value['pid']);
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

     if(in_array($info_value['pid'],$products_value)){

       $page = $products_key;
       break;
     }
   }
?> 
    <tr class="<?php echo $nowColor;?>" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='<?php echo $nowColor;?>'">
      <td class="dataTableContent" align="left"><?php echo tep_date_long(date("Y-m-d\ H:i:s", strtotime($info_value['date_purchased']))); ?></td>
      <td class="dataTableContent" align="left"><?php echo '<a href="'.tep_href_link(FILENAME_ORDERS, 'keywords='.$info_value['orders_id'].'&search_type=orders_id', 'NONSSL').'">'.$info_value['orders_id'].'</a>&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$categories_url_id.'&page='.$page.'&pID='.$info_value['pid'].'&site_id=0', 'NONSSL').'">'.$info_value['pname']; ?></a></td>
      <td class="dataTableContent" align="right"><?php echo $info_value['pquant']; ?></td>
      <td class="dataTableContent" align="right"><?php echo $info_value['psum'] < 0 ? '<font color="red">'.str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info_value['psum'])).'</font>'.TEXT_MONEY_SYMBOL : str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($info_value['psum'])); ?></td> 
   </tr>
<?php 
  }
}
}
?>
            </table>


  <?php } ?>
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
<?php
  require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof -->
</body>
</html>
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
