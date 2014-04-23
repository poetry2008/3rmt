function check_send_mail()
{
   alert(js_newsletters_mail_text);
}
//submit action
function check_letter_form(c_permission, l_type)
{
  if (c_permission == 31) {
    if (l_type == 0) {
      document.forms.newsletter.submit(); 
    } else {
      document.forms.newsletters.submit(); 
    }
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_newsletters_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          if (l_type == 0) {
            document.forms.newsletter.submit(); 
          } else {
            document.forms.newsletters.submit(); 
          }
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          var form_action_str = ''; 
          if (l_type == 0) {
            form_action_str = document.forms.newsletter.action; 
          } else {
            form_action_str = document.forms.newsletters.action; 
          }
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
              async: false,
              success: function(msg_info) {
                if (l_type == 0) {
                  document.forms.newsletter.submit(); 
                } else {
                  document.forms.newsletters.submit(); 
                }
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
//execute action
function toggle_letter_action(l_url_str, c_permission)
{
  if (c_permission == 31) {
    window.location.href = l_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_newsletters_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = l_url_str; 
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(l_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = l_url_str; 
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
