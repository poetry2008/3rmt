var ele_value_obj_att = '';
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

     $("#show_attendance_edit").css('top',ele_obj.top+$(ele).height());
     if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()+box_warp_left); 
     }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
     }
     ele_value_obj_att = ele;

     $('#show_attendance_edit').css('display','block');
 }
  }); 

}


//delect attendance by id
function delete_attendance_info(id){
	if(confirm(attendance_del_confirm)) {
      	
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


//add approve
function add_attendance_approve_person(id){
        $.ajax({
            url: 'ajax.php?action=add_attendance_approve',
            data: 'id='+id,
            type: 'POST',
            dataType: 'text',
            async : false,
            success: function(data){
				if(data){
                  $("#tep_add").append(data);
				}
            }
        }); 
}

//change scheduling_type
function change_type_text(){
	var select_val = $("#type_id").val();

	if(select_val==1){
      $("#src_text_image").css("display","none");	
      $("#image_div").css("display","none");	
      $("#upload_button").css("display","none");	
      $("#src_text_color").css("display","block");	
      $("#color_div").css("display","block");	
	}
	if(select_val==0){
      $("#image_div").css("display","block");	
      $("#src_text_image").css("display","block");	
      $("#upload_button").css("display","block");	
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

function attendance_setting(date,ele,gid){
  if(!gid){
    gid='';
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
    url: 'ajax.php?action=attendance_setting&date='+date+'&gid='+gid+'&index='+index,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele_obj.top+$(ele).height());
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()+box_warp_left); 
      }else{
        left_add = $(ele).index()*($(ele).width()+3);
        $("#show_attendance_edit").css('left',box_warp_left+left_add);
      }
      ele_value_obj_att = ele;
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
    $("#show_attendance_edit").css('top',$(ele_value_obj_att).offset().top+$(ele_value_obj_att).height());
    tmp_ele_obj = $(ele_value_obj_att).offset();
    if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

      $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()+box_warp_left); 
    }else{
      if(tmp_ele_obj.left-box_warp_left+$("#show_attendance_edit").width() < ele_width){
        left_add = $(ele_value_obj_att).index()*($(ele_value_obj_att).width()+3);
        $("#show_attendance_edit").css('left',box_warp_left+left_add);
      }else{
        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()+box_warp_left);
      }
    }   
  }
}

function delete_submit(c_permission){
  del_url = href_attendance_calendar+'?action=delete_as_list';
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
function save_submit(c_permission){
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

function del_as(ele,asl_id,c_permission){
  var tr_index = $(ele).parent().parent().index();
  if(asl_id!=''){
    if (c_permission == 31) {
  $.ajax({
    url: 'ajax_orders.php?action=del_one_as',   
    type: 'POST',
    dataType: 'text',
    data: 'asl_id='+asl_id, 
    async: false,
    success: function(msg) {
      if(msg == 'true'){
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
      }else{
        alert('TEXT_CANNOT_DELETE_ONE_AS');
      }
    }
  });
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
  $.ajax({
    url: 'ajax_orders.php?action=del_one_as',   
    type: 'POST',
    dataType: 'text',
    data: 'asl_id='+asl_id, 
    async: false,
    success: function(msg) {
      if(msg == 'true'){
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
      }else{
        alert('TEXT_CANNOT_DELETE_ONE_AS');
      }
    }
  });
           } else {
             var input_pwd_str = window.prompt(js_text_input_onetime_pwd, ''); 
             if (in_array(input_pwd_str, pwd_list_array)) {
               $.ajax({
                 url: 'ajax_orders.php?action=record_pwd_log',   
                 type: 'POST',
                 dataType: 'text',
                 data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.attendance_setting_form.action),
                 async: false,
                 success: function(msg_info) {
  $.ajax({
    url: 'ajax_orders.php?action=del_one_as',   
    type: 'POST',
    dataType: 'text',
    data: 'asl_id='+asl_id, 
    async: false,
    success: function(msg) {
      if(msg == 'true'){
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
        $('.popup_order_info').find('tr').eq(tr_index).remove();
      }else{
        alert('TEXT_CANNOT_DELETE_ONE_AS');
      }
    }
  });
                 }
               }); 
             } else {
               alert(js_text_onetime_pwd_error); 
             }
           }
        }
       });
    }
  }else{
    $('.popup_order_info').find('tr').eq(tr_index).remove();
    $('.popup_order_info').find('tr').eq(tr_index).remove();
    $('.popup_order_info').find('tr').eq(tr_index).remove();
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
        $("#show_user_list").html(msg);
      }
    }
  });
}

function attendance_replace(date,ele,uid){
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
    url: 'ajax.php?action=attendance_replace&date='+date+'&uid='+uid+'&index='+index,
    dataType: 'text',
    async: false,
    success: function(text) {
      //show content 
      $('#show_attendance_edit').html(text);  
      $("#show_attendance_edit").css('top',ele.top-box_warp_top+$(ele).height());
      if(ele_obj.left-box_warp_left+$("#show_attendance_edit").width() > ele_width){

        $("#show_attendance_edit").css('left',ele_width-$("#show_attendance_edit").width()); 
      }else{
        $("#show_attendance_edit").css('left',ele_obj.left-box_warp_left);
      }
      ele_value_obj_att = ele;
      $('#show_attendance_edit').css('display','block');
    }
  });
}

function add_allow_user(ele,del_str){
  var select_str = $(ele).parent().parent().find('td').eq(1).html();
  $('#add_end').before('<tr><td></td><td>'+select_str+'</td><td><input type="button" value="'+del_str+'" onclick="del_allow_user(this)"></td></tr>');
}

function del_allow_user(ele){
  $(ele).parent().parent().remove();
}
