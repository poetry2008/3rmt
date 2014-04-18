//open calendar
function open_new_calendar(c_type)
{
  var is_open = $('#toggle_open_'+c_type).val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open_'+c_type).val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar_"+c_type,
            width:'170px',

        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        if (c_type == 'start') {
          $("#start").val(dtdate.format(newDate)); 
        } else {
          $("#end").val(dtdate.format(newDate)); 
        }
        $('#toggle_open_'+c_type).val('0');
        $('#toggle_open_'+c_type).next().html('<div id="mycalendar_'+c_type+'"></div>');
      });
    });
  }
}
//when choose calendar finished, check the search
function check_search_form()
{
  start_str = document.getElementById('start').value; 
  end_str = document.getElementById('end').value; 
  
  $.ajax({
    url: 'reset_pwd.php?action=check_search', 
    type:'POST',  
    data:'start='+start_str+'&end='+end_str,
    async:false,
    success: function(msg) {
      if (msg != '') {
        alert(msg); 
      } else {
        document.forms.search.submit(); 
      }
    }
  });
}
//reset calendar
function reset_customers_pwd(c_permission) {
  $.ajax({
    url: 'reset_pwd.php?action=reset_all',
    type: 'POST',
    async:false,
    success: function(msg) {
      if (c_permission == 31) {
        window.location.href = window.location.href; 
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_reset_pwd_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              window.location.href = window.location.href; 
            } else {
              var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_reset_pwd_href),
                  async: false,
                  success: function(msg_info) {
                    window.location.href = window.location.href; 
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
  });
}
//submit form
function check_reset_pwd_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.rp_form.submit(); 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_reset_pwd_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.rp_form.submit(); 
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.rp_form.action),
              async: false,
              success: function(msg_info) {
                document.forms.rp_form.submit(); 
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
