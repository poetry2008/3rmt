<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES.'currencies.php');
  $currencies = new currencies(2);
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'check_one':
        $_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$_GET['orders_id']] = $_GET['orders_id'];
        exit;
      case 'clear_one':
        unset($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$_GET['orders_id']]);
        exit;
      case 'check_all':
        $orders_query = tep_db_query("select * from orders o where o.customers_id='".$_GET['customers_id']."'");
        while($o = tep_db_fetch_array($orders_query)){
          $_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$o['orders_id']] = $o['orders_id'];
        }
        exit;
      case 'clear_all':
        unset($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']]);
        exit;
      case 'get_bill_template':
        echo json_encode(tep_db_fetch_array(tep_db_query("select * from bill_templates where id='".$_GET['id']."'")));
        exit;
      case 'print':
        $total_cost = 0;
        foreach ($_POST['oid'] as $ikey => $ivalue) {
          $print_total_query = tep_db_query("select o.torihiki_date, op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op  where o.orders_id = op.orders_id and o.orders_id = '".$ivalue."'");     
          while ($print_total_res = tep_db_fetch_array($print_total_query)) {
            $total_cost += $print_total_res['final_price']*$print_total_res['products_quantity']; 
          }
        }
?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link media="print" href="includes/print.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script>
  function percent(no) {
    document.getElementById('percent_display_'+no).style.display='none';
    document.getElementById('percent_'+no).style.display='block';
  }
  function percent_out(no){
    document.getElementById('percent_'+no).style.display='none';
    document.getElementById('percent_display_'+no).style.display='block';
    calc_cost();
  }
  function percent_change(no){
    ele = document.getElementById('select_'+no);
    document.getElementById('percent_display_'+no).innerHTML = ele.options[ele.selectedIndex].value;
    document.getElementById('price_'+no).innerHTML = (ele.options[ele.selectedIndex].value * document.getElementById('final_price_'+no).value).toFixed(2);
  }
  function calc_cost() {
    var cost = 0;
    $('.price').each(function(){
      cost += parseFloat(this.innerHTML.replace(/,/g,''));
    });
    cost *= parseFloat(document.getElementById('percent_display_cost').innerHTML);
    $('#cost_display').html(cost.toFixed(2));
  }
  function percent_cost(){
    document.getElementById('percent_display_cost').style.display='none';
    document.getElementById('percent_cost').style.display='block';
  }

  function percent_out_cost(){
    document.getElementById('percent_cost').style.display='none';
    document.getElementById('percent_display_cost').style.display='block';
    calc_cost();
  }

  function percent_change_cost(){
    ele = document.getElementById('select_cost');
    document.getElementById('percent_display_cost').innerHTML = ele.options[ele.selectedIndex].value;
  }
  
  function bill_template_change(ele) {
    bid = ele.options[ele.selectedIndex].value;
    if (bid != '') {
      $.ajax({
        url: 'customers_products.php?action=get_bill_template&id='+bid,
        dataType: 'json',
        success: function(data) {
          $('#data1').val(data['data1']);
          $('#data2').val(data['data2']);
          $('#data3').val(data['data3']);
          $('#data4').val(data['data4']);
          $('#data5').val(data['data5']);
          $('#email').val(data['email']);
          $('#responsible').val(data['responsible']);
        }
      });
    }
  }

</script>
<table border="0" width="563" style="font-family:ＭＳ Ｐゴシック">
  <tr>
    <td style=" font-family:ＭＳ Ｐゴシック;font-size:22px; padding-left:185px;">御請求書</td>
    <td class="print_none">
<?php 
  $bill_query = tep_db_query("select * from bill_templates order by sort_order asc");
  while($b = tep_db_fetch_array($bill_query)){
    $bill_templates[] = $b;
  }
  if ($bill_templates) {
?>
<select id="bill_templates" onchange="bill_template_change(this)">
  <option value=""> -- </option>
<?php foreach ($bill_templates as $bill) { ?>
  <option value="<?php echo $bill['id'];?>"><?php echo $bill['name'];?></option>
<?php } ?>
</select>
<?php
    }
?>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" width="369" class="print_innput">
        <tr><td height="30" colspan="2"><b>&nbsp;&nbsp;<input name="textfield" type="text" id="data1" value="株式会社iimy" style=" height:23px; width:130px; font-size:14px; font-weight:bold; margin-right:5px;">御中</b></td></tr>
    <tr><td height="13"></td></tr>
        <tr><td height="31" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $currencies->format($total_cost);?>税込</td></tr>
        <tr><td height="34" colspan="2" align="left" valign="bottom"><font size="3">上記金額をお振り込みください。</font></td></tr>
    <tr><td height="19"></td></tr>
        <tr><td height="19" colspan="2" align="left"><font size="2">代金振り込み先</font></td></tr>
        <tr>
          <td width="65" height="38"></td>
          <td width="292" height="38" valign="top" align="left"><font size="3"><u><textarea id="data2" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></u></font></td>
        </tr>
        <tr>
          <td></td>
            <td height="33" valign="top"><font size="3"><u><textarea id="data3" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></u></font></td>
        </tr>
        <tr>
          <td></td>
            <td height="67" valign="top"><font size="3"><textarea id="data4" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></font></td>
        </tr>
        <tr><td height="25" valign="top" colspan="2" valign="top"><font size="2">但し、 品代として。</font></td></tr>
        <tr><td height="25" valign="bottom" colspan="2" valign="top"><font size="2">下記の通りご請求申し上げます。</font></td></tr>
      </table>
    </td>
    <td>
      <table border="0" width="195" class="print_innput">
        <tr><td height="10"></td></tr>
        <tr><td height="23" valign="top" align="right"><input name="textfield" type="text" id="textfield" value="<?php echo tep_date_long(date('Y-m-d H:i:s'));?>" style="height:20px; width:150px; text-align:right; font-size:14px; margin-right:5px;"></td></tr>
        <tr><td width="31"></td></tr>
        <tr><td align="right" height="44" valign="bottom"><font size="3"><textarea id="data5" type="text" rows="3" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></font></td></tr>
        <tr><td align="right" height="19" valign="bottom"><font size="2"><a href="#"><input name="textfield" type="text" id="email" value="" style=" height:18px; width:190px; text-align:right; font-size:16px; margin-right:5px;"></a></font></td></tr>
        <tr><td width="19"></td></tr>
        <tr><td align="right" colspan="4" height="163">
          <table width="30" style="border:#666666 1px solid;">
          <tr><td style="border-bottom:#666666 1px solid;" align="center"><font size="2">責任者</font></td></tr>
          <tr><td height="120" colspan="6"><font size="5"><b><textarea id="responsible" type="text" rows="6" value="カ)アールエムティエイチアイ"  style="font-size:14px;"></textarea></b></font></td></tr>
          </table>
        </td></tr>
      </table>
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" border="0" width="563" class="link_print">
<tr align="center">
<td class="link_02">No.</td>
<td class="link_02">取引日</td>
<td class="link_02">商品名</td>
<td class="link_02">単価</td>
<td class="link_02">数量</td>
<td class="link_02">値引</td>
<td class="link_02">金額</td>
</tr>
    <?php
    $print_num = 1; 
    if (is_array($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']])) 
    foreach ($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']] as $okey => $ovalue) {
      $print_order_query = tep_db_query("select o.torihiki_date, op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op  where o.orders_id = op.orders_id and o.orders_id = '".$ovalue."'");     
      while ($print_order_res = tep_db_fetch_array($print_order_query)) {
    ?>
<tr align="center" style="font-size:14px;">
<td class="link_01"><?php echo $print_num;?></td>
<td class="link_01"><?php echo tep_datetime_short($print_order_res['torihiki_date']);?></td>
<td class="link_01"><?php echo $print_order_res['products_name'];?></td>
<td class="link_01" align="right"><?php echo $print_order_res['final_price'];?></td>
<td class="link_01" align="right"><?php echo $print_order_res['products_quantity'];?></td>
<td class="link_01" align="right" onclick="percent(<?php echo $print_num;?>)">
  <span id="percent_<?php echo $print_num;?>" style="display:none;">
    <select id="select_<?php echo $print_num;?>" onblur="percent_out(<?php echo $print_num;?>)" onchange="percent_change(<?php echo $print_num;?>)" onpropertychange="percent_change(<?php echo $print_num;?>)">
      <option value="1.00">100%</option>
      <option value="0.99">99%</option>
      <option value="0.98">98%</option>
      <option value="0.97">97%</option>
      <option value="0.96">96%</option>
      <option value="0.95">95%</option>
      <option value="0.94">94%</option>
      <option value="0.93">93%</option>
      <option value="0.92">92%</option>
      <option value="0.91">91%</option>
      <option value="0.90">90%</option>
    </select>
  </span>
  <span id="percent_display_<?php echo $print_num;?>" class="percent_display">1.00</span>
  <input type="hidden" id="final_price_<?php echo $print_num;?>" value="<?php echo $print_order_res['products_quantity']*$print_order_res['final_price'];?>">
</td>
<td class="link_01 price" align="right" id="price_<?php echo $print_num;?>">
  <?php echo number_format($print_order_res['products_quantity'] * $print_order_res['final_price']);?>
</td>
</tr>
    <?php
      $print_num++; 
      }
    }
    ?>

  <tr><td colspan="4" width="360" ></td>
      <td align="center" bgcolor="#00FFFF" style=" border-top:none; border-right:none; font-family:ＭＳ Ｐゴシック; font-size:13px;">小計<input type="hidden" id="cost" value="<?php echo $total_cost;?>">
</td>
<td class="link_01" align="right" onclick="percent_cost()">
  <span id="percent_cost" style="display:none;">
    <select id="select_cost" onblur="percent_out_cost()" onchange="percent_change_cost()" onpropertychange="percent_change_cost()">
      <option value="1.00">100%</option>
      <option value="0.99">99%</option>
      <option value="0.98">98%</option>
      <option value="0.97">97%</option>
      <option value="0.96">96%</option>
      <option value="0.95">95%</option>
      <option value="0.94">94%</option>
      <option value="0.93">93%</option>
      <option value="0.92">92%</option>
      <option value="0.91">91%</option>
      <option value="0.90">90%</option>
    </select>
  </span>
  <span id="percent_display_cost">1.00</span>
</td>
<td align="center" style="border-top:none; border-left:none; font-family:Arial; font-size:13px;" id="cost_display"><?php echo number_format($total_cost);?></td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="563" class="print_none">
  <tr><td height="10"></td></tr>
  <tr>
      <td align="right" style="display:block;"><input name="" type="button" value="プリント" onclick="window.print();"></td>
  </tr>
</table>
<?php
      exit;
      break;
    }
  }
  
  $product_history_query_raw = "select o.orders_id, o.customers_name, o.date_purchased, o.orders_status from ".TABLE_ORDERS." o where o.customers_id = '".$_GET['cID']."' order by o.date_purchased desc";
  $product_history_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $product_history_query_raw, $product_history_numrows);
  $product_history_query = tep_db_query($product_history_query_raw);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script>
