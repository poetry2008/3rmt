// 被orders.php调用
var f_flag = 'off';
var old_color = '';
window.status_text  = new Array();
window.status_title = new Array();
window.last_status  = 0;
var auto_submit_able = true;
// 最后检查时间
var prev_customer_action = '';

// 全选
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

function chg_tr_color(aaa){
  // 保持邮件发送框显示
  field_on();
  var c_flag = aaa.checked;
  var tr_id = 'tr_' + aaa.value;

  // 如果选中
  if(c_flag == true){

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      old_color = document.getElementById(tr_id).style.backgroundColor
    }

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = "#F08080";
    }
    // 如果未选中
  }else{
    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = old_color;
    }

  }

}

function chg_td_color(bbb){
}

// 打开邮件框
function field_on(){
  if(f_flag == 'off'){
    f_flag = 'on';
    document.getElementById("select_send").style.display = "block";
  }
}
// 关闭邮件框
function field_off(){
  if(f_flag == 'on'){
    f_flag = 'off';
    document.getElementById("select_send").style.display = "none";
  }
}

function fax_over_color(ele){
  old_color = ele.style.backgroundColor
    ele.style.backgroukdColor = "#ffcc99";
}
function fax_over_color(ele){
  ele.style.backgroukdColor = old_color;
}

// 订单搜索
function search_type_changed(elem){
  //if ($('#keywords').val() && elem.selectedIndex != 0) 
  document.forms.orders1.submit();
}

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

//st => form中select的name
//tt => form中textarea的name  邮件内容
//ot => form中input的name 邮件标题
function mail_text(st,tt,ot){

  // 选中的索引
  var idx = document.sele_act.elements[st].selectedIndex;
  // 选中值
  var CI  = document.sele_act.elements[st].options[idx].value;
  // 选中的checkbox值
  if (st == 'status') {
    // 列表页
    chk = getCheckboxValue('chk[]');
  } else {
    // 详细页
    chk = new Array();
    chk[0] = 0;
  }

  // 如果有了游戏人物名则不允许多选

  if((chk.length > 1)  && window.status_text[CI][0].indexOf('${ORDER_A}') != -1){
    alert('複数の選択はできません。');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert('注文書はまだ選択していません。');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  // 记录上一个状态
  window.last_status = idx;
  // 更换表单内容
  if (st == 'status') {
    // 列表页
    if (typeof(window.status_title[CI]) != 'undefined' && typeof(window.status_title[CI][window.orderSite[chk[0]]]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][window.orderSite[chk[0]]];
      document.sele_act.elements[tt].value = window.status_text[CI][window.orderSite[chk[0]]].replace('${ORDER_A}', window.orderStr[chk[0]]);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr[chk[0]]);
    }
  } else {
    // 详细页
    if (typeof(window.status_title[CI]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    }
  }
  // 替换${PAY_DATE}
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

// 邮件和提醒的checkbox
if (nomail[CI] == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}
}


// 当有新订单自动在列表顶部插入一行
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

// 验证order comment ajax提交
function showRequest(formData, jqForm, options) { 
  //var queryString = $.param(formData); 
  return true; 
} 

// 列表右侧的订单信息显示
function showOrdersInfo(oID,ele){

  $.ajax({
type:"POST",
data:"oid="+oID,
async:false, 
url: 'ajax_orders.php?action=show_right_order_info',
success: function(msg) {

$('#orders_info_box').html(msg);
if(document.documentElement.clientHeight < document.body.scrollHeight){
offset = ele.offsetTop + ele.offsetHeight + $('#orders_info_box').height() > $('#orders_list_table').height()? ele.offsetTop+$("#orders_list_table").position().top-$('#tep_site_filter').height()-$('#orders_info_box').height()-$('#offsetHeight').height():ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;
$('#orders_info_box').css('top',offset).show();
}else{
if(ele.offsetTop+$("#orders_list_table").position().top+ele.offsetTop + ele.offsetHeight + $('#orders_info_box').height() > document.documentElement.clientHeight){
offset = ele.offsetTop+$("#orders_list_table").position().top-$('#orders_info_box').height()-$('#offsetHeight').height()-ele.offsetHeight;
$('#orders_info_box').css('top',offset).show();
}else{
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;
$('#orders_info_box').css('top',offset).show();
}
}
}
});

}

// 列表右侧的订单信息隐藏
function hideOrdersInfo(){
  $('#orders_info_box').html('');
  $('#orders_info_box').hide();
}

//播放提示音，需要warn_sound
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
// 当ele选中，则id必须同时被选中
function auto_radio(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = true;
}
// 当ele被选中，则id取消选择
function exclude(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = false;
}
var change_option_enable = true;
function change_option(ele){
  if (change_option_enable) {
    // 自动保存
    auto_save_questions();
    // 是否显示按钮
    show_submit_button();
  }
}
function propertychange_option(ele){
  change_option_enable = false;
  // 自动保存
  auto_save_questions();
  // 是否显示按钮
  show_submit_button();
}

$(function(){
    // 每分钟检查状态是否有修改
    setTimeout(function(){checkChange()}, 60000);
    });
// 每分钟自动检查最新订单和修改
function checkChange(){
  $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=last_customer_action',
success: function(last_customer_action) {
if (
  last_customer_action != cfg_last_customer_action 
  && prev_customer_action != last_customer_action
  ){
// 如果有新订单和修改
// 改变背景颜色
$('body').css('background-color', '#ffcc99');// rgb(255, 204, 153)
// 在列表插入新订单
newOrders(prev_customer_action != '' ? prev_customer_action : cfg_last_customer_action);
// 修改最后检查时间
prev_customer_action = last_customer_action;
// 播放提示音
playSound();
}
}
});
setTimeout(function(){checkChange()}, 60000);
}

