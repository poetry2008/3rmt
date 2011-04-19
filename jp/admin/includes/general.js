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
$("input[name$=\[final_price\]]").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*1.1)){
  flag_tmp=false;
  }
  });
  if(!false){
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
  return true;
  }else{
  alert("パスワードが違います");
  return false;
  }
  }else{
    return true;
  }
}
});
}else
return false;
}
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
$("input[name$=\[final_price\]]").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  var p_name = "input[name^=update_products][name*="+op_id+"][name$=\[name\]]";
  if(input_val > Math.abs(final_val*1.1)&&flag_tmp){
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n"+$(p_name).val(),"");
  var t_str = "input[name^=update_products][name*="+op_id+"][name$=\[pwd\]]";
  if(in_array(pwd,pwd_arr)){
  $(t_str).val(pwd);
  }else{
  $(t_str).val('_false');
  flag_tmp=false;
  alert("パスワードが違います");
  }
  }
  });
document.edit_order.submit();
}
});
}
flag = false;
return flag; 
} else {
  location.href='create_order2.php';
}
} 

function update_price() {

  if (window.confirm("注文内容を確認しますか？")) {


    $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$("input[name$=\[final_price\]]").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  var p_name = "input[name^=update_products][name*="+op_id+"][name$=\[name\]]";
  if(input_val > Math.abs(final_val*1.1)&&flag_tmp){
  var pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n"+$(p_name).val(),"");
  var t_str = "input[name^=update_products][name*="+op_id+"][name$=\[pwd\]]";
  if(in_array(pwd,pwd_arr)){
  $(t_str).val(pwd);
  }else{
  $(t_str).val('_false');
  flag_tmp=false;
  alert("パスワードが違います");
  }
  }
  });
document.edit_order.submit();
window.alert("注文内容を更新しました。合計金額を必ず確認してください。\n\n【 重要 】メールは送信されていません。【 重要 】");
document.edit_order.notify.checked = true;
document.edit_order.notify_comments.checked = false;
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
$("input[name$=\[final_price\]]").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*1.1)){
  flag_tmp = false;
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
  alert("パスワードが違います");
}
}else{
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
