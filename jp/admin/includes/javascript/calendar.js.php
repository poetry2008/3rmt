<?php include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BANK_CL);?>
var ele_value_obj = '';
<?php //银行状态添加弹出层?>
function status_add(ele){

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
    url: 'ajax.php?action=status_add',
    success: function(text) {
      //show content 
      $('#show_date_edit').html(text); 
      $("#show_date_edit").css('top',ele_obj.top-box_warp_top+$(ele).height());
      if(ele_obj.left-box_warp_left+$("#show_date_edit").width() > ele_width){

        $("#show_date_edit").css('left',ele_width-$("#show_date_edit").width()); 
      }else{
        $("#show_date_edit").css('left',ele_obj.left-box_warp_left);
      } 
      ele_value_obj = ele;
      $('#show_date_edit').css('display','block');
      document.getElementsByName("title")[0].focus();
    }
  });
}
<?php //银行状态编辑弹出层?>
function status_edit(id,ele){

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
    url: 'ajax.php?action=status_edit&id='+id,
    success: function(text) {
      //show content 
      $('#show_date_edit').html(text); 
      $("#show_date_edit").css('top',ele_obj.top-box_warp_top+$(ele).height());
      if(ele_obj.left-box_warp_left+$("#show_date_edit").width() > ele_width){

        $("#show_date_edit").css('left',ele_width-$("#show_date_edit").width()); 
      }else{
        $("#show_date_edit").css('left',ele_obj.left-box_warp_left);
      }
      ele_value_obj = ele;
      $('#show_date_edit').css('display','block');
    }
  });
}
<?php //具体日期状态编辑弹出层?>
function status_setting(date,ele){

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
    url: 'ajax.php?action=status_setting&date='+date,
    success: function(text) {
      //show content 
      $('#show_date_edit').html(text);  
      $("#show_date_edit").css('top',ele_obj.top-box_warp_top+$(ele).height());
      if(ele_obj.left-box_warp_left+$("#show_date_edit").width() > ele_width){

        $("#show_date_edit").css('left',ele_width-$("#show_date_edit").width()); 
      }else{
        $("#show_date_edit").css('left',ele_obj.left-box_warp_left);
      }
      ele_value_obj = ele;
      $('#show_date_edit').css('display','block');
    }
  });
}

<?php //隐藏弹出层?>
function hidden_info_box(){
  $('#show_date_edit').css('display','none');
}

<?php //切换个别设定，对重复设置的可选，不可选，进行控制?>
function change_repeat_type(num){

  if(num == 1){
  
    var repeat_type = document.getElementsByName("repeat_type")[0];
    repeat_type.value = 0;
    repeat_type.disabled = "disabled";
  }

  if(num == 0){
  
    var repeat_type = document.getElementsByName("repeat_type")[0];
    repeat_type.disabled = "";
  }
}

<?php //切换是否受理，对开始时间，结束时间的可选，不可选，进行控制?>
function change_is_handle(num){

  if(num == 0){
  
    var start_time = document.getElementsByName("start_time")[0];
    start_time.value = '0';
    start_time.disabled = "disabled";
    var end_time = document.getElementsByName("end_time")[0];
    end_time.value = '0';
    end_time.disabled = "disabled";
    var start_left_min = document.getElementsByName("start_left_min")[0];
    start_left_min.value = '0';
    start_left_min.disabled = "disabled";
    var start_right_min = document.getElementsByName("start_right_min")[0];
    start_right_min.value = '0';
    start_right_min.disabled = "disabled"; 
    var end_left_min = document.getElementsByName("end_left_min")[0];
    end_left_min.value = '0';
    end_left_min.disabled = "disabled";
    var end_right_min = document.getElementsByName("end_right_min")[0];
    end_right_min.value = '0';
    end_right_min.disabled = "disabled";
  }

  if(num == 1){
  
    var start_time = document.getElementsByName("start_time")[0];
    start_time.disabled = "";
    var end_time = document.getElementsByName("end_time")[0];
    end_time.disabled = ""; 
    var start_left_min = document.getElementsByName("start_left_min")[0];
    start_left_min.disabled = "";
    var start_right_min = document.getElementsByName("start_right_min")[0];
    start_right_min.disabled = ""; 
    var end_left_min = document.getElementsByName("end_left_min")[0];
    end_left_min.disabled = "";
    var end_right_min = document.getElementsByName("end_right_min")[0];
    end_right_min.disabled = ""; 
  }
}

