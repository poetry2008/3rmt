<?php // 被orders.php调用?>
var f_flag = 'off';
var old_color = '';
window.status_text  = new Array();
window.status_title = new Array();
window.last_status  = 0;
var auto_submit_able = true;
<?php // 最后检查时间?>
var prev_customer_action = '';

<?php //全选检查 ?>
function all_check(){
  field_on();
  var chk_flag = document.sele_act.all_chk.checked;

  if(chk_flag == true){

    if(document.sele_act.elements["chk[]"].length == null){
      document.sele_act.elements["chk[]"].checked = true;
      var tr_id = 'tr_' + document.sele_act.elements["chk[]"].value;
      if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
        document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
    }else{
      for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
        document.sele_act.elements["chk[]"][i].checked = true;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
        if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
          document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
      }
    }
  }else{
    if(document.sele_act.elements["chk[]"].length == null){
      document.sele_act.elements["chk[]"].checked = false;
      var tr_id = 'tr_' + document.sele_act.elements["chk[]"].value;
      if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
        document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
    }else{
      for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
        document.sele_act.elements["chk[]"][i].checked = false;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
        if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
          document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
      }
    }
  }
}
<?php //保持邮件发送框显示 ?>
function chg_tr_color(aaa){
  field_on();
  var c_flag = aaa.checked;
  var tr_id = 'tr_' + aaa.value;

  <?php // 如果选中?>
  if(c_flag == true){

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      old_color = document.getElementById(tr_id).style.backgroundColor
    }

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = "#F08080";
    }
    <?php // 如果未选中?>
  }else{
    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = old_color;
    }

  }

}

function chg_td_color(bbb){
}

<?php //打开邮件框 ?>
function field_on(){
  if(f_flag == 'off'){
    f_flag = 'on';
    document.getElementById("select_send").style.display = "block";
  }
}
<?php //关闭邮件框 ?>
function field_off(){
  if(f_flag == 'on'){
    f_flag = 'off';
    document.getElementById("select_send").style.display = "none";
  }
}
<?php //传真的颜色 ?>
function fax_over_color(ele){
  old_color = ele.style.backgroundColor
    ele.style.backgroukdColor = "#ffcc99";
}
<?php //传真的颜色 ?>
function fax_over_color(ele){
  ele.style.backgroukdColor = old_color;
}

<?php //订单搜索 ?>
function search_type_changed(elem){
  //if ($('#keywords').val() && elem.selectedIndex != 0) 
  document.forms.orders1.submit();
}
<?php //得到复选框值 ?>
function getCheckboxValue(ccName)
{
  var aa     =   document.getElementsByName(ccName);
  var values = new Array();
  for   (var   i=0;   i<aa.length;   i++){
    if(aa[i].checked){
      values[values.length] = aa[i].value;
    }
  }
  return values;
}
<?php
//st => form中select的name
//tt => form中textarea的name  邮件内容
//ot => form中input的name 邮件标题
?>
<?php //邮件正文 ?>
function mail_text(st,tt,ot){

  <?php // 选中的索引?>
  var idx = document.sele_act.elements[st].selectedIndex;
  <?php // 选中值?>
  var CI  = document.sele_act.elements[st].options[idx].value;
  <?php // 选中的checkbox值?>
  if (st == 'status') {
    <?php // 列表页?>
    chk = getCheckboxValue('chk[]');
  } else {
    <?php // 详细页?>
    chk = new Array();
    chk[0] = 0;
  }


  if((chk.length > 1)  && window.status_text[CI][0].indexOf('${ORDER_A}') != -1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NOT_CHOOSE;?>');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NO_OPTION_ORDER;?>');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  <?php // 记录上一个状态?>
  window.last_status = idx;
  <?php // 更换表单内容?>
  if (st == 'status') {
    <?php // 列表页?>
    if (typeof(window.status_title[CI]) != 'undefined' && typeof(window.status_title[CI][window.orderSite[chk[0]]]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][window.orderSite[chk[0]]];
      document.sele_act.elements[tt].value = window.status_text[CI][window.orderSite[chk[0]]].replace('${ORDER_A}', window.orderStr[chk[0]]);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr[chk[0]]);
    }
  } else {
    <?php // 详细页?>
    if (typeof(window.status_title[CI]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    }
  }
  <?php // 替换${PAY_DATE}?>
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

<?php // 邮件和提醒的checkbox?>
if (nomail[CI] == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}
}


<?php // 当有新订单自动在列表顶部插入一行 ?>
function newOrders(t)
{
  $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=get_new_orders&prev_customer_action='+t,
success: function(text) {
$(text).insertAfter('#orders_list_table tr:eq(0)');
}
});
}

<?php // 验证order comment ajax提交 ?>
function showRequest(formData, jqForm, options) { 
  //var queryString = $.param(formData); 
  return true; 
} 

