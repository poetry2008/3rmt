//submit action
function check_template_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.bill_templates.submit(); 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_bill_templates_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.bill_templates.submit(); 
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.bill_templates.action),
              async: false,
              success: function(msg_info) {
                document.forms.bill_templates.submit(); 
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
