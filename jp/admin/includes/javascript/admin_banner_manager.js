$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_present').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_present').css('display') != 'none') {
            if (o_submit_single){
                cid = $("#cid").val();
                $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_present').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_present').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
//batch delete data
function delete_select_banner(banner_str, c_permission){
     sel_num = 0;
     if (document.del_banner.elements[banner_str].length == null) {
         if (document.del_banner.elements[banner_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_banner.elements[banner_str].length; i++) {
             if(document.del_banner.elements[banner_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
         if (confirm(js_banner_manager_del_news)) {
           if (c_permission == 31) {
             document.forms.del_banner.submit(); 
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name='+js_banner_manager_self, 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.del_banner.submit(); 
                } else {
                  var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_banner.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_banner.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('banner_action')[0].value = 0;
                    alert(js_onetime_error); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('banner_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('banner_action')[0].value = 0;
             alert(js_banner_must_select); 
          }
}
//choose action
function banner_change_action(r_value, r_str) {
    if (r_value == '1') {
       delete_select_banner(r_str, js_banner_manager_npermission);
    }
}
//select all checkbox
function all_select_banner(banner_str){
   var check_flag = document.del_banner.all_check.checked;
        if (document.del_banner.elements[banner_str]) {
             if (document.del_banner.elements[banner_str].length == null){
                  if (check_flag == true) {
                      document.del_banner.elements[banner_str].checked = true;
                  } else {
                      document.del_banner.elements[banner_str].checked = false;
                  }
              } else {
                  for (i = 0; i < document.del_banner.elements[banner_str].length; i++){
                       if(!document.del_banner.elements[banner_str][i].disabled) { 
                          if (check_flag == true) {
                              document.del_banner.elements[banner_str][i].checked = true;
                          } else {
                              document.del_banner.elements[banner_str][i].checked = false;
                          }
                        }
                   }
             }
        }
}
//check radio type
function check_radio(value){
  if(value == 0){
    $("#banners_html_hide").hide(); 
    $("#banners_image_hide").show(); 
  }else{
    $("#banners_html_hide").show(); 
    $("#banners_image_hide").hide(); 
  }
}
//button I popup
function show_banner(ele,bID,page,site_id){
  var sql = js_banner_manager_sql_where;
  var str = js_banner_manager_str;
  var post_site_id = js_banner_manager_site_id;
  var sort = js_banner_manager_sort;
  var type = js_banner_manager_type;
 $.ajax({
 url: 'ajax.php?&action=edit_banner',
 data:
 {bID:bID,site_id:site_id,page:page,post_site_id:post_site_id,str:str,sql:sql,sort:sort,type:type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_banner").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_banner').height()){
offset = ele.offsetTop+$("#show_banner_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_banner').height()) > $('.box_warp').height())&&($('#show_banner').height()<ele.offsetTop+parseInt(head_top)-$("#show_banner_list").position().top-1)) {
offset = ele.offsetTop+$("#show_banner_list").position().top-1-$('#show_banner').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_banner_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_banner_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_banner').height()) > $('.box_warp').height())&&($('#show_banner').height()<ele.offsetTop+parseInt(head_top)-$("#show_banner_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_banner_list").position().top-1-$('#show_banner').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_banner_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_banner_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
   offset = offset+3;
} 
$('#show_banner').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_banner').height()) > $('.box_warp').height())&&($('#show_banner').height()<ele.offsetTop+parseInt(head_top)-$("#show_banner_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_banner_list").position().top-1-$('#show_banner').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_banner_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_banner_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_banner_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_banner').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_banner').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(bID == '-1'){
  $('#show_banner').css('top', $('#show_banner_list').offset().top);
}
$('#show_banner').css('z-index','1');
$('#show_banner').css('left',leftset);
$('#show_banner').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
//delete data verification
function msg(c_permission,page,bID,site_id){
  if (confirm(js_banner_manager_del_news)) {
    if (c_permission == 31) {
      location.href = js_banner_manager_home+'?action=deleteconfirm&page='+page+'&bID='+bID+'&sort='+js_banner_manager_sort+'&type='+js_banner_manager_type;
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+js_banner_manager_self, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            location.href = js_banner_manager_home+'?action=deleteconfirm&page='+page+'&bID='+bID+'&sort='+js_banner_manager_sort+'&type='+js_banner_manager_type;
          } else {
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent( location.href = js_banner_manager_home+'?action=deleteconfirm&page='+page+'&bID='+bID+'&sort='+js_banner_manager_sort+'&type='+js_banner_manager_type ),
                async: false,
                success: function(msg_info) {
                  location.href = js_banner_manager_home+'?action=deleteconfirm&page='+page+'&bID='+bID+'&sort='+js_banner_manager_sort+'&type='+js_banner_manager_type;
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
//img permission onetime pwd
function onetime_images(c_permission,page,bID,site_id){
      location.href = js_banner_manager_statistics+'?page='+page+'&bID='+bID+'&site_id='+site_id;
}
//close I button popup page
function hidden_info_box(){
   $('#show_banner').css('display','none');
}
//popup new calendar
function open_new_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open').val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar",
            width:'170px',
        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        $("#input_date_scheduled").val(dtdate.format(newDate)); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
function open_update_calendar()
{
  var is_open = $('#toggle_open_end').val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#end_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open_end').val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar_end",
            width:'170px',
        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        $("#input_expires_date").val(dtdate.format(newDate)); 
        $('#toggle_open_end').val('0');
        $('#toggle_open_end').next().html('<div id="mycalendar_end"></div>');
      });
    });
  }
}
function popupImageWindow(url) {
  window.open(url, 'popupImageWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=yes,width=300,height=200,left=0,top=0')
}
//submit form
function check_banner_form(b_type) 
{

  p_error = false; 
  if(document.new_banner.banners_title.value == ""){
    $("#title_error").html(js_banner_manager_title_error);
    document.new_banner.banners_title.focus();
    p_error = true; 
  }else{
    $("#title_error").html("");
    document.new_banner.banners_title.focus();
  }
  if(document.new_banner.banners_group.value == "" && document.new_banner.new_banners_group.value == ""){
    $("#group_error").html(js_banner_manager_group_error);
    document.new_banner.banners_group.focus();
    p_error = true; 
  }else{
    $("#group_error").html("");
    document.new_banner.banners_group.focus();
  }
  if(p_error == false){
  if (js_banner_manager_npermission == 31) {
  if(b_type == 1) {
    document.forms.banners.submit(); 
  } else {
    document.forms.new_banner.submit(); 
  }
  } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_banner_manager_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        if(b_type == 1) {
          document.forms.banners.submit(); 
        } else {
          document.forms.new_banner.submit(); 
        }
      } else {
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        var form_action_str = ''; 
        if(b_type == 1) {
          form_action_str = document.forms.banners.action; 
        } else {
          form_action_str = document.forms.new_banner.action;; 
        }
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
            async: false,
            success: function(msg_info) {
              if(b_type == 1) {
                document.forms.banners.submit(); 
              } else {
                document.forms.new_banner.submit(); 
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
}
//forward action
function toggle_banner_action(banner_url_str)
{
  if (js_banner_manager_npermission == 31) {
  window.location.href = banner_url_str; 
  } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_banner_manager_self, 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        window.location.href = banner_url_str; 
      } else {
        var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(banner_url_str),
            async: false,
            success: function(msg_info) {
              window.location.href = banner_url_str; 
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
