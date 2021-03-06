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
   //prev next
   if($('#groups_'+group_id).prev().attr('id') != '' && $('#groups_'+group_id).prev().attr('id') != null){
      var groups_prev_id = $('#groups_'+group_id).prev().attr('id');
      groups_prev_id = groups_prev_id.split('_');

      if(groups_prev_id[0] == 'groups' && groups_prev_id[1] != ''){
        var groups_id = $('#groups_'+group_id).prev().attr('id');
        groups_id = groups_id.split('_');
        $('#next_prev').append('<a id="groups_prev" onclick="'+$('#action_'+groups_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">&lt'+groups_prev+'</a>&nbsp&nbsp');
      }
   }
   if($('#groups_'+group_id).next().attr('id') != '' && $('#groups_'+group_id).next().attr('id') != null){
     var groups_next_id = $('#groups_'+group_id).next().attr('id');
     groups_next_id = groups_next_id.split('_');
     
     if(groups_next_id[0] == 'groups' && groups_next_id[1] != ''){
       var groups_id = $('#groups_'+group_id).next().attr('id');
       groups_id = groups_id.split('_');
       $('#next_prev').append('<a id="groups_next" onclick="'+$('#action_'+groups_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">'+groups_next+'&gt</a>&nbsp&nbsp');
     }
   } 
   //end
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
   var text_num = $("#text_num").val();
   var text_array = new Array();
   text_array = text_num.split(",");
   for(x in text_array){
   
     document.getElementById("textarea_"+text_array[x]).style.height = document.getElementById("textarea_"+text_array[x]).scrollHeight - 5 + "px";
   }
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
    var end_date = document.getElementsByName("end_date[]"); 
    var end_date_length = end_date.length;
    var start_date = document.getElementsByName("start_date[]"); 

    for(var i=0;i<=end_date_length-1;i++){
    
      end_date[i].disabled = false;
      start_date[i].disabled = false;
    }
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
//add object
function add_obj(ele,obj_name_1,obj_name_2,delete_name,flag){

  var obj_type = $("#payroll_select").val();
  var obj_num = $("#obj_num").val();
  obj_num = parseInt(obj_num);
  var obj_str = '';
  if(obj_type == 0){
    obj_str = '<tr id="obj_tr_'+(obj_num+1)+'"><td width="20%" valign="top"><input type="hidden" name="payroll_sort[]" value="0">&nbsp;&nbsp;&nbsp;&nbsp;'+obj_name_1+'</td><td><input type="text" name="object_title[]" value="" style="width:145px;"><input type="text" name="object_contents[]" value="" style="width:150px;"><input type="button" value="'+delete_name+'" onclick="delete_obj('+(obj_num+1)+');">&nbsp;&nbsp;<input type="button" onclick="add_obj(this,\''+obj_name_1+'\',\''+obj_name_2+'\',\''+delete_name+'\',1);" value="'+ele.value+'"><br><input type="text" name="object_value[]" class="td_input" value=""></td></tr>';
  }else if(obj_type == 1){
    obj_str = '<tr id="obj_tr_'+(obj_num+1)+'"><td width="20%" valign="top"><input type="hidden" name="payroll_sort[]" value="-1">&nbsp;&nbsp;&nbsp;&nbsp;'+obj_name_2+'</td><td><input type="text" name="formula_title[]" style="width:145px;" value=""><input type="text" name="formula_contents[]" style="width:150px;" value=""><input type="button" value="'+delete_name+'" onclick="delete_obj('+(obj_num+1)+');">&nbsp;&nbsp;<input type="button" onclick="add_obj(this,\''+obj_name_1+'\',\''+obj_name_2+'\',\''+delete_name+'\',1);" value="'+ele.value+'"><br><input type="text" name="formula_value[]" class="td_input" value=""></td></tr>';
  }
  if(obj_num == 0){
    $(ele).parent().parent().after(obj_str);
  }else{
    if(flag == 1){
      $(ele).parent().parent().after(obj_str); 
    }else{
      $("#end_tr").before(obj_str); 
    }
  }
  $("#obj_num").val(obj_num+1);
}
//delete object
function delete_obj(num){

  var obj_num = $("#obj_num").val();
  $("#obj_tr_"+num).remove();
  $("#obj_num").val(obj_num-1);
}
//date select
function date_select(ele,end_str,start_str,date_str){

  var end_date = document.getElementsByName("end_date[]"); 
  var end_date_length = end_date.length;
  var start_date = document.getElementsByName("start_date[]"); 
  var start_date_length = start_date.length;
  var date_select_array = new Array();
  var tr_date_id = $("#tr_date_id").val();
  tr_date_id = parseInt(tr_date_id);

  var one_start_date = '';
  var one_end_date = '';

  var date_i = 0;
  for(var i=0;i<end_date_length;i++){
 
    if(i != tr_date_id){
    
      start_date[i].disabled = true;
      end_date[i].disabled = true;
    }
    var start_time = parseInt(start_date[i].value);
    var end_time = parseInt(end_date[i].value);
    if(start_time > end_time){
   
      end_time = end_time+28;
    }
    for(var j=start_time;j<=end_time;j++){
   
      if(j < one_start_date){

        date_select_array[date_i] = j+28;
      }else{
      
        date_select_array[date_i] = j;
      }
      date_i++;
    }
   
    if(i == 0){
      one_start_date = parseInt(start_date[i].value);
      one_end_date = parseInt(end_date[i].value);
    }
  }
  date_select_array.sort(function(a,b){return a>b?1:-1});
  var all_date_array = new Array();
  var date_i = 0;
  for(var k=one_start_date;k<=one_start_date-1+28;k++){
     
    all_date_array[date_i] = k;
    date_i++;
  }
  //alert(all_date_array.toString());
  //alert(date_select_array.toString());

  var diff_date_array = new Array();

  for(var m=0;m<all_date_array.length;m++){
 
    var flag = true;
    for(var n=0;n<date_select_array.length;n++){
    
      if(all_date_array[m] == date_select_array[n]){
      
        flag = false;
      }
    }
    if(flag){
    
      diff_date_array.push(all_date_array[m]);
    }
  }
  //alert(diff_date_array.toString());
  if(diff_date_array.length > 0){
 
       var select_str = '<tr id="tr_date_'+tr_date_id+'"><td width="20%"></td><td>';
       var select_end_str = end_str+'<select name="end_date[]" onchange="date_select(this,\''+end_str+'\',\''+start_str+'\',\''+date_str+'\');" >';
       var select_start_str = '&nbsp;&nbsp;'+start_str+'<select name="start_date[]" onchange="start_date_select(this,'+tr_date_id+');" disabled>'; 

      for(var p=0;p<diff_date_array.length;p++){
  
        var diff_value = diff_date_array[p] > 28 ? diff_date_array[p]-28 : diff_date_array[p];
        var diff_value_str = diff_value == 28 ? '28~31' : diff_value;
        if(p != diff_date_array.length-1 && diff_date_array[p]+1 == diff_date_array[p+1]){
     
          select_start_str += '<option value="'+diff_value+'">'+diff_value_str+date_str+'</option>';
          select_end_str += '<option value="'+diff_value+'">'+diff_value_str+date_str+'</option>';
        }else{
    
          if(p == diff_date_array.length-1){
            select_start_str += '<option value="'+diff_value+'">'+diff_value_str+date_str+'</option>';
            select_end_str += '<option value="'+diff_value+'" selected>'+diff_value_str+date_str+'</option>';
          }else{
            var temp_num = tr_date_id;
            $("#tr_date_id").val(tr_date_id+1);
            tr_date_id = $("#tr_date_id").val();
            tr_date_id = parseInt(tr_date_id);
            select_start_str += '<option value="'+diff_value+'">'+diff_value_str+date_str+'</option>';
            select_end_str += '<option value="'+diff_value+'" selected>'+diff_value_str+date_str+'</option>';
            select_str += select_start_str+'</select>'+select_end_str+'</select></td></tr>';
            select_str += '<tr id="tr_date_'+tr_date_id+'"><td width="20%"></td><td>';
            select_end_str = end_str+'<select name="end_date[]" onchange="date_select(this,\''+end_str+'\',\''+start_str+'\',\''+date_str+'\');" disabled>';
            select_start_str = '&nbsp;&nbsp;'+start_str+'<select name="start_date[]" onchange="start_date_select(this,'+tr_date_id+');">';
          }
        }
      } 
      select_str += select_start_str+'</select>'+select_end_str+'</select></td></tr>';
      $(ele).parent().parent().after(select_str);
      if(temp_num){
        document.getElementsByName("start_date[]")[temp_num].disabled = true;
      }
      $("#tr_date_id").val(tr_date_id+1);
  }
}
//popup calendar
function open_new_calendar()
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
    $("input[name=payroll_date]").val(tmp_show_date); 
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}
//start date select
function start_date_select(ele,num){

  if(ele.value != 0){
    document.getElementsByName("end_date[]")[num].disabled = false;
  }
}
//reset date
function check_reset(){

    document.getElementsByName("start_date[]")[0].value = 0;
    document.getElementsByName("start_date[]")[0].disabled = false;
    document.getElementsByName("end_date[]")[0].value = 28;
    document.getElementsByName("end_date[]")[0].disabled = true;

    var tr_date_id = $("#tr_date_id").val();

    tr_date_id = parseInt(tr_date_id);
    for(var i=1;i<=tr_date_id;i++){
    
      $("#tr_date_"+i).remove();
    }
    $("#tr_date_id").val(1);
}
//add payroll admin
function add_admin_list(ele,del_str){
  var select_str = $("#add_payroll_list").find("select").html();
  $("select[name='payroll_admin[]']").each(function(){
 
    var reg = new RegExp('<option value="'+$(this).val()+'".*?>.*?<\/option>','g');
    select_str = select_str.replace(reg,'');
  });

  select_str_temp = select_str.replace('<option vlaue="">--</option>','');

  if(select_str_temp != ''){

    $("#add_payroll_list").append('<li><select name="payroll_admin[]">'+select_str+'</select><input type="button" value="'+del_str+'" onclick="del_admin_list(this);"></li>');
  }else{
  
    ele.disabled = true;
  }
}
//delete admin list
function del_admin_list(ele){
  var add_admin_button = document.getElementById('add_admin_button');
  add_admin_button.disabled = false;
  $(ele).parent().remove();
}
//popup move group page
function move_group_id(id){
  $.ajax({
dataType: 'text',
url: 'ajax.php?'+move_group_id_url+'&action=move_group&id='+id,
success: function(text) {
$('#show_latest_news').html(text);
$('#show_latest_news').css('display','block');
}
});
}
//move group submit
function toggle_group_form(group_url_str,action) 
{
  if (user_permission == 31) {
    if(action == 'move_group'){
      document.forms.move_group.submit();
    }else if(action == 'copy_group'){
      document.forms.copy_group.submit(); 
    }
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
        if(action == 'move_group'){
          document.forms.move_group.submit();
        }else if(action == 'copy_group'){
          document.forms.copy_group.submit(); 
        }
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
              if(action == 'move_group'){
                document.forms.move_group.submit();
              }else if(action == 'copy_group'){
                document.forms.copy_group.submit(); 
              }
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
//popup copy group page
function copy_group_id(id){
  $.ajax({
dataType: 'text',
url: 'ajax.php?'+move_group_id_url+'&action=copy_group&id='+id,
success: function(text) {
$('#show_latest_news').html(text);
$('#show_latest_news').css('display','block');
}
});
}
function add_manager_row(){
	$("#add_manager_list").append($("#add_manager_hidden").html());
}
function del_manager_row(ele){
	$(ele).parent().remove();
}
