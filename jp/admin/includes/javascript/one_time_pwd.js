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
if(data.search(/MySQL server has gone away/i)==-1){
pwd =  window.prompt("ワンタイムパスワードを入力してください\r\n","");
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
alert("パスワードが違います");
location=location;
//跳霓ｬ髞呵ｯｯ鬘ｵ
}
}else{
alert("SQL ERROR CLOST THIS TAB WILL CLOST THIS PAGE");
self.close();
}
}
}
});
}
