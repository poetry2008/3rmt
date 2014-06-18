var o_submit_single = true;
$(document).ready(function() {
  //监听按键 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_latest_news').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
        //回车
        if ($('#show_latest_news').css('display') != 'none') {
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+方向左 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+方向右
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  }); 
});
function group_ajax(ele,group_id,parent_group_id,group_name){
 $.ajax({
 type: "POST",
 url: 'ajax.php?&action=show_group_info',
 data: {group_id:group_id,parent_group_id:parent_group_id,group_name:group_name} ,
 dataType: 'text',
 async : false,
 success: function(data){
   $("div#show_latest_news").html(data);
   ele = ele.parentNode;
   head_top = $('.compatible_head').height();
   if(group_id != -1){
	if($(ele).parent().next()[0] === undefined){
		if($('#show_latest_news').height() > ($(ele).parent().parent().height() - $(ele).parent().height())){
			var topset = $(ele).offset().top + $(ele).height() + 3;
		}else{
			var topset = $(ele).offset().top - $('#show_latest_news').height();
		}
	}else{
		var topset = $(ele).offset().top + $(ele).height() + 3;
	}
	$('#show_latest_news').css('top', topset);
   }
   if($('.show_left_menu').width()){
     leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
   }else{
     leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
   } 
   if(group_id == -1){
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
function check_group(){
  var group_name = document.getElementsByName("group_name")[0];
  group_name_value = group_name.value;
  group_name_value = group_name_value.replace(/\s/g,"");
  var error = false;
  if(group_name_value == ''){

    error = true;
    group_name.focus();
    $("#group_name_error").html('&nbsp;<font color="#FF0000">'+group_name_must+'</font>');
  }

  if(error == false){
    document.forms.new_latest_group.submit();
  }
}

function delete_group(group_id,parent_id){

  if(confirm(delete_group_confirm)){
    document.forms.new_latest_group.action = 'groups.php?action=delete_group&group_id='+group_id+'&parent_id='+parent_id;
    document.forms.new_latest_group.submit();
  }
}

//action link
function toggle_group_action(group_url_str) 
{
  if (user_permission == 31) {
    window.location.href = group_url_str;  
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
        window.location.href = group_url_str;  
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
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(group_url_str),
            async: false,
            success: function(msg_info) {
              window.location.href = group_url_str;  
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
//全选动作
function all_select_group(group_list_id)
{
  var check_flag = document.edit_group.all_check.checked;
  if (document.edit_group.elements[group_list_id]) {
    if (document.edit_group.elements[group_list_id].length == null) {
      if (check_flag == true) {
        document.edit_group.elements[group_list_id].checked = true;
      } else {
        document.edit_group.elements[group_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_group.elements[group_list_id].length; i++) {
        if (check_flag == true) {
          if(!(document.edit_group.elements[group_list_id][i].disabled)){
            document.edit_group.elements[group_list_id][i].checked = true;
          }
        } else {
          document.edit_group.elements[group_list_id][i].checked = false;
        }
      }
    }
  }
}

//删除动作
function group_change_action(value,group_list_id,c_permission,parent_id)
{
  sel_num = 0;
  if (document.edit_group.elements[group_list_id].length == null) {
    if (document.edit_group.elements[group_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_group.elements[group_list_id].length; i++) {
      if (document.edit_group.elements[group_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm(group_select_delete)) {
      if (c_permission == 31) {
        document.edit_group.action = 'groups.php?action=delete_select_group&parent_id='+parent_id;
        document.edit_group.submit(); 
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: '', 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.edit_group.action = 'groups.php?action=delete_select_group&parent_id='+parent_id;
              document.edit_group.submit(); 
            } else {
              var input_pwd_str = window.prompt(ntime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('groups.php?action=delete_select_group&parent_id='+parent_id),
                  async: false,
                  success: function(msg_info) {
                    document.edit_group.action = 'groups.php?action=delete_select_group&parent_id='+parent_id;
                    document.edit_group.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("group_action")[0].value = 0;
                alert(ontime_pwd_error); 
              }
            }
          }
        });
      }
    }else{

      document.getElementsByName("group_action")[0].value = 0;
    } 
  }else{
    document.getElementsByName("group_action")[0].value = 0;
    alert(must_select_group); 
  }
}