<?php // 列表右侧的订单信息显示 ?>
function showOrdersInfo(oID,ele,popup_type,param_str){
  param_str = decodeURIComponent(param_str);
  data_str = "oid="+oID+"&"+param_str; 
  if (popup_type == 1) {
    data_str += "&popup=1"; 
    popup_num = 2; 
    ele = ele.parentNode; 
  }
  $.ajax({
type:"POST",
data:data_str,
async:false, 
url: 'ajax_orders.php?action=show_right_order_info',
success: function(msg) {

$('#orders_info_box').html(msg);
if(document.documentElement.clientHeight < document.body.scrollHeight){
offset = ele.offsetTop + ele.offsetHeight + $('#orders_info_box').height() > $('#orders_list_table').height()? ele.offsetTop+$("#orders_list_table").position().top-1-$('#orders_info_box').height()-$('#offsetHeight').height():ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;
$('#orders_info_box').css('top',offset).show();
}else{
if(ele.offsetTop+$("#orders_list_table").position().top+ele.offsetTop + ele.offsetHeight + $('#orders_info_box').height() > document.documentElement.clientHeight){
offset = ele.offsetTop+$("#orders_list_table").position().top-$('#orders_info_box').height()-$('#offsetHeight').height()-1;
$('#orders_info_box').css('top',offset).show();
}else{
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;
$('#orders_info_box').css('top',offset).show();
}
}
}
});

}

<?php // 列表右侧的订单信息隐藏 ?>
function hideOrdersInfo(popup_type){
  if (popup_type == 1) {
    popup_num = 1; 
  }
  $("#orders_info_box").html("");
  $("#orders_info_box").hide();
}

<?php //播放提示音，需要warn_sound ?>
function playSound()  
{  
  var node=document.getElementById('warn_sound');  
  if(node!=null)  
  {  
    if (node.controls) {
      node.controls.play();
    } else {
      node.play();
    }
  }
}
<?php // 当ele选中，则id必须同时被选中 ?>
function auto_radio(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = true;
}
<?php // 当ele被选中，则id取消选择 ?>
function exclude(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = false;
}
var change_option_enable = true;
<?php //更改选项 ?>
function change_option(ele){
  if (change_option_enable) {
    <?php // 自动保存?>
    auto_save_questions();
    <?php // 是否显示按钮?>
    show_submit_button();
  }
}
<?php //更改属性选项 ?>
function propertychange_option(ele){
  change_option_enable = false;
  <?php // 自动保存?>
  auto_save_questions();
  <?php // 是否显示按钮?>
  show_submit_button();
}

