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
  /* 確認ダイアログ表示 */ 
  var flag = confirm ( "確認はしましたか？\n\n【 重要 】価格構成要素を変更した場合は、先に「注文内容確認」ボタンを押す必要があります。\n\n戻る場合は [キャンセル] ボタンをクリックしてください。"); 
  /* send_flg が TRUEなら送信、FALSEなら送信しない */ 
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
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    document.edit_order.submit();
  }else{
  alert("パスワードが違います");
  $("input[name=update_viladate]").val('_false');
  document.edit_order.submit();
//  alert("更新をキャンセルしました。");
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
alert('エラー: 注文が存在しません。');
flag2 = false;
}
}
});
if (flag2) {
  var flag = confirm ( "確認はしましたか？\n\n【 重要 】価格構成要素を変更した場合は、先に「注文内容確認」ボタンを押す必要があります。\n\n戻る場合は [キャンセル] ボタンをクリックしてください。"); 
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
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    _flag = true; 
    //document.edit_order.submit();
  }else{
  alert("パスワードが違います");
  $("input[name=update_viladate]").val('_false');
  document.edit_order.submit();
//  alert("更新をキャンセルしました。");
  return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    _flag = true;
    //document.edit_order.submit();
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

  if (window.confirm("注文内容を確認しますか？")) {

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
var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("パスワードが違います");
document.edit_order.submit();
//alert("更新をキャンセルしました。");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{  
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});



} else {
  window.alert("注文内容確認をキャンセルしました。\n\n【 重要 】メールは送信されていません。【 重要 】");
}

}
function update_price2() {

  if (window.confirm("注文内容を確認しますか？")) {
    document.edit_order.notify.checked = false;
    document.edit_order.notify_comments.checked = false;
    // 如果减少购买量则提示保存位置
    $('.update_products_qty').each(function(){
        old = $('#'+$(this).attr('id').replace('_new_qty_', '_qty_'));
        if(parseInt(old.val()) > parseInt($(this).val())){
        pid = $(this).attr('id').substr($(this).attr('id').indexOf('_qty_')+5);
        //alert(pid);
        if (window.confirm($('#update_products_name_'+pid).val()+" "+(old.val() - $(this).val())+"個を実在個に保存しますか？架空在庫に保存しますか？\n\n「OK」なら実在庫、「キャンセル」なら架空在庫に足されます")) {
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
var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("パスワードが違います");
document.edit_order.submit();
//alert("更新をキャンセルしました。");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});


// delete   document.edit_order.submit();
} else {
  window.alert("注文内容確認をキャンセルしました。\n\n【 重要 】メールは送信されていません。【 重要 】");
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
  if(!re.test(obj.value)){
  //replace all un number and '.'
  obj.value = obj.value.replace(/[^\d.]/g,"");
  //first char must be number
  obj.value = obj.value.replace(/^\./g,"");
  //only one '.'
  obj.value = obj.value.replace(/\.{2,}/g,".");
  obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
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
      var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
      if(in_array(pwd, pwd_arr)){
        window.location.href = url_str+'&once_pwd='+pwd; 
      } else {
        window.alert("パスワードが違います"); 
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
      var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
      if(in_array(pwd, pwd_arr)){
        window.location.href = url_str+'&once_pwd='+pwd; 
      } else {
        window.alert("パスワードが違います"); 
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
        alert("既に登録されているため使用できません。");
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
        alert("禁止記号は全て「-」に置き換えられます");
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
        alert("既に登録されているため使用できません。");
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
        alert("禁止記号は全て「-」に置き換えられます");
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
        alert("移動先に同じURLが登録されているため移動できません");
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
        alert("移動先に同じURLが登録されているため移動できません");
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
        alert("コピー先に同じURLが登録されているためコピー出来ません");
      }
    }
  });
  return flag;
}

function replace_romaji(romaji){
  //replace & + to a string
  romaji = romaji.replace(/\&/g,'<11111111>');
  romaji = romaji.replace(/\+/g,'<22222222>');
  return romaji;
}
function c_is_set_error_char(){
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
        alert("禁止記号は全て「-」に置き換えられます");
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
        alert("禁止記号は全て「-」に置き換えられます");
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
        alert("既に登録されているため使用できません。");
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
        alert("既に登録されているため使用できません。");
      }
    }
  });
  return flag;
}
function products_form_validator(cid,qid,site_id){
  flag1 = p_is_set_romaji(cid,qid,site_id);
  flag2 = p_is_set_error_char(); 
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
  /* 確認ダイアログ表示 */ 
  var flag = confirm ( "確認はしましたか？\n\n【 重要 】価格構成要素を変更した場合は、先に「注文内容確認」ボタンを押す必要があります。\n\n戻る場合は [キャンセル] ボタンをクリックしてください。"); 
  /* send_flg が TRUEなら送信、FALSEなら送信しない */ 
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
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    document.edit_order.submit();
  }else{
  alert("パスワードが違います");
  $("input[name=update_viladate]").val('_false');
  document.edit_order.submit();
//  alert("更新をキャンセルしました。");
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

  if (window.confirm("注文内容を確認しますか？")) {

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
var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("パスワードが違います");
document.edit_order.submit();
//alert("更新をキャンセルしました。");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{  
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});



} else {
  window.alert("注文内容確認をキャンセルしました。\n\n【 重要 】メールは送信されていません。【 重要 】");
}

}
function pre_update_price2() {

  if (window.confirm("注文内容を確認しますか？")) {
    document.edit_order.notify.checked = false;
    document.edit_order.notify_comments.checked = false;
    // 如果减少购买量则提示保存位置
    $('.update_products_qty').each(function(){
        old = $('#'+$(this).attr('id').replace('_new_qty_', '_qty_'));
        if(parseInt(old.val()) > parseInt($(this).val())){
        pid = $(this).attr('id').substr($(this).attr('id').indexOf('_qty_')+5);
        //alert(pid);
        if (window.confirm($('#update_products_name_'+pid).val()+" "+(old.val() - $(this).val())+"個を実在個に保存しますか？架空在庫に保存しますか？\n\n「OK」なら実在庫、「キャンセル」なら架空在庫に足されます")) {
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
var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
if(in_array(pwd,pwd_arr)){
$("input[name=update_viladate]").val(pwd);
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}else{
$("input[name=update_viladate]").val('_false');
alert("パスワードが違います");
document.edit_order.submit();
//alert("更新をキャンセルしました。");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
}
}else{
$("input[name=update_viladate]").val('');
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;

}
}
});


// delete   document.edit_order.submit();
} else {
  window.alert("注文内容確認をキャンセルしました。\n\n【 重要 】メールは送信されていません。【 重要 】");
}
}