// 点击按钮
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

// 点击A,B,C
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

// 点击PC号码
function orders_computers(ele, cid, oid) {
  if (ele.className == 'orders_computer_checked') {
    $.ajax({
url: 'ajax_orders.php?action=delete&orders_id='+oid+'&computers_id='+cid,
success: function(data) {
ele.className='orders_computer_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_orders.php?action=insert&orders_id='+oid+'&computers_id='+cid,
success: function(data) {
ele.className='orders_computer_checked';
}
});
}
}

// 清楚选项
function clean_option(n,oid){
  // 自动保存
  // auto_save_questions();
  $.ajax({ url: "ajax_orders.php?orders_id="+oid+"&action=clean_option&questions_no="+n, success: function(){}});
  // 是否显示按钮
  show_submit_button();
}

// 是否显示批量问答框
var order_payment_type = '';
var order_buy_type = '';
var form_id = '';
var ids = '';
var order_can_end = 1;
var lastid = '';
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
    function show_questiondiv(show){
    if(show){
        $('#oa_dynamic_groups').html('');
	$('#oa_dynamic_group_item').html('');
	$.ajax({ url: "ajax_orders.php?payment="+order_payment_type+"&buytype="+order_buy_type+"&action=get_oa_groups", success: function(msg){
	    var oa_groupsobj =  eval("("+msg+")");
	    var oa_groups = oa_groupsobj.split('_');;
	    $("#oa_dynamic_groups").find('option').remove();//删除以前数据 
	    $("#oa_dynamic_groups")[0].options.add(new Option('----', '-1', true));
	    for (var groupstring in oa_groups){
		if(oa_groups[groupstring]==''){
		    continue;
		}
		group = oa_groups[groupstring].split('|');
		group_name = group[0];
		group_id = group[1];
		form_id = group[2];
//		$("#oa_dynamic_groups")[0].options.add(new Option(group_name,group_id,true,false));
		$("#oa_dynamic_groups")[0].options.add(new Option(''+group_name+'',group_id,true,false));
	    }
	    if(order_can_end=='1'){
		$("#oa_dynamic_groups")[0].options.add(new Option('取引完了','end',true,false));
	    }
	}});
	$("#oa_dynamic_groups").unbind('change');
	$("#oa_dynamic_groups").change(function(){

	    if($(this).selected().val()=='-1'){
//		$('#oa_dynamic_groups').html('');
		$('#oa_dynamic_group_item').html('');
		$("#oa_dynamic_submit").unbind('click');
		$("#oa_dynamic_submit").hide();
		return true;
	    }
	    if($(this).selected().val()=='end'){
		$("#oa_dynamic_submit").show();
		$("#oa_dynamic_submit").html('取引完了');
		msg = '<input type="hidden" id="endtheseorder" value="1"/>';
		$("#oa_dynamic_group_item").html(msg);
	    }else{
		$("#oa_dynamic_submit").show();
		$("#oa_dynamic_submit").html('保存');
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
		  alert($("#oa_dynamic_groups").find('option|[selected]').text()+'の保存が完了しました');
	      }
	  }
      }
    );
    return false;
    });
}



