$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_pw_manager').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_pw_manager').css('display') != 'none') {
            if (o_submit_single){
               $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_pw_manager').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_pw_manager').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
//select all data to delect
function all_select_pw_manager(pw_manager_str){
        var check_flag = document.del_pw_manager.all_check.checked;
           if (document.del_pw_manager.elements[pw_manager_str]) {
              if (document.del_pw_manager.elements[pw_manager_str].length == null){
                   if (check_flag == true) {
                         document.del_pw_manager.elements[pw_manager_str].checked = true;
                    } else {
                       document.del_pw_manager.elements[pw_manager_str].checked = false;
                     }
               } else {
                 for (i = 0; i < document.del_pw_manager.elements[pw_manager_str].length; i++){
                      if (check_flag == true) {
                           document.del_pw_manager.elements[pw_manager_str][i].checked = true;
                      } else {
                           document.del_pw_manager.elements[pw_manager_str][i].checked = false;
                      }
                  }
              }
        }
}
//ajax popup page
function show_pw_manager(ele,pw_id,page,site_id){
 sort = document.getElementById('pw_manager_sort').value;
 type = document.getElementById('pw_manager_type').value;
 search_type = document.getElementById('pw_manager_search_type').value;
 keywords = document.getElementById('pw_manager_keywords').value;
 $.ajax({
 url: 'ajax.php?&action=edit_pw_manager',
 data: {pw_id:pw_id,page:page,site_id:site_id,sort:sort,type:type,keywords:keywords,search_type:search_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("#show_pw_manager").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(pw_id != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_pw_manager').height()){
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
} else {
offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
  } else {
    offset = ele.offsetTop+$('#orders_list_table').position().top+ele.offsetHeight+head_top;
  }
}
$('#show_pw_manager').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_pw_manager').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_pw_manager').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(pw_id == -1){
  $('#show_pw_manager').css('top',$('#orders_list_table').offset().top);
}
$('#show_pw_manager').css('z-index','1');
$('#show_pw_manager').css('left',leftset);
$('#show_pw_manager').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
//close popup page
function hidden_info_box(){
   $('#show_pw_manager').css('display','none');
}
//popup calendar
function open_new_calendar(){
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
        $("#input_nextdate").val(dtdate.format(newDate)); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
//manager select permission
function self_radio(){
     if($('#self').attr("checked")){ 
      $("#user_select").css('display', 'block');
      }
 
}
//manager select permission
function privilege_s_radio(){
     if($('#privilege_s').attr("checked")){ 
      $("#user_select").css('display', 'none');
      }
}
//manager select permission
function privilege_c_radio(){
     if($('#privilege_c').attr("checked")){ 
     $("#user_select").css('display', 'none');
     }
}
//copy code
function copyCode(idpw,name){
  var testCode;
  $.post(js_id_manager_href,{'action':'load','idpw':idpw,'from':name}, function(data) {
      testCode = data;
    if(copy2Clipboard(testCode)!=false){
        alert(js_id_manager_copy_ok);
    }
  });
}
copy2Clipboard=function(txt){
    if(window.clipboardData){
        window.clipboardData.clearData();
        window.clipboardData.setData("Text",txt);
    }
    else if(navigator.userAgent.indexOf("Opera")!=-1){
        window.location=txt;
    }
    else if(window.netscape){
        try{
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        }
        catch(e){
            alert(js_id_manager_firefox_error);
            return false;
        }
        var clip=Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
        if(!clip)return;
        var trans=Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
        if(!trans)return;
        trans.addDataFlavor('text/unicode');
        var str=new Object();
        var len=new Object();
        var str=Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
        var copytext=txt;str.data=copytext;
        trans.setTransferData("text/unicode",str,copytext.length*2);
        var clipid=Components.interfaces.nsIClipboard;
        if(!clip)return false;
        clip.setData(trans,null,clipid.kGlobalClipboard);
    }
}
//search form submit
function search_type_changed(elem){
	if ($('#keywords').val() && elem.selectedIndex != 0) 
      document.forms.pw_manager1.submit();
}
//check url
function checkurl(url){
  var str = url;
  var objExp = new RegExp(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/);
  if(objExp.test(str)){
    return true;
  }else{
    return false;
  }
}
//verify form
function valdata(c_permission){
  id_pw_error = false; 
  if (document.getElementById('url').value!=''&& !checkurl(document.getElementById('url').value)) {
    id_pw_error = true; 
  }
  if (document.getElementById('loginurl').value!=''&& !checkurl(document.getElementById('loginurl').value)) {
    id_pw_error = true; 
  }
  if (id_pw_error == false) {
    if (c_permission == 31) {
      document.forms.pw_manager.submit(); 
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
            document.forms.pw_manager.submit(); 
          } else {
            $('#button_save').attr('id', 'tmp_button_save'); 
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.pw_manager.action),
                async: false,
                success: function(msg_info) {
                  document.forms.pw_manager.submit(); 
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
  } else {
    alert(js_id_manager_url_example); 
  }
}
//create pwd
function mk_pwd(){
  var len = $('input:checkbox[name=pattern[]]:checked').length;
  var check = '';
  $('input:checkbox[name=pattern[]]:checked').each(function(index) {
    if (index < len-1){
      check += $(this).val()+",";
    }else{
      check += $(this).val();
    }
  });
  var pwd_len = $('#pwd_len').val();
  $.post(js_id_manager_pwd_ajax,{'action':'make_pw','pattern':check,'pwd_len':pwd_len}, function(data) {
      $('#password').val(data);
  });
}
//confirm delete data
function delete_select_pw_manager(pw_manager_str, c_permission){
         sel_num = 0;
         if (pw_manager_str == null){
                  document.getElementsByName('pw_manager_action')[0].value = 0;
                 alert(js_id_manager_must_select); 
         }
         if (document.del_pw_manager.elements[pw_manager_str].length == null) {
                if (document.del_pw_manager.elements[pw_manager_str].checked == true){
                     sel_num = 1;
                 }
          } else {
             for (i = 0; i < document.del_pw_manager.elements[pw_manager_str].length; i++) {
                 if(document.del_pw_manager.elements[pw_manager_str][i].checked == true) {
                     sel_num = 1;
                     break;
                  }
            }
         }
         if (sel_num == 1) {
          if (confirm(js_id_manager_pw_manager)) {
            if (c_permission == 31) {
              document.forms.del_pw_manager.submit(); 
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
                    document.forms.del_pw_manager.submit(); 
                  } else {
                    var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                    if (in_array(input_pwd_str, pwd_list_array)) {
                      $.ajax({
                        url: 'ajax_orders.php?action=record_pwd_log',   
                        type: 'POST',
                        dataType: 'text',
                        data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_pw_manager.action),
                        async: false,
                        success: function(msg_info) {
                          document.forms.del_pw_manager.submit(); 
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
//select action
function pw_manager_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_pw_manager(r_str, js_id_manager_npermission);
  }
}
//execute action
function toggle_idpw_action(idpwd_url_str, c_permission)
{
if (confirm(js_id_manager_pw_manager)) {
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
          if ($('#button_save')) {
            $('#button_save').attr('id', 'tmp_button_save'); 
          }
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
            if ($('#tmp_button_save')) {
              setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
            }
          }
        }
      }
    });
  }
 }
}
