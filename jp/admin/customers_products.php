<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES.'currencies.php');
  require_once(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/japanese/customers_products.php');
  $currencies = new currencies(2);

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");

  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'get_products' 获得商品下拉列表   
   case 'init' 初始化该顾客订单信息   
   case 'check_one' 选择一个复选框    
   case 'clear_one' 清除一个服选框    
   case 'check_all' 全选   
   case 'clear_all' 清除所选择    
   case 'get_bill_template' 获得模板    
   case 'print' 打印    
------------------------------------------------------*/
      case 'get_products':
        echo tep_draw_pull_down_menu('',array_merge(array(array('id' => '0','text' => ' -- ')),tep_get_products_tree($_GET['cid'])),$_GET['rid'],'onchange=\'$(name_ele).val(this.options[this.selectedIndex].innerHTML);name_over();\'');
        exit;
        break;
      case 'init':
        $res = array();
        if (is_array($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']])) {
          $i = 0;
          foreach ($_SESSION['customers_products']['orders_selected'][$_GET['customers_id']] as $okey => $ovalue) {
            $print_order_query = tep_db_query("select o.torihiki_date, op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op  where o.orders_id = op.orders_id and o.orders_id = '".$ovalue."'");     
            
            while ($print_order_res = tep_db_fetch_array($print_order_query)) {
              $print_order_res['torihiki_date'] = date('Y/n/j',strtotime($print_order_res['torihiki_date']));
              $res[strtotime($print_order_res['torihiki_date'])+$i] = $print_order_res;
              $i ++;
            }
          }
          ksort($res);
        }
        echo json_encode($res);
        exit;
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
<!--<meta http-equiv=”X-UA-Compatible” content=”IE=7″>-->
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link media="print" href="includes/print.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script>
<?php //检查文本域?>
function textarea_check(ele){
    var comment_str = $(ele).val();
    var comment_str_array = comment_str.split("\n");
    var comment_str_length = comment_str_array.length;
    var comment_cols = $(ele).css("width");
    var comment_font = $(ele).css("font-size"); 
    var comment_id = $(ele).attr("id");
    var comment_temp = 0;
    var comment_temp_line = 0;
    if(comment_id == 'responsible'){comment_temp = 30;comment_temp_line = 1;}
    var comment_num;
    var comment_line = 0;
    var comment_sum;
    comment_cols = comment_cols.replace("px","");
    comment_font = comment_font.replace("px","");
    comment_cols = parseInt(comment_cols);
    comment_font = parseInt(comment_font);
    comment_num = (comment_cols-comment_temp)/comment_font;
    var comment = '';
    var i = 0;
    for(x in comment_str_array){

      if(comment_str_array[x].length > comment_num){

        comment = comment_str_array[x];
        comment_sum = comment.length;
        comment_sum = parseInt(comment_sum);
        comment_line += Math.round(comment_sum/comment_num)+comment_temp_line;
        i++;
      }
    } 
    var comment_str_temp = 1; 
    $(ele).attr("rows",comment_line+comment_str_length-i+comment_str_temp);
}
$(document).ready(function(){
  $("textarea").keyup(function(){
   textarea_check(this); 
  });
});
  <?php //数据保存?>
  var table_data = new Array();
  <?php  //平均高度?>
  var row_height = 19.20;
  <?php //单页显示个数?>
  var count  = 45;
  <?php //页高?>
  var page_height = 900;
  
  <?php //千位分隔符?>
  function number_format(num)
  { 
     num  =  num+""; 
     var  re=/(-?\d+)(\d{3})/ 
     while(re.test(num))
     { 
       num=num.replace(re,"$1,$2") 
     } 
     return  num; 
  }
  
  <?php //加载初始数据?>
  function init() {
    $.ajax({
      dataType: 'json',
      async: false,
      url: 'customers_products.php?action=init&customers_id=<?php echo $_GET['customers_id'];?>',
      success: function(data) {
        table_data = new Array();
        for (i in data) {
          add_one({
            torihiki_date     : data[i]['torihiki_date'],
            products_name     : data[i]['products_name'],
            final_price       : data[i]['final_price'],
            products_quantity : data[i]['products_quantity'],
            type              : '<?php echo TEXT_CUSTOMERS_PRODUCTS_CURRENCY;?>'
          });
        }
        create_table(table_data);
      } 
    });
  }
  <?php //创建表格?> 
  function create_table (data) {

    one_count = Math.floor((page_height - $('#content_html').height())/row_height)-2;
    html = "";
    
    empty = 0;
    <?php //tr计数器?>
    j = 0; 
    <?php //table计数器?> 
    k = 0; 
    html += table_header(k);
    for(i in data){
      if (j != 0 && (j == one_count || (j+1-one_count)%count == 1)) {
        html += table_footer(k-1, true);
      }
      if (j == one_count || (j+1-one_count)%count == 1) {
        html += table_header(k);
        k++;
      }
      html += add_tr(j, data[i]);
      j++;
    }
    
    if (j <= one_count) {
      empty = one_count - j;
    } else {
      empty = count - ((j - one_count)%count);
    }
    if (empty < count) 
    for (m = 0;m<empty;m++) {
      html += add_tr(j+m, {
        date     : '',
        name     : '',
        price    : '',
        quantity : '',
        percent  : '',
        type     : ''
      });
    }
    
    html += table_footer(k-1, false);
    $('#table_html').html(html);
    calc_cost();
  }
  <?php //表格头部?> 
  function table_header (num) {
    html =  "<div class=\"data_box\" style=\"width:100%\">\n";
    html += "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" width=\"100%\" class=\"data_table\" id=\"data_table_" + num + "\" align=\"center\">\n";
    html += "<thead><tr align=\"center\" >\n";
    html += "<td class=\"link_02\" width=\"5%\">No.</td>\n";
    html += "<td class=\"link_03\" width=\"20%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_ORDER_DATA;?></td>\n";
    html += "<td class=\"link_04\" width=\"10%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_CATEGORY;?></td>\n";
    html += "<td class=\"link_05\" width=\"25%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_PNAME;?></td>\n";
    html += "<td class=\"link_06\" width=\"10%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_ONE_PRICE;?></td>\n";
    html += "<td class=\"link_07\" width=\"5%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_QUANTITY;?></td>\n";
    html += "<td nowrap class=\"link_08\" width=\"10%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_REDUCTION_PRICE;?></td>\n";
    html += "<td nowrap class=\"link_09\" width=\"10%\"><?php echo TEXT_CUSTOMERS_PRODUCTS_PRICE;?></td>\n";
    html += "</tr></thead>";
    return html;
  }
  <?php //表格尾部?> 
  function table_footer (num,pagebreak) {
    html = "</table>";
    html += "<table width=\"100%\" class=\"text_x\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"";
    if (pagebreak) {
      html += " style=\"page-break-after:always;\"";
    }
    html += "><tr><td class=\"text_x1\"></td>\n";
    html += "    <td class=\"text_x2\" align=\"center\"><?php echo TEXT_CUSTOMERS_PRODUCTS_SUB_TOTAL;?></td>\n";
    html += "    <td class=\"text_x3 cost_display\" align=\"right\" id=\"cost_display_"+num+"\" ></td>\n";
    html += "  </tr></table>";
    html += "</div>";
    return html;
  }

  
  <?php //增加页面?>
  function add_empty () {
    for (i=0;i<count;i++) {
      table_data.push({
        date     : '',
        name     : '',
        price    : '',
        quantity : '',
        percent  : '',
        type     : ''
      });
    }
    create_table(table_data);
  }
  <?php //删除页面?> 
  function delete_empty () {
    for (j in table_data) {
      for (i in table_data) {
        if (
          typeof(table_data[i]) != 'undefined'
          && table_data[i]['date'] == ''
          && table_data[i]['name'] == ''
          && table_data[i]['price'] == ''
          && table_data[i]['quantity'] == ''
          && table_data[i]['type'] == ''
          ) {
            table_data.splice(i, 1);
        }
      }
    }
    create_table(table_data);
  }
  
  
  <?php //添加一行的html?>
  function add_tr (number, data) {
    data['price'] = Math.abs(data['price']);
    html = "<tr class=\"data\" align=\"center\" style=\"font-size:15px;\">\n";
    html += "<td class=\"link_01 number\"></td>\n";
    html += "<td id=\"tdate_"+number+"\"  align=\"center\"><input size=\"14\" type=\"text\" value=\""+data['date']+"\" onchange=\"date_change(this,"+number+")\"></td>";
    html += "<td class=\"link_01 type\" id=\"type_"+number+"\" align=\"center\" ><input size=\"10\" type=\"text\" value=\""+data['type']+"\" onchange=\"type_change(this,"+number+")\"></td>";
    html += "<td id=\"pname_"+number+"\" align=\"left\"><input size=\"45\" type=\"text\" value=\""+data['name']+"\" id=\"name_display_"+number+"\" onchange=\"name_change(this,"+number+")\"></td>";
    html += "<td class=\"link_01 price\" id=\"fprice_"+number+"\" align=\"right\" ><input size=\"12\" type=\"text\" value=\""+(Math.abs(data['price']) != ''?(Math.abs(parseFloat(data['price'])).toFixed(1)):'')+"\" onchange=\"price_change(this,"+number+")\" style=\"text-align:right;\"><span class=\"price_display\" id=\"price_display_"+number+"\">"+(data['price'] != ''?('¥'+parseFloat(data['price']).toFixed(1)).replace('-',''):'')+" </span>";
    html += "<input type=\"hidden\" class=\"price_flag\" id=\"fprice_flag_"+number+"\" value=\""+(data['price']>0?1:-1)+ "\" /></td>"
    html += "<td class=\"link_01 quantity\" id=\"pquantity_"+number+"\" align=\"right\"><input size=\"4\"  type=\"text\" value=\""+(data['quantity'] != ''?(parseFloat(data['quantity']).toFixed(1)):'')+"\" onchange=\"quantity_change(this,"+number+")\" style=\"text-align:right;\"></td>";
    html += "<td class=\"link_01 percent\" align=\"right\" onclick=\"percent("+number+")\">\n";
    
    html += "<span id=\"percent_"+number+"\" style=\"display:none;\">\n";
    html += "  <select class=\"percent_select\" id=\"select_"+number+"\" onblur=\"percent_out("+number+")\" onchange=\"percent_change("+number+")\">\n";
    html += "    <option value=\"1.00\""+(data['percent'] == 1.00 ? ' selected' : '')+">100%</option>\n";
    html += "    <option value=\"0.99\""+(data['percent'] == 0.99 ? ' selected' : '')+">99%</option>\n";
    html += "    <option value=\"0.98\""+(data['percent'] == 0.98 ? ' selected' : '')+">98%</option>\n";
    html += "    <option value=\"0.97\""+(data['percent'] == 0.97 ? ' selected' : '')+">97%</option>\n";
    html += "    <option value=\"0.96\""+(data['percent'] == 0.96 ? ' selected' : '')+">96%</option>\n";
    html += "    <option value=\"0.95\""+(data['percent'] == 0.95 ? ' selected' : '')+">95%</option>\n";
    html += "    <option value=\"0.94\""+(data['percent'] == 0.94 ? ' selected' : '')+">94%</option>\n";
    html += "    <option value=\"0.93\""+(data['percent'] == 0.93 ? ' selected' : '')+">93%</option>\n";
    html += "    <option value=\"0.92\""+(data['percent'] == 0.92 ? ' selected' : '')+">92%</option>\n";
    html += "    <option value=\"0.91\""+(data['percent'] == 0.91 ? ' selected' : '')+">91%</option>\n";
    html += "    <option value=\"0.90\""+(data['percent'] == 0.90 ? ' selected' : '')+">90%</option>\n";
    html += "  </select>\n";
    html += "</span>\n";
    html += "<span id=\"percent_display_"+number+"\" class=\"percent_display\">"+data['percent']+"</span>\n";
    
    html += "</td>";
    html += "<td class=\"link_01 je\" align=\"right\"><span class=\"fprice\" id=\"price_"+number+"\"></span><a class=\"not\"href=\"javascript:void(0)\" onclick=\"remove_one("+number+")\"><img src=\"/includes/languages/japanese/images/not.gif\"></a></td>";
    html += "</tr>\n";
    return html;
  }

  <?php //添加一行?>
  function add_one(data){
    table_data.push({
      date     : data['torihiki_date'],
      name     : data['products_name'],
      price    : data['final_price'],
      quantity : data['products_quantity'],
      percent  : '1.00',
      type     : data['type']
    });
  }
  
  <?php //删除一行?>
  function remove_one(num) {
    table_data.splice(num, 1);
    create_table(table_data);
    
  }
  <?php //激活百分比?>
  function percent(no) {
    document.getElementById('percent_display_'+no).style.display='none';
    document.getElementById('percent_'+no).style.display='block';
  }
  <?php //推出百分比选择?>
  function percent_out(no){
    data_empty(no);
    ele = document.getElementById('select_'+no);
    table_data[no]['percent'] = ele.options[ele.selectedIndex].value;
    document.getElementById('percent_display_'+no).innerHTML = ele.options[ele.selectedIndex].value;
    document.getElementById('percent_'+no).style.display='none';
    document.getElementById('percent_display_'+no).style.display='block';
    calc_cost();
  }
  <?php //动作?>
  function percent_change(no){
    data_empty(no);
    ele = document.getElementById('select_'+no);
    table_data[no]['percent'] = ele.options[ele.selectedIndex].value;
    document.getElementById('percent_display_'+no).innerHTML = ele.options[ele.selectedIndex].value;
  }
  <?php //重新计算总价?>
  function calc_cost() {
    var total = 0;
    var cost = 0;
    var no = 1;
    $('.data_box').each(function(){
      $(this).find('.data').each(function(){
        if ($(this).find('.price input').val() != '' && $(this).find('.quantity input').val() != ''){
          fp = $(this).find('.price_flag').val()*parseFloat($(this).find('.price input').val()) 
            * parseFloat($(this).find('.quantity input').val()) 
            * parseFloat($(this).find('.percent_select').val());
          $(this).find('.fprice').html(fp>0?number_format(fp.toFixed(0)):('<font color="red" class="print_black">'+number_format(fp.toFixed(0))+'</font>'));
          cost += fp;
          $(this).find('.number').html(no);
          no ++;
        }
      });
      $(this).find('.cost_display').html(number_format(cost.toFixed(0).replace('-',''))+'&nbsp;');
      total += cost;
      cost = 0;
    });
    $('#cost_print').html(number_format(total.toFixed(0)).replace('-',''));
  }

  <?php //选择模板?>
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
          $('#data11').val(data['data11']);
          $('#email_text').val(data['email']);
          $('#email_display').val(data['email']);
          $('#responsible').val(data['responsible']);
          $("textarea").each(function(){
            textarea_check(this); 
          });
        }
      });
    }
    <?php //重新生成表格?>
    create_table(table_data);
  }
  <?php //设置空数据?>
  function data_empty(num){
    if (typeof(table_data[num]) == 'undefined') {
      table_data[num] = {
        date     : '',
        name     : '',
        price    : '',
        quantity : '',
        percent  : '1.00',
        type     : ''
      }
    }
  }
  <?php //数据赋值?>
  function date_change(ele, num){
    data_empty(num);
    table_data[num]['date'] = ele.value;
  }
  
  <?php //类型赋值?>
  function type_change(ele, num){
    data_empty(num);
    table_data[num]['type'] = ele.value;
  }
  
  <?php //名赋值?>
  function name_change(ele, num){
    data_empty(num);
    table_data[num]['name'] = ele.value;
  }
  
  <?php //单价发生改变要重新计算总价格?>
  function price_change(ele,num){
    data_empty(num);
    table_data[num]['price'] = ele.value;
    ele.value = parseFloat(ele.value).toFixed(1);
    $('#price_display_'+num).html('¥'+parseFloat(ele.value).toFixed(1));
    $('#fprice_flag_'+num).val(1);
    calc_cost();
  }
  
  <?php //个数发生改变要重新计算总价格?>
  function quantity_change(ele,num){
    data_empty(num);
    table_data[num]['quantity'] = ele.value;
    ele.value = parseFloat(ele.value).toFixed(1);
    calc_cost();
  }
  
  <?php //上部文本发生改动时要重新分表格?>
  function textarea_change(){
    data_empty(num);
    create_table(table_data);
  }
  $(function(){
    init();
  });
