function SetFocus() {
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") && 
          (field.elements[i].type != "hidden") && 
          (field.elements[i].type != "reset") && 
          (field.elements[i].type != "submit")  && (field.elements[i].type != "button")) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") || 
            (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();

        break;
      }
    }
  }
}


function submitChk() { 
  var date_orders = document.getElementById("date_orders");
  var date_order = document.getElementById("date_order");
  date_order.value = date_orders.value;
<?php /* 確認ダイアログ表示 */ ?>
  var flag = confirm ( "<?php echo JS_TEXT_GENERAL_IS_CONFIRM;?>"); 
<?php /* send_flg が TRUEなら送信、FALSEなら送信しない */ ?>
  if(flag){
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");
  var percent = 0;
  $.ajax({
    url: 'ajax_orders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if(!flag_tmp){
  var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
  if(pwd != null){
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    document.edit_order.submit();
  }else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  $("input[name=update_viladate]").val('_false');
  return false;
  }
  }else{
    alert("<?php echo JS_TEXT_GENERAL_RESET_UPDATE;?>");
    return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    document.edit_order.submit();
  }
  
}
});
}else{
return false;
}
return false;
} 

function submitChk2() { 
  var flag2 = true;
  $.ajax({
url: 'edit_new_orders2.php?action=check_session',
type: 'GET',
dataType: 'text',
async : false,
success: function(data) {
if (data == 'error') {
alert('<?php echo JS_TEXT_GENERAL_ERROR_PREORDER_IS_SET;?>');
flag2 = false;
}
}
});
if (flag2) {
  var flag = confirm ( "<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_PRICE;?>"); 
  if(flag){
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var percent = 0;
  $.ajax({
    url: 'ajax_orders.php?action=getpercent',
    data: 'pid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if(!flag_tmp){
  var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    _flag = true; 
    //document.edit_order.submit();
  }else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  $("input[name=update_viladate]").val('_false');
  $("input[name=x]").val('43');
  $("input[name=y]").val('12');
  document.edit_order.submit();
  return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    $("input[name=x]").val('43');
    $("input[name=y]").val('12');
    _flag = true;
  }
}
});
}else{
  return false;
}
return _flag; 
} else {
  location.href='create_order2.php';
}
} 

function update_price() {

  if (window.confirm("<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>")) {

    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_orders.php?action=getpercent',
    data: 'pid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp = false;
    }
  }
  });
if(!flag_tmp){
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
$("input[name=x]").val('');
$("input[name=y]").val('');
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
$("input[name=x]").val('');
$("input[name=y]").val('');
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{  
$("input[name=update_viladate]").val('');
$("input[name=x]").val('');
$("input[name=y]").val('');
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});



} else {
  window.alert("<?php echo JS_TEXT_GENERAL_RESET_ORDER_CONFIRM;?>");
}

}
function update_price2() {

  var date_orders = document.getElementById("date_orders");
  var date_order = document.getElementById("date_order");
  date_order.value = date_orders.value;
  if (window.confirm("<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>")) {
    document.edit_order.notify.checked = false;
    document.edit_order.notify_comments.checked = false;
    // 如果减少购买量则提示保存位置
    $('.update_products_qty').each(function(){
        old = $('#'+$(this).attr('id').replace('_new_qty_', '_qty_'));
        if(parseInt(old.val()) > parseInt($(this).val())){
        pid = $(this).attr('id').substr($(this).attr('id').indexOf('_qty_')+5);
        if (window.confirm($('#update_products_name_'+pid).val()+" "+(old.val() - $(this).val())+"<?php 
            echo JS_TEXT_GENERAL_QUANTITY_INFO;?>")) {
        $('#update_products_real_quantity_'+pid).val('1');
        } else {
        $('#update_products_real_quantity_'+pid).val('0');
        }
        }
        });
    // once pw viladate
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_orders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp = false;
    }
  }
  });