// 点击关联商品前的checkbox
function click_relate(pid,ele){
  // 增加库存
  if ($(ele).parent().parent().find('#checkbox_'+pid).attr('checked')) {
    $(ele).parent().parent().find('#offset_'+pid).attr('readonly', true);
    $.ajax({
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count='+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
} else {
  // 减库存
  $(ele).parent().parent().find('#offset_'+pid).attr('readonly', false);
  $.ajax({
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count=-'+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
}
}

// 清空库存输入框
function clear_quantity(){
  $('#relate_products_box input[type=checkbox]').each(function(){
      if ($(this).attr('checked')) {
      $(this).attr('checked', '');
      $(this).click();
      $(this).attr('checked', '');
      }
      });
}

// 计算增加的库存数并实时显示
function print_quantity(pid){
  $('#relate_product_'+pid).html($('#quantity_'+pid).html()-$('#offset_'+pid).val())
}

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
      alert("ブラウザに拒絶されました！\nブラウザのアドレス欄に'about:config'を入力してEnterキーを押します\nそれと'signed.applets.codebase_principal_support'数を'true'にしてください");   
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
  alert("クリップボードにコピーしました！")   
}  

function show_monitor_error(e_id,flag,_this){
  //改变DIV
  if(flag){
    allt(_this,e_id);
  }else{
    document.getElementById(e_id).style.display="none";
  }
}
function obj_obj(obj){
  return typeof(obj)=="string"?document.getElementById(obj):obj;
}
function allt(id,div_id){ 
  //div 赋值
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
function once_pwd_redircet_new_url(url_str){
  //window.location.href = url_str;
  $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");
var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
if(in_array(pwd, pwd_arr)){
window.location.href = url_str+'&once_pwd='+pwd; 
} else {
window.alert("パスワードが違います"); 
}
}
});
}

function new_mail_text(ele,st,tt,ot){
  // 选中的索引
  var idx = document.sele_act.elements[st].selectedIndex;
  // 选中值
  var CI  = document.sele_act.elements[st].options[idx].value;
  // 选中的checkbox值
  if (st == 'status') {
    // 列表页
    chk = getCheckboxValue('chk[]');
  } else {
    // 详细页
    chk = new Array();
    chk[0] = 0;
  }

  // 如果有了游戏人物名则不允许多选

  if((chk.length > 1)  && window.status_text[CI][0].indexOf('${ORDER_A}') != -1){
    alert('複数の選択はできません。');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert('注文書はまだ選択していません。');
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  // 记录上一个状态
  window.last_status = idx;
  // 更换表单内容
  if (st == 'status') {
    // 列表页
    if (typeof(window.status_title[CI]) != 'undefined' && typeof(window.status_title[CI][window.orderSite[chk[0]]]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][window.orderSite[chk[0]]];
      document.sele_act.elements[tt].value = window.status_text[CI][window.orderSite[chk[0]]].replace('${ORDER_A}', window.orderStr[chk[0]]);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr[chk[0]]);
    }
  } else {
    // 详细页
    if (typeof(window.status_title[CI]) != 'undefined') {
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    } else if (typeof(window.status_title[CI]) != 'undefined'){
      document.sele_act.elements[ot].value = window.status_title[CI][0];
      document.sele_act.elements[tt].value = window.status_text[CI][0].replace('${ORDER_A}', window.orderStr);
    }
  }
  // 替换${PAY_DATE}
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

// 邮件和提醒的checkbox
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

function preorders_computers(ele, cid, oid) {
  if (ele.className == 'orders_computer_checked') {
    $.ajax({
url: 'ajax_preorders.php?action=delete&orders_id='+oid+'&computers_id='+cid,
success: function(data) {
ele.className='orders_computer_unchecked';
}
});
} else {
  $.ajax({
url: 'ajax_preorders.php?action=insert&orders_id='+oid+'&computers_id='+cid,
success: function(data) {
ele.className='orders_computer_checked';
}
});
}
}

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
