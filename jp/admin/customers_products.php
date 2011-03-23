<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES.'currencies.php');
  $currencies = new currencies(2);
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'get_products':
        echo tep_draw_pull_down_menu('',array_merge(array(array('id' => '0','text' => ' -- ')),tep_get_products_tree($_GET['cid'])),$_GET['rid'],'onchange=\'$(name_ele).val(this.options[this.selectedIndex].innerHTML);name_over();\'');
        exit;
        break;
      case 'init':
        $res = array();
        if (is_array($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']])) {
          foreach ($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']] as $okey => $ovalue) {
            $print_order_query = tep_db_query("select o.torihiki_date, op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op  where o.orders_id = op.orders_id and o.orders_id = '".$ovalue."'");     
            while ($print_order_res = tep_db_fetch_array($print_order_query)) {
              $print_order_res['torihiki_date'] = date('Y/n/j',strtotime($print_order_res['torihiki_date']));
              $res[] = $print_order_res;
            }
          }
        }
        echo json_encode($res);
        exit;
      case 'check_one': // 跨页复选框
        $_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$_GET['orders_id']] = $_GET['orders_id'];
        exit;
      case 'clear_one': // 跨页复选框
        unset($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$_GET['orders_id']]);
        exit;
      case 'check_all': // 跨页复选框
        $orders_query = tep_db_query("select * from orders o where o.customers_id='".$_GET['customers_id']."'");
        while($o = tep_db_fetch_array($orders_query)){
          $_SESSION['customers_products']['orders_selected'][$_GET['customers_id']][$o['orders_id']] = $o['orders_id'];
        }
        exit;
      case 'clear_all': // 跨页复选框
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
  var number = 0;
  // 插入对象
  var name_ele;
  function init() {
    $.ajax({
      dataType: 'json',
      async: false,
      url: 'customers_products.php?action=init&customers_id=<?php echo $_GET['customers_id'];?>',
      success: function(data) {
        for(i in data){
          add_one({
            torihiki_date:data[i]['torihiki_date'],
            products_name:data[i]['products_name'],
            final_price:data[i]['final_price'],
            products_quantity:data[i]['products_quantity'],
          });
        }
        calc_cost();
      }
    });
    
  }
  function add_empty () {
    add_one({
      torihiki_date:'&nbsp;',
      products_name:'',
      final_price:'',
      products_quantity:'',
    });
  }
  function add_one (data){
    html = "<tr align=\"center\" style=\"font-size:14px;\">\n";
    html += "<td class=\"link_01 number\" id=\"number_"+number+"\"></td>\n";
    html += "<td class=\"link_01 date\" id=\"tdate_"+number+"\"               ><input size=\"10\" type=\"text\" value=\""+data['torihiki_date']+"\"     onchange=\"date_change()\"></td>";
    html += "<td class=\"link_01 name\" id=\"pname_"+number+"\" align=\"left\"><input size=\"30\" type=\"text\" value=\""+data['products_name']+"\" onfocus=\"name_click("+number+",this)\" id=\"name_display_"+number+"\"></td>";
    html += "<td class=\"link_01 price\" id=\"fprice_"+number+"\"             ><input size=\"10\" type=\"text\" value=\""+(data['final_price'] != ''?(parseFloat(data['final_price']).toFixed(2)):'')+"\"       onchange=\"price_change(this)\"></td>";
    html += "<td class=\"link_01 quantity\" id=\"pquantity_"+number+"\"       ><input size=\"4\"  type=\"text\" value=\""+data['products_quantity']+"\" onchange=\"quantity_change()\"></td>";
    html += "<td class=\"link_01 percent\" align=\"right\" onclick=\"percent("+number+")\">\n";
    
    html += "<span id=\"percent_"+number+"\" style=\"display:none;\">\n";
    html += "  <select id=\"select_"+number+"\" onblur=\"percent_out("+number+")\" onchange=\"percent_change("+number+")\" onpropertychange=\"percent_change("+number+")\">\n";
    html += "    <option value=\"1.00\">100%</option>\n";
    html += "    <option value=\"0.99\">99%</option>\n";
    html += "    <option value=\"0.98\">98%</option>\n";
    html += "    <option value=\"0.97\">97%</option>\n";
    html += "    <option value=\"0.96\">96%</option>\n";
    html += "    <option value=\"0.95\">95%</option>\n";
    html += "    <option value=\"0.94\">94%</option>\n";
    html += "    <option value=\"0.93\">93%</option>\n";
    html += "    <option value=\"0.92\">92%</option>\n";
    html += "    <option value=\"0.91\">91%</option>\n";
    html += "    <option value=\"0.90\">90%</option>\n";
    html += "  </select>\n";
    html += "</span>\n";
    html += "<span id=\"percent_display_"+number+"\" class=\"percent_display\">1.00</span>\n";
    
    html += "</td>";
    html += "<td class=\"link_01\" align=\"right\"><span class=\"fprice\" id=\"price_"+number+"\"></span><a href=\"javascript:void(0)\" onclick=\"remove_one(this.parentNode.parentNode)\"><img src=\"includes/languages/japanese/images/not.gif\"></a></td>";
    //html += "<td class=\"link_01\" align=\"right\"></td>";
    html += "</tr>\n";
    $('#tbody').append(html);
    number++;
  }
  // 删除一行
  function remove_one(ele) {
    $(ele).remove();
    calc_cost();
  }
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
    //document.getElementById('price_'+no).innerHTML = (ele.options[ele.selectedIndex].value * document.getElementById('final_price_'+no).value).toFixed(2);
  }
  // 重新计算总价
  function calc_cost() {
    var cost = 0;
    var no   = 1;
    $('#tbody').children().each(function(){
      if ($(this).find('.price input').val() != '' && $(this).find('.quantity input').val() != ''){
        // 插入序号
        $(this).find('.number').html(no);
        fp = parseFloat($(this).find('.price input').val()) 
          * parseFloat($(this).find('.quantity input').val()) 
          * parseFloat($(this).find('.percent_display').html());
        // 插入小计
        $(this).find('.fprice').html(fp.toFixed(2));
        cost += fp;
        no++;
      }
      });
    //$('.price').each(function(){
    //  cost += parseFloat(this.innerHTML.replace(/,/g,''));
    //});
    cost *= parseFloat(document.getElementById('percent_display_cost').innerHTML);
    // 插入总计
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
          $('#data6').val(data['data6']);
          $('#data7').val(data['data7']);
          $('#data8').val(data['data8']);
          $('#data9').val(data['data9']);
          $('#data10').val(data['data10']);
          $('#email').val(data['email']);
          $('#responsible').val(data['responsible']);
        }
      });
    }
  }

  function percent_change_cost(){
    ele = document.getElementById('select_cost');
    document.getElementById('percent_display_cost').innerHTML = ele.options[ele.selectedIndex].value;
  }
  
  function date_change(){
    calc_cost();
  }
  /*
  function name_change(){
    calc_cost();
  }
  */
  
  // 显示商品名选择框
  function name_click(num,ele){
    offset = $(ele).offset();
    $('#products_name_selector').css('left', offset.left);
    $('#products_name_selector').css('top',  offset.top);
    name_ele = document.getElementById('name_display_'+num);;
    $('#products_name_selector').css('display','block');
  }
  
  //  商品名选择结束
  function name_over(){
    name_ele = null;
    $("#products_name_selector").css("display","none");
    $('#relate_products').html('');
    document.getElementById('category_selector').selectedIndex = 0;
  }
  
  function price_change(ele){
    ele.value = parseFloat(ele.value).toFixed(2);
    calc_cost();
  }
  
  function quantity_change(){
    calc_cost();
  }
  function relate_products1(cid,rid){
    $.ajax({
      dataType: 'text',
      url: 'customers_products.php?action=get_products&cid='+cid+'&rid='+rid,
      success: function(text) {
        $('#relate_products').html(text);
      }
    });
  }
  $(function(){
    init();
  });