if(!flag_tmp){
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("<?php JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});


// delete   document.edit_order.submit();
} else {
  window.alert("<?php echo JS_TEXT_GENERAL_RESET_ORDER_CONFIRM;?>");
}
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

//tags sort

function change_sort_type(sort_type)
{
  url = 'tags.php?sort=' +sort_type;
  window.location.href = url;
}


/*
function in_array(needle, haystack) {
  if(typeof needle == 'string' || typeof needle == 'number') {
    for(var i in haystack) {
      if(haystack[i] == needle) {
        return true;
      }
    }
  }
  return false;
}
*/
function clearNoNum(obj)
{
  /*
  e = (window.event)? window.event:"";
  var key = e.keyCode?e.keyCode:e.which;
  if(!(key == 37 || key == 38 || key == 39 || key ==40)){
  */
  var re = /^[0-9]+\.?[0-9]*$/;
  var show_error_msg = false;  
  if(!re.test(obj.value) && obj.value != ''){
    show_error_msg = true; 
    alert('<?php echo JS_TEXT_GENERAL_INPUT_TEXT_ERROR;?>'); 
  } 
  if(show_error_msg){
    //replace all un number and '.'
    obj.value = obj.value.replace(/[^\d.]/g,"");
    //first char must be number
    obj.value = obj.value.replace(/^\./g,"");
    //only one '.'
    obj.value = obj.value.replace(/\.{2,}/g,".");
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    return true;
  }
  return false;
}

function clearNoNum_1(obj)
{
  /*
  e = (window.event)? window.event:"";
  var key = e.keyCode?e.keyCode:e.which;
  if(!(key == 37 || key == 38 || key == 39 || key ==40)){
  */
  var re = /^\-?[0-9]+\.?[0-9]*$/;
  var show_error_msg = false;  
  var temp_value = obj.value;
  if(!re.test(obj.value) && obj.value != ''){
    if(!(obj.value.substr(0,1) == '-' && obj.value.length == 1)){
      show_error_msg = true; 
      alert('<?php echo JS_TEXT_GENERAL_INPUT_TEXT_ERROR;?>'); 
    }  
  } 
  if(show_error_msg){
    //replace all un number and '.'
    obj.value = obj.value.replace(/[^\d.]/g,"");
    //first char must be number
    obj.value = obj.value.replace(/^\./g,"");
    //only one '.'
    obj.value = obj.value.replace(/\.{2,}/g,".");
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    if(temp_value.indexOf("-") == 0 || (temp_value.indexOf("-") == 1 && isNaN(temp_value.substr(0,1)))){ 
      obj.value = '-'+obj.value;
    }
    if(temp_value.indexOf("-") == 1 && !isNaN(temp_value.substr(0,1))){
      obj.value = temp_value.substr(1); 
    }
  }
}

function check_toggle_status(url_str)
{
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

//faq change is show 
function change_status(url_str){
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
function faq_c_is_set_romaji(pid,cid,site_id){
  var flag = true;
  var cromaji = $("#cromaji").val();
  cromaji = replace_romaji(cromaji);
  $.ajax({
    url: 'ajax_orders.php?action=faq_c_is_set_romaji',
    type: 'POST',
    data: 'romaji='+cromaji+'&cid='+cid+'&pid='+pid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_IS_HAS;?>");
      }
    }
  });
  return flag;
}
function faq_c_is_set_error_char(romaji){
  var flag = true;
  var cromaji = $("#cromaji").val();
  cromaji = replace_romaji(cromaji);
  $.ajax({
    url: 'ajax_orders.php?action=check_romaji',
    type: 'POST',
    data: 'romaji='+cromaji,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data!=''){
        flag = false;
        $("#cromaji").val(data);
        alert("<?php echo JS_TEXT_GENERAL_CHAR_SET_INFO;?>");
      }
    }
  });
  return flag;
}
function faq_q_is_set_romaji(cid,qid,site_id){
  var flag = true;
  var qromaji = $("#qromaji").val();
  qromaji = replace_romaji(qromaji);
  $.ajax({
    url: 'ajax_orders.php?action=faq_q_is_set_romaji',
    type: 'POST',
    data: 'romaji='+qromaji+'&cid='+cid+'&qid='+qid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_IS_HAS;?>");
      }
    }
  });
  return flag;
}
function faq_q_is_set_error_char(romaji){
  var flag = true;
  var qromaji = $("#qromaji").val();
  qromaji = replace_romaji(qromaji);
  $.ajax({
    url: 'ajax_orders.php?action=check_romaji',
    type: 'POST',
    data: 'romaji='+qromaji,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data!=''){
        flag = false;
        $("#qromaji").val(data);
        alert("<?php echo JS_TEXT_GENERAL_CHAR_SET_INFO;?>");
      }
    }
  });
  return flag;
}
function faq_category_form_validator(pid,cid,site_id){
  flag1 = faq_c_is_set_romaji(pid,cid,site_id);
  flag2 = faq_c_is_set_error_char(''); 
  if(flag1&&flag2){
    return true;
  }else{
    return false;
  }
}


