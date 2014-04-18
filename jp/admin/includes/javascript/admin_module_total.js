var box_warp_height = 0;
var origin_offset_symbol = 0;
window.onresize = resize_total_page;
//zoom
function resize_total_page()
{
  if ($(".box_warp").height() < ($(".compatible").height() + $("#show_popup_info").height())) {
    $(".box_warp").height($(".compatible").height() + $("#show_popup_info").height()); 
  }
}
//popup page
function show_popup_info(ele, current_module, other_param_str, module_list_info) {
  ele = ele.parentNode;
  other_param_str = decodeURIComponent(other_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_module_total',     
    data: 'current_module='+current_module+'&list_info='+module_list_info+'&'+other_param_str,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      head_top = $('.compatible_head').height();
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (ele.offsetTop < $('#show_popup_info').height()) {
           offset = ele.offsetTop+$("#total_list_box").position().top+ele.offsetHeight+head_top;
           box_warp_height = offset-head_top;
         } else {
           if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
             offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
           } else {
             offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
             offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
           }
           box_warp_height = offset-head_top;
         }
       } else {
        if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
        } else {
          offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
          offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
      }
      $('#show_popup_info').css('top',offset);
      } else {
      if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
        if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
        } else {
          offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
          offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
        box_warp_height = offset-head_top;
      } else {
        offset = ele.offsetTop+$("#total_list_box").position().top+ele.offsetHeight+head_top;
        box_warp_height = offset-head_top;
      }
      $('#show_popup_info').css('top',offset);
      }

      
      if ($('.show_left_menu').width()) {
        leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
      } else {
        leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
      }
      
      $('#show_popup_info').css('left',leftset);
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show();
    }
  }); 
  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}
//hide popup page
function hidden_info_box(){
  $('#show_popup_info').html(''); 
  $('#show_popup_info').css('display','none');
  $(".box_warp").height(box_warp_height); 
}
//display info
function show_module_total_info(current_module, other_param_str) {
  other_param_str = decodeURIComponent(other_param_str);
  $.ajax({
    url: 'ajax.php?action=edit_module_total',     
    data: 'current_module='+current_module+'&'+other_param_str,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      
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
$(function() {
  box_warp_height = $(".box_warp").height();    
});
//listen event
$(document).ready(function() {
  $(document).keyup(function(event) {
     if (event.which == 27) {
       if ($("#show_popup_info").css("display") != "none") {
         hidden_info_box();
       }
     }
  
     if (event.which == 13) {
       if ($("#show_popup_info").css("display") != "none") {
         $("#button_save").trigger("click"); 
       }
     }
     
     if (event.ctrlKey && event.which == 37) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#total_prev")) {
           $("#total_prev").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 39) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#total_next")) {
           $("#total_next").trigger("click"); 
         }
       }
     }
  });    
});