<?php // 点击按钮 ?>
function orders_flag(ele, type, oid) {
  if (ele.className == 'orders_flag_checked') {
    $.ajax({
url: 'ajax_orders.php?orders_id='+oid+'&orders_'+type+'_flag=0',
success: function(data) {
ele.className='orders_flag_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_orders.php?orders_id='+oid+'&orders_'+type+'_flag=1',
success: function(data) {
ele.className='orders_flag_checked';
}
});
}
}

<?php // 点击A,B,C ?>
function orders_work(ele, work, oid) {
  document.getElementById('work_a').className = 'orders_flag_unchecked';
  document.getElementById('work_b').className = 'orders_flag_unchecked';
  document.getElementById('work_c').className = 'orders_flag_unchecked';
  $.ajax({
dataType: 'text',
url: 'ajax_orders.php?orders_id='+oid+'&work='+work,
success: function(data) {
if (data == 'success') {
if (ele.className == 'orders_flag_checked') {
ele.className='orders_flag_unchecked';
} else {
ele.className='orders_flag_checked';
}
}
}
});
}
<?php // 点击按钮动作 ?>
function orders_buttons(ele, cid, oid) {
  if (ele.className == 'orders_buttons_checked') {
    $.ajax({
url: 'ajax_orders.php?action=delete&orders_id='+oid+'&buttons_id='+cid,
success: function(data) {
ele.className='orders_buttons_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_orders.php?action=insert&orders_id='+oid+'&buttons_id='+cid,
success: function(data) {
ele.className='orders_buttons_checked';
}
});
}
}

<?php // 清除选项 ?>
function clean_option(n,oid){
  <?php // 自动保存?>
  $.ajax({ url: "ajax_orders.php?orders_id="+oid+"&action=clean_option&questions_no="+n, success: function(){}});
  <?php // 是否显示按钮?>
  show_submit_button();
}

<?php // 是否显示批量问答框?>
var order_payment_type = '';
var order_buy_type = '';
var form_id = '';
var ids = '';
var order_can_end = 1;
var lastid = '';
<?php //显示问题 ?>
function show_questions(ele){
    ids = '';
    lastid = ele.value;
    show = true;
    if($(".dataTableContent").find('input|[type=checkbox][checked]').length==0){
	show = false;
	show_questiondiv(show)
	return true;
    }
    if(show){
    setTimeout(function(){
	$(".dataTableContent").find('input|[type=checkbox][checked]').each(
	    function(key){
		oid =  $(this).val();
		ids += oid+'_';
	    }
	);		     
	$.ajax({ url: "ajax_orders.php?action=get_oa_type",
		 type:'post',
		 beforeSend: function(jqXHR,settings){
		     if(lastid!=ele.value){
			 show_questiondiv(false)
			 return false;
		     }
		     return true;
		 },
		 data:'oid='+ids,
		 success: function(msg){
		     var oamsg = msg.split("_");
		     if(oamsg.length>1){
			 show = true;
			 order_payment_type = oamsg[0];
			 order_buy_type = oamsg[1];
			 order_can_end =oamsg[2];
		     }else {
			 show =false;
		     }
		     show_questiondiv(show);
		 }});},1000);
    }
    return true;
}
<?php //显示问题的DIV ?>
    function show_questiondiv(show){

    if(show){
        $('#oa_dynamic_groups').html('');
	$('#oa_dynamic_group_item').html('');
	$.ajax({ url: "ajax_orders.php?payment="+order_payment_type+"&buytype="+order_buy_type+"&action=get_oa_groups", success: function(msg){
	    var oa_groupsobj =  eval("("+msg+")");
	    var oa_groups = oa_groupsobj.split('_');;
	    $("#oa_dynamic_groups").find('option').remove();<?php //删除以前数据?> 
	    $("#oa_dynamic_groups")[0].options.add(new Option('----', '-1', true));
	    for (var groupstring in oa_groups){
		if(oa_groups[groupstring]==''){
		    continue;
		}
		group = oa_groups[groupstring].split('|');
		group_name = group[0];
		group_id = group[1];
		form_id = group[2];
		$("#oa_dynamic_groups")[0].options.add(new Option(''+group_name+'',group_id,true,false));
	    }
	    if(order_can_end=='1'){
		$("#oa_dynamic_groups")[0].options.add(new Option('<?php echo JS_TEXT_ALL_ORDERS_COMPLETION_TRANSACTION;?>','end',true,false));
	    }
	}});
	$("#oa_dynamic_groups").unbind('change');
	$("#oa_dynamic_groups").change(function(){

	    if($(this).selected().val()=='-1'){
		$('#oa_dynamic_group_item').html('');
		$("#oa_dynamic_submit").unbind('click');
		$("#oa_dynamic_submit").hide();
		return true;
	    }
	    if($(this).selected().val()=='end'){
		$("#oa_dynamic_submit").show();
		$("#oa_dynamic_submit").html('<?php echo JS_TEXT_ALL_ORDERS_COMPLETION_TRANSACTION;?>');
		msg = '<input type="hidden" id="endtheseorder" value="1"/>';
		$("#oa_dynamic_group_item").html(msg);
	    }else{
		$("#oa_dynamic_submit").show();
		$("#oa_dynamic_submit").html('<?php echo JS_TEXT_ALL_ORDERS_SAVE;?>');
	    $.ajax(
		{ 
		    url: "ajax_orders.php?group_id="+$(this).val()+"&action=get_group_renderstring", 
		    type:"GET",
		    data:"ids="+ids,
		    success: function(msg){	      
			$("#oa_dynamic_group_item").html($(msg));
		    }});
	    }
	    
	});
	$('#select_question').show();
}else{
  $('#oa_dynamic_groups').html('');
  $('#oa_dynamic_group_item').html('');
  $('#select_question').hide();
}
$("#oa_dynamic_submit").unbind('click');
$("#oa_dynamic_submit").click(function(){
    if($("#endtheseorder").val()==1){
    var finish = 1;
    urloa = 'oa_ajax.php?action=finish';
    }else{
    urloa = 'oa_answer_process.php?action=muliUpdateOa';
    }
    longstring = '';
    $("#oa_dynamic_group_item").find('input').each(function(key){
      if(key>0){
      longstring+='&';
      }
      longstring+=$(this).attr('name') +'='+$(this).val();
      });
    longstring+='&oID='+ids;
    longstring+='&form_id='+form_id;
    longstring+='&finish=1';
    $.ajax(
      {
	  type:"POST",
	  async:false,
	  data:longstring,
	  url: urloa,
	  success:function(){
	      if (finish == 1){
		  window.location.reload();
	      }else {
		  alert($("#oa_dynamic_groups").find('option|[selected]').text()+'<?php echo JS_TEXT_ALL_ORDERS_SAVED;?>');
	      }
	  }
      }
    );
    return false;
    });
}



<?php // 点击关联商品前的checkbox ?>
function click_relate(pid,ele){
  <?php // 增加库存?>
  if ($(ele).parent().parent().find('#checkbox_'+pid).attr('checked')) {
    $(ele).parent().parent().find('#offset_'+pid).attr('readonly', true);
    $.ajax({
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count='+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
} else {
  <?php // 减库存?>
  $(ele).parent().parent().find('#offset_'+pid).attr('readonly', false);
  $.ajax({
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count=-'+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
}
}

<?php // 清空库存输入框 ?>
function clear_quantity(){
  $('#relate_products_box input[type=checkbox]').each(function(){
      if ($(this).attr('checked')) {
      $(this).attr('checked', '');
      $(this).click();
      $(this).attr('checked', '');
      }
      });
}

<?php // 计算增加的库存数并实时显示 ?>
function print_quantity(pid){
  $('#relate_product_'+pid).html($('#quantity_'+pid).html()-$('#offset_'+pid).val())
}
<?php //复制到剪切板 ?>
function copyToClipboard(txt) {   
  if(window.clipboardData) {   
    window.clipboardData.clearData();   
    window.clipboardData.setData("Text", txt);   
  } else if(navigator.userAgent.indexOf("Opera") != -1) {   
    window.location = txt;   
  } else if (window.netscape) {   
    try {   
      netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");   
    } catch (e) {   
      alert("<?php echo JS_TEXT_ALL_ORDERS_BROWER_REJECTED;?>");   
    }   
    var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);   
    if (!clip)   
      return;   
    var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);   
    if (!trans)   
      return;   
    trans.addDataFlavor('text/unicode');   
    var str = new Object();   
    var len = new Object();   
    var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);   
    var copytext = txt;   
    str.data = copytext;   
    trans.setTransferData("text/unicode",str,copytext.length*2);   
    var clipid = Components.interfaces.nsIClipboard;   
    if (!clip)   
      return false;   
    clip.setData(trans,null,clipid.kGlobalClipboard);   

  }   
  alert("<?php echo JS_TEXT_ALL_ORDERS_COPY_TO_CLIPBOARD;?>")   
}  
<?php //显示监视器的错误 ?>
function show_monitor_error(e_id,flag,_this){
  <?php //改变DIV?>
  if(flag){
    allt(_this,e_id);
  }else{
    document.getElementById(e_id).style.display="none";
  }
}
<?php //获取ID ?>
function obj_obj(obj){
  return typeof(obj)=="string"?document.getElementById(obj):obj;
}
<?php //添加属性 ?>
function allt(id,div_id){ 
  <?php //div 赋值?>
  e=obj_obj(id) 
    var et=e.offsetTop; 
  var el=e.offsetLeft; 
  while(e=e.offsetParent){ 
    et+=e.offsetTop; 
    el+=e.offsetLeft; 
  } 
  div_e = obj_obj(div_id);
  div_e.style.width="300px";
  div_e.style.left=(window.screen.availWidth-320) + "px"; 
  div_e.style.top=(et+20) + "px"; 
  div_e.style.display=''; 
} 
<?php //定义密码新的URL ?>
function once_pwd_redircet_new_url(url_str){
  //window.location.href = url_str;
  $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd, pwd_arr)){
window.location.href = url_str+'&once_pwd='+pwd; 
} else {
window.alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>"); 
}
}
});
}
<?php //新的邮件订单  ?>
function new_mail_text_orders(ele,st,tt,ot){
  <?php // 选中的索引?>
  var idx = document.edit_order.elements[st].selectedIndex;
  <?php // 选中值?>
  var CI  = document.edit_order.elements[st].options[idx].value;
  <?php // 选中的checkbox值?>
  if (st == 'status') {
    <?php // 列表页?>
    chk = getCheckboxValue('chk[]');
  } else {
    <?php // 详细页?>
    chk = new Array();
    chk[0] = 0;
  }


  if((chk.length > 1)  && window.status_text[CI][0].indexOf('${ORDER_A}') != -1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NOT_CHOOSE;?>');
    document.edit_order.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NO_OPTION_ORDER;?>');
    document.edit_order.elements[st].options[window.last_status].selected = true;
    return false;
  }
  <?php // 记录上一个状态?>
  window.last_status = idx;
  <?php // 更换表单内容?>
  if (st == 'status') {
    <?php // 列表页?>
    if (typeof(window.status_title[CI]) != 'undefined' && typeof(window.status_title[CI][window.orderSite[chk[0]]]) != 'undefined') {
      document.edit_order.elements[ot].value = window.status_title[CI][window.orderSite[chk[0]]];
      document.edit_order.elements[tt].value = window.status_text[CI][window.orderSite[chk[0]]].replace('${ORDER_A}', window.orderStr[chk[0]]);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.edit_order.elements[ot].value = window.status_title[CI][0];
      document.edit_order.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr[chk[0]]);
    }
  } else {
    <?php // 详细页?>
    if (typeof(window.status_title[CI]) != 'undefined') {
      document.edit_order.elements[ot].value = window.status_title[CI][0];
      document.edit_order.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.edit_order.elements[ot].value = window.status_title[CI][0];
      document.edit_order.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    }
  }
  <?php // 替换${PAY_DATE}?>
  if(document.edit_order.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.edit_order.elements[tt].value = document.edit_order.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

<?php // 邮件和提醒的checkbox?>
if (nomail[CI] == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}

if ($(ele).val() == 20) {
  $('#notify').attr('checked', false);  
}
}