</script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<body style="text-align:center;">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
  <div style="margin:0 auto; width:100%;">
    <div id="content_html">
<table border="0" width="100%" style=" margin-bottom:20px;" cellpadding="0" cellspacing="0" align="right">
  <tr>
      <td align="right" class="print">
<?php 
  $bill_query = tep_db_query("select * from bill_templates order by sort_order asc");
  while($b = tep_db_fetch_array($bill_query)){
    $bill_templates[] = $b;
  }
  if ($bill_templates) {
?>
<select id="bill_templates" onChange="bill_template_change(this)">
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
    <td align="center"><input class="input_print01" style="overflow:hidden;" name="textfield" type="text" id="data1" value=""></td>
  </tr>
  </table>
<table border="0" width="100%" style=" clear:both;" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <table border="0" width="50%" align="left" class="print_innput" cellpadding="0" cellspacing="0">
        <tr><td height="30" colspan="2" style="font-size:18px; padding-bottom:10px;"><input name="textfield" type="text" id="data2" value="" style=" height:23px; width:160px; font-size:18px; font-weight:bold; margin-right:30px;"><b><?php echo TEXT_CUSTOMERS_PRODUCTS_MAIL_STR;?></b></td></tr>
        <tr><td height="25" colspan="2" style=" font-size:18px; padding-bottom:15px;"><span id="cost_print"><?php echo $total_cost;?></span><div class="cost_print02"><?php 
        echo TEXT_MONEY_SYMBOL;
        echo '&nbsp;';
        echo TEXT_CUSTOMERS_PRODUCTS_TAX;
        ?></div></td></tr>
        <tr><td height="30" colspan="2" align="left" valign="center"><input name="textfield" type="text" id="data3" value="" style=" width:270px; font-size:16px; margin-right:5px;"></td></tr>
        <tr><td colspan="2" height="10" align="left"><input name="textfield" type="text" id="data4" value="" style=" width:200px; font-size:14px; margin-right:5px;"></td></tr>
        <tr>
          <td width="30"></td>
          <td width="292" valign="top" align="left" class="input_print03">
          <font size="3"><u><textarea id="data5" type="text" rows="2" style="font-size:14px; width:270px; overflow:hidden; resize:none;" onChange="textarea_change()"></textarea></u></font>
          <font size="3"><u><textarea id="data6" type="text" rows="2" style="font-size:14px; overflow:hidden; width:200px; resize:none;" onChange="textarea_change()"></textarea></u></font>
          <font size="3"><u><textarea id="data7" type="text" rows="2" style="font-size:14px; overflow:hidden; width:200px; resize:none;" onChange="textarea_change()"></textarea></u></font>
          </td>
        </tr>
</tr>
        <tr><td height="25" valign="top" colspan="2"><input name="textfield" type="text" id="data8" value="" style="width:200px; font-size:14px; margin-right:5px; margin-top:15px; margin-bottom:10px;"></td></tr>
        <tr><td valign="bottom" colspan="2" class="input_print04"><input name="textfield" type="text" id="data9" value="" style="width:250px; font-size:14px; margin-right:5px;"></td></tr>
      </table>
    </td>
    <td valign="top">
      <table border="0" width="50%" align="right" class="print_innput" style=" margin:10px 0;">
      <tr><td height="4"></td></tr>
        <tr><td height="30" valign="bottom" align="right"><input name="textfield" type="text" id="textfield" value="<?php echo tep_date_long(date('Y-m-d H:i:s'));?>" style=" height:20px; width:190px; text-align:right; font-size:16px;  margin:5px 0 20px 20px;"></td></tr>
        <tr><td align="right"><textarea id="data10" type="text" rows="2" style="font-size:14px; overflow:hidden; width:280px; resize:none;text-align:right;" ></textarea></td></tr>
        <tr><td align="right" class="input_print02">
  <font size="2">
  <input name="textfield" type="text" id="email_text" value="" onChange="$('#email_display').val(this.value)" onpropertychange="$('#email_display').val(this.value)" onBlur="$('#email_display').val(this.value)" style="text-align:right; font-size:12px; width:300px;">
  <input type="hidden" id="email_display" value="">
  </font></td></tr>
        <tr><td align="right" colspan="4">
          <table cellpadding="0" cellspacing="0" style="border:#000000 1px solid;margin-top:19px;">
          <tr><td style="border-bottom:#000000 1px solid;  padding-top:4px;" align="center">
          <input name="textfield" type="text" id="data11" value="" style="width:110px; font-size:12px; padding-top:4px; text-align:center; height:20px;">
          </td></tr>
          <tr><td colspan="6" align="center" valign="middle"><textarea id="responsible" type="text" rows="1" style=" width:100px; font-size:20px; overflow:hidden; text-align:center; padding:15px 0; resize:none;" onChange="textarea_change()"></textarea></td></tr>
          </table>
        </td></tr>
      </table>
    </td>
  </tr>
</table>
</div id="content_html">
<div id="table_html"></div>
<table cellpadding="5" cellspacing="0" border="0" width="100%" class="print_none">
  <tr><td height="10" colspan="2"></td></tr>
  <tr>
      <td align="left">
        <a href="javascript:void(0)" onClick="add_empty()"><img src="/includes/languages/japanese/images/z_01.gif"></a>
        <a href="javascript:void(0)" onClick="delete_empty()"><img src="/includes/languages/japanese/images/not.gif"></a>
      </td>
      <td align="right" style="display:block;"><input name="" type="button" value="<?php echo TEXT_PRINT;?>" onClick="create_table(table_data);window.print();"></td>
  </tr>
</table>
</div></body>
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
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script>
<?php //检查所选择的信息?>
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
<?php //检查是否有选择?>
function check_select()
{
  var sel_p_list = document.getElementsByName('oid[]');
  for (var i=0; i<sel_p_list.length; i++) {
    if (sel_p_list[i].checked) {
      document.forms.orders_form.submit();
      break;
    }
  }
  return false;
}
<?php //选择一个?>
function click_one(ele,oid,cid){
  if (ele.checked == true) {
    check_one(oid,cid);
  } else {
    clear_one(oid,cid);
  }
}
<?php //检查一个?>
function check_one(oid,cid){
  $.ajax({
    dataType: 'text',
    url: 'customers_products.php?action=check_one&orders_id='+oid+'&customers_id='+cid
  });
}
<?php //清除一个?>
function clear_one(oid,cid) {
  $.ajax({
    url: 'customers_products.php?action=clear_one&orders_id='+oid+'&customers_id='+cid
  });
}
<?php //全选?>
function check_all(ele,cid) {
  $.ajax({
    url: 'customers_products.php?action=check_all&customers_id='+cid,
    success: function(data) {
      document.getElementById('allcheck').checked = true;
      all_check(false);
    }
  });
}
<?php //清楚所有选择?>
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
<?php 
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = str_replace('&','|||',$belong);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<form action="?action=print&customers_id=<?php echo $_GET['cID'];?>" method="post" name="orders_form" target="_blank">
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table>
    </td>
    <td valign="top"  width="100%"><div class="box_warp"><?php echo $notes;?>   
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE;?></td> 
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT);?></td> 
            </tr>
        </table>
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
                  $product_list_query = tep_db_query("select o.is_gray, op.products_name, op.final_price, op.products_quantity from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and o.orders_id = '".$product_history['orders_id']."'");
                  $i = 1; 
                  $product_list_total = tep_db_num_rows($product_list_query); 
                  while ($product_list_res = tep_db_fetch_array($product_list_query)) {
                    $even = 'dataTableSecondRow';
                    $odd  = 'dataTableRow';
                    if (isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even; 
                    } else {
                      $nowColor = $odd; 
                    }
                    $is_gray_style = '';
                    if ($product_list_res['is_gray'] == '1') {
                      $is_gray_style = ' style="background-color:#AAAAAA;"'; 
                    }
                  ?>
                  <tr class="<?php echo $nowColor;?>"<?php echo $is_gray_style;?>>
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
                      echo str_replace('<','&lt;',$product_history['customers_name']);
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
                <?php
                if (!$product_history_numrows) {
                ?>
                <tr>
                  <td colspan="7">
                  <?php echo TEXT_DATA_EMPTY;?> 
                  </td> 
                </tr>
                <?php
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
                  <input class="element_button" type="button" name="orders_button" onclick="check_select();" value="<?php echo APPLICATION_CREATE_TEXT;?>">
                  <a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, str_replace('cpage', 'page', tep_get_all_get_params(array('page'))));?>"><?php echo tep_html_element_button(IMAGE_BACK);?></a> 
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </div>
        </div>
    </td>
  </tr>
</table>
</form>
<?php require(DIR_WS_INCLUDES.'footer.php');?>
</body>
</html>
