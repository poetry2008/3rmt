//check form info
function valadate_search(){
  if(
  !$("input[name=se_pname]").val()&&
  !$("input[name=se_mail]").val()&&
  !$("input[name=se_cname]").val()&&
  !$("input[name=se_site]").val()
  ){
    alert(js_mail_one_search);
    return false;
  }else{
    return true;
  }

}
//put mail info into session
function save_mail_info(){
  mail_info_from = $("#mail_info_from").val();
  mail_info_subject = $("#mail_info_subject").val();
  mail_info_message = $("#mail_info_message").val();
  $.ajax({
    url: 'ajax_orders.php?action=save_mail_info',
    data: 'mail_info_from='+mail_info_from+'&mail_info_subject='+mail_info_subject+'&mail_info_message='+mail_info_message,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
    }
  });
}
//change user of send mail
function change_select_mail(_this){
  if($(_this).attr('checked')){
    mail_list_action = 'add';
  }else{
    mail_list_action = 'sub';
  }
  $.ajax({
    url: 'ajax_orders.php?action=change_mail_list',
    data: 'mail_list_value='+_this.value+'&mail_list_action='+mail_list_action,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
    }
  });
}
//back to previous page
function back_to_mail(){
  document.mail.back_mail.value = 'back';
  document.mail.submit();
}
//verification of send mail
function send_mail_validate(){
  save_mail_info();
  var flag_checkbox = true;
  $.ajax({
    url: 'ajax_orders.php?action=mail_checkbox_validate',
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
      if(data == 'true'){
        flag_checkbox=false;
      }
    }
  });
  if(flag_checkbox){
    return true;
  }else{
    alert(js_mail_no_selected_checkbox);
    return false; 
  }
}
