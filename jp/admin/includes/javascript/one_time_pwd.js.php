function one_time_pwd(page_name){
  $.ajax({
url: 'ajax_orders.php?action=getpwdcheckbox',
type: 'POST',
data: 'page_name='+page_name,
dataType: 'text',
async : false,
success: function(data) {
if(data !='false'){
var pwd_arr = data.split(",");
if(data.indexOf('[SQL-ERROR]')==-1){
pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
if(in_array(pwd,pwd_arr)){
$.ajax({
url: 'ajax_orders.php?action=save_pwd_log',
type: 'POST',
data: 'one_time_pwd='+pwd+'&page_name='+page_name,
dataType: 'text',
async : false,
success: function(_data) {
}
});
}else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  location=location;
}
}else{
  location.href='/admin/sql_error.php';
}
}
}
});
}
