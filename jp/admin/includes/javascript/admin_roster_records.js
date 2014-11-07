var temp_group_id = '';
var temp_attendance_id = '';
var ele_value_obj_att = '';
var ele_index = 0;
/*@20141015 
 *最新排版设定 
 */
function set_attendance_info(ele,id,flag,param_y,param_m){
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
  if(flag==0){
    url_tep=  'ajax.php?action=set_attendance_info';
    ele_obj = $("#set_attendance_info").offset();
    ele = "#set_attendance_info";
  }
  if(flag==1){
    url_tep=  'ajax.php?action=set_payrols_info';
    ele_obj = $("#set_payrols_info").offset();
    ele = "#set_payrols_info";
  }
  if(flag==2){
    url_tep= 'ajax.php?action=set_attendance_group_info';
	var att_show_status = $("#show_att_status_hidden").val();
  }
  
  $.ajax({
  url: url_tep,
  data: 'id='+id+'&param_y='+param_y+'&param_m='+param_m+'&att_show_status='+att_show_status,
  type: 'POST',
  dataType: 'text',
  async : false,
  success: function(data){
      $('#show_attendance_edit').html(data);
      $("#show_attendance_edit").css('top',ele_obj.top-box_warp_top+$(ele).height()+2);
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
//change param 
function set_param_style(id,param){
        $.ajax({
            url: 'ajax.php?action=select_param_sigle',
            data: 'id='+id+'&param='+param,
            type: 'POST',
            dataType: 'text',
            async : false,
            success: function(data){
				if(data){
                  var tmp_param_arr = data.split('||'); 
                  $("input[type='text'][name='param_a']").val(tmp_param_arr[0]);
                  $("input[type='text'][name='param_b']").val(tmp_param_arr[1]);
				}
            }
        }); 


}


//select all/no users
function select_all_box(flag){
    if(flag== 1){
      if(document.getElementById("select_all_users").checked){
        $("input[type='checkbox'][name='show_group_user_list[]']").each(function(){
          $(this).attr("checked",true);
         $("#select_all_users").val("2");
        })
      }
    }
	if(flag == 2){
        $("input[type='checkbox'][name='show_group_user_list[]']").each(function(){
          $(this).removeAttr("checked");
         })
		$("#select_all_users").removeAttr("checked");
         $("#select_all_users").val("1");
    }
	if(flag==5){
      if(document.getElementById("select_all_users").checked){
         $("#select_all_users").removeAttr("checked");
         $("#select_all_users").val("2");
    }else{
		 var tag=1;
        $("input[type='checkbox'][name='show_group_user_list[]']").each(function(){
			if(!this.checked){
				tag =0;
			}
         })
		if(tag==1){
		   $("#select_all_users").attr("checked",true);
		}
	  }
	}
}



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

function check_attendance_info(flag,param_a,param_b){
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
      var flag_num = $("#flag_num").val();
      if(flag_num == 0){
      
        flag = 0;
      }
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
		  if(flag==0){
			  if(param_a!=0){
		     document.forms.attendances.action='roster_records.php?&action=insert'+'&y='+param_a+'&m='+param_b;
		  }else{
		     document.forms.attendances.action='roster_records.php?&action=insert';
		  
		  }
		  }if(flag==1){
			  if(param_a!=0){
		     document.forms.attendances.action='roster_records.php?&action=update'+'&y='+param_a+'&m='+param_b;
			  }else{
		     document.forms.attendances.action='roster_records.php?&action=update';
			  
			  }
		  }
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

//show group attendance info
function show_group_attendance_info(ele,date,num,gid,add_id,user){
  if(!user){
    user = '';
  }
  if(!gid){
    gid = '';
  }
	//check the user if is manager or group leader
   $.ajax({
      url: 'ajax.php?action=tep_show_info_attendance',
      data: 'gid='+gid,
      type: 'POST',
      dataType: 'text',
      async : false,
      success: function(data){
        if(data){
	      if(data=='true'){
            is_manager=1;
		  }else{
	   	    is_manager=0;	
		  }
	    }
     }
  }); 
  if(is_manager==0 && admin_npermission < 15){
	return false;
  }

  if(!gid){
    gid='';
    temp_group_id='';
  }else{
    temp_group_id=gid;
  }
  if(!add_id){
    add_id='';
    temp_attendance_id='';
  }else{
    temp_attendance_id=add_id;
  }
  var index = num;
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
    url: 'ajax.php?action=edit_group_attendance_info&date='+date+'&gid='+gid+'&index='+index+'&add_id='+add_id+'&user='+user,
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
          $('#show_user_adl').html('');
          $('#show_user_adl').html(data);
       }
  }); 

}


function hidden_info_box(){
  $('#show_attendance_edit').css('display','none');
  temp_group_id = '';
  temp_attendance_id = '';
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
		   var info = data.split(',');
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
   hidden_info_box();
  //选择的组
  group_id = '';
  error_default ='';
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
        if($(this).val()==''||$(this).val()==0){
		  error_default=1;	
        }else{
          group_id += $(this).val() + '||';
        }
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
        if($(this).val()==''||$(this).val()==0){
		  error_default=1;	
        }else{
          group_id += $(this).val() + '||';
        }
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
      if($(this).val()==''||$(this).val()==0){
		    error_default=1;	
      }else{
        att_id += $(this).val() + '||';
      }
  })
  //默认值没有进行更改


   //请假或加班时间问题的验证
   sign = '';
   var s_hour =$("#leave_start_hour").val();
   var s_m_l =$("#leave_start_min_l").val();
   var s_m_r =$("#leave_start_min_r").val();
   var start_time =s_hour+':'+s_m_l+s_m_r;
   
   var e_hour =$("#leave_end_hour").val();
   var e_m_l =$("#leave_end_min_l").val();
   var e_m_r =$("#leave_end_min_r").val();
   var end_time = e_hour+':'+e_m_l+e_m_r;
if(s_hour==0 && s_m_l==0 && s_m_r==0 && e_hour==0 && e_m_l==0 && e_m_r==0){
  return false;
}
   var tep_str = $("#use_get_userid").text();
   tep_arr = tep_str.split("||");
   var user_id = tep_arr[0];
   var date_str = tep_arr[1];
   $.ajax({
       url: 'ajax.php?action=check_change_ros_rest',
       type: 'POST',
       dataType: 'text',
       data: 'user_id='+user_id+'&date_str='+date_str+'&start_time='+start_time+'&end_time='+end_time, 
       async : false,
       success: function(data){
		   if(data=='error') {
			   //请假排版有交集
			   alert(warn_change_attendance_error);
             sign=1;
		   }else{
		     sign=0; 
		   }
       }
   });

if( sign!=1) {
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

function del_as(num,ele,asl_id,c_permission){
  //start
  var line_num = $("#line_num").val();
  line_num = parseInt(line_num);
  num = parseInt(num);
  if(line_num > 0){
    $(".tr_"+num).parent().remove(); 
    if(num == line_num){
    
      $(".tr_"+(num-1)).last().parent().remove(); 
    }
    if(!($("#add_end").prev().prev().find('input[type=button]').val())){
    
      $("#add_end").prev().prev().remove();
    }
    $("#line_num").val(line_num-1);
  }else{
    //当删除到最后一个将不是删除而是清空 
    if(asl_id != ''){
      $(".popup_order_info select[name='has_group[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='has_attendance_id[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='has_type[]']").each(function(){
        $(this).val('0');
      });
    }else{
      $(".popup_order_info select[name='group[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='attendance_id[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='type[]']").each(function(){
        $(this).val('0');
      });
    }
    $(".space").hide();
  }  
  //end
  var tr_index = $(ele).parent().parent().prev().index();
  var next_input = $(ele).parent().prev().html();
  tr_index++;
  //当删除到最后一个将不是删除是清空
  //var clear_flag_new=$(".popup_order_info").find('select[name="attendance_id[]"]').length;
  //var clear_flag_old=$(".popup_order_info").find('select[name="has_attendance_id[]"]').length;
  //if((clear_flag_new==0&&clear_flag_old==1)||(clear_flag_old==0&&clear_flag_new==1)){
	//var add_str = $('#add_source tbody').html();
	//$("#add_end").before(add_str);
  //}

 //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();
 //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();
 //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();


  var check_last = $('.popup_order_info').find('tr').eq(0).find('td:last input').val();
  if(check_last){
    if(tr_index==0 && check_last.length>0){
     //$('.popup_order_info').find('tr').eq(0).find('td:last').html(next_input);
    }
  }
  if(asl_id!=''){
    $('#get_att_date').after('<input type="hidden" name="del_as[]" value="'+asl_id+'">');
  }
}
function del_as_user(ele,asl_id,is_new){
  var tr_index = $(ele).parent().parent().index();
  tr_index++;
  $('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();
  if(!is_new){
    $('#get_att_date').after('<input type="hidden" name="del_as[]" value="'+asl_id+'">');
  }
}
function del_as_group(num,ele,attendance_group,is_new,c_permission){
  //start

  var line_num = $("#line_num").val();
  line_num = parseInt(line_num);
  num = parseInt(num);
  if(line_num > 1){
    $(".tr_"+num).parent().remove(); 
    if(num+1 == line_num){
    
      $(".tr_"+(num-1)).last().parent().remove(); 
    }
    if(!($("#add_end").prev().prev().find('input[type=button]').val())){
    
      $("#add_end").prev().prev().remove();
    }
    $("#line_num").val(line_num-1);
  }else{
    //当删除到最后一个将不是删除而是清空 
    if(is_new == false){
      $(".popup_order_info select[name='has_user["+attendance_group+"][]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='has_attendance_id[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='has_type[]']").each(function(){
        $(this).val('0');
      });
    }else{
      var uid_num=$(ele).parent().parent().prev().prev().prev().find('input[class="tep_index_num"]').eq(0).val();
      $(".popup_order_info select[name='user["+uid_num+"][]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='attendance_id[]']").each(function(){
        $(this).val('');
      });
      $(".popup_order_info select[name='type[]']").each(function(){
        $(this).val('0');
      });
    }
    $(".space").hide();
  }
  //end
  var tr_index = $(ele).parent().parent().index();
  tr_index++;

  //当删除到最后一个将不是删除是清空
  //var clear_flag_new=$(".popup_order_info").find('select[name="attendance_id[]"]').length;
  //var clear_flag_old=$(".popup_order_info").find('select[name="has_attendance_id[]"]').length;

  if(is_new == false){
  var del_sum = 0;
  $(".popup_order_info select[name='has_user["+attendance_group+"][]']").each(function(){
      del_sum ++;
  });
  }else{
  var del_sum = 0;
  $(".popup_order_info select[name='user["+attendance_group+"][]']").each(function(){
      del_sum ++;
  });
  }
  var next_input = $('.popup_order_info').find('tr').eq(del_sum+2).find('td:last').html();
  var add_str = $('#add_user_group').html();
  next_input = next_input+add_str;
  // get count by attendance_group and for this to remove
  //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();
  for(var i=0;i<del_sum;i++){
    //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();
  }
  //$('.popup_order_info').find('tr:not(.yui3-calendar-row,.yui3-calendar-weekdayrow)').eq(tr_index).remove();

  var check_last = $('.popup_order_info').find('tr').eq(0).find('td:last input').val();
  if(check_last){
    if(tr_index==0 && check_last.length>0){
     //$('.popup_order_info').find('tr').eq(0).find('td:last').html(next_input);
    }
  }
  if(is_new==false){
    $('#get_att_date').after('<input type="hidden" name="del_group[]" value="'+attendance_group+'">');
  }
  //如果是最后一个删除那么在创建者的前面加默认的数据
  //if((clear_flag_new==0&&clear_flag_old==1)||(clear_flag_old==0&&clear_flag_new==1)){
	//var add_str = $('#add_source tbody').html();
    //add_str = add_str.replace("'temp_del_group_id'",'');
	//$("#add_end").before(add_str);
  //}
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

//show replace attendance info
function show_replace_attendance_info(ele,date,num,uid,att_id,group_id){
  if(!uid){
    uid='';
  }
  if(!att_id){
    att_id='';
  }
  if(!group_id){
  
    group_id = '';
  }
  var index = num;
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
    url: 'ajax.php?action=edit_replace_attendance_info&date='+date+'&uid='+uid+'&index='+index+'&att_id='+att_id+'&group_id='+group_id,
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

  var allow_status = document.getElementsByName("allow_status")[0];
  var allow_flag = false;
  $("select[name='allow_user[]']").each(function(){
        if($(this).val() == admin_id){
      
          allow_flag = true;
        }
  });
  if(allow_flag == true){
  
    allow_status.disabled = false;
  }else{

    allow_status.disabled = true;
  }
}

function del_allow_user(ele){
  var add_allow_user_button = document.getElementById('add_allow_user_button');
  add_allow_user_button.disabled = false;
  $(ele).parent().parent().remove();
  var allow_status = document.getElementsByName("allow_status")[0];
  var allow_flag = false;
  $("select[name='allow_user[]']").each(function(){
        if($(this).val() == admin_id){
      
          allow_flag = true;
        }
  });
  if(allow_flag == true){
  
    allow_status.disabled = false;
  }else{

    allow_status.disabled = true;
  }
}

//change groups
function change_users_groups(value){
	var date_tep = $("input[name='get_date']").val();
  $.ajax({
          url: 'ajax.php?action=change_users_groups',
          data: 'users_id='+value+'&date='+date_tep,
          type: 'POST',
          dataType: 'text',
          async : false,
          success: function(data){
             var tmp_msg_arr = data.split('|||'); 
            $("#show_user_adl").html('');
            $("#show_user_adl").html(tmp_msg_arr[2]);
			$(".show_att_titile").html('');
			$(".show_att_titile").html(tmp_msg_arr[1]);
            $("#users_groups").html('');
            $("#users_groups").html(tmp_msg_arr[0]);
          }
        });
}
//change attendance get detail time
function get_detail_att_time(value){
  $.ajax({
          url: 'ajax.php?action=get_detail_att_time',
          data: 'att_id='+value,
          type: 'POST',
          dataType: 'text',
          async : false,
          success: function(data){
            $("#show_user_adl").html('');
            $("#show_user_adl").html(data);
          }
        });

} 
//change users allow
function change_users_allow(value,allow_status_value){

  var allow_status = document.getElementsByName("allow_status")[0];
  var allow_flag = false;
  $("select[name='allow_user[]']").each(function(){
        if($(this).val() == admin_id){
      
          allow_flag = true;
        }
  });
  if(value == admin_id  || allow_flag == true){
 
    if(allow_status_value == 1){
    
      allow_status.value = '1';
    }
    allow_status.disabled = false;
  }else{

    allow_status.value = '0';
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


//show user attendance info
function show_user_attendance_info(ele,date,num,uid,add_id,u_att_id,group_id){
  if(!uid){
    uid='';
  }
  if(!add_id){
    add_id='';
  }
  if(!u_att_id){
    u_att_id='';
  }
  if(!group_id){
  
    group_id='';
  }
  var index = num;
  var ele_width = $(".box_warp").width(); 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var back_group_id = temp_group_id;
  var back_attendance_id = temp_attendance_id;
  var ele_obj = '';
  ele_obj = $(ele).offset();   
  $.ajax({
    dataType: 'text',
    url: 'ajax.php?action=edit_user_attendance_info&date='+date+'&uid='+uid+'&index='+index+'&add_id='+add_id+'&u_att_id='+u_att_id+'&back_group_id='+back_group_id+'&back_attendance_id='+back_attendance_id+'&group_id='+group_id,
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
function add_att_rows(ele){

//rename new user->name
var bid=$(".popup_order_info").find('select[name="attendance_id[]"]').length+1;
var aid=$('#add_source input[class="tep_index_num"]').attr('value',bid);
$('#add_source select[id="user_default"]').attr('name','user['+bid+'][]');

  var add_str = $('#add_source tbody').html();
  add_str = add_str.replace("'temp_del_group_id'",bid);
  var line_num = $("#line_num").val();
  line_num = parseInt(line_num);
  add_str = add_str.replace(/#line_num_1/g,line_num+1);
  add_str = add_str.replace(/#line_num_2/g,line_num-1);
  add_str = add_str.replace(/#line_num/g,line_num);
  $("#add_end").prev().before(add_str);  
}

//add person
function add_person_row(ele,aid,num){
  var show_num = 0;
  if(aid!=''){
    $('#add_person select[id="user_tep"]').attr('name','has_user['+'new'+']['+aid+'][]');
    $('#add_person select[id="user_tep"]').attr('onchange','auto_add_user(this,\''+aid+'\','+num+')');
    $(".popup_order_info select[name='has_user[new]["+aid+"][]']").each(function(){
      if($(this).val() == ''){
        show_num++;
      }
    });
    aid = num; 
  }else{

    var aid=$(ele).parent().parent().find('input[class="tep_index_num"]').eq(0).val();
    $('#add_person select[id="user_tep"]').attr('name','user['+aid+'][]');

    $(".popup_order_info select[name='user["+aid+"][]']").each(function(){
      if($(this).val() == ''){
        show_num++;
      }
    });
  }
  var html_str = $('#add_person tbody').html();
   html_str = html_str.replace(/#line_num/g,aid-1);
  if(show_num == 0){
    $(ele).parent().parent().after(html_str);
    $(ele).parent().parent().next().find('input[class="tep_index_num"]').val(aid);
  }
}

// save att type old or new 
function save_type(ele,url){
  var year = $('#hidden_year').val();
  var month = $('#hidden_month').val();
  var user = $('#hidden_user').val();
  var show_type = ele.value
  document.location.href = url+'?y='+year+'&m='+month+'&user='+user+'&show_type='+show_type+'&action=save_type';
}
// save att status 0 1 2
function save_att_status(url){
  var year = $('#hidden_year').val();
  var month = $('#hidden_month').val();
  var user = $('#hidden_user').val();
  var att_status =  $('input[name="att_status"]:checked').val();
  document.location.href = url+'?y='+year+'&m='+month+'&att_status='+att_status+'&user='+user+'&action=save_att_status'
}
//popup calendar
function open_new_calendar(type,group_id)
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    //mm-dd-yyyy || mm/dd/yyyy
    $('#toggle_open').val('1'); 

    var rules = {
      "all": {
        "all": {
          "all": {
            "all": "current_s_day",
          }
        }
      }};
    if ($("#date_orders").val() != '') {
      if ($("#date_orders").val() == '0000-00-00') {
        date_info_str =  js_cale_date;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders").val().split('-'); 
      }
    } else {
      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = js_cale_date;  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
contentBox: "#mycalendar",
width:'170px',
date: new_date

}).render();
        if (rules != '') {
        month_tmp = date_info[1].substr(0, 1);
        if (month_tmp == '0') {
        month_tmp = date_info[1].substr(1);
        month_tmp = month_tmp-1;
        } else {
        month_tmp = date_info[1]-1; 
        }
        day_tmp = date_info[2].substr(0, 1);

        if (day_tmp == '0') {
        day_tmp = date_info[2].substr(1);
        } else {
        day_tmp = date_info[2];   
        }
        data_tmp_str = date_info[0]+'-'+month_tmp+'-'+day_tmp;
        calendar.set("customRenderer", {
rules: rules,
filterFunction: function (date, node, rules) {
cmp_tmp_str = date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
if (cmp_tmp_str == data_tmp_str) {
node.addClass("redtext"); 
}
}
});
}
var dtdate = Y.DataType.Date;
calendar.on("selectionChange", function (ev) {
    var newDate = ev.newSelection[0];
    tmp_show_date = dtdate.format(newDate); 
    tmp_show_date_array = tmp_show_date.split('-');
    $("#fetch_year").val(tmp_show_date_array[0]); 
    $("#fetch_month").val(tmp_show_date_array[1]); 
    $("#fetch_day").val(tmp_show_date_array[2]);
    date = tmp_show_date_array[0]+tmp_show_date_array[1]+tmp_show_date_array[2];
    if(type == 'user'){
      show_user_attendance_info('',date,'','','','');
    }else if(type == 'group'){
      show_group_attendance_info('',date,'',group_id,'');
    }else if(type == 'replace'){
      show_replace_attendance_info('',date,'','','');
    }
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}

//check date is right
function is_date(dateval)
{
  var arr = new Array();
  if(dateval.indexOf("-") != -1){
    arr = dateval.toString().split("-");
    if(arr.length != 3){
   
      return false;
    }
  }else if(dateval.indexOf("/") != -1){
    arr = dateval.toString().split("/");
    if(arr.length != 3){
   
      return false;
    }
  }else{
    var date_str = dateval.toString(); 
    if(date_str.length == 8){
      arr[0] = date_str.substr(0,4);
      arr[1] = date_str.substr(4,2);
      arr[2] = date_str.substr(6,2);
    }else{
      return false;
    }
  }

  if(!(arr[0].length==4 && arr[1].length==2 && arr[2].length==2)){
  
    return false;
  }
  if(arr[0].length==4){
    var date = new Date(arr[0],arr[1]-1,arr[2]);
    if(date.getFullYear()==arr[0] && date.getMonth()==arr[1]-1 && date.getDate()==arr[2]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[1]-1,arr[0]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[1]-1 && date.getDate()==arr[0]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[0]-1,arr[1]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[0]-1 && date.getDate()==arr[1]) {
      return true;
    }
  }
 
  return false;
}

//copy the date of delivery time to hide field
function change_fetch_date(value,type) {
  fetch_date_str = value.replace(/\//g,'-'); 
  date = value.replace(/\//g,'');; 
  if (!is_date(value)) {
    alert(js_ed_orders_input_right_date); 
  } else {
    $("#date_orders").val(fetch_date_str); 
    if(type == 'user'){
      show_user_attendance_info('',date,'','','','');
    }else if(type == 'group'){
      show_group_attendance_info('',date,'','','');
    }else if(type == 'replace'){
      show_replace_attendance_info('',date,'','','');
    }
  }
}
function select_color(ele,color){
  document.getElementById("color_val").value=color;
  $('.color_div').each(function(){
      $(this).css('border','1px solid #CCCCCC');
  });
  $(ele).find("div").css('border','2px solid #4F4F4F');
}

//auto add user
function auto_add_user(ele,aid,num){

  if(ele.value != ''){
    add_person_row(ele,aid,num);
  }
}

//auto add attendance
function auto_add_attendance(ele){
  if(ele.value != '' ){
  
    var line_num = $("#line_num").val();
    line_num = parseInt(line_num); 
    var show_num = 0;
    $(".popup_order_info select[name='has_attendance_id[]']").each(function(){
      if($(this).val() == ''){
        show_num++; 
      }
    });
    $(".popup_order_info select[name='attendance_id[]']").each(function(){
      if($(this).val() == ''){
        show_num++;
      }
    });
    if(show_num == 0){
      add_att_rows(ele); 
      $("#line_num").val(line_num+1);
    }
    //$(ele).attr('onchange','');
  }
}

//clear data
function clear_data(){

  $("#attendance_title").val('');
  $("#short_language").val('');
  $("#work_start_hour").val(0);
  $("#work_start_min_r").val(0);
  $("#work_start_min_l").val(0);
  $("#work_end_hour").val(0);
  $("#work_end_min_r").val(0);
  $("#work_end_min_l").val(0);
  $("#work_hours").val('');
  $("#rest_hours").val('');
  $("input[name=sort]").eq(0).val(0);
  $("#type_id").val('0');
  change_type_text();
  $("#set_left").attr('checked',true);
  change_set_time(0);
  $("#flag_num").val(0);
  
}

//end date
function end_date(type,id,date){

  if(confirm(end_date_confirm)){
  
    document.forms.attendance_setting_form.action='roster_records.php?action=end_date&type='+type+'&id='+id+'&date='+date;
    document.forms.attendance_setting_form.submit();
  }
}