<?php //新的邮件文本 ?>
function new_mail_text(ele,st,tt,ot){
  <?php // 选中的索引?>
  var idx = document.sele_act.elements[st].selectedIndex;
  <?php // 选中值?>
  var CI  = document.sele_act.elements[st].options[idx].value;
  <?php // 选中的checkbox值?>
  if (st == 'status') {
    <?php // 列表页?>
    chk = getCheckboxValue('chk[]');
  } else {
    <?php // 详细页?>
    chk = new Array();
    chk[0] = 0;
  }


  if((chk.length > 1)  && window.status_text[CI][0].indexOf('${ORDER_A}') != -1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NOT_CHOOSE;?>');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert('<?php echo JS_TEXT_ALL_ORDERS_NO_OPTION_ORDER;?>');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  <?php // 记录上一个状态?>
  window.last_status = idx;
  <?php // 更换表单内容?>
  if (st == 'status') {
    <?php // 列表页?>
    if (typeof(window.status_title[CI]) != 'undefined' && typeof(window.status_title[CI][window.orderSite[chk[0]]]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][window.orderSite[chk[0]]];
      document.sele_act.elements[tt].value = window.status_text[CI][window.orderSite[chk[0]]].replace('${ORDER_A}', window.orderStr[chk[0]]);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr[chk[0]]);
    }
  } else {
    <?php // 详细页?>
    if (typeof(window.status_title[CI]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    }
  }
  <?php // 替换${PAY_DATE}?>
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

<?php // 邮件和提醒的checkbox?>
if (nomail[CI] == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}

if ($(ele).val() == 20) {
  $('#notify').attr('checked', false);  
}
}
<?php //预约标志 ?>
function preorders_flag(ele, type, oid) {
  if (ele.className == 'orders_flag_checked') {
    $.ajax({
url: 'ajax_preorders.php?orders_id='+oid+'&orders_'+type+'_flag=0',
success: function(data) {
ele.className='orders_flag_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_preorders.php?orders_id='+oid+'&orders_'+type+'_flag=1',
success: function(data) {
ele.className='orders_flag_checked';
}
});
}
}
<?php //预约工作 ?>
function preorders_work(ele, work, oid) {
  document.getElementById('work_a').className = 'orders_flag_unchecked';
  document.getElementById('work_b').className = 'orders_flag_unchecked';
  document.getElementById('work_c').className = 'orders_flag_unchecked';
  $.ajax({
dataType: 'text',
url: 'ajax_preorders.php?orders_id='+oid+'&work='+work,
success: function(data) {
if (data == 'success') {
if (ele.className == 'orders_flag_checked') {
ele.className='orders_flag_unchecked';
} else {
ele.className='orders_flag_checked';
}
}
}
});
}
<?php //预约的订单按钮检查 ?>
function preorders_buttons(ele, cid, oid) {
  if (ele.className == 'orders_buttons_checked') {
    $.ajax({
url: 'ajax_preorders.php?action=delete&orders_id='+oid+'&buttons_id='+cid,
success: function(data) {
ele.className='orders_buttons_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_preorders.php?action=insert&orders_id='+oid+'&buttons_id='+cid,
success: function(data) {
ele.className='orders_buttons_checked';
}
});
}
}
<?php //显示前台的订单信息 ?>
function showPreOrdersInfo(oID,ele){
  $.ajax({
type:"POST",
data:"oid="+oID,
async:false, 
url: 'ajax_orders.php?action=show_right_preorder_info',
success: function(msg) {

$('#orders_info_box').html(msg);
offset = ele.offsetTop + $('#orders_info_box').height() > $('#orders_list_table').height()
? ele.offsetTop+$("#orders_list_table").position().top - $('#orders_info_box').height() 
:ele.offsetTop+$("#orders_list_table").position().top;
$('#orders_info_box').css('top',offset).show();
}
});
}
$(document).ready(function(){
    $(".dataTableContent").find("input|[type=checkbox][checked]").parent().parent().each(function(){
      if($(this).attr('class')!='dataTableRowSelected'){$(this).attr('style','background-color: rgb(240, 128, 128);')}})
    });
<?php //删除订单信息 ?>
function delete_order_info(oID, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
type:"POST",
data:'oID='+oID+'&'+param_str,
async:false, 
url: 'ajax_orders.php?action=show_del_info',
success: function(msg) {
  $('#order_del').html(msg);
}
});
}
<?php //取消删除订单信息 ?>
function cancel_del_order_info(oID, param_str)
{
  param_str = decodeURIComponent(param_str);
$.ajax({
type:"POST",
data:'oID='+oID+'&'+param_str,
async:false, 
url: 'ajax_orders.php?action=cancel_del_info',
success: function(msg) {
  $('#order_del').html(msg);
}
});
}

function fmoney(s)
{
   s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(0) + "";
    var l = s.split(".")[0].split("").reverse();
     var t = '';
      for(i = 0; i < l.length; i ++ ){
            t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
              }
       return t.split("").reverse().join("");
}

<?php //重新计算订单价格 ?>
function recalc_order_price(oid, opd, o_str, op_str,opd_str)
{
  var op_array = op_str.split('|||');
  var p_op_info = 0; 
  var op_string = '';
  var op_string_title = '';
  var op_string_val = '';
  for (var i=0; i<op_array.length; i++) {
    if (op_array[i] != '') {
      p_op_info += parseInt(document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][price]')[0].value); 
      p_op_info_value = parseInt(document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][price]')[0].value);
      op_string += p_op_info_value+'|||';
      p_op_info_title = document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][option]')[0].value;
      op_string_title += p_op_info_title+'|||';
      p_op_info_val = document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][value]')[0].value;
      op_string_val += p_op_info_val+'|||';
    } 
  }
  pro_num = document.getElementById('update_products_new_qty_'+opd).value;
  pro_num = pro_num.replace(/\s/g,"");
  if(pro_num == ''){

    pro_num = 0;
  }
  p_price = document.getElementsByName('update_products['+opd+'][p_price]')[0].value;
  p_final_price = document.getElementsByName('update_products['+opd+'][final_price]')[0].value;
  
  $.ajax({
    type: "POST",
    data:'oid='+oid+'&opd='+opd+'&o_str='+o_str+'&op_price='+p_op_info+'&p_num='+pro_num+'&p_price='+p_price+'&p_final_price='+p_final_price+'&op_str='+op_str+'&op_string='+op_string+'&op_string_title='+op_string_title+'&op_string_val='+op_string_val+'&orders_id='+session_orders_id,
    async:false,
    url: 'ajax_orders.php?action=recalc_price',
    success: function(msg) { 
      msg_info = msg.split('|||');
      if(o_str != 3){
        document.getElementsByName('update_products['+opd+'][final_price]')[0].value = msg_info[0];
        document.getElementById('update_products['+opd+'][final_price]').innerHTML = msg_info[11];
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[1];
      }else{
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[7]; 
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[2];
      }else{
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[8]; 
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[3];
      }else{
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[9]; 
      }
      var opd_str_array = opd_str.split('|||');
      var opd_str_value = '';
      var opd_str_total = 0;
      var opd_str_temp = '';
      for(x in opd_str_array){
        opd_str_temp = ''; 
        opd_str_value = document.getElementById('update_products['+opd_str_array[x]+'][c_price]').innerHTML;
        opd_str_temp = opd_str_value;
        opd_str_value = opd_str_value.replace(/<.*?>/g,'');
        opd_str_value = opd_str_value.replace(/,/g,'');
        opd_str_value = opd_str_value.replace(msg_info[10],'');
        opd_str_value = parseFloat(opd_str_value);
        if(opd_str_temp.indexOf('color') > 0){
          opd_str_total -= opd_str_value;
        }else{
          opd_str_total += opd_str_value; 
        }
      }
      var ot_total = '';
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace(msg_info[10],'');
      handle_fee_id = parseInt(handle_fee_id); 
      var shipping_fee_id = document.getElementById('shipping_fee_id').innerHTML;
      shipping_fee_id= shipping_fee_id.replace(/<.*?>/g,'');
      shipping_fee_id = shipping_fee_id.replace(/,/g,'');
      shipping_fee_id = shipping_fee_id.replace(msg_info[10],'');
      shipping_fee_id = parseInt(shipping_fee_id); 
      if(document.getElementById('point_id')){
        var point_id = document.getElementById('point_id').value; 
      }else{
        var point_id = 0; 
      }
      var update_total_temp;
      var update_total_num = 0;
      var sum_num = document.getElementById('button_add_id').value;
      for(var i = 1;i <= sum_num;i++){
     
        if(document.getElementById('update_total_'+i)){
          update_total_temp = document.getElementById('update_total_'+i).value; 
          if(update_total_temp == ''){update_total_temp = 0;}
          update_total_temp = parseInt(update_total_temp);
          update_total_num += update_total_temp;
        }
      }
 
      ot_total = opd_str_total+handle_fee_id+shipping_fee_id-point_id+update_total_num;
      
      if(opd_str_total < 0){
        opd_str_total = Math.abs(opd_str_total);
        document.getElementById('ot_subtotal_id').innerHTML = '<font color="#FF0000">'+fmoney(opd_str_total)+'</font>'+msg_info[10];
      }else{
        document.getElementById('ot_subtotal_id').innerHTML = fmoney(opd_str_total)+msg_info[10]; 
      }
      if(ot_total < 0){
        ot_total = Math.abs(ot_total);
        ot_total -= point_id;
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font>'+msg_info[10];
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+msg_info[10]; 
      }
      document.getElementById('update_products['+opd+'][ah_price]').value = msg_info[4];
      document.getElementById('update_products['+opd+'][bh_price]').value = msg_info[5];
      document.getElementById('update_products['+opd+'][ch_price]').value = msg_info[6];
    }
  });
}
<?php //产品的总价格 ?>
function price_total(str)
{
      var ot_total = '';
      var ot_total_flag = false;
      var ot_subtotal_id = document.getElementById('ot_subtotal_id').innerHTML; 
      if(ot_subtotal_id.indexOf('color') > 0){
        ot_total_flag = true; 
      }
      ot_subtotal_id = ot_subtotal_id.replace(/<.*?>/g,'');
      ot_subtotal_id = ot_subtotal_id.replace(/,/g,'');
      ot_subtotal_id = ot_subtotal_id.replace(str,'');
      ot_subtotal_id= parseInt(ot_subtotal_id);
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace(str,'');
      handle_fee_id = parseInt(handle_fee_id); 
      var shipping_fee_id = document.getElementById('shipping_fee_id').innerHTML;
      shipping_fee_id= shipping_fee_id.replace(/<.*?>/g,'');
      shipping_fee_id = shipping_fee_id.replace(/,/g,'');
      shipping_fee_id = shipping_fee_id.replace(str,'');
      shipping_fee_id = parseInt(shipping_fee_id); 
      if(document.getElementById('point_id')){
        if(document.getElementById('point_value_temp')){
          var point_id = 0; 
        }else{
          var point_id = document.getElementById('point_id').value;
        }
      }else{
        var point_id = 0;
      }
      var update_total_temp;
      var update_total_num = 0;
      var sum_num = document.getElementById('button_add_id').value;
      var total_value = '';
      var total_key = '';
      var total_title_temp = '';
      var total_title = '';
      var temp_flag = false;
      var sign = '';
      for(var i = 1;i <= sum_num;i++){
     
        if(document.getElementById('update_total_'+i)){
          update_total_temp = document.getElementById('update_total_'+i).value; 
          sign = document.getElementById('sign_'+i).value; 
          if(update_total_temp == ''){update_total_temp = 0;temp_flag = true;}
          if(update_total_temp == '-'){update_total_temp = 0;}
          update_total_temp = parseInt(update_total_temp);
          update_total_temp = sign == '0' ? 0-update_total_temp : update_total_temp;
          update_total_num += update_total_temp;
          if(temp_flag == true){update_total_temp = '';temp_flag == false}
          total_value += update_total_temp+'|||';
          total_key += i+'|||';
          total_title_temp = document.getElementsByName('update_totals['+i+'][title]')[0].value;
          total_title += total_title_temp+'|||';
        }
      }
      var ot_subtotal_id_temp;
      if(ot_total_flag == false){
        ot_total = ot_subtotal_id+handle_fee_id+shipping_fee_id+update_total_num;
        ot_subtotal_id_temp = ot_subtotal_id;
      }else{
        ot_total = handle_fee_id+shipping_fee_id+update_total_num-ot_subtotal_id; 
        ot_subtotal_id_temp = 0-ot_subtotal_id;
      }
      if(ot_subtotal_id_temp > 0){
        ot_total -= point_id;
      }
      var ot_total_temp;
      ot_total_temp = ot_total;
       
  var payment_value = document.getElementsByName('payment_method')[0].value; 
  $.ajax({
    type: "POST",
    data: 'total_title='+total_title+'&total_value='+total_value+'&point_value='+point_id+'&total_key='+total_key+'&ot_total='+ot_total_temp+'&ot_subtotal='+ot_subtotal_id_temp+'&payment_value='+payment_value+'&orders_id='+session_orders_id+'&session_site_id='+session_site_id+'&handle_fee='+handle_fee_id+'&fee_total='+update_total_num+'&shipping_fee_id='+shipping_fee_id,
    async:false,
    url: 'ajax_orders.php?action=price_total',
    success: function(msg) {
     var msg_array = new Array();
     msg_array = msg.split('|||');
     var handle_fee = parseInt(msg_array[0]);
     var campaign_fee =  Math.abs(parseInt(msg_array[1]));
     var campaign_flag = msg_array[2];
     var shipping_fee = parseInt(msg_array[3]);
     if(campaign_flag == 1){
       document.getElementById('point_id').value = campaign_fee;
     }
     document.getElementById('handle_fee_id').innerHTML = handle_fee+str;
     document.getElementsByName('payment_code_fee')[0].value = handle_fee; 
     document.getElementById('shipping_fee_id').innerHTML = shipping_fee+str;
     if(document.getElementsByName('shipping_fee_num')[0]){
       document.getElementsByName('shipping_fee_num')[0].value = shipping_fee;
     }
     ot_total = ot_total-handle_fee_id+handle_fee-campaign_fee;
     ot_total = ot_total-shipping_fee_id+shipping_fee;
     if(ot_total < 0){ 
        ot_total = Math.abs(ot_total);
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font>'+str;
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+str; 
      }
    }
  });
}

