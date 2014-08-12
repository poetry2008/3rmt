var o_submit_single = true;
$(document).ready(function() {
  //监听按键 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_user_wage').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
        //回车
        if ($('#show_user_wage').css('display') != 'none') {
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+方向左 
      if ($('#show_user_wage').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+方向右
      if ($('#show_user_wage').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  }); 
});
function show_user_wage(ele,user_id,user_name,groups_id,user_wage_list,save_date){
 $.ajax({
 type: "POST",
 url: 'ajax.php?&action=show_user_wage',
 data: {user_id:user_id,user_name:user_name,groups_id:groups_id,user_wage_list:user_wage_list,save_date:save_date} ,
 dataType: 'text',
 async : false,
 success: function(data){
   $("div#show_user_wage").html(data);
   ele = ele.parentNode;
   head_top = $('.compatible_head').height();
   if($(ele).parent().next()[0] === undefined){
     if($('#show_user_wage').height() > ($(ele).parent().parent().height() - $(ele).parent().height())){
       var topset = $(ele).offset().top + $(ele).height() + 3;
     }else{
       var topset = $(ele).offset().top - $('#show_user_wage').height();
     }
   }else{
     var topset = $(ele).offset().top + $(ele).height() + 3;
   }
   $('#show_user_wage').css('top', topset);
   if($('.show_left_menu').width()){
     leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
   }else{
     leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
   } 
 
   $('#show_user_wage').css('z-index','1');
   $('#show_user_wage').css('left',leftset);
   $('#show_user_wage').css('display', 'block');
   o_submit_single = true;
   }
  }); 
}
function hidden_info_box(){
  $('#show_user_wage').css('display','none');
  o_submit_single = true;
}
//change users list
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
//all select
function all_select_user(user_list_id)
{
  var check_flag = document.edit_users_wage.all_check.checked;
  if (document.edit_users_wage.elements[user_list_id]) {
    if (document.edit_users_wage.elements[user_list_id].length == null) {
      if (check_flag == true) {
        document.edit_users_wage.elements[user_list_id].checked = true;
      } else {
        document.edit_users_wage.elements[user_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_users_wage.elements[user_list_id].length; i++) {
        if (check_flag == true) {
          if(!(document.edit_users_wage.elements[user_list_id][i].disabled)){
            document.edit_users_wage.elements[user_list_id][i].checked = true;
          }
        } else {
          document.edit_users_wage.elements[user_list_id][i].checked = false;
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
    $("input[name=wage_date]").val(tmp_show_date); 
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}
//user send mail
function user_change_action(value,user_list_id,c_permission)
{
  sel_num = 0;
  if (document.edit_users_wage.elements[user_list_id].length == null) {
    if (document.edit_users_wage.elements[user_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_users_wage.elements[user_list_id].length; i++) {
      if (document.edit_users_wage.elements[user_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm(user_select_send_mail)) {
      if (c_permission == 31) {
        document.edit_users_wage.action = 'payrolls.php?action=send_mail';
        document.edit_users_wage.submit(); 
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
              document.edit_users_wage.action = 'payrolls.php?action=send_mail';
              document.edit_users_wage.submit(); 
            } else {
              var input_pwd_str = window.prompt(ntime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('payrolls.php?action=send_mail'),
                  async: false,
                  success: function(msg_info) {
                    document.edit_users_wage.action = 'payrolls.php?action=send_mail';
                    document.edit_users_wage.submit(); 
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
    }else{

      document.getElementsByName("user_action")[0].value = 0;
    } 
  }else{
    document.getElementsByName("user_action")[0].value = 0;
    alert(must_select_user); 
  }
}

//save user wage
function save_user_wage(){

  document.edit_users_wage.submit(); 
}