</script>
<table border="0" width="563" style="font-family:ＭＳ Ｐゴシック">
  <tr>
    <td style=" font-family:ＭＳ Ｐゴシック;font-size:22px; padding-left:185px;"><input name="textfield" type="text" id="data1" value="" style=" height:23px; width:130px; font-size:14px; font-weight:bold; margin-right:5px;"></td>
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
        <tr><td height="30" colspan="2"><b>&nbsp;&nbsp;<input name="textfield" type="text" id="data2" value="" style=" height:23px; width:180px; font-size:14px; font-weight:bold; margin-right:5px;">御中</b></td></tr>
        <tr><td height="13"></td></tr>
        <tr><td height="31" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $currencies->format($total_cost);?>税込</td></tr>
        <tr><td height="34" colspan="2" align="left" valign="bottom"><input name="textfield" type="text" id="data3" value="" style=" height:23px; width:180px; font-size:14px; font-weight:bold; margin-right:5px;"></td></tr>
        <tr><td height="19"></td></tr>
        <tr><td height="19" colspan="2" align="left"><input name="textfield" type="text" id="data4" value="" style=" height:23px; width:180px; font-size:14px; font-weight:bold; margin-right:5px;"></td></tr>
        <tr>
          <td width="65" height="38"></td>
          <td width="292" height="38" valign="top" align="left"><font size="3"><u><textarea id="data5" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></u></font></td>
        </tr>
        <tr>
          <td></td>
            <td height="33" valign="top"><font size="3"><u><textarea id="data6" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></u></font></td>
        </tr>
        <tr>
          <td></td>
            <td height="67" valign="top"><font size="3"><textarea id="data7" type="text" rows="6" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></font></td>
        </tr>
        <tr><td height="25" valign="top" colspan="2" valign="top"><input name="textfield" type="text" id="data8" value="" style=" height:23px; width:180px; font-size:14px; font-weight:bold; margin-right:5px;"></td></tr>
        <tr><td height="25" valign="bottom" colspan="2" valign="top"><input name="textfield" type="text" id="data9" value="" style=" height:23px; width:180px; font-size:14px; font-weight:bold; margin-right:5px;"></td></tr>
      </table>
    </td>
    <td>
      <table border="0" width="195" class="print_innput">
        <tr><td height="10"></td></tr>
        <tr><td height="23" valign="top" align="right"><input name="textfield" type="text" id="textfield" value="<?php echo tep_date_long(date('Y-m-d H:i:s'));?>" style="height:20px; width:150px; text-align:right; font-size:14px; margin-right:5px;"></td></tr>
        <tr><td width="31"></td></tr>
        <tr><td align="right" height="44" valign="bottom"><font size="3"><textarea id="data10" type="text" rows="3" value="カ)アールエムティエイチアイ" style="font-size:18px;" ></textarea></font></td></tr>
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
<tbody id="tbody"></tbody>
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
<td align="right" style="border-top:none; border-left:none; font-family:Arial; font-size:13px;" id="cost_display"><?php echo number_format($total_cost);?></td>
</tr>
</table>
<table cellpadding="5" cellspacing="0" border="0" width="548" class="print_none">
  <tr><td height="10" colspan="2"></td></tr>
  <tr>
      <td align="left"><a href="javascript:void(0)" onclick="add_empty()"><img src="includes/languages/japanese/images/z_01.gif"></a></td>
      <td align="right" style="display:block;"><input name="" type="button" value="プリント" onclick="window.print();"></td>
  </tr>
