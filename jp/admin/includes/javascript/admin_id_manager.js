//execute action
function toggle_idpw_log_action(idpwd_url_str, c_permission)
{
if (confirm(js_id_manager_del_pw)) {
  if (c_permission == 31) {
    window.location.href = idpwd_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_id_manager_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = idpwd_url_str; 
        } else {
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(idpwd_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = idpwd_url_str; 
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
}
//ajax popup page
function show_pw_manager_log(ele,pw_id,page,site_id,pw_l_id){
 sort = document.getElementById('pw_manager_sort').value;
 type = document.getElementById('pw_manager_type').value;
 search_type = document.getElementById('pw_manager_search_type').value;
 keywords = document.getElementById('pw_manager_keywords').value;
 $.ajax({
 url: 'ajax.php?&action=edit_pw_manager_log',
 data: {pw_id:pw_id,page:page,site_id:site_id,sort:sort,type:type,pw_l_id:pw_l_id,keywords:keywords,search_type:search_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_pw_manager_log").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_pw_manager_log').height()){
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
} else {
offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_pw_manager_log').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_pw_manager_log').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_pw_manager_log').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
$('#show_pw_manager_log').css('z-index','1');
$('#show_pw_manager_log').css('left',leftset);
$('#show_pw_manager_log').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
//confirm delete data
function delete_select_pw_manager(pw_manager_str, c_permission){
         sel_num = 0;
         if (document.del_pw_manager_log.elements[pw_manager_str].length == null) {
                if (document.del_pw_manager_log.elements[pw_manager_str].checked == true){
                     sel_num = 1;
                 }
          } else {
             for (i = 0; i < document.del_pw_manager_log.elements[pw_manager_str].length; i++) {
                 if(document.del_pw_manager_log.elements[pw_manager_str][i].checked == true) {
                     sel_num = 1;
                     break;
                  }
            }
         }
         if (sel_num == 1) {
          if (confirm(js_id_manager_del_pw)) {
            if (c_permission == 31) {
              document.forms.del_pw_manager_log.submit(); 
            } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                data: 'current_page_name='+js_id_manager_self, 
                async: false,
                success: function(msg) {
                  var tmp_msg_arr = msg.split('|||'); 
                  var pwd_list_array = tmp_msg_arr[1].split(',');
                  if (tmp_msg_arr[0] == '0') {
                    document.forms.del_pw_manager_log.submit(); 
                  } else {
                    var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                    if (in_array(input_pwd_str, pwd_list_array)) {
                      $.ajax({
                        url: 'ajax_orders.php?action=record_pwd_log',   
                        type: 'POST',
                        dataType: 'text',
                        data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_pw_manager_log.action),
                        async: false,
                        success: function(msg_info) {
                          document.forms.del_pw_manager_log.submit(); 
                        }
                      }); 
                    } else {
                      document.getElementsByName('pw_manager_action')[0].value = 0;
                      alert(js_onetime_error); 
                    }
                  }
                }
              });
            }
          }else{
             document.getElementsByName('pw_manager_action')[0].value = 0;
          }
         } else {
                 document.getElementsByName('pw_manager_action')[0].value = 0;
                 alert(js_id_manager_must_select); 
        }
    }
//choose action
function pw_manager_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_pw_manager(r_str, js_id_manager_npermission);
  }
}
$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc
      if ($('#show_pw_manager_log').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_pw_manager_log').css('display') != 'none') {
            if (o_submit_single){
               $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_pw_manager_log').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_pw_manager_log').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
//close popup page
function hidden_info_box(){
   $('#show_pw_manager_log').css('display','none');
}
//search form submit
function search_type_changed(elem){
   if ($('#keywords').val() && elem.selectedIndex != 0) 
        document.forms.orders1.submit();
}
//choose delete all data
function all_select_pw_manager_log(pw_manager_str){
        var check_flag = document.del_pw_manager_log.all_check.checked;
           if (document.del_pw_manager_log.elements[pw_manager_str]) {
              if (document.del_pw_manager_log.elements[pw_manager_str].length == null){
                   if (check_flag == true) {
                         document.del_pw_manager_log.elements[pw_manager_str].checked = true;
                    } else {
                       document.del_pw_manager_log.elements[pw_manager_str].checked = false;
                     }
               } else {
                 for (i = 0; i < document.del_pw_manager_log.elements[pw_manager_str].length; i++){
                      if (check_flag == true) {
                           document.del_pw_manager_log.elements[pw_manager_str][i].checked = true;
                      } else {
                           document.del_pw_manager_log.elements[pw_manager_str][i].checked = false;
                      }
                  }
              }
        }
}

