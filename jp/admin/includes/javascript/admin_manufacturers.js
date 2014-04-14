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
function all_select_manufacturers(manufacturers_str){
      var check_flag = document.del_manufacturers.all_check.checked;
          if (document.del_manufacturers.elements[manufacturers_str]) {
            if (document.del_manufacturers.elements[manufacturers_str].length == null){
                if (check_flag == true) {
                  document.del_manufacturers.elements[manufacturers_str].checked = true;
                 } else {
                  document.del_manufacturers.elements[manufacturers_str].checked = false;
                 }
                } else {
            for (i = 0; i < document.del_manufacturers.elements[manufacturers_str].length; i++){
                       if (!document.del_manufacturers.elements[manufacturers_str][i].disabled) { 
                         if (check_flag == true) {
                             document.del_manufacturers.elements[manufacturers_str][i].checked = true;
                         } else {
                             document.del_manufacturers.elements[manufacturers_str][i].checked = false;
                         }
                       }
                       }
                   }
             }
}
function delete_select_manufacturers(manufacturers_str, c_permission){
     sel_num = 0;
     if (document.del_manufacturers.elements[manufacturers_str].length == null) {
         if (document.del_manufacturers.elements[manufacturers_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_manufacturers.elements[manufacturers_str].length; i++) {
             if(document.del_manufacturers.elements[manufacturers_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
         if (confirm(js_del_manufacturers)) {
           if (c_permission == 31) {
             document.forms.del_manufacturers.submit(); 
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name='+js_manufacturers_self, 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.del_manufacturers.submit(); 
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_manufacturers.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_manufacturers.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('manufacturers_action')[0].value = 0;
                    alert(js_onetime_error); 
                    setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('manufacturers_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('manufacturers_action')[0].value = 0;
            alert(js_manufacturers_must_select); 
          }
}
//choose action
function manufacturers_change_action(r_value, r_str) {
  if (r_value == '1') {
     delete_select_manufacturers(r_str, js_manufacturers_npermission);
  }
}
$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_manufacturers').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
        //ENTER
        if ($('#show_manufacturers').css('display') != 'none') {
          if(o_submit_single){
             $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left 
      if ($('#show_manufacturers').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right 
      if ($('#show_manufacturers').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
function show_manufacturers(ele,mID,page){
 var sort = $("#sort").val();
 var type = $("#type").val();
 $.ajax({
 url: 'ajax.php?&action=edit_manufacturers',
 data: {mID:mID,page:page,sort:sort,type:type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_manufacturers").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(mID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_manufacturers').height()){
offset = ele.offsetTop+$("#show_manufacturers_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
  offset = offset+2;
} 
$('#show_manufacturers').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_manufacturers').height()) > $('.box_warp').height())&&($('#show_manufacturers').height()<ele.offsetTop+parseInt(head_top)-$("#show_manufacturers_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_manufacturers_list").position().top-1-$('#show_manufacturers').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_manufacturers_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_manufacturers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_manufacturers_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_manufacturers').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_manufacturers').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(mID == -1){
  $('#show_manufacturers').css('top',$('#show_manufacturers_list').offset().top);
}
$('#show_manufacturers').css('z-index','1');
$('#show_manufacturers').css('left',leftset);
$('#show_manufacturers').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
  $('#show_manufacturers').css('display','none');
}
function check_del(mID,page,c_permission){
  if(confirm(js_del_manufacturers)){
  if (c_permission == 31) {
     window.location.href=js_href_manufacturers+"?page="+page+"&mID="+mID+"&action=deleteconfirm";
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_manufacturers_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href=js_href_manufacturers+"?page="+page+"&mID="+mID+"&action=deleteconfirm";
        } else {
          $("#button_save").attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_href_manufacturers+'?page='+page+'&site_id='+site_id+'&cID='+cID+'&act=deleteconfirm'),
              async: false,
              success: function(msg_info) {
                window.location.href=js_href_manufacturers+"?page="+page+"&mID="+mID+"&action=deleteconfirm";
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
}
