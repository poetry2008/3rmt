//listen shortcut keys
$(document).ready(function() { 
box_warp_height = $(".box_warp").height();
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
        if ($("#mail_prev")) {
          $("#mail_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#mail_next")) {
          $("#mail_next").trigger("click");
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

//edit mail templates info
function show_mail_info(ele, mail_id, i_param_str, url)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_mail',      
    data: 'mail_id='+mail_id+'&param_str='+i_param_str+'&url='+url+js_mail_templates_search+js_mail_templates_order,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
          offset = ele.offsetTop+$('#mail_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#mail_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#mail_list_box').position().top+ele.offsetHeight;
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

//edit prev and next mail info
function show_link_mail_info(mail_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_mail',      
    data: 'mail_id='+mail_id+'&param_str='+param_str+js_mail_templates_search+js_mail_templates_order,
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

//mail content edit
function edit_mail_check(c_permission){

  var error = false;
  var mail_name = document.getElementsByName("templates_title")[0];
  var mail_name_value = mail_name.value;
  mail_name_value = mail_name_value.replace(/\s/g,"");
  if(mail_name_value == ''){

    error = true; 
    $("#mail_name_error").html('&nbsp;<font color="#FF0000">'+js_mail_templates_must_input+'</font>');
  }else{
    $("#mail_name_error").html(js_mail_templates_field_required); 
  }
  var mail_title = document.getElementsByName("title")[0];
  var mail_title_value = mail_title.value;
  mail_title_value = mail_title_value.replace(/\s/g,"");
  if(mail_title_value == ''){

    error = true; 
    $("#mail_title_error").html('&nbsp;<font color="#FF0000">'+js_mail_templates_must_input+'</font>');
  }else{
    $("#mail_title_error").html(js_mail_templates_field_required); 
  }
  var mail_contents = document.getElementsByName("contents")[0];
  var mail_contents_value = mail_contents.value;
  mail_contents_value = mail_contents_value.replace(/\s/g,"");
  if(mail_contents_value == ''){

    error = true; 
    $("#mail_contents_error").html('&nbsp;<font color="#FF0000">'+js_mail_templates_must_input+'</font>');
  }else{
    $("#mail_contents_error").html(js_mail_templates_field_required); 
  }
  if(error == false){
  if (c_permission == 31) {
    document.edit_mail.action = js_mail_templates_href;
    document.edit_mail.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_mail_templates_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.edit_mail.action = js_mail_templates_href;
          document.edit_mail.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_mail_templates_href),
              async: false,
              success: function(msg_info) {
                document.edit_mail.action = js_mail_templates_href;
                document.edit_mail.submit();
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

//mail effective or invalid
function valid_mail_check(c_permission){
 
  if (c_permission == 31) {
    document.edit_mail.action = js_mail_templates_href_valid;
    document.edit_mail.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_mail_templates_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.edit_mail.action = js_mail_templates_href_valid;
          document.edit_mail.submit();
        } else {
          $('#button_valid').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_mail_templates_href_valid),
              async: false,
              success: function(msg_info) {
                document.edit_mail.action = js_mail_templates_href_valid;
                document.edit_mail.submit();
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_valid'), 1); 
          }
        }
      }
    });
  }
}
