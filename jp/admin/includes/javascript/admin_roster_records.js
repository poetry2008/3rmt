var ele_value_obj_att = '';
var ele_index = 0;
//show attendance info
function show_attendance_info(ele,id,param_y,param_m){
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $(ele).offset();   

  $.ajax({
  url: 'ajax.php?action=edit_attendance_info',
  data: 'id='+id+'&param_y='+param_y+'&param_m='+param_m,
  type: 'POST',
  dataType: 'text',
  async : false,
  success: function(data){
     $('#show_attendance_edit').html(data);
     $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height());
     if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

       $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
     }else{
       $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
     } 
     ele_value_obj_att = ele;
     ele_index = 0;
     $('#show_attendance_edit').css('display','block');
 }
  }); 

}


//delect attendance by id
function delete_attendance_info(id){
	if(confirm(js_remind_delete)) {
      	
        $.ajax({
            url: 'ajax.php?action=delete_attendance_info',
            data: 'attendance_id='+id,
            type: 'POST',
            dataType: 'text',
            async : false,
            success: function(data){
				if(data){
					 window.location.href = href_attendance_calendar;
				
				}
            }
        }); 
	}
}


//change scheduling_type
function change_type_text(){
	var select_val = $("#type_id").val();

	if(select_val==1){
      $(".upload_image").css("display","none");	
      $("#image_div").css("display","none");	
      $("#src_text_color").css("display","block");	
      $("#color_div").css("display","block");	
	}
	if(select_val==0){
      $("#image_div").css("display","block");	
      $(".upload_image").css("display","block");	
      $("#src_text_color").css("display","none");	
      $("#color_div").css("display","none");	
	}
}

//change set_time
function change_set_time(set_id) {
   if(set_id==1){
      $('.set_time_field_title').css("display","none"); 
      $('.set_time_field_content').css("display","none"); 
      $('.set_time_numbers_title').css("display","block"); 
      $('.set_time_numbers_content').css("display","block"); 
   }

   if(set_id==0){
      $('.set_time_field_title').css("display","block"); 
      $('.set_time_field_content').css("display","block"); 
      $('.set_time_numbers_title').css("display","none"); 
      $('.set_time_numbers_content').css("display","none"); 
   
   }
}

function check_attendance_info(){
      var title_val = $("#attendance_title").val();
      var short_lan_val = $("#short_language").val();
      var work_start_hour = $("#work_start_hour").val();
      var work_start_min_r = $("#work_start_min_r").val();
      var work_start_min_l = $("#work_start_min_l").val();
      var work_end_hour = $("#work_end_hour").val();
      var work_end_min_r = $("#work_end_min_r").val();
      var work_end_min_l = $("#work_end_min_l").val();
      var work_hours = $("#work_hours").val();
      var rest_hours = $("#rest_hours").val();
      var sign = '';
      if(title_val==''){
        $("#title_text_error").html(error_text);
		sign=1;
      }

	  if(short_lan_val==''){
		  sign=1;
        $("#short_lan_error").html(error_text);
	  }

	  if($("#set_left").attr('checked')){
	      if(work_start_hour==0 && work_start_min_r==0 && work_start_min_l==0){
	    	 sign=1;
             $("#work_start_error").html(error_text);
	      }

	      if(work_end_hour==0 && work_end_min_r==0 && work_end_min_l==0){
		     sign=1
             $("#work_end_error").html(error_text);
	      }
     }

	  if($("#set_right").attr('checked')) {
	      if(work_hours ==''){
		      sign=1;
              $("#work_hours_error").html(error_text);
	      }

	      if(rest_hours ==''){
		      sign=1;
              $("#rest_hours_error").html(error_text);
	      }
	  }
	  if(sign ==1){
	      return false;
	  }else{
         document.forms.attendances.submit();
	  }

}

function getFileName(path){
    var pos1 = path.lastIndexOf('/');
    var pos2 = path.lastIndexOf('\\');
    var pos  = Math.max(pos1, pos2)
    if( pos<0 ){
        return path;
    }else{
        return path.substring(pos+1);
    }
}