</table>
<div id="products_name_selector" style="display:none;position:absolute;">
  <?php echo tep_draw_pull_down_menu('categories_name', tep_get_category_tree('&nbsp;'), 0, 'id="category_selector" onchange="relate_products1(this.options[this.selectedIndex].value, \''.$pInfo->relate_products_id.'\')"');?>
  <span id="relate_products"></span>
</div>
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
function all_check(ajax) 
{
  var sel_p = document.getElementById("allcheck");
  var sel_p_list = document.getElementsByName('oid[]');
   
  for (var i=0; i<sel_p_list.length; i++) {
    if (sel_p.checked) {
      sel_p_list[i].checked = true; 
      if (ajax)
        check_one(sel_p_list[i].value,<?php echo $_GET['cID'];?>);
    } else {
      sel_p_list[i].checked = false; 
      if (ajax)
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
      all_check(false);
    }
  });
}
function clear_all(ele,cid) {
  $.ajax({
    url: 'customers_products.php?action=clear_all&customers_id='+cid,
    success: function(data) {
      document.getElementById('allcheck').checked = false;
      all_check(false);
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
        <a href="javascript:void(0);" onclick="check_all(this,<?php echo $_GET['cID'];?>);">全部</a>
        <a href="javascript:void(0);" onclick="clear_all(this,<?php echo $_GET['cID'];?>);">キャンセル</a>
        <table id="orders_list_table" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                  <td class="dataTableHeadingContent"><input id="allcheck" type="checkbox" name="allcheck" value="" onclick="all_check(true);"><?php echo TABLE_HEADING_CUSTOMER_NAME;?></td> 
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