function faq_question_form_validator(cid,qid,site_id){
  flag1 = faq_q_is_set_romaji(cid,qid,site_id);
  flag2 = faq_q_is_set_error_char(''); 
  if(flag1&&flag2){
    return true;
  }else{
    return false;
  }
}

function faq_category_romaji_can_move(cromaji,site_id){
  var flag = true;
  var pid = $("select[name='move_to_faq_category_id']").val();
  cromaji = replace_romaji(cromaji);
  $.ajax({
    url: 'ajax_orders.php?action=faq_c_is_set_romaji',
    type: 'POST',
    data: 'romaji='+cromaji+'&pid='+pid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_NOT_MOVE;?>");
      }
    }
  });
  return flag;
}
function faq_question_romaji_can_move(qromaji,site_id){
  var flag = true;
  var cid = $("select[name='move_to_faq_category_id']").val();
  qromaji = replace_romaji(qromaji);
  $.ajax({
    url: 'ajax_orders.php?action=faq_q_is_set_romaji',
    type: 'POST',
    data: 'romaji='+qromaji+'&cid='+cid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_NOT_MOVE;?>");
      }
    }
  });
  return flag;
}
function faq_question_romaji_can_copy_to(qromaji,site_id){
  var cid = $("select[name='faq_category_id']").val();
  qromaji = replace_romaji(qromaji);
  var flag = true;
  $.ajax({
    url: 'ajax_orders.php?action=faq_q_is_set_romaji',
    type: 'POST',
    data: 'romaji='+qromaji+'&cid='+cid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_NOT_COPY;?>");
      }
    }
  });
  return flag;
}