function change_image_text(_this) {
	 var image_name = getFileName(_this.value);
    $("#src_text_image").val(image_name);
}

function attendance_setting(date,ele,gid,add_id){
  if(!gid){
    gid='';
  }
  if(!add_id){
    add_id='';
  }
  var index = ele;
  var ele = document.getElementById('date_td_'+ele);
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $(ele).offset();   
  $.ajax({
    dataType: 'text',
    url: 'ajax.php?action=attendance_setting&date='+date+'&gid='+gid+'&index='+index+'&add_id='+add_id,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height()+2);
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
      }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
      } 
      ele_value_obj_att = ele;
      ele_index = index;
      $('#show_attendance_edit').css('display','block');
    }
  });
}

function change_model_get_time(model_id){
   $.ajax({
       url: 'ajax.php?action=change_model_get_time',
       data: 'id='+model_id,
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
          $('#show_user_adl').html(data);
       }
  }); 

}


function hidden_info_box(){
  $('#show_attendance_edit').css('display','none');
}

//change mould time
function change_scheduling_time(mould_id){
   $.ajax({
       url: 'ajax.php?action=get_scheduling_time',
       type: 'POST',
       dataType: 'text',
       data: 'mould_id='+mould_id, 
       async : false,
       success: function(data){
		   var tep = data.split('"');
		   var info = tep[1].split(',');
		   $("#leave_start_hour").val(info[0]);
		   $("#leave_start_min_l").val(info[1]);
		   $("#leave_start_minute_b").val(info[2]);
		   $("#leave_end_hour").val(info[3]);
		   $("#leave_end_min_l").val(info[4]);
		   $("#leave_end_min_r").val(info[5]);

       }
  }); 

}

function delete_submit(c_permission,type){
  if(type=='as'){
    del_url = href_attendance_calendar+'?action=delete_as_list';
  }else if(type=='user'){
    del_url = href_attendance_calendar+'?action=delete_as_user_list';
  }else{
    del_url = href_attendance_calendar+'?action=delete_as_replace';
  }
  if(confirm(js_remind_delete)) {
  if (c_permission == 31) {
    document.attendance_setting_form.action = del_url 
    document.attendance_setting_form.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+document.getElementById("hidden_page_info").value, 
      async: false,
      success: function(msg) {
         var tmp_msg_arr = msg.split('|||'); 
         var pwd_list_array = tmp_msg_arr[1].split(',');
         if (tmp_msg_arr[0] == '0') {
           document.attendance_setting_form.action = del_url 
           document.attendance_setting_form.submit();
         } else {
           $('#button_delete').attr('id', 'tmp_button_delete'); 
           var input_pwd_str = window.prompt(js_text_input_onetime_pwd, ''); 
           if (in_array(input_pwd_str, pwd_list_array)) {
             document.attendance_setting_form.action = del_url 
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.attendance_setting_form.action),
               async: false,
               success: function(msg_info) {
                 document.attendance_setting_form.submit();
               }
             }); 
           } else {
             alert(js_text_onetime_pwd_error); 
             setTimeOut($('#tmp_button_delete').attr('id', 'button_delete'), 1); 
           }
         }
      }
    });
  }
  }
}
function save_submit(c_permission){
  //选择的组
  group_id = '';
  var group_select = $("select[name='has_group[]']");
  if(group_select.length>0){
    group_select.each(function(){
      group_id += $(this).val() + '||';
    })
  }
  //后加组
  var group_select_add = $("form select[name='group[]']");
  if(group_select_add.length>0){
    group_select_add.each(function(i){
           group_id += $(this).val() + '||';
    })
  }

  //个人
  var user_select = $("select[name='has_user[]']");
  if(user_select.length>0){
    user_select.each(function(){
      group_id += $(this).val() + '||';
    })
  }
  //后加个人
  var user_select_add = $("form select[name='user[]']");
  if(user_select_add.length>0){
     user_select_add.each(function(i){
          group_id += $(this).val() + '||';
     })
  }

  //选择的排班
  var att_select = $("select[name='has_attendance_id[]']");
  att_id = '';
  att_select.each(function(){
    att_id += $(this).val() + '||';
  })
  //后加排版
  var att_select_add = $("form select[name='attendance_id[]']");
  att_select_add.each(function(i){
      att_id += $(this).val() + '||';
  })

   $.ajax({
       url: 'ajax.php?action=check_same_group_att',
       type: 'POST',
       dataType: 'text',
       data: 'group_id='+group_id+'&att_id='+att_id, 
       async : false,
       success: function(data){
		   if(data) {
			 var data =data + warn_attendance_type_diff;
			 alert(data);
			 flag = 1;
		   }else{
		     flag = 0;
		   }
       }
  }); 

if(flag !=1) {
  if (c_permission == 31) {
    document.attendance_setting_form.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+document.getElementById("hidden_page_info").value, 
      async: false,
      success: function(msg) {
         var tmp_msg_arr = msg.split('|||'); 
         var pwd_list_array = tmp_msg_arr[1].split(',');
         if (tmp_msg_arr[0] == '0') {
           document.attendance_setting_form.submit();
         } else {
           $('#button_save').attr('id', 'tmp_button_save'); 
           var input_pwd_str = window.prompt(js_text_input_onetime_pwd, ''); 
           if (in_array(input_pwd_str, pwd_list_array)) {
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.attendance_setting_form.action),
               async: false,
               success: function(msg_info) {
                 document.attendance_setting_form.submit();
               }
             }); 
           } else {
             alert(js_text_onetime_pwd_error); 
             setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
           }
         }
      }
    });
  }
}
}

