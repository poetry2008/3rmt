// use orders.php
var f_flag = 'off';
var old_color = '';
var send_mail = false;
window.status_text  = new Array();
window.status_title = new Array();
window.last_status  = 0;
var auto_submit_able = true;
//last check time
var prev_customer_action = '';
var check_o_single = '0';
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
var select_send_top = $("#select_send_top").top;
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
  //if uncheck 
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
url: 'ajax_orders.php?action=show_status_mail_send',
success: function(text) {
  document.getElementById('send_mail_td').innerHTML=text;
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
//fax color
function fax_over_color(ele){
  old_color = ele.style.backgroundColor
    ele.style.backgroukdColor = "#ffcc99";
}
//fax color
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
//tt => form textarea name  mail content
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
url: 'ajax_orders.php?action=get_new_orders&prev_customer_action='+t,
success: function(text) {
if((text.indexOf('</body>')>0)&&(text.indexOf('</html>')>0)){
  alert(notice_relogin_str);
  window.location.reload();
}
$(text).insertAfter('#orders_list_table tr:eq(0)');
}
});
}

//validate order comment submit
function showRequest(formData, jqForm, options) { 
  return true; 
} 

//order right content
var temp_oid = '';
function showOrdersInfo(oID,ele,popup_type,param_str){
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
url: 'ajax_orders.php?action=show_right_order_info',
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

window.onresize = orders_info_box_offset; 
//move order information box 
function orders_info_box_offset(){
   var orders_value = '';
   var box_warp = '';
   var box_warp_top = 0;
   var box_warp_left = 0;
   if(temp_oid != ''){
    if($(".box_warp").offset()){
          box_warp = $(".box_warp").offset();
          box_warp_top = box_warp.top;
          box_warp_left = box_warp.left;
      }
   orders_value = $("#tr_" + temp_oid).offset();
  $("#orders_info_box").css('top',orders_value.top+$("#tr_" +  temp_oid).height()-box_warp_top);
  $("#orders_info_box").css('left',orders_value.left-box_warp_left);
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

//hide right order content
function hideOrdersInfo(popup_type){
  if (popup_type == 1) {
    popup_num = 1; 
  }
  $("#orders_info_box").html("");
  $("#orders_info_box").hide();
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
url: 'ajax_orders.php?action=last_customer_action',
success: function(last_customer_action) {
if((last_customer_action.indexOf('</body>')>0)&&(last_customer_action.indexOf('</html>')>0)){
  alert(notice_relogin_str);
  window.location.reload();
}
if (
  last_customer_action != cfg_last_customer_action 
  && prev_customer_action != last_customer_action
  ){
checkNewOrders(prev_customer_action != '' ? prev_customer_action : cfg_last_customer_action, 1)
if (check_o_single == '1') {
//if has new order
//change background color
$('body').css('background-color', '#ffcc99');// rgb(255, 204, 153)
$('.preorder_head').css('background-color', '#ffcc99');
//insert one row
newOrders(prev_customer_action != '' ? prev_customer_action : cfg_last_customer_action);
//update last check time
prev_customer_action = last_customer_action;
//play sound
playSound();
check_o_single = '0';
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

//click A,B,C
function orders_work(ele, work, oid) {
  document.getElementById('work_a').className = 'orders_flag_unchecked';
  document.getElementById('work_b').className = 'orders_flag_unchecked';
  document.getElementById('work_c').className = 'orders_flag_unchecked';
  document.getElementById('work_d').className = 'orders_flag_unchecked';
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
//click button action
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

//clean option
function clean_option(n,oid){
  //auto save 
  $.ajax({ url: "ajax_orders.php?orders_id="+oid+"&action=clean_option&questions_no="+n, success: function(){}});
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
//show question 
function show_questions(ele, notice_question_str, notice_order_save_str){
    ids = '';
    lastid = ele.value;
    show = true;
    if($(".dataTableContent").find('input|[type=checkbox][checked]').length==0){
	show = false;
	show_questiondiv(show, notice_question_str, notice_order_save_str)
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
                     show_questiondiv(show, notice_question_str, notice_order_save_str);
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
    // show question div 
    function show_questiondiv(show, notice_question_str, notice_order_save_str){

    if(show){
        $("#oa_dynamic_submit").attr('disabled',false);
        $('#oa_dynamic_groups').html('');
	$('#oa_dynamic_group_item').html('');
	$.ajax({ url: "ajax_orders.php?payment="+order_payment_type+"&buytype="+order_buy_type+"&action=get_oa_groups", success: function(msg){
	    var oa_groupsobj =  eval("("+msg+")");
	    var oa_groups = oa_groupsobj.split('_');;
	    $("#oa_dynamic_groups").find('option').remove(); // del old data 
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
    longstring+='&eof=eof';
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
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count='+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
success: function(data) {
}
});
} else {
  //reduce store 
  $(ele).parent().parent().find('#offset_'+pid).attr('readonly', false);
  $.ajax({
url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count=-'+($(ele).parent().parent().find('#quantity_'+pid).html()-$(ele).parent().parent().find('#offset_'+pid).val()),
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
function copyToClipboard(txt, notice_reject_str, notice_copy_str) {   
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
url: 'ajax_orders.php?action=getallpwd',
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
     var input_pwd_str = window.prompt(notice_pwd, ''); 
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
// new order email
function new_mail_text_orders(ele,st,tt,ot){
  var _end = $("#mail_title_status").val();
  var oid = $("#tmp_orders_id").val();
  // select index
  var idx = document.edit_order.elements[st].selectedIndex;
  // select value
  var CI  = document.edit_order.elements[st].options[idx].value;
  // select checkbox value
  window.last_status = idx;
  if(send_mail){

  if (document.getElementById('status_title_'+CI+'_0') != null) {
    document.edit_order.elements[ot].value = document.getElementById('status_title_'+CI+'_0').value;
    str_chk = document.getElementById('hidd_order_str').value;
    v_text = document.getElementById('status_text_'+CI+'_0').value;
    document.edit_order.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
  }

  if(document.edit_order.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.edit_order.elements[tt].value = document.edit_order.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

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
      url: 'ajax_orders.php?action=edit_order_send_mail&oid='+oid+'&o_status='+_end,
      success: function(msg) {
        document.getElementById('edit_order_send_mail').innerHTML=msg;
        send_mail = true;
  if (document.getElementById('status_title_'+CI+'_0') != null) {
    document.edit_order.elements[ot].value = document.getElementById('status_title_'+CI+'_0').value;
    str_chk = document.getElementById('hidd_order_str').value;
    v_text = document.getElementById('status_text_'+CI+'_0').value;
    document.edit_order.elements[tt].value = v_text.replace('${MAIL_COMMENT}', str_chk);
    }

  if(document.edit_order.elements[tt].value.indexOf('${PAY_DATE}') != -1){
    $.ajax({
dataType: 'text',
url: 'ajax_orders.php?action=paydate',
success: function(text) {
document.edit_order.elements[tt].value = document.edit_order.elements[tt].value.replace('${PAY_DATE}',text);
}
});
}

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
//new mail text
function new_mail_text(ele,st,tt,ot,notice_no_choose,notice_no_order){

  var _end = $("#mail_title_status").val();
  var oid = $("#tmp_orders_id").val();
  //select index 
  var idx = document.sele_act.elements[st].selectedIndex;
  //select value 
  var CI  = document.sele_act.elements[st].options[idx].value;
  //select checkbox 
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
      url: 'ajax_orders.php?action=edit_order_send_mail&oid='+oid+'&o_status='+_end,
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
//show order info
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
//del order info
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
//calculate order price
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

//calculate all product price
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
//mark work flag
function mark_work(ele, mark_symbol, select_mark, c_site, param_other)
{
  $.ajax({
    dataType: 'text',
    type:"POST",
    data:'param_other=' + param_other,
    async:false, 
    url: 'ajax_orders.php?action=handle_mark&mark_symbol='+mark_symbol+'&select_mark='+select_mark+'&c_site='+c_site,
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
    url: 'ajax_orders.php?action=select_sort',
    success: function(data) {
      data_array = data.split('|||'); 
      if (data_array[0] == 'success') {
        window.location.href = data_array[1]; 
      }
    }
  });

}
//transaction finish show or hide
function transaction_finish(is_finish,param){
  var is_finish = (is_finish==1)?'0':'1';
  $.ajax({
    type: "POST",
    data: 'is_finish='+is_finish+'&param='+param,
    async:false,
    url: 'ajax_orders.php?action=transaction',
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
function checkNewOrders(t)
{
  $.ajax({
    dataType: 'text',
    url: 'ajax_orders.php?action=get_new_orders&type=check&prev_customer_action='+t,
    success: function(msg) {
      if (msg == '1') {
        check_o_single = '1';
      } else {
        check_o_single = '0';
      }
    }
  });
}
//disable fix symbol
function orders_disable()
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
