function one_time_pwd(page_name, redirect_back_url, notice_onetime, error_onetime){
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
pwd =  window.prompt(notice_onetime,"");
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
  if (window.confirm(error_onetime)) {
    one_time_pwd(page_name, redirect_back_url, notice_onetime, error_onetime); 
  } else {
    window.location.href = decodeURIComponent(redirect_back_url);
  }
}
}else{
  location.href='/admin/sql_error.php';
}
}
}
});
}