function del_as(ele,asl_id,c_permission){
  var tr_index = $(ele).parent().parent().index();
  $('.popup_order_info').find('tr').eq(tr_index).remove();
  $('.popup_order_info').find('tr').eq(tr_index).remove();
  $('.popup_order_info').find('tr').eq(tr_index).remove();
  if(asl_id!=''){
    $('#get_att_date').after('<input type="hidden" name="del_as[]" value="'+asl_id+'">');
  }
}

function change_user_list(ele){
  var gid = $(ele).val();
  $.ajax({
    url: 'ajax_orders.php?action=roster_records_user_list',   
    type: 'POST',
    dataType: 'text',
    data: 'gid='+gid, 
    async: false,
    success: function(msg) {
      if(msg!=''){
         var msg_arr = msg.split('|||'); 
		 if(msg_arr[1]=='') {
			msg_arr[1]=0;
		 } 
		 var i= msg_arr[1];
		 $("input[name='att_status']:eq("+i+")").attr("checked",'checked');
         $("#show_user_list").html(msg_arr[0]);
      }
    }
  });
}

function attendance_replace(date,ele,uid,att_id){
  if(!uid){
    uid='';
  }
  if(!att_id){
    att_id='';
  }
  var index = ele;
  var ele = document.getElementById('date_td_'+ele);
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $(ele).offset();   
  $.ajax({
    dataType: 'text',
    url: 'ajax.php?action=attendance_replace&date='+date+'&uid='+uid+'&index='+index+'&att_id='+att_id,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height()+2);
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
      }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
      } 
      ele_value_obj_att = ele;
      ele_index = index;
      $('#show_attendance_edit').css('display','block');
	  var tep_val = $("#att_detail_id").val();
          if(uid==''){
	    change_scheduling_time(tep_val);
          }
    }
  });
}

function add_allow_user(ele,del_str){
  var select_str = $(ele).parent().parent().find('td').eq(1).html();
  $("select[name='allow_user[]']").each(function(){
 
    var reg = new RegExp('<option value="'+$(this).val()+'".*?>.*?<\/option>','g');
    select_str = select_str.replace(reg,'');
  });

  if(select_str.indexOf('<option') > 0){

    $('#add_end').before('<tr><td></td><td>'+select_str+'</td><td><input type="button" value="'+del_str+'" onclick="del_allow_user(this)"></td></tr>');
  }else{
  
    ele.disabled = true;
  }
}

function del_allow_user(ele){
  var add_allow_user_button = document.getElementById('add_allow_user_button');
  add_allow_user_button.disabled = false;
  $(ele).parent().parent().remove();
}

