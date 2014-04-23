window.onresize = resize_meta_page;
//zoom page
function resize_meta_page()
{
  var s_offset = $('#show_popup_info').css('top'); 
  s_offset = s_offset.replace('px', '');
  tmp_s_offset = parseInt(s_offset, 10)
  if ($('#show_popup_info').height() + tmp_s_offset > $('.box_warp').height()) {
    $('.box_warp').height($('#show_popup_info').height() + tmp_s_offset); 
  }
}

//close popup page
function close_meta_info()
{
  $('#show_popup_info').html('');
  $('#show_popup_info').css('display', 'none');
  $('#show_popup_info').css('top', '');
  $('.box_warp').height('');
}

//submit form
function submit_meta_form()
{
  if (js_configuration_meta_npermission > 15) {
  document.forms.meta_form.submit();
  } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_configuration_meta_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.meta_form.submit();
      } else {
        $("#button_save").attr('id', 'tmp_button_save'); 
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.meta_form.action),
            async: false,
            success: function(msg_info) {
              document.forms.meta_form.submit();
            }
          }); 
        } else {
          alert(js_onetime_error); 
          setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
        }
      }
    }
  }); 
  }
}

//popup page
function show_meta_info(ele, meta_id, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (ele.offsetTop < $('#show_popup_info').height()) {
           offset = ele.offsetTop+$("#meta_list_box").position().top+ele.offsetHeight;
           box_warp_height = offset;
         } else {
           if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
             offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
           } else {
             offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
             offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
           }
           box_warp_height = offset;
         }
       } else {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
      }
      $('#show_popup_info').css('top',offset);
      } else {
      if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
        box_warp_height = offset;
      } else {
        offset = ele.offsetTop+$("#meta_list_box").position().top+ele.offsetHeight;
        box_warp_height = offset;
      }
      $('#show_popup_info').css('top',offset);
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

//display meta info
function show_link_meta_info(meta_id, other_param)
{
  other_param = decodeURIComponent(other_param);
  $.ajax({
    url: 'ajax.php?action=edit_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+other_param,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
      o_submit_single = true;
    
      if (origin_offset_symbol == 1) {
        c_offset = $("#show_popup_info").css("top");
        c_offset = c_offset.replace('px', '');
        tmp_c_offset = parseInt(c_offset, 10); 
        $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
      } else {
        $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
      }
    } 
  });
}
//copy meta
function copy_meta(meta_id, other_param)
{
  other_param = decodeURIComponent(other_param);
  $.ajax({
    url: 'ajax.php?action=copy_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+other_param,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
      o_submit_single = true;
    } 
  });
}

//check form
function check_copy_meta()
{
  var check_flag = true; 
  if (document.copy_meta.elements['select_site[]']) {
    if (document.copy_meta.elements['select_site[]'].length == null) {
      if (document.copy_meta.elements['select_site[]'].checked == true) {
        check_flag = false;
      }
    } else {
      for (var i = 0; i < document.copy_meta.elements['select_site[]'].length; i++) {
        if (document.copy_meta.elements['select_site[]'][i].checked == true) {
          check_flag = false; 
        }
      }
    }
  } 
  if (check_flag == false) {
  if (js_configuration_meta_npermission > 15) {
  document.forms.copy_meta.submit();
  } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_configuration_meta_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.copy_meta.submit();
      } else {
        $("#button_save").attr('id', 'tmp_button_save'); 
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.copy_meta.action),
            async: false,
            success: function(msg_info) {
              document.forms.copy_meta.submit();
            }
          }); 
        } else {
          alert(js_onetime_error); 
          setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
        }
      }
    }
  });
  }
  } else {
    $('#site_error').html(js_configuration_meta_site_waring); 
  }
}

$(function() {
  box_warp_height = $('.box_warp').height();    
});

//listen event
$(document).ready(function() {
  $(document).keyup(function(event) {
     if (event.which == 27) {
       if ($("#show_popup_info").css("display") != "none") {
         close_meta_info();
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
         if ($("#meta_prev")) {
           $("#meta_prev").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 39) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#meta_next")) {
           $("#meta_next").trigger("click"); 
         }
       }
     }
  });    
});