function all_check() 
{
  var sel_p = document.getElementById("allcheck");
  var sel_p_list = document.getElementsByName('oid[]');
   
  for (var i=0; i<sel_p_list.length; i++) {
    if (sel_p.checked) {
      sel_p_list[i].checked = true; 
      check_one(sel_p_list[i].value,<?php echo $_GET['cID'];?>);
    } else {
      sel_p_list[i].checked = false; 
      clear_one(sel_p_list[i].value,<?php echo $_GET['cID'];?>);
    }
  }
}
function check_select()
{
  var sel_p_list = document.getElementsByName('oid[]');
  for (var i=0; i<sel_p_list.length; i++) {
    if (sel_p_list[i].checked) {
       return true; 
    }
  }
  return false;
}
function click_one(ele,oid,cid){
  if (ele.checked == true) {
    check_one(oid,cid);
  } else {
    clear_one(oid,cid);
  }
}
function check_one(oid,cid){
  $.ajax({
    dataType: 'text',
    url: 'customers_products.php?action=check_one&orders_id='+oid+'&customers_id='+cid,
    //success: function(text) {}
  });
}
function clear_one(oid,cid) {
  $.ajax({
    url: 'customers_products.php?action=clear_one&orders_id='+oid+'&customers_id='+cid,
    //success: function(data) {}
  });
}
function check_all(ele,cid) {
  $.ajax({
    url: 'customers_products.php?action=check_all&customers_id='+cid,
    success: function(data) {
      document.getElementById('allcheck').checked = true;
      all_check();
    }
  });
}
function clear_all(ele,cid) {
  $.ajax({
    url: 'customers_products.php?action=clear_all&customers_id='+cid,
    success: function(data) {
      document.getElementById('allcheck').checked = false;
      all_check();
    }
  });
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<form action="?action=print&customers_id=<?php echo $_GET['cID'];?>" method="post" name="form" onSubmit="return check_select();" target="_blank">
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table>
    </td>
    <td valign="top">   
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE;?></td> 
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT);?></td> 
            </tr>
        </table>
        <a href="javascript:void(0);" onclick="check_all(this,<?php echo $_GET['cID'];?>);">全选</a>
        <a href="javascript:void(0);" onclick="clear_all(this,<?php echo $_GET['cID'];?>);">取消</a>
        <table id="orders_list_table" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                  <td class="dataTableHeadingContent"><input id="allcheck" type="checkbox" name="allcheck" value="" onclick="all_check();"><?php echo TABLE_HEADING_CUSTOMER_NAME;?></td> 
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE;?></td> 
                  <td style="table-layout:fixed;width:120px;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NAME;?></td> 
                  <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_PRICE;?></td> 
                  <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NUM;?></td> 
                  <td style="width:100px;text-align:right;" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_COST;?></td> 
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS;?></td> 
                </tr>
                <?php 
                while ($product_history = tep_db_fetch_array($product_history_query)) {
                  $product_list_query = tep_db_query("select op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and o.orders_id = '".$product_history['orders_id']."'");
                  $i = 1; 
                  $product_list_total = tep_db_num_rows($product_list_query); 
                  while ($product_list_res = tep_db_fetch_array($product_list_query)) {
                  ?>
                  <tr class="dataTableRow">
                    <?php
                    if ($product_list_total == $i) {
                      $style_str = 'border-bottom: 1px solid #000;padding-top:5px;'; 
                    } else {
                      $style_str = 'padding-top:5px;'; 
                    }
                    ?>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                    <?php 
                    if ($i == 1) {
                      echo '<input type="checkbox" class="ocheckbox" name="oid[]" value="'.$product_history['orders_id'].'" onclick="click_one(this,\''.$product_history['orders_id'].'\','.$_GET['cID'].')"';
                      
                      if(isset($_SESSION['customers_products']['orders_selected'][$_GET['cID']][$product_history['orders_id']])){
                        echo " checked ";
                      }
                      
                      echo '>';
                      echo $product_history['customers_name'];
                    } else {
                      echo '&nbsp;';
                    }
                    ?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                    <?php 
                    if ($i == 1) {
                      echo tep_datetime_short($product_history['date_purchased']);
                    } else {
                      echo '&nbsp;';
                    }
                    ?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top">
                    <?php echo $product_list_res['products_name'];?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                    <?php echo $currencies->format($product_list_res['final_price']);?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                    <?php echo $product_list_res['products_quantity'].PRODUCT_NUM_TEXT;?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top" align="right">
                    <?php echo $currencies->format($product_list_res['products_quantity']*$product_list_res['final_price']);?> 
                    </td>
                    <td class="dataTableContent" style="<?php echo $style_str;?>" valign="top"> 
                    <?php
                    $orders_status_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = ".(int)$product_history['orders_status']); 
                    $orders_status_res = tep_db_fetch_array($orders_status_query); 
                    if ($i == 1) {
                      echo $orders_status_res['orders_status_name']; 
                    } else {
                      echo '&nbsp;';
                    }
                    ?>
                    </td>
                  </tr> 
                  <?php
                    $i++; 
                  }
                }
                ?>
                <tr>
                 <td colspan="7">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText" valign="top"><?php echo $product_history_split->display_count($product_history_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                      <td class="smallText" align="right"><?php echo $product_history_split->display_links($product_history_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></td>
                    </tr>
                  </table>
                 </td>
                </tr>
                <tr>
                  <td colspan="3" align="left"></td>
                  <td colspan="4" align="right">
                  <input type="image" src="includes/languages/japanese/images/buttons/button_print.gif" align="top">
                  <a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, str_replace('cpage', 'page', tep_get_all_get_params(array('page'))));?>"><?php echo tep_image_button('button_back.gif', IMAGE_BACK);?></a> 
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
    </td>
  </tr>
</table>
</form>
<?php require(DIR_WS_INCLUDES.'footer.php');?>
</body>
</html>