function replace_romaji(romaji){
  //replace & + to a string
romaji = romaji.replace(/\&/g,'11111111');
romaji = romaji.replace(/\+/g,'22222222');
romaji = romaji.replace(/\//g,'33333333');
romaji = romaji.replace(/\%/g,'44444444');
romaji = romaji.replace(/\#/g,'55555555');
romaji = romaji.replace(/\?/g,'66666666');
romaji = romaji.replace(/ /g,'77777777');
romaji = romaji.replace(/\,/g,'88888888');
romaji = romaji.replace(/\</g,'aaaaaaaa');
romaji = romaji.replace(/\>/g,'bbbbbbbb');
romaji = romaji.replace(/\{/g,'cccccccc');
romaji = romaji.replace(/\}/g,'dddddddd');
romaji = romaji.replace(/\(/g,'eeeeeeee');
romaji = romaji.replace(/\)/g,'ffffffff');
romaji = romaji.replace(/\|/g,'gggggggg');
romaji = romaji.replace(/\^/g,'hhhhhhhh');
romaji = romaji.replace(/\[/g,'iiiiiiii');
romaji = romaji.replace(/\]/g,'jjjjjjjj');
romaji = romaji.replace(/\`/g,'kkkkkkkk');
romaji = romaji.replace(/\~/g,'llllllll');
romaji = romaji.replace(/\\/g,'mmmmmmmm');
romaji = romaji.replace(/\*/g,'nnnnnnnn');
romaji = romaji.replace(/\"/g,'oooooooo');
romaji = romaji.replace(/\=/g,'pppppppp');
romaji = romaji.replace(/\'/g,'qqqqqqqq');
  return romaji;
}
function c_is_set_error_char(replace_single){
  var flag = true;
  var cromaji = $("#cromaji").val();
  cromaji = replace_romaji(cromaji);
  $.ajax({
    url: 'ajax_orders.php?action=check_romaji',
    type: 'POST',
    data: 'romaji='+cromaji,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data!=''){
        flag = false;
        if (replace_single == false) {
          $("#cromaji").val(data); 
          alert("<?php echo JS_TEXT_GENERAL_CHAR_SET_INFO;?>");
        } else {
          alert("<?php echo JS_TEXT_GENERAL_ROMAJI_ERROR;?>");
        }
      }
    }
  });
  return flag;
}
function p_is_set_error_char(){
  var flag = true;
  var qromaji = $("#promaji").val();
  qromaji = replace_romaji(qromaji);
  $.ajax({
    url: 'ajax_orders.php?action=check_romaji',
    type: 'POST',
    data: 'romaji='+qromaji,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data!=''){
        flag = false;
        $("#promaji").val(data);
        alert("<?php echo JS_TEXT_GENERAL_CHAR_SET_INFO;?>");
      }
    }
  });
  return flag;
}

function c_is_set_romaji(pid,cid,site_id){
  var flag = true;
  var cromaji = $("#cromaji").val();
  cromaji = replace_romaji(cromaji);
  $.ajax({
    url: 'ajax_orders.php?action=c_is_set_romaji',
    type: 'POST',
    data: 'romaji='+cromaji+'&cid='+cid+'&pid='+pid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_IS_HAS;?>");
      }
    }
  });
  return flag;
}

function p_is_set_romaji(cid,qid,site_id){
  var flag = true;
  var qromaji = $("#promaji").val();
  qromaji = replace_romaji(qromaji);
  $.ajax({
    url: 'ajax_orders.php?action=p_is_set_romaji',
    type: 'POST',
    data: 'romaji='+qromaji+'&cid='+cid+'&qid='+qid+'&site_id='+site_id,
    dataType: 'text',
    async : false,
    success: function(data) {
      if(data=='true'){
        flag = false;
        alert("<?php echo JS_TEXT_GENERAL_IS_HAS;?>");
      }
    }
  });
  return flag;
}
function products_form_validator(cid,qid,site_id){
  flag1 = p_is_set_romaji(cid,qid,site_id);
  flag2 = p_is_set_error_char(); 

  var flag = false;
  var op1 = false;
  var op = $("#op").val();
  var pw1 = false;
  var pw = $("#products_weight").val();
  var pp1 = false;
  var pp = $("#pp").val();
  var pad1 = false;
  var pad = $("#products_add_del").val();
  var prq1 = false;
  var prq = $("#products_real_quantity").val();
  var pa1 = false;
  var pa = $("#products_attention_1_3").val();
  var pcm1 = false;
  var pcm = $("#products_cart_min").val();
  var pc1 = false;
  var pc = $("#products_cartorder").val();
  
  if(op.length > 15){
 
    op1 = true;
    flag = true;  
  }

  if(pw.length > 15){
 
    pw1 = true;
    flag = true;  
  }

  if(pp.length > 15){
 
    pp1 = true;
    flag = true;  
  }

  if(pad.length > 15){
 
    pad1 = true;
    flag = true;  
  }

  if(prq.length > 15){
 
    prq1 = true;
    flag = true;  
  }

  if(pa.length > 15){
 
    pa1 = true;
    flag = true;  
  }

  if(pcm.length > 15){
 
    pcm1 = true;
    flag = true;  
  }

  if(pc.length > 15){
 
    pc1 = true;
    flag = true;  
  }

  if(flag == true){

    var error_str = '<?php echo JS_TEXT_GENERAL_INPUT_FORM_ERROR;?>'+"\n\n";
    if(op1 == true){error_str += '<?php echo JS_TEXT_GENERAL_SORT_ERROR;?>'+"\n";}
    if(pw1 == true){error_str += '<?php echo JS_TEXT_GENERAL_WEIGHT_ERROR;?>'+"\n";}
    if(pp1 == true){error_str += '<?php echo JS_TEXT_GENERAL_PRICE_INFO_ERROR;?>'+"\n";}
    if(pad1 == true){error_str += '<?php echo JS_TEXT_GENERAL_ADDORSUB_ERROR;?>'+"\n";}
    if(prq1 == true){error_str += '<?php echo JS_TEXT_GENERAL_REAL_QUANTITY_ERROR;?>'+"\n";}
    if(pa1 == true){error_str += '<?php echo JS_TEXT_GENERAL_QUANTITY_INFO_ERROR;?>'+"\n";}
    if(pcm1 == true){error_str += '<?php echo JS_TEXT_GENERAL_CARTFLAG_TITLE_ERROR;?>'+"\n";} 
    if(pc1 == true){error_str += '<?php echo JS_TEXT_GENERAL_CARTORDER_ERROR;?>'+"\n";} 
    alert(error_str);
    return false;
  }
  if(flag1&&flag2){
    return true;
  }else{
    return false;
  }
}
function doubleClickme()
{

    $(".dataTableRowSelected").removeClass("dataTableRowSelected");
    $(this).addClass("dataTableRowSelected");
}

function presubmitChk() { 
  var flag = confirm ( "<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_PRICE;?>"); 
  if(flag){
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if(!flag_tmp){
  var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    document.edit_order.submit();
  }else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  $("input[name=update_viladate]").val('_false');
  document.edit_order.submit();
  return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    document.edit_order.submit();
  }
}
});
}else{
return false;
}
return false;
} 

function pre_update_price() {
  document.getElementById("h_predate").value = document.getElementById("date_predate").value;
  document.getElementById("h_deadline").value = document.getElementById("date_ensure_deadline").value;
  var num_is_null = false; 

  if (window.confirm("<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>")) {
    $('.update_products_qty').each(function(){
        if ($(this).val() == 0) {
           num_is_null = true; 
          alert('<?php echo JS_TEXT_GENERAL_PRODUCT_NOT_ZERO;?>');
        }
    });

    if (!num_is_null) {
    //viladate
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'pid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp = false;
    }
  }
  });
if(!flag_tmp){
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{  
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});

}

} else {
  window.alert("<?php echo JS_TEXT_GENERAL_RESET_ORDER_CONFIRM;?>");
}

}
function pre_update_price2() {
  document.getElementById("h_predate").value = document.getElementById("date_predate").value;
  document.getElementById("h_deadline").value = document.getElementById("date_ensure_deadline").value;
  var num_is_null = false; 
  if (window.confirm("<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>")) {
    document.edit_order.notify.checked = false;
    document.edit_order.notify_comments.checked = false;
    // 如果减少购买量则提示保存位置
    $('.update_products_qty').each(function(){
        if ($(this).val() == 0) {
           num_is_null = true; 
          alert('<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>');
        }
    });
    if (!num_is_null) {
    // once pw viladate
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp = false;
    }
  }
  });
if(!flag_tmp){
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});
}
// delete   document.edit_order.submit();
} else {
  window.alert("<?php echo JS_TEXT_GENERAL_RESET_ORDER_CONFIRM;?>");
}
}

function check_toggle_black_status(url_str)
{
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(data) {
      var pwd_arr = data.split(",");
      var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
      if(in_array(pwd, pwd_arr)){
        if (window.confirm('<?php echo JS_TEXT_GENERAL_SHOW_REVIEW;?>')) {
          window.location.href = url_str+'&once_pwd='+pwd+'&up_rs=true'; 
        } else {
          window.location.href = url_str+'&once_pwd='+pwd; 
        }
      } else {
        window.alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>"); 
      }
    }
  });
}

function clearLibNum(obj) 
{
  var re = /^[0-9]+$/;
  var error_single = false; 
  if(!re.test(obj.value) && obj.value != ''){
    alert('<?php echo JS_TEXT_GENERAL_INPUT_TEXT_ERROR;?>'); 
    error_single = true; 
  }
  if (error_single) {
    obj.value = obj.value.replace(/[^0-9]/g,"");
  }
}

function createPreorderChk() { 
  var flag2 = true;
  $.ajax({
url: 'edit_new_preorders.php?action=check_session',
type: 'GET',
dataType: 'text',
async : false,
success: function(data) {
if (data == 'error') {
alert('<?php echo JS_TEXT_GENERAL_ERROR_PREORDER_IS_SET;?>');
flag2 = false;
}
}
});
if (flag2) {
    $.ajax({
url: 'ajax_preorders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'pid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if(!flag_tmp){
  var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    _flag = true; 
    //document.edit_order.submit();
  }else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  $("input[name=update_viladate]").val('_false');
  $("input[name=x]").val('43');
  $("input[name=y]").val('12');
  return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    $("input[name=x]").val('43');
    $("input[name=y]").val('12');
    _flag = true;
    //document.edit_order.submit();
  }
}
});
return _flag; 
} else {
  location.href='create_preorder.php';
}
} 

function create_preorder_price() {

  var num_is_null = false; 
  if (window.confirm("<?php echo JS_TEXT_GENERAL_CONFIRM_ORDER_INFO;?>")) {
    $('.update_products_qty').each(function(){
        if ($(this).val() == 0) {
           num_is_null = true; 
          alert('<?php echo JS_TEXT_GENERAL_PRODUCT_NOT_ZERO;?>');
        }
    });

    if (!num_is_null) {
    //viladate
    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var percent = 0;
  $.ajax({
    url: 'ajax_orders.php?action=getpercent',
    data: 'pid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp = false;
    }
  }
  });
if(!flag_tmp){
var pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
$("input[name=x]").val('');
$("input[name=y]").val('');
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
$("input[name=x]").val('');
$("input[name=y]").val('');
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{  
$("input[name=update_viladate]").val('');
$("input[name=x]").val('');
$("input[name=y]").val('');
window.alert("<?php echo JS_TEXT_GENERAL_CONFIRM_PRICE_INFO;?>");
document.edit_order.submit();
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});
}
} else {
  window.alert("<?php echo JS_TEXT_GENERAL_RESET_ORDER_CONFIRM;?>");
}
}

function show_text(id,ele,type,sort,flag,title,name,comment){

    ele = ele.parentNode;
    $.ajax({
       url: 'ajax_address.php',
       data: {id:id,type:type,sort:sort,flag:flag,title:title,name:name,comment:comment},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         $("div#show").html(data);
      
       if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	    if(ele.offsetTop < $('#show').height()){
	       offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	    }else{
	       offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
            }
	}else{
          offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	}
        $('#show').css('top',offset).show();
      }else{
        if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	   offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
	}else{
           offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
        }
        $('#show').css('top',offset).show();
     } 
     
       $("div#show").show();
       }
    }); 
            
}

function show_text_fee(id,ele,flag){
    
    ele = ele.parentNode;
    $.ajax({
       url: 'ajax_country_fee.php',
       data: {id:id,flag:flag},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         $("div#show").html(data);
     
       if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	    if(ele.offsetTop < $('#show').height()){
	       offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	    }else{
	       offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
            }
	}else{
          offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	}
        $('#show').css('top',offset).show();
      }else{
        if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	   offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
	}else{
           offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
        }
        $('#show').css('top',offset).show();
     }
  
         $("div#show").show();
       }
    }); 
            
}

function show_text_area(id,ele,fid,sort,flag){
    
    ele = ele.parentNode;
    $.ajax({
       url: 'ajax_country_area.php',
       data: {id:id,fid:fid,sort:sort,flag:flag},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         $("div#show").html(data);
   
       if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	    if(ele.offsetTop < $('#show').height()){
	       offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	    }else{
	       offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
            }
	}else{
          offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	}
        $('#show').css('top',offset).show();
      }else{
        if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	   offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
	}else{
           offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
        }
        $('#show').css('top',offset).show();
     } 

         $("div#show").show();
       }
    }); 
            
}

//城市配送费用设置
function show_text_city(id,ele,fid,sort,flag){
    
    ele = ele.parentNode;
    $.ajax({
       url: 'ajax_country_city.php',
       data: {id:id,fid:fid,sort:sort,flag:flag},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         $("div#show").html(data);
   
       if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	    if(ele.offsetTop < $('#show').height()){
	       offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	    }else{
	       offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
            }
	}else{
          offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	}
        $('#show').css('top',offset).show();
      }else{
        if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	   offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
	}else{
           offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
        }
        $('#show').css('top',offset).show();
     }
 
         $("div#show").show();
       }
    }); 
            
}

//商品配送时间
function show_text_products(id,ele,sort,flag){
     
    ele = ele.parentNode;
    $.ajax({
       url: 'ajax_products_shipping_time.php',
       data: {id:id,sort:sort,flag:flag},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         $("div#show").html(data);

      if(document.documentElement.clientHeight < document.body.scrollHeight){
	if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	    if(ele.offsetTop < $('#show').height()){
	       offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	    }else{
	       offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
            }
	}else{
          offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
	}
        $('#show').css('top',offset).show();
      }else{
        if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
	   offset = ele.offsetTop+$("#group_list_box").position().top-1-$('#show').height()-$('#offsetHeight').height();
	}else{
           offset = ele.offsetTop+$("#group_list_box").position().top+ele.offsetHeight;	
        }
        $('#show').css('top',offset).show();
     }
 
         $("div#show").show();
       }
    }); 
            
}


function hide_text(){

        $("div#show").hide();
        window.location.reload();
        
}

function check(action){
  var options = {
    url: 'address.php?action='+action,
    type:  'POST',
    success: function() {
      if(action == 'save'){
        alert('<?php echo JS_TEXT_GENERAL_SAVED;?>');
      }else{
        alert('<?php echo JS_TEXT_GENERAL_DELETE_SUCCESS;?>');
        window.location.reload();
      }
    }
  };
  $('#addressform').ajaxSubmit(options);
  return false; 
}

function check_fee(action){
  var options = {
    url: 'country_fee.php?action='+action,
    type:  'POST',
    success: function() {
      if(action == 'save'){
        alert('<?php echo JS_TEXT_GENERAL_SAVED;?>');
      }else{
        alert('<?php echo JS_TEXT_GENERAL_DELETE_SUCCESS;?>');
        window.location.reload();
      }
    }
  };
  $('#country_fee_form').ajaxSubmit(options);
  return false; 
}

function check_area(action){
  var options = {
    url: 'country_area.php?action='+action,
    type:  'POST',
    success: function() {
      if(action == 'save'){
        alert('<?php echo JS_TEXT_GENERAL_SAVED;?>');
      }else{
        alert('<?php echo JS_TEXT_GENERAL_DELETE_SUCCESS;?>');
        window.location.reload();
      }
    }
  };
  $('#country_area_form').ajaxSubmit(options);
  return false;
}

function check_city(action){
  var options = {
    url: 'country_city.php?action='+action,
    type:  'POST',
    success: function() {
      if(action == 'save'){
        alert('<?php echo JS_TEXT_GENERAL_SAVED;?>');
      }else{
        alert('<?php echo JS_TEXT_GENERAL_DELETE_SUCCESS;?>');
        window.location.reload();
      }
    }
  };
  $('#country_area_form').ajaxSubmit(options);
  return false;
}

function check_products(action){
  var options = {
    url: 'products_shipping_time.php?action='+action,
    type:  'POST',
    success: function() {
      if(action == 'save'){
        alert('<?php echo JS_TEXT_GENERAL_SAVED;?>');
      }else{
        alert('<?php echo JS_TEXT_GENERAL_DELETE_SUCCESS;?>');
        window.location.reload();
      }
    }
  };
  $('#products_form').ajaxSubmit(options);
  return false;
}

function check_on(action,id){
  location.href = "address.php?action="+action+"&id="+id;
}

function check_on_fee(action,id){
  location.href = "country_fee.php?action="+action+"&id="+id;
}

function check_on_area(action,id,fid){
  location.href = "country_area.php?action="+action+"&id="+id+"&fid="+fid;
}

function check_on_city(action,id,fid){
  location.href = "country_city.php?action="+action+"&id="+id+"&fid="+fid;
}

function check_on_products(action,id,fid){
  location.href = "products_shipping_time.php?action="+action+"&id="+id;
}

function check_del(num){
  var num_set = document.getElementById('num').value;
  num_set--;
  document.getElementById('num').value = num_set;

  $("#o"+num).remove();
}

function check_add(){
  var str = '';
  var i;
  var num = document.getElementById('num').value;
  var num_1 = document.getElementById('num_1').value;
  num = parseInt(num);
  num_1 = parseInt(num_1);

  num += 5;
  document.getElementById('num').value = num; 
  for(i=num-5;i<num;i++){
    str += '<tr id="o'+i+'"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JS_TEXT_GENERAL_SELECT_OPTION;?></td><td><input type="text" name="option_comment[]" value=""><input type="radio" name="option_value" value="'+i+'"><input type="button" value="<?php echo JS_TEXT_GENERAL_DELETE;?>" onclick="check_del('+i+');"></td></tr>';

  }
  $("#button_add").append(str);
}

function check_form(){
  var title = document.getElementById('title').value;
  var name = document.getElementById('name').value;
  var error_title = '<font color="red"><?php echo JS_TEXT_GENERAL_MUST_INPUT;?></font>'; 
  var error_name = '<font color="red"><?php echo JS_TEXT_GENERAL_MUST_INPUT;?></font>'; 

  error_str = false;
  if(title == ''){
    $("#error_title").html(error_title);
    $("#title").focus();
    error_str = true;
  }else{
    $("#error_title").html('');
  }
  
 if(name == ''){
    $("#error_name").html(error_name);
    $("#name").focus();
    error_str = true;
  }else{
    $("#error_name").html('');
  }
 
 if(error_str == true){
   return false;
 }else{
 
   return true;
 }


}

function check_form_products(){
  var name = document.getElementById('name').value;
  var error_name = '<font color="red"><?php echo JS_TEXT_GENERAL_MUST_INPUT;?></font>'; 

  if(name == ''){
    $("#error_name").html(error_name);
    $("#name").focus();
    return false;
  }else{
  
    $("#error_name").html('');
  }

 return true;

}

function work_add(){
  var work_num = document.getElementById('work_num');
  num = work_num.value;
  num = parseInt(num);
  work_num.value = num+1; 
  var work_str = '<tr id="workid'+num+'"><td width="30%" align="left"><?php echo JS_TEXT_GENERAL_EXPECT_TRADE_TIME;?>'+num+'</td><td><input type="text" name="work_start_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_start_min[]" size="3" maxlength="2" value="">&nbsp;<?php echo JS_TEXT_GENERAL_LINK_CHAR;?>&nbsp;<input type="text" name="work_end_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_end_min[]" size="3" maxlength="2" value="">&nbsp;<input type="button" value="<?php echo JS_TEXT_GENERAL_DELETE;?>" onclick="work_del('+num+');"><br><span id="work_error'+num+'"></span></td></tr>';

 $("#work_list").append(work_str); 
}


function work_check(){

  var work_start_hour = document.getElementsByName("work_start_hour[]");
  var work_start_min = document.getElementsByName("work_start_min[]");
  var work_end_hour = document.getElementsByName("work_end_hour[]");
  var work_end_min = document.getElementsByName("work_end_min[]");
  var mode_hour = /^([0-1][0-9])|(2[0-3])$/;
  var mode_hour_1 = /^[0-9]$/; 
  var mode_min = /^[0-5][0-9]$/;
  var mode_min_1 = /^[0-9]$/;
  var error_str = false;
  var error = false;
  var error_1 = false;
  var error_2 = false;
  var error_3 = false;

  for(i = 0;i < work_start_hour.length;i++){
    start_time_num = work_start_hour[i].value+work_start_min[i].value;
    end_time_num = work_end_hour[i].value+work_end_min[i].value;
    start_time_num = parseInt(start_time_num,10);
    end_time_num = parseInt(end_time_num,10);
    if(end_time_num < start_time_num){
      error_3 = true;  
      error = true;
    }else{
    
      error_3 = false;
    }
    if(i == 0){
      if(work_start_hour[i].value == '' || work_start_min[i].value == '' || work_end_hour[i].value == '' || work_end_min[i].value ==''){
        error_1 = true;
        //error_str = true;
        error = true;
      }else{
        if((!mode_hour.test(work_start_hour[i].value) && !mode_hour_1.test(work_start_hour[i].value)) || (!mode_min.test(work_start_min[i].value) && !mode_min_1.test(work_start_min[i].value)) || (!mode_hour.test(work_end_hour[i].value) && !mode_hour_1.test(work_end_hour[i].value)) || (!mode_min.test(work_end_min[i].value) && !mode_min_1.test(work_end_min[i].value))){
      
          //error_str = true;
          error_2 = true;
          error = true;
        }else{
    
          error_str = false;
        } 
      }
    
    }else{
      if(work_start_hour[i].value != '' || work_start_min[i].value != '' || work_end_hour[i].value != '' || work_end_min[i].value !=''){ 
        if((!mode_hour.test(work_start_hour[i].value) && !mode_hour_1.test(work_start_hour[i].value)) || (!mode_min.test(work_start_min[i].value) && !mode_min_1.test(work_start_min[i].value)) || (!mode_hour.test(work_end_hour[i].value) && !mode_hour_1.test(work_end_hour[i].value)) || (!mode_min.test(work_end_min[i].value) && !mode_min_1.test(work_end_min[i].value))){
      
          error_str = true;
          error = true;
        }else{
    
          error_str = false;
        } 
      }
    }
     
    if(error_str == true){
     
      $("#work_error"+(i+1)).html('<font color="red"><?php echo JS_TEXT_GENERAL_INPUT_ERROR;?></font>');
      
    }else{
        if(error_1 == true && i == 0){
      
          $("#work_error"+(i+1)).html('<font color="red"><?php echo JS_TEXT_GENERAL_MUST_INPUT;?></font>');
        }else if(error_2 == true && i == 0){
      
          $("#work_error"+(i+1)).html('<font color="red"><?php echo JS_TEXT_GENERAL_INPUT_ERROR;?></font>');
        }else if(error_3 == true){
          $("#work_error"+(i+1)).html('<font color="red"><?php echo JS_TEXT_GENERAL_INPUT_ERROR;?></font>');
        }else{
          $("#work_error"+(i+1)).html(''); 
        }
    }
  }

  if(error){
  
    return false;
  }else{
  
    return true;
  }
}

function work_del(value){

  $("#workid"+value).remove();
}

function select_item_radio(i_obj, t_str, o_str, p_str, r_price)
{
      $(i_obj).parent().parent().parent().parent().parent().find('a').each(function() {
        if ($(this).parent().parent()[0].className == 'option_show_border') {
          $(this).parent().parent()[0].className = 'option_hide_border';
        } 
      });   
      if (t_str == '') {
        t_str = $(i_obj).children("span:first").html(); 
      } else {
        t_str = ''; 
      }
      $(i_obj).parent().parent()[0].className = 'option_show_border'; 
      origin_default_value = $('#'+o_str).val(); 
      $('#'+o_str).parent().html("<input type='hidden' id='"+o_str+"' name='"+p_str+"' value=\""+t_str+"\">"); 
}