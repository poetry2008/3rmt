$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc
      if ($('#show_latest_news').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_latest_news').css('display') != 'none') {
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

 function check_news_info(){
       var headline = document.getElementById('headline').value; 
       var content  = document.getElementById('content').value;
       var news_image_description = document.getElementById('news_image_description').value;
       var s_single = false; 
       
       if (document.getElementById('site_type_hidden')) {
         var site_type = document.getElementById('site_type_hidden').value; 
         if (site_type == 0) {
           if (document.new_latest_news.elements['site_id_info[]']) {
             if (document.new_latest_news.elements['site_id_info[]'].length == null) {
               if (document.new_latest_news.elements['site_id_info[]'].checked == true) {
                 s_single = true; 
               }
             } else {
               for (var u = 0; u < document.new_latest_news.elements['site_id_info[]'].length; u++) {
                 if (document.new_latest_news.elements['site_id_info[]'][u].checked == true) {
                   s_single = true; 
                   break; 
                 }
               }
             }
           } else {
             s_single = true; 
           }
         } else {
           s_single = true; 
         }
       } else {
         s_single = true; 
       }
       
       $.ajax({
         url: 'ajax.php?action=edit_latest_news',
         type: 'POST',
         dataType: 'text',
         data:'headline='+headline+'&content='+content+'&news_image_description='+news_image_description, 
         async:false,
         success: function (data){
          if (headline != '' && s_single == true) {
            if (js_news_npermission == 31) {
            document.forms.new_latest_news.submit(); 
            } else {
            $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name='+js_news_self, 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.new_latest_news.submit(); 
                } else {
                  $('#button_save').attr('id', 'tmp_button_save'); 
                  var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.new_latest_news.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.new_latest_news.submit(); 
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
            if (headline != '') {
              $("#title_error").html(''); 
            } else {
              $("#title_error").html(js_news_error_null); 
            }
            if (s_single == false) {
              $("#site_error").html(js_news_error_site); 
            } else {
              if ($("#site_error")) {
                $("#site_error").html(''); 
              }
            }
          }
         }
        });
}
function all_select_news(news_str){
      var check_flag = document.del_news.all_check.checked;
         if (document.del_news.elements[news_str]) {
           if (document.del_news.elements[news_str].length == null){
                if (check_flag == true) {
                    document.del_news.elements[news_str].checked = true;
                   } else {
                       document.del_news.elements[news_str].checked = false;
                   }
            } else {
              for (i = 0; i < document.del_news.elements[news_str].length; i++){
                if (!document.del_news.elements[news_str][i].disabled){
                if (check_flag == true) {
                   document.del_news.elements[news_str][i].checked = true;
                } else {
                 document.del_news.elements[news_str][i].checked = false;
                }
                }
               }
            }
          }
}

function delete_select_news(news_str, c_permission){
         sel_num = 0;
         if (document.del_news.elements[news_str].length == null) {
              if (document.del_news.elements[news_str].checked == true){
                   sel_num = 1;
              }
         } else {
           for (i = 0; i < document.del_news.elements[news_str].length; i++) {
             if(document.del_news.elements[news_str][i].checked == true) {
                 sel_num = 1;
                 break;
             }
            }
         }
        if (sel_num == 1) {
           if (confirm(js_news_del)) {
             if (c_permission == 31) {
               document.forms.del_news.submit(); 
             } else {
               $.ajax({
                 url: 'ajax_orders.php?action=getallpwd',   
                 type: 'POST',
                 dataType: 'text',
                 data: 'current_page_name='+js_news_self, 
                 async: false,
                 success: function(msg) {
                   var tmp_msg_arr = msg.split('|||'); 
                   var pwd_list_array = tmp_msg_arr[1].split(',');
                   if (tmp_msg_arr[0] == '0') {
                     document.forms.del_news.submit(); 
                   } else {
                     var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                     if (in_array(input_pwd_str, pwd_list_array)) {
                       $.ajax({
                         url: 'ajax_orders.php?action=record_pwd_log',   
                         type: 'POST',
                         dataType: 'text',
                         data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_news.action),
                         async: false,
                         success: function(msg_info) {
                           document.forms.del_news.submit(); 
                         }
                       }); 
                     } else {
                       document.getElementsByName('news_action')[0].value = 0;
                       alert(js_onetime_error); 
                     }
                   }
                 }
               });
             }
           }else{
              document.getElementsByName('news_action')[0].value = 0;
           }
         } else {
            document.getElementsByName('news_action')[0].value = 0;
            alert(js_news_must_select); 
         }
}
function show_latest_news(ele,page,latest_news_id,site_id,action_sid,sort_name,sort_type){
 var self_page = js_news_self;
 $.ajax({
 url: 'ajax.php?&action=edit_latest_news',
 data: {page:page,latest_news_id:latest_news_id,site_id:site_id,action_sid:action_sid,self_page:self_page,sort_name:sort_name,sort_type:sort_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_latest_news").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(latest_news_id != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_latest_news').height()){
offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_latest_news').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_latest_news').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_latest_news').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(latest_news_id == -1){
  $('#show_latest_news').css('top', $('#show_text_list').offset().top);
}
$('#show_latest_news').css('z-index','1');
$('#show_latest_news').css('left',leftset);
$('#show_latest_news').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_latest_news').css('display','none');
   o_submit_single = true;
}
//choose action
function news_change_action(r_value, r_str) {
 if (r_value == '1') {
     delete_select_news(r_str, js_news_npermission);
   }
}
//action link
function toggle_news_action(news_url_str) 
{
    if (js_news_npermission == 31) {
  window.location.href = news_url_str;  
    } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_news_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        window.location.href = news_url_str;  
      } else {
        if ($('#button_save')) {
          $('#button_save').attr('id', 'tmp_button_save'); 
        }
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(news_url_str),
            async: false,
            success: function(msg_info) {
              window.location.href = news_url_str;  
            }
          }); 
        } else {
          alert(js_onetime_error); 
          if ($('#tmp_button_save')) {
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    }
  });
    }
}
//all select
function select_all_news_site()
{
  var is_select_value = document.getElementById('is_select').value; 
  if (document.new_latest_news.elements['site_id_info[]']) {
    if (document.new_latest_news.elements['site_id_info[]'].length == null) {
      if (is_select_value == '0') {
        document.new_latest_news.elements['site_id_info[]'].checked = true;
        document.getElementById('is_select').value = '1'; 
      } else {
        document.new_latest_news.elements['site_id_info[]'].checked = false;
        document.getElementById('is_select').value = '0'; 
      }
    } else {
      if (is_select_value == '0') {
        for (var i = 0; i < document.new_latest_news.elements['site_id_info[]'].length; i++) {
          if (!document.new_latest_news.elements['site_id_info[]'][i].disabled) {
            document.new_latest_news.elements['site_id_info[]'][i].checked = true;
          }
        }
        document.getElementById('is_select').value = '1'; 
      } else {
        for (var i = 0; i < document.new_latest_news.elements['site_id_info[]'].length; i++) {
          if (!document.new_latest_news.elements['site_id_info[]'][i].disabled) {
            document.new_latest_news.elements['site_id_info[]'][i].checked = false;
          } 
        }
        document.getElementById('is_select').value = '0'; 
      }
    }
  }
}
//select site
function change_site_type(site_type, site_list)
{
  var site_list_array = site_list.split(','); 
  if (site_type == 0) {
    $('#site_type_hidden').val('0'); 
    $('#select_site').find(':checkbox').each(function() {
      for (var i = 0; i < site_list_array.length; i++) {
        if ($(this).val() == site_list_array[i]) {
          $(this).removeAttr('disabled'); 
        }
      }
    }); 
    $('#all_site_button').removeAttr('disabled'); 
  } else {
    $('#site_type_hidden').val('1'); 
    $('#select_site').find(':checkbox').each(function() {
      $(this).attr('disabled', 'disabled'); 
    }); 
    $('#all_site_button').attr('disabled', 'disabled'); 
  }
}