//change groups
function change_users_groups(value){

  $.ajax({
          url: 'ajax.php?action=change_users_groups',
          data: 'users_id='+value,
          type: 'POST',
          dataType: 'text',
          async : false,
          success: function(data){

            $("#users_groups").html('');
            $("#users_groups").html(data);
          }
        });
}
//change users allow
function change_users_allow(value){

  var allow_status = document.getElementsByName("allow_status")[0];
  var allow_flag = false;
  $("select[name='allow_user[]']").each(function(){
        if($(this).val() == admin_id){
      
          allow_flag = true;
        }
  });
  if(value == admin_id  || allow_flag == true || admin_npermission >= 15){
  
    allow_status.disabled = false;
  }else{

    allow_status.disabled = true;
  }
}
function change_att_date(date,ele,uid,aid){
    if(!uid){
    uid='';
  }
  var index = ele;
  var ele = document.getElementById('date_td_'+ele);
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $(ele).offset();   
  $.ajax({
    dataType: 'text',
    url: 'ajax.php?action=change_att_date&date='+date+'&uid='+uid+'&index='+index+'&aid='+aid,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height()+2);
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
      }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
      } 
      ele_value_obj_att = ele;
      ele_index = index;
      $('#show_attendance_edit').css('display','block');
      $('#show_attendance_edit').html(text);  
    }
  });
}
function delete_replace_submit(c_permission){
  del_url = href_attendance_calendar+'?action=delete_as_replace';
  if (c_permission == 31) {
    document.attendance_setting_form.action = del_url 
    document.attendance_setting_form.submit();
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+document.getElementById("hidden_page_info").value, 
      async: false,
      success: function(msg) {
         var tmp_msg_arr = msg.split('|||'); 
         var pwd_list_array = tmp_msg_arr[1].split(',');
         if (tmp_msg_arr[0] == '0') {
           document.attendance_setting_form.action = del_url 
           document.attendance_setting_form.submit();
         } else {
           $('#button_delete').attr('id', 'tmp_button_delete'); 
           var input_pwd_str = window.prompt(js_text_input_onetime_pwd, ''); 
           if (in_array(input_pwd_str, pwd_list_array)) {
             document.attendance_setting_form.action = del_url 
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.attendance_setting_form.action),
               async: false,
               success: function(msg_info) {
                 document.attendance_setting_form.submit();
               }
             }); 
           } else {
             alert(js_text_onetime_pwd_error); 
             setTimeOut($('#tmp_button_delete').attr('id', 'button_delete'), 1); 
           }
         }
      }
    });
  }
}

window.onresize = resizepage;

function resizepage(){
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $("#show_attendance_edit").offset();
  if(ele_value_obj_att != ''){
    tmp_ele_obj = $(ele_value_obj_att).offset();
    if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

      $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
    }else{
      if(tmp_ele_obj.left-box_warp_left+$("#show_attendance_edit").width() < ele_width){
        $("#show_attendance_edit").css('left',tmp_ele_obj.left-box_warp_left);
      }else{
        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width());
      }
    }   
  }
}


function attendance_setting_user(date,ele,uid,add_id,u_att_id){
  if(!uid){
    uid='';
  }
  if(!add_id){
    add_id='';
  }
  var index = ele;
  var ele = document.getElementById('date_td_'+ele);
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var ele_obj = '';
  ele_obj = $(ele).offset();   
  $.ajax({
    dataType: 'text',
    url: 'ajax.php?action=attendance_setting_user&date='+date+'&uid='+uid+'&index='+index+'&add_id='+add_id+'&u_att_id='+u_att_id,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height()+2);
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
      }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
      } 
      ele_value_obj_att = ele;
      ele_index = index;
      $('#show_attendance_edit').css('display','block');
    }
  });
}

//show edit interval
function edit_space_nums(ele,val) {
   if(val==1){
      $(ele).parent().find('span').eq(0).show();
   }
   else{
      $(ele).parent().find('span').eq(0).hide();
      $(ele).parent().find('span input').eq(0).val("");
   }

}
