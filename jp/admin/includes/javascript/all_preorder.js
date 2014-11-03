//use preorders.php
var send_mail = false;
var f_flag = 'off';
var old_color = '';
window.status_text  = new Array();
window.status_title = new Array();
window.last_status  = 0;
var auto_submit_able = true;
//last check time
var prev_customer_action = '';
var check_pre_o_single = '0';
//check all select
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
var select_send = $("#select_send").offset();
var select_send_top = select_send.top;
var select_send_height = $("#select_send").height();
var box_warp_top = $(".box_warp").height();
if((select_send_top+select_send_height) > box_warp_top){
  $(".box_warp").height(select_send_top+select_send_height);
}
}
//show mail send box
function chg_tr_color(aaa){
  field_on();
  var c_flag = aaa.checked;
  var tr_id = 'tr_' + aaa.value;

  //if checked 
  if(c_flag == true){

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      old_color = document.getElementById(tr_id).style.backgroundColor
    }

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = "#F08080";
    }
  //if not checked 
  }else{
    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
      document.getElementById(tr_id).style.backgroundColor = old_color;
    }

  }

}

function chg_td_color(bbb){
}

//open mail box
function field_on(){
	$.ajax({
	dataType: 'text',
	url: 'ajax_preorders.php?action=show_status_mail_send',
	success:function(text) {
	document.getElementById("send_mail_td").innerHTML=text;
	if(f_flag == 'off'){
	 document.getElementById("select_send").style.display = "block";
   }
  }
 });
}



//close mail box
 function field_off(){
  if(f_flag == 'on'){
    f_flag = 'off';
    document.getElementById("select_send").style.display = "none";
	 document.getElementById('send_mail_td').innerHTML='';
  }
}
//fox color
function fax_over_color(ele){
  old_color = ele.style.backgroundColor
    ele.style.backgroukdColor = "#ffcc99";
}
//fox color
function fax_over_color(ele){
  ele.style.backgroukdColor = old_color;
}

//search order
function search_type_changed(elem){
  document.forms.orders1.submit();
}
//get checkbox value
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

//st => form select name
//tt => form textarea name mail content 
//ot => form input name mail title 

 //mail text

