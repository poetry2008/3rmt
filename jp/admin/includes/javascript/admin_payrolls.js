var o_submit_single = true;
$(document).ready(function() {
  //监听按键 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_user_payroll').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
        //回车
        if ($('#show_user_payroll').css('display') != 'none') {
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+方向左 
      if ($('#show_user_payroll').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+方向右
      if ($('#show_user_payroll').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  }); 
});
function show_user_payroll(ele,user_id,user_name,groups_id,user_payroll_list,save_date,group_id){
 $.ajax({
 type: "POST",
 url: 'ajax.php?&action=show_user_payroll',
 data: {user_id:user_id,user_name:user_name,groups_id:groups_id,user_payroll_list:user_payroll_list,save_date:save_date,group_id:group_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
   $("div#show_user_payroll").html(data);
   //prev next
   if($('#payroll_'+user_id).prev().attr('id') != '' && $('#payroll_'+user_id).prev().attr('id') != null){
      var payroll_prev_id = $('#payroll_'+user_id).prev().attr('id');
      payroll_prev_id = payroll_prev_id.split('_');

      if(payroll_prev_id[0] == 'payroll' && payroll_prev_id[1] != '' && payroll_prev_id[1] != 'total'){
        var payroll_id = $('#payroll_'+user_id).prev().attr('id');
        payroll_id = payroll_id.split('_');
        $('#next_prev').append('<a id="payroll_prev" onclick="'+$('#click_'+payroll_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">&lt'+payroll_prev+'</a>&nbsp&nbsp');
      }
   }
   if($('#payroll_'+user_id).next().attr('id') != '' && $('#payroll_'+user_id).next().attr('id') != null){
     var payroll_next_id = $('#payroll_'+user_id).next().attr('id');
     payroll_next_id = payroll_next_id.split('_');
     
     if(payroll_next_id[0] == 'payroll' && payroll_next_id[1] != '' && payroll_next_id[1] != 'total'){
       var payroll_id = $('#payroll_'+user_id).next().attr('id');
       payroll_id = payroll_id.split('_');
       $('#next_prev').append('<a id="payroll_next" onclick="'+$('#click_'+payroll_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">'+payroll_next+'&gt</a>&nbsp&nbsp');
     }
   } 
   //end
   ele = ele.parentNode;
   head_top = $('.compatible_head').height();
   if($(ele).parent().next()[0] === undefined){
     if($('#show_user_payroll').height() > ($(ele).parent().parent().height() - $(ele).parent().height())){
       var topset = $(ele).offset().top + $(ele).height() + 3;
     }else{
       var topset = $(ele).offset().top - $('#show_user_payroll').height();
     }
   }else{
     var topset = $(ele).offset().top + $(ele).height() + 3;
   }
   $('#show_user_payroll').css('top', topset);
   if($('.show_left_menu').width()){
     leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
   }else{
     leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
   } 
 
   $('#show_user_payroll').css('z-index','1');
   $('#show_user_payroll').css('left',leftset);
   $('#show_user_payroll').css('display', 'block');
   o_submit_single = true;
   }
  }); 
}
function hidden_info_box(){
  $('#show_user_payroll').css('display','none');
  o_submit_single = true;
}
//change users list
function change_user_list(ele){
  var gid = $(ele).val();
  var contents_array = new Array();
  $.ajax({
    url: 'ajax_orders.php?action=payrolls_user_list',   
    type: 'POST',
    dataType: 'text',
    data: 'gid='+gid, 
    async: false,
    success: function(msg) {
      if(msg!=''){
        contents_array = msg.split('|||');
        $("#show_user_list").html(contents_array[0]);
        $("#show_title_list").html(contents_array[1]);
      }
    }
  });
}
//all select
function all_select_user(user_list_id)
{
  var check_flag = document.edit_users_payroll.all_check.checked;
  if (document.edit_users_payroll.elements[user_list_id]) {
    if (document.edit_users_payroll.elements[user_list_id].length == null) {
      if (check_flag == true) {
        document.edit_users_payroll.elements[user_list_id].checked = true;
      } else {
        document.edit_users_payroll.elements[user_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_users_payroll.elements[user_list_id].length; i++) {
        if (check_flag == true) {
          if(!(document.edit_users_payroll.elements[user_list_id][i].disabled)){
            document.edit_users_payroll.elements[user_list_id][i].checked = true;
          }
        } else {
          document.edit_users_payroll.elements[user_list_id][i].checked = false;
        }
      }
    }
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
    $("#fetch_year").val(tmp_show_date_array[0]); 
    $("#fetch_month").val(tmp_show_date_array[1]); 
    $("#fetch_day").val(tmp_show_date_array[2]);
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}
//array  to json
function arrayToJson(o) {
  var r = [];
  if (typeof o == "string") return "\"" + o.replace(/([\'\"\\])/g,
      "\\$1").replace(/(\n)/g, "\\n").replace(/(\r)/g, "\\r").replace(/(\t)/g,
        "\\t") + "\"";
  if (typeof o == "object") {
    if (!o.sort) {
      for (var i in o)
        r.push(i + ":" + arrayToJson(o[i]));
      if (!!document.all && !/^\n?function\s*toString\(\)\s*\{\n?\s*\[native code\]\n?\s*\}\n?\s*$/.test(o.toString)) {
        r.push("toString:" + o.toString.toString());
      }
      r = "{" + r.join() + "}";
    } else {
      for (var i = 0; i < o.length; i++) {
        r.push(arrayToJson(o[i]));
      }
      r = "[" + r.join() + "]";
    }
    return r;
  }
  return o.toString();
}
//user send mail
function user_change_action(value,user_list_id,c_permission)
{
  sel_num = 0;
  if (document.edit_users_payroll.elements[user_list_id].length == null) {
    if (document.edit_users_payroll.elements[user_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_users_payroll.elements[user_list_id].length; i++) {
      if (document.edit_users_payroll.elements[user_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
   if(value == 1){
    if (confirm(user_select_send_mail)) {
      //check email address
      var select_user = new Array();
      $('input[name="user_id[]"]').each(function() {
        if($(this).attr("checked")) {
          select_user.push($(this).val());
	}
      });
      var valadate_type = 'user';
      var select_json = arrayToJson(select_user); 
      var data='';
      var submit_flag = true;
      $.ajax({
         async:false,
         url: 'ajax.php?action=valadate_user_email&type='+valadate_type,
         type: 'POST',
         data:{"select_json":select_json},
         success: function (data){
           if(data!=''){
             if(confirm(data)){ 
                submit_flag = true;
             }else{
                submit_flag = false;
                document.getElementsByName("user_action")[0].value = 0;
             }
           }else{
             submit_flag = true;
           }
         }
      }); 
      if(submit_flag == true){
                if (c_permission == 31) {
                  document.edit_users_payroll.action = 'payrolls.php?action=send_mail';
                  document.edit_users_payroll.submit(); 
                } else {
                  $.ajax({
                    url: 'ajax_orders.php?action=getallpwd',   
                    type: 'POST',
                    dataType: 'text',
                    data: 'current_page_name=/admin/payrolls.php', 
                    async: false,
                    success: function(msg) {
                      var tmp_msg_arr = msg.split('|||'); 
                      var pwd_list_array = tmp_msg_arr[1].split(',');
                      if (tmp_msg_arr[0] == '0') {
                        document.edit_users_payroll.action = 'payrolls.php?action=send_mail';
                        document.edit_users_payroll.submit(); 
                      } else {
                        var input_pwd_str = window.prompt(ontime_pwd, ''); 
                        if (in_array(input_pwd_str, pwd_list_array)) {
                        $.ajax({
                          url: 'ajax_orders.php?action=record_pwd_log',   
                          type: 'POST',
                          dataType: 'text',
                          data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('payrolls.php?action=send_mail'),
                          async: false,
                          success: function(msg_info) {
                            document.edit_users_payroll.action = 'payrolls.php?action=send_mail';
                            document.edit_users_payroll.submit(); 
                          }
                        }); 
                        } else {
                          document.getElementsByName("user_action")[0].value = 0;
                          alert(ontime_pwd_error); 
                        }
                      }
                    }
                    });
                }
      }
    }else{

      document.getElementsByName("user_action")[0].value = 0;
    } 
   }else if(value == 2){
  
     if(confirm(user_export_confirm)){
       payrolls_csv_exe();
     }else{
       document.getElementsByName("user_action")[0].value = 0;
     }
   }else if(value == 3){
     payrolls_print_exe(); 
   }
  }else{
    document.getElementsByName("user_action")[0].value = 0;
    alert(must_select_user); 
  }
}

//save user payroll
function save_user_payroll(){

  document.edit_users_payroll.submit(); 
}
//popup calendar
function open_new_calendar_num(num)
{
  var is_open = $('#toggle_open_'+num).val(); 
  if (is_open == 0) {
    //mm-dd-yyyy || mm/dd/yyyy
    $('#toggle_open_'+num).val('1'); 

    var rules = {
      "all": {
        "all": {
          "all": {
            "all": "current_s_day",
          }
        }
      }};
    if ($("#date_orders_"+num).val() != '') {
      if ($("#date_orders_"+num).val() == '0000-00-00') {
        date_info_str =  js_cale_date;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders_"+num).val().split('-'); 
      }
    } else {
      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = js_cale_date;  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
contentBox: "#mycalendar_"+num,
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
    $("input[name='payroll_date["+num+"]']").val(tmp_show_date); 
    $("#date_orders_"+num).val(tmp_show_date); 
    $('#toggle_open_'+num).val('0');
    $('#toggle_open_'+num).next().html('<div id="mycalendar_'+num+'"></div>');
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
  }else if(dateval.indexOf("/") != -1){
    arr = dateval.toString().split("/");
  }else{
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
function change_fetch_date() {
  fetch_date_str = $("#fetch_year").val()+"-"+$("#fetch_month").val()+"-"+$("#fetch_day").val(); 
  if (!is_date(fetch_date_str)) {
    alert(js_ed_orders_input_right_date); 
  } else {
    $("#date_orders").val(fetch_date_str); 
  }
}
//reset user payroll
function reset_user_payroll(pam_str){

  location.href = 'payrolls.php?'+pam_str+'&reset=1'; 
}
//
function date_change(ele,num){

  $("#date_orders_"+num).val(ele.value);
}
//again computing payroll
function again_computing(){

  document.edit_users_payroll.action = 'payrolls.php?action=again_computing';
  document.edit_users_payroll.submit(); 
}
//submit action
function change_action(url){
  document.edit_users_payroll.action = url;
  document.edit_users_payroll.submit(); 
}
//export csv
function payrolls_csv_exe(){
           if (user_permission == 31) {
             change_action(submit_url);
             document.getElementsByName("user_action")[0].value = 0;
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name=/admin/payrolls.php', 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                   change_action(submit_url);
                   document.getElementsByName("user_action")[0].value = 0;
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt(ontime_pwd, ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         change_action(submit_url);
                         document.getElementsByName("user_action")[0].value = 0;
                      }
                    }); 
                  } else {
                    alert(ontime_pwd_error); 
                    document.getElementsByName("user_action")[0].value = 0;
                  }
                }
              }
            });
           }
}
//print payrolls
function payrolls_print(){

       var payroll_title = '';
       $('input[name^="payroll_title"]').each(function() {
         payroll_title += $(this).val()+'|';
       });
       var user_id = '';
       var user_num = '';
       var i = 0;
       $('input[name="user_id[]"]').each(function() {
          if($(this).attr("checked")) {
            user_id += $(this).val()+'|';
            user_num += i+'|';
	  }
          i++;
       });
       var user_payroll = ''; 
       $('input[name^="users_payroll"]').each(function() {
         user_payroll += $(this).val()+'|';
       });
       var currency_type = document.getElementsByName("currency_type_str")[0].value;
       var group_id = document.getElementsByName("group_id")[0].value;
       var save_date = document.getElementsByName("save_date")[0].value;
       window.open('print_payrolls.php?payroll_title='+payroll_title+'&user_id='+user_id+'&user_payroll='+user_payroll+'&currency_type='+currency_type+'&user_num='+user_num+'&group_id='+group_id+'&save_date='+save_date);
}

//print payrolls action
function payrolls_print_exe(){
           if (user_permission == 31) {
             payrolls_print();
             document.getElementsByName("user_action")[0].value = 0;
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name=/admin/payrolls.php', 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                   payrolls_print();
                   document.getElementsByName("user_action")[0].value = 0;
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt(ontime_pwd, ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         payrolls_print();
                         document.getElementsByName("user_action")[0].value = 0;
                      }
                    }); 
                  } else {
                    alert(ontime_pwd_error); 
                    document.getElementsByName("user_action")[0].value = 0;
                  }
                }
              }
            });
           }
}
//payrolls sort
function payrolls_sort(type,sort,name,asc,desc,num){

  var user_str = '';
  var i = 0;
  $('input[name="user_id[]"]').each(function() {
    if(i != $('input[name="user_id[]"]').length-1){
      user_str += $(this).val()+'|';
    }else{
      user_str += $(this).val();
    }
    i++;
  });
  var user_payrolls_str = '';
  var j = 0;
  switch(type){
  
    case 'name':
      $('input[name="user_name[]"]').each(function() {
        if(j != $('input[name="user_name[]"]').length-1){
          user_payrolls_str += $(this).val()+'|';
        }else{
          user_payrolls_str += $(this).val();
        }
        j++;
      });     
    break;
    case 'title':
      $('input[name^="users_payroll['+num+']"]').each(function() {
        if(j != $('input[name^="users_payroll['+num+']"]').length-1){
          user_payrolls_str += $(this).val()+'|';
        }else{
          user_payrolls_str += $(this).val();
        }
      }); 
    break;
    case 'time':
      $('input[name="payrolls_time[]"]').each(function() {
        if(j != $('input[name="payrolls_time[]"]').length-1){
          user_payrolls_str += $(this).val()+'|';
        }else{
          user_payrolls_str += $(this).val();
        }
        j++;
      });     
    break;
  }

  //ajax submit
  $.ajax({
    url: 'ajax_orders.php?action=payrolls_sort',   
    type: 'POST',
    dataType: 'text',
    data: 'user_str='+user_str+'&user_payrolls_str='+user_payrolls_str+'&sort='+sort, 
    async: false,
    success: function(msg) {
      if(msg!=''){
        var user_id = new Array();
        user_id = msg.split('|');
        var html_str = '';
        var style_str = '';
        for(z in user_id){
      
          style_str = 'dataTableSecondRow';
          if(z%2 == 1){
         
            style_str = 'dataTableRow';
          }
          html_str += '<tr id="payroll_'+user_id[z]+'" class="'+style_str+'" onmouseout="this.className=\''+style_str+'\'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'">'+$("#payroll_"+user_id[z]).html()+'</tr>';
          $("#payroll_"+user_id[z]).remove();
        } 
        $("#payrolls_total").before(html_str);
        var sort_str = 0;
        var order_sort = '<font color="#c0c0c0">'+asc+'</font><font color="#facb9c">'+desc+'</font>';
        if(sort == 0){
        
          sort_str = 1;
          order_sort  = '<font color="#facb9c">'+asc+'</font><font color="#c0c0c0">'+desc+'</font>';
        }

        var tr_index = $("#tr_index").html();
        tr_index = tr_index.replace(/<font.*?>.*?<\/font>/g,"");
        tr_index = tr_index.replace(",0,",",1,");
        $("#tr_index").html(tr_index);
        if(type == 'name'){
          $("#td_"+type).html('<a href="javascript:payrolls_sort(\'name\','+sort_str+',\''+name+'\',\''+asc+'\',\''+desc+'\',\'\');">'+name+order_sort+'</a>');
        }else if(type == 'title'){
          $("#td_"+type+'_'+num).html('<a href="javascript:payrolls_sort(\'title\','+sort_str+',\''+name+'\',\''+asc+'\',\''+desc+'\','+num+');">'+name+order_sort+'</a><input type="hidden" value="'+name+'" name="payroll_title['+num+']">');
        }else if(type == 'time'){
          var group_id = document.getElementsByName("group_id")[0].value;
          $("#td_"+type).html('<a href="javascript:payrolls_sort(\'time\','+sort_str+',\''+name+'\',\''+asc+'\',\''+desc+'\',\'\');">'+name+order_sort+'</a><input type="hidden" value="'+group_id+'" name="group_id">');
        }
      }
    }
  });
}
