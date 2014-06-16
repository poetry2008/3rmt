//close popup page
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}
//count popup position
function info_box_set(ele, current_belong)
{
  $.ajax({
    type:'POST',
    dataType: 'text',
    url: 'ajax_orders.php?action=get_top_layer',
    data: 'current_belong='+current_belong,
    async:false,
    success: function(msg) {
      ele = ele.parentNode;
      head_top = $('.compatible_head').height();
      box_warp_height = 0;
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          if (ele.offsetTop < $('#show_popup_info').height()) {
            offset = ele.offsetTop+$("#pic_list_table").position().top+ele.offsetHeight+head_top;
            box_warp_height = offset-head_top;
          } else {
            if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
              offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
            } else {
              offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
              offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
            }
            box_warp_height = offset-head_top;
          }
        } else {
          if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
            offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
          } else {
            offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
            offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
          }
        }
        $('#show_popup_info').css('top',offset);
     } else {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#pic_list_table").position().top-1)) {
           offset = ele.offsetTop+$("#pic_list_table").position().top-1-$('#show_popup_info').height()+head_top;
         } else {
           offset = ele.offsetTop+$("#pic_list_table").position().top+$(ele).height()+head_top;
           offset = offset + parseInt($('#pic_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
         }
         box_warp_height = offset-head_top;
       } else {
         offset = ele.offsetTop+$("#pic_list_table").position().top+ele.offsetHeight+head_top;
         box_warp_height = offset-head_top;
       }
       $('#show_popup_info').css('top',offset);
     }
     box_warp_height = box_warp_height + $('#show_popup_info').height();
     if ($('.show_left_menu').width()) {
       leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
     } else {
       leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
     }
     $('#show_popup_info').css('z-index', msg);
     $('#show_popup_info').css('left',leftset);
     if ($('.compatible').height()<box_warp_height) {
       $('.box_warp').css('height',box_warp_height);
     } else {
       $('.box_warp').css('height',$('.compatible').height());
     }
   }
  });
}
//open popup page
function show_popup_info(ele, pic_id)
{
  url_str = 'ajax.php?action=show_update_pic_info';
  data_str = 'pic_id='+pic_id;
  $.ajax({
    type:'POST',    
    dataType:'text',
    async:false,
    url:url_str,
    data:data_str,
    success: function(msg) {
      $('#show_popup_info').html(msg);
      if (ele != '') {
        info_box_set(ele, js_marks_belong); 
      }
      $('#show_popup_info').css('display', 'block'); 
    }
  });
}
//submit action
function toggle_marks_form(c_permission)
{
   if (c_permission == 31) {
     document.forms.pic.submit(); 
   } else {
     $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_marks_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.pic.submit(); 
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.pic.action),
              async: false,
              success: function(msg_info) {
                document.forms.pic.submit(); 
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