<?php //重新计算所有产品的价格 ?>
function recalc_all_product_price(oid, or_str)
{
  p_info = or_str.split('|||');
 
  o_str = '';
  for (i=0; i<p_info.length; i++) {
    j = 0; 
    $('#ctable').find('input').each(function() {
      if ($(this).attr('type') == 'text') {
        regex = 'update_products['+p_info[i]+'][attributes]'; 
        regex_p = 'update_products['+p_info[i]+'][p_price]';
        regex_n = 'update_products['+p_info[i]+'][qty]';
        
        if ($(this).attr('name').indexOf(regex) == 0) {
          regex_o = '[price]';
          if ($(this).attr('name').indexOf(regex_o) > 0) {
            o_str += $(this).attr('name')+'='+$(this).val()+'&';  
          }
        }
        
        if ($(this).attr('name').indexOf(regex_p) == 0) {
          o_str += $(this).attr('name')+'='+$(this).val()+'&';  
        }  
        
        if ($(this).attr('name').indexOf(regex_n) == 0) {
          o_str += $(this).attr('name')+'='+$(this).val()+'&';  
        }  
        
        j++; 
      } 
   }); 
  }
   
  $.ajax({
    type: "POST",
    data:o_str+'op_i='+or_str+'&oid='+oid,
    async:false,
    url: 'ajax_orders.php?action=recalc_all_price',
    success: function(msg) {
      msg_array = msg.split('|||');
      for (m=0; m<msg_array.length; m++) {
        mp_array = msg_array[m].split(':::');
        op_id = mp_array[0];
        sp_array = mp_array[1].split('<<<');
        
        document.getElementsByName('update_products['+op_id+'][final_price]')[0].value = sp_array[0];
        document.getElementById('update_products['+op_id+'][a_price]').innerHTML = sp_array[1];
        document.getElementById('update_products['+op_id+'][b_price]').innerHTML = sp_array[2];
        document.getElementById('update_products['+op_id+'][c_price]').innerHTML = sp_array[3];
        
      }
    }
  }); 
}
<?php //删除产品 ?>
function delete_products(opid,o_str,delete_flag){

       document.getElementById('update_products_new_qty_'+opid).value = 0;
       var ot_total_flag = false;
       var ot_subtotal_id = document.getElementById('ot_subtotal_id').innerHTML; 
       if(ot_subtotal_id.indexOf('color') > 0){
         ot_total_flag = true; 
       }
       ot_subtotal_id = ot_subtotal_id.replace(/<.*?>/g,'');
       ot_subtotal_id = ot_subtotal_id.replace(/,/g,'');
       ot_subtotal_id = ot_subtotal_id.replace(o_str,'');
       ot_subtotal_id= parseInt(ot_subtotal_id);
       var ot_products_flag = false;
       var ot_products_id = document.getElementById('update_products['+opid+'][c_price]').innerHTML;
       if(ot_products_id.indexOf('color') > 0){
         ot_products_flag = true; 
       }
       ot_products_id = ot_products_id.replace(/<.*?>/g,'');
       ot_products_id = ot_products_id.replace(/,/g,'');
       ot_products_id = ot_products_id.replace(o_str,'');
       ot_products_id= parseInt(ot_products_id); 
       if(ot_total_flag == true && ot_products_flag == true){
         opd_str_total = ot_subtotal_id-ot_products_id; 
         opd_str_total = 0-opd_str_total;
       }
       if(ot_total_flag == true && ot_products_flag == false){
         opd_str_total = ot_subtotal_id+ot_products_id; 
         opd_str_total = 0-opd_str_total;
       }
       if(ot_total_flag == false && ot_products_flag == true){
         opd_str_total = ot_subtotal_id+ot_products_id; 
       }
       if(ot_total_flag == false && ot_products_flag == false){
         opd_str_total = ot_subtotal_id-ot_products_id; 
       } 
       if(opd_str_total < 0){
        opd_str_total = Math.abs(opd_str_total);
        document.getElementById('ot_subtotal_id').innerHTML = '<font color="#FF0000">'+fmoney(opd_str_total)+'</font>'+o_str;
      }else{
        document.getElementById('ot_subtotal_id').innerHTML = fmoney(opd_str_total)+o_str; 
      }
      document.getElementById('update_products['+opid+'][b_price]').innerHTML = '0'+o_str; 
      document.getElementById('update_products['+opid+'][c_price]').innerHTML = '0'+o_str; 
      price_total(o_str);
}
<?php //订单会话 ?>
function orders_session(type,value){
  
  $.ajax({
    type: "POST",
    data: 'orders_session_type='+type+'&orders_session_value='+value+'&orders_id='+session_orders_id,
    async:false,
    url: 'ajax_orders.php?action=orders_session',
    success: function(msg) {
      
    }
  });
}