function mail_text(st,tt,ot,notice_no_choose,notice_no_order){
  // select index 
  var idx = document.sele_act.elements[st].selectedIndex;
  //select value 
  var CI  = document.sele_act.elements[st].options[idx].value;
  //select checkbox value 
  if (st == 'status') {
    //list page    
    chk = getCheckboxValue('chk[]');
  }


  if((chk.length > 1)  && document.getElementById('status_text_'+CI+'_0').value.indexOf('${MAIL_COMMENT}') != -1){
    alert(notice_no_choose);
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  if(chk.length < 1){
    alert(notice_no_order);
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  //record last status 
  window.last_status = idx;
  //update form content 
  if (st == 'status') {
    //list page    
    if (document.getElementById('ordersite_'+chk[0])!=null){
      site_chk = document.getElementById('ordersite_'+chk[0]).value;
    }else{
      site_chk = 0;
    }
    if (document.getElementById('orderstr_'+chk[0])!=null){
      str_chk = document.getElementById('orderstr_'+chk[0]).value;
    }else{
      str_chk = '';
    }
    if (document.getElementById('status_title_'+CI+'_0') != null && document.getElementById('status_title'+CI+'_'+site_chk) != null) {
      alert('status_title_'+CI+'_'+site_chk);
      document.sele_act.elements[ot].value = document.getElementById('status_title_'+CI+'_'+site_chk).value;
      v_text = document.getElementById('status_text_'+CI+'_'+site_chk).value;
      document.sele_act.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
    } else if (document.getElementById('status_title_'+CI+'_0') != null){
      document.sele_act.elements[ot].value = document.getElementById('status_title_'+CI+'_0').value;
      v_text = document.getElementById('status_text_'+CI+'_0').value;
      document.sele_act.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
    }
  }
  //replace ${PAY_DATE} 
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

//mail and notice checkbox
if (document.getElementById('nomail_'+CI) == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}
}


//insert one row when new order
function newOrders(t)
{
  $.ajax({
dataType: 'text',
url: 'ajax_preorders.php?action=get_new_orders&prev_customer_action='+t,
success: function(text) {
$(text).insertAfter('#orders_list_table tr:eq(0)');
}
});
}

//validata order comment submit
function showRequest(formData, jqForm, options) { 
  return true; 
} 

//order right content
function showOrdersInfo(oID,ele){

  $.ajax({
type:"POST",
data:"oid="+oID,
async:false, 
url: 'ajax_preorders.php?action=show_right_order_info',
success: function(msg) {

$('#orders_info_box').html(msg);
if(document.documentElement.clientHeight < document.body.scrollHeight){
offset = ele.offsetTop + ele.offsetHeight + $('#orders_info_box').height() > $('#orders_list_table').height()? ele.offsetTop+$("#orders_list_table").position().top-$('#tep_site_filter').height()-$('#orders_info_box').height()-$('#offsetHeight').height():ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;
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
var orders_info_box_top = $("#orders_info_box").css("top");
orders_info_box_top = orders_info_box_top.replace("px","");
orders_info_box_top = parseInt(orders_info_box_top);
var orders_info_box_height = $("#orders_info_box").height();
var box_warp_heiht = $(".box_warp").height();
if((orders_info_box_top+orders_info_box_height) > box_warp_heiht){
  $(".box_warp").height(orders_info_box_top+orders_info_box_height);
}
}
});

}

//hide right order content
function hideOrdersInfo(popup_type){
  if (popup_type == 1) {
    popup_num = 1; 
  }
  $('#orders_info_box').html('');
  $('#orders_info_box').hide();
}

//play sound
function playSound()  
{  
  var node=document.getElementById('head_warn');  
  if(node!=null)  
  {  
    $.ajax({
      dataType: 'text', 
      url: 'ajax_orders.php?action=check_play_sound',
      success: function(sound_msg) {
		  if (sound_msg == '1') {
			 if (node.controls) {
				node.controls.play();
                                setTimeout(function(){node.controls.play();},600);
			 } else {
				node.play();
                                setTimeout(function(){node.play();},600);
			}
	    }
      }
    });
  }
}
//id must be selected where ele is selected
function auto_radio(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = true;
}
//id cancle select where ele is selected
function exclude(ele, id){
  if (ele.checked)
    document.getElementById(id).checked = false;
}
var change_option_enable = true;
//change option 
function change_option(ele){
  if (change_option_enable) {
	  //auto save
    auto_save_questions();
	//whether show button
    show_submit_button();
  }
}
//change option
function propertychange_option(ele){
  change_option_enable = false;
  //auto save
  auto_save_questions();
  //whether show button
  show_submit_button();

}

$(function(){
	//check whether status change every minute
    setTimeout(function(){checkChange()}, 60000);
    });
//check whether has new order every minute
function checkChange(){
  $.ajax({
dataType: 'text',
url: 'ajax_preorders.php?action=last_customer_action',
success: function(last_customer_action) {
if (
  last_customer_action != cfg_last_customer_action 
  && prev_customer_action != last_customer_action
  ){
checkNewPreOrders(prev_customer_action != '' ? prev_customer_action : cfg_last_customer_action);
if (check_pre_o_single == '1') {
	//if has new order
	//change background color
$('body').css('background-color', '#83dc94');// rgb(255, 204, 153)
$('.preorder_head').css('background-color', '#83dc94');
//insert one row
newOrders(prev_customer_action != '' ? prev_customer_action : cfg_last_customer_action);
//updata last check time
prev_customer_action = last_customer_action;
//play sound
playSound();
check_pre_o_single = '0';
}
}
}
});
setTimeout(function(){checkChange()}, 60000);
}

//click button
function orders_flag(ele, type, oid) {
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

//ckick A,B,C
function orders_work(ele, work, oid) { document.getElementById('work_a').className = 'orders_flag_unchecked';
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

//click button action
function orders_buttons(ele, cid, oid) {
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

//clean option 
function clean_option(n,oid){
	//auto save
  $.ajax({ url: "ajax_preorders.php?orders_id="+oid+"&action=clean_option&questions_no="+n, success: function(){}});
  //whether show button
  show_submit_button();
}

//whether show batch question box
var order_payment_type = '';
var order_buy_type = '';
var form_id = '';
var ids = '';
var order_can_end = 1;
var lastid = '';
//show qestion
function show_questions(ele,notice_question_str, notice_order_save_str){
    ids = '';
    lastid = ele.value;
    show = true;
    if($(".dataTableContent").find('input|[type=checkbox][checked]').length==0){
	show = false;
	show_questiondiv(show,notice_question_str, notice_order_save_str)
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
	$.ajax({ url: "ajax_preorders.php?action=get_oa_type",
		 type:'post',
		 beforeSend: function(jqXHR,settings){
		     if(lastid!=ele.value){
			 show_questiondiv(false, notice_question_str, notice_order_save_str)
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
                     show_questiondiv(show,notice_question_str, notice_order_save_str);
                     var select_send = $("#select_send").offset();
                     var select_send_top = select_send.top;
                     var select_send_height = $("#select_send").height();
                     var box_warp_top = $(".box_warp").height();
                     if((select_send_top+select_send_height) > box_warp_top){
                       $(".box_warp").height(select_send_top+select_send_height);
                     }
		 }});},1000);
    }
    return true;
}
//show question div
    function show_questiondiv(show, notice_question_str, notice_order_save_str){
    if(show){
        $("#oa_dynamic_submit").attr('disabled',false);
        $('#oa_dynamic_groups').html('');
	$('#oa_dynamic_group_item').html('');
	$.ajax({ url: "ajax_preorders.php?payment="+order_payment_type+"&buytype="+order_buy_type+"&action=get_oa_groups", success: function(msg){
	    var oa_groupsobj =  eval("("+msg+")");
	    var oa_groups = oa_groupsobj.split('_');;
	    $("#oa_dynamic_groups").find('option').remove(); //del old data 
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
		$("#oa_dynamic_groups")[0].options.add(new Option(notice_question_str,'end',true,false));
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
                $("#oa_dynamic_submit").attr('disabled',false);
		$("#oa_dynamic_submit").html(notice_question_str);
		msg = '<input type="hidden" id="endtheseorder" value="1"/>';
		$("#oa_dynamic_group_item").html(msg);
	    }else{
		$("#oa_dynamic_submit").show();
		$("#oa_dynamic_submit").html(notice_order_save_str);
	    $.ajax(
		{ 
		    url: "ajax_preorders.php?group_id="+$(this).val()+"&action=get_group_renderstring", 
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
    urloa = 'pre_oa_ajax.php?action=finish';
    }else{
    urloa = 'pre_oa_answer_process.php?action=muliUpdateOa';
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
		  alert($("#oa_dynamic_groups").find('option|[selected]').text()+notice_order_save_str);
	      }
	  }
      }
    );
    return false;
    });
}


//click relate product checkbox
function click_relate(pid,ele){
	//add store
  if ($(ele).parent().parent().find('#checkbox_'+pid).attr('checked')) {
    $(ele).parent().parent().find('#offset_'+pid).attr('readonly', true);
    $.ajax({
url: 'ajax_preorders.php?action=set_quantity&products_id='+pid+'&count='+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
} else {
	//reduce store
  $(ele).parent().parent().find('#offset_'+pid).attr('readonly', false);
  $.ajax({
url: 'ajax_preorders.php?action=set_quantity&products_id='+pid+'&count=-'+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
}
}

//clear quantity text
function clear_quantity(){
  $('#relate_products_box input[type=checkbox]').each(function(){
      if ($(this).attr('checked')) {
      $(this).attr('checked', '');
      $(this).click();
      $(this).attr('checked', '');
      }
      });
}

//calculate store
function print_quantity(pid){
  $('#relate_product_'+pid).html($('#quantity_'+pid).html()-$('#offset_'+pid).val())
}
//copy clipboard
function copyToClipboard(txt,notice_reject_str,notice_copy_str) {   
  if(window.clipboardData) {   
    window.clipboardData.clearData();   
    window.clipboardData.setData("Text", txt);   
  } else if(navigator.userAgent.indexOf("Opera") != -1) {   
    window.location = txt;   
  } else if (window.netscape) {   
    try {   
      netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");   
    } catch (e) {   
      alert(notice_reject_str);   
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
  alert(notice_copy_str);   
}  
//show monitor error
function show_monitor_error(e_id,flag,_this){
	//change div 
  if(flag){
    allt(_this,e_id);
  }else{
    document.getElementById(e_id).style.display="none";
  }
}
//get id 
function obj_obj(obj){
  return typeof(obj)=="string"?document.getElementById(obj):obj;
}
//all option
function allt(id,div_id){ 
	//assignment div value
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
//get new password url
function once_pwd_redircet_new_url(url_str, c_permission, notice_pwd, notice_pwd_error){
  $.ajax({
url: 'ajax_preorders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
data: 'current_page_name='+document.getElementById("hidden_page_info").value, 
async : false,
success: function(data) {
var tmp_msg_arr = data.split('|||'); 
var pwd_list_array = tmp_msg_arr[1].split(',');
if (c_permission == 31) {
  window.location.href = url_str; 
} else {
   if (tmp_msg_arr[0] == '0') {
     window.location.href = url_str; 
   } else {
     var input_pwd_str = window.prompt(notice_pwd); 
     if (in_array(input_pwd_str, pwd_list_array)) {
       $.ajax({
         url: 'ajax_orders.php?action=record_pwd_log',   
         type: 'POST',
         dataType: 'text',
         data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(url_str),
         async: false,
         success: function(msg_info) {
           window.location.href = url_str+'&once_pwd='+input_pwd_str; 
         }
       }); 
     } else {
       alert(notice_pwd_error); 
     }
   }
}
}
});
}


//new order email
function new_mail_text(ele,st,tt,ot,notice_no_choose,notice_no_order){
	var _end = $("#mail_title_status").val();
	var oid = $("#tmp_orders_id").val();
	//select index
  var idx = document.sele_act.elements[st].selectedIndex;
  //select value
  var CI  = document.sele_act.elements[st].options[idx].value;
  //select checkbox value
  window.last_status = idx;


  //update form content 
    //detail page 
  if(send_mail){

  if (document.getElementById('status_title_'+CI+'_0') != null) {
    document.sele_act.elements[ot].value = document.getElementById('status_title_'+CI+'_0').value;
    str_chk = document.getElementById('hidd_order_str').value;
    v_text = document.getElementById('status_text_'+CI+'_0').value;
    document.sele_act.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
  }
  //replace ${PAY_DATE}
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

//mail and notice checkbox
if (document.getElementById('nomail_'+CI) == '1') {
  $('#notify_comments').attr('checked','');
  $('#notify').attr('checked','');
} else {
  $('#notify_comments').attr('checked',true);
  $('#notify').attr('checked',true);
}

if ($(ele).val() == 20) {
  $('#notify').attr('checked', false);  
}
}else{

    $.ajax({
      dataType: 'text',
      async:false,
      url: 'ajax_preorders.php?action=edit_order_send_mail&oid='+oid+'&o_status='+_end,
      success: function(msg) {
        document.getElementById('edit_order_send_mail').innerHTML=msg;
        send_mail = true;
  if (document.getElementById('status_title_'+CI+'_0') != null) {
    document.sele_act.elements[ot].value = document.getElementById('status_title_'+CI+'_0').value;
    str_chk = document.getElementById('hidd_order_str').value;
    v_text = document.getElementById('status_text_'+CI+'_0').value;
    document.sele_act.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
  }
  //replace ${PAY_DATE}
  if(document.sele_act.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.sele_act.elements[tt].value = document.sele_act.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

//mail and notice checkbox
if (document.getElementById('nomail_'+CI) == '1') {
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
      });
}
}

  
//preorder flag
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
//preorder work
function preorders_work(ele, work, oid) {
  document.getElementById('work_a').className = 'orders_flag_unchecked';
  document.getElementById('work_b').className = 'orders_flag_unchecked';
  document.getElementById('work_c').className = 'orders_flag_unchecked';
  document.getElementById('work_d').className = 'orders_flag_unchecked';
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
//check preorder button
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
var temp_oid = '';
//show order info
function showPreOrdersInfo(oID,ele,popup_type,param_str){
  temp_oid = oID;
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
url: 'ajax_preorders.php?action=show_right_preorder_info',
success: function(msg) {

$('#orders_info_box').html(msg);
if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
		if(ele.offsetTop < $('#orders_info_box').height()){
	offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;	
		}else{
	offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#orders_info_box').height()-$('#offsetHeight').height();}
	}else{
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;	
	}
$('#orders_info_box').css('top',offset).show();
}else{
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#orders_info_box').height()-$('#offsetHeight').height();
	}else{
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight;	
	}
$('#orders_info_box').css('top',offset).show();
}
var orders_info_box_top = $("#orders_info_box").css("top");
orders_info_box_top = orders_info_box_top.replace("px","");
orders_info_box_top = parseInt(orders_info_box_top);
var orders_info_box_height = $("#orders_info_box").height();
var box_warp_heiht = $(".box_warp").height();
if((orders_info_box_top+orders_info_box_height) > box_warp_heiht){
  $(".box_warp").height(orders_info_box_top+orders_info_box_height);
}
}
});
}
$(document).ready(function(){
    $(".dataTableContent").find("input|[type=checkbox][checked]").parent().parent().each(function(){
      if($(this).attr('class')!='dataTableRowSelected'){$(this).attr('style','background-color: rgb(240, 128, 128);')}})
    });

//window.onresize = preorders_info_box_offset;

function preorders_info_box_offset(){
   var preorders_value = '';
   var box_warp = '';
   var box_warp_top = 0;
   var box_warp_left = 0;
   if(temp_oid != ''){
    if($(".box_warp").offset()){
           box_warp = $(".box_warp").offset();
           box_warp_top = box_warp.top;
           box_warp_left = box_warp.left;
     }
    preorders_value = $("#tr_" + temp_oid).offset();
   $("#orders_info_box").css('top',preorders_value.top+$("#tr_" +  temp_oid).height()-box_warp_top);
   $("#orders_info_box").css('left',preorders_value.left-box_warp_left);
  }
 
  var orders_info_box_top = $("#orders_info_box").css("top");
  orders_info_box_top = orders_info_box_top.replace("px","");
  orders_info_box_top = parseInt(orders_info_box_top);
  var orders_info_box_height = $("#orders_info_box").height();
  var box_warp_heiht = $(".box_warp").height();
  if((orders_info_box_top+orders_info_box_height) > box_warp_heiht){
    $(".box_warp").height(orders_info_box_top+orders_info_box_height);
  }
}

//del order info
function delete_preorder_info(oID, param_str)
{
  param_str = decodeURIComponent(param_str);
   $.ajax({
type:"POST",
data:'oID='+oID+'&'+param_str,
async:false, 
url: 'ajax_preorders.php?action=show_del_preorder_info',
success: function(msg) {
  $('#order_del').html(msg);
  var orders_info_box_top = $("#orders_info_box").css("top");
  orders_info_box_top = orders_info_box_top.replace("px","");
  orders_info_box_top = parseInt(orders_info_box_top);
  var orders_info_box_height = $("#orders_info_box").height();
  var box_warp_heiht = $(".box_warp").height();
  if((orders_info_box_top+orders_info_box_height) > box_warp_heiht){
    $(".box_warp").height(orders_info_box_top+orders_info_box_height);
  }
}
});
}
//cancel delete order info
function cancel_del_preorder_info(oID, param_str)
{
  param_str = decodeURIComponent(param_str);
$.ajax({
type:"POST",
data:'oID='+oID+'&'+param_str,
async:false, 
url: 'ajax_preorders.php?action=cancel_del_preorder_info',
success: function(msg) {
  $('#order_del').html(msg);
}
});
}


//mark work flag
function mark_work(ele, mark_symbol, select_mark, c_site, param_other)
{
  $.ajax({
    dataType: 'text',
    type:"POST",
    data:'param_other=' + param_other,
    async:false, 
    url: 'ajax_preorders.php?action=handle_mark&mark_symbol='+mark_symbol+'&select_mark='+select_mark+'&c_site='+c_site,
    success: function(data) {
      data_array = data.split('|||'); 
      if (data_array[0] == 'success') {
        if (ele.className == 'mark_flag_checked') {
          ele.className='mark_flag_unchecked';
        } else {
          ele.className='mark_flag_checked';
        }
        window.location.href = data_array[1]; 
      }
    }
  });
}


// select sort
function select_sort(sort_list,sort_type){
  var type = (sort_type=='asc')?0:1;  
  $.ajax({
    type: "POST",
    data: 'sort_list='+sort_list+'&sort_type='+type,
    async:false,
    url: 'ajax_preorders.php?action=select_sort',
    success: function(data) {
      data_array = data.split('|||'); 
      if (data_array[0] == 'success') {
        window.location.href = data_array[1]; 
      }
    }
  });

}


//transaction finish show or hide
function transaction_finish(is_finish,param)
{
  var is_finish = (is_finish==1)?'0':'1';
  $.ajax({
    type: "POST",
    data: 'is_finish='+is_finish+'&param='+param,
    async:false,
    url: 'ajax_preorders.php?action=transaction',
    success: function(data) {
      data_array = data.split('|||'); 
      if (data_array[0] == 'success') {  
        var otd = document.getElementById('mark_t');
        var otd_class = (is_finish==1)?'mark_flag_checked':'mark_flag_unchecked';
        otd.className = otd_class;
        window.location.href = data_array[1]; 
      }
    }
  });
}

//check new order
function checkNewPreOrders(t)
{
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_new_orders&type=check&prev_customer_action='+t,
    success: function(msg) {
      if (msg == '1') {
        check_pre_o_single = '1';
      } else {
        check_pre_o_single = '0';
      }
    }
  });
}
//disable fix symbol
function preorders_disable()
{
  $("#oa_dynamic_group_item").find('input').each(function(){
    $(this).attr('disabled',true);
  });
  $("#oa_dynamic_group_item").find('button').each(function(){
    $(this).attr('disabled',true);
  });
  $("#oa_dynamic_submit").attr('disabled',true);
}
function show_edit_fax(){
  document.getElementById('customer_fax_textarea').style.display ="block";  
  document.getElementById('customer_fax_textarea_input').style.display ="block";  
  document.getElementById('customer_fax_text').style.display ="none";  
  document.getElementById('customer_fax_text_input').style.display ="none";  
}
