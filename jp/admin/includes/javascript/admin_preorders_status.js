//submit form
function check_status_form() 
{
  if (js_preorders_status_npermission == 31) {
  document.forms.preorders_status.submit(); 
  } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_preorders_status_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.preorders_status.submit(); 
      } else {
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.preorders_status.action),
            async: false,
            success: function(msg_info) {
              document.forms.preorders_status.submit(); 
            }
          }); 
        } else {
          alert(js_onetime_error); 
        }
      }
    }
  });
  }
}
