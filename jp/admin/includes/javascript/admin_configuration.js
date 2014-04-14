$(document).ready(function() {
  //listen keydown
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc
      if ($('#show_text_configuration').css('display') != 'none') {
        hidden_info_box();
      }
    }
     if (event.which == 13) {
           //press enter
        if ($('#show_text_configuration').css('display') != 'none') {
               $("#show_text_configuration").find('input:submit').first().trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_text_configuration').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      }
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_text_configuration').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      }
    }
  });
});

function show_text_configuration(ele,gID,cID,site_id){
 $.ajax({
 url: 'ajax.php?&action=edit_configuration',
 data: {gID:gID,cID:cID,site_id:site_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_text_configuration").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_text_configuration').height()){
offset = ele.offsetTop+$("#show_configuration_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_text_configuration').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_text_configuration').height()) > $('.box_warp').height())&&($('#show_text_configuration').height()<ele.offsetTop+parseInt(head_top)-$("#show_configuration_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_configuration_list").position().top-1-$('#show_text_configuration').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_configuration_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_configuration_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_configuration_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_text_configuration').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_text_configuration').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
}
$('#show_text_configuration').css('z-index','1');
$('#show_text_configuration').css('left',leftset);
$('#show_text_configuration').css('display', 'block');
  }
  }); 
}
function hidden_info_box(){
$('#show_text_configuration').css('display','none');
}

//是否输入一次性密码?>
function update_configuration_info(c_permission)
{
  if (c_permission == 31) {
    document.forms.configuration.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<' + self_configuration, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.configuration.submit();
        } else {
          var input_pwd_str = window.prompt(onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.configuration.action),
              async: false,
              success: function(msg_info) {
                document.forms.configuration.submit();
              }
            }); 
          } else {
            alert(onetime_pwd_error); 
          }
        }
      }
    });
  }
}
//Invalid setup
function set_invalid_configuration(c_permission, gid_info, cid_info)
{
  if (c_permission == 31) {
    window.location.href = invalid_herf+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+self_configuration, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = invalid_herf+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
        } else {
          var input_pwd_str = window.prompt(onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(invalid_herf+'?action=tdel&gID='+gid_info+'&cID='+cid_info),
              async: false,
              success: function(msg_info) {
                window.location.href = invalid_herf+'?action=tdel&gID='+gid_info+'&cID='+cid_info; 
              }
            }); 
          } else {
            alert(onetime_pwd_error); 
          }
        }
      }
    });
  }
}
//Update form
function new_update_configuration_info(c_permission)
{
  var tmp_input_value = $('#setting_text').val(); 
  $.ajax({
    url: 'ajax_orders.php?action=check_is_numeric',   
    type: 'POST',
    dataType: 'text',
    data: 'o_param='+tmp_input_value, 
    async: false,
    success: function(msg_info) {
      if (msg_info != '') {
        alert(msg_info); 
      } else {
        if (c_permission == 31) {
          document.forms.configuration.submit();
        } else {
          $.ajax({
            url: 'ajax_orders.php?action=getallpwd',   
            type: 'POST',
            dataType: 'text',
            data: 'current_page_name='+self_configuration, 
            async: false,
            success: function(msg) {
              var tmp_msg_arr = msg.split('|||'); 
              var pwd_list_array = tmp_msg_arr[1].split(',');
              if (tmp_msg_arr[0] == '0') {
                document.forms.configuration.submit();
              } else {
                var input_pwd_str = window.prompt(onetime_pwd, ''); 
                if (in_array(input_pwd_str, pwd_list_array)) {
                  $.ajax({
                    url: 'ajax_orders.php?action=record_pwd_log',   
                    type: 'POST',
                    dataType: 'text',
                    data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.configuration.action),
                    async: false,
                    success: function(msg_info) {
                      document.forms.configuration.submit();
                    }
                  }); 
                } else {
                  alert(onetime_pwd_error); 
                }
              }
            }
          });
        }   
      }
    }    
  }); 
}