<?php //添加,编辑银行状态，提交时，判断数据是否完整?>
function status_add_submit(c_permission, ca_type){

  var error = false;
  var title = document.getElementsByName("title")[0];  
  var name = document.getElementsByName("name")[0];
  title = title.value;
  title = title.replace(/\s/g,"");
  name = name.value;
  name = name.replace(/\s/g,"");
  var is_handle = document.getElementsByName("is_handle")[0]; 
  if(is_handle.value == 1 && is_handle.checked == true){
  
    var start_time = document.getElementsByName("start_time")[0];
    var end_time = document.getElementsByName("end_time")[0];
    var start_left_min = document.getElementsByName("start_left_min")[0];
    var start_right_min = document.getElementsByName("start_right_min")[0];
    var end_left_min = document.getElementsByName("end_left_min")[0];
    var end_right_min = document.getElementsByName("end_right_min")[0];
    if((start_time.value == '' || start_left_min.value =='' || start_right_min.value == '') && !(start_time.value == '' && start_left_min.value =='' && start_right_min.value == '')){

      error = true;
      $("#start_time_error").html('<br><font color="#FF0000"><?php echo TEXT_CALENDAR_MUST_SETTING;?></font>');
    }else{
      $("#start_time_error").html('');
    }
    if(end_time.value == '' || end_left_min.value =='' || end_right_min.value == ''){

      error = true;
      $("#end_time_error").html('<br><font color="#FF0000"><?php echo TEXT_CALENDAR_MUST_SETTING;?></font>');
    }else{
      $("#end_time_error").html('');
    }

    if(start_time.value != '' && end_time.value != '' && start_left_min.value !='' && start_right_min.value != '' && end_left_min.value !='' && end_right_min.value != ''){

      if(parseInt(start_time.value+start_left_min.value+start_right_min.value) > parseInt(end_time.value+end_left_min.value+end_right_min.value)){

        error = true;
        $("#end_time_error").html('<br><font color="#FF0000"><?php echo TEXT_CALENDAR_SETTING_ERROR;?></font>');    
      }
    }
  }else{
    $("#start_time_error").html('');
    $("#end_time_error").html('');
  }

  if(title == ""){
    error = true;
    $("#title_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_CALENDAR_MUST_INPUT;?></font>'); 
  }else{
    $("#title_error").html('<?php echo TEXT_FIELD_REQUIRED;?>');
  }

  if(name == ""){
    error = true;
    $("#name_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_CALENDAR_MUST_INPUT;?></font>'); 
  }else{
    $("#name_error").html('<?php echo TEXT_FIELD_REQUIRED;?>');
  }
  
  if(error == false){
    if (c_permission == 31) {
      if (ca_type == 0) {
        document.forms.status_add_form.submit();    
      } else {
        document.forms.status_edit_form.submit();    
      }
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
            if (ca_type == 0) {
              document.forms.status_add_form.submit();    
            } else {
              document.forms.status_edit_form.submit();    
            }
           } else {
             $('#button_save').attr('id', 'tmp_button_save'); 
             var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
             var form_action_str = ''; 
             if (ca_type == 0) {
              form_action_str = document.forms.status_add_form.action;    
             } else {
              form_action_str = document.forms.status_edit_form.action;    
             }
             if (in_array(input_pwd_str, pwd_list_array)) {
               $.ajax({
                 url: 'ajax_orders.php?action=record_pwd_log',   
                 type: 'POST',
                 dataType: 'text',
                 data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
                 async: false,
                 success: function(msg_info) {
                  if (ca_type == 0) {
                    form_action_str = document.forms.status_add_form.submit();    
                  } else {
                    form_action_str = document.forms.status_edit_form.submit();    
                  }
                 }
               }); 
             } else {
               alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
               setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
             }
           }
        }
      });
    }
  }
}

<?php //删除银行状态时的提示信息?>
function status_delete(c_permission){
 
  if (c_permission == 31) {
    document.status_edit_form.action = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_BANK_CL;?>?action=status_delete';
    document.status_edit_form.submit();
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
          document.status_edit_form.action = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_BANK_CL;?>?action=status_delete';
          document.status_edit_form.submit();
         } else {
           $('#button_save').attr('id', 'tmp_button_save'); 
           var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
           if (in_array(input_pwd_str, pwd_list_array)) {
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_BANK_CL;?>?action=status_delete'),
               async: false,
               success: function(msg_info) {
                document.status_edit_form.action = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_BANK_CL;?>?action=status_delete';
                document.status_edit_form.submit();
               }
             }); 
           } else {
             alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
             setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
           }
         }
      }
    });
  }
}

<?php //浏览器窗口缩放时，对弹出层位置的控制?>
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
  ele_obj = $("#show_date_edit").offset();
  if(ele_value_obj != ''){
    tmp_ele_obj = $(ele_value_obj).offset();
    if(ele_obj.left-box_warp_left+$("#show_date_edit").width() > ele_width){

      $("#show_date_edit").css('left',ele_width-$("#show_date_edit").width()); 
    }else{
      if(tmp_ele_obj.left-box_warp_left+$("#show_date_edit").width() < ele_width){
        $("#show_date_edit").css('left',tmp_ele_obj.left-box_warp_left);
      }else{
        $("#show_date_edit").css('left',ele_width-$("#show_date_edit").width());
      }
    }   
  }
}

<?php //具体日期状态设置，提交时的处理?>
function save_submit(c_permission){
  if (c_permission == 31) {
    document.calendar_date.submit();
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
           document.calendar_date.submit();
         } else {
           $('#button_save').attr('id', 'tmp_button_save'); 
           var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
           if (in_array(input_pwd_str, pwd_list_array)) {
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',   
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.calendar_date.action),
               async: false,
               success: function(msg_info) {
                 document.calendar_date.submit();
               }
             }); 
           } else {
             alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
             setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
           }
         }
      }
    });
  }
}

<?php //数据重置?>
function date_reset(){

  document.calendar_date.reset();
  var special_flag = document.getElementById("special_flag"); 
  var repeat_type = document.getElementsByName("repeat_type")[0];
  if(special_flag.value == 1){
    repeat_type.value = 0;
    repeat_type.disabled = "disabled";
  }else{
    repeat_type.disabled = ""; 
  }
}
