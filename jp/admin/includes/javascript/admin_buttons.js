//shortcut key listen
$(document).ready(function() {
var box_warp_height = $(".box_warp").height();
  $(document).keyup(function(event) {
    if (event.which == 27) {
      if ($("#show_popup_info").css("display") != "none") {
        hidden_info_box();     
        o_submit_single = true;
      }
    }
    if (event.which == 13) {
      if ($("#show_popup_info").css("display") != "none") {
        if (o_submit_single) {
          $("#button_save").trigger("click");  
        }
      }
    }
    
    if (event.ctrlKey && event.which == 37) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#buttons_prev")) {
          $("#buttons_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#buttons_next")) {
          $("#buttons_next").trigger("click");
        }
      }
    }
  });    
});
var origin_offset_symbol = 0;
window.onresize = resize_option_page;
var o_submit_single = true;
//window zoom event
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
  box_warp_height = $(".box_warp").height(); 
}

//delete action
function select_buttons_change(value,buttons_list_id,c_permission)
{
  sel_num = 0;
  if (document.edit_buttons_form.elements[buttons_list_id].length == null) {
    if (document.edit_buttons_form.elements[buttons_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_buttons_form.elements[buttons_list_id].length; i++) {
      if (document.edit_buttons_form.elements[buttons_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm(js_buttons_edit_confirm)) {
      if (c_permission == 31) {
        document.edit_buttons_form.action = js_buttons_href;
        document.edit_buttons_form.submit(); 
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_buttons_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.edit_buttons_form.action = js_buttons_href;
              document.edit_buttons_form.submit(); 
            } else {
              var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_buttons_href),
                  async: false,
                  success: function(msg_info) {
                    document.edit_buttons_form.action = js_buttons_href;
                    document.edit_buttons_form.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("edit_buttons_list")[0].value = 0;
                alert(js_onetime_error); 
              }
            }
          }
        });
      }
    }else{
      document.getElementsByName("edit_buttons_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_buttons_list")[0].value = 0;
    alert(js_buttons_must_select); 
  }
}

//all selected action
function all_select_buttons(buttons_list_id)
{
  var check_flag = document.edit_buttons_form.all_check.checked;
  if (document.edit_buttons_form.elements[buttons_list_id]) {
    if (document.edit_buttons_form.elements[buttons_list_id].length == null) {
      if (check_flag == true) {
        document.edit_buttons_form.elements[buttons_list_id].checked = true;
      } else {
        document.edit_buttons_form.elements[buttons_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_buttons_form.elements[buttons_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_buttons_form.elements[buttons_list_id][i].checked = true;
        } else {
          document.edit_buttons_form.elements[buttons_list_id][i].checked = false;
        }
      }
    }
  }
}

//edit buttons info
function show_buttons_info(ele, buttons_id, i_param_str, show)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_buttons',      
    data: 'buttons_id='+buttons_id+'&param_str='+i_param_str+'&show='+show,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#buttons_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#buttons_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#buttons_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      o_submit_single = true;
    }
  });

  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}

//edit buttons prev and next info
function show_link_buttons_info(buttons_id, param_str, show)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_buttons',      
    data: 'buttons_id='+buttons_id+'&param_str='+param_str+'&show='+show,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}

//hide popup page
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}

//save buttons content verifiaction
function edit_buttons_check(action, c_permission){

  var buttons_name = document.getElementsByName("buttons_name")[0];
  var buttons_name_value = buttons_name.value;
  buttons_name_value = buttons_name_value.replace(/\s/g,"");

  if(buttons_name_value == ''){

    $("#buttons_name_error").html('&nbsp;<font color="#FF0000">'+js_buttons_must_input+'</font>');
  }else{
    if(action == 'save'){
      if (c_permission == 31) {
        document.edit_buttons.action = js_buttons_ws_admin+'?action='+action;
        document.edit_buttons.submit();
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_buttons_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.edit_buttons.action = js_buttons_ws_admin+'?action='+action;
              document.edit_buttons.submit();
            } else {
              $('#button_save').attr('id', 'tmp_button_save'); 
              var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_buttons_ws_admin+'?action='+action),
                  async: false,
                  success: function(msg_info) {
                    document.edit_buttons.action = js_buttons_ws_admin+'?action='+action;
                    document.edit_buttons.submit();
                  }
                }); 
              } else {
                alert(js_onetime_error); 
                setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
              }
            }
          }
        });
      }
    }else{
      if (c_permission == 31) {
        document.create_buttons.action = js_buttons_ws_admin+'?action='+action; 
        document.create_buttons.submit();
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_buttons_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.create_buttons.action = js_buttons_ws_admin+'?action='+action; 
              document.create_buttons.submit();
            } else {
              $('#button_save').attr('id', 'tmp_button_save'); 
              var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_buttons_ws_admin+'?action='+action),
                  async: false,
                  success: function(msg_info) {
                    document.create_buttons.action = js_buttons_ws_admin+'?action='+action; 
                    document.create_buttons.submit();
                  }
                }); 
              } else {
                alert(js_onetime_error); 
                setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
              }
            }
          }
        });
      }
    } 
  }
}

//delete buttons
function delete_buttons(c_permission){
  if (c_permission == 31) {
    document.edit_buttons.action = js_buttons_href_deleteconfirm;
    document.edit_buttons.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_buttons_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.edit_buttons.action = js_buttons_href_deleteconfirm;
          document.edit_buttons.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_buttons_href_deleteconfirm),
              async: false,
              success: function(msg_info) {
                document.edit_buttons.action = js_buttons_href_deleteconfirm;
                document.edit_buttons.submit();
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
}

//new buttons
function create_buttons_info(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax.php?action=create_buttons',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  }); 
}
